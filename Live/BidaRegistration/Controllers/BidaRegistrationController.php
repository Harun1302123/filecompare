<?php

namespace App\Modules\BidaRegistration\Controllers;

use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Libraries\ImageProcessing;
use App\Libraries\UtilFunction;
use App\Modules\Apps\Models\AppDocuments;
use App\Modules\Apps\Models\Department;
use App\Modules\BasicInformation\Controllers\BasicInformationController;
use App\Modules\BasicInformation\Models\EA_RegistrationType;
use App\Modules\BidaRegistration\Models\BusinessClass;
use App\Modules\BidaRegistration\Models\ListOfDirectors;
use App\Modules\BidaRegistration\Models\ListOfMachineryImported;
use App\Modules\BidaRegistration\Models\ListOfMachineryLocal;
use App\Modules\BidaRegistration\Models\ProductUnit;
use App\Modules\BidaRegistration\Models\SourceOfFinance;
use App\Modules\Settings\Models\Attachment;
use App\Modules\Settings\Models\Configuration;
use App\Modules\Settings\Models\Sector;
use App\Modules\Settings\Models\SubSector;
use App\Modules\BidaRegistration\Models\BidaRegistration;
use App\Modules\BasicInformation\Models\EA_OrganizationStatus;
use App\Modules\BasicInformation\Models\EA_OrganizationType;
use App\Modules\BasicInformation\Models\EA_OwnershipStatus;
use App\Modules\BidaRegistration\Models\LaAnnualProductionCapacity;
use App\Modules\BidaRegistration\Models\ProjectStatus;
use App\Modules\Settings\Models\HsCodes;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\Settings\Models\Currencies;
use App\Modules\SonaliPayment\Controllers\SonaliPaymentController;
use App\Modules\SonaliPayment\Models\PaymentConfiguration;
use App\Modules\SonaliPayment\Models\PaymentDetails;
use App\Modules\SonaliPayment\Models\PaymentDistribution;
use App\Modules\SonaliPayment\Models\SonaliPayment;
use App\Modules\Users\Models\AreaInfo;
use App\Modules\Users\Models\CompanyInfo;
use App\Modules\Users\Models\Countries;
use App\Modules\Users\Models\DivisionalOffice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Validator;
use yajra\Datatables\Datatables;

class BidaRegistrationController extends Controller
{
    protected $process_type_id;
    protected $aclName;

    public function __construct()
    {
        $this->process_type_id = 102;
        $this->aclName = 'BidaRegistration';
    }

    /*
    * application form
    */
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function applicationForm(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [BRC-1001]';
        }

        if (!ACL::getAccsessRight($this->aclName, '-A-')) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>You have no access right! Contact with system admin for more information. [BRC-971]</h4>"
            ]);
        }

        // Check whether the applicant company is eligible and have approved basic information application
        $company_id = CommonFunction::getUserWorkingCompany();
        if (CommonFunction::checkEligibilityAndBiApps($company_id) != 1) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<div class='btn-center'><h4 class='custom-err-msg'>Sorry! You have no approved Basic Information application for BIDA services. [BRC-9991]</h4> <br/> <a href='/dashboard' class='btn btn-primary btn-sm'>Apply for Basic Information</a></div>"
            ]);
        }

        // Check whether the applicant company's department will get this service
        $department_id = CommonFunction::getDeptIdByCompanyId($company_id);
        if (in_array($department_id, [1, 4])) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>Sorry! The department is not allowed to apply to this application. [BRC-1041]</h4>"
            ]);
        }

        try {
            // Check Submission payment configuration
            $payment_config = PaymentConfiguration::leftJoin('sp_payment_category', 'sp_payment_category.id', '=',
                'sp_payment_configuration.payment_category_id')
                ->where([
                    'sp_payment_configuration.process_type_id' => $this->process_type_id,
                    'sp_payment_configuration.payment_category_id' => 1, // Submission fee payment
                    'sp_payment_configuration.status' => 1,
                    'sp_payment_configuration.is_archive' => 0
                ])->first(['sp_payment_configuration.*', 'sp_payment_category.name']);
            if (empty($payment_config)) {
                return response()->json([
                    'responseCode' => 1,
                    'html' => "<h4 class='custom-err-msg'> Payment Configuration not found ![BRC-10101]</h4>"
                ]);
            }
            $unfixed_amount_array = $this->unfixedAmountsForPayment($payment_config);
            $payment_config->amount = $unfixed_amount_array['total_unfixed_amount'] + $payment_config->amount;
            $payment_config->vat_on_pay_amount = $unfixed_amount_array['total_vat_on_pay_amount'];

            // get company information from Basic Information application, if have not then return back.
            $companyIds = CommonFunction::getUserCompanyWithZero();
            $getCompanyData = ProcessList::leftjoin('ea_apps', 'ea_apps.id', '=', 'process_list.ref_id')
                ->where('process_list.process_type_id', 100)
                ->where('process_list.status_id', 25)
                ->whereIn('process_list.company_id', $companyIds)
                ->first(['ea_apps.*']);
            if (empty($getCompanyData)) {
                return response()->json([
                    'responseCode' => 1,
                    'html' => "<h4 class='custom-err-msg'>Sorry! You have no approved Basic Information application. [BRC-9992]</h4>"
                ]);
            }
            $eaOrganizationType = ['' => 'Select one'] + EA_OrganizationType::where('is_archive', 0)->where('status',
                    1)->whereIn('type', [1, 3])->orderBy('name')->lists('name', 'id')->all();
            $countries = ['' => 'Select one'] + Countries::where('country_status', 'Yes')->orderBy('nicename',
                    'asc')->lists('nicename', 'id')->all();
            $countriesWithoutBD = ['' => 'Select One'] + Countries::where('country_status', 'Yes')->where('id', '!=', '18')->orderBy('nicename',
                    'asc')->lists('nicename', 'id')->all();
            $eaOrganizationStatus = ['' => 'Select one'] + EA_OrganizationStatus::where('is_archive',
                    0)->where('status', 1)->orderBy('name')->lists('name', 'id')->all();
            $eaOwnershipStatus = ['' => 'Select one'] + EA_OwnershipStatus::where('is_archive', 0)->where('status',
                    1)->orderBy('name')->lists('name', 'id')->all();
            $sectors = ['' => 'Select one'] + Sector::orderBy('name')->lists('name', 'id')->all() + [0 => 'Others'];
            $sub_sectors = ['' => 'Select one'] + SubSector::orderBy('name')->lists('name', 'id')->all() + [0 => 'Others'];
            $currencies = Currencies::orderBy('code')->where('is_archive', 0)->where('is_active', 1)->lists('code',
                'id');
            $currencyBDT = Currencies::orderBy('code')->whereIn('code', ['BDT'])->where('is_archive',
                0)->where('is_active', 1)->lists('code', 'id');
            $divisions = ['' => 'Select One'] + AreaInfo::where('area_type', 1)->orderBy('area_nm',
                    'asc')->lists('area_nm', 'area_id')->all();
            $districts = ['' => 'Select One'] + AreaInfo::where('area_type', 2)->orderBy('area_nm',
                    'asc')->lists('area_nm', 'area_id')->all();
            $thana = AreaInfo::orderby('area_nm')->where('area_type', 3)->lists('area_nm', 'area_id');
            $projectStatusList = ProjectStatus::orderBy('name')->where('is_archive', 0)->where('status',
                1)->lists('name', 'id');
            $nationality = ['' => 'Select one'] + Countries::where('country_status', 'Yes')->where('nationality', '!=',
                    '')->orderby('nationality', 'asc')->lists('nationality', 'id')->all();

            $viewMode = 'off';
            $mode = '-A-';
            $usdValue = Currencies::where('code', 'USD')->first();
            $totalFee = DB::table('pay_order_amount_setup')->where('process_type_id', 102)->get([
                'min_amount_bdt', 'max_amount_bdt', 'p_o_amount_bdt'
            ]);
            $productUnit = ['' => 'Select one'] + ProductUnit::where('status', 1)->where('is_archive', 0)->orderBy('name')->lists('name', 'id')->all();
            $add_more_validation = Configuration::where('caption', 'BR_MACHINERY_EQUIPMENT_ADD_MORE')->first(['value', 'details']);
            // $approvalCenterList = DivisionalOffice::where('status', 1)
            //     ->where('is_archive', 0)
            //     ->orderBy('id')
            //     ->get([
            //         'id', 'office_name', 'office_address'
            //     ]);

            $public_html = strval(view("BidaRegistration::application-form",
                compact('countriesWithoutBD', 'countries', 'eaOwnershipStatus', 'currencies',
                    'divisions', 'districts', 'thana', 'projectStatusList', 'sectors', 'nationality', 'sub_sectors', 'eaOrganizationType',
                    'eaOrganizationStatus', 'viewMode', 'mode', 'usdValue', 'totalFee', 'getCompanyData', 'currencyBDT', 'productUnit',
                    'payment_config', 'add_more_validation')));
            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (\Exception $e) {
            Log::error('BRAddForm : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [BRC-1005]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<attachment_typeh4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . ' [BRC-1005]' . "</attachment_typeh4>"
            ]);
        }
    }

    public function getDocList(Request $request)
    {
        $attachment_key = $request->get('attachment_key');
        $viewMode = $request->get('viewMode');
        $app_id = ($request->has('app_id') ? Encryption::decodeId($request->get('app_id')) : 0);

        if (!empty($app_id)) {
            $document_query = AppDocuments::leftJoin('attachment_list', 'attachment_list.id', '=', 'app_documents.doc_info_id')
                ->leftJoin('attachment_type', 'attachment_type.id', '=', 'attachment_list.attachment_type_id')
                ->where('attachment_type.key', $attachment_key)
                ->where('app_documents.ref_id', $app_id)
                ->where('app_documents.process_type_id', $this->process_type_id);
//            if ($viewMode == 'on') {
//                $document_query->where('app_documents.doc_file_path', '!=', '');
//            }

            $document = $document_query->get([
                'attachment_list.id',
                'attachment_list.doc_priority',
                'attachment_list.additional_field',
                'app_documents.id as document_id',
                'app_documents.doc_file_path as doc_file_path',
                'app_documents.doc_name',
            ]);

            if (count($document) < 1) {
                $document = Attachment::leftJoin('attachment_type', 'attachment_type.id', '=', 'attachment_list.attachment_type_id')
                    ->where('attachment_type.key', $attachment_key)
                    ->where('attachment_list.status', 1)
                    ->where('attachment_list.is_archive', 0)
                    ->orderBy('attachment_list.order')
                    ->get(['attachment_list.*']);
            }
        } else {
            $document = Attachment::leftJoin('attachment_type', 'attachment_type.id', '=', 'attachment_list.attachment_type_id')
                ->where('attachment_type.key', $attachment_key)
                ->where('attachment_list.status', 1)
                ->where('attachment_list.is_archive', 0)
                ->orderBy('attachment_list.order')
                ->get(['attachment_list.*']);
        }

        $html = strval(view("BidaRegistration::documents",
            compact('document', 'viewMode')));
        return response()->json(['html' => $html]);
    }


//    public function listOfMachineryInfo(Request $request) {
//        $app_id = Encryption::decodeId($request->get('app_id'));
//        $list_of_machinery_imported = DB::select(DB::raw("Select count(l_machinery_imported_name) as 'total_imported_machinery', sum(l_machinery_imported_qty) as 'total_qty', sum(l_machinery_imported_unit_price) as 'total_unit_price', sum(l_machinery_imported_total_value) as 'total_value'
//                                      from br_list_of_machinery_imported
//                                      where app_id = $app_id;"));
//        $list_of_local_machinery = DB::select(DB::raw("Select count(l_machinery_local_name) as 'total_local_machinery', sum(l_machinery_local_qty) as 'total_qty', sum(l_machinery_local_unit_price) as 'total_unit_price', sum(l_machinery_local_total_value) as 'total_value'
//        from br_list_of_machinery_local
//        where app_id = 398;"));
//        dd($list_of_machinery_imported, $list_of_local_machinery);
//
//    }

//    public function getDocListOld(Request $request)
//    {
//        $app_type_mapping_id = $request->get('app_type_mapping_id');
//        $process_type_id = $this->process_type_id;
//        $viewMode = $request->get('viewMode');
//        if ($request->has('app_id') && $request->get('app_id') != '') {
//            $document = AppDocuments::leftJoin('doc_info', 'doc_info.id', '=', 'app_documents.doc_info_id')
//                ->where('app_documents.ref_id', Encryption::decodeId($request->get('app_id')))
//                ->where('app_documents.process_type_id', $this->process_type_id)
//                ->get([
//                    'doc_info.*', 'app_documents.id as document_id', 'app_documents.doc_file_path as doc_file_path',
//                    'app_documents.is_old_file'
//                ]);
//        } else {
//            $company_id = CommonFunction::getUserWorkingCompany();
//            $document = DB::select(DB::raw("select doc_info.*, abc.doc_file_path, if(abc.doc_file_path != '', 1, 0) as is_old_file
//                from doc_info
//                left join (select `app_documents`.`id`, `app_documents`.`process_type_id`, `app_documents`.`ref_id`, `app_documents`.`doc_info_id`, `app_documents`.`doc_name`,
//                `app_documents`.`doc_file_path`
//                from `app_documents`
//                left join `process_list` on `process_list`.`process_type_id` = `app_documents`.`process_type_id` and `process_list`.`ref_id` = `app_documents`.`ref_id`
//                where `process_list`.`company_id` in ($company_id) and `process_list`.`process_type_id` = 100 and `process_list`.`status_id` = 25 and `app_documents`.`doc_file_path` != ''
//                group by `app_documents`.`doc_name`
//                order by `app_documents`.`doc_info_id` asc) as abc on abc.doc_name = doc_info.doc_name
//                where doc_info.process_type_id = $process_type_id and doc_info.ctg_id = $app_type_mapping_id and doc_info.is_archive = 0 order by `doc_info`.`order`"));
//        }
//
//        $html = strval(view("BidaRegistration::documents",
//            compact('document', 'viewMode')));
//        return response()->json(['html' => $html]);
//    }


    /*
    * Application store
    */
    public function appStore(Request $request)
    {
        // Set permission mode and check ACL
        $app_id = (!empty($request->get('app_id')) ? Encryption::decodeId($request->get('app_id')) : '');
        $mode = (!empty($request->get('app_id')) ? '-E-' : '-A-');
        if (!ACL::getAccsessRight($this->aclName, $mode, $app_id)) {
            abort('400', 'You have no access right! Contact with system admin for more information. [BRC-972]');
        }

        // Check whether the applicant company is eligible and have approved basic information application
        $company_id = CommonFunction::getUserWorkingCompany();
        if (CommonFunction::checkEligibilityAndBiApps($company_id) != 1) {
            Session::flash('error', "Sorry! You have no approved Basic Information application for BIDA services. [BRC-9993]");
            return redirect()->back();
        }

        // Checking the Government & Service Fee Payment configuration for this service
        $payment_config = PaymentConfiguration::leftJoin('sp_payment_category', 'sp_payment_category.id', '=',
            'sp_payment_configuration.id')
            ->where([
                'sp_payment_configuration.process_type_id' => $this->process_type_id,
                'sp_payment_configuration.payment_category_id' => 1,  // Government & Service Fee Payment
                'sp_payment_configuration.status' => 1,
                'sp_payment_configuration.is_archive' => 0,
            ])->first(['sp_payment_configuration.*', 'sp_payment_category.name']);

        if (empty($payment_config)) {
            Session::flash('error', "Payment configuration not found [BRC-101]");
            return redirect()->back()->withInput();
        }

        // Checking the payment distributor under payment configuration
        $stakeDistribution = PaymentDistribution::where('sp_pay_config_id', $payment_config->id)
            ->where('status', 1)
            ->where('is_archive', 0)
            ->get(['id', 'stakeholder_ac_no', 'pay_amount', 'fix_status', 'purpose', 'purpose_sbl', 'distribution_type']);
        if ($stakeDistribution->isEmpty()) {
            Session::flash('error', "Stakeholder not found [BRC-100]");
            return redirect()->back()->withInput();
        }

        // Check whether the applicant company's department will get this service
        $department_id = CommonFunction::getDeptIdByCompanyId($company_id);
        if (in_array($department_id, [1, 4])) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>Sorry! The department is not allowed to apply to this application. [BRC-1042]</h4>"
            ]);
        }

        // Attachment key generate and fetch attachment
        $attachment_key = CommonFunction::generateAttachmentKey($request->organization_status_id, $request->ownership_status_id, "br");

        $doc_row = Attachment::leftJoin('attachment_type', 'attachment_type.id', '=', 'attachment_list.attachment_type_id')
            ->where('attachment_type.key', $attachment_key)
            ->where('attachment_list.status', 1)
            ->where('attachment_list.is_archive', 0)
            ->orderBy('attachment_list.order')
            ->get(['attachment_list.id', 'attachment_list.doc_name', 'attachment_list.doc_priority']);

        // Validation Rules when application submitted
        $rules = [];
        $messages = [];
        if ($request->get('actionBtn') != 'draft') {

            // attachment validation check
            if (count($doc_row) > 0) {
                foreach ($doc_row as $value) {
                    if ($value->doc_priority == 1){
                        $rules['validate_field_'.$value->id] = 'required';
                        $messages['validate_field_'.$value->id.'.required'] = $value->doc_name.', this file is required.';
                    }
                }
            }

            $rules['business_class_code'] = 'required';
            $rules['sub_class_id'] = 'required';
            $rules['other_sub_class_name'] = 'required_if:sub_class_id,-1';
            $rules['total_fixed_ivst'] = 'same:finance_src_loc_total_financing_1';
            $rules['company_name'] = 'required';
            $rules['ownership_status_id'] = 'required';
            if($request->organization_status_id != 3){
                $rules['country_of_origin_id'] = 'required';
            }
            $rules['organization_status_id'] = 'required';
            $rules['ceo_gender'] = 'required';
            $rules['trade_licence_num'] = 'required';
            $rules['trade_licence_issuing_authority'] = 'required';
            $rules['tin_number'] = 'required';
            $rules['office_division_id'] = 'required';
            $rules['office_district_id'] = 'required';
            $rules['office_thana_id'] = 'required';
            $rules['office_mobile_no'] = 'required';
            $rules['office_email'] = 'required';
            $rules['local_machinery_ivst'] = 'required';
            $rules['g_full_name'] = 'required';
            $rules['g_designation'] = 'required';
            $rules['accept_terms'] = 'required';
            $rules['total_sales'] = 'numeric|min:0|max:100';

            $salesTypes = [
                'local_sales' => 'local sales',
                'foreign_sales' => 'foreign sales',
            ];
            
            foreach ($salesTypes as $type => $label) {
                $rules[$type] = 'numeric|min:0|max:100';
                $messages["$type.numeric"] = "The Existing $label must be a number.";
                $messages["$type.min"] = "The Existing $label must be at least 0.";
                $messages["$type.max"] = "The Existing $label cannot be more than 100.";
            }

            if (empty($request->get('investor_signature_base64'))) {
                $rules['investor_signature_name'] = 'required';
                $messages['investor_signature_name.required'] = '>Information of (Chairman/ Managing Director/ Or Equivalent) section Signature field is required.';
            } else {
                $rules['investor_signature_base64'] = 'required';
                $messages['investor_signature_base64.required'] = '>Information of (Chairman/ Managing Director/ Or Equivalent) section Signature field is required.';
            }

            // if ($request->get('local_wc_ivst') >= 100) {
            //     $rules['project_profile_attachment'] = 'required';
            //     $messages['project_profile_attachment.required'] = '>6. Investment section Project profile field is required.';
            // }


            $local_sales = $request->get('local_sales') ?: 0;
            $foreign_sales = $request->get('foreign_sales') ?: 0;
            $total_sales = $local_sales + $foreign_sales;

            if ($total_sales > 100) {
                Session::flash('error', "The sum of Existing local sales and foreign sales should be within the range of 0 to 100");
                return redirect()->back()->withInput();
            }

            /*
             * Total Equity (Million) == Equity Amount (Million BDT)
             * Total Local Loan (Million) == Loan Amount (Million BDT)
             * checking those thing here
             */
            $total_equity = 0; //total equity amount
            $total_loan = 0; //total loan amount

            foreach ($request->equity_amount as $value) {
                $total_equity += $value;
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

        } else {
            $rules['total_sales'] = 'numeric|min:0|max:100';
        }

        $rules['approval_center_id'] = 'required';
        $messages['approval_center_id.required'] = 'Please specify your desired office';
        $messages['total_sales.numeric'] = "The Existing Total Sales value must be a number.";
        $messages['total_sales.min'] = "The Existing Total Sales value must be at least 0.";
        $messages['total_sales.max'] = "The Total Sales value cannot be more than 100.";

        $messages['business_class_code.required'] = 'usiness Sector (BBS Class Code) field is required.';
        $messages['sub_class_id.required'] = 'Info. based on your business class section subclass is required.';
        $messages['other_sub_class_name.required_if'] = 'Info. based on your business class section Other sub class name is required.';
        $messages['total_fixed_ivst.same'] = 'Total Financing and Total Investment (BDT) must be equal.';
        $messages['company_name.required'] = 'A. Company Information section Name of Organization/ Company/ Industrial Project (English) field is required.';
        $messages['ownership_status_id.required'] = 'A. Company Information section Ownership status field is required.';
        $messages['organization_status_id.required'] = 'A. Company Information section Status of the organization field is required.';
        $messages['ceo_gender.required'] = 'B. Information of Principal Promoter/Chairman/Managing Director/CEO/Country Manager section Gender field is required.';
        $messages['trade_licence_num.required'] = '9. Trade licence details section Trade Licence Number field is required.';
        $messages['trade_licence_issuing_authority.required'] = '9. Trade licence details section Issuing Authority field is required.';
        $messages['tin_number.required'] = '10. Tin section Tin Number field is required.';
        $messages['office_division_id.required'] = 'C. Office Address section Division field is required.';
        $messages['office_district_id.required'] = 'C. Office Address section District field is required.';
        $messages['office_thana_id.required'] = 'C. Office Address section Police Station field is required.';
        $messages['office_mobile_no.required'] = 'C. Office Address section Mobile No field is required.';
        $messages['office_email.required'] = 'C. Office Address section Email field is required.';
        $messages['local_machinery_ivst.required'] = '6. Investment section Machinery & Equipment (Million) field is required.';
        $messages['g_full_name.required'] = '>Information of (Chairman/ Managing Director/ Or Equivalent) section Full Name field is required.';
        $messages['g_designation.required'] = '>Information of (Chairman/ Managing Director/ Or Equivalent) section Position/ Designation field is required.';
        $messages['accept_terms.required'] = 'I do here by declare that the information given above is true to the best of 
        my knowledge and I shall be liable for any false information/ statement is given field is required.';

        $this->validate($request, $rules, $messages);


        try {
            DB::beginTransaction();
            if ($request->get('app_id')) {
                $appData = BidaRegistration::find($app_id);
                $processData = ProcessList::where([
                    'process_type_id' => $this->process_type_id, 'ref_id' => $appData->id
                ])->first();
            } else {
                $appData = new BidaRegistration();
                $processData = new ProcessList();
            }

            if ($request->get('organization_status_id') == 3) {
                $appData->country_of_origin_id = 18;
            } else {
                $appData->country_of_origin_id = $request->get('country_of_origin_id');
            }
            $appData->organization_status_id = $request->get('organization_status_id');
            $appData->ownership_status_id = $request->get('ownership_status_id');
            $appData->local_male = $request->get('local_male');
            $appData->local_female = $request->get('local_female');
            $appData->local_total = $request->get('local_total');
            $appData->foreign_male = $request->get('foreign_male');
            $appData->foreign_female = $request->get('foreign_female');
            $appData->foreign_total = $request->get('foreign_total');
            $appData->manpower_total = $request->get('manpower_total');
            $appData->manpower_local_ratio = $request->get('manpower_local_ratio');
            $appData->manpower_foreign_ratio = $request->get('manpower_foreign_ratio');

            // Code of your business class
            if ($request->has('business_class_code')) {

                $business_class = $this->getBusinessClassSingleList($request);
                $get_business_class = json_decode($business_class->getContent(), true);

                if (empty($get_business_class['data'])) {
                    Session::flash('error', "Sorry! Your given Code of business class is not valid. Please enter the right one. [BRC-1017]");
                    return redirect()->back();
                }

                $appData->section_id = $get_business_class['data'][0]['section_id'];
                $appData->division_id = $get_business_class['data'][0]['division_id'];
                $appData->group_id = $get_business_class['data'][0]['group_id'];
                $appData->class_id = $get_business_class['data'][0]['id'];
                $appData->class_code = $get_business_class['data'][0]['code'];

                $appData->sub_class_id = $request->get('sub_class_id') == '-1' ? 0 : $request->get('sub_class_id');
                $appData->other_sub_class_code = $request->get('sub_class_id') == '-1' ? $request->get('other_sub_class_code') : '';
                $appData->other_sub_class_name = $request->get('sub_class_id') == '-1' ? $request->get('other_sub_class_name') : '';
            }

            $appData->office_division_id = $request->get('office_division_id');
            $appData->ceo_spouse_name = $request->get('ceo_spouse_name');

            $appData->ceo_dob = (!empty($request->get('ceo_dob')) ? date('Y-m-d', strtotime($request->get('ceo_dob'))) : null);

            $appData->major_activities = $request->get('major_activities');
            //$appData->factory_mouja = $request->get('factory_mouja');
            $appData->company_name = CommonFunction::getCompanyNameById($company_id);
            $appData->company_name_bn = CommonFunction::getCompanyBnNameById($company_id);
            $appData->organization_type_id = $request->get('organization_type_id');
            $appData->project_name = $request->get('project_name');
            $appData->ceo_full_name = $request->get('ceo_full_name');
            $appData->ceo_designation = $request->get('ceo_designation');
            $appData->ceo_country_id = $request->get('ceo_country_id');
            $appData->ceo_district_id = $request->get('ceo_district_id');
            $appData->ceo_thana_id = $request->get('ceo_thana_id');
            $appData->ceo_post_code = $request->get('ceo_post_code');
            $appData->ceo_address = $request->get('ceo_address');
            $appData->ceo_city = $request->get('ceo_city');
            $appData->ceo_state = $request->get('ceo_state');
            $appData->ceo_telephone_no = $request->get('ceo_telephone_no');
            $appData->ceo_mobile_no = $request->get('ceo_mobile_no');
            $appData->ceo_fax_no = $request->get('ceo_fax_no');
            $appData->ceo_email = $request->get('ceo_email');
            $appData->ceo_father_name = $request->get('ceo_father_name');
            $appData->ceo_mother_name = $request->get('ceo_mother_name');
            $appData->ceo_nid = $request->get('ceo_nid');
            $appData->ceo_passport_no = $request->get('ceo_passport_no');
            $appData->ceo_gender = !empty($request->get('ceo_gender')) ? $request->get('ceo_gender') : 'Not defined';
            $appData->office_district_id = $request->get('office_district_id');
            $appData->office_thana_id = $request->get('office_thana_id');
            $appData->office_post_office = $request->get('office_post_office');
            $appData->office_post_code = $request->get('office_post_code');
            $appData->office_address = $request->get('office_address');
            $appData->office_telephone_no = $request->get('office_telephone_no');
            $appData->office_mobile_no = $request->get('office_mobile_no');
            $appData->office_fax_no = $request->get('office_fax_no');
            $appData->office_email = $request->get('office_email');
            $appData->factory_district_id = $request->get('factory_district_id');
            $appData->factory_thana_id = $request->get('factory_thana_id');
            $appData->factory_post_office = $request->get('factory_post_office');
            $appData->factory_post_code = $request->get('factory_post_code');
            $appData->factory_address = $request->get('factory_address');
            $appData->factory_telephone_no = $request->get('factory_telephone_no');
            $appData->factory_mobile_no = $request->get('factory_mobile_no');
            $appData->factory_fax_no = $request->get('factory_fax_no');
            //$appData->factory_email = $request->get('factory_email');
            $appData->project_status_id = $request->get('project_status_id');
            $appData->commercial_operation_date = (!empty($request->get('commercial_operation_date')) ? date('Y-m-d', strtotime($request->get('commercial_operation_date'))) : null);
            $appData->local_sales = $request->get('local_sales');
            $appData->foreign_sales = $request->get('foreign_sales');
            $appData->total_sales = $request->get('total_sales');

//            $appData->local_fixed_ivst = $request->get('local_fixed_ivst');
//            $appData->local_fixed_ivst_ccy = $request->get('local_fixed_ivst_ccy');
//            $appData->foreign_fixed_ivst = $request->get('foreign_fixed_ivst');
//            $appData->foreign_fixed_ivst_ccy = $request->get('foreign_fixed_ivst_ccy');
//            $appData->total_fixed_ivst_single = $request->get('total_fixed_ivst_single');

            $appData->local_land_ivst = (float)$request->get('local_land_ivst');
            //database can't take more fields
            $appData->local_land_ivst_ccy = $request->get('local_land_ivst_ccy');
//            $appData->foreign_land_ivst = $request->get('foreign_land_ivst');
//            $appData->foreign_land_ivst_ccy = $request->get('foreign_land_ivst_ccy');
//            $appData->total_land_ivst = $request->get('total_land_ivst');

            $appData->local_machinery_ivst = (float)$request->get('local_machinery_ivst');
            $appData->local_machinery_ivst_ccy = $request->get('local_machinery_ivst_ccy');
//            $appData->foreign_machinery_ivst = $request->get('foreign_machinery_ivst');
//            $appData->foreign_machinery_ivst_ccy = $request->get('foreign_machinery_ivst_ccy');
//            $appData->total_machinery_ivst = $request->get('total_machinery_ivst');
            $appData->local_building_ivst = (float)$request->get('local_building_ivst');
            $appData->local_building_ivst_ccy = $request->get('local_building_ivst_ccy');

            $appData->local_others_ivst = (float)$request->get('local_others_ivst');
            $appData->local_others_ivst_ccy = $request->get('local_others_ivst_ccy');
//            $appData->foreign_others_ivst = $request->get('foreign_others_ivst');
//            $appData->foreign_others_ivst_ccy = $request->get('foreign_others_ivst_ccy');
//            $appData->total_others_ivst = $request->get('total_others_ivst');

            $appData->local_wc_ivst = (float)$request->get('local_wc_ivst');
            $appData->local_wc_ivst_ccy = $request->get('local_wc_ivst_ccy');
//            $appData->foreign_wc_ivst = $request->get('foreign_wc_ivst');
//            $appData->foreign_wc_ivst_ccy = $request->get('foreign_wc_ivst_ccy');
//            $appData->total_wc_ivst = $request->get('total_wc_ivst');

            $appData->total_fixed_ivst = $request->get('total_fixed_ivst');
            $appData->total_fixed_ivst_million = $request->get('total_fixed_ivst_million');
            $appData->usd_exchange_rate = $request->get('usd_exchange_rate');

            $appData->total_fee = self::BRInvestmentFeeCalculation($request->get('total_fixed_ivst_million'));

            if ($request->hasFile('project_profile_attachment')) {
                $yearMonth = date("Y") . "/" . date("m") . "/";
                $path = 'uploads/' . $yearMonth;
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $_file_path = $request->file('project_profile_attachment');
                $file_path = trim(uniqid('BR_PPA-' . time() . '-', true) . $_file_path->getClientOriginalName());
                $_file_path->move($path, $file_path);
                $appData->project_profile_attachment = $yearMonth . $file_path;
            } else {
                $appData->project_profile_attachment = $request->get('project_profile_attachment_data');
            }

            if($request->get('organization_status_id') == 2){
                $appData->finance_src_foreign_equity_1 = $request->get('finance_src_foreign_equity_1');
                $appData->finance_src_loc_equity_1 = '';
            }elseif ($request->get('organization_status_id') == 3){
                $appData->finance_src_loc_equity_1 = $request->get('finance_src_loc_equity_1');
                $appData->finance_src_foreign_equity_1 = '';
            }else{
                $appData->finance_src_foreign_equity_1 = $request->get('finance_src_foreign_equity_1');
                $appData->finance_src_loc_equity_1 = $request->get('finance_src_loc_equity_1');
            }
//            $appData->finance_src_loc_equity_2 = $request->get('finance_src_loc_equity_2'); // percent
//            $appData->finance_src_foreign_equity_2 = $request->get('finance_src_foreign_equity_2'); //percent

            $appData->finance_src_loc_total_equity_1 = $request->get('finance_src_loc_total_equity_1');

            $appData->finance_src_loc_loan_1 = $request->get('finance_src_loc_loan_1');
//            $appData->finance_src_loc_loan_2 = $request->get('finance_src_loc_loan_2'); // percent
            $appData->finance_src_foreign_loan_1 = $request->get('finance_src_foreign_loan_1');
//            $appData->finance_src_foreign_loan_2 = $request->get('finance_src_foreign_loan_2'); // percent
            $appData->finance_src_total_loan = $request->get('finance_src_total_loan');

            $appData->finance_src_loc_total_financing_m = $request->get('finance_src_loc_total_financing_m');
            $appData->finance_src_loc_total_financing_1 = $request->get('finance_src_loc_total_financing_1');
            $appData->finance_src_loc_total_financing_2 = "";
            $appData->public_land = isset($request->public_land) ? 1 : 0;
            $appData->public_electricity = isset($request->public_electricity) ? 1 : 0;
            $appData->public_gas = isset($request->public_gas) ? 1 : 0;
            $appData->public_telephone = isset($request->public_telephone) ? 1 : 0;
            $appData->public_road = isset($request->public_road) ? 1 : 0;
            $appData->public_water = isset($request->public_water) ? 1 : 0;
            $appData->public_drainage = isset($request->public_drainage) ? 1 : 0;
            $appData->public_others = isset($request->public_others) ? 1 : 0;
            $appData->public_others_field = $request->get('public_others_field');
            $appData->trade_licence_num = $request->get('trade_licence_num');
            $appData->trade_licence_issuing_authority = $request->get('trade_licence_issuing_authority');
            $appData->tin_number = $request->get('tin_number');
            $appData->machinery_local_qty = $request->get('machinery_local_qty');
            $appData->machinery_local_price_bdt = $request->get('machinery_local_price_bdt');
            $appData->imported_qty = $request->get('imported_qty');
            $appData->imported_qty_price_bdt = $request->get('imported_qty_price_bdt');
            $appData->total_machinery_price = $request->get('total_machinery_price');
            $appData->total_machinery_qty = $request->get('total_machinery_qty');
            $appData->local_description = $request->get('local_description');
            $appData->imported_description = $request->get('imported_description');

            $appData->g_full_name = $request->get('g_full_name');
            $appData->g_designation = $request->get('g_designation');

            //Authorized Person Information
            $appData->auth_full_name = $request->get('auth_full_name');
            $appData->auth_designation = $request->get('auth_designation');
            $appData->auth_mobile_no = $request->get('auth_mobile_no');
            $appData->auth_email = $request->get('auth_email');
            $appData->auth_image = $request->get('auth_image');

            if (isset($request->investor_signature_base64) && $request->investor_signature_base64 != '') {
                $yearMonth = date("Y") . "/" . date("m") . "/";
                $path = 'uploads/' . $yearMonth;
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                $splited = explode(',', substr($request->get('investor_signature_base64'), 5), 2);
                $imageData = $splited[1];
                $base64ResizeImage = base64_encode(ImageProcessing::resizeBase64Image($imageData, 300, 80));
                $base64ResizeImage = base64_decode($base64ResizeImage);
                $file_name = trim(sprintf("%s", uniqid('BIDA_BR_', true))) . str_replace(" ", "_", $request->get('investor_signature_name'));

                file_put_contents($path . $file_name, $base64ResizeImage);
                $appData->g_signature = $yearMonth . $file_name;

            }

            $appData->accept_terms = (!empty($request->get('accept_terms')) ? 1 : 0);
            if ($request->get('actionBtn') == "draft") {
                $processData->status_id = -1;
                $processData->desk_id = 0;
            } else {
                if ($processData->status_id == 5) { // For shortfall application re-submission
                    $resubmission_data = CommonFunction::getReSubmissionJson($this->process_type_id, $app_id);
                    $processData->status_id = $resubmission_data['process_starting_status'];
                    $processData->desk_id = $resubmission_data['process_starting_desk'];
                    $processData->process_desc = 'Re-submitted from applicant';
                } else {  // For new application submission
                    $processData->status_id = -1;
                    $processData->desk_id = 0;
                }
            }
            $appData->company_id = $company_id;
            $appData->save();

            // Annual production capacity
            if (!empty($appData->id)) {

                $annualProductionCapacityIDs = [];
                foreach ($request->get('apc_product_name') as $key => $value) {
                    $annualProductionCapacityID = $request->get('annual_production_capacity_id')[$key];
                    $annualProductionCapacity = LaAnnualProductionCapacity::findOrNew($annualProductionCapacityID);
                    $annualProductionCapacity->app_id = $appData->id;
                    $annualProductionCapacity->product_name = $request->get('apc_product_name')[$key];;
                    $annualProductionCapacity->quantity_unit = $request->get('apc_quantity_unit')[$key];
                    $annualProductionCapacity->quantity = $request->get('apc_quantity')[$key];
                    $annualProductionCapacity->price_usd = $request->get('apc_price_usd')[$key];
                    $annualProductionCapacity->price_taka = $request->get('apc_value_taka')[$key];
                    $annualProductionCapacity->save();
                    $annualProductionCapacityIDs[] = $annualProductionCapacity->id;
                }

                if (count($annualProductionCapacityIDs) > 0) {
                    LaAnnualProductionCapacity::where('app_id', $appData->id)->whereNotIn('id', $annualProductionCapacityIDs)->delete();
                }
            }

            // Country wise source of finance (Million BDT)
            if (!empty($appData->id)) {
                $source_of_finance_ids = [];
                foreach ($request->get('country_id') as $key => $value) {
                    $source_of_finance_id = $request->get('source_of_finance_id')[$key];
                    $source_of_finance = SourceOfFinance::findOrNew($source_of_finance_id);
                    $source_of_finance->app_id = $appData->id;
                    $source_of_finance->country_id = $request->get('country_id')[$key];
                    $source_of_finance->equity_amount = $request->get('equity_amount')[$key];
                    $source_of_finance->loan_amount = $request->get('loan_amount')[$key];
                    $source_of_finance->save();
                    $source_of_finance_ids[] = $source_of_finance->id;
                }

                if (count($source_of_finance_ids) > 0) {
                    SourceOfFinance::where('app_id', $appData->id)->whereNotIn('id', $source_of_finance_ids)->delete();
                }
            }

            // List of directors
            if (!empty($appData->id) && !empty(trim($request->get('l_director_name')[0]))) {
                $listOfDirectorsIDs = [];

                foreach ($request->get('l_director_name') as $key => $value) {
                    $listOfDirectorID = $request->get('list_of_director_id')[$key];
                    $listOfDirector = ListOfDirectors::findOrNew($listOfDirectorID);
                    $listOfDirector->app_id = $appData->id;
                    $listOfDirector->process_type_id = $this->process_type_id;
                    $listOfDirector->l_director_name = $request->get('l_director_name')[$key];
                    $listOfDirector->l_director_designation = $request->get('l_director_designation')[$key];
                    $listOfDirector->l_director_nationality = $request->get('l_director_nationality')[$key];
                    $listOfDirector->nid_etin_passport = $request->get('nid_etin_passport')[$key];

                    $listOfDirector->save();
                    $listOfDirectorsIDs[] = $listOfDirector->id;
                }

                if (count($listOfDirectorsIDs) > 0) {
                    ListOfDirectors::where('app_id', $appData->id)
                        ->where('process_type_id', $this->process_type_id)
                        ->whereNotIn('id', $listOfDirectorsIDs)
                        ->delete();
                }
            }

            // List of machinery to be imported
            if (!empty($appData->id) && !empty(trim($request->get('l_machinery_imported_name')[0]))) {

                $listOfMachineryImportedIDs = [];

                foreach ($request->get('l_machinery_imported_name') as $key => $value) {
                    $listOfMachineryImportedID = $request->get('list_of_machinery_imported_id')[$key];
                    $listOfMachineryImported = ListOfMachineryImported::findOrNew($listOfMachineryImportedID);
                    $listOfMachineryImported->app_id = $appData->id;
                    $listOfMachineryImported->process_type_id = $this->process_type_id;
                    $listOfMachineryImported->l_machinery_imported_name = $request->get('l_machinery_imported_name')[$key];
                    $listOfMachineryImported->l_machinery_imported_qty = $request->get('l_machinery_imported_qty')[$key];
                    $listOfMachineryImported->l_machinery_imported_unit_price = $request->get('l_machinery_imported_unit_price')[$key];
                    $listOfMachineryImported->l_machinery_imported_total_value = $request->get('l_machinery_imported_total_value')[$key];

                    $listOfMachineryImported->save();
                    $listOfMachineryImportedIDs[] = $listOfMachineryImported->id;
                }

                if (count($listOfMachineryImportedIDs) > 0) {
                    ListOfMachineryImported::where('app_id', $appData->id)
                        ->where('process_type_id', $this->process_type_id)
                        ->whereNotIn('id', $listOfMachineryImportedIDs)
                        ->delete();
                }
            }

            // List of machinery locally purchase/ procur
            if (!empty($appData->id) && !empty(trim($request->get('l_machinery_local_name')[0]))) {
                $listOfMachineryLocalIDs = [];
                foreach ($request->get('l_machinery_local_name') as $key => $value) {
                    $listOfMachineryLocalID = $request->get('list_of_machinery_local_id')[$key];
                    $listOfMachineryLocal = ListOfMachineryLocal::findOrNew($listOfMachineryLocalID);
                    $listOfMachineryLocal->app_id = $appData->id;
                    $listOfMachineryLocal->process_type_id = $this->process_type_id;
                    $listOfMachineryLocal->l_machinery_local_name = $request->get('l_machinery_local_name')[$key];
                    $listOfMachineryLocal->l_machinery_local_qty = $request->get('l_machinery_local_qty')[$key];
                    $listOfMachineryLocal->l_machinery_local_unit_price = $request->get('l_machinery_local_unit_price')[$key];
                    $listOfMachineryLocal->l_machinery_local_total_value = $request->get('l_machinery_local_total_value')[$key];

                    $listOfMachineryLocal->save();
                    $listOfMachineryLocalIDs[] = $listOfMachineryLocal->id;
                }

                if (count($listOfMachineryLocalIDs) > 0) {
                    ListOfMachineryLocal::where('app_id', $appData->id)
                        ->where('process_type_id', $this->process_type_id)
                        ->whereNotIn('id', $listOfMachineryLocalIDs)
                        ->delete();
                }
            }

            /*
            * Department and Sub-department specification for application processing
            */
            $deptSubDeptData = [
                'company_id' => $company_id,
                'department_id' => $department_id,
                'app_type' => $request->get('organization_status_id'),
            ];
            $deptAndSubDept = CommonFunction::DeptSubDeptSpecification($this->process_type_id, $deptSubDeptData);
            $processData->department_id = $deptAndSubDept['department_id'];
            $processData->sub_department_id = $deptAndSubDept['sub_department_id'];
            $processData->ref_id = $appData->id;
            $processData->process_type_id = $this->process_type_id;
            $processData->process_desc = '';// for re-submit application
            $processData->company_id = $company_id;
            $processData->read_status = 0;
            $processData->approval_center_id = $request->get('approval_center_id');

            // dd($processData->approval_center_id , $request->get('approval_center_id'));

            $jsonData['Applied by'] = CommonFunction::getUserFullName();
            $jsonData['Email'] = Auth::user()->user_email;
            $jsonData['Mobile'] = Auth::user()->user_phone;
//            $jsonData['Company Name'] = CommonFunction::getCompanyNameById($company_id);
//            $jsonData['Department'] = CommonFunction::getDepartmentNameById($processData->department_id);
            $processData['json_object'] = json_encode($jsonData);
            $processData->save();


            //  Required Documents for attachment
            if (count($doc_row) > 0) {
                foreach ($doc_row as $docs) {
                    $app_doc = AppDocuments::firstOrNew([
                        'process_type_id' => $this->process_type_id,
                        'ref_id' => $appData->id,
                        'doc_info_id' => $docs->id
                    ]);
                    $app_doc->doc_name = $docs->doc_name;
                    $app_doc->doc_file_path = $request->get('validate_field_' . $docs->id);
                    $app_doc->save();
                }
            } /* End file uploading */

            // Payment info will not be updated for resubmit
            if ($processData->status_id != 2) {

                // Store payment info
                $paymentInfo = SonaliPayment::firstOrNew([
                    'app_id' => $appData->id, 'process_type_id' => $this->process_type_id,
                    'payment_config_id' => $payment_config->id
                ]);

                $paymentInfo->payment_config_id = $payment_config->id;
                $paymentInfo->app_id = $appData->id;
                $paymentInfo->process_type_id = $this->process_type_id;
                $paymentInfo->app_tracking_no = '';
                $paymentInfo->payment_category_id = $payment_config->payment_category_id;

                //Concat account no of all stakeholder
                $account_no = "";
                foreach ($stakeDistribution as $distribution) {
                    $account_no .= $distribution->stakeholder_ac_no . "-";
                }
                $account_numbers = rtrim($account_no, '-');
                //Concat account no of all stakeholder End

                $paymentInfo->receiver_ac_no = $account_numbers;

                $unfixed_amount_array = $this->unfixedAmountsForPayment($payment_config);

                $paymentInfo->pay_amount = $unfixed_amount_array['total_unfixed_amount'] + $payment_config->amount;
                $paymentInfo->vat_on_pay_amount = $unfixed_amount_array['total_vat_on_pay_amount'];
                $paymentInfo->total_amount = ($paymentInfo->pay_amount + $paymentInfo->vat_on_pay_amount);
                $paymentInfo->contact_name = $request->get('sfp_contact_name');
                $paymentInfo->contact_email = $request->get('sfp_contact_email');
                $paymentInfo->contact_no = $request->get('sfp_contact_phone');
                $paymentInfo->address = $request->get('sfp_contact_address');
                $paymentInfo->sl_no = 1; // Always 1
                $paymentInfo->save();

                $appData->sf_payment_id = $paymentInfo->id;
                $appData->save();

                foreach ($stakeDistribution as $distribution) {
                    $paymentDetails = PaymentDetails::firstOrNew([
                        'sp_payment_id' => $paymentInfo->id, 'payment_distribution_id' => $distribution->id
                    ]);
                    $paymentDetails->sp_payment_id = $paymentInfo->id;
                    $paymentDetails->payment_distribution_id = $distribution->id;
                    $paymentDetails->receiver_ac_no = $distribution->stakeholder_ac_no;
                    $paymentDetails->fix_status = $distribution->fix_status;
                    $paymentDetails->purpose = $distribution->purpose;
                    $paymentDetails->purpose_sbl = $distribution->purpose_sbl;
                    $paymentDetails->distribution_type = $distribution->distribution_type;
                    if ($distribution->fix_status == 1) {
                        $paymentDetails->pay_amount = $distribution->pay_amount;
                    } else {
                        $paymentDetails->pay_amount = $unfixed_amount_array['amounts'][$distribution->distribution_type];
                    }
                    $paymentDetails->save();
                }
                //Payment Details By Stakeholders End
            }

            // dd($request->get('actionBtn') , $processData->status_id ,$processData->tracking_no);
            /*
            * if action is submitted and application status is equal to draft
            * and have payment configuration then, generate a tracking number
            * and go to payment initiator function.
            */

            if ($request->get('actionBtn') == 'Submit' && $processData->status_id == -1) {
                if (empty($processData->tracking_no)) {
                    $prefix = 'BR-' . date("dMY") . '-';
                    UtilFunction::generateTrackingNumber($this->process_type_id, $processData->id, $prefix);
                }
                DB::commit();
                return redirect('spg/initiate-multiple/' . Encryption::encodeId($paymentInfo->id));
            }
            
            // Send Email notification to user on application re-submit
            if ($request->get('actionBtn') != "draft" && $processData->status_id == 2) {
                $processData = ProcessList::leftJoin('process_type', 'process_type.id', '=',
                    'process_list.process_type_id')
                    ->where('process_list.id', $processData->id)
                    ->first([
                        'process_type.name as process_type_name',
                        'process_type.process_supper_name',
                        'process_type.process_sub_name',
                        'process_list.*'
                    ]);

                //get users email and phone no according to working company id
                $applicantEmailPhone = UtilFunction::geCompanyUsersEmailPhone($company_id);

                $appInfo = [
                    'app_id' => $processData->ref_id,
                    'status_id' => $processData->status_id,
                    'process_type_id' => $processData->process_type_id,
                    'tracking_no' => $processData->tracking_no,
                    'process_sub_name' => $processData->process_sub_name,
                    'process_supper_name' => $processData->process_supper_name,
                    'process_type_name' => $processData->process_type_name,
                    'remarks' => ''
                ];

                CommonFunction::sendEmailSMS('APP_RESUBMIT', $appInfo, $applicantEmailPhone);
            }

            if ($processData->status_id == -1) {
                Session::flash('success', 'Successfully updated the Application!');
            } elseif ($processData->status_id == 1) {
                Session::flash('success', 'Successfully Application Submitted !');
            } elseif (in_array($processData->status_id, [2])) {
                Session::flash('success', 'Successfully Application Re-Submitted !');
            } else {
                Session::flash('error', 'Failed due to Application Status Conflict. Please try again later! [BRC-1023]');
            }
            DB::commit();
            return redirect('bida-registration/list/' . Encryption::encodeId($this->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('BRAppStore : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [BRC-1060]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [BRC-1060]');
            return redirect()->back()->withInput();
        }
    }

    /*
     * Application edit
     */
    public function applicationEdit($applicationId, $openMode = "", Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [BRC-1002]';
        }

        $mode = '-E-';
        $viewMode = 'off';

        // it's enough to check ACL for view mode only
        if (!ACL::getAccsessRight($this->aclName, $mode)) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>You have no access right! Contact with system admin for more information. [BRC-973]</h4>"
            ]);
        }

        $company_id = CommonFunction::getUserWorkingCompany();
        // Check whether the applicant company's department will get this service
        $department_id = CommonFunction::getDeptIdByCompanyId($company_id);
        if (in_array($department_id, [1, 4])) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>Sorry! The department is not allowed to apply to this application. [BRC-1043]</h4>"
            ]);
        }

        try {

            $applicationId = Encryption::decodeId($applicationId);
            $process_type_id = $this->process_type_id;
            $appInfo = ProcessList::leftJoin('br_apps as apps', 'apps.id', '=', 'process_list.ref_id')
                ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
                ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                    $join->on('ps.id', '=', 'process_list.status_id');
                    $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
                })
                ->leftJoin('project_status', 'project_status.id', '=', 'apps.project_status_id')
                ->leftJoin('sp_payment as sfp', 'sfp.id', '=', 'apps.sf_payment_id')
                ->leftJoin('sp_payment as gfp', 'gfp.id', '=', 'apps.gf_payment_id')
                ->where('process_list.ref_id', $applicationId)
                ->where('process_list.process_type_id', $process_type_id)
                ->where('process_list.company_id', $company_id)
                ->first([
                    'process_list.id as process_list_id',
                    'process_list.desk_id',
                    'process_list.approval_center_id',
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
                    'project_status.name as project_status_name',
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

                    'gfp.contact_name as gfp_contact_name',
                    'gfp.contact_email as gfp_contact_email',
                    'gfp.contact_no as gfp_contact_phone',
                    'gfp.address as gfp_contact_address',
                    'gfp.pay_amount as gfp_pay_amount',
                    'gfp.vat_on_pay_amount as gfp_vat_on_pay_amount',
                    'gfp.total_amount as gfp_total_amount',
                    'gfp.payment_status as gfp_payment_status',
                    'gfp.pay_mode as gfp_pay_mode',
                    'gfp.pay_mode_code as gfp_pay_mode_code',
                ]);

            $laAnnualProductionCapacity = LaAnnualProductionCapacity::where('app_id', $applicationId)->get();
            $departmentList = Department::where('is_archive', 0)->where('status', 1)->orderBy('name')->lists('name',
                'id')->all();
            $eaOwnershipStatus = ['' => 'Select one'] + EA_OwnershipStatus::where('is_archive', 0)->where('status',
                    1)->orderBy('name')->lists('name', 'id')->all();
            $sectors = ['' => 'Select one'] + Sector::orderBy('name')->lists('name', 'id')->all() + [0 => 'Others'];
            $sub_sectors = ['' => 'Select one'] + SubSector::orderBy('name')->lists('name', 'id')->all() + [0 => 'Others'];
            $eaRegistrationType = ['' => 'Select one'];
            $eaOrganizationStatus = ['' => 'Select one'] + EA_OrganizationStatus::where('is_archive',
                    0)->where('status', 1)->orderBy('name')->lists('name', 'id')->all();
            $eaOrganizationType = ['' => 'Select one'] + EA_OrganizationType::where('is_archive', 0)->where('status',
                    1)->whereIn('type', [1, 3])->orderBy('name')->lists('name', 'id')->all();
            $divisions = ['' => 'Select One'] + AreaInfo::where('area_type', 1)->orderBy('area_nm',
                    'asc')->lists('area_nm', 'area_id')->all();
            $districts = ['' => 'Select One'] + AreaInfo::where('area_type', 2)->orderBy('area_nm',
                    'asc')->lists('area_nm', 'area_id')->all();
            $countriesWithoutBD = ['' => 'Select One'] + Countries::where('country_status', 'Yes')->where('id', '!=', '18')->orderBy('nicename',
                    'asc')->lists('nicename', 'id')->all();
            $countries = ['' => 'Select One'] + Countries::where('country_status', 'Yes')->orderBy('nicename',
                    'asc')->lists('nicename', 'id')->all();
            $nationality = ['' => 'Select one'] + Countries::where('country_status', 'Yes')->where('nationality', '!=',
                    '')->orderby('nationality', 'asc')->lists('nationality', 'id')->all();
            $listOfDirectors = ListOfDirectors::where('app_id', $applicationId)->where('process_type_id', $this->process_type_id)->get();
            $listOfMachineryImported = ListOfMachineryImported::where('app_id', $applicationId)->where('process_type_id', $this->process_type_id)->get();
            $listOfMachineryImportedTotal = ListOfMachineryImported::where('app_id', $applicationId)->where('process_type_id', $this->process_type_id)->sum('l_machinery_imported_total_value');
            $listOfMachineryLocal = ListOfMachineryLocal::where('app_id', $applicationId)->where('process_type_id', $this->process_type_id)->get();
            $listOfMachineryLocalTotal = ListOfMachineryLocal::where('app_id', $applicationId)->where('process_type_id', $this->process_type_id)->sum('l_machinery_local_total_value');
            $source_of_finance = SourceOfFinance::where('app_id', $applicationId)->get();
            $currencies = Currencies::orderBy('code')->where('is_archive', 0)->where('is_active', 1)->lists('code',
                'id');
            // $approvalCenterList = DivisionalOffice::where('status', 1)
            //     ->where('is_archive', 0)
            //     ->orderBy('id')
            //     ->get([
            //         'id', 'office_name', 'office_address'
            //     ]);

            $usdValue = Currencies::where('code', 'USD')->first();
            $projectStatusList = ProjectStatus::orderBy('name')->where('is_archive', 0)->where('status',
                1)->lists('name', 'id');
            $totalFee = DB::table('pay_order_amount_setup')->where('process_type_id', 102)->get([
                'min_amount_bdt', 'max_amount_bdt', 'p_o_amount_bdt'
            ]);
            $productUnit = ['' => 'Select one'] + ProductUnit::where('status', 1)
                    ->where('is_archive', 0)
                    ->orderBy('name')
                    ->lists('name', 'id')
                    ->all();

//            if ($viewMode == 'on') {
//                $attachment_key = "br_";
//                if ($appInfo->organization_status_id == 3) {
//                    $attachment_key .= "local";
//                } else if ($appInfo->organization_status_id == 2) {
//                    $attachment_key .= "foreign";
//                } else {
//                    $attachment_key .= "joint_venture";
//                }
//
//                $document = AppDocuments::leftJoin('attachment_list', 'attachment_list.id', '=', 'app_documents.doc_info_id')
//                    ->leftJoin('attachment_type', 'attachment_type.id', '=', 'attachment_list.attachment_type_id')
//                    ->where('attachment_type.key', $attachment_key)
//                    ->where('app_documents.ref_id', $applicationId)
//                    ->where('app_documents.process_type_id', $this->process_type_id)
//                    ->where('app_documents.doc_file_path', '!=', '')
//                    ->get([
//                        'attachment_list.id',
//                        'attachment_list.doc_priority',
//                        'attachment_list.additional_field',
//                        'app_documents.id as document_id',
//                        'app_documents.doc_file_path as doc_file_path',
//                        'app_documents.doc_name',
//                    ]);
//            }

            $shortfall_readonly_sections = json_encode($this->getShortfallReviewSections($appInfo));

            $public_html = strval(view("BidaRegistration::application-form-edit",
                compact('appInfo', 'countries', 'countriesWithoutBD', 'viewMode', 'projectStatusList',
                    'mode', 'eaOwnershipStatus', 'sectors', 'listOfDirectors', 'listOfMachineryImported',
                    'listOfMachineryLocal', 'sub_sectors', 'nationality', 'eaOrganizationType', 'totalFee',
                    'eaOrganizationStatus', 'eaRegistrationType', 'divisions', 'districts', 'departmentList',
                    'currencies', 'laAnnualProductionCapacity', 'usdValue', 'productUnit', 'source_of_finance', 'listOfMachineryImportedTotal', 'listOfMachineryLocalTotal', 'shortfall_readonly_sections')));

            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (\Exception $e) {
            Log::error('BRViewEditForm : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [BRC-1010]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . "[BRC-1010]" . "</h4>"
            ]);
        }
    }

    private function getShortfallReviewSections($appInfo)
    {
        $data = [];

        if ($appInfo->status_id == 5) {
            if ($appInfo->company_info_review == 0) {
                $data[] = 'company_info_review';
            }
            if ($appInfo->promoter_info_review == 0) {
                $data[] = 'promoter_info_review';
            }
            if ($appInfo->office_address_review == 0) {
                $data[] = 'office_address_review';
            }
            if ($appInfo->factory_address_review == 0) {
                $data[] = 'factory_address_review';
            }
            if ($appInfo->project_status_review == 0) {
                $data[] = 'project_status_review';
            }
            if ($appInfo->production_capacity_review == 0) {
                $data[] = 'production_capacity_review';
            }
            if ($appInfo->commercial_operation_review == 0) {
                $data[] = 'commercial_operation_review';
            }
            if ($appInfo->sales_info_review == 0) {
                $data[] = 'sales_info_review';
            }
            if ($appInfo->manpower_review == 0) {
                $data[] = 'manpower_review';
            }
            if ($appInfo->investment_review == 0) {
                $data[] = 'investment_review';
            }
            if ($appInfo->source_finance_review == 0) {
                $data[] = 'source_finance_review';
            }
            if ($appInfo->utility_service_review == 0) {
                $data[] = 'utility_service_review';
            }
            if ($appInfo->trade_license_review == 0) {
                $data[] = 'trade_license_review';
            }
            if ($appInfo->tin_review == 0) {
                $data[] = 'tin_review';
            }
            if ($appInfo->machinery_equipment_review == 0) {
                $data[] = 'machinery_equipment_review';
            }
            if ($appInfo->raw_materials_review == 0) {
                $data[] = 'raw_materials_review';
            }
            if ($appInfo->ceo_info_review == 0) {
                $data[] = 'ceo_info_review';
            }
            if ($appInfo->director_list_review == 0) {
                $data[] = 'director_list_review';
            }
            if ($appInfo->imported_machinery_review == 0) {
                $data[] = 'imported_machinery_review';
            }
            if ($appInfo->local_machinery_review == 0) {
                $data[] = 'local_machinery_review';
            }
            if ($appInfo->attachment_review == 0) {
                $data[] = 'attachment_review';
            }
            if ($appInfo->declaration_review == 0) {
                $data[] = 'declaration_review';
            }
        }
        
        return $data;
    }

    /*
     * Application View
     */
    public function applicationView($appId, Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [BRC-1003]';
        }

        $viewMode = 'on';
        $mode = '-V-';

        // it's enough to check ACL for view mode only
        if (!ACL::getAccsessRight($this->aclName, $mode)) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>You have no access right! Contact with system admin for more information. [BRC-974]</h4>"
            ]);
        }

        try {
            $decodedAppId = Encryption::decodeId($appId);
            $process_type_id = $this->process_type_id;

            // get application,process info
            $appInfo = ProcessList::leftJoin('br_apps as apps', 'apps.id', '=', 'process_list.ref_id')
                ->leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
                ->leftJoin('divisional_office', 'divisional_office.id', '=', 'process_list.approval_center_id')
                ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                    $join->on('ps.id', '=', 'process_list.status_id');
                    $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
                })
                ->leftJoin('dept_application_type as app_type', 'app_type.id', '=', 'apps.app_type_id')// app type
                ->leftJoin('sp_payment as sfp', 'sfp.id', '=', 'apps.sf_payment_id')
                ->leftJoin('sp_payment as gfp', 'gfp.id', '=', 'apps.gf_payment_id')
                ->leftJoin('company_info', 'company_info.id', '=', 'apps.company_id')
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
                ->leftJoin('project_status', 'project_status.id', '=', 'apps.project_status_id')
                ->leftJoin('currencies as local_land_ivst_ccy_tbl', 'local_land_ivst_ccy_tbl.id', '=', 'apps.local_land_ivst_ccy')
                ->leftJoin('currencies as local_building_ivst_ccy_tbl', 'local_building_ivst_ccy_tbl.id', '=', 'apps.local_building_ivst_ccy')
                ->leftJoin('currencies as local_machinery_ivst_ccy_tbl', 'local_machinery_ivst_ccy_tbl.id', '=', 'apps.local_machinery_ivst_ccy')
                ->leftJoin('currencies as local_others_ivst_ccy_tbl', 'local_others_ivst_ccy_tbl.id', '=', 'apps.local_others_ivst_ccy')
                ->leftJoin('currencies as local_wc_ivst_ccy_tbl', 'local_wc_ivst_ccy_tbl.id', '=', 'apps.local_wc_ivst_ccy')
                ->where('process_list.ref_id', $decodedAppId)
                ->where('process_list.process_type_id', $process_type_id)
                ->first([
                    'process_list.id as process_list_id',
                    'process_list.desk_id',
                    'process_list.department_id',
                    'process_list.process_type_id',
                    'process_list.status_id',
                    'process_list.ref_id',
                    'process_list.tracking_no',
                    'process_list.company_id',
                    'process_list.process_desc',
                    'process_list.submitted_at',
                    'process_list.resend_deadline',
                    'ps.status_name',
                    'apps.*',
                    'app_type.name as app_type_name',

                    'process_type.form_url',

                    'divisional_office.office_name as divisional_office_name',
                    'divisional_office.office_address as divisional_office_address',

                    'sfp.contact_name as sfp_contact_name',
                    'sfp.contact_email as sfp_contact_email',
                    'sfp.contact_no as sfp_contact_phone',
                    'sfp.address as sfp_contact_address',
                    'sfp.pay_amount as sfp_pay_amount',
                    'sfp.vat_on_pay_amount as sfp_vat_on_pay_amount',
                    'sfp.transaction_charge_amount as sfp_transaction_charge_amount',
                    'sfp.vat_on_transaction_charge as sfp_vat_on_transaction_charge',
                    'sfp.total_amount as sfp_total_amount',
                    'sfp.payment_status as sfp_payment_status',
                    'sfp.pay_mode as sfp_pay_mode',
                    'sfp.pay_mode_code as sfp_pay_mode_code',

                    'gfp.contact_name as gfp_contact_name',
                    'gfp.contact_email as gfp_contact_email',
                    'gfp.contact_no as gfp_contact_phone',
                    'gfp.address as gfp_contact_address',
                    'gfp.pay_amount as gfp_pay_amount',
                    'gfp.vat_on_pay_amount as gfp_vat_on_pay_amount',
                    'gfp.transaction_charge_amount as gfp_transaction_charge_amount',
                    'gfp.vat_on_transaction_charge as gfp_vat_on_transaction_charge',
                    'gfp.total_amount as gfp_total_amount',
                    'gfp.payment_status as gfp_payment_status',
                    'gfp.pay_mode as gfp_pay_mode',
                    'gfp.pay_mode_code as gfp_pay_mode_code',

                    'company_info.company_name',
                    'company_info.company_name_bn',
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

                    'factory_district.area_nm as factory_division_name',
                    'factory_district.area_nm as factory_district_name',
                    'factory_thana.area_nm as factory_thana_name',

                    'project_status.name as project_status_name',
                    'local_land_ivst_ccy_tbl.code as local_land_ivst_ccy_code',
                    'local_building_ivst_ccy_tbl.code as local_building_ivst_ccy_code',
                    'local_machinery_ivst_ccy_tbl.code as local_machinery_ivst_ccy_code',
                    'local_others_ivst_ccy_tbl.code as local_others_ivst_ccy_code',
                    'local_wc_ivst_ccy_tbl.code as local_wc_ivst_ccy_code',
                ]);

            // Checking the Government Fee Payment(GFP) configuration for this service
            if (in_array($appInfo->status_id, [15])) {
                $payment_config = PaymentConfiguration::leftJoin('sp_payment_category', 'sp_payment_category.id', '=',
                    'sp_payment_configuration.payment_category_id')
                    ->where([
                        'sp_payment_configuration.process_type_id' => $this->process_type_id,
                        'sp_payment_configuration.payment_category_id' => 2, //Government fee payment
                        'sp_payment_configuration.status' => 1,
                        'sp_payment_configuration.is_archive' => 0
                    ])->first(['sp_payment_configuration.*', 'sp_payment_category.name']);

                if (empty($payment_config)) {
                    return response()->json([
                        'responseCode' => 1,
                        'html' => "<h4 class='custom-err-msg'>Payment Configuration not found ![BRC-10103]</h4>"
                    ]);
                }
                $relevant_info_array = [
                    'total_fee' => $appInfo->total_fee
                ];
                $unfixed_amount_array = $this->unfixedAmountsForPayment($payment_config, $relevant_info_array);
                $payment_config->amount = $unfixed_amount_array['total_unfixed_amount'] + $payment_config->amount;
                $vatFreeAllowed = BasicInformationController::isAllowedWithOutVAT($this->process_type_id);
                $payment_config->vat_on_pay_amount = ($vatFreeAllowed === true) ? 0 : $unfixed_amount_array['total_vat_on_pay_amount'];
            }

            // Company previous application list which is Reject(6), Archive(4), Shortfall(5) and Cancelled(7).
            $listOfPreviousApplications = ProcessList::leftJoin('process_type as prev_app_process_type', function ($join) use ($process_type_id) {
                    $join->where('prev_app_process_type.id', '=', $process_type_id);
                })
                ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                    $join->on('process_list.status_id', '=', 'ps.id')
                        ->where('ps.process_type_id', '=', $process_type_id);
                })
                ->where('process_list.company_id', $appInfo->company_id)
                ->where('process_list.process_type_id', $process_type_id)
                ->where('process_list.ref_id', '!=', $decodedAppId)
                ->whereIn('process_list.status_id', [6, 4, 5, 7])
                ->orderBy('process_list.submitted_at', 'DESC')
                ->select([
                    'process_list.ref_id',
                    'process_list.tracking_no',
                    'process_list.submitted_at',
                    'process_list.updated_at',
                    'ps.status_name as status_name',
                    'prev_app_process_type.type_key as prev_app_process_type_key',
                ])
                ->get();
            
            // Prepare previous applications URL and format the dates.
            if(count($listOfPreviousApplications) > 0) {
                foreach ($listOfPreviousApplications as $singlePreviousApplication) {
                
                    $singlePreviousApplication->previous_app_url = '#';
                
                    if (!empty($singlePreviousApplication->tracking_no)) {
                        $singlePreviousApplication->previous_app_url = url('process/' . $singlePreviousApplication->prev_app_process_type_key . '/view-app/' . Encryption::encodeId($singlePreviousApplication->ref_id) . '/' . Encryption::encodeId($process_type_id));
                    }
    
                    if(!empty($singlePreviousApplication->submitted_at)) {
                        $singlePreviousApplication->formatted_submitted_at = CommonFunction::formateDate($singlePreviousApplication->submitted_at);
                    }
                    if(!empty($singlePreviousApplication->updated_at)) {
                        $singlePreviousApplication->formatted_updated_at = CommonFunction::formateDate($singlePreviousApplication->updated_at);
                    }
                }
            }

            //annual production capacity
            $la_annual_production_capacity = LaAnnualProductionCapacity::leftJoin('product_unit', 'product_unit.id', '=', 'br_annual_production_capacity.quantity_unit')
                ->where('app_id', $appInfo->id)
                ->get([
                    'br_annual_production_capacity.product_name',
                    'br_annual_production_capacity.quantity',
                    'br_annual_production_capacity.price_usd',
                    'br_annual_production_capacity.price_taka',
                    'product_unit.name as unit_name',
                ]);

            $source_of_finance = SourceOfFinance::leftJoin('country_info', 'country_info.id', '=', 'br_source_of_finance.country_id')
                ->where('app_id', $decodedAppId)
                ->get([
                    'br_source_of_finance.equity_amount',
                    'br_source_of_finance.loan_amount',
                    'country_info.nicename as country_name',
                ]);

            $listOfDirectors = ListOfDirectors::leftJoin('country_info', 'country_info.id', '=', 'list_of_directors.l_director_nationality')
                ->Where('app_id', $decodedAppId)
                ->where('process_type_id', $this->process_type_id)
                ->where('status', 1)->limit(20)
                ->orderBy('created_at', 'DESC')
                ->get([
                    'list_of_directors.l_director_name',
                    'list_of_directors.l_director_designation',
                    'list_of_directors.nid_etin_passport',
                    'country_info.nationality',
                ]);

            $listOfMechineryImported = ListOfMachineryImported::Where('app_id', $decodedAppId)->where('process_type_id', $this->process_type_id)->where('status', 1)->get();
            $listOfMechineryLocal = ListOfMachineryLocal::Where('app_id', $decodedAppId)->where('process_type_id', $this->process_type_id)->where('status', 1)->get();

            //total mechinery
            $machineryImportedTotal = ListOfMachineryImported::where('app_id', $decodedAppId)->where('process_type_id', $this->process_type_id)->sum('l_machinery_imported_total_value');

            //Total machinery local value ..
            $machineryLocalTotal = ListOfMachineryLocal::where('app_id', $decodedAppId)->where('process_type_id', $this->process_type_id)->sum('l_machinery_local_total_value');

            //Business Sector
            $query = DB::select("
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
            where sec_class.code = '$appInfo->class_code' limit 1;
          ");

            $business_code = json_decode(json_encode($query), true);
            $sub_class = BusinessClass::select('id', DB::raw("CONCAT(code, ' - ', name) as name"))->where('id', $appInfo->sub_class_id)->first();

            $attachment_key_prev = "br_";
            if ($appInfo->organization_status_id == 3) {
                $attachment_key_prev .= "local";
            } else if ($appInfo->organization_status_id == 2) {
                $attachment_key_prev .= "foreign";
            } else {
                $attachment_key_prev .= "joint_venture";
            }

            $attachment_key = CommonFunction::generateAttachmentKey($appInfo->organization_status_id, $appInfo->ownership_status_id, "br");
            $document = AppDocuments::leftJoin('attachment_list', 'attachment_list.id', '=', 'app_documents.doc_info_id')
                ->leftJoin('attachment_type', 'attachment_type.id', '=', 'attachment_list.attachment_type_id')
                ->where(function ($query) use ($attachment_key, $attachment_key_prev) {
                    $query->where('attachment_type.key', $attachment_key)
                        ->orWhere('attachment_type.key', $attachment_key_prev);
                        
                })
                ->where('app_documents.ref_id', $decodedAppId)
                ->where('app_documents.process_type_id', $this->process_type_id)
                //->where('app_documents.doc_file_path', '!=', '')
                ->get([
                    'attachment_list.id',
                    'attachment_list.doc_priority',
                    'attachment_list.additional_field',
                    'app_documents.id as document_id',
                    'app_documents.doc_file_path as doc_file_path',
                    'app_documents.doc_name',
                ]);

            $public_html = strval(view("BidaRegistration::application-form-view",
                compact('mode', 'viewMode', 'appInfo', 'listOfPreviousApplications', 'la_annual_production_capacity', 'business_code', 'sub_class', 'source_of_finance', 'listOfDirectors',
                    'listOfMechineryImported', 'machineryImportedTotal', 'listOfMechineryLocal', 'machineryLocalTotal', 'document', 'payment_config', 'decodedAppId')));
            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (\Exception $e) {
            Log::error('BRViewForm : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [BRC-1015]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . "[BRC-1015]" . "</h4>"
            ]);
        }

    }

    public function appFormPdf($appId)
    {

        if (!ACL::getAccsessRight($this->aclName, '-V-')) {
            die('You have no access right! Please contact system administration for more information. [BRC-975]');
        }

        try {
            $decodedAppId = Encryption::decodeId($appId);
            $process_type_id = $this->process_type_id;
            $companyIds = CommonFunction::getUserCompanyWithZero();

            // get application,process info
            $appInfo = ProcessList::leftJoin('br_apps as apps', 'apps.id', '=', 'process_list.ref_id')
//                ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
//                ->leftJoin('visa_types as visa_type', 'visa_type.id', '=', 'visa_cat.visa_type_id') // visa type
                ->leftJoin('divisional_office', 'divisional_office.id', '=', 'process_list.approval_center_id')
                ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                    $join->on('ps.id', '=', 'process_list.status_id');
                    $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
                })
                ->leftJoin('dept_application_type as app_type', 'app_type.id', '=', 'apps.app_type_id')// app type
                ->leftJoin('sp_payment as sfp', 'sfp.id', '=', 'apps.sf_payment_id')
                ->where('process_list.ref_id', $decodedAppId)
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
//                    'user_desk.desk_name',
                    'ps.status_name',
//                    'ps.color',
                    'apps.*',
                    'app_type.name as app_type_name',
                    'divisional_office.office_name as divisional_office_name',
                    'divisional_office.office_address as divisional_office_address',

                    'sfp.contact_name as sfp_contact_name',
                    'sfp.contact_email as sfp_contact_email',
                    'sfp.contact_no as sfp_contact_phone',
                    'sfp.address as sfp_contact_address',
                    'sfp.pay_amount as sfp_pay_amount',
                    'sfp.vat_on_pay_amount as sfp_vat_on_pay_amount',
                    'sfp.transaction_charge_amount as sfp_transaction_charge_amount',
                    'sfp.vat_on_transaction_charge as sfp_vat_on_transaction_charge',
                    'sfp.total_amount as sfp_total_amount',
                    'sfp.payment_status as sfp_payment_status',
                    'sfp.pay_mode as sfp_pay_mode',
                    'sfp.pay_mode_code as sfp_pay_mode_code',
                ]);

            $currencies = Currencies::orderBy('code')->where('is_archive', 0)->where('is_active', 1)->lists('code',
                'id');
            $departmentList = Department::where('is_archive', 0)->where('status', 1)->orderBy('name')->lists('name',
                'id')->all();
            $userCompanyList = CompanyInfo::where('id', [$appInfo->company_id])->get([
                'company_name', 'company_name_bn', 'id'
            ]);
            $eaRegistrationType = ['' => 'Select one'] + EA_RegistrationType::where('is_archive', 0)->where('status',
                    1)->orderBy('name')->lists('name', 'id')->all();
            $eaOrganizationStatus = ['' => 'Select one'] + EA_OrganizationStatus::where('is_archive',
                    0)->where('status', 1)->orderBy('name')->lists('name', 'id')->all();
            $eaOrganizationType = ['' => 'Select one'] + EA_OrganizationType::where('is_archive', 0)->where('status',
                    1)->whereIn('type', [1, 3])->orderBy('name')->lists('name', 'id')->all();
            $divisions = ['' => 'Select One'] + AreaInfo::where('area_type', 1)->orderBy('area_nm',
                    'asc')->lists('area_nm', 'area_id')->all();
            $districts = ['' => 'Select One'] + AreaInfo::where('area_type', 2)->orderBy('area_nm',
                    'asc')->lists('area_nm', 'area_id')->all();
            $thana_eng = AreaInfo::orderby('area_nm')->where('area_type', 3)->lists('area_nm', 'area_id');
            $countries = ['' => 'Select one'] + Countries::where('country_status', 'Yes')->orderBy('nicename',
                    'asc')->lists('nicename', 'id')->all();
            $eaOwnershipStatus = ['' => 'Select one'] + EA_OwnershipStatus::where('is_archive', 0)->where('status',
                    1)->orderBy('name')->lists('name', 'id')->all();
            $sectors = ['' => 'Select one'] + Sector::orderBy('name')->lists('name', 'id')->all() + [0 => 'Others'];
            $sub_sectors = ['' => 'Select one'] + SubSector::orderBy('name')->lists('name', 'id')->all();
            $nationality = ['' => 'Select one'] + Countries::where('country_status', 'Yes')->where('nationality', '!=',
                    '')->orderby('nationality', 'asc')->lists('nationality', 'id')->all();
            $la_annual_production_capacity = LaAnnualProductionCapacity::where('app_id', $appInfo->id)->get();

            $projectStatusList = ProjectStatus::orderBy('name')->where('is_archive', 0)->where('status',
                1)->lists('name', 'id');

            //view document in pdf
            $document = AppDocuments::leftJoin('attachment_list', 'attachment_list.id', '=', 'app_documents.doc_info_id')
                ->where('app_documents.ref_id', $decodedAppId)
                ->where('app_documents.process_type_id', $this->process_type_id)
                ->where('app_documents.doc_file_path', '!=', '')
                ->get([
                    'attachment_list.id',
                    'attachment_list.doc_priority',
                    'attachment_list.additional_field',
                    'app_documents.id as document_id',
                    'app_documents.doc_file_path as doc_file_path',
                    'app_documents.doc_name',
                ]);

            $listOfDirector = ListOfDirectors::Where('app_id', $decodedAppId)->where('process_type_id', $this->process_type_id)->where('status', 1)->get();
            $listOfMechineryImported = ListOfMachineryImported::Where('app_id', $decodedAppId)->where('process_type_id', $this->process_type_id)->where('status', 1)->get();
            $listOfMechineryLocal = ListOfMachineryLocal::Where('app_id', $decodedAppId)->where('process_type_id', $this->process_type_id)->where('status', 1)->get();
            //total mechinery
            $machineryImportedTotal = ListOfMachineryImported::where('app_id', $decodedAppId)->where('process_type_id', $this->process_type_id)->sum('l_machinery_imported_total_value');

            //Total machinery local value ..
            $machineryLocalTotal = ListOfMachineryLocal::where('app_id', $decodedAppId)->where('process_type_id', $this->process_type_id)->sum('l_machinery_local_total_value');

            //Business Sector
            $query = DB::select("
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
            where sec_class.code = '$appInfo->class_code' limit 1;
          ");

            $business_code = json_decode(json_encode($query), true);

            $sub_class = BusinessClass::select('id', DB::raw("CONCAT(code, ' - ', name) as name"))->where('id', $appInfo->sub_class_id)->first();

            $contents = view("BidaRegistration::application-form-pdf",
                compact('appInfo', 'basicAppInfo', 'visa_type_id', 'visa_type_name', 'embassy_name',
                    'visa_on_arrival_sought', 'airports', 'sector', 'industrial_unit', 'visiting_service_type',
                    'travel_purpose', 'department', 'workPermitTypes', 'typeofIndustry', 'organizationType',
                    'visaTypes', 'paymentMethods', 'countries', 'currencies', 'thana_eng',
                    'district_eng', 'hsCodes', 'divisions', 'districts', 'userCompanyList', 'departmentList',
                    'eaRegistrationType', 'eaOrganizationStatus', 'eaOrganizationType', 'document',
                    'eaOwnershipStatus', 'sectors', 'nationality', 'sub_sectors', 'la_annual_production_capacity',
                    'projectStatusList', 'listOfDirector', 'business_code', 'sub_class',
                    'listOfMechineryImported', 'machineryImportedTotal', 'listOfMechineryLocal', 'machineryLocalTotal'
                ))->render();

            $mpdf = new mPDF([
                'utf-8', // mode - default ''
                'A4', // format - A4, for example, default ''
                12, // font size - default 0
                'dejavusans', // default font family
                10, // margin_left
                10, // margin right
                10, // margin top
                15, // margin bottom
                10, // margin header
                9, // margin footer
                'P'
            ]);

            // $mpdf->Bookmark('Start of the document');
            $mpdf->useSubstitutions;
            $mpdf->SetProtection(array('print'));
            $mpdf->SetDefaultBodyCSS('color', '#000');
            $mpdf->SetTitle("BIDA One Stop Service");
            $mpdf->SetSubject("Subject");
            $mpdf->SetAuthor("Business Automation Limited");
            $mpdf->autoScriptToLang = true;
            $mpdf->baseScript = 1;
            $mpdf->autoVietnamese = true;
            $mpdf->autoArabic = true;

            $mpdf->autoLangToFont = true;
            $mpdf->SetDisplayMode('fullwidth');
            $mpdf->SetHTMLFooter('
                    <table width="100%">
                        <tr>
                            <td width="50%"><i style="font-size: 10px;">Download time: {DATE j-M-Y h:i a}</i></td>
                            <td width="50%" align="right"><i style="font-size: 10px;">{PAGENO}/{nbpg}</i></td>
                        </tr>
                    </table>');
            $stylesheet = file_get_contents('assets/stylesheets/appviewPDF.css');
            $mpdf->setAutoTopMargin = 'stretch';
            $mpdf->setAutoBottomMargin = 'stretch';
            $mpdf->WriteHTML($stylesheet, 1);

            $mpdf->WriteHTML($contents, 2);

            $mpdf->defaultfooterfontsize = 10;
            $mpdf->defaultfooterfontstyle = 'B';
            $mpdf->defaultfooterline = 0;

            $mpdf->SetCompression(true);
            $mpdf->Output($appInfo->tracking_no . '.pdf', 'I');

        } catch (\Exception $e) {
            Log::error('BRPdfForm : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [BRC-1020]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [BRC-1020]');
            return Redirect::back()->withInput();
        }
    }

    public function preview()
    {
        return view("BidaRegistration::preview");
    }

    public function uploadDocument()
    {
        return View::make('BidaRegistration::ajaxUploadFile');
    }

    public function afterPayment($payment_id)
    {
        $payment_id = Encryption::decodeId($payment_id);
        $paymentInfo = SonaliPayment::find($payment_id);

        $processData = ProcessList::leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
            ->where('ref_id', $paymentInfo->app_id)
            ->where('process_type_id', $paymentInfo->process_type_id)
            ->first([
                'process_type.name as process_type_name',
                'process_type.process_supper_name', 'process_type.process_sub_name',
                'process_type.form_id',
                'process_list.*'
            ]);

        //get users email and phone no according to working company id
        $applicantEmailPhone = UtilFunction::geCompanyUsersEmailPhone($processData->company_id);

        $appInfo = [
            'app_id' => $processData->ref_id,
            'status_id' => $processData->status_id,
            'process_type_id' => $processData->process_type_id,
            'tracking_no' => $processData->tracking_no,
            'process_type_name' => $processData->process_type_name,
            'process_supper_name' => $processData->process_supper_name,
            'process_sub_name' => $processData->process_sub_name,
            'remarks' => ''
        ];

        try {
            DB::beginTransaction();

            if ($paymentInfo->payment_category_id == 1) {
                if ($processData->status_id != '-1') {

                    Session::flash('error', 'This is an invalid status, it\'s not possible to get the next status. [BRC-1007]');
                    return redirect('process/bida-registration/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
                }
//                $processData->status_id = 1;
//                $processData->desk_id = 1;

                $general_submission_process_data = CommonFunction::getGeneralSubmission($this->process_type_id);

                $processData->status_id = $general_submission_process_data['process_starting_status'];
                $processData->desk_id = $general_submission_process_data['process_starting_desk'];
                $processData->process_desc = 'Service Fee Payment completed successfully.';
                $processData->submitted_at = date('Y-m-d H:i:s'); // application submitted Date

                // Application status_id for email queue
                $appInfo['status_id'] = $processData->status_id;

                // application submission mail sending
                CommonFunction::sendEmailSMS('APP_SUBMIT', $appInfo, $applicantEmailPhone);
            } elseif ($paymentInfo->payment_category_id == 2) {
                if (!in_array($processData->status_id, [15, 32])) {
                    Session::flash('error', 'This is an invalid status, it\'s not possible to get the next status.');
                    return redirect('process/bida-registration/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
                }

                $general_submission_process_data = CommonFunction::getGovtPaySubmission($this->process_type_id);

                $processData->status_id = $general_submission_process_data['process_starting_status'];
                $processData->desk_id = $general_submission_process_data['process_starting_desk'];
                $processData->process_desc = 'Government Fee Payment completed successfully.';
                $processData->read_status = 0;

                $appInfo['payment_date'] = date('d-m-Y', strtotime($paymentInfo->payment_date));
                $appInfo['govt_fees'] = CommonFunction::getGovFeesInWord($paymentInfo->pay_amount);
                $appInfo['govt_fees_amount'] = $paymentInfo->pay_amount;

                // Application status_id for email queue
                $appInfo['status_id'] = $processData->status_id;

                CommonFunction::sendEmailSMS('APP_GOV_PAYMENT_SUBMIT', $appInfo, $applicantEmailPhone);
            }
            $processData->save();
            DB::commit();
            Session::flash('success', 'Your application has been successfully submitted after payment. You will receive a confirmation email soon.');

            return redirect('process/bida-registration/view-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('BRAfterPayment: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [BRC-1031]');
            Session::flash('error', 'Something went wrong!, application not updated after payment.' . CommonFunction::showErrorPublic($e->getMessage()) . ' [BRC-1031]');

            return redirect('process/bida-registration/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        }
    }

    public function afterCounterPayment($payment_id)
    {
        $payment_id = Encryption::decodeId($payment_id);
        $paymentInfo = SonaliPayment::find($payment_id);

        $processData = ProcessList::leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
            ->where('ref_id', $paymentInfo->app_id)
            ->where('process_type_id', $paymentInfo->process_type_id)
            ->first([
                'process_type.name as process_type_name',
                'process_type.process_supper_name',
                'process_type.process_sub_name',
                'process_type.form_id',
                'process_list.*'
            ]);

        //get users email and phone no according to working company id
        $applicantEmailPhone = UtilFunction::geCompanyUsersEmailPhone($processData->company_id);

        $appInfo = [
            'app_id' => $processData->ref_id,
            'status_id' => $processData->status_id,
            'process_type_id' => $processData->process_type_id,
            'tracking_no' => $processData->tracking_no,
            'process_type_name' => $processData->process_type_name,
            'process_supper_name' => $processData->process_supper_name,
            'process_sub_name' => $processData->process_sub_name,
            'remarks' => ''
        ];

        try {
            DB::beginTransaction();

            /*
             * if payment verification status is equal to 1
             * then transfer application to 'Submit' status
             */
            if ($paymentInfo->is_verified == 1 && $paymentInfo->payment_category_id == 1) {
//                $processData->status_id = 1; // Submitted
//                $processData->desk_id = 1;

                $general_submission_process_data = CommonFunction::getGeneralSubmission($this->process_type_id);

                $processData->status_id = $general_submission_process_data['process_starting_status'];
                $processData->desk_id = $general_submission_process_data['process_starting_desk'];
                $processData->process_desc = 'Counter Payment Confirm';
                $processData->submitted_at = date('Y-m-d H:i:s'); // application submitted Date

                // Application status_id for email queue
                $appInfo['status_id'] = $processData->status_id;

                // application submission mail sending
                CommonFunction::sendEmailSMS('APP_SUBMIT', $appInfo, $applicantEmailPhone);

                Session::flash('success', 'Payment Confirm successfully');
            } /*
            * Government payment submit
            * */
            elseif ($paymentInfo->is_verified == 1 && $paymentInfo->payment_category_id == 2) {

//                $processData->status_id = 16;
//                $processData->desk_id = 3;

                $general_submission_process_data = CommonFunction::getGovtPaySubmission($this->process_type_id);

                $processData->status_id = $general_submission_process_data['process_starting_status'];
                $processData->desk_id = $general_submission_process_data['process_starting_desk'];
                $processData->process_desc = 'Government Fee Payment completed successfully.';
                $processData->read_status = 0;

                $appInfo['payment_date'] = date('d-m-Y', strtotime($paymentInfo->payment_date));
                $appInfo['govt_fees'] = CommonFunction::getGovFeesInWord($paymentInfo->pay_amount);
                $appInfo['govt_fees_amount'] = $paymentInfo->pay_amount;

                // Application status_id for email queue
                $appInfo['status_id'] = $processData->status_id;

                CommonFunction::sendEmailSMS('APP_GOV_PAYMENT_SUBMIT', $appInfo, $applicantEmailPhone);
            }/*
             * if payment status is not equal 'Waiting for Payment Confirmation'
             * then transfer application to 'Waiting for Payment Confirmation' status
             */
            elseif ($paymentInfo->is_verified == 0 && $paymentInfo->payment_category_id == 1) {
                $processData->status_id = 3; // Waiting for Payment Confirmation
                $processData->desk_id = 0;
                $processData->process_desc = 'Waiting for Payment Confirmation.';

                // SMS/ Email sent to user to notify that application is Waiting for Payment Confirmation
                // TODO:: Needed to sent mail to user

                Session::flash('success', 'Application is waiting for Payment Confirmation');
            } elseif ($paymentInfo->is_verified == 0 && $paymentInfo->payment_category_id == 2) {
                $processData->status_id = 3; // Waiting for Payment Confirmation
                $processData->desk_id = 0;
                $processData->process_desc = 'Waiting for Payment Confirmation.';

                // SMS/ Email sent to user to notify that application is Waiting for Payment Confirmation
                // TODO:: Needed to sent mail to user

                Session::flash('success', 'Application is waiting for Payment Confirmation');
            }

            $paymentInfo->save();
            $processData->save();
            DB::commit();
            return redirect('process/bida-registration/view-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('BRAfterCounterPayment: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [BRC-1032]');
            Session::flash('error',
                'Something went wrong!, application not updated after payment. ' . CommonFunction::showErrorPublic($e->getMessage()) . ' [BRC-1032]');
            return redirect('process/bida-registration/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        }
    }

//    public function startingdeskstatus(){
//        $general_submission_process = CommonFunction::getGeneralSubmission($this->process_type_id);
//        print_r($general_submission_process);
//        echo "<br>";
//
//        $govt_pay_process = CommonFunction::getGovtPaySubmission($this->process_type_id);
//        print_r($govt_pay_process);
//
//
//        echo "resubmission<br>";
//        $app_id = 147;
//        $resubmission_data = CommonFunction::getReSubmissionJson($this->process_type_id, $app_id);
//        dd($resubmission_data);
//    }

    public function appHome()
    {
        if (!ACL::getAccsessRight($this->aclName, '-V-')) {
            die('You have no access right! Please contact system administration for more information. [BRC-976]');
        }
        $company_id = CommonFunction::getUserWorkingCompany();
        return view("BidaRegistration::app-index", compact('company_id'));
    }

    public function licenceList()
    {
        if (!ACL::getAccsessRight($this->aclName, '-V-')) {
            die('You have no access right! Please contact system administration for more information. [BRC-977]');
        }

        $company_id = CommonFunction::getUserWorkingCompany();
        return view("BidaRegistration::licence-list", compact('company_id'));
    }

    public function individualLicence()
    {
        if (!ACL::getAccsessRight($this->aclName, '-V-')) {
            die('You have no access right! Please contact system administration for more information. [BRC-978]');
        }

        $company_id = CommonFunction::getUserWorkingCompany();

        $sql2 = "SELECT 
                        (select  concat(count(process_list.id),'@', nc_apps.id,'@', process_list.process_type_id) from `process_list` 
                        left join `nc_apps` on `process_list`.`ref_id` = `nc_apps`.`id`                        
                        where `process_list`.`process_type_id` = 107
                        and process_list.company_id in ($company_id) 
                        and `process_list`.`status_id` not in (6) ) nc_application,
                        
                        (select concat(count(process_list.id),'@', ba_apps.id,'@', process_list.process_type_id) from `process_list` 
                        left join `ba_apps` on `process_list`.`ref_id` = `ba_apps`.`id`                        
                        where `process_list`.`process_type_id` = 103
                        and process_list.company_id in ($company_id) 
                        and `process_list`.`status_id` not in (6) ) ba_application,
                        
                        (select  concat(count(process_list.id),'@', cr_apps.id,'@', process_list.process_type_id) from `process_list` 
                        left join `cr_apps` on `process_list`.`ref_id` = `cr_apps`.`id`                        
                        where `process_list`.`process_type_id` = 104
                        and process_list.company_id in ($company_id) 
                        and `process_list`.`status_id` not in (6) ) cr_application,
                        
                        (select  concat(count(process_list.id),'@', etin_apps.id,'@', process_list.process_type_id) from `process_list` 
                        left join `etin_apps` on `process_list`.`ref_id` = `etin_apps`.`id`                        
                        where `process_list`.`process_type_id` = 106
                        and process_list.company_id in ($company_id) 
                        and `process_list`.`status_id` not in (6) ) etin_application,
                        
                        (select  concat(count(process_list.id),'@', tl_apps.id,'@', process_list.process_type_id) from `process_list` 
                        left join `tl_apps` on `process_list`.`ref_id` = `tl_apps`.`id`                        
                        where `process_list`.`process_type_id` = 105
                        and process_list.company_id in ($company_id) 
                        and `process_list`.`status_id` not in (6) ) tl_application;";

        $licenseApplications = \DB::select(DB::raw($sql2))[0];

        return view("BidaRegistration::individual-licence", compact('company_id', 'licenseApplications'));
    }

    public function getDistrictByDivision(Request $request)
    {
        $division_id = $request->get('divisionId');
        $districts = AreaInfo::where('PARE_ID', $division_id)->orderBy('AREA_NM', 'ASC')->lists('AREA_NM', 'AREA_ID');
        $data = ['responseCode' => 1, 'data' => $districts];
        return response()->json($data);
    }

    public function getHsList(Request $request)
    {
        $results = HsCodes::where('hs_code', 'LIKE', '%' . $request->get('q') . '%')->get([
            'hs_code', 'product_name', 'id'
        ]);

        $data = array();
        foreach ($results as $key => $value) {
            $data[] = array(
                'value' => $value->hs_code,
                'product' => $value->product_name,
                'id' => $value->id
            );
        }

        return json_encode($data);
    }

    public function RegNoGenerate($app_id, $approval_center_id)
    {
        $appInfo = BidaRegistration::where('id', $app_id)->first();
        $division_Office = DivisionalOffice::where('id', $approval_center_id)
            ->where('status', 1)
            ->first(['short_code']);

        if ($appInfo->reg_no == null) {
            $prefix = '';
            if ($appInfo->organization_status_id == 1) {  //1 = Joint Venture
                $prefix = 'J';
            } elseif ($appInfo->organization_status_id == 2) { //2= Foreign
                $prefix = 'F';
            } elseif ($appInfo->organization_status_id == 3) { // 3= Local
                $prefix = 'L';
            }
            $regNo = $prefix . "-" . date("Ymd") . '00' . $app_id . '-' . $division_Office->short_code;
            $appInfo->reg_no = $regNo;
            $appInfo->save();
        }

    }

//    public function getProduct(Request $request)
//    {
//
//        $product = BusinessClass::where('type', 4)->get(['name']);
//
//        $json = array();
//        foreach ($product as $row) {
//            array_push($json, $row['name']);
//        }
//
//        echo json_encode($json);
//    }

    public function directorsMachineryPDF($appId)
    {
        if (!ACL::getAccsessRight($this->aclName, '-V-')) {
            die('You have no access right! Please contact system administration for more information. [BRC-979]');
        }

        try {
            $decodedAppId = Encryption::decodeId($appId);

            //Generate PDF here ....
            UtilFunction::getListOfDirectorsAndMachinery($decodedAppId, $this->process_type_id, "I");

        } catch (\Exception $e) {
            Log::error('BRListOfDirectorMachinery : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [BRC-1115]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [BRC-1115]');
            return Redirect::back()->withInput();
        }
    }



    /**
     *director, imported machinery, local machinery list view
     */


    /**
     *director, list, multiple add, edit and delete
     */


    /**
     *imported machinery, list, multiple add, edit and delete
     */


    /**
     *local machinery, list, multiple add, edit and delete
     */


    public function showBusinessClassModal(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [BRC-1022]';
        }

        return view("BidaRegistration::business-class-modal");
    }

    public function updateBusinessClass(Request $request)
    {
        if (!ACL::getAccsessRight($this->aclName, 'A')) {
            return response()->json([
                'error' => true,
                'status' => 'You have no access right! Please contact system administration for more information. [BRC-981]',
            ]);
        }


        // Validation Rules when application submitted
        $rules = [
            'business_class_code' => 'required',
            'sub_class_id' => 'required',
            'usd_exchange_rate' => 'required',
            'country_id' => 'requiredArray',
        ];

        $messages = [
            'country_id.requiredArray' => 'Country id required',
            'business_class_code.required' => 'Business Sector (BBS Class Code) is required',
            'sub_class_id.required' => 'Sub class is required',
        ];


        $validation = Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validation->errors(),
            ]);
        }
        try {
            DB::beginTransaction();

            $app_id = Encryption::decodeId($request->get('app_id'));
            $appData = BidaRegistration::find($app_id);

            $business_class = $this->getBusinessClassSingleList($request);
            $get_business_class = json_decode($business_class->getContent(), true);

            if (empty($get_business_class['data'])) {
                DB::rollback();
                return response()->json([
                    'error' => true,
                    'status' => 'Sorry! Your given Code of business class is not valid. Please enter the right one. [BRC-1024]',
                ]);
            }

            $appData->section_id = $get_business_class['data'][0]['section_id'];
            $appData->division_id = $get_business_class['data'][0]['division_id'];
            $appData->group_id = $get_business_class['data'][0]['group_id'];
            $appData->class_id = $get_business_class['data'][0]['id'];
            $appData->class_code = $get_business_class['data'][0]['code'];

            $appData->sub_class_id = $request->get('sub_class_id');
            $appData->usd_exchange_rate = $request->get('usd_exchange_rate');
            $appData->save();

            // Country wise source of finance (Million BDT)
            if (!empty($appData->id)) {

                $source_of_finance_ids = [];

                foreach ($request->get('country_id') as $key => $value) {
                    $source_of_finance_id = $request->get('source_of_finance_id')[$key];
                    $source_of_finance = SourceOfFinance::findOrNew($source_of_finance_id);
                    $source_of_finance->app_id = $appData->id;
                    $source_of_finance->country_id = $request->get('country_id')[$key];
                    $source_of_finance->equity_amount = $request->get('equity_amount')[$key];
                    $source_of_finance->loan_amount = $request->get('loan_amount')[$key];
                    $source_of_finance->save();
                    $source_of_finance_ids[] = $source_of_finance->id;
                }

                if (count($source_of_finance_ids) > 0) {
                    SourceOfFinance::where('app_id', $appData->id)->whereNotIn('id', $source_of_finance_ids)->delete();
                }
            }

            DB::commit();

            Session::forget('update_business_class_modal');
            Session::forget('update_business_class_app_url');

            return response()->json([
                'success' => true,
                'status' => 'Success! Your information has been updated.',
                'link' => url('bida-registration/list/' . Encryption::encodeId($this->process_type_id))
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('BRUpdateBusinessClass : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [BRC-1091]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()) . ' [BRC-1091]'
            ]);
        }
    }

    public function getBusinessClassSingleList(Request $request)
    {
        $business_class_code = $request->get('business_class_code');

        $result = collect(DB::select("
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
            where sec_class.code = '$business_class_code' limit 1;
        "));

        $sub_class = BusinessClass::select('id', DB::raw("CONCAT(code, ' - ', name) as name"))
            ->where('pare_code', $business_class_code)
            ->where('type', 5)
            ->lists('name', 'id')
            ->all();
//            ->all() + [-1 => 'Other'];

        $data = [
            'responseCode' => 1,
            'data' => $result,
            'subClass' => $sub_class
        ];

        return response()->json($data);
    }

    public function getBusinessClassList()
    {
        $data = collect(DB::select("
            SELECT 
            sec_class.id, 
            sec_class.code,
            CONCAT('(',sec_section.code,') ',sec_section.name) AS section_name_code,
            CONCAT(CONCAT(sec_class.code,' - ',sec_class.name), '<p>',GROUP_CONCAT(CONCAT(subb_class.code,' - ',subb_class.name) SEPARATOR '<br />'),'</p>') class
            FROM (SELECT * FROM sector_info_bbs WHERE TYPE = 4) sec_class
            LEFT JOIN sector_info_bbs sec_group ON sec_class.pare_id = sec_group.id 
            LEFT JOIN sector_info_bbs sec_division ON sec_group.pare_id = sec_division.id 
            LEFT JOIN sector_info_bbs sec_section ON sec_division.pare_id = sec_section.id
            LEFT JOIN sector_info_bbs subb_class ON subb_class.pare_id = sec_class.id
            GROUP BY sec_class.id
            ORDER BY sec_section.code ASC;
        "));

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                if ($data) {
                    return '<a href="#" data-subclass="' . $data->code . '" class="btn btn-xs btn-primary" onclick="selectBusinessClass(this)">Select</a>';
                }
            })
            ->filterColumn('class', function ($query, $keyword) {
                $sql = "CONCAT(CONCAT(sec_class.code,' - ',sec_class.name), '<p>',GROUP_CONCAT(CONCAT(subb_class.code,' - ',subb_class.name) SEPARATOR '<br />'),'</p>') like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('section_name_code', function ($query, $keyword) {
                $sql = "CONCAT('(',sec_section.code,') ',sec_section.name) like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->removeColumn('id')
            ->make(true);
    }

    public function Payment(Request $request)
    {
        try {
            $appId = Encryption::decodeId($request->get('app_id'));

            // Get Payment Configuration
            $payment_config = PaymentConfiguration::leftJoin('sp_payment_category', 'sp_payment_category.id', '=',
                'sp_payment_configuration.payment_category_id')
                ->where([
                    'sp_payment_configuration.process_type_id' => $this->process_type_id,
                    'sp_payment_configuration.payment_category_id' => 2,  // Government fee Payment
                    'sp_payment_configuration.status' => 1,
                    'sp_payment_configuration.is_archive' => 0,
                ])->first(['sp_payment_configuration.*', 'sp_payment_category.name']);

            if (empty($payment_config)) {
                Session::flash('error', "Payment configuration not found [BRC-1456]");
                return redirect()->back()->withInput();
            }

            // Get payment distributor list
            $stakeDistribution = PaymentDistribution::where('sp_pay_config_id', $payment_config->id)
                ->where('status', 1)
                ->where('is_archive', 0)
                ->get(['id', 'stakeholder_ac_no', 'pay_amount', 'fix_status', 'purpose', 'purpose_sbl', 'distribution_type']);
            if ($stakeDistribution->isEmpty()) {
                Session::flash('error', "Stakeholder not found [BRC-101]");
                return redirect()->back()->withInput();
            }

            // Check the Govt. vat fee is allowed or not: boolean
            $vatFreeAllowed = BasicInformationController::isAllowedWithOutVAT($this->process_type_id);

            DB::beginTransaction();

            // Store payment info
            $paymentInfo = SonaliPayment::firstOrNew([
                'app_id' => $appId, 'process_type_id' => $this->process_type_id,
                'payment_config_id' => $payment_config->id
            ]);
            $paymentInfo->payment_config_id = $payment_config->id;
            $paymentInfo->app_id = $appId;
            $paymentInfo->process_type_id = $this->process_type_id;
            $paymentInfo->app_tracking_no = '';
            $paymentInfo->payment_category_id = $payment_config->payment_category_id;

            // Concat Account no of stakeholder
            $account_no = "";
            foreach ($stakeDistribution as $distribution) {
                // 4 = Vendor-Vat-Fee, 5 = Govt-Vat-Fee, 6 = Govt-Vendor-Vat-Fee
                if ($vatFreeAllowed && in_array($distribution->distribution_type, [4,5,6])) {
                    continue;
                }

                $account_no .= $distribution->stakeholder_ac_no . "-";
            }
            $account_numbers = rtrim($account_no, '-');
            // Concat Account no of stakeholder End

            $paymentInfo->receiver_ac_no = $account_numbers;

            // Application Info
            $relevant_info_array = [
                'total_fee' => BidaRegistration::where(['id' => $appId,])->value('total_fee')
            ];
            $unfixed_amount_array = $this->unfixedAmountsForPayment($payment_config, $relevant_info_array);
            $paymentInfo->pay_amount = $unfixed_amount_array['total_unfixed_amount'] + $payment_config->amount;
            $paymentInfo->vat_on_pay_amount = ($vatFreeAllowed === true) ? 0 : $unfixed_amount_array['total_vat_on_pay_amount'];
            $paymentInfo->total_amount = ($paymentInfo->pay_amount + $paymentInfo->vat_on_pay_amount);

            $paymentInfo->contact_name = $request->get('gfp_contact_name');
            $paymentInfo->contact_email = $request->get('gfp_contact_email');
            $paymentInfo->contact_no = $request->get('gfp_contact_phone');
            $paymentInfo->address = $request->get('gfp_contact_address');
            $paymentInfo->sl_no = 1; // Always 1

            $paymentInsert = $paymentInfo->save();

            BidaRegistration::where('id', $appId)->update([
                'gf_payment_id' => $paymentInfo->id
            ]);

            if ($vatFreeAllowed) {
                SonaliPaymentController::vatFreeAuditStore($paymentInfo->id, $unfixed_amount_array['total_vat_on_pay_amount']);
            }

            //Payment Details By Stakeholders
            foreach ($stakeDistribution as $distribution) {
                // 4 = Vendor-Vat-Fee, 5 = Govt-Vat-Fee, 6 = Govt-Vendor-Vat-Fee
                if ($vatFreeAllowed && in_array($distribution->distribution_type, [4,5,6])) {
                    continue;
                }

                $paymentDetails = PaymentDetails::firstOrNew([
                    'sp_payment_id' => $paymentInfo->id, 'payment_distribution_id' => $distribution->id
                ]);
                $paymentDetails->sp_payment_id = $paymentInfo->id;
                $paymentDetails->payment_distribution_id = $distribution->id;
                $paymentDetails->pay_amount = ($distribution->fix_status == 1) ? $distribution->pay_amount : $unfixed_amount_array['amounts'][$distribution->distribution_type];
                $paymentDetails->distribution_type = $distribution->distribution_type;
                $paymentDetails->receiver_ac_no = $distribution->stakeholder_ac_no;
                $paymentDetails->purpose = $distribution->purpose;
                $paymentDetails->purpose_sbl = $distribution->purpose_sbl;
                $paymentDetails->fix_status = $distribution->fix_status;
                $paymentDetails->save();
            }
            //Payment Details By Stakeholders End

            // Payment Submission
            DB::commit();
            if ($request->get('actionBtn') == 'Submit' && $paymentInsert) {
                return redirect('spg/initiate-multiple/' . Encryption::encodeId($paymentInfo->id));
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('BRPayment: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [BRC-1025]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . "[BRC-1025]");
            return redirect()->back()->withInput();
        }
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
            $vat_percentage = SonaliPaymentController::getGovtVendorVatPercentage();
            if (empty($vat_percentage)) {
                abort('Please, configure the value for VAT.');
            }

            $unfixed_amount_array[3] = $relevant_info_array['total_fee'];
            $unfixed_amount_array[5] = ($relevant_info_array['total_fee'] / 100) * $vat_percentage;

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

    public static function BRInvestmentFeeCalculation($total_investment_million)
    {
        $fee = 0;
        $convert_bd_tk = (float)$total_investment_million * 1000000;
        $fee_range = DB::table('pay_order_amount_setup')->where('process_type_id', 102)->get([
            'min_amount_bdt', 'max_amount_bdt', 'p_o_amount_bdt'
        ]);

        foreach ($fee_range as $range) {
            if (($convert_bd_tk >= $range->min_amount_bdt && $convert_bd_tk <= $range->max_amount_bdt)) {
                $fee = $range->p_o_amount_bdt;
            }

            if ($convert_bd_tk > 1000000001) {
                $fee = 100000;
            }
        }

        return $fee;
    }
    

    public static function generateAttachmentKey($organization_id, $ownership_id) {
        $organization_key = "";
        $ownership_key = "";

        switch ($organization_id) {
            case 1: // Joint Venture
                $organization_key = "join";
                break;
            case 2: // Foreign
                $organization_key = "fore";
                break;
            case 3: // Local
                $organization_key = "loca";
                break;
            default:
        }

        switch ($ownership_id) {
            case 1: // Company
                $ownership_key = "comp";
                break;
            case 2: // Partnership
                $ownership_key = "part";
                break;
            case 3: // Proprietorship
                $ownership_key = "prop";
                break;
            default:
        }

        return "br_" . $ownership_key . "_" . $organization_key;
    }

    public function getDivisionalOffice(Request $request)
    {
        try {
            $officeDivisionId = $request->input('office_division_id');
            $factoryDistrictId = $request->input('factory_district_id');

            if (!$officeDivisionId && !$factoryDistrictId) {
                return response()->json(['error' => 'Missing required parameters'], 400);
            }

            $pareId = AreaInfo::where('area_id', $factoryDistrictId)->where('area_type', 2)->value('pare_id');
            $isExistOfficeDivision = DivisionalOffice::where('area_id', $officeDivisionId)->exists();
            $isExistFactoryDivision = DivisionalOffice::where('area_id', $pareId)->exists();
            
            if(!$isExistOfficeDivision){
                $officeDivisionId = 2; // 2 = Dhaka Division
            }
            if(!$isExistFactoryDivision){
                $pareId = 2; // 2 = Dhaka Division
            }

            $divisionalOfficeData = DivisionalOffice::whereIn('area_id', [$officeDivisionId, $pareId])
                ->where('status', 1)
                ->where('is_archive', 0)
                ->orderBy('id')
                ->select('id', 'office_name', 'office_address')
                ->get();

            if (empty($divisionalOfficeData)) {
                return response()->json(['error' => 'No data found'], 404);
            }

            return response()->json(['data' => $divisionalOfficeData]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

