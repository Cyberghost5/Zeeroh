<x-organizer-layout>
    <x-slot name="header">My Events</x-slot>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl px-4 py-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-xl px-4 py-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9v4a1 1 0 102 0V9a1 1 0 10-2 0zm0-4a1 1 0 112 0 1 1 0 01-2 0z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- top bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <p class="text-sm text-gray-500">{{ $events->total() }} event{{ $events->total() !== 1 ? 's' : '' }} total</p>
        </div>
        <a href="{{ route('organizer.events.create') }}" class="btn-primary inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Event
        </a>
    </div>

    {{-- Status filter tabs --}}
    <div class="flex gap-1 flex-wrap mb-6 bg-gray-100 rounded-xl p-1 w-fit">
        @foreach(['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'draft' => 'Drafts'] as $key => $label)
            @php $count = $statusCounts[$key] ?? ($key === 'all' ? $events->total() : 0); @endphp
            <a href="{{ route('organizer.events.index', $key !== 'all' ? ['status' => $key] : []) }}"
               class="px-4 py-1.5 rounded-lg text-sm font-medium transition-colors
                      {{ request('status', 'all') === $key
                            ? 'bg-white text-primary-700 shadow-sm'
                            : 'text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
                <span class="ml-1 text-xs {{ request('status', 'all') === $key ? 'text-primary-500' : 'text-gray-400' }}">
                    {{ $count }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- Events grid --}}
    @if($events->isEmpty())
        <div class="card p-12 text-center">
            <div class="stat-icon bg-primary-50 mx-auto mb-4 w-16 h-16">
                <svg class="w-8 h-8 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1">No events yet</h3>
            <p class="text-sm text-gray-500 mb-5">Create your first event to start selling tickets.</p>
            <a href="{{ route('organizer.events.create') }}" class="btn-primary inline-flex items-center gap-2 mx-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Event
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($events as $event)
                <div class="card overflow-hidden flex flex-col hover:shadow-md transition-shadow">

                    {{-- Banner --}}
                    @if($event->banner)
                        <img src="{{ Storage::url($event->banner) }}" alt="{{ $event->title }}"
                             class="h-40 w-full object-cover">
                    @else
                        <div class="h-40 w-full bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center">
                            <svg class="w-10 h-10 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif

                    <div class="p-4 flex flex-col flex-1">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h3 class="font-semibold text-gray-900 text-sm leading-snug line-clamp-2">{{ $event->title }}</h3>
                            <span class="badge-{{ $event->status }} flex-shrink-0 text-xs">{{ ucfirst($event->status) }}</span>
                        </div>

                        <p class="text-xs text-gray-500 mb-3 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $event->start_date->format('D, M j, Y') }}
                            @if(!$event->is_virtual && $event->city)
                                · {{ $event->city }}
                            @elseif($event->is_virtual)
                                · Online
                            @endif
                        </p>

                        @if($event->status === 'rejected' && $event->rejection_reason)
                            <p class="text-xs text-red-600 bg-red-50 rounded-lg px-3 py-2 mb-3 line-clamp-2">
                                <span class="font-medium">Rejected:</span> {{ $event->rejection_reason }}
                            </p>
                        @endif

                        {{-- Ticket summary --}}
                        <div class="mt-auto flex items-center justify-between text-xs text-gray-500 pt-3 border-t border-gray-100">
                            <span>{{ $event->ticket_types_count ?? $event->ticketTypes->count() }} ticket type(s)</span>
                            <span class="font-medium text-gray-700">{{ $event->total_tickets_sold }} sold</span>
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-2 mt-3">
                            <a href="{{ route('organizer.events.show', $event) }}"
                               class="flex-1 text-center btn-secondary text-xs py-1.5">View</a>
                            <a href="{{ route('organizer.events.edit', $event) }}"
                               class="flex-1 text-center btn-primary text-xs py-1.5">Edit</a>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $events->appends(request()->query())->links() }}
        </div>
    @endif

</x-organizer-layout>
