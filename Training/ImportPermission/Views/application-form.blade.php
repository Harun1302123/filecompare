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
        /*overflow: hidden;*/
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

    .croppie-container .cr-slider-wrap {
        width: 100% !important;
        margin: 5px auto !important;
    }

    .table-responsive {
        overflow-x: visible;
    }

    .select2-container .select2-selection--single {
        height: 34px !important;
        padding-top: 3px;
    }

    .dateSpace {
        min-width: 3rem !important;
    }
    @media (min-width: 992px) {
        .modal-lg {
            width: 1020px;
        }
    }

    .readonly-pointer-disabled {
        cursor: pointer;
        pointer-events: none;
        background-color: #eee; /* Optional: Adding a background color to visually indicate readonly state */
    }
</style>

<section class="content" id="applicationForm">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body" id="inputForm">
                {{--start 2 form with wizard--}}
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
                        <div class="pull-right"></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-body">
                        <div>
                            {!! Form::open(array('url' => 'import-permission/add','method' => 'post','id' => 'ImportPermission','role'=>'form','enctype'=>'multipart/form-data')) !!}
                            <input type="hidden" name="selected_file" id="selected_file"/>
                            <input type="hidden" name="validateFieldName" id="validateFieldName"/>
                            <input type="hidden" name="isRequired" id="isRequired"/>
                            <input type="hidden" name="multipleAttachment" id="multipleAttachment"/>
                            <input type="hidden" value="{{$usdValue->bdt_value}}" id="crvalue">
                            {{-- {!! Form::hidden('reg_no', Session::get('brInfo.reg_no') ,['class' => 'form-control input-md']) !!} --}}

                            <h3 class="stepHeader">Basic Information</h3>
                            <fieldset>
                                <div class="panel panel-info">
                                    <div class="panel-heading "><strong>Basic Information</strong></div>
                                    <div class="panel-body">
                                        {{-- <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-12 {{$errors->has('last_br') ? 'has-error': ''}}">
                                                    {!! Form::label('last_br','Did you receive your BIDA Registration/ BIDA Registration amendment approval online OSS?',['class'=>'col-md-6 text-left required-star']) !!}
                                                    <div class="col-md-6">
                                                        <label class="radio-inline">{!! Form::radio('last_br','yes', (Session::get('brInfo.last_br') == 'yes' ? true :false), ['class'=>'cusReadonly required helpTextRadio', 'id'=>'last_br_yes','onclick' => 'lastBidaRegistration(this.value)']) !!}
                                                            Yes</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}
                                        <div class="form-group">
                                            <div class="row">
                                                <div id="ref_app_tracking_no_div"
                                                     class="col-md-12  {{$errors->has('ref_app_tracking_no') ? 'has-error': ''}}">
                                                    {!! Form::label('ref_app_tracking_no','Please give your approved BIDA Registration/ BIDA Registration amendment Tracking ID.',['class'=>'col-md-6 text-left required-star', 'id' => 'ref_app_tracking_no_label']) !!}

                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            {{-- {!! Form::text('ref_app_tracking_no', Session::get('brInfo.ref_app_tracking_no'), ['data-rule-maxlength'=>'100', 'class' => 'form-control cusReadonly input-sm helpText15', 'placeholder' => 'BR-01Jan2022-00001/BRA-01Jan2022-00001']) !!} --}}
                                                            @if(Session::has('brInfo.ref_app_tracking_no'))
                                                                {!! Form::text('ref_app_tracking_no', Session::get('brInfo.ref_app_tracking_no'), ['data-rule-maxlength'=>'100', 'class' => 'form-control cusReadonly input-sm helpText15', 'placeholder' => 'BR-01Jan2022-00001/BRA-01Jan2022-00001','readonly']) !!}
                                                            @else
                                                                {!! Form::select('ref_app_tracking_no', $getLastApproveData, Session::get('brInfo.ref_app_tracking_no'), ['class' => 'form-control  input-md','id'=>'ref_app_tracking_no']) !!}
                                                            @endif
                                                            {!! $errors->first('ref_app_tracking_no','<span class="help-block">:message</span>') !!}
                                                            <span class="input-group-btn">
                                                                @if(Session::get('brInfo'))
                                                                    <button type="submit" class="btn btn-danger btn-sm" value="clean_load_data" name="actionBtn">Clear Loaded Data</button>
                                                                    <a href="{{ !empty(Session::get('brInfo.certificate_link')) ? Session::get('brInfo.certificate_link') : '#' }}" target="_blank" class="btn btn-success btn-sm">View Certificate</a>
                                                                @else
                                                                    <button type="submit" class="btn btn-success btn-sm cancel" value="searchBRinfo" name="actionBtn" id="searchBRinfo">Load Information</button>
                                                                @endif
                                                            </span>
                                                        </div>

                                                        <small class="text-danger">
                                                            N.B.: Once you save or submit the application, the BIDA Registration tracking no. cannot be changed anymore.
                                                        </small>
                                                    </div>

                                                    {!! Form::label('ref_app_approve_date','Approved Date', ['class'=>'col-md-6 text-left required-star']) !!}
                                                    <div class="col-md-6">
                                                        <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                            {!! Form::text('ref_app_approve_date', !empty(Session::get('ref_app_approve_date')) ? date('d-M-Y', strtotime(Session::get('ref_app_approve_date'))) : '', ['class'=>'form-control input-md datepicker','readonly', 'id' => 'ref_app_approve_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group" style="margin-top: 10px;">
                                            <div class="row">
                                                <div class="col-md-12 {{$errors->has('reg_no') ? 'has-error': ''}}">
                                                    {!! Form::label('reg_no','Registration No.', ['class'=>'col-md-6 text-left required-star']) !!}
                                                    <div class="col-md-6">
                                                        {!! Form::text('reg_no', !empty(Session::get('reg_info')) ? Session::get('reg_info')['reg_no'] : '', ['class'=>'form-control', 'id' => 'reg_no','readonly' => !empty(Session::get('reg_info')['reg_no']) ? 'readonly' : null]) !!}
                                                    </div>
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
                                                    <div class="col-md-12 form-group {{$errors->has('company_name') ? 'has-error': ''}}">
                                                        {!! Form::label('company_name','Name of the Organization/ Company/ Industrial Project',['class'=>'col-md-5 text-left']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('company_name',
                                                            (Session::get('brInfo.company_name') ? Session::get('brInfo.company_name') : CommonFunction::getCompanyNameById(Auth::user()->company_ids)),
                                                            ['class' => 'form-control input-md', 'readonly']) !!}
                                                            {!! $errors->first('company_name','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 form-group {{$errors->has('company_name_bn') ? 'has-error': ''}}">
                                                        {!! Form::label('company_name_bn','Name of the Organization/ Company/ Industrial Project (বাংলা)',['class'=>'col-md-5 text-left']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('company_name_bn',
                                                            (Session::get('brInfo.company_name_bn') ? Session::get('brInfo.company_name_bn') : CommonFunction::getCompanyBnNameById(Auth::user()->company_ids)),
                                                            ['class' => 'form-control input-md', 'readonly']) !!}
                                                            {!! $errors->first('company_name_bn','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6 {{$errors->has('organization_type_id') ? 'has-error': ''}}">
                                                        {!! Form::label('organization_type_id','Type of the organization',['class'=>'col-md-5 text-left']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::select('organization_type_id', $eaOrganizationType, (Session::get('brInfo.organization_type_id') ? Session::get('brInfo.organization_type_id') : $getCompanyData->organization_type_id), ['class' => 'form-control  input-md readonly-pointer-disabled','id'=>'organization_type_id']) !!}
                                                            {!! $errors->first('organization_type_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 {{$errors->has('organization_status_id') ? 'has-error': ''}}">
                                                        {!! Form::label('organization_status_id','Status of the organization',['class'=>'col-md-5 text-left required-star']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::select('organization_status_id', $eaOrganizationStatus, (Session::get('brInfo.organization_status_id') ? Session::get('brInfo.organization_status_id') : $getCompanyData->organization_status_id), ['class' => 'form-control input-md readonly-pointer-disabled','id'=>'organization_status_id','readonly']) !!}
                                                            {!! $errors->first('organization_status_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6 {{$errors->has('ownership_status_id') ? 'has-error': ''}}">
                                                        {!! Form::label('ownership_status_id','Ownership status',['class'=>'col-md-5 text-left']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::select('ownership_status_id', $eaOwnershipStatus, (Session::get('brInfo.ownership_status_id') ? Session::get('brInfo.ownership_status_id') : $getCompanyData->ownership_status_id), ['class' => 'form-control  input-md readonly-pointer-disabled','id'=>'ownership_status_id']) !!}
                                                            {!! $errors->first('ownership_status_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 country_of_origin_div">
                                                        {!! Form::label('country_of_origin_id','Country of origin',['class'=>'col-md-5 text-left']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::select('country_of_origin_id',$countriesWithoutBD, (Session::get('brInfo.country_of_origin_id') ? Session::get('brInfo.country_of_origin_id') : $getCompanyData->country_of_origin_id),['class'=>'form-control input-md readonly-pointer-disabled', 'id' => 'country_of_origin_id',  'readonly']) !!}
                                                            {!! $errors->first('country_of_origin_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12 {{$errors->has('project_name') ? 'has-error': ''}}">
                                                        {!! Form::label('project_name','Name of the project',['class'=>'col-md-3 text-left required-star']) !!}
                                                        <div class="col-md-9">
                                                            {!! Form::text('project_name', Session('brInfo.project_name'), ['class' => 'form-control required input-md ','id'=>'project_name', 'readonly']) !!}
                                                            {!! $errors->first('project_name','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="form-group col-md-12 {{$errors->has('business_class_code') ? 'has-error' : ''}}">
                                                        {!! Form::label('business_class_code','Business Sector (BBS Class Code)',['class'=>'col-md-3 required-star']) !!}
                                                        <div class="col-md-9">
                                                            {!! Form::text('business_class_code', Session('brInfo.class_code'), ['class' => 'form-control required input-md', 'min' => 4,'onkeyup' => 'findBusinessClassCode()', 'readonly']) !!}
                                                            <input type="hidden" name="is_valid_bbs_code" id="is_valid_bbs_code"/>
                                                            <span class="help-text" style="margin: 5px 0;">
                                                        </span>
                                                            {!! $errors->first('business_class_code','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div id="no_business_class_result"></div>

                                                        <fieldset class="scheduler-border hidden"
                                                                  id="business_class_list_sec">
                                                            <legend class="scheduler-border">
                                                                Other info. based on your business class (Code = <span id="business_class_list_of_code"></span>)
                                                            </legend>

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
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="form-group col-md-12 {{$errors->has('major_activities') ? 'has-error' : ''}}">
                                                        {!! Form::label('major_activities','Major activities in brief',['class'=>'col-md-12']) !!}
                                                        <div class="col-md-12">
                                                            {!! Form::textarea('major_activities', (Session::get('brInfo.major_activities') ? Session::get('brInfo.major_activities') : $getCompanyData->major_activities), ['class' => 'form-control input-md bigInputField maxTextCountDown', 'size' =>'5x2','data-rule-maxlength'=>'240', 'placeholder' => 'Maximum 240 characters', "data-charcount-maxlength" => "240" ,'readonly']) !!}
                                                            {!! $errors->first('major_activities','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{--Information of Principal--}}
                                <div class="panel panel-info">
                                    <div class="panel-heading "><strong>B. Information of Principal Promoter/Chairman/Managing Director/CEO/Country Manager</strong></div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('ceo_country_id') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_country_id','Country ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('ceo_country_id', $countries, (Session::get('brInfo.ceo_country_id') ? Session::get('brInfo.ceo_country_id') : $getCompanyData->ceo_country_id), ['class' => 'form-control  input-md readonly-pointer-disabled','id'=>'ceo_country_id', 'style' => 'width: 100%']) !!}
                                                        {!! $errors->first('ceo_country_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_dob') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_dob','Date of Birth',['class'=>'col-md-5']) !!}
                                                    <div class=" col-md-7">
                                                        <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                            {!! Form::text('ceo_dob',
                                                            (Session::get('brInfo.ceo_dob') ? (!empty(Session::get('brInfo.ceo_dob')) ? date('d-M-Y', strtotime(Session::get('brInfo.ceo_dob'))) : '') : (!empty($getCompanyData->ceo_dob) ? date('d-M-Y', strtotime($getCompanyData->ceo_dob)) : '')),
                                                            ['class'=>'form-control input-md datepicker', 'readonly','id' => 'ceo_dob', 'placeholder'=>'Pick from datepicker']) !!}
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
                                                        {!! Form::text('ceo_passport_no', (Session::get('brInfo.ceo_passport_no') ? Session::get('brInfo.ceo_passport_no') : $getCompanyData->ceo_passport_no), ['maxlength'=>'20',
                                                        'class' => 'form-control input-md ', 'id'=>'ceo_passport_no','readonly']) !!}
                                                        {!! $errors->first('ceo_passport_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div id="ceo_nid_div"
                                                     class="col-md-6 {{$errors->has('ceo_nid') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_nid','NID No.',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_nid', (Session::get('brInfo.ceo_nid') ? Session::get('brInfo.ceo_nid') : $getCompanyData->ceo_nid), ['maxlength'=>'20',
                                                        'class' => 'form-control number input-md  bd_nid','id'=>'ceo_nid','readonly']) !!}
                                                        {!! $errors->first('ceo_nid','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_designation') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_designation','Designation', ['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_designation', (Session::get('brInfo.ceo_designation') ? Session::get('brInfo.ceo_designation') : $getCompanyData->ceo_designation),
                                                        ['maxlength'=>'80','class' => 'form-control input-md ','readonly']) !!}
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
                                                        {!! Form::text('ceo_full_name', (Session::get('brInfo.ceo_full_name') ? Session::get('brInfo.ceo_full_name') : $getCompanyData->ceo_full_name), ['maxlength'=>'80',
                                                        'class' => 'form-control input-md required','readonly']) !!}
                                                        {!! $errors->first('ceo_full_name','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div id="ceo_district_div"
                                                     class="col-md-6 {{$errors->has('ceo_district_id') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_district_id','District/City/State ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('ceo_district_id',$districts, (Session::get('brInfo.ceo_district_id') ? Session::get('brInfo.ceo_district_id') : $getCompanyData->ceo_district_id), ['maxlength'=>'80','class' => 'form-control input-md readonly-pointer-disabled', 'style' => 'width: 100%']) !!}
                                                        {!! $errors->first('ceo_district_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div id="ceo_city_div"
                                                     class="col-md-6 hidden {{$errors->has('ceo_city') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_city','District/City/State',['class'=>'text-left  col-md-5 ']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_city', (Session::get('brInfo.ceo_city') ? Session::get('brInfo.ceo_city') : $getCompanyData->ceo_city),['class' => 'form-control input-md','readonly']) !!}
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
                                                        {!! Form::text('ceo_state', (Session::get('brInfo.ceo_state') ? Session::get('brInfo.ceo_state') : $getCompanyData->ceo_state),['class' => 'form-control input-md','readonly']) !!}
                                                        {!! $errors->first('ceo_state','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div id="ceo_thana_div"
                                                     class="col-md-6 {{$errors->has('ceo_thana_id') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_thana_id','Police Station/Town ',['class'=>'col-md-5 text-left required-star','placeholder'=>'Select district first']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('ceo_thana_id', $thana, (Session::get('brInfo.ceo_thana_id') ? Session::get('brInfo.ceo_thana_id') : $getCompanyData->ceo_thana_id), ['maxlength'=>'80','class' => 'form-control input-md readonly-pointer-disabled','placeholder' => 'Select district first', 'style' => 'width: 100%','readonly']) !!}
                                                        {!! $errors->first('ceo_thana_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_post_code') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_post_code','Post/Zip Code ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_post_code', (Session::get('brInfo.ceo_post_code') ? Session::get('brInfo.ceo_post_code') : $getCompanyData->ceo_post_code), ['maxlength'=>'80','class' => 'form-control input-md engOnly ','readonly']) !!}
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
                                                        {!! Form::text('ceo_address', (Session::get('brInfo.ceo_address') ? Session::get('brInfo.ceo_address') : $getCompanyData->ceo_address), ['maxlength'=>'150','class' => 'bigInputField form-control input-md ','readonly']) !!}
                                                        {!! $errors->first('ceo_address','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_telephone_no') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_telephone_no','Telephone No.',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_telephone_no', (Session::get('brInfo.ceo_telephone_no') ? Session::get('brInfo.ceo_telephone_no') : $getCompanyData->ceo_telephone_no), ['maxlength'=>'20','class' => 'form-control input-md helpText15 phone_or_mobile','readonly']) !!}
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
                                                        {!! Form::text('ceo_mobile_no',  (Session::get('brInfo.ceo_mobile_no') ? Session::get('brInfo.ceo_mobile_no') : $getCompanyData->ceo_mobile_no), ['class' => 'form-control input-md helpText15 phone_or_mobile required','readonly']) !!}
                                                        {!! $errors->first('ceo_mobile_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_father_name') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_father_name','Father\'s Name',['class'=>'col-md-5 text-left', 'id' => 'ceo_father_label']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_father_name', (Session::get('brInfo.ceo_father_name') ? Session::get('brInfo.ceo_father_name') : $getCompanyData->ceo_father_name), ['class' => 'form-control textOnly input-md ','readonly']) !!}
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
                                                        {!! Form::text('ceo_email', (Session::get('brInfo.ceo_email') ? Session::get('brInfo.ceo_email') : $getCompanyData->ceo_email), ['class' => 'form-control email input-md required','readonly']) !!}
                                                        {!! $errors->first('ceo_email','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_mother_name') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_mother_name','Mother\'s Name',['class'=>'col-md-5 text-left', 'id' => 'ceo_mother_label']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_mother_name', (Session::get('brInfo.ceo_mother_name') ? Session::get('brInfo.ceo_mother_name') : $getCompanyData->ceo_mother_name), ['class' => 'form-control textOnly  input-md','readonly']) !!}
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
                                                        {!! Form::text('ceo_fax_no', (Session::get('brInfo.ceo_fax_no') ? Session::get('brInfo.ceo_fax_no') : $getCompanyData->ceo_fax_no), ['class' => 'form-control input-md','readonly']) !!}
                                                        {!! $errors->first('ceo_fax_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_spouse_name') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_spouse_name','Spouse name',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_spouse_name', (Session::get('brInfo.ceo_spouse_name') ? Session::get('brInfo.ceo_spouse_name') : $getCompanyData->ceo_spouse_name), ['class' => 'form-control textOnly input-md','readonly']) !!}
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
                                                            {!! Form::radio('ceo_gender', 'Male', $getCompanyData->ceo_gender == "Male", ['class'=>'required' , 'disabled']) !!}
                                                            Male
                                                        </label>
                                                        <label class="radio-inline">
                                                            {!! Form::radio('ceo_gender', 'Female', $getCompanyData->ceo_gender == "Female", ['class'=>'required', 'disabled']) !!}
                                                            Female
                                                        </label>
                                                        <input type="hidden" name="ceo_gender" value="{{ $getCompanyData->ceo_gender }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{--Office Address--}}
                                <div class="panel panel-info">
                                    <div class="panel-heading "><strong>C. Office Address</strong></div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6 {{$errors->has('office_division_id') ? 'has-error': ''}}">
                                                {!! Form::label('office_division_id','Division',['class'=>'text-left col-md-5 required-star']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::select('office_division_id', $divisions, (Session::get('brInfo.office_division_id') ? Session::get('brInfo.office_division_id') : $getCompanyData->office_division_id), ['class' => 'form-control imput-md required readonly-pointer-disabled', 'id' => 'office_division_id']) !!}
                                                    {!! $errors->first('office_division_id','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('office_thana_id') ? 'has-error': ''}}">
                                                {!! Form::label('office_thana_id','Police Station', ['class'=>'col-md-5 text-left required-star']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::select('office_thana_id', $thana, (Session::get('brInfo.office_thana_id') ? Session::get('brInfo.office_thana_id') : $getCompanyData->office_thana_id), ['class' => 'form-control input-md required readonly-pointer-disabled','placeholder' => 'Select district first', 'style' => 'width: 100%']) !!}
                                                    {!! $errors->first('office_thana_id','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 form-group {{$errors->has('office_district_id') ? 'has-error': ''}}">
                                                {!! Form::label('office_district_id','District',['class'=>'col-md-5 text-left required-star']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::select('office_district_id', $districts, (Session::get('brInfo.office_district_id') ? Session::get('brInfo.office_district_id') : $getCompanyData->office_district_id), ['class' => 'form-control input-md required readonly-pointer-disabled','placeholder' => 'Select division first', 'style' => 'width: 100%']) !!}
                                                    {!! $errors->first('office_district_id','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('office_post_code') ? 'has-error': ''}}">
                                                {!! Form::label('office_post_code','Post Code', ['class'=>'col-md-5 text-left']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('office_post_code', (Session::get('brInfo.office_post_code') ? Session::get('brInfo.office_post_code') : $getCompanyData->office_post_code), ['class' => 'form-control input-md alphaNumeric','readonly']) !!}
                                                    {!! $errors->first('office_post_code','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 form-group {{$errors->has('office_post_office') ? 'has-error': ''}}">
                                                {!! Form::label('office_post_office','Post Office',['class'=>'col-md-5 text-left ']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('office_post_office', (Session::get('brInfo.office_post_office') ? Session::get('brInfo.office_post_office') : $getCompanyData->office_post_office), ['class' => 'form-control input-md','readonly']) !!}
                                                    {!! $errors->first('office_post_office','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('office_telephone_no') ? 'has-error': ''}}">
                                                {!! Form::label('office_telephone_no','Telephone No.',['class'=>'col-md-5 text-left']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('office_telephone_no', (Session::get('brInfo.office_telephone_no') ? Session::get('brInfo.office_telephone_no') : $getCompanyData->office_telephone_no), ['maxlength'=>'20','class' => 'form-control input-md helpText15 phone_or_mobile','readonly']) !!}
                                                    {!! $errors->first('office_telephone_no','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 form-group {{$errors->has('office_address') ? 'has-error': ''}}">
                                                {!! Form::label('office_address','Address ',['class'=>'col-md-5 text-left ']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('office_address', (Session::get('brInfo.office_address') ? Session::get('brInfo.office_address') : $getCompanyData->office_address), ['maxlength'=>'150','class' => 'form-control input-md','readonly']) !!}
                                                    {!! $errors->first('office_address','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('office_fax_no') ? 'has-error': ''}}">
                                                {!! Form::label('office_fax_no','Fax No. ',['class'=>'col-md-5 text-left']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('office_fax_no', (Session::get('brInfo.office_fax_no') ? Session::get('brInfo.office_fax_no') : $getCompanyData->office_fax_no), ['class' => 'form-control input-md','readonly']) !!}
                                                    {!! $errors->first('office_fax_no','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 form-group {{$errors->has('office_mobile_no') ? 'has-error': ''}}">
                                                {!! Form::label('office_mobile_no','Mobile No. ',['class'=>'col-md-5 text-left required-star']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('office_mobile_no', (Session::get('brInfo.office_mobile_no') ? Session::get('brInfo.office_mobile_no') : $getCompanyData->office_mobile_no), ['class' => 'form-control input-md helpText15 required','readonly']) !!}
                                                    {!! $errors->first('office_mobile_no','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('office_email') ? 'has-error': ''}}">
                                                {!! Form::label('office_email','Email ',['class'=>'col-md-5 text-left required-star']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('office_email', (Session::get('brInfo.office_email') ? Session::get('brInfo.office_email') : $getCompanyData->office_email), ['class' => 'form-control email input-md required','readonly']) !!}
                                                    {!! $errors->first('office_email','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{--Factory Address--}}
                                <div class="panel panel-info">
                                    <div class="panel-heading "><strong>D. Factory Address(This would be IRC address)</strong></div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('factory_district_id') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_district_id','District',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('factory_district_id', $districts, (Session::get('brInfo.factory_district_id') ? Session::get('brInfo.factory_district_id') : $getCompanyData->factory_district_id), ['class' => 'form-control input-md readonly-pointer-disabled', 'style' => 'width: 100%']) !!}
                                                        {!! $errors->first('factory_district_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('factory_thana_id') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_thana_id','Police Station', ['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('factory_thana_id', $thana, (Session::get('brInfo.factory_thana_id') ? Session::get('brInfo.factory_thana_id') : $getCompanyData->factory_thana_id), ['class' => 'form-control input-md readonly-pointer-disabled', 'style' => 'width: 100%']) !!}
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
                                                        {!! Form::text('factory_post_office', (Session::get('brInfo.factory_post_office') ? Session::get('brInfo.factory_post_office') : $getCompanyData->factory_post_office), ['class' => 'form-control input-md','readonly']) !!}
                                                        {!! $errors->first('factory_post_office','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('factory_post_code') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_post_code','Post Code', ['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('factory_post_code', (Session::get('brInfo.factory_post_code') ? Session::get('brInfo.factory_post_code') : $getCompanyData->factory_post_code), ['class' => 'form-control input-md number alphaNumeric','readonly']) !!}
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
                                                        {!! Form::text('factory_address', (Session::get('brInfo.factory_address') ? Session::get('brInfo.factory_address') : $getCompanyData->factory_address), ['maxlength'=>'150','class' => 'form-control input-md','readonly']) !!}
                                                        {!! $errors->first('factory_address','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('factory_telephone_no') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_telephone_no','Telephone No.',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('factory_telephone_no', (Session::get('brInfo.factory_telephone_no') ? Session::get('brInfo.factory_telephone_no') : $getCompanyData->factory_telephone_no), ['maxlength'=>'20','class' => 'form-control input-md helpText15 phone_or_mobile','readonly']) !!}
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
                                                        {!! Form::text('factory_mobile_no', (Session::get('brInfo.factory_mobile_no') ? Session::get('brInfo.factory_mobile_no') : $getCompanyData->factory_mobile_no), ['class' => 'form-control input-md helpText15','readonly']) !!}
                                                        {!! $errors->first('factory_mobile_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('factory_fax_no') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_fax_no','Fax No. ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('factory_fax_no', (Session::get('brInfo.factory_fax_no') ? Session::get('brInfo.factory_fax_no') : $getCompanyData->factory_fax_no), ['class' => 'form-control input-md','readonly']) !!}
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
                                                        <h4>You have selected <b>{{ Session::get('brInfo.des_office_name') }}, </b>{{ Session::get('brInfo.des_office_address') }} .</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                    </div>
                                </div>

                                {{--Registration Information--}}
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
                                                            {!! Form::select('project_status_id', $projectStatusList, Session::get('brInfo.project_status_id'), ["placeholder" => "Select One", 'class' => 'form-control input-md readonly-pointer-disabled']) !!}
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
                                                                    <table id="productionCostTbl" class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                                                                        <thead class="alert alert-info">
                                                                        <tr>
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

                                                                            <th class="text-center"> Sales Value in BDT (million)
                                                                                <span class="required-star"></span><br/>
                                                                            </th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>

                                                                        <?php $inc = 0; ?>
                                                                        @if(count(Session::get('brAnnualProductionCapacity'))>0)
                                                                            @foreach(Session::get('brAnnualProductionCapacity') as $eachProductionCap)
                                                                                <tr id="rowProCostCount{{$inc}}" data-number="0">
                                                                                    <td>
                                                                                        {!! Form::text("apc_product_name[$inc]", $eachProductionCap->product_name, ['data-rule-maxlength'=>'255','class' => 'form-control input-md product apc_product_name','id'=>'apc_product_name','readonly']) !!}
                                                                                        {!! $errors->first('apc_product_name','<span class="help-block">:message</span>') !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::select("apc_quantity_unit[$inc]",$productUnit,$eachProductionCap->quantity_unit,['class'=>'form-control input-md apc_quantity_unit readonly-pointer-disabled', 'id' => 'apc_quantity_unit']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="number" id="apc_quantity" name="apc_quantity[{{$inc}}]" class="form-control quantity1 CalculateInputByBoxNo number apc_quantity" value="{{ $eachProductionCap->quantity}}" readonly>
                                                                                        {!! $errors->first('quantity','<span class="help-block">:message</span>') !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="number" id="apc_price_usd" name="apc_price_usd[{{$inc}}]" class="form-control quantity1 CalculateInputByBoxNo number apc_price_usd" value="{{$eachProductionCap->price_usd}}" readonly>
                                                                                        {!! $errors->first('price_usd','<span class="help-block">:message</span>') !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::text("apc_value_taka[$inc]", $eachProductionCap->price_taka, ['class' => 'form-control input-md number apc_value_taka','id'=>"apc_value_taka",'readonly']) !!}
                                                                                        {!! $errors->first('price_taka','<span class="help-block">:message</span>') !!}
                                                                                    </td>
                                                                                    <?php $inc++; ?>
                                                                                </tr>
                                                                            @endforeach
                                                                        @else
                                                                            <tr id="rowProCostCount0" data-number="0">
                                                                                <td>
                                                                                    {!! Form::text("apc_product_name[0]", '', ['data-rule-maxlength'=>'255','class' => 'form-control input-md product apc_product_name','id'=>'apc_product_name','readonly']) !!}
                                                                                    {!! $errors->first('apc_product_name','<span class="help-block">:message</span>') !!}
                                                                                </td>
                                                                                <td>
                                                                                    {!! Form::select("apc_quantity_unit[0]",$productUnit,'',['class'=>'form-control input-md apc_quantity_unit', 'id' => 'apc_quantity_unit','readonly']) !!}
                                                                                    {!! $errors->first('apc_quantity_unit','<span class="help-block">:message</span>') !!}
                                                                                </td>
                                                                                <td>
                                                                                    <input type="number" id="apc_quantity" name="apc_quantity[{{$inc}}]" class="form-control quantity1 CalculateInputByBoxNo number apc_quantity" readonly>
                                                                                    {!! $errors->first('quantity','<span class="help-block">:message</span>') !!}
                                                                                </td>
                                                                                <td>
                                                                                    <input type="number" id="apc_price_usd" name="apc_price_usd[{{$inc}}]" class="form-control quantity1 CalculateInputByBoxNo number apc_price_usd" readonly>
                                                                                    {!! $errors->first('price_usd','<span class="help-block">:message</span>') !!}
                                                                                </td>
                                                                                <td>
                                                                                    {!! Form::text("apc_value_taka[0]", '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md number apc_value_taka','id'=>'apc_value_taka','readonly']) !!}
                                                                                    {!! $errors->first('price_taka','<span class="help-block">:message</span>') !!}
                                                                                </td>
                                                                            </tr>
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

                                        {{--3 Date of commercial operation:--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">3. Date of commercial operation</legend>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div id="date_of_arrival_div" class="col-md-8 {{$errors->has('commercial_operation_date') ? 'has-error': ''}}">
                                                        {!! Form::label('commercial_operation_date','Date of commercial operation',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            <div class="input-group date">
                                                                {!! Form::text('commercial_operation_date', (Session::get('brInfo.commercial_operation_date') && !empty(Session::get('brInfo.commercial_operation_date')) ? date('d-M-Y', strtotime(Session::get('brInfo.commercial_operation_date'))) : ''), ['class' => 'form-control input-md datepicker date', 'placeholder'=>'dd-mm-yyyy','readonly']) !!}
                                                            </div>
                                                            {!! $errors->first('commercial_operation_date','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--4 Sales (in 100%):--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">4. Sales (in 100%)</legend>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-4 {{$errors->has('local_sales') ? 'has-error': ''}}">
                                                        {!! Form::label('local_sales','Local ',['class'=>'col-md-4 text-left']) !!}
                                                        <div class="col-md-8">
                                                            {!! Form::number('local_sales', Session::get('brInfo.local_sales'), ['class' => 'form-control input-md number', 'id'=>'local_sales_per', 'min' => '0','readonly']) !!}
                                                            {!! $errors->first('local_sales','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 {{$errors->has('foreign_sales') ? 'has-error': ''}}">
                                                        {!! Form::label('foreign_sales','Foreign ',['class'=>'col-md-4 text-left']) !!}
                                                        <div class="col-md-8">
                                                            {!! Form::number('foreign_sales', Session::get('brInfo.foreign_sales'), ['class' => 'form-control input-md number', 'id'=>'foreign_sales_per', 'min' => '0','readonly']) !!}
                                                            {!! $errors->first('foreign_sales','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 {{$errors->has('total_sales') ? 'has-error': ''}}">
                                                        {!! Form::label('total_sales','Total in % ',['class'=>'col-md-4 text-left']) !!}
                                                        <div class="col-md-8">
                                                            {!! Form::number('total_sales', Session::get('brInfo.total_sales'), ['class' => 'form-control input-md number', 'id'=>'total_sales','readonly']) !!}
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
                                                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                    <tbody id="manpower">
                                                    <tr>
                                                        <th class="alert alert-info" colspan="3">Local (Bangladesh only) </th>
                                                        <th class="alert alert-info" colspan="3">Foreign (Abroad country) </th>
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
                                                            {!! Form::text('local_male', Session::get('brInfo.local_male'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'local_male' ,'readonly']) !!}
                                                            {!! $errors->first('local_male','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('local_female', Session::get('brInfo.local_female'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'local_female' ,'readonly']) !!}
                                                            {!! $errors->first('local_female','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('local_total', Session::get('brInfo.local_total'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field numberNoNegative required','id'=>'local_total','readonly' ]) !!}
                                                            {!! $errors->first('local_total','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('foreign_male', Session::get('brInfo.foreign_male'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'foreign_male','readonly']) !!}
                                                            {!! $errors->first('foreign_male','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('foreign_female', Session::get('brInfo.foreign_female'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'foreign_female','readonly']) !!}
                                                            {!! $errors->first('foreign_female','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('foreign_total', Session::get('brInfo.foreign_total'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field numberNoNegative required','id'=>'foreign_total','readonly']) !!}
                                                            {!! $errors->first('foreign_total','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('manpower_total', Session::get('brInfo.manpower_total'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'mp_total','readonly']) !!}
                                                            {!! $errors->first('manpower_total','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('manpower_local_ratio', Session::get('brInfo.manpower_local_ratio'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'mp_ratio_local','readonly']) !!}
                                                            {!! $errors->first('manpower_local_ratio','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('manpower_foreign_ratio', Session::get('brInfo.manpower_foreign_ratio'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'mp_ratio_foreign','readonly']) !!}
                                                            {!! $errors->first('manpower_foreign_ratio','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </fieldset>

                                        {{--6. Investment--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">6. Investment</legend>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
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

                                                    <tbody id="">
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
                                                                            {!! Form::number('local_land_ivst', Session::get('brInfo.local_land_ivst'), ['data-rule-maxlength'=>'40','class' => 'form-control total_investment_item input-md number','id'=>'local_land_ivst', 'onblur' => 'CalculateTotalInvestmentTk()' ,'readonly']) !!}
                                                                            {!! $errors->first('local_land_ivst','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                        <td>
                                                                            {!! Form::select("local_land_ivst_ccy", $currencyBDT, (Session::get('brInfo.local_land_ivst_ccy') ? Session::get('brInfo.local_land_ivst_ccy') : 114), ["id"=>"local_land_ivst_ccy", "class" => "form-control input-md usd-def readonly-pointer-disabled"]) !!}
                                                                            {!! $errors->first('local_land_ivst_ccy','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div style="position: relative;">
                                                                    <span class="helpTextCom" id="investment_building_label">&nbsp; Building <small>(Million)</small></span>
                                                                </div>
                                                            </td>
                                                            <td colspan="2">
                                                                <table style="width:100%;">
                                                                    <tr>
                                                                        <td style="width:75%;">
                                                                            {!! Form::number('local_building_ivst', Session::get('brInfo.local_building_ivst'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md total_investment_item number','id'=>'local_building_ivst', 'onblur' => 'CalculateTotalInvestmentTk()','readonly']) !!}
                                                                            {!! $errors->first('local_building_ivst','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                        <td>
                                                                            {!! Form::select("local_building_ivst_ccy", $currencyBDT, (Session::get('brInfo.local_building_ivst_ccy') ? Session::get('brInfo.local_building_ivst_ccy') : 114), ["id"=>"local_building_ivst_ccy", "class" => "form-control input-md usd-def readonly-pointer-disabled"]) !!}
                                                                            {!! $errors->first('local_building_ivst_ccy','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div style="position: relative;">
                                                                    <span class="required-star helpTextCom" id="investment_machinery_equp_label">&nbsp; Machinery & Equipment <small>(Million)</small></span>
                                                                </div>
                                                            </td>
                                                            <td colspan="2">
                                                                <table style="width:100%;">
                                                                    <tr>
                                                                        <td style="width:75%;">
                                                                            {!! Form::number('local_machinery_ivst', Session::get('brInfo.local_machinery_ivst'), ['data-rule-maxlength'=>'40','class' => 'form-control required input-md number total_investment_item','id'=>'local_machinery_ivst', 'onblur' => 'CalculateTotalInvestmentTk()','readonly']) !!}
                                                                            {!! $errors->first('local_machinery_ivst','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                        <td>
                                                                            {!! Form::select("local_machinery_ivst_ccy", $currencyBDT, (Session::get('brInfo.local_machinery_ivst_ccy') ? Session::get('brInfo.local_machinery_ivst_ccy') : 114), ["id"=>"local_machinery_ivst_ccy", "class" => "form-control input-md usd-def readonly-pointer-disabled"]) !!}
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
                                                                            {!! Form::number('local_others_ivst', Session::get('brInfo.local_others_ivst'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md number total_investment_item','id'=>'local_others_ivst',
                                                                            'onblur' => 'CalculateTotalInvestmentTk()','readonly']) !!}
                                                                            {!! $errors->first('local_others_ivst','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                        <td>
                                                                            {!! Form::select("local_others_ivst_ccy", $currencyBDT, (Session::get('brInfo.local_others_ivst_ccy') ? Session::get('brInfo.local_others_ivst_ccy') : 114), ["id"=>"local_others_ivst_ccy", "class" => "form-control input-md usd-def readonly-pointer-disabled"]) !!}
                                                                            {!! $errors->first('local_others_ivst_ccy','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="35%;">
                                                                <div style="position: relative;">
                                                                    <span class="helpTextCom" id="investment_working_capital_label">&nbsp; Working Capital <small>(Three Months) (Million)</small></span>
                                                                </div>
                                                            </td>
                                                            <td colspan="2">
                                                                <table style="width:100%;">
                                                                    <tr>
                                                                        <td style="width:75%;">
                                                                            {!! Form::number('local_wc_ivst', Session::get('brInfo.local_wc_ivst'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md number total_investment_item','id'=>'local_wc_ivst', 'onblur' => 'CalculateTotalInvestmentTk()','readonly']) !!}
                                                                            {!! $errors->first('local_wc_ivst','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                        <td>
                                                                            {!! Form::select("local_wc_ivst_ccy", $currencyBDT, (Session::get('brInfo.local_wc_ivst_ccy') ? Session::get('brInfo.local_wc_ivst_ccy') : 114), ["id"=>"local_wc_ivst_ccy", "class" => "form-control input-md usd-def readonly-pointer-disabled"]) !!}
                                                                            {!! $errors->first('local_wc_ivst_ccy','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div style="position: relative;">
                                                                    <span class="helpTextCom" id="investment_total_invst_mi_label">&nbsp; Total Investment <small>(Million) (BDT)</small></span>
                                                                </div>
                                                            </td>
                                                            <td width="50%">
                                                                {!! Form::number('total_fixed_ivst_million', Session::get('brInfo.total_fixed_ivst_million'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md numberNoNegative total_fixed_ivst_million required','id'=>'total_fixed_ivst_million','readonly']) !!}
                                                                {!! $errors->first('total_fixed_ivst_million','<span class="help-block">:message</span>') !!}
                                                            </td>
                                                            <td>
                                                                <div style="float: left; display: flex;">
                                                                    <div style="flex-grow: 1;" id="project_profile_label">Project&nbsp;profile:&nbsp;&nbsp;</div>
                                                                    <div style="flex-shrink: 0;">
                                                                        <input type="hidden" name="project_profile_attachment_data" id="project_profile_id" value="{{Session::get('brInfo.project_profile_attachment')}}">
                                                                        @if(!empty(Session::get('brInfo.project_profile_attachment')))
                                                                            <a style="margin-top:0px;" target="_blank" class="btn btn-xs btn-primary" href="{{URL::to('/uploads/'.Session::get('brInfo.project_profile_attachment'))}}">
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
                                                                    <span class="helpTextCom" id="investment_total_invst_bd_label">&nbsp; Total Investment <small>(BDT)</small></span>
                                                                </div>
                                                            </td>
                                                            <td colspan="3">
                                                                {!! Form::number('total_fixed_ivst', Session::get('brInfo.total_fixed_ivst'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md numberNoNegative total_invt_bdt required','id'=>'total_invt_bdt','readonly']) !!}
                                                                {!! $errors->first('total_fixed_ivst','<span class="help-block">:message</span>') !!}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div style="position: relative;">
                                                                    <span class="helpTextCom required-star" id="investment_total_invst_usd_label">&nbsp; Dollar exchange rate (USD)</span>
                                                                </div>
                                                            </td>
                                                            <td colspan="3">
                                                                {!! Form::number('usd_exchange_rate', Session::get('brInfo.usd_exchange_rate'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md numberNoNegative','id'=>'usd_exchange_rate','readonly']) !!}
                                                                {!! $errors->first('usd_exchange_rate','<span class="help-block">:message</span>') !!}
                                                                <span class="help-text">Exchange Rate Ref: <a href="https://www.bangladesh-bank.org/econdata/exchangerate.php" target="_blank">Bangladesh Bank</a>. Please Enter Today's Exchange Rate</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div style="position: relative;">
                                                                    <span class="helpTextCom" id="investment_total_fee_bd_label">&nbsp; Total Fee <small>(BDT)</small></span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <table>
                                                                    <tr>
                                                                        <td width="100%">
                                                                            {!! Form::text('total_fee', Session::get('brInfo.total_fee'), ['class' => 'form-control input-md number', 'id'=>'total_fee', 'readonly']) !!}
                                                                        </td>
                                                                        <td>
                                                                            <a type="button" class="btn btn-md btn-info" data-toggle="modal" data-target="#myModal">Govt. Fees Calculator</a>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </fieldset>

                                        {{--7. Source of finance--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">7. Source of finance</legend>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="table table-striped table-bordered"
                                                               cellspacing="0" width="100%">
                                                            <tbody>
                                                                <tr id="finance_src_loc_equity_1_row_id">
                                                                    <td>Local Equity (Million)</td>
                                                                    <td>
                                                                        {!! Form::number('finance_src_loc_equity_1', Session::get('brInfo.finance_src_loc_equity_1'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md number','id'=>'finance_src_loc_equity_1','onblur'=>"calculateSourceOfFinance(this.id)",'readonly']) !!}
                                                                        {!! $errors->first('finance_src_loc_equity_1','<span class="help-block">:message</span>') !!}
                                                                    </td>
                                                                </tr>
                                                            <tr  id="finance_src_foreign_equity_1_row_id">
                                                                <td width="38%">Foreign Equity (Million)</td>
                                                                <td>
                                                                    {!! Form::number('finance_src_foreign_equity_1', Session::get('brInfo.finance_src_foreign_equity_1'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md number','id'=>'finance_src_foreign_equity_1','onblur'=>"calculateSourceOfFinance(this.id)",'readonly']) !!}
                                                                    {!! $errors->first('finance_src_foreign_equity_1','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Total Equity (Million)</th>
                                                                <td>
                                                                    {!! Form::number('finance_src_loc_total_equity_1', Session::get('brInfo.finance_src_loc_total_equity_1'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md number readOnly','id'=>'finance_src_loc_total_equity_1','readonly']) !!}
                                                                    {!! $errors->first('finance_src_loc_total_equity_1','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Local Loan (Million)</td>
                                                                <td>
                                                                    {!! Form::number('finance_src_loc_loan_1', Session::get('brInfo.finance_src_loc_loan_1'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md number','id'=>'finance_src_loc_loan_1','onblur'=>"calculateSourceOfFinance(this.id)",'readonly']) !!}
                                                                    {!! $errors->first('finance_src_loc_loan_1','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Foreign Loan (Million)</td>
                                                                <td>
                                                                    {!! Form::number('finance_src_foreign_loan_1', Session::get('brInfo.finance_src_foreign_loan_1'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md number ','id'=>'finance_src_foreign_loan_1','onblur'=>"calculateSourceOfFinance(this.id)",'readonly']) !!}
                                                                    {!! $errors->first('finance_src_foreign_loan_1','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Total Loan (Million)</th>
                                                                <td>
                                                                    {!! Form::number('finance_src_total_loan', Session::get('brInfo.finance_src_total_loan'), ['id'=>'finance_src_total_loan','class' => 'form-control input-md readOnly numberNoNegative', 'data-rule-maxlength'=>'240','readonly']) !!}
                                                                    {!! $errors->first('finance_src_total_loan','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Total Financing Million</th>
                                                                <td>
                                                                    {!! Form::number('finance_src_loc_total_financing_m', Session::get('brInfo.finance_src_loc_total_financing_m'), ['id'=>'finance_src_loc_total_financing_m','class' => 'form-control input-md readOnly numberNoNegative', 'data-rule-maxlength'=>'240','readonly']) !!}
                                                                    {!! $errors->first('finance_src_loc_total_financing_m','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Total Financing BDT</th>
                                                                <td colspan="3">
                                                                    {!! Form::number('finance_src_loc_total_financing_1', Session::get('brInfo.finance_src_loc_total_financing_1'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md numberNoNegative readOnly','id'=>'finance_src_loc_total_financing_1','readonly']) !!}
                                                                    {!! $errors->first('finance_src_loc_total_financing_1','<span class="help-block">:message</span>') !!}
                                                                    <span class="text-danger" style="font-size: 12px; font-weight: bold" id="finance_src_loc_total_financing_1_alert"></span>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                        <table>
                                                            <tr>
                                                                <th colspan="4">
                                                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top"
                                                                       title="From the above information, the values of Local Equity (Million) and “Local Loan (Million) will go into the
                                                                                Equity Amount and Loan Amount respectively for Bangladesh. The summation of the Equity Amount and
                                                                                Loan Amount of other countries will be equal to the values of Foreign Equity (Million) and Foreign Loan (Million) respectively."></i>
                                                                    Country wise source of finance (Million BDT)
                                                                </th>
                                                            </tr>
                                                        </table>

                                                        <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="financeTableId">
                                                            <thead>
                                                            <tr>
                                                                <th class="required-star">Country</th>
                                                                <th class="required-star">Equity Amount
                                                                    <span class="text-danger" id="equity_amount_err"></span>
                                                                </th>
                                                                <th class="required-star"> Loan Amount
                                                                    <span class="text-danger" id="loan_amount_err"></span>
                                                                </th>
                                                            </tr>
                                                            </thead>
                                                            @if(count(Session::get('brSourceOfFinance')) > 0)
                                                                <?php $inc = 0; ?>
                                                                @foreach(Session::get('brSourceOfFinance') as $finance)
                                                                    <tr id="financeTableIdRow{{$inc}}" data-number="1">
                                                                        <td>
                                                                            {!!Form::select("country_id[$inc]", $countries, $finance->country_id, ['class' => 'form-control required readonly-pointer-disabled', 'style' => 'width: 100%'])!!}
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
                                                                <tr id="financeTableIdRow0" data-number="1">
                                                                    <td>
                                                                        {!!Form::select('country_id[]', $countries, null, ['class' => 'form-control required readonly-pointer-disabled', 'style' => 'width: 100%'])!!}
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
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--8. Public Utility Service--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border"><span class="required-star">8. Public utility service</span>
                                            </legend>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_land"
                                                                @if(Session::get('brInfo.public_land') == 1) checked="checked" @endif disabled >Land
                                                        </label>
                                                        @if(Session::get('brInfo.public_others') == 1)
                                                            <input type="hidden" name="public_land" value="1">
                                                        @endif

                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_electricity"
                                                                @if(Session::get('brInfo.public_electricity') == 1) checked="checked" @endif disabled>Electricity
                                                        </label>
                                                        @if(Session::get('brInfo.public_electricity') == 1)
                                                            <input type="hidden" name="public_electricity" value="1">
                                                        @endif
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_gas"
                                                                @if(Session::get('brInfo.public_gas') == 1) checked="checked" @endif disabled>Gas
                                                        </label>
                                                        @if(Session::get('brInfo.public_gas') == 1)
                                                            <input type="hidden" name="public_gas" value="1">
                                                        @endif
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_telephone"
                                                                @if(Session::get('brInfo.public_telephone') == 1) checked="checked" @endif disabled>Telephone
                                                        </label>
                                                        @if(Session::get('brInfo.public_telephone') == 1)
                                                            <input type="hidden" name="public_telephone" value="1">
                                                        @endif
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_road"
                                                                @if(Session::get('brInfo.public_road') == 1) checked="checked" @endif disabled>Road
                                                        </label>
                                                        @if(Session::get('brInfo.public_road') == 1)
                                                            <input type="hidden" name="public_road" value="1">
                                                        @endif
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_water"
                                                                @if(Session::get('brInfo.public_water') == 1) checked="checked" @endif disabled>Water
                                                        </label>
                                                        @if(Session::get('brInfo.public_water') == 1)
                                                            <input type="hidden" name="public_water" value="1">
                                                        @endif
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_drainage"
                                                                @if(Session::get('brInfo.public_drainage') == 1) checked="checked" @endif disabled>Drainage
                                                        </label>
                                                        @if(Session::get('brInfo.public_drainage') == 1)
                                                            <input type="hidden" name="public_drainage" value="1">
                                                        @endif
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" id="public_others" name="public_others" class="other_utility myCheckBox"
                                                                @if(Session::get('brInfo.public_others') == 1) checked="checked" @endif disabled>Others
                                                        </label>
                                                        @if(Session::get('brInfo.public_others') == 1)
                                                            <input type="hidden" name="public_others" value="1">
                                                        @endif

                                                    </div>
                                                    <div class="col-md-12" hidden style="margin-top: 5px;"
                                                         id="public_others_field_div">
                                                        {!! Form::text('public_others_field', Session::get('brInfo.public_others_field'), ['placeholder'=>'Specify others', 'class' => 'form-control input-md', 'id' => 'public_others_field','readonly']) !!}
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
                                                            {!! Form::text('trade_licence_num', Session::get('brInfo.trade_licence_num'), ['class' => 'form-control input-md required','readonly']) !!}
                                                            {!! $errors->first('trade_licence_num','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group {{$errors->has('trade_licence_issuing_authority') ? 'has-error': ''}}">
                                                        {!! Form::label('trade_licence_issuing_authority','Issuing Authority',['class'=>'col-md-5 text-left required-star']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('trade_licence_issuing_authority', Session::get('brInfo.trade_licence_issuing_authority'), ['class' => 'form-control input-md required','readonly']) !!}
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
                                                            {!! Form::text('tin_number', Session::get('brInfo.tin_number'), ['class' => 'form-control input-md required','readonly']) !!}
                                                            {!! $errors->first('tin_number','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </fieldset>

                                        <!--{{-- 11. Description of machinery and equipment--}}
                                         <fieldset class="scheduler-border" id="machinery_equipment">
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
                                                            {!! Form::text('machinery_local_qty', Session::get('brInfo.machinery_local_qty'), ['class' => 'form-control input-md','id' => 'machinery_local_qty','onkeyup' => 'totalMachineryEquipmentQty()','readonly']) !!}
                                                            {!! $errors->first('machinery_local_qty','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('machinery_local_price_bdt', Session::get('brInfo.machinery_local_price_bdt'), ['class' => 'form-control input-md','id' => 'machinery_local_price_bdt','onkeyup'=>"totalMachineryEquipmentPrice()",'readonly']) !!}
                                                            {!! $errors->first('machinery_local_price_bdt','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Imported</td>
                                                        <td>
                                                            {!! Form::text('imported_qty', Session::get('brInfo.imported_qty'), ['class' => 'form-control input-md', 'id'=>'imported_qty', 'onkeyup' => 'totalMachineryEquipmentQty()','readonly']) !!}
                                                            {!! $errors->first('imported_qty','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('imported_qty_price_bdt', Session::get('brInfo.imported_qty_price_bdt'), ['class' => 'form-control input-md','id' => 'imported_qty_price_bdt','onkeyup'=>"totalMachineryEquipmentPrice()",'readonly']) !!}
                                                            {!! $errors->first('imported_qty_price_bdt','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total</td>
                                                        <td>
                                                            {!! Form::text('total_machinery_qty', Session::get('brInfo.total_machinery_qty'), ['class' => 'form-control input-md','id' => 'total_machinery_qty','readonly']) !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('total_machinery_price', Session::get('brInfo.total_machinery_price'), ['class' => 'form-control input-md','id' => 'total_machinery_price','readonly']) !!}
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </fieldset> -->

                                        <!-- {{-- 12. Description of raw & packing materials --}}
                                         <fieldset class="scheduler-border" id="packing_materials">
                                            <legend class="scheduler-border">12. Description of raw &amp; packing materials </legend>
                                            <div class="table-responsive">
                                                <table class="table table-bordered dt-responsive" cellspacing="0" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <th class="col-md-2">Locally</th>
                                                            <td class="col-md-10">
                                                                {!! Form::textarea('local_description', Session::get('brInfo.local_description'), ['class' => 'form-control bigInputField input-md maxTextCountDown',
                                                                'id' => 'local_description', 'size'=>'5x2','data-charcount-maxlength'=>'1000','readonly']) !!}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="col-md-2">Imported</th>
                                                            <td class="col-md-10">
                                                                {!! Form::textarea('imported_description', Session::get('brInfo.imported_description'), ['class' => 'form-control bigInputField input-md maxTextCountDown',
                                                                'id' => 'imported_description', 'size'=>'5x2','data-charcount-maxlength'=>'1000','readonly']) !!}
                                                            </td>
                                                        </tr>
                                                    </tbody>

                                                </table>
                                            </div>
                                        </fieldset>  -->
                                    </div>
                                </div>
                            </fieldset>

                            <h3 class="stepHeader">List if Directors</h3>
                            <fieldset>
                                <div class="panel panel-info">
                                    <div class="panel-heading"><strong>List of directors and high authorities</strong></div>
                                    <div class="panel-body">
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">Information of (Chairman/ Managing Director/ Or Equivalent): </legend>
                                            <div class="row">
                                                <div class="form-group col-md-6 {{$errors->has('g_full_name') ? 'has-error': ''}}">
                                                    {!! Form::label('g_full_name','Full Name',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('g_full_name', Session::get('brInfo.g_full_name'), ['class' => 'form-control input-md required','readonly']) !!}
                                                        {!! $errors->first('g_full_name','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6 {{$errors->has('g_designation') ? 'has-error': ''}}">
                                                    {!! Form::label('g_designation','Position/ Designation',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('g_designation', Session::get('brInfo.g_designation'), ['class' => 'form-control input-md required','readonly']) !!}
                                                        {!! $errors->first('g_designation','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('g_signature') ? 'has-error': ''}}">
                                                    <div class="form-group">
                                                        {!! Form::label('g_signature','Signature', ['class'=>'text-left col-md-5 required-star']) !!}
                                                        <div class="col-md-7">
                                                            <div id="investorSignatureViewerDiv">
                                                                <figure>
                                                                    <img class="img-thumbnail img-signature" id="investor_signature_preview"
                                                                         src="{{ (!empty(Session::get('brInfo.g_signature'))? url('uploads/'.Session::get('brInfo.g_signature')) : url('assets/images/photo_default.png')) }}"
                                                                         alt="Investor Signature" style="width: 100%;">
                                                                </figure>

                                                                <input type="hidden" id="investor_signature_base64" name="investor_signature_base64"/>
                                                                @if(!empty(Session::get('brInfo.g_signature')))
                                                                    <input type="hidden" id="investor_signature_hidden" name="investor_signature_hidden" value="{{Session::get('brInfo.g_signature')}}"/>
                                                                @endif
                                                            </div>
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
                                    <div class="panel-heading "><strong>List of Machineries</strong></div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="scheduler-border">
                                                    <legend class="scheduler-border"> List of total importable machinery as registered with BIDA</legend>
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                @if(count(Session::get('brListOfMachineryImported')) > 0)
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
                                                                                <?php $inc = 0; $machinery_imported_sum = 0; ?>

                                                                            @foreach(Session::get('brListOfMachineryImported') as $machineryImported)
                                                                                    <?php $machinery_imported_sum += $machineryImported->total_value; ?>
                                                                                <tr>
                                                                                    <td>
                                                                                        {!! Form::text("l_machinery_imported_name[$inc]", $machineryImported->name, ['class' => 'form-control input-md product required','readonly']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::text("l_machinery_imported_qty[$inc]", $machineryImported->quantity, ['class' => 'form-control input-md product required','readonly']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::text("l_machinery_imported_unit_price[$inc]", $machineryImported->unit_price, ['class' => 'form-control input-md required number', 'readonly']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::number("l_machinery_imported_total_value[$inc]", $machineryImported->total_value, ['class' => 'form-control input-md required machinery_imported_total_value numberNoNegative', 'onkeyup' => "calculateListOfMachineryTotal('machinery_imported_total_value', 'machinery_imported_total_amount')" ,'readonly']) !!}
                                                                                    </td>
                                                                                </tr>
                                                                                    <?php $inc++; ?>
                                                                            @endforeach

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
                                                                @endif

                                                            </div>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="scheduler-border">
                                                    <legend class="scheduler-border">List of machinery to be imported under this application </legend>
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="table-responsive">
                                                                    <table id="machineryImportedSpare" class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
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
                                                                        <?php $inc = 0; ?>
                                                                        @if(Session::has('listOfMachineryImportedMaster'))
                                                                            @foreach(Session::get('listOfMachineryImportedMaster') as $machineryImportedMaster)
                                                                                <tr id="rowMachineryImportedSpare{{$inc}}" data-number="1">
                                                                                    {!! Form::hidden("master_ref_id[$inc]", Encryption::encodeId($machineryImportedMaster->id), ['class' => 'form-control input-md product required','readonly']) !!}
                                                                                    <td>
                                                                                        {!! Form::text("name[$inc]", $machineryImportedMaster->name, ['class' => 'form-control input-md product required','readonly']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::text("quantity[$inc]", $machineryImportedMaster->quantity, ['class' => 'form-control input-md product required ','readonly']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::text("remaining_qty_show[$inc]", $machineryImportedMaster->quantity - $machineryImportedMaster->total_imported, ['class' => 'form-control input-md required remaining_qty_calculation', 'readonly']) !!}
                                                                                        {!! Form::hidden("remaining_quantity[$inc]",  $machineryImportedMaster->quantity - $machineryImportedMaster->total_imported, ['class' => 'form-control input-md required', 'readonly']) !!}

                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::text("required_quantity[$inc]", '', ['class' => 'form-control input-md product required remaining_qty_calculation number', 'required' => 'required']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::select("machinery_type[$inc]", ['new' => 'New', 'used' => 'Used'], '' , ["placeholder" => "Select One","id"=>"machinery_type", "class" => "form-control input-md usd-def", 'required' => 'required']) !!}
                                                                                        {!! $errors->first('machinery_type','<span class="help-block">:message</span>') !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::text("hs_code[$inc]", '', ['class' => 'form-control input-md product', 'required' => 'required']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::text("bill_loading_no[$inc]", '', ['class' => 'form-control input-md product', 'required' => 'required']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::text("bill_loading_date[$inc]", '', ['class' => 'form-control input-md product datepicker dateSpace', 'onblur' => 'dateChecker()', 'placeholder'=>'DD-MMM-YYYY', 'required' => 'required']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::text("invoice_no[$inc]", '', ['class' => 'form-control input-md product', 'required' => 'required']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::text("invoice_date[$inc]", '', ['class' => 'form-control input-md product datepicker dateSpace', 'onblur' => 'dateChecker()', 'placeholder'=>'DD-MMM-YYYY', 'required' => 'required']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::number("total_value_equivalent_usd[$inc]", '', ['class' => 'form-control input-md product', 'required' => 'required']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::select("total_value_ccy[$inc]", $currencies, 107, ["placeholder" => "Select One","id"=>"total_value_ccy", "class" => "form-control input-md usd-def", 'required' => 'required']) !!}
                                                                                        {!! $errors->first('total_value_ccy','<span class="help-block">:message</span>') !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::number("total_value_as_per_invoice[$inc]", '', ['class' => 'form-control input-md product', 'required' => 'required']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        <a href="javascript:void(0);" class="btn btn-md btn-danger removeRow" onclick="removeTableRow('machineryImportedSpare','rowMachineryImportedSpare{{$inc}}');">
                                                                                            <i class="fa fa-times" aria-hidden="true"></i>
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                                                                    <?php $inc++; ?>
                                                                            @endforeach
                                                                        @elseif (Session::has('brListOfMachineryImported'))
                                                                            @foreach(Session::get('brListOfMachineryImported') as $machineryImportedBR)
                                                                                <tr id="rowMachineryImportedSpare{{$inc}}" data-number="1">
                                                                                    {!! Form::hidden("master_ref_id[$inc]", Encryption::encodeId($machineryImportedBR->id)) !!}
                                                                                    <td>
                                                                                        {!! Form::text("name[$inc]", $machineryImportedBR->l_machinery_imported_name, ['class' => 'form-control input-md product required','readonly']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::text("quantity[$inc]", $machineryImportedBR->l_machinery_imported_qty, ['class' => 'form-control input-md product required ','readonly']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::text("remaining_qty_show[$inc]", $machineryImportedBR->l_machinery_imported_qty, ['class' => 'form-control input-md required remaining_qty_calculation', 'readonly']) !!}
                                                                                        {!! Form::hidden("remaining_quantity[$inc]",  $machineryImportedBR->l_machinery_imported_qty, ['class' => 'form-control input-md required', 'readonly']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::text("required_quantity[$inc]", '', ['class' => 'form-control input-md product required remaining_qty_calculation number', 'required' => 'required']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::select("machinery_type[$inc]", ['new' => 'New', 'used' => 'Used'], '' , ["placeholder" => "Select One","id"=>"machinery_type", "class" => "form-control input-md usd-def", 'required' => 'required']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::text("hs_code[$inc]", '', ['class' => 'form-control input-md product', 'required' => 'required']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::text("bill_loading_no[$inc]", '', ['class' => 'form-control input-md product', 'required' => 'required']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::text("bill_loading_date[$inc]", '', ['class' => 'form-control input-md product datepicker dateSpace', 'onblur' => 'dateChecker()', 'placeholder'=>'DD-MMM-YYYY', 'required' => 'required']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::text("invoice_no[$inc]", '', ['class' => 'form-control input-md product', 'required' => 'required']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::text("invoice_date[$inc]", '', ['class' => 'form-control input-md product datepicker dateSpace', 'onblur' => 'dateChecker()', 'placeholder'=>'DD-MMM-YYYY', 'required' => 'required']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::number("total_value_equivalent_usd[$inc]", '', ['class' => 'form-control input-md product', 'required' => 'required']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::select("total_value_ccy[$inc]", $currencies, 107, ["placeholder" => "Select One","id"=>"total_value_ccy", "class" => "form-control input-md usd-def", 'required' => 'required']) !!}
                                                                                        {!! $errors->first('total_value_ccy','<span class="help-block">:message</span>') !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::number("total_value_as_per_invoice[$inc]", '', ['class' => 'form-control input-md product', 'onchange' => 'formatDecimal(this)', 'required' => 'required']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        <a href="javascript:void(0);" class="btn btn-md btn-danger removeRow" onclick="removeTableRow('machineryImportedSpare','rowMachineryImportedSpare{{$inc}}');">
                                                                                            <i class="fa fa-times" aria-hidden="true"></i>
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                                                                    <?php $inc++; ?>
                                                                            @endforeach
                                                                        @endif
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </fieldset>
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
                                                        {!! Form::text('sfp_contact_name', \App\Libraries\CommonFunction::getUserFullName(), ['class' => 'form-control input-md required']) !!}
                                                        {!! $errors->first('sfp_contact_name','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('sfp_contact_email') ? 'has-error': ''}}">
                                                    {!! Form::label('sfp_contact_email','Contact email',['class'=>'col-md-5 text-left required-star']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::email('sfp_contact_email', Auth::user()->user_email, ['class' => 'form-control input-md required email']) !!}
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
                                                        {!! Form::text('sfp_contact_phone', Auth::user()->user_phone, ['class' => 'form-control input-md required helpText15 phone_or_mobile']) !!}
                                                        {!! $errors->first('sfp_contact_phone','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('sfp_contact_address') ? 'has-error': ''}}">
                                                    {!! Form::label('sfp_contact_address','Contact address',['class'=>'col-md-5 text-left required-star']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('sfp_contact_address', Auth::user()->road_no .  (!empty(Auth::user()->house_no) ? ', ' . Auth::user()->house_no : ''), ['class' => 'form-control input-md required']) !!}
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
                                                        {!! Form::text('sfp_pay_amount', $payment_config->amount, ['class' => 'form-control input-md', 'readonly']) !!}
                                                        {!! $errors->first('sfp_pay_amount','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('sfp_vat_on_pay_amount') ? 'has-error': ''}}">
                                                    {!! Form::label('sfp_vat_on_pay_amount','VAT on pay amount',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('sfp_vat_on_pay_amount', $payment_config->vat_on_pay_amount, ['class' => 'form-control input-md', 'readonly']) !!}
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
                                                        {!! Form::text('sfp_total_amount', number_format($payment_config->amount + $payment_config->vat_on_pay_amount, 2), ['class' => 'form-control input-md', 'readonly']) !!}
                                                    </div>
                                                </div>

                                                <div class="col-md-6 {{$errors->has('sfp_status') ? 'has-error': ''}}">
                                                    {!! Form::label('sfp_status','Payment Status',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        <span class="label label-warning">Not Paid</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

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
                                                                {!! Form::text('auth_full_name', CommonFunction::getUserFullName(), ['class' => 'form-control required input-md', 'readonly']) !!}
                                                                {!! $errors->first('auth_full_name','<span class="help-block">:message</span>') !!}
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-6 {{$errors->has('auth_designation') ? 'has-error': ''}}">
                                                            {!! Form::label('auth_designation','Designation',['class'=>'col-md-5 text-left']) !!}
                                                            <div class="col-md-7">
                                                                {!! Form::text('auth_designation', Auth::user()->designation, ['class' => 'form-control required input-md', 'readonly']) !!}
                                                                {!! $errors->first('auth_designation','<span class="help-block">:message</span>') !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-6 {{$errors->has('auth_mobile_no') ? 'has-error': ''}}">
                                                            {!! Form::label('auth_mobile_no','Mobile No.',['class'=>'col-md-5 text-left']) !!}
                                                            <div class="col-md-7">
                                                                {!! Form::text('auth_mobile_no', Auth::user()->user_phone, ['class' => 'form-control required input-sm phone_or_mobile', 'readonly']) !!}
                                                                {!! $errors->first('auth_mobile_no','<span class="help-block">:message</span>') !!}
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-6 {{$errors->has('auth_email') ? 'has-error': ''}}">
                                                            {!! Form::label('auth_email','Email address',['class'=>'col-md-5 text-left']) !!}
                                                            <div class="col-md-7">
                                                                {!! Form::email('auth_email', Auth::user()->user_email, ['class' => 'form-control required input-sm email', 'readonly']) !!}
                                                                {!! $errors->first('auth_email','<span class="help-block">:message</span>') !!}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-md-6 {{$errors->has('auth_image') ? 'has-error': ''}}">
                                                            {!! Form::label('auth_image','Picture',['class'=>'col-md-5 text-left']) !!}
                                                            <div class="col-md-7">
                                                                <img class="img-thumbnail img-user"
                                                                     src="{{ (!empty(Auth::user()->user_pic) ? url('users/upload/'.Auth::user()->user_pic) : url('assets/images/photo_default.png')) }}"
                                                                     alt="User Photo">
                                                            </div>
                                                            <input type="hidden" name="auth_image" value="{{ Auth::user()->user_pic }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                    {!! Form::checkbox('accept_terms',1,null, array('id'=>'accept_terms', 'class'=>'required')) !!}
                                                    I do here by declare that the information given above is true to the best of my knowledge and I shall be liable for any false information/ statement is given.
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <div class="pull-left">
                                <button type="submit" class="btn btn-info btn-md cancel" value="draft" name="actionBtn" id="save_as_draft">
                                    Save as Draft
                                </button>
                            </div>
                            <div class="pull-left" style="padding-left: 1em;">
                                <button type="submit" id="submitForm" style="cursor: pointer;" class="btn btn-success btn-md" value="Submit" name="actionBtn">
                                    Payment & Submit
                                    <i class="fa fa-question-circle" style="cursor: pointer" data-toggle="tooltip" title="" data-original-title="After clicking this button will take you in the payment portal for providing the required payment. After payment, the application will be automatically submitted and you will get a confirmation email with necessary info." aria-describedby="tooltip"></i>
                                </button>
                            </div>

                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
            {{--End application form with wizard--}}
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

<script src="{{ asset("assets/scripts/jquery.steps.js") }}"></script>
{{--Datepicker js--}}
<script src="{{ asset("vendor/datepicker/datepicker.min.js") }}"></script>

@include('partials.image-resize.image-upload')

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


<script>

    function loadUnique(element) {
        var last_tr = $(element).closest('tr').find('.em_unique').val();
        $(".modal-body #existingMachineId").val(last_tr);
    }

    // function lastBidaRegistration(isOnline) {
    //     if (isOnline == 'yes') {
    //         $("#ref_app_tracking_no_div").removeClass('hidden');
    //         $("#ref_app_tracking_no").addClass('required');

    //     } else {
    //         $("#ref_app_tracking_no_div").addClass('hidden');
    //         $("#ref_app_tracking_no").removeClass('required');
    //     }
    // }

</script>


<script type="text/javascript">
    //get session info
    var sessionLastBR = '{{ Session::get('brInfo.last_br') }}';

    //4. Source of Finance Foreign Equity
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
        var total_machinery = 0.00;
        $("." + className).each(function () {
            total_machinery = total_machinery + (this.value ? parseFloat(this.value) : 0.00);
        });
        $("#" + totalShowFieldId).val(total_machinery.toFixed(3));

        if(className == 'em_price_bdt'){
            var $tr = $(option).closest('tr');
            var price_bdt = $tr.find('td:eq(6) input').val();
            $tr.find('td:eq(7) input').val((price_bdt/1000000).toFixed(5));

            var total_lc_taka_mil = 0.00000;
            $(".em_price_taka_mil").each(function () {
                total_lc_taka_mil = total_lc_taka_mil + (this.value ? parseFloat(this.value) : 0.00000);
            });
            $("#total_lc_taka_mil").val(total_lc_taka_mil.toFixed(5));
        }

        if (className == "em_local_price_taka_mil") {
            var em_local_price_taka_mil = 0.000000;
            $("." + className).each(function () {
                em_local_price_taka_mil = em_local_price_taka_mil + (this.value ? parseFloat(this.value) : 0.00000);
            })
            $("#" + totalShowFieldId).val(em_local_price_taka_mil.toFixed(5));
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
    }

    $(document).ready(function () {
        var form = $("#ImportPermission").show();
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
                if (newIndex == 1) {

                    //Previous data loaded validation check
                    var app_tracking_no = $("input[name='ref_app_tracking_no']").val();
                    var app_approve_date = $("input[name='ref_app_approve_date']").val();
                    var reg_no = $("#reg_no").val();

                    if(app_tracking_no && app_approve_date && reg_no){
                        return true;
                    }
                    swal({type: 'error', text: "Please, load BIDA Registration data."});
                    return false;
                }

                if (newIndex == 2) {
                    // Check Total Financing and  Total Investment (BDT) is equal
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

                    // valid bbs code check
                    if ($("#is_valid_bbs_code").val() == 0) {
                        alert('Business Sector (BBS Class Code) is required. Please enter or select from the above list.')
                        return false;
                    }
                }

                if (newIndex == 4) {
                    var dateInputs = $('.dateSpace');
                    var dateFormatPattern = /^\d{2}-(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)-\d{4}$/;
                    for (var i = 0; i < dateInputs.length; i++) {
                        var dateInputValue = $(dateInputs[i]).val();

                        if (!dateFormatPattern.test(dateInputValue) && dateInputValue != '') {
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
                        // console.log(form.validate().errors()); // show hidden errors in last step
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

        if (sessionLastBR == 'yes') {
            lastBidaRegistration(sessionLastBR);
            $("#ref_app_tracking_no").prop('readonly', true);
        }

        var popupWindow = null;
        $('.finish').on('click', function (e) {
            if (form.valid()) {
                $('body').css({"display": "none"});
                popupWindow = window.open('<?php echo URL::to('/import-permission/preview'); ?>', 'Sample', '');
            } else {
                return false;
            }
        });

        var class_code = '{{ Session('brInfo.class_code') }}';
        var sub_class_id = '{{ Session('brInfo.sub_class_id') }}';
        findBusinessClassCode(class_code, sub_class_id);

    });
    // New end
    //--------Step Form init+validation End----------//
    var popupWindow = null;
    $('.finish').on('click', function (e) {
        if (form.valid()) {
            $('body').css({"display": "none"});
            popupWindow = window.open('<?php echo URL::to('/import-permission/preview'); ?>', 'Sample', '');
        } else {
            form.validate().settings.ignore = ":disabled,:hidden";
            return form.valid();
        }
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

    /********Calculating the numbers of two fields inside multiple rowed tables******/

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

        $("#business_sector_id").trigger('change');
        $("#organization_status_id").trigger('change');


        $('#ImportPermission').validate({
            rules: {
                ".myCheckBox": {required: true, maxlength: 1}
            }
        });

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
                // $("#ceo_state").addClass('required');
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

        $('.readOnlyCl input,.readOnlyCl select,.readOnlyCl textarea,.readOnly').not("#business_class_code, #major_activities, #business_sector_id, #business_sector_others, #business_sub_sector_id, #business_sub_sector_others, #country_of_origin_id, #organization_status_id, #project_name").attr('readonly', true);
        $(".readOnlyCl select").each(function () {
            var id = $(this).attr('id');
            if (id != 'business_sector_id' && id != 'business_sub_sector_id' && id != 'country_of_origin_id' && id != 'organization_status_id') {
                $("#" + id + " option:not(:selected)").prop('disabled', true);
            }
        });

    });


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
                    $('#' + targets).html(response);
                    var fileNameArr = inputFile.split("\\");
                    var l = fileNameArr.length;
                    if ($('#label_' + id).length)
                        $('#label_' + id).remove();
                    var doc_id = parseInt(id.substring(4));
                    var newInput = $('<label class="saved_file_' + doc_id + '" id="label_' + id + '"><br/><b class="remove-uploaded-file">File: ' + fileNameArr[l - 1] +
                        ' <a href="javascript:void(0)" onclick="EmptyFile(' + doc_id
                        + ', ' + isRequired + ')"><span class="btn btn-xs btn-danger"><i class="fa fa-times"></i></span> </a></b></label>');

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

        checkOrganizationStatusId();
        $("#organization_status_id").change(checkOrganizationStatusId);

        $("#public_others").click(function () {
            $("#public_others_field_div").hide('slow');
            $("#public_others_field").removeClass('required');
            var isOtherChecked = $(this).is(':checked');
            if (isOtherChecked == true) {
                $("#public_others_field_div").show('slow');
                $("#public_others_field").addClass('required');
            }
        });

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
                            option += '<option value="' + id + '">' + value + '</option>';
                        });
                    }
                    $("#ceo_thana_id").html(option);
                    $(self).next().hide('slow');
                }
            });
        });

        $("#office_division_id").change(function () {
            var divisionId = $('#office_division_id').val();
            $(this).after('<span class="loading_data">Loading...</span>');
            var self = $(this);
            $.ajax({
                type: "GET",
                url: "<?php echo url(); ?>/import-permission/get-district-by-division",
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
                            option += '<option value="' + id + '">' + value + '</option>';
                        });
                    }
                    $("#office_thana_id").html(option);
                    $(self).next().hide('slow');
                }
            });
        });

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
                            option += '<option value="' + id + '">' + value + '</option>';
                        });
                    }
                    $("#factory_thana_id").html(option);
                    $(self).next().hide('slow');
                }
            });
        });
    });

    function checkOrganizationStatusId() {
        var organizationStatusId = $("#organization_status_id").val();
        if (organizationStatusId == 3) {
            $(".country_of_origin_div").hide('slow');
            $("#country_of_origin_id").removeClass('required');


            $("#finance_src_foreign_equity_1").val('');
            $("#finance_src_foreign_equity_1").blur();
            $("#finance_src_foreign_equity_1_row_id").hide('slow');
            $("#finance_src_loc_equity_1_row_id").show('slow');
        } else if (organizationStatusId == 2){
            $("#finance_src_loc_equity_1").val('');
            $("#finance_src_loc_equity_1").blur();
            $("#finance_src_loc_equity_1_row_id").hide('slow');
            $("#finance_src_foreign_equity_1_row_id").show('slow');
        }
        else {
            $(".country_of_origin_div").show('slow');
            $("#country_of_origin_id").addClass('required');

            $("#finance_src_loc_equity_1_row_id").show('slow');
            $("#finance_src_foreign_equity_1_row_id").show('slow');
        }

        organizationStatusWiseDocLoad(organizationStatusId);
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

</script>

<script src="{{ asset("build/js/intlTelInput-jquery_v16.0.8.min.js") }}" type="text/javascript"></script>
<script src="{{ asset("build/js/utils_v16.0.8.js") }}" type="text/javascript"></script>
{{--//textarea count down--}}
<script src="{{ asset("vendor/character-counter/jquery.character-counter_v1.0.min.js") }}" type="text/javascript"></script>
{{--select2 js--}}
<script src="{{ asset("assets/plugins/select2.min.js") }}"></script>

<script>
    $(function () {
        // DocLoad();
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

        {{--Select2 calling--}}
        $(".select2").select2();

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
    });



    function findBusinessClassCode(selectClass, sub_class_id) {

        var business_class_code = (selectClass !== undefined) ? selectClass : $("#business_class_code").val();
        var sub_class_id = (sub_class_id !== undefined) ? sub_class_id : 0;

        var _token = $('input[name="_token"]').val();

        var other_code = '{{ Session::get('brInfo.other_sub_class_code') }}';
        var other_name = '{{ Session::get('brInfo.other_sub_class_name') }}';


        if (business_class_code != '' && (business_class_code.length > 3)) {

            $("#business_class_list_of_code").text('');
            $("#business_class_list").html('');

            $.ajax({
                type: "GET",
                url: "/import-permission/get-business-class-single-list",
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

                        var sub_class_id = '{{(Session::get('brInfo.sub_class_id') === 0) ? '-1' : Session::get('brInfo.sub_class_id')}}';

                        var option = '<option value="">Select One</option>';
                        $.each(response.subClass, function (id, value) {
                            if (id == sub_class_id) {
                                option += '<option value="' + id + '" selected> ' + value + '</option>';
                            } else {
                                option += '<option value="' + id + '">' + value + '</option>';
                            }
                        });

                        table_row += '<tr><td width="10%" class="required-star">Sub class</td><td colspan="2"><select onchange="otherSubClassCodeName(this.value)" name="sub_class_id" class="form-control required readonly-pointer-disabled" >' + option + '</select></td></tr>';

                        table_row += '<tr id="other_sub_class_code_parent" class="hidden"><td width="20%" class="">Other sub class code</td><td colspan="2"><input type="text" name="other_sub_class_code" id="other_sub_class_code" class="form-control" readonly value="'+ other_code +'"></td></tr>';
                        table_row += '<tr id="other_sub_class_name_parent" class="hidden"><td width="20%" class="required-star">Other sub class name</td><td colspan="2"><input type="text" name="other_sub_class_name" id="other_sub_class_name" class="form-control" readonly value="'+ other_name +'"></td></tr>';

                        $("#business_class_list_of_code").text(business_class_code);
                        $("#business_class_list").html(table_row);
                        $("#is_valid_bbs_code").val(1);
                        if (sub_class_id === '-1') {
                            otherSubClassCodeName(sub_class_id);
                        }

                    } else {
                        $("#business_class_list_sec").addClass('hidden');
                        $("#no_business_class_result").html('<div class="alert alert-danger" role="alert">No data found! Please enter or select the appropriate BBS Class Code from the above list.</div>');
                        $("#is_valid_bbs_code").val(0);
                    }
                }
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

    function calculateLcForeignCurrency(arg) {
        var $tr = $(arg).closest('tr');
        var quantity = $tr.find('td:eq(2) input').val();
        var unit_price = $tr.find('td:eq(3) input').val();
        var price_bdt = quantity*unit_price;
        $tr.find('td:eq(5) input').val(price_bdt);
    }

    function calculateLocalPrice(arg) {
        var $tr = $(arg).closest('tr');
        var quantity = $tr.find('td:eq(2) input').val();
        var unit_price = $tr.find('td:eq(3) input').val();
        var price_bdt = quantity*unit_price;

        $tr.find('td:eq(4) input').val(price_bdt);
        $tr.find('td:eq(5) input').val((price_bdt/1000000).toFixed(5));

        var total_local_tala_mil = 0.00;
        $("." + 'em_local_price_taka_mil').each(function () {
            total_local_tala_mil = total_local_tala_mil + (this.value ? parseFloat(this.value) : 0.00000);
        });
        $("#" + 'em_local_total_taka_mil').val(total_local_tala_mil.toFixed(5));
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

    // function DocLoad() {

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


    function projectProfileDocument(id) {
        const projectProfileId = document.getElementById(id);
        var file = projectProfileId.files;
        if (file && file[0]) {
            if (!(file[0].type == 'application/pdf')) {
                swal({
                    type: 'error',
                    title: 'Oops...',
                    text: 'The file format is not valid! Please upload in pdf format.'
                });
                projectProfileId.value = '';
                return false;
            }

            var file_size = parseFloat((file[0].size) / (1024 * 1024)).toFixed(1); //MB Calculation
            if (!(file_size <= 2)) {
                swal({
                    type: 'error',
                    title: 'Oops...',
                    text: 'Max file size 2MB. You have uploaded ' + file_size + 'MB'
                });
                projectProfileId.value = '';
                return false;
            }
        }
    }
</script>