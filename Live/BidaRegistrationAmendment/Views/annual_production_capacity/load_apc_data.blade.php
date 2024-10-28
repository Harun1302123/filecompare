<table id="apcTable" class="table table-bordered">
    <thead>
    <tr>
        <td class="bg-yellow" colspan="6">Existing information (Latest BIDA Reg. Info.)</td>
        <td class="bg-green" colspan="5">Proposed information</td>
    </tr>
    <tr>
        <th class="light-yellow">SL</th>
        <th class="light-yellow">Name of Product</th>
        <th class="light-yellow">Unit of Qty</th>
        <th class="light-yellow">Qty</th>
        <th class="light-yellow">Price (USD)</th>
        <th class="light-yellow">Sales Value in BDT (million)</th>

        <th class="light-green">Name of Product</th>
        <th class="light-green">Unit of Qty</th>
        <th class="light-green">Qty</th>
        <th class="light-green">Price (USD)</th>
        <th class="light-green">Sales Value in BDT (million)</th>
        <th>Action Type</th>

        @if($viewMode == 'off')
            <th style="width: 55px;">Action</th>
        @endif
    </tr>
    </thead>

    <tbody>
    @if(count($getData) > 0)
        @foreach($getData as $annualProduct)
            <tr>
                <td class="light-yellow">{{ $annualProduct->sl }}</td>
                <td class="light-yellow">
                    {{ !empty($annualProduct->product_name) ? $annualProduct->product_name : '' }}
                </td>
                <td class="light-yellow">
                    {{ !empty($annualProduct->ex_unit_name) ? $annualProduct->ex_unit_name : '' }}
                </td>
                <td class="light-yellow">
                    {{ !empty($annualProduct->quantity) ? $annualProduct->quantity : '' }}
                </td>
                <td class="light-yellow">
                    {{ !empty($annualProduct->price_usd) ? $annualProduct->price_usd : '' }}
                </td>
                <td class="light-yellow">
                    {{ !empty($annualProduct->price_taka) ? $annualProduct->price_taka : '' }}
                </td>

                <td class="light-green">
                    {{ !empty($annualProduct->n_product_name) ? $annualProduct->n_product_name : '' }}
                </td>
                <td class="light-green">
                    {{ !empty($annualProduct->pro_unit_name) ? $annualProduct->pro_unit_name : '' }}
                </td>
                <td class="light-green">
                    {{ !empty($annualProduct->n_quantity) ? $annualProduct->n_quantity : '' }}
                </td>
                <td class="light-green">
                    {{ !empty($annualProduct->n_price_usd) ? $annualProduct->n_price_usd : '' }}
                </td>
                <td class="light-green">
                    {{ !empty($annualProduct->n_price_taka) ? $annualProduct->n_price_taka : '' }}
                </td>
                <td>
                    @if(!in_array($annualProduct->amendment_type, ['no change']) )
                    <span class="badge">
                        {{ $annualProduct->amendment_type }}
                    </span>
                    @endif
                </td>
                @if($viewMode == 'off')
                <td>
                    <div style="width: 55px; display: inline-block; text-align: center;">
                        <a class="btn btn-xs btn-success"
                           data-toggle="modal"
                           data-target="#braModal"
                           onclick="openBraModal(this, 'braModal')"
                           data-action="{{ url('bida-registration-amendment/apc-form-edit/'.Encryption::encodeId($annualProduct->id)) }}">
                            <i class="far fa-edit"></i>
                        </a>
                        <a class="btn btn-xs btn-danger"
                           onclick="confirmDelete('{{ url('bida-registration-amendment/apc-delete/'.Encryption::encodeId($annualProduct->id)) }}', 'apc')">
                            <i class="far fa-trash-alt"></i>
                        </a>
                    </div>
                </td>
                @endif
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="13" class="text-center"><span class="text-danger">No data available!</span></td>
        </tr>
    @endif
    </tbody>
</table>