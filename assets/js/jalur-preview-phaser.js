function preloadJalurAssets(scene) {
    scene.load.image('jalur_boat', '/game_pacu/assets/image/jalur/jalur.png');
    scene.load.image('char1', '/game_pacu/assets/image/char/1.png');
    scene.load.image('char2', '/game_pacu/assets/image/char/2.png');
    scene.load.image('char3', '/game_pacu/assets/image/char/3.png');
    scene.load.image('char4', '/game_pacu/assets/image/char/4.png');
    scene.load.image('char5', '/game_pacu/assets/image/char/5.png');
}

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
        const r = data[i], g = data[i + 1], b = data[i + 2], a = data[i + 3];
        if (a < 10) continue;
        if (r < 40 && g < 40 && b < 40) continue;

        if (r - g > 100 && r - b > 100) {
            const factor = Math.min(1.2, r / 199);
            data[i] = Math.min(255, targetHair.r * factor);
            data[i + 1] = Math.min(255, targetHair.g * factor);
            data[i + 2] = Math.min(255, targetHair.b * factor);
        } else if (g - r > 50 && g - b > 40) {
            const factor = Math.min(1.2, g / 122);
            data[i] = Math.min(255, targetPants.r * factor);
            data[i + 1] = Math.min(255, targetPants.g * factor);
            data[i + 2] = Math.min(255, targetPants.b * factor);
        } else if (b - r > 80 && b - g > 40) {
            const factor = Math.min(1.2, b / 203);
            data[i] = Math.min(255, targetPaddle.r * factor);
            data[i + 1] = Math.min(255, targetPaddle.g * factor);
            data[i + 2] = Math.min(255, targetPaddle.b * factor);
        } else if (Math.abs(r - g) < 20 && Math.abs(g - b) < 20 && Math.abs(r - b) < 20) {
            const factor = Math.min(1.2, ((r + g + b) / 3) / 78);
            data[i] = Math.min(255, targetShirt.r * factor);
            data[i + 1] = Math.min(255, targetShirt.g * factor);
            data[i + 2] = Math.min(255, targetShirt.b * factor);
        }
    }
    ctx.putImageData(imgData, 0, 0);
    return canvas;
}

function createJalurPreview(scene, cx, cy, scaleMult = 1.0) {
    scene.customColors = {
        boat: '#8b4513',
        hair: '#e53e3e',
        shirt: '#a0aec0',
        pants: '#38a169',
        paddle: '#3182ce',
        splash: '#a5f3fc'
    };

    const BOAT_SCALE = 2.3 * scaleMult;
    const ROWER_SCALE = 0.18 * scaleMult;
    const BOAT_OFFSET_X = 0;
    const BOAT_OFFSET_Y = 15 * scaleMult;
    const ROWER_OFFSET_X = -25 * scaleMult;
    const ROWER_OFFSET_Y = -25 * scaleMult;
    const ROWER_SPACING = 35 * scaleMult;

    const boatGroup = scene.add.container(cx, cy);
    scene.boatGroup = boatGroup;
    scene.rowerSprites = [];
    scene.waterEmitters = [];

    scene.applyRecolor = function () {
        if (scene.rowerSprites) scene.rowerSprites.forEach(r => r.stop());
        for (let f = 1; f <= 5; f++) {
            const canvas = recolorCharacterImage(scene, `char${f}`, scene.customColors);
            if (scene.textures.exists(`recolored_char${f}`)) scene.textures.remove(`recolored_char${f}`);
            scene.textures.addCanvas(`recolored_char${f}`, canvas);
        }
        if (scene.anims.exists('rowing_anim')) scene.anims.remove('rowing_anim');
        scene.anims.create({
            key: 'rowing_anim',
            frames: [
                { key: 'recolored_char1' }, { key: 'recolored_char2' },
                { key: 'recolored_char3' }, { key: 'recolored_char4' },
                { key: 'recolored_char5' }
            ],
            frameRate: 8, repeat: -1
        });
        if (scene.rowerSprites) scene.rowerSprites.forEach(r => r.play('rowing_anim'));
        if (scene.boatImg) {
            const boatColorInt = Phaser.Display.Color.HexStringToColor(scene.customColors.boat).color;
            scene.boatImg.setTint(boatColorInt);
        }
        if (scene.waterEmitters && scene.waterEmitters.length > 0) {
            const splashColorInt = Phaser.Display.Color.HexStringToColor(scene.customColors.splash).color;
            scene.waterEmitters.forEach(e => e.setParticleTint(splashColorInt));
        }
    };

    scene.applyCorak = function (dataUrl) {
        if (!dataUrl) return;
        const img = new Image();
        img.onload = () => {
            if (!scene.sys.isActive()) return;
            const boatSource = scene.textures.get('jalur_boat').getSourceImage();
            const CORAK_SCALE = 2.3 * scaleMult;
            const CORAK_OFFSET_X = 0;
            const CORAK_OFFSET_Y = 15 * scaleMult;
            const CORAK_ALPHA = 0.82;
            const CORAK_BLEND = Phaser.BlendModes.MULTIPLY;
            const displayW = Math.round(boatSource.width * CORAK_SCALE);
            const displayH = Math.round(boatSource.height * CORAK_SCALE);

            const maskCanvas = document.createElement('canvas');
            maskCanvas.width = displayW; maskCanvas.height = displayH;
            const ctx = maskCanvas.getContext('2d');

            const scaleFactor = (displayW / img.width);
            const drawW = displayW;
            const drawH = Math.round(img.height * scaleFactor);
            const drawX = 0;
            const drawY = Math.round((displayH - drawH) / 2);

            const isPixelArt = (img.width <= 256 && img.height <= 256);
            ctx.imageSmoothingEnabled = !isPixelArt;
            if (ctx.imageSmoothingEnabled) ctx.imageSmoothingQuality = 'high';
            ctx.drawImage(img, drawX, drawY, drawW, drawH);

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

            ctx.imageSmoothingEnabled = false;
            ctx.globalCompositeOperation = 'destination-in';
            ctx.drawImage(boatSource, 0, 0, displayW, displayH);
            ctx.globalCompositeOperation = 'source-over';
            ctx.imageSmoothingEnabled = true;

            if (scene.textures.exists('corak_texture')) scene.textures.remove('corak_texture');
            scene.textures.addCanvas('corak_texture', maskCanvas);

            if (scene.corakSprite) { scene.corakSprite.destroy(); scene.corakSprite = null; }
            scene.corakSprite = scene.make.image({ x: CORAK_OFFSET_X, y: CORAK_OFFSET_Y, key: 'corak_texture', add: false });
            scene.corakSprite.setScale(1.0);
            scene.corakSprite.setAlpha(CORAK_ALPHA);
            scene.corakSprite.setBlendMode(CORAK_BLEND);

            scene.boatGroup.add(scene.corakSprite);
            if (scene.boatImg && scene.boatGroup.list.includes(scene.boatImg)) {
                scene.boatGroup.moveTo(scene.corakSprite, scene.boatGroup.getIndex(scene.boatImg) + 1);
            }
        };
        img.src = dataUrl;
    };

    scene.applyLambai = function (dataUrl) {
        if (!dataUrl) return;
        const img = new Image();
        img.onload = () => {
            if (!scene.sys.isActive()) return;
            const LAMBAI_SCALE = 1.3 * scaleMult;
            const LAMBAI_OFFSET_X = 125 * scaleMult;
            const LAMBAI_OFFSET_Y = -18 * scaleMult;
            const targetSize = 48;
            let w = img.width, h = img.height;
            if (w > h) { h = Math.round((h / w) * targetSize); w = targetSize; }
            else { w = Math.round((w / h) * targetSize); h = targetSize; }

            const canvas = document.createElement('canvas');
            canvas.width = w; canvas.height = h;
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

            if (scene.textures.exists('lambai_texture')) scene.textures.remove('lambai_texture');
            scene.textures.addCanvas('lambai_texture', canvas);

            if (scene.lambaiSprite) {
                if (scene.lambaiTween) scene.lambaiTween.stop();
                scene.lambaiSprite.destroy(); scene.lambaiSprite = null;
            }
            scene.lambaiSprite = scene.make.image({ x: LAMBAI_OFFSET_X, y: LAMBAI_OFFSET_Y, key: 'lambai_texture', add: false });
            scene.lambaiSprite.setScale(LAMBAI_SCALE);
            scene.lambaiSprite.texture.setFilter(Phaser.Textures.FilterMode.NEAREST);

            scene.boatGroup.add(scene.lambaiSprite);
            if (scene.boatImg && scene.boatGroup.list.includes(scene.boatImg)) {
                scene.boatGroup.moveTo(scene.lambaiSprite, scene.boatGroup.getIndex(scene.boatImg));
            }
            scene.lambaiTween = scene.tweens.add({
                targets: scene.lambaiSprite,
                angle: { from: 0, to: 0 },
                duration: 850, yoyo: true, repeat: -1, ease: 'Sine.easeInOut'
            });
        };
        img.src = dataUrl;
    };

    scene.boatImg = scene.add.image(BOAT_OFFSET_X, BOAT_OFFSET_Y, 'jalur_boat');
    scene.boatImg.setScale(BOAT_SCALE);
    scene.boatImg.texture.setFilter(Phaser.Textures.FilterMode.NEAREST);
    boatGroup.add(scene.boatImg);

    if (scene.textures.exists('water_particle')) scene.textures.remove('water_particle');
    const pGfx = scene.make.graphics({ add: false });
    pGfx.fillStyle(0xffffff, 1); pGfx.fillRect(0, 0, 8, 8);
    pGfx.generateTexture('water_particle', 8, 8); pGfx.destroy();

    const SPLASH_OFFSET_X = -1 * scaleMult;
    const SPLASH_OFFSET_Y = 32 * scaleMult;
    const offsetsX = [-ROWER_SPACING * 2, -ROWER_SPACING, 0, ROWER_SPACING, ROWER_SPACING * 2];

    offsetsX.forEach((offsetX) => {
        const rowerX = BOAT_OFFSET_X + ROWER_OFFSET_X + offsetX;
        const rowerY = BOAT_OFFSET_Y + ROWER_OFFSET_Y;
        const rowerSprite = scene.add.sprite(rowerX, rowerY, 'char1');
        rowerSprite.setScale(ROWER_SCALE);
        rowerSprite.texture.setFilter(Phaser.Textures.FilterMode.NEAREST);
        boatGroup.add(rowerSprite);
        scene.rowerSprites.push(rowerSprite);

        const emitter = scene.add.particles(rowerX + SPLASH_OFFSET_X, rowerY + SPLASH_OFFSET_Y, 'water_particle', {
            speed: { min: 40 * scaleMult, max: 110 * scaleMult }, angle: { min: 280, max: 340 },
            scale: { start: 2.2 * scaleMult, end: 0 }, lifespan: { min: 300, max: 550 },
            gravityY: 350 * scaleMult, quantity: 2, frequency: -1
        });
        boatGroup.add(emitter);
        scene.waterEmitters.push(emitter);

        rowerSprite.on('animationupdate', (anim, frame) => {
            if (frame.index === 3 || frame.index === 4) {
                emitter.explode(18);
            }
        });
    });

    scene.tweens.add({
        targets: boatGroup,
        y: boatGroup.y + 4 * scaleMult,
        duration: 1200,
        yoyo: true,
        repeat: -1,
        ease: 'Sine.easeInOut'
    });

    fetch('/tukang-jaluar/get')
        .then(res => res.json())
        .then(data => {
            if (data.customColors) {
                scene.customColors = data.customColors;
            }
            scene.applyRecolor();

            if (data.boat_unlocked && data.corak_data_url) {
                scene.applyCorak(data.corak_data_url);
            }
            if (data.lambai_unlocked && data.lambai_data_url) {
                scene.applyLambai(data.lambai_data_url);
            }
        })
        .catch(err => {
            console.error('Error loading customization from DB:', err);
            scene.applyRecolor();
        });

    return boatGroup;
}

window.initJalurPreview = function (containerId, nameElId) {
    if (!document.getElementById(containerId)) return;

    if (!window.Phaser) {
        const script = document.createElement('script');
        script.src = "https://cdn.jsdelivr.net/npm/phaser@3.88.2/dist/phaser.min.js";
        script.onload = function () {
            window.initJalurPreview(containerId, nameElId);
        };
        document.head.appendChild(script);
        return;
    }

    const game = new Phaser.Game({
        type: Phaser.AUTO,
        width: 250,
        height: 85,
        transparent: true,
        parent: containerId,
        pixelArt: true,
        scene: {
            preload: function () {
                preloadJalurAssets(this);
            },
            create: function () {
                const scene = this;

                fetch('/tukang-jaluar/get')
                    .then(res => res.json())
                    .then(data => {
                        const nameEl = document.getElementById(nameElId);
                        if (nameEl) {
                            nameEl.innerText = data.nama_jalur || 'Jalur Kuansing';
                        }
                    })
                    .catch(err => console.error(err));

                createJalurPreview(this, 125, 40, 0.8);
            }
        }
    });
};
