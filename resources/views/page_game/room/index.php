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
            overflow: hidden;
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
            overflow: hidden;
        }

        /* --- Dynamic Backdrop Glow --- */
        .ps5-backdrop-glow {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
            transition: background 0.8s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .bg-slide-0 {
            background: radial-gradient(circle at 50% 60%, rgba(34, 197, 94, 0.5) 0%, rgba(15, 23, 42, 0.3) 50%, rgba(6, 17, 10, 0.85) 100%);
        }

        .bg-slide-1 {
            background: radial-gradient(circle at 50% 60%, rgba(59, 130, 246, 0.5) 0%, rgba(15, 23, 42, 0.3) 50%, rgba(5, 10, 20, 0.85) 100%);
        }

        .bg-slide-2 {
            background: radial-gradient(circle at 50% 60%, rgba(249, 115, 22, 0.5) 0%, rgba(15, 23, 42, 0.3) 50%, rgba(20, 10, 5, 0.85) 100%);
        }

        .bg-slide-3 {
            background: radial-gradient(circle at 50% 60%, rgba(168, 85, 247, 0.5) 0%, rgba(15, 23, 42, 0.3) 50%, rgba(15, 5, 20, 0.85) 100%);
        }

        /* --- PS5 Carousel Slider --- */
        .ps5-carousel-container {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 5px;
            margin-bottom: 10px;
            z-index: 12;
        }

        .ps5-carousel-view {
            width: 100%;
            max-width: 300px;
            height: 155px;
            overflow: visible;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            /* Align left to make translateX centering math exact */
        }

        .ps5-carousel-track {
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1);
            will-change: transform;
        }

        .ps5-card {
            width: 110px;
            height: 135px;
            flex-shrink: 0;
            border-radius: 16px;
            border: 2px solid rgba(255, 255, 255, 0.15);
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 12px;
            box-sizing: border-box;
            cursor: pointer;
            position: relative;
            transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
            opacity: 0.45;
            transform: scale(0.85);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
            overflow: hidden;
        }

        .ps5-card.active {
            opacity: 1;
            transform: scale(1.1);
            border-color: #ffffff;
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.6), 0 0 15px var(--glow-color);
        }

        .ps5-card.card-green {
            --glow-color: rgba(34, 197, 94, 0.6);
        }

        .ps5-card.card-blue {
            --glow-color: rgba(59, 130, 246, 0.6);
        }

        .ps5-card.card-orange {
            --glow-color: rgba(249, 115, 22, 0.6);
        }

        .ps5-card.card-purple {
            --glow-color: rgba(168, 85, 247, 0.6);
        }

        .ps5-card-icon {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.4));
            transition: transform 0.3s ease;
        }

        .ps5-card.active .ps5-card-icon {
            transform: translateY(-4px) scale(1.08);
        }

        .ps5-card-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            image-rendering: pixelated;
        }

        .ps5-card-label {
            font-family: 'Press Start 2P', monospace;
            font-size: 8px;
            text-align: center;
            color: #ffffff;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
            line-height: 1.4;
            pointer-events: none;
            font-weight: bold;
        }

        .ps5-pattern {
            position: absolute;
            bottom: 4px;
            right: 6px;
            font-family: Arial, sans-serif;
            font-size: 20px;
            font-weight: bold;
            color: rgba(255, 255, 255, 0.04);
            pointer-events: none;
            user-select: none;
            line-height: 1;
        }

        .ps5-card.active .ps5-pattern {
            color: rgba(255, 255, 255, 0.1);
        }

        /* Nav controls */
        .carousel-nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 13;
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
            padding: 0;
            box-shadow: none;
        }

        .carousel-nav-btn img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            image-rendering: pixelated;
        }

        .carousel-nav-btn:hover {
            transform: translateY(-50%) scale(1.15);
            filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.6));
        }

        .carousel-nav-btn:active {
            transform: translateY(-50%) scale(0.9);
        }

        .prev-btn {
            left: 4px;
        }

        .next-btn {
            right: 4px;
        }

        /* Slide Details & Indicators */
        .ps5-details-container {
            width: 90%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            z-index: 12;
            margin-top: 2px;
        }

        .ps5-details-title {
            font-family: 'Press Start 2P', monospace;
            font-size: 11px;
            letter-spacing: 1px;
            color: #ffffff;
            margin-bottom: 6px;
            text-shadow: 0 0 10px var(--glow-color);
            transition: text-shadow 0.3s;
        }

        .ps5-details-desc {
            font-family: 'Pixelify Sans', monospace;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 12px;
            height: 34px;
            line-height: 1.4;
            max-width: 250px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        .ps5-indicators {
            display: flex;
            gap: 8px;
            margin-bottom: 5px;
        }

        .ps5-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.25);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .ps5-dot.active {
            background: #ffffff;
            transform: scale(1.25);
            box-shadow: 0 0 8px #ffffff;
        }

        .back-btn {
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

        .back-btn img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            image-rendering: pixelated;
        }

        .back-btn:hover {
            transform: scale(1.05);
        }

        .back-btn:active {
            transform: scale(0.9);
        }

        .title-banner {
            font-family: 'Press Start 2P', monospace;
            font-size: 12px;
            background: linear-gradient(180deg, #ffffff 0%, #a5f3fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0px 2px 4px rgba(0, 0, 0, 0.8));
            margin-top: 76px;
            margin-bottom: 15px;
            text-align: center;
            line-height: 1.4;
            letter-spacing: 2px;
            z-index: 11;
        }

        .menu-panel {
            background: rgba(15, 23, 42, 0.5);
            border: 2px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            width: 85%;
            padding: 30px 24px;
            box-shadow:
                0 8px 32px 0 rgba(0, 0, 0, 0.4),
                0 0 15px rgba(34, 197, 94, 0.3);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 18px;
            z-index: 11;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-sizing: border-box;
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

        .btn-blue {
            background-color: #3b82f6;
        }

        .btn-blue:hover {
            background-color: #60a5fa;
        }

        .btn-blue:active {
            transform: translateY(4px);
            box-shadow: inset 0 2px 0px rgba(255, 255, 255, 0.1), 0px 0px 0px #000000;
        }

        /* Loading Overlay Styling */
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
            z-index: 100;
            backdrop-filter: blur(4px);
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
            text-shadow: 2px 2px 0px #000000;
            background: rgba(15, 23, 42, 0.6);
            padding: 6px 12px;
            border: 2px solid rgba(255, 255, 255, 0.15);
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            z-index: 102;
        }

        /* Cancel Button Styling */
        .btn-cancel {
            background-color: #ef4444;
            border: 3px solid #000000;
            border-radius: 8px;
            box-shadow: inset 0 2px 0px rgba(255, 255, 255, 0.4), 0px 4px 0px #000000;
            font-family: 'Press Start 2P', monospace;
            font-size: 9px;
            padding: 12px 24px;
            width: auto;
            text-shadow: 1.5px 1.5px 0px #000000;
            transition: all 0.1s ease;
            color: #ffffff;
            cursor: pointer;
            z-index: 102;
        }

        .btn-cancel:hover {
            background-color: #f87171;
        }

        .btn-cancel:active {
            transform: translateY(4px);
            box-shadow: inset 0 2px 0px rgba(255, 255, 255, 0.1), 0px 0px 0px #000000;
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
                    <div id="ps5-backdrop" class="ps5-backdrop-glow bg-slide-0"></div>
                    <canvas id="ps5-particles"
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none; opacity: 0.5;"></canvas>

                    <div class="back-btn" onclick="window.location.href='/main-menu'">
                        <img src="/game_pacu/assets/image/ui/back.png" alt="Back">
                    </div>
                    <!-- <div class="title-banner">✦ ONLINE ARENA ✦</div> -->

                    <div class="menu-panel" style="width: 90%;">
                        <!-- JALUR PREVIEW INSIDE PANEL -->
                        <div class="preview-name" id="jalur-preview-name"
                            style="font-family: 'Pixelify Sans', monospace; font-size: 12px; font-weight: bold; color: #ffaa00 !important; margin-bottom: 6px; letter-spacing: 0.5px; text-transform: uppercase; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.6) !important;">
                            LOADING...</div>
                        <div class="canvas-container" id="jalur-preview-canvas"
                            style="width: 250px; height: 85px; border: none; background: transparent; margin-bottom: 10px; overflow: hidden; display: flex; justify-content: center; align-items: center;">
                        </div>

                        <!-- Carousel Menu PS5 -->
                        <div class="ps5-carousel-container">
                            <button class="carousel-nav-btn prev-btn" onclick="prevSlide(event)">
                                <img src="/game_pacu/assets/image/ui/btn_kiri.png" alt="Left">
                            </button>
                            <div class="ps5-carousel-view">
                                <div class="ps5-carousel-track" id="carousel-track">
                                    <!-- Slide 0: CARI LAWAN -->
                                    <div class="ps5-card card-green active" data-index="0" onclick="selectSlide(0, event)">
                                        <div class="ps5-card-icon">
                                            <img src="/game_pacu/assets/image/ui/magnifer.png" alt="Search">
                                        </div>
                                        <div class="ps5-card-label">CARI LAWAN</div>
                                        <div class="ps5-pattern">&#9587;</div>
                                    </div>
                                    <!-- Slide 1: CUSTOM ROOM -->
                                    <div class="ps5-card card-blue" data-index="1" onclick="selectSlide(1, event)">
                                        <div class="ps5-card-icon">
                                            <img src="/game_pacu/assets/image/ui/customroom.png" alt="Custom">
                                        </div>
                                        <div class="ps5-card-label">CUSTOM ROOM</div>
                                        <div class="ps5-pattern">&#9711;</div>
                                    </div>
                                    <!-- Slide 2: VS AI -->
                                    <div class="ps5-card card-orange" data-index="2" onclick="selectSlide(2, event)">
                                        <div class="ps5-card-icon">
                                            <img src="/game_pacu/assets/image/ui/vsai.png" alt="VS AI">
                                        </div>
                                        <div class="ps5-card-label">VS AI</div>
                                        <div class="ps5-pattern">&#9633;</div>
                                    </div>
                                    <!-- Slide 3: TOURNAMENT -->
                                    <div class="ps5-card card-purple" data-index="3" onclick="selectSlide(3, event)">
                                        <div class="ps5-card-icon">
                                            <img src="/game_pacu/assets/image/ui/piala.png" alt="Tournament">
                                        </div>
                                        <div class="ps5-card-label">TOURNAMENT</div>
                                        <div class="ps5-pattern">&#9651;</div>
                                    </div>
                                </div>
                            </div>
                            <button class="carousel-nav-btn next-btn" onclick="nextSlide(event)">
                                <img src="/game_pacu/assets/image/ui/btn_kanan.png" alt="Right">
                            </button>
                        </div>

                        <!-- Slide Details & Indicators -->
                        <div class="ps5-details-container">
                            <div class="ps5-details-title" id="active-title" style="--glow-color: rgba(34, 197, 94, 0.6)">CARI LAWAN</div>
                            <div class="ps5-details-desc" id="active-desc">Cari musuh secara online sekarang</div>
                            <div class="ps5-indicators">
                                <span class="ps5-dot active" onclick="jumpToSlide(0)"></span>
                                <span class="ps5-dot" onclick="jumpToSlide(1)"></span>
                                <span class="ps5-dot" onclick="jumpToSlide(2)"></span>
                                <span class="ps5-dot" onclick="jumpToSlide(3)"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Custom Coming Soon Modal -->
                    <div id="coming-soon-modal"
                        style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(6px); z-index: 210; align-items: center; justify-content: center; box-sizing: border-box;">
                        <div class="coming-soon-card"
                            style="background: #ffffff; border: 4px solid #000000; box-shadow: 6px 6px 0px #000000; border-radius: 12px; width: 85%; max-width: 300px; padding: 22px 18px; text-align: center; box-sizing: border-box; font-family: 'Press Start 2P', monospace;">
                            <div
                                style="font-size: 10px; color: #a855f7; margin-bottom: 20px; border-bottom: 3px dashed #000000; padding-bottom: 12px; font-weight: bold; letter-spacing: 0.5px;">
                                ✦ SEGERA HADIR ✦</div>
                            <p
                                style="font-family: 'Pixelify Sans', monospace; font-size: 13px; color: #374151; margin-bottom: 20px; line-height: 1.5;">
                                Fitur ini sedang dalam pengembangan dan akan segera hadir di masa mendatang. Terima kasih atas kesabaran Anda!</p>
                            <button class="pixel-btn" onclick="closeComingSoon()"
                                style="margin-top: 0; background-color: #a855f7; border: 3px solid #000000; box-shadow: inset 0 2px 0px rgba(255,255,255,0.4), 0px 4px 0px #000000; color: white; padding: 12px; font-size: 9px; cursor: pointer; text-transform: uppercase; width: 100%; text-shadow: 1.5px 1.5px 0px #000000;">OKE</button>
                        </div>
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

        // Carousel Slider Logic (PS5 style) for Online Arena
        const slidesData = [
            { title: 'CARI LAWAN', desc: 'Cari musuh secara online sekarang', url: '#', glow: 'rgba(34, 197, 94, 0.6)', action: 'search' },
            { title: 'CUSTOM ROOM', desc: 'Buat atau masuk room kustom', url: '/room/create-or-join', glow: 'rgba(59, 130, 246, 0.6)', action: 'link' },
            { title: 'VS AI', desc: 'Latihan balapan melawan bot AI', url: '#', glow: 'rgba(249, 115, 22, 0.6)', action: 'coming_soon' },
            { title: 'TOURNAMENT', desc: 'Ikuti turnamen balapan pacu jalur', url: '#', glow: 'rgba(168, 85, 247, 0.6)', action: 'coming_soon' }
        ];
        let currentSlide = 0;

        function updateCarousel() {
            const track = document.getElementById('carousel-track');
            const view = document.querySelector('.ps5-carousel-view');
            if (!track || !view) return;
            const cards = document.querySelectorAll('.ps5-card');
            const dots = document.querySelectorAll('.ps5-dot');
            const backdrop = document.getElementById('ps5-backdrop');

            const cardWidth = 110;
            const gap = 20;

            // Calculate center offset dynamically
            const viewWidth = view.offsetWidth || 300;
            const centerOffset = (viewWidth - cardWidth) / 2;

            const translateX = centerOffset - currentSlide * (cardWidth + gap);
            track.style.transform = `translateX(${translateX}px)`;

            cards.forEach((card, idx) => {
                if (idx === currentSlide) {
                    card.classList.add('active');
                } else {
                    card.classList.remove('active');
                }
            });

            dots.forEach((dot, idx) => {
                if (idx === currentSlide) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });

            const activeData = slidesData[currentSlide];
            document.getElementById('active-title').innerText = activeData.title;
            document.getElementById('active-desc').innerText = activeData.desc;

            document.getElementById('active-title').style.setProperty('--glow-color', activeData.glow);

            if (backdrop) {
                backdrop.className = 'ps5-backdrop-glow bg-slide-' + currentSlide;
            }
        }

        function nextSlide(e) {
            if (e) e.stopPropagation();
            currentSlide = (currentSlide + 1) % slidesData.length;
            if (typeof window.playClickSound === 'function') window.playClickSound();
            updateCarousel();
        }

        function prevSlide(e) {
            if (e) e.stopPropagation();
            currentSlide = (currentSlide - 1 + slidesData.length) % slidesData.length;
            if (typeof window.playClickSound === 'function') window.playClickSound();
            updateCarousel();
        }

        function selectSlide(idx, e) {
            if (e) e.stopPropagation();
            currentSlide = idx;
            if (typeof window.playClickSound === 'function') window.playClickSound();
            updateCarousel();
            activateActiveSlide();
        }

        function jumpToSlide(idx) {
            if (currentSlide !== idx) {
                currentSlide = idx;
                if (typeof window.playClickSound === 'function') window.playClickSound();
                updateCarousel();
            }
        }

        function activateActiveSlide() {
            const activeData = slidesData[currentSlide];
            if (activeData.action === 'search') {
                cariLawan();
            } else if (activeData.action === 'coming_soon') {
                openComingSoon();
            } else {
                const url = activeData.url;
                if (typeof window.navigateToPage === 'function') {
                    window.navigateToPage(url);
                } else {
                    window.location.href = url;
                }
            }
        }

        function openComingSoon() {
            const modal = document.getElementById('coming-soon-modal');
            if (modal) modal.style.display = 'flex';
        }

        function closeComingSoon() {
            const modal = document.getElementById('coming-soon-modal');
            if (modal) modal.style.display = 'none';
        }

        // Swipe Gestures Support
        (function () {
            let touchStartX = 0;
            let touchEndX = 0;

            const container = document.querySelector('.ps5-carousel-container');
            if (!container) return;

            container.addEventListener('touchstart', e => {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });

            container.addEventListener('touchend', e => {
                touchEndX = e.changedTouches[0].screenX;
                const diff = touchEndX - touchStartX;
                if (Math.abs(diff) > 40) {
                    if (diff < 0) {
                        nextSlide();
                    } else {
                        prevSlide();
                    }
                }
            }, { passive: true });
        })();

        // Keyboard Navigation Controller
        document.addEventListener('keydown', function (e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
            if (e.key === 'ArrowLeft') {
                prevSlide();
            } else if (e.key === 'ArrowRight') {
                nextSlide();
            } else if (e.key === 'Enter' || e.key === ' ') {
                activateActiveSlide();
            }
        });

        // Initialize Jalur Preview
        document.addEventListener('DOMContentLoaded', function () {
            window.initJalurPreview('jalur-preview-canvas', 'jalur-preview-name');
            setTimeout(updateCarousel, 100);
        });

        // Floating particles background effect
        (function () {
            const canvas = document.getElementById('ps5-particles');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            let width = canvas.width = canvas.offsetWidth;
            let height = canvas.height = canvas.offsetHeight;

            const particles = [];
            const particleCount = 25;

            for (let i = 0; i < particleCount; i++) {
                particles.push({
                    x: Math.random() * width,
                    y: Math.random() * height + height,
                    size: Math.random() * 3 + 1,
                    speed: Math.random() * 0.4 + 0.15,
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
    </script>
</body>

</html>