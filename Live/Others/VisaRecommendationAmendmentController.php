<?php

namespace App\Modules\VisaRecommendationAmendment\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Libraries\UtilFunction;
use App\Modules\Apps\Models\Airports;
use App\Modules\Apps\Models\AppDocuments;
use App\Modules\BasicInformation\Controllers\BasicInformationController;
use App\Modules\BasicInformation\Models\EA_WithoutGovtVatService;
use App\Modules\ProcessPath\Models\DeptApplicationTypes;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\Settings\Models\Attachment;
use App\Modules\Settings\Models\Currencies;
use App\Modules\Settings\Models\HighComissions;
use App\Modules\SonaliPayment\Controllers\SonaliPaymentController;
use App\Modules\SonaliPayment\Models\PaymentConfiguration;
use App\Modules\SonaliPayment\Models\PaymentDetails;
use App\Modules\SonaliPayment\Models\PaymentDistribution;
use App\Modules\SonaliPayment\Models\SonaliPayment;
use App\Modules\Users\Models\AreaInfo;
use App\Modules\Users\Models\Countries;
use App\Modules\VisaRecommendation\Models\VR_TravelPurpose;
use App\Modules\VisaRecommendationAmendment\Models\VisaRecommendationAmendment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Mockery\Exception;
//use mPDF;
use Mpdf\Mpdf;

class VisaRecommendationAmendmentController extends Controller
{
    protected $process_type_id;
    protected $aclName;

    public function __construct()
    {
        $this->process_type_id = 10;
        $this->aclName = 'VisaRecommendationAmendment';
    }

    /*
     * application form
     */
    public function applicationForm(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [VRAC-1001]';
        }

        if (!ACL::getAccsessRight($this->aclName, '-A-')) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>You have no access right! Contact with system admin for more information [VRAC-971]</h4>"
            ]);
        }

        $company_id = Auth::user()->company_ids;
        // Check whether the applicant company is eligible and have approved basic information application
        if (CommonFunction::checkEligibilityAndBiApps($company_id) != 1) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<div class='btn-center'><h4 class='custom-err-msg'>Sorry! You have no approved Basic Information application for BIDA services. [VRAC-9991]</h4> <br/> <a href='/dashboard' class='btn btn-primary btn-sm'>Apply for Basic Information</a></div>"
            ]);
        }

        $app_category = DeptApplicationTypes::whereIn('id', [1, 2, 3, 4, 5])
            ->where('status', 1)
            ->where('is_archive', 0)
            ->get([
                'id', 'name', 'attachment_key', 'certificate_text', 'app_instruction'
            ]);
        if ($app_category->isEmpty()) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>Sorry! no Visa type available right now [VRA-1122]</h4>"
            ]);
        }

        try {

            // Checking the payment configuration for this service
            $payment_config = PaymentConfiguration::leftJoin('sp_payment_category', 'sp_payment_category.id', '=',
                'sp_payment_configuration.payment_category_id')
                ->where([
                    'sp_payment_configuration.process_type_id' => $this->process_type_id,
                    'sp_payment_configuration.payment_category_id' => 1,
                    'sp_payment_configuration.status' => 1,
                    'sp_payment_configuration.is_archive' => 0
                ])->first(['sp_payment_configuration.*', 'sp_payment_category.name']);

            if (empty($payment_config)) {
                return response()->json([
                    'responseCode' => 1,
                    'html' => "<h4 class='custom-err-msg'> Payment configuration not found ![VRA-10100]</h4>"
                ]);
            }

            $unfixed_amount_array = $this->unfixedAmountsForPayment($payment_config);
            $payment_config->amount = $unfixed_amount_array['total_unfixed_amount'] + $payment_config->amount;
            $payment_config->vat_on_pay_amount = $unfixed_amount_array['total_vat_on_pay_amount'];
            $department_id = CommonFunction::getDeptIdByCompanyId($company_id);
            $countries = Countries::where('country_status', 'Yes')->orderby('nicename')->lists('nicename', 'id');
            $nationality = Countries::where('country_status', 'Yes')->where('nationality', '!=', '')
                ->where('iso', '!=', 'BD')->orderby('nationality', 'asc')->lists('nationality', 'id');
            $airports = Airports::orderby('name')->lists('name', 'id');
            $travel_purpose = VR_TravelPurpose::where('status', 1)->where('is_archive', 0)->orderBy('id',
                'asc')->lists('name', 'id');
            $highCommission = HighComissions::where('is_active', 1)
                ->where('is_archive', 0)
                ->select('id', DB::raw('CONCAT(high_comissions.name, ", ", high_comissions.address) AS commission'))
                ->orderBy('commission', 'asc')
                ->lists('commission', 'id');

            $basicInfo = CommonFunction::getBasicInformationByCompanyId($company_id);

            $divisions = ['' => 'Select One'] + AreaInfo::where('area_type', 1)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $districts = ['' => 'Select One'] + AreaInfo::where('area_type', 2)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $thana = ['' => 'Select One'] + AreaInfo::orderby('area_nm')->where('area_type', 3)->lists('area_nm', 'area_id')->all();

            $public_html = strval(view("VisaRecommendationAmendment::application-form",
                compact( 'countries', 'highCommission', 'company_id',
                    'payment_config', 'app_category', 'nationality', 'airports', 'travel_purpose', 'department_id', 'basicInfo',
                    'divisions', 'districts', 'thana')));

            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (Exception $e) {
            Log::error('VRAAppForm: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [VRAC-1005]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . ' [VRAC-1005]' . "</h4>"
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
                ->where('app_documents.ref_id', $app_id)
                ->where('app_documents.process_type_id', $this->process_type_id);

            $document_query->leftJoin('attachment_type', 'attachment_type.id', '=', 'attachment_list.attachment_type_id')
                ->where('attachment_type.key', $attachment_key);

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
            $query = Attachment::leftJoin('attachment_type', 'attachment_type.id', '=', 'attachment_list.attachment_type_id')
                ->where('attachment_type.key', $attachment_key);
            if (Auth::user()->company->business_category == 2) { // 2=government; 3=both
                $query->whereIn('attachment_list.business_category', [2, 3]);
            } else {
                $query->whereIn('attachment_list.business_category', [1, 3]); // 1=private; 3=both
            }
            $document = $query->where('attachment_list.status', 1)
                ->where('attachment_list.is_archive', 0)
                ->orderBy('attachment_list.order')
                ->get(['attachment_list.*']);
        }

        $html = strval(view("VisaRecommendationAmendment::documents",
            compact('document', 'viewMode')));
        return response()->json(['html' => $html]);
    }
    /*
     * application store
     */
    public function appStore(Request $request)
    {
        $company_id = CommonFunction::getUserWorkingCompany();
        $dept_id = CommonFunction::getDeptIdByCompanyId($company_id);

        $app_id = (!empty($request->get('app_id')) ? Encryption::decodeId($request->get('app_id')) : '');
        $mode = (!empty($request->get('app_id')) ? '-E-' : '-A-');

        // Set permission mode and check ACL
        if (!ACL::getAccsessRight($this->aclName, $mode, $app_id)) {
            abort('400', "You have no access right! Please contact with system admin if you have any query. [VRAC-972]");
        }

        // Check whether the applicant company is eligible and have approved basic information application
        if (CommonFunction::checkEligibilityAndBiApps($company_id) != 1) {
            Session::flash('error',"Sorry! You have no approved Basic Information application for BIDA services. [VRAC-9992]");
            return redirect()->back();
        }

        // Get basic information
        $basicInfo = CommonFunction::getBasicInformationByCompanyId($company_id);
        if (empty($basicInfo)) {
            Session::flash('error', "Basic information data is not found! [VRAC-105]");
            return redirect()->back()->withInput();
        }

        // get visa recommendation new info & set session
        if ($request->get('actionBtn') == 'searchVRinfo') {
            if ($request->get('is_approval_online') == 'yes' && $request->has('ref_app_tracking_no')) {

                $refAppTrackingNo = trim($request->get('ref_app_tracking_no'));
                $getVRpprovedData = ProcessList::where('tracking_no', $refAppTrackingNo)
                    ->where('status_id', 25)
                    ->where('company_id', $company_id)
                    ->whereIn('process_type_id', [1]) // 1 = Visa Recommendation
                    ->first(['ref_id','tracking_no']);

                if (empty($getVRpprovedData)) {
                    Session::flash('error', 'Sorry! approved Visa Recommendation reference no. is not found or not allowed! [VRAC-111]');
                    return redirect()->back();
                }

                $getVRinfo = UtilFunction::checkVRCommonPoolData($getVRpprovedData->tracking_no, $getVRpprovedData->ref_id);

                if (empty($getVRinfo)) {
                    Session::flash('error', 'Sorry! Visa Recommendation not found by tracking no! [VRAC-1081]');
                    return redirect()->back();
                }

                Session::put('vrInfo', $getVRinfo->toArray());
                Session::put('vrInfo.is_approval_online', $request->get('is_approval_online'));
                Session::put('vrInfo.ref_app_tracking_no', $request->get('ref_app_tracking_no'));

                Session::flash('success', 'Successfully loaded Visa Recommendation data. Please proceed to next step');
                return redirect()->back();
            }
        }

        // Clean session data
        if ($request->get('actionBtn') == 'clean_load_data') {
            Session::forget('vrVisaRecord');
            Session::forget('vrInfo');
            Session::flash('success', 'Successfully cleaned data.');
            return redirect()->back();
        }

        // Check application category is valid or not
        $getAppType = DeptApplicationTypes::find($request->get('app_type_id'));
        if (empty($getAppType)) {
            Session::flash('error', "Unknown Visa type! [VRAC-1211]");
            return redirect()->back();
        }

        // Checking the payment configuration for this service
        $payment_config = PaymentConfiguration::leftJoin('sp_payment_category', 'sp_payment_category.id', '=',
            'sp_payment_configuration.payment_category_id')
            ->where([
                'sp_payment_configuration.process_type_id' => $this->process_type_id,
                'sp_payment_configuration.payment_category_id' => 1,  // Service Fee Payment
                'sp_payment_configuration.status' => 1,
                'sp_payment_configuration.is_archive' => 0,
            ])->first(['sp_payment_configuration.*', 'sp_payment_category.name']);
        if (!$payment_config) {
            Session::flash('error', "Payment configuration not found [VRAC-100]");
            return redirect()->back()->withInput();
        }

        // Checking the payment distributor under payment configuration
        $stakeDistribution = PaymentDistribution::where('sp_pay_config_id', $payment_config->id)
            ->where('status', 1)
            ->where('is_archive', 0)
            ->get(['id', 'stakeholder_ac_no', 'pay_amount', 'fix_status', 'purpose', 'purpose_sbl', 'distribution_type']);
        if ($stakeDistribution->isEmpty()) {
            Session::flash('error', "Stakeholder not found [VRAC-101]");
            return redirect()->back()->withInput();
        }

        //  Required Documents for attachment
        $attachment_key = $getAppType->attachment_key;
        $attachment_key = "vra" . $attachment_key;
        if ($dept_id == 1) {
            $attachment_key .= "cml";
        } else if ($dept_id == 2) {
            $attachment_key .= "i";
        } else {
            $attachment_key .= "comm";
        }

        $query = Attachment::leftJoin('attachment_type', 'attachment_type.id', '=', 'attachment_list.attachment_type_id')
            ->where('attachment_type.key', $attachment_key);
        if (Auth::user()->company->business_category == 2) { // 2=government; 3=both
            $query->whereIn('attachment_list.business_category', [2, 3]);
        } else {
            $query->whereIn('attachment_list.business_category', [1, 3]); // 1=private; 3=both
        }
        $doc_row = $query->where('attachment_list.status', 1)
            ->where('attachment_list.is_archive', 0)
            ->orderBy('attachment_list.order')
            ->get(['attachment_list.id', 'attachment_list.doc_name', 'attachment_list.doc_priority']);

        // Validation Rules when application submitted
        $rules = [];
        $messages = [];

        if ($request->get('actionBtn') != 'draft') {
            if (!$request->has('toggleCheck')) {
                Session::flash('error', 'In order to Submit please select at least one field for amendment. [VRAC-1035]');
                return redirect()->back();
            }

            $rules['is_approval_online'] = 'required';
            $rules['ref_app_tracking_no'] = 'required_if:is_approval_online,yes';
            $rules['manually_approved_vr_no'] = 'required_unless:is_approval_online,yes';
            $rules['issue_date_of_prev_vr'] = 'required';
            $rules['app_type_id'] = 'required|numeric';
            $rules['emp_name'] = 'required';
            $rules['emp_designation'] = 'required';
            $rules['emp_nationality_id'] = 'required|numeric';
            $rules['mission_country_id'] = 'required_if:visa_type_id,1,2,3,4|numeric';
            $rules['high_commision_id'] = 'required_if:visa_type_id,1,2,3,4|numeric';
            $rules['airport_id'] = 'required_if:visa_type_id,5|numeric';
            $rules['visa_purpose_id'] = 'required_if:visa_type_id,5|numeric';
            $rules['visa_purpose_others'] = 'required_if:visa_type_id,5';
            $rules['arrival_date'] = 'required_if:visa_type_id,5|date|date_format:d-M-Y';
            $rules['arrival_flight_no'] = 'required_if:visa_type_id,5';
            $rules['departure_date'] = 'required_if:visa_type_id,5|date|date_format:d-M-Y';
            $rules['departure_time'] = 'required_if:visa_type_id,5';
            $rules['departure_flight_no'] = 'required_if:visa_type_id,5';
            $rules['visa_purpose_others'] = 'required_if:visa_purpose_id,3';
            $rules['accept_terms'] = 'required';

            //Office Address rules
            $rules['office_division_id'] = 'required';
            $rules['office_district_id'] = 'required';
            $rules['office_thana_id'] = 'required';
            $rules['office_post_office'] = 'required';
            $rules['office_post_code'] = 'required';
            $rules['office_address'] = 'required';
            $rules['office_telephone_no'] = 'required';
            $rules['office_mobile_no'] = 'required';
            // $rules['office_fax_no'] = 'required';
            $rules['office_email'] = 'required';


            // attachment validation check
            if (count($doc_row) > 0) {
                foreach ($doc_row as $value) {
                    if ($value->doc_priority == 1){
                        $rules['validate_field_'.$value->id] = 'required';
                        $messages['validate_field_'.$value->id.'.required'] = $value->doc_name.', this file is required.';
                    }
                }
            }

            $messages['is_approval_online.required'] = 'Did you receive your approval online OSS? field is required.';
            $messages['ref_app_tracking_no.required_if'] = 'Please give your approved visa recommendation reference no field is required.';
            $messages['manually_approved_vr_no.required_unless'] = 'Please give your manually approved Visa Recommendation reference no field is required.';
            $messages['issue_date_of_prev_vr.required'] = 'Effective date of the previous VR field is required.';
            $messages['app_type_id.required'] = 'Visa type field is required.';
            $messages['app_type_id.numeric'] = 'Visa type must be numeric.';
            $messages['emp_name.required'] = 'Full Name field is required.';
            $messages['emp_designation.required'] = 'Position/ Designation field is required.';
            $messages['emp_nationality_id.required'] = 'Nationality field is required.';
            $messages['emp_nationality_id.numeric'] = 'Nationality must be numeric.';
            $messages['mission_country_id.required_if'] = 'Select desired country field is required.';
            $messages['mission_country_id.numeric'] = 'Select desired country must be numeric.';
            $messages['high_commision_id.required_if'] = 'Embassy/ High Commission field is required.';
            $messages['high_commision_id.numeric'] = 'Embassy/ High Commission must be numeric.';
            $messages['airport_id.required_if'] = 'Select your desired  airport field is required.';
            $messages['airport_id.numeric'] = 'Select your desired airport must be numeric.';
            $messages['visa_purpose_id.required_if'] = 'Purpose of visit field is required.';
            $messages['visa_purpose_id.numeric'] = 'Purpose of visit must be numeric.';
            $messages['visa_purpose_others.required_if'] = 'Purpose of visit field is required.';

            //Office Address messages
            $messages['office_division_id.required'] = 'Office Address Existing Division field is required.';
            $messages['office_district_id.required'] = 'Office Address Existing District field is required.';
            $messages['office_thana_id.required'] = 'Office Address Existing Police Station field is required.';
            $messages['office_post_office.required'] = 'Office Address Existing Post Office field is required.';
            $messages['office_post_code.required'] = 'Office Address Existing Post Code field is required.';
            $messages['office_address.required'] = 'Office Address Existing Address field is required.';
            $messages['office_telephone_no.required'] = 'Office Address Existing Telephone No field is required.';
            $messages['office_mobile_no.required'] = 'Office Address Existing Mobile No field is required.';
            // $messages['office_fax_no.required'] = 'Office Address Existing Fax No field is required.';
            $messages['office_email.required'] = 'Office Address Existing Email field is required.';

            // Amendment data validation
            if ($request->has('toggleCheck')) {
                foreach ($request->get('toggleCheck') as $key => $val) {
                    if ($key == 'n_visa_purpose_id' && $request->get('n_visa_purpose_id') == 3) {
                        $rules['n_visa_purpose_others'] = 'required';
                    } else {
                        $rules[$key] = 'required';
                    }
                }
                foreach ($request->get('toggleCheck') as $key => $val) {
                    $messages[$key . '.required'] = $key . 'This field is required because of the corresponding checkbox';
                }
            }
            $messages['n_visa_purpose_others.required'] = 'Visa Type others field is required';


        }
        $this->validate($request, $rules, $messages);

        try {
            DB::beginTransaction();

            if ($request->get('app_id')) {
                $appData = VisaRecommendationAmendment::find($app_id);
                $processData = ProcessList::where([
                    'process_type_id' => $this->process_type_id, 'ref_id' => $appData->id
                ])->first();
            } else {
                $appData = new VisaRecommendationAmendment();
                $processData = new ProcessList();
            }

            $processData->company_id = $company_id;
            $appData->is_approval_online = $request->get('is_approval_online');

            if ($request->get('is_approval_online') == 'yes') {
                $appData->ref_app_tracking_no = trim($request->get('ref_app_tracking_no'));
            } else {
                $appData->manually_approved_vr_no = $request->get('manually_approved_vr_no');
            }

            $appData->app_type_id = $request->get('app_type_id');
            $appData->issue_date_of_prev_vr = (!empty($request->get('issue_date_of_prev_vr')) ? date('Y-m-d',
                strtotime($request->get('issue_date_of_prev_vr'))) : null);

            // Company Information
            $appData->company_name = $basicInfo->company_name;
            $appData->company_name_bn = $basicInfo->company_name_bn;
            $appData->service_type = $basicInfo->service_type;
            $appData->reg_commercial_office = $basicInfo->reg_commercial_office;
            $appData->ownership_status_id = $basicInfo->ownership_status_id;
            $appData->organization_type_id = $basicInfo->organization_type_id;
            $appData->major_activities = $basicInfo->major_activities;

            // Information of Principal Promoter/ Chairman/ Managing Director/ State CEO
            $appData->ceo_country_id = $basicInfo->ceo_country_id;
            $appData->ceo_dob = $basicInfo->ceo_dob;
            $appData->ceo_passport_no = $basicInfo->ceo_passport_no;
            $appData->ceo_nid = $basicInfo->ceo_nid;
            $appData->ceo_full_name = $basicInfo->ceo_full_name;
            $appData->ceo_designation = $basicInfo->ceo_designation;
            $appData->ceo_district_id = $basicInfo->ceo_district_id;
            $appData->ceo_city = $basicInfo->ceo_city;
            $appData->ceo_state = $basicInfo->ceo_state;
            $appData->ceo_thana_id = $basicInfo->ceo_thana_id;
            $appData->ceo_post_code = $basicInfo->ceo_post_code;
            $appData->ceo_address = $basicInfo->ceo_address;
            $appData->ceo_telephone_no = $basicInfo->ceo_telephone_no;
            $appData->ceo_mobile_no = $basicInfo->ceo_mobile_no;
            $appData->ceo_fax_no = $basicInfo->ceo_fax_no;
            $appData->ceo_email = $basicInfo->ceo_email;
            $appData->ceo_father_name = $basicInfo->ceo_father_name;
            $appData->ceo_mother_name = $basicInfo->ceo_mother_name;
            $appData->ceo_spouse_name = $basicInfo->ceo_spouse_name;
            $appData->ceo_gender = $basicInfo->ceo_gender;

            // Office Address
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

            // (Proposed) Office Address
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

            // Factory Address
            $appData->factory_district_id = $basicInfo->factory_district_id;
            $appData->factory_thana_id = $basicInfo->factory_thana_id;
            $appData->factory_post_office = $basicInfo->factory_post_office;
            $appData->factory_post_code = $basicInfo->factory_post_code;
            $appData->factory_address = $basicInfo->factory_address;
            $appData->factory_telephone_no = $basicInfo->factory_telephone_no;
            $appData->factory_mobile_no = $basicInfo->factory_mobile_no;
            $appData->factory_fax_no = $basicInfo->factory_fax_no;
            $appData->factory_email = $basicInfo->factory_email;
            $appData->factory_mouja = $basicInfo->factory_mouja;

            $appData->emp_name = $request->get('emp_name');
            $appData->emp_designation = $request->get('emp_designation');
            $appData->emp_passport_no = $request->get('emp_passport_no');
            $appData->emp_nationality_id = $request->get('emp_nationality_id');
            $appData->mission_country_id = $request->get('mission_country_id');
            $appData->high_commision_id = $request->get('high_commision_id');
            $appData->airport_id = $request->get('airport_id');
            $appData->visa_purpose_id = $request->get('visa_purpose_id');
            $appData->visa_purpose_others = $request->get('visa_purpose_others');
            $appData->arrival_date = (!empty($request->get('arrival_date')) ? date('Y-m-d',
                strtotime($request->get('arrival_date'))) : null);
            $appData->arrival_time = (!empty($request->get('arrival_time')) ? date('H:i:s',
                strtotime($request->get('arrival_time'))) : '');
            $appData->arrival_flight_no = $request->get('arrival_flight_no');
            $appData->departure_date = (!empty($request->get('departure_date')) ? date('Y-m-d',
                strtotime($request->get('departure_date'))) : null);
            $appData->departure_time = (!empty($request->get('departure_time')) ? date('H:i:s',
                strtotime($request->get('departure_time'))) : '');
            $appData->departure_flight_no = $request->get('departure_flight_no');

            $appData->n_emp_name = $request->get('n_emp_name');
            $appData->n_emp_designation = $request->get('n_emp_designation');
            $appData->n_emp_passport_no = $request->get('n_emp_passport_no');
            $appData->n_emp_nationality_id = $request->get('n_emp_nationality_id');
            $appData->n_mission_country_id = $request->get('n_mission_country_id');
            $appData->n_high_commision_id = $request->get('n_high_commision_id');
            $appData->n_airport_id = $request->get('n_airport_id');
            $appData->n_visa_purpose_id = $request->get('n_visa_purpose_id');
            $appData->n_visa_purpose_others = $request->get('n_visa_purpose_others');
            $appData->n_arrival_date = (!empty($request->get('n_arrival_date')) ? date('Y-m-d',
                strtotime($request->get('n_arrival_date'))) : null);
            $appData->n_arrival_time = (!empty($request->get('n_arrival_time')) ? date('H:i:s',
                strtotime($request->get('n_arrival_time'))) : '');
            $appData->n_arrival_flight_no = $request->get('n_arrival_flight_no');
            $appData->n_departure_date = (!empty($request->get('n_departure_date')) ? date('Y-m-d',
                strtotime($request->get('n_departure_date'))) : null);
            $appData->n_departure_time = (!empty($request->get('n_departure_time')) ? date('H:i:s',
                strtotime($request->get('n_departure_time'))) : '');
            $appData->n_departure_flight_no = $request->get('n_departure_flight_no');

            //Authorized Person Information
            $appData->auth_full_name = $request->get('auth_full_name');
            $appData->auth_designation = $request->get('auth_designation');
            $appData->auth_mobile_no = $request->get('auth_mobile_no');
            $appData->auth_email = $request->get('auth_email');
            $appData->auth_image = $request->get('auth_image');

            if ($request->has('accept_terms')) {
                $appData->accept_terms = 1;
            }

            // store JSON data
            $data = [];
            $change_fields = '';
            $change_old_value = '';
            $change_new_value = '';

            $caption = $request->get('caption');
            $keys = $request->get('toggleCheck');
            $keys_count = count($keys);
            $i = 0;

            $highCommission = HighComissions::where('is_active', 1)
                ->where('is_archive', 0)
                ->select('id', DB::raw('CONCAT(high_comissions.name, ", ", high_comissions.address) AS commission'))
                ->lists('commission', 'id');

            if ($keys_count) {
                $nationality = Countries::where('country_status', 'Yes')->where('nationality', '!=', '')
                    ->where('iso', '!=', 'BD')->orderby('nationality', 'asc')->lists('nationality', 'id');
                $countries = Countries::where('country_status', 'Yes')->orderby('nicename')->lists('nicename', 'id');
                $divisions = AreaInfo::where('area_type', 1)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
                $districts = AreaInfo::where('area_type', 2)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
                $thana = AreaInfo::orderby('area_nm')->where('area_type', 3)->lists('area_nm', 'area_id')->all();

                $airports = Airports::orderby('name')->lists('name', 'id');
                $travel_purpose = VR_TravelPurpose::where('status', 1)->where('is_archive', 0)->orderBy('id',
                    'asc')->lists('name', 'id');

                foreach ($keys as $key => $value) {
                    $data1 = [];
                    $data1['caption'] = (isset($caption[$key]) ? $caption[$key] : '');
                    if ($key == 'n_emp_nationality_id') {
                        $data1['old'] = ($request->has(substr($key, 2)) ? $nationality[$request->get(substr($key,
                            2))] : '');
                        $data1['new'] = ($request->has($key) ? $nationality[$request->get($key)] : '');
                    } elseif ($key == 'n_airport_id') {
                        $data1['old'] = ($request->has(substr($key, 2)) ? $airports[$request->get(substr($key,
                            2))] : '');
                        $data1['new'] = ($request->has($key) ? $airports[$request->get($key)] : '');
                    } elseif ($key == 'n_visa_purpose_id') {
                        if ($request->get('visa_purpose_id') == 3) {
                            $data1['old'] = $request->get('visa_purpose_others');
                        } else {
                            $data1['old'] = ($request->has(substr($key, 2)) ? $travel_purpose[$request->get(substr($key,
                                2))] : '');
                        }

                        if ($request->get($key) == 3) {
                            $data1['new'] = $request->get('n_visa_purpose_others');
                        } else {
                            $data1['new'] = ($request->has($key) ? $travel_purpose[$request->get($key)] : '');
                        }
                    }
                    elseif ($key == 'n_mission_country_id') {
                        $data1['old'] = ($request->has(substr($key, 2)) ? $countries[$request->get(substr($key, 2))] : '');
                        $data1['new'] = ($request->has($key) ? $countries[$request->get($key)] : '');
                    }
                    elseif ($key == 'n_high_commision_id') {
                        $data1['old'] = ($request->has(substr($key, 2)) ? $highCommission[$request->get(substr($key,
                            2))] : '');
                        $data1['new'] = ($request->has($key) ? $highCommission[$request->get($key)] : '');
                    }elseif ($key == 'n_office_division_id') {
                        $data1['old'] = ($request->has(substr($key, 2)) ? $divisions[$request->get(substr($key,
                            2))] : '');
                        $data1['new'] = ($request->has($key) ? $divisions[$request->get($key)] : '');
                    }elseif ($key == 'n_office_district_id') {
                        $data1['old'] = ($request->has(substr($key, 2)) ? $districts[$request->get(substr($key,
                            2))] : '');
                        $data1['new'] = ($request->has($key) ? $districts[$request->get($key)] : '');
                    }elseif ($key == 'n_office_thana_id') {
                        $data1['old'] = ($request->has(substr($key, 2)) ? $thana[$request->get(substr($key,
                            2))] : '');
                        $data1['new'] = ($request->has($key) ? $thana[$request->get($key)] : '');
                    } else {
                        $data1['old'] = ($request->has(substr($key, 2)) ? $request->get(substr($key, 2)) : '');
                        $data1['new'] = ($request->has($key) ? $request->get($key) : '');
                    }

                    $data[] = $data1;

                    // Amendment data in string start
                    if ($i == 0) {
                        $change_fields .= $data1['caption'];
                        $change_old_value .= $data1['old'];
                        $change_new_value .= $data1['new'];
                    } elseif ($i == ($keys_count - 1)) {
                        $change_fields .= ' & ' . $data1['caption'];
                        $change_old_value .= ' & ' . $data1['old'];
                        $change_new_value .= ' & ' . $data1['new'];
                    } else {
                        $change_fields .= ', ' . $data1['caption'];
                        $change_old_value .= ', ' . $data1['old'];
                        $change_new_value .= ', ' . $data1['new'];
                    }
                    $i++;
                    // Amendment data in string end
                }
            }

            $appData->data = json_encode($data);
            $appData->change_fields = $change_fields;
            $appData->change_old_value = $change_old_value;
            $appData->change_new_value = $change_new_value;

            // If embassy/ high commission is changed then store this value
            if ($request->has('n_mission_country_id') &&
                $request->has('n_high_commision_id') &&
                $request->has('app_type_id') &&
                $request->has('issue_date_of_prev_vr') &&
                ($request->has('ref_app_tracking_no') || $request->has('manually_approved_vr_no')) ) {

                $pre_traking_no = $request->get('ref_app_tracking_no') ? $request->get('ref_app_tracking_no') : $request->get('manually_approved_vr_no');
                $pre_approved_date = date('d/m/Y', strtotime($request->get('issue_date_of_prev_vr')));
                $embassy_name_address = $highCommission[$request->get('n_high_commision_id')];
                $pre_visa_type = CommonFunction::getVisaTypeByAppTypeId($request->get('app_type_id'));

                $appData->change_embassy_high_com = "The earlier recommendation visa online ref no. $pre_traking_no dated $pre_approved_date shall be treated as canceled.<br><br>The above mentioned foreign national may be advised to approach the Embassy of the $embassy_name_address. With the copy of his appointment letter and other related documents for obtaining a $pre_visa_type.";
            }

            $appData->save();

            if ($request->get('actionBtn') == "draft") {
                $processData->status_id = -1;
                $processData->desk_id = 0;
            } else {
                if ($processData->status_id == 5) { // For shortfall application re-submission
                    $resubmission_data = CommonFunction::getReSubmissionJson($this->process_type_id, $app_id);

                    $processData->status_id = $resubmission_data['process_starting_status'];
                    $processData->desk_id = $resubmission_data['process_starting_desk'];
                    $processData->process_desc = 'Re-submitted from applicant';
                } else { // For new application submission
                    $processData->status_id = -1;
                    $processData->desk_id = 0;
                }
            }

            /*
             * Department and Sub-department specification for application processing
             */

            $deptSubDeptData = [
                'company_id' => $company_id,
                'department_id' => $dept_id,
                'app_type' => $request->get('app_type_id')
            ];
            $deptAndSubDept = CommonFunction::DeptSubDeptSpecification($this->process_type_id, $deptSubDeptData);
            $processData->department_id = $deptAndSubDept['department_id'];
            $processData->sub_department_id = $deptAndSubDept['sub_department_id'];

            $processData->ref_id = $appData->id;
            $processData->process_type_id = $this->process_type_id;
            $processData->process_desc = '';// for re-submit application
            //$processData->read_status = 0;
            // $processData->approval_center_id = UtilFunction::getApprovalCenterId($company_id);
            $processData->approval_center_id = $processData->department_id == 1 ? 1 : UtilFunction::getApprovalCenterId($company_id);
            $jsonData['Applied by'] = CommonFunction::getUserFullName();
            $jsonData['Email'] = Auth::user()->user_email;
            $jsonData['Mobile'] = Auth::user()->user_phone;
            $jsonData['Visa Type'] = CommonFunction::getVisaTypeByAppTypeId($request->get('app_type_id'));
            $processData['json_object'] = json_encode($jsonData);
            $processData->save();

            //Store attachment
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

            // Clean session data
            Session::forget("vrInfo");

            /*
             * if action is submitted and application status is equal to draft
             * and have payment configuration then, generate a tracking number
             * and go to payment initiator function.
             */
            if ($request->get('actionBtn') == 'Submit' && $processData->status_id == -1) {
                if (empty($processData->tracking_no)) {
                    $prefix = 'VRA-' . date("dMY") . '-';
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
                    'process_supper_name' => $processData->process_supper_name,
                    'process_sub_name' => $processData->process_sub_name,
                    'process_type_name' => 'Vis Recommendation Amendment',
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
                Session::flash('error', 'Failed due to Application Status Conflict. Please try again later! [BIC-1023]');
            }
            DB::commit();
            return redirect('visa-recommendation-amendment/list/' . Encryption::encodeId($this->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('VRAAppStore ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [VRAC-1011]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . "[VRAC-1011]");
            return redirect()->back()->withInput();
        }
    }

    /*
     * application view/edit
     */
    public function applicationEdit($appId, $openMode = '', Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.';
        }

        // it's enough to check ACL for view mode only
        if (!ACL::getAccsessRight($this->aclName, '-E-')) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>You have no access right! Contact with system admin for more information [VRAC-973]</h4>"
            ]);
        }

        try {
            $decodedAppId = Encryption::decodeId($appId);
            $app_category = DeptApplicationTypes::whereIn('id', [1, 2, 3, 4, 5])
                ->where('status', 1)
                ->where('is_archive', 0)
                ->get([
                    'id', 'name', 'attachment_key', 'certificate_text', 'app_instruction'
                ]);
            if ($app_category->isEmpty()) {
                return response()->json([
                    'responseCode' => 1, 'html' => "Sorry! no Visa type available right now [VRA-1123]"
                ]);
            }

            // get application,process info
            $appInfo = ProcessList::leftJoin('vra_apps as apps', 'apps.id', '=', 'process_list.ref_id')
                ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
                ->leftJoin('process_status as ps', function ($join){
                    $join->on('ps.id', '=', 'process_list.status_id');
                    $join->on('ps.process_type_id', '=', DB::raw($this->process_type_id));
                })
                ->leftJoin('sp_payment as sfp', 'sfp.id', '=', 'apps.sf_payment_id')
                ->where('process_list.ref_id', $decodedAppId)
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
                    'process_list.resend_deadline',
                    'user_desk.desk_name',
                    'ps.status_name',
                    'ps.color',
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

            $department_id = CommonFunction::getDeptIdByCompanyId($appInfo->company_id);

            $countries = Countries::where('country_status', 'Yes')->orderby('nicename')->lists('nicename', 'id');
            $nationality = Countries::where('country_status', 'Yes')->where('nationality', '!=', '')
                ->where('iso', '!=', 'BD')->orderby('nationality', 'asc')->lists('nationality', 'id');
            $airports = Airports::orderby('name')->lists('name', 'id');
            $travel_purpose = VR_TravelPurpose::where('status', 1)->where('is_archive', 0)->orderBy('id',
                'asc')->lists('name', 'id');
            $highCommission = HighComissions::where('is_active', 1)
                ->where('is_archive', 0)
                ->select('id', DB::raw('CONCAT(high_comissions.name, ", ", high_comissions.address) AS commission'))
                ->orderBy('commission', 'asc')
                ->lists('commission', 'id');

            $company_id = Auth::user()->company_ids;
            $basicInfo = CommonFunction::getBasicInformationByCompanyId($company_id);

            $divisions = ['' => 'Select One'] + AreaInfo::where('area_type', 1)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $districts = ['' => 'Select One'] + AreaInfo::where('area_type', 2)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $thana = ['' => 'Select One'] + AreaInfo::orderby('area_nm')->where('area_type', 3)->lists('area_nm', 'area_id')->all();

            $public_html = strval(view("VisaRecommendationAmendment::application-form-edit",
                compact( 'appInfo', 'countries', 'highCommission', 'app_category', 'nationality', 'airports', 'travel_purpose', 'department_id', 'basicInfo', 'divisions', 'districts', 'thana')));

            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (\Exception $e) {
            Log::error('VRAViewEditForm: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [VRAC-1015]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . "[VRAC-1015]" . "</h4>"
            ]);
        }
    }

    public function applicationView($appId, Request $request)
    {
        if (!$request->ajax()){
            return 'Sorry! this is a request without proper way. [VRAC-1002]';
        }

        if (!ACL::getAccsessRight($this->aclName, '-V-')){
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>You have no access right! Contact with system admin for more information. [VRAC-974]</h4>"
            ]);
        }

        try{
            $decodedAppId = Encryption::decodeId($appId);

            // get application,process info
            $appInfo = ProcessList::leftJoin('vra_apps as apps', 'apps.id', '=', 'process_list.ref_id')
                ->leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
                ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
                ->leftJoin('process_status as ps', function ($join) {
                    $join->on('ps.id', '=', 'process_list.status_id');
                    $join->on('ps.process_type_id', '=', DB::raw($this->process_type_id));
                })
                ->leftJoin('divisional_office', 'divisional_office.id', '=', 'process_list.approval_center_id')
                // Reference application
                ->leftJoin('process_list as ref_process', 'ref_process.tracking_no', '=', 'apps.ref_app_tracking_no')
                ->leftJoin('process_type as ref_process_type', 'ref_process_type.id', '=', 'ref_process.process_type_id')

                ->leftJoin('dept_application_type as app_type', 'app_type.id', '=', 'apps.app_type_id')// visa type

                ->leftJoin('sp_payment as sfp', 'sfp.id', '=', 'apps.sf_payment_id')
                ->leftJoin('sp_payment as gfp', 'gfp.id', '=', 'apps.gf_payment_id')

                ->leftJoin('country_info as emp_nationality', 'emp_nationality.id', '=', 'apps.emp_nationality_id')
                ->leftJoin('country_info as n_emp_nationality', 'n_emp_nationality.id', '=', 'apps.n_emp_nationality_id')
                ->leftJoin('country_info as mission_country', 'mission_country.id', '=', 'apps.mission_country_id')
                ->leftJoin('country_info as n_mission_country', 'n_mission_country.id', '=', 'apps.n_mission_country_id')
                ->leftJoin('high_comissions', 'high_comissions.id', '=', 'apps.high_commision_id')
                ->leftJoin('high_comissions as n_high_comissions', 'n_high_comissions.id', '=', 'apps.n_high_commision_id')
                ->leftJoin('airports', 'airports.id', '=', 'apps.airport_id')
                ->leftJoin('airports as n_airports', 'n_airports.id', '=', 'apps.n_airport_id')
                ->leftJoin('vr_travel_purpose', 'vr_travel_purpose.id', '=', 'apps.visa_purpose_id')
                ->leftJoin('vr_travel_purpose as n_vr_travel_purpose', 'n_vr_travel_purpose.id', '=', 'apps.n_visa_purpose_id')

                ->where('process_list.ref_id', $decodedAppId)
                ->where('process_list.process_type_id', $this->process_type_id)
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
                    'user_desk.desk_name',
                    'ps.status_name',
                    'apps.*',

                    'process_type.form_url',

                    'divisional_office.office_name as divisional_office_name',
                    'divisional_office.office_address as divisional_office_address',

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

                    'gfp.contact_name as gfp_contact_name',
                    'gfp.contact_email as gfp_contact_email',
                    'gfp.contact_no as gfp_contact_phone',
                    'gfp.address as gfp_contact_address',
                    'gfp.pay_amount as gfp_pay_amount',
                    'gfp.vat_on_pay_amount as gfp_vat_on_pay_amount',
                    'gfp.total_amount as gfp_total_amount',
                    'gfp.vat_on_transaction_charge as gfp_vat_on_transaction_charge',
                    'gfp.transaction_charge_amount as gfp_transaction_charge_amount',
                    'gfp.payment_status as gfp_payment_status',
                    'gfp.pay_mode as gfp_pay_mode',
                    'gfp.pay_mode_code as gfp_pay_mode_code',

                    'emp_nationality.nationality as emp_nationality_name',
                    'n_emp_nationality.nationality as n_emp_nationality_name',
                    'mission_country.nicename as mission_country_name',
                    'n_mission_country.nicename as n_mission_country_name',
                    'high_comissions.name as high_commision_name',
                    'high_comissions.address as high_commision_address',
                    'n_high_comissions.name as n_high_commision_name',
                    'n_high_comissions.address as n_high_commision_address',
                    'airports.name as airport_name',
                    'n_airports.name as n_airport_name',
                    'vr_travel_purpose.name as visa_purpose_name',
                    'n_vr_travel_purpose.name as n_visa_purpose_name',

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
                        'sp_payment_configuration.payment_category_id' => 2, // Government fee payment
                        'sp_payment_configuration.status' => 1,
                        'sp_payment_configuration.is_archive' => 0
                    ])->first(['sp_payment_configuration.*', 'sp_payment_category.name']);

                if (empty($payment_config)) {
                    return response()->json([
                        'responseCode' => 1,
                        'html' => "<h4 class='custom-err-msg'> Payment Configuration not found ![WPN-10100]</h4>"
                    ]);
                }

                $unfixed_amount_array = $this->unfixedAmountsForPayment($payment_config);
                $payment_config->amount = $unfixed_amount_array['total_unfixed_amount'] + $payment_config->amount;

                $vatFreeAllowed = BasicInformationController::isAllowedWithOutVAT($this->process_type_id);
                $payment_config->vat_on_pay_amount = ($vatFreeAllowed === true) ? 0 : $unfixed_amount_array['total_vat_on_pay_amount'];
            }

            $getAppType = DeptApplicationTypes::find($appInfo->app_type_id);
            $department_id = CommonFunction::getDeptIdByCompanyId($appInfo->company_id);

            $attachment_key = $getAppType->attachment_key;
            $attachment_key = "vra" . $attachment_key;
            if ($department_id == 1) {
                $attachment_key .= "cml";
            } else if ($department_id == 2) {
                $attachment_key .= "i";
            } else {
                $attachment_key .= "comm";
            }
            $document = AppDocuments::leftJoin('attachment_list', 'attachment_list.id', '=', 'app_documents.doc_info_id')
                ->leftJoin('attachment_type', 'attachment_type.id', '=', 'attachment_list.attachment_type_id')
                ->where('attachment_type.key', $attachment_key)
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
            $data['ref_app_url'] = '#';
            if (!empty($appInfo->ref_app_tracking_no)) {
                $data['ref_app_url'] = url('process/'.$appInfo->ref_process_type_key.'/view-app/'.Encryption::encodeId($appInfo->ref_application_ref_id) . '/' . Encryption::encodeId($appInfo->ref_application_process_type_id));
            }

            $company_id = $appInfo->company_id;
            $basicInfo = CommonFunction::getBasicInformationByCompanyId($company_id);
            $divisions = ['' => 'Select One'] + AreaInfo::where('area_type', 1)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $districts = ['' => 'Select One'] + AreaInfo::where('area_type', 2)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $thana = ['' => 'Select One'] + AreaInfo::orderby('area_nm')->where('area_type', 3)->lists('area_nm', 'area_id')->all();

            $public_html = strval(view("VisaRecommendationAmendment::application-form-view", compact( 'appInfo', 'document', 'payment_config', 'data', 'getAppType', 'basicInfo', 'divisions','districts','thana')));
            return response()->json(['responseCode' => 1, 'html' => $public_html]);

        }catch (\Exception $e){
            Log::error('VRAViewApp: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [VRAC-1016]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . "[VRAC-1016]" . "</h4>"
            ]);
        }
    }

    public function preview()
    {
        return view("VisaRecommendationAmendment::preview");
    }

    public function uploadDocument()
    {
        return View::make('VisaRecommendationAmendment::ajaxUploadFile');
    }

//    public function appFormPdf($appId)
//    {
//        if (!ACL::getAccsessRight($this->aclName, '-V-')) {
//            die('You have no access right! Please contact system administration for more information. [VRAC-975]');
//        }
//
//        try {
//            $decodedAppId = Encryption::decodeId($appId);
//            // get application,process info
//            $appInfo = ProcessList::leftJoin('vra_apps as apps', 'apps.id', '=', 'process_list.ref_id')
//                ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
//                ->leftJoin('process_status as ps', function ($join) {
//                    $join->on('ps.id', '=', 'process_list.status_id');
//                    $join->on('ps.process_type_id', '=', DB::raw($this->process_type_id));
//                })
//                ->leftJoin('dept_application_type as app_type', 'app_type.id', '=', 'apps.app_type_id')// visa type
//                ->leftJoin('sp_payment as sfp', 'sfp.id', '=', 'apps.sf_payment_id')
//                ->where('process_list.ref_id', $decodedAppId)
//                ->where('process_list.process_type_id', $this->process_type_id)
//                ->first([
//                    'process_list.id as process_list_id',
//                    'process_list.desk_id',
//                    'process_list.department_id',
//                    'process_list.process_type_id',
//                    'process_list.status_id',
//                    'process_list.locked_by',
//                    'process_list.locked_at',
//                    'process_list.ref_id',
//                    'process_list.tracking_no',
//                    'process_list.company_id',
//                    'process_list.process_desc',
//                    'process_list.submitted_at',
//                    'user_desk.desk_name',
//                    'ps.status_name',
//                    'ps.color',
//                    'apps.*',
//                    'app_type.name as app_type_name',
//
//                    'sfp.contact_name as sfp_contact_name',
//                    'sfp.contact_email as sfp_contact_email',
//                    'sfp.contact_no as sfp_contact_phone',
//                    'sfp.address as sfp_contact_address',
//                    'sfp.pay_amount as sfp_pay_amount',
//                    'sfp.vat_on_pay_amount as sfp_vat_on_pay_amount',
//                    'sfp.transaction_charge_amount as sfp_transaction_charge_amount',
//                    'sfp.vat_on_transaction_charge as sfp_vat_on_transaction_charge',
//                    'sfp.total_amount as sfp_total_amount',
//                    'sfp.payment_status as sfp_payment_status',
//                    'sfp.pay_mode as sfp_pay_mode',
//                    'sfp.pay_mode_code as sfp_pay_mode_code',
//                ]);
//
//            $countries = Countries::where('country_status', 'Yes')->orderby('nicename')->lists('nicename', 'id');
//            $nationality = Countries::where('country_status', 'Yes')->where('nationality', '!=',
//                '')->orderby('nationality', 'asc')->lists('nationality', 'id');
//            $airports = Airports::orderby('name')->lists('name', 'id');
//            $travel_purpose = VR_TravelPurpose::where('status', 1)->where('is_archive', 0)->orderBy('id',
//                'asc')->lists('name', 'id');
//            $embassy_name = HighComissions::where('id', $appInfo->high_commision_id)->first(['name', 'address']);
//            $new_embassy_name = HighComissions::where('id', $appInfo->n_high_commision_id)->first(['name', 'address']);
//
//            $document = AppDocuments::leftJoin('attachment_list', 'attachment_list.id', '=', 'app_documents.doc_info_id')
//                ->where('app_documents.ref_id', $decodedAppId)
//                ->where('app_documents.process_type_id', $this->process_type_id)
//                ->where('app_documents.doc_file_path', '!=', '')
//                ->get([
//                    'attachment_list.id',
//                    'attachment_list.doc_priority',
//                    'attachment_list.additional_field',
//                    'app_documents.id as document_id',
//                    'app_documents.doc_file_path as doc_file_path',
//                    'app_documents.doc_name',
//                ]);
//
//            $contents = view("VisaRecommendationAmendment::application-form-pdf",
//                compact( 'appInfo', 'countries', 'nationality', 'airports', 'document',
//                    'travel_purpose', 'embassy_name', 'new_embassy_name', 'basicInfo'))->render();
//
//            $mpdf = new mPDF([
//                'utf-8', // mode - default ''
//                'A4', // format - A4, for example, default ''
//                12, // font size - default 0
//                'dejavusans', // default font family
//                10, // margin_left
//                10, // margin right
//                10, // margin top
//                15, // margin bottom
//                10, // margin header
//                9, // margin footer
//                'P'
//            ]);
//            // $mpdf->Bookmark('Start of the document');
//            $mpdf->useSubstitutions;
//            $mpdf->SetProtection(array('print'));
//            $mpdf->SetDefaultBodyCSS('color', '#000');
//            $mpdf->SetTitle("BIDA One Stop Service");
//            $mpdf->SetSubject("Subject");
//            $mpdf->SetAuthor("Business Automation Limited");
//            $mpdf->autoScriptToLang = true;
//            $mpdf->baseScript = 1;
//            $mpdf->autoVietnamese = true;
//            $mpdf->autoArabic = true;
//
//            $mpdf->autoLangToFont = true;
//            $mpdf->SetDisplayMode('fullwidth');
//            $mpdf->SetHTMLFooter('
//                    <table width="100%">
//                        <tr>
//                            <td width="50%"><i style="font-size: 10px;">Download time: {DATE j-M-Y h:i a}</i></td>
//                            <td width="50%" align="right"><i style="font-size: 10px;">{PAGENO}/{nbpg}</i></td>
//                        </tr>
//                    </table>');
//            $stylesheet = file_get_contents('assets/stylesheets/appviewPDF.css');
//            $mpdf->setAutoTopMargin = 'stretch';
//            $mpdf->setAutoBottomMargin = 'stretch';
//            $mpdf->WriteHTML($stylesheet, 1);
//
//            $mpdf->WriteHTML($contents, 2);
//
//            $mpdf->defaultfooterfontsize = 10;
//            $mpdf->defaultfooterfontstyle = 'B';
//            $mpdf->defaultfooterline = 0;
//
//            $mpdf->SetCompression(true);
//            $mpdf->Output($appInfo->tracking_no . '.pdf', 'I');
//
//        } catch (\Exception $e) {
//            Log::error('VRAPdfView: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [VRAC-1115]');
//            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [VRAC-1115]');
//            return Redirect::back()->withInput();
//        }
//    }

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

        try {
            DB::beginTransaction();

            // 1 = Service Fee Payment
            if ($paymentInfo->payment_category_id == 1) {

                if ($processData->status_id != '-1') {
                    Session::flash('error', 'This is an invalid status, it\'s not possible to get the next status. [VRAC-912]');
                    return redirect('process/visa-recommendation-amendment/edit-app/'.Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
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
                if (!in_array($processData->status_id, [15,32])) {
                    Session::flash('error', 'This is an invalid status, it\'s not possible to get the next status.');
                    return redirect('process/visa-recommendation-amendment/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
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
            return redirect('process/visa-recommendation-amendment/view-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('VRAAfterPayment: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [VRAC-1060]');
            Session::flash('error','Something went wrong!, application not updated after payment. Error : ' . $e->getMessage().' [VRAC-1060]');
            return redirect('process/visa-recommendation-amendment/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
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
            }
            /*
            * Government payment submit
            * */
            elseif ($paymentInfo->is_verified == 1 && $paymentInfo->payment_category_id == 2) {

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
            return redirect('process/visa-recommendation-amendment/view-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('VRAAfterCounterPayment: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [VRAC-1061]');
            Session::flash('error',
                'Something went wrong!, application not updated after payment. Error : ' . $e->getMessage(). '[VRAC-1061]');
            return redirect('process/visa-recommendation-amendment/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        }
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
                Session::flash('error', "Payment configuration not found [VRAC-1456]");
                return redirect()->back()->withInput();
            }

            // Get payment distributor list
            $stakeDistribution = PaymentDistribution::where('sp_pay_config_id', $payment_config->id)
                ->where('status', 1)
                ->where('is_archive', 0)
                ->get(['id', 'stakeholder_ac_no', 'pay_amount', 'fix_status', 'purpose', 'purpose_sbl', 'distribution_type']);
            if ($stakeDistribution->isEmpty()) {
                Session::flash('error', "Stakeholder not found [VRAC-101]");
                return redirect()->back()->withInput();
            }

            // Check the Govt. vat fee is allowed or not: boolean
            $vatFreeAllowed = BasicInformationController::isAllowedWithOutVAT($this->process_type_id);

            // Store payment info
            DB::beginTransaction();

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

            $unfixed_amount_array = $this->unfixedAmountsForPayment($payment_config);

            $paymentInfo->receiver_ac_no = $account_numbers;
            $paymentInfo->pay_amount = $unfixed_amount_array['total_unfixed_amount'] + $payment_config->amount;
            $paymentInfo->vat_on_pay_amount = ($vatFreeAllowed === true) ? 0 : $unfixed_amount_array['total_vat_on_pay_amount'];
            $paymentInfo->total_amount = ($paymentInfo->pay_amount + $paymentInfo->vat_on_pay_amount);
            $paymentInfo->contact_name = $request->get('gfp_contact_name');
            $paymentInfo->contact_email = $request->get('gfp_contact_email');
            $paymentInfo->contact_no = $request->get('gfp_contact_phone');
            $paymentInfo->address = $request->get('gfp_contact_address');
            $paymentInfo->sl_no = 1; // Always 1
            $paymentInfo->save();

            VisaRecommendationAmendment::where('id', $appId)->update([
                'gf_payment_id' => $paymentInfo->id
            ]);

            if ($vatFreeAllowed) {
                SonaliPaymentController::vatFreeAuditStore($paymentInfo->id, $unfixed_amount_array['total_vat_on_pay_amount']);
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
            if ($request->get('actionBtn') == 'Submit' && $paymentInfo->id) {
                return redirect('spg/initiate-multiple/' . Encryption::encodeId($paymentInfo->id));
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('VRAPayment: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [VRAC-1025]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . "[VRAC-1025]");
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

            $unfixed_amount_array[5] = ($payment_config->amount / 100) * $vat_percentage;

        } elseif ($payment_config->payment_category_id === 3) {

        }

        $unfixed_amount_total = 0;
        $vat_on_pay_amount_total = 0;
        foreach ($unfixed_amount_array as $key => $amount) {
            // 4 = Vendor-Vat-Fee, 5 = Govt-Vat-Fee, 6 = Govt-Vendor-Vat-Fee
            if (in_array($key, [4, 5, 6])) {
                $vat_on_pay_amount_total += $amount;
            } else { // 1 = Vendor-Service-Fee, 2 = Govt-Service-Fee, 3 = Govt. Application Fee
                $unfixed_amount_total += $amount;
            }
        }

        return [
            'amounts' => $unfixed_amount_array,
            'total_unfixed_amount' => $unfixed_amount_total,
            'total_vat_on_pay_amount' => $vat_on_pay_amount_total,
        ];
    }
}