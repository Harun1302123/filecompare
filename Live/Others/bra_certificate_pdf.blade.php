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
                    <td width="75%" style="padding: 0">
                        <strong>Ref No: </strong> {{ !empty($appInfo->tracking_no) ? $appInfo->tracking_no : '' }}
                    </td>
                    <td width="25%" style="padding: 0; text-align: right">
                        <strong>Date:</strong> {{ date('F j, Y', strtotime($appInfo->approved_date)) }}
                    </td>
                </tr>
                {{-- <tr>
                    <td style="padding: 0">
                        <strong>Company Name:</strong> {{ !empty($appInfo->company_name) ? $appInfo->company_name : '' }}
                       
                    </td>
                </tr> --}}
                <tr>
                    <td style="padding: 0">
                        <strong>Company Name:</strong> {{ !empty($appInfo->company_name) ? $appInfo->company_name : '' }}   
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0">
                        <strong>Project Name:</strong> {{ empty($appInfo->n_project_name) ? $appInfo->project_name : $appInfo->n_project_name }}   
                    </td>
                </tr>
                
                <tr>
                    <?php
                    $tracking_no = "";

                    if ($appInfo->is_approval_online == "yes") {
                        $tracking_no = $appInfo->ref_app_tracking_no;
                    }
                    if($appInfo->is_approval_online == "no") {
                        $tracking_no = $appInfo->manually_approved_br_no;
                    }
                    ?>

                    <td style="padding: 10px 0 0 0"><strong>Subject:</strong> Amendment of the registration ({{ $tracking_no }})</td>
                </tr>
                </tbody>
            </table>


            <p>
                Dear Sir/Madam,<br>
                With Reference to his letter dated no ({{ date('d-m-Y', strtotime($appInfo->submitted_at)) }}) on the above subject. I am directed to inform him
                that the Bangladesh Investment Development Authority (BIDA) has been pleased to amend registration
                of his project in the following manner: -
            </p>


            {{-- Company information --}}
            @if (!empty($appInfo->n_company_name) || !empty($appInfo->n_company_name_bn) || !empty($appInfo->n_project_name) || !empty($appInfo->n_organization_type_name) || !empty($appInfo->n_organization_status_name) ||
            !empty($appInfo->n_ownership_status_name) || !empty($appInfo->n_country_of_origin_name))
                <table class="table table-bordered" width="100%" cellpadding="10">
                    <thead>
                    <tr>
                        <td colspan="4"><strong>Company Information</strong></td>
                    </tr>
                    <tr>
                        <td width="30%" style="font-weight: bold;text-align: center;">Field Name</td>
                        <td width="35%" style="font-weight: bold;text-align: center;">Existing Information</td>
                        <td width="35%" style="font-weight: bold;text-align: center;">Amended Information</td>
                    </tr>
                    </thead>
                    <tbody>
                    @if (!empty($appInfo->n_company_name))
                        <tr>
                            <td>Name of organization/ company</td>
                            <td>{{ (!empty($appInfo->company_name)) ? $appInfo->company_name : ''  }}</td>
                            <td>{{ (!empty($appInfo->n_company_name)) ? $appInfo->n_company_name : ''  }}</td>
                        </tr>
                    @endif
                    @if (!empty($appInfo->n_company_name_bn))
                        <tr>
                            <td>Name of organization/ company (বাংলা)</td>
                            <td>{{ (!empty($appInfo->company_name_bn)) ? $appInfo->company_name_bn : ''  }}</td>
                            <td>{{ (!empty($appInfo->n_company_name_bn)) ? $appInfo->n_company_name_bn : ''  }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_project_name))
                        <tr>
                            <td>Name of the project</td>
                            <td>{{ (!empty($appInfo->project_name)) ? $appInfo->project_name : ''  }}</td>
                            <td>{{ (!empty($appInfo->n_project_name)) ? $appInfo->n_project_name : ''  }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_organization_type_name))
                        <tr>
                            <td>Type of theorganization</td>
                            <td>{{ (!empty($appInfo->organization_type_name)) ? $appInfo->organization_type_name : ''  }}</td>
                            <td>{{ (!empty($appInfo->n_organization_type_name)) ? $appInfo->n_organization_type_name : ''  }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_organization_status_name))
                        <tr>
                            <td>Status of the organization</td>
                            <td>{{ (!empty($appInfo->organization_status_name)) ? $appInfo->organization_status_name : ''  }}</td>
                            <td>{{ (!empty($appInfo->n_organization_status_name)) ? $appInfo->n_organization_status_name : ''  }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_ownership_status_name))
                        <tr>
                            <td>Ownership status</td>
                            <td>{{ (!empty($appInfo->ownership_status_name)) ? $appInfo->ownership_status_name : ''  }}</td>
                            <td>{{ (!empty($appInfo->n_ownership_status_name)) ? $appInfo->n_ownership_status_name : ''  }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_country_of_origin_name))
                        <tr>
                            <td>Country of Origin</td>
                            <td>{{ (!empty($appInfo->country_of_origin_name)) ? $appInfo->country_of_origin_name : ''  }}</td>
                            <td>{{ (!empty($appInfo->n_country_of_origin_name)) ? $appInfo->n_country_of_origin_name : ''  }}</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            @endif

            {{-- Business sector --}}
            @if (!empty($appInfo->n_class_code) || !empty($n_sub_class->name))
                <table class="table table-bordered" width="100%" cellpadding="10">
                    <thead>
                    <tr>
                        <td colspan="5"><strong>Business Sector</strong></td>
                    </tr>
                    <tr>
                        <td width="30%" style="font-weight: bold;text-align: center;">Field Name</td>
                        <td width="35%" colspan="2" style="font-weight: bold;text-align: center;">Existing Information</td>
                        <td width="35%" colspan="2" style="font-weight: bold;text-align: center;">Amended Information</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td style="font-weight: bold;text-align: center;">Business Sector (BBS Class Code)</td>
                        <td colspan="2">{{ !empty($appInfo->class_code) ? $appInfo->class_code : '' }}</td>
                        <td colspan="2">{{ !empty($appInfo->n_class_code) ? $appInfo->n_class_code : '' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;text-align: center;">Category</td>
                        <td style="font-weight: bold;text-align: center;">Code</td>
                        <td style="font-weight: bold;text-align: center;">Description</td>
                        <td style="font-weight: bold;text-align: center;">Code</td>
                        <td style="font-weight: bold;text-align: center;">Description</td>
                    </tr>
                    <tr>
                        <td>Section</td>
                        <td>{{ !empty($busness_code) ? $busness_code[0]['section_code'] : ''}}</td>
                        <td>{{ !empty($busness_code) ? $busness_code[0]['section_name'] : ''}}</td>

                        <td>{{ !empty($n_busness_code) ? $n_busness_code[0]['section_code'] : ''}}</td>
                        <td>{{ !empty($n_busness_code) ? $n_busness_code[0]['section_name'] : ''}}</td>
                    </tr>
                    <tr>
                        <td>Division</td>
                        <td>{{ !empty($busness_code) ? $busness_code[0]['division_code'] : ''}}</td>
                        <td>{{ !empty($busness_code) ? $busness_code[0]['division_name'] : ''}}</td>

                        <td>{{ !empty($n_busness_code) ? $n_busness_code[0]['division_code'] : ''}}</td>
                        <td>{{ !empty($n_busness_code) ? $n_busness_code[0]['division_name'] : ''}}</td>
                    </tr>
                    <tr>
                        <td>Group</td>
                        <td>{{ !empty($busness_code) ? $busness_code[0]['group_code'] : ''}}</td>
                        <td>{{ !empty($busness_code) ? $busness_code[0]['group_name'] : ''}}</td>

                        <td>{{ !empty($n_busness_code) ? $n_busness_code[0]['group_code'] : ''}}</td>
                        <td>{{ !empty($n_busness_code) ? $n_busness_code[0]['group_name'] : ''}}</td>
                    </tr>
                    <tr>
                        <td>Class</td>
                        <td>{{ !empty($busness_code) ? $busness_code[0]['code'] : ''}}</td>
                        <td>{{ !empty($busness_code) ? $busness_code[0]['name'] : ''}}</td>
                        <td>{{ !empty($n_busness_code) ? $n_busness_code[0]['code'] : ''}}</td>
                        <td>{{ !empty($n_busness_code) ? $n_busness_code[0]['name'] : ''}}</td>
                    </tr>
                    <tr>
                        <td>Sub class</td>
                        <td colspan="2">{{ (!empty($sub_class->name)) ? $sub_class->name : 'Other' }}</td>
                        <td colspan="2">{{ (!empty($n_sub_class->name)) ? $n_sub_class->name : 'Other' }}</td>
                    </tr>

                    {{-- Other Sub Class --}}
                    @if ($appInfo->n_sub_class_id == 0)
                        @if(!empty($appInfo->n_other_sub_class_code))
                            <tr>
                                <td>Other sub class code</td>
                                <td colspan="2">{{ (!empty($appInfo->other_sub_class_code)) ? $appInfo->other_sub_class_code : '' }}</td>
                                <td colspan="2">{{ $appInfo->n_other_sub_class_code }}</td>
                            </tr>
                        @endif
                        @if(!empty($appInfo->n_other_sub_class_name))
                            <tr>
                                <td>Other sub class name</td>
                                <td colspan="2">{{ (!empty($appInfo->other_sub_class_name)) ? $appInfo->other_sub_class_name : '' }}</td>
                                <td colspan="2">{{ $appInfo->n_other_sub_class_name }}</td>
                            </tr>
                        @endif
                    @endif
                    </tbody>
                </table>
            @endif

            {{-- CEO information --}}
            @if (!empty($appInfo->n_ceo_country_name) || !empty($appInfo->n_ceo_dob) || !empty($appInfo->n_ceo_passport_no) || !empty($appInfo->n_ceo_nid) ||
            !empty($appInfo->n_ceo_designation) || !empty($appInfo->n_ceo_full_name) || !empty($appInfo->n_ceo_district_name) || !empty($appInfo->n_ceo_city) || !empty($appInfo->n_ceo_thana_name) ||
            !empty($appInfo->n_ceo_state) || !empty($appInfo->n_ceo_post_code) || !empty($appInfo->n_ceo_address) || !empty($appInfo->n_ceo_telephone_no) || !empty($appInfo->n_ceo_mobile_no) ||
            !empty($appInfo->n_ceo_email) || !empty($appInfo->n_ceo_fax_no) || !empty($appInfo->n_ceo_father_name) || !empty($appInfo->n_ceo_mother_name) || !empty($appInfo->n_ceo_spouse_name) ||
            (!empty($appInfo->n_ceo_gender) && $appInfo->n_ceo_gender != 'Not defined'))

                <table class="table table-bordered" width="100%" cellpadding="10">
                    <tr>
                        <td colspan="3"><strong>B. Information of Principal Promoter/ Chairman/ Managing Director/ CEO/ Country Manager</strong></td>
                    </tr>
                    <tr>
                        <td width="30%" style="font-weight: bold;text-align: center;">Field Name</td>
                        <td width="35%" style="font-weight: bold;text-align: center;">Existing Information</td>
                        <td width="35%" style="font-weight: bold;text-align: center;">Amended Information</td>
                    </tr>
                    @if (!empty($appInfo->n_ceo_country_name))
                        <tr>
                            <td>Country</td>
                            <td>{{ !empty($appInfo->ceo_country_name) ? $appInfo->ceo_country_name : '' }}</td>
                            <td>{{ !empty($appInfo->n_ceo_country_name) ? $appInfo->n_ceo_country_name : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_ceo_dob))
                        <tr>
                            <td>Date of Birth</td>
                            <td>{{ !empty($appInfo->ceo_dob) ? date("d-M-Y", strtotime($appInfo->ceo_dob)) : '' }}</td>
                            <td>{{ !empty($appInfo->n_ceo_dob) ? date("d-M-Y", strtotime($appInfo->n_ceo_dob)) : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_ceo_nid) || !empty($appInfo->n_ceo_passport_no))
                        <tr>
                            <td>NID/ Passport No.</td>
                            <td>
                                @if($appInfo->ceo_country_id == 18)
                                    {{ !empty($appInfo->ceo_nid) ? $appInfo->ceo_nid : '' }}
                                @else
                                    {{ !empty($appInfo->ceo_passport_no) ? $appInfo->ceo_passport_no : '' }}
                                @endif
                            </td>

                            <td>
                                @if($appInfo->n_ceo_country_id == 18)
                                    {{ !empty($appInfo->n_ceo_nid) ? $appInfo->n_ceo_nid : '' }}
                                @else
                                    {{ !empty($appInfo->n_ceo_passport_no) ? $appInfo->n_ceo_passport_no : '' }}
                                @endif
                            </td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_ceo_designation))
                        <tr>
                            <td>Designation</td>
                            <td>{{ !empty($appInfo->ceo_designation) ? $appInfo->ceo_designation : '' }}</td>
                            <td>{{ !empty($appInfo->n_ceo_designation) ? $appInfo->n_ceo_designation : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_ceo_full_name))
                        <tr>
                            <td>Full Name</td>
                            <td>{{ !empty($appInfo->ceo_full_name) ? $appInfo->ceo_full_name : '' }}</td>
                            <td>{{ !empty($appInfo->n_ceo_full_name) ? $appInfo->n_ceo_full_name : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_ceo_district_name) || !empty($appInfo->n_ceo_city))
                        <tr>
                            <td>District/ City/ State</td>
                            <td>
                                @if($appInfo->ceo_country_id == 18)
                                    {{ !empty($appInfo->ceo_district_name) ? $appInfo->ceo_district_name : '' }}
                                @else
                                    {{ !empty($appInfo->ceo_city) ? $appInfo->ceo_city : '' }}
                                @endif
                            </td>

                            <td>
                                @if($appInfo->n_ceo_country_id == 18)
                                    {{ !empty($appInfo->n_ceo_district_name) ? $appInfo->n_ceo_district_name : '' }}
                                @else
                                    {{ !empty($appInfo->n_ceo_city) ? $appInfo->n_ceo_city : '' }}
                                @endif
                            </td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_ceo_thana_name) || !empty($appInfo->n_ceo_state))
                        <tr>
                            <td>State/ Province/ Police station/ Town</td>
                            <td>
                                @if($appInfo->ceo_country_id == 18)
                                    {{ !empty($appInfo->ceo_thana_name) ? $appInfo->ceo_thana_name : '' }}
                                @else
                                    {{ !empty($appInfo->ceo_state) ? $appInfo->ceo_state : '' }}
                                @endif
                            </td>
                            <td>
                                @if($appInfo->n_ceo_country_id == 18)
                                    {{ !empty($appInfo->n_ceo_thana_name) ? $appInfo->n_ceo_thana_name : '' }}
                                @else
                                    {{ !empty($appInfo->n_ceo_state) ? $appInfo->n_ceo_state : '' }}
                                @endif
                            </td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_ceo_post_code))
                        <tr>
                            <td>Post/ Zip Code</td>
                            <td>{{ !empty($appInfo->ceo_post_code) ? $appInfo->ceo_post_code : '' }}</td>
                            <td>{{ !empty($appInfo->n_ceo_post_code) ? $appInfo->n_ceo_post_code : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_ceo_address))
                        <tr>
                            <td>House, Flat/ Apartment, Road</td>
                            <td>{{ !empty($appInfo->ceo_address) ? $appInfo->ceo_address : '' }}</td>
                            <td>{{ !empty($appInfo->n_ceo_address) ? $appInfo->n_ceo_address : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_ceo_telephone_no))
                        <tr>
                            <td>Telephone No.</td>
                            <td>{{ !empty($appInfo->ceo_telephone_no) ? $appInfo->ceo_telephone_no : '' }}</td>
                            <td>{{ !empty($appInfo->n_ceo_telephone_no) ? $appInfo->n_ceo_telephone_no : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_ceo_mobile_no))
                        <tr>
                            <td>Mobile No.</td>
                            <td>{{ !empty($appInfo->ceo_mobile_no) ? $appInfo->ceo_mobile_no : '' }}</td>
                            <td>{{ !empty($appInfo->n_ceo_mobile_no) ? $appInfo->n_ceo_mobile_no : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_ceo_email))
                        <tr>
                            <td>Email</td>
                            <td>{{ !empty($appInfo->ceo_email) ? $appInfo->ceo_email : '' }}</td>
                            <td>{{ !empty($appInfo->n_ceo_email) ? $appInfo->n_ceo_email : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_ceo_fax_no))
                        <tr>
                            <td>Fax No.</td>
                            <td>{{ !empty($appInfo->ceo_fax_no) ? $appInfo->ceo_fax_no : '' }}</td>
                            <td>{{ !empty($appInfo->n_ceo_fax_no) ? $appInfo->n_ceo_fax_no : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_ceo_father_name))
                        <tr>
                            <td>Father's Name</td>
                            <td>{{ !empty($appInfo->ceo_father_name) ? $appInfo->ceo_father_name : '' }}</td>
                            <td>{{ !empty($appInfo->n_ceo_father_name) ? $appInfo->n_ceo_father_name : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_ceo_mother_name))
                        <tr>
                            <td>Mother's Name</td>
                            <td>{{ !empty($appInfo->ceo_mother_name) ? $appInfo->ceo_mother_name : '' }}</td>
                            <td>{{ !empty($appInfo->n_ceo_mother_name) ? $appInfo->n_ceo_mother_name : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_ceo_spouse_name))
                        <tr>
                            <td>Spouse name</td>
                            <td>{{ !empty($appInfo->ceo_spouse_name) ? $appInfo->ceo_spouse_name : '' }}</td>
                            <td>{{ !empty($appInfo->n_ceo_spouse_name) ? $appInfo->n_ceo_spouse_name : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_ceo_gender) &&  $appInfo->n_ceo_gender != "Not defined")
                        <tr>
                            <td>Gender</td>
                            <td>{{ !empty($appInfo->ceo_gender) ? $appInfo->ceo_gender : '' }}</td>
                            <td>{{ !empty($appInfo->n_ceo_gender) ? $appInfo->n_ceo_gender : '' }}</td>
                        </tr>
                    @endif
                </table>

            @endif

            {{-- C. Office Address --}}
            @if (!empty($appInfo->n_office_division_name) || !empty($appInfo->n_office_district_name) || !empty($appInfo->n_office_thana_name) || !empty($appInfo->n_office_post_office) ||
                !empty($appInfo->n_office_post_code) || !empty($appInfo->n_office_address) || !empty($appInfo->n_office_telephone_no) || !empty($appInfo->n_office_mobile_no) ||
                !empty($appInfo->n_office_fax_no) || !empty($appInfo->n_office_email))

                <table class="table table-bordered" width="100%" cellpadding="10">
                    <tr>
                        <td colspan="3"><strong>C. Office Address</strong></td>
                    </tr>
                    <tr>
                        <td width="30%" style="font-weight: bold;text-align: center;">Field Name</td>
                        <td width="35%" style="font-weight: bold;text-align: center;">Existing Information</td>
                        <td width="35%" style="font-weight: bold;text-align: center;">Amended Information</td>
                    </tr>

                    @if (!empty($appInfo->n_office_division_name))
                        <tr>
                            <td>Division</td>
                            <td>{{ !empty($appInfo->office_division_name) ? $appInfo->office_division_name : '' }}</td>
                            <td>{{ !empty($appInfo->n_office_division_name) ? $appInfo->n_office_division_name : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_office_district_name))
                        <tr>
                            <td>District</td>
                            <td>{{ !empty($appInfo->office_district_name) ? $appInfo->office_district_name : '' }}</td>
                            <td>{{ !empty($appInfo->n_office_district_name) ? $appInfo->n_office_district_name : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_office_thana_name))
                        <tr>
                            <td>Police Station</td>
                            <td>{{ !empty($appInfo->office_thana_name) ? $appInfo->office_thana_name : '' }}</td>
                            <td>{{ !empty($appInfo->n_office_thana_name) ? $appInfo->n_office_thana_name : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_office_post_office))
                        <tr>
                            <td>Post Office</td>
                            <td>{{ !empty($appInfo->office_post_office) ? $appInfo->office_post_office : '' }}</td>
                            <td>{{ !empty($appInfo->n_office_post_office) ? $appInfo->n_office_post_office : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_office_post_code))
                        <tr>
                            <td>Post Code</td>
                            <td>{{ !empty($appInfo->office_post_code) ? $appInfo->office_post_code : '' }}</td>
                            <td>{{ !empty($appInfo->n_office_post_code) ? $appInfo->n_office_post_code : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_office_address))
                        <tr>
                            <td>Address</td>
                            <td>{{ !empty($appInfo->office_address) ? $appInfo->office_address : '' }}</td>
                            <td>{{ !empty($appInfo->n_office_address) ? $appInfo->n_office_address : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_office_telephone_no))
                        <tr>
                            <td>Telephone No.</td>
                            <td>{{ !empty($appInfo->office_telephone_no) ? $appInfo->office_telephone_no : '' }}</td>
                            <td>{{ !empty($appInfo->n_office_telephone_no) ? $appInfo->n_office_telephone_no : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_office_mobile_no))
                        <tr>
                            <td>Mobile No.</td>
                            <td>{{ !empty($appInfo->office_mobile_no) ? $appInfo->office_mobile_no : '' }}</td>
                            <td>{{ !empty($appInfo->n_office_mobile_no) ? $appInfo->n_office_mobile_no : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_office_fax_no))
                        <tr>
                            <td>Fax No.</td>
                            <td>{{ !empty($appInfo->office_fax_no) ? $appInfo->office_fax_no : '' }}</td>
                            <td>{{ !empty($appInfo->n_office_fax_no) ? $appInfo->n_office_fax_no : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_office_email))
                        <tr>
                            <td>Email</td>
                            <td>{{ !empty($appInfo->office_email) ? $appInfo->office_email : '' }}</td>
                            <td>{{ !empty($appInfo->n_office_email) ? $appInfo->n_office_email : '' }}</td>
                        </tr>
                    @endif
                </table>
            @endif

            {{-- D. Factory Address --}}
            @if (!empty($appInfo->n_factory_district_name) || !empty($appInfo->n_factory_thana_name) || !empty($appInfo->n_factory_post_office) || !empty($appInfo->n_factory_post_code) ||
                !empty($appInfo->n_factory_address) || !empty($appInfo->n_factory_telephone_no) || !empty($appInfo->n_factory_mobile_no) || !empty($appInfo->n_factory_fax_no))

                <table class="table table-bordered" width="100%" cellpadding="10">
                    <tr>
                        <td colspan="3"><strong>D. Factory Address</strong></td>
                    </tr>
                    <tr>
                        <td width="30%" style="font-weight: bold;text-align: center;">Field Name</td>
                        <td width="35%" style="font-weight: bold;text-align: center;">Existing Information</td>
                        <td width="35%" style="font-weight: bold;text-align: center;">Amended Information</td>
                    </tr>

                    @if (!empty($appInfo->n_factory_district_name))
                        <tr>
                            <td>District</td>
                            <td>{{ !empty($appInfo->factory_district_name) ? $appInfo->factory_district_name : '' }}</td>
                            <td>{{ !empty($appInfo->n_factory_district_name) ? $appInfo->n_factory_district_name : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_factory_thana_name))
                        <tr>
                            <td>Police Station</td>
                            <td>{{ !empty($appInfo->factory_thana_name) ? $appInfo->factory_thana_name : '' }}</td>
                            <td>{{ !empty($appInfo->n_factory_thana_name) ? $appInfo->n_factory_thana_name : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_factory_post_office))
                        <tr>
                            <td>Post Office</td>
                            <td>{{ !empty($appInfo->factory_post_office) ? $appInfo->factory_post_office : '' }}</td>
                            <td>{{ !empty($appInfo->n_factory_post_office) ? $appInfo->n_factory_post_office : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_factory_post_code))
                        <tr>
                            <td>Post Code</td>
                            <td>{{ !empty($appInfo->factory_post_code) ? $appInfo->factory_post_code : '' }}</td>
                            <td>{{ !empty($appInfo->n_factory_post_code) ? $appInfo->n_factory_post_code : '' }}</td>
                        </tr>
                    @endif


                    @if (!empty($appInfo->n_factory_address))
                        <tr>
                            <td>Address</td>
                            <td>{{ !empty($appInfo->factory_address) ? $appInfo->factory_address : '' }}</td>
                            <td>{{ !empty($appInfo->n_factory_address) ? $appInfo->n_factory_address : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_factory_telephone_no))
                        <tr>
                            <td>Telephone No.</td>
                            <td>{{ !empty($appInfo->factory_telephone_no) ? $appInfo->factory_telephone_no : '' }}</td>
                            <td>{{ !empty($appInfo->n_factory_telephone_no) ? $appInfo->n_factory_telephone_no : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_factory_mobile_no))
                        <tr>
                            <td>Mobile No.</td>
                            <td>{{ !empty($appInfo->factory_mobile_no) ? $appInfo->factory_mobile_no : '' }}</td>
                            <td>{{ !empty($appInfo->n_factory_mobile_no) ? $appInfo->n_factory_mobile_no : '' }}</td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_factory_fax_no))
                        <tr>
                            <td>Fax No.</td>
                            <td>{{ !empty($appInfo->factory_fax_no) ? $appInfo->factory_fax_no : '' }}</td>
                            <td>{{ !empty($appInfo->n_factory_fax_no) ? $appInfo->n_factory_fax_no : '' }}</td>
                        </tr>
                    @endif
                </table>
            @endif


            {{-- 1. Project status --}}
            @if (!empty($appInfo->n_project_status_name))
                <table class="table table-bordered" width="100%" cellpadding="10">
                    <tr>
                        <td colspan="3"><strong>1. Project Status</strong></td>
                    </tr>
                    <tr>
                        <td width="30%" style="font-weight: bold;text-align: center;">Field Name</td>
                        <td width="35%" style="font-weight: bold;text-align: center;">Existing Information</td>
                        <td width="35%" style="font-weight: bold;text-align: center;">Amended Information</td>
                    </tr>
                    <tr>
                        <td>Project status</td>
                        <td>{{ !empty($appInfo->project_status_name) ? $appInfo->project_status_name : '' }}</td>
                        <td>{{ !empty($appInfo->n_project_status_name) ? $appInfo->n_project_status_name : '' }}</td>
                    </tr>
                </table>
            @endif


            {{-- 2. Annual production capacity --}}
            @if (count($annualProductionCapacity) > 0)
                <table class="table table-bordered" width="100%">
                    <tr>
                        <td colspan="11"><strong>2. Annual Production Capacity</strong></td>
                    </tr>
                    <tr>
                        <td colspan="5" style="font-weight: bold;text-align: center;">Existing Information </td>
                        <td colspan="5" style="font-weight: bold;text-align: center;">Amended Information</td>
                    </tr>
                    <tr>
                        <td>Name of Product</td>
                        <td>Unit of Quantity</td>
                        <td>Quantity</td>
                        <td>Price (USD)</td>
                        <td>Value Taka (in million)</td>

                        <td>Name of Product</td>
                        <td>Unit of Quantity</td>
                        <td>Quantity</td>
                        <td>Price (USD)</td>
                        <td>Value Taka (in million)</td>
                        {{-- <td>Action Type</td> --}}
                    </tr>

                    {{-- @foreach($annualProductionCapacity as $product)
                        <tr>
                            <td>{{ $product->product_name }}</td>
                            <td>{{ !empty($product->unit_name) ? $product->unit_name : "" }}</td>
                            <td>{{ $product->quantity }}</td>
                            <td>{{ $product->price_usd }}</td>
                            <td>{{ $product->price_taka }}</td>

                            <td>{{ $product->n_product_name }}</td>
                            <td>{{ !empty($product->n_unit_name) ? $product->n_unit_name : "" }}</td>
                            <td>{{ $product->n_quantity }}</td>
                            <td>{{ $product->n_price_usd }}</td>
                            <td>{{ $product->n_price_taka }}</td>
                            
                        </tr>
                    @endforeach --}}

                    
                    @foreach($annualProductionCapacity as $product)
                        <tr>
                            <td>{{ empty($product->product_name) ? "" : $product->product_name }}</td>
                            <td>{{ empty($product->unit_name) ? "" : $product->unit_name }}</td>
                            <td>{{ empty($product->quantity) ? "" : $product->quantity }}</td>
                            <td>{{ empty($product->price_usd) ? "" : $product->price_usd }}</td>
                            <td>{{ empty($product->price_taka) ? ""  : $product->price_taka }}</td>

                            @if($product->amendment_type != 'no change')
                                <td>{{ empty($product->n_product_name) ? "" : $product->n_product_name }}</td>
                                <td>{{ empty($product->n_unit_name) ? "" : $product->n_unit_name }}</td>
                                <td>{{ empty($product->n_quantity) ? "" : $product->n_quantity }}</td>
                                <td>{{ empty($product->n_price_usd) ? "" : $product->n_price_usd }}</td>
                                <td>{{ empty($product->n_price_taka) ? "" : $product->n_price_taka }}</td>
                            @else
                                <td>{{ empty($product->product_name) ? "" : $product->product_name }}</td>
                                <td>{{ empty($product->unit_name) ? "" : $product->unit_name }}</td>
                                <td>{{ empty($product->quantity) ? "" : $product->quantity }}</td>
                                <td>{{ empty($product->price_usd) ? "" : $product->price_usd }}</td>
                                <td>{{ empty($product->price_taka) ? ""  : $product->price_taka }}</td>
                            @endif
                            {{-- <td>{{ $product->amendment_type }}</td> --}}
                        </tr>
                    @endforeach

                </table>
            @endif

            {{-- 3. Date of commercial operation --}}
            @if (!empty($appInfo->n_commercial_operation_date))
                <table class="table table-bordered" width="100%" cellpadding="10">
                    <tr>
                        <td colspan="3"><strong>3. Date of Commercial Operation</strong></td>
                    </tr>
                    <tr>
                        <td width="30%" style="font-weight: bold;text-align: center;">Field Name</td>
                        <td width="35%" style="font-weight: bold;text-align: center;">Existing Information</td>
                        <td width="35%" style="font-weight: bold;text-align: center;">Amended Information</td>
                    </tr>
                    <tr>
                        <td>Date of commercial operation</td>
                        <td>{{ !empty($appInfo->commercial_operation_date) ? date("d-M-Y", strtotime($appInfo->commercial_operation_date)) : '' }}</td>
                        <td>{{ !empty($appInfo->n_commercial_operation_date) ? date("d-M-Y", strtotime($appInfo->n_commercial_operation_date)) : '' }}</td>
                    </tr>
                </table>
            @endif


            {{-- 4. Sales (in 100%) --}}
            @if (!empty($appInfo->n_local_sales) || !empty($appInfo->n_foreign_sales) || !empty($appInfo->n_total_sales))
                <table class="table table-bordered" width="100%" cellpadding="10">
                    <tr>
                        <td colspan="3"><strong>4. Sales (in 100%)</strong></td>
                    </tr>
                    <tr>
                        <td width="30%" style="font-weight: bold;text-align: center;">Field Name</td>
                        <td width="35%" style="font-weight: bold;text-align: center;">Existing Information</td>
                        <td width="35%" style="font-weight: bold;text-align: center;">Amended Information</td>
                    </tr>
                    <tr>
                        <td>Local</td>
                        <td>{{ !empty($appInfo->local_sales) ? $appInfo->local_sales : '' }}</td>
                        <td>{{ !empty($appInfo->n_local_sales) ? $appInfo->n_local_sales : '' }}</td>
                    </tr>
                    <tr>
                        <td>Foreign</td>
                        <td>{{ !empty($appInfo->foreign_sales) ? $appInfo->foreign_sales : '' }}</td>
                        <td>{{ !empty($appInfo->n_foreign_sales) ? $appInfo->n_foreign_sales : '' }}</td>
                    </tr>
                    <tr>
                        <td>Total in %</td>
                        <td>{{ !empty($appInfo->total_sales) ? $appInfo->total_sales : '' }}</td>
                        <td>{{ !empty($appInfo->n_total_sales) ? $appInfo->n_total_sales : '' }}</td>
                    </tr>
                </table>
            @endif


            {{-- 5. Manpower of the organization --}}
            @if (!empty($appInfo->n_local_male) || !empty($appInfo->n_local_female) || !empty($appInfo->n_local_total) || !empty($appInfo->n_foreign_male) ||
                !empty($appInfo->n_foreign_female) || !empty($appInfo->n_foreign_total) || !empty($appInfo->n_manpower_total) || !empty($appInfo->n_manpower_local_ratio) ||
                !empty($appInfo->n_manpower_foreign_ratio))
                <table class="table table-responsive table-bordered">
                    <thead>
                    <tr>
                        <td colspan="10"><strong>5. Manpower of The Organization</strong></td>
                    </tr>
                    <tr>
                        <td>#</td>
                        <td colspan="3" style="font-weight: bold;text-align: center;">Local (Bangladesh Only)</td>
                        <td colspan="3" style="font-weight: bold;text-align: center;">Foreign (Abroad Country)</td>
                        <td style="font-weight: bold;text-align: center;">Grand Total</td>
                        <td colspan="2" style="font-weight: bold;text-align: center;">Ratio</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Information</td>
                        <td>Male</td>
                        <td>Female</td>
                        <td>Total (a)</td>

                        <td>Male</td>
                        <td>Female</td>
                        <td>Total (b)</td>

                        <td>(a+b)</td>

                        <td>Local</td>
                        <td>Foreign</td>
                    </tr>
                    <tr>
                        <td>Existing</td>
                        <td>{{ !empty($appInfo->local_male) ? $appInfo->local_male : '0' }}</td>
                        <td>
                            {{ !empty($appInfo->local_female) ? $appInfo->local_female : '0' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->local_total) ? $appInfo->local_total : '0' }}
                        </td>

                        <td>
                            {{ !empty($appInfo->foreign_male) ? $appInfo->foreign_male : '0' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->foreign_female) ? $appInfo->foreign_female : '0' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->foreign_total) ? $appInfo->foreign_total : '0' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->manpower_total) ? $appInfo->manpower_total : '0' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->manpower_local_ratio) ? $appInfo->manpower_local_ratio : '0' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->manpower_foreign_ratio) ? $appInfo->manpower_foreign_ratio : '0' }}
                        </td>
                    </tr>

                    <tr >
                        <td>Proposed</td>
                        <td>
                            {{ !empty($appInfo->n_local_male) ? $appInfo->n_local_male : '0' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_local_female) ? $appInfo->n_local_female : '0' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_local_total) ? $appInfo->n_local_total : '0' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_foreign_male) ? $appInfo->n_foreign_male : '0' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_foreign_female) ? $appInfo->n_foreign_female : '0' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_foreign_total) ? $appInfo->n_foreign_total : '0' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_manpower_total) ? $appInfo->n_manpower_total : '0' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_manpower_local_ratio) ? $appInfo->n_manpower_local_ratio : '0' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_manpower_foreign_ratio) ? $appInfo->n_manpower_foreign_ratio : '0' }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            @endif

            {{-- 6. Investment --}}
            @if (!empty($appInfo->n_local_land_ivst) || !empty($appInfo->n_local_building_ivst) || !empty($appInfo->n_local_machinery_ivst) || !empty($appInfo->n_local_others_ivst) ||
                !empty($appInfo->n_local_others_ivst) || !empty($appInfo->n_local_wc_ivst) || !empty($appInfo->n_total_fixed_ivst_million) || !empty($appInfo->n_total_fixed_ivst) ||
                !empty($appInfo->n_usd_exchange_rate) || !empty($appInfo->n_total_fee) || !empty($appInfo->n_finance_src_loc_equity_1) || !empty($appInfo->n_finance_src_foreign_equity_1) ||
                !empty($appInfo->n_finance_src_loc_total_equity_1) || !empty($appInfo->n_finance_src_loc_loan_1) || !empty($appInfo->n_finance_src_foreign_loan_1) ||
                !empty($appInfo->n_finance_src_total_loan) || !empty($appInfo->n_finance_src_loc_total_financing_m) || !empty($appInfo->n_finance_src_loc_total_financing_1))


                <table class="table table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <td colspan="5"><strong>6. Investment</strong></td>
                    </tr>
                    <tr>
                        <td width="30%" style="font-weight: bold;text-align: center;">Fixed Investment</td>
                        <td width="35%" colspan="2" style="font-weight: bold;text-align: center;">Existing Information </td>
                        <td width="35%" colspan="2" style="font-weight: bold;text-align: center;">Amended Information</td>
                    </tr>
                    </thead>
                    <tbody>

                    <tr>
                        <td>Land (Million)</td>
                        <td>
                            {{ !empty($appInfo->local_land_ivst) ? $appInfo->local_land_ivst : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->local_land_ivst_ccy) ? $currencyBDT[$appInfo->local_land_ivst_ccy] : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_local_land_ivst) ? $appInfo->n_local_land_ivst : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_local_land_ivst_ccy) ? $currencyBDT[$appInfo->n_local_land_ivst_ccy] : '' }}
                        </td>
                    </tr>

                    <tr>
                        <td>Building (Million)</td>
                        <td>
                            {{ !empty($appInfo->local_building_ivst) ? $appInfo->local_building_ivst : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->local_building_ivst_ccy) ? $currencyBDT[$appInfo->local_building_ivst_ccy] : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_local_building_ivst) ? $appInfo->n_local_building_ivst : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_local_building_ivst_ccy) ? $currencyBDT[$appInfo->n_local_building_ivst_ccy] : '' }}
                        </td>
                    </tr>

                    <tr>
                        <td>Machinery & Equipment (Million)</td>
                        <td>
                            {{ !empty($appInfo->local_machinery_ivst) ? $appInfo->local_machinery_ivst : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->local_machinery_ivst_ccy) ? $currencyBDT[$appInfo->local_machinery_ivst_ccy] : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_local_machinery_ivst) ? $appInfo->n_local_machinery_ivst : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_local_machinery_ivst_ccy) ? $currencyBDT[$appInfo->n_local_machinery_ivst_ccy] : '' }}
                        </td>
                    </tr>

                    <tr>
                        <td>Others (Million)</td>
                        <td>
                            {{ !empty($appInfo->local_others_ivst) ? $appInfo->local_others_ivst : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->local_others_ivst_ccy) ? $currencyBDT[$appInfo->local_others_ivst_ccy] : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_local_others_ivst) ? $appInfo->n_local_others_ivst : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_local_others_ivst_ccy) ? $currencyBDT[$appInfo->n_local_others_ivst_ccy] : '' }}
                        </td>
                    </tr>

                    <tr>
                        <td>Working Capital (Three Months) (Million)</td>
                        <td>
                            {{ !empty($appInfo->local_wc_ivst) ? $appInfo->local_wc_ivst : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->local_wc_ivst_ccy) ? $currencyBDT[$appInfo->local_wc_ivst_ccy] : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_local_wc_ivst) ? $appInfo->n_local_wc_ivst : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_local_wc_ivst_ccy) ? $currencyBDT[$appInfo->n_local_wc_ivst_ccy] : '' }}
                        </td>
                    </tr>

                    <tr>
                        <td>Total Investment (Million) (BDT)</td>
                        <td  colspan="2">
                            {{ !empty($appInfo->total_fixed_ivst_million) ? $appInfo->total_fixed_ivst_million : '' }}
                        </td>
                        <td  colspan="2">
                            {{ !empty($appInfo->n_total_fixed_ivst_million) ? $appInfo->n_total_fixed_ivst_million : '' }}
                        </td>
                    </tr>

                    <tr>
                        <td>Total Investment (BDT)</td>
                        <td  colspan="2">
                            {{ !empty($appInfo->total_fixed_ivst) ? $appInfo->total_fixed_ivst : '' }}
                        </td>
                        <td  colspan="2">
                            {{ !empty($appInfo->n_total_fixed_ivst) ? $appInfo->n_total_fixed_ivst : '' }}
                        </td>
                    </tr>

                    <tr>
                        <td>Dollar exchange rate (USD)</td>
                        <td  colspan="2">
                            {{ !empty($appInfo->usd_exchange_rate) ? $appInfo->usd_exchange_rate : '' }}
                        </td>
                        <td  colspan="2">
                            {{ !empty($appInfo->n_usd_exchange_rate) ? $appInfo->n_usd_exchange_rate : '' }}
                        </td>
                    </tr>

                    <tr>
                        <td>Total Fee (BDT)</td>
                        <td  colspan="2">
                            {{ !empty($appInfo->total_fee) ? $appInfo->total_fee : '' }}
                        </td>
                        <td  colspan="2">
                            {{ !empty($appInfo->n_total_fee) ? $appInfo->n_total_fee : '' }}
                        </td>
                    </tr>
                    </tbody>
                </table>

                {{-- 7. Source of finance --}}
                <table class="table table-responsive table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <td colspan="3"><strong>7. Source of finance</strong></td>
                    </tr>
                    <tr>
                        <td width="30%" style="font-weight: bold;text-align: center;">Fixed Investment</td>
                        <td width="35%" style="font-weight: bold;text-align: center;">Existing Information </td>
                        <td width="35%" style="font-weight: bold;text-align: center;">Amended Information</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><strong>(a)</strong> Local Equity (Million)</td>
                        <td>
                            {{ !empty($appInfo->finance_src_loc_equity_1) ? $appInfo->finance_src_loc_equity_1 : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_finance_src_loc_equity_1) ? $appInfo->n_finance_src_loc_equity_1 : '' }}
                        </td>
                    </tr>

                    <tr>
                        <td>Foreign Equity (Million)</td>
                        <td>
                            {{ !empty($appInfo->finance_src_foreign_equity_1) ? $appInfo->finance_src_foreign_equity_1 : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_finance_src_foreign_equity_1) ? $appInfo->n_finance_src_foreign_equity_1 : '' }}
                        </td>
                    </tr>

                    <tr>
                        <td><strong>Total Equity</strong></td>
                        <td>
                            {{ !empty($appInfo->finance_src_loc_total_equity_1) ? $appInfo->finance_src_loc_total_equity_1 : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_finance_src_loc_total_equity_1) ? $appInfo->n_finance_src_loc_total_equity_1 : '' }}
                        </td>
                    </tr>

                    <tr>
                        <td><strong>(b)</strong> Local Loan (Million)</td>
                        <td>
                            {{ !empty($appInfo->finance_src_loc_loan_1) ? $appInfo->finance_src_loc_loan_1 : '' }}
                        </td>

                        <td>
                            {{ !empty($appInfo->n_finance_src_loc_loan_1) ? $appInfo->n_finance_src_loc_loan_1 : '' }}
                        </td>
                    </tr>

                    <tr>
                        <td>Foreign Loan (Million)</td>
                        <td>
                            {{ !empty($appInfo->finance_src_foreign_loan_1) ? $appInfo->finance_src_foreign_loan_1 : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_finance_src_foreign_loan_1) ? $appInfo->n_finance_src_foreign_loan_1 : '' }}
                        </td>
                    </tr>

                    <tr>
                        <td>Total Loan (Million)</td>
                        <td>
                            {{ !empty($appInfo->finance_src_total_loan) ? $appInfo->finance_src_total_loan : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_finance_src_total_loan) ? $appInfo->n_finance_src_total_loan : '' }}
                        </td>
                    </tr>

                    <tr>
                        <td><strong>Total Financing Million (a+b)</strong></td>
                        <td>
                            {{ !empty($appInfo->finance_src_loc_total_financing_m) ? $appInfo->finance_src_loc_total_financing_m : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_finance_src_loc_total_financing_m) ? $appInfo->n_finance_src_loc_total_financing_m : '' }}
                        </td>
                    </tr>

                    <tr>
                        <td><strong>Total Financing BDT (a+b)</strong></td>
                        <td>
                            {{ !empty($appInfo->finance_src_loc_total_financing_1) ? $appInfo->finance_src_loc_total_financing_1 : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_finance_src_loc_total_financing_1) ? $appInfo->n_finance_src_loc_total_financing_1 : '' }}
                        </td>
                    </tr>
                    </tbody>
                </table>

                <table width="100%" class="table table-bordered">
                    <tr>
                        <td colspan="6"><strong>Country wise source of finance (Million BDT)</strong></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-weight: bold;text-align: center;">Existing Information </td>
                        <td colspan="3" style="font-weight: bold;text-align: center;">Amended Information</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;text-align: center;">Country</td>
                        <td style="font-weight: bold;text-align: center;">Equity Amount</td>
                        <td style="font-weight: bold;text-align: center;">Loan Amount</td>

                        <td style="font-weight: bold;text-align: center;">Country</td>
                        <td style="font-weight: bold;text-align: center;">Equity Amount</td>
                        <td style="font-weight: bold;text-align: center;">Loan Amount</td>
                    </tr>
                    @if (count($sourceOfFinance) > 0)
                        @foreach($sourceOfFinance as $source)
                            <tr>
                                <td>{{ !empty($source->country_name) ? $source->country_name : "" }}</td>
                                <td>{{ $source->equity_amount }}</td>
                                <td>{{ $source->loan_amount }}</td>

                                <td>{{ !empty($source->n_country_name) ? $source->n_country_name : "" }}</td>
                                <td>{{ $source->n_equity_amount }}</td>
                                <td>{{ $source->n_loan_amount }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6">Not data found!</td>
                        </tr>
                    @endif
                </table>

            @endif

            {{-- 8. Public utility service --}}
            @if (!empty($appInfo->n_public_land) || !empty($appInfo->n_public_electricity) || !empty($appInfo->n_public_gas) || !empty($appInfo->n_public_telephone) ||
                !empty($appInfo->n_public_road) || !empty($appInfo->n_public_water) || !empty($appInfo->n_public_drainage) || !empty($appInfo->n_public_others))
                <table class="table table-responsive table-bordered">
                    <thead>
                    <tr>
                        <td colspan="9"><strong>8. Public Utility Service</strong></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;text-align: center;">Information</td>
                        <td style="font-weight: bold;text-align: center;">Land</td>
                        <td style="font-weight: bold;text-align: center;">Electricity</td>
                        <td style="font-weight: bold;text-align: center;">Gas</td>
                        <td style="font-weight: bold;text-align: center;">Telephone</td>
                        <td style="font-weight: bold;text-align: center;">Road</td>
                        <td style="font-weight: bold;text-align: center;">Water</td>
                        <td style="font-weight: bold;text-align: center;">Drainage</td>
                        <td style="font-weight: bold;text-align: center;">Others</td>

                    </tr>
                    </thead>
                    <tbody>
                    <tr >
                        <td>Existing </td>
                        <td>
                            @if ($appInfo->public_land == 1)
                                <img src="assets/images/checked.png" width="10" height="10"/>
                                Land
                            @endif
                        </td>
                        <td>
                            @if ($appInfo->public_electricity == 1) <img src="assets/images/checked.png" width="10" height="10"/> Electricity @endif
                        </td>
                        <td>
                            @if ($appInfo->public_gas == 1) <img src="assets/images/checked.png" width="10" height="10"/> Gas @endif
                        </td>
                        <td>
                            @if ($appInfo->public_telephone == 1) <img src="assets/images/checked.png" width="10" height="10"/> Telephone @endif
                        </td>
                        <td>
                            @if ($appInfo->public_road == 1) <img src="assets/images/checked.png" width="10" height="10"/> Road @endif
                        </td>
                        <td>
                            @if ($appInfo->public_water == 1) <img src="assets/images/checked.png" width="10" height="10"/> Water @endif
                        </td>

                        <td>
                            @if ($appInfo->public_drainage == 1) <img src="assets/images/checked.png" width="10" height="10"/> Drainage @endif
                        </td>
                        <td>
                            @if ($appInfo->public_others == 1) <img src="assets/images/checked.png" width="10" height="10"/> Others @endif
                        </td>
                    </tr>

                    <tr >
                        <td>Proposed</td>
                        <td>
                            @if ($appInfo->n_public_land == 1) <img src="assets/images/checked.png" width="10" height="10"/> Land @endif
                        </td>
                        <td>
                            @if ($appInfo->n_public_electricity == 1) <img src="assets/images/checked.png" width="10" height="10"/> Electricity @endif
                        </td>
                        <td>
                            @if ($appInfo->n_public_gas == 1) <img src="assets/images/checked.png" width="10" height="10"/> Gas @endif
                        </td>
                        <td>
                            @if ($appInfo->n_public_telephone == 1) <img src="assets/images/checked.png" width="10" height="10"/> Telephone @endif
                        </td>
                        <td>
                            @if ($appInfo->n_public_road == 1) <img src="assets/images/checked.png" width="10" height="10"/> Road @endif
                        </td>
                        <td>
                            @if ($appInfo->n_public_water == 1) <img src="assets/images/checked.png" width="10" height="10"/> Water @endif
                        </td>

                        <td>
                            @if ($appInfo->n_public_drainage == 1) <img src="assets/images/checked.png" width="10" height="10"/> Drainage @endif
                        </td>
                        <td>
                            @if ($appInfo->n_public_others == 1) <img src="assets/images/checked.png" width="10" height="10"/> Others @endif
                        </td>
                    </tr>
                    </tbody>
                </table>
            @endif


            {{-- 9. Trade licence details --}}
            @if (!empty($appInfo->n_trade_licence_num) || !empty($appInfo->n_trade_licence_issuing_authority))
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <td colspan="3"><strong>9. Trade Licence Details</strong></td>
                    </tr>
                    <tr>
                        <td width="30%" style="font-weight: bold;text-align: center;">Field Name</td>
                        <td width="35%" style="font-weight: bold;text-align: center;">Existing Information</td>
                        <td width="35%" style="font-weight: bold;text-align: center;">Amended Information</td>
                    </tr>
                    </thead>
                    <tbody>
                    @if (!empty($appInfo->n_trade_licence_num))
                        <tr>
                            <td>Trade Licence Number</td>
                            <td>
                                {{ !empty($appInfo->trade_licence_num) ? $appInfo->trade_licence_num : '' }}
                            </td>
                            <td>
                                {{ !empty($appInfo->n_trade_licence_num) ? $appInfo->n_trade_licence_num : '' }}
                            </td>
                        </tr>
                    @endif

                    @if (!empty($appInfo->n_trade_licence_issuing_authority))
                        <tr>
                            <td>Issuing Authority</td>
                            <td>
                                {{ !empty($appInfo->trade_licence_issuing_authority) ? $appInfo->trade_licence_issuing_authority : '' }}
                            </td>
                            <td>
                                {{ !empty($appInfo->n_trade_licence_issuing_authority) ? $appInfo->n_trade_licence_issuing_authority : '' }}
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            @endif


            {{-- 10. Tin --}}
            @if (!empty($appInfo->n_tin_number))
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <td colspan="3"><strong>10. Tin</strong></td>
                    </tr>
                    <tr>
                        <td width="30%" style="font-weight: bold;text-align: center;">Field Name</td>
                        <td width="35%" style="font-weight: bold;text-align: center;">Existing Information</td>
                        <td width="35%" style="font-weight: bold;text-align: center;">Amended Information</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Tin Number</td>
                        <td>
                            {{ !empty($appInfo->tin_number) ? $appInfo->tin_number : '' }}
                        </td>
                        <td>
                            {{ !empty($appInfo->n_tin_number) ? $appInfo->n_tin_number : '' }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            @endif


            @if($appInfo->organization_status_id == 3 || $appInfo->n_organization_status_id == 3)
                {{-- 12. Description of machinery and equipment --}}
                @if (!empty($appInfo->n_machinery_local_qty) || !empty($appInfo->n_machinery_local_price_bdt) || !empty($appInfo->n_imported_qty) || !empty($appInfo->n_imported_qty_price_bdt) ||
                    !empty($appInfo->n_total_machinery_qty) || !empty($appInfo->n_total_machinery_price))

                    <table class="table table-responsive table-bordered">
                        <thead>
                        <tr>
                            <td colspan="5"><strong>13. Description of Machinery and Equipment</strong></td>
                        </tr>
                        <tr>
                            <td width="20%" style="font-weight: bold;text-align: center;">Field Name</td>
                            <td width="40%" colspan="2" style="font-weight: bold;text-align: center;">Existing Information </td>
                            <td width="40%" colspan="2" style="font-weight: bold;text-align: center;">Amended Information</td>
                        </tr>
                        <tr>
                            <td width="20%"></td>
                            <td width="20%">Quantity</td>
                            <td width="20%">Price (BDT)</td>
                            <td width="20%">Quantity</td>
                            <td width="20%">Price (BDT)</td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Locally Collected</td>
                            <td>
                                {{ !empty($appInfo->machinery_local_qty) ? $appInfo->machinery_local_qty : '' }}
                            </td>
                            <td>
                                {{ !empty($appInfo->machinery_local_price_bdt) ? $appInfo->machinery_local_price_bdt : '' }}
                            </td>
                            <td>
                                {{ !empty($appInfo->n_machinery_local_qty) ? $appInfo->n_machinery_local_qty : '' }}
                            </td>
                            <td>
                                {{ !empty($appInfo->n_machinery_local_price_bdt) ? $appInfo->n_machinery_local_price_bdt : '' }}
                            </td>
                        </tr>

                        <tr>
                            <td>Imported</td>
                            <td>
                                {{ !empty($appInfo->imported_qty) ? $appInfo->imported_qty : '' }}
                            </td>
                            <td>
                                {{ !empty($appInfo->imported_qty_price_bdt) ? $appInfo->imported_qty_price_bdt : '' }}
                            </td>
                            <td>
                                {{ !empty($appInfo->n_imported_qty) ? $appInfo->n_imported_qty : '' }}
                            </td>
                            <td>
                                {{ !empty($appInfo->n_imported_qty_price_bdt) ? $appInfo->n_imported_qty_price_bdt : '' }}
                            </td>
                        </tr>

                        <tr>
                            <td>Total</td>
                            <td>
                                {{ !empty($appInfo->total_machinery_qty) ? $appInfo->total_machinery_qty : '' }}
                            </td>
                            <td>
                                {{ !empty($appInfo->total_machinery_price) ? $appInfo->total_machinery_price : '' }}
                            </td>
                            <td>
                                {{ !empty($appInfo->n_total_machinery_qty) ? $appInfo->n_total_machinery_qty : '' }}
                            </td>
                            <td>
                                {{ !empty($appInfo->n_total_machinery_price) ? $appInfo->n_total_machinery_price : '' }}
                            </td>
                        </tr>

                        </tbody>
                    </table>
                @endif

                @if (!empty($appInfo->n_local_description) || !empty($appInfo->n_imported_description))
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <td colspan="3"><strong>14. Description of Raw & Packing Materials</strong></td>
                        </tr>
                        <tr>
                            <td width="30%" style="font-weight: bold;text-align: center;">Field Name</td>
                            <td width="35%" style="font-weight: bold;text-align: center;">Existing Information</td>
                            <td width="35%" style="font-weight: bold;text-align: center;">Amended Information</td>
                        </tr>
                        </thead>
                        <tbody>
                        @if (!empty($appInfo->n_local_description))
                            <tr>
                                <td>Locally</td>
                                <td>
                                    {{ !empty($appInfo->local_description) ? $appInfo->local_description : '' }}
                                </td>
                                <td>
                                    {{ !empty($appInfo->n_local_description) ? $appInfo->n_local_description : '' }}
                                </td>
                            </tr>
                        @endif

                        @if (!empty($appInfo->n_imported_description))
                            <tr>
                                <td>Imported</td>
                                <td>
                                    {{ !empty($appInfo->imported_description) ? $appInfo->imported_description : '' }}
                                </td>
                                <td>
                                    {{ !empty($appInfo->n_imported_description) ? $appInfo->n_imported_description : '' }}
                                </td>
                            </tr>
                        @endif
                        </tbody>

                        

                    </table>
                @endif
            @endif
        </div>
       

        <div class="col-md-12">
            <p>
                <strong>Additional Condition :</strong> {{ !empty($appInfo->approval_copy_remarks) ? $appInfo->approval_copy_remarks : '' }}   
            </p>
        </div>
        
        <div class="col-md-12">
            <p>
                All other teams &amp; conditions in the registration No. ({{ $tracking_no }}) which was communicated to
                you vide this Authority&#39;s {{ !empty($bra_memo_no->bra_ref_no) ? $bra_memo_no->bra_ref_no : '' }} shall remain unchanged.
            </p>
        </div>
        <br>
        
    </div>

    <br>
    <div class="row">
        <div class="col-md-12">
            <div style="float: left;width: 55%;">
                <div style="text-align:left">
                    {{ $company_md['name'] }}<br>
                    {{ $company_md['designation'] }}<br>
                    {{ !empty($appInfo->company_name) ? $appInfo->company_name : '' }}<br>
                    {{-- {{ empty($appInfo->n_company_name) ? $appInfo->company_name : $appInfo->n_company_name }}<br> --}}
                    {{ $company_office['address'] . ', ' . $company_office['post_office'] . ', ' . $company_office['police_station'] . ', ' . $company_office['district'] . ', ' . $company_office['post_code'] }}<br>
                </div>
            </div>

            <div style="text-align: center; ">
                <div style="padding-left: 45%;">
                    Your Faithfully <br>
                    <img src="{{ $director_signature }}" width="70" alt="Director Signature" /><br>
                    ({{ $director->signer_name }})<br>
                    {{ $director->signer_designation }}<br>
                    Phone: {{ $director->signer_phone }}<br>
                    Email: {{ $director->signer_email }}<br>
                </div>
            </div>
            <br><br><br>
            <div>
                Copy forwarded for information and necessary action (Not according to seniority):<br>
                1. Chairman, National Board of Revenue, Segunbagicha, Dhaka<br>
                2. Chief Controller, Office of The Chief Controller of Imports &amp; Exports (CCI&amp;E), Level 15, National Sports
                Council Tower, Palton, Dhaka<br>
                3. General Manager, Statistics Department, Bangladesh Bank, Motijheel, Dhaka<br>
                4. Commissioner, Customs Bond Commissionerate, Segun Bagicha, Dhaka<br>
                5. Director (IM&amp;C) Bangladesh Investment Development Authority (BIDA), Dhaka.
            </div>
        </div>
    </div>
</div>
</body>
</html>