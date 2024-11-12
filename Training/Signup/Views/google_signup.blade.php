@extends('layouts.front')
<?php
$users=Auth::user();
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style type="text/css">
    .identity_hover, .identity_type{
        cursor: pointer;
    }
</style>
<style>
    fieldset.scheduler-border {
        border: 1px solid #afa3a3 !important;
        padding: 0 1.4em 1.4em 1.4em !important;
        margin: 0 0 1.5em 0 !important;
        -webkit-box-shadow:  0px 0px 0px 0px #000;
        box-shadow:  0px 0px 0px 0px #000;
    }

    legend.scheduler-border {
        font-size: 1.2em !important;
        font-weight: bold !important;
        text-align: left !important;
        width:auto;
        padding:0 10px;
        border-bottom:none;
    }
</style>

@section("content")
    <header style="width: 100%; height: auto; opacity:0.7;">
        <div class="col-md-12 text-center">
            <div class="col-md-3"></div>
            <div class="col-md-6"  style="margin-top:5px;">
                {{--{!!trans('messages.logo_title')!!}--}}
                {!! Html::image(Session::get('logo'), 'logo', array( 'width' => 70))!!}<br/><br/>
                <h3 class="less-padding">{{Session::get('title')}}</h3><br/>
            </div>
            <div class="col-md-3"></div>
        </div>
        <div class="clearfix"> <br></div>
    </header>

    <div class="col-md-12">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <hr class="top-border"/>
        </div>
        <div class="col-md-1"></div>
    </div>

    <div class="container" style="margin-top:30px;">
        <div class="row">
            <div class="col-md-10 col-md-offset-1 " style="border: 1px solid #8e8686; opacity:0.9; border-radius:8px;">
                <h3 class="text-center">Registration process</h3>
                @if(Session::has('success'))
                    <div class="alert alert-info">
                        {!!Session::get('success') !!}
                        {!! link_to('signup/resend-mail?tmp='.Input::get('tmp'), 'Resend Verification Email', array('class' => 'btn btn-primary btn-sm')) !!}
                    </div>
                @endif
                @if(Session::has('verifyNo'))
                    <div class="alert alert-warning text-center">
                        {{ Session::get('verifyNo') }}
                        {!! link_to('signup/resend-mail?tmp='.Input::get('tmp'), 'Resend Email', array('class' => 'btn btn-primary btn-sm')) !!}
                    </div>
                @endif
                @if(Session::has('verifyYes'))
                    <div class="alert alert-danger text-center">
                        {{ Session::get('verifyYes') }}
                        {!! link_to('forget-password', 'Recover Password', array('class' => 'btn btn-primary btn-sm')) !!}
                    </div>
                @endif
                @if(Session::has('error'))
                    <div class="alert alert-warning">
                        {{ Session::get('error') }}
                    </div>
                @endif
                <hr/>

                <div class="col-md-7 col-sm-7">

                    {!! Form::open(array('url' => '/signup/google/store','method' => 'patch', 'class' => 'form-horizontal', 'id' => 'reg_form',
                    'enctype' =>'multipart/form-data', 'files' => 'true')) !!}

                    <fieldset>
                        <div class="form-group has-feedback {{ $errors->has('user_full_name') ? 'has-error' : ''}}">
                            <label  class="col-md-5 text-left required-star">Name</label>
                            <div class="col-md-7">
                                {!! Form::text('user_full_name', $users->user_full_name, $attributes = array('class'=>'form-control textOnly required', 'data-rule-maxlength'=>'40',
                                'placeholder'=>'Enter your Name', 'id'=>"user_full_name")) !!}
                                <span class="fa fa-user form-control-feedback"></span>
                                {!! $errors->first('user_full_name','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>

                        <div class="form-group has-feedback {{ $errors->has('user_full_name') ? 'has-error' : ''}}">
                            <label  class="col-md-5 text-left required-star">Email Address</label>
                            <div class="col-md-7">
                                {!! Form::text('email', $users->user_email, $attributes = array('class'=>'form-control','readonly', 'data-rule-maxlength'=>'40',
                                'placeholder'=>'Enter your Name', 'id'=>"user_full_name",'style'=>"cursor:not-allowed")) !!}
                                <span class="fa fa-envelope form-control-feedback"></span>
                                {!! $errors->first('user_full_name','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>

                        <div class="form-group has-feedback {{ $errors->has('user_type') ? 'has-error' : ''}}">
                            <label  class="col-md-5 text-left required-star"> User Type</label>
                            <div class="col-md-7">
                                {!! Form::select('user_type', $user_types, $users->user_type, $attributes = array('class'=>'form-control required',  'data-rule-maxlength'=>'40',
                                'id'=>"user_type")) !!}
                                {!! $errors->first('user_type','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>


                        <div class="form-group has-feedback {{ $errors->has('nationality') ? 'has-error' : ''}}">
                            <label  class="col-md-5 text-left required-star"> Nationality</label>
                            <div class="col-md-7">
                                {!! Form::select('nationality', $nationalities, '', $attributes = array('class'=>'form-control required',  'data-rule-maxlength'=>'40',
                                'placeholder' => 'Select One', 'id'=>"nationality")) !!}
                                {!! $errors->first('nationality','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>

                        <div class="form-group has-feedback {{$errors->has('user_DOB') ? 'has-error' : ''}}">
                            {!! Form::label('user_DOB','Date of Birth',['class'=>'col-md-5 text-left required-star']) !!}
                            <div class="col-md-7">
                                <div class="datepicker input-group date" data-date="12-03-2015" data-date-format="dd-mm-yyyy">
                                    {!! Form::text('user_DOB', '', ['class'=>'form-control required', 'placeholder' => 'Pick from Calendar', 'data-rule-maxlength'=>'40']) !!}
                                    <span class="input-group-addon">
                                    <span class="fa fa-calendar"></span>
                                </span>
                                    {!! $errors->first('user_DOB','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group has-feedback {{$errors->has('identity_type') ? 'has-error': ''}}">
                            {!! Form::label('identity_type','Identification Type :',['class'=>'text-left col-md-5', 'id' => 'identity_type_label']) !!}
                            <div class="col-md-7">
                                <label class="identity_hover">{!! Form::radio('identity_type', '1', 'false', ['class'=>'identity_type']) !!} Passport</label>
                                &nbsp;&nbsp;
                                <label class="identity_hover">{!! Form::radio('identity_type', '2', 'false', ['class'=>'identity_type']) !!} National ID</label>
                            </div>
                        </div>

                        <div class="form-group has-feedback {{ $errors->has('passport_no') ? 'has-error' : ''}} hidden" id="passport_div">
                            <label  class="col-md-5 text-left required-star">Passport No.</label>
                            <div class="col-md-7">
                                {!! Form::text('passport_no', null, $attributes = array('class'=>'form-control',  'data-rule-maxlength'=>'40',
                                'placeholder'=>'Enter your Passport No.', 'id'=>"passport_no")) !!}
                                <span class="fa fa-book form-control-feedback"></span>
                                {!! $errors->first('passport_no','<span class="help-block">:message</span>') !!}
                                <p class="text-danger pss-error"></p>
                            </div>
                        </div>

                        <div class="form-group has-feedback {{ $errors->has('user_nid') ? 'has-error' : ''}} hidden" id="nid_div">
                            <label  class="col-md-5 text- required-star">National ID No.</label>
                            <div class="col-md-7">
                                {!! Form::text('user_nid', null, $attributes = array('class'=>'form-control onlyNumber',  'data-rule-maxlength'=>'40',
                                'placeholder'=>'Enter your NID No.', 'id'=>"user_nid")) !!}
                                <span class="fa fa-credit-card form-control-feedback"></span>
                                {!! $errors->first('user_nid','<span class="help-block">:message</span>') !!}
                                <p class="text-danger pss-error"></p>
                            </div>
                        </div>






                        {{--<div class="form-group">--}}
                            {{--<label  class="col-md-10 text-left"><h4>Correspondent Address & Contact Details:</h4></label>--}}
                        {{--</div>--}}
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border text-left">Correspondent Address & Contact Details:</legend>

                        <div class="form-group has-feedback {{ $errors->has('country') ? 'has-error' : ''}}">
                            {!! Form::hidden('location_lat', '', $attributes = array('id'=>"location_lat")) !!}
                            {!! Form::hidden('location_lon', '', $attributes = array('id'=>"location_lon")) !!}
                            <label  class="col-md-5 text-left required-star">Country </label>
                            <div class="col-md-7">
                                {!! Form::select('country', $countries, null, $attributes = array('class'=>'form-control required', 'data-rule-maxlength'=>'40',
                                'placeholder' => 'Select One', 'id'=>"country")) !!}
                                {!! $errors->first('country','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>

                        <div class="form-group has-feedback hidden {{ $errors->has('division') ? 'has-error' : ''}}" id="division_div">
                            <label  class="col-md-5 text-left required-star">Division </label>
                            <div class="col-md-7">
                                {!! Form::select('division', $divisions, '', $attributes = array('class'=>'form-control',
                                'placeholder' => 'Select One', 'id'=>"division")) !!}
                                {!! $errors->first('division','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>

                        <div class="form-group has-feedback {{ $errors->has('state') ? 'has-error' : ''}}" id="state_div">
                            <label  class="col-md-5 text-left required-star"> City </label>
                            <div class="col-md-7">
                                {!! Form::text('state', '', $attributes = array('class'=>'form-control', 'placeholder' => 'Name of your state / division',
                                'data-rule-maxlength'=>'40', 'id'=>"state")) !!}
                                <span class="fa fa-map-marker form-control-feedback"></span>
                                {!! $errors->first('state','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>

                        <div class="form-group has-feedback hidden {{ $errors->has('district') ? 'has-error' : ''}}" id="district_div">
                            <label  class="col-md-5 text-left required-star"> District </label>
                            <div class="col-md-7">
                                {!! Form::select('district', $districts, '', $attributes = array('class'=>'form-control', 'placeholder' => 'Select Division First',
                                'data-rule-maxlength'=>'40','id'=>"district")) !!}
                                {!! $errors->first('district','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>

                        <div class="form-group has-feedback {{ $errors->has('province') ? 'has-error' : ''}}" id="province_div">
                            <label  class="col-md-5 text-left required-star"> State / Province </label>
                            <div class="col-md-7">
                                {!! Form::text('province', '', $attributes = array('class'=>'form-control', 'data-rule-maxlength'=>'40',
                                'placeholder' => 'Enter your Province', 'id'=>"province")) !!}
                                <span class="fa fa-map-marker form-control-feedback"></span>
                                {!! $errors->first('province','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>

                        <div class="form-group has-feedback {{ $errors->has('road_no') ? 'has-error' : ''}}">
                            <label  class="col-md-5 text-left required-star"> Address Line 1</label>
                            <div class="col-md-7">
                                {!! Form::text('road_no', '', $attributes = array('class'=>'form-control bnEng required', 'data-rule-maxlength'=>'100',
                                'placeholder' => 'Enter Road / Street Name /  No.', 'id'=>"road_no")) !!}
                                <span class="fa fa-road  form-control-feedback"></span>
                                {!! $errors->first('road_no','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>

                        <div class="form-group has-feedback {{ $errors->has('user_phone') ? 'has-error' : ''}}">
                            <label  class="col-md-5 text-left required-star">Mobile Number</label>
                            <div class="col-md-7">
                                {!! Form::text('user_phone', null, $attributes = array('class'=>'form-control onlyNumber phone required',
                                'maxlength'=>"20", 'data-rule-maxlength'=>'40', 'placeholder'=>'Enter your Mobile Number','id'=>"user_phone")) !!}
                                <span class="text-danger mobile_number_error"></span>
                                <span class="fa fa-phone form-control-feedback"></span>
                                {!! $errors->first('user_phone','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                        </fieldset>


                        @if(($users->user_type != '1x101') || ($users->user_type != '18x882'))
                            <div class="form-group has-feedback {{$errors->has('company_type') ? 'has-error': ''}}">
                                {!! Form::label('company_type','Company Types :',['class'=>'text-left col-md-5', 'id' => 'company_type_label']) !!}
                                <div class="col-md-7">
                                    <label>{!! Form::radio('company_type', '1', true, ['class'=>'company_type']) !!} Existing company</label>
                                    &nbsp;&nbsp;
                                    <label>{!! Form::radio('company_type', '2',false, ['class'=>'company_type']) !!} New Company</label>
                                </div>
                            </div>

                            <div class="form-group has-feedback {{ $errors->has('agency_id') ? 'has-error' : ''}}
                            {{Input::old('company_type') == 2 ? 'hidden' : ''}}" id="exist_company_div">
                                <div class="col-md-7 col-md-offset-5">
                                    {!! Form::select('company_id', $company_list, '', ['class'=>'form-control required', 'id'=>"company_id"]) !!}
                                    <p class="empty-message"></p>
                                    {!! $errors->first('company_id','<p class="text-danger pss-error">:message</p>') !!}
                                </div>
                            </div>

                            <div class="form-group has-feedback" id="companyInfo">
                                <label  class="col-lg-5 text-left"></label>
                                <div class="col-lg-7">
                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border">Company Info</legend>

                                        <div class="control-group has-feedback{{ $errors->has('company_name') ? 'has-error' : ''}}>
                                            {{Input::old('company_type') == 1 ? 'hidden' : ''}} hidden" id="new_company">
                                            {!! Form::text('company_name', null, $attributes = array('class'=>'form-control required textOnly company',  'data-rule-maxlength'=>'150',
                                        'placeholder'=>'Company Name', 'id'=>"company_name")) !!}
                                            {!! $errors->first('company_name','<p class="text-danger pss-error">:message</p>') !!}
                                        </div><br>

                                        <div class="control-group has-feedback {{ $errors->has('company_division') ? 'has-error' : ''}}">
                                            {!! Form::select('company_division', $divisions, null, $attributes = array('class'=>'form-control required',
                                               'id'=>"company_division")) !!}
                                            {!! $errors->first('company_division','<p class="text-danger pss-error">:message</p>') !!}
                                        </div><br>
                                        <div class="control-group has-feedback {{ $errors->has('company_district') ? 'has-error' : ''}}">
                                            {!! Form::select('company_district', $districts, '', $attributes = array('class'=>'form-control required', 'placeholder' => 'Select Division first',
                                            'data-rule-maxlength'=>'40','id'=>"company_district")) !!}
                                            {!! $errors->first('company_district','<span class="help-block">:message</span>') !!}
                                        </div><br>
                                        <div class="control-group has-feedback {{ $errors->has('company_thana') ? 'has-error' : ''}}">
                                            {!! Form::select('company_thana', $thana, '', $attributes = array('class'=>'form-control required', 'placeholder' => 'Select division first',
                                            'data-rule-maxlength'=>'50','id'=>"company_thana")) !!}
                                            {!! $errors->first('company_thana','<span class="help-block">:message</span>') !!}
                                        </div>


                                    </fieldset>
                                </div>
                            </div>

                        @endif

                        <div class="form-group has-feedback {{ $errors->has('details') ? 'has-error' : ''}}">
                            <label  class="col-lg-5 text-left"> Additional information regarding this registration  (If any)</label>
                            <div class="col-lg-7">
                                {!! Form::textarea('details', $value = null, $attributes = array('size' => '30x4','class'=>'form-control',
                                'placeholder'=>'Details Information','id'=>"details", 'data-rule-maxlength'=>'255')) !!}
                                @if($errors->first('details'))
                                    <span  class="control-label">
                                            <em><i class="fa fa-times-circle-o"></i> {{ $errors->first('details','') }}</em>
                                        </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label>
                                {!! Form::checkbox('user_agreement', 1, null,  ['class'=>'required']) !!}
                                &nbsp;
                                I have read and agree to terms and conditions.
                            </label>
                        </div>

                        <div class="form-group">
                            <div class="col-md-3 col-md-offset-5">
                                <button type="submit" class="btn btn-block btn-primary"><b>Submit</b></button>
                            </div>
                        </div>

                    </fieldset>
                    <div class="clearfix"></div>
                <hr>

                </div>

                <div class="col-md-5">
                    <h3>Terms of Usage of BIDA OSS</h3>
                    Terms and conditions to use this system can be briefed as -
                    <ul>
                        <li>You must follow any policies made available to you within the Services.</li>
                        <li>You have to fill all the given fields with correct information and take responsibility if any wrong or misleading information has been given</li>
                        <li>You are responsible for the activity that happens on or through your account. So, keep your password confidential.</li>
                        <li>We may modify these terms or any additional terms that apply to a Service to, for example,
                            reflect changes to the law or changes to our Services. You should look at the terms regularly.</li>
                    </ul>
                </div>

@endsection

<!--<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>-->

@section('footer-script')
    <!--<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>-->
    <!-- Location picker -->

    <script type="text/javascript" src='https://maps.google.com/maps/api/js?key={{env('GOOGLE_MAP')}}&libraries=places'></script>
    <script src="{{ asset("assets/locationpicker/locationpicker.jquery.js") }}"  type="text/javascript"></script>
    {{--    <script src="{{ asset("assets/scripts/custom.js") }}"  type="text/javascript"></script>--}}

    <script>
        function updateControls(addressComponents) {
            $('#house_no').val(addressComponents.addressLine1);
            $('#state').val(addressComponents.city);
            $('#province').val(addressComponents.stateOrProvince);
            $('#post_code').val(addressComponents.postalCode);
            $("#country option[value=" + addressComponents.country +"]").attr("selected","selected");
        }



        $('#locationpicker').locationpicker({
            location: {latitude: 23.80925758974614, longitude: 90.41546648789063},
            radius: 0,
            inputBinding: {
                latitudeInput: $('#location_lat'),
                longitudeInput: $('#location_lon'),
                locationNameInput: $('#road_no')
            },
            enableAutocomplete: true,
            onchanged: function (currentLocation, radius, isMarkerDropped) {
                var addressComponents = $(this).locationpicker('map').location.addressComponents;
                updateControls(addressComponents);
                //var mapContext = $(this).locationpicker('map');
                //mapContext.map.setZoom(14);
            },
            oninitialized: function (component) {
                var addressComponents = $(component).locationpicker('map').location.addressComponents;
                updateControls(addressComponents);
                var mapContext = $(component).locationpicker('map');
                mapContext.map.setZoom(16);
            }
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function () {
            $(function () {
                var _token = $('input[name="_token"]').val();
                $("#reg_form").validate({
                    errorPlacement: function () {
                        return false;
                    }
                });
            });
        });

        $(document).ready(function () {
            var today = new Date();
            var yyyy = today.getFullYear();
            $('.datepicker').datetimepicker({
                viewMode: 'years',
                format: 'DD-MMM-YYYY',
                maxDate: (new Date()),
                minDate: '01/01/' + (yyyy - 60)
            });



            $('.identity_type').click(function (e) {
                if (this.value == '1') { // 1 is for passport
                    $('#passport_div').removeClass('hidden');
                    $('#passport_no').addClass('required');
                    $('#nid_div').addClass('hidden');
                    $('#user_nid').removeClass('required');
                    $('#user_nid').val('');
                }
                else { // 2 is for NID
                    $('#passport_div').addClass('hidden');
                    $('#passport_no').removeClass('required');
                    $('#passport_no').val('');
                    $('#nid_div').removeClass('hidden');
                    $('#user_nid').addClass('required');
                }
            });
            $('#identity_type').trigger('click');
        });

            $(document).ready(function () {

                $('#country').change(function (e) {
                    if (this.value == 'BD') { // 001 is Bangladesh
                        $('#division_div').removeClass('hidden');
                        $('#division').addClass('required');
                        $('#district_div').removeClass('hidden');
                        $('#district').addClass('required');
                        $('#state_div').addClass('hidden');
                        $('#state').removeClass('required');
                        $('#province_div').addClass('hidden');
                        $('#province').removeClass('required');
                    } else {
                        $('#state_div').removeClass('hidden');
                        $('#state').addClass('required');
                        $('#province_div').removeClass('hidden');
                        $('#province').addClass('required');
                        $('#division_div').addClass('hidden');
                        $('#division').removeClass('required');
                        $('#district_div').addClass('hidden');
                        $('#district').removeClass('required');
                    }
                });
                $('#country').trigger('change');

                $("#division").change(function () {
                    var divisionId = $('#division').val();
                    $(this).after('<span class="loading_data">Loading...</span>');
                    var self = $(this);
                    $.ajax({
                        type: "GET",
                        url: "<?php echo url(); ?>/users/get-district-by-division",
                        data: {
                            divisionId: divisionId
                        },
                        success: function (response) {
                            var option = '<option value="">Select district</option>';
                            if (response.responseCode == 1) {
                                $.each(response.data, function (id, value) {
                                    option += '<option value="' + id + '">' + value + '</option>';
                                });
                            }
                            $("#district").html(option);
                            $(self).next().hide();
                        }
                    });
                });

                $("#district").change(function () {
                    var districtId = $('#district').val();
                    $(this).after('<span class="loading_data">Loading...</span>');
                    var self = $(this);
                    $.ajax({
                        type: "GET",
                        url: "<?php echo url(); ?>/users/get-thana-by-district-id",
                        data: {
                            districtId: districtId
                        },
                        success: function (response) {
                            var option = '<option value="">Select Thana</option>';
                            if (response.responseCode == 1) {
                                $.each(response.data, function (id, value) {
                                    option += '<option value="' + id + '">' + value + '</option>';
                                });
                            }
                            $("#thana").html(option);
                            $(self).next().hide();
                        }
                    });
                });


                ////new
                $("#company_division").change(function () {
                    var divisionId = $('#company_division').val();
                    $(this).after('<span class="loading_data">Loading...</span>');
                    var self = $(this);
                    $.ajax({
                        type: "GET",
                        url: "<?php echo url(); ?>/users/get-district-by-division",
                        data: {
                            divisionId: divisionId
                        },
                        success: function (response) {
                            var option = '<option value="">Select district</option>';
                            if (response.responseCode == 1) {
                                $.each(response.data, function (id, value) {
                                    option += '<option value="' + id + '">' + value + '</option>';
                                });
                            }
                            $("#company_district").html(option);
                            $(self).next().hide();
                        }
                    });
                });
                $("#districtCompany").change(function () {
                    var districtId = $('#districtCompany').val();
                    $(this).after('<span class="loading_data">Loading...</span>');
                    var self = $(this);
                    $.ajax({
                        type: "GET",
                        url: "<?php echo url(); ?>/users/get-thana-by-district-id",
                        data: {
                            districtId: districtId
                        },
                        success: function (response) {
                            var option = '<option value="">Select Thana</option>';
                            if (response.responseCode == 1) {
                                $.each(response.data, function (id, value) {
                                    option += '<option value="' + id + '">' + value + '</option>';
                                });
                            }
                            $("#company_thana").html(option);
                            $(self).next().hide();
                        }
                    });
                });


                $("#company_district").change(function () {
                    $.ajax({
                        type: "GET",
                        url: "<?php echo url(); ?>/users/get-thana-by-district-id",
                        data: {
                            districtId: districtId
                        },
                        success: function (response) {
                            var option = '<option value="">Select Thana</option>';
                            if (response.responseCode == 1) {
                                $.each(response.data, function (id, value) {
                                    option += '<option value="' + id + '">' + value + '</option>';
                                });
                            }
                        }
                    });
                });


            });

            @if (Input::old('company_type') == 2)
            $('#new_company').removeClass('hidden');
            $('#companyInfo').removeClass('hidden');

            @endif

            $('.company_type').click(function (e) {
//                alert(this.value);

                if (this.value == '1') { // 1 for old
                    $('#exist_company_div').removeClass('hidden');
                    $('#companyInfo').addClass('hidden');
                    $('#exist_company_div').addClass('required');
                    $('#new_company').addClass('hidden');
                    $('#new_company_error').removeClass('required');
                    $('#company_name').removeClass('error');
                    $('#company_name').removeClass('required');
                    $('#company_id').addClass('required');
                }
                else { // 2 is for new
                    $('#exist_company_div').addClass('hidden');
                    $('#companyInfo').removeClass('hidden');
                    $('#exist_company_div').removeClass('required');
                    $('#new_company').removeClass('hidden');
                    $('.company').addClass('required');
                    $('#new_company_error').addClass('required');
                    $('#company_id').removeClass('error');
                    $('#company_id').removeClass('required');
                }
            });
            $('.company_type').trigger('click');
        </script>
    @endsection
