<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramSetWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $secret = config('telegram.bots.mybot.webhook_secret');
        if ($secret && $request->header('X-Secret') !== $secret) {
            abort(401);
        }

        $url = config('app.url') . '/api/telegram/webhook';

        try {
            $response = Telegram::setWebhook([
                'url' => $url,
                'drop_pending_updates' => true,
                'allowed_updates' => ['message'],
            ]);

            Log::info('Webhook set', ['url' => $url, 'response' => $response]);

            return response()->json([
                'ok' => true,
                'url' => $url,
                'response' => $response,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to set webhook: ' . $e->getMessage());

            return response()->json([
                'ok' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
