<?php

namespace App\Modules\WorkPermitAmendment\Controllers;

use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Libraries\UtilFunction;
use App\Modules\Apps\Models\AppDocuments;
use App\Modules\Apps\Models\PaymentMethod;
use App\Modules\BasicInformation\Controllers\BasicInformationController;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\Settings\Models\Attachment;
use App\Modules\Settings\Models\Currencies;
use App\Modules\SonaliPayment\Controllers\SonaliPaymentController;
use App\Modules\SonaliPayment\Models\PaymentConfiguration;
use App\Modules\SonaliPayment\Models\PaymentDetails;
use App\Modules\SonaliPayment\Models\PaymentDistribution;
use App\Modules\SonaliPayment\Models\SonaliPayment;
use App\Modules\Users\Models\AreaInfo;
use App\Modules\Users\Models\Countries;
use App\Modules\WorkPermitAmendment\Models\WorkPermitAmendment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
//use mPDF;
use Mpdf\Mpdf;

class WorkPermitAmendmentController extends Controller
{
    protected $process_type_id;
    protected $aclName;

    public function __construct()
    {
        $this->process_type_id = 4;
        $this->aclName = 'WorkPermitAmendment';
    }


    public function applicationForm(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.';
        }

        if (!ACL::getAccsessRight($this->aclName, '-A-')) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>You have no access right! Contact with system admin for more information. [WPA-971]</h4>"
            ]);
        }

        // Check whether the applicant company is eligible and have approved basic information application
        $company_id = CommonFunction::getUserWorkingCompany();
        if (CommonFunction::checkEligibilityAndBiApps($company_id) != 1) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<div class='btn-center'><h4 class='custom-err-msg'>Sorry! You have no approved Basic Information application for BIDA services.</h4> <br/> <a href='/dashboard' class='btn btn-primary btn-sm'>Apply for Basic Information</a></div>"
            ]);
        }

        // Check whether the applicant company's department will get this service
        $department_id = CommonFunction::getDeptIdByCompanyId($company_id);
        if ($department_id == 4) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>Sorry! The department is not allowed to apply to this application.</h4>"
            ]);
        }

        // Get basic information
        $basicInfo = CommonFunction::getBasicInformationByCompanyId($company_id);
        if (empty($basicInfo)) {
            Session::flash('error', "Basic information data is not found! [WPA-105]");
            return redirect()->back()->withInput();
        }

        try {
            // Checking the Service Fee Payment(SFP) configuration for this service
            $payment_config = PaymentConfiguration::leftJoin(
                'sp_payment_category',
                'sp_payment_category.id',
                '=',
                'sp_payment_configuration.payment_category_id'
            )
                ->where([
                    'sp_payment_configuration.process_type_id' => $this->process_type_id,
                    'sp_payment_configuration.payment_category_id' => 1, // Submission fee payment
                    'sp_payment_configuration.status' => 1,
                    'sp_payment_configuration.is_archive' => 0
                ])->first(['sp_payment_configuration.*', 'sp_payment_category.name']);
            if (empty($payment_config)) {
                return response()->json([
                    'responseCode' => 1,
                    'html' => "<h4 class='custom-err-msg'> Payment Configuration not found ![WPA-10100]</h4>"
                ]);
            }
            $unfixed_amount_array = $this->unfixedAmountsForPayment($payment_config);
            $payment_config->amount = $unfixed_amount_array['total_unfixed_amount'] + $payment_config->amount;
            $payment_config->vat_on_pay_amount = $unfixed_amount_array['total_vat_on_pay_amount'];

            $paymentMethods = ['' => 'Select One'] + PaymentMethod::where('status', 1)->where('is_archive', 0)->lists('name', 'id')->all();
            $countries = ['' => 'Select One'] + Countries::where('country_status', 'Yes')->orderBy('nicename', 'asc')->lists('nicename', 'id')->all();
            $nationality = Countries::where('country_status', 'Yes')->where('nationality', '!=', '')
                ->where('iso', '!=', 'BD')->orderby('nationality', 'asc')->lists('nationality', 'id')->all();
            $currencies = ['' => 'Select One'] + Currencies::where('is_archive', 0)->where('is_active', 1)->orderBy('code')->lists('code', 'id')->all();
            $divisions = ['' => 'Select One'] + AreaInfo::where('area_type', 1)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $districts = ['' => 'Select One'] + AreaInfo::where('area_type', 2)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $thana = ['' => 'Select One'] + AreaInfo::orderby('area_nm')->where('area_type', 3)->lists('area_nm', 'area_id')->all();

            $attachment_key = "wpa_";
            if ($department_id == 1) {
                $attachment_key .= "cml";
            } else if ($department_id == 2) {
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
            $document = $query->where('attachment_list.status', 1)
                ->where('attachment_list.is_archive', 0)
                ->orderBy('attachment_list.order')
                ->get(['attachment_list.*']);

            $viewMode = 'off';
            $process_type_id = $this->process_type_id;
            $public_html = strval(view(
                "WorkPermitAmendment::application-form",
                compact(
                    'process_type_id',
                    'viewMode',
                    'nationality',
                    'paymentMethods',
                    'document',
                    'divisions',
                    'districts',
                    'thana',
                    'currencies',
                    'payment_config',
                    'company_id',
                    'countries',
                    'basicInfo'
                )
            ));
            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (Exception $e) {
            Log::error('WPAAppForm: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [WPA-1005]');
            return response()->json([
                'responseCode' => 1, 'html' => CommonFunction::showErrorPublic($e->getMessage()) . ' [WPA-1005]'
            ]);
        }
    }

    public function appStore(Request $request)
    {
        $company_id = CommonFunction::getUserWorkingCompany();
        $department_id = CommonFunction::getDeptIdByCompanyId($company_id);
        $mode = (!empty($request->get('app_id')) ? '-E-' : '-A-');
        $app_id = (!empty($request->get('app_id')) ? Encryption::decodeId($request->get('app_id')) : '');

        // Check whether the applicant company is eligible and have approved basic information application
        if (CommonFunction::checkEligibilityAndBiApps($company_id) != 1) {
            Session::flash('error', "Sorry! You have no approved Basic Information application for BIDA services. [WPA-9991]");
            return redirect()->back();
        }

        // Set permission mode and check ACL
        if (!ACL::getAccsessRight($this->aclName, $mode, $app_id)) {
            abort('400', "You have no access right! Please contact with system admin if you have any query. [WPA-973]");
        }

        // Checking the Service Fee Payment(SFP) configuration for this service
        $payment_config = PaymentConfiguration::leftJoin(
            'sp_payment_category',
            'sp_payment_category.id',
            '=',
            'sp_payment_configuration.payment_category_id'
        )
            ->where([
                'sp_payment_configuration.process_type_id' => $this->process_type_id,
                'sp_payment_configuration.payment_category_id' => 1,  // Submission Payment
                'sp_payment_configuration.status' => 1,
                'sp_payment_configuration.is_archive' => 0,
            ])->first(['sp_payment_configuration.*', 'sp_payment_category.name']);
        if (empty($payment_config)) {
            Session::flash('error', "Payment configuration not found [WPA-2050]");
            return redirect()->back()->withInput();
        }

        // Checking the payment distributor under payment configuration
        $stakeDistribution = PaymentDistribution::where('sp_pay_config_id', $payment_config->id)
            ->where('status', 1)
            ->where('is_archive', 0)
            ->get(['id', 'stakeholder_ac_no', 'pay_amount', 'fix_status', 'purpose', 'purpose_sbl', 'distribution_type']);
        if ($stakeDistribution->isEmpty()) {
            Session::flash('error', "Stakeholder not found [WPA-101]");
            return redirect()->back()->withInput();
        }

        // Get basic information
        $basicInfo = CommonFunction::getBasicInformationByCompanyId($company_id);
        if (empty($basicInfo)) {
            Session::flash('error', "Basic information data is not found! [WPA-105]");
            return redirect()->back()->withInput();
        }

        // get work permit new or extension info & set session
        if ($request->get('searchWPNinfo') == 'searchWPNinfo') {
            if ($request->get('is_approval_online') == 'yes' && $request->has('ref_app_tracking_no')) {
                $refAppTrackingNo = trim($request->get('ref_app_tracking_no'));

                $getWpApprovedRefId = ProcessList::where('tracking_no', $refAppTrackingNo)
                    ->where('status_id', 25)->where('company_id', $company_id)
                    ->whereIn('process_type_id', [2, 3]) //2 = Work Permit New, 3 = Work Permit Extension
                    ->first(['ref_id', 'tracking_no']);

                if (empty($getWpApprovedRefId)) {
                    Session::flash('error', 'Sorry! approved work permit reference no. is not found! [WPAC-111]');
                    return redirect()->back();
                }

                //Get data from WPCommonPool
                $wpInfo = UtilFunction::checkWpCommonPoolData($getWpApprovedRefId->tracking_no, $getWpApprovedRefId->ref_id);

                if (empty($wpInfo)) {
                    Session::flash('error', 'Sorry! Work permit reference number not found by tracking no! [WPA-1081]');
                    return redirect()->back();
                }

                Session::put('wpneInfo', $wpInfo->toArray());
                Session::put('wpneInfo.is_approval_online', $request->get('is_approval_online'));
                Session::put('wpneInfo.ref_app_tracking_no', $refAppTrackingNo);
                Session::flash('success', 'Successfully loaded work permit data. Please proceed to next step');
                return redirect()->back();
            }
        }

        // Clean session data
        if ($request->get('actionBtn') == 'clean_load_data') {
            Session::forget("wpneInfo");
            Session::flash('success', 'Successfully cleaned data.');
            return redirect()->back();
        }

        //  Required Documents for attachment
        $attachment_key = "wpa_";
        if ($department_id == 1) {
            $attachment_key .= "cml";
        } else if ($department_id == 2) {
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

            if (!$request->has('toggleCheck') && !($request->has('CBtoggleCheck'))) {
                Session::flash('error', 'In order to Submit please select at least one field for amendment. [WPA-1041]');
                return redirect()->back();
            }

            // attachment validation check
            if (count($doc_row) > 0) {
                foreach ($doc_row as $value) {
                    if ($value->doc_priority == 1) {
                        $rules['validate_field_' . $value->id] = 'required';
                        $messages['validate_field_' . $value->id . '.required'] = $value->doc_name . ', this file is required.';
                    }
                }
            }

            $rules['is_approval_online'] = 'required';
            $rules['ref_app_tracking_no'] = 'required_if:is_approval_online,yes';

            $rules['office_division_id'] = 'required';
            $rules['office_district_id'] = 'required';
            $rules['office_thana_id'] = 'required';
            $rules['office_post_office'] = 'required';
            $rules['office_post_code'] = 'required';
            $rules['office_address'] = 'required';
            // $rules['office_telephone_no'] = 'required';
            $rules['office_mobile_no'] = 'required';
            // $rules['office_fax_no'] = 'required';
            $rules['office_email'] = 'required';


            $rules['emp_name'] = 'required';
            $rules['emp_designation'] = 'required';
            $rules['emp_passport_no'] = 'required';
            $rules['emp_nationality_id'] = 'required';

            $rules['p_duration_start_date'] = 'date|date_format:d-M-Y';
            $rules['p_duration_end_date'] = 'date|date_format:d-M-Y';
            $rules['n_p_duration_start_date'] = 'date|date_format:d-M-Y';
            $rules['n_p_duration_end_date'] = 'date|date_format:d-M-Y';

            $rules['accept_terms'] = 'required';

            $messages['is_approval_online'] = 'Did you receive last work-permit through online OSS? field required.';
            $messages['ref_app_tracking_no.required_if'] = 'Please give your approved work permit reference No field required.';

            $messages['office_division_id'] = 'Office Address section Division field required.';
            $messages['office_district_id'] = 'Office Address section District field required.';
            $messages['office_thana_id'] = 'Office Address section Police Station field required.';
            $messages['office_post_office'] = 'Office Address section Post Office field required.';
            $messages['office_post_code'] = 'Office Address section Post Code field required.';
            $messages['office_address'] = 'Office Address section Address field required.';
            $messages['office_telephone_no'] = 'Office Address section Telephone No field required.';
            $messages['office_mobile_no'] = 'Office Address section Mobile No field required.';
            $messages['office_fax_no'] = 'Office Address section Fax No field required.';
            $messages['office_email'] = 'Office Address section Email field required.';


            $messages['emp_name'] = 'General information section Full Name field required.';
            $messages['emp_designation'] = 'General information section Position / Designation field required.';
            $messages['emp_passport_no'] = 'General information section Passport No field required.';
            $messages['emp_nationality_id'] = 'General information section Nationality field required.';

            $messages['p_duration_start_date'] = 'Previous work permit duration section Existing information (Latest Work Permit Info.) Start Date field required.';
            $messages['p_duration_end_date'] = 'Previous work permit duration section Existing information (Latest Work Permit Info.) End Date field required.';
            $messages['n_p_duration_start_date'] = 'Previous work permit duration section Proposed information Start Date field required.';
            $messages['n_p_duration_end_date'] = 'Previous work permit duration section Proposed information End Date field required.';

            $messages['accept_terms'] = 'I do here by declare that the information given above is true to the best of my knowledge and I shall be liable for any false information/ statement is given field required.';

            // Amendment data validation
            if ($request->has('toggleCheck')) {
                foreach ($request->get('toggleCheck') as $key => $val) {
                    if ($key == 'n_p_duration') {
                        $rules['n_p_duration_start_date'] = 'required';
                        $rules['n_p_duration_end_date'] = 'required';
                        $rules['n_p_desired_duration'] = 'required';

                        $messages['n_p_duration_start_date.required'] = 'Desired duration start date field is required';
                        $messages['n_p_duration_end_date.required'] = 'Desired duration end date field is required';
                        $messages['n_p_desired_duration.required'] = 'Desired duration field is required';
                    } else {
                        $rules[$key] = 'required';

                        $name_to_lable = str_replace('_', ' ', $key);
                        $name_to_lable = str_replace('n ', '', $name_to_lable);
                        $messages[$key . '.required'] = 'This ' . $name_to_lable . ' field is required because of the corresponding checkbox';
                    }
                }
            }
        }

        $this->validate($request, $rules, $messages);

        try {
            DB::beginTransaction();

            if ($request->get('app_id')) {
                $appData = WorkPermitAmendment::find($app_id);
                $processData = ProcessList::where([
                    'process_type_id' => $this->process_type_id, 'ref_id' => $appData->id
                ])->first();
            } else {
                $appData = new WorkPermitAmendment();
                $processData = new ProcessList();
            }

            // Basic Instructions
            $appData->is_approval_online = $request->get('is_approval_online');
            if ($request->get('is_approval_online') == 'yes') {
                $appData->ref_app_tracking_no = trim($request->get('ref_app_tracking_no'));
                $appData->ref_app_approve_date = (!empty($request->get('ref_app_approve_date')) ? date('Y-m-d', strtotime($request->get('ref_app_approve_date'))) : null);
            } else {
                $appData->manually_approved_wp_no = $request->get('manually_approved_wp_no');
            }

            $appData->issue_date_of_first_wp = (!empty($request->get('issue_date_of_first_wp')) ? date('Y-m-d', strtotime($request->get('issue_date_of_first_wp'))) : null);


            // Basic Company Information
            $appData->company_name = $basicInfo->company_name;
            $appData->company_name_bn = $basicInfo->company_name_bn;
            $appData->service_type = $basicInfo->service_type;
            $appData->reg_commercial_office = $basicInfo->reg_commercial_office;
            $appData->ownership_status_id = $basicInfo->ownership_status_id;
            $appData->organization_type_id = $basicInfo->organization_type_id;
            $appData->major_activities = $basicInfo->major_activities;

            // (Existing) Information of Principal Promoter/ Chairman/ Managing Director/ State CEO
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

            //            $appData->ceo_country_id = $request->get('ceo_country_id');
            //            $appData->ceo_dob = (!empty($request->get('ceo_dob')) ? date('Y-m-d', strtotime($request->get('ceo_dob'))) : 'null');
            //            $appData->ceo_passport_no = $request->get('ceo_passport_no');
            //            $appData->ceo_nid = $request->get('ceo_nid');
            //            $appData->ceo_designation = $request->get('ceo_designation');
            //            $appData->ceo_full_name = $request->get('ceo_full_name');
            //            $appData->ceo_district_id = $request->get('ceo_district_id');
            //            $appData->ceo_thana_id = $request->get('ceo_thana_id');
            //            $appData->ceo_city = $request->get('ceo_city');
            //            $appData->ceo_state = $request->get('ceo_state');
            //            $appData->ceo_post_code = $request->get('ceo_post_code');
            //            $appData->ceo_address = $request->get('ceo_address');
            //            $appData->ceo_telephone_no = $request->get('ceo_telephone_no');
            //            $appData->ceo_mobile_no = $request->get('ceo_mobile_no');
            //            $appData->ceo_fax_no = $request->get('ceo_fax_no');
            //            $appData->ceo_email = $request->get('ceo_email');
            //            $appData->ceo_father_name = $request->get('ceo_father_name');
            //            $appData->ceo_mother_name = $request->get('ceo_mother_name');
            //            $appData->ceo_spouse_name = $request->get('ceo_spouse_name');
            //            $appData->ceo_gender = !empty($request->get('ceo_gender')) ? $request->get('ceo_gender') : 'Not defined';

            // (Existing) Office Address
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

            // (Existing) Factory Address
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

            //            $appData->factory_district_id = $request->get('factory_district_id');
            //            $appData->factory_thana_id = $request->get('factory_thana_id');
            //            $appData->factory_post_office = $request->get('factory_post_office');
            //            $appData->factory_post_code = $request->get('factory_post_code');
            //            $appData->factory_address = $request->get('factory_address');
            //            $appData->factory_telephone_no = $request->get('factory_telephone_no');
            //            $appData->factory_mobile_no = $request->get('factory_mobile_no');
            //            $appData->factory_fax_no = $request->get('factory_fax_no');

            // (Existing) General information
            $appData->emp_name = $request->get('emp_name');
            $appData->emp_designation = $request->get('emp_designation');
            $appData->emp_passport_no = $request->get('emp_passport_no');
            $appData->emp_nationality_id = $request->get('emp_nationality_id');

            // (Existing) Previous work permit duration
            $appData->p_duration_start_date = (!empty($request->get('p_duration_start_date')) ? date(
                'Y-m-d',
                strtotime($request->get('p_duration_start_date'))
            ) : null);
            $appData->p_duration_end_date = (!empty($request->get('p_duration_end_date')) ? date(
                'Y-m-d',
                strtotime($request->get('p_duration_end_date'))
            ) : null);
            $appData->p_desired_duration = $request->get('p_desired_duration');

            // (Existing) Compensation and benefit
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

            // Effective date of Compensation and Benefit
            $appData->effective_date = (!empty($request->get('effective_date')) ? date('Y-m-d', strtotime($request->get('effective_date'))) : null);
            $appData->approved_effective_date = (!empty($request->get('effective_date')) ? date('Y-m-d', strtotime($request->get('effective_date'))) : null);


            // (Proposed) Information of Principal Promoter/Chairman/Managing Director/CEO/Country Manager
            //            $appData->n_ceo_country_id = !empty($request->get('n_ceo_country_id')) ? $request->get('n_ceo_country_id') : null;
            //            $appData->n_ceo_dob = (!empty($request->get('n_ceo_dob')) ? date('Y-m-d', strtotime($request->get('n_ceo_dob'))) : null);
            //            $appData->n_ceo_passport_no = !empty($request->get('n_ceo_passport_no')) ? $request->get('n_ceo_passport_no') : null;
            //            $appData->n_ceo_nid = !empty($request->get('n_ceo_nid')) ? $request->get('n_ceo_nid') : null;
            //            $appData->n_ceo_designation = !empty($request->get('n_ceo_designation')) ? $request->get('n_ceo_designation') : null;
            //            $appData->n_ceo_full_name = !empty($request->get('n_ceo_full_name')) ? $request->get('n_ceo_full_name') : null;
            //            $appData->n_ceo_district_id = !empty($request->get('n_ceo_district_id')) ? $request->get('n_ceo_district_id') : null;
            //            $appData->n_ceo_thana_id = !empty($request->get('n_ceo_thana_id')) ? $request->get('n_ceo_thana_id') : null;
            //            $appData->n_ceo_city = !empty($request->get('n_ceo_city')) ? $request->get('n_ceo_city') : null;
            //            $appData->n_ceo_state = !empty($request->get('n_ceo_state')) ? $request->get('n_ceo_state') : null;
            //            $appData->n_ceo_post_code = !empty($request->get('n_ceo_post_code')) ? $request->get('n_ceo_post_code') : null;
            //            $appData->n_ceo_address = !empty($request->get('n_ceo_address')) ? $request->get('n_ceo_address') : null;
            //            $appData->n_ceo_telephone_no = !empty($request->get('n_ceo_telephone_no')) ? $request->get('n_ceo_telephone_no') : null;
            //            $appData->n_ceo_mobile_no = !empty($request->get('n_ceo_mobile_no')) ? $request->get('n_ceo_mobile_no') : null;
            //            $appData->n_ceo_fax_no = !empty($request->get('n_ceo_fax_no')) ? $request->get('n_ceo_fax_no') : null;
            //            $appData->n_ceo_email = !empty($request->get('n_ceo_email')) ? $request->get('n_ceo_email') : null;
            //            $appData->n_ceo_father_name = !empty($request->get('n_ceo_father_name')) ? $request->get('n_ceo_father_name') : null;
            //            $appData->n_ceo_mother_name = !empty($request->get('n_ceo_mother_name')) ? $request->get('n_ceo_mother_name') : null;
            //            $appData->n_ceo_spouse_name = !empty($request->get('n_ceo_spouse_name')) ? $request->get('n_ceo_spouse_name') : null;
            //            $appData->n_ceo_gender = !empty($request->get('n_ceo_gender')) ? $request->get('n_ceo_gender') : null;

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

            // (Proposed) Factory Address
            //            $appData->n_factory_district_id = !empty($request->get('n_factory_district_id')) ? $request->get('n_factory_district_id') : null;
            //            $appData->n_factory_thana_id = !empty($request->get('n_factory_thana_id')) ? $request->get('n_factory_thana_id') : null;
            //            $appData->n_factory_post_office = !empty($request->get('n_factory_post_office')) ? $request->get('n_factory_post_office') : null;
            //            $appData->n_factory_post_code = !empty($request->get('n_factory_post_code')) ? $request->get('n_factory_post_code') : null;
            //            $appData->n_factory_address = !empty($request->get('n_factory_address')) ? $request->get('n_factory_address') : null;
            //            $appData->n_factory_telephone_no = !empty($request->get('n_factory_telephone_no')) ? $request->get('n_factory_telephone_no') : null;
            //            $appData->n_factory_mobile_no = !empty($request->get('n_factory_mobile_no')) ? $request->get('n_factory_mobile_no') : null;
            //            $appData->n_factory_fax_no = !empty($request->get('n_factory_fax_no')) ? $request->get('n_factory_fax_no') : null;

            // (Proposed) General information
            $appData->n_emp_name = !empty($request->get('n_emp_name')) ? $request->get('n_emp_name') : null;
            $appData->n_emp_designation = !empty($request->get('n_emp_designation')) ? $request->get('n_emp_designation') : null;
            $appData->n_emp_passport_no = !empty($request->get('n_emp_passport_no')) ? $request->get('n_emp_passport_no') : null;
            $appData->n_emp_nationality_id = !empty($request->get('n_emp_nationality_id')) ? $request->get('n_emp_nationality_id') : null;

            // (Proposed) Previous work permit duration
            // Pay amount calculation start
            $duration_fees = 0;
            if (!empty($request->get('n_p_duration_start_date')) && !empty($request->get('n_p_duration_end_date')) && !empty($request->get('n_p_desired_duration'))) {
                $appInfo['approved_duration_start_date'] = $request->get('n_p_duration_start_date');
                $appInfo['approved_duration_end_date'] = $request->get('n_p_duration_end_date');
                $appInfo['process_type_id'] = $this->process_type_id;

                $durationData = commonFunction::getDesiredDurationDiffDate($appInfo);
                $appInfo['approve_duration_year'] = (int)$durationData['approve_duration_year'];
                $duration_fees = (string)commonFunction::getGovtFees($appInfo);
            }
            // Pay amount calculation end

            $appData->n_duration_start_date = (!empty($request->get('n_p_duration_start_date')) ? date('Y-m-d', strtotime($request->get('n_p_duration_start_date'))) : null);
            $appData->n_duration_end_date = (!empty($request->get('n_p_duration_end_date')) ? date('Y-m-d', strtotime($request->get('n_p_duration_end_date'))) : null);
            $appData->n_desired_duration = $request->get('n_p_desired_duration');
            $appData->n_desired_amount = $duration_fees;

            // Approved desired duration for desk user (process)
            $appData->approved_duration_start_date = (!empty($request->get('n_p_duration_start_date')) ? date('Y-m-d', strtotime($request->get('n_p_duration_start_date'))) : date('Y-m-d', strtotime($request->get('p_duration_start_date'))));
            $appData->approved_duration_end_date = (!empty($request->get('n_p_duration_end_date')) ? date('Y-m-d', strtotime($request->get('n_p_duration_end_date'))) : date('Y-m-d', strtotime($request->get('p_duration_end_date'))));
            $appData->approved_desired_duration = (!empty($request->get('n_p_desired_duration')) ? $request->get('n_p_desired_duration') : $request->get('p_desired_duration'));
            $appData->approved_duration_amount = $duration_fees;
            // (Proposed) (End) Previous work permit duration

            // (Proposed) Compensation and benefit
            $appData->n_basic_payment_type_id = $request->get('n_basic_payment_type_id');
            $appData->n_basic_local_amount = $request->get('n_basic_local_amount');
            $appData->n_basic_local_currency_id = $request->get('n_basic_local_currency_id');
            $appData->n_overseas_payment_type_id = $request->get('n_overseas_payment_type_id');
            $appData->n_overseas_local_amount = $request->get('n_overseas_local_amount');
            $appData->n_overseas_local_currency_id = $request->get('n_overseas_local_currency_id');
            $appData->n_house_payment_type_id = $request->get('n_house_payment_type_id');
            $appData->n_house_local_amount = $request->get('n_house_local_amount');
            $appData->n_house_local_currency_id = $request->get('n_house_local_currency_id');
            $appData->n_conveyance_payment_type_id = $request->get('n_conveyance_payment_type_id');
            $appData->n_conveyance_local_amount = $request->get('n_conveyance_local_amount');
            $appData->n_conveyance_local_currency_id = $request->get('n_conveyance_local_currency_id');
            $appData->n_medical_payment_type_id = $request->get('n_medical_payment_type_id');
            $appData->n_medical_local_amount = $request->get('n_medical_local_amount');
            $appData->n_medical_local_currency_id = $request->get('n_medical_local_currency_id');
            $appData->n_ent_payment_type_id = $request->get('n_ent_payment_type_id');
            $appData->n_ent_local_amount = $request->get('n_ent_local_amount');
            $appData->n_ent_local_currency_id = $request->get('n_ent_local_currency_id');
            $appData->n_bonus_payment_type_id = $request->get('n_bonus_payment_type_id');
            $appData->n_bonus_local_amount = $request->get('n_bonus_local_amount');
            $appData->n_bonus_local_currency_id = $request->get('n_bonus_local_currency_id');
            $appData->n_other_benefits = $request->get('n_other_benefits');


            // Store General information ? Proposed : Existing
            $appData->expatriate_name = (!empty($request->get('n_emp_name'))) ? $request->get('n_emp_name') : $request->get('emp_name');
            $appData->expatriate_passport = (!empty($request->get('n_emp_passport_no'))) ? $request->get('n_emp_passport_no') : $request->get('emp_passport_no');
            $appData->expatriate_nationality = (!empty($request->get('n_emp_nationality_id'))) ? $request->get('n_emp_nationality_id') : $request->get('emp_nationality_id');

            // Authorized Person Information
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
            $caption = $request->get('caption');
            $keys = $request->get('toggleCheck');

            if (count($keys)) {
                $nationality = Countries::where('country_status', 'Yes')->where('nationality', '!=', '')
                    ->where('iso', '!=', 'BD')->orderby('nationality', 'asc')->lists('nationality', 'id');
                $divisions = AreaInfo::where('area_type', 1)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
                $districts = AreaInfo::where('area_type', 2)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
                $thana = AreaInfo::orderby('area_nm')->where('area_type', 3)->lists('area_nm', 'area_id')->all();

                foreach ($keys as $key => $value) {
                    $data1 = [];
                    $data1['caption'] = (isset($caption[$key]) ? $caption[$key] : '');
                    if ($key == 'n_emp_nationality_id' || $key == 'n_ceo_country_id') {
                        $data1['old'] = ($request->has(substr($key, 2)) ? $nationality[$request->get(substr($key, 2))] : '');
                        $data1['new'] = ($request->has($key) ? $nationality[$request->get($key)] : '');
                    } elseif ($key == 'n_office_division_id') {
                        $data1['old'] = ($request->has(substr($key, 2)) ? $divisions[$request->get(substr($key, 2))] : '');
                        $data1['new'] = ($request->has($key) ? $divisions[$request->get($key)] : '');
                    } elseif ($key == 'n_office_district_id' || $key == 'n_factory_district_id' || $key == 'n_ceo_district_id') {
                        $data1['old'] = ($request->has(substr($key, 2)) ? $districts[$request->get(substr($key, 2))] : '');
                        $data1['new'] = ($request->has($key) ? $districts[$request->get($key)] : '');
                    } elseif ($key == 'n_office_thana_id' || $key == 'n_factory_thana_id' || $key == 'n_ceo_thana_id') {
                        $data1['old'] = ($request->has(substr($key, 2)) ? $thana[$request->get(substr($key, 2))] : '');
                        $data1['new'] = ($request->has($key) ? $thana[$request->get($key)] : '');
                    } elseif ($key == 'n_p_duration') {
                        $data1['caption'] = 'Work Permit Duration';
                        $data1['old'] = (!empty($request->get('p_duration_start_date')) ? date(
                                'M d, Y',
                                strtotime($request->get('p_duration_start_date'))
                            ) : '')
                            //                            . ' - ' . (!empty($request->get('p_duration_end_date')) ? date('M d, Y', strtotime($request->get('p_duration_end_date'))) : '')
                            . ' (' . $request->get('p_desired_duration') . ')';
                        $data1['new'] = (!empty($request->get('n_p_duration_start_date')) ? date(
                                'M d, Y',
                                strtotime($request->get('n_p_duration_start_date'))
                            ) : '')
                            //                            . ' - ' . (!empty($request->get('n_p_duration_end_date')) ? date('M d, Y', strtotime($request->get('n_p_duration_end_date'))) : '')
                            . ' (' . $request->get('n_p_desired_duration') . ')'; //.$request->get('n_p_desired_amount');
                    } else {
                        $data1['old'] = ($request->has(substr($key, 2)) ? $request->get(substr($key, 2)) : '');
                        $data1['new'] = ($request->has($key) ? $request->get($key) : '');
                    }
                    $data[] = $data1;
                }
            }


            // Compensation and Benefit
            $data_with_cb = $this->getPaymentAndCurrencyData($request, $data);

            // Amendment data for string
            $change_fields = '';
            $change_old_value = '';
            $change_new_value = '';
            $data_with_cb_count = count($data_with_cb);
            $i = 0;


            foreach ($data_with_cb as $key => $data_value) {
                if ($i == 0) {
                    $change_fields .= $data_value['caption'];
                    $change_old_value .= $data_value['old'];
                    $change_new_value .= $data_value['new'];
                } elseif ($i == ($data_with_cb_count - 1)) {
                    $change_fields .= ' & ' . $data_value['caption'];
                    $change_old_value .= ' & ' . $data_value['old'];
                    $change_new_value .= ' & ' . $data_value['new'];
                } else {
                    $change_fields .= ', ' . $data_value['caption'];
                    $change_old_value .= ', ' . $data_value['old'];
                    $change_new_value .= ', ' . $data_value['new'];
                }
                $i++;
            }
            // Amendment data for string

            $appData->data = json_encode($data_with_cb);
            $appData->change_fields = $change_fields;
            $appData->change_old_value = $change_old_value;
            $appData->change_new_value = $change_new_value;
            $appData->save();

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

            $processData->company_id = $company_id;
            $processData->ref_id = $appData->id;
            $processData->process_type_id = $this->process_type_id;
            $processData->process_desc = ''; // for re-submit application
            //$processData->read_status = 0;
            $processData->approval_center_id = UtilFunction::getApprovalCenterId($company_id);

            $jsonData['Applied by'] = CommonFunction::getUserFullName();
            $jsonData['Email'] = Auth::user()->user_email;
            $jsonData['Mobile'] = Auth::user()->user_phone;
            $processData['json_object'] = json_encode($jsonData);
            $processData->save();

            //Attachment store
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

            Session::forget("wpneInfo");

            /*
            * if action is submitted and application status is equal to draft
            * and have payment configuration then, generate a tracking number
            * and go to payment initiator function.
            */
            if ($request->get('actionBtn') == 'Submit' && $processData->status_id == -1) {
                if (empty($processData->tracking_no)) {
                    $prefix = 'WPA-'.date("dMY").'-';
                    UtilFunction::generateTrackingNumber($this->process_type_id, $processData->id, $prefix);
                }
                DB::commit();
                return redirect('spg/initiate-multiple/'.Encryption::encodeId($paymentInfo->id));

            }

            // Send Email notification to user on application re-submit
            if ($request->get('actionBtn') != "draft" && $processData->status_id == 2) {

                $processData = ProcessList::leftJoin(
                    'process_type',
                    'process_type.id',
                    '=',
                    'process_list.process_type_id'
                )
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
                    'process_type_name' => 'Work Permit Amendment',
                    'remarks' => ''
                ];

                CommonFunction::sendEmailSMS('APP_RESUBMIT', $appInfo, $applicantEmailPhone);
            }

            if ($processData->status_id == -1) {
                Session::flash('success', 'Successfully updated the Application!');
            } elseif ($processData->status_id == 1) {
                Session::flash('success', 'Successfully Application Submitted !');
            } elseif ($processData->status_id == 2) {
                Session::flash('success', 'Successfully Application Re-Submitted !');
            } else {
                Session::flash(
                    'error',
                    'Failed due to Application Status Conflict. Please try again later! [WPA-1023]'
                );
            }
            DB::commit();
            return redirect('work-permit-amendment/list/' . Encryption::encodeId($this->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('WPAAppStore: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [WPA-1001]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . "[WPA-1001]");
            return redirect()->back()->withInput();
        }
    }

    public function applicationEdit($appId, $openMode = '', Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.';
        }

        $mode = '-E-';
        $viewMode = 'off';
        if (!ACL::getAccsessRight($this->aclName, $mode)) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>You have no access right! Contact with system admin for more information</h4>"
            ]);
        }

        try {
            $decodedAppId = Encryption::decodeId($appId);
            $process_type_id = $this->process_type_id;

            // get application,process info
            $appInfo = ProcessList::leftJoin('wpa_apps as apps', 'apps.id', '=', 'process_list.ref_id')
                ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
                ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                    $join->on('ps.id', '=', 'process_list.status_id');
                    $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
                })
                ->leftJoin('sp_payment as sfp', function ($join) use ($process_type_id, $decodedAppId) {
                    $join->on('sfp.payment_category_id', '=', DB::raw(1)); // submission fee payment
                    $join->on('sfp.app_id', '=', DB::raw($decodedAppId));
                    $join->on('sfp.process_type_id', '=', DB::raw($process_type_id));
                })
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
                    'sfp.pay_mode as pay_mode',
                    'sfp.pay_mode_code as pay_mode_code',
                ]);

            $paymentMethods = ['' => 'Select One'] + PaymentMethod::where('status', 1)->where('is_archive', 0)->lists('name', 'id')->all();
            $countries = ['' => 'Select One'] + Countries::where('country_status', 'Yes')->orderBy('nicename', 'asc')->lists('nicename', 'id')->all();
            $nationality = Countries::where('country_status', 'Yes')->where('nationality', '!=', '')
                ->where('iso', '!=', 'BD')->orderby('nationality', 'asc')->lists('nationality', 'id')->all();
            $currencies = ['' => 'Select One'] + Currencies::where('is_archive', 0)->where('is_active', 1)->orderBy('code')->lists('code', 'id')->all();
            $divisions = ['' => 'Select One'] + AreaInfo::where('area_type', 1)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $districts = ['' => 'Select One'] + AreaInfo::where('area_type', 2)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $thana = ['' => 'Select One'] + AreaInfo::orderby('area_nm')->where('area_type', 3)->lists('area_nm', 'area_id')->all();

            //document
            $document_query = AppDocuments::leftJoin('attachment_list', 'attachment_list.id', '=', 'app_documents.doc_info_id')
                ->where('app_documents.ref_id', $decodedAppId)
                ->where('app_documents.process_type_id', $this->process_type_id);

            $document = $document_query->get([
                'attachment_list.id',
                'attachment_list.doc_priority',
                'attachment_list.additional_field',
                'app_documents.id as document_id',
                'app_documents.doc_file_path as doc_file_path',
                'app_documents.doc_name',
            ]);

            // Get basic information
            $basicInfo = CommonFunction::getBasicInformationByCompanyId($appInfo->company_id);

            $public_html = strval(view(
                "WorkPermitAmendment::application-form-edit",
                compact(
                    'process_type_id',
                    'appInfo',
                    'document',
                    'countries',
                    'divisions',
                    'districts',
                    'thana',
                    'paymentMethods',
                    'currencies',
                    'nationality',
                    'viewMode',
                    'mode',
                    'basicInfo'
                )
            ));

            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (\Exception $e) {
            Log::error('WPAViewEditForm: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [WPA-1015]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . "[WPA-1015]" . "</h4>"
            ]);
        }
    }

    public function applicationView($appId, Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.';
        }

        $viewMode = 'on';
        $mode = '-V-';

        // it's enough to check ACL for view mode only
        if (!ACL::getAccsessRight($this->aclName, $mode)) {
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>You have no access right! Contact with system admin for more information</h4>"
            ]);
        }

        try {
            $decodedAppId = Encryption::decodeId($appId);
            $process_type_id = $this->process_type_id;

            $appInfo = ProcessList::leftJoin('wpa_apps as apps', 'apps.id', '=', 'process_list.ref_id')
                ->leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
                ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
                ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                    $join->on('ps.id', '=', 'process_list.status_id');
                    $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
                })
                ->leftJoin('divisional_office', 'divisional_office.id', '=', 'process_list.approval_center_id')
                // Basic Company Information
                ->leftJoin('department', 'department.id', '=', 'process_list.department_id')
                ->leftJoin('ea_service', 'ea_service.id', '=', 'apps.service_type')
                ->leftJoin('ea_reg_commercial_offices', 'ea_reg_commercial_offices.id', '=', 'apps.reg_commercial_office')
                ->leftJoin('ea_ownership_status', 'ea_ownership_status.id', '=', 'apps.ownership_status_id')
                ->leftJoin('ea_organization_type', 'ea_organization_type.id', '=', 'apps.organization_type_id')

                // Reference application
                ->leftJoin('process_list as ref_process', 'ref_process.tracking_no', '=', 'apps.ref_app_tracking_no')
                ->leftJoin('process_type as ref_process_type', 'ref_process_type.id', '=', 'ref_process.process_type_id')

                // Payment Information
                ->leftJoin('sp_payment as sfp', function ($join) use ($process_type_id, $decodedAppId) {
                    $join->on('sfp.payment_category_id', '=', DB::raw(1)); // submission fee payment
                    $join->on('sfp.app_id', '=', DB::raw($decodedAppId));
                    $join->on('sfp.process_type_id', '=', DB::raw($process_type_id));
                })
                ->leftJoin('sp_payment as gfp', function ($join) use ($process_type_id, $decodedAppId) {
                    $join->on('gfp.payment_category_id', '=', DB::raw(2)); //Government fee payment
                    $join->on('gfp.app_id', '=', DB::raw($decodedAppId));
                    $join->on('gfp.process_type_id', '=', DB::raw($process_type_id));
                })

                // General information
                ->leftJoin('country_info as emp_nationality', 'emp_nationality.id', '=', 'apps.emp_nationality_id')
                ->leftJoin('country_info as n_emp_nationality', 'n_emp_nationality.id', '=', 'apps.n_emp_nationality_id')

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

                // Proposed Compensation and Benefit
                ->leftJoin('payment_methods as n_basic_payment', 'n_basic_payment.id', '=', 'apps.n_basic_payment_type_id')
                ->leftJoin('currencies as n_basic_currency', 'n_basic_currency.id', '=', 'apps.n_basic_local_currency_id')
                ->leftJoin('payment_methods as n_overseas_payment', 'n_overseas_payment.id', '=', 'apps.n_overseas_payment_type_id')
                ->leftJoin('currencies as n_overseas_currency', 'n_overseas_currency.id', '=', 'apps.n_overseas_local_currency_id')
                ->leftJoin('payment_methods as n_house_payment', 'n_house_payment.id', '=', 'apps.n_house_payment_type_id')
                ->leftJoin('currencies as n_house_currency', 'n_house_currency.id', '=', 'apps.n_house_local_currency_id')
                ->leftJoin('payment_methods as n_conveyance_payment', 'n_conveyance_payment.id', '=', 'apps.n_conveyance_payment_type_id')
                ->leftJoin('currencies as n_conveyance_currency', 'n_conveyance_currency.id', '=', 'apps.n_conveyance_local_currency_id')
                ->leftJoin('payment_methods as n_medical_payment', 'n_medical_payment.id', '=', 'apps.n_medical_payment_type_id')
                ->leftJoin('currencies as n_medical_currency', 'n_medical_currency.id', '=', 'apps.n_medical_local_currency_id')
                ->leftJoin('payment_methods as n_ent_payment', 'n_ent_payment.id', '=', 'apps.n_ent_payment_type_id')
                ->leftJoin('currencies as n_ent_currency', 'n_ent_currency.id', '=', 'apps.n_ent_local_currency_id')
                ->leftJoin('payment_methods as n_bonus_payment', 'n_bonus_payment.id', '=', 'apps.n_bonus_payment_type_id')
                ->leftJoin('currencies as n_bonus_currency', 'n_bonus_currency.id', '=', 'apps.n_bonus_local_currency_id')

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
                    'process_type.form_url',
                    'user_desk.desk_name',
                    'ps.status_name',
                    'apps.*',

                    'divisional_office.office_name as divisional_office_name',
                    'divisional_office.office_address as divisional_office_address',

                    // Basic Company Information
                    'department.name as department',
                    'ea_service.name as service_name',
                    'ea_reg_commercial_offices.name as reg_commercial_office_name',
                    'ea_ownership_status.name as ea_ownership_status',
                    'ea_organization_type.id as ea_organization_type_id',
                    'ea_organization_type.name as ea_organization_type',

                    // Reference application
                    'ref_process.ref_id as ref_application_ref_id',
                    'ref_process.process_type_id as ref_application_process_type_id',
                    'ref_process_type.type_key as ref_process_type_key',

                    // Service fee payment
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
                    'sfp.payment_category_id as sfp_payment_category_id',

                    // Govt. fee payment
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
                    'gfp.payment_category_id as gfp_payment_category_id',

                    // General information
                    'emp_nationality.nationality as emp_nationality_name',
                    'n_emp_nationality.nationality as n_emp_nationality_name',

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

                    // Proposed Compensation and Benefit
                    'n_basic_payment.name as n_basic_payment_type_name',
                    'n_basic_currency.code as n_basic_currency_code',
                    'n_overseas_payment.name as n_overseas_payment_type_name',
                    'n_overseas_currency.code as n_overseas_currency_code',
                    'n_house_payment.name as n_house_payment_type_name',
                    'n_house_currency.code as n_house_currency_code',
                    'n_conveyance_payment.name as n_conveyance_payment_type_name',
                    'n_conveyance_currency.code as n_conveyance_currency_code',
                    'n_medical_payment.name as n_medical_payment_type_name',
                    'n_medical_currency.code as n_medical_currency_code',
                    'n_ent_payment.name as n_ent_payment_type_name',
                    'n_ent_currency.code as n_ent_currency_code',
                    'n_bonus_payment.name as n_bonus_payment_type_name',
                    'n_bonus_currency.code as n_bonus_currency_code',
                ]);

            // Checking the Government Fee Payment(GFP) configuration for this service
            if ($appInfo->status_id == 15 || $appInfo->status_id == 32) {
                $payment_config = PaymentConfiguration::leftJoin('sp_payment_category', 'sp_payment_category.id', '=', 'sp_payment_configuration.payment_category_id')
                    ->where([
                        'sp_payment_configuration.process_type_id' => $this->process_type_id,
                        'sp_payment_configuration.payment_category_id' => 2, //Government fee payment
                        'sp_payment_configuration.status' => 1,
                        'sp_payment_configuration.is_archive' => 0
                    ])->first(['sp_payment_configuration.*', 'sp_payment_category.name']);
                if (empty($payment_config)) {
                    return response()->json(['responseCode' => 1, 'html' => "<h4 style='color: #ff0000;margin-top: 250px;margin-left: 70px;'> Payment Configuration not found ![WPA-10100]</h4>"]);
                }

                $unfixed_amount_array = $this->unfixedAmountsForPayment($payment_config);
                $payment_config->amount = $unfixed_amount_array['total_unfixed_amount'] + $payment_config->amount;

                $vatFreeAllowed = BasicInformationController::isAllowedWithOutVAT($this->process_type_id);
                $payment_config->vat_on_pay_amount = ($vatFreeAllowed === true) ? 0 : $unfixed_amount_array['total_vat_on_pay_amount'];
            }

            //  Required Documents for attachment
            $attachment_key = "wpa_";
            if ($appInfo->department_id == 1) {
                $attachment_key .= "cml";
            } else if ($appInfo->department_id == 2) {
                $attachment_key .= "i";
            } else {
                $attachment_key .= "comm";
            }

            //document
            $document = AppDocuments::leftJoin('attachment_list', 'attachment_list.id', '=', 'app_documents.doc_info_id')
                ->join('attachment_type', 'attachment_type.id', '=', 'attachment_list.attachment_type_id')
                ->where('app_documents.ref_id', $decodedAppId)
                ->where('app_documents.process_type_id', $this->process_type_id)
                ->where('attachment_type.key', $attachment_key)
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
                $data['ref_app_url'] = url('process/' . $appInfo->ref_process_type_key . '/view-app/' . Encryption::encodeId($appInfo->ref_application_ref_id) . '/' . Encryption::encodeId($appInfo->ref_application_process_type_id));
            }

            // Get basic information
            $basicInfo = CommonFunction::getBasicInformationByCompanyId($appInfo->company_id);

            $countries = ['' => 'Select One'] + Countries::where('country_status', 'Yes')->orderBy('nicename', 'asc')->lists('nicename', 'id')->all();
            $divisions = ['' => 'Select One'] + AreaInfo::where('area_type', 1)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $districts = ['' => 'Select One'] + AreaInfo::where('area_type', 2)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
            $thana = ['' => 'Select One'] + AreaInfo::orderby('area_nm')->where('area_type', 3)->lists('area_nm', 'area_id')->all();

            $public_html = view(
                "WorkPermitAmendment::application-form-view",
                compact(
                    'process_type_id',
                    'appInfo',
                    'document',
                    'viewMode',
                    'mode',
                    'payment_config',
                    'data',
                    'basicInfo',
                    'countries',
                    'divisions',
                    'districts',
                    'thana'
                )
            )->render();

            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (\Exception $e) {
            Log::error('WPAViewApp: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [WPA-1016]');
            return response()->json([
                'responseCode' => 1,
                'html' => "<h4 class='custom-err-msg'>" . CommonFunction::showErrorPublic($e->getMessage()) . "[WPA-1016]" . "</h4>"
            ]);
        }
    }

    public function getPaymentAndCurrencyData($request, $data)
    {
        $caption = $request->get('caption');
        // $paymentType = PaymentMethod::where('status', 1)->where('is_archive', 0)->lists('name', 'id');

        $expire = Carbon::now()->addDays(1);
        $paymentType = Cache::remember('activePaymentMethodList', $expire, function () {
            return PaymentMethod::where('status', 1)->where('is_archive', 0)->lists('name', 'id');
        });

        $paymentCurrency = Cache::remember('activeCurrencyList', $expire, function () {
            return Currencies::where('is_archive', 0)->where('is_active', 1)->orderBy('code')->lists('code', 'id');
        });

        // if (isset($request->get('CBtoggleCheck')['n_basic_payment_type_id'])){
        //     $cb_data = [];
        //     $cb_data['caption'] = (isset($caption['n_basic_salary']) ? $caption['n_basic_salary'] : '');
        //     $cb_data['old'] = (isset($paymentType[$request->get('basic_payment_type_id')], $paymentCurrency[$request->get('basic_local_currency_id')]) && !empty($request->get('basic_local_amount'))) ? $paymentType[$request->get('basic_payment_type_id')].' '.$request->get('basic_local_amount').' '.$paymentCurrency[$request->get('basic_local_currency_id')] : '';
        //     $cb_data['new'] = (isset($paymentType[$request->get('n_basic_payment_type_id')], $paymentCurrency[$request->get('n_basic_local_currency_id')]) && !empty($request->get('basic_local_amount'))) ? $paymentType[$request->get('n_basic_payment_type_id')].' '.$request->get('n_basic_local_amount').' '.$paymentCurrency[$request->get('n_basic_local_currency_id')] : '';
        //     $data[] = $cb_data;
        // }

        if (isset($request->get('CBtoggleCheck')['n_basic_payment_type_id'], $paymentType[$request->get('n_basic_payment_type_id')], $paymentCurrency[$request->get('n_basic_local_currency_id')]) && !empty($request->get('n_basic_local_amount'))) {
            $cb_data = [];
            $cb_data['caption'] = (isset($caption['n_basic_salary']) ? $caption['n_basic_salary'] : '');
            $cb_data['old'] = (isset($paymentType[$request->get('basic_payment_type_id')], $paymentCurrency[$request->get('basic_local_currency_id')]) && !empty($request->get('basic_local_amount'))) ? $paymentType[$request->get('basic_payment_type_id')] . ' ' . $request->get('basic_local_amount') . ' ' . $paymentCurrency[$request->get('basic_local_currency_id')] : '';
            $cb_data['new'] = $paymentType[$request->get('n_basic_payment_type_id')] . ' ' . $request->get('n_basic_local_amount') . ' ' . $paymentCurrency[$request->get('n_basic_local_currency_id')];
            $data[] = $cb_data;
        }

        if (isset($request->get('CBtoggleCheck')['n_overseas_payment_type_id'], $paymentType[$request->get('n_overseas_payment_type_id')], $paymentCurrency[$request->get('n_overseas_local_currency_id')]) && !empty($request->get('n_overseas_local_amount'))) {
            $cb_data = [];
            $cb_data['caption'] = (isset($caption['n_overseas_allowance']) ? $caption['n_overseas_allowance'] : '');
            $cb_data['old'] = (isset($paymentType[$request->get('overseas_payment_type_id')], $paymentCurrency[$request->get('overseas_local_currency_id')]) && !empty($request->get('overseas_local_amount'))) ? $paymentType[$request->get('overseas_payment_type_id')] . ' ' . $request->get('overseas_local_amount') . ' ' . $paymentCurrency[$request->get('overseas_local_currency_id')] : '';
            $cb_data['new'] = $paymentType[$request->get('n_overseas_payment_type_id')] . ' ' . $request->get('n_overseas_local_amount') . ' ' . $paymentCurrency[$request->get('n_overseas_local_currency_id')];
            $data[] = $cb_data;
        }

        if (isset($request->get('CBtoggleCheck')['n_house_payment_type_id'], $paymentType[$request->get('n_house_payment_type_id')], $paymentCurrency[$request->get('n_house_local_currency_id')]) && !empty($request->get('n_house_local_amount'))) {
            $cb_data = [];
            $cb_data['caption'] = (isset($caption['n_house_rent']) ? $caption['n_house_rent'] : '');
            $cb_data['old'] = (isset($paymentType[$request->get('house_payment_type_id')], $paymentCurrency[$request->get('house_local_currency_id')]) && !empty($request->get('house_local_amount'))) ? $paymentType[$request->get('house_payment_type_id')] . ' ' . $request->get('house_local_amount') . ' ' . $paymentCurrency[$request->get('house_local_currency_id')] : '';
            $cb_data['new'] = $paymentType[$request->get('n_house_payment_type_id')] . ' ' . $request->get('n_house_local_amount') . ' ' . $paymentCurrency[$request->get('n_house_local_currency_id')];
            $data[] = $cb_data;
        }

        if (isset($request->get('CBtoggleCheck')['n_conveyance_payment_type_id'], $paymentType[$request->get('n_conveyance_payment_type_id')], $paymentCurrency[$request->get('n_conveyance_local_currency_id')]) && !empty($request->get('n_conveyance_local_amount'))) {
            $cb_data = [];
            $cb_data['caption'] = (isset($caption['n_conveyance']) ? $caption['n_conveyance'] : '');
            $cb_data['old'] = (isset($paymentType[$request->get('conveyance_payment_type_id')], $paymentCurrency[$request->get('conveyance_local_currency_id')]) && !empty($request->get('conveyance_local_amount'))) ? $paymentType[$request->get('conveyance_payment_type_id')] . ' ' . $request->get('conveyance_local_amount') . ' ' . $paymentCurrency[$request->get('conveyance_local_currency_id')] : '';
            $cb_data['new'] = $paymentType[$request->get('n_conveyance_payment_type_id')] . ' ' . $request->get('n_conveyance_local_amount') . ' ' . $paymentCurrency[$request->get('n_conveyance_local_currency_id')];
            $data[] = $cb_data;
        }

        if (isset($request->get('CBtoggleCheck')['n_medical_payment_type_id'], $paymentType[$request->get('n_medical_payment_type_id')], $paymentCurrency[$request->get('n_medical_local_currency_id')]) && !empty($request->get('n_medical_local_amount'))) {
            $cb_data = [];
            $cb_data['caption'] = (isset($caption['n_medical_allowance']) ? $caption['n_medical_allowance'] : '');
            $cb_data['old'] = (isset($paymentType[$request->get('medical_payment_type_id')], $paymentCurrency[$request->get('medical_local_currency_id')]) && !empty($request->get('medical_local_amount'))) ? $paymentType[$request->get('medical_payment_type_id')] . ' ' . $request->get('medical_local_amount') . ' ' . $paymentCurrency[$request->get('medical_local_currency_id')] : '';
            $cb_data['new'] = $paymentType[$request->get('n_medical_payment_type_id')] . ' ' . $request->get('n_medical_local_amount') . ' ' . $paymentCurrency[$request->get('n_medical_local_currency_id')];
            $data[] = $cb_data;
        }

        if (isset($request->get('CBtoggleCheck')['n_ent_payment_type_id'], $paymentType[$request->get('n_ent_payment_type_id')], $paymentCurrency[$request->get('n_ent_local_currency_id')]) && !empty($request->get('n_ent_local_amount'))) {
            $cb_data = [];
            $cb_data['caption'] = (isset($caption['n_entertainment_allowance']) ? $caption['n_entertainment_allowance'] : '');
            $cb_data['old'] = (isset($paymentType[$request->get('ent_payment_type_id')], $paymentCurrency[$request->get('ent_local_currency_id')]) && !empty($request->get('ent_local_amount'))) ? $paymentType[$request->get('ent_payment_type_id')] . ' ' . $request->get('ent_local_amount') . ' ' . $paymentCurrency[$request->get('ent_local_currency_id')] : '';
            $cb_data['new'] = $paymentType[$request->get('n_ent_payment_type_id')] . ' ' . $request->get('n_ent_local_amount') . ' ' . $paymentCurrency[$request->get('n_ent_local_currency_id')];
            $data[] = $cb_data;
        }

        if (isset($request->get('CBtoggleCheck')['n_bonus_payment_type_id'], $paymentType[$request->get('n_bonus_payment_type_id')], $paymentCurrency[$request->get('n_bonus_local_currency_id')]) && !empty($request->get('n_bonus_local_amount'))) {
            $cb_data = [];
            $cb_data['caption'] = (isset($caption['n_annual_bonus']) ? $caption['n_annual_bonus'] : '');
            $cb_data['old'] = (isset($paymentType[$request->get('bonus_payment_type_id')], $paymentCurrency[$request->get('bonus_local_currency_id')]) && !empty($request->get('bonus_local_amount'))) ? $paymentType[$request->get('bonus_payment_type_id')] . ' ' . $request->get('bonus_local_amount') . ' ' . $paymentCurrency[$request->get('bonus_local_currency_id')] : '';
            $cb_data['new'] = $paymentType[$request->get('n_bonus_payment_type_id')] . ' ' . $request->get('n_bonus_local_amount') . ' ' . $paymentCurrency[$request->get('n_bonus_local_currency_id')];
            $data[] = $cb_data;
        }

        if (isset($request->get('CBtoggleCheck')['n_other_benefits'])) {
            $cb_data = [];
            $cb_data['caption'] = (isset($caption['n_other_fringe_benefits']) ? $caption['n_other_fringe_benefits'] : '');
            // $cb_data['old'] = $request->get('other_benefits');
            $cb_data['old'] = (empty($request->get('other_benefits')) ? '  ' : $request->get('other_benefits'));
            $cb_data['new'] = $request->get('n_other_benefits');
            $data[] = $cb_data;
        }

        return $data;
    }

    public function preview()
    {
        return view("WorkPermitAmendment::preview");
    }

    public function uploadDocument()
    {
        return View::make('WorkPermitAmendment::ajaxUploadFile');
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
            // 3 = Service & Govt. Fee Payment
            if ($paymentInfo->payment_category_id == 1) {
                if ($processData->status_id != '-1') {
                    Session::flash('error', 'This is an invalid status, it\'s not possible to get the next status. [WPA-912]');
                    return redirect('process/work-permit-amendment/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
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
                    return redirect('process/work-permit-amendment/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
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
            return redirect('process/work-permit-amendment/view-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('WPAAfterPayment: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [WPA-1051]');
            Session::flash(
                'error',
                'Something went wrong!, application not updated after payment. Error : ' . $e->getMessage() . ' [WPA-1051]'
            );
            return redirect('process/work-permit-amendment/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
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
                'process_type.process_supper_name',
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
            }
            /*
            * Government payment submit
            * */ elseif ($paymentInfo->is_verified == 1 && $paymentInfo->payment_category_id == 2) {

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
             */ elseif ($paymentInfo->is_verified == 0 && $paymentInfo->payment_category_id == 1) {
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
            return redirect('process/work-permit-amendment/view-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('WPAAfterCounterPayment: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [WPA-1052]');
            Session::flash(
                'error',
                'Something went wrong!, application not updated after payment. Error : ' . $e->getMessage() . '[WPA-1052]'
            );
            return redirect('process/work-permit-amendment/edit-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        }
    }

    public function Payment(Request $request)
    {
        try {

            $appId = Encryption::decodeId($request->get('app_id'));

            // Get Payment Configuration
            $payment_config = PaymentConfiguration::leftJoin(
                'sp_payment_category',
                'sp_payment_category.id',
                '=',
                'sp_payment_configuration.payment_category_id'
            )
                ->where([
                    'sp_payment_configuration.process_type_id' => $this->process_type_id,
                    'sp_payment_configuration.payment_category_id' => 2,  // Government fee Payment
                    'sp_payment_configuration.status' => 1,
                    'sp_payment_configuration.is_archive' => 0,
                ])->first(['sp_payment_configuration.*', 'sp_payment_category.name']);
            if (empty($payment_config)) {
                Session::flash('error', "Payment configuration not found [WPA-1456]");
                return redirect()->back()->withInput();
            }

            // Get payment distributor list
            $stakeDistribution = PaymentDistribution::where('sp_pay_config_id', $payment_config->id)
                ->where('status', 1)
                ->where('is_archive', 0)
                ->get(['id', 'stakeholder_ac_no', 'pay_amount', 'fix_status', 'purpose', 'purpose_sbl', 'distribution_type']);
            if ($stakeDistribution->isEmpty()) {
                Session::flash('error', "Stakeholder not found [WPA-101]");
                return redirect()->back()->withInput();
            }

            // Check the Govt. vat fee is allowed or not: boolean
            $vatFreeAllowed = BasicInformationController::isAllowedWithOutVAT($this->process_type_id);

            // Store payment information
            DB::beginTransaction();

            $paymentInfo = SonaliPayment::firstOrNew([
                'app_id' => $appId, 'process_type_id' => $this->process_type_id,
                'payment_config_id' => $payment_config->id
            ]);

            $paymentInfo->payment_config_id = $payment_config->id;
            $paymentInfo->app_id = $appId;
            $paymentInfo->process_type_id = $this->process_type_id;
            $paymentInfo->payment_category_id = $payment_config->payment_category_id;
            $paymentInfo->app_tracking_no = '';

            // Concat Account no of stakeholder
            $account_no = "";
            foreach ($stakeDistribution as $distribution) {

                // 4 = Vendor-Vat-Fee, 5 = Govt-Vat-Fee, 6 = Govt-Vendor-Vat-Fee
                if ($vatFreeAllowed && in_array($distribution->distribution_type, [4, 5, 6])) {
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

            WorkPermitAmendment::where('id', $appId)->update([
                'gf_payment_id' => $paymentInfo->id
            ]);

            if ($vatFreeAllowed) {
                SonaliPaymentController::vatFreeAuditStore($paymentInfo->id, $unfixed_amount_array['total_vat_on_pay_amount']);
            }

            // Payment Details By Stakeholders
            foreach ($stakeDistribution as $distribution) {

                // 4 = Vendor-Vat-Fee, 5 = Govt-Vat-Fee, 6 = Govt-Vendor-Vat-Fee
                if ($vatFreeAllowed && in_array($distribution->distribution_type, [4, 5, 6])) {
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

            DB::commit();
            if ($request->get('actionBtn') == 'Submit' && $paymentInfo->id) {
                return redirect('spg/initiate-multiple/' . Encryption::encodeId($paymentInfo->id));
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('WPAPayment: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [WPA-1025]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . "[WPA-1025]");
            return redirect()->back()->withInput();
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
                $file_path = trim(uniqid('BIDA_WPA-' . $appId . '-', true) . $_file_path->getClientOriginalName());
                $_file_path->move($path, $file_path);
                $conditional_approved_file = $yearMonth . $file_path;
            }

            WorkPermitAmendment::where('id', $appId)->update([
                'conditional_approved_file'     => isset($conditional_approved_file) ? $conditional_approved_file : '',
                'conditional_approved_remarks'  => $request->get('conditional_approved_remarks')
            ]);

            $processData = ProcessList::leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
                ->where('ref_id', $appId)
                ->where('process_type_id', $this->process_type_id)
                ->first([
                    'process_list.*',
                    'process_type.form_id'
                ]);

            if (!in_array($processData->status_id, [17, 31])) {
                Session::flash('error', 'This is an invalid status, it\'s not possible to get the next status. [WPA-913]');
                return redirect('process/work-permit-amendment/view-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
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
            return redirect('process/work-permit-amendment/view-app/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('WPAConditionalApproveStore: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [WPA-1026]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . "[WPA-1026]");
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
}