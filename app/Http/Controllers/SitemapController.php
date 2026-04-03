<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\OrganizerProfile;

class SitemapController extends Controller
{
    public function index()
    {
        $events = Event::where('status', 'approved')
            ->select('slug', 'updated_at')
            ->latest('updated_at')
            ->get();

        $organizers = OrganizerProfile::whereHas('user', fn($q) => $q->where('is_active', true))
            ->select('slug', 'updated_at')
            ->get();

        return response()
            ->view('sitemap', compact('events', 'organizers'))
            ->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        $content = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /organizer\nDisallow: /checkout\nDisallow: /profile\n\nSitemap: " . route('sitemap') . "\n";
        return response($content, 200)->header('Content-Type', 'text/plain');
    }
}
