<?php


namespace App\Modules\ProcessPath\Services;


use App\Libraries\CommonFunction;
use App\Libraries\UtilFunction;
use App\Modules\OfficePermissionAmendment\Models\OfficePermissionAmendment;
use App\Modules\OfficePermissionCancellation\Models\OfficePermissionCancellation;
use App\Modules\OfficePermissionExtension\Models\OfficePermissionExtension;
use App\Modules\OfficePermissionNew\Models\OfficePermissionNew;
use App\OPCommonPool;
use Illuminate\Support\Facades\Session;

class OPCommonPoolManager
{
    public static function OPNDataStore($tracking_no, $ref_id)
    {
        try {
            $opnData = OfficePermissionNew::where('id', $ref_id)->first();
            $appData = new OPCommonPool();
            $appData->opn_tracking_no = $tracking_no;
            //$appData->certificate_link = $opnData->certificate_link;

            $appData->approved_duration_start_date = $opnData->approved_duration_start_date;
            $appData->approved_duration_end_date = $opnData->approved_duration_end_date;
            $appData->approved_desired_duration = $opnData->approved_desired_duration;
            $appData->duration_amount = $opnData->duration_amount;

            $appData->approved_duration_amount = $opnData->approved_duration_amount;
            $appData->office_type = $opnData->office_type;

            $appData->c_company_name = $opnData->c_company_name;
            $appData->c_origin_country_id = $opnData->c_origin_country_id;
            $appData->c_country_id = $opnData->c_country_id;
            $appData->c_flat_apart_floor = $opnData->c_flat_apart_floor;
            $appData->c_house_plot_holding = $opnData->c_house_plot_holding;
            $appData->c_street = $opnData->c_street;
            $appData->c_post_zip_code = $opnData->c_post_zip_code;
            $appData->c_telephone = $opnData->c_telephone;
            $appData->c_city = $opnData->c_city;
            $appData->c_email = $opnData->c_email;
            $appData->c_fax = $opnData->c_fax;
            $appData->c_state_province = $opnData->c_state_province;
            $appData->c_org_type  = $opnData->c_org_type;
            $appData->c_major_activity_brief = $opnData->c_major_activity_brief;

            $appData->authorized_capital = $opnData->authorized_capital;
            $appData->paid_up_capital = $opnData->paid_up_capital;
            $appData->local_company_name = $opnData->local_company_name;
            $appData->local_company_name_bn = $opnData->local_company_name_bn;

            $appData->ex_office_division_id = $opnData->ex_office_division_id;
            $appData->ex_office_district_id = $opnData->ex_office_district_id;
            $appData->ex_office_thana_id = $opnData->ex_office_thana_id;
            $appData->ex_office_post_office = $opnData->ex_office_post_office;
            $appData->ex_office_post_code = $opnData->ex_office_post_code;
            $appData->ex_office_address = $opnData->ex_office_address;
            $appData->ex_office_telephone_no = $opnData->ex_office_telephone_no;
            $appData->ex_office_mobile_no = $opnData->ex_office_mobile_no;
            $appData->ex_office_fax_no = $opnData->ex_office_fax_no;
            $appData->ex_office_email = $opnData->ex_office_email;

            $appData->activities_in_bd = $opnData->activities_in_bd;
            $appData->first_commencement_date = $opnData->first_commencement_date;
            $appData->operation_target_date = $opnData->operation_target_date;

            $appData->period_start_date = $opnData->period_start_date;
            $appData->period_end_date = $opnData->period_end_date;
            $appData->period_validity = $opnData->period_validity;

            $appData->local_executive = $opnData->local_executive;
            $appData->local_stuff = $opnData->local_stuff;
            $appData->local_total = $opnData->local_total;
            $appData->foreign_executive = $opnData->foreign_executive;
            $appData->foreign_stuff = $opnData->foreign_stuff;
            $appData->foreign_total = $opnData->foreign_total;
            $appData->manpower_total = $opnData->manpower_total;
            $appData->manpower_local_ratio = $opnData->manpower_local_ratio;
            $appData->manpower_foreign_ratio = $opnData->manpower_foreign_ratio;

            $appData->est_initial_expenses = $opnData->est_initial_expenses;
            $appData->est_monthly_expenses = $opnData->est_monthly_expenses;

            $appData->company_name = $opnData->company_name;
            $appData->company_name_bn = $opnData->company_name_bn;

            $appData->service_type = $opnData->service_type;
            $appData->reg_commercial_office = $opnData->reg_commercial_office;
            $appData->ownership_status_id = $opnData->ownership_status_id;
            $appData->organization_type_id = $opnData->organization_type_id;

            $appData->ceo_full_name = $opnData->ceo_full_name;
            $appData->ceo_dob = $opnData->ceo_dob;
            $appData->ceo_spouse_name = $opnData->ceo_spouse_name;
            $appData->ceo_designation = $opnData->ceo_designation;
            $appData->ceo_country_id = $opnData->ceo_country_id;
            $appData->ceo_district_id = $opnData->ceo_district_id;
            $appData->ceo_thana_id = $opnData->ceo_thana_id;
            $appData->ceo_post_code = $opnData->ceo_post_code;
            $appData->ceo_city = $opnData->ceo_city;
            $appData->ceo_state = $opnData->ceo_state;
            $appData->ceo_address = $opnData->ceo_address;
            $appData->ceo_telephone_no = $opnData->ceo_telephone_no;
            $appData->ceo_mobile_no = $opnData->ceo_mobile_no;
            $appData->ceo_fax_no = $opnData->ceo_fax_no;
            $appData->ceo_email = $opnData->ceo_email;
            $appData->ceo_father_name = $opnData->ceo_father_name;
            $appData->ceo_mother_name = $opnData->ceo_mother_name;
            $appData->ceo_nid = $opnData->ceo_nid;
            $appData->ceo_passport_no = $opnData->ceo_passport_no;
            $appData->ceo_gender = $opnData->ceo_gender;

            $appData->office_division_id = $opnData->office_division_id;
            $appData->office_district_id = $opnData->office_district_id;
            $appData->office_thana_id = $opnData->office_thana_id;
            $appData->office_post_office = $opnData->office_post_office;
            $appData->office_post_code = $opnData->office_post_code;
            $appData->office_address = $opnData->office_address;
            $appData->office_telephone_no = $opnData->office_telephone_no;
            $appData->office_mobile_no = $opnData->office_mobile_no;
            $appData->office_fax_no = $opnData->office_fax_no;
            $appData->office_email = $opnData->office_email;

            $appData->factory_district_id = $opnData->factory_district_id;
            $appData->factory_district_id = $opnData->factory_district_id;
            $appData->factory_post_office = $opnData->factory_post_office;
            $appData->factory_post_code = $opnData->factory_post_code;
            $appData->factory_address = $opnData->factory_address;
            $appData->factory_telephone_no = $opnData->factory_telephone_no;
            $appData->factory_mobile_no = $opnData->factory_mobile_no;
            $appData->factory_fax_no = $opnData->factory_fax_no;
            $appData->factory_email = $opnData->factory_email;
            $appData->factory_mouja = $opnData->factory_mouja;

            $appData->auth_full_name = $opnData->auth_full_name;
            $appData->auth_designation = $opnData->auth_designation;
            $appData->auth_email = $opnData->auth_email;
            $appData->auth_mobile_no = $opnData->auth_mobile_no;
            $appData->auth_image = $opnData->auth_image;

            $appData->major_activities = $opnData->major_activities;
            //$appData->shadow_file_path = $opnData->shadow_file_path;
            $appData->gf_payment_id = $opnData->gf_payment_id;
            $appData->conditional_approved_file = $opnData->conditional_approved_file;
            $appData->conditional_approved_remarks = $opnData->conditional_approved_remarks;
            $appData->sf_payment_id = $opnData->sf_payment_id;
            $appData->accept_terms = $opnData->accept_terms;
            $appData->approved_date = $opnData->approved_date;
            //$appData->payment_date = $opnData->payment_date;
            $appData->save();

            return true;
        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage(), $e->getFile()) . '[OPNCP-1010]');
            return false;
        }
    }

    public static function OPEDataStore($tracking_no, $ref_id)
    {
        try {
            $opeData = OfficePermissionExtension::where('id', $ref_id)->first();
            
            // $appData = OPCommonPool::firstOrNew(['opn_tracking_no' => $opeData->ref_app_tracking_no]);
            if (empty($opeData->ref_app_tracking_no)) {
                $appData = new OPCommonPool();
            } else {
                $appData = OPCommonPool::firstOrNew(['opn_tracking_no' => $opeData->ref_app_tracking_no]);
            }

            $appData->ope_tracking_no = $tracking_no;
            //$appData->certificate_link = $opeData->certificate_link;

            $appData->approved_duration_start_date = $opeData->approved_duration_start_date;
            $appData->approved_duration_end_date = $opeData->approved_duration_end_date;
            $appData->approved_desired_duration = $opeData->approved_desired_duration;
            $appData->duration_amount = $opeData->duration_amount;
            $appData->approved_duration_amount = $opeData->approved_duration_amount;

            $appData->is_approval_online = $opeData->is_approval_online;
            if ($opeData->is_approval_online === 'yes') {
                $appData->ref_app_tracking_no = $opeData->ref_app_tracking_no;
                $appData->ref_app_approve_date = $opeData->ref_app_approve_date;
            } else {
                $appData->manually_approved_op_no = $opeData->manually_approved_op_no;
            }
            $appData->last_remittance_year = $opeData->last_remittance_year;
            $appData->inward_remittance = $opeData->inward_remittance;

            $appData->is_remittance_allowed = $opeData->is_remittance_allowed;
            $appData->approved_is_remittance_allowed = $opeData->approved_is_remittance_allowed;

            $appData->office_type = $opeData->office_type;

            $appData->desired_start_date = $opeData->desired_start_date;
            $appData->desired_end_date = $opeData->desired_end_date;
            $appData->extension_year = $opeData->extension_year;

            $appData->c_company_name = $opeData->c_company_name;
            $appData->c_origin_country_id = $opeData->c_origin_country_id;
            $appData->c_country_id = $opeData->c_country_id;
            $appData->c_flat_apart_floor = $opeData->c_flat_apart_floor;
            $appData->c_house_plot_holding = $opeData->c_house_plot_holding;
            $appData->c_street = $opeData->c_street;
            $appData->c_post_zip_code = $opeData->c_post_zip_code;
            $appData->c_telephone = $opeData->c_telephone;
            $appData->c_city = $opeData->c_city;
            $appData->c_email = $opeData->c_email;
            $appData->c_fax = $opeData->c_fax;
            $appData->c_state_province = $opeData->c_state_province;
            $appData->c_org_type  = $opeData->c_org_type;
            $appData->c_major_activity_brief = $opeData->c_major_activity_brief;

            $appData->authorized_capital = $opeData->authorized_capital;
            $appData->paid_up_capital = $opeData->paid_up_capital;
            $appData->local_company_name = $opeData->local_company_name;
            $appData->local_company_name_bn = $opeData->local_company_name_bn;

            $appData->ex_office_division_id = $opeData->ex_office_division_id;
            $appData->ex_office_district_id = $opeData->ex_office_district_id;
            $appData->ex_office_thana_id = $opeData->ex_office_thana_id;
            $appData->ex_office_post_office = $opeData->ex_office_post_office;
            $appData->ex_office_post_code = $opeData->ex_office_post_code;
            $appData->ex_office_address = $opeData->ex_office_address;
            $appData->ex_office_telephone_no = $opeData->ex_office_telephone_no;
            $appData->ex_office_mobile_no = $opeData->ex_office_mobile_no;
            $appData->ex_office_fax_no = $opeData->ex_office_fax_no;
            $appData->ex_office_email = $opeData->ex_office_email;

            $appData->activities_in_bd = $opeData->activities_in_bd;
            $appData->first_commencement_date = $opeData->first_commencement_date;
            $appData->operation_target_date = $opeData->operation_target_date;

            $appData->period_start_date = $opeData->period_start_date;
            $appData->period_end_date = $opeData->period_end_date;
            $appData->period_validity = $opeData->period_validity;

            $appData->local_executive = $opeData->local_executive;
            $appData->local_stuff = $opeData->local_stuff;
            $appData->local_total = $opeData->local_total;
            $appData->foreign_executive = $opeData->foreign_executive;
            $appData->foreign_stuff = $opeData->foreign_stuff;
            $appData->foreign_total = $opeData->foreign_total;
            $appData->manpower_total = $opeData->manpower_total;
            $appData->manpower_local_ratio = $opeData->manpower_local_ratio;
            $appData->manpower_foreign_ratio = $opeData->manpower_foreign_ratio;

            $appData->est_initial_expenses = $opeData->est_initial_expenses;
            $appData->est_monthly_expenses = $opeData->est_monthly_expenses;

            $appData->company_name = $opeData->company_name;
            $appData->company_name_bn = $opeData->company_name_bn;

            $appData->service_type = $opeData->service_type;
            $appData->reg_commercial_office = $opeData->reg_commercial_office;
            $appData->ownership_status_id = $opeData->ownership_status_id;
            $appData->organization_type_id = $opeData->organization_type_id;

            $appData->ceo_full_name = $opeData->ceo_full_name;
            $appData->ceo_dob = $opeData->ceo_dob;
            $appData->ceo_spouse_name = $opeData->ceo_spouse_name;
            $appData->ceo_designation = $opeData->ceo_designation;
            $appData->ceo_country_id = $opeData->ceo_country_id;
            $appData->ceo_district_id = $opeData->ceo_district_id;
            $appData->ceo_thana_id = $opeData->ceo_thana_id;
            $appData->ceo_post_code = $opeData->ceo_post_code;
            $appData->ceo_city = $opeData->ceo_city;
            $appData->ceo_state = $opeData->ceo_state;
            $appData->ceo_address = $opeData->ceo_address;
            $appData->ceo_telephone_no = $opeData->ceo_telephone_no;
            $appData->ceo_mobile_no = $opeData->ceo_mobile_no;
            $appData->ceo_fax_no = $opeData->ceo_fax_no;
            $appData->ceo_email = $opeData->ceo_email;
            $appData->ceo_father_name = $opeData->ceo_father_name;
            $appData->ceo_mother_name = $opeData->ceo_mother_name;
            $appData->ceo_nid = $opeData->ceo_nid;
            $appData->ceo_passport_no = $opeData->ceo_passport_no;
            $appData->ceo_gender = $opeData->ceo_gender;

            $appData->office_division_id = $opeData->office_division_id;
            $appData->office_district_id = $opeData->office_district_id;
            $appData->office_thana_id = $opeData->office_thana_id;
            $appData->office_post_office = $opeData->office_post_office;
            $appData->office_post_code = $opeData->office_post_code;
            $appData->office_address = $opeData->office_address;
            $appData->office_telephone_no = $opeData->office_telephone_no;
            $appData->office_mobile_no = $opeData->office_mobile_no;
            $appData->office_fax_no = $opeData->office_fax_no;
            $appData->office_email = $opeData->office_email;

            $appData->factory_district_id = $opeData->factory_district_id;
            $appData->factory_district_id = $opeData->factory_district_id;
            $appData->factory_post_office = $opeData->factory_post_office;
            $appData->factory_post_code = $opeData->factory_post_code;
            $appData->factory_address = $opeData->factory_address;
            $appData->factory_telephone_no = $opeData->factory_telephone_no;
            $appData->factory_mobile_no = $opeData->factory_mobile_no;
            $appData->factory_fax_no = $opeData->factory_fax_no;
            $appData->factory_email = $opeData->factory_email;
            $appData->factory_mouja = $opeData->factory_mouja;

            $appData->auth_full_name = $opeData->auth_full_name;
            $appData->auth_designation = $opeData->auth_designation;
            $appData->auth_email = $opeData->auth_email;
            $appData->auth_mobile_no = $opeData->auth_mobile_no;
            $appData->auth_image = $opeData->auth_image;

            $appData->major_activities = $opeData->major_activities;

            $appData->conditional_approved_file = $opeData->conditional_approved_file;
            $appData->conditional_approved_remarks = $opeData->conditional_approved_remarks;

            //$appData->shadow_file_path = $opeData->shadow_file_path;
            $appData->gf_payment_id = $opeData->gf_payment_id;
            $appData->sf_payment_id = $opeData->sf_payment_id;

            $appData->accept_terms = $opeData->accept_terms;
            $appData->approved_date = $opeData->approved_date;
            //$appData->payment_date = $opeData->payment_date;
            $appData->save();

            return true;
        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage(), $e->getFile()) . '[OPECP-1012]');
            return false;
        }
    }

    public static function OPADataStore($tracking_no, $ref_id)
    {
        try {
            $opaData = OfficePermissionAmendment::where('id', $ref_id)->first();
            $tracking_no_column = UtilFunction::getRefAppServiceName($opaData->ref_app_tracking_no);

            if (!empty($tracking_no_column)) {
                $appData = OPCommonPool::firstOrNew([$tracking_no_column => $opaData->ref_app_tracking_no]);
            } else {
                $appData = new OPCommonPool();
            }

            $appData->opa_tracking_no = $tracking_no;
            //$appData->certificate_link = $opaData->certificate_link;

            $appData->is_approval_online = $opaData->is_approval_online;
            if ($opaData->is_approval_online === 'yes') {
                $appData->ref_app_tracking_no = $opaData->ref_app_tracking_no;
                $appData->ref_app_approve_date = $opaData->ref_app_approve_date;
            } else {
                $appData->manually_approved_op_no = $opaData->manually_approved_op_no;
            }

            // $appData->office_type = $opaData->office_type;
            $appData->effective_date = $opaData->effective_date;
            $appData->approved_effective_date = $opaData->approved_effective_date;

            if(!empty($opaData->n_office_type)) {
                $appData->office_type = $opaData->n_office_type;
            }
            if (!empty($opaData->n_local_company_name)) {
                $appData->local_company_name = $opaData->n_local_company_name;
            }
            if (!empty($opaData->n_ex_office_division_id)) {
                $appData->ex_office_division_id = $opaData->n_ex_office_division_id;
            }
            if (!empty($opaData->n_ex_office_district_id)) {
                $appData->ex_office_district_id = $opaData->n_ex_office_district_id;
            }
            if (!empty($opaData->n_ex_office_thana_id)) {
                $appData->ex_office_thana_id = $opaData->n_ex_office_thana_id;
            }
            if (!empty($opaData->n_ex_office_post_office)) {
                $appData->ex_office_post_office = $opaData->n_ex_office_post_office;
            }
            if (!empty($opaData->n_ex_office_post_code)) {
                $appData->ex_office_post_code = $opaData->n_ex_office_post_code;
            }
            if (!empty($opaData->n_ex_office_address)) {
                $appData->ex_office_address = $opaData->n_ex_office_address;
            }
            if (!empty($opaData->n_ex_office_telephone_no)) {
                $appData->ex_office_telephone_no = $opaData->n_ex_office_telephone_no;
            }
            if (!empty($opaData->n_ex_office_mobile_no)) {
                $appData->ex_office_mobile_no = $opaData->n_ex_office_mobile_no;
            }
            if (!empty($opaData->n_ex_office_fax_no)) {
                $appData->ex_office_fax_no = $opaData->n_ex_office_fax_no;
            }
            if (!empty($opaData->n_ex_office_email)) {
                $appData->ex_office_email = $opaData->n_ex_office_email;
            }
            if (!empty($opaData->n_activities_in_bd)) {
                $appData->activities_in_bd = $opaData->n_activities_in_bd;
            }

            $appData->company_name = $opaData->company_name;
            $appData->company_name_bn = $opaData->company_name_bn;

            $appData->service_type = $opaData->service_type;
            $appData->reg_commercial_office = $opaData->reg_commercial_office;
            $appData->ownership_status_id = $opaData->ownership_status_id;
            $appData->organization_type_id = $opaData->organization_type_id;

            $appData->ceo_full_name = $opaData->ceo_full_name;
            $appData->ceo_dob = $opaData->ceo_dob;
            $appData->ceo_spouse_name = $opaData->ceo_spouse_name;
            $appData->ceo_designation = $opaData->ceo_designation;
            $appData->ceo_country_id = $opaData->ceo_country_id;
            $appData->ceo_district_id = $opaData->ceo_district_id;
            $appData->ceo_thana_id = $opaData->ceo_thana_id;
            $appData->ceo_post_code = $opaData->ceo_post_code;
            $appData->ceo_city = $opaData->ceo_city;
            $appData->ceo_state = $opaData->ceo_state;
            $appData->ceo_address = $opaData->ceo_address;
            $appData->ceo_telephone_no = $opaData->ceo_telephone_no;
            $appData->ceo_mobile_no = $opaData->ceo_mobile_no;
            $appData->ceo_fax_no = $opaData->ceo_fax_no;
            $appData->ceo_email = $opaData->ceo_email;
            $appData->ceo_father_name = $opaData->ceo_father_name;
            $appData->ceo_mother_name = $opaData->ceo_mother_name;
            $appData->ceo_nid = $opaData->ceo_nid;
            $appData->ceo_passport_no = $opaData->ceo_passport_no;
            $appData->ceo_gender = $opaData->ceo_gender;

            $appData->office_division_id = $opaData->office_division_id;
            $appData->office_district_id = $opaData->office_district_id;
            $appData->office_thana_id = $opaData->office_thana_id;
            $appData->office_post_office = $opaData->office_post_office;
            $appData->office_post_code = $opaData->office_post_code;
            $appData->office_address = $opaData->office_address;
            $appData->office_telephone_no = $opaData->office_telephone_no;
            $appData->office_mobile_no = $opaData->office_mobile_no;
            $appData->office_fax_no = $opaData->office_fax_no;
            $appData->office_email = $opaData->office_email;

            $appData->factory_district_id = $opaData->factory_district_id;
            $appData->factory_district_id = $opaData->factory_district_id;
            $appData->factory_post_office = $opaData->factory_post_office;
            $appData->factory_post_code = $opaData->factory_post_code;
            $appData->factory_address = $opaData->factory_address;
            $appData->factory_telephone_no = $opaData->factory_telephone_no;
            $appData->factory_mobile_no = $opaData->factory_mobile_no;
            $appData->factory_fax_no = $opaData->factory_fax_no;
            $appData->factory_email = $opaData->factory_email;
            $appData->factory_mouja = $opaData->factory_mouja;

            $appData->auth_full_name = $opaData->auth_full_name;
            $appData->auth_designation = $opaData->auth_designation;
            $appData->auth_email = $opaData->auth_email;
            $appData->auth_mobile_no = $opaData->auth_mobile_no;
            $appData->auth_image = $opaData->auth_image;

            $appData->major_activities = $opaData->major_activities;
            $appData->conditional_approved_file = $opaData->conditional_approved_file;
            $appData->conditional_approved_remarks = $opaData->conditional_approved_remarks;

            //$appData->shadow_file_path = $opaData->shadow_file_path;
            //$appData->payment_date = $opaData->payment_date;

            $appData->gf_payment_id = $opaData->gf_payment_id;
            $appData->sf_payment_id = $opaData->sf_payment_id;

            $appData->accept_terms = $opaData->accept_terms;
            $appData->approved_date = $opaData->approved_date;

            $appData->save();

            return true;
        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage(), $e->getFile()) . '[OPACP-1013]');
            return false;
        }
    }

    public static function OPCDataStore($tracking_no, $ref_id)
    {
        try {
            $opcData = OfficePermissionCancellation::where('id', $ref_id)->first();
            $tracking_no_column = UtilFunction::getRefAppServiceName($opcData->ref_app_tracking_no);

            if (!empty($tracking_no_column)) {
                $appData = OPCommonPool::firstOrNew([$tracking_no_column => $opcData->ref_app_tracking_no]);
            } else {
                $appData = new OPCommonPool();
            }

            $appData->opc_tracking_no = $tracking_no;
            //$appData->certificate_link = $opcData->certificate_link;

            $appData->is_approval_online = $opcData->is_approval_online;
            if ($opcData->is_approval_online === 'yes') {
                $appData->ref_app_tracking_no = $opcData->ref_app_tracking_no;
                $appData->ref_app_approve_date = $opcData->ref_app_approve_date;
            } else {
                $appData->manually_approved_op_no = $opcData->manually_approved_op_no;
            }

            $appData->applicant_remarks = $opcData->applicant_remarks;
            $appData->office_type = $opcData->office_type;
            $appData->approved_effect_date = $opcData->approved_effect_date;
            $appData->date_of_office_permission = $opcData->date_of_office_permission;
            $appData->effect_date = $opcData->effect_date;

            $appData->desired_start_date = $opcData->desired_start_date;
            $appData->desired_end_date = $opcData->desired_end_date;
            $appData->extension_year = $opcData->extension_year;

            $appData->c_company_name = $opcData->c_company_name;
            $appData->c_origin_country_id = $opcData->c_origin_country_id;
            $appData->c_country_id = $opcData->c_country_id;
            $appData->c_flat_apart_floor = $opcData->c_flat_apart_floor;
            $appData->c_house_plot_holding = $opcData->c_house_plot_holding;
            $appData->c_street = $opcData->c_street;
            $appData->c_post_zip_code = $opcData->c_post_zip_code;
            $appData->c_telephone = $opcData->c_telephone;
            $appData->c_city = $opcData->c_city;
            $appData->c_email = $opcData->c_email;
            $appData->c_fax = $opcData->c_fax;
            $appData->c_state_province = $opcData->c_state_province;
            $appData->c_org_type  = $opcData->c_org_type;
            $appData->c_major_activity_brief = $opcData->c_major_activity_brief;

            $appData->authorized_capital = $opcData->authorized_capital;
            $appData->paid_up_capital = $opcData->paid_up_capital;
            $appData->local_company_name = $opcData->local_company_name;

            $appData->ex_office_division_id = $opcData->ex_office_division_id;
            $appData->ex_office_district_id = $opcData->ex_office_district_id;
            $appData->ex_office_thana_id = $opcData->ex_office_thana_id;
            $appData->ex_office_post_office = $opcData->ex_office_post_office;
            $appData->ex_office_post_code = $opcData->ex_office_post_code;
            $appData->ex_office_address = $opcData->ex_office_address;
            $appData->ex_office_telephone_no = $opcData->ex_office_telephone_no;
            $appData->ex_office_mobile_no = $opcData->ex_office_mobile_no;
            $appData->ex_office_fax_no = $opcData->ex_office_fax_no;
            $appData->ex_office_email = $opcData->ex_office_email;

            $appData->first_commencement_date = $opcData->first_commencement_date;
            $appData->operation_target_date = $opcData->operation_target_date;

            $appData->period_start_date = $opcData->period_start_date;
            $appData->period_end_date = $opcData->period_end_date;
            $appData->period_validity = $opcData->period_validity;

            $appData->local_executive = $opcData->local_executive;
            $appData->local_stuff = $opcData->local_stuff;
            $appData->local_total = $opcData->local_total;
            $appData->foreign_executive = $opcData->foreign_executive;
            $appData->foreign_stuff = $opcData->foreign_stuff;
            $appData->foreign_total = $opcData->foreign_total;
            $appData->manpower_total = $opcData->manpower_total;
            $appData->manpower_local_ratio = $opcData->manpower_local_ratio;
            $appData->manpower_foreign_ratio = $opcData->manpower_foreign_ratio;

            $appData->est_initial_expenses = $opcData->est_initial_expenses;
            $appData->est_monthly_expenses = $opcData->est_monthly_expenses;

            $appData->company_name = $opcData->company_name;
            $appData->company_name_bn = $opcData->company_name_bn;

            $appData->service_type = $opcData->service_type;
            $appData->reg_commercial_office = $opcData->reg_commercial_office;
            $appData->ownership_status_id = $opcData->ownership_status_id;
            $appData->organization_type_id = $opcData->organization_type_id;

            $appData->ceo_full_name = $opcData->ceo_full_name;
            $appData->ceo_dob = $opcData->ceo_dob;
            $appData->ceo_spouse_name = $opcData->ceo_spouse_name;
            $appData->ceo_designation = $opcData->ceo_designation;
            $appData->ceo_country_id = $opcData->ceo_country_id;
            $appData->ceo_district_id = $opcData->ceo_district_id;
            $appData->ceo_thana_id = $opcData->ceo_thana_id;
            $appData->ceo_post_code = $opcData->ceo_post_code;
            $appData->ceo_city = $opcData->ceo_city;
            $appData->ceo_state = $opcData->ceo_state;
            $appData->ceo_address = $opcData->ceo_address;
            $appData->ceo_telephone_no = $opcData->ceo_telephone_no;
            $appData->ceo_mobile_no = $opcData->ceo_mobile_no;
            $appData->ceo_fax_no = $opcData->ceo_fax_no;
            $appData->ceo_email = $opcData->ceo_email;
            $appData->ceo_father_name = $opcData->ceo_father_name;
            $appData->ceo_mother_name = $opcData->ceo_mother_name;
            $appData->ceo_nid = $opcData->ceo_nid;
            $appData->ceo_passport_no = $opcData->ceo_passport_no;
            $appData->ceo_gender = $opcData->ceo_gender;

            $appData->office_division_id = $opcData->office_division_id;
            $appData->office_district_id = $opcData->office_district_id;
            $appData->office_thana_id = $opcData->office_thana_id;
            $appData->office_post_office = $opcData->office_post_office;
            $appData->office_post_code = $opcData->office_post_code;
            $appData->office_address = $opcData->office_address;
            $appData->office_telephone_no = $opcData->office_telephone_no;
            $appData->office_mobile_no = $opcData->office_mobile_no;
            $appData->office_fax_no = $opcData->office_fax_no;
            $appData->office_email = $opcData->office_email;

            $appData->factory_district_id = $opcData->factory_district_id;
            $appData->factory_district_id = $opcData->factory_district_id;
            $appData->factory_post_office = $opcData->factory_post_office;
            $appData->factory_post_code = $opcData->factory_post_code;
            $appData->factory_address = $opcData->factory_address;
            $appData->factory_telephone_no = $opcData->factory_telephone_no;
            $appData->factory_mobile_no = $opcData->factory_mobile_no;
            $appData->factory_fax_no = $opcData->factory_fax_no;
            $appData->factory_email = $opcData->factory_email;
            $appData->factory_mouja = $opcData->factory_mouja;

            $appData->auth_full_name = $opcData->auth_full_name;
            $appData->auth_designation = $opcData->auth_designation;
            $appData->auth_email = $opcData->auth_email;
            $appData->auth_mobile_no = $opcData->auth_mobile_no;
            $appData->auth_image = $opcData->auth_image;

            $appData->major_activities = $opcData->major_activities;
            //$appData->shadow_file_path = $opcData->shadow_file_path;
            $appData->gf_payment_id = $opcData->gf_payment_id;
            $appData->conditional_approved_file = $opcData->conditional_approved_file;
            $appData->conditional_approved_remarks = $opcData->conditional_approved_remarks;
            $appData->sf_payment_id = $opcData->sf_payment_id;
            $appData->accept_terms = $opcData->accept_terms;
            $appData->approved_date = $opcData->approved_date;
            //$appData->payment_date = $opcData->payment_date;
            $appData->save();

            return true;
        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage(), $e->getFile()) . '[OPCCP-1014]');
            return false;
        }
    }
}