<x-organizer-layout>
    @section('page-title', 'Dashboard')
    @section('page-subtitle', 'Your events at a glance')

    @section('header-actions')
        <a href="{{ route('organizer.events.create') }}" class="btn-primary text-sm">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Event
        </a>
    @endsection

    {{-- Stats row --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

        <div class="stat-card">
            <div class="stat-icon bg-blue-50">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Events</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($stats['total_events']) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $stats['active_events'] }} approved</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-purple-50">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Tickets Sold</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($stats['tickets_sold']) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $stats['checked_in'] }} checked in</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-yellow-50">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Gross Revenue</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($stats['total_revenue'], 0) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Before platform fees</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-green-50">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Net Earnings</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($stats['net_revenue'], 0) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">After {{ number_format($stats['commission_paid'], 0) }} fees</p>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Revenue by event --}}
        <div class="xl:col-span-2 card">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900 text-sm">Revenue by Event</h2>
                <a href="{{ route('organizer.events.index') }}" class="text-xs text-primary-600 font-medium hover:text-primary-700">All events</a>
            </div>
            @if($event_revenue->isEmpty())
                <div class="text-center py-10 text-sm text-gray-400">No revenue data yet.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-6 py-3">Event</th>
                                <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wide px-6 py-3">Tickets</th>
                                <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wide px-6 py-3">Gross</th>
                                <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wide px-6 py-3">Net</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($event_revenue as $ev)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3">
                                        <p class="font-medium text-gray-900 truncate max-w-[200px]">{{ $ev->title }}</p>
                                        <span class="badge-{{ $ev->status }} text-xs">{{ ucfirst($ev->status) }}</span>
                                    </td>
                                    <td class="px-6 py-3 text-right text-gray-600">{{ number_format($ev->tickets_sold) }}</td>
                                    <td class="px-6 py-3 text-right text-gray-700 font-medium">{{ number_format($ev->gross_revenue, 0) }}</td>
                                    <td class="px-6 py-3 text-right text-green-700 font-semibold">{{ number_format($ev->gross_revenue - $ev->commission_total, 0) }}</td>
                                    <td class="px-6 py-3 text-right">
                                        <a href="{{ route('organizer.events.attendees', $ev) }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Attendees</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Recent orders --}}
        <div class="card">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900 text-sm">Recent Orders</h2>
            </div>
            @if($recent_orders->isEmpty())
                <div class="text-center py-10 text-sm text-gray-400">No orders yet.</div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($recent_orders as $order)
                        <div class="px-6 py-3">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $order->user->name }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $order->event->title }}</p>
                                    <p class="text-xs text-gray-400">{{ $order->paid_at?->format('M j, H:i') }}</p>
                                </div>
                                <p class="text-sm font-semibold text-green-700 flex-shrink-0">{{ number_format($order->subtotal, 0) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</x-organizer-layout>
