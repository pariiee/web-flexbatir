<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    /**
     * Global leaderboard.
     *
     * Query params:
     *   period : week | month | year | all  (default: week)
     *   type   : distance | duration | calories | activities | elevation  (default: distance)
     *   sport  : run | ride | swim | walk | hike | workout | yoga | crossfit | other | all  (default: all)
     *   limit  : int  (default: 50, max: 100)
     */
    public function index(Request $request): JsonResponse
    {
        $period    = $request->query('period', 'week');
        $type      = $request->query('type', 'distance');
        $sport     = $request->query('sport', 'all');
        $limit     = min((int) $request->query('limit', 50), 100);

        // ── Date range ────────────────────────────────────────────────────
        $from = match ($period) {
            'week'  => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year'  => now()->startOfYear(),
            default => null,
        };

        // ── Aggregate column ──────────────────────────────────────────────
        $aggregate = match ($type) {
            'duration'   => 'SUM(duration)',
            'calories'   => 'SUM(calories)',
            'activities' => 'COUNT(*)',
            'elevation'  => 'SUM(elevation_gain)',
            default      => 'SUM(distance)', // distance
        };

        // ── Build query ───────────────────────────────────────────────────
        $query = DB::table('activities')
            ->join('users', 'activities.user_id', '=', 'users.id')
            ->select(
                'users.id as user_id',
                'users.name',
                'users.username',
                'users.avatar',
                DB::raw("$aggregate as total_value"),
                DB::raw('COUNT(*) as total_activities')
            )
            ->where('activities.is_public', true)
            ->groupBy('users.id', 'users.name', 'users.username', 'users.avatar')
            ->orderByDesc('total_value')
            ->limit($limit);

        if ($from) {
            $query->where('activities.started_at', '>=', $from);
        }

        if ($sport !== 'all') {
            $query->where('activities.type', $sport);
        }

        $rows = $query->get();

        // ── Format & rank ─────────────────────────────────────────────────
        $currentUserId = $request->user()?->id;
        $myRank = null;

        $data = $rows->map(function ($row, $index) use ($type, $currentUserId, &$myRank) {
            $rank = $index + 1;
            if ($currentUserId && $row->user_id === $currentUserId) {
                $myRank = $rank;
            }

            return [
                'rank'             => $rank,
                'user'             => [
                    'id'       => $row->user_id,
                    'name'     => $row->name,
                    'username' => $row->username,
                    'avatar'   => $row->avatar
                        ? asset('storage/' . $row->avatar)
                        : null,
                ],
                'total_value'      => round((float) $row->total_value, 2),
                'total_activities' => (int) $row->total_activities,
                'unit'             => $this->unitFor($type),
            ];
        });

        return response()->json([
            'period'  => $period,
            'type'    => $type,
            'sport'   => $sport,
            'my_rank' => $myRank,
            'data'    => $data,
        ]);
    }

    private function unitFor(string $type): string
    {
        return match ($type) {
            'distance'   => 'km',
            'duration'   => 'min',
            'calories'   => 'kcal',
            'activities' => 'aktivitas',
            'elevation'  => 'm',
            default      => '',
        };
    }
}
