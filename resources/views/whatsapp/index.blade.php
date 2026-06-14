<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            💬 WhatsApp Status
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-2">📖 Cara Setup</h3>
                <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1">
                    <li>Daftar dekat <a href="https://www.360dialog.io" target="_blank" class="text-blue-600">360Dialog</a> / <a href="https://www.wati.io" target="_blank" class="text-blue-600">WATI</a> / <a href="https://www.twilio.com" target="_blank" class="text-blue-600">Twilio</a></li>
                    <li>Dapatkan API Key & Phone Number ID</li>
                    <li>Masukkan dekat bawah untuk connect</li>
                    <li>Siap — auto-post ke WhatsApp Status bila staff upload gambar</li>
                </ol>
            </div>

            @foreach($businesses as $business)
                @php
                    $waConfig = json_decode($business->ig_token ?? '{}', true);
                    $isConnected = isset($waConfig['provider']);
                @endphp

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-4">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $business->business_name }}</h3>
                            <p class="text-sm text-gray-500">Code: {{ $business->business_code }}</p>
                        </div>
                        @if($isConnected)
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">✅ Connected ({{ $waConfig['provider'] }})</span>
                        @endif
                    </div>

                    @if($isConnected)
                        <div class="border-t pt-4">
                            <p class="text-sm text-gray-600">Provider: <strong>{{ $waConfig['provider'] }}</strong></p>
                            <p class="text-sm text-gray-600">Phone ID: <strong>{{ $waConfig['phone_number_id'] }}</strong></p>
                            <form method="POST" action="{{ route('whatsapp.disconnect') }}" class="mt-2">
                                @csrf
                                <input type="hidden" name="business_id" value="{{ $business->id }}">
                                <button type="submit" class="text-sm text-red-600 hover:text-red-800">Disconnect</button>
                            </form>
                        </div>
                    @else
                        <form method="POST" action="{{ route('whatsapp.connect') }}" class="border-t pt-4 space-y-3">
                            @csrf
                            <input type="hidden" name="business_id" value="{{ $business->id }}">

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Provider</label>
                                <select name="provider" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                                    <option value="360dialog">360Dialog</option>
                                    <option value="wati">WATI</option>
                                    <option value="twilio">Twilio</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">API Key</label>
                                <input type="text" name="api_key" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200" placeholder="API Key / Token">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone Number ID</label>
                                <input type="text" name="phone_number_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200" placeholder="e.g. 60123456789">
                            </div>

                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                🔗 Connect WhatsApp
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach

            @if($businesses->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-500">Belum ada business. Register dulu.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
