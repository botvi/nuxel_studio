(function () {
 'use strict';
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
 if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
 return;
 }
 const blocked = [' ', 'ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight',
 'PageUp', 'PageDown', 'Home', 'End'];
 if (blocked.includes(e.key)) {
 e.preventDefault();
 }
 });
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
 window.navigateToPage = function (url) {
 if (typeof Livewire !== 'undefined' && typeof Livewire.navigate === 'function') {
 Livewire.navigate(url);
 } else {
 window.location.href = url;
 }
 };
 document.addEventListener('DOMContentLoaded', function () {
 const frame = document.getElementById('mobile-frame') || document.body;
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
 setTimeout(function () {
 overlay.classList.add('fade-out');
 }, 150);
 }
 });
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
 if (a < 10) continue;
 if (r < 40 && g < 40 && b < 40) continue;
 if (r - g > 100 && r - b > 100) {
 const factor = Math.min(1.2, r / 199);
 data[i] = Math.min(255, targetHair.r * factor);
 data[i + 1] = Math.min(255, targetHair.g * factor);
 data[i + 2] = Math.min(255, targetHair.b * factor);
 }
 else if (g - r > 50 && g - b > 40) {
 const factor = Math.min(1.2, g / 122);
 data[i] = Math.min(255, targetPants.r * factor);
 data[i + 1] = Math.min(255, targetPants.g * factor);
 data[i + 2] = Math.min(255, targetPants.b * factor);
 }
 else if (b - r > 80 && b - g > 40) {
 const factor = Math.min(1.2, b / 203);
 data[i] = Math.min(255, targetPaddle.r * factor);
 data[i + 1] = Math.min(255, targetPaddle.g * factor);
 data[i + 2] = Math.min(255, targetPaddle.b * factor);
 }
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
 window.playClickSound = function () {
 const isMuted = localStorage.getItem('sfx_muted') === 'true';
 if (isMuted) return;
 const audio = new Audio('/game_pacu/assets/sound/klik_btn.ogg');
 audio.volume = 0.4;
 audio.play().catch(function (err) {
 console.log('Audio play blocked:', err);
 });
 };
 document.addEventListener('pointerdown', function (e) {
 const target = e.target.closest('button, a, .btn, .btn-small, .menu-card, .profile-btn, .sound-btn, .pixel-btn, [onclick], .back-btn, .room-item button, .fullscreen-btn');
 if (target) {
 window.playClickSound();
 }
 });
 (function () {
  var bgmSrc = '/game_pacu/assets/sound/bgm.ogg';

  function applyMuteState(audio) {
   var isMuted = localStorage.getItem('bgm_muted') === 'true';
   if (isMuted) {
    audio.volume = 0;
    audio.muted = true;
   } else {
    audio.volume = 0.5;
    audio.muted = false;
   }
  }

  function tryPlay(audio) {
   applyMuteState(audio);
   if (!audio.muted) {
    audio.play().catch(function (err) {
     console.log('[BGM] Autoplay blocked:', err);
    });
   }
  }

  function initBGM() {
   // Jika globalBGM masih ada & src-nya benar, tidak perlu buat baru
   if (window.globalBGM && window.globalBGM.src && window.globalBGM.src.includes('bgm')) {
    applyMuteState(window.globalBGM);
    // Jika sedang pause & tidak muted, lanjutkan (jangan restart)
    if (window.globalBGM.paused && !window.globalBGM.muted) {
     window.globalBGM.play().catch(function (err) {
      console.log('[BGM] Resume failed:', err);
     });
    }
    return;
   }

   // Buat instance baru hanya jika belum ada
   var bgm = new Audio(bgmSrc);
   bgm.loop = true;
   bgm.preload = 'auto';
   window.globalBGM = bgm;

   // Tandai bahwa BGM sudah diinisialisasi di sesi ini
   sessionStorage.setItem('bgm_initialized', '1');

   tryPlay(bgm);

   // Fallback: mulai setelah interaksi pertama (autoplay policy mobile)
   var playOnInteraction = function () {
    if (window.globalBGM && window.globalBGM.paused) {
     tryPlay(window.globalBGM);
    }
    document.removeEventListener('pointerdown', playOnInteraction);
    document.removeEventListener('touchstart', playOnInteraction);
    document.removeEventListener('keydown', playOnInteraction);
   };
   document.addEventListener('pointerdown', playOnInteraction, { once: true });
   document.addEventListener('touchstart', playOnInteraction, { once: true, passive: true });
   document.addEventListener('keydown', playOnInteraction, { once: true });
  }

  // Handle visibility change: pause saat app di background, resume saat kembali
  document.addEventListener('visibilitychange', function () {
   if (!window.globalBGM) return;
   if (document.hidden) {
    // App masuk background (Android home button, dll)
    window.globalBGM.pause();
   } else {
    // App kembali ke foreground, resume (bukan restart)
    var isMuted = localStorage.getItem('bgm_muted') === 'true';
    if (!isMuted && window.globalBGM.paused) {
     window.globalBGM.play().catch(function (err) {
      console.log('[BGM] Visibility resume failed:', err);
     });
    }
   }
  });

  // Handle Livewire SPA navigation - pastikan BGM tidak diulang
  document.addEventListener('livewire:navigated', function () {
   if (window.globalBGM) {
    applyMuteState(window.globalBGM);
    // Jika pause karena navigasi, lanjutkan saja (jangan restart)
    var isMuted = localStorage.getItem('bgm_muted') === 'true';
    if (!isMuted && window.globalBGM.paused) {
     window.globalBGM.play().catch(function (err) {
      console.log('[BGM] Post-navigate resume:', err);
     });
    }
   }
  });

  if (document.readyState === 'loading') {
   document.addEventListener('DOMContentLoaded', initBGM);
  } else {
   initBGM();
  }

  // =====================================================
  // GLOBAL BGM CONTROL FUNCTIONS (dipakai semua halaman)
  // =====================================================

  /**
   * Terapkan state mute ke globalBGM secara langsung dari localStorage.
   * Bisa dipanggil kapanpun untuk sync audio ke setting terbaru.
   */
  window.applyBGMMute = function () {
   if (!window.globalBGM) return;
   var isMuted = localStorage.getItem('bgm_muted') === 'true';
   if (isMuted) {
    window.globalBGM.volume = 0;
    window.globalBGM.muted = true;
    if (!window.globalBGM.paused) {
     window.globalBGM.pause();
    }
   } else {
    window.globalBGM.volume = 0.5;
    window.globalBGM.muted = false;
    if (window.globalBGM.paused) {
     window.globalBGM.play().catch(function (err) {
      console.log('[BGM] applyBGMMute play failed:', err);
     });
    }
   }
  };

  /**
   * Toggle BGM on/off — simpan ke localStorage & langsung terapkan ke audio.
   * Menggantikan implementasi per-halaman yang tidak langsung apply.
   */
  window.toggleBGMSetting = function () {
   var bgmMuted = localStorage.getItem('bgm_muted') === 'true';
   localStorage.setItem('bgm_muted', bgmMuted ? 'false' : 'true');
   // Langsung terapkan ke globalBGM tanpa menunggu event apapun
   window.applyBGMMute();
   // Sync tombol UI jika tersedia
   if (typeof window.syncAudioModalButtons === 'function') {
    window.syncAudioModalButtons();
   }
  };

  /**
   * Toggle SFX on/off — simpan ke localStorage.
   * Menggantikan implementasi per-halaman.
   */
  window.toggleSFXSetting = function () {
   var sfxMuted = localStorage.getItem('sfx_muted') === 'true';
   localStorage.setItem('sfx_muted', sfxMuted ? 'false' : 'true');
   // Sync tombol UI jika tersedia
   if (typeof window.syncAudioModalButtons === 'function') {
    window.syncAudioModalButtons();
   }
  };

  /**
   * Fallback updateSoundIcon — akan di-override oleh halaman yang perlu.
   */
  if (!window.updateSoundIcon) {
   window.updateSoundIcon = function () {};
  }

 })();
})();