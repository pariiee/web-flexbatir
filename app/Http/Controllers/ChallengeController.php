<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeParticipant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ChallengeController extends Controller
{
    /**
     * List all public challenges.
     */
    public function index(Request $request): JsonResponse
    {
        $challenges = Challenge::where('is_public', true)
            ->with('user:id,name,username,avatar')
            ->when($request->sport_type, fn($q) => $q->where('sport_type', $request->sport_type))
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->where('end_date', '>=', now())
            ->orderBy('participants_count', 'desc')
            ->paginate(15);

        return response()->json($challenges);
    }

    /**
     * List challenges joined by authenticated user.
     */
    public function myChallenges(Request $request): JsonResponse
    {
        $challenges = Challenge::whereHas('participants', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })
        ->with('user:id,name,username,avatar')
        ->orderBy('end_date')
        ->get();

        return response()->json(['challenges' => $challenges]);
    }

    /**
     * Create a new challenge.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'type'         => 'required|in:distance,duration,calories,activities,elevation',
            'sport_type'   => 'nullable|in:run,ride,swim,walk,hike,any',
            'target_value' => 'required|numeric|min:0',
            'unit'         => 'nullable|string|max:20',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after:start_date',
            'is_public'    => 'nullable|boolean',
        ]);

        $challenge = Challenge::create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        // Pembuat otomatis jadi peserta
        ChallengeParticipant::create([
            'challenge_id'   => $challenge->id,
            'user_id'        => $request->user()->id,
            'target_value'   => $validated['target_value'],
            'current_value'  => 0,
            'progress_percent' => 0,
        ]);

        $challenge->increment('participants_count');

        return response()->json([
            'message'   => 'Tantangan berhasil dibuat.',
            'challenge' => $challenge->load('user:id,name,username,avatar'),
        ], 201);
    }

    /**
     * Show a single challenge.
     */
    public function show(Challenge $challenge): JsonResponse
    {
        return response()->json([
            'challenge' => $challenge->load('user:id,name,username,avatar'),
        ]);
    }

    /**
     * Update a challenge.
     */
    public function update(Request $request, Challenge $challenge): JsonResponse
    {
        if ($challenge->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'end_date'    => 'sometimes|date',
            'is_public'   => 'sometimes|boolean',
        ]);

        $challenge->update($validated);

        return response()->json([
            'message'   => 'Tantangan berhasil diperbarui.',
            'challenge' => $challenge->fresh(),
        ]);
    }

    /**
     * Delete a challenge.
     */
    public function destroy(Request $request, Challenge $challenge): JsonResponse
    {
        if ($challenge->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        if ($challenge->cover_image) {
            Storage::disk('public')->delete($challenge->cover_image);
        }

        $challenge->delete();

        return response()->json(['message' => 'Tantangan berhasil dihapus.']);
    }

    /**
     * Join a challenge.
     */
    public function join(Request $request, Challenge $challenge): JsonResponse
    {
        $existing = ChallengeParticipant::where('challenge_id', $challenge->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Kamu sudah mengikuti tantangan ini.'], 409);
        }

        if ($challenge->end_date < now()) {
            return response()->json(['message' => 'Tantangan ini sudah berakhir.'], 422);
        }

        ChallengeParticipant::create([
            'challenge_id'    => $challenge->id,
            'user_id'         => $request->user()->id,
            'target_value'    => $challenge->target_value,
            'current_value'   => 0,
            'progress_percent' => 0,
        ]);

        $challenge->increment('participants_count');

        return response()->json(['message' => 'Berhasil bergabung ke tantangan.']);
    }

    /**
     * Leave a challenge.
     */
    public function leave(Request $request, Challenge $challenge): JsonResponse
    {
        if ($challenge->user_id === $request->user()->id) {
            return response()->json(['message' => 'Pembuat tantangan tidak dapat keluar.'], 422);
        }

        $deleted = ChallengeParticipant::where('challenge_id', $challenge->id)
            ->where('user_id', $request->user()->id)
            ->delete();

        if ($deleted) {
            $challenge->decrement('participants_count');
        }

        return response()->json(['message' => 'Berhasil keluar dari tantangan.']);
    }

    /**
     * Get leaderboard for a challenge.
     */
    public function leaderboard(Challenge $challenge): JsonResponse
    {
        $leaderboard = ChallengeParticipant::where('challenge_id', $challenge->id)
            ->with('user:id,name,username,avatar')
            ->orderBy('current_value', 'desc')
            ->get()
            ->map(function ($participant, $index) {
                return array_merge($participant->toArray(), [
                    'rank' => $index + 1,
                ]);
            });

        return response()->json([
            'challenge'   => $challenge->only(['id', 'title', 'type', 'target_value', 'unit', 'end_date']),
            'leaderboard' => $leaderboard,
        ]);
    }

    /**
     * Update progress for a participant.
     */
    public function updateProgress(Request $request, Challenge $challenge): JsonResponse
    {
        $request->validate([
            'current_value' => 'required|numeric|min:0',
        ]);

        $participant = ChallengeParticipant::where('challenge_id', $challenge->id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $currentValue    = $request->input('current_value');
        $progressPercent = min(100, round(($currentValue / $challenge->target_value) * 100));
        $status          = $progressPercent >= 100 ? 'completed' : 'active';

        $participant->update([
            'current_value'    => $currentValue,
            'progress_percent' => $progressPercent,
            'status'           => $status,
        ]);

        return response()->json([
            'message'     => 'Progress berhasil diperbarui.',
            'participant' => $participant->fresh(),
        ]);
    }
}
