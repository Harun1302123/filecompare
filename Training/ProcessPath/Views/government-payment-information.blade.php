<style>
    .form-group {
        margin-bottom: 2px;
    }

    .blink_me {
        animation: blinker 5s linear infinite;
    }

    @keyframes blinker {
        50% { opacity: .5; }
    }
</style>

@if(!empty($appInfo->resend_deadline))
    <div class="form-group">
        <div class="row">
            <div class="col-md-12">
                <div style="margin-bottom: 0" class="alert btn-danger" role="alert">
                    The government fee must be paid before <strong>{{ date("d M, Y", strtotime($appInfo->resend_deadline)) }}</strong> otherwise, your application will be automatically canceled.
                </div>
            </div>
        </div>
    </div>
@endif
<div class="panel panel-success">
    <div class="panel-heading">
        <h5><strong>Government fee payment</strong></h5>
    </div>

    @if(isset($payment_config) && !empty($payment_config))
        {!! Form::open(array('url' => $appInfo->form_url.'/payment','method' => 'post','id' => $appInfo->form_url,'enctype'=>'multipart/form-data',
                    'method' => 'post', 'files' => true, 'role'=>'form')) !!}
        <div class="panel-body">
            <input type="hidden" name="app_id" value="{{ \App\Libraries\Encryption::encodeId($appInfo->id) }}"
                   id="app_id"/>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-6 {{$errors->has('gfp_contact_name') ? 'has-error': ''}}">
                        {!! Form::label('gfp_contact_name','Contact name',['class'=>'col-md-5 text-left required-star']) !!}
                        <div class="col-md-7">
                            {!! Form::text('gfp_contact_name', \App\Libraries\CommonFunction::getUserFullName(), ['class' => 'form-control input-md required']) !!}
                            {!! $errors->first('gfp_contact_name','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                    <div class="col-md-6 {{$errors->has('gfp_contact_email') ? 'has-error': ''}}">
                        {!! Form::label('gfp_contact_email','Contact email',['class'=>'col-md-5 text-left required-star']) !!}
                        <div class="col-md-7">
                            {!! Form::email('gfp_contact_email', Auth::user()->user_email, ['class' => 'form-control input-md required email']) !!}
                            {!! $errors->first('gfp_contact_email','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6 {{$errors->has('gfp_contact_phone') ? 'has-error': ''}}">
                        {!! Form::label('gfp_contact_phone','Contact phone',['class'=>'col-md-5 text-left required-star']) !!}
                        <div class="col-md-7">
                            {!! Form::text('gfp_contact_phone', Auth::user()->user_phone, ['class' => 'form-control input-md required gfp_contact_phone phone_or_mobile']) !!}
                            {!! $errors->first('gfp_contact_phone','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                    <div class="col-md-6 {{$errors->has('gfp_contact_address') ? 'has-error': ''}}">
                        {!! Form::label('gfp_contact_address','Contact address',['class'=>'col-md-5 text-left required-star']) !!}
                        <div class="col-md-7">
                            {!! Form::text('gfp_contact_address', Auth::user()->road_no .  (!empty(Auth::user()->house_no) ? ', ' . Auth::user()->house_no : ''), ['class' => 'form-control input-md required']) !!}
                            {!! $errors->first('gfp_contact_address','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6 {{$errors->has('gfp_pay_amount') ? 'has-error': ''}}">
                        {!! Form::label('gfp_pay_amount','Pay amount',['class'=>'col-md-5 text-left']) !!}
                        <div class="col-md-7">
                            {!! Form::text('gfp_pay_amount', $payment_config->amount, ['class' => 'form-control input-md', 'readonly']) !!}
                            {!! $errors->first('gfp_pay_amount','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>

                    <div class="col-md-6 {{$errors->has('gfp_vat_on_pay_amount') ? 'has-error': ''}}">
                        {!! Form::label('gfp_vat_on_pay_amount','VAT on pay amount',['class'=>'col-md-5 text-left']) !!}
                        <div class="col-md-7">
                            {!! Form::text('gfp_vat_on_pay_amount', $payment_config->vat_on_pay_amount, ['class' => 'form-control input-md', 'readonly']) !!}
                            {!! $errors->first('gfp_vat_on_pay_amount','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        {!! Form::label('gfp_total_amount','Total Amount',['class'=>'col-md-5 text-left']) !!}
                        <div class="col-md-7">
                            {!! Form::text('gfp_total_amount', number_format($payment_config->amount + $payment_config->vat_on_pay_amount, 2), ['class' => 'form-control input-md', 'readonly']) !!}
                        </div>
                    </div>

                    <div class="col-md-6 {{$errors->has('gfp_status') ? 'has-error': ''}}">
                        {!! Form::label('gfp_status','Payment status',['class'=>'col-md-5 text-left']) !!}
                        <div class="col-md-7">
                            <span class="label label-warning">Not paid</span>
                        </div>
                    </div>
                </div>
            </div>

            @if($appInfo->gfp_payment_status != 1)
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <div style="margin-bottom: 0" class="alert alert-danger" role="alert">
                                <strong>Vat/ Tax</strong> and <strong>Transaction charge</strong> is an approximate amount, those may vary based on the Sonali Bank system and those will be visible here after payment submission.
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="panel-footer">
            <div class="pull-right">
                <button type="submit" id="submitForm" style="cursor: pointer;" class="btn btn-success btn-md"
                        value="Submit" name="actionBtn">Payment Submit
                </button>
            </div>
            <div class="clearfix"></div>
        </div>
        {!! Form::close() !!}
    @elseif(isset($manualGovtPaymentConfig) && !empty($manualGovtPaymentConfig))
        {!! Form::open(array('url' => $appInfo->form_url.'/manual-payment','method' => 'post','id' => $appInfo->form_url,'enctype'=>'multipart/form-data',
'method' => 'post', 'files' => true, 'role'=>'form')) !!}
        <div class="panel-body">

            <input type="hidden" name="app_id" value="{{ \App\Libraries\Encryption::encodeId($appInfo->id) }}"
                   id="app_id"/>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-6 {{$errors->has('contact_name') ? 'has-error': ''}}">
                        {!! Form::label('contact_name','Contact name',['class'=>'col-md-5 text-left required-star']) !!}
                        <div class="col-md-7">
                            {!! Form::text('contact_name', \App\Libraries\CommonFunction::getUserFullName(), ['class' => 'form-control input-md required']) !!}
                            {!! $errors->first('contact_name','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                    <div class="col-md-6 {{$errors->has('contact_email') ? 'has-error': ''}}">
                        {!! Form::label('contact_email','Contact email',['class'=>'col-md-5 text-left required-star']) !!}
                        <div class="col-md-7">
                            {!! Form::email('contact_email', Auth::user()->user_email, ['class' => 'form-control input-md required email']) !!}
                            {!! $errors->first('contact_email','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6 {{$errors->has('contact_no') ? 'has-error': ''}}">
                        {!! Form::label('contact_no','Contact phone',['class'=>'col-md-5 text-left required-star']) !!}
                        <div class="col-md-7">
                            {!! Form::text('contact_no', Auth::user()->user_phone, ['class' => 'form-control input-md required phone_or_mobile']) !!}
                            {!! $errors->first('contact_no','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                    <div class="col-md-6 {{$errors->has('address') ? 'has-error': ''}}">
                        {!! Form::label('address','Contact address',['class'=>'col-md-5 text-left required-star']) !!}
                        <div class="col-md-7">
                            {!! Form::text('address', Auth::user()->road_no .  (!empty(Auth::user()->house_no) ? ', ' . Auth::user()->house_no : ''), ['class' => 'form-control input-md required']) !!}
                            {!! $errors->first('address','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6 {{$errors->has('ref_tran_no') ? 'has-error': ''}}">
                        {!! Form::label('ref_tran_no','Bank reference number',['class'=>'col-md-5 text-left required-star']) !!}
                        <div class="col-md-7">
                            {!! Form::text('ref_tran_no', '', ['class' => 'form-control input-md', 'required' => 'required']) !!}
                            {!! $errors->first('ref_tran_no','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                    <div class="col-md-6 {{$errors->has('invoice_copy') ? 'has-error': ''}}">
                        {!! Form::label('invoice_copy','Invoice copy',['class'=>'col-md-5 text-left required-star']) !!}
                        <div class="col-md-7">
                            <input type="file" id="invoice_copy"
                                   name="invoice_copy" onchange="checkPdfDocumentType(this.id, 2)"
                                   accept="application/pdf"
                                   class="form-control input-md required" required/>
                            {!! $errors->first('invoice_copy','<span class="help-block">:message</span>') !!}
                            <span class="text-danger" style="font-size: 9px; font-weight: bold">[File Format: *.pdf | Maximum File size 2MB]</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6 {{$errors->has('pay_amount') ? 'has-error': ''}}">
                        {!! Form::label('pay_amount','Pay amount',['class'=>'col-md-5 text-left required-star']) !!}
                        <div class="col-md-7">
                            {!! Form::text('pay_amount', $manualGovtPaymentConfig['pay_amount'], ['class' => 'form-control input-md', 'required' => 'required']) !!}
                            {!! $errors->first('pay_amount','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                    <div class="col-md-6 {{$errors->has('vat_amount') ? 'has-error': ''}}">
                        {!! Form::label('vat_amount','VAT/TAX',['class'=>'col-md-5 text-left required-star']) !!}
                        <div class="col-md-7">
                            {!! Form::text('vat_amount', '', ['class' => 'form-control input-md', 'required' => 'required']) !!}
                            {!! $errors->first('vat_amount','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6 {{$errors->has('transaction_charge_amount') ? 'has-error': ''}}">
                        {!! Form::label('transaction_charge_amount','Bank charge',['class'=>'col-md-5 text-left required-star']) !!}
                        <div class="col-md-7">
                            {!! Form::text('transaction_charge_amount', '', ['class' => 'form-control input-md', 'required' => 'required']) !!}
                            {!! $errors->first('transaction_charge_amount','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        {!! Form::label('total_amount','Total Amount',['class'=>'col-md-5 text-left required-star']) !!}
                        <div class="col-md-7">
                            {!! Form::text('total_amount', '', ['class' => 'form-control input-md', 'required' => 'required']) !!}
                            {!! $errors->first('total_amount','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6 {{$errors->has('payment_status') ? 'has-error': ''}}">
                        {!! Form::label('payment_status','Payment status',['class'=>'col-md-5 text-left']) !!}
                        <div class="col-md-7">
                            <span class="label label-warning">Not paid</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="pull-right">
                <button type="submit" id="submitForm" style="cursor: pointer;" class="btn btn-success btn-md"
                        value="Submit" name="actionBtn">Payment Submit
                </button>
            </div>
            <div class="clearfix"></div>
        </div>
        {!! Form::close() !!}
    @endif
</div>

<script src="{{ asset("build/js/intlTelInput-jquery_v16.0.8.min.js") }}" type="text/javascript"></script>
<script src="{{ asset("build/js/utils_v16.0.8.js") }}" type="text/javascript"></script>
@section('governmentFeeScript')
    <script>
        $(function () {
            $("#gfp_contact_phone").intlTelInput({
                hiddenInput: "gfp_contact_phone",
                initialCountry: "BD",
                placeholderNumberType: "MOBILE",
                separateDialCode: true
            });

            $("#contact_no").intlTelInput({
                hiddenInput: "contact_no",
                initialCountry: "BD",
                placeholderNumberType: "MOBILE",
                separateDialCode: true
            });

            $("#applicationForm").find('.iti').css({"width": "-moz-available", "width": "-webkit-fill-available"});
        });
    </script>
@endsection