<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Pacu Jalur — Registrasi Jalur</title>
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
        background: url('{{ asset('game_pacu/assets/image/bg/bgmenu.jpg') }}') no-repeat center center;
        background-size: cover;
        z-index: 10;
        overflow-y: auto;
        overflow-x: hidden;
        padding-bottom: 20px;
        box-sizing: border-box;
        -webkit-overflow-scrolling: touch;
    }
    /* PS5 Backdrop Glow */
    .ps5-backdrop {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: radial-gradient(circle at 50% 40%, rgba(59, 130, 246, 0.4) 0%, rgba(15, 23, 42, 0.3) 50%, rgba(5, 10, 20, 0.88) 100%);
        z-index: 1;
        pointer-events: none;
    }
    #ps5-particles {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        z-index: 2;
        pointer-events: none;
        opacity: 0.4;
    }
    /* Content */
    .register-content {
        position: relative;
        z-index: 11;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        max-width: 340px;
        padding: 16px 20px 30px;
        box-sizing: border-box;
    }
    /* Header */
    .reg-title {
        font-family: 'Press Start 2P', monospace;
        font-size: 12px;
        background: linear-gradient(180deg, #ffffff 0%, #93c5fd 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        filter: drop-shadow(0 2px 8px rgba(59, 130, 246, 0.5));
        text-align: center;
        line-height: 1.5;
        letter-spacing: 2px;
        margin-top: 60px;
        margin-bottom: 6px;
    }
    .reg-subtitle {
        font-family: 'Pixelify Sans', monospace;
        font-size: 12px;
        color: rgba(255,255,255,0.55);
        text-align: center;
        margin-bottom: 24px;
    }
    /* Main Registration Card */
    .reg-card {
        width: 100%;
        background: rgba(15, 23, 42, 0.65);
        border: 1.5px solid rgba(255,255,255,0.12);
        border-radius: 20px;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.5), 0 0 20px rgba(59, 130, 246, 0.15);
        padding: 24px 20px;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0;
        animation: cardEntrance 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    }
    @keyframes cardEntrance {
        0% { transform: scale(0.85); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    .card-section-label {
        font-family: 'Press Start 2P', monospace;
        font-size: 8px;
        color: #38bdf8;
        text-shadow: 0 0 8px rgba(56, 189, 248, 0.4);
        letter-spacing: 1px;
        margin-bottom: 16px;
        text-align: center;
    }
    /* Avatar Selection */
    .avatar-grid {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
    }
    .avatar-slot {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        border: 2.5px solid rgba(255,255,255,0.15);
        background: rgba(15, 23, 42, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
        overflow: hidden;
    }
    .avatar-slot img {
        width: 44px;
        height: 44px;
        object-fit: cover;
        image-rendering: pixelated;
        pointer-events: none;
    }
    .avatar-slot:hover {
        border-color: rgba(56, 189, 248, 0.5);
        transform: translateY(-3px) scale(1.06);
        box-shadow: 0 8px 20px rgba(0,0,0,0.4), 0 0 12px rgba(56, 189, 248, 0.2);
    }
    .avatar-slot.selected {
        border-color: #f59e0b;
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.3), 0 8px 20px rgba(0,0,0,0.4);
        transform: translateY(-2px);
        background: rgba(245, 158, 11, 0.1);
    }
    .avatar-slot.selected::after {
        content: '✓';
        position: absolute;
        top: 2px; right: 4px;
        font-size: 10px;
        color: #f59e0b;
        font-family: Arial, sans-serif;
        font-weight: bold;
    }
    /* Divider */
    .card-divider {
        width: 100%;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.12), transparent);
        margin: 4px 0 18px;
    }
    /* Name input label */
    .name-label {
        font-family: 'Press Start 2P', monospace;
        font-size: 8px;
        color: #38bdf8;
        text-shadow: 0 0 8px rgba(56, 189, 248, 0.4);
        letter-spacing: 1px;
        margin-bottom: 12px;
        text-align: center;
    }
    /* Custom Input styling */
    #jalur-name-input-visible {
        width: 100%;
        padding: 12px 16px;
        font-family: 'Pixelify Sans', monospace;
        font-size: 16px;
        font-weight: bold;
        color: #ffffff;
        background: rgba(255,255,255,0.07);
        border: 2px solid rgba(255,255,255,0.15);
        border-radius: 12px;
        outline: none;
        text-align: center;
        box-sizing: border-box;
        transition: all 0.2s ease;
        caret-color: #38bdf8;
    }
    #jalur-name-input-visible::placeholder {
        color: rgba(255,255,255,0.25);
        font-style: italic;
    }
    #jalur-name-input-visible:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15), 0 0 12px rgba(56, 189, 248, 0.2);
        background: rgba(56, 189, 248, 0.05);
    }
    #jalur-name-input-visible.error {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
        animation: shake 0.3s ease;
    }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        20% { transform: translateX(-6px); }
        60% { transform: translateX(6px); }
    }
    .warning-txt {
        font-family: 'Pixelify Sans', monospace;
        font-size: 12px;
        color: #ef4444;
        text-align: center;
        min-height: 20px;
        margin-top: 8px;
        transition: all 0.2s ease;
    }
    /* Start Button */
    .start-btn {
        width: 100%;
        margin-top: 20px;
        padding: 14px;
        font-family: 'Press Start 2P', monospace;
        font-size: 10px;
        color: #ffffff;
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        border: none;
        border-radius: 12px;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(34, 197, 94, 0.35), 0 0 0 1px rgba(34, 197, 94, 0.2);
        transition: all 0.15s cubic-bezier(0.25, 0.8, 0.25, 1);
        letter-spacing: 1px;
        position: relative;
        overflow: hidden;
    }
    .start-btn::before {
        content: '';
        position: absolute;
        top: 0; left: -100%;
        width: 50%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transform: skewX(-20deg);
        animation: shimmer-btn 2.5s infinite ease-in-out;
        animation-delay: 1s;
    }
    @keyframes shimmer-btn {
        0% { left: -100%; }
        100% { left: 200%; }
    }
    .start-btn:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 8px 25px rgba(34, 197, 94, 0.45), 0 0 20px rgba(34, 197, 94, 0.3);
    }
    .start-btn:active {
        transform: translateY(1px) scale(0.98);
        box-shadow: 0 2px 8px rgba(34, 197, 94, 0.3);
    }
    </style>
</head>
<body>

<form id="complete-register-form" action="{{ route('google.complete.register') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="user_id" value="{{ $user->id ?? '' }}">
    <input type="hidden" name="nama_jalur" id="form-nama-jalur">
    <input type="hidden" name="foto_profile" id="form-foto-profile">
    <input type="hidden" name="agree-terms" value="1">
</form>

<div id="desktop-wrapper">
    <div id="mobile-frame">
        <div id="status-bar">
            <span id="clock">00:00</span>
            <span>&#11044;&#11044;&#11044;</span>
        </div>
        <div id="game-container">
            <!-- PS5 Styled Registration UI -->
            <div id="game-ui">
                <div class="ps5-backdrop"></div>
                <canvas id="ps5-particles"></canvas>

                <div class="register-content">
                    <!-- Header -->
                    <div class="reg-title">✦ DATA DIRIMU ✦</div>
                    <div class="reg-subtitle">Tentukan identitas dan nama jaluarmu</div>

                    <!-- Registration Card -->
                    <div class="reg-card">

                        <!-- Avatar Selection -->
                        <div class="card-section-label">PILIH FOTO PROFIL</div>
                        <div class="avatar-grid">
                            <div class="avatar-slot selected" data-avatar="profil" onclick="selectAvatar(this, 'profil')">
                                <img src="{{ asset('game_pacu/assets/image/ui/profil.gif') }}" alt="Profil 1">
                            </div>
                            <div class="avatar-slot" data-avatar="profil2" onclick="selectAvatar(this, 'profil2')">
                                <img src="{{ asset('game_pacu/assets/image/ui/profil2.gif') }}" alt="Profil 2">
                            </div>
                            <div class="avatar-slot" data-avatar="profil3" onclick="selectAvatar(this, 'profil3')">
                                <img src="{{ asset('game_pacu/assets/image/ui/profil3.gif') }}" alt="Profil 3">
                            </div>
                            <div class="avatar-slot" data-avatar="profil4" onclick="selectAvatar(this, 'profil4')">
                                <img src="{{ asset('game_pacu/assets/image/ui/profil4.gif') }}" alt="Profil 4">
                            </div>
                        </div>

                        <div class="card-divider"></div>

                        <!-- Name Input -->
                        <div class="name-label">NAMA JALUAR</div>
                        <input
                            type="text"
                            id="jalur-name-input-visible"
                            placeholder="Nama Jalurmu..."
                            maxlength="32"
                            autocomplete="off"
                            spellcheck="false"
                            value="{{ old('nama_jalur') }}"
                        >
                        <div class="warning-txt" id="warning-txt">
                            @error('nama_jalur')
                                {{ $message }}
                            @enderror
                        </div>

                        <!-- Start Button -->
                        <button class="start-btn" id="start-btn" onclick="handleStart()">▶ MULAI BERMAIN</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('game_pacu/assets/js/game-layout.js') }}?v=<?= time() ?>"></script>
<script>
    // ---- Floating Particles ----
    (function () {
        const canvas = document.getElementById('ps5-particles');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let width = canvas.width = canvas.offsetWidth;
        let height = canvas.height = canvas.offsetHeight;

        const particles = [];
        for (let i = 0; i < 22; i++) {
            particles.push({
                x: Math.random() * width,
                y: Math.random() * height + height,
                size: Math.random() * 3 + 1,
                speed: Math.random() * 0.4 + 0.15,
                opacity: Math.random() * 0.35 + 0.15
            });
        }

        function animateParticles() {
            ctx.clearRect(0, 0, width, height);
            particles.forEach(p => {
                ctx.globalAlpha = p.opacity;
                ctx.fillStyle = '#93c5fd';
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

    // ---- Avatar Selection ----
    let selectedAvatar = 'profil';

    function selectAvatar(el, avatarKey) {
        document.querySelectorAll('.avatar-slot').forEach(s => s.classList.remove('selected'));
        el.classList.add('selected');
        selectedAvatar = avatarKey;

        // Bounce animation
        el.style.transform = 'scale(0.88) translateY(-2px)';
        setTimeout(() => { el.style.transform = ''; }, 150);
    }

    // ---- Input error handler ----
    const nameInput = document.getElementById('jalur-name-input-visible');
    const warningEl = document.getElementById('warning-txt');

    if (nameInput) {
        nameInput.addEventListener('input', () => {
            nameInput.classList.remove('error');
            warningEl.textContent = '';
        });
    }

    // ---- Tandai error dari server jika ada ----
    (function () {
        const serverError = warningEl ? warningEl.textContent.trim() : '';
        if (serverError && nameInput) {
            nameInput.classList.add('error');
            nameInput.focus();
        }
    })();

    // ---- Submit Handler ----
    function handleStart() {
        const nameValue = nameInput ? nameInput.value.trim() : '';

        if (!nameValue) {
            nameInput.classList.add('error');
            warningEl.textContent = '⚠️ Silakan masukkan Nama Jalurmu!';
            nameInput.focus();
            return;
        }


        // Save to localStorage
        localStorage.setItem('jalurName', nameValue);
        localStorage.setItem('selectedAvatar', selectedAvatar);
        localStorage.setItem('coins', '0');

        // Fill hidden form
        document.getElementById('form-nama-jalur').value = nameValue;
        document.getElementById('form-foto-profile').value = selectedAvatar;

        // Submit with fade
        const btn = document.getElementById('start-btn');
        btn.disabled = true;
        btn.textContent = '⏳ MENYIMPAN...';

        setTimeout(() => {
            document.getElementById('complete-register-form').submit();
        }, 300);
    }

    // Allow Enter key to submit
    if (nameInput) {
        nameInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') handleStart();
        });
    }
</script>
</body>
</html>
