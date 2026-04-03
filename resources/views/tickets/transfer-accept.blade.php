@extends('layouts.public')

@section('title', 'Accept Ticket Transfer')

@section('content')
<div class="max-w-xl mx-auto px-4 py-10">

    <div class="card p-8 text-center">

        @if ($transfer->status === 'completed')
            {{-- Already completed --}}
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Transfer Complete</h1>
            <p class="text-gray-500">This ticket has already been transferred.</p>
            <a href="{{ route('events.index') }}" class="btn-primary inline-block mt-6 px-8 py-3">Browse Events</a>

        @elseif ($transfer->status === 'cancelled' || $transfer->isExpired())
            {{-- Expired / cancelled --}}
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Transfer Expired</h1>
            <p class="text-gray-500">This transfer link has expired or been cancelled by the sender.</p>
            <a href="{{ route('events.index') }}" class="btn-primary inline-block mt-6 px-8 py-3">Browse Events</a>

        @else
            {{-- Pending — show accept form --}}
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-1">You've received a ticket!</h1>
            <p class="text-gray-500 mb-6">{{ $transfer->fromUser->name }} is transferring the following ticket to you.</p>

            <div class="bg-gray-50 border border-gray-100 rounded-lg p-5 text-left mb-6">
                <p class="font-semibold text-gray-800 text-lg">{{ $transfer->ticket->event->title }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $transfer->ticket->ticketType->name }}</p>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $transfer->ticket->event->start_date->format('l, F j, Y') }}
                </p>
                @unless ($transfer->ticket->event->is_virtual)
                    <p class="text-sm text-gray-500 mt-0.5">
                        {{ $transfer->ticket->event->venue_name }}, {{ $transfer->ticket->event->city }}
                    </p>
                @endunless
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4 text-sm text-red-700 text-left">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('tickets.transfer.confirm', $transfer->token) }}" method="POST" class="space-y-4 text-left">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Your Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $transfer->to_name) }}"
                           class="input-field w-full" required>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Your Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $transfer->to_email) }}"
                           class="input-field w-full" required>
                </div>

                <button type="submit" class="btn-primary w-full py-3 text-center mt-2">
                    Accept Ticket
                </button>
            </form>

            <p class="text-xs text-gray-400 mt-4">
                Expires {{ $transfer->expires_at->diffForHumans() }}.
                By accepting, the ticket will be registered in your name.
            </p>
        @endif

    </div>
</div>
@endsection
