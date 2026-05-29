<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email">{{ __('Email Address') }}</label>
            <input id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                placeholder="nama@perusahaan.com"
                autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password">{{ __('Password') }}</label>
            <input id="password"
                type="password"
                name="password"
                required
                placeholder="••••••••"
                autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" name="remember">
                <span class="ms-2 text-sm">{{ __('Ingat saya di perangkat ini') }}</span>
            </label>
        </div>

        <!-- Login Button and Forgot Password -->
        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">
                    {{ __('Lupa password Anda?') }}
                </a>
            @endif

            <button type="submit">
                {{ __('Masuk Ke Sistem') }}
            </button>
        </div>
    </form>
</x-guest-layout>
