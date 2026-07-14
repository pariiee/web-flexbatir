<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — FlexBatir</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: '#3B82F6' } } }
        }
    </script>
    <style>
        body { background: #0a0a0a; }
        .input-field {
            background: #111;
            border: 1px solid #2a2a2a;
            color: #f1f5f9;
            transition: border-color 0.2s;
        }
        .input-field:focus {
            outline: none;
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        }
        .input-field::placeholder { color: #4b5563; }
        .input-field.error { border-color: #f87171; }
        .card { background: #1a1a1a; border: 1px solid #2a2a2a; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4 py-8">

    <div class="w-full max-w-sm">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-block">
                <h1 class="text-3xl font-bold text-white tracking-tight">
                    <span class="text-brand">Flex</span>Batir
                </h1>
            </a>
            <p class="text-slate-500 text-sm mt-1">Buat akun baru</p>
        </div>

        {{-- Card --}}
        <div class="card rounded-2xl p-8">

            {{-- Error global --}}
            @if ($errors->any())
                <div class="mb-5 text-sm text-red-400 bg-red-400/10 border border-red-400/20 rounded-lg px-4 py-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf

                {{-- Nama --}}
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5" for="name">
                        Nama Lengkap
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autocomplete="name"
                        placeholder="John Doe"
                        class="input-field w-full rounded-lg px-4 py-2.5 text-sm @error('name') error @enderror"
                    >
                </div>

                {{-- Username --}}
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5" for="username">
                        Username
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username') }}"
                        required
                        autocomplete="username"
                        placeholder="johndoe"
                        class="input-field w-full rounded-lg px-4 py-2.5 text-sm @error('username') error @enderror"
                    >
                    @error('username')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5" for="email">
                        Email
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        placeholder="kamu@email.com"
                        class="input-field w-full rounded-lg px-4 py-2.5 text-sm @error('email') error @enderror"
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5" for="password">
                        Password
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="Min. 8 karakter"
                            class="input-field w-full rounded-lg px-4 py-2.5 text-sm pr-10 @error('password') error @enderror"
                        >
                        <button type="button" onclick="togglePassword('password', 'eye-show', 'eye-hide')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300">
                            <svg id="eye-show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eye-hide" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Konfirmasi Password --}}
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5" for="password_confirmation">
                        Konfirmasi Password
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Ulangi password"
                            class="input-field w-full rounded-lg px-4 py-2.5 text-sm pr-10"
                        >
                        <button type="button" onclick="togglePassword('password_confirmation', 'eye-show2', 'eye-hide2')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300">
                            <svg id="eye-show2" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eye-hide2" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full bg-brand hover:bg-blue-500 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors mt-2">
                    Buat Akun
                </button>
            </form>
        </div>

        {{-- Link ke login --}}
        <p class="text-center text-sm text-slate-500 mt-5">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-brand hover:text-blue-400 font-medium transition-colors">Masuk</a>
        </p>

        <p class="text-center text-xs text-slate-600 mt-4">
            FlexBatir &copy; {{ date('Y') }}
        </p>
    </div>

    <script>
        function togglePassword(inputId, showId, hideId) {
            const input = document.getElementById(inputId);
            const show  = document.getElementById(showId);
            const hide  = document.getElementById(hideId);
            if (input.type === 'password') {
                input.type = 'text';
                show.classList.add('hidden');
                hide.classList.remove('hidden');
            } else {
                input.type = 'password';
                show.classList.remove('hidden');
                hide.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
