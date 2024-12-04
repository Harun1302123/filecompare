<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset("assets/stylesheets/styles.css") }}" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
</head>
<body>
<div class="col-md-12" style="text-align: center">
    <img src="uploads/logo/bida-logo_200X69.png" class="img-responsive"/>
</div>

<div class="col-md-12">
    <div style="padding-top: 50px; font-size: 18px; color: #0f0f0f;">
        <div>বরাবর,</div>
        <div>পরিচালক (নিঃ সঃ স্থাঃ শিঃ)</div>
        <div>বাংলাদেশ বিনিয়োগ উন্নয়ন কর্তৃপক্ষ</div>
        <div>প্রধানমন্ত্রীর কার্যালয়</div>
        <div>প্লট নং: ই-৬/বি, আগারগাঁও</div>
        <div>শেরে-ই-বাংলা নগর, ঢাকা </div>

        <div style="padding: 10px 0px 10px 0px;">বিষয়ঃ {{ (!empty($inspectionInfo->company_name) ? $inspectionInfo->company_name : '') }} এর উপর পরিদর্শন প্রতিবেদন প্রসংঙ্গে </div>

        <div>
            <div style="width: 70%; float: left">স্মারক নং- {{ $inspectionInfo->tracking_no }}</div>
            <div style="width: 30%; float: left">তারিখঃ {{ CommonFunction::formateDate($inspectionInfo->submitted_at) }}</div>
        </div>

        <div>উপযুক্ত বিষয় ও সূত্রের পরিপ্রেক্ষিতে {{ (!empty($inspectionInfo->company_name) ? $inspectionInfo->company_name : '') }} এর উপর পরিদর্শন প্রতিবেদনের (এক) প্রস্থ সদয় অবগতি ও প্রয়োজনীয় ব্যবস্থা গ্রহনের জন্য এতদসঙ্গে প্রেরন করা হল। </div><br>
        <div>সংযুক্ত বর্ণনামতে </div>

        <div style="width: 40%; padding: 80px 0 0 400px; text-align: center">
            @if(file_exists("users/signature/".$inspectionInfo->io_signature))
                <img src="users/signature/{{ $inspectionInfo->io_signature }}" alt="singnature"/>
            @endif
            <span>{{ !empty($inspectionInfo->io_name) ? $inspectionInfo->io_name : '' }}</span><br>
            <span>{{ !empty($inspectionInfo->io_designation) ? $inspectionInfo->io_designation : '' }}</span><br>
            বাংলাদেশ বিনিয়োগ উন্নয়ন কর্তৃপক্ষ, ঢাকা
        </div>
    </div>
</div>

<newpage>
    <div style="text-align: center; font-size: 24px; text-decoration: underline">পরিদর্শন প্রতিবেদন</div>
    <div class="col-md-12">
        <table class="table table-responsive">
            <tr>
                <td><strong>Tracking No.:</strong> {{ $inspectionInfo->tracking_no }}</td>
                <td><strong>Date of submission:</strong> {{ CommonFunction::formateDate($inspectionInfo->submitted_at) }}</td>
            </tr>
        </table>

        <table class="table table-responsive" style="font-size: 16px">
            <tr>
                <td style="font-size: 20px" colspan="2">১. প্রকল্পের তথ্য</td>
            </tr>
            <tr>
                <td style="padding-left: 50px" width="40%">শিল্প প্রকল্পের নাম </td>
                <td>{{ (!empty($inspectionInfo->company_name) ? $inspectionInfo->company_name : '') }}</td>
            </tr>
            <tr>
                <td style="padding-left: 50px">শঅফিসের ঠিকানা </td>
                <td>{{ (!empty($inspectionInfo->office_address) ? $inspectionInfo->office_address : '') }}</td>
            </tr>
            <tr>
                <td style="padding-left: 50px">কারখানার ঠিকানা </td>
                <td>{{ (!empty($inspectionInfo->factory_address) ? $inspectionInfo->factory_address : '') }}</td>
            </tr>
        </table>

        <table class="table table-responsive" style="font-size: 16px">
            <tr>
                <td style="font-size: 20px" colspan="2">২. শিল্প খাতের তথ্য</td>
            </tr>
            <tr>
                <td style="padding-left: 50px" width="40%">শশিল্প খাত </td>
                <td>{{ (!empty($inspectionInfo->industrial_sector) ? $inspectionInfo->industrial_sector : '') }}</td>
            </tr>
        </table>

        <table class="table table-responsive" style="font-size: 16px">
            <tr>
                <td style="font-size: 20px" colspan="2">৩. বিনিয়োগের তথ্য</td>
            </tr>
            <tr>
                <td style="padding-left: 50px" width="40%">বিনিয়োগের প্রকৃতি </td>
                <td>{{ (!empty($inspectionInfo->organization_status_name) ? $inspectionInfo->organization_status_name : '') }}</td>
            </tr>
        </table>

        <table class="table table-responsive" style="font-size: 16px">
            <tr>
                <td style="font-size: 20px">৪. উদ্যোক্তার তথ্য</td>
            </tr>
            <tr>
                <td style="padding-left: 50px" width="40%">উদ্যোক্তার নাম </td>
                <td>{{ (!empty($inspectionInfo->entrepreneur_name) ? $inspectionInfo->entrepreneur_name : '') }}</td>
            </tr>
            <tr>
                <td style="padding-left: 50px">ঠিকানা </td>
                <td>{{ (!empty($inspectionInfo->entrepreneur_address) ? $inspectionInfo->entrepreneur_address : '') }}</td>
            </tr>
        </table>

        <table class="table table-responsive" style="font-size: 16px">
            <tr>
                <td style="font-size: 20px" colspan="2">৫. নিবন্ধনকারীর তথ্য</td>
            </tr>
            <tr>
                <td style="padding-left: 50px" width="40%">কতৃপক্ষের নাম </td>
                <td>{{ (!empty($inspectionInfo->registering_authority_name) ? $inspectionInfo->registering_authority_name : '') }}</td>
            </tr>
            <tr>
                <td style="padding-left: 50px">স্মারক নং </td>
                <td>{{ (!empty($inspectionInfo->registering_authority_memo_no) ? $inspectionInfo->registering_authority_memo_no : '') }}</td>
            </tr>
            <tr>
                <td style="padding-left: 50px">নিবন্ধনের সংখ্যা </td>
                <td>{{ (!empty($inspectionInfo->reg_no) ? $inspectionInfo->reg_no : '') }}</td>
            </tr>
            <tr>
                <td style="padding-left: 50px">নিবন্ধনের তারিখ </td>
                <td>{{ (!empty($inspectionInfo->date_of_registration) ? date('d-M-Y', strtotime($inspectionInfo->date_of_registration)) : '') }}</td>
            </tr>
        </table>

        <table class="table table-responsive" style="font-size: 16px" width="100%">
            <tr>
                <td style="font-size: 20px" colspan="2">৬. বিবিধ রেজিস্ট্রেশন নং</td>
            </tr>
            <tr>
                <td style="padding-left: 50px">(ক) ট্রেড লাইসেন্স</td>
            </tr>
            <tr>
                <td style="padding-left: 50px">
                    <table class="table table-responsive" width="100%">
                        <tr>
                            <td style="padding-left: 50px" width="35%">ট্রেড লাইসেন্স নম্বর </td>
                            <td>{{ (!empty($inspectionInfo->trade_licence_num) ? $inspectionInfo->trade_licence_num : '') }}</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 50px">ইস্যুয়িং অথরিটি </td>
                            <td>{{ (!empty($inspectionInfo->trade_licence_issuing_authority) ? $inspectionInfo->trade_licence_issuing_authority : '') }}</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 50px">ইস্যুয়িং ডেট</td>
                            <td>{{ (!empty($inspectionInfo->trade_licence_issue_date ) ? '' : date('d-M-Y', strtotime($inspectionInfo->trade_licence_issue_date))) }}</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 50px">মেয়াদ উত্তীর্ণ সময়কাল </td>
                            <td>{{ (!empty($inspectionInfo->trade_licence_validity_period) ? date('d-M-Y', strtotime($inspectionInfo->trade_licence_validity_period)) : '')}}</td>                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="padding-left: 50px">(খ) টি আই এন নং</td>
            </tr>
            <tr>
                <td style="padding-left: 50px">
                    <table class="table table-responsive">
                        <tr>
                            <td style="padding-left: 50px" width="35%">টি আইএন নম্বর</td>
                            <td>{{ (!empty($inspectionInfo->tin_number) ? $inspectionInfo->tin_number : '') }}</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 50px">ইস্যুয়িং অথরিটি</td>
                            <td>{{ (!empty($inspectionInfo->tin_issuing_authority) ? $inspectionInfo->tin_issuing_authority : '') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="padding-left: 50px">(গ) ব্যাংক প্রত্যয়ন পত্র</td>
            </tr>
            <tr>
                <td style="padding-left: 50px">
                    <table class="table table-responsive" width="100%">
                        <tr>
                            <td style="padding-left: 50px" width="35%">হিসাব নম্বর</td>
                            <td>{{ (!empty($inspectionInfo->bank_account_number) ? $inspectionInfo->bank_account_number : '') }}</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 50px">একাউন্ট নাম</td>
                            <td>{{ (!empty($inspectionInfo->bank_account_title) ? $inspectionInfo->bank_account_title : '') }}</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 50px">ব্যাংকের নাম</td>
                            <td>{{ (!empty($inspectionInfo->bank_name) ? $inspectionInfo->bank_name : '') }}</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 50px">ব্যাংক শাখার নাম</td>
                            <td>{{ (!empty($inspectionInfo->branch_name) ? $inspectionInfo->branch_name : '') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="padding-left: 50px">(ঘ) এসোসিয়েশন সদস্য নং</td>
            </tr>
            <tr>
                <td style="padding-left: 50px">
                    <table class="table table-responsive" width="100%">
                        <tr>
                            <td style="padding-left: 50px" width="35%">সদস্য নম্বর </td>
                            <td>{{ (!empty($inspectionInfo->assoc_membership_number) ? $inspectionInfo->assoc_membership_number : '') }}</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 50px">চেম্বার নাম</td>
                            <td>{{ (!empty($inspectionInfo->assoc_chamber_name) ? $inspectionInfo->assoc_chamber_name : '') }}</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 50px">ইস্যুয়িং ডেট</td>
                            <td>{{ (!empty($inspectionInfo->assoc_issuing_date) ? date('d-M-Y', strtotime($inspectionInfo->assoc_issuing_date)) : '')}}</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 50px">মেয়াদ উত্তীর্ণ তারিখ</td>
                            <td>{{ (!empty($inspectionInfo->assoc_expire_date) ? date('d-M-Y', strtotime($inspectionInfo->assoc_expire_date)) : '')}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="padding-left: 50px">(ঙ) ফায়ার লাইসেন্স নং</td>
            </tr>
            <tr>
                <td style="padding-left: 50px">
                    <table class="table table-responsive" width="100%">
                        {{--Already have--}}
                        @if(!empty($inspectionInfo->fl_number) || !empty($inspectionInfo->fl_expire_date))
                            <tr>
                                <td style="padding-left: 50px" width="35%">ফায়ার লাইসেন্স নম্বর</td>
                                <td>{{ (!empty($inspectionInfo->fl_number) ? $inspectionInfo->fl_number : '') }}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 50px">মেয়াদ উত্তীর্ণ তারিখ</td>
                                <td>{{ (!empty($inspectionInfo->fl_expire_date) ? date('d-M-Y', strtotime($inspectionInfo->fl_expire_date)) : '')}}</td>
                            </tr>
                        @endif

                        {{--Applied for--}}
                        @if(!empty($inspectionInfo->fl_application_number) || !empty($inspectionInfo->fl_apply_date))
                            <tr>
                                <td style="padding-left: 50px" width="35%">আবেদন সংখ্যা</td>
                                <td>{{ (!empty($inspectionInfo->fl_application_number) ? $inspectionInfo->fl_application_number : '') }}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 50px">আবেদনের তারিখ</td>
                                <td>{{ (!empty($inspectionInfo->fl_apply_date) ? date('d-M-Y', strtotime($inspectionInfo->fl_apply_date)) : '')}}</td>
                            </tr>
                        @endif

                        <tr>
                            <td style="padding-left: 50px">ইস্যুয়িং অথরিটি</td>
                            <td>{{ (!empty($inspectionInfo->fl_issuing_authority) ? $inspectionInfo->fl_issuing_authority : '') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="padding-left: 50px">(চ) ইনকর্পোরেশন</td>
            </tr>
            <tr>
                <td style="padding-left: 50px">
                    <table class="table table-responsive" width="100%">
                        <tr>
                            <td style="padding-left: 50px" width="35%">ইনকর্পোরেশন নম্বর</td>
                            <td>{{ (!empty($inspectionInfo->inc_number) ? $inspectionInfo->inc_number : '') }}</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 50px">ইস্যুয়িং অথরিটি</td>
                            <td>{{ (!empty($inspectionInfo->inc_issuing_authority) ? $inspectionInfo->inc_issuing_authority : '') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="padding-left: 50px">(ছ) পরিবেশ ছাড়পত্র</td>
            </tr>
            <tr>
                <td style="padding-left: 50px">
                    <table class="table table-responsive" width="100%">
                        {{--Already have--}}
                        @if(!empty($inspectionInfo->el_number) || !empty($inspectionInfo->el_expire_date))
                            <tr>
                                <td style="padding-left: 50px" width="35%">পরিবেশ ছাড়পত্র নম্বর</td>
                                <td>{{ (!empty($inspectionInfo->el_number) ? $inspectionInfo->el_number : '') }}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 50px">মেয়াদ উত্তীর্ণ তারিখ</td>
                                <td>{{ (!empty($inspectionInfo->el_expire_date) ? date('d-M-Y', strtotime($inspectionInfo->el_expire_date)) : '')}}</td>
                            </tr>
                        @endif

                        {{--Applied for--}}
                        @if(!empty($inspectionInfo->el_application_number) || !empty($inspectionInfo->el_apply_date))
                            <tr>
                                <td style="padding-left: 50px" width="35%">আবেদন সংখ্যা</td>
                                <td>{{ (!empty($inspectionInfo->el_application_number) ? $inspectionInfo->el_application_number : '') }}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 50px">আবেদনের তারিখ</td>
                                <td>{{ (!empty($inspectionInfo->el_apply_date) ? date('d-M-Y', strtotime($inspectionInfo->el_apply_date)) : '')}}</td>
                            </tr>
                        @endif

                        <tr>
                            <td style="padding-left: 50px">ইস্যুয়িং অথরিটি </td>
                            <td>{{ (!empty($inspectionInfo->el_issuing_authority) ? $inspectionInfo->el_issuing_authority : '') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="table table-responsive" style="font-size: 16px">
            <tr>
                <td style="font-size: 20px" colspan="2">৭. প্রকল্পের অবস্থান</td>
            </tr>
            <tr>
                <td style="padding-left: 50px" width="40%">প্রকল্পের অবস্থান</td>
                <td>{{ (!empty($inspectionInfo->project_status_name) ? $inspectionInfo->project_status_name : '') }}</td>
            </tr>
        </table>

        <table class="table table-bordered table-responsive" cellspacing="0" width="100%" style="font-size: 16px;">
            <tr>
                <td style="font-size: 20px" colspan="2">৮. বিনিয়োজিত মূলধন</td>
            </tr>
            <tbody>
            <tr>
                <td style="padding-left: 5px" width="30%">জমি</td>
                <td>{{$inspectionInfo->local_land_ivst}} BDT</td>
            </tr>
            <tr>
                <td style="padding-left: 5px">ভবন</td>
                <td>{{$inspectionInfo->local_building_ivst}} BDT</td>
            </tr>
            <tr>
                <td style="padding-left: 5px"> যন্ত্রপাতি ও সরঞ্জামাদি (মিলিয়ন)</td>
                <td>{{$inspectionInfo->local_machinery_ivst}} BDT</td>
            </tr>
            <tr>
                <td style="padding-left: 5px"> অন্যান্য (মিলিয়ন)</td>
                <td>{{$inspectionInfo->local_others_ivst}} BDT</td>
            </tr>
            <tr>
                <td style="padding-left: 5px"> চলতি মূলধন (মিলিয়ন)</td>
                <td>{{$inspectionInfo->local_wc_ivst}} BDT</td>
            </tr>
            <tr>
                <td style="padding-left: 5px">মোট মূলধন(মিলিয়ন) (টাকা)</td>
                <td>{{$inspectionInfo->total_fixed_ivst_million}}</td>
            </tr>
            <tr>
                <td style="padding-left: 5px"> মোট মূলধন (টাকা)</td>
                <td>{{$inspectionInfo->total_fixed_ivst}}</td>
            </tr>
            <tr>
                <td style="padding-left: 5px"> ডলার এক্সচেঞ্জ রেট (USD)</td>
                <td>{{$inspectionInfo->usd_exchange_rate}}</td>
            </tr>
            <tr>
                <td style="padding-left: 5px">  টোটাল ফি (টাকা)</td>
                <td>{{$inspectionInfo->total_fee}}</td>
            </tr>
            </tbody>
        </table>

        <table class="table table-responsive" style="font-size: 16px" width="100%">
            <tr>
                <td style="font-size: 20px">৯. স্থাপিত যন্ত্রপাতির বিবরণ</td>
            </tr>
            <tr>
                <td style="padding-left: 50px" width="40%">স্থানীয় ভাবে সংগৃহীত (মিলিয়ন) (টাকা)</td>
                <td>{{ $inspectionInfo->em_local_total_taka_mil }}</td>
            </tr>
            <tr>
                <td style="padding-left: 50px">এলসিকৃত (মিলিয়ন) (টাকা)</td>
                <td>{{ $inspectionInfo->em_lc_total_taka_mil }}</td>
            </tr>
        </table>

    </div>
</newpage>
<pagebreak />
<newpage>
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 16px">
                <thead>
                <tr>
                    <td colspan="9" style="font-size: 20px">১০. জনবল</td>
                </tr>
                <tr>
                    <td colspan="3">বাংলাদেশী</td>
                    <td colspan="3">বিদেশী</td>
                    <td>সর্বমোট</td>
                    <td colspan="2">অনুপাত</td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>কার্যনির্বাহী</td>
                    <td>সাপোর্টিং</td>
                    <td>মোট (a)</td>
                    <td>কার্যনির্বাহী</td>
                    <td>সাপোর্টিং</td>
                    <td>মোট (b)</td>
                    <td>(a+b)</td>
                    <td>স্থানীয়</td>
                    <td>বিদেশী</td>
                </tr>
                <tr>
                    <td>
                        {{ (!empty($inspectionInfo->local_male)) ? $inspectionInfo->local_male : '' }}
                    </td>
                    <td>
                        {{ (!empty($inspectionInfo->local_female)) ? $inspectionInfo->local_female : '' }}
                    </td>
                    <td>
                        {{ (!empty($inspectionInfo->local_total)) ? $inspectionInfo->local_total : '' }}
                    </td>
                    <td>
                        {{ (!empty($inspectionInfo->foreign_male)) ? $inspectionInfo->foreign_male : '' }}
                    </td>
                    <td>
                        {{ (!empty($inspectionInfo->foreign_female)) ? $inspectionInfo->foreign_female : '' }}
                    </td>
                    <td>
                        {{ (!empty($inspectionInfo->foreign_total)) ? $inspectionInfo->foreign_total : '' }}
                    </td>
                    <td>
                        {{ (!empty($inspectionInfo->manpower_total)) ? $inspectionInfo->manpower_total : ''  }}
                    </td>
                    <td>
                        {{ (!empty($inspectionInfo->manpower_local_ratio)) ? $inspectionInfo->manpower_local_ratio : ''  }}
                    </td>
                    <td>
                        {{ (!empty($inspectionInfo->manpower_foreign_ratio)) ? $inspectionInfo->manpower_foreign_ratio : ''  }}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div style="font-size: 20px">১১. নিবন্ধপত্র / নিবন্ধনপত্রে সংশোধী অনুযায়ী বার্ষিক উৎপাদন ক্ষমতা</div>

        {{-- @if($inspectionInfo->irc_purpose != 2 && count($inspectionAnnualProductionCapacity) > 0) --}}
        @if($inspectionInfo->irc_purpose_id != 2 && count($annualProductionCapacity) > 0)
            <table class="table table-bordered dt-responsive" cellspacing="0" width="100%" style="font-size: 16px">
                <thead class="alert alert-info">
                <tr>
                    <td class="text-center  ">ক্রমিক নং</td>
                    <td class="text-center">পন্য/ সেবার নাম</td>
                    <td colspan="2" class="text-center">নির্ধারিত বার্ষিক উৎপাদন ক্ষমতা</td>
                </tr>
                </thead>
                <tbody>
                <?php $count = 1;?>
                {{-- @foreach($inspectionAnnualProductionCapacity as $apc) --}}
                @foreach($annualProductionCapacity as $apc)
                    <tr>
                        <td><?php echo $count++ ?></td>
                        <td>
                            {{ (!empty($apc->product_name) ? $apc->product_name : '') }}
                        </td>
                        <td>
                            {{-- {{ (!empty($apc->fixed_production) ? $apc->fixed_production : '') }} --}}
                            {{ (!empty($apc->quantity) ? $apc->quantity : '') }}
                        </td>
                        <td>
                            {{ (!empty($apc->unit_name) ? $apc->unit_name : '') }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif

        {{-- @if($inspectionInfo->irc_purpose != 1 && count($inspectionAnnualProductionSpareParts) > 0) --}}
        @if($inspectionInfo->irc_purpose_id != 1 && count($annualProductionSpareParts) > 0)
            <table class="table table-bordered dt-responsive" cellspacing="0" width="100%" style="font-size: 16px">
                <thead class="alert alert-info">
                <tr>
                    <td class="text-center">ক্রমিক নং</td>
                    <td class="text-center">পন্য/ সেবার নাম</td>
                    <td colspan="2" class="text-center">নির্ধারিত বার্ষিক উৎপাদন ক্ষমতা</td>
                </tr>
                </thead>
                <tbody>
                <?php $count = 1;?>
                {{-- @foreach($inspectionAnnualProductionSpareParts as $apsp) --}}
                @foreach($annualProductionSpareParts as $apsp)
                    <tr>
                        <td><?php echo $count++ ?></td>
                        <td>
                            {{ (!empty($apsp->product_name) ? $apsp->product_name : '') }}
                        </td>
                        <td>
                            {{-- {{ (!empty($apsp->fixed_production) ? $apsp->fixed_production : '') }} --}}
                            {{ (!empty($apsp->quantity) ? $apsp->quantity : '') }}
                        </td>
                        <td>
                            {{ (!empty($apsp->unit_name) ? $apsp->unit_name : '') }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif

        <div style="font-size: 20px">১২. এডহক ভিত্তিক কাঁচামালের ষান্মাসিক আমদানিস্বত্ব/ চাহিদা</div>
        @if($inspectionInfo->irc_purpose != 2 && count($inspectionAnnualProductionCapacity) > 0)
            <table class="table table-bordered dt-responsive" cellspacing="0" width="100%" style="font-size: 16px">
                <thead class="alert alert-info">
                <tr>
                    <td colspan="6">উদ্যোক্তা কর্তৃক দাখিল কৃত তথ্য অনুযায়ী:</td>
                </tr>
                </thead>
                <tbody>
                <?php $count = 1;?>
                @foreach($inspectionAnnualProductionCapacity as $apc)
                    <tr>
                        <td><?php echo $count++ ?></td>
                        <td width="15%">
                            <table>
                                <tr>
                                    <td>প্রতি</td>
                                    <td>
                                        {{ (!empty($apc->unit_of_product) ? $apc->unit_of_product : '') }}
                                    </td>
                                </tr>
                            </table>
                        <td width="15%">
                            {{ (!empty($apc->unit_name) ? $apc->unit_name : '') }}
                        </td>
                        <td width="15%">
                            {{ (!empty($apc->product_name) ? $apc->product_name : '') }}
                        </td>
                        <td width="25%"> উৎপাদনের জন্য কাঁচামাল প্রয়োজন</td>
                        <td width="30%">
                            <table>
                                <tr>
                                    <td>
                                        {{ (!empty($apc->raw_material_total_price) ? $apc->raw_material_total_price : '') }}
                                    </td>
                                    <td>টাকার</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <br>
            <span style="font-size: 16px">কাঁচামালের ষান্মাসিক আমদানিস্বত্ব/ চাহিদা</span>
            <br>
            <span>
                উদ্যোক্তা কর্তৃক কারখানায় স্থাপিত যন্ত্রপাতি (এলসিকৃত/ স্থানীয়ভাবে সংগ্রহীত), নিয়োজিত জনবল, অবকাঠামোগত সুবিধা বিবেচনাপূর্বক প্রদত্ত তথ্য এবং প্রতিষ্ঠান কর্তৃপক্ষের সাথে আলোচনার ভিত্তিতে কারখানাটির বার্ষিক উৎপাদন ক্ষমতা প্রাথমিকভাবে নিম্নরূপ নির্ধারণ করা যেতে পারে।
            </span>
            <table class="table table-bordered dt-responsive" cellspacing="0" width="100%" style="font-size: 16px">
                <thead class="alert alert-info">
                <tr>
                    <td>ক্রমিক নং</td>
                    <td>পন্য/ সেবার নাম</td>
                    <td>নির্ধারিত বার্ষিক উৎপাদন ক্ষমতা</td>
                    <td>ষান্মাসিক উৎপাদন ক্ষমতা</td>
                    <td>ষান্মাসিক আমদানিস্বত্ব (টাকা)</td>
                </tr>
                </thead>
                <tbody>
                <?php $count = 1;?>
                @foreach($inspectionAnnualProductionCapacity as $apc)
                    <tr>
                        <td><?php echo $count++ ?></td>
                        <td>{{ $apc->product_name }}</td>
                        <td>{{ $apc->fixed_production }}</td>
                        <td>{{ $apc->half_yearly_production }}</td>
                        <td>{{ $apc->half_yearly_import }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4"><span class="pull-right">মোট টাকা</span></td>
                    <td>
                        <span>{{ $inspectionInfo->apc_half_yearly_import_total }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="4"><span class="pull-right">অন্যান্য টাকার পরিমান</span></td>
                    <td>
                        <span>{{ $inspectionInfo->apc_half_yearly_import_other ?? 0 }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="4"><span class="pull-right">সর্বমোট টাকা</span></td>
                    <td>
                        <span>{{ ($inspectionInfo->apc_half_yearly_import_total ?? 0) + ($inspectionInfo->apc_half_yearly_import_other ?? 0) }}</span>
                    </td>
                </tr>
                </tfoot>
            </table>
            <br>
            <div class="row">
                <div class="form-group">
                    <div class="col-md-12">
                        {!! Form::label('apc_half_yearly_import_total_in_word','কথায়',['class'=>'text-left col-md-1 v_label']) !!}
                        <div class="col-md-11">
                            <span>{{ $inspectionInfo->apc_half_yearly_import_total_in_word }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($inspectionInfo->irc_purpose != 1)
            <div style="font-size: 20px">১৩. এডহক ভিত্তিক খুচরা যন্ত্রাংশের ষান্মাসিক আমদানিস্বত্ব/ চাহিদা</div>
            <span style="font-size: 16px">
            প্রতিষ্ঠান কর্তৃক এলসিকৃত মূলধনী যন্ত্রপারি মোট মূল্যের {{ $inspectionInfo->em_lc_total_taka_mil }}  {{ $inspectionInfo->em_lc_total_percent }}% হারে অর্থাৎ {{ $inspectionInfo->em_lc_total_five_percent }} কথায় ({{ $inspectionInfo->em_lc_total_five_percent_in_word }}) খুচরা যন্ত্রাংশের জন্য ষান্মাসিক আমদানিস্বত্ব নির্ধারন করা যেতে পারে।
        </span>
        @endif
        <br>
        <br>

        <div style="font-size: 20px">মন্তব্য</div>
        <span>{{$inspectionInfo->remarks}}</span>

        <br>
        <div style="width: 40%; padding: 50px 0px 0px 400px; text-align: center; font-size: 16px">
            @if(file_exists("users/signature/".$inspectionInfo->io_signature))
                <img src="users/signature/{{ $inspectionInfo->io_signature }}" alt="singnature"/>
            @endif
            <span>{{ !empty($inspectionInfo->io_name) ? $inspectionInfo->io_name : '' }}</span><br>
            <span>{{ !empty($inspectionInfo->io_designation) ? $inspectionInfo->io_designation : '' }}</span><br>
            বাংলাদেশ বিনিয়োগ উন্নয়ন কর্তৃপক্ষ, ঢাকা
        </div>
    </div>
</newpage>


</body>
</html>