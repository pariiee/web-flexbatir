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
            'gps_data'            => 'sometimes|nullable|array',
            'encoded_polyline'    => 'sometimes|nullable|string',
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

    /**
     * Personal Best — aktivitas tercepat (durasi terpendek) untuk jenis tertentu
     * yang memiliki gps_data, milik user yang sedang login.
     *
     * GET /activities/pb?type=run
     */
    public function personalBest(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|max:50',
        ]);

        $activity = Activity::where('user_id', $request->user()->id)
            ->where('type', $request->type)
            ->whereNotNull('gps_data')
            ->whereNotNull('duration')
            ->where('duration', '>', 0)
            ->orderBy('duration', 'asc') // terpendek = tercepat
            ->first();

        if (!$activity) {
            return response()->json(['activity' => null], 404);
        }

        return response()->json([
            'activity' => [
                'id'         => $activity->id,
                'type'       => $activity->type,
                'distance'   => $activity->distance,
                'duration'   => $activity->duration,
                'started_at' => $activity->started_at->toIso8601String(),
                'gps_data'   => $activity->gps_data,
            ],
        ]);
    }

    /**
     * Cek Personal Bests baru setelah aktivitas disimpan.
     * Membandingkan pace aktivitas ini vs semua aktivitas sebelumnya
     * pada jarak-jarak standar: 1km, 1mil, 5km, 10km, HM, marathon.
     *
     * GET /activities/{id}/personal-bests
     */
    public function checkPersonalBests(Request $request, int $id): JsonResponse
    {
        $activity = Activity::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$activity || !$activity->distance || !$activity->duration) {
            return response()->json(['prs' => []]);
        }

        // Jarak standar dalam meter => label
        $standardDistances = [
            1000    => '1 km',
            1609    => '1 mil',
            5000    => '5 km',
            10000   => '10 km',
            21097   => 'Half Marathon',
            42195   => 'Marathon',
        ];

        $userId  = $request->user()->id;
        $prs     = [];

        // Pace aktivitas ini (detik per meter)
        $currentPace = $activity->duration / $activity->distance;

        foreach ($standardDistances as $meters => $label) {
            // Hanya relevan jika aktivitas ini mencakup jarak tersebut
            if ($activity->distance < $meters * 0.95) {
                continue;
            }

            // Ambil semua aktivitas user dengan jarak >= threshold, kecuali yg ini
            $others = Activity::where('user_id', $userId)
                ->where('id', '!=', $activity->id)
                ->where('type', $activity->type)
                ->where('distance', '>=', $meters * 0.95)
                ->whereNotNull('duration')
                ->where('duration', '>', 0)
                ->get(['duration', 'distance']);

            // Pace aktivitas ini untuk jarak ini
            $myPace = $currentPace;

            // Hitung rank: berapa banyak aktivitas lain yang pacenya lebih baik (lebih kecil)
            $fasterCount = $others->filter(function ($a) use ($myPace) {
                return ($a->duration / $a->distance) < $myPace;
            })->count();

            $rank = $fasterCount + 1;

            // Hanya tampilkan jika masuk top 5 atau ini PR baru (rank 1)
            if ($rank <= 5) {
                $isAllTime = true; // Semua aktivitas sepanjang masa
                // Format pace: menit:detik per km
                $pacePerKm  = $myPace * 1000;
                $paceMin    = (int) ($pacePerKm / 60);
                $paceSec    = (int) ($pacePerKm % 60);
                $timeLabel  = sprintf("%d:%02d /km", $paceMin, $paceSec);

                $prs[] = [
                    'distance_label' => $label,
                    'time_label'     => $timeLabel,
                    'rank'           => $rank,
                    'is_all_time'    => $isAllTime,
                ];
            }
        }

        return response()->json(['prs' => $prs]);
    }
}
