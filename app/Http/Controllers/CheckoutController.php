<?php

namespace App\Http\Controllers;

use App\Models\CommissionSetting;
use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PromoCode;
use App\Models\Ticket;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CheckoutController extends Controller
{
    /**
     * Show the checkout page — ticket selection summary before payment.
     */
    public function show(Request $request, string $slug)
    {
        $event = Event::where('slug', $slug)
            ->where('status', 'approved')
            ->with('ticketTypes')
            ->firstOrFail();

        // Build selected quantities from query params: ?qty[ticket_type_id]=n
        $quantities = collect($request->get('qty', []))
            ->map(fn($q) => (int) $q)
            ->filter(fn($q) => $q > 0);

        if ($quantities->isEmpty()) {
            return redirect()->route('events.show', $slug)
                ->with('error', 'Please select at least one ticket.');
        }

        $selectedTypes = $event->ticketTypes
            ->whereIn('id', $quantities->keys())
            ->keyBy('id');

        // Validate availability
        foreach ($quantities as $typeId => $qty) {
            $type = $selectedTypes->get($typeId);
            if (!$type || !$type->isAvailable() || $qty > $type->available_quantity) {
                return redirect()->route('events.show', $slug)
                    ->with('error', 'One or more tickets are unavailable or sold out.');
            }
            if ($type->max_per_order && $qty > $type->max_per_order) {
                return redirect()->route('events.show', $slug)
                    ->with('error', "Max {$type->max_per_order} tickets per order for {$type->name}.");
            }
        }

        $commission = CommissionSetting::active();
        $lineItems  = [];
        $subtotal   = 0;

        foreach ($quantities as $typeId => $qty) {
            $type      = $selectedTypes->get($typeId);
            $lineTotal = $type->price * $qty;
            $subtotal += $lineTotal;
            $lineItems[] = [
                'type'      => $type,
                'quantity'  => $qty,
                'subtotal'  => $lineTotal,
            ];
        }

        $serviceFee  = $subtotal > 0 ? $commission->calculateServiceFee($subtotal) : 0;
        $total       = $subtotal + $serviceFee;

        return view('checkout.show', compact('event', 'lineItems', 'subtotal', 'serviceFee', 'total'));
    }

    /**
     * AJAX: validate a promo code and return the discount.
     */
    public function validatePromo(Request $request)
    {
        $request->validate([
            'code'     => ['required', 'string'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'event_id' => ['required', 'integer'],
        ]);

        $code = PromoCode::whereRaw('UPPER(code) = ?', [strtoupper($request->code)])
            ->where('is_active', true)
            ->where(fn($q) => $q->whereNull('event_id')->orWhere('event_id', $request->event_id))
            ->first();

        if (!$code) {
            return response()->json(['valid' => false, 'message' => 'Invalid promo code.'], 422);
        }

        if (!$code->isValid((float) $request->subtotal)) {
            $msg = 'This promo code is not valid right now.';
            if ($code->min_order_amount && $request->subtotal < $code->min_order_amount) {
                $msg = 'Minimum order of ₦' . number_format($code->min_order_amount) . ' required.';
            }
            return response()->json(['valid' => false, 'message' => $msg], 422);
        }

        $discount = $code->calculateDiscount((float) $request->subtotal);

        return response()->json([
            'valid'       => true,
            'code_id'     => $code->id,
            'discount'    => $discount,
            'description' => $code->type === 'percentage'
                ? $code->value . '% off'
                : '₦' . number_format($code->value) . ' off',
        ]);
    }

    /**
     * Create a pending order and redirect to Paystack.
     */
    public function initiate(Request $request, string $slug)
    {
        $request->validate([
            'ticket_types'              => ['required', 'array', 'min:1'],
            'ticket_types.*.id'         => ['required', 'exists:ticket_types,id'],
            'ticket_types.*.quantity'   => ['required', 'integer', 'min:1'],
            'promo_code'                => ['nullable', 'string', 'max:50'],
        ]);

        $event = Event::where('slug', $slug)
            ->where('status', 'approved')
            ->with('ticketTypes')
            ->firstOrFail();

        $commission = CommissionSetting::active();
        $subtotal   = 0;
        $lineItems  = [];

        foreach ($request->ticket_types as $item) {
            $type = $event->ticketTypes->find($item['id']);
            if (!$type || !$type->isAvailable()) {
                return back()->with('error', 'A selected ticket is no longer available.');
            }
            $qty       = (int) $item['quantity'];
            $lineTotal = $type->price * $qty;
            $subtotal += $lineTotal;
            $lineItems[] = ['type' => $type, 'qty' => $qty, 'subtotal' => $lineTotal];
        }

        // Apply promo code
        $promoCode      = null;
        $discountAmount = 0;

        if ($request->filled('promo_code')) {
            $promoCode = PromoCode::whereRaw('UPPER(code) = ?', [strtoupper($request->promo_code)])
                ->where('is_active', true)
                ->where(fn($q) => $q->whereNull('event_id')->orWhere('event_id', $event->id))
                ->first();

            if ($promoCode && $promoCode->isValid($subtotal)) {
                $discountAmount = $promoCode->calculateDiscount($subtotal);
            }
        }

        $discountedSubtotal = $subtotal - $discountAmount;
        $serviceFee         = $discountedSubtotal > 0 ? $commission->calculateServiceFee($discountedSubtotal) : 0;
        $total              = $discountedSubtotal + $serviceFee;
        $commAmt            = $commission->calculateCommission($discountedSubtotal);

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id'          => auth()->id(),
                'event_id'         => $event->id,
                'promo_code_id'    => $promoCode?->id,
                'order_number'     => Order::generateOrderNumber(),
                'subtotal'         => $subtotal,
                'discount_amount'  => $discountAmount,
                'service_fee'      => $serviceFee,
                'commission'       => $commAmt,
                'total_amount'     => $total,
                'payment_gateway'  => 'paystack',
                'payment_status'   => 'pending',
            ]);

            foreach ($lineItems as $li) {
                $order->items()->create([
                    'ticket_type_id' => $li['type']->id,
                    'quantity'       => $li['qty'],
                    'unit_price'     => $li['type']->price,
                    'subtotal'       => $li['subtotal'],
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Order creation failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Could not create your order. Please try again.');
        }

        // Increment promo usage
        $promoCode?->incrementUsage();

        // For free orders skip Paystack
        if ($total == 0) {
            return $this->completeFreeOrder($order);
        }

        // Initialize Paystack transaction
        try {
            $response = Http::timeout(15)->withToken(config('services.paystack.secret'))
                ->post('https://api.paystack.co/transaction/initialize', [
                    'email'        => auth()->user()->email,
                    'amount'       => (int) round($total * 100), // kobo
                    'reference'    => $order->order_number,
                    'callback_url' => route('checkout.callback'),
                    'metadata'     => [
                        'order_id'   => $order->id,
                        'order_number' => $order->order_number,
                    ],
                ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Paystack connection failed', ['error' => $e->getMessage()]);
            $order->update(['payment_status' => 'failed']);
            return back()->with('error', 'Could not reach the payment gateway. Please check your connection and try again.');
        }

        if (!$response->successful() || !$response->json('status')) {
            Log::error('Paystack init failed', [
                'status'  => $response->status(),
                'body'    => $response->json(),
                'order'   => $order->order_number,
            ]);
            $order->update(['payment_status' => 'failed']);
            return back()->with('error', $response->json('message') ?? 'Payment gateway error. Please try again.');
        }

        $order->update(['payment_reference' => $response->json('data.reference')]);

        return redirect($response->json('data.authorization_url'));
    }

    /**
     * Paystack redirect callback.
     */
    public function callback(Request $request)
    {
        $reference = $request->get('reference') ?? $request->get('trxref');

        if (!$reference) {
            return redirect()->route('home')->with('error', 'Invalid payment callback.');
        }

        $order = Order::where('order_number', $reference)
            ->orWhere('payment_reference', $reference)
            ->with('items.ticketType', 'event', 'user')
            ->firstOrFail();

        // Verify with Paystack
        $response = Http::withToken(config('services.paystack.secret'))
            ->get("https://api.paystack.co/transaction/verify/{$reference}");

        if (!$response->successful() || $response->json('data.status') !== 'success') {
            $order->update(['payment_status' => 'failed']);
            return redirect()->route('events.show', $order->event->slug)
                ->with('error', 'Payment was not successful. Please try again.');
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.success', $order->order_number);
        }

        $this->fulfillOrder($order);

        return redirect()->route('orders.success', $order->order_number);
    }

    /**
     * Paystack webhook (server-to-server).
     */
    public function webhook(Request $request)
    {
        $signature = $request->header('x-paystack-signature');
        $payload   = $request->getContent();

        if (!hash_equals(hash_hmac('sha512', $payload, config('services.paystack.secret')), $signature)) {
            abort(403);
        }

        $data = json_decode($payload, true);

        if (($data['event'] ?? '') !== 'charge.success') {
            return response()->json(['status' => 'ignored']);
        }

        $reference = $data['data']['reference'] ?? null;
        $order = Order::where('order_number', $reference)
            ->orWhere('payment_reference', $reference)
            ->with('items.ticketType', 'event', 'user')
            ->first();

        if ($order && $order->payment_status !== 'paid') {
            $this->fulfillOrder($order);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Order success page.
     */
    public function success(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with('tickets.ticketType', 'event', 'items.ticketType')
            ->firstOrFail();

        if (auth()->id() !== $order->user_id) {
            abort(403);
        }

        return view('checkout.success', compact('order'));
    }

    // ── Private helpers ────────────────────────────────────────────

    private function completeFreeOrder(Order $order): \Illuminate\Http\RedirectResponse
    {
        $order->load('items.ticketType', 'event', 'user');
        $this->fulfillOrder($order);
        return redirect()->route('orders.success', $order->order_number);
    }

    private function fulfillOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->update([
                'payment_status' => 'paid',
                'paid_at'        => now(),
            ]);

            foreach ($order->items as $item) {
                // Create one Ticket record per unit
                for ($i = 0; $i < $item->quantity; $i++) {
                    $ticket = Ticket::create([
                        'order_id'       => $order->id,
                        'order_item_id'  => $item->id,
                        'ticket_type_id' => $item->ticket_type_id,
                        'event_id'       => $order->event_id,
                        'user_id'        => $order->user_id,
                        'holder_name'    => $order->user->name,
                        'holder_email'   => $order->user->email,
                        'holder_phone'   => $order->user->phone,
                        'status'         => 'active',
                    ]);

                    // Generate and save QR code as SVG
                    $qrPath = 'qrcodes/' . $ticket->ticket_code . '.svg';
                    \Illuminate\Support\Facades\Storage::disk('public')
                        ->put($qrPath, QrCode::format('svg')->size(200)->generate($ticket->ticket_code));

                    $ticket->update(['qr_code_path' => $qrPath]);
                }

                // Increment quantity_sold
                $item->ticketType->increment('quantity_sold', $item->quantity);
            }
        });

        // Send confirmation email (queue-safe)
        try {
            Mail::to($order->user->email)->send(new \App\Mail\OrderConfirmation($order->fresh(['tickets.ticketType', 'event', 'items.ticketType'])));
        } catch (\Throwable $e) {
            Log::error('Order confirmation email failed', ['order' => $order->id, 'error' => $e->getMessage()]);
        }
    }
}
