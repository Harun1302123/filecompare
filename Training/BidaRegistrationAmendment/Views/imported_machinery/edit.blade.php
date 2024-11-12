{!! Form::open(array('url' => '/bida-registration-amendment/imported-machinery-update','method' => 'post', 'class' => 'form-horizontal smart-form','id'=>'machineryForm',
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

    <div class="table-responsive">
        <table id="directorTable" class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
            <thead>
            <tr>
                <td class="bg-yellow" colspan="4">Existing information (Latest BIDA Reg. Info.)</td>
                <td class="bg-green" colspan="5">Proposed information</td>
            </tr>
            <tr>
                <th class="light-yellow">Name of machineries</th>
                <th class="light-yellow">Quantity</th>
                <th class="light-yellow">Unit prices TK</th>
                <th class="light-yellow">Total value (Million) TK</th>

                <th class="light-green">Name of machineries</th>
                <th class="light-green">Quantity</th>
                <th class="light-green">Unit prices TK</th>
                <th class="light-green">Total value (Million) TK</th>
                <th class="light-green">Action</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <input type="hidden" name="im_id" value="{{ $getImportedMachinery->id }}">
                <td class="light-yellow">
                    {!! Form::text('l_machinery_imported_name', $getImportedMachinery->l_machinery_imported_name, ['class' => 'form-control input-md machinery_imported_name', 'id' => 'l_machinery_imported_name']) !!}
                    {!! $errors->first('l_machinery_imported_name','<span class="help-block">:message</span>') !!}
                </td>
                <td class="light-yellow">
                    {!! Form::text('l_machinery_imported_qty', $getImportedMachinery->l_machinery_imported_qty, ['class' => 'form-control input-md', 'id' => 'l_machinery_imported_qty']) !!}
                    {!! $errors->first('l_machinery_imported_qty','<span class="help-block">:message</span>') !!}
                </td>
                <td class="light-yellow">
                    {!! Form::text('l_machinery_imported_unit_price', $getImportedMachinery->l_machinery_imported_unit_price, ['class' => 'form-control input-md', 'id' => 'l_machinery_imported_unit_price']) !!}
                    {!! $errors->first('l_machinery_imported_unit_price','<span class="help-block">:message</span>') !!}
                </td>
                <td class="light-yellow">
                    {!! Form::text('l_machinery_imported_total_value', $getImportedMachinery->l_machinery_imported_total_value, ['class' => 'form-control input-md', 'id' => 'l_machinery_imported_total_value']) !!}
                    {!! $errors->first('l_machinery_imported_total_value','<span class="help-block">:message</span>') !!}
                </td>

                <td class="light-green">
                    {!! Form::text('n_l_machinery_imported_name', $getImportedMachinery->n_l_machinery_imported_name, ['class' => 'form-control input-md machinery_imported_name', 'id' => 'n_l_machinery_imported_name']) !!}
                    {!! $errors->first('n_l_machinery_imported_name','<span class="help-block">:message</span>') !!}
                </td>
                <td class="light-green">
                    {!! Form::text('n_l_machinery_imported_qty', $getImportedMachinery->n_l_machinery_imported_qty, ['class' => 'form-control input-md', 'id' => 'n_l_machinery_imported_qty']) !!}
                    {!! $errors->first('n_l_machinery_imported_qty','<span class="help-block">:message</span>') !!}
                </td>
                <td class="light-green">
                    {!! Form::text('n_l_machinery_imported_unit_price', $getImportedMachinery->n_l_machinery_imported_unit_price, ['class' => 'form-control input-md', 'id' => 'n_l_machinery_imported_unit_price']) !!}
                    {!! $errors->first('n_l_machinery_imported_unit_price','<span class="help-block">:message</span>') !!}
                </td>
                <td class="light-green">
                    {!! Form::text('n_l_machinery_imported_total_value', $getImportedMachinery->n_l_machinery_imported_total_value, ['class' => 'form-control input-md', 'id' => 'n_l_machinery_imported_total_value']) !!}
                    {!! $errors->first('n_l_machinery_imported_total_value','<span class="help-block">:message</span>') !!}
                </td>
                <td class="light-green">
                    {!! Form::select("amendment_type", $amendment_type, 'edit', ['class'=>'form-control input-md apc-action', 'id' => 'amendment_type0', 'onchange' => 'actionWiseFieldDisable(this, ["l_machinery_imported_name", "l_machinery_imported_qty", "l_machinery_imported_unit_price", "l_machinery_imported_total_value"], ["n_l_machinery_imported_name", "n_l_machinery_imported_qty", "n_l_machinery_imported_unit_price", "n_l_machinery_imported_total_value"])']) !!}
                    {!! $errors->first('action','<span class="help-block">:message</span>') !!}
                </td>
            </tr>
            </tbody>
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
                            $("#braModal").modal('hide');
                        });
                        form.trigger("reset");

                        loadImportedMachineryData(20, 'off');

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
