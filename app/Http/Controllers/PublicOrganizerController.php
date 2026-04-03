<?php

namespace App\Http\Controllers;

use App\Models\OrganizerProfile;
use App\Models\Event;

class PublicOrganizerController extends Controller
{
    public function show(string $slug)
    {
        $organizer = OrganizerProfile::where('slug', $slug)
            ->with('user')
            ->firstOrFail();

        $events = Event::where('organizer_id', $organizer->user_id)
            ->where('status', 'approved')
            ->where('start_date', '>=', today())
            ->with('category', 'ticketTypes')
            ->orderBy('start_date')
            ->paginate(12);

        $profile = $organizer; // alias kept for view compatibility

        return view('organizers.show', compact('organizer', 'profile', 'events'));
    }
}
