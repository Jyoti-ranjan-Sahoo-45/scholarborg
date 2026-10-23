<!-- VoIP Audio Call Overlay -->
<div id="voip-call-overlay" class="voip-overlay d-none">
    <div class="voip-card">
        <!-- Call Outgoing State -->
        <div id="voip-state-outgoing" class="voip-state d-none">
            <div class="voip-avatar-container">
                <img id="voip-outgoing-avatar" src="/assets/default/img/avatar.png" alt="Avatar" class="voip-avatar pulse-animation">
            </div>
            <h3 id="voip-outgoing-name" class="voip-name mt-15 text-white">Recipient Name</h3>
            <p class="voip-status text-gray mt-5">Calling...</p>
            
            <div class="voip-actions mt-30">
                <button id="voip-btn-cancel" class="voip-btn voip-btn-danger" title="Cancel Call">
                    <i class="voip-icon-hangup"></i>
                </button>
            </div>
        </div>

        <!-- Call Incoming State -->
        <div id="voip-state-incoming" class="voip-state d-none">
            <div class="voip-avatar-container">
                <img id="voip-incoming-avatar" src="/assets/default/img/avatar.png" alt="Avatar" class="voip-avatar ringing-animation">
            </div>
            <h3 id="voip-incoming-name" class="voip-name mt-15 text-white">Caller Name</h3>
            <p class="voip-status text-gray mt-5">Incoming Audio Call...</p>
            
            <div class="voip-actions mt-30 d-flex justify-content-center">
                <button id="voip-btn-decline" class="voip-btn voip-btn-danger mr-20" title="Decline">
                    <i class="voip-icon-hangup"></i>
                </button>
                <button id="voip-btn-accept" class="voip-btn voip-btn-success" title="Accept">
                    <i class="voip-icon-answer"></i>
                </button>
            </div>
        </div>

        <!-- Call Connected State -->
        <div id="voip-state-connected" class="voip-state d-none">
            <div class="voip-avatar-container">
                <img id="voip-connected-avatar" src="/assets/default/img/avatar.png" alt="Avatar" class="voip-avatar active-animation">
            </div>
            <h3 id="voip-connected-name" class="voip-name mt-15 text-white">Connected Name</h3>
            <p id="voip-call-timer" class="voip-timer mt-5">00:00</p>
            
            <div class="voip-actions mt-30 d-flex justify-content-center">
                <button id="voip-btn-mute" class="voip-btn voip-btn-secondary mr-20" title="Mute Microphone">
                    <i class="voip-icon-mute"></i>
                </button>
                <button id="voip-btn-hangup" class="voip-btn voip-btn-danger" title="Hang Up">
                    <i class="voip-icon-hangup"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* VoIP Call Overlay Styles */
    .voip-overlay {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 320px;
        background: rgba(26, 32, 44, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        z-index: 99999;
        font-family: 'Outfit', sans-serif;
        overflow: hidden;
        transition: all 0.3s ease-in-out;
    }
    
    .voip-card {
        padding: 30px 24px;
        text-align: center;
    }
    
    .voip-avatar-container {
        position: relative;
        display: inline-block;
        width: 100px;
        height: 100px;
    }
    
    .voip-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 3px solid rgba(255, 255, 255, 0.2);
        object-fit: cover;
    }

    .voip-name {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0;
    }

    .voip-status {
        font-size: 0.9rem;
        font-weight: 400;
        margin-bottom: 0;
    }

    .voip-timer {
        font-size: 1.1rem;
        color: #10B981;
        font-weight: 500;
        letter-spacing: 1px;
    }

    .voip-actions {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .voip-btn {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        border: none;
        outline: none;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: all 0.2s ease;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
    }

    .voip-btn:hover {
        transform: scale(1.08);
    }

    .voip-btn-danger {
        background-color: #EF4444;
        color: white;
    }

    .voip-btn-success {
        background-color: #10B981;
        color: white;
    }

    .voip-btn-secondary {
        background-color: rgba(255, 255, 255, 0.15);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .voip-btn-secondary.muted {
        background-color: #F59E0B;
        color: white;
    }

    /* SVG Icons styling */
    .voip-icon-hangup::before {
        content: "";
        display: block;
        width: 24px;
        height: 24px;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>');
        transform: rotate(135deg);
    }

    .voip-icon-answer::before {
        content: "";
        display: block;
        width: 24px;
        height: 24px;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>');
    }

    .voip-icon-mute::before {
        content: "";
        display: block;
        width: 24px;
        height: 24px;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>');
    }

    .voip-icon-mute.muted::before {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .9-1.077 1.337-1.707.707L5.586 15z" /><path stroke-linecap="round" stroke-linejoin="round" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" /></svg>');
    }

    /* Animations */
    @keyframes pulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 15px rgba(255, 255, 255, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
    }

    @keyframes ring {
        0% { transform: rotate(0) scale(1); }
        15% { transform: rotate(5deg) scale(1.05); }
        30% { transform: rotate(-5deg) scale(1.05); }
        45% { transform: rotate(4deg) scale(1.05); }
        60% { transform: rotate(-4deg) scale(1.05); }
        75% { transform: rotate(2deg) scale(1.02); }
        90% { transform: rotate(-2deg) scale(1.02); }
        100% { transform: rotate(0) scale(1); }
    }

    .pulse-animation {
        animation: pulse 2s infinite;
    }

    .ringing-animation {
        animation: ring 1s infinite;
    }

    .active-animation {
        border-color: #10B981 !important;
        animation: pulse 2.5s infinite;
    }
</style>
