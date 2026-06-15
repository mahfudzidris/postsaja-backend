<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $plans = Plan::where('active', true)
            ->orderBy('price')
            ->get()
            ->toArray();

        // Fallback: if no plans in DB, return hardcoded defaults
        if (empty($plans)) {
            $plans = [
                [
                    'id' => 1,
                    'name' => 'Free',
                    'code' => 'free',
                    'price' => 0,
                    'currency' => 'MYR',
                    'max_channels' => 2,
                    'max_posts_per_month' => 10,
                    'features' => [
                        'Up to 10 posts per month',
                        'Up to 2 social channels',
                        'Basic analytics',
                    ],
                    'active' => true,
                ],
                [
                    'id' => 2,
                    'name' => 'Basic',
                    'code' => 'basic',
                    'price' => 29,
                    'currency' => 'MYR',
                    'max_channels' => 3,
                    'max_posts_per_month' => 50,
                    'features' => [
                        'Up to 50 posts per month',
                        'Up to 3 social channels',
                        'Advanced analytics',
                        'Schedule posts',
                    ],
                    'active' => true,
                ],
                [
                    'id' => 3,
                    'name' => 'Pro',
                    'code' => 'pro',
                    'price' => 49,
                    'currency' => 'MYR',
                    'max_channels' => 10,
                    'max_posts_per_month' => 9999,
                    'features' => [
                        'Unlimited posts',
                        'Up to 10 social channels',
                        'Advanced analytics',
                        'Schedule posts',
                        'Priority support',
                        'Team collaboration (coming soon)',
                    ],
                    'active' => true,
                ],
            ];
        }

        return response()->json($plans);
    }

    public function current(Request $request): JsonResponse
    {
        $user = $request->user();

        $plan = $user->currentPlan();

        return response()->json([
            'plan' => $plan ? [
                'id' => $plan->id,
                'name' => $plan->name,
                'code' => $plan->code,
                'price' => $plan->price,
                'currency' => $plan->currency,
                'max_channels' => $plan->max_channels,
                'max_posts_per_month' => $plan->max_posts_per_month,
            ] : [
                'name' => 'Free',
                'code' => 'free',
                'max_channels' => 2,
                'max_posts_per_month' => 10,
            ],
            'limits' => $user->planLimits(),
        ]);
    }
}
