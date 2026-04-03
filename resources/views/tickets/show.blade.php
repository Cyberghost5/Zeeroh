@extends('layouts.public')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">

    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-primary-600">← My Tickets</a>
        <div class="flex items-center gap-2">
            @if ($ticket->status === 'active' && $ticket->event->start_date->isFuture())
                <a href="{{ route('tickets.transfer.show', $ticket->ticket_code) }}"
                   class="btn-outline text-sm py-2 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Transfer
                </a>
            @endif
            <a href="{{ route('tickets.download', $ticket->ticket_code) }}"
               class="btn-primary text-sm py-2 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download PDF
            </a>
        </div>
    </div>

    {{-- Ticket card --}}
    <div class="card overflow-hidden">

        {{-- Event banner strip --}}
        @if($ticket->event->banner)
            <img src="{{ Storage::url($ticket->event->banner) }}" alt="" class="w-full h-32 object-cover">
        @else
            <div class="w-full h-32 bg-gradient-to-r from-primary-800 to-primary-600"></div>
        @endif

        <div class="p-6">
            <div class="flex items-start justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-xl font-black text-gray-900">{{ $ticket->event->title }}</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $ticket->event->start_date->format('l, F j, Y') }} at {{ $ticket->event->start_time }}
                    </p>
                    @if(!$ticket->event->is_virtual)
                        <p class="text-sm text-gray-500">{{ $ticket->event->venue_name }}, {{ $ticket->event->city }}</p>
                    @else
                        <p class="text-sm text-gray-500">Online Event</p>
                    @endif
                </div>
                <span class="badge-{{ $ticket->status }} flex-shrink-0">{{ ucfirst($ticket->status) }}</span>
            </div>

            {{-- Divider with circles --}}
            <div class="relative flex items-center gap-4 my-5">
                <div class="absolute -left-6 w-5 h-5 rounded-full bg-gray-100 border border-gray-200"></div>
                <div class="flex-1 border-t-2 border-dashed border-gray-200"></div>
                <div class="absolute -right-6 w-5 h-5 rounded-full bg-gray-100 border border-gray-200"></div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                {{-- Ticket info --}}
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Ticket Type</p>
                        <p class="font-semibold text-gray-900">{{ $ticket->ticketType->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Ticket Holder</p>
                        <p class="font-semibold text-gray-900">{{ $ticket->holder_name }}</p>
                        <p class="text-xs text-gray-500">{{ $ticket->holder_email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Order</p>
                        <p class="font-mono text-sm text-gray-900">{{ $ticket->order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Ticket Code</p>
                        <p class="font-mono text-sm font-bold text-primary-700 tracking-wider">{{ $ticket->ticket_code }}</p>
                    </div>
                </div>

                {{-- QR Code --}}
                <div class="flex flex-col items-center justify-center">
                    @if($ticket->qr_code_path && Storage::disk('public')->exists($ticket->qr_code_path))
                        {!! Storage::disk('public')->get($ticket->qr_code_path) !!}
                    @else
                        <div class="w-32 h-32 bg-gray-100 rounded-lg flex items-center justify-center">
                            <p class="text-xs text-gray-400 text-center">QR code<br>not available</p>
                        </div>
                    @endif
                    <p class="text-xs text-gray-400 mt-2 text-center">Show at the door</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
