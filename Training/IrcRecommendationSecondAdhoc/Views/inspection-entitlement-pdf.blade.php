<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<body>
     <div class="content">
         <br>
         <div class="row">
             <div class="col-md-12">
                 <row>
                     <div col-lg-12>
                         <strong>Ref No: </strong> {{ !empty($inspectionInfo->tracking_no) ? $inspectionInfo->tracking_no : '' }}
                     </div>
                 </row>

                 <row style="margin-top:10px">
                     <div col-lg-12 style="margin-top: 15px">
                         শিল্পের কাঁচামাল ও খুচরা যন্ত্রাংশ আমদানির অনুকূলে অনুজ্ঞা পত্রের সুপারিশ <br>
                         শিল্প খাত :<span style="font-size: 12px">{{$inspectionInfo->industrial_sector}}</span> <br>
                         ভিত্তি কাঁচামাল : উদ্যোক্তা কর্তৃক দাখিলকৃত তথ্য অনুযায়ী
                     </div>
                 </row>
                <br>
                <?php $count = 1; ?>

                @if(count($inspectionProductionCapacity) > 0)
                    @foreach($inspectionProductionCapacity as $apc)

                        <span style="font-size: 13px"><?php echo $count++; ?>) প্রতি <span style="font-size: 12px">{{ (!empty($apc->unit_of_product) ? $apc->unit_of_product : '') }} {{ (!empty($apc->unit_name) ? $apc->unit_name : '') }}
                                {{ (!empty($apc->product_name) ? $apc->product_name : '') }}</span> উৎপাদন করতে টাঃ
                                {{ (!empty($apc->raw_material_total_price) ? $apc->raw_material_total_price : '') }}/- মূল্যের
                                {{ ($count == count($inspectionProductionCapacity) ? ', ' : '') }}</span>
                    @endforeach
                      কাঁচামাল প্রয়োজন
                    <div style="margin-top: 5px"><div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <td class="text-center" style="font-size: 13px">কাঁচামালের বিবরণ</td>
                                    <td class="text-center" style="font-size: 13px">এইচ এস কোড নং</td>
                                    <td class="text-center" style="font-size: 13px">মূল্য</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="text-center" style="font-size: 13px">তালিকা সংযুক্ত</td>
                                    <td class="text-center" style="font-size: 13px">সংশ্লিষ্ট এইচ এস কোড সমূহ</td>
                                    <td class="text-center">
                                        <?php $count = 1; ?>
                                        @foreach($inspectionProductionCapacity as $apc)
                                            {{ $count++ }}) টাঃ {{ $apc->raw_material_total_price }}/-
                                            <br/>
                                        @endforeach
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div></div>

                @endif

                <table class="table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <td class="text-center" rowspan="2" style="font-size: 13px">শিল্প প্রতিষ্ঠানের নাম ও ঠিকানা</td>
                            <td class="text-center" rowspan="2" style="font-size: 13px">উৎপাদন শুরু হওয়ার তারিখ</td>
                            <td class="text-center" rowspan="2" style="font-size: 13px">ষান্মাসিক উৎপাদন ক্ষমতা</td>
                            <td class="text-center" colspan="2" style="text-align: center; font-size: 13px">ষান্মাসিক আমদানিসত্ব/চাহিদা</td>
                        </tr>
                        <tr>
                            <td class="text-center" style="font-size: 13px">কাঁচামাল</td>
                            <td class="text-center" style="font-size: 13px">খুচরা যন্ত্রাংশ</td>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td class="text-center" width="25%">{{$inspectionInfo->company_name}} <br> {{$inspectionInfo->factory_address}}</td>
                            <td class="text-center" width="20%">{{$inspectionInfo->project_status_name}}</td>
                            <td class="text-center" width="25%">
                                @if($inspectionInfo->irc_purpose_id != 2)
                                    <?php $count = 1; ?>
                                    @foreach($inspectionProductionCapacity as $apc)
                                        {{ $count++ }}) {{ (!empty($apc->product_name) ? $apc->product_name : '') }}-
                                        {{ (!empty($apc->half_yearly_production) ? $apc->half_yearly_production : '') }} {{ (!empty($apc->unit_name) ? $apc->unit_name : '') }}
                                        <br/>
                                    @endforeach
                                @endif
                                <br/>
                                @if($inspectionInfo->irc_purpose_id != 1)
                                    <?php $count = 1; ?>
                                    @foreach($inspectionProductionSpare as $apcSP)
                                        {{ $count++ }}) {{ (!empty($apcSP->product_name) ? $apcSP->product_name : '') }}-
                                        {{ (!empty($apcSP->half_yearly_production) ? $apcSP->half_yearly_production : '') }} {{ (!empty($apcSP->unit_name) ? $apcSP->unit_name : '') }}
                                        <br/>
                                    @endforeach
                                @endif
                            </td>

                            @if($inspectionInfo->irc_purpose_id != 2)
                                <td class="text-center" style="font-size: 13px">
                                    <?php $count = 1; ?>
                                    @foreach($inspectionProductionCapacity as $apc)
                                        {{ $count++ }}) টাঃ {{ $apc->half_yearly_import }}/-
                                        <br/>
                                    @endforeach
                                    মোট টাঃ {{ $inspectionInfo->apc_half_yearly_import_total }}/-
                                    <br/>
                                    অন্যান্য টাঃ {{ empty($inspectionInfo->apc_half_yearly_import_other) ? 0 : $inspectionInfo->apc_half_yearly_import_other }}/-
                                    <br/>
                                    সর্বমোট টাঃ {{ floatval(isset($inspectionInfo->apc_half_yearly_import_total) ? $inspectionInfo->apc_half_yearly_import_total : 0) + floatval(isset($inspectionInfo->apc_half_yearly_import_other) ? $inspectionInfo->apc_half_yearly_import_other : 0) }}/-
                                    <br/>
                                    ({{ $inspectionInfo->apc_half_yearly_import_total_in_word }})
                                    <br/>
                                </td>
                                @else
                                <td class="text-center">---</td>
                            @endif

                            @if($inspectionInfo->irc_purpose_id != 1)
                                <td style="font-size: 13px">
                                    <?php $count = 1; ?>
                                    @foreach($inspectionProductionSpare as $apcSP)
                                        {{ $count++ }}) টাঃ {{ $apcSP->half_yearly_import }}/-
                                        <br/>
                                    @endforeach
                                        মোট টাঃ {{ $inspectionInfo->em_lc_total_five_percent }}/-
                                        <br/>
                                        ({{ $inspectionInfo->em_lc_total_five_percent_in_word }})
                                        <br/>
                                </td>
                                @else
                                <td class="text-center">---</td>
                            @endif
                        </tr>
                    </tbody>
                </table>

                <table class="table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <td class="text-center" rowspan="2" style="font-size: 13px">পোষকের নাম</td>
                            <td class="text-center" rowspan="2" style="font-size: 13px">আনুমোদিত নিবন্ধনের সংখ্যা</td>
                            <td class="text-center" colspan="2" style="text-align: center; font-size: 13px">স্থাপিত যন্ত্রপাতির নাম</td>
                            <td class="text-center" rowspan="2" style="font-size: 13px">বার্ষিক উৎপাদন ক্ষমতা এবং শ্রমিক সংখ্যা</td>
                        </tr>
                        <tr>
                            <td class="text-center" style="font-size: 13px">স্থানীয় টাকা</td>
                            <td class="text-center" style="font-size: 13px">এলসিকৃত</td>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td class="text-center" width="25%">{{$inspectionInfo->registering_authority_name}}</td>
                            <td class="text-center" width="20%">{{$inspectionInfo->reg_no}}</td>

                            <td class="text-center" style="font-size: 13px">
                                টাঃ {{ $inspectionInfo->em_local_total_taka_mil }} মিঃ
                            </td>
                            <td class="text-center" style="font-size: 13px">
                                টাঃ {{ $inspectionInfo->em_lc_total_taka_mil }} মিঃ
                                <br/>
                            </td>

                            <td class="text-center" width="25%">
                                @if($inspectionInfo->irc_purpose_id != 2)
                                    <?php $count = 1; ?>
                                    @foreach($inspectionProductionCapacity as $apc)
                                        {{ $count++ }}) {{ (!empty($apc->product_name) ? $apc->product_name : '') }}-
                                        {{ (!empty($apc->fixed_production) ? $apc->fixed_production : '') }} {{ (!empty($apc->unit_name) ? $apc->unit_name : '') }}
                                        <br/>
                                    @endforeach
                                @endif
                                <br/>
                                @if($inspectionInfo->irc_purpose_id != 1)
                                    <?php $count = 1; ?>
                                    @foreach($inspectionProductionSpare as $apcSP)
                                        {{ $count++ }}) {{ (!empty($apcSP->product_name) ? $apcSP->product_name : '') }}-
                                        {{ (!empty($apcSP->fixed_production) ? $apcSP->fixed_production : '') }} {{ (!empty($apcSP->unit_name) ? $apcSP->unit_name : '') }}
                                        <br/>
                                    @endforeach
                                @endif
                                জনবল- {{ $inspectionInfo->manpower_total }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                 <row>
                     <div  style="font-size: 14px;border-top:2px solid lightgrey;">
                         <br>
                         <span style="font-size: 13px">মন্তব্য : </span>{{$inspectionInfo->entitlement_remarks}}
                     </div>
                 </row>
             </div>
         </div>
    </div>
</body>
</html>