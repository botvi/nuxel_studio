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
            background: rgba(255, 255, 255, 0.9);
            border: 3px solid #22c55e;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 15;
            transition: all 0.15s ease;
            box-shadow: 3px 3px 0px #15803d;
            box-sizing: border-box;
        }

        .back-btn img {
            width: 28px;
            height: 28px;
            image-rendering: pixelated;
        }

        .back-btn:hover {
            background: #ffffff;
            transform: translate(-2px, -2px);
            box-shadow: 5px 5px 0px #15803d;
        }

        .back-btn:active {
            transform: translate(2px, 2px);
            box-shadow: 1px 1px 0px #15803d;
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

        /* Loading Overlay with nicer Magnifying Glass */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(6, 78, 59, 0.9);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 30;
            backdrop-filter: blur(8px);
        }

        .search-container {
            position: relative;
            width: 120px;
            height: 120px;
            margin-bottom: 40px;
        }


        .pixel-magnifier {
            position: absolute;
            top: 22px;
            left: 22px;
            width: 76px;
            height: 76px;
            image-rendering: pixelated;
            image-rendering: crisp-edges;
            animation: searchHover 3s cubic-bezier(0.4, 0, 0.2, 1) infinite;
            z-index: 2;
        }

        @keyframes searchHover {
            0% {
                transform: translate(0px, 0px) scale(1) rotate(0deg);
            }

            33% {
                transform: translate(15px, -10px) scale(1.05) rotate(10deg);
            }

            66% {
                transform: translate(-10px, 12px) scale(0.95) rotate(-5deg);
            }

            100% {
                transform: translate(0px, 0px) scale(1) rotate(0deg);
            }
        }

        .loading-text {
            font-family: 'Press Start 2P', monospace;
            font-size: 13px;
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
            box-shadow: 0px 5px 0px #991b1b;
            width: auto;
            font-size: 9px;
            padding: 12px 25px;
            margin-top: 30px;
            text-shadow: 1px 1px 0px #7f1d1d;
        }

        .btn-cancel:hover {
            background-color: #f87171;
            box-shadow: 0px 7px 0px #991b1b;
        }

        .btn-cancel:active {
            transform: translateY(5px);
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
                    <div class="search-container">
                        <img class="pixel-magnifier" src="/game_pacu/assets/image/ui/magnifer.png" alt="Mencari">
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

        function cariLawan() {
            document.getElementById('loading-overlay').style.display = 'flex';
            
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
                        document.getElementById('loading-overlay').style.display = 'none';
                    });
                }
            })
            .catch(err => {
                console.error(err);
                showHTMLAlert('Terjadi kesalahan koneksi.').then(() => {
                    document.getElementById('loading-overlay').style.display = 'none';
                });
            });
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