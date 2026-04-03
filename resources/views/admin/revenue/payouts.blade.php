<x-admin-layout>
    @section('page-title', 'Organizer Payouts')
    @section('page-subtitle', 'Net amount owed to each organizer after commission')

    <div class="card overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900 text-sm">All Organizers</h2>
            <a href="{{ route('admin.commission.edit') }}" class="text-xs text-primary-600 font-medium hover:text-primary-700">Commission Settings →</a>
        </div>

        @if($payouts->isEmpty())
            <div class="text-center py-14 text-sm text-gray-400">No organizers found.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Organizer</th>
                            <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Events</th>
                            <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Orders</th>
                            <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Gross Revenue</th>
                            <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Commission</th>
                            <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Net Payout</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($payouts as $organizer)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <p class="font-medium text-gray-900">{{ $organizer->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $organizer->email }}</p>
                                </td>
                                <td class="px-5 py-3 text-right text-gray-700">{{ $organizer->total_events }}</td>
                                <td class="px-5 py-3 text-right text-gray-700">{{ number_format($organizer->orders_count) }}</td>
                                <td class="px-5 py-3 text-right text-gray-700 font-medium">₦{{ number_format($organizer->gross_revenue, 0) }}</td>
                                <td class="px-5 py-3 text-right text-red-600">-₦{{ number_format($organizer->commission, 0) }}</td>
                                <td class="px-5 py-3 text-right">
                                    <span class="text-base font-bold {{ $organizer->net_payout > 0 ? 'text-green-700' : 'text-gray-500' }}">
                                        ₦{{ number_format($organizer->net_payout, 0) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-gray-200 bg-gray-50 font-semibold">
                            <td colspan="3" class="px-5 py-3 text-gray-700">Platform Total</td>
                            <td class="px-5 py-3 text-right text-gray-700">₦{{ number_format($payouts->sum('gross_revenue'), 0) }}</td>
                            <td class="px-5 py-3 text-right text-red-600">-₦{{ number_format($payouts->sum('commission'), 0) }}</td>
                            <td class="px-5 py-3 text-right text-green-700">₦{{ number_format($payouts->sum('net_payout'), 0) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

</x-admin-layout>
