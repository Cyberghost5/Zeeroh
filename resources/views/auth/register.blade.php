<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Create an account</h1>
        <p class="text-sm text-gray-500 mt-1">Join Zeeroh — discover and host events</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        {{-- Account type --}}
        <div>
            <label class="form-label">I want to</label>
            <div class="grid grid-cols-2 gap-3 mt-1">
                <label class="relative cursor-pointer">
                    <input type="radio" name="role" value="attendee" class="sr-only peer"
                           {{ old('role', 'attendee') === 'attendee' ? 'checked' : '' }}>
                    <div class="flex flex-col items-center justify-center p-3 rounded-lg border-2 border-gray-200
                                peer-checked:border-primary-600 peer-checked:bg-primary-50 transition-all duration-150 text-center">
                        <svg class="w-5 h-5 text-gray-500 peer-checked:text-primary-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="text-xs font-semibold text-gray-700">Attend Events</span>
                    </div>
                </label>
                <label class="relative cursor-pointer">
                    <input type="radio" name="role" value="organizer" class="sr-only peer"
                           {{ old('role') === 'organizer' ? 'checked' : '' }}>
                    <div class="flex flex-col items-center justify-center p-3 rounded-lg border-2 border-gray-200
                                peer-checked:border-primary-600 peer-checked:bg-primary-50 transition-all duration-150 text-center">
                        <svg class="w-5 h-5 text-gray-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-xs font-semibold text-gray-700">Host Events</span>
                    </div>
                </label>
            </div>
            @error('role')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Full name --}}
        <div>
            <label for="name" class="form-label">Full name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}"
                   class="form-input @error('name') border-red-300 @enderror"
                   required autofocus autocomplete="name" placeholder="John Doe">
            @error('name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="form-label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="form-input @error('email') border-red-300 @enderror"
                   required autocomplete="username" placeholder="you@example.com">
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Phone --}}
        <div>
            <label for="phone" class="form-label">Phone number <span class="text-gray-400 font-normal">(optional)</span></label>
            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}"
                   class="form-input @error('phone') border-red-300 @enderror"
                   autocomplete="tel" placeholder="080XXXXXXXX">
            @error('phone')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password"
                   class="form-input @error('password') border-red-300 @enderror"
                   required autocomplete="new-password" placeholder="Min. 8 characters">
            @error('password')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm password --}}
        <div>
            <label for="password_confirmation" class="form-label">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="form-input" required autocomplete="new-password" placeholder="Repeat password">
        </div>

        <button type="submit" class="btn-primary w-full mt-2">
            Create account
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        Already have an account?
        <a href="{{ route('login') }}" class="text-primary-600 font-semibold hover:text-primary-700">Sign in</a>
    </p>
</x-guest-layout>
