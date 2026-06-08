<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Room Matchmaking — Papan Jawara</title>
    <link rel="stylesheet" href="/game_pacu/assets/css/game-layout.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Pixelify+Sans:wght@400;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f0fdf4;
            color: #15803d;
            font-family: 'Pixelify Sans', monospace;
        }

        #game-ui {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: url('/game_pacu/assets/image/bg/bgmenu.jpg') no-repeat center center;
            background-size: cover;
            z-index: 10;
            padding-bottom: 20px;
            box-sizing: border-box;
        }

        .profile-btn {
            position: absolute;
            top: 16px;
            left: 14px;
            width: 36px;
            height: 36px;
            background: none;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 15;
            transition: all 0.15s ease;
            box-sizing: border-box;
        }

        .profile-btn img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            image-rendering: pixelated;
        }

        .profile-btn:hover {
            transform: scale(1.05);
        }

        .profile-btn:active {
            transform: scale(0.9);
        }

        .sound-btn {
            position: absolute;
            top: 16px;
            left: 50%;
            transform: translateX(-50%);
            width: 36px;
            height: 36px;
            background: none;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 15;
            transition: all 0.15s ease;
            box-sizing: border-box;
        }

        .sound-btn img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            image-rendering: pixelated;
        }

        .sound-btn:hover {
            transform: translateX(-50%) scale(1.05);
        }

        .sound-btn:active {
            transform: translateX(-50%) scale(0.9);
        }

        .coin-display {
            position: absolute;
            top: 16px;
            right: 14px;
            height: 36px;
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

        #header-coin-count {
            font-family: 'Pixelify Sans', monospace;
            font-size: 13px;
            font-weight: bold;
            color: #FFD700;
            text-shadow:
                1px 1px 0px #15803d,
                -1px -1px 0px #15803d,
                1px -1px 0px #15803d,
                -1px 1px 0px #15803d,
                0px 1px 0px #15803d,
                0px -1px 0px #15803d,
                1px 0px 0px #15803d,
                -1px 0px 0px #15803d;
            line-height: 1;
        }

        @keyframes htmlShimmer {
            0% {
                transform: translateX(-150%) skewX(-25deg);
            }

            100% {
                transform: translateX(150%) skewX(-25deg);
            }
        }

        @keyframes textShimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        .title-banner {
            font-family: 'Press Start 2P', monospace;
            font-size: 16px;
            background: linear-gradient(180deg, #ffea00 0%, #ff5500 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(2px 2px 0px #000000) drop-shadow(-2px -2px 0px #000000) drop-shadow(2px -2px 0px #000000) drop-shadow(-2px 2px 0px #000000);
            margin-top: 90px;
            margin-bottom: 20px;
            text-align: center;
            line-height: 1.4;
            letter-spacing: 1px;
            z-index: 11;
        }

        .panel {
            /* Solid Retro dark background */
            border: 4px solid #000000;
            border-radius: 12px;
            width: 90%;
            padding: 18px 14px;
            margin-bottom: 15px;
            box-shadow: 6px 6px 0px #000000;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            z-index: 11;
        }

        .panel-title {
            font-family: 'Press Start 2P', monospace;
            font-size: 10px;
            color: #00ffff;
            /* Cyberpunk / Retro Cyan */
            text-shadow: 1.5px 1.5px 0px #000000;
            margin-bottom: 18px;
            border-bottom: 3px dashed #000000;
            padding-bottom: 12px;
            text-align: center;
            line-height: 1.5;
            letter-spacing: 1px;
        }

        /* Menu Card Design */
        .menu-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 14px;
            border-radius: 8px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            margin-bottom: 14px;
            box-sizing: border-box;
            border: 3px solid #000000;
            user-select: none;
            -webkit-user-select: none;
            transition: transform 0.05s, box-shadow 0.05s;
        }

        .menu-card:last-child {
            margin-bottom: 0;
        }

        /* Card color variants with solid borders and 3D arcade shadows */
        .card-green {
            background: linear-gradient(180deg, #4ade80 0%, #16a34a 100%);
            box-shadow: inset 0 2.5px 0px rgba(255, 255, 255, 0.5), 0px 6px 0px #000000;
        }

        .card-green:hover {
            background: linear-gradient(180deg, #57eb8d 0%, #18b353 100%);
            box-shadow: inset 0 2.5px 0px rgba(255, 255, 255, 0.5), 0px 8px 0px #000000;
        }

        .card-orange {
            background: linear-gradient(180deg, #fbbf24 0%, #ea580c 100%);
            box-shadow: inset 0 2.5px 0px rgba(255, 255, 255, 0.5), 0px 6px 0px #000000;
        }

        .card-orange:hover {
            background: linear-gradient(180deg, #fccd36 0%, #f66315 100%);
            box-shadow: inset 0 2.5px 0px rgba(255, 255, 255, 0.5), 0px 8px 0px #000000;
        }

        .card-blue {
            background: linear-gradient(180deg, #60a5fa 0%, #2563eb 100%);
            box-shadow: inset 0 2.5px 0px rgba(255, 255, 255, 0.5), 0px 6px 0px #000000;
        }

        .card-blue:hover {
            background: linear-gradient(180deg, #74b3ff 0%, #2e6eff 100%);
            box-shadow: inset 0 2.5px 0px rgba(255, 255, 255, 0.5), 0px 8px 0px #000000;
        }

        /* Hover states translateY override */
        .menu-card:hover {
            transform: translateY(-2px);
        }

        /* Active click states */
        .menu-card:active {
            transform: translateY(6px) !important;
            box-shadow: inset 0 2.5px 0px rgba(255, 255, 255, 0.2), 0px 0px 0px #000000 !important;
        }

        /* Inner element styles */
        .card-icon {
            width: 42px;
            height: 42px;
            background: #27272a;
            /* Dark gray */
            border: 3px solid #000000;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-sizing: border-box;
            box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.8);
        }

        .card-icon img {
            width: 80%;
            height: 80%;
            object-fit: contain;
            image-rendering: pixelated;
        }

        .card-info {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
            text-align: left;
        }

        .card-label {
            font-family: 'Press Start 2P', monospace;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 0.5px;
            background: linear-gradient(180deg, #ffea00 0%, #ff0000 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(1px 1px 0px #000000) drop-shadow(-1px 1px 0px #000000) drop-shadow(1px -1px 0px #000000) drop-shadow(-1px -1px 0px #000000);
            display: inline-block;
        }

        .card-desc {
            font-family: 'Pixelify Sans', monospace;
            font-size: 11px;
            color: #ffffff;
            text-shadow: 1.5px 1.5px 0px #000000;
            line-height: 1.3;
        }

        .card-arrow {
            font-family: 'Press Start 2P', monospace;
            font-size: 10px;
            background: linear-gradient(180deg, #ffea00 0%, #ff0000 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(1px 1px 0px #000000) drop-shadow(-1px 1px 0px #000000) drop-shadow(1px -1px 0px #000000) drop-shadow(-1px -1px 0px #000000);
            flex-shrink: 0;
            transition: transform 0.1s ease;
        }

        .menu-card:hover .card-arrow {
            transform: translateX(4px);
        }

        .pixel-btn {
            background-color: #22c55e;
            border: 3px solid #000000;
            border-radius: 8px;
            box-shadow: inset 0 2px 0px rgba(255, 255, 255, 0.4), 0px 4px 0px #000000;
            color: white;
            font-family: 'Press Start 2P', monospace;
            font-size: 9px;
            padding: 14px;
            width: 100%;
            text-align: center;
            cursor: pointer;
            text-transform: uppercase;
            box-sizing: border-box;
            display: block;
            margin-top: 10px;
            text-shadow: 1.5px 1.5px 0px #000000;
            transition: all 0.1s;
        }

        .pixel-btn:hover {
            background-color: #4ade80;
        }

        .pixel-btn:active {
            transform: translateY(4px);
            box-shadow: inset 0 2px 0px rgba(255, 255, 255, 0.1), 0px 0px 0px #000000;
        }

        .btn-orange {
            background: linear-gradient(180deg, #fbbf24 0%, #ea580c 100%) !important;
        }

        .btn-orange:hover {
            background: linear-gradient(180deg, #fccd36 0%, #f66315 100%) !important;
        }

        .btn-blue {
            background: linear-gradient(180deg, #60a5fa 0%, #2563eb 100%) !important;
        }

        .btn-blue:hover {
            background: linear-gradient(180deg, #74b3ff 0%, #2e6eff 100%) !important;
        }

        /* Loading Overlay with nicer Magnifying Glass */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.85);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 30;
            backdrop-filter: blur(4px);
        }

        .search-container {
            position: relative;
            width: 120px;
            height: 120px;
            margin-bottom: 40px;
        }

        /* Radar sweep background */
        .radar {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid rgba(34, 197, 94, 0.4);
            box-shadow: 0 0 15px rgba(34, 197, 94, 0.3), inset 0 0 15px rgba(34, 197, 94, 0.2);
            overflow: hidden;
            background: rgba(34, 197, 94, 0.1);
        }

        .radar::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 50px;
            height: 50px;
            background: linear-gradient(45deg, rgba(34, 197, 94, 0.9) 0%, transparent 60%);
            transform-origin: 0 0;
            animation: radarSweep 1.5s linear infinite;
        }

        @keyframes radarSweep {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Better Magnifying glass */
        .magnifying-glass {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            animation: searchHover 3s cubic-bezier(0.4, 0, 0.2, 1) infinite;
            z-index: 2;
        }

        .lens {
            width: 44px;
            height: 44px;
            border: 6px solid #ffffff;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            position: absolute;
            top: 20px;
            left: 20px;
            box-shadow: inset 4px 4px 0 rgba(255, 255, 255, 0.6), 0 6px 12px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(2px);
        }

        .handle {
            width: 12px;
            height: 35px;
            background: #d97706;
            /* Gold/brown handle */
            position: absolute;
            top: 60px;
            left: 60px;
            transform: rotate(-45deg);
            transform-origin: top left;
            border-radius: 6px;
            border: 3px solid #78350f;
            box-shadow: inset -2px -2px 0 rgba(0, 0, 0, 0.3), 2px 2px 5px rgba(0, 0, 0, 0.4);
        }

        @keyframes searchHover {
            0% {
                transform: translate(0px, 0px) scale(1) rotate(0deg);
            }

            33% {
                transform: translate(25px, -15px) scale(1.1) rotate(15deg);
            }

            66% {
                transform: translate(-15px, 20px) scale(0.95) rotate(-10deg);
            }

            100% {
                transform: translate(0px, 0px) scale(1) rotate(0deg);
            }
        }

        .loading-text {
            font-family: 'Press Start 2P', monospace;
            font-size: 14px;
            color: #22c55e;
            -webkit-text-stroke: 1px #ffffff;
            text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.6);
            animation: pulseText 1.5s infinite;
            text-align: center;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        @keyframes pulseText {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }
        }

        .btn-cancel {
            background-color: #ef4444;
            border-color: #991b1b;
            box-shadow: 0px 4px 0px #991b1b;
            width: auto;
            font-size: 10px;
            padding: 12px 25px;
            margin-top: 30px;
            text-shadow: 1px 1px 0px #7f1d1d;
        }

        .btn-cancel:hover {
            background-color: #f87171;
        }

        .btn-cancel:active {
            transform: translateY(4px);
            box-shadow: 0px 0px 0px #991b1b;
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
                <div id="game-ui">
                    <div class="profile-btn" onclick="window.location.href='/profil'">
                        <?php
                        $dbFoto = auth()->user()->foto_profile;
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
                        ?>
                        <img src="<?= $profileImgSrc ?>" alt="Profile">
                    </div>

                    <!-- Sound Toggle (Top Middle) -->
                    <div id="sound-btn" class="sound-btn" onclick="toggleSound()">
                        <img id="sound-icon" src="/game_pacu/assets/image/ui/sound_on.png" alt="Sound">
                    </div>

                    <!-- Coin Display (Top Right) -->
                    <div class="coin-display">
                        <div class="coin-icon-wrapper">
                            <img src="/game_pacu/assets/image/ui/koin.png" alt="Coin">
                        </div>
                        <span
                            id="header-coin-count"><?= number_format(auth()->user()->kuansing_poin, 0, ',', '.') ?></span>
                    </div>

                    <div class="title-banner" style="margin-top: 90px; margin-bottom: 15px;"></div>

                    <!-- JALUR PREVIEW BOX -->
                    <div id="jalur-preview-container" class="jalur-preview-box" style="margin-bottom: 15px;">
                        <div class="canvas-container" id="jalur-preview-canvas" style="width: 250px; height: 85px; border: none; background: transparent;"></div>
                        <div class="preview-name" id="jalur-preview-name">LOADING...</div>
                    </div>

                    <!-- Panel Menu Utama -->
                    <div class="" style="width: 90%;">

                        <div class="menu-card card-green" onclick="window.location.href='/room'">
                            <div class="card-icon">
                                <img src="/game_pacu/assets/image/ui/kayuah.png" alt="Main">
                            </div>
                            <div class="card-info">
                                <div class="card-label">MAIN PACU</div>
                                <div class="card-desc">Cari lawan & mulai balapan jalur</div>
                            </div>
                            <div class="card-arrow">▶</div>
                        </div>

                        <div class="menu-card card-orange" onclick="window.location.href='/tukang-jaluar'">
                            <div class="card-icon">
                                <img src="/game_pacu/assets/image/ui/tukang.png" alt="Tukang">
                            </div>
                            <div class="card-info">
                                <div class="card-label">TUKANG JALUAR</div>
                                <div class="card-desc">Kustomisasi perahu & pendayung</div>
                            </div>
                            <div class="card-arrow">▶</div>
                        </div>

                        <div class="menu-card card-blue" onclick="window.location.href='/topup'">
                            <div class="card-icon">
                                <img src="/game_pacu/assets/image/ui/koin.png" alt="Koin">
                            </div>
                            <div class="card-info">
                                <div class="card-label">TOP UP KP</div>
                                <div class="card-desc">Isi ulang Kuansing Poin milikmu</div>
                            </div>
                            <div class="card-arrow">▶</div>
                        </div>
                    </div>

                </div>



                <div class="loading-overlay" id="loading-overlay">
                    <div class="search-container">
                        <div class="radar"></div>
                        <div class="magnifying-glass">
                            <div class="lens"></div>
                            <div class="handle"></div>
                        </div>
                    </div>
                    <div class="loading-text">Mencari<br>Lawan...</div>
                    <button class="pixel-btn btn-cancel" onclick="batalCari()">BATAL</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/game_pacu/assets/js/jalur-preview-phaser.js?v=<?= time() ?>"></script>
    <script src="/game_pacu/assets/js/game-layout.js?v=<?= time() ?>"></script>
    <script>
        // Load and Sync customizations / coin balance from server JSON
        (function () {
            // First display local cache immediately for instant load
            const initialCoins = <?= auth()->user()->kuansing_poin ?>;
            const coinEl = document.getElementById('header-coin-count');
            if (coinEl) {
                coinEl.innerText = initialCoins.toLocaleString('id-ID');
            }

            // Fetch latest settings from server to sync
            fetch('/tukang-jaluar/get')
                .then(res => res.json())
                .then(data => {
                    if (data.customColors) {
                        for (const key in data.customColors) {
                            localStorage.setItem('custom_' + key, data.customColors[key]);
                        }
                    }
                    if (data.corak_data_url) {
                        localStorage.setItem('corak_data_url', data.corak_data_url);
                    } else {
                        localStorage.removeItem('corak_data_url');
                    }
                    if (data.lambai_data_url) {
                        localStorage.setItem('lambai_data_url', data.lambai_data_url);
                    } else {
                        localStorage.removeItem('lambai_data_url');
                    }
                    if (data.coins !== undefined) {
                        localStorage.setItem('coins', String(data.coins));
                        if (coinEl) {
                            coinEl.innerText = data.coins.toLocaleString('id-ID');
                        }
                    }
                })
                .catch(err => console.error('Failed to sync customizations from server:', err));
        })();

        // Load Sound Setting from LocalStorage
        (function () {
            const isMuted = localStorage.getItem('sound_muted') === 'true';
            const soundIcon = document.getElementById('sound-icon');
            if (soundIcon) {
                soundIcon.src = isMuted
                    ? '/game_pacu/assets/image/ui/sound_off.png'
                    : '/game_pacu/assets/image/ui/sound_on.png';
            }
        })();

        function toggleSound() {
            const isMuted = localStorage.getItem('sound_muted') === 'true';
            const nextMuted = !isMuted;
            localStorage.setItem('sound_muted', nextMuted ? 'true' : 'false');

            const soundIcon = document.getElementById('sound-icon');
            if (soundIcon) {
                soundIcon.src = nextMuted
                    ? '/game_pacu/assets/image/ui/sound_off.png'
                    : '/game_pacu/assets/image/ui/sound_on.png';
            }
        }

        function cariLawan() {
            document.getElementById('loading-overlay').style.display = 'flex';
            // Simulasi dummy searching
            window.searchTimeout = setTimeout(() => {
                // Di sini biasanya redirect ke arena/game
                // Untuk sekarang biarkan dummy
            }, 60000);
        }

        function batalCari() {
            document.getElementById('loading-overlay').style.display = 'none';
            if (window.searchTimeout) {
                clearTimeout(window.searchTimeout);
            }
        }

        // Initialize Jalur Preview
        document.addEventListener('DOMContentLoaded', function () {
            window.initJalurPreview('jalur-preview-canvas', 'jalur-preview-name');
        });
    </script>
</body>

</html>