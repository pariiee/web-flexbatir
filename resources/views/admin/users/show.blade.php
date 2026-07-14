@extends('admin.layouts.app')

@section('title', $user->name)
@section('page-title', 'Detail User')

@section('content')
<div class="space-y-5">

    {{-- Back --}}
    <a href="{{ route('admin.users.index') }}"
       class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Daftar User
    </a>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-500/10 border border-green-500/30 text-green-400 rounded-lg px-4 py-3 text-sm">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-500/10 border border-red-500/30 text-red-400 rounded-lg px-4 py-3 text-sm">
        {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Profile Card --}}
        <div class="card rounded-xl p-6 space-y-4">
            {{-- Avatar + Name --}}
            <div class="flex flex-col items-center text-center gap-3">
                <div class="w-20 h-20 rounded-full bg-[#2a2a2a] flex items-center justify-center overflow-hidden">
                    @if($user->avatar)
                        <img src="{{ $user->avatar_url }}" alt="" class="w-20 h-20 object-cover">
                    @else
                        <span class="text-2xl font-bold text-slate-400">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white flex items-center justify-center gap-1.5">
                        {{ $user->name }}
                        @if($user->is_verified)
                            @include('partials.verified-badge', ['size' => 22])
                        @endif
                    </h2>
                    <p class="text-sm text-slate-400">{{ $user->username ? '@'.$user->username : '-' }}</p>
                    <div class="flex items-center justify-center gap-1.5 flex-wrap mt-1">
                        @if($user->is_admin)
                            <span class="text-xs bg-brand/20 text-brand px-2.5 py-0.5 rounded-full">Admin</span>
                        @endif
                        @if($user->is_banned)
                            <span class="text-xs bg-red-500/20 text-red-400 px-2.5 py-0.5 rounded-full">Banned</span>
                        @else
                            <span class="text-xs bg-green-500/20 text-green-400 px-2.5 py-0.5 rounded-full">Aktif</span>
                        @endif
                        @if($user->is_verified)
                            <span class="text-xs bg-brand/20 text-brand px-2.5 py-0.5 rounded-full flex items-center gap-1">
                                @include('partials.verified-badge', ['size' => 12])
                                Verified
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Info --}}
            <div class="space-y-2 text-sm border-t border-[#2a2a2a] pt-4">
                <div class="flex justify-between">
                    <span class="text-slate-500">Email</span>
                    <span class="text-slate-300 text-xs">{{ $user->email }}</span>
                </div>
                @if($user->location)
                <div class="flex justify-between">
                    <span class="text-slate-500">Lokasi</span>
                    <span class="text-slate-300 text-xs">{{ $user->location }}</span>
                </div>
                @endif
                @if($user->gender)
                <div class="flex justify-between">
                    <span class="text-slate-500">Gender</span>
                    <span class="text-slate-300 text-xs capitalize">{{ $user->gender }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-slate-500">Followers</span>
                    <span class="text-slate-300">{{ number_format($user->followers_count) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Following</span>
                    <span class="text-slate-300">{{ number_format($user->following_count) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Bergabung</span>
                    <span class="text-slate-300 text-xs">{{ $user->created_at->format('d M Y') }}</span>
                </div>
            </div>

            @if($user->bio)
            <div class="border-t border-[#2a2a2a] pt-4">
                <p class="text-xs text-slate-500 mb-1">Bio</p>
                <p class="text-sm text-slate-300 leading-relaxed">{{ $user->bio }}</p>
            </div>
            @endif

            @if($user->is_banned && $user->ban_reason)
            <div class="border-t border-[#2a2a2a] pt-4">
                <p class="text-xs text-red-400 mb-1">Alasan Ban</p>
                <p class="text-xs text-slate-400">{{ $user->ban_reason }}</p>
                <p class="text-xs text-slate-600 mt-1">{{ $user->banned_at?->format('d M Y H:i') }}</p>
            </div>
            @endif

            {{-- Actions --}}
            <div class="border-t border-[#2a2a2a] pt-4 space-y-2">

                {{-- Edit profil --}}
                <a href="{{ route('admin.users.edit', $user) }}"
                   class="w-full py-2 text-sm font-medium rounded-lg border border-slate-500/50 text-slate-300 hover:bg-slate-500/10 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Profil
                </a>

                {{-- Toggle verified --}}
                <form method="POST" action="{{ route('admin.users.toggle-verified', $user) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="w-full py-2 text-sm font-medium rounded-lg transition-colors flex items-center justify-center gap-2
                                {{ $user->is_verified
                                    ? 'border border-brand/50 text-brand hover:bg-brand/10'
                                    : 'border border-slate-500/30 text-slate-400 hover:bg-slate-500/10' }}"
                            onclick="return confirm('{{ $user->is_verified ? 'Cabut verified?' : 'Verifikasi akun ini?' }}')">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ $user->is_verified ? 'Cabut Verified' : 'Beri Centang Biru' }}
                    </button>
                </form>

                {{-- Toggle admin --}}
                <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="w-full py-2 text-sm font-medium rounded-lg border border-brand text-brand hover:bg-brand hover:text-white transition-colors"
                            onclick="return confirm('{{ $user->is_admin ? 'Cabut hak admin?' : 'Jadikan admin?' }}')">
                        {{ $user->is_admin ? 'Cabut Hak Admin' : 'Jadikan Admin' }}
                    </button>
                </form>

                @if(!$user->is_banned)
                <button onclick="document.getElementById('banForm').classList.toggle('hidden')"
                        class="w-full py-2 text-sm font-medium rounded-lg border border-red-500/50 text-red-400 hover:bg-red-500/10 transition-colors">
                    Ban User
                </button>
                <div id="banForm" class="hidden space-y-2">
                    <form method="POST" action="{{ route('admin.users.ban', $user) }}">
                        @csrf
                        <textarea name="reason" rows="2" required
                                  placeholder="Alasan ban..."
                                  class="input-field w-full rounded-lg px-3 py-2 text-xs resize-none mb-2"></textarea>
                        <button type="submit"
                                class="w-full py-2 text-xs font-medium rounded-lg bg-red-600 hover:bg-red-700 text-white transition-colors">
                            Konfirmasi Ban
                        </button>
                    </form>
                </div>
                @else
                <form method="POST" action="{{ route('admin.users.unban', $user) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="w-full py-2 text-sm font-medium rounded-lg border border-green-500/50 text-green-400 hover:bg-green-500/10 transition-colors">
                        Unban User
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Stats + Activities --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="card rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-white">{{ $stats['total_activities'] }}</p>
                    <p class="text-xs text-slate-500 mt-1">Aktivitas</p>
                </div>
                <div class="card rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-brand">{{ $stats['total_distance_km'] }}</p>
                    <p class="text-xs text-slate-500 mt-1">km Total</p>
                </div>
                <div class="card rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-green-400">{{ $stats['total_duration_h'] }}</p>
                    <p class="text-xs text-slate-500 mt-1">jam Latihan</p>
                </div>
            </div>

            {{-- Recent Activities --}}
            <div class="card rounded-xl p-5">
                <h3 class="text-sm font-semibold text-slate-300 mb-4">Aktivitas Terbaru</h3>
                @if($recentActivities->isEmpty())
                    <p class="text-sm text-slate-500 text-center py-6">Belum ada aktivitas.</p>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs text-slate-500 border-b border-[#2a2a2a]">
                                <th class="text-left pb-3 font-medium">Judul</th>
                                <th class="text-left pb-3 font-medium">Jenis</th>
                                <th class="text-right pb-3 font-medium">Jarak</th>
                                <th class="text-right pb-3 font-medium">Durasi</th>
                                <th class="text-right pb-3 font-medium">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#2a2a2a]">
                            @foreach($recentActivities as $activity)
                            <tr class="text-slate-300">
                                <td class="py-2.5 font-medium text-sm truncate max-w-[160px]">{{ $activity->title ?? 'Untitled' }}</td>
                                <td class="py-2.5">
                                    <span class="capitalize text-xs bg-[#2a2a2a] px-2 py-0.5 rounded-full">
                                        {{ str_replace('_', ' ', $activity->type) }}
                                    </span>
                                </td>
                                <td class="py-2.5 text-right text-xs">{{ $activity->distance_km ? $activity->distance_km . ' km' : '-' }}</td>
                                <td class="py-2.5 text-right text-xs">{{ $activity->duration_formatted ?? '-' }}</td>
                                <td class="py-2.5 text-right text-xs text-slate-500">
                                    {{ $activity->started_at?->format('d M Y') ?? $activity->created_at->format('d M Y') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
