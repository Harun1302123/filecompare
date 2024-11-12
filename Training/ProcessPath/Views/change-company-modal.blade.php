<?php
if (!ACL::getAccsessRight($aclName, $mode)) {
    die('You have no access right! Contact with system admin for more information.');
}
?>


{!! Form::open(array('url' => '/process/store-change-company','method' => 'post', 'class' => 'form-horizontal smart-form','id'=>'changeCompanyForm',
        'enctype' =>'multipart/form-data', 'files' => 'true', 'role' => 'form')) !!}

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span>
    </button>
    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-th-large"></i> Change organization</h4>
</div>

<div class="modal-body">
    <div class="errorMsg alert alert-danger alert-dismissible hidden">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    </div>
    <div class="successMsg alert alert-success alert-dismissible hidden">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    </div>

    <input type="hidden" name="selected_file" id="selected_file"/>
    <input type="hidden" name="validateFieldName" id="validateFieldName"/>
    <input type="hidden" name="isRequired" id="isRequired"/>

    <div class="form-group">
        <div class="row">
            <div class="col-lg-12 {{$errors->has('change_type') ? 'has-error' : ''}}">
                {!! Form::label('change_type','Change type: ',['class'=>'col-md-4  required-star']) !!}
                <div class="col-md-8 {{$errors->has('change_type') ? 'has-error': ''}}">
                    <label class="radio-inline">{!! Form::radio('change_type','1', false, ['class'=>' required', 'onclick' => 'companyChangeType(this.value)']) !!}
                        Name correction</label>
                    <label class="radio-inline">{!! Form::radio('change_type', '2', false, ['class'=>' required', 'onclick' => 'companyChangeType(this.value)']) !!}
                        Organization transfer</label>
                </div>
            </div>
        </div>
    </div>


    <div class="" id="nameChangeDiv" hidden>
        <div class="form-group">
            <div class="row">
                <div class="col-lg-12 {{$errors->has('company_name_en') ? 'has-error' : ''}}">
                    {!! Form::label('company_name_en','Organization name (English) :',['class'=>'col-md-4  required-star']) !!}
                    <div class="col-md-6">
                        {!! Form::text('company_name_en', $current_company_info->company_name, ['class'=> 'form-control input-sm ']) !!}
                        {!! $errors->first('company_name_en','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-lg-12 {{$errors->has('company_name_bn') ? 'has-error' : ''}}">
                    {!! Form::label('company_name_bn','Organization name (Bangla): ',['class'=>'col-md-4']) !!}
                    <div class="col-md-6">
                        {!! Form::text('company_name_bn', $current_company_info->company_name_bn, ['class'=> 'form-control input-sm ']) !!}
                        {!! $errors->first('company_name_bn','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="" id="companyChangeDiv" hidden>
        <div class="form-group">
            <div class="row">
                <input type="hidden" name="current_company_id" value="{{ $encoded_company_id }}">
                <div class="col-lg-12 {{$errors->has('new_company_id') ? 'has-error' : ''}}">
                    {!! Form::label('new_company_id','Select new organization: ',['class'=>'col-md-4  required-star']) !!}
                    <div class="col-md-6">
                        {!! Form::select('new_company_id', $company_lists, '', ['class'=> 'form-control input-sm', 'id' => 'new_company_id', 'style'=>'width:100%;', 'required', 'placeholder' => 'Select one']) !!}
                        {!! $errors->first('new_company_id','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    {!! Form::label('authorization_letter', 'Authorization letter :', ['class' => 'col-md-4 required-star']) !!}
                    <div class="col-md-6">
                        <input name="authorization_letter<?php echo $decoded_company_id; ?>"
                               class="form-control required"
                               id="file<?php echo $decoded_company_id; ?>" type="file"
                               onchange="uploadDocument('preview_<?php echo $decoded_company_id; ?>', this.id, 'authorization_letter', '1')"/>

                        <br/>
                        <small style="font-size: 9px; font-weight: bold; color: #666363; font-style: italic">
                            [Format: *.PDF | Maximum 3 MB, Application with Name & Signature] </small>
                        <br/>
                        <a target="_blank" href="{{ url('assets/images/sample_auth_letter.png') }}"><i class="fa fa-file" aria-hidden="true"></i> <i>Sample Authorization letter</i></a>

                        <div id="preview_<?php echo $decoded_company_id; ?>">
                            <input type="hidden"
                                   value=""
                                   id="authorization_letter"
                                   name="authorization_letter"
                                   class="required"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer" style="text-align:left;">
    <div class="pull-left">
        {!! Form::button('<i class="fa fa-times"></i> Close', array('type' => 'button', 'class' => 'btn btn-danger', 'data-dismiss' => 'modal')) !!}
    </div>
    <div class="pull-right">
        @if(ACL::getAccsessRight($aclName, $mode))
            <button type="submit" class="btn btn-primary" id="action_btn" name="actionBtn" value="draft">
                <i class="fa fa-chevron-circle-right"></i> Save
            </button>
        @endif
    </div>
    <div class="clearfix"></div>
</div>
{!! Form::close() !!}

<script>
    function companyChangeType(change_type) {
        if (change_type == '1') {
            $("#nameChangeDiv").show('slow');
            $('.nameChangeDivReqField').addClass('required');

            $("#companyChangeDiv").hide('slow');
            $('.companyChangeDivReqField').removeClass('required');
        } else if (change_type == '2') {
            $("#nameChangeDiv").hide('slow');
            $('.nameChangeDivReqField').removeClass('required');

            $("#companyChangeDiv").show('slow');
            $('.companyChangeDivReqField').addClass('required');
        }
    }

    $(document).ready(function () {

        $("#changeCompanyForm").validate({
            errorPlacement: function () {
                return true;
            },
            submitHandler: formSubmit
        });

        //==========Change Company Select2==========
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

        $("#new_company_id").select2({
            matcher: matchCustom,
            minimumInputLength: 3,
            language: {
                inputTooShort: function() {
                    return 'Please search by entering 03 or more characters.';
                }
            },
            tags: false,
        });


        var form = $("#changeCompanyForm"); //Get Form ID
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
                            //location.reload();
                            window.location.href = data.link;
                        });
                        form.trigger("reset");

                    }
                    //=========if data already submitted===========//
                    if (data.error == true) {
                        info_err.hide().empty();
                        info_err.removeClass('hidden').html(data.status);
                        info_err.slideDown('slow');
                        info_err.delay(6000).slideUp(800, function () {
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
            var action = "{{url('/basic-information/upload-auth-letter')}}";

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
</script>