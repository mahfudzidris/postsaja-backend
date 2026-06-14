<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-bold text-text-main">💬 WhatsApp Status</h2>
            <p class="text-sm text-muted">Auto-post gambar ke WhatsApp Status</p>
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
                <h3 class="font-bold text-text-main mb-2">📖 Cara Setup WhatsApp Status</h3>
                <ol class="list-decimal list-inside text-sm text-muted space-y-1">
                    <li>Daftar dekat <a href="https://www.360dialog.io" target="_blank" class="text-primary-600 hover:underline">360Dialog</a> / <a href="https://www.wati.io" target="_blank" class="text-primary-600 hover:underline">WATI</a> / <a href="https://www.twilio.com" target="_blank" class="text-primary-600 hover:underline">Twilio</a></li>
                    <li>Dapatkan API Key & Phone Number ID daripada provider</li>
                    <li>Masukkan details dekat bawah untuk connect</li>
                    <li>Siap — auto-post ke WhatsApp Status bila staff upload gambar</li>
                </ol>
            </div>

            @foreach($businesses as $business)
                @php
                    $waConfig = json_decode($business->ig_token ?? '{}', true);
                    $isConnected = isset($waConfig['provider']);
                @endphp

                <div class="bg-white border border-border rounded-xl p-6 mb-4">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-bold text-text-main">{{ $business->business_name }}</h3>
                            <p class="text-sm text-muted">Code: <code class="bg-primary-50 px-1.5 py-0.5 rounded font-mono text-primary-600">{{ $business->business_code }}</code></p>
                        </div>
                        @if($isConnected)
                            <span class="inline-flex items-center px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-full text-sm font-medium">
                                ✅ {{ $waConfig['provider'] }}
                            </span>
                        @endif
                    </div>

                    @if($isConnected)
                        <div class="border-t border-border pt-4 space-y-1">
                            <p class="text-sm text-muted">Provider: <span class="font-medium text-text-main">{{ $waConfig['provider'] }}</span></p>
                            <p class="text-sm text-muted">Phone ID: <span class="font-medium text-text-main">{{ $waConfig['phone_number_id'] }}</span></p>
                            <p class="text-sm text-emerald-600">✅ Auto-post aktif</p>
                            <form method="POST" action="{{ route('whatsapp.disconnect') }}" class="mt-2">
                                @csrf
                                <input type="hidden" name="business_id" value="{{ $business->id }}">
                                <button type="submit" class="text-sm text-danger-500 hover:text-red-700">Disconnect</button>
                            </form>
                        </div>
                    @else
                        <form method="POST" action="{{ route('whatsapp.connect') }}" class="border-t border-border pt-4 space-y-3">
                            @csrf
                            <input type="hidden" name="business_id" value="{{ $business->id }}">

                            <div>
                                <label class="block text-sm font-medium text-text-main">Provider</label>
                                <select name="provider" class="mt-1 block w-full rounded-xl border-border focus:border-primary-500 focus:ring-primary-500">
                                    <option value="360dialog">360Dialog</option>
                                    <option value="wati">WATI</option>
                                    <option value="twilio">Twilio</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-text-main">API Key</label>
                                <input type="text" name="api_key" class="mt-1 block w-full rounded-xl border-border focus:border-primary-500 focus:ring-primary-500" placeholder="API Key / Token">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-text-main">Phone Number ID</label>
                                <input type="text" name="phone_number_id" class="mt-1 block w-full rounded-xl border-border focus:border-primary-500 focus:ring-primary-500" placeholder="e.g. 60123456789">
                            </div>

                            <button type="submit" class="px-6 py-2.5 bg-gradient-brand text-white font-semibold rounded-xl hover:opacity-90 transition text-sm">
                                🔗 Connect WhatsApp
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach

            @if($businesses->isEmpty())
                <div class="bg-white border border-border rounded-xl p-6 text-center">
                    <p class="text-3xl mb-2">💬</p>
                    <p class="text-muted">Belum ada business. Daftar dulu.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
