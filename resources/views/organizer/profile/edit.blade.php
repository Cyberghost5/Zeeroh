<x-organizer-layout>
@section('page-title', 'Organisation Profile')
@section('page-subtitle', 'Manage your public profile and payout details')

<div class="max-w-3xl space-y-6">

    <form method="POST" action="{{ route('organizer.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PATCH')

        {{-- Organisation info --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Organisation Info</h2>

            {{-- Logo --}}
            <div class="flex items-center gap-5">
                <div class="w-20 h-20 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-200">
                    @if($profile?->logo)
                        <img src="{{ Storage::url($profile->logo) }}" alt="Logo" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Organisation Logo</label>
                    <input type="file" name="logo" accept="image/*"
                           class="text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-primary-50 file:text-primary-700 file:text-xs file:font-medium hover:file:bg-primary-100">
                    <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 2 MB</p>
                    @error('logo')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Organisation name --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Organisation Name <span class="text-red-500">*</span></label>
                <input type="text" name="organization_name"
                       value="{{ old('organization_name', $profile?->organization_name) }}"
                       class="w-full rounded-xl border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"
                       placeholder="e.g. Afrobeats Nigeria">
                @error('organization_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Bio --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bio / About</label>
                <textarea name="bio" rows="4"
                          class="w-full rounded-xl border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"
                          placeholder="Tell attendees about your organisation…">{{ old('bio', $profile?->bio) }}</textarea>
                @error('bio')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Website --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                <input type="url" name="website"
                       value="{{ old('website', $profile?->website) }}"
                       class="w-full rounded-xl border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"
                       placeholder="https://yourwebsite.com">
                @error('website')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Social links --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Social Links</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Facebook</label>
                    <input type="url" name="facebook"
                           value="{{ old('facebook', $profile?->facebook) }}"
                           class="w-full rounded-xl border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"
                           placeholder="https://facebook.com/…">
                    @error('facebook')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Twitter / X</label>
                    <input type="url" name="twitter"
                           value="{{ old('twitter', $profile?->twitter) }}"
                           class="w-full rounded-xl border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"
                           placeholder="https://twitter.com/…">
                    @error('twitter')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Instagram</label>
                    <input type="url" name="instagram"
                           value="{{ old('instagram', $profile?->instagram) }}"
                           class="w-full rounded-xl border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"
                           placeholder="https://instagram.com/…">
                    @error('instagram')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Payout / bank details --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <div class="flex items-start gap-3">
                <div class="flex-1">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Payout / Bank Details</h2>
                    <p class="text-xs text-gray-400 mt-1">Used for processing event payouts. Keep this accurate.</p>
                </div>
                @if($profile?->is_verified)
                    <span class="text-xs bg-green-50 text-green-700 border border-green-200 rounded-full px-3 py-1">✓ Verified</span>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Bank Name</label>
                    <input type="text" name="bank_name"
                           value="{{ old('bank_name', $profile?->bank_name) }}"
                           class="w-full rounded-xl border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"
                           placeholder="e.g. First Bank">
                    @error('bank_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Account Number</label>
                    <input type="text" name="account_number"
                           value="{{ old('account_number', $profile?->account_number) }}"
                           maxlength="10"
                           class="w-full rounded-xl border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500 font-mono"
                           placeholder="10-digit NUBAN">
                    @error('account_number')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Account Name</label>
                    <input type="text" name="account_name"
                           value="{{ old('account_name', $profile?->account_name) }}"
                           class="w-full rounded-xl border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"
                           placeholder="As it appears on your bank statement">
                    @error('account_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-colors">
                Save Profile
            </button>
        </div>
    </form>

    @if($profile?->slug)
    <div class="bg-primary-50 border border-primary-200 rounded-xl px-4 py-3 text-sm text-primary-800 flex items-center gap-3">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
        </svg>
        Your public page:&nbsp;
        <a href="{{ route('organizers.show', $profile->slug) }}" target="_blank"
           class="font-medium underline hover:text-primary-900 truncate">
            {{ url('/organizers/' . $profile->slug) }}
        </a>
    </div>
    @endif

</div>
</x-organizer-layout>
