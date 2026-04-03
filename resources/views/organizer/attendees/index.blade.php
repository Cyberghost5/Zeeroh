<x-organizer-layout>
    @section('page-title', 'Attendees — ' . $event->title)
    @section('page-subtitle', $tickets->total() . ' attendees registered')

    @section('header-actions')
        <a href="{{ route('organizer.events.scanner', $event) }}" class="btn-primary text-sm">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5a.5.5 0 11-1 0 .5.5 0 011 0zM6 13.5a.5.5 0 11-1 0 .5.5 0 011 0zM4 20h4"/>
            </svg>
            QR Scanner
        </a>
    @endsection

    {{-- Back + filters --}}
    <div class="mb-5 flex flex-col sm:flex-row gap-3">
        <a href="{{ route('organizer.events.show', $event) }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1.5 self-start">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Event
        </a>
        <form method="GET" class="flex gap-2 flex-1 sm:justify-end flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search name, email, ticket code…"
                   class="input text-sm w-full sm:w-72">
            <select name="status" class="input text-sm w-auto">
                <option value="">All statuses</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="used" @selected(request('status') === 'used')>Checked In</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
            </select>
            <button type="submit" class="btn-primary text-sm px-4">Filter</button>
            @if(request('search') || request('status'))
                <a href="{{ route('organizer.events.attendees', $event) }}" class="btn-secondary text-sm px-4">Clear</a>
            @endif
        </form>
    </div>

    <div class="card overflow-hidden">
        @if($tickets->isEmpty())
            <div class="text-center py-14">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="text-sm text-gray-500">No attendees found.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Attendee</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Ticket</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Code</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Purchased</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Status</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Checked In</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($tickets as $ticket)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <p class="font-medium text-gray-900">{{ $ticket->holder_name ?? $ticket->user?->name ?? '—' }}</p>
                                    <p class="text-xs text-gray-400">{{ $ticket->holder_email ?? $ticket->user?->email ?? '—' }}</p>
                                </td>
                                <td class="px-5 py-3 text-gray-700">{{ $ticket->ticketType->name }}</td>
                                <td class="px-5 py-3">
                                    <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded">{{ $ticket->ticket_code }}</span>
                                </td>
                                <td class="px-5 py-3 text-gray-500 text-xs">
                                    {{ $ticket->order->paid_at?->format('M j, Y') ?? '—' }}
                                </td>
                                <td class="px-5 py-3">
                                    @if($ticket->status === 'used')
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 bg-green-50 px-2 py-0.5 rounded-full">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full inline-block"></span> Checked In
                                        </span>
                                    @elseif($ticket->status === 'active')
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full">
                                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full inline-block"></span> Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">{{ ucfirst($ticket->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-xs text-gray-500">
                                    {{ $ticket->checked_in_at?->format('M j, H:i') ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-4 border-t border-gray-100">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>

</x-organizer-layout>
