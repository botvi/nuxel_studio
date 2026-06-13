@extends('layouts.game')

@section('title', 'Franchise Game — Shop')

@push('styles')
<style>
    #game-container canvas {
        z-index: 1;
        image-rendering: pixelated;
        image-rendering: crisp-edges;
    }
</style>
@endpush

@section('content')
<!-- Page content will be populated inside game-container parent by Phaser -->
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/phaser@3.88.2/dist/phaser.min.js"></script>
<script>
{
    const GAME_WIDTH = 360;
    const GAME_HEIGHT = 760;

    // =====================================================
    //  HELPER — Shimmer pada PNG icon (BitmapMask)
    // =====================================================
    function addIconShimmer(scene, img, delay) {
        // Disabled for performance
        return;
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
    //  HELPER — Item Card Creator
    // =====================================================
    function makeItemCard(scene, x, y, width, height, itemId, itemName, itemDesc, priceKP, filename, onBuyOrDownload) {
        const container = scene.add.container(x, y);
        container.setSize(width, height);

        const bg = scene.add.graphics();
        const radius = 16;
        const shadowOffset = 4;

        function drawCardBody(fill, border) {
            bg.clear();
            // Main panel
            bg.fillStyle(fill, 1);
            bg.lineStyle(3, border, 1);
            bg.fillRoundedRect(-width / 2, -height / 2, width, height, radius);
            bg.strokeRoundedRect(-width / 2, -height / 2, width, height, radius);
        }

        const defaultFill = 0xffffff;
        const defaultBorder = 0x93c5fd; // soft blue
        const hoverFill = 0xf0f9ff; // glass light blue
        const hoverBorder = 0x3b82f6; // vibrant blue

        drawCardBody(defaultFill, defaultBorder);
        container.add(bg);

        // Hover area zone
        const hoverArea = scene.add.zone(0, 0, width, height).setInteractive({ useHandCursor: true });
        container.add(hoverArea);

        // Icon container
        const iconContainer = scene.add.container(0, -32);
        const iconGfx = scene.add.graphics();

        // Draw a document outline
        const docW = 28;
        const docH = 36;
        iconGfx.fillStyle(0xdbeafe, 1);
        iconGfx.lineStyle(2, 0x3b82f6, 1);
        iconGfx.beginPath();
        iconGfx.moveTo(-docW / 2, -docH / 2);
        iconGfx.lineTo(docW / 2 - 8, -docH / 2);
        iconGfx.lineTo(docW / 2, -docH / 2 + 8);
        iconGfx.lineTo(docW / 2, docH / 2);
        iconGfx.lineTo(-docW / 2, docH / 2);
        iconGfx.closePath();
        iconGfx.fillPath();
        iconGfx.strokePath();

        // Fold corner
        iconGfx.fillStyle(0x93c5fd, 1);
        iconGfx.beginPath();
        iconGfx.moveTo(docW / 2 - 8, -docH / 2);
        iconGfx.lineTo(docW / 2 - 8, -docH / 2 + 8);
        iconGfx.lineTo(docW / 2, -docH / 2 + 8);
        iconGfx.closePath();
        iconGfx.fillPath();
        iconGfx.strokePath();

        // Draw line details
        iconGfx.lineStyle(1.5, 0x3b82f6, 0.6);
        iconGfx.lineBetween(-8, -4, 8, -4);
        iconGfx.lineBetween(-8, 2, 8, 2);
        iconGfx.lineBetween(-8, 8, 2, 8);

        // Draw small download arrow in green
        iconGfx.fillStyle(0x10b981, 1);
        iconGfx.lineStyle(2, 0x10b981, 1);
        iconGfx.lineBetween(6, 6, 6, 12);
        iconGfx.beginPath();
        iconGfx.moveTo(4, 10);
        iconGfx.lineTo(6, 12);
        iconGfx.lineTo(8, 10);
        iconGfx.closePath();
        iconGfx.fillPath();

        iconContainer.add(iconGfx);
        container.add(iconContainer);

        addIconShimmer(scene, iconGfx, Math.random() * 800 + 400);

        // Item Name
        const nameTxt = scene.add.text(0, 10, itemName, {
            fontFamily: '"Press Start 2P", monospace',
            fontSize: '8px',
            fontStyle: 'bold',
            color: '#1e3a8a',
            align: 'center'
        }).setOrigin(0.5);
        container.add(nameTxt);

        // Item Description
        const descTxt = scene.add.text(0, 30, itemDesc, {
            fontFamily: '"Pixelify Sans", monospace',
            fontSize: '9.5px',
            color: '#64748b',
            align: 'center',
            wordWrap: { width: width - 18 }
        }).setOrigin(0.5);
        container.add(descTxt);

        // Action Button Container (Buy/Download)
        const btnW = 110;
        const btnH = 26;
        const btnContainer = scene.add.container(0, 60);
        btnContainer.setSize(btnW, btnH);
        btnContainer.setInteractive({ useHandCursor: true });

        const btnBg = scene.add.graphics();
        const btnTxt = scene.add.text(0, 0, '', {
            fontFamily: '"Press Start 2P", monospace',
            fontSize: '7px',
            color: '#ffffff'
        }).setOrigin(0.5);

        btnContainer.add(btnBg);
        btnContainer.add(btnTxt);
        container.add(btnContainer);

        container.updateState = (isUnlocked) => {
            btnBg.clear();
            if (isUnlocked) {
                // Blue theme for Download
                btnBg.fillStyle(0x0284c7, 0.4);
                btnBg.fillRoundedRect(-btnW / 2 + 2, -btnH / 2 + 2, btnW, btnH, 8);

                btnBg.fillStyle(0x0ea5e9, 1);
                btnBg.lineStyle(2, 0x0284c7, 1);
                btnBg.fillRoundedRect(-btnW / 2, -btnH / 2, btnW, btnH, 8);
                btnBg.strokeRoundedRect(-btnW / 2, -btnH / 2, btnW, btnH, 8);

                btnTxt.setText("DOWNLOAD");
            } else {
                // Green theme for purchase
                btnBg.fillStyle(0x15803d, 0.4);
                btnBg.fillRoundedRect(-btnW / 2 + 2, -btnH / 2 + 2, btnW, btnH, 8);

                btnBg.fillStyle(0x22c55e, 1);
                btnBg.lineStyle(2, 0x15803d, 1);
                btnBg.fillRoundedRect(-btnW / 2, -btnH / 2, btnW, btnH, 8);
                btnBg.strokeRoundedRect(-btnW / 2, -btnH / 2, btnW, btnH, 8);

                btnTxt.setText(`🔒 ${priceKP} KP`);
            }
        };

        // Hover events
        hoverArea.on('pointerover', () => {
            drawCardBody(hoverFill, hoverBorder);
            scene.tweens.add({ targets: container, scaleX: 1.04, scaleY: 1.04, duration: 80 });
        });
        hoverArea.on('pointerout', () => {
            drawCardBody(defaultFill, defaultBorder);
            scene.tweens.add({ targets: container, scaleX: 1, scaleY: 1, duration: 80 });
        });

        btnContainer.on('pointerover', () => {
            btnContainer.setScale(1.05);
        });
        btnContainer.on('pointerout', () => {
            btnContainer.setScale(1);
        });

        let activeAction = false;
        btnContainer.on('pointerdown', () => {
            btnBg.y = 2; btnTxt.y = 2;
        });
        btnContainer.on('pointerup', () => {
            btnBg.y = 0; btnTxt.y = 0;
            if (activeAction) return;

            activeAction = true;
            scene.tweens.add({
                targets: container,
                scaleX: 0.9, scaleY: 0.9,
                duration: 80, yoyo: true,
                onComplete: () => {
                    activeAction = false;
                    onBuyOrDownload(itemId, itemName, priceKP, filename, container);
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
            this.load.image('bgmenu', '/game_pacu/assets/image/bg/bgmenu.jpg');
            this.load.image('koin', '/game_pacu/assets/image/ui/koin.png');
            this.load.image('back', '/game_pacu/assets/image/ui/back.png');
            this.load.image('bubblechat', '/game_pacu/assets/image/ui/bubblechat.png');
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

            this.cameras.main.fadeIn(500, 15, 23, 42);

            // ---- Background cover ----
            const bg = this.add.image(cx, H / 2, 'bgmenu');
            const scaleX_bg = W / bg.width;
            const scaleY_bg = H / bg.height;
            bg.setScale(Math.max(scaleX_bg, scaleY_bg));

            // =============================================
            //  HEADER BAR & NAV
            // =============================================
            // ---- Tombol Kembali (Top Left) ----
            const backBtnContainer = this.add.container(32, 34);
            backBtnContainer.setSize(36, 36);
            backBtnContainer.setInteractive({ useHandCursor: true });

            // Icon kembali (display size 36x36)
            const backIcon = this.add.image(0, 0, 'back').setDisplaySize(36, 36);
            backBtnContainer.add(backIcon);

            // Animasi bounce + aksi klik kembali
            backBtnContainer.on('pointerdown', () => {
                this.tweens.add({
                    targets: backBtnContainer,
                    scaleX: 0.9, scaleY: 0.9,
                    duration: 80, ease: 'Power2',
                    yoyo: true,
                    onComplete: () => {
                        this.cameras.main.fadeOut(300, 15, 23, 42);
                        this.cameras.main.once('camerafadeoutcomplete', () => {
                            window.navigateToPage('/main-menu');
                        });
                    }
                });
            });

            // Hover effect
            backBtnContainer.on('pointerover', () => {
                this.tweens.add({ targets: backBtnContainer, scaleX: 1.05, scaleY: 1.05, duration: 90, ease: 'Power2' });
            });
            backBtnContainer.on('pointerout', () => {
                this.tweens.add({ targets: backBtnContainer, scaleX: 1, scaleY: 1, duration: 90, ease: 'Power2' });
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
            let coinCount = {{ auth()->user()->kuansing_poin }};
            this.coinCount = coinCount;

            const coinText = this.add.text(COIN_ICON_X + 22, BAR_Y + 1, String(this.coinCount), {
                fontFamily: '"Pixelify Sans", monospace',
                fontSize: '13px',
                fontStyle: 'bold',
                color: '#FFD700',
                stroke: '#15803d',
                strokeThickness: 3
            }).setOrigin(0, 0.5);

            // Fetch database customization details
            this.unlocked_template_corak = false;
            this.unlocked_template_lambai = false;
            this.customizationData = null;

            fetch('/tukang-jaluar/get')
                .then(res => res.json())
                .then(data => {
                    this.customizationData = data;
                    if (data.coins !== undefined) {
                        this.coinCount = data.coins;
                        coinText.setText(String(this.coinCount));
                    }
                    this.unlocked_template_corak = !!data.unlocked_template_corak;
                    this.unlocked_template_lambai = !!data.unlocked_template_lambai;
                    if (this.updateItemCards) this.updateItemCards();
                })
                .catch(err => {
                    console.error('Failed to load customizations:', err);
                });

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
                color: '#ffffff',
                stroke: '#0f172a',
                strokeThickness: 3,
                align: 'center',
                lineSpacing: 4
            }).setOrigin(0.5);
            bannerContainer.add(bannerDesc);

            // =============================================
            //  SLIDE SWITCHER (TAB BAR)
            // =============================================
            const tabContainer = this.add.container(cx, 215);
            const tabBg = this.add.graphics();
            tabBg.fillStyle(0x0f172a, 0.6);
            tabBg.lineStyle(2, 0x22c55e, 0.4);
            tabBg.fillRoundedRect(-158, -17, 316, 34, 10);
            tabBg.strokeRoundedRect(-158, -17, 316, 34, 10);
            tabContainer.add(tabBg);

            const activeTabGfx = this.add.graphics();
            const drawActiveTab = (xPos) => {
                activeTabGfx.clear();
                activeTabGfx.fillStyle(0x22c55e, 0.95);
                activeTabGfx.fillRoundedRect(xPos - 77, -14, 154, 28, 8);
                activeTabGfx.fillStyle(0xffffff, 0.15);
                activeTabGfx.fillRoundedRect(xPos - 77, -14, 154, 10, { tl: 8, tr: 8, bl: 0, br: 0 });
            };
            drawActiveTab(-79);
            tabContainer.add(activeTabGfx);

            const tabKoinTxt = this.add.text(-79, 0, 'BELI KOIN', {
                fontFamily: '"Press Start 2P", monospace',
                fontSize: '9px',
                fontStyle: 'bold',
                color: '#ffffff'
            }).setOrigin(0.5);

            const tabItemTxt = this.add.text(79, 0, 'BELI ITEM', {
                fontFamily: '"Press Start 2P", monospace',
                fontSize: '9px',
                fontStyle: 'bold',
                color: '#94a3b8'
            }).setOrigin(0.5);

            tabContainer.add(tabKoinTxt);
            tabContainer.add(tabItemTxt);

            const hitKoin = this.add.zone(-79, 0, 158, 34).setInteractive({ useHandCursor: true });
            const hitItem = this.add.zone(79, 0, 158, 34).setInteractive({ useHandCursor: true });
            tabContainer.add(hitKoin);
            tabContainer.add(hitItem);

            // Setup Containers for slides
            this.coinSlideContainer = this.add.container(0, 0);
            this.itemSlideContainer = this.add.container(0, 0);
            this.itemSlideContainer.setAlpha(0).setVisible(false);

            // =============================================
            //  TOP UP CARDS GRID (SLIDE 1)
            // =============================================
            const cardWidth = 140;
            const cardHeight = 166;
            const startY = 320;
            const rowGap = 180;

            const handlePurchase = (kpAmount, priceString, priceVal, cardSource) => {
                showConfirmModal(this, `Apakah Anda ingin membeli +${kpAmount} KP seharga ${priceString}?`, () => {
                    this.cameras.main.flash(400, 34, 197, 94, 0.18);

                    fetch('/shop/add-points', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ points: kpAmount })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const nextCoins = data.kuansing_poin;
                            this.coinCount = nextCoins;
                            localStorage.setItem('coins', String(nextCoins));

                            if (this.customizationData) {
                                this.customizationData.coins = nextCoins;
                            }

                            const startX = cardSource.x;
                            const startY = cardSource.y;

                            for (let i = 0; i < 12; i++) {
                                this.time.delayedCall(i * 60, () => {
                                    const particle = this.add.image(startX, startY, 'koin')
                                        .setDisplaySize(20, 20)
                                        .setDepth(10);

                                    const randX = startX + Phaser.Math.Between(-30, 30);
                                    const randY = startY + Phaser.Math.Between(-30, 30);

                                    this.tweens.add({
                                        targets: particle,
                                        x: randX,
                                        y: randY,
                                        duration: 180,
                                        ease: 'Quad.easeOut',
                                        onComplete: () => {
                                            this.tweens.add({
                                                targets: particle,
                                                x: COIN_ICON_X,
                                                y: BAR_Y,
                                                duration: 550,
                                                ease: 'Cubic.easeIn',
                                                onComplete: () => {
                                                    particle.destroy();

                                                    const displayedCoins = parseInt(coinText.text);
                                                    const difference = nextCoins - displayedCoins;
                                                    coinText.setText(String(displayedCoins + Math.ceil(difference * 0.22)));

                                                    this.tweens.add({
                                                        targets: [coinImgGlobal, coinText],
                                                        scaleX: 1.25, scaleY: 1.25,
                                                        duration: 70, yoyo: true,
                                                        onComplete: () => {
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
                        } else {
                            alert('Topup gagal.');
                        }
                    })
                    .catch(err => {
                        console.error('Error topup:', err);
                        alert('Gagal memproses topup.');
                    });
                });
            };

            const coinCard1 = makeTopupCard(this, cx - 80, startY, cardWidth, cardHeight, 50, 'Rp 5.000', 5000, handlePurchase);
            const coinCard2 = makeTopupCard(this, cx + 80, startY, cardWidth, cardHeight, 100, 'Rp 10.000', 10000, handlePurchase);
            const coinCard3 = makeTopupCard(this, cx - 80, startY + rowGap, cardWidth, cardHeight, 200, 'Rp 20.000', 20000, handlePurchase);
            const coinCard4 = makeTopupCard(this, cx + 80, startY + rowGap, cardWidth, cardHeight, 500, 'Rp 50.000', 50000, handlePurchase);

            this.coinSlideContainer.add([coinCard1, coinCard2, coinCard3, coinCard4]);

            // =============================================
            //  ITEM SHOP (SLIDE 2)
            // =============================================
            const itemStartY = 330;
            const itemCardWidth = 140;
            const itemCardHeight = 186;

            const handleBuyOrDownload = (itemId, itemName, priceKP, filename, cardSource) => {
                const isUnlocked = (itemId === 'corak') ? this.unlocked_template_corak : this.unlocked_template_lambai;

                if (isUnlocked) {
                    const link = document.createElement('a');
                    link.href = `/game_pacu/assets/template/${filename}`;
                    link.download = filename;
                    link.click();
                } else {
                    showConfirmModal(this, `Beli ${itemName} seharga ${priceKP} KP?`, () => {
                        if (this.coinCount < priceKP) {
                            showConfirmModal(this, "Koin KP tidak cukup! Ingin top up koin?", () => {
                                switchSlide(0);
                            });
                        } else {
                            this.cameras.main.flash(400, 34, 197, 94, 0.18);

                            this.coinCount -= priceKP;
                            if (itemId === 'corak') {
                                this.unlocked_template_corak = true;
                            } else {
                                this.unlocked_template_lambai = true;
                            }

                            localStorage.setItem('coins', String(this.coinCount));
                            coinText.setText(String(this.coinCount));
                            this.updateItemCards();

                            if (this.customizationData) {
                                this.customizationData.coins = this.coinCount;
                                if (itemId === 'corak') {
                                    this.customizationData.unlocked_template_corak = true;
                                } else {
                                    this.customizationData.unlocked_template_lambai = true;
                                }

                                fetch('/tukang-jaluar/save', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify(this.customizationData)
                                })
                                .then(res => res.json())
                                .then(data => {
                                    console.log('Purchase saved:', data);
                                })
                                .catch(err => {
                                    console.error('Error saving purchase:', err);
                                });
                            }

                            const successLabel = this.add.text(cx, H - 100, 'PEMBELIAN BERHASIL!', {
                                fontFamily: '"Press Start 2P", monospace',
                                fontSize: '9px',
                                color: '#ffffff',
                                stroke: '#1e3a8a',
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
                        }
                    });
                }
            };

            const itemCard1 = makeItemCard(this, cx - 80, itemStartY, itemCardWidth, itemCardHeight, 'corak', 'TEMPLATE CORAK', 'Desain body perahu\ncustom sanak!', 100, 'template_corak.png', handleBuyOrDownload);
            const itemCard2 = makeItemCard(this, cx + 80, itemStartY, itemCardWidth, itemCardHeight, 'lambai', 'TEMPLATE LAMBAI', 'Hiasan melambai\nekor perahu!', 250, 'template_lambai.png', handleBuyOrDownload);

            this.itemSlideContainer.add([itemCard1, itemCard2]);

            this.updateItemCards = () => {
                itemCard1.updateState(this.unlocked_template_corak);
                itemCard2.updateState(this.unlocked_template_lambai);
            };
            this.updateItemCards();

            // =============================================
            //  SWITCH SLIDE LOGIC
            // =============================================
            this.currentSlide = 0; 

            const switchSlide = (slideIndex) => {
                if (this.currentSlide === slideIndex) return;
                this.currentSlide = slideIndex;

                if (window.playClickSound) window.playClickSound();

                const targetX = (slideIndex === 0) ? -79 : 79;
                this.tweens.add({
                    targets: { x: activeTabGfx.x },
                    x: targetX,
                    duration: 250,
                    ease: 'Cubic.easeOut',
                    onUpdate: (tween, target) => {
                        drawActiveTab(target.x);
                    }
                });

                if (slideIndex === 0) {
                    bannerTitle.setText('✦ APA ITU KP? ✦');
                    bannerDesc.setText('KP (Kuansing Poin) adalah mata uang utama\ngame ini. Kamu bisa mendapatkan KP secara gratis\nsaat bermain di dalam game, atau melakukan\ntopup langsung melalui paket di bawah ini!');

                    tabKoinTxt.setColor('#ffffff');
                    tabItemTxt.setColor('#94a3b8');

                    this.coinSlideContainer.setVisible(true);
                    this.tweens.add({
                        targets: this.coinSlideContainer,
                        alpha: 1,
                        scaleX: 1, scaleY: 1,
                        duration: 200,
                        ease: 'Quad.easeOut'
                    });

                    this.tweens.add({
                        targets: this.itemSlideContainer,
                        alpha: 0,
                        scaleX: 0.95, scaleY: 0.95,
                        duration: 200,
                        ease: 'Quad.easeIn',
                        onComplete: () => {
                            if (this.currentSlide === 0) {
                                this.itemSlideContainer.setVisible(false);
                            }
                        }
                    });
                } else {
                    bannerTitle.setText('✦ BELI ITEM PREMIUM ✦');
                    bannerDesc.setText('Gunakan KP (Kuansing Poin) milikmu untuk membeli\ndan mengunduh berbagai template kustomisasi premium\nagar tampilan perahumu semakin keren di arena pacu!');

                    tabKoinTxt.setColor('#94a3b8');
                    tabItemTxt.setColor('#ffffff');

                    this.itemSlideContainer.setVisible(true);
                    this.tweens.add({
                        targets: this.itemSlideContainer,
                        alpha: 1,
                        scaleX: 1, scaleY: 1,
                        duration: 200,
                        ease: 'Quad.easeOut'
                    });

                    this.tweens.add({
                        targets: this.coinSlideContainer,
                        alpha: 0,
                        scaleX: 0.95, scaleY: 0.95,
                        duration: 200,
                        ease: 'Quad.easeIn',
                        onComplete: () => {
                            if (this.currentSlide === 1) {
                                this.coinSlideContainer.setVisible(false);
                            }
                        }
                    });
                }
            };

            hitKoin.on('pointerdown', () => switchSlide(0));
            hitItem.on('pointerdown', () => switchSlide(1));

            // Native event listener to bypass Safari/iOS gesture block for downloads
            const canvas = this.sys.game.canvas;
            const handleNativeInteraction = (e) => {
                if (this.currentSlide !== 1) return;

                const rect = canvas.getBoundingClientRect();
                const clientX = e.clientX !== undefined ? e.clientX : (e.changedTouches && e.changedTouches[0] ? e.changedTouches[0].clientX : undefined);
                const clientY = e.clientY !== undefined ? e.clientY : (e.changedTouches && e.changedTouches[0] ? e.changedTouches[0].clientY : undefined);

                if (clientX === undefined || clientY === undefined) return;

                const x = (clientX - rect.left) * (this.scale.width / rect.width);
                const y = (clientY - rect.top) * (this.scale.height / rect.height);

                const btnY = 390;
                const btnW = 110;
                const btnH = 26;

                const btn1X = cx - 80;
                if (x >= btn1X - btnW / 2 && x <= btn1X + btnW / 2 && y >= btnY - btnH / 2 && y <= btnY + btnH / 2) {
                    if (this.unlocked_template_corak) {
                        const filename = 'template_corak.png';
                        const link = document.createElement('a');
                        link.href = `/game_pacu/assets/template/${filename}`;
                        link.download = filename;
                        link.click();
                    }
                    return;
                }

                const btn2X = cx + 80;
                if (x >= btn2X - btnW / 2 && x <= btn2X + btnW / 2 && y >= btnY - btnH / 2 && y <= btnY + btnH / 2) {
                    if (this.unlocked_template_lambai) {
                        const filename = 'template_lambai.png';
                        const link = document.createElement('a');
                        link.href = `/game_pacu/assets/template/${filename}`;
                        link.download = filename;
                        link.click();
                    }
                    return;
                }
            };

            canvas.addEventListener('click', handleNativeInteraction);
            canvas.addEventListener('touchend', handleNativeInteraction);

            this.events.once('shutdown', () => {
                canvas.removeEventListener('click', handleNativeInteraction);
                canvas.removeEventListener('touchend', handleNativeInteraction);
            });

            // =============================================
            //  BOUNCING COIN ANIMATION
            // =============================================
            const bouncingCoin = this.add.image(W + 100, H - 100, 'koin')
                .setDisplaySize(64, 64)
                .setOrigin(0.5);

            const coinBaseScaleX = bouncingCoin.scaleX;
            const coinBaseScaleY = bouncingCoin.scaleY;

            addIconShimmer(this, bouncingCoin, 600);

            const playImpact = (targetCoin) => {
                if (!targetCoin.active) return;

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

            this.tweens.add({
                targets: bouncingCoin,
                x: cx,
                duration: 1600,
                ease: 'Linear',
                onComplete: () => {
                    this.tweens.add({
                        targets: bubble,
                        scaleX: 1,
                        scaleY: 1,
                        duration: 350,
                        ease: 'Back.easeOut'
                    });

                    const bubbleFloat = this.tweens.add({
                        targets: bubble,
                        y: H - 189,
                        duration: 800,
                        yoyo: true,
                        repeat: -1,
                        ease: 'Sine.easeInOut'
                    });

                    const coinFloat = this.tweens.add({
                        targets: bouncingCoin,
                        y: H - 105,
                        duration: 800,
                        yoyo: true,
                        repeat: -1,
                        ease: 'Sine.easeInOut'
                    });

                    this.time.delayedCall(5000, () => {
                        bubbleFloat.stop();
                        coinFloat.stop();

                        bubble.y = H - 185;
                        bouncingCoin.y = H - 100;

                        this.tweens.add({
                            targets: bubble,
                            scaleX: 0,
                            scaleY: 0,
                            duration: 300,
                            ease: 'Back.easeIn'
                        });

                        this.tweens.add({
                            targets: bouncingCoin,
                            x: -100,
                            duration: 1600,
                            ease: 'Linear',
                            onComplete: () => {
                                bouncingCoin.destroy();
                                bubble.destroy();
                            }
                        });

                        this.tweens.add({
                            targets: bouncingCoin,
                            y: H - 160,
                            duration: 200,
                            yoyo: true,
                            repeat: 3,
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

            this.tweens.add({
                targets: bouncingCoin,
                y: H - 160,
                duration: 200,
                yoyo: true,
                repeat: 3,
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
    window.activeShopGame = new Phaser.Game({
        type: Phaser.AUTO,
        width: GAME_WIDTH,
        height: GAME_HEIGHT,
        backgroundColor: '#0f172a',
        parent: 'game-container',
        pixelArt: true,
        scene: [TopupScene],
        scale: {
            mode: Phaser.Scale.RESIZE,
            autoCenter: Phaser.Scale.CENTER_BOTH
        }
    });

    // Cleanup Phaser Game instance on navigation to prevent memory leaks
    document.addEventListener('livewire:navigating', () => {
        if (window.activeShopGame) {
            window.activeShopGame.destroy(true);
            window.activeShopGame = null;
            console.log('Shop page active Phaser game destroyed.');
        }
    }, { once: true });
}
</script>
@endpush
