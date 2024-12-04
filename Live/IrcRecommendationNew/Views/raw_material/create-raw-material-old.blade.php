<style>
    label.error {
        display: inline-block !important;
        border: none !important;
    }
</style>

{!! Form::open(array('url' => '/bida-registration/store-raw-material','method' => 'post', 'class' => 'form-horizontal smart-form','id'=>'rawMaterialForm',
        'enctype' =>'multipart/form-data', 'files' => 'true', 'role' => 'form')) !!}

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span>
    </button>
    <h4 class="modal-title" id="myModalLabel"> Add Raw Material for <strong>{{ $annual_production_capacity->product_name }}</strong></h4>
</div>

<div class="modal-body">
    <div class="errorMsg alert alert-danger alert-dismissible hidden">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    </div>
    <div class="successMsg alert alert-success alert-dismissible hidden">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    </div>

    <input type="hidden" name="app_id" value="{{ $app_id }}">
    <input type="hidden" name="apc_product_id" value="{{ $apc_product_id }}">
    <input type="hidden" name="process_type_id" value="{{ $process_type_id }}">

    <div class="row form-group" id="unit_of_product">
        <div class="col-md-6 {{$errors->has('unit_of_product') ? 'has-error': ''}}">
            {!! Form::label('unit_of_product','Unit of Product:', ['class'=>'col-md-4 text-left']) !!}
            <div class="col-md-8">
                {!! Form::select('unit_of_product', [1=>'1', 10=>'10', 100=>'100', 1000=>'1000'], $annual_production_capacity->unit_of_product, ["placeholder" => "Select One", 'class' => 'form-control input-md required']) !!}
                {!! $errors->first('unit_of_product','<span class="help-block">:message</span>') !!}
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table id="rawMaterialTable" class="table table-striped table-bordered dt-responsive" cellspacing="0"
               width="100%">
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
            <?php $total_percentage = 0; ?>
            @if(count($raw_material) > 0)
                <?php
                $inc = 0;
                ?>
                @foreach($raw_material as $value)
                    <?php $total_percentage += $value->percent; ?>
                    <tr id="rawMaterialTableRow{{$inc}}">
                        <td>
                            {!! Form::text('product_name['.$inc.']', $value->product_name, ['class' => 'form-control input-md required']) !!}
                            {!! $errors->first('product_name','<span class="help-block">:message</span>') !!}
                        </td>
                        <td>
                            {!! Form::text('hs_code['.$inc.']', $value->hs_code, ['class' => 'form-control input-md required']) !!}
                            {!! $errors->first('hs_code','<span class="help-block">:message</span>') !!}
                        </td>

                        <td>
                            {!! Form::number('quantity['.$inc.']', $value->quantity, ['class' => 'form-control input-md required']) !!}
                            {!! $errors->first('quantity','<span class="help-block">:message</span>') !!}
                        </td>

                        <td>
                            {!! Form::select('quantity_unit['.$inc.']', $productUnit, $value->quantity_unit, ['class' => 'form-control input-md required']) !!}
                            {!! $errors->first('quantity_unit','<span class="help-block">:message</span>') !!}
                        </td>

                        <td>
                            {!! Form::number('percent['.$inc.']', $value->percent, ['class' => 'form-control input-md required hundred-percentage','id' => 'percent'.$inc, 'onkeyup'=>'countHundredPercentage(this.id)']) !!}
                            {!! $errors->first('percent','<span class="help-block">:message</span>') !!}
                        </td>
                        <td>
                            {!! Form::number('price_taka['.$inc.']', $value->price_taka, ['class' => 'form-control input-md priceBD required', 'id' => "total_price_bd$inc", 'onkeyup' => 'calculatePriceBD("priceBD", "addCell")']) !!}
                            {!! $errors->first('price_taka','<span class="help-block">:message</span>') !!}
                        </td>
                        <td style="text-align: left;">
                            @if($inc==0)
                                <a class="btn btn-sm btn-primary addTableRows" title="Add more"
                                   onclick="addTableRowRM('rawMaterialTable', 'rawMaterialTableRow{{$inc}}');"><i
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
                <tr id="rawMaterialTableRow">
                    <td>
                        {!! Form::text('product_name[0]', '', ['class' => 'form-control input-md required']) !!}
                        {!! $errors->first('product_name','<span class="help-block">:message</span>') !!}
                    </td>
                    <td>
                        {!! Form::text('hs_code[0]', '', ['class' => 'form-control input-md required']) !!}
                        {!! $errors->first('hs_code','<span class="help-block">:message</span>') !!}
                    </td>
                    <td>
                        {!! Form::number('quantity[0]', '', ['class' => 'form-control input-md required']) !!}
                        {!! $errors->first('quantity','<span class="help-block">:message</span>') !!}
                    </td>
                    <td>
                        {!! Form::select('quantity_unit[0]', $productUnit, '', ['class' => 'form-control input-md required']) !!}
                        {!! $errors->first('quantity_unit','<span class="help-block">:message</span>') !!}
                    </td>
                    <td>
                        {!! Form::number('percent[0]', '', ['class' => 'form-control input-md required hundred-percentage', 'id' => 'percent0', 'onkeyup' => 'countHundredPercentage(this.id)']) !!}
                        {!! $errors->first('percent','<span class="help-block">:message</span>') !!}
                    </td>
                    <td>
                        {!! Form::number('price_taka[0]', '', ['class' => 'form-control input-md priceBD required total-price', 'id' => 'total_price_bd0','onkeyup' => 'calculatePriceBD("priceBD", "addCell")']) !!}
                        {!! $errors->first('price_taka','<span class="help-block">:message</span>') !!}
                    </td>
                    <td style="text-align: left;">
                        <a class="btn btn-sm btn-primary addTableRows" title="Add more"
                           onclick="addTableRowRM('rawMaterialTable', 'rawMaterialTableRow');">
                            <i class="fa fa-plus"></i></a>
                    </td>
                </tr>
            @endif
            </tbody>
            <tfoot>
            <tr>
                <td colspan="4"></td>
                <td>
                    {!! Form::text('total_percentage', $total_percentage, ['class' => 'form-control input-md percentage', 'readonly', 'id' => 'total_percentage']) !!}
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

    // Add table Row script
    function addTableRowRM(tableID, templateRow) {
        //rowCount++;
        //Direct Copy a row to many times
        var x = document.getElementById(templateRow).cloneNode(true);
        x.id = "";
        x.style.display = "";
        var table = document.getElementById(tableID);
        var rowCount = $('#' + tableID).find('tr').length - 1;
        var lastTr = $('#' + tableID).find('tr').last().attr('data-number');
        var production_desc_val = $('#' + tableID).find('tr').last().find('.production_desc_1st').val();
        if (lastTr != '' && typeof lastTr !== "undefined") {
            rowCount = parseInt(lastTr) + 1;
        }

        let totalpercentage = $("#total_percentage").val();
        if (totalpercentage == 100) {
            swal({
                type: 'error',
                title: 'Oops...',
                text: 'You percentage amount is already 100. You cannot add more row'
            });
            return false;
        }
        //var rowCount = table.rows.length;
        //Increment id
        var rowCo = rowCount;
        var idText = 'rowCount' + tableID + rowCount;
        x.id = idText;
        $("#" + tableID).append(x);
        //get select box elements
        var attrSel = $("#" + tableID).find('#' + idText).find('select');
        //edited by ishrat to solve select box id auto increment related bug
        for (var i = 0; i < attrSel.length; i++) {
            var nameAtt = attrSel[i].name;
            var repText = nameAtt.replace('[0]', '[' + rowCo + ']'); //increment all array element name
            attrSel[i].name = repText;
        }
        attrSel.val(''); //value reset
        // end of  solving issue related select box id auto increment related bug by ishrat

        //get input elements
        var attrInput = $("#" + tableID).find('#' + idText).find('input');
        for (var i = 0; i < attrInput.length; i++) {
            var nameAtt = attrInput[i].name;
            var idAtt = attrInput[i].id;
            //increment all array element name
            var repText = nameAtt.replace('[0]', '[' + rowCo + ']');
            attrInput[i].name = repText;

            //increment all array element id
            var repTextId = idAtt.replace('0', rowCo -1);
            attrInput[i].id = repTextId;
        }
        attrInput.val(''); //value reset
        //edited by ishrat to solve textarea id auto increment related bug
        //get textarea elements
        var attrTextarea = $("#" + tableID).find('#' + idText).find('textarea');
        for (var i = 0; i < attrTextarea.length; i++) {
            var nameAtt = attrTextarea[i].name;
            //increment all array element name
            var repText = nameAtt.replace('[0]', '[' + rowCo + ']');
            attrTextarea[i].name = repText;
            $('#' + idText).find('.readonlyClass').prop('readonly', true);
        }
        attrTextarea.val(''); //value reset
        // end of  solving issue related textarea id auto increment related bug by ishrat
        attrSel.prop('selectedIndex', 0);
        if ((tableID === 'machinaryTbl' && templateRow === 'rowMachineCount0') || (tableID === 'machinaryTbl' && templateRow === 'rowMachineCount')) {
            $("#" + tableID).find('#' + idText).find('select.m_currency').val("107");  //selected index reset
        } else {
            attrSel.prop('selectedIndex', 0);  //selected index reset
        }
        $("#" + tableID).find('#' + idText).find('.hundred-percentage').attr('onkeyup', 'countHundredPercentage(this.id)');


        var TotalRows = parseInt(rowCount) + 2;
        var ChakingArray = [10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150, 160, 170, 180, 190, 200];

        if (jQuery.inArray(TotalRows, ChakingArray) !== -1) {
            $("#" + tableID).find('#' + idText).find('.addTableRows').removeClass('btn-danger').addClass('btn-primary');
        } else {
            $("#" + tableID).find('#' + idText).find('.addTableRows').removeClass('btn-primary').addClass('btn-danger')
                .attr('onclick', 'removeTableRawMaterial("' + tableID + '","' + idText + '")');
            $("#" + tableID).find('#' + idText).find('.addTableRows > .fa').removeClass('fa-plus').addClass('fa-times');
        }

        $('#' + tableID).find('tr').last().attr('data-number', rowCount);

        $("#" + tableID).find('#' + idText).find('.onlyNumber').on('keydown', function (e) {
            //period decimal
            if ((e.which >= 48 && e.which <= 57)
                //numpad decimal
                || (e.which >= 96 && e.which <= 105)
                // Allow: backspace, delete, tab, escape, enter and .
                || $.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1
                // Allow: Ctrl+A
                || (e.keyCode == 65 && e.ctrlKey === true)
                // Allow: Ctrl+C
                || (e.keyCode == 67 && e.ctrlKey === true)
                // Allow: Ctrl+V
                || (e.keyCode == 86 && e.ctrlKey === true)
                // Allow: Ctrl+X
                || (e.keyCode == 88 && e.ctrlKey === true)
                // Allow: home, end, left, right
                || (e.keyCode >= 35 && e.keyCode <= 39)) {
                var $this = $(this);
                setTimeout(function () {
                    $this.val($this.val().replace(/[^0-9.]/g, ''));
                }, 4);
                var thisVal = $(this).val();
                if (thisVal.indexOf(".") != -1 && e.key == '.') {
                    return false;
                }
                $(this).removeClass('error');
                return true;
            } else {
                $(this).addClass('error');
                return false;
            }
        }).on('paste', function (e) {
            var $this = $(this);
            setTimeout(function () {
                $this.val($this.val().replace(/[^.0-9]/g, ''));
            }, 4);
        });
    } // end of addTableRow() function

    // Remove Table row script
    function removeTableRawMaterial(tableID, removeNum) {
        var removedCellClass = $("#" + tableID).find('#' + removeNum).find('.priceBD');
        calculatePriceBD(removedCellClass, "removeCell");

        var percentageColumn = $("#" + tableID).find('#' + removeNum).find('.hundred-percentage');
        subtractionPercentageValue(percentageColumn);

        $('#' + tableID).find('#' + removeNum).remove();
    }

    function calculatePriceBD(className, slag) {
        var total_price = 0;
        var target_id = document.getElementById('total_price_bd');

        if (slag == 'addCell') {
            $("." + className).each(function () {
                total_price += parseFloat(Number($(this).val()));
            });
        }

        if (slag == 'removeCell') {
            total_price = target_id.value - className.val();
        }

        target_id.value = total_price;
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
            $('#total_percentage').val(total);
        }
    }

    function subtractionPercentageValue(columnValue) {
        $('#total_percentage').val($('#total_percentage').val() - columnValue.val());
    }

</script>
