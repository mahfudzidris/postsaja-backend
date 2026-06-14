<x-guest-layout>
    <h3 class="text-lg font-bold text-text-main mb-1">Lupa Password?</h3>
    <p class="text-sm text-muted mb-6">Masukkan email. Kami hantar link reset password.</p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full py-2.5 bg-gradient-brand text-white font-semibold rounded-xl hover:opacity-90 transition">
                Hantar Reset Link
            </button>
        </div>

        <p class="text-sm text-muted text-center mt-4">
            <a href="{{ route('login') }}" class="text-primary-600 hover:underline">Kembali ke login</a>
        </p>
    </form>
</x-guest-layout>
