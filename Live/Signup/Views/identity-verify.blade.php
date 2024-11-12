@extends('layouts.front')

@section('style')
    <link rel="stylesheet" href="{{ asset('vendor/cropperjs_v1.5.7/cropper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/signup/identity_verify_v001.min.css') }}">
    <style>
        .font-bold {
            font-weight: 700;
        }
    </style>
@endsection

@section("content")

    <!-- NID data verification Modal -->
    <div class="modal fade" id="NIDVerifyModal" role="dialog" aria-labelledby="NIDVerifyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="NIDVerifyModalTitle">
                        <div class="pull-left">
                            <img src="{{ url('/assets/images/ec.png') }}" width="40px" height="40px" alt="election commision"/>
                            <span class="text-success">National Identity Verification</span>
                        </div>
                        <div class="clearfix"></div>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="errorMsgNID alert alert-danger alert-dismissible hidden">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    </div>
                    <div class="successMsgNID alert alert-success alert-dismissible hidden">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    </div>

                    <!-- NID Verification response div -->
                    <div class="alert alert-info" id="NIDVerificationResponse"></div>

                    <!-- Total spent time to NID verification --> <!-- old nid zaman vai -->
{{--                    <div id="NIDVerificationTimeCounting" class="text-success">--}}
{{--                        Waiting for the connection to the national ID server, <span id="NIDVerifyTimeSpent">0</span>--}}
{{--                        seconds--}}
{{--                        passed.--}}
{{--                    </div>--}}

                    <div id="NIDVerificationTimeCounting" class="text-success"></div>

                    <!-- NID data show after verification -->
                    <div id="VerifiedNIDInfo" class="clearfix" hidden>
                        <div class="col-md-12">
                            <div class="row">
                                {{-- <div class="col-sm-4 text-center">
                                    <img id="nid_image" class="img-circle nidPhoto" src="" width="100px" height="100px">
                                </div> --}}
                                
                                <div class="col-sm-3"></div>
                                <div class="col-sm-8">
                                    <br/>
                                    <form>
                                        <div class="row">
                                            <div class="col-md-4 font-bold">NID</div>
                                            <div class="col-md-1">:</div>
                                            <div class="col-md-7"><span id="nid_number"></span></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 font-bold">Date of Birth</div>
                                            <div class="col-md-1">:</div>
                                            <div class="col-md-7"><span id="nid_dob"></span></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 font-bold">Name</div>
                                            <div class="col-md-1">:</div>
                                            <div class="col-md-7"><span id="nid_name"></span></div>
                                        </div>
                                        {{-- <div class="row">
                                            <div class="col-md-4 font-bold">Post Code</div>
                                            <div class="col-md-1">:</div>
                                            <div class="col-md-7"><span id="nid_postcode"></span></div>
                                        </div> --}}
                                    </form>
                                    <br/>
                                </div>
                                <div class="col-sm-2"></div>
                            </div>
                        </div>
                    </div>
                    <!-- End NID data show after verification -->
                </div>
                <div class="modal-footer" id="NIDVerifyModalFooter">
                    <div class="pull-left">
                        {!! Form::button('<i class="fa fa-times"></i> Close', array('type' => 'button', 'class' => 'btn btn-danger round-btn', 'data-dismiss' => 'modal')) !!}
                    </div>
                    <div class="pull-right">
                        <button class="btn btn-success round-btn hidden" id="SaveContinueBtn"
                                onclick="submitIdentityVerifyForm('identityVerifyForm')">
                            <i class="fa fa-save"></i> Save & continue
                        </button>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <!-- End NID data verification Modal -->


    <!-- ETIN data verification Modal -->
    <div class="modal fade" id="ETINVerifyModal" role="dialog" aria-labelledby="ETINVerifyModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="ETINVerifyModalTitle">
                        <div class="pull-left">
                            <img src="{{ url('/assets/images/nbrlogo.jpg') }}" width="180px" height="35px"
                                 alt="election commision"/>
                        </div>
                        <div class="pull-right">
                            <span class="text-success">ETIN Verification</span>
                        </div>
                        <div class="clearfix"></div>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="errorMsgTIN alert alert-danger alert-dismissible hidden">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    </div>
                    <div class="successMsgTIN alert alert-success alert-dismissible hidden">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    </div>

                    <div class="alert alert-info" id="ETINResponseCountMsg"></div>

                    <div id="ETINVerifySuccessMsg" class="text-danger"></div>

                    <!-- ETIN data show after verification -->
                    <div id="VerifiedETINInfo" class="clearfix" hidden>
                        <div class="col-md-10 col-md-offset-1">
                            <div class="row">
                                <div class="col-sm-12">
                                    <form>
                                        <div class="form-group">
                                            <label>Name:</label>
                                            <span id="etin_name"></span>
                                        </div>
                                        <div class="form-group">
                                            <label>Father's Name:</label>
                                            <span id="etin_father_name"></span>
                                        </div>
                                        <div class="form-group">
                                            <label>Date of Birth:</label>
                                            <span id="etin_dob"></span>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End ETIN data show after verification -->
                </div>
                <div class="modal-footer" id="ETINVerifyModalFooter">
                    <div class="pull-left">
                        {!! Form::button('<i class="fa fa-times"></i> Close', array('type' => 'button', 'class' => 'btn btn-danger round-btn', 'data-dismiss' => 'modal')) !!}
                    </div>
                    <div class="pull-right">
                        <button class="btn btn-success round-btn hidden" id="etinSaveContinueBtn"
                                onclick="submitIdentityVerifyForm('identityVerifyForm')">
                            <i class="fa fa-save"></i> Save & continue
                        </button>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <!-- End ETIN data verification Modal -->

    <!-- Passport error modal -->
    <div class="modal fade" id="PassportErrorModal" role="dialog" aria-labelledby="PassportErrorModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h4 class="modal-title text-danger" id="PassportErrorModalTitle">
                        <strong>Oh snap!</strong> Your passport verification failed.
                    </h4>
                </div>
                <div class="modal-body">
                    <div style="padding: 20px; text-align: center;">
                        <button type="button" id="passport_error_retry" class="btn btn-primary btn-outline">Retry
                        </button>
                        <p style="margin: 15px 0">OR</p>
                        <button type="button" id="passport_error_manual" class="btn btn-warning btn-outline">Manually
                            Input
                        </button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <!-- End Passport error modal -->


    <!-- Previous Verification data -->
    @if(!empty($getPreviousVerificationData) && !empty($previous_info))
        <div id="previousVerificationDataModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <p class="text-success"><i>You tried previously to register by verifying <span class="text-uppercase">{{ $getPreviousVerificationData->identity_type }}</span>.</i></p>
                        <h4 class="modal-title">Do you want to continue with the previous verification information?</h4>
                    </div>
                    {!! Form::open(array('url' => 'signup/identity-verify-previous/' . \App\Libraries\Encryption::encodeId($getPreviousVerificationData->id) ,'method' => 'post', 'class' => 'form-horizontal','enctype' =>'multipart/form-data', 'files' => 'true')) !!}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-10 col-md-offset-1">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="col-sm-3">
                                                <span class="v_label">Nationality Type</span>
                                                <span class="pull-right">&#58;</span>
                                            </label>
                                            <span class="col-sm-9">{{ ucfirst($getPreviousVerificationData->nationality_type) }}</span>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3">
                                                <span class="v_label">Identity Type</span>
                                                <span class="pull-right">&#58;</span>
                                            </label>
                                            <span class="col-sm-9">{{ ucfirst($getPreviousVerificationData->identity_type) }}</span>
                                        </div>

                                        @if($getPreviousVerificationData->identity_type == 'tin')
                                            <div class="form-group">
                                                <label class="col-sm-3">
                                                    <span class="v_label">TIN Number</span>
                                                    <span class="pull-right">&#58;</span>
                                                </label>
                                                <span class="col-sm-9">{{ $previous_info['etin_number'] }}</span>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3">
                                                    <span class="v_label">Name</span>
                                                    <span class="pull-right">&#58;</span>
                                                </label>
                                                <span class="col-sm-9">{{ $previous_info['assesName'] }}</span>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3">
                                                    <span class="v_label">Father's Name</span>
                                                    <span class="pull-right">&#58;</span>
                                                </label>
                                                <span class="col-sm-9">{{ $previous_info['fathersName'] }}</span>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3">
                                                    <span class="v_label">Date of Birth</span>
                                                    <span class="pull-right">&#58;</span>
                                                </label>
                                                <span class="col-sm-9">{{ date('d-M-Y', strtotime($previous_info['dob'])) }}</span>
                                            </div>

                                            
                                        @elseif($getPreviousVerificationData->identity_type == 'nid')
                                            <div class="form-group">
                                                <label class="col-sm-3">
                                                    <span class="v_label">NID Number</span>
                                                    <span class="pull-right">&#58;</span>
                                                </label>
                                                {{-- <span class="col-sm-9">{{ $previous_info['return']['nid'] }}</span> --}}
                                                <span class="col-sm-9">{{ $previous_info['nationalId'] }}</span>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3">
                                                    <span class="v_label">Name</span>
                                                    <span class="pull-right">&#58;</span>
                                                </label>
                                                {{-- <span class="col-sm-9">{{ $previous_info['return']['voterInfo']['voterInfo']['nameEnglish'] }}</span> --}}
                                                <span class="col-sm-9">{{ $previous_info['nameEn'] }}</span>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3">
                                                    <span class="v_label">Date of Birth</span>
                                                    <span class="pull-right">&#58;</span>
                                                </label>
                                                {{-- <span class="col-sm-9">{{ date('d-M-Y', strtotime($previous_info['return']['voterInfo']['voterInfo']['dateOfBirth'])) }}</span> --}}
                                                <span class="col-sm-9">{{ date('d-M-Y', strtotime($previous_info['dateOfBirth'])) }}</span>
                                            </div>
                                        @elseif($getPreviousVerificationData->identity_type == 'passport')
                                            <div class="form-group">
                                                <label class="col-sm-3">
                                                    <span class="v_label">Passport type</span>
                                                    <span class="pull-right">&#58;</span>
                                                </label>
                                                <span class="col-sm-9">{{ $previous_info['passport_type'] }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3">
                                                    <span class="v_label">Passport No.</span>
                                                    <span class="pull-right">&#58;</span>
                                                </label>
                                                <span class="col-sm-9">{{ $previous_info['passport_no'] }}</span>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3">
                                                    <span class="v_label">Surname</span>
                                                    <span class="pull-right">&#58;</span>
                                                </label>
                                                <span class="col-sm-9">{{ $previous_info['passport_surname'] }}</span>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3">
                                                    <span class="v_label">Given Name</span>
                                                    <span class="pull-right">&#58;</span>
                                                </label>
                                                <span class="col-sm-9">{{ $previous_info['passport_given_name'] }}</span>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3">
                                                    <span class="v_label">Personal No.</span>
                                                    <span class="pull-right">&#58;</span>
                                                </label>
                                                <span class="col-sm-9">{{ $previous_info['passport_personal_no'] }}</span>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3">
                                                    <span class="v_label">Date of Birth</span>
                                                    <span class="pull-right">&#58;</span>
                                                </label>
                                                <span class="col-sm-9">{{ date('d-M-Y', strtotime($previous_info['passport_DOB'])) }}</span>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3">
                                                    <span class="v_label">Date of Expiry</span>
                                                    <span class="pull-right">&#58;</span>
                                                </label>
                                                <span class="col-sm-9">{{ date('d-M-Y', strtotime($previous_info['passport_date_of_expire'])) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="pull-left">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Skip</button>
                        </div>
                        <div class="pull-right">
                            <button type="submit" class="btn btn-success">Continue</button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    {!! Form::close() !!}
                </div>

            </div>
        </div>
    @endif


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
                    <strong>Registration process step-2 (For the first time only)</strong>
                </h4>
                <hr/>

                <div class="col-md-10 col-md-offset-1 col-sm-12">
                    {!! Form::open(array('url' => 'signup/identity-verify','method' => 'post', 'class' => 'form-horizontal', 'id' => 'identityVerifyForm', 'name' => 'identityVerifyForm',
                    'enctype' =>'multipart/form-data', 'files' => 'true')) !!}

                    <div class="form-group has-feedback {{$errors->has('nationality_type') ? 'has-error': ''}}">
                        {!! Form::label('nationality_type','Nationality',['class'=>'required-star col-md-4', 'id' => 'nationality']) !!}
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <label class="radio_hover">
                                        {!! Form::radio('nationality_type', 'bangladeshi', false, ['class'=>'required', 'id' => 'nationality_type_bd', 'onclick' => 'setUserNationality(this.value)']) !!}
                                        Bangladeshi
                                    </label>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <label class="radio_hover">
                                        {!! Form::radio('nationality_type', 'foreign', false, ['class'=>'required', 'id' => 'nationality_type_foreign', 'onclick' => 'setUserNationality(this.value)']) !!}
                                        Foreign
                                    </label>
                                </div>
                            </div>
                            {!! $errors->first('nationality_type','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>

                    <div id="bd_nationality_fields"
                         class="form-group has-feedback {{$errors->has('identity_type_bd') ? 'has-error': ''}}" hidden>
                        {!! Form::label('identity_type_bd','Identity',['class'=>'required-star col-md-4', 'id' => 'nationality']) !!}
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <label class="radio_hover">
                                        {!! Form::radio('identity_type_bd', 'nid', false, ['class'=>'required', 'id' => 'identity_type_nid', 'onclick' => 'setUserIdentity(this.value)']) !!}
                                        NID
                                    </label>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <label class="radio_hover">
                                        {!! Form::radio('identity_type_bd', 'tin', false, ['class'=>'required', 'id' => 'identity_type_tin1', 'onclick' => 'setUserIdentity(this.value)']) !!}
                                        TIN (Bangladeshi)
                                    </label>
                                </div>
                            </div>
                            {!! $errors->first('identity_type_bd','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>

                    <div id="foreign_nationality_fields"
                         class="form-group has-feedback {{$errors->has('identity_type_foreign') ? 'has-error': ''}}"
                         hidden>
                        {!! Form::label('identity_type_foreign','Identity',['class'=>'required-star col-md-4', 'id' => 'nationality']) !!}
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <label class="radio_hover">
                                        {!! Form::radio('identity_type_foreign', 'passport', false, ['class'=>'required', 'id' => 'identity_type_passport', 'onclick' => 'setUserIdentity(this.value)']) !!}
                                        Passport
                                    </label>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <label class="radio_hover">
                                        {!! Form::radio('identity_type_foreign', 'tin', false, ['class'=>'required', 'id' => 'identity_type_tin2', 'onclick' => 'setUserIdentity(this.value)']) !!}
                                        TIN (Bangladeshi)
                                    </label>
                                </div>
                            </div>
                            {!! $errors->first('identity_type_foreign','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>

                    {{-- National ID No --}}
                    <div id="nid_field" hidden>
                        <div class="form-group has-feedback {{ $errors->has('user_nid') ? 'has-error' : ''}}">
                            <label for="user_nid" class="col-md-4 text- required-star">National ID No.</label>
                            <div class="col-md-8">
                                {!! Form::text('user_nid', null, $attributes = array('class'=>'form-control bd_nid required input-sm',  'data-rule-maxlength'=>'17',
                                'placeholder'=>'Enter your NID number', 'id'=>"user_nid")) !!}
                                {!! Form::hidden('verified_nid_data', null, $attributes = array('id'=>"verified_nid_data")) !!}
                                <small class="text-danger" style="font-size: 9px; font-weight: bold">You need to enter
                                    13 or 17 digits NID or 10 digits smart card ID.</small>
                                {!! $errors->first('user_nid','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>

                    {{-- TIN Number --}}
                    <div id="etin_number_field"
                         class="form-group has-feedback {{ $errors->has('etin_number') ? 'has-error' : ''}}" hidden>
                        <label for="etin_number" class="col-md-4 required-star">TIN Number</label>
                        <div class="col-md-8">
                            {!! Form::text('etin_number', null, $attributes = array('class'=>'form-control digits input-sm required', 'placeholder'=>'Enter your TIN number',
                            'maxlength'=>"20", 'id'=>"etin_number")) !!}
                            {!! Form::hidden('verified_etin_data', null, $attributes = array('id'=>"verified_etin_data")) !!}
                            {!! $errors->first('etin_number','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>

                    {{-- Date of Birth --}}
                    <div id="user_dob_field"
                         class="form-group has-feedback {{$errors->has('user_DOB') ? 'has-error' : ''}}" hidden>
                        <label for="user_DOB" class="col-md-4 required-star">Date of Birth</label>
                        <div class="col-md-8">
                            <div class="userDP input-group date">
                                {!! Form::text('user_DOB', '', ['class'=>'form-control input-sm required', 'id'=>'user_DOB', 'placeholder' => 'Pick from calendar']) !!}
                                <span class="input-group-addon">
                                        <span class="fa fa-calendar"></span>
                                    </span>
                            </div>
                            {!! $errors->first('user_DOB','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>

                    {{-- National ID No --}}
                    <div id="nid_field_extended" hidden>
                        <div class="form-group has-feedback {{ $errors->has('user_nid_name') ? 'has-error' : ''}}">
                            <label for="user_nid_name" class="col-md-4 required-star">Name(English)</label>
                            <div class="col-md-8">
                                {!! Form::text('user_nid_name', null, $attributes = array('class'=>'form-control required input-sm engOnly',
                                'placeholder'=>'Enter english name of your NID', 'id'=>"user_nid_name")) !!}
                                {!! $errors->first('user_nid_name','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>

                        {{-- <div class="form-group has-feedback {{ $errors->has('user_nid_postal_code') ? 'has-error' : ''}}">
                            <label for="user_nid_postal_code" class="col-md-4 required-star">Post Code</label>
                            <div class="col-md-8">
                                {!! Form::text('user_nid_postal_code', null, $attributes = array('class'=>'form-control required input-sm number',  'data-rule-maxlength'=>'10',
                                'placeholder'=>'Enter post code of your NID', 'id'=>"user_nid_postal_code")) !!}
                                {!! $errors->first('user_nid_postal_code','<span class="help-block">:message</span>') !!}
                            </div>
                        </div> --}}
                    </div>


                    {{-- Passport Information --}}
                    <fieldset id="passport_div" hidden>

                        <div id="passport_upload_wrapper" class="passport-upload-wrapper">
                            <div class="row">
                                <div class="col-md-10 col-md-offset-1 col-sm-12">
                                    <span id="passport_upload_error" class="text-danger text-left"></span>

                                    <div style="text-align: center;" id="passport_upload_div">
                                        <div class="passport-upload">
                                            <div class="passport-upload-message">
                                                <i class="fas fa-cloud-upload-alt fa-3x passport-upload-icon"></i>
                                                <p>
                                                    Drop Your Passport scan copy here or
                                                    <span style="color:#258DFF;">Browse</span>
                                                    <small class="help-block" style="font-size: 9px;">[File Format:
                                                        *.jpg/ .jpeg/ .png | Maximum 5 MB | Width 746 to 3500 pixel |
                                                        Height 1043 to 4500 pixel]</small>
                                                </p>
                                            </div>
                                            <input accept="image/*" type="file" name="passport_upload"
                                                   id="passport_upload" class="passport-upload-input"
                                                   onchange="getPassportImage(this);">
                                        </div>

                                        <div id="sample_passport"
                                             style="margin-top: 20px;outline: 1px dashed #ccc;padding: 10px;">
                                            <div class="row">
                                                <div class="col-xs-8">
                                                    <div id="sample_passport_text"
                                                         style="display: flex;justify-content: center;align-items: center;">
                                                        <div>
                                                            <h4>Sample Passport</h4>
                                                            <p>Good quality image must be uploaded</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-4">
                                                    <img src="{{ asset('assets/images/sample_passport_n.jpg') }}"
                                                         class="img-responsive" alt="Passport sample image"/>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div id="passport_preloader" class="text-center fa-3x"
                                         style="display: none; padding: 10px 0;"><i class="fas fa-spinner fa-pulse"></i>
                                    </div>
                                </div>
                            </div>
                            <div id="passport_upload_view_wrapper" hidden>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6">
                                        <div id="passport_upload_view_div">
                                            <img class="img-thumbnail" id="passport_upload_view" src="#"
                                                 alt="Investor passport upload copy">
                                            <input type="hidden" name="passport_upload_base_code"
                                                   id="passport_upload_base_code">
                                            <input type="hidden" name="passport_upload_manual_file"
                                                   id="passport_upload_manual_file">
                                            <input type="hidden" name="passport_file_name" id="passport_file_name">
                                        </div>

                                        <div id="passport_cropped_result" class="panel panel-info">

                                        </div>

                                        <div style="margin-top: 15px;">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <button style="display: none;" type="button" id="passport_edit_btn"
                                                            class="btn btn-info pull-left">
                                                        <i class="far fa-edit"></i> Edit
                                                    </button>

                                                    <button type="button" id="passport_crop_btn"
                                                            class="btn btn-info pull-left">
                                                        <i class="far fa-check-circle"></i> Done
                                                    </button>
                                                </div>
                                                <div class="col-md-6 text-center">
                                                    {{--                                                Data: <span id="crop_data_info"></span>--}}
                                                </div>
                                                <div class="col-md-3">
                                                    <button type="button" id="passport_reset_btn"
                                                            class="btn btn-link pull-right">
                                                        <i class="fas fa-undo"></i> Remove Image
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <div class="panel panel-info">
                                            <div class="panel-heading" style="padding: 10px 15px;">
                                                <h3 class="panel-title">FAQ</h3>
                                            </div>
                                            <div class="panel-body">
                                                <ul class="list-group">
                                                    <li class="list-group-item">
                                                        <strong>Ques: </strong> How to zoom in or zoom out? <br>
                                                        <strong>Ans: </strong> Enable to zoom by wheeling mouse over the
                                                        image.
                                                    </li>
                                                    <li class="list-group-item">
                                                        <strong>Ques: </strong> How to resize? <br>
                                                        <strong>Ans: </strong> Click and drag on the image to make the
                                                        selection.
                                                    </li>
                                                    <li class="list-group-item">
                                                        <strong>Ques: </strong> How to crop? <br>
                                                        <strong>Ans: </strong> After choosing the image position, just
                                                        click your mouse on the <code>Done</code> button to crop.
                                                    </li>
                                                    <li class="list-group-item">
                                                        <strong>Ques: </strong> How to passport verify? <br>
                                                        <strong>Ans: </strong> After done your image you will get a
                                                        <code>green color verify</code> button. Just click and wait for
                                                        result.
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="preloader" class="text-center fa-3x" style="display: none; padding: 10px 0"></div>
                        <div id="passport_div_verification" hidden>
                            <div class="alert alert-success">
                                Please check your passport information data before submitting it.
                            </div>

                            <div class="col-md-8">
                                {{-- Passport Nationality --}}
                                <div class="form-group has-feedback {{ $errors->has('passport_nationality') ? 'has-error' : ''}}">
                                    <label for="passport_nationality"
                                           class="col-md-4 text-left required-star">Passport nationality</label>
                                    <div class="col-md-8">
                                        {!! Form::select('passport_nationality', $passport_nationalities, '', $attributes = array('class'=>'form-control input-sm required',
                                            'placeholder' => 'Select one', 'id'=>"passport_nationality")) !!}
                                        {!! $errors->first('passport_nationality','<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>

                                {{-- Passport Type --}}
                                <div class="form-group has-feedback {{ $errors->has('passport_type') ? 'has-error' : ''}}">
                                    <label for="passport_type"
                                           class="col-md-4 required-star">Passport type</label>
                                    <div class="col-md-8">
                                        {!! Form::select('passport_type', $passport_types, '', $attributes = array('class'=>'form-control input-sm required',
                                            'placeholder' => 'Select one', 'id'=>"passport_type")) !!}
                                        {!! $errors->first('passport_type','<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>

                                {{-- Passport No --}}
                                <div class="form-group has-feedback {{ $errors->has('passport_no') ? 'has-error' : ''}}">
                                    <label for="passport_no" class="col-md-4 required-star">Passport No.</label>
                                    <div class="col-md-8">
                                        {!! Form::text('passport_no', null, $attributes = array('class'=>'form-control required alphaNumeric input-sm', 'data-rule-maxlength'=>'20',
                                        'placeholder'=>'Enter your passport number', 'id'=>"passport_no")) !!}
                                        {!! $errors->first('passport_no','<span class="help-block">:message</span>') !!}
                                        <span class="text-danger pss-error"></span>
                                    </div>
                                </div>

                                {{-- Surname --}}
                                <div class="form-group has-feedback {{ $errors->has('passport_surname') ? 'has-error' : ''}}">
                                    <label for="passport_surname" class="col-md-4 required-star">Surname</label>
                                    <div class="col-md-8">
                                        {!! Form::text('passport_surname', null, $attributes = array('class'=>'form-control textOnly input-sm required',  'data-rule-maxlength'=>'40',
                                        'placeholder'=>'Enter your surname', 'id'=>"passport_surname")) !!}
                                        {!! $errors->first('passport_surname','<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>

                                {{-- Given Name	 --}}
                                <div class="form-group has-feedback {{ $errors->has('passport_given_name') ? 'has-error' : ''}}">
                                    <label for="passport_given_name" class="col-md-4 required-star">Given Name</label>
                                    <div class="col-md-8">
                                        {!! Form::text('passport_given_name', null, $attributes = array('class'=>'form-control textOnly input-sm required',  'data-rule-maxlength'=>'40',
                                        'placeholder'=>'Enter your given name', 'id'=>"passport_given_name")) !!}
                                        {!! $errors->first('passport_given_name','<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>

                                {{-- Personal No --}}
                                <div class="form-group has-feedback {{ $errors->has('passport_personal_no') ? 'has-error' : ''}}">
                                    <label for="passport_personal_no" class="col-md-4">Personal No.</label>
                                    <div class="col-md-8">
                                        {!! Form::text('passport_personal_no', null, $attributes = array('class'=>'form-control alphaNumeric input-sm',  'data-rule-maxlength'=>'40',
                                        'placeholder'=>'Enter your personal number', 'id'=>"passport_personal_no")) !!}
                                        {!! $errors->first('passport_personal_no','<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>

                                {{-- Date of Birth --}}
                                <div class="form-group has-feedback {{$errors->has('passport_DOB') ? 'has-error' : ''}}">
                                    <label for="passport_DOB" class="col-md-4 required-star">Date of Birth</label>
                                    <div class="col-md-8">
                                        <div class="passportDP input-group date">
                                            {!! Form::text('passport_DOB', '', ['class'=>'form-control input-sm required', 'id'=>'passport_DOB', 'placeholder' => 'Pick from calendar']) !!}
                                            <span class="input-group-addon">
                                                <span class="fa fa-calendar"></span>
                                            </span>
                                        </div>
                                        {!! $errors->first('passport_DOB','<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>

                                {{-- Date of Expire --}}
                                <div class="form-group has-feedback {{$errors->has('passport_date_of_expire') ? 'has-error' : ''}}">
                                    <label for="passport_date_of_expire" class="col-md-4 required-star">Date of
                                        Expiry</label>
                                    <div class="col-md-8">
                                        <div class="passExpiryDate input-group date">
                                            {!! Form::text('passport_date_of_expire', '', ['class'=>'form-control input-sm required','id'=>'passport_date_of_expire',  'placeholder' => 'Pick from calendar']) !!}
                                            <span class="input-group-addon">
                                            <span class="fa fa-calendar"></span>
                                        </span>
                                        </div>
                                        {!! $errors->first('passport_date_of_expire','<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="magnify">
                                    <div class="large" id="magnify_image_large"></div>
                                    <small>File name: <span id="passport_file_name_show"></span></small>
                                    <img class="small img-responsive" id="magnify_image_small"
                                         alt="Investor passport copy" src="">
                                </div>
                            </div>
                        </div>

                    </fieldset>

                    <br/>
                    <div class="form-group">
                        <div class="col-md-offset-4 col-md-8">
                            {{-- reCAPTCHA --}}
                            <div class=" {{$errors->has('g-recaptcha-response') ? 'has-error' : ''}}" style="margin-bottom: 20px;">
{{--                                <div class="col-md-offset-5 col-md-7">--}}
                                    {!! Recaptcha::render() !!}
                                    {!! $errors->first('g-recaptcha-response','<span class="help-block">:message</span>') !!}
{{--                                </div>--}}
                            </div>
                            <button type="button" class="btn btn-md btn-danger round-btn" id="nid_tin_close"
                                    style="display: none;"><strong>Close</strong></button>
                            <a data-action="{{ url('/') }}" href="javascript:void(0)" onclick="confirmLogout(this)"
                               id="passport_logout" class="btn btn-md btn-danger round-btn">
                                <i class="fas fa-sign-out-alt"></i>
                                <strong>Logout</strong>
                            </a>
                            <button type="submit" title="You must fill in all of the fields"
                                    class="btn btn-md btn-success round-btn" id="nid_tin_verify" disabled>
                                <i class="fas fa-check"></i>
                                <strong>Verify</strong>
                            </button>
                            <button type="button" class="btn btn-md btn-success round-btn" id="passport_verify"
                                    style="display: none;">
                                <i class="fas fa-check"></i>
                                <strong>Verify</strong>
                            </button>
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
    <script>
        $(document).ready(function () {
            
            // user_nid_name validation
            $('#user_nid_name').on('input', function () {
                var name = $(this).val();
                // english alphabets and dash(-) dot(.) validation
                name = name.replace(/[^a-zA-Z-.\s]/g, '');
                // uppercase validation
                name = name.toUpperCase();
                // remove extra spaces between words
                name = name.replace(/\s\s+/g, ' ');
                $(this).val(name);
            });
            
            
            @if(!empty($getPreviousVerificationData))
            $('#previousVerificationDataModal').modal('show');
            @endif
        });
    </script>

    <script src="{{ asset('vendor/cropperjs_v1.5.7/cropper.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset("assets/scripts/sweetalert2.all.min.js") }}" type="text/javascript"></script>
    <script src="{{ asset("assets/modules/signup/identity_verify.js") }}" type="text/javascript"></script>
@endsection