<?php

namespace App\Http\Controllers\pagegame;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MainMenuController extends Controller
{
    public function index()
    {
        return view('page_game.main_menu.index');
    }
}
