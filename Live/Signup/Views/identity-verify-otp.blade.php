@extends('layouts.front')

@section('style')

    <style>
        body {
            background: var(--bs-dark);
        }

        .otp-form .otp-field {
            display: inline-block;
            width: 4rem;
            height: 4rem;
            font-size: 2rem;
            line-height: 4rem;
            text-align: center;
            border: 1px solid green;
            /* border-bottom: 2px solid var(--bs-secondary); */
            outline: none;
        }

        .otp-form .otp-field:focus {
            border-bottom-color: var(--bs-dark);
        }

        #loading-image {
            position: absolute;
            top: 210px;
            width: 220px;
            height: 200px;
            left: 400px;
            z-index: 600;
        }

        .iti {
            display: block;
        }
    </style>


    <link rel="stylesheet" href="{{ asset('vendor/cropperjs_v1.5.7/cropper.min.css') }}">
@endsection

@section("content")

{{-- //OTP Timeout Modal --}}

<div class="modal fade" id="TimeoutModal" role="dialog" aria-labelledby="TimeoutModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="TimeoutModalTitle">Time Out</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger text-center">
                    <strong>Your OTP verification time has expired. Please try again.</strong>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" data-dismiss="modal" onclick="sendOTP();">Resend OTP</button>
            </div>
        </div>
    </div>
</div>

{{-- //OTP Expired Modal --}}

<div class="modal fade" id="ExpiredModal" role="dialog" aria-labelledby="ExpiredModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="ExpiredModalTitle">OTP Expired</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger text-center">
                    <strong>Your OTP has expired. Please refresh the page and try again.</strong>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="location.reload();">Refresh Page</button>
            </div>
        </div>
    </div>
</div>


    <!-- NID data verification Modal -->
    <div class="modal fade" id="OTPVerifyModal" role="dialog" aria-labelledby="OTPVerifyModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="OTPVerifyModalTitle">
                        <div class="pull-left">
                            <span class="text-success" id="phone"> </span>
                        </div>
                        <div class="clearfix"></div>
                    </h4>
                </div>
                <div class="modal-body">
                    <!-- OTP Verification div -->
                    <div class="alert alert-info" >
                        <div class="card-body">
                            <h4 class="card-title text-center">OTP Verification</h4>
                            <div class="card-text text-center mt-5">
                                <form action="" class="otp-form">
                                    <input class="otp-field" type="text" name="opt-field[]" maxlength=1>
                                    <input class="otp-field" type="text" name="opt-field[]" maxlength=1>
                                    <input class="otp-field" type="text" name="opt-field[]" maxlength=1>
                                    <input class="otp-field" type="text" name="opt-field[]" maxlength=1>
                                    <input class="otp-field" type="text" name="opt-field[]" maxlength=1>
                                    <input class="otp-field" type="text" name="opt-field[]" maxlength=1>
                                </form>
                            </div>
                        </div>
                    </div>
                    {{-- need to update dynamicly --}}
                    <div class="text-center">
                        <h3><span id="otp-timer" class="text-danger">Expire In: {{ $otpExpireTimeInMinutes }}:00</span></h3>
                    </div>
                </div>
                <div class="modal-footer" id="NIDVerifyModalFooter">
                    <div class="pull-right">
{{--                        <button class="btn btn-success round-btn" id="SaveContinueBtn"onclick="submitOTPVerifyForm('OTPVerifyForm')">--}}
                        <button class="btn btn-success round-btn" type="submit" id="SaveContinueBtn" >
                            <i class="fa fa-save"></i> continue
                        </button>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <!-- End NID data verification Modal -->



    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            @if(Session::has('error'))
                <div class="alert alert-danger">
                    {{ Session::get('error') }}
                </div>
            @endif

            @if($errors->has())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="sign-up-box-div">
                <h4 class="text-center logo-color">
                    <strong>Registration process step-1 (For the first time only)</strong>
                </h4>
                <hr/>
                {{--
                @include('Training::signup-indentification')
                --}}

                <div class="col-md-10 col-md-offset-1 col-sm-12 user_mobile_verify_div" style="display: block">
                    {!! Form::open(array('route' => 'signup.otp_verify', 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'OTPVerifyForm', 'name' => 'OTPVerifyForm',
                    'enctype' =>'multipart/form-data', 'files' => 'true')) !!}

                        <!-- Store OTP Value -->
                        <input class="otp-value" type="hidden" name="otp_value" id="otp-value">

                        <div class="form-group" id="user_mobile_div" style="margin-left: 0px; margin-right: 0px;">
                            <div class="row">
                                <div class="col-md-12 {{$errors->has('user_mobile') ? 'has-error': ''}}">
                                    {!! Form::label('user_mobile','Contact Number',['class'=>'col-md-5 text-left required-star']) !!}
                                    <div class="col-md-5">
                                        {!! Form::text('user_mobile', Session::get('oauth_data')->mobile, ['class' => 'form-control input-md required phone_or_mobile']) !!}
                                        <span class="text-danger" id="mobile-error"></span> <!-- Error message container -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <br/>
                        <div class="form-group">
                            <div class="col-md-12 btn-center" hidden>
                                <button type="submit" title="You must fill in all of the fields"
                                        class="btn btn-md btn-success round-btn" id="submit_otp_button" >
                                    <i class="fas fa-check"></i>
                                    <strong>Submit</strong>
                                </button>
                            </div>
                            <div class="col-md-12 btn-end">
                                <div class="col-md-5"></div>
                                <div class="col-md-7">
                                    <button type="button" class="btn btn-md btn-success round-btn" id="send_otp_button" onclick="sendOTP();">
                                        <i class="fas fa-check"></i>
                                        <strong>Send OTP</strong>
                                    </button>
                                </div>
                                
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>

                <div class="clearfix"></div>
            </div>
        </div>
    </div>
@endsection

@section('footer-script')

    <script src="{{ asset('vendor/cropperjs_v1.5.7/cropper.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset("assets/scripts/sweetalert2.all.min.js") }}" type="text/javascript"></script>
    <script src="{{ asset("build/js/intlTelInput-jquery_v16.0.8.min.js") }}" type="text/javascript"></script>
    <script src="{{ asset("build/js/utils_v16.0.8.js") }}" type="text/javascript"></script>

    <script>
        $("#user_mobile").intlTelInput({
            hiddenInput: "user_mobile",
            initialCountry: "BD",
            placeholderNumberType: "MOBILE",
            separateDialCode: true
        });

        let timer;
        let countdownDuration = "{{ $otpExpireTime }}"; // 3 minutes in seconds, need to update dynamically
        let resendAttempts = 0;
        const maxResendAttempts = 2;

        function startTimer() {
            let timeLeft = countdownDuration;

            timer = setInterval(function() {
                let minutes = Math.floor(timeLeft / 60);
                let seconds = timeLeft % 60;

                // Format the time as MM:SS
                let formattedTime = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                document.getElementById('otp-timer').textContent = "Expire In: "+formattedTime;

                if (timeLeft <= 0) {
                    clearInterval(timer);
                    if (resendAttempts >= maxResendAttempts) {
                        $('#OTPVerifyModal').modal('hide');
                        $('#ExpiredModal').modal('show');
                    } else {
                        $('#OTPVerifyModal').modal('hide');
                        $('#TimeoutModal').modal('show');
                    }
                }

                timeLeft--;
            }, 1000);
        }

        function sendOTP() {
            var user_mobile = $("#user_mobile").intlTelInput("getNumber");

            // Validate the phone number
            if ($("#user_mobile").intlTelInput("isValidNumber")) {
                $('#send_otp_button').html('<i class="fas fa-spinner fa-spin"></i><strong>Sending OTP</strong>');
                $('#send_otp_button').prop('disabled', true);
                $.ajax({
                    url: '{{ route('signup.identity_verify_otp') }}',
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        user_mobile: user_mobile,
                    },
                    datatype: 'json',
                    success: function (response) {
                        $('#send_otp_button').html('<i class="fas fa-check"></i><strong>Send OTP</strong>');
                        $('#send_otp_button').prop('disabled', false);
                        $('#phone').text('OTP has been sent to: ' + user_mobile);
                        if (response.status === 'error') {
                            Swal.fire(
                                response.message,
                                '',
                                'error'
                            );
                        }
                        if (response.status === 'success') {
                            if (response.statusCode == 200) {
                                $('#OTPVerifyModal').modal('show');
                                startTimer(); // Start the countdown timer
                                var button = document.getElementById('send_otp_button');
                                button.innerHTML = '<i class="fas fa-check"></i><strong>Resend OTP</strong>';
                                resendAttempts++;
                            }
                        }
                    },
                    error: function (jqHR, textStatus, errorThrown) {
                        // Handle error
                    },
                    beforeSend: function () {
                        // Handle before send
                    }
                });
            } else {
                // Phone number is invalid, show an error message
                var errorMessage = document.getElementById("user_mobile");
                errorMessage.style.color = "red";
                errorMessage.style.border = "1px solid red";
                $("#mobile-error").text("Invalid phone number.");
            }
        }



        $(document).ready(function () {
            $("#SaveContinueBtn").click(function () {
                $("#submit_otp_button").click();

            });
        });
    </script>

    <script>
        // otp box
        $(document).ready(function () {
            $(".otp-form *:input[type!=hidden]:first").focus();
            let otp_fields = $(".otp-form .otp-field"),
                otp_value_field = $("#otp-value");
            otp_fields
                .on("input", function (e) {
                    $(this).val(
                        $(this)
                            .val()
                            .replace(/[^0-9]/g, "")
                    );
                    let opt_value = "";
                    otp_fields.each(function () {
                        let field_value = $(this).val();
                        if (field_value != "") opt_value += field_value;
                    });
                    otp_value_field.val(opt_value);
                })
                .on("keyup", function (e) {
                    let key = e.keyCode || e.charCode;
                    if (key == 8 || key == 46 || key == 37 || key == 40) {
                        // Backspace or Delete or Left Arrow or Down Arrow
                        $(this).prev().focus();
                    } else if (key == 38 || key == 39 || $(this).val() != "") {
                        // Right Arrow or Top Arrow or Value not empty
                        $(this).next().focus();
                    }
                })
                .on("paste", function (e) {
                    let paste_data = e.originalEvent.clipboardData.getData("text");
                    let paste_data_splitted = paste_data.split("");
                    $.each(paste_data_splitted, function (index, value) {
                        otp_fields.eq(index).val(value);
                    });
                });
        });
    </script>



    <script>
        $(document).on('change', '#signup_type', function (){
            var type = $(this).val();
            if(type == 'trainee'){
                $('.trainee_action_div').removeClass('hidden');
                $('.user_mobile_verify_div').addClass('hidden');
            }else{
                $('.trainee_action_div').addClass('hidden');
                $('.user_mobile_verify_div').removeClass('hidden');
            }
        })
    </script>
@endsection