<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GoalController extends Controller
{
    /**
     * List all goals for authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $goals = Goal::where('user_id', $request->user()->id)
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->orderBy('end_date')
            ->get()
            ->map(fn($goal) => array_merge($goal->toArray(), [
                'progress_percent' => $goal->progress_percent,
            ]));

        return response()->json(['goals' => $goals]);
    }

    /**
     * Create a new goal.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'type'         => 'required|in:distance,duration,calories,activities,elevation',
            'sport_type'   => 'nullable|in:run,ride,swim,walk,hike,any',
            'period'       => 'nullable|in:weekly,monthly,yearly,custom',
            'target_value' => 'required|numeric|min:0',
            'unit'         => 'nullable|string|max:20',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after:start_date',
        ]);

        $goal = Goal::create([
            ...$validated,
            'user_id'       => $request->user()->id,
            'current_value' => 0,
            'status'        => 'active',
        ]);

        return response()->json([
            'message' => 'Target berhasil dibuat.',
            'goal'    => array_merge($goal->toArray(), [
                'progress_percent' => $goal->progress_percent,
            ]),
        ], 201);
    }

    /**
     * Show a single goal.
     */
    public function show(Request $request, Goal $goal): JsonResponse
    {
        if ($goal->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        return response()->json([
            'goal' => array_merge($goal->toArray(), [
                'progress_percent' => $goal->progress_percent,
            ]),
        ]);
    }

    /**
     * Update a goal.
     */
    public function update(Request $request, Goal $goal): JsonResponse
    {
        if ($goal->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $validated = $request->validate([
            'title'         => 'sometimes|string|max:255',
            'description'   => 'sometimes|nullable|string',
            'target_value'  => 'sometimes|numeric|min:0',
            'current_value' => 'sometimes|numeric|min:0',
            'status'        => 'sometimes|in:active,completed,failed,cancelled',
            'end_date'      => 'sometimes|date',
        ]);

        $goal->update($validated);

        // Auto complete jika current >= target
        if ($goal->current_value >= $goal->target_value && $goal->status === 'active') {
            $goal->update(['status' => 'completed']);
        }

        return response()->json([
            'message' => 'Target berhasil diperbarui.',
            'goal'    => array_merge($goal->fresh()->toArray(), [
                'progress_percent' => $goal->fresh()->progress_percent,
            ]),
        ]);
    }

    /**
     * Delete a goal.
     */
    public function destroy(Request $request, Goal $goal): JsonResponse
    {
        if ($goal->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $goal->delete();

        return response()->json(['message' => 'Target berhasil dihapus.']);
    }
}
