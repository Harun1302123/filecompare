<?php
$accessMode = ACL::getAccsessRight('IRCRecommendationNew');
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
        width: 20% !important;
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

    .ircTypeTabContent {
        width: 100%;
        margin-top: 10px;
    }

    .ircTypeTab .btn {
        margin: 5px 10px 5px 0;
    }

    .ircTypeTab .btn-info {
        color: #31708f;
        background-color: #d9edf7;
        border-color: #bce8f1;
    }

    .ircTypeTab .btn-info.active {
        color: #fff;
        background-color: #31b0d5;
        border-color: #269abc
    }

    .ircTypeTab label.radio-inline {
        margin-bottom: 0px !important;
        padding: 0;
    }

    .ircTypeTabContent {
        width: 100%;
    }

    .ircTypeTabContent .checkbox {
        margin-left: 20px;
    }

    .ircTypeTabContent .checkbox {
        font-weight: bold;
    }

    .tab-content {
        float: left;
        margin-bottom: 20px;
    }

    .tab-content .ircTypeTabContent.active {
        border: 1px solid #ccc !important;
        float: left;
        border-radius: 4px;
    }

    .table-responsive {
        overflow-x: visible;
    }

    .select2-container .select2-selection--single {
        height: 34px !important;
        padding-top: 3px;
    }

    .visaTypeTabContent {
        width: 100%;
        margin-top: 10px;
    }

    .visaTypeTab .btn {
        margin: 5px 10px 5px 0;
    }

    .visaTypeTab .btn-info {
        color: #31708f;
        background-color: #d9edf7;
        border-color: #bce8f1;
    }

    .visaTypeTab .btn-info.active {
        color: #fff;
        background-color: #31b0d5;
        border-color: #269abc
    }

    .visaTypeTab label.radio-inline {
        margin-bottom: 0px !important;
        padding: 0;
    }

    .visaTypeTabPane {
        width: 100%;
    }

    .visaTypeTabPane .checkbox {
        margin-left: 20px;
    }

    .visaTypeTabPane .checkbox {
        font-weight: bold;
    }

    @media (min-width: 992px) {
        .modal-lg {
            width: 1020px;
        }
    }
</style>

<section class="content" id="applicationForm">
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
                            <h5><strong>Application for Import Registration Certificate(IRC) recommendations 1st Adhoc</strong></h5>
                        </div>
                        <div class="pull-right"></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-body">
                        <div>
                            {!! Form::open(array('url' => 'irc-recommendation-new/add','method' => 'post','id' => 'IRCRecommendationNewForm','role'=>'form','enctype'=>'multipart/form-data')) !!}
                            <input type="hidden" name="selected_file" id="selected_file"/>
                            <input type="hidden" name="validateFieldName" id="validateFieldName"/>
                            <input type="hidden" name="isRequired" id="isRequired"/>
                            <input type="hidden" name="multipleAttachment" id="multipleAttachment"/>
                            <input type="hidden" value="{{$usdValue->bdt_value}}" id="crvalue">
                            {!! Form::hidden('reg_no', Session::get('brInfo.reg_no') ,['class' => 'form-control input-md']) !!}

                            <h3 class="stepHeader">Basic Instructions</h3>
                            <fieldset>
                                <div class="irc_type_box">
                                    <div class="tab-content ircTypeTabContent">

                                        <div class="row" id="irc_purpose_id">
                                            <div class="col-md-7 {{$errors->has('irc_purpose_id') ? 'has-error': ''}}">
                                                {!! Form::label('irc_purpose_id','Select your purpose for IRC Recommendation:', ['class'=>'col-md-7 text-left required-star']) !!}
                                                <div class="col-md-5">
                                                    {!! Form::select('irc_purpose_id', $IRCPurpose, Session::get('irc_purpose_id'),
                                                    ["placeholder" => "Select One", 'class' => 'form-control input-md required', 'id'=>'irc_purpose_id', 'onchange'=>'sectionChange(this.value)']) !!}
                                                    {!! $errors->first('irc_purpose_id','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane ircTypeTabContent fade in active">
                                            <div class="col-sm-12">
                                                <div class="irc_type_box">
                                                    <h3 class="page-header">Please read the following instructions carefully</h3>
                                                    {!! $IRCType->app_instruction !!}

                                                    <div class="form-group">
                                                        <div class="checkbox">
                                                            <label>
                                                                {!! Form::checkbox('agree_with_instruction', 1, (Session::get('agree_with_instruction') == 1) ? true : false, array('id'=>'irc_type_'.$IRCType->id, 'class'=>'required')) !!}
                                                                I have read the above information and the relevant guidance.
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{--only preview mode--}}
                                                {{-- <h4 id="selected_irc_type" style="margin-top: 0px; display: none;">IRC type: {{ $IRCType->name }}</h4> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="panel panel-info" id="last_br_div" style="display: none">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-12 {{$errors->has('last_br') ? 'has-error': ''}}">
                                                    {!! Form::label('last_br','Did you received BIDA Registration through online OSS?',['class'=>'col-md-5 text-left required-star']) !!}
                                                    <div class="col-md-7">
                                                        <label class="radio-inline">{!! Form::radio('last_br','yes', (Session::get('brInfo.last_br') == 'yes' ? true :false), ['class'=>'cusReadonly required helpTextRadio', 'id'=>'last_br_yes','onclick' => 'lastBidaRegistration(this.value)']) !!}
                                                            Yes</label>
                                                        <label class="radio-inline">{!! Form::radio('last_br', 'no', (Session::get('brInfo.last_br') == 'no' ? true :false), ['class'=>'cusReadonly required', 'id'=>'last_br_no', 'onclick' => 'lastBidaRegistration(this.value)']) !!}
                                                            No</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div id="ref_app_tracking_no_div"
                                                     class="col-md-12 hidden {{$errors->has('ref_app_tracking_no') ? 'has-error': ''}}">
                                                    {!! Form::label('ref_app_tracking_no','Please give your approved Registration Tracking ID.',['class'=>'col-md-5 text-left required-star', 'id' => 'ref_app_tracking_no_label']) !!}

                                                    <div class="col-md-7">
                                                        <div class="input-group">
                                                            {!! Form::text('ref_app_tracking_no', Session::get('brInfo.ref_app_tracking_no'), ['data-rule-maxlength'=>'100', 'class' => 'form-control cusReadonly input-sm helpText15']) !!}
                                                            {!! $errors->first('ref_app_tracking_no','<span class="help-block">:message</span>') !!}
                                                            <span class="input-group-btn">
                                                            @if(Session::get('brInfo'))
                                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                                            value="clean_load_data" name="actionBtn">Clear Loaded Data</button>
                                                                    <a href="{{ !empty(Session::get('brInfo.certificate_link')) ? Session::get('brInfo.certificate_link') : '#' }}" target="_blank" class="btn btn-success btn-sm">View Certificate</a>
                                                                @else
                                                                    <button type="submit"
                                                                            class="btn btn-success btn-sm cancel"
                                                                            value="searchBRinfo" name="actionBtn"
                                                                            id="searchBRinfo">Load Information</button>
                                                                @endif
                                                        </span>
                                                        </div>

                                                        <small class="text-danger">N.B.: Once you save or submit the
                                                            application, the BIDA Registration tracking no. cannot be
                                                            changed anymore.
                                                        </small>
                                                    </div>

                                                    {!! Form::label('ref_app_approve_date','Approved Date', ['class'=>'col-md-5 text-left required-star']) !!}
                                                    <div class="col-md-7">
                                                        <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                            {!! Form::text('ref_app_approve_date', !empty(Session::get('ref_app_approve_date')) ? date('d-M-Y', strtotime(Session::get('ref_app_approve_date'))) : '', ['class'=>'form-control input-md datepicker','readonly', 'id' => 'ref_app_approve_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="manually_approved_no_div"
                                                    class="col-md-12 hidden {{$errors->has('manually_approved_br_no') ? 'has-error': ''}} ">
                                                <div class="row form-group">
                                                    {!! Form::label('manually_approved_br_no','Please give your manually approved BIDA Registration No.',['class'=>'col-md-5 text-left required-star']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('manually_approved_br_no', '', ['data-rule-maxlength'=>'100', 'class' => 'form-control input-sm', 'onblur' => 'checkTrackingNoExists(this)']) !!}
                                                        {!! $errors->first('manually_approved_br_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    {!! Form::label('manually_approved_br_date','Approved Date', ['class'=>'col-md-5 text-left required-star']) !!}
                                                    <div class="col-md-7">
                                                        <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                            {!! Form::text('manually_approved_br_date', '', ['class'=>'form-control input-md datepicker', 'id' => 'manually_approved_br_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <fieldset class="scheduler-border" style='display: none' id="desired_office_div">
                                    <legend class="scheduler-border required-star">Please specify your desired office:
                                    </legend>
                                    <small class="text-danger">N.B.: Once you save or submit the application, the
                                        selected office cannot be changed anymore.
                                    </small>

                                    <div id="tab" class="visaTypeTab" data-toggle="buttons">
                                        @foreach($approvalCenterList as $approval_center)
                                            <a href="#tab{{$approval_center->id}}"
                                               class="showInPreview btn btn-md btn-info {{ ((Session::has('brInfo.approval_center_id') && Session::get('brInfo.approval_center_id') !=0) ? ($approval_center->id == Session::get('brInfo.approval_center_id') ? 'active' : 'disabled') : '') }}"
                                               data-toggle="tab">
                                                {!! Form::radio('approval_center_id', $approval_center->id, $approval_center->id == Session::get('brInfo.approval_center_id') ? true : false, ['class'=>'badgebox required']) !!}  {{ $approval_center->office_name }}
                                                <span class="badge">&check;</span>
                                            </a>
                                        @endforeach
                                    </div>
                                    <div class="tab-content visaTypeTabContent" style="margin-bottom: 0px">
                                        @foreach($approvalCenterList as $key => $approval_center)
                                            <div class="tab-pane visaTypeTabPane fade in {{ ((Session::has('brInfo.approval_center_id') && Session::get('brInfo.approval_center_id') !=0) ? ($approval_center->id == Session::get('brInfo.approval_center_id') ? 'active' : '') : '') }}"
                                                 id="tab{{$approval_center->id}}">
                                                <div class="col-sm-12">
                                                    <div>
                                                        <h4>You have selected <b>'{{$approval_center->office_name}}'</b>, {{ $approval_center->office_address }}
                                                            .</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </fieldset>

                            </fieldset>

                            <h3 class="stepHeader">Details Information</h3>
                            <fieldset>

                                {{--Company Information--}}
                                <div class="panel panel-info">
                                    <div class="panel-heading margin-for-preview"><strong>A. Company Information</strong></div>
                                    <div class="panel-body">
                                        <div class="readOnlyCl ">
                                            <div id="validationError"></div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6 form-group {{$errors->has('company_name') ? 'has-error': ''}}">
                                                        {!! Form::label('company_name','Name of the Organization/ Company/ Industrial Project',['class'=>'col-md-12 text-left']) !!}
                                                        <div class="col-md-12">
                                                            {!! Form::text('company_name',
                                                            (Session::get('brInfo.company_name') ? Session::get('brInfo.company_name') : CommonFunction::getCompanyNameById(Auth::user()->company_ids)),
                                                            ['class' => 'form-control input-md', 'readonly']) !!}
                                                            {!! $errors->first('company_name','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 form-group {{$errors->has('company_name_bn') ? 'has-error': ''}}">
                                                        {!! Form::label('company_name_bn','Name of the Organization/ Company/ Industrial Project (বাংলা)',['class'=>'col-md-12 text-left']) !!}
                                                        <div class="col-md-12">
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
                                                        {!! Form::label('organization_type_id','Type of the organization',['class'=>'col-md-12 text-left']) !!}
                                                        <div class="col-md-12">
                                                            {!! Form::select('organization_type_id', $eaOrganizationType, (Session::get('brInfo.organization_type_id') ? Session::get('brInfo.organization_type_id') : $getCompanyData->organization_type_id), ['class' => 'form-control  input-md ','id'=>'organization_type_id']) !!}
                                                            {!! $errors->first('organization_type_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 {{$errors->has('organization_status_id') ? 'has-error': ''}}">
                                                        {!! Form::label('organization_status_id','Status of the organization',['class'=>'col-md-12 text-left required-star']) !!}
                                                        <div class="col-md-12">
                                                            {!! Form::select('organization_status_id', $eaOrganizationStatus, (Session::get('brInfo.organization_status_id') ? Session::get('brInfo.organization_status_id') : $getCompanyData->organization_status_id), ['class' => 'form-control input-md required','id'=>'organization_status_id']) !!}
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
                                                            {!! Form::select('ownership_status_id', $eaOwnershipStatus, (Session::get('brInfo.ownership_status_id') ? Session::get('brInfo.ownership_status_id') : $getCompanyData->ownership_status_id), ['class' => 'form-control  input-md ','id'=>'ownership_status_id']) !!}
                                                            {!! $errors->first('ownership_status_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 country_of_origin_div">
                                                        {!! Form::label('country_of_origin_id','Country of origin',['class'=>'col-md-12 text-left']) !!}
                                                        <div class="col-md-12">
                                                            {!! Form::select('country_of_origin_id',$countriesWithoutBD, (Session::get('brInfo.country_of_origin_id') ? Session::get('brInfo.country_of_origin_id') : $getCompanyData->country_of_origin_id),['class'=>'form-control input-md select2', 'id' => 'country_of_origin_id', 'style' => 'width: 100%']) !!}
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
                                                            {!! Form::text('project_name', Session('brInfo.project_name'), ['class' => 'form-control required input-md ','id'=>'project_name']) !!}
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
                                                            {!! Form::text('business_class_code', Session('brInfo.class_code'), ['class' => 'form-control required input-md', 'min' => 4,'onkeyup' => 'findBusinessClassCode()']) !!}
                                                            <input type="hidden" name="is_valid_bbs_code" id="is_valid_bbs_code"/>
                                                            <span class="help-text" style="margin: 5px 0;">
                                                            <a style="cursor: pointer;" data-toggle="modal"
                                                               data-target="#businessClassModal"
                                                               onclick="openBusinessSectorModal(this)"
                                                               data-action="/irc-recommendation-new/get-business-class-modal">
                                                                Click here to select from the list
                                                            </a>
                                                        </span>
                                                            {!! $errors->first('business_class_code','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div id="no_business_class_result"></div>

                                                        <fieldset class="scheduler-border hidden"
                                                                  id="business_class_list_sec">
                                                            <legend class="scheduler-border">Other info. based on your
                                                                business class (Code = <span
                                                                        id="business_class_list_of_code"></span>)
                                                            </legend>

                                                            <table class="table table-striped table-bordered">
                                                                <thead class="alert alert-info">
                                                                <tr>
                                                                    <th>Category</th>
                                                                    <th>Code</th>
                                                                    <th>Description</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody id="business_class_list">

                                                                </tbody>
                                                            </table>
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="modal fade" id="businessClassModal" tabindex="-1"
                                                             role="dialog" aria-labelledby="myModalLabel"
                                                             aria-hidden="true">
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
                                                            {!! Form::textarea('major_activities', (Session::get('brInfo.major_activities') ? Session::get('brInfo.major_activities') : $getCompanyData->major_activities), ['class' => 'form-control input-md bigInputField maxTextCountDown', 'size' =>'5x2','data-rule-maxlength'=>'240', 'placeholder' => 'Maximum 240 characters', "data-charcount-maxlength" => "240"]) !!}
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
                                    <div class="panel-heading "><strong>B. Information of Principal
                                            Promoter/Chairman/Managing Director/CEO/Country Manager</strong></div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('ceo_country_id') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_country_id','Country ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('ceo_country_id', $countries, (Session::get('brInfo.ceo_country_id') ? Session::get('brInfo.ceo_country_id') : $getCompanyData->ceo_country_id), ['class' => 'form-control  input-md','id'=>'ceo_country_id', 'style' => 'width: 100%']) !!}
                                                        {!! $errors->first('ceo_country_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_dob') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_dob','Date of Birth',['class'=>'col-md-5']) !!}
                                                    <div class=" col-md-7">
                                                        <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                            {!! Form::text('ceo_dob',
                                                            (Session::get('brInfo.ceo_dob') ? (!empty(Session::get('brInfo.ceo_dob')) ? date('d-M-Y', strtotime(Session::get('brInfo.ceo_dob'))) : '') : (!empty($getCompanyData->ceo_dob) ? date('d-M-Y', strtotime($getCompanyData->ceo_dob)) : '')),
                                                            ['class'=>'form-control input-md datepicker', 'id' => 'ceo_dob', 'placeholder'=>'Pick from datepicker']) !!}
                                                        </div>
                                                        {!! $errors->first('ceo_dob','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div id="ceo_passport_div"
                                                     class="col-md-6 hidden {{$errors->has('ceo_passport_no') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_passport_no','Passport No.',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_passport_no', (Session::get('brInfo.ceo_passport_no') ? Session::get('brInfo.ceo_passport_no') : $getCompanyData->ceo_passport_no), ['maxlength'=>'20',
                                                        'class' => 'form-control input-md ', 'id'=>'ceo_passport_no']) !!}
                                                        {!! $errors->first('ceo_passport_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div id="ceo_nid_div"
                                                     class="col-md-6 {{$errors->has('ceo_nid') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_nid','NID No.',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_nid', (Session::get('brInfo.ceo_nid') ? Session::get('brInfo.ceo_nid') : $getCompanyData->ceo_nid), ['maxlength'=>'20',
                                                        'class' => 'form-control number input-md  bd_nid','id'=>'ceo_nid']) !!}
                                                        {!! $errors->first('ceo_nid','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_designation') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_designation','Designation', ['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_designation', (Session::get('brInfo.ceo_designation') ? Session::get('brInfo.ceo_designation') : $getCompanyData->ceo_designation),
                                                        ['maxlength'=>'80','class' => 'form-control input-md ']) !!}
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
                                                        'class' => 'form-control input-md required']) !!}
                                                        {!! $errors->first('ceo_full_name','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div id="ceo_district_div"
                                                     class="col-md-6 {{$errors->has('ceo_district_id') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_district_id','District/City/State ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('ceo_district_id',$districts, (Session::get('brInfo.ceo_district_id') ? Session::get('brInfo.ceo_district_id') : $getCompanyData->ceo_district_id), ['maxlength'=>'80','class' => 'form-control input-md', 'style' => 'width: 100%']) !!}
                                                        {!! $errors->first('ceo_district_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div id="ceo_city_div"
                                                     class="col-md-6 hidden {{$errors->has('ceo_city') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_city','District/City/State',['class'=>'text-left  col-md-5 ']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_city', (Session::get('brInfo.ceo_city') ? Session::get('brInfo.ceo_city') : $getCompanyData->ceo_city),['class' => 'form-control input-md']) !!}
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
                                                        {!! Form::text('ceo_state', (Session::get('brInfo.ceo_state') ? Session::get('brInfo.ceo_state') : $getCompanyData->ceo_state),['class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('ceo_state','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div id="ceo_thana_div"
                                                     class="col-md-6 {{$errors->has('ceo_thana_id') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_thana_id','Police Station/Town ',['class'=>'col-md-5 text-left required-star','placeholder'=>'Select district first']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('ceo_thana_id', $thana, (Session::get('brInfo.ceo_thana_id') ? Session::get('brInfo.ceo_thana_id') : $getCompanyData->ceo_thana_id), ['maxlength'=>'80','class' => 'form-control input-md','placeholder' => 'Select district first', 'style' => 'width: 100%']) !!}
                                                        {!! $errors->first('ceo_thana_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_post_code') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_post_code','Post/Zip Code ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_post_code', (Session::get('brInfo.ceo_post_code') ? Session::get('brInfo.ceo_post_code') : $getCompanyData->ceo_post_code), ['maxlength'=>'80','class' => 'form-control input-md engOnly ']) !!}
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
                                                        {!! Form::text('ceo_address', (Session::get('brInfo.ceo_address') ? Session::get('brInfo.ceo_address') : $getCompanyData->ceo_address), ['maxlength'=>'150','class' => 'bigInputField form-control input-md ']) !!}
                                                        {!! $errors->first('ceo_address','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_telephone_no') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_telephone_no','Telephone No.',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_telephone_no', (Session::get('brInfo.ceo_telephone_no') ? Session::get('brInfo.ceo_telephone_no') : $getCompanyData->ceo_telephone_no), ['maxlength'=>'20','class' => 'form-control input-md helpText15 phone_or_mobile']) !!}
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
                                                        {!! Form::text('ceo_mobile_no',  (Session::get('brInfo.ceo_mobile_no') ? Session::get('brInfo.ceo_mobile_no') : $getCompanyData->ceo_mobile_no), ['class' => 'form-control input-md helpText15 phone_or_mobile required']) !!}
                                                        {!! $errors->first('ceo_mobile_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_father_name') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_father_name','Father\'s Name',['class'=>'col-md-5 text-left', 'id' => 'ceo_father_label']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_father_name', (Session::get('brInfo.ceo_father_name') ? Session::get('brInfo.ceo_father_name') : $getCompanyData->ceo_father_name), ['class' => 'form-control textOnly input-md ']) !!}
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
                                                        {!! Form::text('ceo_email', (Session::get('brInfo.ceo_email') ? Session::get('brInfo.ceo_email') : $getCompanyData->ceo_email), ['class' => 'form-control email input-md required']) !!}
                                                        {!! $errors->first('ceo_email','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_mother_name') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_mother_name','Mother\'s Name',['class'=>'col-md-5 text-left', 'id' => 'ceo_mother_label']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_mother_name', (Session::get('brInfo.ceo_mother_name') ? Session::get('brInfo.ceo_mother_name') : $getCompanyData->ceo_mother_name), ['class' => 'form-control textOnly  input-md']) !!}
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
                                                        {!! Form::text('ceo_fax_no', (Session::get('brInfo.ceo_fax_no') ? Session::get('brInfo.ceo_fax_no') : $getCompanyData->ceo_fax_no), ['class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('ceo_fax_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_spouse_name') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_spouse_name','Spouse name',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_spouse_name', (Session::get('brInfo.ceo_spouse_name') ? Session::get('brInfo.ceo_spouse_name') : $getCompanyData->ceo_spouse_name), ['class' => 'form-control textOnly input-md']) !!}
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
                                                            {!! Form::radio('ceo_gender', 'Male', !empty($getCompanyData->ceo_gender) && $getCompanyData->ceo_gender == "Male", ['class'=>'required']) !!}
                                                            Male
                                                        </label>
                                                        <label class="radio-inline">
                                                            {!! Form::radio('ceo_gender', 'Female', !empty($getCompanyData->ceo_gender) && $getCompanyData->ceo_gender == "Female", ['class'=>'required']) !!}
                                                            Female
                                                        </label>
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
                                                    {!! Form::select('office_division_id', $divisions, (Session::get('brInfo.office_division_id') ? Session::get('brInfo.office_division_id') : $getCompanyData->office_division_id), ['class' => 'form-control imput-md required', 'id' => 'office_division_id']) !!}
                                                    {!! $errors->first('office_division_id','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('office_thana_id') ? 'has-error': ''}}">
                                                {!! Form::label('office_thana_id','Police Station', ['class'=>'col-md-5 text-left required-star']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::select('office_thana_id', $thana, (Session::get('brInfo.office_thana_id') ? Session::get('brInfo.office_thana_id') : $getCompanyData->office_thana_id), ['class' => 'form-control input-md required','placeholder' => 'Select district first', 'style' => 'width: 100%']) !!}
                                                    {!! $errors->first('office_thana_id','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 form-group {{$errors->has('office_district_id') ? 'has-error': ''}}">
                                                {!! Form::label('office_district_id','District',['class'=>'col-md-5 text-left required-star']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::select('office_district_id', $districts, (Session::get('brInfo.office_district_id') ? Session::get('brInfo.office_district_id') : $getCompanyData->office_district_id), ['class' => 'form-control input-md required','placeholder' => 'Select division first', 'style' => 'width: 100%']) !!}
                                                    {!! $errors->first('office_district_id','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('office_post_code') ? 'has-error': ''}}">
                                                {!! Form::label('office_post_code','Post Code', ['class'=>'col-md-5 text-left']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('office_post_code', (Session::get('brInfo.office_post_code') ? Session::get('brInfo.office_post_code') : $getCompanyData->office_post_code), ['class' => 'form-control input-md alphaNumeric']) !!}
                                                    {!! $errors->first('office_post_code','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 form-group {{$errors->has('office_post_office') ? 'has-error': ''}}">
                                                {!! Form::label('office_post_office','Post Office',['class'=>'col-md-5 text-left ']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('office_post_office', (Session::get('brInfo.office_post_office') ? Session::get('brInfo.office_post_office') : $getCompanyData->office_post_office), ['class' => 'form-control input-md']) !!}
                                                    {!! $errors->first('office_post_office','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('office_telephone_no') ? 'has-error': ''}}">
                                                {!! Form::label('office_telephone_no','Telephone No.',['class'=>'col-md-5 text-left']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('office_telephone_no', (Session::get('brInfo.office_telephone_no') ? Session::get('brInfo.office_telephone_no') : $getCompanyData->office_telephone_no), ['maxlength'=>'20','class' => 'form-control input-md helpText15 phone_or_mobile']) !!}
                                                    {!! $errors->first('office_telephone_no','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 form-group {{$errors->has('office_address') ? 'has-error': ''}}">
                                                {!! Form::label('office_address','Address ',['class'=>'col-md-5 text-left ']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('office_address', (Session::get('brInfo.office_address') ? Session::get('brInfo.office_address') : $getCompanyData->office_address), ['maxlength'=>'150','class' => 'form-control input-md']) !!}
                                                    {!! $errors->first('office_address','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('office_fax_no') ? 'has-error': ''}}">
                                                {!! Form::label('office_fax_no','Fax No. ',['class'=>'col-md-5 text-left']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('office_fax_no', (Session::get('brInfo.office_fax_no') ? Session::get('brInfo.office_fax_no') : $getCompanyData->office_fax_no), ['class' => 'form-control input-md']) !!}
                                                    {!! $errors->first('office_fax_no','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 form-group {{$errors->has('office_mobile_no') ? 'has-error': ''}}">
                                                {!! Form::label('office_mobile_no','Mobile No. ',['class'=>'col-md-5 text-left required-star']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('office_mobile_no', (Session::get('brInfo.office_mobile_no') ? Session::get('brInfo.office_mobile_no') : $getCompanyData->office_mobile_no), ['class' => 'form-control input-md helpText15 required']) !!}
                                                    {!! $errors->first('office_mobile_no','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('office_email') ? 'has-error': ''}}">
                                                {!! Form::label('office_email','Email ',['class'=>'col-md-5 text-left required-star']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('office_email', (Session::get('brInfo.office_email') ? Session::get('brInfo.office_email') : $getCompanyData->office_email), ['class' => 'form-control email input-md required']) !!}
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
                                                        {!! Form::select('factory_district_id', $districts, (Session::get('brInfo.factory_district_id') ? Session::get('brInfo.factory_district_id') : $getCompanyData->factory_district_id), ['class' => 'form-control input-md', 'style' => 'width: 100%']) !!}
                                                        {!! $errors->first('factory_district_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('factory_thana_id') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_thana_id','Police Station', ['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('factory_thana_id', $thana, (Session::get('brInfo.factory_thana_id') ? Session::get('brInfo.factory_thana_id') : $getCompanyData->factory_thana_id), ['class' => 'form-control input-md', 'style' => 'width: 100%']) !!}
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
                                                        {!! Form::text('factory_post_office', (Session::get('brInfo.factory_post_office') ? Session::get('brInfo.factory_post_office') : $getCompanyData->factory_post_office), ['class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('factory_post_office','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('factory_post_code') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_post_code','Post Code', ['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('factory_post_code', (Session::get('brInfo.factory_post_code') ? Session::get('brInfo.factory_post_code') : $getCompanyData->factory_post_code), ['class' => 'form-control input-md number alphaNumeric']) !!}
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
                                                        {!! Form::text('factory_address', (Session::get('brInfo.factory_address') ? Session::get('brInfo.factory_address') : $getCompanyData->factory_address), ['maxlength'=>'150','class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('factory_address','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('factory_telephone_no') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_telephone_no','Telephone No.',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('factory_telephone_no', (Session::get('brInfo.factory_telephone_no') ? Session::get('brInfo.factory_telephone_no') : $getCompanyData->factory_telephone_no), ['maxlength'=>'20','class' => 'form-control input-md helpText15 phone_or_mobile']) !!}
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
                                                        {!! Form::text('factory_mobile_no', (Session::get('brInfo.factory_mobile_no') ? Session::get('brInfo.factory_mobile_no') : $getCompanyData->factory_mobile_no), ['class' => 'form-control input-md helpText15']) !!}
                                                        {!! $errors->first('factory_mobile_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('factory_fax_no') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_fax_no','Fax No. ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('factory_fax_no', (Session::get('brInfo.factory_fax_no') ? Session::get('brInfo.factory_fax_no') : $getCompanyData->factory_fax_no), ['class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('factory_fax_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                                                            {!! Form::select('project_status_id', $projectStatusList, Session::get('brInfo.project_status_id'), ["placeholder" => "Select One", 'class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('project_status_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--2 Date of commercial operation:--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">2. Date of commercial operation</legend>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div id="date_of_arrival_div"
                                                         class="col-md-8 {{$errors->has('commercial_operation_date') ? 'has-error': ''}}">
                                                        {!! Form::label('commercial_operation_date','Date of commercial operation',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            <div class="input-group date">
                                                                {!! Form::text('commercial_operation_date', (Session::get('brInfo.commercial_operation_date') && !empty(Session::get('brInfo.commercial_operation_date')) ? date('d-M-Y', strtotime(Session::get('brInfo.commercial_operation_date'))) : ''), ['class' => 'form-control input-md datepicker date', 'placeholder'=>'dd-mm-yyyy']) !!}
                                                            </div>
                                                            {!! $errors->first('commercial_operation_date','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--3. Investment--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">3. Investment</legend>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered" cellspacing="0"
                                                       width="100%">
                                                    <thead>
                                                    <tr class="alert alert-info">
                                                        <th>Items</th>
                                                        <th></th>
                                                    </tr>
                                                    <tr>
                                                        <th>Fixed Investment</th>
                                                        <td></td>
                                                    </tr>
                                                    </thead>

                                                    <tbody id="">
                                                    <tr>
                                                        <td>
                                                            <div style="position: relative;">
                                                                <span class="helpTextCom" id="investment_land_label">&nbsp; Land <small>(Million)</small></span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td style="width:75%;">
                                                                        {!! Form::number('local_land_ivst', Session::get('brInfo.local_land_ivst'), ['data-rule-maxlength'=>'40','class' => 'form-control total_investment_item input-md number','id'=>'local_land_ivst',
                                                                         'onblur' => 'CalculateTotalInvestmentTk()'
                                                                        ]) !!}
                                                                        {!! $errors->first('local_land_ivst','<span class="help-block">:message</span>') !!}
                                                                    </td>
                                                                    <td>
                                                                        {!! Form::select("local_land_ivst_ccy", $currencyBDT, (Session::get('brInfo.local_land_ivst_ccy') ? Session::get('brInfo.local_land_ivst_ccy') : 114), ["id"=>"local_land_ivst_ccy", "class" => "form-control input-md usd-def"]) !!}
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
                                                        <td>
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td style="width:75%;">
                                                                        {!! Form::number('local_building_ivst', Session::get('brInfo.local_building_ivst'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md total_investment_item number','id'=>'local_building_ivst',
                                                                         'onblur' => 'CalculateTotalInvestmentTk()']) !!}
                                                                        {!! $errors->first('local_building_ivst','<span class="help-block">:message</span>') !!}
                                                                    </td>
                                                                    <td>
                                                                        {!! Form::select("local_building_ivst_ccy", $currencyBDT, (Session::get('brInfo.local_building_ivst_ccy') ? Session::get('brInfo.local_building_ivst_ccy') : 114), ["id"=>"local_building_ivst_ccy", "class" => "form-control input-md usd-def"]) !!}
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
                                                        <td>
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td style="width:75%;">
                                                                        {!! Form::number('local_machinery_ivst', Session::get('brInfo.local_machinery_ivst'), ['data-rule-maxlength'=>'40','class' => 'form-control required input-md number total_investment_item','id'=>'local_machinery_ivst',
                                                                        'onblur' => 'CalculateTotalInvestmentTk()']) !!}
                                                                        {!! $errors->first('local_machinery_ivst','<span class="help-block">:message</span>') !!}
                                                                    </td>
                                                                    <td>
                                                                        {!! Form::select("local_machinery_ivst_ccy", $currencyBDT, (Session::get('brInfo.local_machinery_ivst_ccy') ? Session::get('brInfo.local_machinery_ivst_ccy') : 114), ["id"=>"local_machinery_ivst_ccy", "class" => "form-control input-md usd-def"]) !!}
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
                                                        <td>
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td style="width:75%;">
                                                                        {!! Form::number('local_others_ivst', Session::get('brInfo.local_others_ivst'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md number total_investment_item','id'=>'local_others_ivst',
                                                                        'onblur' => 'CalculateTotalInvestmentTk()']) !!}
                                                                        {!! $errors->first('local_others_ivst','<span class="help-block">:message</span>') !!}
                                                                    </td>
                                                                    <td>
                                                                        {!! Form::select("local_others_ivst_ccy", $currencyBDT, (Session::get('brInfo.local_others_ivst_ccy') ? Session::get('brInfo.local_others_ivst_ccy') : 114), ["id"=>"local_others_ivst_ccy", "class" => "form-control input-md usd-def"]) !!}
                                                                        {!! $errors->first('local_others_ivst_ccy','<span class="help-block">:message</span>') !!}
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>

                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div style="position: relative;">
                                                                <span class="helpTextCom"
                                                                      id="investment_working_capital_label">&nbsp; Working Capital <small>(Three Months) (Million)</small></span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td style="width:75%;">
                                                                        {!! Form::number('local_wc_ivst', Session::get('brInfo.local_wc_ivst'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md number total_investment_item','id'=>'local_wc_ivst',
                                                                        'onblur' => 'CalculateTotalInvestmentTk()']) !!}
                                                                        {!! $errors->first('local_wc_ivst','<span class="help-block">:message</span>') !!}
                                                                    </td>
                                                                    <td>
                                                                        {!! Form::select("local_wc_ivst_ccy", $currencyBDT, (Session::get('brInfo.local_wc_ivst_ccy') ? Session::get('brInfo.local_wc_ivst_ccy') : 114), ["id"=>"local_wc_ivst_ccy", "class" => "form-control input-md usd-def"]) !!}
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
                                                        <td colspan="3">
                                                            {!! Form::number('total_fixed_ivst_million', Session::get('brInfo.total_fixed_ivst_million'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md numberNoNegative total_fixed_ivst_million required','id'=>'total_fixed_ivst_million','readonly']) !!}
                                                            {!! $errors->first('total_fixed_ivst_million','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div style="position: relative;">
                                                                <span class="helpTextCom"
                                                                      id="investment_total_invst_bd_label">&nbsp; Total Investment <small>(BDT)</small></span>
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
                                                                <span class="helpTextCom required-star"
                                                                      id="investment_total_invst_usd_label">&nbsp; Dollar exchange rate (USD)</span>
                                                            </div>
                                                        </td>
                                                        <td colspan="3">
                                                            {!! Form::number('usd_exchange_rate', Session::get('brInfo.usd_exchange_rate'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md numberNoNegative','id'=>'usd_exchange_rate']) !!}
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
                                                        <td>
                                                            <table>
                                                                <tr>
                                                                    <td width="100%">
                                                                        {!! Form::text('total_fee', Session::get('brInfo.total_fee'), ['class' => 'form-control input-md number', 'id'=>'total_fee', 'readonly']) !!}
                                                                    </td>
                                                                    <td>
                                                                        <a type="button" class="btn btn-md btn-info"
                                                                           data-toggle="modal" data-target="#myModal">Govt.
                                                                            Fees Calculator</a>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </fieldset>

                                        {{--4. Source of finance--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">4. Source of finance</legend>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="table table-striped table-bordered"
                                                               cellspacing="0" width="100%">
                                                            <tbody>
                                                            <tr id="local_equity">
                                                                <td>Local Equity (Million)</td>
                                                                <td>
                                                                    {!! Form::number('finance_src_loc_equity_1', Session::get('brInfo.finance_src_loc_equity_1'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md number','id'=>'finance_src_loc_equity_1','onblur'=>"calculateSourceOfFinance(this.id)"]) !!}
                                                                    {!! $errors->first('finance_src_loc_equity_1','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                            </tr>
                                                            <tr id="foreign_equity">
                                                                <td width="38%">Foreign Equity (Million)</td>
                                                                <td>
                                                                    {!! Form::number('finance_src_foreign_equity_1', Session::get('brInfo.finance_src_foreign_equity_1'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md number','id'=>'finance_src_foreign_equity_1','onblur'=>"calculateSourceOfFinance(this.id)"]) !!}
                                                                    {!! $errors->first('finance_src_foreign_equity_1','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Total Equity (Million)</th>
                                                                <td>
                                                                    {!! Form::number('finance_src_loc_total_equity_1', Session::get('brInfo.finance_src_loc_total_equity_1'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md number readOnly','id'=>'finance_src_loc_total_equity_1']) !!}
                                                                    {!! $errors->first('finance_src_loc_total_equity_1','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Local Loan (Million)</td>
                                                                <td>
                                                                    {!! Form::number('finance_src_loc_loan_1', Session::get('brInfo.finance_src_loc_loan_1'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md number','id'=>'finance_src_loc_loan_1','onblur'=>"calculateSourceOfFinance(this.id)"]) !!}
                                                                    {!! $errors->first('finance_src_loc_loan_1','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Foreign Loan (Million)</td>
                                                                <td>
                                                                    {!! Form::number('finance_src_foreign_loan_1', Session::get('brInfo.finance_src_foreign_loan_1'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md number ','id'=>'finance_src_foreign_loan_1','onblur'=>"calculateSourceOfFinance(this.id)"]) !!}
                                                                    {!! $errors->first('finance_src_foreign_loan_1','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Total Loan (Million)</th>
                                                                <td>
                                                                    {!! Form::number('finance_src_total_loan', Session::get('brInfo.finance_src_total_loan'), ['id'=>'finance_src_total_loan','class' => 'form-control input-md readOnly numberNoNegative', 'data-rule-maxlength'=>'240']) !!}
                                                                    {!! $errors->first('finance_src_total_loan','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Total Financing Million</th>
                                                                <td>
                                                                    {!! Form::number('finance_src_loc_total_financing_m', Session::get('brInfo.finance_src_loc_total_financing_m'), ['id'=>'finance_src_loc_total_financing_m','class' => 'form-control input-md readOnly numberNoNegative', 'data-rule-maxlength'=>'240']) !!}
                                                                    {!! $errors->first('finance_src_loc_total_financing_m','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Total Financing BDT</th>
                                                                <td colspan="3">
                                                                    {!! Form::number('finance_src_loc_total_financing_1', Session::get('brInfo.finance_src_loc_total_financing_1'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md numberNoNegative readOnly','id'=>'finance_src_loc_total_financing_1']) !!}
                                                                    {!! $errors->first('finance_src_loc_total_financing_1','<span class="help-block">:message</span>') !!}
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
                                                                    <i class="fa fa-question-circle"
                                                                       data-toggle="tooltip" data-placement="top"
                                                                       title="From the above information, the values of Local Equity (Million) and “Local Loan (Million) will go into the
                                                                       Equity Amount and Loan Amount respectively for
                                                                    Bangladesh. The summation of the Equity Amount and
                                                                    Loan Amount of other countries will be equal to
                                                                    the values of Foreign Equity (Million) and
                                                                    Foreign Loan (Million) respectively."></i>
                                                                    Country wise source of finance (Million BDT)
                                                                </th>
                                                            </tr>
                                                        </table>

                                                        <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="financeTableId">
                                                            <thead>
                                                            <tr>
                                                                <th class="required-star">Country</th>
                                                                <th class="required-star">Equity Amount
                                                                    <span class="text-danger"
                                                                          id="equity_amount_err"></span>
                                                                </th>
                                                                <th class="required-star">
                                                                    Loan Amount
                                                                    <span class="text-danger"
                                                                          id="loan_amount_err"></span>
                                                                </th>
                                                                <th>#</th>
                                                            </tr>
                                                            </thead>
                                                            @if(count(Session::get('brSourceOfFinance')) > 0)
                                                                <?php $inc = 0; ?>
                                                                @foreach(Session::get('brSourceOfFinance') as $finance)
                                                                    <tr id="financeTableIdRow{{$inc}}" data-number="1">
                                                                        <td>
                                                                            {!!Form::select("country_id[$inc]", $countries, $finance->country_id, ['class' => 'form-control required', 'style' => 'width: 100%'])!!}
                                                                            {!! $errors->first('country_id','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                        <td>
                                                                            {!! Form::text("equity_amount[$inc]", $finance->equity_amount, ['class' => 'form-control input-md equity_amount']) !!}
                                                                            {!! $errors->first('equity_amount','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                        <td>
                                                                            {!! Form::text("loan_amount[$inc]", $finance->loan_amount, ['class' => 'form-control input-md loan_amount']) !!}
                                                                            {!! $errors->first('loan_amount','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                        <td>
                                                                            <?php if ($inc == 0) { ?>
                                                                            <a class="btn btn-sm btn-primary addTableRows"
                                                                               onclick="addTableRowForIRC('financeTableId', 'financeTableIdRow0');"><i
                                                                                        class="fa fa-plus"></i></a>
                                                                            <?php } else { ?>
                                                                            <a href="javascript:void(0);"
                                                                               class="btn btn-sm btn-danger removeRow"
                                                                               onclick="removeTableRow('financeTableId','financeTableIdRow{{$inc}}');">
                                                                                <i class="fa fa-times"
                                                                                   aria-hidden="true"></i></a>
                                                                            <?php } ?>
                                                                        </td>
                                                                    </tr>
                                                                    <?php $inc++; ?>
                                                                @endforeach
                                                            @else
                                                                <tr id="financeTableIdRow0" data-number="1">
                                                                    <td>
                                                                        {!!Form::select('country_id[]', $countries, null, ['class' => 'form-control required', 'style' => 'width: 100%'])!!}
                                                                        {!! $errors->first('country_id','<span class="help-block">:message</span>') !!}
                                                                    </td>
                                                                    <td>
                                                                        {!! Form::text('equity_amount[]', '', ['class' => 'form-control input-md equity_amount']) !!}
                                                                        {!! $errors->first('equity_amount','<span class="help-block">:message</span>') !!}
                                                                    </td>
                                                                    <td>
                                                                        {!! Form::text('loan_amount[]', '', ['class' => 'form-control input-md loan_amount']) !!}
                                                                        {!! $errors->first('loan_amount','<span class="help-block">:message</span>') !!}
                                                                    </td>
                                                                    <td>
                                                                        <a class="btn btn-sm btn-primary addTableRows"
                                                                           title="Add more"
                                                                           onclick="addTableRowForIRC('financeTableId', 'financeTableIdRow0');">
                                                                            <i class="fa fa-plus"></i></a>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        </table>
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
                                                            {!! Form::text('local_male', Session::get('brInfo.local_male'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'local_male']) !!}
                                                            {!! $errors->first('local_male','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('local_female', Session::get('brInfo.local_female'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'local_female']) !!}
                                                            {!! $errors->first('local_female','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('local_total', Session::get('brInfo.local_total'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field numberNoNegative required','id'=>'local_total','readonly']) !!}
                                                            {!! $errors->first('local_total','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('foreign_male', Session::get('brInfo.foreign_male'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'foreign_male']) !!}
                                                            {!! $errors->first('foreign_male','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('foreign_female', Session::get('brInfo.foreign_female'), ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'foreign_female']) !!}
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

                                        {{--6 Sales (in 100%):--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">6. Sales (in 100%)</legend>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-4 {{$errors->has('local_sales') ? 'has-error': ''}}">
                                                        {!! Form::label('local_sales','Local ',['class'=>'col-md-4 text-left']) !!}
                                                        <div class="col-md-8">
                                                            {!! Form::number('local_sales', Session::get('brInfo.local_sales'), ['class' => 'form-control input-md number', 'id'=>'local_sales_per', 'min' => '0']) !!}
                                                            {!! $errors->first('local_sales','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 {{$errors->has('foreign_sales') ? 'has-error': ''}}">
                                                        {!! Form::label('foreign_sales','Foreign ',['class'=>'col-md-4 text-left']) !!}
                                                        <div class="col-md-8">
                                                            {!! Form::number('foreign_sales', Session::get('brInfo.foreign_sales'), ['class' => 'form-control input-md number', 'id'=>'foreign_sales_per', 'min' => '0']) !!}
                                                            {!! $errors->first('foreign_sales','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 {{$errors->has('total_sales') ? 'has-error': ''}}">
                                                        {!! Form::label('total_sales','Total in % ',['class'=>'col-md-4 text-left']) !!}
                                                        <div class="col-md-8">
                                                            {!! Form::number('total_sales', Session::get('brInfo.total_sales'), ['class' => 'form-control input-md number', 'id'=>'total_sales']) !!}
                                                            {!! $errors->first('total_sales','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--7. Annual production capacity--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">7. Annual production capacity ‍as per BIDA Registration/Amendment</legend>
                                            {{-- <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-10 {{$errors->has('annual_production_start_date') ? 'has-error': ''}}">
                                                        {!! Form::label('annual_production_start_date','Annual production start date',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            <div class="input-group date">
                                                                {!! Form::text('annual_production_start_date', '', ['class'=>'form-control input-md datepicker', 'id' => 'annual_production_start_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            </div>
                                                            {!! $errors->first('annual_production_start_date','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br> --}}
{{--                                            <fieldset class="scheduler-border" id="annual_raw">--}}
                                            <fieldset id="annual_raw">
{{--                                                <legend class="scheduler-border">Raw Materials</legend>--}}
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
                                                                            <th>#</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>

                                                                        <?php $inc = 0; ?>
                                                                        @if(count(Session::get('brAnnualProductionCapacity'))>0)
                                                                            @foreach(Session::get('brAnnualProductionCapacity') as $eachProductionCap)
                                                                                <tr id="rowProCostCount{{$inc}}" data-number="0">
                                                                                    <td>
                                                                                        {!! Form::text("apc_product_name[$inc]", $eachProductionCap->product_name, ['data-rule-maxlength'=>'255','class' => 'form-control input-md product apc_product_name','id'=>'apc_product_name']) !!}
                                                                                        {!! $errors->first('apc_product_name','<span class="help-block">:message</span>') !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::select("apc_quantity_unit[$inc]",$productUnit,$eachProductionCap->quantity_unit,['class'=>'form-control input-md apc_quantity_unit', 'id' => 'apc_quantity_unit']) !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="number"
                                                                                               id="apc_quantity"
                                                                                               name="apc_quantity[{{$inc}}]"
                                                                                               class="form-control quantity1 CalculateInputByBoxNo number apc_quantity"
                                                                                               value="{{ $eachProductionCap->quantity}}">

                                                                                        {!! $errors->first('quantity','<span class="help-block">:message</span>') !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="number"
                                                                                               id="apc_price_usd"
                                                                                               name="apc_price_usd[{{$inc}}]"
                                                                                               class="form-control quantity1 CalculateInputByBoxNo number apc_price_usd"
                                                                                               value="{{$eachProductionCap->price_usd}}">

                                                                                        {!! $errors->first('price_usd','<span class="help-block">:message</span>') !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        {!! Form::text("apc_value_taka[$inc]", $eachProductionCap->price_taka, ['class' => 'form-control input-md number apc_value_taka','id'=>"apc_value_taka"]) !!}
                                                                                        {!! $errors->first('price_taka','<span class="help-block">:message</span>') !!}
                                                                                    </td>
                                                                                    <td>
                                                                                        <?php if ($inc == 0) { ?>
                                                                                        <a class="btn btn-md btn-primary addTableRows"
                                                                                           onclick="addTableRowForIRC('productionCostTbl', 'rowProCostCount0');"><i
                                                                                                    class="fa fa-plus"></i></a>
                                                                                        <?php } else { ?>
                                                                                        <a href="javascript:void(0);"
                                                                                           class="btn btn-md btn-danger removeRow"
                                                                                           onclick="removeTableRow('productionCostTbl','rowProCostCount{{$inc}}');">
                                                                                            <i class="fa fa-times"
                                                                                               aria-hidden="true"></i></a>
                                                                                        <?php } ?>
                                                                                    </td>
                                                                                    <?php $inc++; ?>
                                                                                </tr>
                                                                            @endforeach
                                                                        @else
                                                                            <tr id="rowProCostCount0" data-number="0">
                                                                                <td>
                                                                                    {!! Form::text("apc_product_name[0]", '', ['data-rule-maxlength'=>'255','class' => 'form-control input-md product apc_product_name','id'=>'apc_product_name']) !!}
                                                                                    {!! $errors->first('apc_product_name','<span class="help-block">:message</span>') !!}
                                                                                </td>
                                                                                <td>
                                                                                    {!! Form::select("apc_quantity_unit[0]",$productUnit,'',['class'=>'form-control input-md apc_quantity_unit', 'id' => 'apc_quantity_unit']) !!}
                                                                                    {!! $errors->first('apc_quantity_unit','<span class="help-block">:message</span>') !!}
                                                                                </td>
                                                                                <td>
                                                                                    <input type="number"
                                                                                           id="apc_quantity"
                                                                                           name="apc_quantity[{{$inc}}]"
                                                                                           class="form-control quantity1 CalculateInputByBoxNo number apc_quantity">

                                                                                    {!! $errors->first('quantity','<span class="help-block">:message</span>') !!}
                                                                                </td>
                                                                                <td>
                                                                                    <input type="number"
                                                                                           id="apc_price_usd"
                                                                                           name="apc_price_usd[{{$inc}}]"
                                                                                           class="form-control quantity1 CalculateInputByBoxNo number apc_price_usd">

                                                                                    {!! $errors->first('price_usd','<span class="help-block">:message</span>') !!}
                                                                                </td>
                                                                                <td>
                                                                                    {!! Form::text("apc_value_taka[0]", '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md number apc_value_taka','id'=>'apc_value_taka']) !!}
                                                                                    {!! $errors->first('price_taka','<span class="help-block">:message</span>') !!}
                                                                                </td>
                                                                                <td>
                                                                                    <a class="btn btn-md btn-primary addTableRows"
                                                                                       onclick="addTableRowForIRC('productionCostTbl', 'rowProCostCount0');"><i
                                                                                                class="fa fa-plus"></i></a>
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                        </tbody>
                                                                    </table>
                                                                    <table>
                                                                        <tr>
                                                                            <div class="text-center fz16 text-danger">
                                                                                To add more than 5 raw materials. Please, click the "Save as Draft" button and try again.
                                                                            </div>
                                                                        </tr>
                                                                        <tr>
                                                                            <div class="col-md-6">
                                                                        <span class="help-text" style="margin: 5px 0;">
                                                                            Exchange Rate Ref: <a href="https://www.bangladesh-bank.org/econdata/exchangerate.php" target="_blank">Bangladesh Bank</a>. Please Enter Today's Exchange Rate
                                                                        </span>
                                                                            </div>
                                                                            {{--                                                                    <div class="col-md-6">--}}
                                                                            {{--                                                                        <a type="button" class="btn btn-md btn-info pull-right" data-toggle="modal" data-target="#myModal">Govt. Fees Calculator</a>--}}
                                                                            {{--                                                                    </div>--}}
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </fieldset>

                                            <fieldset class="scheduler-border" id="annual_spare" style="display: none">
                                                {{-- <legend class="scheduler-border">Spare Parts</legend> --}}
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="table-responsive">
                                                            <table id="productionSpareTbl"
                                                                   class="table table-striped table-bordered dt-responsive"
                                                                   cellspacing="0" width="100%">
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

                                                                    <th class="text-center">
                                                                        Value Taka (in million)
                                                                        <span class="required-star"></span><br/>
                                                                    </th>
                                                                    <th>#</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php $inc = 0; ?>
                                                                    @if(count(Session::get('brAnnualProductionCapacity'))>0)
                                                                        @foreach(Session::get('brAnnualProductionCapacity') as $eachProductionCap)
                                                                            <tr id="rowProSpareCostCount{{$inc}}" data-number="0">
                                                                                <td>
                                                                                    {!! Form::text("apsp_product_name[$inc]", $eachProductionCap->product_name, ['data-rule-maxlength'=>'255','class' => 'form-control input-md product apsp_product_name','id'=>'apsp_product_name']) !!}
                                                                                    {!! $errors->first('apsp_product_name','<span class="help-block">:message</span>') !!}
                                                                                </td>

                                                                                <td>
                                                                                    {!! Form::select("apsp_quantity_unit[$inc]",$productUnit,$eachProductionCap->quantity_unit,['class'=>'form-control input-md apsp_quantity_unit', 'id' => 'apsp_quantity_unit']) !!}
                                                                                </td>

                                                                                <td>
                                                                                    <input type="number" id="apsp_quantity" name="apsp_quantity[{{$inc}}]"
                                                                                            class="form-control quantity1 CalculateInputByBoxNo number apsp_quantity"
                                                                                            value="{{ $eachProductionCap->quantity}}">

                                                                                    {!! $errors->first('quantity','<span class="help-block">:message</span>') !!}
                                                                                </td>

                                                                                <td>
                                                                                    <input type="number" id="apsp_price_usd" name="apsp_price_usd[{{$inc}}]" 
                                                                                            class="form-control quantity1 CalculateInputByBoxNo number apsp_price_usd" 
                                                                                            value="{{$eachProductionCap->price_usd}}">

                                                                                    {!! $errors->first('price_usd','<span class="help-block">:message</span>') !!}
                                                                                </td>

                                                                                <td>
                                                                                    {!! Form::text("apsp_value_taka[$inc]", $eachProductionCap->price_taka, ['class' => 'form-control input-md number apsp_value_taka','id'=>"apsp_value_taka"]) !!}
                                                                                    {!! $errors->first('price_taka','<span class="help-block">:message</span>') !!}
                                                                                </td>

                                                                                <td>
                                                                                    <?php if ($inc == 0) { ?>
                                                                                    <a class="btn btn-md btn-primary addTableRows" onclick="addTableRowForIRC('productionSpareTbl', 'rowProSpareCostCount0');">
                                                                                       <i class="fa fa-plus"></i>
                                                                                    </a>
                                                                                    <?php } else { ?>
                                                                                    <a href="javascript:void(0);" class="btn btn-md btn-danger removeRow" onclick="removeTableRow1('productionSpareTbl','rowProSpareCostCount{{$inc}}');">
                                                                                        <i class="fa fa-times" aria-hidden="true"></i>
                                                                                    </a>
                                                                                    <?php } ?>
                                                                                </td>

                                                                                <?php $inc++; ?>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr id="rowProSpareCostCount0" data-number="0">
                                                                            <td>
                                                                                {!! Form::text("apsp_product_name[0]", '', ['data-rule-maxlength'=>'255','class' => 'form-control input-md product apsp_product_name','id'=>'apsp_product_name']) !!}
                                                                                {!! $errors->first('apsp_product_name','<span class="help-block">:message</span>') !!}
                                                                            </td>

                                                                            <td>
                                                                                {!! Form::select("apsp_quantity_unit[0]",$productUnit,'',['class'=>'form-control input-md apsp_quantity_unit', 'id' => 'apsp_quantity_unit']) !!}
                                                                                {!! $errors->first('apsp_quantity_unit','<span class="help-block">:message</span>') !!}
                                                                            </td>

                                                                            <td>
                                                                                <input type="number" name="apsp_quantity[0]"
                                                                                    class="form-control quantity1 CalculateInputByBoxNo number apsp_quantity" id="apsp_quantity">
                                                                                {!! $errors->first('quantity','<span class="help-block">:message</span>') !!}
                                                                            </td>

                                                                            <td>
                                                                                <input type="number" name="apsp_price_usd[0]" class="form-control quantity1 CalculateInputByBoxNo number apsp_price_usd" id="apsp_price_usd">
                                                                                {!! $errors->first('price_usd','<span class="help-block">:message</span>') !!}
                                                                            </td>

                                                                            <td>
                                                                                {!! Form::text("apsp_value_taka[0]", '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md number apsp_value_taka','id'=>'apsp_value_taka']) !!}
                                                                                {!! $errors->first('price_taka','<span class="help-block">:message</span>') !!}
                                                                            </td>
                                                                            
                                                                            <td>
                                                                                <a class="btn btn-md btn-primary addTableRows" onclick="addTableRowForIRC('productionSpareTbl', 'rowProSpareCostCount0');">
                                                                                    <i class="fa fa-plus"></i></a>
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                            <table>
                                                                <tr>
                                                                    <div class="col-md-6">
                                                                        <span class="help-text" style="margin: 5px 0;">
                                                                            Exchange Rate Ref: <a href="https://www.bangladesh-bank.org/econdata/exchangerate.php" target="_blank">Bangladesh Bank</a>. Please Enter Today's Exchange Rate
                                                                        </span>
                                                                    </div>
                                                                    {{--                                                                    <div class="col-md-6">--}}
                                                                    {{--                                                                        <a type="button" class="btn btn-md btn-info pull-right" data-toggle="modal" data-target="#myModal">Govt. Fees Calculator</a>--}}
                                                                    {{--                                                                    </div>--}}
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </fieldset>

                                        {{--8. Existing machines --}}
                                        <fieldset class="scheduler-border">
                                            {{-- <legend class="scheduler-border"><span class="required-star">8. Existing machines ‍as per BIDA Registration/Amendment</span></legend> --}}
                                            <legend class="scheduler-border">8. Existing machines ‍as per BIDA Registration/Amendment</legend>
                                            <fieldset class="scheduler-border" id="existing_spare" style="display: none">
                                                <legend class="scheduler-border"><span class="required-star">LC Information</span>
                                                </legend>
                                                <div class="table-responsive">
                                                    <table id="existingMachinesSpareTbl" class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                                                        <thead class="alert alert-info">
                                                        <tr>
                                                            <th class="text-center table-header">L/C Number
                                                                <span class="required-star"></span><br/>
                                                            </th>

                                                            <th class="text-center table-header">LC Date
                                                                <span class="required-star"></span><br/>
                                                            </th>

                                                            <th class="text-center table-header">L/C Value (In Foreign Currency)
                                                                <span class="required-star"></span><br/>
                                                            </th>

                                                            <th class="text-center table-header">Value (In BDT)
                                                                <span class="required-star"></span><br/>
                                                            </th>

                                                            <th class="text-center table-header">L/C Opening Bank & Branch Name
                                                                <span class="required-star"></span><br/>
                                                            </th>

                                                            <th class="text-center table-header">Attachment
                                                                <br/><span class="text-danger" style="font-size: 9px; font-weight: bold">[Format: *.pdf | Max File size 2MB]</span>
                                                                <span class="required-star"></span><br/>
                                                            </th>

                                                            <th class="text-center table-header">#</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>

                                                        <tr id="existingMachinesSpare0" data-number="0" class="table-tr">
                                                            <td>
                                                                {!! Form::text("em_spare_lc_no[0]", '', ['class' => 'form-control input-md product apsp_value_taka required', 'id' => 'em_spare_lc_no']) !!}
                                                            </td>

                                                            <td width="20%">
                                                                <div class="input-group date">
                                                                    {!! Form::text('em_spare_lc_date[0]', '', ['class'=>'form-control input-md em_spare_lc_date datepicker required', 'id' => 'annual_production_start_date', 'placeholder'=>'Pick from datepicker', 'id' => 'em_spare_lc_date']) !!}
                                                                </div>
                                                            </td>

                                                            <td width="10%">
                                                                {!! Form::select('em_spare_lc_value_currency[0]', $currencies, '', ['class'=>'form-control input-md em_spare_lc_value_currency required', 'style' => 'width: 100%', 'id' => 'em_spare_lc_value_currency']) !!}
                                                            </td>

                                                            <td>
                                                                {!! Form::number("em_spare_value_bdt[0]", '', ['class' => 'form-control input-md em_spare_value_bdt em_spare_value_bdt required', 'onkeyup' => "calculateListOfMachineryTotal('em_spare_value_bdt', 'total_spare_value_bdt')", 'id' => 'em_spare_value_bdt']) !!}
                                                            </td>

                                                            <td>
                                                                {!! Form::text("em_spare_lc_bank_branch[0]", '', ['class' => 'form-control input-md em_spare_lc_bank_branch required', 'id' => 'em_spare_lc_bank_branch']) !!}
                                                            </td>

                                                            <td>
                                                                <input type="file" name="em_spare_attachment[0]" class="form-control input-md em_spare_attachment ajax-file-upload-function" id="file0" onchange="uploadDocument('preview_0', this.id, 'validate_field_0', 0, 'true')">
                                                                <div class="preview-div" id="preview_0">
                                                                    <input type="hidden" id="validate_field_0" name="attachment_path[0]" class=""/>
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <a class="btn btn-md btn-primary addTableRows"
                                                                   onclick="addTableRowForIRC('existingMachinesSpareTbl', 'existingMachinesSpare0');"><i class="fa fa-plus"></i></a>
                                                            </td>
                                                        </tr>

                                                        </tbody>
                                                        <tr>
                                                            <td colspan="3"><label class="pull-right">Total</label></td>
                                                            <td>{!! Form::text("", '', ['class' => 'form-control input-md', 'id'=>'total_spare_value_bdt', 'readonly']) !!}</td>
                                                        </tr>

                                                    </table>
                                                </div>
                                            </fieldset>

                                            <fieldset class="scheduler-border" id="existing_lc">
                                                {{-- <legend class="scheduler-border"><span class="required-star">As Per L/C Open</span> --}}
                                                <legend class="scheduler-border">As Per L/C Open<span class="required-star-dynamically"></legend>
                                                <div class="table-responsive">
                                                    <table id="existingMachinesLcTbl"
                                                           class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                                                        <thead class="alert alert-info">
                                                        <tr>
                                                            <th class="text-center">Description of Machine
                                                                <span class="required-star-dynamically"></span><br/>
                                                            </th>

                                                            <th class="text-center">Unit of Quantity
                                                                <span class="required-star-dynamically"></span><br/>
                                                            </th>

                                                            <th class="text-center">Quantity (A)
                                                                <span class="required-star-dynamically"></span><br/>
                                                            </th>

                                                            <th class="text-center" colspan="2">Unit Price (B)
                                                                <span class="required-star-dynamically"></span><br/>
                                                                {{-- <span class="required-star"></span><br/> --}}
                                                            </th>

                                                            <th class="text-center">Price Foreign Currency (A X B)
                                                                <span class="required-star-dynamically"></span><br/>
                                                            </th>

                                                            <th class="text-center">Price BDT (C)
                                                                <span class="required-star-dynamically"></span><br/>
                                                            </th>

                                                            <th class="text-center">Value Taka (in million)
                                                                <span class="required-star-dynamically"></span><br/>
                                                            </th>
                                                            <th class="text-center">#</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>

                                                        <tr id="existingMachinesLc0" data-number="0">
                                                            <td>
                                                                {!! Form::text("em_product_name[0]", '', ['class' => 'form-control input-md product em_product_name', 'id'=>'em_product_name']) !!}
                                                            </td>

                                                            <td width="12%">
                                                                {!! Form::select('em_quantity_unit[0]', $productUnit, '', ['class'=>'form-control input-md em_quantity_unit', 'id'=>'em_quantity_unit']) !!}
                                                            </td>

                                                            <td width="8%">
                                                                {!! Form::number("em_quantity[0]", '', ['class' => 'form-control input-md em_quantity', 'id'=>'em_quantity', 'onkeyup' => "calculateLcForeignCurrency(this)"]) !!}
                                                            </td>

                                                            <td width="10%">
                                                                {!! Form::number("em_unit_price[0]", '', ['class' => 'form-control input-md em_unit_price', 'id'=>'em_unit_price', 'onkeyup' => "calculateLcForeignCurrency(this)"]) !!}
                                                            </td>

                                                            <td width="10%">
                                                                {!! Form::select('em_price_unit[0]', $currencies, '', ['class'=>'form-control input-md em_price_unit', 'id'=>'em_price_unit', 'style' => 'width: 100%']) !!}
                                                            </td>

                                                            <td width="15%">
                                                                {!! Form::number("em_price_foreign_currency[0]", '', ['class' => 'form-control input-md em_price_foreign_currency', 'readonly', 'id'=>'em_price_foreign_currency']) !!}
                                                            </td>

                                                            <td>
                                                                {!! Form::number("em_price_bdt[0]", '', ['class' => 'form-control input-md em_price_bdt em_price_bdt', 'id'=>'em_price_bdt', 'onkeyup' => "calculateListOfMachineryTotal('em_price_bdt', 'total_lc_price_bdt', this)"]) !!}
                                                            </td>

                                                            <td>
                                                                {!! Form::number("em_price_taka_mil[0]", '', ['class' => 'form-control input-md em_price_taka_mil em_price_taka_mil', 'id'=>'em_price_taka_mil', 'readonly']) !!}
                                                            </td>

                                                            <td>
                                                                <a class="btn btn-md btn-primary addTableRows"
                                                                   onclick="addTableRowForIRC('existingMachinesLcTbl', 'existingMachinesLc0');"><i class="fa fa-plus"></i></a>
                                                            </td>
                                                        </tr>
                                                        </tbody>

                                                        <tr>
                                                            <td colspan="6"><label class="pull-right">Total</label></td>
                                                            <td>{!! Form::text("", '', ['class' => 'form-control input-md', 'id'=>'total_lc_price_bdt', 'readonly']) !!}</td>
                                                            <td colspan="2">{!! Form::text("em_lc_total_taka_mil", '', ['class' => 'form-control input-md', 'id'=>'total_lc_taka_mil', 'readonly']) !!}</td>
                                                        </tr>
                                                    </table>
                                                    <table>
                                                        <tr>
                                                            <td>
                                                                <span class="help-text pull-right" style="margin: 5px 0;">Exchange Rate Ref: <a
                                                                            href="https://www.bangladesh-bank.org/econdata/exchangerate.php"
                                                                            target="_blank">Bangladesh Bank</a>. Please Enter Today's Exchange Rate
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </fieldset>

                                            <fieldset class="scheduler-border" id="existing_local">
                                                <legend class="scheduler-border">As per Local Procurement/ Collection</legend>
                                                <div class="table-responsive">
                                                    <table id="existingMachinesLocalTbl"
                                                           class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                                                        <thead class="alert alert-info">
                                                        <tr>
                                                            <th class="text-center">Description of Machine</th>
                                                            <th class="text-center">Unit of Quantity</th>
                                                            <th class="text-center">Quantity (A)</th>
                                                            <th class="text-center">Unit Price (B)</th>
                                                            <th class="text-center">Price BDT (A X B)</th>
                                                            <th class="text-center">Value Taka (in million)</th>
                                                            <th class="text-center ">#</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>

                                                        @if(count(Session::get('brListOfMachinesLocal'))>0)
                                                            <?php $inc = 0; ?>
                                                            @foreach(Session::get('brListOfMachinesLocal') as $existing_machines_local)

                                                                <tr id="existingMachinesLocal{{$inc}}" data-number="1">
                                                                    <td width="30%">
                                                                        {!! Form::text("em_local_product_name[0]", $existing_machines_local->l_machinery_local_name, ['class' => 'form-control input-md product em_local_product_name', 'id' => 'em_local_product_name']) !!}
                                                                    </td>

                                                                    <td width="12%">
                                                                        {!! Form::select('em_local_quantity_unit[0]', $productUnit, [], ['class'=>'form-control input-md em_local_quantity_unit', 'id' => 'em_local_quantity_unit']) !!}
                                                                    </td>

                                                                    <td width="10%">
                                                                        {!! Form::number("em_local_quantity[0]", $existing_machines_local->l_machinery_local_qty, ['class' => 'form-control input-md product em_local_quantity', 'onkeyup' => "calculateLocalPrice(this)", 'id' => 'em_local_quantity']) !!}
                                                                    </td>

                                                                    <td width="10%">
                                                                        {!! Form::number("em_local_unit_price[0]", $existing_machines_local->l_machinery_local_unit_price, ['class' => 'form-control input-md em_local_unit_price', 'onkeyup' => "calculateLocalPrice(this)", 'id' => 'em_local_unit_price']) !!}
                                                                    </td>

                                                                    <td>
                                                                        {!! Form::number("em_local_price_bdt[0]", ($existing_machines_local->l_machinery_local_qty * $existing_machines_local->l_machinery_local_unit_price), ['class' => 'form-control input-md number em_local_price_bdt', 'readonly', 'id' => 'em_local_price_bdt']) !!}
                                                                    </td>

                                                                    <td>
                                                                        {!! Form::number("em_local_price_taka_mil[0]", $existing_machines_local->l_machinery_local_total_value, ['class' => 'form-control input-md em_local_price_taka_mil em_local_price_taka_mil', 'onkeyup' => "calculateListOfMachineryTotal('em_local_price_taka_mil', 'em_local_total_taka_mil')", 'id' => 'em_local_price_taka_mil']) !!}
                                                                    </td>

                                                                    <td>
                                                                        <?php if ($inc == 0) { ?>
                                                                        <a class="btn btn-md btn-primary addTableRows"
                                                                           onclick="addTableRowForIRC('existingMachinesLocalTbl', 'existingMachinesLocal0');"><i
                                                                                    class="fa fa-plus"></i></a>
                                                                        <?php } else { ?>
                                                                        <a href="javascript:void(0);"
                                                                           class="btn btn-md btn-danger removeRow"
                                                                           onclick="removeTableRow('existingMachinesLocalTbl','existingMachinesLocal{{$inc}}');">
                                                                            <i class="fa fa-times"
                                                                               aria-hidden="true"></i></a>
                                                                        <?php } ?>
                                                                    </td>
                                                                </tr>
                                                                <?php $inc++; ?>
                                                            @endforeach
                                                        @else
                                                            <tr id="existingMachinesLocal0" data-number="0">
                                                                <td width="30%">
                                                                    {!! Form::text("em_local_product_name[0]", '', ['class' => 'form-control input-md product em_local_product_name', 'id' => 'em_local_product_name']) !!}
                                                                </td>

                                                                <td width="12%">
                                                                    {!! Form::select('em_local_quantity_unit[0]', $productUnit, '', ['class'=>'form-control input-md em_local_quantity_unit', 'id' => 'em_local_quantity_unit']) !!}
                                                                </td>

                                                                <td width="10%">
                                                                    {!! Form::number("em_local_quantity[0]", '', ['class' => 'form-control input-md em_local_quantity', 'onkeyup' => "calculateLocalPrice(this)", 'id' => 'em_local_quantity']) !!}
                                                                </td>

                                                                <td width="10%">
                                                                    {!! Form::number("em_local_unit_price[0]", '', ['class' => 'form-control input-md em_local_unit_price', 'onkeyup' => "calculateLocalPrice(this)", 'id' => 'em_local_unit_price']) !!}
                                                                </td>

                                                                <td>
                                                                    {!! Form::number("em_local_price_bdt[0]", '', ['class' => 'form-control input-md number em_local_price_bdt', 'readonly', 'id'=>'em_local_price_bdt']) !!}
                                                                </td>

                                                                <td>
                                                                    {!! Form::number("em_local_price_taka_mil[0]", '', ['class' => 'form-control input-md em_local_price_taka_mil em_local_price_taka_mil', 'onkeyup' => "calculateListOfMachineryTotal('em_local_price_taka_mil', 'em_local_total_taka_mil', this)", 'id' => 'em_local_price_taka_mil']) !!}
                                                                </td>

                                                                <td>
                                                                    <a class="btn btn-md btn-primary addTableRows"
                                                                       onclick="addTableRowForIRC('existingMachinesLocalTbl', 'existingMachinesLocal0');"><i
                                                                                class="fa fa-plus"></i></a>
                                                                </td>
                                                            </tr>
                                                        @endif

                                                        </tbody>
                                                        <tr>
                                                            <td colspan="5"><label class="pull-right">Total</label></td>
                                                            <td colspan="2">{!! Form::text("em_local_total_taka_mil", Session::get('brListOfMachinesLocalTotal'), ['class' => 'form-control input-md', 'id'=>'em_local_total_taka_mil', 'readonly']) !!}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </fieldset>

                                        </fieldset>

                                        {{--9. Public Utility Service--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border"><span class="required-star">9. Public utility service</span>
                                            </legend>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_land"
                                                                   @if(Session::get('brInfo.public_land') == 1) checked="checked"
                                                                    @endif>Land
                                                        </label>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_electricity"
                                                                   @if(Session::get('brInfo.public_electricity') == 1) checked="checked"
                                                                    @endif>Electricity
                                                        </label>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_gas"
                                                                   @if(Session::get('brInfo.public_gas') == 1) checked="checked"
                                                                    @endif>Gas
                                                        </label>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_telephone"
                                                                   @if(Session::get('brInfo.public_telephone') == 1) checked="checked"
                                                                    @endif>Telephone
                                                        </label>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_road"
                                                                   @if(Session::get('brInfo.public_road') == 1) checked="checked"
                                                                    @endif>Road
                                                        </label>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_water"
                                                                   @if(Session::get('brInfo.public_water') == 1) checked="checked"
                                                                    @endif>Water
                                                        </label>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_drainage"
                                                                   @if(Session::get('brInfo.public_drainage') == 1) checked="checked"
                                                                    @endif>Drainage
                                                        </label>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" id="public_others" name="public_others" class="other_utility myCheckBox"
                                                                   @if(Session::get('brInfo.public_others') == 1) checked="checked"
                                                                    @endif>Others
                                                        </label>
                                                    </div>
                                                    <div class="col-md-12" hidden style="margin-top: 5px;"
                                                         id="public_others_field_div">
                                                        {!! Form::text('public_others_field', Session::get('brInfo.public_others_field'), ['placeholder'=>'Specify others', 'class' => 'form-control input-md', 'id' => 'public_others_field']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--10. Trade license details--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">10. Trade license details</legend>
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('trade_licence_num') ? 'has-error': ''}}">
                                                        {!! Form::label('trade_licence_num','Trade License Number',['class'=>'text-left col-md-5 required-star']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('trade_licence_num', Session::get('brInfo.trade_licence_num'), ['class' => 'form-control input-md required']) !!}
                                                            {!! $errors->first('trade_licence_num','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group {{$errors->has('trade_licence_issuing_authority') ? 'has-error': ''}}">
                                                        {!! Form::label('trade_licence_issuing_authority','Issuing Authority',['class'=>'col-md-5 text-left required-star']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('trade_licence_issuing_authority', Session::get('brInfo.trade_licence_issuing_authority'), ['class' => 'form-control input-md required']) !!}
                                                            {!! $errors->first('trade_licence_issuing_authority','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('trade_licence_issue_date') ? 'has-error': ''}}">
                                                        {!! Form::label('trade_licence_issue_date','Issue Date',['class'=>'text-left col-md-5 required-star']) !!}
                                                        <div class="col-md-7">
                                                            <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                                {!! Form::text('trade_licence_issue_date', '', ['class'=>'form-control input-md datepicker required', 'id' => 'trade_licence_issue_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            </div>
                                                            {!! $errors->first('trade_licence_issue_date','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group {{$errors->has('trade_licence_validity_period') ? 'has-error': ''}}">
                                                        {!! Form::label('trade_licence_validity_period','Validity Period',['class'=>'col-md-5 text-left required-star']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('trade_licence_validity_period', '', ['class' => 'form-control input-md required']) !!}
                                                            {!! $errors->first('trade_licence_validity_period','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--11. Incorporation--}}
                                        <fieldset class="scheduler-border" id="incorporation">
                                            <legend class="scheduler-border">11. Incorporation</legend>
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('inc_number') ? 'has-error': ''}}">
                                                        {{-- {!! Form::label('inc_number','Incorporation Number',['class'=>'text-left col-md-5 required-star']) !!} --}}
                                                        {!! Form::label('inc_number', 'Incorporation Number', ['class' => 'text-left col-md-5 ' . (Session::get('brInfo.ownership_status_id') != 3 ? 'required-star' : '')]) !!}

                                                        <div class="col-md-7">
                                                            {{-- {!! Form::text('inc_number', '', ['class' => 'form-control input-md required']) !!} --}}
                                                            {!! Form::text('inc_number', '', ['class' => 'form-control input-md ' . (Session::get('brInfo.ownership_status_id') != 3 ? 'required' : '')]) !!}
                                                            {!! $errors->first('inc_number','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 {{$errors->has('inc_issuing_authority') ? 'has-error': ''}}">
                                                        {{-- {!! Form::label('inc_issuing_authority','Issuing Authority',['class'=>'text-left col-md-5 required-star']) !!} --}}
                                                        {!! Form::label('inc_issuing_authority', 'Issuing Authority', ['class' => 'text-left col-md-5 ' . (Session::get('brInfo.ownership_status_id') != 3 ? 'required-star' : '')]) !!}

                                                        <div class="col-md-7">
                                                            {{-- {!! Form::text('inc_issuing_authority', '', ['class' => 'form-control input-md required']) !!} --}}
                                                            {!! Form::text('inc_issuing_authority', '', ['class' => 'form-control input-md ' . (Session::get('brInfo.ownership_status_id') != 3 ? 'required' : '')]) !!}
                                                            {!! $errors->first('inc_issuing_authority','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--12. TIN--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">12. TIN</legend>
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('tin_number') ? 'has-error': ''}}">
                                                        {!! Form::label('tin_number','TIN Number',['class'=>'text-left col-md-5 required-star']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('tin_number', Session::get('brInfo.tin_number'), ['class' => 'form-control input-md required']) !!}
                                                            {!! $errors->first('tin_number','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 {{$errors->has('tin_issuing_authority') ? 'has-error': ''}}">
                                                        {!! Form::label('tin_issuing_authority','Issuing Authority',['class'=>'text-left col-md-5 required-star']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('tin_issuing_authority', Session::get('brInfo.tin_issuing_authority'), ['class' => 'form-control input-md required']) !!}
                                                            {!! $errors->first('tin_issuing_authority','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--13. Fire license info--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">13. Fire license information</legend>
                                            <div class="form-group">
                                                <div class="col-md-12 {{$errors->has('fire_license_info') ? 'has-error': ''}}">
                                                    <label class="radio-inline">{!! Form::radio('fire_license_info','already_have', true, ['class'=>'cusReadonly required helpTextRadio', 'id'=>'fire_license_info', 'onchange' => "changeFireLicenseInfo(this.value)"]) !!}
                                                        Already have License No.
                                                    </label>
                                                    <label class="radio-inline">{!! Form::radio('fire_license_info', 'applied_for', false, ['class'=>'cusReadonly required', 'id'=>'fire_license_info', 'onchange' => "changeFireLicenseInfo(this.value)"]) !!}
                                                        Applied for License No.</label>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group" id="already_have_license_div">
                                                    <div class="col-md-6 {{$errors->has('fl_number') ? 'has-error': ''}}">
                                                        {!! Form::label('fl_number','Fire License Number',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('fl_number', '', ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('fl_number','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 {{$errors->has('fl_expire_date') ? 'has-error': ''}}">
                                                        {!! Form::label('fl_expire_date','Expiry Date',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                                {!! Form::text('fl_expire_date', '', ['class'=>'form-control input-md datepicker', 'id' => 'fl_expire_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            </div>
                                                            {!! $errors->first('fl_expire_date','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group" id="applied_for_license_div" style="display: none">
                                                    <div class="col-md-6 {{$errors->has('fl_application_number') ? 'has-error': ''}}">
                                                        {!! Form::label('fl_application_number','Application Number',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('fl_application_number', '', ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('fl_application_number','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 {{$errors->has('fl_apply_date') ? 'has-error': ''}}">
                                                        {!! Form::label('fl_apply_date','Apply Date',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                                {!! Form::text('fl_apply_date', '', ['class'=>'form-control input-md datepicker', 'id' => 'fl_apply_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            </div>
                                                            {!! $errors->first('fl_apply_date','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="col-md-6 form-group {{$errors->has('fl_issuing_authority') ? 'has-error': ''}}">
                                                        {!! Form::label('fl_issuing_authority','Issuing Authority',['class'=>'col-md-5 text-left ']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('fl_issuing_authority', '', ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('fl_issuing_authority','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--14. Environment/ Site clearance certificate--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">14. Environment/ Site clearance certificate</legend>
                                            <div class="form-group">
                                                <div class="col-md-12 {{$errors->has('environment_clearance') ? 'has-error': ''}}">
                                                    <label class="radio-inline">
                                                        {!! Form::radio('environment_clearance','already_have', true, ['class'=>'cusReadonly required helpTextRadio', 'id'=>'have_license_for_environment', 'onchange' => "changeEnvironment(this.value)"]) !!}
                                                        Already have License No.
                                                    </label>
                                                    <label class="radio-inline">{!! Form::radio('environment_clearance', 'applied_for', false, ['class'=>'cusReadonly required', 'id'=>'apply_license_for_environment', 'onchange' => "changeEnvironment(this.value)"]) !!}
                                                        Applied for License No.</label>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group" id="have_license_for_environment_div">
                                                    <div class="col-md-6 {{$errors->has('el_number') ? 'has-error': ''}}">
                                                        {!! Form::label('el_number','Environment License No',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('el_number', '', ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('el_number','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 {{$errors->has('el_expire_date') ? 'has-error': ''}}">
                                                        {!! Form::label('el_expire_date','Expiry Date',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                                {!! Form::text('el_expire_date', '', ['class'=>'form-control input-md datepicker', 'id' => 'el_expire_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            </div>
                                                            {!! $errors->first('el_expire_date','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group" id="apply_license_for_environment_div" style="display: none">
                                                    <div class="col-md-6 {{$errors->has('el_application_number') ? 'has-error': ''}}">
                                                        {!! Form::label('el_application_number','Application Number',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('el_application_number', '', ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('el_application_number','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 {{$errors->has('el_apply_date') ? 'has-error': ''}}">
                                                        {!! Form::label('el_apply_date','Apply Date',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                                {!! Form::text('el_apply_date', '', ['class'=>'form-control input-md datepicker', 'id' => 'el_apply_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            </div>
                                                            {!! $errors->first('el_apply_date','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="col-md-6 form-group {{$errors->has('el_issuing_authority') ? 'has-error': ''}}">
                                                        {!! Form::label('el_issuing_authority','Issuing Authority',['class'=>'col-md-5 text-left ']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('el_issuing_authority', '', ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('el_issuing_authority','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--15. Bank information --}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">15. Bank information</legend>

                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('bank_id') ? 'has-error': ''}}">
                                                        {!! Form::label('bank_id','Bank name',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::select('bank_id', $banks, '',['class'=>'form-control input-md select2', 'id' => 'bank_id', 'onchange'=>"getBranchByBankId('bank_id', this.value, 'branch_id', '')", "style" => "width: 100%"]) !!}
                                                            {!! $errors->first('bank_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group {{$errors->has('branch_id') ? 'has-error': ''}}">
                                                        {!! Form::label('branch_id','Branch name',['class'=>'col-md-5 text-left ']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::select('branch_id', [], '', ['class' => 'form-control input-md select2','placeholder' => 'Select One', "style" => "width: 100%"]) !!}
                                                            {!! $errors->first('branch_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('bank_address') ? 'has-error': ''}}">
                                                        {!! Form::label('bank_address','Bank address',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('bank_address', '', ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('bank_address','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 {{$errors->has('bank_account_number') ? 'has-error': ''}}">
                                                        {!! Form::label('bank_account_number','Account number',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('bank_account_number', '', ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('bank_account_number','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 form-group {{$errors->has('bank_account_title') ? 'has-error': ''}}">
                                                        {!! Form::label('bank_account_title','Account title',['class'=>'col-md-5 text-left ']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('bank_account_title', '', ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('bank_account_title','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--16. Membership of Chamber/ Association information --}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">16. Membership of Chamber/ Association information</legend>
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('assoc_membership_number') ? 'has-error': ''}}">
                                                        {!! Form::label('assoc_membership_number','Membership number',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('assoc_membership_number', '', ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('assoc_membership_number','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group {{$errors->has('assoc_chamber_name') ? 'has-error': ''}}">
                                                        {!! Form::label('assoc_chamber_name','Chamber name',['class'=>'col-md-5 text-left ']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('assoc_chamber_name', '', ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('assoc_chamber_name','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('assoc_issuing_date') ? 'has-error': ''}}">
                                                        {!! Form::label('assoc_issuing_date','Issuing date',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                                {!! Form::text('assoc_issuing_date', '', ['class'=>'form-control input-md datepicker', 'id' => 'assoc_issuing_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            </div>
                                                            {!! $errors->first('assoc_issuing_date','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group {{$errors->has('assoc_expire_date') ? 'has-error': ''}}">
                                                        {!! Form::label('assoc_expire_date','Expiry date',['class'=>'col-md-5 text-left ']) !!}
                                                        <div class="col-md-7">
                                                            <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                                {!! Form::text('assoc_expire_date', '', ['class'=>'form-control input-md datepicker', 'id' => 'assoc_expire_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            </div>
                                                            {!! $errors->first('assoc_expire_date','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--17. BIN/ VAT --}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">17. BIN/ VAT</legend>
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('bin_vat_number') ? 'has-error': ''}}">
                                                        {!! Form::label('bin_vat_number','BIN/ VAT number',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('bin_vat_number', '', ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('bin_vat_number','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group {{$errors->has('bin_vat_issuing_date') ? 'has-error': ''}}">
                                                        {!! Form::label('bin_vat_issuing_date','Issuing date',['class'=>'col-md-5 text-left ']) !!}
                                                        <div class="col-md-7">
                                                            <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                                {!! Form::text('bin_vat_issuing_date', '', ['class'=>'form-control input-md datepicker', 'id' => 'bin_vat_issuing_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            </div>
                                                            {!! $errors->first('bin_vat_issuing_date','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('bin_vat_issuing_authority') ? 'has-error': ''}}">
                                                        {!! Form::label('bin_vat_issuing_authority','Issuing Authority',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('bin_vat_issuing_authority', '', ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('bin_vat_issuing_authority','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group {{$errors->has('bin_vat_expire_date') ? 'has-error': ''}}">
                                                        {!! Form::label('bin_vat_expire_date','Expiry date',['class'=>'col-md-5 text-left ']) !!}
                                                        <div class="col-md-7">
                                                            <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                                {!! Form::text('bin_vat_expire_date', '', ['class'=>'form-control input-md datepicker', 'id' => 'bin_vat_expire_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            </div>
                                                            {!! $errors->first('bin_vat_expire_date','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--18. Other Licenses --}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border"><span>18. Other Licenses/ NOC/ Permission/ Registration</span>
                                            </legend>
                                            <div class="table-responsive">
                                                <table id="otherLicenceTbl"
                                                       class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                                                    <thead class="alert alert-info">
                                                    <tr>
                                                        <th class="text-center table-header">Licence Name</th>

                                                        <th class="text-center table-header">Licence No/ Issue No</th>

                                                        <th class="text-center table-header">Issuing Authority</th>

                                                        <th class="text-center table-header">Date of Issue</th>

                                                        <th class="text-center table-header">#</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    <tr id="otherLicence0" data-number="1" class="table-tr">
                                                        <td>
                                                            {!! Form::text("other_licence_name[0]", '', ['class' => 'form-control input-md', 'id' => 'other_licence_name']) !!}
                                                        </td>

                                                        <td>
                                                            {!! Form::text("other_licence_no[0]", '', ['class' => 'form-control input-md', 'id' => 'other_licence_no']) !!}
                                                        </td>

                                                        <td>
                                                            {!! Form::text("other_licence_issuing_authority[0]", '', ['class' => 'form-control input-md', 'id' => 'other_licence_issuing_authority']) !!}
                                                        </td>

                                                        <td>
                                                            {!! Form::text('other_licence_issue_date[0]', '', ['class'=>'form-control input-md datepicker', 'id' => 'annual_production_start_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                        </td>

                                                        <td>
                                                            <a class="btn btn-md btn-primary addTableRows"
                                                               onclick="addTableRowForIRC('otherLicenceTbl', 'otherLicence0');"><i
                                                                        class="fa fa-plus"></i></a>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </fieldset>

                                    </div>
                                </div>
                            </fieldset>

                            <h3 class="stepHeader">Directors</h3>
                            <fieldset>

                                <div class="panel panel-info">
                                    <div class="panel-heading"><strong>List of directors and high authorities</strong></div>
                                    <div class="panel-body">
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">Information of (Chairman/ Managing
                                                Director/ Or Equivalent):
                                            </legend>
                                            <div class="row">
                                                <div class="form-group col-md-6 {{$errors->has('g_full_name') ? 'has-error': ''}}">
                                                    {!! Form::label('g_full_name','Full Name',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('g_full_name', Session::get('brInfo.g_full_name'), ['class' => 'form-control input-md required']) !!}
                                                        {!! $errors->first('g_full_name','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6 {{$errors->has('g_designation') ? 'has-error': ''}}">
                                                    {!! Form::label('g_designation','Position/ Designation',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('g_designation', Session::get('brInfo.g_designation'), ['class' => 'form-control input-md required']) !!}
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

                                                                <input type="hidden" id="investor_signature_base64"
                                                                       name="investor_signature_base64"/>
                                                                @if(!empty(Session::get('brInfo.g_signature')))
                                                                    <input type="hidden" id="investor_signature_hidden" name="investor_signature_hidden"
                                                                           value="{{Session::get('brInfo.g_signature')}}"/>
                                                                @endif
                                                            </div>

                                                            <div class="form-group">
                                                                <span style="font-size: 9px; font-weight: bold; display: block;">
                                                                [File Format: *.jpg/ .jpeg | Width 300PX, Height 80PX]
                                                                </span>
                                                                <br/>
                                                                <label class="btn btn-primary btn-file" {{ $errors->has('investor_signature') ? 'has-error' : '' }}>
                                                                    <i class="fa fa-picture-o" aria-hidden="true"></i> Browse
                                                                    <input class="{{(!empty(Session::get('brInfo.g_signature')) ? '' : 'required')}}"
                                                                           type="file"
                                                                           style="position: absolute; left: -9999px;"
                                                                           name="investor_signature"
                                                                           id="investor_signature"
                                                                           onchange="imageUploadWithCropping(this, 'investor_signature_preview', 'investor_signature_base64')"
                                                                           size="300x80"/>
                                                                </label>

                                                                {!! $errors->first('g_signature','<span class="help-block">:message</span>') !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--List of directors--}}
                                        <div class="panel panel-info">
                                            <div class="panel-heading">
                                                <div class="pull-left" style="padding:5px 5px"><strong>List of directors</strong></div>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="panel-body">
                                                @if(count(Session::get('brListOfDirectors')) > 0)
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                            <tr>
                                                                <th class="text-center">#</th>
                                                                <th class="text-center">Name</th>
                                                                <th class="text-center">Designation</th>
                                                                <th class="text-center">Nationality</th>
                                                                <th colspan="2" class="text-center">NID/PassportNo.</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php $i = 1; ?>
                                                            @foreach(Session::get('brListOfDirectors') as $director)
                                                                <tr>
                                                                    <td>{{ $i++ }}</td>
                                                                    <td>{{ $director->l_director_name }}</td>
                                                                    <td>{{ $director->l_director_designation }}</td>
                                                                    <td>{{ !empty($director->l_director_nationality) ? $nationality[$director->l_director_nationality] : "" }}</td>
                                                                    <td>{{ $director->nid_etin_passport }}</td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif

                                                <h4 class="text-center fz16 text-danger">
                                                    To add the directors. Please, click the "Save as Draft" button and try again.
                                                </h4>
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

    function loadUnique(element) {
        var last_tr = $(element).closest('tr').find('.em_unique').val();
        $(".modal-body #existingMachineId").val(last_tr);
    }

    let sessionApprovalCenterId = '{{ Session::get('brInfo.approval_center_id') }}';
    function lastBidaRegistration(isOnline) {
        if (isOnline == 'yes') {
            $("#ref_app_tracking_no_div").removeClass('hidden');
            $("#ref_app_tracking_no").addClass('required');

            $("#manually_approved_no_div").addClass('hidden');
            $("#manually_approved_br_no").removeClass('required');

            if (sessionApprovalCenterId != "") {
                document.getElementById('desired_office_div').style.display = 'block';
            } else {
                document.getElementById('desired_office_div').style.display = 'none';
            }

        } else if (isOnline == 'no') {
            $("#ref_app_tracking_no_div").addClass('hidden');
            $("#ref_app_tracking_no").removeClass('required');

            $("#manually_approved_no_div").removeClass('hidden');
            $("#manually_approved_br_no").addClass('required');

            document.getElementById('desired_office_div').style.display = 'block';
        } else {
            $("#ref_app_tracking_no_div").addClass('hidden');
            $("#ref_app_tracking_no").removeClass('required');

            $("#manually_approved_no_div").addClass('hidden');
            $("#manually_approved_br_no").removeClass('required');

            document.getElementById('desired_office_div').style.display = 'none';
        }
    }

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


    // Add table Row script
    function addTableRowForIRC(tableID, template_row_id) {
        // Copy the template row (first row) of table and reset the ID and Styling
        var new_row = document.getElementById(template_row_id).cloneNode(true);
        new_row.id = "";
        new_row.style.display = "";
        var current_total_row = "";

        //has new datepicker
        var hasDatepickerClass = $('#' + tableID).find('.datepicker').hasClass('datepicker');
        if (hasDatepickerClass) {
            //Get the total row, and last row number of table
            current_total_row = $('#' + tableID).find('tbody').find('.table-tr').length;
        } else {
            // Get the total row number, and last row number of table
            current_total_row = $('#' + tableID).find('tbody tr').length;
        }

        var final_total_row = current_total_row + 1;

        // check row count to click add more button start
        var row_limit = "<?php echo $add_more_validation->value?>";
        // temporary code
        if (tableID == 'listOfDirectors') {
            row_limit--;
        }
        if (final_total_row > row_limit && tableID == 'productionCostTbl') {
            swal({
                type: 'error',
                title: 'Oops...',
                text: '<?php echo $add_more_validation->details?>'
            });
            return false;
        }
        // check row count to click add more button end

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

        //New datepiker remove after cloning
        $("#" + tableID).find('#' + new_row_id).find('.datepicker-button').remove();
        $("#" + tableID).find('#' + new_row_id).find('.datepicker-calendar').remove();

        //Uploaded file remove after cloning
        $("#" + tableID).find('#' + new_row_id).find('.remove-uploaded-file').remove();
        $("#" + tableID).find('#' + new_row_id).find('.span_validate_field_0').remove();

        // Convert the add button into remove button of the new row
        $("#" + tableID).find('#' + new_row_id).find('.addTableRows').removeClass('btn-primary').addClass('btn-danger')
            .attr('onclick', 'removeTableRow("' + tableID + '","' + new_row_id + '")');
        // Icon change of the remove button of the new row
        $("#" + tableID).find('#' + new_row_id).find('.addTableRows > .fa').removeClass('fa-plus').addClass('fa-times');

        //Ajax file upload document function argument change
        $("#" + tableID).find('#' + new_row_id).find('.ajax-file-upload-function').attr('onchange', 'uploadDocument("preview_' + current_total_row + '", this.id,"validate_field_' + current_total_row + '", 0, "true")');

        // data-number attribute update of the new row
        $('#' + tableID).find('tbody tr').last().attr('data-number', last_row_number);

        // Get all select box elements from the new row, reset the selected value, and change the name of select box
        var all_select_box = $("#" + tableID).find('#' + new_row_id).find('select');
        all_select_box.val(''); //reset value
        all_select_box.prop('selectedIndex', 0);
        for (var i = 0; i < all_select_box.length; i++) {
            var name_of_select_box = all_select_box[i].name;
            var updated_name_of_select_box = name_of_select_box.replace('[0]', '[' + current_total_row + ']'); //increment all array element name
            all_select_box[i].name = updated_name_of_select_box;
        }

        // Get all input box elements from the new row, reset the value, and change the name of input box
        var all_input_box = $("#" + tableID).find('#' + new_row_id).find('input');
        all_input_box.val(''); // value reset
        for (var i = 0; i < all_input_box.length; i++) {
            var name_of_input_box = all_input_box[i].name;
            var id_of_input_box = all_input_box[i].id;
            var updated_name_of_input_box = name_of_input_box.replace('[0]', '[' + current_total_row + ']');
            var updated_id_of_input_box = id_of_input_box.replace('0', + current_total_row);
            all_input_box[i].name = updated_name_of_input_box;
            all_input_box[i].id = updated_id_of_input_box;
        }
        // Get all textarea box elements from the new row, reset the value, and change the name of textarea box
        var all_textarea_box = $("#" + tableID).find('#' + new_row_id).find('textarea');
        all_textarea_box.val(''); // value reset
        for (var i = 0; i < all_textarea_box.length; i++) {
            var name_of_textarea = all_textarea_box[i].name;
            var updated_name_of_textarea = name_of_textarea.replace('[0]', '[' + current_total_row + ']');
            all_textarea_box[i].name = updated_name_of_textarea;
            $('#' + new_row_id).find('.readonlyClass').prop('readonly', true);
        }

        //Attachment preview div id replace here
        var findPreviewDivClass = $("#" + tableID).find('#' + new_row_id).find('.preview-div');
        if (findPreviewDivClass.hasClass('preview-div')) {
            var updated_preview_div_id = findPreviewDivClass[0].id.replace('0', + current_total_row);
            $("#" + tableID).find('#' + new_row_id).find('.preview-div')[0].id = updated_preview_div_id;
        }

        // Table footer adding with add more button
        var table_header_columns = "";
        if (final_total_row > 3) {
            const check_tfoot_element = $('#' + tableID + ' tfoot').length;
            if (check_tfoot_element === 0) {
                if (hasDatepickerClass) {
                    table_header_columns = $('#' + tableID).find('thead tr').find('.table-header');
                } else {
                    table_header_columns = $('#' + tableID).find('thead th');
                }
                let table_footer = document.getElementById(tableID).createTFoot();
                table_footer.setAttribute('id', 'autoFooter')
                let table_footer_row = table_footer.insertRow(0);
                for (i = 0; i < table_header_columns.length; i++) {
                    const table_footer_th = table_footer_row.insertCell(i);
                    // if this is the last column, then push add more button
                    if (i === (table_header_columns.length - 1)) {
                        table_footer_th.innerHTML = '<a class="btn btn-sm btn-primary addTableRows" title="Add more" onclick="addTableRowForIRC(\'' + tableID + '\', \'' + template_row_id + '\')"><i class="fa fa-plus"></i></a>';
                    } else {
                        table_footer_th.innerHTML = '<b>' + table_header_columns[i].innerHTML + '</b>';
                    }
                }
            }

            // add colspan attr for As Per L/C Open and section 8
            if (tableID === 'existingMachinesLcTbl') {
                $("#existingMachinesLcTbl").find('tfoot').find('td:eq(3)').attr('colspan', 2);
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

        $("#" + tableID).find('.datepicker').datepicker({
            outputFormat: 'dd-MMM-y',
            // daysOfWeekDisabled: [5,6],
            theme : 'blue',
        });

    } // end of addTableRowForIRC() function


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

        calculateListOfMachineryTotal('machinery_imported_total_value', 'machinery_imported_total_amount');
        calculateListOfMachineryTotal('machinery_local_total_value', 'machinery_local_total_amount');

        calculateListOfMachineryTotal('em_spare_value_bdt', 'total_spare_value_bdt');
        calculateListOfMachineryTotal('em_price_bdt', 'total_lc_price_bdt');
        calculateListOfMachineryTotal('em_price_taka_mil', 'total_lc_taka_mil');
        calculateListOfMachineryTotal('em_local_price_taka_mil', 'em_local_total_taka_mil');
    }

    $(document).ready(function () {
        var form = $("#IRCRecommendationNewForm").show();
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
                // return true;
                if (newIndex == 1) {
                    // var ircTypeIsSelect = $('input[name=app_type_id]:checked').length;
                    // if (ircTypeIsSelect != 1) {
                    //     alert('Sorry! You must select any one of the IRC types.');
                    //     return false;
                    // }

                    var desired_office = $('input[name=approval_center_id]:checked').length;
                    if (desired_office != 1) {
                        alert('Sorry! Please specify your desired office.');
                        return false;
                    }

                    //Previous data loaded validation check
                    var last_br = $("input[name='last_br']:checked").val();
                    if (last_br == 'yes') {
                        if (sessionLastBR == 'yes') {
                            return true;
                        }
                        alert('Please, load Bida Registration data.');
                        return false;
                    }
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

                    // Existing machines ‍as per BIDA Registration/Amendment
                    // var total_lc_price_bdt = $("#total_lc_price_bdt").val();
                    // var em_local_total_taka_mil = $("#em_local_total_taka_mil").val();

                    // if(total_lc_price_bdt == 00 && em_local_total_taka_mil == 00) {
                    //     alert('8. Existing machines as per BIDA Registration/Amendment is required. Please enter the data.');
                    //     return false;
                    // }
                }

                if (newIndex == 3) {

                    // List of directors
                    let session_br_list_of_director = '{{ count(Session::get('brListOfDirectors')) }}';
                    if (session_br_list_of_director == 0) {
                        alert('To add the directors. Please, click the "Save as Draft" button and try again.');
                        return false;
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

                if (currentIndex == 4) {
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
                popupWindow = window.open('<?php echo URL::to('/irc-recommendation-new/preview'); ?>', 'Sample', '');
            } else {
                return false;
            }
        });

        var class_code = '{{ Session('brInfo.class_code') }}';
        var sub_class_id = '{{ Session('brInfo.sub_class_id') }}';
        findBusinessClassCode(class_code, sub_class_id);

        {{--        TypeWiseDocLoad('{{$IRCType->id}}', '{{$IRCType->attachment_key}}');--}}
    });
    // New end
    //--------Step Form init+validation End----------//
    var popupWindow = null;
    $('.finish').on('click', function (e) {
        if (form.valid()) {
            $('body').css({"display": "none"});
            popupWindow = window.open('<?php echo URL::to('/irc-recommendation-new/preview'); ?>', 'Sample', '');
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


        $('#IRCRecommendationNewForm').validate({
            rules: {
                ".myCheckBox": {required: true, maxlength: 1}
            }
        });

        if($('input[name=agree_with_instruction]').prop('checked') == true){
            document.getElementById('last_br_div').style.display = 'block';
        }

        $('input[name=agree_with_instruction]').change(function (e) {
            if (this.checked) {
                document.getElementById('last_br_div').style.display = 'block';
            } else {
                document.getElementById('last_br_div').style.display = 'none';
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

        $('#ownership_status_id').change(function (e) {
            var ownership_status_id = this.value;
            $("#incorporation").toggleClass('hidden', ownership_status_id == '3');
        });
        
        // Trigger the change event initially
        $('#ownership_status_id').trigger('change');


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

        sectionChange('{{Session::get('irc_purpose_id')}}');
    });

    function sectionChange(selectedvalue) {
        if (selectedvalue == 1) { // 1 = Raw material
            document.getElementById('annual_raw').style.display = 'block';
            document.getElementById('annual_spare').style.display = 'none';
            document.getElementById('existing_spare').style.display = 'none';

            $(".apc_quantity").addClass("required");
            $(".apsp_quantity").removeClass("required");

            //annual_raw
            classAddRemove(['apc_product_name', 'apc_quantity_unit', 'apc_quantity', 'apc_price_usd', 'apc_value_taka'], 'block');

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

            $(".apc_quantity").removeClass("required");
            $(".apsp_quantity").addClass("required");

            //annual_raw
            classAddRemove(['apc_product_name', 'apc_quantity_unit', 'apc_quantity', 'apc_price_usd', 'apc_value_taka'], 'none');

            // 7. annual_spare
            classAddRemove(['apsp_product_name', 'apsp_quantity_unit', 'apsp_quantity', 'apsp_price_usd', 'apsp_value_taka'], 'none');

            // 7. existing_spare
            classAddRemove(['em_spare_lc_no', 'em_spare_lc_date', 'em_spare_lc_value_currency', 'em_spare_value_bdt', 'em_spare_lc_bank_branch'], 'block');

            // As Per L/C Open
            classAddRemove(['em_product_name', 'em_quantity_unit', 'em_quantity', 'em_unit_price', 'em_price_unit', 'em_price_bdt'], 'block');
            $('.required-star-dynamically').addClass("required-star");

        } else if(selectedvalue == 3) { // 3 = Both
            document.getElementById('annual_raw').style.display = 'block';
            document.getElementById('annual_spare').style.display = 'none';
            document.getElementById('existing_spare').style.display = 'block';

            //annual_raw
            classAddRemove(['apc_product_name', 'apc_quantity_unit', 'apc_quantity', 'apc_price_usd', 'apc_value_taka'], 'block');

            // 7. annual_spare
            classAddRemove(['apsp_product_name', 'apsp_quantity_unit', 'apsp_quantity', 'apsp_price_usd', 'apsp_value_taka'], 'block');

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
            var action = "{{url('/irc-recommendation-new/upload-document')}}";

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
        } else {
            $(".country_of_origin_div").show('slow');
            $("#country_of_origin_id").addClass('required');

        }
        ownershipAndOrganizationStatusWiseDocLoad(organizationStatusId);
        // sourceOfFinanceEquityAmountToggle(organizationStatusId);
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
<script src="{{ asset("assets/scripts/check-tracking-no-exists.js") }}"></script>

<script>
    $(function () {
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
    });

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

                        var sub_class_id = '{{(Session::get('brInfo.sub_class_id') === 0) ? '-1' : Session::get('brInfo.sub_class_id')}}';

                        var option = '<option value="">Select One</option>';
                        $.each(response.subClass, function (id, value) {
                            if (id == sub_class_id) {
                                option += '<option value="' + id + '" selected> ' + value + '</option>';
                            } else {
                                option += '<option value="' + id + '">' + value + '</option>';
                            }
                        });

                        table_row += '<tr><td width="10%" class="required-star">Sub class</td><td colspan="2"><select onchange="otherSubClassCodeName(this.value)" name="sub_class_id" class="form-control required">' + option + '</select></td></tr>';

                        table_row += '<tr id="other_sub_class_code_parent" class="hidden"><td width="20%" class="">Other sub class code</td><td colspan="2"><input type="text" name="other_sub_class_code" id="other_sub_class_code" class="form-control" value="'+ other_code +'"></td></tr>';
                        table_row += '<tr id="other_sub_class_name_parent" class="hidden"><td width="20%" class="required-star">Other sub class name</td><td colspan="2"><input type="text" name="other_sub_class_name" id="other_sub_class_name" class="form-control" value="'+ other_name +'"></td></tr>';

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

    function ownershipAndOrganizationStatusWiseDocLoad (organizationId) {
        const ownershipId = document.getElementById('ownership_status_id').value;
        let _token = $('input[name="_token"]').val();
        let app_id = $("#app_id").val();
        let attachment_key = generateAttachmentKey(organizationId, ownershipId);

        if (ownershipId != undefined && organizationId != undefined) {
            $.ajax({
                type: "POST",
                url: '/irc-recommendation-new/getDocList',
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
                organization_key = "join";
                break;
            case 2:
                organization_key = "fore";
                break;
            case 3:
                organization_key = "loca";
                break;
            default:
        }

        switch (parseInt(ownershipId)) {
            case 1:
                ownership_key = "comp";
                break;
            case 2:
                ownership_key = "part";
                break;
            case 3:
                ownership_key = "prop";
                break;
            default:
        }

        return "irc_1st_" + ownership_key + "_" + organization_key;
    }


    // function sourceOfFinanceEquityAmountToggle(organizationId)
    // {
    //     const local_equity = $('#local_equity');
    //     const foreign_equity = $('#foreign_equity');
    //
    //     if (parseInt(organizationId) === 1) { // Joint Venture
    //         local_equity.removeClass('hidden');
    //         foreign_equity.removeClass('hidden');
    //     } else if (parseInt(organizationId) === 2) { //Foreign
    //         local_equity.addClass('hidden');
    //         foreign_equity.removeClass('hidden');
    //     }else if (parseInt(organizationId) === 3) { //Local
    //         local_equity.removeClass('hidden');
    //         foreign_equity.addClass('hidden');
    //     } else {
    //         local_equity.addClass('hidden');
    //         foreign_equity.addClass('hidden');
    //     }
    // }
</script>