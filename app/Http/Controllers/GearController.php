<?php

namespace App\Http\Controllers;

use App\Models\Gear;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GearController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $gear = $request->user()
            ->gears()
            ->orderBy('is_retired')
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['gear' => $gear]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'brand'         => 'nullable|string|max:100',
            'model'         => 'nullable|string|max:100',
            'type'          => 'nullable|in:shoes,bike,helmet,watch,vest,other',
            'description'   => 'nullable|string|max:1000',
            'purchase_year' => 'nullable|integer|min:1900|max:2100',
        ]);

        $gear = $request->user()->gears()->create($data);

        return response()->json(['gear' => $gear], 201);
    }

    public function show(Request $request, Gear $gear): JsonResponse
    {
        $this->authorizeOwner($request, $gear);

        return response()->json(['gear' => $gear]);
    }

    public function update(Request $request, Gear $gear): JsonResponse
    {
        $this->authorizeOwner($request, $gear);

        $data = $request->validate([
            'name'          => 'sometimes|string|max:255',
            'brand'         => 'nullable|string|max:100',
            'model'         => 'nullable|string|max:100',
            'type'          => 'nullable|in:shoes,bike,helmet,watch,vest,other',
            'description'   => 'nullable|string|max:1000',
            'purchase_year' => 'nullable|integer|min:1900|max:2100',
            'is_retired'    => 'sometimes|boolean',
        ]);

        $gear->update($data);

        return response()->json(['gear' => $gear->fresh()]);
    }

    public function destroy(Request $request, Gear $gear): JsonResponse
    {
        $this->authorizeOwner($request, $gear);
        $gear->delete();

        return response()->json(['message' => 'Gear deleted']);
    }

    private function authorizeOwner(Request $request, Gear $gear): void
    {
        if ($gear->user_id !== $request->user()->id) {
            abort(403, 'Forbidden');
        }
    }
}
