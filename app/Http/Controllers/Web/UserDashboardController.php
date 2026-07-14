<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Stats aktivitas user
        $stats = DB::table('activities')
            ->where('user_id', $user->id)
            ->selectRaw('
                COUNT(*) as total_activities,
                COALESCE(SUM(distance), 0) as total_distance,
                COALESCE(SUM(duration), 0) as total_duration,
                COALESCE(SUM(calories), 0) as total_calories,
                COALESCE(MAX(distance), 0) as longest_activity
            ')
            ->first();

        // Aktivitas terbaru (5)
        $recentActivities = DB::table('activities')
            ->where('user_id', $user->id)
            ->orderBy('started_at', 'desc')
            ->limit(5)
            ->get();

        // Chart aktivitas 30 hari terakhir
        $chartData = DB::table('activities')
            ->where('user_id', $user->id)
            ->where('started_at', '>=', now()->subDays(29))
            ->selectRaw('DATE(started_at) as date, COUNT(*) as count, SUM(distance) as distance')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels    = [];
        $countData = [];
        $distData  = [];
        for ($i = 29; $i >= 0; $i--) {
            $date        = now()->subDays($i)->format('Y-m-d');
            $labels[]    = now()->subDays($i)->format('d/m');
            $countData[] = $chartData[$date]->count ?? 0;
            $distData[]  = round(($chartData[$date]->distance ?? 0) / 1000, 2);
        }

        // Top jenis aktivitas
        $topTypes = DB::table('activities')
            ->where('user_id', $user->id)
            ->selectRaw('type, COUNT(*) as count, SUM(distance) as total_distance')
            ->groupBy('type')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Followers & following count
        $followersCount  = DB::table('follows')->where('following_id', $user->id)->count();
        $followingCount  = DB::table('follows')->where('follower_id', $user->id)->count();

        // PR tercepat per jarak
        $prs = DB::table('activities')
            ->where('user_id', $user->id)
            ->whereNotNull('duration')
            ->where('duration', '>', 0)
            ->whereNotNull('distance')
            ->where('distance', '>', 0)
            ->orderByRaw('duration / distance ASC')
            ->limit(3)
            ->get(['title', 'type', 'distance', 'duration', 'started_at']);

        return view('user.dashboard', compact(
            'user',
            'stats',
            'recentActivities',
            'labels',
            'countData',
            'distData',
            'topTypes',
            'followersCount',
            'followingCount',
            'prs'
        ));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'avatar.image'  => 'File harus berupa gambar.',
            'avatar.mimes'  => 'Format harus jpg, jpeg, png, atau webp.',
            'avatar.max'    => 'Ukuran file maksimal 2MB.',
        ]);

        $user = Auth::user();

        try {
            // Hapus avatar lama kalau ada dan bukan URL eksternal
            if ($user->avatar && str_starts_with($user->avatar, '/storage/')) {
                $oldPath = str_replace('/storage/', 'public/', $user->avatar);
                Storage::delete($oldPath);
            }

            // Simpan dengan nama random di storage/public/avatars
            $path = $request->file('avatar')->store('public/avatars');

            if (!$path) {
                \Log::error('Avatar upload: store() returned false');
                return back()->withErrors(['avatar' => 'Gagal menyimpan file.']);
            }

            $url = '/storage/' . str_replace('public/', '', $path);

            \Log::info("Avatar uploaded: path={$path}, url={$url}, user={$user->id}");

            $user->update(['avatar' => $url]);

            return back()->with('success', 'Foto profil berhasil diupdate.');

        } catch (\Throwable $e) {
            \Log::error('Avatar upload error: ' . $e->getMessage());
            return back()->withErrors(['avatar' => 'Gagal upload: ' . $e->getMessage()]);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'                   => 'required|string|max:100',
            'username'               => "required|string|max:50|unique:users,username,{$user->id}|alpha_dash",
            'bio'                    => 'nullable|string|max:500',
            'location'               => 'nullable|string|max:100',
            'gender'                 => 'nullable|in:male,female,other',
            'weight'                 => 'nullable|numeric|min:1|max:500',
            'height'                 => 'nullable|numeric|min:1|max:300',
            'measurement_preference' => 'nullable|in:metric,imperial',
            'password'               => 'nullable|string|min:8|confirmed',
        ], [
            'username.unique'    => 'Username sudah dipakai.',
            'username.alpha_dash'=> 'Username hanya boleh huruf, angka, strip, dan underscore.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diupdate.');
    }
}
