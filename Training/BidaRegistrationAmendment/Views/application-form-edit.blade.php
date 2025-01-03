<?php
$accessMode = ACL::getAccsessRight('BidaRegistrationAmendment');
if (!ACL::isAllowed($accessMode, '-E-')) {
    die('You have no access right! Please contact with system admin if you have any query.');
}
?>
<link rel="stylesheet" href="{{ url('assets/css/jquery.steps.css') }}">
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
        width: 16.6% !important;
    }

    .wizard > .steps > ul > li a {
        padding: 0.5em 0.5em !important;
    }

    .wizard > .steps .number {
        font-size: 1.2em;
    }

    .wizard > .actions {
        top: -10px;
    }

    .help-text {
        font-size: small;
    }

    .img-thumbnail {
        height: 80px;
        width: 100%;
    }

    input[type=radio].error,
    input[type=checkbox].error {
        outline: 1px solid red !important;
    }

    .table-striped > tbody#exiting_manpower > tr > td, .table-striped > tbody#exiting_manpower > tr > th {
        text-align: center;
    }

    .custom-file-input {
        color: transparent;
        border: none !important;
        padding-top: 2.5px;
    }

    .custom-file-input::-webkit-file-upload-button {
        visibility: hidden;
    }

    .custom-file-input::before {
        content: 'Browse';
        color: black;
        display: inline-block;
        background: -webkit-linear-gradient(top, #f9f9f9, #e3e3e3);
        border: 1px solid #999;
        border-radius: 3px;
        padding: 5px 8px;
        outline: none;
        white-space: nowrap;
        -webkit-user-select: none;
        cursor: pointer;
        text-shadow: 1px 1px #fff;
        font-weight: 700;
        font-size: 10pt;
    }

    .custom-file-input:hover::before {
        border-color: black;
    }

    .custom-file-input:active, .custom-file-input:focus {
        outline: 0;
    }

    .custom-file-input:active::before {
        background: -webkit-linear-gradient(top, #e3e3e3, #f9f9f9);
    }

    .croppie-container .cr-slider-wrap{
        width: 100% !important;
        margin: 5px auto !important;
    }

    .bg-green{
        background-color: rgba(103, 219, 56, 1);
    }
    .bg-yellow{
        background-color: rgba(246, 209, 15, 1);
    }
    .light-green{
        background-color: rgba(223, 240, 216, 1);
    }
    .light-yellow{
        background-color: rgba(252, 248, 227, 1);
    }

    .padding{
        padding: 5px;
    }

    .bbs-code, .table{
        margin-bottom: 0px;
    }

    .iti__country-list {
        z-index: 999999;
    }

    .iti__selected-flag {
        z-index: 9999;
    }
    .mobile-plugin {
        display: flex;
    }

    .reset-image {
        position: absolute;
        top: 75%;
        left: 90px;
        padding: 5px 12px !important;
        font-size: 14px;
    }
    .img-signature {
        height: 80px !important;
        width: 300px;
    }

    .datepicker-width {
        min-width: 21em !important;
    }
    .co-datepicker-width {
        min-width: 18em !important;
    }

    .mb-2 {
        margin-bottom: 2px;
    }

    @media (min-width: 768px) {
        .modal-xl {
            width: 90%;
            /*max-width:1200px;*/
        }
    }
    .modal {
        z-index: 99999 !important;
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
                <div class="alert alert-info alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">Ã—</button>'. Session::get("success") .'</div>
                ' : '' !!}
                {!! Session::has('error') ? '
                <div class="alert alert-danger alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">Ã—</button>'. Session::get("error") .'</div>
                ' : '' !!}

                <div class="panel panel-info">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h5><strong>Application for BIDA Registration Amendment </strong></h5>
                        </div>
                        <div class="pull-right">
                            {{--                            @if (isset($appInfo) && $appInfo->status_id == -1)--}}
                            {{--                                <a href="{{ asset('assets/images/SampleForm/BIDA_Registration_amendment.pdf') }}" target="_blank" class="btn btn-warning">--}}
                            {{--                                    <i class="fas fa-file-pdf"></i>--}}
                            {{--                                    Download Sample Form--}}
                            {{--                                </a>--}}
                            {{--                            @endif--}}
                            @if(in_array($appInfo->status_id,[5]))
                                <a data-toggle="modal" data-target="#remarksModal">
                                    {!! Form::button('<i class="fa fa-eye"></i> Reason of '.$appInfo->status_name.'', array('type' => 'button', 'class' => 'btn btn-md btn-danger')) !!}
                                </a>
                            @endif
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        {!! Form::open(array('url' => '/bida-registration-amendment/add','method' => 'post','id' => 'BidaRegistrationAmendmentForm','role'=>'form','enctype'=>'multipart/form-data')) !!}
                        <input type="hidden" name="selected_file" id="selected_file"/>
                        <input type="hidden" name="validateFieldName" id="validateFieldName"/>
                        <input type="hidden" name="isRequired" id="isRequired"/>
                        <input type="hidden" name="app_id" id="app_id" value="{{ Encryption::encodeId($appInfo->id) }}"/>

                        {{-- Basic information--}}
                        <h3 class="stepHeader">Basic Info</h3>
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
                            <div class="panel panel-info">
                                <div class="panel-heading "><strong>Basic Information</strong></div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-12 {{$errors->has('is_approval_online') ? 'has-error': ''}}">
                                                {!! Form::label('is_approval_online','Did you receive your BIDA Registration/ BIDA Registration amendment approval online OSS?',['class'=>'col-md-6 text-left required-star']) !!}
                                                <div class="col-md-6">
                                                    @if($appInfo->is_approval_online == 'yes')
                                                        <label class="radio-inline">{!! Form::radio('is_approval_online','yes', ($appInfo->is_approval_online == 'yes' ? true :false), ['class'=>'cusReadonly required helpTextRadio', 'id' => 'yes']) !!}
                                                            Yes
                                                        </label>
                                                    @endif
                                                    @if($appInfo->is_approval_online == 'no')
                                                        <label class="radio-inline">{!! Form::radio('is_approval_online', 'no', ($appInfo->is_approval_online == 'no' ? true :false), ['class'=>'cusReadonly required', 'id' => 'no']) !!}
                                                            No
                                                        </label>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            @if($appInfo->is_approval_online == 'yes')
                                                <div class="col-md-12 {{$errors->has('ref_app_tracking_no') ? 'has-error': ''}}">
                                                    {!! Form::label('ref_app_tracking_no','Please give your approved BIDA Registration/ BIDA Registration amendment Tracking No.',['class'=>'col-md-6 text-left']) !!}
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            {!! Form::hidden('ref_app_tracking_no', $appInfo->ref_app_tracking_no, ['data-rule-maxlength'=>'100', 'class' => 'form-control input-sm required', 'readonly']) !!}
                                                            <span class="label label-success" style="font-size: 15px">{{ (empty($appInfo->ref_app_tracking_no) ? '' : $appInfo->ref_app_tracking_no) }}</span>
                                                            @if ($appInfo->is_approval_online == 'yes')
                                                                &nbsp;{!! \App\Libraries\CommonFunction::getCertificateByTrackingNo($appInfo->ref_app_tracking_no) !!}
                                                            @endif
                                                            <br/>

                                                            <small class="text-danger">N.B.: Once you save or
                                                                submit the application, the BIDA Registration
                                                                tracking no cannot be changed anymore.</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        {!! Form::label('ref_app_approve_date', 'Approved Date', ['class' => 'col-md-6 text-left required-star', 'style' => 'padding: 0px;']) !!}
                                                        <div class="col-md-6">

                                                                {!! Form::text('ref_app_approve_date', (!empty($appInfo->ref_app_approve_date)) ? date('d-M-Y',strtotime($appInfo->ref_app_approve_date)) : '', ['class'=>'form-control input-md', 'id' => 'ref_app_approve_date', 'readonly']) !!}

                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($appInfo->is_approval_online == 'no')
                                                <div class="col-md-12 {{$errors->has('manually_approved_br_no') ? 'has-error': ''}} ">
                                                    {!! Form::label('manually_approved_br_no','Please give your manually approved BIDA Registration No',['class'=>'col-md-6 required-star text-left']) !!}
                                                    <div class="col-md-6">
                                                        {!! Form::text('manually_approved_br_no', $appInfo->manually_approved_br_no, ['data-rule-maxlength'=>'100', 'class' => 'form-control input-sm required', 'onblur' => 'checkTrackingNoExists(this)']) !!}
                                                        {!! $errors->first('manually_approved_br_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    {!! Form::label('manually_approved_br_date','Approved Date', ['class'=>'col-md-6 text-left required-star']) !!}
                                                    <div class="col-md-6">
                                                        <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                            {!! Form::text('manually_approved_br_date', (!empty($appInfo->manually_approved_br_date) ? date('d-M-Y', strtotime($appInfo->manually_approved_br_date)) : ''), ['class'=>'form-control input-md datepicker', 'id' => 'manually_approved_br_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="panel panel-info">
                                <div class="panel-heading "><strong>Manually Bida Registration Amendment Info</strong></div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-12 {{$errors->has('is_bra_approval_manually') ? 'has-error': ''}}">
                                                {!! Form::label('is_bra_approval_manually','Did you receive your last BIDA Registration amendment approval manually?',['class'=>'col-md-6 text-left required-star']) !!}
                                                <div class="col-md-6">
                                                    @if($appInfo->is_bra_approval_manually == 'yes')
                                                        <label class="radio-inline">{!! Form::radio('is_bra_approval_manually','yes', ($appInfo->is_bra_approval_manually == 'yes' ? true :false), ['class'=>'cusReadonly required helpTextRadio', 'id' => 'yes']) !!}
                                                            Yes
                                                        </label>
                                                    @endif
                                                    @if($appInfo->is_bra_approval_manually == 'no')
                                                        <label class="radio-inline">{!! Form::radio('is_bra_approval_manually', 'no', ($appInfo->is_bra_approval_manually == 'no' ? true :false), ['class'=>'cusReadonly required', 'id' => 'no']) !!}
                                                            No
                                                        </label>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            @if($appInfo->is_bra_approval_manually == 'yes')
                                                <div class="col-md-12 {{$errors->has('manually_approved_bra_no') ? 'has-error': ''}} mb-2">
                                                    {!! Form::label('manually_approved_bra_no','Please give your manually approved BIDA Registration Amendment memo No. ',['class'=>'col-md-6 required-star text-left']) !!}
                                                    <div class="col-md-6">
                                                        {!! Form::text('manually_approved_bra_no', $appInfo->manually_approved_bra_no, ['data-rule-maxlength'=>'100', 'class' => 'form-control input-sm required', 'onblur' => 'checkTrackingNoExists(this)']) !!}
                                                        {!! $errors->first('manually_approved_bra_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>

                                                <div class="col-md-12 mb-2">
                                                    {!! Form::label('manually_approved_bra_date','Approved Date', ['class'=>'col-md-6 text-left required-star']) !!}
                                                    <div class="col-md-6">
                                                        <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                            {!! Form::text('manually_approved_bra_date', (!empty($appInfo->manually_approved_bra_date)) ? date('d-M-Y',strtotime($appInfo->manually_approved_bra_date)) : '', ['class'=>'form-control input-md datepicker cusReadonly', 'id' => 'manually_approved_bra_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 {{$errors->has('manually_bra_approval_copy') ? 'has-error': ''}}">
                                                    {!! Form::label('manually_bra_approval_copy','Approval Copy',['class'=>'text-left col-md-6 required-star']) !!}
                                                    <div class="col-md-6">
                                                        {!! Form::file('manually_bra_approval_copy', [
                                                            'class' => 'form-control input-md'.(!empty($appInfo->manually_bra_approval_copy) ? '' : ' required'),
                                                            'id' => 'manually_bra_approval_copy_id',
                                                            'multiple' => true,
                                                            'accept' => 'application/pdf',
                                                            'onchange' => 'uploadDocumentProcess(this.id)'
                                                        ]) !!}
                                                        @if(!empty($appInfo->manually_bra_approval_copy))
                                                            <a target="_blank" class="documentUrl btn btn-xs btn-primary"
                                                               href="{{URL::to('/uploads/'.(!empty($appInfo->manually_bra_approval_copy) ? $appInfo->manually_bra_approval_copy : ''))}}">
                                                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                                                Open File
                                                            </a>
                                                        @endif
                                                        {!! $errors->first('manually_bra_approval_copy','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border required-star">Please specify your desired office:
                                </legend>
                                <small class="text-danger">N.B.: Once you save or submit the application, the
                                    selected office cannot be changed anymore.
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
                                                        <h4>You have selected <b>'{{$approval_center->office_name}}
                                                                '</b>, {{ $approval_center->office_address }}
                                                            .</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </fieldset>
                        </fieldset>

                        <h3 class="stepHeader">Registration Info</h3>
                        <fieldset>
                            <div class="panel panel-info">
                                <div class="panel-heading "><strong>A. Company Information</strong></div>
                                <div class="panel-body">
                                    
                                    <div class="clearfix padding"></div>
                                    <table class="table table-responsive table-bordered">
                                        <thead>
                                        <tr>
                                            <td>Field name</td>
                                            <td class="bg-yellow">Existing information (Latest BIDA Reg. Info.)</td>
                                            <td class="bg-green">Proposed information</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td class="required-star">Name of organization/ company</td>

                                            <td class="light-yellow">
                                                {!! Form::text('company_name', $appInfo->company_name, ['class'=>'form-control input-md cusReadonly required', 'id'=>"company_name"]) !!}
                                                {!! $errors->first('company_name','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_company_name]" value="Name of the project"/>
                                                <div class="input-group">
                                                    {!! Form::text('n_company_name', $appInfo->n_company_name, ['class'=>'form-control input-md', 'id'=>"n_company_name", (empty($appInfo->n_company_name) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_company_name]", 1, (empty($appInfo->n_company_name) ? false : true), ['class' => 'field', 'id' => 'n_company_name_check', 'onclick' => "toggleCheckBox('n_company_name_check', ['n_company_name']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_company_name','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">Name of organization/ company (বাংলা)</td>

                                            <td class="light-yellow">
                                                {!! Form::text('company_name_bn', $appInfo->company_name_bn, ['class'=>'form-control input-md cusReadonly required', 'id'=>"company_name_bn"]) !!}
                                                {!! $errors->first('company_name_bn','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_company_name_bn]" value="Name of the project"/>
                                                <div class="input-group">
                                                    {!! Form::text('n_company_name_bn', $appInfo->n_company_name_bn, ['class'=>'form-control input-md', 'id'=>"n_company_name_bn", (empty($appInfo->n_company_name_bn) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_company_name_bn]", 1, (empty($appInfo->n_company_name_bn) ? false : true), ['class' => 'field', 'id' => 'n_company_name_bn_check', 'onclick' => "toggleCheckBox('n_company_name_bn_check', ['n_company_name_bn']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_company_name_bn','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">Name of the project</td>

                                            <td class="light-yellow">
                                                {!! Form::text('project_name', $appInfo->project_name, ['class'=>'form-control input-md cusReadonly required', 'id'=>"project_name"]) !!}
                                                {!! $errors->first('project_name','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_project_name]" value="Name of the project"/>
                                                <div class="input-group">
                                                    {!! Form::text('n_project_name', $appInfo->n_project_name, ['class'=>'form-control input-md', 'id'=>"n_project_name", (empty($appInfo->n_project_name) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_project_name]", 1, (empty($appInfo->n_project_name) ? false : true), ['class' => 'field', 'id' => 'n_project_name_check', 'onclick' => "toggleCheckBox('n_project_name_check', ['n_project_name']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_project_name','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label class="required-star" >Type of the organization</label></td>

                                            <td class="light-yellow">
                                                {!! Form::select('organization_type_id', $eaOrganizationType, $appInfo->organization_type_id, ['class' => 'form-control required cusReadonly input-md ', 'id'=>'organization_type_id']) !!}
                                                {!! $errors->first('organization_type_id','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_organization_type_id]" value="Type of the organization"/>
                                                <div class="input-group">
                                                    {!! Form::select('n_organization_type_id', $eaOrganizationType, $appInfo->n_organization_type_id, ['class' => 'form-control input-md','id'=>'n_organization_type_id', (empty($appInfo->n_organization_type_id) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_organization_type_id]", 1, (empty($appInfo->n_organization_type_id) ? false : true), ['class' => 'field', 'id' => 'n_organization_type_id_check', 'onclick' => "toggleCheckBox('n_organization_type_id_check', ['n_organization_type_id']);"]) !!}
                                                </span>
                                                </div>
                                                {!! $errors->first('n_organization_type_id','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label class="required-star">Status of the organization</label></td>

                                            <td class="light-yellow">
                                                {!! Form::select('organization_status_id', $eaOrganizationStatus, $appInfo->organization_status_id, ['class' => 'form-control input-md required cusReadonly', 'id'=>'organization_status_id', 'onchange' => 'CategoryWiseDocLoad(this.value, "existing", "off")']) !!}
                                                {!! $errors->first('organization_status_id','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_organization_status_id]" value="Status of the organization"/>
                                                <div class="input-group">
                                                    {!! Form::select('n_organization_status_id', $eaOrganizationStatus, $appInfo->n_organization_status_id, ['class' => 'form-control input-md','id'=>'n_organization_status_id', 'onchange' => 'CategoryWiseDocLoad(this.value, "propose", "off")', (empty($appInfo->n_organization_status_id) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_organization_status_id]", 1, (empty($appInfo->n_organization_status_id) ? false : true), ['class' => 'field', 'id' => 'n_organization_status_id_check', 'onclick' => "toggleCheckBox('n_organization_status_id_check', ['n_organization_status_id']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_organization_status_id','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label class="required-star">Ownership status</label></td>

                                            <td class="light-yellow">
                                                {!! Form::select('ownership_status_id', $eaOwnershipStatus, $appInfo->ownership_status_id, ['class' => 'form-control cusReadonly input-md required', 'id'=>'ownership_status_id']) !!}
                                                {{-- {!! Form::select('ownership_status_id', $eaOwnershipStatus, $appInfo->ownership_status_id, ['class' => 'form-control cusReadonly input-md required', 'id'=>'ownership_status_id', 'onchange' => 'CategoryWiseDocLoad(this.value, "existing", "off")']) !!} --}}
                                                {!! $errors->first('ownership_status_id','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_ownership_status_id]" value="Ownership status"/>
                                                <div class="input-group">
                                                    {!! Form::select('n_ownership_status_id', $eaOwnershipStatus, $appInfo->n_ownership_status_id, ['class' => 'form-control input-md','id'=>'n_ownership_status_id', (empty($appInfo->n_ownership_status_id) ? 'disabled' : '')]) !!}
                                                    {{-- {!! Form::select('n_ownership_status_id', $eaOwnershipStatus, $appInfo->n_ownership_status_id, ['class' => 'form-control input-md','id'=>'n_ownership_status_id', 'onchange' => 'CategoryWiseDocLoad(this.value, "propose", "off")', (empty($appInfo->n_ownership_status_id) ? 'disabled' : '')]) !!} --}}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_ownership_status_id]", 1, (empty($appInfo->n_ownership_status_id) ? false : true), ['class' => 'field', 'id' => 'n_ownership_status_id_check', 'onclick' => "toggleCheckBox('n_ownership_status_id_check', ['n_ownership_status_id']);"]) !!}
                                                </span>
                                                </div>
                                                {!! $errors->first('n_ownership_status_id','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label id="country_of_origin_label">Country of Origin</label></td>

                                            <td class="light-yellow">
                                                {!! Form::select('country_of_origin_id', $countries, $appInfo->country_of_origin_id,['class'=>'form-control input-md cusReadonly', 'id' => 'country_of_origin_id']) !!}
                                                {!! $errors->first('country_of_origin_id','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_country_of_origin_id]" value="Country of origin"/>
                                                <div class="input-group">
                                                    {!! Form::select('n_country_of_origin_id', $countries, $appInfo->n_country_of_origin_id,['class'=>'form-control input-md', 'id' => 'n_country_of_origin_id', (empty($appInfo->n_country_of_origin_id) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_country_of_origin_id]", 1, (empty($appInfo->n_country_of_origin_id) ? false : true), ['class' => 'field', 'id' => 'n_country_of_origin_id_check', 'onclick' => "toggleCheckBox('n_country_of_origin_id_check', ['n_country_of_origin_id']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_country_of_origin_id','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <div class="clearfix padding"></div>

                                    <table class="table table-responsive table-bordered top-padding">
                                        <thead>
                                        <tr>
                                            <td colspan="3"><strong>Business Sector</strong></td>
                                        </tr>
                                        <tr>
                                            <td width="22%">Field name</td>
                                            <td class="bg-yellow" width="39%">Existing information (Latest BIDA Reg. Info.)</td>
                                            <td class="bg-green" width="39%">Proposed information</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td class="required-star">Business Sector (BBS Class Code)</td>
                                            <td class="light-yellow">
                                                {!! Form::text('business_class_code', $appInfo->class_code, ['class' => 'form-control required input-md cusReadonly', 'min' => 4, 'id' => 'business_class_code', 'onkeyup' => 'existingFindBusinessClassCode()']) !!}
                                                <span class="help-text" style="margin: 5px 0;">
                                                    <a style="cursor: pointer;" data-toggle="modal" data-target="#businessClassModal" onclick="openBusinessSectorModal(this, 'Existing')" data-action="/bida-registration-amendment/get-business-class-modal">
                                                        Click here to select from the list
                                                    </a>
                                                </span>
                                                {!! $errors->first('business_class_code','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_business_class_code]" value="Business sector code"/>
                                                <div class="input-group">
                                                    {!! Form::text('n_business_class_code', $appInfo->n_class_code, ['class'=>'form-control input-md', 'id'=>"n_business_class_code", 'min' => 4, 'onkeyup' => 'proposedFindBusinessClassCode()', (empty($appInfo->n_class_code)? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_business_class_code]", 1, (empty($appInfo->n_class_code)? false : true), ['class' => 'field', 'id' => 'n_business_class_code_check', 'onclick' => "toggleCheckBox('n_business_class_code_check', ['n_business_class_code']);"]) !!}
                                                </span>
                                                </div>
                                                <span class="help-text {{ empty($appInfo->n_class_code)? "hidden" : "" }}" style="margin: 5px 0;" id="BBSModal">
                                                    <a style="cursor: pointer;" data-toggle="modal" data-target="#businessClassModal" onclick="openBusinessSectorModal(this, 'Proposed')" data-action="/bida-registration-amendment/get-business-class-modal">
                                                        Click here to select from the list
                                                    </a>
                                                </span>
                                                {!! $errors->first('n_business_class_code','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td class="light-yellow">Other info. based on your business class (Code = <span id="ex_business_class_list_of_code"></span>)</td>
                                            <td class="light-green">Other info. based on your business class (Code = <span id="pro_business_class_list_of_code"></span>)</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <div class="bbs-code table-responsive">
                                        <table class="table table-bordered">
                                            <tr>
                                                <td width="22%">Category</td>
                                                <td width="10%" class="light-yellow">Code</td>
                                                <td width="29%" class="light-yellow">Description</td>

                                                <td width="10%" class="light-green">Code</td>
                                                <td width="29%" class="light-green">Description</td>
                                            </tr>
                                            <tr>
                                                <td width="22%" >Section</td>
                                                <td width="10%" class="light-yellow"><span id="ex_section_code"></span></td>
                                                <td width="29%" class="light-yellow"><span id="ex_section_name"></span></td>

                                                <td width="10%" class="light-green"><span id="pro_section_code"></span></td>
                                                <td width="29%" class="light-green"><span id="pro_section_name"></span></td>
                                            </tr>
                                            <tr>
                                                <td width="22%" >Division</td>
                                                <td width="10%" class="light-yellow"><span id="ex_division_code"></span></td>
                                                <td width="29%" class="light-yellow"><span id="ex_division_name"></span></td>

                                                <td width="10%" class="light-green"><span id="pro_division_code"></span></td>
                                                <td width="29%" class="light-green"><span id="pro_division_name"></span></td>
                                            </tr>
                                            <tr>
                                                <td width="22%" >Group</td>
                                                <td width="10%" class="light-yellow"><span id="ex_group_code"></span></td>
                                                <td width="29%" class="light-yellow"><span id="ex_group_name"></span></td>

                                                <td width="10%" class="light-green"><span id="pro_group_code"></span></td>
                                                <td width="29%" class="light-green"><span id="pro_group_name"></span></td>
                                            </tr>
                                            <tr>
                                                <td width="22%" >Class</td>
                                                <td width="10%" class="light-yellow"><span id="ex_class_code"></span></td>
                                                <td width="29%" class="light-yellow"><span id="ex_class_name"></span></td>

                                                <td width="10%" class="light-green"><span id="pro_class_code"></span></td>
                                                <td width="29%" class="light-green"><span id="pro_class_name"></span></td>
                                            </tr>
                                            <tr>
                                                <td width="22%" class="required-star">Sub class</td>
                                                <td colspan="2" class="light-yellow">
                                                    {!! Form::select('sub_class_id', [], null, ['class' => 'form-control required input-md cusReadonly', 'id' => 'sub_class', 'onchange' => "otherSubClassCodeName(this.value, 'existing')"]) !!}
                                                    {!! $errors->first('sub_class_id','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td colspan="2" class="light-green">
                                                    {!! Form::select('n_sub_class_id', [],null, ['class' => 'form-control input-md', 'id' => 'n_sub_class', 'onchange' => "otherSubClassCodeName(this.value, 'propose')", (is_null($appInfo->n_sub_class_id)) ? 'disabled' : '']) !!}
                                                    {!! $errors->first('n_sub_class_id','<span class="help-block">:message</span>') !!}
                                                </td>
                                            </tr>
                                            {{-- {{ dd($appInfo->other_sub_class_code) }} --}}
                                            <tr id="other_sub_class_code_parent">
                                                <td width="20%" class="required-star">Other sub class code</td>
                                                <td colspan="2" class="light-yellow"><input type="text" name="other_sub_class_code" id="other_sub_class_code" class="form-control hidden cusReadonly" value="{{ !empty($appInfo->other_sub_class_code) ? $appInfo->other_sub_class_code : "" }}"></td>
                                                <td colspan="2" class="light-green"><input type="text" name="n_other_sub_class_code" id="n_other_sub_class_code" class="form-control hidden" value="{{ !empty($appInfo->n_other_sub_class_code) ? $appInfo->n_other_sub_class_code : "" }}" {{ empty($appInfo->n_other_sub_class_code) ? 'disabled' : '' }}></td>
                                            </tr>
                                            <tr id="other_sub_class_name_parent">
                                                <td width="20%" class="required-star">Other sub class name</td>
                                                <td colspan="2" class="light-yellow"><input type="text" name="other_sub_class_name" id="other_sub_class_name" class="form-control hidden cusReadonly" value="{{ !empty($appInfo->other_sub_class_name) ? $appInfo->other_sub_class_name : "" }}"></td>
                                                <td colspan="2" class="light-green"><input type="text" name="n_other_sub_class_name" id="n_other_sub_class_name" class="form-control hidden" value="{{ !empty($appInfo->n_other_sub_class_name) ? $appInfo->n_other_sub_class_name : "" }}" {{ empty($appInfo->n_other_sub_class_name) ? 'disabled' : '' }}></td>
                                            </tr>
                                        </table>
                                    </div>

                                </div>
                            </div>

                            <div class="panel panel-info">
                                <div class="panel-heading "><strong>B. Information of Principal Promoter/Chairman/Managing Director/CEO/Country Manager</strong></div>
                                <div class="panel-body">
                                    <table class="table table-responsive table-bordered">
                                        <thead>
                                        <tr>
                                            <td>Field name</td>
                                            <td class="bg-yellow">Existing information (Latest BIDA Reg. Info.)</td>
                                            <td class="bg-green">Proposed information</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td class="required-star">Country</td>

                                            <td class="light-yellow">
                                                {!! Form::select('ceo_country_id', $countries, $appInfo->ceo_country_id, ['class' => 'form-control required  input-md cusReadonly','id'=>'ceo_country_id']) !!}
                                                {!! $errors->first('ceo_country_id','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_ceo_country_id]" value="Principal promoter country"/>
                                                <div class="input-group">
                                                    {!! Form::select('n_ceo_country_id', $countries, $appInfo->n_ceo_country_id, ['class' => 'form-control  input-md ','id'=>'n_ceo_country_id', (empty($appInfo->n_ceo_country_id) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_ceo_country_id]", 1, (empty($appInfo->n_ceo_country_id) ? false : true), ['class' => 'field', 'id' => 'n_ceo_country_id_check', 'onclick' => "toggleCheckBox('n_ceo_country_id_check', ['n_ceo_country_id']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_ceo_country_id','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">Date of Birth</td>

                                            <td class="light-yellow">
                                                <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                    {!! Form::text('ceo_dob', (!empty($appInfo->ceo_dob) ? date('d-M-Y', strtotime($appInfo->ceo_dob)) : ''), ['class'=>'form-control required input-md datepicker cusReadonly', 'id' => 'ceo_dob', 'placeholder'=>'Pick from datepicker']) !!}
                                                </div>
                                                {!! $errors->first('ceo_dob','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_ceo_dob]" value="Principal promoter date of birth"/>
                                                <div class="input-group">
                                                    <div class="date" data-date-format="dd-mm-yyyy">
                                                        {!! Form::text('n_ceo_dob', !empty($appInfo->n_ceo_dob) ? date('d-M-Y', strtotime($appInfo->n_ceo_dob)) : '', ['class'=>'form-control input-md datepicker datepicker-width', 'id' => 'n_ceo_dob', 'placeholder'=>'Pick from datepicker', (empty($appInfo->n_ceo_dob) ? 'disabled' : '')]) !!}
                                                    </div>
                                                    <span class="input-group-addon">
                                                        {!! Form::checkbox("toggleCheck[n_ceo_dob]", 1, (empty($appInfo->n_ceo_dob) ? false : true), ['id' => 'n_ceo_dob_check', 'onclick' => "toggleCheckBox('n_ceo_dob_check', ['n_ceo_dob']);"]) !!}
                                                    </span>
                                                </div>

                                                {!! $errors->first('n_ceo_dob','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">NID/ Passport No.</td>

                                            <td class="light-yellow hidden" id="foreignExistingPassportField">
                                                {!! Form::text('ceo_passport_no', $appInfo->ceo_passport_no,['class'=>'form-control input-md cusReadonly', 'id' => 'ceo_passport_no', 'placeholder' => 'Passport No.']) !!}
                                                {!! $errors->first('ceo_passport_no','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td class="light-yellow hidden" id="BDNIDExistingField">
                                                {!! Form::text('ceo_nid', $appInfo->ceo_nid,['class'=>'form-control input-md cusReadonly', 'id' => 'ceo_nid', 'placeholder' => 'NID']) !!}
                                                {!! $errors->first('ceo_nid','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green hidden" id="foreignProposedPassportField">
                                                <input type="hidden" name="caption[n_ceo_passport_no]" value="Principal promoter passport no."/>
                                                <div class="input-group">
                                                    {!! Form::text('n_ceo_passport_no', $appInfo->n_ceo_passport_no,['class'=>'form-control input-md ', 'placeholder' => 'Passport No.', 'id' => 'n_ceo_passport_no', (empty($appInfo->n_ceo_passport_no) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_ceo_passport_no]", 1, (empty($appInfo->n_ceo_passport_no) ? false:true), ['class' => 'field', 'id' => 'n_ceo_passport_no_check', 'onclick' => "toggleCheckBox('n_ceo_passport_no_check', ['n_ceo_passport_no']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_ceo_passport_no','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td class="light-green hidden" id="BDNIDProposedField">
                                                <input type="hidden" name="caption[n_ceo_nid]" value="Principal promoter NID"/>
                                                <div class="input-group">
                                                    {!! Form::text('n_ceo_nid', $appInfo->n_ceo_nid,['class'=>'form-control input-md', 'placeholder' => 'NID', 'id' => 'n_ceo_nid', (empty($appInfo->n_ceo_nid)? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_ceo_nid]", 1, (empty($appInfo->n_ceo_nid)? false:true), ['class' => 'field', 'id' => 'n_ceo_nid_no_check', 'onclick' => "toggleCheckBox('n_ceo_nid_no_check', ['n_ceo_nid']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_ceo_nid','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">Designation</td>

                                            <td class="light-yellow">
                                                {!! Form::text('ceo_designation', $appInfo->ceo_designation,['class'=>'form-control input-md cusReadonly required', 'id' => 'ceo_designation']) !!}
                                                {!! $errors->first('ceo_designation','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_ceo_designation]" value="Principal promoter designation"/>
                                                <div class="input-group">
                                                    {!! Form::text('n_ceo_designation', $appInfo->n_ceo_designation,['class'=>'form-control input-md', 'id' => 'n_ceo_designation', (empty($appInfo->n_ceo_designation)? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_ceo_designation]", 1, (empty($appInfo->n_ceo_designation)? false : true), ['class' => 'field', 'id' => 'n_ceo_designation_check', 'onclick' => "toggleCheckBox('n_ceo_designation_check', ['n_ceo_designation']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_ceo_designation','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">Full Name</td>

                                            <td class="light-yellow">
                                                {!! Form::text('ceo_full_name', $appInfo->ceo_full_name,['class'=>'form-control input-md cusReadonly required', 'id' => 'ceo_full_name']) !!}
                                                {!! $errors->first('ceo_full_name','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_ceo_full_name]" value="Principal promoter full name"/>
                                                <div class="input-group">
                                                    {!! Form::text('n_ceo_full_name', $appInfo->n_ceo_full_name,['class'=>'form-control input-md', 'id' => 'n_ceo_full_name', (empty($appInfo->n_ceo_full_name) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_ceo_full_name]", 1, (empty($appInfo->n_ceo_full_name) ? false:true), ['class' => 'field', 'id' => 'n_ceo_full_name_check', 'onclick' => "toggleCheckBox('n_ceo_full_name_check', ['n_ceo_full_name']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_ceo_full_name','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">District/ City/ State</td>
                                            
                                            <td class="light-yellow hidden" id="foreignExistingCity">
                                                {!! Form::text('ceo_city', $appInfo->ceo_city,['class'=>'form-control input-md required cusReadonly', 'id' => 'ceo_City', 'placeholder' => 'District/ City/ State']) !!}
                                                {!! $errors->first('ceo_city','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td class="light-yellow hidden" id="BDExistingDistrict">
                                                {!! Form::select('ceo_district_id', $districts, $appInfo->ceo_district_id,['class'=>'form-control input-md  cusReadonly', 'id' => 'ceo_district_id', 'onchange'=>"getThanaByDistrictId('ceo_district_id', this.value, 'ceo_thana_id')"]) !!}
                                                {!! $errors->first('ceo_district_id','<span class="help-block">:message</span>') !!}
                                            </td>

                                                

                                            <td class="light-green hidden" id="foreignProposedCity">
                                                <input type="hidden" name="caption[n_ceo_city]" value="Principal promoter city"/>
                                                <div class="input-group">
                                                    {!! Form::text('n_ceo_city', $appInfo->n_ceo_city,['class'=>'form-control input-md', 'id' => 'n_ceo_city', (empty($appInfo->n_ceo_city)? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_ceo_city]", 1, (empty($appInfo->n_ceo_city)? false : true), ['class' => 'field', 'id' => 'n_ceo_City_check', 'onclick' => "toggleCheckBox('n_ceo_City_check', ['n_ceo_city']);"]) !!}
                                                    </span>
                                                </div>

                                                {!! $errors->first('n_ceo_city','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td class="light-green hidden" id="BDProposedDistrict">
                                                <input type="hidden" name="caption[n_ceo_district_id]" value="Principal promoter district"/>
                                                <div class="input-group">
                                                    {!! Form::select('n_ceo_district_id', $districts, $appInfo->n_ceo_district_id,['class'=>'form-control input-md', 'id' => 'n_ceo_district_id', 'onchange'=>"getThanaByDistrictId('n_ceo_district_id', this.value, 'n_ceo_thana_id')", (empty($appInfo->n_ceo_district_id)? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_ceo_district_id]", 1, (empty($appInfo->n_ceo_district_id)? false:true), ['class' => 'field', 'id' => 'n_ceo_district_id_check', 'onclick' => "toggleCheckBox('n_ceo_district_id_check', ['n_ceo_district_id']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_ceo_district_id','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">State/ Province/ Police station/ Town</td>

                                            <td class="light-yellow hidden" id="foreignExistingState">
                                                {!! Form::text('ceo_state', $appInfo->ceo_state,['class'=>'form-control input-md cusReadonly', 'id' => 'ceo_state', 'placeholder' => 'State/ Province']) !!}
                                                {!! $errors->first('ceo_state','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-yellow hidden" id="BDExistingTown">
                                                {!! Form::select('ceo_thana_id', $thana, $appInfo->ceo_thana_id,['class'=>'form-control input-md cusReadonly', 'id' => 'ceo_thana_id']) !!}
                                                {!! $errors->first('ceo_thana_id','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green hidden" id="foreignProposedState">
                                                <input type="hidden" name="caption[n_ceo_state]" value="Principal promoter state"/>
                                                <div class="input-group">
                                                    {!! Form::text('n_ceo_state', $appInfo->n_ceo_state,['class'=>'form-control input-md', 'id' => 'n_ceo_state', 'placeholder' => 'State/ Province', (empty($appInfo->n_ceo_state) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_ceo_state]", 1, (empty($appInfo->n_ceo_state) ? false:true), ['class' => 'field', 'id' => 'n_ceo_state_check', 'onclick' => "toggleCheckBox('n_ceo_state_check', ['n_ceo_state']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_ceo_state','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td class="light-green hidden" id="BDProposedTown">
                                                <input type="hidden" name="caption[n_ceo_thana_id]" value="Principal promoter police station"/>
                                                <div class="input-group">
                                                    {!! Form::select('n_ceo_thana_id', [], $appInfo->n_ceo_thana_id,['class'=>'form-control input-md', 'id' => 'n_ceo_thana_id', 'placeholder' => 'Select District First', (empty($appInfo->n_ceo_thana_id) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_ceo_thana_id]", 1, (empty($appInfo->n_ceo_thana_id) ? false:true), ['class' => 'field', 'id' => 'n_ceo_thana_id_check', 'onclick' => "toggleCheckBox('n_ceo_thana_id_check', ['n_ceo_thana_id']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_ceo_thana_id','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">Post/ Zip Code</td>

                                            <td class="light-yellow">
                                                {!! Form::text('ceo_post_code', $appInfo->ceo_post_code,['class'=>'form-control input-md cusReadonly required', 'id' => 'ceo_post_code']) !!}
                                                {!! $errors->first('ceo_post_code','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_ceo_post_code]" value="Principal promoter post/ zip code"/>
                                                <div class="input-group">
                                                    {!! Form::text('n_ceo_post_code', $appInfo->n_ceo_post_code,['class'=>'form-control input-md', 'id' => 'n_ceo_post_code', (empty($appInfo->n_ceo_post_code)? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_ceo_post_code]", 1, (empty($appInfo->n_ceo_post_code)? false:true), ['class' => 'field', 'id' => 'n_ceo_post_code_check', 'onclick' => "toggleCheckBox('n_ceo_post_code_check', ['n_ceo_post_code']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_ceo_post_code','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">House, Flat/ Apartment, Road</td>

                                            <td class="light-yellow">
                                                {!! Form::text('ceo_address', $appInfo->ceo_address,['class'=>'form-control input-md cusReadonly', 'id' => 'ceo_address']) !!}
                                                {!! $errors->first('ceo_address','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_ceo_address]" value="Principal promoter house, flat/ apartment, road"/>
                                                <div class="input-group">
                                                    {!! Form::text('n_ceo_address', $appInfo->n_ceo_address,['class'=>'form-control input-md required', 'id' => 'n_ceo_address', (empty($appInfo->n_ceo_address) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_ceo_address]", 1, (empty($appInfo->n_ceo_address) ? false:true), ['class' => 'field', 'id' => 'n_ceo_address_check', 'onclick' => "toggleCheckBox('n_ceo_address_check', ['n_ceo_address']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_ceo_address','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Telephone No.</td>
                                            
                                            <td class="light-yellow">
                                                {!! Form::text('ceo_telephone_no', $appInfo->ceo_telephone_no,['class'=>'form-control input-md cusReadonly', 'id' => 'ceo_telephone_no']) !!}
                                                {!! $errors->first('ceo_telephone_no','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_ceo_telephone_no]" value="Principal promoter telephone no."/>
                                                <div class="input-group mobile-plugin">
                                                    {!! Form::text('n_ceo_telephone_no', $appInfo->n_ceo_telephone_no,['class'=>'form-control input-md', 'id' => 'n_ceo_telephone_no', (empty($appInfo->n_ceo_telephone_no) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon" style="padding: 8px 24px 6px 12px;">
                                                    {!! Form::checkbox("toggleCheck[n_ceo_telephone_no]", 1, (empty($appInfo->n_ceo_telephone_no) ? false:true), ['class' => 'field', 'id' => 'n_ceo_telephone_no_check', 'onclick' => "toggleCheckBox('n_ceo_telephone_no_check', ['n_ceo_telephone_no']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_ceo_telephone_no','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">Mobile No.</td>

                                            <td class="light-yellow">
                                                {!! Form::text('ceo_mobile_no', $appInfo->ceo_mobile_no,['class'=>'form-control input-md cusReadonly required', 'id' => 'ceo_mobile_no']) !!}
                                                {!! $errors->first('ceo_mobile_no','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_ceo_mobile_no]" value="Principal promoter mobile no."/>
                                                <div class="input-group mobile-plugin">
                                                    {!! Form::text('n_ceo_mobile_no', $appInfo->n_ceo_mobile_no,['class'=>'form-control input-md', 'id' => 'n_ceo_mobile_no', (empty($appInfo->n_ceo_mobile_no)? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon" style="padding: 8px 24px 6px 12px;">
                                                    {!! Form::checkbox("toggleCheck[n_ceo_mobile_no]", 1, (empty($appInfo->n_ceo_mobile_no)? false:true), ['class' => 'field', 'id' => 'n_ceo_mobile_no_check', 'onclick' => "toggleCheckBox('n_ceo_mobile_no_check', ['n_ceo_mobile_no']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_ceo_mobile_no','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">Email</td>

                                            <td class="light-yellow">
                                                {!! Form::email('ceo_email', $appInfo->ceo_email,['class'=>'form-control input-md cusReadonly required', 'id' => 'ceo_email']) !!}
                                                {!! $errors->first('ceo_email','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_ceo_email]" value="Principal promoter email"/>
                                                <div class="input-group">
                                                    {!! Form::email('n_ceo_email', $appInfo->n_ceo_email,['class'=>'form-control input-md', 'id' => 'n_ceo_email', (empty($appInfo->n_ceo_email) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_ceo_email]", 1, (empty($appInfo->n_ceo_email) ? false:true), ['class' => 'field', 'id' => 'n_ceo_email_check', 'onclick' => "toggleCheckBox('n_ceo_email_check', ['n_ceo_email']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_ceo_email','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Fax No.</td>

                                            <td class="light-yellow">
                                                {!! Form::text('ceo_fax_no', $appInfo->ceo_fax_no,['class'=>'form-control input-md cusReadonly', 'id' => 'ceo_fax_no']) !!}
                                                {!! $errors->first('ceo_fax_no','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_ceo_fax_no]" value="Principal promoter fax no."/>
                                                <div class="input-group">
                                                    {!! Form::text('n_ceo_fax_no', $appInfo->n_ceo_fax_no,['class'=>'form-control input-md', 'id' => 'n_ceo_fax_no', (empty($appInfo->n_ceo_fax_no) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_ceo_fax_no]", 1, (empty($appInfo->n_ceo_fax_no) ? false:true), ['class' => 'field', 'id' => 'n_ceo_fax_no_check', 'onclick' => "toggleCheckBox('n_ceo_fax_no_check', ['n_ceo_fax_no']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_ceo_fax_no','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">Father's Name</td>
                                            
                                            <td class="light-yellow">
                                                {!! Form::text('ceo_father_name', $appInfo->ceo_father_name,['class'=>'form-control input-md cusReadonly required', 'id' => 'ceo_father_name']) !!}
                                                {!! $errors->first('ceo_father_name','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_ceo_father_name]" value="Principal promoter father's name"/>
                                                <div class="input-group">
                                                    {!! Form::text('n_ceo_father_name', $appInfo->n_ceo_father_name,['class'=>'form-control input-md', 'id' => 'n_ceo_father_name', (empty($appInfo->n_ceo_father_name) ? 'disabled' : '' )]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_ceo_father_name]", 1, (empty($appInfo->n_ceo_father_name) ? false:true), ['class' => 'field', 'id' => 'n_ceo_father_name_check', 'onclick' => "toggleCheckBox('n_ceo_father_name_check', ['n_ceo_father_name']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_ceo_father_name','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">Mother's Name</td>

                                            <td class="light-yellow">
                                                {!! Form::text('ceo_mother_name', $appInfo->ceo_mother_name,['class'=>'form-control input-md cusReadonly', 'id' => 'ceo_mother_name']) !!}
                                                {!! $errors->first('ceo_mother_name','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_ceo_mother_name]" value="Principal promoter mother's name"/>
                                                <div class="input-group">
                                                    {!! Form::text('n_ceo_mother_name', $appInfo->n_ceo_mother_name,['class'=>'form-control input-md required', 'id' => 'n_ceo_mother_name', (empty($appInfo->n_ceo_mother_name) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_ceo_mother_name]", 1, (empty($appInfo->n_ceo_mother_name) ? false:true), ['class' => 'field', 'id' => 'n_ceo_mother_name_check', 'onclick' => "toggleCheckBox('n_ceo_mother_name_check', ['n_ceo_mother_name']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_ceo_mother_name','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Spouse name</td>

                                            <td class="light-yellow">
                                                {!! Form::text('ceo_spouse_name', $appInfo->ceo_spouse_name,['class'=>'form-control input-md cusReadonly', 'id' => 'ceo_spouse_name']) !!}
                                                {!! $errors->first('ceo_spouse_name','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_ceo_spouse_name]" value="Principal promoter spouse name"/>
                                                <div class="input-group">
                                                    {!! Form::text('n_ceo_spouse_name', $appInfo->n_ceo_spouse_name,['class'=>'form-control input-md', 'id' => 'n_ceo_spouse_name', (empty($appInfo->n_ceo_spouse_name) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_ceo_spouse_name]", 1, (empty($appInfo->n_ceo_spouse_name) ? false:true), ['class' => 'field', 'id' => 'n_ceo_spouse_name_check', 'onclick' => "toggleCheckBox('n_ceo_spouse_name_check', ['n_ceo_spouse_name']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_ceo_spouse_name','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">Gender</td>

                                            <td class="light-yellow">
                                                <label class="radio-inline">{!! Form::radio('ceo_gender','male', ($appInfo->ceo_gender == 'Male' ? true : false), ['class' => 'cusReadonly required']) !!}  Male</label>
                                                <label class="radio-inline">{!! Form::radio('ceo_gender', 'female', ($appInfo->ceo_gender == 'Female' ? true : false), ['class' => 'cusReadonly required']) !!}  Female</label>
                                                {!! $errors->first('ceo_gender','<span class="help-block">:message</span>') !!}
                                            </td>
                                            
                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_ceo_gender]" value="Principal promoter gender"/>
                                                <div class="input-group">
                                                    <label class="radio-inline">{!! Form::radio('n_ceo_gender','male', ($appInfo->n_ceo_gender == 'Male' ? true : false), ['class'=>'', 'id'=>'n_male', (empty($appInfo->n_ceo_gender) ? 'disabled' : '')]) !!}  Male</label>
                                                    <label class="radio-inline">{!! Form::radio('n_ceo_gender', 'female', ($appInfo->n_ceo_gender == 'Female' ? true : false), ['class'=>'', 'id'=>'n_female', (empty($appInfo->n_ceo_gender) ? 'disabled' : '')]) !!}  Female</label>
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_ceo_gender]", 1, (empty($appInfo->n_ceo_gender) || $appInfo->ceo_gender == 'Not defined' ? false : true), ['class' => 'field', 'id' => 'n_ceo_gender_check', 'onclick' => "toggleCheckBox('n_ceo_gender_check', ['n_male', 'n_female']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_ceo_gender_check','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="panel panel-info">
                                <div class="panel-heading "><strong>C. Office Address</strong></div>
                                <div class="panel-body">
                                    <table class="table table-responsive table-bordered">
                                        <thead>
                                        <tr>
                                            <td width="30%">Field name</td>
                                            <td class="bg-yellow" width="35%">Existing information (Latest BIDA Reg. Info.)</td>
                                            <td class="bg-green" width="35%">Proposed information</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td class="required-star">Division</td>

                                            <td class="light-yellow">
                                                {!! Form::select('office_division_id', $divisions, $appInfo->office_division_id,['class'=>'form-control required input-md cusReadonly', 'id' => 'office_division_id', 'onchange'=>"getDistrictByDivisionId('office_division_id', this.value, 'office_district_id', ". $appInfo->office_district_id .")"]) !!}
                                                {!! $errors->first('office_division_id','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_office_division_id]" value="Office division"/>
                                                <div class="input-group">
                                                    {!! Form::select('n_office_division_id', $divisions, $appInfo->n_office_division_id,['class'=>'form-control input-md', 'id' => 'n_office_division_id', 'onchange'=>"getDistrictByDivisionId('n_office_division_id', this.value, 'n_office_district_id', ". $appInfo->n_office_district_id .")", (empty($appInfo->n_office_division_id) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_office_division_id]", 1, (empty($appInfo->n_office_division_id) ? false:true), ['class' => 'field', 'id' => 'n_office_division_id_check', 'onclick' => "toggleCheckBox('n_office_division_id_check', ['n_office_division_id']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_office_division_id','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">District</td>
                                            <td class="light-yellow">
                                                {!! Form::select('office_district_id', $districts, $appInfo->office_district_id,['class'=>'form-control required input-md cusReadonly', 'id' => 'office_district_id', 'placeholder' => 'Select Division First', 'onchange'=>"getThanaByDistrictId('office_district_id', this.value, 'office_thana_id', ". $appInfo->office_thana_id .")"]) !!}
                                                {!! $errors->first('office_district_id','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_office_district_id]" value="Office district"/>
                                                <div class="input-group">
                                                    {!! Form::select('n_office_district_id', $districts, $appInfo->n_office_district_id,['class'=>'form-control input-md', 'id' => 'n_office_district_id', 'placeholder' => 'Select Division First', 'onchange'=>"getThanaByDistrictId('n_office_district_id', this.value, 'n_office_thana_id', ". $appInfo->n_office_thana_id .")", (empty($appInfo->n_office_district_id) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_office_district_id]", 1, (empty($appInfo->n_office_district_id) ? false : true), ['class' => 'field', 'id' => 'n_office_district_id_check', 'onclick' => "toggleCheckBox('n_office_district_id_check', ['n_office_district_id']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_office_district_id','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">Police Station</td>
                                            <td class="light-yellow">
                                                {!! Form::select('office_thana_id', [], $appInfo->office_thana_id,['class'=>'form-control required input-md cusReadonly', 'id' => 'office_thana_id', 'placeholder' => 'Select District First']) !!}
                                                {!! $errors->first('office_thana_id','<span class="help-block">:message</span>') !!}
                                            </td>

                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_office_thana_id]" value="Office police station"/>
                                                <div class="input-group">
                                                    {!! Form::select('n_office_thana_id',[], $appInfo->n_office_thana_id,['class'=>'form-control input-md', 'id' => 'n_office_thana_id', 'placeholder' => 'Select District First', (empty($appInfo->n_office_thana_id) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_office_thana_id]", 1, (empty($appInfo->n_office_thana_id) ? false : true), ['class' => 'field', 'id' => 'n_office_thana_id_check', 'onclick' => "toggleCheckBox('n_office_thana_id_check', ['n_office_thana_id']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_office_thana_id','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">Post Office</td>
                                            <td class="light-yellow">
                                                {!! Form::text('office_post_office', $appInfo->office_post_office,['class'=>'form-control required input-md cusReadonly', 'id' => 'office_post_office']) !!}
                                                {!! $errors->first('office_post_office','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_office_post_office]" value="Office post office"/>
                                                <div class="input-group">
                                                    {!! Form::text('n_office_post_office', $appInfo->n_office_post_office,['class'=>'form-control input-md', 'id' => 'n_office_post_office', (empty($appInfo->n_office_post_office) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_office_post_office]", 1, (empty($appInfo->n_office_post_office) ? false:true), ['class' => 'field', 'id' => 'n_office_post_office_check', 'onclick' => "toggleCheckBox('n_office_post_office_check', ['n_office_post_office']);"]) !!}
                                                </span>
                                                </div>
                                                {!! $errors->first('n_office_post_office','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">Post Code</td>
                                            <td class="light-yellow">
                                                {!! Form::text('office_post_code', $appInfo->office_post_code,['class'=>'form-control required input-md alphaNumeric cusReadonly', 'id' => 'office_post_code']) !!}
                                                {!! $errors->first('office_post_code','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_office_post_code]" value="Office post code"/>
                                                <div class="input-group">
                                                    {!! Form::text('n_office_post_code', $appInfo->n_office_post_code,['class'=>'form-control input-md alphaNumeric', 'id' => 'n_office_post_code', (empty($appInfo->n_office_post_code) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_office_post_code]", 1, (empty($appInfo->n_office_post_code) ? false:true), ['class' => 'field', 'id' => 'n_office_post_code_check', 'onclick' => "toggleCheckBox('n_office_post_code_check', ['n_office_post_code']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_office_post_code','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">Address</td>
                                            <td class="light-yellow">
                                                {!! Form::text('office_address', $appInfo->office_address,['class'=>'form-control required input-md cusReadonly', 'id' => 'office_address']) !!}
                                                {!! $errors->first('office_address','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_office_address]" value="Office address"/>
                                                <div class="input-group">
                                                    {!! Form::text('n_office_address', $appInfo->n_office_address,['class'=>'form-control input-md', 'id' => 'n_office_address', (empty($appInfo->n_office_address) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_office_address]", 1, (empty($appInfo->n_office_address) ? false : true), ['class' => 'field', 'id' => 'n_office_address_check', 'onclick' => "toggleCheckBox('n_office_address_check', ['n_office_address']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_office_address','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Telephone No.</td>
                                            <td class="light-yellow">
                                                {!! Form::text('office_telephone_no', $appInfo->office_telephone_no,['class'=>'form-control input-md cusReadonly', 'id' => 'office_telephone_no']) !!}
                                                {!! $errors->first('office_telephone_no','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_office_telephone_no]" value="Office telephone no."/>
                                                <div class="input-group mobile-plugin">
                                                    {!! Form::text('n_office_telephone_no', $appInfo->n_office_telephone_no,['class'=>'form-control input-md', 'id' => 'n_office_telephone_no', (empty($appInfo->n_office_telephone_no) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon" style="padding: 8px 24px 6px 12px;">
                                                    {!! Form::checkbox("toggleCheck[n_office_telephone_no]", 1, (empty($appInfo->n_office_telephone_no) ? false:true), ['class' => 'field', 'id' => 'n_office_telephone_no_check', 'onclick' => "toggleCheckBox('n_office_telephone_no_check', ['n_office_telephone_no']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_office_telephone_no','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">Mobile No.</td>
                                            <td class="light-yellow">
                                                {!! Form::text('office_mobile_no', $appInfo->office_mobile_no,['class'=>'form-control required input-md cusReadonly', 'id' => 'office_mobile_no']) !!}
                                                {!! $errors->first('office_mobile_no','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_office_mobile_no]" value="Office mobile no."/>
                                                <div class="input-group mobile-plugin">
                                                    {!! Form::text('n_office_mobile_no', $appInfo->n_office_mobile_no,['class'=>'form-control input-md', 'id' => 'n_office_mobile_no', (empty($appInfo->n_office_mobile_no) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon" style="padding: 8px 24px 6px 12px;">
                                                    {!! Form::checkbox("toggleCheck[n_office_mobile_no]", 1, (empty($appInfo->n_office_mobile_no) ? false:true), ['class' => 'field', 'id' => 'n_office_mobile_no_check', 'onclick' => "toggleCheckBox('n_office_mobile_no_check', ['n_office_mobile_no']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_office_mobile_no','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Fax No.</td>
                                            <td class="light-yellow">
                                                {!! Form::text('office_fax_no', $appInfo->office_fax_no,['class'=>'form-control input-md cusReadonly', 'id' => 'office_fax_no']) !!}
                                                {!! $errors->first('office_fax_no','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_office_fax_no]" value="Office fax no."/>
                                                <div class="input-group">
                                                    {!! Form::text('n_office_fax_no', $appInfo->n_office_fax_no,['class'=>'form-control input-md', 'id' => 'n_office_fax_no', (empty($appInfo->n_office_fax_no) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_office_fax_no]", 1, (empty($appInfo->n_office_fax_no) ? false:true), ['class' => 'field', 'id' => 'n_office_fax_no_check', 'onclick' => "toggleCheckBox('n_office_fax_no_check', ['n_office_fax_no']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_office_fax_no','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">Email </td>
                                            <td class="light-yellow">
                                                {!! Form::email('office_email', $appInfo->office_email,['class'=>'form-control input-md required cusReadonly', 'id' => 'office_email']) !!}
                                                {!! $errors->first('office_email','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_office_email]" value="Office email"/>
                                                <div class="input-group">
                                                    {!! Form::email('n_office_email', $appInfo->n_office_email,['class'=>'form-control input-md', 'id' => 'n_office_email', (empty($appInfo->n_office_email) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_office_email]", 1, (empty($appInfo->n_office_email) ? false : true), ['class' => 'field', 'id' => 'n_office_email_check', 'onclick' => "toggleCheckBox('n_office_email_check', ['n_office_email']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_office_email','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="panel panel-info">
                                <div class="panel-heading "><strong>D. Factory Address</strong></div>
                                <div class="panel-body">
                                    <table class="table table-responsive table-bordered">
                                        <thead>
                                        <tr>
                                            <td width="30%">Field name</td>
                                            <td class="bg-yellow" width="35%">Existing information (Latest BIDA Reg. Info.)</td>
                                            <td class="bg-green" width="35%">Proposed information</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td class="required-star">District</td>
                                            <td class="light-yellow">
                                                {!! Form::select('factory_district_id', $districts, $appInfo->factory_district_id,['class'=>'form-control   required input-md cusReadonly', 'id' => 'factory_district_id', 'onchange'=>"getThanaByDistrictId('factory_district_id', this.value, 'factory_thana_id', ". $appInfo->factory_thana_id .")"]) !!}
                                                {!! $errors->first('factory_district_id','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_factory_district_id]" value="Factory district"/>
                                                <div class="input-group">
                                                    {!! Form::select('n_factory_district_id', $districts, $appInfo->n_factory_district_id,['class'=>'form-control input-md', 'id' => 'n_factory_district_id', 'onchange'=>"getThanaByDistrictId('n_factory_district_id', this.value, 'n_factory_thana_id', ". $appInfo->n_factory_thana_id .")", (empty($appInfo->n_factory_district_id) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_factory_district_id]", 1, (empty($appInfo->n_factory_district_id) ? false : true), ['class' => 'field', 'id' => 'n_factory_district_id_check', 'onclick' => "toggleCheckBox('n_factory_district_id_check', ['n_factory_district_id']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_factory_district_id','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">Police Station</td>
                                            <td class="light-yellow">
                                                {!! Form::select('factory_thana_id', [], $appInfo->factory_thana_id,['class'=>'form-control required input-md cusReadonly', 'placeholder' => 'Select District First','id' => 'factory_thana_id']) !!}
                                                {!! $errors->first('factory_thana_id','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_factory_thana_id]" value="Factory police station"/>
                                                <div class="input-group">
                                                    {!! Form::select('n_factory_thana_id',[], $appInfo->n_factory_thana_id,['class'=>'form-control input-md', 'id' => 'n_factory_thana_id', 'placeholder' => 'Select District First', (empty($appInfo->n_factory_thana_id) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_factory_thana_id]", 1, (empty($appInfo->n_factory_thana_id) ? false:true), ['class' => 'field', 'id' => 'n_factory_thana_id_check', 'onclick' => "toggleCheckBox('n_factory_thana_id_check', ['n_factory_thana_id']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_factory_thana_id','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">Post Office</td>
                                            <td class="light-yellow">
                                                {!! Form::text('factory_post_office', $appInfo->factory_post_office,['class'=>'form-control required input-md cusReadonly', 'id' => 'factory_post_office']) !!}
                                                {!! $errors->first('factory_post_office','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_factory_post_office]" value="Factory post office"/>
                                                <div class="input-group">
                                                    {!! Form::text('n_factory_post_office', $appInfo->n_factory_post_office,['class'=>'form-control input-md', 'id' => 'n_factory_post_office', (empty($appInfo->n_factory_post_office) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_factory_post_office]", 1, (empty($appInfo->n_factory_post_office) ? false:true), ['class' => 'field', 'id' => 'n_factory_post_office_check', 'onclick' => "toggleCheckBox('n_factory_post_office_check', ['n_factory_post_office']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_factory_post_office','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">Post Code</td>
                                            <td class="light-yellow">
                                                {!! Form::text('factory_post_code', $appInfo->factory_post_code,['class'=>'form-control required input-md alphaNumeric cusReadonly', 'id' => 'factory_post_code']) !!}
                                                {!! $errors->first('factory_post_code','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_factory_post_code]" value="Factory post code"/>
                                                <div class="input-group">
                                                    {!! Form::text('n_factory_post_code', $appInfo->n_factory_post_code,['class'=>'form-control input-md alphaNumeric', 'id' => 'n_factory_post_code', (empty($appInfo->n_factory_post_code) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_factory_post_code]", 1, (empty($appInfo->n_factory_post_code) ? false:true), ['class' => 'field', 'id' => 'n_factory_post_code_check', 'onclick' => "toggleCheckBox('n_factory_post_code_check', ['n_factory_post_code']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_factory_post_code','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">Address</td>
                                            <td class="light-yellow">
                                                {!! Form::text('factory_address', $appInfo->factory_address,['class'=>'form-control required input-md cusReadonly', 'id' => 'factory_address']) !!}
                                                {!! $errors->first('factory_address','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_factory_address]" value="Factory address"/>
                                                <div class="input-group">
                                                    {!! Form::text('n_factory_address', $appInfo->n_factory_address,['class'=>'form-control input-md', 'id' => 'n_factory_address', (empty($appInfo->n_factory_address) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_factory_address]", 1, (empty($appInfo->n_factory_address) ? false:true), ['class' => 'field', 'id' => 'n_factory_address_check', 'onclick' => "toggleCheckBox('n_factory_address_check', ['n_factory_address']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_factory_address','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Telephone No.</td>
                                            <td class="light-yellow">
                                                {!! Form::text('factory_telephone_no', $appInfo->factory_telephone_no,['class'=>'form-control input-md cusReadonly', 'id' => 'factory_telephone_no']) !!}
                                                {!! $errors->first('factory_telephone_no','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_factory_telephone_no]" value="Factory telephone no."/>
                                                <div class="input-group mobile-plugin">
                                                    {!! Form::text('n_factory_telephone_no', $appInfo->n_factory_telephone_no,['class'=>'form-control input-md', 'id' => 'n_factory_telephone_no', (empty($appInfo->n_factory_telephone_no) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon" style="padding: 8px 24px 6px 12px;">
                                                    {!! Form::checkbox("toggleCheck[n_factory_telephone_no]", 1, (empty($appInfo->n_factory_telephone_no) ? false:true), ['class' => 'field', 'id' => 'n_factory_telephone_no_check', 'onclick' => "toggleCheckBox('n_factory_telephone_no_check', ['n_factory_telephone_no']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_factory_telephone_no','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="required-star">Mobile No.</td>
                                            <td class="light-yellow">
                                                {!! Form::text('factory_mobile_no', $appInfo->factory_mobile_no,['class'=>'form-control required input-md cusReadonly', 'id' => 'factory_mobile_no']) !!}
                                                {!! $errors->first('factory_mobile_no','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_factory_mobile_no]" value="Factory mobile no."/>
                                                <div class="input-group mobile-plugin">
                                                    {!! Form::text('n_factory_mobile_no', $appInfo->n_factory_mobile_no,['class'=>'form-control input-md', 'id' => 'n_factory_mobile_no', (empty($appInfo->n_factory_mobile_no) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon" style="padding: 8px 24px 6px 12px;">
                                                    {!! Form::checkbox("toggleCheck[n_factory_mobile_no]", 1, (empty($appInfo->n_factory_mobile_no) ? false:true), ['class' => 'field', 'id' => 'n_factory_mobile_no_check', 'onclick' => "toggleCheckBox('n_factory_mobile_no_check', ['n_factory_mobile_no']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_factory_mobile_no','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Fax No.</td>
                                            <td class="light-yellow">
                                                {!! Form::text('factory_fax_no', $appInfo->factory_fax_no,['class'=>'form-control input-md cusReadonly', 'id' => 'factory_fax_no']) !!}
                                                {!! $errors->first('factory_fax_no','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td class="light-green">
                                                <input type="hidden" name="caption[n_factory_fax_no]" value="Factory fax no."/>
                                                <div class="input-group">
                                                    {!! Form::text('n_factory_fax_no', $appInfo->n_factory_fax_no,['class'=>'form-control input-md', 'id' => 'n_factory_fax_no', (empty($appInfo->n_factory_fax_no) ? 'disabled' : '')]) !!}
                                                    <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_factory_fax_no]", 1, (empty($appInfo->n_factory_fax_no) ? false:true), ['class' => 'field', 'id' => 'n_factory_fax_no_check', 'onclick' => "toggleCheckBox('n_factory_fax_no_check', ['n_factory_fax_no']);"]) !!}
                                                    </span>
                                                </div>
                                                {!! $errors->first('n_factory_fax_no','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="panel panel-info">
                                <div class="panel-heading "><strong>Registration Information</strong></div>
                                <div class="panel-body">
                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border">1. Project status</legend>
                                        <table class="table table-responsive table-bordered">
                                            <thead>
                                            <tr>
                                                <td width="30%">Field name</td>
                                                <td class="bg-yellow" width="35%">Existing information (Latest BIDA Reg. Info.)</td>
                                                <td class="bg-green" width="35%">Proposed information</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td class="required-star">Project status</td>
                                                <td class="light-yellow">
                                                    {!! Form::select('project_status_id', $projectStatusList, $appInfo->project_status_id,['class'=>'form-control required input-md cusReadonly', 'id' => 'project_status_id']) !!}
                                                    {!! $errors->first('project_status_id','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td class="light-green">
                                                    <input type="hidden" name="caption[n_project_status_id]" value="Project status"/>
                                                    <div class="input-group">
                                                        {!! Form::select('n_project_status_id', $projectStatusList, $appInfo->n_project_status_id,['class'=>'form-control input-md', 'id' => 'n_project_status_id', (empty($appInfo->n_project_status_id) ? 'disabled' : '')]) !!}
                                                        <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_project_status_id]", 1, (empty($appInfo->n_project_status_id) ? false : true), ['class' => 'field', 'id' => 'n_project_status_id_check', 'onclick' => "toggleCheckBox('n_project_status_id_check', ['n_project_status_id']);"]) !!}
                                                </span>
                                                    </div>
                                                    {!! $errors->first('n_project_status_id','<span class="help-block">:message</span>') !!}
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </fieldset>

                                    <div class="panel panel-info">
                                        <div class="panel-heading ">
                                            <div class="pull-left">
                                                <strong>2. Annual production capacity</strong>
                                            </div>
                                            <div class="pull-right">
                                                <a data-toggle="modal" data-target="#braModal" onclick="openBraModal(this, 'braModal')"
                                                   data-action="{{ url('bida-registration-amendment/apc-form/'.Encryption::encodeId($appInfo->id)) }}">
                                                    {!! Form::button('Add Annual Production Capacity', array('type' => 'button', 'class' => 'btn btn-md btn-info')) !!}
                                                </a>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <div id="load_apc_data"></div>
                                            </div>
                                            @if($annualProductionCapacity > 20)
                                                <br>
                                                <a href="javascript:void(0)" onclick="loadAnnualProductionCapacityData('all', 'off')" id="apc_data">Load more data</a>
                                            @endif
                                        </div>
                                    </div>

                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border">3. Date of commercial operation</legend>
                                        <table class="table table-responsive table-bordered">
                                            <thead>
                                            <tr>
                                                <td width="30%">Field name</td>
                                                <td class="bg-yellow" width="35%">Existing information (Latest BIDA Reg. Info.)</td>
                                                <td class="bg-green" width="35%">Proposed information</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td class="required-star">Date of commercial operation</td>
                                                <td class="light-yellow">
                                                    <div class="input-group date" data-date-format="dd-mm-yyyy">
                                                        {!! Form::text('commercial_operation_date', (!empty($appInfo->commercial_operation_date) ? date('d-M-Y', strtotime($appInfo->commercial_operation_date)) : ''), ['class'=>'form-control required input-md datepicker cusReadonly', 'id' => 'commercial_operation_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                    </div>
                                                    {!! $errors->first('commercial_operation_date','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td class="light-green">
                                                    <input type="hidden" name="caption[n_commercial_operation_date]" value="Date of commercial operation"/>
                                                    <div class="input-group">
                                                        <div class="date" data-date-format="dd-mm-yyyy">
                                                            {!! Form::text('n_commercial_operation_date', (!empty($appInfo->n_commercial_operation_date) ? date('d-M-Y', strtotime($appInfo->n_commercial_operation_date)) : ''), ['class'=>'form-control input-md datepicker co-datepicker-width', 'id' => 'n_commercial_operation_date', 'placeholder'=>'Pick from datepicker', (empty($appInfo->n_commercial_operation_date) ? 'disabled' : '')]) !!}
                                                        </div>
                                                        <span class="input-group-addon">
                                                        {!! Form::checkbox("toggleCheck[n_commercial_operation_date]", 1, (empty($appInfo->n_commercial_operation_date) ? false : true), ['class' => 'field', 'id' => 'n_commercial_operation_date_check', 'onclick' => "toggleCheckBox('n_commercial_operation_date_check', ['n_commercial_operation_date']);"]) !!}
                                                        </span>
                                                    </div>
                                                    {!! $errors->first('n_commercial_operation_date','<span class="help-block">:message</span>') !!}
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </fieldset>

                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border">4. Sales (in 100%)</legend>
                                        <table class="table table-responsive table-bordered">
                                            <thead>
                                            <tr>
                                                <td>
                                                    <span>
                                                        {!! Form::checkbox("multiToggleCheck[n_local_sales]", 1, (is_null($appInfo->n_local_sales) ? false : true), ['class' => 'field', 'id' => 'n_foreign_sales_check', 'onclick' => "toggleCheckBox('n_foreign_sales_check', ['n_local_sales',  'n_foreign_sales']);"]) !!}
                                                    </span>
                                                </td>
                                                <td width="30%">Field name</td>
                                                <td class="bg-yellow" width="35%">Existing information (Latest BIDA Reg. Info.)</td>
                                                <td class="bg-green" width="35%">Proposed information</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td></td>
                                                <td class="required-star">Local</td>
                                                <td class="light-yellow">
                                                    {!! Form::text('local_sales', $appInfo->local_sales,['class'=>'form-control required input-md cusReadonly', 'id' => 'local_sales_per']) !!}
                                                    {!! $errors->first('local_sales','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td class="light-green">
                                                    <div class="form-group">
                                                        {!! Form::text('n_local_sales', $appInfo->n_local_sales,['class'=>'form-control input-md', 'id' => 'n_local_sales', (is_null($appInfo->n_local_sales) ? 'disabled' : '')]) !!}
                                                    </div>
                                                    {!! $errors->first('n_local_sales','<span class="help-block">:message</span>') !!}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Foreign</td>
                                                <td class="light-yellow">
                                                    {!! Form::text('foreign_sales', $appInfo->foreign_sales,['class'=>'form-control input-md cusReadonly', 'id' => 'foreign_sales_per']) !!}
                                                    {!! $errors->first('foreign_sales','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td class="light-green">
                                                    <div class="form-group">
                                                        {!! Form::text('n_foreign_sales', $appInfo->n_foreign_sales,['class'=>'form-control input-md', 'id' => 'n_foreign_sales', ($appInfo->local_sales > 20 && empty($appInfo->n_foreign_sales) ? 'disabled' : '')]) !!}
                                                    </div>
                                                    {!! $errors->first('n_foreign_sales','<span class="help-block">:message</span>') !!}
                                                </td>
                                            </tr>
                                            {{-- <tr>
                                                <td></td>
                                                <td class="required-star">Direct Export</td>
                                                <td class="light-yellow">
                                                    {!! Form::text('direct_export', $appInfo->direct_export,['class'=>'form-control required input-md cusReadonly', 'id' => 'direct_export_per']) !!}
                                                    {!! $errors->first('direct_export','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td class="light-green">
                                                    <div class="form-group">
                                                        {!! Form::text('n_direct_export', $appInfo->n_direct_export,['class'=>'form-control input-md', 'id' => 'n_direct_export', (empty($appInfo->n_direct_export) ? 'disabled' : '')]) !!}
                                                    </div>
                                                    {!! $errors->first('n_direct_export','<span class="help-block">:message</span>') !!}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td class="required-star">Deemed Export</td>
                                                <td class="light-yellow">
                                                    {!! Form::text('deemed_export', $appInfo->deemed_export,['class'=>'form-control input-md required cusReadonly', 'id' => 'deemed_export_per']) !!}
                                                    {!! $errors->first('deemed_export','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td class="light-green">
                                                    <div class="form-group">
                                                        {!! Form::text('n_deemed_export', $appInfo->n_deemed_export,['class'=>'form-control input-md', 'id' => 'n_deemed_export', (empty($appInfo->n_deemed_export) ? 'disabled' : '')]) !!}
                                                    </div>
                                                    {!! $errors->first('n_deemed_export','<span class="help-block">:message</span>') !!}
                                                </td>
                                            </tr> --}}
                                            <tr>
                                                <td></td>
                                                <td class="required-star">Total in %</td>
                                                <td class="light-yellow">
                                                    {!! Form::text('total_sales', $appInfo->total_sales,['class'=>'form-control required input-md cusReadonly', 'id' => 'total_sales', 'readonly' => 'readonly', 'max' => '100']) !!}
                                                    {!! $errors->first('total_sales','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td class="light-green">
                                                    <div class="form-group">
                                                        {!! Form::number('n_total_sales', $appInfo->n_total_sales,['class'=>'form-control input-md', 'id' => 'n_total_sales', 'readonly' => 'readonly', 'max' => '100']) !!}
                                                    </div>
                                                    {!! $errors->first('n_total_sales','<span class="help-block">:message</span>') !!}
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </fieldset>

                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border">5. Manpower of the organization</legend>
                                        <table class="table table-responsive table-bordered">
                                            <thead>
                                            <tr>
                                                <td>#</td>
                                                <td colspan="4">Local (Bangladesh only)</td>
                                                <td colspan="3">Foreign (Abroad country)</td>
                                                <td>Grand total</td>
                                                <td colspan="2">Ratio</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td></td>
                                                <th>Information</th>
                                                <th class="required-star">Executive</th>
                                                <th class="required-star">Supporting Staff</th>
                                                <th class="required-star">Total (a)</th>

                                                <th class="required-star">Executive</th>
                                                <th class="required-star">Supporting Staff</th>
                                                <th class="required-star">Total (b)</th>

                                                <th class="required-star">(a+b)</th>

                                                <th class="required-star">Local</th>
                                                <th class="required-star">Foreign</th>
                                            </tr>
                                            <tr class="light-yellow" id="exiting_manpower">
                                                <td class="bg-yellow"></td>
                                                <td class="bg-yellow">Existing (Latest BIDA Reg. Info.)</td>
                                                <td>
                                                    {!! Form::text('local_male', $appInfo->local_male,['class'=>'form-control required input-md cusReadonly', 'id' => 'local_male']) !!}
                                                    {!! $errors->first('local_male','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td>
                                                    {!! Form::text('local_female', $appInfo->local_female,['class'=>'form-control required input-md cusReadonly', 'id' => 'local_female']) !!}
                                                    {!! $errors->first('local_female','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td>
                                                    {!! Form::text('local_total', $appInfo->local_total,['class'=>'form-control required input-md cusReadonly', 'id' => 'local_total', 'readonly']) !!}
                                                    {!! $errors->first('local_total','<span class="help-block">:message</span>') !!}
                                                </td>

                                                <td>
                                                    {!! Form::text('foreign_male', $appInfo->foreign_male,['class'=>'form-control required input-md cusReadonly', 'id' => 'foreign_male']) !!}
                                                    {!! $errors->first('foreign_male','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td>
                                                    {!! Form::text('foreign_female', $appInfo->foreign_female,['class'=>'form-control input-md cusReadonly', 'id' => 'foreign_female']) !!}
                                                    {!! $errors->first('foreign_female','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td>
                                                    {!! Form::text('foreign_total', $appInfo->foreign_total,['class'=>'form-control required input-md cusReadonly', 'id' => 'foreign_total', 'readonly']) !!}
                                                    {!! $errors->first('foreign_total','<span class="help-block">:message</span>') !!}
                                                </td>

                                                <td>
                                                    {!! Form::text('manpower_total', $appInfo->manpower_total,['class'=>'form-control required input-md cusReadonly', 'id' => 'mp_total', 'readonly']) !!}
                                                    {!! $errors->first('manpower_total','<span class="help-block">:message</span>') !!}
                                                </td>

                                                <td>
                                                    {!! Form::text('manpower_local_ratio', $appInfo->manpower_local_ratio,['class'=>'form-control required input-md cusReadonly', 'id' => 'mp_ratio_local', 'readonly']) !!}
                                                    {!! $errors->first('manpower_local_ratio','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td>
                                                    {!! Form::text('manpower_foreign_ratio', $appInfo->manpower_foreign_ratio,['class'=>'form-control required input-md cusReadonly', 'id' => 'mp_ratio_foreign', 'readonly']) !!}
                                                    {!! $errors->first('manpower_foreign_ratio','<span class="help-block">:message</span>') !!}
                                                </td>
                                            </tr>

                                            <tr class="light-green" id="proposed_manpower">
                                                <td class="bg-green">
                                                    <span>
                                                        {!! Form::checkbox("multiToggleCheck[n_local_male]", 1, ((is_null($appInfo->n_local_male)  || is_null($appInfo->n_local_female) || is_null($appInfo->n_foreign_male) || is_null($appInfo->n_foreign_female)) ? false : true), ['class' => 'field', 'id' => 'n_manpower_check', 'onclick' => "toggleCheckBox('n_manpower_check', ['n_local_male', 'n_local_female', 'n_foreign_male', 'n_foreign_female']);"]) !!}
                                                    </span>
                                                </td>
                                                <td class="bg-green">Proposed</td>
                                                <td>
                                                    {!! Form::text('n_local_male', $appInfo->n_local_male,['class'=>'form-control input-md', 'id' => 'n_local_male', is_null($appInfo->n_local_male) ? 'disabled' : '']) !!}
                                                    {!! $errors->first('n_local_male','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td>
                                                    {!! Form::text('n_local_female', $appInfo->n_local_female,['class'=>'form-control input-md', 'id' => 'n_local_female', is_null($appInfo->n_local_female) ? 'disabled' : '']) !!}
                                                    {!! $errors->first('n_local_female','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td>
                                                    {!! Form::text('n_local_total', $appInfo->n_local_total,['class'=>'form-control input-md', 'id' => 'n_local_total', 'readonly']) !!}
                                                    {!! $errors->first('n_local_total','<span class="help-block">:message</span>') !!}
                                                </td>

                                                <td>
                                                    {!! Form::text('n_foreign_male', $appInfo->n_foreign_male,['class'=>'form-control input-md', 'id' => 'n_foreign_male', is_null($appInfo->n_foreign_male) ? 'disabled' : '']) !!}
                                                    {!! $errors->first('n_foreign_male','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td>
                                                    {!! Form::text('n_foreign_female', $appInfo->n_foreign_female,['class'=>'form-control input-md', 'id' => 'n_foreign_female', is_null($appInfo->n_foreign_female) ? 'disabled' : '']) !!}
                                                    {!! $errors->first('n_foreign_female','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td>
                                                    {!! Form::text('n_foreign_total', $appInfo->n_foreign_total,['class'=>'form-control input-md', 'id' => 'n_foreign_total', 'readonly']) !!}
                                                    {!! $errors->first('n_foreign_total','<span class="help-block">:message</span>') !!}
                                                </td>

                                                <td>
                                                    {!! Form::text('n_manpower_total', $appInfo->n_manpower_total,['class'=>'form-control input-md', 'id' => 'n_manpower_total', 'readonly']) !!}
                                                    {!! $errors->first('n_manpower_total','<span class="help-block">:message</span>') !!}
                                                </td>

                                                <td>
                                                    {!! Form::text('n_manpower_local_ratio', $appInfo->n_manpower_local_ratio,['class'=>'form-control input-md', 'id' => 'n_manpower_local_ratio', 'readonly']) !!}
                                                    {!! $errors->first('n_manpower_local_ratio','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td>
                                                    {!! Form::text('n_manpower_foreign_ratio', $appInfo->n_manpower_foreign_ratio,['class'=>'form-control input-md', 'id' => 'n_manpower_foreign_ratio', 'readonly']) !!}
                                                    {!! $errors->first('n_manpower_foreign_ratio','<span class="help-block">:message</span>') !!}
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </fieldset>

                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border">6. Investment</legend>
                                        <div class="alert alert-danger" role="alert">
                                            <strong>Note:</strong> If you want to amend any data from the (Investment/ Source of finance/ Country wise source of finance) sections, you must have to Input/update all the proposed information with the existing information of the three sections.
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-responsive table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                <tr>
                                                    <td colspan="6">Items</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="1">
                                                        {!! Form::checkbox("multiToggleCheck[investment_sources_of_finance]", 1, (($appInfo->n_local_land_ivst != null || $appInfo->n_local_building_ivst != null || $appInfo->n_local_machinery_ivst != null || $appInfo->n_local_others_ivst != null || $appInfo->n_total_fixed_ivst_million != null ||
                                                        $appInfo->n_usd_exchange_rate != null || $appInfo->n_total_fee != null || $appInfo->n_finance_src_loc_equity_1 != null ||  $appInfo->n_finance_src_foreign_equity_1 != null ||
                                                        $appInfo->n_finance_src_loc_total_equity_1 != null || $appInfo->n_finance_src_loc_loan_1 != null || $appInfo->n_finance_src_loc_loan_1 != null || $appInfo->n_finance_src_foreign_loan_1 != null ||
                                                        $appInfo->n_finance_src_total_loan != null || $appInfo->n_finance_src_loc_total_financing_m != null     
                                                        ) ? true : false),
                                                        ['class' => 'field', 'id' => 'investmentAndSourceOfFinance_check',
                                                            'onclick' => "toggleCheckBoxForSourceOfFinance('investmentAndSourceOfFinance_check', ['n_local_land_ivst', 'n_local_building_ivst','n_local_machinery_ivst', 'n_local_others_ivst',
                                                            'n_local_wc_ivst', 'n_usd_exchange_rate',
                                                            'n_finance_src_loc_equity_1', 'n_finance_src_foreign_equity_1', 'n_finance_src_loc_loan_1', 'n_finance_src_foreign_loan_1'], ['n_country_id', 'n_equity_amount', 'n_loan_amount'], ['n_total_fixed_ivst_million', 'n_total_fixed_ivst', 'n_total_fee', 'n_finance_src_loc_total_equity_1', 'n_finance_src_total_loan', 'n_finance_src_loc_total_financing_m']);"]) !!}
                                                    </td>
                                                    <td width="30%">Fixed Investment</td>
                                                    <td class="bg-yellow" width="35%" colspan="2">Existing information (Latest BIDA Reg. Info.)</td>
                                                    <td class="bg-green" width="35%" colspan="2">Proposed information</td>
                                                </tr>

                                                </thead>
                                                <tbody>

                                                <tr>
                                                    <td></td>
                                                    <td><span>Land (Million)</span></td>
                                                    <td class="light-yellow">
                                                        <div class="{{ $errors->has('local_land_ivst')?'has-error':'' }}">
                                                            {!! Form::text('local_land_ivst', $appInfo->local_land_ivst,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow cusReadonly', 'id' => 'local_land_ivst', 'onblur' => 'CalculateTotalExistingInvestmentTk()']) !!}
                                                            {!! $errors->first('local_land_ivst','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                    <td class="light-yellow">
                                                        <div class="{{ $errors->has('local_land_ivst_ccy')?'has-error':'' }}">
                                                            {!! Form::select('local_land_ivst_ccy', $currencyBDT, $appInfo->local_land_ivst_ccy,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow cusReadonly', 'id' => 'local_land_ivst_ccy', 'readonly']) !!}
                                                            {!! $errors->first('local_land_ivst_ccy','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                    <td class="light-green">
                                                        <div class="{{ $errors->has('n_local_land_ivst')?'has-error':'' }}">
                                                            {!! Form::text('n_local_land_ivst', $appInfo->n_local_land_ivst,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'n_local_land_ivst', 'onblur' => 'CalculateTotalProposeInvestmentTk()']) !!}
                                                            {!! $errors->first('n_local_land_ivst','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>

                                                    <td class="light-green">
                                                        <div class="{{ $errors->has('n_local_land_ivst_ccy')?'has-error':'' }}">
                                                            {!! Form::select('n_local_land_ivst_ccy', $currencyBDT, $appInfo->n_local_land_ivst_ccy,['data-rule-maxlength'=>'40','class' => 'form-control input-md yellow', 'id' => 'n_local_land_ivst_ccy', 'readonly']) !!}
                                                            {!! $errors->first('n_local_land_ivst_ccy','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td><span> Building (Million)</span></td>
                                                    <td class="light-yellow">
                                                        <div class="{{ $errors->has('local_building_ivst')?'has-error':'' }}">
                                                            {!! Form::text('local_building_ivst', $appInfo->local_building_ivst,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow cusReadonly', 'id' => 'local_building_ivst', 'onblur' => 'CalculateTotalExistingInvestmentTk()']) !!}
                                                            {!! $errors->first('local_building_ivst','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>

                                                    <td class="light-yellow">
                                                        <div class="{{ $errors->has('local_building_ivst_ccy')?'has-error':'' }}">
                                                            {!! Form::select('local_building_ivst_ccy', $currencyBDT, $appInfo->local_building_ivst_ccy,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow cusReadonly',  'id' => 'local_building_ivst_ccy', 'readonly']) !!}
                                                            {!! $errors->first('local_building_ivst_ccy','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                    <td class="light-green">
                                                        <div class="{{ $errors->has('n_local_building_ivst')?'has-error':'' }}">
                                                            {!! Form::text('n_local_building_ivst', $appInfo->n_local_building_ivst,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'n_local_building_ivst', 'onblur' => 'CalculateTotalProposeInvestmentTk()']) !!}
                                                            {!! $errors->first('n_local_building_ivst','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                    <td class="light-green">
                                                        <div class="{{ $errors->has('n_local_building_ivst_ccy')?'has-error':'' }}">
                                                            {!! Form::select('n_local_building_ivst_ccy', $currencyBDT, $appInfo->n_local_building_ivst_ccy,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'n_local_building_ivst_ccy', 'readonly']) !!}
                                                            {!! $errors->first('n_local_building_ivst_ccy','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td></td>
                                                    <td><span class="required-star"> Machinery & Equipment (Million)</span></td>
                                                    <td class="light-yellow">
                                                        <div class="{{ $errors->has('local_machinery_ivst')?'has-error':'' }}">
                                                            {!! Form::text('local_machinery_ivst', $appInfo->local_machinery_ivst,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow required cusReadonly', 'id' => 'local_machinery_ivst', 'onblur' => 'CalculateTotalExistingInvestmentTk()']) !!}
                                                            {!! $errors->first('local_machinery_ivst','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                    <td class="light-yellow">
                                                        <div class="{{ $errors->has('local_machinery_ivst_ccy')?'has-error':'' }}">
                                                            {!! Form::select('local_machinery_ivst_ccy', $currencyBDT, $appInfo->local_machinery_ivst_ccy,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow cusReadonly', 'id' => 'local_machinery_ivst_ccy', 'readonly']) !!}
                                                            {!! $errors->first('local_machinery_ivst_ccy','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                    <td class="light-green">
                                                        <div class="{{ $errors->has('n_local_machinery_ivst')?'has-error':'' }}">
                                                            {!! Form::text('n_local_machinery_ivst', $appInfo->n_local_machinery_ivst,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'n_local_machinery_ivst', 'onblur' => 'CalculateTotalProposeInvestmentTk()']) !!}
                                                            {!! $errors->first('n_local_machinery_ivst','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>

                                                    <td class="light-green">
                                                        <div class="{{ $errors->has('n_local_machinery_ivst_ccy')?'has-error':'' }}">
                                                            {!! Form::select('n_local_machinery_ivst_ccy', $currencyBDT, $appInfo->n_local_machinery_ivst_ccy,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'n_local_machinery_ivst_ccy', 'readonly']) !!}
                                                            {!! $errors->first('n_local_machinery_ivst_ccy','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td></td>
                                                    <td><span> Others (Million)</span></td>

                                                    <td class="light-yellow">
                                                        <div class="{{ $errors->has('local_others_ivst')?'has-error':'' }}">
                                                            {!! Form::text('local_others_ivst', $appInfo->local_others_ivst,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow cusReadonly', 'id' => 'local_others_ivst', 'onblur' => 'CalculateTotalExistingInvestmentTk()']) !!}
                                                            {!! $errors->first('local_others_ivst','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>

                                                    <td class="light-yellow">
                                                        <div class="{{ $errors->has('local_others_ivst_ccy')?'has-error':'' }}">
                                                            {!! Form::select('local_others_ivst_ccy', $currencyBDT, $appInfo->local_others_ivst_ccy,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow cusReadonly', 'id' => 'local_others_ivst_ccy', 'readonly']) !!}
                                                            {!! $errors->first('local_others_ivst_ccy','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                    <td class="light-green">
                                                        <div class="{{ $errors->has('n_local_others_ivst')?'has-error':'' }}">
                                                            {!! Form::text('n_local_others_ivst', $appInfo->n_local_others_ivst,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'n_local_others_ivst', 'onblur' => 'CalculateTotalProposeInvestmentTk()']) !!}
                                                            {!! $errors->first('n_local_others_ivst','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>

                                                    <td class="light-green">
                                                        <div class="{{ $errors->has('n_local_others_ivst_ccy')?'has-error':'' }}">
                                                            {!! Form::select('n_local_others_ivst_ccy', $currencyBDT, $appInfo->n_local_others_ivst_ccy,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'n_local_others_ivst_ccy', 'readonly']) !!}
                                                            {!! $errors->first('n_local_others_ivst_ccy','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td></td>
                                                    <td><span> Working Capital (Three Months) (Million)</span></td>

                                                    <td class="light-yellow">
                                                        <div class="{{ $errors->has('local_wc_ivst')?'has-error':'' }}">
                                                            {!! Form::text('local_wc_ivst', $appInfo->local_wc_ivst,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow cusReadonly', 'id' => 'local_wc_ivst', 'onblur' => 'CalculateTotalExistingInvestmentTk()']) !!}
                                                            {!! $errors->first('local_wc_ivst','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>

                                                    <td class="light-yellow">
                                                        <div class="{{ $errors->has('local_wc_ivst_ccy')?'has-error':'' }}">
                                                            {!! Form::select('local_wc_ivst_ccy', $currencyBDT, $appInfo->local_wc_ivst_ccy,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow cusReadonly', 'id' => 'local_wc_ivst_ccy', 'readonly']) !!}
                                                            {!! $errors->first('local_wc_ivst_ccy','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                    <td class="light-green">
                                                        <div class="{{ $errors->has('n_local_wc_ivst')?'has-error':'' }}">
                                                            {!! Form::text('n_local_wc_ivst', $appInfo->n_local_wc_ivst,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'n_local_wc_ivst', 'onblur' => 'CalculateTotalProposeInvestmentTk()']) !!}
                                                            {!! $errors->first('n_local_wc_ivst','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>

                                                    <td class="light-green">
                                                        <div class="{{ $errors->has('n_local_wc_ivst_ccy')?'has-error':'' }}">
                                                            {!! Form::select('n_local_wc_ivst_ccy', $currencyBDT, $appInfo->n_local_wc_ivst_ccy,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'n_local_wc_ivst_ccy', 'readonly']) !!}
                                                            {!! $errors->first('n_local_wc_ivst_ccy','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td></td>
                                                    <td>
                                                        <span> Total Investment (Million) (BDT)</span>
                                                    </td>

                                                    <td class="light-yellow" colspan="2">
                                                        <div class="{{ $errors->has('total_fixed_ivst_million')?'has-error':'' }}">
                                                            {!! Form::text('total_fixed_ivst_million', $appInfo->total_fixed_ivst_million,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'total_fixed_ivst_million', 'readonly']) !!}
                                                            {!! $errors->first('total_fixed_ivst_million','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>

                                                    <td class="light-green" colspan="2">
                                                        <div class="{{ $errors->has('n_total_fixed_ivst_million')?'has-error':'' }}">
                                                            {!! Form::text('n_total_fixed_ivst_million', $appInfo->n_total_fixed_ivst_million,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'n_total_fixed_ivst_million', 'readonly']) !!}
                                                            {!! $errors->first('n_total_fixed_ivst_million','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td></td>
                                                    <td><span>  Total Investment (BDT)</span></td>
                                                    <td class="light-yellow" colspan="2">
                                                        <div class="{{ $errors->has('total_fixed_ivst')?'has-error':'' }}">
                                                            {!! Form::text('total_fixed_ivst', $appInfo->total_fixed_ivst,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'total_fixed_ivst', 'readonly']) !!}
                                                            {!! $errors->first('total_fixed_ivst','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>

                                                    <td class="light-green" colspan="2">
                                                        <div class="{{ $errors->has('n_total_fixed_ivst')?'has-error':'' }}">
                                                            {!! Form::text('n_total_fixed_ivst', $appInfo->n_total_fixed_ivst,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'n_total_fixed_ivst', 'readonly']) !!}
                                                            {!! $errors->first('n_total_fixed_ivst','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td></td>
                                                    <td><span>  Dollar exchange rate (USD)</span></td>
                                                    <td class="light-yellow" colspan="2">
                                                        <div class="{{ $errors->has('usd_exchange_rate')?'has-error':'' }}">
                                                            {!! Form::text('usd_exchange_rate', $appInfo->usd_exchange_rate,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow cusReadonly', 'id' => 'usd_exchange_rate']) !!}
                                                            {!! $errors->first('usd_exchange_rate','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>

                                                    <td class="light-green" colspan="2">
                                                        <div class="{{ $errors->has('n_usd_exchange_rate')?'has-error':'' }}">
                                                            {!! Form::text('n_usd_exchange_rate', $appInfo->n_usd_exchange_rate,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'n_usd_exchange_rate']) !!}
                                                            {!! $errors->first('n_usd_exchange_rate','<span class="help-block">:message</span>') !!}
                                                        </div>

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="7"><span class="help-text pull-right">Exchange Rate Ref: <a href="https://www.bb.org.bd/econdata/exchangerate.php" target="_blank">Bangladesh Bank</a>. Please Enter Today's Exchange Rate</span></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td><span>   Total Fee (BDT)</span></td>

                                                    <td class="light-yellow" colspan="2">
                                                        <div class="{{ $errors->has('total_fee')?'has-error':'' }}">
                                                            {!! Form::text('total_fee', $appInfo->total_fee,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'total_fee', 'readonly']) !!}
                                                            {!! $errors->first('total_fee','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>

                                                    <td class="light-green" colspan="2">
                                                        <div class="{{ $errors->has('n_total_fee')?'has-error':'' }}">
                                                            {!! Form::text('n_total_fee', $appInfo->n_total_fee,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'n_total_fee', 'readonly']) !!}
                                                            {!! $errors->first('n_total_fee','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>

                                                    {{--                                                    <td class="light-green">--}}
                                                    {{--                                                        <a type="button" class="btn btn-md btn-info"--}}
                                                    {{--                                                           data-toggle="modal" data-target="#govFeesCalculator">Govt.--}}
                                                    {{--                                                            Fees Calculator</a>--}}
                                                    {{--                                                    </td>--}}
                                                </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </fieldset>

                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border">7. Source of finance</legend>
                                        <div class="table-responsive">
                                            <table class="table table-responsive table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                <tr>
                                                    <td colspan="6">Items</td>
                                                </tr>
                                                <tr>
                                                    <td width="30%">Fixed Investment</td>
                                                    <td class="bg-yellow" width="35%">Existing information (Latest BIDA Reg. Info.)</td>
                                                    <td class="bg-green" width="35%">Proposed information</td>
                                                </tr>

                                                </thead>
                                                <tbody>

                                                <tr>
                                                    <td><span>Local Equity (Million)</span></td>

                                                    <td class="light-yellow">
                                                        <div class="{{ $errors->has('finance_src_loc_equity_1')?'has-error':'' }}">
                                                            {!! Form::text('finance_src_loc_equity_1', $appInfo->finance_src_loc_equity_1,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow cusReadonly', 'id' => 'finance_src_loc_equity_1', 'onblur'=>"calculateSourceOfFinanceForExisting()"]) !!}
                                                            {!! $errors->first('finance_src_loc_equity_1','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>

                                                    <td class="light-green">
                                                        <div class="{{ $errors->has('n_finance_src_loc_equity_1')?'has-error':'' }}">
                                                            {!! Form::text('n_finance_src_loc_equity_1', $appInfo->n_finance_src_loc_equity_1,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'n_finance_src_loc_equity_1', 'onblur' => 'calculateSourceOfFinanceForPropose()']) !!}
                                                            {!! $errors->first('n_finance_src_loc_equity_1','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><span>Foreign Equity (Million)</span></td>
                                                    <td class="light-yellow">
                                                        <div class="{{ $errors->has('finance_src_foreign_equity_1')?'has-error':'' }}">
                                                            {!! Form::text('finance_src_foreign_equity_1', $appInfo->finance_src_foreign_equity_1,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow cusReadonly', 'id' => 'finance_src_foreign_equity_1', 'onblur'=>"calculateSourceOfFinanceForExisting()"]) !!}
                                                            {!! $errors->first('finance_src_foreign_equity_1','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>

                                                    <td class="light-green">
                                                        <div class="{{ $errors->has('n_finance_src_foreign_equity_1')?'has-error':'' }}">
                                                            {!! Form::text('n_finance_src_foreign_equity_1', $appInfo->n_finance_src_foreign_equity_1,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'n_finance_src_foreign_equity_1', 'onblur' => 'calculateSourceOfFinanceForPropose()']) !!}
                                                            {!! $errors->first('n_finance_src_foreign_equity_1','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><span><strong>Total Equity (a)</strong></span></td>
                                                    <td class="light-yellow">
                                                        <div class="{{ $errors->has('finance_src_loc_total_equity_1')?'has-error':'' }}">
                                                            {!! Form::text('finance_src_loc_total_equity_1', $appInfo->finance_src_loc_total_equity_1,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'finance_src_loc_total_equity_1', 'readonly']) !!}
                                                            {!! $errors->first('finance_src_loc_total_equity_1','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>

                                                    <td class="light-green">
                                                        <div class="{{ $errors->has('n_finance_src_loc_total_equity_1')?'has-error':'' }}">
                                                            {!! Form::text('n_finance_src_loc_total_equity_1', $appInfo->n_finance_src_loc_total_equity_1,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'n_finance_src_loc_total_equity_1', 'readonly']) !!}
                                                            {!! $errors->first('n_finance_src_loc_total_equity_1','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><span>Local Loan (Million)</span></td>
                                                    <td class="light-yellow">
                                                        <div class="{{ $errors->has('finance_src_loc_loan_1')?'has-error':'' }}">
                                                            {!! Form::text('finance_src_loc_loan_1', $appInfo->finance_src_loc_loan_1,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow cusReadonly', 'id' => 'finance_src_loc_loan_1', 'onblur'=>"calculateSourceOfFinanceForExisting()"]) !!}
                                                            {!! $errors->first('finance_src_loc_loan_1','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>

                                                    <td class="light-green">
                                                        <div class="{{ $errors->has('n_finance_src_loc_loan_1')?'has-error':'' }}">
                                                            {!! Form::text('n_finance_src_loc_loan_1', $appInfo->n_finance_src_loc_loan_1,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'n_finance_src_loc_loan_1', 'onblur' => 'calculateSourceOfFinanceForPropose()']) !!}
                                                            {!! $errors->first('n_finance_src_loc_loan_1','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><span> Foreign Loan (Million)</span></td>
                                                    <td class="light-yellow">
                                                        <div class="{{ $errors->has('finance_src_foreign_loan_1')?'has-error':'' }}">
                                                            {!! Form::text('finance_src_foreign_loan_1', $appInfo->finance_src_foreign_loan_1,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow cusReadonly', 'id' => 'finance_src_foreign_loan_1', 'onblur'=>"calculateSourceOfFinanceForExisting()"]) !!}
                                                            {!! $errors->first('finance_src_foreign_loan_1','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                    <td class="light-green">
                                                        <div class="{{ $errors->has('n_finance_src_foreign_loan_1')?'has-error':'' }}">
                                                            {!! Form::text('n_finance_src_foreign_loan_1', $appInfo->n_finance_src_foreign_loan_1,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'n_finance_src_foreign_loan_1', 'onblur' => 'calculateSourceOfFinanceForPropose()']) !!}
                                                            {!! $errors->first('n_finance_src_foreign_loan_1','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><span><strong>Total Loan (b)</strong></span></td>
                                                    <td class="light-yellow">
                                                        <div class="{{ $errors->has('finance_src_total_loan')?'has-error':'' }}">
                                                            {!! Form::text('finance_src_total_loan', $appInfo->finance_src_total_loan,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'finance_src_total_loan', 'readonly']) !!}
                                                            {!! $errors->first('finance_src_total_loan','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>

                                                    <td class="light-green">
                                                        <div class="{{ $errors->has('n_finance_src_total_loan')?'has-error':'' }}">
                                                            {!! Form::text('n_finance_src_total_loan', $appInfo->n_finance_src_total_loan,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'n_finance_src_total_loan', 'readonly']) !!}
                                                            {!! $errors->first('n_finance_src_total_loan','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><span><strong>Total Financing (Million) (a+b)</strong></span></td>
                                                    <td class="light-yellow">
                                                        <div class="{{ $errors->has('finance_src_loc_total_financing_m')?'has-error':'' }}">
                                                            {!! Form::text('finance_src_loc_total_financing_m', $appInfo->finance_src_loc_total_financing_m,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'finance_src_loc_total_financing_m', 'readonly']) !!}
                                                            {!! $errors->first('finance_src_loc_total_financing_m','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>

                                                    <td class="light-green">
                                                        <div class="{{ $errors->has('n_finance_src_loc_total_financing_m')?'has-error':'' }}">
                                                            {!! Form::text('n_finance_src_loc_total_financing_m', $appInfo->n_finance_src_loc_total_financing_m,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'n_finance_src_loc_total_financing_m', 'readonly']) !!}
                                                            {!! $errors->first('n_finance_src_loc_total_financing_m','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><span><strong>Total Financing (BDT) (a+b)</strong></span></td>
                                                    <td class="light-yellow">
                                                        <div class="{{ $errors->has('finance_src_loc_total_financing_1')?'has-error':'' }}">
                                                            {!! Form::text('finance_src_loc_total_financing_1', $appInfo->finance_src_loc_total_financing_1,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'finance_src_loc_total_financing_1', 'readonly']) !!}
                                                            {!! $errors->first('finance_src_loc_total_financing_1','<span class="help-block">:message</span>') !!}

                                                            <span class="text-danger" style="font-size: 12px; font-weight: bold" id="finance_src_loc_total_financing_1_alert"></span>
                                                        </div>
                                                    </td>

                                                    <td class="light-green">
                                                        <div class="{{ $errors->has('n_finance_src_loc_total_financing_1')?'has-error':'' }}">
                                                            {!! Form::text('n_finance_src_loc_total_financing_1', $appInfo->n_finance_src_loc_total_financing_1,['data-rule-maxlength'=>'40','class' => 'form-control custom_readonly input-md yellow', 'id' => 'n_finance_src_loc_total_financing_1', 'readonly']) !!}
                                                            {!! $errors->first('n_finance_src_loc_total_financing_1','<span class="help-block">:message</span>') !!}

                                                            <span class="text-danger" style="font-size: 12px; font-weight: bold" id="n_finance_src_loc_total_financing_1_alert"></span>
                                                        </div>

                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <br>
                                            <div class="table-responsive">
                                                <table width="100%" id="sourceOfFinanceTbl" class="table table-bordered">
                                                    <tr>
                                                        <td colspan="8">Country wise source of finance (Million BDT)</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-yellow" colspan="3">Existing information (Latest BIDA Reg. Info.)</td>
                                                        <td class="bg-green" colspan="4">Proposed information</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="light-yellow required-star">Country</th>
                                                        <th class="light-yellow required-star">Equity Amount
                                                            <span class="text-danger" id="equity_amount_err"></span>
                                                        </th>
                                                        <th class="light-yellow required-star">Loan Amount
                                                            <span class="text-danger" id="loan_amount_err"></span>
                                                        </th>

                                                        <th class="light-green">Country</th>
                                                        <th class="light-green">Equity Amount
                                                            <span class="text-danger" id="n_equity_amount_err"></span>
                                                        </th>
                                                        <th class="light-green">Loan Amount
                                                            <span class="text-danger" id="n_loan_amount_err"></span>
                                                        </th>
                                                        <th class="light-green">#</th>
                                                    </tr>
                                                    @if(count($sourceOfFinance) > 0)
                                                        <?php $inc = 0; ?>
                                                        @foreach($sourceOfFinance as $value)
                                                            <tr id="rowFinanceCostCount{{$inc}}">
                                                                <input type="hidden" name="source_of_finance_id[]" value="{{ $value->id }}">
                                                                <td class="light-yellow">
                                                                    <div class="{{ $errors->has('country_id')?'has-error':'' }}">
                                                                        {!! Form::select('country_id[]', $countries, $value->country_id,['class' => 'form-control input-md yellow  cusReadonly', 'id' => 'country_id']) !!}
                                                                        {!! $errors->first('country_id','<span class="help-block">:message</span>') !!}
                                                                    </div>
                                                                </td>
                                                                <td class="light-yellow">
                                                                    <div class="{{ $errors->has('equity_amount')?'has-error':'' }}">
                                                                        {!! Form::text('equity_amount[]', $value->equity_amount,['class' => 'form-control input-md yellow equity_amount required cusReadonly', 'id' => 'equity_amount']) !!}
                                                                        {!! $errors->first('equity_amount','<span class="help-block">:message</span>') !!}
                                                                    </div>
                                                                </td>
                                                                <td class="light-yellow">
                                                                    <div class="{{ $errors->has('loan_amount')?'has-error':'' }}">
                                                                        {!! Form::text('loan_amount[]', $value->loan_amount,['class' => 'form-control input-md yellow loan_amount required cusReadonly', 'id' => 'loan_amount']) !!}
                                                                        {!! $errors->first('loan_amount','<span class="help-block">:message</span>') !!}
                                                                    </div>
                                                                </td>

                                                                <td class="light-green">
                                                                    <div class="{{ $errors->has('n_country_id')?'has-error':'' }}">
                                                                        {!! Form::select('n_country_id[]', $countries, $value->n_country_id,['class' => 'form-control input-md yellow n_country_id', 'id' => 'n_country_id', (empty($value->n_country_id) ? 'disabled' : '')]) !!}
                                                                        {!! $errors->first('n_country_id','<span class="help-block">:message</span>') !!}
                                                                    </div>
                                                                </td>
                                                                <td class="light-green">
                                                                    <div class="{{ $errors->has('n_equity_amount')?'has-error':'' }}">
                                                                        {!! Form::text('n_equity_amount[]', $value->n_equity_amount,['class' => 'form-control input-md yellow n_equity_amount', 'id' => 'n_equity_amount', (empty($value->n_equity_amount) ? 'disabled' : '')]) !!}
                                                                        {!! $errors->first('n_equity_amount','<span class="help-block">:message</span>') !!}
                                                                    </div>
                                                                </td>
                                                                <td class="light-green">
                                                                    <div class="{{ $errors->has('n_loan_amount')?'has-error':'' }}">
                                                                        {!! Form::text('n_loan_amount[]', $value->n_loan_amount,['class' => 'form-control input-md yellow n_loan_amount', 'id' => 'n_loan_amount', (empty($value->n_loan_amount) ? 'disabled' : '')]) !!}
                                                                        {!! $errors->first('n_loan_amount','<span class="help-block">:message</span>') !!}
                                                                    </div>
                                                                </td>
                                                                <td class="light-green">
                                                                    @if ($inc == 0)
                                                                        <a class="btn btn-md btn-primary addTableRows" onclick="addTableRowBRA('sourceOfFinanceTbl', 'rowFinanceCostCount0');"><i class="fa fa-plus"></i></a>
                                                                    @else
                                                                        <a href="javascript:void(0);" class="btn btn-md btn-danger removeRow" onclick="removeTableRow('sourceOfFinanceTbl','rowFinanceCostCount{{$inc}}');"><i class="fa fa-times" aria-hidden="true"></i></a>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <?php $inc++ ?>
                                                        @endforeach
                                                    @else
                                                        <tr id="rowFinanceCostCount">
                                                            <td class="light-yellow">
                                                                <div class="{{ $errors->has('country_id')?'has-error':'' }}">
                                                                    {!! Form::select('country_id[0]', $countries, '',['class' => 'form-control input-md yellow  cusReadonly', 'id' => 'country_id']) !!}
                                                                    {!! $errors->first('country_id','<span class="help-block">:message</span>') !!}
                                                                </div>
                                                            </td>
                                                            <td class="light-yellow">
                                                                <div class="{{ $errors->has('equity_amount')?'has-error':'' }}">
                                                                    {!! Form::text('equity_amount[0]', '',['class' => 'form-control input-md yellow equity_amount required cusReadonly', 'id' => 'equity_amount']) !!}
                                                                    {!! $errors->first('equity_amount','<span class="help-block">:message</span>') !!}
                                                                </div>
                                                            </td>
                                                            <td class="light-yellow">
                                                                <div class="{{ $errors->has('loan_amount')?'has-error':'' }}">
                                                                    {!! Form::text('loan_amount[0]', '',['class' => 'form-control input-md yellow loan_amount required cusReadonly', 'id' => 'loan_amount']) !!}
                                                                    {!! $errors->first('loan_amount','<span class="help-block">:message</span>') !!}
                                                                </div>
                                                            </td>

                                                            <td class="light-green">
                                                                <div class="{{ $errors->has('n_country_id')?'has-error':'' }}">
                                                                    {!! Form::select('n_country_id[0]', $countries, '',['class' => 'form-control input-md yellow n_country_id', 'id' => 'n_country_id', 'disabled' => 'disabled']) !!}
                                                                    {!! $errors->first('n_country_id','<span class="help-block">:message</span>') !!}
                                                                </div>
                                                            </td>
                                                            <td class="light-green">
                                                                <div class="{{ $errors->has('n_equity_amount')?'has-error':'' }}">
                                                                    {!! Form::text('n_equity_amount[0]', '',['class' => 'form-control input-md yellow n_equity_amount', 'id' => 'n_equity_amount', 'disabled' => 'disabled']) !!}
                                                                    {!! $errors->first('n_equity_amount','<span class="help-block">:message</span>') !!}
                                                                </div>
                                                            </td>
                                                            <td class="light-green">
                                                                <div class="{{ $errors->has('n_loan_amount')?'has-error':'' }}">
                                                                    {!! Form::text('n_loan_amount[0]', '',['class' => 'form-control input-md yellow n_loan_amount', 'id' => 'n_loan_amount', 'disabled' => 'disabled']) !!}
                                                                    {!! $errors->first('n_loan_amount','<span class="help-block">:message</span>') !!}
                                                                </div>
                                                            </td>
                                                            <td class="light-green"><a class="btn btn-md btn-primary addTableRows" onclick="addTableRowBRA('sourceOfFinanceTbl', 'rowFinanceCostCount');"><i class="fa fa-plus"></i></a></td>
                                                        </tr>

                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </fieldset>

                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border">8. Public utility service</legend>
                                        <div class="alert alert-danger" role="alert">
                                            <strong>Note: </strong>If you want to amend any data from the Public utility service section, you must have to Input/update all the proposed information with the existing information of this section.
                                        </div>
                                        <table class="table table-responsive table-bordered">
                                            <thead>
                                            <tr>
                                                <td></td>
                                                <th>Information</th>
                                                <th>Land</th>
                                                <th>Electricity</th>
                                                <th>Gas</th>
                                                <th>Telephone</th>
                                                <th>Road</th>
                                                <th>Water</th>
                                                <th>Drainage</th>
                                                <th>Others</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr class="light-yellow">
                                                <td class="bg-yellow"></td>
                                                <td class="bg-yellow">Existing (Latest BIDA Reg. Info.)</td>
                                                <td>
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" class="myCheckBox cusReadonly" name="public_land" @if($appInfo->public_land == 1) checked="checked" @endif>Land
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" class="myCheckBox cusReadonly" name="public_electricity" @if($appInfo->public_electricity == 1) checked="checked" @endif>Electricity
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" class="myCheckBox cusReadonly" name="public_gas" @if($appInfo->public_gas == 1) checked="checked" @endif>Gas
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" class="myCheckBox cusReadonly" name="public_telephone" @if($appInfo->public_telephone == 1) checked="checked" @endif>Telephone
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" class="myCheckBox cusReadonly" name="public_road" @if($appInfo->public_road == 1) checked="checked" @endif>Road
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" class="myCheckBox cusReadonly" name="public_water" @if($appInfo->public_water == 1) checked="checked" @endif>Water
                                                    </label>
                                                </td>

                                                <td>
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" class="myCheckBox cusReadonly" name="public_drainage" @if($appInfo->public_drainage == 1) checked="checked" @endif>Drainage
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" id="public_others" onClick=publicOther(this); name="public_others" class="other_utility myCheckBox cusReadonly" @if($appInfo->public_others == 1) checked="checked" @endif>Others
                                                    </label>
                                                </td>

                                            </tr>
                                            @if($appInfo->public_others == 1 && !empty($appInfo->public_others_field))
                                                <tr>
                                            @else
                                                <tr id="public_others_field_div" class="hidden">
                                                    @endif
                                                <td class="bg-yellow"></td>
                                                <td class="bg-yellow"></td>
                                                <td colspan="8">
                                                        {!! Form::text('public_others_field', $appInfo->public_others_field, ['placeholder'=>'Specify others', 'class' => 'form-control input-md', 'id' => 'public_others_field']) !!}
                                                </td>
                                            </tr>

                                            <tr class="light-green">
                                                <td class="bg-green">
                                                    <span>
                                                        {!! Form::checkbox("multiToggleCheck[n_public_land]", 1,
                                                           (!empty($appInfo->n_public_land) || !empty($appInfo->n_public_electricity) || !empty($appInfo->n_public_gas) || !empty($appInfo->n_public_telephone) || !empty($appInfo->n_public_road) || !empty($appInfo->n_public_water) || !empty($appInfo->n_public_drainage) || !empty($appInfo->n_public_others) ? true : false)
                                                            , ['class' => 'field', 'id' => 'n_public_land_check', 'onclick' => "toggleCheckBoxForPublicUtilityService('n_public_land_check', ['n_public_land', 'n_public_electricity', 'n_public_gas', 'n_public_telephone', 'n_public_road', 'n_public_water', 'n_public_drainage', 'n_public_others']);"]) !!}
                                                    </span>
                                                </td>
                                                <td class="bg-green">Proposed</td>
                                                <td>
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" class="myCheckBox"  name="n_public_land" id="n_public_land" @if($appInfo->n_public_land == 1) checked="checked" @endif>Land
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" class="myCheckBox" name="n_public_electricity" id="n_public_electricity" @if($appInfo->n_public_electricity == 1) checked="checked" @endif>Electricity
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" class="myCheckBox" name="n_public_gas" id="n_public_gas" @if($appInfo->n_public_gas == 1) checked="checked" @endif>Gas
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" class="myCheckBox" name="n_public_telephone" id="n_public_telephone" @if($appInfo->n_public_telephone == 1) checked="checked" @endif>Telephone
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" class="myCheckBox" name="n_public_road" id="n_public_road" @if($appInfo->n_public_road == 1) checked="checked" @endif>Road
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" class="myCheckBox" name="n_public_water" id="n_public_water" @if($appInfo->n_public_water == 1) checked="checked" @endif>Water
                                                    </label>
                                                </td>

                                                <td>
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" class="myCheckBox" name="n_public_drainage" id="n_public_drainage" @if($appInfo->n_public_drainage == 1) checked="checked" @endif>Drainage
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" id="n_public_others" name="n_public_others" onClick=NewpublicOther(this); class="other_utility myCheckBox" @if($appInfo->n_public_others == 1) checked="checked" @endif>Others
                                                    </label>
                                                </td>
                                            </tr>
                                            @if($appInfo->n_public_others == 1 && !empty($appInfo->n_public_others_field))
                                                <tr>
                                            @else
                                            <tr id="n_public_others_field_div" class="hidden">
                                            @endif
                                            <td class="bg-green"></td>
                                            <td class="bg-green"></td>
                                            <td colspan="8">
                                                {!! Form::text('n_public_others_field', $appInfo->n_public_others_field, ['placeholder'=>'Specify others', 'class' => 'form-control input-md', 'id' => 'n_public_others_field']) !!}
                                            </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </fieldset>

                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border">9. Trade licence details</legend>
                                        <table class="table table-responsive table-bordered">
                                            <thead>
                                            <tr>
                                                <td>Field name</td>
                                                <td class="bg-yellow">Existing information (Latest BIDA Reg. Info.)</td>
                                                <td class="bg-green">Proposed information</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td class="required-star">Trade Licence Number</td>
                                                <td class="light-yellow">
                                                    {!! Form::text('trade_licence_num', !empty($appInfo->trade_licence_num) ? $appInfo->trade_licence_num : "", ['class'=>'form-control required input-md cusReadonly', 'id'=>"trade_licence_num"]) !!}
                                                    {!! $errors->first('trade_licence_num','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td class="light-green">
                                                    <input type="hidden" name="caption[n_trade_licence_num]" value="Trade licence number">
                                                    <div class="input-group">
                                                        {!! Form::text('n_trade_licence_num', !empty($appInfo->n_trade_licence_num) ? $appInfo->n_trade_licence_num : "", ['class'=>'form-control input-md', 'id'=>"n_trade_licence_num", (empty($appInfo->n_trade_licence_num) ? 'disabled' : '')]) !!}
                                                        <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_trade_licence_num]", 1, empty($appInfo->n_trade_licence_num) ? false : true, ['class' => 'field', 'id' => 'n_trade_licence_num_check', 'onclick' => "toggleCheckBox('n_trade_licence_num_check', ['n_trade_licence_num']);"]) !!}
                                                    </span>
                                                    </div>
                                                    {!! $errors->first('n_trade_licence_num','<span class="help-block">:message</span>') !!}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="required-star">Issuing Authority</td>
                                                <td class="light-yellow">
                                                    {!! Form::text('trade_licence_issuing_authority', !empty($appInfo->trade_licence_issuing_authority) ? $appInfo->trade_licence_issuing_authority : "", ['class'=>'form-control required input-md cusReadonly', 'id'=>"trade_licence_issuing_authority"]) !!}
                                                    {!! $errors->first('trade_licence_issuing_authority','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td class="light-green">
                                                    <input type="hidden" name="caption[n_trade_licence_issuing_authority]" value="Trade licence issuing authority">
                                                    <div class="input-group">
                                                        {!! Form::text('n_trade_licence_issuing_authority', !empty($appInfo->n_trade_licence_issuing_authority) ? $appInfo->n_trade_licence_issuing_authority : "", ['class'=>'form-control input-md', 'id'=>"n_trade_licence_issuing_authority", (empty($appInfo->n_trade_licence_issuing_authority) ? 'disabled' : '')]) !!}
                                                        <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_trade_licence_issuing_authority]", 1, empty($appInfo->n_trade_licence_issuing_authority) ? false : true, ['class' => 'field', 'id' => 'n_trade_licence_issuing_authority_check', 'onclick' => "toggleCheckBox('n_trade_licence_issuing_authority_check', ['n_trade_licence_issuing_authority']);"]) !!}
                                                    </span>
                                                    </div>
                                                    {!! $errors->first('n_trade_licence_issuing_authority','<span class="help-block">:message</span>') !!}
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </fieldset>

                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border">10. Tin</legend>
                                        <table class="table table-responsive table-bordered">
                                            <thead>
                                            <tr>
                                                <td>Field name</td>
                                                <td class="bg-yellow">Existing information (Latest BIDA Reg. Info.)</td>
                                                <td class="bg-green">Proposed information</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td class="required-star">Tin Number</td>
                                                <td class="light-yellow">
                                                    {!! Form::text('tin_number', $appInfo->tin_number, ['class'=>'form-control required input-md cusReadonly', 'id'=>"tin_number"]) !!}
                                                    {!! $errors->first('tin_number','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td class="light-green">
                                                    <input type="hidden" name="caption[n_tin_number]" value="Tin number">
                                                    <div class="input-group">
                                                        {!! Form::text('n_tin_number', $appInfo->n_tin_number, ['class'=>'form-control input-md', 'id'=>"n_tin_number", (empty($appInfo->n_tin_number) ? 'disabled' : '')]) !!}
                                                        <span class="input-group-addon">
                                                    {!! Form::checkbox("toggleCheck[n_tin_number]", 1, empty($appInfo->n_tin_number) ? false: true, ['class' => 'field', 'id' => 'n_tin_number_check', 'onclick' => "toggleCheckBox('n_tin_number_check', ['n_tin_number']);"]) !!}
                                                    </span>
                                                    </div>
                                                    {!! $errors->first('n_tin_number','<span class="help-block">:message</span>') !!}
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </fieldset>

                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border">11. Existing BIDA Registration Amendment</legend>
                                        <div class="table-responsive">
                                            <table width="100%" id="existing_bra_table" class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th class="table-header">BRA Ref/Memo</th>
                                                    <th class="table-header">Approved Date</th>
                                                    <th class="table-header">#</th>
                                                </tr>
                                                </thead>

                                                @if(count($existing_bra) > 0)
                                                    <?php $i = 0; ?>
                                                    @foreach($existing_bra as $value)
                                                        <tr id="existing_bra_table_row{{$i}}" data-number="1" class="table-tr">
                                                            <input type="hidden" name="existing_bra_id[]" value="{{ $value->id }}">
                                                            <td>
                                                                <div class="{{ $errors->has('bra_memo_no')?'has-error':'' }}">
                                                                    {!! Form::text('bra_memo_no[]', $value->bra_memo_no,['class' => 'form-control input-md', 'id' => 'bra_memo_no']) !!}
                                                                    {!! $errors->first('bra_memo_no','<span class="help-block">:message</span>') !!}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                {!! Form::text('bra_approved_date[]', (!empty($value->bra_approved_date) ? date('d-M-Y', strtotime($value->bra_approved_date)) : ''), ['class'=>'form-control input-md datepicker', 'id' => 'bra_approved_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                                {!! $errors->first('bra_approved_date','<span class="help-block">:message</span>') !!}
                                                            </td>
                                                            <td>
                                                                @if ($i == 0)
                                                                    <a class="btn btn-md btn-primary addTableRows" onclick="addTableRowBRA('existing_bra_table', 'existing_bra_table_row0');"><i class="fa fa-plus"></i></a>
                                                                @else
                                                                    <a href="javascript:void(0);" class="btn btn-md btn-danger removeRow" onclick="removeTableRow('existing_bra_table','existing_bra_table_row{{$i}}');"><i class="fa fa-times" aria-hidden="true"></i></a>
                                                                @endif
                                                            </td>
                                                        </tr>

                                                        <?php $i++ ?>
                                                    @endforeach
                                                @else
                                                    <tr id="existing_bra_table_row" data-number="1" class="table-tr">
                                                        <td>
                                                            <div class="{{ $errors->has('bra_memo_no')?'has-error':'' }}">
                                                                {!! Form::text('bra_memo_no[]', '',['class' => 'form-control input-md', 'id' => 'bra_memo_no']) !!}
                                                                {!! $errors->first('bra_memo_no','<span class="help-block">:message</span>') !!}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            {!! Form::text('bra_approved_date[]', '', ['class'=>'form-control input-md datepicker', 'id' => 'bra_approved_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            {!! $errors->first('bra_approved_date','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            <a class="btn btn-md btn-primary addTableRows" onclick="addTableRowBRA('existing_bra_table', 'existing_bra_table_row');"><i class="fa fa-plus"></i></a>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </fieldset>

                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border">12. Why do you want to BIDA Registration Amendment?</legend>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="form-group col-md-12 {{$errors->has('major_remarks') ? 'has-error' : ''}}">
                                                    {!! Form::label('major_remarks','Major remarks in brief',['class'=>'col-md-3']) !!}
                                                    <div class="col-md-9">
                                                        {!! Form::textarea('major_remarks', $appInfo->major_remarks, ['class' => 'form-control input-md bigInputField maxTextCountDown', 'size' =>'5x3','data-rule-maxlength'=>'240', 'placeholder' => 'Maximum 240 characters', "data-charcount-maxlength" => "240"]) !!}
                                                        {!! $errors->first('major_remarks','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>

                                    <fieldset class="scheduler-border hidden" id="machinery_equipment">
                                        <legend class="scheduler-border"><strong>13. Description of machinery and equipment</strong></legend>
                                        <table class="table table-responsive table-bordered">
                                            <thead>
                                            <tr>
                                                <td width="20%">Field name</td>
                                                <td class="bg-yellow" width="40%" colspan="2">Existing information (Latest BIDA Reg. Info.)</td>
                                                <td class="bg-green" width="40%" colspan="2">Proposed information</td>
                                            </tr>
                                            <tr>
                                                <td width="20%">
                                                    <span>
                                                        {!! Form::checkbox("multiToggleCheck[n_machinery_local_qty]", 1, (!empty($appInfo->n_machinery_local_qty) || !empty($appInfo->n_machinery_local_price_bdt) || !empty($appInfo->n_imported_qty) || !empty($appInfo->n_imported_qty_price_bdt)) ? true : false, ['class' => 'field', 'id' => 'n_machinery_local_qty_check', 'onclick' => "toggleCheckBox('n_machinery_local_qty_check', ['n_machinery_local_qty', 'n_machinery_local_price_bdt', 'n_imported_qty', 'n_imported_qty_price_bdt', 'n_total_machinery_qty', 'n_total_machinery_price']);"]) !!}
                                                    </span>
                                                </td>
                                                <td width="20%" class="light-yellow">Quantity</td>
                                                <td width="20%" class="light-yellow">Price (BDT)</td>
                                                <td width="20%" class="light-green">Quantity</td>
                                                <td width="20%" class="light-green">Price (BDT)</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>Locally Collected</td>
                                                <td class="light-yellow">
                                                    {!! Form::text('machinery_local_qty', !empty($appInfo->machinery_local_qty) ? $appInfo->machinery_local_qty : '',['class'=>'form-control input-md cusReadonly', 'id' => 'machinery_local_qty', 'onkeyup' => 'totalMachineryEquipmentQty()']) !!}
                                                    {!! $errors->first('machinery_local_qty','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td class="light-yellow">
                                                    {!! Form::text('machinery_local_price_bdt', !empty($appInfo->machinery_local_price_bdt) ? $appInfo->machinery_local_price_bdt : '',['class'=>'form-control input-md cusReadonly', 'id' => 'machinery_local_price_bdt', 'onkeyup'=>"totalMachineryEquipmentPrice()"]) !!}
                                                    {!! $errors->first('machinery_local_price_bdt','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td class="light-green">
                                                    <div class="form-group">
                                                        {!! Form::text('n_machinery_local_qty', !empty($appInfo->n_machinery_local_qty) ? $appInfo->n_machinery_local_qty : '',['class'=>'form-control input-md', 'id' => 'n_machinery_local_qty', (empty($appInfo->n_machinery_local_qty) ? 'disabled' : ''), 'onkeyup' => 'nTotalMachineryEquipmentQty()']) !!}
                                                    </div>
                                                    {!! $errors->first('n_machinery_local_qty','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td class="light-green">
                                                    <div class="form-group">
                                                        {!! Form::text('n_machinery_local_price_bdt', !empty($appInfo->n_machinery_local_price_bdt) ? $appInfo->n_machinery_local_price_bdt : '',['class'=>'form-control input-md', 'id' => 'n_machinery_local_price_bdt', (empty($appInfo->n_machinery_local_price_bdt) ? 'disabled' : ''), 'onkeyup'=>"nTotalMachineryEquipmentPrice()"]) !!}
                                                    </div>
                                                    {!! $errors->first('n_machinery_local_price_bdt','<span class="help-block">:message</span>') !!}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Imported</td>
                                                <td class="light-yellow">
                                                    {!! Form::text('imported_qty', !empty($appInfo->imported_qty) ? $appInfo->imported_qty : '',['class'=>'form-control input-md cusReadonly', 'id' => 'imported_qty', 'onkeyup' => 'totalMachineryEquipmentQty()']) !!}
                                                    {!! $errors->first('imported_qty','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td class="light-yellow">
                                                    {!! Form::text('imported_qty_price_bdt', !empty($appInfo->imported_qty_price_bdt) ? $appInfo->imported_qty_price_bdt: '',['class'=>'form-control input-md cusReadonly', 'id' => 'imported_qty_price_bdt', 'onkeyup'=>"totalMachineryEquipmentPrice()"]) !!}
                                                    {!! $errors->first('imported_qty_price_bdt','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td class="light-green">
                                                    <div class="form-group">
                                                        {!! Form::text('n_imported_qty', !empty($appInfo->n_imported_qty) ? $appInfo->n_imported_qty : '',['class'=>'form-control input-md', 'id' => 'n_imported_qty', (empty($appInfo->n_imported_qty) ? 'disabled' : ''), 'onkeyup' => 'nTotalMachineryEquipmentQty()']) !!}
                                                    </div>
                                                    {!! $errors->first('n_imported_qty','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td class="light-green">
                                                    <div class="form-group">
                                                        {!! Form::text('n_imported_qty_price_bdt', !empty($appInfo->n_imported_qty_price_bdt) ? $appInfo->n_imported_qty_price_bdt : '',['class'=>'form-control input-md', 'id' => 'n_imported_qty_price_bdt', (empty($appInfo->n_imported_qty_price_bdt) ? 'disabled' : ''), 'onkeyup'=>"nTotalMachineryEquipmentPrice()"]) !!}
                                                    </div>
                                                    {!! $errors->first('n_imported_qty_price_bdt','<span class="help-block">:message</span>') !!}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Total</td>
                                                <td class="light-yellow">
                                                    {!! Form::text('total_machinery_qty', !empty($appInfo->total_machinery_qty) ? $appInfo->total_machinery_qty : '',['class'=>'form-control input-md cusReadonly', 'id' => 'total_machinery_qty', 'readonly']) !!}
                                                    {!! $errors->first('total_machinery_qty','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td class="light-yellow">
                                                    {!! Form::text('total_machinery_price', !empty($appInfo->total_machinery_price) ? $appInfo->total_machinery_price : '',['class'=>'form-control input-md cusReadonly', 'id' => 'total_machinery_price', 'readonly']) !!}
                                                    {!! $errors->first('total_machinery_price','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td class="light-green">
                                                    <div class="form-group">
                                                        {!! Form::text('n_total_machinery_qty', !empty($appInfo->n_total_machinery_qty) ? $appInfo->n_total_machinery_qty : '',['class'=>'form-control input-md', 'id' => 'n_total_machinery_qty', (empty($appInfo->n_total_machinery_qty) ? 'disabled' : ''), 'readonly']) !!}
                                                    </div>
                                                    {!! $errors->first('n_total_machinery_qty','<span class="help-block">:message</span>') !!}
                                                </td>
                                                <td class="light-green">
                                                    <div class="form-group">
                                                        {!! Form::text('n_total_machinery_price', !empty($appInfo->n_total_machinery_price) ? $appInfo->n_total_machinery_price : '',['class'=>'form-control input-md', 'id' => 'n_total_machinery_price', (empty($appInfo->n_total_machinery_price) ? 'disabled' : ''), 'readonly']) !!}
                                                    </div>
                                                    {!! $errors->first('n_total_machinery_price','<span class="help-block">:message</span>') !!}
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </fieldset>

                                    <fieldset class="scheduler-border hidden" id="packing_materials">
                                        <legend class="scheduler-border">14. Description of raw & packing materials</legend>
                                        <table class="table table-responsive table-bordered">
                                            <thead>
                                            <tr>
                                                <td colspan="2">Field name</td>
                                                <td class="bg-yellow">Existing information (Latest BIDA Reg. Info.)</td>
                                                <td class="bg-green">Proposed information</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="caption[n_local_description]" value="Raw & packing materials local description">
                                                    {!! Form::checkbox("toggleCheck[n_local_description]", 1, (empty($appInfo->n_local_description) ? false : true), ['class' => 'field', 'id' => 'n_local_description_check', 'onclick' => "toggleCheckBox('n_local_description_check', ['n_local_description']);"]) !!}
                                                </td>
                                                <td>Locally</td>
                                                <td class="light-yellow">
                                                    {!! Form::textarea('local_description', $appInfo->local_description, ['class' => 'form-control bigInputField input-md maxTextCountDown cusReadonly',
                                                        'id' => 'local_description', 'size'=>'5x2','data-charcount-maxlength'=>'1000']) !!}
                                                </td>
                                                <td class="light-green">
                                                    {!! Form::textarea('n_local_description', $appInfo->n_local_description, ['class' => 'form-control bigInputField input-md maxTextCountDown',
                                                        'id' => 'n_local_description', 'size'=>'5x2','data-charcount-maxlength'=>'1000', (empty($appInfo->n_local_description) ? 'disabled' : '')]) !!}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="caption[n_imported_description]" value="Raw & packing materials imported description">
                                                    {!! Form::checkbox("toggleCheck[n_imported_description]", 1, (empty($appInfo->n_imported_description) ? false : true), ['class' => 'field', 'id' => 'n_imported_description_check', 'onclick' => "toggleCheckBox('n_imported_description_check', ['n_imported_description']);"]) !!}
                                                </td>
                                                <td>Imported</td>
                                                <td class="light-yellow">
                                                    {!! Form::textarea('imported_description', $appInfo->imported_description, ['class' => 'form-control bigInputField input-md maxTextCountDown cusReadonly',
                                                        'id' => 'imported_description', 'size'=>'5x2','data-charcount-maxlength'=>'1000']) !!}
                                                </td>
                                                <td class="light-green">
                                                    {!! Form::textarea('n_imported_description', $appInfo->n_imported_description, ['class' => 'form-control bigInputField input-md maxTextCountDown',
                                                        'id' => 'n_imported_description', 'size'=>'5x2','data-charcount-maxlength'=>'1000', (empty($appInfo->n_imported_description) ? 'disabled' : '')]) !!}
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </fieldset>
                                </div>
                            </div>
                        </fieldset>

                        <h3 class="stepHeader">List of Directors</h3>
                        <fieldset>
                            <div class="panel panel-info">
                                <div class="panel-heading "><strong>List of Directors</strong></div>
                                <div class="panel-body">
                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border">Information of (Chairman/ Managing Director/ Or Equivalent):</legend>
                                        <div class="table-responsive">
                                            <table class="table table-responsive table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                <tr>
                                                    <td>
                                                        <span>
                                                            {!! Form::checkbox("multiToggleCheck[n_g_full_name]", 1, null, ['class' => 'field', 'id' => 'n_list_of_director_check', 'onclick' => "toggleCheckBox('n_list_of_director_check', ['n_g_full_name', 'n_g_designation', 'n_investor_signature']);"]) !!}
                                                        </span>
                                                    </td>
                                                    <td width="30%">Field name</td>
                                                    <td class="bg-yellow" width="35%">Existing information (Latest BIDA Reg. Info.)</td>
                                                    <td class="bg-green" width="35%">Proposed information</td>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td></td>
                                                    <td class="required-star">Full Name</td>
                                                    <td>
                                                        <div class="{{ $errors->has('g_full_name')?'has-error':'' }}">
                                                            {!! Form::text('g_full_name', $appInfo->g_full_name,['class' => 'form-control required input-md yellow cusReadonly', 'id' => 'g_full_name']) !!}
                                                            {!! $errors->first('g_full_name','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="caption[n_g_full_name]" value="Full Name">
                                                        <div>
                                                            {!! Form::text('n_g_full_name', $appInfo->n_g_full_name,['class'=>'form-control input-md', 'id' => 'n_g_full_name', (empty($appInfo->n_g_full_name) ? 'disabled' : '')]) !!}
                                                            {{-- <span class="input-group-addon">
                                                            {!! Form::checkbox("toggleCheck[n_g_full_name]", 1, (empty($appInfo->n_g_full_name) ? false : true), ['class' => 'field', 'id' => 'n_g_full_name_check', 'onclick' => "toggleCheckBox('n_g_full_name_check', ['n_g_full_name']);"]) !!}
                                                            </span> --}}
                                                        </div>
                                                        {!! $errors->first('n_g_full_name','<span class="help-block">:message</span>') !!}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td class="required-star">Position/ Designation</td>
                                                    <td>
                                                        <div class="{{ $errors->has('g_designation')?'has-error':'' }}">
                                                            {!! Form::text('g_designation', $appInfo->g_designation,['class' => 'form-control required input-md yellow cusReadonly', 'id' => 'g_designation']) !!}
                                                            {!! $errors->first('g_designation','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="caption[n_g_designation]" value="Position/ Designation">
                                                        <div>
                                                            {!! Form::text('n_g_designation', $appInfo->n_g_designation,['class'=>'form-control input-md', 'id' => 'n_g_designation', (empty($appInfo->n_g_designation) ? 'disabled' : '')]) !!}
                                                            {{-- <span class="input-group-addon">
                                                                <input type="hidden" name="caption[n_g_designation]" value="Position/ Designation">
                                                            {!! Form::checkbox("toggleCheck[n_g_designation]", 1, (empty($appInfo->n_g_designation) ? false:true), ['class' => 'field', 'id' => 'n_g_designation_check', 'onclick' => "toggleCheckBox('n_g_designation_check', ['n_g_designation']);"]) !!}
                                                            </span> --}}
                                                        </div>
                                                        {!! $errors->first('n_g_designation','<span class="help-block">:message</span>') !!}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td class="required-star">Signature</td>
                                                    <td>
                                                        <div class="col-md-8">
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
                                                                <span id="signature_error_msg" style="font-size: 10px; display: block; font-weight: bold; color: red; display: none">Signature is required</span>
                                                                <br/>
                                                                <label class="btn btn-primary btn-file" {{ $errors->has('investor_signature_name') ? 'has-error' : '' }}>
                                                                    <i class="fa fa-picture-o" aria-hidden="true"></i> Browse
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
                                                    </td>
                                                    <td>
                                                        <div class="col-md-8">
                                                            <div id="investorSignatureViewerDiv">
                                                                <figure>
                                                                    <img class="img-thumbnail img-signature" id="n_investor_signature_preview"
                                                                         src="{{ (!empty($appInfo->n_g_signature)? url('uploads/'. $appInfo->n_g_signature) : url('assets/images/photo_default.png')) }}"
                                                                         alt="Investor Signature" style="width: 100%">
                                                                </figure>

                                                                <input type="hidden" id="n_investor_signature_base64" name="n_investor_signature_base64"/>
                                                                @if(!empty($appInfo->n_g_signature))
                                                                    <input type="hidden" id="n_investor_signature_hidden" name="n_investor_signature_hidden"
                                                                           value="{{$appInfo->n_g_signature}}"/>
                                                                @endif
                                                            </div>
                                                            <div class="form-group">
                                                                <span style="font-size: 9px; font-weight: bold; display: block;">
                                                                       [File Format: *.jpg/ .jpeg | Width 300PX, Height 80PX]
                                                                </span>
                                                                <span id="n_signature_error_msg" style="font-size: 10px; display: block; font-weight: bold; color: red; display: none">Signature is required</span>
                                                                <br/>
                                                                <label class="btn btn-primary btn-file" style="cursor: {{ empty($appInfo->n_g_signature) ? 'not-allowed' : 'pointer' }}" {{ $errors->has('investor_signature') ? 'has-error' : '' }}>
                                                                    <i class="fa fa-picture-o" aria-hidden="true"></i> Browse
                                                                    <input
                                                                            class="{{ empty($appInfo->n_g_signature) ? "required" : "" }}"
                                                                            type="file"
                                                                            style="position: absolute; left: -9999px;"
                                                                            name="n_investor_signature"
                                                                            id="n_investor_signature"
                                                                            onchange="imageUploadWithCropping(this, 'n_investor_signature_preview', 'n_investor_signature_base64')"
                                                                            size="300x80"
                                                                            {{ empty($appInfo->n_g_signature) ? 'disabled' : '' }}
                                                                    />
                                                                </label>
                                                                <input type="hidden" name="caption[n_investor_signature]" value="investor signature">
                                                                {{-- {!! Form::checkbox("toggleCheck[n_investor_signature]", 1, (empty($appInfo->n_g_signature) ? false:true), ['class' => 'field', 'id' => 'n_investor_signature_check', 'onclick' => "toggleCheckBox('n_investor_signature_check', ['n_investor_signature']);"]) !!} --}}
                                                                {!! $errors->first('n_g_signature','<span class="help-block">:message</span>') !!}
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </fieldset>


                                    <div class="panel panel-info">
                                        <div class="panel-heading ">
                                            <div class="pull-left" style="padding:5px 5px"><strong>List of
                                                    directors</strong></div>
                                            <div class="pull-right">
                                                <a data-toggle="modal" data-target="#openDirectorModal" onclick="openBraModal(this, 'openDirectorModal')"
                                                   data-action="{{ url('bida-registration-amendment/director-form/' . Encryption::encodeId($appInfo->id)) }}">
                                                    {!! Form::button('Add New Director', array('type' => 'button', 'class' => 'btn btn-md btn-info')) !!}
                                                </a>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <div id="load_list_of_director"></div>
                                                @if($listOfDirectors > 20)
                                                    <br>
                                                    <a href="javascript:void(0)" onclick="listOfDirectors('all', 'off')" id="load_list_of_director_data">Load more data</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </fieldset>

                        <h3 class="stepHeader">List of Machineries</h3>
                        <fieldset>
                            <div class="panel panel-info">
                                <div class="panel-heading "><strong>List of Machineries</strong></div>
                                <div class="panel-body">
                                    <div class="panel panel-info">
                                        <div class="panel-heading ">
                                            <div class="pull-left">
                                                <strong>List of machinery to be imported</strong>
                                            </div>
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-info" id="batch-delete"><i class="fa fa-trash" style="margin-right: 5px;"></i><b>Delete Selected</b></button>
                                                <a data-toggle="modal" data-target="#excelFileUploadModel" onclick="openBraModal(this, 'braModal')"
                                                   data-action="{{ url('/bida-registration-amendment/excel/imported-machinery/'. Encryption::encodeId($appInfo->id)) }}">
                                                    {!! Form::button('<i class="fa fa-file-excel" aria-hidden="true"></i> <b>Excel File Upload</b>', array('type' => 'button', 'class' => 'btn btn-info')) !!}
                                                </a>
                                                <a data-toggle="modal" data-target="#braModal" onclick="openBraModal(this, 'braModal')"
                                                   data-action="{{ url('bida-registration-amendment/imported-machinery-form/'.Encryption::encodeId($appInfo->id)) }}">
                                                    {!! Form::button('Add New Imported Machinery', array('type' => 'button', 'class' => 'btn btn-md btn-info')) !!}
                                                </a>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="panel-body">
                                            <div id="load_imported_machinery_data"></div>
                                            @if($ListOfMachineryImported > 20)
                                                <br>
                                                <a href="javascript:void(0)" onclick="loadImportedMachineryData('all', 'off')" id="load_imported_data">Load more data</a>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="panel panel-info">
                                        <div class="panel-heading ">
                                            <div class="pull-left">
                                                <strong>List of machinery locally purchase/ procure</strong>
                                            </div>
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-info" id="batch-delete-local"><i class="fa fa-trash" style="margin-right: 5px;"></i><b>Delete Selected</b></button>
                                                <a data-toggle="modal" data-target="#excelFileUploadModel" onclick="openBraModal(this, 'braModal')"
                                                   data-action="{{ url('/bida-registration-amendment/excel/local-machinery/'. Encryption::encodeId($appInfo->id)) }}">
                                                    {!! Form::button('<i class="fa fa-file-excel" aria-hidden="true"></i> <b>Excel File Upload</b>', array('type' => 'button', 'class' => 'btn btn-info')) !!}
                                                </a>
                                                <a data-toggle="modal" data-target="#braModal" onclick="openBraModal(this, 'braModal')"
                                                   data-action="{{ url('bida-registration-amendment/local-machinery-form/'.Encryption::encodeId($appInfo->id)) }}">
                                                    {!! Form::button('Add New Local Machinery', array('type' => 'button', 'class' => 'btn btn-md btn-info')) !!}
                                                </a>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="panel-body">
                                            <div id="load_local_machinery_data"></div>
                                            @if($listOfMachineryLocal > 20)
                                                <br>
                                                <a href="javascript:void(0)" onclick="loadLocalMachineryData('all', 'off')" id="load_local_data">Load more data</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <h3 class="stepHeader">Attachments</h3>
                        <fieldset>
                            <div id="docListDiv"></div>

                            <div class="form-group col-sm-12">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('accept_terms',1, (empty($appInfo->accept_terms) ? false : true), array('id'=>'accept_terms', 'class'=>'required')) !!}
                                        I do here by declare that the information given above is true to the best of
                                        my knowledge and I shall be liable for any false information/ statement is
                                        given.
                                    </label>
                                </div>
                            </div>
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
                                                    {!! Form::text('sfp_contact_name', $appInfo->sfp_contact_name, ['class' => 'form-control required input-md']) !!}
                                                    {!! $errors->first('sfp_contact_name','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 {{$errors->has('sfp_contact_email') ? 'has-error': ''}}">
                                                {!! Form::label('sfp_contact_email','Contact email',['class'=>'col-md-5 text-left required-star']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::email('sfp_contact_email', $appInfo->sfp_contact_email, ['class' => 'form-control required input-md email']) !!}
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
                                                    {!! Form::text('sfp_contact_phone', $appInfo->sfp_contact_phone, ['class' => 'form-control required sfp_contact_phone phone_or_mobile input-md']) !!}
                                                    {!! $errors->first('sfp_contact_phone','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 {{$errors->has('sfp_contact_address') ? 'has-error': ''}}">
                                                {!! Form::label('sfp_contact_address','Contact address',['class'=>'col-md-5 text-left required-star']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('sfp_contact_address', $appInfo->sfp_contact_address, ['class' => 'bigInputField required form-control input-md']) !!}
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
                                                    @else
                                                        <span class="label label-warning">invalid status</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if($appInfo->sfp_payment_status != 1)
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="alert alert-danger" role="alert">
                                                        <strong>Vat/ Tax</strong> and <strong>Transaction charge</strong> is an approximate amount, those may vary based on the Sonali Bank system and those will be visible here after payment submission.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </fieldset>
                        @if(ACL::getAccsessRight('BidaRegistrationAmendment','-E-') && $appInfo->status_id != 6 && Auth::user()->user_type == '5x505')
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
        </div>
    </div>
</section>

<div class="col-md-12">
    <div class="modal fade" id="businessClassModal" tabindex="-1"
         role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content load_business_class_modal"></div>
        </div>
    </div>
</div>

{{--BRA modal--}}
<div class="modal fade" id="braModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content load_modal"></div>
    </div>
</div>

<!-- Modal Govt Payment-->
<div class="modal fade" id="govFeesCalculator" role="dialog">
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

{{--open director modal--}}
<div class="modal fade" id="openDirectorModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content load_modal"></div>
    </div>
</div>

<script src="{{ asset("assets/scripts/jquery.steps.js") }}"></script>
<script src="{{ asset("assets/scripts/jquery-ui-1.11.4.js") }}" type="text/javascript"></script>
<link rel="stylesheet" href="{{ asset("assets/css/jquery-ui.css") }}"/>


<script src="{{ asset("build/js/intlTelInput-jquery_v16.0.8.min.js") }}" type="text/javascript"></script>
<script src="{{ asset("build/js/utils_v16.0.8.js") }}" type="text/javascript"></script>
{{--Datepicker js--}}
<script src="{{ asset("vendor/datepicker/datepicker.min.js") }}"></script>
{{--//textarea count down--}}
<script src="{{ asset("vendor/character-counter/jquery.character-counter_v1.0.min.js") }}" type="text/javascript"></script>
{{--//Attachment type--}}
<script src="{{ asset("assets/scripts/attachment.js") }}"></script>
<script src="{{ asset("assets/scripts/check-tracking-no-exists.js") }}"></script>
@include('partials.image-resize.image-upload')

{{--list of director, List of Machineries data load--}}
@include('BidaRegistrationAmendment::bra-ajax-dataload')



<script>
    var n_ceo_birthday = "{{ empty($appInfo->n_ceo_dob) ? true : false }}";
    var n_co_date = "{{ empty($appInfo->n_commercial_operation_date) ? true : false }}";


    function CategoryWiseDocLoad(org_status_id, mode) {
        if (org_status_id != "" && mode == "propose"){
            if ($('#n_country_of_origin_id_check').is(':not(:checked)')) {
                $('#n_country_of_origin_id_check').click();
            }

            if (org_status_id == 3) {
                if($("#n_country_of_origin_id option[value='18']").length === 0) {
                    $('#n_country_of_origin_id').append(`<option value="18">Bangladesh</option>`);
                }
                $("#n_country_of_origin_id").val("18").change();
            } else {
                $("#n_country_of_origin_id option[value='18']").remove();
            }

        }

        if (org_status_id != "" && mode == "existing") {
            if (org_status_id == 3) {
                $('#country_of_origin_id').removeClass('required');
                $('#country_of_origin_label').removeClass('required-star');
                if($("#country_of_origin_id option[value='18']").length === 0) {
                    $('#country_of_origin_id').append(`<option value="18">Bangladesh</option>`);
                }
                $("#country_of_origin_id").val("18").change();
            } else {
                $('#country_of_origin_id').addClass('required');
                $('#country_of_origin_label').addClass('required-star');
                $("#country_of_origin_id option[value='18']").remove();
            }
        }

        if (org_status_id == 3) {
            $("#machinery_equipment").removeClass('hidden');
            $("#packing_materials").removeClass('hidden');
        } else {
            $("#machinery_equipment").addClass('hidden');
            $("#packing_materials").addClass('hidden');
        }

        const n_organization_status_id = document.getElementById('n_organization_status_id').value;
        const organization_status_id = n_organization_status_id !== '' ? n_organization_status_id : document.getElementById('organization_status_id').value;

        const n_ownership_status_id = document.getElementById('n_ownership_status_id').value;
        const ownership_status_id = n_ownership_status_id !== '' ? n_ownership_status_id : document.getElementById('ownership_status_id').value;

        if (organization_status_id != 0 && organization_status_id != '' && ownership_status_id != 0 && ownership_status_id != '') {
            var _token = $('input[name="_token"]').val();
            var app_id = $("#app_id").val();
            var viewMode = 'off';
            const attachment_key = generateAttachmentKey(organization_status_id, ownership_status_id, 'bra');
            $.ajax({
                type: "POST",
                url: '/bida-registration-amendment/getDocList',
                dataType: "json",
                data: {_token: _token, attachment_key: attachment_key, viewMode: viewMode, app_id: app_id},
                success: function (result) {
                    if (result.html != undefined) {
                        $('#docListDiv').html(result.html);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    //console.log(errorThrown);
                    alert('Unknown error occured. Please, try again after reload');
                },
            });
        } else {
            console.warn('Unknown organization status ID');
        }
    }

    function publicOther (id) {
        $("#public_others_field_div").addClass('hidden');
        $("#public_others_field").removeClass('required');
        var isOtherChecked = $('#public_others').is(':checked');
        if (isOtherChecked == true) {
            $("#public_others_field_div").removeClass('hidden');
            $("#public_others_field").addClass('required');
        }
    }

    function NewpublicOther (id) {
        $("#n_public_others_field_div").addClass('hidden');
        $("#n_public_others_field").removeClass('required');
        var isOtherChecked = $('#n_public_others').is(':checked');
        if (isOtherChecked == true) {
            $("#n_public_others_field_div").removeClass('hidden');
            $("#n_public_others_field").addClass('required');
        }
    }
    $(document).ready(function() {


        listOfDirectors(20, 'off');
        loadAnnualProductionCapacityData(20, 'off');
        loadImportedMachineryData(20, 'off');
        loadLocalMachineryData(20, 'off');

        isBraApprovalManually()

        var form = $("#BidaRegistrationAmendmentForm").show();
        form.find('#save_as_draft').css('display','none');
        form.find('#submitForm').css('display', 'none');
        form.find('.actions').css('top','-15px !important');
        form.steps({
            headerTag: "h3",
            bodyTag: "fieldset",
            transitionEffect: "slideLeft",
            onStepChanging: function (event, currentIndex, newIndex) {
                // return true;
                if (newIndex == 1) {

                }

                if (newIndex == 2) {
                    //Check Total Financing and  Total Investment (BDT) is equal
                    var totalInvestment = document.getElementById('total_fixed_ivst').value;
                    var total_finance = document.getElementById('finance_src_loc_total_financing_1').value;
                    if (!(totalInvestment == total_finance)) {
                        $('#finance_src_loc_total_financing_1').addClass('required error');
                        swal({type: 'error', text: "Total Financing and Total Investment (BDT) must be equal."});
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
                        document.getElementById('finance_src_loc_total_equity_1').value : 0).toFixed(5);

                    if (finance_src_loc_total_equity_1 != total_equity_amounts) {
                        for (var i = 0; i < equity_amount_elements.length; i++) {
                            equity_amount_elements[i].classList.add('required', 'error');
                        }
                        document.getElementById('equity_amount_err').innerHTML = '<br/>Total equity amount should be equal to Total Equity (Million)';
                        return false;
                    } else {
                        for (var i = 0; i < equity_amount_elements.length; i++) {
                            equity_amount_elements[i].classList.remove('required', 'error');
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
                        document.getElementById('finance_src_total_loan').value : 0).toFixed(5);

                    if (finance_src_total_loan != total_loan_amounts) {
                        for (var i = 0; i < loan_amount_elements.length; i++) {
                            loan_amount_elements[i].classList.add('required', 'error');
                        }
                        document.getElementById('loan_amount_err').innerHTML = '<br/>Total loan amount should be equal to Total Loan (Million)';
                        return false;
                    } else {
                        for (var i = 0; i < loan_amount_elements.length; i++) {
                            loan_amount_elements[i].classList.remove('required', 'error');
                        }
                        document.getElementById('loan_amount_err').innerHTML = '';
                    }

                    //propose investment validation
                    if ($("#investmentAndSourceOfFinance_check").is(':checked')) {
                        //Check Proposed Total Financing and  Total Investment (BDT) is equal
                        var n_totalInvestment = document.getElementById('n_total_fixed_ivst').value;
                        var n_total_finance = document.getElementById('n_finance_src_loc_total_financing_1').value;
                        if (!(n_totalInvestment == n_total_finance)){
                            $('#n_finance_src_loc_total_financing_1').addClass('required error');
                            swal({type: 'error', text: "Total Financing and Total Investment (BDT) must be equal."});
                            return false;
                        }

                        //Proposed Equity Amount should be equal to Proposed Total Equity (Million) of 7. Source of finance
                        var n_equity_amount_elements = document.querySelectorAll('.n_equity_amount');
                        var n_total_equity_amounts = 0;
                        for (var i = 0; i < n_equity_amount_elements.length; i++) {
                            n_total_equity_amounts = n_total_equity_amounts + parseFloat(n_equity_amount_elements[i].value ? n_equity_amount_elements[i].value : 0);
                        }
                        n_total_equity_amounts = (n_total_equity_amounts).toFixed(5);
                        var n_finance_src_loc_total_equity_1 = parseFloat(document.getElementById('n_finance_src_loc_total_equity_1').value ?
                            document.getElementById('n_finance_src_loc_total_equity_1').value : 0).toFixed(5);

                        if (n_finance_src_loc_total_equity_1 != n_total_equity_amounts) {
                            for (var i = 0; i < n_equity_amount_elements.length; i++) {
                                n_equity_amount_elements[i].classList.add('required', 'error');
                            }
                            document.getElementById('n_equity_amount_err').innerHTML = '<br/>Total equity amount should be equal to Total Equity (Million)';
                            return false;
                        } else {
                            for (var i = 0; i < n_equity_amount_elements.length; i++) {
                                n_equity_amount_elements[i].classList.remove('required', 'error');
                            }
                            document.getElementById('n_equity_amount_err').innerHTML = '';
                        }

                        //Proposed Loan Amount should be equal to Proposed Total Loan (Million) of 7. Source of finance
                        var n_loan_amount_elements = document.querySelectorAll('.n_loan_amount');
                        var n_total_loan_amounts = 0;
                        for (var i = 0; i < n_loan_amount_elements.length; i++) {
                            n_total_loan_amounts = n_total_loan_amounts + parseFloat(n_loan_amount_elements[i].value ? n_loan_amount_elements[i].value : 0);
                        }
                        n_total_loan_amounts = (n_total_loan_amounts).toFixed(5);
                        var n_finance_src_total_loan = parseFloat(document.getElementById('n_finance_src_total_loan').value ?
                            document.getElementById('n_finance_src_total_loan').value : 0).toFixed(5);

                        if (n_finance_src_total_loan != n_total_loan_amounts) {
                            for (var i = 0; i < n_loan_amount_elements.length; i++) {
                                n_loan_amount_elements[i].classList.add('required', 'error');
                            }
                            document.getElementById('n_loan_amount_err').innerHTML = '<br/>Total loan amount should be equal to Total Loan (Million)';
                            return false;
                        } else {
                            for (var i = 0; i < n_loan_amount_elements.length; i++) {
                                n_loan_amount_elements[i].classList.remove('required', 'error');
                            }
                            document.getElementById('n_loan_amount_err').innerHTML = '';
                        }
                    }


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
                    if($("#AnnualProductdata").val() == 1){
                        swal({
                            type: 'error',
                            title: 'Oops...',
                            text: 'Please add Annual production Information'
                        });

                        return false;
                    }
                    if($("#n_total_sales").val() != 100 && $('#n_foreign_sales_check').is(':checked')) {
                        $("#n_local_sales").addClass('error');
                        $("#n_foreign_sales").addClass('error');
                        // $("#n_direct_export").addClass('error');
                        // $("#n_deemed_export").addClass('error');
                        $('html, body').scrollTop($("#total_sales").offset().top);
                        $("#total_sales").focus();
                        swal({
                            type: 'error',
                            title: 'Oops...',
                            text: 'Proposed Total Sales can not be more than or less than 100%'
                        });
                        
                        return false;
                    } if($("#total_sales").val() != 100 ) {
                        // $("#deemed_export_per").addClass('error');
                        // $("#direct_export_per").addClass('error');
                        $("#local_sales_per").addClass('error');
                        $("#foreign_sales_per").addClass('error');
                        $('html, body').scrollTop($("#n_total_sales").offset().top);
                        $("#n_total_sales").focus();

                        swal({
                            type: 'error',
                            title: 'Oops...',
                            text: 'Existing Total Sales can not be more than or less than 100%'
                        });
                        
                        return false;
                    }
                        

                }

                if(newIndex == 3) {
                    // list of director
                    var total_director = 0;
                    $.ajax({
                        type: "GET",
                        url: "{{ url('/bida-registration-amendment/list-of-director-count') }}",
                        async: false,
                        data: {
                            _token: $('input[name="_token"]').val(),
                            application_id: '{{ Encryption::encodeId($appInfo->ref_id) }}',
                            process_type_id: '{{ Encryption::encodeId($appInfo->process_type_id) }}'
                        },
                        success: function (response) {
                            if (response.success === 200) {
                                total_director = response.total_director;
                            }
                        }
                    });

                    if (total_director < 1) {
                        swal({type: 'error', text: "Existing directors information is required."});
                        return false;
                    }
                    if ($('#investor_signature').hasClass('required') === true) {
                        $('#signature_error_msg').show();
                    }
                    if ($('#reset_image_investor_signature').hasClass('reset-image')){
                        $('#signature_error_msg').hide();
                    }

                    // Proposed investor signature
                    if ($("#n_list_of_director_check").is(':checked')) {
                        if ($('#n_investor_signature').hasClass('required') === true) {
                            $('#n_signature_error_msg').show();
                        }
                        if ($('#reset_image_n_investor_signature').hasClass('reset-image')){
                            $('#n_signature_error_msg').hide();
                        }
                    }

                }

                if(newIndex == 4) {
                    var ExlocalMachineryInvestment = document.getElementById('local_machinery_ivst').value;
                    var ProlocalMachineryInvestment = document.getElementById('n_local_machinery_ivst').value;
                    var totalImportedMachinery = document.getElementById('grand_imported_machinery').innerText;
                    var totalLocalMachinery = document.getElementById('grand_local_machinery').innerText;

                    var grand_total = (parseFloat(totalImportedMachinery) + parseFloat(totalLocalMachinery)).toFixed(5);

                    var totalMachineryInvestmentAmount = 0;
                    if (ProlocalMachineryInvestment != "" && ProlocalMachineryInvestment != 0) {
                        totalMachineryInvestmentAmount = parseFloat(ProlocalMachineryInvestment).toFixed(5);
                    } else {
                        totalMachineryInvestmentAmount = parseFloat(ExlocalMachineryInvestment).toFixed(5);
                    }

                    if(totalMachineryInvestmentAmount != grand_total) {
                        // swal({type: 'error', text: "Machinery & Equipment (Million) value and List of machinery to be imported value will be equal."});
                        swal({type: 'error', text: "Machinery & Equipment (Million) value will equal with the list of machinery to be imported and locally purchased value."});
                        return false;
                    }

                    return true;
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

                if(currentIndex == 5) {
                    form.find('#submitForm').css('display','block');

                    // $('#submitForm').on('click', function (e) {
                    //     form.validate().settings.ignore = ":disabled";
                    //     //console.log(form.validate().errors()); // show hidden errors in last step
                    //     return form.valid();
                    // });
                } else {
                    form.find('#submitForm').css('display','none');
                }
            },
            onFinishing: function (event, currentIndex) {
                form.validate().settings.ignore = ":disabled";
                return form.valid();
            },
            onFinished: function (event, currentIndex) {
                errorPlacement: function errorPlacement(error, element) {
                    console.log(error);
                    element.before(error);
                }
            }
        });

        var popupWindow = null;
        $('.finish').on('click', function (e) {
            if (form.valid()) {
                $('body').css({"display": "none"});
                popupWindow = window.open('<?php echo URL::to('/bida-registration-amendment/preview'); ?>', 'Sample', '');
            } else {
                return false;
            }
        });

        // Datepicker Plugin initialize
        $('.datepicker').datepicker({
            outputFormat: 'dd-MMM-y',
            // daysOfWeekDisabled: [5,6],
            theme : 'blue',
        });

        if (n_ceo_birthday == true) {
            $('#n_ceo_dob').datepicker('disable');
        }

        if (n_co_date == true) {
            $('#n_commercial_operation_date').datepicker('disable');
        }

        $('#ceo_country_id').change(function (e) {
            var country_id = this.value;
            if (country_id == '18') {
                $("#BDNIDExistingField").removeClass('hidden');
                $("#ceo_nid").addClass('required');
                $("#ceo_district_id").addClass('required');
                $("#ceo_thana_id").addClass('required');

                $("#ceo_state").removeClass('required');
                $("#ceo_City").removeClass('required');
                $("#ceo_passport_no").removeClass('required');
                $("#foreignExistingPassportField").addClass('hidden');

                $("#BDExistingTown").removeClass('hidden');
                $("#foreignExistingState").addClass('hidden');

                $("#BDExistingDistrict").removeClass('hidden');
                $("#foreignExistingCity").addClass('hidden');


            } else {
                $("#BDNIDExistingField").addClass('hidden');
                $("#foreignExistingPassportField").removeClass('hidden');

                $("#BDExistingTown").addClass('hidden');
                $("#foreignExistingState").removeClass('hidden');

                $("#BDExistingDistrict").addClass('hidden');
                $("#foreignExistingCity").removeClass('hidden');

                $("#ceo_nid").removeClass('required');
                $("#ceo_district_id").removeClass('required');
                $("#ceo_thana_id").removeClass('required');

                $("#ceo_state").addClass('required');
                $("#ceo_City").addClass('required');
                $("#ceo_passport_no").addClass('required');
            }
        });

        $('#n_ceo_country_id').change(function (e) {
            var n_ceo_country_id = this.value;
            if (n_ceo_country_id == '18') {
                $("#BDNIDProposedField").removeClass('hidden');
                $("#foreignProposedPassportField").addClass('hidden');

                $("#BDProposedTown").removeClass('hidden');
                $("#foreignProposedState").addClass('hidden');

                $("#BDProposedDistrict").removeClass('hidden');
                $("#foreignProposedCity").addClass('hidden');

                if ($('#n_ceo_nid_no_check').is(':not(:checked)')) {
                    $('#n_ceo_nid_no_check').click();
                }

                if ($('#n_ceo_district_id_check').is(':not(:checked)')) {
                    $('#n_ceo_district_id_check').click();
                }

                if ($('#n_ceo_thana_id_check').is(':not(:checked)')) {
                    $('#n_ceo_thana_id_check').click();
                }

            } else {
                $("#BDNIDProposedField").addClass('hidden');
                $("#foreignProposedPassportField").removeClass('hidden');

                $("#BDProposedTown").addClass('hidden');
                $("#foreignProposedState").removeClass('hidden');

                $("#BDProposedDistrict").addClass('hidden');
                $("#foreignProposedCity").removeClass('hidden');

                if (n_ceo_country_id != "") {
                    if ($('#n_ceo_passport_no_check').is(':not(:checked)')) {
                        $('#n_ceo_passport_no_check').click();
                    }

                    if ($('#n_ceo_City_check').is(':not(:checked)')) {
                        $('#n_ceo_City_check').click();
                    }

                    if ($('#n_ceo_state_check').is(':not(:checked)')) {
                        $('#n_ceo_state_check').click();
                    }
                }
            }
        });

        

        // $("#n_local_sales, #n_direct_export, #n_deemed_export").on('input', function () {
        $("#n_local_sales, #n_foreign_sales").on('input', function () {
            $("#n_deemed_export").removeClass('error');
            $("#n_direct_export").removeClass('error');
            $("#n_local_sales").removeClass('error');
            // var n_deemed_export =  $('#n_deemed_export').val() ? $('#n_deemed_export').val() : 0;
            // var n_direct_export =  $('#n_direct_export').val() ? $('#n_direct_export').val() : 0;
            var n_foreign_sales_per =  $('#n_foreign_sales').val() ? $('#n_foreign_sales').val() : 0;
            var n_local_sales_per =  $('#n_local_sales').val() ? $('#n_local_sales').val() : 0;

            if (n_local_sales_per <= 100 && n_local_sales_per >= 0) {
                var cal = parseInt(n_local_sales_per) + parseInt(n_foreign_sales_per);
                // var cal = parseInt(n_local_sales_per) + parseInt(n_direct_export) + parseInt(n_deemed_export);
                let total = cal.toFixed(2);
                $("#n_total_sales").val(total);
                
            } else {
                swal({
                    type: 'error',
                    title: 'Oops...',
                    text: 'Total Sales can not be more than 100% and less than 0%'
                });
                $('#n_local_sales').val(0);
                $('#n_foreign_sales').val(0);
                $("#n_total_sales").val(0);
                // $('#n_deemed_export').val(0);
                // $('#n_direct_export').val(0);
            }
            if($('#n_total_sales').val() > 100){
                swal({
                    type: 'error',
                    title: 'Oops...',
                    text: 'Total Sales can not be more than 100%'
                });
                $('#n_local_sales').val(0);
                $('#n_foreign_sales').val(0);
                // $('#n_direct_export').val(0);
                // $('#n_deemed_export').val(0);
                $("#n_total_sales").val(0);
            }
            
        });

        // $("#local_sales_per, #direct_export_per, #deemed_export_per").on('input', function () {
        $("#local_sales_per, #foreign_sales_per").on('input', function () {
            // $("#deemed_export_per").removeClass('error');
            // $("#direct_export_per").removeClass('error');
            $("#local_sales_per").removeClass('error');
            // var deemed_export =  $('#deemed_export_per').val() ? $('#deemed_export_per').val() : 0;
            // var direct_export =  $('#direct_export_per').val() ? $('#direct_export_per').val() : 0;
            var foreign_sales_per =  $('#foreign_sales_per').val() ? $('#foreign_sales_per').val() : 0;
            var local_sales_per =  $('#local_sales_per').val() ? $('#local_sales_per').val() : 0;


            if (local_sales_per <= 100 && local_sales_per >= 0) {
                var cal = parseInt(local_sales_per) + parseInt(foreign_sales_per);
                // var cal = parseInt(local_sales_per) + parseInt(deemed_export) + parseInt(direct_export);
                let total = cal.toFixed(2);
                $("#total_sales").val(total);
                
            } else {
                alert("Please select a value between 0 & 100");
                $('#local_sales_per').val(0);
                $('#foreign_sales_per').val(0);
                // $('#deemed_export_per').val(0);
                // $('#direct_export_per').val(0);
                $("#total_sales").val(0);
            }
            if($("#total_sales").val() >100){
                swal({
                    type: 'error',
                    title: 'Oops...',
                    text: 'Total Sales can not be more than 100%'
                });
                $('#local_sales_per').val(0);
                $('#foreign_sales_per').val(0);
                // $('#deemed_export_per').val(0);
                // $('#direct_export_per').val(0);
                $("#total_sales").val(0);
            }
        });
        //--------------end sales

        //------- Manpower start -------//
        $('#exiting_manpower').find('input').keyup(function () {
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

        $('#proposed_manpower').find('input').keyup(function () {
            var n_local_male = $('#n_local_male').val() ? parseFloat($('#n_local_male').val()) : 0;
            var n_local_female = $('#n_local_female').val() ? parseFloat($('#n_local_female').val()) : 0;
            var n_local_total = parseInt(n_local_male + n_local_female);
            $('#n_local_total').val(n_local_total);

            var n_foreign_male = $('#n_foreign_male').val() ? parseFloat($('#n_foreign_male').val()) : 0;
            var n_foreign_female = $('#n_foreign_female').val() ? parseFloat($('#n_foreign_female').val()) : 0;
            var n_foreign_total = parseInt(n_foreign_male + n_foreign_female);
            $('#n_foreign_total').val(n_foreign_total);

            var n_manpower_total = parseInt(n_local_total + n_foreign_total);
            $('#n_manpower_total').val(n_manpower_total);

            var n_manpower_local_ratio = parseFloat(n_local_total / n_manpower_total);
            var n_manpower_foreign_ratio = parseFloat(n_foreign_total / n_manpower_total);

            //---------- code from bida old
            n_manpower_local_ratio = ((n_local_total / n_manpower_total) * 100).toFixed(2);
            n_manpower_foreign_ratio = ((n_foreign_total / n_manpower_total) * 100).toFixed(2);
            if (n_foreign_total == 0) {
                n_manpower_local_ratio = n_local_total;
            } else {
                n_manpower_local_ratio = Math.round(parseFloat(n_local_total / n_foreign_total) * 100) / 100;
            }
            n_manpower_foreign_ratio = (n_foreign_total != 0) ? 1 : 0;
            // End of code from bida old -------------

            $('#n_manpower_local_ratio').val(n_manpower_local_ratio);
            $('#n_manpower_foreign_ratio').val(n_manpower_foreign_ratio);

        });
        //----------end manpower

        if ($("#n_organization_status_id").val() != '') {
            $("#n_organization_status_id").trigger('change');
        } else {
            $("#organization_status_id").trigger('change');
        }
        //$('input[name=is_approval_online]:checked').trigger('click');
        $('#ceo_country_id').trigger('change');
        $('#n_ceo_country_id').trigger('change');

        $("#local_sales_per").trigger('input');
        $("#n_local_sales").trigger('input');

        //trigger business class code function when document ready
        $("#business_class_code").keyup();
        $("#n_business_class_code").keyup();

        $("#office_division_id").trigger('change');
        $("#office_district_id").trigger('change');
        $("#n_office_division_id").trigger('change');
        $("#n_office_district_id").trigger('change');

        $("#factory_district_id").trigger('change');
        $("#n_factory_district_id").trigger('change');

        // trigger investment section 
        toggleCheckBoxForSourceOfFinance('investmentAndSourceOfFinance_check', ['n_local_land_ivst', 'n_local_building_ivst', 'n_local_machinery_ivst', 'n_local_others_ivst', 'n_local_wc_ivst', 'n_usd_exchange_rate', 'n_finance_src_loc_equity_1', 'n_finance_src_foreign_equity_1', 'n_finance_src_loc_loan_1', 'n_finance_src_foreign_loan_1', 'n_finance_src_loc_total_financing_1'], ['n_country_id', 'n_equity_amount', 'n_loan_amount'], ['n_total_fixed_ivst_million', 'n_total_fixed_ivst', 'n_total_fee', 'n_finance_src_loc_total_equity_1', 'n_finance_src_total_loan', 'n_finance_src_loc_total_financing_m']);

        //trigger PublicUtilityService section
        toggleCheckBoxForPublicUtilityService('n_public_land_check', ['n_public_land', 'n_public_electricity', 'n_public_gas', 'n_public_telephone', 'n_public_road', 'n_public_water', 'n_public_drainage', 'n_public_others']);

        getThanaByDistrictId('n_ceo_district_id','{{ $appInfo->n_ceo_district_id }}', 'n_ceo_thana_id','{{ $appInfo->n_ceo_thana_id }}');
    });

    function isBraApprovalManually() {
        var is_bra_approval_manually = $("input[name='is_bra_approval_manually']").val();
        if (is_bra_approval_manually == 'no') {
            $(".cusReadonly:radio:not(:checked)").prop('disabled', true); 
            $(".cusReadonly:checkbox:not(:checked)").prop('disabled', true);
            $('.cusReadonly option:not(:selected)').attr('disabled', true);

            $(".cusReadonly").attr("readonly", "readonly"); // Add readonly class for select element

            // Hide the specified element
            $("#manually_approved_bra_no_div").addClass('hidden');

            // Remove 'required' class from specified elements
            $("#manually_approved_br_no").removeClass('required');
            $("#manually_approved_br_date").removeClass('required');
            $("#manually_bra_approval_copy").removeClass('required');

        } else if (is_bra_approval_manually == 'yes') {
            $(".cusReadonly:radio:not(:checked)").prop('disabled', false);
            $(".cusReadonly:checkbox:not(:checked)").prop('disabled', false);
            $('.cusReadonly option:not(:selected)').attr('disabled', false);
            $(".cusReadonly").removeAttr('readonly');

            // Show the specified element
            $("#manually_approved_bra_no_div").removeClass('hidden');

            // Add 'required' class to specified elements
            $("#manually_approved_bra_no").addClass('required');
            $("#manually_approved_bra_date").addClass('required');
            $("#manually_bra_approval_copy").addClass('required');

            // Make specific fields readonly
            $('#ref_app_tracking_no').val();
            $('#ref_app_tracking_no, #ref_app_approve_date').prop('readonly', true);
        }

    }

    // Dynamic modal for business sub-class
    function openBusinessSectorModal(btn, type) {
        var this_action = btn.getAttribute('data-action');
        if (this_action != '') {
            $.get(this_action, {field_type: type}, function (data, success) {
                if (success === 'success') {
                    $('#businessClassModal .load_business_class_modal').html(data);
                } else {
                    $('#businessClassModal .load_business_class_modal').html('Unknown Error!');
                }
                $('#businessClassModal').modal('show', {backdrop: 'static'});
            });
        }
    }

    function existingFindBusinessClassCode(selectClass) {
        var old_value = '{{ $appInfo->sub_class_id == "0" ? "-1" : $appInfo->sub_class_id }}';
        var business_class_code = (selectClass !== undefined) ? selectClass : $("#business_class_code").val();
        var _token = $('input[name="_token"]').val();

        if (business_class_code != '' && (business_class_code.length > 3)) {
            $("#ex_business_class_list_of_code").text('');
            $("#ex_section_code").text('');
            $("#ex_section_name").text('');
            $("#ex_division_code").text('');
            $("#ex_division_name").text('');
            $("#ex_group_code").text('');
            $("#ex_group_name").text('');
            $("#ex_class_code").text('');
            $("#ex_class_name").text('');
            $("#sub_class").html = "";

            $.ajax({
                type: "GET",
                url: "/bida-registration-amendment/get-business-class-single-list",
                data: {
                    _token: _token,
                    business_class_code: business_class_code
                },
                success: function (response) {
                    if (response.responseCode == 1 && response.data.length != 0) {
                        $("#ex_business_class_list_of_code").text(response.data[0].code);
                        $("#ex_section_code").text(response.data[0].section_code);
                        $("#ex_section_name").text(response.data[0].section_name);
                        $("#ex_division_code").text(response.data[0].division_code);
                        $("#ex_division_name").text(response.data[0].division_name);
                        $("#ex_group_code").text(response.data[0].group_code);
                        $("#ex_group_name").text(response.data[0].group_name);
                        $("#ex_class_code").text(response.data[0].code);
                        $("#ex_class_name").text(response.data[0].name);

                        var option = '<option value="">Select One</option>';
                        $.each(response.subClass, function (id, value) {
                            if(id == old_value){
                                option += '<option value="'+ id + '" selected>' + value + '</option>';
                            }else{
                                option += '<option value="' + id + '">' + value + '</option>';
                            }
                        });
                        $("#sub_class").html(option);

                        otherSubClassCodeName(old_value, 'existing');
                    }
                }
            });
        }
    }

    function proposedFindBusinessClassCode(selectClass) {
        var old_value = '{{ $appInfo->n_sub_class_id == "0" ? "-1" : $appInfo->n_sub_class_id }}';
        var n_business_class_code = (selectClass !== undefined) ? selectClass : $("#n_business_class_code").val();
        var _token = $('input[name="_token"]').val();
        if (n_business_class_code != '' && (n_business_class_code.length > 3)) {
            $("#pro_business_class_list_of_code").text('');
            $("#pro_section_code").text('');
            $("#pro_section_name").text('');
            $("#pro_division_code").text('');
            $("#pro_division_name").text('');
            $("#pro_group_code").text('');
            $("#pro_group_name").text('');
            $("#pro_class_code").text('');
            $("#pro_class_name").text('');
            $("#n_sub_class").html = "";

            $.ajax({
                type: "GET",
                url: "/bida-registration-amendment/get-business-class-single-list",
                data: {
                    _token: _token,
                    business_class_code: n_business_class_code
                },
                success: function (response) {
                    if (response.responseCode == 1 && response.data.length != 0) {

                        $("#pro_business_class_list_of_code").text(response.data[0].code);
                        $("#pro_section_code").text(response.data[0].section_code);
                        $("#pro_section_name").text(response.data[0].section_name);
                        $("#pro_division_code").text(response.data[0].division_code);
                        $("#pro_division_name").text(response.data[0].division_name);
                        $("#pro_group_code").text(response.data[0].group_code);
                        $("#pro_group_name").text(response.data[0].group_name);
                        $("#pro_class_code").text(response.data[0].code);
                        $("#pro_class_name").text(response.data[0].name);
                        var option = '<option value="">Select One</option>';
                        
                        $.each(response.subClass, function (id, value) {
                            if(id == old_value){
                                option += '<option value="'+ id + '" selected>' + value + '</option>';
                            }else{
                                option += '<option value="' + id + '">' + value + '</option>';
                            }
                        });
                        // $("#n_sub_class").attr('disabled', false);
                        $("#n_sub_class").html(option);

                        otherSubClassCodeName(old_value, 'propose');
                    }
                }
            });
        }
    }

    function selectBusinessClass(btn) {
        var sub_class_code = btn.getAttribute('data-subclass');
        var field_type = btn.getAttribute('data-type');
        if (field_type == 'Existing') {
            $("#business_class_code").val(sub_class_code);
            existingFindBusinessClassCode(sub_class_code);
        }
        if (field_type == 'Proposed') {
            $("#n_business_class_code").val(sub_class_code);
            proposedFindBusinessClassCode(sub_class_code);
        }
        $("#closeBusinessModal").click();
    }

    function toggleCheckBox(boxId, newFieldId) {
        $.each(newFieldId, function (id, val) {
            if (document.getElementById(boxId).checked) {
                document.getElementById(val).disabled = false;
                if (val == 'n_male' || val == 'n_female'){
                    $("#" + val).attr("disabled", false);
                }

                var field = document.getElementById(val);

                if(boxId != 'n_public_land_check') {
                    $(field).addClass("required");
                }

                if (val == 'n_business_class_code') {
                    $("#BBSModal").removeClass('hidden');
                }

                if (val == 'n_investor_signature') {
                    $("#" + val).attr('disabled', false);
                    $("#" + val).parent('.btn-file').css('cursor', 'pointer');
                }

                if (val == 'n_ceo_dob') {
                    $("#" + val).datepicker('enable');
                }

                if (val == 'n_commercial_operation_date') {
                    $('#n_commercial_operation_date').datepicker('enable');
                }

                if (val === 'n_business_class_code') {
                    $('#n_sub_class').attr('disabled', false);
                    $('#n_other_sub_class_code').attr('disabled', false);
                    $('#n_other_sub_class_name').attr('disabled', false);
                }
            } else {
                document.getElementById(val).disabled = true;

                // trigger on category wise doc load
                CategoryWiseDocLoad($('#organization_status_id').val());

                if(boxId == 'n_foreign_sales_check'){
                    $('#n_direct_export').val('');
                    $('#n_deemed_export').val('');
                    $('#n_direct_export').attr('disabled', true);
                    $('#n_deemed_export').attr('disabled', true);
                    $('#n_total_sales').val('');
                }

                if (val == 'n_male' || val == 'n_female'){
                    $("#" + val).attr("disabled", true);
                    $("#" + val).attr('checked', false);
                }

                var field = document.getElementById(val);
                $(field).removeClass("required");
                $(field).removeClass("error");
                $(field).val("");

                if (val == 'n_business_class_code') {
                    $("#BBSModal").addClass('hidden');
                }

                if (val == 'n_investor_signature') {
                    $("#" + val).attr('disabled', true);
                    $("#" + val).parent('.btn-file').css('cursor', 'not-allowed');
                }

                if (val == 'n_ceo_dob') {
                    $("#" + val).datepicker('disable');
                }

                if (val == 'n_commercial_operation_date') {
                    $('#n_commercial_operation_date').datepicker('disable');
                }

                if (val === 'n_business_class_code') {
                    $('#n_sub_class').attr('disabled', true);
                    $('#n_other_sub_class_code').attr('disabled', true);
                    $('#n_other_sub_class_name').attr('disabled', true);
                    $('#pro_section_code').text('');
                    $('#pro_section_name').text('');
                    $('#pro_division_code').text('');
                    $('#pro_division_name').text('');
                    $('#pro_group_code').text('');
                    $('#pro_group_name').text('');
                    $('#pro_class_code').text('');
                    $('#pro_class_name').text('');
                    $("#pro_business_class_list_of_code").text('');
                    $("#n_sub_class").text('');
                }
            }
        })
    }

    function toggleCheckBoxForSourceOfFinance(boxId, fieldId, countryOfSourceOfFinanceClass, resetFieldsValue) {
        $.each(fieldId, function (id, val) {
            if (document.getElementById(boxId).checked) {
                document.getElementById(val).disabled = false;
            } else {
                document.getElementById(val).disabled = true;
                document.getElementById(val).value = "";
            }
        });

        $.each(countryOfSourceOfFinanceClass, function(id, val){
            if (document.getElementById(boxId).checked) {
                $("." + val).attr("disabled", false);
            } else {
                $("." + val).attr("disabled", true);
                $("." + val).val("");
            }
        });

        if ($('#' + boxId).is(':not(:checked)')) {
            $.each(resetFieldsValue, function(id, val){
                document.getElementById(val).value = "";
            });
            $('#n_finance_src_loc_total_financing_1_alert').hide();
            $('#n_finance_src_loc_total_financing_1').removeClass('required error');
        }
    }

    function toggleCheckBoxForPublicUtilityService(boxId, newFieldId) {
        $.each(newFieldId, function (id, val) {
            if (document.getElementById(boxId).checked) {
                document.getElementById(val).disabled = false;
            } else {
                document.getElementById(val).disabled = true;
                $("#" + val).prop('checked', false);
            }
        })
    }

    //--------File Upload Script Start----------//
    function uploadDocument(targets, id, vField, isRequired) {
//        alert(111);
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
            document.getElementById(targets).innerHTML = '<input type="hidden" class="required" value="" id="' + vField + '" name="' + vField + '">';
            if ($('#label_' + id).length) $('#label_' + id).remove();
            return false;
        }

        try {
            document.getElementById("isRequired").value = isRequired;
            document.getElementById("selected_file").value = id;
            document.getElementById("validateFieldName").value = vField;
            document.getElementById(targets).style.color = "red";
            var action = "{{url('/bida-registration/upload-document')}}";

            $("#" + targets).html('Uploading....');
            var file_data = $("#" + id).prop('files')[0];
            var form_data = new FormData();
            form_data.append('selected_file', id);
            form_data.append('isRequired', isRequired);
            form_data.append('validateFieldName', vField);
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
                    var newInput = $('<label class="saved_file_' + doc_id + '" id="label_' + id + '"><br/><b>File: ' + fileNameArr[l - 1] +
                        ' <a href="javascript:void(0)" onclick="EmptyFile(' + doc_id
                        + ', '+ isRequired +')"><span class="btn btn-xs btn-danger"><i class="fa fa-times"></i></span> </a></b></label>');
                    //var newInput = $('<label id="label_' + id + '"><br/><b>File: ' + fileNameArr[l - 1] + '</b></label>');
                    $("#" + id).after(newInput);
                    //check valid data
                    document.getElementById(id).value = '';
                    var validate_field = $('#' + vField).val();
                    if (validate_field != '') {
                        $("#"+id).removeClass('required');
                    }
                }
            });
        } catch (err) {
            document.getElementById(targets).innerHTML = "Sorry! Something Wrong.";
        }
    }

    // add more script
    function addTableRowBRA(tableID, templateRow) {
        //Direct Copy a row to many times
        var x = document.getElementById(templateRow).cloneNode(true);
        x.id = "";
        x.style.display = "";
        var table = document.getElementById(tableID);
        var rowCount = $('#' + tableID).find('tr').length - 1;
        var lastTr = $('#' + tableID).find('tr').last().attr('data-number');
        var production_desc_val = $('#' + tableID).find('tr').last().find('.production_desc_1st').val();
        if (lastTr != '' && typeof lastTr !== "undefined") {
            rowCount = parseInt(lastTr) + 1;
        }
        //var rowCount = table.rows.length;
        //Increment id
        var rowCo = rowCount;
        var idText = 'rowCount' + tableID + rowCount;
        x.id = idText;
        $("#" + tableID).append(x);

        //New datepiker remove after cloning
        $("#" + tableID).find('#' + idText).find('.datepicker-button').remove();
        $("#" + tableID).find('#' + idText).find('.datepicker-calendar').remove();

        //get select box elements
        var attrSel = $("#" + tableID).find('#' + idText).find('select');
        //edited by ishrat to solve select box id auto increment related bug
        for (var i = 0; i < attrSel.length; i++) {
            var nameAtt = attrSel[i].name;
            var repText = nameAtt.replace('[0]', '[' + rowCo + ']'); //increment all array element name
            attrSel[i].name = repText;
        }
        // end of  solving issue related select box id auto increment related bug by ishrat

        var data = [];
        //get input elements
        var attrInput = $("#" + tableID).find('#' + idText).find('input');
        //change input field id
        for (var i = 0; i < attrInput.length; i++) {
            var idAtt = attrInput[i].id;
            var repText = idAtt.replace(idAtt, idAtt + rowCount++);
            attrInput[i].id = repText;
            data.push(attrInput[i].id);
        }

        //change input field name
        for (var i = 0; i < attrInput.length; i++) {
            var nameAtt = attrInput[i].name;
            //increment all array element name
            var repText = nameAtt.replace('[0]', '[' + rowCo + ']');
            attrInput[i].name = repText;
        }
        attrInput.val(''); //value reset

        //change select field id
        for (var i = 0; i < attrSel.length; i++) {
            var idAtt = attrSel[i].id;
            var repText = idAtt.replace(idAtt, idAtt + rowCo++); //increment all array element name
            attrSel[i].id = repText;
            data.push(attrSel[i].id);
        }
        attrSel.val(''); //value reset

        //edited by ishrat to solve textarea id auto increment related bug
        //get textarea elements
        var attrTextarea = $("#" + tableID).find('#' + idText).find('textarea');
        for (var i = 0; i < attrTextarea.length; i++) {
            var nameAtt = attrTextarea[i].name;
            //increment all array element name
            var repText = nameAtt.replace('[0]', '[' + rowCo + ']');
            attrTextarea[i].name = repText;
            $('#' + idText).find('.readonlyClass').prop('readonly', true);
        }
        attrTextarea.val(''); //value reset
        // end of  solving issue related textarea id auto increment related bug by ishrat

        //$('.m_currency ').prop('selectedIndex', 102);
        //Class change by btn-danger to btn-primary
        $("#" + tableID).find('#' + idText).find('.addTableRows').removeClass('btn-primary').addClass('btn-danger')
            .attr('onclick', 'removeTableRow("' + tableID + '","' + idText + '")');
        $("#" + tableID).find('#' + idText).find('.addTableRows > .fa').removeClass('fa-plus').addClass('fa-times');
        $('#' + tableID).find('tr').last().attr('data-number', rowCount);

        //checkbox toggle script
        // $("#" + tableID).find('#' + idText).find('td:first').on('click', function (e) {
        //     var boxId = e.target.id;
        //     if (document.getElementById(boxId).checked) {
        //         for (var i = 2; i < data.length; i++) {
        //             document.getElementById(data[i]).disabled = false;
        //             var field = document.getElementById(data[i]);
        //             $(field).addClass("required");
        //         }
        //     } else {
        //         for (var i = 2; i < data.length; i++) {
        //             document.getElementById(data[i]).disabled = true;
        //             var field = document.getElementById(data[i]);
        //             $(field).removeClass("required");
        //             $(field).removeClass("error");
        //             $(field).val("");
        //         }
        //     }
        // });

        $("#" + tableID).find('.datepicker').datepicker({
            outputFormat: 'dd-MMM-y',
            // daysOfWeekDisabled: [5,6],
            theme : 'blue',
        });
    } // end of addTableRow() function

    //--------File Upload Script End----------//

    //-----investment calculation
    function CalculateTotalExistingInvestmentTk() {
        var land = parseFloat(document.getElementById("local_land_ivst").value);
        var building = parseFloat(document.getElementById("local_building_ivst").value);
        var machine = parseFloat(document.getElementById("local_machinery_ivst").value);
        var other = parseFloat(document.getElementById("local_others_ivst").value);
        var wcCapital = parseFloat(document.getElementById("local_wc_ivst").value);

        var totalInvest = ((isNaN(land) ? 0 : land) + (isNaN(building) ? 0 : building) + (isNaN(machine) ? 0 : machine) + (isNaN(other) ? 0 : other) + (isNaN(wcCapital) ? 0 : wcCapital)).toFixed(5);
        var totalTk = (totalInvest * 1000000).toFixed(2);
        document.getElementById('total_fixed_ivst_million').value = totalInvest;
        document.getElementById('total_fixed_ivst').value = totalTk;

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
        $("#total_fee").val(fee);
    }
    //---------end--------

    //----------6. Propose investment section auto calculation
    function CalculateTotalProposeInvestmentTk() {
        var land = parseFloat(document.getElementById("n_local_land_ivst").value);
        var building = parseFloat(document.getElementById("n_local_building_ivst").value);
        var machine = parseFloat(document.getElementById("n_local_machinery_ivst").value);
        var other = parseFloat(document.getElementById("n_local_others_ivst").value);
        var wcCapital = parseFloat(document.getElementById("n_local_wc_ivst").value);

        var totalInvest = ((isNaN(land) ? 0 : land) + (isNaN(building) ? 0 : building) + (isNaN(machine) ? 0 : machine) + (isNaN(other) ? 0 : other) + (isNaN(wcCapital) ? 0 : wcCapital)).toFixed(5);
        var totalTk = (totalInvest * 1000000).toFixed(2);
        document.getElementById('n_total_fixed_ivst_million').value = totalInvest;
        document.getElementById('n_total_fixed_ivst').value = totalTk;

        $("#n_total_fee").val(1000);
    }
    //7. Source of Finance Foreign Equity for existing section
    function calculateSourceOfFinanceForExisting() {
        var local_equity = $('#finance_src_loc_equity_1').val() ? parseFloat($('#finance_src_loc_equity_1').val()) : 0;
        var foreign_equity = $('#finance_src_foreign_equity_1').val() ? parseFloat($('#finance_src_foreign_equity_1').val()) : 0;
        var total_equity = (local_equity + foreign_equity);

        $('#finance_src_loc_total_equity_1').val(total_equity.toFixed(5));

        var local_loan = $('#finance_src_loc_loan_1').val() ? parseFloat($('#finance_src_loc_loan_1').val()) : 0;
        var foreign_loan = $('#finance_src_foreign_loan_1').val() ? parseFloat($('#finance_src_foreign_loan_1').val()) : 0;
        var total_loan = (local_loan + foreign_loan);

        $('#finance_src_total_loan').val(total_loan.toFixed(5));

        // Convert into million
        var total_finance_million = (parseFloat(total_equity) + parseFloat(total_loan)).toFixed(5);
        var total_finance = (total_finance_million * 1000000).toFixed(2);
        $('#finance_src_loc_total_financing_m').val(total_finance_million);
        $('#finance_src_loc_total_financing_1').val(total_finance);

        //Check Total Financing and  Total Investment (BDT) is equal
        var total_fixed_ivst_bd = $("#total_fixed_ivst").val();
        $('#finance_src_loc_total_financing_1_alert').hide();
        $('#finance_src_loc_total_financing_1').removeClass('required error');
        if (!(total_fixed_ivst_bd == total_finance)) {
            $('#finance_src_loc_total_financing_1').addClass('required error');
            $('#finance_src_loc_total_financing_1_alert').show();
            $('#finance_src_loc_total_financing_1_alert').text('Total Financing and Total Investment (BDT) must be equal.');
        }
    }

    //7. Source of Finance Foreign Equity for existing section
    function calculateSourceOfFinanceForPropose() {
        var local_equity = $('#n_finance_src_loc_equity_1').val() ? parseFloat($('#n_finance_src_loc_equity_1').val()) : 0;
        var foreign_equity = $('#n_finance_src_foreign_equity_1').val() ? parseFloat($('#n_finance_src_foreign_equity_1').val()) : 0;
        var total_equity = (local_equity + foreign_equity);

        $('#n_finance_src_loc_total_equity_1').val(total_equity.toFixed(5));

        var local_loan = $('#n_finance_src_loc_loan_1').val() ? parseFloat($('#n_finance_src_loc_loan_1').val()) : 0;
        var foreign_loan = $('#n_finance_src_foreign_loan_1').val() ? parseFloat($('#n_finance_src_foreign_loan_1').val()) : 0;
        var total_loan = (local_loan + foreign_loan);

        $('#n_finance_src_total_loan').val(total_loan.toFixed(5));

        // Convert into million
        var total_finance_million = (parseFloat(total_equity) + parseFloat(total_loan)).toFixed(5);
        var total_finance = (total_finance_million * 1000000).toFixed(2);
        $('#n_finance_src_loc_total_financing_m').val(total_finance_million);
        $('#n_finance_src_loc_total_financing_1').val(total_finance);

        //Check Total Financing and  Total Investment (BDT) is equal
        var total_fixed_ivst_bd = $("#n_total_fixed_ivst").val();
        $('#n_finance_src_loc_total_financing_1_alert').hide();
        $('#n_finance_src_loc_total_financing_1').removeClass('required error');
        if (!(total_fixed_ivst_bd == total_finance)) {
            $('#n_finance_src_loc_total_financing_1').addClass('required error');
            $('#n_finance_src_loc_total_financing_1_alert').show();
            $('#n_finance_src_loc_total_financing_1_alert').text('Total Financing and Total Investment (BDT) must be equal.');
        }
    }

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

        $("#n_ceo_telephone_no").intlTelInput({
            hiddenInput: "n_ceo_telephone_no",
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

        $("#n_ceo_mobile_no").intlTelInput({
            hiddenInput: "n_ceo_mobile_no",
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

        $("#n_office_telephone_no").intlTelInput({
            hiddenInput: "n_office_telephone_no",
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

        $("#n_office_mobile_no").intlTelInput({
            hiddenInput: "n_office_mobile_no",
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

        $("#n_factory_mobile_no").intlTelInput({
            hiddenInput: "n_factory_mobile_no",
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

        $("#n_factory_telephone_no").intlTelInput({
            hiddenInput: "n_factory_telephone_no",
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

        $("#applicationForm").find('.iti').css({"width":"-moz-available", "width":"-webkit-fill-available"});
        {{--initail -input plugin script end--}}
    });

    //section 11 existing total price calculation ...
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

    //section 11 existing total price calculation ...
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

    //section 11 proposed total price calculation ...
    function nTotalMachineryEquipmentQty() {
        var n_machinery_local_qty = document.getElementById('n_machinery_local_qty').value;
        var n_imported_qty = document.getElementById('n_imported_qty').value;
        if (n_machinery_local_qty == '') {
            n_machinery_local_qty = 0;
        }
        if (n_imported_qty == '') {
            n_imported_qty = 0;
        }
        var n_total = parseFloat(n_machinery_local_qty) + parseFloat(n_imported_qty);
        if (isNaN(n_total)) {
            n_total = 0;
        }
        $('#n_total_machinery_qty').val(n_total);
    }

    //section 11 proposed total price calculation ...
    function nTotalMachineryEquipmentPrice() {
        var n_machinery_local_price_bdt = document.getElementById('n_machinery_local_price_bdt').value;
        var n_imported_qty_price_bdt = document.getElementById('n_imported_qty_price_bdt').value;
        if (n_machinery_local_price_bdt == '') {
            n_machinery_local_price_bdt = 0;
        }
        if (n_imported_qty_price_bdt == '') {
            n_imported_qty_price_bdt = 0;
        }

        var n_total = parseFloat(n_machinery_local_price_bdt) + parseFloat(n_imported_qty_price_bdt);
        if (isNaN(n_total)) {
            n_total = 0;
        }
        $('#n_total_machinery_price').val(n_total);
    }


    function openBraModal(btn, target) {
        let this_action = btn.getAttribute('data-action');
        let target_id = $("#"+target);
        if (this_action !== '') {
            $.get(this_action, function (data, success) {
                if (success === 'success') {
                    target_id.find('.load_modal').html(data);
                    //$('#braModal .load_modal').html(data);
                } else {
                    target_id.find('.load_modal').html('Unknown Error!');
                    //$('#braModal .load_modal').html('Unknown Error!');
                }
                target_id.modal('show', {backdrop: 'static'});
            });
        }
    }

    function actionWiseFieldDisable(field, existingIds, proposedIds) {
        let action = field.value;
        let is_online_approval ="{{ $appInfo->is_approval_online }}";

        if (action === "add" && is_online_approval === "yes") {
            $.each(existingIds, function (id, value) {
                // $("#" + value).attr('readonly', 'readonly');
                $("#" + value).removeAttr('readonly');
            });

            $.each(proposedIds, function (id, value) {
                $("#" + value).removeAttr('readonly');
            });

        } else if (action === "add" && is_online_approval === "no") {
            $.each(existingIds, function (id, value) {
                $("#" + value).removeAttr('readonly');
            });

            $.each(proposedIds, function (id, value) {
                $("#" + value).removeAttr('readonly');
            });
        } else if(action === "edit") {
            if (is_online_approval === 'yes') {
                $.each(existingIds, function (id, value) {
                    $("#" + value).attr('readonly');
                });
            } else {
                $.each(existingIds, function (id, value) {
                    $("#" + value).removeAttr('readonly');
                });
            }
            $.each(proposedIds, function (id, value) {
                $("#" + value).removeAttr('readonly');
            });

        } else {
            $.each(existingIds, function (id, value) {
                $("#" + value).attr('readonly', 'readonly');
            });

            $.each(proposedIds, function (id, value) {
                $("#" + value).val('');
                $("#" + value).attr('readonly', 'readonly');
            });
        }
    }

    function confirmDelete(url, type) {
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

                            if (type == 'listOfDirectors') {
                                listOfDirectors(20, 'off');
                            } else if (type == 'apc') {
                                loadAnnualProductionCapacityData(20, 'off');
                            } else if (type == 'imported_machinery') {
                                loadImportedMachineryData(20, 'off');
                            } else if (type == 'local_machinery') {
                                loadLocalMachineryData(20, 'off');
                            }
                        } else {
                            swal('Oops!', response.msg, 'warning');
                        }
                    }
                });
            } else {
                return false;
            }
        })
    }

    function otherSubClassCodeName(value, amendment_type) {
        var existingSubClass = $('#sub_class').val();
        var proposeSubClass = $('#n_sub_class').val();

        if (value == '-1') {
            $("#other_sub_class_code_parent").removeClass('hidden');
            $("#other_sub_class_name_parent").removeClass('hidden');

            if (amendment_type == 'existing') {
                $("#other_sub_class_name").removeClass('hidden');
                $("#other_sub_class_code").removeClass('hidden');
                $("#other_sub_class_code").addClass('required');
                $("#other_sub_class_name").addClass('required');
            }
            
            if (amendment_type == 'propose') {
                $("#n_other_sub_class_name").removeClass('hidden');
                $("#n_other_sub_class_code").removeClass('hidden');
                $("#n_other_sub_class_name").addClass('required');
            }            
        } else {
            if (existingSubClass != '-1' && proposeSubClass != '-1') {
                $("#other_sub_class_code_parent").addClass('hidden');
                $("#other_sub_class_name_parent").addClass('hidden');
            }
            if (amendment_type == 'existing') {
                $("#other_sub_class_name").addClass('hidden');
                $("#other_sub_class_code").addClass('hidden');
                $("#other_sub_class_name").removeClass('required');
                $("#other_sub_class_code").removeClass('required');
            }
            if (amendment_type == 'propose') {
                $("#n_other_sub_class_name").addClass('hidden');
                $("#n_other_sub_class_code").addClass('hidden');
                $("#n_other_sub_class_name").removeClass('required');
            }
        }
    }
</script>