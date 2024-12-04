<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
<div class="content">
    <br>
    <div class="row">
        <div class="col-md-12">
            <table width="100%" style="margin-bottom: 10px;">
                <tbody>
                <tr>
                    <td width="30%" style="padding: 0">
                        <strong>Ref No: </strong> {{ !empty($inspectionInfo->tracking_no) ? $inspectionInfo->tracking_no : '' }}
                    </td>
                    <td width="70%" style="padding: 0; text-align: right">
                        <strong>Company Name:</strong> {{ !empty($inspectionInfo->company_name) ? $inspectionInfo->company_name : '' }}
                    </td>
                </tr>
                </tbody>
            </table>

            @if($inspectionInfo->irc_purpose_id != 2 && count($annualProductionCapacity) > 0)
                <?php $count = 1; ?>
                @foreach($annualProductionCapacity as $apc)
                    <span style="font-size: 16px;">
                            <?php echo $count++; ?>. প্রতি
                                <span  style="font-size: 13px">
                                    {{ (!empty($apc->unit_of_product) ? $apc->unit_of_product : '') }}
                                    {{ (!empty($apc->unit_name) ? $apc->unit_name : '') }}
                                    {{ (!empty($apc->product_name) ? $apc->product_name : '') }}
                                </span>
                                উৎপাদনের জন্য কাঁচামাল প্রয়োজন
                                {{ (!empty($apc->raw_material_total_price) ? $apc->raw_material_total_price : '') }} টাকার
                        </span>
                    <?php
                    DB::statement(DB::raw('set @rownum=0'));
                    $raw_material = App\Modules\IrcRecommendationNew\Models\RawMaterial::where('apc_product_id', $apc->id)
                        ->get([DB::raw('@rownum := @rownum+1 AS sl'), 'irc_raw_material.*']);
                    ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dt-responsive">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>HS Code</th>
                                <th>Quantity</th>
                                <th>Unit of Quantity</th>
                                <th>Percentage</th>
                                <th>Price (BD)</th>
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
                                <td colspan="6" class="text-right">
                                    <span class="pull-right">
                                        <strong>Total</strong>
                                    </span>
                                </td>
                                <td>{{ $apc->raw_material_total_price }}</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
</body>
</html>