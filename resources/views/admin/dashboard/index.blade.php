@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card rounded-xl p-5">
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Total User</p>
            <p class="text-3xl font-bold text-white">{{ number_format($stats['total_users']) }}</p>
            <p class="text-xs text-slate-500 mt-1">+{{ $stats['new_users_today'] }} hari ini</p>
        </div>
        <div class="card rounded-xl p-5">
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">User Baru (7 hari)</p>
            <p class="text-3xl font-bold text-brand">{{ number_format($stats['new_users_week']) }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $stats['banned_users'] }} dibanned</p>
        </div>
        <div class="card rounded-xl p-5">
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Total Aktivitas</p>
            <p class="text-3xl font-bold text-white">{{ number_format($stats['total_activities']) }}</p>
            <p class="text-xs text-slate-500 mt-1">+{{ $stats['activities_today'] }} hari ini</p>
        </div>
        <div class="card rounded-xl p-5">
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Live Beacon Aktif</p>
            <p class="text-3xl font-bold text-green-400">{{ $stats['active_beacons'] }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $stats['total_posts'] }} total post</p>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Registrasi Chart --}}
        <div class="card rounded-xl p-5">
            <h3 class="text-sm font-semibold text-slate-300 mb-4">Registrasi User (14 Hari)</h3>
            <canvas id="regChart" height="120"></canvas>
        </div>

        {{-- Aktivitas Chart --}}
        <div class="card rounded-xl p-5">
            <h3 class="text-sm font-semibold text-slate-300 mb-4">Aktivitas (14 Hari)</h3>
            <canvas id="actChart" height="120"></canvas>
        </div>
    </div>

    {{-- Top Sports & Recent Users --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Top Sports --}}
        <div class="card rounded-xl p-5">
            <h3 class="text-sm font-semibold text-slate-300 mb-4">Top Jenis Olahraga</h3>
            <div class="space-y-3">
                @foreach($topSports as $sport)
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-400 w-24 truncate capitalize">{{ str_replace('_', ' ', $sport->type) }}</span>
                    <div class="flex-1 bg-[#2a2a2a] rounded-full h-2">
                        <div class="bg-brand h-2 rounded-full"
                             style="width: {{ $topSports->first()->count > 0 ? round($sport->count / $topSports->first()->count * 100) : 0 }}%">
                        </div>
                    </div>
                    <span class="text-xs text-slate-400 w-8 text-right">{{ $sport->count }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Recent Users --}}
        <div class="card rounded-xl p-5 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-300">User Terbaru</h3>
                <a href="{{ route('admin.users.index') }}" class="text-xs text-brand hover:underline">Lihat semua</a>
            </div>
            <div class="space-y-3">
                @foreach($recentUsers as $user)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#2a2a2a] flex items-center justify-center flex-shrink-0 overflow-hidden">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="" class="w-8 h-8 object-cover rounded-full">
                        @else
                            <span class="text-xs text-slate-400 font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-white font-medium truncate">{{ $user->name }}</p>
                        <p class="text-xs text-slate-500 truncate">@{{ $user->username }}</p>
                    </div>
                    @if($user->is_admin)
                        <span class="text-xs bg-brand/20 text-brand px-2 py-0.5 rounded-full">Admin</span>
                    @elseif($user->is_banned)
                        <span class="text-xs bg-red-500/20 text-red-400 px-2 py-0.5 rounded-full">Banned</span>
                    @endif
                    <span class="text-xs text-slate-600">{{ $user->created_at->diffForHumans() }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent Activities --}}
    <div class="card rounded-xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-slate-300">Aktivitas Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-slate-500 border-b border-[#2a2a2a]">
                        <th class="text-left pb-3 font-medium">User</th>
                        <th class="text-left pb-3 font-medium">Judul</th>
                        <th class="text-left pb-3 font-medium">Jenis</th>
                        <th class="text-right pb-3 font-medium">Jarak</th>
                        <th class="text-right pb-3 font-medium">Durasi</th>
                        <th class="text-right pb-3 font-medium">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2a2a2a]">
                    @foreach($recentActivities as $activity)
                    <tr class="text-slate-300">
                        <td class="py-2.5 text-xs text-slate-400">{{ $activity->user?->username ?? '-' }}</td>
                        <td class="py-2.5 font-medium truncate max-w-[180px]">{{ $activity->title ?? 'Untitled' }}</td>
                        <td class="py-2.5">
                            <span class="capitalize text-xs bg-[#2a2a2a] px-2 py-0.5 rounded-full">
                                {{ str_replace('_', ' ', $activity->type) }}
                            </span>
                        </td>
                        <td class="py-2.5 text-right text-xs">
                            {{ $activity->distance_km ? $activity->distance_km . ' km' : '-' }}
                        </td>
                        <td class="py-2.5 text-right text-xs">{{ $activity->duration_formatted ?? '-' }}</td>
                        <td class="py-2.5 text-right text-xs text-slate-500">{{ $activity->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const chartDefaults = {
    borderColor: '#3B82F6',
    backgroundColor: 'rgba(59,130,246,0.08)',
    borderWidth: 2,
    pointRadius: 3,
    pointBackgroundColor: '#3B82F6',
    tension: 0.4,
    fill: true,
};

const gridColor = '#2a2a2a';
const tickColor = '#888888';
const labels = @json($regLabels);

new Chart(document.getElementById('regChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [{ ...chartDefaults, label: 'Registrasi', data: @json($regData) }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: gridColor }, ticks: { color: tickColor, font: { size: 10 } } },
            y: { grid: { color: gridColor }, ticks: { color: tickColor, font: { size: 10 }, stepSize: 1 } },
        }
    }
});

new Chart(document.getElementById('actChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [{ ...chartDefaults, label: 'Aktivitas', data: @json($actData),
            borderColor: '#16A34A', backgroundColor: 'rgba(22,163,74,0.08)',
            pointBackgroundColor: '#16A34A' }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: gridColor }, ticks: { color: tickColor, font: { size: 10 } } },
            y: { grid: { color: gridColor }, ticks: { color: tickColor, font: { size: 10 }, stepSize: 1 } },
        }
    }
});
</script>
@endsection
