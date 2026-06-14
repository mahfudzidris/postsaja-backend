<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AICaptionService
{
    /**
     * Generate a marketing caption from an image URL.
     */
    public function generate(string $imageUrl, string $businessName, ?string $userHint = null): array
    {
        $provider = config('ai.provider', 'claude');
        $apiKey = config('ai.api_key');
        $model = config('ai.model', 'claude-sonnet-4-20250514');

        if (empty($apiKey)) {
            return $this->fallback($businessName, $userHint, 'AI API key not configured');
        }

        try {
            return match ($provider) {
                'claude' => $this->callClaude($apiKey, $model, $imageUrl, $businessName, $userHint),
                'openai' => $this->callOpenAI($apiKey, $model, $imageUrl, $businessName, $userHint),
                default => $this->fallback($businessName, $userHint, "Unknown provider: $provider"),
            };
        } catch (\Exception $e) {
            Log::error('AI caption generation failed', [
                'provider' => $provider,
                'error' => $e->getMessage(),
                'business' => $businessName,
            ]);
            return $this->fallback($businessName, $userHint, 'AI unavailable');
        }
    }

    /**
     * Call Anthropic Claude Messages API
     */
    private function callClaude(string $apiKey, string $model, string $imageUrl, string $businessName, ?string $userHint): array
    {
        // Build system prompt
        $systemPrompt = "Anda adalah AI Marketing Assistant untuk perniagaan Malaysia bernama {$businessName}.
Tugas anda: lihat gambar yang dihantar, analisis apa yang berlaku, dan hasilkan caption marketing yang autentik.

FORMAT OUTPUT (JSON sahaja, jangan tambah apa-apa lain):
{
  \"caption\": \"caption di sini\",
  \"hashtags\": \"#tag1 #tag2 #tag3\",
  \"platforms\": [\"google_business\", \"facebook\", \"instagram\"]
}

GUIDELINES:
- Caption dalam Bahasa Malaysia, boleh campur English sikit
- Natural dan autentik, jangan terlalu salesy
- 2-3 ayat sahaja
- Hashtags 5-7 tag relevan (localised: #SME #Malaysia #nama_bisnes)
- Platforms: pilih ikut kesesuaian gambar";

        if ($userHint) {
            $systemPrompt .= "\n\nPetunjuk dari staff: {$userHint}";
        }

        $payload = [
            'model' => $model,
            'max_tokens' => config('ai.max_tokens', 300),
            'system' => $systemPrompt,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'image',
                            'source' => [
                                'type' => 'url',
                                'url' => $imageUrl,
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => 'Analisis gambar ni dan hasilkan caption marketing.',
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->post('https://api.anthropic.com/v1/messages', $payload, [
            'x-api-key: ' . $apiKey,
            'anthropic-version: 2023-06-01',
        ]);

        $data = json_decode($response, true);
        $text = $data['content'][0]['text'] ?? '';
        
        // Parse JSON from response
        $parsed = $this->parseJsonFromText($text);

        return [
            'success' => true,
            'caption' => $parsed['caption'] ?? $text,
            'hashtags' => $parsed['hashtags'] ?? '#SME #Malaysia',
            'platforms' => $parsed['platforms'] ?? ['google_business', 'facebook', 'instagram'],
        ];
    }

    /**
     * Call OpenAI GPT-4o Chat Completions API
     */
    private function callOpenAI(string $apiKey, string $model, string $imageUrl, string $businessName, ?string $userHint): array
    {
        $prompt = "Anda adalah AI Marketing Assistant untuk perniagaan Malaysia bernama {$businessName}.
Lihat gambar ini dan hasilkan caption marketing dalam Bahasa Malaysia (campur English sikit).
Natural, autentik, 2-3 ayat. Sediakan juga hashtag (5-7 tag)." . ($userHint ? "\nPetunjuk staff: {$userHint}" : '');

        $payload = [
            'model' => $model ?: 'gpt-4o',
            'max_tokens' => config('ai.max_tokens', 300),
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'image_url',
                            'image_url' => ['url' => $imageUrl],
                        ],
                        [
                            'type' => 'text',
                            'text' => $prompt,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->post('https://api.openai.com/v1/chat/completions', $payload, [
            'Authorization: Bearer ' . $apiKey,
        ]);

        $data = json_decode($response, true);
        $text = $data['choices'][0]['message']['content'] ?? '';

        $hashtags = $this->extractHashtags($text);
        $caption = trim(preg_replace('/#\S+/', '', $text));

        return [
            'success' => true,
            'caption' => $caption,
            'hashtags' => $hashtags,
            'platforms' => ['google_business', 'facebook', 'instagram'],
        ];
    }

    /**
     * Fallback when AI is unavailable
     */
    private function fallback(string $businessName, ?string $userHint, string $reason): array
    {
        $subject = $userHint ?: 'Servis kenderaan';

        return [
            'success' => false,
            'fallback_reason' => $reason,
            'caption' => "Servis berkualiti dari {$businessName}. Kepuasan pelanggan keutamaan kami.",
            'hashtags' => "#servis #berkualiti #{$businessName} #SME #Malaysia",
            'platforms' => ['google_business', 'facebook', 'instagram'],
        ];
    }

    /**
     * HTTP POST helper
     */
    private function post(string $url, array $payload, array $headers): string
    {
        $json = json_encode($payload);
        $allHeaders = array_merge($headers, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json),
        ]);

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $allHeaders),
                'content' => $json,
                'timeout' => 30,
                'ignore_errors' => true,
            ],
        ]);

        $result = @file_get_contents($url, false, $context);
        if ($result === false) {
            throw new \RuntimeException('HTTP request failed to: ' . $url);
        }

        // Check for HTTP errors
        $httpCode = 0;
        if (isset($http_response_header[0])) {
            preg_match('/\d{3}/', $http_response_header[0], $matches);
            $httpCode = (int) ($matches[0] ?? 0);
        }

        if ($httpCode >= 400) {
            Log::warning('AI provider returned error', [
                'http_code' => $httpCode,
                'response' => substr($result, 0, 500),
            ]);
        }

        return $result;
    }

    /**
     * Parse JSON from AI response (handles markdown code blocks)
     */
    private function parseJsonFromText(string $text): array
    {
        // Try extracting JSON from markdown code blocks first
        if (preg_match('/```(?:json)?\s*({[\s\S]*?})\s*```/', $text, $matches)) {
            $parsed = json_decode($matches[1], true);
            if ($parsed) return $parsed;
        }

        // Try parsing entire response as JSON
        $parsed = json_decode($text, true);
        if ($parsed) return $parsed;

        // Try finding JSON object anywhere in text
        if (preg_match('/{[\s\S]*?}/', $text, $matches)) {
            $parsed = json_decode($matches[0], true);
            if ($parsed) return $parsed;
        }

        return [];
    }

    /**
     * Extract hashtags from text
     */
    private function extractHashtags(string $text): string
    {
        preg_match_all('/#\w+/', $text, $matches);
        $tags = array_unique($matches[0] ?? []);
        return implode(' ', $tags) ?: '#SME #Malaysia';
    }
}
