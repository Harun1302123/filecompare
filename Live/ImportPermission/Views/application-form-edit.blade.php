<?php
$accessMode = ACL::getAccsessRight('ImportPermission');
if (!ACL::isAllowed($accessMode, $mode)) {
    die('You have no access right! Please contact with system admin if you have any query.');
}
?>
{{--Step css--}}
<link rel="stylesheet" href="{{ url('assets/css/jquery.steps.css') }}">
{{--Select2 css--}}
<link rel="stylesheet" href="{{ asset("assets/plugins/select2.min.css") }}">
{{--croppie css--}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.min.css">
{{--Datepicker css--}}
<link rel="stylesheet" href="{{ asset("vendor/datepicker/datepicker.min.css") }}">

<style>
    .form-group {
        margin-bottom: 2px;
    }

    .table {
        margin-bottom: 5px;
    }

    textarea {
        resize: vertical;
    }

    .wizard > .steps > ul > li {
        width: 16% !important;
    }

    .wizard > .steps > ul > li a {
        padding: 0.5em 0.5em !important;
    }

    .wizard > .steps .number {
        font-size: 1.2em;
    }

    .wizard > .actions {
        top: -15px;
    }

    .wizard {
        overflow: visible;
    }

    .wizard > .content {
        overflow: visible;
    }

    .help-text {
        font-size: small;
    }

    .img-signature {
        height: 80px !important;
        width: 300px;
    }

    .img-user {
        width: 120px;
        height: 120px;
        float: right;
    }

    input[type=radio].error,
    input[type=checkbox].error {
        outline: 1px solid red !important;
    }

    .table-striped > tbody#manpower > tr > td, .table-striped > tbody#manpower > tr > th {
        text-align: center;
    }

    .table-responsive {
        overflow-x: visible;
    }


    @media (min-width: 992px) {
        .modal-lg {
            width: 1020px;
        }
    }
    .blink_me {
        animation: blinker 5s linear infinite;
    }

    .dateSpace {
        min-width: 3rem !important;
    }

    @keyframes blinker {
        50% { opacity: .5; }
    }

    .readonly-pointer-disabled {
        cursor: pointer;
        pointer-events: none;
        background-color: #eee; /* Optional: Adding a background color to visually indicate readonly state */
    }

</style>

<section class="content" id="applicationForm">
    @include('ProcessPath::remarks-modal')
    <div class="col-md-12">
        <div class="box">
            <div class="box-body" id="inputForm">
                {{--start application form with wizard--}}
                {!! Session::has('success') ? '
                <div class="alert alert-info alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("success") .'</div>
                ' : '' !!}
                {!! Session::has('error') ? '
                <div class="alert alert-danger alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("error") .'</div>
                ' : '' !!}

                <div class="panel panel-info">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h5><strong>Application for Import Permission</strong></h5>
                        </div>
                        <div class="pull-right">
                            @if(in_array($appInfo->status_id,[5]))
                                <a data-toggle="modal" data-target="#remarksModal">
                                    {!! Form::button('<i class="fa fa-eye"></i>Reason of '.$appInfo->status_name.'', array('type' => 'button', 'class' => 'btn btn-md btn-danger')) !!}
                                </a>
                            @endif
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-body">

                        <div>
                            {!! Form::open(array('url' => 'import-permission/add','method' => 'post','id' => 'ImportPermissionForm','role'=>'form','enctype'=>'multipart/form-data')) !!}

                            {{-- Required hidden field for Applicaion category wise document load --}}
                            <input type="hidden" id="viewMode" name="viewMode" value="{{ $viewMode }}">
                            <input type="hidden" id="openMode" name="openMode" value="edit">
                            {!! Form::hidden('app_id', Encryption::encodeId($appInfo->id) ,['class' => 'form-control input-md required', 'id'=>'app_id']) !!}
                            {!! Form::hidden('reg_no', $appInfo->reg_no ,['class' => 'form-control input-md']) !!}

                            <input type="hidden" value="{{$usdValue->bdt_value}}" id="crvalue">
                            {!! Form::hidden('curr_process_status_id', $appInfo->status_id,['class' => 'form-control input-md required', 'id'=>'process_status_id']) !!}

                            {{-- Required Hidden field for Ajax file upload --}}
                            <input type="hidden" name="selected_file" id="selected_file"/>
                            <input type="hidden" name="validateFieldName" id="validateFieldName"/>
                            <input type="hidden" name="isRequired" id="isRequired"/>
                            <input type="hidden" name="multipleAttachment" id="multipleAttachment"/>

                            <h3 class="stepHeader">Basic Information</h3>
                            <fieldset>
                                @if($appInfo->status_id == 5 && (!empty($appInfo->resend_deadline)))
                                    <div class="form-group blink_me">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="alert btn-danger" role="alert">
                                                    You must re-submit the application before <strong>{{ date("d-M-Y", strtotime($appInfo->resend_deadline)) }}</strong>, otherwise, it will be automatically rejected.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="clearfix"></div>
                                <div class="panel panel-info">
                                    <div class="panel-heading "><strong>Basic Information</strong></div>
                                    <div class="panel-body">
                                        {{-- <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-12 {{$errors->has('last_br') ? 'has-error': ''}}">
                                                    {!! Form::label('last_br','Did you receive your BIDA Registration/ BIDA Registration amendment approval online OSS?',['class'=>'col-md-6 text-left required-star']) !!}
                                                    <div class="col-md-6">
                                                        @if($appInfo->last_br == 'yes')
                                                            <label class="radio-inline">{!! Form::radio('last_br','yes', ($appInfo->last_br == 'yes' ? true :false), ['class'=>'cusReadonly required helpTextRadio', 'id'=>'last_br_yes']) !!}
                                                                Yes
                                                            </label>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}

                                        <div class="form-group">
                                            <div class="row">
                                                    <div id="ref_app_tracking_no_div" class="col-md-12 {{$errors->has('ref_app_tracking_no') ? 'has-error': ''}}">
                                                        {!! Form::label('ref_app_tracking_no','Please give your approved BIDA Registration/ BIDA Registration amendment Tracking ID.',['class'=>'col-md-6 text-left required-star']) !!}
                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                {!! Form::hidden('ref_app_tracking_no', $appInfo->ref_app_tracking_no, ['class' => 'form-control input-sm required']) !!}
                                                                <a href="{{ CommonFunction::getUrlByTrackingNo($appInfo->ref_app_tracking_no) }}" target="_blank">
                                                                    <span class="label label-success" style="font-size: 15px">{{ (empty($appInfo->ref_app_tracking_no) ? 'N/A' : $appInfo->ref_app_tracking_no) }}</span>
                                                                </a>
                                                                <br/>
                                                                <small class="text-danger">
                                                                    N.B.: Once you save or submit the application, the BIDA Registration tracking no cannot be changed anymore.
                                                                </small>
                                                            </div>
                                                        </div>

                                                        {{-- this section added by atik for date showing issue --}}
                                                        {!! Form::label('ref_app_approve_date','Approved Date', ['class'=>'col-md-6 text-left required-star']) !!}
                                                        <div class="col-md-6">
                                                            <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                                {!! Form::text('ref_app_approve_date', (empty($appInfo->ref_app_approve_date)) ? '' : date('d-M-Y', strtotime($appInfo->ref_app_approve_date)), ['class'=>'form-control input-md datepicker', 'id' => 'ref_app_approve_date', 'placeholder'=>'Pick from datepicker','readonly']) !!}
                                                            </div>
                                                        </div>
                                                        {{-- this section added by atik for date showing issue --}}
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <h3 class="stepHeader">Registration Info</h3>
                            <fieldset>

                                {{--Company Information--}}
                                <div class="panel panel-info">
                                    <div class="panel-heading margin-for-preview"><strong>A. Company Information</strong></div>
                                    <div class="panel-body">
                                        <div class="readOnlyCl">
                                            <div id="validationError"></div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6 form-group {{$errors->has('company_name') ? 'has-error': ''}}">
                                                        {!! Form::label('company_name','Name of the Organization/ Company/ Industrial Project',['class'=>'col-md-12 text-left']) !!}
                                                        <div class="col-md-12">
                                                            {!! Form::text('company_name', $appInfo->company_name, ['class' => 'form-control input-md', 'readonly']) !!}
                                                            {!! $errors->first('company_name','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 form-group {{$errors->has('company_name_bn') ? 'has-error': ''}}">
                                                        {!! Form::label('company_name_bn','Name of the Organization/ Company/ Industrial Project (বাংলা)',['class'=>'col-md-12 text-left']) !!}
                                                        <div class="col-md-12">
                                                            {!! Form::text('company_name_bn', $appInfo->company_name_bn, ['class' => 'form-control input-md', 'readonly']) !!}
                                                            {!! $errors->first('company_name_bn','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6 {{$errors->has('organization_type_id') ? 'has-error': ''}}">
                                                        {!! Form::label('organization_type_id','Type of the organization',['class'=>'col-md-12 text-left']) !!}
                                                        <div class="col-md-12">
                                                            {{-- {!! Form::select('organization_type_id', $eaOrganizationType, $appInfo->organization_type_id, ['class' => 'form-control  input-md ','id'=>'organization_type_id']) !!} --}}
                                                            {{-- {!! Form::select('organization_type_id', $eaOrganizationType, $appInfo->organization_type_id, ['class' => 'form-control input-md', 'id' => 'organization_type_id', 'disabled' => 'disabled']) !!} --}}
                                                            {!! Form::select('organization_type_id', $eaOrganizationType, $appInfo->organization_type_id, ['class' => 'form-control  input-md readonly-pointer-disabled','id'=>'organization_type_id']) !!}
                                                            {!! $errors->first('organization_type_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 {{$errors->has('organization_status_id') ? 'has-error': ''}}">
                                                        {!! Form::label('organization_status_id','Status of the organization',['class'=>'col-md-12 text-left required-star']) !!}
                                                        <div class="col-md-12">
                                                            {!! Form::select('organization_status_id', $eaOrganizationStatus, $appInfo->organization_status_id, ['class' => 'form-control input-md required readonly-pointer-disabled','id'=>'organization_status_id']) !!}
                                                            {!! $errors->first('organization_status_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6 {{$errors->has('ownership_status_id') ? 'has-error': ''}}">
                                                        {!! Form::label('ownership_status_id','Ownership status',['class'=>'col-md-12 text-left']) !!}
                                                        <div class="col-md-12">
                                                            {!! Form::select('ownership_status_id', $eaOwnershipStatus, $appInfo->ownership_status_id, ['class' => 'form-control  input-md readonly-pointer-disabled','id'=>'ownership_status_id']) !!}
                                                            {!! $errors->first('ownership_status_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 country_of_origin_div">
                                                        {!! Form::label('country_of_origin_id','Country of origin',['class'=>'col-md-12 text-left']) !!}
                                                        <div class="col-md-12">
                                                            {!! Form::select('country_of_origin_id',$countriesWithoutBD, $appInfo->country_of_origin_id,['class'=>'form-control input-md readonly-pointer-disabled', 'id' => 'country_of_origin_id', "style" => "width: 100%"]) !!}
                                                            {!! $errors->first('country_of_origin_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12 {{$errors->has('project_name') ? 'has-error': ''}}">
                                                        {!! Form::label('project_name','Name of the project',['class'=>'col-md-12 text-left required-star']) !!}
                                                        <div class="col-md-12">
                                                            {!! Form::text('project_name', $appInfo->project_name, ['class' => 'form-control required input-md ','id'=>'project_name','readonly']) !!}
                                                            {!! $errors->first('project_name','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="form-group col-md-12 {{$errors->has('business_class_code') ? 'has-error' : ''}}">
                                                        {!! Form::label('business_class_code','Business Sector (BBS Class Code)',['class'=>'col-md-12 required-star']) !!}
                                                        <div class="col-md-12">
                                                            {!! Form::text('business_class_code', $appInfo->class_code, ['class' => 'form-control required input-md', 'min' => 4,'onkeyup' => 'findBusinessClassCode()','readonly']) !!}
                                                            <input type="hidden" name="is_valid_bbs_code" id="is_valid_bbs_code" value="{{ empty($appInfo->class_code) ? 0 : 1 }}" />
                                                            <span class="help-text" style="margin: 5px 0;">

                                                        </span>
                                                            {!! $errors->first('business_class_code','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div id="no_business_class_result"></div>

                                                        <fieldset class="scheduler-border hidden" id="business_class_list_sec">
                                                            <legend class="scheduler-border">Other info. based on your business class (Code = <span id="business_class_list_of_code"></span>)</legend>
                                                            <table class="table table-striped table-bordered">
                                                                <thead class="alert alert-info">
                                                                <tr>
                                                                    <th>Category</th>
                                                                    <th>Code</th>
                                                                    <th>Description</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody id="business_class_list"></tbody>
                                                            </table>
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="modal fade" id="businessClassModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content load_business_class_modal"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="form-group col-md-12 {{$errors->has('major_activities') ? 'has-error' : ''}}">
                                                        {!! Form::label('major_activities','Major activities in brief',['class'=>'col-md-12']) !!}
                                                        <div class="col-md-12">
                                                            {!! Form::textarea('major_activities', $appInfo->major_activities, ['class' => 'form-control input-md maxTextCountDown', 'size' =>'5x2','data-rule-maxlength'=>'240', 'placeholder' => 'Maximum 240 characters', "data-charcount-maxlength" => "240",'readonly']) !!}
                                                            {!! $errors->first('major_activities','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="panel panel-info">
                                    <div class="panel-heading"><strong>B. Information of Principal Promoter/Chairman/Managing Director/CEO/Country Manager</strong></div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('ceo_country_id') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_country_id','Country ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('ceo_country_id', $countries, $appInfo->ceo_country_id, ['class' => 'form-control input-md readonly-pointer-disabled','id'=>'ceo_country_id', "style" => "width: 100%"]) !!}
                                                        {!! $errors->first('ceo_country_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_dob') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_dob','Date of Birth',['class'=>'col-md-5']) !!}
                                                    <div class=" col-md-7">
                                                        <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                            {!! Form::text('ceo_dob', (empty($appInfo->ceo_dob) ? '' : date('d-M-Y', strtotime($appInfo->ceo_dob))), ['class'=>'form-control input-md datepicker date', 'id' => 'ceo_dob', 'placeholder'=>'Pick from datepicker' ,'readonly']) !!}
                                                        </div>
                                                        {!! $errors->first('ceo_dob','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div id="ceo_passport_div" class="col-md-6 hidden {{$errors->has('ceo_passport_no') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_passport_no','Passport No.',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_passport_no', $appInfo->ceo_passport_no, ['maxlength'=>'20', 'class' => 'form-control input-md', 'id'=>'ceo_passport_no' ,'readonly']) !!}
                                                        {!! $errors->first('ceo_passport_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div id="ceo_nid_div" class="col-md-6 {{$errors->has('ceo_nid') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_nid','NID No.',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_nid', $appInfo->ceo_nid, ['maxlength'=>'20', 'class' => 'form-control input-md bd_nid','id'=>'ceo_nid' ,'readonly']) !!}
                                                        {!! $errors->first('ceo_nid','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_designation') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_designation','Designation', ['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_designation', $appInfo->ceo_designation, ['maxlength'=>'80','class' => 'form-control input-md' ,'readonly']) !!}
                                                        {!! $errors->first('ceo_designation','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('ceo_full_name') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_full_name','Full Name',['class'=>'col-md-5 text-left required-star']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_full_name', $appInfo->ceo_full_name, ['maxlength'=>'80',
                                                        'class' => 'form-control input-md required' ,'readonly']) !!}
                                                        {!! $errors->first('ceo_full_name','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div id="ceo_district_div"
                                                     class="col-md-6 {{$errors->has('ceo_district_id') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_district_id','District/City/State ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('ceo_district_id',$districts, $appInfo->ceo_district_id, ['maxlength'=>'80','class' => 'form-control input-md readonly-pointer-disabled', "style" => "width: 100%"]) !!}
                                                        {!! $errors->first('ceo_district_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div id="ceo_city_div" class="col-md-6 hidden {{$errors->has('ceo_city') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_city','City',['class'=>'text-left  col-md-5']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_city', $appInfo->ceo_city,['class' => 'form-control input-md' ,'readonly']) !!}
                                                        {!! $errors->first('ceo_city','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div id="ceo_state_div"
                                                     class="col-md-6 hidden {{$errors->has('ceo_state') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_state','State / Province',['class'=>'text-left  col-md-5']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_state', $appInfo->ceo_state,['class' => 'form-control input-md' ,'readonly']) !!}
                                                        {!! $errors->first('ceo_state','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div id="ceo_thana_div"
                                                     class="col-md-6 {{$errors->has('ceo_thana_id') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_thana_id','Police Station/Town ',['class'=>'col-md-5 text-left','placeholder'=>'Select district first']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('ceo_thana_id',[], $appInfo->ceo_thana_id, ['maxlength'=>'80','class' => 'form-control input-md readonly-pointer-disabled','placeholder' => 'Select district first', "style" => "width: 100%"]) !!}
                                                        {!! $errors->first('ceo_thana_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_post_code') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_post_code','Post/Zip Code ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_post_code', $appInfo->ceo_post_code, ['maxlength'=>'80','class' => 'form-control input-md engOnly' ,'readonly']) !!}
                                                        {!! $errors->first('ceo_post_code','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('ceo_address') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_address','House,Flat/Apartment,Road ',['class'=>'col-md-5 text-left']) !!}

                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_address', $appInfo->ceo_address, ['maxlength'=>'150','class' => 'BigInputField form-control input-md' ,'readonly']) !!}
                                                        {!! $errors->first('ceo_address','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_telephone_no') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_telephone_no','Telephone No.',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_telephone_no', $appInfo->ceo_telephone_no, ['maxlength'=>'20','class' => 'form-control input-md helpText15 phone_or_mobile' ,'readonly']) !!}
                                                        {!! $errors->first('ceo_telephone_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('ceo_mobile_no') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_mobile_no','Mobile No. ',['class'=>'col-md-5 text-left required-star']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_mobile_no', $appInfo->ceo_mobile_no, ['class' => 'form-control input-md helpText15 phone_or_mobile required' ,'readonly']) !!}
                                                        {!! $errors->first('ceo_mobile_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_father_name') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_father_name','Father\'s Name',['class'=>'col-md-5 text-left', 'id' => 'ceo_father_label']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_father_name', $appInfo->ceo_father_name, ['class' => 'form-control textOnly input-md' ,'readonly']) !!}
                                                        {!! $errors->first('ceo_father_name','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('ceo_email') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_email','Email ',['class'=>'col-md-5 text-left required-star']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_email', $appInfo->ceo_email, ['class' => 'form-control email input-md required' ,'readonly']) !!}
                                                        {!! $errors->first('ceo_email','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_mother_name') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_mother_name','Mother\'s Name',['class'=>'col-md-5 text-left', 'id' => 'ceo_mother_label']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_mother_name', $appInfo->ceo_mother_name, ['class' => 'form-control textOnly input-md' ,'readonly']) !!}
                                                        {!! $errors->first('ceo_mother_name','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('ceo_fax_no') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_fax_no','Fax No. ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_fax_no', $appInfo->ceo_fax_no, ['class' => 'form-control input-md' ,'readonly']) !!}
                                                        {!! $errors->first('ceo_fax_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_spouse_name') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_spouse_name','Spouse name',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_spouse_name', $appInfo->ceo_spouse_name, ['class' => 'form-control textOnly input-md' ,'readonly']) !!}
                                                        {!! $errors->first('ceo_spouse_name','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('ceo_gender') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_gender','Gender', ['class'=>'col-md-5 text-left required-star']) !!}
                                                    <div class="col-md-7">
                                                        <label class="radio-inline">
                                                            {!! Form::radio('ceo_gender', 'Male', !empty($appInfo->ceo_gender) && $appInfo->ceo_gender == "Male", ['class'=>'required' ,'disabled' => 'disabled' ]) !!}
                                                            Male
                                                        </label>
                                                        <label class="radio-inline">
                                                            {!! Form::radio('ceo_gender', 'Female', !empty($appInfo->ceo_gender) && $appInfo->ceo_gender == "Female", ['class'=>'required','disabled' => 'disabled' ]) !!}
                                                            Female
                                                        </label>
                                                        <input type="hidden" name="ceo_gender" value="{{$appInfo->ceo_gender}}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="panel panel-info">
                                    <div class="panel-heading"><strong>C. Office Address</strong></div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('office_division_id') ? 'has-error': ''}}">
                                                    {!! Form::label('office_division_id','Division',['class'=>'text-left col-md-5 required-star']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('office_division_id', $divisions, $appInfo->office_division_id, ['class' => 'form-control imput-md required readonly-pointer-disabled','id' => 'office_division_id']) !!}
                                                        {!! $errors->first('office_division_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('office_district_id') ? 'has-error': ''}}">
                                                    {!! Form::label('office_district_id','District',['class'=>'col-md-5 text-left required-star']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('office_district_id', $districts, $appInfo->office_district_id, ['class' => 'form-control input-md required readonly-pointer-disabled', "style" => "width: 100%"]) !!}
                                                        {!! $errors->first('office_district_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('office_thana_id') ? 'has-error': ''}}">
                                                    {!! Form::label('office_thana_id','Police Station', ['class'=>'col-md-5 text-left required-star']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('office_thana_id',[''], $appInfo->office_thana_id, ['class' => 'form-control input-md required readonly-pointer-disabled', "style" => "width: 100%"]) !!}
                                                        {!! $errors->first('office_thana_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('office_post_office') ? 'has-error': ''}}">
                                                    {!! Form::label('office_post_office','Post Office',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('office_post_office', $appInfo->office_post_office, ['class' => 'form-control input-md' ,'readonly']) !!}
                                                        {!! $errors->first('office_post_office','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('office_post_code') ? 'has-error': ''}}">
                                                    {!! Form::label('office_post_code','Post Code', ['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('office_post_code', $appInfo->office_post_code, ['class' => 'form-control input-md alphaNumeric' ,'readonly']) !!}
                                                        {!! $errors->first('office_post_code','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('office_address') ? 'has-error': ''}}">
                                                    {!! Form::label('office_address','Address ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('office_address', $appInfo->office_address, ['maxlength'=>'150','class' => 'form-control input-md' ,'readonly']) !!}
                                                        {!! $errors->first('office_address','<span class="help-block">:message</span>') !!}

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('office_telephone_no') ? 'has-error': ''}}">
                                                    {!! Form::label('office_telephone_no','Telephone No.',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('office_telephone_no', $appInfo->office_telephone_no, ['maxlength'=>'20','class' => 'form-control input-md helpText15 phone_or_mobile' ,'readonly']) !!}
                                                        {!! $errors->first('office_telephone_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('office_mobile_no') ? 'has-error': ''}}">
                                                    {!! Form::label('office_mobile_no','Mobile No. ',['class'=>'col-md-5 text-left required-star']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('office_mobile_no', $appInfo->office_mobile_no, ['class' => 'form-control input-md helpText15 phone_or_mobile required' ,'readonly']) !!}
                                                        {!! $errors->first('office_mobile_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('office_fax_no') ? 'has-error': ''}}">
                                                    {!! Form::label('office_fax_no','Fax No. ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('office_fax_no', $appInfo->office_fax_no, ['class' => 'form-control input-md' ,'readonly']) !!}
                                                        {!! $errors->first('office_fax_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('office_email') ? 'has-error': ''}}">
                                                    {!! Form::label('office_email','Email ',['class'=>'col-md-5 text-left required-star']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('office_email', $appInfo->office_email, ['class' => 'form-control email input-md required' ,'readonly']) !!}
                                                        {!! $errors->first('office_email','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="panel panel-info">
                                    <div class="panel-heading"><strong>D. Factory Address(This would be IRC address)</strong></div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('factory_district_id') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_district_id','District',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('factory_district_id', $districts, $appInfo->factory_district_id, ['class' => 'form-control input-md readonly-pointer-disabled', "style" => "width: 100%"]) !!}
                                                        {!! $errors->first('factory_district_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('factory_thana_id') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_thana_id','Police Station', ['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('factory_thana_id',[''], $appInfo->factory_thana_id, ['class' => 'form-control input-md readonly-pointer-disabled', "style" => "width: 100%"]) !!}
                                                        {!! $errors->first('factory_thana_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('factory_post_office') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_post_office','Post Office',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('factory_post_office', $appInfo->factory_post_office, ['class' => 'form-control input-md' ,'readonly']) !!}
                                                        {!! $errors->first('factory_post_office','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('factory_post_code') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_post_code','Post Code', ['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('factory_post_code', $appInfo->factory_post_code, ['class' => 'form-control input-md number alphaNumeric' ,'readonly']) !!}
                                                        {!! $errors->first('factory_post_code','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('factory_address') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_address','Address ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('factory_address', $appInfo->factory_address, ['maxlength'=>'150','class' => 'form-control input-md' ,'readonly']) !!}
                                                        {!! $errors->first('factory_address','<span class="help-block">:message</span>') !!}

                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('factory_telephone_no') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_telephone_no','Telephone No.',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('factory_telephone_no', $appInfo->factory_telephone_no, ['maxlength'=>'20','class' => 'form-control input-md helpText15 phone_or_mobile' ,'readonly']) !!}
                                                        {!! $errors->first('factory_telephone_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('factory_mobile_no') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_mobile_no','Mobile No. ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('factory_mobile_no', $appInfo->factory_mobile_no, ['class' => 'form-control input-md helpText15' ,'readonly']) !!}
                                                        {!! $errors->first('factory_mobile_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('factory_fax_no') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_fax_no','Fax No. ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('factory_fax_no', $appInfo->factory_fax_no, ['class' => 'form-control input-md' ,'readonly']) !!}
                                                        {!! $errors->first('factory_fax_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="panel panel-info">
                                    
                                    <div class="panel-body">
                                        {{--1. Project status--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">Desired office:</legend>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h4>You have selected <b>{{ $desire_office->des_office_name }}, </b>{{ $desire_office->des_office_address }} .</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                    </div>
                                </div>

                                <div class="panel panel-info">
                                    <div class="panel-heading "><strong>Registration Information</strong></div>
                                    <div class="panel-body">
                                        {{--1. Project status--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">1. Project status</legend>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6 {{$errors->has('project_status_id') ? 'has-error': ''}}">
                                                        {!! Form::label('project_status','Project status', ['class'=>'col-md-5 text-left']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::select('project_status_id', $projectStatusList, $appInfo->project_status_id, ["placeholder" => "Select One", 'class' => 'form-control input-md readonly-pointer-disabled']) !!}
                                                            {!! $errors->first('project_status_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>


                                        {{--2. Annual production capacity--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">2. Annual production capacity </legend>
                                            <fieldset id="annual_raw">
                                                <div class="panel panel-info">
                                                    <div class="panel-heading "><strong>Annual production capacity</strong></div>
                                                    <div class="panel-body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="table-responsive">
                                                                    <table id="productionCostTbl"
                                                                           class="table table-striped table-bordered dt-responsive"
                                                                           cellspacing="0" width="100%">
                                                                        <thead class="alert alert-info">
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th class="text-center ">Name of Product
                                                                                <span class="required-star"></span><br/>
                                                                            </th>

                                                                            <th class="text-center ">Unit of Quantity
                                                                                <span class="required-star"></span><br/>
                                                                            </th>

                                                                            <th class="text-center ">Quantity
                                                                                <span class="required-star"></span><br/>
                                                                            </th>

                                                                            <th class="text-center ">Price (USD)
                                                                                <span class="required-star"></span><br/>
                                                                            </th>

                                                                            <th class="text-center">
                                                                                Sales Value in BDT (million)
                                                                                <span class="required-star"></span><br/>
                                                                            </th>

                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        <?php $inc = 1; ?>
                                                                        @if(count($annualProductionCapacity)>0)
                                                                            @foreach($annualProductionCapacity as $eachProductionCap)
                                                                                <tr>
                                                                                    <td class="light-yellow">{{ $inc++ }}</td>
                                                                                    <td class="light-yellow">{{ $eachProductionCap->product_name }}</td>
                                                                                    <td class="light-yellow">{{ $eachProductionCap->quantity_unit }}</td>
                                                                                    <td class="light-yellow">{{ $eachProductionCap->quantity }}</td>
                                                                                    <td class="light-yellow">{{ $eachProductionCap->price_usd }}</td>
                                                                                    <td class="light-yellow">{{ $eachProductionCap->price_taka }}</td>

                                                                                </tr>
                                                                            @endforeach
                                                                        @endif
                                                                        </tbody>
                                                                    </table>
                                                                    <table>

                                                                        <tr>
                                                                            <div class="col-md-6">
                                                                                <span class="help-text" style="margin: 5px 0;">
                                                                                    Exchange Rate Ref: <a href="https://www.bangladesh-bank.org/econdata/exchangerate.php" target="_blank">Bangladesh Bank</a>.
                                                                                </span>
                                                                            </div>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </fieldset>
                                        </fieldset>


                                        {{--3. Date of commercial operation--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">3. Date of commercial operation</legend>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div id="date_of_arrival_div"
                                                         class="col-md-6 {{$errors->has('commercial_operation_date') ? 'has-error': ''}}">
                                                        {!! Form::label('commercial_operation_date','Date of commercial operation',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            <div class="input-group date">
                                                                {!! Form::text('commercial_operation_date', ((!empty($appInfo->commercial_operation_date )) ? date('d-M-Y', strtotime($appInfo->commercial_operation_date)) : ''), ['class' => 'form-control input-md datepicker date readonly-pointer-disabled', 'placeholder'=>'dd-mm-yyyy']) !!}
                                                            </div>
                                                            {!! $errors->first('commercial_operation_date','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--4. Sales (in 100%)--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">4. Sales (in 100%)</legend>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-4 {{$errors->has('local_sales') ? 'has-error': ''}}">
                                                        {!! Form::label('local_sales','Local ',['class'=>'col-md-4 text-left']) !!}
                                                        <div class="col-md-8">
                                                            {!! Form::text('local_sales', $appInfo->local_sales, ['class' => 'form-control input-md number', 'id'=>'local_sales_per' ,'readonly']) !!}
                                                            {!! $errors->first('local_sales','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 {{$errors->has('foreign_sales') ? 'has-error': ''}}">
                                                        {!! Form::label('foreign_sales','Foreign ',['class'=>'col-md-4 text-left']) !!}
                                                        <div class="col-md-8">
                                                            {!! Form::number('foreign_sales', $appInfo->foreign_sales, ['class' => 'form-control input-md number', 'id'=>'foreign_sales_per' ,'readonly']) !!}
                                                            {!! $errors->first('foreign_sales','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 {{$errors->has('total_sales') ? 'has-error': ''}}">
                                                        {!! Form::label('total_sales','Total in % ',['class'=>'col-md-4 text-left']) !!}
                                                        <div class="col-md-8">
                                                            {!! Form::number('total_sales', $appInfo->total_sales, ['class' => 'form-control input-md number', 'id'=>'total_sales' ,'readonly']) !!}
                                                            {!! $errors->first('total_sales','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--5. Manpower of the organization--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">5. Manpower of the organization</legend>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered" cellspacing="0"
                                                       width="100%">
                                                    <tbody id="manpower">
                                                    <tr>
                                                        <th class="alert alert-info" colspan="3">Local (Bangladesh
                                                            only)
                                                        </th>
                                                        <th class="alert alert-info" colspan="3">Foreign (Abroad
                                                            country)
                                                        </th>
                                                        <th class="alert alert-info" colspan="1">Grand total</th>
                                                        <th class="alert alert-info" colspan="2">Ratio</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="alert alert-info">Executive</th>
                                                        <th class="alert alert-info">Supporting stuff</th>
                                                        <th class="alert alert-info">Total (a)</th>
                                                        <th class="alert alert-info">Executive</th>
                                                        <th class="alert alert-info">Supporting stuff</th>
                                                        <th class="alert alert-info">Total (b)</th>
                                                        <th class="alert alert-info"> (a+b)</th>
                                                        <th class="alert alert-info">Local</th>
                                                        <th class="alert alert-info">Foreign</th>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {!! Form::text('local_male', $appInfo->local_male, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'local_male' ,'readonly']) !!}
                                                            {!! $errors->first('local_male','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('local_female', $appInfo->local_female, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'local_female' ,'readonly']) !!}
                                                            {!! $errors->first('local_female','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('local_total', $appInfo->local_total, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field numberNoNegative required','id'=>'local_total','readonly' ,'readonly']) !!}
                                                            {!! $errors->first('local_total','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('foreign_male', $appInfo->foreign_male, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'foreign_male' ,'readonly']) !!}
                                                            {!! $errors->first('foreign_male','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('foreign_female', $appInfo->foreign_female, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'foreign_female' ,'readonly']) !!}
                                                            {!! $errors->first('foreign_female','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('foreign_total', $appInfo->foreign_total, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field numberNoNegative required','id'=>'foreign_total','readonly' ,'readonly']) !!}
                                                            {!! $errors->first('foreign_total','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('manpower_total', $appInfo->manpower_total, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'mp_total','readonly' ,'readonly']) !!}
                                                            {!! $errors->first('manpower_total','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('manpower_local_ratio', $appInfo->manpower_local_ratio, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'mp_ratio_local','readonly' ,'readonly']) !!}
                                                            {!! $errors->first('manpower_local_ratio','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('manpower_foreign_ratio', $appInfo->manpower_foreign_ratio, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'mp_ratio_foreign','readonly' ,'readonly']) !!}
                                                            {!! $errors->first('manpower_foreign_ratio','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </fieldset>

                                        {{--6. Investment--}}
                                        <div id="investment_review">
                                            <fieldset class="scheduler-border">
                                                <legend class="scheduler-border">6. Investment</legend>
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered" cellspacing="0"
                                                           width="100%">
                                                        <thead>
                                                        <tr class="alert alert-info">
                                                            <th>Items</th>
                                                            <th colspan="2"></th>
                                                        </tr>
                                                        <tr>
                                                            <th>Fixed Investment</th>
                                                            <td colspan="2"></td>
                                                        </tr>
                                                        </thead>

                                                        <tbody id="annual_production_capacity">
                                                        <tr>
                                                            <td>
                                                                <div style="position: relative;">
                                                                    <span class="helpTextCom" id="investment_land_label">&nbsp; Land <small>(Million)</small></span>
                                                                </div>
                                                            </td>
                                                            <td colspan="2">
                                                                <table style="width:100%;">
                                                                    <tr>
                                                                        <td style="width:75%;">
                                                                            {!! Form::text('local_land_ivst', $viewMode == 'on' ? \App\Libraries\CommonFunction::convertToMillionAmount($appInfo->local_land_ivst) : $appInfo->local_land_ivst, ['data-rule-maxlength'=>'40','class' => 'form-control total_investment_item input-md number','id'=>'local_land_ivst',
                                                                             'onblur' => 'CalculateTotalInvestmentTk()'
                                                                             ,'readonly']) !!}
                                                                            {!! $errors->first('local_land_ivst','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                        <td>
                                                                            {!! Form::select("local_land_ivst_ccy", $currencies,$appInfo->local_land_ivst_ccy, ["placeholder" => "Select One","id"=>"local_land_ivst_ccy", "class" => "form-control input-md usd-def readonly-pointer-disabled"]) !!}
                                                                            {!! $errors->first('local_land_ivst_ccy','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div style="position: relative;">
                                                                <span class="helpTextCom"
                                                                      id="investment_building_label">&nbsp; Building <small>(Million)</small></span>
                                                                </div>
                                                            </td>
                                                            <td colspan="2">
                                                                <table style="width:100%;">
                                                                    <tr>
                                                                        <td style="width:75%;">
                                                                            {!! Form::text('local_building_ivst', $viewMode == 'on' ? \App\Libraries\CommonFunction::convertToMillionAmount($appInfo->local_building_ivst) : $appInfo->local_building_ivst, ['data-rule-maxlength'=>'40','class' => 'form-control input-md total_investment_item number','id'=>'local_building_ivst',
                                                                             'onblur' => 'CalculateTotalInvestmentTk()' ,'readonly']) !!}
                                                                            {!! $errors->first('local_building_ivst','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                        <td>
                                                                            {!! Form::select("local_building_ivst_ccy", $currencies, $appInfo->local_building_ivst_ccy, ["placeholder" => "Select One","id"=>"local_building_ivst_ccy", "class" => "form-control input-md usd-def readonly-pointer-disabled" ]) !!}
                                                                            {!! $errors->first('local_building_ivst_ccy','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div style="position: relative;">
                                                                <span class="required-star helpTextCom"
                                                                      id="investment_machinery_equp_label">&nbsp; Machinery & Equipment <small>(Million)</small></span>
                                                                </div>
                                                            </td>
                                                            <td colspan="2">
                                                                <table style="width:100%;">
                                                                    <tr>
                                                                        <td style="width:75%;">
                                                                            {!! Form::text('local_machinery_ivst', $viewMode == 'on' ? \App\Libraries\CommonFunction::convertToMillionAmount($appInfo->local_machinery_ivst) : $appInfo->local_machinery_ivst, ['data-rule-maxlength'=>'40','class' => 'form-control required input-md number total_investment_item','id'=>'local_machinery_ivst',
                                                                            'onblur' => 'CalculateTotalInvestmentTk()' ,'readonly']) !!}
                                                                            {!! $errors->first('local_machinery_ivst','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                        <td>
                                                                            {!! Form::select("local_machinery_ivst_ccy", $currencies, $appInfo->local_machinery_ivst_ccy, ["placeholder" => "Select One","id"=>"local_machinery_ivst_ccy", "class" => "form-control input-md usd-def readonly-pointer-disabled"]) !!}
                                                                            {!! $errors->first('local_machinery_ivst_ccy','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div style="position: relative;">
                                                                    <span class="helpTextCom" id="investment_others_label">&nbsp; Others <small>(Million)</small></span>
                                                                </div>
                                                            </td>
                                                            <td colspan="2">
                                                                <table style="width:100%;">
                                                                    <tr>
                                                                        <td style="width:75%;">
                                                                            {!! Form::text('local_others_ivst', $viewMode == 'on' ? \App\Libraries\CommonFunction::convertToMillionAmount($appInfo->local_others_ivst) : $appInfo->local_others_ivst, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number total_investment_item','id'=>'local_others_ivst',
                                                                            'onblur' => 'CalculateTotalInvestmentTk()' ,'readonly']) !!}
                                                                            {!! $errors->first('local_others_ivst','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                        <td>
                                                                            {!! Form::select("local_others_ivst_ccy", $currencies, $appInfo->local_others_ivst_ccy, ["placeholder" => "Select One","id"=>"local_others_ivst_ccy", "class" => "form-control input-md usd-def readonly-pointer-disabled"]) !!}
                                                                            {!! $errors->first('local_others_ivst_ccy','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="35%;">
                                                                <div style="position: relative;">
                                                                <span class="helpTextCom"
                                                                      id="investment_working_capital_label">&nbsp; Working Capital <small>(Three Months) (Million)</small></span>
                                                                </div>
                                                            </td>
                                                            <td colspan="2">
                                                                <table style="width:100%;">
                                                                    <tr>
                                                                        <td style="width:75%;">
                                                                            {!! Form::text('local_wc_ivst', $viewMode == 'on' ? \App\Libraries\CommonFunction::convertToMillionAmount($appInfo->local_wc_ivst) : $appInfo->local_wc_ivst, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number total_investment_item','id'=>'local_wc_ivst',
                                                                            'onblur' => 'CalculateTotalInvestmentTk()' ,'readonly']) !!}
                                                                            {!! $errors->first('local_wc_ivst','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                        <td>
                                                                            {!! Form::select("local_wc_ivst_ccy", $currencies, $appInfo->local_wc_ivst_ccy, ["placeholder" => "Select One","id"=>"local_wc_ivst_ccy", "class" => "form-control input-md usd-def readonly-pointer-disabled"]) !!}
                                                                            {!! $errors->first('local_wc_ivst_ccy','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div style="position: relative;">
                                                                <span class="helpTextCom"
                                                                      id="investment_total_invst_mi_label">&nbsp; Total Investment <small>(Million) (BDT)</small></span>
                                                                </div>
                                                            </td>
                                                            <td width="50%">
                                                                {!! Form::text('total_fixed_ivst_million', $viewMode == 'on' ? \App\Libraries\CommonFunction::convertToMillionAmount($appInfo->total_fixed_ivst_million) : $appInfo->total_fixed_ivst_million, ['data-rule-maxlength'=>'40','class' => 'form-control input-md numberNoNegative total_fixed_ivst_million required','id'=>'total_fixed_ivst_million','readonly']) !!}
                                                                {!! $errors->first('total_fixed_ivst_million','<span class="help-block">:message</span>') !!}
                                                            </td>
                                                            <td>
                                                                <div style="display: flex; align-items: center;">
                                                                    <div style="flex-grow: 1;" id="project_profile_label">Project&nbsp;profile:&nbsp;&nbsp;</div>
                                                                    <div style="flex-shrink: 0;">
                                                                        <input type="hidden" value="{{ $appInfo->project_profile_attachment }}" name="project_profile_attachment_data" id="project_profile_attachment_data">
                                                                        @if(!empty($appInfo->project_profile_attachment))

                                                                            <a style="margin-top: 5px;" target="_blank" class="btn btn-xs btn-primary" href="{{URL::to('/uploads/'.$appInfo->project_profile_attachment)}}">
                                                                                <i class="fa fa-file-pdf" aria-hidden="true"></i>
                                                                                Open File
                                                                            </a>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div style="position: relative;">
                                                                <span class="helpTextCom"
                                                                      id="investment_total_invst_bd_label">&nbsp; Total Investment <samall>(BDT)</samall></span>
                                                                </div>
                                                            </td>
                                                            <td colspan="3">
                                                                {!! Form::text('total_fixed_ivst', $viewMode == 'on' ? \App\Libraries\CommonFunction::convertToBdtAmount($appInfo->total_fixed_ivst) : $appInfo->total_fixed_ivst, ['data-rule-maxlength'=>'40','class' => 'form-control input-md numberNoNegative total_invt_bdt', 'id'=>'total_invt_bdt','readonly']) !!}
                                                                {!! $errors->first('total_fixed_ivst','<span class="help-block">:message</span>') !!}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div style="position: relative;">
                                                                <span class="helpTextCom required-starrequired-star"
                                                                      id="investment_total_invst_usd_label">&nbsp; Dollar exchange rate (USD)</span>
                                                                </div>
                                                            </td>
                                                            <td colspan="3">
                                                                {!! Form::number('usd_exchange_rate', $appInfo->usd_exchange_rate, ['data-rule-maxlength'=>'40','class' => 'form-control input-md numberNoNegative','id'=>'usd_exchange_rate','readonly']) !!}
                                                                {!! $errors->first('usd_exchange_rate','<span class="help-block">:message</span>') !!}
                                                                <span class="help-text">Exchange Rate Ref: <a
                                                                            href="https://www.bangladesh-bank.org/econdata/exchangerate.php"
                                                                            target="_blank">Bangladesh Bank</a>. Please Enter Today's Exchange Rate</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div style="position: relative;">
                                                                <span class="helpTextCom"
                                                                      id="investment_total_fee_bd_label">&nbsp; Total Fee <small>(BDT)</small></span>
                                                                </div>
                                                            </td>
                                                            <td colspan="2">
                                                                <table>
                                                                    <tr>
                                                                        <td width="100%">
                                                                            {!! Form::text('total_fee', $viewMode == 'on' ? \App\Libraries\CommonFunction::convertToBdtAmount($appInfo->total_fee) : $appInfo->total_fee, ['class' => 'form-control input-md number', 'id'=>'total_fee', 'readonly']) !!}
                                                                        </td>
                                                                        <td>
                                                                            <a type="button" class="btn btn-md btn-info"
                                                                               data-toggle="modal" data-target="#myModal">Govt. Fees Calculator</a>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </fieldset>
                                        </div>

                                        {{--7. Source of Finance--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">7. Source of finance</legend>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered" cellspacing="0"
                                                       width="100%">
                                                    <tbody id="annual_production_capacity">
                                                    {{--(a) Local Equity--}}
                                                    <tr id="finance_src_loc_equity_1_row_id">
                                                        <td>Local Equity (Million)</td>
                                                        <td>
                                                            {!! Form::text('finance_src_loc_equity_1', $appInfo->finance_src_loc_equity_1, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number','id'=>'finance_src_loc_equity_1','onblur'=>"calculateSourceOfFinance(this.id)",'readonly']) !!}
                                                            {!! $errors->first('finance_src_loc_equity_1','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>
                                                    <tr id="finance_src_foreign_equity_1_row_id">
                                                        <td width="38%">Foreign Equity (Million)</td>
                                                        <td>
                                                            {!! Form::text('finance_src_foreign_equity_1', $appInfo->finance_src_foreign_equity_1, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number','id'=>'finance_src_foreign_equity_1','onblur'=>"calculateSourceOfFinance(this.id)",'readonly']) !!}
                                                            {!! $errors->first('finance_src_foreign_equity_1','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Total Equity</th>
                                                        <td>
                                                            {!! Form::text('finance_src_loc_total_equity_1', $appInfo->finance_src_loc_total_equity_1, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number readOnly','id'=>'finance_src_loc_total_equity_1','readonly']) !!}
                                                            {!! $errors->first('finance_src_loc_total_equity_1','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>

                                                    {{--(b) Local Loan --}}
                                                    <tr>
                                                        <td>Local Loan (Million)</td>
                                                        <td>
                                                            {!! Form::text('finance_src_loc_loan_1', $appInfo->finance_src_loc_loan_1, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number','id'=>'finance_src_loc_loan_1','onblur'=>"calculateSourceOfFinance(this.id)",'readonly']) !!}
                                                            {!! $errors->first('finance_src_loc_loan_1','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Foreign Loan (Million)</td>
                                                        <td>
                                                            {!! Form::text('finance_src_foreign_loan_1', $appInfo->finance_src_foreign_loan_1, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number ','id'=>'finance_src_foreign_loan_1','onblur'=>"calculateSourceOfFinance(this.id)",'readonly']) !!}
                                                            {!! $errors->first('finance_src_foreign_loan_1','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Total Loan (Million)</th>
                                                        <td>
                                                            {!! Form::text('finance_src_total_loan', $appInfo->finance_src_total_loan, ['id'=>'finance_src_total_loan','class' => 'form-control input-md readOnly numberNoNegative', 'data-rule-maxlength'=>'240','readonly']) !!}
                                                            {!! $errors->first('finance_src_total_loan','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>

                                                    {{--Total Financing Million (a+b)--}}
                                                    <tr>
                                                        <th>Total Financing Million</th>
                                                        <td>
                                                            {!! Form::text('finance_src_loc_total_financing_m', $appInfo->finance_src_loc_total_financing_m, ['id'=>'finance_src_loc_total_financing_m','class' => 'form-control input-md readOnly numberNoNegative', 'data-rule-maxlength'=>'240','readonly']) !!}
                                                            {!! $errors->first('finance_src_loc_total_financing_m','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>

                                                    {{--Total Financing BDT (a+b)--}}
                                                    <tr>
                                                        <th>Total Financing BDT</th>
                                                        <td>
                                                            {!! Form::text('finance_src_loc_total_financing_1',  $appInfo->finance_src_loc_total_financing_1, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number readOnly numberNoNegative','id'=>'finance_src_loc_total_financing_1','readonly']) !!}
                                                            {!! $errors->first('finance_src_loc_total_financing_1','<span class="help-block">:message</span>') !!}
                                                            {{--Show duration in year--}}
                                                            <span class="text-danger"
                                                                  style="font-size: 12px; font-weight: bold"
                                                                  id="finance_src_loc_total_financing_1_alert"></span>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <table>
                                                    <tr>
                                                        <th colspan="4">
                                                            <i class="fa fa-question-circle" data-toggle="tooltip"
                                                               data-placement="top"
                                                               title="From the above information, the values of “Local Equity (Million)” and “Local Loan (Million)” will go into the "
                                                               Equity Amount" and "Loan Amount" respectively for
                                                            Bangladesh. The summation of the "Equity Amount" and "Loan
                                                            Amount" of other countries will be equal to the values of
                                                            “Foreign Equity (Million)” and “Foreign Loan (Million)”
                                                            respectively." ></i>
                                                            Country wise source of finance (Million BDT)
                                                        </th>
                                                    </tr>
                                                </table>
                                                <table class="table table-striped table-bordered" cellspacing="0"
                                                       width="100%" id="financeTableId">
                                                    <thead>
                                                    <tr>
                                                        <th class="required-star">Country</th>
                                                        <th class="required-star">
                                                            Equity Amount
                                                            <span class="text-danger" id="equity_amount_err"></span>
                                                        </th>
                                                        <th class="required-star">
                                                            Loan Amount
                                                            <span class="text-danger" id="loan_amount_err"></span>
                                                        </th>
                                                    </tr>
                                                    </thead>

                                                    @if(count($source_of_finance) > 0)
                                                            <?php $inc = 0; ?>
                                                        @foreach($source_of_finance as $finance)
                                                            <tr id="financeTableIdRow{{$inc}}" data-number="0">
                                                                <td>
                                                                    {!! Form::hidden("source_of_finance_id[$inc]", $finance->id) !!}
                                                                    {!!Form::select("country_id[$inc]", $countries, $finance->country_id, ['class' => 'form-control required readonly-pointer-disabled', "style" => "width: 100%"])!!}
                                                                    {!! $errors->first('country_id','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                                <td>
                                                                    {!! Form::text("equity_amount[$inc]", $finance->equity_amount, ['class' => 'form-control input-md equity_amount','readonly']) !!}
                                                                    {!! $errors->first('equity_amount','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                                <td>
                                                                    {!! Form::text("loan_amount[$inc]", $finance->loan_amount, ['class' => 'form-control input-md loan_amount','readonly']) !!}
                                                                    {!! $errors->first('loan_amount','<span class="help-block">:message</span>') !!}
                                                                </td>

                                                            </tr>
                                                                <?php $inc++; ?>
                                                        @endforeach
                                                    @else
                                                        <tr id="financeTableIdRow" data-number="0">
                                                            <td>
                                                                {!!Form::select('country_id[]', $countries, null, ['class' => 'form-control required', "style" => "width: 100%",'readonly'])!!}
                                                                {!! $errors->first('country_id','<span class="help-block">:message</span>') !!}
                                                            </td>
                                                            <td>
                                                                {!! Form::text('equity_amount[]', '', ['class' => 'form-control input-md equity_amount','readonly']) !!}
                                                                {!! $errors->first('equity_amount','<span class="help-block">:message</span>') !!}
                                                            </td>
                                                            <td>
                                                                {!! Form::text('loan_amount[]', '', ['class' => 'form-control input-md loan_amount','readonly']) !!}
                                                                {!! $errors->first('loan_amount','<span class="help-block">:message</span>') !!}
                                                            </td>

                                                        </tr>
                                                    @endif

                                                </table>
                                            </div>
                                        </fieldset>

                                        {{--8. Public utility service required--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">8. Public utility service required</legend>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_land"
                                                                   @if($appInfo->public_land == 1) checked="checked"
                                                                   @endif disabled>Land
                                                        </label>
                                                        @if($appInfo->public_land == 1)
                                                            <input type="hidden" name="public_land" value="1">
                                                        @endif

                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox"
                                                                   name="public_electricity"
                                                                   @if($appInfo->public_electricity == 1) checked="checked"
                                                                   @endif  disabled>Electricity
                                                        </label>
                                                        @if($appInfo->public_electricity == 1)
                                                            <input type="hidden" name="public_electricity" value="1">
                                                        @endif
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_gas"
                                                                   @if($appInfo->public_gas == 1) checked="checked"
                                                                   @endif  disabled>Gas
                                                        </label>
                                                        @if($appInfo->public_gas == 1)
                                                            <input type="hidden" name="public_gas" value="1">
                                                        @endif
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox"
                                                                   name="public_telephone"
                                                                   @if($appInfo->public_telephone == 1) checked="checked"
                                                                   @endif  disabled>Telephone
                                                        </label>
                                                        @if($appInfo->public_telephone == 1)
                                                            <input type="hidden" name="public_telephone" value="1">
                                                        @endif
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_road"
                                                                   @if($appInfo->public_road == 1) checked="checked"
                                                                   @endif  disabled>Road
                                                        </label>
                                                        @if($appInfo->public_road == 1)
                                                            <input type="hidden" name="public_road" value="1">
                                                        @endif
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox"
                                                                   name="public_water"
                                                                   @if($appInfo->public_water == 1) checked="checked"
                                                                   @endif  disabled>Water
                                                        </label>
                                                        @if($appInfo->public_water == 1)
                                                            <input type="hidden" name="public_water" value="1">
                                                        @endif
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox"
                                                                   name="public_drainage"
                                                                   @if($appInfo->public_drainage == 1) checked="checked"
                                                                   @endif disabled>Drainage
                                                        </label>
                                                        @if($appInfo->public_drainage == 1)
                                                            <input type="hidden" name="public_drainage" value="1">
                                                        @endif
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox other_utility"
                                                                   id="public_others" name="public_others"
                                                                   @if($appInfo->public_others == 1) checked="checked"
                                                                   @endif  disabled>Others
                                                        </label>
                                                        @if($appInfo->public_others == 1)
                                                            <input type="hidden" name="public_others" value="1">
                                                        @endif

                                                    </div>
                                                    <div class="col-md-12" hidden style="margin-top: 5px;"
                                                         id="public_others_field_div">
                                                        {!! Form::text('public_others_field', $appInfo->public_others_field, ['placeholder'=>'Specify others', 'class' => 'form-control input-md', 'id' => 'public_others_field','readonly']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--9. Trade license details--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">9. Trade license details</legend>
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('trade_licence_num') ? 'has-error': ''}}">
                                                        {!! Form::label('trade_licence_num','Trade License Number',['class'=>'text-left col-md-5 required-star']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('trade_licence_num', $appInfo->trade_licence_num, ['class' => 'form-control input-md required','readonly']) !!}
                                                            {!! $errors->first('trade_licence_num','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group {{$errors->has('trade_licence_issuing_authority') ? 'has-error': ''}}">
                                                        {!! Form::label('trade_licence_issuing_authority','Issuing Authority',['class'=>'col-md-5 text-left required-star']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('trade_licence_issuing_authority', $appInfo->trade_licence_issuing_authority, ['class' => 'form-control input-md required','readonly']) !!}
                                                            {!! $errors->first('trade_licence_issuing_authority','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </fieldset>

                                        {{--10. TIN--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">10. TIN</legend>
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('tin_number') ? 'has-error': ''}}">
                                                        {!! Form::label('tin_number','TIN Number',['class'=>'text-left col-md-5 required-star']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('tin_number', $appInfo->tin_number, ['class' => 'form-control input-md required','readonly']) !!}
                                                            {!! $errors->first('tin_number','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--11. Description of machinery and equipment--}}
                                        <!-- <fieldset class="scheduler-border" id="machinery_equipment">
                                            <legend class="scheduler-border">11. Description of machinery and equipment </legend>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered dt-responsive"
                                                       cellspacing="0" width="100%">
                                                    <thead>
                                                    <tr class="alert alert-info">
                                                        <th></th>
                                                        <th>Quantity</th>
                                                        <th>Price (BDT)</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td>Locally Collected</td>
                                                        <td>
                                                            {!! Form::text('machinery_local_qty', $appInfo->machinery_local_qty, ['class' => 'form-control input-md','id'=> 'machinery_local_qty','onkeyup' => 'totalMachineryEquipmentQty()','readonly']) !!}
                                                            {!! $errors->first('machinery_local_qty','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('machinery_local_price_bdt', ($viewMode == 'on' ? \App\Libraries\CommonFunction::convertToBdtAmount($appInfo->machinery_local_price_bdt) : $appInfo->machinery_local_price_bdt), ['class' => 'form-control input-md','id' => 'machinery_local_price_bdt','onkeyup' => "totalMachineryEquipmentPrice()",'readonly']) !!}
                                                            {!! $errors->first('machinery_local_price_bdt','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Imported</td>
                                                        <td>
                                                            {!! Form::text('imported_qty', $appInfo->imported_qty, ['class' => 'form-control input-md', 'id'=>'imported_qty', 'onkeyup' => 'totalMachineryEquipmentQty()','readonly']) !!}
                                                            {!! $errors->first('imported_qty','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('imported_qty_price_bdt', ($viewMode == 'on' ? \App\Libraries\CommonFunction::convertToBdtAmount($appInfo->imported_qty_price_bdt) : $appInfo->imported_qty_price_bdt), ['class' => 'form-control input-md','id'=>'imported_qty_price_bdt','onkeyup'=> "totalMachineryEquipmentPrice()",'readonly']) !!}
                                                            {!! $errors->first('imported_qty_price_bdt','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total</td>
                                                        <td>
                                                            {!! Form::text('total_machinery_qty', $appInfo->total_machinery_qty, ['class' => 'form-control input-md', 'id' => 'total_machinery_qty', 'readonly']) !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('total_machinery_price', ($viewMode == 'on' ? \App\Libraries\CommonFunction::convertToBdtAmount($appInfo->total_machinery_price) : $appInfo->total_machinery_price), ['class' => 'form-control input-md', 'id' => 'total_machinery_price', 'readonly']) !!}
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </fieldset> -->

                                        {{-- 12. Description of raw & packing materials --}}
                                        <!-- <fieldset class="scheduler-border" id="packing_materials">
                                            <legend class="scheduler-border">12. Description of raw &amp; packing materials </legend>
                                            <div class="table-responsive">
                                                <table class="table table-bordered dt-responsive" cellspacing="0"
                                                       width="100%">
                                                    <tbody>
                                                    <tr>
                                                        <td class="col-md-2">Locally</td>
                                                        <td class="col-md-10">
                                                            {!! Form::textarea('local_description', $appInfo->local_description, ['class' => 'BigInputField form-control input-md maxTextCountDown',
                                                        'id' => 'local_description', 'size'=>'5x2','data-charcount-maxlength'=>'1000','readonly']) !!}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="col-md-2">Imported</td>
                                                        <td class="col-md-10">
                                                            {!! Form::textarea('imported_description', $appInfo->imported_description, ['class' => 'BigInputField form-control input-md maxTextCountDown',
                                                        'id' => 'imported_description', 'size'=>'5x2','data-charcount-maxlength'=>'1000','readonly']) !!}
                                                        </td>
                                                    </tr>
                                                    </tbody>

                                                </table>
                                            </div>
                                        </fieldset> -->

                                    </div>
                                </div>

                            </fieldset>

                            <h3 class="stepHeader">Directors</h3>
                            <fieldset>
                                <div class="panel panel-info">
                                    <div class="panel-heading"><strong>List of directors and high authorities</strong></div>
                                    <div class="panel-body">
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">
                                                Information of (Chairman/ Managing Director/ Or Equivalent):
                                            </legend>
                                            <div class="row">
                                                <div class="form-group col-md-6 {{$errors->has('g_full_name') ? 'has-error': ''}}">
                                                    {!! Form::label('g_full_name','Full Name',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('g_full_name', $appInfo->g_full_name, ['class' => 'form-control input-md required' ,'readonly']) !!}
                                                        {!! $errors->first('g_full_name','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6 {{$errors->has('g_designation') ? 'has-error': ''}}">
                                                    {!! Form::label('g_designation','Position/ Designation',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('g_designation', $appInfo->g_designation, ['class' => 'form-control input-md required','readonly']) !!}
                                                        {!! $errors->first('g_designation','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('g_signature') ? 'has-error': ''}}">
                                                    {!! Form::label('g_signature','Signature', ['class'=>'text-left col-md-5 required-star']) !!}
                                                    <div class="col-md-7">
                                                        <div id="investorSignatureViewerDiv">
                                                            <figure>
                                                                <img class="img-thumbnail img-signature" id="investor_signature_preview"
                                                                     src="{{ (!empty($appInfo->g_signature)? url('uploads/'. $appInfo->g_signature) : url('assets/images/photo_default.png')) }}"
                                                                     alt="Investor Signature" style="width: 100%">
                                                            </figure>

                                                            <input type="hidden" id="investor_signature_base64" name="investor_signature_base64"/>
                                                            @if(!empty($appInfo->g_signature))
                                                                <input type="hidden" id="investor_signature_hidden" name="investor_signature_hidden"
                                                                       value="{{$appInfo->g_signature}}"/>
                                                            @endif
                                                        </div>
                                                        <div class="form-group">
                                                                <span style="font-size: 9px; font-weight: bold; display: block;">
                                                                       [File Format: *.jpg/ .jpeg | Width 300PX, Height 80PX]
                                                                </span>
                                                            <br/>
                                                            <input
                                                                    class="{{ empty($appInfo->g_signature) ? "required" : "" }}"
                                                                    type="hidden"
                                                                    value="{{$appInfo->g_signature}}"
                                                                    style="position: absolute; left: -9999px;"
                                                                    name="investor_signature_name"
                                                                    id="investor_signature"
                                                                    size="300x80"/>
                                                            {!! $errors->first('g_signature','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>


                                    </div>
                                </div>
                            </fieldset>
                            
                            <h3 class="stepHeader">List of Machineries</h3>
                            <fieldset>
                                <div class="panel panel-info">
                                    <div class="panel-heading"><strong>List of Machineries</strong></div>
                                    <div class="panel-body">
                                        {{--List of total importable machinery as registered with BIDA--}}
                                        <div class="panel panel-info" id="imported_machinery_review">
                                            <div class="panel-heading">
                                                <div class="pull-left" style="padding:5px 5px">
                                                    <strong>List of total importable machinery as registered with BIDA</strong>
                                                </div>

                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="panel-body">
                                                <div class="table-responsive">
                                                    <table id="machineryImported" class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                                                        <thead class="alert alert-info">

                                                        <tr id="check">
                                                            <th valign="top" class="text-center valigh-middle">Name of machineries
                                                                <span class="required-star"></span><br/>
                                                            </th>

                                                            <th valign="top" class="text-center valigh-middle">Quantity
                                                                <span class="required-star"></span><br/>
                                                            </th>

                                                            <th valign="top" class="text-center valigh-middle">Unit prices TK
                                                                <span class="required-star"></span><br/>
                                                            </th>

                                                            <th valign="top" class="text-center valigh-middle">Total value (Million) TK
                                                                <span class="required-star"></span><br/>
                                                            </th>

                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                            
                                                        @if(count($listOfMachineryImported) > 0)
                                                            <?php $inc = 0; $mi_i = 1; $machinery_imported_sum = 0; ?>

                                                            @foreach($listOfMachineryImported as $machineryImported)
                                                                <?php
                                                                $machinery_imported_sum += $machineryImported->l_machinery_imported_total_value;
                                                                ?>

                                                                <tr>
                                                                    <td>
                                                                        {!! Form::hidden("list_of_machinery_imported_id[$inc]", $machineryImported->id) !!}

                                                                        {!! Form::text("l_machinery_imported_name[$inc]", $machineryImported->l_machinery_imported_name, ['class' => 'form-control input-md product required','readonly']) !!}
                                                                    </td>

                                                                    <td>
                                                                        {!! Form::text("l_machinery_imported_qty[$inc]", $machineryImported->l_machinery_imported_qty, ['class' => 'form-control input-md product required','readonly']) !!}
                                                                    </td>

                                                                    <td>
                                                                        {!! Form::text("l_machinery_imported_unit_price[$inc]", $machineryImported->l_machinery_imported_unit_price, ['class' => 'form-control input-md required number', 'readonly']) !!}
                                                                    </td>

                                                                    <td>
                                                                        {!! Form::number("l_machinery_imported_total_value[$inc]", $machineryImported->l_machinery_imported_total_value, ['class' => 'form-control input-md required machinery_imported_total_value numberNoNegative', 'onkeyup' => "calculateListOfMachineryTotal('machinery_imported_total_value', 'machinery_imported_total_amount')" ,'readonly']) !!}
                                                                    </td>

                                                                </tr>
                                                                <?php $inc++; ?>
                                                            @endforeach

                                                        @endif
                                                        </tbody>
                                                        <tfoot>
                                                        <tr>
                                                            <th colspan="3" style="text-align: right;">Total :</th>
                                                            <th colspan="2" style="text-align: center;">
                                                                {!! Form::text('machinery_imported_total_amount', $machinery_imported_sum ,['class' => 'form-control input-md numberNoNegative', 'id' => 'machinery_imported_total_amount','readonly','readonly']) !!}
                                                            </th>
                                                        </tr>
                                                        </tfoot>
                                                    </table>

                                                </div> 
                                            </div>
                                        </div>


                                        <div class="panel panel-info" id="imported_machinery_review">
                                            <div class="panel-heading">
                                                <div class="pull-left" style="padding:5px 5px">
                                                    <strong>List of machinery to be imported under this application </strong>
                                                </div>

                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="panel-body">

                                                    <div class="table-responsive">
                                                        <table id="listOfMachineryImportedSpare"
                                                               class="table table-striped dt-responsive"
                                                               cellspacing="0"
                                                               width="100%">
                                                            <thead class="alert alert-info">

                                                            <tr id="check">
                                                                <th valign="top" class="text-center valigh-middle">Name of machineries with Standard Accessories
                                                                    <span class="required-star"></span><br/>
                                                                </th>

                                                                <th valign="top" class="text-center valigh-middle">Quantity  BIDA Reg. /Amend ment
                                                                    <span class="required-star"></span><br/>
                                                                </th>

                                                                <th valign="top" class="text-center valigh-middle">Remaining Quantity
                                                                    <span class="required-star"></span><br/>
                                                                </th>

                                                                <th valign="top" class="text-center valigh-middle">Required Quantity
                                                                    <span class="required-star"></span><br/>
                                                                </th>

                                                                <th valign="top" class="text-center valigh-middle">Machinery Type 
                                                                    <span class="required-star"></span><br/>
                                                                </th>
                                                                
                                                                <th valign="top" class="text-center valigh-middle">H.S. Code&nbsp&nbsp  
                                                                    <span class="required-star"></span><br/>
                                                                </th>
                                                                <th valign="top" class="text-center valigh-middle">Bill of Lading No.
                                                                    <span class="required-star"></span><br/>
                                                                </th>
                                                                <th valign="top" class="text-center valigh-middle">Bill of Lading Date
                                                                    <span class="required-star"></span><br/>
                                                                </th>
                                                                <th valign="top" class="text-center valigh-middle">Invoice No.
                                                                    <span class="required-star"></span><br/>
                                                                </th>
                                                                <th valign="top" class="text-center valigh-middle">Invoice Date
                                                                    <span class="required-star"></span><br/>
                                                                </th>
                                                                <th valign="top" class="text-center valigh-middle">Total value as per Invoice 
                                                                    <span class="required-star"></span><br/>
                                                                </th>
                                                                <th valign="top" class="text-center valigh-middle">Currency 
                                                                    <span class="required-star"></span><br/>
                                                                </th>
                                                                <th valign="top" class="text-center valigh-middle">Total value equivalent (BDT) 
                                                                    <span class="required-star"></span><br/>
                                                                </th>
                                                                <th valign="top" class="text-center valigh-middle"># </th>

                                                            </tr>

                                                            </thead>
                                                            <tbody>
                                                            @if(count($listOfMachineryImportedSpare) > 0)
                                                                    <?php $inc = 0; $sl_no = 1;?>
                                                                @foreach($listOfMachineryImportedSpare as $eachlistOfMachineryImportedSpare)
                                                                    <tr id="rowListOfMachineryImportedSpare{{$inc}}">
                                                                        <td>
                                                                            {!! Form::hidden("list_of_machinery_imported_spare_id[$inc]", $eachlistOfMachineryImportedSpare->id) !!}
                                                                            {!! Form::text("name[$inc]", $eachlistOfMachineryImportedSpare->name, ['class' => 'form-control input-md required','readonly']) !!}
                                                                        </td>
                                                                        
                                                                        <td>
                                                                            {!! Form::text("quantity[$inc]", $eachlistOfMachineryImportedSpare->quantity, ['class' => 'form-control input-md product required ','readonly']) !!}
                                                                        </td>
                                                                        <td>
                                                                            {{-- {!! Form::text("remaining_qty_show[$inc]", $eachlistOfMachineryImportedSpare->remaining_quantity - $eachlistOfMachineryImportedSpare->required_quantity, ['class' => 'form-control input-md required remaining_qty_calculation', 'readonly']) !!} --}}
                                                                            {!! Form::text("remaining_qty_show[$inc]", $eachlistOfMachineryImportedSpare->remaining_quantity, ['class' => 'form-control input-md required remaining_qty_calculation', 'readonly']) !!}

                                                                            {!! Form::hidden("remaining_quantity[$inc]", $eachlistOfMachineryImportedSpare->remaining_quantity + $eachlistOfMachineryImportedSpare->required_quantity, ['class' => 'form-control input-md required', 'readonly']) !!}
                                                                        </td>
                                                                        
                                                                        <td>
                                                                            {{-- {!! Form::text("required_quantity[$inc]", $eachlistOfMachineryImportedSpare->required_quantity, ['class' => 'form-control input-md product required remaining_qty_calculation']) !!} --}}
                                                                            {!! Form::text("required_quantity[$inc]", $eachlistOfMachineryImportedSpare->required_quantity, ['class' => 'form-control input-md product remaining_qty_calculation number', 'required' => 'required']) !!}

                                                                        </td>

                                                                        <td>
                                                                            {!! Form::select("machinery_type[$inc]", ['new' => 'New', 'used' => 'Used'], $eachlistOfMachineryImportedSpare->machinery_type , ["placeholder" => "Select One","id"=>"machinery_type", "class" => "form-control input-md usd-def", 'required' => 'required']) !!}
                                                                            {!! $errors->first('machinery_type','<span class="help-block">:message</span>') !!}
                                                                        </td>

                                                                        <td>
                                                                            {!! Form::text("hs_code[$inc]", $eachlistOfMachineryImportedSpare->hs_code, ['class' => 'form-control input-md', 'required' => 'required']) !!}
                                                                        </td>

                                                                        <td>
                                                                            {!! Form::text("bill_loading_no[$inc]", $eachlistOfMachineryImportedSpare->bill_loading_no, ['class' => 'form-control input-md', 'required' => 'required']) !!}
                                                                        </td>

                                                                        <td>
                                                                            {!! Form::text("bill_loading_date[$inc]", \Carbon\Carbon::parse($eachlistOfMachineryImportedSpare->bill_loading_date)->format('d-M-Y'), ['class' => 'form-control input-md datepicker dateSpace', 'onblur' => 'dateChecker()', 'placeholder'=>'DD-MMM-YYYY', 'required' => 'required']) !!}
                                                                        </td>

                                                                        <td>
                                                                            {!! Form::text("invoice_no[$inc]", $eachlistOfMachineryImportedSpare->invoice_no, ['class' => 'form-control input-md', 'required' => 'required']) !!}
                                                                        </td>

                                                                        <td>
                                                                            {!! Form::text("invoice_date[$inc]",
                                                                            \Carbon\Carbon::parse($eachlistOfMachineryImportedSpare->invoice_date)->format('d-M-Y'), ['class' => 'form-control input-md datepicker dateSpace', 'onblur' => 'dateChecker()', 'placeholder'=>'DD-MMM-YYYY', 'required' => 'required']) !!}
                                                                        </td>
                                                                        <td>
                                                                            {!! Form::number("total_value_equivalent_usd[$inc]", $eachlistOfMachineryImportedSpare->total_value_equivalent_usd, ['class' => 'form-control input-md', 'required' => 'required']) !!}
                                                                        </td>
                                                                        <td>
                                                                            {!! Form::select("total_value_ccy[$inc]", $currencies, $eachlistOfMachineryImportedSpare->total_value_ccy, ["id"=>"total_value_ccy", "class" => "form-control input-md usd-def", 'required' => 'required']) !!}
                                                                            {!! $errors->first('total_value_ccy','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                        <td>
                                                                            {!! Form::number("total_value_as_per_invoice[$inc]", $eachlistOfMachineryImportedSpare->total_value_as_per_invoice, ['class' => 'form-control input-md', 'onchange' => 'formatDecimal(this)', 'required' => 'required']) !!}
                                                                        </td>
                                                                        <td>
                                                                            <a href="javascript:void(0);" class="btn btn-md btn-danger removeRow"
                                                                               onclick="removeTableRow('listOfMachineryImportedSpare','rowListOfMachineryImportedSpare{{$inc}}');">
                                                                                <i class="fa fa-times" aria-hidden="true"></i>
                                                                            </a>
                                                                        </td>

                                                                    </tr>
                                                                    <?php $inc++; ?>
                                                                @endforeach
                                                            @else
                                                                <?php $inc = 0; ?>
                                                                <tr id="rowListOfMachineryImportedSpare{{$inc}}">

                                                                    <td>
                                                                        {!! Form::text("name[$inc]", '', ['class' => 'form-control input-md required']) !!}
                                                                    </td>

                                                                    <td>
                                                                        {!! Form::text("quantity[$inc]", '', ['class' => 'form-control input-md required']) !!}
                                                                    </td>

                                                                    <td>
                                                                        {!! Form::text("remaining_quantity[$inc]", '', ['class' => 'form-control input-md required']) !!}
                                                                    </td>

                                                                    <td>
                                                                        {!! Form::text("required_quantity[$inc]", '', ['class' => 'form-control input-md', 'required' => 'required']) !!}
                                                                    </td>

                                                                    <td>
                                                                        {!! Form::select("machinery_type[$inc]", ['new' => 'New', 'used' => 'Used'], '' , ["placeholder" => "Select One","id"=>"machinery_type", "class" => "form-control input-md usd-def", 'required' => 'required']) !!}
                                                                    </td>

                                                                    <td>
                                                                        {!! Form::text("hs_code[$inc]", '', ['class' => 'form-control input-md', 'required' => 'required']) !!}
                                                                    </td>

                                                                    <td>
                                                                        {!! Form::text("bill_loading_no[$inc]", '', ['class' => 'form-control input-md', 'required' => 'required']) !!}
                                                                    </td>

                                                                    <td>
                                                                        {!! Form::text("bill_loading_date[$inc]", '', ['class' => 'form-control input-md', 'required' => 'required']) !!}
                                                                    </td>

                                                                    <td>
                                                                        {!! Form::text("invoice_no[$inc]", '', ['class' => 'form-control input-md', 'required' => 'required']) !!}
                                                                    </td>

                                                                    <td>
                                                                        {!! Form::text("invoice_date[$inc]", '', ['class' => 'form-control input-md', 'required' => 'required']) !!}
                                                                    </td>

                                                                    <td>
                                                                        {!! Form::number("total_value_equivalent_usd[$inc]", '', ['class' => 'form-control input-md', 'required' => 'required']) !!}

                                                                    </td>
                                                                    <td>
                                                                        {!! Form::select("total_value_ccy[$inc]", $currencies, 107, ["id"=>"total_value_ccy", "class" => "form-control input-md usd-def", 'required' => 'required']) !!}
                                                                        {!! $errors->first('total_value_ccy','<span class="help-block">:message</span>') !!}
                                                                    </td>

                                                                    <td>
                                                                        {!! Form::number("total_value_as_per_invoice[$inc]", '', ['class' => 'form-control input-md', 'required' => 'required']) !!}
                                                                    </td>

                                                                    <td>
                                                                        <a href="javascript:void(0);" class="btn btn-md btn-danger removeRow"
                                                                           onclick="removeTableRow('listOfMachineryImportedSpare','rowListOfMachineryImportedSpare{{$inc}}');">
                                                                            <i class="fa fa-times" aria-hidden="true"></i>
                                                                        </a>
                                                                    </td>

                                                                </tr>
                                                                <?php $inc++; ?>
                                                            @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </fieldset>
                            
                            <h3 class="stepHeader">Attachments</h3>
                            <fieldset>
                                <div id="docListDiv"></div>
                            </fieldset>
                            
                            <h3 class="stepHeader">Payment & Submit</h3>
                            <fieldset>
                                <div class="panel panel-info">
                                    <div class="panel-heading">
                                        <strong>Service Fee Payment</strong>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('sfp_contact_name') ? 'has-error': ''}}">
                                                    {!! Form::label('sfp_contact_name','Contact name',['class'=>'col-md-5 text-left required-star']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('sfp_contact_name', $appInfo->sfp_contact_name, ['class' => 'form-control input-md required']) !!}
                                                        {!! $errors->first('sfp_contact_name','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('sfp_contact_email') ? 'has-error': ''}}">
                                                    {!! Form::label('sfp_contact_email','Contact email',['class'=>'col-md-5 text-left required-star']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::email('sfp_contact_email', $appInfo->sfp_contact_email, ['class' => 'form-control input-md required email']) !!}
                                                        {!! $errors->first('sfp_contact_email','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('sfp_contact_phone') ? 'has-error': ''}}">
                                                    {!! Form::label('sfp_contact_phone','Contact phone',['class'=>'col-md-5 text-left required-star']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('sfp_contact_phone', $appInfo->sfp_contact_phone, ['class' => 'form-control input-md sfp_contact_phone required helpText15 phone_or_mobile']) !!}
                                                        {!! $errors->first('sfp_contact_phone','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('sfp_contact_address') ? 'has-error': ''}}">
                                                    {!! Form::label('sfp_contact_address','Contact address',['class'=>'col-md-5 text-left required-star']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('sfp_contact_address', $appInfo->sfp_contact_address, ['class' => 'form-control input-md required']) !!}
                                                        {!! $errors->first('sfp_contact_address','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('sfp_pay_amount') ? 'has-error': ''}}">
                                                    {!! Form::label('sfp_pay_amount','Pay amount',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('sfp_pay_amount', $appInfo->sfp_pay_amount, ['class' => 'form-control input-md', 'readonly']) !!}
                                                        {!! $errors->first('sfp_pay_amount','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>

                                                <div class="col-md-6 {{$errors->has('sfp_vat_on_pay_amount') ? 'has-error': ''}}">
                                                    {!! Form::label('sfp_vat_on_pay_amount','VAT on pay amount',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('sfp_vat_on_pay_amount', $appInfo->sfp_vat_on_pay_amount, ['class' => 'form-control input-md', 'readonly']) !!}
                                                        {!! $errors->first('sfp_vat_on_pay_amount','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    {!! Form::label('sfp_total_amount','Total Amount',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('sfp_total_amount', number_format($appInfo->sfp_total_amount, 2), ['class' => 'form-control input-md', 'readonly']) !!}
                                                    </div>
                                                </div>

                                                <div class="col-md-6 {{$errors->has('sfp_status') ? 'has-error': ''}}">
                                                    {!! Form::label('sfp_status','Payment Status',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        @if($appInfo->sfp_payment_status == 0)
                                                            <span class="label label-warning">Pending</span>
                                                        @elseif($appInfo->sfp_payment_status == -1)
                                                            <span class="label label-info">In-Progress</span>
                                                        @elseif($appInfo->sfp_payment_status == 1)
                                                            <span class="label label-success">Paid</span>
                                                        @elseif($appInfo->sfp_payment_status == 2)
                                                            <span class="label label-danger">-Exception</span>
                                                        @elseif($appInfo->sfp_payment_status == 3)
                                                            <span class="label label-warning">Waiting for Payment Confirmation</span>
                                                        @else
                                                            <span class="label label-warning">invalid status</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{--Vat/ tax and service charge is an approximate amount--}}
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="alert alert-danger" role="alert">
                                                        <strong>Vat/ Tax</strong> and <strong>Transaction charge</strong> is an approximate amount, those may vary based on the Sonali Bank system and those will be visible here after payment submission.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="panel panel-info">
                                    <div class="panel-heading" style="padding-bottom: 4px;">
                                        <strong>Declaration and undertaking</strong>
                                    </div>
                                    <div class="panel-body">
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">Authorized person of the organization</legend>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="form-group col-md-6 {{$errors->has('auth_full_name') ? 'has-error': ''}}">
                                                            {!! Form::label('auth_full_name','Full Name',['class'=>'col-md-5 text-left']) !!}
                                                            <div class="col-md-7">
                                                                {!! Form::text('auth_full_name', $appInfo->auth_full_name, ['class' => 'form-control required input-md', 'readonly']) !!}
                                                                {!! $errors->first('auth_full_name','<span class="help-block">:message</span>') !!}
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-6 {{$errors->has('auth_designation') ? 'has-error': ''}}">
                                                            {!! Form::label('auth_designation','Designation',['class'=>'col-md-5 text-left']) !!}
                                                            <div class="col-md-7">
                                                                {!! Form::text('auth_designation', $appInfo->auth_designation, ['class' => 'form-control required input-md', 'readonly']) !!}
                                                                {!! $errors->first('auth_designation','<span class="help-block">:message</span>') !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-6 {{$errors->has('auth_mobile_no') ? 'has-error': ''}}">
                                                            {!! Form::label('auth_mobile_no','Mobile No.',['class'=>'col-md-5 text-left']) !!}
                                                            <div class="col-md-7">
                                                                {!! Form::text('auth_mobile_no', $appInfo->auth_mobile_no, ['class' => 'form-control input-sm required phone_or_mobile', 'readonly']) !!}
                                                                {!! $errors->first('auth_mobile_no','<span class="help-block">:message</span>') !!}
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-6 {{$errors->has('auth_email') ? 'has-error': ''}}">
                                                            {!! Form::label('auth_email','Email address',['class'=>'col-md-5 text-left']) !!}
                                                            <div class="col-md-7">
                                                                {!! Form::email('auth_email', $appInfo->auth_email, ['class' => 'form-control required input-sm email', 'readonly']) !!}
                                                                {!! $errors->first('auth_email','<span class="help-block">:message</span>') !!}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-md-6 {{$errors->has('auth_image') ? 'has-error': ''}}">
                                                            {!! Form::label('auth_image','Picture',['class'=>'col-md-5 text-left']) !!}
                                                            <div class="col-md-7">
                                                                <img class="img-thumbnail img-user"
                                                                     src="{{ (!empty($appInfo->auth_image) ? url('users/upload/'.$appInfo->auth_image) : url('users/upload/'.Auth::user()->user_pic)) }}"
                                                                     alt="User Photo">
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="auth_image" value="{{ (!empty($appInfo->auth_image) ? $appInfo->auth_image : Auth::user()->user_pic) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <div class="form-group {{$errors->has('accept_terms') ? 'has-error' : ''}} col-sm-12">
                                            <div class="checkbox">
                                                <label>
                                                    {!! Form::checkbox('accept_terms',1, ($appInfo->accept_terms == 1) ? true : false, array('id'=>'accept_terms', 'class'=>'required')) !!}
                                                    I do here by declare that the information given above is true to the best of
                                                    my knowledge and I shall be liable for any false information/ statement is
                                                    given.
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            @if(ACL::getAccsessRight('ImportPermission','-E-') && $appInfo->status_id != 6 && Auth::user()->user_type == '5x505')
                                @if(!in_array($appInfo->status_id,[5]))
                                    <div class="pull-left">
                                        <button type="submit" class="btn btn-info btn-md cancel"
                                                value="draft" name="actionBtn" id="save_as_draft">Save as Draft
                                        </button>
                                    </div>
                                    <div class="pull-left" style="padding-left: 1em;">
                                        <button type="submit" id="submitForm" style="cursor: pointer;"
                                                class="btn btn-success btn-md"
                                                value="Submit" name="actionBtn">Payment & Submit
                                            <i class="fa fa-question-circle" style="cursor: pointer" data-toggle="tooltip" title="" data-original-title="After clicking this button will take you in the payment portal for providing the required payment. After payment, the application will be automatically submitted and you will get a confirmation email with necessary info." aria-describedby="tooltip"></i>
                                        </button>
                                    </div>
                                @endif

                                @if($appInfo->status_id == 5)
                                    <div class="pull-left">
                                        <span style="display: block; height: 34px">&nbsp;</span>
                                    </div>
                                    <div class="pull-left" style="padding-left: 1em;">
                                        <button type="submit" id="submitForm" style="cursor: pointer;"
                                                class="btn btn-info btn-md"
                                                value="Submit" name="actionBtn">Re-submit
                                        </button>
                                    </div>
                                @endif
                            @endif

                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
                {{--End application form with wizard--}}
            </div>
        </div>
    </div>
</section>

<!-- Modal Govt Payment-->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Govt. Fees Calculator</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th scope="col">SI</th>
                        <th colspan="3" scope="colgroup">Fees break down Taka</th>
                        <th scope="col">Fees Taka</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1; ?>
                    @foreach($totalFee as $fee)
                        <tr>
                            <td scope="row">{{ $i++ }}</td>
                            <td>{{ $fee->min_amount_bdt }}</td>
                            <td>To</td>
                            <td>{{ $fee->max_amount_bdt }}</td>
                            <td>{{ $fee->p_o_amount_bdt }}</td>

                        </tr>
                    @endforeach

                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{--IRC 1st Adhoc modal--}}
<div class="modal fade" id="irc1stadhocModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content load_modal"></div>
    </div>
</div>
{{--Step js--}}
<script src="{{ asset("assets/scripts/jquery.steps.js") }}"></script>
@include('partials.image-resize.image-upload')
{{--select2 js--}}
<script src="{{ asset("assets/plugins/select2.min.js") }}"></script>
{{--Datepicker Js--}}
<script src="{{ asset("vendor/datepicker/datepicker.min.js") }}"></script>


<script>
    function formatDecimal(input) {
        var value = input.value;
        value = value.trim();
        if (value.includes('.')) {
            var parts = value.split('.');
            if (parts[1].length > 2) {
                parts[1] = parts[1].substring(0, 2);
                value = parts.join('.');
            }
        }
        input.value = value;
    }

    function dateChecker() {
        var dateInputs = $('.dateSpace');
        var dateFormatPattern = /^\d{2}-(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)-\d{4}$/;
        for (var i = 0; i < dateInputs.length; i++) {
            var dateInputValue = $(dateInputs[i]).val();
            
            if (!dateFormatPattern.test(dateInputValue) && dateInputValue != '') {
                $(dateInputs[i]).val('');
            }
        }
    }
    $(document).ready(function() {
        $('body').on('change', '.remaining_qty_calculation', function() {
            var row = $(this).closest('tr');
            var remaining_quantity = parseInt(row.find("input[name^='remaining_quantity']").val()) || 0;
            var required_quantity = parseInt(row.find("input[name^='required_quantity']").val()) || 0;
            var remainingQty = remaining_quantity - required_quantity;
    
            // Check if remaining quantity is negative
            if (remainingQty < 0) {
                $(this).val('');
                row.find("input[name^='remaining_qty_show']").val(remaining_quantity);
                swal({type: 'error', text: "Please make sure that the required quantity does not exceed the remaining quantity."});
                return;
            }
    
            row.find("input[name^='remaining_qty_show']").val(remainingQty);
        });
    });
</script>



<script type="text/javascript">

    function openModal(btn) {
        var this_action = btn.getAttribute('data-action');
        var data_target = btn.getAttribute('data-target');
        if (this_action != '') {
            $.get(this_action, function (data, success) {
                if (success === 'success') {
                    $(data_target + ' .load_modal').html(data);
                } else {
                    $(data_target + ' .load_modal').html('Unknown Error!');
                }
                $(data_target).modal('show', {backdrop: 'static'});
            });
        }
    }

  

    function calculateSourceOfFinance(event) {
        var local_equity = $('#finance_src_loc_equity_1').val() ? parseFloat($('#finance_src_loc_equity_1').val()) : 0;
        var foreign_equity = $('#finance_src_foreign_equity_1').val() ? parseFloat($('#finance_src_foreign_equity_1').val()) : 0;
        var total_equity = (local_equity + foreign_equity).toFixed(5);

        $('#finance_src_loc_total_equity_1').val(total_equity);

        var local_loan = $('#finance_src_loc_loan_1').val() ? parseFloat($('#finance_src_loc_loan_1').val()) : 0;
        var foreign_loan = $('#finance_src_foreign_loan_1').val() ? parseFloat($('#finance_src_foreign_loan_1').val()) : 0;
        var total_loan = (local_loan + foreign_loan).toFixed(5);

        $('#finance_src_total_loan').val(total_loan);

        // Convert into million
        var total_finance_million = (parseFloat(total_equity) + parseFloat(total_loan)).toFixed(5);
        var total_finance = (total_finance_million * 1000000).toFixed(2);

        $('#finance_src_loc_total_financing_m').val(total_finance_million);
        $('#finance_src_loc_total_financing_1').val(total_finance);

        //Check Total Financing and  Total Investment (BDT) is equal
        var total_fixed_ivst_bd = $("#total_invt_bdt").val();
        $('#finance_src_loc_total_financing_1_alert').hide();
        $('#finance_src_loc_total_financing_1').removeClass('required error');
        if (!(total_fixed_ivst_bd == total_finance)) {
            $('#finance_src_loc_total_financing_1').addClass('required error');
            $('#finance_src_loc_total_financing_1_alert').show();
            $('#finance_src_loc_total_financing_1_alert').text('Total Financing and Total Investment (BDT) must be equal.');
        }
    }


    function calculateListOfMachineryTotal(className, totalShowFieldId, option) {

        if (className == 'em_local_price_taka_mil') {
            var em_local_price_taka_mil = 0.000000;
            $(".em_local_price_taka_mil").each(function () {
                em_local_price_taka_mil = em_local_price_taka_mil + (this.value ? parseFloat(this.value) : 0.00000);
            })
            $("#em_local_total_taka_mil").val(em_local_price_taka_mil.toFixed(5));
        }

        if (className == 'em_price_bdt'){
            var $tr = $(option).closest('tr');
            var price_bdt = $tr.find('td:eq(6) input').val();
            $tr.find('td:eq(7) input').val((price_bdt/1000000).toFixed(5));

            var total_lc_taka_mil_val = 0.00000;
            $(".em_price_taka_mil").each(function () {
                total_lc_taka_mil_val = total_lc_taka_mil_val + (this.value ? parseFloat(this.value) : 0.00000);
            });
            $("#total_lc_taka_mil").val(total_lc_taka_mil_val);
        }

        var total_machinery = 0.00;
        $("." + className).each(function () {
            total_machinery = total_machinery + (this.value ? parseFloat(this.value) : 0.00);
        });

        $("#" + totalShowFieldId).val(total_machinery.toFixed(3));
    }

    function findBusinessClassCode(selectClass, sub_class_id) {

        // define sub class id as an optional parameter
        if (typeof sub_class_id === 'undefined') {
            sub_class_id = 0;
        }

        var business_class_code = (selectClass !== undefined) ? selectClass : $("#business_class_code").val();
        var _token = $('input[name="_token"]').val();

        if (business_class_code != '' && (business_class_code.length > 3)) {

            $("#business_class_list_of_code").text('');
            $("#business_class_list").html('');

            $.ajax({
                type: "GET",
                url: "/irc-recommendation-new/get-business-class-single-list",
                data: {
                    _token: _token,
                    business_class_code: business_class_code
                },
                success: function (response) {

                    if (response.responseCode == 1 && response.data.length != 0) {

                        $("#no_business_class_result").html('');
                        $("#business_class_list_sec").removeClass('hidden');

                        var table_row = '<tr><td>Section</td><td>' + response.data[0].section_code + '</td><td>' + response.data[0].section_name + '</td></tr>';
                        table_row += '<tr><td>Division</td><td>' + response.data[0].division_code + '</td><td>' + response.data[0].division_name + '</td></tr>';
                        table_row += '<tr><td>Group</td><td>' + response.data[0].group_code + '</td><td>' + response.data[0].group_name + '</td></tr>';
                        table_row += '<tr><td>Class</td><td>' + response.data[0].code + '</td><td>' + response.data[0].name + '</td></tr>';

                        var option = '<option value="">Select One</option>';
                        $.each(response.subClass, function (id, value) {
                            if (id == sub_class_id) {
                                option += '<option value="' + id + '" selected> ' + value + '</option>';
                            } else {
                                option += '<option value="' + id + '">' + value + '</option>';
                            }
                        });

                        table_row += '<tr><td width="10%" class="required-star">Sub class</td><td colspan="2"><select onchange="otherSubClassCodeName(this.value)" name="sub_class_id" class="form-control required" readonly>' + option + '</select></td></tr>';

                        let other_sub_class_code = '{{ $appInfo->other_sub_class_code }}';
                        let other_sub_class_name = '{{ $appInfo->other_sub_class_name }}';
                        table_row += '<tr id="other_sub_class_code_parent" class="hidden"><td width="20%" class="">Other sub class code</td><td colspan="2"><input type="text" name="other_sub_class_code" id="other_sub_class_code" class="form-control" readonly value="'+other_sub_class_code+'"></td></tr>';
                        table_row += '<tr id="other_sub_class_name_parent" class="hidden"><td width="20%" class="required-star">Other sub class name</td><td colspan="2"><input type="text" name="other_sub_class_name" id="other_sub_class_name" class="form-control required" readonly value="'+other_sub_class_name+'"></td></tr>';

                        $("#business_class_list_of_code").text(business_class_code);
                        $("#business_class_list").html(table_row);
                        $("#is_valid_bbs_code").val(1);

                        otherSubClassCodeName(sub_class_id);
                    } else {
                        $("#no_business_class_result").html('<span class="col-md-12 text-danger">No data found! Please, try another code or see the list.</span>');
                        $("#business_class_list_sec").addClass('hidden');
                        $("#is_valid_bbs_code").val(0);
                    }
                }
            });
        }

    }

    // Remove Table row script
    function removeTableRow(tableID, removeNum) {
        var current_total_row = "";
        $('#' + tableID).find('#' + removeNum).remove();
        //remove table footer
        if ($('#' + tableID).find('.datepicker').hasClass('datepicker')) {
            current_total_row = $('#' + tableID).find('tbody').find('.table-tr').length;
        } else {
            current_total_row = $('#' + tableID).find('tbody tr').length;
        }

        if (current_total_row <= 3) {
            const tableFooter = document.getElementById('autoFooter');
            if (tableFooter) {
                tableFooter.remove();
            }
        }
        calculateListOfMachineryTotal('machinery_imported_total_value', 'machinery_imported_total_amount');
        calculateListOfMachineryTotal('machinery_local_total_value', 'machinery_local_total_amount');

        calculateListOfMachineryTotal('em_spare_value_bdt', 'total_spare_value_bdt');
        calculateListOfMachineryTotal('em_price_bdt', 'total_lc_price_bdt');
        // calculateListOfMachineryTotal('em_price_taka_mil', 'total_lc_taka_mil');
        calculateListOfMachineryTotal('em_local_price_taka_mil', 'em_local_total_taka_mil');
    }

    $(document).ready(function () {
        var form = $("#ImportPermissionForm").show();

        //select 2 validation here
        select2OptionValidation(form);

        form.find('#save_as_draft').css('display','none');
        form.find('#submitForm').css('display', 'none');
        form.find('.actions').css('top', '-15px !important');
        form.steps({
            headerTag: "h3",
            bodyTag: "fieldset",
            transitionEffect: "slideLeft",
            onStepChanging: function (event, currentIndex, newIndex) {

                if (newIndex == 2) {

                    //Check Total Financing and  Total Investment (BDT) is equal
                    var totalInvestment = document.getElementById('total_invt_bdt').value;

                    if (totalInvestment == '' || totalInvestment == 0) {
                        $('.total_invt_bdt').addClass('required error');
                        return false;
                    } else {
                        $('.total_invt_bdt').removeClass('required error');
                    }

                    $('#finance_src_loc_total_financing_1_alert').hide();
                    $('#finance_src_loc_total_financing_1').removeClass('required error');
                    var total_finance = document.getElementById('finance_src_loc_total_financing_1').value;
                    if (!(totalInvestment == total_finance)) {
                        $('#finance_src_loc_total_financing_1').addClass('required error');
                        $('#finance_src_loc_total_financing_1_alert').show();
                        $('#finance_src_loc_total_financing_1_alert').text('Total Financing and Total Investment (BDT) must be equal.');
                        return false;
                    }

                    // Equity Amount should be equal to Total Equity (Million) of 7. Source of finance
                    var equity_amount_elements = document.querySelectorAll('.equity_amount');
                    var total_equity_amounts = 0;
                    for (var i = 0; i < equity_amount_elements.length; i++) {
                        total_equity_amounts = total_equity_amounts + parseFloat(equity_amount_elements[i].value ? equity_amount_elements[i].value : 0);
                    }
                    total_equity_amounts = (total_equity_amounts).toFixed(5);

                    var finance_src_loc_total_equity_1 = parseFloat(document.getElementById('finance_src_loc_total_equity_1').value ?
                        document.getElementById('finance_src_loc_total_equity_1').value : 0);

                    if (finance_src_loc_total_equity_1 != total_equity_amounts) {
                        for (var i = 0; i < equity_amount_elements.length; i++) {
                            equity_amount_elements[i].classList.add('required', 'error');
                        }
                        document.getElementById('equity_amount_err').innerHTML = '<br/>Total equity amount should be equal to Total Equity (Million)';
                        return false;
                    } else {
                        for (var i = 0; i < equity_amount_elements.length; i++) {
                            equity_amount_elements[i].classList.remove('error');
                        }
                        document.getElementById('equity_amount_err').innerHTML = '';
                    }

                    // Loan Amount should be equal to Total Loan (Million) of 7. Source of finance
                    var loan_amount_elements = document.querySelectorAll('.loan_amount');
                    var total_loan_amounts = 0;
                    for (var i = 0; i < loan_amount_elements.length; i++) {
                        total_loan_amounts = total_loan_amounts + parseFloat(loan_amount_elements[i].value ? loan_amount_elements[i].value : 0);
                    }

                    total_loan_amounts = (total_loan_amounts).toFixed(5);
                    var finance_src_total_loan = parseFloat(document.getElementById('finance_src_total_loan').value ?
                        document.getElementById('finance_src_total_loan').value : 0);

                    if (finance_src_total_loan != total_loan_amounts) {
                        for (var i = 0; i < loan_amount_elements.length; i++) {
                            loan_amount_elements[i].classList.add('required', 'error');
                        }
                        document.getElementById('loan_amount_err').innerHTML = '<br/>Total loan amount should be equal to Total Loan (Million)';
                        return false;
                    } else {
                        for (var i = 0; i < loan_amount_elements.length; i++) {
                            loan_amount_elements[i].classList.remove('error');
                        }
                        document.getElementById('loan_amount_err').innerHTML = '';
                    }

                    // Public utility service
                    var checkBoxes = document.getElementsByClassName('myCheckBox');
                    var isChecked = false;
                    for (var i = 0; i < checkBoxes.length; i++) {
                        if (checkBoxes[i].checked) {
                            isChecked = true;
                        }
                    }
                    if (isChecked) {
                        $(".myCheckBox").removeClass('required error');
                    } else {
                        $(".myCheckBox").addClass('required error');
                        return false;
                        alert('Please, check at least one checkbox for public utility service!');
                    }

                    // Valid BBS code check
                    if ($("#is_valid_bbs_code").val() == 0) {
                        alert('Business Sector (BBS Class Code) is required. Please enter or select from the above list.')
                        return false;
                    }

                    // annual production check
                    // var irc_purpose = $('#irc_purpose').val();
                    // if(irc_purpose != 2){
                    //     var is_list_of_apc = 0;
                    //     var _token = $('input[name="_token"]').val();
                    //     var application_id = '{{ Encryption::encodeId($appInfo->ref_id) }}';
                    //     $.ajax({
                    //         type: "GET",
                    //         url: "<?php echo url(); ?>/import-permission/list-of-annual-production",
                    //         async: false,
                    //         data: {
                    //             _token: _token,
                    //             application_id: application_id
                    //         },
                    //         success: function (response) {
                    //             is_list_of_apc = response.total_list_of_apc;
                    //             // console.log(is_list_of_apc);
                    //         }
                    //     });
                    // }
                }

                if (newIndex == 4) { 
                    var dateInputs = $('.dateSpace');
                    var dateFormatPattern = /^\d{2}-(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)-\d{4}$/;
                    for (var i = 0; i < dateInputs.length; i++) {
                        var dateInputValue = $(dateInputs[i]).val();
                        
                        if (!dateFormatPattern.test(dateInputValue) && dateInputValue != '') {
                            // alert('Please enter a date in the format DD-MMM-YYYY (e.g., 01-Feb-2024)');
                            swal({
                            type: 'error',
                            title: 'Oops...',
                            text: "Please enter a date in the format DD-MMM-YYYY (e.g., 01-Feb-2024)!",
                        });
                            return false; // Prevent moving to the next step
                        }
                    }
                }

                // Always allow previous action even if the current form is not valid!
                if (currentIndex > newIndex) {
                    return true;
                }

                // Needed in some cases if the user went back (clean up)
                if (currentIndex < newIndex) {
                    // To remove error styles
                    form.find(".body:eq(" + newIndex + ") label.error").remove();
                    form.find(".body:eq(" + newIndex + ") .error").removeClass("error");
                }
                form.validate().settings.ignore = ":disabled,:hidden";
                return form.valid();
            },
            onStepChanged: function (event, currentIndex, priorIndex) {
                if (currentIndex != 0) {
                    form.find('#save_as_draft').css('display','block');
                    form.find('.actions').css('top','-42px');
                } else {
                    form.find('#save_as_draft').css('display','none');
                    form.find('.actions').css('top','-15px');
                }

                if (currentIndex == 5) {
                    form.find('#submitForm').css('display', 'block');

                    $('#submitForm').on('click', function (e) {
                        form.validate().settings.ignore = ":disabled";
                        //console.log(form.validate().errors()); // show hidden errors in last step
                        return form.valid();
                    });
                } else {
                    form.find('#submitForm').css('display', 'none');
                }
            },
            onFinishing: function (event, currentIndex) {
                form.validate().settings.ignore = ":disabled";
                return form.valid();
            },
            onFinished: function (event, currentIndex) {
                errorPlacement: function errorPlacement(error, element) {
                    element.before(error);
                }
            }
        });

        var popupWindow = null;
        $('.finish').on('click', function (e) {
            if (form.valid()) {
                $('body').css({"display": "none"});
                popupWindow = window.open('<?php echo URL::to('/irc-recommendation-new/preview'); ?>', 'Sample', '');
            } else {
                return false;
            }
        });

        //trigger basic information section
        $('input[name=last_br]:checked').trigger('click');
        $("#bank_id").trigger('change');

    });

    /***
     *
     * select 2 option validation here
     * @param form id
     */
    function select2OptionValidation(form) {
        form.validate({
            highlight: function (element, errorClass) {
                var elem = $(element);
                if (elem.hasClass("select2-hidden-accessible")) {
                    $("#select2-" + elem.attr("id") + "-container").css("color", "red");
                    $("#select2-" + elem.attr("id") + "-container").parent().css("border-color", "red");
                } else {
                    elem.addClass(errorClass);
                }
            },
            unhighlight: function (element, errorClass) {
                var elem = $(element);
                if (elem.hasClass("select2-hidden-accessible")) {
                    $("#select2-" + elem.attr("id") + "-container").css("color", "#444");
                    $("#select2-" + elem.attr("id") + "-container").parent().css("border-color", "#aaa");
                } else {
                    elem.removeClass(errorClass);
                }
            }
        });
    }

    // New end
    function CalculateTotalInvestmentTk() {
        var land = parseFloat(document.getElementById('local_land_ivst').value);
        var building = parseFloat(document.getElementById('local_building_ivst').value);
        var machine = parseFloat(document.getElementById('local_machinery_ivst').value);
        var other = parseFloat(document.getElementById('local_others_ivst').value);
        var wcCapital = parseFloat(document.getElementById('local_wc_ivst').value);
        var totalInvest = ((isNaN(land) ? 0 : land) + (isNaN(building) ? 0 : building) + (isNaN(machine) ? 0 : machine) + (isNaN(other) ? 0 : other) + (isNaN(wcCapital) ? 0 : wcCapital)).toFixed(5);
        var totalTk = (totalInvest * 1000000).toFixed(2);
        document.getElementById('total_fixed_ivst_million').value = totalInvest;
        document.getElementById('total_invt_bdt').value = totalTk;

        var totalFee = '<?php echo json_encode($totalFee); ?>';

        var fee = 0;
        if (totalTk != 0) {
            $.each(JSON.parse(totalFee), function (i, row) {
                if ((totalTk >= parseInt(row.min_amount_bdt)) && (totalTk <= parseInt(row.max_amount_bdt))) {
                    fee = parseInt(row.p_o_amount_bdt);
                }
                if (totalTk >= 1000000001) {
                    fee = 100000;
                }
            });
        } else {
            fee = 0;
        }
        $("#total_fee").val(fee.toFixed(2));
    }

    $(document).ready(function () {
        $("#organization_status_id").change(checkOrganizationStatusId);
        //call this function when open application
        checkOrganizationStatusId();

        calculateListOfMachineryTotal('machinery_imported_total_value', 'machinery_imported_total_amount');
        calculateListOfMachineryTotal('machinery_local_total_value', 'machinery_local_total_amount');

        calculateListOfMachineryTotal('em_spare_value_bdt', 'total_spare_value_bdt');
        calculateListOfMachineryTotal('em_price_bdt', 'total_lc_price_bdt');
        //calculateListOfMachineryTotal('em_price_taka_mil', 'total_lc_taka_mil');
        calculateListOfMachineryTotal('em_local_price_taka_mil', 'em_local_total_taka_mil');

        let class_code = '{{ $appInfo->class_code }}';
        let sub_class_id = '{{ $appInfo->sub_class_id == "0" ? "-1" : $appInfo->sub_class_id }}';
        findBusinessClassCode(class_code, sub_class_id);

        {{--        TypeWiseDocLoad('{{$appInfo->app_type_id}}', '{{$IRCType->attachment_key}}');--}}
                {{--loadSixMonthsProduction('{{$appInfo->imported_from_1st_adhoc}}');--}}

        if ($("#public_others").prop('checked') == true) {
            $("#public_others_field_div").show('slow');
            $("#public_others_field").addClass('required');
        }

        $("#public_others").click(function () {
            $("#public_others_field_div").hide('slow');
            $("#public_others_field").removeClass('required');
            var isOtherChecked = $(this).is(':checked');
            if (isOtherChecked == true) {
                $("#public_others_field_div").show('slow');
                $("#public_others_field").addClass('required');
            }
        });

        $("#business_sector_id").trigger('change');


        // Sales (in 100%)
        $("#local_sales_per").on('keyup', function () {
            var local_sales_per = this.value;
            if (local_sales_per <= 100 && local_sales_per >= 0) {
                var cal = 100 - local_sales_per;
                $('#foreign_sales_per').val(cal);
                $("#total_sales").val(100);
            } else {
                alert("Please select a value between 0 & 100");
                $('#local_sales_per').val(0);
                $('#foreign_sales_per').val(0);
                $("#total_sales").val(0);
            }
        });

        $("#foreign_sales_per").on('keyup', function () {
            var foreign_sales_per = this.value;
            if (foreign_sales_per <= 100 && foreign_sales_per >= 0) {
                var cal = 100 - foreign_sales_per;
                $('#local_sales_per').val(cal);
                $("#total_sales").val(100);
            } else {
                alert("Please select a value between 0 & 100");
                $('#local_sales_per').val(0);
                $('#foreign_sales_per').val(0);
                $("#total_sales").val(0);
            }
        });

        $('#ImportPermissionForm').validate({
            rules: {
                ".myCheckBox": {required: true, maxlength: 1}
            }
        });
        $('.readOnlyCl input,.readOnlyCl select,.readOnlyCl textarea,.readOnly').not("#business_class_code, #major_activities, #business_sector_id, #business_sector_others, #business_sub_sector_id, #business_sub_sector_others, #country_of_origin_id, #organization_status_id, #project_name").attr('readonly', true);
        //$('.readOnlyCl :radio:not(:checked)').attr('disabled', true);
        $(".readOnlyCl select").each(function () {
            var id = $(this).attr('id');
            if (id != 'business_sector_id' && id != 'business_sub_sector_id' && id != 'country_of_origin_id' && id != 'organization_status_id') {
                $("#" + id + " option:not(:selected)").prop('disabled', true);
            }
        });
    });

    function checkOrganizationStatusId() {
        var organizationStatusId = $("#organization_status_id").val();

        // 3 = Local, 2 = Foreign, 1 = Joint Venture
        if (organizationStatusId == 3) {
            $("#country_of_origin_id").removeClass('required');
            $(".country_of_origin_div").hide('slow');

            $("#finance_src_foreign_equity_1").val('');
            $("#finance_src_foreign_equity_1").blur();
            $("#finance_src_foreign_equity_1_row_id").hide('slow');
            $("#finance_src_loc_equity_1_row_id").show('slow');
        } else if (organizationStatusId == 2){
            $("#finance_src_loc_equity_1").val('');
            $("#finance_src_loc_equity_1").blur();
            $("#finance_src_loc_equity_1_row_id").hide('slow');
            $("#finance_src_foreign_equity_1_row_id").show('slow');
        } else {
            $(".country_of_origin_div").show('slow');
            $("#country_of_origin_id").addClass('required');

            $("#finance_src_loc_equity_1_row_id").show('slow');
            $("#finance_src_foreign_equity_1_row_id").show('slow');
        }

        organizationStatusWiseDocLoad(organizationStatusId);

    }
    function calculateAnnulCapacity(event) {
        var id = event.split(/[_ ]+/).pop();
        var no1 = $('#apc_quantity_' + id).val() ? parseFloat($('#apc_quantity_' + id).val()) : 0;
        var no2 = $('#apc_price_usd_' + id).val() ? parseFloat($('#apc_price_usd_' + id).val()) : 0;
        var bdtValue = $('#crvalue').val() ? parseFloat($('#crvalue').val()) : 0;
        var usdToBdt = bdtValue * no2;
        var total = (no1 * usdToBdt) / 1000000;

        $('#apc_value_taka_' + id).val(total);
    }

    function Calculate44Numbers(arg1, arg2, place) {

        var no1 = $('#' + arg1).val() ? parseFloat($('#' + arg1).val()) : 0;
        var no2 = $('#' + arg2).val() ? parseFloat($('#' + arg2).val()) : 0;

        var total = new SumArguments(no1, no2);
        $('#' + place).val(total.sum());

        var inputs = $(".totalTakaOrM");
        var total1 = 0;

        var total7 = 0;
        for (var i = 0; i < inputs.length; i++) {
            if ($(inputs[i]).val() !== '')
                total7 += parseFloat($(inputs[i]).val());

        }
        $("#total_ivst").val(total7);
        $("#total_fixed_ivst22").val(total7);
    }

    function SumArguments() {
        var _arguments = arguments;
        this.sum = function () {
            var i = _arguments.length;
            var result = 0;
            while (i--) {
                result += _arguments[i];
            }
            return result;
        };
    }

    //--------File Upload Script Start----------//
    function uploadDocument(targets, id, vField, isRequired, isMultipleAttachment = 'false') {
        var file_id = document.getElementById(id);
        var file = file_id.files;
        if (file && file[0]) {
            if (!(file[0].type == 'application/pdf')) {
                swal({
                    type: 'error',
                    title: 'Oops...',
                    text: 'The file format is not valid! Please upload in pdf format.'
                });
                file_id.value = '';
                return false;
            }

            var file_size = parseFloat((file[0].size) / (1024 * 1024)).toFixed(1); //MB Calculation
            if (!(file_size <= 2)) {
                swal({
                    type: 'error',
                    title: 'Oops...',
                    text: 'Max file size 2MB. You have uploaded ' + file_size + 'MB'
                });
                file_id.value = '';
                return false;
            }
        }

        var inputFile = $("#" + id).val();
        if (inputFile == '') {
            $("#" + id).html('');
            document.getElementById("isRequired").value = '';
            document.getElementById("selected_file").value = '';
            document.getElementById("validateFieldName").value = '';
            document.getElementById("multipleAttachment").value = '';
            document.getElementById(targets).innerHTML = '<input type="hidden" class="required" value="" id="' + vField + '" name="' + vField + '">';
            if ($('#label_' + id).length) $('#label_' + id).remove();
            return false;
        }

        try {
            document.getElementById("isRequired").value = isRequired;
            document.getElementById("selected_file").value = id;
            document.getElementById("validateFieldName").value = vField;
            document.getElementById("multipleAttachment").value = isMultipleAttachment;
            document.getElementById(targets).style.color = "red";
            var action = "{{url('/import-permission/upload-document')}}";

            $("#" + targets).html('Uploading....');
            var file_data = $("#" + id).prop('files')[0];
            var form_data = new FormData();
            form_data.append('selected_file', id);
            form_data.append('isRequired', isRequired);
            form_data.append('validateFieldName', vField);
            form_data.append('multipleAttachment', isMultipleAttachment);
            form_data.append('_token', "{{ csrf_token() }}");
            form_data.append(id, file_data);
            $.ajax({
                target: '#' + targets,
                url: action,
                dataType: 'text',  // what to expect back from the PHP script, if anything
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function (response) {
                    //console.log(response);
                    $('#' + targets).html(response);
                    var fileNameArr = inputFile.split("\\");
                    var l = fileNameArr.length;
                    if ($('#label_' + id).length)
                        $('#label_' + id).remove();
                    var doc_id = parseInt(id.substring(4));
                    var newInput = $('<label class="saved_file_' + doc_id + '" id="label_' + id + '"><br/><b class="remove-uploaded-file">File: ' + fileNameArr[l - 1] +
                        ' <a href="javascript:void(0)" onclick="EmptyFile(' + doc_id
                        + ', ' + isRequired + ')"><span class="btn btn-xs btn-danger"><i class="fa fa-times"></i></span> </a></b></label>');
                    //var newInput = $('<label id="label_' + id + '"><br/><b>File: ' + fileNameArr[l - 1] + '</b></label>');
                    $("#" + id).after(newInput);
                    //check valid data
                    document.getElementById(id).value = '';
                    var validate_field = $('#' + vField).val();
                    if (validate_field != '') {
                        $("#" + id).removeClass('required');
                    }
                }
            });
        } catch (err) {
            document.getElementById(targets).innerHTML = "Sorry! Something Wrong.";
        }
    }

    //--------File Upload Script End----------//

    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();

        // ceo country, city district, state thana, father, mother
        $('#ceo_country_id').change(function (e) {
            var country_id = this.value;
            if (country_id == '18') {
                $("#ceo_city_div").addClass('hidden');
                // $("#ceo_city").removeClass('required');
                $("#ceo_state_div").addClass('hidden');
                $("#ceo_state").removeClass('required');
                $("#ceo_passport_div").addClass('hidden');
                // $("#ceo_passport_no").removeClass('required');


                $("#ceo_district_div").removeClass('hidden');
                $("#ceo_district_id").addClass('required');
                $("#ceo_thana_div").removeClass('hidden');
                $("#ceo_thana_id").addClass('required');
                $("#ceo_nid_div").removeClass('hidden');
                $("#ceo_nid").addClass('required');

                $("#ceo_father_label").addClass('required-star');
                $("#ceo_father_name").addClass('required');
                $("#ceo_mother_label").addClass('required-star');
                $("#ceo_mother_name").addClass('required');
            } else {
                $("#ceo_city_div").removeClass('hidden');
                // $("#ceo_city").addClass('required');
                $("#ceo_state_div").removeClass('hidden');
//                $("#ceo_state").addClass('required');
                $("#ceo_passport_div").removeClass('hidden');
                // $("#ceo_passport_no").addClass('required');

                $("#ceo_district_div").addClass('hidden');
                $("#ceo_district_id").removeClass('required');
                $("#ceo_thana_div").addClass('hidden');
                $("#ceo_thana_id").removeClass('required');
                $("#ceo_nid_div").addClass('hidden');
                $("#ceo_nid").removeClass('required');

                $("#ceo_father_label").removeClass('required-star');
                $("#ceo_father_name").removeClass('required');
                $("#ceo_mother_label").removeClass('required-star');
                $("#ceo_mother_name").removeClass('required');
            }
        });
        $('#ceo_country_id').trigger('change');

        {{--Select2 calling--}}
        $(".select2").select2();

//        $('[data-toggle="tooltip"]').tooltip();

        $('.datepicker').datepicker({
            outputFormat: 'dd-MMM-y',
            // daysOfWeekDisabled: [5,6],
            theme : 'blue',
        });

        $("#ceo_district_id").change(function () {
            var districtId = $(this).val();
            $(this).after('<span class="loading_data">Loading...</span>');
            var self = $(this);
            $.ajax({
                type: "GET",
                url: "<?php echo url(); ?>/users/get-thana-by-district-id",
                data: {
                    districtId: districtId
                },
                success: function (response) {
                    var option = '<option value="">Select One</option>';
                    if (response.responseCode == 1) {
                        $.each(response.data, function (id, value) {
                            if (id == '{{ $appInfo->ceo_thana_id }}') {
                                option += '<option value="' + id + '" selected>' + value + '</option>';
                            } else {
                                option += '<option value="' + id + '">' + value + '</option>';
                            }
                        });
                    }
                    $("#ceo_thana_id").html(option);
                    $(self).next().hide('slow');
                }
            });
        });
        $('#ceo_district_id').trigger('change');

        $("#office_division_id").change(function () {
            var divisionId = $('#office_division_id').val();
            $(this).after('<span class="loading_data">Loading...</span>');
            var self = $(this);
            $.ajax({
                type: "GET",
                url: "<?php echo url(); ?>/irc-recommendation-new/get-district-by-division",
                data: {
                    divisionId: divisionId
                },
                success: function (response) {
                    var option = '<option value="">Select One</option>';
                    if (response.responseCode == 1) {
                        $.each(response.data, function (id, value) {
                            option += '<option value="' + id + '">' + value + '</option>';
                        });
                    }
                    $("#office_district_id").html(option);
                    $(self).next().hide();
                }
            });
        });

        $("#office_district_id").change(function () {
            var districtId = $(this).val();
            $(this).after('<span class="loading_data">Loading...</span>');
            var self = $(this);
            $.ajax({
                type: "GET",
                url: "<?php echo url(); ?>/users/get-thana-by-district-id",
                data: {
                    districtId: districtId
                },
                success: function (response) {
                    var option = '<option value="">Select One</option>';
                    if (response.responseCode == 1) {
                        $.each(response.data, function (id, value) {
                            if (id == '{{ $appInfo->office_thana_id }}') {
                                option += '<option value="' + id + '" selected>' + value + '</option>';
                            } else {
                                option += '<option value="' + id + '">' + value + '</option>';
                            }
                        });
                    }
                    $("#office_thana_id").html(option);
                    $(self).next().hide('slow');
                }
            });
        });
        $('#office_district_id').trigger('change');

        $("#factory_district_id").change(function () {
            var districtId = $(this).val();
            $(this).after('<span class="loading_data">Loading...</span>');
            var self = $(this);
            $.ajax({
                type: "GET",
                url: "<?php echo url(); ?>/users/get-thana-by-district-id",
                data: {
                    districtId: districtId
                },
                success: function (response) {
                    var option = '<option value="">Select One</option>';
                    if (response.responseCode == 1) {
                        $.each(response.data, function (id, value) {
                            if (id == '{{ $appInfo->factory_thana_id }}') {
                                option += '<option value="' + id + '" selected>' + value + '</option>';
                            } else {
                                option += '<option value="' + id + '">' + value + '</option>';
                            }
                        });
                    }
                    $("#factory_thana_id").html(option);
                    $(self).next().hide('slow');
                }
            });
        });
        $('#factory_district_id').trigger('change');

        var fire_license_info = $('input[name="fire_license_info"]:checked').val();
        changeFireLicenseInfo(fire_license_info)

        var env_license = $('input[name="environment_clearance"]:checked').val();
        changeEnvironment(env_license)

        sectionChange('{{$appInfo->irc_purpose_id}}');
    });

    function sectionChange(selectedvalue) {

        if (selectedvalue == 1) { // 1 = Raw material
            document.getElementById('annual_raw').style.display = 'block';
            // document.getElementById('existing_lc').style.display = 'block';
            // document.getElementById('existing_local').style.display = 'block';

            document.getElementById('annual_spare').style.display = 'none';
            document.getElementById('existing_spare').style.display = 'none';

            // 7. annual_spare
            classAddRemove(['apsp_product_name', 'apsp_quantity_unit', 'apsp_quantity', 'apsp_price_usd', 'apsp_value_taka'], 'none');

            // 7. existing_spare
            classAddRemove(['em_spare_lc_no', 'em_spare_lc_date', 'em_spare_lc_value_currency', 'em_spare_value_bdt', 'em_spare_lc_bank_branch'], 'none');

            // As Per L/C Open
            classAddRemove(['em_product_name', 'em_quantity_unit', 'em_quantity', 'em_unit_price', 'em_price_unit', 'em_price_bdt'], 'none');
            $('.required-star-dynamically').removeClass("required-star");

        } else if(selectedvalue == 2) { // 2 = spare parts
            document.getElementById('annual_raw').style.display = 'none';
            document.getElementById('annual_spare').style.display = 'block';
            document.getElementById('existing_spare').style.display = 'block';

            // 7. annual_spare
            classAddRemove(['apsp_product_name', 'apsp_quantity_unit', 'apsp_quantity', 'apsp_price_usd', 'apsp_value_taka'], 'block');
            // 7. existing_spare
            classAddRemove(['em_spare_lc_no', 'em_spare_lc_date', 'em_spare_lc_value_currency', 'em_spare_value_bdt', 'em_spare_lc_bank_branch'], 'block');

            // As Per L/C Open
            classAddRemove(['em_product_name', 'em_quantity_unit', 'em_quantity', 'em_unit_price', 'em_price_unit', 'em_price_bdt'], 'block');
            $('.required-star-dynamically').addClass("required-star");

        } else if(selectedvalue == 3) { // 3 = Both
            document.getElementById('annual_raw').style.display = 'block';
            document.getElementById('annual_spare').style.display = 'none';
            document.getElementById('existing_spare').style.display = 'block';
            // 7. annual_spare
            classAddRemove(['apsp_product_name', 'apsp_quantity_unit', 'apsp_quantity', 'apsp_price_usd', 'apsp_value_taka'], 'none');

            //7. existing_spare
            classAddRemove(['em_spare_lc_no', 'em_spare_lc_date', 'em_spare_lc_value_currency', 'em_spare_value_bdt', 'em_spare_lc_bank_branch'], 'block');

            // As Per L/C Open
            classAddRemove(['em_product_name', 'em_quantity_unit', 'em_quantity', 'em_unit_price', 'em_price_unit', 'em_price_bdt'], 'block');
            $('.required-star-dynamically').addClass("required-star");
        }
    }

    function classAddRemove(inputFieldClass, display)
    {
        if (display == 'none') {
            $.each(inputFieldClass, function (id, value) {
                $("." + value).removeClass("required");
            })
        }

        if (display == 'block') {
            $.each(inputFieldClass, function (id, value) {
                $("." + value).addClass("required");
            })
        }

    }

    $('#ImportPermissionForm h3').hide();
    // for those field which have huge content, e.g. Address Line 1
    $('.bigInputField').each(function () {
        if ($(this)[0]['localName'] == 'select') {
            // This style will not work in mozila firefox, it's bug in firefox, maybe they will update it in next version
            $(this).attr('style', '-webkit-appearance: button; -moz-appearance: button; -webkit-user-select: none; -moz-user-select: none; text-overflow: ellipsis; white-space: pre-wrap; height: auto;');
        } else {
            $(this).replaceWith('<span class="form-control input-md" style="background:#eee; height: auto;min-height: 30px;">' + this.value + '</span>');
        }
    });

</script>


<script src="{{ asset("build/js/intlTelInput-jquery_v16.0.8.min.js") }}" type="text/javascript"></script>
<script src="{{ asset("build/js/utils_v16.0.8.js") }}" type="text/javascript"></script>
{{--//textarea count down--}}
<script src="{{ asset("vendor/character-counter/jquery.character-counter_v1.0.min.js") }}" src="" type="text/javascript"></script>
<script>
    $(function () {
        docLoad();
        //max text count down
        $('.maxTextCountDown').characterCounter();

        {{--initail -input plugin script start--}}
        $("#ceo_telephone_no").intlTelInput({
            hiddenInput: "ceo_telephone_no",
            initialCountry: "BD",
            placeholderNumberType: "MOBILE",
            separateDialCode: true
        });

        $("#ceo_mobile_no").intlTelInput({
            hiddenInput: "ceo_mobile_no",
            initialCountry: "BD",
            placeholderNumberType: "MOBILE",
            separateDialCode: true
        });

        $("#office_mobile_no").intlTelInput({
            hiddenInput: "office_mobile_no",
            initialCountry: "BD",
            placeholderNumberType: "MOBILE",
            separateDialCode: true
        });

        $("#factory_mobile_no").intlTelInput({
            hiddenInput: "factory_mobile_no",
            initialCountry: "BD",
            placeholderNumberType: "MOBILE",
            separateDialCode: true
        });

        $("#office_telephone_no").intlTelInput({
            hiddenInput: "office_telephone_no",
            initialCountry: "BD",
            placeholderNumberType: "MOBILE",
            separateDialCode: true
        });

        $("#factory_telephone_no").intlTelInput({
            hiddenInput: "factory_telephone_no",
            initialCountry: "BD",
            placeholderNumberType: "MOBILE",
            separateDialCode: true
        });

        $(".gfp_contact_phone").intlTelInput({
            hiddenInput: "gfp_contact_phone",
            initialCountry: "BD",
            placeholderNumberType: "MOBILE",
            separateDialCode: true
        });

        $("#sfp_contact_phone").intlTelInput({
            hiddenInput: "sfp_contact_phone",
            initialCountry: "BD",
            placeholderNumberType: "MOBILE",
            separateDialCode: true
        });
        $("#auth_mobile_no").intlTelInput({
            hiddenInput: "auth_mobile_no",
            initialCountry: "BD",
            placeholderNumberType: "MOBILE",
            separateDialCode: true
        });
        $("#applicationForm").find('.iti').css({"width":"-moz-available", "width":"-webkit-fill-available"});
        {{--initail -input plugin script end--}}

        //------- Manpower start -------//
        $('#manpower').find('input').keyup(function () {
            var local_male = $('#local_male').val() ? parseFloat($('#local_male').val()) : 0;
            var local_female = $('#local_female').val() ? parseFloat($('#local_female').val()) : 0;
            var local_total = parseInt(local_male + local_female);
            $('#local_total').val(local_total);


            var foreign_male = $('#foreign_male').val() ? parseFloat($('#foreign_male').val()) : 0;
            var foreign_female = $('#foreign_female').val() ? parseFloat($('#foreign_female').val()) : 0;
            var foreign_total = parseInt(foreign_male + foreign_female);
            $('#foreign_total').val(foreign_total);

            var mp_total = parseInt(local_total + foreign_total);
            $('#mp_total').val(mp_total);

            var mp_ratio_local = parseFloat(local_total / mp_total);
            var mp_ratio_foreign = parseFloat(foreign_total / mp_total);

//            mp_ratio_local = Number((mp_ratio_local).toFixed(3));
//            mp_ratio_foreign = Number((mp_ratio_foreign).toFixed(3));

            //---------- code from bida old
            mp_ratio_local = ((local_total / mp_total) * 100).toFixed(2);
            mp_ratio_foreign = ((foreign_total / mp_total) * 100).toFixed(2);
            if (foreign_total == 0) {
                mp_ratio_local = local_total;
            } else {
                mp_ratio_local = Math.round(parseFloat(local_total / foreign_total) * 100) / 100;
            }
            mp_ratio_foreign = (foreign_total != 0) ? 1 : 0;
            // End of code from bida old -------------

            $('#mp_ratio_local').val(mp_ratio_local);
            $('#mp_ratio_foreign').val(mp_ratio_foreign);
        });
        //------- Manpower end -------//
    });
</script>

<script>

    //section 11 total price calculation ...
    function totalMachineryEquipmentPrice() {
        var localPrice = document.getElementById('machinery_local_price_bdt').value;
        var importedPrice = document.getElementById('imported_qty_price_bdt').value;
        if (localPrice == '') {
            localPrice = 0;
        }
        if (importedPrice == '') {
            importedPrice = 0;
        }

        var total = parseFloat(localPrice) + parseFloat(importedPrice);
        if (isNaN(total)) {
            total = 0;
        }
        $('#total_machinery_price').val(total);
    }
    // Add table Row script
    function addTableRow1(tableID, template_row_id) {
        // Copy the template row (first row) of table and reset the ID and Styling
        var new_row = document.getElementById(template_row_id).cloneNode(true);
        new_row.id = "";
        new_row.style.display = "";

        //Get the total now, and last row number of table
        var current_total_row = $('#' + tableID).find('tbody tr').length;
        var final_total_row = current_total_row + 1;

        // Generate an ID of the new Row, set the row id and append the new row into table
        var last_row_number = $('#' + tableID).find('tbody tr').last().attr('data-number');
        if (last_row_number != '' && typeof last_row_number !== "undefined") {
            last_row_number = parseInt(last_row_number) + 1;
        } else {
            last_row_number = Math.floor(Math.random() * 101);
        }

        var new_row_id = 'rowCount' + tableID + last_row_number;
        new_row.id = new_row_id;
        $("#" + tableID).append(new_row);

        $("#" + new_row_id).find('.sl').text(final_total_row);

        // Convert the add button into remove button of the new row
        $("#" + tableID).find('#' + new_row_id).find('.addTableRows').removeClass('btn-primary').addClass('btn-danger')
            .attr('onclick', 'removeTableRow("' + tableID + '","' + new_row_id + '")');
        // Icon change of the remove button of the new row
        $("#" + tableID).find('#' + new_row_id).find('.addTableRows > .fa').removeClass('fa-plus').addClass('fa-times');
        // data-number attribute update of the new row
        $('#' + tableID).find('tbody tr').last().attr('data-number', last_row_number);

        // Get all select box elements from the new row, reset the selected value, and change the name of select box
        var all_select_box = $("#" + tableID).find('#' + new_row_id).find('select');
        all_select_box.val(''); //reset value
        all_select_box.prop('selectedIndex', 0);
        for (var i = 0; i < all_select_box.length; i++) {
            var name_of_select_box = all_select_box[i].name;
            var updated_name_of_select_box = name_of_select_box.replace('[0]', '[' + final_total_row + ']'); //increment all array element name
            all_select_box[i].name = updated_name_of_select_box;
        }

        // Get all input box elements from the new row, reset the value, and change the name of input box
        var all_input_box = $("#" + tableID).find('#' + new_row_id).find('input');
        all_input_box.val(''); // value reset
        for (var i = 0; i < all_input_box.length; i++) {
            var name_of_input_box = all_input_box[i].name;
            var id_of_input_box = all_input_box[i].id;
            var updated_name_of_input_box = name_of_input_box.replace('[0]', '[' + final_total_row + ']');
            var updated_id_of_input_box = id_of_input_box.replace('[0]', '_' + final_total_row);
            all_input_box[i].name = updated_name_of_input_box;
            all_input_box[i].id = updated_id_of_input_box;
        }

        // Get all textarea box elements from the new row, reset the value, and change the name of textarea box
        var all_textarea_box = $("#" + tableID).find('#' + new_row_id).find('textarea');
        all_textarea_box.val(''); // value reset
        for (var i = 0; i < all_textarea_box.length; i++) {
            var name_of_textarea = all_textarea_box[i].name;
            var updated_name_of_textarea = name_of_textarea.replace('[0]', '[' + final_total_row + ']');
            all_textarea_box[i].name = updated_name_of_textarea;
            $('#' + new_row_id).find('.readonlyClass').prop('readonly', true);
        }

        // Table footer adding with add more button
        if (final_total_row > 3) {
            const check_tfoot_element = $('#' + tableID + ' tfoot').length;
            if (check_tfoot_element === 0) {
                const table_header_columns = $('#' + tableID).find('thead th');
                let table_footer = document.getElementById(tableID).createTFoot();
                table_footer.setAttribute('id', 'autoFooter')
                let table_footer_row = table_footer.insertRow(0);
                for (i = 0; i < table_header_columns.length; i++) {
                    const table_footer_th = table_footer_row.insertCell(i);
                    // if this is the last column, then push add more button
                    if (i === (table_header_columns.length - 1)) {
                        table_footer_th.innerHTML = '<a class="btn btn-sm btn-primary addTableRows" title="Add more" onclick="addTableRow1(\'' + tableID + '\', \'' + template_row_id + '\')"><i class="fa fa-plus"></i></a>';
                    } else {
                        table_footer_th.innerHTML = '<b>' + table_header_columns[i].innerHTML + '</b>';
                    }
                }
            }
        }

        $("#" + tableID).find('#' + new_row_id).find('.onlyNumber').on('keydown', function (e) {
            //period decimal
            if ((e.which >= 48 && e.which <= 57)
                //numpad decimal
                || (e.which >= 96 && e.which <= 105)
                // Allow: backspace, delete, tab, escape, enter and .
                || $.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1
                // Allow: Ctrl+A
                || (e.keyCode == 65 && e.ctrlKey === true)
                // Allow: Ctrl+C
                || (e.keyCode == 67 && e.ctrlKey === true)
                // Allow: Ctrl+V
                || (e.keyCode == 86 && e.ctrlKey === true)
                // Allow: Ctrl+X
                || (e.keyCode == 88 && e.ctrlKey === true)
                // Allow: home, end, left, right
                || (e.keyCode >= 35 && e.keyCode <= 39)) {
                var $this = $(this);
                setTimeout(function () {
                    $this.val($this.val().replace(/[^0-9.]/g, ''));
                }, 4);
                var thisVal = $(this).val();
                if (thisVal.indexOf(".") != -1 && e.key == '.') {
                    return false;
                }
                $(this).removeClass('error');
                return true;
            } else {
                $(this).addClass('error');
                return false;
            }
        }).on('paste', function (e) {
            var $this = $(this);
            setTimeout(function () {
                $this.val($this.val().replace(/[^.0-9]/g, ''));
            }, 4);
        });
        $("#" + tableID).find('.datepicker').datetimepicker({
            viewMode: 'years',
            format: 'DD-MMM-YYYY',
            extraFormats: ['DD.MM.YY', 'DD.MM.YYYY'],
            maxDate: 'now',
            minDate: '01/01/1905'
        });


    } // end of addTableRow() function

    //section 11 total price calculation ...
    function totalMachineryEquipmentQty() {
        var machinery_local_qty = document.getElementById('machinery_local_qty').value;
        var imported_qty = document.getElementById('imported_qty').value;
        if (machinery_local_qty == '') {
            machinery_local_qty = 0;
        }
        if (imported_qty == '') {
            imported_qty = 0;
        }

        var total = parseFloat(machinery_local_qty) + parseFloat(imported_qty);
        if (isNaN(total)) {
            total = 0;
        }
        $('#total_machinery_qty').val(total);
    }

    // Dynamic modal for business sub-class
    function openBusinessSectorModal(btn) {
        var this_action = btn.getAttribute('data-action');

        if (this_action != '') {
            $.get(this_action, function (data, success) {
                if (success === 'success') {
                    $('#businessClassModal .load_business_class_modal').html(data);
                } else {
                    $('#businessClassModal .load_business_class_modal').html('Unknown Error!');
                }
                $('#businessClassModal').modal('show', {backdrop: 'static'});
            });
        }
    }

    function selectBusinessClass(btn) {
        var sub_class_code = btn.getAttribute('data-subclass');
        $("#business_class_code").val(sub_class_code);
        findBusinessClassCode(sub_class_code);

        $("#closeBusinessModal").click();
    }

    function changeFireLicenseInfo(value) {
        if (value == 'already_have') {
            document.getElementById('already_have_license_div').style.display = 'block';
            document.getElementById('applied_for_license_div').style.display = 'none';
        } else if (value == 'applied_for') {
            document.getElementById('applied_for_license_div').style.display = 'block';
            document.getElementById('already_have_license_div').style.display = 'none';
        }
    }

    function changeEnvironment(value) {
        if (value == 'already_have') {
            document.getElementById('have_license_for_environment_div').style.display = 'block';
            document.getElementById('apply_license_for_environment_div').style.display = 'none';
        } else if (value == 'applied_for') {
            document.getElementById('apply_license_for_environment_div').style.display = 'block';
            document.getElementById('have_license_for_environment_div').style.display = 'none';
        }
    }

    function otherSubClassCodeName(value) {
        if (value == '-1') {
            $("#other_sub_class_code_parent").removeClass('hidden');
            $("#other_sub_class_name").addClass('required');
            $("#other_sub_class_name_parent").removeClass('hidden');
        } else {
            $("#other_sub_class_code_parent").addClass('hidden');
            $("#other_sub_class_name").removeClass('required');
            $("#other_sub_class_name_parent").addClass('hidden');
        }
    }

    // function docLoad() {
    //     var attachment_key = "import_permission";
    //     if(attachment_key != ''){
    //         var _token = $('input[name="_token"]').val();
    //         var app_id = $("#app_id").val();
    //         $.ajax({
    //             type: "POST",
    //             url: '/import-permission/getDocList',
    //             dataType: "json",
    //             data: {_token : _token, attachment_key : attachment_key, app_id:app_id},
    //             success: function(result) {
    //                 if (result.html != undefined) {
    //                     $('#docListDiv').html(result.html);
    //                 }
    //             },
    //             error: function (jqXHR, textStatus, errorThrown) {
    //                 console.log(errorThrown);
    //                 // alert('Unknown error occured. Please, try again after reload');
    //             },
    //         });
    //     }
    // }

    function organizationStatusWiseDocLoad (organizationId) {
        const ownershipId = document.getElementById('ownership_status_id').value;
        let _token = $('input[name="_token"]').val();
        let app_id = $("#app_id").val();
        let attachment_key = generateAttachmentKey(organizationId, ownershipId);

        if (ownershipId != undefined && organizationId != undefined) {
            $.ajax({
                type: "POST",
                url: '/import-permission/getDocList',
                dataType: "json",
                data: {_token: _token, attachment_key: attachment_key, app_id: app_id},
                success: function (result) {
                    if (result.html != undefined) {
                        $('#docListDiv').html(result.html);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Unknown error occured. Please, try again after reload');
                },
            });
        }
    }

    function generateAttachmentKey(organizationId, ownershipId) {
        let organization_key = "";
        let ownership_key = "";

        switch (parseInt(organizationId)) {
            case 1:
                organization_key = "joint_venture";
                break;
            case 2:
                organization_key = "foreign";
                break;
            case 3:
                organization_key = "local";
                break;
            default:
        }

        return "ip_" + organization_key;
    }
</script>
