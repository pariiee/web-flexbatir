<?php

namespace App\Http\Controllers;

use App\Models\LiveBeacon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LiveBeaconController extends Controller
{
    /**
     * Aktifkan beacon baru — generate token unik.
     * POST /api/live-beacon/start
     */
    public function start(Request $request): JsonResponse
    {
        $user = $request->user();

        // Nonaktifkan beacon lama jika ada
        LiveBeacon::where('user_id', $user->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $beacon = LiveBeacon::create([
            'user_id'       => $user->id,
            'active_token'  => Str::random(32),
            'is_active'     => true,
            'expires_at'    => now()->addHours(6),
        ]);

        return response()->json([
            'message'    => 'Live beacon aktif.',
            'token'      => $beacon->active_token,
            'share_url'  => url("/live/{$beacon->active_token}"),
            'expires_at' => $beacon->expires_at,
        ], 201);
    }

    /**
     * Update posisi GPS dari Flutter setiap 10-15 detik.
     * POST /api/live-beacon/update
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token'         => 'required|string|size:32',
            'lat'           => 'required|numeric|between:-90,90',
            'lng'           => 'required|numeric|between:-180,180',
            'battery_level' => 'nullable|integer|between:0,100',
            'speed'         => 'nullable|numeric|min:0',
        ]);

        $beacon = LiveBeacon::where('active_token', $validated['token'])
            ->where('user_id', $request->user()->id)
            ->where('is_active', true)
            ->first();

        if (! $beacon) {
            return response()->json(['message' => 'Token tidak valid atau sudah tidak aktif.'], 404);
        }

        if ($beacon->isExpired()) {
            $beacon->update(['is_active' => false]);
            return response()->json(['message' => 'Beacon sudah expired.'], 410);
        }

        $beacon->update([
            'last_lat'      => $validated['lat'],
            'last_lng'      => $validated['lng'],
            'battery_level' => $validated['battery_level'] ?? $beacon->battery_level,
        ]);

        // Broadcast ke channel publik untuk web view real-time
        broadcast(new \App\Events\BeaconLocationUpdated(
            $beacon->active_token,
            $validated['lat'],
            $validated['lng'],
            $validated['battery_level'] ?? null,
            $validated['speed'] ?? null,
        ))->toOthers();

        return response()->json(['message' => 'Posisi diperbarui.']);
    }

    /**
     * Nonaktifkan beacon.
     * POST /api/live-beacon/stop
     */
    public function stop(Request $request): JsonResponse
    {
        LiveBeacon::where('user_id', $request->user()->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        return response()->json(['message' => 'Live beacon dinonaktifkan.']);
    }

    /**
     * Ambil status beacon aktif milik user (untuk restore state di Flutter).
     * GET /api/live-beacon/status
     */
    public function status(Request $request): JsonResponse
    {
        $beacon = LiveBeacon::where('user_id', $request->user()->id)
            ->where('is_active', true)
            ->latest()
            ->first();

        if (! $beacon || $beacon->isExpired()) {
            return response()->json(['active' => false, 'beacon' => null]);
        }

        return response()->json([
            'active'    => true,
            'token'     => $beacon->active_token,
            'share_url' => url("/live/{$beacon->active_token}"),
            'beacon'    => $beacon->only(['last_lat', 'last_lng', 'battery_level', 'expires_at']),
        ]);
    }

    /**
     * Web publik — tampilkan peta tracking.
     * GET /live/{token}  (web route, bukan API)
     */
    public function publicView(string $token)
    {
        $beacon = LiveBeacon::where('active_token', $token)->first();

        if (! $beacon) {
            abort(404, 'Link tracking tidak ditemukan.');
        }

        return view('live.beacon', [
            'beacon'   => $beacon,
            'token'    => $token,
            'userName' => $beacon->user->name ?? 'Pengguna',
        ]);
    }
}
