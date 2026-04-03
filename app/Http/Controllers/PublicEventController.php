<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\SavedEvent;
use Illuminate\Http\Request;

class PublicEventController extends Controller
{
    public function home()
    {
        $featured = Event::where('status', 'approved')
            ->where('is_featured', true)
            ->where('start_date', '>=', today())
            ->with('category', 'ticketTypes')
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        $upcoming = Event::where('status', 'approved')
            ->where('start_date', '>=', today())
            ->with('category', 'ticketTypes')
            ->orderBy('start_date')
            ->take(8)
            ->get();

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('home', compact('featured', 'upcoming', 'categories'));
    }

    public function index(Request $request)
    {
        $query = Event::where('status', 'approved')
            ->where('start_date', '>=', today())
            ->with('category', 'ticketTypes')
            ->orderBy('start_date');

        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->filled('type')) {
            $query->where('is_virtual', $request->type === 'virtual');
        }

        if ($request->filled('price')) {
            if ($request->price === 'free') {
                $query->whereDoesntHave('ticketTypes', fn($q) => $q->where('price', '>', 0));
            } elseif ($request->price === 'paid') {
                $query->whereHas('ticketTypes', fn($q) => $q->where('price', '>', 0));
            }
        }

        $events     = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('events.index', compact('events', 'categories'));
    }

    public function show(string $slug)
    {
        $event = Event::where('slug', $slug)
            ->where('status', 'approved')
            ->with('category', 'organizer.organizerProfile', 'ticketTypes')
            ->withCount(['reviews', 'waitlist'])
            ->withAvg('reviews', 'rating')
            ->firstOrFail();

        $reviews = $event->reviews()
            ->where('is_visible', true)
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(6);

        $savedEventIds = auth()->check()
            ? \App\Models\SavedEvent::where('user_id', auth()->id())->pluck('event_id')->all()
            : [];

        $isSaved = in_array($event->id, $savedEventIds);

        $userWaitlistIds = auth()->check()
            ? \App\Models\WaitlistEntry::where('user_id', auth()->id())
                ->where('event_id', $event->id)
                ->pluck('ticket_type_id')
                ->all()
            : [];

        $canReview = false;
        $hasReviewed = false;
        if (auth()->check()) {
            $hasReviewed = $event->reviews()->where('user_id', auth()->id())->exists();
            $canReview = ! $hasReviewed && \App\Models\Ticket::where('user_id', auth()->id())
                ->where('event_id', $event->id)
                ->where('status', 'used')
                ->exists();
        }

        return view('events.show', compact('event', 'isSaved', 'reviews', 'canReview', 'hasReviewed', 'userWaitlistIds'));
    }

    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $events = Event::where('status', 'approved')
            ->where('category_id', $category->id)
            ->where('start_date', '>=', today())
            ->with('category', 'ticketTypes')
            ->orderBy('start_date')
            ->paginate(12);

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('events.category', compact('category', 'events', 'categories'));
    }

    public function suggestions(Request $request)
    {
        $q = trim($request->input('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = Event::where('status', 'approved')
            ->where('start_date', '>=', today())
            ->where('title', 'like', '%' . $q . '%')
            ->select('id', 'title', 'slug', 'start_date', 'city')
            ->orderBy('start_date')
            ->limit(6)
            ->get()
            ->map(fn($e) => [
                'title' => $e->title,
                'slug'  => $e->slug,
                'date'  => $e->start_date->format('d M Y'),
                'city'  => $e->city,
                'url'   => route('events.show', $e->slug),
            ]);

        return response()->json($results);
    }
}
