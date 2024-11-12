<?php
$accessMode = ACL::getAccsessRight('BidaRegistration');
if (!ACL::isAllowed($accessMode, $mode)) {
    die('You have no access right! Please contact with system admin if you have any query.');
}
?>

<link rel="stylesheet" href="{{ url('assets/css/jquery.steps.css') }}">

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
        width: 16.65% !important;
    }

    .wizard > .steps > ul > li a {
        padding: 0.5em 0.5em !important;
    }

    .wizard > .steps .number {
        font-size: 1.2em;
    }

    .wizard > .actions {
        top: -42px;
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
        width: 100%;
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

    .croppie-container .cr-slider-wrap {
        width: 100% !important;
        margin: 5px auto !important;
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

    .tab-content {
        float: left;
        margin-bottom: 20px;
    }

    .tab-content .visaTypeTabPane.active {
        border: 1px solid #ccc !important;
        float: left;
        border-radius: 4px;
    }

    .ml-1 {
        margin-left: 10px;
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
                            <h5><strong>Apply Industrial Project Registration to Bangladesh </strong></h5>
                        </div>

                        <div class="pull-right">
                            <a href="{{ asset('assets/images/SampleForm/BIDA_Registration.pdf') }}" target="_blank" class="btn btn-warning">
                                <i class="fas fa-file-pdf"></i>
                                Download Sample Form
                            </a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-body">

                        <div>
                            {!! Form::open(array('url' => '/bida-registration/add','method' => 'post','id' => 'BidaRegistrationForm','role'=>'form','enctype'=>'multipart/form-data')) !!}
                            <input type="hidden" name="selected_file" id="selected_file"/>
                            <input type="hidden" name="validateFieldName" id="validateFieldName"/>
                            <input type="hidden" name="isRequired" id="isRequired"/>
                            <input type="hidden" value="{{$usdValue->bdt_value}}" id="crvalue">

                            <h3 class="stepHeader">
                                {{--                                <span data-toggle="tooltip" data-placement="left" title="Tooltip on left">Registration Info</span>--}}
                                Registration Info
                            </h3>
                            <fieldset>

                                <div class="panel panel-info">
                                    <div class="panel-heading margin-for-preview"><strong>Company
                                            Information</strong></div>
                                    <div class="panel-body">
                                        <div class="readOnlyCl ">
                                            <div id="validationError"></div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12 form-group {{$errors->has('company_name') ? 'has-error': ''}}">
                                                        {!! Form::label('company_name','Name of Organization/ Company/ Industrial Project (English)',['class'=>'col-md-5 text-left']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('company_name', CommonFunction::getCompanyNameById(Auth::user()->company_ids), ['class' => 'form-control input-md', 'readonly']) !!}
                                                            {!! $errors->first('company_name','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 form-group {{$errors->has('company_name_bn') ? 'has-error': ''}}">
                                                        {!! Form::label('company_name_bn','Name of Organization/ Company/ Industrial Project (Bangla)',['class'=>'col-md-5 text-left']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('company_name_bn', CommonFunction::getCompanyBnNameById(Auth::user()->company_ids), ['class' => 'form-control input-md', 'readonly']) !!}
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
                                                            {!! Form::select('organization_type_id', $eaOrganizationType, $getCompanyData->organization_type_id, ['class' => 'form-control  input-md ','id'=>'organization_type_id']) !!}
                                                            {!! $errors->first('organization_type_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 {{$errors->has('organization_status_id') ? 'has-error': ''}}">
                                                        {!! Form::label('organization_status_id','Status of the organization',['class'=>'col-md-5 text-left']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::select('organization_status_id', $eaOrganizationStatus, $getCompanyData->organization_status_id, ['class' => 'form-control input-md','id'=>'organization_status_id']) !!}
                                                            {{-- {!! Form::select('organization_status_id', $eaOrganizationStatus, $getCompanyData->organization_status_id, ['class' => 'form-control input-md','id'=>'organization_status_id', 'onchange' => "CategoryWiseDocLoad(this.value)"]) !!} --}}

                                                            {{--                                                        <input type="hidden" name="organization_status_id" id="organization_status_id" value="{{ $getCompanyData->organization_status_id }}">--}}
                                                            {{--                                                        <select class="form-control cusReadonly input-md" id="app_type_mapping_id" name="app_type_mapping_id"--}}
                                                            {{--                                                                onchange="CategoryWiseDocLoad(this.value, this.options[this.selectedIndex].getAttribute('app_type_id'))">--}}
                                                            {{--                                                            <option value="">Select status</option>--}}
                                                            {{--                                                            @foreach($app_category as $category)--}}
                                                            {{--                                                                <option value="{{ $category->app_type_mapping_id }}" app_type_id="{{ $category->app_type_id }}"--}}
                                                            {{--                                                                        {{ ($eaOrganizationStatus[$getCompanyData->organization_status_id] == $category->name ? 'selected' : '') }}--}}
                                                            {{--                                                                >{{ $category->name }}</option>--}}
                                                            {{--                                                            @endforeach--}}
                                                            {{--                                                        </select>--}}

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
                                                            {!! Form::select('ownership_status_id', $eaOwnershipStatus, $getCompanyData->ownership_status_id, ['class' => 'form-control  input-md ','id'=>'ownership_status_id']) !!}
                                                            {!! $errors->first('ownership_status_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 country_of_origin_div">
                                                        {!! Form::label('country_of_origin_id','Country of Origin',['class'=>'col-md-5 text-left']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::select('country_of_origin_id',$countriesWithoutBD, $getCompanyData->country_of_origin_id,['class'=>'form-control input-md', 'id' => 'country_of_origin_id']) !!}
                                                            {!! $errors->first('country_of_origin_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12 {{$errors->has('project_name') ? 'has-error': ''}}">
                                                        {!! Form::label('project_name','Name of the project',['class'=>'col-md-3 text-left']) !!}
                                                        <div class="col-md-9">
                                                            {!! Form::text('project_name', '', ['class' => 'form-control  input-md ','id'=>'project_name']) !!}
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
                                                            {!! Form::text('business_class_code', null, ['class' => 'form-control required input-md', 'min' => 4,'onkeyup' => 'findBusinessClassCode()']) !!}
                                                            <input type="hidden" name="is_valid_bbs_code" id="is_valid_bbs_code"/>
                                                            <span class="help-text" style="margin: 5px 0;">
                                                            <a style="cursor: pointer;" data-toggle="modal"
                                                               data-target="#businessClassModal"
                                                               onclick="openBusinessSectorModal(this)"
                                                               data-action="/bida-registration/get-business-class-modal">
                                                                If you don't know the exact code, please select from the list.
                                                            </a>
                                                        </span>
                                                            {!! $errors->first('business_class_code','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div id="no_business_class_result"></div>

                                                        <fieldset class="scheduler-border hidden"
                                                                  id="business_class_list_sec">
                                                            <legend class="scheduler-border">Info. based on your business class (Code = <span id="business_class_list_of_code"></span>)</legend>
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
                                                        {!! Form::label('major_activities','Major activities in brief',['class'=>'col-md-3']) !!}
                                                        <div class="col-md-9">
                                                            {!! Form::textarea('major_activities', $getCompanyData->major_activities, ['class' => 'form-control input-md bigInputField maxTextCountDown', 'size' =>'5x2','data-rule-maxlength'=>'240', 'placeholder' => 'Maximum 240 characters', "data-charcount-maxlength" => "240"]) !!}
                                                            {!! $errors->first('major_activities','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{--                                        <div class="form-group">--}}
                                        {{--                                            <div class="row">--}}
                                        {{--                                                <div class="col-md-12 {{$errors->has('approval_center_id') ? 'has-error': ''}}">--}}
                                        {{--                                                    {!! Form::label('approval_center_id','Approval Center',['class'=>'col-md-3 text-left']) !!}--}}
                                        {{--                                                    <div class="col-md-9">--}}
                                        {{--                                                        {!! Form::select('approval_center_id', $approvalCenterList, $getCompanyData->approval_center_id, ['class' => 'form-control  input-md ','id'=>'approval_center_id']) !!}--}}
                                        {{--                                                        {!! $errors->first('approval_center_id','<span class="help-block">:message</span>') !!}--}}
                                        {{--                                                    </div>--}}
                                        {{--                                                </div>--}}
                                        {{--                                            </div>--}}
                                        {{--                                        </div>--}}

                                    </div>

                                </div>


                                <div class="panel panel-info">
                                    <div class="panel-heading "><strong>Information of Principal
                                            Promoter/Chairman/Managing Director/CEO/Country Manager</strong></div>
                                    <div class="panel-body readOnlyCl">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('ceo_country_id') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_country_id','Country ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('ceo_country_id', $countries, $getCompanyData->ceo_country_id, ['class' => 'form-control  input-md ','id'=>'ceo_country_id']) !!}
                                                        {!! $errors->first('ceo_country_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_dob') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_dob','Date of Birth',['class'=>'col-md-5']) !!}
                                                    <div class=" col-md-7">
                                                        <div class="datepicker input-group date"
                                                             data-date-format="dd-mm-yyyy">
                                                            {!! Form::text('ceo_dob', (!empty($getCompanyData->ceo_dob) ? date('d-M-Y', strtotime($getCompanyData->ceo_dob)) : ''), ['class'=>'form-control input-md', 'id' => 'ceo_dob', 'placeholder'=>'Pick from datepicker']) !!}
                                                            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
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
                                                        {!! Form::text('ceo_passport_no', $getCompanyData->ceo_passport_no, ['maxlength'=>'20',
                                                        'class' => 'form-control input-md ', 'id'=>'ceo_passport_no']) !!}
                                                        {!! $errors->first('ceo_passport_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div id="ceo_nid_div"
                                                     class="col-md-6 {{$errors->has('ceo_nid') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_nid','NID No.',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_nid', $getCompanyData->ceo_nid, ['maxlength'=>'20',
                                                        'class' => 'form-control number input-md  bd_nid','id'=>'ceo_nid']) !!}
                                                        {!! $errors->first('ceo_nid','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_designation') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_designation','Designation', ['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_designation', $getCompanyData->ceo_designation,
                                                        ['maxlength'=>'80','class' => 'form-control input-md ']) !!}
                                                        {!! $errors->first('ceo_designation','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('ceo_full_name') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_full_name','Full Name',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_full_name', $getCompanyData->ceo_full_name, ['maxlength'=>'80',
                                                        'class' => 'form-control input-md ']) !!}
                                                        {!! $errors->first('ceo_full_name','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div id="ceo_district_div"
                                                     class="col-md-6 {{$errors->has('ceo_district_id') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_district_id','District/City/State ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('ceo_district_id',$districts, $getCompanyData->ceo_district_id, ['maxlength'=>'80','class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('ceo_district_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div id="ceo_city_div"
                                                     class="col-md-6 hidden {{$errors->has('ceo_city') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_city','District/City/State',['class'=>'text-left  col-md-5 ']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_city', $getCompanyData->ceo_city,['class' => 'form-control input-md']) !!}
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
                                                        {!! Form::text('ceo_state', $getCompanyData->ceo_state,['class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('ceo_state','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div id="ceo_thana_div"
                                                     class="col-md-6 {{$errors->has('ceo_thana_id') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_thana_id','Police Station/Town ',['class'=>'col-md-5 text-left required-star','placeholder'=>'Select district first']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('ceo_thana_id',$thana, $getCompanyData->ceo_thana_id, ['maxlength'=>'80','class' => 'form-control input-md','placeholder' => 'Select district first']) !!}
                                                        {!! $errors->first('ceo_thana_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_post_code') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_post_code','Post/Zip Code ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_post_code', $getCompanyData->ceo_post_code, ['maxlength'=>'80','class' => 'form-control input-md engOnly ']) !!}
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
                                                        {!! Form::text('ceo_address', $getCompanyData->ceo_address, ['maxlength'=>'150','class' => 'bigInputField form-control input-md ']) !!}
                                                        {!! $errors->first('ceo_address','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_telephone_no') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_telephone_no','Telephone No.',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_telephone_no', $getCompanyData->ceo_telephone_no, ['maxlength'=>'20','class' => 'form-control input-md helpText15 phone_or_mobile']) !!}
                                                        {!! $errors->first('ceo_telephone_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('ceo_mobile_no') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_mobile_no','Mobile No. ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_mobile_no',  $getCompanyData->ceo_mobile_no, ['class' => 'form-control input-md helpText15 phone_or_mobile']) !!}
                                                        {!! $errors->first('ceo_mobile_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_father_name') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_father_name','Father\'s Name',['class'=>'col-md-5 text-left', 'id' => 'ceo_father_label']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_father_name', $getCompanyData->ceo_father_name, ['class' => 'form-control textOnly input-md ']) !!}
                                                        {!! $errors->first('ceo_father_name','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('ceo_email') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_email','Email ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_email', $getCompanyData->ceo_email, ['class' => 'form-control email input-md']) !!}
                                                        {!! $errors->first('ceo_email','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_mother_name') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_mother_name','Mother\'s Name',['class'=>'col-md-5 text-left', 'id' => 'ceo_mother_label']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_mother_name', $getCompanyData->ceo_mother_name, ['class' => 'form-control textOnly  input-md']) !!}
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
                                                        {!! Form::text('ceo_fax_no', $getCompanyData->ceo_fax_no, ['class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('ceo_fax_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('ceo_spouse_name') ? 'has-error': ''}}">
                                                    {!! Form::label('ceo_spouse_name','Spouse name',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('ceo_spouse_name', $getCompanyData->ceo_spouse_name, ['class' => 'form-control textOnly input-md']) !!}
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
                                <div class="panel panel-info">
                                    <div class="panel-heading "><strong>Office Address</strong></div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6 {{$errors->has('office_division_id') ? 'has-error': ''}}">
                                                {!! Form::label('office_division_id','Division',['class'=>'text-left required-star col-md-5']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::select('office_division_id', $divisions, $getCompanyData->office_division_id, ['class' => 'form-control required imput-md', 'id' => 'office_division_id']) !!}
                                                    {!! $errors->first('office_division_id','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('office_thana_id') ? 'has-error': ''}}">
                                                {!! Form::label('office_thana_id','Police Station', ['class'=>'col-md-5 required-star text-left ']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::select('office_thana_id',$thana, $getCompanyData->office_thana_id, ['class' => 'form-control required input-md','placeholder' => 'Select district first']) !!}
                                                    {!! $errors->first('office_thana_id','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 form-group {{$errors->has('office_district_id') ? 'has-error': ''}}">
                                                {!! Form::label('office_district_id','District',['class'=>'col-md-5 required-star text-left ']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::select('office_district_id', $districts, $getCompanyData->office_district_id, ['class' => 'form-control required input-md','placeholder' => 'Select division first']) !!}
                                                    {!! $errors->first('office_district_id','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('office_post_code') ? 'has-error': ''}}">
                                                {!! Form::label('office_post_code','Post Code', ['class'=>'col-md-5 text-left']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('office_post_code', $getCompanyData->office_post_code, ['class' => 'form-control input-md alphaNumeric']) !!}
                                                    {!! $errors->first('office_post_code','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 form-group {{$errors->has('office_post_office') ? 'has-error': ''}}">
                                                {!! Form::label('office_post_office','Post Office',['class'=>'col-md-5 text-left ']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('office_post_office', $getCompanyData->office_post_office, ['class' => 'form-control input-md']) !!}
                                                    {!! $errors->first('office_post_office','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('office_telephone_no') ? 'has-error': ''}}">
                                                {!! Form::label('office_telephone_no','Telephone No.',['class'=>'col-md-5 text-left']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('office_telephone_no', $getCompanyData->office_telephone_no, ['maxlength'=>'20','class' => 'form-control input-md helpText15 phone_or_mobile']) !!}
                                                    {!! $errors->first('office_telephone_no','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 form-group {{$errors->has('office_address') ? 'has-error': ''}}">
                                                {!! Form::label('office_address','Address ',['class'=>'col-md-5 text-left ']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('office_address', $getCompanyData->office_address, ['maxlength'=>'150','class' => 'form-control input-md']) !!}
                                                    {!! $errors->first('office_address','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('office_fax_no') ? 'has-error': ''}}">
                                                {!! Form::label('office_fax_no','Fax No. ',['class'=>'col-md-5 text-left']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('office_fax_no', $getCompanyData->office_fax_no, ['class' => 'form-control input-md']) !!}
                                                    {!! $errors->first('office_fax_no','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 form-group {{$errors->has('office_mobile_no') ? 'has-error': ''}}">
                                                {!! Form::label('office_mobile_no','Mobile No. ',['class'=>'col-md-5 required-star text-left']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('office_mobile_no', $getCompanyData->office_mobile_no, ['class' => 'form-control required input-md helpText15']) !!}
                                                    {!! $errors->first('office_mobile_no','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('office_email') ? 'has-error': ''}}">
                                                {!! Form::label('office_email','Email ',['class'=>'col-md-5 required-star text-left']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('office_email', $getCompanyData->office_email, ['class' => 'form-control required email input-md']) !!}
                                                    {!! $errors->first('office_email','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{--Factory Address--}}
                                <div class="panel panel-info">
                                    <div class="panel-heading "><strong>Factory Address</strong></div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('factory_district_id') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_district_id','District',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('factory_district_id', $districts, $getCompanyData->factory_district_id, ['class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('factory_district_id','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('factory_thana_id') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_thana_id','Police Station', ['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::select('factory_thana_id',$thana, $getCompanyData->factory_thana_id, ['class' => 'form-control input-md']) !!}
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
                                                        {!! Form::text('factory_post_office', $getCompanyData->factory_post_office, ['class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('factory_post_office','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('factory_post_code') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_post_code','Post Code', ['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('factory_post_code', $getCompanyData->factory_post_code, ['class' => 'form-control input-md number alphaNumeric']) !!}
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
                                                        {!! Form::text('factory_address', $getCompanyData->factory_address, ['maxlength'=>'150','class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('factory_address','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('factory_telephone_no') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_telephone_no','Telephone No.',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('factory_telephone_no', $getCompanyData->factory_telephone_no, ['maxlength'=>'20','class' => 'form-control input-md helpText15 phone_or_mobile']) !!}
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
                                                        {!! Form::text('factory_mobile_no', $getCompanyData->factory_mobile_no, ['class' => 'form-control input-md helpText15']) !!}
                                                        {!! $errors->first('factory_mobile_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{$errors->has('factory_fax_no') ? 'has-error': ''}}">
                                                    {!! Form::label('factory_fax_no','Fax No. ',['class'=>'col-md-5 text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('factory_fax_no', $getCompanyData->factory_fax_no, ['class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('factory_fax_no','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border required-star">Please specify your desired office: </legend>
                                    <small class="text-danger">
                                        {{-- N.B.: Once you save or submit the application, the selected office cannot be changed anymore. --}}
                                        N.B.: Select your preferred <b>office division</b> and <b>factory district</b> to select your <b>desired office</b>.
                                    </small>

                                    <div id="tab" class="visaTypeTab" data-toggle="buttons">
                                        {{-- @foreach($approvalCenterList as $approval_center)
                                            <a href="#tab{{$approval_center->id}}"
                                               class="showInPreview btn btn-md btn-info"
                                               data-toggle="tab">
                                                {!! Form::radio('approval_center_id', $approval_center->id, false, ['class'=>'badgebox required']) !!}  {{ $approval_center->office_name }}
                                                <span class="badge">&check;</span>
                                            </a>
                                        @endforeach --}}
                                    </div>
                                    <div class="tab-content visaTypeTabContent" id="visaTypeTabContent" style="margin-bottom: 0px">
                                        {{-- @foreach($approvalCenterList as $key => $approval_center)
                                            <div class="tab-pane visaTypeTabPane fade in" id="tab{{$approval_center->id}}">
                                                <div class="col-sm-12">
                                                    <div>
                                                        <h4>You have selected <b>'{{$approval_center->office_name}}'</b>, {{ $approval_center->office_address }} .</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach --}}
                                    </div>
                                </fieldset>

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
                                                            {!! Form::select('project_status_id',$projectStatusList, '', ["placeholder" => "Select One", 'class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('project_status_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--2. Annual production capacity--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">2. Annual production capacity</legend>
                                            <div class="table-responsive">
                                                <table id="productionCostTbl" class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                                                    <thead class="alert alert-info">
                                                    <tr id="check">
                                                        <th valign="top" class="text-center valigh-middle">Name of
                                                            Product
                                                            <span class="required-star"></span><br/>
                                                        </th>

                                                        <th valign="top" class="text-center valigh-middle">Unit of
                                                            Quantity
                                                            <span class="required-star"></span><br/>
                                                        </th>

                                                        <th valign="top" class="text-center valigh-middle">Quantity
                                                            <span class="required-star"></span><br/>
                                                        </th>

                                                        <th valign="top" class="text-center valigh-middle">Price (USD)
                                                            <span class="required-star"></span><br/>
                                                        </th>

                                                        <th valign="top" class="text-center valigh-middle">
                                                            Sales Value in BDT (in million)
                                                            <span class="required-star"></span><br/>
                                                        </th>
                                                        <th>#</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr id="rowProCostCount" data-number="1">
                                                        <td>
                                                            {!! Form::text("apc_product_name[0]", '', ['class' => 'form-control input-md product required']) !!}

                                                        </td>

                                                        <td>{!! Form::select('apc_quantity_unit[0]',$productUnit,'',['class'=>'form-control required input-md']) !!}</td>

                                                        <td>
                                                            <input type="number" id="apc_quantity_0" name="apc_quantity[0]" class="form-control quantity1 CalculateInputByBoxNo number required">
                                                        </td>

                                                        <td>
                                                            <input type="number" id="apc_price_usd_0" name="apc_price_usd[0]" class="form-control required CalculateInputByBoxNo number quantity1">
                                                        </td>
                                                        <td>
                                                            {!! Form::number("apc_value_taka[0]",'', ['class' => 'form-control input-md number required', 'id'=>'apc_value_taka_0']) !!}
                                                        </td>
                                                        <td>
                                                            <a class="btn btn-md btn-primary addTableRows" onclick="addTableRow1('productionCostTbl', 'rowProCostCount');"><i class="fa fa-plus"></i></a>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <table>
                                                    <tr>
                                                        <td>
                                                            <span class="help-text pull-right" style="margin: 5px 0;">Exchange Rate Ref:
                                                                <a href="https://www.bangladesh-bank.org/econdata/exchangerate.php" target="_blank">Bangladesh Bank
                                                                </a>. Please Enter Today's Exchange Rate
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </fieldset>

                                        {{--3 Date of commercial operation:--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">3. Date of commercial operation</legend>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div id="date_of_arrival_div"
                                                         class="col-md-8 {{$errors->has('commercial_operation_date') ? 'has-error': ''}}">
                                                        {!! Form::label('commercial_operation_date','Date of commercial operation',['class'=>'text-left col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            <div class="commercial_operation_date input-group date">
                                                                {!! Form::text('commercial_operation_date', '', ['class' => 'form-control input-md date', 'placeholder'=>'dd-mm-yyyy']) !!}
                                                                <span class="input-group-addon"><span
                                                                            class="fa fa-calendar"></span></span>
                                                            </div>
                                                            {!! $errors->first('commercial_operation_date','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{-- 4 Sales (in 100%):--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">4. Sales (in 100%)</legend>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-4 {{$errors->has('local_sales') ? 'has-error': ''}}">
                                                        {!! Form::label('local_sales','Local ',['class'=>'col-md-4 text-left']) !!}
                                                        <div class="col-md-8">
                                                            {!! Form::number('local_sales', '', ['class' => 'form-control input-md number', 'id'=>'local_sales_per', 'min' => '0']) !!}
                                                            {!! $errors->first('local_sales','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 {{$errors->has('foreign_sales') ? 'has-error': ''}}">
                                                        {!! Form::label('foreign_sales','Foreign ',['class'=>'col-md-4 text-left']) !!}
                                                        <div class="col-md-8">
                                                            {!! Form::number('foreign_sales', '', ['class' => 'form-control input-md number', 'id'=>'foreign_sales_per', 'min' => '0']) !!}
                                                            {!! $errors->first('foreign_sales','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 {{$errors->has('total_sales') ? 'has-error': ''}}">
                                                        {!! Form::label('total_sales','Total in % ',['class'=>'col-md-4 text-left']) !!}
                                                        <div class="col-md-8">
                                                            {!! Form::number('total_sales', '', ['class' => 'form-control input-md number', 'id'=>'total_sales']) !!}
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
                                                        <th class="alert alert-info">Supporting Staff</th>
                                                        <th class="alert alert-info">Total (a)</th>
                                                        <th class="alert alert-info">Executive</th>
                                                        <th class="alert alert-info">Supporting Staff</th>
                                                        <th class="alert alert-info">Total (b)</th>
                                                        <th class="alert alert-info"> (a+b)</th>
                                                        <th class="alert alert-info">Local</th>
                                                        <th class="alert alert-info">Foreign</th>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {!! Form::text('local_male', '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number','id'=>'local_male']) !!}
                                                            {!! $errors->first('local_male','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('local_female', '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number','id'=>'local_female']) !!}
                                                            {!! $errors->first('local_female','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('local_total', '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field numberNoNegative','id'=>'local_total','readonly']) !!}
                                                            {!! $errors->first('local_total','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('foreign_male', '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number','id'=>'foreign_male']) !!}
                                                            {!! $errors->first('foreign_male','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('foreign_female', '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number','id'=>'foreign_female']) !!}
                                                            {!! $errors->first('foreign_female','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('foreign_total', '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field numberNoNegative','id'=>'foreign_total','readonly']) !!}
                                                            {!! $errors->first('foreign_total','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('manpower_total', '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number','id'=>'mp_total','readonly']) !!}
                                                            {!! $errors->first('manpower_total','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('manpower_local_ratio', '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number','id'=>'mp_ratio_local','readonly']) !!}
                                                            {!! $errors->first('manpower_local_ratio','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('manpower_foreign_ratio', '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number','id'=>'mp_ratio_foreign','readonly']) !!}
                                                            {!! $errors->first('manpower_foreign_ratio','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>
                                                    {{--<tr>--}}
                                                    {{--<td colspan="9" style="text-align: right;">--}}
                                                    {{--<small style="font-weight: bold; font-size:9px;" class="text-danger">The ratio must be below 5:1</small>--}}
                                                    {{--</td>--}}
                                                    {{--</tr>--}}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </fieldset>

                                        {{--6. Investment--}}
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
                                                                        {!! Form::number('local_land_ivst', '', ['data-rule-maxlength'=>'40','class' => 'form-control total_investment_item input-md number','id'=>'local_land_ivst',
                                                                         'onblur' => 'CalculateTotalInvestmentTk()'
                                                                        ]) !!}
                                                                        {!! $errors->first('local_land_ivst','<span class="help-block">:message</span>') !!}
                                                                    </td>
                                                                    <td>
                                                                        {!! Form::select("local_land_ivst_ccy", $currencyBDT, 114, ["placeholder" => "Select One","id"=>"local_land_ivst_ccy", "class" => "form-control input-md usd-def"]) !!}
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
                                                                        {!! Form::number('local_building_ivst', '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md total_investment_item number','id'=>'local_building_ivst',
                                                                         'onblur' => 'CalculateTotalInvestmentTk()']) !!}
                                                                        {!! $errors->first('local_building_ivst','<span class="help-block">:message</span>') !!}
                                                                    </td>
                                                                    <td>
                                                                        {!! Form::select("local_building_ivst_ccy", $currencyBDT, 114, ["placeholder" => "Select One","id"=>"local_building_ivst_ccy", "class" => "form-control input-md usd-def"]) !!}
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
                                                                        {!! Form::number('local_machinery_ivst', '', ['data-rule-maxlength'=>'40','class' => 'form-control required input-md number total_investment_item','id'=>'local_machinery_ivst',
                                                                        'onblur' => 'CalculateTotalInvestmentTk()']) !!}
                                                                        {!! $errors->first('local_machinery_ivst','<span class="help-block">:message</span>') !!}
                                                                    </td>
                                                                    <td>
                                                                        {!! Form::select("local_machinery_ivst_ccy", $currencyBDT, 114, ["placeholder" => "Select One","id"=>"local_machinery_ivst_ccy", "class" => "form-control input-md usd-def"]) !!}
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
                                                                        {!! Form::number('local_others_ivst', '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md number total_investment_item','id'=>'local_others_ivst',
                                                                        'onblur' => 'CalculateTotalInvestmentTk()']) !!}
                                                                        {!! $errors->first('local_others_ivst','<span class="help-block">:message</span>') !!}
                                                                    </td>
                                                                    <td>
                                                                        {!! Form::select("local_others_ivst_ccy", $currencyBDT, 114, ["placeholder" => "Select One","id"=>"local_others_ivst_ccy", "class" => "form-control input-md usd-def"]) !!}
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
                                                                        {!! Form::number('local_wc_ivst', '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md number total_investment_item','id'=>'local_wc_ivst',
                                                                        'onblur' => 'CalculateTotalInvestmentTk()']) !!}
                                                                        {!! $errors->first('local_wc_ivst','<span class="help-block">:message</span>') !!}
                                                                    </td>
                                                                    <td>
                                                                        {!! Form::select("local_wc_ivst_ccy", $currencyBDT, 114, ["placeholder" => "Select One","id"=>"local_wc_ivst_ccy", "class" => "form-control input-md usd-def"]) !!}
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
                                                            {!! Form::number('total_fixed_ivst_million', '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md numberNoNegative total_fixed_ivst_million required','id'=>'total_fixed_ivst_million','readonly']) !!}
                                                            {!! $errors->first('total_fixed_ivst_million','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            <div style="float: left; display: flex;">
                                                                <div style="flex-grow: 1;" id="project_profile_label">Project&nbsp;profile:&nbsp;&nbsp;</div>
                                                                <div style="flex-shrink: 0;">
                                                                    <input type="file" name="project_profile_attachment" id="project_profile_id" onchange="projectProfileDocument(this.id)" style="border: none;">
                                                                    <small class="text-danger">N.B.: Maximum PDF file upload size 2MB</small>
                                                                </div>
                                                            </div>
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
                                                            {!! Form::number('total_fixed_ivst', '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md numberNoNegative total_invt_bdt required','id'=>'total_invt_bdt','readonly']) !!}
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
                                                            {!! Form::number('usd_exchange_rate', '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md numberNoNegative','id'=>'usd_exchange_rate']) !!}
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
                                                                        {!! Form::text('total_fee', '', ['class' => 'form-control input-md number', 'id'=>'total_fee', 'readonly']) !!}
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
                                                                    {!! Form::number('finance_src_loc_equity_1', '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md number','id'=>'finance_src_loc_equity_1','onblur'=>"calculateSourceOfFinance(this.id)"]) !!}
                                                                    {!! $errors->first('finance_src_loc_equity_1','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                            </tr>
                                                            <tr id="finance_src_foreign_equity_1_row_id">
                                                                <td width="38%">Foreign Equity (Million)</td>
                                                                <td>
                                                                    {!! Form::number('finance_src_foreign_equity_1', '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md number','id'=>'finance_src_foreign_equity_1','onblur'=>"calculateSourceOfFinance(this.id)"]) !!}
                                                                    {!! $errors->first('finance_src_foreign_equity_1','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Total Equity (Million)</th>
                                                                <td>
                                                                    {!! Form::number('finance_src_loc_total_equity_1', '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md number readOnly','id'=>'finance_src_loc_total_equity_1']) !!}
                                                                    {!! $errors->first('finance_src_loc_total_equity_1','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>Local Loan (Million)</td>
                                                                <td>
                                                                    {!! Form::number('finance_src_loc_loan_1', '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md number','id'=>'finance_src_loc_loan_1','onblur'=>"calculateSourceOfFinance(this.id)"]) !!}
                                                                    {!! $errors->first('finance_src_loc_loan_1','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Foreign Loan (Million)</td>
                                                                <td>
                                                                    {!! Form::number('finance_src_foreign_loan_1', '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md number ','id'=>'finance_src_foreign_loan_1','onblur'=>"calculateSourceOfFinance(this.id)"]) !!}
                                                                    {!! $errors->first('finance_src_foreign_loan_1','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Total Loan (Million)</th>
                                                                <td>
                                                                    {!! Form::number('finance_src_total_loan', '', ['id'=>'finance_src_total_loan','class' => 'form-control input-md readOnly numberNoNegative', 'data-rule-maxlength'=>'240']) !!}
                                                                    {!! $errors->first('finance_src_total_loan','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <th>Total Financing Million (Equity  + Loan )</th>
                                                                <td>
                                                                    {!! Form::number('finance_src_loc_total_financing_m', '', ['id'=>'finance_src_loc_total_financing_m','class' => 'form-control input-md readOnly numberNoNegative', 'data-rule-maxlength'=>'240']) !!}
                                                                    {!! $errors->first('finance_src_loc_total_financing_m','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <th>Total Financing BDT (Equity  + Loan )</th>
                                                                <td colspan="3">
                                                                    {!! Form::number('finance_src_loc_total_financing_1', '', ['data-rule-maxlength'=>'40','class' => 'form-control input-md numberNoNegative readOnly','id'=>'finance_src_loc_total_financing_1']) !!}
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
                                                                <td colspan="4">
                                                                    <i class="fa fa-question-circle"
                                                                       data-toggle="tooltip" data-placement="top"
                                                                       title="From the above information, the values of Local Equity (Million)” and “Local Loan (Million)” will go into the
                                                                       Equity Amount and Loan Amount respectively for
                                                                    Bangladesh. The summation of the Equity Amount and
                                                                    Loan Amount of other countries will be equal to
                                                                    the values of Foreign Equity (Million) and
                                                                    Foreign Loan (Million) respectively.">
                                                                    </i>
                                                                    Country wise source of finance (Million BDT)
                                                                </td>
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
                                                            <tr id="financeTableIdRow" data-number="1">
                                                                <td>
                                                                    {!!Form::select('country_id[0]', $countries, 18, ['class' => 'form-control required'])!!}
                                                                    {!! $errors->first('country_id','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                                <td>
                                                                    {!! Form::text('equity_amount[0]', '', ['class' => 'form-control input-md equity_amount']) !!}
                                                                    {!! $errors->first('equity_amount','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                                <td>
                                                                    {!! Form::text('loan_amount[0]', '', ['class' => 'form-control input-md loan_amount']) !!}
                                                                    {!! $errors->first('loan_amount','<span class="help-block">:message</span>') !!}
                                                                </td>
                                                                <td>
                                                                    <a class="btn btn-sm btn-primary addTableRows"
                                                                       title="Add more"
                                                                       onclick="addTableRow1('financeTableId', 'financeTableIdRow');">
                                                                        <i class="fa fa-plus"></i></a>
                                                                </td>
                                                            </tr>
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
                                                            <input type="checkbox" class="myCheckBox"
                                                                   name="public_land">Land
                                                        </label>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox"
                                                                   name="public_electricity">Electricity
                                                        </label>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox" name="public_gas">Gas
                                                        </label>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox"
                                                                   name="public_telephone">Telephone
                                                        </label>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox"
                                                                   name="public_road">Road
                                                        </label>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox"
                                                                   name="public_water">Water
                                                        </label>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" class="myCheckBox"
                                                                   name="public_drainage">Drainage
                                                        </label>
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" id="public_others"
                                                                   name="public_others"
                                                                   class="other_utility myCheckBox">Others
                                                        </label>
                                                    </div>
                                                    <div class="col-md-12" hidden style="margin-top: 5px;"
                                                         id="public_others_field_div">
                                                        {!! Form::text('public_others_field', '', ['placeholder'=>'Specify others', 'class' => 'form-control input-md', 'id' => 'public_others_field']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--9. Trade licence details--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">9. Trade licence details</legend>
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('trade_licence_num') ? 'has-error': ''}}">
                                                        {!! Form::label('trade_licence_num','Trade Licence Number',['class'=>'text-left required-star col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('trade_licence_num', '', ['class' => 'form-control required input-md']) !!}
                                                            {!! $errors->first('trade_licence_num','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group {{$errors->has('trade_licence_issuing_authority') ? 'has-error': ''}}">
                                                        {!! Form::label('trade_licence_issuing_authority','Issuing Authority',['class'=>'col-md-5 text-left ']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('trade_licence_issuing_authority', '', ['class' => 'form-control input-md']) !!}
                                                            {!! $errors->first('trade_licence_issuing_authority','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--10. Tin--}}
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">10. Tin</legend>
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6 {{$errors->has('tin_number') ? 'has-error': ''}}">
                                                        {!! Form::label('tin_number','Tin Number',['class'=>'text-left required-star col-md-5']) !!}
                                                        <div class="col-md-7">
                                                            {!! Form::text('tin_number', '', ['class' => 'form-control required input-md']) !!}
                                                            {!! $errors->first('tin_number','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        {{--11. Description of machinery and equipment--}}
                                        <fieldset class="scheduler-border hidden" id="machinery_equipment">
                                            <legend class="scheduler-border">11. Description of machinery and
                                                equipment
                                            </legend>
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
                                                            {!! Form::text('machinery_local_qty', '', ['class' => 'form-control input-md','id' => 'machinery_local_qty','onkeyup' => 'totalMachineryEquipmentQty()']) !!}
                                                            {!! $errors->first('machinery_local_qty','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('machinery_local_price_bdt', '', ['class' => 'form-control input-md','id' => 'machinery_local_price_bdt','onkeyup'=>"totalMachineryEquipmentPrice()"]) !!}
                                                            {!! $errors->first('machinery_local_price_bdt','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Imported</td>
                                                        <td>
                                                            {!! Form::text('imported_qty', '', ['class' => 'form-control input-md', 'id'=>'imported_qty', 'onkeyup' => 'totalMachineryEquipmentQty()']) !!}
                                                            {!! $errors->first('imported_qty','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('imported_qty_price_bdt', '', ['class' => 'form-control input-md','id' => 'imported_qty_price_bdt','onkeyup'=>"totalMachineryEquipmentPrice()"]) !!}
                                                            {!! $errors->first('imported_qty_price_bdt','<span class="help-block">:message</span>') !!}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total</td>
                                                        <td>
                                                            {!! Form::text('total_machinery_qty', '', ['class' => 'form-control input-md','id' => 'total_machinery_qty']) !!}
                                                        </td>
                                                        <td>
                                                            {!! Form::text('total_machinery_price', '', ['class' => 'form-control input-md','id' => 'total_machinery_price']) !!}
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </fieldset>

                                        <fieldset class="scheduler-border hidden" id="packing_materials">
                                            <legend class="scheduler-border">12. Description of raw &amp; packing
                                                materials
                                            </legend>
                                            <div class="table-responsive">
                                                <table class="table table-bordered dt-responsive" cellspacing="0"
                                                       width="100%">
                                                    <tbody>
                                                    <tr>
                                                        <th class="col-md-2">Locally</th>
                                                        <td class="col-md-10">
                                                            {!! Form::textarea('local_description', '', ['class' => 'form-control bigInputField input-md maxTextCountDown',
                                                        'id' => 'local_description', 'size'=>'5x2','data-charcount-maxlength'=>'1000']) !!}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="col-md-2">Imported</th>
                                                        <td class="col-md-10">
                                                            {!! Form::textarea('imported_description', '', ['class' => 'form-control bigInputField input-md maxTextCountDown',
                                                        'id' => 'imported_description', 'size'=>'5x2','data-charcount-maxlength'=>'1000']) !!}
                                                        </td>
                                                    </tr>
                                                    </tbody>

                                                </table>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>

                            </fieldset>

                            <h3 class="stepHeader">List of Directors</h3>
                            <fieldset>
                                <div class="panel panel-info">
                                    <div class="panel-heading"><strong>List of Directors and high authorities</strong></div>
                                    <div class="panel-body">
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">Information of (Chairman/ Managing
                                                Director/ Or Equivalent):
                                            </legend>
                                            <div class="row">
                                                <div class="form-group col-md-6 {{$errors->has('g_full_name') ? 'has-error': ''}}">
                                                    {!! Form::label('g_full_name','Full Name',['class'=>'col-md-5 required-star text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('g_full_name', '', ['class' => 'form-control required input-md']) !!}
                                                        {!! $errors->first('g_full_name','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6 {{$errors->has('g_designation') ? 'has-error': ''}}">
                                                    {!! Form::label('g_designation','Position/ Designation',['class'=>'col-md-5 required-star text-left']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('g_designation', '', ['class' => 'form-control required input-md']) !!}
                                                        {!! $errors->first('g_designation','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 {{$errors->has('g_signature') ? 'has-error': ''}}">
                                                    <div class="form-group">
                                                        {!! Form::label('g_signature','Signature', ['class'=>'text-left col-md-5 required-star']) !!}
                                                        <span id="investorSignatureUploadError"
                                                              class="text-danger"></span>
                                                        <div class="col-md-7">

                                                            <div id="investorSignatureViewerDiv">
                                                                <img class="img-thumbnail img-signature" id="investorSignatureViewer"
                                                                     src="{{ url('assets/images/photo_default.png')  }}"
                                                                     alt="Investor Signature">
                                                                <input type="hidden" name="investor_signature_base64"
                                                                       id="investor_signature_base64">
                                                                <input type="hidden" name="investor_signature_name"
                                                                       id="investor_signature_name">
                                                            </div>

                                                            <div class="form-group">
                                                                <span id="investorSignatureUploadError"
                                                                      class="text-danger"></span>

                                                                <input type="file"
                                                                       class="custom-file-input required"
                                                                       onchange="readURLUser(this);"
                                                                       id="investorSignatureUploadBtn"
                                                                       name="investorSignatureUploadBtn"
                                                                       data-type="user"
                                                                       data-ref="{{Encryption::encodeId(Auth::user()->id)}}">

                                                                <a id="investorSignatureResetBtn"
                                                                   class="btn btn-sm btn-warning resetIt hidden"
                                                                   onclick="resetImage(this);"
                                                                   data-src="{{ url('assets/images/photo_default.png') }}"><i
                                                                            class="fa fa-refresh"></i> Reset</a>

                                                                <span class="text-success"
                                                                      style="font-size: 9px; font-weight: bold; display: block;">
                                                                [File Format: *.jpg/ .jpeg | Width 300PX, Height 80PX]
                                                            </span>
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
                                                <div class="pull-left" style="padding:5px 5px"><strong>List of
                                                        directors</strong></div>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="panel-body">
                                                <h4 class="text-center fz16 text-danger">To add the directors. Please,
                                                    click the "Save as Draft" button and try again.</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <h3 class="stepHeader">List of Machineries</h3>
                            <fieldset>
                                <div class="panel panel-info">
                                    <div class="panel-heading"><strong>List of Machineries</strong></div>
                                    <div class="panel-body">
                                        {{--List of Machinery to be imported--}}
                                        <div class="panel panel-info">
                                            <div class="panel-heading">
                                                <div class="pull-left" style="padding:5px 5px"><strong>List of machinery
                                                        to be imported</strong></div>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="panel-body">
                                                <div class="table-responsive">
                                                    <table id="listOfMachineryImported"
                                                           class="table table-striped dt-responsive" cellspacing="0"
                                                           width="100%">
                                                        <thead class="alert alert-info">
                                                        <tr>
                                                            <th valign="top" class="text-center" width="50%">Name of
                                                                machineries
                                                                <span class="required-star"></span><br/>
                                                            </th>

                                                            <th valign="top" class="text-center">Quantity
                                                                <span class="required-star"></span><br/>
                                                            </th>

                                                            <th valign="top" class="text-center">Unit prices TK
                                                                <span class="required-star"></span><br/>
                                                            </th>

                                                            <th colspan="2" valign="top" class="text-center">Total value
                                                                (Million) TK
                                                                <span class="required-star"></span><br/>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>

                                                        <?php $inc = 0; ?>

                                                        <tr id="rowListOfMachineryImported{{$inc}}">
                                                            <td>
                                                                {!! Form::text("l_machinery_imported_name[$inc]", '', ['class' => 'form-control input-md required']) !!}
                                                            </td>

                                                            <td>
                                                                {!! Form::number("l_machinery_imported_qty[$inc]", '', ['class' => 'form-control input-md required number']) !!}
                                                            </td>

                                                            <td>
                                                                {!! Form::number("l_machinery_imported_unit_price[$inc]", '', ['class' => 'form-control input-md required number']) !!}
                                                            </td>

                                                            <td>
                                                                {!! Form::number("l_machinery_imported_total_value[$inc]", '', ['class' => 'form-control input-md required machinery_imported_total_value numberNoNegative', 'onkeyup' => "calculateListOfMachineryTotal('machinery_imported_total_value', 'machinery_imported_total_amount')"]) !!}
                                                            </td>

                                                            <td>
                                                                <?php if ($inc == 0) { ?>
                                                                <a class="btn btn-md btn-primary addTableRows"
                                                                   onclick="addTableRow1('listOfMachineryImported', 'rowListOfMachineryImported0');"><i
                                                                            class="fa fa-plus"></i></a>
                                                                <?php } else { ?>
                                                                <a href="javascript:void(0);"
                                                                   class="btn btn-md btn-danger removeRow"
                                                                   onclick="removeTableRow('listOfMachineryImported','rowListOfMachineryImported{{$inc}}');">
                                                                    <i class="fa fa-times" aria-hidden="true"></i></a>
                                                                <?php } ?>

                                                            </td>
                                                        </tr>
                                                        <?php $inc++; ?>
                                                        </tbody>
                                                        <tfoot>
                                                        <tr>
                                                            <th colspan="3" style="text-align: right;">Total :</th>
                                                            <th colspan="2" style="text-align: center;">
                                                                {!! Form::text('machinery_imported_total_amount', '',['class' => 'form-control input-md numberNoNegative', 'id' => 'machinery_imported_total_amount','readonly']) !!}
                                                            </th>
                                                        </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>


                                        {{--List of machinery locally purchase/ procure--}}
                                        <div class="panel panel-info">
                                            <div class="panel-heading">
                                                <div class="pull-left" style="padding:5px 5px"><strong>List of machinery
                                                        locally purchase/
                                                        procure</strong></div>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="panel-body">
                                                <div class="table-responsive">
                                                    <table id="listOfMachineryLocal"
                                                           class="table table-striped dt-responsive" cellspacing="0"
                                                           width="100%">
                                                        <thead class="alert alert-info">
                                                        <tr>
                                                            <th valign="top" class="text-center" width="50%">Name of
                                                                machineries
                                                                <span class="required-star"></span><br/>
                                                            </th>

                                                            <th valign="top" class="text-center">Quantity
                                                                <span class="required-star"></span><br/>
                                                            </th>

                                                            <th valign="top" class="text-center">Unit prices TK
                                                                <span class="required-star"></span><br/>
                                                            </th>

                                                            <th colspan="2" valign="top" class="text-center">Total value
                                                                (Million) TK
                                                                <span class="required-star"></span><br/>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>

                                                        <?php $inc = 0; ?>

                                                        <tr id="rowlistOfMachineryLocal{{$inc}}">
                                                            <td>
                                                                {!! Form::text("l_machinery_local_name[$inc]", '', ['class' => 'form-control input-md required']) !!}
                                                            </td>

                                                            <td>
                                                                {!! Form::number("l_machinery_local_qty[$inc]", '', ['class' => 'form-control input-md required number']) !!}
                                                            </td>

                                                            <td>
                                                                {!! Form::number("l_machinery_local_unit_price[$inc]", '', ['class' => 'form-control input-md required number']) !!}
                                                            </td>

                                                            <td>
                                                                {!! Form::number("l_machinery_local_total_value[$inc]", '', ['class' => 'form-control input-md required machinery_local_total_value numberNoNegative', 'onkeyup' => "calculateListOfMachineryTotal('machinery_local_total_value', 'machinery_local_total_amount')"]) !!}
                                                            </td>

                                                            <td>
                                                                <?php if ($inc == 0) { ?>
                                                                <a class="btn btn-md btn-primary addTableRows"
                                                                   onclick="addTableRow1('listOfMachineryLocal', 'rowlistOfMachineryLocal0');"><i
                                                                            class="fa fa-plus"></i></a>
                                                                <?php } else { ?>
                                                                <a href="javascript:void(0);"
                                                                   class="btn btn-md btn-danger removeRow"
                                                                   onclick="removeTableRow('listOfMachineryLocal','rowlistOfMachineryLocal{{$inc}}');">
                                                                    <i class="fa fa-times" aria-hidden="true"></i></a>
                                                                <?php } ?>

                                                            </td>
                                                        </tr>
                                                        <?php $inc++; ?>
                                                        </tbody>
                                                        <tfoot>
                                                        <tr>
                                                            <th colspan="3" style="text-align: right;">Total :</th>
                                                            <th colspan="2" style="text-align: center;">
                                                                {!! Form::text('machinery_local_total_amount', '',['class' => 'form-control input-md numberNoNegative', 'id' => 'machinery_local_total_amount','readonly']) !!}
                                                            </th>
                                                        </tr>
                                                        </tfoot>
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

                            <h3 class="stepHeader">Declaration</h3>
                            <fieldset>
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
                                                        {!! Form::text('sfp_pay_amount', $payment_config->amount, ['class' => 'form-control input-md', 'readonly', 'id'=>'pay_amount']) !!}
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

                            </button>
                        </div>
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
                        <th colspan="3" scope="colgroup">Fees break down in BDT</th>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.min.js"></script>
<script src="{{ asset("assets/plugins/facedetection.js") }}" type="text/javascript"></script>
<script src="{{ asset("assets/scripts/attachment.js") }}"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.min.css">
<script>
    function resetImage(input) {
        var imgSrc = input.getAttribute('data-src');
        var html = '<img src="' + imgSrc + '" class="img-thumbnail" alt="Profile Picture" id="investorSignatureViewer" alt="investorSignatureViewer"/>';
        $('#investor_signature_base64').val('');
        $('#investorSignatureViewerDiv').prepend(html);
        $("#investorSignatureUploadBtn").removeClass('hidden');
        $("#cropImageBtn").remove();
        $('div.croppie-container').remove();
        $('#investorSignatureUploadBtn').val('');
        $('#investorSignatureResetBtn').addClass('hidden');
    }

    function cropImageAndSetValue(fieldName) {
        uploadCrop.croppie('result', {
            type: 'canvas',
            size: 'original'
        }).then(function (resp) {
            $('#' + fieldName).val(resp);
            toastr.success('Image Cropped & Set');
        });
    }

    function readURLUser(input) {
        if (input.files && input.files[0]) {
            $("#investorSignatureUploadError").html('');

            // Validate Image type
            var mime_type = input.files[0].type;
            if (!(mime_type == 'image/jpeg' || mime_type == 'image/jpg' || mime_type == 'image/png')) {
                $("#investorSignatureUploadError").html("Image format is not valid. Only PNG or JPEG or JPG type images are allowed.");
                return false;
            }

            var reader = new FileReader();
            reader.onload = function (e) {
                $('#investorSignatureViewer').attr('src', e.target.result);
                $('#investor_signature_base64').val(e.target.result);
                $("#investorSignatureUploadBtn").addClass('hidden').after("<img id='waitBtn' style='height: 40px;width: 120px' src='/assets/images/loadWait.gif'>");
                $('#update_info_btn').prop('disabled', true); // Submit or save btn
            };
            reader.readAsDataURL(input.files[0]);

            uploadCrop = $('#investorSignatureViewer');
            setTimeout(function () {
                $('#investorSignatureViewer').faceDetection({
                    complete: function (faces) {
                        // $('.panel-heading').html('Face is detected');
                        uploadCrop.croppie({
                            viewport: {
                                width: 300,
                                height: 80,
                                type: 'square'
                            },
                            boundary: {
                                width: 310,
                                height: 90
                            }

                            // enableResize: true,
                        });
                        toastr.warning("Please click 'Save Image' after cropping");
                        $('#investorSignatureResetBtn').removeClass('hidden');
                        $('#investorSignatureResetBtn').after(' <button type="button" id="cropImageBtn" class="btn btn-success btn-sm" onclick="cropImageAndSetValue(\'investor_signature_base64\')">Save Image</button>');
                        $('#waitBtn').remove();
                        $('#investor_signature_name').val(input.files[0].name);
                        $('#update_info_btn').prop('disabled', false); // Submit or save btn
                    }
                });

            }, 3000);

            // $("#image_name").val(data);
            // $('.ajax-file-upload-statusbar').remove();
        }
    }
</script>


<script type="text/javascript">

    // New start
    function imageDisplay(input, imageView, requiredSize = 0) {
        if (input.files && input.files[0]) {
            var mime_type = input.files[0].type;
            if (!(mime_type == 'image/jpeg' || mime_type == 'image/jpg' || mime_type == 'image/png')) {
//                alert("Image format is not valid. Please upload in jpg,jpeg or png format");
                swal({
                    type: 'error',
                    title: 'Oops...',
                    text: 'Image format is not valid. Please upload in jpg,jpeg or png format',
                });
                $('#' + imageView).attr('src', '{{url('assets/images/photo_default.png')}}');
                $(input).val('').addClass('btn-danger').removeClass('btn-primary');
                return false;
            } else {
                $(input).addClass('btn-primary').removeClass('btn-danger');
            }
            var reader = new FileReader();
            reader.onload = function (e) {
                //$('#'+imageView).attr('src', e.target.result);

                // check height-width
                // in funciton calling third parameter should be (requiredWidth x requiredHeight)
                if (requiredSize != 0) {
                    var size = requiredSize.split('x');
                    var requiredwidth = parseInt(size[0]);
                    var requiredheight = parseInt(size[1]);
                    if (requiredheight != 0 && requiredwidth != 0) {
                        var image = new Image();
                        image.src = e.target.result;
                        image.onload = function () {
                            if (requiredheight != this.height || requiredwidth != this.width) {
                                //alert("Image size must be " + requiredSize);
                                swal({
                                    type: 'error',
                                    title: 'Oops...',
                                    text: 'Image size must be ' + requiredSize + ' PX',
                                });
                                $('#' + imageView).attr('src', '{{url('assets/images/photo_default.png')}}');
                                $(input).val('').addClass('btn-danger').removeClass('btn-primary');
                                return false;
                            } else {
                                $('#' + imageView).attr('src', e.target.result);
                            }
                        }
                    } else {
                        swal({
                            type: 'error',
                            title: 'Oops...',
                            text: 'Error in image required size!',
                        });
                        //alert('Error in image required size!');
                    }
                }
                // if image height and width is not defined , means any size will be uploaded
                else {
                    $('#' + imageView).attr('src', e.target.result);
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    //7. Source of Finance Foreign Equity
    function calculateSourceOfFinance(event) {
        var local_equity = $('#finance_src_loc_equity_1').val() ? parseFloat($('#finance_src_loc_equity_1').val()) : 0;
        var foreign_equity = $('#finance_src_foreign_equity_1').val() ? parseFloat($('#finance_src_foreign_equity_1').val()) : 0;
        var total_equity = (local_equity + foreign_equity).toFixed(5);

        $('#finance_src_loc_total_equity_1').val(total_equity);

        // $('#finance_src_foreign_equity_2').val((foreign_equity*100/total_equity).toFixed(2));

        var local_loan = $('#finance_src_loc_loan_1').val() ? parseFloat($('#finance_src_loc_loan_1').val()) : 0;
        var foreign_loan = $('#finance_src_foreign_loan_1').val() ? parseFloat($('#finance_src_foreign_loan_1').val()) : 0;
        var total_loan = (local_loan + foreign_loan).toFixed(5);

        $('#finance_src_total_loan').val(total_loan);
        // $('#finance_src_loc_loan_2').val((local_loan*100/total_loan).toFixed(2));
        // $('#finance_src_foreign_loan_2').val((foreign_loan*100/total_loan).toFixed(2));


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
    function addTableRow1(tableID, template_row_id) {
        // Copy the template row (first row) of table and reset the ID and Styling
        var new_row = document.getElementById(template_row_id).cloneNode(true);
        new_row.id = "";
        new_row.style.display = "";

        //Get the total now, and last row number of table
        var current_total_row = $('#' + tableID).find('tbody tr').length;
        var final_total_row = current_total_row + 1;

        // check row count to click add more button start
        var row_limit = "<?php echo $add_more_validation->value?>";
        // temporary code
        if (tableID == 'listOfDirectors') {
            row_limit--;
        }
        if (final_total_row > row_limit && tableID != 'productionCostTbl' && tableID != 'financeTableId') {
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


    function calculateListOfMachineryTotal(className, totalShowFieldId) {
        var total_machinery = 0.00;
        $("." + className).each(function () {
            total_machinery = total_machinery + (this.value ? parseFloat(this.value) : 0.00);
        });
        $("#" + totalShowFieldId).val(total_machinery.toFixed(3));
    }

    // Remove Table row script
    function removeTableRow(tableID, removeNum) {
        $('#' + tableID).find('#' + removeNum).remove();
        const current_total_row = $('#' + tableID).find('tbody tr').length;
        if (current_total_row <= 3) {
            document.getElementById(tableID).deleteTFoot();
        }

        //console.log(current_total_row);
        calculateListOfMachineryTotal('machinery_imported_total_value', 'machinery_imported_total_amount');
        calculateListOfMachineryTotal('machinery_local_total_value', 'machinery_local_total_amount');
    }

    $(document).ready(function () {
        //Step js .....
        var form = $("#BidaRegistrationForm").show();
        //form.find('#save_as_draft').css('display','none');
        form.find('#submitForm').css('display', 'none');
        form.find('.actions').css('top', '-15px !important');
        form.steps({
            headerTag: "h3",
            bodyTag: "fieldset",
            transitionEffect: "slideLeft",
            onStepChanging: function (event, currentIndex, newIndex) {
                if (newIndex == 1) {
                    var desired_office = $('input[name=approval_center_id]:checked').length;
                    if (desired_office != 1) {
                        //$(".visaTypeTab").css({"border": "1px solid red"});
                        alert('Sorry! Please specify your desired office.');
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

                    if ($("#is_valid_bbs_code").val() == 0) {
                        alert('Business Sector (BBS Class Code) is required. Please enter or select from the above list.')
                        return false;
                    }

                }

                if (newIndex == 2) {
                    alert('To add the directors. Please, click the "Save as Draft" button and try again.');
                    return false;
                }

                if (newIndex == 3) {

                    var local_machinery_ivst = parseFloat($("#local_machinery_ivst").val()).toFixed(3);
                    var machinery_imported_total_amount = $("#machinery_imported_total_amount").val();
                    var machinery_local_total_amount = $("#machinery_local_total_amount").val();
                    var total_machinery = (parseFloat(machinery_imported_total_amount) + parseFloat(machinery_local_total_amount)).toFixed(3);
                    if (local_machinery_ivst != total_machinery) {
                        alert('"Machinery & Equipment investment (Section: Application info, No: 6)" value must be equal to the sum of "the list of machinery to be imported" and "the list of machinery locally purchase/ procure".');
                        return false;
                    }

                }

                // Always allow previous action even if the current form is not valid!
                if (currentIndex > newIndex) {
                    return true;
                }
                // Forbid next action on "Warning" step if the user is to young
                if (newIndex === 3 && Number($("#age-2").val()) < 18) {
                    return false;
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
                popupWindow = window.open('<?php echo URL::to('/bida-registration/preview'); ?>', 'Sample', '');
            } else {
                return false;
            }
        });


    });
    // New end
    //--------Step Form init+validation End----------//
    var popupWindow = null;
    $('.finish').on('click', function (e) {
        if (form.valid()) {
            $('body').css({"display": "none"});
            popupWindow = window.open('<?php echo URL::to('/bida-registration/preview'); ?>', 'Sample', '');
        } else {
            form.validate().settings.ignore = ":disabled,:hidden";
            return form.valid();
        }
    });

    function CalculateTotalInvestmentTk() {
        var land = parseFloat(document.getElementById('local_land_ivst').value);
        var building = parseFloat(document.getElementById('local_building_ivst').value);
        var machine = parseFloat(document.getElementById('local_machinery_ivst').value);
        var other = parseFloat(document.getElementById('local_others_ivst').value);
        var wcCapital = parseFloat(document.getElementById('local_wc_ivst').value);
        var totalInvest = ((isNaN(land) ? 0 : land) + (isNaN(building) ? 0 : building) + (isNaN(machine) ? 0 : machine) + (isNaN(other) ? 0 : other) + (isNaN(wcCapital) ? 0 : wcCapital)).toFixed(5);
        var totalTk = (totalInvest * 1000000).toFixed(2);
        document.getElementById('total_fixed_ivst_million').value = totalInvest;

        let project_profile_element = document.getElementById('project_profile_id');
        let pp_label_element = document.getElementById('project_profile_label');

        if (totalInvest >= 100) {
            project_profile_element.classList.add('required');
            pp_label_element.classList.add('required-star');

        } else {
            project_profile_element.classList.remove('required');
            pp_label_element.classList.remove('required-star');
        }

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


    // function CategoryWiseDocLoad(org_status_id) {

    //     if (org_status_id == 3) {
    //         $("#machinery_equipment").removeClass('hidden');
    //         $("#packing_materials").removeClass('hidden');
    //     } else {
    //         $("#machinery_equipment").addClass('hidden');
    //         $("#packing_materials").addClass('hidden');
    //     }


    //     var attachment_key = "br_";
    //     if (org_status_id == 3) {
    //         attachment_key += "local";
    //     } else if (org_status_id == 2) {
    //         attachment_key += "foreign";
    //     } else {
    //         attachment_key += "joint_venture";
    //     }

    //     if (org_status_id != 0 && org_status_id != '') {
    //         var _token = $('input[name="_token"]').val();
    //         var app_id = $("#app_id").val();
    //         var viewMode = $("#viewMode").val();

    //         $.ajax({
    //             type: "POST",
    //             url: '/bida-registration/getDocList',
    //             dataType: "json",
    //             data: {_token: _token, attachment_key: attachment_key, app_id: app_id, viewMode: viewMode},
    //             success: function (result) {
    //                 if (result.html != undefined) {
    //                     $('#docListDiv').html(result.html);
    //                 }
    //             },
    //             error: function (jqXHR, textStatus, errorThrown) {
    //                 //console.log(errorThrown);
    //                 alert('Unknown error occured. Please, try again after reload');
    //             },
    //         });
    //     } else {
    //         //console.log('Unknown Visa Type');
    //     }
    // }
    function CategoryWiseDocLoad(org_status_id) {
        const ownership_status_id = document.getElementById('ownership_status_id').value;

        if (org_status_id == 3) {
            $("#machinery_equipment, #packing_materials").removeClass('hidden');
        } else {
            $("#machinery_equipment, #packing_materials").addClass('hidden');
        }
  
        if (org_status_id && org_status_id !== 0) {
            const _token = $('input[name="_token"]').val();
            const app_id = $("#app_id").val();
            const viewMode = $("#viewMode").val();
            const attachment_key = generateAttachmentKey(org_status_id, ownership_status_id, 'br');

            $.ajax({
                type: "POST",
                url: '/bida-registration/getDocList',
                dataType: "json",
                data: {_token: _token, attachment_key: attachment_key, app_id: app_id, viewMode: viewMode},
                success: function (result) {
                    if (result?.html) {
                    $('#docListDiv').html(result.html);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error(errorThrown);
                    alert('An error occurred. Please try again after reloading the page.');
                },
            });
        } else {
            console.warn('Unknown organization status ID');
        }
    }

    // function generateAttachmentKey(org_status_id, ownership_status_id) {
    //     let organization_key = "";
    //     let ownership_key = "";

    //     switch (parseInt(org_status_id)) {
    //         case 1:
    //             organization_key = "join";
    //             break;
    //         case 2:
    //             organization_key = "fore";
    //             break;
    //         case 3:
    //             organization_key = "loca";
    //             break;
    //         default:
    //             console.warn('Unknown organization status ID');
    //     }

    //     switch (parseInt(ownership_status_id)) {
    //         case 1:
    //             ownership_key = "comp";
    //             break;
    //         case 2:
    //             ownership_key = "part";
    //             break;
    //         case 3:
    //             ownership_key = "prop";
    //             break;
    //         default:
    //             console.warn('Unknown ownership status ID');
    //     }

    //     return "br_" + ownership_key + "_" + organization_key;
    // }

    $(document).ready(function () {

        $("#organization_status_id").trigger('change');

        $('#BidaRegistrationForm').validate({
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
//            $("#ceo_state").addClass('required');
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


//   return false;

        $('.readOnlyCl input,.readOnlyCl select,.readOnlyCl textarea,.readOnly').not("#business_class_code, #major_activities, #country_of_origin_id, #organization_status_id, #project_name").attr('readonly', true);
        //$('.readOnlyCl :radio:not(:checked)').attr('disabled', true);
        $(".readOnlyCl select").each(function () {
            var id = $(this).attr('id');
            //console.log(id);
            if (id != 'country_of_origin_id' && id != 'organization_status_id') {
                $("#" + id + " option:not(:selected)").prop('disabled', true);
            }

//            if(id != 'country_of_origin_id') {
//                $("#"+id+" option:not(:selected)").prop('disabled', true);
//            }
        });
    });

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

        var today = new Date();
        var yyyy = today.getFullYear();

        $('.datepicker_registration_date').datetimepicker({
            viewMode: 'years',
            format: 'DD-MMM-YYYY',
            minDate: '01/01/' + (yyyy - 50),
            maxDate: today,
        });

        $('.datepicker').datetimepicker({
            viewMode: 'years',
            format: 'DD-MMM-YYYY',
//            minDate: '01/01/'+(yyyy-10),
//            maxDate: '01/01/'+(yyyy+10)
            maxDate: 'now'
        });


        $('.commercial_operation_date').datetimepicker({
            viewMode: 'years',
            format: 'DD-MMM-YYYY',
            minDate: '01/01/' + (yyyy - 150),
            maxDate: '01/01/' + (yyyy + 150)

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
                url: "<?php echo url(); ?>/bida-registration/get-district-by-division",
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
        // console.log(organizationStatusId);
        // 3 = Local, 2 = Foreign, 1 = Joint Venture
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
        }else {
            $(".country_of_origin_div").show('slow');
            $("#country_of_origin_id").addClass('required');

            $("#finance_src_loc_equity_1_row_id").show('slow');
            $("#finance_src_foreign_equity_1_row_id").show('slow');
        }

        CategoryWiseDocLoad(organizationStatusId);

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

    function findBusinessClassCode(selectClass) {

        var business_class_code = (selectClass !== undefined) ? selectClass : $("#business_class_code").val();

        var _token = $('input[name="_token"]').val();

        if (business_class_code != '' && (business_class_code.length > 3)) {

            $("#business_class_list_of_code").text('');
            $("#business_class_list").html('');

            $.ajax({
                type: "GET",
                url: "/bida-registration/get-business-class-single-list",
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
                            option += '<option value="' + id + '">' + value + '</option>';
                        });

                        // table_row += '<tr><td width="20%" class="required-star">Sub class</td><td colspan="2"><select onchange="otherSubClassCodeName(this.value)" name="sub_class_id" class="form-control required">' + option + '</select></td></tr>';
                        table_row += '<tr><td width="20%" class="required-star">Sub class</td><td colspan="2"><select name="sub_class_id" class="form-control required">' + option + '</select></td></tr>';

                        // table_row += '<tr id="other_sub_class_code_parent" class="hidden"><td width="20%" class="">Other sub class code</td><td colspan="2"><input type="text" name="other_sub_class_code" id="other_sub_class_code" class="form-control" value=""></td></tr>';
                        // table_row += '<tr id="other_sub_class_name_parent" class="hidden"><td width="20%" class="required-star">Other sub class name</td><td colspan="2"><input type="text" name="other_sub_class_name" id="other_sub_class_name" class="form-control" value=""></td></tr>';

                        $("#business_class_list_of_code").text(business_class_code);
                        $("#business_class_list").html(table_row);
                        $("#is_valid_bbs_code").val(1);

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

    $(document).ready(function() {
        getDivisionalOfficeData();

        $('#office_division_id').on('change', function() {
            getDivisionalOfficeData();
        });

        $('#factory_district_id').on('change', function() {
            getDivisionalOfficeData();
        });
    });

    function getDivisionalOfficeData() {
        var _token = $('input[name="_token"]').val();
        var officeDivisionId = $('#office_division_id').val();
        var factoryDistrictId = $('#factory_district_id').val();

        $('.loading_data').remove();
        $('#tab').after('<span class="loading_data"><strong>Loading...</strong> <i class="fa fa-spinner fa-spin"></i></span>');

        $.ajax({
            url: '{{ route("get-divisional-office") }}',
            method: 'POST',
            data: {
                _token: _token,
                office_division_id: officeDivisionId,
                factory_district_id: factoryDistrictId
            },
            success: function(approvalCenterList) {
                $('.loading_data').remove();
                updateTabs(approvalCenterList.data);
            },
            error: function(error) {
                $('.loading_data').remove();
                console.error('Error fetching divisional office data:', error);
            }
        });
    }

    function updateTabs(data) {
        var tabContainer = $('#tab');
        var contentContainer = $('#visaTypeTabContent');

        tabContainer.empty();
        contentContainer.empty();

        data.forEach(function(approvalCenterList, index) {
            // Create a new tab
            var tab = $('<a>', {
                href: '#tab' + approvalCenterList.id,
                class: 'showInPreview btn btn-md btn-info',
                'data-toggle': 'tab'
            });

            // Create a radio button inside the tab
            var radioBtn = $('<input>', {
                type: 'radio',
                name: 'approval_center_id',
                value: approvalCenterList.id,
                class: 'badgebox required ml-1'
            });

            // Create the label for the radio button
            var label = $('<span>', {
                class: 'badge ml-1',
                text: ' ',
                css: {
                    'padding': '8px 13px'
                }
            });
            var tabLabel = $('<span>', { text: approvalCenterList.office_name });

            // Append elements to the tab
            tab.append(radioBtn, tabLabel, label);

            // Append the tab to the tab container
            tabContainer.append(tab);

            // Create content for the tab
            var tabContent = $('<div>', {
                class: 'tab-pane visaTypeTabPane fade in',
                id: 'tab' + approvalCenterList.id
            });

            var content = $('<div>', { class: 'col-sm-12' });
            content.append($('<div>', { html: '<h4>You have selected <b>' + approvalCenterList.office_name + '</b>, ' + approvalCenterList.office_address + '.</h4>' }));

            // Append the content to the tab content container
            tabContent.append(content);
            contentContainer.append(tabContent);


            // Event handler for radio button change
            radioBtn.change(function () {
                $('input[name="approval_center_id"]').each(function () {
                    var otherLabel = $(this).siblings('.badge');
                    otherLabel.text(' ');
                    otherLabel.css({ 'padding': '8px 13px' });
                });

                if ($(this).is(':checked')) {
                    label.text('✔');
                    label.css({ 'padding': '3px 9px' });
                    contentContainer.show();
                } else {
                    label.text(' ');
                    label.css({ 'padding': '8px 13px' });
                    contentContainer.hide();
                }
            });
        });

        contentContainer.hide();

        $('input[name="approval_center_id"]').change(function () {
            if ($(this).is(':checked')) {
                contentContainer.show();
            }
        });
    }
</script>