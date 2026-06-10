<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Franchise Game — Topup KP</title>
    <link rel="stylesheet" href="../assets/css/game-layout.css">
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
        //  HELPER — Topup Option Card Creator
        // =====================================================
        function makeTopupCard(scene, x, y, width, height, kpAmount, priceString, priceVal, onBuyComplete) {
            const container = scene.add.container(x, y);
            container.setSize(width, height);

            const bg = scene.add.graphics();
            const radius = 16;
            const shadowOffset = 4;

            function drawCardBody(fill, border) {
                bg.clear();
                // Shadow
                bg.fillStyle(0x15803d, 0.3);
                bg.fillRoundedRect(-width / 2 + shadowOffset, -height / 2 + shadowOffset, width, height, radius);
                // Main panel
                bg.fillStyle(fill, 1);
                bg.lineStyle(3, border, 1);
                bg.fillRoundedRect(-width / 2, -height / 2, width, height, radius);
                bg.strokeRoundedRect(-width / 2, -height / 2, width, height, radius);
            }

            // Palette: Sleek Green / Emerald
            const defaultFill = 0xffffff;
            const defaultBorder = 0x86efac;
            const hoverFill = 0xf0fdf4;
            const hoverBorder = 0x22c55e;

            drawCardBody(defaultFill, defaultBorder);
            container.add(bg);

            // Card interactive hover effect
            const hoverArea = scene.add.zone(0, 0, width, height)
                .setInteractive({ useHandCursor: true });
            container.add(hoverArea);

            // Coin icon scaling based on package amount
            let coinScale = 1.0;
            if (kpAmount >= 5000) coinScale = 1.6;
            else if (kpAmount >= 2000) coinScale = 1.3;
            else if (kpAmount >= 500) coinScale = 1.15;
            else coinScale = 0.95;

            const coinImg = scene.add.image(0, -26, 'koin')
                .setDisplaySize(36 * coinScale, 36 * coinScale);
            container.add(coinImg);

            addIconShimmer(scene, coinImg, Math.random() * 800 + 400);

            // Amount text (Yellow Golden style)
            const amountTxt = scene.add.text(0, 16, `+${kpAmount} KP`, {
                fontFamily: '"Pixelify Sans", monospace',
                fontSize: '18px',
                fontStyle: 'bold',
                color: '#d97706', // gold-darker
                stroke: '#fef3c7',
                strokeThickness: 3
            }).setOrigin(0.5);
            container.add(amountTxt);

            // Price label
            const priceTxt = scene.add.text(0, 36, priceString, {
                fontFamily: '"Pixelify Sans", monospace',
                fontSize: '12px',
                fontStyle: 'bold',
                color: '#15803d'
            }).setOrigin(0.5);
            container.add(priceTxt);

            // Buy Button inside card
            const btnW = 94;
            const btnH = 26;
            const btnContainer = scene.add.container(0, 60);
            btnContainer.setSize(btnW, btnH);
            btnContainer.setInteractive({ useHandCursor: true });

            const btnBg = scene.add.graphics();
            function drawBtn(fill, border) {
                btnBg.clear();
                btnBg.fillStyle(0x16a34a, 0.4);
                btnBg.fillRoundedRect(-btnW / 2 + 2, -btnH / 2 + 2, btnW, btnH, 8);
                btnBg.fillStyle(fill, 1);
                btnBg.lineStyle(2, border, 1);
                btnBg.fillRoundedRect(-btnW / 2, -btnH / 2, btnW, btnH, 8);
                btnBg.strokeRoundedRect(-btnW / 2, -btnH / 2, btnW, btnH, 8);
            }

            const btnFill = 0x22c55e;
            const btnBorder = 0x16a34a;
            const btnHoverFill = 0x4ade80;
            const btnHoverBorder = 0x22c55e;

            drawBtn(btnFill, btnBorder);
            btnContainer.add(btnBg);

            const btnTxt = scene.add.text(0, 0, 'BELI', {
                fontFamily: '"Press Start 2P", monospace',
                fontSize: '8px',
                color: '#ffffff',
                stroke: '#15803d',
                strokeThickness: 2
            }).setOrigin(0.5);
            btnContainer.add(btnTxt);
            container.add(btnContainer);

            // Hover events for the card
            hoverArea.on('pointerover', () => {
                drawCardBody(hoverFill, hoverBorder);
                scene.tweens.add({ targets: container, scaleX: 1.04, scaleY: 1.04, duration: 80 });
            });
            hoverArea.on('pointerout', () => {
                drawCardBody(defaultFill, defaultBorder);
                scene.tweens.add({ targets: container, scaleX: 1, scaleY: 1, duration: 80 });
            });

            // Hover events for button specifically
            btnContainer.on('pointerover', () => {
                drawBtn(btnHoverFill, btnHoverBorder);
                btnContainer.setScale(1.05);
            });
            btnContainer.on('pointerout', () => {
                drawBtn(btnFill, btnBorder);
                btnContainer.setScale(1);
            });

            let activeBuy = false;
            btnContainer.on('pointerdown', () => {
                btnBg.y = 2; btnTxt.y = 2;
            });
            btnContainer.on('pointerup', () => {
                btnBg.y = 0; btnTxt.y = 0;
                if (activeBuy) return;

                activeBuy = true;
                scene.tweens.add({
                    targets: container,
                    scaleX: 0.9, scaleY: 0.9,
                    duration: 80, yoyo: true,
                    onComplete: () => {
                        activeBuy = false;
                        onBuyComplete(kpAmount, priceString, priceVal, container);
                    }
                });
            });

            return container;
        }

        // =====================================================
        //  HELPER — Confirmation Modal
        // =====================================================
        function showConfirmModal(scene, text, onConfirm) {
            const W = scene.scale.width;
            const H = scene.scale.height;

            const overlay = scene.add.graphics();
            overlay.fillStyle(0x000000, 0.65);
            overlay.fillRect(0, 0, W, H);
            overlay.setInteractive(new Phaser.Geom.Rectangle(0, 0, W, H), Phaser.Geom.Rectangle.Contains);

            const dialog = scene.add.container(W / 2, H / 2);

            const dW = 270;
            const dH = 150;

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

            // Yes Button (Confirm)
            const btnYes = scene.add.container(-60, 40);
            btnYes.setSize(90, 30);
            btnYes.setInteractive({ useHandCursor: true });

            const btnYesBg = scene.add.graphics();
            btnYesBg.fillStyle(0x22c55e, 1);
            btnYesBg.lineStyle(2, 0x16a34a, 1);
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
            const btnNo = scene.add.container(60, 40);
            btnNo.setSize(90, 30);
            btnNo.setInteractive({ useHandCursor: true });

            const btnNoBg = scene.add.graphics();
            btnNoBg.fillStyle(0xef4444, 1);
            btnNoBg.lineStyle(2, 0xdc2626, 1);
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

            // Button interactions
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

            // Fade-in animation
            dialog.setScale(0);
            scene.tweens.add({
                targets: dialog,
                scaleX: 1, scaleY: 1,
                duration: 220,
                ease: 'Back.easeOut'
            });
        }

        // =====================================================
        //  TOP UP SCENE CLASS
        // =====================================================
        class TopupScene extends Phaser.Scene {
            constructor() { super({ key: 'TopupScene' }); }

            preload() {
                this.load.image('bgmenu', '../assets/image/bg/bgmenu.jpg');
                this.load.image('koin', '../assets/image/ui/koin.png');
                this.load.image('back', '../assets/image/ui/back.png');
                this.load.image('bubblechat', '../assets/image/ui/bubblechat.png');
            }

            create() {
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

                // Background kotak transparan (rounded rectangle)
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

                // Icon kembali diperbesar di tengah
                const backIcon = this.add.image(0, 0, 'back').setDisplaySize(32, 32);
                backBtnContainer.add(backIcon);

                // Animasi bounce + aksi klik kembali
                backBtnContainer.on('pointerdown', () => {
                    this.tweens.add({
                        targets: backBtnContainer,
                        scaleX: 0.8, scaleY: 0.8,
                        duration: 80, ease: 'Power2',
                        yoyo: true,
                        onComplete: () => {
                            this.cameras.main.fadeOut(300, 240, 253, 244);
                            this.cameras.main.once('camerafadeoutcomplete', () => {
                                window.location.href = '../mainmenu.php';
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

                // Shimmer pada tombol kembali kotak (menggunakan geometry mask rounded rectangle)
                const backMask = this.make.graphics({ add: false });
                backMask.fillStyle(0xffffff);
                backMask.fillRoundedRect(38 - 24, 34 - 24, 48, 48, 8);

                const backShimGfx = this.add.graphics();
                backShimGfx.setMask(backMask.createGeometryMask());

                const backWsp = { v: 14 - 15 - 15 }; // ix - shimW - slant
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
                //  TOP RIGHT: KOIN (seperti di mainmenu.php)
                // =============================================
                const BAR_Y = 34;
                const COIN_ICON_X = W - 78;

                const coinImgGlobal = this.add.image(COIN_ICON_X, BAR_Y, 'koin')
                    .setDisplaySize(36, 36)
                    .setInteractive({ useHandCursor: true });

                // Animasi bounce + aksi klik koin
                coinImgGlobal.on('pointerdown', () => {
                    this.tweens.add({
                        targets: coinImgGlobal,
                        scaleX: 0.7, scaleY: 0.7,
                        duration: 80, ease: 'Power2',
                        yoyo: true
                    });
                });

                let coinCount = parseInt(localStorage.getItem('coins') || '100000');

                const coinText = this.add.text(COIN_ICON_X + 22, BAR_Y + 1, String(coinCount), {
                    fontFamily: '"Pixelify Sans", monospace',
                    fontSize: '13px',
                    fontStyle: 'bold',
                    color: '#FFD700',
                    stroke: '#15803d',
                    strokeThickness: 3
                }).setOrigin(0, 0.5);

                // Shimmer koin — ikuti bentuk PNG via BitmapMask
                addIconShimmer(this, coinImgGlobal, 1100);

                // =============================================
                //  EXPLANATION BANNER
                // =============================================
                const bannerContainer = this.add.container(cx, 134);
                const bW = W - 44;
                const bH = 100;

                const bannerBg = this.add.graphics();
                bannerBg.fillStyle(0x16a34a, 0.25);
                bannerBg.fillRoundedRect(-bW / 2, -bH / 2, bW, bH, 16);
                bannerBg.lineStyle(3, 0x22c55e, 1);
                bannerBg.strokeRoundedRect(-bW / 2, -bH / 2, bW, bH, 16);
                bannerContainer.add(bannerBg);

                const bannerTitle = this.add.text(0, -30, '✦ APA ITU KP? ✦', {
                    fontFamily: '"Press Start 2P", monospace',
                    fontSize: '11px',
                    fontStyle: 'bold',
                    color: '#22c55e',
                    stroke: '#ffffff',
                    strokeThickness: 3
                }).setOrigin(0.5);
                bannerContainer.add(bannerTitle);

                const bannerDesc = this.add.text(0, 10, 'KP (Kuansing Poin) adalah mata uang utama\ngame ini. Kamu bisa mendapatkan KP secara gratis\nsaat bermain di dalam game, atau melakukan\ntopup langsung melalui paket di bawah ini!', {
                    fontFamily: '"Pixelify Sans", monospace',
                    fontSize: '11px',
                    fontStyle: 'bold',
                    color: '#15803d',
                    align: 'center',
                    lineSpacing: 4
                }).setOrigin(0.5);
                bannerContainer.add(bannerDesc);

                // =============================================
                //  TOP UP CARDS GRID
                // =============================================
                const cardWidth = 140;
                const cardHeight = 166;
                const startY = 280;
                const rowGap = 190;

                const handlePurchase = (kpAmount, priceString, priceVal, cardSource) => {
                    showConfirmModal(this, `Apakah Anda ingin membeli +${kpAmount} KP seharga ${priceString}?`, () => {
                        // Confirm purchase
                        // Play sound trigger placeholder or popup tween
                        this.cameras.main.flash(400, 34, 197, 94, 0.18);

                        // Save coins in localstorage
                        const currentCoins = parseInt(localStorage.getItem('coins') || '100000');
                        const nextCoins = currentCoins + kpAmount;
                        localStorage.setItem('coins', String(nextCoins));

                        // Floating coin particles animation (flying to the coin indicator)
                        const startX = cardSource.x;
                        const startY = cardSource.y;

                        for (let i = 0; i < 12; i++) {
                            this.time.delayedCall(i * 60, () => {
                                const particle = this.add.image(startX, startY, 'koin')
                                    .setDisplaySize(20, 20)
                                    .setDepth(10);

                                // Random arc starting coordinates
                                const randX = startX + Phaser.Math.Between(-30, 30);
                                const randY = startY + Phaser.Math.Between(-30, 30);

                                this.tweens.add({
                                    targets: particle,
                                    x: randX,
                                    y: randY,
                                    duration: 180,
                                    ease: 'Quad.easeOut',
                                    onComplete: () => {
                                        // Fly to destination top-right indicator
                                        this.tweens.add({
                                            targets: particle,
                                            x: COIN_ICON_X,
                                            y: BAR_Y,
                                            duration: 550,
                                            ease: 'Cubic.easeIn',
                                            onComplete: () => {
                                                particle.destroy();

                                                // Dynamic count tick
                                                const displayedCoins = parseInt(coinText.text);
                                                const difference = nextCoins - displayedCoins;
                                                // Smooth count ticking towards final value
                                                coinText.setText(String(displayedCoins + Math.ceil(difference * 0.22)));

                                                // Pop scale on coin bar icon
                                                this.tweens.add({
                                                    targets: [coinImgGlobal, coinText],
                                                    scaleX: 1.25, scaleY: 1.25,
                                                    duration: 70, yoyo: true,
                                                    onComplete: () => {
                                                        // Make sure exact end value is shown at the end
                                                        if (i === 11) {
                                                            coinText.setText(String(nextCoins));
                                                        }
                                                    }
                                                });
                                            }
                                        });
                                    }
                                });
                            });
                        }

                        // Show dynamic success floating label
                        const successLabel = this.add.text(cx, H - 100, 'TOPUP BERHASIL! + ' + kpAmount + ' KP', {
                            fontFamily: '"Press Start 2P", monospace',
                            fontSize: '9px',
                            color: '#ffffff',
                            stroke: '#16a34a',
                            strokeThickness: 4,
                            align: 'center'
                        }).setOrigin(0.5).setDepth(20).setScale(0);

                        this.tweens.add({
                            targets: successLabel,
                            scaleX: 1, scaleY: 1,
                            y: `-=40`,
                            duration: 350,
                            ease: 'Back.easeOut',
                            onComplete: () => {
                                this.time.delayedCall(1500, () => {
                                    this.tweens.add({
                                        targets: successLabel,
                                        alpha: 0,
                                        duration: 400,
                                        onComplete: () => successLabel.destroy()
                                    });
                                });
                            }
                        });
                    });
                };

                // Option 1: 50 KP
                makeTopupCard(this, cx - 80, startY, cardWidth, cardHeight, 50, 'Rp 5.000', 5000, handlePurchase);

                // Option 2: 100 KP
                makeTopupCard(this, cx + 80, startY, cardWidth, cardHeight, 100, 'Rp 10.000', 10000, handlePurchase);

                // Option 3: 200 KP
                makeTopupCard(this, cx - 80, startY + rowGap, cardWidth, cardHeight, 200, 'Rp 20.000', 20000, handlePurchase);

                // Option 4: 500 KP
                makeTopupCard(this, cx + 80, startY + rowGap, cardWidth, cardHeight, 500, 'Rp 50.000', 50000, handlePurchase);

                // =============================================
                //  BOUNCING COIN ANIMATION (NO ROLLING, SPLASH PARTICLES ON IMPACT)
                // =============================================
                // Koin di luar layar (W + 100), digeser lebih ke bawah (H - 100)
                const bouncingCoin = this.add.image(W + 100, H - 100, 'koin')
                    .setDisplaySize(64, 64)
                    .setOrigin(0.5);

                const coinBaseScaleX = bouncingCoin.scaleX;
                const coinBaseScaleY = bouncingCoin.scaleY;

                // Tambah shimmer pada koin bounce
                addIconShimmer(this, bouncingCoin, 600);

                // Particle manager untuk koin terpantul (hanya muncul saat menghantam tanah)
                const coinParticles = this.add.particles(0, 0, 'koin', {
                    scale: { start: 0.16, end: 0 },
                    alpha: { start: 0.8, end: 0 },
                    speed: { min: 80, max: 160 },
                    angle: { min: 230, max: 310 }, // Semburan memancar ke atas
                    gravityY: 550, // Simulasi gravitasi menariknya kembali ke tanah
                    lifespan: 450,
                    frequency: -1, // Matikan emisi otomatis
                    emitting: false,
                    blendMode: 'NORMAL'
                });

                // Helper untuk efek hantaman tanah (squash + particle burst)
                const playImpact = (targetCoin) => {
                    if (!targetCoin.active) return;

                    // Ledakan partikel di bawah koin (pada posisi lantai H - 68)
                    coinParticles.explode(8, targetCoin.x, H - 68);

                    // Efek squash & stretch pada koin
                    this.tweens.add({
                        targets: targetCoin,
                        scaleX: coinBaseScaleX * 1.18,
                        scaleY: coinBaseScaleY * 0.82,
                        duration: 60,
                        yoyo: true,
                        ease: 'Quad.easeOut',
                        onComplete: () => {
                            if (targetCoin.active) {
                                targetCoin.scaleX = coinBaseScaleX;
                                targetCoin.scaleY = coinBaseScaleY;
                            }
                        }
                    });
                };

                // Speech bubble container (seperti di Kustomisasi)
                const bubble = this.add.container(cx + 40, H - 185);
                bubble.setScale(0);

                const bubbleBg = this.add.image(0, 0, 'bubblechat').setDisplaySize(136, 66);
                bubble.add(bubbleBg);
                addIconShimmer(this, bubbleBg, 1200);

                const bubbleTxt = this.add.text(0, -2, "Top up KP,\nsanak!", {
                    fontFamily: '"Press Start 2P", monospace',
                    fontSize: '9px',
                    color: '#000000',
                    align: 'center',
                    lineSpacing: 4
                }).setOrigin(0.5);
                bubble.add(bubbleTxt);

                // Slide-in Horisontal untuk Koin (dari kanan ke tengah)
                this.tweens.add({
                    targets: bouncingCoin,
                    x: cx,
                    duration: 1600,
                    ease: 'Linear',
                    onComplete: () => {
                        // Tampilkan balon percakapan dengan efek pop
                        this.tweens.add({
                            targets: bubble,
                            scaleX: 1,
                            scaleY: 1,
                            duration: 350,
                            ease: 'Back.easeOut'
                        });

                        // Idle floating untuk bubble
                        const bubbleFloat = this.tweens.add({
                            targets: bubble,
                            y: H - 189,
                            duration: 800,
                            yoyo: true,
                            repeat: -1,
                            ease: 'Sine.easeInOut'
                        });

                        // Idle floating untuk Koin (pantulan kecil statis saat diam)
                        const coinFloat = this.tweens.add({
                            targets: bouncingCoin,
                            y: H - 105,
                            duration: 800,
                            yoyo: true,
                            repeat: -1,
                            ease: 'Sine.easeInOut'
                        });

                        // Tunggu 5 detik sebelum berjalan keluar ke kiri
                        this.time.delayedCall(5000, () => {
                            // Hentikan float tweens
                            bubbleFloat.stop();
                            coinFloat.stop();

                            // Reset posisi stabil
                            bubble.y = H - 185;
                            bouncingCoin.y = H - 100;

                            // Hilangkan bubble chat
                            this.tweens.add({
                                targets: bubble,
                                scaleX: 0,
                                scaleY: 0,
                                duration: 300,
                                ease: 'Back.easeIn'
                            });

                            // Slide-out Horisontal ke kiri (X = -100)
                            this.tweens.add({
                                targets: bouncingCoin,
                                x: -100,
                                duration: 1600,
                                ease: 'Linear',
                                onComplete: () => {
                                    bouncingCoin.destroy();
                                    bubble.destroy();
                                    coinParticles.destroy();
                                }
                            });

                            // Bouncing tween saat keluar ke kiri (yoyo repeat untuk mensimulasikan pantulan)
                            this.tweens.add({
                                targets: bouncingCoin,
                                y: H - 160,
                                duration: 200,
                                yoyo: true,
                                repeat: 3, // 4 pantulan
                                ease: 'Power1.easeOut',
                                onRepeat: () => {
                                    playImpact(bouncingCoin);
                                },
                                onComplete: () => {
                                    playImpact(bouncingCoin);
                                }
                            });
                        });
                    }
                });

                // Bouncing tween saat masuk ke kanan (yoyo repeat untuk mensimulasikan pantulan)
                this.tweens.add({
                    targets: bouncingCoin,
                    y: H - 160,
                    duration: 200,
                    yoyo: true,
                    repeat: 3, // 4 pantulan
                    ease: 'Power1.easeOut',
                    onRepeat: () => {
                        playImpact(bouncingCoin);
                    },
                    onComplete: () => {
                        playImpact(bouncingCoin);
                    }
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
            scene: [TopupScene],
            scale: {
                mode: Phaser.Scale.RESIZE,
                autoCenter: Phaser.Scale.CENTER_BOTH
            }
        });
    </script>

    <script src="../assets/js/game-layout.js?v=<?= time() ?>"></script>
</body>

</html>