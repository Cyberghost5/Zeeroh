<x-admin-layout>
@section('page-title', 'Organizers')
@section('page-subtitle', 'Manage organizer accounts')

{{-- Filters --}}
<form method="GET" action="{{ route('admin.organizers.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
    <input type="search" name="q" value="{{ request('q') }}"
           placeholder="Search by name or email…"
           class="form-input flex-1 text-sm">

    <select name="status" onchange="this.form.submit()"
            class="form-input text-sm">
        <option value="">All statuses</option>
        <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Active</option>
        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
    </select>

    <button type="submit" class="btn-primary text-sm px-5 py-2">
        Search
    </button>
    @if(request()->anyFilled(['q','status']))
        <a href="{{ route('admin.organizers.index') }}"
           class="btn-secondary text-sm px-4 py-2 self-center text-center">Clear</a>
    @endif
</form>

{{-- Table --}}
<div class="card overflow-hidden">
    @if($organizers->isEmpty())
        <div class="text-center py-16 text-gray-500 text-sm">No organizers found.</div>
    @else
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-100 bg-gray-50">
                <tr class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    <th class="text-left px-5 py-3">Organizer</th>
                    <th class="text-left px-5 py-3 hidden md:table-cell">Organisation</th>
                    <th class="text-center px-5 py-3 hidden lg:table-cell">Events</th>
                    <th class="text-center px-5 py-3">Status</th>
                    <th class="text-left px-5 py-3">Joined</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($organizers as $organizer)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-xs font-bold text-primary-700 flex-shrink-0">
                                {{ strtoupper(substr($organizer->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-gray-900 truncate">{{ $organizer->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $organizer->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 hidden md:table-cell">
                        <span class="text-gray-600">
                            {{ $organizer->organizerProfile?->organization_name ?? '—' }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-center hidden lg:table-cell">
                        <span class="text-gray-600">{{ $organizer->events_count }}</span>
                    </td>
                    <td class="px-5 py-4 text-center">
                        @if($organizer->is_active)
                            <span class="inline-block text-xs bg-green-100 text-green-700 border border-green-200 rounded-full px-2.5 py-0.5">Active</span>
                        @else
                            <span class="inline-block text-xs bg-red-100 text-red-700 border border-red-200 rounded-full px-2.5 py-0.5">Suspended</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-gray-500 text-xs">{{ $organizer->created_at->format('d M Y') }}</td>
                    <td class="px-5 py-4">
                        <a href="{{ route('admin.organizers.show', $organizer) }}"
                           class="text-xs text-primary-600 hover:text-primary-500 font-medium">View →</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>

        <div class="px-5 py-4 border-t border-gray-100">
            {{ $organizers->links() }}
        </div>
    @endif
</div>
</x-admin-layout>
