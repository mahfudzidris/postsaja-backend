<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📝 All Posts
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if($posts->isEmpty())
                    <p class="text-gray-500">Belum ada posts. Suruh staff hantar gambar ke @PostSajaBot!</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">#</th>
                                    <th class="text-left py-2">Image</th>
                                    <th class="text-left py-2">Business</th>
                                    <th class="text-left py-2">AI Caption</th>
                                    <th class="text-left py-2">Status</th>
                                    <th class="text-left py-2">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($posts as $post)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2">{{ $post->id }}</td>
                                    <td class="py-2">
                                        @if($post->image_url)
                                            <img src="{{ $post->image_url }}" class="w-16 h-16 object-cover rounded" alt="Post">
                                        @else
                                            <span class="text-gray-400">─</span>
                                        @endif
                                    </td>
                                    <td class="py-2">{{ $post->business?->business_name ?? '─' }}</td>
                                    <td class="py-2 max-w-xs truncate">{{ $post->ai_caption }}</td>
                                    <td class="py-2">
                                        <span class="px-2 py-1 rounded text-xs
                                            {{ $post->status === 'posted' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $post->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $post->status === 'processing' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                            {{ $post->status }}
                                        </span>
                                    </td>
                                    <td class="py-2">{{ $post->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
