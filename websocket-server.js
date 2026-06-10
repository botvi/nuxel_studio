import { WebSocketServer, WebSocket } from 'ws';
import http from 'http';

const server = http.createServer();
const wss = new WebSocketServer({ server });

// Store room connections: room_id => { players: Map(userId => ws), readyStates: Map(userId => readyBool), customizations: Map(userId => customObj), names: Map(userId => nameStr) }
const rooms = new Map();

// Global chat history — simpan 50 pesan terakhir, bertahan selama server running
const CHAT_HISTORY_MAX = 50;
const chatHistory = [];

wss.on('connection', (ws) => {
    let currentRoomId = null;
    let userId = null;

    ws.on('message', (message) => {
        try {
            const data = JSON.parse(message);
            const { type, roomId, payload } = data;

            if (type === 'join') {
                currentRoomId = roomId;
                userId = payload.userId;
                ws.userId = userId;
                ws.userName = payload.userName;

                if (!rooms.has(roomId)) {
                    rooms.set(roomId, {
                        players: new Map(),
                        readyStates: new Map(),
                        arenaReadyStates: new Map(),
                        customizations: new Map(),
                        names: new Map()
                    });
                }

                const room = rooms.get(roomId);
                room.players.set(userId, ws);
                room.customizations.set(userId, payload.customizations);
                room.names.set(userId, payload.userName);

                console.log(`User ${payload.userName} (ID: ${userId}) joined room ${roomId}`);

                // Kirim riwayat chat ke user baru yang join global_chat
                if (roomId === 'global_chat' && chatHistory.length > 0) {
                    if (ws.readyState === WebSocket.OPEN) {
                        ws.send(JSON.stringify({
                            type: 'chat_history',
                            payload: chatHistory
                        }));
                    }
                }

                // Notify all players in room about the current players list
                broadcastToRoom(roomId, {
                    type: 'room_update',
                    payload: getRoomPlayersData(roomId)
                });
            }

            else if (type === 'ready') {
                const room = rooms.get(currentRoomId);
                if (room) {
                    room.readyStates.set(userId, payload.ready);
                    console.log(`User (ID: ${userId}) ready status in room ${currentRoomId} set to: ${payload.ready}`);

                    broadcastToRoom(currentRoomId, {
                        type: 'room_update',
                        payload: getRoomPlayersData(currentRoomId)
                    });

                    // Check if both players are ready
                    const playersArray = Array.from(room.players.keys());
                    if (playersArray.length === 2 && 
                        room.readyStates.get(playersArray[0]) === true && 
                        room.readyStates.get(playersArray[1]) === true) {
                        
                        console.log(`Both players ready in room ${currentRoomId}. Starting game...`);
                        broadcastToRoom(currentRoomId, {
                            type: 'game_start',
                            payload: {
                                roomId: currentRoomId
                            }
                        });
                    }
                }
            }

            else if (type === 'arena_ready') {
                const room = rooms.get(currentRoomId);
                if (room) {
                    room.arenaReadyStates.set(userId, true);
                    console.log(`User (ID: ${userId}) arena ready status set to: true`);

                    const playersArray = Array.from(room.players.keys());
                    if (playersArray.length === 2 && 
                        room.arenaReadyStates.get(playersArray[0]) === true && 
                        room.arenaReadyStates.get(playersArray[1]) === true) {
                        
                        console.log(`Both players arena-ready in room ${currentRoomId}. Broadcasting countdown start...`);
                        broadcastToRoom(currentRoomId, {
                            type: 'start_countdown',
                            payload: {}
                        });
                    }
                }
            }

            else if (type === 'game_state_sync') {
                // Relay game state to the opponent (speed, distance)
                const room = rooms.get(currentRoomId);
                if (room) {
                    for (const [pId, pWs] of room.players.entries()) {
                        if (pId !== userId && pWs.readyState === WebSocket.OPEN) {
                            pWs.send(JSON.stringify({
                                type: 'opponent_sync',
                                payload: {
                                    userId: userId,
                                    speed: payload.speed,
                                    distance: payload.distance,
                                    isTapped: payload.isTapped,
                                    feedback: payload.feedback || null,
                                    feedbackStroke: payload.feedbackStroke || null,
                                    tintTop: payload.tintTop || null,
                                    tintBottom: payload.tintBottom || null
                                }
                            }));
                        }
                    }
                }
            }

            else if (type === 'game_over') {
                const room = rooms.get(currentRoomId);
                if (room) {
                    console.log(`Game over in room ${currentRoomId}. Winner ID: ${payload.winnerId}`);
                    broadcastToRoom(currentRoomId, {
                        type: 'game_finished',
                        payload: {
                            winnerId: payload.winnerId
                        }
                    });
                }
            }

            else if (type === 'global_chat') {
                // Broadcast chat ke semua user yang join global_chat room
                const msg = (payload.message || '').toString().trim().slice(0, 200);
                if (!msg) return;
                const chatMsg = {
                    userId: userId,
                    userName: payload.userName || 'Anonim',
                    message: msg,
                    timestamp: Date.now()
                };
                // Simpan ke history
                chatHistory.push(chatMsg);
                if (chatHistory.length > CHAT_HISTORY_MAX) chatHistory.shift();

                broadcastToRoom('global_chat', {
                    type: 'global_chat',
                    payload: chatMsg
                });
                console.log(`[GLOBAL CHAT] ${chatMsg.userName}: ${msg}`);
            }
        } catch (e) {
            console.error('Error handling message:', e);
        }
    });

    ws.on('close', () => {
        if (currentRoomId && rooms.has(currentRoomId)) {
            const room = rooms.get(currentRoomId);
            room.players.delete(userId);
            room.readyStates.delete(userId);
            room.arenaReadyStates.delete(userId);
            room.customizations.delete(userId);
            room.names.delete(userId);

            console.log(`User (ID: ${userId}) disconnected from room ${currentRoomId}`);

            if (room.players.size === 0) {
                // Jangan hapus global_chat agar history tetap bertahan
                if (currentRoomId !== 'global_chat') {
                    rooms.delete(currentRoomId);
                    console.log(`Room ${currentRoomId} is empty. Deleting room.`);
                }
            } else {
                broadcastToRoom(currentRoomId, {
                    type: 'room_update',
                    payload: getRoomPlayersData(currentRoomId)
                });
            }
        }
    });
});

function broadcastToRoom(roomId, messageObj) {
    const room = rooms.get(roomId);
    if (room) {
        const msgStr = JSON.stringify(messageObj);
        for (const ws of room.players.values()) {
            if (ws.readyState === WebSocket.OPEN) {
                ws.send(msgStr);
            }
        }
    }
}

function getRoomPlayersData(roomId) {
    const room = rooms.get(roomId);
    if (!room) return { players: [] };
    const playersData = [];
    for (const [pId, pWs] of room.players.entries()) {
        playersData.push({
            userId: pId,
            userName: room.names.get(pId),
            ready: room.readyStates.get(pId) || false,
            customizations: room.customizations.get(pId)
        });
    }
    return {
        players: playersData
    };
}

const PORT = 8080;
server.listen(PORT, () => {
    console.log(`WebSocket server listening on port ${PORT}`);
});
