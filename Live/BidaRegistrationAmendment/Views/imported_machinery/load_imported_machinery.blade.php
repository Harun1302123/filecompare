<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
        <tr>
            <td></td>
            <td class="bg-yellow" colspan="5">Existing  information (Latest BIDA Reg. Info.)</td>
            <td class="bg-green" colspan="4">Proposed information</td>
        </tr>
        <tr>
            <th><input type="checkbox" id="select-all-imported"></th>
            <th class="light-yellow">SL.</th>
            <th class="light-yellow">Name of machineries</th>
            <th class="light-yellow">Quantity</th>
            <th class="light-yellow">Unit prices TK</th>
            <th class="light-yellow">Total value (Million) TK</th>

            <th class="light-green">Name of machineries</th>
            <th class="light-green">Quantity</th>
            <th class="light-green">Unit prices TK</th>
            <th class="light-green">Total value (Million) TK</th>
            <th>Action Type</th>
            @if ($viewMode == 'off')
            <th style="width: 55px;">Action</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @foreach($importedMachineryData['getData'] as $machineryImported)
            <tr>
                <td><input type="checkbox" value={{ Encryption::encodeId($machineryImported->id) }} class="row-checkbox"></td>
                <td class="light-yellow">{{ $machineryImported->sl }}</td>
                <td class="light-yellow">{{ $machineryImported->l_machinery_imported_name }}</td>
                <td class="light-yellow">{{ $machineryImported->l_machinery_imported_qty }}</td>
                <td class="light-yellow">{{ $machineryImported->l_machinery_imported_unit_price }}</td>
                <td class="light-yellow">{{ $machineryImported->l_machinery_imported_total_value }}</td>

                <td class="light-green">{{ $machineryImported->n_l_machinery_imported_name }}</td>
                <td class="light-green">{{ $machineryImported->n_l_machinery_imported_qty }}</td>
                <td class="light-green">{{ $machineryImported->n_l_machinery_imported_unit_price }}</td>
                <td class="light-green">{{ $machineryImported->n_l_machinery_imported_total_value }}</td>
                <td>
                    @if(!in_array($machineryImported->amendment_type, ['no change']) )
                        <span class="badge">
                        {{ $machineryImported->amendment_type }}
                    </span>
                    @endif
                </td>
                @if ($viewMode == 'off')
                <td>
                    <div style="width: 55px; display: inline-block; text-align: center;">
                        <a class="btn btn-xs btn-success"
                           data-toggle="modal"
                           data-target="#braModal"
                           onclick="openModal(this)"
                           data-action="{{ url('bida-registration-amendment/imported-machinery-edit-form/'.Encryption::encodeId($machineryImported->id)) }}">
                            <i class="far fa-edit"></i>
                        </a>
                        <a class="btn btn-xs btn-danger"
                           onclick="confirmDelete('{{ url('bida-registration-amendment/imported-machinery-delete/'.Encryption::encodeId($machineryImported->id)) }}', 'imported_machinery')">
                            <i class="far fa-trash-alt"></i>
                        </a>
                    </div>
                </td>
                @endif
            </tr>
        @endforeach
        </tbody>
        <tfoot align="right">
        <tr>
            <th class="light-yellow" colspan="5" style="text-align: right">Total:</th>
            <th class="light-yellow">{{ number_format($importedMachineryData['ex_imported_machinery_total'], 5) }}</th>

            <th class="light-green" colspan="3" style="text-align: right">Total:</th>
            <th class="light-green">{{ number_format($importedMachineryData['pro_imported_machinery_total'], 5) }}</th>
        </tr>
        <tr>
            <td colspan="9"><strong>Grand Total:</strong></td>
            <td style="text-align: left;">
                <strong id="grand_imported_machinery">{{ number_format($importedMachineryData['grand_total'],5,'.','') }}</strong>
            </td>
        </tr>
        </tfoot>
    </table>
</div>

<script>
        // Select all checkboxes imported machinery
    $('#select-all-imported').click(function(event) {
        if(this.checked) {
            // Iterate each checkbox
            $('.row-checkbox').each(function() {
                this.checked = true;
            });
        } else {
            $('.row-checkbox').each(function() {
                this.checked = false;
            });
        }
    });

    $('#batch-delete').click(function() {
        var selectedIds = [];
        
        $('.row-checkbox').each(function() {
            if ($(this).is(':checked')) {
                selectedIds.push($(this).val());
            }
        });
        var confirmDelete = window.confirm('Are you sure you want to delete the selected items?');
        if (confirmDelete) {
            $.ajax({
                url: '/bida-registration-amendment/batch-delete-imported-machinery',
                type: 'POST',
                data: {
                    _token: $('input[name="_token"]').val(),
                    ids: selectedIds
                },
                success: function(response) {
                    if(response.responseCode==1){
                        swal({
                            type: 'success',
                            title: 'Deleted!',
                            text: 'Data has been deleted successfully'
                        });
                        refreshPage();
                    }
                    else{
                        swal({
                            type: 'error',
                            title: 'Oops...',
                            text: response.msg
                        });
                        return false;
                    }
                    
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
    });

    function refreshPage() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', window.location.href, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                window.location.reload(true);
            }
        };
        xhr.send();
    }

</script>