<?php

namespace App\Http\Controllers;

use App\Models\PostsajaPost;
use App\Models\PostsajaStaffTelegram;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get all businesses the user is associated with (any role)
        $businesses = $user->businesses()->with('staff')->get();

        $businessIds = $businesses->pluck('id');

        $totalPosts = PostsajaPost::whereIn('business_id', $businessIds)->count();
        $totalStaff = PostsajaStaffTelegram::whereIn('business_id', $businessIds)->count();
        $recentPosts = PostsajaPost::whereIn('business_id', $businessIds)
            ->with('business')
            ->latest()
            ->take(10)
            ->get();

        $todayPosts = PostsajaPost::whereIn('business_id', $businessIds)
            ->whereDate('created_at', today())
            ->count();

        // Count posts pending approval (if this user is a supervisor)
        $pendingApproval = 0;
        $supervisedIds = $user->businesses()->wherePivot('role', 'supervisor')->pluck('id');
        if ($supervisedIds->isNotEmpty()) {
            $pendingApproval = PostsajaPost::whereIn('business_id', $supervisedIds)
                ->where('status', 'pending')
                ->count();
        }

        return view('dashboard', compact(
            'businesses', 'totalPosts', 'totalStaff', 'recentPosts', 'todayPosts', 'pendingApproval'
        ));
    }
}
