<?php

namespace App\Http\Controllers\pagegame;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModelJalur;

class TukangJaluarController extends Controller
{
    public function index()
    {
        return view('page_game.tukangjaluar.index');
    }
    public function save(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $data = $request->all();

        // Update user's kuansing_poin if sent in the request
        if (isset($data['coins'])) {
            $user->kuansing_poin = intval($data['coins']);
            $user->save();
            // Remove coins from the JSON model_jalur
            unset($data['coins']);
        }

        // Save customization details to database model_jalurs table
        $modelJalur = ModelJalur::updateOrCreate(
            ['user_id' => $user->id],
            [
                'model_jalur' => $data,
                'fitur_corak' => ($data['boat_unlocked'] ?? false) ? 'active' : 'inactive',
                'fitur_lambai' => ($data['lambai_unlocked'] ?? false) ? 'active' : 'inactive',
            ]
        );

        return response()->json(['success' => true]);
    }

    public function get()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $modelJalur = ModelJalur::where('user_id', $user->id)->first();
        
        $data = $modelJalur ? ($modelJalur->model_jalur ?? []) : [];
        
        // Ensure coins is set from the user's kuansing_poin, NOT from JSON
        $data['coins'] = $user->kuansing_poin;
        $data['nama_jalur'] = $user->nama_jalur;
        
        // Overwrite lock status based on DB fields
        $data['boat_unlocked'] = $modelJalur ? ($modelJalur->fitur_corak === 'active') : false;
        $data['lambai_unlocked'] = $modelJalur ? ($modelJalur->fitur_lambai === 'active') : false;
        
        if (empty($data['customColors'])) {
            $data['customColors'] = [
                'boat' => '#8b4513',
                'hair' => '#e53e3e',
                'shirt' => '#a0aec0',
                'pants' => '#38a169',
                'paddle' => '#3182ce',
                'splash' => '#a5f3fc'
            ];
        }

        return response()->json($data);
    }

    public function uploadCorak(Request $request)
    {
        $userId = auth()->id();
        if (!$userId) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();

            if (!file_exists(public_path('corak'))) {
                mkdir(public_path('corak'), 0755, true);
            }

            $file->move(public_path('corak'), $filename);

            return response()->json([
                'success' => true,
                'url' => '/corak/' . $filename
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

    public function uploadLambai(Request $request)
    {
        $userId = auth()->id();
        if (!$userId) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();

            if (!file_exists(public_path('profiles'))) {
                mkdir(public_path('profiles'), 0755, true);
            }

            $file->move(public_path('profiles'), $filename);

            return response()->json([
                'success' => true,
                'url' => '/profiles/' . $filename
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }
}

