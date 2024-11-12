<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset("assets/stylesheets/styles.css") }}"/>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css"
          integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">

</head>
<body>

<section class="content" id="applicationForm">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12" style="text-align: center">
                        <img src="assets/images/bida_logo.png" style="width: 100px"/><br/>
                        <br>
                        Bangladesh Investment Development Authority (BIDA)<br/>
                        Application for Industrial IRC Recommendation
                    </div>
                </div>
                <div class="panel panel-info" id="inputForm">
                    <div class="panel-heading">Application for Industrial Project Registration to Bangladesh</div>
                    <div class="panel-body">
                        <table width="100%">
                            <tr>
                                <td style="padding-top: 5px; padding-right: 5px; padding-left: 15px; padding-bottom: 5px;">
                                    Tracking no. : <span>{{ $appInfo->tracking_no  }}</span></td>
                                <td style="padding: 5px;">Date of Submission:
                                    <span> {{ \App\Libraries\CommonFunction::formateDate($appInfo->submitted_at)  }} </span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top: 5px; padding-right: 5px; padding-left: 15px; padding-bottom: 5px;">
                                    Current Status : <span>{{$appInfo->status_name}}</span></td>
                                <td style="padding: 5px;">Current Desk :
                                    @if($appInfo->desk_id != 0)
                                        <span>  {{ \App\Libraries\CommonFunction::getDeskName($appInfo->desk_id)  }} </span>
                                    @else
                                        <span>Applicant</span>
                                    @endif

                                </td>
                            </tr>
                        </table>

                        <!-- Inspection Submission Deadline Date -->
                        @if((in_array($appInfo->status_id, [40, 41, 42, 5, 2]) && Auth::user()->user_type != '5x505'))
                            <div class="panel panel-info">
                                <div class="panel-heading">Inspection Submission Deadline Date</div>
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        <table width="100%" cellpadding="10">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    Inspection Submission Deadline :
                                                    <span> {{ (!empty($appInfo->io_submission_deadline)) ? \App\Libraries\CommonFunction::changeDateFormat($appInfo->io_submission_deadline) : ''  }}</span>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="panel panel-info">
                            <div class="panel-heading">Basic Information</div>
                            <div class="panel-body">
                                <div class="col-md-12">
                                    <table width="100%" cellpadding="10">
                                        <tbody>
                                        <tr>
                                            <td>
                                                IRC type :
                                                <span> {{ (!empty($appInfo->irc_type_name)) ? $appInfo->irc_type_name : ''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Purpose for IRC Recommendation :
                                                <span> {{ (!empty($appInfo->purpose_name)) ? $appInfo->purpose_name : ''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Did you received BIDA Registration through online OSS? :
                                                <span> {{ (!empty($appInfo->last_br)) ? $appInfo->last_br : ''  }}</span>
                                            </td>
                                        </tr>
                                        @if($appInfo->last_br == 'yes')
                                            <tr>
                                                <td>
                                                    Please give your approved Registration Tracking ID. :
                                                    <span> {{ (!empty($appInfo->ref_app_tracking_no)) ? $appInfo->ref_app_tracking_no : ''  }}</span>
                                                </td>
                                            </tr>
                                        @endif
                                        @if($appInfo->last_br == 'no')
                                            <tr>
                                                <td>
                                                    Please give your manually approved BIDA Registration No. :
                                                    <span> {{ (!empty($appInfo->manually_approved_br_no)) ? $appInfo->manually_approved_br_no : ''  }}</span>
                                                </td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{---company information--}}
                        <div class="panel panel-info">
                            <div class="panel-heading">A. Company Information</div>
                            <div class="panel-body">
                                <div class="col-md-12">
                                    <table width="100%" cellpadding="10">
                                        <tbody>
                                        <tr>
                                            <td colspan="2">
                                                Name of the Organization/ Company/ Industrial Project :
                                                <span> {{ (!empty($appInfo->company_id)) ? $appInfo->company_name : ''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                Name of the Organization/ Company/ Industrial Project (বাংলা):
                                                <span> {{ (!empty($appInfo->company_id)) ? $appInfo->company_name_bn : ''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                Type of the organization :
                                                <span>{{ (!empty($appInfo->organization_type_id)) ? $eaOrganizationType[$appInfo->organization_type_id]:''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                Status of the organization :
                                                <span> {{ (!empty($appInfo->organization_status_id)) ? $eaOrganizationStatus[$appInfo->organization_status_id]:''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Ownership status :
                                                <span>{{ (!empty($appInfo->ownership_status_id)) ? $eaOwnershipStatus[$appInfo->ownership_status_id]:''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Country of Origin :
                                                <span> {{ (!empty($appInfo->country_of_origin_id)) ? $countries[$appInfo->country_of_origin_id]:''  }}</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="2">
                                                Name of the project :
                                                <span>{{ (!empty($appInfo->project_name)) ? $appInfo->project_name : ''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Business Sector (BBS Class Code) :
                                                <span> {{ (!empty($appInfo->class_code)) ? $appInfo->class_code :''  }}</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th colspan="2">Other info. based on your business class (Code
                                                = {{ (!empty($appInfo->class_code)) ? $appInfo->class_code :''  }})
                                            </th>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <table class="table table-striped table-bordered dt-responsive"
                                                       cellspacing="0" width="100%">
                                                    <thead>
                                                    <tr>
                                                        <th scope="col">Category</th>
                                                        <th scope="col">Code</th>
                                                        <th scope="col">Description</th>
                                                    </tr>
                                                    </thead>
                                                    @if(!empty($business_code))
                                                        <tbody>
                                                        <tr>
                                                            <td>Section</td>
                                                            <td>{{ $business_code[0]['section_code'] }}</td>
                                                            <td>{{ $business_code[0]['section_name'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Division</td>
                                                            <td>{{ $business_code[0]['division_code'] }}</td>
                                                            <td>{{ $business_code[0]['division_name'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Group</td>
                                                            <td>{{ $business_code[0]['group_code'] }}</td>
                                                            <td>{{ $business_code[0]['group_name'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Class</td>
                                                            <td>{{ $business_code[0]['code'] }}</td>
                                                            <td>{{ $business_code[0]['name'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Sub class</td>
                                                            <td colspan="2">{{ (!empty($sub_class->name)) ? $sub_class->name : 'Other' }}</td>
                                                        </tr>
                                                        @if($appInfo->sub_class_id == 0)
                                                            <tr>
                                                                <td>Other sub class code</td>
                                                                <td colspan="2">{{ (!empty($appInfo->other_sub_class_code)) ? $appInfo->other_sub_class_code : '' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Other sub class name</td>
                                                                <td colspan="2">{{ (!empty($appInfo->other_sub_class_name)) ? $appInfo->other_sub_class_name : '' }}</td>
                                                            </tr>
                                                        @endif
                                                        </tbody>
                                                    @endif
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="padding: 5px;">
                                                Major activities in brief :
                                                <span>{{ (!empty($appInfo->major_activities)) ? $appInfo->major_activities :''  }}</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{---CEO information--}}
                        <div class="panel panel-info">
                            <div class="panel-heading">B. Information of Principal Promoter/Chairman/Managing
                                Director/CEO
                            </div>
                            <div class="panel-body">
                                <div class="col-md-12">
                                    <table width="100%" cellpadding="10">
                                        <tbody>
                                        <tr>
                                            <td width="50%" style="padding: 5px;">
                                                Country :
                                                <span> {{ (!empty($appInfo->ceo_country_id)) ? $countries[$appInfo->ceo_country_id]:''  }}</span>
                                            </td>
                                            <td width="50%" style="padding: 5px;">
                                                Date of Birth :
                                                <span>{{ (!empty($appInfo->ceo_dob)) ? date('d-M-Y', strtotime($appInfo->ceo_dob)): '' }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            @if($appInfo->ceo_country_id == 18)
                                                <td width="50%" style="padding: 5px;">
                                                    NID No. :
                                                    <span>{{ (!empty($appInfo->ceo_nid)) ? $appInfo->ceo_nid:''  }}</span>
                                                </td>
                                            @else
                                                <td width="50%" style="padding: 5px;">
                                                    Passport No. :
                                                    <span>{{ (!empty($appInfo->ceo_passport_no)) ? $appInfo->ceo_passport_no:''  }}</span>
                                                </td>
                                            @endif

                                            <td width="50%" style="padding: 5px;">
                                                Designation :
                                                <span>{{ (!empty($appInfo->ceo_designation)) ? $appInfo->ceo_designation:''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="50%" style="padding: 5px;">
                                                Full Name :
                                                <span> {{ (!empty($appInfo->ceo_full_name)) ? $appInfo->ceo_full_name:''  }}</span>
                                            </td>
                                            @if($appInfo->ceo_country_id == 18)
                                                <td width="50%" style="padding: 5px;">
                                                    District/City/State :
                                                    <span>{{ (!empty($appInfo->ceo_district_id)) ? $districts[$appInfo->ceo_district_id]:''  }}</span>
                                                </td>
                                            @else
                                                <td width="50%" style="padding: 5px;">
                                                    City :
                                                    <span>{{ (!empty($appInfo->ceo_city)) ? $appInfo->ceo_city:''  }}</span>
                                                </td>
                                            @endif
                                        </tr>
                                        <tr>
                                            @if($appInfo->ceo_country_id == 18)
                                                <td width="50%" style="padding: 5px;">
                                                    Police Station/Town :
                                                    <span> {{ (!empty($appInfo->ceo_thana_id)) ? $thana[$appInfo->ceo_thana_id]:''  }}</span>
                                                </td>
                                            @else
                                                <td width="50%" style="padding: 5px;">
                                                    State/Province :
                                                    <span>{{ (!empty($appInfo->ceo_state)) ? $appInfo->ceo_state:''  }}</span>
                                                </td>
                                            @endif

                                            <td width="50%" style="padding: 5px;">
                                                Post/Zip Code :
                                                <span>{{ (!empty($appInfo->ceo_post_code)) ? $appInfo->ceo_post_code:''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="50%" style="padding: 5px;">
                                                House,Flat/Apartment,Road :
                                                <span> {{ (!empty($appInfo->ceo_address)) ? $appInfo->ceo_address:''  }}</span>
                                            </td>
                                            <td width="50%" style="padding: 5px;">
                                                Telephone No :
                                                <span>{{ (!empty($appInfo->ceo_telephone_no)) ? $appInfo->ceo_telephone_no:''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="50%" style="padding: 5px;">
                                                Mobile No :
                                                <span> {{ (!empty($appInfo->ceo_mobile_no)) ? $appInfo->ceo_mobile_no:''  }}</span>
                                            </td>
                                            <td width="50%" style="padding: 5px;">
                                                Father's Name :
                                                <span> {{ (!empty($appInfo->ceo_father_name)) ? $appInfo->ceo_father_name:''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="50%" style="padding: 5px;">
                                                Email :
                                                <span>{{ (!empty($appInfo->ceo_email)) ? $appInfo->ceo_email:''  }}</span>
                                            </td>
                                            <td width="50%" style="padding: 5px;">
                                                Mother's Name :
                                                <span>{{ (!empty($appInfo->ceo_mother_name)) ? $appInfo->ceo_mother_name:''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="50%" style="padding: 5px;">
                                                Fax No :
                                                <span>{{ (!empty($appInfo->ceo_fax_no)) ? $appInfo->ceo_fax_no:''  }}</span>
                                            </td>
                                            <td width="50%" style="padding: 5px;">
                                                Spouse name :
                                                <span>{{ (!empty($appInfo->ceo_spouse_name)) ? $appInfo->ceo_spouse_name:''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="50%" style="padding: 5px;">
                                                Gender :
                                                <span>{{ (!empty($appInfo->ceo_gender)) ? $appInfo->ceo_gender : '' }}</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{---Office address----}}
                        <div class="panel panel-info">
                            <div class="panel-heading">Office Address</div>
                            <div class="panel-body">
                                <div class="col-md-12">
                                    <table width="100%" cellpadding="10">
                                        <tbody>
                                        <tr>
                                            <td width="50%" style="padding: 5px;">
                                                Division :
                                                <span> {{ (!empty($appInfo->office_division_id)) ? $divisions[$appInfo->office_division_id]:''  }}</span>
                                            </td>
                                            <td width="50%" style="padding: 5px;">
                                                District :
                                                <span> {{ (!empty($appInfo->office_district_id)) ? $districts[$appInfo->office_district_id]:''  }}</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="50%" style="padding: 5px;">
                                                Police Station :
                                                <span> {{ (!empty($appInfo->office_thana_id)) ? $thana[$appInfo->office_thana_id]:''  }}</span>
                                            </td>
                                            <td width="50%" style="padding: 5px;">
                                                Post Office :
                                                <span> {{ (!empty($appInfo->office_post_office)) ? $appInfo->office_post_office:''  }}</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="50%" style="padding: 5px;">
                                                Post Code :
                                                <span>{{ (!empty($appInfo->office_post_code)) ? $appInfo->office_post_code:''  }}</span>
                                            </td>
                                            <td width="50%" style="padding: 5px;">
                                                Address :
                                                <span> {{ (!empty($appInfo->office_address)) ? $appInfo->office_address:''  }}</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="50%" style="padding: 5px;">
                                                Telephone No :
                                                <span>{{ (!empty($appInfo->office_telephone_no)) ? $appInfo->office_telephone_no:''  }}</span>
                                            </td>
                                            <td width="50%" style="padding: 5px;">
                                                Mobile No :
                                                <span> {{ (!empty($appInfo->office_mobile_no)) ? $appInfo->office_mobile_no:''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="50%" style="padding: 5px;">
                                                Fax No :
                                                <span>{{ (!empty($appInfo->office_fax_no)) ? $appInfo->office_fax_no:''  }}</span>
                                            </td>
                                            <td width="50%" style="padding: 5px;">
                                                Email :
                                                <span>{{ (!empty($appInfo->office_email)) ? $appInfo->office_email:''  }}</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{---Factory Address----}}
                        <div class="panel panel-info">
                            <div class="panel-heading">Factory Address(This would be IRC address)</div>
                            <div class="panel-body">
                                <div class="col-md-12">
                                    <table width="100%" cellpadding="10">
                                        <tbody>
                                        <tr>
                                            <td width="50%" style="padding: 5px;">
                                                District :
                                                <span> {{ (!empty($appInfo->factory_district_id)) ? $districts[$appInfo->factory_district_id]:''  }}</span>
                                            </td>
                                            <td width="50%" style="padding: 5px;">
                                                Police Station :
                                                <span> {{ (!empty($appInfo->factory_thana_id)) ? $thana[$appInfo->factory_thana_id]:''  }}</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="50%" style="padding: 5px;">
                                                Post Office :
                                                <span> {{ (!empty($appInfo->factory_post_office)) ? $appInfo->factory_post_office:''  }}</span>
                                            </td>
                                            <td width="50%" style="padding: 5px;">
                                                Post Code :
                                                <span>{{ (!empty($appInfo->factory_post_code)) ? $appInfo->factory_post_code:''  }}</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="50%" style="padding: 5px;">
                                                Address :
                                                <span> {{ (!empty($appInfo->factory_address)) ? $appInfo->factory_address:''  }}</span>
                                            </td>
                                            <td width="50%" style="padding: 5px;">
                                                Telephone No :
                                                <span>{{ (!empty($appInfo->factory_telephone_no)) ? $appInfo->factory_telephone_no:''  }}</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="50%" style="padding: 5px;">
                                                Mobile No :
                                                <span> {{ (!empty($appInfo->factory_mobile_no)) ? $appInfo->factory_mobile_no:''  }}</span>
                                            </td>
                                            <td width="50%" style="padding: 5px;">
                                                Fax No :
                                                <span>{{ (!empty($appInfo->factory_fax_no)) ? $appInfo->factory_fax_no:''  }}</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <br/>
                            </div>
                        </div>

                        {{--Registration Information--}}
                        <div class="panel panel-info">
                            <div class="panel-heading">Registration Information</div>
                            <div class="panel-body">
                                <div class="col-md-12">
                                    {{--                                    <div>1. Project Status</div>--}}
                                    <table width="100%" cellpadding="10">
                                        <tbody>
                                        <tr>
                                            <td>
                                                1. Project Status : <span> {{ (!empty($appInfo->project_status_id)) ? $projectStatusList[$appInfo->project_status_id]:''  }}</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <br>

                                <div class="col-md-12">
                                    {{--                                    <div>2. Date of commercial operation</div>--}}
                                    <table width="100%" cellpadding="10">
                                        <tbody>
                                        <tr>
                                            <td>
                                                2. Date of commercial operation : <span> {{ (!empty($appInfo->commercial_operation_date)) ? date('d-M-Y',strtotime($appInfo->commercial_operation_date)) : ''}}</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <br/>

                                <div class="col-md-12">
                                    <div>3. Investment</div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered" cellspacing="0"
                                               width="100%">
                                            <tbody id="investment_tbl">
                                            <tr>
                                                <th colspan="3">Items</th>
                                            </tr>

                                            <tr>
                                                <th width="50%">Fixed Investment</th>
                                                <td width="25%"></td>
                                                <td width="25%"></td>

                                            </tr>
                                            <tr>
                                                <td> &nbsp;&nbsp;&nbsp;&nbsp; Land (Million)</td>
                                                <td>{{ (!empty($appInfo->local_land_ivst) ? $appInfo->local_land_ivst : '') }}</td>
                                                <td>{{isset($currencies[$appInfo->local_land_ivst_ccy]) ? $currencies[$appInfo->local_land_ivst_ccy]:""}}</td>

                                            </tr>
                                            <tr>
                                                <td> &nbsp;&nbsp;&nbsp;&nbsp; Building (Million)</td>
                                                <td>{{(!empty($appInfo->local_building_ivst) ? $appInfo->local_building_ivst : '')}}</td>
                                                <td>{{isset($currencies[$appInfo->local_building_ivst_ccy]) ? $currencies[$appInfo->local_land_ivst_ccy]:""}}</td>

                                            </tr>
                                            <tr>
                                                <td> &nbsp;&nbsp;&nbsp;&nbsp; Machinery & Equipment (Million)</td>
                                                <td>{{(!empty($appInfo->local_machinery_ivst) ? $appInfo->local_machinery_ivst : '')}}</td>
                                                <td>{{isset($currencies[$appInfo->local_machinery_ivst_ccy]) ? $currencies[$appInfo->local_machinery_ivst_ccy]:""}}</td>
                                            </tr>
                                            <tr>
                                                <td> &nbsp;&nbsp;&nbsp;&nbsp; Others (Million)</td>
                                                <td>{{(!empty($appInfo->local_others_ivst) ? $appInfo->local_others_ivst : '')}}</td>
                                                <td>{{isset($currencies[$appInfo->local_others_ivst_ccy]) ? $currencies[$appInfo->local_others_ivst_ccy]:""}}</td>

                                            </tr>
                                            <tr>
                                                <td>&nbsp;&nbsp;&nbsp;&nbsp; Working Capital (Three Months)
                                                    (Million)
                                                </td>
                                                <td>{{ (!empty($appInfo->local_wc_ivst) ? $appInfo->local_wc_ivst : '') }}</td>
                                                <td>{{isset($currencies[$appInfo->local_wc_ivst_ccy]) ? $currencies[$appInfo->local_wc_ivst_ccy]:""}}</td>
                                            </tr>
                                            <tr>
                                                <td> &nbsp;&nbsp;&nbsp;&nbsp; Total Investment (Million) (BDT)</td>
                                                <td colspan="3">
                                                    {{ (!empty($appInfo->total_fixed_ivst_million) ? CommonFunction::convertToMillionAmount($appInfo->total_fixed_ivst_million) : '') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td> &nbsp;&nbsp;&nbsp;&nbsp; Total Investment (BDT)</td>
                                                <td colspan="3">
                                                    {{ (!empty($appInfo->total_fixed_ivst) ? CommonFunction::convertToBdtAmount($appInfo->total_fixed_ivst) : '') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td> &nbsp;&nbsp;&nbsp;&nbsp; Dollar exchange rate (USD)</td>
                                                <td colspan="3">
                                                    {{ (!empty($appInfo->usd_exchange_rate) ? $appInfo->usd_exchange_rate : '') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td> &nbsp;&nbsp;&nbsp;&nbsp; Total Fee (BDT)</td>
                                                <td colspan="3">
                                                    {{ (!empty($appInfo->total_fee) ? CommonFunction::convertToBdtAmount($appInfo->total_fee) : '') }}
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <br/>

                                <div class="col-md-12">
                                    <div>4. Source of Finance</div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered" cellspacing="0"
                                               width="100%">
                                            <tbody>
                                            <tr>
                                                <td width="50%"><strong>(a)</strong> Local Equity (Million)</td>
                                                <td width="50%">{{(!empty($appInfo->finance_src_loc_equity_1) ? $appInfo->finance_src_loc_equity_1 : '')}}</td>
                                            </tr>
                                            <tr>
                                                <td>Foreign Equity (Million)</td>
                                                <td>{{ (!empty($appInfo->finance_src_foreign_equity_1) ? $appInfo->finance_src_foreign_equity_1 : '') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Total Equity (Million)</th>
                                                <td>{{ (!empty($appInfo->finance_src_loc_total_equity_1) ? CommonFunction::convertToMillionAmount($appInfo->finance_src_loc_total_equity_1) : '') }}</td>
                                            </tr>

                                            <tr>
                                                <td><strong>(b)</strong> Local Loan (Million)</td>
                                                <td>{{ (!empty($appInfo->finance_src_loc_loan_1) ? $appInfo->finance_src_loc_loan_1 : '') }}</td>
                                            </tr>
                                            <tr>
                                                <td>Foreign Loan (Million)</td>
                                                <td>{{ (!empty($appInfo->finance_src_foreign_loan_1) ? $appInfo->finance_src_foreign_loan_1 : '') }}</td>
                                            </tr>

                                            <tr>
                                                <th>Total Loan (Million)</th>
                                                <td>{{ (!empty($appInfo->finance_src_total_loan) ? CommonFunction::convertToMillionAmount($appInfo->finance_src_total_loan) : '') }}</td>
                                            </tr>

                                            <tr>
                                                <th>Total Financing Million (a+b)</th>
                                                <td>{{ !empty($appInfo->finance_src_loc_total_financing_m) ? CommonFunction::convertToMillionAmount($appInfo->finance_src_loc_total_financing_m) : '' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Total Financing BDT (a+b)</th>
                                                <td>{{ !empty($appInfo->finance_src_loc_total_financing_1) ? CommonFunction::convertToBdtAmount($appInfo->finance_src_loc_total_financing_1) : '' }}</td>
                                            </tr>

                                            </tbody>
                                        </table>
                                        <table class="table table-striped table-bordered" cellspacing="0"
                                               width="100%" id="financeTableId">
                                            <thead>
                                            <tr>
                                                <th colspan="4">
                                                    <i class="fa fa-question-circle" data-toggle="tooltip"
                                                       data-placement="top"
                                                       title="From the above information, the values of &quot;Local Equity (Million)&quot; and &quot;Local Loan (Million)&quot; will go into the
                                                           Equity Amount&quot; and &quot;Loan Amount&quot; respectively for Bangladesh. The summation of the &quot;Equity Amount&quot; and &quot;Loan Amount&quot; of other countries will be equal to the values of &quot;Foreign Equity (Million)&quot; and &quot;Foreign Loan (Million)&quot; respectively.">
                                                    </i>
                                                    Country wise source of finance (Million BDT)
                                                </th>
                                            </tr>
                                            </thead>

                                            <tr>
                                                <td>#</td>
                                                <td>Country</td>
                                                <td>Equity Amount</td>
                                                <td>Loan Amount</td>
                                            </tr>

                                            @if(count($source_of_finance) > 0)
                                                <?php $i = 1; ?>
                                                @foreach($source_of_finance as $finance)
                                                    <tr>
                                                        <td>{{ $i++ }}</td>
                                                        <td>{{ ($finance->country_id != 0 ? $countries[$finance->country_id] : '') }}</td>
                                                        <td>{{ CommonFunction::convertToMillionAmount($finance->equity_amount) }}</td>
                                                        <td>{{ CommonFunction::convertToMillionAmount($finance->loan_amount) }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </table>
                                    </div>
                                </div>
                                <br/>

                                <div class="col-md-12">
                                    <div>5. Manpower of the organization</div>
                                    <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                        <tr>
                                            <td class="text-center" style="padding: 5px;" colspan="3">Local (a)</td>
                                            <td class="text-center" style="padding: 5px;" colspan="3">Foreign (b)
                                            </td>
                                            <td class="text-center" style="padding: 5px;">Grand Total</td>
                                            <td class="text-center" style="padding: 5px;" colspan="2">Ratio</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td class="text-center" style="padding: 5px;">Executive</td>
                                            <td class="text-center" style="padding: 5px;">Supporting stuff</td>
                                            <td class="text-center" style="padding: 5px;">Total</td>
                                            <td class="text-center" style="padding: 5px;">Executive</td>
                                            <td class="text-center" style="padding: 5px;">Supporting stuff</td>
                                            <td class="text-center" style="padding: 5px;">Total</td>
                                            <td class="text-center" style="padding: 5px;">(a+b)</td>
                                            <td class="text-center" style="padding: 5px;">Local</td>
                                            <td class="text-center" style="padding: 5px;">Foreign</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 5px;">
                                                <span> {{ (!empty($appInfo->local_male))? $appInfo->local_male:''  }}</span>
                                            </td>
                                            <td style="padding: 5px;">
                                                <span> {{ (!empty($appInfo->local_female))? $appInfo->local_female:''  }}</span>
                                            </td>
                                            <td style="padding: 5px;">
                                                <span> {{ (!empty($appInfo->local_total))? $appInfo->local_total:''  }}</span>
                                            </td>
                                            <td style="padding: 5px;">
                                                <span> {{ (!empty($appInfo->foreign_male))? $appInfo->foreign_male:''  }}</span>
                                            </td>
                                            <td style="padding: 5px;">
                                                <span> {{ (!empty($appInfo->foreign_female))? $appInfo->foreign_female:''  }}</span>
                                            </td>
                                            <td style="padding: 5px;">
                                                <span> {{ (!empty($appInfo->foreign_total))? $appInfo->foreign_total:''  }}</span>
                                            </td>
                                            <td style="padding: 5px;">
                                                <span> {{ (!empty($appInfo->manpower_total))? $appInfo->manpower_total:''  }}</span>
                                            </td>
                                            <td style="padding: 5px;">
                                                <span> {{ (!empty($appInfo->manpower_local_ratio))? $appInfo->manpower_local_ratio:''  }}</span>
                                            </td>
                                            <td style="padding: 5px;">
                                                <span> {{ (!empty($appInfo->manpower_foreign_ratio))? $appInfo->manpower_foreign_ratio:''  }}</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <br/>

                                <div class="col-md-12">
                                    <div>6. Sales (in 100%)</div>
                                    <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                        <tbody>
                                        <tr>
                                            <td>Local : {{ (!empty($appInfo->local_sales)) ? $appInfo->local_sales :''  }}</td>
                                            <td>Foreign : {{ (!empty($appInfo->foreign_sales)) ? $appInfo->foreign_sales :''  }}</td>
                                            <td>Total in % : {{ (!empty($appInfo->total_sales)) ? $appInfo->total_sales :''  }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <br/>

                                <div class="col-md-12">
                                    <div>7. Annual production capacity ‍as per BIDA Registration/Amendment</div>
                                    <table>
                                        <tr>
                                            <td>Annual production start date :
                                                {{ ((!empty($appInfo->annual_production_start_date)) ? date('d-M-Y', strtotime($appInfo->annual_production_start_date)) : '') }}
                                            </td>
                                        </tr>
                                    </table>

                                    {{--raw materials--}}
                                    @if($appInfo->purpose_id != 2)
                                        <table class="table table-striped table-bordered dt-responsive"
                                               cellspacing="0" width="100%">
                                            <thead>
                                            <tr>
                                                <th colspan="6">Raw Materials</th>
                                            </tr>
                                            <tr>
                                                <td>Name of Product</td>
                                                <td>Unit of Quantity</td>
                                                <td>Quantity</td>
                                                <td>Price (USD)</td>
                                                <td colspan='2'>Sales Value in BDT (million)</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(count($annualProductionCapacity) > 0)
                                                @foreach($annualProductionCapacity as $value1)
                                                    <tr>
                                                        <td width="40%">{{ (!empty($value1->product_name)) ? $value1->product_name : ''  }}</td>
                                                        <td width="10%">{{ (!empty($value1->quantity_unit)) ? $productUnit[$value1->quantity_unit] : ''  }}</td>
                                                        <td width="10%">{{ (!empty($value1->quantity)) ? $value1->quantity : ''  }}</td>
                                                        <td width="10%">{{ (!empty($value1->price_usd)) ? $value1->price_usd : ''  }}</td>
                                                        <td width="20%"
                                                            colspan='2'>{{ (!empty($value1->price_taka)) ? CommonFunction::convertToMillionAmount($value1->price_taka) : ''  }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                        <br/>

                                        <?php $count = 1; ?>
                                        @foreach($annualProductionCapacity as $apc)
                                            <span>
                                               <?php echo $count++; ?>. প্রতি {{ (!empty($apc->unit_of_product) ? $apc->unit_of_product : '') }} {{ (!empty($apc->unit_name) ? $apc->unit_name : '') }}
                                                {{ (!empty($apc->product_name) ? $apc->product_name : '') }} উৎপাদনের জন্য কাঁচামাল প্রয়োজন
                                                    {{ (!empty($apc->raw_material_total_price) ? $apc->raw_material_total_price : '') }} টাকার
                                            </span>
                                            <?php
                                            DB::statement(DB::raw('set @rownum=0'));
                                            $raw_material = App\Modules\IrcRecommendationNew\Models\RawMaterial::where('apc_product_id', $apc->id)
                                                ->get([DB::raw('@rownum := @rownum+1 AS sl'), 'irc_raw_material.*']);
                                            ?>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                                                    <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th class="text-center">Name</th>
                                                        <th class="text-center">HS Code</th>
                                                        <th class="text-center">Quantity</th>
                                                        <th class="text-center">Price (BD)</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($raw_material as $data)
                                                        <tr>
                                                            <td>{{ $data->sl }}</td>
                                                            <td>{{ $data->product_name }}</td>
                                                            <td>{{ $data->hs_code }}</td>
                                                            <td>{{ $data->quantity }}</td>
                                                            <td>{{ $data->price_taka }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                                @endforeach
                                            </div>
                                            <br/>
                                            @endif

                                            {{--Spare Parts--}}
                                            @if($appInfo->purpose_id != 1)
                                                <table class="table table-striped table-bordered" cellspacing="0"
                                                       width="100%" id="financeTableId">
                                                    <thead>
                                                    <tr>
                                                        <th colspan="5">Spare Parts</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Name of Product</td>
                                                        <td>Unit of Quantity</td>
                                                        <td>Quantity</td>
                                                        <td>Price (USD)</td>
                                                        <td>Value Taka (in million)</td>
                                                    </tr>
                                                    </thead>
                                                    <?php $inc = 0; ?>
                                                    @foreach($annualProductionSpareParts as $eachProductionSpare)
                                                        <tr>
                                                            <td>{{ ($eachProductionSpare->product_name ? $eachProductionSpare->product_name : '') }}</td>
                                                            <td>{{ ($eachProductionSpare->quantity_unit ? $productUnit[$eachProductionSpare->quantity_unit] : '') }}</td>
                                                            <td>{{ ($eachProductionSpare->quantity ? $eachProductionSpare->quantity : '') }}</td>
                                                            <td>{{ ($eachProductionSpare->price_usd ? $eachProductionSpare->price_usd : '') }}</td>
                                                            <td>{{ ($eachProductionSpare->price_taka ? $eachProductionSpare->price_taka : '') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                                <br/>
                                            @endif

                                </div>

                                <div class="col-md-12">
                                    <div>8. Existing machines ‍as per BIDA Registration/Amendment</div>

                                    {{--Spare Parts--}}
                                    @if($appInfo->purpose_id != 1 && count($existing_machines_spare) > 0)
                                        <table class="table table-striped table-bordered" cellspacing="0"
                                               width="100%" id="financeTableId">
                                            <thead>
                                            <tr>
                                                <th colspan="6">Spare Parts</th>
                                            </tr>
                                            <tr>
                                                <td>L/C Number</td>
                                                <td>LC Date</td>
                                                <td>L/C Value (In Foreign Currency)</td>
                                                <td>Value (In BDT)</td>
                                                <td>L/C Opening Bank & Branch Name</td>
                                                <td>Attachment</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($existing_machines_spare as $EM_spare)
                                                <tr>
                                                    <td>{{ ($EM_spare->lc_no ? $EM_spare->lc_no : '') }}</td>
                                                    <td>{{ ((!empty($EM_spare->lc_date)) ? date('d-M-Y', strtotime($EM_spare->lc_date)) : '') }}</td>
                                                    <td>{{ ($EM_spare->lc_value_currency ? $currencies[$EM_spare->lc_value_currency] : '') }}</td>
                                                    <td>{{ ($EM_spare->value_bdt ? $EM_spare->value_bdt : '') }}</td>
                                                    <td>{{ ($EM_spare->lc_bank_branch ? $EM_spare->lc_bank_branch : '') }}</td>
                                                    <td>
                                                        @if(!empty($EM_spare->attachment))
                                                            <a target="_blank" class="btn btn-xs btn-primary"
                                                               href="{{URL::to('/uploads/'.$EM_spare->attachment)}}"
                                                               title="{{ $EM_spare->attachment}}">
                                                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                                                Open File
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td colspan="3"><span class="pull-right">Total</span></td>
                                                <td>{{ $total_existing_machines_spare }}</td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                        <br>
                                    @endif

                                    {{--As Per L/C Open--}}
                                    <table class="table table-striped table-bordered" cellspacing="0"
                                           width="100%" id="financeTableId">
                                        <thead>
                                        <tr>
                                            <th colspan="7">As Per L/C Open</th>
                                        </tr>
                                        <tr>
                                            <td>Description of Machine</td>
                                            <td>Unit of Quantity</td>
                                            <td>Quantity (A)</td>
                                            <td colspan="2">Unit Price (B)</td>
                                            <td>Price Foreign Currency (A X B)</td>
                                            <td>Price BDT (C)</td>
                                            <td>Value Taka (in million)</td>
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
                                    <br>

                                    {{--As per Local Procurement/ Collection--}}
                                    <table class="table table-striped table-bordered" cellspacing="0"
                                           width="100%" id="financeTableId">
                                        <thead>
                                        <tr>
                                            <th colspan="7">As per Local Procurement/ Collection</th>
                                        </tr>
                                        <tr>
                                            <td>Description of Machine</td>
                                            <td>Unit of Quantity</td>
                                            <td>Quantity (A)</td>
                                            <td>Unit Price (B)</td>
                                            <td>Price BDT (A X B) </td>
                                            <td>Value Taka (in million)</td>
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
                                <br/>

                                <div class="col-md-12">
                                    <div>9. Public utility service</div>
                                    @if($appInfo->public_land == 1)
                                        <label class="checkbox-inline">
                                            <img src="assets/images/checked.png" alt="Land" width="10" height="10"/> Land
                                        </label>
                                    @endif

                                    @if($appInfo->public_electricity == 1)
                                        <label class="checkbox-inline">
                                            <img src="assets/images/checked.png" alt="Electricity" width="10" height="10"/> Electricity
                                        </label>
                                    @endif

                                    @if($appInfo->public_gas == 1)
                                        <label class="checkbox-inline">
                                            <img src="assets/images/checked.png" alt="Gas" width="10" height="10"/> Gas
                                        </label>
                                    @endif

                                    @if($appInfo->public_telephone == 1)
                                        <label class="checkbox-inline">
                                            <img src="assets/images/checked.png" alt="Telephone" width="10" height="10"/> Telephone
                                        </label>
                                    @endif

                                    @if($appInfo->public_road == 1)
                                        <label class="checkbox-inline">
                                            <img src="assets/images/checked.png" alt="Road" width="10" height="10"/> Road
                                        </label>
                                    @endif

                                    @if($appInfo->public_water == 1)
                                        <label class="checkbox-inline">
                                            <img src="assets/images/checked.png" alt="Water" width="10" height="10"/> Water
                                        </label>
                                    @endif

                                    @if($appInfo->public_drainage == 1)
                                        <label class="checkbox-inline">
                                            <img src="assets/images/checked.png" alt="Drainage" width="10" height="10"/> Drainage
                                        </label>
                                    @endif

                                    @if($appInfo->public_others == 1)
                                        <label class="checkbox-inline">
                                            <img src="assets/images/checked.png" alt="Others" width="10" height="10"/> Others
                                        </label>
                                    @endif
                                </div>
                                <br/>

                                <div class="col-md-12">
                                    <div>10. Trade licence details</div>
                                    <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                        <tbody>
                                        <tr>
                                            <td>Trade Licence Number :
                                                <span> {{ (!empty($appInfo->trade_licence_num)) ? $appInfo->trade_licence_num :''  }}</span>
                                            </td>
                                            <td>Issuing Authority :
                                                <span> {{ (!empty($appInfo->trade_licence_issuing_authority)) ? $appInfo->trade_licence_issuing_authority:''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Issue Date :
                                                <span> {{ (!empty($appInfo->trade_licence_issue_date)) ? date('d-M-Y', strtotime($appInfo->trade_licence_issue_date)) :''  }}</span>
                                            </td>
                                            <td>Issuing Authority :
                                                <span> {{ (!empty($appInfo->trade_licence_validity_period)) ? $appInfo->trade_licence_validity_period:''  }}</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <br/>

                                <div class="col-md-12">
                                    <div>11. Incorporation</div>
                                    <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                        <tbody>
                                        <tr>
                                            <td>Incorporation Number :
                                                <span> {{ (!empty($appInfo->inc_number)) ? $appInfo->inc_number :''  }}</span>
                                            </td>
                                            <td>Issuing Authority :
                                                <span> {{ (!empty($appInfo->inc_issuing_authority)) ? $appInfo->inc_issuing_authority:''  }}</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <br/>

                                <div class="col-md-12">
                                    <div>12. TIN</div>
                                    <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                        <tbody>
                                        <tr>
                                            <td>TIN Number :
                                                <span> {{ (!empty($appInfo->tin_number)) ? $appInfo->tin_number :''  }}</span>
                                            </td>
                                            <td>Issuing Authority :
                                                <span> {{ (!empty($appInfo->tin_issuing_authority)) ? $appInfo->tin_issuing_authority:''  }}</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <br/>

                                <div class="col-md-12">
                                    <div>13. Fire license information</div>
                                    <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                        <tbody>
                                        @if($appInfo->fire_license_info == 'already_have')
                                            <tr>
                                                <td colspan="2"><span>Already have License No.</span></td>
                                            </tr>
                                            <tr>
                                                <td>Fire License Number :
                                                    <span> {{ (!empty($appInfo->fl_number)) ? $appInfo->fl_number :'' }}</span>
                                                </td>
                                                <td>Expiry Date :
                                                    <span> {{ (!empty($appInfo->fl_expire_date)) ? date('d-M-Y', strtotime($appInfo->fl_expire_date)) :'' }}</span>
                                                </td>
                                            </tr>
                                        @endif

                                        @if($appInfo->fire_license_info == 'applied_for')
                                            <tr>
                                                <td colspan="2"><span>Applied for License No.</span></td>
                                            </tr>
                                            <tr>
                                                <td>Application Number :
                                                    <span> {{ (!empty($appInfo->fl_application_number)) ? $appInfo->fl_application_number :'' }}</span>
                                                </td>
                                                <td>Apply Date :
                                                    <span> {{ (!empty($appInfo->fl_apply_date)) ? date('d-M-Y', strtotime($appInfo->fl_apply_date)) :'' }}</span>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td colspan="2">
                                                Issuing Authority :
                                                <span> {{ (!empty($appInfo->fl_issuing_authority)) ? $appInfo->fl_issuing_authority : '' }}</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                </div>
                                <br/>

                                <div class="col-md-12">
                                    <div>14. Environment/ Site clearance certificate</div>
                                    <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                        <tbody>
                                        @if($appInfo->fire_license_info == 'already_have')
                                            <tr>
                                                <td colspan="2"><span>Already have License No.</span></td>
                                            </tr>
                                            <tr>
                                                <td>Environment License No :
                                                    <span> {{ (!empty($appInfo->el_number)) ? $appInfo->el_number :'' }}</span>
                                                </td>
                                                <td>Expiry Date :
                                                    <span> {{ (!empty($appInfo->el_expire_date)) ? date('d-M-Y', strtotime($appInfo->el_expire_date)) :'' }}</span>
                                                </td>
                                            </tr>
                                        @endif

                                        @if($appInfo->fire_license_info == 'applied_for')
                                            <tr>
                                                <td colspan="2"><span>Applied for License No.</span></td>
                                            </tr>
                                            <tr>
                                                <td>Application Number :
                                                    <span> {{ (!empty($appInfo->el_application_number)) ? $appInfo->el_application_number :'' }}</span>
                                                </td>
                                                <td>Apply Date :
                                                    <span> {{ (!empty($appInfo->el_apply_date)) ? date('d-M-Y', strtotime($appInfo->el_apply_date)) :'' }}</span>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td colspan="2">
                                                Issuing Authority :
                                                <span> {{ (!empty($appInfo->el_issuing_authority)) ? $appInfo->el_issuing_authority : '' }}</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <br/>

                                <div class="col-md-12">
                                    <div>15. Bank information</div>
                                    <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                        <tbody>
                                        <tr>
                                            <td colspan="2">
                                                Bank name :
                                                <span> {{ (!empty($appInfo->bank_name)) ? $appInfo->bank_name : '' }}</span>
                                            </td>
                                            <td>
                                                Branch name :
                                                <span> {{ (!empty($appInfo->branch_name)) ? $appInfo->branch_name : '' }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                Bank address :
                                                <span> {{ (!empty($appInfo->bank_address)) ? $appInfo->bank_address : '' }}</span>
                                            </td>
                                            <td>
                                                Account number :
                                                <span> {{ (!empty($appInfo->bank_account_number)) ? $appInfo->bank_account_number : '' }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                Account title :
                                                <span> {{ (!empty($appInfo->bank_account_title)) ? $appInfo->bank_account_title : '' }}</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <br/>

                                <div class="col-md-12">
                                    <div>16. Membership of Chamber/ Association information</div>
                                    <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                        <tbody>
                                        <tr>
                                            <td colspan="2">
                                                Membership number :
                                                <span> {{ (!empty($appInfo->assoc_membership_number)) ? $appInfo->assoc_membership_number : '' }}</span>
                                            </td>
                                            <td>
                                                Chamber name :
                                                <span> {{ (!empty($appInfo->assoc_chamber_name)) ? $appInfo->assoc_chamber_name : '' }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                Issuing date :
                                                <span> {{ (!empty($appInfo->assoc_issuing_date)) ? date('d-M-Y', strtotime($appInfo->assoc_issuing_date)) : '' }}</span>
                                            </td>
                                            <td>
                                                Expiry date :
                                                <span> {{ (!empty($appInfo->assoc_expire_date)) ? date('d-M-Y', strtotime($appInfo->assoc_expire_date)) : '' }}</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <br/>

                                <div class="col-md-12">
                                    <div>17. BIN/ VAT</div>
                                    <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                        <tbody>
                                        <tr>
                                            <td colspan="2">
                                                BIN/ VAT number :
                                                <span> {{ (!empty($appInfo->bin_vat_number)) ? $appInfo->bin_vat_number : '' }}</span>
                                            </td>
                                            <td>
                                                Issuing date :
                                                <span> {{ (!empty($appInfo->bin_vat_issuing_date)) ? date('d-M-Y', strtotime($appInfo->bin_vat_issuing_date)) : '' }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                Issuing Authority :
                                                <span> {{ (!empty($appInfo->bin_vat_issuing_authority)) ? $appInfo->bin_vat_issuing_authority : '' }}</span>
                                            </td>
                                            <td>
                                                Expiry date :
                                                <span> {{ (!empty($appInfo->bin_vat_expire_date)) ? date('d-M-Y', strtotime($appInfo->bin_vat_expire_date)) : '' }}</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <br/>

                                <div class="col-md-12">
                                    <div>18. Other Licenses/ NOC/ Permission/ Registration</div>
                                    <table class="table table-striped table-bordered" cellspacing="0"
                                           width="100%" id="financeTableId">
                                        <thead>
                                        <tr>
                                            <td>#</td>
                                            <td>Licence Name</td>
                                            <td>Licence No/ Issue No</td>
                                            <td>Issuing Authority</td>
                                            <td>Date of Issue</td>
                                        </tr>
                                        </thead>
                                        @if(count($otherLicence)>0)
                                            <?php $inc = 1; ?>
                                            @foreach($otherLicence as $otherLicence)
                                                <tr>
                                                    <td>{{ $inc++ }}</td>
                                                    <td>{{ ($otherLicence->licence_name ? $otherLicence->licence_name : '') }}</td>
                                                    <td>{{ ($otherLicence->licence_no ? $otherLicence->licence_no : '') }}</td>
                                                    <td>{{ ($otherLicence->issuing_authority ? $otherLicence->issuing_authority : '') }}</td>
                                                    <td>{{ (!empty($otherLicence->issue_date) ? date('d-M-Y', strtotime($otherLicence->issue_date)) : '') }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </table>

                                </div>
                                <br/>


                            </div>
                        </div>
                        {{--End Registration Information--}}

                        {{-- Information about Declaration and undertaking --}}
                        <div class="panel panel-info">
                            <div class="panel-heading">Declaration and undertaking</div>
                            <div class="panel-body">
                                <div class="col-md-12">
                                    <br>
                                    <table width="100%" cellpadding="10">
                                        <tr>
                                            <td colspan="2" style="padding: 5px;">
                                                <strong class="text-info">Authorized Personnel
                                                    of the organization: </strong>
                                            </td>
                                        </tr>
                                        <tbody>
                                        <tr>
                                            <td width="50%" style="padding: 5px;">
                                                Full Name :
                                                <span> {{ (!empty($appInfo->auth_full_name)) ? $appInfo->auth_full_name:'N/A'  }}</span>
                                            </td>
                                            <td width="50%" style="padding: 5px;">
                                                Designation :
                                                <span>{{ (!empty($appInfo->auth_designation)) ? $appInfo->auth_designation:'N/A'  }}</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="50%" style="padding: 5px;">
                                                Mobile No :
                                                <span> {{ (!empty($appInfo->auth_mobile_no)) ? $appInfo->auth_mobile_no:'N/A'  }}</span>
                                            </td>
                                            <td width="50%" style="padding: 5px;">
                                                Email address :
                                                <span>{{ (!empty($appInfo->auth_email)) ? $appInfo->auth_email:'N/A'  }}</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="50%" style="padding: 5px;">
                                                Profile Picture :

                                                @if(file_exists("users/upload/".$appInfo->auth_image))
                                                    <img class="img-thumbnail" width="60" height="60"
                                                         src="users/upload/{{ $appInfo->auth_image }}"
                                                         alt="Applicant Photo"/>
                                                @else
                                                    <img class="img-thumbnail" width="60" height="60"
                                                         src="assets/images/no_image.png" alt="Image not found"/>
                                                @endif
                                            </td>
                                            <td colspan="3" style="padding: 5px;">
                                                Date :
                                                <?php echo date('F d,Y')?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <div class="row">
                                        <div class="col-md-12">
                                            &nbsp; @if($appInfo->accept_terms == 1) <img src="assets/images/checked.png"
                                                                                         width="10" height="10"/> @else
                                                <img src="assets/images/unchecked.png" width="10" height="10"/> @endif
                                            <label for="acceptTerms-2"
                                                   class="col-md-11 text-left required-star form-control">
                                                <span>
                                                    I do here by declare that the information given above is true to the
                                                best of my knowledge and I shall be liable for any false information/
                                                system is given.
                                                </span>
                                            </label>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</body>
</html>