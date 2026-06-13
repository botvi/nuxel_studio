@extends('layouts.game')

@section('title', 'Franchise Game — Profil Pamacu')

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

    #profile-dashboard {
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
        padding: 16px 20px;
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

    .back-btn-container:active {
        transform: scale(0.9);
    }

    /* Coin Display (Top Right) from Menu style */
    .coin-display {
        display: flex;
        align-items: center;
        gap: 6px;
        z-index: 15;
        box-sizing: border-box;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .coin-display:hover {
        transform: scale(1.05);
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
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
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
            -1px 1px 0px #15803d,
            0px 1px 0px #15803d,
            0px -1px 0px #15803d,
            1px 0px 0px #15803d,
            -1px 0px 0px #15803d;
    }

    @keyframes htmlShimmer {
        0% {
            transform: translateX(-150%) skewX(-25deg);
        }

        100% {
            transform: translateX(150%) skewX(-25deg);
        }
    }

    /* Main Content Scrollable Area */
    .profile-container {
        flex: 1;
        display: flex;
        flex-direction: column;
        width: 100%;
        overflow-y: auto;
        z-index: 11;
        scrollbar-width: thin;
        scrollbar-color: rgba(59, 130, 246, 0.4) rgba(255, 255, 255, 0.02);
        box-sizing: border-box;
    }

    .profile-container::-webkit-scrollbar {
        width: 5px;
    }

    .profile-container::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.02);
        border-radius: 4px;
    }

    .profile-container::-webkit-scrollbar-thumb {
        background: rgba(59, 130, 246, 0.4);
        border-radius: 4px;
    }

    .profile-container::-webkit-scrollbar-thumb:hover {
        background: rgba(59, 130, 246, 0.6);
    }

    /* Premium PS5 Glass Card */
    .ps5-card {
        background: rgba(255, 255, 255, 0.025);
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 24px;
        width: calc(100% - 28px);
        margin: 0 auto 12px;
        padding: 16px;
        box-shadow:
            0 10px 32px rgba(0, 0, 0, 0.6),
            0 0 0 1px rgba(255, 255, 255, 0.05),
            inset 0 1px 0 rgba(255, 255, 255, 0.08);
        display: flex;
        flex-direction: column;
        align-items: center;
        box-sizing: border-box;
        position: relative;
        overflow: hidden;
    }

    .ps5-card::before {
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
        border-radius: 24px;
    }

    /* Profile Avatar wrapper container */
    .profile-avatar-wrapper {
        position: relative;
        margin-top: 6px;
        margin-bottom: 10px;
        z-index: 2;
    }

    .profile-avatar-container {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 3.5px solid #3b82f6;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.6), 0 0 15px rgba(59, 130, 246, 0.5);
        background: #0f172a;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
    }

    .profile-avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .status-dot-pulse {
        position: absolute;
        bottom: 3px;
        right: 3px;
        width: 13px;
        height: 13px;
        background-color: #22c55e;
        border-radius: 50%;
        border: 2px solid #0f172a;
        box-shadow: none;
    }

    /* Identity Details */
    .profile-name {
        font-size: 13px;
        font-weight: bold;
        color: #ffffff;
        margin-bottom: 6px;
        text-align: center;
        font-family: 'Press Start 2P', monospace;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    }

    .profile-badge {
        font-family: 'Press Start 2P', monospace;
        font-size: 8px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6);
        color: #ffffff;
        padding: 5px 12px;
        border-radius: 20px;
        box-shadow: 0 0 10px rgba(59, 130, 246, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.15);
        display: inline-block;
        margin-bottom: 16px;
        text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.3);
        text-transform: uppercase;
    }

    /* Trophy-style Stats grid */
    .stats-row {
        display: flex;
        gap: 8px;
        width: 100%;
        margin-bottom: 14px;
    }

    .stat-card {
        flex: 1;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        padding: 8px 6px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.05);
        box-sizing: border-box;
    }

    .stat-card-gold {
        border-color: rgba(245, 158, 11, 0.3);
        background: linear-gradient(180deg, rgba(245, 158, 11, 0.08) 0%, rgba(0, 0, 0, 0) 100%);
        box-shadow: inset 0 1px 0 rgba(245, 158, 11, 0.1), 0 0 12px rgba(245, 158, 11, 0.06);
    }

    .stat-card-silver {
        border-color: rgba(148, 163, 184, 0.3);
        background: linear-gradient(180deg, rgba(148, 163, 184, 0.08) 0%, rgba(0, 0, 0, 0) 100%);
        box-shadow: inset 0 1px 0 rgba(148, 163, 184, 0.1);
    }

    .stat-card-bronze {
        border-color: rgba(6, 182, 212, 0.3);
        background: linear-gradient(180deg, rgba(6, 182, 212, 0.08) 0%, rgba(0, 0, 0, 0) 100%);
        box-shadow: inset 0 1px 0 rgba(6, 182, 212, 0.1), 0 0 12px rgba(6, 182, 212, 0.06);
    }

    .stat-icon {
        font-size: 15px;
        margin-bottom: 4px;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.5));
    }

    .stat-label {
        font-family: 'Press Start 2P', monospace;
        font-size: 6px;
        color: #94a3b8;
        margin-bottom: 4px;
        text-transform: uppercase;
    }

    .stat-value {
        font-size: 13px;
        font-weight: bold;
        font-family: 'Pixelify Sans', monospace;
    }

    .stat-card-gold .stat-value {
        color: #f59e0b;
        text-shadow: 0 0 6px rgba(245, 158, 11, 0.4);
    }

    .stat-card-silver .stat-value {
        color: #e2e8f0;
        text-shadow: 0 0 6px rgba(148, 163, 184, 0.4);
    }

    .stat-card-bronze .stat-value {
        color: #22d3ee;
        text-shadow: 0 0 6px rgba(6, 182, 212, 0.4);
    }

    /* Boat Preview Card container */
    .preview-panel {
        width: 100%;
        background: rgba(0, 0, 0, 0.22);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 16px;
        padding: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        box-sizing: border-box;
    }

    .preview-panel-title {
        font-family: 'Press Start 2P', monospace;
        font-size: 7px;
        color: #38bdf8;
        margin-bottom: 6px;
        letter-spacing: 0.5px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        text-transform: uppercase;
    }

    #jalur-preview-container {
        width: 250px;
        height: 85px;
        border-radius: 10px;
        overflow: hidden;
        background: rgba(0, 0, 0, 0.25);
        border: 1px solid rgba(59, 130, 246, 0.2);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    #jalur-name {
        font-family: 'Pixelify Sans', monospace;
        font-size: 13px;
        font-weight: bold;
        color: #f59e0b;
        margin-top: 6px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
    }

    .section-header {
        width: calc(100% - 28px);
        margin: 4px auto 6px;
        font-family: 'Press Start 2P', monospace;
        font-size: 8px;
        color: #38bdf8;
        letter-spacing: 0.5px;
        text-align: left;
        text-shadow: 2px 2px 0px #000000;
        text-transform: uppercase;
    }

    .history-slider {
        width: calc(100% - 28px);
        margin: 0 auto 10px;
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding-bottom: 8px;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #3b82f6 rgba(15, 23, 42, 0.6);
        box-sizing: border-box;
        touch-action: pan-x !important;
    }

    .history-slider::-webkit-scrollbar {
        height: 5px;
    }

    .history-slider::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.3);
        border-radius: 4px;
    }

    .history-slider::-webkit-scrollbar-thumb {
        background: #3b82f6;
        border-radius: 4px;
    }

    .history-slider::-webkit-scrollbar-thumb:hover {
        background: #2563eb;
    }

    .history-card {
        flex-shrink: 0;
        width: 136px;
        height: 86px;
        border-radius: 12px;
        background: #0f172a;
        border: 1.5px solid rgba(255, 255, 255, 0.12);
        padding: 8px 10px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        scroll-snap-align: start;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        box-sizing: border-box;
        transition: all 0.2s ease;
    }

    .history-card:hover {
        border-color: #3b82f6;
        background: #1e293b;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.25);
    }

    .history-outcome-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .outcome-badge {
        font-size: 8px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 6px;
        font-family: 'Press Start 2P', monospace;
        text-transform: uppercase;
    }

    .outcome-win {
        background: rgba(34, 197, 94, 0.15);
        color: #4ade80;
        border: 1px solid rgba(34, 197, 94, 0.3);
        text-shadow: 0 0 5px rgba(74, 222, 128, 0.4);
    }

    .outcome-loss {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.3);
        text-shadow: 0 0 5px rgba(248, 113, 113, 0.4);
    }

    .history-room-code {
        font-family: 'Press Start 2P', monospace;
        font-size: 6px;
        color: #64748b;
    }

    .history-opponent {
        font-size: 11px;
        font-weight: 600;
        color: #e2e8f0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin: 4px 0;
    }

    .history-time-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 8px;
        color: #64748b;
    }

    .history-date {
        font-size: 8px;
        font-family: 'Pixelify Sans', monospace;
    }

    .history-mode {
        font-size: 7px;
        font-family: 'Press Start 2P', monospace;
        color: #475569;
    }

    /* Empty state layout */
    .history-empty {
        width: 100%;
        background: rgba(255, 255, 255, 0.01);
        border: 1.5px dashed rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        padding: 16px;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        box-sizing: border-box;
    }

    .history-empty-title {
        font-family: 'Press Start 2P', monospace;
        font-size: 7px;
        color: #38bdf8;
        margin-bottom: 6px;
        text-shadow: 0 0 5px rgba(56, 189, 248, 0.3);
    }

    .history-empty-subtitle {
        font-size: 10px;
        color: rgba(255, 255, 255, 0.6);
    }
</style>

@php
$user = auth()->user();
$winsCount = $targetUser->wins()->count();
$lossesCount = $targetUser->losses()->count();
$statusText = 'ANAK BARU';
if ($winsCount >= 100) {
    $statusText = 'PAMACU INTI';
} elseif ($winsCount >= 50) {
    $statusText = 'PAMAIN SEWA';
}

// Fetch match history (riwayat permainan) for targetUser
$targetUserId = $targetUser->id;
$history = \App\Models\Room::where('status', 'finished')
    ->where(function ($query) use ($targetUserId) {
        $query->where('host_id', $targetUserId)
            ->orWhere('guest_id', $targetUserId);
    })
    ->with(['host', 'guest', 'winner'])
    ->orderBy('updated_at', 'desc')
    ->take(10)
    ->get();

// Custom Colors and skins for targetUser
$modelJalur = $targetUser->modelJalur;
$customColors = $modelJalur->model_jalur['customColors'] ?? [
    'boat' => '#8D6E63',
    'hair' => '#111827',
    'pants' => '#38a169',
    'shirt' => '#10B981',
    'paddle' => '#8D6E63',
    'splash' => '#a5f3fc',
];
$corakDataUrl = ($modelJalur && ($modelJalur->model_jalur['boat_unlocked'] ?? false)) ? ($modelJalur->model_jalur['corak_data_url'] ?? null) : null;
$lambaiDataUrl = ($modelJalur && ($modelJalur->model_jalur['lambai_unlocked'] ?? false)) ? ($modelJalur->model_jalur['lambai_data_url'] ?? null) : null;
@endphp

<div id="profile-dashboard">
    <!-- Top bar: Back and Coin display -->
    <div class="top-bar">
        <div class="back-btn-container" onclick="goBack()">
            <img class="back-btn" src="/game_pacu/assets/image/back.png" alt="Kembali"
                onerror="this.src='/game_pacu/assets/image/ui/back.png'">
        </div>
        <div class="coin-display">
            <div class="coin-icon-wrapper">
                <img src="/game_pacu/assets/image/ui/koin.png" alt="Coin">
            </div>
            <span class="coin-amount">{{ number_format($user->kuansing_poin, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Scrollable Area -->
    <div class="profile-container scrollable">

        <!-- Main Card -->
        <div class="ps5-card">

            <!-- Avatar overlapping cover -->
            <div class="profile-avatar-wrapper">
                <div class="profile-avatar-container">
                    @php
                    $dbFoto = $targetUser->foto_profile;
                    if (!empty($dbFoto)) {
                        if (strpos($dbFoto, 'http://') === 0 || strpos($dbFoto, 'https://') === 0) {
                            $profileImgSrc = $dbFoto;
                        } elseif (strpos($dbFoto, '/') !== false || strpos($dbFoto, '.gif') !== false) {
                            $profileImgSrc = (strpos($dbFoto, '/') === 0) ? $dbFoto : '/' . $dbFoto;
                        } else {
                            $profileImgSrc = '/game_pacu/assets/image/ui/' . $dbFoto . '.gif';
                        }
                    } else {
                        $profileImgSrc = '/game_pacu/assets/image/ui/profil.gif';
                    }
                    @endphp
                    <img src="{{ $profileImgSrc }}" alt="profil" class="profile-avatar-img">
                </div>
                <div class="status-dot-pulse"></div>
            </div>

            <!-- Identity -->
            <div class="profile-name">{{ $targetUser->nama_jalur ?? $targetUser->email }}</div>
            <div class="profile-badge">⚡ {{ $statusText }} ⚡</div>

            <!-- Trophy Stats Grid -->
            <div class="stats-row">
                <div class="stat-card stat-card-gold">
                    <span class="stat-icon">🏆</span>
                    <span class="stat-label">Wins</span>
                    <span class="stat-value">{{ $winsCount }}</span>
                </div>
                <div class="stat-card stat-card-silver">
                    <span class="stat-icon">💀</span>
                    <span class="stat-label">Losses</span>
                    <span class="stat-value">{{ $lossesCount }}</span>
                </div>
                <div class="stat-card stat-card-bronze">
                    <span class="stat-icon">🔥</span>
                    <span class="stat-label">Win Rate</span>
                    <span class="stat-value">
                        @php
                        $total = $winsCount + $lossesCount;
                        echo $total > 0 ? round(($winsCount / $total) * 100) . '%' : '0%';
                        @endphp
                    </span>
                </div>
            </div>

            <!-- Boat Preview Panel -->
            <div class="preview-panel">
                <div class="preview-panel-title">✦ JALUR AKTIF ✦</div>
                <div id="jalur-preview-container"></div>
                <div id="jalur-name">{{ $targetUser->nama_jalur ?? 'Jalur Kuansing' }}</div>
            </div>
        </div>

        <!-- Game History Slider Section -->
        <div class="section-header">✦ Riwayat Permainan ✦</div>

        <div class="history-slider scrollable">
            @if ($history->isEmpty())
                <div class="history-empty">
                    <div class="history-empty-title">BELUM ADA RIWAYAT</div>
                    <div class="history-empty-subtitle">Pemain ini belum bertanding.</div>
                </div>
            @else
                @foreach ($history as $match)
                    @php
                    $isWinner = ($match->winner_id === $targetUser->id);
                    $opponent = ($match->host_id === $targetUser->id) ? $match->guest : $match->host;
                    $opponentName = $opponent ? ($opponent->nama_jalur ?? $opponent->email) : 'Lawan';
                    $formattedDate = $match->updated_at ? $match->updated_at->format('d/m H:i') : '';
                    @endphp
                    <div class="history-card">
                        <div class="history-outcome-row">
                            <span class="outcome-badge {{ $isWinner ? 'outcome-win' : 'outcome-loss' }}">
                                {{ $isWinner ? 'WIN' : 'LOSS' }}
                            </span>
                            <span class="history-room-code">#{{ $match->room_code }}</span>
                        </div>
                        <div class="history-opponent" title="vs {{ $opponentName }}">
                            vs {{ $opponentName }}
                        </div>
                        <div class="history-time-row">
                            <span class="history-date">{{ $formattedDate }}</span>
                            <span class="history-mode">{{ $match->name === 'Quick Match' ? 'QUICK' : 'ROOM' }}</span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/phaser@3.88.2/dist/phaser.min.js"></script>
<script>
{
    const customColors = @json($customColors);
    const corakDataUrl = @json($corakDataUrl);
    const lambaiDataUrl = @json($lambaiDataUrl);

    window.goBack = function() {
        window.navigateToPage('/cari-pemain');
    };

    document.addEventListener('DOMContentLoaded', () => {
        if (window.Phaser) {
            initJalurPreviewCustom('jalur-preview-container');
        }
    });

    document.addEventListener('livewire:navigated', () => {
        if (window.Phaser && document.getElementById('jalur-preview-container')) {
            initJalurPreviewCustom('jalur-preview-container');
        }
    }, { once: true });

    function initJalurPreviewCustom(containerId) {
        if (window.activePreviewGame) {
            window.activePreviewGame.destroy(true);
            window.activePreviewGame = null;
        }

        window.activePreviewGame = new Phaser.Game({
            type: Phaser.AUTO,
            width: 250,
            height: 85,
            transparent: true,
            parent: containerId,
            pixelArt: true,
            scene: {
                preload: function () {
                    this.load.image('jalur_boat', '/game_pacu/assets/image/jalur/jalur.png');
                    this.load.image('char1', '/game_pacu/assets/image/char/1.png');
                    this.load.image('char2', '/game_pacu/assets/image/char/2.png');
                    this.load.image('char3', '/game_pacu/assets/image/char/3.png');
                    this.load.image('char4', '/game_pacu/assets/image/char/4.png');
                    this.load.image('char5', '/game_pacu/assets/image/char/5.png');
                },
                create: function () {
                    const scene = this;
                    const scaleMult = 0.8;
                    const BOAT_SCALE = 2.3 * scaleMult;
                    const ROWER_SCALE = 0.18 * scaleMult;
                    const BOAT_OFFSET_X = 0;
                    const BOAT_OFFSET_Y = 15 * scaleMult;
                    const ROWER_OFFSET_X = -25 * scaleMult;
                    const ROWER_OFFSET_Y = -25 * scaleMult;
                    const ROWER_SPACING = 35 * scaleMult;

                    const boatGroup = scene.add.container(125, 40);

                    // Recolor character image function
                    function recolorCharacterImage(sourceKey) {
                        const sourceTexture = scene.textures.get(sourceKey);
                        const sourceImage = sourceTexture.getSourceImage();
                        const canvas = document.createElement('canvas');
                        canvas.width = sourceImage.width;
                        canvas.height = sourceImage.height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(sourceImage, 0, 0);

                        const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const data = imgData.data;

                        const targetHair = Phaser.Display.Color.HexStringToColor(customColors.hair);
                        const targetShirt = Phaser.Display.Color.HexStringToColor(customColors.shirt);
                        const targetPants = Phaser.Display.Color.HexStringToColor(customColors.pants);
                        const targetPaddle = Phaser.Display.Color.HexStringToColor(customColors.paddle);

                        for (let i = 0; i < data.length; i += 4) {
                            const r = data[i], g = data[i + 1], b = data[i + 2], a = data[i + 3];
                            if (a < 10) continue;
                            if (r < 40 && g < 40 && b < 40) continue;

                            if (r - g > 100 && r - b > 100) {
                                const factor = Math.min(1.2, r / 199);
                                data[i] = Math.min(255, targetHair.r * factor);
                                data[i + 1] = Math.min(255, targetHair.g * factor);
                                data[i + 2] = Math.min(255, targetHair.b * factor);
                            } else if (g - r > 50 && g - b > 40) {
                                const factor = Math.min(1.2, g / 122);
                                data[i] = Math.min(255, targetPants.r * factor);
                                data[i + 1] = Math.min(255, targetPants.g * factor);
                                data[i + 2] = Math.min(255, targetPants.b * factor);
                            } else if (b - r > 80 && b - g > 40) {
                                const factor = Math.min(1.2, b / 203);
                                data[i] = Math.min(255, targetPaddle.r * factor);
                                data[i + 1] = Math.min(255, targetPaddle.g * factor);
                                data[i + 2] = Math.min(255, targetPaddle.b * factor);
                            } else if (Math.abs(r - g) < 20 && Math.abs(g - b) < 20 && Math.abs(r - b) < 20) {
                                const factor = Math.min(1.2, ((r + g + b) / 3) / 78);
                                data[i] = Math.min(255, targetShirt.r * factor);
                                data[i + 1] = Math.min(255, targetShirt.g * factor);
                                data[i + 2] = Math.min(255, targetShirt.b * factor);
                            }
                        }
                        ctx.putImageData(imgData, 0, 0);
                        return canvas;
                    }

                    // Apply recolors to animations
                    const rowerSprites = [];
                    for (let f = 1; f <= 5; f++) {
                        const canvas = recolorCharacterImage(`char${f}`);
                        scene.textures.addCanvas(`recolored_char${f}`, canvas);
                    }

                    scene.anims.create({
                        key: 'rowing_anim',
                        frames: [
                            { key: 'recolored_char1' }, { key: 'recolored_char2' },
                            { key: 'recolored_char3' }, { key: 'recolored_char4' },
                            { key: 'recolored_char5' }
                        ],
                        frameRate: 8, repeat: -1
                    });

                    const boatImg = scene.add.image(BOAT_OFFSET_X, BOAT_OFFSET_Y, 'jalur_boat');
                    boatImg.setScale(BOAT_SCALE);
                    boatImg.texture.setFilter(Phaser.Textures.FilterMode.NEAREST);
                    const boatColorInt = Phaser.Display.Color.HexStringToColor(customColors.boat).color;
                    boatImg.setTint(boatColorInt);
                    boatGroup.add(boatImg);

                    const offsetsX = [-ROWER_SPACING * 2, -ROWER_SPACING, 0, ROWER_SPACING, ROWER_SPACING * 2];

                    offsetsX.forEach((offsetX) => {
                        const rowerX = BOAT_OFFSET_X + ROWER_OFFSET_X + offsetX;
                        const rowerY = BOAT_OFFSET_Y + ROWER_OFFSET_Y;
                        const rowerSprite = scene.add.sprite(rowerX, rowerY, 'char1');
                        rowerSprite.setScale(ROWER_SCALE);
                        rowerSprite.texture.setFilter(Phaser.Textures.FilterMode.NEAREST);
                        boatGroup.add(rowerSprite);
                        rowerSprites.push(rowerSprite);
                        rowerSprite.play('rowing_anim');
                    });

                    // Apply Corak if unlocked
                    if (corakDataUrl) {
                        const img = new Image();
                        img.onload = () => {
                            if (!scene.sys.isActive()) return;
                            const boatSource = scene.textures.get('jalur_boat').getSourceImage();
                            const displayW = Math.round(boatSource.width * BOAT_SCALE);
                            const displayH = Math.round(boatSource.height * BOAT_SCALE);

                            const maskCanvas = document.createElement('canvas');
                            maskCanvas.width = displayW; maskCanvas.height = displayH;
                            const ctx = maskCanvas.getContext('2d');

                            const scaleFactor = (displayW / img.width);
                            const drawW = displayW;
                            const drawH = Math.round(img.height * scaleFactor);
                            ctx.drawImage(img, 0, Math.round((displayH - drawH) / 2), drawW, drawH);

                            const imageData = ctx.getImageData(0, 0, displayW, displayH);
                            const data = imageData.data;
                            for (let i = 0; i < data.length; i += 4) {
                                if (data[i] > 240 && data[i + 1] > 240 && data[i + 2] > 240) {
                                    data[i + 3] = 0;
                                }
                            }
                            ctx.putImageData(imageData, 0, 0);
                            ctx.globalCompositeOperation = 'destination-in';
                            ctx.drawImage(boatSource, 0, 0, displayW, displayH);
                            ctx.globalCompositeOperation = 'source-over';

                            if (scene.textures.exists('corak_texture')) scene.textures.remove('corak_texture');
                            scene.textures.addCanvas('corak_texture', maskCanvas);
                            const corakSprite = scene.make.image({ x: BOAT_OFFSET_X, y: BOAT_OFFSET_Y, key: 'corak_texture', add: false });
                            corakSprite.setAlpha(0.82);
                            corakSprite.setBlendMode(Phaser.BlendModes.MULTIPLY);
                            boatGroup.add(corakSprite);
                            boatGroup.moveTo(corakSprite, boatGroup.getIndex(boatImg) + 1);
                        };
                        img.src = corakDataUrl;
                    }

                    // Apply Lambai if unlocked
                    if (lambaiDataUrl) {
                        const img = new Image();
                        img.onload = () => {
                            if (!scene.sys.isActive()) return;
                            const LAMBAI_SCALE = 1.3 * scaleMult;
                            const LAMBAI_OFFSET_X = 125 * scaleMult;
                            const LAMBAI_OFFSET_Y = -18 * scaleMult;
                            const targetSize = 48;
                            let w = img.width, h = img.height;
                            if (w > h) { h = Math.round((h / w) * targetSize); w = targetSize; }
                            else { w = Math.round((w / h) * targetSize); h = targetSize; }

                            const canvas = document.createElement('canvas');
                            canvas.width = w; canvas.height = h;
                            const ctx = canvas.getContext('2d');
                            ctx.imageSmoothingEnabled = false;
                            ctx.drawImage(img, 0, 0, w, h);

                            const imageData = ctx.getImageData(0, 0, w, h);
                            const data = imageData.data;
                            for (let i = 0; i < data.length; i += 4) {
                                if (data[i] > 240 && data[i + 1] > 240 && data[i + 2] > 240) data[i + 3] = 0;
                            }
                            ctx.putImageData(imageData, 0, 0);

                            if (scene.textures.exists('lambai_texture')) scene.textures.remove('lambai_texture');
                            scene.textures.addCanvas('lambai_texture', canvas);
                            const lambaiSprite = scene.make.image({ x: LAMBAI_OFFSET_X, y: LAMBAI_OFFSET_Y, key: 'lambai_texture', add: false });
                            lambaiSprite.setScale(LAMBAI_SCALE);
                            lambaiSprite.texture.setFilter(Phaser.Textures.FilterMode.NEAREST);
                            boatGroup.add(lambaiSprite);
                            boatGroup.moveTo(lambaiSprite, boatGroup.getIndex(boatImg));
                        };
                        img.src = lambaiDataUrl;
                    }

                    // Idle bobbing animation
                    scene.tweens.add({
                        targets: boatGroup,
                        y: boatGroup.y + 4 * scaleMult,
                        duration: 1200,
                        yoyo: true,
                        repeat: -1,
                        ease: 'Sine.easeInOut'
                    });
                }
            }
        });
    }

    // Cleanup Phaser preview instance on navigation
    document.addEventListener('livewire:navigating', () => {
        if (window.activePreviewGame) {
            window.activePreviewGame.destroy(true);
            window.activePreviewGame = null;
            console.log('Target user preview Phaser destroyed.');
        }
    }, { once: true });
}
</script>
@endpush
