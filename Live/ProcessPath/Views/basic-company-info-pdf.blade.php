<?php
// Get application basic company information
$basic_company_info = CommonFunction::getBasicInformationByProcessRefId($appInfo->process_type_id, $appInfo->id);
?>
<div class="panel panel-info">
    <div class="panel-heading">Basic Company Information (Non editable info. pulled from the basic information provided at the first time by your company)</div>
    <div class="panel-body">
        <div class="col-md-12">
            <table width="100%" cellpadding="10">
                <tbody>
                <tr>
                    <td colspan="2" style="padding: 5px;">
                        <strong class="text-info">Company information: </strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 5px;">
                        Department :
                        <span>{{ (!empty($basic_company_info->department)) ? $basic_company_info->department : '' }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 5px;">
                        Name of Organization in English (Proposed) :
                        <span>{{ (!empty($basic_company_info->company_name)) ? $basic_company_info->company_name : '' }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 5px;">
                        Name of Organization in Bangla (Proposed) :
                        <span>{{ (!empty($basic_company_info->company_name_bn)) ? $basic_company_info->company_name_bn : '' }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 5px;">
                        Desired Service from BIDA :
                        <span>{{ (!empty($basic_company_info->service_name)) ? $basic_company_info->service_name : '' }}</span>
                    </td>
                </tr>
                @if($basic_company_info->service_type == 5)
                    <tr>
                        <td colspan="2" style="padding: 5px;">
                            Commercial office type :
                            <span>{{ (!empty($basic_company_info->reg_commercial_office_name)) ? $basic_company_info->reg_commercial_office_name : '' }}</span>
                        </td>
                    </tr>
                @endif
                <tr>
                    <td colspan="2" style="padding: 5px;">
                        Ownership status :
                        <span>{{ (!empty($basic_company_info->ea_ownership_status)) ? $basic_company_info->ea_ownership_status : '' }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 5px;">
                        Type of the organization :
                        <span>{{ (!empty($basic_company_info->ea_organization_type)) ? $basic_company_info->ea_organization_type : '' }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 5px;">
                        Major activities in brief :
                        <span>{{ (!empty($basic_company_info->major_activities)) ? $basic_company_info->major_activities : '' }}</span>
                    </td>
                </tr>


                {{-- Start business category --}}
                @if($basic_company_info->business_category == 2)
                    {{--Information of Responsible Person--}}
                    <tr>
                        <td colspan="2">
                            <strong class="text-info">Information of Responsible Person</strong>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding: 5px;">
                            Country :
                            <span>{{ (!empty($basic_company_info->ceo_country)) ? $basic_company_info->ceo_country : '' }}</span>
                        </td>
                        <td width="50%" style="padding: 5px;">
                            Full Name :
                            <span>{{ (!empty($basic_company_info->ceo_full_name)) ? $basic_company_info->ceo_full_name : '' }}</span>
                        </td>
                    </tr>
                    <tr>
                        @if($basic_company_info->ceo_country_id == 18)
                            <td width="50%" style="padding: 5px;">
                                NID No. :
                                <span>{{ (!empty($basic_company_info->ceo_nid)) ? $basic_company_info->ceo_nid : '' }}</span>
                            </td>
                        @else
                            <td width="50%" style="padding: 5px;">
                                Passport No. :
                                <span>{{ (!empty($basic_company_info->ceo_passport_no)) ? $basic_company_info->ceo_passport_no : '' }}</span>
                            </td>
                        @endif
                        <td width="50%" style="padding: 5px;">
                            Designation :
                            <span>{{ (!empty($basic_company_info->ceo_designation)) ? $basic_company_info->ceo_designation : '' }}</span>
                        </td>
                    </tr>

                    <tr>
                        <td width="50%" style="padding: 5px;">
                            Mobile No. :
                            <span>{{ (!empty($basic_company_info->ceo_mobile_no)) ? $basic_company_info->ceo_mobile_no : '' }}</span>
                        </td>
                        <td width="50%" style="padding: 5px;">
                            Email :
                            <span>{{ (!empty($basic_company_info->ceo_email)) ? $basic_company_info->ceo_email : '' }}</span>
                        </td>
                    </tr>

                    <tr>
                        <td width="50%" style="padding: 5px;">
                            Gender :
                            <span>{{ (!empty($basic_company_info->ceo_gender)) ? $basic_company_info->ceo_gender : '' }}</span>
                        </td>
                    </tr>
                @else
                    <tr>
                        <td colspan="2">
                            <strong class="text-info">Information of Principal Promoter/ Chairman/ Managing Director/ State CEO :</strong>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding: 5px;">
                            Country :
                            <span>{{ (!empty($basic_company_info->ceo_country)) ? $basic_company_info->ceo_country : '' }}</span>
                        </td>
                        <td width="50%" style="padding: 5px;">
                            Date of Birth :
                            <span>{{ (!empty($basic_company_info->ceo_dob)) ? date('d-M-Y', strtotime($basic_company_info->ceo_dob)) : '' }}</span>
                        </td>
                    </tr>
                    <tr>
                        @if($basic_company_info->ceo_country_id == 18)
                            <td width="50%" style="padding: 5px;">
                                NID No. :
                                <span>{{ (!empty($basic_company_info->ceo_nid)) ? $basic_company_info->ceo_nid : '' }}</span>
                            </td>
                        @else
                            <td width="50%" style="padding: 5px;">
                                Passport No. :
                                <span>{{ (!empty($basic_company_info->ceo_passport_no)) ? $basic_company_info->ceo_passport_no : '' }}</span>
                            </td>
                        @endif
                        <td width="50%" style="padding: 5px;">
                            Designation :
                            <span>{{ (!empty($basic_company_info->ceo_designation)) ? $basic_company_info->ceo_designation : '' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding: 5px;">
                            Full Name :
                            <span>{{ (!empty($basic_company_info->ceo_full_name)) ? $basic_company_info->ceo_full_name : '' }}</span>
                        </td>
                        @if($basic_company_info->ceo_country_id == 18)
                            <td width="50%" style="padding: 5px;">
                                District/ City/ State :
                                <span>{{ (!empty($basic_company_info->ceo_district_name)) ? $basic_company_info->ceo_district_name : '' }}</span>
                            </td>
                        @else
                            <td width="50%" style="padding: 5px;">
                                District/ City/ State :
                                <span>{{ (!empty($basic_company_info->ceo_city)) ? $basic_company_info->ceo_city : '' }}</span>
                            </td>
                        @endif
                    </tr>
                    <tr>
                        @if($basic_company_info->ceo_country_id == 18)
                            <td width="50%" style="padding: 5px;">
                                Police Station/ Town :
                                <span>{{ (!empty($basic_company_info->ceo_thana_name)) ? $basic_company_info->ceo_thana_name : '' }}</span>
                            </td>
                        @else
                            <td width="50%" style="padding: 5px;">
                                State/ Province :
                                <span>{{ (!empty($basic_company_info->ceo_state)) ? $basic_company_info->ceo_state : '' }}</span>
                            </td>
                        @endif
                        <td width="50%" style="padding: 5px;">
                            Post/ Zip Code :
                            <span>{{ (!empty($basic_company_info->ceo_post_code)) ? $basic_company_info->ceo_post_code : '' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding: 5px;">
                            House, Flat/ Apartment, Road :
                            <span>{{ (!empty($basic_company_info->ceo_address)) ? $basic_company_info->ceo_address : '' }}</span>
                        </td>
                        <td width="50%" style="padding: 5px;">
                            Telephone No. :
                            <span>{{ (!empty($basic_company_info->ceo_telephone_no)) ? $basic_company_info->ceo_telephone_no : '' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding: 5px;">
                            Mobile No. :
                            <span>{{ (!empty($basic_company_info->ceo_mobile_no)) ? $basic_company_info->ceo_mobile_no : '' }}</span>
                        </td>
                        <td width="50%" style="padding: 5px;">
                            Father's Name :
                            <span>{{ (!empty($basic_company_info->ceo_father_name)) ? $basic_company_info->ceo_father_name : '' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding: 5px;">
                            Email :
                            <span>{{ (!empty($basic_company_info->ceo_email)) ? $basic_company_info->ceo_email : '' }}</span>
                        </td>
                        <td width="50%" style="padding: 5px;">
                            Mother's Name :
                            <span>{{ (!empty($basic_company_info->ceo_mother_name)) ? $basic_company_info->ceo_mother_name : '' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding: 5px;">
                            Fax No. :
                            <span>{{ (!empty($basic_company_info->ceo_fax_no)) ? $basic_company_info->ceo_fax_no : '' }}</span>
                        </td>
                        <td width="50%" style="padding: 5px;">
                            Spouse name :
                            <span>{{ (!empty($basic_company_info->ceo_spouse_name)) ? $basic_company_info->ceo_spouse_name : '' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding: 5px;">
                            Gender :
                            <span>{{ (!empty($basic_company_info->ceo_gender)) ? $basic_company_info->ceo_gender : '' }}</span>
                        </td>
                    </tr>

                @endif

                <tr>
                    <td colspan="2">
                        <strong class="text-info">Office Address :</strong>
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding: 5px;">
                        Division :
                        <span>{{ (!empty($basic_company_info->office_division_name)) ? $basic_company_info->office_division_name : '' }}</span>
                    </td>
                    <td width="50%" style="padding: 5px;">
                        District :
                        <span>{{ (!empty($basic_company_info->office_district_name)) ? $basic_company_info->office_district_name : '' }}</span>
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding: 5px;">
                        Police Station :
                        <span>{{ (!empty($basic_company_info->office_thana_name)) ? $basic_company_info->office_thana_name : '' }}</span>
                    </td>
                    <td width="50%" style="padding: 5px;">
                        Post Office :
                        <span>{{ (!empty($basic_company_info->office_post_office)) ? $basic_company_info->office_post_office : '' }}</span>
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding: 5px;">
                        Post Code :
                        <span>{{ (!empty($basic_company_info->office_post_code)) ? $basic_company_info->office_post_code : '' }}</span>
                    </td>
                    <td width="50%" style="padding: 5px;">
                        House, Flat/ Apartment, Road :
                        <span>{{ (!empty($basic_company_info->office_address)) ? $basic_company_info->office_address : '' }}</span>
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding: 5px;">
                        Telephone No. :
                        <span>{{ (!empty($basic_company_info->office_telephone_no)) ? $basic_company_info->office_telephone_no : '' }}</span>
                    </td>
                    <td width="50%" style="padding: 5px;">
                        Mobile No. :
                        <span>{{ (!empty($basic_company_info->office_mobile_no)) ? $basic_company_info->office_mobile_no : '' }}</span>
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding: 5px;">
                        Fax No. :
                        <span>{{ (!empty($basic_company_info->office_fax_no)) ? $basic_company_info->office_fax_no : '' }}</span>
                    </td>
                    <td width="50%" style="padding: 5px;">
                        Email :
                        <span>{{ (!empty($basic_company_info->office_email)) ? $basic_company_info->office_email : '' }}</span>
                    </td>
                </tr>

                {{--2 = industrial department --}}
                @if($basic_company_info->business_category != 2 && $basic_company_info->department_id == 2)
                    <tr>
                        <td colspan="2">
                            <strong class="text-info">Factory Address :</strong>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding: 5px;">
                            District :
                            <span>{{ (!empty($basic_company_info->factory_district_name)) ? $basic_company_info->factory_district_name : '' }}</span>
                        </td>
                        <td width="50%" style="padding: 5px;">
                            Police Station :
                            <span>{{ (!empty($basic_company_info->factory_thana_name)) ? $basic_company_info->factory_thana_name : '' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding: 5px;">
                            Post Office :
                            <span>{{ (!empty($basic_company_info->factory_post_office)) ? $basic_company_info->factory_post_office : '' }}</span>
                        </td>
                        <td width="50%" style="padding: 5px;">
                            Post Code :
                            <span>{{ (!empty($basic_company_info->factory_post_office)) ? $basic_company_info->factory_post_office : '' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding: 5px;">
                            House, Flat/ Apartment, Road :
                            <span>{{ (!empty($basic_company_info->factory_address)) ? $basic_company_info->factory_address : '' }}</span>
                        </td>
                        <td width="50%" style="padding: 5px;">
                            Telephone No. :
                            <span>{{ (!empty($basic_company_info->factory_telephone_no)) ? $basic_company_info->factory_telephone_no : '' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding: 5px;">
                            Mobile No. :
                            <span>{{ (!empty($basic_company_info->factory_mobile_no)) ? $basic_company_info->factory_mobile_no : '' }}</span>
                        </td>
                        <td width="50%" style="padding: 5px;">
                            Fax No. :
                            <span>{{ (!empty($basic_company_info->factory_fax_no)) ? $basic_company_info->factory_fax_no : '' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding: 5px;">
                            Email :
                            <span>{{ (!empty($basic_company_info->factory_email)) ? $basic_company_info->factory_email : '' }}</span>
                        </td>
                        <td width="50%" style="padding: 5px;">
                            Mouja No. :
                            <span>{{ (!empty($basic_company_info->factory_mouja)) ? $basic_company_info->factory_mouja : '' }}</span>
                        </td>
                    </tr>
                @endif

                </tbody>
            </table>
        </div>
    </div>
</div>