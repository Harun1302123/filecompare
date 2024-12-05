<?php

namespace App\Modules\BidaRegistrationAmendment\Controllers;

use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Libraries\ImageProcessing;
use App\Libraries\UtilFunction;
use App\Modules\Apps\Models\AppDocuments;
use App\Modules\BasicInformation\Controllers\BasicInformationController;
use App\Modules\BasicInformation\Models\EA_OrganizationType;
use App\Modules\BasicInformation\Models\EA_OwnershipStatus;
use App\Modules\BidaRegistration\Models\ListOfDirectors;
use App\Modules\BidaRegistration\Models\SourceOfFinance;
use App\Modules\BidaRegistrationAmendment\Models\BusinessClass;
use App\Modules\BidaRegistrationAmendment\Models\AnnualProductionCapacity;
use App\Modules\BidaRegistrationAmendment\Models\ListOfDirectorsAmendment;
use App\Modules\BidaRegistrationAmendment\Models\ListOfMachineryImported;
use App\Modules\BidaRegistrationAmendment\Models\ListOfMachineryLocal;
use App\Modules\BidaRegistrationAmendment\Models\ProductUnit;
use App\Modules\BidaRegistrationAmendment\Models\BidaRegistrationAmendment;
use App\Modules\BidaRegistrationAmendment\Models\AnnualProductionCapacityAmendment;
use App\Modules\BidaRegistrationAmendment\Models\ListOfMachineryImportedAmendment;
use App\Modules\BidaRegistrationAmendment\Models\ListOfMachineryLocalAmendment;
use App\Modules\BidaRegistrationAmendment\Models\SourceOfFinanceAmendment;
use App\Modules\Settings\Models\Attachment;
use App\Modules\BasicInformation\Models\EA_OrganizationStatus;
use App\Modules\BidaRegistration\Models\ProjectStatus;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\Settings\Models\Currencies;
use App\Modules\SonaliPayment\Controllers\SonaliPaymentController;
use App\Modules\SonaliPayment\Models\PaymentConfiguration;
use App\Modules\SonaliPayment\Models\PaymentDetails;
use App\Modules\SonaliPayment\Models\PaymentDistribution;
use App\Modules\SonaliPayment\Models\SonaliPayment;
use App\Modules\Users\Models\AreaInfo;
use App\Modules\Users\Models\Countries;
use App\Modules\Users\Models\DivisionalOffice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\BidaRegistrationAmendment\Models\ExistingBRA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Validator;
use yajra\Datatables\Datatables;
use App\Modules\ProcessPath\Services\BRCommonPoolManager;

class BidaRegistrationAmendmentController extends Controller
{
    protected $process_type_id;
    protected $aclName;

    public function __construct()
    {
        $this->process_type_id = 12;
        $this->aclName = 'BidaRegistrationAmendment';
    }

    /*
     * application form open
     */
    public function applicationForm(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.';
        }

        if (!ACL::getAccsessRight($this->aclName, '-A-')) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>You have no access right! Contact with system admin for more information [BRAC-971]</h4>"
            ]);
        }

        // Check whether the applicant company is eligible and have approved basic information application
        $company_id = CommonFunction::getUserWorkingCompany();
        if (CommonFunction::checkEligibilityAndBiApps($company_id) != 1) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<div class='btn-center'><h4 class='custom-err-msg'>Sorry! You have no approved Basic Information application for BIDA services. [BRAC-9991]</h4> <br/> <a href='/dashboard' class='btn btn-primary btn-sm'>Apply for Basic Information</a></div>"
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
                    'html' => "<h4 class='custom-err-msg'> Payment Configuration not found ![BRAC-10101]</h4>"
                ]);
            }

            $amount_array = $this->fixedUnfixedAmountsForPayment($payment_config);
            $payment_config->amount = $amount_array['total_fixed_unfixed_amount'] + $payment_config->amount;
            $payment_config->vat_on_pay_amount = $amount_array['total_vat_on_pay_amount'];

            $totalFee = DB::table('pay_order_amount_setup')->where('process_type_id', 102)->get([
                'min_amount_bdt', 'max_amount_bdt', 'p_o_amount_bdt'
            ]);

            $eaOrganizationType = ['' => 'Select one'] + EA_OrganizationType::where('is_archive', 0)->whereIn('type', [1, 3])->where('status', 1)->orderBy('name')->lists('name', 'id')->all();
            $eaOrganizationStatus = ['' => 'Select one'] + EA_OrganizationStatus::where('is_archive', 0)->where('status', 1)->orderBy('name')->lists('name', 'id')->all();
            $eaOwnershipStatus = ['' => 'Select one'] + EA_OwnershipStatus::where('is_archive', 0)->where('status', 1)->orderBy('name')->lists('name', 'id')->all();
            $countries = ['' => 'Select One'] + Countries::where('country_status', 'Yes')->orderBy('nicename', 'asc')->lists('nicename', 'id')->all();
            $projectStatusList = ['' => 'Select One'] + ProjectStatus::orderBy('name')->where('is_archive', 0)->where('status', 1)->lists('name', 'id')->all();
            $productUnit = ['0' => 'Select one'] + ProductUnit::where('status', 1)->where('is_archive', 0)->orderBy('name')->lists('name', 'id')->all();
            $currencyBDT = Currencies::orderBy('code')->whereIn('code', ['BDT'])->where('is_archive', 0)->where('is_active', 1)->lists('code', 'id');
            $nationality = ['' => 'Select one'] + Countries::where('country_status', 'Yes')->where('nationality', '!=', '')->orderby('nationality', 'asc')->lists('nationality', 'id')->all();
            $divisions = ['' => 'Select One'] + AreaInfo::where('area_type', 1)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $districts = ['' => 'Select One'] + AreaInfo::where('area_type', 2)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $thana = ['' => 'Select One'] + AreaInfo::orderby('area_nm')->where('area_type', 3)->lists('area_nm', 'area_id')->all();

            $approvalCenterList = DivisionalOffice::where('status', 1)
                ->where('is_archive', 0)
                ->orderBy('id')
                ->get([
                    'id', 'office_name', 'office_address'
                ]);
            $public_html = strval(view("BidaRegistrationAmendment::application-form", compact('eaOrganizationStatus',
                'projectStatusList', 'productUnit', 'currencyBDT', 'countries', 'nationality', 'totalFee', 'approvalCenterList',
                'payment_config', 'divisions', 'districts', 'thana', 'eaOrganizationType', 'eaOwnershipStatus')));
            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (\Exception $e) {
            Log::error('BRAddForm : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [BRC-1005]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . ' [BRAC-1005]' . "</h4>"
            ]);
        }

    }

    /*
     * application document
     * @request ajax
     * @param $attachment_key
     * @param $viewMode
     * @param $app_id
     *
     */
    public function getDocList(Request $request)
    {
        $attachment_key = $request->get('attachment_key');
        $viewMode = $request->get('viewMode');
        $app_id = ($request->has('app_id') ? Encryption::decodeId($request->get('app_id')) : 0);

        if (!empty($app_id)) {
            $document = AppDocuments::leftJoin('attachment_list', 'attachment_list.id', '=', 'app_documents.doc_info_id')
                ->leftJoin('attachment_type', 'attachment_type.id', '=', 'attachment_list.attachment_type_id')
                ->where('attachment_type.key', $attachment_key)
                ->where('app_documents.ref_id', $app_id)
                ->where('app_documents.process_type_id', $this->process_type_id)
                ->where('app_documents.doc_section', 'master')
                ->get([
                    'attachment_list.id',
                    'attachment_list.doc_priority',
                    'attachment_list.additional_field',
                    'app_documents.id as document_id',
                    'app_documents.doc_file_path as doc_file_path',
                    'app_documents.doc_name'
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
        $html = strval(view("BidaRegistrationAmendment::documents",
            compact('document', 'viewMode')));
        return response()->json(['html' => $html]);
    }

    /*
     * application store
     * @request @request
     * @method POST
     */
    public function appStore(Request $request)
    {
        // Check whether the applicant company is eligible and have approved basic information application
        $company_id = CommonFunction::getUserWorkingCompany();
        if (CommonFunction::checkEligibilityAndBiApps($company_id) != 1) {
            Session::flash('error', "Sorry! You have no approved Basic Information application for BIDA services. [BRAC-9993]");
            return redirect()->back();
        }

        // Set permission mode and check ACL
        $app_id = (!empty($request->get('app_id')) ? Encryption::decodeId($request->get('app_id')) : '');
        $mode = (!empty($request->get('app_id')) ? '-E-' : '-A-');
        if (!ACL::getAccsessRight($this->aclName, $mode, $app_id)) {
            abort('400', 'You have no access right! Contact with system admin for more information. [BRAC-972]');
        }

        // Check whether the applicant company's department will get this service
        $department_id = CommonFunction::getDeptIdByCompanyId($company_id);
        if (in_array($department_id, [1, 4])) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>Sorry! The department is not allowed to apply to this application. [BRC-1042]</h4>"
            ]);
        }

        // Checking the Service Fee Payment(SFP) configuration for this service
        $payment_config = PaymentConfiguration::leftJoin('sp_payment_category', 'sp_payment_category.id', '=',
            'sp_payment_configuration.payment_category_id')
            ->where([
                'sp_payment_configuration.process_type_id' => $this->process_type_id,
                'sp_payment_configuration.payment_category_id' => 1,  // Submission Payment
                'sp_payment_configuration.status' => 1,
                'sp_payment_configuration.is_archive' => 0,
            ])->first(['sp_payment_configuration.*', 'sp_payment_category.name']);
        if (!$payment_config) {
            Session::flash('error', "Payment configuration not found [OPNC-100]");
            return redirect()->back()->withInput();
        }

        // Checking the payment distributor under payment configuration
        $stakeDistribution = PaymentDistribution::where('sp_pay_config_id', $payment_config->id)
            ->where('status', 1)
            ->where('is_archive', 0)
            ->get(['id', 'stakeholder_ac_no', 'pay_amount', 'fix_status', 'purpose', 'purpose_sbl', 'distribution_type']);
        if ($stakeDistribution->isEmpty()) {
            Session::flash('error', "Stakeholder not found [BRAC-101]");
            return redirect()->back()->withInput();
        }

        $organization_status_id = $request->get('n_organization_status_id') ? $request->n_organization_status_id : $request->organization_status_id;

        $ownership_status_id = $request->get('n_ownership_status_id') ? $request->n_ownership_status_id : $request->ownership_status_id;

        $attachment_key = CommonFunction::generateAttachmentKey($organization_status_id, $ownership_status_id, "bra");

        $doc_row = Attachment::leftJoin('attachment_type', 'attachment_type.id', '=', 'attachment_list.attachment_type_id')
            ->where('attachment_type.key', $attachment_key)
            ->where('attachment_list.status', 1)
            ->where('attachment_list.is_archive', 0)
            ->orderBy('attachment_list.order')
            ->get(['attachment_list.id', 'attachment_list.doc_name', 'attachment_list.doc_priority']);

        // get BR new info & set session
        if ($request->get('actionBtn') == 'searchBRinfo') {
            if ($request->get('is_approval_online') == 'yes' && $request->has('ref_app_tracking_no')) {
                $refAppTrackingNo = trim($request->get('ref_app_tracking_no'));

                // if br info session is empty then create new session
                $getBrApprovedData = ProcessList::where('tracking_no', $refAppTrackingNo)
                    ->where('status_id', 25)
                    ->where('company_id', $company_id)
                    ->whereIn('process_type_id', [102, 12]) // BR & BRA
                    ->first(['process_type_id', 'ref_id', 'tracking_no', 'approval_center_id']);
                if (empty($getBrApprovedData)) {
                    Session::flash('error', 'Sorry! approved BIDA Registration reference number is not found or not allowed! [BRAC-1081]');
                    return redirect()->back();
                }

                $getBrInfo = UtilFunction::checkBRCommonPoolData($getBrApprovedData->tracking_no,$getBrApprovedData->ref_id);
                if (empty($getBrInfo)) {
                    Session::flash('error', 'Sorry! BIDA Registration not found by tracking no! [BRAC-1082]');
                    return redirect()->back();
                }

                // $getServiceName = UtilFunction::getRefAppServiceName($getBrApprovedData->tracking_no);
                Session::put('brInfo', $getBrInfo->toArray());
                Session::put('brInfo.is_approval_online', $request->get('is_approval_online'));
                Session::put('brInfo.ref_app_tracking_no', $request->get('ref_app_tracking_no'));
                Session::put('brInfo.approval_center_id', $getBrApprovedData->approval_center_id);

                //  Load BRA information
                if ($getBrApprovedData->process_type_id == 12  && !empty($getBrInfo->bra_tracking_no)) {
                    Session::put('brInfo.ref_app_approve_date',$getBrInfo->bra_approved_date);

                    $bra_ref_no = ProcessList::where('tracking_no', $getBrInfo->bra_tracking_no)
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
                        ->where(['app_id' => $bra_ref_no, 'process_type_id' => $this->process_type_id, 'status' => 1])
                        ->whereNotIn('amendment_type', ['delete', 'remove'])
                        ->get();

                    $BRListOfDirectors = DB::table('list_of_director_amendment')
                        ->select(DB::raw('
                            ifnull(n_nationality_type, nationality_type) as nationality_type,
                            ifnull(n_identity_type, identity_type) as identity_type,
                            ifnull(n_l_director_name, l_director_name) as l_director_name,
                            ifnull(n_l_director_designation, l_director_designation) as l_director_designation,
                            ifnull(n_l_director_nationality, l_director_nationality) as l_director_nationality,
                            ifnull(n_nid_etin_passport, nid_etin_passport) as nid_etin_passport,
                            gender, date_of_birth, passport_type, date_of_expiry, passport_scan_copy, status
                        '))
                        ->where(['app_id' => $bra_ref_no, 'process_type_id' => $this->process_type_id, 'status' => 1])
                        ->whereNotIn('amendment_type', ['delete', 'remove'])
                        ->get();
                   


                    $listOfMachineryLocal = DB::table('list_of_machinery_local_amendment')
                        // ->select(DB::raw('
                        //     ifnull(n_l_machinery_local_name, l_machinery_local_name) as l_machinery_local_name,
                        //     ifnull(n_l_machinery_local_qty, l_machinery_local_qty) as l_machinery_local_qty,
                        //     ifnull(n_l_machinery_local_unit_price, l_machinery_local_unit_price) as l_machinery_local_unit_price,
                        //     ifnull(n_l_machinery_local_total_value, l_machinery_local_total_value) as l_machinery_local_total_value
                        // '))
                        // ->where(['app_id' => $bra_ref_no, 'process_type_id' => $this->process_type_id, 'status' => 1])
                        // ->whereNotIn('amendment_type', ['delete', 'remove'])
                        // ->get();
                        ->select(DB::raw('
                            IFNULL(NULLIF(n_l_machinery_local_name, \'\'), l_machinery_local_name) as l_machinery_local_name,
                            IFNULL(NULLIF(n_l_machinery_local_qty, \'\'), l_machinery_local_qty) as l_machinery_local_qty,
                            IFNULL(NULLIF(n_l_machinery_local_unit_price, \'\'), l_machinery_local_unit_price) as l_machinery_local_unit_price,
                            IFNULL(NULLIF(n_l_machinery_local_total_value, \'\'), l_machinery_local_total_value) as l_machinery_local_total_value
                        '))
                        ->where(['app_id' => $bra_ref_no, 'process_type_id' => $this->process_type_id, 'status' => 1])
                        ->whereNotIn('amendment_type', ['delete', 'remove'])
                        ->get();

                    $getSourceOfFinance = DB::table('source_of_finance_amendment')
                        ->select(DB::raw('
                           ifnull(n_country_id, country_id) as country_id,
                           ifnull(n_equity_amount, equity_amount) as equity_amount,
                           ifnull(n_loan_amount, loan_amount) as loan_amount
                       '))
                        ->where(['app_id' => $bra_ref_no, 'process_type_id' => $this->process_type_id, 'status' => 1])
                        ->get();

                    $getExistingBRA = ExistingBRA::where('app_id', $bra_ref_no)->get();
                    if (count($getExistingBRA) > 0) {
                        Session::put('existingBRA', $getExistingBRA);
                    }
                }
                
                //  Load BR information
                elseif ($getBrApprovedData->process_type_id == 102 && !empty($getBrInfo->br_tracking_no)) {
                    Session::put('brInfo.ref_app_approve_date',$getBrInfo->br_approved_date);

                    $getAnnualProductionCapacity = AnnualProductionCapacity::where('app_id', $getBrApprovedData->ref_id)
                        ->get([
                            'product_name',
                            'quantity_unit',
                            'quantity',
                            'price_usd',
                            'price_taka',
                        ]);

                    $BRListOfDirectors = ListOfDirectors::where('app_id', $getBrApprovedData->ref_id)
                        ->where('process_type_id', $getBrApprovedData->process_type_id)
                        ->get([
                            'nationality_type',
                            'identity_type',
                            'l_director_name',
                            'l_director_designation',
                            'l_director_nationality',
                            'nid_etin_passport',
                            'gender',
                            'date_of_birth',
                            'passport_type',
                            'date_of_expiry',
                            'passport_scan_copy',
                            'status',
                        ]);

                    $listOfMachineryLocal = ListOfMachineryLocal::where('app_id', $getBrApprovedData->ref_id)
                        ->where('process_type_id', $getBrApprovedData->process_type_id)
                        ->get([
                            'l_machinery_local_name',
                            'l_machinery_local_qty',
                            'l_machinery_local_unit_price',
                            'l_machinery_local_total_value',
                        ]);

                    $getSourceOfFinance = SourceOfFinance::where('app_id', $getBrApprovedData->ref_id)
                        ->where('status', 1)
                        ->get([
                            'country_id',
                            'equity_amount',
                            'loan_amount',
                        ]);
                }
                // load machinery imported data start
                $ref_process_type_id = $getBrInfo->bra_tracking_no ? 12 : 102;
                $ref_tracking_no = $getBrInfo->bra_tracking_no ? $getBrInfo->bra_tracking_no : $getBrInfo->br_tracking_no;

                $ref_id = ProcessList::where('tracking_no', $ref_tracking_no)
                    ->where('process_type_id', $ref_process_type_id)
                    ->where('status_id', 25)
                    ->value('ref_id');

                $listOfMachineryImported = BRCommonPoolManager::listOfMachineryImported($ref_process_type_id, $ref_id);
                // load machinery imported data end


                // BR or BRA session data
                if (count($getAnnualProductionCapacity) > 0) {
                    Session::put('brAnnualProductionCapacity', $getAnnualProductionCapacity);
                }
                if (count($BRListOfDirectors) > 0) {
                    Session::put('brListOfDirectors', $BRListOfDirectors);
                }
                if (count($listOfMachineryImported) > 0) {
                    Session::put('brListOfMachineryImported', $listOfMachineryImported);
                }
                if (count($listOfMachineryLocal) > 0) {
                    Session::put('brListOfMachineryLocal', $listOfMachineryLocal);
                }
                if (count($getSourceOfFinance) > 0) {
                    Session::put('sourceOfFinance', $getSourceOfFinance);
                }
                Session::put('brInfo.is_approval_online', $request->get('is_approval_online'));
                Session::put('brInfo.ref_app_tracking_no', $request->get('ref_app_tracking_no'));
                Session::put('brInfo.approval_center_id', $getBrApprovedData->approval_center_id);
                Session::flash('success', 'Successfully loaded BIDA Registration data. Please proceed to next step');
                return redirect()->back();
            }
        }

        // Clean session data
        if ($request->get('actionBtn') == 'clean_load_data') {
            Session::forget('brInfo');
            Session::forget('brAnnualProductionCapacity');
            Session::forget('brListOfDirectors');
            Session::forget('brListOfMachineryImported');
            Session::forget('brListOfMachineryLocal');
            Session::forget('sourceOfFinance');
            Session::forget('existingBRA');
            Session::flash('success', 'Successfully cleaned data.');
            return redirect()->back();
        }

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

            // write validation here
            $rules['organization_status_id'] = 'required';

            if(!empty($request->get('n_total_sales'))) {
                $rules['n_total_sales'] = 'required|numeric|max:100';
                $messages['n_total_sales.required'] = 'Total Sales field is required.';
                $messages['n_total_sales.numeric'] = 'Total Sales field must be a number.';
                $messages['n_total_sales.max'] = 'Total Sales field cannot be more than 100.';
            }

            if ($request->get('is_approval_online') == 'yes') {
                $rules['ref_app_tracking_no'] = 'required';
                $messages['ref_app_tracking_no.required'] = 'Please give your approved BIDA Registration reference no field is required.';

                if($request->get('is_bra_approval_manually') == 'yes') {
                    $rules['manually_approved_bra_no'] = 'required';
                    $messages['manually_approved_bra_no.required'] = 'Please give your manually approved BIDA Registration Amendment reference no field is required.';
                }
            } else {
                $rules['manually_approved_br_no'] = 'required';
                $messages['manually_approved_br_no.required'] = 'Please give your manually approved BIDA Registration reference no field is required.';
            }

            $rules['business_class_code'] = 'required';
            $rules['sub_class_id'] = 'required';
            $rules['other_sub_class_name'] = 'required_if:sub_class_id,-1';
            $rules['approval_center_id'] = 'required';
            $messages['approval_center_id.required'] = 'Please specify your desired office field is required.';
            $rules['organization_status_id'] = 'required';
            $messages['organization_status_id.required'] = 'Status of the organization field is required.';
            if ($request->get('organization_status_id') != 3) { //3 = local
                $rules['country_of_origin_id'] = 'required';
                $messages['country_of_origin_id.required'] = 'Country of Origin field is required.';
            }

            $salesTypes = [
                'local_sales' => 'Existing local sales',
                'foreign_sales' => 'Existing foreign sales',
                'n_local_sales' => 'Proposed local sales',
                'n_foreign_sales' => 'Proposed foreign sales',
            ];
            
            foreach ($salesTypes as $type => $label) {
                $rules[$type] = 'numeric|min:0|max:100';
                $messages["$type.numeric"] = "The $label must be a number.";
                $messages["$type.min"] = "The $label must be at least 0.";
                $messages["$type.max"] = "The $label cannot be more than 100.";
            }

            $rules['local_machinery_ivst'] = 'required';
            $rules['trade_licence_num'] = 'required';
            $rules['trade_licence_issuing_authority'] = 'required';
            $rules['tin_number'] = 'required';
            $rules['g_full_name'] = 'required';
            $rules['g_designation'] = 'required';
            $rules['accept_terms'] = 'required';

            $messages['business_class_code.required'] = 'usiness Sector (BBS Class Code) field is required.';
            $messages['sub_class_id.required'] = 'Info. based on your business class section subclass is required.';
            $messages['other_sub_class_name.required_if'] = 'Info. based on your business class section Other sub class name is required.';
            $messages['local_machinery_ivst.required'] = 'Investment section Machinery & Equipment (Million) field is required.';
            $messages['trade_licence_num.required'] = '9. Trade licence details section Trade Licence Number field is required.';
            $messages['trade_licence_issuing_authority.required'] = '9. Trade licence details section Issuing Authority field is required.';
            $messages['tin_number.required'] = '10. Tin section Tin Number field is required.';
            $messages['g_full_name.required'] = '>Information of (Chairman/ Managing Director/ Or Equivalent) section Full Name field is required.';
            $messages['g_designation.required'] = '>Information of (Chairman/ Managing Director/ Or Equivalent) section Position/ Designation field is required.';
            $messages['accept_terms.required'] = 'I do here by declare that the information given above is true to the best of 
            my knowledge and I shall be liable for any false information/ statement is given field is required.';

            if (empty($request->get('investor_signature_base64'))) {
                $rules['investor_signature_hidden'] = 'required';
                $messages['investor_signature_hidden.required'] = '>Information of (Chairman/ Managing Director/ Or Equivalent) section Signature field is required.';
            } else {
                $rules['investor_signature_base64'] = 'required';
                $messages['investor_signature_base64.required'] = '>Information of (Chairman/ Managing Director/ Or Equivalent) section Signature field is required.';
            }

            $this->validate($request, $rules, $messages);

            if ($request->get('is_approval_online') == 'no') {
                
                $trackingNo = $request->get('manually_approved_br_no');

                $exists = ProcessList::where('tracking_no', $trackingNo)
                ->whereIn('process_type_id', [102, 12]) // BR & BRA
                ->where('company_id', $company_id)
                ->where('status_id', 25)
                ->orderBy('id', 'desc')
                ->exists();

                if($exists) {
                    Session::flash('error', "You've already taken application online using this tracking number.");
                    return redirect()->back()->withInput();
                }
            }

            $local_sales = $request->get('local_sales') ?: 0;
            $foreign_sales = $request->get('foreign_sales') ?: 0;

            $n_local_sales = $request->get('n_local_sales') ?: 0;
            $n_foreign_sales = $request->get('n_foreign_sales') ?: 0;

            $existingSalesValid = $this->validateSales($local_sales, $foreign_sales, 'existing');
            $proposedSalesValid = $this->validateSales($n_local_sales, $n_foreign_sales, 'proposed');

            if (!$existingSalesValid || !$proposedSalesValid) {
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
                $total_equity += floatval($value);
            }
            //checking equity amount
            if (number_format((float)$total_equity, 5, '.', '') != $request->finance_src_loc_total_equity_1) {
                Session::flash('error', "Total equity amount should be equal to Total Equity (Million)");
                return redirect()->back()->withInput();
            }

            foreach ($request->loan_amount as $value) {
                $total_loan += floatval($value);
            }
            //checking loan amount
            if (number_format((float)$total_loan, 5, '.', '') != $request->finance_src_total_loan) {
                Session::flash('error', "Total loan amount should be equal to Total Loan (Million)");
                return redirect()->back()->withInput();
            }

            if (isset($request->get('multiToggleCheck')['investment_sources_of_finance']) === true) {
                $n_total_equity = 0; //total equity amount
                $n_total_loan = 0; //total loan amount

                foreach ($request->n_equity_amount as $value) {
                    $n_total_equity += floatval($value);
                }

                //checking equity amount
                if (number_format((float)$n_total_equity, 5, '.', '') != $request->n_finance_src_loc_total_equity_1) {
                    Session::flash('error', "Total equity amount should be equal to Total Equity (Million)");
                    return redirect()->back()->withInput();
                }
                $n_total_loan = 0;
                if(!empty($request->n_loan_amount[0])){
                    foreach ($request->n_loan_amount as $value) {
                        $n_total_loan += floatval($value);
                    }
                }
                //checking loan amount
                if (number_format((float)$n_total_loan, 5, '.', '') != $request->n_finance_src_total_loan) {
                    Session::flash('error', "Total loan amount should be equal to Total Loan (Million)");
                    return redirect()->back()->withInput();
                }
            }
        }
        
        try {
            DB::beginTransaction();

            if ($request->get('app_id')) {
                $appData = BidaRegistrationAmendment::find($app_id);
                $processData = ProcessList::where([
                    'process_type_id' => $this->process_type_id, 'ref_id' => $appData->id
                ])->first();
            } else {
                $appData = new BidaRegistrationAmendment();
                $processData = new ProcessList();
            }

            // Existing information
            $appData->is_approval_online = $request->get('is_approval_online');
            $appData->is_bra_approval_manually = $request->get('is_bra_approval_manually');
            if ($request->get('is_approval_online') == 'yes') {
                $appData->ref_app_tracking_no = trim($request->get('ref_app_tracking_no'));
                $appData->ref_app_approve_date = (!empty($request->get('ref_app_approve_date')) ? date('Y-m-d', strtotime($request->get('ref_app_approve_date'))) : null);

                if($request->get('is_bra_approval_manually') == 'yes'){
                    $appData->manually_approved_bra_no = trim($request->get('manually_approved_bra_no'));
                    $appData->manually_approved_bra_date = (!empty($request->get('manually_approved_bra_date')) ? date('Y-m-d', strtotime($request->get('manually_approved_bra_date'))) : null);

                    if ($request->hasFile('manually_bra_approval_copy')) {
                        $yearMonth = date("Y") . "/" . date("m") . "/";
                        $path = 'uploads/' . $yearMonth;
                        if (!file_exists($path)) {
                            mkdir($path, 0777, true);
                        }
                        $_file_path = $request->file('manually_bra_approval_copy');
                        $file_path = trim(uniqid('Manual_BRA_approval_copy' . '-', true) . $_file_path->getClientOriginalName());
                        $_file_path->move($path, $file_path);
                        $manually_bra_approval_copy = $yearMonth . $file_path;
                        $appData->manually_bra_approval_copy = $manually_bra_approval_copy;
                    }
                }
            } else {
                $appData->manually_approved_br_no = $request->get('manually_approved_br_no');
                $appData->manually_approved_br_date = (!empty($request->get('manually_approved_br_date')) ? date('Y-m-d', strtotime($request->get('manually_approved_br_date'))) : null);
            }

            $appData->company_name = !empty($request->get('company_name')) ? $request->get('company_name') : null;
            $appData->company_name_bn = !empty($request->get('company_name_bn')) ? $request->get('company_name_bn') : null;
            $appData->project_name = $request->get('project_name');

            if ($request->get('organization_status_id') == 3) { //3 = local
                $appData->country_of_origin_id = 18; // 18 = Bangladesh
            } else {
                $appData->country_of_origin_id = $request->get('country_of_origin_id');
            }

            $appData->organization_type_id = $request->get('organization_type_id');
            $appData->organization_status_id = $request->get('organization_status_id');
            $appData->ownership_status_id = $request->get('ownership_status_id');

            // Code of your business class
            if ($request->has('business_class_code')) {
                $business_class = $this->businessClassSingleList($request->get('business_class_code'));
                $existing_business_class = json_decode($business_class->getContent(), true);
                if (empty($existing_business_class['data'])) {
                    Session::flash('error', "Sorry! Your given Code of business class is not valid. Please enter the right one. [BRC-1017]");
                    DB::rollback();
                    return redirect()->back();
                }
                $appData->section_id = $existing_business_class['data'][0]['section_id'];
                $appData->division_id = $existing_business_class['data'][0]['division_id'];
                $appData->group_id = $existing_business_class['data'][0]['group_id'];
                $appData->class_id = $existing_business_class['data'][0]['id'];
                $appData->class_code = $existing_business_class['data'][0]['code'];

                $appData->sub_class_id = $request->get('sub_class_id') == '-1' ? 0 : $request->get('sub_class_id');
                $appData->other_sub_class_code = $request->get('sub_class_id') == '-1' ? $request->get('other_sub_class_code') : '';
                $appData->other_sub_class_name = $request->get('sub_class_id') == '-1' ? $request->get('other_sub_class_name') : '';
            }

            //eco information
            $appData->ceo_country_id = $request->get('ceo_country_id');
            $appData->ceo_dob = (!empty($request->get('ceo_dob')) ? date('Y-m-d', strtotime($request->get('ceo_dob'))) : null);
            $appData->ceo_passport_no = $request->get('ceo_passport_no');
            $appData->ceo_nid = $request->get('ceo_nid');
            $appData->ceo_designation = $request->get('ceo_designation');
            $appData->ceo_full_name = $request->get('ceo_full_name');
            $appData->ceo_district_id = $request->get('ceo_district_id');
            $appData->ceo_thana_id = $request->get('ceo_thana_id');
            $appData->ceo_city = $request->get('ceo_city');
            $appData->ceo_state = $request->get('ceo_state');
            $appData->ceo_post_code = $request->get('ceo_post_code');
            $appData->ceo_address = $request->get('ceo_address');
            $appData->ceo_telephone_no = $request->get('ceo_telephone_no');
            $appData->ceo_mobile_no = $request->get('ceo_mobile_no');
            $appData->ceo_fax_no = $request->get('ceo_fax_no');
            $appData->ceo_email = $request->get('ceo_email');
            $appData->ceo_father_name = $request->get('ceo_father_name');
            $appData->ceo_mother_name = $request->get('ceo_mother_name');
            $appData->ceo_spouse_name = $request->get('ceo_spouse_name');
            $appData->ceo_gender = !empty($request->get('ceo_gender')) ? $request->get('ceo_gender') : 'Not defined';

            //Existing office information
            $appData->office_division_id = $request->get('office_division_id');
            $appData->office_district_id = $request->get('office_district_id');
            $appData->office_thana_id = $request->get('office_thana_id');
            $appData->office_post_office = $request->get('office_post_office');
            $appData->office_post_code = $request->get('office_post_code');
            $appData->office_address = $request->get('office_address');
            $appData->office_telephone_no = $request->get('office_telephone_no');
            $appData->office_mobile_no = $request->get('office_mobile_no');
            $appData->office_fax_no = $request->get('office_fax_no');
            $appData->office_email = $request->get('office_email');

            //Existing factory information
            $appData->factory_district_id = $request->get('factory_district_id');
            $appData->factory_thana_id = $request->get('factory_thana_id');
            $appData->factory_post_office = $request->get('factory_post_office');
            $appData->factory_post_code = $request->get('factory_post_code');
            $appData->factory_address = $request->get('factory_address');
            $appData->factory_telephone_no = $request->get('factory_telephone_no');
            $appData->factory_mobile_no = $request->get('factory_mobile_no');
            $appData->factory_fax_no = $request->get('factory_fax_no');

            //Existing registration information
            $appData->project_status_id = $request->get('project_status_id');
            $appData->commercial_operation_date = (!empty($request->get('commercial_operation_date')) ? date('Y-m-d', strtotime($request->get('commercial_operation_date'))) : null);

            //Existing sales
            $appData->local_sales = $request->get('local_sales');
            $appData->foreign_sales = $request->get('foreign_sales');
            $appData->total_sales = $request->get('total_sales');

            //Existing manpower
            $appData->local_male = $request->get('local_male');
            $appData->local_female = $request->get('local_female');
            $appData->local_total = $request->get('local_total');
            $appData->foreign_male = $request->get('foreign_male');
            $appData->foreign_female = $request->get('foreign_female');
            $appData->foreign_total = $request->get('foreign_total');
            $appData->manpower_total = $request->get('manpower_total');
            $appData->manpower_local_ratio = $request->get('manpower_local_ratio');
            $appData->manpower_foreign_ratio = $request->get('manpower_foreign_ratio');

            //Existing Investment
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

            //Existing source of finance
            $appData->finance_src_loc_equity_1 = $request->get('finance_src_loc_equity_1');
            $appData->finance_src_foreign_equity_1 = $request->get('finance_src_foreign_equity_1');
            $appData->finance_src_loc_total_equity_1 = $request->get('finance_src_loc_total_equity_1');
            $appData->finance_src_loc_loan_1 = !empty($request->get('finance_src_loc_loan_1')) ? $request->get('finance_src_loc_loan_1') : 0;
            $appData->finance_src_foreign_loan_1 = $request->get('finance_src_foreign_loan_1');
            $appData->finance_src_total_loan = !empty($request->get('finance_src_total_loan')) ? $request->get('finance_src_total_loan') : 0;
            $appData->finance_src_loc_total_financing_m = $request->get('finance_src_loc_total_financing_m');
            $appData->finance_src_loc_total_financing_1 = $request->get('finance_src_loc_total_financing_1');

            //8. Public utility service
            $appData->public_land = isset($request->public_land) ? 1 : null;
            $appData->public_electricity = isset($request->public_electricity) ? 1 : null;
            $appData->public_gas = isset($request->public_gas) ? 1 : null;
            $appData->public_telephone = isset($request->public_telephone) ? 1 : null;
            $appData->public_road = isset($request->public_road) ? 1 : null;
            $appData->public_water = isset($request->public_water) ? 1 : null;
            $appData->public_drainage = isset($request->public_drainage) ? 1 : null;
            $appData->public_others = isset($request->public_others) ? 1 : null;

            $appData->trade_licence_num = $request->trade_licence_num;
            $appData->trade_licence_issuing_authority = $request->trade_licence_issuing_authority;

            $appData->tin_number = $request->tin_number;

            //11. Description of machinery and equipment
            $appData->machinery_local_qty = $request->machinery_local_qty;
            $appData->machinery_local_price_bdt = $request->machinery_local_price_bdt;
            $appData->imported_qty = $request->imported_qty;
            $appData->imported_qty_price_bdt = $request->imported_qty_price_bdt;
            $appData->total_machinery_price = $request->total_machinery_price;
            $appData->total_machinery_qty = $request->total_machinery_qty;

            $appData->local_description = $request->local_description;
            $appData->imported_description = $request->imported_description;

            $appData->major_remarks = $request->major_remarks;

            $appData->g_full_name = $request->get('g_full_name');
            $appData->g_designation = $request->get('g_designation');

            //signature upload
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
                $investor_signature_name = trim(sprintf("%s", uniqid('BIDA_BRA_', true))) . '_' . time() . '.jpeg';


                file_put_contents($path . $investor_signature_name, $base64ResizeImage);

                $appData->g_signature = $yearMonth . $investor_signature_name;
            } else {
                $appData->g_signature = $request->investor_signature_hidden;
            }

            //-------------End existing information --------------

            //---------------Proposed information----------------
            $appData->n_company_name = !empty($request->get('n_company_name')) ? $request->get('n_company_name') : null;
            $appData->n_company_name_bn = !empty($request->get('n_company_name_bn')) ? $request->get('n_company_name_bn') : null;
            $appData->n_project_name = !empty($request->get('n_project_name')) ? $request->get('n_project_name') : null;
            if ($request->get('n_organization_status_id') == 3) { // 3 = Local
                $appData->n_country_of_origin_id = 18; //18 = Bangladesh
            } else {
                $appData->n_country_of_origin_id = !empty($request->get('n_country_of_origin_id')) ? $request->get('n_country_of_origin_id') : null;
            }

            $appData->n_organization_type_id = !empty($request->get('n_organization_type_id')) ? $request->get('n_organization_type_id') : null;
            $appData->n_organization_status_id = !empty($request->get('n_organization_status_id')) ? $request->get('n_organization_status_id') : null;
            $appData->n_ownership_status_id = !empty($request->get('n_ownership_status_id')) ? $request->get('n_ownership_status_id') : null;

            // Code of your business class
            if ($request->has('n_business_class_code')) {
                $business_class = $this->businessClassSingleList($request->get('n_business_class_code'));
                $proposed_business_class = json_decode($business_class->getContent(), true);
                if (empty($proposed_business_class['data'])) {
                    Session::flash('error', "Sorry! Your given Code of business class is not valid. Please enter the right one. [BRAC-1017]");
                    DB::rollback();
                    return redirect()->back();
                }
                $appData->n_section_id = $proposed_business_class['data'][0]['section_id'];
                $appData->n_division_id = $proposed_business_class['data'][0]['division_id'];
                $appData->n_group_id = $proposed_business_class['data'][0]['group_id'];
                $appData->n_class_id = $proposed_business_class['data'][0]['id'];
                $appData->n_class_code = $proposed_business_class['data'][0]['code'];

                $appData->n_sub_class_id = $request->get('n_sub_class_id') == '-1' ? 0 : $request->get('n_sub_class_id');
                $appData->n_other_sub_class_code = $request->get('n_sub_class_id') == '-1' ? $request->get('n_other_sub_class_code') : '';
                $appData->n_other_sub_class_name = $request->get('n_sub_class_id') == '-1' ? $request->get('n_other_sub_class_name') : '';
            } else {
                $appData->n_section_id = '';
                $appData->n_division_id = '';
                $appData->n_group_id = '';
                $appData->n_class_id = '';
                $appData->n_class_code = '';
            
                $appData->n_sub_class_id = null;
                $appData->n_other_sub_class_code = '';
                $appData->n_other_sub_class_name = '';
            }

            //Proposed ceo information
            $appData->n_ceo_country_id = !empty($request->get('n_ceo_country_id')) ? $request->get('n_ceo_country_id') : null;
            $appData->n_ceo_dob = (!empty($request->get('n_ceo_dob')) ? date('Y-m-d', strtotime($request->get('n_ceo_dob'))) : null);
            $appData->n_ceo_passport_no = !empty($request->get('n_ceo_passport_no')) ? $request->get('n_ceo_passport_no') : null;
            $appData->n_ceo_nid = !empty($request->get('n_ceo_nid')) ? $request->get('n_ceo_nid') : null;
            $appData->n_ceo_designation = !empty($request->get('n_ceo_designation')) ? $request->get('n_ceo_designation') : null;
            $appData->n_ceo_full_name = !empty($request->get('n_ceo_full_name')) ? $request->get('n_ceo_full_name') : null;
            $appData->n_ceo_district_id = !empty($request->get('n_ceo_district_id')) ? $request->get('n_ceo_district_id') : null;
            $appData->n_ceo_thana_id = !empty($request->get('n_ceo_thana_id')) ? $request->get('n_ceo_thana_id') : null;
            $appData->n_ceo_city = !empty($request->get('n_ceo_city')) ? $request->get('n_ceo_city') : null;
            $appData->n_ceo_state = !empty($request->get('n_ceo_state')) ? $request->get('n_ceo_state') : null;
            $appData->n_ceo_post_code = !empty($request->get('n_ceo_post_code')) ? $request->get('n_ceo_post_code') : null;
            $appData->n_ceo_address = !empty($request->get('n_ceo_address')) ? $request->get('n_ceo_address') : null;
            $appData->n_ceo_telephone_no = !empty($request->get('n_ceo_telephone_no')) ? $request->get('n_ceo_telephone_no') : null;
            $appData->n_ceo_mobile_no = !empty($request->get('n_ceo_mobile_no')) ? $request->get('n_ceo_mobile_no') : null;
            $appData->n_ceo_fax_no = !empty($request->get('n_ceo_fax_no')) ? $request->get('n_ceo_fax_no') : null;
            $appData->n_ceo_email = !empty($request->get('n_ceo_email')) ? $request->get('n_ceo_email') : null;
            $appData->n_ceo_father_name = !empty($request->get('n_ceo_father_name')) ? $request->get('n_ceo_father_name') : null;
            $appData->n_ceo_mother_name = !empty($request->get('n_ceo_mother_name')) ? $request->get('n_ceo_mother_name') : null;
            $appData->n_ceo_spouse_name = !empty($request->get('n_ceo_spouse_name')) ? $request->get('n_ceo_spouse_name') : null;
            $appData->n_ceo_gender = !empty($request->get('n_ceo_gender')) ? $request->get('n_ceo_gender') : null;
            // dd($appData->n_ceo_country_id, $appData->n_ceo_passport_no, $appData->n_ceo_city, $appData->n_ceo_state);

            //proposed office information
            $appData->n_office_division_id = !empty($request->get('n_office_division_id')) ? $request->get('n_office_division_id') : null;
            $appData->n_office_district_id = !empty($request->get('n_office_district_id')) ? $request->get('n_office_district_id') : null;
            $appData->n_office_thana_id = !empty($request->get('n_office_thana_id')) ? $request->get('n_office_thana_id') : null;
            $appData->n_office_post_office = !empty($request->get('n_office_post_office')) ? $request->get('n_office_post_office') : null;
            $appData->n_office_post_code = !empty($request->get('n_office_post_code')) ? $request->get('n_office_post_code') : null;
            $appData->n_office_address = !empty($request->get('n_office_address')) ? $request->get('n_office_address') : null;
            $appData->n_office_telephone_no = !empty($request->get('n_office_telephone_no')) ? $request->get('n_office_telephone_no') : null;
            $appData->n_office_mobile_no = !empty($request->get('n_office_mobile_no')) ? $request->get('n_office_mobile_no') : null;
            $appData->n_office_fax_no = !empty($request->get('n_office_fax_no')) ? $request->get('n_office_fax_no') : null;
            $appData->n_office_email = !empty($request->get('n_office_email')) ? $request->get('n_office_email') : null;

            //proposed factory information
            $appData->n_factory_district_id = !empty($request->get('n_factory_district_id')) ? $request->get('n_factory_district_id') : null;
            $appData->n_factory_thana_id = !empty($request->get('n_factory_thana_id')) ? $request->get('n_factory_thana_id') : null;
            $appData->n_factory_post_office = !empty($request->get('n_factory_post_office')) ? $request->get('n_factory_post_office') : null;
            $appData->n_factory_post_code = !empty($request->get('n_factory_post_code')) ? $request->get('n_factory_post_code') : null;
            $appData->n_factory_address = !empty($request->get('n_factory_address')) ? $request->get('n_factory_address') : null;
            $appData->n_factory_telephone_no = !empty($request->get('n_factory_telephone_no')) ? $request->get('n_factory_telephone_no') : null;
            $appData->n_factory_mobile_no = !empty($request->get('n_factory_mobile_no')) ? $request->get('n_factory_mobile_no') : null;
            $appData->n_factory_fax_no = !empty($request->get('n_factory_fax_no')) ? $request->get('n_factory_fax_no') : null;

            //proposed registration information
            $appData->n_project_status_id = !empty($request->get('n_project_status_id')) ? $request->get('n_project_status_id') : null;
            $appData->n_commercial_operation_date = !empty($request->get('n_commercial_operation_date')) ? date('Y-m-d', strtotime($request->get('n_commercial_operation_date'))) : null;

            //proposed sales
            $appData->n_local_sales = !empty($request->get('n_local_sales')) ? $request->get('n_local_sales') : null;
            $appData->n_foreign_sales = !empty($request->get('n_foreign_sales')) ? $request->get('n_foreign_sales') : null;
            $appData->n_total_sales = !empty($request->get('n_total_sales')) ? $request->get('n_total_sales') : null;

            //proposed manpower
            $appData->n_local_male = !empty($request->get('n_local_male')) ? $request->get('n_local_male') : null;
            $appData->n_local_female = !empty($request->get('n_local_female')) ? $request->get('n_local_female') : null;
            $appData->n_local_total = !empty($request->get('n_local_total')) ? $request->get('n_local_total') : null;
            $appData->n_foreign_male = !empty($request->get('n_foreign_male')) ? $request->get('n_foreign_male') : null;
            $appData->n_foreign_female = !empty($request->get('n_foreign_female')) ? $request->get('n_foreign_female') : null;
            $appData->n_foreign_total = !empty($request->get('n_foreign_total')) ? $request->get('n_foreign_total') : null;
            $appData->n_manpower_total = !empty($request->get('n_manpower_total')) ? $request->get('n_manpower_total') : null;
            $appData->n_manpower_local_ratio = !empty($request->get('n_manpower_local_ratio')) ? $request->get('n_manpower_local_ratio') : null;
            $appData->n_manpower_foreign_ratio = !empty($request->get('n_manpower_foreign_ratio')) ? $request->get('n_manpower_foreign_ratio') : null;

            //proposed investment
            $appData->n_local_land_ivst = !empty($request->get('n_local_land_ivst')) ? (float)$request->get('n_local_land_ivst') : null;
            $appData->n_local_land_ivst_ccy = !empty($request->get('n_local_land_ivst_ccy')) ? $request->get('n_local_land_ivst_ccy') : null;
            $appData->n_local_machinery_ivst = !empty($request->get('n_local_machinery_ivst')) ? (float)$request->get('n_local_machinery_ivst') : null;
            $appData->n_local_machinery_ivst_ccy = !empty($request->get('n_local_machinery_ivst_ccy')) ? $request->get('n_local_machinery_ivst_ccy') : null;
            $appData->n_local_building_ivst = !empty($request->get('n_local_building_ivst')) ? (float)$request->get('n_local_building_ivst') : null;
            $appData->n_local_building_ivst_ccy = !empty($request->get('n_local_building_ivst_ccy')) ? $request->get('n_local_building_ivst_ccy') : null;
            $appData->n_local_others_ivst = !empty($request->get('n_local_others_ivst')) ? (float)$request->get('n_local_others_ivst') : null;
            $appData->n_local_others_ivst_ccy = !empty($request->get('n_local_others_ivst_ccy')) ? $request->get('n_local_others_ivst_ccy') : null;
            $appData->n_local_wc_ivst = !empty($request->get('n_local_wc_ivst')) ? (float)$request->get('n_local_wc_ivst') : null;
            $appData->n_local_wc_ivst_ccy = !empty($request->get('n_local_wc_ivst_ccy')) ? $request->get('n_local_wc_ivst_ccy') : null;
            $appData->n_total_fixed_ivst = !empty($request->get('n_total_fixed_ivst')) ? $request->get('n_total_fixed_ivst') : null;
            $appData->n_total_fixed_ivst_million = !empty($request->get('n_total_fixed_ivst_million')) ? $request->get('n_total_fixed_ivst_million') : null;
            $appData->n_usd_exchange_rate = !empty($request->get('n_usd_exchange_rate')) ? $request->get('n_usd_exchange_rate') : null;
            $appData->n_total_fee = !empty($request->get('n_total_fee')) ? $request->get('n_total_fee') : null;

            //proposed source of finance
            $appData->n_finance_src_loc_equity_1 = !empty($request->get('n_finance_src_loc_equity_1')) ? $request->get('n_finance_src_loc_equity_1') : null;
            $appData->n_finance_src_foreign_equity_1 = !empty($request->get('n_finance_src_foreign_equity_1')) ? $request->get('n_finance_src_foreign_equity_1') : null;
            $appData->n_finance_src_loc_total_equity_1 = !empty($request->get('n_finance_src_loc_total_equity_1')) ? $request->get('n_finance_src_loc_total_equity_1') : null;
            $appData->n_finance_src_loc_loan_1 = !empty($request->get('n_finance_src_loc_loan_1')) ? $request->get('n_finance_src_loc_loan_1') : null;
            $appData->n_finance_src_foreign_loan_1 = !empty($request->get('n_finance_src_foreign_loan_1')) ? $request->get('n_finance_src_foreign_loan_1') : null;
            $appData->n_finance_src_total_loan = !empty($request->get('n_finance_src_total_loan')) ? $request->get('n_finance_src_total_loan') : null;
            $appData->n_finance_src_loc_total_financing_m = !empty($request->get('n_finance_src_loc_total_financing_m')) ? $request->get('n_finance_src_loc_total_financing_m') : null;
            $appData->n_finance_src_loc_total_financing_1 = !empty($request->get('n_finance_src_loc_total_financing_1')) ? $request->get('n_finance_src_loc_total_financing_1') : null;

            //8. Public utility service
            $appData->n_public_land = isset($request->n_public_land) ? 1 : null;
            $appData->n_public_electricity = isset($request->n_public_electricity) ? 1 : null;
            $appData->n_public_gas = isset($request->n_public_gas) ? 1 : null;
            $appData->n_public_telephone = isset($request->n_public_telephone) ? 1 : null;
            $appData->n_public_road = isset($request->n_public_road) ? 1 : null;
            $appData->n_public_water = isset($request->n_public_water) ? 1 : null;
            $appData->n_public_drainage = isset($request->n_public_drainage) ? 1 : null;
            $appData->n_public_others = isset($request->n_public_others) ? 1 : null;

            $appData->n_trade_licence_num = !empty($request->get('n_trade_licence_num')) ? $request->get('n_trade_licence_num') : null;
            $appData->n_trade_licence_issuing_authority = !empty($request->get('n_trade_licence_issuing_authority')) ? $request->get('n_trade_licence_issuing_authority') : null;

            $appData->n_tin_number = !empty($request->get('n_tin_number')) ? $request->get('n_tin_number') : null;

            //11. Description of machinery and equipment
            $appData->n_machinery_local_qty = !empty($request->get('n_machinery_local_qty')) ? $request->get('n_machinery_local_qty') : null;
            $appData->n_machinery_local_price_bdt = !empty($request->get('n_machinery_local_price_bdt')) ? $request->get('n_machinery_local_price_bdt') : null;
            $appData->n_imported_qty = !empty($request->get('n_imported_qty')) ? $request->get('n_imported_qty') : null;
            $appData->n_imported_qty_price_bdt = !empty($request->get('n_imported_qty_price_bdt')) ? $request->get('n_imported_qty_price_bdt') : null;
            $appData->n_total_machinery_price = !empty($request->get('n_total_machinery_price')) ? $request->get('n_total_machinery_price') : null;
            $appData->n_total_machinery_qty = !empty($request->get('n_total_machinery_qty')) ? $request->get('n_total_machinery_qty') : null;

            $appData->n_local_description = !empty($request->get('n_local_description')) ? $request->get('n_local_description') : null;
            $appData->n_imported_description = !empty($request->get('n_imported_description')) ? $request->get('n_imported_description') : null;

            $appData->n_g_full_name = !empty($request->get('n_g_full_name')) ? $request->get('n_g_full_name') : null;
            $appData->n_g_designation = !empty($request->get('n_g_designation')) ? $request->get('n_g_designation') : null;

            //signature upload
//            if ($request->hasFile('n_g_signature')) {
//                $yearMonth = date("Y") . "/" . date("m") . "/";
//                $path = 'users/upload/' . $yearMonth;
//                $n_signature = $request->file('n_g_signature');
//                $signatureFile = trim(uniqid('BIDA_BRA-' . time() . '-', true) . $n_signature->getClientOriginalName());
//                if (!file_exists($path)) {
//                    mkdir($path, 0777, true);
//                }
//                $n_signature->move($path, $signatureFile);
//                $appData->n_g_signature = $yearMonth . $signatureFile;
//            }

            if (!empty($request->n_investor_signature_base64)) {
                $yearMonth = date("Y") . "/" . date("m") . "/";
                $path = 'uploads/' . $yearMonth;
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                $splited = explode(',', substr($request->get('n_investor_signature_base64'), 5), 2);
                $imageData = $splited[1];
                $base64ResizeImageEncode = base64_encode(ImageProcessing::resizeBase64Image($imageData, 300, 80));
                $base64ResizeImage = base64_decode($base64ResizeImageEncode);
                $n_investor_signature_name = trim(uniqid('BRA_', true) . '.' . 'jpg');

                file_put_contents($path . $n_investor_signature_name, $base64ResizeImage);

                $appData->n_g_signature = $yearMonth . $n_investor_signature_name;
            } else {
                $appData->n_g_signature = !empty($request->get('n_investor_signature_hidden')) ? $request->get('n_investor_signature_hidden') : null;
                // $appData->n_g_signature = $request->n_investor_signature_hidden;
            }

            $appData->accept_terms = (!empty($request->get('accept_terms')) ? 1 : 0);
            if ($request->get('actionBtn') == "draft") {
                $appData->is_archive = 1;
            } else {
                $appData->is_archive = 0;
            }

            //set process list table data for application status and desk with condition basis
            if ($request->get('actionBtn') == "draft") {
                $processData->status_id = -1; // -1 = Draft
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

            //$amendmentJson = $this->generateJsonData($request);
            // store JSON data

//            $appData->data = $amendmentJson['json'];
//            $appData->change_fields = $amendmentJson['change_fields'];
//            $appData->change_old_value = $amendmentJson['change_old_value'];
//            $appData->change_new_value = $amendmentJson['change_new_value'];
            $appData->save();

            // Annual production capacity
            if (!empty($appData->id) && count(Session::get('brAnnualProductionCapacity')) > 0) {

                foreach (Session::get('brAnnualProductionCapacity') as $annualProductionCapacity) {
                    $annualCapacity = new AnnualProductionCapacityAmendment();
                    $annualCapacity->app_id = $appData->id;
                    $annualCapacity->process_type_id = $this->process_type_id;
                    $annualCapacity->product_name = $annualProductionCapacity->product_name;
                    $annualCapacity->quantity_unit = $annualProductionCapacity->quantity_unit;
                    $annualCapacity->quantity = $annualProductionCapacity->quantity;
                    $annualCapacity->price_usd = $annualProductionCapacity->price_usd;
                    $annualCapacity->price_taka = $annualProductionCapacity->price_taka;
                    $annualCapacity->save();
                }
            }

            // Country wise source of finance
            if (!empty($appData->id) && (!empty(trim($request->get('country_id')[0])) || !empty(trim($request->get('equity_amount')[0])) || !empty(trim($request->get('loan_amount')[0])) ||
                    !empty(trim($request->get('n_country_id')[0])) || !empty(trim($request->get('n_equity_amount')[0])) || !empty(trim($request->get('n_loan_amount')[0])))) {
                $source_of_finance_ids = [];
                foreach ($request->country_id as $key => $value) {
                    $source_of_finance_id = $request->get('source_of_finance_id')[$key];
                    $source_of_finance = SourceOfFinanceAmendment::findOrNew($source_of_finance_id);
                    $source_of_finance->app_id = $appData->id;
                    $source_of_finance->process_type_id = $this->process_type_id;
                    $source_of_finance->country_id = $request->get('country_id')[$key];
                    $source_of_finance->equity_amount = $request->get('equity_amount')[$key];
                    $source_of_finance->loan_amount = $request->get('loan_amount')[$key];
                    $source_of_finance->n_country_id = !empty($request->get('n_country_id')[$key]) ? $request->get('n_country_id')[$key] : null;
                    $source_of_finance->n_equity_amount = !empty($request->get('n_equity_amount')[$key]) ? $request->get('n_equity_amount')[$key] : null;
                    $source_of_finance->n_loan_amount = !empty($request->get('n_loan_amount')[$key]) ? $request->get('n_loan_amount')[$key] : null;
                    $source_of_finance->save();
                    $source_of_finance_ids[] = $source_of_finance->id;
                }
                if (count($source_of_finance_ids) > 0) {
                    SourceOfFinanceAmendment::where('app_id', $appData->id)->where('process_type_id', $this->process_type_id)
                        ->whereNotIn('id', $source_of_finance_ids)->delete();
                }
            }

            // List of Directors
            if (!empty($appData->id) && count(Session::get('brListOfDirectors')) > 0) {

                foreach (Session::get('brListOfDirectors') as $director) {
                    $listOfDirector = new ListOfDirectorsAmendment();
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

            // List of machinery to be imported
            if (!empty($appData->id) && count(Session::get('brListOfMachineryImported')) > 0) {
                foreach (Session::get('brListOfMachineryImported') as $machineryImported) {
                    $listOfMachineryImported = new ListOfMachineryImportedAmendment();
                    $listOfMachineryImported->ref_master_id = isset($machineryImported->ref_master_id) ? $machineryImported->ref_master_id : 0;
                    $listOfMachineryImported->app_id = $appData->id;
                    $listOfMachineryImported->process_type_id = $this->process_type_id;
                    $listOfMachineryImported->l_machinery_imported_name = $machineryImported->l_machinery_imported_name;
                    $listOfMachineryImported->l_machinery_imported_qty = $machineryImported->l_machinery_imported_qty;
                    $listOfMachineryImported->l_machinery_imported_unit_price = $machineryImported->l_machinery_imported_unit_price;
                    $listOfMachineryImported->l_machinery_imported_total_value = $machineryImported->l_machinery_imported_total_value;
                    $listOfMachineryImported->total_million = $machineryImported->l_machinery_imported_total_value;
                    $listOfMachineryImported->save();
                }
            }

            // List of machinery locally purchase/ procure
            if (!empty($appData->id) && count(Session::get('brListOfMachineryLocal')) > 0) {
                foreach (Session::get('brListOfMachineryLocal') as $machineryLocal) {
                    $listOfMachineryLocal = new ListOfMachineryLocalAmendment();
                    $listOfMachineryLocal->app_id = $appData->id;
                    $listOfMachineryLocal->process_type_id = $this->process_type_id;
                    $listOfMachineryLocal->l_machinery_local_name = $machineryLocal->l_machinery_local_name;
                    $listOfMachineryLocal->l_machinery_local_qty = $machineryLocal->l_machinery_local_qty;
                    $listOfMachineryLocal->l_machinery_local_unit_price = $machineryLocal->l_machinery_local_unit_price;
                    $listOfMachineryLocal->l_machinery_local_total_value = $machineryLocal->l_machinery_local_total_value;
                    $listOfMachineryLocal->total_million = $machineryLocal->l_machinery_local_total_value;
                    $listOfMachineryLocal->save();
                }
            }

            // Existing BRA ref/memo
            if ($appData->id && !empty(trim($request->get('bra_memo_no')[0]))) {
                $existing_bra_ids = [];
                foreach ($request->bra_memo_no as $key => $value) {
                    $existing_bra_id = $request->get('existing_bra_id')[$key];
                    $existing_bra = ExistingBRA::findOrNew($existing_bra_id);
                    $existing_bra->app_id = $appData->id;
                    $existing_bra->bra_memo_no = $request->get('bra_memo_no')[$key];
                    $existing_bra->bra_approved_date = !empty($request->get('bra_approved_date')[$key]) ? date('Y-m-d', strtotime($request->get('bra_approved_date')[$key])) : null;
                    $existing_bra->save();
                    $existing_bra_ids[] = $existing_bra->id;
                }

                if (count($existing_bra_ids) > 0) {
                    ExistingBRA::where('app_id', $appData->id)->whereNotIn('id', $existing_bra_ids)->delete();
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

            $jsonData['Applied by'] = CommonFunction::getUserFullName();
            $jsonData['Email'] = Auth::user()->user_email;
            $jsonData['Mobile'] = Auth::user()->user_phone;
            $processData['json_object'] = json_encode($jsonData);
            $processData->save();

            //  Required Documents for attachment
            if (($request->get('organization_status_id') || $request->get('n_organization_status_id')) && ($request->get('ownership_status_id') || $request->get('n_ownership_status_id'))) {

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
                }
            }
            /* End file uploading */

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

                $amount_array = $this->fixedUnfixedAmountsForPayment($payment_config);

                $paymentInfo->pay_amount = $amount_array['total_fixed_unfixed_amount'] + $payment_config->amount;
                $paymentInfo->vat_on_pay_amount = $amount_array['total_vat_on_pay_amount'];
                $paymentInfo->total_amount = ($paymentInfo->pay_amount + $paymentInfo->vat_on_pay_amount);
                $paymentInfo->contact_name = $request->get('sfp_contact_name');
                $paymentInfo->contact_email = $request->get('sfp_contact_email');
                $paymentInfo->contact_no = $request->get('sfp_contact_phone');
                $paymentInfo->address = $request->get('sfp_contact_address');
                $paymentInfo->sl_no = 1; // Always 1
                $paymentInfo->save();

                $appData->sf_payment_id = $paymentInfo->id;
                $appData->save();

                // Payment Details By Stakeholders
                foreach ($stakeDistribution as $distribution) {
                    $paymentDetails = PaymentDetails::firstOrNew([
                        'sp_payment_id' => $paymentInfo->id, 'payment_distribution_id' => $distribution->id
                    ]);
                    $paymentDetails->sp_payment_id = $paymentInfo->id;
                    $paymentDetails->payment_distribution_id = $distribution->id;
                    if ($distribution->fix_status == 1) {
                        $paymentDetails->pay_amount = $distribution->pay_amount;
                    } else {
                        $paymentDetails->pay_amount = $amount_array['amounts'][$distribution->distribution_type];
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

            // Clean session data
            Session::forget('brInfo');
            Session::forget('brAnnualProductionCapacity');
            Session::forget('brListOfDirectors');
            Session::forget('brListOfMachineryImported');
            Session::forget('brListOfMachineryLocal');
            Session::forget('sourceOfFinance');

            /*
            * if action is submitted and application status is equal to draft
            * and have payment configuration then, generate a tracking number
            * and go to payment initiator function.
            */
            if ($request->get('actionBtn') == 'Submit' && $processData->status_id == -1) {
                if(empty($processData->tracking_no)) {
                    $prefix = 'BRA-' . date("dMY") . '-';
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
                Session::flash('error', 'Failed due to Application Status Conflict. Please try again later! [BRAC-1023]');
            }

            DB::commit();
            return redirect('bida-registration-amendment/list/' . Encryption::encodeId($this->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('BRAAppStore: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [BRAC-1011]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage(), $e->getLine(), $e->getFile()) . "[BRAC-1011]");
            return redirect()->back()->withInput();
        }

    }

    /*
     * BRA application edit
     * @request $request
     * @param $app_id
     * @param $openMode edit
     */
    public function applicationEdit($applicationId, $openMode = "", Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.';
        }
        // it's enough to check ACL for view mode only
        if (!ACL::getAccsessRight($this->aclName, '-E-')) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>You have no access right! Contact with system admin for more information. [BRAC-9741]</h4>"
            ]);
        }

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
                'html' => "<h4 class='custom-err-msg'> Payment Configuration not found ![BRAC-10102]</h4>"
            ]);
        }

        try {
            $applicationId = Encryption::decodeId($applicationId);
            $process_type_id = $this->process_type_id;
            $appInfo = ProcessList::leftJoin('bra_apps as apps', 'apps.id', '=', 'process_list.ref_id')
                ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
                ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                    $join->on('ps.id', '=', 'process_list.status_id');
                    $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
                })
                ->leftJoin('project_status', 'project_status.id', '=', 'apps.project_status_id')
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
                    'process_list.approval_center_id',
                    'user_desk.desk_name',
                    'ps.status_name',
                    'ps.color',
                    'project_status.name as project_status_name',
                    'apps.id as appid',
                    'apps.*',

                    'sfp.contact_name as sfp_contact_name',
                    'sfp.contact_email as sfp_contact_email',
                    'sfp.contact_no as sfp_contact_phone',
                    'sfp.address as sfp_contact_address',
                    'sfp.pay_amount as sfp_pay_amount',
                    'sfp.vat_on_pay_amount as sfp_vat_on_pay_amount',
//                    'sfp.transaction_charge_amount as sfp_transaction_charge_amount',
//                    'sfp.vat_on_transaction_charge as sfp_vat_on_transaction_charge',
                    'sfp.total_amount as sfp_total_amount',
                    'sfp.payment_status as sfp_payment_status',
//                    'sfp.pay_mode as pay_mode',
//                    'sfp.pay_mode_code as pay_mode_code',
                ]);

            $eaOrganizationType = ['' => 'Select one'] + EA_OrganizationType::where('is_archive', 0)->whereIn('type', [1, 3])->where('status', 1)->orderBy('name')->lists('name', 'id')->all();
            $eaOrganizationStatus = ['' => 'Select one'] + EA_OrganizationStatus::where('is_archive', 0)->where('status', 1)->orderBy('name')->lists('name', 'id')->all();
            $eaOwnershipStatus = ['' => 'Select one'] + EA_OwnershipStatus::where('is_archive', 0)->where('status', 1)->orderBy('name')->lists('name', 'id')->all();
            $countries = ['' => 'Select One'] + Countries::where('country_status', 'Yes')->orderBy('nicename', 'asc')->lists('nicename', 'id')->all();
            $projectStatusList = ['' => 'Select One'] + ProjectStatus::orderBy('name')->where('is_archive', 0)->where('status', 1)->lists('name', 'id')->all();
            $productUnit = ['0' => 'Select one'] + ProductUnit::where('status', 1)->where('is_archive', 0)->orderBy('name')->lists('name', 'id')->all();
            $currencyBDT = Currencies::orderBy('code')->whereIn('code', ['BDT'])->where('is_archive', 0)->where('is_active', 1)->lists('code', 'id');
            $nationality = ['' => 'Select one'] + Countries::where('country_status', 'Yes')->where('nationality', '!=', '')->orderby('nationality', 'asc')->lists('nationality', 'id')->all();
            $divisions = ['' => 'Select One'] + AreaInfo::where('area_type', 1)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $districts = ['' => 'Select One'] + AreaInfo::where('area_type', 2)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $thana = ['' => 'Select One'] + AreaInfo::orderby('area_nm')->where('area_type', 3)->lists('area_nm', 'area_id')->all();

            $annualProductionCapacity = AnnualProductionCapacityAmendment::where('app_id', $applicationId)->where('process_type_id', $this->process_type_id)->count();

            $sourceOfFinance = SourceOfFinanceAmendment::where('app_id', $applicationId)->where('process_type_id', $this->process_type_id)->get();
            $listOfDirectors = ListOfDirectorsAmendment::where('app_id', $applicationId)->where('process_type_id', $process_type_id)->count();
            $ListOfMachineryImported = ListOfMachineryImportedAmendment::where('app_id', $applicationId)->where('process_type_id', $process_type_id)->count();
            $listOfMachineryLocal = ListOfMachineryLocalAmendment::where('app_id', $applicationId)->where('process_type_id', $process_type_id)->count();

            $totalFee = DB::table('pay_order_amount_setup')->where('process_type_id', 102)->get([
                'min_amount_bdt', 'max_amount_bdt', 'p_o_amount_bdt'
            ]);

            $existing_bra = ExistingBRA::where('app_id', $applicationId)->get();

            $approvalCenterList = DivisionalOffice::where('status', 1)
                ->where('is_archive', 0)
                ->orderBy('id')
                ->get([
                    'id', 'office_name', 'office_address'
                ]);

            $public_html = strval(view("BidaRegistrationAmendment::application-form-edit", compact('eaOrganizationStatus', 'countries', 'projectStatusList', 'productUnit', 'currencyBDT', 'nationality', 'existing_bra',
                'payment_config', 'divisions', 'districts', 'thana', 'appInfo', 'annualProductionCapacity', 'sourceOfFinance', 'ListOfMachineryImported', 'listOfDirectors', 'listOfMachineryLocal', 'totalFee', 'eaOrganizationType', 'eaOwnershipStatus',
                'approvalCenterList')));
            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (\Exception $e) {
            Log::error('BRAViewEditForm : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [BRAC-11010]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . "[BRAC-11010]" . "</h4>"
            ]);
        }
    }

    /*
     * BRA application view
     * @request $request
     * @param $app_id
     * @param $openMode view
     */
    public function applicationView($app_id, Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.';
        }

        // it's enough to check ACL for view mode only
        if (!ACL::getAccsessRight($this->aclName, '-V-')) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>You have no access right! Contact with system admin for more information. [BRAC-9740]</h4>"
            ]);
        }

        try {
            $applicationId = Encryption::decodeId($app_id);
            $viewMode = 'on';
            $appInfo = ProcessList::leftJoin('bra_apps as apps', 'apps.id', '=', 'process_list.ref_id')
                ->leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
                ->leftJoin('divisional_office', 'divisional_office.id', '=', 'process_list.approval_center_id')
                ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
                ->leftJoin('process_status as ps', function ($join) {
                    $join->on('ps.id', '=', 'process_list.status_id');
                    $join->on('ps.process_type_id', '=', DB::raw($this->process_type_id));
                })

                // Reference application
                ->leftJoin('process_list as ref_process', 'ref_process.tracking_no', '=', 'apps.ref_app_tracking_no')
                ->leftJoin('process_type as ref_process_type', 'ref_process_type.id', '=', 'ref_process.process_type_id')

                ->leftJoin('project_status', 'project_status.id', '=', 'apps.project_status_id')
                ->leftJoin('sp_payment as sfp', 'sfp.id', '=', 'apps.sf_payment_id')
                ->leftJoin('sp_payment as gfp', 'gfp.id', '=', 'apps.gf_payment_id')
                ->where('process_list.ref_id', $applicationId)
                ->where('process_list.process_type_id', $this->process_type_id)
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
                    'project_status.name as project_status_name',
                    'apps.*',

                    'divisional_office.office_name as divisional_office_name',
                    'divisional_office.office_address as divisional_office_address',

                    'process_type.form_url',

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

                    // Reference application
                    'ref_process.ref_id as ref_application_ref_id',
                    'ref_process.process_type_id as ref_application_process_type_id',
                    'ref_process_type.type_key as ref_process_type_key',
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
                        'html' => "<h4 class='custom-err-msg'>Payment Configuration not found ![BRAC-10103]</h4>"
                    ]);
                }

                // Get payment distributor list
                $stakeDistribution = PaymentDistribution::where('sp_pay_config_id', $payment_config->id)
                    ->where('status', 1)
                    ->where('is_archive', 0)
                    ->get(['id', 'stakeholder_ac_no', 'pay_amount', 'fix_status', 'purpose', 'purpose_sbl', 'distribution_type']);
                if ($stakeDistribution->isEmpty()) {
                    DB::rollback();
                    Session::flash('error', "Stakeholder not found [BRAC-101]");
                    return redirect()->back()->withInput();
                }

                $relevant_info_array = [
                    'stakeholder_distribution' => $stakeDistribution,
                ];

                $amount_array = $this->fixedUnfixedAmountsForPayment($payment_config, $relevant_info_array);
                $payment_config->amount = $amount_array['total_fixed_unfixed_amount'];
                $vatFreeAllowed = BasicInformationController::isAllowedWithOutVAT($this->process_type_id);
                $payment_config->vat_on_pay_amount = ($vatFreeAllowed === true) ? 0 : $amount_array['total_vat_on_pay_amount'];
            }

            $sourceOfFinance = SourceOfFinanceAmendment::where('app_id', $applicationId)->where('process_type_id', $this->process_type_id)->get();
            $eaOrganizationType = ['' => 'Select one'] + EA_OrganizationType::where('is_archive', 0)->whereIn('type', [1, 3])->where('status', 1)->orderBy('name')->lists('name', 'id')->all();
            $eaOrganizationStatus = ['' => 'Select one'] + EA_OrganizationStatus::where('is_archive', 0)->where('status', 1)->orderBy('name')->lists('name', 'id')->all();
            $eaOwnershipStatus = ['' => 'Select one'] + EA_OwnershipStatus::where('is_archive', 0)->where('status', 1)->orderBy('name')->lists('name', 'id')->all();
            $countriesWithoutBD = ['' => 'Select One'] + Countries::where('country_status', 'Yes')->where('id', '!=', '18')->orderBy('nicename', 'asc')->lists('nicename', 'id')->all();
            $countries = ['' => 'Select One'] + Countries::where('country_status', 'Yes')->orderBy('nicename', 'asc')->lists('nicename', 'id')->all();
            $projectStatusList = ['' => 'Select One'] + ProjectStatus::orderBy('name')->where('is_archive', 0)->where('status', 1)->lists('name', 'id')->all();
            $productUnit = ['0' => ''] + ProductUnit::where('status', 1)->where('is_archive', 0)->orderBy('name')->lists('name', 'id')->all();
            $currencyBDT = Currencies::orderBy('code')->whereIn('code', ['BDT'])->where('is_archive', 0)->where('is_active', 1)->lists('code', 'id');
            $nationality = ['' => 'Select one'] + Countries::where('country_status', 'Yes')->where('nationality', '!=', '')->orderby('nationality', 'asc')->lists('nationality', 'id')->all();
            $divisions = ['' => 'Select One'] + AreaInfo::where('area_type', 1)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $districts = ['' => 'Select One'] + AreaInfo::where('area_type', 2)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $thana = ['' => 'Select One'] + AreaInfo::orderby('area_nm')->where('area_type', 3)->lists('area_nm', 'area_id')->all();

            //Business Sector for existing information
            $existingQuery = DB::select("
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

            $busness_code = json_decode(json_encode($existingQuery), true);
            $sub_class = BusinessClass::select('id', DB::raw("CONCAT(code, ' - ', name) as name"))->where('id', $appInfo->sub_class_id)->first();
            //end business sector

            //Business Sector for proposed information
            $proposedQuery = DB::select("
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
            where sec_class.code = '$appInfo->n_class_code' limit 1;
          ");

            $n_busness_code = json_decode(json_encode($proposedQuery), true);
            $n_sub_class = BusinessClass::select('id', DB::raw("CONCAT(code, ' - ', name) as name"))->where('id', $appInfo->n_sub_class_id)->first();
            //end business sector

            $attachment_key_prev = "bra_";
            if ($appInfo->n_organization_status_id) {
                if ($appInfo->n_organization_status_id == 3) {
                    $attachment_key_prev .= "local";
                } else if ($appInfo->n_organization_status_id == 2) {
                    $attachment_key_prev .= "foreign";
                } else {
                    $attachment_key_prev .= "joint_venture";
                }

            } else {
                if ($appInfo->organization_status_id == 3) {
                    $attachment_key_prev .= "local";
                } else if ($appInfo->organization_status_id == 2) {
                    $attachment_key_prev .= "foreign";
                } else {
                    $attachment_key_prev .= "joint_venture";
                }
            }

            $organization_status_id = !empty($appInfo->n_organization_status_id) ? $appInfo->n_organization_status_id : $appInfo->organization_status_id;

            $ownership_status_id = !empty($appInfo->n_ownership_status_id) ? $appInfo->n_ownership_status_id : $appInfo->ownership_status_id;

            $attachment_key = CommonFunction::generateAttachmentKey($organization_status_id, $ownership_status_id, "bra");

            $document = AppDocuments::leftJoin('attachment_list', 'attachment_list.id', '=', 'app_documents.doc_info_id')
                ->leftJoin('attachment_type', 'attachment_type.id', '=', 'attachment_list.attachment_type_id')
                ->where(function ($query) use ($attachment_key, $attachment_key_prev) {
                    $query->where('attachment_type.key', $attachment_key)
                        ->orWhere('attachment_type.key', $attachment_key_prev);
                })
                ->where('app_documents.ref_id', $applicationId)
                ->where('app_documents.process_type_id', $this->process_type_id)
                ->where('app_documents.doc_section', 'master')
                ->get([
                    'attachment_list.id',
                    'attachment_list.doc_priority',
                    'attachment_list.additional_field',
                    'app_documents.id as document_id',
                    'app_documents.doc_file_path as doc_file_path',
                    'app_documents.doc_name'
                ]);

            $list_of_directors = ListOfDirectorsAmendment::where('app_id', $applicationId)->where('process_type_id', $this->process_type_id)->count();
            $importedMachineryData = ListOfMachineryImportedAmendment::where('app_id', $applicationId)->where('process_type_id', $this->process_type_id)->count();
            $localMachineryData = ListOfMachineryLocalAmendment::where('app_id', $applicationId)->where('process_type_id', $this->process_type_id)->count();
            $annualProductionCapacity = AnnualProductionCapacityAmendment::where('app_id', $applicationId)->where('process_type_id', $this->process_type_id)->count();

            $existing_bra = ExistingBRA::where('app_id', $applicationId)->get();

            $data['ref_app_url'] = '#';
            if (!empty($appInfo->ref_app_tracking_no)) {
                $data['ref_app_url'] = url('process/'.$appInfo->ref_process_type_key.'/view-app/'.Encryption::encodeId($appInfo->ref_application_ref_id) . '/' . Encryption::encodeId($appInfo->ref_application_process_type_id));
            }

            $public_html = strval(view("BidaRegistrationAmendment::application-form-view", compact('eaOrganizationStatus', 'countriesWithoutBD', 'countries', 'projectStatusList', 'productUnit', 'currencyBDT', 'countries', 'nationality',
                'payment_config', 'divisions', 'districts', 'thana', 'appInfo', 'annualProductionCapacity', 'sourceOfFinance', 'viewMode', 'existing_bra',
                'busness_code', 'sub_class', 'n_busness_code', 'n_sub_class', 'document', 'eaOrganizationType', 'eaOwnershipStatus', 'list_of_directors', 'importedMachineryData', 'localMachineryData', 'data')));
            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (\Exception $e) {
            Log::error('BRAViewForm : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [BRAC-1015]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . "[BRAC-1015]" . "</h4>"
            ]);
        }

    }

    public function preview()
    {
        return view("BidaRegistrationAmendment::preview");
    }

    public function showBusinessClassModal(Request $request)
    {
        $fieldType = $request->get('field_type');
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.';
        }
        return view("BidaRegistrationAmendment::business-class-modal", compact('fieldType'));
    }

    public function getBusinessClassList(Request $request)
    {
        $field_type = $request->get('type');
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
            ->addColumn('action', function ($data) use ($field_type) {
                if ($data) {
                    return '<a href="#" data-type="' . $field_type . '"  data-subclass="' . $data->code . '" class="btn btn-xs btn-primary" onclick="selectBusinessClass(this)">Select</a>';
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

    public function getBusinessClassSingleList(Request $request)
    {
        return $this->businessClassSingleList($request->business_class_code);
    }

    private function businessClassSingleList($business_class_code)
    {
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
                ->where('type', 5)->lists('name', 'id')
                ->all() + [-1 => 'Other'];

        return response()->json(
            [
                'responseCode' => 1,
                'data' => $result,
                'subClass' => $sub_class
            ]
        );
    }

    public function manageBRA($section, $param, $app_id)
    {
        $app_id = Encryption::decodeId($app_id);
        $nationality = ['' => 'Select one'] + Countries::where('country_status', 'Yes')->where('nationality', '!=',
                '')->orderby('nationality', 'asc')->lists('nationality', 'id')->all();
        $passport_nationalities = Countries::orderby('nationality')->where('nationality', '!=',
            '')->where('nationality', '!=', 'Bangladeshi')
            ->lists('nationality', 'id');
        $passport_types = [
            'ordinary' => 'Ordinary',
            'diplomatic' => 'Diplomatic',
            'official' => 'Official',
        ];
        return view("BidaRegistrationAmendment::" . $section . '.' . $param, compact('app_id', 'nationality', 'passport_nationalities', 'passport_types'));
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
                    Session::flash('error', 'This is an invalid status, it\'s not possible to get the next status. [BRAC-1007]');
                    return redirect('process/bida-registration/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
                }

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
                    return redirect('process/bida-registration-amendment/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
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

            return redirect('process/bida-registration-amendment/view-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('BRAfterPayment: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [BRAC-1031]');
            Session::flash('error', 'Something went wrong!, application not updated after payment.' . CommonFunction::showErrorPublic($e->getMessage()) . ' [BRAC-1031]');
            return redirect('process/bida-registration-amendment/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
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
            return redirect('process/bida-registration--amendment/view-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('BRAfterCounterPayment: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [BRAC-1032]');
            Session::flash('error',
                'Something went wrong!, application not updated after payment. ' . CommonFunction::showErrorPublic($e->getMessage()) . ' [BRAC-1032]');
            return redirect('process/bida-registration-amendment/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        }
    }

    public function Payment(Request $request)
    {
        try {
            $appId = Encryption::decodeId($request->get('app_id'));

            // Get SBL payment configuration
            $payment_config = PaymentConfiguration::leftJoin('sp_payment_category', 'sp_payment_category.id', '=',
                'sp_payment_configuration.payment_category_id')
                ->where([
                    'sp_payment_configuration.process_type_id' => $this->process_type_id,
                    'sp_payment_configuration.payment_category_id' => 2,  // Government fee Payment
                    'sp_payment_configuration.status' => 1,
                    'sp_payment_configuration.is_archive' => 0,
                ])->first(['sp_payment_configuration.*', 'sp_payment_category.name']);
            if (empty($payment_config)) {
                Session::flash('error', "Payment configuration not found [BRAC-1456]");
                return redirect()->back()->withInput();
            }

            // Get payment distributor list
            $stakeDistribution = PaymentDistribution::where('sp_pay_config_id', $payment_config->id)
                ->where('status', 1)
                ->where('is_archive', 0)
                ->get(['id', 'stakeholder_ac_no', 'pay_amount', 'fix_status', 'purpose', 'purpose_sbl', 'distribution_type']);
            if ($stakeDistribution->isEmpty()) {
                Session::flash('error', "Stakeholder not found [BRAC-101]");
                return redirect()->back()->withInput();
            }

            // Check the Govt. vat fee is allowed or not: boolean
            $vatFreeAllowed = BasicInformationController::isAllowedWithOutVAT($this->process_type_id);

            // Store payment info
            DB::beginTransaction();

            // Store payment info
            $paymentInfo = SonaliPayment::firstOrNew([
                'app_id' => $appId, 'process_type_id' => $this->process_type_id,
                'payment_config_id' => $payment_config->id
            ]);

            $paymentInfo->payment_config_id = $payment_config->id;
            $paymentInfo->app_id = $appId;
            $paymentInfo->process_type_id = $this->process_type_id;
            $paymentInfo->payment_category_id = $payment_config->payment_category_id;

            $relevant_info_array = [
                'stakeholder_distribution' => $stakeDistribution,
            ];
            $amount_array = $this->fixedUnfixedAmountsForPayment($payment_config, $relevant_info_array);

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

            $paymentInfo->app_tracking_no = '';
            $paymentInfo->receiver_ac_no = $account_numbers;
            $paymentInfo->pay_amount = $amount_array['total_fixed_unfixed_amount'];
            $paymentInfo->vat_on_pay_amount = ($vatFreeAllowed === true) ? 0 : $amount_array['total_vat_on_pay_amount'];
            $paymentInfo->total_amount = ($paymentInfo->pay_amount + $paymentInfo->vat_on_pay_amount);
            $paymentInfo->contact_name = $request->get('gfp_contact_name');
            $paymentInfo->contact_email = $request->get('gfp_contact_email');
            $paymentInfo->contact_no = $request->get('gfp_contact_phone');
            $paymentInfo->address = $request->get('gfp_contact_address');
            $paymentInfo->sl_no = 1; // Always 1
            $paymentInfo->save();

            BidaRegistrationAmendment::where('id', $appId)->update([
                'gf_payment_id' => $paymentInfo->id
            ]);

            if ($vatFreeAllowed) {
                SonaliPaymentController::vatFreeAuditStore($paymentInfo->id, $amount_array['total_vat_on_pay_amount']);
            }

            // Payment Details By Stakeholders
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
                $paymentDetails->pay_amount = ($distribution->fix_status == 1) ? $distribution->pay_amount : $amount_array['amounts'][$distribution->distribution_type];
                $paymentDetails->distribution_type = $distribution->distribution_type;
                $paymentDetails->receiver_ac_no = $distribution->stakeholder_ac_no;
                $paymentDetails->purpose = $distribution->purpose;
                $paymentDetails->purpose_sbl = $distribution->purpose_sbl;
                $paymentDetails->fix_status = $distribution->fix_status;
                $paymentDetails->save();
            }
            // Payment Details By Stakeholders End

            // Payment Submission
            DB::commit();
            if ($request->get('actionBtn') == 'Submit' && $paymentInfo->id) {
                return redirect('spg/initiate-multiple/' . Encryption::encodeId($paymentInfo->id));
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('BRAPayment: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [BRAC-1025]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . "[BRAC-1025]");
            return redirect()->back()->withInput();
        }
    }

    // public function RegNoGenerate($app_id, $approval_center_id)
    // {
    //     $appInfo = BidaRegistrationAmendment::where('id', $app_id)->first();
    //     $division_Office = DivisionalOffice::where('id', $approval_center_id)
    //         ->where('status', 1)
    //         ->first(['short_code']);

    //     if ($appInfo->reg_no == null) {
    //         $prefix = '';
    //         if ($appInfo->organization_status_id == 1) {  //1 = Joint Venture
    //             $prefix = 'J';
    //         } elseif ($appInfo->organization_status_id == 2) { //2= Foreign
    //             $prefix = 'F';
    //         } elseif ($appInfo->organization_status_id == 3) { // 3= Local
    //             $prefix = 'L';
    //         }
    //         $regNo = $prefix . "-" . date("Ymd") . '00' . $app_id . '-' . $division_Office->short_code;
    //         $appInfo->reg_no = $regNo;
    //         $appInfo->save();
    //     }
    // }

    public function RegNoGenerate($app_id, $approval_center_id)
    {
        $appInfo = BidaRegistrationAmendment::where('id', $app_id)->first();
        $division_Office = DivisionalOffice::where('id', $approval_center_id)
            ->where('status', 1)
            ->first(['short_code']);

        if ($appInfo->reg_no == null) {
            $prefix = '';

            if (!empty($appInfo->n_organization_status_id)) {
                if ($appInfo->n_organization_status_id == 1) {
                    $prefix = 'J';
                } elseif ($appInfo->n_organization_status_id == 2) {
                    $prefix = 'F';
                } elseif ($appInfo->n_organization_status_id == 3) {
                    $prefix = 'L';
                }
            } else {
                if ($appInfo->organization_status_id == 1) {  //1 = Joint Venture
                    $prefix = 'J';
                } elseif ($appInfo->organization_status_id == 2) { //2= Foreign
                    $prefix = 'F';
                } elseif ($appInfo->organization_status_id == 3) { // 3= Local
                    $prefix = 'L';
                }
            }

            $regNo = $prefix . "-" . date("Ymd") . '00' . $app_id . '-' . $division_Office->short_code;
            $appInfo->reg_no = $regNo;
            $appInfo->save();
        }
    }

    public function fixedUnfixedAmountsForPayment($payment_config, $relevant_info_array = [])
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

        $amount_array = [
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

//            $vat_percentage = SonaliPaymentController::getGovtVendorVatPercentage();
//            if (empty($vat_percentage)) {
//                abort('Please, configure the value for VAT.');
//            }

            foreach ($relevant_info_array['stakeholder_distribution'] as $stakeholder) {
                if ($stakeholder->fix_status === 1) { // fixed
                    $amount_array[$stakeholder->distribution_type] = $stakeholder->pay_amount;
                }
                //elseif ($stakeholder->fix_status === 0) { // unfixed
                // unfixed amount business logic
                //}
            }
        }

//        elseif ($payment_config->payment_category_id === 3) {
//
//        }

        $fixed_unfixed_amount_total = 0;
        $vat_on_pay_amount_total = 0;
        foreach ($amount_array as $key => $amount) {
            // 4 = Vendor-Vat-Fee, 5 = Govt-Vat-Fee, 6 = Govt-Vendor-Vat-Fee
            if (in_array($key, [4, 5, 6])) {
                $vat_on_pay_amount_total += $amount;
            } else {
                $fixed_unfixed_amount_total += $amount;
            }
        }

        return [
            'amounts' => $amount_array,
            'total_fixed_unfixed_amount' => $fixed_unfixed_amount_total,
            'total_vat_on_pay_amount' => $vat_on_pay_amount_total,
        ];
    }

    /*
     * request @param
     * return @array
     * generate amendment json
     */
//    public function generateJsonData($request)
//    {
//        $data = [];
//        $caption = $request->get('caption');
//        $amendment_fields = $request->get('toggleCheck');
//        $countries = ['' => ''] + Countries::where('country_status', 'Yes')->orderBy('nicename', 'asc')->lists('nicename', 'id')->all();
//
//        if (count($amendment_fields)) {
//            $eaOrganizationType = ['' => 'Select one'] + EA_OrganizationType::where('is_archive', 0)->whereIn('type', [1, 3])->where('status', 1)->orderBy('name')->lists('name', 'id')->all();
//            $eaOrganizationStatus = ['' => 'Select one'] + EA_OrganizationStatus::where('is_archive', 0)->where('status', 1)->orderBy('name')->lists('name', 'id')->all();
//            $eaOwnershipStatus = ['' => 'Select one'] + EA_OwnershipStatus::where('is_archive', 0)->where('status', 1)->orderBy('name')->lists('name', 'id')->all();
//
//            $divisions = ['' => 'Select One'] + AreaInfo::where('area_type', 1)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
//            $districts = ['' => 'Select One'] + AreaInfo::where('area_type', 2)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
//            $thana = ['' => 'Select One'] + AreaInfo::orderby('area_nm')->where('area_type', 3)->lists('area_nm', 'area_id')->all();
//            $projectStatusList = ['' => 'Select One'] + ProjectStatus::orderBy('name')->where('is_archive', 0)->where('status', 1)->lists('name', 'id')->all();
//
//            foreach ($amendment_fields as $key => $value) {
//                $amendment_data = [];
//                $amendment_data['caption'] = (isset($caption[$key]) ? $caption[$key] : '');
//                if ($key == 'n_organization_type_id') {
//                    $amendment_data['old'] = ($request->has(substr($key, 2)) ? $eaOrganizationType[$request->get(substr($key, 2))] : '');
//                    $amendment_data['new'] = ($request->has($key) ? $eaOrganizationType[$request->get($key)] : '');
//
//                } elseif ($key == 'n_organization_status_id') {
//                    //$amendment_data['caption'] = $caption[$key];
//                    $amendment_data['old'] = ($request->has(substr($key, 2)) ? $eaOrganizationStatus[$request->get(substr($key, 2))] : '');
//                    $amendment_data['new'] = ($request->has($key) ? $eaOrganizationStatus[$request->get($key)] : '');
//
//                } elseif ($key == 'n_ownership_status_id') {
//                    $amendment_data['caption'] = $caption[$key];
//                    $amendment_data['old'] = ($request->has(substr($key, 2)) ? $eaOwnershipStatus[$request->get(substr($key, 2))] : '');
//                    $amendment_data['new'] = ($request->has($key) ? $eaOwnershipStatus[$request->get($key)] : '');
//
//                } elseif ($key == 'n_country_of_origin_id') {
//                    $amendment_data['old'] = ($request->has(substr($key, 2)) ? $countries[$request->get(substr($key, 2))] : '');
//                    $amendment_data['new'] = ($request->has($key) ? $countries[$request->get($key)] : '');
//
//                } elseif ($key == 'n_sub_class_id'){
//                    $amendment_data['old'] = $this->getBusinessSubClass($request->has(substr($key, 2)) ? $request->get(substr($key, 2)) : "");
//                    $amendment_data['new'] = $this->getBusinessSubClass($request->has($key) ? $request->get($key) : "");
//
//                } elseif ($key == 'n_ceo_country_id') {
//                    $amendment_data['old'] = ($request->has(substr($key, 2)) ? $countries[$request->get(substr($key, 2))] : '');
//                    $amendment_data['new'] = ($request->has($key) ? $countries[$request->get($key)] : '');
//
//                } elseif ($key == 'n_ceo_district_id') {
//                    //$amendment_data['caption'] = $caption[$key];
//                    $amendment_data['old'] = ($request->has(substr($key, 2)) ? $districts[$request->get(substr($key, 2))] : '');
//                    $amendment_data['new'] = ($request->has($key) ? $districts[$request->get($key)] : '');
//
//                } elseif ($key == 'n_ceo_thana_id') {
//                    $amendment_data['old'] = ($request->has(substr($key, 2)) ? $thana[$request->get(substr($key, 2))] : '');
//                    $amendment_data['new'] = ($request->has($key) ? $thana[$request->get($key)] : '');
//
//                } elseif ($key == 'n_office_division_id') {
//                    $amendment_data['old'] = ($request->has(substr($key, 2)) ? $divisions[$request->get(substr($key, 2))] : '');
//                    $amendment_data['new'] = ($request->has($key) ? $divisions[$request->get($key)] : '');
//
//                } elseif ($key == 'n_office_district_id') {
//                    $amendment_data['old'] = ($request->has(substr($key, 2)) ? $districts[$request->get(substr($key, 2))] : '');
//                    $amendment_data['new'] = ($request->has($key) ? $districts[$request->get($key)] : '');
//
//                } elseif ($key == 'n_office_thana_id') {
//                    $amendment_data['old'] = ($request->has(substr($key, 2)) ? $thana[$request->get(substr($key, 2))] : '');
//                    $amendment_data['new'] = ($request->has($key) ? $thana[$request->get($key)] : '');
//
//                } elseif ($key == 'n_factory_district_id') {
//                    $amendment_data['old'] = ($request->has(substr($key, 2)) ? $districts[$request->get(substr($key, 2))] : '');
//                    $amendment_data['new'] = ($request->has($key) ? $districts[$request->get($key)] : '');
//
//                } elseif ($key == 'n_factory_thana_id') {
//                    $amendment_data['old'] = ($request->has(substr($key, 2)) ? $thana[$request->get(substr($key, 2))] : '');
//                    $amendment_data['new'] = ($request->has($key) ? $thana[$request->get($key)] : '');
//
//                } elseif ($key == 'n_project_status_id') {
//                    $amendment_data['old'] = ($request->has(substr($key, 2)) ? $projectStatusList[$request->get(substr($key, 2))] : '');
//                    $amendment_data['new'] = ($request->has($key) ? $projectStatusList[$request->get($key)] : '');
//
//                }  else {
//                    $amendment_data['old'] = ($request->has(substr($key, 2)) ? $request->get(substr($key, 2)) : '');
//                    $amendment_data['new'] = ($request->has($key) ? $request->get($key) : '');
//
//                }
//                $data[] = $amendment_data;
//            }
//        }
//
//        // A. Company Information
//
//        // B. Information of Principal Promoter/Chairman/Managing Director/CEO/Country Manager
//
//        // C. Office Address
//
//        // D. Factory Address
//
//        // 1. Project status
//
//        // 3. Date of commercial operation
//
//        // 4. Sales (in 100%)
//        if (isset($request->get('multiToggleCheck')['n_local_sales'])) {
//            $fields = ['n_local_sales', 'n_foreign_sales', 'n_total_sales'];
//            $caption = ['Local sale', 'Foreign sale', 'Total sale in (%)'];
//
//            for ($inc = 0; $inc < count($fields); $inc++) {
//                $data_store = [];
//                $data_store['caption'] = $caption[$inc];
//                $data_store['old'] = ($request->has(substr($fields[$inc], 2)) ? $request->get(substr($fields[$inc], 2)) : '');
//                $data_store['new'] = ($request->has($fields[$inc]) ? $request->get($fields[$inc]) : '');
//                $data[] = $data_store;
//            }
//        }
//
//        // 5. Manpower of the organization (Latest BIDA Reg. Info.)
//        if (isset($request->get('multiToggleCheck')['n_local_male'])) {
//            $manpower_fields = ['n_local_male', 'n_local_female', 'n_local_total', 'n_foreign_male', 'n_foreign_female', 'n_foreign_total', 'n_manpower_local_ratio', 'n_manpower_foreign_ratio'];
//            $caption = ['Local male', 'Local female', 'Local total (a)', 'Foreign male', 'Foreign female', 'Foreign total (b)', 'Grand total (a+b)', 'Ratio local', 'Ratio foreign'];
//
//            for ($inc = 0; $inc < count($manpower_fields); $inc++) {
//                $manpower_data = [];
//                $manpower_data['caption'] = $caption[$inc];
//                $manpower_data['old'] = ($request->has(substr($manpower_fields[$inc], 2)) ? $request->get(substr($manpower_fields[$inc], 2)) : '');
//                $manpower_data['new'] = ($request->has($manpower_fields[$inc]) ? $request->get($manpower_fields[$inc]) : '');
//                $data[] = $manpower_data;
//            }
//        }
//
//        // 6. Investment
//        // 7. Source of finance
//        if (isset($request->get('multiToggleCheck')['investment_sources_of_finance'])) {
//
//            $investment_fields = ['n_local_land_ivst', 'n_local_building_ivst', 'n_local_machinery_ivst', 'n_local_others_ivst', 'n_local_wc_ivst', 'n_total_fixed_ivst_million', 'n_total_fixed_ivst', 'n_usd_exchange_rate', 'n_total_fee',
//                'n_finance_src_loc_equity_1', 'n_finance_src_foreign_equity_1', 'n_finance_src_loc_loan_1', 'n_finance_src_foreign_loan_1', 'n_finance_src_total_loan',
//                'n_finance_src_loc_total_financing_m', 'n_finance_src_loc_total_financing_1', 'n_finance_src_loc_total_equity_1'];
//
//            $caption = ['Investment land (million)', 'Investment building (million)', 'Investment machinery & equipment (million)', 'Investment others (million)', 'Investment working capital (three months) (million)', 'Investment total investment (million) (BDT)',
//                'Investment total investment (BDT)', 'Investment dollar exchange rate (USD)', 'Investment total fee (BDT)', 'Source of finance local equity (million)', 'Source of finance foreign equity (million)', 'Source of finance total equity (a)', 'Source of finance local loan (million)',
//                'Source of finance foreign loan (million)', 'Source of finance total loan (million) (b)', 'Total financing (million) (a+b)', 'Total financing (BDT) (a+b)'];
//
//            for ($inc = 0; $inc < count($investment_fields); $inc++) {
//                $investment_data = [];
//                $investment_data['caption'] = $caption[$inc];
//                $investment_data['old'] = ($request->has(substr($investment_fields[$inc], 2)) ? $request->get(substr($investment_fields[$inc], 2)) : '');
//                $investment_data['new'] = ($request->has($investment_fields[$inc]) ? $request->get($investment_fields[$inc]) : '');
//                $data[] = $investment_data;
//            }
//
//            // Country wise source of finance (Million BDT)
//            $caption = ['Country wise source of finance country', 'Country wise source of finance equity amount', 'Country wise source of finance loan amount'];
//            $field = ['n_country_id', 'n_equity_amount', 'n_loan_amount'];
//
//            if(!empty($request->n_country_id[0])) {
//                for($i = 0; $i < 3; $i++) {
//                    foreach($request->n_country_id as $key=>$value) {
//                        $finance_data = [];
//                        $finance_data['caption'] = $caption[$i];
//                        if ($field[$i] == 'n_country_id') {
//                            $finance_data['old'] = !empty($request->get(substr($field[$i], 2))[$key]) ? $countries[$request->get(substr($field[$i], 2))[$key]] : "";
//                            $finance_data['new'] = !empty($request->get($field[$i])[$key]) ? $countries[$request->get($field[$i])[$key]] : "";
//                        } else{
//                            $finance_data['old'] = !empty($request->get(substr($field[$i], 2))[$key]) ? $request->get(substr($field[$i], 2))[$key] : "";
//                            $finance_data['new'] = !empty($request->get($field[$i])[$key]) ? $request->get($field[$i])[$key] : "";
//                        }
//                        $data[] = $finance_data;
//                    }
//                }
//            }
//        }
//
//        // 8. Public utility service
//        if (isset($request->get('multiToggleCheck')['n_public_land'])) {
//            $public_utility_fields = ['n_public_land', 'n_public_electricity', 'n_public_gas', 'n_public_telephone', 'n_public_road', 'n_public_water', 'n_public_drainage', 'n_public_others'];
//            $caption = ['Public utility service (land)', 'Public utility service (electricity)', 'Public utility service (gas)', 'Public utility service (telephone)', 'Public utility service (road)', 'Public utility service (water)', 'Public utility service (drainage)', 'Public utility service (others)'];
//
//            for ($inc = 0; $inc < count($public_utility_fields); $inc++) {
//                $public_utility_data = [];
//                $public_utility_data['caption'] = $caption[$inc];
//                $public_utility_data['old'] = ($request->has(substr($public_utility_fields[$inc], 2)) ? $request->get(substr($public_utility_fields[$inc], 2)) : '');
//                $public_utility_data['new'] = ($request->has($public_utility_fields[$inc]) ? $request->get($public_utility_fields[$inc]) : '');
//                $data[] = $public_utility_data;
//            }
//        }
//
//        // 9. Trade licence details
//
//        //10. Tin
//
//        // 13. Description of machinery and equipment
//        if (isset($request->get('multiToggleCheck')['n_machinery_local_qty'])) {
//            $machinery_local_fields = ['n_machinery_local_qty', 'n_machinery_local_price_bdt', 'n_imported_qty', 'n_imported_qty_price_bdt', 'n_total_machinery_qty', 'n_total_machinery_price'];
//            $caption = ['Machinery and equipment locally collected quantity', 'Machinery and equipment locally collected price (BDT)', 'Machinery and equipment imported quantity', 'Machinery and equipment imported price (BDT)', 'Machinery and equipment total quantity', 'Machinery and equipment total price (BDT)'];
//
//            for ($inc = 0; $inc < count($machinery_local_fields); $inc++) {
//                $machinery_local_data = [];
//                $machinery_local_data['caption'] = $caption[$inc];
//                $machinery_local_data['old'] = ($request->has(substr($machinery_local_fields[$inc], 2)) ? $request->get(substr($machinery_local_fields[$inc], 2)) : '');
//                $machinery_local_data['new'] = ($request->has($machinery_local_fields[$inc]) ? $request->get($machinery_local_fields[$inc]) : '');
//                $data[] = $machinery_local_data;
//            }
//        }
//
//        // Amendment data for string
//        $change_fields = '';
//        $change_old_value = '';
//        $change_new_value = '';
//
//        $counter = 0;
//        foreach ($data as $key => $data_value) {
//            if ($counter == 0) {
//                $change_fields .= $data_value['caption'];
//                $change_old_value .= $data_value['old'];
//                $change_new_value .= $data_value['new'];
//            } elseif ($counter == (count($data) - 1)) {
//                $change_fields .= ' & ' . $data_value['caption'];
//                $change_old_value .= ' & ' . $data_value['old'];
//                $change_new_value .= ' & ' . $data_value['new'];
//            } else {
//                $change_fields .= ', ' . $data_value['caption'];
//                $change_old_value .= ', ' . $data_value['old'];
//                $change_new_value .= ', ' . $data_value['new'];
//            }
//            $counter++;
//        }
//
//        // Amendment data for string
//        return ['json' => json_encode($data), 'change_fields' => $change_fields, 'change_old_value' => $change_old_value, 'change_new_value' => $change_new_value];
//    }

    /**
     * @param $sub_class_id
     * @return string
     */

    private function getBusinessSubClass($sub_class_id)
    {
        if (!empty($sub_class_id)){
            return BusinessClass::select('id', DB::raw("CONCAT(code, ' - ', name) as name"))->where('id', $sub_class_id)->where('type', 5)->pluck('name');
        }

        return "";
    }

    // function validateSales($localSales, $foreignSales, $messagePrefix) {
    //     $totalSales = $localSales + $foreignSales;
    
    //     if ($totalSales > 100) {
    //         Session::flash('error', "The sum of $messagePrefix local sales and foreign sales should be within the range of 0 to 100");
    //         return false;
    //     }
    
    //     return true;
    // }

    function validateSales() {
        $args = func_get_args();
        $messagePrefix = array_shift($args);
        $totalSales = array_sum($args);
    
        if ($totalSales > 100) {
            Session::flash('error', "The sum of $messagePrefix sales should be within the range of 0 to 100");
            return false;
        }
    
        return true;
    }

    public function checkTrackingNoExists(Request $request)
    {
        try {
            $trackingNo = $request->get('manually_approved_br_no');
            $company_id = CommonFunction::getUserWorkingCompany();
            // Perform the database query to check if the tracking number exists
            $exists = ProcessList::where('tracking_no', $trackingNo)
                ->whereIn('process_type_id', [102, 12]) // BR & BRA
                ->where('company_id', $company_id)
                ->where('status_id', 25)
                ->orderBy('id', 'desc')
                ->exists();
    
            return response()->json(['responseCode' => 1, 'exists' => $exists]);

        } catch (\Exception $e) {
            Log::error('BRACheckTrackingNoExists: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [BRAC-1018]');
            return response()->json(['error' => 'An error occurred while checking the tracking number.'], 500);
        }
    }

}