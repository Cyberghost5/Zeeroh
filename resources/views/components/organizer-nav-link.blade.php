@props(['href', 'active' => false])

<a href="{{ $href }}"
   class="{{ $active
       ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium bg-primary-50 text-primary-700'
       : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors duration-150'
   }}">
    <span class="{{ $active ? 'text-primary-600' : 'text-gray-400' }} flex-shrink-0">{{ $icon }}</span>
    <span>{{ $slot }}</span>
</a>
