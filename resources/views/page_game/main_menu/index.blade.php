@extends('layouts.game')

@section('title', 'Room Matchmaking — Papan Jawara')

@section('content')
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

    .bg-slide-0 {
        background: rgba(6, 17, 10, 0.9);
    }

    .bg-slide-1 {
        background: rgba(15, 5, 20, 0.9);
    }

    .bg-slide-2 {
        background: rgba(20, 10, 5, 0.9);
    }

    .bg-slide-3 {
        background: rgba(20, 5, 5, 0.9);
    }

    .bg-slide-4 {
        background: rgba(18, 15, 5, 0.9);
    }

    .bg-slide-5 {
        background: rgba(5, 10, 20, 0.9);
    }

    /* --- PS5 Top Header Layout (Reverted to Flat Retro Theme) --- */
    .profile-btn {
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

    .profile-btn img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        image-rendering: pixelated;
    }

    .profile-btn:hover {
        transform: scale(1.05);
    }

    .profile-btn:active {
        transform: scale(0.9);
    }

    .sound-btn {
        position: absolute;
        top: 16px;
        left: 50%;
        transform: translateX(-50%);
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

    .sound-btn img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        image-rendering: pixelated;
    }

    .sound-btn:hover {
        transform: translateX(-50%) scale(1.05);
    }

    .sound-btn:active {
        transform: translateX(-50%) scale(0.9);
    }

    .coin-display {
        position: absolute;
        top: 16px;
        right: 14px;
        height: 36px;
        display: flex;
        align-items: center;
        gap: 6px;
        z-index: 15;
        box-sizing: border-box;
    }

    .coin-icon-wrapper {
        position: relative;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .coin-icon-wrapper img {
        width: 100%;
        height: 100%;
        image-rendering: pixelated;
    }

    .coin-icon-wrapper::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: none;
        display: none;
    }

    #header-coin-count {
        font-family: 'Pixelify Sans', monospace;
        font-size: 13px;
        font-weight: bold;
        color: #FFD700;
        text-shadow:
            1px 1px 0px #15803d,
            -1px -1px 0px #15803d,
            1px -1px 0px #15803d,
            -1px 1px 0px #15803d,
            0px 1px 0px #15803d,
            0px -1px 0px #15803d,
            1px 0px 0px #15803d,
            -1px 0px 0px #15803d;
        line-height: 1;
    }

    @keyframes htmlShimmer {
        0% {
            transform: translateX(-150%) skewX(-25deg);
        }

        100% {
            transform: translateX(150%) skewX(-25deg);
        }
    }

    .title-banner {
        font-family: 'Press Start 2P', monospace;
        font-size: 12px;
        background: linear-gradient(180deg, #ffffff 0%, #a5f3fc 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        filter: drop-shadow(0px 2px 4px rgba(0, 0, 0, 0.8));
        margin-top: 76px;
        margin-bottom: 8px;
        text-align: center;
        line-height: 1.4;
        letter-spacing: 2px;
        z-index: 11;
    }

    /* --- Glassmorphic Jalur Preview Widget --- */
    .jalur-preview-box {
        margin-top: 4px;
        margin-bottom: 10px !important;
        width: calc(100% - 32px);
        max-width: 320px;
        background: rgba(0, 0, 0, 0.45) !important;
        border: 2px solid rgba(255, 255, 255, 0.15) !important;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.4) !important;
        backdrop-filter: blur(10px) !important;
        -webkit-backdrop-filter: blur(10px) !important;
        border-radius: 16px;
        padding: 8px 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        box-sizing: border-box;
        z-index: 12;
    }

    .jalur-preview-box .preview-title {
        font-family: 'Press Start 2P', monospace;
        font-size: 8px;
        color: #00ffff !important;
        margin-bottom: 6px;
        letter-spacing: 0.5px;
        text-shadow: 0 0 5px rgba(0, 255, 255, 0.5) !important;
    }

    .jalur-preview-box .canvas-container {
        width: 250px;
        height: 85px;
        overflow: hidden;
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 8px;
        background: rgba(0, 0, 0, 0.3) !important;
        border: 2px solid rgba(0, 255, 255, 0.2) !important;
    }

    .jalur-preview-box .canvas-container canvas {
        display: block;
        image-rendering: pixelated;
        image-rendering: crisp-edges;
    }

    .jalur-preview-box .preview-name {
        font-family: 'Pixelify Sans', monospace;
        font-size: 12px;
        font-weight: bold;
        color: #e59e0b !important;
        margin-top: 6px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.6) !important;
    }

    /* --- PS5 Carousel Slider --- */
    .ps5-carousel-container {
        position: relative;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 5px;
        margin-bottom: 10px;
        z-index: 12;
    }

    .ps5-carousel-view {
        width: 100%;
        max-width: 300px;
        height: 155px;
        overflow: visible;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .ps5-carousel-track {
        display: flex;
        align-items: center;
        gap: 20px;
        transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1);
        will-change: transform;
    }

    .ps5-card {
        width: 110px;
        height: 135px;
        flex-shrink: 0;
        border-radius: 16px;
        border: 2px solid rgba(255, 255, 255, 0.15);
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 12px;
        box-sizing: border-box;
        cursor: pointer;
        position: relative;
        transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
        opacity: 0.45;
        transform: scale(0.85);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        overflow: hidden;
    }

    .ps5-card.active {
        opacity: 1;
        transform: scale(1.1);
        border-color: #ffffff;
        background: rgba(255, 255, 255, 0.12);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.6), 0 0 15px var(--glow-color);
    }

    .ps5-card.card-green {
        --glow-color: rgba(34, 197, 94, 0.6);
    }

    .ps5-card.card-red {
        --glow-color: rgba(239, 68, 68, 0.6);
    }

    .ps5-card.card-orange {
        --glow-color: rgba(249, 115, 22, 0.6);
    }

    .ps5-card.card-yellow {
        --glow-color: rgba(234, 179, 8, 0.6);
    }

    .ps5-card.card-purple {
        --glow-color: rgba(168, 85, 247, 0.6);
    }

    .ps5-card.card-blue {
        --glow-color: rgba(59, 130, 246, 0.6);
    }

    .ps5-card-icon {
        width: 52px;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.4));
        transition: transform 0.3s ease;
    }

    .ps5-card.active .ps5-card-icon {
        transform: translateY(-4px) scale(1.08);
    }

    .ps5-card-icon img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        image-rendering: pixelated;
    }

    .ps5-card-label {
        font-family: 'Press Start 2P', monospace;
        font-size: 8px;
        text-align: center;
        color: #ffffff;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
        line-height: 1.4;
        pointer-events: none;
        font-weight: bold;
    }

    .ps5-pattern {
        position: absolute;
        bottom: 4px;
        right: 6px;
        font-family: Arial, sans-serif;
        font-size: 20px;
        font-weight: bold;
        color: rgba(255, 255, 255, 0.04);
        pointer-events: none;
        user-select: none;
        line-height: 1;
    }

    .ps5-card.active .ps5-pattern {
        color: rgba(255, 255, 255, 0.1);
    }

    .carousel-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 13;
        transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        padding: 0;
        box-shadow: none;
    }

    .carousel-nav-btn img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        image-rendering: pixelated;
    }

    .carousel-nav-btn:hover {
        transform: translateY(-50%) scale(1.15);
        filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.6));
    }

    .carousel-nav-btn:active {
        transform: translateY(-50%) scale(0.9);
    }

    .prev-btn {
        left: 4px;
    }

    .next-btn {
        right: 4px;
    }

    /* --- Slide details & Controller CTA button --- */
    .ps5-details-container {
        width: 90%;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        z-index: 12;
        margin-top: 2px;
    }

    .ps5-details-title {
        font-family: 'Press Start 2P', monospace;
        font-size: 11px;
        letter-spacing: 1px;
        color: #ffffff;
        margin-bottom: 6px;
        text-shadow: 0 0 10px var(--glow-color);
        transition: text-shadow 0.3s;
    }

    .ps5-details-desc {
        font-family: 'Pixelify Sans', monospace;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 12px;
        height: 34px;
        line-height: 1.4;
        max-width: 250px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    }

    .ps5-indicators {
        display: flex;
        gap: 8px;
        margin-bottom: 14px;
    }

    .ps5-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.25);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .ps5-dot.active {
        background: #ffffff;
        transform: scale(1.25);
        box-shadow: 0 0 8px #ffffff;
    }

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
        letter-spacing: 0.5px;
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
            inset 0 1px 0 rgba(255, 255, 255, 0.15),
            0 1px 0 #14532d,
            0 2px 6px rgba(34, 197, 94, 0.2);
    }

    /* Loading Overlay */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.85);
        display: none;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 30;
        backdrop-filter: blur(4px);
    }

    .search-container {
        position: relative;
        width: 120px;
        height: 120px;
        margin-bottom: 40px;
    }

    /* Radar sweep */
    .radar {
        position: absolute;
        top: 10px;
        left: 10px;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 3px solid rgba(34, 197, 94, 0.4);
        box-shadow: 0 0 15px rgba(34, 197, 94, 0.3), inset 0 0 15px rgba(34, 197, 94, 0.2);
        overflow: hidden;
        background: rgba(34, 197, 94, 0.1);
    }

    .radar::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 50px;
        height: 50px;
        background: linear-gradient(45deg, rgba(34, 197, 94, 0.9) 0%, transparent 60%);
        transform-origin: 0 0;
        animation: radarSweep 1.5s linear infinite;
    }

    @keyframes radarSweep {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .magnifying-glass {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        animation: searchHover 3s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        z-index: 2;
    }

    .lens {
        width: 44px;
        height: 44px;
        border: 6px solid #ffffff;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        position: absolute;
        top: 20px;
        left: 20px;
        box-shadow: inset 4px 4px 0 rgba(255, 255, 255, 0.6), 0 6px 12px rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(2px);
    }

    .handle {
        width: 12px;
        height: 35px;
        background: #d97706;
        position: absolute;
        top: 60px;
        left: 60px;
        transform: rotate(-45deg);
        transform-origin: top left;
        border-radius: 6px;
        border: 3px solid #78350f;
        box-shadow: inset -2px -2px 0 rgba(0, 0, 0, 0.3), 2px 2px 5px rgba(0, 0, 0, 0.4);
    }

    @keyframes searchHover {
        0% { transform: translate(0px, 0px) scale(1) rotate(0deg); }
        33% { transform: translate(25px, -15px) scale(1.1) rotate(15deg); }
        66% { transform: translate(-15px, 20px) scale(0.95) rotate(-10deg); }
        100% { transform: translate(0px, 0px) scale(1) rotate(0deg); }
    }

    .loading-text {
        font-family: 'Press Start 2P', monospace;
        font-size: 14px;
        color: #22c55e;
        -webkit-text-stroke: 1px #ffffff;
        text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.6);
        animation: pulseText 1.5s infinite;
        text-align: center;
        line-height: 1.6;
        margin-bottom: 10px;
    }

    @keyframes pulseText {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.8; }
    }

    .btn-cancel {
        background: linear-gradient(180deg, #ef4444 0%, #dc2626 100%);
        border-color: #991b1b;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,0.3),
            0 5px 0 #7f1d1d,
            0 6px 14px rgba(239, 68, 68, 0.35);
        width: auto;
        font-size: 10px;
        padding: 12px 25px;
        margin-top: 30px;
        text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    }

    .btn-cancel:hover {
        background: linear-gradient(180deg, #f87171 0%, #ef4444 100%);
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,0.35),
            0 5px 0 #7f1d1d,
            0 8px 20px rgba(239, 68, 68, 0.45);
        transform: translateY(-1px);
    }

    .btn-cancel:active {
        transform: translateY(4px);
        box-shadow: 0 1px 0 #7f1d1d;
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

    #chat-toggle-btn:hover {
        background: linear-gradient(135deg, #334155 0%, #1e293b 100%);
        width: 40px;
    }

    #chat-toggle-btn .chat-icon { font-size: 16px; }

    #chat-unread-dot {
        position: absolute;
        top: 4px; right: 4px;
        width: 8px; height: 8px;
        background: #ef4444;
        border-radius: 50%;
        display: none;
        animation: dotPulse 1s infinite ease-in-out;
    }

    @keyframes dotPulse {
        0%,100% { transform: scale(1); opacity: 1; }
        50%      { transform: scale(1.4); opacity: 0.7; }
    }

    #chat-sidebar {
        position: absolute;
        top: 0; left: -260px;
        width: 260px;
        height: 100%;
        background: rgba(8, 15, 30, 0.92);
        border-right: 1px solid rgba(255,255,255,0.1);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        z-index: 49;
        display: flex;
        flex-direction: column;
        box-shadow: 4px 0 24px rgba(0,0,0,0.6);
        transition: left 0.32s cubic-bezier(0.4, 0, 0.2, 1);
        box-sizing: border-box;
    }

    #chat-sidebar.open { left: 0; }

    #chat-sidebar::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: repeating-linear-gradient(
            0deg, transparent, transparent 3px,
            rgba(0,0,0,0.03) 3px, rgba(0,0,0,0.03) 4px
        );
        pointer-events: none;
        z-index: 0;
    }

    .chat-header {
        padding: 12px 14px 10px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        z-index: 1;
        flex-shrink: 0;
    }

    .chat-header-title {
        font-family: 'Press Start 2P', monospace;
        font-size: 7px;
        color: #22c55e;
        text-shadow: 0 0 8px rgba(34,197,94,0.5);
        letter-spacing: 0.5px;
    }

    .chat-online-badge {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 10px;
        color: rgba(255,255,255,0.4);
    }

    .chat-online-dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: #22c55e;
        box-shadow: 0 0 5px rgba(34,197,94,0.7);
        animation: dotPulse 2s infinite;
    }

    #chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 10px 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
        z-index: 1;
        scrollbar-width: thin;
        scrollbar-color: rgba(34,197,94,0.3) rgba(255,255,255,0.02);
    }

    #chat-messages::-webkit-scrollbar { width: 3px; }
    #chat-messages::-webkit-scrollbar-thumb {
        background: rgba(34,197,94,0.3);
        border-radius: 3px;
    }

    .chat-msg {
        display: flex;
        flex-direction: column;
        gap: 2px;
        animation: msgSlideIn 0.2s ease;
    }

    @keyframes msgSlideIn {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .chat-msg.is-me { align-items: flex-end; }

    .chat-msg-name {
        font-family: 'Press Start 2P', monospace;
        font-size: 5.5px;
        color: rgba(255,255,255,0.45);
        padding: 0 6px;
    }

    .chat-msg.is-me .chat-msg-name { color: rgba(34,197,94,0.7); }

    .chat-msg-bubble {
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 10px 10px 10px 2px;
        padding: 7px 10px;
        font-size: 12px;
        color: #e2e8f0;
        max-width: 86%;
        word-break: break-word;
        line-height: 1.4;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }

    .chat-msg.is-me .chat-msg-bubble {
        background: rgba(34,197,94,0.12);
        border-color: rgba(34,197,94,0.25);
        border-radius: 10px 10px 2px 10px;
        color: #d1fae5;
    }

    .chat-msg-time {
        font-size: 9px;
        color: rgba(255,255,255,0.2);
        padding: 0 6px;
    }

    .chat-system-msg {
        text-align: center;
        font-family: 'Press Start 2P', monospace;
        font-size: 5.5px;
        color: rgba(255,255,255,0.25);
        padding: 4px 0;
    }

    .chat-input-area {
        padding: 10px 12px;
        border-top: 1px solid rgba(255,255,255,0.08);
        display: flex;
        gap: 7px;
        position: relative;
        z-index: 1;
        flex-shrink: 0;
    }

    #chat-input {
        flex: 1;
        background: rgba(255,255,255,0.05);
        border: 1.5px solid rgba(255,255,255,0.12);
        border-radius: 8px;
        padding: 8px 10px;
        font-family: 'Pixelify Sans', monospace;
        font-size: 13px;
        color: #ffffff;
        outline: none;
        transition: all 0.2s;
    }

    #chat-input:focus {
        border-color: rgba(34,197,94,0.5);
        background: rgba(255,255,255,0.08);
        box-shadow: 0 0 8px rgba(34,197,94,0.15);
    }

    #chat-input::placeholder { color: rgba(255,255,255,0.2); }

    #chat-send-btn {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        border: none;
        border-radius: 8px;
        padding: 8px 10px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.15s ease;
        flex-shrink: 0;
        box-shadow: 0 3px 8px rgba(34,197,94,0.3);
    }

    #chat-send-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 12px rgba(34,197,94,0.4);
    }

    #chat-send-btn:active { transform: translateY(1px); }

    /* PWA Install Alert Banner */
    .pwa-install-alert {
        position: absolute;
        top: -250px;
        left: 5%;
        width: 90%;
        background: rgba(15, 23, 42, 0.95);
        border: 3px solid #22c55e;
        border-radius: 16px;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.75), 0 0 20px rgba(34, 197, 94, 0.25);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        padding: 18px 16px;
        box-sizing: border-box;
        z-index: 1050;
        transition: top 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .pwa-install-alert.show {
        top: 20px;
    }

    .pwa-alert-header {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .pwa-alert-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        border: 2px solid #22c55e;
        object-fit: cover;
    }

    .pwa-alert-title-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
    }

    .pwa-alert-title {
        font-family: 'Press Start 2P', monospace;
        font-size: 9px;
        color: #22c55e;
        text-shadow: 0 0 8px rgba(34, 197, 94, 0.4);
        margin: 0;
    }

    .pwa-alert-desc {
        font-family: 'Pixelify Sans', monospace;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.8);
        margin: 0;
        line-height: 1.4;
    }

    .pwa-alert-buttons {
        display: flex;
        gap: 12px;
        width: 100%;
    }

    .pwa-btn {
        flex: 1;
        font-family: 'Press Start 2P', monospace;
        font-size: 8px;
        padding: 12px 0;
        border: 3px solid #000000;
        border-radius: 8px;
        cursor: pointer;
        text-align: center;
        transition: all 0.1s;
        box-shadow: 0px 4px 0px #000000;
    }

    .pwa-btn-install {
        background-color: #22c55e;
        color: white;
        text-shadow: 1.5px 1.5px 0px #000000;
    }

    .pwa-btn-install:hover {
        background-color: #4ade80;
    }

    .pwa-btn-install:active {
        transform: translateY(4px);
        box-shadow: 0px 0px 0px #000000;
    }

    .pwa-btn-cancel {
        background-color: #475569;
        color: #cbd5e1;
        text-shadow: 1px 1px 0px #000000;
    }

    .pwa-btn-cancel:hover {
        background-color: #64748b;
    }

    .pwa-btn-cancel:active {
        transform: translateY(4px);
        box-shadow: 0px 0px 0px #000000;
    }

    .pwa-ios-instructions {
        font-family: 'Pixelify Sans', monospace;
        font-size: 11px;
        color: rgba(255, 255, 255, 0.9);
        background: rgba(34, 197, 94, 0.1);
        border: 1px dashed rgba(34, 197, 94, 0.4);
        border-radius: 8px;
        padding: 10px;
        margin-top: 2px;
        display: flex;
        align-items: center;
        gap: 8px;
        line-height: 1.4;
    }

    .pwa-ios-icon {
        font-size: 18px;
        display: inline-block;
        flex-shrink: 0;
    }
</style>

<div id="game-ui">
    <!-- Top Bar elements (Profile, Sound, Coins) -->
    <div class="profile-btn" onclick="window.navigateToPage('/profil')">
        @php
        $dbFoto = auth()->user()->foto_profile;
        if (!empty($dbFoto)) {
            if (strpos($dbFoto, 'http://') === 0 || strpos($dbFoto, 'https://') === 0) {
                $profileImgSrc = $dbFoto;
            } elseif (strpos($dbFoto, '/') !== false || strpos($dbFoto, '.gif') !== false) {
                $profileImgSrc = (strpos($dbFoto, '/') === 0) ? $dbFoto : '/' . $dbFoto;
            } else {
                $profileImgSrc = '/game_pacu/assets/image/ui/' . $dbFoto . '.gif';
            }
        } else {
            $profileImgSrc = '/game_pacu/assets/image/ui/profil.gif';
        }
        @endphp
        <img src="{{ $profileImgSrc }}" alt="Profile">
    </div>

    <!-- Sound Toggle (Top Middle) -->
    <div id="sound-btn" class="sound-btn" onclick="openAudioSettings()">
        <img id="sound-icon" src="/game_pacu/assets/image/ui/sound_on.png" alt="Sound">
    </div>

    <!-- Coin Display (Top Right) -->
    <div class="coin-display">
        <div class="coin-icon-wrapper">
            <img src="/game_pacu/assets/image/ui/koin.png" alt="Coin">
        </div>
        <span id="header-coin-count">{{ number_format(auth()->user()->kuansing_poin, 0, ',', '.') }}</span>
    </div>
    <div id="ps5-backdrop" class="ps5-backdrop-glow bg-slide-0"></div>
    <canvas id="ps5-particles" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none; opacity: 0.5;"></canvas>

    <div class="title-banner"></div>

    <!-- JALUR PREVIEW BOX -->
    <div id="jalur-preview-container" class="jalur-preview-box"
        style="background: none !important; border: none !important; box-shadow: none !important; backdrop-filter: none !important; -webkit-backdrop-filter: none !important; margin-bottom: 5px !important;">
        <div class="preview-title">PERAHU SAAT INI</div>
        <div class="canvas-container" id="jalur-preview-canvas" style="width: 250px; height: 85px; border: none; background: transparent;"></div>
        <div class="preview-name" id="jalur-preview-name">LOADING...</div>
    </div>

    <!-- Carousel Menu PS5 -->
    <div class="ps5-carousel-container">
        <button class="carousel-nav-btn prev-btn" onclick="prevSlide(event)">
            <img src="/game_pacu/assets/image/ui/btn_kiri.png" alt="Left">
        </button>
        <div class="ps5-carousel-view">
            <div class="ps5-carousel-track" id="carousel-track">
                <!-- Slide 0: MAIN PACU -->
                <div class="ps5-card card-green active" data-index="0" onclick="selectSlide(0, event)">
                    <div class="ps5-card-icon">
                        <img src="/game_pacu/assets/image/ui/kayuah.png" alt="Main">
                    </div>
                    <div class="ps5-card-label">MAIN PACU</div>
                    <div class="ps5-pattern">&#9587;</div>
                </div>
                <!-- Slide 1: SHOP -->
                <div class="ps5-card card-purple" data-index="1" onclick="selectSlide(1, event)">
                    <div class="ps5-card-icon">
                        <img src="/game_pacu/assets/image/ui/tentang.png" alt="Shop">
                    </div>
                    <div class="ps5-card-label">SHOP</div>
                    <div class="ps5-pattern">&#9587;</div>
                </div>
                <!-- Slide 2: TUKANG JALUAR -->
                <div class="ps5-card card-orange" data-index="2" onclick="selectSlide(2, event)">
                    <div class="ps5-card-icon">
                        <img src="/game_pacu/assets/image/ui/tukang.png" alt="Tukang">
                    </div>
                    <div class="ps5-card-label">TUKANG JALUAR</div>
                    <div class="ps5-pattern">&#9711;</div>
                </div>
                <!-- Slide 3: CARI PEMAIN -->
                <div class="ps5-card card-red" data-index="3" onclick="selectSlide(3, event)">
                    <div class="ps5-card-icon">
                        <img src="/game_pacu/assets/image/ui/magnifer.png" alt="Search">
                    </div>
                    <div class="ps5-card-label">CARI PEMAIN</div>
                    <div class="ps5-pattern">&#9651;</div>
                </div>
                <!-- Slide 4: LEADERBOARD -->
                <div class="ps5-card card-yellow" data-index="4" onclick="selectSlide(4, event)">
                    <div class="ps5-card-icon">
                        <img src="/game_pacu/assets/image/ui/piala.png" alt="Trophy">
                    </div>
                    <div class="ps5-card-label">LEADERBOARD</div>
                    <div class="ps5-pattern">&#9633;</div>
                </div>
            </div>
        </div>
        <button class="carousel-nav-btn next-btn" onclick="nextSlide(event)">
            <img src="/game_pacu/assets/image/ui/btn_kanan.png" alt="Right">
        </button>
    </div>

    <!-- Slide Details & Button -->
    <div class="ps5-details-container">
        <div class="ps5-details-title" id="active-title" style="--glow-color: rgba(34, 197, 94, 0.6)">MAIN PACU</div>
        <div class="ps5-details-desc" id="active-desc">Cari lawan & mulai balapan jalur</div>
        <div class="ps5-indicators">
            <span class="ps5-dot active" onclick="jumpToSlide(0)"></span>
            <span class="ps5-dot" onclick="jumpToSlide(1)"></span>
            <span class="ps5-dot" onclick="jumpToSlide(2)"></span>
            <span class="ps5-dot" onclick="jumpToSlide(3)"></span>
            <span class="ps5-dot" onclick="jumpToSlide(4)"></span>
        </div>
    </div>
    <div class="loading-overlay" id="loading-overlay">
        <div class="search-container">
            <div class="radar"></div>
            <div class="magnifying-glass">
                <div class="lens"></div>
                <div class="handle"></div>
            </div>
        </div>
        <div class="loading-text">Mencari<br>Lawan...</div>
        <button class="pixel-btn btn-cancel" onclick="batalCari()">BATAL</button>
    </div>

    <!-- Custom Coming Soon Modal -->
    <div id="coming-soon-modal"
        style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(6px); z-index: 210; align-items: center; justify-content: center; box-sizing: border-box;">
        <div class="coming-soon-card"
            style="background: #ffffff; border: 4px solid #000000; box-shadow: 6px 6px 0px #000000; border-radius: 12px; width: 85%; max-width: 300px; padding: 22px 18px; text-align: center; box-sizing: border-box; font-family: 'Press Start 2P', monospace;">
            <div style="font-size: 10px; color: #a855f7; margin-bottom: 20px; border-bottom: 3px dashed #000000; padding-bottom: 12px; font-weight: bold; letter-spacing: 0.5px;">✦ FITUR DUMMY ✦</div>
            <p style="font-family: 'Pixelify Sans', monospace; font-size: 13px; color: #374151; margin-bottom: 20px; line-height: 1.5;">Menu ini adalah simulasi dummy dan akan segera dikembangkan di masa mendatang!</p>
            <button class="pixel-btn" onclick="closeComingSoon()"
                style="margin-top: 0; background-color: #a855f7; border: 3px solid #000000; box-shadow: inset 0 2px 0px rgba(255,255,255,0.4), 0px 4px 0px #000000; color: white; padding: 12px; font-size: 9px; cursor: pointer; text-transform: uppercase; width: 100%; text-shadow: 1.5px 1.5px 0px #000000;">OKE</button>
        </div>
    </div>

    <!-- Custom Audio Settings Modal -->
    <div id="audio-settings-modal"
        style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(2, 44, 34, 0.85); backdrop-filter: blur(6px); z-index: 200; align-items: center; justify-content: center; box-sizing: border-box;">
        <div class="audio-modal-card"
            style="background: #ffffff; border: 4px solid #000000; box-shadow: 6px 6px 0px #000000; border-radius: 12px; width: 85%; max-width: 300px; padding: 22px 18px; text-align: center; box-sizing: border-box; font-family: 'Press Start 2P', monospace;">
            <div class="audio-modal-title" style="font-size: 10px; color: #0d9488; margin-bottom: 20px; border-bottom: 3px dashed #000000; padding-bottom: 12px; font-weight: bold; letter-spacing: 0.5px;">✦ PENGATURAN SUARA ✦</div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <span style="font-size: 8px; color: #15803d; text-align: left; text-shadow: 1px 1px 0px rgba(0,0,0,0.05);">MUSIK (BGM)</span>
                <button id="bgm-toggle-btn" onclick="toggleBGMSetting()" style="font-family: 'Press Start 2P', monospace; font-size: 8px; width: 80px; padding: 8px 0; border: 3px solid #000000; border-radius: 6px; cursor: pointer; text-shadow: 1.5px 1.5px 0px #000000; color: white; transition: all 0.1s; box-shadow: 0px 3px 0px #000000;">ON</button>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <span style="font-size: 8px; color: #15803d; text-align: left; text-shadow: 1px 1px 0px rgba(0,0,0,0.05);">EFEK (SFX)</span>
                <button id="sfx-toggle-btn" onclick="toggleSFXSetting()" style="font-family: 'Press Start 2P', monospace; font-size: 8px; width: 80px; padding: 8px 0; border: 3px solid #000000; border-radius: 6px; cursor: pointer; text-shadow: 1.5px 1.5px 0px #000000; color: white; transition: all 0.1s; box-shadow: 0px 3px 0px #000000;">ON</button>
            </div>
            <button class="pixel-btn" onclick="closeAudioSettings()" style="margin-top: 0; background-color: #22c55e; border: 3px solid #000000; box-shadow: inset 0 2px 0px rgba(255,255,255,0.4), 0px 4px 0px #000000; color: white; padding: 12px; font-size: 9px; cursor: pointer; text-transform: uppercase; width: 100%; text-shadow: 1.5px 1.5px 0px #000000;">OKE</button>
        </div>
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
    <div id="chat-messages">
        <div class="chat-system-msg">— Selamat datang di Global Chat —</div>
    </div>
    <div class="chat-input-area">
        <input type="text" id="chat-input" placeholder="Ketik pesan..." maxlength="200" onkeydown="if(event.key==='Enter') sendChat()">
        <button id="chat-send-btn" onclick="sendChat()" title="Kirim">➤</button>
    </div>
</div>

<!-- PWA Install Alert Dialog -->
<div id="pwa-install-alert" class="pwa-install-alert">
    <div class="pwa-alert-header">
        <img src="/game_pacu/assets/image/ui/pwa-icon-192.png" alt="Icon Game" class="pwa-alert-icon">
        <div class="pwa-alert-title-group">
            <h4 class="pwa-alert-title">✦ PASANG GAME ✦</h4>
            <p class="pwa-alert-desc">Pasang game Pacu Jalur di Home Screen kamu untuk bermain lebih lancar, cepat, dan layar penuh!</p>
        </div>
    </div>
    <div id="pwa-ios-guide" class="pwa-ios-instructions" style="display: none;">
        <span class="pwa-ios-icon">📤</span>
        <span>Ketuk tombol <strong>Bagikan (Share)</strong> di Safari lalu pilih <strong>'Tambahkan ke Layar Utama (Add to Home Screen)'</strong>.</span>
    </div>
    <div class="pwa-alert-buttons">
        <button id="pwa-btn-cancel" class="pwa-btn pwa-btn-cancel">BATAL</button>
        <button id="pwa-btn-install" class="pwa-btn pwa-btn-install">PASANG</button>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/phaser@3.88.2/dist/phaser.min.js"></script>
<script src="/game_pacu/assets/js/jalur-preview-phaser.js?v={{ time() }}"></script>
<script>
{
    // Close existing global chat WebSocket connection to prevent duplication
    if (window.chatWs) {
        window.chatWs.close();
        window.chatWs = null;
    }

    // Load and Sync customizations
    (function () {
        const initialCoins = {{ auth()->user()->kuansing_poin }};
        const coinEl = document.getElementById('header-coin-count');
        if (coinEl) {
            coinEl.innerText = initialCoins.toLocaleString('id-ID');
        }

        fetch('/tukang-jaluar/get')
            .then(res => res.json())
            .then(data => {
                if (data.customColors) {
                    for (const key in data.customColors) {
                        localStorage.setItem('custom_' + key, data.customColors[key]);
                    }
                }
                if (data.corak_data_url) {
                    localStorage.setItem('corak_data_url', data.corak_data_url);
                } else {
                    localStorage.removeItem('corak_data_url');
                }
                if (data.lambai_data_url) {
                    localStorage.setItem('lambai_data_url', data.lambai_data_url);
                } else {
                    localStorage.removeItem('lambai_data_url');
                }
                if (data.coins !== undefined) {
                    localStorage.setItem('coins', String(data.coins));
                    if (coinEl) {
                        coinEl.innerText = data.coins.toLocaleString('id-ID');
                    }
                }
            })
            .catch(err => console.error('Failed to sync customizations:', err));
    })();

    // Audio Settings Modal handlers
    window.openAudioSettings = function() {
        const modal = document.getElementById('audio-settings-modal');
        if (modal) {
            modal.style.display = 'flex';
            syncAudioModalButtons();
        }
    };

    window.closeAudioSettings = function() {
        const modal = document.getElementById('audio-settings-modal');
        if (modal) modal.style.display = 'none';
    };

    window.syncAudioModalButtons = function() {
        const bgmMuted = localStorage.getItem('bgm_muted') === 'true';
        const sfxMuted = localStorage.getItem('sfx_muted') === 'true';

        const bgmBtn = document.getElementById('bgm-toggle-btn');
        const sfxBtn = document.getElementById('sfx-toggle-btn');

        if (bgmBtn) {
            if (bgmMuted) {
                bgmBtn.textContent = 'OFF';
                bgmBtn.style.backgroundColor = '#ef4444';
                bgmBtn.style.boxShadow = '0px 3px 0px #991b1b';
            } else {
                bgmBtn.textContent = 'ON';
                bgmBtn.style.backgroundColor = '#22c55e';
                bgmBtn.style.boxShadow = '0px 3px 0px #15803d';
            }
        }

        if (sfxBtn) {
            if (sfxMuted) {
                sfxBtn.textContent = 'OFF';
                sfxBtn.style.backgroundColor = '#ef4444';
                sfxBtn.style.boxShadow = '0px 3px 0px #991b1b';
            } else {
                sfxBtn.textContent = 'ON';
                sfxBtn.style.backgroundColor = '#22c55e';
                sfxBtn.style.boxShadow = '0px 3px 0px #15803d';
            }
        }

        if (window.updateSoundIcon) window.updateSoundIcon();
    };

    // toggleBGMSetting & toggleSFXSetting sudah didefinisikan global
    // di game-layout.js dan langsung apply ke window.globalBGM

    window.cariLawan = function() {
        document.getElementById('loading-overlay').style.display = 'flex';
        window.searchTimeout = setTimeout(() => {}, 60000);
    };

    window.batalCari = function() {
        document.getElementById('loading-overlay').style.display = 'none';
        if (window.searchTimeout) {
            clearTimeout(window.searchTimeout);
        }
    };

    // Initialize Jalur Preview Canvas
    if (typeof window.initJalurPreview === 'function') {
        window.initJalurPreview('jalur-preview-canvas', 'jalur-preview-name');
    }

    document.addEventListener('livewire:navigated', () => {
        if (typeof window.initJalurPreview === 'function' && document.getElementById('jalur-preview-canvas')) {
            window.initJalurPreview('jalur-preview-canvas', 'jalur-preview-name');
        }
    }, { once: true });

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
            const canvasEl = document.getElementById('ps5-particles');
            if (!canvasEl) return; // Stop loop if canvas is gone on navigation

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

    // Carousel Menu PS5
    const slidesData = [
        { title: 'MAIN PACU', desc: 'Cari lawan & mulai balapan jalur', url: '/room', glow: 'rgba(34, 197, 94, 0.6)', action: 'link' },
        { title: 'SHOP', desc: 'Beli koin KP & unduh template item', url: '/shop', glow: 'rgba(168, 85, 247, 0.6)', action: 'link' },
        { title: 'TUKANG JALUAR', desc: 'Kustomisasi perahu & pendayung', url: '/tukang-jaluar', glow: 'rgba(249, 115, 22, 0.6)', action: 'link' },
        { title: 'CARI PEMAIN', desc: 'Cari profil pemain lain', url: '/cari-pemain', glow: 'rgba(239, 68, 68, 0.6)', action: 'link' },
        { title: 'LEADERBOARD', desc: 'Lihat peringkat pemain terbaik', url: '/leaderboard', glow: 'rgba(234, 179, 8, 0.6)', action: 'link' },
    ];
    let currentSlide = 0;

    window.updateCarousel = function() {
        const track = document.getElementById('carousel-track');
        const view = document.querySelector('.ps5-carousel-view');
        if (!track || !view) return;
        const cards = document.querySelectorAll('.ps5-card');
        const dots = document.querySelectorAll('.ps5-dot');
        const backdrop = document.getElementById('ps5-backdrop');

        const cardWidth = 110;
        const gap = 20;

        const viewWidth = view.offsetWidth || 300;
        const centerOffset = (viewWidth - cardWidth) / 2;

        const translateX = centerOffset - currentSlide * (cardWidth + gap);
        track.style.transform = `translateX(${translateX}px)`;

        cards.forEach((card, idx) => {
            if (idx === currentSlide) {
                card.classList.add('active');
            } else {
                card.classList.remove('active');
            }
        });

        dots.forEach((dot, idx) => {
            if (idx === currentSlide) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });

        const activeData = slidesData[currentSlide];
        document.getElementById('active-title').innerText = activeData.title;
        document.getElementById('active-desc').innerText = activeData.desc;

        document.getElementById('active-title').style.setProperty('--glow-color', activeData.glow);
        const actionBtn = document.getElementById('ps5-action-btn');
        if (actionBtn) {
            actionBtn.style.setProperty('--glow-color', activeData.glow);
        }

        if (backdrop) {
            backdrop.className = 'ps5-backdrop-glow bg-slide-' + currentSlide;
        }
    };

    window.nextSlide = function(e) {
        if (e) e.stopPropagation();
        currentSlide = (currentSlide + 1) % slidesData.length;
        if (typeof window.playClickSound === 'function') window.playClickSound();
        updateCarousel();
    };

    window.prevSlide = function(e) {
        if (e) e.stopPropagation();
        currentSlide = (currentSlide - 1 + slidesData.length) % slidesData.length;
        if (typeof window.playClickSound === 'function') window.playClickSound();
        updateCarousel();
    };

    window.selectSlide = function(idx, e) {
        if (e) e.stopPropagation();
        if (idx !== currentSlide) {
            currentSlide = idx;
            if (typeof window.playClickSound === 'function') window.playClickSound();
            updateCarousel();
            return;
        }
        if (typeof window.playClickSound === 'function') window.playClickSound();
        updateCarousel();
        activateActiveSlide();
    };

    window.jumpToSlide = function(idx) {
        if (currentSlide !== idx) {
            currentSlide = idx;
            if (typeof window.playClickSound === 'function') window.playClickSound();
            updateCarousel();
        }
    };

    window.activateActiveSlide = function() {
        const activeData = slidesData[currentSlide];
        if (activeData.action === 'search') {
            cariLawan();
        } else if (activeData.action === 'link_dummy') {
            openComingSoon();
        } else {
            const url = activeData.url;
            window.navigateToPage(url);
        }
    };

    window.openComingSoon = function() {
        const modal = document.getElementById('coming-soon-modal');
        if (modal) modal.style.display = 'flex';
    };

    window.closeComingSoon = function() {
        const modal = document.getElementById('coming-soon-modal');
        if (modal) modal.style.display = 'none';
    };

    // Swipe Gestures Support
    (function () {
        let touchStartX = 0;
        let touchEndX = 0;

        const container = document.querySelector('.ps5-carousel-container');
        if (!container) return;

        container.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        container.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            const diff = touchEndX - touchStartX;
            if (Math.abs(diff) > 40) {
                if (diff < 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }
        }, { passive: true });
    })();

    // Keyboard Navigation Controller
    document.addEventListener('keydown', function (e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
        if (e.key === 'ArrowLeft') {
            prevSlide();
        } else if (e.key === 'ArrowRight') {
            nextSlide();
        } else if (e.key === 'Enter' || e.key === ' ') {
            activateActiveSlide();
        }
    });

    // Initial position trigger
    setTimeout(updateCarousel, 100);

    // ============= GLOBAL CHAT =============
    const chatCurrentUserId  = {{ auth()->id() }};
    const chatCurrentUser    = "{{ addslashes(auth()->user()->nama_jalur ?? auth()->user()->email) }}";
    let chatOpen             = false;
    let chatUnread           = 0;
    const MAX_MESSAGES       = 80;

    function initGlobalChat() {
        if (window.chatWs) return;

        const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        let wsUrl;
        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' || window.location.hostname.startsWith('192.168.')) {
            wsUrl = `${protocol}//${window.location.hostname}:8080`;
        } else {
            wsUrl = `${protocol}//${window.location.hostname}/ws`;
        }
        
        window.chatWs = new WebSocket(wsUrl);

        window.chatWs.onopen = () => {
            if (window.chatWs) {
                window.chatWs.send(JSON.stringify({
                    type: 'join',
                    roomId: 'global_chat',
                    payload: {
                        userId: chatCurrentUserId,
                        userName: chatCurrentUser,
                        customizations: {}
                    }
                }));
            }
        };

        window.chatWs.onmessage = (event) => {
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
                } else if (data.type === 'room_update') {
                    const count = data.payload && data.payload.players ? data.payload.players.length : 0;
                    const el = document.getElementById('chat-online-count');
                    if (el) el.textContent = count;
                }
            } catch(e) {}
        };

        window.chatWs.onclose = () => {
            window.chatWs = null;
            // Only reconnect if we are still on the main menu page
            if (document.getElementById('game-ui')) {
                setTimeout(initGlobalChat, 3000);
            }
        };

        window.chatWs.onerror = () => {};
    }

    function appendChatMessage(payload) {
        const container = document.getElementById('chat-messages');
        if (!container) return;

        const isMe = parseInt(payload.userId) === chatCurrentUserId;

        const d = new Date(payload.timestamp);
        const hh = String(d.getHours()).padStart(2,'0');
        const mm = String(d.getMinutes()).padStart(2,'0');

        const msgEl = document.createElement('div');
        msgEl.className = 'chat-msg' + (isMe ? ' is-me' : '');
        msgEl.innerHTML = `
            <div class="chat-msg-name">${escapeHTML(payload.userName)}</div>
            <div class="chat-msg-bubble">${escapeHTML(payload.message)}</div>
            <div class="chat-msg-time">${hh}:${mm}</div>
        `;
        container.appendChild(msgEl);

        while (container.children.length > MAX_MESSAGES) {
            container.removeChild(container.firstChild);
        }

        container.scrollTop = container.scrollHeight;

        if (!chatOpen && !isMe) {
            chatUnread++;
            const dot = document.getElementById('chat-unread-dot');
            if (dot) dot.style.display = 'block';
        }
    }

    window.sendChat = function() {
        const input = document.getElementById('chat-input');
        if (!input) return;
        const msg = input.value.trim();
        if (!msg) return;
        if (!window.chatWs || window.chatWs.readyState !== WebSocket.OPEN) {
            return;
        }
        window.chatWs.send(JSON.stringify({
            type: 'global_chat',
            roomId: 'global_chat',
            payload: {
                userId: chatCurrentUserId,
                userName: chatCurrentUser,
                message: msg
            }
        }));
        input.value = '';
        input.focus();
    };

    window.toggleChat = function() {
        chatOpen = !chatOpen;
        const sidebar = document.getElementById('chat-sidebar');
        if (sidebar) {
            if (chatOpen) {
                sidebar.classList.add('open');
                chatUnread = 0;
                const dot = document.getElementById('chat-unread-dot');
                if (dot) dot.style.display = 'none';
                setTimeout(() => {
                    const inp = document.getElementById('chat-input');
                    if (inp) inp.focus();
                }, 350);
                const msgs = document.getElementById('chat-messages');
                if (msgs) msgs.scrollTop = msgs.scrollHeight;
            } else {
                sidebar.classList.remove('open');
            }
        }
    };

    function escapeHTML(str) {
        return String(str)
            .replace(/&/g,'&amp;')
            .replace(/</g,'&lt;')
            .replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;')
            .replace(/'/g,'&#39;');
    }

    // Close chat when clicking outside
    document.addEventListener('click', function(e) {
        if (!chatOpen) return;
        const sidebar = document.getElementById('chat-sidebar');
        const toggleBtn = document.getElementById('chat-toggle-btn');
        if (sidebar && !sidebar.contains(e.target) && toggleBtn && !toggleBtn.contains(e.target)) {
            chatOpen = false;
            sidebar.classList.remove('open');
        }
    });

    initGlobalChat();

    // Cleanup global chat WS connection on navigation
    document.addEventListener('livewire:navigating', () => {
        if (window.chatWs) {
            window.chatWs.close();
            window.chatWs = null;
            console.log('Main menu global chat WebSocket connection closed.');
        }
        
        // Also cleanup active preview game if any is active on this page
        if (window.activePreviewGame) {
            window.activePreviewGame.destroy(true);
            window.activePreviewGame = null;
        }
    }, { once: true });

    // ---- PWA Service Worker & Install Prompt Logic ----
    let deferredPrompt;
    const pwaAlert = document.getElementById('pwa-install-alert');
    const btnInstall = document.getElementById('pwa-btn-install');
    const btnCancel = document.getElementById('pwa-btn-cancel');
    const iosGuide = document.getElementById('pwa-ios-guide');

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => console.log('[PWA] Service Worker registered:', reg.scope))
                .catch(err => console.error('[PWA] Service Worker registration failed:', err));
        });
    }

    function isInstalled() {
        return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
    }

    function isIOS() {
        return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    }

    function showPwaNotification() {
        const dismissedTime = localStorage.getItem('pwa-prompt-dismissed');
        const now = Date.now();
        if (dismissedTime && (now - parseInt(dismissedTime)) < (24 * 60 * 60 * 1000)) return;
        if (isInstalled()) return;

        setTimeout(() => {
            if (pwaAlert) pwaAlert.classList.add('show');
        }, 1500);
    }

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        showPwaNotification();
    });

    if (btnInstall) {
        btnInstall.addEventListener('click', async () => {
            if (pwaAlert) pwaAlert.classList.remove('show');
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                deferredPrompt = null;
            } else if (isIOS()) {
                if (pwaAlert) pwaAlert.classList.add('show');
                if (iosGuide) iosGuide.style.display = 'flex';
                btnInstall.style.display = 'none';
                if (btnCancel) btnCancel.textContent = 'OKE';
            }
        });
    }

    if (btnCancel) {
        btnCancel.addEventListener('click', () => {
            if (pwaAlert) pwaAlert.classList.remove('show');
            localStorage.setItem('pwa-prompt-dismissed', Date.now().toString());
        });
    }

    if (isIOS() && !isInstalled()) {
        showPwaNotification();
    }
}
</script>
@endpush
