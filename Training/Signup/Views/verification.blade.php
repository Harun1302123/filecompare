@extends('layouts.front')

@section("content")
    <style>
        fieldset.scheduler-border {
            border: 1px groove #ddd !important;
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
    <div class="container">
        <div class="row">
            @include('partials.messages')
            <div class="col-md-10 col-md-offset-1" style="background: #ABD6AC; opacity:0.88; border-radius:8px;">
                <div class="panel panel-success" style="margin: 10px 0px;">
                    <div class="panel-heading text-success">
                        <h3 class="text-center">Verify Your Account </h3>
                    </div>
                    <div class="panel-body">
                        {!! Form::open(array('url' => '/signup/verification_store/'.$confirmationCode,'method' => 'patch', 'class' => 'form-horizontal',
                        'id'=> 'vreg_form')) !!}

                        <div class="col-md-12">
                            <h4>Terms of Usage of BIDA OSS</h4>
                            Terms and conditions to use this system can be briefed as -
                            <ul>
                                <li>You must follow any policies made available to you within the Services.</li>
                                <li>You have to fill all the given fields with correct information and take responsibility if any wrong or misleading information has been given</li>
                                <li>You are responsible for the activity that happens on or through your account. So, keep your password confidential.</li>
                                <li>We may modify these terms or any additional terms that apply to a Service to, for example,
                                    reflect changes to the law or changes to our Services. You should look at the terms regularly.</li>
                            </ul>
                        </div>

                        <div class="col-md-12">
                            <fieldset>
                                <div class="col-md-12"><br/></div>
                                @if(($user_type != '1x101') || ($user_type != '18x882'))
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

                                    {{--<div class="form-group has-feedback {{ $errors->has('company_type') ? 'has-error' : ''}}--}}
                                    {{--{{Input::old('company_type') == 1 ? 'hidden' : ''}} hidden" id="new_company">--}}
                                    {{--<div class="col-md-6 col-md-offset-6">--}}
                                    {{--{!! Form::text('company_name', null, $attributes = array('class'=>'form-control required textOnly company',  'data-rule-maxlength'=>'150',--}}
                                    {{--'placeholder'=>'Company Name', 'id'=>"company_name")) !!}--}}
                                    {{--{!! $errors->first('company_name','<p class="text-danger pss-error">:message</p>') !!}--}}
                                    {{--</div>--}}
                                    {{--</div>--}}


                                    <div class="form-group has-feedback" id="companyInfo">
                                        <label  class="col-lg-5 text-left"></label>
                                        <div class="col-lg-7">
                                            <fieldset class="scheduler-border">
                                                <legend class="scheduler-border">Company Info</legend>

                                                <div class="control-group has-feedback{{ $errors->has('company_name') ? 'has-error' : ''}}>
                                            {{Input::old('company_type') == 1 ? 'hidden' : ''}} hidden" id="new_company">
                                                    {!! Form::text('company_name', null, $attributes = array('class'=>'form-control required textOnly company',  'data-rule-maxlength'=>'255',
                                                'placeholder'=>'Company Name', 'id'=>"company_name")) !!}
                                                    {!! $errors->first('company_name','<p class="text-danger pss-error">:message</p>') !!}
                                                </div><br>

                                                <div class="control-group has-feedback {{ $errors->has('division') ? 'has-error' : ''}}">
                                                    {!! Form::select('division', $divisions, null, $attributes = array('class'=>'form-control required',
                                                       'id'=>"division")) !!}
                                                    {!! $errors->first('division','<p class="text-danger pss-error">:message</p>') !!}
                                                </div><br>
                                                <div class="control-group has-feedback {{ $errors->has('district') ? 'has-error' : ''}}">
                                                    {!! Form::select('district', $districts, '', $attributes = array('class'=>'form-control required', 'placeholder' => 'Select Division first',
                                                    'data-rule-maxlength'=>'40','id'=>"district")) !!}
                                                    {!! $errors->first('district','<span class="help-block">:message</span>') !!}
                                                </div><br>
                                                <div class="control-group has-feedback {{ $errors->has('thana') ? 'has-error' : ''}}">
                                                    {!! Form::select('thana', $thana, '', $attributes = array('class'=>'form-control required', 'placeholder' => 'Select division first',
                                                    'data-rule-maxlength'=>'50','id'=>"thana")) !!}
                                                    {!! $errors->first('thana','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    {{--<div class="form-group has-feedback {{$errors->has('member_type') ? 'has-error': ''}}">--}}
                                    {{--{!! Form::label('member_type','Member Types :',['class'=>'text-left col-md-5', 'id' => 'company_type_label']) !!}--}}
                                    {{--<div class="col-md-3">--}}
                                    {{--{!! Form::select('member_type', ['1'=>'General Member','2'=>'Associated Member','3'=>'Other'], '', $attributes = array('class'=>'form-control required', 'placeholder' => 'Select A Type',--}}
                                    {{--'data-rule-maxlength'=>'40','id'=>"members")) !!}--}}
                                    {{--{!! $errors->first('member_type','<span class="help-block">:message</span>') !!}--}}
                                    {{--</div>--}}
                                    {{--<div class="col-md-1">--}}
                                    {{--<span id="" class="pull-right membership_short_form" style="margin-top: 5px;"></span>--}}
                                    {{--<input type="text" hidden name="member_type_short_name"  class="membership_short_form_input">--}}
                                    {{--</div>--}}
                                    {{--<div class="col-md-3">--}}
                                    {{--{!! Form::text('membership_no', null, $attributes = array('class'=>'form-control required onlyNumber',  'data-rule-maxlength'=>'20',--}}
                                    {{--'placeholder'=>'Membership No', 'id'=>"membership_no")) !!}--}}
                                    {{--{!! $errors->first('membership_no','<span class="help-block">:message</span>') !!}--}}
                                    {{--</div>--}}
                                    {{--</div>--}}
                                @endif

                                <div class="form-group has-feedback {{ $errors->has('details') ? 'has-error' : ''}}">
                                    <label  class="col-lg-5 text-left"> Additional information regarding this registration  (If any)</label>
                                    <div class="col-lg-7">
                                        {!! Form::textarea('details', $value = null, $attributes = array('size' => '30x2','class'=>'form-control',
                                        'placeholder'=>'Details Information','id'=>"details", 'data-rule-maxlength'=>'255')) !!}
                                        @if($errors->first('details'))
                                            <span  class="control-label">
                                            <em><i class="fa fa-times-circle-o"></i> {{ $errors->first('details','') }}</em>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group has-feedback {{ $errors->has('user_agreement') ? 'has-error' : ''}}">
                                    <div class="col-md-12">
                                        <label>
                                            {!! Form::checkbox('user_agreement', 1, null,  ['class'=>'required']) !!}
                                            &nbsp;
                                            I have read and agree to terms and conditions.
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-5 col-md-offset-7">
                                        <div class="text-right">
                                            <button type="submit" class="btn  btn-block btn-primary btn-large"><b>Save and Continue</b></button>
                                        </div>
                                        <div class="text-center">
                                            Already have an account? <b>{!! link_to('users/login', 'Login', array('class' => '')) !!}</b>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </fieldset>
                        </div>
                        {!! Form::close() !!}
                        <div class="clearfix"></div>
                    </div>
                </div><!--/panel-->
            </div>
        </div>
        @endsection

        @section('footer-script')
            <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.devbridge-autocomplete/1.2.24/jquery.autocomplete.min.js"></script>
            <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
            <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
            <script>
                $(function () {
                    var _token = $('input[name="_token"]').val();
                    $("#vreg_form").validate({
                        errorPlacement: function () {
                            return false;
                        }
                    });
                });

                $(document).ready(function () {
                    $("#members").change(function () {
                        var members_type = $(this).val();
                        if(members_type == 1){
                            $('.membership_short_form').html("G");
                            $('.membership_short_form_input').val("G");
                        }else if(members_type == 2){
                            $('.membership_short_form').html("A");
                            $('.membership_short_form_input').val("A");
                        }else if(members_type == 3){
                            $('.membership_short_form').html("O");
                            $('.membership_short_form_input').val("O");
                        }
                    });
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
                                var option = '<option value="">Select thana</option>';
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
                });

                @if (Input::old('company_type') == 2)
                $('#new_company').removeClass('hidden');
                $('#companyInfo').removeClass('hidden');
                @endif

                $('.company_type').click(function (e) {
//    alert(this.value);
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
            <style>
                input[type="checkbox"].error{
                    outline: 1px solid red !important;
                }
            </style>

@endsection