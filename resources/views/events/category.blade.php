@extends('layouts.public')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="mb-8">
        <nav class="text-xs text-gray-400 flex items-center gap-2 mb-4">
            <a href="{{ route('home') }}" class="hover:text-primary-600">Home</a>
            <span>/</span>
            <a href="{{ route('events.index') }}" class="hover:text-primary-600">Events</a>
            <span>/</span>
            <span class="text-gray-600">{{ $category->name }}</span>
        </nav>

        <div class="flex items-center gap-3">
            @if($category->icon)
                <span class="text-3xl">{{ $category->icon }}</span>
            @else
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: {{ $category->color }}20;">
                    <div class="w-4 h-4 rounded-full" style="background-color: {{ $category->color }};"></div>
                </div>
            @endif
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h1>
                <p class="text-sm text-gray-500">{{ $events->total() }} event{{ $events->total() !== 1 ? 's' : '' }}</p>
            </div>
        </div>
    </div>

    {{-- Category pills --}}
    <div class="flex flex-wrap gap-2 mb-8">
        <a href="{{ route('events.index') }}"
           class="text-xs px-3 py-1.5 rounded-full border border-gray-200 text-gray-500 hover:border-primary-400 hover:text-primary-600 transition-colors">
            All categories
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('events.category', $cat->slug) }}"
               class="text-xs px-3 py-1.5 rounded-full border transition-colors
                      {{ $cat->id === $category->id
                          ? 'bg-primary-600 text-white border-primary-600'
                          : 'border-gray-200 text-gray-500 hover:border-primary-400 hover:text-primary-600' }}">
                @if($cat->icon) {{ $cat->icon }}&nbsp; @endif{{ $cat->name }}
            </a>
        @endforeach
    </div>

    {{-- Events grid --}}
    @if($events->isEmpty())
        <div class="text-center py-24">
            <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-400">No upcoming events in this category.</p>
            <a href="{{ route('events.index') }}" class="text-sm text-primary-600 mt-2 inline-block">Browse all events</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($events as $event)
                @include('partials.event-card', ['event' => $event])
            @endforeach
        </div>

        <div class="mt-8">
            {{ $events->links() }}
        </div>
    @endif

</div>
@endsection
