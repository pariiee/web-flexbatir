@extends('admin.layouts.app')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('content')
<div class="space-y-5">

    {{-- Filter & Search --}}
    <div class="card rounded-xl p-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama, username, email..."
                   class="input-field rounded-lg px-4 py-2 text-sm flex-1 min-w-[200px]">
            <select name="status" class="input-field rounded-lg px-4 py-2 text-sm">
                <option value="">Semua User</option>
                <option value="admin" {{ request('status') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Banned</option>
            </select>
            <button type="submit"
                    class="bg-brand text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-600 transition-colors">
                Cari
            </button>
            @if(request('search') || request('status'))
            <a href="{{ route('admin.users.index') }}"
               class="text-slate-400 hover:text-white px-4 py-2 rounded-lg text-sm border border-[#2a2a2a] transition-colors">
                Reset
            </a>
            @endif
        </form>
    </div>

    {{-- Flash messages --}}
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

    {{-- Table --}}
    <div class="card rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-slate-500 border-b border-[#2a2a2a] bg-[#111]">
                        <th class="text-left px-5 py-3 font-medium">User</th>
                        <th class="text-left px-5 py-3 font-medium">Email</th>
                        <th class="text-center px-5 py-3 font-medium">Followers</th>
                        <th class="text-center px-5 py-3 font-medium">Status</th>
                        <th class="text-left px-5 py-3 font-medium">Bergabung</th>
                        <th class="text-right px-5 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2a2a2a]">
                    @forelse($users as $user)
                    <tr class="hover:bg-[#111] transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-[#2a2a2a] flex items-center justify-center overflow-hidden flex-shrink-0">
                                    @if($user->avatar)
                                        <img src="{{ $user->avatar_url }}" alt="" class="w-9 h-9 object-cover">
                                    @else
                                        <span class="text-sm font-bold text-slate-400">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-white">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $user->username ? '@'.$user->username : '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-slate-400 text-xs">{{ $user->email }}</td>
                        <td class="px-5 py-3 text-center text-slate-300">{{ number_format($user->followers_count) }}</td>
                        <td class="px-5 py-3 text-center">
                            @if($user->is_admin)
                                <span class="text-xs bg-brand/20 text-brand px-2.5 py-1 rounded-full font-medium">Admin</span>
                            @elseif($user->is_banned)
                                <span class="text-xs bg-red-500/20 text-red-400 px-2.5 py-1 rounded-full font-medium">Banned</span>
                            @else
                                <span class="text-xs bg-green-500/20 text-green-400 px-2.5 py-1 rounded-full font-medium">Aktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-xs text-slate-500">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="text-xs text-brand hover:underline">Detail</a>

                                <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="text-xs text-slate-400 hover:text-white transition-colors"
                                            onclick="return confirm('{{ $user->is_admin ? 'Cabut hak admin?' : 'Jadikan admin?' }}')">
                                        {{ $user->is_admin ? 'Cabut Admin' : 'Jadikan Admin' }}
                                    </button>
                                </form>

                                @if(!$user->is_banned)
                                <button onclick="openBanModal({{ $user->id }}, '{{ $user->username }}')"
                                        class="text-xs text-red-400 hover:text-red-300 transition-colors">
                                    Ban
                                </button>
                                @else
                                <form method="POST" action="{{ route('admin.users.unban', $user) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs text-green-400 hover:text-green-300 transition-colors">
                                        Unban
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-slate-500">Tidak ada user ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="px-5 py-4 border-t border-[#2a2a2a]">
            {{ $users->links('admin.partials.pagination') }}
        </div>
        @endif
    </div>
</div>

{{-- Ban Modal --}}
<div id="banModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70">
    <div class="card rounded-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-base font-semibold text-white mb-1">Ban User</h3>
        <p class="text-sm text-slate-400 mb-4">Ban <span id="banUsername" class="text-white font-medium"></span></p>
        <form id="banForm" method="POST">
            @csrf
            <textarea name="reason" rows="3" required
                      placeholder="Alasan ban..."
                      class="input-field w-full rounded-lg px-3 py-2 text-sm resize-none mb-4"></textarea>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeBanModal()"
                        class="px-4 py-2 text-sm text-slate-400 hover:text-white border border-[#2a2a2a] rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                    Ban User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openBanModal(userId, username) {
    document.getElementById('banUsername').textContent = '@' + username;
    document.getElementById('banForm').action = '/admin/users/' + userId + '/ban';
    document.getElementById('banModal').classList.remove('hidden');
    document.getElementById('banModal').classList.add('flex');
}
function closeBanModal() {
    document.getElementById('banModal').classList.add('hidden');
    document.getElementById('banModal').classList.remove('flex');
}
</script>
@endsection
