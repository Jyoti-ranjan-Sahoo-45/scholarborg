// VoIP Audio Calling Script using Agora RTC SDK
$(document).ready(function () {
    "use strict";

    var agoraAppId = '';
    var activeCallId = null;
    var activeChannelName = null;
    var activeRtcToken = null;
    var callTimerInterval = null;
    var statusPollInterval = null;
    var incomingPollInterval = null;
    var callTimeoutTimer = null;
    var rtcClient = null;
    var localAudioTrack = null;
    var isMuted = false;

    // Web Audio Synthesizer variables
    var audioCtx = null;
    var toneInterval = null;

    // Fetch settings on load
    $.get('/panel/audio-calls/check-incoming', function (res) {
        // Polling will initialize below
    });

    // 1. Play Ringing Sounds using Web Audio API (to bypass downloading asset files)
    function playTelephoneTone(isRingback) {
        try {
            if (!audioCtx) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }

            function play() {
                var osc1 = audioCtx.createOscillator();
                var osc2 = audioCtx.createOscillator();
                var gainNode = audioCtx.createGain();

                osc1.type = 'sine';
                osc2.type = 'sine';

                if (isRingback) {
                    // Outgoing ringback tone: 440Hz + 480Hz
                    osc1.frequency.value = 440;
                    osc2.frequency.value = 480;
                } else {
                    // Incoming telephone ring: 440Hz + 480Hz with faster beat
                    osc1.frequency.value = 453;
                    osc2.frequency.value = 440;
                }

                gainNode.gain.setValueAtTime(0, audioCtx.currentTime);
                gainNode.gain.linearRampToValueAtTime(0.2, audioCtx.currentTime + 0.1);
                gainNode.gain.linearRampToValueAtTime(0.2, audioCtx.currentTime + 1.8);
                gainNode.gain.linearRampToValueAtTime(0, audioCtx.currentTime + 2.0);

                osc1.connect(gainNode);
                osc2.connect(gainNode);
                gainNode.connect(audioCtx.destination);

                osc1.start();
                osc2.start();

                osc1.stop(audioCtx.currentTime + 2.0);
                osc2.stop(audioCtx.currentTime + 2.0);
            }

            stopTones();
            play();
            toneInterval = setInterval(play, 6000); // 2s on, 4s off
        } catch (e) {
            console.error("Web Audio synthesis failed: ", e);
        }
    }

    function stopTones() {
        if (toneInterval) {
            clearInterval(toneInterval);
            toneInterval = null;
        }
    }

    // 2. Start Incoming Call Polling (every 5 seconds)
    function startIncomingPoll() {
        incomingPollInterval = setInterval(function () {
            if (activeCallId !== null) return; // Skip if in call

            $.ajax({
                url: '/panel/audio-calls/check-incoming',
                method: 'GET',
                success: function (res) {
                    if (res.active) {
                        showIncomingCall(res);
                    }
                }
            });
        }, 5000);
    }
    startIncomingPoll();

    // 3. Initiate Outgoing Call
    $('body').on('click', '.btn-voip-call', function (e) {
        e.preventDefault();
        var receiverId = $(this).data('receiver-id');
        var receiverName = $(this).data('receiver-name') || 'User';

        if (activeCallId !== null) {
            alert("You are already in an active call.");
            return;
        }

        // Show loader modal or state
        $.ajax({
            url: '/panel/audio-calls/initiate',
            method: 'POST',
            data: {
                receiver_id: receiverId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if (res.success) {
                    activeCallId = res.call_id;
                    activeChannelName = res.channel_name;
                    activeRtcToken = res.rtc_token;

                    agoraAppId = res.agora_app_id || '53364d20895748c09c175abfeff7cdc0'; // Agora App ID

                    showOutgoingCall(res.receiver_name, res.receiver_avatar);
                    playTelephoneTone(true); // Play ringback tone

                    // Start checking status (recipient accepted/rejected)
                    startStatusPoll();
                } else {
                    alert(res.message || "Failed to place call.");
                }
            },
            error: function (xhr) {
                var msg = "Call blocked: " + (xhr.responseJSON ? xhr.responseJSON.message : "Connection failed.");
                alert(msg);
            }
        });
    });

    // 4. Show Outgoing Call Panel
    function showOutgoingCall(name, avatar) {
        $('#voip-outgoing-name').text(name);
        $('#voip-outgoing-avatar').attr('src', avatar);
        
        $('#voip-state-outgoing').removeClass('d-none');
        $('#voip-state-incoming').addClass('d-none');
        $('#voip-state-connected').addClass('d-none');
        $('#voip-call-overlay').removeClass('d-none');
    }

    // 5. Show Incoming Call Panel
    function showIncomingCall(callInfo) {
        activeCallId = callInfo.call_id;
        activeChannelName = callInfo.channel_name;
        activeRtcToken = callInfo.rtc_token;
        agoraAppId = '53364d20895748c09c175abfeff7cdc0';

        $('#voip-incoming-name').text(callInfo.caller_name);
        $('#voip-incoming-avatar').attr('src', callInfo.caller_avatar);

        $('#voip-state-outgoing').addClass('d-none');
        $('#voip-state-incoming').removeClass('d-none');
        $('#voip-state-connected').addClass('d-none');
        $('#voip-call-overlay').removeClass('d-none');

        playTelephoneTone(false); // Play ringtone

        // Auto hangup after 30 seconds if unanswered
        callTimeoutTimer = setTimeout(function () {
            declineCall();
        }, 30000);

        startStatusPoll();
    }

    // 6. Accept Call (Receiver)
    $('#voip-btn-accept').click(function () {
        if (callTimeoutTimer) clearTimeout(callTimeoutTimer);
        stopTones();

        $.ajax({
            url: '/panel/audio-calls/' + activeCallId + '/accept',
            method: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                if (res.success) {
                    $('#voip-connected-name').text($('#voip-incoming-name').text());
                    $('#voip-connected-avatar').attr('src', $('#voip-incoming-avatar').attr('src'));
                    
                    showConnectedState();
                    joinAgoraChannel(authUserId);
                } else {
                    hangupCall();
                }
            },
            error: function () {
                hangupCall();
            }
        });
    });

    // 7. Decline Call (Receiver)
    $('#voip-btn-decline').click(function () {
        declineCall();
    });

    function declineCall() {
        if (callTimeoutTimer) clearTimeout(callTimeoutTimer);
        stopTones();

        $.ajax({
            url: '/panel/audio-calls/' + activeCallId + '/reject',
            method: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            complete: function () {
                resetCallUI();
            }
        });
    }

    // 8. Cancel Call (Caller)
    $('#voip-btn-cancel').click(function () {
        stopTones();
        $.ajax({
            url: '/panel/audio-calls/' + activeCallId + '/cancel',
            method: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            complete: function () {
                resetCallUI();
            }
        });
    });

    // 9. Hangup Call
    $('#voip-btn-hangup').click(function () {
        hangupCall();
    });

    function hangupCall() {
        stopTones();
        if (activeCallId) {
            $.ajax({
                url: '/panel/audio-calls/' + activeCallId + '/hangup',
                method: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                complete: function () {
                    leaveAgoraChannel();
                    resetCallUI();
                }
            });
        }
    }

    // 10. Join Agora RTC Room
    async function joinAgoraChannel(uid) {
        try {
            if (!rtcClient) {
                rtcClient = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });
            }

            // Set up audio subscriptions
            rtcClient.on("user-published", async (user, mediaType) => {
                if (mediaType === "audio") {
                    await rtcClient.subscribe(user, mediaType);
                    user.audioTrack.play();
                }
            });

            rtcClient.on("user-unpublished", (user) => {
                // Audio track naturally stops playing
            });

            // Join Room
            await rtcClient.join(agoraAppId, activeChannelName, activeRtcToken, uid);

            // Publish microphone track
            localAudioTrack = await AgoraRTC.createMicrophoneAudioTrack();
            await rtcClient.publish([localAudioTrack]);

            console.log("Joined Agora calling room successfully!");
        } catch (e) {
            console.error("Agora RTC Connection failed: ", e);
            hangupCall();
        }
    }

    // 11. Leave Agora Room
    async function leaveAgoraChannel() {
        try {
            if (localAudioTrack) {
                localAudioTrack.close();
                localAudioTrack = null;
            }
            if (rtcClient) {
                await rtcClient.leave();
                rtcClient = null;
            }
        } catch (e) {
            console.error("Agora RTC Close error: ", e);
        }
    }

    // 12. Mute Microphone
    $('#voip-btn-mute').click(function () {
        if (!localAudioTrack) return;

        if (isMuted) {
            localAudioTrack.setMuted(false);
            isMuted = false;
            $(this).removeClass('muted');
            $(this).find('i').removeClass('muted');
        } else {
            localAudioTrack.setMuted(true);
            isMuted = true;
            $(this).addClass('muted');
            $(this).find('i').addClass('muted');
        }
    });

    // 13. Call Status Polling (Caller tracks response / Recipient tracks caller hangup)
    function startStatusPoll() {
        if (statusPollInterval) clearInterval(statusPollInterval);

        statusPollInterval = setInterval(function () {
            if (!activeCallId) return;

            $.ajax({
                url: '/panel/audio-calls/' + activeCallId + '/check-status',
                method: 'GET',
                success: function (res) {
                    if (res.status === 'connected' && $('#voip-state-connected').hasClass('d-none')) {
                        // Recipient accepted! Join connection
                        stopTones();
                        showConnectedState();
                        joinAgoraChannel(authUserId);
                    } else if (res.status === 'rejected' || res.status === 'completed') {
                        // Call hung up or rejected
                        stopTones();
                        leaveAgoraChannel();
                        resetCallUI();
                    }
                },
                error: function () {
                    // Fallback to disconnect on continuous server errors
                    stopTones();
                    leaveAgoraChannel();
                    resetCallUI();
                }
            });
        }, 2000);
    }

    // 14. UI Transition Helpers
    function showConnectedState() {
        $('#voip-state-outgoing').addClass('d-none');
        $('#voip-state-incoming').addClass('d-none');
        $('#voip-state-connected').removeClass('d-none');

        // Start Timer
        var seconds = 0;
        if (callTimerInterval) clearInterval(callTimerInterval);
        
        callTimerInterval = setInterval(function () {
            seconds++;
            var m = Math.floor(seconds / 60).toString().padStart(2, '0');
            var s = (seconds % 60).toString().padStart(2, '0');
            $('#voip-call-timer').text(m + ':' + s);
        }, 1000);
    }

    function resetCallUI() {
        if (callTimerInterval) clearInterval(callTimerInterval);
        if (statusPollInterval) clearInterval(statusPollInterval);
        
        $('#voip-call-overlay').addClass('d-none');
        $('#voip-state-outgoing').addClass('d-none');
        $('#voip-state-incoming').addClass('d-none');
        $('#voip-state-connected').addClass('d-none');
        
        $('#voip-call-timer').text('00:00');
        
        activeCallId = null;
        activeChannelName = null;
        activeRtcToken = null;
        isMuted = false;
        $('#voip-btn-mute').removeClass('muted');
        
        stopTones();
    }
});
