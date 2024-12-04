<style>
    label.error {
        display: inline-block !important;
        border: none !important;
    }
</style>
{!! Form::open(array('url' => '/irc-recommendation-second-adhoc/store-raw-material','method' => 'post', 'class' => 'form-horizontal smart-form','id'=>'rawMaterialForm',
        'enctype' =>'multipart/form-data', 'files' => 'true', 'role' => 'form')) !!}

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span>
    </button>
    <h4 class="modal-title" id="myModalLabel"> Add Raw Material for: <strong>{{ $annual_production_capacity->product_name }}</strong></h4>
</div>

<div class="modal-body">
    <div class="errorMsg alert alert-danger alert-dismissible hidden">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    </div>
    <div class="successMsg alert alert-success alert-dismissible hidden">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    </div>

    <input type="hidden" name="app_id" value="{{ $app_id }}">
    <input type="hidden" name="apc_product_id" value="{{ $id }}">

    <div class="row form-group" id="unit_of_product">
        <div class="col-md-6 {{$errors->has('unit_of_product') ? 'has-error': ''}}">
            {!! Form::label('unit_of_product','Unit of Product:', ['class'=>'required-star col-md-4 text-left']) !!}
            <div class="col-md-8">
                {!! Form::select('unit_of_product', [1=>'1', 10=>'10', 100=>'100', 1000=>'1000'], $annual_production_capacity->unit_of_product, ["placeholder" => "Select One", 'class' => 'form-control input-md required']) !!}
                {!! $errors->first('unit_of_product','<span class="help-block">:message</span>') !!}
            </div>
        </div>
    </div>
    <br>
    <div class="table-responsive">
        <table id="rawMaterialTable" class="table table-striped table-bordered dt-responsive" cellspacing="0"width="100%">
            <thead>
            <tr>
                <th class="required-star">Name</th>
                <th class="required-star">HS Code</th>
                <th class="required-star">Quantity</th>
                <th class="required-star">Unit of Quantity</th>
                <th class="required-star">Percentage</th>
                <th class="required-star">Price (BD)</th>
                <th>#</th>
            </tr>
            </thead>
            <tbody>

            <?php $total_percentage=0; ?>
            @if(count($raw_material) > 0)
                <?php $inc = 0; ?>
                @foreach($raw_material as $value)
                    <?php $total_percentage += $value->percent; ?>
                    <tr id="rawMaterialTableRow{{$inc}}" data-number="{{$inc}}" class="table-tr">
                        <td>
                            {!! Form::text('product_name['.$inc.']', $value->product_name, ['class' => 'form-control input-md required']) !!}
                            {!! $errors->first('product_name','<span class="help-block">:message</span>') !!}
                        </td>
                        <td>
                            {!! Form::text('hs_code['.$inc.']', $value->hs_code, ['class' => 'form-control input-md required']) !!}
                            {!! $errors->first('hs_code','<span class="help-block">:message</span>') !!}
                        </td>

                        <td>
                            {!! Form::number('quantity['.$inc.']', $value->quantity, ['class' => 'numberNoNegative form-control input-md required']) !!}
                            {!! $errors->first('quantity','<span class="help-block">:message</span>') !!}
                        </td>

                        <td>
                            {!! Form::select('quantity_unit['.$inc.']', $productUnit, $value->quantity_unit, ['class' => 'form-control input-md required']) !!}
                            {!! $errors->first('quantity_unit','<span class="help-block">:message</span>') !!}
                        </td>

                        <td>
                            {!! Form::number('percent['.$inc.']', $value->percent, ['class' => 'numberNoNegative form-control input-md required hundred-percentage','id' => 'percent'.$inc, 'onkeyup'=>'countHundredPercentage(this.id)']) !!}
                            {!! $errors->first('percent','<span class="help-block">:message</span>') !!}
                        </td>

                        <td>
                            {!! Form::number('price_taka['.$inc.']', $value->price_taka, ['class' => 'numberNoNegative form-control input-md priceBD required', 'id' => "total_price_bd$inc", 'onkeyup' => 'calculatePriceBD("priceBD", "addCell")']) !!}
                            {!! $errors->first('price_taka','<span class="help-block">:message</span>') !!}
                        </td>
                        <td style="text-align: left;">
                            @if($inc==0)
                                <a class="btn btn-sm btn-primary addTableRows" title="Add more"
                                   onclick="addTableRowForIRC('rawMaterialTable', 'rawMaterialTableRow{{$inc}}');"><i
                                            class="fa fa-plus"></i></a>
                            @else
                                <a href="javascript:void(0);" class="btn btn-sm btn-danger removeRow"
                                   onclick="removeTableRawMaterial('rawMaterialTable','rawMaterialTableRow{{$inc}}');"> <i
                                            class="fa fa-times" aria-hidden="true"></i></a>
                            @endif
                        </td>
                        <input type="hidden" name="raw_material_id[{{$inc}}]" value="{{$value->id}}">
                    </tr>
                    <?php $inc++; ?>
                @endforeach
            @else
                <tr id="rawMaterialTableRow" data-number="1" class="table-tr">
                    <td>
                        {!! Form::text('product_name[0]', '', ['class' => 'form-control input-md required']) !!}
                        {!! $errors->first('product_name','<span class="help-block">:message</span>') !!}
                    </td>
                    <td>
                        {!! Form::text('hs_code[0]', '', ['class' => 'form-control input-md required']) !!}
                        {!! $errors->first('hs_code','<span class="help-block">:message</span>') !!}
                    </td>
                    <td>
                        {!! Form::number('quantity[0]', '', ['class' => 'numberNoNegative form-control input-md required']) !!}
                        {!! $errors->first('quantity','<span class="help-block">:message</span>') !!}
                    </td>
                    <td>
                        {!! Form::select('quantity_unit[0]', $productUnit, '', ['class' => 'form-control input-md required']) !!}
                        {!! $errors->first('quantity_unit','<span class="help-block">:message</span>') !!}
                    </td>
                    <td>
                        {!! Form::number('percent[0]', '', ['class' => 'numberNoNegative form-control input-md required hundred-percentage', 'id' => 'percent0', 'onkeyup' => 'countHundredPercentage(this.id)']) !!}
                        {!! $errors->first('percent','<span class="help-block">:message</span>') !!}
                    </td>
                    <td>
                        {!! Form::number('price_taka[0]', '', ['class' => 'numberNoNegative form-control input-md priceBD required total-price', 'id' => 'total_price_bd0','onkeyup' => 'calculatePriceBD("priceBD", "addCell")']) !!}
                        {!! $errors->first('price_taka','<span class="help-block">:message</span>') !!}
                    </td>
                    <td style="text-align: left;">
                        <a class="btn btn-sm btn-primary addTableRows" title="Add more"
                           onclick="addTableRowForIRC('rawMaterialTable', 'rawMaterialTableRow');">
                            <i class="fa fa-plus"></i></a>
                    </td>
                </tr>
            @endif
            </tbody>
            <tfoot>
            <tr>
                <td colspan="4"></td>
                <td>
                    <div>
                        {!! Form::text('total_percentage',$total_percentage, ['class' => 'form-control input-md percentage', 'readonly', 'id' => 'total_percentage']) !!}
                    </div>
                </td>
                <td>
                    {!! Form::text('raw_material_total_price', $total_price, ['class' => 'form-control input-md', 'readonly', 'id' => 'total_price_bd']) !!}
                </td>
            </tr>
            </tfoot>
        </table>
    </div>

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

        jQuery.validator.addMethod("percentage", function(value, element) {
            if (value < 100) {
                return false;
            }
            return true;
        });

        $("#rawMaterialForm").validate({
            messages: {
                total_percentage: 'Percentage amount must be 100'
            },
            submitHandler: formSubmit
        });



        let form = $("#rawMaterialForm"); //Get Form ID
        let url = form.attr("action"); //Get Form action
        let type = form.attr("method"); //get form's data send method
        let info_err = $('.errorMsg'); //get error message div
        let info_suc = $('.successMsg'); //get success message div

        //============Ajax Setup===========//
        function formSubmit() {

            let total_percent = $("#total_percentage");
            total_percent.removeClass('error');
            if (total_percent.val() != 100) {
                total_percent.addClass('error');
                alert("Percentage amount must be 100");
                return false;
            }

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
                            $('#irc2ndadhocModal').modal('hide');
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
                    let errors = data.responseJSON;
                    $("#Duplicated jQuery selector").prop('disabled', false);
                    console.log(errors);
                    alert('Sorry, an unknown Error has been occured! Please try again later.');
                }
            });
            return false;
        }
    });

    // Remove Table row script
    function removeTableRawMaterial(tableID, removeNum) {
        let removedCellClass = $("#" + tableID).find('#' + removeNum).find('.priceBD');
        calculatePriceBD(removedCellClass, "removeCell");

        let percentageColumn = $("#" + tableID).find('#' + removeNum).find('.hundred-percentage');
        subtractionPercentageValue(percentageColumn);

        $('#' + tableID).find('#' + removeNum).remove();
    }

    function calculatePriceBD(className, slag) {
        let total_price = 0;
        let target_id = document.getElementById('total_price_bd');

        if (slag == 'addCell') {
            $("." + className).each(function () {
                total_price += parseFloat(Number($(this).val()));
            });
        }

        if (slag == 'removeCell') {
            total_price = target_id.value - className.val();
        }

        target_id.value = total_price.toFixed(3);
    }

    function countHundredPercentage(percentId) {
        const percentageValue = $('#' + percentId).val();
        let total = 0;

        if (percentageValue > 100) {
            alert("Please, select a value that is under and equal to the hundred.");
            $('#' + percentId).val('');
        }

        $('.hundred-percentage').each(function (){
            total += (isNaN(parseFloat(this.value))) ? "" : parseFloat(this.value);
        });

        if(total > 100) {
            alert("Please, select a value that is under and equal to the hundred.");
            $('#' + percentId).val('');
        }

        if (total <= 100) {
            $('#total_percentage').val(total.toFixed(5));
        }
    }

    function subtractionPercentageValue(columnValue) {
        $('#total_percentage').val($('#total_percentage').val() - columnValue.val());
    }

</script>
