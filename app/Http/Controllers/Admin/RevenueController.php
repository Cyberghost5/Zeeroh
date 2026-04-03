<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionSetting;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    /**
     * Platform revenue overview.
     */
    public function index()
    {
        $summary = [
            'gross_revenue'     => Order::where('payment_status', 'paid')->sum('total_amount'),
            'commission_earned' => Order::where('payment_status', 'paid')->sum('commission'),
            'service_fees'      => Order::where('payment_status', 'paid')->sum('service_fee'),
            'total_orders'      => Order::where('payment_status', 'paid')->count(),
        ];

        // Revenue by event
        $event_breakdown = Order::where('payment_status', 'paid')
            ->with('event.organizer')
            ->select('event_id',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(subtotal) as gross'),
                DB::raw('SUM(commission) as commission'),
                DB::raw('SUM(service_fee) as service_fee'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('event_id')
            ->orderByDesc('gross')
            ->paginate(20);

        return view('admin.revenue.index', compact('summary', 'event_breakdown'));
    }

    /**
     * Organizer payouts — what each organizer is owed.
     */
    public function payouts()
    {
        $payouts = User::role('organizer')
            ->withCount(['events as total_events'])
            ->get()
            ->map(function ($organizer) {
                $orders = Order::whereHas('event', fn($q) => $q->where('organizer_id', $organizer->id))
                    ->where('payment_status', 'paid')
                    ->selectRaw('SUM(subtotal) as gross, SUM(commission) as commission, COUNT(*) as orders')
                    ->first();

                $organizer->gross_revenue  = $orders->gross ?? 0;
                $organizer->commission     = $orders->commission ?? 0;
                $organizer->net_payout     = ($orders->gross ?? 0) - ($orders->commission ?? 0);
                $organizer->orders_count   = $orders->orders ?? 0;
                return $organizer;
            })
            ->sortByDesc('net_payout');

        return view('admin.revenue.payouts', compact('payouts'));
    }

    /**
     * Show & edit commission settings.
     */
    public function commissionSettings()
    {
        $setting = CommissionSetting::active();
        return view('admin.revenue.commission', compact('setting'));
    }

    /**
     * Update commission settings.
     */
    public function updateCommission(Request $request)
    {
        $data = $request->validate([
            'commission_percentage' => 'required|numeric|min:0|max:100',
            'service_fee_type'      => 'required|in:fixed,percentage',
            'service_fee_value'     => 'required|numeric|min:0',
        ]);

        CommissionSetting::where('is_active', true)->update($data);

        return back()->with('success', 'Commission settings updated.');
    }
}
