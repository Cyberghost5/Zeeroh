@extends('layouts.public')

@section('title', 'Transfer Ticket')

@section('content')
<div class="max-w-xl mx-auto px-4 py-10">

    <div class="mb-6">
        <a href="{{ route('tickets.show', $ticket->ticket_code) }}" class="text-sm text-gray-500 hover:text-primary-600">← Back to Ticket</a>
    </div>

    <div class="card p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-1">Transfer Ticket</h1>
        <p class="text-sm text-gray-500 mb-6">Send this ticket to someone else. They will receive an email to accept it.</p>

        {{-- Ticket summary --}}
        <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-100">
            <p class="font-semibold text-gray-800">{{ $ticket->event->title }}</p>
            <p class="text-sm text-gray-500 mt-0.5">{{ $ticket->ticketType->name }} &mdash; <span class="font-mono">{{ $ticket->ticket_code }}</span></p>
            <p class="text-sm text-gray-500 mt-0.5">{{ $ticket->event->start_date->format('D, M j, Y') }}</p>
        </div>

        @if ($pendingTransfer)
            {{-- Pending transfer notice --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-yellow-800">Transfer pending</p>
                        <p class="text-sm text-yellow-700 mt-0.5">
                            A transfer to <strong>{{ $pendingTransfer->to_email }}</strong> is waiting to be accepted.
                            Expires {{ $pendingTransfer->expires_at->diffForHumans() }}.
                        </p>
                    </div>
                </div>
                <form action="{{ route('tickets.transfer.cancel', $ticket->ticket_code) }}" method="POST" class="mt-3">
                    @csrf
                    @method('DELETE')
                    <button class="text-sm text-red-600 hover:text-red-800 font-medium">Cancel transfer</button>
                </form>
            </div>
        @else
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('tickets.transfer.store', $ticket->ticket_code) }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="to_name" class="block text-sm font-medium text-gray-700 mb-1">Recipient Name</label>
                    <input type="text" id="to_name" name="to_name" value="{{ old('to_name') }}"
                           class="input-field w-full" placeholder="Full name" required>
                </div>

                <div>
                    <label for="to_email" class="block text-sm font-medium text-gray-700 mb-1">Recipient Email</label>
                    <input type="email" id="to_email" name="to_email" value="{{ old('to_email') }}"
                           class="input-field w-full" placeholder="email@example.com" required>
                    @if (auth()->user()->email)
                        <p class="text-xs text-gray-400 mt-1">Cannot transfer to yourself.</p>
                    @endif
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-primary w-full py-3">
                        Send Transfer Email
                    </button>
                </div>

                <p class="text-xs text-gray-400 text-center">
                    The recipient has 48 hours to accept the transfer. You can cancel it any time before then.
                </p>
            </form>
        @endif
    </div>
</div>
@endsection
