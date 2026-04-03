<x-admin-layout>
    @section('page-title', 'Payout Requests')
    @section('page-subtitle', 'Review and process organizer payout requests')

    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl px-4 py-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="card p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Pending</p>
            <p class="text-xl font-bold text-yellow-600">₦{{ number_format($stats['pending'], 2) }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Approved (not yet paid)</p>
            <p class="text-xl font-bold text-blue-600">₦{{ number_format($stats['approved'], 2) }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total Paid Out</p>
            <p class="text-xl font-bold text-green-600">₦{{ number_format($stats['paid'], 2) }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="flex gap-2 mb-6">
        <select name="status" class="input-field text-sm py-2" onchange="this.form.submit()">
            <option value="">All statuses</option>
            @foreach(['pending','approved','paid','rejected'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </form>

    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 font-medium text-gray-500">Organizer</th>
                    <th class="text-left px-5 py-3 font-medium text-gray-500">Amount</th>
                    <th class="text-left px-5 py-3 font-medium text-gray-500">Bank</th>
                    <th class="text-left px-5 py-3 font-medium text-gray-500">Requested</th>
                    <th class="text-left px-5 py-3 font-medium text-gray-500">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($requests as $req)
                    <tr x-data="{ rejectOpen: false }">
                        <td class="px-5 py-4">
                            <p class="font-medium text-gray-900">{{ $req->organizer->organizerProfile?->organization_name ?? $req->organizer->name }}</p>
                            <p class="text-xs text-gray-400">{{ $req->organizer->email }}</p>
                        </td>
                        <td class="px-5 py-4 font-semibold text-gray-900">₦{{ number_format($req->amount, 2) }}</td>
                        <td class="px-5 py-4 text-xs text-gray-500">
                            {{ $req->bank_name }}<br>
                            <span class="font-mono">{{ $req->account_number }}</span><br>
                            {{ $req->account_name }}
                        </td>
                        <td class="px-5 py-4 text-xs text-gray-500">{{ $req->created_at->format('M j, Y') }}</td>
                        <td class="px-5 py-4">
                            @if($req->status === 'pending')
                                <span class="badge-yellow">Pending</span>
                            @elseif($req->status === 'approved')
                                <span class="badge-blue">Approved</span>
                            @elseif($req->status === 'paid')
                                <span class="badge-success">Paid {{ $req->paid_at?->format('M j') }}</span>
                            @else
                                <span class="badge-red">Rejected</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($req->status === 'pending')
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('admin.payouts.approve', $req) }}" method="POST">
                                        @csrf
                                        <button class="text-xs btn-primary py-1.5 px-3">Approve</button>
                                    </form>
                                    <button @click="rejectOpen = !rejectOpen"
                                            class="text-xs text-red-600 hover:text-red-800 font-medium">Reject</button>
                                </div>
                                {{-- Reject inline form --}}
                                <div x-show="rejectOpen" x-transition class="mt-3">
                                    <form action="{{ route('admin.payouts.reject', $req) }}" method="POST" class="space-y-2">
                                        @csrf
                                        <textarea name="admin_notes" rows="2" placeholder="Reason for rejection…"
                                                  class="input-field w-full text-xs py-1.5" required maxlength="500"></textarea>
                                        <button type="submit" class="text-xs text-red-600 border border-red-200 rounded px-3 py-1.5 hover:bg-red-50">Confirm Reject</button>
                                    </form>
                                </div>
                            @elseif($req->status === 'approved')
                                <form action="{{ route('admin.payouts.paid', $req) }}" method="POST">
                                    @csrf
                                    <button class="text-xs btn-primary py-1.5 px-3">Mark Paid</button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400">{{ $req->admin_notes ? 'Note: '.Str::limit($req->admin_notes, 40) : '—' }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-gray-400 text-sm">No payout requests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $requests->links() }}</div>
</x-admin-layout>
