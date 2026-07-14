@extends('admin.layouts.app')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan Sistem')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Alert sukses --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400 text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Alert error validasi --}}
    @if($errors->any())
    <div class="px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm space-y-1">
        @foreach($errors->all() as $error)
            <p>• {{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" autocomplete="off">
        @csrf
        @method('PUT')

        {{-- AI Configuration Card --}}
        <div class="card rounded-xl p-6 space-y-5">
            <div class="flex items-center gap-3 pb-4 border-b border-[#2a2a2a]">
                <div class="w-8 h-8 rounded-lg bg-brand/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.347.347A3.495 3.495 0 0112 18.5a3.495 3.495 0 01-2.121-.853l-.347-.347z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-white">Konfigurasi AI</h2>
                    <p class="text-xs text-slate-500 mt-0.5">OpenAgentic — endpoint: openagentic.id/api/v1</p>
                </div>
            </div>

            @foreach($settings as $setting)
            <div class="space-y-1.5">
                <label for="{{ $setting['key'] }}" class="block text-sm font-medium text-slate-300">
                    {{ $setting['label'] }}
                    @if($setting['is_encrypted'])
                        <span class="ml-1.5 text-xs text-slate-500 font-normal">
                            <svg class="w-3 h-3 inline-block mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            terenkripsi
                        </span>
                    @endif
                </label>

                @if($setting['type'] === 'select')
                    <select
                        id="{{ $setting['key'] }}"
                        name="{{ $setting['key'] }}"
                        class="input-field w-full rounded-lg px-3 py-2.5 text-sm transition-colors">
                        <option value="">— Pilih model —</option>
                        @foreach($setting['options'] as $opt)
                            <option value="{{ $opt }}"
                                {{ $setting['current_value'] === $opt ? 'selected' : '' }}>
                                {{ $opt }}
                            </option>
                        @endforeach
                    </select>

                @elseif($setting['type'] === 'password')
                    <div class="relative">
                        <input
                            type="password"
                            id="{{ $setting['key'] }}"
                            name="{{ $setting['key'] }}"
                            placeholder="{{ $setting['has_value'] ? 'Sudah dikonfigurasi — kosongkan untuk tidak mengubah' : $setting['placeholder'] }}"
                            class="input-field w-full rounded-lg px-3 py-2.5 pr-10 text-sm transition-colors"
                            autocomplete="new-password">
                        {{-- Toggle visibility --}}
                        <button type="button"
                            onclick="togglePwd('{{ $setting['key'] }}')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300">
                            <svg id="eye-{{ $setting['key'] }}" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @if($setting['has_value'])
                    <p class="text-xs text-green-500 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Sudah dikonfigurasi
                    </p>
                    @endif

                @else
                    <input
                        type="text"
                        id="{{ $setting['key'] }}"
                        name="{{ $setting['key'] }}"
                        value="{{ old($setting['key'], $setting['current_value']) }}"
                        placeholder="{{ $setting['placeholder'] }}"
                        class="input-field w-full rounded-lg px-3 py-2.5 text-sm transition-colors">
                @endif
            </div>
            @endforeach
        </div>

        {{-- Fallback order info --}}
        <div class="card rounded-xl p-5">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Urutan Fallback Model Otomatis</h3>
            <ol class="space-y-2">
                @foreach(['claude-sonnet-4.5', 'claude-sonnet-4.5-1m', 'claude-sonnet-4.5-thinking', 'deepseek-v4-flash', 'minimax-m2.5'] as $i => $model)
                <li class="flex items-center gap-3 text-sm">
                    <span class="w-5 h-5 rounded-full bg-[#2a2a2a] flex items-center justify-center text-xs font-bold text-slate-400">
                        {{ $i + 1 }}
                    </span>
                    <code class="text-slate-300 font-mono text-xs bg-[#0a0a0a] px-2 py-0.5 rounded">{{ $model }}</code>
                    @if($i === 0)
                        <span class="text-xs text-brand">Primary</span>
                    @elseif($i === 4)
                        <span class="text-xs text-slate-500">Last resort</span>
                    @endif
                </li>
                @endforeach
            </ol>
            <p class="text-xs text-slate-600 mt-3">
                Jika model gagal atau timeout, sistem otomatis berpindah ke model berikutnya.
            </p>
        </div>

        {{-- Submit --}}
        <button type="submit"
            class="w-full bg-brand hover:bg-blue-600 text-white font-semibold text-sm py-3 rounded-xl transition-colors">
            Simpan Pengaturan
        </button>
    </form>
</div>

<script>
function togglePwd(id) {
    const input = document.getElementById(id);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
}
</script>
@endsection
