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

        .back-btn {
            position: absolute;
            top: 20px;
            left: 18px;
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.9);
            border: 3px solid #22c55e;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 15;
            transition: all 0.15s ease;
            box-shadow: 3px 3px 0px #15803d;
            box-sizing: border-box;
        }

        .back-btn img {
            width: 28px;
            height: 28px;
            image-rendering: pixelated;
        }

        .back-btn:hover {
            background: #ffffff;
            transform: translate(-2px, -2px);
            box-shadow: 5px 5px 0px #15803d;
        }

        .back-btn:active {
            transform: translate(2px, 2px);
            box-shadow: 1px 1px 0px #15803d;
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
            width: 88%;
            padding: 22px 20px;
            margin-bottom: 20px;
            box-shadow: 
                0 8px 20px rgba(21, 128, 61, 0.2),
                6px 6px 0px #15803d;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
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
            text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.05);
        }

        /* Daftar Room Scrollable */
        .room-list-container {
            flex: 1;
            max-height: 230px;
            overflow-y: auto;
            padding-right: 6px;
            scrollbar-width: thin;
            scrollbar-color: #22c55e #f0fdf4;
        }

        .room-list-container::-webkit-scrollbar {
            width: 6px;
        }

        .room-list-container::-webkit-scrollbar-track {
            background: #f0fdf4;
            border-radius: 4px;
        }

        .room-list-container::-webkit-scrollbar-thumb {
            background-color: #22c55e;
            border-radius: 4px;
        }

        .room-item {
            background: #ffffff;
            padding: 14px;
            margin-bottom: 12px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 3px solid #86efac;
            transition: all 0.15s ease;
            box-shadow: 3px 3px 0px rgba(21, 128, 61, 0.15);
        }

        .room-item:hover {
            background: #f0fdf4;
            border-color: #22c55e;
            transform: translateY(-2px);
            box-shadow: 5px 5px 0px rgba(21, 128, 61, 0.2);
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
            color: #15803d;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .room-status {
            font-size: 12px;
            color: #16a34a;
            font-weight: bold;
        }

        .room-status.private {
            color: #dc2626;
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

        .btn-small {
            background-color: #22c55e;
            border: 2px solid #15803d;
            border-radius: 8px;
            box-shadow: 0px 3px 0px #15803d;
            color: white;
            font-family: 'Press Start 2P', monospace;
            font-size: 8px;
            padding: 10px 14px;
            cursor: pointer;
            text-align: center;
            text-shadow: 1px 1px 0px #15803d;
            transition: all 0.15s ease;
        }

        .btn-small:hover {
            background-color: #4ade80;
            transform: translateY(-1px);
            box-shadow: 0px 4px 0px #15803d;
        }

        .btn-small:active {
            transform: translateY(3px);
            box-shadow: 0px 0px 0px #15803d;
        }

        .btn-orange {
            background-color: #f59e0b;
            border-color: #b45309;
            box-shadow: 0px 3px 0px #b45309;
            text-shadow: 1px 1px 0px #78350f;
        }

        .btn-orange:hover {
            background-color: #fbbf24;
            box-shadow: 0px 4px 0px #b45309;
        }

        .btn-orange:active {
            transform: translateY(3px);
            box-shadow: 0px 0px 0px #b45309;
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

        /* Form Buat Room */
        .input-group {
            margin-bottom: 16px;
        }

        .input-label {
            font-family: 'Press Start 2P', monospace;
            font-size: 8px;
            color: #15803d;
            margin-bottom: 8px;
            display: block;
            letter-spacing: 0.5px;
        }

        .pixel-input {
            width: 100%;
            background: #f0fdf4;
            border: 3px solid #86efac;
            border-radius: 8px;
            color: #15803d;
            font-family: 'Pixelify Sans', monospace;
            font-size: 15px;
            font-weight: bold;
            padding: 12px 14px;
            box-sizing: border-box;
            outline: none;
            transition: all 0.2s;
        }

        .pixel-input:focus {
            border-color: #22c55e;
            background: #ffffff;
            box-shadow: 0 0 10px rgba(34, 197, 94, 0.15);
        }

        .pixel-input::placeholder {
            color: #86efac;
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
            background: rgba(6, 78, 59, 0.85);
            backdrop-filter: blur(8px);
            z-index: 50;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: #ffffff;
            border: 4px solid #22c55e;
            padding: 24px 20px;
            width: 85%;
            max-width: 320px;
            border-radius: 20px;
            box-sizing: border-box;
            box-shadow: 
                0 10px 30px rgba(0, 0, 0, 0.4),
                6px 6px 0px #15803d;
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

                        <div class="room-list-container scrollable" id="room-list-container">
                            <div style="text-align: center; color: #16a34a; font-size: 12px; padding: 20px; font-family: 'Press Start 2P', monospace;">Mencari room...</div>
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
                    container.innerHTML = '<div style="text-align: center; color: #16a34a; font-size: 11px; padding: 20px; font-family: \'Press Start 2P\', monospace;">BELUM ADA ROOM</div>';
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
    </script>
    <script src="/game_pacu/assets/js/game-layout.js?v=<?= time() ?>"></script>
</body>

</html>