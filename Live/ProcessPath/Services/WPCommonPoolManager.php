<?php

namespace App\Modules\ProcessPath\Services;

use App\Libraries\UtilFunction;
use App\Modules\WorkPermitAmendment\Models\WorkPermitAmendment;
use App\Modules\WorkPermitCancellation\Models\WorkPermitCancellation;
use App\Modules\WorkPermitExtension\Models\WorkPermitExtension;
use App\Modules\WorkPermitNew\Models\WorkPermitNew;
use App\VRCommonPool;
use App\WPCommonPool;
use Illuminate\Support\Facades\DB;

class WPCommonPoolManager
{
    public static function wpnDataStore($tracking_no, $ref_id)
    {

        try {
            DB::beginTransaction();
            $wpnData = WorkPermitNew::where('id', $ref_id)->first();

            $appData = new WPCommonPool();

            if ($wpnData->last_vr == 'yes') {
                $getVRCommonPoolId = VRCommonPool::where('vr_tracking_no', $wpnData->ref_app_tracking_no)
                    ->orWhere('vra_tracking_no', $wpnData->ref_app_tracking_no)
                    ->first(['id']);
                $appData->vr_common_pool_id = isset($getVRCommonPoolId) ? $getVRCommonPoolId->id : 0; // vr_common_pool id
            }

            // Work permit new tracking number
            $appData->wpn_tracking_no = $tracking_no;

            // Company Information
            $appData->company_name = $wpnData->company_name;
            $appData->company_name_bn = $wpnData->company_name_bn;
            $appData->service_type = $wpnData->service_type;
            $appData->reg_commercial_office = $wpnData->reg_commercial_office;
            $appData->ownership_status_id = $wpnData->ownership_status_id;
            $appData->organization_type_id = $wpnData->organization_type_id;
            if ($wpnData->organization_type_id == 14) {
                $appData->organization_type_other = $wpnData->organization_type_other;
            }
            $appData->major_activities = $wpnData->major_activities;

            // Information of Principal Promoter/ Chairman/ Managing Director/ State CEO
            $appData->ceo_country_id = $wpnData->ceo_country_id;
            $appData->ceo_dob = $wpnData->ceo_dob;
            $appData->ceo_passport_no = $wpnData->ceo_passport_no;
            $appData->ceo_nid = $wpnData->ceo_nid;
            $appData->ceo_full_name = $wpnData->ceo_full_name;
            $appData->ceo_designation = $wpnData->ceo_designation;
            $appData->ceo_district_id = $wpnData->ceo_district_id;
            $appData->ceo_city = $wpnData->ceo_city;
            $appData->ceo_state = $wpnData->ceo_state;
            $appData->ceo_thana_id = $wpnData->ceo_thana_id;
            $appData->ceo_post_code = $wpnData->ceo_post_code;
            $appData->ceo_address = $wpnData->ceo_address;
            $appData->ceo_telephone_no = $wpnData->ceo_telephone_no;
            $appData->ceo_mobile_no = $wpnData->ceo_mobile_no;
            $appData->ceo_fax_no = $wpnData->ceo_fax_no;
            $appData->ceo_email = $wpnData->ceo_email;
            $appData->ceo_father_name = $wpnData->ceo_father_name;
            $appData->ceo_mother_name = $wpnData->ceo_mother_name;
            $appData->ceo_spouse_name = $wpnData->ceo_spouse_name;
            $appData->ceo_gender = $wpnData->ceo_gender;
            $appData->ceo_auth_letter = $wpnData->ceo_auth_letter;

            // Office Address
            $appData->office_division_id = $wpnData->office_division_id;
            $appData->office_district_id = $wpnData->office_district_id;
            $appData->office_thana_id = $wpnData->office_thana_id;
            $appData->office_post_office = $wpnData->office_post_office;
            $appData->office_post_code = $wpnData->office_post_code;
            $appData->office_address = $wpnData->office_address;
            $appData->office_telephone_no = $wpnData->office_telephone_no;
            $appData->office_mobile_no = $wpnData->office_mobile_no;
            $appData->office_fax_no = $wpnData->office_fax_no;
            $appData->office_email = $wpnData->office_email;

            // Factory Address
            $appData->factory_district_id = $wpnData->factory_district_id;
            $appData->factory_thana_id = $wpnData->factory_thana_id;
            $appData->factory_post_office = $wpnData->factory_post_office;
            $appData->factory_post_code = $wpnData->factory_post_code;
            $appData->factory_address = $wpnData->factory_address;
            $appData->factory_telephone_no = $wpnData->factory_telephone_no;
            $appData->factory_mobile_no = $wpnData->factory_mobile_no;
            $appData->factory_fax_no = $wpnData->factory_fax_no;
            $appData->factory_email = $wpnData->factory_email;
            $appData->factory_mouja = $wpnData->factory_mouja;
            $appData->investor_photo = $wpnData->investor_photo;

            // Business category (private/ govt.)
            $appData->business_category = $wpnData->business_category;

            // Did you receive Visa Recommendation through online OSS?
            $appData->last_vr = $wpnData->last_vr;

            // Type of visa
            $appData->work_permit_type = $wpnData->work_permit_type;

            // Date of arrival in Bangladesh
            $appData->date_of_arrival = $wpnData->date_of_arrival;

            // Expiry Date of Office Permission
            $appData->expiry_date_of_op = $wpnData->expiry_date_of_op;

            // Desired duration for work permit
            $appData->duration_start_date = $wpnData->duration_start_date;
            $appData->duration_end_date = $wpnData->duration_end_date;
            $appData->desired_duration = $wpnData->desired_duration;
            $appData->duration_amount = $wpnData->duration_amount;

            // Approved Permission Period
            $appData->approved_duration_start_date = $wpnData->approved_duration_start_date;
            $appData->approved_duration_end_date = $wpnData->approved_duration_end_date;
            $appData->approved_desired_duration = $wpnData->approved_desired_duration;
            $appData->approved_duration_amount = $wpnData->approved_duration_amount;

            // Reference approved visa recommendation tracking number and date
            $appData->ref_app_tracking_no = $wpnData->ref_app_tracking_no;
            $appData->ref_app_approve_date = $wpnData->ref_app_approve_date;

            // Others Particular of Organization
            $appData->nature_of_business = $wpnData->nature_of_business;
            $appData->received_remittance = $wpnData->received_remittance;
            $appData->auth_capital = $wpnData->auth_capital;
            $appData->paid_capital = $wpnData->paid_capital;

            // Contact address of the expatriate in Bangladesh
            $appData->ex_office_division_id = $wpnData->ex_office_division_id;
            $appData->ex_office_district_id =$wpnData->ex_office_district_id;
            $appData->ex_office_thana_id = $wpnData->ex_office_thana_id;
            $appData->ex_office_post_office = $wpnData->ex_office_post_office;
            $appData->ex_office_post_code = $wpnData->ex_office_post_code;
            $appData->ex_office_address = $wpnData->ex_office_address;
            $appData->ex_office_telephone_no = $wpnData->ex_office_telephone_no;
            $appData->ex_office_mobile_no = $wpnData->ex_office_mobile_no;
            $appData->ex_office_fax_no = $wpnData->ex_office_fax_no;
            $appData->ex_office_email = $wpnData->ex_office_email;

            // Passport Information
            $appData->emp_passport_no = $wpnData->emp_passport_no;
            $appData->emp_personal_no = $wpnData->emp_personal_no;
            $appData->emp_surname = $wpnData->emp_surname;
            $appData->emp_name = $wpnData->emp_name;
            $appData->emp_designation = $wpnData->emp_designation;
            $appData->brief_job_description = $wpnData->brief_job_description;
            // $appData->major_activities = $wpnData->major_activities;
            $appData->emp_given_name = $wpnData->emp_given_name;
            $appData->emp_nationality_id = $wpnData->emp_nationality_id;
            $appData->emp_date_of_birth = $wpnData->emp_date_of_birth;
            $appData->emp_place_of_birth = $wpnData->emp_place_of_birth;
            $appData->pass_issue_date = $wpnData->pass_issue_date;
            $appData->pass_expiry_date = $wpnData->pass_expiry_date;
            $appData->place_of_issue = $wpnData->place_of_issue;

            // Previous Travel history of the expatriate to Bangladesh
            $appData->travel_history = $wpnData->travel_history;
            $appData->th_visit_with_emp_visa = $wpnData->th_visit_with_emp_visa;
            $appData->th_emp_work_permit = $wpnData->th_emp_work_permit;
            $appData->th_emp_tin_no = $wpnData->th_emp_tin_no;
            $appData->th_emp_wp_no = $wpnData->th_emp_wp_no;
            $appData->th_emp_org_name = $wpnData->th_emp_org_name;
            $appData->th_emp_org_address = $wpnData->th_emp_org_address;
            $appData->th_org_district_id = $wpnData->th_org_district_id;
            $appData->th_org_thana_id = $wpnData->th_org_thana_id;
            $appData->th_org_post_office = $wpnData->th_org_post_office;
            $appData->th_org_post_code = $wpnData->th_org_post_code;
            $appData->th_org_telephone_no = $wpnData->th_org_telephone_no;
            $appData->th_org_email = $wpnData->th_org_email;
            $appData->th_first_work_permit = $wpnData->th_first_work_permit;
            $appData->th_resignation_letter = $wpnData->th_resignation_letter;
            $appData->th_release_order = $wpnData->th_release_order;
            $appData->th_last_extension = $wpnData->th_last_extension;
            $appData->th_last_work_permit = $wpnData->th_last_work_permit;
            $appData->th_income_tax = $wpnData->th_income_tax;

            // Manpower section
            $appData->local_executive = $wpnData->local_executive;
            $appData->local_stuff = $wpnData->local_stuff;
            $appData->local_total = $wpnData->local_total;
            $appData->foreign_executive = $wpnData->foreign_executive;
            $appData->foreign_stuff = $wpnData->foreign_stuff;
            $appData->foreign_total = $wpnData->foreign_total;
            $appData->manpower_total = $wpnData->manpower_total;
            $appData->manpower_local_ratio = $wpnData->manpower_local_ratio;
            $appData->manpower_foreign_ratio = $wpnData->manpower_foreign_ratio;

            // Compensation and Benefit
            $appData->basic_payment_type_id = $wpnData->basic_payment_type_id;
            $appData->basic_local_amount = $wpnData->basic_local_amount;
            $appData->basic_local_currency_id = $wpnData->basic_local_currency_id;

            $appData->overseas_payment_type_id = $wpnData->overseas_payment_type_id;
            $appData->overseas_local_amount = $wpnData->overseas_local_amount;
            $appData->overseas_local_currency_id = $wpnData->overseas_local_currency_id;

            $appData->house_payment_type_id = $wpnData->house_payment_type_id;
            $appData->house_local_amount = $wpnData->house_local_amount;
            $appData->house_local_currency_id = $wpnData->house_local_currency_id;

            $appData->conveyance_payment_type_id = $wpnData->conveyance_payment_type_id;
            $appData->conveyance_local_amount = $wpnData->conveyance_local_amount;
            $appData->conveyance_local_currency_id = $wpnData->conveyance_local_currency_id;

            $appData->medical_payment_type_id = $wpnData->medical_payment_type_id;
            $appData->medical_local_amount = $wpnData->medical_local_amount;
            $appData->medical_local_currency_id = $wpnData->medical_local_currency_id;

            $appData->ent_payment_type_id = $wpnData->ent_payment_type_id;
            $appData->ent_local_amount = $wpnData->ent_local_amount;
            $appData->ent_local_currency_id = $wpnData->ent_local_currency_id;

            $appData->bonus_payment_type_id = $wpnData->bonus_payment_type_id;
            $appData->bonus_local_amount = $wpnData->bonus_local_amount;
            $appData->bonus_local_currency_id = $wpnData->bonus_local_currency_id;
            $appData->other_benefits = $wpnData->other_benefits;

            // Authorized Person Information
            $appData->auth_full_name = $wpnData->auth_full_name;
            $appData->auth_designation = $wpnData->auth_designation;
            $appData->auth_mobile_no = $wpnData->auth_mobile_no;
            $appData->auth_email = $wpnData->auth_email;
            $appData->auth_image = $wpnData->auth_image;
            $appData->accept_terms = $wpnData->accept_terms;

            // Payment information
            $appData->sf_payment_id = $wpnData->sf_payment_id;
            $appData->gf_payment_id = $wpnData->gf_payment_id;

            // Application approved data
            $appData->approved_date = $wpnData->approved_date;

            $appData->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
//            dd($e->getMessage(), $e->getLine(), $e->getFile());
            DB::rollback();
            return false;
        }
    }

    public static function wpeDataStore($tracking_no, $ref_id)
    {
        try {

            DB::beginTransaction();

            $wpeData = WorkPermitExtension::where('id', $ref_id)->first();

            if (!empty($wpeData->ref_app_tracking_no)) {
                $appData = WPCommonPool::firstOrNew(['wpn_tracking_no' => $wpeData->ref_app_tracking_no]);
            } else {
                $appData = new WPCommonPool();
            }

            // Work permit extension tracking number
            $appData->wpe_tracking_no = $tracking_no;

            // Company Information
            $appData->company_name = $wpeData->company_name;
            $appData->company_name_bn = $wpeData->company_name_bn;
            $appData->service_type = $wpeData->service_type;
            $appData->reg_commercial_office = $wpeData->reg_commercial_office;
            $appData->ownership_status_id = $wpeData->ownership_status_id;
            $appData->organization_type_id = $wpeData->organization_type_id;
            $appData->major_activities = $wpeData->major_activities;

            // Information of Principal Promoter/ Chairman/ Managing Director/ State CEO
            $appData->ceo_country_id = $wpeData->ceo_country_id;
            $appData->ceo_dob = $wpeData->ceo_dob;
            $appData->ceo_passport_no = $wpeData->ceo_passport_no;
            $appData->ceo_nid = $wpeData->ceo_nid;
            $appData->ceo_full_name = $wpeData->ceo_full_name;
            $appData->ceo_designation = $wpeData->ceo_designation;
            $appData->ceo_district_id = $wpeData->ceo_district_id;
            $appData->ceo_city = $wpeData->ceo_city;
            $appData->ceo_state = $wpeData->ceo_state;
            $appData->ceo_thana_id = $wpeData->ceo_thana_id;
            $appData->ceo_post_code = $wpeData->ceo_post_code;
            $appData->ceo_address = $wpeData->ceo_address;
            $appData->ceo_telephone_no = $wpeData->ceo_telephone_no;
            $appData->ceo_mobile_no = $wpeData->ceo_mobile_no;
            $appData->ceo_fax_no = $wpeData->ceo_fax_no;
            $appData->ceo_email = $wpeData->ceo_email;
            $appData->ceo_father_name = $wpeData->ceo_father_name;
            $appData->ceo_mother_name = $wpeData->ceo_mother_name;
            $appData->ceo_spouse_name = $wpeData->ceo_spouse_name;
            $appData->ceo_gender = $wpeData->ceo_gender;

            // Office Address
            $appData->office_division_id = $wpeData->office_division_id;
            $appData->office_district_id = $wpeData->office_district_id;
            $appData->office_thana_id = $wpeData->office_thana_id;
            $appData->office_post_office = $wpeData->office_post_office;
            $appData->office_post_code = $wpeData->office_post_code;
            $appData->office_address = $wpeData->office_address;
            $appData->office_telephone_no = $wpeData->office_telephone_no;
            $appData->office_mobile_no = $wpeData->office_mobile_no;
            $appData->office_fax_no = $wpeData->office_fax_no;
            $appData->office_email = $wpeData->office_email;

            // Factory Address
            $appData->factory_district_id = $wpeData->factory_district_id;
            $appData->factory_thana_id = $wpeData->factory_thana_id;
            $appData->factory_post_office = $wpeData->factory_post_office;
            $appData->factory_post_code = $wpeData->factory_post_code;
            $appData->factory_address = $wpeData->factory_address;
            $appData->factory_telephone_no = $wpeData->factory_telephone_no;
            $appData->factory_mobile_no = $wpeData->factory_mobile_no;
            $appData->factory_fax_no = $wpeData->factory_fax_no;
            $appData->factory_email = $wpeData->factory_email;
            $appData->factory_mouja = $wpeData->factory_mouja;
            $appData->investor_photo = $wpeData->investor_photo;

            // Type of visa
            $appData->work_permit_type = $wpeData->work_permit_type;

            // Expiry Date of Office Permission
            $appData->expiry_date_of_op = $wpeData->expiry_date_of_op;

            // Desired duration for work permit
            $appData->duration_start_date = $wpeData->duration_start_date;
            $appData->duration_end_date = $wpeData->duration_end_date;
            $appData->desired_duration = $wpeData->desired_duration;
            $appData->duration_amount = $wpeData->duration_amount;

            // Approved Permission Period
            $appData->approved_duration_start_date = $wpeData->approved_duration_start_date;
            $appData->approved_duration_end_date = $wpeData->approved_duration_end_date;
            $appData->approved_desired_duration = $wpeData->approved_desired_duration;
            $appData->approved_duration_amount = $wpeData->approved_duration_amount;

            // Reference approved work permit new tracking number and date
            $appData->ref_app_tracking_no =$wpeData->ref_app_tracking_no;
            $appData->ref_app_approve_date = $wpeData->ref_app_approve_date;

            // Others Particular of Organization
            $appData->nature_of_business = $wpeData->nature_of_business;
            $appData->received_remittance = $wpeData->received_remittance;
            $appData->auth_capital = $wpeData->auth_capital;
            $appData->paid_capital = $wpeData->paid_capital;

            // Contact address of the expatriate in Bangladesh
            $appData->ex_office_division_id = $wpeData->ex_office_division_id;
            $appData->ex_office_district_id =$wpeData->ex_office_district_id;
            $appData->ex_office_thana_id = $wpeData->ex_office_thana_id;
            $appData->ex_office_post_office = $wpeData->ex_office_post_office;
            $appData->ex_office_post_code = $wpeData->ex_office_post_code;
            $appData->ex_office_address = $wpeData->ex_office_address;
            $appData->ex_office_telephone_no = $wpeData->ex_office_telephone_no;
            $appData->ex_office_mobile_no = $wpeData->ex_office_mobile_no;
            $appData->ex_office_fax_no = $wpeData->ex_office_fax_no;
            $appData->ex_office_email = $wpeData->ex_office_email;

            // Passport Information
            $appData->emp_passport_no = $wpeData->emp_passport_no;
            $appData->emp_personal_no = $wpeData->emp_personal_no;
            $appData->emp_surname = $wpeData->emp_surname;
            $appData->emp_name = $wpeData->emp_name;
            $appData->emp_designation = $wpeData->emp_designation;
            $appData->brief_job_description = $wpeData->brief_job_description;
            // $appData->major_activities = $wpnData->major_activities;
            $appData->emp_given_name = $wpeData->emp_given_name;
            $appData->emp_nationality_id = $wpeData->emp_nationality_id;
            $appData->emp_date_of_birth = $wpeData->emp_date_of_birth;
            $appData->emp_place_of_birth = $wpeData->emp_place_of_birth;
            $appData->pass_issue_date = $wpeData->pass_issue_date;
            $appData->pass_expiry_date = $wpeData->pass_expiry_date;
            $appData->place_of_issue = $wpeData->place_of_issue;

            // Previous Travel history of the expatriate to Bangladesh
            $appData->travel_history = $wpeData->travel_history;
            $appData->th_visit_with_emp_visa = $wpeData->th_visit_with_emp_visa;
            $appData->th_emp_work_permit = $wpeData->th_emp_work_permit;
            $appData->th_emp_tin_no = $wpeData->th_emp_tin_no;
            $appData->th_emp_wp_no = $wpeData->th_emp_wp_no;
            $appData->th_emp_org_name = $wpeData->th_emp_org_name;
            $appData->th_emp_org_address = $wpeData->th_emp_org_address;
            $appData->th_org_district_id = $wpeData->th_org_district_id;
            $appData->th_org_thana_id = $wpeData->th_org_thana_id;
            $appData->th_org_post_office = $wpeData->th_org_post_office;
            $appData->th_org_post_code = $wpeData->th_org_post_code;
            $appData->th_org_telephone_no = $wpeData->th_org_telephone_no;
            $appData->th_org_email = $wpeData->th_org_email;
            $appData->th_first_work_permit = $wpeData->th_first_work_permit;
            $appData->th_resignation_letter = $wpeData->th_resignation_letter;
            $appData->th_release_order = $wpeData->th_release_order;
            $appData->th_last_extension = $wpeData->th_last_extension;
            $appData->th_last_work_permit = $wpeData->th_last_work_permit;
            $appData->th_income_tax = $wpeData->th_income_tax;

            // Manpower section
            $appData->local_executive = $wpeData->local_executive;
            $appData->local_stuff = $wpeData->local_stuff;
            $appData->local_total = $wpeData->local_total;
            $appData->foreign_executive = $wpeData->foreign_executive;
            $appData->foreign_stuff = $wpeData->foreign_stuff;
            $appData->foreign_total = $wpeData->foreign_total;
            $appData->manpower_total = $wpeData->manpower_total;
            $appData->manpower_local_ratio = $wpeData->manpower_local_ratio;
            $appData->manpower_foreign_ratio = $wpeData->manpower_foreign_ratio;

            // Compensation and Benefit
            $appData->basic_payment_type_id = $wpeData->basic_payment_type_id;
            $appData->basic_local_amount = $wpeData->basic_local_amount;
            $appData->basic_local_currency_id = $wpeData->basic_local_currency_id;

            $appData->overseas_payment_type_id = $wpeData->overseas_payment_type_id;
            $appData->overseas_local_amount = $wpeData->overseas_local_amount;
            $appData->overseas_local_currency_id = $wpeData->overseas_local_currency_id;

            $appData->house_payment_type_id = $wpeData->house_payment_type_id;
            $appData->house_local_amount = $wpeData->house_local_amount;
            $appData->house_local_currency_id = $wpeData->house_local_currency_id;

            $appData->conveyance_payment_type_id = $wpeData->conveyance_payment_type_id;
            $appData->conveyance_local_amount = $wpeData->conveyance_local_amount;
            $appData->conveyance_local_currency_id = $wpeData->conveyance_local_currency_id;

            $appData->medical_payment_type_id = $wpeData->medical_payment_type_id;
            $appData->medical_local_amount = $wpeData->medical_local_amount;
            $appData->medical_local_currency_id = $wpeData->medical_local_currency_id;

            $appData->ent_payment_type_id = $wpeData->ent_payment_type_id;
            $appData->ent_local_amount = $wpeData->ent_local_amount;
            $appData->ent_local_currency_id = $wpeData->ent_local_currency_id;

            $appData->bonus_payment_type_id = $wpeData->bonus_payment_type_id;
            $appData->bonus_local_amount = $wpeData->bonus_local_amount;
            $appData->bonus_local_currency_id = $wpeData->bonus_local_currency_id;
            $appData->other_benefits = $wpeData->other_benefits;

            // Authorized Person Information
            $appData->auth_full_name = $wpeData->auth_full_name;
            $appData->auth_designation = $wpeData->auth_designation;
            $appData->auth_mobile_no = $wpeData->auth_mobile_no;
            $appData->auth_email = $wpeData->auth_email;
            $appData->auth_image = $wpeData->auth_image;
            $appData->accept_terms = $wpeData->accept_terms;

            // Payment information
            $appData->sf_payment_id = $wpeData->sf_payment_id;
            $appData->gf_payment_id = $wpeData->gf_payment_id;

            // Application approved data
            $appData->approved_date = $wpeData->approved_date;

            $appData->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            //dd($e->getMessage(), $e->getLine(), $e->getFile());
            DB::rollback();
            return false;
        }
    }

    public static function wpaDataStore($tracking_no, $ref_id)
    {
        try {
            DB::beginTransaction();
            $wpaData = WorkPermitAmendment::where('id', $ref_id)->first();

            $ref_service_name = UtilFunction::getRefAppServiceName($wpaData->ref_app_tracking_no);

            if (!empty($ref_service_name)) {
                $appData = WPCommonPool::firstOrNew([$ref_service_name => $wpaData->ref_app_tracking_no]);
            } else {
                $appData = new WPCommonPool();
            }

            // WPN or WPE tracking number
            $appData->wpa_tracking_no = $tracking_no;

            // Reference approved WPN or WPE tracking number and date
            $appData->ref_app_tracking_no = $wpaData->ref_app_tracking_no;
            $appData->ref_app_approve_date = $wpaData->ref_app_approve_date;

            // Desired duration for work permit
            if (!empty($wpaData->n_duration_start_date)){
                $appData->duration_start_date = $wpaData->n_duration_start_date;
            }
            $appData->approved_duration_start_date = $wpaData->approved_duration_start_date;
            if (!empty($wpaData->n_duration_end_date)){
                $appData->duration_end_date = $wpaData->n_duration_end_date;
            }
            $appData->approved_duration_end_date = $wpaData->approved_duration_end_date;
            if ($wpaData->n_desired_duration){
                $appData->desired_duration = $wpaData->n_desired_duration;
            }
            $appData->approved_desired_duration = $wpaData->approved_desired_duration;
            if (!empty($wpaData->n_desired_amount)){
                $appData->duration_amount = $wpaData->n_desired_amount;
            }

            $appData->approved_duration_amount = $wpaData->approved_duration_amount;


            // Company Information
            $appData->company_name = $wpaData->company_name;
            $appData->company_name_bn = $wpaData->company_name_bn;
            $appData->service_type = $wpaData->service_type;
            $appData->reg_commercial_office = $wpaData->reg_commercial_office;
            $appData->ownership_status_id = $wpaData->ownership_status_id;
            $appData->organization_type_id = $wpaData->organization_type_id;
            $appData->major_activities = $wpaData->major_activities;

            // Information of Principal Promoter/ Chairman/ Managing Director/ State CEO
            $appData->ceo_country_id = $wpaData->ceo_country_id;
            $appData->ceo_dob = $wpaData->ceo_dob;
            $appData->ceo_passport_no = $wpaData->ceo_passport_no;
            $appData->ceo_nid = $wpaData->ceo_nid;
            $appData->ceo_full_name = $wpaData->ceo_full_name;
            $appData->ceo_designation = $wpaData->ceo_designation;
            $appData->ceo_district_id = $wpaData->ceo_district_id;
            $appData->ceo_city = $wpaData->ceo_city;
            $appData->ceo_state = $wpaData->ceo_state;
            $appData->ceo_thana_id = $wpaData->ceo_thana_id;
            $appData->ceo_post_code = $wpaData->ceo_post_code;
            $appData->ceo_address = $wpaData->ceo_address;
            $appData->ceo_telephone_no = $wpaData->ceo_telephone_no;
            $appData->ceo_mobile_no = $wpaData->ceo_mobile_no;
            $appData->ceo_fax_no = $wpaData->ceo_fax_no;
            $appData->ceo_email = $wpaData->ceo_email;
            $appData->ceo_father_name = $wpaData->ceo_father_name;
            $appData->ceo_mother_name = $wpaData->ceo_mother_name;
            $appData->ceo_spouse_name = $wpaData->ceo_spouse_name;
            $appData->ceo_gender = $wpaData->ceo_gender;
            
//            if (!empty($wpaData->n_ceo_full_name)){
//                $appData->ceo_full_name = $wpaData->n_ceo_full_name;
//            }
//            if (!empty($wpaData->n_ceo_dob)){
//                $appData->ceo_dob = $wpaData->n_ceo_dob;
//            }
//            if (!empty($wpaData->n_ceo_spouse_name)){
//                $appData->ceo_spouse_name = $wpaData->n_ceo_spouse_name;
//            }
//            if (!empty($wpaData->n_ceo_designation)){
//                $appData->ceo_designation = $wpaData->n_ceo_designation;
//            }
//            if (!empty($wpaData->n_ceo_country_id)){
//                $appData->ceo_country_id = $wpaData->n_ceo_country_id;
//            }
//            if (!empty($wpaData->n_ceo_district_id)){
//                $appData->ceo_district_id = $wpaData->n_ceo_district_id;
//            }
//            if (!empty($wpaData->n_ceo_thana_id)){
//                $appData->ceo_thana_id = $wpaData->n_ceo_thana_id;
//            }
//            if (!empty($wpaData->n_ceo_post_code)){
//                $appData->ceo_post_code = $wpaData->n_ceo_post_code;
//            }
//            if (!empty($wpaData->n_ceo_address)){
//                $appData->ceo_address = $wpaData->n_ceo_address;
//            }
//            if (!empty($wpaData->n_ceo_telephone_no)){
//                $appData->ceo_telephone_no = $wpaData->n_ceo_telephone_no;
//            }
//            if (!empty($wpaData->n_ceo_mobile_no)){
//                $appData->ceo_mobile_no = $wpaData->n_ceo_mobile_no;
//            }
//            if (!empty($wpaData->n_ceo_fax_no)){
//                $appData->ceo_fax_no = $wpaData->n_ceo_fax_no;
//            }
//            if (!empty($wpaData->n_ceo_email)){
//                $appData->ceo_email = $wpaData->n_ceo_email;
//            }
//            if (!empty($wpaData->n_ceo_father_name)){
//                $appData->ceo_father_name = $wpaData->n_ceo_father_name;
//            }
//            if (!empty($wpaData->n_ceo_mother_name)){
//                $appData->ceo_mother_name = $wpaData->n_ceo_mother_name;
//            }
//            if (!empty($wpaData->n_ceo_nid)){
//                $appData->ceo_nid = $wpaData->n_ceo_nid;
//            }
//            if (!empty($wpaData->n_ceo_passport_no)){
//                $appData->ceo_passport_no = $wpaData->n_ceo_passport_no;
//            }
//            if (!empty($wpaData->n_ceo_city)){
//                $appData->ceo_city = $wpaData->n_ceo_city;
//            }
//            if (!empty($wpaData->n_ceo_state)){
//                $appData->ceo_state = $wpaData->n_ceo_state;
//            }
//            if (!empty($wpaData->n_ceo_gender)){
//                $appData->ceo_gender = $wpaData->n_ceo_gender;
//            }

            // Office Address
            if (!empty($wpaData->n_office_division_id)){
                $appData->office_division_id = $wpaData->n_office_division_id;
            }
            if (!empty($wpaData->n_office_district_id)){
                $appData->office_district_id = $wpaData->n_office_district_id;
            }
            if (!empty($wpaData->n_office_thana_id)){
                $appData->office_thana_id = $wpaData->n_office_thana_id;
            }
            if (!empty($wpaData->n_office_post_office)){
                $appData->office_post_office = $wpaData->n_office_post_office;
            }
            if (!empty($wpaData->n_office_post_code)){
                $appData->office_post_code = $wpaData->n_office_post_code;
            }
            if (!empty($wpaData->n_office_address)){
                $appData->office_address = $wpaData->n_office_address;
            }
            if (!empty($wpaData->n_office_telephone_no)){
                $appData->office_telephone_no = $wpaData->n_office_telephone_no;
            }
            if (!empty($wpaData->n_office_mobile_no)){
                $appData->office_mobile_no = $wpaData->n_office_mobile_no;
            }
            if (!empty($wpaData->n_office_fax_no)){
                $appData->office_fax_no = $wpaData->n_office_fax_no;
            }
            if (!empty($wpaData->n_office_email)){
                $appData->office_email = $wpaData->n_office_email;
            }

            // Factory Address
            $appData->factory_district_id = $wpaData->factory_district_id;
            $appData->factory_thana_id = $wpaData->factory_thana_id;
            $appData->factory_post_office = $wpaData->factory_post_office;
            $appData->factory_post_code = $wpaData->factory_post_code;
            $appData->factory_address = $wpaData->factory_address;
            $appData->factory_telephone_no = $wpaData->factory_telephone_no;
            $appData->factory_mobile_no = $wpaData->factory_mobile_no;
            $appData->factory_fax_no = $wpaData->factory_fax_no;
            $appData->factory_email = $wpaData->factory_email;
            $appData->factory_mouja = $wpaData->factory_mouja;

//            if (!empty($wpaData->n_factory_district_id)){
//                $appData->factory_district_id = $wpaData->n_factory_district_id;
//            }
//            if (!empty($wpaData->n_factory_thana_id)){
//                $appData->factory_thana_id = $wpaData->n_factory_thana_id;
//            }
//            if (!empty($wpaData->n_factory_post_office)){
//                $appData->factory_post_office = $wpaData->n_factory_post_office;
//            }
//            if (!empty($wpaData->n_factory_post_code)){
//                $appData->factory_post_code = $wpaData->n_factory_post_code;
//            }
//            if (!empty($wpaData->n_factory_address)){
//                $appData->factory_address = $wpaData->n_factory_address;
//            }
//            if (!empty($wpaData->n_factory_telephone_no)){
//                $appData->factory_telephone_no = $wpaData->n_factory_telephone_no;
//            }
//            if (!empty($wpaData->n_factory_mobile_no)){
//                $appData->factory_mobile_no = $wpaData->n_factory_mobile_no;
//            }
//            if (!empty($wpaData->n_factory_fax_no)){
//                $appData->factory_fax_no = $wpaData->n_factory_fax_no;
//            }

            // Passport Information
            if (!empty($wpaData->n_emp_name)){
                $appData->emp_name = $wpaData->n_emp_name;
            }
            if (!empty($wpaData->n_emp_designation)){
                $appData->emp_designation = $wpaData->n_emp_designation;
            }
            if (!empty($wpaData->n_emp_nationality_id)){
                $appData->emp_nationality_id = $wpaData->n_emp_nationality_id;
            }
            if (!empty($wpaData->n_emp_passport_no)){
                $appData->emp_passport_no = $wpaData->n_emp_passport_no;
            }

            // Effective date of Compensation and Benefit
            $appData->effective_date = $wpaData->effective_date;

            // Compensation and Benefit
            if (!empty($wpaData->n_basic_payment_type_id)){
                $appData->basic_payment_type_id = $wpaData->n_basic_payment_type_id;
            }
            if (!empty($wpaData->n_basic_local_amount)){
                $appData->basic_local_amount = $wpaData->n_basic_local_amount;
            }
            if (!empty($wpaData->n_basic_local_currency_id)){
                $appData->basic_local_currency_id = $wpaData->n_basic_local_currency_id;
            }
            if (!empty($wpaData->n_overseas_payment_type_id)){
                $appData->overseas_payment_type_id = $wpaData->n_overseas_payment_type_id;
            }
            if (!empty($wpaData->n_overseas_local_amount)){
                $appData->overseas_local_amount = $wpaData->n_overseas_local_amount;
            }
            if (!empty($wpaData->n_overseas_local_currency_id)){
                $appData->overseas_local_currency_id = $wpaData->n_overseas_local_currency_id;
            }
            if (!empty($wpaData->n_house_payment_type_id)){
                $appData->house_payment_type_id = $wpaData->n_house_payment_type_id;
            }
            if (!empty($wpaData->n_house_local_amount)){
                $appData->house_local_amount = $wpaData->n_house_local_amount;
            }
            if (!empty($wpaData->n_house_local_currency_id)){
                $appData->house_local_currency_id = $wpaData->n_house_local_currency_id;
            }
            if (!empty($wpaData->n_conveyance_payment_type_id)){
                $appData->conveyance_payment_type_id = $wpaData->n_conveyance_payment_type_id;
            }
            if (!empty($wpaData->n_conveyance_local_amount)){
                $appData->conveyance_local_amount = $wpaData->n_conveyance_local_amount;
            }
            if (!empty($wpaData->n_conveyance_local_currency_id)){
                $appData->conveyance_local_currency_id = $wpaData->n_conveyance_local_currency_id;
            }
            if (!empty($wpaData->n_medical_payment_type_id)){
                $appData->medical_payment_type_id = $wpaData->n_medical_payment_type_id;
            }
            if (!empty($wpaData->n_medical_local_amount)){
                $appData->medical_local_amount = $wpaData->n_medical_local_amount;
            }
            if (!empty($wpaData->n_medical_local_currency_id)){
                $appData->medical_local_currency_id = $wpaData->n_medical_local_currency_id;
            }
            if (!empty($wpaData->n_ent_payment_type_id)){
                $appData->ent_payment_type_id = $wpaData->n_ent_payment_type_id;
            }
            if (!empty($wpaData->n_ent_local_amount)){
                $appData->ent_local_amount = $wpaData->n_ent_local_amount;
            }
            if (!empty($wpaData->n_ent_local_currency_id)){
                $appData->ent_local_currency_id = $wpaData->n_ent_local_currency_id;
            }
            if (!empty($wpaData->n_bonus_payment_type_id)){
                $appData->bonus_payment_type_id = $wpaData->n_bonus_payment_type_id;
            }
            if (!empty($wpaData->n_bonus_local_amount)){
                $appData->bonus_local_amount = $wpaData->n_bonus_local_amount;
            }
            if (!empty($wpaData->n_bonus_local_currency_id)){
                $appData->bonus_local_currency_id = $wpaData->n_bonus_local_currency_id;
            }
            if (!empty($wpaData->n_other_benefits)){
                $appData->other_benefits = $wpaData->n_other_benefits;
            }

            // Conditionally approve information
            $appData->conditional_approved_file = $wpaData->conditional_approved_file;
            $appData->conditional_approved_remarks = $wpaData->conditional_approved_remarks;

            $appData->basic_salary = $wpaData->basic_salary;

            // Authorized Person Information
            $appData->auth_full_name = $wpaData->auth_full_name;
            $appData->auth_designation = $wpaData->auth_designation;
            $appData->auth_mobile_no = $wpaData->auth_mobile_no;
            $appData->auth_email = $wpaData->auth_email;
            $appData->auth_image = $wpaData->auth_image;
            $appData->accept_terms = $wpaData->accept_terms;

            // Payment information
            $appData->sf_payment_id = $wpaData->sf_payment_id;
            $appData->gf_payment_id = $wpaData->gf_payment_id;

            // Application approved data
            $appData->approved_date = $wpaData->approved_date;

            //$appData->certificate_link = $wpaData->certificate_link;

            $appData->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            //dd($e->getMessage(), $e->getLine(), $e->getFile());
            DB::rollback();
            return false;
        }
    }

    public static function wpcDataStore($tracking_no, $ref_id)
    {
        try {
            DB::beginTransaction();
            $wpcData = WorkPermitCancellation::where('id', $ref_id)->first();

            $ref_service_name = UtilFunction::getRefAppServiceName($wpcData->ref_app_tracking_no);

            if (!empty($ref_service_name)) {
                $appData = WPCommonPool::firstOrNew([$ref_service_name => $wpcData->ref_app_tracking_no]);
            } else {
                $appData = new WPCommonPool();
            }

            $appData->wpc_tracking_no = $tracking_no;
            $appData->ref_app_tracking_no = $wpcData->ref_app_tracking_no;
            $appData->ref_app_approve_date = $wpcData->ref_app_approve_date;
            $appData->expiry_date_of_op = $wpcData->expiry_date_of_op;

            $appData->date_of_cancellation = $wpcData->date_of_cancellation;
            $appData->applicant_remarks = $wpcData->applicant_remarks;
            $appData->approved_effect_date = $wpcData->approved_effect_date;

            // Company Information
            $appData->company_name = $wpcData->company_name;
            $appData->company_name_bn = $wpcData->company_name_bn;
            $appData->service_type = $wpcData->service_type;
            $appData->reg_commercial_office = $wpcData->reg_commercial_office;
            $appData->ownership_status_id = $wpcData->ownership_status_id;
            $appData->organization_type_id = $wpcData->organization_type_id;
            $appData->major_activities = $wpcData->major_activities;

            // Information of Principal Promoter/ Chairman/ Managing Director/ State CEO
            $appData->ceo_country_id = $wpcData->ceo_country_id;
            $appData->ceo_dob = $wpcData->ceo_dob;
            $appData->ceo_passport_no = $wpcData->ceo_passport_no;
            $appData->ceo_nid = $wpcData->ceo_nid;
            $appData->ceo_full_name = $wpcData->ceo_full_name;
            $appData->ceo_designation = $wpcData->ceo_designation;
            $appData->ceo_district_id = $wpcData->ceo_district_id;
            $appData->ceo_city = $wpcData->ceo_city;
            $appData->ceo_state = $wpcData->ceo_state;
            $appData->ceo_thana_id = $wpcData->ceo_thana_id;
            $appData->ceo_post_code = $wpcData->ceo_post_code;
            $appData->ceo_address = $wpcData->ceo_address;
            $appData->ceo_telephone_no = $wpcData->ceo_telephone_no;
            $appData->ceo_mobile_no = $wpcData->ceo_mobile_no;
            $appData->ceo_fax_no = $wpcData->ceo_fax_no;
            $appData->ceo_email = $wpcData->ceo_email;
            $appData->ceo_father_name = $wpcData->ceo_father_name;
            $appData->ceo_mother_name = $wpcData->ceo_mother_name;
            $appData->ceo_spouse_name = $wpcData->ceo_spouse_name;
            $appData->ceo_gender = $wpcData->ceo_gender;

            // Office Address
            $appData->office_division_id = $wpcData->office_division_id;
            $appData->office_district_id = $wpcData->office_district_id;
            $appData->office_thana_id = $wpcData->office_thana_id;
            $appData->office_post_office = $wpcData->office_post_office;
            $appData->office_post_code = $wpcData->office_post_code;
            $appData->office_address = $wpcData->office_address;
            $appData->office_telephone_no = $wpcData->office_telephone_no;
            $appData->office_mobile_no = $wpcData->office_mobile_no;
            $appData->office_fax_no = $wpcData->office_fax_no;
            $appData->office_email = $wpcData->office_email;

            // Factory Address
            $appData->factory_district_id = $wpcData->factory_district_id;
            $appData->factory_thana_id = $wpcData->factory_thana_id;
            $appData->factory_post_office = $wpcData->factory_post_office;
            $appData->factory_post_code = $wpcData->factory_post_code;
            $appData->factory_address = $wpcData->factory_address;
            $appData->factory_telephone_no = $wpcData->factory_telephone_no;
            $appData->factory_mobile_no = $wpcData->factory_mobile_no;
            $appData->factory_fax_no = $wpcData->factory_fax_no;
            $appData->factory_email = $wpcData->factory_email;
            $appData->factory_mouja = $wpcData->factory_mouja;


            $appData->basic_payment_type_id = $wpcData->basic_payment_type_id;
            $appData->basic_local_amount = $wpcData->basic_local_amount;
            $appData->basic_local_currency_id = $wpcData->basic_local_currency_id;
            $appData->overseas_payment_type_id = $wpcData->overseas_payment_type_id;
            $appData->overseas_local_amount = $wpcData->overseas_local_amount;
            $appData->overseas_local_currency_id = $wpcData->overseas_local_currency_id;
            $appData->house_payment_type_id = $wpcData->house_payment_type_id;
            $appData->house_local_amount = $wpcData->house_local_amount;
            $appData->house_local_currency_id = $wpcData->house_local_currency_id;
            $appData->conveyance_payment_type_id = $wpcData->conveyance_payment_type_id;
            $appData->conveyance_local_amount = $wpcData->conveyance_local_amount;
            $appData->conveyance_local_currency_id = $wpcData->conveyance_local_currency_id;
            $appData->medical_payment_type_id = $wpcData->medical_payment_type_id;
            $appData->medical_local_amount = $wpcData->medical_local_amount;
            $appData->medical_local_currency_id = $wpcData->medical_local_currency_id;
            $appData->ent_payment_type_id = $wpcData->ent_payment_type_id;
            $appData->ent_local_amount = $wpcData->ent_local_amount;
            $appData->ent_local_currency_id = $wpcData->ent_local_currency_id;
            $appData->bonus_payment_type_id = $wpcData->bonus_payment_type_id;
            $appData->bonus_local_amount = $wpcData->bonus_local_amount;
            $appData->bonus_local_currency_id = $wpcData->bonus_local_currency_id;
            $appData->other_benefits = $wpcData->other_benefits;

            //BD address
            $appData->ex_office_division_id = $wpcData->ex_office_division_id;
            $appData->ex_office_district_id = $wpcData->ex_office_district_id;
            $appData->ex_office_thana_id = $wpcData->ex_office_thana_id;
            $appData->ex_office_post_office = $wpcData->ex_office_post_office;
            $appData->ex_office_post_code = $wpcData->ex_office_post_code;
            $appData->ex_office_address = $wpcData->ex_office_address;
            $appData->ex_office_telephone_no = $wpcData->ex_office_telephone_no;
            $appData->ex_office_mobile_no = $wpcData->ex_office_mobile_no;
            $appData->ex_office_fax_no = $wpcData->ex_office_fax_no;
            $appData->ex_office_email = $wpcData->ex_office_email;

            //Authorized Person Information
            $appData->auth_full_name = $wpcData->auth_full_name;
            $appData->auth_designation = $wpcData->auth_designation;
            $appData->auth_mobile_no = $wpcData->auth_mobile_no;
            $appData->auth_email = $wpcData->auth_email;
            $appData->auth_image = $wpcData->auth_image;
            $appData->accept_terms = $wpcData->accept_terms;

            //Payment
            $appData->sf_payment_id = $wpcData->sf_payment_id;
            $appData->gf_payment_id = $wpcData->gf_payment_id;
            $appData->approved_date = $wpcData->approved_date;
            $appData->certificate_link = $wpcData->certificate_link;

            $appData->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            //dd($e->getMessage(), $e->getLine(), $e->getFile());
            DB::rollback();
            return false;
        }
    }

}