<x-organizer-layout>
    <x-slot name="header">New Promo Code</x-slot>

    <div class="max-w-2xl">
        <a href="{{ route('organizer.promos.index') }}" class="text-sm text-gray-500 hover:text-primary-600 mb-6 inline-block">← Back to Promo Codes</a>

        @if($errors->any())
            <div class="mb-5 bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card p-6">
            <form action="{{ route('organizer.promos.store') }}" method="POST" class="space-y-5">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code <span class="text-red-500">*</span></label>
                        <input type="text" name="code" value="{{ old('code') }}"
                               class="input-field w-full uppercase" placeholder="SUMMER20" required
                               style="text-transform:uppercase">
                        <p class="text-xs text-gray-400 mt-1">Letters, numbers, hyphens/underscores only.</p>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apply to Event</label>
                        <select name="event_id" class="input-field w-full">
                            <option value="">All my events</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" @selected(old('event_id') == $event->id)>{{ $event->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Discount Type <span class="text-red-500">*</span></label>
                        <select name="type" class="input-field w-full" required>
                            <option value="percentage" @selected(old('type', 'percentage') === 'percentage')>Percentage (%)</option>
                            <option value="fixed" @selected(old('type') === 'fixed')>Fixed Amount (₦)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Value <span class="text-red-500">*</span></label>
                        <input type="number" name="value" value="{{ old('value') }}"
                               class="input-field w-full" placeholder="e.g. 20" min="0.01" step="0.01" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Usage Limit</label>
                        <input type="number" name="usage_limit" value="{{ old('usage_limit') }}"
                               class="input-field w-full" placeholder="Leave blank for unlimited" min="1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min. Order Amount (₦)</label>
                        <input type="number" name="min_order_amount" value="{{ old('min_order_amount') }}"
                               class="input-field w-full" placeholder="Leave blank for none" min="0">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Valid From</label>
                        <input type="datetime-local" name="valid_from" value="{{ old('valid_from') }}" class="input-field w-full">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Valid Until</label>
                        <input type="datetime-local" name="valid_until" value="{{ old('valid_until') }}" class="input-field w-full">
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary py-2.5 px-6">Create Promo Code</button>
                    <a href="{{ route('organizer.promos.index') }}" class="btn-outline py-2.5 px-6">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-organizer-layout>
