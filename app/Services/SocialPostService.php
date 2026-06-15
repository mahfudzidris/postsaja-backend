<?php

namespace App\Services;

use App\Models\SocialAccount;
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia as SocialMediaFacade;
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;
use Illuminate\Support\Facades\Log;

class SocialPostService
{
    /**
     * Map our internal channel identifiers to the package platform names.
     */
    protected array $channelMap = [
        'instagram' => 'instagram',
        'tiktok'     => 'tiktok',
        'telegram'   => 'telegram',
        'facebook'   => 'facebook',
        'twitter'    => 'twitter',
        'linkedin'   => 'linkedin',
        'youtube'    => 'youtube',
        'pinterest'  => 'pinterest',
    ];

    /**
     * The Google My Business service instance (optional).
     */
    protected ?GoogleMyBusinessService $gmbService = null;

    public function __construct(?GoogleMyBusinessService $gmbService = null)
    {
        $this->gmbService = $gmbService;
    }

    /**
     * Post to selected channels.
     *
     * @param string $caption
     * @param array  $media      Array of media URLs (first one used as primary)
     * @param array  $channels   Array of internal channel IDs
     * @param int    $userId     The user posting
     * @return array  Per-platform results
     */
    public function post(string $caption, array $media, array $channels, int $userId): array
    {
        $results = [];

        foreach ($channels as $channel) {
            $channel = trim($channel);
            $platform = $this->channelMap[$channel] ?? null;

            if ($channel === 'google_business') {
                $results[$channel] = $this->postToGoogleBusiness($caption, $media, $userId);
                continue;
            }

            if (! $platform) {
                $results[$channel] = [
                    'success' => false,
                    'error'   => "Platform '{$channel}' is not supported or coming soon.",
                ];
                continue;
            }

            try {
                $socialAccount = SocialAccount::where('user_id', $userId)
                    ->where('platform', $channel)
                    ->where('active', true)
                    ->first();

                if (! $socialAccount) {
                    $results[$channel] = [
                        'success' => false,
                        'error'   => "No active {$channel} account connected.",
                    ];
                    continue;
                }

                // Use the package's withCredentials to pass dynamic tokens
                $credentials = $this->buildCredentials($platform, $socialAccount);
                $manager = SocialMediaFacade::withCredentials($credentials);
                $firstMedia = $media[0] ?? null;

                if ($firstMedia) {
                    // Attempt to detect from URL extension
                    $extension = pathinfo(parse_url($firstMedia, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION);

                    if (in_array($extension, ['mp4', 'mov', 'avi', 'webm', 'mkv'])) {
                        $result = $manager->shareVideo([$platform], $caption, $firstMedia);
                    } else {
                        $result = $manager->shareImage([$platform], $caption, $firstMedia);
                    }
                } else {
                    $result = $manager->share([$platform], $caption, '');
                }

                $platformResult = $result['results'][$platform] ?? [];
                $results[$channel] = [
                    'success' => $platformResult['success'] ?? false,
                    'data'    => $platformResult,
                    'error'   => $result['errors'][$platform] ?? null,
                ];
            } catch (SocialMediaException $e) {
                Log::error("SocialPostService: {$channel} posting failed", [
                    'user_id' => $userId,
                    'error'   => $e->getMessage(),
                ]);

                $results[$channel] = [
                    'success' => false,
                    'error'   => $e->getMessage(),
                ];
            } catch (\Exception $e) {
                Log::error("SocialPostService: {$channel} unexpected error", [
                    'user_id' => $userId,
                    'error'   => $e->getMessage(),
                ]);

                $results[$channel] = [
                    'success' => false,
                    'error'   => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Build the credentials array for the SocialMediaManager::withCredentials method.
     */
    protected function buildCredentials(string $platform, SocialAccount $account): array
    {
        $meta = $account->meta ?? [];
        $token = $account->token;

        return match ($platform) {
            'telegram' => [
                'telegram' => [
                    'telegram_bot_token' => $token,
                    'chat_id'            => $meta['chat_id'] ?? $meta['telegram_chat_id'] ?? '',
                ],
            ],
            'instagram' => [
                'instagram' => [
                    'access_token'        => $token,
                    'instagram_account_id'=> $meta['instagram_account_id'] ?? '',
                    'facebook_page_id'    => $meta['facebook_page_id'] ?? '',
                ],
            ],
            'tiktok' => [
                'tiktok' => [
                    'access_token'  => $token,
                    'client_key'    => $meta['client_key'] ?? '',
                    'client_secret' => $meta['client_secret'] ?? '',
                ],
            ],
            'facebook' => [
                'facebook' => [
                    'access_token' => $token,
                    'page_id'      => $meta['page_id'] ?? '',
                ],
            ],
            default => [
                $platform => [
                    'access_token' => $token,
                ],
            ],
        };
    }

    /**
     * Post to Google Business Profile using our custom service.
     */
    protected function postToGoogleBusiness(string $caption, array $media, int $userId): array
    {
        try {
            $account = SocialAccount::where('user_id', $userId)
                ->where('platform', 'google_business')
                ->where('active', true)
                ->first();

            if (! $account) {
                return [
                    'success' => false,
                    'error'   => 'No Google Business account connected.',
                ];
            }

            if (! $this->gmbService) {
                $this->gmbService = app(GoogleMyBusinessService::class);
            }

            $meta = $account->meta ?? [];
            $locationId = $meta['location_id'] ?? '';

            if (! $locationId) {
                return [
                    'success' => false,
                    'error'   => 'No Google Business location configured.',
                ];
            }

            $result = $this->gmbService->postUpdate(
                $account->token,
                $locationId,
                $caption,
                $media[0] ?? null
            );

            return [
                'success' => true,
                'data'    => $result,
            ];
        } catch (\Exception $e) {
            Log::error("SocialPostService: google_business posting failed", [
                'user_id' => $userId,
                'error'   => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }
}
