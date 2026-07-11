<?php

namespace App\Http\Controllers;

use App\Models\UserRoute;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class RouteController extends Controller
{
    /**
     * Map a UserRoute to the array shape expected by the Flutter client.
     */
    private function formatRoute(UserRoute $route): array
    {
        return [
            'id'                          => $route->id,
            'name'                        => $route->name,
            'description'                 => $route->description,
            'sport_type'                  => $route->type,          // Flutter uses sport_type
            'distance_km'                 => $route->distance_km,   // computed: distance / 1000
            'elevation_gain'              => $route->elevation_gain ? (int) $route->elevation_gain : null,
            'estimated_duration_minutes'  => $route->estimated_duration, // Flutter uses _minutes suffix
            'difficulty'                  => $route->difficulty ?? null,
            'is_public'                   => (bool) $route->is_public,
            'usage_count'                 => $route->times_used ?? 0, // Flutter uses usage_count
            'start_lat'                   => $route->start_lat,
            'start_lng'                   => $route->start_lng,
            'end_lat'                     => $route->end_lat,
            'end_lng'                     => $route->end_lng,
            'map_preview_url'             => $route->map_image
                ? asset('storage/' . $route->map_image)
                : null,
            'created_at'                  => $route->created_at?->toIso8601String(),
        ];
    }

    /**
     * List all routes for authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'sport_type' => 'nullable|in:run,ride,swim,walk,hike,other',
        ]);

        $routes = UserRoute::where('user_id', $request->user()->id)
            ->when($request->sport_type, fn($q) => $q->where('type', $request->sport_type))
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($r) => $this->formatRoute($r));

        return response()->json(['routes' => $routes]);
    }

    /**
     * Store a new route.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'description'          => 'nullable|string',
            'type'                 => 'required|in:run,ride,swim,walk,hike,other',
            'distance'             => 'nullable|numeric|min:0',
            'elevation_gain'       => 'nullable|numeric|min:0',
            'elevation_loss'       => 'nullable|numeric|min:0',
            'waypoints'            => 'nullable|array',
            'waypoints.*.lat'      => 'required_with:waypoints|numeric|between:-90,90',
            'waypoints.*.lng'      => 'required_with:waypoints|numeric|between:-180,180',
            'waypoints.*.ele'      => 'nullable|numeric',
            'start_lat'            => 'nullable|numeric|between:-90,90',
            'start_lng'            => 'nullable|numeric|between:-180,180',
            'end_lat'              => 'nullable|numeric|between:-90,90',
            'end_lng'              => 'nullable|numeric|between:-180,180',
            'estimated_duration'   => 'nullable|integer|min:1',
            'estimated_calories'   => 'nullable|integer|min:0',
            'is_public'            => 'nullable|boolean',
            'difficulty'           => 'nullable|in:easy,moderate,hard',
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Rute berhasil dibuat.',
            'route'   => $route,
        ], 201);
    }

    /**
     * Show a single route.
     */
    public function show(Request $request, UserRoute $userRoute): JsonResponse
    {
        if ($userRoute->user_id !== $request->user()->id && !$userRoute->is_public) {
            return response()->json(['message' => 'Rute ini bersifat privat.'], 403);
        }

        return response()->json([
            'route' => $this->formatRoute($userRoute),
        ]);
    }

    /**
     * Update a route.
     */
    public function update(Request $request, UserRoute $userRoute): JsonResponse
    {
        if ($userRoute->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $validated = $request->validate([
            'name'                 => 'sometimes|string|max:255',
            'description'          => 'sometimes|nullable|string',
            'type'                 => 'sometimes|in:run,ride,swim,walk,hike,other',
            'distance'             => 'sometimes|nullable|numeric|min:0',
            'elevation_gain'       => 'sometimes|nullable|numeric|min:0',
            'elevation_loss'       => 'sometimes|nullable|numeric|min:0',
            'waypoints'            => 'sometimes|nullable|array',
            'waypoints.*.lat'      => 'required_with:waypoints|numeric|between:-90,90',
            'waypoints.*.lng'      => 'required_with:waypoints|numeric|between:-180,180',
            'waypoints.*.ele'      => 'nullable|numeric',
            'start_lat'            => 'sometimes|nullable|numeric|between:-90,90',
            'start_lng'            => 'sometimes|nullable|numeric|between:-180,180',
            'end_lat'              => 'sometimes|nullable|numeric|between:-90,90',
            'end_lng'              => 'sometimes|nullable|numeric|between:-180,180',
            'estimated_duration'   => 'sometimes|nullable|integer|min:1',
            'estimated_calories'   => 'sometimes|nullable|integer|min:0',
            'is_public'            => 'sometimes|boolean',
            'difficulty'           => 'sometimes|nullable|in:easy,moderate,hard',
        ]);

        $userRoute->update($validated);

        return response()->json([
            'message' => 'Rute berhasil diperbarui.',
            'route'   => $this->formatRoute($userRoute->fresh()),
        ]);
    }

    /**
     * Delete a route.
     */
    public function destroy(Request $request, UserRoute $userRoute): JsonResponse
    {
        if ($userRoute->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        if ($userRoute->map_image) {
            Storage::disk('public')->delete($userRoute->map_image);
        }

        $userRoute->delete();

        return response()->json([
            'message' => 'Rute berhasil dihapus.',
        ]);
    }

    /**
     * List public routes.
     */
    public function explore(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'nullable|in:run,ride,swim,walk,hike,other',
        ]);

        $routes = UserRoute::where('is_public', true)
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->with('user:id,name,username,avatar')
            ->orderBy('times_used', 'desc')
            ->paginate(15);

        return response()->json($routes);
    }
}
