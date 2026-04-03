@props(['href', 'active' => false])

<a href="{{ $href }}"
   class="{{ $active
       ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium bg-primary-800 text-white'
       : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-primary-300 hover:bg-primary-800 hover:text-white transition-colors duration-150'
   }}">
    <span class="flex-shrink-0">{{ $icon }}</span>
    <span>{{ $slot }}</span>
</a>
