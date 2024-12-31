<?php

namespace App\Modules\Signup\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\ETINverification;
use App\Libraries\ImageProcessing;
use App\Libraries\nidTokenServiceJWT;
use App\Libraries\NIDverification;
use App\Libraries\OtpService;
use App\Libraries\UtilFunction;
use App\Modules\CompanyAssociation\Models\CompanyAssociation;
use App\Modules\Signup\Models\UserVerificationData;
use App\Modules\Signup\Models\UserVerificationOtp;
use App\Modules\Users\Models\CompanyInfo;
use App\Modules\Users\Models\Countries;
use App\Modules\Users\Models\Users;
use App\Modules\Users\Models\UserTypes;
use App\Modules\Users\Models\AreaInfo;
use Illuminate\Http\Request;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use stdClass;
use GuzzleHttp\Client;
use App\Modules\Settings\Models\Configuration;


class SignupController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user_types = ['' => 'Select One'] + UserTypes::orderBy('type_name')->where('is_registarable', 1)->orderBy('type_name', 'ASC')->lists('type_name', 'id')->all();
        $countries = Countries::orderby('name')->lists('nicename', 'iso');
        $nationalities = Countries::orderby('nationality')->where('nationality', '!=', '')->lists('nationality', 'iso');
        $divisions = AreaInfo::orderby('area_nm')->where('area_type', 1)->lists('area_nm', 'area_id');
        $districts = AreaInfo::orderby('area_nm')->where('area_type', 2)->lists('area_nm', 'area_id');

        return view("Signup::registration", compact("user_types", "nationalities", "countries", "divisions", "districts"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'user_full_name' => 'required',
            'user_DOB' => 'required|date',
            'user_phone' => 'required',
            'user_email' => 'required|email',
            'country' => 'required',
            'nationality' => 'required',
            'road_no' => 'required',
            'g-recaptcha-response' => 'required'
        ]);


        try {
            $requestEmail = $request->get('user_email');
            $result = Users::where('user_email', '=', $requestEmail)->first();
            if ($result) {
                if ($result->user_status == "inactive" && strtotime($result->created_at) > strtotime("-6 hours")) {
                    \Session::flash('error', "Your email already taken. You may try after 6 Hours again.");
                    return Redirect::back()->withInput();
                } else if ($result->user_status == "inactive" && $result->is_approved == 1) {
                    \Session::flash('error', "The email is already exists and user is inactive now. Please contact with System Admin.");
                    return Redirect::back()->withInput();
                } else if ($result->user_status == "active") {
                    \Session::flash('error', "Your email already taken and you are active user.");
                    return Redirect::back()->withInput();
                } else if ($result->user_verification == 'no') {
                    \Session::flash('verifyNo', "You have previously received a sign up request with this email address, now you are Inactive or Rejected user, please click the 'Resend Email' button and follow the given instructions to complete your sign up process.");
                    return redirect('signup?tmp=' . Encryption::encodeId($request->get('user_email')));
                } else {
                }

            }

            DB::beginTransaction();
            $approve_status = 0;
            $user_status = 'inactive';
            $token_no = hash('SHA256', "-" . $request->get('user_email') . "-");
            $encrypted_token = Encryption::encodeId($token_no);
            $insertData = Users::firstOrNew(['user_email' => $requestEmail]);
            $insertData->user_full_name = $request->get('user_full_name');
            $insertData->user_type = $request->get('user_type');
            $insertData->nationality = $request->get('nationality');
            $insertData->identity_type = $request->get('identity_type');
            $insertData->passport_no = $request->get('passport_no');
            $insertData->user_nid = $request->get('user_nid');
            $insertData->country = $request->get('country');
            if (!empty($request->get('user_DOB')))
                $insertData->user_DOB = CommonFunction::changeDateFormat($request->get('user_DOB'), true);
            $insertData->user_phone = $request->get('user_phone');
            $insertData->user_hash = $encrypted_token;
            $insertData->division = $request->get('division');
            $insertData->district = $request->get('district');
            $insertData->state = $request->get('state');
            $insertData->province = $request->get('province');
            $insertData->road_no = $request->get('road_no');
            $insertData->house_no = $request->get('house_no');
            $insertData->post_code = $request->get('post_code');
            $insertData->user_fax = $request->get('user_fax');
            $insertData->user_hash_expire_time = new Carbon('+6 hours');
            $insertData->is_approved = $approve_status;
            $insertData->user_status = $user_status;
            $insertData->user_agreement = 0;
            $insertData->first_login = 0;
            $insertData->user_verification = 'no';
            $insertData->save();

            if ($this->signupMail($request, $encrypted_token)) {
                DB::commit(); //DB Commit
                return redirect('signup?tmp=' . Encryption::encodeId($request->get('user_email')));
            } else {
                DB::rollback(); //DB rollback
                return Redirect::back()->withInput();
            }
        } catch (\Exception $e) {
            DB::rollback(); //DB rollback
            Log::error('SignUp: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [SIGNUP1001]');
            Session::flash('error', 'Something went wrong[SIGNUP1001]');
            return Redirect::back()->withInput();
        }
    }

    public function GoogleStore(Request $request)
    {
        $user = Auth::user();
        $approve_status = 0;
        $rules = [
            'user_full_name' => 'required',
            'user_DOB' => 'required|date',
            'district' => 'required',
            'division' => 'required',
        ];

        if ($request->get('company_type') == 1) { // existing company
            $rules['company_id'] = 'required';
        } else { // new company
            $rules['company_name'] = 'required|regex:/^[a-zA-Z\'\. \&]+$/';
            $rules['company_division'] = 'required';
            $rules['company_district'] = 'required';
            $rules['company_thana'] = 'required';
        }

        $rules['user_agreement'] = 'required';
        $this->validate($request, $rules);

        try {
            $companyId = '';
            if ($request->get('company_type') == 1) { // existing company
                $companyId = $request->get('company_id');
            } else if ($request->get('company_type') == 2) { // new company
                $companyData = CompanyInfo::where('company_name', trim($request->get('company_name')))->first();
                if (count($companyData) > 0) {
                    if ($companyData->is_rejected == 'no') {
                        Session::flash('error', 'Your company name is Duplicate! Please give an Unique name');
                        return Redirect::back()->withInput();
                    } else {
                        $companyData->company_name = $request->get('company_name');
                        $companyData->division = $request->get('company_division');
                        $companyData->district = $request->get('company_district');
                        $companyData->thana = $request->get('company_thana');
                        $companyData->created_by = $user->id;
                        $companyData->created_at = Carbon::now();
                        $companyData->is_rejected = 'no'; // again reset the rejected status and is_approved
                        $companyData->is_approved = 0;
                        $companyData->save();
                        $companyId = $companyData->id;
                    }
                } else {
                    $companyId = DB::table('company_info')->insertGetId([
                        'company_name' => trim($request->get('company_name')),
                        'created_by' => $user->id,
                        'created_at' => Carbon::now()
                    ]);
                }
            }

            $data = [
                'user_full_name' => $request->get('user_full_name'),
                'user_type' => $request->get('user_type'),
                'company_ids' => $companyId,
                'identity_type' => $request->get('identity_type'),
                'passport_no' => $request->get('passport_no'),
                'user_nid' => $request->get('user_nid'),
                'nationality' => $request->get('nationality'),
                'user_DOB' => CommonFunction::changeDateFormat($request->get('user_DOB'), true),
                'country' => $request->get('country'),
                'division' => $request->get('division'),
                'district' => $request->get('district'),
                'state' => $request->get('state'),
                'province' => $request->get('province'),
                'road_no' => $request->get('road_no'),
                'user_phone' => $request->get('user_phone'),
                'user_verification' => 'yes',
                'is_approved' => $approve_status,
                'user_agreement' => $request->get('user_agreement'),
            ];
            Users::where('id', $user->id)->update($data);
            $this->entryAccessLog();
            return redirect()->to('/dashboard');
        } catch (\Exception $e) {
            Session::flash('error', 'Something went wrong[SIGNUP10056]');
            return Redirect::back()->withInput();
        }
    }

    private function signupMail($request, $token)
    {
        try {
            \Session::flash('success', 'Thanks for signing up! Please check your email and follow the instruction to complete the sign up process');
            return true;
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something went wrong, Please try again later.');
            return false;
        }
    }

    public function verification($confirmationCode)
    {
        $user = Users::where('user_hash', $confirmationCode)->first();
        if (!$user) {
            \Session::flash('error', 'Invalid Token! Please resend email verification link.');
            return redirect('login');
        }
        $currentTime = new Carbon;
        $validateTime = new Carbon($user->created_at . '+6 hours');

        if ($currentTime >= $validateTime) {
            Session::flash('error', 'Verification link is expired (validity period 6 hrs). Please sign up again!');
            return redirect('/login');
        }

        $user_type = $user->user_type;
        if ($user->user_verification != 'yes') {
            $company_list = ['' => 'Select Existing Company '] + CompanyInfo::leftJoin('area_info as ai', 'ai.area_id', '=', 'company_info.division')
                    ->leftJoin('area_info as di', 'di.area_id', '=', 'company_info.thana')
                    ->select('id', DB::raw('CONCAT(company_name, ", ", ai.area_nm,", ", di.area_nm) AS company_info'))->where('is_approved', 1)->where('company_status', 1)->where('is_rejected', 'no')
                    ->orderBy('company_name', 'ASC')->lists('company_info', 'id')->all();

            $divisions = ['' => 'Select Division '] + AreaInfo::orderby('area_nm')->where('area_type', 1)->lists('area_nm', 'area_id')->all();
            $districts = AreaInfo::orderby('area_nm')->where('area_type', 2)->lists('area_nm', 'area_id');
            $thana = AreaInfo::orderby('area_nm')->where('area_type', 3)->lists('area_nm', 'area_id');

            return view('Signup::verification', compact('user_type', 'confirmationCode', 'districts', 'divisions', 'thana', 'company_list'));
        } else {
            \Session::flash('error', 'Invalid Token! Please sign up again.');
            return redirect('signup/resend-mail');
        }
    }

    function verificationStore($confirmationCode, Request $request, Users $usersmodel)
    {
        $rules = [];
        $rules['company_type'] = 'required';
        $rules['company_id'] = 'required_if:company_type,1';
        $rules['company_name'] = 'required_if:company_type,2|regex:/^[a-zA-Z\'\. \&]+$/';
        $rules['division'] = 'required_if:company_type,2';
        $rules['district'] = 'required_if:company_type,2';
        $rules['thana'] = 'required_if:company_type,2';
        $rules['user_agreement'] = 'required';
        $this->validate($request, $rules);

        try {
            $TOKEN_NO = $confirmationCode;
            $user_password = str_random(10);
            $user = Users::where('user_hash', $TOKEN_NO)->first();
            if (!$user) {
                \Session::flash('error', 'Invalid token! Please sign up again to complete the process');
                return redirect('create');
            }

            DB::beginTransaction();

            $data = array(
                'details' => $request->get('details'),
                'user_agreement' => $request->get('user_agreement'),
                'password' => Hash::make($user_password),
                'user_verification' => 'yes',
                'user_first_login' => Carbon::now()
            );

            if ($request->get('company_type') == 1) { // existing company
                $data['company_ids'] = $request->get('company_id');
            } else if ($request->get('company_type') == 2) { // new company
                $companyData = CompanyInfo::where('company_name', trim($request->get('company_name')))->first();
                if ($companyData) {
                    if ($companyData->is_rejected == 'no') {
                        Session::flash('error', 'Your company name is Duplicate! Please give an Unique name');
                        return Redirect::back()->withInput();
                    } else {
                        $companyData->company_name = trim($request->get('company_name'));
                        $companyData->created_by = $user->id;
                        $companyData->created_at = Carbon::now();
                        $companyData->is_rejected = 'no'; // again reset the rejected status and is_approved
                        $companyData->is_approved = 0;
                        $companyData->save();
                        $companyId = $companyData->id;
                    }
                } else {
                    $companyId = DB::table('company_info')->insertGetId([
                        'company_name' => trim($request->get('company_name')),
                        'created_by' => $user->id,
                        'created_at' => Carbon::now()
                    ]);
                }
                $data['company_ids'] = $companyId;
            }
            $usersmodel->chekced_verified($TOKEN_NO, $data);
            DB::commit();
            \Session::flash('success', 'Thanks for signing up! Please check your email for the account activation message.');
            return redirect('login');
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [SUC1005]');
            return Redirect::back()->withInput();
        }
    }

    public function resendMail(Request $request)
    {
        try {
            $email = Encryption::decodeId(Input::get('tmp'));
            $result = DB::table('users')->where('user_email', '=', $email)->first();
            $ACTIVE_STATUS = $result->user_status;
            $encrypted_token = Encryption::encode($result->user_hash);
            $verify_link = 'signup/verification/' . ($encrypted_token);


            if ($ACTIVE_STATUS == 'inactive') {

                \Session::flash("success", "An email has been re-sent to your address.<br/>
                                Please check the newest email and follow the instructions to complete the sign up process.<br/>
                                Thank you!<br/>");
            } elseif ($ACTIVE_STATUS == 'active') {


                \Session::flash('success', 'Please check your email for new update!');
            }
            $ecptEmail = Encryption::encodeId($email);
            return redirect('signup?tmp=' . $ecptEmail);
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something is Wrong.');
            return Redirect::back()->withInput();
        }
    }

    public function google_signUp()
    {

        $company_list = ['' => 'Select Company '] + CompanyInfo::leftJoin('area_info as ai', 'ai.area_id', '=', 'company_info.division')
                ->leftJoin('area_info as di', 'di.area_id', '=', 'company_info.thana')
                ->select('id', DB::raw('CONCAT(company_name, ", ", ai.area_nm,", ", di.area_nm) AS company_info'))->where('is_approved', 1)->where('company_status', 1)
                ->orderBy('company_name', 'ASC')->lists('company_info', 'id')->all();

        $user_types = ['' => 'Select One'] + UserTypes::orderBy('type_name')->where('is_registarable', 1)->orderBy('type_name', 'ASC')->lists('type_name', 'id')->all();
        $countries = Countries::orderby('name')->lists('name', 'iso');
        $nationalities = Countries::orderby('nationality')->where('nationality', '!=', '')->lists('nationality', 'iso');
        $divisions = ['' => 'Select Division '] + AreaInfo::orderby('area_nm')->where('area_type', 1)->lists('area_nm', 'area_id')->all();
        $districts = AreaInfo::orderby('area_nm')->where('area_type', 2)->lists('area_nm', 'area_id');
        $thana = AreaInfo::orderby('area_nm')->where('area_type', 3)->lists('area_nm', 'area_id');


        return view("Signup::google_signup", compact("user_types", "nationalities", "thana", "countries", "divisions", "districts", "company_list"));
    }

    public function identityVerifyOtp(Request $request)
    {
        try{
            if (isset(Session::get('oauth_data')->mobile)) {
                $otpService = new OtpService();
                $url = $otpService->generateOtpVerificationUrl(Session::get('oauth_data')->mobile);
                return redirect()->away($url);
            }else{
                return redirect()->to('/login');
            }
            // old below code is for previous version
            $otpExpireTime = Configuration::where('caption', 'SIGNUP_NID_OTP_JWT_TOKEN_TIME')->pluck('value');
            if (empty($otpExpireTime)) {
                return response()->json([
                    'status' => "error",
                    'statusCode' => '400',
                    'message' => 'SIGNUP NID OTP JWT TOKEN TIME NOT FOUND'
                ]);
            }
            if ($request->ajax() && $request->isMethod('post')) {
                $rules['user_mobile'] = 'required';
                $this->validate($request, $rules);
                $userMobile = $request->get('user_mobile');

                // add user_mobile to oauth_data session
                $oauthData = session('oauth_data');
                $oauthDataObject = json_decode(json_encode($oauthData));
                $oauthDataObject->user_mobile = $userMobile;
                session(['oauth_data' => $oauthDataObject]);

                $today = Carbon::today();

                $countTodayOtpVerification = UserVerificationOtp::whereDate('created_at', '=', $today->toDateString())
                    ->where('otp_status', '<>', 0)
                    ->where(function ($query) use ($userMobile) {
                        $query->where('user_email', Session::get('oauth_data')->user_email)
                            ->orWhere('user_mobile', $userMobile);
                    })
                    ->count();

                if($countTodayOtpVerification <= 10){
                    $nidVerificationOtpSendResponse = UtilFunction::nidVerificationOtpSend($userMobile, $otpExpireTime);
                    return response()->json($nidVerificationOtpSendResponse);
                }

                return response()->json([
                    'status' => "error",
                    'statusCode' => '400',
                    'message' => 'Your Daily otp request exceed!'
                ]);
            }

            if (!Session::has('oauth_token') or !Session::has('oauth_data')) {
                Session::flash('error', 'You have no access right! This incidence will be reported. Contact with system admin for more information.');
                return redirect()->to('/login');
            }

            $data['otpExpireTime'] = $otpExpireTime;
            $data['otpExpireTimeInMinutes'] = $otpExpireTime / 60;

            return view('Signup::identity-verify-otp', $data);

        } catch (\Exception $e) {
            Log::error("Error occurred in SignupController@identityVerifyOtp ({$e->getFile()}:{$e->getLine()}): {$e->getMessage()}");
            Session::flash('error', "Something went wrong [identityVerifyOtp-1001]");
            return Redirect::back();
        }
    }

    public function otpVerify(Request $request)
    {
        $otpVerificationData = UserVerificationOtp::where('user_email', Session::get('oauth_data')->user_email)
            ->where('user_mobile', Session::get('oauth_data')->user_mobile)
            ->where('otp', $request->get('otp_value'))
            ->where('otp_expire_time', '>', Carbon::now())
            ->orderBy('id', 'DESC')
            ->first();

        if($otpVerificationData && $otpVerificationData->otp_status == 1){
            $otpVerificationData->otp_status = 2;
            $otpVerificationData->save();

            $clientData = new stdClass();
            $clientData->client_id = config('app.NID_JWT_ID');
            $clientData->client_secret_key = config('app.NID_JWT_SECRET_KEY');
            $clientData->encryption_key = config('app.NID_JWT_ENCRYPTION_KEY');

            // JNID verification JWT token generation
            $tokenService = new nidTokenServiceJWT();
            $jwtTokenArray = $tokenService->generateNIDToken($clientData);

            // NID verification JWT token store
            $tokenService->storeNIDToken($jwtTokenArray);
            return redirect()->to('signup/identity-verify');
        }
        Session::flash('error', "Invalid OTP! [otpVerify-1002]");
        return Redirect::back();
    }

    public function identityVerifyOld()
    {
        if (!Session::has('oauth_token') or !Session::has('oauth_data')) {
            Session::flash('error', 'You have no access right! This incidence will be reported. Contact with system admin for more information.');
            return redirect()->to('/login');
        }

        $nidUserVerificationData = UserVerificationOtp::where('user_email', Session::get('oauth_data')->user_email)
            ->orderBy('id', 'DESC')
            ->first();

        if($nidUserVerificationData && $nidUserVerificationData->token_expire_time > Carbon::now() && $nidUserVerificationData->token_status == 1){
            $countries = Countries::where('country_status', 'Yes')->orderby('nicename')->lists('nicename', 'id');
            $passport_types = [
                'ordinary' => 'Ordinary',
                'diplomatic' => 'Diplomatic',
                'official' => 'Official',
            ];
            $nationalities = Countries::orderby('nationality')->where('nationality', '!=', '')
                ->lists('nationality', 'id');
            $passport_nationalities = Countries::orderby('nationality')->where('nationality', '!=', '')->where('nationality', '!=', 'Bangladeshi')
                ->lists('nationality', 'id');

            $getPreviousVerificationData = UserVerificationData::where('user_email', Session::get('oauth_data')->user_email)->where('created_at', '>=', Carbon::now()->subDay())->first();

            $previous_info = '';

            if (!empty($getPreviousVerificationData) && $getPreviousVerificationData->identity_type == 'tin') {
                $previous_info = json_decode(Encryption::decode($getPreviousVerificationData->eTin_info), true);
            } elseif (!empty($getPreviousVerificationData) && $getPreviousVerificationData->identity_type == 'nid') {
                $previous_info = json_decode(Encryption::decode($getPreviousVerificationData->nid_info), true);
            } elseif (!empty($getPreviousVerificationData) && $getPreviousVerificationData->identity_type == 'passport') {
                $previous_info = json_decode(Encryption::decode($getPreviousVerificationData->passport_info), true);
            }

            return view('Signup::identity-verify', compact('countries', 'passport_types', 'nationalities',
                'passport_nationalities', 'getPreviousVerificationData', 'previous_info'));
        }

        // if token status 2 then flash error message not show
        if($nidUserVerificationData && $nidUserVerificationData->token_status == 2){
            return redirect()->route('signup.identity_verify_otp');
        }

        Session::flash('error', 'Token not found! You have no access right! This incidence will be reported.');
        return redirect()->route('signup.identity_verify_otp');
    }

    public function identityVerify()
    {
        if (!Session::has('oauth_token') or !Session::has('oauth_data')) {
            Session::flash('error', 'You have no access right! This incidence will be reported. Contact with system admin for more information.');
            return redirect()->to('/login');
        }

        $nidUserVerificationData = UserVerificationOtp::where('user_email', Session::get('oauth_data')->user_email)
            ->orderBy('id', 'DESC')
            ->first();

        if($nidUserVerificationData && $nidUserVerificationData->token_expire_time > Carbon::now() && $nidUserVerificationData->token_status == 1){

            $countries = Countries::where('country_status', 'Yes')->orderby('nicename')->lists('nicename', 'id');
            $passport_types = [
                'ordinary' => 'Ordinary',
                'diplomatic' => 'Diplomatic',
                'official' => 'Official',
            ];
            $nationalities = Countries::orderby('nationality')->where('nationality', '!=', '')
                ->lists('nationality', 'id');
            $passport_nationalities = Countries::orderby('nationality')->where('nationality', '!=', '')->where('nationality', '!=', 'Bangladeshi')
                ->lists('nationality', 'id');

            $getPreviousVerificationData = UserVerificationData::where('user_email', Session::get('oauth_data')->user_email)->where('created_at', '>=', Carbon::now()->subDay())->first();

            $previous_info = '';

            if (!empty($getPreviousVerificationData) && $getPreviousVerificationData->identity_type == 'tin') {
                $previous_info = json_decode(Encryption::decode($getPreviousVerificationData->eTin_info), true);
            } elseif (!empty($getPreviousVerificationData) && $getPreviousVerificationData->identity_type == 'nid') {
                $previous_info = json_decode(Encryption::decode($getPreviousVerificationData->nid_info), true);
            } elseif (!empty($getPreviousVerificationData) && $getPreviousVerificationData->identity_type == 'passport') {
                $previous_info = json_decode(Encryption::decode($getPreviousVerificationData->passport_info), true);
            }

            return view('Signup::identity-verify', compact('countries', 'passport_types', 'nationalities',
                'passport_nationalities', 'getPreviousVerificationData', 'previous_info'));
        }

        // if token status 2 then flash error message not show
        if($nidUserVerificationData && $nidUserVerificationData->token_status == 2){
            return redirect()->route('signup.identity_verify_otp');
        }

        Session::flash('error', 'Token not found! You have no access right! This incidence will be reported.');
        return redirect()->route('signup.identity_verify_otp');
    }


    public function nidVerifyAuth(Request $request)
    {
        if(isset(Auth::user()->id)){
            return $this->nidVerifyRequest($request);
        }else{
            return response()->json([
                'status' => "error",
                'message' => 'Unauthorized access!'
            ]);
        }
    }

    /**
     * NID verification request
     * @param Request $request
     * @return string
     */
    public function nidVerifyRequest(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'status' => "error",
                'message' => 'Sorry! this is a request without proper way.'
            ]);
        }

        $recaptchaResponse = $request->get('g_recaptcha_response');

        if (is_null($recaptchaResponse)) {
            return response()->json([
                'status' => "error",
                'message' => 'Please Complete the Recaptcha to proceed.'
            ]);
        }

        $recaptchaVerificationResponse = UtilFunction::verifyGoogleReCaptcha($recaptchaResponse);

        if(!$recaptchaVerificationResponse['data']->success){
            return response()->json([
                'status' => "error",
                'message' => 'Please Complete the Recaptcha Again to proceed'
            ]);
        }

        if(!isset(Auth::user()->id)){
            $nidUserVerificationData = UserVerificationOtp::where('user_email', Session::get('oauth_data')->user_email)
                ->orderBy('id', 'DESC')
                ->first();

            if($nidUserVerificationData && $nidUserVerificationData->token_expire_time > Carbon::now() && $nidUserVerificationData->token_status == 1) {

                // Check token validity
                $tokenService = new nidTokenServiceJWT();
                $encryptionKey = config('app.NID_JWT_ENCRYPTION_KEY');

                //token validity check
                if (!$tokenService->checkNIDTokenValidity($nidUserVerificationData->token, $encryptionKey)) {
                    return response()->json([
                        'status' => "error",
                        'message' => 'Authorization failed. Token mismatch!'
                    ]);
                }

                $response = $this->nidVerify($request);

                $responseData = json_decode($response->getContent(), true);

                if(array_key_exists('success', $responseData) && $responseData['success'] == true){
                    $nidUserVerificationData->token_status = 2;
                    $nidUserVerificationData->save();
                }

                return $response;
            }else{
                return response()->json([
                    'status' => "error",
                    'message' => 'Authorization failed. Token mismatch!'
                ]);
            }
        }
        return $this->nidVerify($request);
    }

    private function nidVerify(Request $request)
    {
        $rules = [];
        $messages = [];
        $rules['nid_number'] = 'required|integer|bd_nid';
        $rules['user_DOB'] = 'required|date|date_format:d-M-Y';
        $rules['user_nid_name'] = 'required|regex:/^[a-zA-Z\'\. \-]+$/';
        $validation = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json([
                'status' => "error",
                'statusCode' => 'SIGN-UP-200',
                'data' => [],
                'message' => $validation->errors()
            ]);
        }

        try {
            // Get NID Authorization token
            $nid_verification = new NIDverification();
            $nid_auth_token = $nid_verification->getAuthToken();
            if (empty($nid_auth_token)) {
                return response()->json([
                    'status' => "error",
                    'statusCode' => 'SIGN-UP-212',
                    'data' => [],
                    'message' => 'NID auth token not found! Please try again'
                ]);
            }

            Session::forget('nid_info');
            Session::forget('eTin_info');
            $user_nid_name = $request->get('user_nid_name');
            $user_nid_postal_code = null;
            $nid_number = $request->get('nid_number');
            $user_DOB = $request->get('user_DOB');
            $nid_data = [
                'nid_number' => $nid_number,
                'user_DOB' => $user_DOB,
                'user_nid_name' => $user_nid_name,
                'user_nid_postal_code' => $user_nid_postal_code,
            ];
            $nid_verify_response = $nid_verification->verifyNID($nid_data, $nid_auth_token);

            /* 
             * new nid verification (harun) start
            */
            $data = [
                'success' => false,
                'status' => 500,
                'message' => 'Sorry! NID verification failed. Please try again',
                'response_messages' => 'ERROR',
            ];

            if ($nid_verify_response->status != 200) {
                $data['message'] = 'Sorry! NID and DOB is not valid. Please try again';
            }

            if (isset($user_nid_name) && isset($nid_verify_response->status) && $nid_verify_response->status == 200) {
                // check name
                if (UtilFunction::cleanAndCompareNames($user_nid_name, $nid_verify_response->data->nameEn) ) {
                    $data = [
                        'success' => true,
                        'status' => 200,
                        'message' => 'SUCCESS'
                    ];

                    // nid_info in session
                    $nid_info_data_session = [
                        'nationalId' => $nid_number,
                        'dateOfBirth' => date("Y-m-d", strtotime($user_DOB)),
                        'nameEn' => $user_nid_name,
                        'dob' => $user_DOB,
                        'photo' => null,
                        'permanentAddress' => [
                            'postalCode' => $user_nid_postal_code
                        ],
                    ];
                    Session::put('nid_info', Encryption::encode(json_encode($nid_info_data_session)));

                }else{
                    $data['message'] = 'Sorry! Name is not valid. Please try again';
                }

            }
            return response()->json($data);
            /* 
             * new nid verification (harun) end
            */

        } catch (\Exception $e) {
            Log::error('SignUp: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [SIGN-UP-201]');
            return response()->json([
                'status' => "error",
                'statusCode' => 'SIGN-UP-201',
                'data' => [],
                'message' => CommonFunction::showErrorPublic($e->getMessage())
            ]);
        }
    }

    /**
     * e-TIN Verification request
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function etinVerify(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.';
        }

        $rules = [];
        $messages = [];
        $rules['etin_number'] = 'required|integer|digits_between:10,15';
        $rules['user_DOB'] = 'required|date|date_format:d-M-Y';
        $validation = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json([
                'status' => "error",
                'statusCode' => 'SIGN-UP-202',
                'data' => [],
                'message' => $validation->errors()
            ]);
        }

        try {
            // Get TIN Authorization token
            $etin_verification = new ETINverification();
            $etin_auth_token = $etin_verification->getAuthToken();
            if (empty($etin_auth_token)) {
                return response()->json([
                    'status' => "error",
                    'statusCode' => 'SIGN-UP-213',
                    'data' => [],
                    'message' => 'e-TIN auth token not found! Please try again'
                ]);
            }

            Session::forget('eTin_info');
            Session::forget('nid_info');
            $etin_number = $request->get('etin_number');
            $user_DOB = $request->get('user_DOB');

            $etin_verify_response = $etin_verification->verifyETIn($etin_number, $etin_auth_token);

            $data = [];
            if (isset($etin_verify_response['status']) && $etin_verify_response['status'] === 'success') {

                // Validate Date of birth
                if (date('d-M-Y', strtotime($etin_verify_response['data']['dob'])) != $user_DOB) {
                    return response()->json([
                        'status' => "error",
                        'statusCode' => 'SIGN-UP-203',
                        'data' => [],
                        'message' => 'Sorry! Invalid date of birth. Please provide valid information.'
                    ]);
                }

                // Add etin number with etin_info
                $etin_verify_response['data']['etin_number'] = $etin_number;
                Session::put('eTin_info', Encryption::encode(json_encode($etin_verify_response['data'])));

                // Re-arrange e-tin response
                // Send only some specific data
                $data['nameEn'] = $etin_verify_response['data']['assesName'];
                $data['father_name'] = $etin_verify_response['data']['fathersName'];
                $data['dob'] = $etin_verify_response['data']['dob'];
            }
            $etin_verify_response['data'] = $data;
            return response()->json($etin_verify_response);
        } catch (\Exception $e) {
            Log::error('SignUp: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [SIGN-UP-204]');
            return response()->json([
                'status' => "error",
                'statusCode' => 'SIGN-UP-204',
                'data' => [],
                'message' => CommonFunction::showErrorPublic($e->getMessage())
            ]);
        }
    }

    /**
     * Identity verification and redirect to sign-up page
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function identityVerifyConfirm(Request $request)
    {
        $rules = [];
        $messages = [];

        $rules['nationality_type'] = 'required|in:bangladeshi,foreign';
        $rules['identity_type_bd'] = 'required_if:nationality_type,==,bangladeshi|in:nid,tin';
        $rules['identity_type_foreign'] = 'required_if:nationality_type,==,foreign|in:passport,tin';

        $rules['user_nid_name'] = 'required_if:identity_type_bd,==,nid|string';
        $rules['user_nid'] = 'required_if:identity_type_bd,==,nid|integer|bd_nid';
        $rules['user_DOB'] = 'required_if:identity_type_bd,==,nid|date|date_format:d-M-Y';

        if ($request->get('identity_type_bd') === 'tin' or $request->get('identity_type_foreign') === 'tin') {
            $rules['etin_number'] = 'required|integer';
        }

        $rules['passport_nationality'] = 'required_if:identity_type_foreign,==,passport|integer';
        $rules['passport_type'] = 'required_if:identity_type_foreign,==,passport|in:ordinary,diplomatic,official';
        $rules['passport_no'] = 'required_if:identity_type_foreign,==,passport';
        $rules['passport_surname'] = 'required_if:identity_type_foreign,==,passport';
        $rules['passport_given_name'] = 'required_if:identity_type_foreign,==,passport';
        $rules['passport_DOB'] = 'required_if:identity_type_foreign,==,passport|date|date_format:d-M-Y';
        $rules['passport_date_of_expire'] = 'required_if:identity_type_foreign,==,passport|date|date_format:d-M-Y';
        $rules['passport_upload_base_code'] = 'required_if:identity_type_foreign,==,passport';
        //$rules['passport_copy'] = 'required_if:identity_type_foreign,==,passport|mimes:jpeg,jpg,png|max:1024';
        $this->validate($request, $rules, $messages);

        try {
            $nationality_type = $request->get('nationality_type');
            if ($nationality_type === 'foreign') {
                $identity_type = $request->get('identity_type_foreign');
            } else {
                $identity_type = $request->get('identity_type_bd');
            }

            Session::put('nationality_type', $nationality_type);
            Session::put('identity_type', $identity_type);
            Session::forget('passport_info');

            $passport_info = [];
            if ($identity_type === 'nid') {

                if (!Session::has('nid_info')) {
                    Session::flash('error', 'Something went wrong! please try again' . ' SIGN-UP-205');
                    return \redirect()->back();
                }
                if (empty(Encryption::decode(Session::get('nid_info')))) {
                    Session::flash('error', 'Something went wrong! please try again' . ' SIGN-UP-206');
                    return \redirect()->back();
                }
            } elseif ($identity_type === 'tin') {
                if (!Session::has('eTin_info')) {
                    Session::flash('error', 'Something went wrong! please try again' . ' SIGN-UP-207');
                    return \redirect()->back();
                }
                if (empty(Encryption::decode(Session::get('eTin_info')))) {
                    Session::flash('error', 'Something went wrong! please try again' . ' SIGN-UP-208');
                    return \redirect()->back();
                }
            } elseif ($identity_type === 'passport') {
                $passport_info['passport_nationality'] = $request->passport_nationality;
                $passport_info['passport_type'] = $request->passport_type;
                $passport_info['passport_no'] = $request->passport_no;
                $passport_info['passport_surname'] = $request->passport_surname;
                $passport_info['passport_given_name'] = $request->passport_given_name;
                $passport_info['passport_personal_no'] = $request->passport_personal_no;
                $passport_info['passport_DOB'] = $request->passport_DOB;
                $passport_info['passport_date_of_expire'] = $request->passport_date_of_expire;

                // Passport copy upload
                $yearMonth = date("Y") . "/" . date("m") . "/";
                $path = 'users/upload/' . $yearMonth;
                $passport_pic_name = trim(uniqid('BIDA_PC_PN-' . $request->passport_no . '_', true) . '.' . 'jpeg');
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                if (!empty($request->get('passport_upload_manual_file'))) {
                    $passport_split = explode(',', substr($request->get('passport_upload_manual_file'), 5), 2);
                    $passport_image_data = $passport_split[1];
                    $passport_base64_decode = base64_decode($passport_image_data);
                    file_put_contents($path . $passport_pic_name, $passport_base64_decode);
                } else {
                    $passport_split = explode(',', substr($request->get('passport_upload_base_code'), 5), 2);
                    $passport_image_data = $passport_split[1];
                    $passport_base64_decode = base64_decode($passport_image_data);
                    file_put_contents($path . $passport_pic_name, $passport_base64_decode);
                }

                //$request->file('passport_copy')->move($path, $passport_pic_name);
                // End Passport copy upload

                $passport_info['passport_copy'] = $passport_pic_name;
                Session::put('passport_info', Encryption::encode(json_encode($passport_info)));
            }
            return \redirect('signup/registration');
        } catch (\Exception $e) {
            Log::error('SignUp: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [SIGN-UP-209]');
            Session::flash('error', $e->getMessage() . ' SIGN-UP-209');
            return \redirect()->back();
        }
    }

    /**
     * @param $verification_id
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector\
     */
    public function identityVerifyConfirmWithPreviousData($verification_id)
    {
        try {
            $decoded_verification_id = Encryption::decodeId($verification_id);
            $user_verification_data = UserVerificationData::find($decoded_verification_id);
            if (empty($user_verification_data)) {
                Session::flash('error', 'Your previous verification data not found. Please try to sign up with the new verification.');
                return \redirect()->back();
            }

            Session::put('nationality_type', $user_verification_data->nationality_type);
            Session::put('identity_type', $user_verification_data->identity_type);
            if ($user_verification_data->identity_type == 'tin') {
                Session::put('eTin_info', $user_verification_data->eTin_info);
            } elseif ($user_verification_data->identity_type == 'nid') {
                Session::put('nid_info', $user_verification_data->nid_info);
            } elseif ($user_verification_data->identity_type == 'passport') {
                Session::put('passport_info', $user_verification_data->passport_info);
            }
            return \redirect('signup/registration');
        } catch (\Exception $e) {
            Log::error('SignUp: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [SIGN-UP-214]');
            Session::flash('error', 'Sorry! something went wrong, Please try again. SIGN-UP-214');
            return \redirect()->back();
        }
    }

    /**
     * Registration form
     * @param UserVerificationDataController $userVerificationDataController
     * @return \BladeView|bool|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function OSSsignupForm(UserVerificationDataController $userVerificationDataController)
    {
        if (!Session::has('oauth_token') or !Session::has('oauth_data')) {
            Session::flash('error', 'You have no access right! This incidence will be reported. Contact with system admin for more information.');
            return redirect()->to('/login');
        }

        try {
            // Store verification data of users for further usage
            $userVerificationDataController->storeUserVerificationData(session('oauth_data')->user_email);

            $nationality_type = Session::get('nationality_type');
            $company_infos = ['' => 'Select One'] + CompanyInfo::where('is_approved', 1)->where('is_rejected', 'no')->where('company_status', 1)->orderBy('company_name', 'ASC')->lists('company_name', 'id')->all();

            $identity_type = Session::get('identity_type');

            // Set suggested address for NID info
            $suggested_address = [
                'division_id' => '',
                'district_id' => '',
                'police_station_id' => '',
                'post_office' => '',
                'post_code' => '',
                'city' => '',
                'village_ward' => '',
            ];


            if ($identity_type === 'nid') {
                $nid_data = json_decode(Encryption::decode(Session::get('nid_info')), true);

                // Get division ID from area_info table
                if (!empty($nid_data['permanentAddress']['division'])) {
                    $division_id = AreaInfo::where([
                        'area_nm_ban' => trim($nid_data['permanentAddress']['division']),
                        'area_type' => 1 // 1 For Division
                    ])->pluck('area_id');
                    $suggested_address['division_id'] = $division_id;
                }

                // Get district ID from area_info table
                if (!empty($nid_data['permanentAddress']['district'])) {
                    $district_id = AreaInfo::where([
                        'area_nm_ban' => trim($nid_data['permanentAddress']['district']),
                        'area_type' => 2 // 2 for District
                    ])->pluck('area_id');
                    $suggested_address['district_id'] = $district_id;
                }

                // Get Police station ID from area_info table
                if (!empty($nid_data['permanentAddress']['upozila'])) {
                    $upozila_id = AreaInfo::where([
                        'area_nm_ban' => trim($nid_data['permanentAddress']['upozila']),
                        'area_type' => 3 // 3 for Sub-district
                    ])->pluck('area_id');
                    $suggested_address['police_station_id'] = $upozila_id;
                }

                //$suggested_address['post_office'] = $nid_data['return']['voterInfo']['voterInfo']['permanentAddress']['postOffice'];
                if (isset($nid_data['permanentAddress']['postalCode'])) {
                    $suggested_address['post_code'] = CommonFunction::convert2English($nid_data['permanentAddress']['postalCode']);
                }

                /* nid old (zaman vai)
                // Get division ID from area_info table
                if (!empty($nid_data['return']['voterInfo']['voterInfo']['permanentAddress']['division'])) {
                    $division_id = AreaInfo::where([
                        'area_nm_ban' => trim($nid_data['return']['voterInfo']['voterInfo']['permanentAddress']['division']),
                        'area_type' => 1 // 1 For Division
                    ])->pluck('area_id');
                    $suggested_address['division_id'] = $division_id;
                }

                // Get district ID from area_info table
                if (!empty($nid_data['return']['voterInfo']['voterInfo']['permanentAddress']['district'])) {
                    $district_id = AreaInfo::where([
                        'area_nm_ban' => trim($nid_data['return']['voterInfo']['voterInfo']['permanentAddress']['district']),
                        'area_type' => 2 // 2 for District
                    ])->pluck('area_id');
                    $suggested_address['district_id'] = $district_id;
                }

                // Get Police station ID from area_info table
                if (!empty($nid_data['return']['voterInfo']['voterInfo']['permanentAddress']['upozila'])) {
                    $upozila_id = AreaInfo::where([
                        'area_nm_ban' => trim($nid_data['return']['voterInfo']['voterInfo']['permanentAddress']['upozila']),
                        'area_type' => 3 // 3 for Sub-district
                    ])->pluck('area_id');
                    $suggested_address['police_station_id'] = $upozila_id;
                }

                //$suggested_address['post_office'] = $nid_data['return']['voterInfo']['voterInfo']['permanentAddress']['postOffice'];
                if (isset($nid_data['return']['voterInfo']['voterInfo']['permanentAddress']['postalCode'])) {
                    $suggested_address['post_code'] = CommonFunction::convert2English($nid_data['return']['voterInfo']['voterInfo']['permanentAddress']['postalCode']);
                }
                */



            } elseif ($identity_type === 'tin') {
                $tin_info = json_decode(Encryption::decode(Session::get('eTin_info')), true);

                $suggested_address['village_ward'] = $tin_info['address']['present']['addr'] . $tin_info['address']['present']['addr1'];
                $suggested_address['city'] = $tin_info['address']['present']['city'];

                // Get district and division ID from area_info table
                if (!empty($tin_info['address']['present']['distName'])) {
                    $district_info = AreaInfo::where([
                        'area_nm' => trim($tin_info['address']['present']['distName']),
                        'area_type' => 2
                    ])->first(['area_id', 'pare_id']);

                    if (isset($district_info->area_id)) {
                        $suggested_address['district_id'] = $district_info->area_id;

                    }
                    if (isset($district_info->pare_id)) {
                        $suggested_address['division_id'] = AreaInfo::where([
                            'area_id' => $district_info->pare_id
                        ])->pluck('area_id');
                    }
                }

                // Get Police Station ID from area_info table
                if (!empty($tin_info['address']['present']['thanaName'])) {
                    $suggested_address['police_station_id'] = AreaInfo::where([
                        'area_nm' => trim($tin_info['address']['present']['thanaName']),
                        'area_type' => 3
                    ])->pluck('area_id');
                }

                $suggested_address['post_code'] = $tin_info['address']['present']['postCode'];
            }
            // End Set suggested address for NID info

            if ($nationality_type === 'bangladeshi') {
                $countries = Countries::orderby('name')->where('id', 18)->lists('nicename', 'id');
                $nationalities = Countries::orderby('nationality')
                    ->where('id', 18)
                    ->where('nationality', '!=', '')
                    ->lists('nationality', 'id');

                $divisions = ['' => 'Select One'] + AreaInfo::where('area_type', 1)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
                $districts = ['' => 'Select One'] + AreaInfo::where('area_type', 2)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
                $thana = AreaInfo::orderby('area_nm')->where('area_type', 3)->lists('area_nm', 'area_id');
            } else {
                $countries = Countries::orderby('name')->where('id', '!=', 18)->lists('nicename', 'id');
                $nationalities = Countries::orderby('nationality')
                    ->where('id', '!=', 18)
                    ->where('nationality', '!=', '')
                    ->lists('nationality', 'id');
            }

            return view('Signup::registration', compact('company_infos', 'divisions', 'districts', 'thana', 'nationalities', 'countries', 'suggested_address'));
        } catch (\Exception $e) {
            Log::error('SignUp: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [SIGN-UP-210]');
            Session::flash('error', $e->getMessage() . ' SIGN-UP-210');
            return \redirect()->back();
        }
    }

    public function getPassportData(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.';
        }

        $base64_split = explode(',', substr($request->get('file'), 5), 2);

        // This API need passport base64 data
        $url = "https://api-k8s.oss.net.bd/api/passport-service/passport";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $base64_split[1]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));

        $result = curl_exec($ch);

        if (!curl_errno($ch)) {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        } else {
            $http_code = 0;
        }

        curl_close($ch);

        $response = \GuzzleHttp\json_decode($result);

        $returnData = [
            'success' => true,
            'code' => '',
            'msg' => '',
            'data' => []
        ];

        if (isset($response->code) && $response->code == '200') {
            $returnData['data'] = $response->data;
            $returnData['code'] = '200';
            $returnData['nationality_id'] = Countries::Where('iso3', 'like', '%' . $response->data->country . '%')->pluck('id');
        } else if (isset($response->code) && in_array($response->code, ['400', '401', '405'])) {
            $returnData['msg'] = $response->msg;
            $returnData['code'] = $response->code;
        }

        // uncomment the below line for python API
        //unlink($file_temp_path);
        return response()->json($returnData);
    }

    /**
     * New user store
     * @param Request $request
     * @return mixed
     */
    public function OSSsignupStore(Request $request)
    {
        $rules = [];
        $messages = [];

        $rules['nationality_type'] = 'required';
        $rules['identity_type'] = 'required';

        $rules['designation'] = 'required';

        $rules['company_type'] = 'required|in:1,2';
        $rules['company_id'] = 'required_if:company_type,1';
        $rules['company_name_en'] = 'required_if:company_type,2';

        // Business category, 1 = private, 2 = govt
        $rules['business_category'] = 'required_if:company_type,2';

        if ($request->get('business_category') == 1) {
            $rules['country'] = 'required|integer';
            $rules['nationality'] = 'required|integer';

            $rules['division_id'] = 'required_if:nationality,18|integer';
            $rules['district_id'] = 'required_if:nationality,18|integer';
            $rules['thana_id'] = 'required_if:nationality,18|integer';
            $rules['post_office'] = 'required_if:nationality,18';
            $rules['post_code'] = 'required_if:nationality,18|digits:4';
            $rules['road_no'] = 'required_if:nationality,18';

            $rules['state'] = 'required_unless:nationality,18';
            $rules['province'] = 'required_unless:nationality,18';
            $rules['post_code_abroad'] = 'required_unless:nationality,18';
            $rules['road_no_abroad'] = 'required_unless:nationality,18';

            $rules['user_phone'] = 'required|phone_or_mobile';
            $rules['investor_photo_base64'] = 'required';

            // custom messages
            $messages['division_id.required_if'] = 'The division field is required when nationality is bangladeshi.';
            $messages['district_id.required_if'] = 'The district field is required when nationality is bangladeshi.';
            $messages['thana_id.required_if'] = 'The thana field is required when nationality is bangladeshi.';
            $messages['post_office.required_if'] = 'The post field is required when nationality is bangladeshi.';
            $messages['post_code.required_if'] = 'The post code (number) field is required when nationality is bangladeshi.';
            $messages['road_no.required_if'] = 'The address field is required when nationality is bangladeshi.';

            $messages['state.required_unless'] = 'The state field is required when nationality is not bangladeshi.';
            $messages['province.required_unless'] = 'The province/city field is required when nationality is not bangladeshi.';
            $messages['post_code_abroad.required_unless'] = 'The post code field is required when nationality is not bangladeshi.';
            $messages['road_no_abroad.required_unless'] = 'The address field is required when nationality is not bangladeshi.';

        }



        $rules['authorization_file'] = 'required|mimes:pdf|max:3072';
        $rules['g-recaptcha-response'] = 'required';

        // custom messages
        $messages['company_name_en.required_if'] = 'The organization name (en) field is required when organization type is new.';
        $messages['business_category.required_if'] = 'The business category field is required when organization type is new.';

        $this->validate($request, $rules, $messages);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();
            $identity_type = Encryption::decode($request->get('identity_type'));
            $user_email = Session::get('oauth_data')->user_email;
            $user = Users::firstOrNew(['user_email' => $user_email]);
            $nationality_type = Encryption::decode($request->get('nationality_type'));
            $user->nationality_type = $nationality_type;
            $user->identity_type = $identity_type;

            if ($identity_type === 'nid') {
                $nid_info = json_decode(Encryption::decode(Session::get('nid_info')), true);

                $user_nid = $nid_info['nationalId'];
                $user_name_en = $nid_info['nameEn'];
                $user_DOB = $nid_info['dateOfBirth'];

                $user->user_nid = $user_nid;

            } elseif ($identity_type === 'tin') {
                $eTin_info = json_decode(Encryption::decode(Session::get('eTin_info')), true);

                $user_name_en = $eTin_info['assesName'];
                $user_DOB = $eTin_info['dob'];
                //$father_name = $eTin_info['fathersName'];
                $user->user_tin = $eTin_info['etin_number'];
            } elseif ($identity_type === 'passport') {
                $passport_info = json_decode(Encryption::decode(Session::get('passport_info')), true);
                $user_name_en = $passport_info['passport_given_name'] . ' ' . $passport_info['passport_surname'];

                $user->passport_nationality_id = $passport_info['passport_nationality'];
                $user->passport_type = $passport_info['passport_type'];
                $user->passport_no = $passport_info['passport_no'];
                $user->passport_surname = $passport_info['passport_surname'];
                $user->passport_given_name = $passport_info['passport_given_name'];
                $user->passport_personal_no = $passport_info['passport_personal_no'];
                $user->passport_DOB = CommonFunction::changeDateFormat($passport_info['passport_DOB'], true);
                $user->passport_date_of_expire = CommonFunction::changeDateFormat($passport_info['passport_date_of_expire'], true);
                $user->passport_copy = $passport_info['passport_copy'];

                $user_DOB = $passport_info['passport_DOB'];
            }

            $user->user_first_name = $user_name_en;
            $user->user_middle_name = '';
            $user->user_last_name = '';
            if (!empty(Session::get('oauth_data')->gender) && in_array(Session::get('oauth_data')->gender, ['male', 'female', 'other'])) {
                $user->user_gender = ucfirst(Session::get('oauth_data')->gender);
            } else {
                $user->user_gender = 'Not defined';
            }

            if (!empty($user_DOB)) {
                $user->user_DOB = CommonFunction::changeDateFormat(date('d-M-Y', strtotime($user_DOB)), true);
            }

            $user->designation = $request->get('designation');

            $user->user_type = '5x505';

            if ($request->get('business_category') == 1) { // 1 for private
                $user->nationality_id = $request->get('nationality');
                $user->country_id = $request->get('country');

                if ($request->get('nationality') == '18') {
                    $user->division = $request->get('division_id');
                    $user->district = $request->get('district_id');
                    $user->thana = $request->get('thana_id');
                    $user->post_office = $request->get('post_office');
                    $user->post_code = $request->get('post_code');
                    $user->road_no = $request->get('road_no');
                } else {
                    $user->state = $request->get('state');
                    $user->province = $request->get('province');
                    $user->post_code = $request->get('post_code_abroad');
                    $user->road_no = $request->get('road_no_abroad');
                }
                $user->user_phone = $request->get('user_phone');
            }

            $user->user_agreement = 1;
            $user->user_verification = 'yes';
            $user->first_login = 0;
            $user->social_login = 0;
            $user->user_status = 'inactive';

            if ($request->get('company_type') == 1) { // existing Company
                $user->company_ids = $request->get('company_id');
                $user->working_company_id = $request->get('company_id');
            } elseif ($request->get('company_type') == 2) {
                $companyData = CompanyInfo::where('company_name', $request->get('company_name_en'))
                    ->orWhere(function ($query) use ($request) {
                        $query->where('company_name_bn', $request->get('company_name_bn'))
                            ->where('company_name_bn', '!=', '')
                            ->whereNotNull('company_name_bn');
                    })
                    ->first();
                if ($companyData) {
                    if ($companyData->is_rejected == 'no') {
                        DB::rollback();
                        Session::flash('error', 'Your company name is duplicate! Please give a unique name.');
                        return Redirect::back()->withInput();
                    } else {
                        $companyData->company_name = trim($request->get('company_name_en'));
                        $companyData->company_name_bn = trim($request->get('company_name_bn'));
                        $companyData->business_category = $request->get('business_category');
                        $companyData->created_by = '';
                        $companyData->created_at = Carbon::now();
                        $companyData->is_rejected = 'no'; // again reset the rejected status and is_approved
                        $companyData->is_approved = 1; // when user is auto approved then 1
                        $companyData->company_status = 1; // company auto approved 1
                        $companyData->save();
                        $companyId = $companyData->id;
                    }
                } else {
                    $companyId = DB::table('company_info')->insertGetId([
                        'company_name' => trim($request->get('company_name_en')),
                        'company_name_bn' => trim($request->get('company_name_bn')),
                        'business_category' => trim($request->get('business_category')),
                        'is_approved' => 1, // 1 = auto approved
                        'company_status' => 1, // 1 = auto approved
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => ''
                    ]);
                }
                $user->company_ids = $companyId;
                $user->working_company_id = $companyId;
                $user->is_approved = 1; // 1 = auto approved
                $user->user_status = 'active';
            }

            // Profile image store
            if ($request->business_category == '1') { // 1 for private
                if (!empty($request->get('investor_photo_base64'))) {
                    $split_user_pic = explode(',', substr($request->get('investor_photo_base64'), 5), 2);
                    $base64_image = $split_user_pic[1];
                    $base64ResizeImage = base64_encode(ImageProcessing::resizeBase64Image($base64_image, 150, 150));
                    $base64ResizeImage = base64_decode($base64ResizeImage);
                    $path = 'users/upload/';
                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }
                    $user_pic_name = trim(uniqid('BIDA_PP_CID-' . $user->company_ids . '_', true) . '.' . 'jpeg');
                    file_put_contents($path . $user_pic_name, $base64ResizeImage);
                    $user->user_pic = $user_pic_name;
                }
            }
            // End Profile image store

            $user->save();

            // Store company association request
            $company_association = new CompanyAssociation();
            if ($request->get('company_type') == 1) {
                $existing_company_info = CompanyInfo::where('id', $user->company_ids)->first();
                $company_association->company_type = 'existing';
                $company_association->business_category = $existing_company_info->business_category;
                $company_association->company_name_en = $existing_company_info->company_name;
                $company_association->company_name_bn = $existing_company_info->company_name_bn;
            } else {
                $company_association->company_type = 'new';
                $company_association->business_category = $request->get('business_category');
                $company_association->company_name_en = trim($request->get('company_name_en'));
                $company_association->company_name_bn = trim($request->get('company_name_bn'));
            }

            if ($request->hasFile('authorization_file')) {
                $yearMonth = date("Y") . "/" . date("m") . "/";
                $path = 'users/upload/' . $yearMonth;
                $_authorization_file = $request->file('authorization_file');
                $full_name_concat = trim($user->user_first_name . ' ' . $user->user_middle_name . ' ' . $user->user_last_name);
                $full_name = str_replace(' ', '_', $full_name_concat);
                $authorization_file = trim(uniqid('BIDA_AL_CID-' . $user->company_ids . '_' . $full_name . '_', true) . '.' . $_authorization_file->getClientOriginalExtension());
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $_authorization_file->move($path, $authorization_file);
                $authorization_file_path = $yearMonth . $authorization_file;
            }

            $company_association->user_id = $user->id;
            $company_association->authorization_letter = $authorization_file_path;
            $company_association->current_company_ids = 0;
            $company_association->requested_company_id = $user->company_ids;
            $company_association->approved_user_type = 'Employee';
            $company_association->request_type = 'Add';
            $company_association->user_remarks = 'Request from signup';
            $company_association->application_date = date('Y-m-d H:i:s');
            $company_association->status_id = 25;
            $company_association->status = 1;
            $company_association->save();
            // End Store company association request

            // User login
            Auth::loginUsingId($user->id);
            CommonFunction::GlobalSettings();
            $this->entryAccessLog();
            $user->login_token = Encryption::encode(Session::getId());
            $user->save();


            // Update user verification data
            $UserVerificationDataController = new UserVerificationDataController();
            $UserVerificationDataController->updateNidTinPassportUserVerificationData($user_email);


            DB::commit();

            // Forget session data
            Session::forget('nationality_type');
            Session::forget('identity_type');
            Session::forget('nid_info');
            Session::forget('eTin_info');
            Session::forget('passport_info');
            Session::forget('oauth_token');
            Session::forget('oauth_data');

            if ($request->get('company_type') == 1) {
                Session::flash('success', 'Successfully Registered! You can login to system.');
                return Redirect::to('/dashboard');
            }
            Session::flash('success', 'You have successfully completed sign-up. Please provide <b>Basic information</b> at first to get all the services provided in this portal.');
            return Redirect::to('/dashboard');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('SignUp: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [SIGN-UP-211]');
            Session::flash('error', 'Something went wrong [SIGN-UP-211]');
            return Redirect::back()->withInput();
        }
    }

    public function entryAccessLog()
    {
        // access_log table.
        $str_random = str_random(10);
        $insert_id = DB::table('user_logs')->insertGetId(
            array(
                'user_id' => Auth::user()->id,
                'login_dt' => date('Y-m-d H:i:s'),
                'ip_address' => \Request::getClientIp(),
                'access_log_id' => $str_random
            )
        );

        Session::put('access_log_id', $str_random);
    }

    public function otpServiceCallback(Request $request)
    {
        
        $otpService = new OtpService();


        $base64DecodeData = base64_decode($request->get('oauthData'));

        $jsonDecodedData = json_decode($base64DecodeData);


       // Log::info('otpServiceCallback: ' . $base64DecodeData);



//dd($jsonDecodedData);
        $otpResponse = $otpService->verifySecretKey($jsonDecodedData);
       // dd($otpResponse, 1);
        if ($otpResponse->status) {

            $userVerificationOtp = new UserVerificationOtp();
            $userVerificationOtp->user_email = Session::get('oauth_data')->user_email;
            $userVerificationOtp->user_mobile = Session::get('oauth_data')->mobile;
            $userVerificationOtp->otp_status = 2;
            $userVerificationOtp->save();


            $clientData = new stdClass();
            $clientData->client_id = config('app.NID_JWT_ID');
            $clientData->client_secret_key = config('app.NID_JWT_SECRET_KEY');
            $clientData->encryption_key = config('app.NID_JWT_ENCRYPTION_KEY');

            //dd(Session::get('oauth_data'));

            // JNID verification JWT token generation
            $tokenService = new nidTokenServiceJWT();
            $jwtTokenArray = $tokenService->generateNIDToken($clientData);




            // NID verification JWT token store
            $tokenService->storeNIDToken($jwtTokenArray);
            return redirect()->to('signup/identity-verify');

        } else {
            $errorMessage = !empty($otpResponse->errorMessage) ? $otpResponse->errorMessage : 'Failed to verify otp.Please try again';
            Session::put("exception", $errorMessage);
            return redirect('/login-message');
        }
    }

}