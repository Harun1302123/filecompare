{!! Form::open(array('url' => '/bida-registration-amendment/apc-data-update','method' => 'post', 'class' => 'form-horizontal smart-form','id'=>'apcEditForm',
        'enctype' =>'multipart/form-data', 'files' => 'true', 'role' => 'form')) !!}

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title" id="myModalLabel"> Edit Annual Production Capacity</h4>
</div>

<div class="modal-body">
    <div class="errorMsg alert alert-danger alert-dismissible hidden">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>
    <div class="successMsg alert alert-success alert-dismissible hidden">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>

    <div class="table-responsive">
        <table width="100%" id="apcTableId" class="table table-bordered">

            <div class="alert alert-success">

                <strong>Add :</strong> Add a new Production Capacity for Existing or Proposed information.<br>
                <strong>Edit :</strong>  Edit/ update a Production Capacity for proposed information.<br>
                <strong>Remove :</strong>  If you Remove any Production Capacity from your list, you won't be able to retrieve this.<br>
            </div>

            <tr>
                <td class="bg-yellow" colspan="5">Existing information (Latest BIDA Reg. Info.)</td>
                <td class="bg-green" colspan="5">Proposed information</td>
            </tr>
            <tr>
                <td class="light-yellow">Name of Product</td>
                <td class="light-yellow">Unit of Quantity</td>
                <td class="light-yellow">Quantity</td>
                <td class="light-yellow">Price (USD)</td>
                <td class="light-yellow">Sales Value in BDT (million)</td>

                <td class="light-green">Name of Product</td>
                <td class="light-green">Unit of Quantity</td>
                <td class="light-green">Quantity</td>
                <td class="light-green">Price (USD)</td>
                <td class="light-green">Sales Value in BDT (million)</td>
                <td>Action</td>
                <td>#</td>
            </tr>

            <tr>
                <input type="hidden" name="apc_id" value="{{ $get_apc_data->id }}">
                <td class="light-yellow">
                    {!! Form::text("product_name", $get_apc_data->product_name,['class'=>'form-control input-md', 'id' => 'apc_product_name0']) !!}
                    {!! $errors->first('product_name','<span class="help-block">:message</span>') !!}
                </td>
                <td class="light-yellow">
                    {!! Form::select("quantity_unit",$productUnit, $get_apc_data->quantity_unit,['class'=>'form-control input-md quantity-unit', 'id' => 'quantity_unit0']) !!}
                    {!! $errors->first('quantity_unit','<span class="help-block">:message</span>') !!}
                </td>
                <td class="light-yellow">
                    {!! Form::text("quantity", $get_apc_data->quantity,['class'=>'form-control input-md', 'id' => 'apc_quantity0']) !!}
                    {!! $errors->first('quantity','<span class="help-block">:message</span>') !!}
                </td>
                <td class="light-yellow">
                    {!! Form::text("price_usd", $get_apc_data->price_usd,['class'=>'form-control input-md', 'id' => 'apc_price_usd0']) !!}
                    {!! $errors->first('price_usd','<span class="help-block">:message</span>') !!}
                </td>
                <td class="light-yellow">
                    {!! Form::text("price_taka", $get_apc_data->price_taka,['class'=>'form-control input-md', 'id' => 'apc_value_taka0']) !!}
                    {!! $errors->first('price_taka','<span class="help-block">:message</span>') !!}
                </td>


                <td class="light-green">
                    {!! Form::text("n_product_name", $get_apc_data->n_product_name, ['class'=>'form-control input-md', 'id' => 'n_apc_product_name0']) !!}
                    {!! $errors->first('n_product_name','<span class="help-block">:message</span>') !!}
                </td>
                <td class="light-green">
                    {!! Form::select("n_quantity_unit",$productUnit, $get_apc_data->n_quantity_unit, ['class'=>'form-control input-md pro-quantity-unit', 'id' => 'n_quantity_unit0']) !!}
                    {!! $errors->first('n_quantity_unit','<span class="help-block">:message</span>') !!}
                </td>
                <td class="light-green">
                    {!! Form::text("n_quantity", $get_apc_data->n_quantity,['class'=>'form-control input-md', 'id' => 'n_apc_quantity0']) !!}
                    {!! $errors->first('n_quantity','<span class="help-block">:message</span>') !!}
                </td>
                <td class="light-green">
                    {!! Form::text("n_price_usd", $get_apc_data->n_price_usd,['class'=>'form-control input-md', 'id' => 'n_apc_price_usd0']) !!}
                    {!! $errors->first('n_price_usd','<span class="help-block">:message</span>') !!}
                </td>
                <td class="light-green">
                    {!! Form::text("n_price_taka", $get_apc_data->n_price_taka,['class'=>'form-control input-md', 'id' => 'n_apc_value_taka0']) !!}
                    {!! $errors->first('n_price_taka','<span class="help-block">:message</span>') !!}
                </td>

                <td>
                    {!! Form::select("amendment_type", $amendment_type, 'edit',['class'=>'form-control input-md apc-action', 'id' => 'amendment_type0', 'onchange' => 'actionWiseFieldDisable(this, ["apc_product_name0", "quantity_unit0", "apc_quantity0", "apc_price_usd0", "apc_value_taka0"], ["n_apc_product_name0", "n_quantity_unit0", "n_apc_quantity0", "n_apc_price_usd0", "n_apc_value_taka0"])']) !!}
                    {!! $errors->first('action','<span class="help-block">:message</span>') !!}
                </td>
            </tr>

        </table>
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
    $("#amendment_type0").trigger('change');

    $(document).ready(function () {
        $("#apcEditForm").validate({
            errorPlacement: function () {
                return true;
            },
            submitHandler: formSubmit
        });

        var form = $("#apcEditForm"); //Get Form ID
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
                            $("#braModal").modal('hide');
                        });
                        form.trigger("reset");

                        loadAnnualProductionCapacityData(20, 'off');
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
