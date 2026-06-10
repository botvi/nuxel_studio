<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Room Matchmaking — Papan Jawara</title>
    <link rel="stylesheet" href="../assets/css/game-layout.css">
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
            background: url('../assets/image/bg/bgmenu.jpg') no-repeat center center;
            background-size: cover;
            z-index: 10;
        }

        .back-btn {
            position: absolute;
            top: 10px;
            left: 14px;
            width: 48px;
            height: 48px;
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
            width: 32px;
            height: 32px;
            image-rendering: pixelated;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.35);
            border-color: rgba(255, 255, 255, 0.7);
            transform: scale(1.05);
        }

        .back-btn:active {
            transform: scale(0.9);
        }

        .title-banner {
            font-family: 'Pixelify Sans', monospace;
            font-weight: 700;
            font-size: 32px;
            color: #ffffff;
            text-shadow:
                2px 2px 0px #16a34a,
                -2px -2px 0px #16a34a,
                2px -2px 0px #16a34a,
                -2px 2px 0px #16a34a,
                4px 4px 0px rgba(0, 0, 0, 0.4);
            margin-bottom: 30px;
            text-align: center;
            line-height: 1.4;
            z-index: 11;
            letter-spacing: 1px;
        }

        .menu-panel {
            background-color: #ffffff;
            border: 3px solid #86efac;
            border-radius: 16px;
            width: 75%;
            padding: 25px 20px;
            box-shadow: 4px 4px 0px rgba(21, 128, 61, 0.3);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            z-index: 11;
        }

        .pixel-btn {
            background-color: #22c55e;
            border: 2px solid #16a34a;
            border-radius: 8px;
            box-shadow: 0px 4px 0px #16a34a;
            color: white;
            font-family: 'Press Start 2P', monospace;
            font-size: 11px;
            padding: 15px 10px;
            width: 100%;
            text-align: center;
            cursor: pointer;
            text-transform: uppercase;
            box-sizing: border-box;
            display: block;
            text-shadow: 1px 1px 0px #15803d;
            transition: all 0.1s;
        }

        .pixel-btn:hover {
            background-color: #4ade80;
        }

        .pixel-btn:active {
            transform: translateY(4px);
            box-shadow: 0px 0px 0px #16a34a;
        }

        .btn-blue {
            background-color: #3b82f6;
            border-color: #1e3a8a;
            box-shadow: 0px 4px 0px #1e3a8a;
            text-shadow: 1px 1px 0px #1e40af;
        }

        .btn-blue:hover {
            background-color: #60a5fa;
        }

        .btn-blue:active {
            box-shadow: 0px 0px 0px #1e3a8a;
            transform: translateY(4px);
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
                    <div class="back-btn" onclick="window.location.href='../mainmenu.php'">
                        <img src="../assets/image/ui/back.png" alt="Back">
                    </div>

                    <div class="title-banner">✦ PILIH<br>MODE BERMAIN ✦</div>

                    <div class="menu-panel">
                        <div class="pixel-btn btn-blue" onclick="cariLawan()">CARI LAWAN</div>
                        <div class="pixel-btn" onclick="window.location.href='createorjoin.php'">CUSTOM ROOM</div>
                    </div>

                    <div id="phaser-preview-container"
                        style="position: absolute; bottom: 10px; left: 0; width: 100%; height: 160px; z-index: 12; pointer-events: none;">
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

    <script src="../assets/js/game-layout.js?v=<?= time() ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/phaser@3.88.2/dist/phaser.min.js"></script>
    <script src="../assets/js/jalur-preview-phaser.js"></script>
    <script>
        class RoomPreviewScene extends Phaser.Scene {
            constructor() { super({ key: 'RoomPreviewScene' }); }
            preload() { preloadJalurAssets(this); }
            create() {
                const W = this.scale.width;
                const H = this.scale.height;
                const cx = W / 2;

                // Tambahkan background kotak
                const boxBg = this.add.graphics();
                const cW = W - 44;
                boxBg.fillStyle(0x15803d, 0.25);
                boxBg.fillRoundedRect(cx - cW / 2 + 5, H - 120 + 5, cW, 110, 16);
                boxBg.fillStyle(0xffffff, 0.9);
                boxBg.lineStyle(4, 0x22c55e, 1);
                boxBg.fillRoundedRect(cx - cW / 2, H - 120, cW, 110, 16);
                boxBg.strokeRoundedRect(cx - cW / 2, H - 120, cW, 110, 16);

                // const boxTitle = this.add.text(cx, H - 100, '✦ JALUR SAYA ✦', {
                //     fontFamily: '"Press Start 2P", monospace',
                //     fontSize: '8px',
                //     color: '#15803d',
                //     stroke: '#ffffff',
                //     strokeThickness: 2
                // }).setOrigin(0.5);

                createJalurPreview(this, cx, H - 65, 0.85);
            }
        }

        const phaserConfig = {
            type: Phaser.AUTO,
            width: 360,
            height: 160,
            transparent: true,
            parent: 'phaser-preview-container',
            pixelArt: true,
            scene: [RoomPreviewScene],
            scale: { mode: Phaser.Scale.FIT, autoCenter: Phaser.Scale.CENTER_HORIZONTALLY }
        };
        new Phaser.Game(phaserConfig);

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
    </script>
</body>

</html>