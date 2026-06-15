<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $posts = $request->user()
            ->posts()
            ->where('status', '!=', 'draft')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($posts);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'caption' => 'required|string',
            'media' => 'nullable|array',
            'media.*' => 'string',
            'channels' => 'nullable|array',
            'channels.*' => 'string',
        ]);

        $post = $request->user()->posts()->create([
            'caption' => $validated['caption'],
            'media' => $validated['media'] ?? null,
            'channels' => $validated['channels'] ?? null,
            'status' => 'published',
        ]);

        // Create notification
        $request->user()->notifications()->create([
            'type' => 'post_published',
            'title' => 'Post published successfully',
            'body' => 'Your post has been published successfully.',
        ]);

        return response()->json($post, 201);
    }

    public function drafts(Request $request): JsonResponse
    {
        $drafts = $request->user()
            ->posts()
            ->where('status', 'draft')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($drafts);
    }

    public function show(Request $request, Post $post): JsonResponse
    {
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json($post);
    }

    public function update(Request $request, Post $post): JsonResponse
    {
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'caption' => 'sometimes|string',
            'media' => 'nullable|array',
            'media.*' => 'string',
            'channels' => 'nullable|array',
            'channels.*' => 'string',
        ]);

        $post->update($validated);

        return response()->json($post);
    }

    public function destroy(Request $request, Post $post): JsonResponse
    {
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }

    public function publish(Request $request, Post $post): JsonResponse
    {
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($post->status !== 'draft') {
            return response()->json(['message' => 'Only draft posts can be published'], 422);
        }

        $post->update(['status' => 'published']);

        $request->user()->notifications()->create([
            'type' => 'post_published',
            'title' => 'Post published successfully',
            'body' => 'Your draft has been published successfully.',
        ]);

        return response()->json($post);
    }
}
