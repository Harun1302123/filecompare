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
                        Application for Business Registration
                    </div>
                </div>
                <div class="panel panel-info" id="inputForm">
                    <div class="panel-heading">Application for Business Registration</div>
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

                        <div class="alert alert-info" style="margin-bottom: 5px">You have selected
                            <b>'{{$appInfo->divisional_office_name}}
                                '</b>, {{ $appInfo->divisional_office_address }}.
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">Company Information</div>
                            <div class="panel-body">
                                <div class="col-md-12">
                                    <table width="100%" cellpadding="10">
                                        <tbody>
                                        <tr>
                                            <td colspan="2" style="padding: 5px;">
                                                Name of Organization/ Company/ Industrial Project (English):
                                                <span> {{ (!empty($appInfo->company_id)) ? $userCompanyList[0]->company_name : ''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="padding: 5px;">
                                                Name of Organization/ Company/ Industrial Project (Bangla):
                                                <span> {{ (!empty($appInfo->company_id)) ? $userCompanyList[0]->company_name_bn : ''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="50%" style="padding: 5px;">
                                                Type of the organization :
                                                <span>{{ (!empty($appInfo->organization_type_id)) ? $eaOrganizationType[$appInfo->organization_type_id]:''  }}</span>
                                            </td>
                                            <td width="50%" style="padding: 5px;">
                                                Status of the organization :
                                                <span> {{ (!empty($appInfo->organization_status_id)) ? $eaOrganizationStatus[$appInfo->organization_status_id]:''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="50%" style="padding: 5px;">
                                                Ownership status :
                                                <span>{{ (!empty($appInfo->ownership_status_id)) ? $eaOwnershipStatus[$appInfo->ownership_status_id]:''  }}</span>
                                            </td>
                                            <td width="50%" style="padding: 5px;">
                                                Country of Origin :
                                                <span> {{ (!empty($appInfo->country_of_origin_id)) ? $countries[$appInfo->country_of_origin_id]:''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="50%" style="padding: 5px;">
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
                                            <th colspan="2">Info. based on your business class (Code
                                                = {{ (!empty($appInfo->class_code)) ? $appInfo->class_code :''  }})
                                            </th>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <table class="table table-striped table-bordered dt-responsive"
                                                       cellspacing="0" width="100%">
                                                    <thead>
                                                    <tr>
                                                        <th width="20%" scope="col">Category</th>
                                                        <th width="10%" scope="col">Code</th>
                                                        <th width="70%" scope="col">Description</th>
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
                                                <span>{{ (!empty($appInfo->ceo_dob)) ? date('d-M-Y', strtotime($appInfo->ceo_dob)):''  }}</span>
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
                                                    <span> {{ (!empty($appInfo->ceo_thana_id)) ? $thana_eng[$appInfo->ceo_thana_id]:''  }}</span>
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
                                                <span> {{ (!empty($appInfo->office_thana_id)) ? $thana_eng[$appInfo->office_thana_id]:''  }}</span>
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

                        <div class="panel panel-info">
                            <div class="panel-heading">Factory Address (Optional)</div>
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
                                                <span> {{ (!empty($appInfo->factory_thana_id)) ? $thana_eng[$appInfo->factory_thana_id]:''  }}</span>
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

                        <div id="" class="panel panel-info">

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div>1. Project Status
                                            :<span> {{ (!empty($appInfo->project_status_id)) ? $projectStatusList[$appInfo->project_status_id]:''  }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="panel-body">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <div>2. Annual Production Capacity</div>
                                        <label class="col-md-12 text-left"></label>
                                        <table id="productionCostTbl"
                                               class="table table-striped table-bordered dt-responsive" cellspacing="0"
                                               width="100%">
                                            <thead class="alert alert-info">
                                            <tr>
                                                <th valign="top" class="text-center valigh-middle">Name of Product
                                                    <span class="required-star"></span><br/>
                                                </th>

                                                <th valign="top" class="text-center valigh-middle">HS Code
                                                    <span class="required-star"></span><br/>
                                                </th>

                                                <th valign="top" class="text-center valigh-middle">Quantity
                                                    <span class="required-star"></span><br/>
                                                </th>

                                                <th valign="top" class="text-center valigh-middle">Price (USD)
                                                    <span class="required-star"></span><br/>
                                                </th>

                                                <th colspan='2' valign="top" class="text-center valigh-middle">Sales Value in BDT (million)
                                                    <span class="required-star"></span><br/>
                                                </th>


                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(count($la_annual_production_capacity)>0)
                                                @foreach($la_annual_production_capacity as $value1)
                                                    <tr>
                                                        <td>{{ (!empty($value1->product_name)) ? $value1->product_name:''  }}</td>
                                                        <td>{{ (!empty($value1->hs_code)) ? $value1->hs_code:''  }}</td>
                                                        <td>{{ (!empty($value1->quantity)) ? $value1->quantity:''  }}</td>
                                                        <td>{{ (!empty($value1->price_usd)) ? $value1->price_usd:''  }}</td>
                                                        <td colspan='2'>{{ (!empty($value1->price_taka)) ? $value1->price_taka:''  }}</td>
                                                    </tr>

                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12 {{$errors->has('commercial_operation_date') ? 'has-error': ''}}">
                                            {!! Form::label('commercial_operation_date','3. Date of Commercial Operation: ',['class'=>'col-md-4 text-left']) !!}

                                            {{$appInfo->commercial_operation_date}}

                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-2">
                                            {!! Form::label('local_sales','4. Sales (in 100%) ',['class'=>'col-md-12 text-left']) !!}
                                        </div>
                                        <div class="col-md-3 {{$errors->has('local_sales') ? 'has-error': ''}}">
                                            {!! Form::label('local_sales','Local :',['class'=>'col-md-3 text-left']) !!}
                                            {{ (!empty($appInfo->local_sales)) ? $appInfo->local_sales :''  }}
                                        </div>
                                        <div class="col-md-3 {{$errors->has('foreign_sales') ? 'has-error': ''}}">
                                            {!! Form::label('foreign_sales','Foreign :',['class'=>'col-md-3 text-left']) !!}
                                            {{ (!empty($appInfo->foreign_sales)) ? $appInfo->foreign_sales :''  }}
                                        </div>
                                        <div class="col-md-4 {{$errors->has('total_sales') ? 'has-error': ''}}">
                                            {!! Form::label('total_sales','Total in % :',['class'=>'col-md-3 text-left']) !!}
                                            {{ (!empty($appInfo->total_sales)) ? $appInfo->total_sales :''  }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <div>5. Manpower of the organization
                                                </div>
                                                <table class="table table-striped table-bordered" cellspacing="0"
                                                       width="100%">
                                                    <thead class="alert alert-info">
                                                    <tr>
                                                        <th class="alert alert-info" colspan="3">Local (Bangladesh
                                                            only)
                                                        </th>
                                                        <th class="alert alert-info" colspan="3">Foreign (Abroad
                                                            country)
                                                        </th>
                                                        <th class="alert alert-info" colspan="1">Grand total</th>
                                                        <th class="alert alert-info" colspan="2">Ratio</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="alert alert-info">Executive</th>
                                                        <th class="alert alert-info">Supporting Staff</th>
                                                        <th class="alert alert-info">Total (a)</th>
                                                        <th class="alert alert-info">Executive</th>
                                                        <th class="alert alert-info">Supporting Staff</th>
                                                        <th class="alert alert-info">Total (b)</th>
                                                        <th class="alert alert-info"> (a+b)</th>
                                                        <th class="alert alert-info">Local</th>
                                                        <th class="alert alert-info">Foreign</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="manpower">

                                                    <tr>
                                                        <td>
                                                            {{ (!empty($appInfo->local_male)) ? $appInfo->local_male:''  }}
                                                        </td>
                                                        <td>
                                                            {{ (!empty($appInfo->local_female)) ? $appInfo->local_female :''  }}
                                                        </td>
                                                        <td>
                                                            {{ (!empty($appInfo->local_total)) ? $appInfo->local_total:''  }}
                                                        </td>
                                                        <td>
                                                            {{ (!empty($appInfo->foreign_male)) ? $appInfo->foreign_male :''  }}
                                                        </td>
                                                        <td>
                                                            {{ (!empty($appInfo->foreign_female)) ? $appInfo->foreign_female :''  }}
                                                        </td>
                                                        <td>
                                                            {{ (!empty($appInfo->foreign_total)) ? $appInfo->foreign_total:''  }}
                                                        </td>
                                                        <td>
                                                            {{ (!empty($appInfo->manpower_total)) ? $appInfo->manpower_total:''  }}
                                                        </td>
                                                        <td>
                                                            {{ (!empty($appInfo->local_female)) ? $appInfo->local_female:''  }}
                                                        </td>
                                                        <td>
                                                            {{ (!empty($appInfo->manpower_foreign_ratio)) ? $appInfo->manpower_foreign_ratio:''  }}
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <label class="col-md-12 text-left"></label>
                                                <div>6. Investment</div>
                                                <table class="table table-striped table-bordered" cellspacing="0"
                                                       width="100%">
                                                    <tbody id="investment_tbl">
                                                    <tr>
                                                        <th class="alert alert-info">Items</th>
                                                        <th class="alert alert-info">Local (Million Taka)</th>
                                                    </tr>

                                                    <tr>
                                                        <th>Fixed Investment</th>
                                                        <td>
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td colspan='2'></td>
                                                                </tr>
                                                            </table>
                                                        </td>

                                                    </tr>
                                                    <tr>
                                                        <td> &nbsp;&nbsp;&nbsp;&nbsp; Land</td>
                                                        <td>
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td style="width:75%;">
                                                                        {{$appInfo->local_land_ivst}}
                                                                    </td>
                                                                    <td>
                                                                        {{isset($currencies[$appInfo->local_land_ivst_ccy]) ? $currencies[$appInfo->local_land_ivst_ccy]:""}}
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>

                                                    </tr>
                                                    <tr>
                                                        <td> &nbsp;&nbsp;&nbsp;&nbsp; Building</td>
                                                        <td>
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td style="width:75%;">
                                                                        {{$appInfo->local_building_ivst}}
                                                                    </td>
                                                                    <td>
                                                                        {{isset($currencies[$appInfo->local_building_ivst_ccy]) ? $currencies[$appInfo->local_land_ivst_ccy]:""}}
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>

                                                    </tr>
                                                    <tr>
                                                        <td> &nbsp;&nbsp;&nbsp;&nbsp; Machinery & Equipment</td>
                                                        <td>
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td style="width:75%;">
                                                                        {{$appInfo->local_machinery_ivst}}
                                                                    </td>
                                                                    <td>
                                                                        {{isset($currencies[$appInfo->local_machinery_ivst_ccy]) ? $currencies[$appInfo->local_machinery_ivst_ccy]:""}}

                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>

                                                    </tr>
                                                    <tr>
                                                        <td> &nbsp;&nbsp;&nbsp;&nbsp; Others</td>
                                                        <td>
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td style="width:75%;">
                                                                        {{$appInfo->local_others_ivst}}
                                                                    </td>
                                                                    <td>
                                                                        {{isset($currencies[$appInfo->local_others_ivst_ccy]) ? $currencies[$appInfo->local_others_ivst_ccy]:""}}

                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>

                                                    </tr>
                                                    <tr>
                                                        <th>Working Capital</th>
                                                        <td>
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td style="width:75%;">
                                                                        {{$appInfo->local_wc_ivst}}
                                                                    </td>
                                                                    <td>
                                                                        {{isset($currencies[$appInfo->local_wc_ivst_ccy]) ? $currencies[$appInfo->local_wc_ivst_ccy]:""}}
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>

                                                    </tr>
                                                    <tr>
                                                        <td> &nbsp;&nbsp;&nbsp;&nbsp; Total Investment (Million)</td>
                                                        <td>
                                                            <table style="width:100%;">
                                                                <tr>
                                                                    <td style="width:75%;">
                                                                        {{$appInfo->total_fixed_ivst_million}}
                                                                    </td>
                                                                    <td>
                                                                        @if(!empty($appInfo->project_profile_attachment))
                                                                            Project Profile:
                                                                            <a target="_blank" class="btn btn-xs btn-primary" href="{{URL::to('/uploads/'.$appInfo->project_profile_attachment)}}" title="{{$appInfo->project_profile_attachment}}">
                                                                                <i class="fa fa-file-pdf" aria-hidden="true"></i>
                                                                                Open File
                                                                            </a>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td> &nbsp;&nbsp;&nbsp;&nbsp; Total Investment (BDT)</td>
                                                        <td colspan="3">
                                                            {{$appInfo->total_fixed_ivst}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td> &nbsp;&nbsp;&nbsp;&nbsp; Total Investment (USD)</td>
                                                        <td colspan="3">
                                                            {{$appInfo->total_working_capital}}
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="panel-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <label class="col-md-12 text-left">7. Source of Finance</label>
                                        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                            <tbody id="annual_production_capacity">
                                            <tr>
                                                <td>
                                                    Local Equity (Taka)
                                                </td>
                                                <td>
                                                    {{$appInfo->finance_src_loc_equity_1}}
                                                </td>
                                                <td>
                                                    Local Equity (%)
                                                </td>
                                                <td>
                                                    {{$appInfo->finance_src_loc_equity_2}}
                                                </td>

                                            </tr>
                                            <tr>
                                                <td>
                                                    Foreign Equity (Taka)
                                                </td>
                                                <td>
                                                    {{$appInfo->finance_src_foreign_equity_1}}
                                                </td>
                                                <td>
                                                    Foreign Equity (%)
                                                </td>
                                                <td>
                                                    {{$appInfo->finance_src_foreign_equity_2}}
                                                </td>

                                            </tr>
                                            <tr>
                                                <th>
                                                    &nbsp;&nbsp;&nbsp;&nbsp; (a) Total Equity
                                                </th>
                                                <td colspan="3">
                                                    {{$appInfo->finance_src_loc_total_equity_1}}
                                                </td>

                                            </tr>

                                            <tr>
                                                <td>
                                                    Local Loan (Taka)
                                                </td>
                                                <td>
                                                    {{$appInfo->finance_src_loc_loan_1}}
                                                </td>
                                                <td rowspan="2"
                                                    style="vertical-align: middle;text-align: center; font-weight: bold;">
                                                    (b) Total Loan<br/>
                                                    (Taka)
                                                </td>
                                                <td rowspan="2" style="vertical-align: middle;text-align: center;">
                                                    {{$appInfo->finance_src_total_loan}}
                                                </td>

                                            </tr>
                                            <tr>
                                                <td>
                                                    Foreign Loan (Taka)
                                                </td>
                                                <td>
                                                    {{$appInfo->finance_src_foreign_loan_1}}
                                                </td>

                                            </tr>

                                            <tr>
                                                <th>
                                                    &nbsp;&nbsp;&nbsp;&nbsp; Total Financing Million (a+b)
                                                </th>
                                                <td colspan="3">
                                                    {{$appInfo->finance_src_loc_total_financing_m}}
                                                </td>

                                            </tr>

                                            <tr>
                                                <th>
                                                    &nbsp;&nbsp;&nbsp;&nbsp; Total Financing BDT (a+b)
                                                </th>
                                                <td colspan="3">
                                                    {{$appInfo->finance_src_loc_total_financing_1}}
                                                </td>

                                            </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="col-md-12 text-left">8. Public Utility Service Required</label><br/>
                                    <div class="col-md-12">
                                        <label class="checkbox-inline">
                                            @if($appInfo->public_land == 1) <img src="assets/images/checked.png"
                                                                                 width="10" height="10"/> Land @endif
                                        </label>
                                        <label class="checkbox-inline">
                                            @if($appInfo->public_electricity == 1) <img src="assets/images/checked.png"
                                                                                        width="10" height="10"/>
                                            Electricity @endif
                                        </label>
                                        <label class="checkbox-inline">
                                            @if($appInfo->public_gas == 1) <img src="assets/images/checked.png"
                                                                                width="10" height="10"/> Gas @endif
                                        </label>
                                        <label class="checkbox-inline">
                                            @if($appInfo->public_telephone == 1) <img src="assets/images/checked.png"
                                                                                      width="10" height="10"/>
                                            Telephone @endif
                                        </label>
                                        <label class="checkbox-inline">
                                            @if($appInfo->public_road == 1) <img src="assets/images/checked.png"
                                                                                 width="10" height="10"/> Road @endif
                                        </label>
                                        <label class="checkbox-inline">
                                            @if($appInfo->public_water == 1) <img src="assets/images/checked.png"
                                                                                  width="10" height="10"/> Water @endif
                                        </label>
                                        <label class="checkbox-inline">
                                            @if($appInfo->public_drainage == 1) <img src="assets/images/checked.png"
                                                                                     width="10" height="10"/>
                                            Drainage @endif
                                        </label>
                                        <label class="checkbox-inline">
                                            @if($appInfo->public_others == 1) <img src="assets/images/checked.png"
                                                                                   width="10" height="10"/>Others @endif
                                        </label>
                                        {{--<label class="checkbox-inline">--}}
                                        {{--{!! Form::text('other_utility_txt', '', ['data-rule-maxlength'=>'40','class' => 'other_utility_txt form-control input-md hide','id'=>'other_utility_txt']) !!}--}}
                                        {{--{!! $errors->first('other_utility_txt','<span class="help-block">:message</span>') !!}--}}
                                        {{--</label>--}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-info">
                        <div class="panel-heading">9. Trade Licence Details</div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <table width="100%" cellpadding="10">
                                    <tbody>
                                    <tr>
                                        <td width="50%" style="padding: 5px;">
                                            Trade Licence Number :
                                            <span> {{ (!empty($appInfo->trade_licence_num)) ? $appInfo->trade_licence_num :''  }}</span>
                                        </td>
                                        <td width="50%" style="padding: 5px;">
                                            Issuing Authority :
                                            <span> {{ (!empty($appInfo->trade_licence_issuing_authority)) ? $appInfo->trade_licence_issuing_authority:''  }}</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <br/>
                        </div>
                    </div>

                    <div class="panel panel-info">
                        <div class="panel-heading">10. Tin</div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <table width="100%" cellpadding="10">
                                    <tbody>
                                    <tr>
                                        <td width="50%" style="padding: 5px;">
                                            Tin Number :
                                            <span> {{ (!empty($appInfo->tin_number)) ? $appInfo->tin_number  :''  }}</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <br/>
                        </div>
                    </div>

                    @if($appInfo->organization_status_id == 3)
                        <div class="panel panel-info">
                            <div class="panel-heading">11. Description of Machinery and Equipment</div>
                            <div class="panel-body">
                                <div class="col-md-12">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <td></td>
                                            <td>Quantity</td>
                                            <td>Price (BDT)</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Locally Collected</td>
                                            <td>
                                                <span> {{ (!empty($appInfo->machinery_local_qty)) ? $appInfo->machinery_local_qty  :''  }}</span>
                                            </td>
                                            <td>
                                                <span> {{ (!empty($appInfo->machinery_local_price_bdt)) ? $appInfo->machinery_local_price_bdt  :''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Imported</td>
                                            <td>
                                                <span> {{ (!empty($appInfo->imported_qty)) ? $appInfo->imported_qty  :''  }}</span>
                                            </td>
                                            <td>
                                                <span> {{ (!empty($appInfo->imported_qty_price_bdt)) ? $appInfo->imported_qty_price_bdt  :''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Total</td>
                                            <td>
                                                <span> {{ (!empty($appInfo->total_machinery_qty)) ? $appInfo->total_machinery_qty  :''  }}</span>
                                            </td>
                                            <td>
                                                <span> {{ (!empty($appInfo->total_machinery_price)) ? $appInfo->total_machinery_price  :''  }}</span>
                                            </td>
                                        </tr>
                                        </tbody>

                                    </table>
                                </div>
                                <br/>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">12. Description of raw & packing materials</div>
                            <div class="panel-body">
                                <div class="col-md-12">
                                    <table class="table table-bordered dt-responsive">
                                        <tbody>
                                        <tr>
                                            <td width="20%">Locally</td>
                                            <td>
                                                <span> {{ (!empty($appInfo->local_description)) ? $appInfo->local_description  :''  }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="20%">Imported</td>
                                            <td>
                                                <span> {{ (!empty($appInfo->imported_description)) ? $appInfo->imported_description  :''  }}</span>
                                            </td>
                                        </tr>
                                        </tbody>

                                    </table>
                                </div>
                                <br/>
                            </div>
                        </div>
                    @endif

                    <div class="panel panel-info">
                        <div class="panel-heading">List of Directors and high authorities</div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <table class="table table-responsive">
                                    <tbody>
                                    <tr>
                                        <td colspan="2">Information of (Chairman/ Managing Director/ Or Equivalent)</td>
                                    </tr>
                                    <tr>
                                        <td>Full Name :
                                            <span> {{ (!empty($appInfo->g_full_name)) ? $appInfo->g_full_name :''  }}</span>
                                        </td>
                                        <td>
                                            Position/ Designation :
                                            <span> {{ (!empty($appInfo->g_designation)) ? $appInfo->g_designation  :''  }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" width="60%">Signature:
                                            @if(!empty($appInfo->g_signature) && file_exists("uploads/".$appInfo->g_signature))
                                                <img src="uploads/{{ $appInfo->g_signature }}"
                                                     class="signature-user-img img-responsive img-rounded user_signature"
                                                     alt="User Signature" id="user_signature" width="200"/>
                                            @else
                                                <img class="img-thumbnail" width="120" height="auto"
                                                     src="assets/images/photo_default.png" alt="Image not found"/>
                                            @endif
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <br>
                                <label>List of directors</label>
                                <table class="table table-bordered dt-responsive">
                                    <thead>
                                    <tr>
                                        <td>SL No</td>
                                        <td>Name</td>
                                        <td>Designation</td>
                                        <td>Nationality</td>
                                        <td>NID/ Passport No.</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 1; ?>
                                    @foreach($listOfDirector as $listOfDirector)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $listOfDirector->l_director_name }}</td>
                                            <td>{{ $listOfDirector->l_director_designation }}</td>
                                            <td>{{ (!empty($listOfDirector->l_director_nationality) ? $nationality[$listOfDirector->l_director_nationality] : '' ) }}</td>
                                            <td>{{ $listOfDirector->nid_etin_passport }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <br/>
                        </div>
                    </div>

                    <div class="panel panel-info">
                        <div class="panel-heading">List of Machineries</div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <label>List of machinery to be imported</label>
                                <table class="table table-bordered dt-responsive">
                                    <thead>
                                    <tr>
                                        <td>SL No</td>
                                        <td>Name of machineries</td>
                                        <td>Quantity</td>
                                        <td>Unit prices TK</td>
                                        <td>Total value (Million) TK</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $j = 1; ?>
                                    @foreach($listOfMechineryImported as $imported)
                                        <tr>
                                            <td>{{ $j++ }}</td>
                                            <td>{{ $imported->l_machinery_imported_name }}</td>
                                            <td>{{ $imported->l_machinery_imported_qty }}</td>
                                            <td>{{ $imported->l_machinery_imported_unit_price }}</td>
                                            <td>{{ $imported->l_machinery_imported_total_value }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tr>
                                        <td colspan="4">Total</td>
                                        <td>{{ CommonFunction::convertToMillionAmount($machineryImportedTotal) }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <label>List of machinery locally purchase/ procure</label>
                                <table class="table table-bordered dt-responsive">
                                    <thead>
                                    <tr>
                                        <td>SL No</td>
                                        <td>Name of machineries</td>
                                        <td>Quantity</td>
                                        <td>Unit prices TK</td>
                                        <td>Total value (Million) TK</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $k = 1; ?>
                                    @foreach($listOfMechineryLocal as $local)
                                        <tr>
                                            <td>{{ $k++ }}</td>
                                            <td>{{ $local->l_machinery_local_name }}</td>
                                            <td>{{ $local->l_machinery_local_qty }}</td>
                                            <td>{{ $local->l_machinery_local_unit_price }}</td>
                                            <td>{{ $local->l_machinery_local_total_value }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tr>
                                        <td colspan="4">Total</td>
                                        <td>{{ CommonFunction::convertToMillionAmount($machineryLocalTotal) }}</td>
                                    </tr>
                                </table>
                            </div>
                            <br/>
                        </div>
                    </div>


                    <div id="ep_form" class="panel panel-info">
                        <div class="panel-heading">Necessary documents to be attached here (Only PDF file)</div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <table class="table table-striped table-bordered table-hover ">
                                    <thead>
                                    <tr>
                                        <th style="padding: 5px;">No.</th>
                                        <th colspan="6" style="padding: 5px;">Required attachments</th>
                                        <th colspan="2" style="padding: 5px;">Attached PDF file (Each File Maximum size
                                            2MB)
                                            {{--<span>--}}
                                            {{--<i title="Attached PDF file (Each File Maximum size 2MB)!" data-toggle="tooltip" data-placement="right" class="fa fa-question-circle" aria-hidden="true"></i>--}}
                                            {{--</span>--}}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 1; ?>
                                    @foreach($document as $row)
                                        <tr>
                                            <td style="padding: 5px;">
                                                <div align="left">{!! $i !!}<?php echo $row->doc_priority == "1" ? "<span class='required-star'></span>" : ""; ?></div>
                                            </td>
                                            <td colspan="6" style="padding: 5px;">{!!  $row->doc_name !!}</td>
                                            <td colspan="2" style="padding: 5px;">
                                                @if(!empty($row->doc_file_path))

                                                    <div class="save_file">
                                                        <a target="_blank" title=""
                                                           href="{{URL::to('/uploads/'.(!empty($row->doc_file_path) ?
                                                           $row->doc_file_path : ''))}}"> <img width="10" height="10"
                                                                                               src="assets/images/pdf.png"
                                                                                               alt="pdf"/> Open File
                                                        </a>
                                                    </div>
                                                @else
                                                    No file found
                                                @endif
                                            </td>
                                        </tr>
                                        <?php $i++; ?>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-info">
                        <div class="panel-heading">Payment Info</div>
                        <div class="panel-body" style="padding: 5px">
                            <div class="panel panel-default">
                                <div class="panel-heading" style="padding: 2px 5px;">Service & Government Fee Payment
                                </div>
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        <div class="row" style="padding: 5px">
                                            <table class="table table-striped table-bordered">
                                                <tr>
                                                    <td>Contact name :
                                                        <span>{{ (!empty($appInfo->sfp_contact_name)) ? $appInfo->sfp_contact_name :''  }}</span>
                                                    </td>
                                                    <td>Contact email :
                                                        <span>{{ (!empty($appInfo->sfp_contact_email)) ? $appInfo->sfp_contact_email :''  }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Contact phone :
                                                        <span>{{ (!empty($appInfo->sfp_contact_phone)) ? $appInfo->sfp_contact_phone :''  }}</span>
                                                    </td>
                                                    <td>Contact address :
                                                        <span>{{ (!empty($appInfo->sfp_contact_address)) ? $appInfo->sfp_contact_address :''  }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Pay amount :
                                                        <span>{{ (!empty($appInfo->sfp_pay_amount)) ? $appInfo->sfp_pay_amount :''  }}</span>
                                                    </td>
                                                    <td>VAT on pay amount :
                                                        <span>{{ (!empty($appInfo->sfp_vat_on_pay_amount)) ? $appInfo->sfp_vat_on_pay_amount :''  }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Transaction charge :
                                                        <span>{{ (!empty($appInfo->sfp_transaction_charge_amount)) ? $appInfo->sfp_transaction_charge_amount :''  }}</span>
                                                    </td>
                                                    <td>
                                                        VAT on transaction charge:
                                                        <span>{{ (!empty($appInfo->sfp_vat_on_transaction_charge)) ? $appInfo->sfp_vat_on_transaction_charge :''  }}</span>
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td>Total Amount :
                                                        <span>{{ (!empty($appInfo->sfp_total_amount)) ? $appInfo->sfp_total_amount : ''  }}</span>
                                                    </td>
                                                    <td>Payment Status :
                                                        @if($appInfo->sfp_payment_status == 0)
                                                            <span class="label label-warning">Pending</span>
                                                        @elseif($appInfo->sfp_payment_status == -1)
                                                            <span class="label label-info">In-Progress</span>
                                                        @elseif($appInfo->sfp_payment_status == 1)
                                                            <span class="label label-success">Paid</span>
                                                        @elseif($appInfo->sfp_payment_status == 2)
                                                            <span class="label label-danger">-Exception</span>
                                                        @elseif($appInfo->sfp_payment_status == 3)
                                                            <span class="label label-warning">Waiting for Payment Confirmation</span>
                                                        @else
                                                            <span class="label label-warning">invalid status</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">Payment Mode :
                                                        <span>{{ (!empty($appInfo->sfp_pay_mode_code)) ? $appInfo->sfp_pay_mode_code :''  }}</span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Information about Declaration and undertaking --}}
                    <div id="ep_form" class="panel panel-info">
                        <div class="panel-heading">Declaration and undertaking</div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <table width="100%" cellpadding="10">
                                    <tr>
                                        <td width="" style="padding: 5px;">
                                            <p>a. I do hereby declare that the information given above is true to
                                                the best of my knowledge and I shall be liable for any false
                                                information/ statement given</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 5px;">
                                            <p>b. I do hereby undertake full responsibility of the expatriate for
                                                whom visa recommendation is sought during their stay in
                                                Bangladesh. </p>
                                        </td>
                                    </tr>
                                </table>
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
