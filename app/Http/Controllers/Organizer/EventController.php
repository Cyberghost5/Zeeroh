<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organizer\StoreEventRequest;
use App\Http\Requests\Organizer\UpdateEventRequest;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::where('organizer_id', auth()->id())
            ->with('category', 'ticketTypes')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $events = $query->paginate(12);
        $statusCounts = Event::where('organizer_id', auth()->id())
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('organizer.events.index', compact('events', 'statusCounts'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('organizer.events.create', compact('categories'));
    }

    public function store(StoreEventRequest $request)
    {
        $data = $request->validated();

        $bannerPath = null;
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('banners', 'public');
        }

        $event = Event::create([
            'organizer_id'  => auth()->id(),
            'category_id'   => $data['category_id'] ?? null,
            'title'         => $data['title'],
            'description'   => $data['description'],
            'banner'        => $bannerPath,
            'venue_name'    => $data['venue_name'] ?? null,
            'venue_address' => $data['venue_address'] ?? null,
            'city'          => $data['city'] ?? null,
            'state'         => $data['state'] ?? null,
            'is_virtual'    => $data['is_virtual'] ?? false,
            'virtual_link'  => $data['virtual_link'] ?? null,
            'start_date'    => $data['start_date'],
            'end_date'      => $data['end_date'] ?? null,
            'start_time'    => $data['start_time'],
            'end_time'      => $data['end_time'] ?? null,
            'status'        => 'pending',
        ]);

        foreach ($data['ticket_types'] as $ticketType) {
            $event->ticketTypes()->create([
                'name'          => $ticketType['name'],
                'description'   => $ticketType['description'] ?? null,
                'price'         => $ticketType['price'],
                'quantity'      => $ticketType['quantity'],
                'max_per_order' => $ticketType['max_per_order'] ?? 10,
                'sale_start'    => $ticketType['sale_start'] ?? null,
                'sale_end'      => $ticketType['sale_end'] ?? null,
                'is_active'     => true,
            ]);
        }

        return redirect()
            ->route('organizer.events.index')
            ->with('success', "Event submitted for review! You'll be notified once approved.");
    }

    public function show(Event $event)
    {
        $this->authorizeEvent($event);
        $event->load('category', 'ticketTypes');

        $salesData = $event->ticketTypes->map(fn($tt) => [
            'name'     => $tt->name,
            'quantity' => $tt->quantity,
            'sold'     => $tt->quantity_sold,
            'revenue'  => $tt->quantity_sold * $tt->price,
        ]);

        $waitlist = $event->waitlist()
            ->with('user', 'ticketType')
            ->orderBy('created_at')
            ->get();

        return view('organizer.events.show', compact('event', 'salesData', 'waitlist'));
    }

    public function edit(Event $event)
    {
        $this->authorizeEvent($event);

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $event->load('ticketTypes');

        return view('organizer.events.edit', compact('event', 'categories'));
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        $data = $request->validated();

        if ($request->hasFile('banner')) {
            if ($event->banner) {
                Storage::disk('public')->delete($event->banner);
            }
            $data['banner'] = $request->file('banner')->store('banners', 'public');
        } elseif ($request->boolean('remove_banner') && $event->banner) {
            Storage::disk('public')->delete($event->banner);
            $data['banner'] = null;
        } else {
            unset($data['banner']);
        }

        unset($data['remove_banner']);

        // Sync ticket types
        $submittedTickets = $data['ticket_types'] ?? [];
        unset($data['ticket_types']);

        // Only keep real integer DB ids (empty string for new tickets gets filtered out)
        $submittedIds = collect($submittedTickets)
            ->pluck('id')
            ->filter(fn($id) => is_numeric($id) && (int) $id > 0)
            ->map(fn($id) => (int) $id)
            ->all();

        // Work out which of this event's ticket types were removed
        $existingIds = $event->ticketTypes()->pluck('id')->all();
        $removedIds  = array_values(array_diff($existingIds, $submittedIds));

        if (!empty($removedIds)) {
            // Delete ticket types that have no orders
            $event->ticketTypes()
                ->whereIn('id', $removedIds)
                ->whereDoesntHave('orderItems')
                ->delete();

            // Deactivate ones with orders (preserve data integrity)
            $event->ticketTypes()
                ->whereIn('id', $removedIds)
                ->whereHas('orderItems')
                ->update(['is_active' => false]);
        }

        foreach ($submittedTickets as $tt) {
            $ttId = isset($tt['id']) && is_numeric($tt['id']) && (int) $tt['id'] > 0
                ? (int) $tt['id'] : null;
            $fields = [
                'name'          => $tt['name'],
                'description'   => $tt['description'] ?? null,
                'price'         => $tt['price'],
                'quantity'      => $tt['quantity'],
                'max_per_order' => $tt['max_per_order'] ?? 10,
            ];

            $existing = $ttId ? $event->ticketTypes()->find($ttId) : null;

            if ($existing) {
                $existing->update($fields);
            } else {
                $event->ticketTypes()->create(array_merge($fields, ['is_active' => true]));
            }
        }

        // Approved events stay approved; everything else goes back to pending review
        $newStatus = $event->status === 'approved' ? 'approved' : 'pending';
        $event->update(array_merge($data, ['status' => $newStatus]));

        $message = $newStatus === 'approved'
            ? 'Event updated successfully.'
            : 'Event updated and re-submitted for approval.';

        return redirect()
            ->route('organizer.events.show', $event)
            ->with('success', $message);
    }

    public function destroy(Event $event)
    {
        $this->authorizeEvent($event);

        if ($event->status === 'approved' && $event->total_tickets_sold > 0) {
            return back()->with('error', 'Cannot delete an event that has sold tickets.');
        }

        if ($event->banner) {
            Storage::disk('public')->delete($event->banner);
        }

        $event->delete();

        return redirect()
            ->route('organizer.events.index')
            ->with('success', 'Event deleted successfully.');
    }

    private function authorizeEvent(Event $event): void
    {
        if ($event->organizer_id !== auth()->id()) {
            abort(403);
        }
    }
}

