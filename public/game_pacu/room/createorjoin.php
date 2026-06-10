<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Custom Room — Papan Jawara</title>
    <link rel="stylesheet" href="../assets/css/game-layout.css">
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
            background: url('../assets/image/bg/bgmenu.jpg') no-repeat center center;
            background-size: cover;
            z-index: 10;
            padding-bottom: 20px;
            box-sizing: border-box;
        }

        .back-btn {
            position: absolute;
            top: 10px;
            left: 14px;
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.4);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 15;
            transition: all 0.15s ease;
            box-sizing: border-box;
        }

        .back-btn img {
            width: 32px;
            height: 32px;
            image-rendering: pixelated;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.35);
            border-color: rgba(255, 255, 255, 0.7);
            transform: scale(1.05);
        }

        .back-btn:active {
            transform: scale(0.9);
        }

        .title-banner {
            font-family: 'Pixelify Sans', monospace;
            font-weight: 700;
            font-size: 28px;
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
            background-color: #ffffff;
            border: 3px solid #86efac;
            border-radius: 16px;
            width: 85%;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 4px 4px 0px rgba(21, 128, 61, 0.3);
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }

        .panel-title {
            font-family: 'Press Start 2P', monospace;
            font-size: 10px;
            color: #22c55e;
            -webkit-text-stroke: 1px #ffffff;
            margin-bottom: 15px;
            border-bottom: 2px dashed #bbf7d0;
            padding-bottom: 10px;
            text-align: center;
            line-height: 1.5;
            text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.1);
        }

        /* Daftar Room Scrollable */
        .room-list-container {
            flex: 1;
            /* take remaining height */
            max-height: 230px;
            overflow-y: auto;
            padding-right: 6px;
            /* Scrollbar styling */
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
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 2px solid #86efac;
            transition: all 0.1s;
        }

        .room-item:hover {
            background: #f0fdf4;
            border-color: #22c55e;
            transform: translateX(2px);
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
            border: 2px solid #16a34a;
            border-radius: 8px;
            box-shadow: 0px 4px 0px #16a34a;
            color: white;
            font-family: 'Press Start 2P', monospace;
            font-size: 10px;
            padding: 15px;
            width: 100%;
            text-align: center;
            cursor: pointer;
            text-transform: uppercase;
            box-sizing: border-box;
            display: block;
            margin-top: 10px;
            text-shadow: 1px 1px 0px #15803d;
            transition: all 0.1s;
        }

        .pixel-btn:hover {
            background-color: #4ade80;
        }

        .pixel-btn:active {
            transform: translateY(4px);
            box-shadow: 0px 0px 0px #16a34a;
        }

        .btn-small {
            background-color: #22c55e;
            border: 2px solid #16a34a;
            border-radius: 6px;
            box-shadow: 0px 3px 0px #16a34a;
            color: white;
            font-family: 'Press Start 2P', monospace;
            font-size: 8px;
            padding: 8px 12px;
            cursor: pointer;
            text-align: center;
            text-shadow: 1px 1px 0px #15803d;
            transition: all 0.1s;
        }

        .btn-small:hover {
            background-color: #4ade80;
        }

        .btn-small:active {
            transform: translateY(3px);
            box-shadow: 0px 0px 0px #16a34a;
        }

        .btn-orange {
            background-color: #f59e0b;
            border-color: #b45309;
            box-shadow: 0px 3px 0px #b45309;
            text-shadow: 1px 1px 0px #78350f;
        }

        .btn-orange:hover {
            background-color: #fbbf24;
        }

        .btn-orange:active {
            transform: translateY(3px);
            box-shadow: 0px 0px 0px #b45309;
        }

        .btn-red {
            background-color: #ef4444;
            border-color: #b91c1c;
            box-shadow: 0px 4px 0px #b91c1c;
            text-shadow: 1px 1px 0px #7f1d1d;
        }

        .btn-red:hover {
            background-color: #f87171;
        }

        .btn-red:active {
            transform: translateY(4px);
            box-shadow: 0px 0px 0px #b91c1c;
        }

        /* Form Buat Room */
        .input-group {
            margin-bottom: 12px;
        }

        .input-label {
            font-size: 14px;
            font-weight: bold;
            color: #15803d;
            margin-bottom: 6px;
            display: block;
        }

        .pixel-input {
            width: 100%;
            background: #f0fdf4;
            border: 2px solid #86efac;
            border-radius: 6px;
            color: #15803d;
            font-family: 'Pixelify Sans', monospace;
            font-size: 16px;
            font-weight: bold;
            padding: 10px;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.2s, background 0.2s;
        }

        .pixel-input:focus {
            border-color: #22c55e;
            background: #ffffff;
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
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(3px);
            z-index: 50;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: #ffffff;
            border: 4px solid #22c55e;
            padding: 20px;
            width: 85%;
            border-radius: 16px;
            box-sizing: border-box;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.5);
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
                    <div class="back-btn" onclick="window.location.href='index.php'">
                        <img src="../assets/image/ui/back.png" alt="Back">
                    </div>

                    <div class="title-banner">✦ CUSTOM ROOM ✦</div>

                    <!-- Panel Buat Room -->
                    <div class="panel">
                        <div class="panel-title">✦ BUAT ROOM BARU ✦</div>
                        <div class="input-group">
                            <label class="input-label">Nama Room</label>
                            <input type="text" class="pixel-input" placeholder="Masukkan nama room...">
                        </div>
                        <div class="input-group">
                            <label class="input-label">Password (Opsional)</label>
                            <input type="password" class="pixel-input" placeholder="Kosongkan jika publik...">
                        </div>
                        <button class="pixel-btn" style="margin-top: 5px;"
                            onclick="alert('Room berhasil dibuat! (Dummy)')">BUAT & MASUK</button>
                    </div>

                    <!-- Panel Room Tersedia -->
                    <div class="panel" style="flex: 1; max-height: 45%;">
                        <div class="panel-title">✦ ROOM TERSEDIA ✦</div>

                        <div class="room-list-container">
                            <!-- Dummy Room 1 -->
                            <div class="room-item">
                                <div class="room-info">
                                    <span class="room-name">Mabar Seru 1</span>
                                    <span class="room-status">Publik</span>
                                </div>
                                <button class="btn-small" onclick="alert('Berhasil Masuk Room! (Dummy)')">JOIN</button>
                            </div>

                            <!-- Dummy Room 2 -->
                            <div class="room-item">
                                <div class="room-info">
                                    <span class="room-name">Pro Only 99</span>
                                    <span class="room-status private">Privat</span>
                                </div>
                                <button class="btn-small btn-orange"
                                    onclick="showPasswordModal('Pro Only 99')">PASSWORD</button>
                            </div>

                            <!-- Dummy Room 3 -->
                            <div class="room-item">
                                <div class="room-info">
                                    <span class="room-name">Santai Aja</span>
                                    <span class="room-status">Publik</span>
                                </div>
                                <button class="btn-small" onclick="alert('Berhasil Masuk Room! (Dummy)')">JOIN</button>
                            </div>

                            <!-- Dummy Room 4 -->
                            <div class="room-item">
                                <div class="room-info">
                                    <span class="room-name">Turnamen 1</span>
                                    <span class="room-status private">Privat</span>
                                </div>
                                <button class="btn-small btn-orange"
                                    onclick="showPasswordModal('Turnamen 1')">PASSWORD</button>
                            </div>

                            <!-- Dummy Room 5 -->
                            <div class="room-item">
                                <div class="room-info">
                                    <span class="room-name">Adu Cepat</span>
                                    <span class="room-status">Publik</span>
                                </div>
                                <button class="btn-small" onclick="alert('Berhasil Masuk Room! (Dummy)')">JOIN</button>
                            </div>

                            <!-- Dummy Room 6 -->
                            <div class="room-item">
                                <div class="room-info">
                                    <span class="room-name">Tantangan VVIP</span>
                                    <span class="room-status private">Privat</span>
                                </div>
                                <button class="btn-small btn-orange"
                                    onclick="showPasswordModal('Tantangan VVIP')">PASSWORD</button>
                            </div>
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

    <script src="../assets/js/game-layout.js?v=<?= time() ?>"></script>
    <script>
        function showPasswordModal(roomName) {
            document.getElementById('modal-room-name').innerText = '✦ ROOM: ' + roomName.toUpperCase() + ' ✦';
            document.getElementById('join-password').value = '';
            document.getElementById('password-modal').style.display = 'flex';
        }

        function hidePasswordModal() {
            document.getElementById('password-modal').style.display = 'none';
        }

        function submitPassword() {
            const pwd = document.getElementById('join-password').value;
            if (pwd.trim() === '') {
                alert('Silakan masukkan password!');
            } else {
                alert('Memeriksa password... (Dummy)');
                hidePasswordModal();
            }
        }
    </script>
</body>

</html>