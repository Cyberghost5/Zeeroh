<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_events'      => Event::count(),
            'pending_events'    => Event::where('status', 'pending')->count(),
            'total_users'       => User::role('attendee')->count(),
            'total_organizers'  => User::role('organizer')->count(),
            'gross_revenue'     => Order::where('payment_status', 'paid')->sum('total_amount'),
            'commission_earned' => Order::where('payment_status', 'paid')->sum('commission'),
            'total_orders'      => Order::where('payment_status', 'paid')->count(),
            'tickets_sold'      => Ticket::where('status', 'active')->count(),
        ];

        $recent_events = Event::with('organizer', 'category')
            ->latest()
            ->take(5)
            ->get();

        $recent_orders = Order::with('user', 'event')
            ->where('payment_status', 'paid')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_events', 'recent_orders'));
    }
}
