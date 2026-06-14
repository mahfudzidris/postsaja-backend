<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📰 Google Business
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
            @endif

            @foreach($businesses as $business)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $business->business_name }}</h3>
                            <p class="text-sm text-gray-500">Code: {{ $business->business_code }}</p>
                        </div>
                        <div class="text-right">
                            @if($business->google_business_token)
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">✅ Connected</span>
                                <form method="POST" action="{{ route('google-business.disconnect') }}" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="business_id" value="{{ $business->id }}">
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">Disconnect</button>
                                </form>
                            @else
                                <a href="{{ route('google-business.connect', ['business_id' => $business->id]) }}"
                                   class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    🔗 Connect Google Business
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            @if($businesses->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-500">Belum ada business. Register business dulu.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
