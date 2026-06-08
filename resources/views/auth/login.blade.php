<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Franchise Game — Masuk Akun</title>
    <link rel="stylesheet" href="{{ asset('game_pacu/assets/css/game-layout.css') }}">
    <style>
    #game-container canvas {
        z-index: 1;
        image-rendering: pixelated;
        image-rendering: crisp-edges;
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
        <div id="game-container"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/phaser@3.88.2/dist/phaser.min.js"></script>

<script>
const GAME_WIDTH  = 360;
const GAME_HEIGHT = 760;

// =====================================================
//  AUTH SCENE CLASS
// =====================================================
class AuthScene extends Phaser.Scene {
    constructor() { super({ key: 'AuthScene' }); }

    preload() {
        this.load.image('bgmenu', '{{ asset('game_pacu/assets/image/bg/bgmenu.jpg') }}');
        this.load.image('google_logo', '{{ asset('game_pacu/assets/image/ui/google.png') }}');
    }

    create() {
        // Play click sound on any interactive Phaser object
        this.input.on('pointerdown', (pointer, currentlyOver) => {
            if (currentlyOver.length > 0 && window.playClickSound) {
                window.playClickSound();
            }
        });

        const W  = this.scale.width;
        const H  = this.scale.height;
        const cx = W / 2;
        const cy = H / 2;

        this.cameras.main.fadeIn(500, 240, 253, 244);

        // ---- Background cover ----
        const bg = this.add.image(cx, cy, 'bgmenu');
        const scaleX_bg = W / bg.width;
        const scaleY_bg = H / bg.height;
        bg.setScale(Math.max(scaleX_bg, scaleY_bg));

        // =============================================
        //  TITLE BANNER
        // =============================================
        this.add.text(cx, cy - 140, '✦ MASUK GAME ✦', {
            fontFamily: '"Press Start 2P", monospace',
            fontSize: '18px',
            fontStyle: 'bold',
            color: '#22c55e',
            stroke: '#ffffff',
            strokeThickness: 4
        }).setOrigin(0.5);

        // Subtitle
        this.add.text(cx, cy - 105, 'Hubungkan akun untuk menyimpan skor', {
            fontFamily: '"Pixelify Sans", monospace',
            fontSize: '13px',
            fontStyle: 'bold',
            color: '#15803d'
        }).setOrigin(0.5);

        // =============================================
        //  GOOGLE SIGN IN BUTTON (SHIMMER CONTAINER)
        // =============================================
        const btnW = 250;
        const btnH = 50;
        const btnContainer = this.add.container(cx, cy + 10);
        btnContainer.setSize(btnW, btnH);
        btnContainer.setInteractive({ useHandCursor: true });

        const btnBg = this.add.graphics();
        const shadowOffset = 4;
        
        function drawBtn(fill, border) {
            btnBg.clear();
            // Shadow
            btnBg.fillStyle(0x15803d, 0.25);
            btnBg.fillRoundedRect(-btnW/2 + shadowOffset, -btnH/2 + shadowOffset, btnW, btnH, 12);
            // Main button
            btnBg.fillStyle(fill, 1);
            btnBg.lineStyle(3, border, 1);
            btnBg.fillRoundedRect(-btnW/2, -btnH/2, btnW, btnH, 12);
            btnBg.strokeRoundedRect(-btnW/2, -btnH/2, btnW, btnH, 12);
        }

        // Draw initial white button
        drawBtn(0xffffff, 0x86efac);
        btnContainer.add(btnBg);

        // Google "G" Logo Image
        const googleIcon = this.add.image(-btnW/2 + 28, 0, 'google_logo')
            .setDisplaySize(24, 24);
        btnContainer.add(googleIcon);

        // Button label
        const btnTxt = this.add.text(14, 0, 'MASUK DENGAN GOOGLE', {
            fontFamily: '"Press Start 2P", monospace',
            fontSize: '8px',
            color: '#374151'
        }).setOrigin(0.5);
        btnContainer.add(btnTxt);

        // Shimmer sweep effect (using GeometryMask)
        const btnMask = this.make.graphics({ add: false });
        btnMask.fillStyle(0xffffff);
        btnMask.fillRoundedRect(cx - btnW/2, cy + 10 - btnH/2, btnW, btnH, 12);

        const btnShimGfx = this.add.graphics();
        btnShimGfx.setMask(btnMask.createGeometryMask());

        const shimW = btnW * 0.2;
        const slant = btnH * 0.65;
        const wsp = { v: cx - btnW/2 - shimW - slant * 2 };
        
        this.tweens.add({
            targets: wsp,
            v: cx + btnW/2 + shimW,
            duration: 650,
            ease: 'Quad.easeInOut',
            delay: 1000,
            repeat: -1,
            repeatDelay: 2500,
            onUpdate: () => {
                btnShimGfx.clear();
                btnShimGfx.fillStyle(0xffffff, 0.35);
                btnShimGfx.fillPoints([
                    { x: wsp.v,                    y: cy + 10 - btnH/2 },
                    { x: wsp.v + shimW,            y: cy + 10 - btnH/2 },
                    { x: wsp.v + shimW + slant*2,  y: cy + 10 + btnH/2 },
                    { x: wsp.v + slant*2,          y: cy + 10 + btnH/2 },
                ], true);
            },
            onRepeat: () => btnShimGfx.clear()
        });

        // Hover & click handlers
        btnContainer.on('pointerover', () => {
            drawBtn(0xfafafa, 0x22c55e);
            this.tweens.add({ targets: btnContainer, scaleX: 1.04, scaleY: 1.04, duration: 80 });
        });

        btnContainer.on('pointerout', () => {
            drawBtn(0xffffff, 0x86efac);
            this.tweens.add({ targets: btnContainer, scaleX: 1, scaleY: 1, duration: 80 });
        });

        let isLoggingIn = false;
        btnContainer.on('pointerdown', () => {
            if (isLoggingIn) return;
            btnContainer.setScale(0.96);
        });

        btnContainer.on('pointerup', () => {
            if (isLoggingIn) return;
            btnContainer.setScale(1.04);
            isLoggingIn = true;

            // Show connecting loading overlay
            const overlay = this.add.graphics();
            overlay.fillStyle(0x000000, 0.6);
            overlay.fillRect(0, 0, W, H);
            overlay.setInteractive(new Phaser.Geom.Rectangle(0, 0, W, H), Phaser.Geom.Rectangle.Contains);

            const loadingBox = this.add.container(cx, cy);
            
            const lbBg = this.add.graphics();
            lbBg.fillStyle(0xffffff, 1);
            lbBg.lineStyle(4, 0x22c55e, 1);
            lbBg.fillRoundedRect(-120, -50, 240, 100, 16);
            lbBg.strokeRoundedRect(-120, -50, 240, 100, 16);
            loadingBox.add(lbBg);

            const lbTxt = this.add.text(0, 20, 'Menghubungkan...', {
                fontFamily: '"Pixelify Sans", monospace',
                fontSize: '14px',
                fontStyle: 'bold',
                color: '#15803d'
            }).setOrigin(0.5);
            loadingBox.add(lbTxt);

            // Retro custom loading spinner (spinning arc)
            const spinnerGfx = this.add.graphics();
            loadingBox.add(spinnerGfx);

            let angle = 0;
            const spinnerTimer = this.time.addEvent({
                delay: 16,
                callback: () => {
                    angle += 0.12;
                    spinnerGfx.clear();
                    spinnerGfx.lineStyle(4, 0x22c55e, 1);
                    spinnerGfx.beginPath();
                    spinnerGfx.arc(0, -15, 14, angle, angle + Math.PI * 1.5, false);
                    spinnerGfx.strokePath();
                },
                loop: true
            });

            // Redirect after 1.2s delay
            this.time.delayedCall(1200, () => {
                spinnerTimer.destroy();
                this.cameras.main.fadeOut(400, 240, 253, 244);
                this.cameras.main.once('camerafadeoutcomplete', () => {
                    window.location.href = '{{ route('google.login') }}';
                });
            });
        });

        // =============================================
        //  FOOTER TEXT
        // =============================================
        this.add.text(cx, H - 24, 'v1.0.0  |  Masuk Akun Google', {
            fontFamily: '"Pixelify Sans", monospace',
            fontSize: '11px',
            fontStyle: 'bold',
            color: 'rgba(21, 128, 61, 0.45)'
        }).setOrigin(0.5);
    }
}

// =====================================================
//  INIT PHASER GAME
// =====================================================
const game = new Phaser.Game({
    type: Phaser.AUTO,
    width: GAME_WIDTH,
    height: GAME_HEIGHT,
    backgroundColor: '#f0fdf4',
    parent: 'game-container',
    pixelArt: true,
    scene: [ AuthScene ],
    scale: {
        mode: Phaser.Scale.RESIZE,
        autoCenter: Phaser.Scale.CENTER_BOTH
    }
});
</script>

<script src="{{ asset('game_pacu/assets/js/game-layout.js') }}?v=<?= time() ?>"></script>
</body>
</html>
