@php $compact = $compact ?? false; @endphp

<a href="{{ route('events.show', $event->slug) }}"
   class="card overflow-hidden flex flex-col hover:shadow-lg transition-shadow group">

    {{-- Banner --}}
    <div class="{{ $compact ? 'h-36' : 'h-48' }} overflow-hidden bg-gradient-to-br from-primary-100 to-primary-200 flex-shrink-0">
        @if($event->banner)
            <img src="{{ Storage::url($event->banner) }}" alt="{{ $event->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-10 h-10 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        @endif
    </div>

    <div class="p-4 flex flex-col flex-1">
        {{-- Category + featured tag --}}
        <div class="flex items-center gap-2 mb-2">
            @if($event->category)
                <span class="text-xs text-primary-600 font-medium bg-primary-50 px-2 py-0.5 rounded-full">
                    {{ $event->category->name }}
                </span>
            @endif
            @if($event->is_featured)
                <span class="text-xs text-yellow-700 bg-yellow-100 px-2 py-0.5 rounded-full font-medium">★ Featured</span>
            @endif
        </div>

        <h3 class="font-semibold text-gray-900 text-sm leading-snug line-clamp-2 mb-2 group-hover:text-primary-700 transition-colors">
            {{ $event->title }}
        </h3>

        <div class="mt-auto space-y-1.5">
            <p class="text-xs text-gray-500 flex items-center gap-1">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ $event->start_date->format('D, M j, Y') }}
            </p>
            <p class="text-xs text-gray-500 flex items-center gap-1">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ $event->is_virtual ? 'Online' : ($event->city ? $event->city . ', ' . $event->state : 'Nigeria') }}
            </p>
        </div>

        {{-- Price --}}
        <div class="mt-3 pt-3 border-t border-gray-100">
            @php $minPrice = $event->min_price; @endphp
            <span class="text-sm font-bold {{ $minPrice > 0 ? 'text-primary-700' : 'text-green-600' }}">
                {{ $minPrice > 0 ? 'From ₦' . number_format($minPrice) : 'Free' }}
            </span>
        </div>
    </div>
</a>
