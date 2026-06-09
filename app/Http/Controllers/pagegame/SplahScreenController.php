<?php

namespace App\Http\Controllers\pagegame;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SplahScreenController extends Controller
{
    public function index()
    {

        return view('page_game.splash_screen.index');
    }
}