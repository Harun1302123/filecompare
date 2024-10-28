<?php
$moduleName = Request::segment(1);
$proecss_type_id = Request::segment(3);
?>
<style>
    input[type="radio"] {
        -webkit-appearance: checkbox;
        /* Chrome, Safari, Opera */
        -moz-appearance: checkbox;
        /* Firefox */
        -ms-appearance: checkbox;
        /* not currently supported */
    }

    input[type=file]:-moz-read-only {
        padding: 0;
    }

    /* #remarks {
        opacity: 0.5;
        position: relative;
        height: auto !important;
        z-index: 1;
    }

    #mainInput {
        opacity: 1;
        background: transparent;
        position: absolute;
        height: auto !important;
        width: 98.7%;
        z-index: 2;
    } */
</style>

@include('ProcessPath::remarks-history-modal')

{{-- {!! Session::has('success') ? '<div class="alert alert-success alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("success") .'</div>' : '' !!} --}}
{{-- {!! Session::has('error') ? '<div class="alert alert-danger alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("error") .'</div>' : '' !!} --}}
{!! Form::open(['url' => 'process-path/batch-process-update', 'method' => 'post', 'id' => 'batch-process-form', 'files' => true]) !!}
<div class="col-md-12">
    <div class="alert alert-info" style="border: 10px solid #32a9c2 !important; overflow: inherit">
        <div class="row">
            <div class="col-sm-12">
                <div class="col-sm-6">
                    <h4>Application Process :</h4>
                </div>
                <div class="col-sm-6">
                    @if($appInfo->status_id != 1)
                        <a data-toggle="modal" data-target="#remarksHistoryModal" class="pull-right">
                            {!! Form::button('<i class="fa fa-eye"></i> <strong>Last Remarks</strong>', ['type' => 'button', 'class' => 'btn btn-md btn-info']) !!}
                        </a>
                    @endif
                </div>
                {{-- <span class="label label-success pull-right" style="font-size: 16px">Remaining Day : {{ $remainingDay }} </span></h4> --}}
            </div>
            <hr style="border-top-color: #32a9c2;margin-top: 40px; margin-bottom: 5px !important;" />
        </div>


        {{-- hidden data for data validation, update process --}}
        @if (isset($appInfo->ref_id))
            {!! Form::hidden('application_ids[0]', Encryption::encodeId($appInfo->ref_id), ['class' => 'form-control input-md required', 'id' => 'application_id']) !!}
        @endif
        {!! Form::hidden('status_from', Encryption::encodeId($appInfo->status_id)) !!}
        {!! Form::hidden('desk_from', Encryption::encodeId($appInfo->desk_id)) !!}
        {!! Form::hidden('process_list_id', Encryption::encodeId($appInfo->process_list_id), ['id' => 'process_list_id']) !!}
        {!! Form::hidden('cat_id', Encryption::encodeId($cat_id), ['id' => 'cat_id']) !!}
        {!! Form::hidden('data_verification', Encryption::encode(\App\Libraries\UtilFunction::processVerifyData($verificationData)), ['id' => 'data_verification']) !!}
        {!! Form::hidden('is_remarks_required', '', ['class' => 'form-control input-md ', 'id' => 'is_remarks_required']) !!}
        {!! Form::hidden('is_file_required', '', ['class' => 'form-control input-md ', 'id' => 'is_file_required']) !!}

        <div class="row">
            <div class="loading" style="display: none">
                <h2><i class="fa fa-spinner fa-spin"></i> &nbsp;</h2>
            </div>
            <div class="col-md-3 form-group {{ $errors->has('status_id') ? 'has-error' : '' }}">
                {!! Form::label('status_id', 'Apply Status') !!}
                {!! Form::select('status_id', [], null, ['class' => 'form-control required applyStausId', 'id' => 'application_status']) !!}
                {!! $errors->first('status_id', '<span class="help-block">:message</span>') !!}
            </div>

            <div id="resend_deadline_field" class="hidden">
                <div class="col-md-3 form-group {{ $errors->has('resend_deadline') ? 'has-error' : '' }}">
                    <label for="resend_deadline">Resend deadline</label>
                    <div class="datepicker input-group date">
                        {!! Form::text('resend_deadline', '', ['class' => 'form-control input-md', 'placeholder' => 'dd-mm-yyyy']) !!}
                        <span class="input-group-addon">
                            <span class="fa fa-calendar"></span>
                        </span>
                    </div>
                    {!! $errors->first('resend_deadline', '<span class="help-block">:message</span>') !!}
                </div>
            </div>

            <div id="sendToDeskOfficer">
                <div class="col-md-3 form-group {{ $errors->has('desk_id') ? 'has-error' : '' }}">
                    {!! Form::label('desk_id', 'Send to Desk') !!}
                    {!! Form::select('desk_id', ['' => 'Select Below'], '', ['class' => 'form-control dd_id required', 'id' => 'desk_status']) !!}
                    {!! $errors->first('desk_id', '<span class="help-block">:message</span>') !!}
                </div>
                {{-- <span class="col-md-1 {{$errors->has('priority') ? 'has-error' : ''}}" style="width: 15%"> --}}
                {{-- {!! Form::label('priority','Priority') !!} --}}
                {{-- {!! Form::select('priority', [''=>'Select Below'], '', ['class' => 'form-control required', 'id' => 'priority']) !!} --}}
                {{-- {!! $errors->first('priority','<span class="help-block">:message</span>') !!} --}}
                {{-- </span> --}}
                <div class="is_user col-md-3 form-group hidden {{ $errors->has('is_user') ? 'has-error' : '' }}">
                    {!! Form::label('is_user', 'Select desk user') !!}<br>
                    {{-- <span id="is_user"></span> --}}
                    {!! Form::select('is_user', ['' => 'Select user'], '', ['class' => 'form-control', 'id' => 'is_user']) !!}
                    {!! $errors->first('is_user', '<span class="help-block">:message</span>') !!}
                </div>
            </div>

            <div class="col-md-3 form-group hidden {{ $errors->has('desk_id') ? 'has-error' : '' }}" id="is_meeting">
                {!! Form::label('Meeting Number', '') !!}
                {!! Form::select('board_meeting_id', ['' => 'Select Below'], '', ['class' => 'form-control required', 'id' => 'meeting_number']) !!}
            </div>

            <div class="col-md-3 form-group {{ $errors->has('desk_id') ? 'has-error' : '' }}">
                <label for="attach_file">Attach file
                    <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title=""
                       data-original-title="To select multiple files, hold down the CTRL or SHIFT key while selecting."></i>
                    <span class="text-danger" style="font-size: 9px; font-weight: bold">[File: *.pdf | Maximum 2
                        MB]</span></label>
                {!! Form::file('attach_file[]', ['class' => 'form-control input-md', 'id' => 'attach_file_id', 'multiple' => true, 'accept' => 'application/pdf', 'onchange' => 'uploadDocumentProcess(this.id)']) !!}
                {!! $errors->first('attach_file', '<span class="help-block">:message</span>') !!}
            </div>

            <div class="col-md-3 form-group hidden {{ $errors->has('desk_id') ? 'has-error' : '' }}" id="pin_number">
                {!! Form::label('Enter Pin Number', '') !!}
                <input class="form-control input-md col-sm " type="text" name="pin_number">
                <span class="text-danger" style="font-size: 10px; font-weight: bold">Please check your email or phone
                    number</span>
            </div>

            <div class="col-md-3 form-group hidden" id="basic_salary">
                {!! Form::label('basic_salary', 'Minimum range of basic salary') !!}
                <input class="form-control required input-md col-sm onlyNumber" value="{{ $appInfo->basic_salary }}"
                       type="text" name="basic_salary">
                {!! $errors->first('basic_salary', '<span class="help-block">:message</span>') !!}
            </div>
        </div>



        <div class="col-md-3 form-group hidden {{ $errors->has('ref_no') ? 'has-error' : '' }}" id="is_ref_no">
            {!! Form::label('Reference Number', '', ['class' => 'required-star']) !!}
            {!! Form::text('ref_no', '', ['class' => 'form-control required', 'id' => 'ref_no']) !!}
        </div>

        <div class="col-md-3 form-group hidden {{ $errors->has('is_incorporation') ? 'has-error' : '' }}"
             id="is_incorporation">
            {!! Form::label('Incorporation Number', '', ['class' => 'required-star']) !!}
            {!! Form::text('incorporation_number', '', ['class' => 'form-control required', 'id' => 'incorporation_number']) !!}
        </div>

        <div class="col-md-3 form-group hidden {{ $errors->has('is_etin') ? 'has-error' : '' }}" id="is_etin">
            {!! Form::label('etin Number', '', ['class' => 'required-star']) !!}
            {!! Form::text('etin_number', '', ['class' => 'form-control required', 'id' => 'etin_no']) !!}
        </div>


        <div class="col-md-3 form-group hidden {{ $errors->has('is_tl') ? 'has-error' : '' }}" id="is_tl">
            {!! Form::label('Trade License Number', '', ['class' => 'required-star']) !!}
            {!! Form::text('tl_number', '', ['class' => 'form-control required', 'id' => 'is_tl']) !!}
        </div>

        <div class="col-md-3 form-group hidden {{ $errors->has('is_accno') ? 'has-error' : '' }}" id="is_accno">
            {!! Form::label('Account Number', '', ['class' => 'required-star']) !!}
            {!! Form::text('acc_number', '', ['class' => 'form-control required', 'id' => 'is_accno']) !!}
        </div>

        <div class="col-md-3 form-group hidden {{ $errors->has('is_branch') ? 'has-error' : '' }}" id="is_branch">
            {!! Form::label('Branch Name', '', ['class' => 'required-star']) !!}
            {!! Form::text('branch_name', '', ['class' => 'form-control required', 'id' => 'is_branch']) !!}
        </div>

        <div class="col-md-3 form-group hidden {{ $errors->has('is_reg') ? 'has-error' : '' }}" id="is_reg">
            {!! Form::label('Registration Number', '', ['class' => 'required-star']) !!}
            {!! Form::text('reg_number', '', ['class' => 'form-control required', 'id' => 'is_reg']) !!}
        </div>

        {{-- AdD-on form div --}}
        <div class="row">
            <div id="FormDiv"></div>
        </div>

        {{-- <span class="col-md-3" style="margin-top: 28px;"> --}}
        {{-- <button type="button" class="btn btn-warning" id="request_shadow_file">Request for shadow file</button> --}}
        {{-- </span> --}}

        <div id="approval_copy_remarks_area" class="hidden">
            <br />
            <div class="row">
                <div class="col-md-12 form-group {{ $errors->has('approval_copy_remarks') ? 'has-error' : '' }}">
                    <label for="approval_copy_remarks">Approval copy remarks <span class="text-danger"
                                                                                   style="font-size: 9px; font-weight: bold">(Maximum length 250)</span></label>
                    {!! Form::textarea('approval_copy_remarks', null, ['class' => 'form-control maxTextCountDown', 'id' => 'approval_copy_remarks', 'placeholder' => 'Enter approval copy remarks', 'data-charcount-maxlength' => '240', 'size' => '10x2']) !!}
                    {!! $errors->first('approval_copy_remarks', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12 form-group {{ $errors->has('remarks') ? 'has-error' : '' }}">
                <label for="remarks">Remarks <span class="text-danger"
                                                   style="font-size: 9px; font-weight: bold">(Maximum length 5000)</span></label>
                {!! Form::textarea('remarks', !in_array($appInfo->status_id, [1]) ? $appInfo->process_desc : '', ['class' => 'form-control maxTextCountDown', 'id' => 'remarks', 'placeholder' => 'Enter Remarks', 'data-charcount-maxlength' => '5000', 'size' => '10x2']) !!}
                {!! $errors->first('remarks', '<span class="help-block">:message</span>') !!}
            </div>
        </div>

        {{-- <div class="row">
            <div class="col-md-12 form-group {{ $errors->has('remarks') ? 'has-error' : '' }}">
                <label for="remarks">Remarks <span class="text-danger" style="font-size: 9px; font-weight: bold">(Maximum length 5000)</span></label>
                {!! Form::textarea('mainInput', '', ['class' => 'form-control', 'id' => 'mainInput', 'size' => '10x2']) !!}
                {!! Form::textarea('remarks', !in_array($appInfo->status_id, [1]) ? $appInfo->process_desc : '', ['class' => 'form-control maxTextCountDown', 'id' => 'remarks', 'placeholder' => 'Enter Remarks', 'data-charcount-maxlength' => '5000', 'size' => '10x2']) !!}
                {!! $errors->first('remarks', '<span class="help-block">:message</span>') !!}
            </div>
        </div> --}}

        <div class="row">
            <div class="col-sm-7">
                @if ($session_get == 'batch_update')

                    <div class="col-sm-10">
                        <i style="color: #a94442">
                            You are processing {{ $total_process_app }} of {{ $total_selected_app }} application
                            in
                            batch.
                            <br>
                            Tracking no. of next application is.{{ $next_app_info }}</i>
                    </div>
                @endif
            </div>

            @if ($session_get == 'batch_update')
                <div class="col-md-3">
                    <input name="is_batch_update" type="hidden"
                           value="{{ \App\Libraries\Encryption::encode('batch_update') }}">
                    <input name="single_process_id_encrypt" type="hidden" value="{{ $single_process_id_encrypt }}"
                           id="process_id">

                    <a class="btn btn-info" @if ($total_process_app == 1) disabled=""
                       @else href="/process/batch-process-previous/{{ $single_process_id_encrypt }}" @endif><i class="fa fa-angle-double-left"></i> Previous</a>
                    <a style="padding: 6px 27px" class="btn btn-info " @if ($total_process_app == $total_selected_app) disabled=""
                       @else  href="/process/batch-process-skip/{{ $single_process_id_encrypt }}" @endif>Next <i class="fa fa-angle-double-right"></i></a>
                </div>
                {{-- <div class="col-md-2"> --}}

                {{-- </div> --}}
            @endif
            <div class="col-sm-2 <?php if ($session_get == null) {
                echo 'col-sm-offset-3';
            } ?>">
                <div class="form-group">
                    {!! Form::button('<i class="fa fa-save"></i> Process', ['type' => 'submit', 'value' => 'Submit', 'class' => 'btn btn-primary btn-block send', 'id' => 'process_btn_id']) !!}
                </div>
            </div>
        </div>


        <?php
        $ut = \App\Libraries\CommonFunction::getUserType();
        $getUserIDfromDepartmentSubdeptWisePermission = CommonFunction::getUserIdByhasDeskDepartmentWisePermission($appInfo->desk_id,$appInfo->approval_center_id, $appInfo->department_id, $appInfo->sub_department_id, $appInfo->process_type_id, $appInfo->user_id, $ut);

        if($getUserIDfromDepartmentSubdeptWisePermission != 0 && $is_delegation == 'is_delegation')
        {
        $DelegateUserInfo = CommonFunction::DelegateUserInfo($getUserIDfromDepartmentSubdeptWisePermission);
        //        dd($DelegateUserInfo);
        ?>
        <span class="col-md-6 col-sm-offset-2">
            <div class="form-group has-feedback">
                {!! Form::hidden('on_behalf_user_id', Encryption::encodeId($DelegateUserInfo->id), ['maxlength' => '500', 'class' => 'form-control input-md']) !!}
                <label class="col-lg-4 text-left"></label>
                <div class="col-lg-8">
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border">On-behalf of</legend>
                        <div class="control-group">
                            <span>Name: {{ $DelegateUserInfo->user_full_name }}</span><br>
                            <span>Designation: {{ $DelegateUserInfo->designation }}</span><br>
                            <span>User Image: <img style="width: 100px;"
                                                   src="{{ $userPic = url() . '/users/upload/' . $DelegateUserInfo->user_pic }}"
                                                   class="profile-user-img img-responsive" alt="Profile Picture" id="uploaded_pic"
                                                   width="150"></span>
                        </div>
                    </fieldset>
                </div>
            </div>
        </span>
        <?php
        }
        ?>
        <div class="clearfix"></div>
    </div>
</div>
{!! Form::close() !!}


{{-- <script src="{{ asset("assets/scripts/jquery.min.js") }}" type="text/javascript"></script> --}}
<script src="{{ asset('vendor/character-counter/jquery.character-counter_v1.0.min.js') }}" type="text/javascript">
</script>
<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
<script>
    $(document).ready(function(){
        let autocomplete = $("#remarks");
        let mainInput = $("#mainInput");
        let foundName = '';
        let predicted = '';
        let apiBusy = false;

        mainInput.on('keyup', function(e) {
            if (mainInput.val() == '') {
                autocomplete.text('');
                return;
            }

            if (e.keyCode == 32) {
                autocomplete.removeAttr('placeholder');
                callMLDataSetAPI(e);
                scrolltobototm();
                return;
            }

            if (e.key == 'Backspace'){
                autocomplete.text('');
                predicted = '';
                apiBusy = true;
                return;
            }

            if(e.key != 'ArrowRight'){
                if (autocomplete.text() != '' && predicted){
                    let first_character = predicted.charAt(0);
                    if(e.key == first_character){
                        predicted = predicted.substr(1);
                        apiBusy = true;
                    }else{
                        autocomplete.text('');
                        apiBusy = false;
                    }
                }else{
                    autocomplete.text('');
                    apiBusy = false;
                }
                return;
            } else {
                if(predicted){
                    if (apiBusy == true){
                        apiBusy = false;
                    }
                    if (apiBusy == false){
                        mainInput.val(foundName);
                        autocomplete.text('');
                    }
                }else{
                    return;
                }
            }
        });

        mainInput.on('keypress', function(e) {
            let sc = 0;
            mainInput.each(function () {
                this.style.height = '0px';
                this.style.height = (this.scrollHeight + 3) + 'px';
                sc = this.scrollHeight;
            });

            autocomplete.each(function () {
                if (sc <= 400){
                    this.style.height = '0px';
                    this.style.height = (sc + 2) + 'px';
                }
            }).on('input', function () {
                this.style.height = '0px';
                this.style.height = (sc + 2) + 'px';
            });
        });

        function callMLDataSetAPI(event) {
            $.ajax({
                url: 'https://ba-ml.oss.net.bd/sr/autocomplete',
                type: 'post',
                data: { input_text: mainInput.val() },
                success: function (response) {
                    let new_text = event.target.value + response.data;
                    autocomplete.text(new_text);
                    foundName = new_text;
                    predicted = response.data;
                },
                error: function (jqXHR, textStatus, errorThrown) {}
            });
        }

        function scrolltobototm() {
            setInterval(function(){
                autocomplete.scrollTop = mainInput.scrollHeight;
            }, 1000);
        }

        mainInput.keydown(function(e) {
            if (e.keyCode === 9) {
                e.preventDefault();
                presstabkey();
            }
        });

        function presstabkey() {
            if(predicted){
                if (apiBusy == true){
                    apiBusy = false;
                }
                if (apiBusy == false){
                    mainInput.val(foundName);
                    autocomplete.text('');
                }
            } else {
                return;
            }
        }
    });
</script>
<script>
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

    $(document).ready(function() {


        // Datepicker Plugin initialize
        var today = new Date();
        var yyyy = today.getFullYear();
        var mm = today.getMonth();
        var dd = today.getDate();
        $('.datepicker').datetimepicker({
            viewMode: 'days',
            format: 'DD-MMM-YYYY',
            maxDate: '01/01/' + (yyyy + 150),
            minDate: moment(),
        });

        $('.datepickerMemo').datetimepicker({
            viewMode: 'days',
            format: 'DD-MMM-YYYY',
            maxDate: '01/01/' + (yyyy + 150),
        });


        /**
         * Batch Form Validate
         * @type {jQuery}
         */
        $("#batch-process-form").validate({
            errorPlacement: function() {
                return false;
            },
            submitHandler: function(form) {
                // This submitHandler() function will only work when the form is valid.
                var conditional_remarks = document.getElementsByName("approval_copy_remarks");
                var process_id = "{{ $appInfo->process_type_id }}";
                var status_id = $('#application_status').val();

                if (status_id == '25' && conditional_remarks.length > 0 && conditional_remarks[0]
                    .value.trim()) {
                    swal({
                        title: 'Are you sure regarding the issuing letter mentioning with the remarks?',
                        text: "You won't be able to revert this!",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes'
                    }).then((result) => {
                        if (result.value) {
                            form.submit();
                        }
                    });
                } else if (process_id == '2' || process_id == '3' || process_id == '6' ||
                    process_id == '7') {
                    // Compare start_date and end_date
                    // WPN = 2, WPE = 3, OPN = 6, OPE = 7, valid form submit
                    var startDate = document.getElementById('start_date').value;
                    var endDate = document.getElementById('end_date').value;

                    var actualStartDate = new Date(startDate.replace(/-/g,
                        ' ')); // convert to actual date
                    var actualEndDate = new Date(endDate.replace(/-/g,
                        ' ')); // convert to actual date

                    if ((Date.parse(actualStartDate) > Date.parse(actualEndDate))) {
                        $('#end_date').addClass('error').removeClass('valid');
                        return false;
                    } else {
                        form.submit();
                    }
                } else {
                    form.submit();
                }
            }
        });

        /**
         * load Process status(Send to desk) on select apply status
         * @type {jQuery}
         */
        $("#application_status").change(function() {
            var self = $(this);
            var statusId = $('#application_status').val();
            var process_type_id = "{{ $appInfo->process_type_id }}";

            if (statusId == 25) {
                document.getElementById('approval_copy_remarks_area').classList.remove('hidden');
            } else {
                document.getElementById('approval_copy_remarks_area').classList.add('hidden');
            }

            if ((statusId == 5 || statusId == 15 || statusId == 32) && (process_type_id != 5 || process_type_id != 9)) { // 5 = WPC, 9 = OPC
                document.getElementById('resend_deadline_field').classList.remove('hidden');
            } else {
                document.getElementById('resend_deadline_field').classList.add('hidden');
            }

            var cat_id = $('#cat_id').val();
            if (statusId !== '') {
                //process btn disable
                $("#process_btn_id").prop("disabled", true);

                $(this).after('<span class="loading_data">Loading...</span>');
                $.ajax({
                    type: "POST",
                    url: "{{ url('process-path/get-desk-by-status') }}",
                    data: {
                        _token: $('input[name="_token"]').val(),
                        process_list_id: $('input[name="process_list_id"]').val(),
                        status_from: $('input[name="status_from"]').val(),
                        cat_id: cat_id,
                        statusId: statusId
                    },
                    success: function(response) {

                        var option = '<option value="">Select One</option>';

                        var countDesk = 0;

                        if (response.responseCode == 1) {
                            if (response.pin_number == 1) {
                                $('#pin_number').removeClass('hidden');
                                $('#pin_number').children('input').addClass('required');
                                $('#pin_number').children('input').attr('disabled', false);
                            } else {
                                $('#pin_number').addClass('hidden');
                                $('#pin_number').children('input').removeClass('required');
                                $('#pin_number').children('input').attr('disabled', true);

                            }

                            var process_type_id = '{{ $appInfo->process_type_id }}';
                            // alert(response.chk_sts);
                            if (process_type_id == 107 && response.chk_sts == 10) {
                                $('#is_ref_no').removeClass('hidden');
                                $('#is_incorporation').removeClass('hidden');
                                $('#is_incorporation').addClass('required');
                                $('#is_ref_no').addClass('required');
                            } else if (process_type_id == 106 && response.chk_sts == 10) {
                                $('#is_ref_no').removeClass('hidden');
                                $('#is_ref_no').addClass('required');
                                $('#is_etin').removeClass('hidden');
                                $('#is_etin').addClass('required');
                            } else if (process_type_id == 105 && response.chk_sts == 10) {
                                $('#is_ref_no').removeClass('hidden');
                                $('#is_ref_no').addClass('required');
                                $('#is_tl').removeClass('hidden');
                                $('#is_tl').addClass('required');
                            } else if (process_type_id == 103 && response.chk_sts == 10) {
                                $('#is_ref_no').removeClass('hidden');
                                $('#is_ref_no').addClass('required');
                                $('#is_accno').removeClass('hidden');
                                $('#is_accno').addClass('required');
                                $('#is_branch').removeClass('hidden');
                                $('#is_branch').addClass('required');
                            } else if (process_type_id == 104 && response.chk_sts == 10) {
                                $('#is_ref_no').removeClass('hidden');
                                $('#is_ref_no').addClass('required');
                                $('#is_reg').removeClass('hidden');
                                $('#is_reg').addClass('required');
                            } else {
                                $('#is_ref_no').addClass('hidden');
                                $('#is_incorporation').addClass('hidden');
                                $('#is_incorporation').removeClass('required');
                                $('#is_ref_no').removeClass('required');
                                $('#is_etin').addClass('hidden');
                                $('#is_etin').removeClass('required');
                                $('#is_tl').addClass('hidden');
                                $('#is_tl').removeClass('required');
                                $('#is_accno').addClass('hidden');
                                $('#is_accno').removeClass('required');
                                $('#is_branch').addClass('hidden');
                                $('#is_branch').removeClass('required');
                                $('#is_reg').addClass('hidden');
                                $('#is_reg').removeClass('required');
                            }

                            //meeting number showing if available
                            var optionMeetingNumber =
                                '<option value="">Select One</option>';
                            if (response.meeting_number.length > 0) {
                                $('#is_meeting').removeClass('hidden');
                                $('#is_meeting').children('input').addClass('required');
                                $('#is_meeting').children('input').attr('disabled', false);

                                $.each(response.meeting_number, function(id, value) {
                                    optionMeetingNumber += '<option value="' + value
                                            .id + '">' + value.meting_number + "(" +
                                        value.meting_date + ")" + '</option>';
                                });
                                $("#meeting_number").html(optionMeetingNumber);

                            } else {
                                if (response.chk_sts == 19) {
                                    $("#meeting_number").html(optionMeetingNumber);
                                    $('#is_meeting').removeClass('hidden');
                                    $('#is_meeting').children('input').addClass('required');
                                    $('#is_meeting').children('input').attr('disabled',
                                        false);
                                } else {
                                    $("#meeting_number").html('');
                                    $('#is_meeting').addClass('hidden');
                                    $('#is_meeting').children('input').removeClass(
                                        'required');
                                    $('#is_meeting').children('input').attr('disabled',
                                        true);
                                    //$('#basic_salary').children('input').removeClass('required');
                                    //$('#basic_salary').addClass('hidden');
                                }

                            }
                            // end meeting number showing if available

                            $('#FormDiv').html(response.html);
                            var option_selected = ((Object.keys(response.data).length ==
                                1) ? "selected" : "");
                            $.each(response.data, function(id, value) {
                                countDesk++;
                                option += '<option ' + option_selected +
                                    ' value="' + id + '">' + value + '</option>';
                            });
                            // Setup required field about remarks field
                            if (response.remarks == 1 || statusId == 5 || statusId == 6) {
                                $("#remarks").addClass('required');
                                $('#is_remarks_required').val(1);
                            } else {
                                $("#remarks").removeClass('required');
                                $('#is_remarks_required').val('');
                            }

                            // Conditional approved remarks
                            if (response.conditional_approved_remarks.length > 0) {
                                $("#remarks").text(response.conditional_approved_remarks);
                            }

                            // Setup required field about remarks field
                            if (response.file_attachment == 1) {
                                $("#attach_file").addClass('required');
                                $('#is_file_required').val(response.file_attachment);
                            } else {
                                $("#attach_file").removeClass('required');
                            }

                        }
                        $("#desk_status").html(option);

                        if (option_selected) {
                            $("#desk_status").trigger("change");
                        }

                        self.next().hide();
                        if (countDesk == 0) {
                            $('.dd_id').removeClass('required');
                            $('#sendToDeskOfficer').css('display', 'none');

                            //meeting date remove
                            // $("#meeting_date").val('');
                            // $("#meeting_date").removeClass('required');
                            // $("#is_calender").addClass('hidden');
                        } else {
                            $('.dd_id').addClass('required');
                            $('#sendToDeskOfficer').css('display', 'block');
                        }
                        //process btn Enable
                        $("#process_btn_id").prop("disabled", false);
                    }
                });
            }

            //Basic salary show for WPN, WPE
            var process_id = "{{ $appInfo->process_type_id }}";

            var basicStatus = ['8', '9', '15', '19'];
            var basicRequiredFlag = basicStatus.includes(statusId);

            var department_id = "{{ $appInfo->department_id }}";
            if (department_id == 1 && basicRequiredFlag == true && (process_id == 2 || process_id ==
                3)) {
                $("#basic_salary").removeClass('hidden');
                $('#basic_salary').children('input').addClass('required');
            } else {
                $("#basic_salary").addClass('hidden');
                $('#basic_salary').children('input').removeClass('required');
            }


        });

        {{-- $("#meeting_number").change(function () { --}}
        {{-- var meeting_id = $(this).val(); --}}
        {{-- var process_type_id = "{{$appInfo->process_type_id}}"; --}}
        {{-- var self = $(this); --}}
        {{-- if (meeting_id != '' && (process_type_id == 2 ||process_type_id == 3 || process_type_id == 4 )) { --}}
        {{-- $("#basic_salary").removeClass('hidden'); --}}
        {{-- }else{ --}}
        {{-- $("#basic_salary").addClass('hidden'); --}}
        {{-- } --}}
        {{-- }); --}}


        /**
         * load apply status list on load page
         * @type {jQuery}
         */
        var application_id = $("#application_id").val();
        var process_list_id = $("#process_list_id").val();
        var cat_id = $("#cat_id").val();
        var curr_process_status_id = $("#curr_process_status_id").val();
        
        $.ajaxSetup({
            async: false
        });
        var _token = $('input[name="_token"]').val();
        var delegate = '{{ @$delegated_desk }}';
        var state = false;
        $.post('/process-path/ajax/load-status-list', {
            curr_process_status_id: curr_process_status_id,
            application_id: application_id,
            process_list_id: process_list_id,
            cat_id: cat_id,
            delegate: delegate,
            _token: _token
        }, function(response) {

            if (response.responseCode == 1) {
                var option = '';
                option += '<option selected="selected" value="">Select Below</option>';
                $.each(response.data, function(id, value) {
                    // select suggested desk
                    var selected = "";
                    if (response.suggested_status === parseInt(value.id)) {
                        selected = "selected";
                    }
                    option += '<option ' + selected + ' value="' + value.id + '">' + value
                        .status_name + '</option>';
                });

                {{-- var PriorityOption = '<option value="">Select One</option>'; --}}
                {{-- var CurrentPriority = '{{ $appInfo->priority}}'; --}}
                {{-- $.each(response.priority, function (id, value) { --}}
                {{-- var selected = ""; --}}
                {{-- if (id == CurrentPriority) --}}
                {{-- selected = "selected"; --}}
                {{-- PriorityOption += '<option ' + selected + ' value="' + id + '">' + value + '</option>'; --}}
                {{-- }); --}}

                $("#application_status").html(option);
                // $("#priority").html(PriorityOption);
                $("#application_status").trigger("change");
                $("#application_status").focus();
            } else if (response.responseCode == 5) {
                alert('Without verification, application can not be processed');
                break_for_pending_verification = 1;
                option = '<option selected="selected" value="">Select Below</option>';
                $("#application_status").html(option);
                $("#application_status").trigger("change");
                return false;
            } else {
                $('#status_id').html('Please wait');
            }
        });
        $.ajaxSetup({
            async: true
        });

        $("#desk_status").change(function() {
            var self = $(this);
            var desk_id = $(this).val();
            var cat_id = $("#cat_id").val();
            var application_status = $('#application_status').val();
            if (desk_id != '') {
                $(this).after('<span class="loading_data">Loading...</span>');
                //process btn disable
                $("#process_btn_id").prop("disabled", true);

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ url('process-path/get-user-by-desk') }}",
                    data: {
                        _token: $('input[name="_token"]').val(),
                        desk_to: desk_id,
                        status_from: $('input[name="status_from"]').val(),
                        desk_from: $('input[name="desk_from"]').val(),
                        statusId: application_status,
                        cat_id: cat_id,
                        process_type_id: "{{ \App\Libraries\Encryption::encodeId($appInfo->process_type_id) }}",
                        department_id: "{{ \App\Libraries\Encryption::encodeId($appInfo->department_id) }}",
                        sub_department_id: "{{ \App\Libraries\Encryption::encodeId($appInfo->sub_department_id) }}",
                        approval_center_id: "{{ \App\Libraries\Encryption::encodeId($appInfo->approval_center_id) }}",
                        app_id: "{{ \App\Libraries\Encryption::encodeId($appInfo->ref_id) }}"
                    },
                    success: function(response) {
                        var option = '<option value="">Select One</option>';
                        //                        var option = '';
                        var countUser = 0;
                        var option_selected = ((Object.keys(response.data).length == 1) ?
                            "selected" : "");
                        $.each(response.data, function(id, value) {
                            countUser++;
                            //option += '<label><input type="radio" class="required" name="is_user" value="' + value.user_id + '">' + value.user_full_name + '</label><br>';
                            option += '<option ' + option_selected + ' value="' +
                                value.user_id + '">' + value.user_full_name +
                                '</option>'
                        });
                        self.next().hide();
                        if (countUser == 0) {
                            $('#is_user').removeClass('required');
                            $(".is_user").addClass('hidden');
                        } else {
                            console.log(countUser);
                            $("#is_user").html(option);
                            $('#is_user').addClass('required');
                            $(".is_user").removeClass('hidden');
                        }
                        //process btn enable
                        $("#process_btn_id").prop("disabled", false);
                    }
                });
            }
        });
    });


    /**
     * Check application verification and process time
     * if the user have process permission
     * @type {jQuery}
     */
    {{-- @if (\App\Libraries\CommonFunction::getUserType() == '4x404' && in_array($appInfo->desk_id, [1, 2, 3, 4, 5])) --}}
    @if ($hasDeskDepartmentWisePermission && \App\Libraries\CommonFunction::getUserType() == '4x404')

    function getVerificationSession() {
        var setVerificationSession = '';
        var data_verification = $("#data_verification").val();
        var process_list_id = $("#process_list_id").val();
        $.get("{{ url('process-path/check-process-validity') }}",
            {
                data_verification: data_verification,
                process_list_id: process_list_id
            },
            function (data, status) {
                if (data.responseCode == 1) {
                    setVerificationSession = setTimeout(getVerificationSession, 120000);
                } else {
                    alert('Sorry, Data has been updated by another user.');
                    window.location.href =
                        "{{ url($moduleName . '/list/' . \App\Libraries\Encryption::encodeId($appInfo->process_type_id)) }}";
                }
            });
    }

    setVerificationSession = setTimeout(getVerificationSession, 120000);
    @endif

    var setSession = '';

    // This function is call two times from here and plane.blade
    //    function getSession() {
    //        $.get("/users/get-user-session", function (data, status) {
    //            if (data.responseCode == 1) {
    //                setSession = setTimeout(getSession, 8000);
    //            } else {
    //                alert('Your session has been closed. Please login again');
    //                window.location.replace('/login');
    //            }
    //        });
    //    }
    //
    //    setSession = setTimeout(getSession, 10000);

    $('.maxTextCountDown').characterCounter();
</script>