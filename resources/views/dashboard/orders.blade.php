<x-app-layout>
    @if($orders->isEmpty())
        <div class="card p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-50 mb-4">
                <svg class="w-8 h-8 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1">No orders yet</h3>
            <p class="text-sm text-gray-500 mb-6">Your completed orders will appear here.</p>
            <a href="{{ route('events.index') }}" class="btn-primary">Browse Events</a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
            <div class="card overflow-hidden">
                {{-- Order header --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4 bg-gray-50 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div>
                            <p class="text-sm font-bold text-gray-900 font-mono">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-400">{{ $order->created_at->format('d M Y, g:ia') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-xs text-gray-400">Total</p>
                            <p class="text-sm font-bold text-gray-900">₦{{ number_format($order->total_amount) }}</p>
                        </div>
                        <span class="text-xs bg-green-100 text-green-700 font-medium px-2.5 py-1 rounded-full capitalize">
                            {{ $order->payment_status }}
                        </span>
                    </div>
                </div>

                {{-- Event + items --}}
                <div class="px-5 py-4">
                    <div class="flex items-center gap-3 mb-3">
                        @if($order->event?->banner)
                            <img src="{{ Storage::url($order->event->banner) }}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0" alt="">
                        @else
                            <div class="w-10 h-10 bg-primary-100 rounded-lg flex-shrink-0 flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $order->event?->title ?? 'Event' }}</p>
                            <p class="text-xs text-gray-400">{{ $order->event?->start_date?->format('D, M j, Y') }}</p>
                        </div>
                    </div>

                    <table class="w-full text-xs text-gray-600">
                        <tbody class="divide-y divide-gray-50">
                            @foreach($order->items as $item)
                            <tr>
                                <td class="py-1.5">{{ $item->ticketType?->name }}</td>
                                <td class="text-center text-gray-400">× {{ $item->quantity }}</td>
                                <td class="text-right font-medium text-gray-900">₦{{ number_format($item->subtotal) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t border-gray-200">
                                <td colspan="2" class="pt-2 text-gray-500">Service fee</td>
                                <td class="pt-2 text-right text-gray-500">₦{{ number_format($order->service_fee) }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="pt-1 font-bold text-gray-900">Total paid</td>
                                <td class="pt-1 text-right font-bold text-gray-900">₦{{ number_format($order->total_amount) }}</td>
                            </tr>
                        </tfoot>
                    </table>

                    {{-- Ticket links --}}
                    @if($order->tickets->isNotEmpty())
                    <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-gray-100">
                        @foreach($order->tickets as $ticket)
                            <a href="{{ route('tickets.show', $ticket->ticket_code) }}"
                               class="text-xs text-primary-600 bg-primary-50 hover:bg-primary-100 px-2.5 py-1 rounded-lg font-mono transition-colors">
                                {{ $ticket->ticket_code }}
                            </a>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif
</x-app-layout>
