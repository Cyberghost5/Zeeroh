@extends('layouts.public')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Browse Events</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $events->total() }} event{{ $events->total() !== 1 ? 's' : '' }} found</p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">

        {{-- ── Sidebar Filters ── --}}
        <aside class="lg:w-56 flex-shrink-0">
            <form method="GET" action="{{ route('events.index') }}" id="filter-form" class="space-y-6">

                <div>
                    <label class="form-label text-xs uppercase tracking-wide">Search</label>
                    <input type="search" name="q" value="{{ request('q') }}"
                           placeholder="Keywords…"
                           class="form-input text-sm mt-1"
                           onchange="document.getElementById('filter-form').submit()">
                </div>

                <div>
                    <label class="form-label text-xs uppercase tracking-wide">Category</label>
                    <div class="mt-2 space-y-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="category" value="" onchange="this.form.submit()"
                                   {{ !request('category') ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 border-gray-300">
                            <span class="text-sm text-gray-600">All</span>
                        </label>
                        @foreach($categories as $cat)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="category" value="{{ $cat->id }}" onchange="this.form.submit()"
                                       {{ request('category') == $cat->id ? 'checked' : '' }}
                                       class="h-4 w-4 text-primary-600 border-gray-300">
                                <span class="text-sm text-gray-600">{{ $cat->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="form-label text-xs uppercase tracking-wide">Event Type</label>
                    <div class="mt-2 space-y-1">
                        @foreach([''=>'All', 'physical'=>'Physical', 'virtual'=>'Virtual'] as $val => $label)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="type" value="{{ $val }}" onchange="this.form.submit()"
                                       {{ request('type', '') === $val ? 'checked' : '' }}
                                       class="h-4 w-4 text-primary-600 border-gray-300">
                                <span class="text-sm text-gray-600">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="form-label text-xs uppercase tracking-wide">Price</label>
                    <div class="mt-2 space-y-1">
                        @foreach([''=>'All', 'free'=>'Free', 'paid'=>'Paid'] as $val => $label)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="price" value="{{ $val }}" onchange="this.form.submit()"
                                       {{ request('price', '') === $val ? 'checked' : '' }}
                                       class="h-4 w-4 text-primary-600 border-gray-300">
                                <span class="text-sm text-gray-600">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                @if(request()->anyFilled(['q','category','type','price','city']))
                    <a href="{{ route('events.index') }}"
                       class="text-xs text-red-500 hover:text-red-700 font-medium">✕ Clear filters</a>
                @endif

            </form>
        </aside>

        {{-- ── Event Grid ── --}}
        <div class="flex-1">
            @if($events->isEmpty())
                <div class="text-center py-20">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-500">No events match your filters.</p>
                    <a href="{{ route('events.index') }}" class="text-sm text-primary-600 mt-2 inline-block">Clear all filters</a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($events as $event)
                        @include('partials.event-card', ['event' => $event])
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $events->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
