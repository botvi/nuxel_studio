<?php

namespace App\Http\Controllers\pagegame;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'wins'); // wins | losses | winrate

        // Hitung wins, losses, total match per user dari tabel rooms
        $leaderboard = User::whereNotNull('nama_jalur')
            ->withCount([
                'wins',   // rooms where winner_id = user.id
                'losses', // rooms where loser_id  = user.id
            ])
            ->get()
            ->map(function ($user) {
                $total = $user->wins_count + $user->losses_count;
                $user->total_matches = $total;
                $user->winrate = $total > 0
                    ? round(($user->wins_count / $total) * 100, 1)
                    : 0;
                return $user;
            });

        // Sort berdasarkan filter
        if ($filter === 'losses') {
            $leaderboard = $leaderboard->sortByDesc('losses_count')->values();
        } elseif ($filter === 'winrate') {
            $leaderboard = $leaderboard
                ->filter(fn($u) => $u->total_matches >= 1)
                ->sortByDesc('winrate')
                ->values();
        } else {
            // default: wins
            $leaderboard = $leaderboard->sortByDesc('wins_count')->values();
        }

        // Ambil top 50
        $leaderboard = $leaderboard->take(50);

        // Data user login sendiri untuk highlight
        $currentUser = auth()->user();
        $myRank = $leaderboard->search(fn($u) => $u->id === $currentUser->id);
        $myRank = $myRank !== false ? $myRank + 1 : null;

        return view('page_game.leaderboard.index', compact('leaderboard', 'filter', 'currentUser', 'myRank'));
    }
}
