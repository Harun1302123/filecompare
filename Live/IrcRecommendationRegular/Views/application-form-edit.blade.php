<?php
$accessMode = ACL::getAccsessRight('IRCRecommendationRegular');
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
    .v-align-middle{
        vertical-align: middle !important;
    }
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
        width: 100px;
        height: 100px;
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

    .ircTypeTabPane {
        width: 100%;
    }

    .ircTypeTabPane .checkbox {
        margin-left: 20px;
    }

    .ircTypeTabPane .checkbox {
        font-weight: bold;
    }

    .tab-content {
        float: left;
        margin-bottom: 20px;
    }

    .tab-content .ircTypeTabPane.active {
        border: 1px solid #ccc !important;
        float: left;
        border-radius: 4px;
    }

    .table-responsive {
        overflow-x: visible;
    }

    .select2-container {
        display: block !important;
    }

    .select2-container .select2-selection--single {
        height: 34px !important;
        padding-top: 3px;
    }

    .page-header {
        display: block !important;
    }

    @media (min-width: 992px) {
        .modal-xl {
            width: 1020px;
        }
    }
    .blink_me {
        animation: blinker 5s linear infinite;
    }

    @keyframes blinker {
        50% { opacity: .5; }
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
                            <h5>
                                <strong>Application for Import Registration Certificate(IRC) recommendations Regular</strong>
                            </h5>
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
                            {!! Form::open(array('url' => 'irc-recommendation-regular/add','method' => 'post','id' => 'IrcRecommendationRegularForm','role'=>'form','enctype'=>'multipart/form-data')) !!}

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

                            <h3 class="stepHeader">Basic Instructions</h3>
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
                                <div class="irc_type_box">
                                    <div class="tab-content ircTypeTabContent">
                                        <div class="row" id="irc_purpose_id">
                                            <div class="col-md-6 {{$errors->has('irc_purpose_id') ? 'has-error': ''}}">
                                                {!! Form::label('irc_purpose_id','Select your purpose for IRC Recommendation:', ['class'=>'col-md-6 text-left required-star']) !!}
                                                <div class="col-md-6">
                                                    {!! Form::select('irc_purpose_id', $IRCPurpose, $appInfo->irc_purpose_id, ["placeholder" => "Select One", 'class' => 'form-control input-md required', 'id'=>'irc_purpose', 'onchange'=>'sectionChange(this.value)']) !!}
                                                    {!! $errors->first('irc_purpose_id','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 {{$errors->has('irc_regular_purpose_id') ? 'has-error': ''}}">
                                                {!! Form::label('irc_regular_purpose_id','Select your purpose for Regular IRC Recommendation:', ['class'=>'col-md-6 text-left required-star']) !!}
                                                <div class="col-md-6">
                                                    {!! Form::select('irc_regular_purpose_id', $IRCRegularPurpose, $appInfo->irc_regular_purpose_id,
                                                    ["placeholder" => "Select One", 'class' => 'form-control input-md required', 'id'=>'irc_regular_purpose_id', 'onchange'=>'regularSectionChange(this.value)']) !!}
                                                    {!! $errors->first('irc_regular_purpose_id','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                        <br/>

                                        <div class="tab-pane ircTypeTabPane fade in active" id="tab{{$IRCType->id}}">
                                            <div class="col-sm-12">
                                                <div class="irc_type_box">
                                                    <h3 class="page-header">Please read the following instructions carefully</h3>
                                                    {!! $IRCType->app_instruction !!}

                                                    <div class="form-group">
                                                        <div class="checkbox">
                                                            <label>
                                                                {!! Form::checkbox('agree_with_instruction', 1, ($appInfo->agree_with_instruction == 1) ? true : false, array('id'=>'irc_type_'.$IRCType->id, 'class'=>'required')) !!}
                                                                I have read the above information and the relevant guidance.
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{--only preview mode--}}
                                                <h4 id="selected_irc_type" style="margin-top: 0px; display: none;">IRC type: {{ $IRCType->name }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>

                                <div id="last_app_data_div" style="display: none;">

                                    <div class="panel panel-info">
                                        <div class="panel-heading">
                                            <strong>Basic Information</strong>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="{{$errors->has('last_br_check') ? 'has-error': ''}}">
                                                        {!! Form::label('last_br_check','Did you received BIDA Registration through online OSS?',['class'=>'col-md-6 text-left required-star']) !!}
                                                        <div class="col-md-6">

                                                            @if($appInfo->last_br == 'yes')
                                                            <label class="radio-inline">{!! Form::radio('last_br_check','yes', ($appInfo->last_br == 'yes' ? true :false), ['class'=>'cusReadonly required', 'id'=>'last_br_yes']) !!}
                                                                Yes
                                                            </label>
                                                            @endif
                                                            @if($appInfo->last_br == 'no')
                                                            <label class="radio-inline">{!! Form::radio('last_br_check', 'no', ($appInfo->last_br == 'no' ? true :false), ['class'=>'cusReadonly required', 'id'=>'last_br_no']) !!}
                                                                No
                                                            </label>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                @if($appInfo->last_br == 'yes')
                                                <div class="row" id="br_ref_app_tracking_no_div">
                                                    <div class="{{$errors->has('br_ref_app_tracking_no') ? 'has-error': ''}}">
                                                        {!! Form::label('br_ref_app_tracking_no','Please give your approved Registration Tracking ID.',['class'=>'col-md-6 text-left required-star', 'id' => 'br_ref_app_tracking_no_label']) !!}

                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                {!! Form::hidden('br_ref_app_tracking_no', $appInfo->br_ref_app_tracking_no, ['class' => 'form-control input-sm required']) !!}
                                                                <span class="label label-success" style="font-size: 15px">{{ (empty($appInfo->ref_app_tracking_no) ? '' : $appInfo->ref_app_tracking_no) }}</span>

                                                                <a href="{{ CommonFunction::getUrlByTrackingNo($appInfo->br_ref_app_tracking_no) }}" target="_blank">
                                                                    <span class="label label-success"
                                                                            style="font-size: 15px">{{ (empty($appInfo->br_ref_app_tracking_no) ? 'N/A' : $appInfo->br_ref_app_tracking_no) }}</span></a><br/>
                                                                <small class="text-danger">N.B.: Once you save or submit the application, the BIDA Registration tracking no cannot be changed anymore.</small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {!! Form::label('br_ref_app_approve_date','Approved Date', ['class'=>'col-md-6 text-left required-star']) !!}
                                                    <div class="col-md-6">
                                                        <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                            {!! Form::text('br_ref_app_approve_date', (empty($appInfo->br_ref_app_approve_date)) ? '' : date('d-M-Y', strtotime($appInfo->br_ref_app_approve_date)), ['class'=>'form-control input-md datepicker', 'id' => 'br_ref_app_approve_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                @if($appInfo->last_br == 'no')
                                                <div id="br_manually_approved_div"
                                                     class="{{$errors->has('br_manually_approved_no') ? 'has-error': ''}} ">
                                                    <div class="row form-group">
                                                        {!! Form::label('br_manually_approved_no','Please give your manually approved BIDA Registration No.',['class'=>'col-md-6 text-left required-star']) !!}
                                                        <div class="col-md-6">
                                                            {!! Form::text('br_manually_approved_no', $appInfo->br_manually_approved_no, ['data-rule-maxlength'=>'100', 'class' => 'form-control input-sm required']) !!}
                                                            {!! $errors->first('br_manually_approved_no','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        {!! Form::label('br_manually_approved_date','Approved Date', ['class'=>'col-md-6 text-left required-star']) !!}
                                                        <div class="col-md-6">
                                                            <div class="input-group date"
                                                                 data-date-format="dd-mm-yyyy">
                                                                {!! Form::text('br_manually_approved_date', (empty($appInfo->br_manually_approved_date)) ? '' : date('d-M-Y', strtotime($appInfo->br_manually_approved_date)), ['class'=>'form-control input-md datepicker', 'id' => 'br_manually_approved_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="panel panel-info">
                                        <div class="panel-heading">
                                            <strong>IRC Information</strong>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="{{$errors->has('last_irc_check') ? 'has-error': ''}}">
                                                        {!! Form::label('last_irc_check','Did you receive BIDA IRC recommendation through OSS?',['class'=>'col-md-6 text-left required-star']) !!}
                                                        <div class="col-md-6">
                                                            <label class="radio-inline">{!! Form::radio('last_irc_check','yes', ($appInfo->last_irc_2nd_adhoc == 'yes' ? true :false), ['class'=>'cusReadonly required', 'id'=>'last_irc_yes', 'onclick' => 'lastIrc(this.value)']) !!}
                                                                Yes</label>
                                                            <label class="radio-inline">{!! Form::radio('last_irc_check', 'no', ($appInfo->last_irc_2nd_adhoc == 'no' ? true :false), ['class'=>'cusReadonly required', 'id'=>'last_irc_no', 'onclick' => 'lastIrc(this.value)']) !!}
                                                                No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row hidden" id="irc_ref_app_tracking_no_div">
                                                    <div class="{{$errors->has('irc_ref_app_tracking_no') ? 'has-error': ''}}">
                                                        {!! Form::label('irc_ref_app_tracking_no','Please give your latest IRC recommendation Tracking ID 1st/2nd/3rd.',['class'=>'col-md-6 text-left required-star', 'id' => 'irc_ref_app_tracking_no_label']) !!}

                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                {!! Form::hidden('irc_ref_app_tracking_no', $appInfo->irc_ref_app_tracking_no, ['class' => 'form-control input-sm']) !!}

                                                                <a href="{{ CommonFunction::getUrlByTrackingNo($appInfo->irc_ref_app_tracking_no) }}" target="_blank">
                                                                    <span class="label label-success"
                                                                            style="font-size: 15px">{{ (empty($appInfo->irc_ref_app_tracking_no) ? 'N/A' : $appInfo->irc_ref_app_tracking_no) }}</span></a><br/>

                                                                <small class="text-danger">
                                                                    N.B.: Once you save or submit the application, the IRC 2nd adhoc tracking no cannot be changed anymore.
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {!! Form::label('irc_ref_app_approve_date','Approved Date', ['class'=>'col-md-6 text-left required-star']) !!}
                                                    <div class="col-md-6">
                                                        <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                            {!! Form::text('irc_ref_app_approve_date', (empty($appInfo->irc_ref_app_approve_date)) ? '' : date('d-M-Y', strtotime($appInfo->irc_ref_app_approve_date)), ['class'=>'form-control input-md datepicker', 'id' => 'irc_ref_app_approve_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="irc_manually_approved_div" class="hidden {{$errors->has('irc_manually_approved_no') ? 'has-error': ''}} ">
                                                    <div class="row form-group">
                                                        {!! Form::label('irc_manually_approved_no','Please give your memo number of latest IRC Recommendation 1st/2nd/3rd',['class'=>'col-md-6 text-left required-star']) !!}
                                                        <div class="col-md-6">
                                                            {!! Form::text('irc_manually_approved_no', $appInfo->irc_manually_approved_no, ['data-rule-maxlength'=>'100', 'class' => 'form-control input-sm']) !!}
                                                            {!! $errors->first('irc_manually_approved_no','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        {!! Form::label('irc_manually_approved_date','Approved Date', ['class'=>'col-md-6 text-left required-star']) !!}
                                                        <div class="col-md-6">
                                                            <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                                {!! Form::text('irc_manually_approved_date', (empty($appInfo->irc_manually_approved_date)) ? '' : date('d-M-Y', strtotime($appInfo->irc_manually_approved_date)), ['class'=>'form-control input-md datepicker', 'id' => 'irc_manually_approved_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="panel panel-info">
                                        <div class="panel-heading">
                                            <strong>IRC Information (CCI&E)</strong>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">

                                                <div class="row" id="irc_ccie_no_div">
                                                    <div class="col-md-6 {{$errors->has('irc_ccie_no') ? 'has-error': ''}}">
                                                        {!! Form::label('irc_ccie_no','Please give your last IRC Number  (CCI&E)',['class'=>'text-left col-md-5 required-star']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('irc_ccie_no', $appInfo->irc_ccie_no, ['class' => 'form-control input-md required']) !!}
                                                            {!! $errors->first('irc_ccie_no','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>


                                                    <div class="col-md-6 {{$errors->has('irc_ccie_brows_copy') ? 'has-error': ''}}">
                                                        {!! Form::label('irc_ccie_brows_copy','Brows copy IRC (CCI&E)',['class'=>'text-left col-md-5 required-star']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::file('irc_ccie_brows_copy', [
                                                                'class' => 'form-control input-md'.(!empty($appInfo->irc_ccie_brows_copy) ? '' : ' required'),
                                                                'id' => 'irc_ccie_brows_copy_id',
                                                                'multiple' => true,
                                                                'accept' => 'application/pdf',
                                                                'onchange' => 'uploadDocumentProcess(this.id)'
                                                            ]) !!}
                                                            @if(!empty($appInfo->irc_ccie_brows_copy))
                                                                <a target="_blank" class="documentUrl btn btn-xs btn-primary"
                                                                   href="{{URL::to('/uploads/'.(!empty($appInfo->irc_ccie_brows_copy) ? $appInfo->irc_ccie_brows_copy : ''))}}">
                                                                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                                                    Open File
                                                                </a>
                                                            @endif
                                                            {!! $errors->first('irc_ccie_brows_copy','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-6 {{$errors->has('irc_ccie_brows_copy') ? 'has-error': ''}}">
                                                        {!! Form::label('irc_ccie_approve_date','Approved Date', ['class'=>'col-md-5 text-left required-star']) !!}
                                                        <div class="col-md-7">
                                                            <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                                {!! Form::text('irc_ccie_approve_date', (!empty($appInfo->irc_ccie_approve_date) ? date('d-M-Y', strtotime($appInfo->irc_ccie_approve_date)) : '' ), ['class'=>'form-control input-md datepicker required', 'id' => 'irc_ccie_approve_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border required-star">Please specify your desired office: </legend>
                                    <small class="text-danger">
                                        N.B.: Once you save or submit the application, the selectd office cannot be changed anymore.
                                    </small>

                                    <div id="tab" class="visaTypeTab" data-toggle="buttons">
                                        @foreach($approvalCenterList as $approval_center)
                                            @if($appInfo->approval_center_id == $approval_center->id)
                                                <a href="#tab{{$approval_center->id}}"
                                                   class="showInPreview btn btn-md btn-info disabled active"
                                                   data-toggle="tab">
                                                    {!! Form::radio('approval_center_id', $approval_center->id, true, ['class'=>'badgebox required']) !!}  {{ $approval_center->office_name }}
                                                    <span class="badge">&check;</span>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="tab-content visaTypeTabContent" style="margin-bottom: 0px">
                                        @foreach($approvalCenterList as $key => $approval_center)
                                            @if($appInfo->approval_center_id == $approval_center->id)
                                                <div class="tab-pane visaTypeTabPane fade in active"
                                                     id="tab{{$approval_center->id}}">
                                                    <div class="col-sm-12">
                                                        <div>
                                                            <h4>You have selected <b>'{{$approval_center->office_name}}'</b>, {{ $approval_center->office_address }}</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </fieldset>

                            </fieldset>

                            <h3 class="stepHeader">Details Information</h3>
                            <fieldset>

                                {{--Company Information--}}
                                <div class="panel panel-info">
                                    <div class="panel-heading margin-for-preview"><strong>Company Information</strong></div>
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
                                                            {!! Form::select('organization_type_id', $eaOrganizationType, $appInfo->organization_type_id, ['class' => 'form-control  input-md ','id'=>'organization_type_id']) !!}
                                                            {!! $errors->first('organization_type_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 {{$errors->has('organization_status_id') ? 'has-error': ''}}">
                                                        {!! Form::label('organization_status_id','Status of the organization',['class'=>'col-md-12 text-left required-star']) !!}
                                                        <div class="col-md-12">
                                                            {!! Form::select('organization_status_id', $eaOrganizationStatus, $appInfo->organization_status_id, ['class' => 'form-control input-md required','id'=>'organization_status_id']) !!}
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
                                                            {!! Form::select('ownership_status_id', $eaOwnershipStatus, $appInfo->ownership_status_id, ['class' => 'form-control  input-md ','id'=>'ownership_status_id']) !!}
                                                            {!! $errors->first('ownership_status_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 country_of_origin_div">
                                                        {!! Form::label('country_of_origin_id','Country of origin',['class'=>'col-md-12 text-left']) !!}
                                                        <div class="col-md-12">
                                                            {!! Form::select('country_of_origin_id',$countriesWithoutBD, $appInfo->country_of_origin_id,['class'=>'form-control input-md select2', 'id' => 'country_of_origin_id', "style" => "width: 100%"]) !!}
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
                                                            {!! Form::text('project_name', $appInfo->project_name, ['class' => 'form-control required input-md ','id'=>'project_name']) !!}
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
                                                            {!! Form::text('business_class_code', $appInfo->class_code, ['class' => 'form-control required input-md', 'min' => 4,'onkeyup' => 'findBusinessClassCode()']) !!}

                                                            <input type="hidden" name="is_valid_bbs_code" id="is_valid_bbs_code" value="{{ empty($appInfo->class_code) ? 0 : 1 }}"/>

                                                            <span class="help-text" style="margin: 5px 0;">
                                                            <a style="cursor: pointer;" data-toggle="modal" data-target="#businessClassModal" onclick="openBusinessSectorModal(this)"
                                                               data-action="/irc-recommendation-regular/get-business-class-modal">
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
                                                            <legend class="scheduler-border">Other info. based on your business class (Code = <span id="business_class_list_of_code"></span>)</legend>

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
                                                            {!! Form::textarea('major_activities', $appInfo->major_activities, ['class' => 'form-control input-md maxTextCountDown', 'size' =>'5x2','data-rule-maxlength'=>'240', 'placeholder' => 'Maximum 240 characters', "data-charcount-maxlength" => "240"]) !!}
                                                            {!! $errors->first('major_activities','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="panel panel-info">
                                    <div class="panel-heading"><strong>Information of Principal Promoter/Chairman/Managing Director/CEO/Country Manager</strong></div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('ceo_country_id') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_country_id','Country ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('ceo_country_id', $countries, $appInfo->ceo_country_id, ['class' => 'form-control input-md','id'=>'ceo_country_id', "style" => "width: 100%"]) !!}
                                                        {!! $errors->first('ceo_country_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_dob') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_dob','Date of Birth',['class'=>'col-md-5']) !!}
                                                    <div class=" col-md-7">
                                                        <div class="input-group date"
                                                             data-date-format="dd-mm-yyyy">
                                                            {!! Form::text('ceo_dob', (empty($appInfo->ceo_dob)) ? '' : date('d-M-Y', strtotime($appInfo->ceo_dob)), ['class'=>'form-control input-md datepicker date', 'id' => 'ceo_dob', 'placeholder'=>'Pick from datepicker']) !!}
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
                                                        {!! Form::text('ceo_passport_no', $appInfo->ceo_passport_no, ['maxlength'=>'20',
                                                        'class' => 'form-control input-md', 'id'=>'ceo_passport_no']) !!}
                                                        {!! $errors->first('ceo_passport_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div id="ceo_nid_div"
                                                     class="col-md-6 {{$errors->has('ceo_nid') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_nid','NID No.',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_nid', $appInfo->ceo_nid, ['maxlength'=>'20',
                                                        'class' => 'form-control input-md bd_nid','id'=>'ceo_nid']) !!}
                                                        {!! $errors->first('ceo_nid','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_designation') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_designation','Designation', ['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_designation', $appInfo->ceo_designation,
                                                        ['maxlength'=>'80','class' => 'form-control input-md']) !!}
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
                                                        'class' => 'form-control input-md required']) !!}
                                                        {!! $errors->first('ceo_full_name','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div id="ceo_district_div" class="col-md-6 {{$errors->has('ceo_district_id') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_district_id','District/City/State ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('ceo_district_id',$districts, $appInfo->ceo_district_id, ['maxlength'=>'80','class' => 'form-control input-md', "style" => "width: 100%"]) !!}
                                                        {!! $errors->first('ceo_district_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div id="ceo_city_div"
                                                     class="col-md-6 hidden {{$errors->has('ceo_city') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_city','City',['class'=>'text-left  col-md-5']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_city', $appInfo->ceo_city,['class' => 'form-control input-md']) !!}
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
                                                        {!! Form::text('ceo_state', $appInfo->ceo_state,['class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('ceo_state','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div id="ceo_thana_div"
                                                     class="col-md-6 {{$errors->has('ceo_thana_id') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_thana_id','Police Station/Town ',['class'=>'col-md-5 text-left','placeholder'=>'Select district first']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('ceo_thana_id',[], $appInfo->ceo_thana_id, ['maxlength'=>'80','class' => 'form-control input-md','placeholder' => 'Select district first', "style" => "width: 100%"]) !!}
                                                        {!! $errors->first('ceo_thana_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_post_code') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_post_code','Post/Zip Code ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_post_code', $appInfo->ceo_post_code, ['maxlength'=>'80','class' => 'form-control input-md engOnly']) !!}
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
                                                        {!! Form::text('ceo_address', $appInfo->ceo_address, ['maxlength'=>'150','class' => 'BigInputField form-control input-md']) !!}
                                                        {!! $errors->first('ceo_address','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_telephone_no') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_telephone_no','Telephone No.',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_telephone_no', $appInfo->ceo_telephone_no, ['maxlength'=>'20','class' => 'form-control input-md helpText15 phone_or_mobile']) !!}
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
                                                        {!! Form::text('ceo_mobile_no', $appInfo->ceo_mobile_no, ['class' => 'form-control input-md helpText15 phone_or_mobile required']) !!}
                                                        {!! $errors->first('ceo_mobile_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_father_name') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_father_name','Father\'s Name',['class'=>'col-md-5 text-left', 'id' => 'ceo_father_label']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_father_name', $appInfo->ceo_father_name, ['class' => 'form-control textOnly input-md']) !!}
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
                                                        {!! Form::text('ceo_email', $appInfo->ceo_email, ['class' => 'form-control email input-md required']) !!}
                                                        {!! $errors->first('ceo_email','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_mother_name') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_mother_name','Mother\'s Name',['class'=>'col-md-5 text-left', 'id' => 'ceo_mother_label']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_mother_name', $appInfo->ceo_mother_name, ['class' => 'form-control textOnly input-md']) !!}
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
                                                        {!! Form::text('ceo_fax_no', $appInfo->ceo_fax_no, ['class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('ceo_fax_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_spouse_name') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_spouse_name','Spouse name',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_spouse_name', $appInfo->ceo_spouse_name, ['class' => 'form-control textOnly input-md']) !!}
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
                                                            {!! Form::radio('ceo_gender', 'Male', !empty($appInfo->ceo_gender) && $appInfo->ceo_gender == "Male", ['class'=>'required']) !!}
                                                            Male
                                                        </label>
                                                        <label class="radio-inline">
                                                            {!! Form::radio('ceo_gender', 'Female', !empty($appInfo->ceo_gender) && $appInfo->ceo_gender == "Female", ['class'=>'required']) !!}
                                                            Female
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="panel panel-info">
                                    <div class="panel-heading"><strong>Office Address</strong></div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('office_division_id') ? 'has-error': ''}}">
                                                    {!! Form::label('office_division_id','Division',['class'=>'text-left col-md-5 required-star']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('office_division_id', $divisions, $appInfo->office_division_id, ['class' => 'form-control imput-md required','id' => 'office_division_id']) !!}
                                                        {!! $errors->first('office_division_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('office_district_id') ? 'has-error': ''}}">
                                                    {!! Form::label('office_district_id','District',['class'=>'col-md-5 text-left required-star']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('office_district_id', $districts, $appInfo->office_district_id, ['class' => 'form-control input-md required', "style" => "width: 100%"]) !!}
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
                                                        {!! Form::select('office_thana_id',[''], $appInfo->office_thana_id, ['class' => 'form-control input-md required', "style" => "width: 100%"]) !!}
                                                        {!! $errors->first('office_thana_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('office_post_office') ? 'has-error': ''}}">
                                                    {!! Form::label('office_post_office','Post Office',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('office_post_office', $appInfo->office_post_office, ['class' => 'form-control input-md']) !!}
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
                                                        {!! Form::text('office_post_code', $appInfo->office_post_code, ['class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('office_post_code','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('office_address') ? 'has-error': ''}}">
                                                    {!! Form::label('office_address','Address ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('office_address', $appInfo->office_address, ['maxlength'=>'150','class' => 'form-control input-md']) !!}
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
                                                        {!! Form::text('office_telephone_no', $appInfo->office_telephone_no, ['maxlength'=>'20','class' => 'form-control input-md helpText15 phone_or_mobile']) !!}
                                                        {!! $errors->first('office_telephone_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('office_mobile_no') ? 'has-error': ''}}">
                                                    {!! Form::label('office_mobile_no','Mobile No. ',['class'=>'col-md-5 text-left required-star']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('office_mobile_no', $appInfo->office_mobile_no, ['class' => 'form-control input-md helpText15 phone_or_mobile required']) !!}
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
                                                        {!! Form::text('office_fax_no', $appInfo->office_fax_no, ['class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('office_fax_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('office_email') ? 'has-error': ''}}">
                                                    {!! Form::label('office_email','Email ',['class'=>'col-md-5 text-left required-star']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('office_email', $appInfo->office_email, ['class' => 'form-control email input-md required']) !!}
                                                        {!! $errors->first('office_email','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="panel panel-info">
                                    <div class="panel-heading"><strong>Factory Address(This would be IRC
                                            address)</strong></div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('factory_district_id') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_district_id','District',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('factory_district_id', $districts, $appInfo->factory_district_id, ['class' => 'form-control input-md', "style" => "width: 100%"]) !!}
                                                        {!! $errors->first('factory_district_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('factory_thana_id') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_thana_id','Police Station', ['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('factory_thana_id',[''], $appInfo->factory_thana_id, ['class' => 'form-control input-md', "style" => "width: 100%"]) !!}
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
                                                        {!! Form::text('factory_post_office', $appInfo->factory_post_office, ['class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('factory_post_office','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('factory_post_code') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_post_code','Post Code', ['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('factory_post_code', $appInfo->factory_post_code, ['class' => 'form-control input-md number']) !!}
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
                                                        {!! Form::text('factory_address', $appInfo->factory_address, ['maxlength'=>'150','class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('factory_address','<span class="help-block">:message</span>') !!}

                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('factory_telephone_no') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_telephone_no','Telephone No.',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('factory_telephone_no', $appInfo->factory_telephone_no, ['maxlength'=>'20','class' => 'form-control input-md helpText15 phone_or_mobile']) !!}
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
                                                        {!! Form::text('factory_mobile_no', $appInfo->factory_mobile_no, ['class' => 'form-control input-md helpText15']) !!}
                                                        {!! $errors->first('factory_mobile_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('factory_fax_no') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_fax_no','Fax No. ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('factory_fax_no', $appInfo->factory_fax_no, ['class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('factory_fax_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="panel panel-info">
                                    <div class="panel-heading"><strong>Registration Information</strong></div>
                                    <div class="panel-body">
                                        {{--1. Project status--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">1. Project status</legend>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6 {{$errors->has('project_status_id') ? 'has-error': ''}}">
                                                        {!! Form::label('project_status','Project status', ['class'=>'col-md-5 text-left']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::select('project_status_id', $projectStatusList, $appInfo->project_status_id, ["placeholder" => "Select One", 'class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('project_status_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--2. Date of commercial operation--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">2. Date of commercial operation</legend>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div id="date_of_arrival_div"
                                                         class="col-md-6 {{$errors->has('commercial_operation_date') ? 'has-error': ''}}">
                                                        {!! Form::label('commercial_operation_date','Date of commercial operation',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            <div class="input-group date">
                                                                {!! Form::text('commercial_operation_date', (!empty($appInfo->commercial_operation_date) ? date('d-M-Y', strtotime($appInfo->commercial_operation_date)) : ''), ['class' => 'form-control input-md datepicker date', 'placeholder'=>'dd-mm-yyyy']) !!}
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

                                                    <tbody id="annual_production_capacity">
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
                                                                        {!! Form::text('local_land_ivst', $appInfo->local_land_ivst, ['data-rule-maxlength'=>'40','class' => 'form-control total_investment_item input-md number','id'=>'local_land_ivst',
                                                                         'onblur' => 'CalculateTotalInvestmentTk()'
                                                                        ]) !!}
                                                                        {!! $errors->first('local_land_ivst','<span class="help-block">:message</span>') !!}
                                                                    </td>
                                                                    <td>
                                                                        {!! Form::select("local_land_ivst_ccy", $currencies, $appInfo->local_land_ivst_ccy, ["id"=>"local_land_ivst_ccy", "class" => "form-control input-md usd-def"]) !!}
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
                                                                        {!! Form::text('local_building_ivst', $appInfo->local_building_ivst, ['data-rule-maxlength'=>'40','class' => 'form-control input-md total_investment_item number','id'=>'local_building_ivst',
                                                                         'onblur' => 'CalculateTotalInvestmentTk()']) !!}
                                                                        {!! $errors->first('local_building_ivst','<span class="help-block">:message</span>') !!}
                                                                    </td>
                                                                    <td>
                                                                        {!! Form::select("local_building_ivst_ccy", $currencies, $appInfo->local_building_ivst_ccy, ["id"=>"local_building_ivst_ccy", "class" => "form-control input-md usd-def"]) !!}
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
                                                                        {!! Form::text('local_machinery_ivst', $appInfo->local_machinery_ivst, ['data-rule-maxlength'=>'40','class' => 'form-control required input-md number total_investment_item','id'=>'local_machinery_ivst',
                                                                        'onblur' => 'CalculateTotalInvestmentTk()']) !!}
                                                                        {!! $errors->first('local_machinery_ivst','<span class="help-block">:message</span>') !!}
                                                                    </td>
                                                                    <td>
                                                                        {!! Form::select("local_machinery_ivst_ccy", $currencies, $appInfo->local_machinery_ivst_ccy, ["id"=>"local_machinery_ivst_ccy", "class" => "form-control input-md usd-def"]) !!}
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
                                                                        {!! Form::text('local_others_ivst', $appInfo->local_others_ivst, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number total_investment_item','id'=>'local_others_ivst',
                                                                        'onblur' => 'CalculateTotalInvestmentTk()']) !!}
                                                                        {!! $errors->first('local_others_ivst','<span class="help-block">:message</span>') !!}
                                                                    </td>
                                                                    <td>
                                                                        {!! Form::select("local_others_ivst_ccy", $currencies, $appInfo->local_others_ivst_ccy, ["id"=>"local_others_ivst_ccy", "class" => "form-control input-md usd-def"]) !!}
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
                                                                        {!! Form::text('local_wc_ivst', $appInfo->local_wc_ivst, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number total_investment_item','id'=>'local_wc_ivst',
                                                                        'onblur' => 'CalculateTotalInvestmentTk()']) !!}
                                                                        {!! $errors->first('local_wc_ivst','<span class="help-block">:message</span>') !!}
                                                                    </td>
                                                                    <td>
                                                                        {!! Form::select("local_wc_ivst_ccy", $currencies, $appInfo->local_wc_ivst_ccy, ["id"=>"local_wc_ivst_ccy", "class" => "form-control input-md usd-def"]) !!}
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
                                                            {!! Form::text('total_fixed_ivst_million', $appInfo->total_fixed_ivst_million, ['data-rule-maxlength'=>'40','class' => 'form-control input-md numberNoNegative total_fixed_ivst_million required','id'=>'total_fixed_ivst_million','readonly']) !!}
                                                            {!! $errors->first('total_fixed_ivst_million','<span class="help-block">:message</span>') !!}
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
                                                            {!! Form::text('total_fixed_ivst', $appInfo->total_fixed_ivst, ['data-rule-maxlength'=>'40','class' => 'form-control input-md numberNoNegative total_invt_bdt', 'id'=>'total_invt_bdt','readonly']) !!}
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
                                                            {!! Form::number('usd_exchange_rate', $appInfo->usd_exchange_rate, ['data-rule-maxlength'=>'40','class' => 'form-control input-md numberNoNegative','id'=>'usd_exchange_rate']) !!}
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
                                                                        {!! Form::text('total_fee', $appInfo->total_fee, ['class' => 'form-control input-md number', 'id'=>'total_fee', 'readonly']) !!}
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

                                        {{--4. Source of Finance--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">4. Source of finance</legend>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered" cellspacing="0"
                                                       width="100%">
                                                    <tbody id="annual_production_capacity">
                                                    {{--(a) Local Equity--}}
                                                    <tr>
                                                        <td><strong>(a)</strong> Local Equity (Million)</td>
                                                        <td>
                                                            {!! Form::text('finance_src_loc_equity_1', $appInfo->finance_src_loc_equity_1, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number','id'=>'finance_src_loc_equity_1','onblur'=>"calculateSourceOfFinance(this.id)"]) !!}
                                                            {!! $errors->first('finance_src_loc_equity_1','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="38%">Foreign Equity (Million)</td>
                                                        <td>
                                                            {!! Form::text('finance_src_foreign_equity_1', $appInfo->finance_src_foreign_equity_1, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number','id'=>'finance_src_foreign_equity_1','onblur'=>"calculateSourceOfFinance(this.id)"]) !!}
                                                            {!! $errors->first('finance_src_foreign_equity_1','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Total Equity</th>
                                                        <td>
                                                            {!! Form::text('finance_src_loc_total_equity_1', $appInfo->finance_src_loc_total_equity_1, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number readOnly','id'=>'finance_src_loc_total_equity_1']) !!}
                                                            {!! $errors->first('finance_src_loc_total_equity_1','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>

                                                    {{--(b) Local Loan --}}
                                                    <tr>
                                                        <td><strong>(b)</strong> Local Loan (Million)</td>
                                                        <td>
                                                            {!! Form::text('finance_src_loc_loan_1', $appInfo->finance_src_loc_loan_1, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number','id'=>'finance_src_loc_loan_1','onblur'=>"calculateSourceOfFinance(this.id)"]) !!}
                                                            {!! $errors->first('finance_src_loc_loan_1','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Foreign Loan (Million)</td>
                                                        <td>
                                                            {!! Form::text('finance_src_foreign_loan_1', $appInfo->finance_src_foreign_loan_1, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number ','id'=>'finance_src_foreign_loan_1','onblur'=>"calculateSourceOfFinance(this.id)"]) !!}
                                                            {!! $errors->first('finance_src_foreign_loan_1','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Total Loan (Million)</th>
                                                        <td>
                                                            {!! Form::text('finance_src_total_loan', $appInfo->finance_src_total_loan, ['id'=>'finance_src_total_loan','class' => 'form-control input-md readOnly numberNoNegative', 'data-rule-maxlength'=>'240']) !!}
                                                            {!! $errors->first('finance_src_total_loan','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>

                                                    {{--Total Financing Million (a+b)--}}
                                                    <tr>
                                                        <th>Total Financing Million (a+b)</th>
                                                        <td>
                                                            {!! Form::text('finance_src_loc_total_financing_m', $appInfo->finance_src_loc_total_financing_m, ['id'=>'finance_src_loc_total_financing_m','class' => 'form-control input-md readOnly numberNoNegative', 'data-rule-maxlength'=>'240']) !!}
                                                            {!! $errors->first('finance_src_loc_total_financing_m','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>

                                                    {{--Total Financing BDT (a+b)--}}
                                                    <tr>
                                                        <th>Total Financing BDT (a+b)</th>
                                                        <td>
                                                            {!! Form::text('finance_src_loc_total_financing_1',  $appInfo->finance_src_loc_total_financing_1, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number readOnly numberNoNegative','id'=>'finance_src_loc_total_financing_1']) !!}
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
                                                               title="From the above information, the values of “Local Equity (Million)” and “Local Loan (Million)” will go into the
                                                                Equity Amount and Loan Amount respectively for Bangladesh. The summation of the Equity Amount and Loan Amount of other
                                                                countries will be equal to the values of Foreign Equity (Million) and “Foreign Loan (Million) respectively.">
                                                            </i>
                                                            Country wise source of finance (Million BDT)
                                                        </th>
                                                    </tr>
                                                </table>
                                                <table class="table table-striped table-bordered" cellspacing="0"
                                                       width="100%" id="financeTableId">
                                                    <thead>
                                                    <tr>
                                                        <th class="required-star">Country</th>
                                                        <th class="required-star">Equity Amount<span class="text-danger" id="equity_amount_err"></span></th>
                                                        <th class="required-star">Loan Amount<span class="text-danger" id="loan_amount_err"></span></th>
                                                        <th>#</th>
                                                    </tr>
                                                    </thead>

                                                    @if(count($source_of_finance) > 0)
                                                        <?php $inc = 0; ?>
                                                        @foreach($source_of_finance as $finance)
                                                            <tr id="financeTableIdRow{{$inc}}" data-number="0">
                                                                <td>
                                                                    {!! Form::hidden("source_of_finance_id[$inc]", $finance->id) !!}
                                                                    {!!Form::select("country_id[$inc]", $countries, $finance->country_id, ['class' => 'form-control required', "style" => "width: 100%"])!!}
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
                                                                {{-- <td> --}}
                                                                    <?php 
                                                                        // if ($inc == 0) { 
                                                                    ?>
                                                                    {{-- <a class="btn btn-sm btn-primary addTableRows"
                                                                       onclick="addTableRowForIRC('financeTableId', 'financeTableIdRow0');"><i
                                                                                class="fa fa-plus"></i></a> --}}
                                                                    <?php 
                                                                    // } else { 
                                                                    ?>
                                                                    {{-- <a href="javascript:void(0);"
                                                                       class="btn btn-sm btn-danger removeRow"
                                                                       onclick="removeTableRow('financeTableId','financeTableIdRow{{$inc}}');">
                                                                        <i class="fa fa-times"
                                                                           aria-hidden="true"></i></a> --}}
                                                                    <?php 
                                                                    // } 
                                                                    ?>
                                                                {{-- </td> --}}

                                                                <td>
                                                                    @if($inc == 0)
                                                                    <a class="btn btn-sm btn-primary addTableRows" onclick="addTableRowForIRC('financeTableId', 'financeTableIdRow0');">
                                                                        <i class="fa fa-plus"></i>
                                                                    </a>
                                                                   @else
                                                                    <a href="javascript:void(0);" class="btn btn-sm btn-danger removeRow" onclick="removeTableRow('financeTableId','financeTableIdRow{{$inc}}');">
                                                                        <i class="fa fa-times" aria-hidden="true"></i>
                                                                    </a>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <?php $inc++; ?>
                                                        @endforeach
                                                    @else
                                                        <tr id="financeTableIdRow" data-number="0">
                                                            <td>
                                                                {!!Form::select('country_id[]', $countries, null, ['class' => 'form-control required', "style" => "width: 100%"])!!}
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
                                                                <a class="btn btn-sm btn-primary addTableRows" title="Add more" onclick="addTableRowForIRC('financeTableId', 'financeTableIdRow');">
                                                                    <i class="fa fa-plus"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endif

                                                </table>
                                            </div>
                                        </fieldset>

                                        {{--5. Manpower of the organization--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">5. Manpower of the organization</legend>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                    <tbody id="manpower">
                                                    <tr>
                                                        <th class="alert alert-info" colspan="3">Local (Bangladesh only)</th>
                                                        <th class="alert alert-info" colspan="3">Foreign (Abroad country)</th>
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
                                                            {!! Form::text('local_male', $appInfo->local_male, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'local_male']) !!}
                                                            {!! $errors->first('local_male','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('local_female', $appInfo->local_female, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'local_female']) !!}
                                                            {!! $errors->first('local_female','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('local_total', $appInfo->local_total, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field numberNoNegative required','id'=>'local_total','readonly']) !!}
                                                            {!! $errors->first('local_total','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('foreign_male', $appInfo->foreign_male, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'foreign_male']) !!}
                                                            {!! $errors->first('foreign_male','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('foreign_female', $appInfo->foreign_female, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'foreign_female']) !!}
                                                            {!! $errors->first('foreign_female','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('foreign_total', $appInfo->foreign_total, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field numberNoNegative required','id'=>'foreign_total','readonly']) !!}
                                                            {!! $errors->first('foreign_total','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('manpower_total', $appInfo->manpower_total, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'mp_total','readonly']) !!}
                                                            {!! $errors->first('manpower_total','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('manpower_local_ratio', $appInfo->manpower_local_ratio, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'mp_ratio_local','readonly']) !!}
                                                            {!! $errors->first('manpower_local_ratio','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('manpower_foreign_ratio', $appInfo->manpower_foreign_ratio, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number required','id'=>'mp_ratio_foreign','readonly']) !!}
                                                            {!! $errors->first('manpower_foreign_ratio','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </fieldset>

                                        {{--6. Sales (in 100%)--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">6. Sales (in 100%)</legend>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-4 {{$errors->has('local_sales') ? 'has-error': ''}}">
                                                        {!! Form::label('local_sales','Local ',['class'=>'col-md-4 text-left']) !!}
                                                        <div class="col-md-8">
                                                            {!! Form::text('local_sales', $appInfo->local_sales, ['class' => 'form-control input-md number', 'id'=>'local_sales_per']) !!}
                                                            {!! $errors->first('local_sales','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 {{$errors->has('foreign_sales') ? 'has-error': ''}}">
                                                        {!! Form::label('foreign_sales','Foreign ',['class'=>'col-md-4 text-left']) !!}
                                                        <div class="col-md-8">
                                                            {!! Form::number('foreign_sales', $appInfo->foreign_sales, ['class' => 'form-control input-md number', 'id'=>'foreign_sales_per']) !!}
                                                            {!! $errors->first('foreign_sales','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 {{$errors->has('total_sales') ? 'has-error': ''}}">
                                                        {!! Form::label('total_sales','Total in % ',['class'=>'col-md-4 text-left']) !!}
                                                        <div class="col-md-8">
                                                            {!! Form::number('total_sales', $appInfo->total_sales, ['class' => 'form-control input-md number', 'id'=>'total_sales', 'readonly']) !!}
                                                            {!! $errors->first('total_sales','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--7. Annual production capacity--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">7. Annual Production Capacity as per BIDA registration/Amendment</legend>
                                            {{-- <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-10 {{$errors->has('annual_production_start_date') ? 'has-error': ''}}">
                                                        {!! Form::label('annual_production_start_date','Annual production start date',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            <div class="input-group date">
                                                                {!! Form::text('annual_production_start_date', (!empty($appInfo->annual_production_start_date) ? date('d-M-Y', strtotime($appInfo->annual_production_start_date)) : ''), ['class'=>'form-control input-md datepicker', 'id' => 'annual_production_start_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            </div>
                                                            {!! $errors->first('annual_production_start_date','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br> --}}
                                            <fieldset class="scheduler-border">
                                                {{-- <legend class="scheduler-border">Annual Production Capacity as per BIDA registration</legend> --}}
                                                <fieldset >
                                                    <div class="panel panel-info">
                                                        <div class="table-responsive">
                                                            <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="brProductionTbl">
                                                                <thead>
                                                                <tr>
                                                                    <th>Name of Product</th>
                                                                    <th>Unit of Quantity</th>
                                                                    <th>Quantity</th>
                                                                    <th>Price (USD)</th>
                                                                    <th>Sales Value in BDT (million)</th>
                                                                    <th></th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @if(count($brAnnualProductionCapacity)>0)
                                                                    <?php $inc = 0; ?>
                                                                    @foreach($brAnnualProductionCapacity as $brAnnualProductionCap)
                                                                        <tr id="brRowProCount{{$inc}}" data-number="0">
                                                                            <input type="hidden" name="br_apc_id[{{ $inc }}]" value="{{ $brAnnualProductionCap->id }}">
                                                                            <td>
                                                                                {!! Form::text("br_apc_product_name[$inc]", $brAnnualProductionCap->product_name, ['data-rule-maxlength'=>'255','class' => 'form-control input-md product apc_product_name', 'id'=>'apc_product_name']) !!}
                                                                                {!! $errors->first('br_apc_product_name','<span class="help-block">:message</span>') !!}
                                                                            </td>

                                                                            <td>
                                                                                {!! Form::select("br_apc_quantity_unit[$inc]",$productUnit,$brAnnualProductionCap->quantity_unit,['class'=>'form-control input-md apc_quantity_unit', 'id' => 'apc_quantity_unit']) !!}
                                                                            </td>

                                                                            <td>
                                                                                <input type="number"
                                                                                       id="apc_quantity"
                                                                                       name="br_apc_quantity[{{$inc}}]"
                                                                                       class="form-control quantity1 CalculateInputByBoxNo number apc_quantity"
                                                                                       value="{{ $brAnnualProductionCap->quantity}}">

                                                                                {!! $errors->first('br_quantity','<span class="help-block">:message</span>') !!}
                                                                            </td>

                                                                            <td>
                                                                                <input type="number"
                                                                                       id="br_apc_price_usd"
                                                                                       name="br_apc_price_usd[{{$inc}}]"
                                                                                       class="form-control quantity1 CalculateInputByBoxNo number apc_price_usd"
                                                                                       value="{{$brAnnualProductionCap->price_usd}}">

                                                                                {!! $errors->first('br_price_usd','<span class="help-block">:message</span>') !!}
                                                                            </td>

                                                                            <td>
                                                                                {!! Form::text("br_apc_value_taka[$inc]", $brAnnualProductionCap->price_taka, ['class' => 'form-control input-md number apc_value_taka', 'id'=>"apc_value_taka"]) !!}
                                                                                {!! $errors->first('br_price_taka','<span class="help-block">:message</span>') !!}
                                                                            </td>

                                                                            <td>
                                                                                @if($inc == 0)
                                                                                <a class="btn btn-md btn-primary addTableRows"
                                                                                   onclick="addTableRowForIRC('brProductionTbl', 'brRowProCount0');">
                                                                                    <i class="fa fa-plus"></i>
                                                                                </a>
                                                                                @else
                                                                                <a href="javascript:void(0);" class="btn btn-md btn-danger removeRow"
                                                                                   onclick="removeTableRow('brProductionTbl','brRowProCount{{$inc}}');">
                                                                                    <i class="fa fa-times" aria-hidden="true"></i></a>
                                                                                @endif
                                                                            </td>
                                                                            <?php $inc++; ?>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr id="brRowProCount0" data-number="0">
                                                                        <td>
                                                                            {!! Form::text("br_apc_product_name[0]", '', ['data-rule-maxlength'=>'255','class' => 'form-control input-md product apc_product_name', 'id'=>'apc_product_name']) !!}
                                                                            {!! $errors->first('br_apc_product_name','<span class="help-block">:message</span>') !!}
                                                                        </td>

                                                                        <td>
                                                                            {!! Form::select("br_apc_quantity_unit[0]",$productUnit,'',['class'=>'form-control input-md apc_quantity_unit', 'id' => 'apc_quantity_unit']) !!}
                                                                        </td>

                                                                        <td>
                                                                            <input type="number"
                                                                                   id="apc_quantity"
                                                                                   name="br_apc_quantity[0]"
                                                                                   class="form-control quantity1 CalculateInputByBoxNo number apc_quantity"
                                                                                   value="">

                                                                            {!! $errors->first('br_quantity','<span class="help-block">:message</span>') !!}
                                                                        </td>

                                                                        <td>
                                                                            <input type="number"
                                                                                   id="br_apc_price_usd"
                                                                                   name="br_apc_price_usd[0]"
                                                                                   class="form-control quantity1 CalculateInputByBoxNo number apc_price_usd"
                                                                                   value="">

                                                                            {!! $errors->first('br_price_usd','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                        <td>
                                                                            {!! Form::text("br_apc_value_taka[0]", '', ['class' => 'form-control input-md number apc_value_taka', 'id'=>"apc_value_taka"]) !!}
                                                                            {!! $errors->first('br_price_taka','<span class="help-block">:message</span>') !!}
                                                                        </td>

                                                                        <td>
                                                                            <a class="btn btn-md btn-primary addTableRows" onclick="addTableRowForIRC('brProductionTbl', 'brRowProCount0');">
                                                                                <i class="fa fa-plus"></i>
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </fieldset>

                                            <fieldset class="scheduler-border" id="annual_raw">
                                                <legend class="scheduler-border">Recommended Annual Production Capacity as per IRC with Raw Materials. </legend>
                                                <fieldset id="annual_raw">
                                                    <div class="panel panel-info">
                                                        <div class="panel-heading">
                                                            <div class="pull-left">
                                                                <strong>Annual production capacity</strong>
                                                            </div>
                                                            <div class="pull-right">
                                                                <a data-toggle="modal" data-target="#ircRegularadhocModal" onclick="openModal(this)"
                                                                data-action="{{ url('irc-recommendation-regular/apc-form/'.Encryption::encodeId($appInfo->id)) }}">
                                                                {!! Form::button('Add Annual Production', array('type' => 'button', 'class' => 'btn btn-sm btn-info')) !!}
                                                                </a>
                                                            </div>
                                                            <div class="clearfix"></div>
                                                        </div>
                                                        <div class="panel-body" id="load_apc_data"></div>
                                                    </div>
                                                </fieldset>
                                            </fieldset>

                                            <fieldset class="scheduler-border" id="annual_spare" style="display:none">
                                                <legend class="scheduler-border">Spare Parts Details</legend>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="table-responsive">
                                                            <table id="productionSpareTbl" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                                <thead>
                                                                <tr>
                                                                    <th class="alert alert-info">Name of the Spare Parts</th>
                                                                    <th class="alert alert-info">Unit of Quantity</th>
                                                                    <th class="alert alert-info">Quantity</th>
                                                                    <th class="alert alert-info">Price (USD)</th>
                                                                    <th class="alert alert-info">Value Taka (in million)</th>
                                                                    <th class="alert alert-info">#</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @if(count($annualProductionSpareParts)>0)
                                                                    <?php $inc = 0; ?>
                                                                    @foreach($annualProductionSpareParts as $eachProductionSpare)
                                                                        <tr id="rowProSpareCount{{$inc}}" data-number="0">
                                                                            <input type="hidden" name="apsp_id[{{ $inc }}]" value="{{ $eachProductionSpare->id }}">
                                                                            <td style="width: 40%;">
                                                                                {!! Form::text("apsp_product_name[$inc]", $eachProductionSpare->product_name, ['data-rule-maxlength'=>'255','class' => 'form-control apsp_product_name input-md product','id'=>'apsp_product_name']) !!}
                                                                                {!! $errors->first('apsp_product_name','<span class="help-block">:message</span>') !!}

                                                                            </td>
                                                                            <td style="width: 15%;">{!! Form::select("apsp_quantity_unit[$inc]",$productUnit,$eachProductionSpare->quantity_unit,['class'=>'form-control apsp_quantity_unit input-md', 'id' => 'apsp_quantity_unit']) !!}</td>
                                                                            <td style="width: 15%;">
                                                                                <input type="number"
                                                                                       id="apsp_quantity"
                                                                                       name="apsp_quantity[{{$inc}}]"
                                                                                       class="form-control quantity1 CalculateInputByBoxNo number apsp_quantity"
                                                                                       value="{{ $eachProductionSpare->quantity}}">

                                                                                {!! $errors->first('quantity','<span class="help-block">:message</span>') !!}
                                                                            </td>
                                                                            <td style="width: 15%;">
                                                                                <input type="number"
                                                                                       id="apsp_price_usd"
                                                                                       name="apsp_price_usd[{{$inc}}]"
                                                                                       class="form-control quantity1 CalculateInputByBoxNo number apsp_price_usd"
                                                                                       value="{{$eachProductionSpare->price_usd}}">

                                                                                {!! $errors->first('price_usd','<span class="help-block">:message</span>') !!}
                                                                            </td>
                                                                            <td style="width: 15%;">
                                                                                {!! Form::text("apsp_value_taka[$inc]", $eachProductionSpare->price_taka, ['class' => 'form-control input-md number apsp_value_taka','id'=>"apsp_value_taka"]) !!}
                                                                                {!! $errors->first('price_taka','<span class="help-block">:message</span>') !!}
                                                                            </td>
                                                                            <td>
                                                                                @if($inc == 0)
                                                                                <a class="btn btn-md btn-primary addTableRows"
                                                                                   onclick="addTableRowForIRC('productionSpareTbl', 'rowProSpareCount0');">
                                                                                   <i class="fa fa-plus"></i>
                                                                                </a>
                                                                                @else
                                                                                <a href="javascript:void(0);" class="btn btn-md btn-danger removeRow"
                                                                                   onclick="removeTableRow('productionSpareTbl','rowProSpareCount{{$inc}}');">
                                                                                    <i class="fa fa-times" aria-hidden="true"></i>
                                                                                </a>
                                                                                @endif
                                                                            </td>
                                                                            <?php $inc++; ?>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr id="rowProSpareCount0" data-number="0">
                                                                        <td>
                                                                            {!! Form::text("apsp_product_name[0]", '', ['data-rule-maxlength'=>'255','class' => 'form-control input-md product apsp_product_name','id'=>'apsp_product_name']) !!}
                                                                            {!! $errors->first('product_name','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                        <td style="width: 15%;">
                                                                            {!! Form::select("apsp_quantity_unit[0]", $productUnit, '',['class'=>'form-control input-md apsp_quantity_unit', 'id' => 'apsp_quantity_unit']) !!}
                                                                        </td>
                                                                        <td>
                                                                            <input type="number"
                                                                                   id="apsp_quantity"
                                                                                   name="apsp_quantity[0]"
                                                                                   onblur="calculateAnnulCapacity(this.id)"
                                                                                   class="form-control quantity1 CalculateInputByBoxNo number apsp_quantity">

                                                                            {!! $errors->first('quantity','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                        <td>
                                                                            <input type="number"
                                                                                   id="apsp_price_usd"
                                                                                   name="apsp_price_usd[0]"
                                                                                   class="form-control quantity1 CalculateInputByBoxNo number apsp_price_usd"
                                                                                   onblur="calculateAnnulCapacity(this.id)">

                                                                            {!! $errors->first('price_usd','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                        <td>
                                                                            {!! Form::text("apsp_value_taka[0]", '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md number apsp_value_taka','id'=>'apsp_value_taka']) !!}
                                                                            {!! $errors->first('price_taka','<span class="help-block">:message</span>') !!}
                                                                        </td>
                                                                        <td>
                                                                            <a class="btn btn-md btn-primary addTableRows"
                                                                               onclick="addTableRowForIRC('productionSpareTbl', 'rowProSpareCount0');">
                                                                               <i class="fa fa-plus"></i>
                                                                            </a>
                                                                        </td>

                                                                    </tr>
                                                                @endif
                                                                </tbody>
                                                            </table>
                                                            <table>
                                                                <tr>
                                                                    <div class="col-md-6">
                                                                        <span class="help-text" style="margin: 5px 0;">
                                                                            Exchange Rate Ref: 
                                                                            <a href="https://www.bangladesh-bank.org/econdata/exchangerate.php" target="_blank">Bangladesh Bank</a>
                                                                            . Please Enter Today's Exchange Rate
                                                                        </span>
                                                                    </div>
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
                                                    <table id="existingMachinesSpareTbl"
                                                           class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                                                        <thead class="alert alert-info">
                                                        <tr>
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
                                                                </br><span class="text-danger" style="font-size: 9px; font-weight: bold">[Format: *.pdf | Max File size 2MB]</span>                                                         <span class="required-star"></span><br/>
                                                            </th>

                                                            <th class="text-center table-header">#</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>

                                                        @if(count($existing_machines_spare)>0)
                                                            <?php $inc = 0; ?>
                                                            @foreach($existing_machines_spare as $key => $existing_machines_spare)
                                                                <tr id="existingMachinesSpare{{$inc}}" data-number="1" class="table-tr">
                                                                    <input type="hidden" name="irc_ex_machines_spare_id[{{ $inc }}]" value="{{ $existing_machines_spare->id }}">
                                                                    <td>
                                                                        {!! Form::text("em_spare_lc_no[$inc]", $existing_machines_spare->lc_no, ['class' => 'form-control input-md product em_spare_lc_no', 'id' => 'em_spare_lc_no']) !!}
                                                                    </td>

                                                                    <td width="20%">
                                                                        <div class="input-group date">
                                                                            {!! Form::text("em_spare_lc_date[$inc]", (empty($existing_machines_spare->lc_date) ? '' : date('d-M-Y', strtotime($existing_machines_spare->lc_date))), ['class'=>'form-control input-md em_spare_lc_date datepicker', 'id' => 'em_spare_lc_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                                        </div>
                                                                    </td>

                                                                    <td width="10%">
                                                                        {!! Form::select("em_spare_lc_value_currency[$inc]", $currencies, $existing_machines_spare->lc_value_currency, ['class'=>'form-control input-md em_spare_lc_value_currency', "id" => "em_spare_lc_value_currency", "style" => "width: 100%"]) !!}
                                                                    </td>

                                                                    <td>
                                                                        {!! Form::number("em_spare_value_bdt[$inc]", $existing_machines_spare->value_bdt, ['class' => 'form-control input-md em_spare_value_bdt em_spare_value_bdt', 'onkeyup' => "calculateListOfMachineryTotal('em_spare_value_bdt', 'total_spare_value_bdt')", "id" => "em_spare_value_bdt"]) !!}
                                                                    </td>

                                                                    <td>
                                                                        {!! Form::text("em_spare_lc_bank_branch[$inc]", $existing_machines_spare->lc_bank_branch, ['class' => 'form-control input-md em_spare_lc_bank_branch', "id" => "em_spare_lc_bank_branch"]) !!}
                                                                    </td>

                                                                    <td>
                                                                        <input type="file" name="em_spare_attachment[{{ $inc }}]" value="" class="form-control input-md ajax-file-upload-function"
                                                                               id="file{{ $inc }}" onchange="uploadDocument('preview_{{ $inc }}', this.id, 'validate_field_{{ $inc }}', 0, 'true')">

                                                                        @if(!empty($existing_machines_spare->attachment))
                                                                            <div class="remove-uploaded-file save_file saved_file_{{ $inc }}" style="padding-top: 3px">
                                                                                <a target="_blank" class="documentUrl btn btn-xs btn-primary" href="{{URL::to('/uploads/'.(!empty($existing_machines_spare->attachment) ? $existing_machines_spare->attachment : ''))}}">
                                                                                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                                                                    Open File
                                                                                </a>

                                                                                <a href="javascript:void(0)" onclick="removeAttachedFile({!! $inc !!}, 0)"><span class="btn btn-xs btn-danger"><i class="fa fa-times"></i></span> </a>
                                                                            </div>
                                                                        @endif

                                                                        <div class="preview-div" id="preview_{{ $inc }}">
                                                                            <input type="hidden" id="validate_field_{{ $inc }}" name="attachment_path[{{ $inc }}]" class="" value="{{ !empty($existing_machines_spare->attachment) ? $existing_machines_spare->attachment : "" }}"/>
                                                                        </div>
                                                                    </td>

                                                                    <td>
                                                                        <?php if ($inc == 0) { ?>
                                                                        <a class="btn btn-md btn-primary addTableRows"
                                                                           onclick="addTableRowForIRC('existingMachinesSpareTbl', 'existingMachinesSpare0');"><i class="fa fa-plus"></i></a>
                                                                        <?php } else { ?>
                                                                        <a href="javascript:void(0);"
                                                                           class="btn btn-md btn-danger removeRow" onclick="removeTableRow('existingMachinesSpareTbl','existingMachinesSpare{{$inc}}');">
                                                                            <i class="fa fa-times" aria-hidden="true"></i>
                                                                        </a>
                                                                        <?php } ?>
                                                                    </td>
                                                                </tr>
                                                                <?php $inc++; ?>
                                                            @endforeach
                                                        @else
                                                            <tr id="existingMachinesSpare0" data-number="1" class="table-tr">
                                                                <td>
                                                                    {!! Form::text("em_spare_lc_no[0]", '', ['class' => 'form-control input-md product em_spare_lc_no']) !!}
                                                                </td>

                                                                <td>
                                                                    <div class="input-group date">
                                                                        {!! Form::text('em_spare_lc_date[0]', '', ['class'=>'form-control input-md em_spare_lc_date datepicker', 'id' => 'annual_production_start_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                                    </div>
                                                                </td>

                                                                <td width="10%">
                                                                    {!! Form::select('em_spare_lc_value_currency[0]', $currencies, '', ['class'=>'form-control input-md em_spare_lc_value_currency', "style" => "width: 100%"]) !!}
                                                                </td>

                                                                <td>
                                                                    {!! Form::number("em_spare_value_bdt[0]", '', ['class' => 'form-control input-md em_spare_value_bdt em_spare_value_bdt', 'onkeyup' => "calculateListOfMachineryTotal('em_spare_value_bdt', 'total_spare_value_bdt')"]) !!}
                                                                </td>

                                                                <td>
                                                                    {!! Form::text("em_spare_lc_bank_branch[0]", '', ['class' => 'form-control input-md em_spare_lc_bank_branch']) !!}
                                                                </td>

                                                                <td>
                                                                    <input type="file" name="em_spare_attachment[0]" class="form-control input-md" id="em_spare_attachment0" onchange="checkFileSize(this.id)">
                                                                    <span class="text-danger" style="font-size: 9px; font-weight: bold">[Format: *.pdf | Max File size 2MB]</span>
                                                                </td>

                                                                <td>
                                                                    <a class="btn btn-md btn-primary addTableRows"
                                                                       onclick="addTableRowForIRC('existingMachinesSpareTbl', 'existingMachinesSpare0');"><i class="fa fa-plus"></i></a>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        </tbody>
                                                        <tr>
                                                            <td colspan="3"><label class="pull-right">Total</label></td>
                                                            <td>{!! Form::text("", '', ['class' => 'form-control input-md', 'id'=>'total_spare_value_bdt', 'readonly']) !!}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </fieldset>

                                            <fieldset class="scheduler-border" id="existing_lc">
                                                {{-- <legend class="scheduler-border"><span class="required-star">As Per L/C Open</span></legend> --}}
                                                <legend class="scheduler-border required-star-dynamically">As Per L/C Open</legend>

                                                <div class="table-responsive">
                                                    <table id="existingMachinesLcTbl"
                                                           class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                                                        <thead class="alert alert-info">
                                                        <tr>
                                                            <th class="text-center ">Description of Machine
                                                                <span class="required-star-dynamically"></span><br/>
                                                            </th>

                                                            <th class="text-center ">Unit of Quantity
                                                                <span class="required-star-dynamically"></span><br/>
                                                            </th>

                                                            <th class="text-center ">Quantity (A)
                                                                <span class="required-star-dynamically"></span><br/>
                                                            </th>

                                                            <th class="text-center " colspan="2">Unit Price (B)
                                                                <span class="required-star-dynamically"></span><br/>
                                                            </th>

                                                            <th class="text-center ">Price Foreign Currency (A X B)
                                                                <span class="required-star-dynamically"></span><br/>
                                                            </th>

                                                            <th class="text-center ">Price BDT (C)
                                                                <span class="required-star-dynamically"></span><br/>
                                                            </th>

                                                            <th class="text-center ">Value Taka (in million)
                                                                <span class="required-star-dynamically"></span><br/>
                                                            </th>
                                                            <th class="text-center ">#</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>

                                                        @if(count($existing_machines_lc)>0)
                                                            <?php $inc = 0; ?>
                                                            @foreach($existing_machines_lc as $existing_machines)

                                                                <tr id="existingMachinesLc{{$inc}}" data-number="1">
                                                                    <input type="hidden" name="em_lc_id[{{ $inc }}]" value="{{ $existing_machines->id }}">
                                                                    <td>
                                                                        {!! Form::text("em_product_name[$inc]", $existing_machines->product_name, ['class' => 'form-control input-md product em_product_name', 'id' => 'em_product_name']) !!}
                                                                    </td>

                                                                    <td width="12%">
                                                                        {!! Form::select("em_quantity_unit[$inc]", $productUnit, $existing_machines->quantity_unit, ['class'=>'form-control input-md em_quantity_unit', 'id' => 'em_quantity_unit']) !!}
                                                                    </td>

                                                                    <td width="8%">
                                                                        {!! Form::number("em_quantity[$inc]", $existing_machines->quantity, ['class' => 'form-control input-md product em_quantity', 'onkeyup' => "calculateLcForeignCurrency(this)", 'id' => 'em_quantity']) !!}
                                                                    </td>

                                                                    <td width="10%">
                                                                        {!! Form::number("em_unit_price[$inc]", $existing_machines->unit_price, ['class' => 'form-control input-md em_unit_price', 'onkeyup' => "calculateLcForeignCurrency(this)", 'id' => 'em_unit_price']) !!}
                                                                    </td>

                                                                    <td width="10%">
                                                                        {!! Form::select("em_price_unit[$inc]", $currencies, $existing_machines->price_unit, ['class'=>'form-control input-md em_price_unit', 'style' => 'width: 100%', 'id' => 'em_price_unit']) !!}
                                                                    </td>

                                                                    <td width="15%">
                                                                        {!! Form::number("em_price_foreign_currency[$inc]", $existing_machines->price_foreign_currency, ['class' => 'form-control input-md product em_price_foreign_currency', 'readonly', 'id' => 'em_price_foreign_currency']) !!}
                                                                    </td>

                                                                    <td>
                                                                        {!! Form::number("em_price_bdt[$inc]", $existing_machines->price_bdt, ['class' => 'form-control input-md em_price_bdt em_price_bdt', 'onkeyup' => "calculateListOfMachineryTotal('em_price_bdt', 'total_lc_price_bdt', this)", "id" => "em_price_bdt"]) !!}
                                                                    </td>

                                                                    <td>
                                                                        {!! Form::number("em_price_taka_mil[$inc]", $existing_machines->price_taka_mil, ['class' => 'form-control input-md em_price_taka_mil em_price_taka_mil', 'readonly', 'id' => 'em_price_taka_mil']) !!}
                                                                    </td>

                                                                    <td>
                                                                        <?php if ($inc == 0) { ?>
                                                                        <a class="btn btn-md btn-primary addTableRows"
                                                                           onclick="addTableRowForIRC('existingMachinesLcTbl', 'existingMachinesLc0');"><i
                                                                                    class="fa fa-plus"></i></a>
                                                                        <?php } else { ?>
                                                                        <a href="javascript:void(0);"
                                                                           class="btn btn-md btn-danger removeRow"
                                                                           onclick="removeTableRow('existingMachinesLcTbl','existingMachinesLc{{$inc}}');">
                                                                            <i class="fa fa-times"
                                                                               aria-hidden="true"></i></a>
                                                                        <?php } ?>
                                                                    </td>
                                                                </tr>
                                                                <?php $inc++; ?>
                                                            @endforeach
                                                        @else
                                                            <tr id="existingMachinesLc0" data-number="1">
                                                                <td>
                                                                    {!! Form::text("em_product_name[0]", '', ['class' => 'form-control input-md product em_product_name', 'id' => 'existing_lc']) !!}
                                                                </td>

                                                                <td width="12%">
                                                                    {!! Form::select('em_quantity_unit[0]', $productUnit, '', ['class'=>'form-control input-md em_quantity_unit', 'id' => 'em_quantity_unit']) !!}
                                                                </td>

                                                                <td width="8%">
                                                                    {!! Form::number("em_quantity[0]", '', ['class' => 'form-control input-md product em_quantity', 'onkeyup' => "calculateLcForeignCurrency(this)", 'id' => 'em_quantity']) !!}
                                                                </td>

                                                                <td width="10%">
                                                                    {!! Form::number("em_unit_price[0]", '', ['class' => 'form-control input-md em_unit_price', 'onkeyup' => "calculateLcForeignCurrency(this)", 'id' => 'em_unit_price']) !!}
                                                                </td>

                                                                <td width="10%">
                                                                    {!! Form::select('em_price_unit[0]', $currencies, '', ['class'=>'form-control input-md em_price_unit', "style" => "width: 100%", 'id' => 'em_price_unit']) !!}
                                                                </td>

                                                                <td width="15%">
                                                                    {!! Form::number("em_price_foreign_currency[0]", '', ['class' => 'form-control input-md product em_price_foreign_currency', 'readonly', 'id' => 'em_price_foreign_currency']) !!}
                                                                </td>

                                                                <td>
                                                                    {!! Form::number("em_price_bdt[0]", '', ['class' => 'form-control input-md em_price_bdt em_price_bdt', 'onkeyup' => "calculateListOfMachineryTotal('em_price_bdt', 'total_lc_price_bdt', this)", 'id' => 'em_price_taka_mil']) !!}
                                                                </td>

                                                                <td>
                                                                    {!! Form::number("em_price_taka_mil[0]", '', ['class' => 'form-control input-md em_price_taka_mil em_price_taka_mil', 'readonly', 'id' => 'em_price_taka_mil']) !!}
                                                                </td>

                                                                <td>
                                                                    <a class="btn btn-md btn-primary addTableRows"
                                                                       onclick="addTableRowForIRC('existingMachinesLcTbl', 'existingMachinesLc0');"><i
                                                                                class="fa fa-plus"></i></a>
                                                                </td>
                                                            </tr>
                                                        @endif
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
                                                            <th class="text-center">#</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>

                                                        @if(count($existing_machines_local)>0)
                                                            <?php $inc = 0; ?>
                                                            @foreach($existing_machines_local as $existing_machines_local)

                                                                <tr id="existingMachinesLocal{{$inc}}" data-number="1">
                                                                    <input type="hidden" name="em_local_id[{{ $inc }}]" value="{{ $existing_machines_local->id }}">
                                                                    <td width="28%">
                                                                        {!! Form::text("em_local_product_name[$inc]", $existing_machines_local->product_name, ['class' => 'form-control input-md product em_local_product_name', 'id' => 'em_local_product_name']) !!}
                                                                    </td>

                                                                    <td width="12%">
                                                                        {!! Form::select("em_local_quantity_unit[$inc]", $productUnit, $existing_machines_local->quantity_unit, ['class'=>'form-control input-md em_local_quantity_unit', 'id' => 'em_local_quantity_unit']) !!}
                                                                    </td>

                                                                    <td width="10%">
                                                                        {!! Form::number("em_local_quantity[$inc]", $existing_machines_local->quantity, ['class' => 'form-control input-md product em_local_quantity', 'onkeyup' => "calculateLocalPrice(this)", 'id' => 'em_local_quantity']) !!}
                                                                    </td>

                                                                    <td width="10%">
                                                                        {!! Form::number("em_local_unit_price[$inc]", $existing_machines_local->unit_price, ['class' => 'form-control input-md em_local_unit_price', 'onkeyup' => "calculateLocalPrice(this)", 'id' => 'em_local_unit_price']) !!}
                                                                    </td>

                                                                    <td>
                                                                        {!! Form::number("em_local_price_bdt[$inc]", $existing_machines_local->price_bdt, ['class' => 'form-control input-md number em_local_price_bdt', 'readonly', 'id' => 'em_local_price_bdt']) !!}
                                                                    </td>

                                                                    <td>
                                                                        {!! Form::number("em_local_price_taka_mil[$inc]", $existing_machines_local->price_taka_mil, ['class' => 'form-control input-md em_local_price_taka_mil em_local_price_taka_mil', 'onkeyup' => "calculateListOfMachineryTotal('em_local_price_taka_mil', 'em_local_total_taka_mil')", 'id' => 'em_local_price_taka_mil']) !!}
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
                                                            <tr id="existingMachinesLocal0" data-number="1">
                                                                <td width="28%">
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
                                                                    {!! Form::number("em_local_price_taka_mil[0]", '', ['class' => 'form-control input-md em_local_price_taka_mil em_local_price_taka_mil', 'onkeyup' => "calculateListOfMachineryTotal('em_local_price_taka_mil', 'em_local_total_taka_mil')", 'id' => 'em_local_price_taka_mil']) !!}
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
                                                            <td colspan="2">{!! Form::text("em_local_total_taka_mil", '', ['class' => 'form-control input-md', 'id'=>'em_local_total_taka_mil', 'readonly']) !!}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </fieldset>

                                        </fieldset>

                                        <fieldset class="scheduler-border"> 
                                            <legend class="scheduler-border">9. Value of the Existing Machineries</legend>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                    <thead class="alert alert-info">
                                                    <tr>
                                                        <th class="v-align-middle">Imported Value BDT</th>
                                                        <th class="v-align-middle">Local Value BDT</th>
                                                        <th class="v-align-middle">Total Value BDT</th>
                                                        <th class="v-align-middle">
                                                            Attachment
                                                            <br/>
                                                            <span class="text-danger" style="font-size: 9px; font-weight: bold">[Format: *.pdf | Max File size 2MB]</span>
                                                            <br/>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td>
                                                            {!! Form::text('ex_machine_imported_value_bdt',$appInfo->ex_machine_imported_value_bdt,['class' => 'form-control input-md', 'id' => 'ex_machine_imported_value_bdt', 'onkeyup' => "calculateImportedAndLocalVaule()"]) !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('ex_machine_local_value_bdt',$appInfo->ex_machine_local_value_bdt,['class' => 'form-control input-md', 'id' => 'ex_machine_local_value_bdt', 'onkeyup' => "calculateImportedAndLocalVaule()"]) !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('ex_machine_total_value_bdt',$appInfo->ex_machine_total_value_bdt,['class' => 'form-control input-md', 'id' => 'ex_machine_total_value_bdt', 'readonly']) !!}
                                                        </td>
                                                        <td>
                                                            @if(!empty($appInfo->ex_machine_attachment))
                                                                <a target="_blank" class="Url btn btn-xs btn-primary"
                                                                   href="{{URL::to('/uploads/'.(!empty($appInfo->ex_machine_attachment) ? $appInfo->ex_machine_attachment : ''))}}">
                                                                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                                                    Open File
                                                                </a>
                                                            @endif
                                                            <input type="file" name="ex_machine_attachment" value="{{$appInfo->ex_machine_attachment}}">
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </fieldset>

                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">10. Imported Raw materials/ Packaging Materials/ Spare parts details</legend>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                    <thead class="alert alert-info">
                                                    <tr>
                                                        <th colspan="2" class="v-align-middle">Duration</th>
                                                        <th class="v-align-middle">Total Price (USD)</th>
                                                        <th class="v-align-middle">Total Price (BDT)</th>
                                                        <th class="v-align-middle">
                                                            Attachment
                                                            <br/>
                                                            <span class="text-danger" style="font-size: 9px; font-weight: bold">[Format: *.pdf | Max File size 2MB]</span>
                                                            <br/>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td>
                                                            <div>
                                                                From {!! Form::text('import_duration_from_date', (empty($appInfo->import_duration_from_date)) ? '' : \Carbon\Carbon::parse($appInfo->import_duration_from_date)->format('M-Y'), ['class'=>'form-control input-md monthpicker', 'id' => 'irc_date', 'placeholder'=>'Pick from datepicker']) !!}</div>
                                                        </td>
                                                        <td>
                                                            <div>
                                                                To {!! Form::text('import_duration_to_date', (empty($appInfo->import_duration_to_date)) ? '' : \Carbon\Carbon::parse($appInfo->import_duration_to_date)->format('M-Y'), ['class'=>'form-control input-md monthpicker', 'id' => 'irc_date', 'placeholder'=>'Pick from datepicker']) !!}</div>
                                                        </td>
                                                        <td>
                                                            {!! Form::text('import_total_price_usd',$appInfo->import_total_price_usd,['class' => 'form-control input-md']) !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('import_total_price_bdt',$appInfo->import_total_price_bdt,['class' => 'form-control input-md']) !!}
                                                        </td>
                                                        <td>
                                                            @if(!empty($appInfo->import_attachment))
                                                                <a target="_blank" class="documentUrl btn btn-xs btn-primary"
                                                                   href="{{URL::to('/uploads/'.(!empty($appInfo->import_attachment) ? $appInfo->import_attachment : ''))}}">
                                                                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                                                    Open File
                                                                </a>
                                                            @endif
                                                            <input type="file" name="import_attachment" value="{{$appInfo->import_attachment}}">
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </fieldset>

                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">11. Production Details</legend>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                    <thead class="alert alert-info">
                                                    <tr>
                                                        <th colspan="2" class="v-align-middle">Duration</th>
                                                        <th class="v-align-middle">Total Quantity (a)</th>
                                                        <th class="v-align-middle">Total Sales (b)</th>
                                                        <th class="v-align-middle">Total Stock (a-b)</th>
                                                        <th class="v-align-middle">
                                                            Attachment
                                                            <br/>
                                                            <span class="text-danger" style="font-size: 9px; font-weight: bold">[Format: *.pdf | Max File size 2MB]</span>
                                                            <br/>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td>
                                                            <div>
                                                                From {!! Form::text('production_duration_from_date', (empty($appInfo->production_duration_from_date)) ? '' : \Carbon\Carbon::parse($appInfo->production_duration_from_date)->format('M-Y'), ['class'=>'form-control input-md monthpicker', 'id' => 'irc_date', 'placeholder'=>'Pick from datepicker']) !!}</div>
                                                        </td>
                                                        <td>
                                                            <div>
                                                                To {!! Form::text('production_duration_to_date', (empty($appInfo->production_duration_to_date)) ? '' : \Carbon\Carbon::parse($appInfo->production_duration_to_date)->format('M-Y'), ['class'=>'form-control input-md monthpicker', 'id' => 'irc_date', 'placeholder'=>'Pick from datepicker']) !!}</div>
                                                        </td>
                                                        <td>
                                                            {!! Form::text('production_total_quantity',$appInfo->production_total_quantity,['class' => 'form-control input-md', 'id' => 'production_total_quantity', 'onkeyup' => "calculateProductionStock()"]) !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('production_total_sales',$appInfo->production_total_sales,['class' => 'form-control input-md', 'id' => 'production_total_sales', 'onkeyup' => "calculateProductionStock()"]) !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('production_total_stock',$appInfo->production_total_stock,['class' => 'form-control input-md', 'id' => 'production_total_stock', 'readonly']) !!}
                                                        </td>
                                                        <td>
                                                            @if(!empty($appInfo->production_attachment))
                                                                <a target="_blank" class="documentUrl btn btn-xs btn-primary"
                                                                   href="{{URL::to('/uploads/'.(!empty($appInfo->production_attachment) ? $appInfo->production_attachment : ''))}}">
                                                                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                                                    Open File
                                                                </a>
                                                            @endif
                                                            <input type="file" name="production_attachment" value="{{$appInfo->production_attachment}}">
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </fieldset>

                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">12. Sales Statement</legend>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="saleStatementTbl">
                                                    <thead class="alert alert-info">
                                                    <tr>
                                                        <th class="table-header v-align-middle" colspan="2">Month & Year</th>
                                                        <th class="table-header v-align-middle">Sales Value (BDT)</th>
                                                        <th class="table-header v-align-middle">VAT (BDT)</th>
                                                        <th class="table-header v-align-middle">Attachment</th>
                                                        <th class="table-header v-align-middle">#</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if(count($salesStatement) > 0)
                                                        <?php $count = 0; ?>
                                                        @foreach($salesStatement as $salesStatement)
                                                            <tr id="saleStatementTblRow{{ $count }}" data-number="1"
                                                                class="table-tr">
                                                                {!! Form::hidden("sales_statement_id[$count]", $salesStatement->id) !!}
                                                                <td>
                                                                    From {!! Form::text("sales_statement_from_date[$count]", (empty($salesStatement->sales_statement_from_date)) ? '' : date('M-Y', strtotime($salesStatement->sales_statement_from_date)), ['class'=>'form-control input-md monthpicker', 'id' => '', 'placeholder'=>'Pick from datepicker']) !!}
                                                                </td>
                                                                <td>
                                                                    To {!! Form::text("sales_statement_to_date[$count]", (empty($salesStatement->sales_statement_to_date)) ? '' : date('M-Y', strtotime($salesStatement->sales_statement_to_date)), ['class'=>'form-control input-md monthpicker', 'id' => '', 'placeholder'=>'Pick from datepicker']) !!}
                                                                </td>
                                                                <td>
                                                                    {!! Form::text("sales_value_bdt[$count]",$salesStatement->sales_value_bdt,['class' => 'form-control input-md sales_value_bdt', 'onkeyup' => "calculateAddRowTotal('sales_value_bdt', 'sales_value_bdt_total')"]) !!}
                                                                </td>
                                                                <td>
                                                                    {!! Form::text("sales_vat_bdt[$count]",$salesStatement->sales_vat_bdt,['class' => 'form-control input-md sales_vat_bdt', 'onkeyup' => "calculateAddRowTotal('sales_vat_bdt', 'sales_vat_total')"]) !!}
                                                                </td>
                                                                
                                                                <td>
                                                                    <input type="file" id="sales_attachment" name="sales_attachment[{{ $count }}]" class="form-control input-md sales_attachment" />
                                                                    {!! $errors->first('sales_attachment[]', '<span class="help-block">:message</span>') !!}
                                                                    @if(!empty($salesStatement->sales_attachment))
                                                                        <a target="_blank" class="btn btn-xs btn-primary documentUrl"
                                                                            href="{{ URL::to('/uploads/'.(!empty($salesStatement->sales_attachment) ? $salesStatement->sales_attachment : '')) }}"
                                                                            title="{{ $salesStatement->sales_attachment }}"
                                                                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                                                            Open File
                                                                        </a>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                   @if($count == 0)
                                                                    <a class="btn btn-md btn-primary addTableRows"
                                                                       onclick="addTableRowForIRC('saleStatementTbl', 'saleStatementTblRow0');">
                                                                       <i class="fa fa-plus"></i>
                                                                    </a>
                                                                    @else
                                                                    <a href="javascript:void(0);" class="btn btn-md btn-danger removeRow"
                                                                       onclick="removeTableRow('saleStatementTbl','saleStatementTblRow{{$count}}');">
                                                                        <i class="fa fa-times" aria-hidden="true"></i>
                                                                    </a>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <?php $count++; ?>
                                                        @endforeach
                                                    @else
                                                        <tr id="saleStatementTblRow0" data-number="1" class="table-tr">
                                                            <td>
                                                                From {!! Form::text('sales_statement_from_date[]', '', ['class'=>'form-control input-md monthpicker sales_statement_from_date', 'id' => '', 'placeholder'=>'Pick from datepicker']) !!}
                                                            </td>
                                                            <td>
                                                                To {!! Form::text('sales_statement_to_date[]', '', ['class'=>'form-control input-md monthpicker sales_statement_to_date', 'id' => '', 'placeholder'=>'Pick from datepicker']) !!}
                                                            </td>
                                                            <td>
                                                                {!! Form::text('sales_value_bdt[]','',['class' => 'form-control input-md sales_value_bdt', 'onkeyup' => "calculateAddRowTotal('sales_value_bdt', 'sales_value_bdt_total')"]) !!}
                                                            </td>
                                                            <td>
                                                                {!! Form::text('sales_vat_bdt[]','',['class' => 'form-control input-md sales_vat_bdt', 'onkeyup' => "calculateAddRowTotal('sales_vat_bdt', 'sales_vat_total')"]) !!}
                                                            </td>
                                                            <td>
                                                                <input type="file" id="sales_attachment" name="sales_attachment[]" class="form-control input-md sales_attachment" />
                                                                {!! $errors->first('sales_attachment','<span class="help-block">:message</span>') !!}
                                                            </td>
                                                            <td>
                                                                <a class="btn btn-md btn-primary addTableRows"
                                                                   onclick="addTableRowForIRC('saleStatementTbl', 'saleStatementTblRow0');">
                                                                   <i class="fa fa-plus"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    </tbody>
                                                    <tr>
                                                        <td class="text-right">Total:</td>
                                                        <td>{!! Form::text('sales_value_bdt_total',$appInfo->sales_value_bdt_total,['class' => 'form-control input-md', 'id'=>'sales_value_bdt_total', 'readonly']) !!}</td>
                                                        <td colspan="2">{!! Form::text('sales_vat_total',$appInfo->sales_vat_total,['class' => 'form-control input-md', 'id'=>'sales_vat_total', 'readonly']) !!}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </fieldset>

                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">13. Export Statement</legend>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered" cellspacing="0"
                                                       width="100%">
                                                    <thead class="alert alert-info">
                                                    <tr>
                                                        <th colspan="2" class="v-align-middle">Duration</th>
                                                        <th class="v-align-middle">Total Price (USD)</th>
                                                        <th class="v-align-middle">Total Price (BDT)</th>
                                                        <th class="v-align-middle">
                                                            Attachment
                                                            <br/>
                                                            <span class="text-danger" style="font-size: 9px; font-weight: bold">[Format: *.pdf | Max File size 2MB]</span>
                                                            <br/>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td>
                                                            <div>
                                                                From {!! Form::text('export_duration_from_date', (empty($appInfo->export_duration_from_date)) ? '' : \Carbon\Carbon::parse($appInfo->export_duration_from_date)->format('M-Y'), ['class'=>'form-control input-md monthpicker', 'id' => '', 'placeholder'=>'Pick from datepicker']) !!}</div>
                                                        </td>
                                                        <td>
                                                            <div>
                                                                To {!! Form::text('export_duration_to_date', (empty($appInfo->export_duration_to_date)) ? '' : \Carbon\Carbon::parse($appInfo->export_duration_to_date)->format('M-Y'), ['class'=>'form-control input-md monthpicker', 'id' => '', 'placeholder'=>'Pick from datepicker']) !!}</div>
                                                        </td>
                                                        <td>
                                                            {!! Form::text('export_total_price_usd',$appInfo->export_total_price_usd,['class' => 'form-control input-md']) !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('export_total_price_bdt',$appInfo->export_total_price_bdt,['class' => 'form-control input-md']) !!}
                                                        </td>
                                                        <td>
                                                            @if(!empty($appInfo->export_attachment))
                                                                <a target="_blank" class="documentUrl btn btn-xs btn-primary"
                                                                   href="{{URL::to('/uploads/'.(!empty($appInfo->export_attachment) ? $appInfo->export_attachment : ''))}}">
                                                                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                                                    Open File
                                                                </a>
                                                            @endif
                                                            <input type="file" name="export_attachment" value="{{$appInfo->export_attachment}}">
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </fieldset>

                                        {{-- 13. ‍A. According to the latest ad-hoc IRC --}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">14. ‍A. According to the latest ad-hoc IRC</legend>
                                            <fieldset class="scheduler-border" id="annual_ins_raw">
                                                <legend class="scheduler-border">For Raw Materials</legend>
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="insProductionRawTbl">
                                                        <thead class="alert alert-info">
                                                        <tr>
                                                            <th>Product Name</th>
                                                            <th>Yearly production</th>
                                                            {{-- <th colspan="2">Half yearly production</th> --}}
                                                            <th>Half yearly production</th>
                                                            <th>Half year amount (BDT)</th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @if(count($six_months_import_raw)>0)
                                                            <?php $count = 0; ?>
                                                            @foreach($six_months_import_raw as $apc)
                                                                <tr id="insProRawCostCount{{$count}}">
                                                                    <input type="hidden" name="ins_apc_id[{{ $count }}]" value="{{ $apc->id }}">
                                                                    <td>
                                                                        {!! Form::text("ins_apc_product_name[$count]",$apc->product_name,['class' => 'form-control input-md']) !!}
                                                                    </td>
                                                                    <td>
                                                                        {!! Form::text("ins_apc_yearly_production[$count]",$apc->yearly_production,['class' => 'form-control input-md', 'onkeyup' => "calculateHalfYearlyProduction(this)"]) !!}
                                                                    </td>
                                                                    <td>
                                                                        {!! Form::text("ins_apc_half_yearly_production[$count]",$apc->half_yearly_production,['class' => 'form-control input-md', 'readonly']) !!}
                                                                    </td>
                                                                    {{-- <td>
                                                                        {!! Form::select("ins_apc_quantity_unit[$count]",$productUnit,$apc->quantity_unit,['class'=>'form-control input-md', 'id' => '']) !!}
                                                                    </td> --}}
                                                                    <td>
                                                                        {!! Form::text("ins_apc_half_yearly_import[$count]",$apc->half_yearly_import,['class' => 'form-control input-md ins_apc_half_yearly_import',
                                                                        'onkeyup' => "calculateAddRowTotal('ins_apc_half_yearly_import', 'ins_apc_half_yearly_import_total')"]) !!}
                                                                    </td>
                                                                    <td>
                                                                        @if($count == 0)
                                                                        <a class="btn btn-md btn-primary addTableRows"
                                                                           onclick="addTableRowForIRC('insProductionRawTbl', 'insProRawCostCount0');">
                                                                           <i class="fa fa-plus"></i>
                                                                        </a>
                                                                        @else
                                                                        <a href="javascript:void(0);" class="btn btn-md btn-danger removeRow"
                                                                           onclick="removeTableRow('insProductionRawTbl','insProRawCostCount{{$count}}');">
                                                                            <i class="fa fa-times" aria-hidden="true"></i>
                                                                        </a>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                <?php $count++ ?>
                                                            @endforeach
                                                        @else
                                                            <tr id="insProRawCostCount0">
                                                                <td>
                                                                    {!! Form::text('ins_apc_product_name[0]','',['class' => 'form-control input-md']) !!}
                                                                </td>
                                                                <td>
                                                                    {!! Form::text('ins_apc_yearly_production[0]','',['class' => 'form-control input-md', 'onkeyup' => "calculateHalfYearlyProduction(this)"]) !!}
                                                                </td>
                                                                <td>
                                                                    {!! Form::text('ins_apc_half_yearly_production[0]','',['class' => 'form-control input-md', 'readonly']) !!}
                                                                </td>
                                                                {{-- <td>
                                                                    {!! Form::select("ins_apc_quantity_unit[0]",$productUnit,'',['class'=>'form-control input-md', 'id' => '']) !!}
                                                                </td> --}}
                                                                <td>
                                                                    {!! Form::text('ins_apc_half_yearly_import[0]','',['class' => 'form-control input-md ins_apc_half_yearly_import',
                                                                    'onkeyup' => "calculateAddRowTotal('ins_apc_half_yearly_import', 'ins_apc_half_yearly_import_total')"]) !!}
                                                                </td>
                                                                <td>
                                                                    <a class="btn btn-md btn-primary addTableRows"
                                                                       onclick="addTableRowForIRC('insProductionRawTbl', 'insProRawCostCount0');">
                                                                        <i class="fa fa-plus"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <table class="table" width="100%">
                                                    <tr>
                                                        <td colspan="2" class="text-right">Total import capacity for raw materials</td>
                                                        <td>{!! Form::text('ins_apc_half_yearly_import_total',$appInfo->ins_apc_half_yearly_import_total, ['class' => 'form-control input-md', 'id'=>'ins_apc_half_yearly_import_total', 'readonly']) !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-right">Total import capacity for raw materials (in words)</td>
                                                        <td colspan="2">{!! Form::text('ins_apc_half_yearly_import_total_in_word',$appInfo->ins_apc_half_yearly_import_total_in_word? $appInfo->ins_apc_half_yearly_import_total_in_word: '',['class' => 'form-control input-md']) !!}</td>
                                                    </tr>
                                                </table>

                                            </fieldset>

                                            <fieldset class="scheduler-border" id="adhoc_based_spare_parts" style="display: none">
                                                <legend class="scheduler-border"><span>Half-yearly import rights of adhoc based spare parts/ demand</span></legend>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="form-group" style="margin-top: 2px">
                                                            <div class="col-md-6 {{$errors->has('first_em_lc_total_five_percent') ? 'has-error': ''}}">
                                                                {!! Form::label('first_em_lc_total_five_percent','Value In BDT',['class'=>'text-left col-md-3']) !!}
                                                                <div class="col-md-9">
                                                                    {!! Form::number('first_em_lc_total_five_percent', !empty($appInfo->first_em_lc_total_five_percent) ? intval(preg_replace('/[^\d.]/', '', $appInfo->first_em_lc_total_five_percent)) : "", ['class' => 'form-control input-md first_em_lc_total_five_percent']) !!}
                                                                    {!! $errors->first('first_em_lc_total_five_percent','<span class="help-block">:message</span>') !!}
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6 form-group {{$errors->has('first_em_lc_total_five_percent_in_word') ? 'has-error': ''}}">
                                                                {!! Form::label('first_em_lc_total_five_percent_in_word','in word',['class'=>'col-md-2 text-left ']) !!}
                                                                <div class="col-md-10">
                                                                    {!! Form::text('first_em_lc_total_five_percent_in_word', !empty($appInfo->first_em_lc_total_five_percent_in_word) ? $appInfo->first_em_lc_total_five_percent_in_word : "", ['class' => 'form-control input-md first_em_lc_total_five_percent_in_word']) !!}
                                                                    {!! $errors->first('first_em_lc_total_five_percent_in_word','<span class="help-block">:message</span>') !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                   
                                                    <br>
                                                    <div class="row">
                                                        <div class="form-group">
                                                            <div class="col-md-12">
                                                                {!! Form::label('','Half-yearly import rights can be fixed for spare parts.',['class'=>'text-left']) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>

                                            <fieldset class="scheduler-border" id="annual_ins_both" style="display: none">
                                                <legend class="scheduler-border">Grand Total : Raw Materials & spare parts/ demand </legend>

                                                <table class="table" width="100%">
                                                    <tr>
                                                        <td colspan="2" class="text-right">Grand total</td>
                                                        <td>{!! Form::number('import_cap_grd_total',$appInfo->import_cap_grd_total,['class' => 'form-control input-md import_cap_grd_total', 'id'=>'import_cap_grd_total']) !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-right">Grand total (in words)</td>
                                                        <td colspan="2">{!! Form::text('import_cap_grd_total_wrd',$appInfo->import_cap_grd_total_wrd,['class' => 'form-control input-md import_cap_grd_total_wrd','id'=>'import_cap_grd_total_wrd']) !!}</td>
                                                    </tr>
                                                </table>
                                            </fieldset>
                                        </fieldset>


                                        {{-- 13. B. According to the latest ad-hoc IRC Amendment Information --}}
                                        <fieldset class="scheduler-border {{($appInfo->irc_regular_purpose_id == '1') ? 'hidden': ''}}" id="annual_amendment_info">
                                            <legend class="scheduler-border">14. B. According to the latest ad-hoc IRC Amendment Information</legend>
                                            <fieldset class="scheduler-border" id="n_annual_ins_raw">
                                                <legend class="scheduler-border">For Raw Materials</legend>
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="n_insProductionRawTbl">
                                                        <thead class="alert alert-info">
                                                        <tr>
                                                            <th>Product Name</th>
                                                            <th>Yearly production</th>
                                                            {{-- <th colspan="2">Half yearly production</th> --}}
                                                            <th>Half yearly production</th>
                                                            <th>Half year amount (BDT)</th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @if(count($six_months_import_raw_amendment)>0)
                                                            <?php $count = 0; ?>
                                                            @foreach($six_months_import_raw_amendment as $apc)
                                                                <tr id="n_insProRawCostCount{{$count}}">
                                                                    <input type="hidden" name="n_ins_apc_id[{{ $count }}]" value="{{ $apc->id }}">
                                                                    <td>
                                                                        {!! Form::text("n_ins_apc_product_name[$count]",$apc->n_product_name,['class' => 'form-control input-md']) !!}
                                                                    </td>
                                                                    <td>
                                                                        {!! Form::text("n_ins_apc_yearly_production[$count]",$apc->n_yearly_production,['class' => 'form-control input-md', 'onkeyup' => "calculateHalfYearlyProduction(this)"]) !!}
                                                                    </td>
                                                                    <td>
                                                                        {!! Form::text("n_ins_apc_half_yearly_production[$count]",$apc->n_half_yearly_production,['class' => 'form-control input-md', 'readonly']) !!}
                                                                    </td>
                                                                    {{-- <td>
                                                                        {!! Form::select("n_ins_apc_quantity_unit[$count]",$productUnit,$apc->n_quantity_unit,['class'=>'form-control input-md', 'id' => '']) !!}
                                                                    </td> --}}
                                                                    <td>
                                                                        {!! Form::text("n_ins_apc_half_yearly_import[$count]",$apc->n_half_yearly_import,['class' => 'form-control input-md n_ins_apc_half_yearly_import',
                                                                        'onkeyup' => "calculateAddRowTotal('n_ins_apc_half_yearly_import', 'n_ins_apc_half_yearly_import_total')"]) !!}
                                                                    </td>
                                                                    <td>
                                                                        @if($count == 0)
                                                                        <a class="btn btn-md btn-primary addTableRows"
                                                                            onclick="addTableRowForIRC('n_insProductionRawTbl', 'n_insProRawCostCount0');">
                                                                            <i class="fa fa-plus"></i>
                                                                        </a>
                                                                        @else
                                                                        <a href="javascript:void(0);" class="btn btn-md btn-danger removeRow"
                                                                            onclick="removeTableRow('n_insProductionRawTbl','n_insProRawCostCount{{$count}}');">
                                                                            <i class="fa fa-times" aria-hidden="true"></i>
                                                                        </a>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                <?php $count++ ?>
                                                            @endforeach
                                                        @else
                                                            <tr id="n_insProRawCostCount0">
                                                                <td>
                                                                    {!! Form::text('n_ins_apc_product_name[0]','',['class' => 'form-control input-md']) !!}
                                                                </td>
                                                                <td>
                                                                    {!! Form::text('n_ins_apc_yearly_production[0]','',['class' => 'form-control input-md', 'onkeyup' => "calculateHalfYearlyProduction(this)"]) !!}
                                                                </td>
                                                                <td>
                                                                    {!! Form::text('n_ins_apc_half_yearly_production[0]','',['class' => 'form-control input-md', 'readonly']) !!}
                                                                </td>
                                                                {{-- <td>
                                                                    {!! Form::select("n_ins_apc_quantity_unit[0]",$productUnit,'',['class'=>'form-control input-md', 'id' => '']) !!}
                                                                </td> --}}
                                                                <td>
                                                                    {!! Form::text('n_ins_apc_half_yearly_import[0]','',['class' => 'form-control input-md n_ins_apc_half_yearly_import',
                                                                    'onkeyup' => "calculateAddRowTotal('n_ins_apc_half_yearly_import', 'n_ins_apc_half_yearly_import_total')"]) !!}
                                                                </td>
                                                                <td>
                                                                    <a class="btn btn-md btn-primary addTableRows"
                                                                        onclick="addTableRowForIRC('n_insProductionRawTbl', 'n_insProRawCostCount0');">
                                                                        <i class="fa fa-plus"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <table class="table" width="100%">
                                                    <tr>
                                                        <td colspan="2" class="text-right">Total import capacity for raw materials</td>
                                                        <td>{!! Form::text('n_ins_apc_half_yearly_import_total',$appInfo->n_ins_apc_half_yearly_import_total, ['class' => 'form-control input-md', 'id'=>'n_ins_apc_half_yearly_import_total', 'readonly']) !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-right">Total import capacity for raw materials (in words)</td>
                                                        <td colspan="2">{!! Form::text('n_ins_apc_half_yearly_import_total_in_word',$appInfo->n_ins_apc_half_yearly_import_total_in_word,['class' => 'form-control input-md']) !!}</td>
                                                    </tr>
                                                </table>

                                            </fieldset>

                                            <fieldset class="scheduler-border" id="n_adhoc_based_spare_parts" style="display: none">
                                                <legend class="scheduler-border"><span>Half-yearly import rights of adhoc based spare parts/ demand</span></legend>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="form-group" style="margin-top: 2px">
                                                            <div class="col-md-6 {{$errors->has('n_first_em_lc_total_five_percent') ? 'has-error': ''}}">
                                                                {!! Form::label('n_first_em_lc_total_five_percent','Value In BDT',['class'=>'text-left col-md-3']) !!}
                                                                <div class="col-md-9">
                                                                    {!! Form::number('n_first_em_lc_total_five_percent', !empty($appInfo->n_first_em_lc_total_five_percent) ? intval(preg_replace('/[^\d.]/', '', $appInfo->n_first_em_lc_total_five_percent)) : "", ['class' => 'form-control input-md n_first_em_lc_total_five_percent']) !!}
                                                                    {!! $errors->first('n_first_em_lc_total_five_percent','<span class="help-block">:message</span>') !!}
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6 form-group {{$errors->has('n_first_em_lc_total_five_percent_in_word') ? 'has-error': ''}}">
                                                                {!! Form::label('n_first_em_lc_total_five_percent_in_word','in word',['class'=>'col-md-2 text-left ']) !!}
                                                                <div class="col-md-10">
                                                                    {!! Form::text('n_first_em_lc_total_five_percent_in_word', !empty($appInfo->n_first_em_lc_total_five_percent_in_word) ? $appInfo->n_first_em_lc_total_five_percent_in_word : "", ['class' => 'form-control input-md n_first_em_lc_total_five_percent_in_word']) !!}
                                                                    {!! $errors->first('n_first_em_lc_total_five_percent_in_word','<span class="help-block">:message</span>') !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                   
                                                    <br>
                                                    <div class="row">
                                                        <div class="form-group">
                                                            <div class="col-md-12">
                                                                {!! Form::label('','Half-yearly import rights can be fixed for spare parts.',['class'=>'text-left']) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>

                                            <fieldset class="scheduler-border" id="n_annual_ins_both" style="display: none">
                                                <legend class="scheduler-border">Grand Total : Raw Materials & spare parts/ demand </legend>

                                                <table class="table" width="100%">
                                                    <tr>
                                                        <td colspan="2" class="text-right">Grand total</td>
                                                        <td>{!! Form::number('n_import_cap_grd_total',$appInfo->n_import_cap_grd_total,['class' => 'form-control input-md n_import_cap_grd_total', 'id'=>'n_import_cap_grd_total']) !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-right">Grand total (in words)</td>
                                                        <td colspan="2">{!! Form::text('n_import_cap_grd_total_wrd',$appInfo->n_import_cap_grd_total_wrd,['class' => 'form-control input-md n_import_cap_grd_total_wrd','id'=>'n_import_cap_grd_total_wrd']) !!}</td>
                                                    </tr>
                                                </table>
                                            </fieldset>
                                        </fieldset>

                                        {{--14. Public utility service required--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">15. Public utility service required</legend>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_land"
                                                                   @if($appInfo->public_land == 1) checked="checked"
                                                                   @endif value="Land">Land
                                                        </label>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_electricity"
                                                                   @if($appInfo->public_electricity == 1) checked="checked"
                                                                   @endif value="Electricity">Electricity
                                                        </label>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_gas"
                                                                   @if($appInfo->public_gas == 1) checked="checked"
                                                                   @endif value="Gas">Gas
                                                        </label>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_telephone"
                                                                   @if($appInfo->public_telephone == 1) checked="checked"
                                                                   @endif value="Telephone">Telephone
                                                        </label>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_road"
                                                                   @if($appInfo->public_road == 1) checked="checked"
                                                                   @endif value="Road">Road
                                                        </label>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_water"
                                                                   @if($appInfo->public_water == 1) checked="checked"
                                                                   @endif value="Water">Water
                                                        </label>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_drainage"
                                                                   @if($appInfo->public_drainage == 1) checked="checked"
                                                                   @endif value="Drainage">Drainage
                                                        </label>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox other_utility" id="public_others" name="public_others"
                                                                   @if($appInfo->public_others == 1) checked="checked"
                                                                   @endif value="Others">Others
                                                        </label>
                                                    </div>
                                                    <div class="col-md-12" hidden style="margin-top: 5px;" id="public_others_field_div">
                                                        {!! Form::text('public_others_field', $appInfo->public_others_field, ['placeholder'=>'Specify others', 'class' => 'form-control input-md', 'id' => 'public_others_field']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--15. Trade license details--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">16. Trade license details</legend>
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('trade_licence_num') ? 'has-error': ''}}">
                                                        {!! Form::label('trade_licence_num','Trade License Number',['class'=>'text-left col-md-5 required-star']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('trade_licence_num', $appInfo->trade_licence_num, ['class' => 'form-control input-md required']) !!}
                                                            {!! $errors->first('trade_licence_num','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group {{$errors->has('trade_licence_issuing_authority') ? 'has-error': ''}}">
                                                        {!! Form::label('trade_licence_issuing_authority','Issuing Authority',['class'=>'col-md-5 text-left ']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('trade_licence_issuing_authority', $appInfo->trade_licence_issuing_authority, ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('trade_licence_issuing_authority','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('trade_licence_issue_date') ? 'has-error': ''}}">
                                                        {!! Form::label('trade_licence_issue_date','Issue Date',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                                {!! Form::text('trade_licence_issue_date', (empty($appInfo->trade_licence_issue_date)) ? '' : date('d-M-Y', strtotime($appInfo->trade_licence_issue_date)), ['class'=>'form-control input-md datepicker', 'id' => 'trade_licence_issue_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            </div>
                                                            {!! $errors->first('trade_licence_issue_date','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group {{$errors->has('trade_licence_validity_period') ? 'has-error': ''}}">
                                                        {!! Form::label('trade_licence_validity_period','Validity Period',['class'=>'col-md-5 text-left ']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('trade_licence_validity_period', $appInfo->trade_licence_validity_period, ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('trade_licence_validity_period','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--16. Incorporation--}}
                                        <fieldset class="scheduler-border" id="incorporation">
                                            <legend class="scheduler-border">17. Incorporation Certificate Details</legend>
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('inc_number') ? 'has-error': ''}}">
                                                        {!! Form::label('inc_number','Incorporation Number',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('inc_number', $appInfo->inc_number, ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('inc_number','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 {{$errors->has('inc_issuing_authority') ? 'has-error': ''}}">
                                                        {!! Form::label('inc_issuing_authority','Issuing Authority',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('inc_issuing_authority', $appInfo->inc_issuing_authority, ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('inc_issuing_authority','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--17. TIN--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">18. Tex Identification Number (TIN) Details</legend>
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('tin_number') ? 'has-error': ''}}">
                                                        {!! Form::label('tin_number','TIN Number',['class'=>'text-left col-md-5 required-star']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('tin_number', $appInfo->tin_number, ['class' => 'form-control input-md required']) !!}
                                                            {!! $errors->first('tin_number','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 {{$errors->has('tin_issuing_authority') ? 'has-error': ''}}">
                                                        {!! Form::label('tin_issuing_authority','Issuing Authority',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('tin_issuing_authority', $appInfo->tin_issuing_authority, ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('tin_issuing_authority','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--18. Fire license info--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">19. Fire License Information Details</legend>

                                            <div class="row">
                                                <div class="form-group" id="already_have_license_div">
                                                    <div class="col-md-6 {{$errors->has('fl_number') ? 'has-error': ''}}">
                                                        {!! Form::label('fl_number','Fire License Number',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('fl_number', $appInfo->fl_number, ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('fl_number','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 {{$errors->has('fl_expire_date') ? 'has-error': ''}}">
                                                        {!! Form::label('fl_expire_date','Expiry Date',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                                {!! Form::text('fl_expire_date', (empty($appInfo->fl_expire_date)) ? '' : date('d-M-Y', strtotime($appInfo->fl_expire_date)), ['class'=>'form-control input-md datepicker', 'id' => 'fl_expire_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            </div>
                                                            {!! $errors->first('fl_expire_date','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="col-md-6 form-group {{$errors->has('fl_issuing_authority') ? 'has-error': ''}}">
                                                        {!! Form::label('fl_issuing_authority','Issuing Authority',['class'=>'col-md-5 text-left ']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('fl_issuing_authority', $appInfo->fl_issuing_authority, ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('fl_issuing_authority','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--19. Environment/ Site clearance certificate--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">20. Environment Clearance Certificate Details</legend>

                                            <div class="row">
                                                <div class="form-group" id="have_license_for_environment_div">
                                                    <div class="col-md-6 {{$errors->has('el_number') ? 'has-error': ''}}">
                                                        {!! Form::label('el_number','Environment License No',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('el_number', $appInfo->el_number, ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('el_number','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 {{$errors->has('el_expire_date') ? 'has-error': ''}}">
                                                        {!! Form::label('el_expire_date','Expiry Date',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                                {!! Form::text('el_expire_date', (empty($appInfo->el_expire_date)) ? '' : date('d-M-Y', strtotime($appInfo->el_expire_date)), ['class'=>'form-control input-md datepicker', 'id' => 'el_expire_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            </div>
                                                            {!! $errors->first('el_expire_date','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="col-md-6 form-group {{$errors->has('el_issuing_authority') ? 'has-error': ''}}">
                                                        {!! Form::label('el_issuing_authority','Issuing Authority',['class'=>'col-md-5 text-left ']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('el_issuing_authority', $appInfo->el_issuing_authority, ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('el_issuing_authority','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--20. (a) Bank information --}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">21. (a) Authorized Bank Information Details According to the latest IRC</legend>

                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('bank_id') ? 'has-error': ''}}">
                                                        {!! Form::label('bank_id','Bank name',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::select('bank_id', $banks, $appInfo->bank_id,['class'=>'form-control input-md select2', 'id' => 'bank_id', 'onchange'=>"getBranchByBankId('bank_id', this.value, 'branch_id', ".(!empty($appInfo->branch_id) ? $appInfo->branch_id:'').")", "style" => "width: 100%"]) !!}
                                                            {!! $errors->first('bank_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group {{$errors->has('branch_id') ? 'has-error': ''}}">
                                                        {!! Form::label('branch_id','Branch name',['class'=>'col-md-5 text-left ']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::select('branch_id', [], '', ['class' => 'form-control input-md','placeholder' => 'Select One']) !!}
                                                            {!! $errors->first('branch_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('bank_account_number') ? 'has-error': ''}}">
                                                        {!! Form::label('bank_account_number','Account number',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('bank_account_number', $appInfo->bank_account_number, ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('bank_account_number','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group {{$errors->has('bank_account_title') ? 'has-error': ''}}">
                                                        {!! Form::label('bank_account_title','Account title',['class'=>'col-md-5 text-left ']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('bank_account_title', $appInfo->bank_account_title, ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('bank_account_title','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--20. (b) Bank information --}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">21. (b) Want to change bank information?</legend>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="{{$errors->has('chnage_bank_info_check') ? 'has-error': ''}}">
                                                        {!! Form::label('chnage_bank_info_check','Want to change bank information?',['class'=>'col-md-6 text-left required-star']) !!}
                                                        <div class="col-md-6">
                                                            <label class="radio-inline">{!! Form::radio('chnage_bank_info_check','yes', ($appInfo->chnage_bank_info == 'yes' ? true :false), ['class'=>'cusReadonly required', 'id'=>'chnage_bank_info_yes', 'onclick' => 'chnageBankInfo(this.value)']) !!}
                                                                Yes</label>
                                                            <label class="radio-inline">{!! Form::radio('chnage_bank_info_check', 'no', ($appInfo->chnage_bank_info == 'no' ? true :false), ['class'=>'cusReadonly required', 'id'=>'chnage_bank_info_no', 'onclick' => 'chnageBankInfo(this.value)']) !!}
                                                                No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        
                                        <fieldset class="scheduler-border row  {{($appInfo->chnage_bank_info == 'yes') ? '': 'hidden'}}" id="change_bank_info_div">
                                            <legend class="scheduler-border">Authorized Bank Information Details</legend>

                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('n_bank_id') ? 'has-error': ''}}">
                                                        {!! Form::label('n_bank_id','Bank name',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::select('n_bank_id', $banks, $appInfo->n_bank_id,['class'=>'form-control input-md select2', 'id' => 'n_bank_id', 'onchange'=>"getBranchByBankId('n_bank_id', this.value, 'n_branch_id', ".(!empty($appInfo->n_branch_id) ? $appInfo->n_branch_id:'').")", "style" => "width: 100%"]) !!}
                                                            {!! $errors->first('n_bank_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group {{$errors->has('n_branch_id') ? 'has-error': ''}}">
                                                        {!! Form::label('n_branch_id','Branch name',['class'=>'col-md-5 text-left ']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::select('n_branch_id', [], '', ['class' => 'form-control input-md','placeholder' => 'Select One']) !!}
                                                            {!! $errors->first('n_branch_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('n_bank_account_number') ? 'has-error': ''}}">
                                                        {!! Form::label('n_bank_account_number','Account number',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('n_bank_account_number', $appInfo->n_bank_account_number, ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('n_bank_account_number','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group {{$errors->has('n_bank_account_title') ? 'has-error': ''}}">
                                                        {!! Form::label('n_bank_account_title','Account title',['class'=>'col-md-5 text-left ']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('n_bank_account_title', $appInfo->n_bank_account_title, ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('n_bank_account_title','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('noc_letter') ? 'has-error': ''}}">
                                                    {!! Form::label('noc_letter', ' NOC letter :', ['class' => 'col-md-5']) !!}
                                                    <div class="col-md-6">
                                                        {!! Form::file('noc_letter', ['class' => 'form-control', 'accept'=>"application/pdf"]) !!}
                                                        <small style="font-size: 9px; font-weight: bold; color: #666363; font-style: italic">[Format: *.PDF | Maximum 3 MB] </small>
                                                        {!! $errors->first('noc_letter','<span class="help-block">:message</span>') !!}
                                                        <br>
                                                    </div>
                                                    <div class="col-md-1" style="margin-top: 5px;">
                                                        @if(!empty($appInfo->noc_letter))
                                                            <a target="_blank" class="Url btn btn-xs btn-primary" href="{{URL::to('/uploads/'.(!empty($appInfo->noc_letter) ? $appInfo->noc_letter : ''))}}">
                                                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                                                Open File
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                        </fieldset>
                                        
                                        {{--21. Membership of Chamber/ Association information --}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">22. Membership of the Chamber/ Association Information Details</legend>
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('assoc_membership_number') ? 'has-error': ''}}">
                                                        {!! Form::label('assoc_membership_number','Membership number',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('assoc_membership_number', $appInfo->assoc_membership_number, ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('assoc_membership_number','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group {{$errors->has('assoc_chamber_name') ? 'has-error': ''}}">
                                                        {!! Form::label('assoc_chamber_name','Chamber name',['class'=>'col-md-5 text-left ']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('assoc_chamber_name', $appInfo->assoc_chamber_name, ['class' => 'form-control input-md']) !!}
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
                                                                {!! Form::text('assoc_issuing_date', (empty($appInfo->assoc_issuing_date)) ? '' : date('d-M-Y', strtotime($appInfo->assoc_issuing_date)), ['class'=>'form-control input-md datepicker', 'id' => 'assoc_issuing_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            </div>
                                                            {!! $errors->first('assoc_issuing_date','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group {{$errors->has('assoc_expire_date') ? 'has-error': ''}}">
                                                        {!! Form::label('assoc_expire_date','Expiry date',['class'=>'col-md-5 text-left ']) !!}
                                                        <div class="col-md-7">
                                                            <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                                {!! Form::text('assoc_expire_date', (empty($appInfo->assoc_expire_date)) ? '' : date('d-M-Y', strtotime($appInfo->assoc_expire_date)), ['class'=>'form-control input-md datepicker', 'id' => 'assoc_expire_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            </div>
                                                            {!! $errors->first('assoc_expire_date','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--22. BIN/ VAT --}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">23. Business Identification Number (BIN)/ Value-added Tex (VAT) Details</legend>
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('bin_vat_number') ? 'has-error': ''}}">
                                                        {!! Form::label('bin_vat_number','BIN/ VAT number',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('bin_vat_number', $appInfo->bin_vat_number, ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('bin_vat_number','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group {{$errors->has('bin_vat_issuing_date') ? 'has-error': ''}}">
                                                        {!! Form::label('bin_vat_issuing_date','Issuing date',['class'=>'col-md-5 text-left ']) !!}
                                                        <div class="col-md-7">
                                                            <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                                {!! Form::text('bin_vat_issuing_date', (empty($appInfo->bin_vat_issuing_date)) ? '' : date('d-M-Y', strtotime($appInfo->bin_vat_issuing_date)), ['class'=>'form-control input-md datepicker', 'id' => 'bin_vat_issuing_date', 'placeholder'=>'Pick from datepicker']) !!}
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
                                                            {!! Form::text('bin_vat_issuing_authority', $appInfo->bin_vat_issuing_authority, ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('bin_vat_issuing_authority','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group {{$errors->has('bin_vat_expire_date') ? 'has-error': ''}}">
                                                        {!! Form::label('bin_vat_expire_date','Expiry date',['class'=>'col-md-5 text-left ']) !!}
                                                        <div class="col-md-7">
                                                            <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                                {!! Form::text('bin_vat_expire_date', (empty($appInfo->bin_vat_expire_date)) ? '' : date('d-M-Y', strtotime($appInfo->bin_vat_expire_date)), ['class'=>'form-control input-md datepicker', 'id' => 'bin_vat_expire_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            </div>
                                                            {!! $errors->first('bin_vat_expire_date','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--23. Other Licenses --}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border"><span>24. Other Licenses/ NOC/ Permission/ Registration</span>
                                            </legend>
                                            <div class="table-responsive">
                                                <table id="otherLicenceTbl" class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
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

                                                    @if(count($otherLicence)>0)
                                                        <?php $inc = 0; ?>
                                                        @foreach($otherLicence as $otherLicence)
                                                            <tr id="otherLicence{{$inc}}" data-number="1"
                                                                class="table-tr">
                                                                <input type="hidden" name="other_licence_id[{{ $inc }}]"
                                                                       value="{{ $otherLicence->id }}">
                                                                <td>
                                                                    {!! Form::text("other_licence_name[$inc]", $otherLicence->licence_name, ['class' => 'form-control input-md']) !!}
                                                                </td>

                                                                <td>
                                                                    {!! Form::text("other_licence_no[$inc]", $otherLicence->licence_no, ['class' => 'form-control input-md']) !!}
                                                                </td>

                                                                <td>
                                                                    {!! Form::text("other_licence_issuing_authority[$inc]", $otherLicence->issuing_authority, ['class' => 'form-control input-md']) !!}
                                                                </td>

                                                                <td>
                                                                    <div class="input-group date">
                                                                        {!! Form::text("other_licence_issue_date[$inc]", (empty($otherLicence->issue_date)) ? '' : date('d-M-Y', strtotime($otherLicence->issue_date)), ['class'=>'form-control input-md datepicker', 'id' => 'annual_production_start_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                                    </div>
                                                                </td>

                                                                <td>
                                                                    @if($inc == 0)
                                                                    <a class="btn btn-md btn-primary addTableRows" onclick="addTableRowForIRC('otherLicenceTbl', 'otherLicence0');">
                                                                       <i class="fa fa-plus"></i>
                                                                    </a>
                                                                    @else
                                                                    <a href="javascript:void(0);" class="btn btn-md btn-danger removeRow"
                                                                       onclick="removeTableRow('otherLicenceTbl','otherLicence{{$inc}}');">
                                                                        <i class="fa fa-times" aria-hidden="true"></i>
                                                                    </a>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <?php $inc++; ?>
                                                        @endforeach
                                                    @else
                                                        <tr id="otherLicence0" data-number="1" class="table-tr">
                                                            <td>
                                                                {!! Form::text("other_licence_name[]", '', ['class' => 'form-control input-md']) !!}
                                                            </td>

                                                            <td>
                                                                {!! Form::text("other_licence_no[]", '', ['class' => 'form-control input-md']) !!}
                                                            </td>

                                                            <td>
                                                                {!! Form::text("other_licence_issuing_authority[]", '', ['class' => 'form-control input-md']) !!}
                                                            </td>

                                                            <td>
                                                                <div class="input-group date">
                                                                    {!! Form::text('other_licence_issue_date[]', '', ['class'=>'form-control input-md datepicker', 'id' => 'annual_production_start_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <a class="btn btn-md btn-primary addTableRows" onclick="addTableRowForIRC('otherLicenceTbl', 'otherLicence0');">
                                                                   <i class="fa fa-plus"></i></a>
                                                            </td>
                                                        </tr>
                                                    @endif
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
                                            <legend class="scheduler-border">Information of (Chairman/ Managing Director/ Or Equivalent):</legend>
                                            <div class="row">
                                                <div class="form-group col-md-6 {{$errors->has('g_full_name') ? 'has-error': ''}}">
                                                    {!! Form::label('g_full_name','Full Name',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('g_full_name', $appInfo->g_full_name, ['class' => 'form-control input-md required']) !!}
                                                        {!! $errors->first('g_full_name','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6 {{$errors->has('g_designation') ? 'has-error': ''}}">
                                                    {!! Form::label('g_designation','Position/ Designation',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('g_designation', $appInfo->g_designation, ['class' => 'form-control input-md required']) !!}
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
                                                                <input type="hidden" id="investor_signature_hidden" name="investor_signature_hidden" value="{{$appInfo->g_signature}}"/>
                                                            @endif
                                                        </div>
                                                        <div class="form-group">
                                                                <span style="font-size: 9px; font-weight: bold; display: block;">
                                                                       [File Format: *.jpg/ .jpeg | Width 300PX, Height 80PX]
                                                                </span>
                                                            <br/>
                                                            <label class="btn btn-primary btn-file" {{ $errors->has('investor_signature') ? 'has-error' : '' }}>
                                                                <i class="fa fa-picture-o" aria-hidden="true"></i>
                                                                Browse
                                                                <input
                                                                        class="{{ empty($appInfo->g_signature) ? "required" : "" }}"
                                                                        type="file"
                                                                        style="position: absolute; left: -9999px;"
                                                                        name="investor_signature_name"
                                                                        id="investor_signature"
                                                                        onchange="imageUploadWithCropping(this, 'investor_signature_preview', 'investor_signature_base64')"
                                                                        size="300x80"/>
                                                            </label>
                                                            {!! $errors->first('g_signature','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--List of directors--}}
                                        <div class="panel panel-info">
                                            <div class="panel-heading">
                                                <div class="pull-left" style="padding:5px 5px"><strong>List of directors</strong></div>
                                                <div class="pull-right">
                                                    @if((in_array($appInfo->status_id, [-1,5])) && (Auth::user()->desk_id == 0))
                                                    <a data-toggle="modal" data-target="#ircRegularadhocModal" onclick="openModal(this)"
                                                        data-action="{{ url('/irc-recommendation-regular/create-director/'.Encryption::encodeId($appInfo->id)) }}">
                                                        {!! Form::button('<i class="fa fa-plus"></i> <b>Add New Director</b>', array('type' => 'button', 'class' => 'pull-right btn btn-info')) !!}
                                                    </a>
                                                    @endif
                                                </div> 
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="panel-body" id="list_of_director"></div>
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
                                                    my knowledge and I shall be liable for any false information/statement is given.
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            @if(ACL::getAccsessRight('IRCRecommendationRegular','-E-') && $appInfo->status_id != 6 && Auth::user()->user_type == '5x505')
                                @if(!in_array($appInfo->status_id,[5]))
                                    <div class="pull-left">
                                        <button type="submit" class="btn btn-info btn-md cancel"
                                                value="draft" name="actionBtn" id="save_as_draft">Save as Draft
                                        </button>
                                    </div>
                                    <div class="pull-left" style="padding-left: 1em;">
                                        <button type="submit" id="submitForm" style="cursor: pointer;" class="btn btn-success btn-md"
                                                value="Submit" name="actionBtn">Payment & Submit
                                            <i class="fa fa-question-circle" style="cursor: pointer"
                                               data-toggle="tooltip" title="" 
                                               data-original-title="After clicking this button will take you in the payment portal for providing the required payment. After payment, the application will be automatically submitted and you will get a confirmation email with necessary info."
                                               aria-describedby="tooltip"></i>
                                        </button>
                                    </div>
                                @endif

                                @if($appInfo->status_id == 5)
                                    <div class="pull-left">
                                        <span style="display: block; height: 34px">&nbsp;</span>
                                    </div>
                                    <div class="pull-left" style="padding-left: 1em;">
                                        <button type="submit" id="submitForm" style="cursor: pointer;" class="btn btn-info btn-md"
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

{{--IRC Regular Adhoc modal--}}
<div class="modal fade" id="ircRegularadhocModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
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

    function calculateHalfYearlyProduction(arg) {
        var $tr = $(arg).closest('tr');
        var quantity = $tr.find('td:eq(1) input').val();
        $tr.find('td:eq(2) input').val(quantity/2);
    }

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

    function lastIrc(value) {
        if (value == 'yes') {
            $("#irc_ref_app_tracking_no_div").removeClass('hidden');
            $("#irc_ref_app_tracking_no").addClass('required');
            $("#irc_ref_app_approve_date").addClass('required');

            $("#irc_manually_approved_div").addClass('hidden');
            $("#irc_manually_approved_no").removeClass('required');
            $("#irc_manually_approved_date").removeClass('required');

        } else if (value == 'no') {
            $("#irc_ref_app_tracking_no_div").addClass('hidden');
            $("#irc_ref_app_tracking_no").removeClass('required');
            $("#irc_ref_app_approve_date").removeClass('required');

            $("#irc_manually_approved_div").removeClass('hidden');
            $("#irc_manually_approved_no").addClass('required');
            $("#irc_manually_approved_date").addClass('required');

        } else {
            $("#irc_ref_app_tracking_no_div").addClass('hidden');
            $("#irc_ref_app_tracking_no").removeClass('required');
            $("#irc_ref_app_approve_date").removeClass('required');

            $("#irc_manually_approved_div").addClass('hidden');
            $("#irc_manually_approved_no").removeClass('required');
            $("#irc_manually_approved_date").removeClass('required');
        }
    }
    
    function chnageBankInfo(value) {
        if (value == 'yes') {
            $("#change_bank_info_div").removeClass('hidden');


        } else if (value == 'no') {
            $("#change_bank_info_div").addClass('hidden');

        } else {
            $("#change_bank_info_div").addClass('hidden');
        }
    }
</script>


<script type="text/javascript">

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
        let new_row = document.getElementById(template_row_id).cloneNode(true);
        new_row.id = "";
        new_row.style.display = "";
        let current_total_row = "";

        //has new datepicker
        if (tableID == 'saleStatementTbl') {
            var hasDatepickerClass = $('#' + tableID).find('.monthpicker').hasClass('monthpicker');
        } else {
            var hasDatepickerClass = $('#' + tableID).find('.datepicker').hasClass('datepicker');
        }

        if (hasDatepickerClass) {
            //Get the total row, and last row number of table
            current_total_row = $('#' + tableID).find('tbody').find('.table-tr').length;
        } else {
            // Get the total row number, and last row number of table
            current_total_row = $('#' + tableID).find('tbody tr').length;
        }

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
        // data-number attribute update of the new row
        $('#' + tableID).find('tbody tr').last().attr('data-number', last_row_number);

        //Ajax file uploadDocument function argument change
        $("#" + tableID).find('#' + new_row_id).find('.ajax-file-upload-function').attr('onchange', 'uploadDocument("preview_' + current_total_row + '", this.id,"validate_field_' + current_total_row + '", 0, "true")');

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
            var updated_id_of_input_box = id_of_input_box.replace('0', +current_total_row);
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
            var updated_preview_div_id = findPreviewDivClass[0].id.replace('0', +current_total_row);
            $("#" + tableID).find('#' + new_row_id).find('.preview-div')[0].id = updated_preview_div_id;
        }

        // Table footer adding with add more button
        var table_header_columns = "";
        //console.log(final_total_row);
        if (final_total_row > 3) {
            const check_tfoot_element = $('#' + tableID + ' tfoot').length;
            console.log(check_tfoot_element);
            if (check_tfoot_element === 0) {
                if (hasDatepickerClass) {
                    table_header_columns = $('#' + tableID).find('thead tr').find('.table-header');
                } else {
                    table_header_columns = $('#' + tableID).find('thead th');
                }
                let table_footer = document.getElementById(tableID).createTFoot();
                console.log(table_footer);
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
            theme: 'blue',
        });

        $("#" + tableID).find('.monthpicker').datepicker({
            outputFormat: 'MMM-y',
            theme: 'blue',
            startView: 1
        });

    } // end of addTableRowForIRC() function

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

    function calculateAddRowTotal(className, totalShowFieldId) {
        var total_value = 0.00;
        $("." + className).each(function () {
            total_value = total_value + (this.value ? parseFloat(this.value) : 0.00);
        });
        $("#" + totalShowFieldId).val(total_value.toFixed(3));
    }

    function calculateImportedAndLocalVaule(){
        var total = 0.00;
        var imported_value = document.getElementById('ex_machine_imported_value_bdt').value;
        var local_value = document.getElementById('ex_machine_local_value_bdt').value;
        total = Number(imported_value) + Number(local_value);
        document.getElementById('ex_machine_total_value_bdt').value = total.toFixed(2);
    }

    function calculateProductionStock(){
        var total = 0;
        var production_quantity = document.getElementById('production_total_quantity').value;
        var production_sales = document.getElementById('production_total_sales').value;
        total = Number(production_quantity) - Number(production_sales);
        document.getElementById('production_total_stock').value = total;
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
                url: "/irc-recommendation-regular/get-business-class-single-list",
                data: {
                    _token: _token,
                    business_class_code: business_class_code
                },
                success: function (response) {

                    if (response.responseCode == 1 && response.data.length != 0) {

                        $("#no_business_class_result").html('');
                        $("#business_class_list_sec").removeClass('hidden');

                        let table_row = '<tr><td>Section</td><td>' + response.data[0].section_code + '</td><td>' + response.data[0].section_name + '</td></tr>';
                        table_row += '<tr><td>Division</td><td>' + response.data[0].division_code + '</td><td>' + response.data[0].division_name + '</td></tr>';
                        table_row += '<tr><td>Group</td><td>' + response.data[0].group_code + '</td><td>' + response.data[0].group_name + '</td></tr>';
                        table_row += '<tr><td>Class</td><td>' + response.data[0].code + '</td><td>' + response.data[0].name + '</td></tr>';

                        let option = '<option value="">Select One</option>';
                        $.each(response.subClass, function (id, value) {
                            if (id == sub_class_id) {
                                option += '<option value="' + id + '" selected> ' + value + '</option>';
                            } else {
                                option += '<option value="' + id + '">' + value + '</option>';
                            }
                        });

                        table_row += '<tr><td width="20%" class="required-star">Sub class</td><td colspan="2"><select onchange="otherSubClassCodeName(this.value)" id="sub_class_id" name="sub_class_id" class="form-control required">' + option + '</select></td></tr>';

                        let other_sub_class_code = '{{ $appInfo->other_sub_class_code }}';
                        let other_sub_class_name = '{{ $appInfo->other_sub_class_name }}';

                        table_row += '<tr id="other_sub_class_code_parent" class="hidden"><td width="20%" class="">Other sub class code</td><td colspan="2"><input type="text" name="other_sub_class_code" id="other_sub_class_code" class="form-control" value="'+other_sub_class_code+'"></td></tr>';
                        table_row += '<tr id="other_sub_class_name_parent" class="hidden"><td width="20%" class="required-star">Other sub class name</td><td colspan="2"><input type="text" name="other_sub_class_name" id="other_sub_class_name" class="form-control required" value="'+other_sub_class_name+'"></td></tr>';

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
        if ($('#' + tableID).find('.datepicker').hasClass('datepicker') || $('#saleStatementTbl').find('.monthpicker').hasClass('monthpicker')) {
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

        calculateAddRowTotal('sales_value_bdt', 'sales_value_bdt_total');
        calculateAddRowTotal('sales_vat_bdt', 'sales_vat_total');
        calculateAddRowTotal('em_spare_value_bdt', 'total_spare_value_bdt');
        calculateAddRowTotal('ins_apc_half_yearly_import', 'ins_apc_half_yearly_import_total');
        calculateAddRowTotal('ins_apsp_half_yearly_import', 'ins_apsp_half_yearly_import_total');

        calculateListOfMachineryTotal('machinery_imported_total_value', 'machinery_imported_total_amount');
        calculateListOfMachineryTotal('machinery_local_total_value', 'machinery_local_total_amount');

        calculateListOfMachineryTotal('em_spare_value_bdt', 'total_spare_value_bdt');
        calculateListOfMachineryTotal('em_price_bdt', 'total_lc_price_bdt');
        // calculateListOfMachineryTotal('em_price_taka_mil', 'total_lc_taka_mil');
        calculateListOfMachineryTotal('em_local_price_taka_mil', 'em_local_total_taka_mil');
    }

    $(document).ready(function () {
        var form = $("#IrcRecommendationRegularForm").show();
        var irc_regular_purpose_value = $('#irc_regular_purpose_id').val();
        regularSectionChange(irc_regular_purpose_value);

        //select 2 validation here
        select2OptionValidation(form);

        form.find('#save_as_draft').css('display', 'none');
        form.find('#submitForm').css('display', 'none');
        form.find('.actions').css('top', '-15px !important');
        form.steps({
            headerTag: "h3",
            bodyTag: "fieldset",
            transitionEffect: "slideLeft",
            onStepChanging: function (event, currentIndex, newIndex) {
                // return true;
                if (newIndex == 1) {
                    var is_dataload_from_irc_2nd = '{{ $appInfo->last_irc_2nd_adhoc }}';
                    var app_irc_purpose_id = '{{ $appInfo->irc_purpose_id }}';
                    if($('#irc_purpose_id').find(":selected").val() != app_irc_purpose_id && is_dataload_from_irc_2nd == 'yes' && $('#irc_regular_purpose_id').val() == 1){
                        alert('IRC purpose did not match with 2nd adhoc. Please change.');
                        return false;
                    }
                }

                if (newIndex == 2) {
                    //Check annual production capacity has raw materials 
                    var annualHaveMaterial = 0;
                    var _token = $('input[name="_token"]').val();
                    var application_id = '{{ Encryption::encodeId($appInfo->ref_id) }}';
                    var process_type_id = '{{ Encryption::encodeId($appInfo->process_type_id) }}';
                    $.ajax({
                        type: "GET",
                        url: "<?php echo url(); ?>/irc-recommendation-regular/production-info",
                        async: false,
                        data: {
                            _token: _token,
                            application_id: application_id,
                            process_type_id: process_type_id
                        },
                        success: function (response) {
                            annualHaveMaterial = response.annualHaveMaterial;
                        }
                    });

                    if (annualHaveMaterial == 1) {
                        swal({
                            type: 'error',
                            title: 'Oops...',
                            text: "Please Insert Annual Production's Raw Material Information on 7th Section!",
                        });
                        return false;
                    }

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
                    var irc_purpose = $('#irc_purpose').val();
                    if (irc_purpose != 2) {
                        var is_list_of_apc = 0;
                        var _token = $('input[name="_token"]').val();
                        var application_id = '{{ Encryption::encodeId($appInfo->ref_id) }}';
                        $.ajax({
                            type: "GET",
                            url: "<?php echo url(); ?>/irc-recommendation-regular/list-of-annual-production",
                            async: false,
                            data: {
                                _token: _token,
                                application_id: application_id
                            },
                            success: function (response) {
                                is_list_of_apc = response.total_list_of_apc;
                                // console.log(is_list_of_apc);
                            }
                        });

                        if (is_list_of_apc < 1) {
                            swal({
                                type: 'error',
                                title: 'Oops...',
                                text: "Annual production capacity required in section 7",
                                footer: '<a target="_blank" href="{{ url('bida-registration/list-of/annual-production/'.Encryption::encodeId($appInfo->id).'/'.Encryption::encodeId($appInfo->process_type_id)) }}">Click here to add or update the records</a>'
                            });
                            return false;
                        }
                    }
                }

                if (newIndex == 3) {

                    // list of director
                    var is_list_of_director = 0;
                    var _token = $('input[name="_token"]').val();
                    var application_id = '{{ Encryption::encodeId($appInfo->ref_id) }}';
                    var process_type_id = '{{ Encryption::encodeId($appInfo->process_type_id) }}';
                    $.ajax({
                        type: "GET",
                        url: "<?php echo url(); ?>/bida-registration/list-of-director-info",
                        async: false,
                        data: {
                            _token: _token,
                            application_id: application_id,
                            process_type_id: process_type_id
                        },
                        success: function (response) {
                            is_list_of_director = response.total_list_of_dirctors;
                            // console.log(is_list_of_director);
                        }
                    });

                    if (is_list_of_director < 1) {
                        swal({
                            type: 'error',
                            title: 'Oops...',
                            text: "List of directors required",
                            footer: '<a target="_blank" href="{{ url('bida-registration/list-of/director/'.Encryption::encodeId($appInfo->id).'/'.Encryption::encodeId($appInfo->process_type_id)) }}">Click here to add or update the records</a>'
                        });
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
                    form.find('#save_as_draft').css('display', 'block');
                    form.find('.actions').css('top', '-42px');
                } else {
                    form.find('#save_as_draft').css('display', 'none');
                    form.find('.actions').css('top', '-15px');
                }

                if (currentIndex == 4) {
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
                popupWindow = window.open('<?php echo URL::to('/irc-recommendation-regular/preview'); ?>', 'Sample', '');
            } else {
                return false;
            }
        });

        //trigger basic information section
        $('input[name=last_br_check]:checked').trigger('click');
        $('input[name=last_irc_check]:checked').trigger('click');
        $("#bank_id").trigger('change');
        $("#n_bank_id").trigger('change');

        // Load list of directors
        LoadListOfDirectors();

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
        //load annual production capacity
        loadAnnualProductionCapacityData();
        //checkOrganizationStatusId();
        $("#organization_status_id").change(checkOrganizationStatusId);
        //call this function when open application
        checkOrganizationStatusId();

        calculateAddRowTotal('sales_value_bdt', 'sales_value_bdt_total');
        calculateAddRowTotal('sales_vat_bdt', 'sales_vat_total');
        calculateAddRowTotal('em_spare_value_bdt', 'total_spare_value_bdt');
        calculateAddRowTotal('ins_apc_half_yearly_import', 'ins_apc_half_yearly_import_total');

        calculateListOfMachineryTotal('machinery_imported_total_value', 'machinery_imported_total_amount');
        calculateListOfMachineryTotal('machinery_local_total_value', 'machinery_local_total_amount');

        calculateListOfMachineryTotal('em_spare_value_bdt', 'total_spare_value_bdt');
        calculateListOfMachineryTotal('em_price_bdt', 'total_lc_price_bdt');
        //calculateListOfMachineryTotal('em_price_taka_mil', 'total_lc_taka_mil');
        calculateListOfMachineryTotal('em_local_price_taka_mil', 'em_local_total_taka_mil');

        var class_code = '{{ $appInfo->class_code }}';
        var sub_class_id = '{{ $appInfo->sub_class_id }}';
        var sub_class_id = '{{ $appInfo->sub_class_id == "0" ? "-1" : $appInfo->sub_class_id }}';
        findBusinessClassCode(class_code, sub_class_id);

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

        $('#IrcRecommendationRegularForm').validate({
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
        //console.log(organizationStatusId);
        if (organizationStatusId == 3) {
            $("#country_of_origin_id").removeClass('required');
            $(".country_of_origin_div").hide('slow');
        } else {
            $(".country_of_origin_div").show('slow');
            $("#country_of_origin_id").addClass('required');

        }
        ownershipAndOrganizationStatusWiseDocLoad(organizationStatusId);
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

        // $(inputs).each(function( value ) {
        //    console.log(value)
        // });
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
            var action = "{{url('/irc-recommendation-regular/upload-document')}}";

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

        if ($('input[name=agree_with_instruction]').prop('checked') == true) {
            document.getElementById('last_app_data_div').style.display = 'block';
        }

        $('input[name=agree_with_instruction]').change(function (e) {

            if (this.checked) {
                document.getElementById('last_app_data_div').style.display = 'block';
            } else {
                document.getElementById('last_app_data_div').style.display = 'none';
            }
        });

        $(document).ready(function() {
            $('#ownership_status_id').change(function (e) {
                var ownership_status_id = this.value;
                $("#incorporation").toggleClass('hidden', ownership_status_id == '3');
            });
            var initialOwnershipStatus = $('#ownership_status_id').val();
            $("#incorporation").toggleClass('hidden', initialOwnershipStatus == '3');
        });

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
            theme: 'blue',
        });

        $('.monthpicker').datepicker({
            outputFormat: 'MMM-y',
            theme: 'blue',
            startView: 1
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
                url: "<?php echo url(); ?>/irc-recommendation-regular/get-district-by-division",
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

        sectionChange('{{$appInfo->irc_purpose_id}}');
    });

    function sectionChange(selectedvalue) {
        if (selectedvalue == 1) { // 1 = Raw material
            document.getElementById('annual_raw').style.display = 'block';
            document.getElementById('annual_ins_raw').style.display = 'block';
            document.getElementById('n_annual_ins_raw').style.display = 'block';
            // document.getElementById('existing_lc').style.display = 'block';
            // document.getElementById('existing_local').style.display = 'block';
            document.getElementById('existing_spare').style.display = 'none';
            document.getElementById('annual_spare').style.display = 'none';
            // document.getElementById('annual_ins_spare').style.display = 'none';
            document.getElementById('annual_ins_both').style.display = 'none';
            document.getElementById('n_annual_ins_both').style.display = 'none';
            // document.getElementById('existing_spare').style.display = 'none';

            //section 24
            document.getElementById('adhoc_based_spare_parts').style.display = 'none';
            document.getElementById('n_adhoc_based_spare_parts').style.display = 'none';

            $(".apc_quantity").addClass("required");
            $(".apsp_quantity").removeClass("required");

            //annual_raw
            classAddRemove(['apc_product_name', 'apc_quantity_unit', 'apc_quantity', 'apc_price_usd', 'apc_value_taka'], 'block');

            //For Spare Parts (annual_ins_spare)
            // classAddRemove(['ins_apsp_half_yearly_import_total', 'ins_apsp_half_yearly_import_total_in_word'], 'none');

            // 7. annual_spare
            classAddRemove(['apsp_product_name', 'apsp_quantity_unit', 'apsp_quantity', 'apsp_price_usd', 'apsp_value_taka'], 'none');

            //annual_ins_both grand total
            classAddRemove(['import_cap_grd_total', 'import_cap_grd_total_wrd'], 'none');

            //Half-yearly import rights of adhoc based spare parts/ demand (adhoc_based_spare_parts)
            classAddRemove(['first_em_lc_total_taka_mil', 'first_em_lc_total_percent', 'first_em_lc_total_five_percent', 'first_em_lc_total_five_percent_in_word'], 'none');

            // 7. existing_spare
            classAddRemove(['em_spare_lc_no', 'em_spare_lc_date', 'em_spare_lc_value_currency', 'em_spare_value_bdt', 'em_spare_lc_bank_branch'], 'none');

            // As Per L/C Open
            classAddRemove(['em_product_name', 'em_quantity_unit', 'em_quantity', 'em_unit_price', 'em_price_unit', 'em_price_bdt'], 'none');
            $('.required-star-dynamically').removeClass("required-star");

        } else if (selectedvalue == 2) { // 2 = spare parts
            document.getElementById('annual_raw').style.display = 'none';
            document.getElementById('annual_ins_raw').style.display = 'none';
            document.getElementById('n_annual_ins_raw').style.display = 'none';
            document.getElementById('existing_spare').style.display = 'block';
            // document.getElementById('annual_spare').style.display = 'block';
            document.getElementById('annual_spare').style.display = 'none';
            // document.getElementById('annual_ins_spare').style.display = 'block';
            document.getElementById('annual_ins_both').style.display = 'none';
            document.getElementById('n_annual_ins_both').style.display = 'none';
            // document.getElementById('existing_spare').style.display = 'block';

            //section 24
            document.getElementById('adhoc_based_spare_parts').style.display = 'block';
            document.getElementById('n_adhoc_based_spare_parts').style.display = 'block';

            $(".apc_quantity").removeClass("required");
            // $(".apsp_quantity").addClass("required");

            //annual_raw
            classAddRemove(['apc_product_name', 'apc_quantity_unit', 'apc_quantity', 'apc_price_usd', 'apc_value_taka'], 'none');

            // 7. annual_spare
            classAddRemove(['apsp_product_name', 'apsp_quantity_unit', 'apsp_quantity', 'apsp_price_usd', 'apsp_value_taka'], 'none');

            //For Spare Parts (annual_ins_spare)
            // classAddRemove(['ins_apsp_half_yearly_import_total', 'ins_apsp_half_yearly_import_total_in_word'], 'block');

            // 7. annual_spare
            // classAddRemove(['apsp_product_name', 'apsp_quantity_unit', 'apsp_quantity', 'apsp_price_usd', 'apsp_value_taka'], 'block');

            //annual_ins_both grand total
            classAddRemove(['import_cap_grd_total', 'import_cap_grd_total_wrd'], 'none');

            //Half-yearly import rights of adhoc based spare parts/ demand (adhoc_based_spare_parts)
            classAddRemove(['first_em_lc_total_taka_mil', 'first_em_lc_total_percent', 'first_em_lc_total_five_percent', 'first_em_lc_total_five_percent_in_word'], 'block');
             // 7. existing_spare
             classAddRemove(['em_spare_lc_no', 'em_spare_lc_date', 'em_spare_lc_value_currency', 'em_spare_value_bdt', 'em_spare_lc_bank_branch'], 'block');

            // As Per L/C Open
            classAddRemove(['em_product_name', 'em_quantity_unit', 'em_quantity', 'em_unit_price', 'em_price_unit', 'em_price_bdt'], 'block');
            $('.required-star-dynamically').addClass("required-star");

        } else if (selectedvalue == 3) { // 3 = Both
            document.getElementById('annual_raw').style.display = 'block';
            document.getElementById('annual_ins_raw').style.display = 'block';
            document.getElementById('n_annual_ins_raw').style.display = 'block';
            document.getElementById('existing_spare').style.display = 'block';
            document.getElementById('annual_ins_both').style.display = 'block';
            document.getElementById('n_annual_ins_both').style.display = 'block';

            // document.getElementById('annual_spare').style.display = 'block';
            document.getElementById('annual_spare').style.display = 'none';
            // document.getElementById('annual_ins_spare').style.display = 'block';

            //section 24
            document.getElementById('adhoc_based_spare_parts').style.display = 'block';
            document.getElementById('n_adhoc_based_spare_parts').style.display = 'block';

            //annual_raw
            classAddRemove(['apc_product_name', 'apc_quantity_unit', 'apc_quantity', 'apc_price_usd', 'apc_value_taka'], 'block');

            //For Spare Parts (annual_ins_spare)
            // classAddRemove(['ins_apsp_half_yearly_import_total', 'ins_apsp_half_yearly_import_total_in_word'], 'block');

            // 7. annual_spare
            classAddRemove(['apsp_product_name', 'apsp_quantity_unit', 'apsp_quantity', 'apsp_price_usd', 'apsp_value_taka'], 'none');

            // 7. annual_spare
            // classAddRemove(['apsp_product_name', 'apsp_quantity_unit', 'apsp_quantity', 'apsp_price_usd', 'apsp_value_taka'], 'block');

            //annual_ins_both grand total
            classAddRemove(['import_cap_grd_total', 'import_cap_grd_total_wrd'], 'block');

            //Half-yearly import rights of adhoc based spare parts/ demand (adhoc_based_spare_parts)
            classAddRemove(['first_em_lc_total_taka_mil', 'first_em_lc_total_percent', 'first_em_lc_total_five_percent', 'first_em_lc_total_five_percent_in_word'], 'block');
            //7. existing_spare
            classAddRemove(['em_spare_lc_no', 'em_spare_lc_date', 'em_spare_lc_value_currency', 'em_spare_value_bdt', 'em_spare_lc_bank_branch'], 'block');

            // As Per L/C Open
            classAddRemove(['em_product_name', 'em_quantity_unit', 'em_quantity', 'em_unit_price', 'em_price_unit', 'em_price_bdt'], 'block');
            $('.required-star-dynamically').addClass("required-star");
        }
    }


    function regularSectionChange(selectedvalue) {
        if (selectedvalue == 1) { // 1 = No Change
            document.getElementById('annual_amendment_info').style.display = 'none';
        }else {
            document.getElementById('annual_amendment_info').style.display = 'block';
        }
        
    }


    function classAddRemove(inputFieldClass, display) {
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

    $('#IrcRecommendationRegularForm h3').hide();
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
<script src="{{ asset("vendor/character-counter/jquery.character-counter_v1.0.min.js") }}" src=""
        type="text/javascript"></script>
<script>
    $(function () {

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
        $("#applicationForm").find('.iti').css({"width": "-moz-available", "width": "-webkit-fill-available"});
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

    //Load list of directors
    function LoadListOfDirectors() {
        $.ajax({
            url: "{{ url("irc-recommendation-regular/load-listof-directors") }}",
            type: "POST",
            data: {
                app_id: "{{ Encryption::encodeId($appInfo->id) }}",
                process_type_id: "{{ Encryption::encodeId($appInfo->process_type_id) }}",
                _token: $('input[name="_token"]').val()
            },
            success: function (response) {
                if (response.responseCode == 1){
                    $('#list_of_director').html(response.html);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Unknown error occured. Please, try again after reload');
            }

        });
    }

    function loadAnnualProductionCapacityData() {
        $.ajax({
            url: "{{ url("irc-recommendation-regular/load-apc-data") }}",
            type: "POST",
            data: {
                app_id: "{{ Encryption::encodeId($appInfo->id) }}",
                _token : $('input[name="_token"]').val()
            },
            success: function(response){
                if (response.responseCode == 1){
                    $('#load_apc_data').html(response.html);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Unknown error occured. Please, try again after reload');
            }

        });
    }

    function confirmDelete(url, loading_type) {
        console.log(loading_type);
        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: "get",
                    success: function (response) {
                        if (response.responseCode == 1) {
                            swal('Deleted!', response.msg, 'success');
                            if(loading_type == 'director'){
                                LoadListOfDirectors();
                            }else if(loading_type == 'apc'){
                                loadAnnualProductionCapacityData();
                            }


                        } else {
                            swal('Oops!', response.msg,'warning');
                        }
                    }
                });
            } else {
                return false;
            }
        })
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
                url: '/irc-recommendation-regular/getDocList',
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

        return "irc_regular_" + ownership_key + "_" + organization_key;
    }

    function uploadDocumentProcess(id) {
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
    }

</script>