<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Pacu Jalur — Masuk Akun</title>
    <link rel="stylesheet" href="{{ asset('game_pacu/assets/css/game-layout.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Pixelify+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
    body {
        margin: 0;
        padding: 0;
        background-color: #0f172a;
        font-family: 'Pixelify Sans', monospace;
        overflow: hidden;
    }
    #game-ui {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: url('{{ asset('game_pacu/assets/image/bg/bgmenu.jpg') }}') no-repeat center center;
        background-size: cover;
        z-index: 10;
        overflow: hidden;
    }
    /* PS5 Backdrop Glow */
    .ps5-backdrop {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: radial-gradient(circle at 50% 60%, rgba(34, 197, 94, 0.45) 0%, rgba(15, 23, 42, 0.35) 50%, rgba(6, 17, 10, 0.88) 100%);
        z-index: 1;
        pointer-events: none;
    }
    /* Floating particles canvas */
    #ps5-particles {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        z-index: 2;
        pointer-events: none;
        opacity: 0.5;
    }
    /* Main content wrapper */
    .login-content {
        position: relative;
        z-index: 11;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        max-width: 320px;
        padding: 0 20px;
        box-sizing: border-box;
    }
    /* Game Title */
    .game-title {
        font-family: 'Press Start 2P', monospace;
        font-size: 13px;
        background: linear-gradient(180deg, #ffffff 0%, #86efac 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        filter: drop-shadow(0 2px 8px rgba(34, 197, 94, 0.5));
        text-align: center;
        line-height: 1.5;
        letter-spacing: 2px;
        margin-bottom: 6px;
    }
    .game-subtitle {
        font-family: 'Pixelify Sans', monospace;
        font-size: 13px;
        color: rgba(255,255,255,0.6);
        text-align: center;
        margin-bottom: 36px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.4);
    }
    /* Glassmorphism Login Card */
    .login-card {
        width: 100%;
        background: rgba(15, 23, 42, 0.65);
        border: 1.5px solid rgba(255,255,255,0.12);
        border-radius: 20px;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.5), 0 0 20px rgba(34, 197, 94, 0.15);
        padding: 28px 24px;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0;
    }
    .card-header {
        font-family: 'Press Start 2P', monospace;
        font-size: 9px;
        color: #22c55e;
        text-shadow: 0 0 10px rgba(34, 197, 94, 0.5);
        letter-spacing: 1px;
        margin-bottom: 20px;
        text-align: center;
    }
    /* Google Sign In Button */
    .google-btn {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        background: rgba(255,255,255,0.96);
        border: none;
        border-radius: 12px;
        padding: 14px 20px;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3), 0 0 0 1px rgba(255,255,255,0.2);
        transition: all 0.15s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
        overflow: hidden;
        text-decoration: none;
        box-sizing: border-box;
    }
    .google-btn:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 8px 25px rgba(0,0,0,0.4), 0 0 15px rgba(34, 197, 94, 0.3);
    }
    .google-btn:active {
        transform: translateY(1px) scale(0.98);
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }
    .google-btn-shimmer {
        position: absolute;
        top: 0; left: -100%;
        width: 50%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
        transform: skewX(-20deg);
        animation: shimmer 3s infinite ease-in-out;
    }
    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 200%; }
    }
    .google-logo {
        width: 22px;
        height: 22px;
        object-fit: contain;
        flex-shrink: 0;
    }
    .google-btn-text {
        font-family: 'Press Start 2P', monospace;
        font-size: 8px;
        color: #374151;
        white-space: nowrap;
    }
    /* Divider */
    .card-divider {
        width: 100%;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.12), transparent);
        margin: 20px 0 16px;
    }
    /* Footer info */
    .card-footer {
        font-family: 'Pixelify Sans', monospace;
        font-size: 11px;
        color: rgba(255,255,255,0.35);
        text-align: center;
        line-height: 1.5;
    }
    /* Loading overlay */
    .connecting-overlay {
        display: none;
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(8px);
        z-index: 100;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 16px;
    }
    .connecting-overlay.show {
        display: flex;
    }
    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid rgba(34, 197, 94, 0.2);
        border-top-color: #22c55e;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .connecting-text {
        font-family: 'Press Start 2P', monospace;
        font-size: 9px;
        color: #22c55e;
        text-shadow: 0 0 10px rgba(34, 197, 94, 0.5);
        animation: pulse-text 1.2s ease-in-out infinite;
    }
    @keyframes pulse-text {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
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
            <!-- PS5 Styled Login UI -->
            <div id="game-ui">
                <div class="ps5-backdrop"></div>
                <canvas id="ps5-particles"></canvas>

                <div class="login-content">
                    <!-- Game Title -->
                    <div class="game-title">PACU JALUR</div>
                    <div class="game-subtitle">Arena Balapan Perahu Nusantara</div>

                    <!-- Login Card -->
                    <div class="login-card">
                        <div class="card-header">✦ MASUK GAME ✦</div>

                        <!-- Google Sign In Button -->
                        <a id="google-login-btn" href="#" class="google-btn" onclick="handleGoogleLogin(event)">
                            <div class="google-btn-shimmer"></div>
                            <img src="{{ asset('game_pacu/assets/image/ui/google.png') }}" alt="Google" class="google-logo">
                            <span class="google-btn-text">MASUK DENGAN GOOGLE</span>
                        </a>

                        <div class="card-divider"></div>
                        <div class="card-footer">Hubungkan akun Google<br>untuk mulai bermain</div>
                    </div>
                </div>

                <!-- Connecting overlay -->
                <div class="connecting-overlay" id="connecting-overlay">
                    <div class="spinner"></div>
                    <div class="connecting-text">MENGHUBUNGKAN...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('game_pacu/assets/js/game-layout.js') }}?v=<?= time() ?>"></script>
<script>
    // ---- Floating Particles Effect ----
    (function () {
        const canvas = document.getElementById('ps5-particles');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let width = canvas.width = canvas.offsetWidth;
        let height = canvas.height = canvas.offsetHeight;

        const particles = [];
        for (let i = 0; i < 25; i++) {
            particles.push({
                x: Math.random() * width,
                y: Math.random() * height + height,
                size: Math.random() * 3 + 1,
                speed: Math.random() * 0.4 + 0.15,
                opacity: Math.random() * 0.4 + 0.2
            });
        }

        function animateParticles() {
            ctx.clearRect(0, 0, width, height);
            ctx.fillStyle = '#22c55e';
            particles.forEach(p => {
                ctx.globalAlpha = p.opacity;
                ctx.fillRect(p.x, p.y, p.size, p.size);
                p.y -= p.speed;
                if (p.y < -10) {
                    p.y = height + 10;
                    p.x = Math.random() * width;
                }
            });
            requestAnimationFrame(animateParticles);
        }

        window.addEventListener('resize', () => {
            if (canvas.offsetWidth) {
                width = canvas.width = canvas.offsetWidth;
                height = canvas.height = canvas.offsetHeight;
            }
        });

        animateParticles();
    })();

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

    // ---- Google Login Handler ----
    function handleGoogleLogin(e) {
        e.preventDefault();

        const width = 500, height = 650;
        const left = (window.screen.width / 2) - (width / 2);
        const top = (window.screen.height / 2) - (height / 2);
        const popup = window.open('about:blank', 'GoogleLoginPopup', `width=${width},height=${height},left=${left},top=${top},status=no,resizable=yes,scrollbars=yes`);

        if (!popup) {
            alert('Silakan aktifkan pop-up browser Anda untuk login.');
            return;
        }

        // Show connecting overlay
        document.getElementById('connecting-overlay').classList.add('show');

        // Redirect popup after slight delay
        setTimeout(() => {
            if (popup) popup.location.href = '{{ route('google.login') }}';
        }, 1200);

        // Periodically check if popup closed
        const checkTimer = setInterval(() => {
            if (popup && popup.closed) {
                clearInterval(checkTimer);
                window.location.reload();
            }
        }, 500);
    }

    // ---- Popup message listener ----
    window.addEventListener('message', function (event) {
        if (event.origin !== window.location.origin) return;
        if (event.data && event.data.type === 'google-login-response') {
            if (event.data.status === 'success') {
                window.location.href = event.data.redirect;
            } else {
                window.location.reload();
            }
        }
    });
</script>
</body>
</html>
