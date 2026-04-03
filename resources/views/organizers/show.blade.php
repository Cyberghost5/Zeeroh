@extends('layouts.public')

@section('title', ($organizer->organization_name ?? $organizer->user->name) . ' — Zeeroh')

@section('seo')
<link rel="canonical" href="{{ route('organizers.show', $organizer->slug) }}">
<meta name="description" content="{{ Str::limit(strip_tags($organizer->bio ?? ''), 155) }}">
<meta property="og:title" content="{{ $organizer->organization_name ?? $organizer->user->name }}">
<meta property="og:description" content="{{ Str::limit(strip_tags($organizer->bio ?? ''), 155) }}">
<meta property="og:url" content="{{ route('organizers.show', $organizer->slug) }}">
@if($organizer->logo)
<meta property="og:image" content="{{ Storage::url($organizer->logo) }}">
@endif
<meta property="og:site_name" content="Zeeroh">
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Organizer header --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-8 mb-8">
        <div class="flex flex-col sm:flex-row gap-6 items-start sm:items-center">

            {{-- Logo --}}
            <div class="w-24 h-24 rounded-2xl overflow-hidden bg-primary-50 border border-primary-100 flex-shrink-0 flex items-center justify-center">
                @if($profile->logo)
                    <img src="{{ Storage::url($profile->logo) }}" alt="{{ $profile->organization_name }}" class="w-full h-full object-cover">
                @else
                    <span class="text-3xl font-black text-primary-600">
                        {{ strtoupper(substr($profile->organization_name, 0, 1)) }}
                    </span>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1">
                <div class="flex items-center gap-2 flex-wrap mb-1">
                    <h1 class="text-2xl font-black text-gray-900">{{ $profile->organization_name }}</h1>
                    @if($profile->is_verified)
                        <span class="inline-flex items-center gap-1 text-xs bg-blue-50 text-blue-700 border border-blue-200 font-medium px-2.5 py-0.5 rounded-full">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Verified
                        </span>
                    @endif
                </div>

                @if($profile->bio)
                    <p class="text-sm text-gray-500 max-w-xl leading-relaxed">{{ $profile->bio }}</p>
                @endif

                {{-- Social links --}}
                <div class="flex items-center gap-3 mt-3">
                    @if($profile->website)
                        <a href="{{ $profile->website }}" target="_blank" rel="noopener noreferrer"
                           class="text-gray-400 hover:text-primary-600 transition-colors" title="Website">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
                            </svg>
                        </a>
                    @endif
                    @if($profile->facebook)
                        <a href="{{ $profile->facebook }}" target="_blank" rel="noopener noreferrer"
                           class="text-gray-400 hover:text-blue-600 transition-colors" title="Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
                            </svg>
                        </a>
                    @endif
                    @if($profile->twitter)
                        <a href="{{ $profile->twitter }}" target="_blank" rel="noopener noreferrer"
                           class="text-gray-400 hover:text-sky-500 transition-colors" title="Twitter / X">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                    @endif
                    @if($profile->instagram)
                        <a href="{{ $profile->instagram }}" target="_blank" rel="noopener noreferrer"
                           class="text-gray-400 hover:text-pink-500 transition-colors" title="Instagram">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="currentColor" stroke-width="2"/>
                                <circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/>
                                <circle cx="17.5" cy="6.5" r="1" fill="currentColor"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Stats --}}
            <div class="flex gap-6 text-center flex-shrink-0">
                <div>
                    <p class="text-2xl font-black text-gray-900">{{ $events->total() }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Events</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Events grid --}}
    <div>
        <h2 class="text-lg font-bold text-gray-900 mb-5">Upcoming Events</h2>

        @if($events->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($events as $event)
                    @include('partials.event-card', ['event' => $event])
                @endforeach
            </div>

            <div class="mt-8">
                {{ $events->links() }}
            </div>
        @else
            <div class="text-center py-16 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm">No upcoming events from this organizer.</p>
            </div>
        @endif
    </div>

</div>
@endsection
