<?php

namespace App\Http\Controllers;

use App\Models\PostsajaPost;
use App\Models\PostsajaBusiness;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $businessIds = PostsajaBusiness::where('owner_name', $user->name)
            ->orWhereHas('staff', fn($q) => $q->where('telegram_chat_id', $user->telegram_chat_id ?? 0))
            ->pluck('id');

        $posts = PostsajaPost::whereIn('business_id', $businessIds)
            ->with('business')
            ->latest()
            ->paginate(20);

        return view('posts.index', compact('posts'));
    }
}
