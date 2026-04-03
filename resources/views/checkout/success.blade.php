@extends('layouts.public')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-14">

    {{-- Success header --}}
    <div class="text-center mb-10">
        <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-3xl font-black text-gray-900">You're going!</h1>
        <p class="text-gray-500 mt-2">
            Your tickets have been booked. Check your email for confirmation.
        </p>
    </div>

    {{-- Order summary card --}}
    <div class="card p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-900">Order {{ $order->order_number }}</h2>
            <span class="badge-approved">Confirmed</span>
        </div>

        {{-- Event brief --}}
        <div class="flex gap-4 p-4 bg-gray-50 rounded-xl mb-5">
            @if($order->event->banner)
                <img src="{{ Storage::url($order->event->banner) }}" alt="" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
            @endif
            <div>
                <p class="font-semibold text-gray-900 text-sm">{{ $order->event->title }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $order->event->start_date->format('D, M j, Y') }} at {{ $order->event->start_time }}</p>
                @if(!$order->event->is_virtual)
                    <p class="text-xs text-gray-500">{{ $order->event->city }}, {{ $order->event->state }}</p>
                @else
                    <p class="text-xs text-gray-500">Online</p>
                @endif
            </div>
        </div>

        {{-- Items --}}
        <div class="space-y-2 mb-4">
            @foreach($order->items as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-700">{{ $item->ticketType->name }} × {{ $item->quantity }}</span>
                    <span class="font-medium text-gray-900">
                        {{ $item->unit_price > 0 ? '₦'.number_format($item->subtotal) : 'Free' }}
                    </span>
                </div>
            @endforeach
        </div>

        <div class="border-t border-gray-100 pt-3 flex justify-between font-bold text-gray-900">
            <span>Total paid</span>
            <span>{{ $order->total_amount > 0 ? '₦'.number_format($order->total_amount) : 'Free' }}</span>
        </div>
    </div>

    {{-- Tickets --}}
    <div class="space-y-3 mb-8">
        <h2 class="font-bold text-gray-900">Your Tickets</h2>

        @foreach($order->tickets as $ticket)
            <div class="card p-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    <div class="text-center">
        <a href="{{ route('events.index') }}" class="btn-secondary">Browse more events</a>
    </div>

</div>
@endsection
