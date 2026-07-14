@extends('user.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Saya')

@section('content')
<div class="space-y-6">

    {{-- Welcome banner --}}
    <div class="card rounded-2xl p-6 flex items-center gap-5">
        <div class="w-14 h-14 rounded-full bg-brand/20 flex items-center justify-center text-brand font-bold text-xl flex-shrink-0 overflow-hidden">
            @if($user->avatar)
                <img src="{{ $user->avatar_url }}" alt="" class="w-full h-full object-cover">
            @else
                {{ strtoupper(substr($user->name, 0, 1)) }}
            @endif
        </div>
        <div>
            <h2 class="text-lg font-bold text-white">Halo, {{ $user->name }}! 👋</h2>
            <p class="text-sm text-slate-500">
                {{ $user->username ? '@'.$user->username.' · ' : '' }}{{ $followersCount }} followers · {{ $followingCount }} following
            </p>
        </div>
        <div class="ml-auto hidden sm:flex gap-2">
            @if($user->is_admin)
                <a href="{{ route('admin.dashboard') }}"
                   class="text-xs bg-yellow-500/10 text-yellow-400 border border-yellow-400/20 px-3 py-1.5 rounded-lg font-medium hover:bg-yellow-500/20 transition">
                    ⚡ Admin Panel
                </a>
            @endif
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card rounded-xl p-5">
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Total Aktivitas</p>
            <p class="text-3xl font-bold text-white">{{ number_format($stats->total_activities) }}</p>
            <p class="text-xs text-slate-500 mt-1">semua waktu</p>
        </div>
        <div class="card rounded-xl p-5">
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Total Jarak</p>
            <p class="text-3xl font-bold text-brand">{{ number_format($stats->total_distance / 1000, 1) }}</p>
            <p class="text-xs text-slate-500 mt-1">km ditempuh</p>
        </div>
        <div class="card rounded-xl p-5">
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Total Durasi</p>
            @php
                $totalH = intdiv($stats->total_duration, 3600);
                $totalM = intdiv($stats->total_duration % 3600, 60);
            @endphp
            <p class="text-3xl font-bold text-white">{{ $totalH }}</p>
            <p class="text-xs text-slate-500 mt-1">jam {{ $totalM }} menit</p>
        </div>
        <div class="card rounded-xl p-5">
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Total Kalori</p>
            <p class="text-3xl font-bold text-orange-400">{{ number_format($stats->total_calories) }}</p>
            <p class="text-xs text-slate-500 mt-1">kkal dibakar</p>
        </div>
    </div>

    {{-- Chart + Top Types --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Activity Chart --}}
        <div class="card rounded-xl p-5 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-300">Aktivitas 30 Hari Terakhir</h3>
                <div class="flex gap-3 text-xs">
                    <span class="flex items-center gap-1.5 text-slate-500">
                        <span class="w-2 h-2 rounded-full bg-brand inline-block"></span> Jumlah
                    </span>
                    <span class="flex items-center gap-1.5 text-slate-500">
                        <span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span> Jarak (km)
                    </span>
                </div>
            </div>
            <canvas id="activityChart" height="100"></canvas>
        </div>

        {{-- Top Types --}}
        <div class="card rounded-xl p-5">
            <h3 class="text-sm font-semibold text-slate-300 mb-4">Olahraga Favorit</h3>
            @if($topTypes->isEmpty())
                <p class="text-sm text-slate-600 text-center py-6">Belum ada aktivitas</p>
            @else
                <div class="space-y-3">
                    @foreach($topTypes as $type)
                    @php
                        $icons = [
                            'run' => '🏃', 'ride' => '🚴', 'walk' => '🚶',
                            'swim' => '🏊', 'hike' => '🥾', 'trail_run' => '🌲',
                            'workout' => '💪', 'yoga' => '🧘', 'mountain_bike' => '🚵',
                        ];
                        $icon = $icons[$type->type] ?? '⚡';
                        $labels = [
                            'run' => 'Lari', 'ride' => 'Bersepeda', 'walk' => 'Jalan',
                            'swim' => 'Renang', 'hike' => 'Hiking', 'trail_run' => 'Trail Run',
                            'workout' => 'Workout', 'yoga' => 'Yoga', 'mountain_bike' => 'MTB',
                        ];
                        $label = $labels[$type->type] ?? ucfirst($type->type);
                    @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">{{ $icon }}</span>
                            <span class="text-sm text-slate-300">{{ $label }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-bold text-white">{{ $type->count }}x</span>
                            <span class="text-xs text-slate-500 ml-1">{{ number_format($type->total_distance / 1000, 1) }} km</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- PR + Recent Activities --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Personal Records --}}
        <div class="card rounded-xl p-5">
            <h3 class="text-sm font-semibold text-slate-300 mb-4">🏆 Personal Records Terbaik</h3>
            @if($prs->isEmpty())
                <p class="text-sm text-slate-600 text-center py-6">Belum ada aktivitas</p>
            @else
                <div class="space-y-3">
                    @foreach($prs as $pr)
                    @php
                        $durationMin = intdiv($pr->duration, 60);
                        $durationSec = $pr->duration % 60;
                        $pacePerKm   = $pr->distance > 0 ? ($pr->duration / $pr->distance) * 1000 : 0;
                        $paceMin     = intdiv($pacePerKm, 60);
                        $paceSec     = intdiv($pacePerKm % 60, 1);
                    @endphp
                    <div class="flex items-center justify-between py-2 border-b border-[#2a2a2a] last:border-0">
                        <div>
                            <p class="text-sm font-medium text-white">{{ $pr->title }}</p>
                            <p class="text-xs text-slate-500">{{ number_format($pr->distance / 1000, 2) }} km</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-brand">{{ $paceMin }}:{{ str_pad($paceSec, 2, '0', STR_PAD_LEFT) }} /km</p>
                            <p class="text-xs text-slate-500">{{ $durationMin }}m {{ $durationSec }}s</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Recent Activities --}}
        <div class="card rounded-xl p-5">
            <h3 class="text-sm font-semibold text-slate-300 mb-4">Aktivitas Terbaru</h3>
            @if($recentActivities->isEmpty())
                <p class="text-sm text-slate-600 text-center py-6">Belum ada aktivitas. Yuk mulai tracking!</p>
            @else
                <div class="space-y-3">
                    @foreach($recentActivities as $act)
                    @php
                        $icons = ['run'=>'🏃','ride'=>'🚴','walk'=>'🚶','swim'=>'🏊','hike'=>'🥾','workout'=>'💪'];
                        $icon  = $icons[$act->type] ?? '⚡';
                        $distKm = number_format($act->distance / 1000, 2);
                        $durMin = intdiv($act->duration ?? 0, 60);
                        $durSec = ($act->duration ?? 0) % 60;
                        $date   = \Carbon\Carbon::parse($act->started_at)->format('d M Y');
                    @endphp
                    <div class="flex items-center gap-3 py-2 border-b border-[#2a2a2a] last:border-0">
                        <span class="text-xl flex-shrink-0">{{ $icon }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ $act->title }}</p>
                            <p class="text-xs text-slate-500">{{ $date }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-bold text-white">{{ $distKm }} km</p>
                            <p class="text-xs text-slate-500">{{ $durMin }}m {{ $durSec }}s</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels    = @json($labels);
const countData = @json($countData);
const distData  = @json($distData);
const gridColor = 'rgba(42,42,42,0.8)';
const tickColor = '#6b7280';

new Chart(document.getElementById('activityChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            {
                label: 'Aktivitas',
                data: countData,
                backgroundColor: 'rgba(59,130,246,0.5)',
                borderColor: '#3B82F6',
                borderWidth: 1,
                borderRadius: 4,
                yAxisID: 'y',
            },
            {
                label: 'Jarak (km)',
                data: distData,
                type: 'line',
                borderColor: '#16A34A',
                backgroundColor: 'rgba(22,163,74,0.08)',
                borderWidth: 2,
                pointRadius: 0,
                fill: true,
                tension: 0.4,
                yAxisID: 'y1',
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: gridColor }, ticks: { color: tickColor, font: { size: 10 }, maxTicksLimit: 10 } },
            y: { grid: { color: gridColor }, ticks: { color: tickColor, font: { size: 10 }, stepSize: 1 }, position: 'left' },
            y1: { grid: { display: false }, ticks: { color: tickColor, font: { size: 10 } }, position: 'right' },
        }
    }
});
</script>
@endpush
