@extends('user.layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
<div class="max-w-2xl space-y-6">

    {{-- Profile card --}}
    <div class="card rounded-2xl p-6">
        <div class="flex items-center gap-5 mb-6">
            <div class="w-16 h-16 rounded-full bg-brand/20 flex items-center justify-center text-brand font-bold text-2xl flex-shrink-0 overflow-hidden">
                @if($user->avatar)
                    <img src="{{ $user->avatar }}" alt="" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                @endif
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">{{ $user->name }}</h2>
                @if($user->username)
                    <p class="text-sm text-slate-500">@{{ $user->username }}</p>
                @endif
                @if($user->bio)
                    <p class="text-sm text-slate-400 mt-1">{{ $user->bio }}</p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-xs text-slate-500 mb-0.5">Email</p>
                <p class="text-white font-medium">{{ $user->email }}</p>
            </div>
            @if($user->location)
            <div>
                <p class="text-xs text-slate-500 mb-0.5">Lokasi</p>
                <p class="text-white font-medium">{{ $user->location }}</p>
            </div>
            @endif
            @if($user->gender)
            <div>
                <p class="text-xs text-slate-500 mb-0.5">Gender</p>
                <p class="text-white font-medium">{{ ucfirst($user->gender) }}</p>
            </div>
            @endif
            @if($user->weight)
            <div>
                <p class="text-xs text-slate-500 mb-0.5">Berat</p>
                <p class="text-white font-medium">{{ $user->weight }} kg</p>
            </div>
            @endif
            @if($user->height)
            <div>
                <p class="text-xs text-slate-500 mb-0.5">Tinggi</p>
                <p class="text-white font-medium">{{ $user->height }} cm</p>
            </div>
            @endif
            <div>
                <p class="text-xs text-slate-500 mb-0.5">Bergabung</p>
                <p class="text-white font-medium">{{ $user->created_at->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Badges --}}
    <div class="card rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-slate-300 mb-4">Status Akun</h3>
        <div class="flex flex-wrap gap-2">
            <span class="text-xs px-3 py-1.5 rounded-full font-medium bg-green-400/10 text-green-400 border border-green-400/20">
                ✓ Aktif
            </span>
            @if($user->is_admin)
            <span class="text-xs px-3 py-1.5 rounded-full font-medium bg-yellow-400/10 text-yellow-400 border border-yellow-400/20">
                ⚡ Admin
            </span>
            @endif
            @if($user->measurement_preference)
            <span class="text-xs px-3 py-1.5 rounded-full font-medium bg-brand/10 text-brand border border-brand/20">
                {{ strtoupper($user->measurement_preference) }}
            </span>
            @endif
        </div>
    </div>

    {{-- Untuk edit profil, gunakan aplikasi mobile --}}
    <div class="card rounded-2xl p-5 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-brand/10 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-medium text-white">Edit profil via aplikasi</p>
            <p class="text-xs text-slate-500">Untuk mengubah data profil, gunakan aplikasi FlexBatir di smartphone kamu.</p>
        </div>
    </div>

</div>
@endsection
