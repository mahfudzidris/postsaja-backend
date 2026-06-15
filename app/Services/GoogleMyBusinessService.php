<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\MyBusinessAccountManagement;
use Google\Service\MyBusinessBusinessInformation;
use Google\Service\MyBusinessQA;
use Illuminate\Support\Facades\Log;

class GoogleMyBusinessService
{
    protected ?GoogleClient $client = null;

    /**
     * Initialize the Google client with the user's OAuth token.
     */
    protected function initClient(string $accessToken): GoogleClient
    {
        if ($this->client === null) {
            $client = new GoogleClient();
            $client->setApplicationName('PostSaja');
            $client->setScopes([
                'https://www.googleapis.com/auth/business.manage',
            ]);
            $client->setAccessToken($accessToken);

            // Handle token refresh if expired
            if ($client->isAccessTokenExpired()) {
                $refreshToken = $client->getRefreshToken();
                if ($refreshToken) {
                    $client->fetchAccessTokenWithRefreshToken($refreshToken);
                }
            }

            $this->client = $client;
        }

        return $this->client;
    }

    /**
     * Get the list of GMB locations for the authenticated user.
     *
     * @param string $accessToken
     * @return array
     */
    public function getLocations(string $accessToken): array
    {
        try {
            $client = $this->initClient($accessToken);
            $myBusiness = new MyBusinessBusinessInformation($client);

            // List accounts
            $accountsService = new MyBusinessAccountManagement($client);
            $accounts = $accountsService->accounts->listAccounts();

            $locations = [];

            foreach ($accounts->getAccounts() ?? [] as $account) {
                $accountName = $account->getName();

                try {
                    $accountLocations = $myBusiness->accounts_locations->listAccountsLocations($accountName);

                    foreach ($accountLocations->getLocations() ?? [] as $location) {
                        $locations[] = [
                            'name'        => $location->getName(),
                            'locationName' => $location->getLocationName(),
                            'title'       => $location->getTitle(),
                            'phoneNumbers' => $location->getPhoneNumbers()?->getPrimaryPhone() ?? null,
                            'address'     => $location->getAddress()?->getAddressLines() ?? [],
                        ];
                    }
                } catch (\Exception $e) {
                    Log::warning("GMB: Failed to fetch locations for account {$accountName}", [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return $locations;
        } catch (\Exception $e) {
            Log::error('GMB: Failed to get locations', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Create a post on a Google Business Profile location.
     *
     * @param string      $accessToken
     * @param string      $locationId  The full location name (e.g. accounts/{accountId}/locations/{locationId})
     * @param string      $text        The post body text
     * @param string|null $mediaUrl    Optional media URL to attach
     * @return array
     */
    public function postUpdate(string $accessToken, string $locationId, string $text, ?string $mediaUrl = null): array
    {
        try {
            $client = $this->initClient($accessToken);

            // GMB v4 API uses MyBusinessBusinessInformation for locations
            // For posting, we use the MyBusiness API via HTTP (no official v4 PHP lib for posts)
            // We'll use google/apiclient's HTTP capabilities

            $media = [];
            if ($mediaUrl) {
                $media[] = [
                    'mediaFormat' => $this->detectMediaFormat($mediaUrl),
                    'sourceUrl'   => $mediaUrl,
                ];
            }

            $postBody = [
                'summary'      => $text,
                'topicType'    => 'STANDARD',
                'callToAction' => null,
                'media'        => $media,
                'event'        => null,
                'alertType'    => null,
                'offer'        => null,
            ];

            // Use the Google Client's HTTP POST directly
            $httpClient = $client->authorize();
            $response = $httpClient->post(
                "https://mybusiness.googleapis.com/v4/{$locationId}/localPosts",
                [
                    'json' => $postBody,
                ]
            );

            $body = json_decode((string) $response->getBody(), true);

            Log::info('GMB: Post created successfully', [
                'locationId' => $locationId,
                'postId'     => $body['name'] ?? null,
            ]);

            return [
                'success' => true,
                'data'    => $body,
            ];
        } catch (\Google\Service\Exception $e) {
            Log::error('GMB: API error creating post', [
                'locationId' => $locationId,
                'error'      => $e->getMessage(),
            ]);

            throw $e;
        } catch (\Exception $e) {
            Log::error('GMB: Failed to post update', [
                'locationId' => $locationId,
                'error'      => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Verify the Google OAuth token is still valid.
     *
     * @param string $accessToken
     * @return bool
     */
    public function verifyToken(string $accessToken): bool
    {
        try {
            $client = new GoogleClient();
            $client->setScopes(['https://www.googleapis.com/auth/business.manage']);
            $client->setAccessToken($accessToken);

            $tokenInfo = $client->verifyIdToken()->verifyIdToken($accessToken);

            return ! $client->isAccessTokenExpired();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Detect the media format from a URL.
     */
    protected function detectMediaFormat(string $url): string
    {
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION));

        return in_array($extension, ['mp4', 'mov', 'avi', 'webm'])
            ? 'VIDEO'
            : 'PHOTO';
    }
}
