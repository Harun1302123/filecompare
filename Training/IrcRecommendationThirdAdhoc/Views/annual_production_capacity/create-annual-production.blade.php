{!! Form::open(array('url' => '/irc-recommendation-third-adhoc/store-annual-production','method' => 'post', 'class' => 'form-horizontal smart-form','id'=>'annualProductionForm',
        'enctype' =>'multipart/form-data', 'files' => 'true', 'role' => 'form')) !!}

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span>
    </button>
    <h4 class="modal-title" id="myModalLabel"> Add New Annual Production</h4>
</div>

<div class="modal-body">
    <div class="errorMsg alert alert-danger alert-dismissible hidden">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    </div>
    <div class="successMsg alert alert-success alert-dismissible hidden">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    </div>

    <input type="hidden" name="app_id" value="{{ $app_id }}">

    <div class="table-responsive">
        <table id="annualProductionTable" class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th class="required-star">Name of Product</th>
                <th class="required-star">Unit of Quantity</th>
                <th class="required-star">Quantity</th>
                <th class="required-star">Price (USD)</th>
                <th class="required-star">Sales Value in BDT (million)</th>
                <th>#</th>
            </tr>
            </thead>
            <tbody>
            <tr id="annualProductionTableRow" data-number="1" class="table-tr">
                <td>
                    {!! Form::text("em_product_name[]", '', ['class' => 'form-control input-md product required']) !!}
                </td>

                <td>
                    {!! Form::select('em_quantity_unit[]', $productUnit, '', ['class'=>'form-control input-md required']) !!}
                </td>

                <td>
                    {!! Form::number("em_quantity[]", '', ['class' => 'numberNoNegative form-control input-md product required']) !!}
                </td>

                <td>
                    {!! Form::number("em_price_usd[]", '', ['class' => 'numberNoNegative form-control input-md product required']) !!}
                </td>

                <td>
                    {!! Form::number("em_value_taka[]", '', ['class' => 'numberNoNegative form-control input-md number required', 'id'=>'product_capacity_price_usd']) !!}
                </td>
                <td style="text-align: left;">
                    <a class="btn btn-sm btn-primary addTableRows" onclick="addTableRowForIRC('annualProductionTable', 'annualProductionTableRow');">
                        <i class="fa fa-plus"></i>
                    </a>
                </td>
            </tr>
            </tbody>
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
        $("#annualProductionForm").validate({
            errorPlacement: function () {
                return true;
            },
            submitHandler: formSubmit
        });

        let form = $("#annualProductionForm"); //Get Form ID
        let url = form.attr("action"); //Get Form action
        let type = form.attr("method"); //get form's data send method
        let info_err = $('.errorMsg'); //get error message div
        let info_suc = $('.successMsg'); //get success message div

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
                            //window.location.href = data.link;
                            $("#irc3rdadhocModal").modal('hide');
                        });

                        loadAnnualProductionCapacityData();
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
</script>
