<x-guest-layout>
    <h3 class="text-lg font-bold text-text-main mb-1">Daftar Akaun</h3>
    <p class="text-sm text-muted mb-6">Buat akaun untuk mula guna PostSaja</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" value="Nama" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirm Password" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full py-2.5 bg-gradient-brand text-white font-semibold rounded-xl hover:opacity-90 transition">
                Daftar
            </button>
        </div>

        <p class="text-sm text-muted text-center mt-4">
            Dah ada akaun?
            <a href="{{ route('login') }}" class="text-primary-600 hover:underline">Log masuk</a>
        </p>
    </form>
</x-guest-layout>
