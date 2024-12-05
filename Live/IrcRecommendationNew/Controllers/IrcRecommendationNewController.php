<?php

namespace App\Modules\IrcRecommendationNew\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Libraries\ImageProcessing;
use App\Libraries\UtilFunction;
use App\Modules\Apps\Models\AppDocuments;
use App\Modules\Apps\Models\ManualPayment;
use App\Modules\BasicInformation\Models\EA_OrganizationStatus;
use App\Modules\BasicInformation\Models\EA_OrganizationType;
use App\Modules\BasicInformation\Models\EA_OwnershipStatus;
use App\Modules\BidaRegistration\Models\LaAnnualProductionCapacity;
use App\Modules\BidaRegistration\Models\SourceOfFinance;
use App\Modules\IrcRecommendationNew\Models\AnnualProductionCapacity;
use App\Modules\IrcRecommendationNew\Models\AnnualProductionSpareParts;
use App\Modules\IrcRecommendationNew\Models\BusinessClass;
use App\Modules\IrcRecommendationNew\Models\ExistingMachinesLc;
use App\Modules\IrcRecommendationNew\Models\ExistingMachinesLocal;
use App\Modules\IrcRecommendationNew\Models\ExistingMachinesSpare;
use App\Modules\IrcRecommendationNew\Models\InspectionAnnualProduction;
use App\Modules\IrcRecommendationNew\Models\IrcInspection;
use App\Modules\IrcRecommendationNew\Models\IrcOtherLicenceNocPermission;
use App\Modules\IrcRecommendationNew\Models\IrcProjectStatus;
use App\Modules\IrcRecommendationNew\Models\IrcPurpose;
use App\Modules\IrcRecommendationNew\Models\IrcRecommendationNew;
use App\Modules\IrcRecommendationNew\Models\IrcSourceOfFinance;
use App\Modules\IrcRecommendationNew\Models\IrcTypes;
use App\Modules\IrcRecommendationNew\Models\ListOfDirectors;
use App\Modules\IrcRecommendationNew\Models\ListOfMachineryLocal;
use App\Modules\IrcRecommendationNew\Models\ProductUnit;
use App\Modules\IrcRecommendationNew\Models\RawMaterial;
use App\Modules\ProcessPath\Models\ProcessHistory;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\Settings\Models\Attachment;
use App\Modules\Settings\Models\Bank;
use App\Modules\Settings\Models\Configuration;
use App\Modules\Settings\Models\Currencies;
use App\Modules\SonaliPayment\Models\PaymentConfiguration;
use App\Modules\SonaliPayment\Models\PaymentDetails;
use App\Modules\SonaliPayment\Models\PaymentDistribution;
use App\Modules\SonaliPayment\Models\SonaliPayment;
use App\Modules\Users\Models\AreaInfo;
use App\Modules\Users\Models\Countries;
use App\Modules\Users\Models\DivisionalOffice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use Mpdf\Mpdf;

class IrcRecommendationNewController extends Controller
{
    protected $process_type_id;
    protected $app_type_id;
    protected $aclName;

    public function __construct()
    {
        $this->process_type_id = 13;
        $this->app_type_id = 1;
        $this->aclName = 'IRCRecommendationNew';
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
            return 'Sorry! this is a request without proper way. [IRC-1001]';
        }

        $mode = '-A-';
        $viewMode = 'off';

        if (!ACL::getAccsessRight($this->aclName, $mode)) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>You have no access right! Contact with system admin for more information. [IRC-971]</h4>"
            ]);
        }

        // Check whether the applicant company is eligible and have approved basic information application
        $working_company_id = CommonFunction::getUserWorkingCompany();
        if (CommonFunction::checkEligibilityAndBiApps($working_company_id) != 1) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<div class='btn-center'><h4 class='custom-err-msg'>Sorry! You have no approved Basic Information application for BIDA services. [IRC-9991]</h4> <br/> <a href='/dashboard' class='btn btn-primary btn-sm'>Apply for Basic Information</a></div>"
            ]);
        }

        // Check whether the applicant company's department will get this service
        $department_id = CommonFunction::getDeptIdByCompanyId($working_company_id);
        if (in_array($department_id, [1, 4])) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>Sorry! The department is not allowed to apply to this application. [IRC-1041]</h4>"
            ]);
        }

        try {

            $IRCType = IrcTypes::where('id', $this->app_type_id)->where('status', 1)->where('is_archive', 0)->first([
                'id', 'type', 'attachment_key', 'app_instruction'
            ]);
            if (empty($IRCType)) {
                return response()->json([
                    'responseCode' => 1,
                    'html' => "<h4 class='custom-err-msg'>Sorry! IRC type not available right now</h4>"
                ]);
            }

            $IRCPurpose = IrcPurpose::where('status', 1)->where('is_archive', 0)->lists('purpose', 'id')->all();

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
                    'html' => "<h4 class='custom-err-msg'> Payment Configuration not found ![IRC-10101]</h4>"
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
                    'html' => "<h4 class='custom-err-msg'>Sorry! You have no approved Basic Information application. [IRC-9992]</h4>"
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
            $currencies = ['' => 'Select'] + Currencies::orderBy('code')->where('is_archive', 0)->where('is_active', 1)->lists('code',
                    'id')->all();
            $currencyBDT = ['' => 'Select one'] + Currencies::orderBy('code')->whereIn('code', ['BDT'])->where('is_archive',
                    0)->where('is_active', 1)->lists('code', 'id')->all();
            $divisions = ['' => 'Select One'] + AreaInfo::where('area_type', 1)->orderBy('area_nm',
                    'asc')->lists('area_nm', 'area_id')->all();
            $districts = ['' => 'Select One'] + AreaInfo::where('area_type', 2)->orderBy('area_nm',
                    'asc')->lists('area_nm', 'area_id')->all();
            $thana = AreaInfo::orderby('area_nm')->where('area_type', 3)->lists('area_nm', 'area_id');

            $banks = ['' => 'Select One'] + Bank::where('is_active', 1)->where('is_archive', 0)->orderBy('name',
                    'asc')->lists('name', 'id')->all();

            $projectStatusList = IrcProjectStatus::where('is_archive', 0)->where('status', 1)->lists('name', 'id');
            $nationality = ['' => 'Select one'] + Countries::where('country_status', 'Yes')->where('nationality', '!=',
                    '')->orderby('nationality', 'asc')->lists('nationality', 'id')->all();

            $usdValue = Currencies::where('code', 'USD')->first();
            $totalFee = DB::table('pay_order_amount_setup')->where('process_type_id', 102)->get([
                'min_amount_bdt', 'max_amount_bdt', 'p_o_amount_bdt'
            ]);
            $productUnit = ['' => 'Select one'] + ProductUnit::where('status', 1)->where('is_archive', 0)->orderBy('name')->lists('name', 'id')->all();
            $add_more_validation = Configuration::where('caption', 'BR_MACHINERY_EQUIPMENT_ADD_MORE')->first(['value', 'details']);

            $approvalCenterList = DivisionalOffice::where('status', 1)
                ->where('is_archive', 0)
                ->orderBy('id')
                ->get([
                    'id', 'office_name', 'office_address'
                ]);

            $public_html = strval(view("IrcRecommendationNew::application-form", compact('mode', 'IRCType', 'IRCPurpose', 'countriesWithoutBD', 'countries', 'approvalCenterList',
                'eaOwnershipStatus', 'currencies', 'divisions', 'districts', 'thana', 'banks', 'projectStatusList', 'nationality', 'department_id',
                'eaOrganizationType', 'eaOrganizationStatus', 'usdValue', 'totalFee', 'getCompanyData', 'currencyBDT', 'productUnit', 'payment_config', 'add_more_validation', 'approvalCenterList')));
            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (\Exception $e) {
            Log::error('IRCAddForm : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [IRC-1005]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . ' [IRC-1005]' . "</h4>"
            ]);
        }
    }

    public function getDocList(Request $request)
    {
        $attachment_key = $request->get('attachment_key');
        $app_id = ($request->has('app_id') ? Encryption::decodeId($request->get('app_id')) : 0);

        if ($app_id > 0) {
            //previous shortfall and draft application
            $document = AppDocuments::leftJoin('attachment_list', 'attachment_list.id', '=', 'app_documents.doc_info_id')
                ->leftJoin('attachment_type', 'attachment_type.id', '=', 'attachment_list.attachment_type_id')
                ->where('app_documents.ref_id', $app_id)
                ->where('app_documents.process_type_id', $this->process_type_id)
                ->where('attachment_type.key', $attachment_key)
                ->get([
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

        $html = strval(view("IrcRecommendationNew::documents", compact('document', 'viewMode')));
        return response()->json(['html' => $html]);
    }

    public function appStore(Request $request)
    {
        $app_id = (!empty($request->get('app_id')) ? Encryption::decodeId($request->get('app_id')) : '');
        $mode = (!empty($request->get('app_id')) ? '-E-' : '-A-');
        // Check whether the applicant company is eligible and have approved basic information application
        $working_company_id = CommonFunction::getUserWorkingCompany();

        if (!ACL::getAccsessRight($this->aclName, $mode, $app_id)) {
            abort('400', 'You have no access right! Contact with system admin for more information. [IRC-972]');
        }

        // Check application category is valid or not
        $getIrcType = IrcTypes::find($this->app_type_id);
        if (empty($getIrcType)) {
            Session::flash('error', "Unknown Irc type! [IRCN-1211]");
            return redirect()->back();
        }

        // if submitted for get BR info
        if ($request->get('actionBtn') == 'searchBRinfo') {

            // if applicant have approved BIDA Reg tracking no given then set session
            if ($request->get('last_br') == 'yes' && $request->has('ref_app_tracking_no')) {
                $refAppTrackingNo = trim($request->get('ref_app_tracking_no'));

                $getBrApprovedData = ProcessList::where('tracking_no', $refAppTrackingNo)
                    ->where('status_id', 25)
                    ->where('company_id', $working_company_id)
                    ->whereIn('process_type_id', [102,12])
                    ->first(['ref_id', 'tracking_no', 'approval_center_id', 'process_type_id']);
                if (empty($getBrApprovedData)) {
                    Session::flash('error', 'Sorry! BIDA Registration not found by this tracking number! [IRC-111]');
                    return redirect()->back();
                }

                $getBRinfo = UtilFunction::checkBRCommonPoolData($getBrApprovedData->tracking_no, $getBrApprovedData->ref_id);
                if (empty($getBRinfo)) {
                    Session::flash('error', 'Sorry! BIDA Registration not found by tracking no! [IRC-1081]');
                    return redirect()->back();
                }

                // Common session data
                Session::put('brInfo', $getBRinfo->toArray());
                Session::put('irc_purpose_id', $request->get('irc_purpose_id'));
                Session::put('agree_with_instruction', $request->get('agree_with_instruction'));
                Session::put('brInfo.last_br', $request->get('last_br'));
                Session::put('brInfo.ref_app_tracking_no', $refAppTrackingNo);
                Session::put('brInfo.approval_center_id', $getBrApprovedData->approval_center_id);

                //  Load BRA information
                if ($getBrApprovedData->process_type_id == 12 && !empty($getBRinfo->bra_tracking_no)) {
                    $this->BRAChildTableDataLoad($getBRinfo->bra_tracking_no);
                    Session::put('ref_app_approve_date',$getBRinfo->bra_approved_date);
                } else {
                    $this->BRChildTableDataLoad($getBrApprovedData->ref_id);
                    Session::put('ref_app_approve_date',$getBRinfo->br_approved_date);
                }

                Session::flash('success', 'Successfully loaded BIDA Registration data. Please proceed to next step.');
                return redirect()->back();
            }
        }

        // Clean session data
        if ($request->get('actionBtn') == 'clean_load_data') {
            // Session::forget('app_type_id');
            Session::forget('irc_purpose_id');
            Session::forget('agree_with_instruction');
            // Session::forget('attachment_key');
            Session::forget('brAnnualProductionCapacity');
            Session::forget('brSourceOfFinance');
            Session::forget('brListOfDirectors');
            Session::forget('brListOfMachinesLocal');
            Session::forget('brListOfMachinesLocalTotal');

            //newly date issue added by atik
            Session::forget('ref_app_approve_date');

            Session::forget('brInfo');

            Session::flash('success', 'Successfully cleaned data.');
            return redirect()->back();
        }

        // Check whether the applicant company's department will get this service
        $department_id = CommonFunction::getDeptIdByCompanyId($working_company_id);
        if (in_array($department_id, [1, 4])) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>Sorry! The department is not allowed to apply to this application. [IRC-1042]</h4>"
            ]);
        }

        if (CommonFunction::checkEligibilityAndBiApps($working_company_id) != 1) {
            Session::flash('error', "Sorry! You have no approved Basic Information application for BIDA services. [IRC-9993]");
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

        if (!$payment_config) {
            Session::flash('error', "Payment configuration not found [IRC-101]");
            return redirect()->back()->withInput();
        }

        // Checking the payment distributor under payment configuration
        $stakeDistribution = PaymentDistribution::where('sp_pay_config_id', $payment_config->id)
            ->where('status', 1)
            ->where('is_archive', 0)
            ->get(['id', 'stakeholder_ac_no', 'pay_amount', 'fix_status', 'purpose', 'purpose_sbl', 'distribution_type']);
        if ($stakeDistribution->isEmpty()) {
            Session::flash('error', "Stakeholder not found [IRC-100]");
            return redirect()->back()->withInput();
        }

        //  Required Documents for attachment
        $attachment_key = self::generateAttachmentKey($request->organization_status_id, $request->ownership_status_id);

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
            $rules['irc_purpose_id'] = 'required';
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
            $rules['trade_licence_issue_date'] = 'required|date|date_format:d-M-Y';
            $rules['trade_licence_validity_period'] = 'required';
            // $rules['inc_number'] = 'required_if:ownership_status_id, 3';
            $rules['inc_number'] = 'required_unless:ownership_status_id,3';
            $rules['inc_issuing_authority'] = 'required_unless:ownership_status_id,3';
            $rules['tin_number'] = 'required';
            $rules['tin_issuing_authority'] = 'required';

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
            $messages['sub_class_id.required'] = 'Code of your business subclass is required.';
            $messages['total_fixed_ivst.same'] = 'Total Financing and Total Investment (BDT) must be equal.';

            $messages['trade_licence_num.required'] = 'Trade License Number field is required.';
            $messages['trade_licence_issuing_authority.required'] = 'Trade License Issuing Authority field is required.';
            $messages['trade_licence_issue_date.required'] = 'Trade License Issue Date field is required.';
            $messages['trade_licence_issue_date.date'] = 'Trade License Issue Date must be date format.';
            $messages['trade_licence_validity_period.required'] = 'Trade License Validity Period field is required.';
            $messages['inc_number.required_unless'] = 'Incorporation Number field is required.';
            $messages['inc_issuing_authority.required_unless'] = 'Incorporation Issuing Authority field is required.';
            $messages['tin_number.required'] = 'TIN Number field is required.';
            $messages['tin_issuing_authority.required'] = 'TIN Issuing Authority field is required.';

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
        }

//        else {
//            $rules['app_type_id'] = 'required';
//            $messages['app_type_id.required'] = 'IRC Type is required';
//        }

        $rules['approval_center_id'] = 'required';
        $messages['approval_center_id.required'] = 'Please specify your desired office';
        $this->validate($request, $rules, $messages);

        if ($request->get('last_br') == 'no') {
            $trackingNo = $request->get('manually_approved_br_no');
            $exists = ProcessList::where('tracking_no', $trackingNo)
            ->whereIn('process_type_id', [102, 12]) // BR & BRA
            ->where('company_id', $working_company_id)
            ->where('status_id', 25)
            ->orderBy('id', 'desc')
            ->exists();

            if($exists) {
                Session::flash('error', "You've already taken application online using this tracking number.");
                return redirect()->back()->withInput();
            }
        }

        try {
            DB::beginTransaction();
            if ($request->get('app_id')) {
                $appData = IrcRecommendationNew::find($app_id);
                $processData = ProcessList::where([
                    'process_type_id' => $this->process_type_id, 'ref_id' => $appData->id
                ])->first();
            } else {
                $appData = new IrcRecommendationNew();
                $processData = new ProcessList();
            }

            $appData->app_type_id = $this->app_type_id;
            $appData->irc_purpose_id = $request->get('irc_purpose_id');
            if ($request->has('agree_with_instruction')) {
                $appData->agree_with_instruction = 1;
            }
            $appData->last_br = $request->get('last_br');
            if ($request->get('last_br') == 'yes') {
                $appData->ref_app_tracking_no = trim($request->get('ref_app_tracking_no'));
                $appData->ref_app_approve_date = (!empty($request->get('ref_app_approve_date')) ? date('Y-m-d', strtotime($request->get('ref_app_approve_date'))) : null);
                $appData->reg_no = $request->get('reg_no');
            } else {
                $appData->manually_approved_br_no = $request->get('manually_approved_br_no');
                $appData->manually_approved_br_date = (!empty($request->get('manually_approved_br_date')) ? date('Y-m-d', strtotime($request->get('manually_approved_br_date'))) : null);
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
                    Session::flash('error', "Sorry! Your given Code of business class is not valid. Please enter the right one. [IRC-1017]");
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
            $appData->ceo_dob = (!empty($request->get('ceo_dob')) ? date('Y-m-d',
                strtotime($request->get('ceo_dob'))) : null);

            $appData->major_activities = $request->get('major_activities');
            $appData->company_name = CommonFunction::getCompanyNameById($working_company_id);
            $appData->company_name_bn = CommonFunction::getCompanyBnNameById($working_company_id);
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
            $appData->project_status_id = $request->get('project_status_id');
            $appData->commercial_operation_date = (!empty($request->get('commercial_operation_date')) ? date('Y-m-d',
                strtotime($request->get('commercial_operation_date'))) : null);
            $appData->local_sales = $request->get('local_sales');
            $appData->foreign_sales = $request->get('foreign_sales');
            $appData->total_sales = $request->get('total_sales');

            $appData->annual_production_start_date = (!empty($request->get('annual_production_start_date')) ? date('Y-m-d',
                strtotime($request->get('annual_production_start_date'))) : null);

            $appData->local_land_ivst = (float)$request->get('local_land_ivst');
            $appData->local_land_ivst_ccy = $request->get('local_land_ivst_ccy');
            $appData->local_machinery_ivst = (float)$request->get('local_machinery_ivst');
            $appData->local_machinery_ivst_ccy = $request->get('local_machinery_ivst_ccy');
            $appData->local_building_ivst = (float)$request->get('local_building_ivst');
            $appData->local_building_ivst_ccy = $request->get('local_building_ivst_ccy');
            $appData->local_others_ivst = (float)$request->get('local_others_ivst');
            $appData->local_others_ivst_ccy = $request->get('local_others_ivst_ccy');
            $appData->local_wc_ivst = (float)$request->get('local_wc_ivst');
            $appData->local_wc_ivst_ccy = $request->get('local_wc_ivst_ccy');
            $appData->total_fixed_ivst = $request->get('total_fixed_ivst');
            $appData->total_fixed_ivst_million = $request->get('total_fixed_ivst_million');
            $appData->usd_exchange_rate = $request->get('usd_exchange_rate');
            $appData->total_fee = $request->get('total_fee');

            $appData->finance_src_loc_equity_1 = $request->get('finance_src_loc_equity_1');
            $appData->finance_src_foreign_equity_1 = $request->get('finance_src_foreign_equity_1');
            $appData->finance_src_loc_total_equity_1 = $request->get('finance_src_loc_total_equity_1');
            $appData->finance_src_loc_loan_1 = $request->get('finance_src_loc_loan_1');
            $appData->finance_src_foreign_loan_1 = $request->get('finance_src_foreign_loan_1');
            $appData->finance_src_total_loan = $request->get('finance_src_total_loan');
            $appData->finance_src_loc_total_financing_m = $request->get('finance_src_loc_total_financing_m');
            $appData->finance_src_loc_total_financing_1 = $request->get('finance_src_loc_total_financing_1');

//            $appData->imported_from_1st_adhoc = $request->get('imported_from_1st_adhoc');
            $appData->em_lc_total_taka_mil = $request->get('em_lc_total_taka_mil');
            $appData->em_local_total_taka_mil = $request->get('em_local_total_taka_mil');

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
            $appData->trade_licence_issue_date = (!empty($request->get('trade_licence_issue_date')) ? date('Y-m-d',
                strtotime($request->get('trade_licence_issue_date'))) : null);
            $appData->trade_licence_issuing_authority = $request->get('trade_licence_issuing_authority');
            $appData->trade_licence_validity_period = $request->get('trade_licence_validity_period');

            $appData->inc_number = $request->get('inc_number');
            $appData->inc_issuing_authority = $request->get('inc_issuing_authority');

            $appData->tin_number = $request->get('tin_number');
            $appData->tin_issuing_authority = $request->get('tin_issuing_authority');

            $appData->fire_license_info = $request->get('fire_license_info');
            if ($appData->fire_license_info == 'already_have') {
                $appData->fl_number = $request->get('fl_number');
                $appData->fl_expire_date = (!empty($request->get('fl_expire_date')) ? date('Y-m-d',
                    strtotime($request->get('fl_expire_date'))) : null);
            } else {
                $appData->fl_application_number = $request->get('fl_application_number');
                $appData->fl_apply_date = (!empty($request->get('fl_apply_date')) ? date('Y-m-d',
                    strtotime($request->get('fl_apply_date'))) : null);
            }
            $appData->fl_issuing_authority = $request->get('fl_issuing_authority');

            $appData->environment_clearance = $request->get('environment_clearance');
            if ($appData->environment_clearance == 'already_have') {
                $appData->el_number = $request->get('el_number');
                $appData->el_expire_date = (!empty($request->get('el_expire_date')) ? date('Y-m-d',
                    strtotime($request->get('el_expire_date'))) : null);
            } else {
                $appData->el_application_number = $request->get('el_application_number');
                $appData->el_apply_date = (!empty($request->get('el_apply_date')) ? date('Y-m-d',
                    strtotime($request->get('el_apply_date'))) : null);
            }
            $appData->el_issuing_authority = $request->get('el_issuing_authority');

            $appData->bank_account_number = $request->get('bank_account_number');
            $appData->bank_account_title = $request->get('bank_account_title');
            $appData->bank_id = $request->get('bank_id');
            $appData->branch_id = $request->get('branch_id');
            $appData->bank_address = $request->get('bank_address');
            $appData->assoc_membership_number = $request->get('assoc_membership_number');
            $appData->assoc_chamber_name = $request->get('assoc_chamber_name');
            $appData->assoc_issuing_date = (!empty($request->get('assoc_issuing_date')) ? date('Y-m-d',
                strtotime($request->get('assoc_issuing_date'))) : null);
            $appData->assoc_expire_date = (!empty($request->get('assoc_expire_date')) ? date('Y-m-d',
                strtotime($request->get('assoc_expire_date'))) : null);

            $appData->bin_vat_number = $request->get('bin_vat_number');
            $appData->bin_vat_issuing_date = (!empty($request->get('bin_vat_issuing_date')) ? date('Y-m-d',
                strtotime($request->get('bin_vat_issuing_date'))) : null);
            $appData->bin_vat_issuing_authority = $request->get('bin_vat_issuing_authority');
            $appData->bin_vat_expire_date = (!empty($request->get('bin_vat_expire_date')) ? date('Y-m-d',
                strtotime($request->get('bin_vat_expire_date'))) : null);

            $appData->g_full_name = $request->get('g_full_name');
            $appData->g_designation = $request->get('g_designation');

            //Authorized Person Information
            $appData->auth_full_name = $request->get('auth_full_name');
            $appData->auth_designation = $request->get('auth_designation');
            $appData->auth_mobile_no = $request->get('auth_mobile_no');
            $appData->auth_email = $request->get('auth_email');
            $appData->auth_image = $request->get('auth_image');

            if (!empty($request->investor_signature_base64)) {
                $yearMonth = date("Y") . "/" . date("m") . "/";
                $path = 'uploads/' . $yearMonth;
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                $splited = explode(',', substr($request->get('investor_signature_base64'), 5), 2);

                $imageData = $splited[1];

                $base64ResizeImageEncode = base64_encode(ImageProcessing::resizeBase64Image($imageData, 300, 80));

                $base64ResizeImage = base64_decode($base64ResizeImageEncode);
                $investor_signature_name = trim(sprintf("%s", uniqid('BIDA_IRC_1st_', true))) . '_' . time() . '.jpeg';

                file_put_contents($path . $investor_signature_name, $base64ResizeImage);

                $appData->g_signature = $yearMonth . $investor_signature_name;
            } else {
                $appData->g_signature = $request->get('investor_signature_hidden');
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

                    $getProcessHistory = ProcessHistory::where([
                        'process_type' => $this->process_type_id,
                        'ref_id' => $appData->id
                    ])->whereIn('status_id', [40, 41, 8, 9])->take(1)->orderBy('id', 'desc')->first(['status_id', 'user_id', 'desk_id']);

                    if (!empty($getProcessHistory) && in_array($getProcessHistory->status_id, [40, 41])) {

                        $processData->user_id = $getProcessHistory->user_id;
                    }

                } else {  // For new application submission
                    $processData->status_id = -1;
                    $processData->desk_id = 0;
                }
            }
            $appData->company_id = $working_company_id;
            $appData->save();

            // Annual production capacity- Nothing will be added from edit page
            if ($appData->irc_purpose_id != 2 && !empty($appData->id) && !empty($request->get('apc_product_name')[0])) {
                //is working only from add page
                foreach ($request->apc_product_name as $proKey => $proData) {

                    $annualCap = new AnnualProductionCapacity();
                    $annualCap->app_id = $appData->id;
                    $annualCap->product_name = $request->apc_product_name[$proKey];
                    $annualCap->quantity_unit = $request->apc_quantity_unit[$proKey];
                    $annualCap->quantity = $request->apc_quantity[$proKey];
                    $annualCap->price_usd = $request->apc_price_usd[$proKey];
                    $annualCap->price_taka = $request->apc_value_taka[$proKey];
                    $annualCap->save();
                }
            }

            //Annual production capacity spare parts
            if ($appData->irc_purpose_id != 1 && !empty($appData->id) && !empty($request->get('apsp_product_name')[0])) {
                $apsp_ids = [];
                foreach ($request->apsp_product_name as $proKey => $proData) {
                    $apsp_id = $request->get('apsp_id')[$proKey];
                    $annualSpare = AnnualProductionSpareParts::findOrNew($apsp_id);
                    $annualSpare->app_id = $appData->id;
                    $annualSpare->product_name = $request->apsp_product_name[$proKey];
                    $annualSpare->quantity_unit = $request->apsp_quantity_unit[$proKey];
                    $annualSpare->quantity = $request->apsp_quantity[$proKey];
                    $annualSpare->price_usd = $request->apsp_price_usd[$proKey];
                    $annualSpare->price_taka = $request->apsp_value_taka[$proKey];
                    $annualSpare->save();

                    $apsp_ids[] = $annualSpare->id;
                }
                if (count($apsp_ids) > 0) {
                    AnnualProductionSpareParts::where('app_id', $appData->id)->whereNotIn('id', $apsp_ids)->delete();
                }
            }

            //Existing machines Spare Parts
            if ($appData->irc_purpose_id != 1 && !empty($appData->id)) {
                $IRC_EM_spare_ids = [];
                foreach ($request->em_spare_lc_no as $key => $value) {
                    $em_spare_id = $request->get('irc_ex_machines_spare_id')[$key];

                    $existingMachineSpare = ExistingMachinesSpare::findOrNew($em_spare_id);
                    $existingMachineSpare->app_id = $appData->id;
                    $existingMachineSpare->lc_no = $request->em_spare_lc_no[$key];
                    $existingMachineSpare->lc_date = (!empty($request->get('em_spare_lc_date')[$key]) ? date('Y-m-d',
                        strtotime($request->get('em_spare_lc_date')[$key])) : null);
                    $existingMachineSpare->lc_value_currency = $request->em_spare_lc_value_currency[$key];
                    $existingMachineSpare->value_bdt = $request->em_spare_value_bdt[$key];
                    $existingMachineSpare->lc_bank_branch = $request->em_spare_lc_bank_branch[$key];
                    $existingMachineSpare->attachment = $request->attachment_path[$key];
                    $existingMachineSpare->save();
                    $IRC_EM_spare_ids[] = $existingMachineSpare->id;
                }
                if (count($IRC_EM_spare_ids) > 0) {
                    ExistingMachinesSpare::where('app_id', $appData->id)->whereNotIn('id', $IRC_EM_spare_ids)->delete();
                }
            }

            //As Per L/C Open
            if (!empty($appData->id)) {
                $em_lc_ids = [];
                foreach ($request->em_product_name as $lcKey => $proData) {
                    $em_lc_id = $request->get('em_lc_id')[$lcKey];
                    $existingMachineLc = ExistingMachinesLc::findOrNew($em_lc_id);
                    $existingMachineLc->app_id = $appData->id;
                    $existingMachineLc->product_name = $proData;
                    $existingMachineLc->quantity_unit = $request->em_quantity_unit[$lcKey];
                    $existingMachineLc->quantity = $request->em_quantity[$lcKey];
                    $existingMachineLc->unit_price = $request->em_unit_price[$lcKey];
                    $existingMachineLc->price_unit = $request->em_price_unit[$lcKey];
                    $existingMachineLc->price_foreign_currency = $request->em_price_foreign_currency[$lcKey];
                    $existingMachineLc->price_bdt = $request->em_price_bdt[$lcKey];
                    $existingMachineLc->price_taka_mil = $request->em_price_taka_mil[$lcKey];
                    $existingMachineLc->save();
                    $em_lc_ids[] = $existingMachineLc->id;
                }

                if (count($em_lc_ids) > 0) {
                    ExistingMachinesLc::where('app_id', $appData->id)->whereNotIn('id', $em_lc_ids)->delete();
                }
            }

            //As per Local Procurement/ Collection
            if (!empty($appData->id)) {
                $em_local_ids = [];
                foreach ($request->em_local_product_name as $proKey => $proData) {
                    $em_local_id = $request->get('em_local_id')[$proKey];

                    $existingMachineLocal = ExistingMachinesLocal::findOrNew($em_local_id);
                    $existingMachineLocal->app_id = $appData->id;
                    $existingMachineLocal->product_name = $proData;
                    $existingMachineLocal->quantity_unit = $request->em_local_quantity_unit[$proKey];
                    $existingMachineLocal->quantity = $request->em_local_quantity[$proKey];
                    $existingMachineLocal->unit_price = $request->em_local_unit_price[$proKey];
//                    $existingMachineLocal->price_unit = $request->em_local_price_unit[$proKey];
                    $existingMachineLocal->price_bdt = $request->em_local_price_bdt[$proKey];
                    $existingMachineLocal->price_taka_mil = $request->em_local_price_taka_mil[$proKey];
                    $existingMachineLocal->save();
                    $em_local_ids[] = $existingMachineLocal->id;
                }

                if (count($em_local_ids) > 0) {
                    ExistingMachinesLocal::where('app_id', $appData->id)->whereNotIn('id', $em_local_ids)->delete();
                }
            }

            // Country wise source of finance (Million BDT)
            if (!empty($appData->id)) {
                $source_of_finance_ids = [];
                foreach ($request->get('country_id') as $key => $value) {
                    $source_of_finance_id = $request->get('source_of_finance_id')[$key];
                    $source_of_finance = IrcSourceOfFinance::findOrNew($source_of_finance_id);
                    $source_of_finance->app_id = $appData->id;
                    $source_of_finance->country_id = $request->get('country_id')[$key];
                    $source_of_finance->equity_amount = $request->get('equity_amount')[$key];
                    $source_of_finance->loan_amount = $request->get('loan_amount')[$key];
                    $source_of_finance->save();
                    $source_of_finance_ids[] = $source_of_finance->id;
                }

                if (count($source_of_finance_ids) > 0) {
                    IrcSourceOfFinance::where('app_id', $appData->id)->whereNotIn('id', $source_of_finance_ids)->delete();
                }
            }

            //Other licences
            if (!empty($appData->id)) {
                $other_licence_ids = [];
                foreach ($request->other_licence_name as $key => $value) {
                    $source_of_finance_id = $request->get('other_licence_id')[$key];
                    $otherLicence = IrcOtherLicenceNocPermission::findOrNew($source_of_finance_id);
                    $otherLicence->app_id = $appData->id;
                    $otherLicence->licence_name = $value;
                    $otherLicence->licence_no = $request->other_licence_no[$key];
                    $otherLicence->issuing_authority = $request->other_licence_issuing_authority[$key];
                    $otherLicence->issue_date = (!empty($request->get('other_licence_issue_date')[$key]) ? date('Y-m-d',
                        strtotime($request->get('other_licence_issue_date')[$key])) : null);
                    $otherLicence->save();
                    $other_licence_ids[] = $otherLicence->id;
                }
                if (count($other_licence_ids) > 0) {
                    IrcOtherLicenceNocPermission::where('app_id', $appData->id)->whereNotIn('id', $other_licence_ids)->delete();
                }
            }

            // List of directors- No directors will be added from add page
            if (!empty($appData->id) && count(Session::get('brListOfDirectors')) > 0) {

                foreach (Session::get('brListOfDirectors') as $director) {

                    $listOfDirector = new ListOfDirectors();
                    $listOfDirector->app_id = $appData->id;
                    $listOfDirector->process_type_id = $this->process_type_id;

                    $listOfDirector->nationality_type = $director->nationality_type;
                    $listOfDirector->identity_type = $director->identity_type;
                    $listOfDirector->l_director_name = $director->l_director_name;
                    $listOfDirector->l_director_designation = $director->l_director_designation;
                    $listOfDirector->l_director_nationality = $director->l_director_nationality;
                    $listOfDirector->nid_etin_passport = $director->nid_etin_passport;
                    $listOfDirector->gender = $director->gender;
                    $listOfDirector->date_of_birth = $director->date_of_birth;
                    $listOfDirector->passport_type = $director->passport_type;
                    $listOfDirector->date_of_expiry = $director->date_of_expiry;
                    $listOfDirector->passport_scan_copy = $director->passport_scan_copy;
                    $listOfDirector->status = $director->status;
                    $listOfDirector->save();
                }

            }

            /*
            * Department and Sub-department specification for application processing
            */
            $deptSubDeptData = [
                'company_id' => $working_company_id,
                'department_id' => $department_id,
                'app_type' => $request->get('organization_status_id'),
            ];
            $deptAndSubDept = CommonFunction::DeptSubDeptSpecification($this->process_type_id, $deptSubDeptData);
            $processData->department_id = $deptAndSubDept['department_id'];
            $processData->sub_department_id = $deptAndSubDept['sub_department_id'];
            $processData->ref_id = $appData->id;
            $processData->process_type_id = $this->process_type_id;
            $processData->process_desc = '';// for re-submit application
            $processData->company_id = $working_company_id;
            $processData->read_status = 0;
            $processData->approval_center_id = $request->get('approval_center_id');

            $jsonData['Applied by'] = CommonFunction::getUserFullName();
            $jsonData['Email'] = Auth::user()->user_email;
            $jsonData['Mobile'] = Auth::user()->user_phone;
            $processData['json_object'] = json_encode($jsonData);
            $processData->save();

            //attachment store
            if (count($doc_row) > 0) {
                $doc_ids = [];
                foreach ($doc_row as $docs) {
                    $app_doc = AppDocuments::firstOrNew([
                        'process_type_id' => $this->process_type_id,
                        'ref_id' => $appData->id,
                        'doc_info_id' => $docs->id
                    ]);
                    $app_doc->doc_name = $docs->doc_name;
                    $app_doc->doc_file_path = $request->get('validate_field_' . $docs->id);
                    $app_doc->save();
                    $doc_ids[] = $app_doc->id;
                }
                if (count($doc_ids) > 0) {
                    AppDocuments::where('ref_id', $appData->id)
                        ->where('process_type_id', $this->process_type_id)
                        ->whereNotIn('id', $doc_ids)
                        ->delete();
                }
            }

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

                // Concat Account no of stakeholder
                $account_no = "";
                foreach ($stakeDistribution as $distribution) {
                    $account_no .= $distribution->stakeholder_ac_no . "-";
                }
                $account_numbers = rtrim($account_no, '-');
                // Concat Account no of stakeholder End

                $paymentInfo->receiver_ac_no = $account_numbers;
                $unfixed_amount_array = $this->unfixedAmountsForPayment($payment_config);

                $paymentInfo->pay_amount = $unfixed_amount_array['total_unfixed_amount'] + $payment_config->amount;
                $paymentInfo->vat_on_pay_amount = $unfixed_amount_array['total_vat_on_pay_amount'];
                $paymentInfo->total_amount = ($paymentInfo->pay_amount + $paymentInfo->vat_on_pay_amount);
                $paymentInfo->contact_name = CommonFunction::getUserFullName();
                $paymentInfo->contact_email = Auth::user()->user_email;
                $paymentInfo->contact_no = Auth::user()->user_phone;
                $paymentInfo->address = Auth::user()->road_no;
                $paymentInfo->sl_no = 1; // Always 1
                $paymentInfo->save();

                $appData->sf_payment_id = $paymentInfo->id;
                $appData->save();

                //Payment Details By Stakeholders
                foreach ($stakeDistribution as $distribution) {
                    $paymentDetails = PaymentDetails::firstOrNew([
                        'sp_payment_id' => $paymentInfo->id, 'payment_distribution_id' => $distribution->id
                    ]);
                    $paymentDetails->sp_payment_id = $paymentInfo->id;
                    $paymentDetails->payment_distribution_id = $distribution->id;
                    if ($distribution->fix_status == 1) {
                        $paymentDetails->pay_amount = $distribution->pay_amount;
                    } else {
                        $paymentDetails->pay_amount = $unfixed_amount_array['amounts'][$distribution->distribution_type];
                    }
                    $paymentDetails->receiver_ac_no = $distribution->stakeholder_ac_no;
                    $paymentDetails->purpose = $distribution->purpose;
                    $paymentDetails->purpose_sbl = $distribution->purpose_sbl;
                    $paymentDetails->fix_status = $distribution->fix_status;
                    $paymentDetails->distribution_type = $distribution->distribution_type;
                    $paymentDetails->save();
                }
                //Payment Details By Stakeholders End
            }

//            if ($processData->status_id == 0) {
//                dd('Application status not found!');
//            }

//            Session::forget('app_type_id');
            Session::forget('irc_purpose_id');
            Session::forget('agree_with_instruction');
            Session::forget('brAnnualProductionCapacity');
            Session::forget('brSourceOfFinance');
            Session::forget('brListOfDirectors');
            Session::forget('brListOfMachinesLocal');
            Session::forget('brListOfMachinesLocalTotal');
            Session::forget('brInfo');
//            Session::forget('attachment_key');

            /*
            * if action is submitted and application status is equal to draft
            * and have payment configuration then, generate a tracking number
            * and go to payment initiator function.
            */
            if ($request->get('actionBtn') == 'Submit' && $processData->status_id == -1) {
                
                $applicationInProcessing = CommonFunction::applicationInProcessing($this->process_type_id);
                if ($applicationInProcessing) {
                    Session::flash('error', "Sorry! You already have pending application in processing. [IRC-1105]");
                    return redirect()->back();
                }

                if (empty($processData->tracking_no)) {
                    $prefix = 'IRC-' . date("dMY") . '-';
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
                $applicantEmailPhone = UtilFunction::geCompanyUsersEmailPhone($working_company_id);

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
                Session::flash('error', 'Failed due to Application Status Conflict. Please try again later! [IRC-1023]');
            }

            DB::commit();
            return redirect('irc-recommendation-new/list/' . Encryption::encodeId($this->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('IRCAppStore : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [IRC-1060]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [IRC-1060]');
            return redirect()->back()->withInput();
        }
    }

    public function applicationViewEdit($applicationId, $openMode = "", Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [IRC-1002]';
        }

        $mode = '-E-';
        $viewMode = 'off';

        // it's enough to check ACL for view mode only
        if (!ACL::getAccsessRight($this->aclName, $mode)) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>You have no access right! Contact with system admin for more information. [IRC-973]</h4>"
            ]);
        }

        $working_company_id = CommonFunction::getUserWorkingCompany();
        // Check whether the applicant company's department will get this service
        $department_id = CommonFunction::getDeptIdByCompanyId($working_company_id);
        if (in_array($department_id, [1, 4])) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>Sorry! The department is not allowed to apply to this application. [IRC-1043]</h4>"
            ]);
        }

        try {

            $applicationId = Encryption::decodeId($applicationId);
            $process_type_id = $this->process_type_id;
            $appInfo = ProcessList::leftJoin('irc_apps as apps', 'apps.id', '=', 'process_list.ref_id')
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

            $IRCType = IrcTypes::where('id', $appInfo->app_type_id)->where('status', 1)->where('is_archive', 0)->first([
                'id', 'type', 'attachment_key', 'app_instruction'
            ]);

            $IRCPurpose = IrcPurpose::where('status', 1)->where('is_archive', 0)->lists('purpose', 'id')->all();

            $annualProductionCapacity = AnnualProductionCapacity::leftJoin('product_unit', 'product_unit.id', '=', 'irc_annual_production_capacity.quantity_unit')
                ->where('app_id', $applicationId)->limit(20)
                ->get([
                    'irc_annual_production_capacity.*',
                    'product_unit.name as unit_name'
                ]);

            $annualProductionSpareParts = AnnualProductionSpareParts::where('app_id', $applicationId)->get();
            $eaOwnershipStatus = ['' => 'Select one'] + EA_OwnershipStatus::where('is_archive', 0)->where('status',
                    1)->orderBy('name')->lists('name', 'id')->all();
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

            $source_of_finance = IrcSourceOfFinance::where('app_id', $applicationId)->get();
            $existing_machines_lc = ExistingMachinesLc::where('app_id', $applicationId)->get();
            $existing_machines_local = ExistingMachinesLocal::where('app_id', $applicationId)->get();
            $existing_machines_spare = ExistingMachinesSpare::where('app_id', $applicationId)->get();
            $currencies = ['' => 'Select'] + Currencies::orderBy('code')->where('is_archive', 0)->where('is_active', 1)->lists('code', 'id')->all();
            $currencyBDT = ['' => 'Select one'] + Currencies::orderBy('code')->whereIn('code', ['BDT'])->where('is_archive',
                    0)->where('is_active', 1)->lists('code', 'id')->all();
            $listOfDirectors = ListOfDirectors::where('app_id', $applicationId)->where('process_type_id', $this->process_type_id)->get();

            $banks = ['' => 'Select One'] + Bank::where('is_active', 1)->where('is_archive', 0)->orderBy('name',
                    'asc')->lists('name', 'id')->all();
            $usdValue = Currencies::where('code', 'USD')->first();
            $projectStatusList = IrcProjectStatus::where('is_archive', 0)->where('status', 1)->lists('name', 'id');
            $totalFee = DB::table('pay_order_amount_setup')->where('process_type_id', 102)->get([
                'min_amount_bdt', 'max_amount_bdt', 'p_o_amount_bdt'
            ]);
            $productUnit = ['' => 'Select one'] + ProductUnit::where('status', 1)->where('is_archive', 0)->orderBy('name')->lists('name', 'id')->all();
            $otherLicence = IrcOtherLicenceNocPermission::where('app_id', $applicationId)->get();

            $approvalCenterList = DivisionalOffice::where('status', 1)
                ->where('is_archive', 0)
                ->orderBy('id')
                ->get([
                    'id', 'office_name', 'office_address'
                ]);

            $public_html = strval(view("IrcRecommendationNew::application-form-edit",
                compact('appInfo', 'IRCType', 'IRCPurpose', 'department_id', 'countries', 'approvalCenterList', 'countriesWithoutBD', 'viewMode', 'projectStatusList',
                    'mode', 'eaOwnershipStatus', 'listOfDirectors', 'otherLicence', 'banks', 'nationality', 'eaOrganizationType', 'totalFee',
                    'eaOrganizationStatus', 'eaRegistrationType', 'divisions', 'districts',
                    'currencies', 'currencyBDT', 'annualProductionCapacity', 'annualProductionSpareParts', 'usdValue', 'productUnit', 'source_of_finance', 'existing_machines_lc', 'existing_machines_local', 'existing_machines_spare', 'approvalCenterList')));

            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (\Exception $e) {
            Log::error('IRCViewEditForm : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [IRC-1010]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . "[IRC-1010]" . "</h4>"
            ]);
        }
    }

    public function applicationView($app_id, Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [IRC-1003]';
        }

        // it's enough to check ACL for view mode only
        if (!ACL::getAccsessRight($this->aclName, '-V-')) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>You have no access right! Contact with system admin for more information. [BRC-973]</h4>"
            ]);
        }

        try {
            $applicationId = Encryption::decodeId($app_id);
            $process_type_id = $this->process_type_id;
            $viewMode = 'on';
            $appInfo = ProcessList::leftJoin('irc_apps as apps', 'apps.id', '=', 'process_list.ref_id')
                ->leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
                ->leftJoin('divisional_office', 'divisional_office.id', '=', 'process_list.approval_center_id')
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
                ->leftJoin('manual_payment as mgfp', 'mgfp.id', '=', 'apps.gf_manual_payment_id')
                ->leftJoin('irc_types', 'irc_types.id', '=', 'apps.app_type_id')
                ->leftJoin('irc_purpose', 'irc_purpose.id', '=', 'apps.irc_purpose_id')
                ->leftJoin('bank', 'bank.id', '=', 'apps.bank_id')
                ->leftJoin('bank_branches', 'bank_branches.id', '=', 'apps.branch_id')
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

                    'mgfp.contact_name as mgfp_contact_name',
                    'mgfp.contact_email as mgfp_contact_email',
                    'mgfp.contact_no as mgfp_contact_phone',
                    'mgfp.address as mgfp_contact_address',
                    'mgfp.pay_amount as mgfp_pay_amount',
                    'mgfp.vat_amount as mgfp_vat_amount',
                    'mgfp.transaction_charge_amount as mgfp_bank_charge',
                    'mgfp.total_amount as mgfp_total_amount',
                    'mgfp.payment_status as mgfp_payment_status',
                    'mgfp.ref_tran_no as mgf_ref_tran_no',
                    'mgfp.invoice_copy as mgf_invoice_copy',

                    'irc_types.type as irc_type_name',
                    'irc_types.attachment_key',
                    'irc_purpose.id as purpose_id',
                    'irc_purpose.purpose as purpose_name',
                    'bank.name as bank_name',
                    'bank_branches.branch_name',
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

                    'divisional_office.office_name as divisional_office_name',
                    'divisional_office.office_address as divisional_office_address',

                    // Reference application
                    'ref_process.ref_id as ref_application_ref_id',
                    'ref_process.process_type_id as ref_application_process_type_id',
                    'ref_process_type.type_key as ref_process_type_key',
                ]);

            // Checking the Manual Government Fee Payment(GFP) for this service
            if (in_array($appInfo->status_id, [15])) {
                $manualGovtPaymentConfig = [
                    'pay_amount' => $appInfo->inspection_gov_fee
                ];
            }

            $source_of_finance = IrcSourceOfFinance::leftJoin('country_info', 'country_info.id', '=', 'irc_source_of_finance.country_id')
                ->where('app_id', $applicationId)
                ->get([
                    'irc_source_of_finance.equity_amount',
                    'irc_source_of_finance.loan_amount',
                    'country_info.nicename as country_name',
                ]);

            $annualProductionCapacity = AnnualProductionCapacity::leftJoin('product_unit', 'product_unit.id', '=', 'irc_annual_production_capacity.quantity_unit')
                ->where('irc_annual_production_capacity.app_id', $applicationId)
                ->get([
                    'irc_annual_production_capacity.id as apc_id',
                    'irc_annual_production_capacity.product_name',
                    'product_unit.name',
                    'irc_annual_production_capacity.quantity',
                    'irc_annual_production_capacity.price_usd',
                    'irc_annual_production_capacity.price_taka',
                ]);

            $annualProductionSpareParts = AnnualProductionSpareParts::leftJoin('product_unit', 'product_unit.id', '=', 'irc_annual_production_spare_parts.quantity_unit')
                ->where('irc_annual_production_spare_parts.app_id', $applicationId)
                ->get([
                    'irc_annual_production_spare_parts.product_name',
                    'product_unit.name',
                    'irc_annual_production_spare_parts.quantity',
                    'irc_annual_production_spare_parts.price_usd',
                    'irc_annual_production_spare_parts.price_taka',
                ]);

            $existing_machines_spare = ExistingMachinesSpare::leftJoin('currencies', 'currencies.id', '=', 'irc_existing_machines_spare.lc_value_currency')
                ->where('irc_existing_machines_spare.app_id', $applicationId)
                ->get([
                    'irc_existing_machines_spare.lc_no',
                    'irc_existing_machines_spare.lc_date',
                    'currencies.code',
                    'irc_existing_machines_spare.value_bdt',
                    'irc_existing_machines_spare.lc_bank_branch',
                    'irc_existing_machines_spare.attachment',
                ]);

            $total_existing_machines_spare = ExistingMachinesSpare::where('app_id', $applicationId)->sum('value_bdt');

            $existing_machines_lc = ExistingMachinesLc::leftJoin('product_unit', 'product_unit.id', '=', 'irc_existing_machines_lc.quantity_unit')
                ->leftJoin('currencies', 'currencies.id', '=', 'irc_existing_machines_lc.price_unit')
                ->where('irc_existing_machines_lc.app_id', $applicationId)
                ->get([
                    'irc_existing_machines_lc.product_name',
                    'product_unit.name',
                    'irc_existing_machines_lc.quantity',
                    'irc_existing_machines_lc.unit_price',
                    'currencies.code',
                    'irc_existing_machines_lc.price_foreign_currency',
                    'irc_existing_machines_lc.price_bdt',
                    'irc_existing_machines_lc.price_taka_mil',
                ]);

            $total_existing_machines_lc_bdt = ExistingMachinesLc::where('app_id', $applicationId)->sum('price_bdt');
            $existing_machines_local = ExistingMachinesLocal::leftJoin('product_unit', 'product_unit.id', '=', 'irc_existing_machines_local.quantity_unit')
                ->where('irc_existing_machines_local.app_id', $applicationId)
                ->get([
                    'irc_existing_machines_local.product_name',
                    'product_unit.name',
                    'irc_existing_machines_local.quantity',
                    'irc_existing_machines_local.unit_price',
                    'irc_existing_machines_local.price_bdt',
                    'irc_existing_machines_local.price_taka_mil',
                ]);

            $listOfDirectors = ListOfDirectors::leftJoin('country_info', 'country_info.id', '=', 'list_of_directors.l_director_nationality')
                ->where('app_id', $applicationId)
                ->where('process_type_id', $this->process_type_id)
                ->get([
                    'list_of_directors.l_director_name',
                    'list_of_directors.l_director_designation',
                    'country_info.nationality',
                    'list_of_directors.nid_etin_passport',
                ]);

            $otherLicence = IrcOtherLicenceNocPermission::where('app_id', $applicationId)->get();

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

            //$attachment_key = "ircrn" . $appInfo->attachment_key . "i";

            $document = AppDocuments::leftJoin('attachment_list', 'attachment_list.id', '=', 'app_documents.doc_info_id')
                ->leftJoin('attachment_type', 'attachment_type.id', '=', 'attachment_list.attachment_type_id')
                //->where('attachment_type.key', $attachment_key)
                ->where('app_documents.ref_id', $applicationId)
                ->where('app_documents.process_type_id', $this->process_type_id)
                //->where('app_documents.doc_file_path', '!=', '')
                ->get([
                    //'attachment_list.id',
                    'attachment_list.doc_priority',
                    //'attachment_list.additional_field',
                    //'app_documents.id as document_id',
                    'app_documents.doc_file_path as doc_file_path',
                    'app_documents.doc_name',
                ]);

            $inspectionInfo = IrcInspection::where('app_id', $appInfo->id)->orderBy('id', 'desc')
                ->get([
                    'io_name',
                    'id',
                    'inspection_report_date',
                    'ins_approved_status',
                    'created_at',
                    'created_by'
                ]);

            $last_inspection_id = IrcInspection::where('app_id', $appInfo->id)->where('ins_approved_status', 1)->value('id');

            $data['ref_app_url'] = '#';
            if (!empty($appInfo->ref_app_tracking_no)) {
                $data['ref_app_url'] = url('process/'.$appInfo->ref_process_type_key.'/view-app/'.Encryption::encodeId($appInfo->ref_application_ref_id) . '/' . Encryption::encodeId($appInfo->ref_application_process_type_id));
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
                ->where('process_list.ref_id', '!=', $applicationId)
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

            $public_html = strval(view("IrcRecommendationNew::application-form-view",
                compact('appInfo', 'inspectionInfo', 'last_inspection_id', 'viewMode', 'listOfDirectors', 'otherLicence',
                    'sub_class', 'document', 'annualProductionCapacity', 'annualProductionSpareParts', 'business_code',
                    'source_of_finance', 'existing_machines_lc', 'existing_machines_local', 'existing_machines_spare',
                    'total_existing_machines_spare', 'total_existing_machines_lc_bdt', 'manualGovtPaymentConfig', 'data', 'listOfPreviousApplications')));

            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (\Exception $e) {
            Log::error('IRCView : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [IRC-10111]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . "[IRC-10111]" . "</h4>"
            ]);
        }
    }


    public function preview()
    {
        return view("IrcRecommendationNew::preview");
    }

    public function uploadDocument()
    {
        return View::make('IrcRecommendationNew::ajaxUploadFile');
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
                ->where('type', 5)->lists('name', 'id')->all()+ [-1 => 'Other'];

        $data = ['responseCode' => 1, 'data' => $result, 'subClass' => $sub_class];
        return response()->json($data);
    }

    public function showBusinessClassModal(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [IRC-1022]';
        }

        return view("IrcRecommendationNew::business-class-modal");
    }

    public function getDistrictByDivision(Request $request)
    {
        $division_id = $request->get('divisionId');
        $districts = AreaInfo::where('PARE_ID', $division_id)->orderBy('AREA_NM', 'ASC')->lists('AREA_NM', 'AREA_ID');
        $data = ['responseCode' => 1, 'data' => $districts];
        return response()->json($data);
    }

    public function manualPayment(Request $request)
    {
        $rules['contact_name'] = 'required';
        $rules['contact_email'] = 'required';
        $rules['contact_no'] = 'required';
        $rules['address'] = 'required';
        $rules['ref_tran_no'] = 'required';
        $rules['pay_amount'] = 'required';
        $rules['vat_amount'] = 'required';
        $rules['transaction_charge_amount'] = 'required';
        $rules['total_amount'] = 'required';
        $rules['invoice_copy'] = 'required';

        $messages['invoice_copy'] = 'Attachment file is required';

        $this->validate($request, $rules, $messages);

        try {

            DB::beginTransaction();

            $appId = Encryption::decodeId($request->get('app_id'));
            $working_company_id = CommonFunction::getUserWorkingCompany();

            $processData = ProcessList::leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
                ->where('ref_id', $appId)
                ->where('process_type_id', $this->process_type_id)
                ->first([
                    'process_type.name as process_type_name',
                    'process_type.process_supper_name',
                    'process_type.process_sub_name',
                    'process_type.form_id',
                    'process_list.*'
                ]);

            if (empty($processData)) {
                return response()->json([
                    'responseCode' => 1,
                    'html' => "<h4 class='custom-err-msg'>Sorry! No information found try again or contact with support help desk.[IRC-10103]</h4>"
                ]);
            }

            // get users email and phone no according to working company id
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

            // Store Govt. payment info
            // 5 = Manual Government Fee Payment
            $paymentInfo = ManualPayment::firstOrNew([
                'app_id' => $appId,
                'process_type_id' => $this->process_type_id,
                'payment_category_id' => 5
            ]);

            $paymentInfo->app_id = $appId;
            $paymentInfo->process_type_id = $this->process_type_id;
            $paymentInfo->app_tracking_no = $processData->tracking_no;
            $paymentInfo->payment_category_id = 5; // Manual Government Fee Payment
            $paymentInfo->pay_amount = $request->get('pay_amount');
            $paymentInfo->transaction_charge_amount = $request->get('transaction_charge_amount');
            $paymentInfo->vat_amount = $request->get('vat_amount');
            $paymentInfo->total_amount = $request->get('total_amount');
            $paymentInfo->contact_name = $request->get('contact_name');
            $paymentInfo->contact_email = $request->get('contact_email');
            $paymentInfo->contact_no = $request->get('contact_no');
            $paymentInfo->address = $request->get('address');
            $paymentInfo->payment_status = 1; // Successful
            $paymentInfo->ref_tran_no = $request->get('ref_tran_no');
            $paymentInfo->payment_date = date('Y-m-d');

            if ($request->hasFile('invoice_copy')) {
                $yearMonth = date("Y") . "/" . date("m") . "/";
                $path = 'uploads/' . $yearMonth;
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $_file_path = $request->file('invoice_copy');
                $file_path = trim(uniqid('IRC_Govt_Invoice-' . $appId . '-', true) . $_file_path->getClientOriginalName());
                $_file_path->move($path, $file_path);
                $invoice_copy_file = $yearMonth . $file_path;
                $paymentInfo->invoice_copy = $invoice_copy_file;
            }

            $paymentInfo->save();

            IrcRecommendationNew::where('id', $appId)->update(['gf_manual_payment_id' => $paymentInfo->id]);

            $general_submission_process_data = CommonFunction::getGovtPaySubmission($this->process_type_id);
            $processData->status_id = $general_submission_process_data['process_starting_status'];
            $processData->desk_id = $general_submission_process_data['process_starting_desk'];
            $processData->read_status = 0;
            $processData->process_desc = 'Government Fee Payment completed successfully.';

            $appInfo['payment_date'] = date('d-m-Y');
            $appInfo['govt_fees'] = CommonFunction::getGovFeesInWord($paymentInfo->pay_amount);
            $appInfo['govt_fees_amount'] = $paymentInfo->pay_amount;
            $appInfo['status_id'] = $processData->status_id;

            $processData->save();
            DB::commit();

            CommonFunction::sendEmailSMS('APP_GOV_PAYMENT_SUBMIT', $appInfo, $applicantEmailPhone);

            Session::flash('success', 'Your application has been successfully submitted after payment. You will receive a confirmation email soon.');

            return redirect('process/irc-recommendation-new/view-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('IrcGovtManualPayment: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [IRC-1025]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . "[IRC-1025]");
            return redirect()->back()->withInput();
        }
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
            if ($paymentInfo->payment_category_id == 1) {
                if ($processData->status_id != '-1') {
                    Session::flash('error', 'This is an invalid status, it\'s not possible to get the next status. [IRC-911]');
                    return redirect('process/irc-recommendation-new/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
                }

                $general_submission_process_data = CommonFunction::getGeneralSubmission($this->process_type_id);

                $processData->status_id = $general_submission_process_data['process_starting_status'];
                $processData->desk_id = $general_submission_process_data['process_starting_desk'];

                $processData->process_desc = 'Service Fee Payment completed successfully.';
                $processData->submitted_at = date('Y-m-d H:i:s'); // application submitted Date

                // Application submit status_id for email queue
                $appInfo['status_id'] = $processData->status_id;

                // application submission mail sending
                CommonFunction::sendEmailSMS('APP_SUBMIT', $appInfo, $applicantEmailPhone);
            }

            $processData->save();

            DB::commit();
            Session::flash('success', 'Your application has been successfully submitted after payment. You will receive a confirmation email soon.');

            return redirect('process/irc-recommendation-new/view-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('IRCAfterPayment: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [IRC-102]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [IRC-102]');
            return redirect('process/irc-recommendation-new/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
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

        // get users email and phone no according to working company id
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

        DB::beginTransaction();

        try {

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
                $paymentInfo->payment_status = 1;
                $paymentInfo->save();

                // Application status_id for email queue
                $appInfo['status_id'] = $processData->status_id;

                // application submission mail sending
                CommonFunction::sendEmailSMS('APP_SUBMIT', $appInfo, $applicantEmailPhone);

                Session::flash('success', 'Payment Confirm successfully');
            } /*
             * if payment status is not equal 'Waiting for Payment Confirmation'
             * then transfer application to 'Waiting for Payment Confirmation' status
             */
            else {
                $processData->status_id = 3; // Waiting for Payment Confirmation
                $processData->desk_id = 0;
                $processData->process_desc = 'Waiting for Payment Confirmation.';

                // SMS/ Email sent to user to notify that application is Waiting for Payment Confirmation
                // TODO:: Needed to sent mail to user

                Session::flash('success', 'Application is waiting for Payment Confirmation');
            }

            $processData->save();
            DB::commit();

            return redirect('process/irc-recommendation-new/view-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('IRCAfterCounterPayment: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [IRC-103]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [IRC-103]');
            return redirect('process/irc-recommendation-new/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        }
    }

    public function getRawMaterial($id)
    {
        $id = Encryption::decodeId($id);
        DB::statement(DB::raw('set @rownum=0'));
        $raw_material = RawMaterial::where('apc_product_id', $id)
            ->get([DB::raw('@rownum := @rownum+1 AS sl'), 'irc_raw_material.*']);

        $productUnit = [0 => ''] + ProductUnit::where('status', 1)->where('is_archive', 0)->orderBy('name')->lists('name', 'id')->all();

        //total raw material price
        $total_value = AnnualProductionCapacity::where('id', $id)->first(['unit_of_product', 'raw_material_total_price']);

        return \view('IrcRecommendationNew::raw_material.raw-material-modal', compact('raw_material', 'total_value', 'productUnit'));
    }

    //Inspection start
    public function inspectionForm($inspectionId)
    {
        // it's enough to check ACL for view mode only
        if (!ACL::getAccsessRight($this->aclName, '-V-')) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>You have no access right! Contact with system admin for more information. [IRC-973]</h4>"
            ]);
        }

        try {

            $inspection_id = Encryption::decodeId($inspectionId);
            $process_type_id = $this->process_type_id;
            $userDeskIds = CommonFunction::getUserDeskIds();

            $inspectionInfo = ProcessList::leftJoin('irc_inspection as apps', 'apps.app_id', '=', 'process_list.ref_id')
                ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
                ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                    $join->on('ps.id', '=', 'process_list.status_id');
                    $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
                })
                ->leftJoin('ea_organization_status', 'ea_organization_status.id', '=', 'apps.organization_status_id')
                ->leftJoin('bank', 'bank.id', '=', 'apps.bank_id')
                ->leftJoin('bank_branches', 'bank_branches.bank_id', '=', 'apps.bank_id')
                ->leftJoin('irc_project_status', 'irc_project_status.id', '=', 'apps.project_status_id')
                ->where('process_list.process_type_id', $process_type_id)
                ->where('apps.id', $inspection_id)
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
                    'user_desk.desk_name',
                    'ps.status_name',
                    'ps.color',
                    'ea_organization_status.name as organization_status_name',
                    'bank.name as bank_name',
                    'bank_branches.branch_name as branch_name',
                    'apps.*',
                    'irc_project_status.name as project_status_name',
                ]);

            // System admin, IT Help Desk, Deputy Director, Director, Managing Director & submited inspection officer are able to see details
            if (!($inspectionInfo->created_by == Auth::user()->id || in_array(Auth::user()->user_type, ['1x101', '2x202']) || (Auth::user()->user_type == '4x404' && (in_array(2, $userDeskIds) || in_array(3, $userDeskIds) || in_array(4, $userDeskIds))))) {
                return response()->json([
                    'responseCode' => 1,
                    'html' => "<h4 class='custom-err-msg'>You have no access right! Contact with system admin for more information. [IRC-974]</h4>"
                ]);
            }

            $annualProductionCapacity = AnnualProductionCapacity::leftJoin('product_unit', 'product_unit.id', '=', 'irc_annual_production_capacity.quantity_unit')
                ->where('app_id', $inspectionInfo->app_id)
                ->get(['product_unit.name as unit_name', 'irc_annual_production_capacity.product_name', 'irc_annual_production_capacity.quantity']);

            $inspectionAnnualProductionCapacity = InspectionAnnualProduction::leftJoin('product_unit', 'product_unit.id', '=', 'irc_inspection_annual_production.quantity_unit')
                ->where('irc_inspection_annual_production.inspection_id', $inspectionInfo->id)
                ->get(['product_unit.name as unit_name', 'irc_inspection_annual_production.*']);

            $totalFee = DB::table('irc_inspection_gov_fee_range')->where('status', 1)->get();

            return view('IrcRecommendationNew::inspection-report-form', compact('inspectionInfo', 'inspectionAnnualProductionCapacity',
                'annualProductionCapacity' ,'initialProduction', 'listOfMachineryImportedTotal', 'listOfMachineryLocalTotal', 'totalFee'));

        } catch (\Exception $e) {
            Log::error('IRCViewInspectionForm : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [IRC-1010]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . "[IRC-1010]" . "</h4>"
            ]);
        }
    }

    public function inspectionReportGenerate($inspectionId)
    {
        try {
            $inspection_id = Encryption::decodeId($inspectionId);
            $process_type_id = $this->process_type_id;

            $inspectionInfo = ProcessList::leftJoin('irc_inspection as apps', 'apps.app_id', '=', 'process_list.ref_id')
                ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
                ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                    $join->on('ps.id', '=', 'process_list.status_id');
                    $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
                })
                ->leftJoin('ea_organization_status', 'ea_organization_status.id', '=', 'apps.organization_status_id')
                ->leftJoin('bank', 'bank.id', '=', 'apps.bank_id')
                ->leftJoin('bank_branches', 'bank_branches.bank_id', '=', 'apps.bank_id')
                ->leftJoin('irc_project_status', 'irc_project_status.id', '=', 'apps.project_status_id')
                ->where('process_list.process_type_id', $process_type_id)
                ->where('apps.id', $inspection_id)
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
                    'user_desk.desk_name',
                    'ps.status_name',
                    'ps.color',
                    'ea_organization_status.name as organization_status_name',
                    'bank.name as bank_name',
                    'bank_branches.branch_name as branch_name',
                    'apps.*',
                    'irc_project_status.name as project_status_name',
                ]);

            $inspectionAnnualProductionCapacity = InspectionAnnualProduction::leftJoin('product_unit', 'product_unit.id', '=', 'irc_inspection_annual_production.quantity_unit')
                ->where('irc_inspection_annual_production.inspection_id', $inspectionInfo->id)
                ->get(['product_unit.name as unit_name', 'irc_inspection_annual_production.*']);

            $annualProductionCapacity = AnnualProductionCapacity::leftJoin('product_unit', 'product_unit.id', '=', 'irc_annual_production_capacity.quantity_unit')
                ->where('app_id', $inspectionInfo->app_id)
                ->get(['product_unit.name as unit_name', 'irc_annual_production_capacity.product_name', 'irc_annual_production_capacity.quantity']);

            $contents = view('IrcRecommendationNew::inspection-report-generate', compact('inspectionInfo', 'inspectionAnnualProductionCapacity', 'annualProductionCapacity',
                'initialProduction', 'listOfMachineryImportedTotal', 'listOfMachineryLocalTotal'))->render();

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
            $mpdf->Output('name' . '.pdf', 'I');

        } catch (\Exception $e) {
            Log::error('IRCViewInspectionForm : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [IRC-1070]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . "[IRC-1010]" . "</h4>"
            ]);
        }
    }

    public function entitlementPaperPDF($inspectionId)
    {
        if (!ACL::getAccsessRight($this->aclName, '-V-')) {
            die('You have no access right! Please contact system administration for more information. [IRC-979]');
        }

        $inspection_id = Encryption::decodeId($inspectionId);

        try {

            $inspectionInfo = ProcessList::leftJoin('irc_inspection as apps', 'apps.app_id', '=', 'process_list.ref_id')
                ->leftjoin('irc_project_status', 'irc_project_status.id', '=', 'apps.project_status_id')
                ->leftjoin('irc_apps', 'irc_apps.id', '=', 'apps.app_id')
                ->where('apps.id', $inspection_id)
                ->where('process_list.process_type_id', $this->process_type_id)
                ->where('apps.ins_approved_status', 1)
                ->where('process_list.status_id', 25)
                ->first([
                    'process_list.tracking_no',
                    'irc_project_status.name as project_status_name',
                    'irc_apps.g_full_name',
                    'irc_apps.g_designation',
                    'irc_apps.g_signature',
                    'apps.*'
                ]);

            //Barcode generator
            $dn1d = new DNS1D();
            $trackingNo = $inspectionInfo->tracking_no; // tracking no push on barcode.
            if (!empty($trackingNo)) {
                $barcode = $dn1d->getBarcodePNG($trackingNo, 'C39', 2, 60);
                $barcode_url = 'data:image/png;base64,' . $barcode;
            } else {
                $barcode_url = '';
            }

            //Qr code generator
            $dn2d = new DNS2D();
            if (!empty($trackingNo)) {
                $qrcode = $dn2d->getBarcodePNG($trackingNo, 'QRCODE');
                $qrcode_url = 'data:image/png;base64,' . $qrcode;
            } else {
                $qrcode_url = '';
            }

            // Desk user signature url
            if (!empty($inspectionInfo->dd_signature)) {
                $dd_signature = '<img src="users/signature/' . $inspectionInfo->dd_signature . '" alt="Deputy Director" width="70">';
            } else {
                $dd_signature = "";
            }

            // Company director signature url
            if (!empty($inspectionInfo->g_signature)) {
                if(file_exists("uploads/".$inspectionInfo->g_signature)){
                    $signature = '<img src="uploads/' . $inspectionInfo->g_signature . '" alt="Company Director" width="70">';
                }else{
                    $url = url('uploads', $inspectionInfo->g_signature);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    $g_signature_response = curl_exec($ch);
                    $g_signature_image_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
                    curl_close($ch);
                    $signature = '<img src="data:' . $g_signature_image_type . ';base64,' . base64_encode($g_signature_response) . '" width="70">';

                }
            } else {
                $signature = "";
            }

            $inspectionProductionCapacity = InspectionAnnualProduction::leftJoin('product_unit', 'product_unit.id', '=', 'irc_inspection_annual_production.quantity_unit')
                ->where('irc_inspection_annual_production.inspection_id', $inspection_id)
                ->get([
                    'product_unit.name as unit_name',
                    'irc_inspection_annual_production.*'
                ]);

            $contents = view('IrcRecommendationNew::inspection-entitlement-pdf',
                compact('inspectionInfo', 'inspectionProductionCapacity'))->render();

            $mpdf = new mPDF([
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font_size' => 10,
                'default_font' => 'timesnewroman',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'margin_header' => 10,
                'margin_footer' => 10,
                'setAutoTopMargin' => 'pad',
                'setAutoBottomMargin' => 'pad'
            ]);

            //static header section
            $mpdf->SetHTMLHeader('<div class="header">
            <div class="logo_image" style="float: left; width: 140px">
               <img src="' . $qrcode_url . '" alt="QR Code" height="60" />
             </div>
             <div style="text-align: right;">
               <span style="font-size: 18px;  float: right; font-weight: bold; color: #170280;">' .$inspectionInfo->company_name . '</span><br>
                   <span style="font-size: 13px; font-weight: bold">' . $inspectionInfo->office_address . '</span>
             </div><br>
             <div class="barcode" style="text-align: center;">
                 <img src="' . $barcode_url . '" width="25%" alt="Barcode" height="30" />
             </div>
            </div>');

            if (config('app.server_type') != 'live') {
                $mpdf->SetWatermarkText('TEST PURPOSE ONLY');
                $mpdf->showWatermarkText = true;
                $mpdf->watermark_font = 'timesnewroman';
                $mpdf->watermarkTextAlpha = 0.1;
            }

            $mpdf->useSubstitutions;
            $mpdf->SetProtection(array('print'));
            $mpdf->SetDefaultBodyCSS('color', '#000');
            $mpdf->SetTitle("BIDA One Stop Service");
            $mpdf->SetSubject("Entitlement Paper");
            $mpdf->SetAuthor("Business Automation Limited");
            $mpdf->autoScriptToLang = true;
            $mpdf->baseScript = 1;
            $mpdf->autoVietnamese = true;
            $mpdf->autoArabic = true;
            $mpdf->autoLangToFont = true;
            $mpdf->SetDisplayMode('fullwidth');

            //static footer section
            $mpdf->SetHTMLFooter('
            <div style="margin-top:20px;">
                <table class="table" width="100%">
                    <tr>
                        <td style="align:left; width: 75%">
                           ' . $signature . '<br>' .
                $inspectionInfo->g_full_name  . '<br>' .
                $inspectionInfo->g_designation . '<br>' .
                $inspectionInfo->company_name. '<br>' .
                $inspectionInfo->office_address . ' 
                        </td>
                        
                        <td style="text-align:center;" >
                                ' . $dd_signature . '<br> 
                                (' . $inspectionInfo->dd_name . ') <br>
                                ' . $inspectionInfo->dd_designation . '<br> 
                                Phone: ' . $inspectionInfo->dd_mobile_no . '<br>
                                Email: ' . $inspectionInfo->dd_email . '
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="font-size: 9px; text-align: center">
                            <br>
                            Note: This is an authenticated system generated documents and does not require signature.<br>
                            Document generated by BIDA One Stop Service System. <i>https://bidaquickserv.org</i>
                        </td>
                    </tr>
                </table>
            </div>
            <table width="100%">
                <tr>
                    <td width="50%"><i style="font-size: 9px;">Download time: {DATE j-M-Y h:i a}</i></td>
                    <td width="50%" align="right"><i style="font-size: 9px;">{PAGENO}/{nbpg}</i></td>
                </tr>
            </table>');

            $stylesheet = file_get_contents('assets/stylesheets/certificate.css');
            $mpdf->setAutoTopMargin = 'stretch';
            $mpdf->setAutoBottomMargin = 'stretch';
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->WriteHTML($contents, 2);
            $mpdf->defaultfooterfontsize = 9;
            $mpdf->defaultfooterfontstyle = 'B';
            $mpdf->defaultfooterline = 0;
            $mpdf->SetCompression(true);
            $mpdf->Output('EntitlementPaper.pdf', 'I'); // Saving pdf *** F for Save only, I for view only.

        } catch (\Exception $e) {
            Log::error('IRCViewInspectionForm : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [IRC-1017]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . "[IRC-1017]" . "</h4>"
            ]);
        }
    }

    public function ProductionCapacityPDF($app_id)
    {
        try {
            $app_id = Encryption::decodeId($app_id);
            
            $inspectionInfo = ProcessList::leftJoin('irc_inspection as apps', 'apps.app_id', '=', 'process_list.ref_id')
                ->where('process_list.ref_id', $app_id)
                ->where('process_list.process_type_id', $this->process_type_id)
                ->where('apps.ins_approved_status', 1)
                ->where('process_list.status_id', 25)
                ->first([
                    'process_list.tracking_no',
                    'apps.id',
                    'apps.company_name',
                    'apps.irc_purpose_id',
                    'apps.io_name',
                    'apps.io_designation',
                    'apps.io_mobile_no',
                    'apps.io_email',
                    'apps.io_signature',
                ]);

            //Barcode generator
            $dn1d = new DNS1D();
            $trackingNo = $inspectionInfo->tracking_no; // tracking no push on barcode.
            if (!empty($trackingNo)) {
                $barcode = $dn1d->getBarcodePNG($trackingNo, 'C39',2,60);
                $barcode_url = 'data:image/png;base64,' . $barcode;
            } else {
                $barcode_url = '';
            }

            //Qr code generator
            $dn2d = new DNS2D();
            if (!empty($trackingNo)) {
                $qrcode = $dn2d->getBarcodePNG($trackingNo, 'QRCODE');
                $qrcode_url = 'data:image/png;base64,' . $qrcode;
            } else {
                $qrcode_url = '';
            }

            //signature url
            if (!empty($inspectionInfo->io_signature)) {
                $signature = file_exists("users/signature/" . $inspectionInfo->io_signature) ? '<img src="users/signature/' . $inspectionInfo->io_signature . '" alt="Inspection Officer" width="70">' : '';
            } else {
                $signature = "";
            }

            $productUnit = [0 => ''] + ProductUnit::where('status', 1)->where('is_archive', 0)->orderBy('name')->lists('name', 'id')->all();

            $annualProductionCapacity = AnnualProductionCapacity::leftJoin('product_unit', 'product_unit.id', '=', 'irc_annual_production_capacity.quantity_unit')
                ->where('app_id', $app_id)
                ->get(['product_unit.name as unit_name', 'irc_annual_production_capacity.*']);

            $contents = view('IrcRecommendationNew::inspection-production-capacity-pdf',
                compact('annualProductionCapacity', 'productUnit', 'inspectionInfo'))->render();

            $mpdf = new mPDF([
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font_size' => 10,
                'default_font' => 'timesnewroman',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'margin_header' => 10,
                'margin_footer' => 10,
                'setAutoTopMargin' => 'pad',
                'setAutoBottomMargin' => 'pad'
            ]);

            // static header section
            $mpdf->SetHTMLHeader('<div class="header">
            <div class="logo_image" style="float: left; width: 140px">
               <img src="assets/images/bida_logo.png" alt="" height="80px">
             </div>
             <div style="text-align: right;">
               <span style="font-size: 18px;  float: right; font-weight: bold; color: #170280;">Bangladesh Investment Development Authority </span>
               <span style="font-size: 18px;  float: right; font-weight: bold; color: #170280;">(BIDA)</span><br>
               <span style="font-size: 13px; font-weight: bold">'.trans('messages.authority_text').'</span>
             </div><br>
             <div class="barcode" style="text-align: center;">
                 <img src="'.$barcode_url.'" width="25%" alt="Barcode" height="30" />
             </div>
            </div>');

            if (config('app.server_type') != 'live') {
                $mpdf->SetWatermarkText('TEST PURPOSE ONLY');
                $mpdf->showWatermarkText = true;
                $mpdf->watermark_font = 'timesnewroman';
                $mpdf->watermarkTextAlpha = 0.1;
            }

            $mpdf->useSubstitutions;
            $mpdf->SetProtection(array('print'));
            $mpdf->SetDefaultBodyCSS('color', '#000');
            $mpdf->SetTitle("BIDA One Stop Service");
            $mpdf->SetSubject("Production Capacity");
            $mpdf->SetAuthor("Business Automation Limited");
            $mpdf->autoScriptToLang = true;
            $mpdf->baseScript = 1;
            $mpdf->autoVietnamese = true;
            $mpdf->autoArabic = true;
            $mpdf->autoLangToFont = true;
            $mpdf->SetDisplayMode('fullwidth');

            // static footer section
            $mpdf->SetHTMLFooter('
                <div style="margin-top:20px;">
                    <table class="table" width="100%">
                        <tr>
                            <td width="72%" class="text-left" style="padding-top:-30px;font-size:12px;">
                                   <img src="' . $qrcode_url . '" width="70" alt="QR Code" height="70" />
                            </td>
                              
                            <td style="text-align:center;" >
                                '.$signature.'<br> 
                                ('.$inspectionInfo->io_name.') <br>
                                    ' . $inspectionInfo->io_designation.'<br> 
                                    Phone: '.$inspectionInfo->io_mobile_no.'<br>
                                    Email: '.$inspectionInfo->io_email.'
                            </td>
                           
                        </tr>
                    </table>
                    
                </div>
                <table width="100%">
                    <tr>
                         <td colspan="2" style="font-size: 9px; text-align: center">
                            <br>
                            Bangladesh Investment Development Authority, '.trans('messages.authority_text').', Plot #E-6/B, Agargaon, Sher-E-Bangla Nagar, Dhaka-1207.<br>
                            Phone: PABX 88-02-55007241-5, Fax: 88-02-55007238, Email: info@bida.gov.bd, Web: www.bida.gov.bd<br>
                            <i>To verify the authenticity of the approval copy, please scan the QR & log on to https://bidaquickserv.org.</i>
                        </td>
                    </tr>
                    
                    <tr>
                        <td width="50%"><i style="font-size: 9px;">Download time: {DATE j-M-Y h:i a}</i></td>
                        <td width="50%" align="right"><i style="font-size: 9px;">{PAGENO}/{nbpg}</i></td>
                    </tr>
                </table>');

            $stylesheet = file_get_contents('assets/stylesheets/certificate.css');
            $mpdf->setAutoTopMargin = 'stretch';
            $mpdf->setAutoBottomMargin = 'stretch';
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->WriteHTML($contents, 2);
            $mpdf->defaultfooterfontsize = 9;
            $mpdf->defaultfooterfontstyle = 'B';
            $mpdf->defaultfooterline = 0;
            $mpdf->SetCompression(true);
            $mpdf->Output('ProductionCapacity.pdf', 'I');

        } catch (\Exception $e) {
            Log::error('IRCViewInspectionForm : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [IRC-1070]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . "[IRC-1010]" . "</h4>"
            ]);
        }
    }

    public function ExisitingMachinesPDF($applicationId)
    {
        try {
            $applicationId = Encryption::decodeId($applicationId);
            
            $appInfo = ProcessList::leftJoin('irc_apps', 'irc_apps.id', '=', 'process_list.ref_id')
                ->where('process_list.ref_id', $applicationId)
                ->where('process_list.process_type_id', $this->process_type_id)
                ->where('process_list.status_id', 25)
                ->first([
                    'irc_apps.company_name',
                    'irc_apps.em_lc_total_taka_mil',
                    'irc_apps.em_local_total_taka_mil',
                    'process_list.tracking_no',
                ]);

            $lastInspectionInfo = IrcInspection::where('app_id', $applicationId)
                ->where('ins_approved_status', 1)
                ->orderBy('id', 'desc')
                ->first([
                    'io_name',
                    'io_designation',
                    'io_mobile_no',
                    'io_email',
                    'io_signature',
                ]);

            $existing_machines_lc = ExistingMachinesLc::leftJoin('product_unit', 'product_unit.id', '=', 'irc_existing_machines_lc.quantity_unit')
                ->leftJoin('currencies', 'currencies.id', '=', 'irc_existing_machines_lc.price_unit')
                ->where('irc_existing_machines_lc.app_id', $applicationId)
                ->get([
                    'irc_existing_machines_lc.product_name',
                    'product_unit.name',
                    'irc_existing_machines_lc.quantity',
                    'irc_existing_machines_lc.unit_price',
                    'currencies.code',
                    'irc_existing_machines_lc.price_foreign_currency',
                    'irc_existing_machines_lc.price_bdt',
                    'irc_existing_machines_lc.price_taka_mil',
                ]);
            $total_existing_machines_lc_bdt = ExistingMachinesLc::where('app_id', $applicationId)->sum('price_bdt');

            $existing_machines_local = ExistingMachinesLocal::leftJoin('product_unit', 'product_unit.id', '=', 'irc_existing_machines_local.quantity_unit')
                ->where('irc_existing_machines_local.app_id', $applicationId)
                ->get([
                    'irc_existing_machines_local.product_name',
                    'product_unit.name',
                    'irc_existing_machines_local.quantity',
                    'irc_existing_machines_local.unit_price',
                    'irc_existing_machines_local.price_bdt',
                    'irc_existing_machines_local.price_taka_mil',
                ]);

            //signature url
            if (!empty($lastInspectionInfo->io_signature)) {
                $signature = file_exists("users/signature/" . $lastInspectionInfo->io_signature) ? '<img src="users/signature/' . $lastInspectionInfo->io_signature . '" alt="Inspection Officer" width="70">' : '';
            } else {
                $signature = "";
            }

            //Barcode generator
            $dn1d = new DNS1D();
            $trackingNo = $appInfo->tracking_no; // tracking no push on barcode.
            if (!empty($trackingNo)) {
                $barcode = $dn1d->getBarcodePNG($trackingNo, 'C39', 2, 60);
                $barcode_url = 'data:image/png;base64,' . $barcode;
            } else {
                $barcode_url = '';
            }

            //Qr code generator
            $dn2d = new DNS2D();
            if (!empty($trackingNo)) {
                $qrcode = $dn2d->getBarcodePNG($trackingNo, 'QRCODE');
                $qrcode_url = 'data:image/png;base64,' . $qrcode;
            } else {
                $qrcode_url = '';
            }

            $contents = view('IrcRecommendationNew::inspection-existing-machines-pdf', compact('appInfo', 'existing_machines_lc',
                'total_existing_machines_lc_bdt', 'existing_machines_local', 'lastInspectionInfo'))->render();

            $mpdf = new mPDF([
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font_size' => 10,
                'default_font' => 'timesnewroman',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'margin_header' => 10,
                'margin_footer' => 10,
                'setAutoTopMargin' => 'pad',
                'setAutoBottomMargin' => 'pad'
            ]);

            //static header section
            $mpdf->SetHTMLHeader('<div class="header">
            <div class="logo_image" style="float: left; width: 140px">
               <img src="assets/images/bida_logo.png" alt="" height="80px">
             </div>
             <div style="text-align: right;">
               <span style="font-size: 18px;  float: right; font-weight: bold; color: #170280;">Bangladesh Investment Development Authority </span>
               <span style="font-size: 18px;  float: right; font-weight: bold; color: #170280;">(BIDA)</span><br>
               <span style="font-size: 13px; font-weight: bold">'.trans('messages.authority_text').'</span>
             </div><br>
             <div class="barcode" style="text-align: center;">
                 <img src="'.$barcode_url.'" width="25%" alt="Barcode" height="30" />
             </div>
            </div>');

            if (config('app.server_type') != 'live') {
                $mpdf->SetWatermarkText('TEST PURPOSE ONLY');
                $mpdf->showWatermarkText = true;
                $mpdf->watermark_font = 'timesnewroman';
                $mpdf->watermarkTextAlpha = 0.1;
            }

            $mpdf->useSubstitutions;
            $mpdf->SetProtection(array('print'));
            $mpdf->SetDefaultBodyCSS('color', '#000');
            $mpdf->SetTitle("BIDA One Stop Service");
            $mpdf->SetSubject("Existing Machinery");
            $mpdf->SetAuthor("Business Automation Limited");
            $mpdf->autoScriptToLang = true;
            $mpdf->baseScript = 1;
            $mpdf->autoVietnamese = true;
            $mpdf->autoArabic = true;
            $mpdf->autoLangToFont = true;
            $mpdf->SetDisplayMode('fullwidth');

            // static footer section
            $mpdf->SetHTMLFooter('
                <div style="margin-top:20px;">
                    <table class="table" width="100%">
                        <tr>
                            <td style="align:left; width: 75%">
                                 <img src="'.$qrcode_url.'" width="70" alt="QR Code" height="70" />
                            </td>
                              
                            <td style="text-align:center;" >
                                '.$signature.'<br> 
                                ('.$lastInspectionInfo->io_name.') <br>
                                    ' . $lastInspectionInfo->io_designation.'<br> 
                                    Phone: '.$lastInspectionInfo->io_mobile_no.'<br>
                                    Email: '.$lastInspectionInfo->io_email.'
                            </td>
                        </tr>
                        <tr>
                        <td colspan="2" style="font-size: 9px; text-align: center">
                            <br>
                            Bangladesh Investment Development Authority, '.trans('messages.authority_text').', Plot #E-6/B, Agargaon, Sher-E-Bangla Nagar, Dhaka-1207.<br>
                            Phone: PABX 88-02-55007241-5, Fax: 88-02-55007238, Email: info@bida.gov.bd, Web: www.bida.gov.bd<br>
                            <i>To verify the authenticity of the approval copy, please scan the QR & log on to https://bidaquickserv.org.</i>
                        </td>
                        </tr>
                    </table>
                    
                </div>
                <table width="100%">
                    <tr>
                        <td width="50%"><i style="font-size: 9px;">Download time: {DATE j-M-Y h:i a}</i></td>
                        <td width="50%" align="right"><i style="font-size: 9px;">{PAGENO}/{nbpg}</i></td>
                    </tr>
                </table>');

            $stylesheet = file_get_contents('assets/stylesheets/certificate.css');
            $mpdf->setAutoTopMargin = 'stretch';
            $mpdf->setAutoBottomMargin = 'stretch';
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->WriteHTML($contents, 2);
            $mpdf->defaultfooterfontsize = 9;
            $mpdf->defaultfooterfontstyle = 'B';
            $mpdf->defaultfooterline = 0;
            $mpdf->SetCompression(true);
            $mpdf->Output('ExistingMachines.pdf', 'I');

        } catch (\Exception $e) {
            Log::error('IRCViewInspectionForm : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [IRC-1070]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . "[IRC-1010]" . "</h4>"
            ]);
        }
    }

    public function listOfAnnualProduction(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [ASDC-1012]';
        }

        $app_id = Encryption::decodeId($request->application_id);

        $total_list_of_apc = AnnualProductionCapacity::where('app_id', $app_id)->count();

        return response()->json([
            'total_list_of_apc' => $total_list_of_apc
        ]);
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

        return "irc_1st_" . $ownership_key . "_" . $organization_key;
    }

    /**
     * @param $bra_tracking_no
     * @return bool
     */
    public function BRAChildTableDataLoad($bra_tracking_no)
    {
        $bra_ref_no = ProcessList::where('tracking_no', $bra_tracking_no)
            ->where('process_type_id', 12)
            ->where('status_id', 25)
            ->value('ref_id');

        $getAnnualProductionCapacity = DB::table('annual_production_capacity_amendment')
            ->select(DB::raw('
                COALESCE(NULLIF(n_product_name, ""), product_name) as product_name,
                COALESCE(NULLIF(n_quantity_unit, ""), quantity_unit) as quantity_unit,
                COALESCE(NULLIF(n_quantity, ""), quantity) as quantity,
                COALESCE(NULLIF(n_price_usd, ""), price_usd) as price_usd,
                COALESCE(NULLIF(n_price_taka, ""), price_taka) as price_taka
            '))
            ->where(['app_id' => $bra_ref_no, 'process_type_id' => 12, 'status' => 1, 'is_archive' => 0])
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
            ->where(['app_id' => $bra_ref_no, 'process_type_id' => 12, 'status' => 1, 'is_archive' => 0])
            ->get();

        if (count($getSourceOfFinance) > 0) {
            Session::put('brSourceOfFinance', $getSourceOfFinance);
        }

        $BRListOfDirectors = DB::table('list_of_director_amendment')
            ->select(DB::raw('
                            nationality_type, identity_type,
                            ifnull(n_l_director_name, l_director_name) as l_director_name,
                            ifnull(n_l_director_designation, l_director_designation) as l_director_designation,
                            ifnull(n_l_director_nationality, l_director_nationality) as l_director_nationality,
                            ifnull(n_nid_etin_passport, nid_etin_passport) as nid_etin_passport,
                            gender, date_of_birth, passport_type, date_of_expiry, passport_scan_copy, status
                        '))
            ->where(['app_id' => $bra_ref_no, 'process_type_id' => 12, 'status' => 1, 'is_archive' => 0])
            ->get();

        if (count($BRListOfDirectors) > 0) {
            Session::put('brListOfDirectors', $BRListOfDirectors);
        }
        
        $listOfMachineryLocal = DB::table('list_of_machinery_local_amendment')
            ->select(DB::raw('
                            ifnull(n_l_machinery_local_name, l_machinery_local_name) as l_machinery_local_name,
                            ifnull(n_l_machinery_local_qty, l_machinery_local_qty) as l_machinery_local_qty,
                            ifnull(n_l_machinery_local_unit_price, l_machinery_local_unit_price) as l_machinery_local_unit_price,
                            ifnull(n_l_machinery_local_total_value, l_machinery_local_total_value) as l_machinery_local_total_value,
                            total_million
                        '))
            ->where(['app_id' => $bra_ref_no, 'process_type_id' => 12, 'status' => 1, 'is_archive' => 0]);

        if ($listOfMachineryLocal->count() > 0) {
            Session::put('brListOfMachinesLocal', $listOfMachineryLocal->get());
            Session::put('brListOfMachinesLocalTotal', $listOfMachineryLocal->sum('total_million'));
        }

        return true;
    }

    /**
     * @param $br_ref_id
     * @return bool
     */
    public function BRChildTableDataLoad($br_ref_id)
    {
        $BRAnnualProductionCapacity = LaAnnualProductionCapacity::where('app_id', $br_ref_id)->where('status',1)->where('is_archive',0)->get();
        $BRSourceOfFinance = SourceOfFinance::where('app_id', $br_ref_id)->where('status',1)->where('is_archive',0)->get();
        $BRListOfDirectors = ListOfDirectors::where('app_id', $br_ref_id)->where('process_type_id', 102)->where('status',1)->where('is_archive',0)->get();
        $BRListOfMachinesLocal = ListOfMachineryLocal::where('app_id', $br_ref_id)->where('process_type_id', 102)->where('status',1)->where('is_archive',0);

        if (count($BRAnnualProductionCapacity) > 0) {
            Session::put('brAnnualProductionCapacity', $BRAnnualProductionCapacity);
        }

        if (count($BRSourceOfFinance) > 0) {
            Session::put('brSourceOfFinance', $BRSourceOfFinance);
        }

        if (count($BRListOfDirectors) > 0) {
            Session::put('brListOfDirectors', $BRListOfDirectors);
        }

        if ($BRListOfMachinesLocal->count() > 0) {
            Session::put('brListOfMachinesLocal', $BRListOfMachinesLocal->get());
            Session::put('brListOfMachinesLocalTotal', $BRListOfMachinesLocal->sum('l_machinery_local_total_value'));
        }

        return true;
    }

}