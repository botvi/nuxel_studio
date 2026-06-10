<?php

use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\{
    DashboardController,
};

use App\Http\Controllers\superadmin\{
    DashboardSuperAdminController,
};

use App\Http\Controllers\auth\{
    LoginController,
    GoogleController,
};

use App\Http\Controllers\pagegame\{
    MainMenuController,
    ArenaPacuController,
    ProfilController,
    RoomController,
    ShopController,
    TukangJaluarController,
    SplahScreenController,
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Manual
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');


// splash
Route::get('/', [SplahScreenController::class, 'index'])->name('splash');

// Google
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
Route::get('/auth/google/complete', [GoogleController::class, 'showCompleteForm'])->name('google.complete');
Route::post('/auth/google/complete-register', [GoogleController::class, 'completeRegister'])->name('google.complete.register');

Route::group(['middleware' => ['role:admin']], function () {
    Route::get('/dashboard-superadmin', [DashboardSuperAdminController::class, 'index'])->name('dashboard-superadmin');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/main-menu', [MainMenuController::class, 'index'])->name('main-menu');
    Route::get('/arena-pacu', [ArenaPacuController::class, 'index'])->name('arena-pacu');
    Route::post('/arena-pacu/add-coins', [ArenaPacuController::class, 'addCoins'])->name('arena-pacu.add-coins');
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::get('/room', [RoomController::class, 'index'])->name('room');
    Route::get('/room/create-or-join', [RoomController::class, 'createOrJoin'])->name('room.create-or-join');
    Route::post('/room/create', [RoomController::class, 'create'])->name('room.create');
    Route::post('/room/join', [RoomController::class, 'join'])->name('room.join');
    Route::post('/room/matchmake', [RoomController::class, 'matchmake'])->name('room.matchmake');
    Route::get('/room/lobby/{id}', [RoomController::class, 'lobby'])->name('room.lobby');
    Route::post('/room/ready', [RoomController::class, 'ready'])->name('room.ready');
    Route::post('/room/leave', [RoomController::class, 'leave'])->name('room.leave');
    Route::post('/room/finish', [RoomController::class, 'finish'])->name('room.finish');
    Route::get('/room/list', [RoomController::class, 'list'])->name('room.list');
    Route::get('/shop', [ShopController::class, 'index'])->name('shop');
    Route::post('/shop/add-points', [ShopController::class, 'addPoints'])->name('shop.add-points');
    Route::get('/tukang-jaluar', [TukangJaluarController::class, 'index'])->name('tukang-jaluar');
    Route::post('/tukang-jaluar/save', [TukangJaluarController::class, 'save'])->name('tukang-jaluar.save');
    Route::get('/tukang-jaluar/get', [TukangJaluarController::class, 'get'])->name('tukang-jaluar.get');
    Route::post('/tukang-jaluar/upload-corak', [TukangJaluarController::class, 'uploadCorak'])->name('tukang-jaluar.upload-corak');
    Route::post('/tukang-jaluar/upload-lambai', [TukangJaluarController::class, 'uploadLambai'])->name('tukang-jaluar.upload-lambai');
    Route::get('/cari-pemain', [\App\Http\Controllers\pagegame\CariPemainController::class, 'index'])->name('cari-pemain');
    Route::get('/cari-pemain/detail/{id}', [\App\Http\Controllers\pagegame\CariPemainController::class, 'detail'])->name('cari-pemain.detail');
});
