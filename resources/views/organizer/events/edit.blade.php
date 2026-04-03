<x-organizer-layout>
    <x-slot name="header">Edit Event</x-slot>

    <div class="max-w-4xl mx-auto">

        @if($event->status === 'rejected' && $event->rejection_reason)
            <div class="mb-5 flex gap-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-xl px-4 py-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9v4a1 1 0 102 0V9a1 1 0 10-2 0zm0-4a1 1 0 112 0 1 1 0 01-2 0z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="font-semibold mb-0.5">This event was rejected</p>
                    <p>{{ $event->rejection_reason }}</p>
                    <p class="mt-1 text-red-600 font-medium">Fix the issues above, then re-submit.</p>
                </div>
            </div>
        @endif

        @include('organizer.events._form', [
            'event'      => $event,
            'categories' => $categories,
            'action'     => route('organizer.events.update', $event),
            'method'     => 'PUT',
        ])

    </div>

    @stack('scripts')
</x-organizer-layout>
