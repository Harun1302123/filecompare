<?php


namespace App\Modules\ProcessPath\Services;

use Carbon\Carbon;
use Exception;
use App\BRCommonPool;
use App\Libraries\UtilFunction;
use App\Modules\BidaRegistration\Models\BidaRegistration;
use App\Modules\BidaRegistrationAmendment\Models\BidaRegistrationAmendment;
use App\Modules\BidaRegistrationAmendment\Models\ListOfMachineryImported;
use App\Modules\ImportPermission\Models\MasterMachineryImported;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class BRCommonPoolManager
{
    public static function BRDataStore($tracking_no, $ref_id)
    {
        try {
            DB::beginTransaction();

            //Fetch br_apps data according to ref_id
            $brData = BidaRegistration::where('id', $ref_id)->first();
            $appData = new BRCommonPool();

            $appData->br_tracking_no = $tracking_no;
            $appData->reg_no = $brData->reg_no;
            $appData->br_approved_date = $brData->approved_date;

            //Company information
            $appData->company_id = $brData->company_id;
            $appData->company_name = $brData->company_name;
            $appData->company_name_bn = $brData->company_name_bn;
            $appData->organization_type_id = $brData->organization_type_id;
            $appData->organization_status_id = $brData->organization_status_id;
            $appData->ownership_status_id = $brData->ownership_status_id;
            $appData->country_of_origin_id = $brData->country_of_origin_id;
            $appData->project_name = $brData->project_name;
            $appData->major_activities = $brData->major_activities;

            //Business class
            $appData->section_id = $brData->section_id;
            $appData->division_id = $brData->division_id;
            $appData->group_id = $brData->group_id;
            $appData->class_id = $brData->class_id;
            $appData->class_code = $brData->class_code;
            $appData->sub_class_id = $brData->sub_class_id;
            $appData->other_sub_class_code	 = $brData->other_sub_class_code	;
            $appData->other_sub_class_name = $brData->other_sub_class_name;

            //CEO information
            $appData->ceo_full_name = $brData->ceo_full_name;
            $appData->ceo_dob = date('Y-m-d', strtotime($brData->ceo_dob));
            $appData->ceo_spouse_name = $brData->ceo_spouse_name;
            $appData->ceo_designation = $brData->ceo_designation;
            $appData->ceo_country_id = $brData->ceo_country_id;
            $appData->ceo_district_id = $brData->ceo_district_id;
            $appData->ceo_thana_id = $brData->ceo_thana_id;
            $appData->ceo_post_code = $brData->ceo_post_code;
            $appData->ceo_address = $brData->ceo_address;
            $appData->ceo_telephone_no = $brData->ceo_telephone_no;
            $appData->ceo_mobile_no = $brData->ceo_mobile_no;
            $appData->ceo_fax_no = $brData->ceo_fax_no;
            $appData->ceo_email = $brData->ceo_email;
            $appData->ceo_father_name = $brData->ceo_father_name;
            $appData->ceo_mother_name = $brData->ceo_mother_name;
            $appData->ceo_nid = $brData->ceo_nid;
            $appData->ceo_passport_no = $brData->ceo_passport_no;
            $appData->ceo_city = $brData->ceo_city;
            $appData->ceo_state = $brData->ceo_state;
            $appData->ceo_gender = $brData->ceo_gender;

            //Office Address
            $appData->office_division_id = $brData->office_division_id;
            $appData->office_district_id = $brData->office_district_id;
            $appData->office_thana_id = $brData->office_thana_id;
            $appData->office_post_office = $brData->office_post_office;
            $appData->office_post_code = $brData->office_post_code;
            $appData->office_address = $brData->office_address;
            $appData->office_telephone_no = $brData->office_telephone_no;
            $appData->office_mobile_no = $brData->office_mobile_no;
            $appData->office_fax_no = $brData->office_fax_no;
            $appData->office_email = $brData->office_email;

            //Factory Address
            $appData->factory_district_id = $brData->factory_district_id;
            $appData->factory_thana_id = $brData->factory_thana_id;
            $appData->factory_post_office = $brData->factory_post_office;
            $appData->factory_post_code = $brData->factory_post_code;
            $appData->factory_address = $brData->factory_address;
            $appData->factory_telephone_no = $brData->factory_telephone_no;
            $appData->factory_mobile_no = $brData->factory_mobile_no;
            $appData->factory_fax_no = $brData->factory_fax_no;

            //Project status
            $appData->project_status_id = $brData->project_status_id;

            //Date of commercial operation
            $appData->commercial_operation_date = $brData->commercial_operation_date;

            //Sales
            $appData->local_sales = $brData->local_sales;
            $appData->foreign_sales = $brData->foreign_sales;
            $appData->total_sales = $brData->total_sales;

            //Manpower of the organization
            $appData->local_male = $brData->local_male;
            $appData->local_female = $brData->local_female;
            $appData->local_total = $brData->local_total;
            $appData->foreign_male = $brData->foreign_male;
            $appData->foreign_female = $brData->foreign_female;
            $appData->foreign_total = $brData->foreign_total;
            $appData->manpower_total = $brData->manpower_total;
            $appData->manpower_local_ratio = $brData->manpower_local_ratio;
            $appData->manpower_foreign_ratio = $brData->manpower_foreign_ratio;

            //Investment
            $appData->local_land_ivst = $brData->local_land_ivst;
            $appData->local_land_ivst_ccy = $brData->local_land_ivst_ccy;
            $appData->local_building_ivst = $brData->local_building_ivst;
            $appData->local_building_ivst_ccy = $brData->local_building_ivst_ccy;
            $appData->local_machinery_ivst = $brData->local_machinery_ivst;
            $appData->local_machinery_ivst_ccy = $brData->local_machinery_ivst_ccy;
            $appData->local_others_ivst = $brData->local_others_ivst;
            $appData->local_others_ivst_ccy = $brData->local_others_ivst_ccy;
            $appData->local_wc_ivst = $brData->local_wc_ivst;
            $appData->local_wc_ivst_ccy = $brData->local_wc_ivst_ccy;
            $appData->total_fixed_ivst_million = $brData->total_fixed_ivst_million;
            $appData->total_fixed_ivst = $brData->total_fixed_ivst;
            $appData->usd_exchange_rate = $brData->usd_exchange_rate;
            $appData->total_fee = $brData->total_fee;

            $appData->project_profile_attachment = !empty($brData->project_profile_attachment) ? $brData->project_profile_attachment : null;

            //Source of finance
            $appData->finance_src_loc_equity_1 = $brData->finance_src_loc_equity_1;
            $appData->finance_src_foreign_equity_1 = $brData->finance_src_foreign_equity_1;
            $appData->finance_src_loc_total_equity_1 = $brData->finance_src_loc_total_equity_1;
            $appData->finance_src_loc_loan_1 = $brData->finance_src_loc_loan_1;
            $appData->finance_src_foreign_loan_1 = $brData->finance_src_foreign_loan_1;
            $appData->finance_src_total_loan = $brData->finance_src_total_loan;
            $appData->finance_src_loc_total_financing_m = $brData->finance_src_loc_total_financing_m;
            $appData->finance_src_loc_total_financing_1 = $brData->finance_src_loc_total_financing_1;

            //Public utility service
            $appData->public_land = $brData->public_land;
            $appData->public_electricity = $brData->public_electricity;
            $appData->public_gas = $brData->public_gas;
            $appData->public_telephone = $brData->public_telephone;
            $appData->public_road = $brData->public_road;
            $appData->public_water = $brData->public_water;
            $appData->public_drainage = $brData->public_drainage;
            $appData->public_others = $brData->public_others;
            $appData->public_others_field = $brData->public_others_field;

            //Trade licence details
            $appData->trade_licence_num = $brData->trade_licence_num;
            $appData->trade_licence_issuing_authority = $brData->trade_licence_issuing_authority;

            //Tin
            $appData->tin_number = $brData->tin_number;

            //Description of machinery and equipment
            $appData->machinery_local_qty = $brData->machinery_local_qty;
            $appData->machinery_local_price_bdt = $brData->machinery_local_price_bdt;
            $appData->imported_qty = $brData->imported_qty;
            $appData->imported_qty_price_bdt = $brData->imported_qty_price_bdt;
            $appData->total_machinery_price = $brData->total_machinery_price;
            $appData->total_machinery_qty = $brData->total_machinery_qty;

            //Description of raw &amp; packing materials
            $appData->local_description = $brData->local_description;
            $appData->imported_description = $brData->imported_description;

            $appData->list_of_dir_machinery_doc = $brData->list_of_dir_machinery_doc;

            //Information of (Chairman/ Managing Director/ Or Equivalent)
            $appData->g_full_name = $brData->g_full_name;
            $appData->g_designation = $brData->g_designation;
            $appData->g_signature = $brData->g_signature;

            //Authorized Person Information
            $appData->auth_full_name = $brData->auth_full_name;
            $appData->auth_designation = $brData->auth_designation;
            $appData->auth_email = $brData->auth_email;
            $appData->auth_mobile_no = $brData->auth_mobile_no;
            $appData->auth_image = $brData->auth_image;
            $appData->accept_terms = $brData->accept_terms;

            //Shortfall review sections
            $appData->company_info_review = $brData->company_info_review;
            $appData->promoter_info_review = $brData->promoter_info_review;
            $appData->office_address_review = $brData->office_address_review;
            $appData->factory_address_review = $brData->factory_address_review;
            $appData->project_status_review = $brData->project_status_review;
            $appData->production_capacity_review = $brData->production_capacity_review;
            $appData->commercial_operation_review = $brData->commercial_operation_review;
            $appData->sales_info_review = $brData->sales_info_review;
            $appData->manpower_review = $brData->manpower_review;
            $appData->investment_review = $brData->investment_review;
            $appData->source_finance_review = $brData->source_finance_review;
            $appData->utility_service_review = $brData->utility_service_review;
            $appData->trade_license_review = $brData->trade_license_review;
            $appData->tin_review = $brData->tin_review;
            $appData->machinery_equipment_review = $brData->machinery_equipment_review;
            $appData->raw_materials_review = $brData->raw_materials_review;
            $appData->ceo_info_review = $brData->ceo_info_review;
            $appData->director_list_review = $brData->director_list_review;
            $appData->imported_machinery_review = $brData->imported_machinery_review;
            $appData->local_machinery_review = $brData->local_machinery_review;
            $appData->attachment_review = $brData->attachment_review;
            $appData->declaration_review = $brData->declaration_review;

            $appData->sf_payment_id = $brData->sf_payment_id;
            $appData->gf_payment_id = $brData->gf_payment_id;
            $appData->certificate_link = $brData->certificate_link;
            $appData->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public static function BRADataStore($tracking_no, $ref_id)
    {
        try {
            DB::beginTransaction();
            $braData = BidaRegistrationAmendment::where('id', $ref_id)->first();
            $ref_service_name = UtilFunction::getRefAppServiceName($braData->ref_app_tracking_no);

            if (!empty($ref_service_name)) {
                $appData = BRCommonPool::firstOrNew([$ref_service_name => $braData->ref_app_tracking_no]);
                $appData->ref_app_tracking_no = $braData->ref_app_tracking_no;
                $appData->ref_app_approve_date = $braData->ref_app_approve_date;

            } else {
                $appData = new BRCommonPool();
                $appData->manually_approved_br_no = $braData->manually_approved_br_no;
                $appData->manually_approved_br_date = $braData->manually_approved_br_date;
            }

            $appData->bra_tracking_no = $tracking_no;
            $appData->bra_approved_date = $braData->approved_date;

            // Company information
            $appData->company_id = $braData->company_id;
            $appData->company_name = !empty($braData->n_company_name) ? $braData->n_company_name : $braData->company_name;
            $appData->company_name_bn = !empty($braData->n_company_name_bn) ? $braData->n_company_name_bn : $braData->company_name_bn;
            $appData->organization_type_id = !empty($braData->n_organization_type_id) ? $braData->n_organization_type_id : $braData->organization_type_id;
            $appData->organization_status_id = !empty($braData->n_organization_status_id) ? $braData->n_organization_status_id : $braData->organization_status_id;
            $appData->ownership_status_id = !empty($braData->n_ownership_status_id) ? $braData->n_ownership_status_id : $braData->ownership_status_id;
            $appData->country_of_origin_id = !empty($braData->n_country_of_origin_id) ? $braData->n_country_of_origin_id : $braData->country_of_origin_id;
            $appData->project_name = !empty($braData->n_project_name) ? $braData->n_project_name : $braData->project_name;
            
            //Business class
            $appData->section_id = !empty($braData->n_section_id) ? $braData->n_section_id : $braData->section_id;
            $appData->division_id = !empty($braData->n_division_id) ? $braData->n_division_id : $braData->division_id;
            $appData->group_id = !empty($braData->n_group_id) ? $braData->n_group_id : $braData->group_id;
            $appData->class_id = !empty($braData->n_class_id) ? $braData->n_class_id : $braData->class_id;
            $appData->class_code = !empty($braData->n_class_code) ? $braData->n_class_code : $braData->class_code;
            $appData->sub_class_id = isset($braData->n_sub_class_id) ? $braData->n_sub_class_id : $braData->sub_class_id;
            $appData->other_sub_class_code = !empty($braData->n_other_sub_class_code) ? $braData->n_other_sub_class_code : $braData->other_sub_class_code;
            $appData->other_sub_class_name = !empty($braData->n_other_sub_class_name) ? $braData->n_other_sub_class_name : $braData->other_sub_class_name;
            $appData->other_sub_class_name = !empty($braData->n_other_sub_class_name) ? $braData->n_other_sub_class_name : $braData->other_sub_class_name;

            // CEO information
            $appData->ceo_full_name = !empty($braData->n_ceo_full_name) ? $braData->n_ceo_full_name : $braData->ceo_full_name;
            $appData->ceo_dob = !empty($braData->n_ceo_dob) ? $braData->n_ceo_dob : $braData->ceo_dob;
            $appData->ceo_spouse_name = !empty($braData->n_ceo_spouse_name) ? $braData->n_ceo_spouse_name : $braData->ceo_spouse_name;
            $appData->ceo_designation = !empty($braData->n_ceo_designation) ? $braData->n_ceo_designation : $braData->ceo_designation;
            $appData->ceo_country_id = !empty($braData->n_ceo_country_id) ? $braData->n_ceo_country_id : $braData->ceo_country_id;
            $appData->ceo_district_id = !empty($braData->n_ceo_district_id) ? $braData->n_ceo_district_id : $braData->ceo_district_id;
            $appData->ceo_thana_id = !empty($braData->n_ceo_thana_id) ? $braData->n_ceo_thana_id : $braData->ceo_thana_id;
            $appData->ceo_post_code = !empty($braData->n_ceo_post_code) ? $braData->n_ceo_post_code : $braData->ceo_post_code;
            $appData->ceo_address = !empty($braData->n_ceo_address) ? $braData->n_ceo_address : $braData->ceo_address;
            $appData->ceo_telephone_no = !empty($braData->n_ceo_telephone_no) ? $braData->n_ceo_telephone_no : $braData->ceo_telephone_no;
            $appData->ceo_mobile_no = !empty($braData->n_ceo_mobile_no) ? $braData->n_ceo_mobile_no : $braData->ceo_mobile_no;
            $appData->ceo_fax_no = !empty($braData->n_ceo_fax_no) ? $braData->n_ceo_fax_no : $braData->ceo_fax_no;
            $appData->ceo_email = !empty($braData->n_ceo_email) ? $braData->n_ceo_email : $braData->ceo_email;
            $appData->ceo_father_name = !empty($braData->n_ceo_father_name) ? $braData->n_ceo_father_name : $braData->ceo_father_name;
            $appData->ceo_mother_name = !empty($braData->n_ceo_mother_name) ? $braData->n_ceo_mother_name : $braData->ceo_mother_name;
            $appData->ceo_nid = !empty($braData->n_ceo_nid) ? $braData->n_ceo_nid : $braData->ceo_nid;
            $appData->ceo_passport_no = !empty($braData->n_ceo_passport_no) ? $braData->n_ceo_passport_no : $braData->ceo_passport_no;
            $appData->ceo_city = !empty($braData->n_ceo_city) ? $braData->n_ceo_city : $braData->ceo_city;
            $appData->ceo_state = !empty($braData->n_ceo_state) ? $braData->n_ceo_state : $braData->ceo_state;
            $appData->ceo_gender = !empty($braData->n_ceo_gender) ? $braData->n_ceo_gender : $braData->ceo_gender;

            // Office Address
            $appData->office_division_id = !empty($braData->n_office_division_id) ? $braData->n_office_division_id : $braData->office_division_id;
            $appData->office_district_id = !empty($braData->n_office_district_id) ? $braData->n_office_district_id : $braData->office_district_id;
            $appData->office_thana_id = !empty($braData->n_office_thana_id) ? $braData->n_office_thana_id : $braData->office_thana_id;
            $appData->office_post_office = !empty($braData->n_office_post_office) ? $braData->n_office_post_office : $braData->office_post_office;
            $appData->office_post_code = !empty($braData->n_office_post_code) ? $braData->n_office_post_code : $braData->office_post_code;
            $appData->office_address = !empty($braData->n_office_address) ? $braData->n_office_address : $braData->office_address;
            $appData->office_telephone_no = !empty($braData->n_office_telephone_no) ? $braData->n_office_telephone_no : $braData->office_telephone_no;
            $appData->office_mobile_no = !empty($braData->n_office_mobile_no) ? $braData->n_office_mobile_no : $braData->office_mobile_no;
            $appData->office_fax_no = !empty($braData->n_office_fax_no) ? $braData->n_office_fax_no : $braData->office_fax_no;
            $appData->office_email = !empty($braData->n_office_email) ? $braData->n_office_email : $braData->office_email;

            // Factory Address
            $appData->factory_district_id = !empty($braData->n_factory_district_id) ? $braData->n_factory_district_id : $braData->factory_district_id;
            $appData->factory_thana_id = !empty($braData->n_factory_thana_id) ? $braData->n_factory_thana_id : $braData->factory_thana_id;
            $appData->factory_post_office = !empty($braData->n_factory_post_office) ? $braData->n_factory_post_office : $braData->factory_post_office;
            $appData->factory_post_code = !empty($braData->n_factory_post_code) ? $braData->n_factory_post_code : $braData->factory_post_code;
            $appData->factory_address = !empty($braData->n_factory_address) ? $braData->n_factory_address : $braData->factory_address;
            $appData->factory_telephone_no = !empty($braData->n_factory_telephone_no) ? $braData->n_factory_telephone_no : $braData->factory_telephone_no;
            $appData->factory_mobile_no = !empty($braData->n_factory_mobile_no) ? $braData->n_factory_mobile_no : $braData->factory_mobile_no;
            $appData->factory_fax_no = !empty($braData->n_factory_fax_no) ? $braData->n_factory_fax_no : $braData->factory_fax_no;

            // Project status
            $appData->project_status_id = !empty($braData->n_project_status_id) ? $braData->n_project_status_id : $braData->project_status_id;

            // Date of commercial operation
            $appData->commercial_operation_date = !empty($braData->n_commercial_operation_date) ? $braData->n_commercial_operation_date : $braData->commercial_operation_date;

            // Sales
            $appData->local_sales = !empty($braData->n_local_sales) ? $braData->n_local_sales : $braData->local_sales;
            $appData->foreign_sales = !empty($braData->n_foreign_sales) ? $braData->n_foreign_sales : $braData->foreign_sales;
            $appData->total_sales = !empty($braData->n_total_sales) ? $braData->n_total_sales : $braData->total_sales;

            // Manpower of the organization
            $appData->local_male = !empty($braData->n_local_male) ? $braData->n_local_male : $braData->local_male;
            $appData->local_female = !empty($braData->n_local_female) ? $braData->n_local_female : $braData->local_female;
            $appData->local_total = !empty($braData->n_local_total) ? $braData->n_local_total : $braData->local_total;
            $appData->foreign_male = !empty($braData->n_foreign_male) ? $braData->n_foreign_male : $braData->foreign_male;
            $appData->foreign_female = !empty($braData->n_foreign_female) ? $braData->n_foreign_female : $braData->foreign_female;
            $appData->foreign_total = !empty($braData->n_foreign_total) ? $braData->n_foreign_total : $braData->foreign_total;
            $appData->manpower_total = !empty($braData->n_manpower_total) ? $braData->n_manpower_total : $braData->manpower_total;
            $appData->manpower_local_ratio = !empty($braData->n_manpower_local_ratio) ? $braData->n_manpower_local_ratio : $braData->manpower_local_ratio;
            $appData->manpower_foreign_ratio = !empty($braData->n_manpower_foreign_ratio) ? $braData->n_manpower_foreign_ratio : $braData->manpower_foreign_ratio;
            $appData->manpower_foreign_ratio = !empty($braData->n_manpower_foreign_ratio) ? $braData->n_manpower_foreign_ratio : $braData->manpower_foreign_ratio;

            if (!empty($braData->n_local_land_ivst) || !empty($braData->n_local_building_ivst) || !empty($braData->n_local_machinery_ivst) || !empty($braData->n_local_others_ivst) ||
                !empty($braData->n_local_others_ivst) || !empty($braData->n_local_wc_ivst) || !empty($braData->n_total_fixed_ivst_million) || !empty($braData->n_total_fixed_ivst) ||
                !empty($braData->n_usd_exchange_rate) || !empty($braData->n_total_fee) || !empty($braData->n_finance_src_loc_equity_1) || !empty($braData->n_finance_src_foreign_equity_1) ||
                !empty($braData->n_finance_src_loc_total_equity_1) || !empty($braData->n_finance_src_loc_loan_1) || !empty($braData->n_finance_src_foreign_loan_1) ||
                !empty($braData->n_finance_src_total_loan) || !empty($braData->n_finance_src_loc_total_financing_m) || !empty($braData->n_finance_src_loc_total_financing_1)) {

                //Investment
                $appData->local_land_ivst = empty($braData->n_local_land_ivst) ? null : $braData->n_local_land_ivst;
                $appData->local_land_ivst_ccy = empty($braData->n_local_land_ivst_ccy) ? null : $braData->n_local_land_ivst_ccy;
                $appData->local_building_ivst = empty($braData->n_local_building_ivst) ? null : $braData->n_local_building_ivst;
                $appData->local_building_ivst_ccy = empty($braData->n_local_building_ivst_ccy) ? null : $braData->n_local_building_ivst_ccy;
                $appData->local_machinery_ivst = empty($braData->n_local_machinery_ivst) ? null : $braData->n_local_machinery_ivst;
                $appData->local_machinery_ivst_ccy = empty($braData->n_local_machinery_ivst_ccy) ? null : $braData->n_local_machinery_ivst_ccy;
                $appData->local_others_ivst = empty($braData->n_local_others_ivst) ? null : $braData->n_local_others_ivst;
                $appData->local_others_ivst_ccy = empty($braData->n_local_others_ivst_ccy) ? null : $braData->n_local_others_ivst_ccy;
                $appData->local_wc_ivst = empty($braData->n_local_wc_ivst) ? null : $braData->n_local_wc_ivst;
                $appData->local_wc_ivst_ccy = empty($braData->n_local_wc_ivst_ccy) ? null : $braData->n_local_wc_ivst_ccy;
                $appData->total_fixed_ivst_million = empty($braData->n_total_fixed_ivst_million) ? null : $braData->n_total_fixed_ivst_million;
                $appData->total_fixed_ivst = empty($braData->n_total_fixed_ivst) ? null : $braData->n_total_fixed_ivst;
                $appData->usd_exchange_rate = empty($braData->n_usd_exchange_rate) ? null : $braData->n_usd_exchange_rate;
                $appData->total_fee = empty($braData->n_total_fee) ? null : $braData->n_total_fee;

                //Source of finance
                $appData->finance_src_loc_equity_1 = empty($braData->n_finance_src_loc_equity_1) ? 0 : $braData->n_finance_src_loc_equity_1;
                $appData->finance_src_foreign_equity_1 = empty($braData->n_finance_src_foreign_equity_1) ? 0 : $braData->n_finance_src_foreign_equity_1;
                $appData->finance_src_loc_total_equity_1 = empty($braData->n_finance_src_loc_total_equity_1) ? 0 : $braData->n_finance_src_loc_total_equity_1;
                $appData->finance_src_loc_loan_1 = empty($braData->n_finance_src_loc_loan_1) ? 0 : $braData->n_finance_src_loc_loan_1;
                $appData->finance_src_foreign_loan_1 = empty($braData->n_finance_src_foreign_loan_1) ? 0 : $braData->n_finance_src_foreign_loan_1;
                $appData->finance_src_total_loan = empty($braData->n_finance_src_total_loan) ? 0 : $braData->n_finance_src_total_loan;
                $appData->finance_src_loc_total_financing_m = empty($braData->n_finance_src_loc_total_financing_m) ? 0 : $braData->n_finance_src_loc_total_financing_m;
                $appData->finance_src_loc_total_financing_1 = empty($braData->n_finance_src_loc_total_financing_1) ? 0 : $braData->n_finance_src_loc_total_financing_1;
            }else {
                //Investment
                $appData->local_land_ivst = empty($braData->local_land_ivst) ? null : $braData->local_land_ivst;
                $appData->local_land_ivst_ccy = empty($braData->local_land_ivst_ccy) ? null : $braData->local_land_ivst_ccy;
                $appData->local_building_ivst = empty($braData->local_building_ivst) ? null : $braData->local_building_ivst;
                $appData->local_building_ivst_ccy = empty($braData->local_building_ivst_ccy) ? null : $braData->local_building_ivst_ccy;
                $appData->local_machinery_ivst = empty($braData->local_machinery_ivst) ? null : $braData->local_machinery_ivst;
                $appData->local_machinery_ivst_ccy = empty($braData->local_machinery_ivst_ccy) ? null : $braData->local_machinery_ivst_ccy;
                $appData->local_others_ivst = empty($braData->local_others_ivst) ? null : $braData->local_others_ivst;
                $appData->local_others_ivst_ccy = empty($braData->local_others_ivst_ccy) ? null : $braData->local_others_ivst_ccy;
                $appData->local_wc_ivst = empty($braData->local_wc_ivst) ? null : $braData->local_wc_ivst;
                $appData->local_wc_ivst_ccy = empty($braData->local_wc_ivst_ccy) ? null : $braData->local_wc_ivst_ccy;
                $appData->total_fixed_ivst_million = empty($braData->total_fixed_ivst_million) ? null : $braData->total_fixed_ivst_million;
                $appData->total_fixed_ivst = empty($braData->total_fixed_ivst) ? null : $braData->total_fixed_ivst;
                $appData->usd_exchange_rate = empty($braData->usd_exchange_rate) ? null : $braData->usd_exchange_rate;
                $appData->total_fee = empty($braData->total_fee) ? null : $braData->total_fee;

                //Source of finance
                $appData->finance_src_loc_equity_1 = empty($braData->finance_src_loc_equity_1) ? 0 : $braData->finance_src_loc_equity_1;
                $appData->finance_src_foreign_equity_1 = empty($braData->finance_src_foreign_equity_1) ? 0 : $braData->finance_src_foreign_equity_1;
                $appData->finance_src_loc_total_equity_1 = empty($braData->finance_src_loc_total_equity_1) ? 0 : $braData->finance_src_loc_total_equity_1;
                $appData->finance_src_loc_loan_1 = empty($braData->finance_src_loc_loan_1) ? 0 : $braData->finance_src_loc_loan_1;
                $appData->finance_src_foreign_loan_1 = empty($braData->finance_src_foreign_loan_1) ? 0 : $braData->finance_src_foreign_loan_1;
                $appData->finance_src_total_loan = empty($braData->finance_src_total_loan) ? 0 : $braData->finance_src_total_loan;
                $appData->finance_src_loc_total_financing_m = empty($braData->finance_src_loc_total_financing_m) ? 0 : $braData->finance_src_loc_total_financing_m;
                $appData->finance_src_loc_total_financing_1 = empty($braData->finance_src_loc_total_financing_1) ? 0 : $braData->finance_src_loc_total_financing_1;
            }

            // Public utility service
            if (!empty($braData->n_public_land) || !empty($braData->n_public_electricity) || !empty($braData->n_public_gas) || !empty($braData->n_public_telephone) ||
                !empty($braData->n_public_road) || !empty($braData->n_public_water) || !empty($braData->n_public_drainage) || !empty($braData->n_public_others)) {

                $appData->public_land = empty($braData->n_public_land) ? 0 : $braData->n_public_land;
                $appData->public_electricity = empty($braData->n_public_electricity) ? 0 : $braData->n_public_electricity;
                $appData->public_gas = empty($braData->n_public_gas) ? 0 : $braData->n_public_gas;
                $appData->public_telephone = empty($braData->n_public_telephone) ? 0 : $braData->n_public_telephone;
                $appData->public_road = empty($braData->n_public_road) ? 0 : $braData->n_public_road;
                $appData->public_water = empty($braData->n_public_water) ? 0 : $braData->n_public_water;
                $appData->public_drainage = empty($braData->n_public_drainage) ? 0 : $braData->n_public_drainage;
                $appData->public_others = empty($braData->n_public_others) ? 0 : $braData->n_public_others;
            } else {
                $appData->public_land = empty($braData->public_land) ? 0 : $braData->public_land;
                $appData->public_electricity = empty($braData->public_electricity) ? 0 : $braData->public_electricity;
                $appData->public_gas = empty($braData->public_gas) ? 0 : $braData->public_gas;
                $appData->public_telephone = empty($braData->public_telephone) ? 0 : $braData->public_telephone;
                $appData->public_road = empty($braData->public_road) ? 0 : $braData->public_road;
                $appData->public_water = empty($braData->public_water) ? 0 : $braData->public_water;
                $appData->public_drainage = empty($braData->public_drainage) ? 0 : $braData->public_drainage;
                $appData->public_others = empty($braData->public_others) ? 0 : $braData->public_others;
            }

            //Trade licence details
            $appData->trade_licence_num = !empty($braData->n_trade_licence_num) ? $braData->n_trade_licence_num : $braData->trade_licence_num;
            $appData->trade_licence_issuing_authority = !empty($braData->n_trade_licence_issuing_authority) ? $braData->n_trade_licence_issuing_authority : $braData->trade_licence_issuing_authority;

            //Tin
            $appData->tin_number = !empty($braData->n_tin_number) ? $braData->n_tin_number : $braData->tin_number;

            //Description of machinery and equipment
            $appData->machinery_local_qty = !empty($braData->n_machinery_local_qty) ? $braData->n_machinery_local_qty : $braData->machinery_local_qty;
            $appData->machinery_local_price_bdt = !empty($braData->n_machinery_local_price_bdt) ? $braData->n_machinery_local_price_bdt : $braData->machinery_local_price_bdt;
            $appData->imported_qty = !empty($braData->n_imported_qty) ? $braData->n_imported_qty : $braData->imported_qty;
            $appData->imported_qty_price_bdt = !empty($braData->n_imported_qty_price_bdt) ? $braData->n_imported_qty_price_bdt : $braData->imported_qty_price_bdt;
            $appData->total_machinery_price = !empty($braData->n_total_machinery_price) ? $braData->n_total_machinery_price : $braData->total_machinery_price;
            $appData->total_machinery_qty = !empty($braData->n_total_machinery_qty) ? $braData->n_total_machinery_qty : $braData->total_machinery_qty;


            //Description of raw &amp; packing materials
            $appData->local_description = !empty($braData->n_local_description) ? $braData->n_local_description : $braData->local_description;
            $appData->imported_description = !empty($braData->n_imported_description) ? $braData->n_imported_description : $braData->imported_description;


            // Information of (Chairman/ Managing Director/ Or Equivalent)
            $appData->g_full_name = !empty($braData->n_g_full_name) ? $braData->n_g_full_name : $braData->g_full_name;
            $appData->g_designation = !empty($braData->n_g_designation) ? $braData->n_g_designation : $braData->g_designation;
            $appData->g_signature = !empty($braData->n_g_signature) ? $braData->n_g_signature : $braData->g_signature;

            // Why do you want to BIDA Registration Amendment?
            $appData->major_remarks = $braData->major_remarks;

            //Authorized Person Information
            $appData->accept_terms = $braData->accept_terms;

            $appData->sf_payment_id = $braData->sf_payment_id;
            $appData->gf_payment_id = $braData->gf_payment_id;
            $appData->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            //dd($e->getFile(), $e->getMessage(), $e->getLine());
            return false;
        }
    }

    public static function BRMachineryDataStore($ref_id)
    {
        try {

            $listOfMachineryImported = ListOfMachineryImported::where('app_id', $ref_id)
                ->where('process_type_id', 102)
                ->get();

            if (count($listOfMachineryImported) > 0) {
                $machineryData = [];

                foreach ($listOfMachineryImported as $machineryImported) {
                    $machineryData[] = [
                        
                        'name' => $machineryImported->l_machinery_imported_name,
                        'quantity' => $machineryImported->l_machinery_imported_qty,
                        'total_imported' => 0,
                        'unit_price' => $machineryImported->l_machinery_imported_unit_price,
                        'total_value' => $machineryImported->l_machinery_imported_total_value,
                        'amendment_type' => 'no change',
                        'br_process_type_id' => 102,
                        'br_app_id' => $ref_id,
                        'br_mechinery_table_id' => $machineryImported->id,
                        'status' => 1,
                        'is_archive' => 0,
                        'is_deleted' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'created_by' => Auth::user()->id,	
                        'updated_by' => Auth::user()->id,	
                    ];
                }

                // Insert machinery data in bulk
                MasterMachineryImported::insert($machineryData);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('BRCommonPoolManager', ['ref_id' => $ref_id, 'error' => $e->getFile(), ' ' . $e->getLine(), ' ' . $e->getMessage()]);

            return false;
        }
    }

    public static function BRAMachineryDataStore($ref_id, $process_type_id)
    {
        try {
            $listOfMachineryImportedBRA = DB::table('list_of_machinery_imported_amendment')
                ->selectRaw('l_machinery_imported_name as machinery_imported_name')
                ->selectRaw('COALESCE(NULLIF(n_l_machinery_imported_name, ""), l_machinery_imported_name) as l_machinery_imported_name')
                ->selectRaw('COALESCE(NULLIF(n_l_machinery_imported_qty, ""), l_machinery_imported_qty) as l_machinery_imported_qty')
                ->selectRaw('COALESCE(NULLIF(n_l_machinery_imported_unit_price, ""), l_machinery_imported_unit_price) as l_machinery_imported_unit_price')
                ->selectRaw('COALESCE(NULLIF(n_l_machinery_imported_total_value, ""), l_machinery_imported_total_value) as l_machinery_imported_total_value')
                ->selectRaw('amendment_type')
                ->selectRaw('status')
                ->selectRaw('id')
                ->selectRaw('ref_master_id')
                ->where(['app_id' => $ref_id, 'process_type_id' => 12])
                ->get();
            DB::beginTransaction();

            if (count($listOfMachineryImportedBRA) > 0) {
                foreach ($listOfMachineryImportedBRA as $machineryImportedBRA) {
                    // check master data
                    if ($machineryImportedBRA->amendment_type == 'add') {
                        $machineryImportedMaster = new MasterMachineryImported();
                    }else {
                        $machineryImportedMaster = MasterMachineryImported::firstOrNew(["id"=>$machineryImportedBRA->ref_master_id]);
                    }
                    // check amendment type
                    if ($machineryImportedBRA->amendment_type == 'edit') {
                        $machineryImportedMaster->name = $machineryImportedBRA->l_machinery_imported_name;
                        $machineryImportedMaster->quantity = $machineryImportedBRA->l_machinery_imported_qty;
                    }
                    if (in_array($machineryImportedBRA->amendment_type, ['delete', 'remove'])) {
                        $machineryImportedMaster->is_deleted = 1;
                    }
                    // store data
                    $machineryImportedMaster->bra_process_type_id = 12;
                    $machineryImportedMaster->bra_app_id = $ref_id;
                    $machineryImportedMaster->bra_mechinery_table_id = $machineryImportedBRA->id;
                    $machineryImportedMaster->name = $machineryImportedBRA->l_machinery_imported_name;
                    $machineryImportedMaster->quantity = $machineryImportedBRA->l_machinery_imported_qty;
                    $machineryImportedMaster->unit_price = $machineryImportedBRA->l_machinery_imported_unit_price;
                    $machineryImportedMaster->total_value = $machineryImportedBRA->l_machinery_imported_total_value;
                    $machineryImportedMaster->amendment_type = $machineryImportedBRA->amendment_type;
                    $machineryImportedMaster->status =$machineryImportedBRA->status;
                    $machineryImportedMaster->save();
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage().$e->getLine().$e->getFile());
            DB::rollback();
            return false;
        }
    }
    public static function listOfMachineryImported($process_type_id, $ref_id)
    {
        try {
            $ref_app_id_column = $process_type_id == 102 ? 'br_app_id' : 'bra_app_id';
            $process_type_id_column = $process_type_id == 102 ? 'br_process_type_id' : 'bra_process_type_id';
            // check MasterMachineryImported data count > 0
            $checkMasterMachineryImported = MasterMachineryImported::where("$ref_app_id_column", $ref_id)
                ->where("$process_type_id_column", $process_type_id)
                ->where('status', 1)
                ->whereNotIn('amendment_type', ['delete', 'remove'])
                ->count();
            if ($checkMasterMachineryImported > 0) {
                $listOfMachineryImported = MasterMachineryImported::where("$ref_app_id_column", $ref_id)
                    ->where("$process_type_id_column", $process_type_id)
                    ->where('status', 1)
                    ->whereNotIn('amendment_type', ['delete', 'remove'])
                    ->get([
                        'id as ref_master_id',
                        'name as l_machinery_imported_name',
                        'quantity as l_machinery_imported_qty',
                        'unit_price as l_machinery_imported_unit_price',
                        'total_value as l_machinery_imported_total_value',
                    ]);
            }else{
                if ($process_type_id == 102) {
                    $listOfMachineryImported = ListOfMachineryImported::where('app_id', $ref_id)
                        ->where('process_type_id', $process_type_id)
                        ->get([
                            'l_machinery_imported_name',
                            'l_machinery_imported_qty',
                            'l_machinery_imported_unit_price',
                            'l_machinery_imported_total_value',
                        ]);
                }else{
                    $listOfMachineryImported = DB::table('list_of_machinery_imported_amendment')
                        ->select(DB::raw('
                                IFNULL(NULLIF(n_l_machinery_imported_name, \'\'), l_machinery_imported_name) as l_machinery_imported_name,
                                IFNULL(NULLIF(n_l_machinery_imported_qty, \'\'), l_machinery_imported_qty) as l_machinery_imported_qty,
                                IFNULL(NULLIF(n_l_machinery_imported_unit_price, \'\'), l_machinery_imported_unit_price) as l_machinery_imported_unit_price,
                                IFNULL(NULLIF(n_l_machinery_imported_total_value, \'\'), l_machinery_imported_total_value) as l_machinery_imported_total_value
                            '))
                            ->where(['app_id' => $ref_id, 'process_type_id' => $process_type_id, 'status' => 1])
                            ->whereNotIn('amendment_type', ['delete', 'remove'])
                            ->get();
                }
            }

            return $listOfMachineryImported;
        } catch (\Exception $e) {
            Log::error($e->getMessage().$e->getLine().$e->getFile());
            return false;
        }
    }
    
}