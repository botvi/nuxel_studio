<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Nuxel Games — Loading</title>
    <link rel="stylesheet" href="{{ asset('game_pacu/assets/css/game-layout.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Pixelify+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        #page-transition-overlay {
            display: none !important;
        }

        body {
            margin: 0;
            padding: 0;
            background: #0a0f1a;
            overflow: hidden;
            font-family: 'Press Start 2P', monospace;
        }

        /* ---- Main Splash Container ---- */
        .splash-container {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 50px 24px 32px;
            overflow: hidden;
            box-sizing: border-box;
        }

        /* ---- PS5-style deep background glow ---- */
        .splash-glow {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: #0a0f1a;
            z-index: 1;
            pointer-events: none;
        }

        /* ---- Floating Particles Canvas ---- */
        #splash-particles {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 2;
            pointer-events: none;
        }

        /* ---- Content layers ---- */
        .splash-top {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .studio-intro {
            font-size: 7px;
            color: rgba(56, 189, 248, 0.7);
            letter-spacing: 3px;
            animation: fade-pulse 2s ease-in-out infinite alternate;
        }

        @keyframes fade-pulse {
            0% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        /* ---- Logo area ---- */
        .splash-middle {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            justify-content: center;
            gap: 0;
        }

        .logo-wrapper {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            animation: logo-float 3.5s ease-in-out infinite;
        }

        @keyframes logo-float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .pixel-logo {
            max-width: 260px;
            height: auto;
            image-rendering: pixelated;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.6));
            animation: logo-entrance 1s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
        }

        @keyframes logo-entrance {
            0% { transform: scale(0.3) rotate(-8deg); opacity: 0; }
            70% { transform: scale(1.06) rotate(2deg); }
            100% { transform: scale(1) rotate(0deg); opacity: 1; }
        }

        /* Glow ring behind logo */
        .logo-glow-ring {
            display: none;
        }

        /* ---- Loading section ---- */
        .splash-bottom {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0;
        }

        .loading-label {
            font-size: 7px;
            color: rgba(255,255,255,0.7);
            letter-spacing: 2px;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        .percent-val {
            color: #22c55e;
        }

        /* ---- Glassmorphism Progress Bar ---- */
        .progress-track {
            width: 100%;
            height: 8px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 99px;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .progress-fill {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, #16a34a 0%, #22c55e 50%, #4ade80 100%);
            border-radius: 99px;
            transition: width 0.1s linear;
            position: relative;
        }

        .status-text {
            font-size: 7px;
            color: rgba(56, 189, 248, 0.7);
            letter-spacing: 1.5px;
            animation: blink-status 1s step-end infinite;
            margin-bottom: 20px;
        }

        @keyframes blink-status {
            50% { opacity: 0.4; }
        }

        /* ---- Footer ---- */
        .splash-footer {
            font-size: 6px;
            color: rgba(255,255,255,0.18);
            letter-spacing: 1px;
            text-align: center;
            font-family: 'Pixelify Sans', monospace;
        }

        /* ---- Decorative floating stars ---- */
        .pixel-stars {
            display: none;
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

        <div class="splash-container">
            <!-- Background Glow Layer -->
            <div class="splash-glow"></div>

            <!-- Particles Canvas -->
            <canvas id="splash-particles" style="display: none;"></canvas>

            <!-- Decorative pixel stars -->
            <div class="pixel-stars">
                <div class="star s1"></div>
                <div class="star s2"></div>
                <div class="star s3"></div>
                <div class="star s4"></div>
                <div class="star s5"></div>
                <div class="star s6"></div>
                <div class="star s7"></div>
            </div>

            <!-- Top: Studio name -->
            <div class="splash-top">
                <div class="studio-intro">NUXEL STUDIO PRESENTS</div>
            </div>

            <!-- Middle: Logo -->
            <div class="splash-middle">
                <div class="logo-wrapper">
                    <div class="logo-glow-ring"></div>
                    <img src="{{ asset('env/logo_text1.png') }}" alt="Pacu Jalur Logo" class="pixel-logo">
                </div>
            </div>

            <!-- Bottom: Loading bar -->
            <div class="splash-bottom">
                <div class="loading-label">MEMUAT GAME... <span class="percent-val" id="percent-val">0%</span></div>
                <div class="progress-track">
                    <div class="progress-fill" id="progress-fill"></div>
                </div>
                <div class="status-text" id="status-text">INITIALIZING SYSTEM...</div>
                <div class="splash-footer">© 2026 Nuxel Studio. All Rights Reserved.</div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('game_pacu/assets/js/game-layout.js') }}?v=<?= time() ?>"></script>
<script>
    // ---- Clock ----
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

    // ---- Floating Particles disabled for performance ----

    // ---- Progress Bar Animation ----
    document.addEventListener('DOMContentLoaded', () => {
        const fill = document.getElementById('progress-fill');
        const percentEl = document.getElementById('percent-val');
        const statusEl = document.getElementById('status-text');

        const statusMessages = [
            "CONNECTING TO SERVER...",
            "LOADING PIXEL ASSETS...",
            "PREPARING ARENA PACU...",
            "GETTING JALUR READY...",
            "STARTING ENGINE...",
            "READY!"
        ];

        let start = null;
        const duration = 4600;

        function animate(timestamp) {
            if (!start) start = timestamp;
            const elapsed = timestamp - start;
            const progress = Math.min(elapsed / duration, 1);

            const percentage = Math.floor(progress * 100);
            percentEl.textContent = percentage + '%';
            fill.style.width = percentage + '%';

            const msgIndex = Math.min(Math.floor(progress * (statusMessages.length - 1)), statusMessages.length - 1);
            statusEl.textContent = statusMessages[msgIndex];

            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        }

        requestAnimationFrame(animate);

        // Redirect after 5 seconds
        setTimeout(() => {
            if (typeof window.navigateToPage === 'function') {
                window.navigateToPage("{{ route('login') }}");
            } else {
                window.location.href = "{{ route('login') }}";
            }
        }, 5000);
    });
</script>
</body>
</html>
