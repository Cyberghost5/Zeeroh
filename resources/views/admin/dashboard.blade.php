<x-admin-layout>
    @section('page-title', 'Dashboard')
    @section('page-subtitle', 'Welcome back, ' . auth()->user()->name)

    {{-- Stats row --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

        <div class="stat-card">
            <div class="stat-icon bg-blue-50">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Events</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($stats['total_events']) }}</p>
                <p class="text-xs text-yellow-600 font-medium mt-0.5">{{ $stats['pending_events'] }} pending</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-purple-50">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Users</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($stats['total_users']) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $stats['total_organizers'] }} organizers</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-yellow-50">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Gross Revenue</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">₦{{ number_format($stats['gross_revenue'], 0) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $stats['total_orders'] }} orders</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-green-50">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Commission Earned</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">₦{{ number_format($stats['commission_earned'], 0) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $stats['tickets_sold'] }} tickets sold</p>
            </div>
        </div>

    </div>

    {{-- Two-column grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Recent events --}}
        <div class="card">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900 text-sm">Recent Events</h2>
                <a href="{{ route('admin.events.index') }}" class="text-xs text-primary-600 font-medium hover:text-primary-700">View all</a>
            </div>
            @forelse($recent_events as $event)
                    <div class="flex items-start gap-3 px-6 py-3">
                        <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $event->title }}</p>
                            <p class="text-xs text-gray-500">{{ $event->organizer->name }} &bull; {{ $event->start_date->format('M d, Y') }}</p>
                        </div>
                        <span class="badge-{{ $event->status }} flex-shrink-0">{{ ucfirst($event->status) }}</span>
                    </div>
                @empty
                    <p class="px-6 py-6 text-sm text-gray-400 text-center">No events yet</p>
                @endforelse
        </div>

        {{-- Recent orders --}}
        <div class="card">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900 text-sm">Recent Orders</h2>
                <a href="{{ route('admin.revenue.index') }}" class="text-xs text-primary-600 font-medium hover:text-primary-700">View all</a>
            </div>
            @forelse($recent_orders as $order)
                    <div class="flex items-start gap-3 px-6 py-3">
                        <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-500">{{ $order->user->name }} &bull; {{ $order->event->title }}</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 flex-shrink-0">₦{{ number_format($order->total_amount, 0) }}</span>
                    </div>
                @empty
                    <p class="px-6 py-6 text-sm text-gray-400 text-center">No orders yet</p>
                @endforelse
        </div>

    </div>

</x-admin-layout>
