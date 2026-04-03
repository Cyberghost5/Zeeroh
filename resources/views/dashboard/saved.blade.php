<x-app-layout>
    @if($savedEvents->isEmpty())
        <div class="card p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-50 mb-4">
                <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1">No saved events</h3>
            <p class="text-sm text-gray-500 mb-6 max-w-sm mx-auto">Browse events and tap the save button to add them here.</p>
            <a href="{{ route('events.index') }}" class="btn-primary">Browse Events</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($savedEvents as $saved)
                @include('partials.event-card', ['event' => $saved->event])
            @endforeach
        </div>

        <div class="mt-8">
            {{ $savedEvents->links() }}
        </div>
    @endif
</x-app-layout>
