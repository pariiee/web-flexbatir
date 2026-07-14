<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PostCommentController extends Controller
{
    /**
     * List comments for a post.
     */
    public function index(Post $post): JsonResponse
    {
        $comments = PostComment::where('post_id', $post->id)
            ->whereNull('parent_id')
            ->with([
                'user:id,name,username,avatar',
                'replies.user:id,name,username,avatar',
            ])
            ->orderBy('created_at')
            ->paginate(20);

        return response()->json($comments);
    }

    /**
     * Add a comment to a post.
     */
    public function store(Request $request, Post $post): JsonResponse
    {
        $validated = $request->validate([
            'content'   => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:post_comments,id',
        ]);

        // Pastikan parent_id milik post yang sama
        if (!empty($validated['parent_id'])) {
            $parent = PostComment::find($validated['parent_id']);
            if ($parent->post_id !== $post->id) {
                return response()->json(['message' => 'Komentar induk tidak valid.'], 422);
            }
        }

        $comment = PostComment::create([
            'user_id'   => $request->user()->id,
            'post_id'   => $post->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'content'   => $validated['content'],
        ]);

        $post->increment('comments_count');

        return response()->json([
            'message' => 'Komentar berhasil ditambahkan.',
            'comment' => $comment->load('user:id,name,username,avatar'),
        ], 201);
    }

    /**
     * Delete a comment.
     */
    public function destroy(Request $request, PostComment $postComment): JsonResponse
    {
        if ((int) $postComment->user_id !== (int) $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        // Hitung berapa komentar yang akan hilang (1 + jumlah reply)
        $deleteCount = 1 + $postComment->replies()->count();

        $postComment->delete();

        // Kurangi comments_count sebanyak komentar yang dihapus
        Post::where('id', $postComment->post_id)
            ->decrement('comments_count', $deleteCount);

        return response()->json(['message' => 'Komentar berhasil dihapus.']);
    }
}
