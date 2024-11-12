<?php
// Get application basic company information
$basic_company_info = CommonFunction::getBasicInformationByProcessRefId($appInfo->process_type_id, $appInfo->id);
?>

<div class="panel panel-info">
    <div class="panel-heading">
        <strong>Basic Company Information (Non editable info. pulled from the basic information provided at the first
            time by your company)</strong>
    </div>
    <div class="panel-body">
        <fieldset class="scheduler-border" style="margin: 5px !important;">
            <legend class="scheduler-border">Company Information</legend>
            <div class="row">
                <div class="col-md-5 col-xs-6">
                    <span class="v_label">Department</span>
                    <span class="pull-right">&#58;</span>
                </div>
                <div class="col-md-7 col-xs-6">
                    {{ (!empty($basic_company_info->department)) ? $basic_company_info->department : '' }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-5 col-xs-6">
                    <span class="v_label">Name of Organization in English (Proposed)</span>
                    <span class="pull-right">&#58;</span>
                </div>
                <div class="col-md-7 col-xs-6">
                    {{ (!empty($basic_company_info->company_name)) ? $basic_company_info->company_name : '' }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-5 col-xs-6">
                    <span class="v_label">Name of Organization in Bangla (Proposed)</span>
                    <span class="pull-right">&#58;</span>
                </div>
                <div class="col-md-7 col-xs-6">
                    {{ (!empty($basic_company_info->company_name_bn)) ? $basic_company_info->company_name_bn : '' }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-5 col-xs-6">
                    <span class="v_label">Desired Service from BIDA</span>
                    <span class="pull-right">&#58;</span>
                </div>
                <div class="col-md-7 col-xs-6">
                    {{ (!empty($basic_company_info->service_name)) ? $basic_company_info->service_name : '' }}
                </div>
            </div>

            @if($basic_company_info->service_type == 5)
                <div class="row">
                    <div class="col-md-5 col-xs-6">
                        <span class="v_label">Commercial office type</span>
                        <span class="pull-right">&#58;</span>
                    </div>
                    <div class="col-md-7 col-xs-6">
                        {{ (!empty($basic_company_info->reg_commercial_office_name)) ? $basic_company_info->reg_commercial_office_name : '' }}
                    </div>
                </div>
            @endif

            @if($basic_company_info->business_category === 1)
            <div class="row">
                <div class="col-md-5 col-xs-6">
                    <span class="v_label">Ownership status</span>
                    <span class="pull-right">&#58;</span>
                </div>
                <div class="col-md-7 col-xs-6">
                    {{--                {{ (!empty($basic_company_info->ownership_status_id == 0)) ? $basic_company_info->ownership_status_other : $basic_company_info->ownership_status_name }}--}}
                    {{ (!empty($basic_company_info->ea_ownership_status)) ? $basic_company_info->ea_ownership_status : '' }}
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-md-5 col-xs-6">
                    <span class="v_label">Type of the organization</span>
                    <span class="pull-right">&#58;</span>
                </div>
                <div class="col-md-7 col-xs-6">
                    {{--                {{ (!empty($basic_company_info->organization_type_id == 0)) ? $basic_company_info->organization_type_other : $basic_company_info->organization_type_name }}--}}
                    {{ (!empty($basic_company_info->ea_organization_type)) ? $basic_company_info->ea_organization_type : '' }}
                    @if($basic_company_info->ea_organization_type_id ==14 && $basic_company_info->business_category == 2)
                        ({{ (!empty($basic_company_info->organization_type_other)) ? $basic_company_info->organization_type_other : '' }})
                    @endif

                </div>
            </div>



            <div class="row">
                <div class="col-md-5 col-xs-6">
                    <span class="v_label">Major activities in brief</span>
                    <span class="pull-right">&#58;</span>
                </div>
                <div class="col-md-7 col-xs-6">
                    {{ (!empty($basic_company_info->major_activities)) ? $basic_company_info->major_activities : '' }}
                </div>
            </div>

            <div class="row">
                <div class="col-md-5 col-xs-6">
                    <span class="v_label">Organization type</span>
                    <span class="pull-right">&#58;</span>
                </div>
                <div class="col-md-7 col-xs-6">
                    @if($basic_company_info->business_category === 2)
                        Government
                    @else
                        Private
                    @endif

                </div>
            </div>

            {{--        <div class="row">--}}
            {{--            <div class="col-md-5 col-xs-6">--}}
            {{--                <span class="v_label">Country of origin</span>--}}
            {{--                <span class="pull-right">&#58;</span>--}}
            {{--            </div>--}}
            {{--            <div class="col-md-7 col-xs-6">--}}
            {{--                {{ (!empty($basic_company_info->nicename)) ? $basic_company_info->nicename : '' }}--}}
            {{--            </div>--}}
            {{--        </div>--}}


            {{--        @if(!empty($basic_company_info->business_sector_id) && !empty($basic_company_info->business_sub_sector_id))--}}
            {{--            <div class="row">--}}
            {{--                <div class="col-md-6">--}}
            {{--                    <div class="row">--}}
            {{--                        <div class="col-md-5 col-xs-6">--}}
            {{--                            <span class="v_label">Business sector</span>--}}
            {{--                            <span class="pull-right">&#58;</span>--}}
            {{--                        </div>--}}
            {{--                        <div class="col-md-7 col-xs-6">--}}
            {{--                            {{ (!empty($basic_company_info->business_sector_id == 0)) ? $basic_company_info->business_sector_others : $basic_company_info->sector_name }}--}}
            {{--                        </div>--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            {{--                <div class="col-md-6">--}}
            {{--                    <div class="row">--}}
            {{--                        <div class="col-md-5 col-xs-6">--}}
            {{--                            <span class="v_label">Sub sector</span>--}}
            {{--                            <span class="pull-right">&#58;</span>--}}
            {{--                        </div>--}}
            {{--                        <div class="col-md-7 col-xs-6">--}}
            {{--                            {{ (!empty($basic_company_info->business_sub_sector_id == 0)) ? $basic_company_info->business_sub_sector_others : $basic_company_info->sub_sector_name  }}--}}
            {{--                        </div>--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            {{--            </div>--}}
            {{--        @endif--}}
        </fieldset>

        <div class="text-center panel-title">
            <a data-toggle="collapse" href="#basicCompanyInfo" role="button"
               aria-expanded="false" aria-controls="collapseExample" style="font-size: 16px; font-weight: 600; color: #337ab7">Click
                here to see details of the company information
            </a>
        </div>
        <div class="text-center">
            <i class="fa fa-angle-down" aria-hidden="true"></i>
        </div>

        <div id="basicCompanyInfo" class="collapse">
            {{-- Start business category --}}
            @if($basic_company_info->business_category == 2)
                {{--Information of Responsible Person--}}
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border">Information of Responsible Person</legend>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Country</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->ceo_country)) ? $basic_company_info->ceo_country : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Full Name</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->ceo_full_name)) ? $basic_company_info->ceo_full_name : '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @if($basic_company_info->ceo_country_id == 18)
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">NID No.</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($basic_company_info->ceo_nid)) ? $basic_company_info->ceo_nid : '' }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Passport No.</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($basic_company_info->ceo_passport_no)) ? $basic_company_info->ceo_passport_no : '' }}
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Designation</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->ceo_designation)) ? $basic_company_info->ceo_designation : '' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Mobile No.</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->ceo_mobile_no)) ? $basic_company_info->ceo_mobile_no : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Email</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->ceo_email)) ? $basic_company_info->ceo_email : '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Gender</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->ceo_gender)) ? $basic_company_info->ceo_gender : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Authorization Letter</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    <a target="_blank" class="btn btn-xs btn-primary" title="Authorization Letter"
                                       href="{{ URL::to('users/upload/'.$basic_company_info->ceo_auth_letter) }}">
                                        <i class="fa fa-file-pdf" aria-hidden="true"></i>
                                        Authorization Letter
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            @else
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border">Information of Principal Promoter/ Chairman/ Managing Director/ State CEO</legend>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Country</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->ceo_country)) ? $basic_company_info->ceo_country : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Date of Birth</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ !empty($basic_company_info->ceo_dob) ? date('d-M-Y', strtotime($basic_company_info->ceo_dob)) : '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @if($basic_company_info->ceo_country_id == 18)
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">NID No.</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($basic_company_info->ceo_nid)) ? $basic_company_info->ceo_nid : '' }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Passport No.</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($basic_company_info->ceo_passport_no)) ? $basic_company_info->ceo_passport_no : '' }}
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Designation</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->ceo_designation)) ? $basic_company_info->ceo_designation : '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Full Name</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->ceo_full_name)) ? $basic_company_info->ceo_full_name : '' }}
                                </div>
                            </div>
                        </div>
                        @if($basic_company_info->ceo_country_id == 18)
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">District/ City/ State</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($basic_company_info->ceo_district_name)) ? $basic_company_info->ceo_district_name : '' }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">District/ City/ State</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($basic_company_info->ceo_city)) ? $basic_company_info->ceo_city : '' }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        @if($basic_company_info->ceo_country_id == 18)
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Police Station/ Town</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($basic_company_info->ceo_thana_name)) ? $basic_company_info->ceo_thana_name : '' }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">State/ Province</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($basic_company_info->ceo_state)) ? $basic_company_info->ceo_state : '' }}
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Post/ Zip Code</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->ceo_post_code)) ? $basic_company_info->ceo_post_code : '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">House, Flat/ Apartment, Road</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->ceo_address)) ? $basic_company_info->ceo_address : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Telephone No.</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->ceo_telephone_no)) ? $basic_company_info->ceo_telephone_no : '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Mobile No.</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->ceo_mobile_no)) ? $basic_company_info->ceo_mobile_no : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Father's Name</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->ceo_father_name)) ? $basic_company_info->ceo_father_name : '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Email</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->ceo_email)) ? $basic_company_info->ceo_email : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Mother's Name</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->ceo_mother_name)) ? $basic_company_info->ceo_mother_name : '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Fax No.</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->ceo_fax_no)) ? $basic_company_info->ceo_fax_no : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Spouse name</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->ceo_spouse_name)) ? $basic_company_info->ceo_spouse_name : '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Gender</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->ceo_gender)) ? $basic_company_info->ceo_gender : '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            @endif

            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Office Address</legend>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">Division</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($basic_company_info->office_division_name)) ? $basic_company_info->office_division_name : '' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">District</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($basic_company_info->office_district_name)) ? $basic_company_info->office_district_name : '' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">Police Station</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($basic_company_info->office_thana_name)) ? $basic_company_info->office_thana_name : '' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">Post Office</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($basic_company_info->office_post_office)) ? $basic_company_info->office_post_office : '' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">Post Code</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($basic_company_info->office_post_code)) ? $basic_company_info->office_post_code : '' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">House, Flat/ Apartment, Road</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($basic_company_info->office_address)) ? $basic_company_info->office_address : '' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">Telephone No.</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($basic_company_info->office_telephone_no)) ? $basic_company_info->office_telephone_no : '' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">Mobile No.</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($basic_company_info->office_mobile_no)) ? $basic_company_info->office_mobile_no : '' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">Fax No.</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($basic_company_info->office_fax_no)) ? $basic_company_info->office_fax_no : '' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">Email</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($basic_company_info->office_email)) ? $basic_company_info->office_email : '' }}
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

            {{--2 = industrial department --}}
            @if($basic_company_info->business_category != 2 && $basic_company_info->department_id == 2)
                <fieldset class="scheduler-border" style="margin-bottom: 0 !important;">
                    <legend class="scheduler-border">Factory Address</legend>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">District</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->factory_district_name)) ? $basic_company_info->factory_district_name : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Police Station</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->factory_thana_name)) ? $basic_company_info->factory_thana_name : '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Post Office</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->factory_post_office)) ? $basic_company_info->factory_post_office : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Post Code</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->factory_post_code)) ? $basic_company_info->factory_post_code : '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">House, Flat/ Apartment, Road</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->factory_address)) ? $basic_company_info->factory_address : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Telephone No.</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->factory_telephone_no)) ? $basic_company_info->factory_telephone_no : '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Mobile No.</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->factory_mobile_no)) ? $basic_company_info->factory_mobile_no : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Fax No.</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->factory_fax_no)) ? $basic_company_info->factory_fax_no : '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Email</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->factory_email)) ? $basic_company_info->factory_email : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Mouja No.</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($basic_company_info->factory_mouja)) ? $basic_company_info->factory_mouja : '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            @endif
        </div>
    </div>
</div>

<script>
    $('#basicCompanyInfo').on('shown.bs.collapse', function () {
        $(".fa").removeClass("fa-angle-down").addClass("fa-angle-up");
    });

    $('#basicCompanyInfo').on('hidden.bs.collapse', function () {
        $(".fa").removeClass("fa-angle-up").addClass("fa-angle-down");
    });
</script>
