<?php

namespace App\Http\Controllers;

use App\Models\PostsajaPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $businessIds = $user->businesses()->pluck('id');

        $posts = PostsajaPost::whereIn('business_id', $businessIds)
            ->with('business')
            ->latest()
            ->paginate(20);

        return view('posts.index', compact('posts'));
    }

    /**
     * Approve a pending post (supervisor action)
     */
    public function approve(Request $request, PostsajaPost $post)
    {
        $user = Auth::user();

        // Check if user is supervisor for this business
        $isSupervisor = $user->businesses()
            ->wherePivot('role', 'supervisor')
            ->where('id', $post->business_id)
            ->exists();

        $isOwner = $user->businesses()
            ->wherePivot('role', 'owner')
            ->where('id', $post->business_id)
            ->exists();

        if (!$isSupervisor && !$isOwner) {
            return back()->with('error', 'You are not authorized to approve posts.');
        }

        $post->update([
            'status' => 'posted',
            'approved_by' => $user->id,
        ]);

        return back()->with('success', '✅ Post approved and published!');
    }

    /**
     * Reject a pending post
     */
    public function reject(Request $request, PostsajaPost $post)
    {
        $user = Auth::user();

        $isSupervisor = $user->businesses()
            ->wherePivot('role', 'supervisor')
            ->where('id', $post->business_id)
            ->exists();

        $isOwner = $user->businesses()
            ->wherePivot('role', 'owner')
            ->where('id', $post->business_id)
            ->exists();

        if (!$isSupervisor && !$isOwner) {
            return back()->with('error', 'Not authorized.');
        }

        $post->update(['status' => 'failed']);

        return back()->with('success', 'Post rejected.');
    }
}
