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
            background:
                radial-gradient(ellipse at 50% 35%, rgba(34, 197, 94, 0.25) 0%, transparent 60%),
                radial-gradient(ellipse at 20% 80%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 75%, rgba(168, 85, 247, 0.12) 0%, transparent 50%),
                linear-gradient(180deg, #060d18 0%, #0a0f1a 100%);
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
            text-shadow: 0 0 8px rgba(56, 189, 248, 0.4);
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
            filter:
                drop-shadow(0 0 20px rgba(34, 197, 94, 0.4))
                drop-shadow(0 4px 12px rgba(0, 0, 0, 0.6));
            animation: logo-entrance 1s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
        }

        @keyframes logo-entrance {
            0% { transform: scale(0.3) rotate(-8deg); opacity: 0; }
            70% { transform: scale(1.06) rotate(2deg); }
            100% { transform: scale(1) rotate(0deg); opacity: 1; }
        }

        /* Glow ring behind logo */
        .logo-glow-ring {
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(34, 197, 94, 0.12) 0%, transparent 65%);
            animation: glow-pulse 3s ease-in-out infinite alternate;
            pointer-events: none;
            z-index: -1;
        }

        @keyframes glow-pulse {
            0% { transform: scale(0.8); opacity: 0.4; }
            100% { transform: scale(1.3); opacity: 0.8; }
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
            text-shadow: 0 0 8px rgba(34, 197, 94, 0.5);
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
            box-shadow: 0 0 12px rgba(34, 197, 94, 0.1);
        }

        .progress-fill {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, #16a34a 0%, #22c55e 50%, #4ade80 100%);
            border-radius: 99px;
            transition: width 0.1s linear;
            box-shadow: 0 0 10px rgba(34, 197, 94, 0.6);
            position: relative;
        }

        /* Shimmer on the progress fill */
        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0; left: -60%;
            width: 40%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
            animation: progress-shimmer 1.5s infinite linear;
        }

        @keyframes progress-shimmer {
            0% { left: -60%; }
            100% { left: 120%; }
        }

        .status-text {
            font-size: 7px;
            color: rgba(56, 189, 248, 0.7);
            letter-spacing: 1.5px;
            text-shadow: 0 0 6px rgba(56, 189, 248, 0.3);
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
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            pointer-events: none;
            z-index: 3;
        }

        .star {
            position: absolute;
            width: 3px;
            height: 3px;
            border-radius: 50%;
            animation: blink-star 2.5s infinite ease-in-out;
        }

        @keyframes blink-star {
            0%, 100% { opacity: 0; transform: scale(0); }
            50% { opacity: 1; transform: scale(1); }
        }

        .s1 { top: 12%; left: 18%; background: #22c55e; box-shadow: 0 0 6px #22c55e; animation-delay: 0.3s; width: 4px; height: 4px; }
        .s2 { top: 28%; right: 14%; background: #facc15; box-shadow: 0 0 6px #facc15; animation-delay: 0.9s; }
        .s3 { bottom: 40%; left: 22%; background: #38bdf8; box-shadow: 0 0 6px #38bdf8; animation-delay: 1.5s; }
        .s4 { bottom: 22%; right: 18%; background: #ec4899; box-shadow: 0 0 6px #ec4899; animation-delay: 0.6s; width: 4px; height: 4px; }
        .s5 { top: 55%; left: 78%; background: #a855f7; box-shadow: 0 0 6px #a855f7; animation-delay: 1.2s; }
        .s6 { top: 70%; left: 10%; background: #f97316; box-shadow: 0 0 6px #f97316; animation-delay: 0.2s; }
        .s7 { top: 18%; right: 30%; background: #4ade80; box-shadow: 0 0 6px #4ade80; animation-delay: 1.8s; width: 2px; height: 2px; }
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
            <canvas id="splash-particles"></canvas>

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

    // ---- Floating Particles ----
    (function () {
        const canvas = document.getElementById('splash-particles');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let width = canvas.width = canvas.offsetWidth;
        let height = canvas.height = canvas.offsetHeight;

        const COLORS = ['#22c55e', '#38bdf8', '#a855f7', '#f59e0b'];
        const particles = [];
        for (let i = 0; i < 30; i++) {
            particles.push({
                x: Math.random() * width,
                y: Math.random() * height + height,
                size: Math.random() * 2.5 + 0.5,
                speed: Math.random() * 0.5 + 0.1,
                opacity: Math.random() * 0.35 + 0.1,
                color: COLORS[Math.floor(Math.random() * COLORS.length)]
            });
        }

        function animateParticles() {
            ctx.clearRect(0, 0, width, height);
            particles.forEach(p => {
                ctx.globalAlpha = p.opacity;
                ctx.fillStyle = p.color;
                ctx.fillRect(p.x, p.y, p.size, p.size);
                p.y -= p.speed;
                if (p.y < -10) { p.y = height + 10; p.x = Math.random() * width; }
            });
            requestAnimationFrame(animateParticles);
        }

        window.addEventListener('resize', () => {
            if (canvas.offsetWidth) { width = canvas.width = canvas.offsetWidth; height = canvas.height = canvas.offsetHeight; }
        });
        animateParticles();
    })();

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
