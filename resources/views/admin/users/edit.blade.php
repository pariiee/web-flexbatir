@extends('admin.layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit Profil User')

@section('content')
<div class="max-w-2xl space-y-5">

    {{-- Back --}}
    <a href="{{ route('admin.users.show', $user) }}"
       class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Detail User
    </a>

    {{-- Flash --}}
    @if($errors->any())
    <div class="bg-red-500/10 border border-red-500/30 text-red-400 rounded-lg px-4 py-3 text-sm space-y-1">
        @foreach($errors->all() as $error)
            <p>• {{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Identity Card --}}
        <div class="card rounded-xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-white border-b border-[#2a2a2a] pb-3">Identitas</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Nama --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="input-field w-full rounded-lg px-4 py-2.5 text-sm @error('name') border-red-500 @enderror">
                </div>

                {{-- Username --}}
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Username</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                           class="input-field w-full rounded-lg px-4 py-2.5 text-sm @error('username') border-red-500 @enderror">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="input-field w-full rounded-lg px-4 py-2.5 text-sm @error('email') border-red-500 @enderror">
                </div>

                {{-- Password --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">
                        Password Baru
                        <span class="text-slate-600 font-normal ml-1">(kosongkan jika tidak ingin mengubah)</span>
                    </label>
                    <input type="password" name="password" placeholder="Min. 8 karakter"
                           class="input-field w-full rounded-lg px-4 py-2.5 text-sm @error('password') border-red-500 @enderror">
                </div>
            </div>
        </div>

        {{-- Profile Card --}}
        <div class="card rounded-xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-white border-b border-[#2a2a2a] pb-3">Profil</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Bio --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Bio</label>
                    <textarea name="bio" rows="2" placeholder="Bio singkat..."
                              class="input-field w-full rounded-lg px-4 py-2.5 text-sm resize-none">{{ old('bio', $user->bio) }}</textarea>
                </div>

                {{-- Lokasi --}}
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Lokasi</label>
                    <input type="text" name="location" value="{{ old('location', $user->location) }}"
                           placeholder="Jakarta, Indonesia"
                           class="input-field w-full rounded-lg px-4 py-2.5 text-sm">
                </div>

                {{-- Gender --}}
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Gender</label>
                    <select name="gender" class="input-field w-full rounded-lg px-4 py-2.5 text-sm">
                        <option value="">-- Pilih --</option>
                        <option value="male"   {{ old('gender', $user->gender) === 'male'   ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Perempuan</option>
                        <option value="other"  {{ old('gender', $user->gender) === 'other'  ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                {{-- Berat --}}
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Berat (kg)</label>
                    <input type="number" name="weight" value="{{ old('weight', $user->weight) }}"
                           min="1" max="500" step="0.1" placeholder="70"
                           class="input-field w-full rounded-lg px-4 py-2.5 text-sm">
                </div>

                {{-- Tinggi --}}
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Tinggi (cm)</label>
                    <input type="number" name="height" value="{{ old('height', $user->height) }}"
                           min="1" max="300" step="0.1" placeholder="170"
                           class="input-field w-full rounded-lg px-4 py-2.5 text-sm">
                </div>

                {{-- Measurement --}}
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Satuan Ukuran</label>
                    <select name="measurement_preference" class="input-field w-full rounded-lg px-4 py-2.5 text-sm">
                        <option value="metric"   {{ old('measurement_preference', $user->measurement_preference) === 'metric'   ? 'selected' : '' }}>Metric (km, kg)</option>
                        <option value="imperial" {{ old('measurement_preference', $user->measurement_preference) === 'imperial' ? 'selected' : '' }}>Imperial (mi, lbs)</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex gap-3">
            <button type="submit"
                    class="flex-1 bg-brand hover:bg-blue-500 text-white font-semibold py-3 rounded-xl text-sm transition-colors">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.users.show', $user) }}"
               class="px-6 py-3 rounded-xl border border-[#2a2a2a] text-slate-400 hover:text-white text-sm font-medium transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
