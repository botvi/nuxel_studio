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
        }

        .title-banner {
            font-family: 'Pixelify Sans', monospace;
            font-weight: 700;
            font-size: 26px;
            color: #ffffff;
            text-shadow:
                2px 2px 0px #16a34a,
                -2px -2px 0px #16a34a,
                2px -2px 0px #16a34a,
                -2px 2px 0px #16a34a,
                4px 4px 0px rgba(0, 0, 0, 0.4);
            margin-top: 80px;
            margin-bottom: 20px;
            text-align: center;
            line-height: 1.4;
            letter-spacing: 1px;
        }

        .panel {
            background: rgba(255, 255, 255, 0.92);
            border: 4px solid #22c55e;
            border-radius: 20px;
            width: 90%;
            padding: 22px 20px;
            margin-bottom: 15px;
            box-shadow:
                0 8px 20px rgba(21, 128, 61, 0.2),
                6px 6px 0px #15803d;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
            backdrop-filter: blur(10px);
        }

        .panel-title {
            font-family: 'Press Start 2P', monospace;
            font-size: 10px;
            color: #15803d;
            margin-bottom: 15px;
            border-bottom: 2px dashed #bbf7d0;
            padding-bottom: 12px;
            text-align: center;
            line-height: 1.5;
            text-shadow: 1px 1px 0px #dcfce7;
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
            background: #ffffff;
            border: 3px solid #86efac;
            border-radius: 16px;
            padding: 16px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 170px;
            box-sizing: border-box;
            position: relative;
            box-shadow: 4px 4px 0px rgba(21, 128, 61, 0.1);
            transition: all 0.2s;
        }

        .player-card.empty {
            border-style: dashed;
            border-color: #86efac;
            background: rgba(255, 255, 255, 0.4);
            justify-content: center;
            box-shadow: none;
        }

        .player-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #ffffff;
            border: 3px solid #22c55e;
            margin-bottom: 12px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(21, 128, 61, 0.15);
        }

        .player-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .player-name {
            font-size: 14px;
            font-weight: 700;
            color: #15803d;
            text-align: center;
            margin-bottom: 6px;
            max-width: 100px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .player-role {
            font-family: 'Press Start 2P', monospace;
            font-size: 7px;
            background: #22c55e;
            color: #ffffff;
            padding: 3px 8px;
            border-radius: 6px;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .ready-badge {
            font-family: 'Press Start 2P', monospace;
            font-size: 7px;
            padding: 6px 10px;
            border-radius: 8px;
            color: white;
            text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.2);
            text-align: center;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }

        .ready-badge.ready {
            background-color: #22c55e;
            box-shadow: 0 3px 0 #15803d;
        }

        .ready-badge.not-ready {
            background-color: #ef4444;
            box-shadow: 0 3px 0 #991b1b;
        }

        /* Radar Search animation for empty slot */
        .searching-radar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 3px solid rgba(34, 197, 94, 0.4);
            margin-bottom: 10px;
            position: relative;
            animation: radarPulse 1.5s infinite ease-in-out;
        }

        @keyframes radarPulse {
            0% {
                transform: scale(0.9);
                opacity: 0.5;
            }

            50% {
                transform: scale(1.1);
                opacity: 1;
            }

            100% {
                transform: scale(0.9);
                opacity: 0.5;
            }
        }

        /* Buttons */
        .pixel-btn {
            background-color: #22c55e;
            border: 3px solid #15803d;
            border-radius: 10px;
            box-shadow: 0px 5px 0px #15803d;
            color: white;
            font-family: 'Press Start 2P', monospace;
            font-size: 10px;
            padding: 16px 10px;
            width: 100%;
            text-align: center;
            cursor: pointer;
            text-transform: uppercase;
            box-sizing: border-box;
            display: block;
            margin-top: 10px;
            text-shadow: 1px 1px 0px #15803d;
            transition: all 0.15s ease;
        }

        .pixel-btn:hover {
            background-color: #4ade80;
            transform: translateY(-2px);
            box-shadow: 0px 7px 0px #15803d;
        }

        .pixel-btn:active {
            transform: translateY(5px);
            box-shadow: 0px 0px 0px #15803d;
        }

        .btn-red {
            background-color: #ef4444;
            border-color: #991b1b;
            box-shadow: 0px 5px 0px #991b1b;
            text-shadow: 1px 1px 0px #7f1d1d;
        }

        .btn-red:hover {
            background-color: #f87171;
            box-shadow: 0px 7px 0px #991b1b;
        }

        .btn-red:active {
            transform: translateY(5px);
            box-shadow: 0px 0px 0px #991b1b;
        }

        .connection-status {
            font-family: 'Press Start 2P', monospace;
            font-size: 7px;
            margin-top: 12px;
            color: #dc2626;
            letter-spacing: 0.5px;
        }

        .connection-status.connected {
            color: #16a34a;
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
                                <div style="font-size: 11px; font-weight: bold; color: #16a34a;">MENUNGGU LAWAN...</div>
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
                    <div style="font-size: 11px; font-weight: bold; color: #16a34a;">MENUNGGU LAWAN...</div>
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
    </script>
</body>

</html>