@extends('layouts.front')

<?php
$userData = session('oauth_data');
$nationality_type = session('nationality_type');
$identity_type = session('identity_type');
$passport_info = Session::has('passport_info') ? json_decode(Encryption::decode(Session::get('passport_info')), true) : '';
$eTin_info = Session::has('eTin_info') ? json_decode(Encryption::decode(Session::get('eTin_info')), true) : '';
$nid_info = Session::has('nid_info') ? json_decode(Encryption::decode(Session::get('nid_info')), true) : '';
$user_pic = '';

if ($identity_type === 'nid') {
    $user_pic = isset($nid_info['photo']) ? (empty($nid_info['photo']) ? '' : $nid_info['photo']) : '';
} else if ($identity_type === 'passport') {
    $passport_nationality = \App\Modules\Users\Models\Countries::where('id', $passport_info['passport_nationality'])->pluck('nicename');
}

?>

@section('style')
    {{--<link rel="stylesheet" href="{{ asset("assets/plugins/toastr.min.css") }}"/>--}}
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2-bootstrap.css') }}">

    <style>
        .radio_hover {
            cursor: pointer;
        }

        fieldset.scheduler-border {
            border: 1px solid #afa3a3 !important;
            padding: 0 1.4em 0 1.4em !important;
            margin: 0 0 1.5em 0 !important;
            -webkit-box-shadow: 0px 0px 0px 0px #000;
            box-shadow: 0px 0px 0px 0px #000;
        }

        legend.scheduler-border {
            font-size: 1.2em !important;
            font-weight: bold !important;
            text-align: left !important;
            width: auto;
            padding: 0 10px;
            border-bottom: none;
        }

        input[type="radio"] {
            -webkit-appearance: checkbox; /* Chrome, Safari, Opera */
            -moz-appearance: checkbox; /* Firefox */
            -ms-appearance: checkbox; /* not currently supported */
        }

        #userPicViewer, #investorPhotoViewer {
            width: 150px;
            height: 150px;
            /*border-radius: 50%;*/
            border-radius: 3px;
        }

        .user_pic_area .img-thumbnail {
            display: inline-block;
            max-width: 100%;
            height: auto;
            padding: 2px;
            line-height: 1.42857143;
            background-color: #8eb8ca;
            border: none;
        }

        .iti {
            display: block;
            width: 100%;
        }

        .g-recaptcha {
            transform: scale(0.97);
            transform-origin: 0 0;
        }

        #registrationForm .form-group {
            margin-bottom: 5px;
            margin-right: -10px;
            margin-left: -10px;
        }

        legend {
            font-size: 17px !important;
            border-bottom: 1px solid #828282 !important;
            font-weight: bold;
        }
    </style>
@endsection

@section("content")

    <div class="row">
        <div class="col-md-12">
            <div class="row">
                @include('partials.messages')
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="sign-up-box-">
                {{-- <h3 class="text-center">Registration process Step-3 (For the first time only)</h3> --}}
                <h4 class="text-center logo-color">
                    <strong>Registration process step-3 (For the first time only)</strong>
                </h4>
                <hr/>

                {!! Form::open(array('url' => 'signup/registration','method' => 'post', 'class' => 'form-horizontal', 'id' => 'registrationForm', 'name' => 'registrationForm',
                    'enctype' =>'multipart/form-data', 'files' => 'true')) !!}

                <div class="col-md-7">

                    <div class="form-group has-feedback {{ $errors->has('nationality_type') ? 'has-error' : ''}}">
                        <label for="nationality_type" class="col-md-5">Nationality (Based on country)</label>
                        <div class="col-md-7">
                            {{ ': ' . ucfirst($nationality_type) }}
                            {!! Form::hidden('nationality_type', \App\Libraries\Encryption::encode($nationality_type)) !!}
                            {!! $errors->first('nationality_type','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>

                    <div class="form-group has-feedback {{ $errors->has('identity_type') ? 'has-error' : ''}}">
                        <label for="identity_type" class="col-md-5">Identity type</label>
                        <div class="col-md-7">
                            {{ ': ' . strtoupper($identity_type) }}
                            {!! Form::hidden('identity_type', \App\Libraries\Encryption::encode($identity_type)) !!}
                            {!! $errors->first('identity_type','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>

                    @if ($identity_type === 'nid')
                        <div id="NIDInfoArea">
                            <div class="form-group has-feedback">
                                <label for="user_nid_number" class="col-md-5">NID</label>
                                <div class="col-md-7">
                                    <?php $user_nid_number = !empty($nid_info['nationalId']) ? $nid_info['nationalId'] : 'N/A'; ?>
                                    {{ ': ' . $user_nid_number }}
                                </div>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="user_name_en" class="col-md-5">Name</label>
                                <div class="col-md-7">
                                    <?php $user_name_en = !empty($nid_info['nameEn']) ? $nid_info['nameEn'] : 'N/A'; ?>
                                    {{ ': ' . $user_name_en }}
                                </div>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="user_DOB" class="col-md-5">Date of Birth</label>
                                <div class="col-md-7">
                                    <?php $user_DOB = !empty($nid_info['dateOfBirth']) ? date('d-M-Y', strtotime($nid_info['dateOfBirth'])) : 'N/A'; ?>
                                    {{ ': ' . $user_DOB }}
                                </div>
                            </div>
                            
                        </div>
                    @elseif($identity_type === 'tin')
                        <div id="ETINInfoArea">
                            <div class="form-group has-feedback">
                                <label for="user_name_en" class="col-md-5">TIN Number</label>
                                <div class="col-md-7">
                                    <?php $user_tin_number = !empty($eTin_info['etin_number']) ? ucfirst($eTin_info['etin_number']) : 'N/A'; ?>
                                    {{ ': ' . $user_tin_number }}
                                </div>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="user_name_en" class="col-md-5">Name</label>
                                <div class="col-md-7">
                                    <?php $user_name_en = !empty($eTin_info['assesName']) ? ucfirst($eTin_info['assesName']) : 'N/A'; ?>
                                    {{ ': ' . $user_name_en }}
                                </div>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="father_name" class="col-md-5">Father's Name</label>
                                <div class="col-md-7">
                                    <?php $father_name = !empty($eTin_info['fathersName']) ? ucfirst($eTin_info['fathersName']) : 'N/A'; ?>
                                    {{ ': ' . $father_name }}
                                </div>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="user_DOB" class="col-md-5">Date of Birth</label>
                                <div class="col-md-7">
                                    <?php $user_DOB = ($eTin_info['dob'] != '') ? date('d-M-Y', strtotime($eTin_info['dob'])) : 'N/A'; ?>
                                    {{ ': ' . $user_DOB }}
                                </div>
                            </div>
                        </div>
                    @elseif($identity_type === 'passport')
                        <div id="PassportInfoArea">
                            <div class="form-group has-feedback">
                                <label for="passport_nationality" class="col-md-5">Passport nationality</label>
                                <div class="col-md-7">
                                    <?php $passport_nationality = !empty($passport_nationality) ? ucfirst($passport_nationality) : 'N/A'; ?>
                                    {{ ': ' . $passport_nationality }}
                                </div>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="passport_type" class="col-md-5">Passport type</label>
                                <div class="col-md-7">
                                    <?php $passport_type = !empty($passport_info['passport_type']) ? ucfirst($passport_info['passport_type']) : 'N/A'; ?>
                                    {{ ': ' . $passport_type }}
                                </div>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="passport_no" class="col-md-5">Passport No.</label>
                                <div class="col-md-7">
                                    <?php $passport_no = !empty($passport_info['passport_no']) ? $passport_info['passport_no'] : 'N/A'; ?>
                                    {{ ': ' . $passport_no }}
                                </div>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="passport_surname" class="col-md-5">Surname</label>
                                <div class="col-md-7">
                                    <?php $passport_surname = !empty($passport_info['passport_surname']) ? $passport_info['passport_surname'] : 'N/A'; ?>
                                    {{ ': ' . $passport_surname }}
                                </div>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="passport_given_name" class="col-md-5">Given Name</label>
                                <div class="col-md-7">
                                    <?php $passport_given_name = !empty($passport_info['passport_given_name']) ? $passport_info['passport_given_name'] : 'N/A'; ?>
                                    {{ ': ' . $passport_given_name }}
                                </div>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="passport_personal_no" class="col-md-5">Personal No.</label>
                                <div class="col-md-7">
                                    <?php $passport_personal_no = !empty($passport_info['passport_personal_no']) ? $passport_info['passport_personal_no'] : 'N/A'; ?>
                                    {{ ': ' . $passport_personal_no }}
                                </div>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="passport_DOB" class="col-md-5">Date of Birth</label>
                                <div class="col-md-7">
                                    <?php $passport_DOB = !empty($passport_info['passport_DOB']) ? date('d-M-Y', strtotime($passport_info['passport_DOB'])) : 'N/A'; ?>
                                    {{ ': ' . $passport_DOB }}
                                </div>
                            </div>

                            <div class="form-group has-feedback">
                                <label for="passport_date_of_expire" class="col-md-5">Date of Expiry</label>
                                <div class="col-md-7">
                                    <?php $passport_date_of_expire = !empty($passport_info['passport_date_of_expire']) ? date('d-M-Y', strtotime($passport_info['passport_date_of_expire'])) : 'N/A'; ?>
                                    {{ ': ' . $passport_date_of_expire }}
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- designation --}}
                    <div class="form-group has-feedback {{ $errors->has('designation') ? 'has-error' : ''}}">
                        <label for="designation" class="col-md-5 required-star">Designation</label>
                        <div class="col-md-7">
                            {!! Form::text('designation', '', $attributes = array('class'=>'form-control input-sm required',
                                   'placeholder' => 'Enter your designation', 'id'=>"designation")) !!}
                            {!! $errors->first('designation','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>

                    <div id="companyInfo">
                        <fieldset>
                            <legend>Organization info</legend>

                            {{-- Organization Types --}}
                            <div class="form-group has-feedback {{$errors->has('company_type') ? 'has-error': ''}}"
                                 id="company_type_div">
                                {!! Form::label('company_type','Organization types',['class'=>'col-md-5 required-star']) !!}
                                <div class="col-md-7">
                                    <div class="radio">
                                        <label>{!! Form::radio('company_type', '1', false, ['class'=>'company_type required', 'id' => 'existing_company']) !!}
                                            Existing (The company is exist in the database of BIDA)</label>
                                    </div>
                                    <div class="radio">
                                        <label>{!! Form::radio('company_type', '2', false, ['class'=>'company_type required', 'id' => 'new_company']) !!}
                                            New (The company is not exist in the database of BIDA. But, next time it will be available)</label>
                                    </div>
                                    {!! $errors->first('company_type','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>

                            {{-- Business Category --}}
                            <div class="form-group has-feedback {{$errors->has('business_category') ? 'has-error': ''}} hidden"
                                 id="business_category_div">
                                {!! Form::label('business_category','Business Category',['class'=>'col-md-5 required-star']) !!}
                                <div class="col-md-7">
                                    <label class="radio-inline">{!! Form::radio('business_category', '1', false, ['class'=>'business_category', 'id' => 'private', 'onclick' => 'businessCategoryCheck(this.value)']) !!}
                                        Private</label>
                                    <label class="radio-inline">{!! Form::radio('business_category', '2', false, ['class'=>'business_category', 'id' => 'government', 'onclick' => 'businessCategoryCheck(this.value)']) !!}
                                        Government</label>
                                    {!! $errors->first('business_category','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>

                            {{-- Organization Name --}}
                            <div id="new_company_div" class="hidden">
                                <div class="form-group has-feedback {{ $errors->has('company_name') ? 'has-error' : ''}}">
                                    <label class="col-md-5 required-star" for="company_name_en">Organization
                                        name (english)</label>
                                    <div class="col-md-7">
                                        {!! Form::text('company_name_en', null, $attributes = array('class'=>'form-control input-sm',  'data-rule-maxlength'=>'255',
                                'placeholder'=>'Organization name in English', 'id'=>"company_name_en")) !!}
                                        {!! $errors->first('company_name_en','<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                                <div class="form-group has-feedback {{ $errors->has('company_name_bn') ? 'has-error' : ''}}">
                                    <label class="col-md-5" for="company_info">Organization name (bangla)</label>
                                    <div class="col-md-7">
                                        {!! Form::text('company_name_bn', null, $attributes = array('class'=>'form-control input-sm',  'data-rule-maxlength'=>'255',
                                'placeholder'=>'Organization name in Bangla', 'id'=>"company_name_bn")) !!}
                                        {!! $errors->first('company_name_bn','<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                            </div>

                            {{-- Existing Company --}}
                            <div class="form-group has-feedback hidden {{ $errors->has('company_id') ? 'has-error' : ''}}"
                                 id="existing_company_div">
                                <label class="col-md-5 required-star" for="company_id">Organization
                                    Info</label>
                                <div class="col-md-7">
                                    {!! Form::select('company_id', $company_infos, '', $attributes = array('class'=>'input-sm form-control',
                                    'id'=>"company_id", 'style'=>'width:100%', 'placeholder' => 'Select one')) !!}
                                    {!! $errors->first('company_id','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    {{-- Address info --}}
                    <div id="address_info_div" class="hidden">
                        <fieldset>
                            <legend>Address info</legend>
                            {{-- Nationality --}}
                            <div class="form-group has-feedback {{ $errors->has('nationality') ? 'has-error' : ''}}">
                                <label for="nationality" class="col-md-5 required-star">Nationality (Based on
                                    residence)</label>
                                <div class="col-md-7">
                                    {!! Form::select('nationality', $nationalities, ($nationality_type === 'bangladeshi' ? 18 : ''), $attributes = array('class'=>'form-control input-sm required',
                                           'placeholder' => 'Select one', 'id'=>"nationality")) !!}
                                    {!! $errors->first('nationality','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>

                            {{-- Country --}}
                            <div class="form-group has-feedback {{ $errors->has('country') ? 'has-error' : ''}}">
                                <label for="country" class="col-md-5 required-star">Country of residence</label>
                                <div class="col-md-7">
                                    {!! Form::select('country', $countries, ($nationality_type === 'bangladeshi' ? 18 : ''), $attributes = array('class'=>'form-control input-sm required', 'data-rule-maxlength'=>'40',
                                    'placeholder' => 'Select one', 'id'=>"country")) !!}
                                    {!! $errors->first('country','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>

                            @if ($nationality_type === 'bangladeshi')
                                <div id="address_bd_area" class="">
                                    <div class="form-group has-feedback {{ $errors->has('division_id') ? 'has-error' : ''}}">
                                        <label for="division_id" class="col-md-5 required-star">Division </label>
                                        <div class="col-md-7">
                                            <?php $district_id = $suggested_address['district_id']; ?>
                                            {!! Form::select('division_id', $divisions, $suggested_address['division_id'], $attributes = array('class'=>'form-control input-sm required','id'=>"division_id", 'onchange'=>"getDistrictByDivisionId('division_id', this.value, 'district_id', $district_id)")) !!}
                                            {!! $errors->first('division_id','<span class="help-block">:message</span>') !!}
                                        </div>
                                    </div>
                                    <div class="form-group has-feedback {{ $errors->has('district_id') ? 'has-error' : ''}}">
                                        <label for="district_id" class="col-md-5 required-star">District </label>
                                        <div class="col-md-7">
                                            <?php $police_station_id = $suggested_address['police_station_id']; ?>
                                            {!! Form::select('district_id', $districts, $suggested_address['district_id'], $attributes = array('class'=>'form-control input-sm required',
                                            'placeholder' => 'Select division first', 'id'=>"district_id", 'onchange'=>"getThanaByDistrictId('district_id', this.value, 'thana_id', $police_station_id)")) !!}
                                            {!! $errors->first('district_id','<span class="help-block">:message</span>') !!}
                                        </div>
                                    </div>

                                    <div class="form-group has-feedback {{ $errors->has('thana') ? 'has-error' : ''}}">
                                        <label for="thana" class="col-md-5 required-star">Police Station</label>
                                        <div class="col-md-7">
                                            {!! Form::select('thana_id', $thana, $suggested_address['police_station_id'], $attributes = array('class'=>'form-control input-sm required', 'placeholder' => 'Select district first',
                                            'data-rule-maxlength'=>'40','id'=>"thana_id")) !!}
                                            {!! $errors->first('thana','<span class="help-block">:message</span>') !!}
                                        </div>
                                    </div>

                                    <div class="form-group has-feedback {{ $errors->has('post_office') ? 'has-error' : ''}}">
                                        <label for="post_office" class="col-md-5 required-star">Post Office</label>
                                        <div class="col-md-7">
                                            {!! Form::text('post_office', $suggested_address['post_office'], $attributes = array('class'=>'form-control engOnly input-sm required', 'placeholder' => 'Name of your post office',
                                            'data-rule-maxlength'=>'40', 'id'=>"post_office")) !!}
                                            {!! $errors->first('post_office','<span class="help-block">:message</span>') !!}
                                        </div>
                                    </div>
                                    <div class="form-group has-feedback {{ $errors->has('post_code') ? 'has-error' : ''}}">
                                        <label for="post_code" class="col-md-5 required-star">Post code (number)</label>
                                        <div class="col-md-7">
                                            {!! Form::text('post_code', $suggested_address['post_code'], $attributes = array('class'=>'form-control post_code_bd input-sm required', 'placeholder'=>'Enter your post code',
                                            'maxlength'=>"20", 'id'=>"post_code")) !!}
                                            {!! $errors->first('post_code','<span class="help-block">:message</span>') !!}
                                        </div>
                                    </div>
                                    <div class="form-group has-feedback {{ $errors->has('road_no') ? 'has-error' : ''}}">
                                        <label for="road_no" class="col-md-5 required-star"> Address
                                            <i class="fa fa-question-circle" data-toggle="tooltip"
                                               title="Please specify your address by mentioning road/ street name and holding no."></i>
                                        </label>
                                        <div class="col-md-7">
                                            {!! Form::text('road_no', $suggested_address['village_ward'], $attributes = array('class'=>'form-control bnEng input-sm required', 'data-rule-maxlength'=>'250',
                                            'placeholder' => 'Enter road / street name / holding number', 'id'=>"road_no")) !!}
                                            {!! $errors->first('road_no','<span class="help-block">:message</span>') !!}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div id="address_abroad_area" class="">
                                    {{-- State --}}
                                    <div class="form-group has-feedback {{ $errors->has('state') ? 'has-error' : ''}}"
                                         id="state_div">
                                        <label for="state" class="col-md-5 text-left required-star"> State </label>
                                        <div class="col-md-7">
                                            {!! Form::text('state', '', $attributes = array('class'=>'form-control textOnly input-sm required', 'placeholder' => 'Name of your state / division',
                                            'data-rule-maxlength'=>'40', 'id'=>"state")) !!}
                                            {!! $errors->first('state','<span class="help-block">:message</span>') !!}
                                        </div>
                                    </div>

                                    {{-- Province / City --}}
                                    <div class="form-group has-feedback {{ $errors->has('province') ? 'has-error' : ''}}"
                                         id="province_div">
                                        <label for="province" class="col-md-5 text-left required-star">Province/
                                            City</label>
                                        <div class="col-md-7">
                                            {!! Form::text('province', $suggested_address['city'], $attributes = array('class'=>'form-control textOnly input-sm required', 'data-rule-maxlength'=>'40',
                                            'placeholder' => 'Enter your province/ city', 'id'=>"province")) !!}
                                            {!! $errors->first('province','<span class="help-block">:message</span>') !!}
                                        </div>
                                    </div>

                                    {{-- Post --}}
                                    <div class="form-group has-feedback {{ $errors->has('post_code_abroad') ? 'has-error' : ''}}">
                                        <label for="post_code_abroad" class="col-md-5 text-left required-star">Post
                                            code</label>
                                        <div class="col-md-7">
                                            {!! Form::text('post_code_abroad', $suggested_address['post_code'], $attributes = array('class'=>'form-control engOnly input-sm required alphaNumeric', 'placeholder'=>'Enter your post code',
                                            'maxlength'=>"20",'id'=>"post_code_abroad")) !!}
                                            {!! $errors->first('post_code_abroad','<span class="help-block">:message</span>') !!}
                                        </div>
                                    </div>

                                    {{-- Address --}}
                                    <div class="form-group has-feedback {{ $errors->has('road_no_abroad') ? 'has-error' : ''}}">
                                        <label for="road_no_abroad" class="col-md-5 text-left required-star">
                                            Address <i class="fa fa-question-circle" data-toggle="tooltip"
                                                       title="Please specify your address by mentioning road/ street name and holding no."></i></label>
                                        <div class="col-md-7">
                                            {!! Form::text('road_no_abroad', $suggested_address['village_ward'], $attributes = array('class'=>'form-control input-sm required', 'data-rule-maxlength'=>'250',
                                            'placeholder' => 'Enter road / street name / holding number', 'id'=>"road_no_abroad")) !!}
                                            {!! $errors->first('road_no_abroad','<span class="help-block">:message</span>') !!}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Mobile Number --}}
                            <div class="form-group has-feedback {{ $errors->has('user_phone') ? 'has-error' : ''}}">
                                <label for="user_phone" class="col-md-5 required-star">Mobile number</label>
                                <div class="col-md-7">
                                    {!! Form::text('user_phone', $userData->mobile, $attributes = array('class'=>'form-control input-sm required',
                                    'maxlength'=>"20", 'data-rule-maxlength'=>'40', 'placeholder'=>'Enter your mobile number','id'=>"user_phone")) !!}
                                    {!! $errors->first('user_phone','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>

                            <div class="form-group has-feedback {{ $errors->has('user_pic') ? 'has-error' : ''}}">
                                <label for="investorPhotoViewer" class="col-md-5 required-star">Profile Image</label>
                                <div class="col-md-7">
                                    <div class="user_pic_area"
                                         style="display: block;background: #f6f6f6;border-radius: 3px;">
                                        <div class="pull-left text-center">
                                            <div id="investorPhotoViewerDiv" style="max-height: 220px">
                                                <img class="img-thumbnail" id="investorPhotoViewer"
                                                     src="{{ empty($user_pic) ? url('assets/images/photo_default.png') : $user_pic }}"
                                                     alt="Investor Photo">
                                                <input type="hidden" name="investor_photo_base64"
                                                       id="investor_photo_base64"
                                                       value="{{ !empty($user_pic) ? $user_pic : '' }}">
                                                <input type="hidden" name="investor_photo_name"
                                                       id="investor_photo_name">
                                            </div>
                                        </div>
                                        <div class="pull-left" style="padding: 15px 10px 0px 10px;text-align: center;">
                                            {!! Form::label('user_pic','Profile Image', ['class'=>'required-star','style'=>'font-size:18px']) !!}
                                            <br/>
                                            <span class="text-success"
                                                  style="font-size: 9px; font-weight: bold;">[File Format: *.jpg/ .jpeg/ .png |<br/> Width 300PX, Height 300PX]</span>
                                            <br/>
                                            <input type="file"
                                                   class="input-md {{ empty($user_pic) ? 'required' : '' }}"
                                                   onchange="readURLUser(this);"
                                                   id="investorPhotoUploadBtn"
                                                   name="investorPhotoUploadBtn"
                                                   data-type="user"
                                                   data-ref="{{Encryption::encodeId(123)}}"
                                                   style="display: inline-block;max-width: 100px;margin: 8px 0;">
                                            <a id="investorPhotoResetBtn"
                                               class="btn btn-sm btn-warning resetIt hidden"
                                               onclick="resetImage(this);"
                                               data-src="{{ url('assets/images/photo_default.png') }}"><i
                                                        class="fa fa-refresh"></i> Reset</a>
                                            {!! $errors->first('user_pic','<span class="help-block">:message</span>') !!}
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>

                        </fieldset>
                    </div>

                    {{-- Authorization letter --}}
                    <div class="form-group has-feedback {{ $errors->has('authorization_file') ? 'has-error' : ''}}">
                        <label class="col-md-5 required-star" for="authorization_file">Authorization letter <i class="fa fa-question-circle" data-toggle="tooltip" title="" data-original-title="If anyone wants to work on behalf of an organization, the company's managing director/chief of the company will sanction a consent letter printed on a Letter Head pad of the respective company."></i></label>
                        <div class="col-md-7">
                            {!! Form::file('authorization_file', ['id'=>'auth_letter', 'class'=>'form-control input-sm required', 'accept'=>"application/pdf"]) !!}
                            <small class=""
                                   style="font-size: 9px; font-weight: bold; color: #666363; font-style: italic">
                                [Format: *.PDF | Maximum 3 MB, Application with Name & Signature] </small>
                            <br/>
                            <a target="_blank" href="{{ url('assets/images/sample_auth_letter.png') }}"><i class="fa fa-file" aria-hidden="true"></i> <i>Sample Authorization letter</i></a>
                            {!! $errors->first('authorization_file','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>

                    {{-- reCAPTCHA --}}
                    <div class="form-group  {{$errors->has('g-recaptcha-response') ? 'has-error' : ''}}">
                        <div class="col-md-offset-5 col-md-7">
                            {!! Recaptcha::render() !!}
                            {!! $errors->first('g-recaptcha-response','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>

                    {{-- Submit btn --}}
                    <div class="form-group">
                        <div class="col-md-offset-5 col-md-7 text-right">
                            <button type="submit" class="btn btn-md btn-primary round-btn btn-block"><b>Submit</b></button>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    {{--                    <div class="form-group has-feedback {{ $errors->has('user_pic') ? 'has-error' : ''}}">--}}
                    {{--                        <div class="row">--}}
                    {{--                            <div class="col-md-offset-1 col-sm-10">--}}
                    {{--                                <div class="user_pic_area"--}}
                    {{--                                     style="display: block;background: rgba(161, 204, 217, 1);border-radius: 60px;">--}}
                    {{--                                    <div class="pull-left text-center">--}}
                    {{--                                        <img class="img-thumbnail" id="userPicViewer"--}}
                    {{--                                             src="{{ empty($user_pic) ? url('assets/images/photo_default.png') : $user_pic }}"--}}
                    {{--                                             alt="Investor Photo">--}}
                    {{--                                    </div>--}}
                    {{--                                    <div class="pull-left" style="padding: 15px 10px 0px 10px;text-align: center;">--}}
                    {{--                                        {!! Form::label('user_pic','Profile Image', ['class'=>'required-star','style'=>'font-size:18px']) !!}--}}
                    {{--                                        <br/>--}}
                    {{--                                        <span class="text-success"--}}
                    {{--                                              style="font-size: 9px; font-weight: bold;">[File Format: *.jpg/ .jpeg/ .png |<br/> Width 300PX, Height 300PX]</span>--}}
                    {{--                                        <br/>--}}
                    {{--                                        <input type="file" name="user_pic" id="user_pic"--}}
                    {{--                                               class="input-md {{ empty($user_pic) ? 'required' : '' }}"--}}
                    {{--                                               onchange="imageDisplay(this,'userPicViewer', 'user_pic_base64', '300x300')"--}}
                    {{--                                               style="display: inline-block;max-width: 100px;margin: 8px 0;"/>--}}
                    {{--                                        <input type="hidden" value="{{ !empty($user_pic) ? $user_pic : '' }}"--}}
                    {{--                                               name="user_pic_base64" id="user_pic_base64">--}}
                    {{--                                        {!! $errors->first('user_pic','<span class="help-block">:message</span>') !!}--}}
                    {{--                                    </div>--}}
                    {{--                                    <div class="clearfix"></div>--}}
                    {{--                                </div>--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}


                    <h5><strong>Terms of Usage of BIDA OSS</strong></h5>
                    <ul>
                        <li>You must follow any policies made available to you within the Services.</li>
                        <li>You have to fill all the given fields with correct information and take responsibility if
                            any wrong or misleading information has been given
                        </li>
                        <li>You are responsible for the activity that happens on or through your account. So, keep your
                            password confidential.
                        </li>
                        <li>We may modify these terms or any additional terms that apply to a Service to, for example,
                            reflect changes to the law or changes to our Services. You should look at the terms
                            regularly.
                        </li>
                    </ul>
                    <p>We may modify these terms or add any additional terms to any service. You should follow the terms
                        regularly.</p>

                    <br/>
                    <h5><strong>What is Existing Organization?</strong></h5>
                    <p>If you select Organization type as "Existing", your registration information will be sent to the
                        corresponding organization for approval.</p>
                    <br/>

                    <h5><strong>What is an Authorization Letter?</strong></h5>
                    <p>If anyone wants to work on behalf of an organization, the company's managing director / chief of
                        the company will sanction a consent letter printed on a Letter Head pad of the respective
                        company.</p>
                </div>

                <div class="clearfix"></div>

                {!! Form::close() !!}

                <div class="clearfix"></div>
            </div>
        </div>
    </div>
@endsection



@section('footer-script')
    @include('partials.image-resize')
    <script src="{{ asset("assets/plugins/toastr.min.js") }}" type="text/javascript"></script>
    <script src="{{ asset("assets/scripts/select2.full.js") }}" type="text/javascript"></script>
    <script>

        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip();

            $("#company_id").select2({
                matcher: matchCustom,
                minimumInputLength: 3,
                language: {
                    inputTooShort: function() {
                        return 'Please search by entering 03 or more characters.';
                    }
                },
                tags: false,
            });

            $("#registrationForm").validate({
                errorPlacement: function () {
                    return false;
                }
            });

            $('.company_type').click(function (e) {
                if (this.value == '1') { // 1 for old
                    $('#existing_company_div').removeClass('hidden');
                    $('#company_id').addClass('required');
                    $("#company_id").val('').trigger('change');

                    $('#business_category_div').addClass('hidden');
                    $('.business_category').removeClass('required');

                    $('#new_company_div').addClass('hidden');
                    $('#company_name_en').removeClass('required');
                    // $('#company_name_bn').removeClass('required');
                } else if (this.value == '2') { // 2 is for new
                    $('#business_category_div').removeClass('hidden');
                    $('.business_category').addClass('required');

                    $('#new_company_div').removeClass('hidden');
                    $('#company_name_en').addClass('required');
                    $('#company_name_en').val('');
                    // $('#company_name_bn').addClass('required');
                    $('#company_name_bn').val('');

                    $('#existing_company_div').addClass('hidden');
                    $('#company_id').removeClass('required');
                }
            });
            //$('.company_type').trigger('click');

            // trigger function for Business Category wise office info hide/show
            businessCategoryCheck($(".business_category:checked").val());

            var division_id = document.getElementById('division_id');
            if (division_id) {
                // division_id.dispatchEvent(new Event('change'));
            }

            var org_type = document.querySelector('input[name="company_type"]:checked');
            if (org_type) {
                org_type.dispatchEvent(new Event('click'));
            }
        });

        // Business Category wise office info hide/show
        function businessCategoryCheck(val) {
            if (val == '1') { // 1 for private
                $('#address_info_div').removeClass('hidden');
                $('#address_info_div').find(':input').addClass('required');

            } else { // 2 is for government
                $('#address_info_div').addClass('hidden');
                $('#address_info_div').find(':input').removeClass('required');
                $('#address_info_div').find(':input').val('');
            }
        }

        $(window).on('load', function () {
            // $('#district_id').trigger('change');
        });

        function matchCustom(params, data) {
            $('.select2-results').css('display', 'block');

            // If there are no search terms
            if ($.trim(params.term) === '') {
                $('.select2-results').css('display', 'none');
                return data;
            }

            // Do not display the item if there is no 'text' property
            if (typeof data.text === 'undefined') {
                return null;
            }

            if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                var modifiedData = $.extend({}, data, true);
                //modifiedData.text += ' (matched)';

                return modifiedData;
            }
            // Return `null` if the term should not be displayed
            return null;
        }

        function imageDisplay(input, imagePreviewID, base64InputId = '', requiredSize = 0) {
            if (input.files && input.files[0]) {
                var mime_type = input.files[0].type;
                if (!(mime_type == 'image/jpeg' || mime_type == 'image/jpg' || mime_type == 'image/png')) {
                    alert("Image format is not valid. Please upload in jpg,jpeg or png format");
                    $('#' + imagePreviewID).attr('src', '{{url('assets/images/photo_default.png')}}');
                    $(input).val('').addClass('btn-danger').removeClass('btn-primary');
                    return false;
                } else {
                    $(input).addClass('btn-primary').removeClass('btn-danger');
                }
                var reader = new FileReader();
                reader.onload = function (e) {
                    //$('#'+imagePreviewID).attr('src', e.target.result);

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
                                    alert("Image size must be " + requiredSize);
                                    $('#' + imagePreviewID).attr('src', '{{url('assets/images/photo_default.png')}}');
                                    $(input).val('').addClass('btn-danger').removeClass('btn-primary');
                                    return false;
                                } else {
                                    if (base64InputId) {
                                        $('#' + base64InputId).val(e.target.result);
                                    }
                                    $('#' + imagePreviewID).attr('src', e.target.result);
                                }
                            }
                        } else {
                            alert('Error in image required size!');
                        }
                    }
                    // if image height and width is not defined , means any size will be uploaded
                    else {
                        $('#' + imagePreviewID).attr('src', e.target.result);
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

    <script src="{{ asset("build/js/intlTelInput-jquery_v16.0.8.min.js") }}" type="text/javascript"></script>
    <script src="{{ asset("build/js/utils_v16.0.8.js") }}" type="text/javascript"></script>
    <script>
        $(function () {
            $("#user_phone").intlTelInput({
                hiddenInput: "user_phone",
                initialCountry: "BD",
                placeholderNumberType: "MOBILE",
                separateDialCode: true
            });

            $("#user_phone").css({
                "padding-left": '89px'
            });
        });
    </script>
@endsection
