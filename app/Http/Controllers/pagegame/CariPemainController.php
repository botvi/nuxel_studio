<?php

namespace App\Http\Controllers\pagegame;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Room;
use Illuminate\Http\Request;

class CariPemainController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $players = [];

        $currentUserId = auth()->id();
        if ($search) {
            $players = User::where('nama_jalur', 'LIKE', '%' . $search . '%')
                ->where('id', '!=', $currentUserId)
                ->whereNotNull('nama_jalur')
                ->take(20)
                ->get();
        } else {
            // Get some active or random players as recommendation
            $players = User::where('id', '!=', $currentUserId)
                ->whereNotNull('nama_jalur')
                ->take(10)
                ->get();
        }

        return view('page_game.caripemain.index', compact('players', 'search'));
    }

    public function detail($id)
    {
        $targetUser = User::findOrFail($id);
        
        $winsCount = $targetUser->wins()->count();
        $lossesCount = $targetUser->losses()->count();
        
        $statusText = 'ANAK BARU';
        if ($winsCount >= 100) {
            $statusText = 'PAMACU INTI';
        } elseif ($winsCount >= 50) {
            $statusText = 'PAMAIN SEWA';
        }

        // Fetch match history
        $history = Room::where('status', 'finished')
            ->where(function($query) use ($id) {
                $query->where('host_id', $id)
                      ->orWhere('guest_id', $id);
            })
            ->with(['host', 'guest', 'winner'])
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        return view('page_game.caripemain.detailpemain', compact('targetUser', 'winsCount', 'lossesCount', 'statusText', 'history'));
    }
}
