<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — FlexBatir</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: '#3B82F6',
                    }
                }
            }
        }
    </script>
    <style>
        body { background: #0a0a0a; color: #f1f5f9; }
        .sidebar { background: #1a1a1a; border-right: 1px solid #2a2a2a; }
        .card { background: #1a1a1a; border: 1px solid #2a2a2a; }
        .input-field {
            background: #0a0a0a;
            border: 1px solid #2a2a2a;
            color: #f1f5f9;
        }
        .input-field:focus {
            outline: none;
            border-color: #3B82F6;
            box-shadow: 0 0 0 2px rgba(59,130,246,0.2);
        }
    </style>
</head>
<body class="min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="sidebar w-60 min-h-screen flex flex-col fixed top-0 left-0 z-40">
        {{-- Logo --}}
        <div class="px-6 py-5 border-b border-[#2a2a2a]">
            <span class="text-xl font-bold text-white tracking-tight">
                <span class="text-brand">Flex</span>Batir
            </span>
            <span class="ml-2 text-xs text-slate-500 font-medium uppercase tracking-widest">Admin</span>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 space-y-1">

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                      {{ request()->routeIs('admin.dashboard') ? 'bg-brand text-white' : 'text-slate-400 hover:bg-[#2a2a2a] hover:text-white' }}
                      transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            {{-- Users --}}
            <a href="{{ route('admin.users.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                      {{ request()->routeIs('admin.users.*') ? 'bg-brand text-white' : 'text-slate-400 hover:bg-[#2a2a2a] hover:text-white' }}
                      transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Users
            </a>

            {{-- Divider --}}
            <div class="pt-2 pb-1 px-3">
                <p class="text-xs text-slate-600 uppercase tracking-widest font-medium">Sistem</p>
            </div>

            {{-- Pengaturan --}}
            <a href="{{ route('admin.settings.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                      {{ request()->routeIs('admin.settings.*') ? 'bg-brand text-white' : 'text-slate-400 hover:bg-[#2a2a2a] hover:text-white' }}
                      transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Pengaturan
            </a>
        </nav>

        {{-- Bottom --}}
        <div class="px-4 py-4 border-t border-[#2a2a2a]">
            <p class="text-xs text-slate-600">FlexBatir Admin v1.0</p>
        </div>
    </aside>

    {{-- Main --}}
    <div class="ml-60 flex-1 flex flex-col min-h-screen">
        {{-- Topbar --}}
        <header class="h-14 border-b border-[#2a2a2a] flex items-center justify-between px-6 bg-[#0a0a0a] sticky top-0 z-30">
            <h1 class="text-sm font-semibold text-slate-300">@yield('page-title', 'Dashboard')</h1>
            <div class="flex items-center gap-4">
                <a href="{{ route('user.dashboard') }}"
                   class="text-xs text-slate-500 hover:text-slate-300 transition">
                    ← User View
                </a>
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-yellow-400/20 flex items-center justify-center text-yellow-400 font-bold text-xs overflow-hidden">
                        @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar }}" alt="" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <span class="text-xs text-slate-400">{{ auth()->user()->name }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-xs text-slate-500 hover:text-red-400 transition">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

</body>
</html>
