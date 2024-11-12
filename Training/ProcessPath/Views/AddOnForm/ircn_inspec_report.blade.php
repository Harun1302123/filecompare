<style>
    .form-group{
        margin-bottom: 2px;
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
                            <h5><strong>Inspection Report Submission</strong></h5>
                        </div>
                    </div>

                    <div class="panel-body">
                        {!! Form::hidden('app_id', Encryption::encodeId($appInfo->id) ,['class' => 'form-control input-md ', 'id'=>'app_id']) !!}
                        {{--                        1 for IRC 1st adhoc--}}
                        {!! Form::hidden('app_type', 1,['class' => 'form-control input-md ', 'id'=>'']) !!}
                        {!! Form::hidden('irc_purpose_id', $appInfo->irc_purpose_id ,['class' => 'form-control input-md ', 'id'=>'']) !!}
                        {!! Form::hidden('annual_production_start_date', $appInfo->annual_production_start_date ,['class' => 'form-control input-md ', 'id'=>'annual_production_start_date']) !!}

                        <div class="row">
                            <div class="col-md-12">
                                <ol class="breadcrumb">
                                    <li><strong>Tracking no. : </strong> {{ $appInfo->tracking_no }}</li>
                                    <li><strong>Date of Submission: </strong> {{ \App\Libraries\CommonFunction::formateDate($appInfo->submitted_at) }}</li>
                                    <li><strong>Current Status : </strong> {{ $appInfo->status_name }}</li>
                                    <li><strong>Current Desk :</strong> {{ $appInfo->desk_id != 0 ? \App\Libraries\CommonFunction::getDeskName($appInfo->desk_id) : 'Applicant' }}</li>
                                </ol>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        {!! Form::label('inspection_report_date','পরিদর্শনের তারিখ', ['class'=>'col-md-5']) !!}
                                        <div class="col-md-7">
                                            <div class="datetimeicker input-group date" data-date-format="dd-mm-yyyy">
                                                {!! Form::text('inspection_report_date', '', ['class'=>'form-control input-md required', 'id' => '', 'placeholder'=>'Pick from datepicker']) !!}
                                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                            </div>
                                            {!! $errors->first('inspection_report_date','<span class="help-block">:message</span>') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">১.প্রকল্পের তথ্য</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12 {{$errors->has('company_name') ? 'has-error': ''}}">
                                            {!! Form::label('company_name','শিল্প প্রকল্পের নাম ',['class'=>'col-md-3']) !!}
                                            <div class="col-md-9">
                                                {!! Form::text('company_name', $appInfo->company_name, ['class'=>'form-control input-md', 'id'=>"organization_name"]) !!}
                                                {!! $errors->first('company_name','<span class="help-block">:message</span>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12 {{$errors->has('office_address') ? 'has-error': ''}}">
                                            {!! Form::label('office_address','অফিসের ঠিকানা ',['class'=>'col-md-3']) !!}
                                            <div class="col-md-9">
                                                {!! Form::textarea('office_address', $appInfo->office_address,['class' => 'form-control input-md', 'size'=>'1x2', 'id' => '', 'data-charcount-maxlength'=>'200']) !!}
                                                {!! $errors->first('office_address','<span class="help-block">:message</span>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12 {{$errors->has('factory_address') ? 'has-error': ''}}">
                                            {!! Form::label('factory_address','কারখানার ঠিকানা ',['class'=>'col-md-3']) !!}
                                            <div class="col-md-9">
                                                {!! Form::textarea('factory_address', $appInfo->factory_address,['class' => 'form-control input-md', 'size'=>'1x2', 'id' => '', 'data-charcount-maxlength'=>'200']) !!}
                                                {!! $errors->first('factory_address','<span class="help-block">:message</span>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">২. শিল্প খাতের তথ্য</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12 {{$errors->has('industrial_sector') ? 'has-error': ''}}">
                                            {!! Form::label('industrial_sector','শিল্প খাত',['class'=>'text-left col-md-3']) !!}
                                            <div class="col-md-9">
                                                {!! Form::text('industrial_sector', $appInfo->industrial_sector, ['class'=>'form-control input-md', 'id'=>"industrial_sector"]) !!}
                                                {!! $errors->first('industrial_sector','<span class="help-block">:message</span>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">৩. বিনিয়োগের তথ্য</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12 {{$errors->has('organization_status_id') ? 'has-error': ''}}">
                                            {!! Form::label('organization_status_id','বিনিয়োগের প্রকৃতি ',['class'=>'text-left col-md-3']) !!}
                                            <div class="col-md-6">
                                                {!! Form::select('organization_status_id', $eaOrganizationStatus, $appInfo->organization_status_id, ['class'=>'form-control input-md', 'id'=>"investment_nature"]) !!}
                                                {!! $errors->first('organization_status_id','<span class="help-block">:message</span>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">৪. উদ্যোক্তার তথ্য</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 {{$errors->has('entrepreneur_name') ? 'has-error': ''}}">
                                            {!! Form::label('entrepreneur_name','উদ্যোক্তার নাম',['class'=>'text-left col-md-4']) !!}
                                            <div class="col-md-8">
                                                {!! Form::text('entrepreneur_name',$appInfo->ceo_full_name, ['class'=>'form-control input-md', 'id'=>"entrepreneur_name_and_address"]) !!}
                                                {!! $errors->first('entrepreneur_name','<span class="help-block">:message</span>') !!}
                                            </div>
                                        </div>

                                        <div class="col-md-6 {{$errors->has('entrepreneur_address') ? 'has-error': ''}}">
                                            {!! Form::label('entrepreneur_address','ঠিকানা',['class'=>'text-left col-md-4']) !!}
                                            <div class="col-md-8">
                                                {!! Form::text('entrepreneur_address',$appInfo->ceo_address, ['class'=>'form-control input-md', 'id'=>"entrepreneur_name_and_address"]) !!}
                                                {!! $errors->first('entrepreneur_address','<span class="help-block">:message</span>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">৫. নিবন্ধনকারীর তথ্য</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 {{$errors->has('registering_authority_name') ? 'has-error': ''}}">
                                            {!! Form::label('registering_authority_name','কতৃপক্ষের নাম',['class'=>'text-left col-md-4']) !!}
                                            <div class="col-md-8">
                                                {!! Form::text('registering_authority_name','Bangladesh Investment Development Authority', ['class'=>'form-control input-md', 'id'=>"registering_authority_name"]) !!}
                                                {!! $errors->first('registering_authority_name','<span class="help-block">:message</span>') !!}
                                            </div>
                                        </div>

                                        <div class="col-md-6 {{$errors->has('registering_authority_memo_no') ? 'has-error': ''}}">
                                            {!! Form::label('registering_authority_memo_no','স্মারক নং',['class'=>'text-left col-md-4']) !!}
                                            <div class="col-md-8">
                                                {!! Form::text('registering_authority_memo_no', $appInfo->ref_app_tracking_no, ['class'=>'form-control input-md', 'id'=>"registering_authority_memo_no"]) !!}
                                                {!! $errors->first('registering_authority_memo_no','<span class="help-block">:message</span>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 {{$errors->has('reg_no') ? 'has-error': ''}}">
                                            {!! Form::label('reg_no','নিবন্ধন  নম্বর',['class'=>'text-left col-md-4']) !!}
                                            <div class="col-md-8">
                                                {!! Form::text('reg_no', $appInfo->reg_no, ['class'=>'form-control input-md', 'id'=>"number_of_registration"]) !!}
                                                {!! $errors->first('reg_no','<span class="help-block">:message</span>') !!}
                                            </div>
                                        </div>

                                        <div class="col-md-6 {{$errors->has('date_of_registration') ? 'has-error': ''}}">
                                            {!! Form::label('date_of_registration','নিবন্ধনের তারিখ',['class'=>'text-left col-md-4']) !!}
                                            <div class="col-md-8">
                                                <div class="datepicker input-group date" data-date-format="dd-mm-yyyy">
                                                    {!! Form::text('date_of_registration', (empty($appInfo->ref_app_approve_date) ? '' : date('d-M-Y', strtotime($appInfo->ref_app_approve_date))), ['class'=>'form-control input-md', 'id' => 'date_of_registration', 'placeholder'=>'Pick from datepicker']) !!}
                                                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                                </div>
                                                {!! $errors->first('date_of_registration','<span class="help-block">:message</span>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">৬. বিবিধ রেজিস্ট্রেশন নং</div>
                            <div class="panel-body">
                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">(ক) ট্রেড লাইসেন্স</legend>
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-6 {{$errors->has('trade_licence_num') ? 'has-error': ''}}">
                                                {!! Form::label('trade_licence_num','ট্রেড লাইসেন্স নম্বর :',['class'=>'text-left col-md-5']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('trade_licence_num', $appInfo->trade_licence_num, ['class' => 'form-control input-md']) !!}
                                                    {!! $errors->first('trade_licence_num','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('trade_licence_issuing_authority') ? 'has-error': ''}}">
                                                {!! Form::label('trade_licence_issuing_authority','ইস্যুয়িং অথরিটি :',['class'=>'col-md-5 text-left ']) !!}
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
                                                {!! Form::label('trade_licence_issue_date','ইস্যুয়িং ডেট :',['class'=>'text-left col-md-5']) !!}
                                                <div class="col-md-7">
                                                    <div class="datepicker input-group date" data-date-format="dd-mm-yyyy">
                                                        {!! Form::text('trade_licence_issue_date', (empty($appInfo->trade_licence_issue_date) ? '' : date('d-M-Y', strtotime($appInfo->trade_licence_issue_date))), ['class'=>'form-control input-md', 'id' => 'trade_licence_issue_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                                    </div>
                                                    {!! $errors->first('trade_licence_issue_date','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('trade_licence_validity_period') ? 'has-error': ''}}">
                                                {!! Form::label('trade_licence_validity_period','মেয়াদ উত্তীর্ণ সময়কাল :',['class'=>'col-md-5 text-left ']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('trade_licence_validity_period', $appInfo->trade_licence_validity_period, ['class' => 'form-control input-md']) !!}
                                                    {!! $errors->first('trade_licence_validity_period','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">(খ) টি  আই  এন  নং</legend>
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-6 {{$errors->has('tin_number') ? 'has-error': ''}}">
                                                {!! Form::label('tin_number','টি আইএন নম্বর :',['class'=>'text-left col-md-5']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('tin_number', $appInfo->tin_number, ['class' => 'form-control input-md']) !!}
                                                    {!! $errors->first('tin_number','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('tin_issuing_authority') ? 'has-error': ''}}">
                                                {!! Form::label('tin_issuing_authority','ইস্যুয়িং অথরিটি :',['class'=>'col-md-5 text-left ']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('tin_issuing_authority', $appInfo->tin_issuing_authority, ['class' => 'form-control input-md']) !!}
                                                    {!! $errors->first('tin_issuing_authority','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">(গ) ব্যাংক প্রত্যয়ন পত্র</legend>

                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-6 {{$errors->has('bank_id') ? 'has-error': ''}}">
                                                {!! Form::label('bank_id','ব্যাংকের নাম',['class'=>'text-left col-md-5']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::select('bank_id', $banks, $appInfo->bank_id,['class'=>'form-control input-md', 'id' => 'bank_id', 'onchange'=>"getBranchByBankId('bank_id', this.value, 'branch_id', ".(!empty($appInfo->branch_id) ? $appInfo->branch_id:'').")"]) !!}
                                                    {!! $errors->first('bank_id','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('branch_id') ? 'has-error': ''}}">
                                                {!! Form::label('branch_id','ব্যাংক শাখার নাম',['class'=>'col-md-5 text-left ']) !!}
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
                                                {!! Form::label('bank_account_number','হিসাব নম্বর :',['class'=>'text-left col-md-5']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('bank_account_number', $appInfo->bank_account_number, ['class' => 'form-control input-md']) !!}
                                                    {!! $errors->first('bank_account_number','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('bank_address') ? 'has-error': ''}}">
                                                {!! Form::label('bank_address','ব্যাংকের ঠিকানা :',['class'=>'col-md-5 text-left ']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('bank_address', $appInfo->bank_address, ['class' => 'form-control input-md']) !!}
                                                    {!! $errors->first('bank_address','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-6 form-group {{$errors->has('bank_account_title') ? 'has-error': ''}}">
                                                {!! Form::label('bank_account_title','একাউন্ট নাম :',['class'=>'col-md-5 text-left ']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('bank_account_title', $appInfo->bank_account_title, ['class' => 'form-control input-md']) !!}
                                                    {!! $errors->first('bank_account_title','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">(ঘ) মেম্বারশিপ অফ চেম্বার / এসোসিয়েশন ইনফরমেশন</legend>
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-6 {{$errors->has('assoc_membership_number') ? 'has-error': ''}}">
                                                {!! Form::label('assoc_membership_number','সদস্য নম্বর :',['class'=>'text-left col-md-5']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('assoc_membership_number', $appInfo->assoc_membership_number, ['class' => 'form-control input-md']) !!}
                                                    {!! $errors->first('assoc_membership_number','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('assoc_chamber_name') ? 'has-error': ''}}">
                                                {!! Form::label('assoc_chamber_name','চেম্বার নাম :',['class'=>'col-md-5 text-left ']) !!}
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
                                                {!! Form::label('assoc_issuing_date','ইস্যুয়িং ডেট :',['class'=>'text-left col-md-5']) !!}
                                                <div class="col-md-7">
                                                    <div class="datepicker input-group date" data-date-format="dd-mm-yyyy">
                                                        {!! Form::text('assoc_issuing_date', (empty($appInfo->assoc_issuing_date) ? '' : date('d-M-Y', strtotime($appInfo->assoc_issuing_date))), ['class'=>'form-control input-md', 'id' => 'association_issuing_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                                    </div>
                                                    {!! $errors->first('assoc_issuing_date','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('assoc_expire_date') ? 'has-error': ''}}">
                                                {!! Form::label('assoc_expire_date','মেয়াদ উত্তীর্ণ তারিখ :',['class'=>'col-md-5 text-left ']) !!}
                                                <div class="col-md-7">
                                                    <div class="datepicker input-group date" data-date-format="dd-mm-yyyy">
                                                        {!! Form::text('assoc_expire_date', (empty($appInfo->assoc_expire_date) ? '' : date('d-M-Y', strtotime($appInfo->assoc_expire_date))), ['class'=>'form-control input-md', 'id' => 'association_expire_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                                    </div>
                                                    {!! $errors->first('assoc_expire_date','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">(ঙ) ফায়ার লাইসেন্স নং</legend>
                                    <input type="hidden" name="fire_license_info" value="{{ $appInfo->fire_license_info }}">
                                    <div class="row">
                                        @if($appInfo->fire_license_info == 'already_have')
                                            <div class="form-group">
                                                <div class="col-md-6 {{$errors->has('fl_number') ? 'has-error': ''}}">
                                                    {!! Form::label('fl_number','ফায়ার লাইসেন্স নম্বর :',['class'=>'text-left col-md-5']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('fl_number', $appInfo->fl_number, ['class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('fl_number','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>

                                                <div class="col-md-6 form-group {{$errors->has('fl_expire_date') ? 'has-error': ''}}">
                                                    {!! Form::label('fl_expire_date','মেয়াদ উত্তীর্ণ তারিখ :',['class'=>'col-md-5 text-left ']) !!}
                                                    <div class="col-md-7">
                                                        <div class="datepicker input-group date" data-date-format="dd-mm-yyyy">
                                                            {!! Form::text('fl_expire_date', (empty($appInfo->fl_expire_date) ? '' : date('d-M-Y', strtotime($appInfo->fl_expire_date))), ['class'=>'form-control input-md', 'id' => 'fl_expire_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                                        </div>
                                                        {!! $errors->first('fl_expire_date','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if($appInfo->fire_license_info == 'applied_for')
                                            <div class="form-group">
                                                <div class="col-md-6 {{$errors->has('fl_number') ? 'has-error': ''}}">
                                                    {!! Form::label('fl_number','ফায়ার লাইসেন্স নম্বর :',['class'=>'text-left col-md-5']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('fl_number', $appInfo->fl_number, ['class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('fl_number','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>

                                                <div class="col-md-6 form-group {{$errors->has('fl_expire_date') ? 'has-error': ''}}">
                                                    {!! Form::label('fl_expire_date','মেয়াদ উত্তীর্ণ তারিখ :',['class'=>'col-md-5 text-left ']) !!}
                                                    <div class="col-md-7">
                                                        <div class="datepicker input-group date" data-date-format="dd-mm-yyyy">
                                                            {!! Form::text('fl_expire_date', (empty($appInfo->fl_expire_date) ? '' : date('d-M-Y', strtotime($appInfo->fl_expire_date))), ['class'=>'form-control input-md', 'id' => 'fl_expire_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                                        </div>
                                                        {!! $errors->first('fl_expire_date','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-6 {{$errors->has('fl_application_number') ? 'has-error': ''}}">
                                                    {!! Form::label('fl_application_number','আবেদন নম্বর :',['class'=>'text-left col-md-5']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('fl_application_number', $appInfo->fl_application_number, ['class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('fl_application_number','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>

                                                <div class="col-md-6 form-group {{$errors->has('fl_apply_date') ? 'has-error': ''}}">
                                                    {!! Form::label('fl_apply_date','আবেদনের তারিখ :',['class'=>'col-md-5 text-left ']) !!}
                                                    <div class="col-md-7">
                                                        <div class="datepicker input-group date" data-date-format="dd-mm-yyyy">
                                                            {!! Form::text('fl_apply_date', (empty($appInfo->fl_apply_date) ? '' : date('d-M-Y', strtotime($appInfo->fl_apply_date))), ['class'=>'form-control input-md', 'id' => 'fl_apply_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                                        </div>
                                                        {!! $errors->first('fl_apply_date','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    </div>

                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-6 form-group {{$errors->has('fl_issuing_authority') ? 'has-error': ''}}">
                                                {!! Form::label('fl_issuing_authority','ইস্যুয়িং অথরিটি :',['class'=>'col-md-5 text-left ']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('fl_issuing_authority', $appInfo->fl_issuing_authority, ['class' => 'form-control input-md']) !!}
                                                    {!! $errors->first('fl_issuing_authority','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">(চ) ইনকর্পোরেশন</legend>
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-6 {{$errors->has('inc_number') ? 'has-error': ''}}">
                                                {!! Form::label('inc_number','ইনকর্পোরেশন নম্বর :',['class'=>'text-left col-md-5']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('inc_number', $appInfo->inc_number, ['class' => 'form-control input-md']) !!}
                                                    {!! $errors->first('inc_number','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group {{$errors->has('inc_issuing_authority') ? 'has-error': ''}}">
                                                {!! Form::label('inc_issuing_authority','ইস্যুয়িং অথরিটি :',['class'=>'col-md-5 text-left ']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('inc_issuing_authority', $appInfo->inc_issuing_authority, ['class' => 'form-control input-md']) !!}
                                                    {!! $errors->first('inc_issuing_authority','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">(ছ) পরিবেশ ছাড়পত্র</legend>
                                    <input type="hidden" name="environment_clearance" value="{{ $appInfo->environment_clearance }}">
                                    <div class="row">
                                        @if($appInfo->environment_clearance == 'already_have')
                                            <div class="form-group">
                                                <div class="col-md-6 {{$errors->has('el_number') ? 'has-error': ''}}">
                                                    {!! Form::label('el_number','পরিবেশ ছাড়পত্র নম্বর :',['class'=>'text-left col-md-5']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('el_number', $appInfo->el_number, ['class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('el_number','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>

                                                <div class="col-md-6 form-group {{$errors->has('el_expire_date') ? 'has-error': ''}}">
                                                    {!! Form::label('el_expire_date','মেয়াদ উত্তীর্ণ তারিখ :',['class'=>'col-md-5 text-left ']) !!}
                                                    <div class="col-md-7">
                                                        <div class="datepicker input-group date" data-date-format="dd-mm-yyyy">
                                                            {!! Form::text('el_expire_date', (empty($appInfo->el_expire_date) ? '' : date('d-M-Y', strtotime($appInfo->el_expire_date))), ['class'=>'form-control input-md', 'id' => 'environmental_clearance_expire_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                                        </div>
                                                        {!! $errors->first('el_expire_date','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if($appInfo->environment_clearance == 'applied_for')
                                            <div class="form-group">
                                                <div class="col-md-6 {{$errors->has('el_number') ? 'has-error': ''}}">
                                                    {!! Form::label('el_number','পরিবেশ ছাড়পত্র নম্বর :',['class'=>'text-left col-md-5']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('el_number', $appInfo->el_number, ['class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('el_number','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>

                                                <div class="col-md-6 form-group {{$errors->has('el_expire_date') ? 'has-error': ''}}">
                                                    {!! Form::label('el_expire_date','মেয়াদ উত্তীর্ণ তারিখ :',['class'=>'col-md-5 text-left ']) !!}
                                                    <div class="col-md-7">
                                                        <div class="datepicker input-group date" data-date-format="dd-mm-yyyy">
                                                            {!! Form::text('el_expire_date', (empty($appInfo->el_expire_date) ? '' : date('d-M-Y', strtotime($appInfo->el_expire_date))), ['class'=>'form-control input-md', 'id' => 'environmental_clearance_expire_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                                        </div>
                                                        {!! $errors->first('el_expire_date','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-6 {{$errors->has('el_application_number') ? 'has-error': ''}}">
                                                    {!! Form::label('el_application_number','আবেদন নম্বর :',['class'=>'text-left col-md-5']) !!}
                                                    <div class="col-md-7">
                                                        {!! Form::text('el_application_number', $appInfo->el_application_number, ['class' => 'form-control input-md']) !!}
                                                        {!! $errors->first('el_application_number','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>

                                                <div class="col-md-6 form-group {{$errors->has('el_apply_date') ? 'has-error': ''}}">
                                                    {!! Form::label('el_apply_date','আবেদনের তারিখ :',['class'=>'col-md-5 text-left ']) !!}
                                                    <div class="col-md-7">
                                                        <div class="datepicker input-group date" data-date-format="dd-mm-yyyy">
                                                            {!! Form::text('el_apply_date', (empty($appInfo->el_apply_date) ? '' : date('d-M-Y', strtotime($appInfo->el_apply_date))), ['class'=>'form-control input-md', 'id' => 'el_apply_date', 'placeholder'=>'Pick from datepicker']) !!}
                                                            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                                        </div>
                                                        {!! $errors->first('el_apply_date','<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-6 form-group {{$errors->has('el_issuing_authority') ? 'has-error': ''}}">
                                                {!! Form::label('el_issuing_authority','ইস্যুয়িং অথরিটি :',['class'=>'col-md-5 text-left ']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('el_issuing_authority', $appInfo->el_issuing_authority, ['class' => 'form-control input-md']) !!}
                                                    {!! $errors->first('el_issuing_authority','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">৭. প্রকল্পের অবস্থান</div>
                            <div class="panel panel-body">
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-md-6 {{$errors->has('project_status_id') ? 'has-error': ''}}">
                                            {!! Form::label('project_status_id','প্রকল্পের অবস্থান',['class'=>'text-left col-md-5']) !!}
                                            <div class="col-md-7">
                                                {!! Form::select('project_status_id', $projectStatusList, $appInfo->project_status_id,['class' => 'form-control input-md', 'id'=>'project_status_id', 'onchange'=>'loadOtherStatus()']) !!}
                                                {!! $errors->first('project_status_id','<span class="help-block">:message</span>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group {{$errors->has('other_details') ? 'has-error': ''}}" id="other_details" style="display: none">
                                            {!! Form::label('other_details','অন্যান্য',['class'=>'col-md-5 text-left ']) !!}
                                            <div class="col-md-7">
                                                {!! Form::text('other_details', '', ['class' => 'form-control input-md']) !!}
                                                {!! $errors->first('other_details','<span class="help-block">:message</span>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">৮. বিনিয়োজিত মূলধন</div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" cellspacing="0" width="100%">
                                        <tbody id="annual_production_capacity">
                                        <tr>
                                            <td>
                                                <div style="position: relative;">
                                                    <span class="helpTextCom" id="investment_land_label">&nbsp; জমি </span>
                                                </div>
                                            </td>
                                            <td>
                                                <table style="width:100%;">
                                                    <tr>
                                                        <td style="width:75%;">
                                                            {!! Form::number('local_land_ivst', $appInfo->local_land_ivst, ['data-rule-maxlength'=>'40','class' => 'form-control total_investment_item input-md number','id'=>'local_land_ivst',
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
                                                                      id="investment_building_label">&nbsp; ভবন</span>
                                                </div>
                                            </td>
                                            <td>
                                                <table style="width:100%;">
                                                    <tr>
                                                        <td style="width:75%;">
                                                            {!! Form::number('local_building_ivst', $appInfo->local_building_ivst, ['data-rule-maxlength'=>'40','class' => 'form-control input-md total_investment_item number','id'=>'local_building_ivst',
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
                                                                      id="investment_machinery_equp_label">&nbsp; যন্ত্রপাতি ও সরঞ্জামাদি <small>(মিলিয়ন)</small></span>
                                                </div>
                                            </td>
                                            <td>
                                                <table style="width:100%;">
                                                    <tr>
                                                        <td style="width:75%;">
                                                            {!! Form::number('local_machinery_ivst', $appInfo->local_machinery_ivst, ['data-rule-maxlength'=>'40','class' => 'form-control  input-md number total_investment_item','id'=>'local_machinery_ivst',
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
                                                    <span class="helpTextCom" id="investment_others_label">&nbsp; অন্যান্য <small>(মিলিয়ন)</small></span>
                                                </div>
                                            </td>
                                            <td>
                                                <table style="width:100%;">
                                                    <tr>
                                                        <td style="width:75%;">
                                                            {!! Form::number('local_others_ivst', $appInfo->local_others_ivst, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number total_investment_item','id'=>'local_others_ivst',
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
                                            <td>
                                                <div style="position: relative;">
                                                                <span class="helpTextCom"
                                                                      id="investment_working_capital_label">&nbsp; চলতি মূলধন <small>(মিলিয়ন)</small></span>
                                                </div>
                                            </td>
                                            <td>
                                                <table style="width:100%;">
                                                    <tr>
                                                        <td style="width:75%;">
                                                            {!! Form::number('local_wc_ivst', $appInfo->local_wc_ivst, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number total_investment_item','id'=>'local_wc_ivst',
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
                                                                      id="investment_total_invst_mi_label">&nbsp; মোট মূলধন<small>(মিলিয়ন) (টাকা)</small></span>
                                                </div>
                                            </td>
                                            <td colspan="3">
                                                {!! Form::number('total_fixed_ivst_million', $appInfo->total_fixed_ivst_million, ['data-rule-maxlength'=>'40','class' => 'form-control input-md numberNoNegative total_fixed_ivst_million ','id'=>'total_fixed_ivst_million','readonly']) !!}
                                                {!! $errors->first('total_fixed_ivst_million','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div style="position: relative;">
                                                                <span class="helpTextCom"
                                                                      id="investment_total_invst_bd_label">&nbsp; মোট মূলধন <small>(টাকা)</small></span>
                                                </div>
                                            </td>
                                            <td colspan="3">
                                                {!! Form::number('total_fixed_ivst', $appInfo->total_fixed_ivst, ['data-rule-maxlength'=>'40','class' => 'form-control input-md numberNoNegative total_invt_bdt ','id'=>'total_invt_bdt','readonly']) !!}
                                                {!! $errors->first('total_fixed_ivst','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div style="position: relative;">
                                                                <span class="helpTextCom required-star"
                                                                      id="investment_total_invst_usd_label">&nbsp; ডলার এক্সচেঞ্জ রেট (USD)</span>
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
                                                                      id="investment_total_fee_bd_label">&nbsp; টোটাল ফি <small>(টাকা)</small></span>
                                                </div>
                                            </td>
                                            <td>
                                                {!! Form::text('total_fee', $appInfo->total_fee, ['class' => 'form-control input-md number', 'id'=>'total_fee', 'readonly']) !!}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">৯. স্থাপিত যন্ত্রপাতির বিবরণ</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-md-6 {{$errors->has('em_local_total_taka_mil') ? 'has-error': ''}}">
                                            {!! Form::label('em_local_total_taka_mil','স্থানীয় ভাবে সংগৃহীত (মিলিয়ন) (টাকা)',['class'=>'text-left col-md-5']) !!}
                                            <div class="col-md-7">
                                                {!! Form::text('em_local_total_taka_mil', $appInfo->em_local_total_taka_mil, ['class' => 'form-control input-md']) !!}
                                                {!! $errors->first('em_local_total_taka_mil','<span class="help-block">:message</span>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group {{$errors->has('em_lc_total_taka_mil') ? 'has-error': ''}}">
                                            {!! Form::label('em_lc_total_taka_mil','এলসিকৃত (মিলিয়ন) (টাকা)',['class'=>'col-md-5 text-left ']) !!}
                                            <div class="col-md-7">
                                                {!! Form::text('em_lc_total_taka_mil', $appInfo->em_lc_total_taka_mil, ['class' => 'form-control input-md']) !!}
                                                {!! $errors->first('em_lc_total_taka_mil','<span class="help-block">:message</span>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">১০. জনবল</div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" cellspacing="0" width="100%">
                                        <tbody id="manpower" class="alert alert-info">
                                        <tr>
                                            <th colspan="3">বাংলাদেশী</th>
                                            <th colspan="3">বিদেশী</th>
                                            <th colspan="1">সর্বমোট</th>
                                            <th colspan="2">অনুপাত</th>
                                        </tr>
                                        <tr>
                                            <th>কার্যনির্বাহী</th>
                                            <th>সাপোর্টিং</th>
                                            <th>মোট (a)</th>
                                            <th>কার্যনির্বাহী</th>
                                            <th>সাপোর্টিং</th>
                                            <th>মোট (b)</th>
                                            <th> (a+b)</th>
                                            <th>স্থানীয়</th>
                                            <th>বিদেশী</th>
                                        </tr>
                                        <tr>
                                            <td>
                                                {!! Form::text('local_male', $appInfo->local_male, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number','id'=>'local_male']) !!}
                                                {!! $errors->first('local_male','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td>
                                                {!! Form::text('local_female', $appInfo->local_female, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number','id'=>'local_female']) !!}
                                                {!! $errors->first('local_female','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td>
                                                {!! Form::text('local_total', $appInfo->local_total, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field numberNoNegative','id'=>'local_total','readonly']) !!}
                                                {!! $errors->first('local_total','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td>
                                                {!! Form::text('foreign_male', $appInfo->foreign_male, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number','id'=>'foreign_male']) !!}
                                                {!! $errors->first('foreign_male','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td>
                                                {!! Form::text('foreign_female', $appInfo->foreign_female, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number','id'=>'foreign_female']) !!}
                                                {!! $errors->first('foreign_female','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td>
                                                {!! Form::text('foreign_total', $appInfo->foreign_total, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field numberNoNegative','id'=>'foreign_total','readonly']) !!}
                                                {!! $errors->first('foreign_total','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td>
                                                {!! Form::text('manpower_total', $appInfo->manpower_total, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number','id'=>'mp_total','readonly']) !!}
                                                {!! $errors->first('manpower_total','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td>
                                                {!! Form::text('manpower_local_ratio', $appInfo->manpower_local_ratio, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number','id'=>'mp_ratio_local','readonly']) !!}
                                                {!! $errors->first('manpower_local_ratio','<span class="help-block">:message</span>') !!}
                                            </td>
                                            <td>
                                                {!! Form::text('manpower_foreign_ratio', $appInfo->manpower_foreign_ratio, ['data-rule-maxlength'=>'40','class' => 'form-control input-md mp_req_field number','id'=>'mp_ratio_foreign','readonly']) !!}
                                                {!! $errors->first('manpower_foreign_ratio','<span class="help-block">:message</span>') !!}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">১১. নিবন্ধপত্র / নিবন্ধনপত্রে সংশোধী অনুযায়ী বার্ষিক উৎপাদন ক্ষমতা</div>
                            <div class="panel-body">

                                <br>
                                {{-- @if($appInfo->irc_purpose_id != 2) --}}
                                @if(in_array($appInfo->irc_purpose_id, [1,3]) && count($annualProductionCapacity) > 0)  {{-- 1 = Raw materials , 3 = Both --}}
                                <table class="table table-bordered dt-responsive" cellspacing="0" width="100%">
                                    <thead class="alert alert-info">
                                    <tr>
                                        <th class="text-center">ক্রমিক নং</th>
                                        <th class="text-center">পন্য/ সেবার নাম</th>
                                        <th colspan="2" class="text-center">নির্ধারিত বার্ষিক উৎপাদন ক্ষমতা</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $count = 1;?>
                                    @foreach($annualProductionCapacity as $apc)
                                        <tr>
                                            <td><?php echo $count++ ?></td>
                                            <td>
                                                {!! Form::text("", $apc->product_name, ['class' => 'form-control input-md', 'readonly']) !!}
                                            </td>
                                            <td>
                                                {!! Form::text("", $apc->quantity, ['class' => 'form-control input-md', 'readonly']) !!}
                                            </td>
                                            <td>
                                                {!! Form::text("", $apc->unit_name, ['class' => 'form-control input-md', 'readonly']) !!}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                @endif

                                {{-- @if($appInfo->irc_purpose_id != 1) --}}
                                @if($appInfo->irc_purpose_id == 2 && count($annualProductionSpareParts) > 0)
                                    <table class="table table-bordered dt-responsive" cellspacing="0" width="100%">
                                        <thead class="alert alert-info">
                                        <tr>
                                            <th class="text-center">ক্রমিক নং</th>
                                            <th class="text-center">পন্য/ সেবার নাম</th>
                                            <th colspan="2" class="text-center">নির্ধারিত বার্ষিক উৎপাদন ক্ষমতা</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $count = 1;?>
                                        @foreach($annualProductionSpareParts as $apsp)
                                            <tr>
                                                <td><?php echo $count++ ?></td>
                                                <td>
                                                    {!! Form::text("", $apsp->product_name, ['class' => 'form-control input-md', 'readonly']) !!}
                                                </td>
                                                <td>
                                                    {!! Form::text("", $apsp->quantity, ['class' => 'form-control input-md', 'readonly']) !!}
                                                </td>
                                                <td>
                                                    {!! Form::text("", $apsp->unit_name, ['class' => 'form-control input-md', 'readonly']) !!}
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">১২. এডহক ভিত্তিক কাঁচামালের ষান্মাসিক আমদানিস্বত্ব/ চাহিদা</div>
                            <div class="panel-body">
                                @if($appInfo->irc_purpose_id != 2)
                                    <table class="table table-bordered dt-responsive" cellspacing="0" width="100%">
                                        <thead class="alert alert-info">
                                        <tr>
                                            <td colspan="6">উদ্যোক্তা কর্তৃক দাখিল কৃত তথ্য অনুযায়ী:</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $count = 1;?>
                                        @foreach($annualProductionCapacity as $apc)
                                            <tr>
                                                <td><?php echo $count++ ?></td>
                                                <td width="15%">
                                                    <table>
                                                        <tr>
                                                            <td>প্রতি&nbsp;</td>
                                                            <td>{!! Form::text("unit_of_product[]", $apc->unit_of_product, ['class' => 'form-control input-md', 'readonly']) !!}</td></td>
                                                        </tr>
                                                    </table>
                                                <td width="15%">
                                                    {!! Form::select("quantity_unit[]", $productUnit, $apc->quantity_unit, ['class' => 'form-control input-md', 'readonly']) !!}
                                                </td>
                                                <td width="15%">
                                                    {!! Form::text("", $apc->product_name, ['class' => 'form-control input-md', 'readonly']) !!}
                                                </td>
                                                <td width="25%"> উৎপাদনের জন্য কাঁচামাল প্রয়োজন </td>
                                                <td width="30%">
                                                    <table>
                                                        <tr>
                                                            <td>{!! Form::text("raw_material_total_price[]", $apc->raw_material_total_price, ['class' => 'form-control input-md required', 'readonly', 'id' => 'raw_material_total_price']) !!}</td>
                                                            <td>&nbsp;টাকার</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <br>

                                    <span>
                                        <strong>কাঁচামালের ষান্মাসিক আমদানিস্বত্ব/ চাহিদা</strong> <br>
                                        উদ্যোক্তা কর্তৃক কারখানায় স্থাপিত যন্ত্রপাতি (এলসিকৃত/ স্থানীয়ভাবে সংগ্রহীত), নিয়োজিত জনবল, অবকাঠামোগত সুবিধা বিবেচনাপূর্বক প্রদত্ত তথ্য এবং প্রতিষ্ঠান কর্তৃপক্ষের সাথে আলোচনার ভিত্তিতে কারখানাটির বার্ষিক উৎপাদন ক্ষমতা প্রাথমিকভাবে নিম্নরূপ নির্ধারণ করা যেতে পারে।
                                    </span>
                                    <table class="table table-bordered dt-responsive" cellspacing="0" width="100%">
                                        <thead class="alert alert-info">
                                        <tr>
                                            <td>ক্রমিক নং</td>
                                            <td>পন্য/ সেবার নাম</td>
                                            <td>নির্ধারিত বার্ষিক উৎপাদন ক্ষমতা</td>
                                            <td>ষান্মাসিক উৎপাদন ক্ষমতা</td>
                                            <td>ষান্মাসিক আমদানিস্বত্ব (টাকা)</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $count = 1;?>
                                        @foreach($annualProductionCapacity as $apc)
                                            <tr>
                                                <td><?php echo $count++ ?></td>
                                                <td>{!! Form::text("product_name[]", $apc->product_name, ['class' => 'form-control input-md', 'readonly']) !!}</td>
                                                <td>{!! Form::number("fixed_production[]", '', ['class' => 'form-control input-md required', 'onkeyup' => "calculateHalfYearlyProduction(this)"]) !!}</td>
                                                <td>{!! Form::number("half_yearly_production[]", '', ['class' => 'form-control input-md required', 'readonly']) !!}</td>
                                                <td>{!! Form::number("half_yearly_import[]", '', ['class' => 'form-control input-md apc_half_yearly_import required', 'onkeyup' => "calculateHalfYearlyImportTotal('apc_half_yearly_import', 'apc_half_yearly_import_total')"]) !!}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4"><span class="pull-right">মোট টাকা</span></td>
                                                <td>{!! Form::number("apc_half_yearly_import_total", '', ['class' => 'form-control input-md', 'id' => 'apc_half_yearly_import_total', 'readonly']) !!}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"><span class="pull-right">অন্যান্য টাকার পরিমান</span></td>
                                                <td>{!! Form::number("apc_half_yearly_import_other", '', ['class' => 'form-control input-md', 'id' => 'apc_half_yearly_import_other', 'onkeyup' => "calculateGrandTotal()"]) !!}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"><span class="pull-right">সর্বমোট টাকা</span></td>
                                                <td>{!! Form::number("apc_half_yearly_import_grand_total", '', ['class' => 'form-control input-md', 'id' => 'apc_half_yearly_import_grand_total', 'readonly']) !!}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <br>
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-12 {{$errors->has('apc_half_yearly_import_total_in_word') ? 'has-error': ''}}">
                                                {!! Form::label('apc_half_yearly_import_total_in_word','কথায়',['class'=>'text-left col-md-1']) !!}
                                                <div class="col-md-11">
                                                    {!! Form::text('apc_half_yearly_import_total_in_word', '', ['class' => 'form-control input-md required']) !!}
                                                    {!! $errors->first('apc_half_yearly_import_total_in_word','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($appInfo->irc_purpose_id != 1)
                            <div class="panel panel-info">
                                <div class="panel-heading">১৩. এডহক ভিত্তিক খুচরা যন্ত্রাংশের ষান্মাসিক আমদানিস্বত্ব/ চাহিদা</div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-12 {{$errors->has('em_lc_total_taka_mil') ? 'has-error': ''}}">
                                                {!! Form::label('em_lc_total_taka_mil','প্রতিষ্ঠান কর্তৃক এলসিকৃত মূলধনী যন্ত্রপাতির মোট মূল্যের',['class'=>'text-left col-md-5']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('em_lc_total_taka_mil', $appInfo->em_lc_total_taka_mil, ['class' => 'form-control input-md required']) !!}
                                                    {!! $errors->first('em_lc_total_taka_mil','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group" style="margin-top: 2px">
                                            <div class="col-md-2 {{$errors->has('em_lc_total_percent') ? 'has-error': ''}}">
                                                {!! Form::text('em_lc_total_percent', '', ['class' => 'form-control input-md required']) !!}
                                                {!! $errors->first('em_lc_total_percent','<span class="help-block">:message</span>') !!}
                                            </div>
                                            <div class="col-md-4 {{$errors->has('em_lc_total_five_percent') ? 'has-error': ''}}">
                                                {!! Form::label('em_lc_total_five_percent','% হারে অর্থাৎ',['class'=>'text-left col-md-4']) !!}
                                                <div class="col-md-8">
                                                    {!! Form::number('em_lc_total_five_percent', '', ['class' => 'form-control input-md required']) !!}
                                                    {!! $errors->first('em_lc_total_five_percent','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                            {{-- <div class="col-md-6 {{$errors->has('em_lc_total_five_percent') ? 'has-error': ''}}">
                                                {!! Form::label('em_lc_total_five_percent','প্রতিষ্ঠান কর্তৃক এলসিকৃত মূলধনী যন্ত্রপাতির মোট মূল্যের',['class'=>'text-left col-md-5']) !!}
                                                <div class="col-md-7">
                                                    {!! Form::text('em_lc_total_five_percent', $appInfo->em_lc_total_five_percent, ['class' => 'form-control input-md required']) !!}
                                                    {!! $errors->first('em_lc_total_five_percent','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div> --}}
                                            <div class="col-md-6 form-group {{$errors->has('em_lc_total_five_percent_in_word') ? 'has-error': ''}}">
                                                {!! Form::label('em_lc_total_five_percent_in_word','কথায়',['class'=>'col-md-2 text-left ']) !!}
                                                <div class="col-md-10">
                                                    {!! Form::text('em_lc_total_five_percent_in_word', '', ['class' => 'form-control input-md required']) !!}
                                                    {!! $errors->first('em_lc_total_five_percent_in_word','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12">
                                            খুচরা  যন্ত্রাংশের জন্য ষান্মাসিক আমদানিস্বত্ব নির্ধারন করা যেতে পারে।
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel-body">
                                    <a type="button" class="btn btn-md btn-info pull-right" data-toggle="modal" data-target="#myModal">Govt. Fees Calculator for inspection</a>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="panel panel-info">
                            <div class="panel-heading">মন্তব্য</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-md-12 {{$errors->has('irc_remarks') ? 'has-error': ''}}">
                                        <textarea class="form-control maxTextCountDown required" rows="8"
                                                  name="irc_remarks"></textarea>
                                            {!! $errors->first('irc_remarks','<span class="help-block">:message</span>') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">এনটাইটেলমেন্ট পেপার</div>
                            <div class="panel-body">
                                <table class="table table-bordered table-responsive">
                                    <thead class="alert alert-info">
                                    <tr>
                                        <th rowspan="2">শিল্প প্রতিষ্ঠানের নাম ও ঠিকানা</th>
                                        <th rowspan="2">মন্তব্য</th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td width="25%">
                                            {{$appInfo->company_name}} <br>
                                            {{ $appInfo->factory_address }}
                                        </td>
                                        <td width="20%"><textarea class="form-control maxTextCountDown valid" name="entitlement_remarks" required></textarea></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> {{--end pannel body--}}

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
                <h4 class="modal-title">Govt. Fees Calculator for inspection</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th scope="col">Class</th>
                        <th colspan="3" scope="colgroup">Fees break down Taka</th>
                        <th scope="col">Registration Fees</th>
                        <th scope="col">Renew Fees</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1; ?>
                    @foreach($totalFee as $fee)
                        <tr>
                            <td scope="row">{{ $fee->class }}</td>
                            <td>{{ $fee->annual_total_import_min }}</td>
                            <td>To</td>
                            <td>{{ $fee->annual_total_import_max }}</td>
                            <td>{{ $fee->primary_reg_fee }}</td>
                            <td>{{ $fee->annual_renew_fee }}</td>

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

<script>
    $(document).ready(function () {
        var today = new Date();
        var yyyy = today.getFullYear();

        $('.datepicker').datetimepicker({
            viewMode: 'days',
            format: 'DD-MMM-YYYY',
        });

        $('.datetimeicker').datetimepicker({
            viewMode: 'days',
            format: 'DD-MMM-YYYY hh:mm',
        });

        $("#bank_id").trigger('change');

        // document.getElementById("ep_raw_material_total_val").innerHTML = (document.getElementById('raw_material_total_price').value)/2;

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

    function calculateHalfYearlyImportTotal(className, totalShowFieldId) {
        var total_import = 0;
        $("." + className).each(function () {
            total_import = total_import + (this.value ? parseFloat(this.value) : 0);
        });
        $("#" + totalShowFieldId).val(total_import);

        // Calculate the grand total by adding apc_half_yearly_import_total and apc_half_yearly_import_other
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        var total = $("#apc_half_yearly_import_total").val() ? parseFloat($("#apc_half_yearly_import_total").val()) : 0;
        var other = $("#apc_half_yearly_import_other").val() ? parseFloat($("#apc_half_yearly_import_other").val()) : 0;
        var grandTotal = total + other;
        $("#apc_half_yearly_import_grand_total").val(grandTotal);
    }

    function calculateHalfYearlyProduction(arg) {
        var $tr = $(arg).closest('tr');
        var quantity = $tr.find('td:eq(2) input').val();
        $tr.find('td:eq(3) input').val(quantity/2);
    }

    var popupWindow = null;
    $('.formPreview').on('click', function (e) {
        $('body').css({"display": "none"});
        popupWindow = window.open('<?php echo URL::to('/inspection-report-submission/preview'); ?>', 'Sample', '');
    });

    function loadOtherStatus(){
        var selected_value = $("#project_status_id").val();
        if(selected_value == 4){
            $("#other_details").css("display", "block");
        }else{
            $("#other_details").css("display", "none");
        }
    }
</script>