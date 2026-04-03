<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class OrganizerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('organizer')
            ->with('organizerProfile')
            ->withCount('events');

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $organizers = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.organizers.index', compact('organizers'));
    }

    public function show(User $organizer)
    {
        abort_if(! $organizer->hasRole('organizer'), 404);

        $organizer->load('organizerProfile');

        $events = $organizer->events()
            ->with('category', 'ticketTypes')
            ->withCount('tickets')
            ->orderByDesc('created_at')
            ->paginate(10);

        $stats = [
            'total_events'   => $organizer->events()->count(),
            'approved_events'=> $organizer->events()->where('status', 'approved')->count(),
            'total_tickets'  => \App\Models\Ticket::whereIn('event_id', $organizer->events()->pluck('id'))->count(),
            'total_revenue'  => \App\Models\Order::whereIn('event_id', $organizer->events()->pluck('id'))
                                    ->where('payment_status', 'paid')->sum('total_amount'),
        ];

        return view('admin.organizers.show', compact('organizer', 'events', 'stats'));
    }

    public function suspend(User $organizer)
    {
        abort_if(! $organizer->hasRole('organizer'), 404);

        $organizer->update(['is_active' => false]);

        return back()->with('success', "Organizer {$organizer->name} has been suspended.");
    }

    public function reactivate(User $organizer)
    {
        abort_if(! $organizer->hasRole('organizer'), 404);

        $organizer->update(['is_active' => true]);

        return back()->with('success', "Organizer {$organizer->name} has been reactivated.");
    }

    public function impersonate(User $organizer)
    {
        abort_if(! $organizer->hasRole('organizer'), 404);

        session(['impersonating_admin' => auth()->id()]);

        auth()->login($organizer);

        return redirect()->route('organizer.dashboard')
            ->with('success', "You are now viewing as {$organizer->name}.");
    }

    public function destroy(User $organizer)
    {
        abort_if(! $organizer->hasRole('organizer'), 404);

        $name = $organizer->name;

        // Load events with children and delete in safe order
        $organizer->load('events');
        $eventIds = $organizer->events->pluck('id');

        \App\Models\Ticket::whereIn('event_id', $eventIds)->delete();
        \App\Models\Order::whereIn('event_id', $eventIds)->delete();

        foreach ($organizer->events as $event) {
            $event->ticketTypes()->delete();
            $event->reviews()->delete();
            $event->waitlist()->delete();
        }

        $organizer->events()->delete();

        // Delete profile and user account
        $organizer->organizerProfile()->delete();
        $organizer->roles()->detach();
        $organizer->delete();

        return redirect()->route('admin.organizers.index')
            ->with('success', "Organizer '{$name}' and all their data have been permanently deleted.");
    }

    public function stopImpersonation()
    {
        $adminId = session()->pull('impersonating_admin');

        if (! $adminId) {
            return redirect()->route('admin.dashboard');
        }

        $admin = User::findOrFail($adminId);
        auth()->login($admin);

        return redirect()->route('admin.organizers.index')
            ->with('success', 'Impersonation ended. Welcome back.');
    }
}
