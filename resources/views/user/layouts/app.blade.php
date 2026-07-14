<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — FlexBatir</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: '#3B82F6' } } }
        }
    </script>
    <style>
        body { background: #0a0a0a; color: #f1f5f9; }
        .sidebar { background: #1a1a1a; border-right: 1px solid #2a2a2a; }
        .card { background: #1a1a1a; border: 1px solid #2a2a2a; }
        .nav-link { transition: all 0.15s; }
        .nav-link:hover { background: #2a2a2a; color: white; }
        .nav-link.active { background: #3B82F6; color: white; }
        .avatar-ring { ring: 2px; ring-color: #3B82F6; }
    </style>
</head>
<body class="min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="sidebar w-60 min-h-screen flex flex-col fixed top-0 left-0 z-40">
        {{-- Logo --}}
        <div class="px-6 py-5 border-b border-[#2a2a2a]">
            <a href="{{ route('user.dashboard') }}" class="text-xl font-bold text-white tracking-tight">
                <span class="text-brand">Flex</span>Batir
            </a>
        </div>

        {{-- User mini profile --}}
        <div class="px-4 py-4 border-b border-[#2a2a2a]">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-brand/20 flex items-center justify-center text-brand font-bold text-sm flex-shrink-0 overflow-hidden">
                    @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" alt="" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <a href="{{ route('user.dashboard') }}"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-400
                      {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('user.profile') }}"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-400
                      {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Profil Saya
            </a>

            <div class="pt-2 pb-1 px-3">
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-widest">Aktivitas</p>
            </div>

            <a href="#"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-400">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Riwayat Aktivitas
            </a>

            <a href="#"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-400">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Statistik
            </a>
        </nav>

        {{-- Logout --}}
        <div class="px-3 py-3 border-t border-[#2a2a2a]">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="nav-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-400 text-left">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="ml-60 flex-1 flex flex-col min-h-screen">
        {{-- Topbar --}}
        <header class="h-14 border-b border-[#2a2a2a] flex items-center justify-between px-6 bg-[#0a0a0a] sticky top-0 z-30">
            <h1 class="text-sm font-semibold text-slate-300">@yield('page-title', 'Dashboard')</h1>
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-500">{{ auth()->user()->name }}</span>
                <div class="w-7 h-7 rounded-full bg-brand/20 flex items-center justify-center text-brand font-bold text-xs overflow-hidden">
                    @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" alt="" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
