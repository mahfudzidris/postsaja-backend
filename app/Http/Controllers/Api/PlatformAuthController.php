<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Services\GoogleMyBusinessService;
use Google\Client as GoogleClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class PlatformAuthController extends Controller
{
    /**
     * List all available platforms and user's connected platforms.
     */
    public function index(Request $request): JsonResponse
    {
        $availablePlatforms = [
            [
                'id'          => 'telegram',
                'name'        => 'Telegram',
                'description' => 'Post to Telegram channels or groups',
                'auth_type'   => 'api_key',
                'available'   => true,
                'icon'        => 'telegram',
            ],
            [
                'id'          => 'google_business',
                'name'        => 'Google Business Profile',
                'description' => 'Post updates to your Google Business Profile',
                'auth_type'   => 'oauth',
                'available'   => true,
                'icon'        => 'google',
            ],
            [
                'id'          => 'instagram',
                'name'        => 'Instagram',
                'description' => 'Post images and videos to Instagram',
                'auth_type'   => 'api_key',
                'available'   => true,
                'icon'        => 'instagram',
            ],
            [
                'id'          => 'tiktok',
                'name'        => 'TikTok',
                'description' => 'Post videos to TikTok',
                'auth_type'   => 'api_key',
                'available'   => true,
                'icon'        => 'tiktok',
            ],
            [
                'id'          => 'facebook',
                'name'        => 'Facebook',
                'description' => 'Post to Facebook pages',
                'auth_type'   => 'api_key',
                'available'   => true,
                'icon'        => 'facebook',
            ],
            [
                'id'          => 'twitter',
                'name'        => 'Twitter / X',
                'description' => 'Post tweets to Twitter/X',
                'auth_type'   => 'oauth',
                'available'   => false,
                'coming_soon' => true,
                'icon'        => 'twitter',
            ],
        ];

        $connected = $request->user()
            ->socialAccounts()
            ->where('active', true)
            ->get(['platform', 'provider_user_id', 'meta', 'updated_at'])
            ->keyBy('platform')
            ->toArray();

        $platforms = array_map(function ($platform) use ($connected) {
            $platform['connected'] = isset($connected[$platform['id']]);
            $platform['connection'] = $connected[$platform['id']] ?? null;
            return $platform;
        }, $availablePlatforms);

        return response()->json([
            'platforms' => $platforms,
        ]);
    }

    /**
     * Initiate OAuth flow or return connection URL for a platform.
     */
    public function connect(Request $request, string $platform): JsonResponse
    {
        $validPlatforms = ['telegram', 'google_business', 'instagram', 'tiktok', 'facebook', 'twitter'];

        if (! in_array($platform, $validPlatforms)) {
            return response()->json(['message' => 'Invalid platform.'], 422);
        }

        // For Telegram: just return a message asking for the API key
        if ($platform === 'telegram') {
            return response()->json([
                'message' => 'Telegram uses an API key. Send a POST request with your bot token and chat ID.',
                'auth_type' => 'api_key',
                'requires' => [
                    'bot_token' => 'Your Telegram Bot Token from @BotFather',
                    'chat_id'   => 'The chat/channel ID to post to',
                ],
            ]);
        }

        // For Google Business: return OAuth redirect URL
        if ($platform === 'google_business') {
            $client = new GoogleClient();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setRedirectUri(route('platform.callback', ['platform' => 'google_business']));
            $client->setScopes(['https://www.googleapis.com/auth/business.manage']);
            $client->setAccessType('offline');
            $client->setPrompt('consent');

            $authUrl = $client->createAuthUrl();

            // Store the state in the session for CSRF protection
            $request->session()->put('google_oauth_state', $client->getState());

            return response()->json([
                'auth_type' => 'oauth',
                'redirect_url' => $authUrl,
            ]);
        }

        // For Facebook / Instagram / TikTok: generate OAuth URL if credentials configured
        $clientId = config("services.{$platform}.client_id");
        $clientSecret = config("services.{$platform}.client_secret");

        if ($clientId && $clientSecret) {
            $redirectUri = route('platform.callback', ['platform' => $platform]);
            $scopes = $this->getOAuthScopes($platform);
            $state = bin2hex(random_bytes(16));

            $request->session()->put("oauth_state_{$platform}", $state);

            // Facebook OAuth handles both Facebook & Instagram
            $authUrl = "https://www.facebook.com/v22.0/dialog/oauth?"
                . http_build_query([
                    'client_id'    => $clientId,
                    'redirect_uri' => $redirectUri,
                    'state'        => $state,
                    'scope'        => implode(',', $scopes),
                    'response_type'=> 'code',
                ]);

            return response()->json([
                'auth_type'   => 'oauth',
                'redirect_url' => $authUrl,
            ]);
        }

        // Fallback: manual token entry (for testing when no OAuth credentials)
        return response()->json([
            'message'   => "Enter your {$platform} access token.",
            'auth_type' => 'api_key',
            'requires'  => [
                'access_token' => "Your {$platform} API access token",
                'meta'         => 'Optional metadata (page_id, account_id, etc.)',
            ],
        ]);
    }

    /**
     * Handle OAuth callback for a platform.
     * For Telegram, store the API key directly.
     */
    protected function getOAuthScopes(string $platform): array
    {
        return match ($platform) {
            'facebook' => ['pages_manage_posts', 'pages_read_engagement', 'pages_show_list'],
            'instagram' => ['instagram_basic', 'instagram_content_publish', 'pages_show_list', 'pages_manage_posts'],
            'tiktok' => ['user.info.basic', 'video.publish', 'video.upload'],
            'twitter' => ['tweet.read', 'tweet.write', 'users.read'],
            'google_business' => ['https://www.googleapis.com/auth/business.manage'],
            default => [],
        };
    }

    public function callback(Request $request, string $platform): JsonResponse
    {
        // ── Telegram: store bot token + chat_id ──
        if ($platform === 'telegram') {
            $validated = $request->validate([
                'bot_token' => 'required|string',
                'chat_id'   => 'required|string',
            ]);

            // Verify the token by calling getMe
            $response = Http::get("https://api.telegram.org/bot{$validated['bot_token']}/getMe");

            if (! $response->successful()) {
                throw ValidationException::withMessages([
                    'bot_token' => ['Invalid Telegram bot token. Please check and try again.'],
                ]);
            }

            $botInfo = $response->json('result');

            $socialAccount = SocialAccount::updateOrCreate(
                [
                    'user_id'  => $request->user()->id,
                    'platform' => 'telegram',
                ],
                [
                    'provider_user_id' => (string) ($botInfo['id'] ?? $validated['bot_token']),
                    'token'            => $validated['bot_token'],
                    'meta'             => [
                        'chat_id'          => $validated['chat_id'],
                        'bot_username'     => $botInfo['username'] ?? null,
                        'bot_name'         => $botInfo['first_name'] ?? null,
                    ],
                    'active'           => true,
                ]
            );

            return response()->json([
                'message'  => 'Telegram connected successfully.',
                'platform' => $socialAccount,
            ]);
        }

        // ── Google Business: handle OAuth callback ──
        if ($platform === 'google_business') {
            $validated = $request->validate([
                'code' => 'required|string',
            ]);

            try {
                $client = new GoogleClient();
                $client->setClientId(config('services.google.client_id'));
                $client->setClientSecret(config('services.google.client_secret'));
                $client->setRedirectUri(route('platform.callback', ['platform' => 'google_business']));

                $token = $client->fetchAccessTokenWithAuthCode($validated['code']);

                if (isset($token['error'])) {
                    return response()->json([
                        'message' => 'Failed to authenticate with Google: ' . ($token['error_description'] ?? $token['error']),
                    ], 422);
                }

                // Get the user's Google account email
                $oauth2 = new \Google\Service\Oauth2($client);
                $userInfo = $oauth2->userinfo->get();

                // Try to get GMB locations
                $gmbService = app(GoogleMyBusinessService::class);
                $locations = $gmbService->getLocations($token['access_token']);

                $socialAccount = SocialAccount::updateOrCreate(
                    [
                        'user_id'  => $request->user()->id,
                        'platform' => 'google_business',
                    ],
                    [
                        'provider_user_id' => $userInfo->getId(),
                        'token'            => $token['access_token'],
                        'meta'             => [
                            'refresh_token'     => $token['refresh_token'] ?? null,
                            'expires_in'        => $token['expires_in'] ?? null,
                            'email'             => $userInfo->getEmail(),
                            'name'              => $userInfo->getName(),
                            'locations'         => $locations,
                            'location_id'       => $locations[0]['name'] ?? null,
                        ],
                        'active'           => true,
                    ]
                );

                return response()->json([
                    'message'  => 'Google Business Profile connected successfully.',
                    'platform' => $socialAccount,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Google authentication failed: ' . $e->getMessage(),
                ], 422);
            }
        }

        // For Facebook / Instagram / TikTok: handle OAuth code or manual token
        if (in_array($platform, ['facebook', 'instagram', 'tiktok', 'twitter'])) {
            $code = $request->input('code');
            $accessToken = $request->input('access_token');

            // If no code or access_token provided, return error
            if (!$code && !$accessToken) {
                return response()->json(['message' => 'OAuth code or access_token is required.'], 422);
            }

            // Manual token entry (for testing)
            if ($accessToken) {
                $meta = $request->input('meta', []);
                $socialAccount = SocialAccount::updateOrCreate(
                    ['user_id' => $request->user()->id, 'platform' => $platform],
                    [
                        'token'            => $accessToken,
                        'provider_user_id' => $request->user()->email,
                        'meta'             => $meta,
                        'active'           => true,
                    ]
                );
                return response()->json([
                    'message'  => "{$platform} connected successfully.",
                    'platform' => $socialAccount,
                ]);
            }

            // OAuth code exchange via Facebook Graph API
            $clientId = config("services.{$platform}.client_id");
            $clientSecret = config("services.{$platform}.client_secret");

            if (!$clientId || !$clientSecret) {
                return response()->json(['message' => "{$platform} OAuth not configured. Use access_token manually."], 422);
            }

            $redirectUri = route('platform.callback', ['platform' => $platform]);

            $tokenUrl = "https://graph.facebook.com/v22.0/oauth/access_token?"
                . http_build_query([
                    'client_id'     => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect_uri'  => $redirectUri,
                    'code'          => $code,
                ]);

            $fbResponse = \Illuminate\Support\Facades\Http::get($tokenUrl);

            if (!$fbResponse->successful()) {
                return response()->json([
                    'message' => 'Failed to exchange OAuth code: ' . ($fbResponse->json('error.message') ?? 'Unknown error'),
                ], 422);
            }

            $tokenData = $fbResponse->json();
            $finalToken = $tokenData['access_token'] ?? null;

            if (!$finalToken) {
                return response()->json(['message' => 'No access token received.'], 422);
            }

            // Exchange for long-lived token
            $longUrl = "https://graph.facebook.com/v22.0/oauth/access_token?"
                . http_build_query([
                    'grant_type'        => 'fb_exchange_token',
                    'client_id'         => $clientId,
                    'client_secret'     => $clientSecret,
                    'fb_exchange_token' => $finalToken,
                ]);

            $longResponse = \Illuminate\Support\Facades\Http::get($longUrl);
            if ($longResponse->successful()) {
                $longData = $longResponse->json();
                $finalToken = $longData['access_token'] ?? $finalToken;
            }

            // Get user's pages for Facebook
            $pages = [];
            $pagesUrl = "https://graph.facebook.com/v22.0/me/accounts?access_token={$finalToken}";
            $pagesResponse = \Illuminate\Support\Facades\Http::get($pagesUrl);
            if ($pagesResponse->successful()) {
                $pages = $pagesResponse->json('data') ?? [];
            }

            // Get user profile
            $profileUrl = "https://graph.facebook.com/v22.0/me?fields=id,name,email&access_token={$finalToken}";
            $profileResponse = \Illuminate\Support\Facades\Http::get($profileUrl);
            $profile = $profileResponse->successful() ? $profileResponse->json() : [];

            $socialAccount = SocialAccount::updateOrCreate(
                ['user_id' => $request->user()->id, 'platform' => $platform],
                [
                    'token'            => $finalToken,
                    'provider_user_id' => $profile['id'] ?? $request->user()->email,
                    'meta'             => [
                        'profile'      => $profile,
                        'pages'        => $pages,
                        'page_id'      => $pages[0]['id'] ?? null,
                        'page_name'    => $pages[0]['name'] ?? null,
                        'page_token'   => $pages[0]['access_token'] ?? null,
                        'expires_in'   => $tokenData['expires_in'] ?? null,
                    ],
                    'active'           => true,
                ]
            );

            // For OAuth popup callbacks, return HTML that closes the popup
            if ($request->wantsJson()) {
                return response()->json([
                    'message'  => "{$platform} connected successfully.",
                    'platform' => $socialAccount,
                ]);
            }

            // Return HTML page that closes popup or redirects back to app
            $appUrl = config('app.frontend_url', 'http://localhost:8081');
            $platformName = ucfirst($platform);

            return response("
                <html>
                <head><title>Connected</title></head>
                <body>
                    <script>
                        try {
                            if (window.opener) {
                                window.opener.postMessage({type: 'social_connected', platform: '{$platform}'}, '{$appUrl}');
                                window.close();
                            } else {
                                window.location.href = '{$appUrl}/settings';
                            }
                        } catch(e) {
                            window.location.href = '{$appUrl}/settings';
                        }
                    </script>
                    <p>{$platformName} connected! You can close this window.</p>
                </body>
                </html>
            ")->header('Content-Type', 'text/html');
        }

        return response()->json(['message' => "{$platform} callback not implemented yet."], 501);
    }

    /**
     * Disconnect a platform.
     */
    public function disconnect(Request $request, string $platform): JsonResponse
    {
        $deleted = $request->user()
            ->socialAccounts()
            ->where('platform', $platform)
            ->delete();

        if ($deleted === 0) {
            return response()->json(['message' => "No {$platform} connection found."], 404);
        }

        return response()->json(['message' => "{$platform} disconnected successfully."]);
    }

    /**
     * Verify a platform token is still valid.
     */
    public function verify(Request $request, string $platform): JsonResponse
    {
        $account = $request->user()
            ->socialAccounts()
            ->where('platform', $platform)
            ->where('active', true)
            ->first();

        if (! $account) {
            return response()->json([
                'valid'  => false,
                'message' => "No active {$platform} connection found.",
            ], 404);
        }

        $valid = false;
        $message = '';

        switch ($platform) {
            case 'telegram':
                $response = Http::get("https://api.telegram.org/bot{$account->token}/getMe");
                $valid = $response->successful();
                $message = $valid ? 'Token is valid.' : 'Token is invalid or expired.';
                break;

            case 'google_business':
                try {
                    $gmbService = app(GoogleMyBusinessService::class);
                    $valid = $gmbService->verifyToken($account->token);
                    $message = $valid ? 'Token is valid.' : 'Token is expired. Please reconnect.';
                } catch (\Exception $e) {
                    $valid = false;
                    $message = 'Token verification failed: ' . $e->getMessage();
                }
                break;

            default:
                $message = "Verification for {$platform} is not yet implemented.";
                break;
        }

        // Update active status if invalid
        if (! $valid) {
            $account->update(['active' => false]);
        }

        return response()->json([
            'valid'   => $valid,
            'message' => $message,
            'platform' => $account,
        ]);
    }
}
