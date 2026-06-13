/**
 * game-layout.js
 * Script pendukung layout game mobile.
 *
 * CARA PAKAI di file PHP baru:
 *   <script src="assets/js/game-layout.js"></script>
 *   (letakkan sebelum </body>)
 *
 * Yang dilakukan script ini:
 *   1. Memblokir semua scroll (touch, wheel, keyboard)
 *   2. Mengupdate elemen #clock setiap menit
 */

(function () {
    'use strict';

    /* -----------------------------------------------
       1. BLOKIR SCROLL — touch, wheel, keyboard
    ----------------------------------------------- */
    document.addEventListener('touchmove', function (e) {
        if (e.target.closest('.scrollable')) {
            return;
        }
        e.preventDefault();
    }, { passive: false });

    document.addEventListener('wheel', function (e) {
        if (e.target.closest('.scrollable')) {
            return;
        }
        e.preventDefault();
    }, { passive: false });

    document.addEventListener('keydown', function (e) {
        // Jangan blokir jika event berasal dari input atau textarea agar user bisa mengetik spasi dan navigasi arah
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
            return;
        }
        const blocked = [' ', 'ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight',
            'PageUp', 'PageDown', 'Home', 'End'];
        if (blocked.includes(e.key)) {
            e.preventDefault();
        }
    });

    /* -----------------------------------------------
       2. JAM DIGITAL — update elemen #clock
    ----------------------------------------------- */
    function updateClock() {
        var el = document.getElementById('clock');
        if (!el) return;
        var now = new Date();
        var h = String(now.getHours()).padStart(2, '0');
        var m = String(now.getMinutes()).padStart(2, '0');
        el.textContent = h + ':' + m;
    }

    updateClock();
    setInterval(updateClock, 15000);

    /* -----------------------------------------------
       3. KONFIRMASI FULLSCREEN (Jika diakses langsung)
    ----------------------------------------------- */
    // const isAccessedDirectly = window.self === window.top;
    // const isFullscreenSupported = document.documentElement.requestFullscreen ||
    //                               document.documentElement.webkitRequestFullscreen ||
    //                               document.documentElement.msRequestFullscreen;

    // if (isAccessedDirectly && isFullscreenSupported) {
    //     // Buat elemen modal secara dinamis
    //     const overlay = document.createElement('div');
    //     overlay.id = 'fullscreen-modal-overlay';
    //     overlay.innerHTML = `
    //         <div class="fullscreen-modal-card">
    //             <div class="fullscreen-modal-title">✦ FULLSCREEN MODE ✦</div>
    //             <div class="fullscreen-modal-body">
    //                 Mainkan game dalam mode Fullscreen untuk pengalaman bermain terbaik?
    //             </div>
    //             <div class="fullscreen-modal-buttons">
    //                 <button class="fullscreen-btn fullscreen-btn-yes" id="fs-btn-yes">YA</button>
    //                 <button class="fullscreen-btn fullscreen-btn-no" id="fs-btn-no">TIDAK</button>
    //             </div>
    //         </div>
    //     `;
    //     document.body.appendChild(overlay);

    //     // Paksa browser melakukan reflow untuk memicu animasi masuk
    //     overlay.offsetHeight;
    //     overlay.classList.add('show');

    //     const btnYes = document.getElementById('fs-btn-yes');
    //     const btnNo = document.getElementById('fs-btn-no');

    //     const requestFullscreen = () => {
    //         const docEl = document.documentElement;
    //         if (docEl.requestFullscreen) {
    //             docEl.requestFullscreen();
    //         } else if (docEl.webkitRequestFullscreen) {
    //             docEl.webkitRequestFullscreen();
    //         } else if (docEl.msRequestFullscreen) {
    //             docEl.msRequestFullscreen();
    //         }
    //     };

    //     const closeModal = () => {
    //         overlay.classList.remove('show');
    //         setTimeout(() => {
    //             overlay.remove();
    //         }, 300);
    //     };

    //     btnYes.addEventListener('click', function () {
    //         requestFullscreen();
    //         closeModal();
    //     });

    //     btnNo.addEventListener('click', function () {
    //         closeModal();
    //     });
    // }

    /* -----------------------------------------------
       4. GLOBAL PAGE TRANSITION (FADE IN/OUT)
    ----------------------------------------------- */
    // Helper global untuk navigasi via script JS
    window.navigateToPage = function (url) {
        if (typeof Livewire !== 'undefined' && typeof Livewire.navigate === 'function') {
            Livewire.navigate(url);
        } else {
            window.location.href = url;
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        const frame = document.getElementById('mobile-frame') || document.body;

        // Buat elemen transition overlay secara dinamis jika belum ada
        if (!document.getElementById('page-transition-overlay')) {
            const overlay = document.createElement('div');
            overlay.id = 'page-transition-overlay';
            overlay.innerHTML = `
                <div class="transition-content">
                    <div class="transition-title">✦ MEMUAT... ✦</div>
                    <div class="transition-bar-container">
                        <div class="transition-bar-fill"></div>
                    </div>
                </div>
            `;
            frame.appendChild(overlay);

            // Trigger reflow & fade out overlay setelah delay singkat (150ms) agar smooth
            setTimeout(function () {
                overlay.classList.add('fade-out');
            }, 150);
        }
    });

    // SPA Transitions using Livewire 3 events
    document.addEventListener('livewire:navigating', function () {
        const overlay = document.getElementById('page-transition-overlay');
        if (overlay) {
            overlay.classList.remove('fade-out');
        }
    });

    document.addEventListener('livewire:navigated', function () {
        const overlay = document.getElementById('page-transition-overlay');
        if (overlay) {
            setTimeout(function () {
                overlay.classList.add('fade-out');
            }, 150);
        }
    });

    /* -----------------------------------------------
       5. RECOLOR CHARACTER HELPER (SHARED)
    ----------------------------------------------- */
    window.recolorCharacterImage = function (scene, sourceKey, customColors) {
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
    };

    /* -----------------------------------------------
       6. GLOBAL CLICK SOUND ON INTERACTIVE ELEMENTS
    ----------------------------------------------- */
    window.playClickSound = function () {
        const isMuted = localStorage.getItem('sfx_muted') === 'true';
        if (isMuted) return;
        const audio = new Audio('/game_pacu/assets/sound/klik_btn.ogg');
        audio.volume = 0.4;
        audio.play().catch(function (err) {
            console.log('Audio play blocked:', err);
        });
    };

    // DOM click interceptor
    document.addEventListener('pointerdown', function (e) {
        const target = e.target.closest('button, a, .btn, .btn-small, .menu-card, .profile-btn, .sound-btn, .pixel-btn, [onclick], .back-btn, .room-item button, .fullscreen-btn');
        if (target) {
            window.playClickSound();
        }
    });

    /* -----------------------------------------------
       7. BACKGROUND MUSIC (BGM)
    ----------------------------------------------- */
    (function () {
        const bgmSrc = '/game_pacu/assets/sound/bgm.ogg';
        let bgm = null;

        function initBGM() {
            if (window.globalBGM) return;

            bgm = new Audio(bgmSrc);
            bgm.loop = true;
            bgm.preload = 'auto';
            window.globalBGM = bgm;

            // Sync volume/mute with settings
            const isMuted = localStorage.getItem('bgm_muted') === 'true';
            if (isMuted) {
                bgm.volume = 0;
                bgm.muted = true;
            } else {
                bgm.volume = 0.5;
                bgm.muted = false;
            }

            const startPlay = function () {
                const isMuted = localStorage.getItem('bgm_muted') === 'true';
                if (isMuted) {
                    bgm.volume = 0;
                    bgm.muted = true;
                } else {
                    bgm.volume = 0.5;
                    bgm.muted = false;
                }
                bgm.play().catch(function (err) {
                    console.log('Autoplay blocked, waiting for interaction:', err);
                });
            };

            startPlay();

            // Fallback: play on first user interaction if blocked by browser autoplay policy
            const playOnInteraction = function () {
                startPlay();
                document.removeEventListener('pointerdown', playOnInteraction);
                document.removeEventListener('keydown', playOnInteraction);
            };
            document.addEventListener('pointerdown', playOnInteraction);
            document.addEventListener('keydown', playOnInteraction);
        }

        // Periodically sync volume/mute state from localStorage changes
        setInterval(function () {
            if (bgm) {
                const isMuted = localStorage.getItem('bgm_muted') === 'true';
                if (isMuted) {
                    bgm.volume = 0;
                    bgm.muted = true;
                } else {
                    bgm.volume = 0.5;
                    bgm.muted = false;
                    if (bgm.paused) {
                        bgm.play().catch(function (err) {
                            console.log('Play failed on sync:', err);
                        });
                    }
                }
            }
        }, 300);

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initBGM);
        } else {
            initBGM();
        }
    })();

})();
