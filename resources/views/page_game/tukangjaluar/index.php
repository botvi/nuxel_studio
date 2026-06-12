<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title>Franchise Game — Kustomisasi Grid</title>
    <link rel="stylesheet" href="/game_pacu/assets/css/game-layout.css">
    <style>
        /* Phaser canvas di bawah overlay */
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
            <div id="game-container"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/phaser@3.88.2/dist/phaser.min.js"></script>

    <script>
        const GAME_WIDTH = 360;
        const GAME_HEIGHT = 760;

        // =====================================================
        //  HELPER — Tombol Pixel 3D Rounded (Kembali ke Menu)
        // =====================================================
        function makeBackButton(scene, cx, cy, bw, bh, radius, label, colors, onTap) {
            const halfW = bw / 2;
            const halfH = bh / 2;
            const shadowOffset = 6;

            const container = scene.add.container(cx, cy);
            container.setSize(bw, bh);
            container.setInteractive({ useHandCursor: true });

            const shadowGfx = scene.add.graphics();
            shadowGfx.fillStyle(colors.shadow, 1);
            shadowGfx.fillRoundedRect(-halfW + shadowOffset, -halfH + shadowOffset, bw, bh, radius);
            container.add(shadowGfx);

            const bodyGfx = scene.add.graphics();
            function drawBody(fill, border) {
                bodyGfx.clear();
                bodyGfx.fillStyle(fill, 1);
                bodyGfx.lineStyle(4, border, 1);
                bodyGfx.fillRoundedRect(-halfW, -halfH, bw, bh, radius);
                bodyGfx.strokeRoundedRect(-halfW, -halfH, bw, bh, radius);
            }
            drawBody(colors.fill, colors.border);
            container.add(bodyGfx);

            const shineGfx = scene.add.graphics();
            shineGfx.fillStyle(0xffffff, 0.22);
            shineGfx.fillRoundedRect(-halfW + 8, -halfH + 5, bw - 16, bh * 0.35,
                { tl: radius - 4, tr: radius - 4, bl: 0, br: 0 });
            container.add(shineGfx);

            const lbl = scene.add.text(0, 0, label, {
                fontFamily: '"Pixelify Sans", monospace',
                fontSize: '22px',
                fontStyle: 'bold',
                color: colors.textColor,
                stroke: colors.stroke || '#0369a1',
                strokeThickness: 5,
                align: 'center'
            }).setOrigin(0.5);
            container.add(lbl);

            let isPressed = false;

            container.on('pointerover', () => {
                if (isPressed) return;
                drawBody(colors.hoverFill, colors.hoverBorder);
                lbl.setColor(colors.hoverText);
                // Removed heavy blur shadow glow for performance
                scene.tweens.add({ targets: container, scaleX: 1.05, scaleY: 1.05, duration: 90, ease: 'Power2' });
            });

            container.on('pointerout', () => {
                if (isPressed) return;
                drawBody(colors.fill, colors.border);
                lbl.setColor(colors.textColor);
                scene.tweens.add({ targets: container, scaleX: 1, scaleY: 1, duration: 90, ease: 'Power2' });
            });

            container.on('pointerdown', () => {
                isPressed = true;
                bodyGfx.y = shadowOffset; shineGfx.y = shadowOffset; lbl.y = shadowOffset;
                drawBody(colors.hoverFill, colors.hoverBorder);
            });

            container.on('pointerup', () => {
                bodyGfx.y = 0; shineGfx.y = 0; lbl.y = 0;
                isPressed = false;
                drawBody(colors.fill, colors.border);
                lbl.setColor(colors.textColor);
                scene.cameras.main.fadeOut(300, 15, 23, 42);
                scene.cameras.main.once('camerafadeoutcomplete', onTap);
            });

            // ---- Shimmer sweep disabled for performance ----
            /*
            const shimW = bw * 0.18;
            const slant = bh * 0.65;

            const btnMask = scene.make.graphics({ add: false });
            btnMask.fillStyle(0xffffff);
            btnMask.fillRoundedRect(cx - halfW, cy - halfH, bw, bh, radius);

            const btnShimGfx = scene.add.graphics();
            btnShimGfx.setMask(btnMask.createGeometryMask());

            const wsp = { v: cx - halfW - shimW - slant * 2 };
            scene.tweens.add({
                targets: wsp,
                v: cx + halfW + shimW,
                duration: 650,
                ease: 'Quad.easeInOut',
                delay: Math.random() * 700 + 1400,
                repeat: -1,
                repeatDelay: 2600,
                onUpdate: () => {
                    btnShimGfx.clear();
                    btnShimGfx.fillStyle(0xffffff, 0.28);
                    btnShimGfx.fillPoints([
                        { x: wsp.v, y: cy - halfH },
                        { x: wsp.v + shimW, y: cy - halfH },
                        { x: wsp.v + shimW + slant * 2, y: cy + halfH },
                        { x: wsp.v + slant * 2, y: cy + halfH },
                    ], true);
                },
                onRepeat: () => btnShimGfx.clear()
            });
            */

            return container;
        }

        // =====================================================
        //  HELPER — Shimmer pada PNG icon (Disabled for performance)
        // =====================================================
        function addIconShimmer(scene, img, delay) {
            return; // Immediately return to prevent rendering overhead
        }

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

            const dialog = scene.add.container(W / 2, H / 2);

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

            const dialog = scene.add.container(W / 2, H / 2);

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

                const btnTopupTxt = scene.add.text(0, 0, 'SHOP', {
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
                    scene.cameras.main.fadeOut(300, 15, 23, 42);
                    scene.cameras.main.once('camerafadeoutcomplete', () => {
                        window.location.href = '/shop';
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

                // Abaikan outline hitam/gelap
                if (r < 40 && g < 40 && b < 40) continue;

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
        //  SERVER SIDE SAVE HELPER
        // =====================================================
        function saveCustomizationsToServer(scene) {
            const data = {
                customColors: scene.customColors,
                corak_data_url: localStorage.getItem('corak_data_url'),
                lambai_data_url: localStorage.getItem('lambai_data_url'),
                boat_unlocked: scene.boatUnlocked ? true : false,
                lambai_unlocked: scene.lambaiUnlocked ? true : false,
                coins: parseInt(localStorage.getItem('coins') || '100000')
            };

            fetch('/tukang-jaluar/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(data)
            })
                .then(res => res.json())
                .then(data => {
                    console.log('Customization saved to server:', data);
                })
                .catch(err => console.error('Error saving customization:', err));
        }

        // =====================================================
        //  CUSTOMISATION SCENE
        // =====================================================
        class CustomizeScene extends Phaser.Scene {
            constructor() { super({ key: 'CustomizeScene' }); }

            preload() {
                this.load.image('bgmenu', '/game_pacu/assets/image/bg/bgmenu.jpg');
                this.load.image('tukang', '/game_pacu/assets/image/ui/animasitukang.png');
                this.load.image('bubblechat', '/game_pacu/assets/image/ui/bubblechat.png');
                this.load.image('back', '/game_pacu/assets/image/ui/back.png');
                this.load.image('koin', '/game_pacu/assets/image/ui/koin.png');
                this.load.image('btn_kiri', '/game_pacu/assets/image/ui/btn_kiri.png');
                this.load.image('btn_kanan', '/game_pacu/assets/image/ui/btn_kanan.png');

                // Load gambar perahu (boat)
                this.load.image('jalur_boat', '/game_pacu/assets/image/jalur/jalur.png');

                // Load frame-frame gambar orang mendayung (rowing animation frames)
                this.load.image('char1', '/game_pacu/assets/image/char/1.png');
                this.load.image('char2', '/game_pacu/assets/image/char/2.png');
                this.load.image('char3', '/game_pacu/assets/image/char/3.png');
                this.load.image('char4', '/game_pacu/assets/image/char/4.png');
                this.load.image('char5', '/game_pacu/assets/image/char/5.png');
            }

            applyRecolor() {
                // Hentikan animasi sementara sebelum memodifikasi tekstur
                if (this.rowerSprites) {
                    this.rowerSprites.forEach(rower => {
                        rower.stop();
                    });
                }

                // Jalankan pewarnaan ulang frame 1-5
                for (let f = 1; f <= 5; f++) {
                    const sourceKey = `char${f}`;
                    const destKey = `recolored_char${f}`;

                    const canvas = recolorCharacterImage(this, sourceKey, this.customColors);

                    if (this.textures.exists(destKey)) {
                        this.textures.remove(destKey);
                    }
                    this.textures.addCanvas(destKey, canvas);
                }

                // Buat ulang animasi dengan tekstur baru
                if (this.anims.exists('rowing_anim')) {
                    this.anims.remove('rowing_anim');
                }
                this.anims.create({
                    key: 'rowing_anim',
                    frames: [
                        { key: 'recolored_char1' },
                        { key: 'recolored_char2' },
                        { key: 'recolored_char3' },
                        { key: 'recolored_char4' },
                        { key: 'recolored_char5' }
                    ],
                    frameRate: 8,
                    repeat: -1
                });

                // Mainkan kembali animasi pada semua sprite pendayung
                if (this.rowerSprites) {
                    this.rowerSprites.forEach(rower => {
                        rower.play('rowing_anim');
                    });
                }

                // Aplikasikan warna perahu menggunakan Tint
                if (this.boatImg) {
                    const boatColorInt = Phaser.Display.Color.HexStringToColor(this.customColors.boat).color;
                    this.boatImg.setTint(boatColorInt);
                }

                // Aplikasikan warna cipratan air ke semua emitter
                if (this.waterEmitters && this.waterEmitters.length > 0) {
                    const splashColorInt = Phaser.Display.Color.HexStringToColor(this.customColors.splash).color;
                    this.waterEmitters.forEach(emitter => {
                        emitter.setParticleTint(splashColorInt);
                    });
                }
            }

            // =====================================================
            //  METHOD: Terapkan Corak ke Jalur
            //  Strategi: canvas dibuat di RESOLUSI TAMPILAN (bukan resolusi sumber pixel)
            //  sehingga corak dirender tanpa bergantung pada Phaser scaling.
            //  Untuk pixel art, digunakan Nearest-Neighbor (smoothing disabled) agar tetap tajam.
            // =====================================================
            applyCorak(dataUrl) {
                const img = new Image();
                img.onload = () => {
                    // Ambil source image jalur dari Phaser texture cache
                    const boatSource = this.textures.get('jalur_boat').getSourceImage();

                    // =====================================================
                    //  ⚙️ PENGATURAN POSISI & TAMPILAN SPRITE DI ATAS JALUR
                    //  Sesuaikan nilai-nilai di bawah ini untuk posisi sprite di game
                    // =====================================================

                    // Skala tampilan — HARUS sama dengan BOAT_SCALE (2.3)
                    // Nilai ini digunakan untuk menghitung resolusi canvas agar
                    // corak dirender PERSIS di ukuran layar (tanpa scaling Phaser)
                    const CORAK_SCALE = 2.3;

                    // Offset X corak relatif terhadap pusat boatGroup
                    // Harus sama dengan BOAT_OFFSET_X (0) agar rata dengan perahu
                    const CORAK_OFFSET_X = 0;

                    // Offset Y corak relatif terhadap pusat boatGroup
                    // Harus sama dengan BOAT_OFFSET_Y (15) agar persis di atas perahu
                    const CORAK_OFFSET_Y = 15;

                    // Transparansi corak: 0.0 = tidak terlihat, 1.0 = opak penuh
                    const CORAK_ALPHA = 0.82;

                    // Blend mode corak di atas jalur:
                    // Phaser.BlendModes.NORMAL    → Corak menimpa jalur (default, paling aman)
                    // Phaser.BlendModes.MULTIPLY  → Corak menyatu dengan warna jalur (efek serat/tekstur)
                    const CORAK_BLEND = Phaser.BlendModes.MULTIPLY;

                    // =====================================================
                    //  ⚙️ PENGATURAN TAMPILAN GAMBAR UPLOAD (DI ATAS CANVAS PERAHU)
                    //  Gunakan variabel di bawah ini untuk menggeser/mengatur corak
                    // =====================================================

                    // Jaga aspect ratio: true = gambar tidak memanjang vertikal (misal untuk 128x40 agar tetap proporsional),
                    // false = regangkan gambar agar pas dengan tinggi perahu secara paksa.
                    const PRESERVE_ASPECT_RATIO = true;

                    // Skala gambar corak relatif: 1.0 = normal/proporsional.
                    const CUSTOM_CORAK_SCALE = 1.0;

                    // Geser gambar corak secara horizontal (dalam pixel koordinat layar).
                    // Nilai negatif = geser ke KIRI, positif = geser ke KANAN.
                    const CUSTOM_CORAK_OFFSET_X = 0;

                    // Geser gambar corak secara vertikal (dalam pixel koordinat layar).
                    // Nilai negatif = geser ke ATAS, positif = geser ke BAWAH.
                    const CUSTOM_CORAK_OFFSET_Y = 0;

                    // =====================================================

                    // -----------------------------------------------
                    //  LANGKAH 1: Buat canvas di RESOLUSI TAMPILAN
                    // -----------------------------------------------
                    const displayW = Math.round(boatSource.width * CORAK_SCALE);
                    const displayH = Math.round(boatSource.height * CORAK_SCALE);

                    const maskCanvas = document.createElement('canvas');
                    maskCanvas.width = displayW;
                    maskCanvas.height = displayH;
                    const ctx = maskCanvas.getContext('2d');

                    // -----------------------------------------------
                    //  LANGKAH 2: Hitung ukuran & posisi gambar agar tidak terdistorsi
                    // -----------------------------------------------
                    let drawW, drawH;
                    if (PRESERVE_ASPECT_RATIO) {
                        // Jaga proporsi asli gambar
                        const scaleFactor = (displayW / img.width) * CUSTOM_CORAK_SCALE;
                        drawW = displayW * CUSTOM_CORAK_SCALE;
                        drawH = Math.round(img.height * scaleFactor);
                    } else {
                        // Regangkan gambar untuk memenuhi canvas perahu
                        drawW = displayW * CUSTOM_CORAK_SCALE;
                        drawH = displayH * CUSTOM_CORAK_SCALE;
                    }

                    // Posisikan di tengah secara default, lalu tambahkan offset manual
                    const drawX = Math.round((displayW - drawW) / 2) + CUSTOM_CORAK_OFFSET_X;
                    const drawY = Math.round((displayH - drawH) / 2) + CUSTOM_CORAK_OFFSET_Y;

                    // -----------------------------------------------
                    //  LANGKAH 3: Gambar corak ke canvas
                    //  Jika gambar berukuran kecil (<= 256px), anggap sebagai pixel art.
                    //  Gunakan Nearest-Neighbor (smoothing disabled) agar tetap tajam
                    //  dan tidak kabur/gajels.
                    // -----------------------------------------------
                    const isPixelArt = (img.width <= 256 && img.height <= 256);
                    ctx.imageSmoothingEnabled = !isPixelArt;
                    if (ctx.imageSmoothingEnabled) {
                        ctx.imageSmoothingQuality = 'high';
                    }
                    ctx.drawImage(img, drawX, drawY, drawW, drawH);

                    // -----------------------------------------------
                    //  LANGKAH 4: Hapus background putih dari corak
                    // -----------------------------------------------
                    const imageData = ctx.getImageData(0, 0, displayW, displayH);
                    const data = imageData.data;

                    // ⚙️ THRESHOLD HAPUS BACKGROUND PUTIH (0–255)
                    // Naikkan jika warna corak ikut terhapus (maksimal ~250)
                    // Turunkan jika sisa putih masih terlihat di tepi corak
                    const WHITE_THRESHOLD = 240;

                    for (let i = 0; i < data.length; i += 4) {
                        const r = data[i], g = data[i + 1], b = data[i + 2];
                        if (r > WHITE_THRESHOLD && g > WHITE_THRESHOLD && b > WHITE_THRESHOLD) {
                            // Fade halus: semakin putih piksel → semakin transparan
                            const brightness = Math.min(r, g, b);
                            const fade = (brightness - WHITE_THRESHOLD) / (255 - WHITE_THRESHOLD);
                            data[i + 3] = Math.round(255 * (1 - fade));
                        }
                    }
                    ctx.putImageData(imageData, 0, 0);

                    // -----------------------------------------------
                    //  LANGKAH 5: Potong corak ke bentuk jalur
                    //  Teknik canvas 2D "destination-in"
                    // -----------------------------------------------
                    ctx.imageSmoothingEnabled = false;
                    ctx.globalCompositeOperation = 'destination-in';
                    ctx.drawImage(boatSource, 0, 0, displayW, displayH);
                    ctx.globalCompositeOperation = 'source-over'; // Reset ke mode normal
                    ctx.imageSmoothingEnabled = true;

                    // -----------------------------------------------
                    //  LANGKAH 6: Daftarkan tekstur ke Phaser
                    // -----------------------------------------------
                    if (this.textures.exists('corak_texture')) {
                        this.textures.remove('corak_texture');
                    }
                    this.textures.addCanvas('corak_texture', maskCanvas);

                    // Hancurkan sprite corak lama jika ada
                    if (this.corakSprite) {
                        this.corakSprite.destroy();
                        this.corakSprite = null;
                    }

                    // -----------------------------------------------
                    //  LANGKAH 7: Buat sprite dan masukkan ke boatGroup
                    // -----------------------------------------------
                    this.corakSprite = this.make.image({
                        x: CORAK_OFFSET_X,
                        y: CORAK_OFFSET_Y,
                        key: 'corak_texture',
                        add: false  // Jangan tambah ke scene root — kita kelola manual
                    });
                    this.corakSprite.setScale(1.0);
                    this.corakSprite.setAlpha(CORAK_ALPHA);
                    this.corakSprite.setBlendMode(CORAK_BLEND);

                    // Masukkan corak ke boatGroup agar ikut animasi bobbing perahu
                    this.boatGroup.add(this.corakSprite);

                    // Pindahkan corak tepat SETELAH boatImg dalam urutan render
                    if (this.boatImg && this.boatGroup.list.includes(this.boatImg)) {
                        const boatIdx = this.boatGroup.getIndex(this.boatImg);
                        this.boatGroup.moveTo(this.corakSprite, boatIdx + 1);
                    }
                };
                img.src = dataUrl;
            }

            // =====================================================
            //  METHOD: Terapkan Hiasan Ekor (Lambai-Lambai)
            //  Merubah gambar biasa menjadi Pixel Art HD menggunakan canvas downsampling
            // =====================================================
            applyLambai(dataUrl) {
                const img = new Image();
                img.onload = () => {
                    // =====================================================
                    //  ⚙️ PENGATURAN POSISI & TAMPILAN LAMBAI-LAMBAI (EKOR JALUR)
                    //  Sesuaikan nilai-nilai di bawah ini untuk posisi ekor di game
                    // =====================================================
                    const LAMBAI_SCALE = 1.3;       // Ukuran tampilan ekor/lambai-lambai
                    const LAMBAI_OFFSET_X = 125;    // Geser posisi horizontal ke ekor perahu (kanan)
                    const LAMBAI_OFFSET_Y = -18;    // Geser posisi vertikal di atas ekor perahu
                    const LAMBAI_ROTATION_LIMIT = 0; // Sudut maksimum ayunan lambai-lambai
                    // =====================================================

                    // Ubah gambar biasa menjadi pixel art HD dengan canvas downsampling
                    const targetSize = 48; // Resolusi piksel art HD
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

                    // Menonaktifkan image smoothing saat menggambar hasil perkecilan (downsampling)
                    // untuk memberikan tepi piksel yang kasar dan tajam khas retro pixel art.
                    ctx.imageSmoothingEnabled = false;
                    ctx.drawImage(img, 0, 0, w, h);

                    // Hapus background putih dari lambai-lambai
                    const imageData = ctx.getImageData(0, 0, w, h);
                    const data = imageData.data;
                    const WHITE_THRESHOLD = 240;
                    for (let i = 0; i < data.length; i += 4) {
                        const r = data[i], g = data[i + 1], b = data[i + 2];
                        if (r > WHITE_THRESHOLD && g > WHITE_THRESHOLD && b > WHITE_THRESHOLD) {
                            // Fade halus: semakin putih piksel → semakin transparan
                            const brightness = Math.min(r, g, b);
                            const fade = (brightness - WHITE_THRESHOLD) / (255 - WHITE_THRESHOLD);
                            data[i + 3] = Math.round(255 * (1 - fade));
                        }
                    }
                    ctx.putImageData(imageData, 0, 0);

                    // Daftarkan tekstur ke Phaser
                    if (this.textures.exists('lambai_texture')) {
                        this.textures.remove('lambai_texture');
                    }
                    this.textures.addCanvas('lambai_texture', canvas);

                    // Hancurkan sprite lama jika ada
                    if (this.lambaiSprite) {
                        if (this.lambaiTween) this.lambaiTween.stop();
                        this.lambaiSprite.destroy();
                        this.lambaiSprite = null;
                    }

                    // Buat sprite lambai-lambai baru
                    this.lambaiSprite = this.make.image({
                        x: LAMBAI_OFFSET_X,
                        y: LAMBAI_OFFSET_Y,
                        key: 'lambai_texture',
                        add: false
                    });
                    this.lambaiSprite.setScale(LAMBAI_SCALE);
                    this.lambaiSprite.texture.setFilter(Phaser.Textures.FilterMode.NEAREST);

                    // Masukkan ke boatGroup agar ikut bergoyang naik-turun bersama perahu
                    this.boatGroup.add(this.lambaiSprite);

                    // Posisikan lambai-lambai di bawah perahu dalam rendering list
                    if (this.boatImg && this.boatGroup.list.includes(this.boatImg)) {
                        const boatIdx = this.boatGroup.getIndex(this.boatImg);
                        this.boatGroup.moveTo(this.lambaiSprite, boatIdx);
                    }

                    // Tambahkan animasi melambai (angle rotation loop)
                    this.lambaiTween = this.tweens.add({
                        targets: this.lambaiSprite,
                        angle: { from: -LAMBAI_ROTATION_LIMIT, to: LAMBAI_ROTATION_LIMIT },
                        duration: 850,
                        yoyo: true,
                        repeat: -1,
                        ease: 'Sine.easeInOut'
                    });
                };
                img.src = dataUrl;
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

                this.coinCount = <?= auth()->user()->kuansing_poin ?>;

                this.cameras.main.fadeIn(500, 15, 23, 42);

                // Load custom colors dari localStorage atau gunakan default
                this.customColors = {
                    boat: localStorage.getItem('custom_boat') || '#8b4513',
                    hair: localStorage.getItem('custom_hair') || '#e53e3e',
                    shirt: localStorage.getItem('custom_shirt') || '#a0aec0',
                    pants: localStorage.getItem('custom_pants') || '#38a169',
                    paddle: localStorage.getItem('custom_paddle') || '#3182ce',
                    splash: localStorage.getItem('custom_splash') || '#a5f3fc'
                };

                this.boatUnlocked = window.serverCustomizations ? !!window.serverCustomizations.boat_unlocked : false;
                this.lambaiUnlocked = window.serverCustomizations ? !!window.serverCustomizations.lambai_unlocked : false;

                // ---- Background Image ----
                const bg = this.add.image(cx, H / 2, 'bgmenu');
                const scaleX_bg = W / bg.width;
                const scaleY_bg = H / bg.height;
                bg.setScale(Math.max(scaleX_bg, scaleY_bg));

                // Radial background glow disabled for performance
                /*
                const glowCanvas = document.createElement('canvas');
                glowCanvas.width = 360;
                glowCanvas.height = 760;
                const glowCtx = glowCanvas.getContext('2d');
                const gradient = glowCtx.createRadialGradient(180, 228, 0, 180, 228, 360);
                gradient.addColorStop(0, 'rgba(59, 130, 246, 0.35)');   // Vibrant blue center glow
                gradient.addColorStop(0.5, 'rgba(30, 41, 59, 0.25)');  // Soft slate-blue transition
                gradient.addColorStop(1, 'rgba(15, 23, 42, 0.45)');    // Gentle slate-blue vignette (much brighter)
                glowCtx.fillStyle = gradient;
                glowCtx.fillRect(0, 0, 360, 760);
                
                if (this.textures.exists('bg_glow')) {
                    this.textures.remove('bg_glow');
                }
                this.textures.addCanvas('bg_glow', glowCanvas);
                const bgGlow = this.add.image(cx, H / 2, 'bg_glow');
                bgGlow.setDisplaySize(W, H);
                */

                // Background floating particles disabled for performance
                /*
                if (!this.textures.exists('bg_particle')) {
                    const pGfxBg = this.make.graphics({ add: false });
                    pGfxBg.fillStyle(0xffffff, 1);
                    pGfxBg.fillRect(0, 0, 4, 4);
                    pGfxBg.generateTexture('bg_particle', 4, 4);
                    pGfxBg.destroy();
                }

                const bgParticles = this.add.particles(0, 0, 'bg_particle', {
                    x: { min: 0, max: W },
                    y: { min: 0, max: H },
                    lifespan: { min: 6000, max: 10000 },
                    speedY: { min: -20, max: -8 },
                    speedX: { min: -4, max: 4 },
                    scale: { min: 0.5, max: 1.1 },
                    alpha: { min: 0.15, max: 0.5 },
                    frequency: 300,
                    maxParticles: 25
                });
                */

                // =====================================================
                //  KOTAK KUSTOMISASI / PREVIEW JALUAR (ATAS HALAMAN)
                // =====================================================

                // -----------------------------------------------------
                // PENGATURAN POSISI DAN UKURAN (Silakan sesuaikan di sini)
                // -----------------------------------------------------
                // X & Y posisi kotak kustomisasi di layar
                const boxX = cx;  // Tengah-tengah layar secara horizontal
                const boxY = 175; // Bagian atas layar (di bawah tombol kembali dan koin)

                // Dimensi Kotak Kustomisasi
                const boxWidth = W - 44; // Lebar kotak menyesuaikan frame (lebar default 360, jadi ~316px)
                const boxHeight = 150;   // Tinggi kotak

                // Pengaturan skala (scale) gambar pixel art agar terlihat proporsional
                const BOAT_SCALE = 2.3;   // Mengatur ukuran perahu (1.6x lebih besar agar jelas)
                const ROWER_SCALE = 0.18; // Mengatur ukuran orang mendayung (dikecilkan agar pas dengan perahu)

                // Posisi perahu & pendayung relatif terhadap titik tengah kotak kustomisasi
                // Ubah nilai ini jika ingin menggeser posisi perahu & karakter secara manual
                const BOAT_OFFSET_X = 0;   // Geser perahu ke kiri (-) atau kanan (+)
                const BOAT_OFFSET_Y = 15;  // Geser perahu ke atas (-) atau bawah (+)

                // Posisi orang mendayung relatif terhadap perahu
                const ROWER_OFFSET_X = -25; // Geser orang mendayung ke kiri (-) atau kanan (+) di atas perahu (diubah dari 8 ke -15 agar geser ke kiri)
                const ROWER_OFFSET_Y = -25; // Geser orang mendayung ke atas (-) atau bawah (+) di atas perahu
                const ROWER_SPACING = 35;   // Jarak antar pendayung (berjajar horizontal)

                // Pengaturan Animasi
                const ROWING_SPEED = 8;     // Kecepatan animasi mendayung (frame per second)
                const BOBBING_HEIGHT = 4;   // Jarak perahu mengapung naik-turun (pixel)
                const BOBBING_SPEED = 1200;  // Durasi satu siklus mengapung (milidetik)
                // -----------------------------------------------------

                // Container utama untuk Kotak Kustomisasi
                const boxContainer = this.add.container(boxX, boxY);

                // Grafik Background Kotak Kustomisasi (PS5 Glass style)
                const boxBg = this.add.graphics();

                // Dark Shadow
                boxBg.fillStyle(0x000000, 0.45);
                boxBg.fillRoundedRect(-boxWidth / 2 + 6, -boxHeight / 2 + 6, boxWidth, boxHeight, 16);

                // Card Body (Translucent Dark Slate)
                boxBg.fillStyle(0x0f172a, 0.75);
                boxBg.lineStyle(2.5, 0x3b82f6, 0.5); // Soft blue border
                boxBg.fillRoundedRect(-boxWidth / 2, -boxHeight / 2, boxWidth, boxHeight, 16);
                boxBg.strokeRoundedRect(-boxWidth / 2, -boxHeight / 2, boxWidth, boxHeight, 16);
                boxContainer.add(boxBg);

                // Label Judul di dalam Kotak Kustomisasi
                const boxTitle = this.add.text(0, -boxHeight / 2 + 20, '✦ JALUAR PREVIEW ✦', {
                    fontFamily: '"Press Start 2P", monospace',
                    fontSize: '10px',
                    color: '#38bdf8',
                    fontStyle: 'bold',
                    stroke: '#0f172a',
                    strokeThickness: 3
                }).setOrigin(0.5);
                boxContainer.add(boxTitle);

                // Sub-container untuk perahu + pendayung agar efek mengapung (bobbing) berjalan bersamaan
                const boatGroup = this.add.container(0, 0);
                boxContainer.add(boatGroup);
                // Simpan referensi boatGroup agar bisa diakses dari method applyCorak()
                this.boatGroup = boatGroup;

                this.rowerSprites = [];
                this.waterEmitters = [];

                // Lakukan pewarnaan ulang pertama kali
                this.applyRecolor();

                // Tampilkan Perahu
                this.boatImg = this.add.image(BOAT_OFFSET_X, BOAT_OFFSET_Y, 'jalur_boat');
                this.boatImg.setScale(BOAT_SCALE);
                this.boatImg.texture.setFilter(Phaser.Textures.FilterMode.NEAREST);
                // Aplikasikan warna perahu awal
                const boatColorInt = Phaser.Display.Color.HexStringToColor(this.customColors.boat).color;
                this.boatImg.setTint(boatColorInt);
                boatGroup.add(this.boatImg);

                // Buat tekstur partikel air sederhana programmatically
                // PENTING: warna dasar HARUS putih (0xffffff) agar setParticleTint() bekerja akurat.
                // Phaser menerapkan tint secara multiplicatif: warna_akhir = warna_tekstur × warna_tint.
                // Jika tekstur berwarna kuning/emas, hasil tint akan meleset (kuning × biru = hijau, bukan biru).
                // Dengan tekstur putih: putih × warna_apapun = warna itu sendiri (hasil tepat).
                if (this.textures.exists('water_particle')) {
                    this.textures.remove('water_particle'); // Hapus cache lama jika ada
                }
                const pGfx = this.make.graphics({ add: false });
                pGfx.fillStyle(0xffffff, 1); // Putih solid — tint akan menghasilkan warna yang dipilih persis
                pGfx.fillRect(0, 0, 8, 8);
                pGfx.generateTexture('water_particle', 8, 8);
                pGfx.destroy();

                // Posisi cipratan air relatif terhadap pendayung (disesuaikan: digeser ke bawah dan ke kanan)
                const SPLASH_OFFSET_X = -1;   // Geser cipratan air ke kanan (diubah dari -10 ke 2)
                const SPLASH_OFFSET_Y = 32;  // Geser cipratan air ke bawah (diubah dari 22 ke 32 dekat garis air perahu)

                // Tampilkan 5 Orang Mendayung Berjajar di atas perahu beserta partikel cipratan air
                const offsetsX = [-ROWER_SPACING * 2, -ROWER_SPACING, 0, ROWER_SPACING, ROWER_SPACING * 2];

                // --- Loop 1: Tambahkan semua karakter (rower) ke boatGroup terlebih dahulu ---
                // Ini memastikan karakter dirender sebelum partikel dalam urutan container
                const emitterList = []; // Simpan emitter dan rower untuk dihubungkan setelah loop
                offsetsX.forEach((offsetX) => {
                    const rowerX = BOAT_OFFSET_X + ROWER_OFFSET_X + offsetX;
                    const rowerY = BOAT_OFFSET_Y + ROWER_OFFSET_Y;

                    // Buat Sprite Rower
                    const rowerSprite = this.add.sprite(rowerX, rowerY, 'recolored_char1');
                    rowerSprite.setScale(ROWER_SCALE);
                    rowerSprite.play('rowing_anim');
                    rowerSprite.texture.setFilter(Phaser.Textures.FilterMode.NEAREST);

                    boatGroup.add(rowerSprite);
                    this.rowerSprites.push(rowerSprite);

                    // Simpan data posisi emitter untuk dibuat di loop berikutnya
                    emitterList.push({ rowerX, rowerY, rowerSprite });
                });

                // --- Loop 2: Tambahkan semua emitter partikel SETELAH semua karakter ---
                // Karena container Phaser merender objek sesuai urutan add(), emitter yang
                // ditambahkan belakangan akan selalu tampil di atas karakter.
                // Disabled water particles for mobile/low-end performance optimization
                /*
                emitterList.forEach(({ rowerX, rowerY, rowerSprite }) => {
                    // Buat Particle Emitter di atas setiap karakter
                    const emitter = this.add.particles(rowerX + SPLASH_OFFSET_X, rowerY + SPLASH_OFFSET_Y, 'water_particle', {
                        speed: { min: 40, max: 110 },
                        angle: { min: 280, max: 340 },
                        scale: { start: 2.2, end: 0 },
                        lifespan: { min: 300, max: 550 },
                        gravityY: 350,
                        quantity: 2,
                        frequency: -1 // Jangan emit otomatis, kita trigger secara manual
                    });

                    // Set rendering mode ke NEAREST agar tetap pixelated/crisp
                    emitter.texture.setFilter(Phaser.Textures.FilterMode.NEAREST);

                    // Terapkan warna cipratan awal dari customColors
                    const initSplashColor = Phaser.Display.Color.HexStringToColor(this.customColors.splash).color;
                    emitter.setParticleTint(initSplashColor);

                    // Simpan referensi emitter agar bisa diupdate warnanya lewat applyRecolor()
                    this.waterEmitters.push(emitter);

                    // Tambahkan emitter ke boatGroup SETELAH semua rower — ini yang membuatnya tampil di atas karakter
                    boatGroup.add(emitter);

                    // Dengarkan update animasi untuk memicu cipratan air pada frame 3 dan 4
                    rowerSprite.on('animationupdate', (anim, frame) => {
                        // frame.index adalah 1-based (1, 2, 3, 4, 5)
                        if (frame.index === 3 || frame.index === 4) {
                            emitter.explode(18);
                        }
                    });
                });
                */

                // Animasi Mengapung (Bobbing) secara halus pada sumbu Y
                this.tweens.add({
                    targets: boatGroup,
                    y: BOBBING_HEIGHT,
                    duration: BOBBING_SPEED,
                    yoyo: true,
                    repeat: -1,
                    ease: 'Sine.easeInOut'
                });

                // =====================================================
                //  PANEL KUSTOMISASI WARNA — FORMAT SLIDE
                // =====================================================
                const panelY = 400;
                const panelContainer = this.add.container(cx, panelY);

                const panelWidth = W - 44;
                const panelHeight = 185;
                const panelBg = this.add.graphics();

                // Dark Shadow
                panelBg.fillStyle(0x000000, 0.45);
                panelBg.fillRoundedRect(-panelWidth / 2 + 6, -panelHeight / 2 + 6, panelWidth, panelHeight, 16);

                // Main Panel (Translucent Dark Slate)
                panelBg.fillStyle(0x0f172a, 0.85);
                panelBg.lineStyle(2.5, 0x3b82f6, 0.5); // Soft blue border
                panelBg.fillRoundedRect(-panelWidth / 2, -panelHeight / 2, panelWidth, panelHeight, 16);
                panelBg.strokeRoundedRect(-panelWidth / 2, -panelHeight / 2, panelWidth, panelHeight, 16);
                panelContainer.add(panelBg);

                // --- Data Kategori ---
                const categories = [
                    {
                        key: 'boat',
                        label: 'JALUR',
                        icon: '🚣',
                        colors: [
                            '#8D6E63', // Violet
                            '#6D4C41', // Pink
                            '#F59E0B', // Amber
                            '#06B6D4', // Cyan
                            '#10B981', // Emerald
                            '#FBBF24'  // Red
                        ]
                    },
                    {
                        key: 'lambai',
                        label: 'LAMBAI LAMBAI',
                        icon: '🚩',
                        colors: [] // Tidak butuh pilihan warna, hanya upload, template, dan bersihkan
                    },
                    {
                        key: 'hair',
                        label: 'RAMBUT',
                        icon: '💇',
                        colors: [
                            '#111827', // Hitam
                            '#F59E0B', // Pirang
                            '#DC2626', // Merah
                            '#7C3AED', // Ungu
                            '#2563EB', // Biru
                            '#EC4899'  // Pink
                        ]
                    },
                    {
                        key: 'shirt',
                        label: 'BAJU',
                        icon: '👕',
                        colors: [
                            '#2563EB', // Royal Blue
                            '#EF4444', // Merah
                            '#10B981', // Hijau
                            '#F59E0B', // Kuning
                            '#8B5CF6', // Ungu
                            '#EC4899'  // Pink
                        ]
                    },
                    {
                        key: 'pants',
                        label: 'CELANA',
                        icon: '👖',
                        colors: [
                            '#1F2937', // Dark Gray
                            '#2563EB', // Biru
                            '#059669', // Hijau Tua
                            '#7C3AED', // Ungu
                            '#DC2626', // Merah Tua
                            '#F59E0B'  // Amber
                        ]
                    },
                    {
                        key: 'paddle',
                        label: 'DAYUNG',
                        icon: '🏏',
                        colors: [
                            '#8D6E63', // Orange
                            '#EF4444', // Merah
                            '#8B5CF6', // Ungu
                            '#06B6D4', // Cyan
                            '#10B981', // Emerald
                            '#FBBF24'  // Gold
                        ]
                    },
                    {
                        key: 'splash',
                        label: 'BAKABUIK',
                        icon: '💧',
                        colors: [
                            '#38BDF8', // Sky Blue
                            '#22D3EE', // Aqua
                            '#A78BFA', // Lavender
                            '#34D399', // Mint
                            '#F9A8D4', // Soft Pink
                            '#FBBF24'  // Gold
                        ]
                    }
                ];

                let currentSlide = 0;
                let isSliding = false;
                this.selectionIndicators = {};
                categories.forEach(cat => { this.selectionIndicators[cat.key] = []; });

                // --- Konstanta tampilan swatch ---
                const CC_RADIUS = 14;
                const CC_GAP = 34;

                // --- Fungsi untuk membangun konten satu slide ---
                const buildSlideContent = (catIndex, targetContainer) => {
                    const cat = categories[catIndex];

                    // Judul kategori (tengah atas)
                    const labelTxt = this.add.text(0, -panelHeight / 2 + 22, cat.label, {
                        fontFamily: '"Press Start 2P", monospace',
                        fontSize: '10px',
                        color: '#38bdf8',
                        fontStyle: 'bold',
                        stroke: '#0f172a',
                        strokeThickness: 3
                    }).setOrigin(0.5);
                    targetContainer.add(labelTxt);

                    // Baris pilihan warna (rata tengah)
                    const numColors = cat.colors.length;
                    const totalW = (numColors - 1) * CC_GAP;
                    const startX = -totalW / 2;

                    this.selectionIndicators[cat.key] = [];

                    // Posisikan pilihan warna sedikit ke bawah hanya untuk JALUR agar muat dengan tombol upload/bersihkan
                    const swatchY = (cat.key === 'boat') ? 10 : 12;

                    cat.colors.forEach((colorHex, cIdx) => {
                        const colorX = startX + cIdx * CC_GAP;
                        const btnCol = this.add.container(colorX, swatchY);
                        targetContainer.add(btnCol);

                        const colorInt = Phaser.Display.Color.HexStringToColor(colorHex).color;
                        const isSelected = this.customColors[cat.key] === colorHex;
                        const circleGfx = this.add.graphics();

                        const drawCircle = (selected) => {
                            circleGfx.clear();
                            if (selected) {
                                // Ring luar biru tebal untuk terpilih
                                circleGfx.lineStyle(3.5, 0x3b82f6, 1);
                                circleGfx.fillStyle(colorInt, 1);
                                circleGfx.fillCircle(0, 0, CC_RADIUS + 3);
                                circleGfx.strokeCircle(0, 0, CC_RADIUS + 3);
                            } else {
                                circleGfx.lineStyle(2, 0xffffff, 0.25);
                                circleGfx.fillStyle(colorInt, 1);
                                circleGfx.fillCircle(0, 0, CC_RADIUS);
                                circleGfx.strokeCircle(0, 0, CC_RADIUS);
                            }
                        };
                        drawCircle(isSelected);
                        btnCol.add(circleGfx);

                        // Titik putih indikator terpilih
                        const selDot = this.add.graphics();
                        selDot.fillStyle(0xffffff, 1);
                        selDot.fillCircle(0, 0, 4.5);
                        selDot.setVisible(isSelected);
                        btnCol.add(selDot);

                        this.selectionIndicators[cat.key].push({ color: colorHex, gfx: selDot, circleGfx, drawCircle });

                        const hitArea = new Phaser.Geom.Circle(0, 0, CC_RADIUS + 4);
                        btnCol.setInteractive(hitArea, Phaser.Geom.Circle.Contains, { useHandCursor: true });

                        btnCol.on('pointerover', () => {
                            this.tweens.add({ targets: btnCol, scaleX: 1.3, scaleY: 1.3, duration: 80 });
                        });
                        btnCol.on('pointerout', () => {
                            this.tweens.add({ targets: btnCol, scaleX: 1, scaleY: 1, duration: 80 });
                        });
                        btnCol.on('pointerdown', () => {
                            this.tweens.add({
                                targets: btnCol, scaleX: 0.85, scaleY: 0.85,
                                duration: 50, yoyo: true,
                                onComplete: () => {
                                    this.customColors[cat.key] = colorHex;
                                    localStorage.setItem(`custom_${cat.key}`, colorHex);
                                    // Update semua indikator pada slide ini
                                    this.selectionIndicators[cat.key].forEach(ind => {
                                        const sel = ind.color === colorHex;
                                        ind.gfx.setVisible(sel);
                                        ind.drawCircle(sel);
                                    });
                                    this.applyRecolor();
                                    saveCustomizationsToServer(this);
                                }
                            });
                        });
                    });

                    // Jika ini slide JALUR atau LAMBAI LAMBAI, tambahkan tombol UPLOAD, TEMPLATE, dan BERSIHKAN
                    if (cat.key === 'boat' || cat.key === 'lambai') {
                        const descText = (cat.key === 'boat')
                            ? 'Upload corak gambar\nuntuk menghias bodi perahu'
                            : 'Upload gambar ekor\nuntuk hiasan melambai perahu';

                        const descTxt = this.add.text(0, -22, descText, {
                            fontFamily: '"Pixelify Sans", monospace',
                            fontSize: '9.5px',
                            color: '#94a3b8',
                            fontStyle: 'bold',
                            align: 'center',
                            lineSpacing: 3
                        }).setOrigin(0.5);
                        targetContainer.add(descTxt);

                        const btnW = 82;
                        const btnH = 26;
                        const btnY = 52;

                        // 1. TOMBOL UPLOAD (Hijau, posisi X = -86)
                        const uploadBtn = this.add.container(-86, btnY);
                        targetContainer.add(uploadBtn);

                        const upBg = this.add.graphics();
                        const drawUpBg = (fillAlpha) => {
                            upBg.clear();
                            // Shadow
                            upBg.fillStyle(0x0f172a, 0.25);
                            upBg.fillRoundedRect(-btnW / 2 + 3, -btnH / 2 + 3, btnW, btnH, 6);
                            // Body
                            upBg.fillStyle(0x22c55e, fillAlpha);
                            upBg.lineStyle(2, 0x15803d, 1);
                            upBg.fillRoundedRect(-btnW / 2, -btnH / 2, btnW, btnH, 6);
                            upBg.strokeRoundedRect(-btnW / 2, -btnH / 2, btnW, btnH, 6);
                        };
                        drawUpBg(0.88);
                        uploadBtn.add(upBg);

                        const upTxt = this.add.text(0, 0, 'UPLOAD', {
                            fontFamily: '"Press Start 2P", monospace',
                            fontSize: '7px',
                            color: '#ffffff',
                            fontStyle: 'bold',
                            stroke: '#15803d',
                            strokeThickness: 2
                        }).setOrigin(0.5);
                        uploadBtn.add(upTxt);

                        uploadBtn.setSize(btnW, btnH);
                        uploadBtn.setInteractive({ useHandCursor: true });

                        uploadBtn.on('pointerover', () => {
                            drawUpBg(1);
                            this.tweens.add({ targets: uploadBtn, scaleX: 1.05, scaleY: 1.05, duration: 80 });
                        });
                        uploadBtn.on('pointerout', () => {
                            drawUpBg(0.88);
                            this.tweens.add({ targets: uploadBtn, scaleX: 1, scaleY: 1, duration: 80 });
                        });
                        uploadBtn.on('pointerdown', () => {
                            this.tweens.add({ targets: uploadBtn, scaleX: 0.92, scaleY: 0.92, duration: 60, yoyo: true });
                            // Pemicu dialog file browser dipindahkan ke native canvas listener agar tidak diblokir browser iOS/Safari
                        });

                        // 2. TOMBOL TEMPLATE (Biru, posisi X = 0)
                        const templateBtn = this.add.container(0, btnY);
                        targetContainer.add(templateBtn);

                        const tempBg = this.add.graphics();
                        const drawTempBg = (fillAlpha) => {
                            tempBg.clear();
                            // Shadow
                            tempBg.fillStyle(0x0f172a, 0.25);
                            tempBg.fillRoundedRect(-btnW / 2 + 3, -btnH / 2 + 3, btnW, btnH, 6);
                            // Body
                            tempBg.fillStyle(0x3b82f6, fillAlpha);
                            tempBg.lineStyle(2, 0x1d4ed8, 1);
                            tempBg.fillRoundedRect(-btnW / 2, -btnH / 2, btnW, btnH, 6);
                            tempBg.strokeRoundedRect(-btnW / 2, -btnH / 2, btnW, btnH, 6);
                        };
                        drawTempBg(0.88);
                        templateBtn.add(tempBg);

                        const tempTxt = this.add.text(0, 0, 'TEMPLATE', {
                            fontFamily: '"Press Start 2P", monospace',
                            fontSize: '6.5px',
                            color: '#ffffff',
                            fontStyle: 'bold',
                            stroke: '#1d4ed8',
                            strokeThickness: 2
                        }).setOrigin(0.5);
                        templateBtn.add(tempTxt);

                        templateBtn.setSize(btnW, btnH);
                        templateBtn.setInteractive({ useHandCursor: true });

                        templateBtn.on('pointerover', () => {
                            drawTempBg(1);
                            this.tweens.add({ targets: templateBtn, scaleX: 1.05, scaleY: 1.05, duration: 80 });
                        });
                        templateBtn.on('pointerout', () => {
                            drawTempBg(0.88);
                            this.tweens.add({ targets: templateBtn, scaleX: 1, scaleY: 1, duration: 80 });
                        });
                        templateBtn.on('pointerdown', () => {
                            this.tweens.add({ targets: templateBtn, scaleX: 0.92, scaleY: 0.92, duration: 60, yoyo: true });
                            // Download file template dipindahkan ke native canvas listener agar tidak diblokir browser iOS/Safari
                        });

                        // 3. TOMBOL BERSIHKAN (Merah, posisi X = 86)
                        const clearBtn = this.add.container(86, btnY);
                        targetContainer.add(clearBtn);

                        const clBg = this.add.graphics();
                        const drawClBg = (fillAlpha) => {
                            clBg.clear();
                            // Shadow
                            clBg.fillStyle(0x0f172a, 0.25);
                            clBg.fillRoundedRect(-btnW / 2 + 3, -btnH / 2 + 3, btnW, btnH, 6);
                            // Body
                            clBg.fillStyle(0xef4444, fillAlpha);
                            clBg.lineStyle(2, 0x991b1b, 1);
                            clBg.fillRoundedRect(-btnW / 2, -btnH / 2, btnW, btnH, 6);
                            clBg.strokeRoundedRect(-btnW / 2, -btnH / 2, btnW, btnH, 6);
                        };
                        drawClBg(0.88);
                        clearBtn.add(clBg);

                        const clTxt = this.add.text(0, 0, 'BERSIHKAN', {
                            fontFamily: '"Press Start 2P", monospace',
                            fontSize: '6px',
                            color: '#ffffff',
                            fontStyle: 'bold',
                            stroke: '#991b1b',
                            strokeThickness: 2
                        }).setOrigin(0.5);
                        clearBtn.add(clTxt);

                        clearBtn.setSize(btnW, btnH);
                        clearBtn.setInteractive({ useHandCursor: true });

                        // Mengubah state visual tombol bersihkan daripada menghilangkannya
                        const updateClearBtnState = () => {
                            if (!clearBtn || !clearBtn.scene) return;
                            const hasItem = (cat.key === 'boat') ? !!this.corakSprite : !!this.lambaiSprite;
                            if (hasItem) {
                                clearBtn.setAlpha(1.0);
                                drawClBg(0.88);
                                clTxt.setAlpha(1.0);
                            } else {
                                // Redupkan dan beri indikasi tidak aktif
                                clearBtn.setAlpha(0.4);
                                drawClBg(0.4);
                                clTxt.setAlpha(0.6);
                            }
                        };
                        updateClearBtnState();

                        clearBtn.on('pointerover', () => {
                            const hasItem = (cat.key === 'boat') ? !!this.corakSprite : !!this.lambaiSprite;
                            if (!hasItem) return;
                            drawClBg(1);
                            this.tweens.add({ targets: clearBtn, scaleX: 1.05, scaleY: 1.05, duration: 80 });
                        });
                        clearBtn.on('pointerout', () => {
                            const hasItem = (cat.key === 'boat') ? !!this.corakSprite : !!this.lambaiSprite;
                            if (!hasItem) return;
                            drawClBg(0.88);
                            this.tweens.add({ targets: clearBtn, scaleX: 1, scaleY: 1, duration: 80 });
                        });
                        clearBtn.on('pointerdown', () => {
                            const hasItem = (cat.key === 'boat') ? !!this.corakSprite : !!this.lambaiSprite;
                            if (!hasItem) return;
                            this.tweens.add({ targets: clearBtn, scaleX: 0.92, scaleY: 0.92, duration: 60, yoyo: true });

                            if (cat.key === 'boat') {
                                if (this.corakSprite) {
                                    this.corakSprite.destroy();
                                    this.corakSprite = null;
                                }
                                localStorage.removeItem('corak_data_url');
                            } else if (cat.key === 'lambai') {
                                if (this.lambaiSprite) {
                                    if (this.lambaiTween) this.lambaiTween.stop();
                                    this.lambaiSprite.destroy();
                                    this.lambaiSprite = null;
                                }
                                localStorage.removeItem('lambai_data_url');
                            }
                            updateClearBtnState();
                            saveCustomizationsToServer(this);
                        });

                        this.clearBtn = clearBtn;
                        this.updateClearBtnState = updateClearBtnState;
                    }

                    // JALUR (boat) lock overlay
                    if (cat.key === 'boat' && !this.boatUnlocked) {
                        const lockOverlay = this.add.container(0, 52);
                        targetContainer.add(lockOverlay);

                        const lockBg = this.add.graphics();

                        // Shadow
                        lockBg.fillStyle(0x000000, 0.35);
                        lockBg.fillRoundedRect(-panelWidth / 2 + 13, -15, panelWidth - 26, 36, 10);

                        // Translucent slate panel background
                        lockBg.fillStyle(0x1e293b, 0.9);
                        lockBg.lineStyle(2.5, 0x3b82f6, 0.7); // blue-border
                        lockBg.fillRoundedRect(-panelWidth / 2 + 10, -18, panelWidth - 20, 36, 10);
                        lockBg.strokeRoundedRect(-panelWidth / 2 + 10, -18, panelWidth - 20, 36, 10);
                        lockOverlay.add(lockBg);

                        const lockTxt = this.add.text(0, 0, '🔒 BUKA UPLOAD CORAK (200 KP)', {
                            fontFamily: '"Press Start 2P", monospace',
                            fontSize: '7.5px',
                            color: '#38bdf8',
                            fontStyle: 'bold',
                            stroke: '#0f172a',
                            strokeThickness: 3
                        }).setOrigin(0.5);
                        lockOverlay.add(lockTxt);

                        lockOverlay.setSize(panelWidth - 20, 36);
                        lockOverlay.setInteractive({ useHandCursor: true });

                        lockOverlay.on('pointerover', () => {
                            this.tweens.add({ targets: lockOverlay, scaleX: 1.02, scaleY: 1.02, duration: 80 });
                        });
                        lockOverlay.on('pointerout', () => {
                            this.tweens.add({ targets: lockOverlay, scaleX: 1, scaleY: 1, duration: 80 });
                        });

                        lockOverlay.on('pointerdown', () => {
                            showCustomConfirmModal(this, "Buka Upload Corak seharga\n200 Kuansing Poin?", () => {
                                if (this.coinCount < 200) {
                                    showCustomAlertModal(this, "Poin Kuansing tidak cukup!\nSilakan lakukan top up.", true);
                                } else {
                                    this.coinCount -= 200;
                                    this.boatUnlocked = true;
                                    localStorage.setItem('coins', String(this.coinCount));
                                    this.coinText.setText(String(this.coinCount));
                                    saveCustomizationsToServer(this);
                                    lockOverlay.destroy();
                                    showCustomAlertModal(this, "Berhasil membuka\nUpload Corak!");
                                }
                            });
                        });
                    }

                    // LAMBAI-LAMBAI (lambai) lock overlay
                    if (cat.key === 'lambai' && !this.lambaiUnlocked) {
                        const lockOverlay = this.add.container(0, 52);
                        targetContainer.add(lockOverlay);

                        const lockBg = this.add.graphics();

                        // Shadow
                        lockBg.fillStyle(0x000000, 0.35);
                        lockBg.fillRoundedRect(-panelWidth / 2 + 13, -15, panelWidth - 26, 36, 10);

                        // Translucent slate panel background
                        lockBg.fillStyle(0x1e293b, 0.9);
                        lockBg.lineStyle(2.5, 0x3b82f6, 0.7); // blue-border
                        lockBg.fillRoundedRect(-panelWidth / 2 + 10, -18, panelWidth - 20, 36, 10);
                        lockBg.strokeRoundedRect(-panelWidth / 2 + 10, -18, panelWidth - 20, 36, 10);
                        lockOverlay.add(lockBg);

                        const lockTxt = this.add.text(0, 0, '🔒 BUKA LAMBAI-LAMBAI (500 KP)', {
                            fontFamily: '"Press Start 2P", monospace',
                            fontSize: '7.5px',
                            color: '#38bdf8',
                            fontStyle: 'bold',
                            stroke: '#0f172a',
                            strokeThickness: 3
                        }).setOrigin(0.5);
                        lockOverlay.add(lockTxt);

                        lockOverlay.setSize(panelWidth - 20, 36);
                        lockOverlay.setInteractive({ useHandCursor: true });

                        lockOverlay.on('pointerover', () => {
                            this.tweens.add({ targets: lockOverlay, scaleX: 1.02, scaleY: 1.02, duration: 80 });
                        });
                        lockOverlay.on('pointerout', () => {
                            this.tweens.add({ targets: lockOverlay, scaleX: 1, scaleY: 1, duration: 80 });
                        });

                        lockOverlay.on('pointerdown', () => {
                            showCustomConfirmModal(this, "Buka Lambai-lambai seharga\n500 Kuansing Poin?", () => {
                                if (this.coinCount < 500) {
                                    showCustomAlertModal(this, "Poin Kuansing tidak cukup!\nSilakan lakukan top up.", true);
                                } else {
                                    this.coinCount -= 500;
                                    this.lambaiUnlocked = true;
                                    localStorage.setItem('coins', String(this.coinCount));
                                    this.coinText.setText(String(this.coinCount));
                                    saveCustomizationsToServer(this);
                                    lockOverlay.destroy();
                                    showCustomAlertModal(this, "Berhasil membuka\nLambai-lambai!");
                                }
                            });
                        });
                    }
                };

                // --- Dot Indicator (bawah panel) ---
                const DOT_Y = panelHeight / 2 - 16;
                const DOT_GAP = 14;
                const dotStartX = -(categories.length - 1) * DOT_GAP / 2;
                const dotGfxList = [];
                for (let d = 0; d < categories.length; d++) {
                    const dg = this.add.graphics();
                    dg.x = dotStartX + d * DOT_GAP;
                    dg.y = DOT_Y;
                    panelContainer.add(dg);
                    dotGfxList.push(dg);
                }
                const updateDots = (activeIdx) => {
                    dotGfxList.forEach((gfx, i) => {
                        gfx.clear();
                        gfx.fillStyle(0x38bdf8, i === activeIdx ? 1 : 0.28);
                        gfx.fillCircle(0, 0, i === activeIdx ? 5 : 3.5);
                    });
                };

                // --- Mask panel agar konten slide tidak keluar panel ---
                // Mask dibuat di world-space menggunakan posisi panelContainer
                const panelMask = this.make.graphics({ add: false });
                panelMask.fillStyle(0xffffff);
                panelMask.fillRoundedRect(
                    cx - panelWidth / 2,
                    panelY - panelHeight / 2,
                    panelWidth,
                    panelHeight,
                    16
                );
                panelContainer.setMask(panelMask.createGeometryMask());

                // --- Slide Content Container ---
                let activeSlideContainer = this.add.container(0, 0);
                panelContainer.add(activeSlideContainer);
                buildSlideContent(0, activeSlideContainer);
                updateDots(0);

                // --- Fungsi Navigasi Slide ---
                // Referensi tombol nav akan disimpan agar bisa di-bringToTop
                let navBtnLeft, navBtnRight;

                const navigateTo = (nextIdx, dir) => {
                    if (isSliding || nextIdx < 0 || nextIdx >= categories.length) return;
                    isSliding = true;

                    const oldContainer = activeSlideContainer;

                    // Kontainer baru masuk dari sisi berlawanan
                    const newContainer = this.add.container(dir * (panelWidth * 0.85), 0);
                    panelContainer.add(newContainer);
                    buildSlideContent(nextIdx, newContainer);

                    // Pastikan tombol navigasi selalu di atas slide container
                    if (navBtnLeft) panelContainer.bringToTop(navBtnLeft);
                    if (navBtnRight) panelContainer.bringToTop(navBtnRight);

                    // Slide lama keluar
                    this.tweens.add({
                        targets: oldContainer,
                        x: -dir * (panelWidth * 0.85),
                        alpha: 0,
                        duration: 240,
                        ease: 'Cubic.easeIn',
                        onComplete: () => { oldContainer.destroy(true); }
                    });

                    // Slide baru masuk
                    this.tweens.add({
                        targets: newContainer,
                        x: 0,
                        alpha: { from: 0.2, to: 1 },
                        duration: 240,
                        ease: 'Cubic.easeOut',
                        onComplete: () => {
                            activeSlideContainer = newContainer;
                            currentSlide = nextIdx;
                            updateDots(currentSlide);
                            isSliding = false;
                        }
                    });
                };

                // --- Tombol Navigasi (Kiri & Kanan) ---
                const NAV_X = panelWidth / 2 - 20;
                const NAV_SIZE = 30;

                const makeNavBtn = (xPos, textureKey, onClick) => {
                    const btn = this.add.image(xPos, 0, textureKey);
                    panelContainer.add(btn);

                    // Set filter mode ke NEAREST agar tetap tajam (pixel art)
                    btn.texture.setFilter(Phaser.Textures.FilterMode.NEAREST);

                    // Sesuaikan ukuran tombol agar terlihat pas di panel kustomisasi
                    btn.setDisplaySize(32, 32);

                    const baseScale = btn.scaleX; // scaleX dan scaleY sama karena gambar persegi

                    btn.setInteractive({ useHandCursor: true });
                    btn.on('pointerover', () => {
                        this.tweens.add({ targets: btn, scaleX: baseScale * 1.12, scaleY: baseScale * 1.12, duration: 80 });
                    });
                    btn.on('pointerout', () => {
                        this.tweens.add({ targets: btn, scaleX: baseScale, scaleY: baseScale, duration: 80 });
                    });
                    btn.on('pointerdown', () => {
                        this.tweens.add({ targets: btn, scaleX: baseScale * 0.85, scaleY: baseScale * 0.85, duration: 60, yoyo: true });
                        onClick();
                    });
                    return btn;
                };

                navBtnLeft = makeNavBtn(-NAV_X, 'btn_kiri', () => navigateTo(currentSlide - 1, -1));
                navBtnRight = makeNavBtn(NAV_X, 'btn_kanan', () => navigateTo(currentSlide + 1, 1));

                // Pastikan dot indicator dan tombol navigasi di atas slide awal
                dotGfxList.forEach(dg => panelContainer.bringToTop(dg));
                panelContainer.bringToTop(navBtnLeft);
                panelContainer.bringToTop(navBtnRight);


                // Handler perubahan file input (dipicu setelah user memilih gambar)
                document.getElementById('corak-upload-input').addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('file', file);

                    fetch('/tukang-jaluar/upload-corak', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: formData
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                const url = data.url;
                                try { localStorage.setItem('corak_data_url', url); } catch (err) { /* Abaikan jika storage penuh */ }

                                this.applyCorak(url);

                                if (this.updateClearBtnState) {
                                    this.updateClearBtnState();
                                }
                                saveCustomizationsToServer(this);
                            } else {
                                showCustomAlertModal(this, 'Gagal mengunggah corak: ' + (data.error || 'Terjadi kesalahan'));
                            }
                        })
                        .catch(err => {
                            console.error('Error uploading corak:', err);
                            showCustomAlertModal(this, 'Gagal mengunggah corak.');
                        });

                    // Reset nilai input agar file yang sama bisa di-upload lagi
                    e.target.value = '';
                });

                // Handler perubahan file input lambai-lambai (dipicu setelah user memilih gambar)
                document.getElementById('lambai-upload-input').addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('file', file);

                    fetch('/tukang-jaluar/upload-lambai', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: formData
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                const url = data.url;
                                try { localStorage.setItem('lambai_data_url', url); } catch (err) { /* Abaikan jika storage penuh */ }

                                this.applyLambai(url);

                                if (this.updateClearBtnState) {
                                    this.updateClearBtnState();
                                }
                                saveCustomizationsToServer(this);
                            } else {
                                showCustomAlertModal(this, 'Gagal mengunggah lambai-lambai: ' + (data.error || 'Terjadi kesalahan'));
                            }
                        })
                        .catch(err => {
                            console.error('Error uploading lambai-lambai:', err);
                            showCustomAlertModal(this, 'Gagal mengunggah lambai-lambai.');
                        });

                    // Reset nilai input agar file yang sama bisa di-upload lagi
                    e.target.value = '';
                });

                // Muat corak dari localStorage jika tersimpan dari sesi sebelumnya
                const savedCorak = localStorage.getItem('corak_data_url');
                if (savedCorak) {
                    // Delay kecil agar boatImg sudah terrender sepenuhnya di scene
                    this.time.delayedCall(150, () => {
                        this.applyCorak(savedCorak);
                        if (this.updateClearBtnState) {
                            this.updateClearBtnState();
                        }
                    });
                }

                // Muat lambai-lambai dari localStorage jika tersimpan dari sesi sebelumnya
                const savedLambai = localStorage.getItem('lambai_data_url');
                if (savedLambai) {
                    this.time.delayedCall(150, () => {
                        this.applyLambai(savedLambai);
                        if (this.updateClearBtnState) {
                            this.updateClearBtnState();
                        }
                    });
                }


                // Handler resize agar posisi container tetap responsif
                const resizeBox = () => {
                    const currentW = this.scale.width;
                    boxContainer.setPosition(currentW / 2, boxY);
                    panelContainer.setPosition(currentW / 2, 400);
                };
                this.scale.on('resize', resizeBox);
                this.events.once('shutdown', () => {
                    this.scale.off('resize', resizeBox);
                });

                // Native event listener untuk menghindari pemblokiran gesture user (seperti klik file upload/download)
                // oleh browser Safari (iOS/iPhone) dan beberapa browser desktop yang mendeteksi Phaser events sebagai non-user gesture.
                let lastTriggerTime = 0;
                const canvas = this.sys.game.canvas;
                const handleNativeInteraction = (e) => {
                    // Hanya deteksi jika di slide JALUR (slide 0) atau LAMBAI LAMBAI (slide 1) dan tidak sedang dalam transisi slide
                    if ((currentSlide !== 0 && currentSlide !== 1) || isSliding) return;

                    // Blokir interaksi jika slide/fitur terkunci
                    if (currentSlide === 0 && !this.boatUnlocked) return;
                    if (currentSlide === 1 && !this.lambaiUnlocked) return;

                    const now = Date.now();
                    if (now - lastTriggerTime < 300) return;

                    const rect = canvas.getBoundingClientRect();
                    const clientX = e.clientX !== undefined ? e.clientX : (e.changedTouches && e.changedTouches[0] ? e.changedTouches[0].clientX : undefined);
                    const clientY = e.clientY !== undefined ? e.clientY : (e.changedTouches && e.changedTouches[0] ? e.changedTouches[0].clientY : undefined);

                    if (clientX === undefined || clientY === undefined) return;

                    // Konversi koordinat browser (CSS) ke sistem koordinat game Phaser
                    const x = (clientX - rect.left) * (this.scale.width / rect.width);
                    const y = (clientY - rect.top) * (this.scale.height / rect.height);

                    // Batas-batas tombol (Upload dan Template)
                    const btnY = 52;
                    const btnW = 82;
                    const btnH = 26;
                    const panelY = 400;
                    const btnCenterY = panelY + btnY; // 452

                    // 1. TOMBOL UPLOAD (X = -86 relatif ke center panel)
                    const uploadCenterX = this.scale.width / 2 - 86;
                    const uploadLeft = uploadCenterX - btnW / 2;
                    const uploadRight = uploadCenterX + btnW / 2;
                    const uploadTop = btnCenterY - btnH / 2;
                    const uploadBottom = btnCenterY + btnH / 2;

                    if (x >= uploadLeft && x <= uploadRight && y >= uploadTop && y <= uploadBottom) {
                        lastTriggerTime = now;
                        const inputId = (currentSlide === 0) ? 'corak-upload-input' : 'lambai-upload-input';
                        const fileInput = document.getElementById(inputId);
                        if (fileInput) {
                            fileInput.click();
                        }
                        return;
                    }

                    // 2. TOMBOL TEMPLATE (X = 0 relatif ke center panel)
                    const templateCenterX = this.scale.width / 2;
                    const templateLeft = templateCenterX - btnW / 2;
                    const templateRight = templateCenterX + btnW / 2;
                    const templateTop = btnCenterY - btnH / 2;
                    const templateBottom = btnCenterY + btnH / 2;

                    if (x >= templateLeft && x <= templateRight && y >= templateTop && y <= templateBottom) {
                        lastTriggerTime = now;
                        const filename = (currentSlide === 0) ? 'template_corak.png' : 'template_lambai.png';
                        const link = document.createElement('a');
                        link.href = `/game_pacu/assets/template/${filename}`;
                        link.download = filename;
                        link.click();
                        return;
                    }
                };

                // Daftarkan listener native click dan touchend pada canvas
                canvas.addEventListener('click', handleNativeInteraction);
                canvas.addEventListener('touchend', handleNativeInteraction);

                // Bersihkan event listener jika scene dimatikan/hancur
                this.events.once('shutdown', () => {
                    canvas.removeEventListener('click', handleNativeInteraction);
                    canvas.removeEventListener('touchend', handleNativeInteraction);
                });


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
                                window.location.href = '/main-menu';
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
                //  TOP RIGHT: KOIN (seperti di mainmenu.php)
                // =============================================
                const BAR_Y = 34;
                const COIN_ICON_X = W - 78;

                const coinImg = this.add.image(COIN_ICON_X, BAR_Y, 'koin')
                    .setDisplaySize(36, 36)
                    .setInteractive({ useHandCursor: true });

                // Animasi bounce + aksi klik koin
                coinImg.on('pointerdown', () => {
                    this.tweens.add({
                        targets: coinImg,
                        scaleX: 0.7, scaleY: 0.7,
                        duration: 80, ease: 'Power2',
                        yoyo: true
                    });
                });

                this.coinText = this.add.text(COIN_ICON_X + 22, BAR_Y + 1, String(this.coinCount), {
                    fontFamily: '"Pixelify Sans", monospace',
                    fontSize: '13px',
                    fontStyle: 'bold',
                    color: '#FFD700',
                    stroke: '#15803d',
                    strokeThickness: 3
                }).setOrigin(0, 0.5);

                // Shimmer koin — ikuti bentuk PNG via BitmapMask
                addIconShimmer(this, coinImg, 1100);

                // Shimmer teks koin — disabled for performance
                /*
                const txb = this.coinText.getBounds();
                const tix = txb.left, tiy = txb.top, tw = txb.width, th = txb.height;
                const tShimGfx = this.add.graphics();
                tShimGfx.setMask(this.coinText.createBitmapMask());
                const tW = tw * 0.32, tSl = th * 0.65;
                const tPos = { v: tix - tW - tSl * 2 };
                this.tweens.add({
                    targets: tPos, v: tix + tw + tW,
                    duration: 600, ease: 'Quad.easeInOut',
                    delay: 1400, repeat: -1, repeatDelay: 2600,
                    onUpdate: () => {
                        tShimGfx.clear();
                        tShimGfx.fillStyle(0xffffff, 0.60);
                        tShimGfx.fillPoints([
                            { x: tPos.v, y: tiy },
                            { x: tPos.v + tW, y: tiy },
                            { x: tPos.v + tW + tSl * 2, y: tiy + th },
                            { x: tPos.v + tSl * 2, y: tiy + th },
                        ], true);
                    },
                    onRepeat: () => tShimGfx.clear()
                });
                */

                // =============================================
                //  TUKANG JALUAR CHARACTER ANIMATION (CENTERED)
                // =============================================
                // Karakter di luar layar (W + 100), digeser lebih ke bawah (H - 100)
                const tukangImg = this.add.image(W + 100, H - 100, 'tukang')
                    .setDisplaySize(96, 96)
                    .setOrigin(0.5);

                // Tambah shimmer pada Tukang Jaluar
                addIconShimmer(this, tukangImg, 600);

                // Buat Speech Bubble Container
                const bubble = this.add.container(cx + 40, H - 185);
                bubble.setScale(0);

                // Bubble Image background
                const bubbleBg = this.add.image(0, 0, 'bubblechat')
                    .setDisplaySize(136, 66);
                bubble.add(bubbleBg);

                // Tambah shimmer pada bubble chat
                addIconShimmer(this, bubbleBg, 1200);

                // bubble text (sekarang relatif 0,0 karena bubbleBg menggunakan origin 0.5)
                const bubbleTxt = this.add.text(0, -8, "", {
                    fontFamily: '"Press Start 2P", monospace',
                    fontSize: '9px',
                    color: '#000000',
                    align: 'center',
                    lineSpacing: 4
                }).setOrigin(0.5);
                bubble.add(bubbleTxt);

                // Container untuk tombol SKIP dan NEXT
                const skipBtn = this.add.container(-32, 20);
                const nextBtn = this.add.container(32, 20);
                bubble.add(skipBtn);
                bubble.add(nextBtn);

                // Tombol SKIP (Merah)
                const skipGfx = this.add.graphics();
                const drawSkipGfx = (hovered) => {
                    skipGfx.clear();
                    skipGfx.fillStyle(hovered ? 0xfecaca : 0xef4444, 1);
                    skipGfx.lineStyle(1.5, 0xb91c1c, 1);
                    skipGfx.fillRoundedRect(-24, -7, 48, 14, 3);
                    skipGfx.strokeRoundedRect(-24, -7, 48, 14, 3);
                };
                drawSkipGfx(false);
                skipBtn.add(skipGfx);

                const skipText = this.add.text(0, -0.5, "SKIP", {
                    fontFamily: '"Press Start 2P", monospace',
                    fontSize: '6.5px',
                    color: '#ffffff',
                    fontStyle: 'bold'
                }).setOrigin(0.5);
                skipBtn.add(skipText);

                // Tombol NEXT (Hijau)
                const nextGfx = this.add.graphics();
                const drawNextGfx = (hovered) => {
                    nextGfx.clear();
                    nextGfx.fillStyle(hovered ? 0xbbf7d0 : 0x22c55e, 1);
                    nextGfx.lineStyle(1.5, 0x15803d, 1);
                    nextGfx.fillRoundedRect(-24, -7, 48, 14, 3);
                    nextGfx.strokeRoundedRect(-24, -7, 48, 14, 3);
                };
                drawNextGfx(false);
                nextBtn.add(nextGfx);

                const nextText = this.add.text(0, -0.5, "NEXT", {
                    fontFamily: '"Press Start 2P", monospace',
                    fontSize: '6.5px',
                    color: '#ffffff',
                    fontStyle: 'bold'
                }).setOrigin(0.5);
                nextBtn.add(nextText);

                // Dialogue data
                const dialogs = [
                    "Salam\nkayuah!",
                    "Mau diapain\nni jalurnya?",
                    "Wahhh keren\njuga kamu"
                ];
                let currentDialogIdx = 0;
                let typingTimer = null;

                const showDialog = (index) => {
                    if (typingTimer) {
                        typingTimer.destroy();
                        typingTimer = null;
                    }

                    const fullText = dialogs[index];
                    bubbleTxt.setText('');
                    let charIndex = 0;

                    typingTimer = this.time.addEvent({
                        delay: 60, // Kecepatan mengetik
                        repeat: fullText.length - 1,
                        callback: () => {
                            if (!bubbleTxt.scene) return;
                            charIndex++;
                            bubbleTxt.setText(fullText.slice(0, charIndex));
                        }
                    });
                };

                // Definisikan fungsi keluar untuk Tukang & Bubble
                let bubbleFloat = null;
                let tukangFloat = null;

                const exitTukang = () => {
                    if (bubbleFloat) bubbleFloat.stop();
                    if (tukangFloat) tukangFloat.stop();

                    // Hilangkan bubble chat
                    this.tweens.add({
                        targets: bubble,
                        scaleX: 0,
                        scaleY: 0,
                        duration: 300,
                        ease: 'Back.easeIn'
                    });

                    // Jalan keluar ke kiri (X = -120)
                    this.tweens.add({
                        targets: tukangImg,
                        x: -120,
                        duration: 1600,
                        ease: 'Power1.easeIn',
                        onComplete: () => {
                            tukangImg.destroy();
                            bubble.destroy();
                        }
                    });

                    // Efek bouncing berjalan saat keluar
                    this.tweens.add({
                        targets: tukangImg,
                        y: H - 112,
                        duration: 220,
                        yoyo: true,
                        repeat: 7,
                        ease: 'Sine.easeInOut'
                    });
                };

                // Interaktivitas tombol SKIP
                skipBtn.setSize(48, 14);
                skipBtn.setInteractive({ useHandCursor: true });
                skipBtn.on('pointerover', () => drawSkipGfx(true));
                skipBtn.on('pointerout', () => drawSkipGfx(false));
                skipBtn.on('pointerdown', () => {
                    this.tweens.add({
                        targets: skipBtn,
                        scaleX: 0.9, scaleY: 0.9,
                        duration: 50, yoyo: true,
                        onComplete: () => {
                            exitTukang();
                        }
                    });
                });

                // Interaktivitas tombol NEXT
                nextBtn.setSize(48, 14);
                nextBtn.setInteractive({ useHandCursor: true });
                nextBtn.on('pointerover', () => drawNextGfx(true));
                nextBtn.on('pointerout', () => drawNextGfx(false));
                nextBtn.on('pointerdown', () => {
                    this.tweens.add({
                        targets: nextBtn,
                        scaleX: 0.9, scaleY: 0.9,
                        duration: 50, yoyo: true,
                        onComplete: () => {
                            const fullText = dialogs[currentDialogIdx];

                            // Jika teks sedang diketik, langsung tampilkan penuh
                            if (bubbleTxt.text.length < fullText.length) {
                                if (typingTimer) {
                                    typingTimer.destroy();
                                    typingTimer = null;
                                }
                                bubbleTxt.setText(fullText);
                                return;
                            }

                            // Lanjut ke dialog berikutnya
                            if (currentDialogIdx < dialogs.length - 1) {
                                currentDialogIdx++;
                                showDialog(currentDialogIdx);
                                if (currentDialogIdx === dialogs.length - 1) {
                                    nextText.setText("CLOSE");
                                }
                            } else {
                                // Keluar jika sudah klik CLOSE pada halaman terakhir
                                exitTukang();
                            }
                        }
                    });
                });

                // Slide-in Animation untuk Tukang dari kanan ke kiri dengan BOUNCE
                this.tweens.add({
                    targets: tukangImg,
                    x: cx,
                    duration: 1800,
                    ease: 'Bounce.easeOut',
                    onComplete: () => {
                        // Tampilkan balon percakapan dengan efek pop
                        this.tweens.add({
                            targets: bubble,
                            scaleX: 1,
                            scaleY: 1,
                            duration: 350,
                            ease: 'Back.easeOut',
                            onComplete: () => {
                                showDialog(0);
                            }
                        });

                        // Idle floating untuk bubble
                        bubbleFloat = this.tweens.add({
                            targets: bubble,
                            y: H - 189,
                            duration: 800,
                            yoyo: true,
                            repeat: -1,
                            ease: 'Sine.easeInOut'
                        });

                        // Idle floating untuk Tukang (berdiam & mengambang secara halus di tengah)
                        tukangFloat = this.tweens.add({
                            targets: tukangImg,
                            y: H - 105,
                            duration: 800,
                            yoyo: true,
                            repeat: -1,
                            ease: 'Sine.easeInOut'
                        });
                    }
                });



                // ---- Footer ----
                this.add.text(cx, H - 22, 'v1.0.0  |  Kustomisasi Grid', {
                    fontFamily: '"Pixelify Sans", monospace',
                    fontSize: '11px',
                    fontStyle: 'bold',
                    color: 'rgba(255, 255, 255, 0.3)'
                }).setOrigin(0.5);
            }
        }

        // =====================================================
        //  INIT PHASER WITH SERVER CUSTOMIZATIONS
        // =====================================================
        fetch('/tukang-jaluar/get')
            .then(res => res.json())
            .then(data => {
                window.serverCustomizations = data;
                if (data.customColors) {
                    for (const key in data.customColors) {
                        localStorage.setItem('custom_' + key, data.customColors[key]);
                    }
                }
                if (data.corak_data_url) {
                    localStorage.setItem('corak_data_url', data.corak_data_url);
                } else {
                    localStorage.removeItem('corak_data_url');
                }
                if (data.lambai_data_url) {
                    localStorage.setItem('lambai_data_url', data.lambai_data_url);
                } else {
                    localStorage.removeItem('lambai_data_url');
                }
                if (data.coins !== undefined) {
                    localStorage.setItem('coins', String(data.coins));
                }

                bootPhaser();
            })
            .catch(err => {
                console.error('Failed to load customizations, booting with defaults:', err);
                bootPhaser();
            });

        function bootPhaser() {
            const game = new Phaser.Game({
                type: Phaser.AUTO,
                width: GAME_WIDTH,
                height: GAME_HEIGHT,
                backgroundColor: '#0f172a',
                parent: 'game-container',
                pixelArt: true,
                scene: [CustomizeScene],
                scale: {
                    mode: Phaser.Scale.RESIZE,
                    autoCenter: Phaser.Scale.CENTER_BOTH
                }
            });
        }
    </script>

    <!-- Input file tersembunyi untuk upload corak jalur (dipicu oleh tombol Phaser) -->
    <input type="file" id="corak-upload-input" accept="image/*" style="display:none;">
    <!-- Input file tersembunyi untuk upload lambai-lambai (dipicu oleh tombol Phaser) -->
    <input type="file" id="lambai-upload-input" accept="image/*" style="display:none;">

    <script src="/game_pacu/assets/js/game-layout.js?v=<?= time() ?>"></script>
</body>

</html>