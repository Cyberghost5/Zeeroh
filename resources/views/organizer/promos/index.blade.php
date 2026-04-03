<x-organizer-layout>
    <x-slot name="header">Promo Codes</x-slot>

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

    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">{{ $codes->total() }} code{{ $codes->total() !== 1 ? 's' : '' }}</p>
        <a href="{{ route('organizer.promos.create') }}" class="btn-primary text-sm py-2">+ New Promo Code</a>
    </div>

    @if($codes->isEmpty())
        <div class="card p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            <p class="text-gray-600 font-medium">No promo codes yet</p>
            <p class="text-sm text-gray-400 mt-1">Create your first discount code to reward attendees.</p>
            <a href="{{ route('organizer.promos.create') }}" class="btn-primary text-sm py-2 mt-4 inline-block">Create Promo Code</a>
        </div>
    @else
        <div class="card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Code</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Discount</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Event</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Used</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Expires</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($codes as $code)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-4">
                                <span class="font-mono font-semibold text-gray-900 bg-gray-100 px-2 py-0.5 rounded text-xs">{{ $code->code }}</span>
                            </td>
                            <td class="px-5 py-4 font-medium text-gray-800">
                                {{ $code->type === 'percentage' ? $code->value.'%' : '₦'.number_format($code->value) }} off
                            </td>
                            <td class="px-5 py-4 text-gray-500">
                                {{ $code->event?->title ?? 'All events' }}
                            </td>
                            <td class="px-5 py-4 text-gray-600">
                                {{ $code->used_count }}{{ $code->usage_limit ? ' / '.$code->usage_limit : '' }}
                            </td>
                            <td class="px-5 py-4 text-gray-500 text-xs">
                                {{ $code->valid_until ? $code->valid_until->format('M j, Y') : '—' }}
                            </td>
                            <td class="px-5 py-4">
                                @if($code->is_active)
                                    <span class="badge-success">Active</span>
                                @else
                                    <span class="badge-gray">Inactive</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 flex items-center gap-3 justify-end">
                                <form action="{{ route('organizer.promos.toggle', $code) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button class="text-xs text-gray-500 hover:text-primary-600 font-medium">
                                        {{ $code->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                @if($code->used_count === 0)
                                    <form action="{{ route('organizer.promos.destroy', $code) }}" method="POST"
                                          onsubmit="return confirm('Delete this promo code?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-xs text-red-500 hover:text-red-700 font-medium">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $codes->links() }}</div>
    @endif
</x-organizer-layout>
