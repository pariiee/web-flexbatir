<?php

namespace App\Http\Controllers;

use App\Models\Segment;
use App\Models\SegmentEffort;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SegmentController extends Controller
{
    /**
     * List all public segments.
     */
    public function index(Request $request): JsonResponse
    {
        $segments = Segment::where('is_public', true)
            ->with('user:id,name,username,avatar')
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('effort_count', 'desc')
            ->paginate(15);

        return response()->json($segments);
    }

    /**
     * List segments created by authenticated user.
     */
    public function mySegments(Request $request): JsonResponse
    {
        $segments = Segment::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($segments);
    }

    /**
     * Create a new segment.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'type'            => 'required|in:run,ride,swim,walk,hike,other',
            'distance'        => 'nullable|numeric|min:0',
            'elevation_gain'  => 'nullable|numeric',
            'elevation_loss'  => 'nullable|numeric',
            'average_grade'   => 'nullable|numeric',
            'maximum_grade'   => 'nullable|numeric',
            'start_lat'       => 'nullable|numeric|between:-90,90',
            'start_lng'       => 'nullable|numeric|between:-180,180',
            'end_lat'         => 'nullable|numeric|between:-90,90',
            'end_lng'         => 'nullable|numeric|between:-180,180',
            'polyline'        => 'nullable|array',
            'polyline.*.lat'  => 'required_with:polyline|numeric|between:-90,90',
            'polyline.*.lng'  => 'required_with:polyline|numeric|between:-180,180',
            'is_public'       => 'nullable|boolean',
        ]);

        $segment = Segment::create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Segmen berhasil dibuat.',
            'segment' => $segment,
        ], 201);
    }

    /**
     * Show a single segment.
     */
    public function show(Request $request, Segment $segment): JsonResponse
    {
        if (!$segment->is_public && $segment->user_id !== $request->user()?->id) {
            return response()->json(['message' => 'Segmen ini bersifat privat.'], 403);
        }

        return response()->json([
            'segment' => $segment->load('user:id,name,username,avatar'),
        ]);
    }

    /**
     * Update a segment.
     */
    public function update(Request $request, Segment $segment): JsonResponse
    {
        if ($segment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $validated = $request->validate([
            'name'           => 'sometimes|string|max:255',
            'description'    => 'sometimes|nullable|string',
            'type'           => 'sometimes|in:run,ride,swim,walk,hike,other',
            'distance'       => 'sometimes|nullable|numeric|min:0',
            'elevation_gain' => 'sometimes|nullable|numeric',
            'elevation_loss' => 'sometimes|nullable|numeric',
            'average_grade'  => 'sometimes|nullable|numeric',
            'maximum_grade'  => 'sometimes|nullable|numeric',
            'is_public'      => 'sometimes|boolean',
        ]);

        $segment->update($validated);

        return response()->json([
            'message' => 'Segmen berhasil diperbarui.',
            'segment' => $segment->fresh(),
        ]);
    }

    /**
     * Delete a segment.
     */
    public function destroy(Request $request, Segment $segment): JsonResponse
    {
        if ($segment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $segment->delete();

        return response()->json(['message' => 'Segmen berhasil dihapus.']);
    }

    /**
     * Get leaderboard for a segment.
     */
    public function leaderboard(Segment $segment): JsonResponse
    {
        $leaderboard = SegmentEffort::where('segment_id', $segment->id)
            ->with('user:id,name,username,avatar')
            ->orderBy('elapsed_time')
            ->limit(50)
            ->get()
            ->map(function ($effort, $index) {
                return array_merge($effort->toArray(), [
                    'rank'                   => $index + 1,
                    'elapsed_time_formatted' => $effort->elapsed_time_formatted,
                ]);
            });

        return response()->json([
            'segment'     => $segment->only(['id', 'name', 'type', 'distance']),
            'leaderboard' => $leaderboard,
        ]);
    }

    /**
     * Log a segment effort.
     */
    public function logEffort(Request $request, Segment $segment): JsonResponse
    {
        $validated = $request->validate([
            'activity_id'        => 'nullable|exists:activities,id',
            'started_at'         => 'required|date',
            'ended_at'           => 'required|date|after:started_at',
            'elapsed_time'       => 'required|integer|min:1',
            'average_speed'      => 'nullable|numeric|min:0',
            'max_speed'          => 'nullable|numeric|min:0',
            'average_heart_rate' => 'nullable|integer|min:0|max:300',
            'max_heart_rate'     => 'nullable|integer|min:0|max:300',
        ]);

        $effort = SegmentEffort::create([
            ...$validated,
            'segment_id' => $segment->id,
            'user_id'    => $request->user()->id,
        ]);

        // Update rank di leaderboard
        $rank = SegmentEffort::where('segment_id', $segment->id)
            ->where('elapsed_time', '<=', $effort->elapsed_time)
            ->count();

        $effort->update(['rank' => $rank]);

        // Update statistik segmen
        $segment->increment('effort_count');
        $athleteCount = SegmentEffort::where('segment_id', $segment->id)
            ->distinct('user_id')
            ->count('user_id');
        $segment->update(['athlete_count' => $athleteCount]);

        return response()->json([
            'message' => 'Effort segmen berhasil dicatat.',
            'effort'  => array_merge($effort->toArray(), [
                'elapsed_time_formatted' => $effort->elapsed_time_formatted,
            ]),
        ], 201);
    }

    /**
     * Get efforts by authenticated user for a segment.
     */
    public function myEfforts(Request $request, Segment $segment): JsonResponse
    {
        $efforts = SegmentEffort::where('segment_id', $segment->id)
            ->where('user_id', $request->user()->id)
            ->orderBy('elapsed_time')
            ->get()
            ->map(fn($e) => array_merge($e->toArray(), [
                'elapsed_time_formatted' => $e->elapsed_time_formatted,
            ]));

        return response()->json(['efforts' => $efforts]);
    }
}
