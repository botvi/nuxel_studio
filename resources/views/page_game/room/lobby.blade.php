@extends('layouts.game')

@section('title', 'Room Lobby — Papan Jawara')

@push('styles')
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

    .bg-slide-1 {
        background: rgba(20, 5, 5, 0.9);
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
        width: 90%;
        padding: 22px 20px;
        margin-bottom: 15px;
        box-shadow:
            0 12px 40px rgba(0, 0, 0, 0.6),
            0 0 0 1px rgba(255, 255, 255, 0.04),
            inset 0 1px 0 rgba(255, 255, 255, 0.06);
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        align-items: center;
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
        margin-bottom: 10px;
        border-bottom: 2px dashed rgba(255, 255, 255, 0.15);
        padding-bottom: 12px;
        text-align: center;
        line-height: 1.5;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.6);
        width: 100%;
    }

    /* Room Code Badge */
    .room-code-box {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: rgba(251, 191, 36, 0.08);
        border: 1.5px dashed rgba(251, 191, 36, 0.45);
        border-radius: 12px;
        padding: 10px 16px;
        margin-bottom: 16px;
        width: 100%;
        box-sizing: border-box;
    }
    .room-code-label {
        font-family: 'Press Start 2P', monospace;
        font-size: 6px;
        color: rgba(251,191,36,0.7);
        letter-spacing: 0.5px;
    }
    .room-code-value {
        font-family: 'Press Start 2P', monospace;
        font-size: 13px;
        color: #fbbf24;
        letter-spacing: 3px;
        text-shadow: 2px 2px 0px #92400e;
    }
    .room-code-copy {
        background: rgba(251,191,36,0.15);
        border: 1px solid rgba(251,191,36,0.35);
        border-radius: 7px;
        padding: 5px 9px;
        font-family: 'Press Start 2P', monospace;
        font-size: 6px;
        color: #fbbf24;
        cursor: pointer;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .room-code-copy:hover {
        background: rgba(251,191,36,0.3);
        transform: translateY(-1px);
    }
    .room-code-copy:active {
        transform: translateY(1px);
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
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 16px 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 170px;
        box-sizing: border-box;
        position: relative;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255,255,255,0.05);
        transition: all 0.2s;
        z-index: 1;
    }

    .player-card.empty {
        border-style: dashed;
        border-color: rgba(255, 255, 255, 0.15);
        background: rgba(255, 255, 255, 0.01);
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
        background: rgba(239, 68, 68, 0.05);
    }

    /* Buttons */
    .pixel-btn {
        background: linear-gradient(180deg, #22c55e 0%, #16a34a 100%);
        border: 2px solid #15803d;
        border-radius: 10px;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,0.35),
            0 5px 0 #14532d;
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
            inset 0 1px 0 rgba(255,255,255,0.4),
            0 5px 0 #14532d;
        transform: translateY(-1px);
    }

    .pixel-btn:active {
        transform: translateY(4px);
        box-shadow: 0 1px 0 #14532d;
    }

    .btn-red {
        background: linear-gradient(180deg, #ef4444 0%, #dc2626 100%);
        border-color: #991b1b;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,0.3),
            0 5px 0 #7f1d1d;
    }

    .btn-red:hover {
        background: linear-gradient(180deg, #f87171 0%, #ef4444 100%);
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,0.35),
            0 5px 0 #7f1d1d;
        transform: translateY(-1px);
    }

    .btn-red:active {
        transform: translateY(4px);
        box-shadow: 0 1px 0 #7f1d1d;
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

    /* ========= GLOBAL CHAT SIDEBAR ========= */
    #chat-toggle-btn {
        position: absolute;
        bottom: 90px;
        left: 0;
        width: 34px;
        height: 34px;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border: 1.5px solid rgba(255,255,255,0.15);
        border-left: none;
        border-radius: 0 10px 10px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 50;
        transition: all 0.2s ease;
        box-shadow: 3px 0 12px rgba(0,0,0,0.4);
    }
    #chat-toggle-btn:hover { background: linear-gradient(135deg,#334155 0%,#1e293b 100%); width: 38px; }
    #chat-toggle-btn .chat-icon { font-size: 16px; }
    #chat-unread-dot {
        position: absolute; top: 4px; right: 4px;
        width: 8px; height: 8px;
        background: #ef4444; border-radius: 50%; display: none;
    }
    #chat-sidebar {
        position: absolute;
        top: 0; left: -260px;
        width: 260px; height: 100%;
        background: rgba(8,15,30,0.94);
        border-right: 1px solid rgba(255,255,255,0.1);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        z-index: 49;
        display: flex; flex-direction: column;
        box-shadow: 4px 0 24px rgba(0,0,0,0.6);
        transition: left 0.32s cubic-bezier(0.4,0,0.2,1);
        box-sizing: border-box;
    }
    #chat-sidebar.open { left: 0; }
    #chat-sidebar::before {
        content: ''; position: absolute; top:0; left:0; width:100%; height:100%;
        background: repeating-linear-gradient(0deg,transparent,transparent 3px,rgba(0,0,0,0.03) 3px,rgba(0,0,0,0.03) 4px);
        pointer-events: none; z-index: 0;
    }
    .chat-header {
        padding: 12px 14px 10px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        display: flex; align-items: center; justify-content: space-between;
        position: relative; z-index: 1; flex-shrink: 0;
    }
    .chat-header-title {
        font-family: 'Press Start 2P', monospace; font-size: 7px;
        color: #22c55e; letter-spacing: 0.5px;
    }
    .chat-online-badge { display:flex; align-items:center; gap:5px; font-size:10px; color:rgba(255,255,255,0.4); }
    .chat-online-dot {
        width:6px; height:6px; border-radius:50%; background:#22c55e;
    }
    .chat-share-btn {
        margin: 8px 12px 0;
        background: rgba(251,191,36,0.1);
        border: 1px dashed rgba(251,191,36,0.4);
        border-radius: 8px;
        padding: 7px 10px;
        font-family: 'Press Start 2P', monospace;
        font-size: 5.5px;
        color: #fbbf24;
        cursor: pointer;
        width: calc(100% - 24px);
        text-align: center;
        transition: all 0.15s ease;
        position: relative; z-index: 1;
        letter-spacing: 0.5px;
    }
    .chat-share-btn:hover { background: rgba(251,191,36,0.2); transform: translateY(-1px); }
    .chat-share-btn:active { transform: translateY(1px); }
    #chat-messages {
        flex: 1; overflow-y: auto; padding: 10px 12px;
        display: flex; flex-direction: column; gap: 8px;
        position: relative; z-index: 1;
        scrollbar-width: thin; scrollbar-color: rgba(34,197,94,0.3) rgba(255,255,255,0.02);
    }
    #chat-messages::-webkit-scrollbar { width: 3px; }
    #chat-messages::-webkit-scrollbar-thumb { background: rgba(34,197,94,0.3); border-radius: 3px; }
    .chat-msg { display:flex; flex-direction:column; gap:2px; animation: msgSlideIn 0.2s ease; }
    @keyframes msgSlideIn {
        from { opacity:0; transform:translateY(6px); }
        to   { opacity:1; transform:translateY(0); }
    }
    .chat-msg.is-me { align-items: flex-end; }
    .chat-msg-name {
        font-family: 'Press Start 2P', monospace; font-size: 5.5px;
        color: rgba(255,255,255,0.45); padding: 0 6px;
    }
    .chat-msg.is-me .chat-msg-name { color: rgba(34,197,94,0.7); }
    .chat-msg-bubble {
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 10px 10px 10px 2px;
        padding: 7px 10px; font-size: 12px; color: #e2e8f0;
        max-width: 86%; word-break: break-word; line-height: 1.4;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }
    .chat-msg.is-me .chat-msg-bubble {
        background: rgba(34,197,94,0.12); border-color: rgba(34,197,94,0.25);
        border-radius: 10px 10px 2px 10px; color: #d1fae5;
    }
    .chat-msg-bubble.room-share {
        background: rgba(251,191,36,0.08); border-color: rgba(251,191,36,0.3);
        color: #fef3c7;
    }
    .chat-msg-time { font-size: 9px; color: rgba(255,255,255,0.2); padding: 0 6px; }
    .chat-system-msg {
        text-align: center; font-family: 'Press Start 2P', monospace;
        font-size: 5.5px; color: rgba(255,255,255,0.25); padding: 4px 0;
    }
    .chat-input-area {
        padding: 10px 12px; border-top: 1px solid rgba(255,255,255,0.08);
        display: flex; gap: 7px; position: relative; z-index: 1; flex-shrink: 0;
    }
    #chat-input {
        flex: 1; background: rgba(255,255,255,0.05);
        border: 1.5px solid rgba(255,255,255,0.12); border-radius: 8px;
        padding: 8px 10px; font-family: 'Pixelify Sans', monospace;
        font-size: 13px; color: #fff; outline: none; transition: all 0.2s;
    }
    #chat-input:focus {
        border-color: rgba(34,197,94,0.5); background: rgba(255,255,255,0.08);
        box-shadow: none;
    }
    #chat-input::placeholder { color: rgba(255,255,255,0.2); }
    #chat-send-btn {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        border: none; border-radius: 8px; padding: 8px 10px;
        font-size: 14px; cursor: pointer; transition: all 0.15s ease;
        flex-shrink: 0;
    }
    #chat-send-btn:hover { transform: translateY(-1px); }
    #chat-send-btn:active { transform: translateY(1px); }
</style>
@endpush

@section('content')
<div id="game-ui">
    <div id="ps5-backdrop" class="ps5-backdrop-glow bg-slide-1"></div>
    <canvas id="ps5-particles"
        style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none; opacity: 0.5;"></canvas>

    <div class="title-banner">✦ ROOM LOBBY ✦</div>

    <!-- Panel Lobby -->
    <div class="panel">
        <div class="panel-title" id="lobby-room-name">✦ ROOM: {{ strtoupper($room->name) }} ✦</div>

        <!-- Room Code Display -->
        <div class="room-code-box">
            <span class="room-code-label">KODE:</span>
            <span class="room-code-value" id="room-code-display">{{ strtoupper($room->room_code) }}</span>
            <button class="room-code-copy" id="copy-code-btn" onclick="copyRoomCode()" title="Salin kode room">📋 SALIN</button>
        </div>

        <div class="players-container">
            <!-- Player 1 (Host) -->
            <div class="player-card" id="host-card">
                <div class="player-avatar">
                    @php
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
                    @endphp
                    <img src="{{ $hostImg }}" alt="Host">
                </div>
                <div class="player-name">{{ $room->host->nama_jalur ?? $room->host->email }}</div>
                <div class="player-role">HOST</div>
                <div class="ready-badge not-ready" id="host-ready-badge">BELUM READY</div>
            </div>

            <!-- Player 2 (Guest) -->
            <div class="player-card empty" id="guest-card">
                <div class="searching-radar"></div>
                <div style="font-family: 'Pixelify Sans', monospace; font-size: 10px; font-weight: bold; color: #fca5a5; text-shadow: 0 1px 2px rgba(0,0,0,0.5); text-align: center;">
                    MENUNGGU LAWAN...</div>
            </div>
        </div>

        <!-- Action Buttons -->
        <button class="pixel-btn" id="ready-btn" onclick="toggleReady()">READY UP</button>
        <button class="pixel-btn btn-red" onclick="leaveRoom()">KELUAR LOBBY</button>

        <div class="connection-status" id="ws-status">Menghubungkan ke server...</div>
    </div>

</div>

<!-- ===== GLOBAL CHAT SIDEBAR ===== -->
<div id="chat-toggle-btn" onclick="toggleChat()">
    <span class="chat-icon">💬</span>
    <span id="chat-unread-dot"></span>
</div>
<div id="chat-sidebar">
    <div class="chat-header">
        <div class="chat-header-title">💬 GLOBAL CHAT</div>
        <div class="chat-online-badge">
            <span class="chat-online-dot"></span>
            <span id="chat-online-count">0</span> online
        </div>
    </div>
    <!-- Share Room Button -->
    <button class="chat-share-btn" onclick="shareRoomToChat()">
        📎 BAGIKAN KODE ROOM INI KE CHAT
    </button>
    <div id="chat-messages">
        <div class="chat-system-msg">— Global Chat —</div>
    </div>
    <div class="chat-input-area">
        <input type="text" id="chat-input" placeholder="Ketik pesan..." maxlength="200" onkeydown="if(event.key==='Enter') sendChat()">
        <button id="chat-send-btn" onclick="sendChat()" title="Kirim">➤</button>
    </div>
</div>
<!-- ================================= -->
@endsection

@push('scripts')
<script>
{
    // Custom HTML Modals (Confirm & Alert) using existing game-layout.css styles
    window.showHTMLAlert = function(message, title = "✦ INFORMASI ✦") {
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
    };

    window.showHTMLConfirm = function(message, title = "✦ KONFIRMASI ✦") {
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
    };

    const roomId = "{{ $room->id }}";
    const currentUserId = {{ auth()->id() }};
    const currentUserName = "{{ addslashes(auth()->user()->nama_jalur ?? auth()->user()->email) }}";
    const currentUserPhoto = "{{ addslashes(auth()->user()->foto_profile ?? '') }}";

    let ws = null;
    let isReady = false;
    let hostId = {{ $room->host_id }};
    let guestId = {{ $room->guest_id ?? 'null' }};

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

    window.initWebSocket = function() {
        if (ws) return;

        const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        let wsUrl;
        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' || window.location.hostname.startsWith('192.168.')) {
            wsUrl = `${protocol}//${window.location.hostname}:8080`;
        } else {
            wsUrl = `${protocol}//${window.location.hostname}/ws`;
        }

        ws = new WebSocket(wsUrl);

        ws.onopen = () => {
            console.log('Connected to WebSocket server');
            const statusEl = document.getElementById('ws-status');
            if (statusEl) {
                statusEl.innerText = 'TERHUBUNG';
                statusEl.className = 'connection-status connected';
            }

            // Send Join Message
            if (ws) {
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
            }
        };

        ws.onmessage = (event) => {
            try {
                const message = JSON.parse(event.data);
                const { type, payload } = message;

                if (type === 'room_update') {
                    updateLobbyUI(payload.players);
                } else if (type === 'game_start') {
                    console.log('Game starting...');
                    window.navigateToPage(`/arena-pacu?room_id=room_${roomId}`);
                }
            } catch(e) {}
        };

        ws.onclose = () => {
            ws = null;
            // Only reconnect if still in the lobby page
            if (document.getElementById('lobby-room-name')) {
                console.log('Disconnected from WebSocket server. Reconnecting...');
                const statusEl = document.getElementById('ws-status');
                if (statusEl) {
                    statusEl.innerText = 'TERPUTUS. MENCOBA LAGI...';
                    statusEl.className = 'connection-status';
                }
                setTimeout(initWebSocket, 2000);
            }
        };

        ws.onerror = (err) => {
            console.error('WebSocket Error:', err);
        };
    };

    window.updateLobbyUI = function(players) {
        const guestCard = document.getElementById('guest-card');
        if (!guestCard) return;

        // Find host and guest from players list
        const hostPlayer = players.find(p => parseInt(p.userId) === hostId);
        const guestPlayer = players.find(p => parseInt(p.userId) !== hostId);

        // Host status update
        const hostBadge = document.getElementById('host-ready-badge');
        if (hostBadge) {
            if (hostPlayer && hostPlayer.ready) {
                hostBadge.innerText = 'READY';
                hostBadge.className = 'ready-badge ready';
            } else {
                hostBadge.innerText = 'BELUM READY';
                hostBadge.className = 'ready-badge not-ready';
            }
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
                <div class="player-name">${escapeHTML(guestPlayer.userName)}</div>
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
    };

    window.toggleReady = function() {
        isReady = !isReady;

        const btn = document.getElementById('ready-btn');
        if (btn) {
            if (isReady) {
                btn.innerText = 'BATAL READY';
                btn.className = 'pixel-btn btn-red';
            } else {
                btn.innerText = 'READY UP';
                btn.className = 'pixel-btn';
            }
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
    };

    window.leaveRoom = function() {
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
                        window.navigateToPage(data.redirect_url);
                    }
                })
                .catch(err => {
                    console.error(err);
                    window.navigateToPage('/room');
                });
            }
        });
    };

    window.copyRoomCode = function() {
        const code = document.getElementById('room-code-display').innerText;
        navigator.clipboard.writeText(code).then(() => {
            const btn = document.getElementById('copy-code-btn');
            if (btn) {
                const orig = btn.innerText;
                btn.innerText = '✅ TERSALIN!';
                setTimeout(() => { btn.innerText = orig; }, 2000);
            }
        }).catch(() => {
            // Fallback for older browsers
            const ta = document.createElement('textarea');
            ta.value = code;
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            const btn = document.getElementById('copy-code-btn');
            if (btn) {
                btn.innerText = '✅ TERSALIN!';
                setTimeout(() => { btn.innerText = '📋 SALIN'; }, 2000);
            }
        });
    };

    // ============= GLOBAL CHAT =============
    const chatCurrentUserId = {{ auth()->id() }};
    const chatCurrentUser   = "{{ addslashes(auth()->user()->nama_jalur ?? auth()->user()->email) }}";
    const chatRoomCode      = "{{ strtoupper(e($room->room_code)) }}";
    const chatRoomName      = "{{ addslashes(e($room->name)) }}";
    let chatWs    = null;
    let chatOpen  = false;
    let chatUnread = 0;
    const MAX_CHAT_MESSAGES = 80;

    window.initGlobalChat = function() {
        if (chatWs) return;

        const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        let wsUrl;
        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' || window.location.hostname.startsWith('192.168.')) {
            wsUrl = `${protocol}//${window.location.hostname}:8080`;
        } else {
            wsUrl = `${protocol}//${window.location.hostname}/ws`;
        }
        chatWs = new WebSocket(wsUrl);

        chatWs.onopen = () => {
            if (chatWs) {
                chatWs.send(JSON.stringify({
                    type: 'join',
                    roomId: 'global_chat',
                    payload: { userId: chatCurrentUserId, userName: chatCurrentUser, customizations: {} }
                }));
            }
        };

        chatWs.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data);
                if (data.type === 'global_chat') {
                    appendChatMessage(data.payload);
                } else if (data.type === 'chat_history') {
                    const container = document.getElementById('chat-messages');
                    if (container && Array.isArray(data.payload) && data.payload.length > 0) {
                        container.innerHTML = '';
                        data.payload.forEach(msg => appendChatMessage(msg));
                    }
                } else if (data.type === 'room_update' && data.payload && data.payload.players) {
                    const el = document.getElementById('chat-online-count');
                    if (el) el.textContent = data.payload.players.length;
                }
            } catch(e) {}
        };

        chatWs.onclose = () => {
            chatWs = null;
            if (document.getElementById('lobby-room-name')) {
                setTimeout(initGlobalChat, 3000);
            }
        };
        chatWs.onerror = () => {};
    };

    window.appendChatMessage = function(payload) {
        const container = document.getElementById('chat-messages');
        if (!container) return;
        const isMe = parseInt(payload.userId) === chatCurrentUserId;
        const d = new Date(payload.timestamp);
        const hh = String(d.getHours()).padStart(2,'0');
        const mm = String(d.getMinutes()).padStart(2,'0');
        const isRoomShare = payload.message && payload.message.startsWith('🏔 ROOM:');

        const msgEl = document.createElement('div');
        msgEl.className = 'chat-msg' + (isMe ? ' is-me' : '');
        msgEl.innerHTML = `
            <div class="chat-msg-name">${escapeHTML(payload.userName)}</div>
            <div class="chat-msg-bubble${isRoomShare ? ' room-share' : ''}">${escapeHTML(payload.message)}</div>
            <div class="chat-msg-time">${hh}:${mm}</div>
        `;
        container.appendChild(msgEl);
        while (container.children.length > MAX_CHAT_MESSAGES) container.removeChild(container.firstChild);
        container.scrollTop = container.scrollHeight;

        if (!chatOpen && !isMe) {
            chatUnread++;
            const dot = document.getElementById('chat-unread-dot');
            if (dot) dot.style.display = 'block';
        }
    };

    window.sendChat = function() {
        const input = document.getElementById('chat-input');
        if (!input) return;
        const msg = input.value.trim();
        if (!msg || !chatWs || chatWs.readyState !== WebSocket.OPEN) return;
        chatWs.send(JSON.stringify({
            type: 'global_chat',
            roomId: 'global_chat',
            payload: { userId: chatCurrentUserId, userName: chatCurrentUser, message: msg }
        }));
        input.value = '';
        input.focus();
    };

    window.shareRoomToChat = function() {
        if (!chatWs || chatWs.readyState !== WebSocket.OPEN) return;
        const shareMsg = `🏔 ROOM: ${chatRoomName} | KODE: ${chatRoomCode} | Masuk via menu Custom Room → masukkan kode`;
        chatWs.send(JSON.stringify({
            type: 'global_chat',
            roomId: 'global_chat',
            payload: { userId: chatCurrentUserId, userName: chatCurrentUser, message: shareMsg }
        }));
        if (!chatOpen) toggleChat();
    };

    window.toggleChat = function() {
        chatOpen = !chatOpen;
        const sidebar = document.getElementById('chat-sidebar');
        if (!sidebar) return;
        if (chatOpen) {
            sidebar.classList.add('open');
            chatUnread = 0;
            const dot = document.getElementById('chat-unread-dot');
            if (dot) dot.style.display = 'none';
            setTimeout(() => { const inp = document.getElementById('chat-input'); if (inp) inp.focus(); }, 350);
            const msgs = document.getElementById('chat-messages');
            if (msgs) msgs.scrollTop = msgs.scrollHeight;
        } else {
            sidebar.classList.remove('open');
        }
    };

    window.escapeHTML = function(str) {
        return String(str)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    };

    // Click outside chat handler
    const chatClickOutside = function(e) {
        if (!chatOpen) return;
        const sidebar = document.getElementById('chat-sidebar');
        const btn = document.getElementById('chat-toggle-btn');
        if (sidebar && !sidebar.contains(e.target) && btn && !btn.contains(e.target)) {
            chatOpen = false;
            sidebar.classList.remove('open');
        }
    };
    document.addEventListener('click', chatClickOutside);

    // Initial WebSocket calls
    initWebSocket();
    initGlobalChat();

    // Clean up connections on navigation to prevent leaks
    document.addEventListener('livewire:navigating', () => {
        document.removeEventListener('click', chatClickOutside);
        if (ws) {
            ws.close();
            ws = null;
            console.log('Match lobby WebSocket closed.');
        }
        if (chatWs) {
            chatWs.close();
            chatWs = null;
            console.log('Lobby page global chat WebSocket closed.');
        }
    }, { once: true });
}
</script>
@endpush
