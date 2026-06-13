@extends('layouts.game')

@section('title', 'Franchise Game — Cari Pemain')

@section('content')
<style>
    body {
        margin: 0;
        padding: 0;
        background-color: #0c111d;
        color: #ffffff;
        font-family: 'Pixelify Sans', monospace;
        overflow: hidden;
    }

    #search-dashboard {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        background: #0c111d url('/game_pacu/assets/image/bg/bgmenu.jpg') no-repeat center center;
        background-size: cover;
        z-index: 10;
        box-sizing: border-box;
        overflow: hidden;
        padding-bottom: 10px;
    }

    /* Particles animation canvas */
    #ps5-particles {
        display: none;
    }

    /* Top Navigation Bar */
    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        padding: 16px 20px 8px;
        z-index: 11;
        margin-top: 10px;
        box-sizing: border-box;
    }

    .back-btn-container {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .back-btn {
        width: 36px;
        height: 36px;
    }

    .back-btn-container:hover {
        transform: scale(1.1);
    }

    .coin-display {
        display: flex;
        align-items: center;
        gap: 6px;
        z-index: 15;
        box-sizing: border-box;
    }

    .coin-icon-wrapper {
        position: relative;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .coin-icon-wrapper img {
        width: 100%;
        height: 100%;
        image-rendering: pixelated;
    }
    .coin-icon-wrapper::after {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: none;
        display: none;
    }

    .coin-amount {
        font-family: 'Pixelify Sans', monospace;
        font-size: 13px;
        font-weight: bold;
        color: #FFD700;
        line-height: 1;
        text-shadow:
            1px 1px 0px #15803d,
            -1px -1px 0px #15803d,
            1px -1px 0px #15803d,
            -1px 1px 0px #15803d;
    }

    /* Search Section & Card */
    .search-container {
        flex: 1;
        display: flex;
        flex-direction: column;
        width: 100%;
        overflow-y: auto;
        z-index: 11;
        scrollbar-width: thin;
        scrollbar-color: rgba(239, 68, 68, 0.4) rgba(255, 255, 255, 0.02);
        box-sizing: border-box;
        padding: 0 14px;
    }

    .search-container::-webkit-scrollbar {
        width: 5px;
    }

    .search-container::-webkit-scrollbar-thumb {
        background: rgba(239, 68, 68, 0.4);
        border-radius: 4px;
    }

    /* Premium PS5 Search Bar Card */
    .search-card {
        background: rgba(255, 255, 255, 0.025);
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 18px;
        box-shadow:
            0 10px 32px rgba(0, 0, 0, 0.6),
            0 0 0 1px rgba(255, 255, 255, 0.04),
            inset 0 1px 0 rgba(255, 255, 255, 0.08);
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 16px;
        position: relative;
        overflow: hidden;
    }

    /* Pixel scanline on search card */
    .search-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: repeating-linear-gradient(
            0deg, transparent, transparent 3px,
            rgba(0,0,0,0.03) 3px, rgba(0,0,0,0.03) 4px
        );
        pointer-events: none;
        z-index: 0;
        border-radius: 20px;
    }

    .search-title {
        font-family: 'Press Start 2P', monospace;
        font-size: 10px;
        color: #f87171;
        text-shadow: 0 0 8px rgba(239, 68, 68, 0.5);
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }

    .search-form {
        display: flex;
        gap: 10px;
        width: 100%;
    }

    .search-input {
        flex: 1;
        background: rgba(0, 0, 0, 0.3);
        border: 1.5px solid rgba(255, 255, 255, 0.12);
        border-radius: 12px;
        padding: 10px 14px;
        font-family: 'Pixelify Sans', monospace;
        font-size: 14px;
        color: #ffffff;
        outline: none;
        transition: all 0.2s ease;
    }

    .search-input::placeholder {
        color: rgba(255, 255, 255, 0.25);
    }

    .search-input:focus {
        border-color: #f87171;
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.2);
        background: rgba(0, 0, 0, 0.4);
    }

    .search-btn {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border: none;
        border-radius: 12px;
        padding: 10px 18px;
        font-family: 'Press Start 2P', monospace;
        font-size: 8px;
        color: #ffffff;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        transition: all 0.15s ease;
        text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.4);
    }

    .search-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
    }

    .search-btn:active {
        transform: translateY(1px);
    }

    /* Players List */
    .section-header {
        font-family: 'Press Start 2P', monospace;
        font-size: 8px;
        color: #ffffff;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
        text-shadow: 2px 2px 0px #000000;
        text-transform: uppercase;
    }

    .players-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 20px;
    }

    .player-row {
        background: rgba(15, 23, 42, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.04);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    .player-row:hover {
        border-color: rgba(239, 68, 68, 0.5);
        background: rgba(30, 41, 59, 0.8);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.4), 0 0 16px rgba(239, 68, 68, 0.12);
    }

    .player-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .player-avatar-wrapper {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 2px solid #ef4444;
        background: #0f172a;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.3);
    }

    .player-avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .player-details {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .player-name {
        font-family: 'Press Start 2P', monospace;
        font-size: 8px;
        color: #ffffff;
        text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    }

    .player-wins {
        font-size: 11px;
        color: #f59e0b;
        font-weight: bold;
    }

    .detail-btn {
        background: rgba(239, 68, 68, 0.08);
        border: 1px solid rgba(239, 68, 68, 0.25);
        border-radius: 10px;
        padding: 8px 12px;
        font-family: 'Press Start 2P', monospace;
        font-size: 7px;
        color: #f87171;
        cursor: pointer;
        transition: all 0.2s ease;
        text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    }

    .detail-btn:hover {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border-color: #ef4444;
        color: #ffffff;
        box-shadow:
            0 4px 0 #7f1d1d,
            0 6px 16px rgba(239, 68, 68, 0.35);
        transform: translateY(-2px);
        text-shadow: 0 1px 2px rgba(0,0,0,0.4);
    }

    .detail-btn:active {
        transform: translateY(2px);
        box-shadow: 0 1px 0 #7f1d1d;
    }

    /* Empty State */
    .players-empty {
        background: rgba(255, 255, 255, 0.01);
        border: 1.5px dashed rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 24px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .empty-icon {
        font-size: 24px;
        margin-bottom: 8px;
    }

    .empty-title {
        font-family: 'Press Start 2P', monospace;
        font-size: 8px;
        color: #64748b;
        margin-bottom: 4px;
    }

    .empty-subtitle {
        font-size: 11px;
        color: #475569;
    }
</style>

@php
$user = auth()->user();
@endphp

<div id="search-dashboard">
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="back-btn-container" onclick="goBack()">
            <img class="back-btn" src="/game_pacu/assets/image/back.png" alt="Kembali" onerror="this.src='/game_pacu/assets/image/ui/back.png'">
        </div>
        <div class="coin-display">
            <div class="coin-icon-wrapper">
                <img src="/game_pacu/assets/image/ui/koin.png" alt="Coin">
            </div>
            <span class="coin-amount">{{ number_format($user->kuansing_poin, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Main Search Area -->
    <div class="search-container">
        <!-- Search Box -->
        <div class="search-card">
            <div class="search-title">CARI PAMACU</div>
            <form action="/cari-pemain" method="GET" class="search-form">
                <input
                    type="text"
                    name="search"
                    class="search-input"
                    placeholder="Masukkan nama jalur..."
                    value="{{ $search }}"
                    autocomplete="off"
                >
                <button type="submit" class="search-btn">CARI</button>
            </form>
        </div>

        <!-- Player List Header -->
        <div class="section-header">
            {{ $search ? 'Hasil Pencarian' : 'Rekomendasi Pemain' }}
        </div>

        <!-- Player Cards -->
        <div class="players-list">
            @if (empty($players) || count($players) == 0)
                <div class="players-empty">
                    <div class="empty-icon">🔍</div>
                    <div class="empty-title">TIDAK DITEMUKAN</div>
                    <div class="empty-subtitle">Silakan cari dengan kata kunci lain.</div>
                </div>
            @else
                @foreach ($players as $player)
                    @php
                    $playerWins = $player->wins()->count();
                    $dbAvatar = $player->foto_profile;
                    if (!empty($dbAvatar)) {
                        if (strpos($dbAvatar, 'http://') === 0 || strpos($dbAvatar, 'https://') === 0) {
                            $avatarUrl = $dbAvatar;
                        } elseif (strpos($dbAvatar, '/') !== false || strpos($dbAvatar, '.gif') !== false) {
                            $avatarUrl = (strpos($dbAvatar, '/') === 0) ? $dbAvatar : '/' . $dbAvatar;
                        } else {
                            $avatarUrl = '/game_pacu/assets/image/ui/' . $dbAvatar . '.gif';
                        }
                    } else {
                        $avatarUrl = '/game_pacu/assets/image/ui/profil.gif';
                    }
                    @endphp
                    <div class="player-row">
                        <div class="player-info">
                            <div class="player-avatar-wrapper">
                                <img src="{{ $avatarUrl }}" alt="Avatar" class="player-avatar-img">
                            </div>
                            <div class="player-details">
                                <div class="player-name">{{ $player->nama_jalur ?? $player->email }}</div>
                                <div class="player-wins">🏆 {{ $playerWins }} Wins</div>
                            </div>
                        </div>
                        <button class="detail-btn" onclick="viewDetail({{ $player->id }})">PROFIL</button>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
{
    window.goBack = function() {
        window.navigateToPage('/main-menu');
    };

    window.viewDetail = function(id) {
        window.navigateToPage('/cari-pemain/detail/' + id);
    };
}
</script>
@endpush
