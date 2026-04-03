<x-organizer-layout>
    <x-slot name="header">Event Details</x-slot>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl px-4 py-3">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Page header row --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-xl font-bold text-gray-900">{{ $event->title }}</h1>
                <span class="badge-{{ $event->status }}">{{ ucfirst($event->status) }}</span>
                @if($event->is_featured)
                    <span class="badge-featured">Featured</span>
                @endif
            </div>
            <p class="text-sm text-gray-500 mt-1">
                Created {{ $event->created_at->diffForHumans() }}
            </p>
        </div>

        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('organizer.events.edit', $event) }}" class="btn-primary">Edit Event</a>
            <a href="{{ route('organizer.events.index') }}" class="btn-secondary">← Back</a>
        </div>
    </div>

    {{-- Rejection reason --}}
    @if($event->status === 'rejected' && $event->rejection_reason)
        <div class="mb-5 flex gap-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9v4a1 1 0 102 0V9a1 1 0 10-2 0zm0-4a1 1 0 112 0 1 1 0 01-2 0z" clip-rule="evenodd"/>
            </svg>
            <div><p class="font-semibold">Rejection reason:</p><p>{{ $event->rejection_reason }}</p></div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main info --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Banner --}}
            @if($event->banner)
                <div class="card overflow-hidden">
                    <img src="{{ Storage::url($event->banner) }}" alt="{{ $event->title }}" class="w-full h-60 object-cover">
                </div>
            @endif

            {{-- Details card --}}
            <div class="card p-6">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Event Info</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex gap-4">
                        <dt class="w-32 flex-shrink-0 text-gray-500">Category</dt>
                        <dd class="text-gray-900">{{ $event->category?->name ?? '—' }}</dd>
                    </div>
                    <div class="flex gap-4">
                        <dt class="w-32 flex-shrink-0 text-gray-500">Start</dt>
                        <dd class="text-gray-900">{{ $event->start_date->format('D, M j, Y') }} at {{ $event->start_time }}</dd>
                    </div>
                    @if($event->end_date)
                    <div class="flex gap-4">
                        <dt class="w-32 flex-shrink-0 text-gray-500">End</dt>
                        <dd class="text-gray-900">{{ $event->end_date->format('D, M j, Y') }} at {{ $event->end_time }}</dd>
                    </div>
                    @endif
                    <div class="flex gap-4">
                        <dt class="w-32 flex-shrink-0 text-gray-500">Type</dt>
                        <dd class="text-gray-900">{{ $event->is_virtual ? 'Virtual / Online' : 'Physical' }}</dd>
                    </div>
                    @if(!$event->is_virtual)
                        <div class="flex gap-4">
                            <dt class="w-32 flex-shrink-0 text-gray-500">Venue</dt>
                            <dd class="text-gray-900">{{ $event->venue_name }}, {{ $event->venue_address }}, {{ $event->city }}, {{ $event->state }}</dd>
                        </div>
                    @else
                        <div class="flex gap-4">
                            <dt class="w-32 flex-shrink-0 text-gray-500">Stream Link</dt>
                            <dd class="text-gray-900 truncate">
                                @if($event->status === 'approved')
                                    <a href="{{ $event->virtual_link }}" target="_blank" class="text-primary-600 hover:underline">{{ $event->virtual_link }}</a>
                                @else
                                    <span class="text-gray-400 italic">Visible once approved</span>
                                @endif
                            </dd>
                        </div>
                    @endif
                </dl>

                <div class="mt-5 pt-5 border-t border-gray-100">
                    <p class="text-sm font-semibold text-gray-700 mb-2">Description</p>
                    <p class="text-sm text-gray-600 whitespace-pre-line">{{ $event->description }}</p>
                </div>
            </div>

            {{-- Ticket types --}}
            <div class="card p-6">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Ticket Types & Sales</h2>
                @if($event->ticketTypes->isEmpty())
                    <p class="text-sm text-gray-400">No ticket types defined.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    <th class="text-left pb-3 pr-4">Name</th>
                                    <th class="text-right pb-3 pr-4">Price</th>
                                    <th class="text-right pb-3 pr-4">Qty</th>
                                    <th class="text-right pb-3 pr-4">Sold</th>
                                    <th class="text-right pb-3">Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($event->ticketTypes as $type)
                                    @php
                                        $sold     = $type->quantity_sold;
                                        $revenue  = $sold * $type->price;
                                        $available = $type->quantity - $sold;
                                    @endphp
                                    <tr>
                                        <td class="py-3 pr-4 font-medium text-gray-900">
                                            {{ $type->name }}
                                            @if($type->description)
                                                <span class="block text-xs text-gray-400 font-normal">{{ $type->description }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 pr-4 text-right">
                                            {{ $type->price > 0 ? '₦'.number_format($type->price) : 'Free' }}
                                        </td>
                                        <td class="py-3 pr-4 text-right text-gray-600">{{ number_format($type->quantity) }}</td>
                                        <td class="py-3 pr-4 text-right">
                                            <span class="{{ $available <= 0 ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                                {{ number_format($sold) }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-right font-medium text-gray-900">
                                            ₦{{ number_format($revenue) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-t border-gray-200 text-sm font-semibold">
                                    <td class="pt-3 pr-4 text-gray-700">Total</td>
                                    <td></td>
                                    <td class="pt-3 pr-4 text-right text-gray-700">{{ number_format($event->ticketTypes->sum('quantity')) }}</td>
                                    <td class="pt-3 pr-4 text-right text-gray-700">{{ number_format($event->total_tickets_sold) }}</td>
                                    <td class="pt-3 text-right text-primary-700">
                                        ₦{{ number_format($event->ticketTypes->sum(fn($t) => $t->quantity_sold * $t->price)) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">

            {{-- Waitlist --}}
            @if($waitlist->isNotEmpty())
            <div class="card overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Waitlist</h3>
                    <span class="bg-amber-100 text-amber-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $waitlist->count() }}</span>
                </div>
                <ul class="divide-y divide-gray-50">
                    @foreach($waitlist as $entry)
                    <li class="flex items-center gap-3 px-5 py-3">
                        <div class="w-7 h-7 rounded-full bg-primary-100 flex items-center justify-center text-xs font-bold text-primary-700 flex-shrink-0">
                            {{ strtoupper(substr($entry->user->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $entry->user->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $entry->ticketType?->name ?? 'Any' }} &bull; {{ $entry->created_at->format('d M') }}</p>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Quick stats --}}
            <div class="card p-5">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">Quick Stats</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Total tickets sold</span>
                        <span class="font-semibold text-gray-900">{{ number_format($event->total_tickets_sold) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Ticket types</span>
                        <span class="font-semibold text-gray-900">{{ $event->ticketTypes->count() }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Min. ticket price</span>
                        <span class="font-semibold text-gray-900">
                            {{ $event->min_price > 0 ? '₦'.number_format($event->min_price) : 'Free' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Danger zone (delete) --}}
            @if($event->total_tickets_sold === 0 && in_array($event->status, ['draft', 'rejected']))
                <div class="card p-5 border-red-200"
                     x-data="{ confirmDelete: false }">
                    <h3 class="text-xs font-semibold text-red-600 uppercase tracking-wide mb-3">Danger Zone</h3>

                    <button @click="confirmDelete = true" type="button"
                            class="w-full btn-danger text-sm py-2" x-show="!confirmDelete">
                        Delete this event
                    </button>

                    <div x-show="confirmDelete" x-cloak class="space-y-3">
                        <p class="text-sm text-red-700">Are you sure? This cannot be undone.</p>
                        <form method="POST" action="{{ route('organizer.events.destroy', $event) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full btn-danger text-sm py-2">Yes, delete permanently</button>
                        </form>
                        <button @click="confirmDelete = false" type="button" class="w-full btn-secondary text-sm py-2">Cancel</button>
                    </div>
                </div>
            @endif

        </div>

    </div>

</x-organizer-layout>
