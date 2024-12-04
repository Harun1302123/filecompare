<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel"> Raw Material</h4>
    </div>
    <div class="modal-body">
        <h5><b>Unit of Product: {{ $total_value->unit_of_product }}</b></h5>
        <div class="table-responsive">
            <table id="existingMachinesTbl" class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>HS Code</th>
                    <th>Quantity</th>
                    <th>Unit of Quantity</th>
                    <th>Percentage</th>
                    <th>Price (BDT)</th>
                </tr>
                </thead>
                <tbody>
                @foreach($raw_material as $data)
                    <tr>
                        <td>{{ $data->sl }}</td>
                        <td>{{ $data->product_name }}</td>
                        <td>{{ $data->hs_code }}</td>
                        <td>{{ $data->quantity }}</td>
                        <td>{{ $productUnit[$data->quantity_unit] }}</td>
                        <td>{{ $data->percent }}</td>
                        <td>{{ $data->price_taka }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="6"><span class="pull-right">Total</span></td>
                    <td>{{ $total_value->raw_material_total_price }}</td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-md btn-danger pull-right" data-dismiss="modal">Close</button>
    </div>
</div>
