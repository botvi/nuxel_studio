<?php

namespace App\Http\Controllers\pagegame;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModelJalur;

class VsAiController extends Controller
{
    private function getPlayerData()
    {
        $user = auth()->user();
        $modelJalur = ModelJalur::where('user_id', $user->id)->first();
        $modelJalurData = $modelJalur ? ($modelJalur->model_jalur ?? []) : [];

        $customColors = $modelJalurData['customColors'] ?? [
            'boat'   => '#8b4513',
            'hair'   => '#e53e3e',
            'shirt'  => '#a0aec0',
            'pants'  => '#38a169',
            'paddle' => '#3182ce',
            'splash' => '#a5f3fc',
        ];
        $corakDataUrl  = $modelJalurData['corak_data_url'] ?? null;
        $lambaiDataUrl = $modelJalurData['lambai_data_url'] ?? null;

        return compact('customColors', 'corakDataUrl', 'lambaiDataUrl');
    }

    public function level()
    {
        $data = $this->getPlayerData();
        return view('page_game.vsai.level', $data);
    }

    public function arena(Request $request)
    {
        $level = max(1, min(100, intval($request->query('level', 1))));
        $data  = $this->getPlayerData();
        return view('page_game.vsai.arena', array_merge($data, compact('level')));
    }

    public function addCoins(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            $user->kuansing_poin += intval($request->input('coins', 0));
            $user->save();
        }
        return response()->json(['success' => true, 'coins' => $user->kuansing_poin ?? 0]);
    }
}
