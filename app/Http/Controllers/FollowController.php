<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FollowController extends Controller
{
    /**
     * Follow user berdasarkan ID.
     */
    public function follow(Request $request, User $user): JsonResponse
    {
        $followerId = $request->user()->id;

        if ($followerId === $user->id) {
            return response()->json(['message' => 'Cannot follow yourself'], 422);
        }

        Follow::firstOrCreate([
            'follower_id'  => $followerId,
            'following_id' => $user->id,
        ]);

        return response()->json([
            'message'         => 'Followed',
            'followers_count' => $user->followers()->count(),
            'is_following'    => true,
        ]);
    }

    /**
     * Unfollow user berdasarkan ID.
     */
    public function unfollow(Request $request, User $user): JsonResponse
    {
        Follow::where([
            'follower_id'  => $request->user()->id,
            'following_id' => $user->id,
        ])->delete();

        return response()->json([
            'message'         => 'Unfollowed',
            'followers_count' => $user->followers()->count(),
            'is_following'    => false,
        ]);
    }

    /**
     * List followers user tertentu.
     */
    public function followers(Request $request, User $user): JsonResponse
    {
        $followers = $user->followers()
            ->select('users.id', 'users.name', 'users.username', 'users.avatar')
            ->paginate(20);

        return response()->json($followers);
    }

    /**
     * List user yang di-follow oleh user tertentu.
     */
    public function following(Request $request, User $user): JsonResponse
    {
        $following = $user->following()
            ->select('users.id', 'users.name', 'users.username', 'users.avatar')
            ->paginate(20);

        return response()->json($following);
    }

    /**
     * Saran user untuk di-follow.
     * Ambil user yang belum di-follow dan bukan diri sendiri.
     */
    public function suggestions(Request $request): JsonResponse
    {
        $authUser = $request->user();

        $followingIds = $authUser->following()->pluck('users.id')->toArray();
        $excludeIds   = array_merge($followingIds, [$authUser->id]);

        $suggestions = User::whereNotIn('id', $excludeIds)
            ->select('id', 'name', 'username', 'avatar')
            ->inRandomOrder()
            ->limit(10)
            ->get()
            ->map(function ($user) use ($authUser) {
                return [
                    'id'              => $user->id,
                    'name'            => $user->name,
                    'username'        => $user->username,
                    'avatar'          => $user->avatar,
                    'is_following'    => false,
                    'followers_count' => $user->followers()->count(),
                ];
            });

        return response()->json(['data' => $suggestions]);
    }

    /**
    public function isFollowing(Request $request, User $user): JsonResponse
    {
        $isFollowing = Follow::where([
            'follower_id'  => $request->user()->id,
            'following_id' => $user->id,
        ])->exists();

        return response()->json([
            'is_following'    => $isFollowing,
            'followers_count' => $user->followers()->count(),
            'following_count' => $user->following()->count(),
        ]);
    }

    /**
     * Ambil aktivitas publik user tertentu.
     * Akun privat hanya bisa dilihat oleh follower.
     */
    public function userActivities(Request $request, User $user): JsonResponse
    {
        $authUser = $request->user();

        // Cek akses: jika privat, harus follower atau diri sendiri
        if ($user->is_private && $authUser->id !== $user->id) {
            $isFollower = Follow::where([
                'follower_id'  => $authUser->id,
                'following_id' => $user->id,
            ])->exists();

            if (!$isFollower) {
                return response()->json([
                    'message' => 'Akun ini bersifat privat. Follow untuk melihat aktivitasnya.',
                ], 403);
            }
        }

        $activities = Activity::where('user_id', $user->id)
            ->where('is_public', true)
            ->orderBy('started_at', 'desc')
            ->paginate(15);

        return response()->json($activities);
    }
}
