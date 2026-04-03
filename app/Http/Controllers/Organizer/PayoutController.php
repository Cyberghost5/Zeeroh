<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Event;
use App\Models\PayoutRequest;
use App\Models\User;
use App\Notifications\PayoutRequestedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class PayoutController extends Controller
{
    public function index()
    {
        $organizer = auth()->user();
        $profile   = $organizer->organizerProfile;

        // Total net revenue earned (subtotal minus commission)
        $eventIds = Event::where('organizer_id', $organizer->id)->pluck('id');

        $totalEarned = Order::whereIn('event_id', $eventIds)
            ->where('payment_status', 'paid')
            ->selectRaw('SUM(subtotal - commission) as net')
            ->value('net') ?? 0;

        $totalPaidOut = PayoutRequest::where('organizer_id', $organizer->id)
            ->whereIn('status', ['approved', 'paid'])
            ->sum('amount');

        $availableBalance = max(0, $totalEarned - $totalPaidOut);

        $pendingRequests = PayoutRequest::where('organizer_id', $organizer->id)
            ->where('status', 'pending')
            ->sum('amount');

        $requests = PayoutRequest::where('organizer_id', $organizer->id)
            ->latest()
            ->paginate(10);

        return view('organizer.payouts.index', compact(
            'requests', 'profile', 'totalEarned', 'totalPaidOut',
            'availableBalance', 'pendingRequests'
        ));
    }

    public function store(Request $request)
    {
        $organizer = auth()->user();
        $profile   = $organizer->organizerProfile;

        abort_unless($profile, 403, 'Complete your organizer profile first.');

        $eventIds = Event::where('organizer_id', $organizer->id)->pluck('id');

        $totalEarned = Order::whereIn('event_id', $eventIds)
            ->where('payment_status', 'paid')
            ->selectRaw('SUM(subtotal - commission) as net')
            ->value('net') ?? 0;

        $totalPaidOut = PayoutRequest::where('organizer_id', $organizer->id)
            ->whereIn('status', ['approved', 'paid'])
            ->sum('amount');

        $availableBalance = max(0, $totalEarned - $totalPaidOut);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1000', 'max:' . $availableBalance],
        ], [
            'amount.max' => 'Requested amount exceeds available balance of ₦' . number_format($availableBalance, 2) . '.',
            'amount.min' => 'Minimum payout is ₦1,000.',
        ]);

        // Block if there's already a pending request
        $hasPending = PayoutRequest::where('organizer_id', $organizer->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return back()->with('error', 'You already have a pending payout request. Please wait for it to be processed.');
        }

        $payout = PayoutRequest::create([
            'organizer_id'   => $organizer->id,
            'amount'         => $data['amount'],
            'bank_name'      => $profile->bank_name,
            'account_number' => $profile->account_number,
            'account_name'   => $profile->account_name,
            'status'         => 'pending',
        ]);

        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new PayoutRequestedNotification($payout->load('organizer')));

        return back()->with('success', 'Payout request submitted. We\'ll process it within 2–3 business days.');
    }
}
