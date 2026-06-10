<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Franchise Game — Registrasi Jalur</title>
    <link rel="stylesheet" href="../assets/css/game-layout.css">
    <style>
    #jalur-name-input {
        position: fixed;
        left: 0;
        top: 0;
        width: 0;
        height: 0;
        z-index: 20;
        padding: 0 12px;
        font-family: 'Pixelify Sans', monospace;
        font-size: 15px;
        font-weight: bold;
        color: #15803d;
        background: #f0fdf4;
        border: 3px solid #22c55e;
        border-radius: 12px;
        box-shadow: 0 4px 0 0 #15803d;
        outline: none;
        text-align: center;
        image-rendering: pixelated;
        image-rendering: crisp-edges;
        display: block;
        transform: translate(-50%, -50%) scale(0);
        pointer-events: auto !important;
        user-select: text !important;
        -webkit-user-select: text !important;
        -moz-user-select: text !important;
        -ms-user-select: text !important;
    }
    #jalur-name-input::placeholder {
        color: #86efac;
        font-style: italic;
    }
    #jalur-name-input:focus {
        border-color: #d97706;
        box-shadow: 0 4px 0 0 #b45309;
        background: #ffffff;
    }
    #game-container canvas {
        z-index: 1;
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
            <input type="text" id="jalur-name-input" placeholder="Nama Jalurmu..." maxlength="16">
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/phaser@3.88.2/dist/phaser.min.js"></script>

<script>
const GAME_WIDTH  = 360;
const GAME_HEIGHT = 760;

// =====================================================
//  HELPER — Shimmer pada PNG icon (BitmapMask)
// =====================================================
function addIconShimmer(scene, img, delay) {
    const w = img.displayWidth;
    const h = img.displayHeight;
    const shimW = w * 0.32;
    const slant = h * 0.65;

    // Create BitmapMask directly from the target image (conforms to png transparency shape)
    const bitmapMask = img.createBitmapMask();

    const shimGfx = scene.add.graphics();
    shimGfx.setMask(bitmapMask);

    const animData = { progress: 0 };
    const tween = scene.tweens.add({
        targets: animData,
        progress: 1,
        duration: 600,
        ease: 'Quad.easeInOut',
        delay: delay || 0,
        repeat: -1,
        repeatDelay: 2600,
        onUpdate: () => {
            if (!img.active) return;

            let wx = img.x;
            let wy = img.y;
            let parent = img.parentContainer;
            while (parent) {
                wx += parent.x;
                wy += parent.y;
                parent = parent.parentContainer;
            }

            const ix = wx - w / 2;
            const iy = wy - h / 2;

            const startX = ix - shimW - slant * 2;
            const endX = ix + w + shimW;
            const currentX = startX + (endX - startX) * animData.progress;

            shimGfx.clear();
            shimGfx.fillStyle(0xffffff, 0.55);
            shimGfx.fillPoints([
                { x: currentX,                    y: iy     },
                { x: currentX + shimW,            y: iy     },
                { x: currentX + shimW + slant*2,  y: iy + h },
                { x: currentX + slant*2,          y: iy + h },
            ], true);
        },
        onRepeat: () => shimGfx.clear()
    });

    // Clean up graphics and stop tween when the image is destroyed
    img.once('destroy', () => {
        if (tween) tween.stop();
        if (shimGfx) shimGfx.destroy();
    });
}

// =====================================================
//  REGISTRATION SCENE CLASS
// =====================================================
class RegisterScene extends Phaser.Scene {
    constructor() { super({ key: 'RegisterScene' }); }

    preload() {
        this.load.image('bgmenu', '../assets/image/bg/bgmenu.jpg');
        this.load.image('profil',  '../assets/image/ui/profil.gif');
        this.load.image('profil2', '../assets/image/ui/profil2.gif');
        this.load.image('profil3', '../assets/image/ui/profil3.gif');
        this.load.image('profil4', '../assets/image/ui/profil4.gif');
    }

    create() {
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
        const titleText = this.add.text(cx, 85, '✦ DATA DIRIMU ✦', {
            fontFamily: '"Press Start 2P", monospace',
            fontSize: '18px',
            fontStyle: 'bold',
            color: '#22c55e',
            stroke: '#ffffff',
            strokeThickness: 4
        }).setOrigin(0.5);

        // Subtitle
        const subtitleText = this.add.text(cx, 115, 'Tentukan identitas dan nama jaluarmu', {
            fontFamily: '"Pixelify Sans", monospace',
            fontSize: '12px',
            fontStyle: 'bold',
            color: '#15803d'
        }).setOrigin(0.5);

        // =============================================
        //  REGISTRATION CARD PANEL
        // =============================================
        const cardContainer = this.add.container(cx, 310);
        const cW = W - 44;
        const cH = 320;

        const cardBg = this.add.graphics();
        const radius = 20;
        const shadowOffset = 5;

        // Shadow
        cardBg.fillStyle(0x15803d, 0.3);
        cardBg.fillRoundedRect(-cW/2 + shadowOffset, -cH/2 + shadowOffset, cW, cH, radius);
        // Main panel
        cardBg.fillStyle(0xffffff, 1);
        cardBg.lineStyle(4, 0x22c55e, 1);
        cardBg.fillRoundedRect(-cW/2, -cH/2, cW, cH, radius);
        cardBg.strokeRoundedRect(-cW/2, -cH/2, cW, cH, radius);

        cardContainer.add(cardBg);

        // Instruction Labels inside Card
        const avatarLbl = this.add.text(0, -cH/2 + 25, 'PILIH FOTO PROFIL', {
            fontFamily: '"Press Start 2P", monospace',
            fontSize: '9px',
            color: '#15803d'
        }).setOrigin(0.5);
        cardContainer.add(avatarLbl);

        const nameLbl = this.add.text(0, 36, 'NAMA JALUAR', {
            fontFamily: '"Press Start 2P", monospace',
            fontSize: '9px',
            color: '#15803d'
        }).setOrigin(0.5);
        cardContainer.add(nameLbl);

        // =============================================
        //  AVATAR OPTIONS SELECTION (ROUNDED CORNER FRAMES)
        // =============================================
        const avatarOptions = [
            { key: 'profil',  x: -96, displaySize: 42 },
            { key: 'profil2', x: -32, displaySize: 42 },
            { key: 'profil3', x: 32, displaySize: 42 },
            { key: 'profil4', x: 96, displaySize: 42 }
        ];

        let selectedAvatar = 'profil';
        const selectionRing = this.add.graphics();
        cardContainer.add(selectionRing);

        const drawSelectionRing = () => {
            selectionRing.clear();
            const option = avatarOptions.find(o => o.key === selectedAvatar);
            if (option) {
                // Outer highlight border (slightly larger rounded rectangle)
                selectionRing.lineStyle(3.5, 0xd97706, 1);
                selectionRing.strokeRoundedRect(option.x - 32, -82, 64, 64, 8);
                // Inner highlight border
                selectionRing.lineStyle(1.5, 0xfef3c7, 1);
                selectionRing.strokeRoundedRect(option.x - 29, -79, 58, 58, 6);
            }
        };

        avatarOptions.forEach((option) => {
            // Draw background rounded square slot (54x54, radius 6)
            const slotBg = this.add.graphics();
            slotBg.fillStyle(0xf0fdf4, 1);
            slotBg.lineStyle(2.5, 0x86efac, 1);
            slotBg.fillRoundedRect(option.x - 27, -77, 54, 54, 6);
            slotBg.strokeRoundedRect(option.x - 27, -77, 54, 54, 6);
            cardContainer.add(slotBg);

            // Icon Image
            const img = this.add.image(option.x, -50, option.key)
                .setDisplaySize(option.displaySize, option.displaySize)
                .setInteractive({ useHandCursor: true });
            
            cardContainer.add(img);
            addIconShimmer(this, img, Math.random() * 800 + 400);

            // Click event to select avatar
            img.on('pointerdown', () => {
                this.tweens.add({
                    targets: img,
                    scaleX: 0.8, scaleY: 0.8,
                    duration: 60, yoyo: true,
                    onComplete: () => {
                        selectedAvatar = option.key;
                        drawSelectionRing();
                    }
                });
            });
        });

        // Draw selection ring initially
        drawSelectionRing();

        // =============================================
        //  PLAY / MULAI BUTTON
        // =============================================
        const startBtn = this.add.container(cx, 515);
        const sW = 160;
        const sH = 46;
        startBtn.setSize(sW, sH);
        startBtn.setInteractive({ useHandCursor: true });

        const startBg = this.add.graphics();
        function drawStartBtn(fill, border) {
            startBg.clear();
            // Shadow
            startBg.fillStyle(0x15803d, 0.25);
            startBg.fillRoundedRect(-sW/2 + 4, -sH/2 + 4, sW, sH, 12);
            // Main button
            startBg.fillStyle(fill, 1);
            startBg.lineStyle(3, border, 1);
            startBg.fillRoundedRect(-sW/2, -sH/2, sW, sH, 12);
            startBg.strokeRoundedRect(-sW/2, -sH/2, sW, sH, 12);
        }

        drawStartBtn(0x22c55e, 0x16a34a);
        startBtn.add(startBg);

        const startTxt = this.add.text(0, 0, 'MULAI', {
            fontFamily: '"Press Start 2P", monospace',
            fontSize: '11px',
            color: '#ffffff',
            stroke: '#15803d',
            strokeThickness: 3
        }).setOrigin(0.5);
        startBtn.add(startTxt);

        // Add Shimmer to start button
        const startMask = this.make.graphics({ add: false });
        startMask.fillStyle(0xffffff);
        startMask.fillRoundedRect(cx - sW/2, 515 - sH/2, sW, sH, 12);

        const startShim = this.add.graphics();
        startShim.setMask(startMask.createGeometryMask());

        const startShimW = sW * 0.22;
        const startSlant = sH * 0.65;
        const animData = { progress: 0 };
        this.tweens.add({
            targets: animData,
            progress: 1,
            duration: 650,
            ease: 'Quad.easeInOut',
            delay: 1200,
            repeat: -1,
            repeatDelay: 2500,
            onUpdate: () => {
                const currentBtnX = startBtn.x;
                const currentBtnY = startBtn.y;
                const startX = currentBtnX - sW/2 - startShimW - startSlant * 2;
                const endX = currentBtnX + sW/2 + startShimW;
                const currentX = startX + (endX - startX) * animData.progress;

                startShim.clear();
                startShim.fillStyle(0xffffff, 0.45);
                startShim.fillPoints([
                    { x: currentX,                    y: currentBtnY - sH/2 },
                    { x: currentX + startShimW,       y: currentBtnY - sH/2 },
                    { x: currentX + startShimW + startSlant*2, y: currentBtnY + sH/2 },
                    { x: currentX + startSlant*2,     y: currentBtnY + sH/2 },
                ], true);
            },
            onRepeat: () => startShim.clear()
        });

        // Hover & Click events for play button
        startBtn.on('pointerover', () => {
            drawStartBtn(0x4ade80, 0x22c55e);
            this.tweens.add({ targets: startBtn, scaleX: 1.05, scaleY: 1.05, duration: 80 });
        });

        startBtn.on('pointerout', () => {
            drawStartBtn(0x22c55e, 0x16a34a);
            this.tweens.add({ targets: startBtn, scaleX: 1, scaleY: 1, duration: 80 });
        });

        startBtn.on('pointerdown', () => {
            startBg.y = 2; startTxt.y = 2;
        });

        // Warning Text placed inside cardContainer directly below the input slot
        const warningTxt = this.add.text(0, 112, '', {
            fontFamily: '"Pixelify Sans", monospace',
            fontSize: '12px',
            fontStyle: 'bold',
            color: '#ef4444',
            align: 'center'
        }).setOrigin(0.5);
        cardContainer.add(warningTxt);

        startBtn.on('pointerup', () => {
            startBg.y = 0; startTxt.y = 0;
            
            const inputEl = document.getElementById('jalur-name-input');
            const nameValue = inputEl ? inputEl.value.trim() : '';

            if (!nameValue) {
                // Show warning message
                warningTxt.setText('⚠️ Silakan masukkan Nama Jalurmu!');
                this.cameras.main.shake(200, 0.012);
                if (inputEl) {
                    inputEl.style.borderColor = '#ef4444';
                    inputEl.style.boxShadow = '0 4px 0 0 #b91c1c';
                    inputEl.focus();
                }
                return;
            }

            // Valid - Save and Redirect
            localStorage.setItem('jalurName', nameValue);
            localStorage.setItem('selectedAvatar', selectedAvatar);
            localStorage.setItem('coins', '100000'); // set initial coin balance
            
            this.tweens.add({
                targets: startBtn,
                scaleX: 0.92, scaleY: 0.92,
                duration: 70, yoyo: true,
                onComplete: () => {
                    if (inputEl) inputEl.style.display = 'none';
                    this.cameras.main.fadeOut(400, 240, 253, 244);
                    this.cameras.main.once('camerafadeoutcomplete', () => {
                        window.location.href = '../mainmenu.php';
                    });
                }
            });
        });

        // =============================================
        //  RESPONSIVE DOM OVERLAY POSITIONING (INPUT)
        // =============================================
        const positionInput = () => {
            const input = document.getElementById('jalur-name-input');
            if (!input) return;
            const canvas = this.game.canvas;
            if (!canvas) return;

            const rect = canvas.getBoundingClientRect();
            const scaleX = rect.width / this.scale.width;
            const scaleY = rect.height / this.scale.height;

            // Target frame slot is centered inside cardContainer + yOffset (74)
            const inputY = cardContainer.y + 74;

            input.style.left = (rect.left + cardContainer.x * scaleX) + 'px';
            input.style.top = (rect.top + inputY * scaleY) + 'px';
            input.style.width = (244 * scaleX) + 'px';
            input.style.height = (40 * scaleY) + 'px';
            input.style.fontSize = (15 * scaleY) + 'px';
            
            // Sync input scale with the card container scale
            input.style.transform = `translate(-50%, -50%) scale(${cardContainer.scaleX})`;
        };

        // Card Container intro animation
        cardContainer.setScale(0);
        this.tweens.add({
            targets: cardContainer,
            scaleX: 1, scaleY: 1,
            duration: 450,
            ease: 'Back.easeOut',
            delay: 150,
            onUpdate: positionInput,
            onComplete: positionInput
        });

        // Resize handler inside scene
        const resizeAll = () => {
            const currentW = this.scale.width;
            const currentH = this.scale.height;
            const currentCx = currentW / 2;
            const currentCy = currentH / 2;

            // Background
            bg.setPosition(currentCx, currentCy);
            const sX = currentW / bg.width;
            const sY = currentH / bg.height;
            bg.setScale(Math.max(sX, sY));

            // Titles
            titleText.setPosition(currentCx, 85);
            subtitleText.setPosition(currentCx, 115);

            // Card Panel container
            const cardY = currentCy - 70;
            cardContainer.setPosition(currentCx, cardY);

            // Start Button
            const btnY = cardY + 205;
            startBtn.setPosition(currentCx, btnY);

            // Start shimmer mask
            startMask.clear();
            startMask.fillStyle(0xffffff);
            startMask.fillRoundedRect(currentCx - sW/2, btnY - sH/2, sW, sH, 12);

            positionInput();
        };

        this.scale.on('resize', resizeAll);
        
        // Initial positioning call
        this.time.delayedCall(50, () => {
            resizeAll();
        });

        // Event listener on input type resets border color
        const inputEl = document.getElementById('jalur-name-input');
        if (inputEl) {
            inputEl.addEventListener('input', () => {
                inputEl.style.borderColor = '#22c55e';
                inputEl.style.boxShadow = '0 4px 0 0 #15803d';
                warningTxt.setText('');
            });
        }

        this.events.once('shutdown', () => {
            this.scale.off('resize', resizeAll);
            if (inputEl) inputEl.style.display = 'none';
        });
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
    input: {
        keyboard: false // Nonaktifkan penangkapan input keyboard Phaser agar input field HTML dapat diketik
    },
    scene: [ RegisterScene ],
    scale: {
        mode: Phaser.Scale.RESIZE,
        autoCenter: Phaser.Scale.CENTER_BOTH
    }
});
</script>

<script src="../assets/js/game-layout.js?v=<?= time() ?>"></script>
</body>
</html>
