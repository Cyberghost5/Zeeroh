<x-admin-layout>
    @section('page-title', 'Commission Settings')
    @section('page-subtitle', 'Configure platform fees applied at checkout')

    <div class="max-w-xl">
        <div class="card p-6">

            @if(session('success'))
                <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl px-4 py-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.commission.update') }}">
                @csrf
                @method('PATCH')

                <div class="space-y-5">

                    {{-- Commission % --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Platform Commission
                            <span class="text-gray-400 font-normal">(% of ticket subtotal deducted from organizer)</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="commission_percentage" step="0.01" min="0" max="100"
                                   value="{{ old('commission_percentage', $setting->commission_percentage) }}"
                                   class="input pr-8 @error('commission_percentage') border-red-400 @enderror">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">%</span>
                        </div>
                        @error('commission_percentage')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Service fee type --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Service Fee Type</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="service_fee_type" value="fixed"
                                       @checked(old('service_fee_type', $setting->service_fee_type) === 'fixed')
                                       class="w-4 h-4 text-primary-600">
                                <span class="text-sm text-gray-700">Fixed (₦ per order)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="service_fee_type" value="percentage"
                                       @checked(old('service_fee_type', $setting->service_fee_type) === 'percentage')
                                       class="w-4 h-4 text-primary-600">
                                <span class="text-sm text-gray-700">Percentage (% of subtotal)</span>
                            </label>
                        </div>
                        @error('service_fee_type')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Service fee value --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Service Fee Value</label>
                        <input type="number" name="service_fee_value" step="0.01" min="0"
                               value="{{ old('service_fee_value', $setting->service_fee_value) }}"
                               class="input @error('service_fee_value') border-red-400 @enderror">
                        <p class="text-xs text-gray-400 mt-1">
                            Enter a flat naira amount (e.g. 100) or a percentage (e.g. 1.5) depending on the type selected above.
                        </p>
                        @error('service_fee_value')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Preview box --}}
                <div class="mt-6 p-4 bg-gray-50 rounded-xl text-sm text-gray-600 border border-gray-200">
                    <p class="font-semibold text-gray-800 mb-2">Current settings preview</p>
                    <p>For a <strong>₦10,000</strong> order:</p>
                    @php
                        $exampleSubtotal = 10000;
                        $commission = $exampleSubtotal * ($setting->commission_percentage / 100);
                        $fee = $setting->service_fee_type === 'fixed'
                            ? $setting->service_fee_value
                            : $exampleSubtotal * ($setting->service_fee_value / 100);
                        $total = $exampleSubtotal + $fee;
                        $organizerNet = $exampleSubtotal - $commission;
                    @endphp
                    <ul class="mt-1 space-y-1 text-xs">
                        <li>→ Attendee pays: <strong>₦{{ number_format($total, 2) }}</strong> (subtotal + ₦{{ number_format($fee, 2) }} service fee)</li>
                        <li>→ Platform earns: <strong>₦{{ number_format($commission + $fee, 2) }}</strong> (commission + fee)</li>
                        <li>→ Organizer receives: <strong>₦{{ number_format($organizerNet, 2) }}</strong></li>
                    </ul>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="btn-primary">Save Settings</button>
                    <a href="{{ route('admin.revenue.index') }}" class="btn-secondary">Cancel</a>
                </div>

            </form>
        </div>
    </div>

</x-admin-layout>
