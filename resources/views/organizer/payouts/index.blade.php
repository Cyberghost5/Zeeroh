<x-organizer-layout>
    <x-slot name="header">Payouts</x-slot>

    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl px-4 py-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-xl px-4 py-3">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Balance cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="card p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Available Balance</p>
            <p class="text-2xl font-bold text-primary-600">₦{{ number_format($availableBalance, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">After commissions &amp; paid-out requests</p>
        </div>
        <div class="card p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total Earned (Net)</p>
            <p class="text-2xl font-bold text-gray-800">₦{{ number_format($totalEarned, 2) }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total Paid Out</p>
            <p class="text-2xl font-bold text-gray-800">₦{{ number_format($totalPaidOut, 2) }}</p>
        </div>
    </div>

    {{-- Request payout form --}}
    @if($profile && $profile->bank_name && $profile->account_number)
        @if($pendingRequests > 0)
            <div class="card p-5 mb-8 bg-yellow-50 border border-yellow-200">
                <p class="text-sm font-medium text-yellow-800">You have a pending payout request for ₦{{ number_format($pendingRequests, 2) }}.</p>
                <p class="text-xs text-yellow-600 mt-0.5">Please wait for it to be processed before submitting a new one.</p>
            </div>
        @elseif($availableBalance >= 1000)
            <div class="card p-6 mb-8" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center justify-between w-full text-left">
                    <span class="font-semibold text-gray-800">Request a Payout</span>
                    <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="mt-5">
                    <div class="bg-gray-50 rounded-lg p-4 text-sm mb-4 space-y-1">
                        <p class="text-gray-500">Bank: <span class="font-medium text-gray-800">{{ $profile->bank_name }}</span></p>
                        <p class="text-gray-500">Account: <span class="font-medium text-gray-800">{{ $profile->account_number }}</span></p>
                        <p class="text-gray-500">Name: <span class="font-medium text-gray-800">{{ $profile->account_name }}</span></p>
                        <a href="{{ route('organizer.profile.edit') }}" class="text-xs text-primary-600 hover:underline">Update bank details</a>
                    </div>

                    <form action="{{ route('organizer.payouts.store') }}" method="POST" class="flex gap-3">
                        @csrf
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Amount (₦)</label>
                            <input type="number" name="amount" min="1000" max="{{ $availableBalance }}"
                                   step="1" placeholder="e.g. 50000"
                                   value="{{ old('amount') }}"
                                   class="input-field w-full @error('amount') border-red-400 @enderror">
                            @error('amount')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="btn-primary py-2.5 px-5">Request</button>
                        </div>
                    </form>
                    <p class="text-xs text-gray-400 mt-2">Minimum ₦1,000. Processed within 2–3 business days.</p>
                </div>
            </div>
        @else
            <div class="card p-5 mb-8 border-dashed border-gray-200">
                <p class="text-sm text-gray-500">Your available balance is below the ₦1,000 minimum payout threshold.</p>
            </div>
        @endif
    @else
        <div class="card p-5 mb-8 bg-blue-50 border border-blue-200">
            <p class="text-sm font-medium text-blue-800">Add your bank details to request payouts.</p>
            <a href="{{ route('organizer.profile.edit') }}" class="text-xs text-primary-600 mt-1 inline-block hover:underline">Go to Profile Settings →</a>
        </div>
    @endif

    {{-- Request history --}}
    <h2 class="text-base font-semibold text-gray-800 mb-3">Payout History</h2>

    @if($requests->isEmpty())
        <div class="card p-8 text-center text-gray-400 text-sm">No payout requests yet.</div>
    @else
        <div class="card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Date</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Amount</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Bank</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Status</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($requests as $req)
                        <tr>
                            <td class="px-5 py-4 text-gray-500 text-xs">{{ $req->created_at->format('M j, Y') }}</td>
                            <td class="px-5 py-4 font-semibold text-gray-800">₦{{ number_format($req->amount, 2) }}</td>
                            <td class="px-5 py-4 text-gray-500 text-xs">
                                {{ $req->bank_name }}<br>
                                <span class="font-mono">{{ $req->account_number }}</span>
                            </td>
                            <td class="px-5 py-4">
                                @if($req->status === 'pending')
                                    <span class="badge-yellow">Pending</span>
                                @elseif($req->status === 'approved')
                                    <span class="badge-blue">Approved</span>
                                @elseif($req->status === 'paid')
                                    <span class="badge-success">Paid</span>
                                @else
                                    <span class="badge-red">Rejected</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-gray-400 text-xs max-w-xs truncate">{{ $req->admin_notes ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $requests->links() }}</div>
    @endif
</x-organizer-layout>
