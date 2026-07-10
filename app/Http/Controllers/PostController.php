<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostLike;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Feed - list public posts.
     */
    public function index(Request $request): JsonResponse
    {
        $posts = Post::where('is_public', true)
            ->with('user:id,name,username,avatar')
            ->withCount('likes', 'comments')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($posts);
    }

    /**
     * List posts by authenticated user.
     */
    public function myPosts(Request $request): JsonResponse
    {
        $posts = Post::where('user_id', $request->user()->id)
            ->with('user:id,name,username,avatar')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($posts);
    }

    /**
     * Create a new post.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'content'     => 'required|string|max:2000',
            'type'        => 'nullable|in:post,activity,route',
            'activity_id' => 'nullable|exists:activities,id',
            'is_public'   => 'nullable|boolean',
            'images.*'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('posts', 'public');
            }
        }

        $post = Post::create([
            'user_id'     => $request->user()->id,
            'content'     => $validated['content'],
            'type'        => $validated['type'] ?? 'post',
            'activity_id' => $validated['activity_id'] ?? null,
            'is_public'   => $validated['is_public'] ?? true,
            'images'      => $imagePaths ?: null,
        ]);

        return response()->json([
            'message' => 'Posting berhasil dibuat.',
            'post'    => $post->load('user:id,name,username,avatar'),
        ], 201);
    }

    /**
     * Show a single post.
     */
    public function show(Request $request, Post $post): JsonResponse
    {
        if (!$post->is_public && $post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Posting ini bersifat privat.'], 403);
        }

        $isLiked = PostLike::where('user_id', $request->user()->id)
            ->where('post_id', $post->id)
            ->exists();

        return response()->json([
            'post'     => $post->load([
                'user:id,name,username,avatar',
                'activity:id,title,type,distance,duration',
                'comments.user:id,name,username,avatar',
                'comments.replies.user:id,name,username,avatar',
            ]),
            'is_liked' => $isLiked,
        ]);
    }

    /**
     * Delete a post.
     */
    public function destroy(Request $request, Post $post): JsonResponse
    {
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        // Hapus gambar jika ada
        if ($post->images) {
            foreach ($post->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $post->delete();

        return response()->json(['message' => 'Posting berhasil dihapus.']);
    }

    /**
     * Like a post.
     */
    public function like(Request $request, Post $post): JsonResponse
    {
        $existing = PostLike::where('user_id', $request->user()->id)
            ->where('post_id', $post->id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Kamu sudah menyukai posting ini.'], 409);
        }

        PostLike::create([
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
        ]);

        $post->increment('likes_count');

        return response()->json([
            'message'     => 'Posting disukai.',
            'likes_count' => $post->fresh()->likes_count,
        ]);
    }

    /**
     * Unlike a post.
     */
    public function unlike(Request $request, Post $post): JsonResponse
    {
        $deleted = PostLike::where('user_id', $request->user()->id)
            ->where('post_id', $post->id)
            ->delete();

        if ($deleted) {
            $post->decrement('likes_count');
        }

        return response()->json([
            'message'     => 'Like dibatalkan.',
            'likes_count' => $post->fresh()->likes_count,
        ]);
    }
}
