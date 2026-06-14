<?php

namespace App\Http\Controllers;

use App\Models\PostsajaBusiness;
use App\Models\PostsajaSocialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GoogleBusinessController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $businesses = $user->ownedBusinesses()->get();

        return view('google-business.index', compact('businesses'));
    }

    /**
     * Redirect to Google OAuth
     */
    public function redirect(Request $request)
    {
        $businessId = $request->query('business_id');
        if (!$businessId) {
            return back()->with('error', 'Sila pilih business dahulu.');
        }

        $clientId = config('services.google.client_id');
        $redirectUri = route('google-business.callback');

        $params = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/business.manage',
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => $businessId,
        ]);

        return redirect('https://accounts.google.com/o/oauth2/auth?' . $params);
    }

    /**
     * Handle OAuth callback
     */
    public function callback(Request $request)
    {
        $code = $request->query('code');
        $businessId = $request->query('state');

        if (!$code || !$businessId) {
            return redirect()->route('google-business')
                ->with('error', 'Authorization failed. Cuba lagi.');
        }

        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirectUri = route('google-business.callback');

        try {
            $response = $this->httpPost('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ]);

            $data = json_decode($response, true);

            if (!isset($data['access_token'])) {
                Log::error('Google OAuth failed', ['response' => $response]);
                return redirect()->route('google-business')
                    ->with('error', 'Gagal dapatkan access token. Cuba lagi.');
            }

            // Store token in social_accounts table
            PostsajaSocialAccount::updateOrCreate(
                [
                    'business_id' => $businessId,
                    'platform' => 'google_business',
                ],
                [
                    'label' => 'Google Business',
                    'token' => json_encode([
                        'access_token' => $data['access_token'],
                        'refresh_token' => $data['refresh_token'] ?? null,
                        'expires_at' => now()->addSeconds($data['expires_in'] ?? 3600),
                    ]),
                    'active' => true,
                ]
            );

            return redirect()->route('google-business')
                ->with('success', '✅ Google Business berjaya dipautkan!');

        } catch (\Exception $e) {
            Log::error('Google OAuth error: ' . $e->getMessage());
            return redirect()->route('google-business')
                ->with('error', 'Ralat: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect Google Business
     */
    public function disconnect(Request $request)
    {
        $businessId = $request->input('business_id');

        PostsajaSocialAccount::where('business_id', $businessId)
            ->where('platform', 'google_business')
            ->delete();

        return back()->with('success', 'Google Business diputuskan.');
    }

    private function httpPost(string $url, array $data): string
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query($data),
            ],
        ]);

        $result = @file_get_contents($url, false, $context);
        if ($result === false) {
            throw new \RuntimeException('HTTP request failed');
        }

        return $result;
    }
}
