<?php

namespace App\Http\Controllers;

use App\Models\Insight;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class InsightController extends Controller
{
    /**
     * GET /api/insights
     * Ambil daftar insight personal untuk user yang sedang login.
     * Jika belum ada insight, generate otomatis dari data aktivitas.
     */
    public function index(Request $request): JsonResponse
    {
        $user  = $request->user();
        $limit = (int) $request->query('limit', 20);

        // Auto-generate jika insight kosong / sudah lebih dari 1 hari
        $lastInsight = Insight::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->first();

        if (!$lastInsight || $lastInsight->created_at->lt(Carbon::now()->subDay())) {
            $this->generateInsights($user->id);
        }

        $insights = Insight::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return response()->json(['insights' => $insights]);
    }

    /**
     * GET /api/insights/summary
     * Ringkasan statistik minggu ini untuk header Insights screen.
     */
    public function summary(Request $request): JsonResponse
    {
        $user      = $request->user();
        $weekStart = Carbon::now()->startOfWeek();
        $prevStart = Carbon::now()->subWeek()->startOfWeek();
        $prevEnd   = Carbon::now()->subWeek()->endOfWeek();

        // Aktivitas minggu ini
        $thisWeek = Activity::where('user_id', $user->id)
            ->where('started_at', '>=', $weekStart)
            ->get();

        // Aktivitas minggu lalu
        $lastWeek = Activity::where('user_id', $user->id)
            ->whereBetween('started_at', [$prevStart, $prevEnd])
            ->get();

        $thisDistKm  = $thisWeek->sum('distance_meters') / 1000;
        $lastDistKm  = $lastWeek->sum('distance_meters') / 1000;
        $changePercent = $lastDistKm > 0
            ? (($thisDistKm - $lastDistKm) / $lastDistKm) * 100
            : 0;

        // Rata-rata pace (detik/km) dari aktivitas lari minggu ini
        $runActivities = $thisWeek->where('type', 'run')
            ->filter(fn($a) => $a->distance_meters > 0);
        $avgPace = $runActivities->count() > 0
            ? $runActivities->avg(fn($a) => $a->duration_seconds / ($a->distance_meters / 1000))
            : 0;

        // Streak hari berturut-turut
        $streak = $this->calculateStreak($user->id);

        // Training load score (0-100, berdasarkan jarak + frekuensi)
        $trainingLoad = min(100, round(($thisDistKm * 3) + ($thisWeek->count() * 5)));

        // Fitness level
        $totalKm = Activity::where('user_id', $user->id)
            ->selectRaw('SUM(distance_meters) / 1000 as total')
            ->value('total') ?? 0;

        $fitnessLevel = match (true) {
            $totalKm >= 1000 => 'elite',
            $totalKm >= 300  => 'advanced',
            $totalKm >= 100  => 'intermediate',
            default          => 'beginner',
        };

        return response()->json([
            'summary' => [
                'weekly_distance_km'               => round($thisDistKm, 2),
                'weekly_distance_change_percent'   => round($changePercent, 1),
                'weekly_activities'                => $thisWeek->count(),
                'avg_pace_seconds_per_km'          => round($avgPace),
                'current_streak_days'              => $streak,
                'training_load_score'              => $trainingLoad,
                'fitness_level'                    => $fitnessLevel,
            ],
        ]);
    }

    /**
     * DELETE /api/insights/{id}
     * Dismiss / hapus satu insight.
     */
    public function destroy(Request $request, Insight $insight): JsonResponse
    {
        if ($insight->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $insight->delete();

        return response()->json(['message' => 'Insight dismissed']);
    }

    // ─── Private helpers ────────────────────────────────────────────────────────

    private function generateInsights(int $userId): void
    {
        // Hapus insight lama (lebih dari 3 hari)
        Insight::where('user_id', $userId)
            ->where('created_at', '<', Carbon::now()->subDays(3))
            ->delete();

        $weekStart = Carbon::now()->startOfWeek();
        $activities = Activity::where('user_id', $userId)
            ->where('started_at', '>=', $weekStart)
            ->get();

        $insightsToCreate = [];

        // 1. Training load warning
        $totalKmThisWeek = $activities->sum('distance_meters') / 1000;
        if ($totalKmThisWeek > 50) {
            $insightsToCreate[] = [
                'user_id'      => $userId,
                'type'         => 'training_load',
                'title'        => 'Beban Latihan Tinggi',
                'body'         => "Kamu sudah berlari {$totalKmThisWeek} km minggu ini. Pastikan kamu istirahat cukup untuk menghindari cedera.",
                'severity'     => 'warning',
                'action_label' => 'Lihat Kalender',
                'action_route' => '/training-calendar',
            ];
        }

        // 2. Streak achievement
        $streak = $this->calculateStreak($userId);
        if ($streak >= 3) {
            $insightsToCreate[] = [
                'user_id'  => $userId,
                'type'     => 'streak',
                'title'    => "{$streak} Hari Berturut-turut! 🔥",
                'body'     => "Luar biasa! Kamu sudah aktif {$streak} hari tanpa jeda. Pertahankan konsistensimu!",
                'severity' => 'achievement',
            ];
        }

        // 3. Weekly summary tip
        if ($activities->count() === 0) {
            $insightsToCreate[] = [
                'user_id'      => $userId,
                'type'         => 'weekly_summary',
                'title'        => 'Mulai Minggu Ini',
                'body'         => 'Belum ada aktivitas minggu ini. Mulai dengan sesi pendek 20-30 menit untuk menjaga rutinitas.',
                'severity'     => 'tip',
                'action_label' => 'Mulai Aktivitas',
                'action_route' => '/',
            ];
        } elseif ($activities->count() >= 4) {
            $insightsToCreate[] = [
                'user_id'  => $userId,
                'type'     => 'weekly_summary',
                'title'    => 'Minggu yang Produktif',
                'body'     => "Kamu sudah menyelesaikan {$activities->count()} aktivitas minggu ini. Kerja bagus!",
                'severity' => 'info',
            ];
        }

        // 4. Recovery tip (jika 3 hari terakhir berturut-turut)
        $last3Days = Activity::where('user_id', $userId)
            ->where('started_at', '>=', Carbon::now()->subDays(3))
            ->count();
        if ($last3Days >= 3) {
            $insightsToCreate[] = [
                'user_id'  => $userId,
                'type'     => 'recovery',
                'title'    => 'Waktu Pemulihan',
                'body'     => 'Kamu sudah berlatih 3 hari berturut-turut. Pertimbangkan satu hari istirahat aktif seperti jalan santai atau peregangan.',
                'severity' => 'tip',
            ];
        }

        foreach ($insightsToCreate as $data) {
            Insight::create($data);
        }
    }

    private function calculateStreak(int $userId): int
    {
        $streak = 0;
        $date   = Carbon::today();

        while (true) {
            $hasActivity = Activity::where('user_id', $userId)
                ->whereDate('started_at', $date)
                ->exists();

            if (!$hasActivity) break;

            $streak++;
            $date = $date->subDay();
        }

        return $streak;
    }
}
