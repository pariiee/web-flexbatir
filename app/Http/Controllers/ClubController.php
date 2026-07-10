<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\ClubMember;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClubController extends Controller
{
    /**
     * List all public clubs.
     */
    public function index(Request $request): JsonResponse
    {
        $clubs = Club::where('privacy', 'public')
            ->with('owner:id,name,username,avatar')
            ->withCount('members')
            ->when($request->sport_type, fn($q) => $q->where('sport_type', $request->sport_type))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('members_count', 'desc')
            ->paginate(15);

        return response()->json($clubs);
    }

    /**
     * List clubs for authenticated user.
     */
    public function myClubs(Request $request): JsonResponse
    {
        $clubs = Club::whereHas('members', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id)
              ->where('status', 'approved');
        })
        ->with('owner:id,name,username,avatar')
        ->orderBy('name')
        ->get();

        return response()->json(['clubs' => $clubs]);
    }

    /**
     * Create a new club.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'sport_type'  => 'required|in:run,ride,swim,walk,hike,multisport,other',
            'privacy'     => 'nullable|in:public,private',
            'location'    => 'nullable|string|max:100',
            'website'     => 'nullable|url|max:255',
        ]);

        $slug = Str::slug($validated['name']) . '-' . Str::random(5);

        $club = Club::create([
            ...$validated,
            'owner_id' => $request->user()->id,
            'slug'     => $slug,
        ]);

        // Owner otomatis jadi member dengan role owner
        ClubMember::create([
            'club_id' => $club->id,
            'user_id' => $request->user()->id,
            'role'    => 'owner',
            'status'  => 'approved',
        ]);

        $club->increment('members_count');

        return response()->json([
            'message' => 'Klub berhasil dibuat.',
            'club'    => $club->load('owner:id,name,username,avatar'),
        ], 201);
    }

    /**
     * Show a single club.
     */
    public function show(Club $club): JsonResponse
    {
        return response()->json([
            'club' => $club->load([
                'owner:id,name,username,avatar',
                'approvedMembers:id,name,username,avatar',
            ]),
        ]);
    }

    /**
     * Update a club.
     */
    public function update(Request $request, Club $club): JsonResponse
    {
        if ($club->owner_id !== $request->user()->id) {
            return response()->json(['message' => 'Hanya owner yang dapat mengubah klub.'], 403);
        }

        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'sport_type'  => 'sometimes|in:run,ride,swim,walk,hike,multisport,other',
            'privacy'     => 'sometimes|in:public,private',
            'location'    => 'sometimes|nullable|string|max:100',
            'website'     => 'sometimes|nullable|url|max:255',
        ]);

        $club->update($validated);

        return response()->json([
            'message' => 'Klub berhasil diperbarui.',
            'club'    => $club->fresh(),
        ]);
    }

    /**
     * Delete a club.
     */
    public function destroy(Request $request, Club $club): JsonResponse
    {
        if ($club->owner_id !== $request->user()->id) {
            return response()->json(['message' => 'Hanya owner yang dapat menghapus klub.'], 403);
        }

        if ($club->logo) Storage::disk('public')->delete($club->logo);
        if ($club->cover_image) Storage::disk('public')->delete($club->cover_image);

        $club->delete();

        return response()->json(['message' => 'Klub berhasil dihapus.']);
    }

    /**
     * Join a club.
     */
    public function join(Request $request, Club $club): JsonResponse
    {
        $existing = ClubMember::where('club_id', $club->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Kamu sudah bergabung atau menunggu persetujuan.'], 409);
        }

        $status = $club->privacy === 'public' ? 'approved' : 'pending';

        ClubMember::create([
            'club_id' => $club->id,
            'user_id' => $request->user()->id,
            'role'    => 'member',
            'status'  => $status,
        ]);

        if ($status === 'approved') {
            $club->increment('members_count');
        }

        return response()->json([
            'message' => $status === 'approved'
                ? 'Berhasil bergabung ke klub.'
                : 'Permintaan bergabung dikirim, menunggu persetujuan.',
            'status'  => $status,
        ]);
    }

    /**
     * Leave a club.
     */
    public function leave(Request $request, Club $club): JsonResponse
    {
        if ($club->owner_id === $request->user()->id) {
            return response()->json(['message' => 'Owner tidak dapat meninggalkan klub. Hapus klub atau transfer kepemilikan.'], 422);
        }

        $member = ClubMember::where('club_id', $club->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$member) {
            return response()->json(['message' => 'Kamu bukan anggota klub ini.'], 404);
        }

        if ($member->status === 'approved') {
            $club->decrement('members_count');
        }

        $member->delete();

        return response()->json(['message' => 'Berhasil keluar dari klub.']);
    }

    /**
     * List members of a club.
     */
    public function members(Club $club): JsonResponse
    {
        $members = ClubMember::where('club_id', $club->id)
            ->where('status', 'approved')
            ->with('user:id,name,username,avatar')
            ->orderBy('role')
            ->paginate(20);

        return response()->json($members);
    }

    /**
     * Upload club logo.
     */
    public function uploadLogo(Request $request, Club $club): JsonResponse
    {
        if ($club->owner_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($club->logo) Storage::disk('public')->delete($club->logo);

        $path = $request->file('logo')->store('clubs/logos', 'public');
        $club->update(['logo' => $path]);

        return response()->json([
            'message'  => 'Logo klub berhasil diupload.',
            'logo_url' => Storage::url($path),
        ]);
    }
}
