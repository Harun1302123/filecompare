<?php 

namespace App\Modules\BidaRegistrationAmendment\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class BidaRegistrationAmendment extends Model {
    protected $table = 'bra_apps';
    protected $fillable = [
        'certificate_link',
        'company_id',
        'is_approval_online',
        'ref_app_tracking_no',
        'manually_approved_br_no',
        'sf_payment_id',
        'gf_payment_id',
        'company_name',
        'company_name_bn',
        'project_name',
        'organization_status_id',
        'country_of_origin_id',
        'section_id',
        'division_id',
        'group_id',
        'class_id',
        'class_code',
        'sub_class_id',
        'other_sub_class_code',
        'other_sub_class_name',
        'ceo_country_id',
        'ceo_dob',
        'ceo_passport_no',
        'ceo_nid',
        'ceo_designation',
        'ceo_full_name',
        'ceo_city',
        'ceo_district_id',
        'ceo_state',
        'ceo_thana_id',
        'ceo_post_code',
        'ceo_address',
        'ceo_telephone_no',
        'ceo_mobile_no',
        'ceo_email',
        'ceo_fax_no',
        'ceo_father_name',
        'ceo_mother_name',
        'ceo_spouse_name',
        'ceo_gender',

        'office_division_id',
        'office_district_id',
        'office_thana_id',
        'office_post_office',
        'office_post_code',
        'office_address',
        'office_telephone_no',
        'office_mobile_no',
        'office_fax_no',
        'office_email',

        'factory_district_id',
        'factory_thana_id',
        'factory_post_office',
        'factory_post_code',
        'factory_address',
        'factory_telephone_no',
        'factory_mobile_no',
        'factory_fax_no',

        'project_status_id',
        'commercial_operation_date',

        'local_sales',
        'foreign_sales',
        'total_sales',
        'local_male',
        'local_female',
        'local_total',
        'foreign_male',
        'foreign_female',
        'foreign_total',
        'manpower_total',
        'manpower_local_ratio',
        'manpower_foreign_ratio',

        'local_land_ivst',
        'local_land_ivst_ccy',
        'local_building_ivst',
        'local_building_ivst_ccy',
        'local_machinery_ivst',
        'local_machinery_ivst_ccy',
        'local_others_ivst',
        'local_others_ivst_ccy',
        'local_wc_ivst',
        'local_wc_ivst_ccy',
        'total_fixed_ivst_million',
        'total_fixed_ivst',
        'usd_exchange_rate',
        'total_fee',

        'finance_src_loc_equity_1',
        'finance_src_foreign_equity_1',
        'finance_src_loc_total_equity_1',
        'finance_src_loc_loan_1',
        'finance_src_foreign_loan_1',
        'finance_src_total_loan',
        'finance_src_loc_total_financing_m',
        'finance_src_loc_total_financing_1',

        'g_full_name',
        'g_designation',
        'g_signature',

        'n_company_name',
        'n_company_name_bn',
        'n_project_name',
        'n_organization_status_id',
        'n_country_of_origin_id',
        'n_section_id',
        'n_division_id',
        'n_group_id',
        'n_class_id',
        'n_class_code',
        'n_sub_class_id',
        'n_other_sub_class_code',
        'n_other_sub_class_name',
        'n_ceo_country_id',
        'n_ceo_dob',
        'n_ceo_passport_no',
        'n_ceo_nid',
        'n_ceo_designation',
        'n_ceo_full_name',
        'n_ceo_city',
        'n_ceo_district_id',
        'n_ceo_state',
        'n_ceo_thana_id',
        'n_ceo_post_code',
        'n_ceo_address',
        'n_ceo_telephone_no',
        'n_ceo_mobile_no',
        'n_ceo_email',
        'n_ceo_fax_no',
        'n_ceo_father_name',
        'n_ceo_mother_name',
        'n_ceo_spouse_name',
        'n_ceo_gender',

        'n_office_division_id',
        'n_office_district_id',
        'n_office_thana_id',
        'n_office_post_office',
        'n_office_post_code',
        'n_office_address',
        'n_office_telephone_no',
        'n_office_mobile_no',
        'n_office_fax_no',
        'n_office_email',

        'n_factory_district_id',
        'n_factory_thana_id',
        'n_factory_post_office',
        'n_factory_post_code',
        'n_factory_address',
        'n_factory_telephone_no',
        'n_factory_mobile_no',
        'n_factory_fax_no',

        'n_project_status_id',
        'n_commercial_operation_date',

        'n_local_sales',
        'n_foreign_sales',
        'n_total_sales',
        'n_local_male',
        'n_local_female',
        'n_local_total',
        'n_foreign_male',
        'n_foreign_female',
        'n_foreign_total',
        'n_manpower_total',
        'n_manpower_local_ratio',
        'n_manpower_foreign_ratio',

        'n_local_land_ivst',
        'n_local_land_ivst_ccy',
        'n_local_building_ivst',
        'n_local_building_ivst_ccy',
        'n_local_machinery_ivst',
        'n_local_machinery_ivst_ccy',
        'n_local_others_ivst',
        'n_local_others_ivst_ccy',
        'n_local_wc_ivst',
        'n_local_wc_ivst_ccy',
        'n_total_fixed_ivst_million',
        'n_total_fixed_ivst',
        'n_usd_exchange_rate',
        'n_total_fee',

        'n_finance_src_loc_equity_1',
        'n_finance_src_foreign_equity_1',
        'n_finance_src_loc_total_equity_1',
        'n_finance_src_loc_loan_1',
        'n_finance_src_foreign_loan_1',
        'n_finance_src_total_loan',
        'n_finance_src_loc_total_financing_m',
        'n_finance_src_loc_total_financing_1',

        'n_g_full_name',
        'n_g_designation',
        'n_g_signature',

        'approved_date',
        'payment_date',
        'list_of_dir_machinery_doc',
        'accept_terms',
        'is_approved',
        'is_archive',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'reg_no'
    ];

    public static function boot() {
        parent::boot();
        static::creating(function($post) {
            $post->created_by = CommonFunction::getUserId();
            $post->updated_by = CommonFunction::getUserId();
        });

        static::updating(function($post) {
            $post->updated_by = CommonFunction::getUserId();
        });
    }

}
