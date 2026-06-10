<?php
$user = auth()->user();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Franchise Game — Leaderboard</title>
    <link rel="stylesheet" href="/game_pacu/assets/css/game-layout.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Pixelify+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #0c111d;
            color: #111111;
            font-family: 'Pixelify Sans', monospace;
            overflow: hidden;
        }

        #leaderboard-dashboard {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            background: radial-gradient(circle at 50% 20%, rgba(234, 179, 8, 0.25) 0%, rgba(30, 41, 59, 0.2) 50%, rgba(15, 23, 42, 0.5) 100%),
                        url('/game_pacu/assets/image/bg/bgmenu.jpg') no-repeat center center;
            background-size: cover;
            z-index: 10;
            box-sizing: border-box;
            overflow: hidden;
            padding-bottom: 10px;
        }

        /* Particles */
        #ps5-particles {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 1;
            pointer-events: none;
            opacity: 0.35;
        }

        /* Top Bar */
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
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        .back-btn { width: 36px; height: 36px; }
        .back-btn-container:hover { transform: scale(1.1); }

        .coin-display {
            display: flex; align-items: center; gap: 6px;
            z-index: 15;
        }
        .coin-icon-wrapper {
            position: relative;
            width: 36px; height: 36px;
            border-radius: 50%; overflow: hidden;
            display: flex; align-items: center; justify-content: center;
        }
        .coin-icon-wrapper img { width: 100%; height: 100%; image-rendering: pixelated; }
        .coin-icon-wrapper::after {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: linear-gradient(90deg,
                rgba(255,255,255,0) 0%,
                rgba(255,255,255,0.6) 50%,
                rgba(255,255,255,0) 100%);
            transform: translateX(-150%) skewX(-25deg);
            animation: coinShimmer 3s infinite ease-in-out;
            pointer-events: none;
        }
        @keyframes coinShimmer {
            0%   { transform: translateX(-150%) skewX(-25deg); }
            100% { transform: translateX(150%) skewX(-25deg); }
        }
        .coin-amount {
            font-family: 'Pixelify Sans', monospace;
            font-size: 13px; font-weight: bold;
            color: #FFD700; line-height: 1;
            text-shadow: 1px 1px 0px #15803d, -1px -1px 0px #15803d,
                         1px -1px 0px #15803d, -1px 1px 0px #15803d;
        }

        /* Title */
        .lb-title-wrap {
            z-index: 11;
            padding: 0 14px 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }
        .lb-main-title {
            font-family: 'Press Start 2P', monospace;
            font-size: 12px;
            color: #fbbf24;
            text-shadow: 0 0 12px rgba(251, 191, 36, 0.6), 2px 2px 0px #92400e;
            letter-spacing: 1px;
        }
        .lb-subtitle {
            font-size: 11px;
            color: rgba(0,0,0,0.55);
        }

        /* Filter Tabs */
        .filter-tabs {
            display: flex;
            gap: 8px;
            z-index: 11;
            padding: 0 14px 10px;
            justify-content: center;
        }
        .filter-tab {
            background: rgba(0,0,0,0.08);
            border: 1px solid rgba(0,0,0,0.15);
            border-radius: 10px;
            padding: 7px 14px;
            font-family: 'Press Start 2P', monospace;
            font-size: 6px;
            color: rgba(0,0,0,0.6);
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            white-space: nowrap;
        }
        .filter-tab:hover {
            border-color: rgba(251, 191, 36, 0.4);
            color: #fbbf24;
        }
        .filter-tab.active {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border-color: #f59e0b;
            color: #0c111d;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        }

        /* My Rank Banner */
        .my-rank-banner {
            z-index: 11;
            margin: 0 14px 10px;
            background: rgba(251, 191, 36, 0.07);
            border: 1px solid rgba(251, 191, 36, 0.2);
            border-radius: 14px;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3), inset 0 1px 0 rgba(251,191,36,0.1);
        }
        .my-rank-left {
            display: flex; align-items: center; gap: 10px;
        }
        .my-rank-badge {
            font-family: 'Press Start 2P', monospace;
            font-size: 8px;
            color: #fbbf24;
            background: rgba(251,191,36,0.15);
            border-radius: 8px;
            padding: 5px 8px;
        }
        .my-rank-name {
            font-family: 'Press Start 2P', monospace;
            font-size: 7px;
            color: #111111;
        }
        .my-rank-info {
            display: flex; gap: 12px;
            font-size: 10px;
        }
        .my-rank-stat { display: flex; flex-direction: column; align-items: flex-end; gap: 1px; }
        .my-rank-stat-val { font-weight: bold; color: #fbbf24; }
        .my-rank-stat-lbl { color: rgba(0,0,0,0.45); font-size: 9px; }

        /* Scroll container */
        .lb-scroll {
            flex: 1;
            overflow-y: auto;
            z-index: 11;
            padding: 0 14px;
            scrollbar-width: thin;
            scrollbar-color: rgba(251, 191, 36, 0.3) rgba(255,255,255,0.02);
        }
        .lb-scroll::-webkit-scrollbar { width: 4px; }
        .lb-scroll::-webkit-scrollbar-thumb {
            background: rgba(251, 191, 36, 0.3);
            border-radius: 4px;
        }

        /* Rank Header */
        .rank-header {
            display: flex;
            align-items: center;
            padding: 0 12px 6px;
            font-family: 'Press Start 2P', monospace;
            font-size: 6px;
            color: rgba(0,0,0,0.45);
            letter-spacing: 0.5px;
        }
        .rh-rank { width: 32px; }
        .rh-player { flex: 1; }
        .rh-wins { width: 48px; text-align: center; }
        .rh-losses { width: 48px; text-align: center; }
        .rh-winrate { width: 52px; text-align: right; }

        /* Row */
        .lb-row {
            display: flex;
            align-items: center;
            background: rgba(15, 23, 42, 0.65);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 14px;
            padding: 10px 12px;
            margin-bottom: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 3px 10px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.03);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            cursor: pointer;
        }
        .lb-row:hover {
            border-color: rgba(251, 191, 36, 0.35);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.4), 0 0 14px rgba(251,191,36,0.08);
        }
        .lb-row.is-me {
            border-color: rgba(251, 191, 36, 0.5);
            background: rgba(251, 191, 36, 0.07);
        }
        .lb-row.top1 { border-color: rgba(255, 215, 0, 0.55); background: rgba(255,215,0,0.06); }
        .lb-row.top2 { border-color: rgba(192, 192, 192, 0.45); background: rgba(192,192,192,0.04); }
        .lb-row.top3 { border-color: rgba(205, 127, 50, 0.45); background: rgba(205,127,50,0.04); }

        /* Rank Number */
        .lb-rank {
            width: 32px;
            font-family: 'Press Start 2P', monospace;
            font-size: 8px;
            color: rgba(0,0,0,0.45);
            flex-shrink: 0;
        }
        .lb-rank.r1 { color: #FFD700; text-shadow: 0 0 8px rgba(255,215,0,0.5); }
        .lb-rank.r2 { color: #C0C0C0; text-shadow: 0 0 6px rgba(192,192,192,0.4); }
        .lb-rank.r3 { color: #CD7F32; text-shadow: 0 0 6px rgba(205,127,50,0.4); }

        /* Avatar */
        .lb-avatar-wrap {
            width: 36px; height: 36px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.12);
            overflow: hidden;
            flex-shrink: 0;
            margin-right: 10px;
            background: #0f172a;
        }
        .lb-row.top1 .lb-avatar-wrap { border-color: #FFD700; box-shadow: 0 0 8px rgba(255,215,0,0.4); }
        .lb-row.top2 .lb-avatar-wrap { border-color: #C0C0C0; }
        .lb-row.top3 .lb-avatar-wrap { border-color: #CD7F32; }
        .lb-row.is-me .lb-avatar-wrap { border-color: #fbbf24; }
        .lb-avatar-wrap img { width: 100%; height: 100%; object-fit: cover; }

        /* Player info */
        .lb-info { flex: 1; min-width: 0; }
        .lb-name {
            font-family: 'Press Start 2P', monospace;
            font-size: 7px;
            color: #111111;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            max-width: 110px;
        }
        .lb-badge-me {
            font-size: 9px;
            color: #fbbf24;
            display: inline-block;
            margin-left: 4px;
        }
        .lb-totalmatch {
            font-size: 9px;
            color: rgba(0,0,0,0.4);
            margin-top: 2px;
        }

        /* Stats */
        .lb-wins {
            width: 48px;
            text-align: center;
            font-family: 'Press Start 2P', monospace;
            font-size: 8px;
            color: #4ade80;
            flex-shrink: 0;
        }
        .lb-losses {
            width: 48px;
            text-align: center;
            font-family: 'Press Start 2P', monospace;
            font-size: 8px;
            color: #f87171;
            flex-shrink: 0;
        }
        .lb-winrate {
            width: 52px;
            text-align: right;
            font-size: 10px;
            font-weight: bold;
            flex-shrink: 0;
        }
        .lb-winrate span {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Empty */
        .lb-empty {
            text-align: center;
            padding: 40px 20px;
            color: rgba(0,0,0,0.3);
        }
        .lb-empty-icon { font-size: 30px; margin-bottom: 10px; }
        .lb-empty-title { font-family: 'Press Start 2P', monospace; font-size: 8px; color: #475569; }

        /* Crown icons for top 3 */
        .crown { font-size: 11px; }
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
                <div id="leaderboard-dashboard">
                    <!-- Particles -->
                    <canvas id="ps5-particles"></canvas>

                    <!-- Top Bar -->
                    <div class="top-bar">
                        <div class="back-btn-container" onclick="goBack()">
                            <img class="back-btn" src="/game_pacu/assets/image/back.png" alt="Kembali"
                                 onerror="this.src='/game_pacu/assets/image/ui/back.png'">
                        </div>
                        <div class="coin-display">
                            <div class="coin-icon-wrapper">
                                <img src="/game_pacu/assets/image/ui/koin.png" alt="Coin">
                            </div>
                            <span class="coin-amount"><?= number_format($user->kuansing_poin, 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <!-- Title -->
                    <div class="lb-title-wrap">
                        <div class="lb-main-title">🏆 LEADERBOARD</div>
                        <div class="lb-subtitle">Peringkat Pamacu Terbaik</div>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="filter-tabs">
                        <a href="/leaderboard?filter=wins"
                           class="filter-tab <?= $filter === 'wins' ? 'active' : '' ?>">
                            🥇 WINS
                        </a>
                        <a href="/leaderboard?filter=losses"
                           class="filter-tab <?= $filter === 'losses' ? 'active' : '' ?>">
                            💀 LOSSES
                        </a>
                        <a href="/leaderboard?filter=winrate"
                           class="filter-tab <?= $filter === 'winrate' ? 'active' : '' ?>">
                            📊 WIN RATE
                        </a>
                    </div>

                    <!-- My Rank Banner -->
                    <?php
                    $myUser = $currentUser;
                    $myWins = $myUser->wins_count ?? $myUser->wins()->count();
                    $myLosses = $myUser->losses_count ?? $myUser->losses()->count();
                    $myTotal = $myWins + $myLosses;
                    $myWr = $myTotal > 0 ? round(($myWins / $myTotal) * 100, 1) : 0;

                    $dbAvatarMe = $myUser->foto_profile;
                    if (!empty($dbAvatarMe)) {
                        if (strpos($dbAvatarMe, 'http://') === 0 || strpos($dbAvatarMe, 'https://') === 0) {
                            $myAvatar = $dbAvatarMe;
                        } elseif (strpos($dbAvatarMe, '/') !== false || strpos($dbAvatarMe, '.gif') !== false) {
                            $myAvatar = (strpos($dbAvatarMe, '/') === 0) ? $dbAvatarMe : '/' . $dbAvatarMe;
                        } else {
                            $myAvatar = '/game_pacu/assets/image/ui/' . $dbAvatarMe . '.gif';
                        }
                    } else {
                        $myAvatar = '/game_pacu/assets/image/ui/profil.gif';
                    }
                    ?>
                    <div class="my-rank-banner">
                        <div class="my-rank-left">
                            <div class="my-rank-badge">
                                #<?= $myRank ?? '—' ?>
                            </div>
                            <div>
                                <div class="my-rank-name"><?= e($myUser->nama_jalur ?? $myUser->email) ?></div>
                                <div style="font-size:9px;color:rgba(0,0,0,0.45);margin-top:2px;">Kamu</div>
                            </div>
                        </div>
                        <div class="my-rank-info">
                            <div class="my-rank-stat">
                                <span class="my-rank-stat-val" style="color:#4ade80"><?= $myWins ?></span>
                                <span class="my-rank-stat-lbl">Wins</span>
                            </div>
                            <div class="my-rank-stat">
                                <span class="my-rank-stat-val" style="color:#f87171"><?= $myLosses ?></span>
                                <span class="my-rank-stat-lbl">Losses</span>
                            </div>
                            <div class="my-rank-stat">
                                <span class="my-rank-stat-val" style="color:#fbbf24"><?= $myWr ?>%</span>
                                <span class="my-rank-stat-lbl">WR</span>
                            </div>
                        </div>
                    </div>

                    <!-- Scrollable List -->
                    <div class="lb-scroll">
                        <!-- Header Labels -->
                        <div class="rank-header">
                            <span class="rh-rank">#</span>
                            <span class="rh-player">PLAYER</span>
                            <span class="rh-wins">W</span>
                            <span class="rh-losses">L</span>
                            <span class="rh-winrate">WR%</span>
                        </div>

                        <?php if ($leaderboard->isEmpty()): ?>
                            <div class="lb-empty">
                                <div class="lb-empty-icon">🏁</div>
                                <div class="lb-empty-title">BELUM ADA DATA</div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($leaderboard as $idx => $player): ?>
                                <?php
                                $rank = $idx + 1;
                                $isMe = $player->id === $currentUser->id;

                                // Row class
                                $rowClass = 'lb-row';
                                if ($rank === 1) $rowClass .= ' top1';
                                elseif ($rank === 2) $rowClass .= ' top2';
                                elseif ($rank === 3) $rowClass .= ' top3';
                                if ($isMe) $rowClass .= ' is-me';

                                // Rank label
                                $rankClass = 'lb-rank';
                                if ($rank === 1) $rankClass .= ' r1';
                                elseif ($rank === 2) $rankClass .= ' r2';
                                elseif ($rank === 3) $rankClass .= ' r3';

                                // Crown
                                $rankLabel = '#' . $rank;
                                if ($rank === 1) $rankLabel = '👑';
                                elseif ($rank === 2) $rankLabel = '🥈';
                                elseif ($rank === 3) $rankLabel = '🥉';

                                // Avatar
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

                                $winrate = $player->winrate ?? 0;
                                ?>
                                <div class="<?= $rowClass ?>"
                                     onclick="viewDetail(<?= $player->id ?>)">
                                    <!-- Rank -->
                                    <div class="<?= $rankClass ?>"><?= $rankLabel ?></div>

                                    <!-- Avatar -->
                                    <div class="lb-avatar-wrap">
                                        <img src="<?= $avatarUrl ?>" alt="Avatar"
                                             onerror="this.src='/game_pacu/assets/image/ui/profil.gif'">
                                    </div>

                                    <!-- Name + total -->
                                    <div class="lb-info">
                                        <div class="lb-name">
                                            <?= e($player->nama_jalur ?? $player->email) ?>
                                            <?php if ($isMe): ?>
                                                <span class="lb-badge-me">★</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="lb-totalmatch"><?= $player->total_matches ?> match</div>
                                    </div>

                                    <!-- Stats -->
                                    <div class="lb-wins"><?= $player->wins_count ?></div>
                                    <div class="lb-losses"><?= $player->losses_count ?></div>
                                    <div class="lb-winrate"><span><?= $winrate ?>%</span></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/game_pacu/assets/js/game-layout.js?v=<?= time() ?>"></script>
    <script>
        function goBack() {
            window.location.href = '/main-menu';
        }

        function viewDetail(id) {
            window.location.href = '/cari-pemain/detail/' + id;
        }

        // Clock
        (function () {
            function updateClock() {
                const now = new Date();
                const h = String(now.getHours()).padStart(2, '0');
                const m = String(now.getMinutes()).padStart(2, '0');
                const el = document.getElementById('clock');
                if (el) el.textContent = h + ':' + m;
            }
            updateClock();
            setInterval(updateClock, 10000);
        })();

        // Particles (gold colour)
        (function () {
            const canvas = document.getElementById('ps5-particles');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            let width = canvas.width = canvas.offsetWidth;
            let height = canvas.height = canvas.offsetHeight;

            const particles = [];
            const particleCount = 22;
            const colors = ['#fbbf24', '#f59e0b', '#fde68a', '#ffffff'];

            for (let i = 0; i < particleCount; i++) {
                particles.push({
                    x: Math.random() * width,
                    y: Math.random() * height + height,
                    size: Math.random() * 2.5 + 0.8,
                    speed: Math.random() * 0.35 + 0.1,
                    opacity: Math.random() * 0.4 + 0.15,
                    color: colors[Math.floor(Math.random() * colors.length)]
                });
            }

            function animate() {
                ctx.clearRect(0, 0, width, height);
                particles.forEach(p => {
                    ctx.globalAlpha = p.opacity;
                    ctx.fillStyle = p.color;
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
    </script>
</body>

</html>