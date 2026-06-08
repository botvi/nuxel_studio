<?php

namespace App\Http\Controllers\pagegame;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class RoomController extends Controller
{
    public function index()
    {
        return view('page_game.room.index');
    }

    public function createOrJoin()
    {
        return view('page_game.room.createorjoin');
    }

    public function list()
    {
        $rooms = Room::where('status', 'waiting')
            ->whereNull('guest_id')
            ->where('host_id', '!=', auth()->id())
            ->with('host')
            ->get();

        return response()->json([
            'rooms' => $rooms->map(function($room) {
                return [
                    'id' => $room->id,
                    'name' => $room->name,
                    'is_private' => !empty($room->password),
                    'host_name' => $room->host->nama_jalur ?? $room->host->email,
                ];
            })
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'password' => 'nullable|string|max:50',
        ]);

        $room = Room::create([
            'room_code' => strtoupper(Str::random(6)),
            'name' => $request->name,
            'password' => $request->password ? Hash::make($request->password) : null,
            'host_id' => auth()->id(),
            'status' => 'waiting',
        ]);

        return response()->json([
            'success' => true,
            'redirect_url' => route('room.lobby', ['id' => $room->id])
        ]);
    }

    public function join(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'password' => 'nullable|string',
        ]);

        $room = Room::findOrFail($request->room_id);

        if ($room->host_id === auth()->id()) {
            return response()->json([
                'success' => true,
                'redirect_url' => route('room.lobby', ['id' => $room->id])
            ]);
        }

        if (!empty($room->password)) {
            if (!$request->password || !Hash::check($request->password, $room->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password room salah!'
                ], 422);
            }
        }

        if (!empty($room->guest_id) && $room->guest_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Room sudah penuh!'
            ], 422);
        }

        $room->guest_id = auth()->id();
        $room->save();

        return response()->json([
            'success' => true,
            'redirect_url' => route('room.lobby', ['id' => $room->id])
        ]);
    }

    public function matchmake(Request $request)
    {
        // Find an active Quick Match room waiting for a guest, created by someone else
        $room = Room::where('name', 'Quick Match')
            ->where('status', 'waiting')
            ->whereNull('guest_id')
            ->where('host_id', '!=', auth()->id())
            ->first();

        if ($room) {
            $room->guest_id = auth()->id();
            $room->save();
        } else {
            // Create a new Quick Match room
            $room = Room::create([
                'room_code' => strtoupper(Str::random(6)),
                'name' => 'Quick Match',
                'host_id' => auth()->id(),
                'status' => 'waiting',
            ]);
        }

        return response()->json([
            'success' => true,
            'redirect_url' => route('room.lobby', ['id' => $room->id])
        ]);
    }

    public function lobby($id)
    {
        $room = Room::with(['host', 'guest'])->findOrFail($id);

        // Security check: only host and guest can view the lobby
        if ($room->host_id !== auth()->id() && (!empty($room->guest_id) && $room->guest_id !== auth()->id())) {
            return redirect()->route('room')->with('error', 'Anda tidak memiliki akses ke room ini.');
        }

        return view('page_game.room.lobby', compact('room'));
    }

    public function ready(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'ready' => 'required|boolean',
        ]);

        $room = Room::findOrFail($request->room_id);

        if ($room->host_id === auth()->id()) {
            $room->host_ready = $request->ready;
        } elseif ($room->guest_id === auth()->id()) {
            $room->guest_ready = $request->ready;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $room->save();

        return response()->json([
            'success' => true,
            'host_ready' => $room->host_ready,
            'guest_ready' => $room->guest_ready,
        ]);
    }

    public function leave(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);

        $room = Room::findOrFail($request->room_id);

        if ($room->host_id === auth()->id()) {
            // Host leaves -> delete the room
            $room->delete();
            return response()->json([
                'success' => true,
                'redirect_url' => route('room')
            ]);
        } elseif ($room->guest_id === auth()->id()) {
            // Guest leaves -> vacate slot and reset ready states
            $room->guest_id = null;
            $room->guest_ready = false;
            $room->host_ready = false;
            $room->save();
            return response()->json([
                'success' => true,
                'redirect_url' => route('room')
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    public function finish(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'winner_id' => 'required|exists:users,id',
        ]);

        $room = Room::findOrFail($request->room_id);

        // Prevent duplicate rewards / state changes
        if ($room->status === 'finished') {
            return response()->json([
                'success' => true,
                'message' => 'Room match has already been finished.'
            ]);
        }

        $room->status = 'finished';
        $room->winner_id = $request->winner_id;
        $room->loser_id = ($request->winner_id == $room->host_id) ? $room->guest_id : $room->host_id;
        $room->save();

        return response()->json([
            'success' => true,
            'message' => 'Match results saved successfully.'
        ]);
    }
}
