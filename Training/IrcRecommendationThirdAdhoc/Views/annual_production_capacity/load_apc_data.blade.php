<table id="" class="table-responsive table table-bordered">
    <thead>
     <tr>
        <th>#</th>
        <th>Name of Product</th>
        <th>Unit of Quantity</th>
        <th>Quantity</th>
        <th>Price (USD)</th>
        <th>Sales Value in BDT (million)</th>
        <th>Action</th>
        <th>Raw Materials Details</th>
    </tr>
    </thead>

    <tbody>
    @if(count($getData) > 0)
        @foreach($getData as $annualProduct)
            <tr>
                <td>{{ $annualProduct->sl }}</td>
                <td>
                    {{ !empty($annualProduct->product_name) ? $annualProduct->product_name : '' }}
                </td>
                <td>
                    {{ !empty($annualProduct->unit_name) ? $annualProduct->unit_name : '' }}
                </td>
                <td>
                    {{ !empty($annualProduct->quantity) ? $annualProduct->quantity : '' }}
                </td>
                <td>
                    {{ !empty($annualProduct->price_usd) ? $annualProduct->price_usd : '' }}
                </td>
                <td>
                    {{ !empty($annualProduct->price_taka) ? $annualProduct->price_taka : '' }}
                </td>

                <td>
                    <div style="width: 55px; display: inline-block; text-align: center;">
                        <a class="btn btn-xs btn-success" data-toggle="modal"
                           data-target="#irc3rdadhocModal"
                           onclick="openModal(this, 'irc3rdadhocModal')"
                           data-action="{{ url('irc-recommendation-third-adhoc/apc-form-edit/'.Encryption::encodeId($annualProduct->id)) }}">
                            <i class="far fa-edit"></i>
                        </a>
                        <a class="btn btn-xs btn-danger"
                           onclick="confirmDelete('{{ url('irc-recommendation-third-adhoc/apc-delete/'.Encryption::encodeId($annualProduct->id)) }}', 'apc')">
                            <i class="far fa-trash-alt"></i>
                        </a>
                    </div>
                </td>
                <td>
                    <a data-toggle="modal" data-target="#irc3rdadhocModal" onclick="openModal(this, 'irc3rdadhocModal')" data-action="{{ url('irc-recommendation-third-adhoc/add-raw-material/'.Encryption::encodeId($annualProduct->app_id).'/'.Encryption::encodeId($annualProduct->id)) }}" class="btn btn-xs btn-primary"><i class="fa fa-plus"></i> Manual</a>
                    <a data-toggle="modal" data-target="#irc3rdadhocModal" onclick="openModal(this, 'irc3rdadhocModal')" data-action="{{ url('irc-recommendation-third-adhoc/import/'.Encryption::encodeId($annualProduct->app_id).'/'.Encryption::encodeId($annualProduct->id)) }}" class="btn btn-xs btn-success"><i class="fa fa-plus"></i> Excel</a>
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="8" class="text-center"><span class="text-danger">No data available!</span></td>
        </tr>
    @endif
    </tbody>
</table>


