<?php

namespace App\Http\Controllers;

use App\Models\TrainingLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TrainingCalendarController extends Controller
{
    /**
     * List training logs by month.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'year'  => 'nullable|integer|min:2000|max:2100',
            'month' => 'nullable|integer|min:1|max:12',
        ]);

        $year  = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $logs = TrainingLog::where('user_id', $request->user()->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->with('activity:id,title,type,distance,duration')
            ->orderBy('date')
            ->get();

        return response()->json([
            'year'  => $year,
            'month' => $month,
            'logs'  => $logs,
        ]);
    }

    /**
     * Store a new training log.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date'              => 'required|date',
            'title'             => 'required|string|max:255',
            'notes'             => 'nullable|string',
            'status'            => 'nullable|in:planned,completed,skipped',
            'type'              => 'required|in:run,ride,swim,walk,hike,workout,yoga,crossfit,other',
            'activity_id'       => 'nullable|exists:activities,id',
            'planned_duration'  => 'nullable|integer|min:1',
            'planned_distance'  => 'nullable|numeric|min:0',
            'actual_duration'   => 'nullable|integer|min:1',
            'actual_distance'   => 'nullable|numeric|min:0',
            'actual_calories'   => 'nullable|integer|min:0',
            'perceived_effort'  => 'nullable|integer|min:1|max:10',
            'mood'              => 'nullable|integer|min:1|max:5',
        ]);

        $log = TrainingLog::create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Catatan latihan berhasil ditambahkan.',
            'log'     => $log,
        ], 201);
    }

    /**
     * Show a single training log.
     */
    public function show(Request $request, TrainingLog $trainingLog): JsonResponse
    {
        if ($trainingLog->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        return response()->json([
            'log' => $trainingLog->load('activity:id,title,type,distance,duration'),
        ]);
    }

    /**
     * Update a training log.
     */
    public function update(Request $request, TrainingLog $trainingLog): JsonResponse
    {
        if ($trainingLog->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $validated = $request->validate([
            'date'              => 'sometimes|date',
            'title'             => 'sometimes|string|max:255',
            'notes'             => 'sometimes|nullable|string',
            'status'            => 'sometimes|in:planned,completed,skipped',
            'type'              => 'sometimes|in:run,ride,swim,walk,hike,workout,yoga,crossfit,other',
            'activity_id'       => 'sometimes|nullable|exists:activities,id',
            'planned_duration'  => 'sometimes|nullable|integer|min:1',
            'planned_distance'  => 'sometimes|nullable|numeric|min:0',
            'actual_duration'   => 'sometimes|nullable|integer|min:1',
            'actual_distance'   => 'sometimes|nullable|numeric|min:0',
            'actual_calories'   => 'sometimes|nullable|integer|min:0',
            'perceived_effort'  => 'sometimes|nullable|integer|min:1|max:10',
            'mood'              => 'sometimes|nullable|integer|min:1|max:5',
        ]);

        $trainingLog->update($validated);

        return response()->json([
            'message' => 'Catatan latihan berhasil diperbarui.',
            'log'     => $trainingLog->fresh(),
        ]);
    }

    /**
     * Delete a training log.
     */
    public function destroy(Request $request, TrainingLog $trainingLog): JsonResponse
    {
        if ($trainingLog->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $trainingLog->delete();

        return response()->json([
            'message' => 'Catatan latihan berhasil dihapus.',
        ]);
    }

    /**
     * Get summary stats for a given month.
     */
    public function summary(Request $request): JsonResponse
    {
        $request->validate([
            'year'  => 'nullable|integer|min:2000|max:2100',
            'month' => 'nullable|integer|min:1|max:12',
        ]);

        $year  = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $logs = TrainingLog::where('user_id', $request->user()->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        return response()->json([
            'year'           => $year,
            'month'          => $month,
            'total'          => $logs->count(),
            'completed'      => $logs->where('status', 'completed')->count(),
            'planned'        => $logs->where('status', 'planned')->count(),
            'skipped'        => $logs->where('status', 'skipped')->count(),
            'total_duration' => $logs->sum('actual_duration'),   // detik
            'total_distance' => $logs->sum('actual_distance'),   // meter
            'total_calories' => $logs->sum('actual_calories'),
        ]);
    }
}
