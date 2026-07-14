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
                    <p class="text-sm text-slate-500">{{ $user->username ? '@'.$user->username : '' }}</p>
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

    {{-- Form Edit Profil --}}
    <div class="card rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-white border-b border-[#2a2a2a] pb-3 mb-5">Edit Profil</h3>

        @if(session('success'))
        <div class="mb-4 text-sm text-green-400 bg-green-400/10 border border-green-400/20 rounded-lg px-4 py-3">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="mb-4 text-sm text-red-400 bg-red-400/10 border border-red-400/20 rounded-lg px-4 py-3 space-y-1">
            @foreach($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('user.profile.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Nama --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full rounded-lg px-4 py-2.5 text-sm text-white"
                           style="background:#111; border:1px solid #2a2a2a; outline:none;"
                           onfocus="this.style.borderColor='#3B82F6'" onblur="this.style.borderColor='#2a2a2a'">
                </div>

                {{-- Username --}}
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Username</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                           class="w-full rounded-lg px-4 py-2.5 text-sm text-white"
                           style="background:#111; border:1px solid #2a2a2a; outline:none;"
                           onfocus="this.style.borderColor='#3B82F6'" onblur="this.style.borderColor='#2a2a2a'">
                </div>

                {{-- Gender --}}
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Gender</label>
                    <select name="gender"
                            class="w-full rounded-lg px-4 py-2.5 text-sm text-white"
                            style="background:#111; border:1px solid #2a2a2a; outline:none;"
                            onfocus="this.style.borderColor='#3B82F6'" onblur="this.style.borderColor='#2a2a2a'">
                        <option value="">-- Pilih --</option>
                        <option value="male"   {{ old('gender', $user->gender) === 'male'   ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Perempuan</option>
                        <option value="other"  {{ old('gender', $user->gender) === 'other'  ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                {{-- Bio --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Bio</label>
                    <textarea name="bio" rows="2"
                              class="w-full rounded-lg px-4 py-2.5 text-sm text-white resize-none"
                              style="background:#111; border:1px solid #2a2a2a; outline:none;"
                              onfocus="this.style.borderColor='#3B82F6'" onblur="this.style.borderColor='#2a2a2a'"
                              placeholder="Cerita singkat tentang kamu...">{{ old('bio', $user->bio) }}</textarea>
                </div>

                {{-- Lokasi --}}
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Lokasi</label>
                    <input type="text" name="location" value="{{ old('location', $user->location) }}"
                           placeholder="Jakarta, Indonesia"
                           class="w-full rounded-lg px-4 py-2.5 text-sm text-white"
                           style="background:#111; border:1px solid #2a2a2a; outline:none;"
                           onfocus="this.style.borderColor='#3B82F6'" onblur="this.style.borderColor='#2a2a2a'">
                </div>

                {{-- Satuan --}}
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Satuan Ukuran</label>
                    <select name="measurement_preference"
                            class="w-full rounded-lg px-4 py-2.5 text-sm text-white"
                            style="background:#111; border:1px solid #2a2a2a; outline:none;"
                            onfocus="this.style.borderColor='#3B82F6'" onblur="this.style.borderColor='#2a2a2a'">
                        <option value="metric"   {{ old('measurement_preference', $user->measurement_preference) === 'metric'   ? 'selected' : '' }}>Metric (km, kg)</option>
                        <option value="imperial" {{ old('measurement_preference', $user->measurement_preference) === 'imperial' ? 'selected' : '' }}>Imperial (mi, lbs)</option>
                    </select>
                </div>

                {{-- Berat --}}
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Berat (kg)</label>
                    <input type="number" name="weight" value="{{ old('weight', $user->weight) }}"
                           min="1" max="500" step="0.1" placeholder="70"
                           class="w-full rounded-lg px-4 py-2.5 text-sm text-white"
                           style="background:#111; border:1px solid #2a2a2a; outline:none;"
                           onfocus="this.style.borderColor='#3B82F6'" onblur="this.style.borderColor='#2a2a2a'">
                </div>

                {{-- Tinggi --}}
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Tinggi (cm)</label>
                    <input type="number" name="height" value="{{ old('height', $user->height) }}"
                           min="1" max="300" step="0.1" placeholder="170"
                           class="w-full rounded-lg px-4 py-2.5 text-sm text-white"
                           style="background:#111; border:1px solid #2a2a2a; outline:none;"
                           onfocus="this.style.borderColor='#3B82F6'" onblur="this.style.borderColor='#2a2a2a'">
                </div>

                {{-- Password baru --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">
                        Password Baru
                        <span class="text-slate-600 font-normal ml-1">(kosongkan jika tidak ingin mengubah)</span>
                    </label>
                    <input type="password" name="password" placeholder="Min. 8 karakter"
                           class="w-full rounded-lg px-4 py-2.5 text-sm text-white"
                           style="background:#111; border:1px solid #2a2a2a; outline:none;"
                           onfocus="this.style.borderColor='#3B82F6'" onblur="this.style.borderColor='#2a2a2a'">
                </div>

                {{-- Konfirmasi password --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                           class="w-full rounded-lg px-4 py-2.5 text-sm text-white"
                           style="background:#111; border:1px solid #2a2a2a; outline:none;"
                           onfocus="this.style.borderColor='#3B82F6'" onblur="this.style.borderColor='#2a2a2a'">
                </div>
            </div>

            <button type="submit"
                    class="w-full py-2.5 rounded-xl text-sm font-semibold text-white transition-colors"
                    style="background:#3B82F6;">
                Simpan Perubahan
            </button>
        </form>
    </div>

</div>
@endsection
