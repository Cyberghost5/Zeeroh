<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\SavedEvent;
use App\Models\Ticket;

class DashboardController extends Controller
{
    public function index()
    {
        $tickets = Ticket::where('user_id', auth()->id())
            ->with('event', 'ticketType', 'order', 'review')
            ->orderByDesc('created_at')
            ->get();

        $orders = Order::where('user_id', auth()->id())
            ->where('payment_status', 'paid')
            ->with('event', 'items.ticketType')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('dashboard', compact('tickets', 'orders'));
    }

    public function saved()
    {
        $savedEvents = SavedEvent::where('user_id', auth()->id())
            ->with('event.category', 'event.ticketTypes')
            ->whereHas('event', fn($q) => $q->where('status', 'approved'))
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('dashboard.saved', compact('savedEvents'));
    }

    public function orders()
    {
        $orders = Order::where('user_id', auth()->id())
            ->where('payment_status', 'paid')
            ->with('event', 'items.ticketType', 'tickets')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('dashboard.orders', compact('orders'));
    }
}
