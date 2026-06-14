<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-bold text-text-main">📰 Google Business</h2>
            <p class="text-sm text-muted">Pautkan Google Business Profile untuk auto-post</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-6 text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm">{{ session('error') }}</div>
            @endif

            {{-- Guide card --}}
            <div class="bg-white border border-border rounded-xl p-6 mb-6">
                <h3 class="font-bold text-text-main mb-2">📖 Cara Setup Google Business</h3>
                <ol class="list-decimal list-inside text-sm text-muted space-y-1">
                    <li>Klik <strong>"Connect Google Business"</strong> untuk business yang nak dipautkan</li>
                    <li>Log masuk Google Account yang ada business profile</li>
                    <li>Beri kebenaran — kami akan post gambar & caption secara auto</li>
                    <li>Selesai. Setiap gambar staff upload akan auto-post ke Google Business</li>
                </ol>
            </div>

            @foreach($businesses as $business)
                <div class="bg-white border border-border rounded-xl p-6 mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-text-main">{{ $business->business_name }}</h3>
                            <p class="text-sm text-muted">Code: <code class="bg-primary-50 px-1.5 py-0.5 rounded font-mono text-primary-600">{{ $business->business_code }}</code></p>
                        </div>
                        <div class="text-right">
                            @if($business->google_business_token)
                                <span class="inline-flex items-center px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-full text-sm font-medium">
                                    ✅ Connected
                                </span>
                                <form method="POST" action="{{ route('google-business.disconnect') }}" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="business_id" value="{{ $business->id }}">
                                    <button type="submit" class="text-sm text-danger-500 hover:text-red-700">Disconnect</button>
                                </form>
                            @else
                                <a href="{{ route('google-business.connect', ['business_id' => $business->id]) }}"
                                   class="inline-flex items-center px-4 py-2 bg-gradient-brand text-white font-semibold rounded-xl hover:opacity-90 transition text-sm">
                                    🔗 Connect Google Business
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            @if($businesses->isEmpty())
                <div class="bg-white border border-border rounded-xl p-6 text-center">
                    <p class="text-3xl mb-2">🏢</p>
                    <p class="text-muted">Belum ada business. Daftar business dulu dari dashboard.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
