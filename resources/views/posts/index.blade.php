<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-bold text-text-main">📝 All Posts</h2>
            <p class="text-sm text-muted">Semua gambar yang staff upload via Telegram</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-border rounded-xl p-6">

                @if($posts->isEmpty())
                    <div class="text-center py-8">
                        <p class="text-3xl mb-2">📸</p>
                        <p class="text-muted">Belum ada posts. Suruh staff hantar gambar ke @PostSajaBot!</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-border">
                                    <th class="text-left py-3 text-muted font-medium">#</th>
                                    <th class="text-left py-3 text-muted font-medium">Image</th>
                                    <th class="text-left py-3 text-muted font-medium">Business</th>
                                    <th class="text-left py-3 text-muted font-medium">AI Caption</th>
                                    <th class="text-left py-3 text-muted font-medium">Status</th>
                                    <th class="text-left py-3 text-muted font-medium">Date</th>
                                    <th class="text-left py-3 text-muted font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($posts as $post)
                                <tr class="border-b border-border hover:bg-primary-50">
                                    <td class="py-3 text-muted">{{ $post->id }}</td>
                                    <td class="py-3">
                                        @if($post->image_url)
                                            <img src="{{ $post->image_url }}" class="w-12 h-12 object-cover rounded-lg" alt="Post">
                                        @else
                                            <span class="text-muted">─</span>
                                        @endif
                                    </td>
                                    <td class="py-3 font-medium text-text-main">{{ $post->business?->business_name ?? '─' }}</td>
                                    <td class="py-3 max-w-xs truncate text-muted">{{ $post->ai_caption ?: '─' }}</td>
                                    <td class="py-3">
                                        @php
                                            $statusClasses = [
                                                'posted' => 'bg-emerald-50 text-emerald-700',
                                                'processing' => 'bg-amber-50 text-amber-700',
                                                'pending' => 'bg-amber-50 text-amber-600',
                                                'failed' => 'bg-red-50 text-red-700',
                                            ][$post->status] ?? 'bg-gray-50 text-gray-600';
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClasses }}">
                                            {{ $post->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-muted whitespace-nowrap">{{ $post->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-3">
                                        @if($post->status === 'pending')
                                            <form action="{{ route('posts.approve', $post) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-xs px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full font-medium hover:bg-emerald-100">
                                                    ✅ Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('posts.reject', $post) }}" method="POST" class="inline ml-1">
                                                @csrf
                                                <button type="submit" class="text-xs px-2.5 py-1 bg-red-50 text-red-700 rounded-full font-medium hover:bg-red-100">
                                                    ❌ Reject
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted text-xs">─</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
