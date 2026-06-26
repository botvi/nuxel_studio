<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Nuxel Games')</title>
    <link rel="stylesheet" href="/game_pacu/assets/css/game-layout.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Pixelify+Sans:wght@400;700&display=swap" rel="stylesheet">
    {{-- Preload Phaser.js lokal agar browser mulai download di background --}}
    <link rel="preload" href="/game_pacu/assets/js/phaser.min.js" as="script" crossorigin="anonymous">
    {{-- Preload asset arena yang sering dipakai --}}
    <link rel="preload" href="/game_pacu/assets/image/bg/bgmenu.jpg" as="image">
    <link rel="preload" href="/game_pacu/assets/image/ui/koin.png" as="image">
    <link rel="preload" href="/game_pacu/assets/image/ui/back.png" as="image">
    @livewireStyles
    @stack('styles')
</head>
<body>
    <div id="desktop-wrapper">
        <div id="mobile-frame">
            <div id="status-bar">
                <span id="clock">00:00</span>
                <span>&#11044;&#11044;&#11044;</span>
            </div>

            <!-- Page Transition Overlay (smooth SPA transitions) -->
            <div id="page-transition-overlay">
                <div class="transition-content">
                    <div class="transition-title">✦ MEMUAT... ✦</div>
                    <div class="transition-bar-container">
                        <div class="transition-bar-fill"></div>
                    </div>
                </div>
            </div>

            <div id="game-container">
                @yield('content')
            </div>
        </div>
    </div>

    @livewireScripts
    <script src="/game_pacu/assets/js/game-layout.js?v={{ time() }}"></script>
    <script>
        // Digital Clock
        (function () {
            function updateClock() {
                const now = new Date();
                const h = String(now.getHours()).padStart(2, '0');
                const m = String(now.getMinutes()).padStart(2, '0');
                const el = document.getElementById('clock');
                if (el) el.textContent = h + ':' + m;
            }
            updateClock();
            setInterval(updateClock, 15000);
        })();

        // Fade out transition overlay on first load
        document.addEventListener('DOMContentLoaded', () => {
            const overlay = document.getElementById('page-transition-overlay');
            if (overlay) {
                setTimeout(() => {
                    overlay.classList.add('fade-out');
                }, 150);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
