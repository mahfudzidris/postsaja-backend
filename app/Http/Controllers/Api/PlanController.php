<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $plans = [
            [
                'id' => 'free',
                'name' => 'Free',
                'price' => 0,
                'features' => [
                    'Up to 10 posts per month',
                    '1 social channel',
                    'Basic analytics',
                ],
            ],
            [
                'id' => 'pro',
                'name' => 'Pro',
                'price' => 29,
                'features' => [
                    'Unlimited posts',
                    'Unlimited social channels',
                    'Advanced analytics',
                    'Schedule posts',
                    'Priority support',
                ],
            ],
            [
                'id' => 'business',
                'name' => 'Business',
                'price' => 99,
                'features' => [
                    'Everything in Pro',
                    'Team collaboration',
                    'API access',
                    'Custom branding',
                    'Dedicated account manager',
                ],
            ],
        ];

        return response()->json($plans);
    }

    public function current(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'plan' => $user->plan,
        ]);
    }
}
