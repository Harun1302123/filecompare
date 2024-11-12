<style>
    .margin-bottom{
        margin-bottom: 3px;
    }
    .radio-inline{
        padding-top: 0px !important;
    }

</style>
<link rel="stylesheet" href="{{ asset("assets/stylesheets/bootstrap-datetimepicker.css") }}" />
{!! Form::open(array('url' => '/bida-registration/update-director','method' => 'post', 'class' => 'form-horizontal smart-form','id'=>'directorForm',
        'enctype' =>'multipart/form-data', 'files' => 'true', 'role' => 'form')) !!}

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title" id="myModalLabel">
        Edit
        @if($director_list->identity_type == 'nid')
            NID
        @elseif($director_list->identity_type == 'tin')
            ETIN
        @else
            passport
        @endif
        information
    </h4>
</div>

<div class="modal-body">
    <div class="errorMsg alert alert-danger alert-dismissible hidden">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>
    <div class="successMsg alert alert-success alert-dismissible hidden">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>
    <input type="hidden" name="id" value="{{ Encryption::encodeId($director_list->id) }}">

    @if($director_list->identity_type == 'nid')
        <div class="col-md-10 col-md-offset-1">
            <div class="form-group margin-bottom">
                <div class="{{$errors->has('user_nid') ? 'has-error': ''}}">
                    {!! Form::label('user_nid','NID',['class'=>'text-left required-star col-md-3']) !!}
                    <div class="col-md-9">
                        <span class="form-control input-md" style="background:#eee; height: auto;min-height: 30px;">{{ $director_list->nid_etin_passport }}</span>
                    </div>
                </div>
            </div>
            <div class="form-group margin-bottom">
                <div class="{{$errors->has('l_director_name') ? 'has-error': ''}}">
                    {!! Form::label('l_director_name','Name',['class'=>'text-left required-star col-md-3']) !!}
                    <div class="col-md-9">
                        <span class="form-control input-md" style="background:#eee; height: auto;min-height: 30px;">{{ $director_list->l_director_name }}</span>
                    </div>
                </div>
            </div>
            <div class="form-group margin-bottom">
                <div class="{{$errors->has('nid_dob') ? 'has-error': ''}}">
                    {!! Form::label('nid_dob','NID',['class'=>'text-left required-star col-md-3']) !!}
                    <div class="col-md-9">
                        <span class="form-control input-md" style="background:#eee; height: auto;min-height: 30px;">{{ date("Y-m-d", strtotime($director_list->date_of_birth)) }}</span>
                    </div>
                </div>
            </div>
            <div class="form-group margin-bottom">
                {!! Form::label('gender','Gender', ['class'=>'col-md-3 text-left required-star']) !!}
                <div class="col-md-9 {{$errors->has('gender') ? 'has-error': ''}}">
                    <label class="radio-inline">
                        {!! Form::radio('gender', 'Male', (($director_list->gender == 'Male') ? true : false),['class' => '', 'id'=>'gender']) !!}
                        Male
                    </label>
                    <label class="radio-inline">
                        {!! Form::radio('gender', 'Female', (($director_list->gender == 'Female') ? true : false),['class' => '', 'id'=>'gender']) !!}
                        Female
                    </label>
                    <label class="radio-inline">
                        {!! Form::radio('gender', 'Other', (($director_list->gender == 'Other') ? true : false),['class' => '', 'id'=>'gender']) !!}
                        Other
                    </label>
                </div>
            </div>
            <div class="form-group margin-bottom">
                <div class="{{$errors->has('nid_designation') ? 'has-error': ''}}">
                    {!! Form::label('nid_designation','Designation',['class'=>'text-left required-star col-md-3']) !!}
                    <div class="col-md-9">
                        {!! Form::text('nid_designation', $director_list->l_director_designation, ['class' => 'form-control input-md required']) !!}
                        {!! $errors->first('nid_designation','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
            </div>
            <div class="form-group margin-bottom">
                <div class="{{$errors->has('nid_nationality') ? 'has-error': ''}}">
                    {!! Form::label('nid_nationality','Nationality',['class'=>'text-left required-star col-md-3']) !!}
                    <div class="col-md-9">
                        {!! Form::select('nid_nationality', $nationality, $director_list->l_director_nationality, ['class' => 'form-control input-md required']) !!}
                        {!! $errors->first('nid_nationality','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($director_list->identity_type == 'tin')
        <div class="col-md-10 col-md-offset-1">
            <div class="form-group margin-bottom">
                <div class="{{$errors->has('user_tin') ? 'has-error': ''}}">
                    {!! Form::label('user_nid','TIN (Bangladesh)',['class'=>'text-left required-star col-md-3']) !!}
                    <div class="col-md-9">
                        <span class="form-control input-md" style="background:#eee; height: auto;min-height: 30px;">{{ $director_list->nid_etin_passport }}</span>
                    </div>
                </div>
            </div>
            <div class="form-group margin-bottom">
                <div class="{{$errors->has('etin_name') ? 'has-error': ''}}">
                    {!! Form::label('etin_name','Name',['class'=>'text-left required-star col-md-3']) !!}
                    <div class="col-md-9">
                        <span class="form-control input-md" style="background:#eee; height: auto;min-height: 30px;">{{ $director_list->l_director_name }}</span>
                    </div>
                </div>
            </div>
            <div class="form-group margin-bottom">
                <div class="{{$errors->has('tin_dob') ? 'has-error': ''}}">
                    {!! Form::label('tin_dob','NID',['class'=>'text-left required-star col-md-3']) !!}
                    <div class="col-md-9">
                        <span class="form-control input-md" style="background:#eee; height: auto;min-height: 30px;">{{ date("d-M-Y", strtotime($director_list->date_of_birth)) }}</span>
                    </div>
                </div>
            </div>
            <div class="form-group margin-bottom">
                {!! Form::label('gender','Gender', ['class'=>'col-md-3 text-left required-star']) !!}
                <div class="col-md-9 {{$errors->has('gender') ? 'has-error': ''}}">
                    <label class="radio-inline">
                        {!! Form::radio('gender', 'Male', (($director_list->gender == 'Male') ? true : false),['class' => '', 'id'=>'gender']) !!}
                        Male
                    </label>
                    <label class="radio-inline">
                        {!! Form::radio('gender', 'Female', (($director_list->gender == 'Female') ? true : false),['class' => '', 'id'=>'gender']) !!}
                        Female
                    </label>
                    <label class="radio-inline">
                        {!! Form::radio('gender', 'Other', (($director_list->gender == 'Other') ? true : false),['class' => '', 'id'=>'gender']) !!}
                        Other
                    </label>
                </div>
            </div>
            <div class="form-group margin-bottom">
                <div class="{{$errors->has('tin_designation') ? 'has-error': ''}}">
                    {!! Form::label('tin_designation','Designation',['class'=>'text-left required-star col-md-3']) !!}
                    <div class="col-md-9">
                        {!! Form::text('tin_designation', $director_list->l_director_designation, ['class' => 'form-control input-md required']) !!}
                        {!! $errors->first('tin_designation','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
            </div>
            <div class="form-group margin-bottom">
                <div class="{{$errors->has('tin_nationality') ? 'has-error': ''}}">
                    {!! Form::label('tin_nationality','Nationality',['class'=>'text-left required-star col-md-3']) !!}
                    <div class="col-md-9">
                        {!! Form::select('tin_nationality', $nationality, $director_list->l_director_nationality, ['class' => 'form-control input-md required']) !!}
                        {!! $errors->first('tin_nationality','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($director_list->identity_type == 'passport')
        <div class="col-md-10 col-md-offset-1">
            <div class="form-group margin-bottom">
                {!! Form::label('passport_name','Name', ['class'=>'col-md-3 text-left required-star']) !!}
                <div class="col-md-9 {{$errors->has('passport_name') ? 'has-error': ''}}">
                    {!! Form::text('passport_name', !empty($director_list->l_director_name) ? $director_list->l_director_name : '', ['class' => 'form-control input-md required', 'id'=>'passport_name']) !!}
                    {!! $errors->first('passport_name','<span class="help-block">:message</span>') !!}
                </div>
            </div>
            <div class="form-group margin-bottom">
                {!! Form::label('passport_dob','Date of Birth', ['class'=>'col-md-3 text-left required-star']) !!}
                <div class="col-md-9 {{$errors->has('passport_dob') ? 'has-error': ''}}">
                    <div class="passportDOB input-group date">
                        {!! Form::text('passport_dob', (!empty($director_list->date_of_birth) ? date("d-M-Y", strtotime($director_list->date_of_birth)) : ''), ['class' => 'form-control input-md required', 'id'=>'passport_dob']) !!}
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    </div>
                    {!! $errors->first('passport_dob','<span class="help-block">:message</span>') !!}
                </div>
            </div>
            <div class="form-group margin-bottom">
                {!! Form::label('gender','Gender', ['class'=>'col-md-3 text-left required-star']) !!}
                <div class="col-md-9 {{$errors->has('gender') ? 'has-error': ''}}">
                    <label class="radio-inline">
                        {!! Form::radio('gender', 'Male', (($director_list->gender == 'Male') ? true : false),['class' => '', 'id'=>'gender']) !!}
                        Male
                    </label>
                    <label class="radio-inline">
                        {!! Form::radio('gender', 'Female', (($director_list->gender == 'Female') ? true : false),['class' => '', 'id'=>'gender']) !!}
                        Female
                    </label>
                    <label class="radio-inline">
                        {!! Form::radio('gender', 'Other', (($director_list->gender == 'Other') ? true : false),['class' => '', 'id'=>'gender']) !!}
                        Other
                    </label>
                </div>
            </div>
            <div class="form-group margin-bottom">
                {!! Form::label('passport_designation','Designation', ['class'=>'col-md-3 text-left required-star']) !!}
                <div class="col-md-9 {{$errors->has('passport_designation') ? 'has-error': ''}}">
                    {!! Form::text('passport_designation', !empty($director_list->l_director_designation) ? $director_list->l_director_designation : '', ['class' => 'form-control input-md required', 'id'=>'passport_designation']) !!}
                    {!! $errors->first('passport_designation','<span class="help-block">:message</span>') !!}
                </div>
            </div>
            <div class="form-group margin-bottom">
                {!! Form::label('passport_nationality','Nationality', ['class'=>'col-md-3 text-left required-star']) !!}
                <div class="col-md-9 {{$errors->has('passport_nationality') ? 'has-error': ''}}">
                    {!! Form::select('passport_nationality', $nationality, $director_list->l_director_nationality, ['class' => 'form-control input-md required', 'id'=>'passport_nationality']) !!}
                    {!! $errors->first('passport_nationality','<span class="help-block">:message</span>') !!}
                </div>
            </div>
            <div class="form-group margin-bottom">
                {!! Form::label('passport_type','Passport type', ['class'=>'col-md-3 text-left required-star']) !!}
                <div class="col-md-9 {{$errors->has('passport_type') ? 'has-error': ''}}">
                    {!! Form::select('passport_type', ['0'=>'select one','ordinary'=>'Ordinary','diplomatic'=>'Diplomatic','official'=>'Official'], $director_list->passport_type, ['class' => 'form-control input-md required', 'id'=>'passport_type']) !!}
                    {!! $errors->first('passport_type','<span class="help-block">:message</span>') !!}
                </div>
            </div>
            <div class="form-group margin-bottom">
                {!! Form::label('passport_no','Passport No.', ['class'=>'col-md-3 text-left required-star']) !!}
                <div class="col-md-9 {{$errors->has('passport_no') ? 'has-error': ''}}">
                    {!! Form::text('passport_no', !empty($director_list->nid_etin_passport) ? $director_list->nid_etin_passport : '', ['class' => 'form-control input-md required', 'id'=>'passport_no']) !!}
                    {!! $errors->first('passport_no','<span class="help-block">:message</span>') !!}
                </div>
            </div>
            <div class="form-group margin-bottom">
                {!! Form::label('date_of_expiry','Date of expiry', ['class'=>'col-md-3 text-left required-star']) !!}
                <div class="col-md-9 {{$errors->has('date_of_expiry') ? 'has-error': ''}}">
                    <div class="passportDOB input-group date">
                        {!! Form::text('date_of_expiry', (!empty($director_list->date_of_expiry) ? date("d-M-Y", strtotime($director_list->date_of_expiry)) : ''), ['class' => 'form-control input-md required', 'id'=>'date_of_expiry', 'placeholder'=>'dd-mm-yyyy']) !!}
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    </div>
                    {!! $errors->first('date_of_expiry','<span class="help-block">:message</span>') !!}
                </div>
            </div>
{{--            <div class="form-group">--}}
{{--                {!! Form::label('passport_scan_copy','Passport scan copy', ['class'=>'col-md-3 text-left required-star']) !!}--}}
{{--                <div class="col-md-9 {{$errors->has('passport_scan_copy') ? 'has-error': ''}}">--}}
{{--                    <input type="file" id="passport_scan_id" name="passport_scan_copy" value="{{$director_list->passport_scan_copy}}"--}}
{{--                           class="input-md {{ (empty($director_list->passport_scan_copy) ? 'required' : '') }}" onchange="uploadDocument('preview', this.id, 'passport_scan_copy', '0')"/>--}}

{{--                    <div id="preview">--}}
{{--                        <input type="hidden" class="required" id="passport_scan_copy" name="passport_scan_copy"/>--}}
{{--                    </div>--}}
{{--                    </br>--}}
{{--                    @if(!empty($director_list->passport_scan_copy))--}}
{{--                        <a target="_blank" class="btn btn-xs btn-primary documentUrl"--}}
{{--                           href="{{URL::to('/uploads/'.$director_list->passport_scan_copy)}}"--}}
{{--                           title="{{ $director_list->passport_scan_copy }}">--}}
{{--                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>--}}
{{--                            Open File--}}
{{--                        </a>--}}
{{--                    @endif--}}
{{--                    {!! $errors->first('passport_scan_copy','<span class="help-block">:message</span>') !!}--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>
    @endif
    <div class="clearfix"></div>
</div>

<div class="modal-footer" style="text-align:left;">
    <div class="pull-left">
        {!! Form::button('<i class="fa fa-times"></i> Close', array('type' => 'button', 'class' => 'btn btn-danger', 'data-dismiss' => 'modal')) !!}
    </div>
    <div class="pull-right">
        <button type="submit" class="btn btn-primary" id="director_create_btn" name="actionBtn" value="draft">
            <i class="fa fa-chevron-circle-right"></i> Save
        </button>
    </div>
    <div class="clearfix"></div>
</div>
{!! Form::close() !!}

<script>
    //datePicker ....
    var today = new Date();
    var yyyy = today.getFullYear();
    $('.passportDOB').datetimepicker({
        viewMode: 'years',
        format: 'DD-MMM-YYYY',
        // maxDate: '01/01/' + (yyyy - 110),
        // minDate: '01/01/' + (yyyy - 110)
    });

    $('.dobDP').datetimepicker({
        viewMode: 'years',
        format: 'DD-MMM-YYYY',
        maxDate: '01/01/' + (yyyy + 20),
        minDate: '01/01/' + (yyyy - 10)
    });

    $(document).ready(function () {
        $("#directorForm").validate({
            errorPlacement: function () {
                return true;
            },
            submitHandler: formSubmit
        });

        var form = $("#directorForm"); //Get Form ID
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
                    console.log("before send");
                    $("#Duplicated jQuery selector").html('<i class="fa fa-cog fa-spin"></i> Loading...');
                    $("#Duplicated jQuery selector").prop('disabled', true); // disable button
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
                            $("#Duplicated jQuery selector").html('Submit');
                            $("#Duplicated jQuery selector").prop('disabled', false);
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
                            $("#Duplicated jQuery selector").html('Submit');
                            $("#Duplicated jQuery selector").prop('disabled', false);
                        });
                    }
                },
                error: function (data) {
                    var errors = data.responseJSON;
                    $("#Duplicated jQuery selector").prop('disabled', false);
                    console.log(errors);
                    alert('Sorry, an unknown Error has been occured! Please try again later.');
                }
            });
            return false;
        }
    });

    function uploadDocument(targets, id, vField, isRequired) {
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
            if (!(file_size <= 2)) { // maximum file size 2 MB
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
            // document.getElementById("isRequired").value = isRequired;
            // document.getElementById("selected_file").value = id;
            // document.getElementById("validateFieldName").value = vField;
            // document.getElementById(targets).style.color = "red";
            var action = "<?php echo e(url('/bida-registration/upload-document')); ?>";
            $("#" + targets).html('Uploading....');
            var file_data = $("#" + id).prop('files')[0];
            var form_data = new FormData();
            form_data.append('selected_file', id);
            form_data.append('isRequired', isRequired);
            form_data.append('validateFieldName', vField);
            form_data.append('_token', "<?php echo e(csrf_token()); ?>");
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
                    var newInput = $('<label class="saved_file_' + doc_id + '" id="label_' + id + '"><br/><b>File: ' + fileNameArr[l - 1] +
                        ' <a href="javascript:void(0)"></a></b></label>');
                    //var newInput = $('<label id="label_' + id + '"><br/><b>File: ' + fileNameArr[l - 1] + '</b></label>');
                    $("#" + id).after(newInput);
                    //check valid data
                    var validate_field = $('#' + vField).val();
                    if (validate_field == '') {
                        document.getElementById(id).value = '';
                    }
                }
            });
        } catch (err) {
            document.getElementById(targets).innerHTML = "Sorry! Something Wrong.";
        }
    }
</script>
