<x-app-layout>
    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl px-4 py-3">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($tickets->isEmpty())
        {{-- Empty state --}}
        <div class="card p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-50 mb-4">
                <svg class="w-8 h-8 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1">No tickets yet</h3>
            <p class="text-sm text-gray-500 mb-6 max-w-sm mx-auto">
                You haven't purchased any tickets. Browse events and grab your spot!
            </p>
            <a href="{{ route('events.index') }}" class="btn-primary">Browse Events</a>
        </div>
    @else
        {{-- Group tickets by event --}}
        @php $grouped = $tickets->groupBy('event_id'); @endphp

        <div class="space-y-5">
            @foreach($grouped as $eventId => $eventTickets)
                @php $event = $eventTickets->first()->event; @endphp
                <div class="card overflow-hidden">
                    {{-- Event header --}}
                    <div class="flex items-center gap-4 p-5 border-b border-gray-100 bg-gray-50">
                        @if($event->banner)
                            <img src="{{ Storage::url($event->banner) }}" class="w-14 h-14 object-cover rounded-lg flex-shrink-0" alt="">
                        @else
                            <div class="w-14 h-14 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-7 h-7 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 truncate">{{ $event->title }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $event->start_date->format('D, M j, Y') }} ·
                                @if($event->is_virtual) Online @else {{ $event->city }} @endif
                            </p>
                        </div>
                        @if($event->start_date->isFuture())
                            <span class="badge-approved text-xs flex-shrink-0">Upcoming</span>
                        @else
                            <span class="badge-draft text-xs flex-shrink-0">Past</span>
                        @endif
                    </div>

                    {{-- Individual tickets --}}
                    <div class="divide-y divide-gray-50">
                        @foreach($eventTickets as $ticket)
                            <div class="flex items-center justify-between gap-4 px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $ticket->ticketType->name }}</p>
                                        <p class="text-xs text-gray-400 font-mono">{{ $ticket->ticket_code }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('tickets.show', $ticket->ticket_code) }}"
                                       class="btn-secondary text-xs py-1.5 px-3">View</a>
                                    <a href="{{ route('tickets.download', $ticket->ticket_code) }}"
                                       class="btn-primary text-xs py-1.5 px-3">Download PDF</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
