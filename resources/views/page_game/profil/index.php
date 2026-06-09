<?php
$winsCount = auth()->user()->wins()->count();
$statusText = 'ANAK BARU';
if ($winsCount >= 100) {
    $statusText = 'PAMACU INTI';
} elseif ($winsCount >= 50) {
    $statusText = 'PAMAIN SEWA';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Franchise Game — Profil Pamacu Inti</title>
    <link rel="stylesheet" href="/game_pacu/assets/css/game-layout.css">
    <style>
        /* Overlay GIF profil di atas canvas Phaser */
        #profil-gif-page {
            position: fixed;
            left: 0;
            top: 0;
            width: 0;
            height: 0;
            z-index: 20;
            pointer-events: none;
            display: block;
            image-rendering: pixelated;
            image-rendering: crisp-edges;
            border-radius: 8px;
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
                <img id="profil-gif-page" src="/game_pacu/assets/image/ui/profil.gif" alt="profil">
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/phaser@3.88.2/dist/phaser.min.js"></script>

    <script>
        const GAME_WIDTH = 360;
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
                        { x: currentX, y: iy },
                        { x: currentX + shimW, y: iy },
                        { x: currentX + shimW + slant * 2, y: iy + h },
                        { x: currentX + slant * 2, y: iy + h },
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
        //  PROFILE SCENE CLASS
        // =====================================================
        class ProfileScene extends Phaser.Scene {
            constructor() { super({ key: 'ProfileScene' }); }

            preload() {
                this.load.image('bgmenu', '/game_pacu/assets/image/bg/bgmenu.jpg');
                this.load.image('koin', '/game_pacu/assets/image/ui/koin.png');
                this.load.image('back', '/game_pacu/assets/image/ui/back.png');
                this.load.image('bubblechat', '/game_pacu/assets/image/ui/bubblechat.png');
                preloadJalurAssets(this);
            }

            create() {
                // Play click sound on any interactive Phaser object
                this.input.on('pointerdown', (pointer, currentlyOver) => {
                    if (currentlyOver.length > 0 && window.playClickSound) {
                        window.playClickSound();
                    }
                });

                const W = this.scale.width;
                const H = this.scale.height;
                const cx = W / 2;

                this.cameras.main.fadeIn(500, 240, 253, 244);

                // ---- Background cover ----
                const bg = this.add.image(cx, H / 2, 'bgmenu');
                const scaleX_bg = W / bg.width;
                const scaleY_bg = H / bg.height;
                bg.setScale(Math.max(scaleX_bg, scaleY_bg));

                // =============================================
                //  HEADER BAR & NAV
                // =============================================
                // ---- Tombol Kembali (Top Left) ----
                const backBtnContainer = this.add.container(38, 34);
                backBtnContainer.setSize(48, 48);
                backBtnContainer.setInteractive({ useHandCursor: true });

                const backBtnBg = this.add.graphics();
                function drawBackBtnBg(fillAlpha, lineAlpha, lineWidth) {
                    backBtnBg.clear();
                    backBtnBg.fillStyle(0xffffff, fillAlpha);
                    backBtnBg.lineStyle(lineWidth, 0xffffff, lineAlpha);
                    backBtnBg.fillRoundedRect(-24, -24, 48, 48, 8);
                    backBtnBg.strokeRoundedRect(-24, -24, 48, 48, 8);
                }
                drawBackBtnBg(0.2, 0.4, 2);
                backBtnContainer.add(backBtnBg);

                const backIcon = this.add.image(0, 0, 'back').setDisplaySize(32, 32);
                backBtnContainer.add(backIcon);

                // Action klik kembali
                backBtnContainer.on('pointerdown', () => {
                    this.tweens.add({
                        targets: backBtnContainer,
                        scaleX: 0.8, scaleY: 0.8,
                        duration: 80, ease: 'Power2',
                        yoyo: true,
                        onComplete: () => {
                            this.cameras.main.fadeOut(300, 240, 253, 244);
                            this.cameras.main.once('camerafadeoutcomplete', () => {
                                window.location.href = '/main-menu';
                            });
                        }
                    });
                });

                // Hover effect
                backBtnContainer.on('pointerover', () => {
                    drawBackBtnBg(0.35, 0.7, 2.5);
                    this.tweens.add({ targets: backBtnContainer, scaleX: 1.05, scaleY: 1.05, duration: 90, ease: 'Power2' });
                });
                backBtnContainer.on('pointerout', () => {
                    drawBackBtnBg(0.2, 0.4, 2);
                    this.tweens.add({ targets: backBtnContainer, scaleX: 1, scaleY: 1, duration: 90, ease: 'Power2' });
                });

                // Shimmer pada tombol kembali kotak
                const backMask = this.make.graphics({ add: false });
                backMask.fillStyle(0xffffff);
                backMask.fillRoundedRect(38 - 24, 34 - 24, 48, 48, 8);

                const backShimGfx = this.add.graphics();
                backShimGfx.setMask(backMask.createGeometryMask());

                const backWsp = { v: 14 - 15 - 15 };
                this.tweens.add({
                    targets: backWsp,
                    v: 62 + 15,
                    duration: 650,
                    ease: 'Quad.easeInOut',
                    delay: Math.random() * 500 + 1000,
                    repeat: -1,
                    repeatDelay: 2500,
                    onUpdate: () => {
                        backShimGfx.clear();
                        backShimGfx.fillStyle(0xffffff, 0.45);
                        backShimGfx.fillPoints([
                            { x: backWsp.v, y: 34 - 24 },
                            { x: backWsp.v + 15, y: 34 - 24 },
                            { x: backWsp.v + 15 + 15, y: 34 + 24 },
                            { x: backWsp.v + 15, y: 34 + 24 },
                        ], true);
                    },
                    onRepeat: () => backShimGfx.clear()
                });

                // =============================================
                //  TOP RIGHT: KOIN
                // =============================================
                const BAR_Y = 34;
                const COIN_ICON_X = W - 78;

                const coinImgGlobal = this.add.image(COIN_ICON_X, BAR_Y, 'koin')
                    .setDisplaySize(36, 36)
                    .setInteractive({ useHandCursor: true });

                coinImgGlobal.on('pointerdown', () => {
                    this.tweens.add({
                        targets: coinImgGlobal,
                        scaleX: 0.7, scaleY: 0.7,
                        duration: 80, ease: 'Power2',
                        yoyo: true
                    });
                });

                let coinCount = <?= auth()->user()->kuansing_poin ?>;

                const coinText = this.add.text(COIN_ICON_X + 22, BAR_Y + 1, String(coinCount), {
                    fontFamily: '"Pixelify Sans", monospace',
                    fontSize: '13px',
                    fontStyle: 'bold',
                    color: '#FFD700',
                    stroke: '#15803d',
                    strokeThickness: 3
                }).setOrigin(0, 0.5);

                addIconShimmer(this, coinImgGlobal, 1100);

                // =============================================
                //  PROFILE CARD PANEL
                // =============================================
                const cardContainer = this.add.container(cx, H / 2);
                const cW = W - 44;
                const cH = 420;

                const cardBg = this.add.graphics();
                const radius = 20;
                const shadowOffset = 5;

                // Shadow
                cardBg.fillStyle(0x15803d, 0.3);
                cardBg.fillRoundedRect(-cW / 2 + shadowOffset, -cH / 2 + shadowOffset, cW, cH, radius);
                // Main panel
                cardBg.fillStyle(0xffffff, 1);
                cardBg.lineStyle(4, 0x22c55e, 1);
                cardBg.fillRoundedRect(-cW / 2, -cH / 2, cW, cH, radius);
                cardBg.strokeRoundedRect(-cW / 2, -cH / 2, cW, cH, radius);

                // Slot foto profil frame (100x100)
                cardBg.fillStyle(0xf0fdf4, 1);
                cardBg.fillRoundedRect(-50, -180, 100, 100, 14);
                cardBg.lineStyle(3, 0x86efac, 1);
                cardBg.strokeRoundedRect(-50, -180, 100, 100, 14);

                // Judul Badge "PAMACU INTI"
                cardBg.fillStyle(0xd97706, 1);
                cardBg.lineStyle(2, 0xfef3c7, 1);
                cardBg.fillRoundedRect(-80, -42, 160, 24, 6);
                cardBg.strokeRoundedRect(-80, -42, 160, 24, 6);

                // Garis Pembatas
                cardBg.lineStyle(2, 0xe2e8f0, 1);
                cardBg.lineBetween(-cW / 2 + 24, 5, cW / 2 - 24, 5);

                cardContainer.add(cardBg);

                // Render track preview natively inside card
                const previewGroup = createJalurPreview(this, 0, 75, 0.8);
                cardContainer.add(previewGroup);

                // Add Card text elements
                const customName = localStorage.getItem('jalurName') || 'Sanak Kuansing';
                const nameTxt = this.add.text(0, -60, customName, {
                    fontFamily: '"Press Start 2P", monospace',
                    fontSize: '13px',
                    color: '#15803d'
                }).setOrigin(0.5);
                cardContainer.add(nameTxt);

                const badgeTxt = this.add.text(0, -30, '⚡ <?= $statusText ?> ⚡', {
                    fontFamily: '"Press Start 2P", monospace',
                    fontSize: '8px',
                    color: '#ffffff'
                }).setOrigin(0.5);
                cardContainer.add(badgeTxt);

                // Extra stats at bottom (shifted slightly up)
                const bottomStats = this.add.text(0, 160, '🏆 MENANG: <?= auth()->user()->wins()->count() ?>  |  ❌ KALAH: <?= auth()->user()->losses()->count() ?>', {
                    fontFamily: '"Pixelify Sans", monospace',
                    fontSize: '12px',
                    fontStyle: 'bold',
                    color: '#16a34a'
                }).setOrigin(0.5);
                cardContainer.add(bottomStats);

                // Intro Animation for card
                cardContainer.setScale(0);
                this.tweens.add({
                    targets: cardContainer,
                    scaleX: 1, scaleY: 1,
                    duration: 450,
                    ease: 'Back.easeOut',
                    delay: 150,
                    onUpdate: () => positionProfilGif(),
                    onComplete: () => positionProfilGif()
                });

                // =============================================
                //  RESPONSIVE DOM OVERLAY POSITIONING (PROFIL.GIF)
                // =============================================
                function positionProfilGif() {
                    const gif = document.getElementById('profil-gif-page');
                    if (!gif) return;
                    const canvas = cardContainer.scene.game.canvas;
                    if (!canvas) return;

                    const rect = canvas.getBoundingClientRect();
                    const scaleX = rect.width / cardContainer.scene.scale.width;
                    const scaleY = rect.height / cardContainer.scene.scale.height;

                    // Target frame center is at cardContainer (cx, cardContainer.y) - 130
                    // Left offset of frame slot is cx - 50. Top offset is cardContainer.y - 130 - 50. Width=100, Height=100.
                    const cardCenterY = cardContainer.y;
                    const gifCenterY = cardCenterY - 130;

                    gif.style.left = (rect.left + (cx - 50) * scaleX) + 'px';
                    gif.style.top = (rect.top + (gifCenterY - 50) * scaleY) + 'px';
                    gif.style.width = (100 * scaleX) + 'px';
                    gif.style.height = (100 * scaleY) + 'px';

                    // Introduce a subtle CSS scale animation to match phaser card intro scaling
                    if (cardContainer.scaleX < 0.1) {
                        gif.style.transform = 'scale(0)';
                    } else {
                        gif.style.transform = `scale(${cardContainer.scaleX})`;
                        gif.style.transition = 'transform 0.1s ease-out';
                    }
                }

                this.time.delayedCall(50, positionProfilGif);
                this.scale.on('resize', positionProfilGif);
                window.addEventListener('resize', positionProfilGif);

                this.events.once('shutdown', () => {
                    window.removeEventListener('resize', positionProfilGif);
                    const gif = document.getElementById('profil-gif-page');
                    if (gif) gif.style.display = 'none';
                });
            }
        }

        // =====================================================
        //  INIT PHASER WITH SERVER CUSTOMIZATIONS
        // =====================================================
        fetch('/tukang-jaluar/get')
            .then(res => res.json())
            .then(data => {
                if (data.nama_jalur) {
                    localStorage.setItem('jalurName', data.nama_jalur);
                }
                bootPhaser();
            })
            .catch(err => {
                console.error('Failed to sync customizations:', err);
                bootPhaser();
            });

        function bootPhaser() {
            const game = new Phaser.Game({
                type: Phaser.AUTO,
                width: GAME_WIDTH,
                height: GAME_HEIGHT,
                backgroundColor: '#f0fdf4',
                parent: 'game-container',
                pixelArt: true,
                scene: [ProfileScene],
                scale: {
                    mode: Phaser.Scale.RESIZE,
                    autoCenter: Phaser.Scale.CENTER_BOTH
                }
            });
        }
    </script>

    <script src="/game_pacu/assets/js/jalur-preview-phaser.js?v=<?= time() ?>"></script>
    <script src="/game_pacu/assets/js/game-layout.js?v=<?= time() ?>"></script>
    <script>
        (function () {
            const dbAvatar = '<?php echo auth()->user()->foto_profile ?? ""; ?>';
            const selectedAvatar = localStorage.getItem('selectedAvatar') || 'profil';
            const avatarEl = document.getElementById('profil-gif-page');

            function getAvatarUrl(avatarStr) {
                if (!avatarStr) return '';
                if (avatarStr.startsWith('http://') || avatarStr.startsWith('https://')) {
                    return avatarStr;
                }
                if (avatarStr.endsWith('.gif') || avatarStr.includes('/')) {
                    return avatarStr.startsWith('/') ? avatarStr : '/' + avatarStr;
                }
                return '/game_pacu/assets/image/ui/' + avatarStr + '.gif';
            }

            if (avatarEl) {
                const finalAvatar = dbAvatar || selectedAvatar;
                avatarEl.src = getAvatarUrl(finalAvatar) || '/game_pacu/assets/image/ui/profil.gif';
            }
        })();
    </script>
</body>

</html>