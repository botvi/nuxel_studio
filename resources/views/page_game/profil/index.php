<?php
$user = auth()->user();
$winsCount = $user->wins()->count();
$lossesCount = $user->losses()->count();
$statusText = 'ANAK BARU';
if ($winsCount >= 100) {
    $statusText = 'PAMACU INTI';
} elseif ($winsCount >= 50) {
    $statusText = 'PAMAIN SEWA';
}

// Fetch match history (riwayat permainan)
$userId = $user->id;
$history = \App\Models\Room::where('status', 'finished')
    ->where(function($query) use ($userId) {
        $query->where('host_id', $userId)
              ->orWhere('guest_id', $userId);
    })
    ->with(['host', 'guest', 'winner'])
    ->orderBy('updated_at', 'desc')
    ->take(10) // Get latest 10 matches
    ->get();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Franchise Game — Profil Pamacu</title>
    <link rel="stylesheet" href="/game_pacu/assets/css/game-layout.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Pixelify+Sans:wght@400;700&display=swap" rel="stylesheet">
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
            background: radial-gradient(circle at 50% 30%, rgba(59, 130, 246, 0.35) 0%, rgba(30, 41, 59, 0.25) 50%, rgba(15, 23, 42, 0.45) 100%), url('/game_pacu/assets/image/bg/bgmenu.jpg') no-repeat center center;
            background-size: cover;
            z-index: 10;
            box-sizing: border-box;
            overflow: hidden;
            padding-bottom: 10px;
        }

        /* Particles animation canvas */
        #ps5-particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
            opacity: 0.4;
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
            background: linear-gradient(90deg,
                    rgba(255, 255, 255, 0) 0%,
                    rgba(255, 255, 255, 0.6) 50%,
                    rgba(255, 255, 255, 0) 100%);
            transform: translateX(-150%) skewX(-25deg);
            animation: htmlShimmer 3s infinite ease-in-out;
            pointer-events: none;
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
            0% { transform: translateX(-150%) skewX(-25deg); }
            100% { transform: translateX(150%) skewX(-25deg); }
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

        /* Pixel scanline effect on card */
        .ps5-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 3px,
                rgba(0, 0, 0, 0.03) 3px,
                rgba(0, 0, 0, 0.03) 4px
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

        /* Online Status Badge Pulse */
        .status-dot-pulse {
            position: absolute;
            bottom: 3px;
            right: 3px;
            width: 13px;
            height: 13px;
            background-color: #22c55e;
            border-radius: 50%;
            border: 2px solid #0f172a;
            box-shadow: 0 0 8px #22c55e;
            animation: statusPulse 1.8s infinite ease-in-out;
        }

        @keyframes statusPulse {
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            70% { box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
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
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));
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

        .stat-card-gold .stat-value { color: #f59e0b; text-shadow: 0 0 6px rgba(245, 158, 11, 0.4); }
        .stat-card-silver .stat-value { color: #e2e8f0; text-shadow: 0 0 6px rgba(148, 163, 184, 0.4); }
        .stat-card-bronze .stat-value { color: #22d3ee; text-shadow: 0 0 6px rgba(6, 182, 212, 0.4); }

        /* Boat Preview Card container */
        .preview-panel {
            width: 100%;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(59, 130, 246, 0.15);
            border-radius: 16px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-sizing: border-box;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04),
                        0 0 16px rgba(59, 130, 246, 0.06);
        }

        .preview-panel-title {
            font-family: 'Press Start 2P', monospace;
            font-size: 7px;
            color: #38bdf8;
            margin-bottom: 8px;
            letter-spacing: 1px;
            text-shadow: 0 0 10px rgba(56, 189, 248, 0.5);
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
            text-shadow: 0 1px 2px rgba(0,0,0,0.5);
        }

        /* History Slide Container */
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
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
            color: #64748b;
            margin-bottom: 6px;
        }

        .history-empty-subtitle {
            font-size: 10px;
            color: #475569;
        }
    </style>
</head>

<body>

    <div id="desktop-wrapper">
        <div id="mobile-frame">
            <div id="status-bar">
                <span id="clock">00:00</span>
                <span>&#11044;&#11044;&#11044;</span>
            </div>
            
            <div id="game-container">
                <div id="profile-dashboard">
                    <!-- Ascending particles canvas -->
                    <canvas id="ps5-particles"></canvas>

                    <!-- Top bar: Back and Coin display -->
                    <div class="top-bar">
                        <div class="back-btn-container" onclick="goBack()">
                            <img class="back-btn" src="/game_pacu/assets/image/back.png" alt="Kembali" onerror="this.src='/game_pacu/assets/image/ui/back.png'">
                        </div>
                        <div class="coin-display" onclick="window.location.href='/shop'">
                            <div class="coin-icon-wrapper">
                                <img src="/game_pacu/assets/image/ui/koin.png" alt="Coin">
                            </div>
                            <span class="coin-amount"><?= number_format($user->kuansing_poin, 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <!-- Scrollable Area -->
                    <div class="profile-container scrollable">
                        
                        <!-- Main Card -->
                        <div class="ps5-card">


                            <!-- Avatar overlapping cover -->
                            <div class="profile-avatar-wrapper">
                                <div class="profile-avatar-container">
                                    <img id="profil-gif-page" src="/game_pacu/assets/image/ui/profil.gif" alt="profil" class="profile-avatar-img">
                                </div>
                                <div class="status-dot-pulse"></div>
                            </div>

                            <!-- Identity -->
                            <div class="profile-name"><?= e($user->nama_jalur ?? $user->email) ?></div>
                            <div class="profile-badge">⚡ <?= $statusText ?> ⚡</div>

                            <!-- Trophy Stats Grid -->
                            <div class="stats-row">
                                <div class="stat-card stat-card-gold">
                                    <span class="stat-icon">🏆</span>
                                    <span class="stat-label">Wins</span>
                                    <span class="stat-value"><?= $winsCount ?></span>
                                </div>
                                <div class="stat-card stat-card-silver">
                                    <span class="stat-icon">💀</span>
                                    <span class="stat-label">Losses</span>
                                    <span class="stat-value"><?= $lossesCount ?></span>
                                </div>
                                <div class="stat-card stat-card-bronze">
                                    <span class="stat-icon">🔥</span>
                                    <span class="stat-label">Win Rate</span>
                                    <span class="stat-value">
                                        <?php
                                        $total = $winsCount + $lossesCount;
                                        echo $total > 0 ? round(($winsCount / $total) * 100) . '%' : '0%';
                                        ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Boat Preview Panel -->
                            <div class="preview-panel">
                                <div class="preview-panel-title">✦ JALUR AKTIF ✦</div>
                                <div id="jalur-preview-container"></div>
                                <div id="jalur-name">Memuat Jalur...</div>
                            </div>
                        </div>

                        <!-- Game History Slider Section -->
                        <div class="section-header">✦ Riwayat Permainan ✦</div>
                        
                        <div class="history-slider scrollable">
                            <?php if ($history->isEmpty()): ?>
                                <div class="history-empty">
                                    <div class="history-empty-title">BELUM ADA RIWAYAT</div>
                                    <div class="history-empty-subtitle">Mainkan Quick Match untuk memulai!</div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($history as $match): ?>
                                    <?php
                                    $isWinner = ($match->winner_id === $user->id);
                                    $opponent = ($match->host_id === $user->id) ? $match->guest : $match->host;
                                    $opponentName = $opponent ? ($opponent->nama_jalur ?? $opponent->email) : 'Lawan';
                                    $formattedDate = $match->updated_at ? $match->updated_at->format('d/m H:i') : '';
                                    ?>
                                    <div class="history-card">
                                        <div class="history-outcome-row">
                                            <span class="outcome-badge <?= $isWinner ? 'outcome-win' : 'outcome-loss' ?>">
                                                <?= $isWinner ? 'WIN' : 'LOSS' ?>
                                            </span>
                                            <span class="history-room-code">#<?= e($match->room_code) ?></span>
                                        </div>
                                        <div class="history-opponent" title="vs <?= e($opponentName) ?>">
                                            vs <?= e($opponentName) ?>
                                        </div>
                                        <div class="history-time-row">
                                            <span class="history-date"><?= $formattedDate ?></span>
                                            <span class="history-mode"><?= e($match->name === 'Quick Match' ? 'QUICK' : 'ROOM') ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preload Phaser and scripts -->
    <script src="https://cdn.jsdelivr.net/npm/phaser@3.88.2/dist/phaser.min.js"></script>
    <script src="/game_pacu/assets/js/jalur-preview-phaser.js?v=<?= time() ?>"></script>
    <script src="/game_pacu/assets/js/game-layout.js?v=<?= time() ?>"></script>
    <script>
        // Synchronous page transition back
        function goBack() {
            window.location.href = '/main-menu';
        }

        // Initialize boat preview widget
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof window.initJalurPreview === 'function') {
                window.initJalurPreview('jalur-preview-container', 'jalur-name');
            }
        });

        // Background floating particles anim
        (function () {
            const canvas = document.getElementById('ps5-particles');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            let width = canvas.width = canvas.offsetWidth;
            let height = canvas.height = canvas.offsetHeight;

            const particles = [];
            const particleCount = 20;

            for (let i = 0; i < particleCount; i++) {
                particles.push({
                    x: Math.random() * width,
                    y: Math.random() * height + height,
                    size: Math.random() * 2 + 1,
                    speed: Math.random() * 0.3 + 0.1,
                    opacity: Math.random() * 0.4 + 0.2
                });
            }

            function animate() {
                ctx.clearRect(0, 0, width, height);
                ctx.fillStyle = '#ffffff';

                particles.forEach(p => {
                    ctx.globalAlpha = p.opacity;
                    ctx.fillRect(p.x, p.y, p.size, p.size);
                    p.y -= p.speed;
                    if (p.y < -10) {
                        p.y = height + 10;
                        p.x = Math.random() * width;
                    }
                });

                requestAnimationFrame(animate);
            }

            window.addEventListener('resize', () => {
                if (canvas.offsetWidth) {
                    width = canvas.width = canvas.offsetWidth;
                    height = canvas.height = canvas.offsetHeight;
                }
            });

            animate();
        })();

        // Load correct avatar GIF based on local storage or database
        (function () {
            const dbAvatar = '<?php echo auth()->user()->foto_profile ?? ""; ?>';
            const selectedAvatar = localStorage.getItem('selectedAvatar') || 'profil';
            const avatarEl = document.getElementById('profil-gif-page');

            function getAvatarUrl(avatarStr) {
                if (!avatarStr) return '';
                if (avatarStr.startsWith('http://') || avatarStr.startsWith('https://')) {
                    return avatarStr;
                }
                if (avatarStr.endsWith('.gif') || avatarStr.includes('/')) {
                    return avatarStr.startsWith('/') ? avatarStr : '/' + avatarStr;
                }
                return '/game_pacu/assets/image/ui/' + avatarStr + '.gif';
            }

            if (avatarEl) {
                const finalAvatar = dbAvatar || selectedAvatar;
                avatarEl.src = getAvatarUrl(finalAvatar) || '/game_pacu/assets/image/ui/profil.gif';
            }
        })();
    </script>
</body>

</html>