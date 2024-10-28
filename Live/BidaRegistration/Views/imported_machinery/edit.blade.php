{!! Form::open(array('url' => '/bida-registration/update-imported-machinery','method' => 'post', 'class' => 'form-horizontal smart-form','id'=>'machineryForm',
        'enctype' =>'multipart/form-data', 'files' => 'true', 'role' => 'form')) !!}

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title" id="myModalLabel"> Edit Imported Machinery</h4>
</div>

<div class="modal-body">
    <div class="errorMsg alert alert-danger alert-dismissible hidden">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>
    <div class="successMsg alert alert-success alert-dismissible hidden">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>

    <input type="hidden" name="id" value="{{ Encryption::encodeId($imported_machinery->id) }}">
    <div class="col-md-10 col-md-offset-1">
        <div class="form-group">
            <div class="{{$errors->has('l_machinery_imported_name') ? 'has-error': ''}}">
                {!! Form::label('l_machinery_imported_name','Name of machineries',['class'=>'text-left required-star col-md-4']) !!}
                <div class="col-md-8">
                    {!! Form::text('l_machinery_imported_name', $imported_machinery->l_machinery_imported_name, ['class' => 'form-control input-md required']) !!}
                    {!! $errors->first('l_machinery_imported_name','<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="{{$errors->has('l_machinery_imported_qty') ? 'has-error': ''}}">
                {!! Form::label('l_machinery_imported_qty','Quantity',['class'=>'text-left required-star col-md-4']) !!}
                <div class="col-md-8">
                    {!! Form::text('l_machinery_imported_qty', $imported_machinery->l_machinery_imported_qty, ['class' => 'form-control input-md required']) !!}
                    {!! $errors->first('l_machinery_imported_qty','<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="{{$errors->has('l_machinery_imported_unit_price') ? 'has-error': ''}}">
                {!! Form::label('l_machinery_imported_unit_price','Unit prices TK',['class'=>'text-left required-star col-md-4']) !!}
                <div class="col-md-8">
                    {!! Form::text('l_machinery_imported_unit_price', $imported_machinery->l_machinery_imported_unit_price, ['class' => 'form-control input-md required']) !!}
                    {!! $errors->first('l_machinery_imported_unit_price','<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="{{$errors->has('l_machinery_imported_total_value') ? 'has-error': ''}}">
                {!! Form::label('l_machinery_imported_total_value','Total value (Million) TK',['class'=>'text-left required-star col-md-4']) !!}
                <div class="col-md-8">
                    {!! Form::text('l_machinery_imported_total_value', $imported_machinery->l_machinery_imported_total_value, ['class' => 'form-control input-md required']) !!}
                    {!! $errors->first('l_machinery_imported_total_value','<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</div>

<div class="modal-footer" style="text-align:left;">
    <div class="pull-left">
        {!! Form::button('<i class="fa fa-times"></i> Close', array('type' => 'button', 'class' => 'btn btn-danger', 'data-dismiss' => 'modal')) !!}
    </div>
    <div class="pull-right">
        <button type="submit" class="btn btn-primary" id="machinery_create_btn" name="actionBtn" value="draft">
            <i class="fa fa-chevron-circle-right"></i> Save
        </button>
    </div>
    <div class="clearfix"></div>
</div>
{!! Form::close() !!}


<script>


    $(document).ready(function () {
        $("#machineryForm").validate({
            errorPlacement: function () {
                return true;
            },
            submitHandler: formSubmit
        });

        var form = $("#machineryForm"); //Get Form ID
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
</script>
