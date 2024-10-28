<?php

namespace App\Modules\ImportPermission\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Libraries\ImageProcessing;
use App\Libraries\UtilFunction;
use App\Modules\Apps\Models\AppDocuments;
use App\Modules\Apps\Models\ManualPayment;
use App\Modules\BidaRegistration\Models\LaAnnualProductionCapacity;
use App\Modules\ImportPermission\Models\ListOfMachineryImported;
use App\Modules\ImportPermission\Models\AnnualProductionCapacity;
use App\Modules\ImportPermission\Models\BusinessClass;
use App\Modules\ImportPermission\Models\ImportPermission;
use App\Modules\ImportPermission\Models\IrcSourceOfFinance;
use App\Modules\ImportPermission\Models\ListOfMachineryImportedSpareParts;
use App\Modules\ImportPermission\Models\MasterMachineryImported;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\SonaliPayment\Models\PaymentDetails;
use App\Modules\SonaliPayment\Models\PaymentDistribution;
use App\Modules\SonaliPayment\Models\SonaliPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use App\Modules\ImportPermission\Services\ImportPermissionService;
use App\Modules\Settings\Models\Attachment;
use App\Modules\Settings\Models\Currencies;

class ImportPermissionController extends Controller
{
    protected $process_type_id;
    protected $aclName;
    protected $importPermissionService;

    // public function __construct()
    public function __construct(ImportPermissionService $importPermissionService)
    {
        $this->importPermissionService = $importPermissionService;
        $this->process_type_id = 21;
        $this->aclName = 'ImportPermission';
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function applicationForm(Request $request)
    {
        $data['mode'] = '-A-';
        $viewMode = 'off';

        $requestValidationCheck = $this->importPermissionService->validateRequestAccess($request, $data['mode'], 'IP-1001', 'IP-971');
        if ($requestValidationCheck !== true) {
            return $requestValidationCheck;
        }

        $working_company_id = CommonFunction::getUserWorkingCompany();
        $errorResponse = $this->importPermissionService->checkBasicInfoAndDepartment($working_company_id, 'IP-9991', 'IP-1041');
        if ($errorResponse !== null) {
            return $errorResponse;
        }

        try {
            $data['payment_config'] = $this->importPermissionService->getPaymentInfo(1); // Submission fee payment
            if (empty($data['payment_config'])) {
                return response()->json([
                    'responseCode' => 1,
                    'html' => "<h4 class='custom-err-msg'> Payment Configuration not found ![IP-10101]</h4>"
                ]);
            }
            $unfixed_amount_array = $this->importPermissionService->unfixedAmountsForPayment($data['payment_config']);
            $data['payment_config']->amount = $unfixed_amount_array['total_unfixed_amount'] + $data['payment_config']->amount;
            $data['payment_config']->vat_on_pay_amount = $unfixed_amount_array['total_vat_on_pay_amount'];

            $data['getCompanyData'] = $this->importPermissionService->getCompanyInfoData();
            if (empty($data['getCompanyData'])) {
                return response()->json([
                    'responseCode' => 1,
                    'html' => "<h4 class='custom-err-msg'>Sorry! You have no approved Basic Information application. [IP-9992]</h4>"
                ]);
            }

            $data['eaOrganizationType'] = $this->importPermissionService->getData('eaOrganizationType');
            $data['countries'] = $this->importPermissionService->getData('countries');
            $data['countriesWithoutBD'] = $this->importPermissionService->getData('countriesWithoutBD');
            $data['eaOrganizationStatus'] = $this->importPermissionService->getData('eaOrganizationStatus');
            $data['eaOwnershipStatus'] = $this->importPermissionService->getData('eaOwnershipStatus');
            $data['currencyBDT'] = $this->importPermissionService->getData('currencyBDT');
            $data['currencies'] = Currencies::orderBy('code')->where('is_archive', 0)->where('is_active', 1)->lists('code',
                'id');
            $data['divisions'] = $this->importPermissionService->getData('divisions');
            $data['districts'] = $this->importPermissionService->getData('districts');
            $data['thana'] = $this->importPermissionService->getData('thana');
            $data['projectStatusList'] = $this->importPermissionService->getData('projectStatusList');
            $data['nationality'] = $this->importPermissionService->getData('nationality');
            $data['usdValue'] = $this->importPermissionService->getData('usdValue');
            $data['totalFee'] = $this->importPermissionService->getData('totalFee');
            $data['productUnit'] = $this->importPermissionService->getData('productUnit');

            $public_html = strval(view("ImportPermission::application-form", $data));
            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (\Exception $e) {
            Log::error('IPAddForm : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [IP-1005]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . ' [IP-1005]' . "</h4>"
            ]);
        }
    }

    public function applicationEdit($applicationId, $openMode = "", Request $request)
    {
        $data = [];
        $data['mode'] = '-E-';
        $data['viewMode'] = 'off';

        $requestValidationCheck = $this->importPermissionService->validateRequestAccess($request, $data['mode'], 'IP-1002', 'IP-973');
        if ($requestValidationCheck !== true) {
            return $requestValidationCheck;
        }

        $working_company_id = CommonFunction::getUserWorkingCompany();

        $errorResponse = $this->importPermissionService->checkBasicInfoAndDepartment($working_company_id, 'IP-9994', 'IP-1043');
        if ($errorResponse !== null) {
            return $errorResponse;
        }

        try {

            $applicationId = Encryption::decodeId($applicationId);
            $process_type_id = $this->process_type_id;
            
            $data['appInfo'] = $this->importPermissionService->getAppEditInfo($applicationId);

            $data['annualProductionCapacity'] = $this->importPermissionService->getAnnualProductionCapacityData($applicationId);
            $data['eaOwnershipStatus'] = $this->importPermissionService->getData('eaOwnershipStatus');
            $data['eaOrganizationStatus'] = $this->importPermissionService->getData('eaOrganizationStatus');
            $data['eaOrganizationType'] = $this->importPermissionService->getData('eaOrganizationType');
            $data['divisions'] = $this->importPermissionService->getData('divisions');
            $data['districts'] = $this->importPermissionService->getData('districts');
            $data['countriesWithoutBD'] = $this->importPermissionService->getData('countriesWithoutBD');
            $data['countries'] = $this->importPermissionService->getData('countries');
            $data['nationality'] = $this->importPermissionService->getData('nationality');
            $data['currencies'] = $this->importPermissionService->getData('currencies');
            $data['usdValue'] = $this->importPermissionService->getData('usdValue');
            $data['projectStatusList'] = $this->importPermissionService->getData('projectStatusList');
            $data['totalFee'] = $this->importPermissionService->getData('totalFee');
            $data['laAnnualProductionCapacity'] = LaAnnualProductionCapacity::where('app_id', $applicationId)->get();
            $data['listOfMachineryImported'] = ListOfMachineryImported::where('app_id', $applicationId)->where('process_type_id', $this->process_type_id)->get();
            $data['listOfMachineryImportedTotal'] = ListOfMachineryImported::where('app_id', $applicationId)->where('process_type_id', $this->process_type_id)->sum('l_machinery_imported_total_value');
            $data['listOfMachineryImportedSpare'] = ListOfMachineryImportedSpareParts::where('app_id', $applicationId)->where('process_type_id', $this->process_type_id)->get();
            $data['currencyBDT'] = $this->importPermissionService->getData('currencyBDT');
            $data['source_of_finance'] = IrcSourceOfFinance::where('app_id', $applicationId)->get();
            $data['desire_office'] = ProcessList::where('ref_id', $applicationId)
            ->where('process_type_id',$process_type_id)
            ->leftJoin('divisional_office', 'process_list.approval_center_id', '=', 'divisional_office.id')
            ->first(['divisional_office.office_name as des_office_name', 'divisional_office.office_address as des_office_address']);

            $public_html = strval(view("ImportPermission::application-form-edit", $data));

            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (\Exception $e) {

            Log::error('IPEditForm : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [IP-1010]');

            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . "[IP-1010]" . "</h4>"
            ]);
        }
    }

    public function applicationView($app_id, Request $request)
    {
        $data['viewMode'] = 'on';
        $data['mode'] = '-V-';

        $requestValidationCheck = $this->importPermissionService->validateRequestAccess($request, $data['mode'], 'IP-1003', 'IP-973');
        if ($requestValidationCheck !== true) {
            return $requestValidationCheck;
        }

        try {
            $applicationId = Encryption::decodeId($app_id);
            $process_type_id = $this->process_type_id;

            $appInfo = $this->importPermissionService->getAppViewInfo($applicationId);

            $data['source_of_finance'] = $this->importPermissionService->getSourceOfFinanceData($applicationId);
            $data['machineryImportedTotal'] = ListOfMachineryImported::where('app_id', $applicationId)->where('process_type_id', $this->process_type_id)->sum('l_machinery_imported_total_value');
            $data['annual_production_capacity'] = $this->importPermissionService->getAnnualProductionCapacityData($applicationId);
            $data['listOfMechineryImported'] = ListOfMachineryImported::Where('app_id', $applicationId)->where('process_type_id', $this->process_type_id)->where('status', 1)->get();
            //$listOfMechineryImportedSpare = ListOfMachineryImportedSpareParts::with('currency')->Where('app_id', $applicationId)->where('process_type_id', $this->process_type_id)->where('status', 1)->get();
           $data['listOfMechineryImportedSpare'] = ListOfMachineryImportedSpareParts::leftJoin('currencies', 'ip_list_of_machinery_imported_spare_parts.total_value_ccy', '=', 'currencies.id')->where('ip_list_of_machinery_imported_spare_parts.app_id', $applicationId)->where('ip_list_of_machinery_imported_spare_parts.process_type_id', $this->process_type_id)->where('ip_list_of_machinery_imported_spare_parts.status', 1)->select('ip_list_of_machinery_imported_spare_parts.*', 'currencies.id as currency_id', 'currencies.code as currency_code')->get();
            $data['listOfMechineryImportedSpare'] = $this->importPermissionService->updateRemainingQuantity($data['listOfMechineryImportedSpare'], $appInfo->status_id);
            $query = $this->importPermissionService->getBusinessSectorData($appInfo->class_code);
            $data['business_code'] = json_decode(json_encode($query), true);
            $data['sub_class'] = BusinessClass::select('id', DB::raw("CONCAT(code, ' - ', name) as name"))->where('id', $appInfo->sub_class_id)->first();
            $data['document'] = $this->importPermissionService->getAppDocumentsData($applicationId);
            $data['desire_office'] = ProcessList::where('ref_id', $applicationId)
            ->where('process_type_id',$process_type_id)
            ->leftJoin('divisional_office', 'process_list.approval_center_id', '=', 'divisional_office.id')
            ->first(['divisional_office.office_name as des_office_name', 'divisional_office.office_address as des_office_address']);
            $data['ref_app_url'] = '#';
            if (!empty($appInfo->ref_app_tracking_no)) {
                $data['ref_app_url'] = url('process/'.$appInfo->ref_process_type_key.'/view-app/'.Encryption::encodeId($appInfo->ref_application_ref_id) . '/' . Encryption::encodeId($appInfo->ref_application_process_type_id));
            }
            $data['appInfo'] = $appInfo;
            $data['process_type_id'] = $process_type_id;
            $public_html = strval(view("ImportPermission::application-form-view",$data));

            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (\Exception $e) {
            Log::error('IPView : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [IP-10111]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . "[IP-10111]" . "</h4>"
            ]);
        }
    }

    public function appStore(Request $request)
    {
        $app_id = (!empty($request->get('app_id')) ? Encryption::decodeId($request->get('app_id')) : '');
        $mode = (!empty($request->get('app_id')) ? '-E-' : '-A-');
        // Check whether the applicant company is eligible and have approved basic information application
        $working_company_id = CommonFunction::getUserWorkingCompany();

        if (!ACL::getAccsessRight($this->aclName, $mode, $app_id)) {
            abort('400', 'You have no access right! Contact with system admin for more information. [IP-972]');
        }

        // if submitted for get BR info
        if ($request->get('actionBtn') == 'searchBRinfo') {
            // if applicant have approved BIDA Reg tracking no given then set session
            if ( $request->has('ref_app_tracking_no')) {
                $refAppTrackingNo = trim($request->get('ref_app_tracking_no'));
    
                $getBrApprovedData = ProcessList::where('tracking_no', $refAppTrackingNo)
                    ->where('status_id', 25)
                    ->where('company_id', $working_company_id)
                    ->whereIn('process_type_id', [102,12])
                    ->leftJoin('divisional_office', 'process_list.approval_center_id', '=', 'divisional_office.id')
                    ->first(['process_type_id', 'ref_id', 'tracking_no', 'approval_center_id', 'divisional_office.office_name', 'divisional_office.office_address']);
    
                if (empty($getBrApprovedData)) {
                    Session::flash('error', 'Sorry! BIDA Registration not found by this tracking number! [IP-111]');
                    return redirect()->back();
                }
    
                $getBRinfo = UtilFunction::checkBRCommonPoolData($getBrApprovedData->tracking_no, $getBrApprovedData->ref_id);
                if (empty($getBRinfo)) {
                    Session::flash('error', 'Sorry! BIDA Registration not found by tracking no! [IP-1081]');
                    return redirect()->back();
                }
                
                if($getBRinfo->organization_status_id == 3 && Auth::user()->sub_department_id != 4){
                    Session::flash('error', 'Sorry! Local BIDA Registration isn\'t eligible to apply for Import Permission. [IP-1082]');
                    return redirect()->back();
                }
                
                if($getBrApprovedData->process_type_id == 12 && !empty($getBRinfo->br_tracking_no)){
                    $refBRAppTrackingNo = $getBRinfo->br_tracking_no;

                    $listOfMachineryImportedMaster = MasterMachineryImported::where('ref_app_tracking_no', $refBRAppTrackingNo)
                    ->whereNotIn('amendment_type', ['delete', 'remove'])
                    ->where('status', 1)
                    ->get();

                } else {
                    $listOfMachineryImportedMaster = MasterMachineryImported::where('ref_app_tracking_no', $refAppTrackingNo)
                    ->whereNotIn('amendment_type', ['delete', 'remove'])
                    ->where('status', 1)
                    ->get();
                }
    
                if(count($listOfMachineryImportedMaster) > 0){
                    Session::put('listOfMachineryImportedMaster', $listOfMachineryImportedMaster);
                }
                Session::put('brInfo', $getBRinfo->toArray());
                Session::put('brInfo.last_br', $request->get('last_br'));
                Session::put('brInfo.ref_app_tracking_no', $refAppTrackingNo);
                Session::put('brInfo.approval_center_id', $getBrApprovedData->approval_center_id);
                Session::put('brInfo.des_office_name', $getBrApprovedData->office_name);
                Session::put('brInfo.des_office_address', $getBrApprovedData->office_address);
                // dd($getBrApprovedData->process_type_id, $getBRinfo->bra_tracking_no);
                // if ($getBrApprovedData->process_type_id == 12 && !empty($getBRinfo->bra_tracking_no)) {
                if (!empty($getBRinfo->bra_tracking_no)) {
                // if (!empty($getBRinfo->br_tracking_no) && !empty($getBRinfo->bra_tracking_no)) {
    
                    $bra_ref_no = ProcessList::where('tracking_no', $getBRinfo->bra_tracking_no)
                        ->where('process_type_id', 12)
                        ->where('status_id', 25)
                        ->value('ref_id');
    
                    $this->importPermissionService->BRAChildTableDataLoad($bra_ref_no);
                    Session::put('ref_app_approve_date',$getBRinfo->bra_approved_date);
                } 
                elseif ($getBrApprovedData->process_type_id == 102 && !empty($getBRinfo->br_tracking_no)) {
                // else {
                    $this->importPermissionService->BRChildTableDataLoad($getBrApprovedData->ref_id);
                    Session::put('ref_app_approve_date', $getBRinfo->br_approved_date);
                }
    
                Session::flash('success', 'Successfully loaded BIDA Registration data. Please proceed to next step.');
                return redirect()->back();
            }
        }

        // Clean session data
        if ($request->get('actionBtn') == 'clean_load_data') {
            $this->importPermissionService->cleanLoadData();
            return redirect()->back();
        }

        $errorResponse = $this->importPermissionService->checkBasicInfoAndDepartment($working_company_id, 'IP-9993', 'IP-1042');
        if ($errorResponse !== null) {
            return $errorResponse;
        }

        
        // Checking the Government & Service Fee Payment configuration for this service
        $payment_config = $this->importPermissionService->getPaymentInfo(1); // Submission fee payment
        if (!$payment_config) {
            Session::flash('error', "Payment configuration not found [IP-101]");
            return redirect()->back()->withInput();
        }

        // Checking the payment distributor under payment configuration
        $stakeDistribution = PaymentDistribution::where('sp_pay_config_id', $payment_config->id)
            ->where('status', 1)
            ->where('is_archive', 0)
            ->get(['id', 'stakeholder_ac_no', 'pay_amount', 'fix_status', 'purpose', 'purpose_sbl', 'distribution_type']);
        if ($stakeDistribution->isEmpty()) {
            Session::flash('error', "Stakeholder not found [IP-100]");
            return redirect()->back()->withInput();
        }

        //  Required Documents for attachment
        // $attachment_key = "import_permission";

        // $doc_row = $this->importPermissionService->getAttachment($attachment_key);

        // // Validation Rules when application submitted
        // $validation = $this->importPermissionService->getApplicationValidationRules($request, $doc_row);
        // $this->validate($request, $validation['rules'], $validation['messages']);

        $attachment_key = self::generateAttachmentKey($request->organization_status_id);

        $doc_row = $this->importPermissionService->getAttachment($attachment_key);

        // Validation Rules when application submitted
        $validation = $this->importPermissionService->getApplicationValidationRules($request, $doc_row);
        $this->validate($request, $validation['rules'], $validation['messages']);

        try {
            DB::beginTransaction();
            if ($request->get('app_id')) {
                $appData = ImportPermission::find($app_id);
                $processData = ProcessList::where([
                    'process_type_id' => $this->process_type_id, 'ref_id' => $appData->id
                ])->first();
            } else {
                $appData = new ImportPermission();
                $processData = new ProcessList();
            }


                $appData->ref_app_tracking_no = trim($request->get('ref_app_tracking_no'));
                $appData->ref_app_approve_date = (!empty($request->get('ref_app_approve_date')) ? date('Y-m-d', strtotime($request->get('ref_app_approve_date'))) : null);
                $appData->reg_no = $request->get('reg_no');
            

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

            if ($request->hasFile('project_profile_attachment')) {
                $yearMonth = date("Y") . "/" . date("m") . "/";
                $path = 'uploads/' . $yearMonth;
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $_file_path = $request->file('project_profile_attachment');
                $file_path = trim(uniqid('IP_PPA-' . time() . '-', true) . $_file_path->getClientOriginalName());
                $_file_path->move($path, $file_path);
                $appData->project_profile_attachment = $yearMonth . $file_path;
            } else {
                $appData->project_profile_attachment = $request->get('project_profile_attachment_data');
            }

            // Code of your business class
            if ($request->has('business_class_code')) {

                $business_class = $this->getBusinessClassSingleList($request);
                $get_business_class = json_decode($business_class->getContent(), true);

                if (empty($get_business_class['data'])) {
                    Session::flash('error', "Sorry! Your given Code of business class is not valid. Please enter the right one. [IP-1017]");
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
            $appData->company_name = CommonFunction::getCompanyNameById($working_company_id);
            $appData->company_name_bn = CommonFunction::getCompanyBnNameById($working_company_id);
            $appData->organization_type_id = $request->get('organization_type_id');
            // dd($appData->organization_type_id );
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
            $appData->trade_licence_validity_period = (!empty($request->get('trade_licence_validity_period')) ? date('Y-m-d',
                strtotime($request->get('trade_licence_validity_period'))) : null);
            $appData->trade_licence_issuing_authority = $request->get('trade_licence_issuing_authority');


            $appData->tin_number = $request->get('tin_number');
            $appData->tin_issuing_authority = $request->get('tin_issuing_authority');

            // $appData->machinery_local_qty = $request->get('machinery_local_qty');
            // $appData->machinery_local_price_bdt = $request->get('machinery_local_price_bdt');
            // $appData->imported_qty = $request->get('imported_qty');
            // $appData->imported_qty_price_bdt = $request->get('imported_qty_price_bdt');
            // $appData->total_machinery_price = $request->get('total_machinery_price');
            // $appData->total_machinery_qty = $request->get('total_machinery_qty');
            // $appData->local_description = $request->get('local_description');
            // $appData->imported_description = $request->get('imported_description');

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
                $investor_signature_name = trim(sprintf("%s", uniqid('BIDA_IP_', true))) . '_' . time() . '.jpeg';

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

                } else {  // For new application submission
                    $processData->status_id = -1;
                    $processData->desk_id = 0;
                }
            }
            $appData->company_id = $working_company_id;
            $appData->save();
            
            // Annual production capacity- Nothing will be added from edit page
            if (!empty($appData->id) && !empty($request->get('apc_product_name')[0])) {
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

            // Name of machinery’s with spare parts to Imported
            if (!empty($appData->id)) {
                $listOfMachineryImportedSpareIDs = [];

                foreach ($request->get('name') as $key => $value) {
                    $listOfMachineryImportedSpareID = $request->get('list_of_machinery_imported_spare_id')[$key];
                    // dd($listOfMachineryImportedSpareID);
                    $listOfMachineryImportedSpare = ListOfMachineryImportedSpareParts::findOrNew($listOfMachineryImportedSpareID);
                    $listOfMachineryImportedSpare->app_id = $appData->id;
                    $listOfMachineryImportedSpare->process_type_id = $this->process_type_id;
                    $listOfMachineryImportedSpare->name = $request->get('name')[$key];
                    $listOfMachineryImportedSpare->quantity = $request->get('quantity')[$key];
                    $listOfMachineryImportedSpare->remaining_quantity = $request->get('remaining_quantity')[$key];
                    $listOfMachineryImportedSpare->required_quantity = $request->get('required_quantity')[$key];
                    $listOfMachineryImportedSpare->machinery_type = $request->get('machinery_type')[$key];
                    $listOfMachineryImportedSpare->hs_code = $request->get('hs_code')[$key];
                    $listOfMachineryImportedSpare->bill_loading_no = $request->get('bill_loading_no')[$key];
                    $listOfMachineryImportedSpare->bill_loading_date = (!empty($request->get('bill_loading_date')[$key]) ? date('Y-m-d', strtotime($request->get('bill_loading_date')[$key])) : null);
                    $listOfMachineryImportedSpare->invoice_no = $request->get('invoice_no')[$key];
                    $listOfMachineryImportedSpare->invoice_date = (!empty($request->get('invoice_date')[$key]) ? date('Y-m-d', strtotime($request->get('invoice_date')[$key])) : null);
                    $listOfMachineryImportedSpare->total_value_as_per_invoice = $request->get('total_value_as_per_invoice')[$key]? $request->get('total_value_as_per_invoice')[$key] : 0;
                    $listOfMachineryImportedSpare->total_value_equivalent_usd = $request->get('total_value_equivalent_usd')[$key]? $request->get('total_value_equivalent_usd')[$key] : 0;
                    $listOfMachineryImportedSpare->total_value_ccy = $request->get('total_value_ccy')[$key]? $request->get('total_value_ccy')[$key] : 0;
                    $listOfMachineryImportedSpare->save();
                    $listOfMachineryImportedSpareIDs[] = $listOfMachineryImportedSpare->id;
                }

                if (count($listOfMachineryImportedSpareIDs) > 0) {
                    ListOfMachineryImportedSpareParts::where('app_id', $appData->id)
                        ->where('process_type_id', $this->process_type_id)
                        ->whereNotIn('id', $listOfMachineryImportedSpareIDs)
                        ->delete();
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


            /*
            * Department and Sub-department specification for application processing
            */
            $department_id = CommonFunction::getDeptIdByCompanyId($working_company_id);

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

            if(empty($processData->approval_center_id) && Session::has('brInfo.approval_center_id'))
            {
                $processData->approval_center_id = Session::get('brInfo.approval_center_id');
            }

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
                $unfixed_amount_array = $this->importPermissionService->unfixedAmountsForPayment($payment_config);

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

            $this->importPermissionService->cleanLoadData();
            
            /*
            * if action is submitted and application status is equal to draft
            * and have payment configuration then, generate a tracking number
            * and go to payment initiator function.
            */
            if ($request->get('actionBtn') == 'Submit' && $processData->status_id == -1) {

                $applicationInProcessing = CommonFunction::applicationInProcessing($this->process_type_id, $processData->id);
                if ($applicationInProcessing) {
                    Session::flash('error', "Sorry! You already have pending application in processing. [IRC-1105]");
                    return redirect()->back();
                }

                if (empty($processData->tracking_no)) {
                    $prefix = 'IP-' . date("dMY") . '-';
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
                Session::flash('error', 'Failed due to Application Status Conflict. Please try again later! [IP-1023]');
            }

            DB::commit();
            return redirect('import-permission/list/' . Encryption::encodeId($this->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('IPAppStore : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [IP-1060]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [IP-1060]');
            return redirect()->back()->withInput();
        }
    }
    
    public function preview()
    {
        return view("ImportPermission::preview");
    }

    public function uploadDocument()
    {
        return View::make('ImportPermission::ajaxUploadFile');
    }

    public function getDocList(Request $request)
    {
        $viewMode = $request->get('viewMode');
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
                $document = $this->importPermissionService->getAttachment($attachment_key);
            }
        } else {
            $document = $this->importPermissionService->getAttachment($attachment_key);
        }

        $html = strval(view("ImportPermission::documents", compact('document', 'viewMode')));
        return response()->json(['html' => $html]);
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
            return 'Sorry! this is a request without proper way. [IP-1022]';
        }

        return view("ImportPermission::business-class-modal");
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
                    'html' => "<h4 class='custom-err-msg'>Sorry! No information found try again or contact with support help desk.[IP-10103]</h4>"
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

            ImportPermission::where('id', $appId)->update(['gf_manual_payment_id' => $paymentInfo->id]);

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

            return redirect('process/import-permission/view-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('IrcGovtManualPayment: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [IP-1025]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . "[IP-1025]");
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
                    Session::flash('error', 'This is an invalid status, it\'s not possible to get the next status. [IP-911]');
                    return redirect('process/import-permission/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
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

            return redirect('process/import-permission/view-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('IRCAfterPayment: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [IP-102]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [IP-102]');
            return redirect('process/import-permission/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
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
            //    $processData->status_id = 1; // Submitted
            //    $processData->desk_id = 1;

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

            return redirect('process/import-permission/view-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('IRCAfterCounterPayment: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [IP-103]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [IP-103]');
            return redirect('process/import-permission/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        }
    }

    public static function IpMachineryImportedApproved($ref_no, $ipRefAppTrackingNo)
    {
        $listOfMachineryImportedSpare = ListOfMachineryImportedSpareParts::where('app_id', $ref_no)->get();
        
        $listOfMachineryImportedMaster = MasterMachineryImported::where('ref_app_tracking_no', $ipRefAppTrackingNo)->get();
        if ($listOfMachineryImportedMaster->isEmpty()) {
            foreach ($listOfMachineryImportedSpare as $machineryImportedSpare) {
                $machineryImportedMaster = new MasterMachineryImported();
                $machineryImportedMaster->ref_app_tracking_no = $ipRefAppTrackingNo;
                $machineryImportedMaster->name = $machineryImportedSpare->name;
                $machineryImportedMaster->quantity = $machineryImportedSpare->quantity;
                $machineryImportedMaster->total_imported = $machineryImportedSpare->required_quantity;
                $machineryImportedMaster->amendment_type = 'add';
                $machineryImportedMaster->status = $machineryImportedSpare->status;
                $machineryImportedMaster->save();
            }
        } else {
            foreach ($listOfMachineryImportedSpare as $machineryImportedSpare) {
                foreach ($listOfMachineryImportedMaster as $machineryImportedMaster) {
                    if (MasterMachineryImported::processName($machineryImportedSpare->name) == MasterMachineryImported::processName($machineryImportedMaster->name)) {
                        $machineryImportedMaster->total_imported += $machineryImportedSpare->required_quantity;
                        $machineryImportedSpare->remaining_quantity = $machineryImportedMaster->quantity - $machineryImportedMaster->total_imported;
                        $machineryImportedMaster->save();
                        $machineryImportedSpare->save();
                        break;
                    }
                }
            }
        }
    }

    public static function generateAttachmentKey($organization_id) {
        $organization_key = "";

        switch ($organization_id) {
            case 1: // Joint Venture
                $organization_key = "joint_venture";
                break;
            case 2: // Foreign
                $organization_key = "foreign";
                break;
            case 3: // Local
                $organization_key = "local";
                break;
            default:
        }

        return "ip_" . $organization_key;
    }
}