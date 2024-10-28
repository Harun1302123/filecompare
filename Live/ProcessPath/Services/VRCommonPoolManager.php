<?php


namespace App\Modules\ProcessPath\Services;

use App\Libraries\UtilFunction;
use App\Modules\VisaRecommendation\Models\VisaRecommendation;
use App\Modules\VisaRecommendationAmendment\Models\VisaRecommendationAmendment;
use App\VRCommonPool;
use Illuminate\Support\Facades\DB;

class VRCommonPoolManager
{
    public static function VRDataStore($tracking_no, $ref_id)
    {
        try {
            DB::beginTransaction();

            //fetch vr_apps data according to ref_id
            $vrData = VisaRecommendation::where('id', $ref_id)->first();

            $appData = new VRCommonPool();

            $appData->vr_tracking_no = $tracking_no;
            ///$appData->certificate_link = $vrData->certificate_link;
            $appData->app_type_id = $vrData->app_type_id;
            //business category
            $appData->business_category = $vrData->business_category;
            //$appData->app_type_mapping_id = $vrData->app_type_mapping_id;

            //company information
            $appData->company_name = $vrData->company_name;
            $appData->company_name_bn = $vrData->company_name_bn;
            $appData->service_type = $vrData->service_type;
            $appData->reg_commercial_office = $vrData->reg_commercial_office;
            $appData->ownership_status_id = $vrData->ownership_status_id;
            $appData->organization_type_id = $vrData->organization_type_id;

            if ($vrData->organization_type_id == 14) {
                $appData->organization_type_other = $vrData->organization_type_other;
            }

            $appData->major_activities = $vrData->major_activities;

            //CEO information
            $appData->ceo_country_id = $vrData->ceo_country_id;
            $appData->ceo_dob = $vrData->ceo_dob;
            $appData->ceo_passport_no = $vrData->ceo_passport_no;
            $appData->ceo_nid = $vrData->ceo_nid;
            $appData->ceo_full_name = $vrData->ceo_full_name;
            $appData->ceo_designation = $vrData->ceo_designation;
            $appData->ceo_district_id = $vrData->ceo_district_id;
            $appData->ceo_city = $vrData->ceo_city;
            $appData->ceo_state = $vrData->ceo_state;
            $appData->ceo_thana_id = $vrData->ceo_thana_id;
            $appData->ceo_post_code = $vrData->ceo_post_code;
            $appData->ceo_address = $vrData->ceo_address;
            $appData->ceo_telephone_no = $vrData->ceo_telephone_no;
            $appData->ceo_mobile_no = $vrData->ceo_mobile_no;
            $appData->ceo_fax_no = $vrData->ceo_fax_no;
            $appData->ceo_email = $vrData->ceo_email;
            $appData->ceo_father_name = $vrData->ceo_father_name;
            $appData->ceo_mother_name = $vrData->ceo_mother_name;
            $appData->ceo_spouse_name = $vrData->ceo_spouse_name;
            $appData->ceo_gender = $vrData->ceo_gender;
            $appData->ceo_auth_letter = $vrData->ceo_auth_letter;

            // Office Address
            $appData->office_division_id = $vrData->office_division_id;
            $appData->office_district_id = $vrData->office_district_id;
            $appData->office_thana_id = $vrData->office_thana_id;
            $appData->office_post_office = $vrData->office_post_office;
            $appData->office_post_code = $vrData->office_post_code;
            $appData->office_address = $vrData->office_address;
            $appData->office_telephone_no = $vrData->office_telephone_no;
            $appData->office_mobile_no = $vrData->office_mobile_no;
            $appData->office_fax_no = $vrData->office_fax_no;
            $appData->office_email = $vrData->office_email;

            // Factory Address
            $appData->factory_district_id = $vrData->factory_district_id;
            $appData->factory_thana_id = $vrData->factory_thana_id;
            $appData->factory_post_office = $vrData->factory_post_office;
            $appData->factory_post_code = $vrData->factory_post_code;
            $appData->factory_address = $vrData->factory_address;
            $appData->factory_telephone_no = $vrData->factory_telephone_no;
            $appData->factory_mobile_no = $vrData->factory_mobile_no;
            $appData->factory_fax_no = $vrData->factory_fax_no;
            $appData->factory_email = $vrData->factory_email;
            $appData->factory_mouja = $vrData->factory_mouja;

            //Spouse Information
            $appData->emp_marital_status = $vrData->emp_marital_status;
            $appData->emp_spouse_name = $vrData->emp_spouse_name;
            $appData->emp_spouse_passport_no = $vrData->emp_spouse_passport_no;
            $appData->emp_spouse_nationality = $vrData->emp_spouse_nationality;
            $appData->emp_spouse_work_status = $vrData->emp_spouse_work_status;
            $appData->emp_spouse_org_name = $vrData->emp_spouse_org_name;

            $appData->mission_country_id = $vrData->mission_country_id;
            $appData->high_commision_id = $vrData->high_commision_id;

            //General Information
            $appData->emp_name = $vrData->emp_name;
            $appData->emp_designation = $vrData->emp_designation;
            $appData->brief_job_description = $vrData->brief_job_description;
            $appData->investor_photo = $vrData->investor_photo;

            //Passport Information
            $appData->emp_passport_no = $vrData->emp_passport_no;
            $appData->emp_personal_no = $vrData->emp_personal_no;
            $appData->emp_surname = $vrData->emp_surname;
            $appData->place_of_issue = $vrData->place_of_issue;
            $appData->emp_given_name = $vrData->emp_given_name;
            $appData->emp_nationality_id = $vrData->emp_nationality_id;
            $appData->emp_date_of_birth = $vrData->emp_date_of_birth;
            $appData->emp_place_of_birth = $vrData->emp_place_of_birth;
            $appData->pass_issue_date = $vrData->pass_issue_date;
            $appData->pass_expiry_date = $vrData->pass_expiry_date;

            //Compensation and Benefit
            $appData->basic_payment_type_id = $vrData->basic_payment_type_id;
            $appData->basic_local_amount = $vrData->basic_local_amount;
            $appData->basic_local_currency_id = $vrData->basic_local_currency_id;

            $appData->overseas_payment_type_id = $vrData->overseas_payment_type_id;
            $appData->overseas_local_amount = $vrData->overseas_local_amount;
            $appData->overseas_local_currency_id = $vrData->overseas_local_currency_id;

            $appData->house_payment_type_id = $vrData->house_payment_type_id;
            $appData->house_local_amount = $vrData->house_local_amount;
            $appData->house_local_currency_id = $vrData->house_local_currency_id;

            $appData->conveyance_payment_type_id = $vrData->conveyance_payment_type_id;
            $appData->conveyance_local_amount = $vrData->conveyance_local_amount;
            $appData->conveyance_local_currency_id = $vrData->conveyance_local_currency_id;

            $appData->medical_payment_type_id = $vrData->medical_payment_type_id;
            $appData->medical_local_amount = $vrData->medical_local_amount;
            $appData->medical_local_currency_id = $vrData->medical_local_currency_id;

            $appData->ent_payment_type_id = $vrData->ent_payment_type_id;
            $appData->ent_local_amount = $vrData->ent_local_amount;
            $appData->ent_local_currency_id = $vrData->ent_local_currency_id;

            $appData->bonus_payment_type_id = $vrData->bonus_payment_type_id;
            $appData->bonus_local_amount = $vrData->bonus_local_amount;
            $appData->bonus_local_currency_id = $vrData->bonus_local_currency_id;
            $appData->other_benefits = $vrData->other_benefits;

            //Contact address of the expatriate in Bangladesh
            $appData->ex_office_division_id = $vrData->ex_office_division_id;
            $appData->ex_office_district_id = $vrData->ex_office_district_id;
            $appData->ex_office_thana_id = $vrData->ex_office_thana_id;
            $appData->ex_office_post_office = $vrData->ex_office_post_office;
            $appData->ex_office_post_code = $vrData->ex_office_post_code;
            $appData->ex_office_address = $vrData->ex_office_address;
            $appData->ex_office_telephone_no = $vrData->ex_office_telephone_no;
            $appData->ex_office_mobile_no = $vrData->ex_office_mobile_no;
            $appData->ex_office_fax_no = $vrData->ex_office_fax_no;
            $appData->ex_office_email = $vrData->ex_office_email;

            //Others Particular of Organization(If Commercial)
            $appData->nature_of_business = $vrData->nature_of_business;
            $appData->received_remittance = $vrData->received_remittance;
            $appData->auth_capital = $vrData->auth_capital;
            $appData->paid_capital = $vrData->paid_capital;

            $appData->travel_history = $vrData->travel_history;
            $appData->th_visit_with_emp_visa = $vrData->th_visit_with_emp_visa;
            $appData->th_emp_work_permit = $vrData->th_emp_work_permit;

            $appData->th_emp_tin_no = $vrData->th_emp_tin_no;
            $appData->th_emp_wp_no = $vrData->th_emp_wp_no;
            $appData->th_emp_org_name = $vrData->th_emp_org_name;
            $appData->th_emp_org_address = $vrData->th_emp_org_address;
            $appData->th_org_district_id = $vrData->th_org_district_id;
            $appData->th_org_thana_id = $vrData->th_org_thana_id;
            $appData->th_org_post_office = $vrData->th_org_post_office;
            $appData->th_org_post_code = $vrData->th_org_post_code;
            $appData->th_org_telephone_no = $vrData->th_org_telephone_no;
            $appData->th_org_email = $vrData->th_org_email;
            $appData->th_first_work_permit = $vrData->th_first_work_permit;
            $appData->th_resignation_letter = $vrData->th_resignation_letter;
            $appData->th_release_order = $vrData->th_release_order;
            $appData->th_last_extension = $vrData->th_last_extension;
            $appData->th_last_work_permit = $vrData->th_last_work_permit;
            $appData->th_income_tax = $vrData->th_income_tax;

            //manpower section
            $appData->local_executive = $vrData->local_executive;
            $appData->local_stuff = $vrData->local_stuff;
            $appData->local_total = $vrData->local_total;
            $appData->foreign_executive = $vrData->foreign_executive;
            $appData->foreign_stuff = $vrData->foreign_stuff;
            $appData->foreign_total = $vrData->foreign_total;
            $appData->manpower_total = $vrData->manpower_total;
            $appData->manpower_local_ratio = $vrData->manpower_local_ratio;
            $appData->manpower_foreign_ratio = $vrData->manpower_foreign_ratio;

            //Airport Info
            $appData->airport_id = $vrData->airport_id;
            $appData->visa_purpose_id = $vrData->visa_purpose_id;
            $appData->visa_purpose_others = $vrData->visa_purpose_others;

            //Flight Details of the visiting expatriates
            $appData->arrival_date = $vrData->arrival_date;
            $appData->arrival_time = $vrData->arrival_time;
            $appData->arrival_flight_no = $vrData->arrival_flight_no;
            $appData->departure_date = $vrData->departure_date;
            $appData->departure_time = $vrData->departure_time;
            $appData->departure_flight_no = $vrData->departure_flight_no;

            $appData->visiting_service_id = $vrData->visiting_service_id;
            $appData->visa_on_arrival_sought_id = $vrData->visa_on_arrival_sought_id;
            $appData->visa_on_arrival_sought_other = $vrData->visa_on_arrival_sought_other;

            //Authorized Person Information
            $appData->auth_full_name = $vrData->auth_full_name;
            $appData->auth_designation = $vrData->auth_designation;
            $appData->auth_mobile_no = $vrData->auth_mobile_no;
            $appData->auth_email = $vrData->auth_email;
            $appData->auth_image = $vrData->auth_image;

            $appData->accept_terms = $vrData->accept_terms;
            $appData->sf_payment_id = $vrData->sf_payment_id;
            $appData->gf_payment_id = $vrData->gf_payment_id;
            $appData->approved_date = $vrData->approved_date;
            $appData->save();

            DB::commit();
            return true;
        }catch (\Exception $e){
            DB::rollback();
            return false;
        }
    }

    public static function VRADataStore($tracking_no, $ref_id)
    {
        try {
            DB::beginTransaction();
            $vraData = VisaRecommendationAmendment::where('id', $ref_id)->first();

            $ref_service_name = UtilFunction::getRefAppServiceName($vraData->ref_app_tracking_no);

            if (!empty($ref_service_name)) {
                $appData = VRCommonPool::firstOrNew([$ref_service_name => $vraData->ref_app_tracking_no]);
            } else {
                $appData = new VRCommonPool();
            }

            $appData->vra_tracking_no = $tracking_no;
            //$appData->certificate_link = $vraData->certificate_link;
            $appData->app_type_id = $vraData->app_type_id;
            //$appData->app_type_mapping_id = $vraData->app_type_mapping_id;

            //company information
            $appData->company_name = $vraData->company_name;
            $appData->company_name_bn = $vraData->company_name_bn;
            $appData->service_type = $vraData->service_type;
            $appData->reg_commercial_office = $vraData->reg_commercial_office;
            $appData->ownership_status_id = $vraData->ownership_status_id;
            $appData->organization_type_id = $vraData->organization_type_id;
            $appData->major_activities = $vraData->major_activities;

            //CEO information
            $appData->ceo_country_id = $vraData->ceo_country_id;
            $appData->ceo_dob = $vraData->ceo_dob;
            $appData->ceo_passport_no = $vraData->ceo_passport_no;
            $appData->ceo_nid = $vraData->ceo_nid;
            $appData->ceo_full_name = $vraData->ceo_full_name;
            $appData->ceo_designation = $vraData->ceo_designation;
            $appData->ceo_district_id = $vraData->ceo_district_id;
            $appData->ceo_city = $vraData->ceo_city;
            $appData->ceo_state = $vraData->ceo_state;
            $appData->ceo_thana_id = $vraData->ceo_thana_id;
            $appData->ceo_post_code = $vraData->ceo_post_code;
            $appData->ceo_address = $vraData->ceo_address;
            $appData->ceo_telephone_no = $vraData->ceo_telephone_no;
            $appData->ceo_mobile_no = $vraData->ceo_mobile_no;
            $appData->ceo_fax_no = $vraData->ceo_fax_no;
            $appData->ceo_email = $vraData->ceo_email;
            $appData->ceo_father_name = $vraData->ceo_father_name;
            $appData->ceo_mother_name = $vraData->ceo_mother_name;
            $appData->ceo_spouse_name = $vraData->ceo_spouse_name;
            $appData->ceo_gender = $vraData->ceo_gender;

            // Office Address
            $appData->office_division_id = $vraData->office_division_id;
            $appData->office_district_id = $vraData->office_district_id;
            $appData->office_thana_id = $vraData->office_thana_id;
            $appData->office_post_office = $vraData->office_post_office;
            $appData->office_post_code = $vraData->office_post_code;
            $appData->office_address = $vraData->office_address;
            $appData->office_telephone_no = $vraData->office_telephone_no;
            $appData->office_mobile_no = $vraData->office_mobile_no;
            $appData->office_fax_no = $vraData->office_fax_no;
            $appData->office_email = $vraData->office_email;

            // Office Address
            // if (!empty($vraData->n_office_division_id)){
            //     $appData->office_division_id = $vraData->n_office_division_id;
            // }
            // if (!empty($vraData->n_office_district_id)){
            //     $appData->office_district_id = $vraData->n_office_district_id;
            // }
            // if (!empty($vraData->n_office_thana_id)){
            //     $appData->office_thana_id = $vraData->n_office_thana_id;
            // }
            // if (!empty($vraData->n_office_post_office)){
            //     $appData->office_post_office = $vraData->n_office_post_office;
            // }
            // if (!empty($vraData->n_office_post_code)){
            //     $appData->office_post_code = $vraData->n_office_post_code;
            // }
            // if (!empty($vraData->n_office_address)){
            //     $appData->office_address = $vraData->n_office_address;
            // }
            // if (!empty($vraData->n_office_telephone_no)){
            //     $appData->office_telephone_no = $vraData->n_office_telephone_no;
            // }
            // if (!empty($vraData->n_office_mobile_no)){
            //     $appData->office_mobile_no = $vraData->n_office_mobile_no;
            // }
            // if (!empty($vraData->n_office_fax_no)){
            //     $appData->office_fax_no = $vraData->n_office_fax_no;
            // }
            // if (!empty($vraData->n_office_email)){
            //     $appData->office_email = $vraData->n_office_email;
            // }

            // Factory Address
            $appData->factory_district_id = $vraData->factory_district_id;
            $appData->factory_thana_id = $vraData->factory_thana_id;
            $appData->factory_post_office = $vraData->factory_post_office;
            $appData->factory_post_code = $vraData->factory_post_code;
            $appData->factory_address = $vraData->factory_address;
            $appData->factory_telephone_no = $vraData->factory_telephone_no;
            $appData->factory_mobile_no = $vraData->factory_mobile_no;
            $appData->factory_fax_no = $vraData->factory_fax_no;
            $appData->factory_email = $vraData->factory_email;
            $appData->factory_mouja = $vraData->factory_mouja;
            $appData->investor_photo = $vraData->investor_photo;

            // amendment information
            if (!empty($vraData->n_emp_name)) {
                $appData->emp_name = $vraData->n_emp_name;
            }

            if (!empty($vraData->n_emp_designation)) {
                $appData->emp_designation = $vraData->n_emp_designation;
            }

            if (!empty($vraData->n_emp_nationality_id)) {
                $appData->emp_nationality_id = $vraData->n_emp_nationality_id;
            }

            if (!empty($vraData->n_emp_passport_no)) {
                $appData->emp_passport_no = $vraData->n_emp_passport_no;
            }

            if (!empty($vraData->n_mission_country_id)) {
                $appData->mission_country_id = $vraData->n_mission_country_id;
            }

            if (!empty($vraData->n_high_commision_id)) {
                $appData->high_commision_id = $vraData->n_high_commision_id;
            }

            if (!empty($vraData->n_airport_id)) {
                $appData->airport_id = $vraData->n_airport_id;
            }

            if (!empty($vraData->n_visa_purpose_id)) {
                $appData->visa_purpose_id = $vraData->n_visa_purpose_id;
            }

            if (!empty($vraData->n_visa_purpose_others)) {
                $appData->visa_purpose_others = $vraData->n_visa_purpose_others;
            }

            if (!empty($vraData->n_departure_date)) {
                $appData->departure_date = $vraData->n_departure_date;
            }

            if (!empty($vraData->n_arrival_date)) {
                $appData->arrival_date = $vraData->n_arrival_date;
            }

            if (!empty($vraData->n_departure_time)) {
                $appData->departure_time = $vraData->n_departure_time;
            }

            if (!empty($vraData->n_arrival_time)) {
                $appData->arrival_time = $vraData->n_arrival_time;
            }

            if (!empty($vraData->n_arrival_flight_no)) {
                $appData->arrival_flight_no = $vraData->n_arrival_flight_no;
            }

            if (!empty($vraData->n_departure_flight_no)) {
                $appData->departure_flight_no = $vraData->n_departure_flight_no;
            }

            //Authorized Person Information
            $appData->auth_full_name = $vraData->auth_full_name;
            $appData->auth_designation = $vraData->auth_designation;
            $appData->auth_mobile_no = $vraData->auth_mobile_no;
            $appData->auth_email = $vraData->auth_email;
            $appData->auth_image = $vraData->auth_image;

            $appData->accept_terms = $vraData->accept_terms;
            $appData->sf_payment_id = $vraData->sf_payment_id;
            $appData->gf_payment_id = $vraData->gf_payment_id;
            $appData->approved_date = $vraData->approved_date;
            $appData->save();

            DB::commit();
            return true;
        }catch (\Exception $e){
            DB::rollback();
            return false;
        }

    }



}