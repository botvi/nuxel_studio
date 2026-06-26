@extends('layouts.game')

@section('title', 'Pacu Jalur — Masuk Akun')

@push('styles')
<link rel="manifest" href="/manifest.json">
<link rel="apple-touch-icon" href="/game_pacu/assets/image/ui/pwa-icon-192.png">
<style>
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
        background: #0f172a;
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
        box-shadow: 0 12px 40px rgba(0,0,0,0.5);
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
        box-shadow: 0 8px 25px rgba(0,0,0,0.4);
    }
    .google-btn:active {
        transform: translateY(1px) scale(0.98);
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }
    .google-btn-shimmer { display: none; }
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
        animation: pulse-text 1.2s ease-in-out infinite;
    }
    @keyframes pulse-text {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    /* PWA Install Alert Banner */
    .pwa-install-alert {
        position: absolute;
        top: -250px;
        left: 5%;
        width: 90%;
        background: rgba(15, 23, 42, 0.95);
        border: 3px solid #22c55e;
        border-radius: 16px;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        padding: 18px 16px;
        box-sizing: border-box;
        z-index: 1050;
        transition: top 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .pwa-install-alert.show { top: 20px; }
    .pwa-alert-header {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .pwa-alert-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        border: 2px solid #22c55e;
        object-fit: cover;
    }
    .pwa-alert-title-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
    }
    .pwa-alert-title {
        font-family: 'Press Start 2P', monospace;
        font-size: 9px;
        color: #22c55e;
        margin: 0;
    }
    .pwa-alert-desc {
        font-family: 'Pixelify Sans', monospace;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.8);
        margin: 0;
        line-height: 1.4;
    }
    .pwa-alert-buttons {
        display: flex;
        gap: 12px;
        width: 100%;
    }
    .pwa-btn {
        flex: 1;
        font-family: 'Press Start 2P', monospace;
        font-size: 8px;
        padding: 12px 0;
        border: 3px solid #000000;
        border-radius: 8px;
        cursor: pointer;
        text-align: center;
        transition: all 0.1s;
        box-shadow: 0px 4px 0px #000000;
    }
    .pwa-btn-install {
        background-color: #22c55e;
        color: white;
        text-shadow: 1.5px 1.5px 0px #000000;
    }
    .pwa-btn-install:hover { background-color: #4ade80; }
    .pwa-btn-install:active { transform: translateY(4px); box-shadow: 0px 0px 0px #000000; }
    .pwa-btn-cancel {
        background-color: #475569;
        color: #cbd5e1;
        text-shadow: 1px 1px 0px #000000;
    }
    .pwa-btn-cancel:hover { background-color: #64748b; }
    .pwa-btn-cancel:active { transform: translateY(4px); box-shadow: 0px 0px 0px #000000; }
    .pwa-ios-instructions {
        font-family: 'Pixelify Sans', monospace;
        font-size: 11px;
        color: rgba(255, 255, 255, 0.9);
        background: rgba(34, 197, 94, 0.1);
        border: 1px dashed rgba(34, 197, 94, 0.4);
        border-radius: 8px;
        padding: 10px;
        margin-top: 2px;
        display: flex;
        align-items: center;
        gap: 8px;
        line-height: 1.4;
    }
    .pwa-ios-icon {
        font-size: 18px;
        display: inline-block;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<!-- PS5 Styled Login UI -->
<div id="game-ui">
    <div class="ps5-backdrop"></div>
    <canvas id="ps5-particles" style="display: none;"></canvas>

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

    <!-- PWA Install Alert Dialog -->
    <div id="pwa-install-alert" class="pwa-install-alert">
        <div class="pwa-alert-header">
            <img src="/game_pacu/assets/image/ui/pwa-icon-192.png" alt="Icon Game" class="pwa-alert-icon">
            <div class="pwa-alert-title-group">
                <h4 class="pwa-alert-title">✦ PASANG GAME ✦</h4>
                <p class="pwa-alert-desc">Pasang game Pacu Jalur di Home Screen kamu untuk bermain lebih lancar, cepat, dan layar penuh!</p>
            </div>
        </div>
        <!-- iOS specific message (hidden by default) -->
        <div id="pwa-ios-guide" class="pwa-ios-instructions" style="display: none;">
            <span class="pwa-ios-icon">📤</span>
            <span>Ketuk tombol <strong>Bagikan (Share)</strong> di Safari lalu pilih <strong>'Tambahkan ke Layar Utama (Add to Home Screen)'</strong>.</span>
        </div>
        <div class="pwa-alert-buttons">
            <button id="pwa-btn-cancel" class="pwa-btn pwa-btn-cancel">BATAL</button>
            <button id="pwa-btn-install" class="pwa-btn pwa-btn-install">PASANG</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ---- Google Login Handler ----
    function handleGoogleLogin(e) {
        e.preventDefault();

        var width = 500, height = 650;
        var left = (window.screen.width / 2) - (width / 2);
        var top = (window.screen.height / 2) - (height / 2);
        var popup = window.open('about:blank', 'GoogleLoginPopup', 'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top + ',status=no,resizable=yes,scrollbars=yes');

        if (!popup) {
            alert('Silakan aktifkan pop-up browser Anda untuk login.');
            return;
        }

        // Show connecting overlay
        var overlay = document.getElementById('connecting-overlay');
        if (overlay) overlay.classList.add('show');

        // Redirect popup after slight delay
        setTimeout(function () {
            if (popup) popup.location.href = '{{ route('google.login') }}';
        }, 1200);

        // Periodically check if popup closed
        var checkTimer = setInterval(function () {
            if (popup && popup.closed) {
                clearInterval(checkTimer);
                if (!window._googleLoginRedirecting) {
                    window.location.reload();
                }
            }
        }, 500);

        window._googleLoginCheckTimer = checkTimer;
    }

    // ---- Popup message listener ----
    window.addEventListener('message', function (event) {
        if (event.origin !== window.location.origin) return;
        if (event.data && event.data.type === 'google-login-response') {
            if (window._googleLoginCheckTimer) {
                clearInterval(window._googleLoginCheckTimer);
                window._googleLoginCheckTimer = null;
            }

            if (event.data.status === 'success') {
                window._googleLoginRedirecting = true;
                var overlay = document.getElementById('connecting-overlay');
                if (overlay) overlay.classList.add('show');
                // Gunakan Livewire.navigate agar SPA (tidak reload penuh)
                if (typeof Livewire !== 'undefined' && typeof Livewire.navigate === 'function') {
                    Livewire.navigate(event.data.redirect);
                } else {
                    window.location.href = event.data.redirect;
                }
            } else {
                window._googleLoginRedirecting = false;
                window.location.reload();
            }
        }
    });

    // ---- PWA Service Worker & Install Prompt Logic ----
    (function () {
        var deferredPrompt;
        var pwaAlert = document.getElementById('pwa-install-alert');
        var btnInstall = document.getElementById('pwa-btn-install');
        var btnCancel = document.getElementById('pwa-btn-cancel');
        var iosGuide = document.getElementById('pwa-ios-guide');

        if (!pwaAlert || !btnInstall || !btnCancel) return;

        // Register Service Worker (hanya sekali per sesi)
        if ('serviceWorker' in navigator && !window._swRegistered) {
            window._swRegistered = true;
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js')
                    .then(function (reg) { console.log('[PWA] SW registered:', reg.scope); })
                    .catch(function (err) { console.error('[PWA] SW failed:', err); });
            });
        }

        function isInstalled() {
            return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
        }

        function isIOS() {
            return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        }

        function showPwaNotification() {
            var dismissedTime = localStorage.getItem('pwa-prompt-dismissed');
            var now = Date.now();
            if (dismissedTime && (now - parseInt(dismissedTime)) < (24 * 60 * 60 * 1000)) return;
            if (isInstalled()) return;
            setTimeout(function () { pwaAlert.classList.add('show'); }, 1500);
        }

        window.addEventListener('beforeinstallprompt', function (e) {
            e.preventDefault();
            deferredPrompt = e;
            showPwaNotification();
        });

        btnInstall.addEventListener('click', async function () {
            pwaAlert.classList.remove('show');
            if (deferredPrompt) {
                deferredPrompt.prompt();
                var result = await deferredPrompt.userChoice;
                console.log('[PWA] User choice:', result.outcome);
                deferredPrompt = null;
            } else if (isIOS()) {
                pwaAlert.classList.add('show');
                if (iosGuide) iosGuide.style.display = 'flex';
                btnInstall.style.display = 'none';
                btnCancel.textContent = 'OKE';
            }
        });

        btnCancel.addEventListener('click', function () {
            pwaAlert.classList.remove('show');
            localStorage.setItem('pwa-prompt-dismissed', Date.now().toString());
        });

        if (isIOS() && !isInstalled()) {
            showPwaNotification();
        }
    })();
</script>
@endpush
