<x-admin-layout>
    <x-slot name="header">Events</x-slot>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row gap-4 mb-6">
        {{-- Search --}}
        <form method="GET" action="{{ route('admin.events.index') }}" class="flex gap-2 flex-1">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="search" name="search" value="{{ request('search') }}"
                       placeholder="Search events or organizers…"
                       class="form-input pl-9 text-sm">
            </div>
            <button type="submit" class="btn-primary text-sm px-4 py-2">Search</button>
            @if(request('search'))
                <a href="{{ route('admin.events.index', ['status' => request('status')]) }}" class="btn-secondary text-sm px-4 py-2">Clear</a>
            @endif
        </form>
    </div>

    {{-- Status tabs --}}
    <div class="flex gap-1 flex-wrap mb-6 bg-white/10 rounded-xl p-1 w-fit">
        @foreach(['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'draft' => 'Drafts'] as $key => $label)
            @php $count = $statusCounts[$key] ?? 0; @endphp
            <a href="{{ route('admin.events.index', array_filter(['status' => $key !== 'all' ? $key : null, 'search' => request('search')])) }}"
               class="px-4 py-1.5 rounded-lg text-sm font-medium transition-colors
                      {{ request('status', 'all') === $key
                            ? 'bg-white text-primary-700 shadow-sm'
                            : 'text-gray-300 hover:text-white' }}">
                {{ $label }}
                <span class="ml-1 text-xs opacity-70">{{ $count }}</span>
            </a>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="card overflow-hidden">
        @if($events->isEmpty())
            <div class="p-12 text-center">
                <p class="text-gray-500 text-sm">No events found.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-100 bg-gray-50">
                        <tr class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            <th class="text-left px-5 py-3">Event</th>
                            <th class="text-left px-4 py-3 hidden md:table-cell">Organizer</th>
                            <th class="text-left px-4 py-3 hidden lg:table-cell">Date</th>
                            <th class="text-right px-4 py-3 hidden sm:table-cell">Sold</th>
                            <th class="text-left px-4 py-3">Status</th>
                            <th class="text-right px-5 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($events as $event)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($event->banner)
                                            <img src="{{ Storage::url($event->banner) }}" alt="" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-900 leading-snug">{{ Str::limit($event->title, 45) }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $event->category?->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 hidden md:table-cell text-gray-600">
                                    {{ $event->organizer->name }}
                                </td>
                                <td class="px-4 py-4 hidden lg:table-cell text-gray-600">
                                    {{ $event->start_date->format('M j, Y') }}
                                </td>
                                <td class="px-4 py-4 hidden sm:table-cell text-right text-gray-700 font-medium">
                                    {{ number_format($event->total_tickets_sold) }}
                                </td>
                                <td class="px-4 py-4">
                                    <span class="badge-{{ $event->status }}">{{ ucfirst($event->status) }}</span>
                                    @if($event->is_featured)
                                        <span class="ml-1 badge-featured text-xs">Featured</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.events.show', $event) }}"
                                           class="text-primary-400 hover:text-primary-200 text-xs font-medium transition-colors">
                                            Review
                                        </a>
                                        @if($event->status === 'pending')
                                            <form method="POST" action="{{ route('admin.events.approve', $event) }}">
                                                @csrf
                                                <button type="submit"
                                                        class="text-green-400 hover:text-green-200 text-xs font-medium transition-colors">
                                                    Approve
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-4 border-t border-gray-100">
                {{ $events->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

</x-admin-layout>
