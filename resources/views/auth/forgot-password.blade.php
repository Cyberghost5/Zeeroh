<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Reset your password</h1>
        <p class="text-sm text-gray-500 mt-1">Enter your email and we'll send you a reset link.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="form-label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="form-input @error('email') border-red-300 @enderror"
                   required autofocus placeholder="you@example.com">
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-primary w-full">
            Send reset link
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        Remembered it?
        <a href="{{ route('login') }}" class="text-primary-600 font-semibold hover:text-primary-700">Sign in</a>
    </p>
</x-guest-layout>
