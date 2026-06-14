<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-text-main">Delete Account</h2>
        <p class="mt-1 text-sm text-muted">Setelah akaun dipadam, semua data akan hilang secara kekal</p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete Account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-text-main">Are you sure?</h2>
            <p class="mt-1 text-sm text-muted">Sahkan dengan masukkan password</p>

            <div class="mt-6">
                <x-input-label for="password" value="Password" class="sr-only" />
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="Password"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-danger-button class="ms-3">Delete Account</x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
