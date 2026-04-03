@extends('layouts.public')

@section('title', $event->title . ' — Zeeroh')

@section('seo')
{{-- Canonical --}}
<link rel="canonical" href="{{ route('events.show', $event->slug) }}">

{{-- Meta description --}}
<meta name="description" content="{{ Str::limit(strip_tags($event->description), 155) }}">

{{-- Open Graph --}}
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $event->title }}">
<meta property="og:description" content="{{ Str::limit(strip_tags($event->description), 155) }}">
<meta property="og:url" content="{{ route('events.show', $event->slug) }}">
@if($event->banner)
<meta property="og:image" content="{{ Storage::url($event->banner) }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
@endif
<meta property="og:site_name" content="Zeeroh">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $event->title }}">
<meta name="twitter:description" content="{{ Str::limit(strip_tags($event->description), 155) }}">
@if($event->banner)
<meta name="twitter:image" content="{{ Storage::url($event->banner) }}">
@endif

{{-- JSON-LD --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Event",
    "name": "{{ addslashes($event->title) }}",
    "startDate": "{{ $event->start_date->toIso8601String() }}",
    @if($event->end_date)
    "endDate": "{{ $event->end_date->toIso8601String() }}",
    @endif
    "description": "{{ addslashes(Str::limit(strip_tags($event->description), 300)) }}",
    "url": "{{ route('events.show', $event->slug) }}",
    @if($event->banner)
    "image": "{{ Storage::url($event->banner) }}",
    @endif
    "eventStatus": "https://schema.org/EventScheduled",
    "eventAttendanceMode": "{{ $event->is_virtual ? 'https://schema.org/OnlineEventAttendanceMode' : 'https://schema.org/OfflineEventAttendanceMode' }}",
    @if(!$event->is_virtual && $event->venue_name)
    "location": {
        "@type": "Place",
        "name": "{{ addslashes($event->venue_name) }}",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "{{ addslashes($event->city ?? '') }}",
            "addressRegion": "{{ addslashes($event->state ?? '') }}",
            "addressCountry": "NG"
        }
    },
    @elseif($event->is_virtual)
    "location": {
        "@type": "VirtualLocation",
        "url": "{{ route('events.show', $event->slug) }}"
    },
    @endif
    "organizer": {
        "@type": "Organization",
        "name": "{{ addslashes($event->organizer->organizerProfile?->organization_name ?? $event->organizer->name) }}"
    },
    "offers": [
        @foreach($event->ticketTypes as $i => $type)
        {
            "@type": "Offer",
            "name": "{{ addslashes($type->name) }}",
            "price": "{{ $type->price }}",
            "priceCurrency": "NGN",
            "availability": "{{ $type->isAvailable() ? 'https://schema.org/InStock' : 'https://schema.org/SoldOut' }}",
            "url": "{{ route('events.show', $event->slug) }}"
        }{{ $i < $event->ticketTypes->count() - 1 ? ',' : '' }}
        @endforeach
    ]
}
</script>
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="text-xs text-gray-400 mb-6 flex items-center gap-2">
        <a href="{{ route('home') }}" class="hover:text-primary-600">Home</a>
        <span>/</span>
        <a href="{{ route('events.index') }}" class="hover:text-primary-600">Events</a>
        <span>/</span>
        <span class="text-gray-600 truncate max-w-xs">{{ $event->title }}</span>
    </nav>

    {{-- Flash --}}
    @if(session('error'))
        <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-xl px-4 py-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9v4a1 1 0 102 0V9a1 1 0 10-2 0zm0-4a1 1 0 112 0 1 1 0 01-2 0z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="ticketSelector()">

        {{-- ── Main content ── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Banner --}}
            @if($event->banner)
                <img src="{{ Storage::url($event->banner) }}" alt="{{ $event->title }}"
                     class="w-full h-72 object-cover rounded-2xl">
            @else
                <div class="w-full h-72 rounded-2xl bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center">
                    <svg class="w-16 h-16 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            @endif

            {{-- Title & meta --}}
            <div>
                <div class="flex items-start gap-3 flex-wrap mb-3">
                    @if($event->category)
                        <span class="text-xs text-primary-600 bg-primary-50 font-medium px-3 py-1 rounded-full">{{ $event->category->name }}</span>
                    @endif
                    @if($event->is_featured)
                        <span class="text-xs text-yellow-700 bg-yellow-100 font-medium px-3 py-1 rounded-full">★ Featured</span>
                    @endif
                </div>
                <h1 class="text-3xl font-black text-gray-900 leading-tight">{{ $event->title }}</h1>

                {{-- Actions: share + save --}}
                <div class="flex items-center gap-2 mt-4 flex-wrap" x-data="shareActions()">
                    {{-- Save / Wishlist --}}
                    @auth
                    <form method="POST" action="{{ route('wishlist.toggle', $event) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 text-xs px-3 py-2 rounded-lg border transition-colors
                                       {{ $isSaved ? 'bg-red-50 border-red-200 text-red-600 hover:bg-red-100' : 'bg-white border-gray-200 text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                            <svg class="w-3.5 h-3.5" fill="{{ $isSaved ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            {{ $isSaved ? 'Saved' : 'Save' }}
                        </button>
                    </form>
                    @endauth

                    {{-- Copy link --}}
                    <button @click="copyLink()" type="button"
                            class="inline-flex items-center gap-1.5 text-xs px-3 py-2 rounded-lg border border-gray-200 bg-white text-gray-500 hover:border-gray-300 hover:text-gray-700 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span x-text="copied ? 'Copied!' : 'Copy link'">Copy link</span>
                    </button>

                    {{-- WhatsApp --}}
                    <a :href="whatsappUrl" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-1.5 text-xs px-3 py-2 rounded-lg border border-gray-200 bg-white text-gray-500 hover:border-green-300 hover:text-green-600 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        WhatsApp
                    </a>

                    {{-- Twitter --}}
                    <a :href="twitterUrl" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-1.5 text-xs px-3 py-2 rounded-lg border border-gray-200 bg-white text-gray-500 hover:border-sky-300 hover:text-sky-500 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                        Tweet
                    </a>
                </div>
            </div>

            {{-- Event details --}}
            <div class="card p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Event Details</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex gap-3">
                        <dt class="w-6 flex-shrink-0 text-gray-400 mt-0.5">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </dt>
                        <dd class="text-gray-900">
                            <span class="font-medium">{{ $event->start_date->format('l, F j, Y') }}</span>
                            at {{ $event->start_time }}
                            @if($event->end_date)
                                – {{ $event->end_date->format('F j, Y') }} at {{ $event->end_time }}
                            @endif
                        </dd>
                    </div>
                    <div class="flex gap-3">
                        <dt class="w-6 flex-shrink-0 text-gray-400 mt-0.5">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </dt>
                        <dd class="text-gray-900">
                            @if($event->is_virtual)
                                <span class="font-medium">Online / Virtual</span>
                            @else
                                <span class="font-medium">{{ $event->venue_name }}</span>
                                @if($event->venue_address), {{ $event->venue_address }}@endif
                                @if($event->city), {{ $event->city }}@endif
                                @if($event->state), {{ $event->state }}@endif
                            @endif
                        </dd>
                    </div>
                    <div class="flex gap-3">
                        <dt class="w-6 flex-shrink-0 text-gray-400 mt-0.5">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </dt>
                        <dd class="text-gray-900">Hosted by <span class="font-medium">{{ $event->organizer->name }}</span></dd>
                    </div>
                </dl>
            </div>

            {{-- Description --}}
            <div class="card p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">About This Event</h2>
                <div class="prose prose-sm max-w-none text-gray-700 whitespace-pre-line leading-relaxed">
                    {{ $event->description }}
                </div>
            </div>

        </div>

        {{-- ── Ticket Sidebar ── --}}
        <div class="space-y-4">
            <div class="card p-5 sticky top-20">
                <h2 class="text-base font-bold text-gray-900 mb-4">Get Tickets</h2>

                @if($event->ticketTypes->isEmpty())
                    <p class="text-sm text-gray-400">No tickets available.</p>
                @else
                    <div class="space-y-3 mb-5">
                        @foreach($event->ticketTypes as $type)
                            @php $available = $type->available_quantity; @endphp
                            <div class="border border-gray-200 rounded-xl p-4 {{ $available <= 0 ? 'opacity-50' : '' }}">
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">{{ $type->name }}</p>
                                        @if($type->description)
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $type->description }}</p>
                                        @endif
                                    </div>
                                    <span class="font-bold text-primary-700 text-sm flex-shrink-0">
                                        {{ $type->price > 0 ? '₦' . number_format($type->price) : 'Free' }}
                                    </span>
                                </div>

                                @if($available <= 0)
                                    <p class="text-xs text-red-500 font-medium mt-2">Sold out</p>
                                    @auth
                                        @php $onWaitlist = in_array($type->id, $userWaitlistIds); @endphp
                                        <form method="POST"
                                              action="{{ $onWaitlist ? route('waitlist.leave') : route('waitlist.join') }}"
                                              class="mt-2">
                                            @csrf
                                            <input type="hidden" name="ticket_type_id" value="{{ $type->id }}">
                                            <button type="submit"
                                                    class="w-full text-xs py-2 rounded-lg border transition-colors
                                                           {{ $onWaitlist
                                                              ? 'border-orange-300 text-orange-600 bg-orange-50 hover:bg-orange-100'
                                                              : 'border-gray-300 text-gray-600 bg-white hover:border-primary-400 hover:text-primary-600' }}">
                                                {{ $onWaitlist ? '✓ On Waitlist — Leave' : 'Join Waitlist' }}
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}" class="block text-center text-xs text-primary-600 mt-2 hover:underline">
                                            Log in to join waitlist
                                        </a>
                                    @endauth
                                @else
                                    <div class="flex items-center gap-2 mt-3">
                                        <button type="button"
                                                @click="decrement({{ $type->id }})"
                                                class="w-7 h-7 rounded-lg border border-gray-300 flex items-center justify-center text-gray-600 hover:border-primary-400 hover:bg-primary-50 transition-colors text-lg leading-none">
                                            −
                                        </button>
                                        <span class="w-8 text-center font-semibold text-gray-900 text-sm"
                                              x-text="quantities[{{ $type->id }}] || 0"></span>
                                        <button type="button"
                                                @click="increment({{ $type->id }}, {{ $available }}, {{ $type->max_per_order ?: $available }})"
                                                class="w-7 h-7 rounded-lg border border-gray-300 flex items-center justify-center text-gray-600 hover:border-primary-400 hover:bg-primary-50 transition-colors text-lg leading-none">
                                            +
                                        </button>
                                        <span class="text-xs text-gray-400 ml-1">{{ number_format($available) }} left</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Subtotal preview --}}
                    <div class="border-t border-gray-100 pt-3 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Estimated total</span>
                            <span class="font-bold text-gray-900" x-text="'₦' + subtotal.toLocaleString()"></span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">+ service fee at checkout</p>
                    </div>

                    @auth
                        {{-- Build checkout URL with qty params --}}
                        <button type="button" @click="goToCheckout()" x-bind:disabled="totalTickets === 0"
                                class="w-full btn-primary py-3 disabled:opacity-50 disabled:cursor-not-allowed">
                            Get Tickets
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="w-full btn-primary py-3 block text-center">
                            Log in to get tickets
                        </a>
                        <p class="text-xs text-center text-gray-400 mt-2">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="text-primary-600 hover:underline">Register free</a>
                        </p>
                    @endauth
                @endif
            </div>
        </div>

    </div>
</div>

{{-- ── Reviews ── --}}
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <div class="border-t border-gray-200 pt-10">

        {{-- Rating summary --}}
        <div class="flex items-center gap-4 mb-8">
            <div class="text-center">
                <p class="text-5xl font-black text-gray-900">{{ number_format($event->reviews_avg_rating ?? 0, 1) }}</p>
                <div class="flex items-center justify-center gap-0.5 mt-1">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= round($event->reviews_avg_rating ?? 0) ? 'text-yellow-400' : 'text-gray-200' }}"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <p class="text-xs text-gray-400 mt-1">{{ $event->reviews_count }} {{ Str::plural('review', $event->reviews_count) }}</p>
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-bold text-gray-900">Reviews</h2>
                <p class="text-sm text-gray-500 mt-0.5">From verified attendees</p>
            </div>
        </div>

        {{-- Leave a review --}}
        @if($canReview)
        <div class="bg-primary-50 border border-primary-200 rounded-2xl p-6 mb-8" x-data="{ rating: 0, hover: 0 }">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Leave a Review</h3>
            <form method="POST" action="{{ route('reviews.store', $event) }}">
                @csrf
                {{-- Star picker --}}
                <div class="flex items-center gap-1 mb-4">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button"
                                @click="rating = {{ $i }}"
                                @mouseenter="hover = {{ $i }}"
                                @mouseleave="hover = 0"
                                class="w-8 h-8 transition-colors">
                            <svg :class="(hover || rating) >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'"
                                 class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </button>
                    @endfor
                    <input type="hidden" name="rating" :value="rating">
                    <span class="text-sm text-gray-500 ml-2" x-text="rating ? rating + ' star' + (rating > 1 ? 's' : '') : 'Select rating'"></span>
                </div>
                @error('rating')<p class="text-xs text-red-500 mb-3">{{ $message }}</p>@enderror
                <textarea name="comment" rows="3" placeholder="Share your experience (optional)…"
                          class="w-full rounded-xl border-primary-200 text-sm focus:ring-primary-400 focus:border-primary-400 bg-white">{{ old('comment') }}</textarea>
                @error('comment')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                <button type="submit" x-bind:disabled="rating === 0"
                        class="mt-3 btn-primary text-sm py-2 px-5 disabled:opacity-50 disabled:cursor-not-allowed">
                    Submit Review
                </button>
            </form>
        </div>
        @elseif($hasReviewed)
        <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm text-green-700 mb-8 flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            You've already reviewed this event.
        </div>
        @endif

        {{-- Review list --}}
        @if($reviews->isEmpty())
            <p class="text-sm text-gray-400 text-center py-8">No reviews yet. Be the first to review after attending!</p>
        @else
            <div class="space-y-4">
                @foreach($reviews as $review)
                <div class="bg-white border border-gray-200 rounded-2xl p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center text-xs font-bold text-primary-700 flex-shrink-0">
                                {{ strtoupper(substr($review->user->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $review->user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-0.5 flex-shrink-0">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                    </div>
                    @if($review->comment)
                        <p class="text-sm text-gray-600 mt-3 leading-relaxed">{{ $review->comment }}</p>
                    @endif
                    @if(auth()->check() && (auth()->id() === $review->user_id || auth()->user()->hasRole('admin')))
                        <form method="POST" action="{{ route('reviews.destroy', $review) }}" class="mt-3">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this review?')"
                                    class="text-xs text-red-400 hover:text-red-600">Delete</button>
                        </form>
                    @endif
                </div>
                @endforeach
            </div>

            @if($reviews->hasPages())
                <div class="mt-6">{{ $reviews->links() }}</div>
            @endif
        @endif

    </div>
</div>

@push('scripts')
<script>
    function ticketSelector() {        const prices = @json($event->ticketTypes->pluck('price', 'id'));

        return {
            quantities: {},
            get subtotal() {
                return Object.entries(this.quantities).reduce((sum, [id, qty]) => {
                    return sum + (prices[id] || 0) * qty;
                }, 0);
            },
            get totalTickets() {
                return Object.values(this.quantities).reduce((s, q) => s + q, 0);
            },
            increment(id, available, maxPerOrder) {
                const current = this.quantities[id] || 0;
                if (current < available && current < maxPerOrder) {
                    this.quantities[id] = current + 1;
                }
            },
            decrement(id) {
                const current = this.quantities[id] || 0;
                if (current > 0) this.quantities[id] = current - 1;
            },
            goToCheckout() {
                const params = new URLSearchParams();
                Object.entries(this.quantities).forEach(([id, qty]) => {
                    if (qty > 0) params.append(`qty[${id}]`, qty);
                });
                window.location.href = `{{ route('checkout.show', $event->slug) }}?${params.toString()}`;
            }
        };
    }

    function shareActions() {
        const url   = encodeURIComponent(window.location.href);
        const title = encodeURIComponent('{{ addslashes($event->title) }}');
        return {
            copied: false,
            whatsappUrl: `https://wa.me/?text=${title}%20${url}`,
            twitterUrl:  `https://twitter.com/intent/tweet?text=${title}&url=${url}`,
            async copyLink() {
                try {
                    await navigator.clipboard.writeText(window.location.href);
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2500);
                } catch {
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2500);
                }
            }
        };
    }
</script>
@endpush

@endsection
