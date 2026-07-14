<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    // ── Daftar semua user ──────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = User::withCount(['followers', 'following'])
            ->latest();

        // Filter pencarian
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($request->get('status') === 'banned') {
            $query->where('is_banned', true);
        } elseif ($request->get('status') === 'admin') {
            $query->where('is_admin', true);
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    // ── Detail user ────────────────────────────────────────────────────────────
    public function show(User $user)
    {
        $user->loadCount(['followers', 'following']);

        $stats = [
            'total_activities'  => Activity::where('user_id', $user->id)->count(),
            'total_distance_km' => round(
                Activity::where('user_id', $user->id)->sum('distance') / 1000, 1
            ),
            'total_duration_h'  => round(
                Activity::where('user_id', $user->id)->sum('duration') / 3600, 1
            ),
        ];

        $recentActivities = Activity::where('user_id', $user->id)
            ->latest('started_at')
            ->limit(10)
            ->get();

        return view('admin.users.show', compact('user', 'stats', 'recentActivities'));
    }

    // ── Ban user ───────────────────────────────────────────────────────────────
    public function ban(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // Jangan ban admin lain
        if ($user->is_admin) {
            return back()->with('error', 'Tidak bisa mem-ban akun admin.');
        }

        $user->update([
            'is_banned'  => true,
            'ban_reason' => $request->reason,
            'banned_at'  => now(),
        ]);

        return back()->with('success', "User @{$user->username} telah di-ban.");
    }

    // ── Unban user ─────────────────────────────────────────────────────────────
    public function unban(User $user)
    {
        $user->update([
            'is_banned'  => false,
            'ban_reason' => null,
            'banned_at'  => null,
        ]);

        return back()->with('success', "User @{$user->username} telah di-unban.");
    }

    // ── Toggle admin ───────────────────────────────────────────────────────────
    public function toggleAdmin(User $user)
    {
        $user->update(['is_admin' => !$user->is_admin]);

        $status = $user->is_admin ? 'dijadikan admin' : 'dicabut hak adminnya';
        return back()->with('success', "User @{$user->username} telah {$status}.");
    }
}
