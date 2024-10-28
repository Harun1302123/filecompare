<?php

namespace App\Modules\ImportPermission\Services;

use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\SonaliPayment\Models\PaymentConfiguration;
use App\Modules\BasicInformation\Models\EA_OrganizationType;
use App\Modules\Apps\Models\AppDocuments;
use App\Modules\BasicInformation\Models\EA_OrganizationStatus;
use App\Modules\Users\Models\Countries;
use App\Modules\BasicInformation\Models\EA_OwnershipStatus;
use App\Modules\Settings\Models\Currencies;
use App\Modules\Users\Models\AreaInfo;
use App\Modules\ImportPermission\Models\IrcProjectStatus;
use App\Modules\ImportPermission\Models\ProductUnit;
use App\Modules\BidaRegistration\Models\LaAnnualProductionCapacity;
use App\Modules\BidaRegistration\Models\SourceOfFinance;
use App\Modules\ImportPermission\Models\AnnualProductionCapacity;
use App\Modules\ImportPermission\Models\IrcSourceOfFinance;
use App\Modules\Settings\Models\Attachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ImportPermissionService
{
    protected $process_type_id = 21;
    protected $aclName = 'ImportPermission';
    
    public function validateRequestAccess($request, $mode, $ajaxErrNo, $aclErrNo)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. ' . [$ajaxErrNo];
        }

        if (!ACL::getAccsessRight($this->aclName, $mode)) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>You have no access right! Contact with system admin for more information. [$aclErrNo]</h4>"
            ]);
        }
        
        return true;
    }

    public function checkBasicInfoAndDepartment($working_company_id, $bi_err, $dep_err)
    {
        if (CommonFunction::checkEligibilityAndBiApps($working_company_id) != 1) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<div class='btn-center'><h4 class='custom-err-msg'>Sorry! You have no approved Basic Information application for BIDA services. [$bi_err]</h4> <br/> <a href='/dashboard' class='btn btn-primary btn-sm'>Apply for Basic Information</a></div>"
            ]);
        }

        // Check whether the applicant company's department will get this service
        $department_id = CommonFunction::getDeptIdByCompanyId($working_company_id);
        if (in_array($department_id, [1, 4])) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>Sorry! The department is not allowed to apply to this application. [$dep_err]</h4>"
            ]);
        }

        return null;
    }

    public function getPaymentInfo($payment_category_id) 
    {
        return PaymentConfiguration::leftJoin('sp_payment_category', 'sp_payment_category.id', '=',
            'sp_payment_configuration.payment_category_id')
            ->where([
                'sp_payment_configuration.process_type_id' => $this->process_type_id,
                'sp_payment_configuration.payment_category_id' => $payment_category_id,
                'sp_payment_configuration.status' => 1,
                'sp_payment_configuration.is_archive' => 0
            ])->first(['sp_payment_configuration.*', 'sp_payment_category.name']);
    
    }

    public function unfixedAmountsForPayment($payment_config, $relevant_info_array = [])
    {
        /**
         * DB Table Name: sp_payment_category
         * Payment Categories:
         * 1 = Service Fee Payment
         * 2 = Government Fee Payment
         * 3 = Government & Service Fee Payment
         * 4 = Manual Service Fee Payment
         * 5 = Manual Government Fee Payment
         * 6 = Manual Government & Service Fee Payment
         */

        $unfixed_amount_array = [
            1 => 0, // Vendor-Service-Fee
            2 => 0, // Govt-Service-Fee
            3 => 0, // Govt. Application Fee
            4 => 0, // Vendor-Vat-Fee
            5 => 0, // Govt-Vat-Fee
            6 => 0, // Govt-Vendor-Vat-Fee
        ];

        if ($payment_config->payment_category_id === 1) {
            // For service fee payment there have no unfixed distribution.

        } elseif ($payment_config->payment_category_id === 2) {
            // Govt-Vendor-Vat-Fee

        } elseif ($payment_config->payment_category_id === 3) {

        }

        $unfixed_amount_total = 0;
        $vat_on_pay_amount_total = 0;
        foreach ($unfixed_amount_array as $key => $amount) {
            // 4 = Vendor-Vat-Fee, 5 = Govt-Vat-Fee, 6 = Govt-Vendor-Vat-Fee
            if (in_array($key, [4, 5, 6])) {
                $vat_on_pay_amount_total += $amount;
            } else {
                $unfixed_amount_total += $amount;
            }
        }

        return [
            'amounts' => $unfixed_amount_array,
            'total_unfixed_amount' => $unfixed_amount_total,
            'total_vat_on_pay_amount' => $vat_on_pay_amount_total,
        ];
    }

    public function getCompanyInfoData()
    {
        $companyIds = CommonFunction::getUserCompanyWithZero();
        return $data['getCompanyData'] =  ProcessList::leftjoin('ea_apps', 'ea_apps.id', '=', 'process_list.ref_id')
                ->where('process_list.process_type_id', 100)
                ->where('process_list.status_id', 25)
                ->whereIn('process_list.company_id', $companyIds)
                ->first(['ea_apps.*']);
    }

    public function getData($dataType) 
    {
        switch ($dataType) {
            case 'eaOrganizationType':
                $data = ['' => 'Select one'] + EA_OrganizationType::where('is_archive', 0)->where('status', 1)->whereIn('type', [1, 3])->orderBy('name')->lists('name', 'id')->all();
                break;
            case 'countries':
                $data = ['' => 'Select one'] + Countries::where('country_status', 'Yes')->orderBy('nicename', 'asc')->lists('nicename', 'id')->all();
                break;
            case 'countriesWithoutBD':
                $data = ['' => 'Select One'] + Countries::where('country_status', 'Yes')->where('id', '!=', '18')->orderBy('nicename', 'asc')->lists('nicename', 'id')->all();
                break;
            case 'eaOrganizationStatus':
                $data = ['' => 'Select One'] + EA_OrganizationStatus::where('is_archive', 0)->where('status', 1)->orderBy('name')->lists('name', 'id')->all();
                break;
            case 'eaOwnershipStatus':
                $data = ['' => 'Select one'] + EA_OwnershipStatus::where('is_archive', 0)->where('status', 1)->orderBy('name')->lists('name', 'id')->all();
                break;
            case 'currencies':
                $data = ['' => 'Select'] + Currencies::orderBy('code')->where('is_archive', 0)->where('is_active', 1)->lists('code', 'id')->all();
                break;
            case 'currencyBDT':
                $data = ['' => 'Select one'] + Currencies::orderBy('code')->whereIn('code', ['BDT'])->where('is_archive', 0)->where('is_active', 1)->lists('code', 'id')->all();
                break;
            case 'divisions':
                $data = ['' => 'Select One'] + AreaInfo::where('area_type', 1)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
                break;
            case 'districts':
                $data = ['' => 'Select One'] + AreaInfo::where('area_type', 2)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
                break;
            case 'thana':
                $data = AreaInfo::orderby('area_nm')->where('area_type', 3)->lists('area_nm', 'area_id');
                break;
            case 'projectStatusList':
                $data = IrcProjectStatus::where('is_archive', 0)->where('status', 1)->lists('name', 'id');
                break;
            case 'nationality':
                $data = ['' => 'Select one'] + Countries::where('country_status', 'Yes')->where('nationality', '!=', '')->orderby('nationality', 'asc')->lists('nationality', 'id')->all();
                break;
            case 'usdValue':
                $data = Currencies::where('code', 'USD')->first();
                break;
            case 'totalFee':
                $data = DB::table('pay_order_amount_setup')->where('process_type_id', 102)->get([ 'min_amount_bdt', 'max_amount_bdt', 'p_o_amount_bdt' ]);
                break;
            case 'productUnit':
                $data = ['' => 'Select one'] + ProductUnit::where('status', 1)->where('is_archive', 0)->orderBy('name')->lists('name', 'id')->all();
                break;

            default:
                $data = '';
        }
            return $data;
    }

    public function cleanLoadData()
    {
        Session::forget('brAnnualProductionCapacity');
        Session::forget('brSourceOfFinance');
        Session::forget('brListOfMachineryImported');
        Session::forget('ref_app_approve_date');
        Session::forget('brInfo');
        Session::forget('listOfMachineryImportedMaster');
        Session::forget('brInfo.approval_center_id');
        Session::forget('brInfo.des_office_name');
        Session::forget('brInfo.des_office_address');

        Session::flash('success', 'Successfully cleaned data.');
    }

    public function getAttachment($attachment_key)
    {
        return Attachment::leftJoin('attachment_type', 'attachment_type.id', '=', 'attachment_list.attachment_type_id')
            ->where('attachment_type.key', $attachment_key)
            ->where('attachment_list.status', 1)
            ->where('attachment_list.is_archive', 0)
            ->orderBy('attachment_list.order')
            ->get(['attachment_list.*']);
    }

    public function getApplicationValidationRules($request, $doc_row) 
    {
        $rules = [];
        $messages = [];

        if ($request->get('actionBtn') != 'draft') {
            $rules['total_fixed_ivst'] = 'same:finance_src_loc_total_financing_1';
            $rules['company_name'] = 'required';
            $rules['ownership_status_id'] = 'required';
            $rules['organization_status_id'] = 'required';
            $rules['ceo_full_name'] = 'required';
            $rules['ceo_mobile_no'] = 'required';
            $rules['ceo_email'] = 'required';
            $rules['ceo_gender'] = 'required';

            $rules['office_division_id'] = 'required';
            $rules['office_district_id'] = 'required';
            $rules['office_thana_id'] = 'required';
            $rules['office_mobile_no'] = 'required';
            $rules['office_email'] = 'required';

            $rules['local_machinery_ivst'] = 'required';
            $rules['g_full_name'] = 'required';
            $rules['g_designation'] = 'required';

            $rules['business_class_code'] = 'required';

            /* Manpower of the organization */
            $rules['local_male'] = 'required';
            $rules['local_female'] = 'required';
            $rules['local_total'] = 'required';
            $rules['foreign_male'] = 'required';
            $rules['foreign_female'] = 'required';
            $rules['foreign_total'] = 'required';
            $rules['manpower_total'] = 'required';
            $rules['manpower_local_ratio'] = 'required';
            $rules['manpower_foreign_ratio'] = 'required';

            if (empty($request->get('investor_signature_base64'))) {
                $rules['investor_signature_hidden'] = 'required';
            } else {
                $rules['investor_signature_base64'] = 'required';
            }

            $rules['trade_licence_num'] = 'required';
            $rules['trade_licence_issuing_authority'] = 'required';

            $rules['tin_number'] = 'required';


            // attachment validation check
            if (count($doc_row) > 0) {
                foreach ($doc_row as $value) {
                    if ($value->doc_priority == 1) {
                        $rules['validate_field_' . $value->id] = 'required';
                        $messages['validate_field_' . $value->id . '.required'] = $value->doc_name . ', this file is required.';
                    }
                }
            }

            $messages['local_machinery_ivst.required'] = 'Machinery & Equipment is required.';
            $messages['g_full_name.required'] = '(Chairman/ Managing Director/ Or Equivalent) name is required.';
            $messages['g_designation.required'] = '(Chairman/ Managing Director/ Or Equivalent) designation is required.';
            $messages['investor_signature_hidden.required'] = '(Chairman/ Managing Director/ Or Equivalent) signature is required.';
            $messages['investor_signature_base64.required'] = '(Chairman/ Managing Director/ Or Equivalent) signature is required.';
            $messages['business_class_code.required'] = 'Code of your business class is required.';
            $messages['total_fixed_ivst.same'] = 'Total Financing and Total Investment (BDT) must be equal.';

            $messages['trade_licence_num.required'] = 'Trade License Number field is required.';
            $messages['trade_licence_issuing_authority.required'] = 'Trade License Issuing Authority field is required.';
            $messages['trade_licence_issue_date.date'] = 'Trade License Issue Date must be date format.';
            $messages['inc_issuing_authority.required'] = 'Incorporation Issuing Authority field is required.';
            $messages['tin_number.required'] = 'TIN Number field is required.';

            $total_equity = 0; //total equity amount
            $total_loan = 0; //total loan amount

            foreach ($request->equity_amount as $value) {
                if (is_numeric($value)) {
                    $total_equity += $value;
                }
                else {
                    $value = (float) $value;
                    $total_equity += $value;
                }
            }
            //checking equity amount
            if (number_format((float)$total_equity, 5, '.', '') != $request->finance_src_loc_total_equity_1) {
                Session::flash('error', "Total equity amount should be equal to Total Equity (Million)");
                return redirect()->back()->withInput();
            }
            foreach ($request->loan_amount as $value) {
                $total_loan += $value;
            }
            //checking loan amount
            if (number_format((float)$total_loan, 5, '.', '') != $request->finance_src_total_loan) {
                Session::flash('error', "Total loan amount should be equal to Total Loan (Million)");
                return redirect()->back()->withInput();
            }
        }
    
        return [
            'rules' => $rules,
            'messages' => $messages
        ];
    }

    /**
     * @param $bra_tracking_no
     * @return bool
     */
    public function BRAChildTableDataLoad($bra_ref_no)
    {
        $getAnnualProductionCapacity = DB::table('annual_production_capacity_amendment')
            ->select(DB::raw('
                            ifnull(n_product_name, product_name) as product_name, 
                            ifnull(n_quantity_unit, quantity_unit) as quantity_unit,
                            ifnull(n_quantity, quantity) as quantity, 
                            ifnull(n_price_usd, price_usd) as price_usd, 
                            ifnull(n_price_taka, price_taka) as price_taka
                        '))
            ->where(['app_id' => $bra_ref_no, 'process_type_id' => 12, 'status' => 1])
            ->whereNotIn('amendment_type', ['delete', 'remove'])
            ->get();

        if (count($getAnnualProductionCapacity) > 0) {
            Session::put('brAnnualProductionCapacity', $getAnnualProductionCapacity);
        }

        $getSourceOfFinance = DB::table('source_of_finance_amendment')
            ->select(DB::raw('
                            ifnull(n_country_id, country_id) as country_id,
                            ifnull(n_equity_amount, equity_amount) as equity_amount,
                            ifnull(n_loan_amount, loan_amount) as loan_amount
                        '))
            ->where(['app_id' => $bra_ref_no, 'process_type_id' => 12])
            ->get();

        if (count($getSourceOfFinance) > 0) {
            Session::put('brSourceOfFinance', $getSourceOfFinance);
        }

        $listOfMachineryImported = DB::table('list_of_machinery_imported_amendment')
            ->select(DB::raw('
                COALESCE(NULLIF(n_l_machinery_imported_name, ""), l_machinery_imported_name) as l_machinery_imported_name,
                COALESCE(NULLIF(n_l_machinery_imported_qty, ""), l_machinery_imported_qty) as l_machinery_imported_qty,
                COALESCE(NULLIF(n_l_machinery_imported_unit_price, ""), l_machinery_imported_unit_price) as l_machinery_imported_unit_price,
                COALESCE(NULLIF(n_l_machinery_imported_total_value, ""), l_machinery_imported_total_value) as l_machinery_imported_total_value, app_id
            '))
            ->where(['app_id' => $bra_ref_no, 'process_type_id' => 12])
            ->whereNotIn('amendment_type', ['delete', 'remove'])
            ->get();

        if (count($listOfMachineryImported)> 0) {
            Session::put('brListOfMachineryImported', $listOfMachineryImported);
        }

        return true;
    }

    /**
     * @param $br_ref_id
     * @return bool
     */
    public function BRChildTableDataLoad($br_ref_id)
    {
        $BRAnnualProductionCapacity = LaAnnualProductionCapacity::where('app_id', $br_ref_id)->get();
        $BRSourceOfFinance = SourceOfFinance::where('app_id', $br_ref_id)->get();
        $listOfMachineryImported = \App\Modules\BidaRegistration\Models\ListOfMachineryImported::where('app_id', $br_ref_id)->where('process_type_id', 102)->get();


        if (count($BRAnnualProductionCapacity) > 0) {
            Session::put('brAnnualProductionCapacity', $BRAnnualProductionCapacity);
        }

        if (count($BRSourceOfFinance) > 0) {
            Session::put('brSourceOfFinance', $BRSourceOfFinance);
        }

        if(count($listOfMachineryImported) > 0){
            Session::put('brListOfMachineryImported', $listOfMachineryImported);
        }

        return true;
    }

    public function getAppEditInfo($applicationId)
    {
        $process_type_id = $this->process_type_id;
        return ProcessList::leftJoin('ip_apps as apps', 'apps.id', '=', 'process_list.ref_id')
            ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
            ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                $join->on('ps.id', '=', 'process_list.status_id');
                $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
            })
            ->leftJoin('irc_project_status', 'irc_project_status.id', '=', 'apps.project_status_id')
            ->leftJoin('sp_payment as sfp', 'sfp.id', '=', 'apps.sf_payment_id')
            ->where('process_list.ref_id', $applicationId)
            ->where('process_list.process_type_id', $process_type_id)
            ->first([
                'process_list.id as process_list_id',
                'process_list.desk_id',
                'process_list.department_id',
                'process_list.process_type_id',
                'process_list.status_id',
                'process_list.locked_by',
                'process_list.locked_at',
                'process_list.ref_id',
                'process_list.tracking_no',
                'process_list.company_id',
                'process_list.process_desc',
                'process_list.submitted_at',
                'process_list.resend_deadline',
                'user_desk.desk_name',
                'ps.status_name',
                'ps.color',
                'irc_project_status.name as project_status_name',
                'apps.*',

                'sfp.contact_name as sfp_contact_name',
                'sfp.contact_email as sfp_contact_email',
                'sfp.contact_no as sfp_contact_phone',
                'sfp.address as sfp_contact_address',
                'sfp.pay_amount as sfp_pay_amount',
                'sfp.vat_on_pay_amount as sfp_vat_on_pay_amount',
                'sfp.total_amount as sfp_total_amount',
                'sfp.payment_status as sfp_payment_status',
                'sfp.pay_mode as pay_mode',
                'sfp.pay_mode_code as pay_mode_code',
            ]);
    }

    public function getAppViewInfo($applicationId)
    {
        $process_type_id = $this->process_type_id;
        return ProcessList::leftJoin('ip_apps as apps', 'apps.id', '=', 'process_list.ref_id')
            ->leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
            ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
            ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                $join->on('ps.id', '=', 'process_list.status_id');
                $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
            })

            // Reference application
            ->leftJoin('process_list as ref_process', 'ref_process.tracking_no', '=', 'apps.ref_app_tracking_no')
            ->leftJoin('process_type as ref_process_type', 'ref_process_type.id', '=', 'ref_process.process_type_id')
            ->leftJoin('irc_project_status', 'irc_project_status.id', '=', 'apps.project_status_id')
            ->leftJoin('sp_payment as sfp', 'sfp.id', '=', 'apps.sf_payment_id')
            ->leftJoin('ea_organization_type', 'ea_organization_type.id', '=', 'apps.organization_type_id')
            ->leftJoin('ea_organization_status', 'ea_organization_status.id', '=', 'apps.organization_status_id')
            ->leftJoin('ea_ownership_status', 'ea_ownership_status.id', '=', 'apps.ownership_status_id')
            ->leftJoin('country_info as country_of_origin', 'country_of_origin.id', '=', 'apps.country_of_origin_id')
            ->leftJoin('country_info as ceo_country', 'ceo_country.id', '=', 'apps.ceo_country_id')
            ->leftJoin('area_info as ceo_district', 'ceo_district.area_id', '=', 'apps.ceo_district_id')
            ->leftJoin('area_info as ceo_thana', 'ceo_thana.area_id', '=', 'apps.ceo_thana_id')
            ->leftJoin('area_info as office_division', 'office_division.area_id', '=', 'apps.office_division_id')
            ->leftJoin('area_info as office_district', 'office_district.area_id', '=', 'apps.office_district_id')
            ->leftJoin('area_info as office_thana', 'office_thana.area_id', '=', 'apps.office_thana_id')
            ->leftJoin('area_info as factory_district', 'factory_district.area_id', '=', 'apps.factory_district_id')
            ->leftJoin('area_info as factory_thana', 'factory_thana.area_id', '=', 'apps.factory_thana_id')
            ->leftJoin('currencies as local_land_ivst_ccy_tbl', 'local_land_ivst_ccy_tbl.id', '=', 'apps.local_land_ivst_ccy')
            ->leftJoin('currencies as local_building_ivst_ccy_tbl', 'local_building_ivst_ccy_tbl.id', '=', 'apps.local_building_ivst_ccy')
            ->leftJoin('currencies as local_machinery_ivst_ccy_tbl', 'local_machinery_ivst_ccy_tbl.id', '=', 'apps.local_machinery_ivst_ccy')
            ->leftJoin('currencies as local_others_ivst_ccy_tbl', 'local_others_ivst_ccy_tbl.id', '=', 'apps.local_others_ivst_ccy')
            ->leftJoin('currencies as local_wc_ivst_ccy_tbl', 'local_wc_ivst_ccy_tbl.id', '=', 'apps.local_wc_ivst_ccy')
            ->where('process_list.ref_id', $applicationId)
            ->where('process_list.process_type_id', $process_type_id)
            ->first([
                'process_list.id as process_list_id',
                'process_list.desk_id',
                'process_list.user_id',
                'process_list.department_id',
                'process_list.process_type_id',
                'process_list.status_id',
                'process_list.locked_by',
                'process_list.locked_at',
                'process_list.ref_id',
                'process_list.tracking_no',
                'process_list.company_id',
                'process_list.process_desc',
                'process_list.submitted_at',
                'user_desk.desk_name',
                'ps.status_name',
                'ps.color',
                'irc_project_status.name as project_status_name',
                'apps.*',

                'process_type.form_url',

                'sfp.contact_name as sfp_contact_name',
                'sfp.contact_email as sfp_contact_email',
                'sfp.contact_no as sfp_contact_phone',
                'sfp.address as sfp_contact_address',
                'sfp.pay_amount as sfp_pay_amount',
                'sfp.vat_on_pay_amount as sfp_vat_on_pay_amount',
                'sfp.total_amount as sfp_total_amount',
                'sfp.vat_on_transaction_charge as sfp_vat_on_transaction_charge',
                'sfp.transaction_charge_amount as sfp_transaction_charge_amount',
                'sfp.payment_status as sfp_payment_status',
                'sfp.pay_mode as sfp_pay_mode',
                'sfp.pay_mode_code as sfp_pay_mode_code',
                // 'irc_types.attachment_key',
                'ea_organization_type.name as organization_type_name',
                'ea_organization_status.name as organization_status_name',
                'ea_ownership_status.name as ownership_status_name',
                'country_of_origin.nicename as country_of_origin_name',

                'ceo_country.nicename as ceo_country_name',
                'ceo_district.area_nm as ceo_district_name',
                'ceo_thana.area_nm as ceo_thana_name',

                'office_division.area_nm as office_division_name',
                'office_district.area_nm as office_district_name',
                'office_thana.area_nm as office_thana_name',

                'factory_district.area_nm as factory_district_name',
                'factory_thana.area_nm as factory_thana_name',

                'local_land_ivst_ccy_tbl.code as local_land_ivst_ccy_code',
                'local_building_ivst_ccy_tbl.code as local_building_ivst_ccy_code',
                'local_machinery_ivst_ccy_tbl.code as local_machinery_ivst_ccy_code',
                'local_others_ivst_ccy_tbl.code as local_others_ivst_ccy_code',
                'local_wc_ivst_ccy_tbl.code as local_wc_ivst_ccy_code',
                // Reference application
                'ref_process.ref_id as ref_application_ref_id',
                'ref_process.process_type_id as ref_application_process_type_id',
                'ref_process_type.type_key as ref_process_type_key',
            ]);
    }

    public function getAnnualProductionCapacityData($applicationId)
    {
        return AnnualProductionCapacity::leftJoin('product_unit', 'product_unit.id', '=', 'ip_annual_production_capacity.quantity_unit')
            ->where('app_id', $applicationId)
            ->limit(20)
            ->get([
                'ip_annual_production_capacity.*',
                'product_unit.name as unit_name'
            ]);
    }

    public function getBusinessSectorData($classCode)
    {
        return $query = DB::select("
            Select 
            sec_class.id, 
            sec_class.code, 
            sec_class.name, 
            sec_group.id as group_id,
            sec_group.code as group_code,
            sec_group.name as group_name,
            sec_division.id as division_id,
            sec_division.code as division_code,
            sec_division.name as division_name,
            sec_section.id as section_id,
            sec_section.code as section_code,
            sec_section.name as section_name
            from (select * from sector_info_bbs where type = 4) sec_class
            left join sector_info_bbs sec_group on sec_class.pare_id = sec_group.id 
            left join sector_info_bbs sec_division on sec_group.pare_id = sec_division.id 
            left join sector_info_bbs sec_section on sec_division.pare_id = sec_section.id 
            where sec_class.code = '$classCode' limit 1;
        ");
    }

    public function getSourceOfFinanceData($applicationId)
    {
        return IrcSourceOfFinance::leftJoin('country_info', 'country_info.id', '=', 'ip_source_of_finance.country_id')
            ->where('app_id', $applicationId)
            ->get([
                'ip_source_of_finance.equity_amount',
                'ip_source_of_finance.loan_amount',
                'country_info.nicename as country_name',
            ]);
    }

    public function getAppDocumentsData($applicationId)
    {
        return AppDocuments::leftJoin('attachment_list', 'attachment_list.id', '=', 'app_documents.doc_info_id')
            ->leftJoin('attachment_type', 'attachment_type.id', '=', 'attachment_list.attachment_type_id')
            ->where('app_documents.ref_id', $applicationId)
            ->where('app_documents.process_type_id', $this->process_type_id)
            ->get([
                'attachment_list.doc_priority',
                'app_documents.doc_file_path as doc_file_path',
                'app_documents.doc_name',
            ]);
    }

    public function updateRemainingQuantity($listOfMechineryImportedSpare, $statusId)
    {
        foreach ($listOfMechineryImportedSpare as $mechineryImportedSpare) {
            if ($statusId == 25) {
                $mechineryImportedSpare->remaining_quantity = $mechineryImportedSpare->remaining_quantity;
            } else {
                $mechineryImportedSpare->remaining_quantity = $mechineryImportedSpare->remaining_quantity - $mechineryImportedSpare->required_quantity;
            }
        }

        return $listOfMechineryImportedSpare;
    }
}