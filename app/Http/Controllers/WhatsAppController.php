<?php

namespace App\Http\Controllers;

use App\Models\PostsajaBusiness;
use App\Models\PostsajaSocialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $businesses = $user->ownedBusinesses()->get();

        return view('whatsapp.index', compact('businesses'));
    }

    /**
     * Connect WhatsApp Business API (360Dialog / WATI / Twilio)
     */
    public function connect(Request $request)
    {
        $validated = $request->validate([
            'business_id' => 'required|exists:postsaja_businesses,id',
            'provider' => 'required|in:360dialog,wati,twilio',
            'api_key' => 'required|string',
            'phone_number_id' => 'required|string',
        ]);

        $business = PostsajaBusiness::findOrFail($validated['business_id']);

        // Validate the connection by making a test API call
        try {
            $testResult = $this->testConnection(
                $validated['provider'],
                $validated['api_key'],
                $validated['phone_number_id']
            );

            if (!$testResult) {
                return back()->with('error', 'Gagal verify connection. Check API key & Phone ID.');
            }

            // Store WhatsApp config in social_accounts table
            PostsajaSocialAccount::updateOrCreate(
                [
                    'business_id' => $validated['business_id'],
                    'platform' => 'whatsapp',
                ],
                [
                    'label' => 'WhatsApp Status',
                    'meta' => [
                        'provider' => $validated['provider'],
                        'api_key' => $validated['api_key'],
                        'phone_number_id' => $validated['phone_number_id'],
                        'connected_at' => now()->toIso8601String(),
                    ],
                    'active' => true,
                ]
            );

            return back()->with('success', '✅ WhatsApp Status berjaya dipautkan!');

        } catch (\Exception $e) {
            Log::error('WhatsApp connect error: ' . $e->getMessage());
            return back()->with('error', 'Ralat: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect WhatsApp
     */
    public function disconnect(Request $request)
    {
        $businessId = $request->input('business_id');

        PostsajaSocialAccount::where('business_id', $businessId)
            ->where('platform', 'whatsapp')
            ->delete();

        return back()->with('success', 'WhatsApp diputuskan.');
    }

    /**
     * Test WhatsApp provider connection
     */
    private function testConnection(string $provider, string $apiKey, string $phoneNumberId): bool
    {
        $url = match ($provider) {
            '360dialog' => "https://waba.360dialog.io/v1/configs/websocket",
            'wati' => "https://api.wati.io/api/v1/getMessageTemplates",
            'twilio' => "https://api.twilio.com/2010-04-01/Accounts/{$phoneNumberId}/Messages.json",
        };

        $headers = match ($provider) {
            '360dialog' => ['D360-API-KEY: ' . $apiKey],
            'wati' => ['Authorization: Bearer ' . $apiKey],
            'twilio' => ['Authorization: Basic ' . base64_encode($apiKey)],
        };

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => implode("\r\n", $headers),
                'timeout' => 10,
                'ignore_errors' => true,
            ],
        ]);

        $result = @file_get_contents($url, false, $context);
        return $result !== false;
    }

    /**
     * Send WhatsApp Status update
     * Called by the posting pipeline when a new post is created
     */
    public static function sendStatusUpdate(string $businessId, string $imageUrl, string $caption): bool
    {
        $whatsapp = PostsajaSocialAccount::where('business_id', $businessId)
            ->where('platform', 'whatsapp')
            ->where('active', true)
            ->first();

        if (!$whatsapp || !$whatsapp->meta) {
            return false;
        }

        $config = $whatsapp->meta;

        try {
            $provider = $config['provider'];
            $apiKey = $config['api_key'];
            $phoneId = $config['phone_number_id'];

            $payload = match ($provider) {
                '360dialog' => [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => 'status',
                    'type' => 'image',
                    'image' => ['link' => $imageUrl],
                ],
                'wati' => [
                    'phone' => 'status',
                    'imageUrl' => $imageUrl,
                    'caption' => $caption,
                ],
                'twilio' => [
                    'To' => 'status',
                    'MediaUrl' => $imageUrl,
                    'Body' => $caption,
                ],
            };

            $url = match ($provider) {
                '360dialog' => 'https://waba.360dialog.io/v1/messages',
                'wati' => 'https://api.wati.io/api/v1/sendSessionMessage/status',
                'twilio' => "https://api.twilio.com/2010-04-01/Accounts/{$phoneId}/Messages.json",
            };

            $headers = match ($provider) {
                '360dialog' => [
                    'D360-API-KEY: ' . $apiKey,
                    'Content-Type: application/json',
                ],
                'wati' => [
                    'Authorization: Bearer ' . $apiKey,
                    'Content-Type: application/json',
                ],
                'twilio' => [
                    'Authorization: Basic ' . base64_encode($apiKey),
                    'Content-Type: application/x-www-form-urlencoded',
                ],
            };

            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => implode("\r\n", $headers),
                    'content' => json_encode($payload),
                    'timeout' => 10,
                ],
            ]);

            $result = @file_get_contents($url, false, $context);
            return $result !== false;

        } catch (\Exception $e) {
            Log::error('WhatsApp status post failed', [
                'business' => $businessId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
