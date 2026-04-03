<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PromoCodeController extends Controller
{
    public function index()
    {
        $codes = PromoCode::where('organizer_id', auth()->id())
            ->with('event')
            ->withCount('orders')
            ->latest()
            ->paginate(15);

        return view('organizer.promos.index', compact('codes'));
    }

    public function create()
    {
        $events = Event::where('organizer_id', auth()->id())
            ->where('status', 'approved')
            ->orderBy('title')
            ->get();

        return view('organizer.promos.create', compact('events'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'             => ['required', 'string', 'max:50', 'alpha_dash', 'unique:promo_codes,code'],
            'event_id'         => ['nullable', 'exists:events,id'],
            'type'             => ['required', 'in:percentage,fixed'],
            'value'            => ['required', 'numeric', 'min:0.01'],
            'usage_limit'      => ['nullable', 'integer', 'min:1'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'valid_from'       => ['nullable', 'date'],
            'valid_until'      => ['nullable', 'date', 'after_or_equal:valid_from'],
        ]);

        // If event_id provided, verify it belongs to this organizer
        if (!empty($data['event_id'])) {
            $event = Event::where('id', $data['event_id'])
                ->where('organizer_id', auth()->id())
                ->firstOrFail();
        }

        if ($data['type'] === 'percentage' && $data['value'] > 100) {
            return back()->withErrors(['value' => 'Percentage discount cannot exceed 100%.'])->withInput();
        }

        PromoCode::create([
            ...$data,
            'organizer_id' => auth()->id(),
            'code'         => strtoupper($data['code']),
            'is_active'    => true,
        ]);

        return redirect()->route('organizer.promos.index')
            ->with('success', 'Promo code created successfully.');
    }

    public function toggle(PromoCode $promoCode)
    {
        abort_unless($promoCode->organizer_id === auth()->id(), 403);

        $promoCode->update(['is_active' => !$promoCode->is_active]);

        return back()->with('success', 'Promo code ' . ($promoCode->is_active ? 'deactivated' : 'activated') . '.');
    }

    public function destroy(PromoCode $promoCode)
    {
        abort_unless($promoCode->organizer_id === auth()->id(), 403);

        if ($promoCode->used_count > 0) {
            return back()->with('error', 'Cannot delete a promo code that has been used.');
        }

        $promoCode->delete();

        return back()->with('success', 'Promo code deleted.');
    }
}
