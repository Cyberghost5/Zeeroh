<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $organizer = Auth::user();

        $eventIds = Event::where('organizer_id', $organizer->id)->pluck('id');

        $paidOrders = Order::whereIn('event_id', $eventIds)->where('payment_status', 'paid');

        $stats = [
            'total_events'    => $eventIds->count(),
            'active_events'   => Event::whereIn('id', $eventIds)->where('status', 'approved')->count(),
            'tickets_sold'    => Ticket::whereIn('event_id', $eventIds)->where('status', 'active')->count(),
            'total_revenue'   => (clone $paidOrders)->sum('subtotal'), // organizer revenue (before commission)
            'commission_paid' => (clone $paidOrders)->sum('commission'),
            'net_revenue'     => (clone $paidOrders)->sum(DB::raw('subtotal - commission')),
            'checked_in'      => Ticket::whereIn('event_id', $eventIds)->whereNotNull('checked_in_at')->count(),
        ];

        $recent_events = Event::whereIn('id', $eventIds)
            ->with('category', 'ticketTypes')
            ->latest()
            ->take(5)
            ->get();

        $recent_orders = Order::whereIn('event_id', $eventIds)
            ->where('payment_status', 'paid')
            ->with('user', 'event', 'items.ticketType')
            ->latest()
            ->take(8)
            ->get();

        // Revenue by event (for breakdown table)
        $event_revenue = Event::whereIn('id', $eventIds)
            ->withCount(['tickets as tickets_sold' => fn($q) => $q->where('status', 'active')])
            ->withSum(['orders as gross_revenue' => fn($q) => $q->where('payment_status', 'paid')], 'subtotal')
            ->withSum(['orders as commission_total' => fn($q) => $q->where('payment_status', 'paid')], 'commission')
            ->orderByDesc('gross_revenue')
            ->take(10)
            ->get();

        return view('organizer.dashboard', compact('stats', 'recent_events', 'recent_orders', 'event_revenue'));
    }
}
