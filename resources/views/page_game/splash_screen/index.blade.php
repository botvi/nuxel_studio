<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Nuxel Games — Loading</title>
    <link rel="stylesheet" href="{{ asset('game_pacu/assets/css/game-layout.css') }}">
    <style>
        #page-transition-overlay {
            display: none !important;
        }
        /* Container styling */
        .splash-container {
            position: relative;
            width: 100%;
            height: 100%;
            background: #0a0813; /* Classic retro dark space/indigo */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 40px 20px;
            overflow: hidden;
            color: #ffffff;
            box-sizing: border-box;
            font-family: 'Press Start 2P', monospace;
            image-rendering: pixelated;
        }

        /* CRT Screen Effect */
        .crt-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.35) 50%), linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06));
            background-size: 100% 4px, 6px 100%;
            z-index: 50;
            pointer-events: none;
            opacity: 0.9;
        }

        /* Screen Flicker Animation */
        @keyframes flicker {
            0% { opacity: 0.98; }
            50% { opacity: 1; }
            100% { opacity: 0.99; }
        }

        .splash-container::after {
            content: " ";
            display: block;
            position: absolute;
            top: 0; left: 0; bottom: 0; right: 0;
            background: rgba(18, 16, 16, 0.07);
            opacity: 0;
            z-index: 51;
            pointer-events: none;
            animation: flicker 0.15s infinite;
        }

        /* Vignette effect */
        .splash-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, transparent 55%, rgba(0, 0, 0, 0.85) 120%);
            z-index: 10;
            pointer-events: none;
        }

        /* Retro Studio intro text */
        .studio-intro {
            font-size: 8px;
            color: #00ffff; /* Arcade neon cyan */
            text-shadow: 2px 2px 0px #000000;
            letter-spacing: 2px;
            animation: pulse 1.5s infinite alternate;
            margin-top: 30px;
            text-align: center;
            z-index: 15;
        }

        @keyframes pulse {
            0% { opacity: 0.6; transform: scale(0.98); }
            100% { opacity: 1; transform: scale(1.02); }
        }

        /* Logo styling */
        .logo-wrapper {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: auto 0;
            z-index: 15;
            animation: float 3s ease-in-out infinite;
        }

        .pixel-logo {
            max-width: 280px;
            height: auto;
            image-rendering: pixelated;
            filter: drop-shadow(0px 8px 0px #000000); /* Solid black shadow */
            animation: logo-entrance 1.2s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        @keyframes logo-entrance {
            0% {
                transform: scale(0) rotate(-10deg);
                opacity: 0;
            }
            70% {
                transform: scale(1.1) rotate(3deg);
            }
            100% {
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        /* Shimmer scanline effect on the logo */
        .logo-glow {
            position: absolute;
            top: -20%;
            left: -20%;
            width: 140%;
            height: 140%;
            background: radial-gradient(circle, rgba(0, 255, 255, 0.15) 0%, transparent 60%);
            pointer-events: none;
            z-index: -1;
            animation: glow-pulse 3s ease-in-out infinite alternate;
        }

        @keyframes glow-pulse {
            0% { transform: scale(0.9); opacity: 0.5; }
            100% { transform: scale(1.1); opacity: 1; }
        }

        /* Loading section styling */
        .loading-section {
            width: 100%;
            max-width: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 15;
            margin-bottom: 20px;
        }

        .loading-text {
            font-size: 8px;
            color: #ffffff;
            margin-bottom: 12px;
            letter-spacing: 1.5px;
            text-shadow: 2px 2px 0px #000000;
        }

        .percent-val {
            color: #ffff00; /* Retro yellow percent value */
            font-weight: bold;
        }

        /* Pixel Progress Bar */
        .pixel-progress-bar {
            width: 100%;
            height: 20px;
            background: #000000; /* Solid black background */
            border: 4px solid #ffffff; /* Solid white border */
            border-radius: 0px;
            padding: 2px;
            position: relative;
            box-sizing: border-box;
            box-shadow: 4px 4px 0px #000000; /* Solid black shadow */
        }

        .pixel-progress-fill {
            width: 0%;
            height: 100%;
            background: #00ff00; /* Solid neon green fill */
            transition: width 0.1s linear;
        }

        .retro-status {
            font-size: 7px;
            color: #00ffff; /* Retro neon cyan */
            margin-top: 10px;
            letter-spacing: 1px;
            text-shadow: 2px 2px 0px #000000;
            animation: blink 1s step-end infinite;
        }

        @keyframes blink {
            50% { opacity: 0.5; }
        }

        /* Splash footer styling */
        .splash-footer {
            font-size: 6px;
            color: #555566; /* Retro dim gray */
            letter-spacing: 1px;
            text-align: center;
            z-index: 15;
        }

        /* Decorative Stars/Particles */
        .pixel-stars {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 2;
        }

        .star {
            position: absolute;
            width: 6px;
            height: 6px;
            background: #ffffff;
            opacity: 0.6;
            animation: blink-star 2s infinite ease-in-out;
        }

        .star::before, .star::after {
            content: "";
            position: absolute;
            background: #ffffff;
        }

        /* Cross shape for retro stars */
        .star::before {
            top: -2px; left: 2px; width: 2px; height: 10px;
        }
        .star::after {
            top: 2px; left: -2px; width: 10px; height: 2px;
        }

        @keyframes blink-star {
            0%, 100% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1); opacity: 0.8; }
        }

        /* Position various stars randomly */
        .s1 { top: 15%; left: 15%; animation-delay: 0.2s; background: #22c55e; }
        .s1::before, .s1::after { background: #22c55e; }

        .s2 { top: 30%; right: 20%; animation-delay: 0.8s; background: #facc15; }
        .s2::before, .s2::after { background: #facc15; }

        .s3 { bottom: 35%; left: 25%; animation-delay: 1.4s; background: #38bdf8; }
        .s3::before, .s3::after { background: #38bdf8; }

        .s4 { bottom: 20%; right: 15%; animation-delay: 0.5s; background: #ec4899; }
        .s4::before, .s4::after { background: #ec4899; }

        .s5 { top: 50%; left: 80%; animation-delay: 1.1s; background: #a855f7; }
        .s5::before, .s5::after { background: #a855f7; }
    </style>
</head>
<body>

<div id="desktop-wrapper">
    <div id="mobile-frame">
        <div id="status-bar">
            <span id="clock">00:00</span>
            <span>&#11044;&#11044;&#11044;</span>
        </div>
        
        <!-- Splash Screen Layout -->
        <div class="splash-container">
            <!-- Scanline/CRT effects -->
            <div class="crt-overlay"></div>
            
            <!-- Floating/Sparking Star Particles (Retro Pixel Art style) -->
            <div class="pixel-stars">
                <div class="star s1"></div>
                <div class="star s2"></div>
                <div class="star s3"></div>
                <div class="star s4"></div>
                <div class="star s5"></div>
            </div>

            <!-- Content elements -->
            <div class="splash-content">
                <!-- Intro Studio Subtitle -->
                <div class="studio-intro">NUXEL STUDIO PRESENTS</div>
                
                <!-- Pixel Logo -->
                <div class="logo-wrapper">
                    <img src="{{ asset('env/logo_text1.png') }}" alt="Nuxel Games Logo" class="pixel-logo">
                    <div class="logo-glow"></div>
                </div>
            </div>

            <!-- Loading section -->
            <div class="loading-section">
                <div class="loading-text">MEMUAT GAME... <span class="percent-val">0%</span></div>
                <div class="pixel-progress-bar">
                    <div class="pixel-progress-fill"></div>
                </div>
                <div class="retro-status">INITIALIZING SYSTEM...</div>
            </div>
            
            <!-- Footer copyrights -->
            <div class="splash-footer">
                © 2026 Nuxel Studio. All Rights Reserved.
            </div>
        </div>

    </div>
</div>

<script src="{{ asset('game_pacu/assets/js/game-layout.js') }}?v=<?= time() ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Start playing the progress bar animation in sync with JS counter
        const fill = document.querySelector('.pixel-progress-fill');
        const percentVal = document.querySelector('.percent-val');
        const statusText = document.querySelector('.retro-status');
        
        const statusMessages = [
            "CONNECTING TO SERVER...",
            "LOADING PIXEL ASSETS...",
            "PREPARING ARENA PACU...",
            "GETTING JALUR READY...",
            "STARTING ENGINE...",
            "READY!"
        ];
        
        let start = null;
        const duration = 4700; // 4.7 seconds (leaves 300ms for redirect fade transition)

        function animate(timestamp) {
            if (!start) start = timestamp;
            const elapsed = timestamp - start;
            const progress = Math.min(elapsed / duration, 1);
            
            // Update percentage text
            const percentage = Math.floor(progress * 100);
            percentVal.textContent = percentage + '%';
            fill.style.width = percentage + '%';
            
            // Dynamic retro status messages based on percentage
            let msgIndex = Math.floor(progress * (statusMessages.length - 1));
            statusText.textContent = statusMessages[msgIndex];

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
