<?php
// Get application basic company information
$basicInfo = CommonFunction::getBasicInformationByCompanyId($company_id);
?>

<div class="panel panel-info">
    <div class="panel-heading"><strong>Basic Company Information (Non editable info. pulled from the basic information provided at the first time by your company)</strong></div>
    <div class="panel-body">
        <fieldset class="scheduler-border">
            <legend class="scheduler-border">Company Information:</legend>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 {{$errors->has('department_id') ? 'has-error': ''}}">
                        {!! Form::label('department_id','Department',['class'=>'col-md-3 text-left']) !!}
                        <div class="col-md-9">
                            <input class="form-control input-md" type="text" value="{{ $basicInfo->department }}" readonly>
                            {!! $errors->first('department_id','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 form-group {{$errors->has('company_name') ? 'has-error': ''}}">
                        {!! Form::label('company_name','Name of Organization in English (Proposed)',['class'=>'col-md-3 text-left']) !!}
                        <div class="col-md-9">
                            <input class="form-control input-md" type="text" value="{{ $basicInfo->company_name }}" readonly>
                            {!! $errors->first('company_name','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>

                    <div class="col-md-12 form-group {{$errors->has('company_name_bn') ? 'has-error': ''}}">
                        {!! Form::label('company_name_bn','Name of Organization in Bangla (Proposed)',['class'=>'col-md-3 text-left']) !!}
                        <div class="col-md-9">
                            <input class="form-control input-md" type="text" value="{{ $basicInfo->company_name_bn }}" readonly>
                            {!! $errors->first('company_name_bn','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 {{$errors->has('service_type') ? 'has-error': ''}}">
                        {!! Form::label('service_type','Desired Service from BIDA',['class'=>'col-md-3 text-left', 'id'=> 'service_type_label']) !!}
                        <div class="col-md-9">
                            <input class="form-control input-md" type="text" value="{{ $basicInfo->service_name }}" readonly>
                            {!! $errors->first('service_type','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                </div>
            </div>
            @if($basicInfo->service_type == 5)
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::label('reg_commercial_office','Commercial office type', ['class'=>'col-md-3 text-left']) !!}
                            <div class="col-md-9">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->reg_commercial_office_name }}" readonly>
                                {!! $errors->first('reg_commercial_office','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($basicInfo->business_category == 1)
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 {{$errors->has('ownership_status_id') ? 'has-error': ''}}">
                        {!! Form::label('ownership_status_id','Ownership status',['class'=>'col-md-3 text-left']) !!}
                        <div class="col-md-9">
                            <input class="form-control input-md" type="text" value="{{ $basicInfo->ea_ownership_status }}" readonly>
                            {!! $errors->first('ownership_status_id','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 {{$errors->has('organization_type_id') ? 'has-error': ''}}">
                        {!! Form::label('organization_type_id','Type of the organization',['class'=>'col-md-3 text-left']) !!}
                        <div class="col-md-9">
                            <input class="form-control input-md" type="text" value="{{ $basicInfo->ea_organization_type }}" readonly>
                            {!! $errors->first('organization_type_id','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                </div>
            </div>

            @if($basicInfo->ea_organization_type_id == 14 && $basicInfo->business_category ==2)
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12 {{$errors->has('organization_type_other') ? 'has-error': ''}}">
                            <div class="col-md-3"></div>
                            <div class="col-md-9">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->organization_type_other }}" readonly>
                                {!! $errors->first('organization_type_other','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endif


            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-12 {{$errors->has('major_activities') ? 'has-error' : ''}}">
                        {!! Form::label('major_activities','Major activities in brief',['class'=>'col-md-3']) !!}
                        <div class="col-md-9">
                            <span style="height: 100%; background: #eee;" class="form-control input-md">{{ $basicInfo->major_activities }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
        {{-- Start business category --}}
        @if($basicInfo->business_category == 2)
            {{--Information of Responsible Person--}}
            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Information of Responsible Person:</legend>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6 {{$errors->has('ceo_country_id') ? 'has-error': ''}}">
                            {!! Form::label('ceo_country_id','Country ',['class'=>'col-md-5 text-left']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_country }}" readonly>
                                {!! $errors->first('ceo_country_id','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-md-6 {{$errors->has('ceo_full_name') ? 'has-error': ''}}">
                            {!! Form::label('ceo_full_name','Full Name',['class'=>'col-md-5 text-left']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_full_name }}" readonly>
                                {!! $errors->first('ceo_full_name','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        @if($basicInfo->ceo_country_id == 18)
                            <div class="col-md-6 {{$errors->has('ceo_nid') ? 'has-error': ''}}">
                                {!! Form::label('ceo_nid','NID No.',['class'=>'col-md-5 text-left']) !!}
                                <div class="col-md-7">
                                    <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_nid }}" readonly>
                                    {!! $errors->first('ceo_nid','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        @else
                            <div class="col-md-6 {{$errors->has('ceo_passport_no') ? 'has-error': ''}}">
                                {!! Form::label('ceo_passport_no','Passport No.',['class'=>'col-md-5 text-left']) !!}
                                <div class="col-md-7">
                                    <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_passport_no }}" readonly>
                                    {!! $errors->first('ceo_passport_no','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        @endif
                        <div class="col-md-6 {{$errors->has('ceo_designation') ? 'has-error': ''}}">
                            {!! Form::label('ceo_designation','Designation', ['class'=>'col-md-5 text-left']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_designation }}" readonly>
                                {!! $errors->first('ceo_designation','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6 {{$errors->has('ceo_mobile_no') ? 'has-error': ''}}">
                            {!! Form::label('ceo_mobile_no','Mobile No. ',['class'=>'col-md-5 text-left']) !!}
                            <div class="col-md-7">
                                <input id="ceo_mobile_no" class="form-control input-md" type="text" value="{{ $basicInfo->ceo_mobile_no }}" readonly>
                                {!! $errors->first('ceo_mobile_no','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-md-6 {{$errors->has('ceo_email') ? 'has-error': ''}}">
                            {!! Form::label('ceo_email','Email ',['class'=>'col-md-5 text-left']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_email }}" readonly>
                                {!! $errors->first('ceo_email','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6 {{$errors->has('ceo_gender') ? 'has-error': ''}}">
                            {!! Form::label('ceo_gender','Gender', ['class'=>'col-md-5 text-left']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_gender }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            {!! Form::label('ceo_auth_letter','Authorization Letter', ['class'=>'col-md-5 text-left']) !!}
                            <div class="col-md-7">
                                <a target="_blank" class="btn btn-xs btn-primary" title="Authorization Letter"
                                   href="{{ URL::to('users/upload/'.$basicInfo->ceo_auth_letter) }}">
                                    <i class="fa fa-file-pdf-o"></i>
                                    Authorization Letter
                                </a>
                                <input type="hidden" name="ceo_auth_letter" value="{{ $basicInfo->ceo_auth_letter }}">
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        @else
            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Information of Principal Promoter/ Chairman/ Managing Director/ State CEO:</legend>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6 {{$errors->has('ceo_country_id') ? 'has-error': ''}}">
                            {!! Form::label('ceo_country_id','Country ',['class'=>'col-md-5 text-left']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_country }}" readonly>
                                {!! $errors->first('ceo_country_id','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>

                        <div class="col-md-6 {{$errors->has('ceo_dob') ? 'has-error': ''}}">
                            {!! Form::label('ceo_dob','Date of Birth',['class'=>'col-md-5']) !!}
                            <div class=" col-md-7">
                                <div class="datepicker input-group date" data-date-format="dd-mm-yyyy">
                                    <input class="form-control input-md" type="text" value="{{ date('d-M-Y', strtotime($basicInfo->ceo_dob)) }}" readonly>
                                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                </div>
                                {!! $errors->first('ceo_dob','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        @if($basicInfo->ceo_country_id == 18)
                            <div class="col-md-6 {{$errors->has('ceo_nid') ? 'has-error': ''}}">
                                {!! Form::label('ceo_nid','NID No.',['class'=>'col-md-5 text-left']) !!}
                                <div class="col-md-7">
                                    <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_nid }}" readonly>
                                    {!! $errors->first('ceo_nid','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        @else
                            <div class="col-md-6 {{$errors->has('ceo_passport_no') ? 'has-error': ''}}">
                                {!! Form::label('ceo_passport_no','Passport No.',['class'=>'col-md-5 text-left']) !!}
                                <div class="col-md-7">
                                    <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_passport_no }}" readonly>
                                    {!! $errors->first('ceo_passport_no','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        @endif
                        <div class="col-md-6 {{$errors->has('ceo_designation') ? 'has-error': ''}}">
                            {!! Form::label('ceo_designation','Designation', ['class'=>'col-md-5 text-left']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_designation }}" readonly>
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
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_full_name }}" readonly>
                                {!! $errors->first('ceo_full_name','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                        @if($basicInfo->ceo_country_id == 18)
                            <div class="col-md-6 {{$errors->has('ceo_district_id') ? 'has-error': ''}}">
                                {!! Form::label('ceo_district_id','District/ City/ State ',['class'=>'col-md-5 text-left']) !!}
                                <div class="col-md-7">
                                    <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_district_name }}" readonly>
                                    {!! $errors->first('ceo_district_id','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        @else
                            <div class="col-md-6 {{$errors->has('ceo_city') ? 'has-error': ''}}">
                                {!! Form::label('ceo_city','District/ City/ State',['class'=>'text-left  col-md-5']) !!}
                                <div class="col-md-7">
                                    <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_city }}" readonly>
                                    {!! $errors->first('ceo_city','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        @if($basicInfo->ceo_country_id == 18)
                            <div class="col-md-6 {{$errors->has('ceo_thana_id') ? 'has-error': ''}}">
                                {!! Form::label('ceo_thana_id','Police Station/ Town ',['class'=>'col-md-5 text-left','placeholder'=>'Select district first']) !!}
                                <div class="col-md-7">
                                    <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_thana_name }}" readonly>
                                    {!! $errors->first('ceo_thana_id','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        @else
                            <div class="col-md-6 {{$errors->has('ceo_state') ? 'has-error': ''}}">
                                {!! Form::label('ceo_state','State/ Province',['class'=>'text-left  col-md-5']) !!}
                                <div class="col-md-7">
                                    <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_state }}" readonly>
                                    {!! $errors->first('ceo_state','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        @endif
                        <div class="col-md-6 {{$errors->has('ceo_post_code') ? 'has-error': ''}}">
                            {!! Form::label('ceo_post_code','Post/ Zip Code ',['class'=>'col-md-5 text-left']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_post_code }}" readonly>
                                {!! $errors->first('ceo_post_code','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6 {{$errors->has('ceo_address') ? 'has-error': ''}}">
                            {!! Form::label('ceo_address','House, Flat/ Apartment, Road ',['class'=>'col-md-5 text-left']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_address }}" readonly>
                                {!! $errors->first('ceo_address','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-md-6 {{$errors->has('ceo_telephone_no') ? 'has-error': ''}}">
                            {!! Form::label('ceo_telephone_no','Telephone No.',['class'=>'col-md-5 text-left']) !!}
                            <div class="col-md-7">
                                <input id="ceo_telephone_no" class="form-control input-md" type="text" value="{{ $basicInfo->ceo_telephone_no }}" readonly>
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
                                <input id="ceo_mobile_no" class="form-control input-md" type="text" value="{{ $basicInfo->ceo_mobile_no }}" readonly>
                                {!! $errors->first('ceo_mobile_no','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>

                        <div class="col-md-6 {{$errors->has('ceo_father_name') ? 'has-error': ''}}">
                            {!! Form::label('ceo_father_name','Father\'s Name',['class'=>'col-md-5 text-left', 'id' => 'ceo_father_label']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_father_name }}" readonly>
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
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_email }}" readonly>
                                {!! $errors->first('ceo_email','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-md-6 {{$errors->has('ceo_mother_name') ? 'has-error': ''}}">
                            {!! Form::label('ceo_mother_name','Mother\'s Name',['class'=>'col-md-5 text-left', 'id' => 'ceo_mother_label']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_mother_name }}" readonly>
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
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_fax_no }}" readonly>
                                {!! $errors->first('ceo_fax_no','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-md-6 {{$errors->has('ceo_spouse_name') ? 'has-error': ''}}">
                            {!! Form::label('ceo_spouse_name','Spouse name',['class'=>'col-md-5 text-left']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_spouse_name }}" readonly>
                                {!! $errors->first('ceo_spouse_name','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6 {{$errors->has('ceo_gender') ? 'has-error': ''}}">
                            {!! Form::label('ceo_gender','Gender', ['class'=>'col-md-5 text-left']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->ceo_gender }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        @endif

        <fieldset class="scheduler-border">
            <legend class="scheduler-border">Office Address:</legend>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6 {{$errors->has('office_division_id') ? 'has-error': ''}}">
                        {!! Form::label('office_division_id','Division',['class'=>'text-left col-md-5']) !!}
                        <div class="col-md-7">
                            <input class="form-control input-md" type="text" value="{{ $basicInfo->office_division_name }}" readonly>
                            {!! $errors->first('office_division_id','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                    <div class="col-md-6 {{$errors->has('office_district_id') ? 'has-error': ''}}">
                        {!! Form::label('office_district_id','District',['class'=>'col-md-5 text-left']) !!}
                        <div class="col-md-7">
                            <input class="form-control input-md" type="text" value="{{ $basicInfo->office_district_name }}" readonly>
                            {!! $errors->first('office_district_id','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6 {{$errors->has('office_thana_id') ? 'has-error': ''}}">
                        {!! Form::label('office_thana_id','Police Station', ['class'=>'col-md-5 text-left']) !!}
                        <div class="col-md-7">
                            <input class="form-control input-md" type="text" value="{{ $basicInfo->office_thana_name }}" readonly>
                            {!! $errors->first('office_thana_id','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                    <div class="col-md-6 {{$errors->has('office_post_office') ? 'has-error': ''}}">
                        {!! Form::label('office_post_office','Post Office',['class'=>'col-md-5 text-left']) !!}
                        <div class="col-md-7">
                            <input class="form-control input-md" type="text" value="{{ $basicInfo->office_post_office }}" readonly>
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
                            <input class="form-control input-md" type="text" value="{{ $basicInfo->office_post_code }}" readonly>
                            {!! $errors->first('office_post_code','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                    <div class="col-md-6 {{$errors->has('office_address') ? 'has-error': ''}}">
                        {!! Form::label('office_address','House, Flat/ Apartment, Road ',['class'=>'col-md-5 text-left']) !!}
                        <div class="col-md-7">
                            <input class="form-control input-md" type="text" value="{{ $basicInfo->office_address }}" readonly>
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
                            <input id="office_telephone_no" class="form-control input-md" type="text" value="{{ $basicInfo->office_telephone_no }}" readonly>
                            {!! $errors->first('office_telephone_no','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                    <div class="col-md-6 {{$errors->has('office_mobile_no') ? 'has-error': ''}}">
                        {!! Form::label('office_mobile_no','Mobile No. ',['class'=>'col-md-5 text-left']) !!}
                        <div class="col-md-7">
                            <input id="office_mobile_no" class="form-control input-md" type="text" value="{{ $basicInfo->office_mobile_no }}" readonly>
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
                            <input class="form-control input-md" type="text" value="{{ $basicInfo->office_fax_no }}" readonly>
                            {!! $errors->first('office_fax_no','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                    <div class="col-md-6 {{$errors->has('office_email') ? 'has-error': ''}}">
                        {!! Form::label('office_email','Email ',['class'=>'col-md-5 text-left']) !!}
                        <div class="col-md-7">
                            <input class="form-control input-md" type="text" value="{{ $basicInfo->office_email }}" readonly>
                            {!! $errors->first('office_email','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>

        {{--2 = industrial department --}}
        @if($basicInfo->business_category != 2 && $basicInfo->department_id == 2)
            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Factory Address:</legend>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6 {{$errors->has('factory_district_id') ? 'has-error': ''}}">
                            {!! Form::label('factory_district_id','District',['class'=>'col-md-5 text-left','id' => 'factory_district_label']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->factory_district_name }}" readonly>
                                {!! $errors->first('factory_district_id','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-md-6 {{$errors->has('factory_thana_id') ? 'has-error': ''}}">
                            {!! Form::label('factory_thana_id','Police Station', ['class'=>'col-md-5 text-left', 'id' => 'factory_thana_label']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->factory_thana_name }}" readonly>
                                {!! $errors->first('factory_thana_id','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6 {{$errors->has('factory_post_office') ? 'has-error': ''}}">
                            {!! Form::label('factory_post_office','Post Office',['class'=>'col-md-5 text-left', 'id'=>'factory_post_label']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->factory_post_office }}" readonly>
                                {!! $errors->first('factory_post_office','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-md-6 {{$errors->has('factory_post_code') ? 'has-error': ''}}">
                            {!! Form::label('factory_post_code','Post Code', ['class'=>'col-md-5 text-left', 'id'=>'factory_post_code_label']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->factory_post_code }}" readonly>
                                {!! $errors->first('factory_post_code','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6 {{$errors->has('factory_address') ? 'has-error': ''}}">
                            {!! Form::label('factory_address','House, Flat/ Apartment, Road ',['class'=>'col-md-5 text-left', 'id'=>'factory_address_label']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->factory_address }}" readonly>
                                {!! $errors->first('factory_address','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-md-6 {{$errors->has('factory_telephone_no') ? 'has-error': ''}}">
                            {!! Form::label('factory_telephone_no','Telephone No.',['class'=>'col-md-5 text-left']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->factory_telephone_no }}" readonly>
                                {!! $errors->first('factory_telephone_no','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6 {{$errors->has('factory_mobile_no') ? 'has-error': ''}}">
                            {!! Form::label('factory_mobile_no','Mobile No. ',['class'=>'col-md-5 text-left', 'id'=>'factory_mobile_label']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->factory_mobile_no }}" readonly>
                                {!! $errors->first('factory_mobile_no','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-md-6 {{$errors->has('factory_fax_no') ? 'has-error': ''}}">
                            {!! Form::label('factory_fax_no','Fax No. ',['class'=>'col-md-5 text-left']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->factory_fax_no }}" readonly>
                                {!! $errors->first('factory_fax_no','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6 {{$errors->has('factory_email') ? 'has-error': ''}}">
                            {!! Form::label('factory_email','Email ',['class'=>'col-md-5 text-left', 'id'=>'factory_email_label']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->factory_email }}" readonly>
                                {!! $errors->first('factory_email','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-md-6 {{$errors->has('factory_mouja') ? 'has-error': ''}}">
                            {!! Form::label('factory_mouja','Mouja No.',['class'=>'col-md-5 text-left', 'id'=>'factory_mouja_label']) !!}
                            <div class="col-md-7">
                                <input class="form-control input-md" type="text" value="{{ $basicInfo->factory_mouja }}" readonly>
                                {!! $errors->first('factory_mouja','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        @endif
    </div>
</div>

<script>
    $(function () {
        $("#office_mobile_no").intlTelInput({
            hiddenInput: "ex_office_mobile_no",
            initialCountry: "BD",
            placeholderNumberType: "MOBILE",
            separateDialCode: true
        });

        $("#office_telephone_no").intlTelInput({
            hiddenInput: "ex_office_mobile_no",
            initialCountry: "BD",
            placeholderNumberType: "MOBILE",
            separateDialCode: true
        });

        $("#ceo_mobile_no").intlTelInput({
            hiddenInput: "ex_office_mobile_no",
            initialCountry: "BD",
            placeholderNumberType: "MOBILE",
            separateDialCode: true
        });

        $("#ceo_telephone_no").intlTelInput({
            hiddenInput: "ex_office_mobile_no",
            initialCountry: "BD",
            placeholderNumberType: "MOBILE",
            separateDialCode: true
        });
        $("#applicationForm").find('.iti').css({"width":"-moz-available", "width":"-webkit-fill-available"});
    });

</script>