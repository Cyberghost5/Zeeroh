<x-admin-layout>
@section('page-title', $organizer->name)
@section('page-subtitle', $organizer->email)

@section('header-actions')
    {{-- Impersonate --}}
    <form method="POST" action="{{ route('admin.organizers.impersonate', $organizer) }}"
          onsubmit="return confirm('Log in as {{ addslashes($organizer->name) }}? You will be taken to their organizer dashboard.')">
        @csrf
        <button type="submit"
                class="text-sm bg-primary-600 hover:bg-primary-700 text-white font-medium px-4 py-2 rounded-xl transition-colors">
            Login as Organizer
        </button>
    </form>

    @if($organizer->is_active)
        <form method="POST" action="{{ route('admin.organizers.suspend', $organizer) }}">
            @csrf
            <button type="submit"
                    onclick="return confirm('Suspend {{ addslashes($organizer->name) }}? They will lose access to the organizer dashboard.')"
                    class="text-sm bg-red-600 hover:bg-red-500 text-white font-medium px-4 py-2 rounded-xl transition-colors">
                Suspend Organizer
            </button>
        </form>
    @else
        <form method="POST" action="{{ route('admin.organizers.reactivate', $organizer) }}">
            @csrf
            <button type="submit"
                    class="text-sm bg-green-600 hover:bg-green-500 text-white font-medium px-4 py-2 rounded-xl transition-colors">
                Reactivate Organizer
            </button>
        </form>
    @endif
@endsection

<div class="space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Total Events',    'value' => $stats['total_events']],
            ['label' => 'Approved Events', 'value' => $stats['approved_events']],
            ['label' => 'Tickets Sold',    'value' => number_format($stats['total_tickets'])],
            ['label' => 'Gross Revenue',   'value' => '₦' . number_format($stats['total_revenue'])],
        ] as $stat)
        <div class="card p-5">
            <p class="text-2xl font-black text-gray-900">{{ $stat['value'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Profile --}}
        <div class="card p-6">
            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">Profile</h2>

            {{-- Logo --}}
            <div class="flex items-center gap-4 mb-5">
                <div class="w-16 h-16 rounded-xl overflow-hidden bg-primary-100 flex items-center justify-center text-2xl font-black text-primary-600 flex-shrink-0">
                    @if($organizer->organizerProfile?->logo)
                        <img src="{{ Storage::url($organizer->organizerProfile->logo) }}" class="w-full h-full object-cover" alt="Logo">
                    @else
                        {{ strtoupper(substr($organizer->name, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <p class="text-gray-900 font-semibold">{{ $organizer->organizerProfile?->organization_name ?? 'No profile set' }}</p>
                    <div class="flex items-center gap-1.5 mt-1">
                        @if($organizer->organizerProfile?->is_verified)
                            <span class="text-xs text-blue-400">✓ Verified</span>
                        @else
                            <span class="text-xs text-gray-500">Not verified</span>
                        @endif
                        &middot;
                        @if($organizer->is_active)
                            <span class="text-xs text-green-400">Active</span>
                        @else
                            <span class="text-xs text-red-400">Suspended</span>
                        @endif
                    </div>
                </div>
            </div>

            <dl class="space-y-3 text-sm">
                @if($organizer->organizerProfile?->bio)
                <div>
                    <dt class="text-xs text-gray-500 mb-1">Bio</dt>
                    <dd class="text-gray-600 text-xs leading-relaxed">{{ $organizer->organizerProfile->bio }}</dd>
                </div>
                @endif
                @if($organizer->organizerProfile?->slug)
                <div>
                    <dt class="text-xs text-gray-500 mb-1">Public page</dt>
                    <dd>
                        <a href="{{ route('organizers.show', $organizer->organizerProfile->slug) }}" target="_blank"
                           class="text-xs text-primary-600 hover:text-primary-500 underline">
                            /organizers/{{ $organizer->organizerProfile->slug }}
                        </a>
                    </dd>
                </div>
                @endif
                <div>
                    <dt class="text-xs text-gray-500 mb-1">Member since</dt>
                    <dd class="text-gray-700 text-xs">{{ $organizer->created_at->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 mb-1">Phone</dt>
                    <dd class="text-gray-700 text-xs">{{ $organizer->phone ?? '—' }}</dd>
                </div>
            </dl>

            {{-- Bank details --}}
            @if($organizer->organizerProfile?->bank_name)
            <div class="mt-5 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Payout Details</p>
                <dl class="space-y-1 text-xs">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Bank</dt>
                        <dd class="text-gray-700">{{ $organizer->organizerProfile->bank_name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Account</dt>
                        <dd class="text-gray-700 font-mono">{{ $organizer->organizerProfile->account_number }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Name</dt>
                        <dd class="text-gray-700">{{ $organizer->organizerProfile->account_name }}</dd>
                    </div>
                </dl>
            </div>
            @endif
        </div>

        {{-- Events list --}}
        <div class="lg:col-span-2 card overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Events</h2>
            </div>

            @if($events->isEmpty())
                <div class="text-center py-12 text-gray-500 text-sm">No events yet.</div>
            @else
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-100 bg-gray-50">
                        <tr class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            <th class="text-left px-5 py-2">Title</th>
                            <th class="text-center px-5 py-2 hidden sm:table-cell">Tickets</th>
                            <th class="text-center px-5 py-2">Status</th>
                            <th class="text-left px-5 py-2 hidden md:table-cell">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($events as $event)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3">
                                <a href="{{ route('admin.events.show', $event) }}" class="text-gray-900 hover:text-primary-600 font-medium">
                                    {{ Str::limit($event->title, 40) }}
                                </a>
                            </td>
                            <td class="px-5 py-3 text-center text-gray-500 text-xs hidden sm:table-cell">
                                {{ $event->tickets_count }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                @php
                                $statusColors = ['pending'=>'text-yellow-600','approved'=>'text-green-600','rejected'=>'text-red-600'];
                                @endphp
                                <span class="text-xs {{ $statusColors[$event->status] ?? 'text-gray-500' }} capitalize">
                                    {{ $event->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-500 text-xs hidden md:table-cell">
                                {{ $event->start_date->format('d M Y') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $events->links() }}
                </div>
            @endif
        </div>

        {{-- Danger zone --}}
        <div class="card p-5 border border-red-200">
            <h3 class="text-xs font-semibold text-red-500 uppercase tracking-wide mb-3">Danger Zone</h3>
            <p class="text-xs text-gray-500 mb-4">Permanently deletes this organizer account along with all their events, tickets, orders, and sales data. This cannot be undone.</p>
            <form method="POST" action="{{ route('admin.organizers.destroy', $organizer) }}"
                  x-data="{}"
                  @submit.prevent="if(confirm('Delete {{ addslashes($organizer->name) }} and ALL their data permanently? This cannot be undone.')) $el.submit()">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-full py-2.5 px-4 rounded-xl bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition-colors">
                    Delete Organizer Permanently
                </button>
            </form>
        </div>

    </div>

</div>
</x-admin-layout>