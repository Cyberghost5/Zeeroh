<x-admin-layout>
    <x-slot name="header">Review Event</x-slot>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Back + Status bar --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.events.index') }}" class="btn-secondary text-sm py-2">← Back to Events</a>
        <div class="flex items-center gap-3">
            <span class="badge-{{ $event->status }} text-sm">{{ ucfirst($event->status) }}</span>
            @if($event->is_featured)
                <span class="badge-featured text-sm">Featured</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main content --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Banner --}}
            @if($event->banner)
                <div class="card overflow-hidden">
                    <img src="{{ Storage::url($event->banner) }}" alt="{{ $event->title }}" class="w-full h-64 object-cover">
                </div>
            @endif

            {{-- Event info --}}
            <div class="card p-6">
                <div class="flex items-start gap-3 mb-5">
                    <div class="flex-1">
                        <h1 class="text-xl font-bold text-gray-900">{{ $event->title }}</h1>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $event->category?->name }}</p>
                    </div>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm mb-5">
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Start</dt>
                        <dd class="text-gray-900 font-medium">{{ $event->start_date->format('D, M j, Y') }} at {{ $event->start_time }}</dd>
                    </div>
                    @if($event->end_date)
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">End</dt>
                        <dd class="text-gray-900 font-medium">{{ $event->end_date->format('D, M j, Y') }} at {{ $event->end_time }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Type</dt>
                        <dd class="text-gray-900 font-medium">{{ $event->is_virtual ? 'Virtual / Online' : 'Physical' }}</dd>
                    </div>
                    @if(!$event->is_virtual)
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Venue</dt>
                        <dd class="text-gray-900 font-medium">{{ $event->venue_name }}</dd>
                        <dd class="text-gray-600 text-xs">{{ $event->venue_address }}, {{ $event->city }}, {{ $event->state }}</dd>
                    </div>
                    @else
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Stream URL</dt>
                        <dd class="text-gray-900 font-medium truncate">
                            <a href="{{ $event->virtual_link }}" target="_blank" class="text-primary-600 hover:underline">{{ $event->virtual_link }}</a>
                        </dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Submitted</dt>
                        <dd class="text-gray-900 font-medium">{{ $event->created_at->format('M j, Y \a\t g:ia') }}</dd>
                    </div>
                </dl>

                <div class="pt-4 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Description</p>
                    <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $event->description }}</p>
                </div>
            </div>

            {{-- Ticket types --}}
            <div class="card p-6">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Ticket Types</h2>
                @if($event->ticketTypes->isEmpty())
                    <p class="text-sm text-gray-400">No ticket types defined.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                    <th class="text-left pb-3 pr-4">Name</th>
                                    <th class="text-right pb-3 pr-4">Price</th>
                                    <th class="text-right pb-3 pr-4">Qty</th>
                                    <th class="text-right pb-3 pr-4">Max/Order</th>
                                    <th class="text-left pb-3">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($event->ticketTypes as $type)
                                    <tr>
                                        <td class="py-3 pr-4 font-medium text-gray-900">{{ $type->name }}</td>
                                        <td class="py-3 pr-4 text-right">{{ $type->price > 0 ? '₦'.number_format($type->price) : 'Free' }}</td>
                                        <td class="py-3 pr-4 text-right text-gray-600">{{ number_format($type->quantity) }}</td>
                                        <td class="py-3 pr-4 text-right text-gray-600">{{ $type->max_per_order ?: '—' }}</td>
                                        <td class="py-3 text-gray-500 text-xs">{{ $type->description ?: '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>

        {{-- Sidebar: Organizer info + Actions --}}
        <div class="space-y-5">

            {{-- Organizer --}}
            <div class="card p-5">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">Organizer</h3>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-semibold text-primary-600">{{ strtoupper(substr($event->organizer->name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ $event->organizer->name }}</p>
                        <p class="text-xs text-gray-400">{{ $event->organizer->email }}</p>
                    </div>
                </div>
                @if($event->organizer->phone)
                    <p class="text-xs text-gray-500">Phone: {{ $event->organizer->phone }}</p>
                @endif
            </div>

            {{-- Actions panel --}}
            @if($event->status === 'pending')
                <div class="card p-5" x-data="{ showRejectForm: false }">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">Review Actions</h3>

                    {{-- Approve --}}
                    <form method="POST" action="{{ route('admin.events.approve', $event) }}" class="mb-3">
                        @csrf
                        <button type="submit"
                                class="w-full py-2.5 px-4 rounded-xl bg-green-600 text-white text-sm font-semibold
                                       hover:bg-green-700 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Approve Event
                        </button>
                    </form>

                    {{-- Reject toggle --}}
                    <button type="button" @click="showRejectForm = !showRejectForm"
                            class="w-full py-2.5 px-4 rounded-xl border border-red-300 text-red-600 text-sm font-semibold
                                   hover:bg-red-50 transition-colors">
                        Reject Event
                    </button>

                    {{-- Reject form --}}
                    <div x-show="showRejectForm" x-cloak class="mt-4">
                        <form method="POST" action="{{ route('admin.events.reject', $event) }}">
                            @csrf
                            <label class="form-label mb-1">Rejection Reason <span class="text-red-500">*</span></label>
                            <textarea name="rejection_reason" rows="4" required
                                      class="form-input text-sm @error('rejection_reason') border-red-300 @enderror"
                                      placeholder="Explain why the event is being rejected…">{{ old('rejection_reason') }}</textarea>
                            @error('rejection_reason') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            <button type="submit"
                                    class="mt-3 w-full py-2.5 px-4 rounded-xl bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition-colors">
                                Send Rejection
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Feature toggle (approved events only) --}}
            @if($event->status === 'approved')
                <div class="card p-5">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Featured Listing</h3>
                    <p class="text-xs text-gray-500 mb-4">
                        Featured events appear on the homepage and top of search results.
                    </p>
                    <form method="POST" action="{{ route('admin.events.feature', $event) }}">
                        @csrf
                        <button type="submit"
                                class="w-full py-2.5 px-4 rounded-xl border text-sm font-semibold transition-colors
                                       {{ $event->is_featured
                                            ? 'border-yellow-400 text-yellow-700 bg-yellow-50 hover:bg-yellow-100'
                                            : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                            {{ $event->is_featured ? '★ Remove from Featured' : '☆ Mark as Featured' }}
                        </button>
                    </form>
                </div>
            @endif

            {{-- Danger zone --}}
            <div class="card p-5 border border-red-200">
                <h3 class="text-xs font-semibold text-red-500 uppercase tracking-wide mb-3">Danger Zone</h3>
                <p class="text-xs text-gray-500 mb-4">Permanently deletes this event along with all its tickets, orders, and sales data. This cannot be undone.</p>
                <form method="POST" action="{{ route('admin.events.destroy', $event) }}"
                      x-data="{}"
                      @submit.prevent="if(confirm('Delete \'{{ addslashes($event->title) }}\' and all its data? This cannot be undone.')) $el.submit()">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full py-2.5 px-4 rounded-xl bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition-colors">
                        Delete Event Permanently
                    </button>
                </form>
            </div>

            {{-- Event stats (approved) --}}
            @if($event->status === 'approved')
                <div class="card p-5">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">Sales Stats</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total sold</span>
                            <span class="font-semibold text-gray-900">{{ number_format($event->total_tickets_sold) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Gross revenue</span>
                            <span class="font-semibold text-gray-900">
                                ₦{{ number_format($event->ticketTypes->sum(fn($t) => $t->quantity_sold * $t->price)) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif

        </div>

    </div>

</x-admin-layout>
