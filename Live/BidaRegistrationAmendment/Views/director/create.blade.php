<style>
    .margin-bottom{
        margin-bottom: 3px;
    }
    .radio-inline{
        padding-top: 0px !important;
    }

    .round-save {
        border-radius: 30px !important;
        min-width: 80px;
    }
</style>
<link rel="stylesheet" href="{{ asset("assets/stylesheets/bootstrap-datetimepicker.css") }}" />
<link rel="stylesheet" href="{{ asset('vendor/cropperjs_v1.5.7/cropper.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/modules/signup/identity_verify.css') }}">

{!! Form::open(array('url' => '/bida-registration-amendment/store-verify-director', 'method' => 'post', 'class' => 'form-horizontal smart-form', 'id'=>'directorVerifyForm',
        'enctype'=> 'multipart/form-data', 'role' => 'form')) !!}
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
                    <button type="button" id="passport_error_retry" class="btn btn-primary btn-outline">Retry</button>
                    <p style="margin: 15px 0">OR</p>
                    <button type="button" id="passport_error_manual" class="btn btn-warning btn-outline">Manually Input</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- End Passport error modal -->

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" onclick="closeModal()"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title" id="myModalLabel"> Add New Director</h4>
</div>

<div class="modal-body">
    <div class="errorMsg alert alert-danger alert-dismissible hidden">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>
    <div class="successMsg alert alert-success alert-dismissible hidden">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>

    <input type="hidden" value="{{ $app_id }}" name="app_id">
    <input type="hidden" value="{{ $encoded_process_type }}" name="encoded_process_type">

    {{--Nationality type--}}
    <div class="form-group margin-bottom" id="nationality_type">
        <div class="row">
            <div class="col-md-offset-2 col-md-8 {{$errors->has('nationality_type') ? 'has-error': ''}}">
                {!! Form::label('nationality_type','Nationality type', ['class'=>'col-md-3 text-left required-star']) !!}
                <div class="col-md-9">
                    <label class="radio-inline">
                        {!! Form::radio('nationality_type', 'bangladeshi', 0, ['class'=>'required', 'onclick' => 'setUserNationality(this.value)']) !!}
                        Bangladeshi
                    </label>
                    <label class="radio-inline">
                        {!! Form::radio('nationality_type', 'foreign', 0, ['class'=>'required', 'onclick' => 'setUserNationality(this.value)']) !!}
                        Foreign
                    </label>
                </div>
            </div>
        </div>
    </div>

    {{--bangladeshi indentity type fields--}}
    <div class="form-group margin-bottom" id="bd_nationality_fields" hidden>
        <div class="row">
            <div class="col-md-offset-2 col-md-8 {{$errors->has('identity_type') ? 'has-error': ''}}">
                {!! Form::label('identity_type','Identity type', ['class'=>'col-md-3 text-left required-star']) !!}
                <div class="col-md-9">
                    <label class="radio-inline">
                        {!! Form::radio('identity_type', 'nid', 0, ['class'=>'required', 'onclick' => 'setUserIdentity(this.value)']) !!}
                        NID
                    </label>
                    <label class="radio-inline">
                        {!! Form::radio('identity_type', 'tin', 0, ['class'=>'required', 'onclick' => 'setUserIdentity(this.value)']) !!}
                        TIN (Bangladeshi)
                    </label>
                </div>
            </div>
        </div>
    </div>

    {{--foreigner indentity type fields--}}
    <div class="form-group margin-bottom" id="foreign_nationality_fields" hidden>
        <div class="row">
            <div class="col-md-offset-2 col-md-8 {{$errors->has('identity_type') ? 'has-error': ''}}">
                {!! Form::label('identity_type','Identity type', ['class'=>'col-md-3 text-left required-star']) !!}
                <div class="col-md-9">
                    <label class="radio-inline">
                        {!! Form::radio('identity_type', 'tin', 0, ['class'=>'required', 'onclick' => 'setUserIdentity(this.value)']) !!}
                        TIN (Bangladeshi)
                    </label>
                    <label class="radio-inline">
                        {!! Form::radio('identity_type', 'passport', 0, ['class'=>'required', 'onclick' => 'setUserIdentity(this.value)']) !!}
                        Passport
                    </label>
                </div>
            </div>
        </div>
    </div>

    {{--NIDVerify step one--}}
    <div class="row" id="nid_verify" hidden>
        <div class="col-md-offset-2 col-md-8">
            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Step 1</legend>

                <div class="errorMsgNID alert alert-danger alert-dismissible hidden">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                </div>
                <div class="successMsgNID alert alert-success alert-dismissible hidden">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                </div>

                <!-- NID Verification response div -->
                <div class="alert alert-info" id="NIDVerificationResponse" hidden></div>

                <!-- Total spent time to NID verification -->
                <div id="NIDVerificationTimeCounting" class="text-success" hidden>
                    Waiting for the connection to the national ID server, <span id="NIDVerifyTimeSpent">0</span>
                    seconds
                    passed.
                </div>

                <div class="form-group margin-bottom">
                    {!! Form::label('user_nid','National ID No', ['class'=>'col-md-3 text-left required-star']) !!}
                    <div class="col-md-9 {{$errors->has('user_nid') ? 'has-error': ''}}">
                        {!! Form::text('user_nid', '', ['class' => 'form-control input-md required', 'id'=>'']) !!}
                        {!! $errors->first('user_nid','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
                <div class="form-group margin-bottom">
                    {!! Form::label('nid_dob','Date of Birth', ['class'=>'col-md-3 text-left required-star']) !!}
                    <div class="col-md-9 {{$errors->has('nid_dob') ? 'has-error': ''}}">
                        <div class="userDP input-group date">
                            {!! Form::text('nid_dob', '', ['class'=>'form-control input-sm required', 'id'=>'nid_dob', 'placeholder' => 'Pick from calendar']) !!}
                            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                        </div>
                        {!! $errors->first('nid_dob','<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group margin-bottom has-feedback {{ $errors->has('user_nid_name') ? 'has-error' : ''}}">
                    <label for="user_nid_name" class="col-md-3 required-star">Name(English)</label>
                    <div class="col-md-9">
                        {!! Form::text('user_nid_name', null, $attributes = array('class'=>'form-control required input-sm engOnly',
                        'placeholder'=>'Enter english name of your NID', 'id'=>"user_nid_name")) !!}
                        {!! $errors->first('user_nid_name','<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                {{-- <div class="form-group has-feedback {{ $errors->has('user_nid_postal_code') ? 'has-error' : ''}}">
                    <label for="user_nid_postal_code" class="col-md-3 required-star">Post Code</label>
                    <div class="col-md-9">
                        {!! Form::text('user_nid_postal_code', null, $attributes = array('class'=>'form-control required input-sm number',  'data-rule-maxlength'=>'10',
                        'placeholder'=>'Enter post code of your NID', 'id'=>"user_nid_postal_code")) !!}
                        {!! $errors->first('user_nid_postal_code','<span class="help-block">:message</span>') !!}
                    </div>
                </div> --}}

                {{-- reCAPTCHA --}}
                <div class=" {{$errors->has('g-recaptcha-response') ? 'has-error' : ''}}" style="margin-bottom: 20px;">
                    <div class="col-md-offset-3 col-md-9" style="padding-left: 0px;">
                        {!! Recaptcha::render() !!}
                        {!! $errors->first('g-recaptcha-response','<span class="help-block">:message</span>') !!}
                    </div>
                </div>


                <div class="pull-right">
                    <button class="btn btn-info round-btn" id="NIDVerifyBtn" name="nid_verify_btn" onclick="submitIdentityVerifyForm('directorVerifyForm')"><i class="fa fa-check-circle" aria-hidden="true"></i> Verify</button>
                </div>
            </fieldset>
        </div>
    </div>

    {{--ETINVerify step one--}}
    <div class="row" id="tin_verify" hidden>
        <div class="col-md-offset-2 col-md-8">
            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Step 1</legend>

                <div class="errorMsgTIN alert alert-danger alert-dismissible hidden">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                </div>
                <div class="successMsgTIN alert alert-success alert-dismissible hidden">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                </div>

                <div class="alert alert-info" id="ETINResponseCountMsg" hidden></div>

                <div id="ETINVerifySuccessMsg" class="text-danger"></div>

                <div class="form-group margin-bottom">
                    {!! Form::label('user_tin','TIN Number', ['class'=>'col-md-3 text-left required-star']) !!}
                    <div class="col-md-9 {{$errors->has('nid') ? 'has-error': ''}}">
                        {!! Form::text('user_tin', '', ['class' => 'form-control input-md required', 'id'=>'etin_number']) !!}
                        {!! $errors->first('user_tin','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('etin_dob','Date of Birth', ['class'=>'col-md-3 text-left required-star']) !!}
                    <div class="col-md-9 {{$errors->has('etin_dob') ? 'has-error': ''}}">
                        <div class="eTinDP input-group date">
                            {!! Form::text('etin_dob', '', ['class'=>'form-control input-sm required', 'id'=>'etin_dob', 'placeholder' => 'Pick from calendar']) !!}
                            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                        </div>
                        {!! $errors->first('etin_dob','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
                <div class="pull-right">
                    <button class="btn btn-info round-btn" id="TINVerifyBtn" name="tin_verify_btn" onclick="submitIdentityVerifyForm('directorVerifyForm')"><i class="fa fa-check-circle" aria-hidden="true"></i> Verify</button>
                </div>
            </fieldset>
        </div>
    </div>

    {{--NIDVerify step two save information--}}
    <div class="row" id="nid_save" hidden>
        <div class="col-md-offset-2 col-md-8">
            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Step 2</legend>
                {{-- Amendment type	 --}}
                <div class="form-group has-feedback {{ $errors->has('amendment_type') ? 'has-error' : ''}}">
                    <label for="amendment_type" class="col-md-4 required-star">Amendment type</label>
                    <div class="col-md-8">
                        <label class="radio-inline">
                            {!! Form::radio('amendment_type', 'existing', 0, ['class'=>'required']) !!}
                            Existing
                        </label>
                        <label class="radio-inline">
                            {!! Form::radio('amendment_type', 'proposed', 0, ['class'=>'required']) !!}
                            Proposed
                        </label>
                    </div>
                </div>

                <div class="form-group margin-bottom">
                    {!! Form::label('user_nid','National ID No', ['class'=>'col-md-3 text-left']) !!}
                    <div class="col-md-9 {{$errors->has('user_nid') ? 'has-error': ''}}">
                        {!! Form::text('user_nid', '', ['class' => 'form-control input-md', 'id'=>'user_nid', 'readonly' => 'true']) !!}
                        {!! $errors->first('user_nid','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
                <div class="form-group margin-bottom">
                    {!! Form::label('nid_dob','Date of Birth', ['class'=>'col-md-3 text-left']) !!}
                    <div class="col-md-9 {{$errors->has('nid_dob') ? 'has-error': ''}}">
                        <span class="form-control input-md" id="save_nid_dob" style="background:#eee; height: auto;min-height: 30px;"></span>
                    </div>
                </div>
                <div class="form-group margin-bottom">
                    {!! Form::label('nid_name','Name', ['class'=>'col-md-3 text-left']) !!}
                    <div class="col-md-9 {{$errors->has('nid_name') ? 'has-error': ''}}">
                        {!! Form::text('nid_name', '', ['class' => 'form-control input-md', 'id'=>'nid_name', 'readonly' => 'true']) !!}
                        {!! $errors->first('nid_name','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
                <div class="form-group margin-bottom">
                    {!! Form::label('gender','Gender', ['class'=>'col-md-3 text-left required-star']) !!}
                    <div class="col-md-9 {{$errors->has('gender') ? 'has-error': ''}}">
                        <label class="radio-inline">
                            {!! Form::radio('gender', 'male', 0, ['class'=>'required']) !!}
                            Male
                        </label>
                        <label class="radio-inline">
                            {!! Form::radio('gender', 'female', 0, ['class'=>'required']) !!}
                            Female
                        </label>
                        <label class="radio-inline">
                            {!! Form::radio('gender', 'other', 0, ['class'=>'required']) !!}
                            Other
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('nid_designation','Designation', ['class'=>'col-md-3 text-left required-star']) !!}
                    <div class="col-md-9 {{$errors->has('nid_designation') ? 'has-error': ''}}">
                        {!! Form::text('nid_designation', '', ['class' => 'form-control input-md required', 'id'=>'nid_designation']) !!}
                        {!! $errors->first('nid_designation','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('nid_nationality','Nationality', ['class'=>'col-md-3 text-left required-star']) !!}
                    <div class="col-md-9 {{$errors->has('nid_nationality') ? 'has-error': ''}}">
                        {!! Form::select('nid_nationality', countryBD(), countryBDId(), ['class' => 'form-control input-md required', 'id'=>'nid_nationality']) !!}
                        {!! $errors->first('nid_nationality','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
                <div class="pull-right">
                    <button type="submit" class="btn btn-info round-save" id="btn_save_close" name="btn_save" value="NID" onclick="submitIdentityVerifyForm('directorVerifyForm')"><i class="fas fa-save"></i> Save</button>
                    <button type="submit" class="btn btn-warning round-btn" id="btn_save" name="btn_save" value="NID" onclick="submitIdentityVerifyForm('directorVerifyForm')"><i class="fas fa-save"></i> Save and new</button>
                </div>
            </fieldset>
        </div>
    </div>

    {{--ETINVerify step two and save information--}}
    <div class="row" id="tin_save" hidden>
        <div class="col-md-offset-2 col-md-8">
            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Step 2</legend>
                {{-- Amendment type	 --}}
                <div class="form-group has-feedback {{ $errors->has('amendment_type') ? 'has-error' : ''}}">
                    <label for="amendment_type" class="col-md-4 required-star">Amendment type</label>
                    <div class="col-md-8">
                        <label class="radio-inline">
                            {!! Form::radio('amendment_type', 'existing', 0, ['class'=>'required']) !!}
                            Existing
                        </label>
                        <label class="radio-inline">
                            {!! Form::radio('amendment_type', 'proposed', 0, ['class'=>'required']) !!}
                            Proposed
                        </label>
                    </div>
                </div>

                <div class="form-group margin-bottom">
                    {!! Form::label('etin_name','Name', ['class'=>'col-md-3 text-left']) !!}
                    <div class="col-md-9 {{$errors->has('etin_name') ? 'has-error': ''}}">
                        {!! Form::text('etin_name', '', ['class' => 'form-control input-md', 'id'=>'etin_name', 'readonly' => 'true']) !!}
                        {!! $errors->first('etin_name','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
                <div class="form-group margin-bottom">
                    {!! Form::label('user_etin','TIN Number', ['class'=>'col-md-3 text-left']) !!}
                    <div class="col-md-9 {{$errors->has('user_etin') ? 'has-error': ''}}">
                        {!! Form::text('user_etin', '', ['class' => 'form-control input-md', 'id'=>'user_etin', 'readonly' => 'true']) !!}
                        {!! $errors->first('user_etin','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
                <div class="form-group margin-bottom">
                    {!! Form::label('etin_dob','Date of Birth', ['class'=>'col-md-3 text-left']) !!}
                    <div class="col-md-9 {{$errors->has('etin_dob') ? 'has-error': ''}}">
                        <span class="form-control input-md" id="save_etin_dob" style="background:#eee; height: auto;min-height: 30px;"></span>
                    </div>
                </div>

                <div class="form-group margin-bottom">
                    {!! Form::label('gender','Gender', ['class'=>'col-md-3 text-left required-star']) !!}
                    <div class="col-md-9 {{$errors->has('gender') ? 'has-error': ''}}">
                        <label class="radio-inline">
                            {!! Form::radio('gender', 'male', 0, ['class'=>'required']) !!}
                            Male
                        </label>
                        <label class="radio-inline">
                            {!! Form::radio('gender', 'female', 0, ['class'=>'required']) !!}
                            Female
                        </label>
                        <label class="radio-inline">
                            {!! Form::radio('gender', 'other', 0, ['class'=>'required']) !!}
                            Other
                        </label>
                    </div>
                </div>
                <div class="form-group margin-bottom">
                    {!! Form::label('etin_designation','Designation', ['class'=>'col-md-3 text-left required-star']) !!}
                    <div class="col-md-9 {{$errors->has('etin_designation') ? 'has-error': ''}}">
                        {!! Form::text('etin_designation', '', ['class' => 'form-control input-md required', 'id'=>'etin_designation']) !!}
                        {!! $errors->first('etin_designation','<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('etin_nationality','Nationality', ['class'=>'col-md-3 text-left required-star']) !!}
                    <div class="col-md-9 {{$errors->has('etin_nationality') ? 'has-error': ''}}">
                        {!! Form::select('etin_nationality', $nationality,'', ['class' => 'form-control input-md required', 'id'=>'etin_nationality']) !!}
                        {!! $errors->first('etin_nationality','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
                <div class="pull-right">
                    <button class="btn btn-info round-save" id="ETINVerifySave" name="btn_save" value="ETIN" onclick="submitIdentityVerifyForm('directorVerifyForm')"><i class="fas fa-save"></i> Save</button>
                    <button class="btn btn-warning round-btn" id="ETINVerifySaveBtn" name="btn_save" value="ETIN" onclick="submitIdentityVerifyForm('directorVerifyForm')"><i class="fas fa-save"></i> Save and new</button>
                </div>
            </fieldset>
        </div>
    </div>

    {{--passport information--}}
    <div class="row" id="passport_div" hidden>
        <div class="col-md-offset-1 col-md-10">
            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Passport information</legend>
                <div id="passport_upload_wrapper" class="passport-upload-wrapper">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <span id="passport_upload_error" class="text-danger text-left"></span>

                            <div style="text-align: center;" id="passport_upload_div">
                                <div class="passport-upload" style="height: 300px;">
                                    <div class="passport-upload-message">
                                        <i class="fas fa-cloud-upload-alt fa-3x passport-upload-icon"></i>
                                        <p>
                                            Drop Your Passport scan copy here or
                                            <span style="color:#258DFF;">Browse</span>
                                            <small class="help-block" style="font-size: 9px;">[File Format: *.jpg/ .jpeg/ .png | Maximum 5 MB | Width 746 to 3500 pixel | Height 1043 to 4500 pixel]</small>
                                        </p>
                                    </div>
                                    <input accept="image/*" type="file" name="passport_upload" id="passport_upload" class="passport-upload-input" onchange="getPassportImage(this);">
                                </div>

                            </div>
                            <div style="text-align: center">
                                <a href="{{ asset('assets/images/sample_passport.jpg') }}" ref="noopener" target="_blank">Sample Passport</a>
                            </div>

                            <div id="passport_preloader" class="text-center fa-3x" style="display: none; padding: 10px 0;"><i class="fas fa-spinner fa-pulse"></i></div>
                        </div>
                    </div>
                    <div id="passport_upload_view_wrapper" hidden>
                        <div class="row">
                            <div class="col-md-6 col-sm-6">
                                <div id="passport_upload_view_div">
                                    <img class="img-thumbnail" id="passport_upload_view" src="#" alt="Investor passport upload copy">
                                    <input type="hidden" name="passport_upload_base_code" id="passport_upload_base_code">
                                    <input type="hidden" name="passport_upload_manual_file" id="passport_upload_manual_file">
                                    <input type="hidden" name="passport_file_name" id="passport_file_name">
                                </div>

                                <div id="passport_cropped_result" class="panel panel-info">

                                </div>

                                <div style="margin-top: 15px;">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <button style="display: none;" type="button" id="passport_edit_btn" class="btn btn-info pull-left">
                                                <i class="far fa-edit"></i> Edit
                                            </button>

                                            <button type="button" id="passport_crop_btn" class="btn btn-info pull-left">
                                                <i class="far fa-check-circle"></i> Done
                                            </button>
                                        </div>
                                        <div class="col-md-6 text-center">
                                            {{--                                                Data: <span id="crop_data_info"></span>--}}
                                        </div>
                                        <div class="col-md-3">
                                            <button type="button" id="passport_reset_btn" class="btn btn-link pull-right">
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
                                                <strong>Ans: </strong> Enable to zoom by wheeling mouse over the image.
                                            </li>
                                            <li class="list-group-item">
                                                <strong>Ques: </strong> How to resize? <br>
                                                <strong>Ans: </strong> Click and drag on the image to make the selection.
                                            </li>
                                            <li class="list-group-item">
                                                <strong>Ques: </strong> How to crop? <br>
                                                <strong>Ans: </strong> After choosing the image position, just click your mouse on the <code>Done</code> button to crop.
                                            </li>
                                            <li class="list-group-item">
                                                <strong>Ques: </strong> How to passport verify? <br>
                                                <strong>Ans: </strong> After done your image you will get a <code>green color verify</code> button. Just click and wait for result.
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

                        {{-- Amendment type	 --}}
                        <div class="form-group has-feedback {{ $errors->has('amendment_type') ? 'has-error' : ''}}">
                            <label for="amendment_type" class="col-md-4 required-star">Amendment type</label>
                            <div class="col-md-8">
                                <label class="radio-inline">
                                    {!! Form::radio('amendment_type', 'existing', 0, ['class'=>'required']) !!}
                                    Existing
                                </label>
                                <label class="radio-inline">
                                    {!! Form::radio('amendment_type', 'proposed', 0, ['class'=>'required']) !!}
                                    Proposed
                                </label>
                            </div>
                        </div>

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

                        {{-- Gender	 --}}
                        <div class="form-group has-feedback {{ $errors->has('gender') ? 'has-error' : ''}}">
                            <label for="gender" class="col-md-4 required-star">Gender</label>
                            <div class="col-md-8">
                                <label class="radio-inline">
                                    {!! Form::radio('gender', 'male', 0, ['class'=>'required']) !!}
                                    Male
                                </label>
                                <label class="radio-inline">
                                    {!! Form::radio('gender', 'female', 0, ['class'=>'required']) !!}
                                    Female
                                </label>
                                <label class="radio-inline">
                                    {!! Form::radio('gender', 'other', 0, ['class'=>'required']) !!}
                                    Other
                                </label>
                                {!! $errors->first('gender','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>

                        {{-- Designation --}}
                        <div class="form-group has-feedback {{ $errors->has('l_director_designation') ? 'has-error' : ''}}">
                            <label for="l_director_designation" class="col-md-4 required-star">Designation</label>
                            <div class="col-md-8">
                                {!! Form::text('l_director_designation', null, $attributes = array('class'=>'form-control input-sm required',  'data-rule-maxlength'=>'40',
                                'placeholder'=>'Enter your designation', 'id'=>"l_director_designation")) !!}
                                {!! $errors->first('l_director_designation','<span class="help-block">:message</span>') !!}
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
                                <div class="passportDOB input-group date">
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
                            <label for="passport_date_of_expire" class="col-md-4 required-star">Date of Expiry</label>
                            <div class="col-md-8">
                                <div class="passExpiryDate passportDOB input-group date">
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
                            <img class="small img-responsive" id="magnify_image_small" alt="Investor passport copy" src="">
                        </div>
                    </div>

                    <div class="form-group col-md-8 pull-right">
                        <div class="row">
                            <button class="btn btn-info round-save" id="passport_save_close" name="btn_save" value="passport" onclick="submitIdentityVerifyForm('directorVerifyForm')" style="display: none; margin-right: 5px;"><i class="fas fa-save"></i> Save</button>
                            <button class="btn btn-warning round-btn" id="passport_save" name="btn_save" value="passport" onclick="submitIdentityVerifyForm('directorVerifyForm')" style="display: none"><i class="fas fa-save"></i> Save and new</button>
                        </div>
                    </div>
                </div>
                <br>
                <div class="pull-right">
                    <button type="button" class="btn btn-md btn-success round-btn pull-right" id="passport_verify" style="display: none"><strong>Verify</strong></button>
                </div>
            </fieldset>
        </div>
    </div>
</div>
{!! Form::close() !!}

<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
<script src="{{ asset('vendor/cropperjs_v1.5.7/cropper.min.js') }}" type="text/javascript"></script>

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
        
    });
</script>

<script>

    $(document).ready(function () {
        //datePicker ....
        var today = new Date();
        var yyyy = today.getFullYear();

        $('.userDP').datetimepicker({
            viewMode: 'years',
            format: 'DD-MMM-YYYY',
            maxDate: 'now',
            minDate: '01/01/' + (yyyy - 110)
        });

        $('.dobDP').datetimepicker({
            viewMode: 'years',
            format: 'DD-MMM-YYYY',
            maxDate: '01/01/' + (yyyy + 20),
            minDate: '01/01/' + (yyyy - 10)
        });

        $('.eTinDP').datetimepicker({
            viewMode: 'years',
            format: 'DD-MMM-YYYY',
            maxDate: 'now',
            minDate: '01/01/' + (yyyy - 110)
        });

        $('.passportDOB').datetimepicker({
            viewMode: 'years',
            format: 'DD-MMM-YYYY',
            maxDate: '01/01/' + (yyyy - 110),
            minDate: '01/01/' + (yyyy - 110)
        });
    });

    /**
     * Show nationality wise fields
     * @param nationality
     */
    function setUserNationality(nationality) {
        if (nationality === 'bangladeshi') {
            document.getElementById('bd_nationality_fields').style.display = 'block';
            document.getElementById('foreign_nationality_fields').style.display = 'none';
            $('input[name="identity_type_foreign"]').prop('checked', false);

            //hidden second step
            document.getElementById('nid_save').style.display = 'none';
            document.getElementById('tin_save').style.display = 'none';
            document.getElementById('passport_div').style.display = 'none';
        } else if (nationality === 'foreign') {
            document.getElementById('foreign_nationality_fields').style.display = 'block';
            document.getElementById('bd_nationality_fields').style.display = 'none';
            $('input[name="identity_type_bd"]').prop('checked', false);

            //hidden second step
            document.getElementById('nid_save').style.display = 'none';
            document.getElementById('tin_save').style.display = 'none';
            document.getElementById('passport_div').style.display = 'none';
        } else {
            document.getElementById('foreign_nationality_fields').style.display = 'none';
            document.getElementById('bd_nationality_fields').style.display = 'none';
        }

        // Trigger on user identity
        setUserIdentity();
    }

    /**
     * Show identity type wise div
     * @param identity
     */
    function setUserIdentity(identity) {
        if (identity === 'nid') {
            document.getElementById('nid_verify').style.display = 'block';
            document.getElementById('tin_verify').style.display = 'none';
            //display none second step tin infomation
            document.getElementById('tin_save').style.display = 'none';
            document.getElementById('nid_save').style.display = 'none';
            document.getElementById('passport_div').style.display = 'none';

        } else if (identity === 'tin') {
            document.getElementById('tin_verify').style.display = 'block';
            document.getElementById('nid_verify').style.display = 'none';
            document.getElementById('passport_div').style.display = 'none';
            //display none second step nid information
            document.getElementById('nid_save').style.display = 'none';
            document.getElementById('tin_save').style.display = 'none';
        } else if (identity === 'passport') {
            document.getElementById('passport_div').style.display = 'block';
            document.getElementById('tin_verify').style.display = 'none';
            //display none second step tin information
            document.getElementById('tin_save').style.display = 'none';
        } else {
            document.getElementById('nid_verify').style.display = 'none';
            document.getElementById('tin_verify').style.display = 'none';
            document.getElementById('passport_div').style.display = 'none';
        }
    }

    /**
     * sumit form
     * @param form id
     * validation input field
     */
    function submitIdentityVerifyForm(form_name){
        $("#" + form_name).validate({
            errorPlacement: function () {
                return true;
            },
            submitHandler: function (form) {
                var getBtnId = document.activeElement.id;
                if (getBtnId === 'NIDVerifyBtn') {
                    document.getElementById('NIDVerificationTimeCounting').style.display = 'block';
                    document.getElementById('NIDVerificationResponse').style.display = 'block';
                    $("#NIDVerificationResponse").slideDown('slow', function () {
                        $("#NIDVerificationResponse").html('<i class="fa fa-spinner fa-spin"></i> Waiting for the connection to the national ID server.....');

                        /**
                         * submit_time need to assign before verifyNID() calling.
                         * Because, this time will calculate inside the verifyNID() function.
                         */
                        submit_time = new Date();
                        VerifyNID();
                    });

                } else if (getBtnId === 'TINVerifyBtn') {
                    document.getElementById('ETINResponseCountMsg').style.display = 'block';
                    $("#ETINResponseCountMsg").slideDown('slow', function () {
                        $("#ETINResponseCountMsg").html('<i class="fa fa-spinner fa-spin"></i> Waiting for the connection to the NBR server.....');

                        /**
                         * submit_time need to assign before verifyETIN() calling.
                         * Because, this time will calculate inside the verifyETIN() function.
                         */
                        submit_time = new Date();
                        VerifyETIN();
                    });

                }else {

                    /**
                     * form submit
                     * save NID, ETIN, passport information
                     * call function @formSubmit for ajax request
                     */
                    formSubmit(getBtnId);
                }
            }
        });
    }

    var form = $("#directorVerifyForm"); //Get Form ID
    var url = form.attr("action"); //Get Form action
    var type = form.attr("method"); //get form's data send method
    var info_err = $('.errorMsg'); //get error message div
    var info_suc = $('.successMsg'); //get success message div

    //============Ajax Setup===========//
    function formSubmit(btn) {
        $.ajax({
            type: type,
            url: url,
            data: form.serialize(),
            dataType: 'json',
            beforeSend: function (msg) {
                $("#Duplicated jQuery selector").html('<i class="fa fa-cog fa-spin"></i> Loading...');
                $("#Duplicated jQuery selector").prop('disabled', true); // disable button
            },
            success: function (data) {
                //==========validation error===========//
                if (data.success == false) {
                    info_err.hide().empty();
                    $.each(data.error, function (index, error) {
                        info_err.removeClass('hidden').append('<li>' + error + '</li>');
                    });
                    info_err.slideDown('slow');
                    info_err.delay(2000).slideUp(1000, function () {
                        $("#Duplicated jQuery selector").html('Submit');
                        $("#Duplicated jQuery selector").prop('disabled', false);
                    });
                }
                //==========if data is saved=============//
                if (data.success == true) {
                    info_suc.hide().empty();
                    info_suc.removeClass('hidden').html(data.status);
                    info_suc.slideDown('slow');
                    info_suc.delay(2000).slideUp(800, function () {

                        if (btn == 'ETINVerifySave' || btn == 'passport_save_close' || btn == 'btn_save_close') {
                            $("#openDirectorModal").modal('hide');
                            closeModal();
                        }

                        if (btn == 'ETINVerifySaveBtn' || btn == 'passport_save' || btn == 'btn_save') {
                            //hidden second step
                            document.getElementById('nid_save').style.display = 'none';
                            document.getElementById('tin_save').style.display = 'none';
                            document.getElementById('passport_div').style.display = 'none';
                            document.getElementById('bd_nationality_fields').style.display = 'none';
                            document.getElementById('foreign_nationality_fields').style.display = 'none';

                            document.getElementById('passport_div_verification').style.display = 'none';
                            document.getElementById('passport_upload_view_wrapper').style.display = 'none';
                            document.getElementById('passport_upload_div').style.display = 'block';
                            document.getElementById('passport_upload_wrapper').style.display = 'block';
                        }

                        if (btn == 'passport_save') {
                            $('#passport_reset_btn').click();
                        }

                        // reload list of directior table
                        listOfDirectors(20, 'off');
                    });

                    // form.trigger("reset");
                    form.each(function(){
                        this.reset();
                    });

                }
                //=========if data already submitted===========//
                if (data.error == true) {
                    info_err.hide().empty();
                    info_err.removeClass('hidden').html(data.status);
                    info_err.slideDown();
                    info_err.delay(8000).slideUp(800, function () {
                        $("#Duplicated jQuery selector").html('Submit');
                        $("#Duplicated jQuery selector").prop('disabled', false);
                    });
                }
            },
            error: function (data) {
                var errors = data.responseJSON;
                $("#Duplicated jQuery selector").prop('disabled', false);
                alert('Sorry, an unknown Error has been occured! Please try again later.');
            }
        });
        return false;
    }

    /**
     * This variable will used to time counting in NID Verification.
     * we should declare the variable at first, for global scope.
     *
     * But, data will be assigned before verifyNID() function calling.
     */
    var submit_time;
    function NIDVerifyTimeCount() {
        var date1 = new Date();
        /**
         * submit_time must need to declared before verifyNID() calling
         *
         */
        return Math.floor((date1 - submit_time) / 1000);
    }

    /**
     * NID verification recursion variable (setRecursionForNIDVerification), which will used to stop recursion calling.
     */
    var setRecursionForNIDVerification;

    /**
     * ETIN verification recursion variable (setRecursionForETINVerification), which will used to stop recursion calling.
     */
    var setRecursionForETINVerification;

    function VerifyNID() {
        var info_err = $('.errorMsgNID'); //get error message div
        var info_suc = $('.successMsgNID'); //get success message div

        var nid_number = document.querySelector("input[name='user_nid']").value;
        var user_DOB = document.querySelector("input[name='nid_dob']").value;
        var user_nid_name = document.getElementById("user_nid_name").value.trim();
        // var user_nid_postal_code = document.getElementById("user_nid_postal_code").value;

        var nid_length = nid_number.length;
        if (nid_length === 10 || nid_length === 13 || nid_length === 17) {
            $.ajax({
                // url: '/signup/identity-verify/nid-verify',
                url: '/signup/identity-verify/nid-verify-auth',
                type: 'GET',
                data: {
                    nid_number: nid_number,
                    user_DOB: user_DOB,
                    user_nid_name: user_nid_name,
                    // user_nid_postal_code: user_nid_postal_code,
                    g_recaptcha_response: grecaptcha.getResponse()
                },
                datatype: 'json',
                success: function (response) {
                    // console.log(response);
                    var NIDVerificationTimeCounting = document.getElementById('NIDVerificationTimeCounting');

                    // Print validation errors or others error (Execution, try-catch, custom condition)
                    if (response.success == false || response.status == 'error') {
                        // Hide NIDVerificationTimeCounting div
                        NIDVerificationTimeCounting.style.display = 'none';

                        // Reset error message div and put the message inside
                        info_err.hide().empty();
                        if (typeof response.message === 'object') {
                            $.each(response.message, function (index, error) {
                                info_err.removeClass('hidden').append('<li>' + error + '</li>');
                            });
                        } else {
                            info_err.removeClass('hidden').html(response.message);
                        }

                        // Slide up NIDVerificationResponse div and slide down error message div
                        $("#NIDVerificationResponse").slideUp('slow', function () {
                            info_err.slideDown('slow');
                        });
                    }
                    // End Print validation errors or others error (Execution, try-catch, custom condition)

                    if (response.success == true) {
                        if (response.status == 200) {
                            // Hide NIDVerificationTimeCounting div
                            NIDVerificationTimeCounting.style.display = 'none';
                            // Put success message inside the NIDVerificationResponse div
                            document.getElementById('NIDVerificationResponse').innerHTML = 'Congrats!!! Your NID is valid.';
                            // Slide up NIDVerificationResponse div and slide down the NID details div
                            $('#NIDVerificationResponse').delay(3000).slideUp(1000, function () {

                                // NID details div slide down and put the details inside
                                $('#VerifiedNIDInfo').slideDown('slow');

                                //$('#step_one').slideUp('slow');
                                $('#nid_verify').slideUp('slow');
                                $('#nid_save').slideDown('slow');

                                $('input[name="user_nid"]').val(nid_number);
                                $('input[name="nid_name"]').val(user_nid_name);
                                document.getElementById('save_nid_dob').innerHTML = user_DOB;
                                // var nid_dob = new Date(response.data.dob);
                                // document.getElementById('save_nid_dob').innerHTML = nid_dob.getDate() + "-" + nid_dob.toLocaleString('default', {month: 'short'}) + "-" + nid_dob.getFullYear();
                            });
                        } 
                        /* nid old (zaman vai)
                        else {
                            // Reset error message div and put the message inside
                            info_err.hide().empty();

                            // Set the message based on different condition
                            if (NIDVerifyTimeCount() > 60 * 3) {
                                document.getElementById('NIDVerificationResponse').innerHTML = '<i class="fa fa-spinner fa-spin"></i> Its been 3 minute already!<br />We will try to verify for you, when server is available<br /><b>You can try again after some time...</b>';
                            } else if (response.statusCode === 999) {
                                document.getElementById('NIDVerificationResponse').innerHTML = '<i class="fa fa-spinner fa-spin"></i> Your national ID information has been sent for verification, please wait...';
                            } else if (response.statusCode === 777) {
                                document.getElementById('NIDVerificationResponse').innerHTML = '<i class="fa fa-spinner fa-spin"></i> Your national ID information has been sent to EC Server. Please wait for some more time...';
                            } else {
                                document.getElementById('NIDVerificationResponse').innerHTML = '<i class="fa fa-spinner fa-spin"></i> Waiting for response from the national ID server. Please wait...';
                            }
                            // print the total spent time from first calling to till now to verify the NID
                            document.getElementById('NIDVerifyTimeSpent').innerHTML = NIDVerifyTimeCount();
                            // Re-call NID API after 8 second
                            setRecursionForNIDVerification = setTimeout(VerifyNID, 8000);
                        }
                        */
                    }

                    // reset grecaptcha
                    grecaptcha.reset();
                    
                },
                error: function (jqHR, textStatus, errorThrown) {
                    // Reset error message div and put the message inside
                    info_err.hide().empty();
                    info_err.removeClass('hidden').html(errorThrown);

                    // Slide up NIDVerificationResponse div and slide down error message div
                    $("#NIDVerificationResponse").slideUp('slow', function () {
                        info_err.slideDown('slow');
                    });

                    // On modal close, disable NID verification calling.
                    clearTimeout(setRecursionForNIDVerification);
                },
                beforeSend: function () {
                    // Reset error message div and put the message inside
                    info_err.hide().empty();
                }
            });
        } else {
            $("#NIDVerificationResponse").slideUp('slow', function () {
                info_err.hide().empty();
                info_err.removeClass('hidden').html('Invalid NID length. It should be 10, 13 or 17 digits.');
                info_err.slideDown('slow');
            });

        }
    }

    function VerifyETIN() {
        var etin_number = document.getElementById("etin_number").value;
        var user_DOB = document.getElementById("etin_dob").value;

        var info_err = $('.errorMsgTIN'); //get error message div
        var info_suc = $('.successMsgTIN'); //get success message div

        if (etin_number == '' || user_DOB == '') {
            alert('Invalid ETIN No. or Date of Birth');
        }
        var etin_length = etin_number.length;
        if (etin_length >= 10 && etin_length <= 15) {
            $.ajax({
                url: '/signup/identity-verify/etin-verify',
                type: 'GET',
                data: {
                    etin_number: etin_number,
                    user_DOB: user_DOB,
                },
                datatype: 'json',
                success: function (response) {
                    // Print validation errors or others error (Execution, try-catch, custom condition)
                    if (response.status === 'error') {
                        info_err.hide().empty();
                        if (typeof response.message === 'object') {
                            $.each(response.message, function (index, error) {
                                info_err.removeClass('hidden').append('<li>' + error + '</li>');
                            });
                        } else {
                            info_err.removeClass('hidden').html(response.message);
                        }

                        $("#ETINResponseCountMsg").slideUp('slow', function () {
                            info_err.slideDown('slow');
                            // info_err.delay(2000).slideUp(1000, function () {
                            //     $('#ETInVerifyModal').modal('hide');
                            // });
                        });
                    }
                    // End Print validation errors or others error (Execution, try-catch, custom condition)


                    if (response.status === 'success') {
                        var ETINVerifySuccessMsg = document.getElementById('ETINVerifySuccessMsg');
                        // Reset ETINVerifySuccessMsg div
                        ETINVerifySuccessMsg.style.display = 'none';

                        document.getElementById('ETINResponseCountMsg').innerHTML = 'Congrats!!! Your ETIN is valid.';
                        $('#ETINResponseCountMsg').delay(1000).slideUp(1000, function () {

                            $('#VerifiedETINInfo').slideDown('slow');

                            //$('#step_one').slideUp('slow');
                            $('#tin_verify').slideUp('slow');
                            $('#tin_save').slideDown('slow');

                            $('input[name="etin_name"]').val(response.data.nameEn);
                            $('input[name="user_etin"]').val(etin_number);
                            var etin_dob = new Date(response.data.dob);
                            document.getElementById('save_etin_dob').innerHTML = etin_dob.getDate() + "-" + etin_dob.toLocaleString('default', {month: 'short'}) + "-" + etin_dob.getFullYear();
                            // put etin data into hidden field
                            //document.getElementById('verified_etin_data').value = JSON.stringify(response.data);

                            //document.getElementById('etinSaveContinueBtn').classList.remove('hidden');
                        });
                    }
                },
                error: function (jqHR, textStatus, errorThrown) {
                    info_err.hide().empty();
                    info_err.removeClass('hidden').html(errorThrown);
                    $("#ETINResponseCountMsg").slideUp('slow', function () {
                        info_err.slideDown('slow');
                        // info_err.delay(1000).slideUp(1000, function () {
                        //     $('#ETINVerifyModal').modal('hide');
                        // });
                    });
                    // On modal close, disable ETIN verification calling.
                    clearTimeout(setRecursionForETINVerification);
                },
                beforeSend: function () {
                    info_err.hide().empty();
                }
            });
        } else {
            $("#ETINResponseCountMsg").slideUp('slow', function () {
                info_err.hide().empty();
                info_err.removeClass('hidden').html('The e-tin number must be between 10 and 15 digits.');
                info_err.slideDown('slow');
                // info_err.delay(1000).slideUp(1000, function () {
                //     $('#ETINVerifyModal').modal('hide');
                // });
            });

        }
    }

    /**
     * This variable will used to time counting in ETIN Verification.
     * we should declare the variable at first, for global scope.
     *
     * But, data will be assigned before verifyETIN() function calling.
     */
    var submit_time;

    function ETINVerifyTimeCount() {
        var date1 = new Date();
        /**
         * submit_time must need to declared before verifyETIN() calling
         *
         */
        return Math.floor((date1 - submit_time) / 1000);
    }

    /**
     * Passport image preset info
     */
    var cropper;
    var canvas;
    var is_passport_croped;
    var image = document.getElementById('passport_upload_view');

    /**
     * Passport image removed/ reset
     */
    $("#passport_reset_btn").click(function () {

        cropper.destroy();
        cropper = null;

        $('#passport_upload_base_code').val('');
        $('#passport_file_name').val('');
        $('#passport_upload_manual_file').val('');
        $('#passport_cropped_result').html('');
        $('#passport_upload').val('');

        $('#passport_upload_view').attr('src', '#');
        $('div.cropper-container').remove();

        document.getElementById('passport_upload_view_div').style.display = 'block';
        document.getElementById('passport_edit_btn').style.display = 'none';
        document.getElementById('passport_crop_btn').style.display = 'block';
        document.getElementById('passport_upload_view_wrapper').style.display = 'none';
        document.getElementById('passport_upload_div').style.display = 'block';
        $("#passport_verify").hide();
    });

    /**
     * Passport image edit/ resize option
     */
    $("#passport_edit_btn").click(function () {
        $("#passport_verify").hide();
        document.getElementById('passport_cropped_result').style.display = 'none';
        document.getElementById('passport_edit_btn').style.display = 'none';
        document.getElementById('passport_upload_view_div').style.display = 'block';
        document.getElementById('passport_crop_btn').style.display = 'block';
    });

    /**
     * Passport image crop and set value
     */
    $("#passport_crop_btn").click(function () {

        // If user don't crop the image then show the alert
        if (is_passport_croped == 'no') {
            swal({
                type: 'error',
                title: 'Oops...',
                text: 'Please follow the instruction shown on the right side, and position the rectangular properly to crop your proper passport image.'
            });
            return false;
        }

        document.getElementById('passport_preloader').style.display = 'block';
        document.getElementById('passport_upload_view_div').style.display = 'none';
        document.getElementById('passport_crop_btn').style.display = 'none';

        canvas =  cropper.getCroppedCanvas({
            width: 1492,
            height: 2087,
        }).toDataURL('image/jpeg', 0.7); // 1.0 = image full quality
        //console.log(canvas);
        var passport_file_name = $('#passport_file_name').val();

        $('#passport_upload_base_code').val(canvas);

        document.getElementById('passport_cropped_result').style.display = 'block';
        $('#passport_cropped_result').html('');
        $('#passport_cropped_result').html('<div style="padding: 10px 15px;" class="panel-heading"><h3 class="panel-title">File name: '+passport_file_name+'</h3></div><div class="panel-body"><img alt="Passport copy" src="'+canvas+'" /></div>');
        document.getElementById('passport_edit_btn').style.display = 'block';

        // magnify
        $('#magnify_image_large').css({'background-image': 'url(' + canvas + ')', 'background-repeat': 'no-repeat'});
        $('#magnify_image_small').attr('src', canvas);

        $("#passport_verify").show();

        toastr.success('Passport cropped and resized.');
        document.getElementById('passport_preloader').style.display = 'none';
    });

    /**
     * Passport image initialization for crop
     */
    function initPassportCropper() {
        cropper = new Cropper(image, {
            viewMode: 1,
            ready: function () {
                is_passport_croped = 'no';
            },
            crop: function(e) {
                is_passport_croped = 'yes';
                //crop_data_info.textContent = JSON.stringify(cropper.getData(true));
            }
        });

        document.getElementById('passport_preloader').style.display = 'none';
    }

    /**
     * Passport image upload
     * @param input_data
     */
    function getPassportImage(input_data) {

        $('.no-js').css('overflow', 'auto');
        if (input_data.files && input_data.files[0]) {

            $("#passport_upload_error").html('');

            // validated image height and width
            var _URL = window.URL || window.webkitURL;
            var image = new Image();
            image.src = _URL.createObjectURL(input_data.files[0]);
            image.onload = function() {
                if ((4500 < this.height || 3500 < this.width) || (1043 > this.height || 746 > this.width)) {
                    $("#passport_upload_error").html('<div class="alert alert-warning fade in" role="alert"><h4>Warning! Better check yourself. </h4><p>The passport image resolution minimum <strong>746X1043</strong> pixel and maximum <strong>3500X4500</strong> pixel is suitable for verify. You have given <strong>'+this.width+'x'+this.height+' </strong> pixel.</p></div>');
                }
            };

            // Validate image type
            var mime_type = input_data.files[0].type;
            if (!(mime_type == 'image/jpeg' || mime_type == 'image/jpg' || mime_type == 'image/png')) {
                $("#passport_upload_error").html('<div class="alert alert-danger fade in" role="alert"><h4>Oh snap! You got an error!</h4><p>The passport format is not valid. Only PNG, JPEG, or JPG type is allowed.</p></div>');
                return false;
            }

            // validated image size
            if (!(input_data.files[0].size <= 5242880)) { // 5mb = 5242880, 1mb = 1048576
                $("#passport_upload_error").html('<div class="alert alert-danger fade in" role="alert"><h4>Oh snap! You got an error!</h4><p>The passport size maximum of 5 MB.</p></div>');
                return false;
            }

            document.getElementById('passport_upload_div').style.display = 'none';
            document.getElementById('passport_preloader').style.display = 'block';

            var reader = new FileReader();
            reader.onload = function (e) {
                $('#passport_upload_view').attr('src', e.target.result);
                $('#passport_upload_base_code').val(e.target.result);
                $('#passport_file_name').val(input_data.files[0].name);
                //console.log(e.target.result);
                // manual file
                $('#passport_upload_manual_file').val(e.target.result);

                // magnify start
                $('#magnify_image_large').css({'background-image': 'url(' + e.target.result + ')', 'background-repeat': 'no-repeat'});
                $('#magnify_image_small').attr('src', e.target.result);
                // magnify end

                document.getElementById('passport_upload_view_wrapper').style.display = 'block';
            };
            reader.readAsDataURL(input_data.files[0]);

            setTimeout(initPassportCropper, 1000);
        }
    }

    $(document).ready(function(){

        $('#passport_error_retry').on('click', function () {
            $('#PassportErrorModal').modal('hide');
            $('#passport_reset_btn').trigger('click');
            $('#openDirectorModal').css('overflow', 'auto');
        });

        $('#passport_error_manual').on('click', function () {
            $('#PassportErrorModal').modal('hide');
            $('#openDirectorModal').css('overflow', 'auto');
            // file name
            var manual_file_name = $('#passport_file_name').val();
            var manual_file_data = $('#passport_upload_manual_file').val();
            $('#passport_file_name_show').text(manual_file_name);

            // magnify start
            $('#magnify_image_large').css({'background-image': 'url(' + manual_file_data + ')', 'background-repeat': 'no-repeat'});
            $('#magnify_image_small').attr('src', manual_file_data);
            // magnify end

            document.getElementById('passport_upload_wrapper').style.display = 'none';
            $("#preloader").hide().html('');
            document.getElementById('passport_div_verification').style.display = 'block';
            $('#passport_verify').hide();
            $('#passport_save').show();
            $('#passport_save_close').show();
        });

        $('#passport_verify').on('click', function() {

            $("#preloader").show().html('<i class="fas fa-spinner fa-pulse"></i>');

            var file_data = $('#passport_upload_base_code').val(); // crop or origin image
            //var file_data = $('#passport_upload_manual_file').val(); // only origin image

            if(!file_data){
                return false;
            }

            $.ajax({
                url: '/signup/getPassportData',
                type: 'POST',
                dataType: 'text',           // what to expect back from the PHP script, if anything
                data: {
                    _token: $('input[name="_token"]').val(),
                    file: file_data,
                    photo: 'yes',
                },
                success: function(response) {
                    //console.log(response);
                    var obj = JSON.parse(response);

                    if (obj.code =='200') {

                        document.getElementById('passport_no').value = obj.data.document_number;
//                            document.getElementById('passport_type').value = obj.data.document_type;
                        $("#passport_nationality").val(obj.nationality_id).change();
                        $("#passport_type").val(obj.data.document_type.toLowerCase()).change();

                        var passport_dob = new Date(obj.data.birth_date);
                        var expiry_date = new Date(obj.data.expiry_date);

                        document.getElementById('passport_surname').value = obj.data.surname;
                        document.getElementById('passport_given_name').value = obj.data.name;
                        document.getElementById('passport_personal_no').value = obj.data.optional_data;
                        document.getElementById('passport_DOB').value = passport_dob.getDate() + "-" + passport_dob.toLocaleString('default', {month: 'short'}) + "-" + passport_dob.getUTCFullYear();
                        document.getElementById('passport_date_of_expire').value = expiry_date.getDate() + "-" + expiry_date.toLocaleString('default', {month: 'short'}) + "-" + expiry_date.getUTCFullYear();
                        //gender checked
                        if (obj.data.sex == 'M'){
                            $('input:radio[name="gender"][value="male"]').attr('checked',true);
                        }else {
                            $('input:radio[name="gender"][value="female"]').attr('checked',true);
                        }

                        document.getElementById('passport_upload_wrapper').style.display = 'none';
                        $("#preloader").hide().html('');
                        document.getElementById('passport_div_verification').style.display = 'block';
                        $('#passport_verify').hide();
                        // file name
                        var pass_file_name = $('#passport_file_name').val();

                        // empty manual file
                        $('#passport_upload_manual_file').val('');

                        $('#passport_file_name_show').text(pass_file_name);
                        $('#passport_save').show();
                        $('#passport_save_close').show();
                    } else {
                        $("#preloader").hide().html('');
                        $('#PassportErrorModal').modal('show');
                    }
                }
            });
        });

        // passport magnify
        var native_width = 0;
        var native_height = 0;
        var loadLocker = true;
        var image_object = null;

        // Now the mousemove function
        $(".magnify").mousemove(function (e) {
            if (!native_width && !native_height) {
                if (loadLocker) {
                    loadLocker = false;
                    image_object = new Image();
                    image_object.src = $(this).children(".small").attr("src");
                }

                native_width = image_object.width;
                native_height = image_object.height;
            } else {
                var magnify_offset = $(this).offset();
                var mx = e.pageX - magnify_offset.left;
                var my = e.pageY - magnify_offset.top;

                if (mx < $(this).width() && my < $(this).height() && mx > 0 && my > 0) {
                    $(this).children(".large").fadeIn(100);
                } else {
                    $(this).children(".large").fadeOut(100);
                }
                if ($(this).children(".large").is(":visible")) {
                    var rx = Math.round(mx / $(this).children(".small").width() * native_width - $(this).children(".large").width() / 2) * -1;
                    var ry = Math.round(my / $(this).children(".small").height() * native_height - $(this).children(".large").height() / 2) * -1;
                    var bgp = rx + "px " + ry + "px";
                    var px = mx - $(this).children(".large").width() / 2;
                    var py = my - $(this).children(".large").height() / 2;
                    $(this).children(".large").css({left: px, top: py, backgroundPosition: bgp});
                }
            }
        }).on("mouseleave", function () {
            native_width = 0;
            native_height = 0;
            loadLocker = true;
        });

    });

    /**
     * reset modal and value ...
     */
    function closeModal() {
        // Clear div of NID Verification Response
        document.getElementById('NIDVerificationResponse').innerHTML = '';

        // Reset NID info, name, gender, dob
        document.getElementById('nid_name').innerHTML = '';
        document.getElementById('nid_dob').innerHTML = '';

        // On modal close, disable NID verification calling.
        clearTimeout(setRecursionForNIDVerification);

        // Clear div of ETIN Response count Message
        document.getElementById('ETINResponseCountMsg').innerHTML = '';
        // Reset ETIN info, name, dob
        document.getElementById('etin_name').innerHTML = '';
        document.getElementById('etin_dob').innerHTML = '';
        // Reset ETIN info, name, dob

        // On modal close, disable NID verification calling.
        clearTimeout(setRecursionForNIDVerification);


    }

</script>
