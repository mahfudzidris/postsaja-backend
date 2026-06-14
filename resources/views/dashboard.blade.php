<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-bold text-text-main">Dashboard</h2>
            <p class="text-sm text-muted">Ringkasan bisnes anda</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Stats Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white border border-border rounded-xl p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted">Business</p>
                            <p class="text-2xl font-bold text-text-main mt-1">{{ $businesses->count() }}</p>
                        </div>
                        <span class="text-2xl">🏢</span>
                    </div>
                </div>
                <div class="bg-white border border-border rounded-xl p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted">Total Posts</p>
                            <p class="text-2xl font-bold text-text-main mt-1">{{ $totalPosts }}</p>
                        </div>
                        <span class="text-2xl">📸</span>
                    </div>
                </div>
                <div class="bg-white border border-border rounded-xl p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted">Hari Ini</p>
                            <p class="text-2xl font-bold text-text-main mt-1">{{ $todayPosts }}</p>
                        </div>
                        <span class="text-2xl">🔥</span>
                    </div>
                </div>
                <div class="bg-white border border-border rounded-xl p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted">Staff</p>
                            <p class="text-2xl font-bold text-text-main mt-1">{{ $totalStaff }}</p>
                        </div>
                        <span class="text-2xl">👥</span>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white border border-border rounded-xl p-6 mb-8">
                <h3 class="font-bold text-text-main mb-4">⚡ Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('google-business') }}"
                       class="block p-4 bg-gradient-soft border border-primary-100 rounded-xl hover:shadow-sm transition">
                        <div class="font-semibold text-text-main">📰 Google Business</div>
                        <div class="text-sm text-muted mt-1">Connect & manage profil bisnes</div>
                    </a>
                    <a href="{{ route('whatsapp') }}"
                       class="block p-4 bg-gradient-soft border border-primary-100 rounded-xl hover:shadow-sm transition">
                        <div class="font-semibold text-text-main">💬 WhatsApp Status</div>
                        <div class="text-sm text-muted mt-1">Auto-post ke WhatsApp Status</div>
                    </a>
                    <a href="https://t.me/PostSajaBot" target="_blank"
                       class="block p-4 bg-gradient-soft border border-primary-100 rounded-xl hover:shadow-sm transition">
                        <div class="font-semibold text-text-main">📸 @PostSajaBot</div>
                        <div class="text-sm text-muted mt-1">Staff upload gambar via Telegram</div>
                    </a>
                </div>
            </div>

            {{-- Recent Posts --}}
            <div class="bg-white border border-border rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-text-main">📝 Recent Posts</h3>
                    <a href="{{ route('posts.index') }}" class="text-sm text-primary-600 hover:underline">View all →</a>
                </div>

                @if($recentPosts->isEmpty())
                    <div class="text-center py-8">
                        <p class="text-3xl mb-2">📸</p>
                        <p class="text-muted">Belum ada posts. Suruh staff hantar gambar ke @PostSajaBot!</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-border">
                                    <th class="text-left py-3 text-muted font-medium">Image</th>
                                    <th class="text-left py-3 text-muted font-medium">Caption</th>
                                    <th class="text-left py-3 text-muted font-medium">Status</th>
                                    <th class="text-left py-3 text-muted font-medium">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPosts as $post)
                                <tr class="border-b border-border hover:bg-primary-50">
                                    <td class="py-3">
                                        @if($post->image_url)
                                            <img src="{{ $post->image_url }}" class="w-12 h-12 object-cover rounded-lg" alt="Post">
                                        @else
                                            <span class="text-muted">─</span>
                                        @endif
                                    </td>
                                    <td class="py-3 max-w-xs truncate text-muted">{{ $post->ai_caption ?: '─' }}</td>
                                    <td class="py-3">
                                        @php
                                            $statusClasses = [
                                                'posted' => 'bg-emerald-50 text-emerald-700',
                                                'processing' => 'bg-amber-50 text-amber-700',
                                                'failed' => 'bg-red-50 text-red-700',
                                            ][$post->status] ?? 'bg-gray-50 text-gray-600';
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClasses }}">
                                            {{ $post->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-muted">{{ $post->created_at->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
