{{--
  Shared event form partial.
  Variables expected:
    $event     — Event model (null for create)
    $categories — Collection of categories
    $action    — form action URL
    $method    — 'POST' or 'PUT'
--}}

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" x-data="eventForm()" class="space-y-8">
    @csrf
    @if($method === 'PUT') @method('PUT') @endif

    {{-- ── BASIC INFO ─────────────────────────────────────────────── --}}
    <div class="card p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-5">Event Details</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Title --}}
            <div class="md:col-span-2">
                <label class="form-label">Event Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $event->title ?? '') }}"
                       class="form-input @error('title') border-red-300 @enderror"
                       placeholder="e.g. Lagos Music Festival 2026" required>
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Category --}}
            <div>
                <label class="form-label">Category</label>
                <select name="category_id" class="form-input @error('category_id') border-red-300 @enderror">
                    <option value="">Select category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ old('category_id', $event->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Venue type --}}
            <div>
                <label class="form-label">Event Type</label>
                <div class="flex gap-4 mt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="is_virtual" value="0"
                               x-model="isVirtual"
                               {{ old('is_virtual', isset($event) ? ($event->is_virtual ? '1' : '0') : '0') == '0' ? 'checked' : '' }}
                               class="h-4 w-4 text-primary-600 border-gray-300 focus:ring-primary-500">
                        <span class="text-sm text-gray-700">Physical</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="is_virtual" value="1"
                               x-model="isVirtual"
                               {{ old('is_virtual', isset($event) ? ($event->is_virtual ? '1' : '0') : '0') == '1' ? 'checked' : '' }}
                               class="h-4 w-4 text-primary-600 border-gray-300 focus:ring-primary-500">
                        <span class="text-sm text-gray-700">Virtual / Online</span>
                    </label>
                </div>
            </div>

            {{-- Description --}}
            <div class="md:col-span-2">
                <label class="form-label">Description <span class="text-red-500">*</span></label>
                <textarea name="description" rows="5"
                          class="form-input @error('description') border-red-300 @enderror"
                          placeholder="Describe your event in detail…" required>{{ old('description', $event->description ?? '') }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

        </div>
    </div>

    {{-- ── DATE & TIME ────────────────────────────────────────────── --}}
    <div class="card p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-5">Date & Time</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

            <div>
                <label class="form-label">Start Date <span class="text-red-500">*</span></label>
                <input type="date" name="start_date"
                       value="{{ old('start_date', isset($event) ? $event->start_date->format('Y-m-d') : '') }}"
                       class="form-input @error('start_date') border-red-300 @enderror" required>
                @error('start_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="form-label">Start Time <span class="text-red-500">*</span></label>
                <input type="time" name="start_time"
                       value="{{ old('start_time', $event->start_time ?? '') }}"
                       class="form-input @error('start_time') border-red-300 @enderror" required>
                @error('start_time') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="form-label">End Date <span class="text-gray-400 font-normal">(opt.)</span></label>
                <input type="date" name="end_date"
                       value="{{ old('end_date', isset($event) && $event->end_date ? $event->end_date->format('Y-m-d') : '') }}"
                       class="form-input @error('end_date') border-red-300 @enderror">
                @error('end_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="form-label">End Time <span class="text-gray-400 font-normal">(opt.)</span></label>
                <input type="time" name="end_time"
                       value="{{ old('end_time', $event->end_time ?? '') }}"
                       class="form-input @error('end_time') border-red-300 @enderror">
                @error('end_time') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

        </div>
    </div>

    {{-- ── VENUE ──────────────────────────────────────────────────── --}}
    <div class="card p-6" x-show="isVirtual === '0'">
        <h2 class="text-base font-semibold text-gray-900 mb-5">Venue</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <div>
                <label class="form-label">Venue Name</label>
                <input type="text" name="venue_name"
                       value="{{ old('venue_name', $event->venue_name ?? '') }}"
                       class="form-input @error('venue_name') border-red-300 @enderror"
                       placeholder="e.g. Eko Convention Centre">
                @error('venue_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="form-label">City</label>
                <input type="text" name="city"
                       value="{{ old('city', $event->city ?? '') }}"
                       class="form-input @error('city') border-red-300 @enderror"
                       placeholder="e.g. Lagos">
                @error('city') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="form-label">Address</label>
                <input type="text" name="venue_address"
                       value="{{ old('venue_address', $event->venue_address ?? '') }}"
                       class="form-input @error('venue_address') border-red-300 @enderror"
                       placeholder="Full street address">
                @error('venue_address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="form-label">State</label>
                <select name="state" class="form-input @error('state') border-red-300 @enderror">
                    <option value="">Select state</option>
                    @foreach(['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River',
                              'Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano',
                              'Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun',
                              'Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'] as $state)
                        <option value="{{ $state }}"
                            {{ old('state', $event->state ?? '') === $state ? 'selected' : '' }}>{{ $state }}</option>
                    @endforeach
                </select>
                @error('state') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

        </div>
    </div>

    {{-- Virtual link --}}
    <div class="card p-6" x-show="isVirtual === '1'">
        <h2 class="text-base font-semibold text-gray-900 mb-5">Virtual Event Link</h2>
        <div>
            <label class="form-label">Stream / Meeting URL</label>
            <input type="url" name="virtual_link"
                   value="{{ old('virtual_link', $event->virtual_link ?? '') }}"
                   class="form-input @error('virtual_link') border-red-300 @enderror"
                   placeholder="https://zoom.us/j/...">
            @error('virtual_link') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- ── BANNER ─────────────────────────────────────────────────── --}}
    <div class="card p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-1">Event Banner</h2>
        <p class="text-xs text-gray-500 mb-5">JPG, PNG or WebP · Max 4 MB · Recommended: 1200×630px</p>

        @if(isset($event) && $event->banner)
            <div class="mb-4">
                <img src="{{ Storage::url($event->banner) }}" alt="Current banner"
                     class="h-40 w-full object-cover rounded-lg border border-gray-200">
                <label class="flex items-center gap-2 mt-2 cursor-pointer">
                    <input type="checkbox" name="remove_banner" value="1" class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="text-sm text-red-600">Remove current banner</span>
                </label>
            </div>
        @endif

        <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-300
                      rounded-lg cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition-colors"
               x-on:dragover.prevent x-on:drop.prevent="handleDrop($event)">
            <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-sm text-gray-500">Click or drag to upload</p>
            <p x-text="fileName" class="text-xs text-primary-600 mt-1 font-medium"></p>
            <input type="file" name="banner" accept="image/*" class="hidden"
                   x-on:change="fileName = $event.target.files[0]?.name ?? ''">
        </label>
        @error('banner') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- ── TICKET TYPES ───────────────────────────────────────────── --}}
    <div class="card p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Ticket Types</h2>
                <p class="text-xs text-gray-500 mt-0.5">Add one or more ticket categories (e.g. VIP, Regular, Early Bird)</p>
            </div>
            <button type="button" @click="addTicket()"
                    class="btn-secondary text-sm py-2 px-4">
                + Add Ticket Type
            </button>
        </div>

        @error('ticket_types') <p class="mb-3 text-xs text-red-600">{{ $message }}</p> @enderror

        <div class="space-y-4" id="ticket-types-list">
            <template x-for="(ticket, index) in tickets" :key="ticket._key">
                <div class="border border-gray-200 rounded-xl p-5 bg-gray-50 relative">

                    {{-- Pass the real DB id so the controller knows to update vs create --}}
                    <input type="hidden" :name="`ticket_types[${index}][id]`" :value="ticket.id ?? ''">

                    <button type="button" @click="removeTicket(index)"
                            x-show="tickets.length > 1"
                            class="absolute top-3 right-3 text-gray-400 hover:text-red-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                        <div class="sm:col-span-2">
                            <label class="form-label">Ticket Name <span class="text-red-500">*</span></label>
                            <input type="text" :name="`ticket_types[${index}][name]`" x-model="ticket.name"
                                   class="form-input" placeholder="e.g. VIP, Regular, Early Bird" required>
                            @error('ticket_types.*.name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="form-label">Price (₦) <span class="text-red-500">*</span></label>
                            <input type="number" :name="`ticket_types[${index}][price]`" x-model="ticket.price"
                                   class="form-input" placeholder="0" min="0" step="0.01" required>
                            @error('ticket_types.*.price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="form-label">Quantity <span class="text-red-500">*</span></label>
                            <input type="number" :name="`ticket_types[${index}][quantity]`" x-model="ticket.quantity"
                                   class="form-input" placeholder="100" min="1" required>
                            @error('ticket_types.*.quantity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="form-label">Description <span class="text-gray-400 font-normal">(opt.)</span></label>
                            <input type="text" :name="`ticket_types[${index}][description]`" x-model="ticket.description"
                                   class="form-input" placeholder="What does this ticket include?">
                        </div>

                        <div>
                            <label class="form-label">Max Per Order</label>
                            <input type="number" :name="`ticket_types[${index}][max_per_order]`" x-model="ticket.max_per_order"
                                   class="form-input" placeholder="10" min="1" max="100">
                        </div>

                        <div class="flex items-end">
                            <p class="text-sm text-gray-500 pb-2">
                                Price: <span class="font-semibold text-gray-800" x-text="ticket.price == 0 ? 'FREE' : '₦' + Number(ticket.price).toLocaleString()"></span>
                            </p>
                        </div>

                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Submit --}}
    <div class="flex items-center justify-between pt-2">
        <a href="{{ route('organizer.events.index') }}" class="btn-secondary">Cancel</a>
        <button type="submit" class="btn-primary px-8">
            {{ $method === 'PUT' ? 'Update & Re-submit' : 'Submit for Review' }}
        </button>
    </div>

</form>

@php
    $ticketDefaults = (isset($event) && $event->ticketTypes && $event->ticketTypes->count())
        ? $event->ticketTypes->map(fn($t) => [
              '_key'          => $t->id,
              'id'            => $t->id,
              'name'          => $t->name,
              'price'         => (float) $t->price,
              'quantity'      => (int) $t->quantity,
              'description'   => $t->description ?? '',
              'max_per_order' => (int) $t->max_per_order,
          ])->values()->toArray()
        : [['_key' => 0, 'id' => null, 'name' => '', 'price' => 0, 'quantity' => 100, 'description' => '', 'max_per_order' => 10]];
@endphp

@push('scripts')
<script>
    const existingTickets = @json($ticketDefaults);

    function eventForm() {
        return {
            isVirtual: '{{ old('is_virtual', isset($event) ? ($event->is_virtual ? '1' : '0') : '0') }}',
            fileName: '',
            tickets: existingTickets,
            addTicket() {
                this.tickets.push({
                    _key: Date.now(),
                    id: null,
                    name: '', price: 0, quantity: 100, description: '', max_per_order: 10
                });
            },
            removeTicket(index) {
                if (this.tickets.length > 1) this.tickets.splice(index, 1);
            },
            handleDrop(e) {
                const file = e.dataTransfer.files[0];
                if (file) {
                    this.fileName = file.name;
                    const input = this.$el.querySelector('input[type=file]');
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    input.files = dt.files;
                }
            }
        };
    }
</script>
@endpush
