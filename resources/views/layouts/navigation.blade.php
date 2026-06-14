<nav x-data="{ open: false }" class="bg-white border-b border-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            {{-- Left: Logo + Nav --}}
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="text-xl font-extrabold tracking-tight mr-8">
                    <span class="bg-gradient-brand bg-clip-text text-transparent">PostSaja</span>
                </a>

                <div class="hidden sm:flex sm:space-x-1">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        📊 Dashboard
                    </x-nav-link>
                    <x-nav-link :href="route('posts.index')" :active="request()->routeIs('posts.*')">
                        📝 Posts
                    </x-nav-link>
                    <x-nav-link :href="route('google-business')" :active="request()->routeIs('google-business*')">
                        📰 Google
                    </x-nav-link>
                    <x-nav-link :href="route('whatsapp')" :active="request()->routeIs('whatsapp*')">
                        💬 WhatsApp
                    </x-nav-link>
                </div>
            </div>

            {{-- Right: User dropdown --}}
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm font-medium text-muted bg-white rounded-lg hover:text-text-main hover:bg-primary-50 transition border border-transparent focus:outline-none">
                            <div>{{ Auth::user()->name }}</div>
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profile
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Hamburger --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="p-2 rounded-md text-muted hover:text-text-main hover:bg-primary-50 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile nav --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-border">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                📊 Dashboard
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('posts.index')" :active="request()->routeIs('posts.*')">
                📝 Posts
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('google-business')" :active="request()->routeIs('google-business*')">
                📰 Google Business
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('whatsapp')" :active="request()->routeIs('whatsapp*')">
                💬 WhatsApp
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-3 border-t border-border">
            <div class="px-4">
                <div class="font-semibold text-sm text-text-main">{{ Auth::user()->name }}</div>
                <div class="text-sm text-muted">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
