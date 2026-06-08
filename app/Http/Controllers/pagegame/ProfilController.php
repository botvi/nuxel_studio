<?php

namespace App\Http\Controllers\pagegame;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function index()
    {
        return view('page_game.profil.index');
    }
}
