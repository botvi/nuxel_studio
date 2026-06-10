<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title>Custom Room — Papan Jawara</title>
    <link rel="stylesheet" href="/game_pacu/assets/css/game-layout.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Pixelify+Sans:wght@400;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #060d18;
            color: #e2e8f0;
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

        .bg-slide-4 {
            background: radial-gradient(circle at 50% 60%, rgba(168, 85, 247, 0.5) 0%, rgba(15, 23, 42, 0.3) 50%, rgba(15, 5, 20, 0.85) 100%);
        }

        .back-btn {
            position: absolute;
            top: 16px;
            left: 14px;
            width: 36px;
            height: 36px;
            background: none;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 15;
            transition: all 0.15s ease;
            box-sizing: border-box;
        }

        .back-btn img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            image-rendering: pixelated;
        }

        .back-btn:hover {
            transform: scale(1.05);
        }

        .back-btn:active {
            transform: scale(0.9);
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
            background: rgba(10, 18, 36, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            width: 88%;
            padding: 22px 20px;
            margin-bottom: 20px;
            box-shadow:
                0 12px 40px rgba(0, 0, 0, 0.6),
                0 0 0 1px rgba(255, 255, 255, 0.04),
                0 0 20px rgba(168, 85, 247, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.06);
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            z-index: 11;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            position: relative;
            overflow: hidden;
        }

        .panel::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: repeating-linear-gradient(
                0deg, transparent, transparent 3px,
                rgba(0,0,0,0.025) 3px, rgba(0,0,0,0.025) 4px
            );
            pointer-events: none;
            z-index: 0;
            border-radius: 20px;
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
            text-shadow: 0 2px 4px rgba(0,0,0,0.6);
        }

        /* Daftar Room Scrollable */
        .room-list-container {
            flex: 1;
            max-height: 230px;
            overflow-y: auto;
            padding-right: 6px;
            scrollbar-width: thin;
            scrollbar-color: #a855f7 rgba(15, 23, 42, 0.3);
        }

        .room-list-container::-webkit-scrollbar {
            width: 6px;
        }

        .room-list-container::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.3);
            border-radius: 4px;
        }

        .room-list-container::-webkit-scrollbar-thumb {
            background-color: #a855f7;
            border-radius: 4px;
        }

        .room-item {
            background: rgba(255, 255, 255, 0.04);
            padding: 14px;
            margin-bottom: 10px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.18s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 4px 10px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.04);
            position: relative;
            z-index: 1;
        }

        .room-item:hover {
            background: rgba(168, 85, 247, 0.08);
            border-color: rgba(168, 85, 247, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.35), 0 0 12px rgba(168, 85, 247, 0.12);
        }

        .room-info {
            display: flex;
            flex-direction: column;
            width: 65%;
        }

        .room-name {
            font-family: 'Pixelify Sans', monospace;
            font-weight: 700;
            font-size: 16px;
            color: #ffffff;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-shadow: 0 2px 4px rgba(0,0,0,0.6);
        }

        .room-status {
            font-size: 11px;
            color: #22c55e;
            font-weight: bold;
        }

        .room-status.private {
            color: #ef4444;
        }

        /* Buttons */
        .pixel-btn {
            background: linear-gradient(180deg, #22c55e 0%, #16a34a 100%);
            border: 2px solid #15803d;
            border-radius: 10px;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.35),
                0 5px 0 #14532d,
                0 6px 14px rgba(34, 197, 94, 0.35);
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
            text-shadow: 0 1px 2px rgba(0,0,0,0.5);
            transition: all 0.12s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .pixel-btn:hover {
            background: linear-gradient(180deg, #4ade80 0%, #22c55e 100%);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.4),
                0 5px 0 #14532d,
                0 8px 20px rgba(34, 197, 94, 0.45);
            transform: translateY(-1px);
        }

        .pixel-btn:active {
            transform: translateY(4px);
            box-shadow:
                inset 0 1px 0 rgba(0,0,0,0.1),
                0 1px 0 #14532d;
        }

        .btn-small {
            background: linear-gradient(180deg, #22c55e 0%, #16a34a 100%);
            border: 2px solid #15803d;
            border-radius: 8px;
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,0.35),
                0 4px 0 #14532d,
                0 5px 12px rgba(34, 197, 94, 0.3);
            color: white;
            font-family: 'Press Start 2P', monospace;
            font-size: 8px;
            padding: 10px 14px;
            cursor: pointer;
            text-align: center;
            text-shadow: 0 1px 2px rgba(0,0,0,0.5);
            transition: all 0.12s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .btn-small:hover {
            background: linear-gradient(180deg, #4ade80 0%, #22c55e 100%);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,0.4),
                0 4px 0 #14532d,
                0 6px 16px rgba(34, 197, 94, 0.4);
            transform: translateY(-1px);
        }

        .btn-small:active {
            transform: translateY(3px);
            box-shadow:
                inset 0 1px 0 rgba(0,0,0,0.1),
                0 1px 0 #14532d;
        }

        .btn-orange {
            background: linear-gradient(180deg, #f59e0b 0%, #d97706 100%);
            border-color: #92400e;
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,0.3),
                0 4px 0 #78350f,
                0 5px 12px rgba(245, 158, 11, 0.3);
        }

        .btn-orange:hover {
            background: linear-gradient(180deg, #fbbf24 0%, #f59e0b 100%);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,0.35),
                0 4px 0 #78350f,
                0 6px 16px rgba(245, 158, 11, 0.4);
            transform: translateY(-1px);
        }

        .btn-orange:active {
            transform: translateY(3px);
            box-shadow: 0 1px 0 #78350f;
        }

        .btn-red {
            background: linear-gradient(180deg, #ef4444 0%, #dc2626 100%);
            border-color: #991b1b;
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,0.3),
                0 4px 0 #7f1d1d,
                0 5px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-red:hover {
            background: linear-gradient(180deg, #f87171 0%, #ef4444 100%);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,0.35),
                0 4px 0 #7f1d1d,
                0 6px 16px rgba(239, 68, 68, 0.4);
            transform: translateY(-1px);
        }

        .btn-red:active {
            transform: translateY(4px);
            box-shadow: 0 1px 0 #7f1d1d;
        }

        /* Form Buat Room */
        .input-group {
            margin-bottom: 16px;
        }

        .input-label {
            font-family: 'Press Start 2P', monospace;
            font-size: 8px;
            color: #ffaa00;
            margin-bottom: 8px;
            display: block;
            letter-spacing: 0.5px;
            text-shadow: 0 1.5px 2px rgba(0,0,0,0.6);
        }

        .pixel-input {
            width: 100%;
            background: rgba(15, 23, 42, 0.6);
            border: 2px solid rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            color: #ffffff;
            font-family: 'Pixelify Sans', monospace;
            font-size: 15px;
            font-weight: bold;
            padding: 12px 14px;
            box-sizing: border-box;
            outline: none;
            transition: all 0.2s;
        }

        .pixel-input:focus {
            border-color: #a855f7;
            background: rgba(15, 23, 42, 0.85);
            box-shadow: 0 0 12px rgba(168, 85, 247, 0.4);
        }

        .pixel-input::placeholder {
            color: rgba(255, 255, 255, 0.3);
            font-weight: normal;
        }

        /* Modal Style for Password */
        #password-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(8px);
            z-index: 50;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: rgba(15, 23, 42, 0.95);
            border: 3px solid #a855f7;
            padding: 24px 20px;
            width: 85%;
            max-width: 320px;
            border-radius: 20px;
            box-sizing: border-box;
            box-shadow: 
                0 10px 30px rgba(0, 0, 0, 0.6),
                0 0 20px rgba(168, 85, 247, 0.3);
            animation: modalFadeIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        @keyframes modalFadeIn {
            from {
                transform: scale(0.9);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
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
                    <div id="ps5-backdrop" class="ps5-backdrop-glow bg-slide-4"></div>
                    <canvas id="ps5-particles"
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none; opacity: 0.5;"></canvas>

                    <div class="back-btn" onclick="window.location.href='/room'">
                        <img src="/game_pacu/assets/image/ui/back.png" alt="Back">
                    </div>

                    <div class="title-banner">✦ CUSTOM ROOM ✦</div>

                    <!-- Panel Buat Room -->
                    <div class="panel">
                        <div class="panel-title">✦ BUAT ROOM BARU ✦</div>
                        <div class="input-group">
                            <label class="input-label">Nama Room</label>
                            <input type="text" id="room-name-input" class="pixel-input" placeholder="Masukkan nama room...">
                        </div>
                        <div class="input-group">
                            <label class="input-label">Password (Opsional)</label>
                            <input type="password" id="room-password-input" class="pixel-input" placeholder="Kosongkan jika publik...">
                        </div>
                        <button class="pixel-btn" style="margin-top: 5px;"
                            onclick="createRoom()">BUAT & MASUK</button>
                    </div>

                    <!-- Panel Room Tersedia -->
                    <div class="panel" style="flex: 1; max-height: 45%;">
                        <div class="panel-title">✦ ROOM TERSEDIA ✦</div>

                        <!-- Cari Kode Room -->
                        <div style="display:flex; gap:8px; margin-bottom:12px; position:relative; z-index:1;">
                            <input
                                type="text"
                                id="room-code-input"
                                class="pixel-input"
                                placeholder="Masukkan kode room..."
                                maxlength="6"
                                style="flex:1; font-size:14px; letter-spacing:3px; text-transform:uppercase; padding:10px 12px;"
                                oninput="this.value=this.value.toUpperCase()"
                                onkeydown="if(event.key==='Enter') joinByCode()"
                            >
                            <button class="btn-small btn-orange" onclick="joinByCode()" style="white-space:nowrap; padding:10px 14px;">MASUK</button>
                        </div>

                        <div class="room-list-container scrollable" id="room-list-container">
                            <div style="text-align: center; color: #ffaa00; font-size: 8px; padding: 20px; font-family: 'Press Start 2P', monospace; text-shadow: 0 1.5px 2px rgba(0,0,0,0.6);">MENCARI ROOM...</div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Password Modal -->
            <div id="password-modal">
                <div class="modal-content">
                    <div class="panel-title" id="modal-room-name">✦ MASUK ROOM ✦</div>
                    <div class="input-group" style="margin-top: 20px;">
                        <label class="input-label">Masukkan Password</label>
                        <input type="password" class="pixel-input" id="join-password" placeholder="Password...">
                    </div>
                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button class="pixel-btn btn-red" style="margin-top: 0;"
                            onclick="hidePasswordModal()">BATAL</button>
                        <button class="pixel-btn" style="margin-top: 0;" onclick="submitPassword()">MASUK</button>
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

        let selectedRoomId = null;

        function showPasswordModal(roomId, roomName) {
            selectedRoomId = roomId;
            document.getElementById('modal-room-name').innerText = '✦ ROOM: ' + roomName.toUpperCase() + ' ✦';
            document.getElementById('join-password').value = '';
            document.getElementById('password-modal').style.display = 'flex';
        }

        function hidePasswordModal() {
            document.getElementById('password-modal').style.display = 'none';
            selectedRoomId = null;
        }

        function createRoom() {
            const name = document.getElementById('room-name-input').value.trim();
            const password = document.getElementById('room-password-input').value;

            if (!name) {
                showHTMLAlert('Silakan masukkan nama room!');
                return;
            }

            fetch('/room/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ name, password })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    showHTMLAlert(data.message || 'Gagal membuat room.');
                }
            })
            .catch(err => {
                console.error(err);
                showHTMLAlert('Terjadi kesalahan koneksi.');
            });
        }

        function joinByCode() {
            const code = document.getElementById('room-code-input').value.trim().toUpperCase();
            if (!code || code.length < 4) {
                showHTMLAlert('Masukkan kode room yang valid!');
                return;
            }

            fetch('/room/join-by-code', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ room_code: code })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else if (data.requires_password) {
                    showPasswordModal(data.room_id, data.room_name);
                } else {
                    showHTMLAlert(data.message || 'Kode room tidak ditemukan.');
                }
            })
            .catch(err => {
                console.error(err);
                showHTMLAlert('Terjadi kesalahan koneksi.');
            });
        }

        function joinRoom(roomId, password = null) {
            fetch('/room/join', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ room_id: roomId, password: password })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    showHTMLAlert(data.message || 'Gagal masuk ke room.');
                }
            })
            .catch(err => {
                console.error(err);
                showHTMLAlert('Terjadi kesalahan koneksi.');
            });
        }

        function submitPassword() {
            const pwd = document.getElementById('join-password').value;
            if (pwd.trim() === '') {
                showHTMLAlert('Silakan masukkan password!');
                return;
            }
            if (selectedRoomId) {
                joinRoom(selectedRoomId, pwd);
                hidePasswordModal();
            }
        }

        function fetchRooms() {
            fetch('/room/list')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('room-list-container');
                container.innerHTML = '';
                
                if (!data.rooms || data.rooms.length === 0) {
                    container.innerHTML = '<div style="text-align: center; color: #ffaa00; font-size: 8px; padding: 20px; font-family: \'Press Start 2P\', monospace; text-shadow: 0 1.5px 2px rgba(0,0,0,0.6);">BELUM ADA ROOM</div>';
                    return;
                }

                data.rooms.forEach(room => {
                    const item = document.createElement('div');
                    item.className = 'room-item';
                    
                    const info = document.createElement('div');
                    info.className = 'room-info';
                    
                    const nameSpan = document.createElement('span');
                    nameSpan.className = 'room-name';
                    nameSpan.innerText = room.name;
                    
                    const statusSpan = document.createElement('span');
                    statusSpan.className = 'room-status' + (room.is_private ? ' private' : '');
                    statusSpan.innerText = (room.is_private ? 'Privat' : 'Publik') + ' (Host: ' + room.host_name + ')';
                    
                    info.appendChild(nameSpan);
                    info.appendChild(statusSpan);
                    
                    const button = document.createElement('button');
                    if (room.is_private) {
                        button.className = 'btn-small btn-orange';
                        button.innerText = 'PASSWORD';
                        button.onclick = () => showPasswordModal(room.id, room.name);
                    } else {
                        button.className = 'btn-small';
                        button.innerText = 'JOIN';
                        button.onclick = () => joinRoom(room.id);
                    }
                    
                    item.appendChild(info);
                    item.appendChild(button);
                    
                    container.appendChild(item);
                });
            })
            .catch(err => console.error('Gagal mengambil daftar room:', err));
        }

        // Poll rooms every 4 seconds
        setInterval(fetchRooms, 4000);
        document.addEventListener('DOMContentLoaded', fetchRooms);

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
    <script src="/game_pacu/assets/js/game-layout.js?v=<?= time() ?>"></script>
</body>

</html>