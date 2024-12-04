{!! Form::open(array('url' => '/bida-registration/update-annual-production','method' => 'post', 'class' => 'form-horizontal smart-form','id'=>'annualProductionForm',
        'enctype' =>'multipart/form-data', 'files' => 'true', 'role' => 'form')) !!}

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span>
    </button>
    <h4 class="modal-title" id="myModalLabel"> Edit Annual Production</h4>
</div>

<div class="modal-body">
    <div class="errorMsg alert alert-danger alert-dismissible hidden">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    </div>
    <div class="successMsg alert alert-success alert-dismissible hidden">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    </div>

    <input type="hidden" name="app_id" value="{{ Encryption::encodeId($apc_product->id) }}">
    <input type="hidden" name="process_type_id" value="{{ $process_type_id }}">

    <div class="form-group">
        <div class="row">
            <div class="col-md-offset-1 col-md-10 {{$errors->has('em_product_name') ? 'has-error': ''}}">
                {!! Form::label('em_product_name','Name of Product',['class'=>'col-md-3 text-left']) !!}
                <div class="col-md-7">
                    {!! Form::text('em_product_name', $apc_product->product_name, ['class' => 'form-control  input-md ','id'=>'em_product_name']) !!}
                    {!! $errors->first('em_product_name','<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-md-offset-1 col-md-10 {{$errors->has('em_quantity_unit') ? 'has-error': ''}}">
                {!! Form::label('em_quantity_unit','Unit of Quantity',['class'=>'col-md-3 text-left']) !!}
                <div class="col-md-7">
                    {!! Form::select('em_quantity_unit', $productUnit, $apc_product->quantity_unit,['class' => 'form-control  input-md ','id'=>'em_quantity_unit']) !!}
                    {!! $errors->first('em_quantity_unit','<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-md-offset-1 col-md-10 {{$errors->has('em_quantity') ? 'has-error': ''}}">
                {!! Form::label('em_quantity','Quantity',['class'=>'col-md-3 text-left']) !!}
                <div class="col-md-7">
                    {!! Form::number('em_quantity', $apc_product->quantity, ['class' => 'form-control  input-md ','id'=>'em_quantity']) !!}
                    {!! $errors->first('em_quantity','<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-md-offset-1 col-md-10 {{$errors->has('em_price_usd') ? 'has-error': ''}}">
                {!! Form::label('em_price_usd','Price (USD)',['class'=>'col-md-3 text-left']) !!}
                <div class="col-md-7">
                    {!! Form::number('em_price_usd', $apc_product->price_usd, ['class' => 'form-control  input-md ','id'=>'em_price_usd']) !!}
                    {!! $errors->first('em_price_usd','<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-md-offset-1 col-md-10 {{$errors->has('em_value_taka') ? 'has-error': ''}}">
                {!! Form::label('em_value_taka','Sales Value in BDT (million)',['class'=>'col-md-3 text-left']) !!}
                <div class="col-md-7">
                    {!! Form::number('em_value_taka', $apc_product->price_taka, ['class' => 'form-control  input-md ','id'=>'em_value_taka']) !!}
                    {!! $errors->first('em_value_taka','<span class="help-block">:message</span>') !!}
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
        <button type="submit" class="btn btn-primary" id="" name="actionBtn" value="update">
            <i class="fa fa-chevron-circle-right"></i> update
        </button>
    </div>
    <div class="clearfix"></div>
</div>
{!! Form::close() !!}


<script>
    $(document).ready(function () {
        $("#annualProductionForm").validate({
            errorPlacement: function () {
                return true;
            },
            submitHandler: formSubmit
        });

        var form = $("#annualProductionForm"); //Get Form ID
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
                            // window.location.href = data.link;
                            $("#irc1stadhocModal").modal('hide');

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
