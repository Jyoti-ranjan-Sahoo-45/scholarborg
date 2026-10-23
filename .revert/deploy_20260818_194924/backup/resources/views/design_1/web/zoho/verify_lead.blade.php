@extends('design_1.web.layouts.app')

@push('styles_top')
    <style>
        .verification-container {
            max-width: 500px;
            margin: 0 auto;
        }
        .otp-input-field {
            letter-spacing: 0.5em;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }
        .timer-sec {
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <div class="container mt-96 pb-104">
        <div class="verification-container">
            <div class="bg-white p-30 rounded-32 shadow-sm text-center">
                <div class="d-flex justify-content-center mb-24">
                    <div class="bg-primary-light p-16 rounded-circle">
                        <i class="feather-mail text-primary font-36"></i>
                    </div>
                </div>

                <h1 class="font-28 font-weight-bold text-dark">{{ $pageTitle }}</h1>
                <p class="font-14 text-gray mt-12 px-15">
                    We've sent a 6-digit verification code to <br>
                    <strong class="text-dark">{{ $email }}</strong>. <br>
                    Please enter the code below to complete your submission.
                </p>

                <form action="/zoho/verify-lead" method="post" class="mt-30 text-left">
                    {{ csrf_field() }}
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="form-group">
                        <label class="form-group-label text-center d-block font-14 font-weight-500 mb-10">Verification Code</label>
                        <input type="text" 
                               name="otp" 
                               id="otp" 
                               maxlength="6" 
                               placeholder="******" 
                               value="{{ old('otp') }}" 
                               class="form-control otp-input-field @error('otp') is-invalid @enderror" 
                               required 
                               autocomplete="one-time-code">
                        @error('otp')
                        <div class="invalid-feedback text-center mt-8">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-block rounded-pill mt-24 py-12 font-16 font-weight-bold">
                        Verify Code
                    </button>
                </form>

                <div class="mt-30 border-top pt-20">
                    <p class="font-14 text-gray">
                        Didn't receive the code? <br>
                        <span id="timer-container" class="mt-8 d-inline-block">
                            You can resend in <span class="timer-sec text-primary" id="timer-sec">60</span> seconds
                        </span>
                        <a href="javascript:void(0);" 
                           id="resend-btn" 
                           class="font-14 font-weight-bold text-primary d-none mt-8" 
                           onclick="handleResend()">
                            Resend Code
                        </a>
                    </p>
                    <div id="resend-message" class="font-12 mt-8 text-success font-weight-500"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts_bottom')
    <script>
        let seconds = 60;
        const timerSec = document.getElementById('timer-sec');
        const timerContainer = document.getElementById('timer-container');
        const resendBtn = document.getElementById('resend-btn');
        const resendMessage = document.getElementById('resend-message');
        
        let interval = setInterval(() => {
            seconds--;
            timerSec.innerText = seconds;
            
            if (seconds <= 0) {
                clearInterval(interval);
                timerContainer.classList.add('d-none');
                resendBtn.classList.remove('d-none');
            }
        }, 1000);

        function handleResend() {
            resendBtn.classList.add('d-none');
            resendMessage.classList.remove('text-danger', 'text-success');
            resendMessage.innerText = 'Sending new code...';
            
            fetch('/zoho/verify-lead/resend', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email: '{{ $email }}' })
            })
            .then(async response => {
                const data = await response.json();
                if (response.ok) {
                    resendMessage.classList.add('text-success');
                    resendMessage.innerText = data.message || 'Verification code resent successfully!';
                    
                    // Restart timer
                    seconds = 60;
                    timerSec.innerText = seconds;
                    timerContainer.classList.remove('d-none');
                } else {
                    resendMessage.classList.add('text-danger');
                    resendMessage.innerText = data.message || 'Failed to resend code. Please try again.';
                    resendBtn.classList.remove('d-none');
                }
            })
            .catch(error => {
                resendMessage.classList.add('text-danger');
                resendMessage.innerText = 'Network error. Please try again.';
                resendBtn.classList.remove('d-none');
            });
        }
    </script>
@endpush
