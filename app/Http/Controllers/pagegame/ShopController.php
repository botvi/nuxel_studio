<?php

namespace App\Http\Controllers\pagegame;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CoinPackage;

class ShopController extends Controller
{
    public function index()
    {
        $packages = CoinPackage::orderBy('coin_amount', 'asc')->get();
        return view('page_game.shop.index', compact('packages'));
    }

    public function addPoints(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $points = intval($request->input('points', 0));
        if ($points > 0) {
            $user->kuansing_poin += $points;
            $user->save();
        }

        return response()->json([
            'success' => true,
            'kuansing_poin' => $user->kuansing_poin
        ]);
    }
}
