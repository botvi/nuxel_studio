<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title>Room Lobby — Papan Jawara</title>
    <link rel="stylesheet" href="/game_pacu/assets/css/game-layout.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Pixelify+Sans:wght@400;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f0fdf4;
            color: #15803d;
            font-family: 'Pixelify Sans', monospace;
            overflow: hidden;
        }

        #game-ui {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: url('/game_pacu/assets/image/bg/bgmenu.jpg') no-repeat center center;
            background-size: cover;
            z-index: 10;
            padding-bottom: 20px;
            box-sizing: border-box;
            overflow: hidden;
        }

        /* --- Dynamic Backdrop Glow --- */
        .ps5-backdrop-glow {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
            transition: background 0.8s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .bg-slide-1 {
            background: radial-gradient(circle at 50% 60%, rgba(239, 68, 68, 0.5) 0%, rgba(15, 23, 42, 0.3) 50%, rgba(20, 5, 5, 0.85) 100%);
        }

        .title-banner {
            font-family: 'Press Start 2P', monospace;
            font-size: 12px;
            background: linear-gradient(180deg, #ffffff 0%, #a5f3fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0px 2px 4px rgba(0, 0, 0, 0.8));
            margin-top: 76px;
            margin-bottom: 15px;
            text-align: center;
            line-height: 1.4;
            letter-spacing: 2px;
            z-index: 11;
        }

        .panel {
            background: rgba(15, 23, 42, 0.5);
            border: 2px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            width: 90%;
            padding: 22px 20px;
            margin-bottom: 15px;
            box-shadow:
                0 8px 32px 0 rgba(0, 0, 0, 0.4),
                0 0 15px rgba(239, 68, 68, 0.3);
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 11;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .panel-title {
            font-family: 'Press Start 2P', monospace;
            font-size: 8px;
            color: #ffaa00;
            margin-bottom: 15px;
            border-bottom: 2px dashed rgba(255, 255, 255, 0.15);
            padding-bottom: 12px;
            text-align: center;
            line-height: 1.5;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.6);
            width: 100%;
        }

        /* Lobby Players Grid */
        .players-container {
            display: flex;
            width: 100%;
            justify-content: space-around;
            gap: 12px;
            margin-bottom: 20px;
        }

        .player-card {
            flex: 1;
            background: rgba(255, 255, 255, 0.06);
            border: 2px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            padding: 16px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 170px;
            box-sizing: border-box;
            position: relative;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
            transition: all 0.2s;
        }

        .player-card.empty {
            border-style: dashed;
            border-color: rgba(255, 255, 255, 0.25);
            background: rgba(255, 255, 255, 0.02);
            justify-content: center;
            box-shadow: none;
        }

        .player-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: rgba(15, 23, 42, 0.4);
            border: 2.5px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 12px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .player-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .player-name {
            font-size: 13px;
            font-weight: 700;
            color: #ffffff;
            text-align: center;
            margin-bottom: 6px;
            max-width: 100px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.6);
        }

        .player-role {
            font-family: 'Press Start 2P', monospace;
            font-size: 7px;
            background: #e53e3e;
            color: #ffffff;
            padding: 4px 8px;
            border-radius: 6px;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .ready-badge {
            font-family: 'Press Start 2P', monospace;
            font-size: 7px;
            padding: 6px 10px;
            border-radius: 8px;
            color: white;
            text-shadow: 1.5px 1.5px 0px #000000;
            border: 2px solid #000000;
            text-align: center;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }

        .ready-badge.ready {
            background-color: #22c55e;
            box-shadow: 0 3px 0 #000000;
        }

        .ready-badge.not-ready {
            background-color: #ef4444;
            box-shadow: 0 3px 0 #000000;
        }

        /* Radar Search animation for empty slot */
        .searching-radar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 3px solid rgba(239, 68, 68, 0.4);
            margin-bottom: 10px;
            position: relative;
            animation: radarPulse 1.5s infinite ease-in-out;
            background: rgba(239, 68, 68, 0.05);
        }

        @keyframes radarPulse {
            0% {
                transform: scale(0.9);
                opacity: 0.5;
                box-shadow: 0 0 0 rgba(239, 68, 68, 0);
            }

            50% {
                transform: scale(1.05);
                opacity: 1;
                box-shadow: 0 0 10px rgba(239, 68, 68, 0.3);
            }

            100% {
                transform: scale(0.9);
                opacity: 0.5;
                box-shadow: 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        /* Buttons */
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

        .btn-red {
            background-color: #ef4444;
        }

        .btn-red:hover {
            background-color: #f87171;
        }

        .btn-red:active {
            transform: translateY(4px);
            box-shadow: inset 0 2px 0px rgba(255, 255, 255, 0.1), 0px 0px 0px #000000;
        }

        .connection-status {
            font-family: 'Press Start 2P', monospace;
            font-size: 7px;
            margin-top: 15px;
            color: #ef4444;
            letter-spacing: 0.5px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        .connection-status.connected {
            color: #22c55e;
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
                <div id="game-ui">
                    <div id="ps5-backdrop" class="ps5-backdrop-glow bg-slide-1"></div>
                    <canvas id="ps5-particles"
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none; opacity: 0.5;"></canvas>

                    <div class="title-banner">✦ ROOM LOBBY ✦</div>

                    <!-- Panel Lobby -->
                    <div class="panel">
                        <div class="panel-title" id="lobby-room-name">✦ ROOM: <?= strtoupper(e($room->name)) ?> ✦</div>

                        <div class="players-container">
                            <!-- Player 1 (Host) -->
                            <div class="player-card" id="host-card">
                                <div class="player-avatar">
                                    <?php
                                    $hostFoto = $room->host->foto_profile;
                                    if (!empty($hostFoto)) {
                                        if (strpos($hostFoto, 'http://') === 0 || strpos($hostFoto, 'https://') === 0) {
                                            $hostImg = $hostFoto;
                                        } elseif (strpos($hostFoto, '/') !== false || strpos($hostFoto, '.gif') !== false) {
                                            $hostImg = (strpos($hostFoto, '/') === 0) ? $hostFoto : '/' . $hostFoto;
                                        } else {
                                            $hostImg = '/game_pacu/assets/image/ui/' . $hostFoto . '.gif';
                                        }
                                    } else {
                                        $hostImg = '/game_pacu/assets/image/ui/profil.gif';
                                    }
                                    ?>
                                    <img src="<?= $hostImg ?>" alt="Host">
                                </div>
                                <div class="player-name"><?= e($room->host->nama_jalur ?? $room->host->email) ?></div>
                                <div class="player-role">HOST</div>
                                <div class="ready-badge not-ready" id="host-ready-badge">BELUM READY</div>
                            </div>

                            <!-- Player 2 (Guest) -->
                            <div class="player-card empty" id="guest-card">
                                <div class="searching-radar"></div>
                                <div
                                    style="font-family: 'Pixelify Sans', monospace; font-size: 10px; font-weight: bold; color: #fca5a5; text-shadow: 0 1px 2px rgba(0,0,0,0.5); text-align: center;">
                                    MENUNGGU LAWAN...</div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <button class="pixel-btn" id="ready-btn" onclick="toggleReady()">READY UP</button>
                        <button class="pixel-btn btn-red" onclick="leaveRoom()">KELUAR LOBBY</button>

                        <div class="connection-status" id="ws-status">Menghubungkan ke server...</div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="/game_pacu/assets/js/game-layout.js?v=<?= time() ?>"></script>
    <script>
        // Custom HTML Modals (Confirm & Alert) using existing game-layout.css styles
        function showHTMLAlert(message, title = "✦ INFORMASI ✦") {
            return new Promise((resolve) => {
                const overlay = document.createElement('div');
                overlay.id = 'fullscreen-modal-overlay';
                overlay.innerHTML = `
                    <div class="fullscreen-modal-card">
                        <div class="fullscreen-modal-title">${title}</div>
                        <div class="fullscreen-modal-body">${message}</div>
                        <div class="fullscreen-modal-buttons">
                            <button class="fullscreen-btn fullscreen-btn-yes" id="custom-alert-ok-btn" style="width: 120px;">OKE</button>
                        </div>
                    </div>
                `;
                document.body.appendChild(overlay);
                overlay.offsetHeight;
                overlay.classList.add('show');
                const okBtn = document.getElementById('custom-alert-ok-btn');
                okBtn.addEventListener('click', () => {
                    overlay.classList.remove('show');
                    setTimeout(() => {
                        overlay.remove();
                        resolve();
                    }, 300);
                });
            });
        }

        function showHTMLConfirm(message, title = "✦ KONFIRMASI ✦") {
            return new Promise((resolve) => {
                const overlay = document.createElement('div');
                overlay.id = 'fullscreen-modal-overlay';
                overlay.innerHTML = `
                    <div class="fullscreen-modal-card">
                        <div class="fullscreen-modal-title">${title}</div>
                        <div class="fullscreen-modal-body">${message}</div>
                        <div class="fullscreen-modal-buttons">
                            <button class="fullscreen-btn fullscreen-btn-yes" id="custom-confirm-yes-btn" style="width: 100px;">YA</button>
                            <button class="fullscreen-btn fullscreen-btn-no" id="custom-confirm-no-btn" style="width: 100px;">TIDAK</button>
                        </div>
                    </div>
                `;
                document.body.appendChild(overlay);
                overlay.offsetHeight;
                overlay.classList.add('show');
                const yesBtn = document.getElementById('custom-confirm-yes-btn');
                const noBtn = document.getElementById('custom-confirm-no-btn');
                yesBtn.addEventListener('click', () => {
                    overlay.classList.remove('show');
                    setTimeout(() => {
                        overlay.remove();
                        resolve(true);
                    }, 300);
                });
                noBtn.addEventListener('click', () => {
                    overlay.classList.remove('show');
                    setTimeout(() => {
                        overlay.remove();
                        resolve(false);
                    }, 300);
                });
            });
        }

        const roomId = "<?= $room->id ?>";
        const currentUserId = <?= auth()->id() ?>;
        const currentUserName = "<?= addslashes(auth()->user()->nama_jalur ?? auth()->user()->email) ?>";
        const currentUserPhoto = "<?= addslashes(auth()->user()->foto_profile ?? '') ?>";

        let ws;
        let isReady = false;
        let hostId = <?= $room->host_id ?>;
        let guestId = <?= $room->guest_id ?? 'null' ?>;

        // Customizations cache from localStorage
        const customColors = {
            boat: localStorage.getItem('custom_boat') || '#8b4513',
            hair: localStorage.getItem('custom_hair') || '#e53e3e',
            shirt: localStorage.getItem('custom_shirt') || '#a0aec0',
            pants: localStorage.getItem('custom_pants') || '#38a169',
            paddle: localStorage.getItem('custom_paddle') || '#3182ce',
            splash: localStorage.getItem('custom_splash') || '#a5f3fc'
        };
        const corakDataUrl = localStorage.getItem('corak_data_url') || null;
        const lambaiDataUrl = localStorage.getItem('lambai_data_url') || null;

        function initWebSocket() {
            const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
            const wsUrl = `${protocol}//${window.location.hostname}:8080`;

            ws = new WebSocket(wsUrl);

            ws.onopen = () => {
                console.log('Connected to WebSocket server');
                document.getElementById('ws-status').innerText = 'TERHUBUNG';
                document.getElementById('ws-status').className = 'connection-status connected';

                // Send Join Message
                ws.send(JSON.stringify({
                    type: 'join',
                    roomId: 'room_' + roomId,
                    payload: {
                        userId: currentUserId,
                        userName: currentUserName,
                        customizations: {
                            colors: customColors,
                            corak_data_url: corakDataUrl,
                            lambai_data_url: lambaiDataUrl,
                            photo: currentUserPhoto
                        }
                    }
                }));
            };

            ws.onmessage = (event) => {
                const message = JSON.parse(event.data);
                const { type, payload } = message;

                if (type === 'room_update') {
                    updateLobbyUI(payload.players);
                } else if (type === 'game_start') {
                    console.log('Game starting...');
                    window.location.href = `/arena-pacu?room_id=room_${roomId}`;
                }
            };

            ws.onclose = () => {
                console.log('Disconnected from WebSocket server');
                document.getElementById('ws-status').innerText = 'TERPUTUS. MENCOBA LAGI...';
                document.getElementById('ws-status').className = 'connection-status';
                setTimeout(initWebSocket, 2000);
            };

            ws.onerror = (err) => {
                console.error('WebSocket Error:', err);
            };
        }

        function updateLobbyUI(players) {
            // Reset cards
            const guestCard = document.getElementById('guest-card');

            // Find host and guest from players list
            const hostPlayer = players.find(p => parseInt(p.userId) === hostId);
            const guestPlayer = players.find(p => parseInt(p.userId) !== hostId);

            // Host status update
            const hostBadge = document.getElementById('host-ready-badge');
            if (hostPlayer && hostPlayer.ready) {
                hostBadge.innerText = 'READY';
                hostBadge.className = 'ready-badge ready';
            } else {
                hostBadge.innerText = 'BELUM READY';
                hostBadge.className = 'ready-badge not-ready';
            }

            // Guest status update
            if (guestPlayer) {
                guestId = parseInt(guestPlayer.userId);

                // Construct profile image
                let guestImgSrc = '/game_pacu/assets/image/ui/profil.gif';
                const guestPhoto = guestPlayer.customizations.photo;
                if (guestPhoto) {
                    if (guestPhoto.indexOf('http://') === 0 || guestPhoto.indexOf('https://') === 0) {
                        guestImgSrc = guestPhoto;
                    } else if (guestPhoto.indexOf('/') !== -1 || guestPhoto.indexOf('.gif') !== -1) {
                        guestImgSrc = guestPhoto.indexOf('/') === 0 ? guestPhoto : '/' + guestPhoto;
                    } else {
                        guestImgSrc = '/game_pacu/assets/image/ui/' + guestPhoto + '.gif';
                    }
                }

                guestCard.innerHTML = `
                    <div class="player-avatar">
                        <img src="${guestImgSrc}" alt="Lawan">
                    </div>
                    <div class="player-name">${guestPlayer.userName}</div>
                    <div class="player-role">LAWAN</div>
                    <div class="ready-badge ${guestPlayer.ready ? 'ready' : 'not-ready'}" id="guest-ready-badge">
                        ${guestPlayer.ready ? 'READY' : 'BELUM READY'}
                    </div>
                `;
                guestCard.className = 'player-card';
            } else {
                guestId = null;
                // Revert to empty card
                guestCard.innerHTML = `
                    <div class="searching-radar"></div>
                    <div style="font-family: 'Pixelify Sans', monospace; font-size: 10px; font-weight: bold; color: #fca5a5; text-shadow: 0 1px 2px rgba(0,0,0,0.5); text-align: center;">MENUNGGU LAWAN...</div>
                `;
                guestCard.className = 'player-card empty';
            }
        }

        function toggleReady() {
            isReady = !isReady;

            const btn = document.getElementById('ready-btn');
            if (isReady) {
                btn.innerText = 'BATAL READY';
                btn.className = 'pixel-btn btn-red';
            } else {
                btn.innerText = 'READY UP';
                btn.className = 'pixel-btn';
            }

            // Send to WebSocket
            if (ws && ws.readyState === WebSocket.OPEN) {
                ws.send(JSON.stringify({
                    type: 'ready',
                    roomId: 'room_' + roomId,
                    payload: { ready: isReady }
                }));
            }

            // Sync with HTTP endpoint in background
            fetch('/room/ready', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ room_id: roomId, ready: isReady })
            }).catch(err => console.error('Gagal sinkron status ready:', err));
        }

        function leaveRoom() {
            showHTMLConfirm('Apakah Anda yakin ingin keluar dari room?').then(confirmed => {
                if (confirmed) {
                    fetch('/room/leave', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ room_id: roomId })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success && data.redirect_url) {
                                window.location.href = data.redirect_url;
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            window.location.href = '/room';
                        });
                }
            });
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            initWebSocket();
        });

        // Floating particles background effect
        (function () {
            const canvas = document.getElementById('ps5-particles');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            let width = canvas.width = canvas.offsetWidth;
            let height = canvas.height = canvas.offsetHeight;

            const particles = [];
            const particleCount = 25;

            for (let i = 0; i < particleCount; i++) {
                particles.push({
                    x: Math.random() * width,
                    y: Math.random() * height + height,
                    size: Math.random() * 3 + 1,
                    speed: Math.random() * 0.4 + 0.15,
                    opacity: Math.random() * 0.4 + 0.2
                });
            }

            function animate() {
                ctx.clearRect(0, 0, width, height);
                ctx.fillStyle = '#ffffff';

                particles.forEach(p => {
                    ctx.globalAlpha = p.opacity;
                    ctx.fillRect(p.x, p.y, p.size, p.size);
                    p.y -= p.speed;
                    if (p.y < -10) {
                        p.y = height + 10;
                        p.x = Math.random() * width;
                    }
                });

                requestAnimationFrame(animate);
            }

            window.addEventListener('resize', () => {
                if (canvas.offsetWidth) {
                    width = canvas.width = canvas.offsetWidth;
                    height = canvas.height = canvas.offsetHeight;
                }
            });

            animate();
        })();
    </script>
</body>

</html>