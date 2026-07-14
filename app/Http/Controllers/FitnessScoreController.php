<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\FitnessScore;
use App\Services\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FitnessScoreController extends Controller
{
    /**
     * Ambil riwayat fitness score user (30 hari terakhir).
     */
    public function index(Request $request): JsonResponse
    {
        $scores = FitnessScore::where('user_id', $request->user()->id)
            ->orderByDesc('date')
            ->limit(30)
            ->get();

        return response()->json(['data' => $scores]);
    }

    /**
     * Ambil fitness score hari ini atau tanggal tertentu.
     */
    public function show(Request $request, string $date = 'today'): JsonResponse
    {
        $targetDate = $date === 'today' ? now()->toDateString() : $date;

        $score = FitnessScore::where('user_id', $request->user()->id)
            ->where('date', $targetDate)
            ->first();

        if (! $score) {
            return response()->json(['message' => 'Belum ada data untuk tanggal ini.'], 404);
        }

        return response()->json(['data' => $score]);
    }

    /**
     * Analisa fitness & freshness score user menggunakan AI.
     * Ambil aktivitas 42 hari terakhir → hitung TSS → minta analisis ke AIService.
     *
     * POST /api/fitness/analyze
     */
    public function analyze(Request $request): JsonResponse
    {
        $user = $request->user();

        // Ambil aktivitas 42 hari terakhir (window CTL standar)
        $activities = Activity::where('user_id', $user->id)
            ->where('started_at', '>=', now()->subDays(42))
            ->orderByDesc('started_at')
            ->get(['type', 'started_at', 'duration', 'distance', 'calories',
                   'average_heart_rate', 'elevation_gain']);

        if ($activities->isEmpty()) {
            return response()->json([
                'message' => 'Belum ada aktivitas dalam 42 hari terakhir untuk dianalisis.',
            ], 422);
        }

        // ── Hitung metrik per periode ──────────────────────────────────────────
        $last7  = $activities->filter(fn ($a) => $a->started_at->gte(now()->subDays(7)));
        $last28 = $activities->filter(fn ($a) => $a->started_at->gte(now()->subDays(28)));
        $last42 = $activities;

        // TSS sederhana: (durasi_menit × intensitas) — intensitas dari HR atau kalori
        $tss = $this->calculateTSS($last7);

        // ATL (Acute Training Load) — rata-rata TSS 7 hari
        $atl = $last7->count() > 0
            ? round($this->calculateTSS($last7) / max($last7->count(), 1))
            : 0;

        // CTL (Chronic Training Load) — rata-rata TSS 42 hari
        $ctl = $last42->count() > 0
            ? round($this->calculateTSS($last42) / max($last42->count(), 1))
            : 0;

        // TSB / Form = CTL - ATL (positif = segar, negatif = lelah)
        $tsb = $ctl - $atl;

        // Fatigue level 0-10
        $fatigueLevel = min(10, max(0, (int) round($atl / 10)));

        // ── Siapkan prompt untuk AI ────────────────────────────────────────────
        $activitiesSummary = $last7->map(fn ($a) => sprintf(
            '- %s | %s | %dm | %.1fkm | %skcal | HR: %s bpm',
            $a->started_at->format('D d/m'),
            $a->type,
            round(($a->duration ?? 0) / 60),
            ($a->distance ?? 0) / 1000,
            $a->calories ?? '?',
            $a->average_heart_rate ?? '?'
        ))->join("\n");

        $prompt = <<<PROMPT
Kamu adalah pelatih kebugaran profesional. Analisa data pelatihan atlet berikut dan berikan rekomendasi.

DATA PELATIHAN:
- Nama: {$user->name}
- Aktivitas 7 hari terakhir:
{$activitiesSummary}

METRIK BEBAN LATIHAN (Training Load):
- ATL (Fatigue/7 hari): {$atl}
- CTL (Fitness/42 hari): {$ctl}
- TSB/Form (CTL-ATL): {$tsb} (positif = segar, negatif = kelelahan)
- Tingkat kelelahan: {$fatigueLevel}/10

INSTRUKSI:
Berikan analisis singkat dan rekomendasi dalam 3-4 kalimat dalam Bahasa Indonesia. Fokus pada:
1. Status kebugaran saat ini berdasarkan TSB
2. Apakah perlu istirahat atau bisa latihan keras
3. Saran spesifik untuk 2-3 hari ke depan
Jangan gunakan bullet points, tulis dalam paragraf singkat yang natural.
PROMPT;

        // ── Panggil AIService ──────────────────────────────────────────────────
        try {
            $ai     = new AIService();
            $result = $ai->ask($prompt, ['max_tokens' => 300]);

            $recommendation = $result['content'];
            $modelUsed      = $result['model_used'];
        } catch (\Exception $e) {
            Log::error('[FitnessScore] AI gagal: ' . $e->getMessage());

            // Fallback: rekomendasi rule-based jika AI tidak tersedia
            $recommendation = $this->ruleBasedRecommendation($tsb, $fatigueLevel, $last7->count());
            $modelUsed      = 'rule-based';
        }

        // ── Simpan ke database ─────────────────────────────────────────────────
        $score = FitnessScore::updateOrCreate(
            [
                'user_id' => $user->id,
                'date'    => now()->toDateString(),
            ],
            [
                'training_stress_score' => min(999, max(0, $tss)),
                'fatigue_level'         => $fatigueLevel,
                'fitness_level'         => min(255, max(0, $ctl)),
                'form_score'            => max(-127, min(127, $tsb)),
                'ai_recommendation'     => $recommendation,
                'ai_model_used'         => $modelUsed,
            ]
        );

        return response()->json([
            'message' => 'Analisis berhasil.',
            'data'    => [
                'score'          => $score,
                'metrics'        => [
                    'atl'          => $atl,
                    'ctl'          => $ctl,
                    'tsb'          => $tsb,
                    'fatigue'      => $fatigueLevel,
                    'form_status'  => $this->formStatus($tsb),
                ],
                'activities_analyzed' => $activities->count(),
            ],
        ]);
    }

    // ── Private Helpers ────────────────────────────────────────────────────────

    /**
     * Hitung Training Stress Score sederhana dari koleksi aktivitas.
     * TSS ≈ (durasi_jam × HR_ratio² × 100) jika HR tersedia,
     * fallback ke (durasi_jam × faktor_tipe × 100).
     */
    private function calculateTSS($activities): int
    {
        $total = 0;

        foreach ($activities as $activity) {
            $durationHours = ($activity->duration ?? 0) / 3600;

            if ($durationHours < 0.05) continue; // skip < 3 menit

            if ($activity->average_heart_rate) {
                // Threshold HR estimasi 85% dari max HR (220 - 30 asumsi)
                $thresholdHr = 161;
                $hrRatio     = $activity->average_heart_rate / $thresholdHr;
                $total      += (int) round($durationHours * ($hrRatio ** 2) * 100);
            } else {
                // Faktor intensitas per tipe olahraga
                $factor = match (true) {
                    in_array($activity->type, ['run', 'trail_run', 'hiit'])  => 0.85,
                    in_array($activity->type, ['ride', 'mountain_bike'])     => 0.75,
                    in_array($activity->type, ['swim', 'walk'])              => 0.65,
                    default                                                  => 0.70,
                };
                $total += (int) round($durationHours * $factor * 100);
            }
        }

        return $total;
    }

    private function formStatus(int $tsb): string
    {
        return match (true) {
            $tsb > 25  => 'Sangat Segar',
            $tsb > 5   => 'Segar',
            $tsb >= -10 => 'Optimal',
            $tsb >= -30 => 'Lelah',
            default    => 'Sangat Lelah',
        };
    }

    private function ruleBasedRecommendation(int $tsb, int $fatigue, int $activitiesCount): string
    {
        if ($fatigue >= 8 || $tsb < -30) {
            return 'Tingkat kelelahan kamu sangat tinggi saat ini. Disarankan untuk istirahat aktif atau recovery ringan selama 2-3 hari ke depan. Prioritaskan tidur cukup dan nutrisi yang baik sebelum kembali berlatih keras.';
        }

        if ($tsb > 20) {
            return 'Kamu dalam kondisi sangat segar dengan beban latihan rendah. Ini saat yang tepat untuk sesi intensitas tinggi atau long run. Manfaatkan kondisi ini untuk meningkatkan performa.';
        }

        if ($activitiesCount < 2) {
            return 'Volume latihan minggu ini masih rendah. Pertimbangkan untuk menambah sesi latihan dengan intensitas sedang untuk menjaga kebugaran. Konsistensi adalah kunci progres jangka panjang.';
        }

        return 'Beban latihan kamu berada dalam zona optimal. Pertahankan ritme saat ini dengan variasi antara sesi intensitas tinggi dan recovery. Pastikan tidur cukup untuk mendukung adaptasi tubuh.';
    }
}
