<?php

namespace App\Modules\ProcessPath\Services;

use App\BRCommonPool;
use App\IRCCommonPool;
use App\Libraries\CommonFunction;
use App\Libraries\UtilFunction;
use App\Modules\IrcRecommendationNew\Models\IrcInspection;
use App\Modules\IrcRecommendationNew\Models\IrcRecommendationNew;
use App\Modules\IrcRecommendationRegular\Models\IrcRecommendationRegular;
use App\Modules\IrcRecommendationRegular\Models\RegularIrcInspection;
use App\Modules\IrcRecommendationSecondAdhoc\Models\IrcRecommendationSecondAdhoc;
use App\Modules\IrcRecommendationSecondAdhoc\Models\SecondIrcInspection;
use App\Modules\IrcRecommendationThirdAdhoc\Models\IrcRecommendationThirdAdhoc;
use App\Modules\IrcRecommendationThirdAdhoc\Models\ThirdIrcInspection;
use Illuminate\Support\Facades\Log;

class IRCCommonPoolManager
{
    public static function ircFirstAdhocDataStore($tracking_id, $ref_app_id)
    {
        try {
            $ircFirstAdhocData = IrcRecommendationNew::where('id', $ref_app_id)->first();
            $first_adhoc_inspection_id = IrcInspection::where('app_id', $ref_app_id)->where('ins_approved_status', 1)->pluck('id');

            $appData = new IRCCommonPool();
            $appData->first_adhoc_tracking_no = $tracking_id;
            $appData->first_adhoc_inspection_id = !empty($first_adhoc_inspection_id) ? $first_adhoc_inspection_id : null;
            $appData->company_id = $ircFirstAdhocData->company_id;
            $appData->app_type_id = $ircFirstAdhocData->app_type_id;
            $appData->irc_purpose_id = $ircFirstAdhocData->irc_purpose_id;
            $appData->agree_with_instruction = $ircFirstAdhocData->agree_with_instruction;
            $appData->last_br = $ircFirstAdhocData->last_br;

            if ($ircFirstAdhocData->last_br == 'yes') {
                $getBRCommonPoolId = BRCommonPool::where('br_tracking_no', $ircFirstAdhocData->ref_app_tracking_no)->pluck('id');

                $appData->br_ref_app_tracking_no = $ircFirstAdhocData->ref_app_tracking_no;
                $appData->br_ref_app_approve_date = $ircFirstAdhocData->ref_app_approve_date;
                $appData->reg_no = $ircFirstAdhocData->reg_no;

                $appData->br_common_pool_id = !empty($getBRCommonPoolId) ? $getBRCommonPoolId : null;
            }

            if ($ircFirstAdhocData->last_br == 'no') {
                $appData->br_manually_approved_no = $ircFirstAdhocData->manually_approved_br_no;
                $appData->br_manually_approved_date = $ircFirstAdhocData->manually_approved_br_date;
            }

            $appData->io_submission_deadline = $ircFirstAdhocData->io_submission_deadline;

            $appData->company_name = $ircFirstAdhocData->company_name;
            $appData->company_name_bn = $ircFirstAdhocData->company_name_bn;
            $appData->organization_type_id = $ircFirstAdhocData->organization_type_id;
            $appData->organization_status_id = $ircFirstAdhocData->organization_status_id;
            $appData->ownership_status_id = $ircFirstAdhocData->ownership_status_id;
            $appData->country_of_origin_id = $ircFirstAdhocData->country_of_origin_id;
            $appData->project_name = $ircFirstAdhocData->project_name;
            $appData->section_id = $ircFirstAdhocData->section_id;
            $appData->division_id = $ircFirstAdhocData->division_id;
            $appData->group_id = $ircFirstAdhocData->group_id;
            $appData->class_id = $ircFirstAdhocData->class_id;
            $appData->class_code = $ircFirstAdhocData->class_code;
            $appData->sub_class_id = $ircFirstAdhocData->sub_class_id;
            $appData->other_sub_class_code = $ircFirstAdhocData->other_sub_class_code;
            $appData->other_sub_class_name = $ircFirstAdhocData->other_sub_class_name;
            $appData->major_activities = $ircFirstAdhocData->major_activities;

            //ECO information
            $appData->ceo_country_id = $ircFirstAdhocData->ceo_country_id;
            $appData->ceo_dob = $ircFirstAdhocData->ceo_dob;
            $appData->ceo_passport_no = $ircFirstAdhocData->ceo_passport_no;
            $appData->ceo_nid = $ircFirstAdhocData->ceo_nid;
            $appData->ceo_full_name = $ircFirstAdhocData->ceo_full_name;
            $appData->ceo_designation = $ircFirstAdhocData->ceo_designation;
            $appData->ceo_district_id = $ircFirstAdhocData->ceo_district_id;
            $appData->ceo_city = $ircFirstAdhocData->ceo_city;
            $appData->ceo_state = $ircFirstAdhocData->ceo_state;
            $appData->ceo_thana_id = $ircFirstAdhocData->ceo_thana_id;
            $appData->ceo_post_code = $ircFirstAdhocData->ceo_post_code;
            $appData->ceo_address = $ircFirstAdhocData->ceo_address;
            $appData->ceo_telephone_no = $ircFirstAdhocData->ceo_telephone_no;
            $appData->ceo_mobile_no = $ircFirstAdhocData->ceo_mobile_no;
            $appData->ceo_fax_no = $ircFirstAdhocData->ceo_fax_no;
            $appData->ceo_email = $ircFirstAdhocData->ceo_email;
            $appData->ceo_father_name = $ircFirstAdhocData->ceo_father_name;
            $appData->ceo_mother_name = $ircFirstAdhocData->ceo_mother_name;
            $appData->ceo_spouse_name = $ircFirstAdhocData->ceo_spouse_name;
            $appData->ceo_gender = $ircFirstAdhocData->ceo_gender;
            // Office Address
            $appData->office_division_id = $ircFirstAdhocData->office_division_id;
            $appData->office_district_id = $ircFirstAdhocData->office_district_id;
            $appData->office_thana_id = $ircFirstAdhocData->office_thana_id;
            $appData->office_post_office = $ircFirstAdhocData->office_post_office;
            $appData->office_post_code = $ircFirstAdhocData->office_post_code;
            $appData->office_address = $ircFirstAdhocData->office_address;
            $appData->office_telephone_no = $ircFirstAdhocData->office_telephone_no;
            $appData->office_mobile_no = $ircFirstAdhocData->office_mobile_no;
            $appData->office_fax_no = $ircFirstAdhocData->office_fax_no;
            $appData->office_email = $ircFirstAdhocData->office_email;
            // Factory Address
            $appData->factory_district_id = $ircFirstAdhocData->factory_district_id;
            $appData->factory_thana_id = $ircFirstAdhocData->factory_thana_id;
            $appData->factory_post_office = $ircFirstAdhocData->factory_post_office;
            $appData->factory_post_code = $ircFirstAdhocData->factory_post_code;
            $appData->factory_address = $ircFirstAdhocData->factory_address;
            $appData->factory_telephone_no = $ircFirstAdhocData->factory_telephone_no;
            $appData->factory_mobile_no = $ircFirstAdhocData->factory_mobile_no;
            $appData->factory_fax_no = $ircFirstAdhocData->factory_fax_no;
            $appData->factory_email = $ircFirstAdhocData->factory_email;

            $appData->project_status_id = $ircFirstAdhocData->project_status_id;
            $appData->commercial_operation_date = $ircFirstAdhocData->commercial_operation_date;

            $appData->local_sales = $ircFirstAdhocData->local_sales;
            $appData->foreign_sales = $ircFirstAdhocData->foreign_sales;
            $appData->total_sales = $ircFirstAdhocData->total_sales;
            //manpower section
            $appData->local_male = $ircFirstAdhocData->local_male;
            $appData->local_female = $ircFirstAdhocData->local_female;
            $appData->local_total = $ircFirstAdhocData->local_total;
            $appData->foreign_male = $ircFirstAdhocData->foreign_male;
            $appData->foreign_female = $ircFirstAdhocData->foreign_female;
            $appData->foreign_total = $ircFirstAdhocData->foreign_total;
            $appData->manpower_total = $ircFirstAdhocData->manpower_total;
            $appData->manpower_local_ratio = $ircFirstAdhocData->manpower_local_ratio;
            $appData->manpower_foreign_ratio = $ircFirstAdhocData->manpower_foreign_ratio;

            $appData->local_land_ivst = $ircFirstAdhocData->local_land_ivst;
            $appData->local_land_ivst_ccy = $ircFirstAdhocData->local_land_ivst_ccy;
            $appData->local_building_ivst = $ircFirstAdhocData->local_building_ivst;
            $appData->local_building_ivst_ccy = $ircFirstAdhocData->local_building_ivst_ccy;
            $appData->local_machinery_ivst = $ircFirstAdhocData->local_machinery_ivst;
            $appData->local_machinery_ivst_ccy = $ircFirstAdhocData->local_machinery_ivst_ccy;
            $appData->local_others_ivst = $ircFirstAdhocData->local_others_ivst;
            $appData->local_others_ivst_ccy = $ircFirstAdhocData->local_others_ivst_ccy;
            $appData->local_wc_ivst = $ircFirstAdhocData->local_wc_ivst;
            $appData->local_wc_ivst_ccy = $ircFirstAdhocData->local_wc_ivst_ccy;
            $appData->total_fixed_ivst_million = $ircFirstAdhocData->total_fixed_ivst_million;
            $appData->total_fixed_ivst = $ircFirstAdhocData->total_fixed_ivst;
            $appData->usd_exchange_rate = $ircFirstAdhocData->usd_exchange_rate;
            $appData->total_fee = $ircFirstAdhocData->total_fee;

            $appData->finance_src_loc_equity_1 = $ircFirstAdhocData->finance_src_loc_equity_1;
            $appData->finance_src_foreign_equity_1 = $ircFirstAdhocData->finance_src_foreign_equity_1;
            $appData->finance_src_loc_total_equity_1 = $ircFirstAdhocData->finance_src_loc_total_equity_1;
            $appData->finance_src_loc_loan_1 = $ircFirstAdhocData->finance_src_loc_loan_1;
            $appData->finance_src_foreign_loan_1 = $ircFirstAdhocData->finance_src_foreign_loan_1;
            $appData->finance_src_total_loan = $ircFirstAdhocData->finance_src_total_loan;
            $appData->finance_src_loc_total_financing_1 = $ircFirstAdhocData->finance_src_loc_total_financing_1;
            $appData->finance_src_loc_total_financing_m = $ircFirstAdhocData->finance_src_loc_total_financing_m;

            $appData->annual_production_start_date = $ircFirstAdhocData->annual_production_start_date;
            $appData->em_lc_total_taka_mil = $ircFirstAdhocData->em_lc_total_taka_mil;
            $appData->em_local_total_taka_mil = $ircFirstAdhocData->em_local_total_taka_mil;

            $appData->public_land = $ircFirstAdhocData->public_land;
            $appData->public_electricity = $ircFirstAdhocData->public_electricity;
            $appData->public_gas = $ircFirstAdhocData->public_gas;
            $appData->public_telephone = $ircFirstAdhocData->public_telephone;
            $appData->public_road = $ircFirstAdhocData->public_road;
            $appData->public_water = $ircFirstAdhocData->public_water;
            $appData->public_drainage = $ircFirstAdhocData->public_drainage;
            $appData->public_others = $ircFirstAdhocData->public_others;
            $appData->public_others_field = $ircFirstAdhocData->public_others_field;

            $appData->trade_licence_num = $ircFirstAdhocData->trade_licence_num;
            $appData->trade_licence_issue_date = $ircFirstAdhocData->trade_licence_issue_date;
            $appData->trade_licence_issuing_authority = $ircFirstAdhocData->trade_licence_issuing_authority;
            $appData->trade_licence_validity_period = $ircFirstAdhocData->trade_licence_validity_period;

            $appData->tin_number = $ircFirstAdhocData->tin_number;
            $appData->tin_issuing_authority = $ircFirstAdhocData->tin_issuing_authority;

            $appData->inc_number = $ircFirstAdhocData->inc_number;
            $appData->inc_issuing_authority = $ircFirstAdhocData->inc_issuing_authority;

            $appData->fire_license_info = $ircFirstAdhocData->fire_license_info;
            if ($appData->fire_license_info == 'already_have') {
                $appData->fl_number = $ircFirstAdhocData->fl_number;
                $appData->fl_expire_date = $ircFirstAdhocData->fl_expire_date;
            }
            if ($appData->fire_license_info == 'applied_for') {
                $appData->fl_application_number = $ircFirstAdhocData->fl_application_number;
                $appData->fl_apply_date = $ircFirstAdhocData->fl_apply_date;
            }
            $appData->fl_issuing_authority = $ircFirstAdhocData->fl_issuing_authority;

            $appData->environment_clearance = $ircFirstAdhocData->environment_clearance;
            if ($appData->environment_clearance == 'already_have') {
                $appData->el_number = $ircFirstAdhocData->el_number;
                $appData->el_expire_date = $ircFirstAdhocData->el_expire_date;
            }
            if ($appData->environment_clearance == 'applied_for') {
                $appData->el_application_number = $ircFirstAdhocData->el_application_number;
                $appData->el_apply_date = $ircFirstAdhocData->el_apply_date;
            }
            $appData->el_issuing_authority = $ircFirstAdhocData->el_issuing_authority;

            $appData->bank_account_number = $ircFirstAdhocData->bank_account_number;
            $appData->bank_account_title = $ircFirstAdhocData->bank_account_title;
            $appData->bank_id = $ircFirstAdhocData->bank_id;
            $appData->branch_id = $ircFirstAdhocData->branch_id;
            $appData->bank_address = $ircFirstAdhocData->bank_address;

            $appData->assoc_membership_number = $ircFirstAdhocData->assoc_membership_number;
            $appData->assoc_chamber_name = $ircFirstAdhocData->assoc_chamber_name;
            $appData->assoc_issuing_date = $ircFirstAdhocData->assoc_issuing_date;
            $appData->assoc_expire_date = $ircFirstAdhocData->assoc_expire_date;

            $appData->bin_vat_number = $ircFirstAdhocData->bin_vat_number;
            $appData->bin_vat_issuing_authority = $ircFirstAdhocData->bin_vat_issuing_authority;
            $appData->bin_vat_issuing_date = $ircFirstAdhocData->bin_vat_issuing_date;
            $appData->bin_vat_expire_date = $ircFirstAdhocData->bin_vat_expire_date;

            $appData->g_full_name = $ircFirstAdhocData->g_full_name;
            $appData->g_designation = $ircFirstAdhocData->g_designation;
            $appData->g_signature = $ircFirstAdhocData->g_signature;

            $appData->auth_full_name = $ircFirstAdhocData->auth_full_name;
            $appData->auth_designation = $ircFirstAdhocData->auth_designation;
            $appData->auth_mobile_no = $ircFirstAdhocData->auth_mobile_no;
            $appData->auth_email = $ircFirstAdhocData->auth_email;
            $appData->auth_image = $ircFirstAdhocData->auth_image;

            $appData->certificate_link = $ircFirstAdhocData->certificate_link;
            //$appData->list_of_dir_machinery_doc = $ircFirstAdhocData->list_of_dir_machinery_doc;

            $appData->accept_terms = $ircFirstAdhocData->accept_terms;
            $appData->inspection_gov_fee = $ircFirstAdhocData->inspection_gov_fee;
            $appData->is_ccie_submitted_irc_1st = $ircFirstAdhocData->is_ccie_submitted_irc_1st;
            $appData->sf_payment_id = $ircFirstAdhocData->sf_payment_id;
            $appData->gf_payment_id = $ircFirstAdhocData->gf_payment_id;
            $appData->gf_manual_payment_id = $ircFirstAdhocData->gf_manual_payment_id;
            $appData->payment_date = $ircFirstAdhocData->payment_date;
            $appData->approved_date = $ircFirstAdhocData->approved_date;

            $appData->save();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function ircSecondAdhocDataStore($tracking_id, $ref_app_id)
    {

        try {
            $ircSecondAdhocData = IrcRecommendationSecondAdhoc::where('id', $ref_app_id)->first();
            $second_adhoc_inspection_id = SecondIrcInspection::where('app_id', $ref_app_id)->where('ins_approved_status', 1)->pluck('id');

            $ref_service_name = UtilFunction::getRefAppServiceName($ircSecondAdhocData->irc_ref_app_tracking_no);

            if (!empty($ref_service_name)) {
                $appData = IRCCommonPool::firstOrNew([$ref_service_name => $ircSecondAdhocData->irc_ref_app_tracking_no]);
            } else {
                $appData = new IRCCommonPool();
            }

            $appData->second_adhoc_tracking_no = $tracking_id;
            $appData->second_adhoc_inspection_id = !empty($second_adhoc_inspection_id) ? $second_adhoc_inspection_id : null;
            $appData->company_id = $ircSecondAdhocData->company_id;
            $appData->app_type_id = $ircSecondAdhocData->app_type_id;
            $appData->irc_purpose_id = $ircSecondAdhocData->irc_purpose_id;
            $appData->agree_with_instruction = $ircSecondAdhocData->agree_with_instruction;
            $appData->last_br = $ircSecondAdhocData->last_br;
            if ($ircSecondAdhocData->last_br == 'yes') {
                $getBRCommonPoolId = BRCommonPool::where('br_tracking_no', $ircSecondAdhocData->br_ref_app_tracking_no)->pluck('id');
                $appData->br_ref_app_tracking_no = $ircSecondAdhocData->br_ref_app_tracking_no;
                $appData->br_ref_app_approve_date = $ircSecondAdhocData->br_ref_app_approve_date;
                $appData->br_common_pool_id = !empty($getBRCommonPoolId) ? $getBRCommonPoolId : null;
            }

            if ($ircSecondAdhocData->last_br == 'no') {
                $appData->br_manually_approved_no = $ircSecondAdhocData->br_manually_approved_no;
                $appData->br_manually_approved_date = $ircSecondAdhocData->br_manually_approved_date;
            }

            $appData->last_irc_1st_adhoc = $ircSecondAdhocData->last_irc_1st_adhoc;
            if ($ircSecondAdhocData->last_irc_1st_adhoc == 'yes') {
                $appData->irc_ref_app_tracking_no = $ircSecondAdhocData->irc_ref_app_tracking_no;
                $appData->irc_ref_app_approve_date = $ircSecondAdhocData->irc_ref_app_approve_date;
            }
            if ($ircSecondAdhocData->last_irc_1st_adhoc == 'no') {
                $appData->irc_manually_approved_no = $ircSecondAdhocData->irc_manually_approved_no;
                $appData->irc_manually_approved_date = $ircSecondAdhocData->irc_manually_approved_date;
            }

            $appData->io_submission_deadline = $ircSecondAdhocData->io_submission_deadline;

            $appData->company_name = $ircSecondAdhocData->company_name;
            $appData->company_name_bn = $ircSecondAdhocData->company_name_bn;
            $appData->organization_type_id = $ircSecondAdhocData->organization_type_id;
            $appData->organization_status_id = $ircSecondAdhocData->organization_status_id;
            $appData->ownership_status_id = $ircSecondAdhocData->ownership_status_id;
            $appData->country_of_origin_id = $ircSecondAdhocData->country_of_origin_id;
            $appData->project_name = $ircSecondAdhocData->project_name;
            $appData->section_id = $ircSecondAdhocData->section_id;
            $appData->division_id = $ircSecondAdhocData->division_id;
            $appData->group_id = $ircSecondAdhocData->group_id;
            $appData->class_id = $ircSecondAdhocData->class_id;
            $appData->class_code = $ircSecondAdhocData->class_code;
            $appData->sub_class_id = $ircSecondAdhocData->sub_class_id;
            $appData->other_sub_class_code = $ircSecondAdhocData->other_sub_class_code;
            $appData->other_sub_class_name = $ircSecondAdhocData->other_sub_class_name;
            $appData->major_activities = $ircSecondAdhocData->major_activities;

            //ECO information
            $appData->ceo_country_id = $ircSecondAdhocData->ceo_country_id;
            $appData->ceo_dob = $ircSecondAdhocData->ceo_dob;
            $appData->ceo_passport_no = $ircSecondAdhocData->ceo_passport_no;
            $appData->ceo_nid = $ircSecondAdhocData->ceo_nid;
            $appData->ceo_full_name = $ircSecondAdhocData->ceo_full_name;
            $appData->ceo_designation = $ircSecondAdhocData->ceo_designation;
            $appData->ceo_district_id = $ircSecondAdhocData->ceo_district_id;
            $appData->ceo_city = $ircSecondAdhocData->ceo_city;
            $appData->ceo_state = $ircSecondAdhocData->ceo_state;
            $appData->ceo_thana_id = $ircSecondAdhocData->ceo_thana_id;
            $appData->ceo_post_code = $ircSecondAdhocData->ceo_post_code;
            $appData->ceo_address = $ircSecondAdhocData->ceo_address;
            $appData->ceo_telephone_no = $ircSecondAdhocData->ceo_telephone_no;
            $appData->ceo_mobile_no = $ircSecondAdhocData->ceo_mobile_no;
            $appData->ceo_fax_no = $ircSecondAdhocData->ceo_fax_no;
            $appData->ceo_email = $ircSecondAdhocData->ceo_email;
            $appData->ceo_father_name = $ircSecondAdhocData->ceo_father_name;
            $appData->ceo_mother_name = $ircSecondAdhocData->ceo_mother_name;
            $appData->ceo_spouse_name = $ircSecondAdhocData->ceo_spouse_name;
            $appData->ceo_gender = $ircSecondAdhocData->ceo_gender;
            // Office Address
            $appData->office_division_id = $ircSecondAdhocData->office_division_id;
            $appData->office_district_id = $ircSecondAdhocData->office_district_id;
            $appData->office_thana_id = $ircSecondAdhocData->office_thana_id;
            $appData->office_post_office = $ircSecondAdhocData->office_post_office;
            $appData->office_post_code = $ircSecondAdhocData->office_post_code;
            $appData->office_address = $ircSecondAdhocData->office_address;
            $appData->office_telephone_no = $ircSecondAdhocData->office_telephone_no;
            $appData->office_mobile_no = $ircSecondAdhocData->office_mobile_no;
            $appData->office_fax_no = $ircSecondAdhocData->office_fax_no;
            $appData->office_email = $ircSecondAdhocData->office_email;
            // Factory Address
            $appData->factory_district_id = $ircSecondAdhocData->factory_district_id;
            $appData->factory_thana_id = $ircSecondAdhocData->factory_thana_id;
            $appData->factory_post_office = $ircSecondAdhocData->factory_post_office;
            $appData->factory_post_code = $ircSecondAdhocData->factory_post_code;
            $appData->factory_address = $ircSecondAdhocData->factory_address;
            $appData->factory_telephone_no = $ircSecondAdhocData->factory_telephone_no;
            $appData->factory_mobile_no = $ircSecondAdhocData->factory_mobile_no;
            $appData->factory_fax_no = $ircSecondAdhocData->factory_fax_no;
            $appData->factory_email = $ircSecondAdhocData->factory_email;

            $appData->project_status_id = $ircSecondAdhocData->project_status_id;
            $appData->commercial_operation_date = $ircSecondAdhocData->commercial_operation_date;

            $appData->local_sales = $ircSecondAdhocData->local_sales;
            $appData->foreign_sales = $ircSecondAdhocData->foreign_sales;
            $appData->total_sales = $ircSecondAdhocData->total_sales;
            //manpower section
            $appData->local_male = $ircSecondAdhocData->local_male;
            $appData->local_female = $ircSecondAdhocData->local_female;
            $appData->local_total = $ircSecondAdhocData->local_total;
            $appData->foreign_male = $ircSecondAdhocData->foreign_male;
            $appData->foreign_female = $ircSecondAdhocData->foreign_female;
            $appData->foreign_total = $ircSecondAdhocData->foreign_total;
            $appData->manpower_total = $ircSecondAdhocData->manpower_total;
            $appData->manpower_local_ratio = $ircSecondAdhocData->manpower_local_ratio;
            $appData->manpower_foreign_ratio = $ircSecondAdhocData->manpower_foreign_ratio;

            $appData->local_land_ivst = $ircSecondAdhocData->local_land_ivst;
            $appData->local_land_ivst_ccy = $ircSecondAdhocData->local_land_ivst_ccy;
            $appData->local_building_ivst = $ircSecondAdhocData->local_building_ivst;
            $appData->local_building_ivst_ccy = $ircSecondAdhocData->local_building_ivst_ccy;
            $appData->local_machinery_ivst = $ircSecondAdhocData->local_machinery_ivst;
            $appData->local_machinery_ivst_ccy = $ircSecondAdhocData->local_machinery_ivst_ccy;
            $appData->local_others_ivst = $ircSecondAdhocData->local_others_ivst;
            $appData->local_others_ivst_ccy = $ircSecondAdhocData->local_others_ivst_ccy;
            $appData->local_wc_ivst = $ircSecondAdhocData->local_wc_ivst;
            $appData->local_wc_ivst_ccy = $ircSecondAdhocData->local_wc_ivst_ccy;
            $appData->total_fixed_ivst_million = $ircSecondAdhocData->total_fixed_ivst_million;
            $appData->total_fixed_ivst = $ircSecondAdhocData->total_fixed_ivst;
            $appData->usd_exchange_rate = $ircSecondAdhocData->usd_exchange_rate;
            $appData->total_fee = $ircSecondAdhocData->total_fee;

            $appData->finance_src_loc_equity_1 = $ircSecondAdhocData->finance_src_loc_equity_1;
            $appData->finance_src_foreign_equity_1 = $ircSecondAdhocData->finance_src_foreign_equity_1;
            $appData->finance_src_loc_total_equity_1 = $ircSecondAdhocData->finance_src_loc_total_equity_1;
            $appData->finance_src_loc_loan_1 = $ircSecondAdhocData->finance_src_loc_loan_1;
            $appData->finance_src_foreign_loan_1 = $ircSecondAdhocData->finance_src_foreign_loan_1;
            $appData->finance_src_total_loan = $ircSecondAdhocData->finance_src_total_loan;
            $appData->finance_src_loc_total_financing_1 = $ircSecondAdhocData->finance_src_loc_total_financing_1;
            $appData->finance_src_loc_total_financing_m = $ircSecondAdhocData->finance_src_loc_total_financing_m;

            $appData->annual_production_start_date = $ircSecondAdhocData->annual_production_start_date;
            $appData->import_cap_grd_total = $ircSecondAdhocData->import_cap_grd_total;
            $appData->import_cap_grd_total_wrd = $ircSecondAdhocData->import_cap_grd_total_wrd;

            $appData->ex_machine_imported_value_bdt = $ircSecondAdhocData->ex_machine_imported_value_bdt;
            $appData->ex_machine_local_value_bdt = $ircSecondAdhocData->ex_machine_local_value_bdt;
            $appData->ex_machine_total_value_bdt = $ircSecondAdhocData->ex_machine_total_value_bdt;
            $appData->ex_machine_attachment = $ircSecondAdhocData->ex_machine_attachment;

            $appData->import_duration_from_date = $ircSecondAdhocData->import_duration_from_date;
            $appData->import_duration_to_date = $ircSecondAdhocData->import_duration_to_date;
            $appData->import_total_price_usd = $ircSecondAdhocData->import_total_price_usd;
            $appData->import_total_price_bdt = $ircSecondAdhocData->import_total_price_bdt;
            $appData->import_attachment = $ircSecondAdhocData->import_attachment;

            $appData->production_duration_from_date = $ircSecondAdhocData->production_duration_from_date;
            $appData->production_duration_to_date = $ircSecondAdhocData->production_duration_to_date;
            $appData->production_total_quantity = $ircSecondAdhocData->production_total_quantity;
            $appData->production_total_sales = $ircSecondAdhocData->production_total_sales;
            $appData->production_total_stock = $ircSecondAdhocData->production_total_stock;
            $appData->production_attachment = $ircSecondAdhocData->production_attachment;

            $appData->sales_value_bdt_total = $ircSecondAdhocData->sales_value_bdt_total;
            $appData->sales_vat_total = $ircSecondAdhocData->sales_vat_total;

            $appData->export_duration_from_date = $ircSecondAdhocData->export_duration_from_date;
            $appData->export_duration_to_date = $ircSecondAdhocData->export_duration_to_date;
            $appData->export_total_price_usd = $ircSecondAdhocData->export_total_price_usd;
            $appData->export_total_price_bdt = $ircSecondAdhocData->export_total_price_bdt;
            $appData->export_attachment = $ircSecondAdhocData->export_attachment;

            if ($ircSecondAdhocData->irc_purpose_id != 2) {
                $appData->ins_apc_half_yearly_import_total = $ircSecondAdhocData->ins_apc_half_yearly_import_total;
                $appData->ins_apc_half_yearly_import_total_in_word = $ircSecondAdhocData->ins_apc_half_yearly_import_total_in_word;
            }

            $appData->public_land = $ircSecondAdhocData->public_land;
            $appData->public_electricity = $ircSecondAdhocData->public_electricity;
            $appData->public_gas = $ircSecondAdhocData->public_gas;
            $appData->public_telephone = $ircSecondAdhocData->public_telephone;
            $appData->public_road = $ircSecondAdhocData->public_road;
            $appData->public_water = $ircSecondAdhocData->public_water;
            $appData->public_drainage = $ircSecondAdhocData->public_drainage;
            $appData->public_others = $ircSecondAdhocData->public_others;
            $appData->public_others_field = $ircSecondAdhocData->public_others_field;

            $appData->trade_licence_num = $ircSecondAdhocData->trade_licence_num;
            $appData->trade_licence_issue_date = $ircSecondAdhocData->trade_licence_issue_date;
            $appData->trade_licence_issuing_authority = $ircSecondAdhocData->trade_licence_issuing_authority;
            $appData->trade_licence_validity_period = $ircSecondAdhocData->trade_licence_validity_period;

            $appData->tin_number = $ircSecondAdhocData->tin_number;
            $appData->tin_issuing_authority = $ircSecondAdhocData->tin_issuing_authority;

            $appData->inc_number = $ircSecondAdhocData->inc_number;
            $appData->inc_issuing_authority = $ircSecondAdhocData->inc_issuing_authority;


            $appData->fl_number = $ircSecondAdhocData->fl_number;
            $appData->fl_expire_date = $ircSecondAdhocData->fl_expire_date;
            $appData->fl_issuing_authority = $ircSecondAdhocData->fl_issuing_authority;

            $appData->el_number = $ircSecondAdhocData->el_number;
            $appData->el_expire_date = $ircSecondAdhocData->el_expire_date;
            $appData->el_issuing_authority = $ircSecondAdhocData->el_issuing_authority;

            $appData->bank_account_number = $ircSecondAdhocData->bank_account_number;
            $appData->bank_account_title = $ircSecondAdhocData->bank_account_title;
            $appData->bank_id = $ircSecondAdhocData->bank_id;
            $appData->branch_id = $ircSecondAdhocData->branch_id;
        //    $appData->bank_address = $ircSecondAdhocData->bank_address;

            $appData->assoc_membership_number = $ircSecondAdhocData->assoc_membership_number;
            $appData->assoc_chamber_name = $ircSecondAdhocData->assoc_chamber_name;
            $appData->assoc_issuing_date = $ircSecondAdhocData->assoc_issuing_date;
            $appData->assoc_expire_date = $ircSecondAdhocData->assoc_expire_date;

            $appData->bin_vat_number = $ircSecondAdhocData->bin_vat_number;
            $appData->bin_vat_issuing_authority = $ircSecondAdhocData->bin_vat_issuing_authority;
            $appData->bin_vat_issuing_date = $ircSecondAdhocData->bin_vat_issuing_date;
            $appData->bin_vat_expire_date = $ircSecondAdhocData->bin_vat_expire_date;

            if ($appData->irc_purpose_id != 1){
                $appData->first_em_lc_total_taka_mil = $ircSecondAdhocData->first_em_lc_total_taka_mil;
                $appData->first_em_lc_total_percent = $ircSecondAdhocData->first_em_lc_total_percent;
                $appData->first_em_lc_total_five_percent = $ircSecondAdhocData->first_em_lc_total_five_percent;
                $appData->first_em_lc_total_five_percent_in_word = $ircSecondAdhocData->first_em_lc_total_five_percent_in_word;

                $appData->second_em_lc_total_taka_mil = $ircSecondAdhocData->second_em_lc_total_taka_mil;
                $appData->second_em_lc_total_percent = $ircSecondAdhocData->second_em_lc_total_percent;
                $appData->second_em_lc_total_five_percent = $ircSecondAdhocData->second_em_lc_total_five_percent;
                $appData->second_em_lc_total_five_percent_in_word = $ircSecondAdhocData->second_em_lc_total_five_percent_in_word;
            }

            $appData->g_full_name = $ircSecondAdhocData->g_full_name;
            $appData->g_designation = $ircSecondAdhocData->g_designation;
            $appData->g_signature = $ircSecondAdhocData->g_signature;

            $appData->auth_full_name = $ircSecondAdhocData->auth_full_name;
            $appData->auth_designation = $ircSecondAdhocData->auth_designation;
            $appData->auth_mobile_no = $ircSecondAdhocData->auth_mobile_no;
            $appData->auth_email = $ircSecondAdhocData->auth_email;
            $appData->auth_image = $ircSecondAdhocData->auth_image;

            $appData->certificate_link = $ircSecondAdhocData->certificate_link;
            //$appData->list_of_dir_machinery_doc = $ircSecondAdhocData->list_of_dir_machinery_doc;

            $appData->accept_terms = $ircSecondAdhocData->accept_terms;
        //    $appData->inspection_gov_fee = $ircSecondAdhocData->inspection_gov_fee;
            $appData->sf_payment_id = $ircSecondAdhocData->sf_payment_id;
            $appData->gf_payment_id = $ircSecondAdhocData->gf_payment_id;
            $appData->gf_manual_payment_id = $ircSecondAdhocData->gf_manual_payment_id;
            $appData->payment_date = $ircSecondAdhocData->payment_date;
            $appData->approved_date = $ircSecondAdhocData->approved_date;

            $appData->save();

            return true;
        } catch (\Exception $e) {
            dd($e->getFile(), $e->getLine(), $e->getCode(), $e->getMessage());
            return false;
        }
    }

    public static function ircThirdAdhocDataStore($tracking_id, $ref_app_id)
    {
        try {
            $third_adhoc_inspection_id = ThirdIrcInspection::where('app_id', $ref_app_id)->where('ins_approved_status', 1)->pluck('id');
            $ircThirdAdhocData = IrcRecommendationThirdAdhoc::where('id', $ref_app_id)->first();

            $ref_service_name = UtilFunction::getRefAppServiceName($ircThirdAdhocData->irc_ref_app_tracking_no);

            if (!empty($ref_service_name)) {
                $appData = IRCCommonPool::firstOrNew([$ref_service_name => $ircThirdAdhocData->irc_ref_app_tracking_no]);
            } else {
                $appData = new IRCCommonPool();
            }

            $appData->third_adhoc_tracking_no = $tracking_id;
            $appData->third_adhoc_inspection_id = !empty($third_adhoc_inspection_id) ? $third_adhoc_inspection_id : null;

            $appData->company_id = $ircThirdAdhocData->company_id;
            $appData->app_type_id = $ircThirdAdhocData->app_type_id;
            $appData->irc_purpose_id = $ircThirdAdhocData->irc_purpose_id;
            $appData->agree_with_instruction = $ircThirdAdhocData->agree_with_instruction;
            $appData->last_br = $ircThirdAdhocData->last_br;

            if ($ircThirdAdhocData->last_br == 'yes') {
                $getBRCommonPoolId = BRCommonPool::where('br_tracking_no', $ircThirdAdhocData->br_ref_app_tracking_no)->pluck('id');
                $appData->br_ref_app_tracking_no = $ircThirdAdhocData->br_ref_app_tracking_no;
                $appData->br_ref_app_approve_date = $ircThirdAdhocData->br_ref_app_approve_date;
                $appData->br_common_pool_id = !empty($getBRCommonPoolId) ? $getBRCommonPoolId : null;
            }
            if ($ircThirdAdhocData->last_br == 'no') {
                $appData->br_manually_approved_no = $ircThirdAdhocData->br_manually_approved_no;
                $appData->br_manually_approved_date = $ircThirdAdhocData->br_manually_approved_date;
            }

            $appData->last_irc_2nd_adhoc = $ircThirdAdhocData->last_irc_2nd_adhoc;
            if ($ircThirdAdhocData->last_irc_2nd_adhoc == 'yes') {
                $appData->irc_2nd_ref_app_tracking_no = $ircThirdAdhocData->irc_ref_app_tracking_no;
                $appData->irc_2nd_ref_app_approve_date = $ircThirdAdhocData->irc_ref_app_approve_date;
            }
            if ($ircThirdAdhocData->last_irc_2nd_adhoc == 'no') {
                $appData->irc_2nd_manually_approved_no = $ircThirdAdhocData->irc_manually_approved_no;
                $appData->irc_2nd_manually_approved_date = $ircThirdAdhocData->irc_manually_approved_date;
            }

            $appData->io_submission_deadline = $ircThirdAdhocData->io_submission_deadline;

            // Company Info
            $appData->company_name = $ircThirdAdhocData->company_name;
            $appData->company_name_bn = $ircThirdAdhocData->company_name_bn;
            $appData->organization_type_id = $ircThirdAdhocData->organization_type_id;
            $appData->organization_status_id = $ircThirdAdhocData->organization_status_id;
            $appData->ownership_status_id = $ircThirdAdhocData->ownership_status_id;
            $appData->country_of_origin_id = $ircThirdAdhocData->country_of_origin_id;
            $appData->project_name = $ircThirdAdhocData->project_name;
            $appData->section_id = $ircThirdAdhocData->section_id;
            $appData->division_id = $ircThirdAdhocData->division_id;
            $appData->group_id = $ircThirdAdhocData->group_id;
            $appData->class_id = $ircThirdAdhocData->class_id;
            $appData->class_code = $ircThirdAdhocData->class_code;
            $appData->sub_class_id = $ircThirdAdhocData->sub_class_id;
            $appData->other_sub_class_code = $ircThirdAdhocData->other_sub_class_code;
            $appData->other_sub_class_name = $ircThirdAdhocData->other_sub_class_name;
            $appData->major_activities = $ircThirdAdhocData->major_activities;

            //CEO information
            $appData->ceo_country_id = $ircThirdAdhocData->ceo_country_id;
            $appData->ceo_dob = $ircThirdAdhocData->ceo_dob;
            $appData->ceo_passport_no = $ircThirdAdhocData->ceo_passport_no;
            $appData->ceo_nid = $ircThirdAdhocData->ceo_nid;
            $appData->ceo_full_name = $ircThirdAdhocData->ceo_full_name;
            $appData->ceo_designation = $ircThirdAdhocData->ceo_designation;
            $appData->ceo_district_id = $ircThirdAdhocData->ceo_district_id;
            $appData->ceo_city = $ircThirdAdhocData->ceo_city;
            $appData->ceo_state = $ircThirdAdhocData->ceo_state;
            $appData->ceo_thana_id = $ircThirdAdhocData->ceo_thana_id;
            $appData->ceo_post_code = $ircThirdAdhocData->ceo_post_code;
            $appData->ceo_address = $ircThirdAdhocData->ceo_address;
            $appData->ceo_telephone_no = $ircThirdAdhocData->ceo_telephone_no;
            $appData->ceo_mobile_no = $ircThirdAdhocData->ceo_mobile_no;
            $appData->ceo_fax_no = $ircThirdAdhocData->ceo_fax_no;
            $appData->ceo_email = $ircThirdAdhocData->ceo_email;
            $appData->ceo_father_name = $ircThirdAdhocData->ceo_father_name;
            $appData->ceo_mother_name = $ircThirdAdhocData->ceo_mother_name;
            $appData->ceo_spouse_name = $ircThirdAdhocData->ceo_spouse_name;
            $appData->ceo_gender = $ircThirdAdhocData->ceo_gender;

            // Office Address
            $appData->office_division_id = $ircThirdAdhocData->office_division_id;
            $appData->office_district_id = $ircThirdAdhocData->office_district_id;
            $appData->office_thana_id = $ircThirdAdhocData->office_thana_id;
            $appData->office_post_office = $ircThirdAdhocData->office_post_office;
            $appData->office_post_code = $ircThirdAdhocData->office_post_code;
            $appData->office_address = $ircThirdAdhocData->office_address;
            $appData->office_telephone_no = $ircThirdAdhocData->office_telephone_no;
            $appData->office_mobile_no = $ircThirdAdhocData->office_mobile_no;
            $appData->office_fax_no = $ircThirdAdhocData->office_fax_no;
            $appData->office_email = $ircThirdAdhocData->office_email;

            // Factory Address
            $appData->factory_district_id = $ircThirdAdhocData->factory_district_id;
            $appData->factory_thana_id = $ircThirdAdhocData->factory_thana_id;
            $appData->factory_post_office = $ircThirdAdhocData->factory_post_office;
            $appData->factory_post_code = $ircThirdAdhocData->factory_post_code;
            $appData->factory_address = $ircThirdAdhocData->factory_address;
            $appData->factory_telephone_no = $ircThirdAdhocData->factory_telephone_no;
            $appData->factory_mobile_no = $ircThirdAdhocData->factory_mobile_no;
            $appData->factory_fax_no = $ircThirdAdhocData->factory_fax_no;
            $appData->factory_email = $ircThirdAdhocData->factory_email;

            $appData->project_status_id = $ircThirdAdhocData->project_status_id;
            $appData->commercial_operation_date = $ircThirdAdhocData->commercial_operation_date;

            $appData->local_sales = $ircThirdAdhocData->local_sales;
            $appData->foreign_sales = $ircThirdAdhocData->foreign_sales;
            $appData->total_sales = $ircThirdAdhocData->total_sales;

            // Manpower section
            $appData->local_male = $ircThirdAdhocData->local_male;
            $appData->local_female = $ircThirdAdhocData->local_female;
            $appData->local_total = $ircThirdAdhocData->local_total;
            $appData->foreign_male = $ircThirdAdhocData->foreign_male;
            $appData->foreign_female = $ircThirdAdhocData->foreign_female;
            $appData->foreign_total = $ircThirdAdhocData->foreign_total;
            $appData->manpower_total = $ircThirdAdhocData->manpower_total;
            $appData->manpower_local_ratio = $ircThirdAdhocData->manpower_local_ratio;
            $appData->manpower_foreign_ratio = $ircThirdAdhocData->manpower_foreign_ratio;

            // Investment
            $appData->local_land_ivst = $ircThirdAdhocData->local_land_ivst;
            $appData->local_land_ivst_ccy = $ircThirdAdhocData->local_land_ivst_ccy;
            $appData->local_building_ivst = $ircThirdAdhocData->local_building_ivst;
            $appData->local_building_ivst_ccy = $ircThirdAdhocData->local_building_ivst_ccy;
            $appData->local_machinery_ivst = $ircThirdAdhocData->local_machinery_ivst;
            $appData->local_machinery_ivst_ccy = $ircThirdAdhocData->local_machinery_ivst_ccy;
            $appData->local_others_ivst = $ircThirdAdhocData->local_others_ivst;
            $appData->local_others_ivst_ccy = $ircThirdAdhocData->local_others_ivst_ccy;
            $appData->local_wc_ivst = $ircThirdAdhocData->local_wc_ivst;
            $appData->local_wc_ivst_ccy = $ircThirdAdhocData->local_wc_ivst_ccy;
            $appData->total_fixed_ivst_million = $ircThirdAdhocData->total_fixed_ivst_million;
            $appData->total_fixed_ivst = $ircThirdAdhocData->total_fixed_ivst;
            $appData->usd_exchange_rate = $ircThirdAdhocData->usd_exchange_rate;
            $appData->total_fee = $ircThirdAdhocData->total_fee;

            $appData->finance_src_loc_equity_1 = $ircThirdAdhocData->finance_src_loc_equity_1;
            $appData->finance_src_foreign_equity_1 = $ircThirdAdhocData->finance_src_foreign_equity_1;
            $appData->finance_src_loc_total_equity_1 = $ircThirdAdhocData->finance_src_loc_total_equity_1;
            $appData->finance_src_loc_loan_1 = $ircThirdAdhocData->finance_src_loc_loan_1;
            $appData->finance_src_foreign_loan_1 = $ircThirdAdhocData->finance_src_foreign_loan_1;
            $appData->finance_src_total_loan = $ircThirdAdhocData->finance_src_total_loan;
            $appData->finance_src_loc_total_financing_1 = $ircThirdAdhocData->finance_src_loc_total_financing_1;
            $appData->finance_src_loc_total_financing_m = $ircThirdAdhocData->finance_src_loc_total_financing_m;

            $appData->annual_production_start_date = $ircThirdAdhocData->annual_production_start_date;
            $appData->import_cap_grd_total = $ircThirdAdhocData->import_cap_grd_total;
            $appData->import_cap_grd_total_wrd = $ircThirdAdhocData->import_cap_grd_total_wrd;

            $appData->ex_machine_imported_value_bdt = $ircThirdAdhocData->ex_machine_imported_value_bdt;
            $appData->ex_machine_local_value_bdt = $ircThirdAdhocData->ex_machine_local_value_bdt;
            $appData->ex_machine_total_value_bdt = $ircThirdAdhocData->ex_machine_total_value_bdt;
            $appData->ex_machine_attachment = $ircThirdAdhocData->ex_machine_attachment;

            $appData->import_duration_from_date = $ircThirdAdhocData->import_duration_from_date;
            $appData->import_duration_to_date = $ircThirdAdhocData->import_duration_to_date;
            $appData->import_total_price_usd = $ircThirdAdhocData->import_total_price_usd;
            $appData->import_total_price_bdt = $ircThirdAdhocData->import_total_price_bdt;
            $appData->import_attachment = $ircThirdAdhocData->import_attachment;

            $appData->production_duration_from_date = $ircThirdAdhocData->production_duration_from_date;
            $appData->production_duration_to_date = $ircThirdAdhocData->production_duration_to_date;
            $appData->production_total_quantity = $ircThirdAdhocData->production_total_quantity;
            $appData->production_total_sales = $ircThirdAdhocData->production_total_sales;
            $appData->production_total_stock = $ircThirdAdhocData->production_total_stock;
            $appData->production_attachment = $ircThirdAdhocData->production_attachment;

            $appData->sales_value_bdt_total = $ircThirdAdhocData->sales_value_bdt_total;
            $appData->sales_vat_total = $ircThirdAdhocData->sales_vat_total;

            $appData->export_duration_from_date = $ircThirdAdhocData->export_duration_from_date;
            $appData->export_duration_to_date = $ircThirdAdhocData->export_duration_to_date;
            $appData->export_total_price_usd = $ircThirdAdhocData->export_total_price_usd;
            $appData->export_total_price_bdt = $ircThirdAdhocData->export_total_price_bdt;
            $appData->export_attachment = $ircThirdAdhocData->export_attachment;

            if ($ircThirdAdhocData->irc_purpose_id != 2) {
                $appData->ins_apc_half_yearly_import_total = $ircThirdAdhocData->ins_apc_half_yearly_import_total;
                $appData->ins_apc_half_yearly_import_total_in_word = $ircThirdAdhocData->ins_apc_half_yearly_import_total_in_word;
            }
            // if ($ircThirdAdhocData->irc_purpose_id != 1) {
            //     $appData->ins_apsp_half_yearly_import_total = $ircThirdAdhocData->ins_apsp_half_yearly_import_total;
            //     $appData->ins_apsp_half_yearly_import_total_in_word = $ircThirdAdhocData->ins_apsp_half_yearly_import_total_in_word;
            // }

            $appData->public_land = $ircThirdAdhocData->public_land;
            $appData->public_electricity = $ircThirdAdhocData->public_electricity;
            $appData->public_gas = $ircThirdAdhocData->public_gas;
            $appData->public_telephone = $ircThirdAdhocData->public_telephone;
            $appData->public_road = $ircThirdAdhocData->public_road;
            $appData->public_water = $ircThirdAdhocData->public_water;
            $appData->public_drainage = $ircThirdAdhocData->public_drainage;
            $appData->public_others = $ircThirdAdhocData->public_others;
            $appData->public_others_field = $ircThirdAdhocData->public_others_field;

            $appData->trade_licence_num = $ircThirdAdhocData->trade_licence_num;
            $appData->trade_licence_issue_date = $ircThirdAdhocData->trade_licence_issue_date;
            $appData->trade_licence_issuing_authority = $ircThirdAdhocData->trade_licence_issuing_authority;
            $appData->trade_licence_validity_period = $ircThirdAdhocData->trade_licence_validity_period;

            $appData->tin_number = $ircThirdAdhocData->tin_number;
            $appData->tin_issuing_authority = $ircThirdAdhocData->tin_issuing_authority;

            $appData->inc_number = $ircThirdAdhocData->inc_number;
            $appData->inc_issuing_authority = $ircThirdAdhocData->inc_issuing_authority;


            $appData->fl_number = $ircThirdAdhocData->fl_number;
            $appData->fl_expire_date = $ircThirdAdhocData->fl_expire_date;
            $appData->fl_issuing_authority = $ircThirdAdhocData->fl_issuing_authority;

            $appData->el_number = $ircThirdAdhocData->el_number;
            $appData->el_expire_date = $ircThirdAdhocData->el_expire_date;
            $appData->el_issuing_authority = $ircThirdAdhocData->el_issuing_authority;

            $appData->bank_account_number = $ircThirdAdhocData->bank_account_number;
            $appData->bank_account_title = $ircThirdAdhocData->bank_account_title;
            $appData->bank_id = $ircThirdAdhocData->bank_id;
            $appData->branch_id = $ircThirdAdhocData->branch_id;
            // $appData->bank_address = $ircSecondAdhocData->bank_address;

            $appData->assoc_membership_number = $ircThirdAdhocData->assoc_membership_number;
            $appData->assoc_chamber_name = $ircThirdAdhocData->assoc_chamber_name;
            $appData->assoc_issuing_date = $ircThirdAdhocData->assoc_issuing_date;
            $appData->assoc_expire_date = $ircThirdAdhocData->assoc_expire_date;

            $appData->bin_vat_number = $ircThirdAdhocData->bin_vat_number;
            $appData->bin_vat_issuing_authority = $ircThirdAdhocData->bin_vat_issuing_authority;
            $appData->bin_vat_issuing_date = $ircThirdAdhocData->bin_vat_issuing_date;
            $appData->bin_vat_expire_date = $ircThirdAdhocData->bin_vat_expire_date;

            if ($appData->irc_purpose_id != 1){
                $appData->first_em_lc_total_taka_mil = $ircThirdAdhocData->first_em_lc_total_taka_mil;
                $appData->first_em_lc_total_percent = $ircThirdAdhocData->first_em_lc_total_percent;
                $appData->first_em_lc_total_five_percent = $ircThirdAdhocData->first_em_lc_total_five_percent;
                $appData->first_em_lc_total_five_percent_in_word = $ircThirdAdhocData->first_em_lc_total_five_percent_in_word;

                $appData->second_em_lc_total_taka_mil = $ircThirdAdhocData->second_em_lc_total_taka_mil;
                $appData->second_em_lc_total_percent = $ircThirdAdhocData->second_em_lc_total_percent;
                $appData->second_em_lc_total_five_percent = $ircThirdAdhocData->second_em_lc_total_five_percent;
                $appData->second_em_lc_total_five_percent_in_word = $ircThirdAdhocData->second_em_lc_total_five_percent_in_word;
            }

            $appData->g_full_name = $ircThirdAdhocData->g_full_name;
            $appData->g_designation = $ircThirdAdhocData->g_designation;
            $appData->g_signature = $ircThirdAdhocData->g_signature;

            $appData->auth_full_name = $ircThirdAdhocData->auth_full_name;
            $appData->auth_designation = $ircThirdAdhocData->auth_designation;
            $appData->auth_mobile_no = $ircThirdAdhocData->auth_mobile_no;
            $appData->auth_email = $ircThirdAdhocData->auth_email;
            $appData->auth_image = $ircThirdAdhocData->auth_image;

            $appData->certificate_link = $ircThirdAdhocData->certificate_link;

            $appData->accept_terms = $ircThirdAdhocData->accept_terms;
            $appData->sf_payment_id = $ircThirdAdhocData->sf_payment_id;
            $appData->gf_payment_id = $ircThirdAdhocData->gf_payment_id;
            $appData->gf_manual_payment_id = $ircThirdAdhocData->gf_manual_payment_id;
            $appData->payment_date = $ircThirdAdhocData->payment_date;
            $appData->approved_date = $ircThirdAdhocData->approved_date;
            $appData->save();

            return true;
        } catch (\Exception $e) {
            dd($e->getFile(), $e->getLine(), $e->getCode(), $e->getMessage());
            return false;
        }
    }

    public static function ircRegularDataStore($tracking_id, $ref_app_id)
    {
        try {
            $regular_adhoc_inspection_id = RegularIrcInspection::where('app_id', $ref_app_id)->where('ins_approved_status', 1)->pluck('id');
            $ircRegularAdhocData = IrcRecommendationRegular::where('id', $ref_app_id)->first();

            $ref_service_name = UtilFunction::getRefAppServiceName($ircRegularAdhocData->irc_ref_app_tracking_no);

            if (!empty($ref_service_name)) {
                $appData = IRCCommonPool::firstOrNew([$ref_service_name => $ircRegularAdhocData->irc_ref_app_tracking_no]);
            } else {
                $appData = new IRCCommonPool();
            }

            $appData->regular_adhoc_tracking_no = $tracking_id;
            $appData->regular_adhoc_inspection_id = !empty($regular_adhoc_inspection_id) ? $regular_adhoc_inspection_id : null;

            $appData->company_id = $ircRegularAdhocData->company_id;
            $appData->app_type_id = $ircRegularAdhocData->app_type_id;
            $appData->irc_purpose_id = $ircRegularAdhocData->irc_purpose_id;
            $appData->agree_with_instruction = $ircRegularAdhocData->agree_with_instruction;
            $appData->last_br = $ircRegularAdhocData->last_br;

            if ($ircRegularAdhocData->last_br == 'yes') {
                $getBRCommonPoolId = BRCommonPool::where('br_tracking_no', $ircRegularAdhocData->br_ref_app_tracking_no)->pluck('id');
                $appData->br_ref_app_tracking_no = $ircRegularAdhocData->br_ref_app_tracking_no;
                $appData->br_ref_app_approve_date = $ircRegularAdhocData->br_ref_app_approve_date;
                $appData->br_common_pool_id = !empty($getBRCommonPoolId) ? $getBRCommonPoolId : null;
            }
            if ($ircRegularAdhocData->last_br == 'no') {
                $appData->br_manually_approved_no = $ircRegularAdhocData->br_manually_approved_no;
                $appData->br_manually_approved_date = $ircRegularAdhocData->br_manually_approved_date;
            }

            $appData->last_irc_2nd_adhoc = $ircRegularAdhocData->last_irc_2nd_adhoc;
            if ($ircRegularAdhocData->last_irc_2nd_adhoc == 'yes') {
                $appData->irc_2nd_ref_app_tracking_no = $ircRegularAdhocData->irc_ref_app_tracking_no;
                $appData->irc_2nd_ref_app_approve_date = $ircRegularAdhocData->irc_ref_app_approve_date;
            }
            if ($ircRegularAdhocData->last_irc_2nd_adhoc == 'no') {
                $appData->irc_2nd_manually_approved_no = $ircRegularAdhocData->irc_manually_approved_no;
                $appData->irc_2nd_manually_approved_date = $ircRegularAdhocData->irc_manually_approved_date;
            }

            $appData->io_submission_deadline = $ircRegularAdhocData->io_submission_deadline;

            // Company Info
            $appData->company_name = $ircRegularAdhocData->company_name;
            $appData->company_name_bn = $ircRegularAdhocData->company_name_bn;
            $appData->organization_type_id = $ircRegularAdhocData->organization_type_id;
            $appData->organization_status_id = $ircRegularAdhocData->organization_status_id;
            $appData->ownership_status_id = $ircRegularAdhocData->ownership_status_id;
            $appData->country_of_origin_id = $ircRegularAdhocData->country_of_origin_id;
            $appData->project_name = $ircRegularAdhocData->project_name;
            $appData->section_id = $ircRegularAdhocData->section_id;
            $appData->division_id = $ircRegularAdhocData->division_id;
            $appData->group_id = $ircRegularAdhocData->group_id;
            $appData->class_id = $ircRegularAdhocData->class_id;
            $appData->class_code = $ircRegularAdhocData->class_code;
            $appData->sub_class_id = $ircRegularAdhocData->sub_class_id;
            $appData->other_sub_class_code = $ircRegularAdhocData->other_sub_class_code;
            $appData->other_sub_class_name = $ircRegularAdhocData->other_sub_class_name;
            $appData->major_activities = $ircRegularAdhocData->major_activities;

            //CEO information
            $appData->ceo_country_id = $ircRegularAdhocData->ceo_country_id;
            $appData->ceo_dob = $ircRegularAdhocData->ceo_dob;
            $appData->ceo_passport_no = $ircRegularAdhocData->ceo_passport_no;
            $appData->ceo_nid = $ircRegularAdhocData->ceo_nid;
            $appData->ceo_full_name = $ircRegularAdhocData->ceo_full_name;
            $appData->ceo_designation = $ircRegularAdhocData->ceo_designation;
            $appData->ceo_district_id = $ircRegularAdhocData->ceo_district_id;
            $appData->ceo_city = $ircRegularAdhocData->ceo_city;
            $appData->ceo_state = $ircRegularAdhocData->ceo_state;
            $appData->ceo_thana_id = $ircRegularAdhocData->ceo_thana_id;
            $appData->ceo_post_code = $ircRegularAdhocData->ceo_post_code;
            $appData->ceo_address = $ircRegularAdhocData->ceo_address;
            $appData->ceo_telephone_no = $ircRegularAdhocData->ceo_telephone_no;
            $appData->ceo_mobile_no = $ircRegularAdhocData->ceo_mobile_no;
            $appData->ceo_fax_no = $ircRegularAdhocData->ceo_fax_no;
            $appData->ceo_email = $ircRegularAdhocData->ceo_email;
            $appData->ceo_father_name = $ircRegularAdhocData->ceo_father_name;
            $appData->ceo_mother_name = $ircRegularAdhocData->ceo_mother_name;
            $appData->ceo_spouse_name = $ircRegularAdhocData->ceo_spouse_name;
            $appData->ceo_gender = $ircRegularAdhocData->ceo_gender;

            // Office Address
            $appData->office_division_id = $ircRegularAdhocData->office_division_id;
            $appData->office_district_id = $ircRegularAdhocData->office_district_id;
            $appData->office_thana_id = $ircRegularAdhocData->office_thana_id;
            $appData->office_post_office = $ircRegularAdhocData->office_post_office;
            $appData->office_post_code = $ircRegularAdhocData->office_post_code;
            $appData->office_address = $ircRegularAdhocData->office_address;
            $appData->office_telephone_no = $ircRegularAdhocData->office_telephone_no;
            $appData->office_mobile_no = $ircRegularAdhocData->office_mobile_no;
            $appData->office_fax_no = $ircRegularAdhocData->office_fax_no;
            $appData->office_email = $ircRegularAdhocData->office_email;

            // Factory Address
            $appData->factory_district_id = $ircRegularAdhocData->factory_district_id;
            $appData->factory_thana_id = $ircRegularAdhocData->factory_thana_id;
            $appData->factory_post_office = $ircRegularAdhocData->factory_post_office;
            $appData->factory_post_code = $ircRegularAdhocData->factory_post_code;
            $appData->factory_address = $ircRegularAdhocData->factory_address;
            $appData->factory_telephone_no = $ircRegularAdhocData->factory_telephone_no;
            $appData->factory_mobile_no = $ircRegularAdhocData->factory_mobile_no;
            $appData->factory_fax_no = $ircRegularAdhocData->factory_fax_no;
            $appData->factory_email = $ircRegularAdhocData->factory_email;

            $appData->project_status_id = $ircRegularAdhocData->project_status_id;
            $appData->commercial_operation_date = $ircRegularAdhocData->commercial_operation_date;

            $appData->local_sales = $ircRegularAdhocData->local_sales;
            $appData->foreign_sales = $ircRegularAdhocData->foreign_sales;
            $appData->total_sales = $ircRegularAdhocData->total_sales;

            // Manpower section
            $appData->local_male = $ircRegularAdhocData->local_male;
            $appData->local_female = $ircRegularAdhocData->local_female;
            $appData->local_total = $ircRegularAdhocData->local_total;
            $appData->foreign_male = $ircRegularAdhocData->foreign_male;
            $appData->foreign_female = $ircRegularAdhocData->foreign_female;
            $appData->foreign_total = $ircRegularAdhocData->foreign_total;
            $appData->manpower_total = $ircRegularAdhocData->manpower_total;
            $appData->manpower_local_ratio = $ircRegularAdhocData->manpower_local_ratio;
            $appData->manpower_foreign_ratio = $ircRegularAdhocData->manpower_foreign_ratio;

            // Investment
            $appData->local_land_ivst = $ircRegularAdhocData->local_land_ivst;
            $appData->local_land_ivst_ccy = $ircRegularAdhocData->local_land_ivst_ccy;
            $appData->local_building_ivst = $ircRegularAdhocData->local_building_ivst;
            $appData->local_building_ivst_ccy = $ircRegularAdhocData->local_building_ivst_ccy;
            $appData->local_machinery_ivst = $ircRegularAdhocData->local_machinery_ivst;
            $appData->local_machinery_ivst_ccy = $ircRegularAdhocData->local_machinery_ivst_ccy;
            $appData->local_others_ivst = $ircRegularAdhocData->local_others_ivst;
            $appData->local_others_ivst_ccy = $ircRegularAdhocData->local_others_ivst_ccy;
            $appData->local_wc_ivst = $ircRegularAdhocData->local_wc_ivst;
            $appData->local_wc_ivst_ccy = $ircRegularAdhocData->local_wc_ivst_ccy;
            $appData->total_fixed_ivst_million = $ircRegularAdhocData->total_fixed_ivst_million;
            $appData->total_fixed_ivst = $ircRegularAdhocData->total_fixed_ivst;
            $appData->usd_exchange_rate = $ircRegularAdhocData->usd_exchange_rate;
            $appData->total_fee = $ircRegularAdhocData->total_fee;

            $appData->finance_src_loc_equity_1 = $ircRegularAdhocData->finance_src_loc_equity_1;
            $appData->finance_src_foreign_equity_1 = $ircRegularAdhocData->finance_src_foreign_equity_1;
            $appData->finance_src_loc_total_equity_1 = $ircRegularAdhocData->finance_src_loc_total_equity_1;
            $appData->finance_src_loc_loan_1 = $ircRegularAdhocData->finance_src_loc_loan_1;
            $appData->finance_src_foreign_loan_1 = $ircRegularAdhocData->finance_src_foreign_loan_1;
            $appData->finance_src_total_loan = $ircRegularAdhocData->finance_src_total_loan;
            $appData->finance_src_loc_total_financing_1 = $ircRegularAdhocData->finance_src_loc_total_financing_1;
            $appData->finance_src_loc_total_financing_m = $ircRegularAdhocData->finance_src_loc_total_financing_m;

            $appData->annual_production_start_date = $ircRegularAdhocData->annual_production_start_date;
            $appData->import_cap_grd_total = $ircRegularAdhocData->import_cap_grd_total;
            $appData->import_cap_grd_total_wrd = $ircRegularAdhocData->import_cap_grd_total_wrd;

            $appData->ex_machine_imported_value_bdt = $ircRegularAdhocData->ex_machine_imported_value_bdt;
            $appData->ex_machine_local_value_bdt = $ircRegularAdhocData->ex_machine_local_value_bdt;
            $appData->ex_machine_total_value_bdt = $ircRegularAdhocData->ex_machine_total_value_bdt;
            $appData->ex_machine_attachment = $ircRegularAdhocData->ex_machine_attachment;

            $appData->import_duration_from_date = $ircRegularAdhocData->import_duration_from_date;
            $appData->import_duration_to_date = $ircRegularAdhocData->import_duration_to_date;
            $appData->import_total_price_usd = $ircRegularAdhocData->import_total_price_usd;
            $appData->import_total_price_bdt = $ircRegularAdhocData->import_total_price_bdt;
            $appData->import_attachment = $ircRegularAdhocData->import_attachment;

            $appData->production_duration_from_date = $ircRegularAdhocData->production_duration_from_date;
            $appData->production_duration_to_date = $ircRegularAdhocData->production_duration_to_date;
            $appData->production_total_quantity = $ircRegularAdhocData->production_total_quantity;
            $appData->production_total_sales = $ircRegularAdhocData->production_total_sales;
            $appData->production_total_stock = $ircRegularAdhocData->production_total_stock;
            $appData->production_attachment = $ircRegularAdhocData->production_attachment;

            $appData->sales_value_bdt_total = $ircRegularAdhocData->sales_value_bdt_total;
            $appData->sales_vat_total = $ircRegularAdhocData->sales_vat_total;

            $appData->export_duration_from_date = $ircRegularAdhocData->export_duration_from_date;
            $appData->export_duration_to_date = $ircRegularAdhocData->export_duration_to_date;
            $appData->export_total_price_usd = $ircRegularAdhocData->export_total_price_usd;
            $appData->export_total_price_bdt = $ircRegularAdhocData->export_total_price_bdt;
            $appData->export_attachment = $ircRegularAdhocData->export_attachment;

            if ($ircRegularAdhocData->irc_purpose_id != 2) {
                $appData->ins_apc_half_yearly_import_total = $ircRegularAdhocData->ins_apc_half_yearly_import_total;
                $appData->ins_apc_half_yearly_import_total_in_word = $ircRegularAdhocData->ins_apc_half_yearly_import_total_in_word;
            }
            // if ($ircRegularAdhocData->irc_purpose_id != 1) {
            //     $appData->ins_apsp_half_yearly_import_total = $ircRegularAdhocData->ins_apsp_half_yearly_import_total;
            //     $appData->ins_apsp_half_yearly_import_total_in_word = $ircRegularAdhocData->ins_apsp_half_yearly_import_total_in_word;
            // }

            $appData->public_land = $ircRegularAdhocData->public_land;
            $appData->public_electricity = $ircRegularAdhocData->public_electricity;
            $appData->public_gas = $ircRegularAdhocData->public_gas;
            $appData->public_telephone = $ircRegularAdhocData->public_telephone;
            $appData->public_road = $ircRegularAdhocData->public_road;
            $appData->public_water = $ircRegularAdhocData->public_water;
            $appData->public_drainage = $ircRegularAdhocData->public_drainage;
            $appData->public_others = $ircRegularAdhocData->public_others;
            $appData->public_others_field = $ircRegularAdhocData->public_others_field;

            $appData->trade_licence_num = $ircRegularAdhocData->trade_licence_num;
            $appData->trade_licence_issue_date = $ircRegularAdhocData->trade_licence_issue_date;
            $appData->trade_licence_issuing_authority = $ircRegularAdhocData->trade_licence_issuing_authority;
            $appData->trade_licence_validity_period = $ircRegularAdhocData->trade_licence_validity_period;

            $appData->tin_number = $ircRegularAdhocData->tin_number;
            $appData->tin_issuing_authority = $ircRegularAdhocData->tin_issuing_authority;

            $appData->inc_number = $ircRegularAdhocData->inc_number;
            $appData->inc_issuing_authority = $ircRegularAdhocData->inc_issuing_authority;

            $appData->fl_number = $ircRegularAdhocData->fl_number;
            $appData->fl_expire_date = $ircRegularAdhocData->fl_expire_date;
            $appData->fl_issuing_authority = $ircRegularAdhocData->fl_issuing_authority;

            $appData->el_number = $ircRegularAdhocData->el_number;
            $appData->el_expire_date = $ircRegularAdhocData->el_expire_date;
            $appData->el_issuing_authority = $ircRegularAdhocData->el_issuing_authority;

            $appData->bank_account_number = $ircRegularAdhocData->bank_account_number;
            $appData->bank_account_title = $ircRegularAdhocData->bank_account_title;
            $appData->bank_id = $ircRegularAdhocData->bank_id;
            $appData->branch_id = $ircRegularAdhocData->branch_id;
            // $appData->bank_address = $ircSecondAdhocData->bank_address;

            $appData->assoc_membership_number = $ircRegularAdhocData->assoc_membership_number;
            $appData->assoc_chamber_name = $ircRegularAdhocData->assoc_chamber_name;
            $appData->assoc_issuing_date = $ircRegularAdhocData->assoc_issuing_date;
            $appData->assoc_expire_date = $ircRegularAdhocData->assoc_expire_date;

            $appData->bin_vat_number = $ircRegularAdhocData->bin_vat_number;
            $appData->bin_vat_issuing_authority = $ircRegularAdhocData->bin_vat_issuing_authority;
            $appData->bin_vat_issuing_date = $ircRegularAdhocData->bin_vat_issuing_date;
            $appData->bin_vat_expire_date = $ircRegularAdhocData->bin_vat_expire_date;

            if ($appData->irc_purpose_id != 1){
                $appData->first_em_lc_total_taka_mil = $ircRegularAdhocData->first_em_lc_total_taka_mil;
                $appData->first_em_lc_total_percent = $ircRegularAdhocData->first_em_lc_total_percent;
                $appData->first_em_lc_total_five_percent = $ircRegularAdhocData->first_em_lc_total_five_percent;
                $appData->first_em_lc_total_five_percent_in_word = $ircRegularAdhocData->first_em_lc_total_five_percent_in_word;

                $appData->second_em_lc_total_taka_mil = $ircRegularAdhocData->second_em_lc_total_taka_mil;
                $appData->second_em_lc_total_percent = $ircRegularAdhocData->second_em_lc_total_percent;
                $appData->second_em_lc_total_five_percent = $ircRegularAdhocData->second_em_lc_total_five_percent;
                $appData->second_em_lc_total_five_percent_in_word = $ircRegularAdhocData->second_em_lc_total_five_percent_in_word;
            }

            $appData->g_full_name = $ircRegularAdhocData->g_full_name;
            $appData->g_designation = $ircRegularAdhocData->g_designation;
            $appData->g_signature = $ircRegularAdhocData->g_signature;

            $appData->auth_full_name = $ircRegularAdhocData->auth_full_name;
            $appData->auth_designation = $ircRegularAdhocData->auth_designation;
            $appData->auth_mobile_no = $ircRegularAdhocData->auth_mobile_no;
            $appData->auth_email = $ircRegularAdhocData->auth_email;
            $appData->auth_image = $ircRegularAdhocData->auth_image;

            $appData->certificate_link = $ircRegularAdhocData->certificate_link;

            $appData->accept_terms = $ircRegularAdhocData->accept_terms;
            $appData->sf_payment_id = $ircRegularAdhocData->sf_payment_id;
            $appData->gf_payment_id = $ircRegularAdhocData->gf_payment_id;
            $appData->gf_manual_payment_id = $ircRegularAdhocData->gf_manual_payment_id;
            $appData->payment_date = $ircRegularAdhocData->payment_date;
            $appData->approved_date = $ircRegularAdhocData->approved_date;
            $appData->save();

            return true;
        } catch (\Exception $e) {
            // dd($e->getFile(), $e->getLine(), $e->getCode(), $e->getMessage());
            Log::error('ShowAppForm : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [IRC-Reg-1000]');
            return response()->json(['responseCode' => 1, 'html' => CommonFunction::showErrorPublic($e->getMessage()) . ' [IRC-Reg-1000]']);
            return false;
        }
    }

}