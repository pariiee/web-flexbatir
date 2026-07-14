<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\FitnessScore;
use App\Models\LiveBeacon;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // ── Stats utama ────────────────────────────────────────────────────────
        $stats = [
            'total_users'      => User::count(),
            'new_users_today'  => User::whereDate('created_at', today())->count(),
            'new_users_week'   => User::where('created_at', '>=', now()->subDays(7))->count(),
            'banned_users'     => User::where('is_banned', true)->count(),
            'total_activities' => Activity::count(),
            'activities_today' => Activity::whereDate('created_at', today())->count(),
            'total_posts'      => Post::count(),
            'active_beacons'   => LiveBeacon::where('is_active', true)->count(),
        ];

        // ── Registrasi per hari (14 hari terakhir) ─────────────────────────────
        $registrations = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(13))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Isi tanggal yang kosong dengan 0
        $regLabels = [];
        $regData   = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = now()->subDays($i)->toDateString();
            $regLabels[] = now()->subDays($i)->format('d/m');
            $regData[]   = $registrations->get($d)?->count ?? 0;
        }

        // ── Aktivitas per hari (14 hari terakhir) ──────────────────────────────
        $activities = Activity::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(13))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $actData = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = now()->subDays($i)->toDateString();
            $actData[] = $activities->get($d)?->count ?? 0;
        }

        // ── Top sport types ────────────────────────────────────────────────────
        $topSports = Activity::select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->orderByDesc('count')
            ->limit(6)
            ->get();

        // ── Users terbaru ──────────────────────────────────────────────────────
        $recentUsers = User::latest()->limit(8)->get();

        // ── Aktivitas terbaru ──────────────────────────────────────────────────
        $recentActivities = Activity::with('user')
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard.index', compact(
            'stats', 'regLabels', 'regData', 'actData',
            'topSports', 'recentUsers', 'recentActivities'
        ));
    }
}
