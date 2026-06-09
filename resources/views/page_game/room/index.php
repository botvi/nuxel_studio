<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
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
            justify-content: center;
            background: url('/game_pacu/assets/image/bg/bgmenu.jpg') no-repeat center center;
            background-size: cover;
            z-index: 10;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 18px;
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.4);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 15;
            transition: all 0.15s ease;
            box-sizing: border-box;
        }

        .back-btn img {
            width: 28px;
            height: 28px;
            image-rendering: pixelated;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.35);
            border: 2.5px solid rgba(255, 255, 255, 0.7);
            transform: scale(1.05);
        }

        .back-btn:active {
            transform: scale(0.95);
        }

        .title-banner {
            font-family: 'Pixelify Sans', monospace;
            font-weight: 700;
            font-size: 26px;
            color: #ffffff;
            text-shadow:
                2px 2px 0px #16a34a,
                -2px -2px 0px #16a34a,
                2px -2px 0px #16a34a,
                -2px 2px 0px #16a34a,
                4px 4px 0px rgba(0, 0, 0, 0.4);
            margin-bottom: 24px;
            text-align: center;
            line-height: 1.4;
            z-index: 11;
            letter-spacing: 1px;
        }

        .menu-panel {
            background: rgba(255, 255, 255, 0.92);
            border: 4px solid #22c55e;
            border-radius: 20px;
            width: 85%;
            padding: 30px 24px;
            box-shadow:
                0 10px 25px rgba(21, 128, 61, 0.25),
                6px 6px 0px #15803d;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 18px;
            z-index: 11;
            backdrop-filter: blur(10px);
        }

        .pixel-btn {
            background-color: #22c55e;
            border: 3px solid #15803d;
            border-radius: 10px;
            box-shadow: 0px 5px 0px #15803d;
            color: white;
            font-family: 'Press Start 2P', monospace;
            font-size: 10px;
            padding: 16px 10px;
            width: 100%;
            text-align: center;
            cursor: pointer;
            text-transform: uppercase;
            box-sizing: border-box;
            display: block;
            text-shadow: 1px 1px 0px #15803d;
            transition: all 0.15s ease;
        }

        .pixel-btn:hover {
            background-color: #4ade80;
            transform: translateY(-2px);
            box-shadow: 0px 7px 0px #15803d;
        }

        .pixel-btn:active {
            transform: translateY(5px);
            box-shadow: 0px 0px 0px #15803d;
        }

        .btn-blue {
            background-color: #3b82f6;
            border-color: #1e40af;
            box-shadow: 0px 5px 0px #1e40af;
            text-shadow: 1px 1px 0px #1e3a8a;
        }

        .btn-blue:hover {
            background-color: #60a5fa;
            border-color: #1e40af;
            box-shadow: 0px 7px 0px #1e40af;
        }

        .btn-blue:active {
            box-shadow: 0px 0px 0px #1e40af;
            transform: translateY(5px);
        }

        /* Loading Overlay Styling */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(6, 78, 59, 0.95) 0%, rgba(2, 44, 34, 0.98) 100%);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 100;
            backdrop-filter: blur(8px);
            box-sizing: border-box;
            padding: 30px 20px;
        }

        /* Scanline Overlay for retro vibe */
        .loading-overlay::before {
            content: " ";
            display: block;
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.3) 50%);
            background-size: 100% 4px;
            z-index: 101;
            pointer-events: none;
            opacity: 0.4;
        }

        /* Radar Search Container */
        .radar-container {
            position: relative;
            width: 140px;
            height: 140px;
            border: 4px solid #14b8a6;
            border-radius: 50%;
            background: #022c22;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow:
                0px 0px 20px rgba(20, 184, 166, 0.3),
                inset 0px 0px 15px rgba(20, 184, 166, 0.2);
            overflow: hidden;
            z-index: 102;
        }

        /* Radar sweep line rotating */
        .radar-sweep {
            position: absolute;
            width: 50%;
            height: 50%;
            background: linear-gradient(45deg, rgba(20, 184, 166, 0.6) 0%, transparent 100%);
            transform-origin: bottom right;
            bottom: 50%;
            right: 50%;
            animation: sweep 2.5s linear infinite;
            border-right: 1px solid rgba(20, 184, 166, 0.8);
            z-index: 2;
        }

        @keyframes sweep {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Radar concentric grid circles */
        .radar-circle-1,
        .radar-circle-2 {
            position: absolute;
            border: 1.5px dashed rgba(20, 184, 166, 0.35);
            border-radius: 50%;
        }

        .radar-circle-1 {
            width: 90px;
            height: 90px;
        }

        .radar-circle-2 {
            width: 45px;
            height: 45px;
        }

        /* Radar crosshairs */
        .radar-cross-h,
        .radar-cross-v {
            position: absolute;
            background: rgba(20, 184, 166, 0.2);
        }

        .radar-cross-h {
            width: 100%;
            height: 1.5px;
        }

        .radar-cross-v {
            width: 1.5px;
            height: 100%;
        }

        /* Pulsing blips (targets found) */
        .radar-blip {
            position: absolute;
            width: 6px;
            height: 6px;
            background: #4ade80;
            border-radius: 50%;
            box-shadow: 0 0 8px #4ade80;
            z-index: 3;
            opacity: 0;
        }

        .blip-1 {
            top: 25%;
            left: 30%;
            animation: blip-flash 3.5s ease-in-out infinite;
        }

        .blip-2 {
            bottom: 30%;
            right: 25%;
            animation: blip-flash 3.5s ease-in-out infinite 1.2s;
        }

        @keyframes blip-flash {
            0% {
                opacity: 0;
                transform: scale(0.6);
            }

            20% {
                opacity: 1;
                transform: scale(1.1);
            }

            40% {
                opacity: 0.2;
            }

            55% {
                opacity: 0.8;
            }

            70%,
            100% {
                opacity: 0;
                transform: scale(0.8);
            }
        }

        /* Magnifier hovering in the center */
        .pixel-magnifier {
            width: 44px;
            height: 44px;
            image-rendering: pixelated;
            z-index: 5;
            animation: floatMagnifier 3s ease-in-out infinite;
        }

        @keyframes floatMagnifier {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-6px) rotate(5deg);
            }
        }

        /* Highly readable Matchmaking text */
        .loading-text {
            font-family: 'Press Start 2P', monospace;
            font-size: 11px;
            color: #4ade80;
            text-align: center;
            line-height: 1.8;
            margin-bottom: 8px;
            letter-spacing: 1px;
            text-shadow: 2px 2px 0px #022c22;
            z-index: 102;
        }

        /* Search timer indicator */
        .search-timer {
            font-family: 'Press Start 2P', monospace;
            font-size: 8px;
            color: #a7f3d0;
            margin-bottom: 24px;
            letter-spacing: 0.5px;
            text-shadow: 2px 2px 0px #022c22;
            background: rgba(2, 44, 34, 0.6);
            padding: 6px 12px;
            border: 2px solid #0d9488;
            border-radius: 4px;
            z-index: 102;
        }

        /* Cancel Button Styling */
        .btn-cancel {
            background-color: #ef4444;
            border: 3px solid #991b1b;
            box-shadow: 0px 4px 0px #7f1d1d;
            font-family: 'Press Start 2P', monospace;
            font-size: 9px;
            padding: 12px 24px;
            width: auto;
            text-shadow: 1px 1px 0px #7f1d1d;
            transition: all 0.1s ease;
            color: #ffffff;
            cursor: pointer;
            z-index: 102;
        }

        .btn-cancel:hover {
            background-color: #f87171;
            transform: translateY(-2px);
            box-shadow: 0px 6px 0px #7f1d1d;
        }

        .btn-cancel:active {
            transform: translateY(2px);
            box-shadow: 0px 2px 0px #7f1d1d;
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
                    <div class="back-btn" onclick="window.location.href='/main-menu'">
                        <img src="/game_pacu/assets/image/ui/back.png" alt="Back">
                    </div>
                    <div class="title-banner">✦ ONLINE ARENA ✦</div>

                    <div class="menu-panel" style="width: 90%;">
                        <!-- JALUR PREVIEW INSIDE PANEL -->
                        <div class="preview-name" id="jalur-preview-name"
                            style="font-family: 'Press Start 2P', monospace; font-size: 10px; color: #15803d; margin-bottom: 8px; text-transform: uppercase; font-weight: bold;">
                            LOADING...</div>
                        <div class="canvas-container" id="jalur-preview-canvas"
                            style="width: 250px; height: 85px; border: 2px solid #86efac; border-radius: 8px; background: #ffffff; margin-bottom: 15px; overflow: hidden; display: flex; justify-content: center; align-items: center;">
                        </div>

                        <div class="pixel-btn btn-blue" onclick="cariLawan()">CARI LAWAN</div>
                        <div class="pixel-btn" onclick="window.location.href='/room/create-or-join'">CUSTOM ROOM</div>
                    </div>

                </div>

                <div class="loading-overlay" id="loading-overlay">
                    <!-- Radar Search Animation -->
                    <div class="radar-container">
                        <div class="radar-sweep"></div>
                        <div class="radar-circle-1"></div>
                        <div class="radar-circle-2"></div>
                        <div class="radar-cross-h"></div>
                        <div class="radar-cross-v"></div>
                        <div class="radar-blip blip-1"></div>
                        <div class="radar-blip blip-2"></div>
                        <img class="pixel-magnifier" src="/game_pacu/assets/image/ui/magnifer.png" alt="Mencari">
                    </div>

                    <div class="loading-text">MENCARI LAWAN<span class="loading-dots">...</span></div>
                    <div class="search-timer">WAKTU MENCARI: <span id="search-timer-val">00:00</span></div>

                    <button class="pixel-btn btn-cancel" onclick="batalCari()">BATAL</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/game_pacu/assets/js/jalur-preview-phaser.js?v=<?= time() ?>"></script>
    <script src="/game_pacu/assets/js/game-layout.js?v=<?= time() ?>"></script>
    <script>
        // Custom HTML Modals (Confirm & Alert) using existing game-layout.css styles
        function showHTMLAlert(message, title = "✦ INFORMASI ✦") {
            return new Promise((resolve) => {
                const overlay = document.createElement('div');
                overlay.id = 'fullscreen-modal-overlay';
                overlay.innerHTML = `
                    <div class="fullscreen-modal-card">
                        <div class="fullscreen-modal-title">${title}</div>
                        <div class="fullscreen-modal-body">${message}</div>
                        <div class="fullscreen-modal-buttons">
                            <button class="fullscreen-btn fullscreen-btn-yes" id="custom-alert-ok-btn" style="width: 120px;">OKE</button>
                        </div>
                    </div>
                `;
                document.body.appendChild(overlay);
                overlay.offsetHeight;
                overlay.classList.add('show');
                const okBtn = document.getElementById('custom-alert-ok-btn');
                okBtn.addEventListener('click', () => {
                    overlay.classList.remove('show');
                    setTimeout(() => {
                        overlay.remove();
                        resolve();
                    }, 300);
                });
            });
        }

        function showHTMLConfirm(message, title = "✦ KONFIRMASI ✦") {
            return new Promise((resolve) => {
                const overlay = document.createElement('div');
                overlay.id = 'fullscreen-modal-overlay';
                overlay.innerHTML = `
                    <div class="fullscreen-modal-card">
                        <div class="fullscreen-modal-title">${title}</div>
                        <div class="fullscreen-modal-body">${message}</div>
                        <div class="fullscreen-modal-buttons">
                            <button class="fullscreen-btn fullscreen-btn-yes" id="custom-confirm-yes-btn" style="width: 100px;">YA</button>
                            <button class="fullscreen-btn fullscreen-btn-no" id="custom-confirm-no-btn" style="width: 100px;">TIDAK</button>
                        </div>
                    </div>
                `;
                document.body.appendChild(overlay);
                overlay.offsetHeight;
                overlay.classList.add('show');
                const yesBtn = document.getElementById('custom-confirm-yes-btn');
                const noBtn = document.getElementById('custom-confirm-no-btn');
                yesBtn.addEventListener('click', () => {
                    overlay.classList.remove('show');
                    setTimeout(() => {
                        overlay.remove();
                        resolve(true);
                    }, 300);
                });
                noBtn.addEventListener('click', () => {
                    overlay.classList.remove('show');
                    setTimeout(() => {
                        overlay.remove();
                        resolve(false);
                    }, 300);
                });
            });
        }

        let searchTimerInterval = null;
        let searchSeconds = 0;

        function updateSearchTimer() {
            searchSeconds++;
            const minutes = String(Math.floor(searchSeconds / 60)).padStart(2, '0');
            const seconds = String(searchSeconds % 60).padStart(2, '0');
            const timerEl = document.getElementById('search-timer-val');
            if (timerEl) {
                timerEl.textContent = `${minutes}:${seconds}`;
            }
        }

        function cariLawan() {
            document.getElementById('loading-overlay').style.display = 'flex';

            // Reset and start timer
            searchSeconds = 0;
            const timerVal = document.getElementById('search-timer-val');
            if (timerVal) timerVal.textContent = '00:00';

            if (searchTimerInterval) clearInterval(searchTimerInterval);
            searchTimerInterval = setInterval(updateSearchTimer, 1000);

            fetch('/room/matchmake', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        showHTMLAlert(data.message || 'Gagal mencari lawan.').then(() => {
                            batalCari();
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    showHTMLAlert('Terjadi kesalahan koneksi.').then(() => {
                        batalCari();
                    });
                });
        }

        function batalCari() {
            document.getElementById('loading-overlay').style.display = 'none';
            if (searchTimerInterval) {
                clearInterval(searchTimerInterval);
                searchTimerInterval = null;
            }
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