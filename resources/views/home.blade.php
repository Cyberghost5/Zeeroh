@extends('layouts.public')

@section('content')

    {{-- ── HERO ── --}}
    <section class="bg-gradient-to-br from-primary-950 via-primary-900 to-primary-800 text-white py-20 px-4">
        <div class="max-w-4xl mx-auto text-center"
             x-data="typewriter()"
             x-init="start()">
            <h1 class="text-4xl sm:text-5xl font-black leading-tight mb-4">
                Discover &amp; attend <br class="hidden sm:block">
                <span class="text-primary-300" x-text="displayed"></span><span class="inline-block w-0.5 h-10 bg-primary-300 align-middle ml-0.5 animate-pulse" x-show="!done"></span>
            </h1>
            <p class="text-lg text-primary-200 mb-8"
               x-show="done"
               x-transition:enter="transition ease-out duration-700"
               x-transition:enter-start="opacity-0 translate-y-3"
               x-transition:enter-end="opacity-100 translate-y-0"
               style="display:none;">
                Conferences, concerts, networking, and more — all across Nigeria.
            </p>

            {{-- Search bar --}}
            <form action="{{ route('events.index') }}" method="GET"
                  class="flex flex-col sm:flex-row gap-3 max-w-2xl mx-auto"
                  x-data="liveSearch()" @submit.prevent="submitSearch">

                <div class="flex-1 relative">
                    <input type="search" name="q" x-model="query"
                           @input.debounce.300ms="fetchSuggestions"
                           @keydown.escape="open = false"
                           @focus="query.length >= 2 && (open = true)"
                           placeholder="Search events…"
                           class="w-full px-5 py-3 rounded-xl text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">

                    {{-- Suggestions dropdown --}}
                    <div x-show="open && suggestions.length > 0"
                         x-transition
                         @click.away="open = false"
                         class="absolute top-full left-0 right-0 mt-1 bg-white rounded-xl shadow-lg border border-gray-100 z-50 overflow-hidden">
                        <template x-for="item in suggestions" :key="item.slug">
                            <a :href="item.url"
                               class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 text-left border-b border-gray-50 last:border-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate" x-text="item.title"></p>
                                    <p class="text-xs text-gray-400" x-text="item.date + (item.city ? ' · ' + item.city : '')"></p>
                                </div>
                                <svg class="w-3.5 h-3.5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </template>
                    </div>
                </div>

                <select name="category"
                        class="px-4 py-3 rounded-xl text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white">
                    <option value="">All categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <button type="submit"
                        class="bg-primary-400 hover:bg-primary-300 text-primary-950 font-bold px-6 py-3 rounded-xl transition-colors">
                    Search
                </button>
            </form>
        </div>
    </section>

    {{-- ── FEATURED ── --}}
    @if($featured->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Featured Events</h2>
            <a href="{{ route('events.index') }}" class="text-sm text-primary-600 font-medium hover:text-primary-700">View all →</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featured as $event)
                @include('partials.event-card', ['event' => $event])
            @endforeach
        </div>
    </section>
    @endif

    {{-- ── UPCOMING ── --}}
    @if($upcoming->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Upcoming Events</h2>
            <a href="{{ route('events.index') }}" class="text-sm text-primary-600 font-medium hover:text-primary-700">View all →</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($upcoming as $event)
                @include('partials.event-card', ['event' => $event, 'compact' => true])
            @endforeach
        </div>
    </section>
    @endif

    {{-- Empty state --}}
    @if($featured->isEmpty() && $upcoming->isEmpty())
    <section class="max-w-7xl mx-auto px-4 py-24 text-center">
        <p class="text-gray-400 text-lg">No events available yet. Check back soon!</p>
        @auth
            @if(auth()->user()->isOrganizer())
                <a href="{{ route('organizer.events.create') }}" class="btn-primary mt-4 inline-block">Create an Event</a>
            @endif
        @endauth
    </section>
    @endif

@endsection

@push('scripts')
<script>
function liveSearch() {
    return {
        query: '',
        suggestions: [],
        open: false,
        async fetchSuggestions() {
            if (this.query.length < 2) { this.suggestions = []; this.open = false; return; }
            try {
                const res = await fetch(`{{ route('search.suggestions') }}?q=${encodeURIComponent(this.query)}`);
                this.suggestions = await res.json();
                this.open = this.suggestions.length > 0;
            } catch { this.suggestions = []; }
        },
        submitSearch() {
            this.open = false;
            this.$el.submit();
        }
    };
}

function typewriter() {
    const phrases = [
        'amazing events near you',
        'unforgettable experiences',
        'concerts & live shows',
        'networking opportunities',
        'conferences & summits',
    ];
    return {
        displayed: '',
        done: false,
        _phrase: 0,
        _charIndex: 0,
        _deleting: false,
        _timer: null,
        start() {
            this._tick();
        },
        _tick() {
            const phrase = phrases[this._phrase];
            if (!this._deleting) {
                this.displayed = phrase.slice(0, ++this._charIndex);
                if (this._charIndex === phrase.length) {
                    // Finished typing first phrase — show subtitle, keep looping
                    if (!this.done) this.done = true;
                    this._timer = setTimeout(() => {
                        this._deleting = true;
                        this._tick();
                    }, 2200);
                    return;
                }
            } else {
                this.displayed = phrase.slice(0, --this._charIndex);
                if (this._charIndex === 0) {
                    this._deleting = false;
                    this._phrase = (this._phrase + 1) % phrases.length;
                    this._timer = setTimeout(() => this._tick(), 400);
                    return;
                }
            }
            const speed = this._deleting ? 40 : 75;
            this._timer = setTimeout(() => this._tick(), speed);
        }
    };
}
</script>
@endpush
