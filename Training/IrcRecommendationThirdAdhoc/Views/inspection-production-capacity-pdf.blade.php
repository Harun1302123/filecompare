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
                        <strong>Ref No: </strong> {{ !empty($lastInspectionInfo->tracking_no) ? $lastInspectionInfo->tracking_no : '' }}
                    </td>
                    <td width="70%" style="padding: 0; text-align: right">
                        <strong>Company Name:</strong> {{ !empty($lastInspectionInfo->company_name) ? $lastInspectionInfo->company_name : '' }}
                    </td>
                </tr>
                </tbody>
            </table>

            @if($lastInspectionInfo->irc_purpose != 2 && count($annualProductionCapacity) > 0)
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
                    $raw_material = App\Modules\IrcRecommendationThirdAdhoc\Models\ThirdRawMaterial::where('apc_product_id', $apc->id)
                        ->get([DB::raw('@rownum := @rownum+1 AS sl'), 'irc_3rd_raw_material.*']);
                    ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
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

            @if($lastInspectionInfo->irc_purpose != 1 && count($inspectionAnnualProductionSpareParts) > 0)
                <table class="table table-bordered dt-responsive" cellspacing="0" width="100%">
                    <thead class="alert alert-info">
                    <tr>
                        <td style="font-size: 16px;">ক্রমিক নং</td>
                        <td style="font-size: 16px;">পন্য/ সেবার নাম</td>
                        <td style="font-size: 16px;">নির্ধারিত বার্ষিক উৎপাদন ক্ষমতা</td>
                        <td style="font-size: 16px;">ষান্মাসিক উৎপাদন ক্ষমতা</td>
                        <td style="font-size: 16px;">ষান্মাসিক আমদানিস্বত্ব (টাকা)</td>
                    </tr>
                    </thead>

                    <tbody>
                    <?php $count = 1;?>
                    @foreach($inspectionAnnualProductionSpareParts as $apsp)
                        <tr>
                            <td><?php echo $count++ ?></td>
                            <td>
                                {{ (!empty($apsp->product_name) ? $apsp->product_name : '') }}
                            </td>
                            <td>
                                {{ (!empty($apsp->fixed_production) ? $apsp->fixed_production : '') }} {{ (!empty($apsp->unit_name) ? $apsp->unit_name : '') }}
                            </td>
                            <td>
                                {{ (!empty($apsp->half_yearly_production) ? $apsp->half_yearly_production : '') }} {{ (!empty($apsp->unit_name) ? $apsp->unit_name : '') }}
                            </td>
                            <td>
                                {{ (!empty($apsp->half_yearly_import) ? $apsp->half_yearly_import : '') }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
</body>
</html>