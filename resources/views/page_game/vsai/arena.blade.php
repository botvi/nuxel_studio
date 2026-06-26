@extends('layouts.game')

@section('title', 'Franchise Game — Arena Pacu Jalur')

@push('styles')
<style>
    /* Phaser canvas di bawah overlay */
    #game-container canvas {
        z-index: 1;
    }
    
    .sound-btn {
        position: absolute;
        top: 16px;
        left: 50%;
        transform: translateX(-50%);
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

    .sound-btn img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        image-rendering: pixelated;
    }

    .sound-btn:hover {
        transform: translateX(-50%) scale(1.05);
    }

    .sound-btn:active {
        transform: translateX(-50%) scale(0.9);
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
        margin-top: 10px;
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
</style>
<style>
    /* ── ARENA LOADING SCREEN (VS AI) ── */
    #arena-loading-screen {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(160deg, #0a1628 0%, #0d2b1a 60%, #071a10 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        font-family: 'Press Start 2P', monospace;
        gap: 24px;
        transition: opacity 0.5s ease;
    }
    #arena-loading-screen.hidden {
        opacity: 0;
        pointer-events: none;
    }
    .arena-loading-title {
        font-size: 12px;
        color: #facc15;
        text-shadow: 0 0 20px rgba(250,204,21,0.8), 0 0 40px rgba(250,204,21,0.4);
        letter-spacing: 3px;
        animation: titleGlow2 1.5s ease-in-out infinite alternate;
        text-align: center;
    }
    @keyframes titleGlow2 {
        from { text-shadow: 0 0 10px rgba(250,204,21,0.5); }
        to   { text-shadow: 0 0 30px rgba(250,204,21,1), 0 0 60px rgba(250,204,21,0.6); }
    }
    .arena-loading-boat {
        font-size: 36px;
        animation: boatRace2 0.8s steps(2) infinite;
    }
    @keyframes boatRace2 {
        0%   { transform: translateX(-4px) rotate(-2deg); }
        50%  { transform: translateX(4px) rotate(2deg); }
        100% { transform: translateX(-4px) rotate(-2deg); }
    }
    .arena-loading-bar-wrap {
        width: 240px;
        background: rgba(255,255,255,0.07);
        border: 2px solid rgba(250,204,21,0.3);
        border-radius: 999px;
        height: 16px;
        overflow: hidden;
        box-shadow: 0 0 12px rgba(250,204,21,0.2);
    }
    #arena-loading-bar {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #eab308, #facc15, #fde68a);
        border-radius: 999px;
        transition: width 0.25s ease;
        box-shadow: 0 0 10px rgba(250,204,21,0.6);
    }
    #arena-loading-text {
        font-size: 7px;
        color: rgba(255,255,255,0.5);
        letter-spacing: 1px;
        min-height: 16px;
        text-align: center;
    }
    #arena-loading-pct {
        font-size: 10px;
        color: #facc15;
        text-shadow: 0 0 8px rgba(250,204,21,0.6);
    }
</style>
@endpush

@section('content')
{{-- ── ARENA LOADING SCREEN (VS AI) ── --}}
<div id="arena-loading-screen">
    <div class="arena-loading-title">✦ VS AI ARENA ✦</div>
    <div class="arena-loading-boat">🚣</div>
    <div class="arena-loading-bar-wrap">
        <div id="arena-loading-bar"></div>
    </div>
    <div id="arena-loading-pct">0%</div>
    <div id="arena-loading-text">Memuat arena...</div>
</div>
@php
$winsCount = auth()->user()->wins()->count();
$statusText = 'ANAK BARU';
if ($winsCount >= 100) {
    $statusText = 'PAMACU INTI';
} elseif ($winsCount >= 50) {
    $statusText = 'PAMAIN SEWA';
}
@endphp

<!-- Sound Toggle (Top Middle) -->
<button id="sound-btn" class="sound-btn" onclick="openAudioSettings()">
    <img id="sound-icon" src="/game_pacu/assets/image/ui/sound_on.png" alt="Sound">
</button>

<!-- Custom Audio Settings Modal -->
<div id="audio-settings-modal" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(2, 44, 34, 0.85); backdrop-filter: blur(6px); z-index: 200; align-items: center; justify-content: center; box-sizing: border-box;">
    <div class="audio-modal-card" style="background: #ffffff; border: 4px solid #000000; box-shadow: 6px 6px 0px #000000; border-radius: 12px; width: 85%; max-width: 300px; padding: 22px 18px; text-align: center; box-sizing: border-box; font-family: 'Press Start 2P', monospace;">
        <div class="audio-modal-title" style="font-size: 10px; color: #0d9488; margin-bottom: 20px; border-bottom: 3px dashed #000000; padding-bottom: 12px; font-weight: bold; letter-spacing: 0.5px;">✦ PENGATURAN SUARA ✦</div>
        
        <!-- BGM Toggle Row -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <span style="font-size: 8px; color: #15803d; text-align: left; text-shadow: 1px 1px 0px rgba(0,0,0,0.05);">MUSIK (BGM)</span>
            <button id="bgm-toggle-btn" onclick="toggleBGMSetting()" style="font-family: 'Press Start 2P', monospace; font-size: 8px; width: 80px; padding: 8px 0; border: 3px solid #000000; border-radius: 6px; cursor: pointer; text-shadow: 1.5px 1.5px 0px #000000; color: white; transition: all 0.1s; box-shadow: 0px 3px 0px #000000;">ON</button>
        </div>
        
        <!-- SFX Toggle Row -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <span style="font-size: 8px; color: #15803d; text-align: left; text-shadow: 1px 1px 0px rgba(0,0,0,0.05);">EFEK (SFX)</span>
            <button id="sfx-toggle-btn" onclick="toggleSFXSetting()" style="font-family: 'Press Start 2P', monospace; font-size: 8px; width: 80px; padding: 8px 0; border: 3px solid #000000; border-radius: 6px; cursor: pointer; text-shadow: 1.5px 1.5px 0px #000000; color: white; transition: all 0.1s; box-shadow: 0px 3px 0px #000000;">ON</button>
        </div>
        
        <!-- Save/Close Button -->
        <button class="pixel-btn" onclick="closeAudioSettings()" style="margin-top: 0; background-color: #22c55e; border: 3px solid #000000; box-shadow: inset 0 2px 0px rgba(255,255,255,0.4), 0px 4px 0px #000000; color: white; padding: 12px; font-size: 9px; cursor: pointer; text-transform: uppercase; width: 100%; text-shadow: 1.5px 1.5px 0px #000000;">OKE</button>
    </div>
</div>
@endsection

@push('scripts')
<script src="/game_pacu/assets/js/phaser.min.js"></script>
<script>
{
    const AI_LEVEL = {{ $level }};
    const GAME_WIDTH = 360;
    const GAME_HEIGHT = 760;

    // =====================================================
    //  HELPER — Custom Modals (Confirm & Alert)
    // =====================================================
    function showCustomConfirmModal(scene, text, onConfirm) {
        const W = scene.scale.width;
        const H = scene.scale.height;

        const overlay = scene.add.graphics();
        overlay.fillStyle(0x000000, 0.7);
        overlay.fillRect(0, 0, W, H);
        overlay.setInteractive(new Phaser.Geom.Rectangle(0, 0, W, H), Phaser.Geom.Rectangle.Contains);
        overlay.setDepth(99999);

        const dialog = scene.add.container(W / 2, H / 2);
        dialog.setDepth(100000);

        const dW = 270;
        const dH = 160;

        const dBg = scene.add.graphics();
        dBg.fillStyle(0x14532d, 0.4); // shadow
        dBg.fillRoundedRect(-dW / 2 + 5, -dH / 2 + 5, dW, dH, 16);
        dBg.fillStyle(0xffffff, 1); // white card
        dBg.lineStyle(4, 0x22c55e, 1);
        dBg.fillRoundedRect(-dW / 2, -dH / 2, dW, dH, 16);
        dBg.strokeRoundedRect(-dW / 2, -dH / 2, dW, dH, 16);
        dialog.add(dBg);

        const dTxt = scene.add.text(0, -25, text, {
            fontFamily: '"Pixelify Sans", monospace',
            fontSize: '14px',
            fontStyle: 'bold',
            color: '#15803d',
            align: 'center',
            wordWrap: { width: dW - 40 }
        }).setOrigin(0.5);
        dialog.add(dTxt);

        // Yes Button (Confirm)
        const btnYes = scene.add.container(-60, 45);
        btnYes.setSize(90, 30);
        btnYes.setInteractive({ useHandCursor: true });

        const btnYesBg = scene.add.graphics();
        btnYesBg.fillStyle(0x22c55e, 1);
        btnYesBg.lineStyle(2, 0x15803d, 1);
        btnYesBg.fillRoundedRect(-45, -15, 90, 30, 8);
        btnYesBg.strokeRoundedRect(-45, -15, 90, 30, 8);
        btnYes.add(btnYesBg);

        const btnYesTxt = scene.add.text(0, 0, 'YA', {
            fontFamily: '"Press Start 2P", monospace',
            fontSize: '9px',
            color: '#ffffff'
        }).setOrigin(0.5);
        btnYes.add(btnYesTxt);
        dialog.add(btnYes);

        // Cancel Button
        const btnNo = scene.add.container(60, 45);
        btnNo.setSize(90, 30);
        btnNo.setInteractive({ useHandCursor: true });

        const btnNoBg = scene.add.graphics();
        btnNoBg.fillStyle(0xef4444, 1);
        btnNoBg.lineStyle(2, 0x991b1b, 1);
        btnNoBg.fillRoundedRect(-45, -15, 90, 30, 8);
        btnNoBg.strokeRoundedRect(-45, -15, 90, 30, 8);
        btnNo.add(btnNoBg);

        const btnNoTxt = scene.add.text(0, 0, 'BATAL', {
            fontFamily: '"Press Start 2P", monospace',
            fontSize: '9px',
            color: '#ffffff'
        }).setOrigin(0.5);
        btnNo.add(btnNoTxt);
        dialog.add(btnNo);

        btnYes.on('pointerover', () => btnYes.setScale(1.05));
        btnYes.on('pointerout', () => btnYes.setScale(1));
        btnYes.on('pointerdown', () => {
            overlay.destroy();
            dialog.destroy();
            onConfirm();
        });

        btnNo.on('pointerover', () => btnNo.setScale(1.05));
        btnNo.on('pointerout', () => btnNo.setScale(1));
        btnNo.on('pointerdown', () => {
            overlay.destroy();
            dialog.destroy();
        });

        dialog.setScale(0);
        scene.tweens.add({
            targets: dialog,
            scaleX: 1, scaleY: 1,
            duration: 220,
            ease: 'Back.easeOut'
        });
    }

    function showCustomAlertModal(scene, text, redirectToTopup = false) {
        const W = scene.scale.width;
        const H = scene.scale.height;

        const overlay = scene.add.graphics();
        overlay.fillStyle(0x000000, 0.7);
        overlay.fillRect(0, 0, W, H);
        overlay.setInteractive(new Phaser.Geom.Rectangle(0, 0, W, H), Phaser.Geom.Rectangle.Contains);
        overlay.setDepth(99999);

        const dialog = scene.add.container(W / 2, H / 2);
        dialog.setDepth(100000);

        const dW = 270;
        const dH = 160;

        const dBg = scene.add.graphics();
        dBg.fillStyle(0x14532d, 0.4); // shadow
        dBg.fillRoundedRect(-dW / 2 + 5, -dH / 2 + 5, dW, dH, 16);
        dBg.fillStyle(0xffffff, 1); // white card
        dBg.lineStyle(4, 0x22c55e, 1);
        dBg.fillRoundedRect(-dW / 2, -dH / 2, dW, dH, 16);
        dBg.strokeRoundedRect(-dW / 2, -dH / 2, dW, dH, 16);
        dialog.add(dBg);

        const dTxt = scene.add.text(0, -20, text, {
            fontFamily: '"Pixelify Sans", monospace',
            fontSize: '14px',
            fontStyle: 'bold',
            color: '#15803d',
            align: 'center',
            wordWrap: { width: dW - 40 }
        }).setOrigin(0.5);
        dialog.add(dTxt);

        if (redirectToTopup) {
            const btnTopup = scene.add.container(-60, 45);
            btnTopup.setSize(100, 30);
            btnTopup.setInteractive({ useHandCursor: true });

            const btnTopupBg = scene.add.graphics();
            btnTopupBg.fillStyle(0xd97706, 1);
            btnTopupBg.lineStyle(2, 0x78350f, 1);
            btnTopupBg.fillRoundedRect(-50, -15, 100, 30, 8);
            btnTopupBg.strokeRoundedRect(-50, -15, 100, 30, 8);
            btnTopup.add(btnTopupBg);

            const btnTopupTxt = scene.add.text(0, 0, 'TOP UP', {
                fontFamily: '"Press Start 2P", monospace',
                fontSize: '9px',
                color: '#ffffff'
            }).setOrigin(0.5);
            btnTopup.add(btnTopupTxt);
            dialog.add(btnTopup);

            const btnClose = scene.add.container(60, 45);
            btnClose.setSize(90, 30);
            btnClose.setInteractive({ useHandCursor: true });

            const btnCloseBg = scene.add.graphics();
            btnCloseBg.fillStyle(0xef4444, 1);
            btnCloseBg.lineStyle(2, 0x991b1b, 1);
            btnCloseBg.fillRoundedRect(-45, -15, 90, 30, 8);
            btnCloseBg.strokeRoundedRect(-45, -15, 90, 30, 8);
            btnClose.add(btnCloseBg);

            const btnCloseTxt = scene.add.text(0, 0, 'TUTUP', {
                fontFamily: '"Press Start 2P", monospace',
                fontSize: '9px',
                color: '#ffffff'
            }).setOrigin(0.5);
            btnClose.add(btnCloseTxt);
            dialog.add(btnClose);

            btnTopup.on('pointerover', () => btnTopup.setScale(1.05));
            btnTopup.on('pointerout', () => btnTopup.setScale(1));
            btnTopup.on('pointerdown', () => {
                overlay.destroy();
                dialog.destroy();
                scene.cameras.main.fadeOut(300, 240, 253, 244);
                scene.cameras.main.once('camerafadeoutcomplete', () => {
                    window.navigateToPage('/topup');
                });
            });

            btnClose.on('pointerover', () => btnClose.setScale(1.05));
            btnClose.on('pointerout', () => btnClose.setScale(1));
            btnClose.on('pointerdown', () => {
                overlay.destroy();
                dialog.destroy();
            });
        } else {
            const btnClose = scene.add.container(0, 45);
            btnClose.setSize(100, 30);
            btnClose.setInteractive({ useHandCursor: true });

            const btnCloseBg = scene.add.graphics();
            btnCloseBg.fillStyle(0x22c55e, 1);
            btnCloseBg.lineStyle(2, 0x15803d, 1);
            btnCloseBg.fillRoundedRect(-50, -15, 100, 30, 8);
            btnCloseBg.strokeRoundedRect(-50, -15, 100, 30, 8);
            btnClose.add(btnCloseBg);

            const btnCloseTxt = scene.add.text(0, 0, 'OKE', {
                fontFamily: '"Press Start 2P", monospace',
                fontSize: '9px',
                color: '#ffffff'
            }).setOrigin(0.5);
            btnClose.add(btnCloseTxt);
            dialog.add(btnClose);

            btnClose.on('pointerover', () => btnClose.setScale(1.05));
            btnClose.on('pointerout', () => btnClose.setScale(1));
            btnClose.on('pointerdown', () => {
                overlay.destroy();
                dialog.destroy();
            });
        }

        dialog.setScale(0);
        scene.tweens.add({
            targets: dialog,
            scaleX: 1, scaleY: 1,
            duration: 220,
            ease: 'Back.easeOut'
        });
    }

    // =====================================================
    //  HELPER — Dynamic Pixel Art Recoloring
    // =====================================================
    function recolorCharacterImage(scene, sourceKey, customColors) {
        const sourceTexture = scene.textures.get(sourceKey);
        const sourceImage = sourceTexture.getSourceImage();

        const canvas = document.createElement('canvas');
        canvas.width = sourceImage.width;
        canvas.height = sourceImage.height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(sourceImage, 0, 0);

        const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const data = imgData.data;

        const targetHair = Phaser.Display.Color.HexStringToColor(customColors.hair);
        const targetShirt = Phaser.Display.Color.HexStringToColor(customColors.shirt);
        const targetPants = Phaser.Display.Color.HexStringToColor(customColors.pants);
        const targetPaddle = Phaser.Display.Color.HexStringToColor(customColors.paddle);

        for (let i = 0; i < data.length; i += 4) {
            const r = data[i];
            const g = data[i + 1];
            const b = data[i + 2];
            const a = data[i + 3];

            if (a < 10) continue; // transparan
            if (r < 40 && g < 40 && b < 40) continue; // outline gelap

            // 1. Rambut (Merah): R dominan dan R - G > 100
            if (r - g > 100 && r - b > 100) {
                const factor = Math.min(1.2, r / 199);
                data[i] = Math.min(255, targetHair.r * factor);
                data[i + 1] = Math.min(255, targetHair.g * factor);
                data[i + 2] = Math.min(255, targetHair.b * factor);
            }
            // 2. Celana (Hijau): G dominan dan G - R > 50
            else if (g - r > 50 && g - b > 40) {
                const factor = Math.min(1.2, g / 122);
                data[i] = Math.min(255, targetPants.r * factor);
                data[i + 1] = Math.min(255, targetPants.g * factor);
                data[i + 2] = Math.min(255, targetPants.b * factor);
            }
            // 3. Dayung (Biru): B dominan dan B - R > 80
            else if (b - r > 80 && b - g > 40) {
                const factor = Math.min(1.2, b / 203);
                data[i] = Math.min(255, targetPaddle.r * factor);
                data[i + 1] = Math.min(255, targetPaddle.g * factor);
                data[i + 2] = Math.min(255, targetPaddle.b * factor);
            }
            // 4. Baju/Badan (Abu-abu): R, G, B mirip
            else if (Math.abs(r - g) < 20 && Math.abs(g - b) < 20 && Math.abs(r - b) < 20) {
                const factor = Math.min(1.2, ((r + g + b) / 3) / 78);
                data[i] = Math.min(255, targetShirt.r * factor);
                data[i + 1] = Math.min(255, targetShirt.g * factor);
                data[i + 2] = Math.min(255, targetShirt.b * factor);
            }
        }

        ctx.putImageData(imgData, 0, 0);
        return canvas;
    }

    // =====================================================
    //  LOADING SCENE — preload semua assets dengan progress bar
    // =====================================================
    class LoadingScene extends Phaser.Scene {
        constructor() { super({ key: 'LoadingScene' }); }

        preload() {
            const bar  = document.getElementById('arena-loading-bar');
            const pct  = document.getElementById('arena-loading-pct');
            const txt  = document.getElementById('arena-loading-text');

            this.load.on('progress', (value) => {
                const p = Math.round(value * 100);
                if (bar) bar.style.width = p + '%';
                if (pct) pct.textContent = p + '%';
            });
            this.load.on('fileprogress', (file) => {
                if (txt) txt.textContent = 'Memuat: ' + file.key + '...';
            });
            this.load.on('complete', () => {
                if (bar) bar.style.width = '100%';
                if (pct) pct.textContent = '100%';
                if (txt) txt.textContent = 'Siap lawan AI! 🤖';
            });

            this.load.image('bgmenu',     '/game_pacu/assets/image/bg/bgmenu.jpg');
            this.load.image('back',       '/game_pacu/assets/image/ui/back.png');
            this.load.image('koin',       '/game_pacu/assets/image/ui/koin.png');
            this.load.image('jalur_boat', '/game_pacu/assets/image/jalur/jalur.png');

            for (let i = 1; i <= 5; i++) {
                this.load.image(`char${i}`, `/game_pacu/assets/image/char/${i}.png`);
            }
            for (let i = 1; i <= 6; i++) {
                this.load.image(`pancang${i}`, `/game_pacu/assets/image/pancang/${i}.png`);
            }
            for (let i = 1; i <= 8; i++) {
                this.load.image(`promosi${i}`, `/game_pacu/assets/image/promosi/spanduk_${i}.png`);
            }
            for (let i = 1; i <= 5; i++) {
                this.load.image(`whiteflag${i}`, `/game_pacu/assets/image/ui/whiteflag/${i}.png`);
            }

            this.load.audio('sound_321',      '/game_pacu/assets/sound/321.ogg');
            this.load.audio('sound_suporter', '/game_pacu/assets/sound/suporter.ogg');
            this.load.audio('sound_pluit',    '/game_pacu/assets/sound/pluit.ogg');
        }

        create() {
            const screen = document.getElementById('arena-loading-screen');
            if (screen) screen.classList.add('hidden');
            setTimeout(() => {
                if (screen) screen.remove();
                this.scene.start('ArenaScene');
            }, 520);
        }
    }

    // =====================================================
    //  ARENA SCENE
    // =====================================================
    class ArenaScene extends Phaser.Scene {
        constructor() { super({ key: 'ArenaScene' }); }

        preload() {
            // Kosong — semua sudah di-load oleh LoadingScene
        }

        applyRecolor(isPlayer, customColors) {
            const prefix = isPlayer ? 'player' : 'opponent';

            // Jalankan pewarnaan ulang frame 1-5
            for (let f = 1; f <= 5; f++) {
                const sourceKey = `char${f}`;
                const destKey = `${prefix}_char${f}`;

                const canvas = recolorCharacterImage(this, sourceKey, customColors);

                if (this.textures.exists(destKey)) {
                    const texture = this.textures.get(destKey);
                    const ctx = texture.getContext();
                    ctx.clearRect(0, 0, texture.width, texture.height);
                    ctx.drawImage(canvas, 0, 0);
                    texture.refresh();
                } else {
                    this.textures.addCanvas(destKey, canvas);
                }
            }

            // Buat animasi rowing
            const animKey = `${prefix}_rowing_anim`;
            if (this.anims.exists(animKey)) {
                this.anims.remove(animKey);
            }
            this.anims.create({
                key: animKey,
                frames: [
                    { key: `${prefix}_char1` },
                    { key: `${prefix}_char2` },
                    { key: `${prefix}_char3` },
                    { key: `${prefix}_char4` },
                    { key: `${prefix}_char5` }
                ],
                frameRate: 8,
                repeat: -1
            });
        }

        applyPlayerCorak(boatImg, boatGroup, dataUrl) {
            if (!dataUrl) return;

            const img = new Image();
            img.onload = () => {
                if (!this.textures) return;
                const boatSource = this.textures.get('jalur_boat').getSourceImage();
                const CORAK_SCALE = 2.3;
                const displayW = Math.round(boatSource.width * CORAK_SCALE);
                const displayH = Math.round(boatSource.height * CORAK_SCALE);

                const maskCanvas = document.createElement('canvas');
                maskCanvas.width = displayW;
                maskCanvas.height = displayH;
                const ctx = maskCanvas.getContext('2d');

                ctx.imageSmoothingEnabled = false;
                ctx.drawImage(img, 0, 0, displayW, displayH);

                // Hapus background putih
                const imageData = ctx.getImageData(0, 0, displayW, displayH);
                const data = imageData.data;
                const WHITE_THRESHOLD = 240;
                for (let i = 0; i < data.length; i += 4) {
                    const r = data[i], g = data[i + 1], b = data[i + 2];
                    if (r > WHITE_THRESHOLD && g > WHITE_THRESHOLD && b > WHITE_THRESHOLD) {
                        const brightness = Math.min(r, g, b);
                        const fade = (brightness - WHITE_THRESHOLD) / (255 - WHITE_THRESHOLD);
                        data[i + 3] = Math.round(255 * (1 - fade));
                    }
                }
                ctx.putImageData(imageData, 0, 0);

                ctx.globalCompositeOperation = 'destination-in';
                ctx.drawImage(boatSource, 0, 0, displayW, displayH);
                ctx.globalCompositeOperation = 'source-over';

                if (this.textures.exists('player_corak_texture')) {
                    this.textures.remove('player_corak_texture');
                }
                this.textures.addCanvas('player_corak_texture', maskCanvas);

                const corakSprite = this.make.image({
                    x: 0,
                    y: 15,
                    key: 'player_corak_texture',
                    add: false
                });
                corakSprite.setScale(1.0);
                corakSprite.setAlpha(0.82);
                corakSprite.setBlendMode(Phaser.BlendModes.MULTIPLY);

                boatGroup.add(corakSprite);
                if (boatImg && boatGroup.list.includes(boatImg)) {
                    const boatIdx = boatGroup.getIndex(boatImg);
                    boatGroup.moveTo(corakSprite, boatIdx + 1);
                }
            };
            img.src = dataUrl;
        }

        applyPlayerLambai(boatImg, boatGroup, dataUrl) {
            if (!dataUrl) return;

            const img = new Image();
            img.onload = () => {
                if (!this.textures) return;
                const LAMBAI_SCALE = 1.3;
                const LAMBAI_OFFSET_X = 125;
                const LAMBAI_OFFSET_Y = -18;

                const targetSize = 48;
                let w = img.width;
                let h = img.height;
                if (w > h) {
                    h = Math.round((h / w) * targetSize);
                    w = targetSize;
                } else {
                    w = Math.round((w / h) * targetSize);
                    h = targetSize;
                }

                const canvas = document.createElement('canvas');
                canvas.width = w;
                canvas.height = h;
                const ctx = canvas.getContext('2d');

                ctx.imageSmoothingEnabled = false;
                ctx.drawImage(img, 0, 0, w, h);

                // Hapus background putih
                const imageData = ctx.getImageData(0, 0, w, h);
                const data = imageData.data;
                const WHITE_THRESHOLD = 240;
                for (let i = 0; i < data.length; i += 4) {
                    const r = data[i], g = data[i + 1], b = data[i + 2];
                    if (r > WHITE_THRESHOLD && g > WHITE_THRESHOLD && b > WHITE_THRESHOLD) {
                        const brightness = Math.min(r, g, b);
                        const fade = (brightness - WHITE_THRESHOLD) / (255 - WHITE_THRESHOLD);
                        data[i + 3] = Math.round(255 * (1 - fade));
                    }
                }
                ctx.putImageData(imageData, 0, 0);

                if (this.textures.exists('player_lambai_texture')) {
                    this.textures.remove('player_lambai_texture');
                }
                this.textures.addCanvas('player_lambai_texture', canvas);

                const lambaiSprite = this.make.image({
                    x: LAMBAI_OFFSET_X,
                    y: LAMBAI_OFFSET_Y,
                    key: 'player_lambai_texture',
                    add: false
                });
                lambaiSprite.setScale(LAMBAI_SCALE);
                lambaiSprite.texture.setFilter(Phaser.Textures.FilterMode.NEAREST);

                boatGroup.add(lambaiSprite);
                if (boatImg && boatGroup.list.includes(boatImg)) {
                    const boatIdx = boatGroup.getIndex(boatImg);
                    boatGroup.moveTo(lambaiSprite, boatIdx); // Di bawah perahu
                }
                boatGroup.lambaiSprite = lambaiSprite;
            };
            img.src = dataUrl;
        }

        create() {
            const W = this.scale.width;
            const H = this.scale.height;
            const cx = W / 2;

            this.cameras.main.fadeIn(500, 240, 253, 244);

            // Sync sound mute setting
            this.sound.mute = (localStorage.getItem('sfx_muted') === 'true');

            // Stop all sounds before starting a new round to prevent overlapping
            this.sound.stopAll();

            // Cegah refresh / tutup halaman saat game sedang berjalan
            this.allowExit = false;
            this.beforeUnloadHandler = (e) => {
                if (!this.allowExit && (this.gameState === 'countdown' || this.gameState === 'racing')) {
                    e.preventDefault();
                    e.returnValue = '';
                    return '';
                }
            };
            window.addEventListener('beforeunload', this.beforeUnloadHandler);
            this.events.once('shutdown', () => {
                window.removeEventListener('beforeunload', this.beforeUnloadHandler);
            });

            // Multiplay setup
            const urlParams = new URLSearchParams(window.location.search);
            const rawRoomId = urlParams.get('room_id');
            this.isMultiplayer = !!rawRoomId;
            this.roomId = rawRoomId;
            this.currentUserId = {{ auth()->id() }};
            this.currentUserName = "{!! addslashes(auth()->user()->nama_jalur ?? auth()->user()->email) !!}";
            this.currentUserPhoto = "{!! addslashes(auth()->user()->foto_profile ?? '') !!}";
            this.opponentId = null;
            this.lastSyncTime = 0;

            if (this.isMultiplayer) {
                this.isWaitingForOpponentToLoad = true;
            } else {
                this.isWaitingForOpponentToLoad = false;
            }

            this.opponentCorakApplied = false;
            this.opponentLambaiApplied = false;
            this.opponentRecolored = false;

            // --- Load Data dari Database (lewat Controller) ---
            this.customColors = {!! json_encode($customColors) !!};
            const corakDataUrl = {!! json_encode($corakDataUrl) !!};
            const lambaiDataUrl = {!! json_encode($lambaiDataUrl) !!};

            // Warna rival/lawan (deterministik berdasarkan level number jika VS AI)
            function getLevelColors(level) {
                function hslToHex(h, s, l) {
                    l /= 100;
                    const a = s * Math.min(l, 1 - l) / 100;
                    const f = n => {
                        const k = (n + h / 30) % 12;
                        const color = l - a * Math.max(Math.min(k - 3, 9 - k, 1), -1);
                        return Math.round(255 * color).toString(16).padStart(2, '0');
                    };
                    return `#${f(0)}${f(8)}${f(4)}`;
                }

                const PALETTES = [];
                for (let i = 1; i <= 100; i++) {
                    const boatHue = (i * 37) % 360;
                    const hairHue = (i * 73 + 60) % 360;
                    const shirtHue = (i * 109 + 120) % 360;
                    const pantsHue = (i * 149 + 180) % 360;
                    const paddleHue = (i * 197 + 240) % 360;
                    const splashHue = (i * 233 + 180) % 360;

                    PALETTES.push({
                        boat: hslToHex(boatHue, 75, 40),
                        hair: hslToHex(hairHue, 85, 50),
                        shirt: hslToHex(shirtHue, 70, 50),
                        pants: hslToHex(pantsHue, 65, 45),
                        paddle: hslToHex(paddleHue, 80, 45),
                        splash: hslToHex(splashHue, 90, 75)
                    });
                }

                return PALETTES[(level - 1) % PALETTES.length];
            }

            if (!this.isMultiplayer) {
                this.opponentColors = getLevelColors(AI_LEVEL);
            } else {
                this.opponentColors = {
                    boat: '#d97706',
                    hair: '#2563eb',
                    shirt: '#ea580c',
                    pants: '#4b5563',
                    paddle: '#854d0e',
                    splash: '#a5f3fc'
                };
            }

            // --- Game States ---
            this.raceDistance = 1000;
            this.playerDistance = this.raceDistance;
            this.opponentDistance = this.raceDistance;
            this.playerSpeed = 0;
            this.opponentSpeed = 0;
            this.maxSpeed = 25;
            this.gameState = 'countdown';
            this.scrollSpeed = 0;
            this.elapsedTime = 0;
            this.pointerX = 0;
            this.pointerTime = 0;
            this.coinsEarnedThisMatch = 0;

            // --- 1. Background static & Dynamic Water ---
            const bg = this.add.image(cx, H / 2, 'bgmenu');
            const scaleX_bg = W / bg.width;
            const scaleY_bg = H / bg.height;
            bg.setScale(Math.max(scaleX_bg, scaleY_bg));
            bg.setAlpha(0.28);

            // Generate River Texture
            if (this.textures.exists('river_water')) {
                this.textures.remove('river_water');
            }
            const riverGfx = this.make.graphics({ add: false });
            riverGfx.fillStyle(0x0284c7, 1);
            riverGfx.fillRect(0, 0, 64, 64);
            riverGfx.fillStyle(0x0ea5e9, 0.4);
            riverGfx.fillRect(0, 14, 64, 3);
            riverGfx.fillRect(0, 44, 64, 3);
            riverGfx.fillStyle(0x38bdf8, 0.25);
            riverGfx.fillRect(16, 28, 32, 2);
            riverGfx.fillRect(32, 58, 20, 2);
            riverGfx.generateTexture('river_water', 64, 64);
            riverGfx.destroy();

            // Generate Water Shimmer / Glow Texture
            if (this.textures.exists('water_shimmer')) {
                this.textures.remove('water_shimmer');
            }
            const shimmerGfx = this.make.graphics({ add: false });
            shimmerGfx.fillStyle(0xffffff, 0.4);
            shimmerGfx.fillRoundedRect(5, 10, 20, 2, 1);
            shimmerGfx.fillRoundedRect(40, 25, 15, 2, 1);
            shimmerGfx.fillRoundedRect(15, 45, 25, 2, 1);
            shimmerGfx.fillStyle(0xffffff, 0.6);
            shimmerGfx.fillRect(10, 10, 5, 2);
            shimmerGfx.fillRect(45, 25, 6, 2);
            shimmerGfx.generateTexture('water_shimmer', 64, 64);
            shimmerGfx.destroy();

            // Generate Grass Bank Texture
            if (this.textures.exists('grass_bank')) {
                this.textures.remove('grass_bank');
            }
            const grassGfx = this.make.graphics({ add: false });
            grassGfx.fillStyle(0x22c55e, 1);
            grassGfx.fillRect(0, 0, 32, 32);
            grassGfx.fillStyle(0x16a34a, 0.7);
            grassGfx.fillRect(0, 0, 32, 2);
            grassGfx.fillRect(4, 8, 4, 4);
            grassGfx.fillRect(20, 24, 4, 4);
            grassGfx.generateTexture('grass_bank', 32, 32);
            grassGfx.destroy();

            // Air sungai & Rumput pinggiran
            this.riverTile = this.add.tileSprite(cx, 350, W, 300, 'river_water');

            // Lapisan pantulan cahaya (shimmer) untuk efek glow mengalir
            this.shimmerTile = this.add.tileSprite(cx, 350, W, 300, 'water_shimmer');
            this.shimmerTile.setBlendMode(Phaser.BlendModes.ADD);
            this.shimmerTile.setAlpha(0.6);

            // Animasi kelap-kelip glow air
            this.tweens.add({
                targets: this.shimmerTile,
                alpha: 0.15,
                duration: 1200,
                yoyo: true,
                repeat: -1,
                ease: 'Sine.easeInOut'
            });

            this.topBank = this.add.tileSprite(cx, 195, W, 10, 'grass_bank');
            this.bottomBank = this.add.tileSprite(cx, 505, W, 10, 'grass_bank');

            // --- 2. Pembuatan Tribun & Penonton Cheering ---
            this.spectators = [];
            const spectatorColors = [0xef4444, 0x3b82f6, 0x10b981, 0xf59e0b, 0x8b5cf6, 0xec4899, 0xf43f5e, 0x06b6d4];

            // Generate Spectator Texture
            if (this.textures.exists('spectator_crowd')) {
                this.textures.remove('spectator_crowd');
            }
            const specGfx = this.make.graphics({ add: false });
            specGfx.fillStyle(0xffdbac);
            specGfx.fillRect(4, 0, 8, 8);
            specGfx.fillStyle(0xffffff);
            specGfx.fillRect(2, 8, 12, 10);
            specGfx.generateTexture('spectator_crowd', 16, 18);
            specGfx.destroy();

            const spawnSpectators = (yPos, num) => {
                const spacing = (W + 100) / num;
                let lastBannerIdx = -100;

                for (let i = 0; i < num; i++) {
                    const x = i * spacing - 50 + Math.random() * 15;

                    const specContainer = this.add.container(x, yPos);

                    const spec = this.add.image(0, 0, 'spectator_crowd');
                    spec.setScale(1.2);
                    spec.texture.setFilter(Phaser.Textures.FilterMode.NEAREST);
                    spec.setTint(Phaser.Utils.Array.GetRandom(spectatorColors));

                    specContainer.add(spec);

                    if (yPos < 300 && Math.random() < 0.30 && (i - lastBannerIdx) > 3) {
                        lastBannerIdx = i;
                        const bannerIdx = Phaser.Math.Between(1, 8);
                        const bannerImg = this.add.image(0, 0, `promosi${bannerIdx}`);

                        const BANNER_W = 140;
                        const BANNER_H = 44;
                        bannerImg.setDisplaySize(BANNER_W, BANNER_H);

                        const bannerBg = this.add.graphics();
                        bannerBg.fillStyle(0xffffff, 1);
                        bannerBg.fillRect(-BANNER_W / 2 - 3, -BANNER_H / 2 - 3, BANNER_W + 6, BANNER_H + 6);

                        const bannerGroup = this.add.container(0, 10);
                        bannerGroup.add(bannerBg);
                        bannerGroup.add(bannerImg);

                        specContainer.add(bannerGroup);
                        specContainer.setDepth(yPos + 10);
                    } else {
                        specContainer.setDepth(yPos);
                    }

                    this.tweens.add({
                        targets: spec,
                        y: -5 - Math.random() * 4,
                        duration: 180 + Math.random() * 100,
                        yoyo: true,
                        repeat: -1,
                        delay: Math.random() * 400,
                        ease: 'Sine.easeInOut'
                    });

                    this.spectators.push({ sprite: specContainer, baseTranslateY: yPos });
                }
            };

            spawnSpectators(135, 10);
            spawnSpectators(165, 12);
            spawnSpectators(545, 12);
            spawnSpectators(575, 10);

            // Struktur Tribun Kayu
            const tribuneColor = 0xb45309;
            const tb = this.add.graphics();
            tb.fillStyle(tribuneColor, 0.9);
            tb.fillRect(0, 140, W, 8);
            tb.fillRect(0, 170, W, 8);
            tb.lineStyle(2, 0x78350f, 1);
            tb.strokeRect(0, 140, W, 8);
            tb.strokeRect(0, 170, W, 8);

            tb.fillRect(0, 550, W, 8);
            tb.fillRect(0, 580, W, 8);
            tb.strokeRect(0, 550, W, 8);
            tb.strokeRect(0, 580, W, 8);

            // --- 2.5 Pancang Pemisah di Tengah Sungai ---
            this.pancangs = [];
            const pDistances = [1000, 800, 600, 400, 200, 0];
            pDistances.forEach((dist, index) => {
                const pancangImg = this.add.image(-100, 350, `pancang${index + 1}`);
                pancangImg.texture.setFilter(Phaser.Textures.FilterMode.NEAREST);
                pancangImg.displayHeight = 140;
                pancangImg.scaleX = pancangImg.scaleY;
                pancangImg.setDepth(350);
                this.pancangs.push({ sprite: pancangImg, dist: dist, soundPlayed: false });
            });

            // --- 3. Pembentukan Kontainer Perahu Pemain & Lawan ---
            const BOAT_SCALE = 2.3;
            const ROWER_SCALE = 0.18;
            const ROWER_SPACING = 35;
            const BOAT_OFFSET_X = 0;
            const BOAT_OFFSET_Y = 15;
            const ROWER_OFFSET_X = -25;
            const ROWER_OFFSET_Y = -25;

            // A. Kontainer Pemain (Player)
            this.playerBoatGroup = this.add.container(cx, 420);
            this.playerBoatGroup.baseY = 420;
            this.playerBoatGroup.setDepth(420);
            this.applyRecolor(true, this.customColors);

            this.playerBoatImg = this.add.image(BOAT_OFFSET_X, BOAT_OFFSET_Y, 'jalur_boat');
            this.playerBoatImg.setScale(BOAT_SCALE);
            this.playerBoatImg.texture.setFilter(Phaser.Textures.FilterMode.NEAREST);
            const playerBoatColInt = Phaser.Display.Color.HexStringToColor(this.customColors.boat).color;
            this.playerBoatImg.setTint(playerBoatColInt);
            this.playerBoatGroup.add(this.playerBoatImg);

            // B. Kontainer Lawan (Opponent)
            this.opponentBoatGroup = this.add.container(cx, 280);
            this.opponentBoatGroup.baseY = 280;
            this.opponentBoatGroup.setDepth(280);
            this.applyRecolor(false, this.opponentColors);

            this.opponentBoatImg = this.add.image(BOAT_OFFSET_X, BOAT_OFFSET_Y, 'jalur_boat');
            this.opponentBoatImg.setScale(BOAT_SCALE);
            this.opponentBoatImg.texture.setFilter(Phaser.Textures.FilterMode.NEAREST);
            const oppBoatColInt = Phaser.Display.Color.HexStringToColor(this.opponentColors.boat).color;
            this.opponentBoatImg.setTint(oppBoatColInt);
            this.opponentBoatGroup.add(this.opponentBoatImg);

            // Terapkan Corak & Ekor Pemain
            this.applyPlayerCorak(this.playerBoatImg, this.playerBoatGroup, corakDataUrl);
            this.applyPlayerLambai(this.playerBoatImg, this.playerBoatGroup, lambaiDataUrl);

            // Tambahkan Teks Nama
            const pName = this.currentUserName;
            this.playerNameText = this.add.text(0, -60, pName, {
                fontFamily: '"Press Start 2P", monospace',
                fontSize: '11px',
                color: '#ffffff',
                stroke: '#15803d',
                strokeThickness: 5,
                shadow: { offsetX: 2, offsetY: 2, color: '#000', blur: 0, fill: true }
            }).setOrigin(0.5);
            this.playerBoatGroup.add(this.playerNameText);

            // Teks Status Badge Pemain
            const pStatus = "⚡ {{ $statusText }} ⚡";
            this.playerStatusText = this.add.text(0, -42, pStatus, {
                fontFamily: '"Press Start 2P", monospace',
                fontSize: '7px',
                color: '#fef3c7',
                stroke: '#15803d',
                strokeThickness: 3,
                shadow: { offsetX: 1, offsetY: 1, color: '#000', blur: 0, fill: true }
            }).setOrigin(0.5);
            this.playerBoatGroup.add(this.playerStatusText);

            let initialOppName = 'LAWAN';
            let initialOppStatus = '⚡ PAMAIN SEWA ⚡';
            if (!this.isMultiplayer) {
                if (AI_LEVEL > 80) {
                    initialOppName = 'AI MASTER';
                    initialOppStatus = '⚡ TINGKAT MASTER ⚡';
                } else if (AI_LEVEL > 50) {
                    initialOppName = 'AI HANDAL';
                    initialOppStatus = '⚡ TINGKAT HANDAL ⚡';
                } else if (AI_LEVEL > 20) {
                    initialOppName = 'AI MENENGAH';
                    initialOppStatus = '⚡ TINGKAT MENENGAH ⚡';
                } else {
                    initialOppName = 'AI PEMULA';
                    initialOppStatus = '⚡ TINGKAT PEMULA ⚡';
                }
            } else {
                initialOppStatus = 'LOADING...';
            }

            this.oppNameText = this.add.text(0, -60, initialOppName, {
                fontFamily: '"Press Start 2P", monospace',
                fontSize: '11px',
                color: '#ffffff',
                stroke: '#b45309',
                strokeThickness: 5,
                shadow: { offsetX: 2, offsetY: 2, color: '#000', blur: 0, fill: true }
            }).setOrigin(0.5);
            this.opponentBoatGroup.add(this.oppNameText);

            this.oppStatusText = this.add.text(0, -42, initialOppStatus, {
                fontFamily: '"Press Start 2P", monospace',
                fontSize: '7px',
                color: '#fef3c7',
                stroke: '#b45309',
                strokeThickness: 3,
                shadow: { offsetX: 1, offsetY: 1, color: '#000', blur: 0, fill: true }
            }).setOrigin(0.5);
            this.opponentBoatGroup.add(this.oppStatusText);

            // Buat Tekstur Splash Air programmatically
            if (this.textures.exists('water_particle_splash')) {
                this.textures.remove('water_particle_splash');
            }
            const pGfx = this.make.graphics({ add: false });
            pGfx.fillStyle(0xffffff, 1);
            pGfx.fillRect(0, 0, 8, 8);
            pGfx.generateTexture('water_particle_splash', 8, 8);
            pGfx.destroy();

            // Generate Electric Spark Texture
            if (this.textures.exists('electric_spark')) {
                this.textures.remove('electric_spark');
            }
            const sparkGfx = this.make.graphics({ add: false });
            sparkGfx.fillStyle(0xffffff, 1);
            sparkGfx.fillRect(0, 0, 10, 3);
            sparkGfx.generateTexture('electric_spark', 10, 3);
            sparkGfx.destroy();

            // Emitter Partikel Listrik Global
            this.sparkEmitter = this.add.particles(0, 0, 'electric_spark', {
                speed: { min: 100, max: 280 },
                angle: { min: 0, max: 360 },
                scale: { start: 1, end: 0 },
                alpha: { start: 1, end: 0 },
                lifespan: { min: 200, max: 400 },
                blendMode: 'ADD',
                tint: [0x00ffff, 0xffff00, 0xff00ff, 0xffffff],
                emitting: false
            });
            this.sparkEmitter.setDepth(995);

            const SPLASH_OFFSET_X = -1;
            const SPLASH_OFFSET_Y = 32;

            const makeRowersAndEmitters = (boatGroup, colorObj, isPlayer) => {
                const rowers = [];
                const emitters = [];
                const animKey = isPlayer ? 'player_rowing_anim' : 'opponent_rowing_anim';
                const offsetsX = [-ROWER_SPACING * 2, -ROWER_SPACING, 0, ROWER_SPACING, ROWER_SPACING * 2];

                const emitterList = [];
                offsetsX.forEach((offsetX) => {
                    const rowerX = BOAT_OFFSET_X + ROWER_OFFSET_X + offsetX;
                    const rowerY = BOAT_OFFSET_Y + ROWER_OFFSET_Y;

                    const rowerSprite = this.add.sprite(rowerX, rowerY, `${isPlayer ? 'player' : 'opponent'}_char1`);
                    rowerSprite.setScale(ROWER_SCALE);
                    rowerSprite.play(animKey);
                    rowerSprite.texture.setFilter(Phaser.Textures.FilterMode.NEAREST);

                    boatGroup.add(rowerSprite);
                    rowers.push(rowerSprite);
                    emitterList.push({ rowerX, rowerY, rowerSprite });
                });

                emitterList.forEach(({ rowerX, rowerY, rowerSprite }) => {
                    const emitter = this.add.particles(rowerX + SPLASH_OFFSET_X, rowerY + SPLASH_OFFSET_Y, 'water_particle_splash', {
                        speed: { min: 45, max: 115 },
                        angle: { min: 280, max: 340 },
                        scale: { start: 2.2, end: 0 },
                        lifespan: { min: 300, max: 550 },
                        gravityY: 350,
                        quantity: 2,
                        frequency: -1
                    });
                    emitter.texture.setFilter(Phaser.Textures.FilterMode.NEAREST);
                    const splashColorInt = Phaser.Display.Color.HexStringToColor(colorObj.splash).color;
                    emitter.setParticleTint(splashColorInt);

                    emitters.push(emitter);
                    boatGroup.add(emitter);

                    rowerSprite.on('animationupdate', (anim, frame) => {
                        if (frame.index === 3 || frame.index === 4) {
                            emitter.explode(12);
                        }

                        if (rowers.indexOf(rowerSprite) === 0) {
                            const bounceY = (frame.index === 1 || frame.index === 5) ? 0 :
                                (frame.index === 2 || frame.index === 4) ? 2 : 4;
                            boatGroup.y = boatGroup.baseY + bounceY;

                            if (boatGroup.lambaiSprite) {
                                boatGroup.lambaiSprite.angle = 0;
                            }
                        }
                    });
                });

                return { rowers, emitters };
            };

            this.playerRowers = makeRowersAndEmitters(this.playerBoatGroup, this.customColors, true);
            this.opponentRowers = makeRowersAndEmitters(this.opponentBoatGroup, this.opponentColors, false);

            // --- 5. PROGRESS BAR BALAPAN ---
            const progressContainer = this.add.container(cx, 68);
            const pbWidth = 180;
            const pbHeight = 24;

            const pbBg = this.add.graphics();
            pbBg.fillStyle(0xffffff, 0.22);
            pbBg.lineStyle(2, 0xffffff, 0.45);
            pbBg.fillRoundedRect(-pbWidth / 2, -pbHeight / 2, pbWidth, pbHeight, 6);
            pbBg.strokeRoundedRect(-pbWidth / 2, -pbHeight / 2, pbWidth, pbHeight, 6);
            progressContainer.add(pbBg);

            const track = this.add.graphics();
            track.fillStyle(0x15803d, 0.5);
            track.fillRect(-pbWidth / 2 + 10, -2, pbWidth - 20, 4);
            progressContainer.add(track);

            const finishMarker = this.add.text(-pbWidth / 2 + 14, 0, '🏁', {
                fontSize: '11px'
            }).setOrigin(0.5);
            progressContainer.add(finishMarker);

            this.playerMarker = this.add.text(pbWidth / 2 - 10, -1, 'P', {
                fontFamily: '"Press Start 2P", monospace',
                fontSize: '9px',
                color: '#ffffff',
                stroke: '#15803d',
                strokeThickness: 3
            }).setOrigin(0.5);

            this.opponentMarker = this.add.text(pbWidth / 2 - 10, -1, 'L', {
                fontFamily: '"Press Start 2P", monospace',
                fontSize: '9px',
                color: '#f59e0b',
                stroke: '#78350f',
                strokeThickness: 3
            }).setOrigin(0.5);

            progressContainer.add(this.playerMarker);
            progressContainer.add(this.opponentMarker);

            // --- 6. UI KONTROL & PENGUKUR KECEPATAN ---
            const barContainer = this.add.container(cx, 540);
            barContainer.setDepth(2000);

            const barGfx = this.add.graphics();
            barGfx.fillStyle(0xef4444, 1);
            barGfx.fillRect(-100, -7, 40, 14);
            barGfx.fillRect(60, -7, 40, 14);
            barGfx.fillStyle(0xf59e0b, 1);
            barGfx.fillRect(-60, -7, 40, 14);
            barGfx.fillRect(20, -7, 40, 14);
            barGfx.fillStyle(0x22c55e, 1);
            barGfx.fillRect(-20, -7, 40, 14);
            barGfx.lineStyle(2, 0xffffff, 1);
            barGfx.strokeRect(-100, -7, 200, 14);
            barContainer.add(barGfx);

            this.pointerGfx = this.add.graphics();
            this.pointerGfx.fillStyle(0xffffff, 1);
            this.pointerGfx.lineStyle(1.5, 0x000000, 1);
            this.pointerGfx.fillTriangle(0, -7, -5, -15, 5, -15);
            this.pointerGfx.strokeTriangle(0, -7, -5, -15, 5, -15);
            barContainer.add(this.pointerGfx);

            const paddleBtnY = 590;
            const paddleBtn = this.add.container(cx, paddleBtnY);
            paddleBtn.setDepth(2000);
            const pBtnW = 140;
            const pBtnH = 38;

            const pBtnGfx = this.add.graphics();
            const drawPaddleBtn = (hovered) => {
                pBtnGfx.clear();
                pBtnGfx.fillStyle(hovered ? 0x15803d : 0x22c55e, 1);
                pBtnGfx.lineStyle(3, 0x14532d, 1);
                pBtnGfx.fillStyle(0x14532d, 0.22);
                pBtnGfx.fillRoundedRect(-pBtnW / 2 + 3, -pBtnH / 2 + 3, pBtnW, pBtnH, 8);
                pBtnGfx.fillStyle(hovered ? 0x15803d : 0x22c55e, 1);
                pBtnGfx.fillRoundedRect(-pBtnW / 2, -pBtnH / 2, pBtnW, pBtnH, 8);
                pBtnGfx.strokeRoundedRect(-pBtnW / 2, -pBtnH / 2, pBtnW, pBtnH, 8);
            };
            drawPaddleBtn(false);
            paddleBtn.add(pBtnGfx);

            const pBtnTxt = this.add.text(0, 0, 'KAYUAH (TAP)', {
                fontFamily: '"Press Start 2P", monospace',
                fontSize: '10px',
                color: '#ffffff',
                fontStyle: 'bold',
                stroke: '#14532d',
                strokeThickness: 3
            }).setOrigin(0.5);
            paddleBtn.add(pBtnTxt);

            paddleBtn.setSize(pBtnW, pBtnH);
            paddleBtn.setInteractive({ useHandCursor: true });

            paddleBtn.on('pointerover', () => drawPaddleBtn(true));
            paddleBtn.on('pointerout', () => drawPaddleBtn(false));
            paddleBtn.on('pointerdown', () => {
                if (this.gameState !== 'racing') return;

                this.tweens.add({
                    targets: paddleBtn,
                    scaleX: 0.9, scaleY: 0.9,
                    duration: 50, yoyo: true
                });

                const absX = Math.abs(this.pointerX);
                let speedBoost = 0;
                let feedbackStr = "";
                let tintTop = 0xffffff;
                let tintBottom = 0xffffff;
                let feedbackStroke = "";

                if (absX <= 20) {
                    speedBoost = 5;
                    feedbackStr = "BAKABUUIKKK!!";
                    tintTop = 0xffea00;
                    tintBottom = 0xff0055;
                    feedbackStroke = "#4c0519";
                    this.cameras.main.shake(100, 0.004);
                    this.playerRowers.emitters.forEach(emitter => {
                        emitter.explode(22);
                    });
                    this.spawnCoinRewardAnimation();
                } else if (absX <= 60) {
                    speedBoost = 2;
                    feedbackStr = "KAYUAHHHHH!";
                    tintTop = 0x00ffff;
                    tintBottom = 0x0000ff;
                    feedbackStroke = "#0f172a";
                    this.cameras.main.shake(60, 0.002);
                    this.playerRowers.emitters.forEach(emitter => {
                        emitter.explode(10);
                    });
                } else {
                    speedBoost = 0;
                    feedbackStr = "LOMAHHHH!";
                    tintTop = 0xff0000;
                    tintBottom = 0x440000;
                    feedbackStroke = "#000000";
                }

                this.playerSpeed = Math.min(this.maxSpeed, this.playerSpeed + speedBoost);
                this.showFeedbackText(feedbackStr, tintTop, tintBottom, feedbackStroke);

                if (this.isMultiplayer && this.ws && this.ws.readyState === WebSocket.OPEN) {
                    this.ws.send(JSON.stringify({
                        type: 'game_state_sync',
                        roomId: this.roomId,
                        payload: {
                            speed: this.playerSpeed,
                            distance: this.playerDistance,
                            isTapped: true,
                            feedback: feedbackStr,
                            feedbackStroke: feedbackStroke,
                            tintTop: tintTop,
                            tintBottom: tintBottom
                        }
                    }));
                }
            });

            // Panel Speedometer
            const speedContainer = this.add.container(cx, 114);
            const sBg = this.add.graphics();
            sBg.fillStyle(0xffffff, 0.22);
            sBg.lineStyle(1.5, 0xffffff, 0.35);
            sBg.fillRoundedRect(-70, -11, 140, 22, 6);
            sBg.strokeRoundedRect(-70, -11, 140, 22, 6);
            speedContainer.add(sBg);

            this.speedText = this.add.text(0, 0, 'SPEED: 0 km/h', {
                fontFamily: '"Press Start 2P", monospace',
                fontSize: '7px',
                color: '#ffffff',
                stroke: '#15803d',
                strokeThickness: 2
            }).setOrigin(0.5);
            speedContainer.add(this.speedText);

            // --- 7. TOMBOL KEMBALI & KOIN HEADER ---
            const backBtnContainer = this.add.container(38, 34);
            backBtnContainer.setSize(48, 48);
            backBtnContainer.setInteractive({ useHandCursor: true });

            const backBtnBg = this.add.graphics();
            const drawBackBtnBg = (fillAlpha, lineAlpha, lineWidth) => {
                backBtnBg.clear();
                if (this.isMultiplayer) return;
                backBtnBg.fillStyle(0xffffff, fillAlpha);
                backBtnBg.lineStyle(lineWidth, 0xffffff, lineAlpha);
                backBtnBg.fillRoundedRect(-24, -24, 48, 48, 8);
                backBtnBg.strokeRoundedRect(-24, -24, 48, 48, 8);
            };
            drawBackBtnBg(0.2, 0.4, 2);
            backBtnContainer.add(backBtnBg);

            if (this.isMultiplayer) {
                if (!this.anims.exists('whiteflag_anim')) {
                    this.anims.create({
                        key: 'whiteflag_anim',
                        frames: [
                            { key: 'whiteflag1' },
                            { key: 'whiteflag2' },
                            { key: 'whiteflag3' },
                            { key: 'whiteflag4' },
                            { key: 'whiteflag5' }
                        ],
                        frameRate: 6,
                        repeat: -1
                    });
                }

                const flagSprite = this.add.sprite(0, 0, 'whiteflag1');
                flagSprite.setDisplaySize(25, 36);
                flagSprite.texture.setFilter(Phaser.Textures.FilterMode.NEAREST);
                flagSprite.play('whiteflag_anim');
                backBtnContainer.add(flagSprite);
            } else {
                const backIcon = this.add.image(0, 0, 'back').setDisplaySize(32, 32);
                backBtnContainer.add(backIcon);
            }

            backBtnContainer.on('pointerdown', () => {
                this.tweens.add({
                    targets: backBtnContainer,
                    scaleX: 0.8, scaleY: 0.8,
                    duration: 80, ease: 'Power2',
                    yoyo: true,
                    onComplete: () => {
                        if (this.isMultiplayer) {
                            showCustomConfirmModal(this, "Apakah Anda yakin ingin menyerah?\nLawan akan langsung menang.", () => {
                                this.allowExit = true;
                                if (this.ws && this.ws.readyState === WebSocket.OPEN) {
                                    this.ws.send(JSON.stringify({
                                        type: 'game_over',
                                        roomId: this.roomId,
                                        payload: { winnerId: this.opponentId }
                                    }));
                                }
                            });
                        } else {
                            this.allowExit = true;
                            this.cameras.main.fadeOut(300, 240, 253, 244);
                            this.cameras.main.once('camerafadeoutcomplete', () => {
                                window.navigateToPage(this.isMultiplayer ? '/main-menu' : '/vsai/level');
                            });
                        }
                    }
                });
            });

            backBtnContainer.on('pointerover', () => {
                drawBackBtnBg(0.35, 0.7, 2.5);
                this.tweens.add({ targets: backBtnContainer, scaleX: 1.05, scaleY: 1.05, duration: 90 });
            });
            backBtnContainer.on('pointerout', () => {
                drawBackBtnBg(0.2, 0.4, 2);
                this.tweens.add({ targets: backBtnContainer, scaleX: 1, scaleY: 1, duration: 90 });
            });

            // KOIN TOP RIGHT
            const COIN_ICON_X = W - 78;
            this.coinImg = this.add.image(COIN_ICON_X, 34, 'koin').setDisplaySize(36, 36);
            let coinCount = parseInt(localStorage.getItem('coins') || '100000');
            this.coinText = this.add.text(COIN_ICON_X + 22, 35, String(coinCount), {
                fontFamily: '"Pixelify Sans", monospace',
                fontSize: '13px',
                fontStyle: 'bold',
                color: '#FFD700',
                stroke: '#15803d',
                strokeThickness: 3
            }).setOrigin(0, 0.5);

            // --- 8. COUNTDOWN 3, 2, 1, GO! ---
            this.countdownText = this.add.text(cx, 350, this.isMultiplayer ? 'MENUNGGU LAWAN...' : 'READY?', {
                fontFamily: '"Press Start 2P", monospace',
                fontSize: this.isMultiplayer ? '11px' : '28px',
                color: '#ffffff',
                stroke: '#000000',
                strokeThickness: 8,
                align: 'center',
                shadow: { offsetX: 4, offsetY: 4, color: '#000', blur: 0, fill: true }
            }).setOrigin(0.5).setDepth(1000);

            this.countdownText.setTint(0x00ffff, 0x00ffff, 0xff00ff, 0xff00ff);

            this.tweens.add({
                targets: this.countdownText,
                angle: { from: -1, to: 1 },
                scaleX: { value: '+=0.02', duration: 60 },
                scaleY: { value: '+=0.02', duration: 60 },
                duration: 60,
                yoyo: true,
                repeat: -1,
                ease: 'Sine.easeInOut'
            });

            if (this.isMultiplayer) {
                this.initWebSocket();
            } else {
                this.startCountdownSequence();
            }
        }

        initWebSocket() {
            const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
            let wsUrl;
            if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' || window.location.hostname.startsWith('192.168.')) {
                wsUrl = `${protocol}//${window.location.hostname}:8080`;
            } else {
                wsUrl = `${protocol}//${window.location.hostname}/ws`;
            }
            this.ws = new WebSocket(wsUrl);

            this.ws.onopen = () => {
                console.log('Connected to WebSocket server from Arena');

                const colors = {!! json_encode($customColors) !!};
                const corak = {!! json_encode($corakDataUrl) !!};
                const lambai = {!! json_encode($lambaiDataUrl) !!};

                this.ws.send(JSON.stringify({
                    type: 'join',
                    roomId: this.roomId,
                    payload: {
                        userId: this.currentUserId,
                        userName: this.currentUserName,
                        customizations: {
                            colors,
                            corak,
                            lambai,
                            photo: this.currentUserPhoto,
                            statusText: "{{ $statusText }}"
                        }
                    }
                }));

                this.ws.send(JSON.stringify({
                    type: 'arena_ready',
                    roomId: this.roomId,
                    payload: {
                        userId: this.currentUserId
                    }
                }));
            };

            this.ws.onmessage = (event) => {
                const message = JSON.parse(event.data);
                const { type, payload } = message;

                if (type === 'room_update') {
                    const opponent = payload.players.find(p => parseInt(p.userId) !== this.currentUserId);
                    if (opponent) {
                        this.opponentId = parseInt(opponent.userId);

                        if (opponent.customizations && opponent.customizations.colors && !this.opponentRecolored) {
                            this.opponentRecolored = true;
                            this.opponentColors = Object.assign({}, this.opponentColors, opponent.customizations.colors);
                            this.applyRecolor(false, this.opponentColors);

                            const oppBoatColInt = Phaser.Display.Color.HexStringToColor(this.opponentColors.boat).color;
                            this.opponentBoatImg.setTint(oppBoatColInt);

                            const oppSplashColInt = Phaser.Display.Color.HexStringToColor(this.opponentColors.splash).color;
                            this.opponentRowers.emitters.forEach(emitter => {
                                emitter.setParticleTint(oppSplashColInt);
                            });
                        }

                        if (opponent.customizations && opponent.customizations.corak && !this.opponentCorakApplied) {
                            this.opponentCorakApplied = true;
                            this.applyOpponentCorak(this.opponentBoatImg, this.opponentBoatGroup, opponent.customizations.corak);
                        }

                        if (opponent.customizations && opponent.customizations.lambai && !this.opponentLambaiApplied) {
                            this.opponentLambaiApplied = true;
                            this.applyOpponentLambai(this.opponentBoatImg, this.opponentBoatGroup, opponent.customizations.lambai);
                        }

                        if (opponent.userName) {
                            this.oppNameText.setText(opponent.userName.toUpperCase());
                        }

                        if (opponent.customizations && opponent.customizations.statusText) {
                            this.oppStatusText.setText("⚡ " + opponent.customizations.statusText.toUpperCase() + " ⚡");
                        }
                    }
                }

                else if (type === 'start_countdown') {
                    if (this.isWaitingForOpponentToLoad) {
                        this.isWaitingForOpponentToLoad = false;
                        this.startCountdownSequence();
                    }
                }

                else if (type === 'opponent_sync') {
                    if (this.gameState === 'racing') {
                        this.opponentSpeed = payload.speed;
                        this.opponentDistance = payload.distance;

                        if (payload.isTapped) {
                            this.opponentRowers.emitters.forEach(emitter => {
                                emitter.explode(payload.feedback === 'BAKABUUIKKK!!' ? 22 : 10);
                            });
                        }
                    }
                }

                else if (type === 'game_finished') {
                    if (this.gameState !== 'finished') {
                        this.showRaceResult(parseInt(payload.winnerId) === this.currentUserId);
                    }
                }
            };

            this.ws.onclose = () => {
                console.log('Arena WebSocket disconnected');
            };
        }

        startCountdownSequence() {
            const cx = this.scale.width / 2;
            const H = this.scale.height;

            this.countdownText.setFontSize(28);
            this.countdownText.setText('READY?');
            this.countdownText.setTint(0x00ffff, 0x00ffff, 0xff00ff, 0xff00ff);

            let count = 3;
            const countdownTimer = this.time.addEvent({
                delay: 1000,
                loop: true,
                callback: () => {
                    if (count > 0) {
                        if (count === 3) {
                            this.sound.play('sound_321', { volume: 0.2 });
                        }
                        this.countdownText.setText(String(count));
                        this.countdownText.setTint(0xffff00, 0xffff00, 0xff6600, 0xff6600);
                        this.countdownText.setScale(0.8);
                        this.tweens.add({ targets: this.countdownText, scaleX: 1.2, scaleY: 1.2, duration: 300, ease: 'Back.easeOut' });

                        if (this.sparkEmitter) {
                            const txtW = this.countdownText.width;
                            for (let i = 0; i < 20; i++) {
                                const randX = cx + Phaser.Math.Between(-txtW / 2 - 10, txtW / 2 + 10);
                                const randY = (H / 2) + Phaser.Math.Between(-15, 15);
                                this.sparkEmitter.emitParticleAt(randX, randY);
                            }
                        }
                        count--;
                    } else if (count === 0) {
                        this.countdownText.setText('GO!!');
                        this.countdownText.setTint(0xffea00, 0xffea00, 0xff0000, 0xff0000);
                        this.countdownText.setScale(1.0);
                        this.countdownText.setStroke('#4c0519', 8);
                        this.tweens.add({ targets: this.countdownText, scaleX: 1.5, scaleY: 1.5, duration: 300, ease: 'Back.easeOut' });

                        if (this.sparkEmitter) {
                            const txtW = this.countdownText.width;
                            for (let i = 0; i < 40; i++) {
                                const randX = cx + Phaser.Math.Between(-txtW / 2 - 20, txtW / 2 + 20);
                                const randY = (H / 2) + Phaser.Math.Between(-20, 20);
                                this.sparkEmitter.emitParticleAt(randX, randY);
                            }
                        }

                        this.sound.play('sound_pluit', { volume: 0.03, loop: true });

                        this.gameState = 'racing';
                        count--;
                    } else {
                        this.countdownText.setVisible(false);
                        countdownTimer.destroy();
                    }
                }
            });
        }

        applyOpponentCorak(boatImg, boatGroup, dataUrl) {
            if (!dataUrl) return;

            const img = new Image();
            img.onload = () => {
                if (!this.textures) return;
                const boatSource = this.textures.get('jalur_boat').getSourceImage();
                const CORAK_SCALE = 2.3;
                const displayW = Math.round(boatSource.width * CORAK_SCALE);
                const displayH = Math.round(boatSource.height * CORAK_SCALE);

                const maskCanvas = document.createElement('canvas');
                maskCanvas.width = displayW;
                maskCanvas.height = displayH;
                const ctx = maskCanvas.getContext('2d');

                ctx.imageSmoothingEnabled = false;
                ctx.drawImage(img, 0, 0, displayW, displayH);

                const imageData = ctx.getImageData(0, 0, displayW, displayH);
                const data = imageData.data;
                const WHITE_THRESHOLD = 240;
                for (let i = 0; i < data.length; i += 4) {
                    const r = data[i], g = data[i + 1], b = data[i + 2];
                    if (r > WHITE_THRESHOLD && g > WHITE_THRESHOLD && b > WHITE_THRESHOLD) {
                        const brightness = Math.min(r, g, b);
                        const fade = (brightness - WHITE_THRESHOLD) / (255 - WHITE_THRESHOLD);
                        data[i + 3] = Math.round(255 * (1 - fade));
                    }
                }
                ctx.putImageData(imageData, 0, 0);

                ctx.globalCompositeOperation = 'destination-in';
                ctx.drawImage(boatSource, 0, 0, displayW, displayH);
                ctx.globalCompositeOperation = 'source-over';

                const oppCorakKey = 'opponent_corak_texture';
                if (this.textures.exists(oppCorakKey)) {
                    this.textures.remove(oppCorakKey);
                }
                this.textures.addCanvas(oppCorakKey, maskCanvas);

                const corakSprite = this.make.image({
                    x: 0,
                    y: 15,
                    key: oppCorakKey,
                    add: false
                });
                corakSprite.setScale(1.0);
                corakSprite.setAlpha(0.82);
                corakSprite.setBlendMode(Phaser.BlendModes.MULTIPLY);

                boatGroup.add(corakSprite);
                if (boatImg && boatGroup.list.includes(boatImg)) {
                    const boatIdx = boatGroup.getIndex(boatImg);
                    boatGroup.moveTo(corakSprite, boatIdx + 1);
                }
            };
            img.src = dataUrl;
        }

        applyOpponentLambai(boatImg, boatGroup, dataUrl) {
            if (!dataUrl) return;

            const img = new Image();
            img.onload = () => {
                if (!this.textures) return;
                const LAMBAI_SCALE = 1.3;
                const LAMBAI_OFFSET_X = 125;
                const LAMBAI_OFFSET_Y = -18;

                const targetSize = 48;
                let w = img.width;
                let h = img.height;
                if (w > h) {
                    h = Math.round((h / w) * targetSize);
                    w = targetSize;
                } else {
                    w = Math.round((w / h) * targetSize);
                    h = targetSize;
                }

                const canvas = document.createElement('canvas');
                canvas.width = w;
                canvas.height = h;
                const ctx = canvas.getContext('2d');

                ctx.imageSmoothingEnabled = false;
                ctx.drawImage(img, 0, 0, w, h);

                const imageData = ctx.getImageData(0, 0, w, h);
                const data = imageData.data;
                const WHITE_THRESHOLD = 240;
                for (let i = 0; i < data.length; i += 4) {
                    const r = data[i], g = data[i + 1], b = data[i + 2];
                    if (r > WHITE_THRESHOLD && g > WHITE_THRESHOLD && b > WHITE_THRESHOLD) {
                        const brightness = Math.min(r, g, b);
                        const fade = (brightness - WHITE_THRESHOLD) / (255 - WHITE_THRESHOLD);
                        data[i + 3] = Math.round(255 * (1 - fade));
                    }
                }
                ctx.putImageData(imageData, 0, 0);

                const oppLambaiKey = 'opponent_lambai_texture';
                if (this.textures.exists(oppLambaiKey)) {
                    this.textures.remove(oppLambaiKey);
                }
                this.textures.addCanvas(oppLambaiKey, canvas);

                const lambaiSprite = this.make.image({
                    x: LAMBAI_OFFSET_X,
                    y: LAMBAI_OFFSET_Y,
                    key: oppLambaiKey,
                    add: false
                });
                lambaiSprite.setScale(LAMBAI_SCALE);
                lambaiSprite.texture.setFilter(Phaser.Textures.FilterMode.NEAREST);

                boatGroup.add(lambaiSprite);
                if (boatImg && boatGroup.list.includes(boatImg)) {
                    const boatIdx = boatGroup.getIndex(boatImg);
                    boatGroup.moveTo(lambaiSprite, boatIdx);
                }
                boatGroup.lambaiSprite = lambaiSprite;
            };
            img.src = dataUrl;
        }

        showOpponentFeedbackText(txt, tintTop, tintBottom, stroke) {
            const cx = this.scale.width / 2;
            const container = this.add.container(cx, 240);
            container.setDepth(1000);

            const popText = this.add.text(
                0, 0,
                txt, {
                fontFamily: '"Press Start 2P", monospace',
                fontSize: '18px',
                color: '#ffffff',
                stroke: stroke,
                strokeThickness: 8,
                align: 'center',
                shadow: { offsetX: 3, offsetY: 3, color: '#000', blur: 0, fill: true }
            }
            ).setOrigin(0.5);

            popText.setTint(tintTop, tintTop, tintBottom, tintBottom);
            container.add(popText);

            if (this.sparkEmitter) {
                const txtW = popText.width;
                for (let i = 0; i < 30; i++) {
                    const randX = cx + Phaser.Math.Between(-txtW / 2 - 10, txtW / 2 + 10);
                    const randY = 240 + Phaser.Math.Between(-15, 15);
                    this.sparkEmitter.emitParticleAt(randX, randY);
                }
            }

            this.tweens.add({
                targets: popText,
                angle: { from: -1, to: 1 },
                scaleX: { from: 0.95, to: 1.05 },
                scaleY: { from: 0.95, to: 1.05 },
                duration: 60,
                yoyo: true,
                repeat: -1,
                ease: 'Sine.easeInOut'
            });

            this.tweens.add({
                targets: container,
                y: container.y - 65,
                alpha: 0,
                duration: 850,
                ease: 'Back.easeOut',
                onComplete: () => container.destroy()
            });
        }

        showFeedbackText(txt, tintTop, tintBottom, stroke) {
            const cx = this.scale.width / 2;
            const container = this.add.container(cx, 380);
            container.setDepth(1000);

            const popText = this.add.text(
                0, 0,
                txt, {
                fontFamily: '"Press Start 2P", monospace',
                fontSize: '18px',
                color: '#ffffff',
                stroke: stroke,
                strokeThickness: 8,
                align: 'center',
                shadow: { offsetX: 3, offsetY: 3, color: '#000', blur: 0, fill: true }
            }
            ).setOrigin(0.5);

            popText.setTint(tintTop, tintTop, tintBottom, tintBottom);
            container.add(popText);

            if (this.sparkEmitter) {
                const txtW = popText.width;
                for (let i = 0; i < 30; i++) {
                    const randX = cx + Phaser.Math.Between(-txtW / 2 - 10, txtW / 2 + 10);
                    const randY = 380 + Phaser.Math.Between(-15, 15);
                    this.sparkEmitter.emitParticleAt(randX, randY);
                }
            }

            this.tweens.add({
                targets: popText,
                angle: { from: -1, to: 1 },
                scaleX: { from: 0.95, to: 1.05 },
                scaleY: { from: 0.95, to: 1.05 },
                duration: 60,
                yoyo: true,
                repeat: -1,
                ease: 'Sine.easeInOut'
            });

            this.tweens.add({
                targets: container,
                y: container.y - 65,
                alpha: 0,
                duration: 850,
                ease: 'Back.easeOut',
                onComplete: () => container.destroy()
            });
        }

        spawnCoinRewardAnimation() {
            if (!this.isMultiplayer) return;
            if ((this.coinsEarnedThisMatch || 0) >= 5) return;
            if (Math.random() >= 0.4) return;

            const W = this.scale.width;
            const H = this.scale.height;

            const startX = Phaser.Math.Between(40, W - 40);
            const startY = Phaser.Math.Between(H / 2 - 100, H - 250);

            const tempCoin = this.add.image(startX, startY, 'koin');
            tempCoin.setDisplaySize(24, 24);
            tempCoin.texture.setFilter(Phaser.Textures.FilterMode.NEAREST);
            tempCoin.setDepth(3000);

            const COIN_ICON_X = W - 78;
            const targetX = COIN_ICON_X;
            const targetY = 34;

            this.tweens.add({
                targets: tempCoin,
                x: targetX,
                y: targetY,
                displayWidth: 36,
                displayHeight: 36,
                duration: 800,
                ease: 'Quad.easeOut',
                onComplete: () => {
                    tempCoin.destroy();

                    let currentCoins = parseInt(localStorage.getItem('coins') || '100000');
                    currentCoins += 1;
                    localStorage.setItem('coins', String(currentCoins));
                    this.coinText.setText(String(currentCoins));

                    this.coinsEarnedThisMatch = (this.coinsEarnedThisMatch || 0) + 1;

                    this.tweens.add({
                        targets: this.coinImg,
                        displayWidth: 48,
                        displayHeight: 48,
                        duration: 100,
                        yoyo: true,
                        onComplete: () => {
                            this.coinImg.setDisplaySize(36, 36);
                        }
                    });
                    this.tweens.add({
                        targets: this.coinText,
                        scaleX: 1.25,
                        scaleY: 1.25,
                        duration: 100,
                        yoyo: true
                    });
                }
            });
        }

        showRaceResult(isWinner) {
            this.gameState = 'finished';
            this.playerSpeed = 0;
            this.opponentSpeed = 0;

            this.sound.stopByKey('sound_pluit');

            let coinsReward = 0;

            if (!this.isMultiplayer) {
                if (isWinner) {
                    coinsReward = AI_LEVEL * 5;

                    fetch('/vsai/add-coins', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            coins: coinsReward
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        console.log('VS AI Coins updated in DB:', data);
                    })
                    .catch(err => console.error('Failed to update VS AI coins in DB:', err));

                    let currentCoins = parseInt(localStorage.getItem('coins') || '100000');
                    currentCoins += coinsReward;
                    localStorage.setItem('coins', String(currentCoins));
                    this.coinText.setText(String(currentCoins));

                    const currentUnlocked = parseInt(localStorage.getItem('vsai_unlocked') || '1');
                    if (AI_LEVEL >= currentUnlocked) {
                        localStorage.setItem('vsai_unlocked', String(AI_LEVEL + 1));
                    }
                }
            } else {
                coinsReward = this.coinsEarnedThisMatch || 0;
                fetch('/arena-pacu/add-coins', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        coins: coinsReward
                    })
                })
                .then(res => res.json())
                .then(data => {
                    console.log('Coins updated in DB:', data);
                })
                .catch(err => console.error('Failed to update coins in DB:', err));

                fetch('/room/finish', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        room_id: this.roomId.replace('room_', ''),
                        winner_id: isWinner ? this.currentUserId : this.opponentId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    console.log('Match successfully stored in DB:', data);
                })
                .catch(err => console.error('Failed to save match to DB:', err));
            }

            const W = this.scale.width;
            const H = this.scale.height;
            const cx = W / 2;

            this.playerRowers.rowers.forEach(r => r.stop());
            this.opponentRowers.rowers.forEach(r => r.stop());

            this.children.list.forEach(child => {
                if (child.texture && child.texture.key === 'bgmenu') return;
                child.setVisible(false);
            });

            const overlay = this.add.graphics();
            overlay.fillStyle(0x042f1a, 0.62);
            overlay.fillRect(0, 0, W, H);
            overlay.setAlpha(0);
            this.tweens.add({ targets: overlay, alpha: 1, duration: 450 });

            const modal = this.add.container(cx, H / 2);
            modal.setScale(0);

            const modalW = W - 50;
            const modalH = 290;
            const modalBg = this.add.graphics();

            modalBg.fillStyle(0x14532d, 0.28);
            modalBg.fillRoundedRect(-modalW / 2 + 6, -modalH / 2 + 6, modalW, modalH, 16);

            modalBg.fillStyle(0xffffff, 0.95);
            modalBg.lineStyle(5, isWinner ? 0x22c55e : 0xef4444, 1);
            modalBg.fillRoundedRect(-modalW / 2, -modalH / 2, modalW, modalH, 16);
            modalBg.strokeRoundedRect(-modalW / 2, -modalH / 2, modalW, modalH, 16);
            modal.add(modalBg);

            const titleStr = !this.isMultiplayer
                ? (isWinner ? 'LEVEL SELESAI!' : 'COBA LAGI!')
                : (isWinner ? '✦ KEMENANGAN ✦' : '✦ KEKALAHAN ✦');
            const titleCol = isWinner ? '#16a34a' : '#dc2626';
            const strokeCol = isWinner ? '#dcfce7' : '#fee2e2';

            const titleTxt = this.add.text(0, -modalH / 2 + 35, titleStr, {
                fontFamily: '"Press Start 2P", monospace',
                fontSize: '14px',
                color: titleCol,
                fontStyle: 'bold',
                stroke: strokeCol,
                strokeThickness: 3
            }).setOrigin(0.5);
            modal.add(titleTxt);

            const msgStr = isWinner
                ? "Hebat! Kamu berhasil\nmenjadi yang tercepat!"
                : "Sayang sekali!\nLawan mendahuluimu.";

            const msgTxt = this.add.text(0, -25, msgStr, {
                fontFamily: '"Pixelify Sans", monospace',
                fontSize: '14px',
                fontStyle: 'bold',
                color: '#374151',
                align: 'center',
                lineSpacing: 4
            }).setOrigin(0.5);
            modal.add(msgTxt);

            const coinGroup = this.add.container(0, 32);
            modal.add(coinGroup);
            if (isWinner) {
                const coinIcon = this.add.image(-25, 0, 'koin').setDisplaySize(28, 28);
                const rewardTxt = this.add.text(10, 0, `+${coinsReward} KP`, {
                    fontFamily: '"Press Start 2P", monospace',
                    fontSize: '12px',
                    color: '#d97706',
                    fontStyle: 'bold'
                }).setOrigin(0.5);
                coinGroup.add(coinIcon);
                coinGroup.add(rewardTxt);
            } else {
                coinGroup.setVisible(false);
            }

            const btnW = 110;
            const btnH = 34;
            const btnY = modalH / 2 - 45;

            // Tombol Main Lagi
            const btnRetry = this.add.container(-60, btnY);
            modal.add(btnRetry);

            const btnR_gfx = this.add.graphics();
            const drawBtnR = (hovered) => {
                btnR_gfx.clear();
                btnR_gfx.fillStyle(hovered ? 0x15803d : 0x22c55e, 1);
                btnR_gfx.lineStyle(2, 0x14532d, 1);
                btnR_gfx.fillRoundedRect(-btnW / 2, -btnH / 2, btnW, btnH, 6);
                btnR_gfx.strokeRoundedRect(-btnW / 2, -btnH / 2, btnW, btnH, 6);
            };
            drawBtnR(false);
            btnRetry.add(btnR_gfx);

            const btnR_txt = this.add.text(0, 0, !this.isMultiplayer ? (isWinner ? 'LANJUT' : 'COBA LAGI') : 'LAGI', {
                fontFamily: '"Press Start 2P", monospace',
                fontSize: !this.isMultiplayer ? '6.5px' : '7.5px',
                color: '#ffffff',
                stroke: '#14532d',
                strokeThickness: 2
            }).setOrigin(0.5);
            btnRetry.add(btnR_txt);

            btnRetry.setSize(btnW, btnH);
            btnRetry.setInteractive({ useHandCursor: true });
            btnRetry.on('pointerover', () => drawBtnR(true));
            btnRetry.on('pointerout', () => drawBtnR(false));
            btnRetry.on('pointerdown', () => {
                this.tweens.add({
                    targets: btnRetry,
                    scaleX: 0.9, scaleY: 0.9,
                    duration: 60, yoyo: true,
                    onComplete: () => {
                        this.allowExit = true;
                        if (this.isMultiplayer) {
                            window.navigateToPage('/room');
                        } else {
                            if (isWinner) {
                                if (AI_LEVEL < 100) {
                                    window.navigateToPage(`/vsai/arena?level=${AI_LEVEL + 1}`);
                                } else {
                                    window.navigateToPage('/vsai/level');
                                }
                            } else {
                                this.scene.restart();
                            }
                        }
                    }
                });
            });

            // Tombol Kembali
            const btnMenu = this.add.container(60, btnY);
            modal.add(btnMenu);

            const btnM_gfx = this.add.graphics();
            const drawBtnM = (hovered) => {
                btnM_gfx.clear();
                btnM_gfx.fillStyle(hovered ? 0x4b5563 : 0x6b7280, 1);
                btnM_gfx.lineStyle(2, 0x374151, 1);
                btnM_gfx.fillRoundedRect(-btnW / 2, -btnH / 2, btnW, btnH, 6);
                btnM_gfx.strokeRoundedRect(-btnW / 2, -btnH / 2, btnW, btnH, 6);
            };
            drawBtnM(false);
            btnMenu.add(btnM_gfx);

            const btnM_txt = this.add.text(0, 0, !this.isMultiplayer ? 'PILIH LEVEL' : 'MENU', {
                fontFamily: '"Press Start 2P", monospace',
                fontSize: !this.isMultiplayer ? '6.5px' : '7.5px',
                color: '#ffffff',
                stroke: '#374151',
                strokeThickness: 2
            }).setOrigin(0.5);
            btnMenu.add(btnM_txt);

            btnMenu.setSize(btnW, btnH);
            btnMenu.setInteractive({ useHandCursor: true });
            btnMenu.on('pointerover', () => drawBtnM(true));
            btnMenu.on('pointerout', () => drawBtnM(false));
            btnMenu.on('pointerdown', () => {
                this.tweens.add({
                    targets: btnMenu,
                    scaleX: 0.9, scaleY: 0.9,
                    duration: 60, yoyo: true,
                    onComplete: () => {
                        this.allowExit = true;
                        this.cameras.main.fadeOut(300, 240, 253, 244);
                        this.cameras.main.once('camerafadeoutcomplete', () => {
                            window.navigateToPage(!this.isMultiplayer ? '/vsai/level' : '/main-menu');
                        });
                    }
                });
            });

            this.tweens.add({
                targets: modal,
                scaleX: 1, scaleY: 1,
                duration: 500,
                ease: 'Back.easeOut'
            });
        }

        update(time, delta) {
            const W = this.scale.width;

            if (this.gameState === 'racing') {
                this.pointerTime += delta;
                this.pointerX = Math.sin(this.pointerTime / 250) * 100;
                this.pointerGfx.x = this.pointerX;

                if (!this.isMultiplayer) {
                    this.opponentSpeed = Math.min(13.0, 4.0 + (AI_LEVEL - 1) * 0.09);
                }

                let baseSpeed = this.isMultiplayer ? 5.0 : this.opponentSpeed;
                if (this.playerSpeed > baseSpeed) {
                    this.playerSpeed = Math.max(baseSpeed, this.playerSpeed - 0.06);
                } else {
                    this.playerSpeed = baseSpeed;
                }

                this.playerDistance = Math.max(0, this.playerDistance - this.playerSpeed * (delta / 1000));
                this.opponentDistance = Math.max(0, this.opponentDistance - this.opponentSpeed * (delta / 1000));

                this.pancangs.forEach(p => {
                    if (!p.soundPlayed && this.playerDistance <= p.dist) {
                        p.soundPlayed = true;
                        this.sound.play('sound_suporter', { volume: 0.03 });
                    }
                });

                this.scrollSpeed = this.playerSpeed * 0.3;

                this.speedText.setText(`SPEED: ${Math.round(this.playerSpeed * 3.6)} km/h`);

                this.playerRowers.rowers.forEach(r => {
                    if (!r.anims.isPlaying) r.play('player_rowing_anim');
                    r.anims.timeScale = 0.8 + (this.playerSpeed / 10);
                });

                if (this.opponentSpeed > 0) {
                    this.opponentRowers.rowers.forEach(r => {
                        if (!r.anims.isPlaying) r.play('opponent_rowing_anim');
                        r.anims.timeScale = this.opponentSpeed / 8.5;
                    });
                }

                if (this.isMultiplayer && this.ws && this.ws.readyState === WebSocket.OPEN) {
                    if (time - this.lastSyncTime > 100) {
                        this.lastSyncTime = time;
                        this.ws.send(JSON.stringify({
                            type: 'game_state_sync',
                            roomId: this.roomId,
                            payload: {
                                speed: this.playerSpeed,
                                distance: this.playerDistance,
                                isTapped: false
                            }
                        }));
                    }
                }
            }

            const distDiff = this.playerDistance - this.opponentDistance;
            const visualOffsetScale = 6;
            const targetOpponentX = (W / 2) - distDiff * visualOffsetScale;

            this.opponentBoatGroup.x = targetOpponentX;

            this.playerRowers.emitters.forEach(emitter => {
                emitter.setParticleSpeed(45 + this.playerSpeed * 6, 60 + this.playerSpeed * 10);
            });

            this.spectators.forEach(specData => {
                specData.sprite.x += this.scrollSpeed;
                if (specData.sprite.x > W + 80) {
                    specData.sprite.x = -60 - Math.random() * 30;
                }
            });

            const DISTANCE_TO_PIXELS = 18;
            const MONCONG_OFFSET = 130;
            this.pancangs.forEach(p => {
                p.sprite.x = (W / 2) - MONCONG_OFFSET - (this.playerDistance - p.dist) * DISTANCE_TO_PIXELS;
            });

            this.riverTile.tilePositionX -= this.scrollSpeed;

            this.shimmerTile.tilePositionX -= (this.scrollSpeed * 1.3 + 0.4);
            this.shimmerTile.tilePositionY += 0.15;

            const barStart = 80;
            const barLength = -140;

            const playerProgress = 1 - (this.playerDistance / this.raceDistance);
            this.playerMarker.x = barStart + playerProgress * barLength;

            const opponentProgress = 1 - (this.opponentDistance / this.raceDistance);
            this.opponentMarker.x = barStart + opponentProgress * barLength;

            if (this.gameState === 'racing') {
                if (this.playerDistance <= 0) {
                    if (this.isMultiplayer) {
                        if (this.ws && this.ws.readyState === WebSocket.OPEN) {
                            this.ws.send(JSON.stringify({
                                type: 'game_over',
                                roomId: this.roomId,
                                payload: { winnerId: this.currentUserId }
                            }));
                        }
                    } else {
                        this.showRaceResult(true);
                    }
                } else if (!this.isMultiplayer && this.opponentDistance <= 0) {
                    this.showRaceResult(false);
                }
            }
        }
    }

    // Sound & BGM Management
    (() => {
        if (localStorage.getItem('bgm_muted') === null) {
            localStorage.setItem('bgm_muted', 'false');
        }
        if (localStorage.getItem('sfx_muted') === null) {
            localStorage.setItem('sfx_muted', 'false');
        }

        updateSoundIcon();
    })();

    function updateSoundIcon() {
        const bgmMuted = localStorage.getItem('bgm_muted') === 'true';
        const sfxMuted = localStorage.getItem('sfx_muted') === 'true';
        const soundIcon = document.getElementById('sound-icon');
        if (soundIcon) {
            if (bgmMuted && sfxMuted) {
                soundIcon.src = '/game_pacu/assets/image/ui/sound_off.png';
            } else {
                soundIcon.src = '/game_pacu/assets/image/ui/sound_on.png';
            }
        }
    }

    function openAudioSettings() {
        const modal = document.getElementById('audio-settings-modal');
        if (modal) {
            modal.style.display = 'flex';
            syncAudioModalButtons();
        }
    }

    function closeAudioSettings() {
        const modal = document.getElementById('audio-settings-modal');
        if (modal) modal.style.display = 'none';
    }

    function syncAudioModalButtons() {
        const bgmMuted = localStorage.getItem('bgm_muted') === 'true';
        const sfxMuted = localStorage.getItem('sfx_muted') === 'true';
        
        const bgmBtn = document.getElementById('bgm-toggle-btn');
        const sfxBtn = document.getElementById('sfx-toggle-btn');
        
        if (bgmBtn) {
            if (bgmMuted) {
                bgmBtn.textContent = 'OFF';
                bgmBtn.style.backgroundColor = '#ef4444';
                bgmBtn.style.boxShadow = '0px 3px 0px #991b1b';
            } else {
                bgmBtn.textContent = 'ON';
                bgmBtn.style.backgroundColor = '#22c55e';
                bgmBtn.style.boxShadow = '0px 3px 0px #15803d';
            }
        }
        
        if (sfxBtn) {
            if (sfxMuted) {
                sfxBtn.textContent = 'OFF';
                sfxBtn.style.backgroundColor = '#ef4444';
                sfxBtn.style.boxShadow = '0px 3px 0px #991b1b';
            } else {
                sfxBtn.textContent = 'ON';
                sfxBtn.style.backgroundColor = '#22c55e';
                sfxBtn.style.boxShadow = '0px 3px 0px #15803d';
            }
        }
        
        updateSoundIcon();
    }

    function toggleBGMSetting() {
        const bgmMuted = localStorage.getItem('bgm_muted') === 'true';
        localStorage.setItem('bgm_muted', bgmMuted ? 'false' : 'true');
        syncAudioModalButtons();
    }

    function toggleSFXSetting() {
        const sfxMuted = localStorage.getItem('sfx_muted') === 'true';
        const nextMuted = !sfxMuted;
        localStorage.setItem('sfx_muted', nextMuted ? 'true' : 'false');
        
        if (window.activeVsaiArenaGame && window.activeVsaiArenaGame.sound) {
            window.activeVsaiArenaGame.sound.mute = nextMuted;
        }
        
        syncAudioModalButtons();
    }

    // Attach functions to window for onclick handlers
    window.openAudioSettings = openAudioSettings;
    window.closeAudioSettings = closeAudioSettings;
    window.toggleBGMSetting = toggleBGMSetting;
    window.toggleSFXSetting = toggleSFXSetting;

    // =====================================================
    //  INIT PHASER
    // =====================================================
    window.activeVsaiArenaGame = new Phaser.Game({
        type: Phaser.AUTO,
        width: GAME_WIDTH,
        height: GAME_HEIGHT,
        backgroundColor: '#f0fdf4',
        parent: 'game-container',
        pixelArt: true,
        scene: [LoadingScene, ArenaScene],
        scale: {
            mode: Phaser.Scale.RESIZE,
            autoCenter: Phaser.Scale.CENTER_BOTH
        }
    });

    // Cleanup when leaving page via Livewire
    const cleanUpArenaPage = function() {
        console.log("Cleaning up Arena Page...");
        if (window.activeVsaiArenaGame) {
            const scene = window.activeVsaiArenaGame.scene.getScene('ArenaScene');
            if (scene && scene.ws) {
                console.log("Closing WebSocket in ArenaScene...");
                scene.ws.close();
            }
            window.activeVsaiArenaGame.destroy(true);
            window.activeVsaiArenaGame = null;
        }
        delete window.openAudioSettings;
        delete window.closeAudioSettings;
        delete window.toggleBGMSetting;
        delete window.toggleSFXSetting;
    };

    document.addEventListener('livewire:navigating', function cleanup() {
        cleanUpArenaPage();
        document.removeEventListener('livewire:navigating', cleanup);
    }, { once: true });
}
</script>
@endpush
