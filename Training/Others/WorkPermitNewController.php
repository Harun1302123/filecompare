<?php

namespace App\Modules\WorkPermitNew\Controllers;

use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Libraries\ImageProcessing;
use App\Libraries\UtilFunction;
use App\Modules\Apps\Models\AppDocuments;
use App\Modules\Apps\Models\PaymentMethod;
use App\Modules\BasicInformation\Controllers\BasicInformationController;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Models\ProcessWiseVisaTypes;
use App\Modules\Settings\Models\Attachment;
use App\Modules\Settings\Models\Currencies;
use App\Modules\SonaliPayment\Controllers\SonaliPaymentController;
use App\Modules\SonaliPayment\Models\PaymentConfiguration;
use App\Modules\SonaliPayment\Models\PaymentDetails;
use App\Modules\SonaliPayment\Models\PaymentDistribution;
use App\Modules\SonaliPayment\Models\SonaliPayment;
use App\Modules\Users\Models\AreaInfo;
use App\Modules\Users\Models\Countries;
use App\Modules\VisaRecommendation\Models\TravelVisaRecord;
use App\Modules\WorkPermitNew\Models\WorkPermitNew;
use App\Modules\WorkPermitNew\Models\WP_TravelVisaRecord;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Mockery\Exception;
use Mpdf\Mpdf;

class WorkPermitNewController extends Controller
{
    protected $process_type_id;
    protected $aclName;

    public function __construct()
    {
        $this->process_type_id = 2;
        $this->aclName = 'WorkPermitNew';
    }

    public function applicationForm(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.[WPNC-1001] ';
        }

        if (!ACL::getAccsessRight($this->aclName, '-A-')) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>You have no access right! Contact with system admin for more information. [WPNC-971]</h4>"
            ]);
        }


        // Check whether the applicant company is eligible and have approved basic information application
        $company_id = CommonFunction::getUserWorkingCompany();
        if (CommonFunction::checkEligibilityAndBiApps($company_id) != 1) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<div class='btn-center'><h4 class='custom-err-msg'>Sorry! You have no approved Basic Information application for BIDA services. [WPNC-9991]</h4> <br/> <a href='/dashboard' class='btn btn-primary btn-sm'>Apply for Basic Information</a></div>"
            ]);
        }

        // Check whether the applicant company's department will get this service
        $department_id = CommonFunction::getDeptIdByCompanyId($company_id);
        if ($department_id == 4) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>Sorry! The department is not allowed to apply to this application. [WPNC-1041]</h4>"
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
                    'html' => "<h4 class='custom-err-msg'> Payment Configuration not found ![WP-10100]</h4>"
                ]);
            }
            $unfixed_amount_array = $this->unfixedAmountsForPayment($payment_config);
            $payment_config->amount = $unfixed_amount_array['total_unfixed_amount'] + $payment_config->amount;
            $payment_config->vat_on_pay_amount = $unfixed_amount_array['total_vat_on_pay_amount'];

            $WP_visaTypes = ProcessWiseVisaTypes::leftJoin('visa_types', 'visa_types.id', '=',
                'process_wise_visa_type.visa_type_id')
                ->where([
                    'process_wise_visa_type.process_type_id' => $this->process_type_id,
                    'process_wise_visa_type.other_significant_id' => 1,
                    'process_wise_visa_type.status' => 1,
                    'process_wise_visa_type.is_archive' => 0
                ])
                ->orderBy('process_wise_visa_type.id', 'asc')
                ->select('visa_types.type', 'visa_types.id')
                ->lists('visa_types.type', 'visa_types.id');

            $divisions = ['' => 'Select One'] + AreaInfo::where('area_type', 1)->orderBy('area_nm',
                    'asc')->lists('area_nm', 'area_id')->all();
            $district_eng = AreaInfo::where('area_type', 2)->orderBy('area_nm', 'asc')->lists('area_nm',
                'area_id')->all();
            $thana_eng = AreaInfo::where('area_type', 3)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $nationality = Countries::where('country_status', 'Yes')->where('nationality', '!=', '')
                ->where('iso', '!=', 'BD')->orderby('nationality', 'asc')->lists('nationality', 'id');
            $paymentMethods = ['' => 'Select One'] + PaymentMethod::where('status', 1)->where('is_archive', 0)->lists('name', 'id')->all();
            $currencies = ['' => 'Select One'] + Currencies::where('is_archive', 0)->where('is_active', 1)->orderBy('code')->lists('code',
                    'id')->all();


            $travelVisaType = ProcessWiseVisaTypes::leftJoin('visa_types', 'visa_types.id', '=',
                'process_wise_visa_type.visa_type_id')
                ->where([
                    'process_wise_visa_type.process_type_id' => $this->process_type_id,
                    'process_wise_visa_type.other_significant_id' => 2,
                    'process_wise_visa_type.status' => 1,
                    'process_wise_visa_type.is_archive' => 0
                ])
                ->orderBy('process_wise_visa_type.id', 'asc')
                ->select('visa_types.type', 'visa_types.id')
                ->lists('visa_types.type', 'visa_types.id');

            //  Previous travel history attachments
            $travel_history_document = $this->getDocList('wpn_travel_history', 'type2');

            //  Required Documents for attachment
            $attachment_key = "wpn_";
            if ($department_id == 1) {
                $attachment_key .= "cml";
            } else if ($department_id == 2) {
                $attachment_key .= "i";
            } else {
                $attachment_key .= "comm";
            }
            $document = $this->getDocList($attachment_key);

            $process_type_id = $this->process_type_id;
            $business_category = Auth::user()->company->business_category;
            $viewMode = 'off';
            $mode = '-A-';

            $public_html = strval(view("WorkPermitNew::application-form",
                compact('process_type_id', 'business_category', 'viewMode', 'mode', 'WP_visaTypes', 'district_eng', 'department_id',
                    'nationality', 'paymentMethods', 'currencies', 'divisions', 'thana_eng',
                    'travelVisaType', 'payment_config', 'department_id', 'document', 'travel_history_document', 'company_id')));
            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (Exception $e) {
            Log::error('WPNAppForm: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [WPNC-1005]');
            return response()->json([
                'responseCode' => 1, 'html' => CommonFunction::showErrorPublic($e->getMessage()) . ' [WPNC-1005]'
            ]);
        }
    }

    public function appStore(Request $request)
    {
        // Check whether the applicant company is eligible and have approved basic information application
        $company_id = CommonFunction::getUserWorkingCompany();
        if (CommonFunction::checkEligibilityAndBiApps($company_id) != 1) {
            Session::flash('error', "Sorry! You have no approved Basic Information application for BIDA services. [WPNC-9992]");
            return redirect()->back();
        }

        // if submitted for get Visa Recommendation info
        if ($request->get('actionBtn') == 'searchVRinfo') {
            // if applicant have approved VR and Vr tracking no given then set session
            if ($request->get('last_vr') == 'yes' && $request->has('ref_app_tracking_no')) {

                $refAppTrackingNo = trim($request->get('ref_app_tracking_no'));
                $getVRpprovedData = ProcessList::leftjoin('vr_apps', 'vr_apps.id', '=', 'process_list.ref_id')
                    ->where('process_list.tracking_no', $refAppTrackingNo)
                    ->where('process_list.status_id', 25)
                    ->where('process_list.company_id', $company_id)
                    ->whereIn('process_list.process_type_id', [1, 10]) // 1 = Visa Recommendation
                    ->first(['process_list.ref_id', 'process_list.tracking_no', 'vr_apps.app_type_id']);

                if (empty($getVRpprovedData)) {
                    Session::flash('error', 'Sorry! approved Visa Recommendation reference no. is not found or not allowed! [WPNC-111]');
                    return redirect()->back();
                }
                //check on arrival ...Visa type id = 5
                if ($getVRpprovedData->app_type_id == 5) {
                    Session::flash('error', 'Sorry! Visa on arrival application can not applied for work permit. [WPNC-112]');
                    return redirect()->back();
                }

                $getVRinfo = UtilFunction::checkVRCommonPoolData($getVRpprovedData->tracking_no, $getVRpprovedData->ref_id);

                if (empty($getVRinfo)) {
                    Session::flash('error', 'Sorry! Visa Recommendation not found by tracking no! [VRAC-1081]');
                    return redirect()->back();
                }

                $getVRVisaRecord = TravelVisaRecord::where('app_id', $getVRpprovedData->ref_id)->where('status', 1)->get();

                if (count($getVRVisaRecord) > 0) {
                    Session::put('vrVisaRecord', $getVRVisaRecord->toArray());
                }

                Session::put('vrInfo', $getVRinfo->toArray());
                Session::put('vrInfo.last_vr', $request->get('last_vr'));
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

        // Set permission mode and check ACL
        $app_id = (!empty($request->get('app_id')) ? Encryption::decodeId($request->get('app_id')) : '');
        $mode = (!empty($request->get('app_id')) ? '-E-' : '-A-');
        if (!ACL::getAccsessRight($this->aclName, $mode, $app_id)) {
            abort('400', "You have no access right! Please contact with system admin if you have any query. [WPNC-972]");
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
            Session::flash('error', "Payment configuration not found [WPNC-100]");
            return redirect()->back()->withInput();
        }

        // Checking the payment distributor under payment configuration
        $stakeDistribution = PaymentDistribution::where('sp_pay_config_id', $payment_config->id)
            ->where('status', 1)
            ->where('is_archive', 0)
            ->get(['id', 'stakeholder_ac_no', 'pay_amount', 'fix_status', 'purpose', 'purpose_sbl', 'distribution_type']);
        if ($stakeDistribution->isEmpty()) {
            DB::rollback();
            Session::flash('error', "Stakeholder not found [WPNC-101]");
            return redirect()->back()->withInput();
        }

        // Get basic information
        $basicInfo = CommonFunction::getBasicInformationByCompanyId($company_id);
        if (empty($basicInfo)) {
            Session::flash('error', "Basic information data is not found! [WPNC-105]");
            return redirect()->back()->withInput();
        }
        $business_category = Auth::user()->company->business_category;
        $department_id = CommonFunction::getDeptIdByCompanyId($company_id);

        if ($request->get('th_emp_work_permit') == 'yes') {
            $travel_history_docs = $this->getDocList('wpn_travel_history', 'type2');
        }

        // Attachment key generate and fetch attachment
        $attachment_key = "wpn_";
        if ($department_id == 1) {
            $attachment_key .= "cml";
        } else if ($department_id == 2) {
            $attachment_key .= "i";
        } else {
            $attachment_key .= "comm";
        }

        $doc_row = $this->getDocList($attachment_key);

        // Validation Rules when application submitted
        $rules = [];
        $messages = [];
        if ($request->get('actionBtn') != 'draft') {

            if (empty($request->get('investor_photo_base64'))) {
                $rules['investor_photo_name'] = 'required';
            } else {
                $rules['investor_photo_base64'] = 'required';
            }

            $rules['last_vr'] = 'required';
            $rules['date_of_arrival'] = 'date|date_format:d-M-Y';
            $rules['work_permit_type'] = 'required|numeric';
            $rules['expiry_date_of_op'] = 'date|date_format:d-M-Y';
            $rules['duration_start_date'] = 'required|date|date_format:d-M-Y';
            $rules['duration_end_date'] = 'required|date|date_format:d-M-Y';
            $rules['desired_duration'] = 'required';
            $rules['emp_name'] = 'required';
            $rules['emp_designation'] = 'required';
            $rules['emp_surname'] = 'required';
            $rules['place_of_issue'] = 'required';
            $rules['emp_given_name'] = 'required';
            $rules['emp_nationality_id'] = 'required';
            $rules['emp_date_of_birth'] = 'required|date|date_format:d-M-Y';
            $rules['emp_place_of_birth'] = 'required';
            $rules['pass_issue_date'] = 'required|date|date_format:d-M-Y';
            $rules['pass_expiry_date'] = 'required|date|date_format:d-M-Y';

            $rules['basic_payment_type_id'] = 'required';
            $rules['basic_local_amount'] = 'numeric|required';
            $rules['basic_local_currency_id'] = 'required';
            // $rules['overseas_payment_type_id'] = 'required';
            // $rules['overseas_local_amount'] = 'numeric|required';
            // $rules['house_payment_type_id'] = 'required';
            // $rules['house_local_amount'] = 'numeric|required';
            // $rules['conveyance_payment_type_id'] = 'required';
            // $rules['conveyance_local_amount'] = 'numeric|required';
            // $rules['medical_payment_type_id'] = 'required';
            // $rules['medical_local_amount'] = 'numeric|required';
            // $rules['ent_payment_type_id'] = 'required';
            // $rules['ent_local_amount'] = 'numeric|required';
            // $rules['bonus_payment_type_id'] = 'required';
            // $rules['bonus_local_amount'] = 'numeric|required';

            // Travel history start
            $rules['travel_history'] = 'required';
            $rules['th_visa_type_id'] = 'required_if:travel_history,yes';
            $rules['th_visit_with_emp_visa'] = 'required_if:th_visa_type_id,7,8,10';
            $rules['th_emp_work_permit'] = 'required_if:th_visit_with_emp_visa,yes';

            $rules['th_emp_tin_no'] = 'required_if:th_emp_work_permit,yes';
            $rules['th_emp_wp_no'] = 'required_if:th_emp_work_permit,yes';
            $rules['th_emp_org_name'] = 'required_if:th_emp_work_permit,yes';
            $rules['th_emp_org_address'] = 'required_if:th_emp_work_permit,yes';
            $rules['th_org_district_id'] = 'required_if:th_emp_work_permit,yes';
            $rules['th_org_thana_id'] = 'required_if:th_emp_work_permit,yes';
            $rules['th_org_post_office'] = 'required_if:th_emp_work_permit,yes';
            $rules['th_org_post_code'] = 'required_if:th_emp_work_permit,yes';
            $rules['th_org_telephone_no'] = 'required_if:th_emp_work_permit,yes';
            $rules['th_org_email'] = 'required_if:th_emp_work_permit,yes';
            // Travel history end

            // attachment validation check
            if (isset($travel_history_docs) && count($travel_history_docs) > 0 && $request->get('th_emp_work_permit') == 'yes') {
                foreach ($travel_history_docs as $value) {
                    if ($value->doc_priority == 1){
                        $rules['validate_field_'.$value->id] = 'required';
                        $messages['validate_field_'.$value->id.'.required'] = $value->doc_name.', this file is required.';
                    }
                }
            }

            // attachment validation check
            if (count($doc_row) > 0) {
                foreach ($doc_row as $value) {
                    if ($value->doc_priority == 1){
                        $rules['validate_field_'.$value->id] = 'required';
                        $messages['validate_field_'.$value->id.'.required'] = $value->doc_name.', this file is required.';
                    }
                }
            }

            $messages['date_of_arrival.required'] = 'Date of arrival in Bangladesh field is required.';
            $messages['date_of_arrival.date'] = 'Date of arrival in Bangladesh must be date format.';
            $messages['work_permit_type.required'] = 'Type of visa field is required.';
            $messages['work_permit_type.numeric'] = 'Type of visa must be numeric.';
            $messages['duration_start_date.required'] = 'Desired duration for work permit start date field is required.';
            $messages['duration_start_date.date'] = 'Desired duration for work permit start date must be date format.';
            $messages['duration_end_date.required'] = 'Desired duration for work permit end date field is required.';
            $messages['duration_end_date.date'] = 'Desired duration for work permit end date must be date format.';
            $messages['desired_duration.required'] = 'Desired duration for work permit Desired duration field is required.';

            $messages['emp_name.required'] = 'General Information: Full Name field is required.';
            $messages['emp_designation.required'] = 'General Information: Position/ Designation field is required.';
            $messages['emp_surname.required'] = 'Passport Information: Surname field is required.';
            $messages['place_of_issue.required'] = 'Passport Information: Issuing authority field is required.';
            $messages['emp_given_name.required'] = 'Passport Information: Given Name field is required.';
            $messages['emp_nationality_id.required'] = 'Passport Information: Nationality field is required.';
            $messages['emp_date_of_birth.required'] = 'Passport Information: Date of Birth field is required.';
            $messages['emp_date_of_birth.date'] = 'Passport Information: Date of Birth must be date format.';
            $messages['emp_place_of_birth.required'] = 'Passport Information: Place of Birth field is required.';

            $messages['pass_issue_date.required'] = 'Passport Information: Date of issue field is required.';
            $messages['pass_issue_date.date'] = 'Passport Information: Date of issue must be date format.';
            $messages['pass_expiry_date.required'] = 'Passport Information: Date of expiry field is required.';
            $messages['pass_expiry_date.date'] = 'Passport Information: Date of expiry must be date format.';

            $messages['basic_payment_type_id.required'] = 'Compensation and Benefit Basic salary/ Honorarium Payment field is required.';
            $messages['basic_local_amount.required'] = 'Compensation and Benefit Basic salary/ Honorarium Amount field is required.';
            $messages['basic_local_amount.min'] = 'Compensation and Benefit Basic salary/ Honorarium Amount greater than zero(0).';
            $messages['basic_local_currency_id.required'] = 'Compensation and Benefit Basic salary/ Honorarium Currency field is required.';

            // Travel history start
            $messages['th_visa_type_id.required_if'] = 'Type of visa availed is required when you have visited to Bangladesh previously.';
            $messages['th_visit_with_emp_visa.required_unless'] = 'The visited to Bangladesh with Employment Visa field is required when visa type is not On Arrival.';
            $messages['th_visit_with_emp_visa.required_if'] = 'The visited to Bangladesh with Employment Visa field is required when PI, A3, E type of visa availed.';
            $messages['th_emp_work_permit.required_if'] = 'Have you received a work permit from Bangladesh? is required when you have visited Bangladesh with Employment Visa.';

            $messages['th_emp_tin_no.required_if'] = 'TIN Number is required of Previous work permit information in Bangladesh.';
            $messages['th_emp_wp_no.required_if'] = 'Last Work Permit Ref No is required of Previous work permit information in Bangladesh.';
            $messages['th_emp_org_name.required_if'] = 'Name of the employer organization is required of Previous work permit information in Bangladesh.';
            $messages['th_emp_org_address.required_if'] = 'Address of the organization is required of Previous work permit information in Bangladesh.';
            $messages['th_org_district_id.required_if'] = 'City/ District is required of Previous work permit information in Bangladesh.';
            $messages['th_org_thana_id.required_if'] = 'Thana/ Upazilla is required of Previous work permit information in Bangladesh.';
            $messages['th_org_post_office.required_if'] = 'Post Office is required of Previous work permit information in Bangladesh.';
            $messages['th_org_post_code.required_if'] = 'Post Code is required of Previous work permit information in Bangladesh.';
            $messages['th_org_telephone_no.required_if'] = 'Contact Number is required of Previous work permit information in Bangladesh.';
            $messages['th_org_email.required_if'] = 'Email is required of Previous work permit information in Bangladesh.';
            // Travel history end

            $rules['accept_terms'] = 'required';

            if ($business_category == 1) {
                $rules['ex_office_division_id'] = 'required|numeric';
                $rules['ex_office_district_id'] = 'required|numeric';
                $rules['ex_office_thana_id'] = 'required|numeric';
                $rules['ex_office_post_code'] = 'required';
                $rules['ex_office_address'] = 'required';
                $rules['ex_office_mobile_no'] = 'required|phone_or_mobile';
                $rules['ex_office_email'] = 'required|email';

                $messages['ex_office_division_id.required'] = 'Contact address of the expatriate in Bangladesh Division field is required';
                $messages['ex_office_district_id.required'] = 'Contact address of the expatriate in Bangladesh District field is required';
                $messages['ex_office_thana_id.required'] = 'Contact address of the expatriate in Bangladesh Police Station field is required';
                $messages['ex_office_post_code.required'] = 'Contact address of the expatriate in Bangladesh Post Code field is required';
                $messages['ex_office_address.required'] = 'Contact address of the expatriate in Bangladesh House, Flat/ Apartment, Road field is required';
                $messages['ex_office_mobile_no.required'] = 'Contact address of the expatriate in Bangladesh Mobile No. field is required';
                $messages['ex_office_email.required'] = 'Contact address of the expatriate in Bangladesh Email field is required';
            }


        }
        $this->validate($request, $rules, $messages);

        try {
            DB::beginTransaction();
            if ($request->get('app_id')) {
                $appData = WorkPermitNew::find($app_id);
                $processData = ProcessList::where([
                    'process_type_id' => $this->process_type_id, 'ref_id' => $appData->id
                ])->first();
            } else {
                $appData = new WorkPermitNew();
                $processData = new ProcessList();
            }
            $processData->company_id = $company_id;
            $appData->business_category = $business_category;

            // Company Information
            $appData->company_name = $basicInfo->company_name;
            $appData->company_name_bn = $basicInfo->company_name_bn;
            $appData->service_type = $basicInfo->service_type;
            $appData->reg_commercial_office = $basicInfo->reg_commercial_office;
            $appData->ownership_status_id = $basicInfo->ownership_status_id;
            $appData->organization_type_id = $basicInfo->organization_type_id;

            if ($business_category == 2 && $basicInfo->organization_type_id == 14) {
                $appData->organization_type_other = $basicInfo->organization_type_other;
            }
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
            if ($business_category == 2) {
                $appData->ceo_auth_letter = $basicInfo->ceo_auth_letter;
            }

            // Office Address
            $appData->office_division_id = $basicInfo->office_division_id;
            $appData->office_district_id = $basicInfo->office_district_id;
            $appData->office_thana_id = $basicInfo->office_thana_id;
            $appData->office_post_office = $basicInfo->office_post_office;
            $appData->office_post_code = $basicInfo->office_post_code;
            $appData->office_address = $basicInfo->office_address;
            $appData->office_telephone_no = $basicInfo->office_telephone_no;
            $appData->office_mobile_no = $basicInfo->office_mobile_no;
            $appData->office_fax_no = $basicInfo->office_fax_no;
            $appData->office_email = $basicInfo->office_email;

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

            // Applicant Photo upload
            // at first get photo if exists
            $appData->investor_photo = $request->get('investor_photo_name');
            if (isset($request->investor_photo_base64) && $request->investor_photo_base64 != '') {
                $yearMonth = date("Y") . "/" . date("m") . "/";
                $path = 'uploads/' . $yearMonth;
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                $splited = explode(',', substr($request->get('investor_photo_base64'), 5), 2);
                $imageData = $splited[1];
                $base64ResizeImage = base64_encode(ImageProcessing::resizeBase64Image($imageData, 300, 300));
                $base64ResizeImage = base64_decode($base64ResizeImage);
                $f = finfo_open();
                $file_name = trim(sprintf("%s", uniqid('BIDA_WPN_', true))) . str_replace(" ", "_", $request->get('investor_photo_name'));

                file_put_contents($path . $file_name, $base64ResizeImage);
                $appData->investor_photo = $yearMonth . $file_name;

            }

            //$appData->app_type_id = $getAppType->app_type_id;
            //$appData->app_type_mapping_id = $request->get('app_type_mapping_id');
            $appData->last_vr = $request->get('last_vr');
            $appData->work_permit_type = $request->get('work_permit_type');
            $appData->date_of_arrival = (!empty($request->get('date_of_arrival')) ? date('Y-m-d',
                strtotime($request->get('date_of_arrival'))) : null);
            $appData->expiry_date_of_op = (!empty($request->get('expiry_date_of_op')) ? date('Y-m-d',
                strtotime($request->get('expiry_date_of_op'))) : null);

            $appData->duration_start_date = (!empty($request->get('duration_start_date')) ? date('Y-m-d',
                strtotime($request->get('duration_start_date'))) : null);
            $appData->duration_end_date = (!empty($request->get('duration_end_date')) ? date('Y-m-d',
                strtotime($request->get('duration_end_date'))) : null);
            $appData->desired_duration = $request->get('desired_duration');
            $appData->duration_amount = $request->get('duration_amount');

            //insert also approved desired duration for desk user (process)
            $appData->approved_duration_start_date = (!empty($request->get('duration_start_date')) ? date('Y-m-d',
                strtotime($request->get('duration_start_date'))) : null);
            $appData->approved_duration_end_date = (!empty($request->get('duration_end_date')) ? date('Y-m-d',
                strtotime($request->get('duration_end_date'))) : null);
            $appData->approved_desired_duration = $request->get('desired_duration');
            $appData->approved_duration_amount = $request->get('duration_amount');
            //end

            if ($request->get('last_vr') == 'yes') {
                $appData->ref_app_tracking_no = trim($request->get('ref_app_tracking_no'));
                $appData->ref_app_approve_date = $request->get('ref_app_approve_date');
            } else {
                $appData->manually_approved_wp_no = $request->get('manually_approved_wp_no');
            }

            if ($business_category == 1) {
                $appData->nature_of_business = $request->get('nature_of_business');
                $appData->received_remittance = $request->get('received_remittance');
                $appData->auth_capital = $request->get('auth_capital');
                $appData->paid_capital = $request->get('paid_capital');

                $appData->ex_office_division_id = $request->get('ex_office_division_id');
                $appData->ex_office_district_id = $request->get('ex_office_district_id');
                $appData->ex_office_thana_id = $request->get('ex_office_thana_id');
                $appData->ex_office_post_office = $request->get('ex_office_post_office');
                $appData->ex_office_post_code = $request->get('ex_office_post_code');
                $appData->ex_office_address = $request->get('ex_office_address');
                $appData->ex_office_telephone_no = $request->get('ex_office_telephone_no');
                $appData->ex_office_mobile_no = $request->get('ex_office_mobile_no');
                $appData->ex_office_fax_no = $request->get('ex_office_fax_no');
                $appData->ex_office_email = $request->get('ex_office_email');
            }

            $appData->emp_passport_no = $request->get('emp_passport_no');
            $appData->emp_personal_no = $request->get('emp_personal_no');
            $appData->emp_surname = $request->get('emp_surname');
            $appData->emp_name = $request->get('emp_name');
            $appData->emp_designation = $request->get('emp_designation');
            $appData->brief_job_description = $request->get('brief_job_description');
//            $appData->major_activities = $request->get('major_activities');
            $appData->emp_given_name = $request->get('emp_given_name');
            $appData->emp_nationality_id = $request->get('emp_nationality_id');
            $appData->emp_date_of_birth = (!empty($request->get('emp_date_of_birth')) ? date('Y-m-d',
                strtotime($request->get('emp_date_of_birth'))) : null);
            $appData->emp_place_of_birth = $request->get('emp_place_of_birth');
            $appData->pass_issue_date = (!empty($request->get('pass_issue_date')) ? date('Y-m-d',
                strtotime($request->get('pass_issue_date'))) : null);
            $appData->pass_expiry_date = (!empty($request->get('pass_expiry_date')) ? date('Y-m-d',
                strtotime($request->get('pass_expiry_date'))) : null);
            $appData->place_of_issue = $request->get('place_of_issue');

            $appData->travel_history = $request->get('travel_history');
            $appData->th_visit_with_emp_visa = $request->get('th_visit_with_emp_visa');
            $appData->th_emp_work_permit = $request->get('th_emp_work_permit');
            $appData->th_emp_tin_no = $request->get('th_emp_tin_no');
            $appData->th_emp_wp_no = $request->get('th_emp_wp_no');
            $appData->th_emp_org_name = $request->get('th_emp_org_name');
            $appData->th_emp_org_address = $request->get('th_emp_org_address');
            $appData->th_org_district_id = $request->get('th_org_district_id');
            $appData->th_org_thana_id = $request->get('th_org_thana_id');
            $appData->th_org_post_office = $request->get('th_org_post_office');
            $appData->th_org_post_code = $request->get('th_org_post_code');
            $appData->th_org_telephone_no = $request->get('th_org_telephone_no');
            $appData->th_org_email = $request->get('th_org_email');
            // Previous travel history attachment store OLD
//            if ($request->get('th_first_work_permit') != "") {
//                $appData->th_first_work_permit = $request->get('th_first_work_permit');
//            }
//            if ($request->get('th_resignation_letter') != "") {
//                $appData->th_resignation_letter = $request->get('th_resignation_letter');
//            }
//            if ($request->get('th_release_order') != "") {
//                $appData->th_release_order = $request->get('th_release_order');
//            }
//            if ($request->get('th_last_extension') != "") {
//                $appData->th_last_extension = $request->get('th_last_extension');
//            }
//            if ($request->get('th_last_work_permit') != "") {
//                $appData->th_last_work_permit = $request->get('th_last_work_permit');
//            }
//            if ($request->get('th_income_tax') != "") {
//                $appData->th_income_tax = $request->get('th_income_tax');
//            }
            // Previous travel history attachment store OLD

            //manpower section
            if ($business_category == 1) {
                $appData->local_executive = $request->get('local_executive');
                $appData->local_stuff = $request->get('local_stuff');
                $appData->local_total = $request->get('local_total');
                $appData->foreign_executive = $request->get('foreign_executive');
                $appData->foreign_stuff = $request->get('foreign_stuff');
                $appData->foreign_total = $request->get('foreign_total');
                $appData->manpower_total = $request->get('manpower_total');
                $appData->manpower_local_ratio = $request->get('manpower_local_ratio');
                $appData->manpower_foreign_ratio = $request->get('manpower_foreign_ratio');
            }

            $appData->basic_payment_type_id = $request->get('basic_payment_type_id');
            $appData->basic_local_amount = empty($request->get('basic_local_amount')) ? null : $request->get('basic_local_amount');
            $appData->basic_local_currency_id = $request->get('basic_local_currency_id');

            $appData->overseas_payment_type_id = $request->get('overseas_payment_type_id');
            $appData->overseas_local_amount = empty($request->get('overseas_local_amount')) ? null : $request->get('overseas_local_amount');
            $appData->overseas_local_currency_id = $request->get('overseas_local_currency_id');

            $appData->house_payment_type_id = $request->get('house_payment_type_id');
            $appData->house_local_amount = empty($request->get('house_local_amount')) ? null : $request->get('house_local_amount');
            $appData->house_local_currency_id = $request->get('house_local_currency_id');

            $appData->conveyance_payment_type_id = $request->get('conveyance_payment_type_id');
            $appData->conveyance_local_amount = empty($request->get('conveyance_local_amount')) ? null : $request->get('conveyance_local_amount');
            $appData->conveyance_local_currency_id = $request->get('conveyance_local_currency_id');

            $appData->medical_payment_type_id = $request->get('medical_payment_type_id');
            $appData->medical_local_amount = empty($request->get('medical_local_amount')) ? null : $request->get('medical_local_amount');
            $appData->medical_local_currency_id = $request->get('medical_local_currency_id');

            $appData->ent_payment_type_id = $request->get('ent_payment_type_id');
            $appData->ent_local_amount = empty($request->get('ent_local_amount')) ? null : $request->get('ent_local_amount');
            $appData->ent_local_currency_id = $request->get('ent_local_currency_id');

            $appData->bonus_payment_type_id = $request->get('bonus_payment_type_id');
            $appData->bonus_local_amount = empty($request->get('bonus_local_amount')) ? null : $request->get('bonus_local_amount');
            $appData->bonus_local_currency_id = $request->get('bonus_local_currency_id');
            $appData->other_benefits = $request->get('other_benefits');

            $appData->cb_list = $this->getPaymentAndCurrencyData($request);

            //Authorized Person Information
            $appData->auth_full_name = $request->get('auth_full_name');
            $appData->auth_designation = $request->get('auth_designation');
            $appData->auth_mobile_no = $request->get('auth_mobile_no');
            $appData->auth_email = $request->get('auth_email');
            $appData->auth_image = $request->get('auth_image');

            if ($request->has('accept_terms')) {
                $appData->accept_terms = 1;
            }

            if ($request->get('actionBtn') == "draft") {
                $processData->status_id = -1;
                $processData->desk_id = 0;
            } else {
                if ($processData->status_id == 5) { // For shortfall application re-submission
//                    $getLastProcessInfo = ProcessHistory::where('process_id', $processData->id)->orderBy('id',
//                        'desc')->skip(1)->take(1)->first();
//                    $processData->status_id = 2; // re-submit
//                    // if application is in observation status from Meeting Chairperson(desk 6)
//                    // then resubmit to Assistant Director(desk 1) else resubmit to previous desk
//                    $processData->desk_id = $getLastProcessInfo->desk_id;

//                    if ($getLastProcessInfo->desk_id == 6) {
//                        $processData->desk_id = 1;
//                    }


                    $resubmission_data = CommonFunction::getReSubmissionJson($this->process_type_id, $app_id);
                    $processData->status_id = $resubmission_data['process_starting_status'];
                    $processData->desk_id = $resubmission_data['process_starting_desk'];
                    $processData->process_desc = 'Re-submitted form applicant';
                } elseif ($processData->status_id == 22) {  // For resubmit from mc
//                    $processData->status_id = 2;
//                    $processData->desk_id = 1; // 1 is Assistant Desk


                    $resubmission_data = CommonFunction::getReSubmissionJson($this->process_type_id, $app_id);

                    $processData->status_id = $resubmission_data['process_starting_status'];
                    $processData->desk_id = $resubmission_data['process_starting_desk'];
                } else {  // For new application submission
                    $processData->status_id = -1;
                    $processData->desk_id = 0;
                }
            }
            $appData->save();

            /*
             * Department and Sub-department specification for application processing
             */
            $deptSubDeptData = [
                'company_id' => $company_id,
                'department_id' => $department_id,
            ];
            $deptAndSubDept = CommonFunction::DeptSubDeptSpecification($this->process_type_id, $deptSubDeptData);
            $processData->department_id = $deptAndSubDept['department_id'];
            $processData->sub_department_id = $deptAndSubDept['sub_department_id'];

            $processData->ref_id = $appData->id;
            $processData->process_type_id = $this->process_type_id;
            $processData->process_desc = '';// for re-submit application
            //$processData->read_status = 0;
            $processData->approval_center_id = UtilFunction::getApprovalCenterId($company_id);

            $jsonData['Applied by'] = CommonFunction::getUserFullName();
            $jsonData['Email'] = Auth::user()->user_email;
            $jsonData['Mobile'] = Auth::user()->user_phone;
            $processData['json_object'] = json_encode($jsonData);
            $processData->save();

            //  Previous travel history attachments
            if ($request->get('th_emp_work_permit') == 'yes') {
                if (count($travel_history_docs) > 0) {
                    foreach ($travel_history_docs as $travel_history_doc) {
                        $history_doc = AppDocuments::firstOrNew([
                            'process_type_id' => $this->process_type_id,
                            'ref_id' => $appData->id,
                            'doc_info_id' => $travel_history_doc->id
                        ]);
                        $history_doc->doc_name = $travel_history_doc->doc_name;
                        $history_doc->doc_file_path = $request->get('validate_field_' . $travel_history_doc->id);
                        $history_doc->doc_section = 'type2';
                        $history_doc->save();
                    }
                }
            }
            //  Previous travel history attachments store end

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
                    $app_doc->doc_section = 'master';
                    $app_doc->save();
                }
            }

            // Visa Record Entry
            if ($request->has('travel_history') and $request->get('travel_history') == 'yes') {
                $visaRecordIds = [];
                foreach ($request->get('th_emp_duration_from') as $key => $value) {
                    if (empty($request->get('travel_visa_record_id')[$key])) {
                        $visaRecord = new WP_TravelVisaRecord();
                        $visaRecord->app_id = $appData->id;
                    } else {
                        $recordId = $request->get('travel_visa_record_id')[$key];
                        $visaRecord = WP_TravelVisaRecord::where('id', $recordId)->first();
                    }
                    $visaRecord->th_emp_duration_from = (!empty($request->get('th_emp_duration_from')[$key]) ? date('Y-m-d',
                        strtotime($request->get('th_emp_duration_from')[$key])) : null);
                    $visaRecord->th_emp_duration_to = (!empty($request->get('th_emp_duration_to')[$key]) ? date('Y-m-d',
                        strtotime($request->get('th_emp_duration_to')[$key])) : null);
                    $visaRecord->th_visa_type_id = $request->get('th_visa_type_id')[$key];
                    $visaRecord->th_visa_type_others = $request->get('th_visa_type_others')[$key];
                    $visaRecord->save();
                    $visaRecordIds[] = $visaRecord->id;
                }
                if (!empty($visaRecordIds)) {
                    WP_TravelVisaRecord::where('app_id', $appData->id)->whereNotIn('id', $visaRecordIds)->delete();
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
                // Payment Details By Stakeholders End
            }

            Session::forget('vrVisaRecord');
            Session::forget('vrInfo');

            /*
             * if action is submitted and application status is equal to draft
             * and have payment configuration then, generate a tracking number
             * and go to payment initiator function.
             */
            if ($request->get('actionBtn') == 'Submit' && $processData->status_id == -1) {
                if (empty($processData->tracking_no)) {
                    $prefix = 'WPN-' . date("dMY") . '-';
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
                    'process_type_name' => 'Work Permit New',
                    'remarks' => ''
                ];

                CommonFunction::sendEmailSMS('APP_RESUBMIT', $appInfo, $applicantEmailPhone);
            }

            if ($processData->status_id == -1) {
                Session::flash('success', 'Successfully updated the Application!');
            } elseif ($processData->status_id == 1) {
                Session::flash('success', 'Successfully Application Submitted !');
            } elseif (in_array($processData->status_id, [2, 8, 9])) {
                Session::flash('success', 'Successfully Application Re-Submitted !');
            } else {
                Session::flash('error', 'Failed due to Application Status Conflict. Please try again later! [WPNC-1023]');
            }
            DB::commit();
            return redirect('work-permit-new/list/' . Encryption::encodeId($this->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('WPNAppStore: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [WPNC-1011]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile()) . "[WPNC-1011]");
            return redirect()->back()->withInput();
        }
    }

    public function getPaymentAndCurrencyData($request)
    {
        $data = [];
        $paymentType = PaymentMethod::where('status', 1)->where('is_archive', 0)->lists('name', 'id');
        $paymentCurrency = Currencies::where('is_archive', 0)->where('is_active', 1)->orderBy('code')->lists('code',
            'id');

        if ($request->get('basic_local_amount') > 0) {
            $data[] = [
                'payment_type' => 'Basic salary',
                'pay_cat' => (isset($paymentType[$request->get('basic_payment_type_id')]) ? $paymentType[$request->get('basic_payment_type_id')] : ''),
                'pay_amt' => (isset($paymentCurrency[$request->get('basic_local_currency_id')]) ? $paymentCurrency[$request->get('basic_local_currency_id')] : '') . ' ' . $request->get('basic_local_amount'),
            ];
        }
        if ($request->get('overseas_local_amount') > 0) {
            $data[] = [
                'payment_type' => 'Overseas allowance',
                'pay_cat' => (isset($paymentType[$request->get('overseas_payment_type_id')]) ? $paymentType[$request->get('overseas_payment_type_id')] : ''),
                'pay_amt' => (isset($paymentCurrency[$request->get('overseas_local_currency_id')]) ? $paymentCurrency[$request->get('overseas_local_currency_id')] : '') . ' ' . $request->get('overseas_local_amount'),
            ];
        }
        if ($request->get('house_local_amount') > 0) {
            $data[] = [
                'payment_type' => 'House rent',
                'pay_cat' => (isset($paymentType[$request->get('house_payment_type_id')]) ? $paymentType[$request->get('house_payment_type_id')] : ''),
                'pay_amt' => (isset($paymentCurrency[$request->get('house_local_currency_id')]) ? $paymentCurrency[$request->get('house_local_currency_id')] : '') . ' ' . $request->get('house_local_amount'),
            ];
        }
        if ($request->get('conveyance_local_amount') > 0) {
            $data[] = [
                'payment_type' => 'Conveyance',
                'pay_cat' => (isset($paymentType[$request->get('conveyance_payment_type_id')]) ? $paymentType[$request->get('conveyance_payment_type_id')] : ''),
                'pay_amt' => (isset($paymentCurrency[$request->get('conveyance_local_currency_id')]) ? $paymentCurrency[$request->get('conveyance_local_currency_id')] : '') . ' ' . $request->get('conveyance_local_amount'),
            ];
        }
        if ($request->get('medical_local_amount') > 0) {
            $data[] = [
                'payment_type' => 'Medical allowance',
                'pay_cat' => (isset($paymentType[$request->get('medical_payment_type_id')]) ? $paymentType[$request->get('medical_payment_type_id')] : ''),
                'pay_amt' => (isset($paymentCurrency[$request->get('medical_local_currency_id')]) ? $paymentCurrency[$request->get('medical_local_currency_id')] : '') . ' ' . $request->get('medical_local_amount'),
            ];
        }
        if ($request->get('ent_local_amount') > 0) {
            $data[] = [
                'payment_type' => 'Entertainment allowance',
                'pay_cat' => (isset($paymentType[$request->get('ent_payment_type_id')]) ? $paymentType[$request->get('ent_payment_type_id')] : ''),
                'pay_amt' => (isset($paymentCurrency[$request->get('ent_local_currency_id')]) ? $paymentCurrency[$request->get('ent_local_currency_id')] : '') . ' ' . $request->get('ent_local_amount'),
            ];
        }
        if ($request->get('bonus_local_amount') > 0) {
            $data[] = [
                'payment_type' => 'Annual Bonus',
                'pay_cat' => (isset($paymentType[$request->get('bonus_payment_type_id')]) ? $paymentType[$request->get('bonus_payment_type_id')] : ''),
                'pay_amt' => (isset($paymentCurrency[$request->get('bonus_local_currency_id')]) ? $paymentCurrency[$request->get('bonus_local_currency_id')] : '') . ' ' . $request->get('bonus_local_amount'),
            ];
        }
        return json_encode($data);
    }

    public function applicationViewEdit($appId, $openMode = '', Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.';
        }

        $mode = 'SecurityBreak';
        $viewMode = 'SecurityBreak';
        if ($openMode == 'view') {
            $viewMode = 'on';
            $mode = '-V-';
        } else {
            if ($openMode == 'edit') {
                $viewMode = 'off';
                $mode = '-E-';
            }
        }

        // it's enough to check ACL for view mode only
        if (!ACL::getAccsessRight($this->aclName, $mode)) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>You have no access right! Contact with system admin for more information. [WPNC-973]</h4>"
            ]);
        }

        try {
            $decodedAppId = Encryption::decodeId($appId);
            $process_type_id = $this->process_type_id;

            // get application,process info
            $appInfo = ProcessList::leftJoin('wp_apps as apps', 'apps.id', '=', 'process_list.ref_id')
                ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
                ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                    $join->on('ps.id', '=', 'process_list.status_id');
                    $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
                })
                ->leftJoin('company_info', 'company_info.id', '=', 'process_list.company_id')
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
                    'process_list.resend_deadline',
                    'user_desk.desk_name',
                    'ps.status_name',
                    'ps.color',
                    'apps.*',

                    'company_info.business_category',

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

            $company_id = $appInfo->company_id;

            // Last remarks attachment
            $remarks_attachment = DB::select(DB::raw("select * from `process_documents`
            where `process_type_id` = $this->process_type_id and `ref_id` = $appInfo->process_list_id and `status_id` = $appInfo->status_id
            and `process_hist_id` = (SELECT MAX(process_hist_id) FROM process_documents WHERE ref_id=$appInfo->process_list_id AND process_type_id=$this->process_type_id AND status_id=$appInfo->status_id)
            ORDER BY id ASC"));

            $WP_visaTypes = ProcessWiseVisaTypes::leftJoin('visa_types', 'visa_types.id', '=',
                'process_wise_visa_type.visa_type_id')
                ->where([
                    'process_wise_visa_type.process_type_id' => $this->process_type_id,
                    'process_wise_visa_type.other_significant_id' => 1,
                    'process_wise_visa_type.status' => 1,
                    'process_wise_visa_type.is_archive' => 0
                ])
                ->orderBy('process_wise_visa_type.id', 'asc')
                ->select('visa_types.type', 'visa_types.id')
                ->lists('visa_types.type', 'visa_types.id');

            $divisions = ['' => 'Select One'] + AreaInfo::where('area_type', 1)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $district_eng = AreaInfo::where('area_type', 2)->orderBy('area_nm', 'asc')->lists('area_nm',
                'area_id')->all();
            $thana_eng = AreaInfo::where('area_type', 3)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $nationality = Countries::where('country_status', 'Yes')->where('nationality', '!=', '')
                ->where('iso', '!=', 'BD')->orderby('nationality', 'asc')->lists('nationality', 'id');
            $paymentMethods = ['' => 'Select One'] + PaymentMethod::where('status', 1)->where('is_archive', 0)->lists('name', 'id')->all();

            $currencies = ['' => 'Select One'] + Currencies::where('is_archive', 0)->where('is_active', 1)->orderBy('code')->lists('code','id')->all();


            $travelVisaType = ProcessWiseVisaTypes::leftJoin('visa_types', 'visa_types.id', '=',
                'process_wise_visa_type.visa_type_id')
                ->where([
                    'process_wise_visa_type.process_type_id' => $this->process_type_id,
                    'process_wise_visa_type.other_significant_id' => 2,
                    'process_wise_visa_type.status' => 1,
                    'process_wise_visa_type.is_archive' => 0
                ])
                ->orderBy('process_wise_visa_type.id', 'asc')
                ->select('visa_types.type', 'visa_types.id')
                ->lists('visa_types.type', 'visa_types.id');

            $visaRecords = [];
            if ($appInfo->travel_history == 'yes') {
                $visaRecords = WP_TravelVisaRecord::where('app_id', $appInfo->id)->where('status', 1)->get();
            }

            //  Previous travel history attachments
            $travel_history_document = $this->getDocList('wpn_travel_history', 'type2', $decodedAppId);

            $department_id = CommonFunction::getDeptIdByCompanyId($company_id);
            //  Required Documents for attachment
            $attachment_key = "wpn_";
            if ($department_id == 1) {
                $attachment_key .= "cml";
            } else if ($department_id == 2) {
                $attachment_key .= "i";
            } else {
                $attachment_key .= "comm";
            }
            $document = $this->getDocList($attachment_key, 'master', $decodedAppId);

            //get meting no and meting date ...
            $metingInformation = CommonFunction::getMeetingInfo($appInfo->process_list_id);

            // Get application basic company information
            $basic_company_info = CommonFunction::getBasicCompanyInfo($appInfo->company_id);

            $public_html = strval(view("WorkPermitNew::application-form-edit",
                compact('process_type_id', 'appInfo', 'WP_visaTypes', 'document', 'travel_history_document', 'thana_eng',
                    'paymentMethods', 'currencies', 'remarks_attachment', 'divisions', 'travelVisaType', 'visaRecords',
                    'district_eng', 'nationality', 'viewMode', 'mode', 'metingInformation', 'basic_company_info',
                    'company_id')));

            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (\Exception $e) {
            Log::error('WPNViewEditForm: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [WPNC-10115]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getline()) . "[WPNC-1015]" . "</h4>"
            ]);
        }
    }

    public function applicationView($appId, Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [WPNC-1002]';
        }

        $viewMode = "on";
        $mode = '-V-';

        // it's enough to check ACL for view mode only
        if (!ACL::getAccsessRight($this->aclName, $mode)) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>You have no access right! Contact with system admin for more information. [WPNC-974]</h4>"
            ]);
        }

        try {
            $decodedAppId = Encryption::decodeId($appId);
            $process_type_id = $this->process_type_id;

            $appInfo = ProcessList::leftJoin('wp_apps as apps', 'apps.id', '=', 'process_list.ref_id')
                ->leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
                ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
                ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                    $join->on('ps.id', '=', 'process_list.status_id');
                    $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
                })
                ->leftJoin('divisional_office', 'divisional_office.id', '=', 'process_list.approval_center_id')
                ->leftJoin('company_info', 'company_info.id', '=', 'process_list.company_id')
                ->leftJoin('sp_payment as sfp', 'sfp.id', '=', 'apps.sf_payment_id')
                ->leftJoin('sp_payment as gfp', 'gfp.id', '=', 'apps.gf_payment_id')
                ->leftJoin('visa_types', 'visa_types.id', '=', 'apps.work_permit_type')
                ->leftJoin('country_info as emp_nationality', 'emp_nationality.id', '=', 'apps.emp_nationality_id')

                // Reference application
                ->leftJoin('process_list as ref_process', 'ref_process.tracking_no', '=', 'apps.ref_app_tracking_no')
                ->leftJoin('process_type as ref_process_type', 'ref_process_type.id', '=', 'ref_process.process_type_id')

                // Compensation and Benefit
                ->leftJoin('payment_methods as basic_payment', 'basic_payment.id', '=', 'apps.basic_payment_type_id')
                ->leftJoin('currencies as basic_currency', 'basic_currency.id', '=', 'apps.basic_local_currency_id')
                ->leftJoin('payment_methods as overseas_payment', 'overseas_payment.id', '=', 'apps.overseas_payment_type_id')
                ->leftJoin('currencies as overseas_currency', 'overseas_currency.id', '=', 'apps.overseas_local_currency_id')
                ->leftJoin('payment_methods as house_payment', 'house_payment.id', '=', 'apps.house_payment_type_id')
                ->leftJoin('currencies as house_currency', 'house_currency.id', '=', 'apps.house_local_currency_id')
                ->leftJoin('payment_methods as conveyance_payment', 'conveyance_payment.id', '=', 'apps.conveyance_payment_type_id')
                ->leftJoin('currencies as conveyance_currency', 'conveyance_currency.id', '=', 'apps.conveyance_local_currency_id')
                ->leftJoin('payment_methods as medical_payment', 'medical_payment.id', '=', 'apps.medical_payment_type_id')
                ->leftJoin('currencies as medical_currency', 'medical_currency.id', '=', 'apps.medical_local_currency_id')
                ->leftJoin('payment_methods as ent_payment', 'ent_payment.id', '=', 'apps.ent_payment_type_id')
                ->leftJoin('currencies as ent_currency', 'ent_currency.id', '=', 'apps.ent_local_currency_id')
                ->leftJoin('payment_methods as bonus_payment', 'bonus_payment.id', '=', 'apps.bonus_payment_type_id')
                ->leftJoin('currencies as bonus_currency', 'bonus_currency.id', '=', 'apps.bonus_local_currency_id')

                ->leftJoin('area_info as ex_office_division', 'ex_office_division.area_id', '=', 'apps.ex_office_division_id')
                ->leftJoin('area_info as ex_office_district', 'ex_office_district.area_id', '=', 'apps.ex_office_district_id')
                ->leftJoin('area_info as ex_office_thana', 'ex_office_thana.area_id', '=', 'apps.ex_office_thana_id')
                ->leftJoin('area_info as th_org_district', 'th_org_district.area_id', '=', 'apps.th_org_district_id')
                ->leftJoin('area_info as th_org_thana', 'th_org_thana.area_id', '=', 'apps.th_org_thana_id')
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
                    'user_desk.desk_name',
                    'ps.status_name',
                    'apps.*',

                    'company_info.business_category',
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
                    'sfp.payment_status as sfp_payment_status',
                    'sfp.pay_mode as sfp_pay_mode',
                    'sfp.pay_mode_code as sfp_pay_mode_code',
                    'sfp.total_amount as sfp_total_amount',

                    'gfp.contact_name as gfp_contact_name',
                    'gfp.contact_email as gfp_contact_email',
                    'gfp.contact_no as gfp_contact_phone',
                    'gfp.address as gfp_contact_address',
                    'gfp.pay_amount as gfp_pay_amount',
                    'gfp.vat_on_pay_amount as gfp_vat_on_pay_amount',
                    'gfp.transaction_charge_amount as gfp_transaction_charge_amount',
                    'gfp.vat_on_transaction_charge as gfp_vat_on_transaction_charge',
                    'gfp.payment_status as gfp_payment_status',
                    'gfp.pay_mode as gfp_pay_mode',
                    'gfp.pay_mode_code as gfp_pay_mode_code',
                    'gfp.total_amount as gfp_total_amount',

                    'visa_types.type as visa_type_name',
                    'emp_nationality.nationality as emp_nationality_name',

                    // Compensation and Benefit
                    'basic_payment.name as basic_payment_type_name',
                    'basic_currency.code as basic_currency_code',
                    'overseas_payment.name as overseas_payment_type_name',
                    'overseas_currency.code as overseas_currency_code',
                    'house_payment.name as house_payment_type_name',
                    'house_currency.code as house_currency_code',
                    'conveyance_payment.name as conveyance_payment_type_name',
                    'conveyance_currency.code as conveyance_currency_code',
                    'medical_payment.name as medical_payment_type_name',
                    'medical_currency.code as medical_currency_code',
                    'ent_payment.name as ent_payment_type_name',
                    'ent_currency.code as ent_currency_code',
                    'bonus_payment.name as bonus_payment_type_name',
                    'bonus_currency.code as bonus_currency_code',

                    // Reference application
                    'ref_process.ref_id as ref_application_ref_id',
                    'ref_process.process_type_id as ref_application_process_type_id',
                    'ref_process_type.type_key as ref_process_type_key',

                    'ex_office_division.area_nm as ex_office_division_name',
                    'ex_office_district.area_nm as ex_office_district_name',
                    'ex_office_thana.area_nm as ex_office_thana_name',

                    'th_org_district.area_nm as th_org_district_name',
                    'th_org_thana.area_nm as th_org_thana_name',
                ]);

            // Checking the Government Fee Payment(GFP) configuration for this service
            if (in_array($appInfo->status_id, [15, 32])) {
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
                        'html' => "<h4 class='custom-err-msg'> Payment Configuration not found ![WPN-10100]</h4>"
                    ]);
                }

                $relevant_info_array = [
                    'approved_duration_start_date' => $appInfo->approved_duration_start_date,
                    'approved_duration_end_date' => $appInfo->approved_duration_end_date,
                    'process_type_id' => $this->process_type_id,
                ];
                $unfixed_amount_array = $this->unfixedAmountsForPayment($payment_config, $relevant_info_array);
                $payment_config->amount = $unfixed_amount_array['total_unfixed_amount'];
                // TODO : application dependent fee need to separate from payment configuration
                //$payment_config->amount = $unfixed_amount_array['total_unfixed_amount'] + $payment_config->amount;
                $vatFreeAllowed = BasicInformationController::isAllowedWithOutVAT($this->process_type_id);
                $payment_config->vat_on_pay_amount = ($vatFreeAllowed === true) ? 0 : $unfixed_amount_array['total_vat_on_pay_amount'];
            }

            $previous_travel_history = WP_TravelVisaRecord::leftJoin('visa_types', 'visa_types.id', '=', 'wp_travel_visa_record.th_visa_type_id')
                ->where('wp_travel_visa_record.app_id', $appInfo->id)
                ->get([
                    'wp_travel_visa_record.th_emp_duration_from',
                    'wp_travel_visa_record.th_emp_duration_to',
                    'wp_travel_visa_record.th_visa_type_others',
                    'visa_types.type'
                ]);

            // Previous travel history attachments
            $travel_history_document = $this->getDocument($decodedAppId, 'type2');

            // Required Documents for attachment
            $document = $this->getDocument($decodedAppId, 'master');

            // Meeting Information
            $metingInformation = CommonFunction::getMeetingInfo($appInfo->process_list_id);

            $data['ref_app_url'] = '#';
            if (!empty($appInfo->ref_app_tracking_no)) {
                $data['ref_app_url'] = url('process/'.$appInfo->ref_process_type_key.'/view-app/'.Encryption::encodeId($appInfo->ref_application_ref_id) . '/' . Encryption::encodeId($appInfo->ref_application_process_type_id));
            }

            $public_html = strval(view("WorkPermitNew::application-form-view",
                compact('viewMode', 'mode', 'appInfo', 'metingInformation', 'document', 'travel_history_document', 'payment_config', 'previous_travel_history','data')));
            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (\Exception $e) {
            Log::error('WPNViewForm: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [WPNC-10116]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . "[WPNC-1016]" . "</h4>"
            ]);
        }
    }

    public function uploadDocument()
    {
        return View::make('WorkPermitNew::ajaxUploadFile');
    }

    public function preview()
    {
        return view("WorkPermitNew::preview");
    }

    public function appFormPdf($appId)
    {
        if (!ACL::getAccsessRight($this->aclName, '-V-')) {
            die('You have no access right! Please contact system administration for more information. [WPNC-975]');
        }
        try {
            $decodedAppId = Encryption::decodeId($appId);
            $process_type_id = $this->process_type_id;

            // get application,process info
            $appInfo = ProcessList::leftJoin('wp_apps as apps', 'apps.id', '=', 'process_list.ref_id')
                ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
                ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                    $join->on('ps.id', '=', 'process_list.status_id');
                    $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
                })
                ->leftJoin('company_info', 'company_info.id', '=', 'process_list.company_id')
                ->leftJoin('sp_payment as sfp', 'sfp.id', '=', 'apps.sf_payment_id')
                ->leftJoin('sp_payment as gfp', 'gfp.id', '=', 'apps.gf_payment_id')
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
                    'user_desk.desk_name',
                    'ps.status_name',
                    'ps.color',
                    'apps.*',

                    'company_info.business_category',

                    'sfp.contact_name as sfp_contact_name',
                    'sfp.contact_email as sfp_contact_email',
                    'sfp.contact_no as sfp_contact_phone',
                    'sfp.address as sfp_contact_address',
                    'sfp.pay_amount as sfp_pay_amount',
                    'sfp.vat_on_pay_amount as sfp_vat_on_pay_amount',
                    'sfp.transaction_charge_amount as sfp_transaction_charge_amount',
                    'sfp.vat_on_transaction_charge as sfp_vat_on_transaction_charge',
                    'sfp.payment_status as sfp_payment_status',
                    'sfp.pay_mode as sfp_pay_mode',
                    'sfp.pay_mode_code as sfp_pay_mode_code',
                    'sfp.total_amount as sfp_total_amount',

                    'gfp.contact_name as gfp_contact_name',
                    'gfp.contact_email as gfp_contact_email',
                    'gfp.contact_no as gfp_contact_phone',
                    'gfp.address as gfp_contact_address',
                    'gfp.pay_amount as gfp_pay_amount',
                    'gfp.vat_on_pay_amount as gfp_vat_on_pay_amount',
                    'gfp.transaction_charge_amount as gfp_transaction_charge_amount',
                    'gfp.vat_on_transaction_charge as gfp_vat_on_transaction_charge',
                    'gfp.payment_status as gfp_payment_status',
                    'gfp.pay_mode as gfp_pay_mode',
                    'gfp.pay_mode_code as gfp_pay_mode_code',
                    'gfp.total_amount as gfp_total_amount',
                ]);

            $thana_eng = AreaInfo::where('area_type', 3)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $WP_visaTypes = ProcessWiseVisaTypes::leftJoin('visa_types', 'visa_types.id', '=',
                'process_wise_visa_type.visa_type_id')
                ->where([
                    'process_wise_visa_type.process_type_id' => $this->process_type_id,
                    'process_wise_visa_type.other_significant_id' => 1,
                    'process_wise_visa_type.status' => 1,
                    'process_wise_visa_type.is_archive' => 0
                ])
                ->orderBy('process_wise_visa_type.id', 'asc')
                ->select('visa_types.type', 'visa_types.id')
                ->lists('visa_types.type', 'visa_types.id');
            $divisions = AreaInfo::where('area_type', 1)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $district_eng = AreaInfo::where('area_type', 2)->orderBy('area_nm', 'asc')->lists('area_nm',
                'area_id')->all();
            $nationality = Countries::where('country_status', 'Yes')->where('nationality', '!=', '')
                ->where('iso', '!=', 'BD')->orderby('nationality', 'asc')->lists('nationality', 'id');
            $paymentMethods = PaymentMethod::where('status', 1)->where('is_archive', 0)->lists('name', 'id');
            $currencies = Currencies::where('is_archive', 0)->where('is_active', 1)->orderBy('code')->lists('code',
                'id');
            $travelVisaType = ProcessWiseVisaTypes::leftJoin('visa_types', 'visa_types.id', '=',
                'process_wise_visa_type.visa_type_id')
                ->where([
                    'process_wise_visa_type.process_type_id' => $this->process_type_id,
                    'process_wise_visa_type.other_significant_id' => 2,
                    'process_wise_visa_type.status' => 1,
                    'process_wise_visa_type.is_archive' => 0
                ])
                ->orderBy('process_wise_visa_type.id', 'asc')
                ->select('visa_types.type', 'visa_types.id')
                ->lists('visa_types.type', 'visa_types.id');

            //  Previous travel history attachments
            $travel_history_document = $this->getDocument($decodedAppId, 'type2');

            //  Required Documents for attachment
            $document = $this->getDocument($decodedAppId, 'master');

            $visaRecords = [];
            if ($appInfo->travel_history == 'yes') {
                $visaRecords = WP_TravelVisaRecord::where('app_id', $appInfo->id)->where('status', 1)->get();
            }

            $clrDocuments = [];
            $clr_document = AppDocuments::where('ref_id', $decodedAppId)->where('process_type_id',
                $this->process_type_id)->get();
            foreach ($clr_document as $documents) {
                $clrDocuments[$documents->doc_info_id]['document_id'] = $documents->id;
                $clrDocuments[$documents->doc_info_id]['file'] = $documents->doc_file_path;
                $clrDocuments[$documents->doc_info_id]['doc_name'] = $documents->doc_name;
            }

            //get meting no and meting date ...
            $metingInformation = CommonFunction::getMeetingInfo($appInfo->process_list_id);

            $contents = view("WorkPermitNew::application-form-pdf",
                compact('process_type_id', 'appInfo', 'WP_visaTypes', 'district_eng', 'nationality', 'paymentMethods',
                    'currencies', 'thana_eng',
                    'travelVisaType', 'visaRecords', 'document', 'travel_history_document', 'clrDocuments', 'viewMode', 'mode', 'metingInformation',
                    'divisions'))->render();

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
            Log::error('WPNPdfView: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [WPNC-1115]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [WPNC-1115]');
            return Redirect::back()->withInput();
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
                Session::flash('error', "Payment configuration not found [WPNC-1456]");
                return redirect()->back()->withInput();
            }

            // Get payment distributor list
            $stakeDistribution = PaymentDistribution::where('sp_pay_config_id', $payment_config->id)
                ->where('status', 1)
                ->where('is_archive', 0)
                ->get(['id', 'stakeholder_ac_no', 'pay_amount', 'fix_status', 'purpose', 'purpose_sbl', 'distribution_type']);
            if ($stakeDistribution->isEmpty()) {
                Session::flash('error', "Stakeholder not found [WPNC-101]");
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

            $paymentInfo->receiver_ac_no = $account_numbers;

            $appInfo = ProcessList::leftJoin('wp_apps as apps', 'apps.id', '=', 'process_list.ref_id')
                ->where('process_list.ref_id', $appId)
                ->where('process_list.process_type_id', $this->process_type_id)
                ->first([
                    'approved_duration_start_date',
                    'approved_duration_end_date',
                ]);
            $relevant_info_array = [
                'approved_duration_start_date' => $appInfo->approved_duration_start_date,
                'approved_duration_end_date' => $appInfo->approved_duration_end_date,
                'process_type_id' => $this->process_type_id,
            ];
            $unfixed_amount_array = $this->unfixedAmountsForPayment($payment_config, $relevant_info_array);

            $paymentInfo->pay_amount = $unfixed_amount_array['total_unfixed_amount'];
            // TODO : application dependent fee need to separate from payment configuration
            //$paymentInfo->pay_amount = $unfixed_amount_array['total_unfixed_amount'] + $payment_config->amount;

            $paymentInfo->vat_on_pay_amount = ($vatFreeAllowed === true) ? 0 : $unfixed_amount_array['total_vat_on_pay_amount'];
            $paymentInfo->total_amount = ($paymentInfo->pay_amount + $paymentInfo->vat_on_pay_amount);
            $paymentInfo->contact_name = $request->get('gfp_contact_name');
            $paymentInfo->contact_email = $request->get('gfp_contact_email');
            $paymentInfo->contact_no = $request->get('gfp_contact_phone');
            $paymentInfo->address = $request->get('gfp_contact_address');
            $paymentInfo->sl_no = 1; // Always 1
            $paymentInfo->save();

            WorkPermitNew::where('id', $appId)->update([
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
            // Payment Details By Stakeholders End

            // Payment Submission
            DB::commit();
            if ($request->get('actionBtn') == 'Submit' && $paymentInfo->id) {
                return redirect('spg/initiate-multiple/' . Encryption::encodeId($paymentInfo->id));
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('WPNPayment: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [WPNC-1025]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . "[WPNC-1025]");
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

            // Approve Duration calculation
            $applicationInfo['approved_duration_start_date'] = $relevant_info_array['approved_duration_start_date'];
            $applicationInfo['approved_duration_end_date'] = $relevant_info_array['approved_duration_end_date'];
            $applicationInfo['process_type_id'] = $relevant_info_array['process_type_id'];

            $govt_application_fee = (int)commonFunction::getGovtFeesAmount($applicationInfo);

            $unfixed_amount_array[3] = $govt_application_fee;
            $unfixed_amount_array[5] = ($govt_application_fee / 100) * $vat_percentage;

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
            // tracking no generate only when payment is Service Fee Payment
            if ($paymentInfo->payment_category_id == 1) {
                if ($processData->status_id != '-1') {
                    Session::flash('error', 'This is an invalid status, it\'s not possible to get the next status. [WPNC-912]');
                    return redirect('process/work-permit-new/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
                }

                $general_submission_process_data = CommonFunction::getGeneralSubmission($this->process_type_id);
                $processData->status_id = $general_submission_process_data['process_starting_status'];
                $processData->desk_id = $general_submission_process_data['process_starting_desk'];

                $processData->process_desc = 'Service Fee Payment completed successfully.';
                $processData->submitted_at = date('Y-m-d H:i:s'); // application submitted Date

                // Application status_id for email queue
                $appInfo['status_id'] = $processData->status_id;

                CommonFunction::sendEmailSMS('APP_SUBMIT', $appInfo, $applicantEmailPhone);


            } elseif ($paymentInfo->payment_category_id == 2) {
                if (!in_array($processData->status_id, [15, 32])) {
                    Session::flash('error', 'This is an invalid status, it\'s not possible to get the next status. [WPNC-913]');
                    return redirect('process/work-permit-new/view-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
                }

                $general_submission_process_data = CommonFunction::getGovtPaySubmission($this->process_type_id);
                $processData->status_id = $general_submission_process_data['process_starting_status'];
                $processData->desk_id = $general_submission_process_data['process_starting_desk'];

                $processData->read_status = 0;
                $processData->process_desc = 'Government Fee Payment completed successfully.';
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

            return redirect('process/work-permit-new/view-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('WPNAfterPayment: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [WPNC-1081]');
            Session::flash('error',
                'Something went wrong!, application not updated after payment. Error : ' . $e->getMessage() . '[WPNC-1081]');
            return redirect('process/work-permit-new/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
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
                $paymentInfo->payment_status = 1;

                // Application status_id for email queue
                $appInfo['status_id'] = $processData->status_id;

                CommonFunction::sendEmailSMS('APP_SUBMIT', $appInfo, $applicantEmailPhone);

                Session::flash('success', 'Payment Confirm successfully');
            } /*
             * Government payment submit
             * */
            elseif ($paymentInfo->is_verified == 1 && $paymentInfo->payment_category_id == 2) {
                $paymentInfo->payment_status = 1;
//                $processData->status_id = 16;
//                $processData->desk_id = 1;

                $general_submission_process_data = CommonFunction::getGovtPaySubmission($this->process_type_id);

                $processData->status_id = $general_submission_process_data['process_starting_status'];
                $processData->desk_id = $general_submission_process_data['process_starting_desk'];

                $processData->read_status = 0;
                $processData->process_desc = 'Government Fee Payment completed successfully.';
                $appInfo['payment_date'] = date('d-m-Y', strtotime($paymentInfo->payment_date));
                $appInfo['govt_fees'] = CommonFunction::getGovFeesInWord($paymentInfo->pay_amount);
                $appInfo['govt_fees_amount'] = $paymentInfo->pay_amount;

                // Application status_id for email queue
                $appInfo['status_id'] = $processData->status_id;

                CommonFunction::sendEmailSMS('APP_GOV_PAYMENT_SUBMIT', $appInfo, $applicantEmailPhone);

            } /*
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

            return redirect('process/work-permit-new/view-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('WPNCounterPayment: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [WPNC-1082]');
            Session::flash('error',
                'Something went wrong!, application not updated after payment. Error : ' . $e->getMessage() . '[WPNC-1082]');
            return redirect('process/work-permit-new/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        }
    }

    public function conditionalApproveStore(Request $request)
    {
        // Validation
        $rules['conditional_approved_file'] = 'required';
        $messages['conditional_approved_file'] = 'Attachment file is required';
        $this->validate($request, $rules, $messages);

        try {

            DB::beginTransaction();
            $appId = Encryption::decodeId($request->get('app_id'));

            if ($request->hasFile('conditional_approved_file')) {
                $yearMonth = date("Y") . "/" . date("m") . "/";
                $path = 'uploads/' . $yearMonth;
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $_file_path = $request->file('conditional_approved_file');
                $file_path = trim(uniqid('BIDA_WPN-' . $appId . '-', true) . $_file_path->getClientOriginalName());
                $_file_path->move($path, $file_path);
                $conditional_approved_file = $yearMonth . $file_path;
            }

            WorkPermitNew::where('id', $appId)->update([
                'conditional_approved_file' => isset($conditional_approved_file) ? $conditional_approved_file : '',
                'conditional_approved_remarks' => $request->get('conditional_approved_remarks')
            ]);

            $processData = ProcessList::leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
                ->where('ref_id', $appId)
                ->where('process_type_id', $this->process_type_id)
                ->first([
                    'process_list.*',
                    'process_type.form_id'
                ]);

            if (!in_array($processData->status_id, [17, 31])) {
                Session::flash('error', 'This is an invalid status, it\'s not possible to get the next status. [WPNC-914]');
                return redirect('process/work-permit-new/view-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
            }

            $conditional_submission_process_data = CommonFunction::getConditionFulfillSubmission($this->process_type_id);
            $processData->status_id = $conditional_submission_process_data['process_starting_status'];
            $processData->desk_id = $conditional_submission_process_data['process_starting_desk'];

            $processData->read_status = 0;

            // Applicant conditional remarks
            $processData->process_desc = $request->get('conditional_approved_remarks');
            $processData->save();

            DB::commit();
            Session::flash('success', 'Condition fulfilled successfully');
            return redirect('process/work-permit-new/view-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('WPNConditionalApproveStore: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [WPNC-1026]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . "[WPNC-1026]");
            return redirect()->back()->withInput();
        }
    }

    private function getDocList($attachment_key = '', $doc_section = 'master', $app_id = '')
    {
        if ($app_id != '') {
            $attachment = AppDocuments::leftJoin('attachment_list', 'attachment_list.id', '=', 'app_documents.doc_info_id')
                ->where('app_documents.ref_id', $app_id)
                ->where('app_documents.process_type_id', $this->process_type_id)
                ->where('app_documents.doc_section', $doc_section)
                ->get([
                    'attachment_list.id',
                    'attachment_list.doc_priority',
                    'attachment_list.additional_field',
                    'app_documents.id as document_id',
                    'app_documents.doc_file_path as doc_file_path',
                    'app_documents.doc_name'
                ]);

            if (count($attachment) < 1) {
                $attachment_query = Attachment::leftJoin('attachment_type', 'attachment_type.id', '=', 'attachment_list.attachment_type_id')
                    ->where('attachment_type.key', $attachment_key)
                    ->where('attachment_list.process_type_id', $this->process_type_id);
                if (Auth::user()->company->business_category == 2) { // 2=government; 3=both
                    $attachment_query->whereIn('attachment_list.business_category', [2, 3]);
                } else {
                    $attachment_query->whereIn('attachment_list.business_category', [1, 3]); // 1=private; 3=both
                }
                $attachment = $attachment_query->where('attachment_list.status', 1)
                    ->where('attachment_list.is_archive', 0)
                    ->orderBy('attachment_list.order')
                    ->get(['attachment_list.*']);
            }

        } else {
            $attachment_query = Attachment::leftJoin('attachment_type', 'attachment_type.id', '=', 'attachment_list.attachment_type_id')
                ->where('attachment_type.key', $attachment_key)
                ->where('attachment_list.process_type_id', $this->process_type_id);
            if (Auth::user()->company->business_category == 2) { // 2=government; 3=both
                $attachment_query->whereIn('attachment_list.business_category', [2, 3]);
            } else {
                $attachment_query->whereIn('attachment_list.business_category', [1, 3]); // 1=private; 3=both
            }
            $attachment = $attachment_query->where('attachment_list.status', 1)
                ->where('attachment_list.is_archive', 0)
                ->orderBy('attachment_list.order')
                ->get(['attachment_list.*']);
        }

        return $attachment;
    }

    private function getDocument($app_id, $doc_section)
    {
        return AppDocuments::leftJoin('attachment_list', 'attachment_list.id', '=', 'app_documents.doc_info_id')
            ->where('app_documents.ref_id', $app_id)
            ->where('app_documents.process_type_id', $this->process_type_id)
            ->where('app_documents.doc_section', $doc_section)
            ->get([
                'attachment_list.id',
                'attachment_list.doc_priority',
                'attachment_list.additional_field',
                'app_documents.id as document_id',
                'app_documents.doc_file_path as doc_file_path',
                'app_documents.doc_name'
            ]);
    }

}