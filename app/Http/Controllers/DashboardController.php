<?php

namespace App\Http\Controllers;

use App\Models\PostsajaBusiness;
use App\Models\PostsajaPost;
use App\Models\PostsajaStaffTelegram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $businesses = PostsajaBusiness::where('owner_name', $user->name)
            ->orWhereHas('staff', fn($q) => $q->where('telegram_chat_id', $user->telegram_chat_id ?? 0))
            ->get();

        $totalPosts = PostsajaPost::whereIn('business_id', $businesses->pluck('id'))->count();
        $totalStaff = PostsajaStaffTelegram::whereIn('business_id', $businesses->pluck('id'))->count();
        $recentPosts = PostsajaPost::whereIn('business_id', $businesses->pluck('id'))
            ->latest()
            ->take(10)
            ->get();

        $todayPosts = PostsajaPost::whereIn('business_id', $businesses->pluck('id'))
            ->whereDate('created_at', today())
            ->count();

        return view('dashboard', compact(
            'businesses', 'totalPosts', 'totalStaff', 'recentPosts', 'todayPosts'
        ));
    }
}
