<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h3 class="text-lg font-bold text-text-main mb-1">Log Masuk</h3>
    <p class="text-sm text-muted mb-6">Masuk semula untuk uruskan bisnes</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-border text-primary-500 focus:ring-primary-500" name="remember">
                <span class="ml-2 text-sm text-muted">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-primary-600 hover:underline" href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full py-2.5 bg-gradient-brand text-white font-semibold rounded-xl hover:opacity-90 transition">
                Log Masuk
            </button>
        </div>

        <p class="text-sm text-muted text-center mt-4">
            Belum daftar?
            <a href="{{ route('register') }}" class="text-primary-600 hover:underline">Daftar sini</a>
        </p>
    </form>
</x-guest-layout>
