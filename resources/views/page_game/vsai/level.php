<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title>Pilih Level — VS AI Mode</title>
    <link rel="stylesheet" href="/game_pacu/assets/css/game-layout.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Pixelify+Sans:wght@400;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #060d18;
            color: #e2e8f0;
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
            background: radial-gradient(circle at 50% 60%, rgba(249, 115, 22, 0.4) 0%, rgba(15, 23, 42, 0.35) 50%, rgba(20, 10, 5, 0.9) 100%);
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
            padding: 0;
            box-shadow: none;
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

        /* --- Wallet Koin (Borderless, transparent to match other screens) --- */
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
                    rgba(255, 255, 255, 0.3) 50%,
                    rgba(255, 255, 255, 0) 100%);
            transform: translateX(-100%);
            animation: shimmer 3s infinite;
        }

        #coin-count {
            font-family: 'Pixelify Sans', monospace;
            font-size: 13px;
            font-weight: bold;
            color: #ffd700;
            text-shadow:
                -2px -2px 0 #000,
                2px -2px 0 #000,
                -2px 2px 0 #000,
                2px 2px 0 #000,
                -2px 0px 0 #000,
                2px 0px 0 #000,
                0px -2px 0 #000,
                0px 2px 0 #000;
        }

        @keyframes shimmer {
            100% {
                transform: translateX(100%);
            }
        }

        .title-banner {
            font-family: 'Press Start 2P', monospace;
            font-size: 11px;
            color: #ffffff;
            text-shadow:
                -2px -2px 0 #000,
                2px -2px 0 #000,
                -2px 2px 0 #000,
                2px 2px 0 #000,
                -2px 0px 0 #000,
                2px 0px 0 #000,
                0px -2px 0 #000,
                0px 2px 0 #000;
            margin-top: 55px;
            margin-bottom: 12px;
            text-align: center;
            line-height: 1.4;
            letter-spacing: 2px;
            z-index: 11;
        }

        .menu-panel {
            background: none;
            border: none;
            box-shadow: none;
            width: 95%;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 11;
            box-sizing: border-box;
            position: relative;
            height: 520px;
        }

        /* --- PlayStation Horizontal Slider styling --- */
        .ps5-carousel-container {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            box-sizing: border-box;
            padding: 0 10px;
        }

        .ps5-carousel-view {
            width: 130px;
            /* displays 1 card active at center */
            height: 155px;
            overflow: visible;
            position: relative;
            box-sizing: border-box;
        }

        .ps5-carousel-track {
            display: flex;
            gap: 20px;
            transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            will-change: transform;
        }

        .ps5-card {
            width: 130px;
            height: 150px;
            background: rgba(10, 18, 36, 0.85);
            border: 3px solid #000000;
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-sizing: border-box;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            opacity: 0.32;
            transform: scale(0.8);
            position: relative;
            box-shadow: 0px 6px 0px #000000;
        }

        .ps5-card.active {
            opacity: 1;
            transform: scale(1.05);
            border-color: #ffd700;
            box-shadow: 0px 6px 0px #000000, 0px 0px 0px 3px #ffd700;
        }

        /* Inner circular tech chip inside cards */
        .level-chip {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 68px;
            height: 68px;
            border-radius: 4px;
            background: #0f172a;
            border: 3px solid #000000;
            box-shadow: 0px 4px 0px #000000;
            font-family: 'Press Start 2P', monospace;
            transition: all 0.2s ease;
            position: relative;
        }

        .ps5-card.active .level-chip {
            transform: scale(1.08);
        }

        /* States for level chips */
        .level-chip.locked {
            background: #0d121e;
            border-color: #000000;
            color: #4b5563;
        }

        .level-chip.completed {
            background: linear-gradient(135deg, #064e3b 0%, #022c22 100%);
            border-color: #000000;
            color: #34d399;
        }

        .level-chip.unlocked-current {
            background: linear-gradient(135deg, #ca8a04 0%, #78350f 100%);
            border-color: #000000;
            color: #fde047;
        }

        .level-chip.unlocked-current::after {
            content: '';
            position: absolute;
            top: -6px;
            left: -6px;
            right: -6px;
            bottom: -6px;
            border-radius: 4px;
            border: 2px dashed #eab308;
            animation: rotate-gold-dash 10s linear infinite;
        }

        @keyframes rotate-gold-dash {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .level-num {
            font-size: 15px;
            font-weight: bold;
            text-shadow:
                -2px -2px 0 #000,
                2px -2px 0 #000,
                -2px 2px 0 #000,
                2px 2px 0 #000,
                -2px 0px 0 #000,
                2px 0px 0 #000,
                0px -2px 0 #000,
                0px 2px 0 #000;
        }

        .level-status {
            font-size: 8px;
            margin-top: 4px;
            font-weight: bold;
            text-shadow:
                -1px -1px 0 #000,
                1px -1px 0 #000,
                -1px 1px 0 #000,
                1px 1px 0 #000,
                -1px 0px 0 #000,
                1px 0px 0 #000,
                0px -1px 0 #000,
                0px 1px 0 #000;
        }

        /* Details styling (PS5 action/details layout with high readability panel) */
        .ps5-details-container {
            margin-top: 25px;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 85%;
            max-width: 320px;
            background: rgba(10, 18, 36, 0.85);
            border: 3px solid #000000;
            border-radius: 8px;
            box-shadow: 0px 6px 0px #000000;
            padding: 16px 12px;
            z-index: 10;
            box-sizing: border-box;
        }

        .ps5-details-title {
            font-family: 'Press Start 2P', monospace;
            font-size: 13px;
            font-weight: bold;
            color: #ffd700;
            text-shadow:
                -2px -2px 0 #000,
                2px -2px 0 #000,
                -2px 2px 0 #000,
                2px 2px 0 #000,
                -2px 0px 0 #000,
                2px 0px 0 #000,
                0px -2px 0 #000,
                0px 2px 0 #000;
            margin-bottom: 8px;
        }

        .ps5-details-desc {
            font-family: 'Pixelify Sans', monospace;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-shadow:
                -2px -2px 0 #000,
                2px -2px 0 #000,
                -2px 2px 0 #000,
                2px 2px 0 #000,
                -2px 0px 0 #000,
                2px 0px 0 #000,
                0px -2px 0 #000,
                0px 2px 0 #000;
        }

        /* PS5 Action Button (pixel-btn override) */
        .pixel-btn {
            background: linear-gradient(180deg, #22c55e 0%, #16a34a 100%);
            border: 3px solid #000000;
            border-radius: 6px;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.35),
                0 5px 0 #000000;
            color: white;
            font-family: 'Press Start 2P', monospace;
            font-size: 9px;
            padding: 13px;
            width: 100%;
            text-align: center;
            cursor: pointer;
            text-transform: uppercase;
            box-sizing: border-box;
            display: block;
            text-shadow:
                -2px -2px 0 #000,
                2px -2px 0 #000,
                -2px 2px 0 #000,
                2px 2px 0 #000,
                -2px 0px 0 #000,
                2px 0px 0 #000,
                0px -2px 0 #000,
                0px 2px 0 #000;
            transition: all 0.12s cubic-bezier(0.25, 0.8, 0.25, 1);
            letter-spacing: 0.5px;
        }

        .pixel-btn:hover:not(:disabled) {
            background: linear-gradient(180deg, #4ade80 0%, #22c55e 100%);
            transform: translateY(-1px);
        }

        .pixel-btn:active:not(:disabled) {
            transform: translateY(4px);
            box-shadow: 0 1px 0 #000000;
        }

        .pixel-btn:disabled {
            background: #4b5563;
            border-color: #000000;
            box-shadow: 0 4px 0 #000000;
            color: #9ca3af;
            cursor: not-allowed;
            opacity: 0.6;
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
                    <div id="ps5-backdrop" class="ps5-backdrop-glow"></div>

                    <!-- Tombol Back ke Room Menu -->
                    <button class="back-btn" onclick="window.location.href='/room'">
                        <img src="/game_pacu/assets/image/ui/back.png" alt="Back">
                    </button>

                    <!-- Wallet Koin (Borderless, matches main menu) -->
                    <div class="coin-display">
                        <div class="coin-icon-wrapper">
                            <img src="/game_pacu/assets/image/ui/koin.png" alt="Coin">
                        </div>
                        <span id="coin-count">...</span>
                    </div>

                    <div class="title-banner">✦ PILIH LEVEL ✦</div>

                    <div class="menu-panel">
                        <!-- Carousel Menu PS5 -->
                        <div class="ps5-carousel-container">
                            <button class="carousel-nav-btn prev-btn" onclick="prevSlide(event)"
                                style="background: none; border: none; cursor: pointer; padding: 10px; z-index: 10;">
                                <img src="/game_pacu/assets/image/ui/btn_kiri.png" alt="Left"
                                    style="width: 28px; height: 28px; image-rendering: pixelated;">
                            </button>
                            <div class="ps5-carousel-view">
                                <div class="ps5-carousel-track" id="carousel-track">
                                    <!-- Generated dynamically by JS -->
                                </div>
                            </div>
                            <button class="carousel-nav-btn next-btn" onclick="nextSlide(event)"
                                style="background: none; border: none; cursor: pointer; padding: 10px; z-index: 10;">
                                <img src="/game_pacu/assets/image/ui/btn_kanan.png" alt="Right"
                                    style="width: 28px; height: 28px; image-rendering: pixelated;">
                            </button>
                        </div>

                        <!-- Details & Actions (PS5-Style) -->
                        <div class="ps5-details-container">
                            <div class="ps5-details-title" id="active-title">LEVEL 1</div>
                            <div class="ps5-details-desc" id="active-desc">STATUS: MAINKAN</div>
                            <div id="active-reward-wrapper"
                                style="font-family: 'Pixelify Sans', monospace; font-size: 13px; color: #ffd700; display: flex; align-items: center; gap: 4px; margin-bottom: 20px; text-shadow: -2px -2px 0 #000, 2px -2px 0 #000, -2px 2px 0 #000, 2px 2px 0 #000, -2px 0px 0 #000, 2px 0px 0 #000, 0px -2px 0 #000, 0px 2px 0 #000;">
                                <img src="/game_pacu/assets/image/ui/koin.png" alt="Coin"
                                    style="width: 16px; height: 16px; image-rendering: pixelated;">
                                <span id="active-reward">REWARD: 5 KP</span>
                            </div>

                            <button id="ps5-action-btn" class="pixel-btn" style="width: 180px; padding: 12px;"
                                onclick="startSelectedLevel()">MAIN BALAPAN</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="/game_pacu/assets/js/game-layout.js?v=<?= time() ?>"></script>
    <script>
        // Sync user coins
        const userCoins = <?= auth()->user()->kuansing_poin ?? 0 ?>;
        localStorage.setItem('coins', userCoins);
        document.getElementById('coin-count').innerText = userCoins.toLocaleString('id-ID');

        // Load level progression
        const vsaiUnlocked = parseInt(localStorage.getItem('vsai_unlocked') || '1');
        const totalLevels = 100;
        let currentSlide = vsaiUnlocked - 1; // Center on current unlocked level
        if (currentSlide >= totalLevels) currentSlide = totalLevels - 1;

        // Render level cards
        const carouselTrack = document.getElementById('carousel-track');
        for (let level = 1; level <= totalLevels; level++) {
            const card = document.createElement('div');
            card.className = 'ps5-card' + (level === (currentSlide + 1) ? ' active' : '');
            card.dataset.index = level - 1;

            let statusChar = '🔒';
            let chipClass = 'level-chip locked';

            if (level < vsaiUnlocked) {
                statusChar = '✓';
                chipClass = 'level-chip completed';
            } else if (level === vsaiUnlocked) {
                statusChar = 'GO!';
                chipClass = 'level-chip unlocked-current';
            }

            card.innerHTML = `
                <div class="${chipClass}">
                    <div class="level-num">${level}</div>
                    <div class="level-status">${statusChar}</div>
                </div>
            `;

            card.onclick = (e) => {
                selectSlide(level - 1, e);
            };

            carouselTrack.appendChild(card);
        }

        function updateCarousel() {
            const track = document.getElementById('carousel-track');
            const view = document.querySelector('.ps5-carousel-view');
            if (!track || !view) return;
            const cards = document.querySelectorAll('.ps5-card');

            const cardWidth = 130;
            const gap = 20;

            const viewWidth = view.offsetWidth || 130;
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

            // Update details
            const activeLevel = currentSlide + 1;
            const activeTitle = document.getElementById('active-title');
            const activeDesc = document.getElementById('active-desc');
            const activeReward = document.getElementById('active-reward');
            const actionBtn = document.getElementById('ps5-action-btn');

            activeTitle.innerText = `LEVEL ${activeLevel}`;

            const rewardCoins = activeLevel * 5;
            activeReward.innerText = `REWARD: ${rewardCoins} KP`;

            if (activeLevel < vsaiUnlocked) {
                activeDesc.innerText = 'STATUS: SELESAI';
                activeDesc.style.color = '#34d399';
                actionBtn.innerText = 'MAIN LAGI';
                actionBtn.style.background = 'linear-gradient(180deg, #10b981 0%, #047857 100%)';
                actionBtn.style.borderColor = '#000000';
                actionBtn.style.boxShadow = 'inset 0 1px 0 rgba(255,255,255,0.35), 0 5px 0 #000000';
                actionBtn.disabled = false;
                actionBtn.style.opacity = '1';
                actionBtn.style.cursor = 'pointer';
            } else if (activeLevel === vsaiUnlocked) {
                activeDesc.innerText = 'STATUS: MAINKAN';
                activeDesc.style.color = '#fde047';
                actionBtn.innerText = 'MAIN BALAPAN';
                actionBtn.style.background = 'linear-gradient(180deg, #eab308 0%, #ca8a04 100%)';
                actionBtn.style.borderColor = '#000000';
                actionBtn.style.boxShadow = 'inset 0 1px 0 rgba(255,255,255,0.35), 0 5px 0 #000000';
                actionBtn.disabled = false;
                actionBtn.style.opacity = '1';
                actionBtn.style.cursor = 'pointer';
            } else {
                activeDesc.innerText = 'STATUS: TERKUNCI 🔒';
                activeDesc.style.color = '#9ca3af';
                actionBtn.innerText = 'TERKUNCI';
                actionBtn.style.background = '#4b5563';
                actionBtn.style.borderColor = '#000000';
                actionBtn.style.boxShadow = '0 4px 0 #000000';
                actionBtn.disabled = true;
                actionBtn.style.opacity = '0.6';
                actionBtn.style.cursor = 'not-allowed';
            }
        }

        function nextSlide(e) {
            if (e) e.stopPropagation();
            if (currentSlide < totalLevels - 1) {
                currentSlide++;
                if (typeof window.playClickSound === 'function') window.playClickSound();
                updateCarousel();
            }
        }

        function prevSlide(e) {
            if (e) e.stopPropagation();
            if (currentSlide > 0) {
                currentSlide--;
                if (typeof window.playClickSound === 'function') window.playClickSound();
                updateCarousel();
            }
        }

        function selectSlide(idx, e) {
            if (e) e.stopPropagation();
            if (currentSlide !== idx) {
                currentSlide = idx;
                if (typeof window.playClickSound === 'function') window.playClickSound();
                updateCarousel();
            }
        }

        function startSelectedLevel() {
            const activeLevel = currentSlide + 1;
            if (activeLevel <= vsaiUnlocked) {
                if (typeof window.playClickSound === 'function') window.playClickSound();
                window.location.href = `/vsai/arena?level=${activeLevel}`;
            }
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

        // Keyboard Navigation
        document.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowLeft') {
                prevSlide();
            } else if (e.key === 'ArrowRight') {
                nextSlide();
            } else if (e.key === 'Enter') {
                startSelectedLevel();
            }
        });

        // Auto positioning trigger
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(updateCarousel, 100);
        });
    </script>
</body>

</html>