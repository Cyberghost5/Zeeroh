@extends('layouts.public')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="text-xs text-gray-400 mb-6 flex items-center gap-2">
        <a href="{{ route('events.show', $event->slug) }}" class="hover:text-primary-600">← Back to {{ $event->title }}</a>
    </nav>

    <h1 class="text-2xl font-bold text-gray-900 mb-8">Checkout</h1>

    @if(session('error'))
        <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

        {{-- ── Order summary ── --}}
        <div class="lg:col-span-3">
            <div class="card p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-5">Order Summary</h2>

                {{-- Event brief --}}
                <div class="flex gap-4 p-4 bg-gray-50 rounded-xl mb-5">
                    @if($event->banner)
                        <img src="{{ Storage::url($event->banner) }}" alt="" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                    @endif
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ $event->title }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $event->start_date->format('D, M j, Y') }} at {{ $event->start_time }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $event->is_virtual ? 'Online' : ($event->city . ', ' . $event->state) }}
                        </p>
                    </div>
                </div>

                {{-- Line items --}}
                <div class="space-y-3 mb-5">
                    @foreach($lineItems as $item)
                        <div class="flex items-center justify-between text-sm">
                            <div>
                                <p class="font-medium text-gray-900">{{ $item['type']->name }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $item['quantity'] }} × {{ $item['type']->price > 0 ? '₦'.number_format($item['type']->price) : 'Free' }}
                                </p>
                            </div>
                            <span class="font-semibold text-gray-900">
                                {{ $item['type']->price > 0 ? '₦'.number_format($item['subtotal']) : 'Free' }}
                            </span>
                        </div>
                    @endforeach
                </div>

                {{-- Totals --}}
                <div class="border-t border-gray-100 pt-4 space-y-2" id="totals-block">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Subtotal</span>
                        <span>{{ $subtotal > 0 ? '₦'.number_format($subtotal) : 'Free' }}</span>
                    </div>
                    <div id="discount-row" class="hidden flex justify-between text-sm text-green-600 font-medium">
                        <span>Promo discount (<span id="promo-label"></span>)</span>
                        <span>−₦<span id="discount-amount">0</span></span>
                    </div>
                    @if($serviceFee > 0)
                        <div class="flex justify-between text-sm text-gray-600" id="fee-row">
                            <span>Service fee</span>
                            <span id="fee-display">₦{{ number_format($serviceFee) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-base font-bold text-gray-900 pt-1 border-t border-gray-200">
                        <span>Total</span>
                        <span id="total-display">{{ $total > 0 ? '₦'.number_format($total) : 'Free' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Payment CTA ── --}}
        <div class="lg:col-span-2">
            <div class="card p-6 sticky top-20">

                <h2 class="text-base font-semibold text-gray-900 mb-5">
                    {{ $total > 0 ? 'Pay ₦'.number_format($total) : 'Confirm Free Tickets' }}
                </h2>

                <div class="flex items-center gap-3 mb-5 p-3 bg-gray-50 rounded-xl">
                    <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-primary-700 font-bold text-sm">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                {{-- Hidden form posts to initiate controller --}}
                <form method="POST" action="{{ route('checkout.initiate', $event->slug) }}"
                      x-data="promoCode({{ $subtotal }}, {{ $serviceFee }}, {{ $total }}, {{ $event->id }})"
                      @submit="handleSubmit">
                    @csrf
                    @foreach($lineItems as $item)
                        <input type="hidden" name="ticket_types[{{ $loop->index }}][id]" value="{{ $item['type']->id }}">
                        <input type="hidden" name="ticket_types[{{ $loop->index }}][quantity]" value="{{ $item['quantity'] }}">
                    @endforeach
                    <input type="hidden" name="promo_code" x-model="appliedCode">

                    {{-- Promo code input --}}
                    @if($subtotal > 0)
                    <div class="mb-5">
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Have a promo code?</label>
                        <div class="flex gap-2">
                            <input type="text" x-model="codeInput" @keydown.enter.prevent="applyCode"
                                   placeholder="Enter code" maxlength="50"
                                   class="input-field flex-1 text-sm uppercase py-2"
                                   style="text-transform:uppercase"
                                   :disabled="appliedCode !== ''">
                            <button type="button" @click="appliedCode ? removeCode() : applyCode()"
                                    class="text-sm font-medium px-3 py-2 rounded-lg border transition-colors"
                                    :class="appliedCode ? 'border-red-200 text-red-600 hover:bg-red-50' : 'border-primary-200 text-primary-600 hover:bg-primary-50'">
                                <span x-text="appliedCode ? 'Remove' : 'Apply'"></span>
                            </button>
                        </div>
                        <p x-show="promoError" x-text="promoError" class="text-xs text-red-600 mt-1"></p>
                        <p x-show="promoSuccess" x-text="promoSuccess" class="text-xs text-green-600 mt-1 font-medium"></p>
                    </div>
                    @endif

                    @if($total > 0)
                        <p class="text-xs text-gray-400 mb-4">
                            You'll be redirected to Paystack to complete payment securely.
                        </p>
                        <button type="submit" class="w-full btn-primary py-3 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Pay <span x-text="btnTotal"></span> with Paystack
                        </button>
                    @else
                        <button type="submit" class="w-full btn-primary py-3">
                            Confirm Free Registration
                        </button>
                    @endif
                </form>

                <p class="text-xs text-center text-gray-400 mt-3">
                    Secured by Paystack · SSL encrypted
                </p>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
function promoCode(subtotal, serviceFee, total, eventId) {
    return {
        codeInput: '',
        appliedCode: '',
        promoError: '',
        promoSuccess: '',
        discountAmount: 0,
        btnTotal: '₦' + total.toLocaleString('en-NG'),
        currentTotal: total,
        baseFee: serviceFee,
        subtotal: subtotal,

        async applyCode() {
            this.promoError = '';
            this.promoSuccess = '';
            const code = this.codeInput.trim().toUpperCase();
            if (!code) return;

            try {
                const res = await fetch('{{ route("checkout.validate-promo") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ code, subtotal: this.subtotal, event_id: eventId }),
                });
                const data = await res.json();
                if (!res.ok || !data.valid) {
                    this.promoError = data.message || 'Invalid promo code.';
                    return;
                }
                this.appliedCode = code;
                this.discountAmount = data.discount;
                this.promoSuccess = data.description + ' applied!';
                this.updateTotals();
                // Update discount row UI
                document.getElementById('discount-row').classList.remove('hidden');
                document.getElementById('promo-label').textContent = data.description;
                document.getElementById('discount-amount').textContent = data.discount.toLocaleString('en-NG');
            } catch (e) {
                this.promoError = 'Could not validate code. Please try again.';
            }
        },

        removeCode() {
            this.appliedCode = '';
            this.codeInput = '';
            this.discountAmount = 0;
            this.promoError = '';
            this.promoSuccess = '';
            this.updateTotals();
            document.getElementById('discount-row').classList.add('hidden');
        },

        updateTotals() {
            const discounted = Math.max(0, this.subtotal - this.discountAmount);
            const fee = discounted > 0 ? Math.round(this.baseFee * (discounted / this.subtotal) * 100) / 100 : 0;
            const newTotal = discounted + fee;
            this.currentTotal = newTotal;
            this.btnTotal = newTotal > 0 ? '₦' + Math.round(newTotal).toLocaleString('en-NG') : 'Free';
            document.getElementById('total-display').textContent = newTotal > 0 ? '₦' + Math.round(newTotal).toLocaleString('en-NG') : 'Free';
            const feeRow = document.getElementById('fee-row');
            if (feeRow) {
                document.getElementById('fee-display').textContent = '₦' + Math.round(fee).toLocaleString('en-NG');
            }
        },

        handleSubmit(e) {
            // allow form to submit normally; promo_code x-model already bound
        },
    };
}
</script>
@endpush
@endsection
