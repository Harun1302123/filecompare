{{-- Business class modal --}}
<div class="row">
    <div class="col-md-12">
        <div class="modal fade" id="businessClassModal" tabindex="-1" style="z-index: 99999;" role="dialog"
             aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content load_business_class_modal"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">

    <!-- Trigger the modal with a button -->
    <button type="button" style="display: none" id="myModalBtn" class="btn btn-info btn-lg" data-toggle="modal"
            data-target="#myModal" data-backdrop="static" data-keyboard="false">Open Modal
    </button>

    <!-- Modal -->
    <div class="modal fade" tabindex="-1" id="myModal" role="dialog">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <div class="alert alert-danger modal-title text-center" role="alert">
                        <span class="text-primary">A vital change is immediately needed in your BIDA Registration information based on the decision from legal authority.</span>
                    </div>

                    <br/>
                    <div class="errorMsg alert alert-danger alert-dismissible hidden">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    </div>
                    <div class="successMsg alert alert-success alert-dismissible hidden">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    </div>
                </div>

                {!! Form::open(array('url' => '/bida-registration/update-business-class', 'method' => 'post','id'=>'businessClassForm')) !!}
                {!! Form::hidden('app_id', Encryption::encodeId($appInfo->id) ,['class' => 'form-control input-md required', 'id'=>'app_id']) !!}
                <div class="modal-body br_upgrade_modal" style="overflow: hidden; height: 400px; overflow-y: scroll;">

                    <div class="form-group col-md-12">
                        <ol class="breadcrumb">
                            <li><strong>Tracking no. : </strong>{{ $appInfo->tracking_no  }}</li>
                            <li><strong>Current Status : </strong>{{ $appInfo->status_name }}</li>
                            <li><strong> Date of
                                    Submission: </strong> {{ \App\Libraries\CommonFunction::formateDate($appInfo->submitted_at)  }}
                            </li>
                        </ol>

                        <fieldset class="scheduler-border" style="margin-bottom: 20px;">
                            <legend class="scheduler-border">According to the decision of BIDA, you need to give the
                                following information
                            </legend>
                            <div class="form-group col-md-12 {{$errors->has('business_class_code') ? 'has-error' : ''}}">
                                {!! Form::label('business_class_code','Business Sector (BBS Class Code)',['class'=>'col-md-3']) !!}
                                <div class="col-md-9">
                                    {!! Form::text('business_class_code', $appInfo->class_code, ['class' => 'form-control input-md', 'min' => 4,'onkeyup' => 'findBusinessClassCode()']) !!}
                                    <span class="help-text" style="margin: 5px 0;">
                                        <a style="cursor: pointer;" data-toggle="modal"
                                           data-target="#businessClassModal" onclick="openBusinessClassModal(this)"
                                           data-action="/bida-registration/get-business-class-modal">
                                            Click here to select from the list
                                        </a>
                                    </span>
                                    {!! $errors->first('business_class_code','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div id="no_business_class_result"></div>

                                <fieldset class="scheduler-border hidden" id="business_class_list_sec">
                                    <legend class="scheduler-border">Other info. based on your business class (Code =
                                        <span id="business_class_list_of_code"></span>)
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
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">
                                After updating, the following information will not be shown in the form anymore.
                                However, a hyperlink will be provided so that you can see this information if required
                            </legend>
                            <div class="col-md-6 {{$errors->has('business_sector_id') ? 'has-error': ''}}">
                                {!! Form::label('business_sector_id','Business sector',['class'=>'col-md-5 text-left']) !!}
                                @if($viewMode == 'on')
                                    <div class="col-md-7">
                            <span class="form-control input-md" style="background:#eee; height: auto;min-height: 30px;">
                            {{ $sectors[$appInfo->business_sector_id] }}
                            </span>
                                    </div>
                                @else
                                    <div class="col-md-7">
                                        {!! Form::select('business_sector_id', $sectors, $appInfo->business_sector_id, ['class' => 'form-control  input-md bigInputField','id'=>'business_sector_id', 'onchange'=>"LoadSubSector(this.value, 'SECTOR_OTHERS', 'business_sector_others', 'business_sub_sector_id',". $appInfo->business_sub_sector_id .")"]) !!}
                                        {!! $errors->first('business_sector_id','<span class="help-block">:message</span>') !!}
                                    </div>
                                    <div style="margin-top: 10px;" class="col-md-12 maxTextCountDown"
                                         id="SECTOR_OTHERS" hidden>
                                        {!! Form::textarea('business_sector_others', $appInfo->business_sector_others, ['placeholder'=>'Specify others sector', 'class' => 'form-control bigInputField input-md',
                                        'id' => 'business_sector_others', 'size'=>'5x1','maxlength'=>'200']) !!}
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6 {{$errors->has('business_sub_sector_id') ? 'has-error': ''}}">
                                {!! Form::label('business_sub_sector_id','Sub sector',['class'=>'col-md-5 text-left']) !!}
                                @if($viewMode == 'on')
                                    <div class="col-md-7">
                            <span class="form-control input-md" style="background:#eee; height: auto;min-height: 30px;">
                            {{ $sub_sectors[$appInfo->business_sub_sector_id] }}
                            </span>
                                    </div>
                                @else
                                    <div class="col-md-7">
                                        {!! Form::select('business_sub_sector_id', $sub_sectors, $appInfo->business_sub_sector_id, ['class' => 'form-control input-md bigInputField','id'=>'business_sub_sector_id', 'onchange'=>"SubSectorOthersDiv(this.value, 'SUB_SECTOR_OTHERS', 'business_sub_sector_others')"]) !!}
                                        {!! $errors->first('business_sub_sector_id','<span class="help-block">:message</span>') !!}
                                    </div>
                                    <div style="margin-top: 10px;" class="col-md-12 maxTextCountDown"
                                         id="SUB_SECTOR_OTHERS" hidden>
                                        {!! Form::textarea('business_sub_sector_others', $appInfo->business_sub_sector_others, ['placeholder'=>'Specify others sub-sector', 'class' => 'form-control bigInputField input-md',
                                        'id' => 'business_sub_sector_others', 'size'=>'5x1','maxlength'=>'200']) !!}
                                    </div>
                                @endif
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">Investment</legend>
                            <div class="col-md-6 {{$errors->has('usd_exchange_rate') ? 'has-error': ''}}">
                                {!! Form::label('usd_exchange_rate','Dollar exchange rate',['class'=>'col-md-5 text-left required-star']) !!}
                                <div class="col-md-7">
                                    {!! Form::number('usd_exchange_rate', $appInfo->usd_exchange_rate, ['data-rule-maxlength'=>'40','class' => 'form-control input-md numberNoNegative required','id'=>'usd_exchange_rate']) !!}
                                    {!! $errors->first('usd_exchange_rate','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                            <span class="help-text" style="margin: 5px 0;">Exchange Rate Ref: <a
                                        href="https://www.bangladesh-bank.org/econdata/exchangerate.php"
                                        target="_blank">Bangladesh Bank</a>. Please Enter Today's Exchange Rate</span>
                        </fieldset>
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">Source of finance [please enter the values under Country
                                wise source of finance (Million BDT) only]
                            </legend>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" cellspacing="0"
                                       width="100%">
                                    <tbody id="annual_production_capacity">
                                    <tr>
                                        <td><strong>(a)</strong> Local Equity (Million)</td>
                                        <td>
                                            {!! Form::number('finance_src_loc_equity_1', $appInfo->finance_src_loc_equity_1, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number readOnly','id'=>'finance_src_loc_equity_1']) !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Foreign Equity (Million)</td>
                                        <td>
                                            {!! Form::number('finance_src_foreign_equity_1', $appInfo->finance_src_foreign_equity_1, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number readOnly','id'=>'finance_src_foreign_equity_1']) !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Total Equity</th>
                                        <td>
                                            {!! Form::number('finance_src_loc_total_equity_1', $appInfo->finance_src_loc_total_equity_1, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number readOnly','id'=>'finance_src_loc_total_equity_1']) !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>(b)</strong> Local Loan (Million)</td>
                                        <td>
                                            {!! Form::number('finance_src_loc_loan_1', $appInfo->finance_src_loc_loan_1, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number readOnly','id'=>'finance_src_loc_loan_1']) !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Foreign Loan (Million)</td>
                                        <td>
                                            {!! Form::number('finance_src_foreign_loan_1', $appInfo->finance_src_foreign_loan_1, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number readOnly','id'=>'finance_src_foreign_loan_1']) !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Total Loan (Million)</th>
                                        <td>
                                            {!! Form::number('finance_src_total_loan', $appInfo->finance_src_total_loan, ['id'=>'finance_src_total_loan','class' => 'form-control input-md readOnly numberNoNegative', 'data-rule-maxlength'=>'240']) !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Total Financing Million (a+b)</th>
                                        <td>
                                            {!! Form::number('finance_src_loc_total_financing_m', $appInfo->finance_src_loc_total_financing_m, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number readOnly','id'=>'finance_src_loc_total_financing_m']) !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Total Financing BDT (a+b)</th>
                                        <td>
                                            {!! Form::number('finance_src_loc_total_financing_1', $appInfo->finance_src_loc_total_financing_1, ['data-rule-maxlength'=>'40','class' => 'form-control input-md number readOnly','id'=>'finance_src_loc_total_financing_1']) !!}
                                            <span class="text-danger"
                                                  style="font-size: 12px; font-weight: bold"
                                                  id="finance_src_loc_total_financing_1_alert"></span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                                <table class="table table-striped table-bordered" cellspacing="0"
                                       width="100%" id="financeTableId">
                                    <thead>
                                    <tr>
                                        <th colspan="4">
                                            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="From the above information, the values of “Local Equity (Million)” and “Local Loan (Million)” will go into the "Equity Amount" and "Loan Amount" respectively for Bangladesh. The summation of the "Equity Amount" and "Loan Amount" of other countries will be equal to the values of “Foreign Equity (Million)” and “Foreign Loan (Million)” respectively." ></i>
                                            Country wise source of finance (Million BDT)
                                        </th>
                                    </tr>
                                    </thead>

                                    <tr>
                                        <td class="required-star">Country</td>
                                        <td class="required-star">
                                            Equity Amount
                                            <span class="text-danger" id="equity_amount_err"></span>
                                        </td>
                                        <td class="required-star">
                                            Loan Amount
                                            <span class="text-danger" id="loan_amount_err"></span>
                                        </td>
                                        <td>#</td>
                                    </tr>

                                    @if(count($source_of_finance) > 0)
                                        <?php $inc = 0; ?>
                                        @foreach($source_of_finance as $finance)
                                            <tr id="financeTableIdRow{{$inc}}">
                                                <td>
                                                    {!! Form::hidden("source_of_finance_id[$inc]", $finance->id) !!}
                                                    {!!Form::select("country_id[$inc]", $countries, $finance->country_id, ['class' => 'form-control required'])!!}
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
                                                    <a class="btn btn-sm btn-primary addTableRows show-in-view"
                                                       title="Add more"
                                                       onclick="addTableRow('financeTableId', 'financeTableIdRow0');">
                                                        <i class="fa fa-plus"></i></a>
                                                    <?php } else { ?>
                                                    <a href="javascript:void(0)"
                                                       class="btn btn-sm btn-danger removeRow show-in-view"
                                                       title="Remove row"
                                                       onclick="removeTableRow('financeTableId', 'financeTableIdRow{{$inc}}');">
                                                        <i class="fa fa-times" aria-hidden="true"></i></a>
                                                    <?php } ?>

                                                </td>
                                            </tr>
                                            <?php $inc++; ?>
                                        @endforeach
                                    @else
                                        <tr id="financeTableIdRow">
                                            <td>
                                                {!!Form::select('country_id[]', $countries, 18, ['class' => 'form-control required'])!!}
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
                                                <a class="btn btn-sm btn-primary addTableRows show-in-view"
                                                   title="Add more"
                                                   onclick="addTableRow('financeTableId', 'financeTableIdRow');">
                                                    <i class="fa fa-plus"></i></a>
                                            </td>
                                        </tr>
                                    @endif

                                </table>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" id="update_bus_class_close" class="btn btn-info pull-left"
                            data-dismiss="modal">Close
                    </button>

                    <button type="submit" id="action_btn" class="btn btn-primary"><i
                                class="fa fa-check-circle"></i> Update
                    </button>

                </div>
                {!! Form::close() !!}
            </div>

        </div>
    </div>

</div>

<style>
    .modal {
        text-align: center;
        padding: 0 !important;
    }

    .modal:before {
        content: '';
        display: inline-block;
        height: 100%;
        vertical-align: middle;
        margin-right: -4px;
    }

    .modal-dialog {
        display: inline-block;
        text-align: left;
        vertical-align: middle;
    }
</style>

<script>
    $(document).ready(function () {
        document.getElementById('myModalBtn').click();

        $.validator.addMethod("equityAmountRule", function (value, element) {
            var equity_amount_elements = document.querySelectorAll('.br_upgrade_modal .equity_amount');

            var total_equity_amounts = 0;
            for (var i = 0; i < equity_amount_elements.length; i++) {
                total_equity_amounts = total_equity_amounts + parseFloat(equity_amount_elements[i].value ? equity_amount_elements[i].value : 0);
            }
            var finance_src_loc_total_equity_1 = parseFloat(document.getElementById('finance_src_loc_total_equity_1').value ?
                document.getElementById('finance_src_loc_total_equity_1').value : 0);

            if (finance_src_loc_total_equity_1.toFixed(3) != total_equity_amounts.toFixed(3)) {
                document.getElementById('equity_amount_err').innerHTML = '<br/>Total equity amount should be equal to Total Equity (Million)';
                return false;
            } else {
                document.getElementById('equity_amount_err').innerHTML = '';
                return true;
            }

        }, "Please input a reason");

        $.validator.addMethod("loanAmountRule", function (value, element) {
            var loan_amount_elements = document.querySelectorAll('.br_upgrade_modal .loan_amount');
            var total_loan_amounts = 0;
            for (var i = 0; i < loan_amount_elements.length; i++) {
                total_loan_amounts = total_loan_amounts + parseFloat(loan_amount_elements[i].value ? loan_amount_elements[i].value : 0);
            }
            var finance_src_total_loan = parseFloat(document.getElementById('finance_src_total_loan').value ?
                document.getElementById('finance_src_total_loan').value : 0);
            if (finance_src_total_loan.toFixed(3) != total_loan_amounts.toFixed(3)) {
                document.getElementById('loan_amount_err').innerHTML = '<br/>Total loan amount should be equal to Total Loan (Million)';
                return false;
            } else {
                document.getElementById('loan_amount_err').innerHTML = '';
                return true;
            }
        }, "Please input a reason");

        $.validator.addClassRules("equity_amount", {
            required: true,
            equityAmountRule: true
        });

        $.validator.addClassRules("loan_amount", {
            required: true,
            loanAmountRule: true
        });

        $("#businessClassForm").validate({

            errorPlacement: function () {
                return false;
            },
            submitHandler: formSubmit
        });

        var form = $("#businessClassForm"); //Get Form ID
        var url = form.attr("action"); //Get Form action
        var type = form.attr("method"); //get form's data send method
        var info_err = $('.errorMsg'); //get error message div
        var info_suc = $('.successMsg'); //get success message div

        //============Ajax Setup===========//
        function formSubmit() {

            $.ajax({
                type: type,
                url: url,
                data: form.serialize(),
                dataType: 'json',
                beforeSend: function (msg) {
                    //console.log("before send");
                    $("#action_btn").html('<i class="fa fa-cog fa-spin"></i> Loading...');
                    $("#action_btn").prop('disabled', true); // disable button
                },
                success: function (data) {
                    //==========validation error===========//
                    if (data.success == false) {
                        info_err.hide().empty();
                        $.each(data.error, function (index, error) {
                            info_err.removeClass('hidden').append('<li>' + error + '</li>');
                        });
                        info_err.slideDown('slow');
                        info_err.delay(2000).slideUp(1000, function () {
                            $("#action_btn").html('Submit');
                            $("#action_btn").prop('disabled', false);
                        });
                    }
                    //==========if data is saved=============//
                    if (data.success == true) {
                        info_suc.hide().empty();
                        info_suc.removeClass('hidden').html(data.status);
                        info_suc.slideDown('slow');
                        info_suc.delay(2000).slideUp(800, function () {
                            window.location.href = data.link;
                        });
                        form.trigger("reset");

                    }
                    //=========if data already submitted===========//
                    if (data.error == true) {
                        info_err.hide().empty();
                        info_err.removeClass('hidden').html(data.status);
                        info_err.slideDown('slow');
                        info_err.delay(1000).slideUp(800, function () {
                            $("#action_btn").html('Submit');
                            $("#action_btn").prop('disabled', false);
                        });
                    }
                },
                error: function (data) {
                    var errors = data.responseJSON;
                    $("#action_btn").prop('disabled', false);
                    console.log(errors);
                    alert('Sorry, an unknown Error has been occured! Please try again later.');
                }
            });
            return false;
        }
    });

    function openBusinessClassModal(btn) {
        console.log('ok');
        //e.preventDefault();
        var this_action = btn.getAttribute('data-action');
        if(this_action != ''){
            $.get(this_action, function(data, success) {
                if(success === 'success'){
                    console.log(data);
                    $('#businessClassModal .load_business_class_modal').html(data);
                }else{
                    $('#businessClassModal .load_business_class_modal').html('Unknown Error!');
                }
                $('#businessClassModal').modal('show', {backdrop: 'static'});
            });
        }
    }
</script>