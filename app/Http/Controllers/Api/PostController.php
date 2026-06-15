<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Post;
use App\Services\SocialPostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected SocialPostService $socialPostService;

    public function __construct(SocialPostService $socialPostService)
    {
        $this->socialPostService = $socialPostService;
    }

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
            'status' => 'sometimes|string|in:draft,published',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $status = $validated['status'] ?? 'published';

        $post = $request->user()->posts()->create([
            'caption' => $validated['caption'],
            'media' => $validated['media'] ?? null,
            'channels' => $validated['channels'] ?? null,
            'status' => $validated['scheduled_at'] ?? false ? 'scheduled' : $status,
            'scheduled_at' => $validated['scheduled_at'] ?? null,
        ]);

        // If published immediately, post to social channels
        if ($post->status === 'published' && ! empty($post->channels)) {
            $results = $this->socialPostService->post(
                $post->caption,
                $post->media ?? [],
                $post->channels,
                $request->user()->id
            );

            // Store analytics per channel
            $post->update(['analytics' => $results]);

            // Create notification summarizing the results
            $successCount = count(array_filter($results, fn ($r) => $r['success'] ?? false));
            $totalChannels = count($post->channels);

            if ($successCount === $totalChannels) {
                $request->user()->notifications()->create([
                    'type' => 'post_published',
                    'title' => 'Post published successfully',
                    'body' => 'Your post has been published to all selected channels.',
                    'data' => ['post_id' => $post->id, 'results' => $results],
                ]);
            } elseif ($successCount > 0) {
                $request->user()->notifications()->create([
                    'type' => 'post_partial',
                    'title' => 'Post partially published',
                    'body' => "Published to {$successCount} of {$totalChannels} channels. Some platforms had errors.",
                    'data' => ['post_id' => $post->id, 'results' => $results],
                ]);
            } else {
                $request->user()->notifications()->create([
                    'type' => 'post_failed',
                    'title' => 'Post failed',
                    'body' => 'Your post could not be published to any channels.',
                    'data' => ['post_id' => $post->id, 'results' => $results],
                ]);
            }
        } elseif ($post->status === 'published') {
            // No channels — just notify
            $request->user()->notifications()->create([
                'type' => 'post_published',
                'title' => 'Post saved successfully',
                'body' => 'Your post has been saved. No channels were selected to post to.',
            ]);
        }

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
            'status' => 'sometimes|string|in:draft,published',
            'scheduled_at' => 'nullable|date',
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

        $post->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Post to social channels
        $results = [];
        if (! empty($post->channels)) {
            $results = $this->socialPostService->post(
                $post->caption,
                $post->media ?? [],
                $post->channels,
                $request->user()->id
            );

            $post->update(['analytics' => $results]);
        }

        $successCount = count(array_filter($results, fn ($r) => $r['success'] ?? false));
        $totalChannels = count($post->channels);

        if ($successCount === $totalChannels && $totalChannels > 0) {
            $request->user()->notifications()->create([
                'type' => 'post_published',
                'title' => 'Post published successfully',
                'body' => 'Your draft has been published to all selected channels.',
                'data' => ['post_id' => $post->id, 'results' => $results],
            ]);
        } elseif ($successCount > 0) {
            $request->user()->notifications()->create([
                'type' => 'post_partial',
                'title' => 'Post partially published',
                'body' => "Published to {$successCount} of {$totalChannels} channels. Some platforms had errors.",
                'data' => ['post_id' => $post->id, 'results' => $results],
            ]);
        } else {
            $request->user()->notifications()->create([
                'type' => 'post_published',
                'title' => 'Draft published',
                'body' => 'Your draft has been published successfully.',
                'data' => ['post_id' => $post->id, 'results' => $results],
            ]);
        }

        return response()->json($post);
    }
}
