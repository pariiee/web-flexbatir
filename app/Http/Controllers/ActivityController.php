<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ActivityController extends Controller
{
    /**
     * List all activities for authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $activities = Activity::where('user_id', $request->user()->id)
            ->orderBy('started_at', 'desc')
            ->paginate(15);

        return response()->json($activities);
    }

    /**
     * Store a new activity (manual entry).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'type'                => 'required|in:run,ride,swim,walk,hike,workout,yoga,crossfit,other',
            'started_at'          => 'required|date',
            'ended_at'            => 'nullable|date|after:started_at',
            'duration'            => 'nullable|integer|min:1',
            'distance'            => 'nullable|numeric|min:0',
            'average_speed'       => 'nullable|numeric|min:0',
            'max_speed'           => 'nullable|numeric|min:0',
            'elevation_gain'      => 'nullable|numeric|min:0',
            'elevation_loss'      => 'nullable|numeric|min:0',
            'calories'            => 'nullable|integer|min:0',
            'average_heart_rate'  => 'nullable|integer|min:0|max:300',
            'max_heart_rate'      => 'nullable|integer|min:0|max:300',
            'gps_data'            => 'nullable|array',
            'is_public'           => 'nullable|boolean',
        ]);

        $activity = Activity::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'source'  => 'manual',
        ]);

        return response()->json([
            'message'  => 'Aktivitas berhasil ditambahkan.',
            'activity' => $activity,
        ], 201);
    }

    /**
     * Upload activity file (GPX/FIT).
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file'  => 'required|file|mimes:gpx,fit,tcx|max:10240',
            'title' => 'nullable|string|max:255',
            'type'  => 'nullable|in:run,ride,swim,walk,hike,workout,yoga,crossfit,other',
        ]);

        $path = $request->file('file')->store('activities', 'public');

        $activity = Activity::create([
            'user_id'    => $request->user()->id,
            'title'      => $request->input('title', 'Aktivitas dari file'),
            'type'       => $request->input('type', 'other'),
            'started_at' => now(),
            'file_path'  => $path,
            'source'     => 'upload',
        ]);

        return response()->json([
            'message'  => 'File aktivitas berhasil diupload.',
            'activity' => $activity,
        ], 201);
    }

    /**
     * Show a single activity.
     */
    public function show(Request $request, Activity $activity): JsonResponse
    {
        // Cek akses: milik sendiri atau publik
        if ($activity->user_id !== $request->user()->id && !$activity->is_public) {
            return response()->json(['message' => 'Aktivitas ini bersifat privat.'], 403);
        }

        return response()->json([
            'activity' => array_merge($activity->toArray(), [
                'duration_formatted' => $activity->duration_formatted,
                'distance_km'        => $activity->distance_km,
                'user'               => $activity->user->only(['id', 'name', 'username', 'avatar']),
            ]),
        ]);
    }

    /**
     * Update an activity.
     */
    public function update(Request $request, Activity $activity): JsonResponse
    {
        if ($activity->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $validated = $request->validate([
            'title'               => 'sometimes|string|max:255',
            'description'         => 'sometimes|nullable|string',
            'type'                => 'sometimes|in:run,ride,swim,walk,hike,workout,yoga,crossfit,other',
            'started_at'          => 'sometimes|date',
            'ended_at'            => 'sometimes|nullable|date|after:started_at',
            'duration'            => 'sometimes|nullable|integer|min:1',
            'distance'            => 'sometimes|nullable|numeric|min:0',
            'average_speed'       => 'sometimes|nullable|numeric|min:0',
            'max_speed'           => 'sometimes|nullable|numeric|min:0',
            'elevation_gain'      => 'sometimes|nullable|numeric|min:0',
            'elevation_loss'      => 'sometimes|nullable|numeric|min:0',
            'calories'            => 'sometimes|nullable|integer|min:0',
            'average_heart_rate'  => 'sometimes|nullable|integer|min:0|max:300',
            'max_heart_rate'      => 'sometimes|nullable|integer|min:0|max:300',
            'is_public'           => 'sometimes|boolean',
        ]);

        $activity->update($validated);

        return response()->json([
            'message'  => 'Aktivitas berhasil diperbarui.',
            'activity' => $activity->fresh(),
        ]);
    }

    /**
     * Delete an activity.
     */
    public function destroy(Request $request, Activity $activity): JsonResponse
    {
        if ($activity->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        // Hapus file jika ada
        if ($activity->file_path) {
            Storage::disk('public')->delete($activity->file_path);
        }

        $activity->delete();

        return response()->json([
            'message' => 'Aktivitas berhasil dihapus.',
        ]);
    }

    /**
     * List public activities (feed).
     */
    public function feed(Request $request): JsonResponse
    {
        $activities = Activity::where('is_public', true)
            ->with('user:id,name,username,avatar')
            ->orderBy('started_at', 'desc')
            ->paginate(15);

        return response()->json($activities);
    }

    /**
     * List public activities from users that the authenticated user follows.
     */
    public function followingFeed(Request $request): JsonResponse
    {
        $followingIds = $request->user()
            ->following()
            ->pluck('users.id');

        $activities = Activity::whereIn('user_id', $followingIds)
            ->where('is_public', true)
            ->with('user:id,name,username,avatar')
            ->orderBy('started_at', 'desc')
            ->paginate(15);

        return response()->json($activities);
    }
}
