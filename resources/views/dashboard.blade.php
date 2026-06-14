<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Business</div>
                    <div class="mt-1 text-3xl font-bold">{{ $businesses->count() }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Total Posts</div>
                    <div class="mt-1 text-3xl font-bold">{{ $totalPosts }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Hari Ini</div>
                    <div class="mt-1 text-3xl font-bold">{{ $todayPosts }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Staff</div>
                    <div class="mt-1 text-3xl font-bold">{{ $totalStaff }}</div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold mb-4">⚡ Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('google-business') }}" class="block p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                        <div class="font-medium">📰 Google Business</div>
                        <div class="text-sm text-gray-600">Connect & manage</div>
                    </a>
                    <a href="{{ route('whatsapp') }}" class="block p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                        <div class="font-medium">💬 WhatsApp Status</div>
                        <div class="text-sm text-gray-600">Connect & auto-post</div>
                    </a>
                    <a href="#" class="block p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                        <div class="font-medium">📸 @PostSajaBot</div>
                        <div class="text-sm text-gray-600">Staff upload via Telegram</div>
                    </a>
                </div>
            </div>

            {{-- Recent Posts --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">📝 Recent Posts</h3>

                @if($recentPosts->isEmpty())
                    <p class="text-gray-500">Belum ada posts. Suruh staff hantar gambar ke @PostSajaBot!</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">Image</th>
                                    <th class="text-left py-2">Caption</th>
                                    <th class="text-left py-2">Status</th>
                                    <th class="text-left py-2">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPosts as $post)
                                <tr class="border-b">
                                    <td class="py-2">
                                        @if($post->image_url)
                                            <img src="{{ $post->image_url }}" class="w-16 h-16 object-cover rounded" alt="Post image">
                                        @else
                                            <span class="text-gray-400">No image</span>
                                        @endif
                                    </td>
                                    <td class="py-2 max-w-xs truncate">{{ $post->ai_caption }}</td>
                                    <td class="py-2">
                                        <span class="px-2 py-1 rounded text-xs {{ $post->status === 'posted' ? 'bg-green-100 text-green-800' : ($post->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ $post->status }}
                                        </span>
                                    </td>
                                    <td class="py-2">{{ $post->created_at->diffForHumans() }}</td>
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
