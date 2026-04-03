<x-organizer-layout>
    <x-slot name="header">Create New Event</x-slot>

    <div class="max-w-4xl mx-auto">
        @include('organizer.events._form', [
            'event'      => null,
            'categories' => $categories,
            'action'     => route('organizer.events.store'),
            'method'     => 'POST',
        ])
    </div>

    @stack('scripts')
</x-organizer-layout>
