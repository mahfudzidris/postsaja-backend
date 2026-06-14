<?php

namespace App\Http\Controllers;

use App\Models\PostsajaBusiness;
use App\Models\PostsajaStaffTelegram;
use App\Models\PostsajaPost;
use App\Services\AICaptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $update = $request->all();

        if (!isset($update['message']['chat']['id'])) {
            return response('', 200);
        }

        $chatId = $update['message']['chat']['id'];
        $text = trim($update['message']['text'] ?? '');
        $photo = $update['message']['photo'] ?? null;
        $caption = trim($update['message']['caption'] ?? '');
        $username = $update['message']['from']['username'] ?? '';

        try {
            if (str_starts_with($text, '/start')) {
                $this->handleStart($chatId);
                return response('', 200);
            }

            if ($text && !$photo) {
                $this->handleCode($chatId, $text, $username);
                return response('', 200);
            }

            if ($photo) {
                $this->handlePhoto($chatId, $photo, $caption);
                return response('', 200);
            }

            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ Maaf, saya tak faham.\n\n📸 *Hantar gambar* → AI auto-post\n🔑 *Hantar Business Code* → Pautkan akaun\n/start → Lihat panduan",
                'parse_mode' => 'Markdown',
            ]);

        } catch (\Exception $e) {
            Log::error('PostSaja webhook error: ' . $e->getMessage(), [
                'chat_id' => $chatId,
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return response('', 200);
    }

    private function handleStart(int $chatId): void
    {
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "👋 *Salam! Saya PostSaja Bot.*\n\n"
                . "Saya AI Marketing Assistant yang akan auto-post gambar bisnes awak ke:\n"
                . "📰 Google Business · 📘 Facebook · 📷 Instagram · 💬 WhatsApp Status\n\n"
                . "📌 *Staff:* Hantar gambar, saya uruskan posting.\n"
                . "📌 *Owner:* Dapat ringkasan harian.\n\n"
                . "🔑 Dah ada akaun? Hantar *Business Code* 6 digit yang owner bagi.\n"
                . "❌ Belum daftar? Minta owner daftar di postsaja.com dulu.",
            'parse_mode' => 'Markdown',
        ]);
    }

    private function handleCode(int $chatId, string $code, string $username): void
    {
        // Secret seed command — only works for admin chat
        if (strtoupper($code) === 'SEEDDB' && $chatId === 40540268) {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => '✅ Database seeded!',
            ]);
            return;
        }

        $business = PostsajaBusiness::where('business_code', strtoupper($code))->first();

        if (!$business) {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ *Business Code* tak sah. Sila semak semula dengan owner.\n\nAtau daftar dulu di postsaja.com",
                'parse_mode' => 'Markdown',
            ]);
            return;
        }

        PostsajaStaffTelegram::updateOrCreate(
            ['telegram_chat_id' => $chatId],
            [
                'business_id' => $business->id,
                'telegram_username' => $username,
                'role' => 'staff',
            ]
        );

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "✅ *Siap!* Akaun anda dah dipautkan ke *{$business->business_name}*.\n\n"
                . "Sekarang hantar gambar bila-bila — AI saya akan:\n"
                . "1️⃣ Analyze gambar\n"
                . "2️⃣ Generate caption + hashtags\n"
                . "3️⃣ Hantar untuk approval supervisor\n"
                . "4️⃣ Auto-post lepas approve!\n\n"
                . "📸 *Cuba hantar gambar sekarang!*",
            'parse_mode' => 'Markdown',
        ]);
    }

    private function handlePhoto(int $chatId, array $photos, string $caption): void
    {
        $fileId = end($photos)['file_id'];

        $response = Telegram::getFile(['file_id' => $fileId]);
        $filePath = $response->getFilePath();
        $token = config('telegram.bots.mybot.token');
        $fullUrl = "https://api.telegram.org/file/bot{$token}/{$filePath}";

        // Get staff business
        $staff = PostsajaStaffTelegram::where('telegram_chat_id', $chatId)
            ->where('active', true)
            ->with('business')
            ->first();

        $businessName = $staff?->business?->business_name ?? 'Business anda';
        $businessId = $staff?->business_id;

        // Acknowledge
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "📸 *Gambar diterima!*\n\nAI sedang menganalisis gambar untuk *{$businessName}*...",
            'parse_mode' => 'Markdown',
        ]);

        // ─── Generate AI Caption ───
        $ai = app(AICaptionService::class);
        $result = $ai->generate($fullUrl, $businessName, $caption ?: null);

        $aiCaption = $result['caption'];
        $hashtags = $result['hashtags'];
        $aiLabel = $result['success'] ? '🤖 *AI Caption:*' : '📝 *Caption:*';

        // ─── Check if approval needed ───
        $needsApproval = false;
        if ($businessId) {
            $hasSupervisor = \App\Models\PostsajaBusiness::find($businessId)
                ?->supervisors()->exists() ?? false;
            $needsApproval = $hasSupervisor;
        }

        $initialStatus = $needsApproval ? 'pending' : 'processing';
        $statusText = $needsApproval
            ? "⏳ Menunggu approval supervisor..."
            : "🚀 Auto-post ke platform sosial!";

        $replyText = "✅ *Gambar diproses!*\n\n"
            . "{$aiLabel}\n"
            . "\"{$aiCaption}\"\n\n"
            . $hashtags . "\n\n"
            . "📤 *Status:* {$statusText}";

        Telegram::sendPhoto([
            'chat_id' => $chatId,
            'photo' => $fullUrl,
            'caption' => $replyText,
            'parse_mode' => 'Markdown',
        ]);

        // ─── Log post ───
        if ($businessId) {
            $post = PostsajaPost::create([
                'business_id' => $businessId,
                'staff_chat_id' => $chatId,
                'image_url' => $fullUrl,
                'ai_caption' => $aiCaption,
                'status' => $initialStatus,
            ]);

            // Auto-post only if no approval needed
            if (!$needsApproval) {
                try {
                    WhatsAppController::sendStatusUpdate($businessId, $fullUrl, $aiCaption);
                    $post->update(['status' => 'posted']);
                } catch (\Exception $e) {
                    Log::warning('WhatsApp auto-post failed (maybe not connected)', [
                        'business_id' => $businessId,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }
}
