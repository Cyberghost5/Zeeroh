<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Notifications\EventStatusNotification;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('organizer', 'category')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $events = $query->paginate(20);

        $statusCounts = Event::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('admin.events.index', compact('events', 'statusCounts'));
    }

    public function show(Event $event)
    {
        $event->load('organizer.organizerProfile', 'category', 'ticketTypes');
        return view('admin.events.show', compact('event'));
    }

    public function approve(Event $event)
    {
        if ($event->status !== 'pending') {
            return back()->with('error', 'Only pending events can be approved.');
        }

        $event->update(['status' => 'approved', 'rejection_reason' => null]);

        $event->load('organizer');
        $event->organizer?->notify(new EventStatusNotification($event, 'approved'));

        return back()->with('success', "Event '{$event->title}' approved successfully.");
    }

    public function reject(Request $request, Event $event)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        if ($event->status !== 'pending') {
            return back()->with('error', 'Only pending events can be rejected.');
        }

        $event->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        $event->load('organizer');
        $event->organizer?->notify(new EventStatusNotification($event, 'rejected'));

        return back()->with('success', "Event '{$event->title}' rejected.");
    }

    public function feature(Event $event)
    {
        $event->update(['is_featured' => !$event->is_featured]);
        $label = $event->is_featured ? 'featured' : 'unfeatured';

        return back()->with('success', "Event '{$event->title}' {$label}.");
    }

    public function destroy(Event $event)
    {
        $title = $event->title;

        // Remove children in safe order
        $event->tickets()->delete();
        $event->ticketTypes()->delete();
        $event->orders()->delete();
        $event->reviews()->delete();
        $event->waitlist()->delete();
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', "Event '{$title}' has been permanently deleted.");
    }
}

