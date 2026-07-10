<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Get authenticated user profile.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => array_merge($user->toArray(), [
                'avatar_url' => $user->avatar
                    ? Storage::url($user->avatar)
                    : null,
            ]),
        ]);
    }

    /**
     * Update authenticated user profile.
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'                   => 'sometimes|string|max:255',
            'username'               => ['sometimes', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'bio'                    => 'sometimes|nullable|string|max:500',
            'gender'                 => 'sometimes|nullable|in:male,female,other',
            'birth_date'             => 'sometimes|nullable|date|before:today',
            'weight'                 => 'sometimes|nullable|numeric|min:20|max:500',
            'height'                 => 'sometimes|nullable|numeric|min:50|max:300',
            'location'               => 'sometimes|nullable|string|max:100',
            'website'                => 'sometimes|nullable|url|max:255',
            'measurement_preference' => 'sometimes|in:metric,imperial',
            'is_private'             => 'sometimes|boolean',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'user'    => $user->fresh(),
        ]);
    }

    /**
     * Upload user avatar.
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user = $request->user();

        // Hapus avatar lama jika ada
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        return response()->json([
            'message'    => 'Avatar berhasil diupload.',
            'avatar_url' => Storage::url($path),
        ]);
    }

    /**
     * Delete user avatar.
     */
    public function deleteAvatar(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return response()->json([
            'message' => 'Avatar berhasil dihapus.',
        ]);
    }

    /**
     * Get public profile by username.
     * Juga return followers_count, following_count, dan is_following jika ada auth user.
     */
    public function showByUsername(string $username): JsonResponse
    {
        $user = \App\Models\User::where('username', $username)->firstOrFail();

        $authUser = auth('sanctum')->user();
        $isFollowing = false;

        if ($authUser) {
            $isFollowing = Follow::where([
                'follower_id'  => $authUser->id,
                'following_id' => $user->id,
            ])->exists();
        }

        if ($user->is_private && (!$authUser || ($authUser->id !== $user->id && !$isFollowing))) {
            return response()->json([
                'id'              => $user->id,
                'name'            => $user->name,
                'username'        => $user->username,
                'avatar_url'      => $user->avatar ? Storage::url($user->avatar) : null,
                'is_private'      => true,
                'is_following'    => $isFollowing,
                'followers_count' => $user->followers()->count(),
                'following_count' => $user->following()->count(),
                'message'         => 'Akun ini bersifat privat.',
            ], 200);
        }

        return response()->json(array_merge($user->toArray(), [
            'avatar_url'      => $user->avatar ? Storage::url($user->avatar) : null,
            'is_following'    => $isFollowing,
            'followers_count' => $user->followers()->count(),
            'following_count' => $user->following()->count(),
        ]));
    }
}
