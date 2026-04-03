<x-admin-layout>
    @section('page-title', 'Revenue Overview')
    @section('page-subtitle', 'Platform earnings and event-level breakdown')

    {{-- Summary stats --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <div class="stat-icon bg-yellow-50">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Gross Revenue</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">₦{{ number_format($summary['gross_revenue'], 0) }}</p>
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
                <p class="text-2xl font-bold text-gray-900 mt-0.5">₦{{ number_format($summary['commission_earned'], 0) }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-blue-50">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Service Fees</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">₦{{ number_format($summary['service_fees'], 0) }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-purple-50">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Orders</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($summary['total_orders']) }}</p>
            </div>
        </div>
    </div>

    {{-- Event breakdown table --}}
    <div class="card overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900 text-sm">Revenue by Event</h2>
            <a href="{{ route('admin.revenue.payouts') }}" class="text-xs text-primary-600 font-medium hover:text-primary-700">View Organizer Payouts →</a>
        </div>

        @if($event_breakdown->isEmpty())
            <div class="text-center py-14 text-sm text-gray-400">No paid orders yet.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Event</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Organizer</th>
                            <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Orders</th>
                            <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Gross</th>
                            <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Commission</th>
                            <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Service Fee</th>
                            <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Total Collected</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($event_breakdown as $row)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <p class="font-medium text-gray-900 truncate max-w-[220px]">{{ $row->event->title }}</p>
                                    <p class="text-xs text-gray-400">{{ $row->event->start_date->format('M j, Y') }}</p>
                                </td>
                                <td class="px-5 py-3 text-gray-600 text-xs">{{ $row->event->organizer->name ?? '—' }}</td>
                                <td class="px-5 py-3 text-right text-gray-700">{{ number_format($row->order_count) }}</td>
                                <td class="px-5 py-3 text-right text-gray-700 font-medium">₦{{ number_format($row->gross, 0) }}</td>
                                <td class="px-5 py-3 text-right text-green-700 font-semibold">₦{{ number_format($row->commission, 0) }}</td>
                                <td class="px-5 py-3 text-right text-blue-700">₦{{ number_format($row->service_fee, 0) }}</td>
                                <td class="px-5 py-3 text-right font-bold text-gray-900">₦{{ number_format($row->total, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-gray-200 bg-gray-50 font-semibold">
                            <td colspan="2" class="px-5 py-3 text-gray-700">Page Total</td>
                            <td class="px-5 py-3 text-right text-gray-700">{{ $event_breakdown->sum('order_count') }}</td>
                            <td class="px-5 py-3 text-right text-gray-700">₦{{ number_format($event_breakdown->sum('gross'), 0) }}</td>
                            <td class="px-5 py-3 text-right text-green-700">₦{{ number_format($event_breakdown->sum('commission'), 0) }}</td>
                            <td class="px-5 py-3 text-right text-blue-700">₦{{ number_format($event_breakdown->sum('service_fee'), 0) }}</td>
                            <td class="px-5 py-3 text-right text-gray-900">₦{{ number_format($event_breakdown->sum('total'), 0) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $event_breakdown->links() }}
            </div>
        @endif
    </div>

</x-admin-layout>
