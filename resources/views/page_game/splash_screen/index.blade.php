@extends('layouts.game')

@section('title', 'Nuxel Games — Loading')

@push('styles')
<style>
    #page-transition-overlay {
        display: none !important;
    }

    /* ---- Main Splash Container ---- */
    .splash-container {
        position: absolute;
        top: 0; left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        padding: 50px 24px 32px;
        overflow: hidden;
        box-sizing: border-box;
        background: #0a0f1a;
        font-family: 'Press Start 2P', monospace;
        z-index: 10;
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

    .pixel-stars {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="splash-container">
    <!-- Background Glow Layer -->
    <div class="splash-glow"></div>

    <!-- Particles Canvas -->
    <canvas id="splash-particles" style="display: none;"></canvas>

    <!-- Decorative pixel stars -->
    <div class="pixel-stars"></div>

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
@endsection

@push('scripts')
<script>
    // ---- Progress Bar Animation ----
    (function () {
        var fill = document.getElementById('progress-fill');
        var percentEl = document.getElementById('percent-val');
        var statusEl = document.getElementById('status-text');

        if (!fill || !percentEl || !statusEl) return;

        var statusMessages = [
            "CONNECTING TO SERVER...",
            "LOADING PIXEL ASSETS...",
            "PREPARING ARENA PACU...",
            "GETTING JALUR READY...",
            "STARTING ENGINE...",
            "READY!"
        ];

        var start = null;
        var duration = 4600;
        var redirected = false;

        function animate(timestamp) {
            // Hentikan jika elemen sudah hilang (SPA navigasi lain)
            if (!document.getElementById('progress-fill')) return;

            if (!start) start = timestamp;
            var elapsed = timestamp - start;
            var progress = Math.min(elapsed / duration, 1);

            var percentage = Math.floor(progress * 100);
            percentEl.textContent = percentage + '%';
            fill.style.width = percentage + '%';

            var msgIndex = Math.min(Math.floor(progress * (statusMessages.length - 1)), statusMessages.length - 1);
            statusEl.textContent = statusMessages[msgIndex];

            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        }

        requestAnimationFrame(animate);

        // Navigate setelah 5 detik — gunakan Livewire.navigate agar SPA
        setTimeout(function () {
            if (redirected) return;
            redirected = true;
            if (typeof Livewire !== 'undefined' && typeof Livewire.navigate === 'function') {
                Livewire.navigate("{{ route('login') }}");
            } else if (typeof window.navigateToPage === 'function') {
                window.navigateToPage("{{ route('login') }}");
            } else {
                window.location.href = "{{ route('login') }}";
            }
        }, 5000);
    })();
</script>
@endpush
