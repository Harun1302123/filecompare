<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset("assets/stylesheets/styles.css") }}" />
    <link rel="stylesheet" href="https://stackpatd.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
</head>
<body>

{{--As Per L/C Open--}}
<fieldset class="scheduler-border">
    <legend class="scheduler-border">As Per L/C Open</legend>
    <div class="form-group">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered table-responsive" cellspacing="0" width="100%" id="financeTableId">
                    <thead>
                    <tr>
                        <th>Description of Machine</th>
                        <th>Unit of Quantity</th>
                        <th>Quantity (A)</th>
                        <th colspan="2">Unit Price (B)</th>
                        <th>Price Foreign Currency (A X B)</th>
                        <th>Price BDT (C)</th>
                        <th>Value Taka (in million)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($existing_machines_lc as $existing_machines_lc)
                        <tr>
                            <td>{{ ($existing_machines_lc->product_name ? $existing_machines_lc->product_name : '') }}</td>
                            <td>{{ ($existing_machines_lc->name ? $existing_machines_lc->name : '') }}</td>
                            <td>{{ ($existing_machines_lc->quantity ? $existing_machines_lc->quantity : '') }}</td>
                            <td>{{ ($existing_machines_lc->unit_price ? $existing_machines_lc->unit_price : '') }}</td>
                            <td>{{ ($existing_machines_lc->code ? $existing_machines_lc->code : '') }}</td>
                            <td>{{ ($existing_machines_lc->price_foreign_currency ? $existing_machines_lc->price_foreign_currency : '') }}</td>
                            <td>{{ ($existing_machines_lc->price_bdt ? $existing_machines_lc->price_bdt : '') }}</td>
                            <td>{{ ($existing_machines_lc->price_taka_mil ? $existing_machines_lc->price_taka_mil : '') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="6"><span class="pull-right">Total</span></td>
                        <td>{{ $total_existing_machines_lc_bdt }}</td>
                        <td>{{ $appInfo->em_lc_total_taka_mil }}</td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</fieldset>

{{--As per Local Procurement/ Collection--}}
<fieldset class="scheduler-border">
    <legend class="scheduler-border">As per Local Procurement/ Collection</legend>
    <div class="form-group">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered" cellspacing="0"
                       width="100%" id="financeTableId">
                    <thead>
                    <tr>
                        <th>Description of Machine</th>
                        <th>Unit of Quantity</th>
                        <th>Quantity (A)</th>
                        <th>Unit Price (B)</th>
                        <th>Price BDT (A X B) </th>
                        <th>Value Taka (in million)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($existing_machines_local as $existing_machines_local)
                        <tr>
                            <td>{{ ($existing_machines_local->product_name ? $existing_machines_local->product_name : '') }}</td>
                            <td>{{ ($existing_machines_local->name ? $existing_machines_local->name : '') }}</td>
                            <td>{{ ($existing_machines_local->quantity ? $existing_machines_local->quantity : '') }}</td>
                            <td>{{ ($existing_machines_local->unit_price ? $existing_machines_local->unit_price : '') }}</td>
                            <td>{{ ($existing_machines_local->price_bdt ? $existing_machines_local->price_bdt : '') }}</td>
                            <td>{{ ($existing_machines_local->price_taka_mil ? $existing_machines_local->price_taka_mil : '') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="5"><span class="pull-right">Total</span></td>
                        <td>{{ $appInfo->em_local_total_taka_mil }}</td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</fieldset>

</body>
</html>