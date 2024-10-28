<?php

namespace App\Libraries;

use App\ActionInformation;
use App\AuditLog;
use App\HomePageViewCount;
use App\HomePageViewLog;
use App\Modules\SecurityClearance\Models\SecurityClearance;
use App\Modules\Apps\Models\Department;
use App\Modules\Apps\Models\EmailQueue;
use App\Modules\Apps\Models\Templates;
use App\Modules\BasicInformation\Models\BasicInformation;
use App\Modules\BidaRegistration\Models\BidaRegistration;
use App\Modules\BoardMeting\Models\Agenda;
use App\Modules\BoardMeting\Models\BoardMeting;
use App\Modules\BoardMeting\Models\Committee;
use App\Modules\BoardMeting\Models\ProcessListBMRemarks;
use App\Modules\BoardMeting\Models\ProcessListBoardMeting;
use App\Modules\CompanyAssociation\Models\CompanyAssociation;
use App\Modules\Files\Controllers\FilesController;
use App\Modules\IrcRecommendationNew\Models\IrcInspection;
use App\Modules\NewReg\Models\NewReg;
use App\Modules\NewReg\Models\RjscNrEntityType;
use App\Modules\NewReg\Models\RjscNrSubmitForms;
use App\Modules\NewRegForeign\Models\NewRegForeign;
use App\Modules\NewRegForeign\Models\RjscNrfSubmitForms;
use App\Modules\OfficePermissionNew\Models\OPOfficeType;
use App\Modules\ProcessPath\Models\DeptApplicationTypes;
use App\Modules\ProcessPath\Models\ProcessFavoriteList;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Models\ProcessStatus;
use App\Modules\ProcessPath\Models\ProcessType;
use App\Modules\ProcessPath\Models\UserDesk;
use App\Modules\Remittance\Models\Remittance;
use App\Modules\SecurityClearance\Controllers\SecurityClearanceController;
use App\Modules\Settings\Models\Configuration;
use App\Modules\Settings\Models\Logo;
use App\Modules\Settings\Models\RegulatoryAgency;
use App\Modules\Settings\Models\RegulatoryAgencyDetails;
use App\Modules\SonaliPayment\Models\PaymentConfiguration;
use App\Modules\SonaliPayment\Models\PaymentDistribution;
use App\Modules\SonaliPayment\Models\PayMode;
use App\Modules\Users\Models\AreaInfo;
use App\Modules\Users\Models\CompanyInfo;
use App\Modules\Users\Models\Users;
use App\User;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Session;
use App\Libraries\CommonFunctionStakeholder;

class CommonFunction
{

    /**
     * @param Carbon|string $updated_at
     * @param string $updated_by
     * @return string
     * @internal param $Users->id /string $updated_by
     */
    public static function showAuditLog($updated_at = '', $updated_by = '')
    {
        $update_was = 'Unknown';
        if ($updated_at && $updated_at > '0') {
            $update_was = Carbon::createFromFormat('Y-m-d H:i:s', $updated_at)->diffForHumans();
        }

        $user_name = 'Unknown';
        if ($updated_by) {
            $name = User::where('id', $updated_by)->first();
            if ($name) {
                $user_name = $name->user_first_name . ' ' . $name->user_middle_name . ' ' . $name->user_last_name;
            }
        }
        return '<span class="help-block">Last updated : <i>' . $update_was . '</i> by <b>' . $user_name . '</b></span>';
    }

    public static function showErrorPublic($param, $msg = 'Sorry! Something went wrong! ')
    {
        $j = strpos($param, '(SQL:');
        if ($j > 15) {
            $param = substr($param, 8, $j - 9);
        } else {
            //
        }
        return $msg . $param;
    }

    public static function statuswiseAppInDesks($process_type_id)
    {
        $appList = ProcessList::getStatusWiseApplication($process_type_id);

        $appsStatus = ProcessStatus::where('process_type_id', $process_type_id)
            ->whereNotIn('id', [-1, 3])
            ->where('process_type_id', '!=', 0)
            ->orderBy('id')
            ->get();

        $statusArr = [];
        foreach ($appsStatus as $status) {
            $statusArr[$status->id] = [
                'process_type_id' => $process_type_id,
                'status_id' => $status->id,
                'status_name' => $status->status_name,
                'no_of_application' => 0,
                'color' => $status->color,
            ];
        }

        foreach ($appList as $app) {
            $statusArr[$app->status_id]['no_of_application'] = $statusArr[$app->status_id]['no_of_application'] + 1;
        }

        return $statusArr;

    }

//    public static function statuswiseSecurityClearanceApp($process_type_id)
//    {
////        $appList = SecurityClearanceController::securityClearanceApplist();
//
//        $securityDataList = SecurityClearance::all();
//
////        $appsStatus = DB::select(DB::raw('select * from security_clearance_status where process_type_id=2'));
//
//        $appsStatus = DB::table('security_clearance_status')
//            ->select('*')
//            ->where('process_type_id', 2)
//            ->where('status', 1)
//            ->get();
//
//        foreach ($appsStatus as $status) {
//            $statusArr[$status->id] = [
//                'process_type_id' => $process_type_id,
//                'status_id' => $status->id,
//                'status_name' => $status->status_name,
//                'no_of_application' => 0,
//                'color' => $status->color,
//            ];
//        }
//
//
//        $waitingForSubmissionIds = SecurityClearance::lists('ref_id')->toArray();
//
//        $processList = ProcessList::whereNotIn('ref_id', $waitingForSubmissionIds)->where('process_type_id', 2)->where('status_id', '=', 25)->count();
//
//        foreach ($securityDataList as $key => $securityData) {
//
//            $statusArr[$securityData->status]['no_of_application'] = $statusArr[$securityData->status]['no_of_application'] + 1;
//        }
//
////        $statusArr[2]['no_of_application'] = $processList;
//
////        $waiting_app['waiting_for_submission'] = $processList;
////        $waiting_app['status_of_app'] = $statusArr;
//        return $statusArr;
//
//    }

    public static function showCreateLog($created_at = '', $created_by = '', $msg = 'Created')
    {
        try {
            $update_was = 'Unknown';
            if ($created_at && $created_at > '0') {
                $update_was = Carbon::createFromFormat('Y-m-d H:i:s', $created_at)->diffForHumans();
            }

            $user_name = 'Unknown';
            if ($created_by) {
                $name = User::where('id', $created_by)->first();
                if ($name) {
                    $user_name = $name->user_first_name . ' ' . $user_name->user_middle_name . ' ' . $user_name->user_last_name;
                }
            }
            return '<span class="help-block"> ' . $msg . ' at : <i>' . $update_was . '</i> by <b>' . $user_name . '</b></span>';
        } catch (\Exception $e) {
            if (env('APP_DEBUG')) {
                dd($e);
            } else {
                return 'Some errors occurred (code:790)';
            }
        }
    }

    public static function createAuditLog($module, $request, $id = '')
    {
        $data = $request->all();

        if ($id) {
            $data['id'] = $id;
        }

        try {
            unset($data['_token']);
            unset($data['_method']);
            unset($data['selected_file']);
            unset($data['TOKEN_NO']);
        } catch (\Exception $e) {
            echo 'Something wrong for audit log '.CommonFunction::showErrorPublic($e->getMessage()). '[CM-1011]';
        }

        $details = json_encode($data);

        $inserData = [
            'remote_ip' => UtilFunction::getVisitorRealIP(),
            'module_or_log_key' => $module,
            'details' => $details,
            'is_helpful' => (!empty($request->is_helpful) ? $request->is_helpful : '')
        ];

        try {
            $sessionID = AuditLog::create($inserData);

        } catch (\Exception $e) {
            echo 'Something wrong for audit log '.CommonFunction::showErrorPublic($e->getMessage()). '[CM-1012]';
        }
    }

    public static function createHomePageViewLog($module_log_key, $request, $reference_id = '')
    {
        $data = $request->all();

        if ($reference_id) {
            $data['reference_id'] = $reference_id;
        }

        try {
            unset($data['_token']);
            unset($data['_method']);
            unset($data['selected_file']);
            unset($data['TOKEN_NO']);

            $home_page_log = new HomePageViewLog();
            $home_page_log->remote_ip = UtilFunction::getVisitorRealIP();
            $home_page_log->module_or_log_key = $module_log_key.(!empty($request->is_helpful) ? '.'.$request->is_helpful : '');
            $home_page_log->details = json_encode($data);;
            $home_page_log->save();

            $home_page_count = HomePageViewCount::firstOrNew(['module_or_log_key' => $module_log_key]);
            $home_page_count->reference_id = $reference_id;

            if (!empty($request->is_helpful) && $request->is_helpful == 'yes') {
                $home_page_count->yes_count += 1;
            } elseif (!empty($request->is_helpful) && $request->is_helpful == 'no') {
                $home_page_count->no_count += 1;
            } else {
                $home_page_count->view_count +=  1;
            }

            $home_page_count->save();

        } catch (\Exception $e) {
            echo 'Something wrong for audit log '.CommonFunction::showErrorPublic($e->getMessage()). '[CM-1013]';
        }
    }

    public static function updatedOn($updated_at = '')
    {
        $update_was = '';
        if ($updated_at && $updated_at > '0') {
            $update_was = Carbon::createFromFormat('Y-m-d H:i:s', $updated_at)->diffForHumans();
        }
        return $update_was;
    }

    public static function updatedBy($updated_by = '')
    {
        $user_name = 'Unknown';
        if ($updated_by) {
            $name = User::where('id', $updated_by)->first(['user_first_name', 'user_middle_name', 'user_last_name']);
            if ($name) {
                $user_name = $name->user_first_name . ' ' . $name->user_middle_name . ' ' . $name->user_last_name;
            }
        }
        return $user_name;
    }

    public static function GlobalSettings()
    {
        $logoInfo = Logo::orderBy('id', 'DESC')->first();
        if ($logoInfo != "") {
            Session::set('logo', $logoInfo->logo);
            Session::set('title', $logoInfo->title);
            Session::set('manage_by', $logoInfo->manage_by);
            Session::set('help_link', $logoInfo->help_link);
        } else {
            Session::set('logo', 'assets/images/company_logo.png');
        }
        //return $logoInfo;
    }

    public static function getUserTypeWithZero()
    {

        if (Auth::user()) {
            return Auth::user()->user_type;
        } else {
            return 0;
        }
    }

    public static function getUserSubTypeWithZero()
    {

        if (Auth::user()) {
            return Auth::user()->user_sub_type;
        } else {
            return 0;
        }
    }

    public static function getUserCompanyByUserId($userId)
    {
        $user = Users::find($userId);
        if ($user) {
            return explode(',', $user->company_ids);
        } else {
            return [0];
        }
    }

    public static function getUserCompanyWithZero()
    {
        if (Auth::user()) {
            return explode(',', Auth::user()->company_ids);
        } else {
            return [0];
        }
    }

    public static function getUserCompanyAllWithZero()
    {
        if (Auth::user()) {
            $company_ids_all = Users::find(Auth::user()->id)->pluck('company_ids');
            return explode(',', $company_ids_all);
        } else {
            return [0];
        }
    }

    public static function getUserCompanyAllWithZeroWithoutEloquent()
    {
        if (Auth::user()) {
            $company_ids_all = DB::table('users')->where('id', Auth::user()->id)->pluck('company_ids');
            return explode(',', $company_ids_all);
        } else {
            return [0];
        }
    }

    public static function getDeskId()
    {
        if (Auth::user()) {
            return Auth::user()->desk_id;
        } else {
            CommonFunction::redirectToLogin();
        }
    }

    public static function redirectToLogin()
    {
        echo "<script>location.replace('users/login');</script>";
    }

    public static function formateDate($date = '')
    {
        if (!empty($date)) {
            return date('d M, Y', strtotime($date));
        } else {
            return null;
        }
    }

    public static function getUserStatus()
    {

        if (Auth::user()) {
            return Auth::user()->user_status;
        } else {
            // return 1;
            dd('Invalid User status');
        }
    }

    public static function convertUTF8($string)
    {
//        $string = 'u0986u09a8u09c7u09beu09dfu09beu09b0 u09b9u09c7u09beu09b8u09beu0987u09a8';
        $string = preg_replace('/u([0-9a-fA-F]+)/', '&#x$1;', $string);
        return html_entity_decode($string, ENT_COMPAT, 'UTF-8');
    }

    public static function showDate($updated_at = '')
    {
        $update_was = '';
        if (!empty($updated_at)) {
            $update_was = Carbon::createFromFormat('Y-m-d H:i:s', $updated_at)->diffForHumans();
        }
        return '<span class="help-block"><i>' . $update_was . '</i></span>';
    }

    public static function checkUpdate($model, $id, $updated_at)
    {
        if ($model::where('updated_at', $updated_at)->find($id)) {
            return true;
        } else {
            return false;
        }
    }

    /* This function determines if an user is an admin or sub-admin
     * Based On User Type
     **/
    public static function isAdmin()
    {
        $user_type = Auth::user()->user_type;
        /*
         * 1x101 for System Admin
         * 14x141 for Programmer
         * 2x202 for IT Help Desk
         */
        if ($user_type == '1x101' or $user_type == '14x141' or $user_type == '2x202') {
            return true;
        } else {
            return false;
        }
    }

    public static function changeDateFormat($datePicker, $mysql = false, $with_time = false)
    {
        if (empty($datePicker)) {
            return null;
        }

        try {
            if ($mysql) {
                if ($with_time) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $datePicker)->format('d-M-Y');
                } else {
                    return Carbon::createFromFormat('d-M-Y', $datePicker)->format('Y-m-d');
                }
            } else {
                return Carbon::createFromFormat('Y-m-d', $datePicker)->format('d-M-Y');
            }
        } catch (\Exception $e) {
            if (env('APP_DEBUG')) {
                dd($e);
            } else {
                return $datePicker; //'Some errors occurred (code:793)';
            }
        }
    }

    public static function validateMobileNumber($mobile_no)
    {
        $mobile_validation_err = '';
        $first_digit = substr($mobile_no, 0, 1);
        $first_two_digit = substr($mobile_no, 0, 2);
        $first_four_digit = substr($mobile_no, 0, 4); // without '+'
        $first_five_digit = substr($mobile_no, 0, 5); // with '+'
        // if first two digit is 01
        if (strlen($mobile_no) < 11) {
            $mobile_validation_err = 'Mobile number should be minimum 11 digit';
        } elseif ($first_two_digit == '01') {
            if (strlen($mobile_no) != 11) {
                $mobile_validation_err = 'Mobile number should be 11 digit';
            }
        } // if first four digit is '8801'
        else if ($first_four_digit == '8801') {
            if (strlen($mobile_no) != 13) {
                $mobile_validation_err = 'Mobile number should be 14 digit';
            }
        }// if first five digit is '+8801'
        else if ($first_five_digit == '+8801') {
            if (strlen($mobile_no) != 14) {
                $mobile_validation_err = 'Mobile number should be 14 digit';
            }
        } // if first digit is only
        else if ($first_digit == '+') {
            // Mobile number will be ok
        } else {
            $mobile_validation_err = 'Please enter valid Mobile number';
        }

        if (strlen($mobile_validation_err) > 0) {
            return $mobile_validation_err;
        } else {
            return 'ok';
        }
    }

    public static function age($birthDate)
    {
        $year = '';
        if ($birthDate) {
            $year = Carbon::createFromFormat('Y-m-d', $birthDate)->diff(Carbon::now())->format('%y years, %m months and %d days');
        }
        return $year;
    }

    public static function getFieldName($id, $field, $search, $table)
    {

        if ($id == NULL || $id == '') {
            return '';
        } else {
            return DB::table($table)->where($field, $id)->pluck($search);
        }
    }

    public static function getUserFullnameById($user_id)
    {
        $userById = Users::Where('id', $user_id)->first([
            'user_first_name',
            'user_middle_name',
            'user_last_name'
        ]);
        if (!empty($userById)) {
            return $userById->user_first_name . ' ' . $userById->user_middle_name . ' ' . $userById->user_last_name;
        } else {
            return "Invalid user Id";
        }


    }

    public static function getUserFullName()
    {
        if (Auth::user()) {
            return Auth::user()->user_first_name . ' ' . Auth::user()->user_middle_name . ' ' . Auth::user()->user_last_name;
        } else {
            return 'Invalid Login Id';
        }
    }

    public static function getUserType()
    {
        if (Auth::user()) {
            return Auth::user()->user_type;
        } else {
            // return 1;
            dd('Invalid User Type');
        }
    }

    public static function getUserId()
    {
        if (Auth::user()) {
            return Auth::user()->id;
        } else {
            return 'Invalid Login Id';
        }
    }

    public static function getUserDeskIds()
    {

        if (Auth::user()) {
            $deskIds = Auth::user()->desk_id;
            $userDeskIds = explode(',', $deskIds);
            return $userDeskIds;
        } else {
            // return 1;
            dd('Invalid User status');
        }
    }


    public static function getUserDivisionIdsWithZero()
    {

        if (Auth::user()) {
            $devisionIds = Auth::user()->division_id;
            $userDivisionIds = explode(',', $devisionIds);
            return $userDivisionIds;
        } else {
            return [0];
        }
    }

    public static function getUserDeskList()
    {
        if (Auth::user()) {
            $deskIds = Auth::user()->desk_id;
            $userDeskIds = explode(',', $deskIds);
            $userDeskList = UserDesk::whereIn('id', $userDeskIds)
                ->lists('user_desk.desk_name', 'user_desk.id')
                ->all();
            if (count($userDeskList) > 0)
                return $userDeskList;
            else return [0 => 'None'];
        } else {
            return 0;
        }
    }

    public static function getUserDeskIdsWithzero()
    {
        if (Auth::user()) {
            $deskIds = Auth::user()->desk_id;
            $userDeskIds = explode(',', $deskIds);
            return $userDeskIds;
        } else {
            return [0];
        }
    }

    public static function getUserDepartmentIds()
    {
        if (Auth::user()) {
            $departmentIds = Auth::user()->department_id;
            $userDepartemntIds = explode(',', $departmentIds);
            return $userDepartemntIds;
        } else {
            return [0];
        }
    }

    public static function getUserSubDepartmentIds()
    {
        if (Auth::user()) {
            $subDepartmentIds = Auth::user()->sub_department_id;
            $userSubDepartemntIds = explode(',', $subDepartmentIds);
            return $userSubDepartemntIds;
        } else {
            return [0];
        }
    }

    public static function getDelegatedUserDeskDepartmentIds()
    {

        $userId = CommonFunction::getUserId();
        $delegated_usersArr = Users::where('delegate_to_user_id', $userId)
            ->get([
                'id as user_id',
                'desk_id',
                'division_id',
                'department_id',
                'sub_department_id'
            ]);
        $delegatedDeskDepartmentIds = array();
        foreach ($delegated_usersArr as $value) {

            $userDesk = explode(',', $value->desk_id);
            $userDivision = explode(',', $value->division_id);
            $userDepartment = explode(',', $value->department_id);
            $userSubDepartment = explode(',', $value->sub_department_id);
            $tempArr = array();
            $tempArr['user_id'] = $value->user_id;
            $tempArr['desk_ids'] = $userDesk;
            $tempArr['division_ids'] = $userDivision;
            $tempArr['department_ids'] = $userDepartment;
            $tempArr['sub_department_ids'] = $userSubDepartment;
            $delegatedDeskDepartmentIds[$value->user_id] = $tempArr;
        }
        return $delegatedDeskDepartmentIds;
    }

    public static function getDelegationUser()
    {
        $userId = CommonFunction::getUserId();
        $delegated_usersArr = Users::where('delegate_to_user_id', $userId)
            ->get([
                'id as user_id',
                'desk_id',
                'department_id',
                'user_email',
                'sub_department_id'
            ]);
        return $delegated_usersArr;
    }

    public static function getDelegatedUserDeskIds()
    {
        $userId = CommonFunction::getUserId();
        $delegated_usersArr = Users::where('delegate_to_user_id', $userId)
            ->get([
                'id as user_id',
                'desk_id',
                'department_id'
            ]);
        $delegatedDeskIds = array();
        foreach ($delegated_usersArr as $value) {
            $userDesk = explode(',', $value->desk_id);
            $delegatedDeskIds = array_merge($delegatedDeskIds, $userDesk);
        }
        return $delegatedDeskIds;
    }

    public static function getSelfAndDelegatedUserDeskDepartmentIds()
    {

        $userId = CommonFunction::getUserId();
        $delegated_usersArr = Users::where('delegate_to_user_id', $userId)
            ->orWhere('id', $userId)
            ->get([
                'id as user_id',
                'desk_id',
                'division_id',
                'department_id',
                'sub_department_id'
            ]);
        $delegatedDeskDepartmentIds = array();
        foreach ($delegated_usersArr as $value) {

            $userDesk = explode(',', $value->desk_id);
            $userDivision = explode(',', $value->division_id);
            $userDepartment = explode(',', $value->department_id);
            $userSubDepartment = explode(',', $value->sub_department_id);
            $tempArr = array();
            $tempArr['user_id'] = $value->user_id;
            $tempArr['desk_ids'] = $userDesk;
            $tempArr['division_ids'] = $userDivision;
            $tempArr['department_ids'] = $userDepartment;
            $tempArr['sub_department_ids'] = $userSubDepartment;
            $delegatedDeskDepartmentIds[$value->user_id] = $tempArr;
        }
        return $delegatedDeskDepartmentIds;
    }

    /*
     * check application processing permission for desk user
     * $desk_id = application desk id
     * $department_id = application department id
     * $process_type_id = application process type id
     * $user_id = application user id
     */
    public static function hasDeskDepartmentWisePermission($desk_id, $approval_center_id, $department_id, $sub_department_id, $process_type_id, $user_id, $userType)
    {
        if (in_array($userType, ['9x901', '9x902', '9x903', '9x904'])) { //stack holder user list
            $getSelfAndDelegatedUserDeskDepartmentIds = CommonFunction::getSelfAndDelegatedUserDeskDepartmentIds();
            foreach ($getSelfAndDelegatedUserDeskDepartmentIds as $selfDeskId => $value) {
                if (in_array($desk_id, $value['desk_ids']) && ($user_id == $value['user_id'] or $user_id == 0)) {
                    return true;
                }
            }
        } elseif ($userType == '6x606') { // Bangladesh bank desk
            $getSelfAndDelegatedUserDeskDepartmentIds = CommonFunction::getSelfAndDelegatedUserDeskDepartmentIds();
            foreach ($getSelfAndDelegatedUserDeskDepartmentIds as $selfDeskId => $value) {
                if ($process_type_id == 20) { // Waiver8 = 20 
                    if (in_array($desk_id, $value['desk_ids']) && (in_array($department_id, $value['department_ids']) or $department_id == 0)
                        && (in_array($sub_department_id, $value['sub_department_ids']) or $sub_department_id == 1) && ($user_id == $value['user_id'] or $user_id == 0)){
                        return true;
                    }
                }
            }
        } elseif ($userType == '4x404') {
            $getSelfAndDelegatedUserDeskDepartmentIds = CommonFunction::getSelfAndDelegatedUserDeskDepartmentIds();
//            dd($getSelfAndDelegatedUserDeskDepartmentIds);
            foreach ($getSelfAndDelegatedUserDeskDepartmentIds as $selfDeskId => $value) {
                // if this is Basic Information application and application desk is 5 and current user desk id  is 1 or 2 or 3
                if ($process_type_id == 100 && $desk_id == 5 &&
                    (in_array(1, $value['desk_ids']) or in_array(2, $value['desk_ids']) or in_array(3, $value['desk_ids']) or in_array(5, $value['desk_ids']))) {
                    return true;
                } else if (in_array($process_type_id, [12,102,13,14,15,1,10,2,3,4,5])) { //BR=102, BRA=12 IRC 1st=13, IRC 2nd=14, IRC 3rd=15, VRN=1, VRA=10, WPN=2,WPE=3,WPA=4,WPC=5
                    if (in_array($desk_id, $value['desk_ids']) && in_array($approval_center_id, $value['division_ids']) && (in_array($department_id, $value['department_ids']) or $department_id == 0)
                        && (in_array($sub_department_id, $value['sub_department_ids']) or $sub_department_id == 1) && ($user_id == $value['user_id'] or $user_id == 0)){
                        return true;
                    }
                } else if (in_array($desk_id, $value['desk_ids']) && (in_array($department_id, $value['department_ids']) or $department_id == 0)
                    && (in_array($sub_department_id, $value['sub_department_ids']) or $sub_department_id == 1) && ($user_id == $value['user_id'] or $user_id == 0)) {
                    return true;
                }
            }
        }
        return false;
    }


    //shahin
    public static function getUserIdByhasDeskDepartmentWisePermission($desk_id, $approval_center_id, $department_id, $sub_department_id, $process_type_id, $user_id, $userType)
    {
        if (in_array($userType, ['9x901', '9x902', '9x903', '9x904'])) { //stack holder user list
            $getSelfAndDelegatedUserDeskDepartmentIds = CommonFunction::getDelegatedUserDeskDepartmentIds();
            foreach ($getSelfAndDelegatedUserDeskDepartmentIds as $selfDeskId => $value) {
                if (in_array($desk_id, $value['desk_ids']) && ($user_id == $value['user_id'] or $user_id == 0)) {
                    return $value['user_id'];
                }
            }
        } elseif ($userType == '4x404') {
            $getSelfAndDelegatedUserDeskDepartmentIds = CommonFunction::getDelegatedUserDeskDepartmentIds();
            foreach ($getSelfAndDelegatedUserDeskDepartmentIds as $selfDeskId => $value) {
                // if this is Basic Information application and application desk is 5 and current user desk id  is 1 or 2 or 3
                if ($process_type_id == 100 && $desk_id == 5 &&
                    (in_array(1, $value['desk_ids']) or in_array(2, $value['desk_ids']) or in_array(3, $value['desk_ids']) or in_array(5, $value['desk_ids']))) {
                    return $value['user_id'];
                } else if (in_array($process_type_id, [12, 102, 13, 14, 15])) { // BR=102, BRA=12
                    if(in_array($desk_id, $value['desk_ids']) && in_array($approval_center_id, $value['division_ids']) && (in_array($department_id, $value['department_ids']) or $department_id == 0)
                        && (in_array($sub_department_id, $value['sub_department_ids']) or $sub_department_id == 1) && ($user_id == $value['user_id'] or $user_id == 0)){
                        return true;
                    }
                } else if (in_array($desk_id, $value['desk_ids']) && (in_array($department_id, $value['department_ids']) or $department_id == 0)
                    && (in_array($sub_department_id, $value['sub_department_ids']) or $sub_department_id == 1) && ($user_id == $value['user_id'] or $user_id == 0)) {
                    return $value['user_id'];
                }
            }
        }
        return false;
    }

    public static function getDesiredDurationDiffDate($appInfo)
    {
        $duration_start = Carbon::parse($appInfo['approved_duration_start_date'])->toDateString(); // format(Y-m-d)
        $duration_end = Carbon::parse($appInfo['approved_duration_end_date'])->toDateString();   // format(Y-m-d)

        $find_leaper = strpos($duration_start, '02-29');
        $is_leaper = date('L', strtotime($duration_end));
        if ($find_leaper !== false && ($is_leaper === "0" || $is_leaper === 0)) {
            $duration_end_date = $duration_end;
        } else {
            $duration_end_date = Carbon::parse($appInfo['approved_duration_end_date'])->addDay(1)->toDateString();   // format(Y-m-d)
        }

        // Stored routine
        DB::select(DB::raw("CALL OSS_TIME_DIFF('$duration_start', '$duration_end_date', @difference)"));
        $result = DB::select('select @difference as date_difference');
        $date_difference = $result[0]->date_difference;

        if ($date_difference == 'Error') {
            $data['string'] = 'Something wrong';
            $data['duration_year'] = 'Something wrong';
            return $data;
        }

        $duration_difference = explode("-", $date_difference);

        $y = $duration_difference[0]; //years
        $m = $duration_difference[1]; //months
        $d = $duration_difference[2]; //days

        $string = '';
        if ($y > 0) {
            $string = ($y <= 1) ? $string . $y . ' year ' : $string . $y . ' years ';
        }
        if ($m > 0) {
            $string = ($m <= 1) ? $string . $m . ' month ' : $string . $m . ' months ';
        }
        if ($d > 0) {
            $string = ($d <= 1) ? $string . $d . ' day ' : $string . $d . ' days ';
        }

        //$duration_year = '2 years payment';

        $duration_year = (int)$duration_difference[0];

        if ((int)$duration_difference[1] > 0 || (int)$duration_difference[2] > 0) {
            $duration_year++;
        }


//        DB::select(DB::raw("CALL OSS_TIME_DIFF('$duration_start', '$duration_end', @difference)"));
//        $year_difference_result = DB::select('select @difference as year_difference');
//        $year_difference = $year_difference_result[0]->year_difference;
//        $duration_year_difference = explode("-", $year_difference);
//        $year_diff = $duration_year_difference[0]; //years

//        if ($year_diff < 1) {
//            $duration_year = '1 year payment';
//        }

        $data['string'] = $string;
        $data['duration_year'] = $duration_year . ' years payment';
        $data['approve_duration_year'] = $duration_year;

        return $data;
    }

    public static function getGovtFees($appInfo)
    {
        $amount = 0;

        $payment_config = PaymentConfiguration::leftJoin('sp_payment_category', 'sp_payment_category.id', '=', 'sp_payment_configuration.payment_category_id')
            ->where([
                'sp_payment_configuration.process_type_id' => $appInfo['process_type_id'],
                'sp_payment_configuration.payment_category_id' => 2, //Government fee payment
                'sp_payment_configuration.status' => 1,
                'sp_payment_configuration.is_archive' => 0
            ])->first(['sp_payment_configuration.*', 'sp_payment_category.name']);

        switch ($appInfo['process_type_id']) {

            // Approve Duration calculation
            case 2: //Work permit new
                $amount = $payment_config->amount * $appInfo['approve_duration_year'];
                break;

            case 3: //Work permit extension
                $amount = $payment_config->amount * $appInfo['approve_duration_year'];
                break;

            case 4: // Work permit amendment
                $amount = $payment_config->amount;
                break;

            case 6: // office permission new
                if ($appInfo['approve_duration_year'] > 0 && $appInfo['approve_duration_year'] <= 3)
                    $amount = $payment_config->amount;
                else if ($appInfo['approve_duration_year'] > 3) {
                    $amount = $payment_config->amount + (10000 * ceil(($appInfo['approve_duration_year'] - 3) / 2));
                } else {
                    $amount = 0;
                }

                break;

            case 7: // office permission extension
                $amount = ceil($appInfo['approve_duration_year'] / 2) * $payment_config->amount;
                break;

            case 8: // office permission amendment
                $amount = $payment_config->amount;
                break;

            case 10: // Visa recommendation amendment
                $amount = $payment_config->amount;
                break;

            case 11: // Remittance
                $amount = Remittance::where('id', $appInfo['app_id'])->pluck('total_fee');
                break;

            case 12: // BIDA Registration Amendment
                $amount = PaymentDistribution::where([
                    'process_type_id' => 12,
                    'sp_pay_category_id' => 2,  // Government fee Payment
                    'distribution_type' => 3,  // Govt-Application-Fee
                    'fix_status' => 1,
                    'status' => 1,
                    'is_archive' => 0,
                ])->limit(1)->pluck('pay_amount');
                break;

            case 13: // Remittance
                $amount = IrcInspection::where('app_id', $appInfo['app_id'])->orderBy('id', 'desc')->limit(1)->pluck('inspection_gov_fee');
                break;

            case 102: // BIDA Registration
                $amount = BidaRegistration::where('id', $appInfo['app_id'])->pluck('total_fee');
                break;
        }

        // if don't need to convert amount to words this getDesiredDurationDiffDate function return here
        if (array_key_exists("not_convert_number_to_words", $appInfo)) {
            return $amount;
        }

        return CommonFunction::getGovFeesInWord($amount);
    }

    public static function getGovFeesInWord($amount)
    {
        $amount_in_word = ucfirst(CommonFunction::convert_number_to_words($amount));
        $amount_with_word = number_format($amount, 2) . ' (' . $amount_in_word . ' only)';
        return $amount_with_word;
    }

    public static function getGovtFeesAmount($appInfo)
    {
        $durationData = commonFunction::getDesiredDurationDiffDate($appInfo);
        $appInfo['approve_duration_year'] = (int)$durationData['approve_duration_year'];
        $appInfo['not_convert_number_to_words'] = 'not_convert_number_to_words';

        return (int)commonFunction::getGovtFees($appInfo);
    }

//    public static function sendEmailSMSOld($caption = '', $appInfo = [], $receiverInfo = [])
//    {
//        try {
//
//            $template = Templates::where('caption', $caption)->first();
//
//            if (isset($appInfo['process_type_id']) && in_array($appInfo['process_type_id'], [12, 101, 102, 103, 104, 105, 106])) { //Eliminating service type from email content for these service
//                $template->email_content = str_replace('Service Type: {$serviceSubName}<br/>', '', $template->email_content);
//            }
//            if (!in_array($caption, ['ACCOUNT_ACTIVATION', 'CONFIRM_ACCOUNT', 'APPROVE_USER', 'REJECT_USER', 'nc_expired_date_notification'])) {
//                $template->email_content = str_replace('{$trackingNumber}', $appInfo['tracking_no'], $template->email_content);
//                $template->email_content = str_replace('{$serviceName}', $appInfo['process_type_name'], $template->email_content);
//                $template->email_content = str_replace('{$serviceSupperName}', $appInfo['process_supper_name'], $template->email_content);
//                $template->email_content = str_replace('{$serviceSubName}', $appInfo['process_sub_name'], $template->email_content);
//                $template->email_content = str_replace('{$remarks}', $appInfo['remarks'], $template->email_content);
//                $template->sms_content = str_replace('{$serviceName}', $appInfo['process_type_name'], $template->sms_content);
//                $template->sms_content = str_replace('{$serviceSupperName}', $appInfo['process_supper_name'], $template->sms_content);
//                $template->sms_content = str_replace('{$trackingNumber}', $appInfo['tracking_no'], $template->sms_content);
//            }
//            if ($caption == 'PROCEED_TO_MEETING') {
//                $template->email_content = str_replace('{$metingNumber}', $appInfo['meting_number'], $template->email_content);
//                $template->email_content = str_replace('{$meetingDate}', $appInfo['meeting_date'], $template->email_content);
//                $template->email_content = str_replace('{$meetingTime}', $appInfo['meeting_time'], $template->email_content);
//            } elseif ($caption == 'nc_expired_date_notification') {
//                $template->email_content = str_replace('{{$trackingNumber}}', $appInfo['tracking_no'], $template->email_content);
//                $template->email_content = str_replace('{{$expired_date}}', $appInfo['exp_date'], $template->email_content);
//            } elseif (in_array($caption, ['APP_APPROVE_AND_PAYMENT', 'APP_CONDITION_SATISFIED_AND_PAYMENT', 'MC_APP_APPROVE_AND_PAYMENT', 'MC_APP_CONDITIONAL_APPROVED'])) {
//                $template->email_content = str_replace('{$govtFees}', $appInfo['govt_fees'], $template->email_content);
//            } elseif ($caption == 'APP_GOV_PAYMENT_SUBMIT') {
//                $template->email_content = str_replace('{$govtFees}', $appInfo['govt_fees'], $template->email_content);
//                $template->email_content = str_replace('{$govtFeesOnlyAmount}', $appInfo['govt_fees_amount'], $template->email_content);
//                $template->email_content = str_replace('{$paymentDate}', $appInfo['payment_date'], $template->email_content);
//                $template->sms_content = str_replace('{$govtFeesOnlyAmount}', $appInfo['govt_fees_amount'], $template->sms_content);
//            } elseif ($caption == 'IMMIGRATION') {
//                $template->email_content = str_replace('{$name}', $appInfo['name'], $template->email_content);
//                $template->email_content = str_replace('{$nationality}', $appInfo['nationality'], $template->email_content);
//                $template->email_content = str_replace('{$passportNumber}', $appInfo['passport_number'], $template->email_content);
//                $template->email_content = str_replace('{$designation}', $appInfo['designation'], $template->email_content);
//                $template->email_content = str_replace('{$visaType}', $appInfo['visa_type'], $template->email_content);
//                $template->email_content = str_replace('{$airportName}', $appInfo['airport_name'], $template->email_content);
//                $template->email_content = str_replace('{$airportAddress}', $appInfo['airport_address'], $template->email_content);
//            } elseif ($caption == 'EMBASSY_HIGH_COMMISSION') {
//                $template->email_content = str_replace('{$name}', $appInfo['name'], $template->email_content);
//                $template->email_content = str_replace('{$nationality}', $appInfo['nationality'], $template->email_content);
//                $template->email_content = str_replace('{$passportNumber}', $appInfo['passport_number'], $template->email_content);
//                $template->email_content = str_replace('{$designation}', $appInfo['designation'], $template->email_content);
//                $template->email_content = str_replace('{$visaType}', $appInfo['visa_type'], $template->email_content);
//                $template->email_content = str_replace('{$highCommissionName}', $appInfo['high_commission_name'], $template->email_content);
//                $template->email_content = str_replace('{$highCommissionAddress}', $appInfo['high_commission_address'], $template->email_content);
//            } elseif ($caption == 'WP_ISSUED_LETTER_STAKEHOLDER') {
//                $template->email_content = str_replace('{$name}', $appInfo['name'], $template->email_content);
//                $template->email_content = str_replace('{$designation}', $appInfo['designation'], $template->email_content);
//                $template->email_content = str_replace('{$nationality}', $appInfo['nationality'], $template->email_content);
//                $template->email_content = str_replace('{$passportNumber}', $appInfo['passport_number'], $template->email_content);
//            } elseif ($caption == 'OP_ISSUED_LETTER_STAKEHOLDER') {
//                $template->email_content = str_replace('{$organizationName}', $appInfo['organization_name'], $template->email_content);
//            } elseif ($caption == 'REJECT_USER') {
//                $template->email_content = str_replace('{$rejectReason}', $appInfo['reject_reason'], $template->email_content);
//            } elseif ($caption == 'CONFIRM_ACCOUNT') {
//                $template->email_content = str_replace('{$verificationLink}', $appInfo['verification_link'], $template->email_content);
//            } elseif (in_array($caption, ['VRN_ISSUED_LETTER_STAKEHOLDER', 'VRA_ISSUED_LETTER_STAKEHOLDER'])) {
//                $template->email_content = str_replace('{$name}', $appInfo['name'], $template->email_content);
//                $template->email_content = str_replace('{$nationality}', $appInfo['nationality'], $template->email_content);
//                $template->email_content = str_replace('{$passportNumber}', $appInfo['passport_number'], $template->email_content);
//                $template->email_content = str_replace('{$designation}', $appInfo['designation'], $template->email_content);
//                $template->email_content = str_replace('{$visaType}', $appInfo['visa_type'], $template->email_content);
//            }elseif ($caption == 'APP_STAKEHOLDER_NOTIFICATION') {
//                $template->email_content = str_replace('{$username}', $appInfo['username'], $template->email_content);
//                $template->email_content = str_replace('{$password}', $appInfo['password'], $template->email_content);
//            }
//
//            $smsBody = $template->sms_content;
//            $header = $template->email_subject;
//            $param = $template->email_content;
//            $caption = $template->caption;
//            $email_content = view("Users::message", compact('header', 'param'))->render();
//
//            $emailQueueData = [];
//            $ccEmailFromConfiguration = CommonFunction::ccEmail();
//            foreach ($receiverInfo as $receiver) {
//                $emailQueue = [];
//                $emailQueue['process_type_id'] = isset($appInfo['process_type_id']) ? $appInfo['process_type_id'] : 0;
//                $emailQueue['app_id'] = isset($appInfo['app_id']) ? $appInfo['app_id'] : 0;
//                $emailQueue['status_id'] = isset($appInfo['status_id']) ? $appInfo['status_id'] : 0;
//                $emailQueue['caption'] = $caption;
//                $emailQueue['email_content'] = $email_content;
//                $emailQueue['email_to'] = $receiver['user_email'];
//                $emailQueue['email_cc'] = !empty($template->email_cc) ? $template->email_cc : $ccEmailFromConfiguration;
//                $emailQueue['email_subject'] = $header;
//                if (!empty(trim($receiver['user_phone'])) && $template->sms_active_status == 1) {
//                    $emailQueue['sms_content'] = $smsBody;
//                    $emailQueue['sms_to'] = $receiver['user_phone'];
//                }
//                $emailQueue['attachment'] = isset($appInfo['attachment']) ? $appInfo['attachment'] : '';
//                $emailQueue['attachment_certificate_name'] = isset($appInfo['attachment_certificate_name']) ? $appInfo['attachment_certificate_name'] : '';
//                $emailQueue['secret_key'] = '';
//                $emailQueue['pdf_type'] = '';
//                $emailQueue['created_at'] = date('Y-m-d H:i:s');
//                $emailQueue['updated_at'] = date('Y-m-d H:i:s');
//
//                $emailQueueData[] = $emailQueue;
//            }
//            EmailQueue::insert($emailQueueData);
//        } catch (\Exception $e) {
//            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [CM-1005]');
//            return Redirect::back()->withInput();
//        }
//    }

    public static function sendEmailSMS($caption = '', $appInfo = [], $receiverInfo = [])
    {
        if (empty($caption) || empty($appInfo) || count($receiverInfo) < 1) {
            // we can keep a log that is missing
            return false;
        }

        try {

            $template = Templates::where('caption', $caption)->first();

            if (isset($appInfo['process_type_id']) && in_array($appInfo['process_type_id'], [12, 101, 102, 103, 104, 105, 106])) { //Eliminating service type from email content for these service
                $template->email_content = str_replace('Service Type: {$serviceSubName}<br/>', '', $template->email_content);
            }

            if (array_key_exists('resend_deadline', $appInfo)) {
                $template->email_content = str_replace('{$resend_deadline}', $appInfo['resend_deadline'], $template->email_content);
                $template->sms_content = str_replace('{$resend_deadline}', $appInfo['resend_deadline'], $template->sms_content);
            }

            if (!in_array($caption, ['ACCOUNT_ACTIVATION', 'CONFIRM_ACCOUNT', 'APPROVE_USER', 'REJECT_USER', 'nc_expired_date_notification', 'TR_BULK_NOTIFICATION'])) {
                $template->email_content = str_replace('{$trackingNumber}', $appInfo['tracking_no'], $template->email_content);
                $template->email_content = str_replace('{$serviceName}', $appInfo['process_type_name'], $template->email_content);
                $template->email_content = str_replace('{$serviceSupperName}', $appInfo['process_supper_name'], $template->email_content);
                $template->email_content = str_replace('{$serviceSubName}', $appInfo['process_sub_name'], $template->email_content);
                $template->email_content = str_replace('{$remarks}', $appInfo['remarks'], $template->email_content);

                $template->sms_content = str_replace('{$serviceName}', $appInfo['process_type_name'], $template->sms_content);
                $template->sms_content = str_replace('{$serviceSupperName}', $appInfo['process_supper_name'], $template->sms_content);
                $template->sms_content = str_replace('{$trackingNumber}', $appInfo['tracking_no'], $template->sms_content);
            }
            if ($caption == 'PROCEED_TO_MEETING') {
                $template->email_content = str_replace('{$metingNumber}', $appInfo['meting_number'], $template->email_content);
                $template->email_content = str_replace('{$meetingDate}', $appInfo['meeting_date'], $template->email_content);
                $template->email_content = str_replace('{$meetingTime}', $appInfo['meeting_time'], $template->email_content);
            } elseif ($caption == 'nc_expired_date_notification') {
                $template->email_content = str_replace('{{$trackingNumber}}', $appInfo['tracking_no'], $template->email_content);
                $template->email_content = str_replace('{{$expired_date}}', $appInfo['exp_date'], $template->email_content);
            } elseif (in_array($caption, ['APP_APPROVE_AND_PAYMENT', 'APP_CONDITION_SATISFIED_AND_PAYMENT', 'MC_APP_APPROVE_AND_PAYMENT', 'MC_APP_CONDITIONAL_APPROVED'])) {
                $template->email_content = str_replace('{$govtFees}', $appInfo['govt_fees'], $template->email_content);
            } elseif ($caption == 'APP_GOV_PAYMENT_SUBMIT') {
                $template->email_content = str_replace('{$govtFees}', $appInfo['govt_fees'], $template->email_content);
                $template->email_content = str_replace('{$govtFeesOnlyAmount}', $appInfo['govt_fees_amount'], $template->email_content);
                $template->email_content = str_replace('{$paymentDate}', $appInfo['payment_date'], $template->email_content);
                $template->sms_content = str_replace('{$govtFeesOnlyAmount}', $appInfo['govt_fees_amount'], $template->sms_content);
            } elseif ($caption == 'IMMIGRATION') {
                $template->email_content = str_replace('{$name}', $appInfo['name'], $template->email_content);
                $template->email_content = str_replace('{$nationality}', $appInfo['nationality'], $template->email_content);
                $template->email_content = str_replace('{$passportNumber}', $appInfo['passport_number'], $template->email_content);
                $template->email_content = str_replace('{$designation}', $appInfo['designation'], $template->email_content);
                $template->email_content = str_replace('{$visaType}', $appInfo['visa_type'], $template->email_content);
                $template->email_content = str_replace('{$airportName}', $appInfo['airport_name'], $template->email_content);
                $template->email_content = str_replace('{$airportAddress}', $appInfo['airport_address'], $template->email_content);
            } elseif ($caption == 'EMBASSY_HIGH_COMMISSION') {
                $template->email_content = str_replace('{$name}', $appInfo['name'], $template->email_content);
                $template->email_content = str_replace('{$nationality}', $appInfo['nationality'], $template->email_content);
                $template->email_content = str_replace('{$passportNumber}', $appInfo['passport_number'], $template->email_content);
                $template->email_content = str_replace('{$designation}', $appInfo['designation'], $template->email_content);
                $template->email_content = str_replace('{$visaType}', $appInfo['visa_type'], $template->email_content);
                $template->email_content = str_replace('{$highCommissionName}', $appInfo['high_commission_name'], $template->email_content);
                $template->email_content = str_replace('{$highCommissionAddress}', $appInfo['high_commission_address'], $template->email_content);
            } elseif ($caption == 'VIPL_IMMIGRATION') {
                $template->email_content = str_replace('{$companyName}', $appInfo['company_name'], $template->email_content);
                $template->email_content = str_replace('{$name}', $appInfo['name'], $template->email_content);
                $template->email_content = str_replace('{$designation}', $appInfo['designation'], $template->email_content);
                $template->email_content = str_replace('{$approved_date}', $appInfo['approved_date'], $template->email_content);
            } elseif ($caption == 'VIPL_ISSUED_LETTER_STAKEHOLDER') {
                $template->email_content = str_replace('{$companyName}', $appInfo['company_name'], $template->email_content);
                $template->email_content = str_replace('{$name}', $appInfo['name'], $template->email_content);
                $template->email_content = str_replace('{$designation}', $appInfo['designation'], $template->email_content);
                $template->email_content = str_replace('{$approved_date}', $appInfo['approved_date'], $template->email_content);
            } elseif ($caption == 'WP_ISSUED_LETTER_STAKEHOLDER') {
                $template->email_content = str_replace('{$name}', $appInfo['name'], $template->email_content);
                $template->email_content = str_replace('{$designation}', $appInfo['designation'], $template->email_content);
                $template->email_content = str_replace('{$nationality}', $appInfo['nationality'], $template->email_content);
                $template->email_content = str_replace('{$passportNumber}', $appInfo['passport_number'], $template->email_content);
            } elseif (in_array($caption, ['IRC_IO_ASSIGN_APPLICANT_CONTENT', 'IRC_IO_ASSIGN_DESK_CONTENT'])) {
                $template->email_content = str_replace('{$organization}', $appInfo['organization_name'], $template->email_content);
                $template->email_content = str_replace('{$trackingNumber}', $appInfo['tracking_no'], $template->email_content);
                $template->email_content = str_replace('{$ircType}', $appInfo['irc_type'], $template->email_content);

                $template->sms_content = str_replace('{$trackingNumber}', $appInfo['tracking_no'], $template->sms_content);

                if ($caption == 'IRC_IO_ASSIGN_APPLICANT_CONTENT') {
                    $template->email_content = str_replace('{$ioFullname}', $appInfo['ins_officer_name'], $template->email_content);
                    $template->email_content = str_replace('{$ioDesignation}', $appInfo['ins_officer_designation'], $template->email_content);
                    $template->email_content = str_replace('{$ioMobileNo}', $appInfo['ins_officer_phone_no'], $template->email_content);
                    $template->email_content = str_replace('{$ioEmail}', $appInfo['ins_officer_email'], $template->email_content);

                    $template->sms_content = str_replace('{$ioDesignation}', $appInfo['ins_officer_designation'], $template->sms_content);
                    $template->sms_content = str_replace('{$ioFullname}', $appInfo['ins_officer_name'], $template->sms_content);
                    $template->sms_content = str_replace('{$ioDesignation}', $appInfo['ins_officer_designation'], $template->sms_content);
                    $template->sms_content = str_replace('{$ioMobileNo}', $appInfo['ins_officer_phone_no'], $template->sms_content);
                } else {
                    $template->email_content = str_replace('{$ioSubmissionDeadline}', CommonFunction::changeDateFormat($appInfo['io_submission_deadline']), $template->email_content);
                }
            } elseif ($caption == 'APP_APPROVE_EXCEPT_IRC_APPROVAL_COPY') {
                $template->email_content = str_replace('{$inspectionAmount}', $appInfo['inspection_amount'], $template->email_content);
            } elseif ($caption == 'OP_ISSUED_LETTER_STAKEHOLDER') {
                $template->email_content = str_replace('{$organizationName}', $appInfo['organization_name'], $template->email_content);
            } elseif ($caption == 'WVR8_ISSUED_LETTER_STAKEHOLDER') {
                $template->email_content = str_replace('{$organizationName}', $appInfo['organization_name'], $template->email_content);
            } elseif ($caption == 'REJECT_USER') {
                $template->email_content = str_replace('{$rejectReason}', $appInfo['reject_reason'], $template->email_content);
            } elseif ($caption == 'CONFIRM_ACCOUNT') {
                $template->email_content = str_replace('{$verificationLink}', $appInfo['verification_link'], $template->email_content);
            } elseif (in_array($caption, ['VRN_ISSUED_LETTER_STAKEHOLDER', 'VRA_ISSUED_LETTER_STAKEHOLDER'])) {
                $template->email_content = str_replace('{$name}', $appInfo['name'], $template->email_content);
                $template->email_content = str_replace('{$nationality}', $appInfo['nationality'], $template->email_content);
                $template->email_content = str_replace('{$passportNumber}', $appInfo['passport_number'], $template->email_content);
                $template->email_content = str_replace('{$designation}', $appInfo['designation'], $template->email_content);
                $template->email_content = str_replace('{$visaType}', $appInfo['visa_type'], $template->email_content);
            }elseif ($caption == 'TR_BULK_NOTIFICATION'){
                $template->email_content = str_replace('{$email_description}', $appInfo['email_description'], $template->email_content);
                $template->email_content = str_replace('{$attachment}', $appInfo['attachment'], $template->email_content);
                $template->sms_content = str_replace('{$email_description}', $appInfo['email_description'], $template->sms_content);
                $template->sms_content = str_replace('{$attachment}', $appInfo['attachment'], $template->sms_content);
                if ($appInfo['email_subject']) {
                    $template->email_subject = $appInfo['email_subject'];
                }
            }

            $smsBody = $template->sms_content;
            $header = $template->email_subject;
            $param = $template->email_content;
            $caption = $template->caption;
            $email_content = view("Users::message", compact('header', 'param'))->render();


            $ccEmailFromConfiguration = CommonFunction::ccEmail();
            //            $NotificationWebService = new NotificationWebService();
            if ($template->email_active_status == 1 || $template->sms_active_status == 1){  // checking whether template status is on/off for email and sms
                $emailQueueData = [];
                foreach ($receiverInfo as $receiver) {
                    $emailQueue = [];
                    $emailQueue['process_type_id'] = isset($appInfo['process_type_id']) ? $appInfo['process_type_id'] : 0;
                    $emailQueue['app_id'] = isset($appInfo['app_id']) ? $appInfo['app_id'] : 0;
                    $emailQueue['status_id'] = isset($appInfo['status_id']) ? $appInfo['status_id'] : 0;
                    $emailQueue['caption'] = $caption;
                    $emailQueue['email_content'] = $email_content;
                    if ($template->email_active_status == 1){
                        $emailQueue['email_to'] = $receiver['user_email'];
                        $emailQueue['email_cc'] = !empty($template->email_cc) ? $template->email_cc : $ccEmailFromConfiguration;
                    }
                    $emailQueue['email_subject'] = $header;
                    if (!empty(trim($receiver['user_phone'])) && $template->sms_active_status == 1) {
                        $emailQueue['sms_content'] = $smsBody;
                        $emailQueue['sms_to'] = $receiver['user_phone'];

                        // Instant SMS Sending
//                        $sms_sending_response = $NotificationWebService->sendSms($receiver['user_phone'], $smsBody);
//                        $emailQueue['sms_response'] = $sms_sending_response['msg'];
//                        if ($sms_sending_response['status'] === 1) {
//                            $emailQueue['sms_status'] = 1;
//                            $emailQueue['sms_response_id'] = $sms_sending_response['message_id'];
//                        }
                        // End of Instant SMS Sending
                    }
                    $emailQueue['attachment'] = isset($appInfo['attachment']) ? $appInfo['attachment'] : '';
                    $emailQueue['attachment_certificate_name'] = isset($appInfo['attachment_certificate_name']) ? $appInfo['attachment_certificate_name'] : '';
                    //$emailQueue['secret_key'] = '';
                    //$emailQueue['pdf_type'] = '';
                    $emailQueue['created_at'] = date('Y-m-d H:i:s');
                    $emailQueue['updated_at'] = date('Y-m-d H:i:s');

                    // Instant Email sending
//                    if (empty($emailQueue['attachment_certificate_name']) && $template->email_active_status == 1) {

//                        $email_sending_response = $NotificationWebService->sendEmail([
//                            'header_text' => config('app.project_name'),
//                            'recipient' => $receiver['user_email'],
//                            'subject' => $header,
//                            'bodyText' => '',
//                            'bodyHtml' => $email_content,
//                            'email_cc' => $emailQueue['email_cc']
//                        ]);
//                        //dd($email_sending_response);
//                        $emailQueue['email_response'] = $email_sending_response['msg'];
//                        if ($email_sending_response['status'] === 1) {
//                            $emailQueue['email_status'] = 1;
//                            $emailQueue['email_response_id'] = $email_sending_response['message_id'];
//                        }
//                    }
                    // End of Instant Email sending

                    $emailQueueData[] = $emailQueue;
                }
                EmailQueue::insert($emailQueueData);
            }

            return true;

        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [CM-1005]');
            // dd($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
            return false;
            //return Redirect::back()->withInput();
        }
    }

    public static function getTrackingNoByProcessId($processListId)
    {
        return ProcessList::where('id', $processListId)->pluck('tracking_no');
    }

    public static function DelegateUserInfo($desk_id)
    {

        $userID = CommonFunction::getUserId();
//        $delegateUserInfo = Users::where('desk_id', 'like', '%' . $desk_id . '%')
//            ->where('delegate_to_user_id', $userID)
//            ->first([
//                'id',
//                DB::raw("CONCAT(users.user_first_name,' ',users.user_middle_name, ' ',users.user_last_name) as user_full_name"),
//                'user_email',
//                'user_pic',
//                'designation'
//            ]);

        $delegateUserInfo = Users::where('delegate_to_user_id', $userID)
            ->first([
                'id',
                DB::raw("CONCAT(users.user_first_name,' ',users.user_middle_name, ' ',users.user_last_name) as user_full_name"),
                'user_email',
                'user_pic',
                'designation'
            ]);
        return $delegateUserInfo;
    }

    public static function getPicture($type, $ref_id)
    {
        $files = new FilesController();
        $img_data = $files->getFile(['type' => $type, 'ref_id' => $ref_id]);
        $json_data = json_decode($img_data->getContent());
        if ($json_data->responseCode == 1) {
            $base64 = $json_data->data;
        } else {
            $user_pic = User::where('id', $ref_id)->first(['user_pic']);
            $pos = strpos($user_pic, 'http');
            if ($pos === false) {
                $path = 'assets/images/no_image.png';
            } else {
                $path = $user_pic->user_pic;
            }
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        return $base64;
    }

    public static function convert2Bangla($eng_number)
    {
        $eng = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $ban = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        return str_replace($eng, $ban, $eng_number);
    }

    public static function convert2English($ban_number)
    {
        $eng = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $ban = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        return str_replace($ban, $eng, $ban_number);
    }

    public static function generateTrackingID($prefix, $id)
    {
        $prefix = strtoupper($prefix);
        $str = $id . date('Y') . mt_rand(0, 9);
        if ($prefix == 'M' || $prefix == 'N') {
            if (strlen($str) > 12) {
                $str = substr($str, strlen($str) - 12);
            }
        } elseif ($prefix == 'G') {
            if (strlen($str) > 10) {
                $str = substr($str, strlen($str) - 10);
            }
        } elseif ($prefix == 'T') {
            if (strlen($str) > 12) {
                $str = substr($str, strlen($str) - 12);
            }
        } else {
            if (strlen($str) > 14) {
                $str = substr($str, strlen($str) - 14);
            }
        }
        return $prefix . dechex($str);
    }

    public static function getImageConfig($type)
    {
        extract(CommonFunction::getImageDocConfig());
        $config = Configuration::where('caption', $type)->pluck('details');
        $reportHelper = new ReportHelper();
//        [File Format: *.jpg / *.png Dimension: {$height}x{$width}px File size($filesize)KB]
        if ($type == 'IMAGE_SIZE') {
            $data['width'] = ($IMAGE_WIDTH - ($IMAGE_WIDTH * $IMAGE_DIMENSION_PERCENT) / 100) . '-' . ($IMAGE_WIDTH + ($IMAGE_WIDTH * $IMAGE_DIMENSION_PERCENT) / 100);
            $data['height'] = ($IMAGE_HEIGHT - ($IMAGE_HEIGHT * $IMAGE_DIMENSION_PERCENT) / 100) . '-' . ($IMAGE_HEIGHT + ($IMAGE_HEIGHT * $IMAGE_DIMENSION_PERCENT) / 100);
            $data['variation'] = $IMAGE_DIMENSION_PERCENT;
            $data['filesize'] = $IMAGE_SIZE;
        } elseif ($type == 'DOC_IMAGE_SIZE') {
            $data['width'] = ($DOC_WIDTH - ($DOC_WIDTH * $IMAGE_DIMENSION_PERCENT) / 100) . '-' . ($DOC_WIDTH + ($DOC_WIDTH * $IMAGE_DIMENSION_PERCENT) / 100);
            $data['height'] = ($DOC_HEIGHT - ($DOC_HEIGHT * $IMAGE_DIMENSION_PERCENT) / 100) . '-' . ($DOC_HEIGHT + ($DOC_HEIGHT * $IMAGE_DIMENSION_PERCENT) / 100);
            $data['variation'] = $DOC_DIMENSION_PERCENT;
            $data['filesize'] = $DOC_SIZE;
        }
        $string = $reportHelper->ConvParaEx($config, $data);
        return $string;
    }

    //   ConvParaEx function imported from Report Helper Libraries
    public static function ConvParaEx($sql, $data, $sm = '{$', $em = '}', $optional = false)
    {
        $sql = ' ' . $sql;
        $start = strpos($sql, $sm);
        $i = 0;
        while ($start > 0) {
            if ($i++ > 20) {
                return $sql;
            }
            $end = strpos($sql, $em, $start);
            if ($end > $start) {
                $filed = substr($sql, $start + 2, $end - $start - 2);
                if (strtolower(substr($filed, 0, 8)) == 'optional') {
                    $optionalCond = self::ConvParaEx(substr($filed, 9), $data, '[$', ']', true);
                    $sql = substr($sql, 0, $start) . $optionalCond . substr($sql, $end + 1);
                } else {
                    $inputData = self::getData($filed, $data, substr($sql, 0, $start));
                    if ($optional && (($inputData == '') || ($inputData == "''"))) {
                        $sql = '';
                        break;
                    } else {
                        $sql = substr($sql, 0, $start) . $inputData . substr($sql, $end + 1);
                    }
                }
            }
            $start = strpos($sql, $sm);
        }
        return trim($sql);
    }

    public static function getData($filed, $data, $prefix = null)
    {
        $filedKey = explode('|', $filed);
        $val = trim($data[$filedKey[0]]);
        if (!is_numeric($val)) {
            if ($prefix) {
                $prefix = strtoupper(trim($prefix));
                if (substr($prefix, strlen($prefix) - 3) == 'IN(') {
                    $vals = explode(',', $val);
                    $val = '';
                    for ($i = 0; $i < count($vals); $i++) {
                        if (is_numeric($vals[$i])) {
                            $val .= (strlen($val) > 0 ? ',' : '') . $vals[$i];
                        } else {
                            $val .= (strlen($val) > 0 ? ',' : '') . "'" . $vals[$i] . "'";
                        }
                    }
                } elseif (!(substr($prefix, strlen($prefix) - 1) == "'" || substr($prefix, strlen($prefix) - 1) == "%")) {
                    $val = "'" . $val . "'";
                }
            }
        }
        if ($val == '') $val = "''";
        return $val;
    }

    public static function getNotice($flag = 0, $limit=50)
    {
        if ($flag == 1) {
            $list = DB::select(DB::raw("SELECT date_format(updated_at,'%d %M, %Y') `Date`,heading,details,importance,id, case when importance='Top' then 1 else 0 end Priority FROM notice where status='public' or status='private' and is_active=1 and prefix=NULL order by Priority desc, updated_at desc LIMIT $limit"));
        } else {
            $list = DB::select(DB::raw("SELECT date_format(updated_at,'%d %M, %Y') `Date`,heading,details,importance,id, case when importance='Top' then 1 else 0 end Priority FROM notice where status='public' and is_active=1 order by Priority desc, updated_at desc LIMIT $limit"));
        }
        return $list;
    }

    public static function getImageDocConfig()
    {
        $config = array();
        $config['IMAGE_DIMENSION'] = Configuration::where('caption', 'IMAGE_SIZE')->pluck('value');
        $config['IMAGE_SIZE'] = Configuration::where('caption', 'IMAGE_SIZE')->pluck('value2');

        // Image size
        $split_img_size = explode('-', $config['IMAGE_SIZE']);
        $config['IMAGE_MIN_SIZE'] = $split_img_size[0];
        $config['IMAGE_MAX_SIZE'] = $split_img_size[1];

        // image dimension
        $split_img_dimension = explode('x', $config['IMAGE_DIMENSION']);
        $split_img_variation = explode('~', $split_img_dimension[1]);
        $config['IMAGE_WIDTH'] = $split_img_dimension[0];
        $config['IMAGE_HEIGHT'] = $split_img_variation[0];
        $config['IMAGE_DIMENSION_PERCENT'] = $split_img_variation[1];

        //image max/min width and height
        $config['IMAGE_MIN_WIDTH'] = $split_img_dimension[0] - (($split_img_dimension[0] * $split_img_variation[1]) / 100);
        $config['IMAGE_MAX_WIDTH'] = $split_img_dimension[0] + (($split_img_dimension[0] * $split_img_variation[1]) / 100);

        $config['IMAGE_MIN_HEIGHT'] = $split_img_variation[0] - (($split_img_variation[0] * $split_img_variation[1]) / 100);
        $config['IMAGE_MAX_HEIGHT'] = $split_img_variation[0] + (($split_img_variation[0] * $split_img_variation[1]) / 100);

        //========================= image config end =====================
        // for doc file
        $config['DOC_DIMENSION'] = Configuration::where('caption', 'DOC_IMAGE_SIZE')->pluck('value');
        $config['DOC_SIZE'] = Configuration::where('caption', 'DOC_IMAGE_SIZE')->pluck('value2');

        // Doc size
        $split_doc_size = explode('-', $config['DOC_SIZE']);
        $config['DOC_MIN_SIZE'] = $split_doc_size[0];
        $config['DOC_MAX_SIZE'] = $split_doc_size[1];

        // doc dimension
        $split_doc_dimension = explode('x', $config['DOC_DIMENSION']);
        $split_doc_variation = explode('~', $split_doc_dimension[1]);
        $config['DOC_WIDTH'] = $split_doc_dimension[0];
        $config['DOC_HEIGHT'] = $split_doc_variation[0];
        $config['DOC_DIMENSION_PERCENT'] = $split_doc_variation[1];

        //doc max/min width and height
        $config['DOC_MIN_WIDTH'] = $split_doc_dimension[0] - (($split_doc_dimension[0] * $split_doc_variation[1]) / 100);
        $config['DOC_MAX_WIDTH'] = $split_doc_dimension[0] + (($split_doc_dimension[0] * $split_doc_variation[1]) / 100);

        $config['DOC_MIN_HEIGHT'] = $split_doc_variation[0] - (($split_doc_variation[0] * $split_doc_variation[1]) / 100);
        $config['DOC_MAX_HEIGHT'] = $split_doc_variation[0] + (($split_doc_variation[0] * $split_doc_variation[1]) / 100);

        return $config;
    }

    public static function updateScriptPara($script, $data)
    {
        $start = strpos($script, '{$');
        while ($start > 0) {
            $end = strpos($script, '}', $start);
            if ($end > 0) {
                $filed = substr($script, $start + 2, $end - $start - 2);
                $script = substr($script, 0, $start) . $data[$filed] . substr($script, $end + 1);
            }

            $start = strpos($script, '{$');
        }

        return $script;
    }

    public static function getDeskName($desk_id)
    {
        if (Auth::user()) {
            return UserDesk::where('id', $desk_id)->pluck('desk_name');
        } else {
            return '';
        }
    }

    public static function getCompanyNameById($id)
    {
        if ($id) {
            return CompanyInfo::where('id', $id)->pluck('company_name');
        } else {
            return 'N/A';
        }
    }

    public static function getDepartmentNameById($id)
    {
        if ($id) {
            $name = Department::where('id', $id)->pluck('name');
            return $name;
        } else {
            return 'N/A';
        }
    }

    public static function getVisaTypeByAppTypeId($id)
    {
        if ($id) {
            $visa_type = DeptApplicationTypes::where('id', $id)->pluck('name');
            return $visa_type;
        } else {
            return 'N/A';
        }
    }

    public static function getOfficeTypeById($office_type_id)
    {
        if ($office_type_id) {
            $office_type_name = OPOfficeType::where('id', $office_type_id)->pluck('name');
            return $office_type_name;
        } else {
            return 'N/A';
        }
    }

    public static function getCompanyBnNameById($id)
    {
        if ($id) {
            $name = CompanyInfo::where('id', $id)->pluck('company_name_bn');
            return $name;
        } else {
            return 'N/A';
        }
    }

    public static function report_gen($id, $data, $report_title, $link = '', $heading = '')
    {
        $dataTablePara = '';
        $showaction = false;
        $cols = array();
        $count = 0;
        if ($link) {
            $json_data = json_decode($link);
            if (!empty($json_data)) {
                foreach ($json_data as $jrow) {
                    if ($jrow->type == 'link') {
                        $showaction = true;
                    } else if ($jrow->type == 'dataTable') {
                        $dataTablePara = $jrow->properties;
                    } else if ($jrow->type == 'column') {
                        $cols[$jrow->ID]['caption'] = $jrow->caption;
                        $cols[$jrow->ID]['style'] = $jrow->style;
                    } else {
                        $showaction = true;
                    }
                }
            }
        }
        ?>
        <div class="graph_box">
            <?php if ($heading) { ?>
                <div class="report_heading">
                    <div><?php echo $heading; ?></div>
                </div>
            <?php } ?>
            <?php if (count($data) > 0) { ?>
                <table id="report_data" class="table-rpt-border table table-responsive table-condensed">
                    <thead>
                    <tr>
                        <?php
                        foreach ($data[0] as $key => $value) {
                            echo '<th';
                            if (isset($cols[$key]['style']))
                                echo ' style="' . $cols[$key]['style'] . '"';
                            echo '>';
                            echo isset($cols[$key]['caption']) ? $cols[$key]['caption'] : $key;
                            echo '</th>';
                        }
                        if ($showaction) {
                            echo '<th>Action</th>';
                        }
                        ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sl = 0;
                    foreach ($data as $row):
                        $rowdata = array();
                        if ($sl % 2 == 0) {
                            $row_bg_color = 'style="background-color:#FAFAFA"';
                        } else {
                            $row_bg_color = 'style=""';
                        }
                        if ($count >= 250) {
                            echo '<tfoot><tr><td colspan="5"><b>Showing ' . 250 . ' rows out of total ' . count($data) . '! Please export as CSV to show all data.</b></td></tr></tfoot>';
                            break;
                        }
                        $count++;
                        ?>
                        <tr <?php echo $row_bg_color; ?>>
                            <?php
                            foreach ($row as $key => $field_value):
                                //echo '<td>';
                                $td_align = is_numeric($field_value) ? 'text-align:center;' : '';
                                echo '<td';
                                if (isset($cols[$key]['style']))
                                    echo ' style="' . $cols[$key]['style'] . ';"';
                                echo '>';
                                echo formatTDValue($field_value);
//                                if (is_numeric($field_value)) {
//                                    echo '<span style="text-align:center;width:100%;float: left;">' . $field_value . '&nbsp;</span>';
//                                } else {
//                                    echo $field_value . '&nbsp;';
//                                }
                                echo '</td>';
                                if ($link) {
                                    $rowdata[$key] = $field_value;
                                }
                            endforeach;
                            if ($showaction) {
                                echo '<td>';
                                foreach ($json_data as $jrow) {
                                    if ($jrow->type == 'link') {
                                        $rowdata['baseurl'] = base_url();
                                        echo '<a href="' . ConvPara($jrow->url, $rowdata) . '">' . $jrow->caption . '</a>&nbsp;';
                                    } else if ($jrow->type == 'dataTable') {

                                    } else {
                                        print_r($jrow);
                                    }
                                }
                                echo '</td>';
                            }
                            ?>
                        </tr>
                        <?php
                        $sl++;
                    endforeach;
                    if ($count <= 250) {
                        echo '<tfoot><tr><td colspan="5">Showing ' . $count . ' rows out of total ' . count($data) . ' Records</td></tr></tfoot>';
                    } ?>
                    </tbody>
                </table>

                <?php
            } else {
                echo '<h4 style="text-align: center;color: gray">Data Not Found!</h4>';
            }
            ?>
        </div>

        <?php
        return $count;
    }

    public static function lastAction()
    {
        $lastAction = ActionInformation::where('user_id', '=', Auth::user()->id)->orderBy('id', 'DESC')->limit(3)->get();
        return $lastAction;
    }

    public static function pendingApplication()
    {
        $userDeskIds = CommonFunction::getUserDeskIds();
        $userDepartmentIds = CommonFunction::getUserDepartmentIds();
        $userSubDepartmentIds = CommonFunction::getUserSubDepartmentIds();
        $divisionIds = UtilFunction::getUserDivisionIds();
        $user_id = CommonFunction::getUserId();
        $from = Carbon::now();
        $to = Carbon::now();
        $from->subMonths(3); //maximum 3 month data selection by default

        return ProcessList::leftJoin('process_type', 'process_list.process_type_id', '=', 'process_type.id')
            ->whereIn('process_type.id', \Illuminate\Support\Facades\Session::get('accessible_process'))
            ->where(function ($query1) use ($userDeskIds, $userDepartmentIds, $user_id, $userSubDepartmentIds, $divisionIds) {
                $query1->where(function ($query2) use ($userDeskIds, $userDepartmentIds, $user_id, $userSubDepartmentIds, $divisionIds) {
                    $query2->whereIn('process_list.desk_id', $userDeskIds)
                        ->where(function ($query1) use ($userDepartmentIds) {
                            $query1->whereIn('process_list.department_id', $userDepartmentIds)
                                ->orWhere('process_list.department_id', 0);
                        })
                        ->where(function ($query2) use ($userSubDepartmentIds) {
                            $query2->whereIn('process_list.sub_department_id', $userSubDepartmentIds)
                                ->orWhere('process_list.sub_department_id', 1);
                        })
                        ->where(function ($query2) use ($divisionIds) {
                            $query2->whereIn('process_list.approval_center_id', $divisionIds)
                                ->orWhere('process_list.approval_center_id', 0);
                        })
                        ->where(function ($query2) use ($user_id) {
                            $query2->where('process_list.user_id', $user_id)
                                ->orWhere('process_list.user_id', 0);
                        });
                })
                    ->orWhere('process_list.user_id', $user_id);
            })
            ->whereNotIn('process_list.status_id', [-1,19])
            ->whereBetween('process_list.updated_at', [$from, $to])
            ->count();
    }

    public static function requestPinNumber()
    {
        $email_queue_id = \Session::get('email_queue_id');
        $users = Users::where('id', CommonFunction::getUserId())->get(['user_email', 'user_phone']);
        $emailAndSms = EmailQueue::where('id', $email_queue_id)->orderby('id', 'DESC')->first(['email_to', 'sms_to']);
        $code = rand(1000, 9999);
        $token = $code . '-' . CommonFunction::getUserId();
        $encrypted_pin = Encryption::encode($token);

        $data = [
            'code' => $code
        ];

        Users::where('user_email', $users->user_email)->update(['pin_number' => $encrypted_pin]);

        CommonFunction::sendEmailSMS('PIN_NUMBER', $data, $users);
        return true;
    }

    public static function alreadyAdded($process_id, $agenda_id = 0)
    {
        $boardMeting = ProcessListBoardMeting::where('process_id', $process_id)->where('is_archive', 0)->first();
//        $boardMeting =  ProcessListBoardMeting::where('process_id', $process_id)->where('agenda_id', $agenda_id)->where('is_archive', 0)->first();
        if ($boardMeting) {
            $a = 1;
        } else {
            $a = 0;
        }
        return $a;
    }

    public static function alreadyAddedAgenda($agenda_id)
    {
        $boardMeting = ProcessListBoardMeting::where('agenda_id', $agenda_id)->first();
        if ($boardMeting) {
            $a = 1;
        } else {
            $a = 0;
        }
        return $a;
    }

    public static function checkChairperson($board_meeting_id)
    {
        $boardMeting = Committee::where('board_meeting_id', $board_meeting_id)->where('type', '=', 'yes')->first();
        return $boardMeting->user_email;
    }

    public static function getBoardMeetingInfo($ref_id)
    {
        $board_meeting_id = Encryption::decodeId(Session::get('board_meeting_id'));
        $agenda_id = Encryption::decodeId(Session::get('agenda_id'));
        $app_id = Encryption::decodeId($ref_id);

        $boardMeetingInfo = BoardMeting::leftJoin('board_meeting_process_status', 'board_meeting_process_status.id', '=', 'board_meting.status')
            ->where('board_meting.id', $board_meeting_id)
            ->first(['board_meting.*', 'board_meeting_process_status.status_name', 'board_meeting_process_status.panel']);

        $agendaInfo = Agenda::leftJoin('process_type', 'process_type.id', '=', 'agenda.process_type_id')
            ->leftJoin('board_meeting_process_status', 'board_meeting_process_status.id', '=', 'agenda.status')
            ->where('agenda.id', $agenda_id)
            ->first(['agenda.*', 'process_type.name as process_name',
                'board_meeting_process_status.status_name', 'board_meeting_process_status.id as status_id', 'board_meeting_process_status.panel']);

        if ($boardMeetingInfo->status == 10) { // 11= board meeting publish
            $chairmanRemarks = ProcessList::leftJoin('process_list_board_meeting', 'process_list.id', '=', 'process_list_board_meeting.process_id')
                ->where('ref_id', $app_id)
                ->first(['process_list_board_meeting.bm_remarks']);
        } else {
            $chairmanRemarks = '';
        }

        $data = ['agenda_info' => $agendaInfo, 'board_meeting_info' => $boardMeetingInfo, 'chairmanRemarks' => $chairmanRemarks];
        return $data;
    }

    public static function getMemberRemarks($id)
    {
        $bm_process = ProcessListBMRemarks::where('bm_process_id', $id)->where('user_id', CommonFunction::getUserId())->orderBy('id', 'desc')->first();

        if (count($bm_process) > 0) {
            return $bm_process->remarks;
        } else {
            return $bm_process = "";
        }
    }

    public static function getSequenceNo($board_meeting_id)
    {
        $id = Encryption::decodeId($board_meeting_id);
        $data = BoardMeting::where('id', $id)->first();
        $sequence_no = 1;
        if (count($data) > 0) {
            $sequence_no = $data->sequence_no;
        }
        return $sequence_no;
    }

    public static function checkCompleteProfileInfo()
    {
        $user_id = CommonFunction::getUserId();
        $userInfo = Users::find($user_id);

        if ($userInfo->user_type == traineeUserType()) {
            return true;
        }

        if (($userInfo->user_type == '4x404') && ($userInfo->user_first_name == '' || $userInfo->user_email == '' || $userInfo->user_phone == ''
                || $userInfo->user_pic == '' || $userInfo->designation == '' || $userInfo->signature == '')) {
            return false;
        } elseif ($userInfo->user_first_name == '' || $userInfo->user_email == '' || $userInfo->user_phone == ''
            || $userInfo->user_pic == '' || $userInfo->designation == '') {
            return false;
        }  
        else {
            return true;
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

    public static function checkFavoriteItem($process_id)
    {
        $result = ProcessFavoriteList::where('process_id', $process_id)
            ->where('user_id', CommonFunction::getUserId())
            ->count();
        return $result;
    }

    /**
     * Eligibility checking for minimum one company to access others module/application
     * @return int
     */
    public static function checkEligibility()
    {
        $companyIds = explode(',', Auth::user()->company_ids);
        $data = CompanyInfo::whereIn('id', $companyIds)->where('is_eligible', 1)->count();
        if ($data > 0)
            return 1;
        else
            return 0;
    }

    /**
     * Check whether the applicant company is eligible and have approved basic information application
     * @return int
     */
    public static function checkEligibilityAndBiApps($company_id)
    {
        $basicAppInfo = ProcessList::leftjoin('ea_apps', 'ea_apps.id', '=', 'process_list.ref_id')
            ->leftJoin('company_info', 'company_info.id', '=', 'process_list.company_id')
            ->where('process_list.process_type_id', 100)
            ->where('process_list.status_id', 25)
            ->where('process_list.company_id', $company_id)
            ->where('company_info.is_eligible', 1)
            ->where(function ($query) {
                $query->where('is_new_for_bida', 1)
                    ->orWhere('is_existing_for_bida', 1);
            })
            ->first(['process_list.id']);
        if (count($basicAppInfo) > 0) {
            return 1;
        }
        return 0;
    }

    public static function getDeptIdByCompanyId($company_id)
    {
        $departmentId = ProcessList::leftjoin('ea_apps', 'ea_apps.id', '=', 'process_list.ref_id')
            ->leftJoin('company_info', 'company_info.id', '=', 'process_list.company_id')
            ->where('process_list.process_type_id', 100)
            ->where('process_list.status_id', 25)
            ->where('process_list.company_id', $company_id)
            ->where('company_info.is_eligible', 1)
            ->first(['process_list.department_id']);
        if (empty($departmentId)) {
            return 0;
        }
        return $departmentId->department_id;
    }

    // Basic information
    public static function basicInfoDepSubDepSet($service_type)
    {
        $returnData = [
            'department_id' => '',
            'sub_department_id' => 1 // Sub-department id default 1
        ];

        if (in_array($service_type, [1, 2, 3])) {
            $returnData['department_id'] = 2;
        }

        if (in_array($service_type, [4, 5, 6])) {
            $returnData['department_id'] = 1;
        }

        return $returnData;
    }

    public static function DeptSubDeptSpecification($process_type_id, $data = [])
    {
        /********* Total Department = 3
         * 1. Registration & Incentives-I (Commercial)
         * 2. Registration and Incentives – I (Industry - Foreign)
         * 3. Communication
         ********  Total Sub-department = 6
         * 1. None
         * 2. Registration & Incentive-1 (Industry) Foreign - Work Permit
         * 3. Registration & Incentive-1 (Industry) Foreign
         * 4. Registration & Incentive-1 (Industry) Local
         * 5. Commercial Office
         * 6. Fascilation
         */


        /*
         * Department will be same as Basic Information's department
         * Sub department will be default '1'
         */
        $returnData = [
            'department_id' => $data['department_id'],
            'sub_department_id' => 1
        ];

        switch ($process_type_id) {
            // Visa Recommendation New
            case 1:

                if ($data['app_type'] == 5) {
                    $returnData['department_id'] = 1;
                    $returnData['sub_department_id'] = 6;
                } else if ($data['department_id'] == 1 and in_array($data['app_type'], [1, 2, 3, 4, 6])) {
                    $returnData['sub_department_id'] = 5;
                } else if ($data['department_id'] == 2 and $data['app_type'] != 5) {
                    $returnData['sub_department_id'] = 2;
                }

                break;

            // Visa Recommendation Amendment
            case 10:
                if ($data['department_id'] == 1 || $data['app_type'] == 5) {
                    $returnData['department_id'] = 1;
                    $returnData['sub_department_id'] = 6;
                } else if ($data['department_id'] == 2 and $data['app_type'] != 5) {
                    $returnData['sub_department_id'] = 2;
                }
                break;

            // Outward Remittance Approval
            case 11:
                if ($data['department_id'] == 2) {
                    $returnData['sub_department_id'] = 3;
                }
                break;

            // Work Permit New
            // Work Permit Extension
            case 2:
            case 3:
                if ($data['department_id'] == 1) {
                    $returnData['sub_department_id'] = 5;
                } else if ($data['department_id'] == 2) {
                    $returnData['sub_department_id'] = 2;
                }
                break;

            // Work Permit Amendment
            // Work Permit Cancellation
            case 4:
            case 5:
                if ($data['department_id'] == 1) {
                    $returnData['sub_department_id'] = 6;
                } else if ($data['department_id'] == 2) {
                    $returnData['sub_department_id'] = 2;
                }
                break;

            // Office Permission New
            // Office Permission Extension
            // Office Permission Cancellation
            // Waiver Condition 7
            // Waiver Condition 8
            case 6:
            case 7:
            case 9:
            case 19:
            case 20:
                if ($data['department_id'] == 1) {
                    $returnData['sub_department_id'] = 5;
                }
                break;

            // Office Permission Amendment
            case 8:
                if ($data['department_id'] == 1) {
                    $returnData['sub_department_id'] = 6;
                }
                break;

            // BIDA Registration
            // BIDA Registration Amendment
            // IRC Recommendation 1st Adhoc
            // IRC Recommendation 2nd Adhoc
            // IRC Recommendation 3rd Adhoc
            case 102:
            case 12:
            case 13:
            case 14:
            case 15:
            case 16:
            case 21:
                if ($data['department_id'] == 2) { //Industrial
                    if ($data['app_type'] == 1 or $data['app_type'] == 2) {  // 2 = Foreign
                        $returnData['sub_department_id'] = 3;
                    } elseif ($data['app_type'] == 3) {// 2 = Local
                        $returnData['sub_department_id'] = 4;
                    }
                }
                break;

            default:
                // default value
                break;
        }
        return $returnData;
    }

    public static function ccEmail()
    {
        return Configuration::where('caption', 'CC_EMAIL')->pluck('value');
    }

    public static function getRemainingDay($application_date, $holidays, $max_processing_day = 0)
    {
        // application start date is when application is application submit date
        $applicationStartDate = new DateTime(date('Y-m-d', strtotime($application_date)));
        // The last date of application approval is available
        // by adding the submit date of the application
        // to the day of max_processing_time from process type table.
        $end_dat_of_application_approval = new DateTime(date('Y-m-d', strtotime($application_date)));
        $end_dat_of_application_approval = $end_dat_of_application_approval->modify('+' . $max_processing_day . ' day');

        $today = new DateTime(date('Y-m-d'));
        if ($today > $end_dat_of_application_approval) {
            // if today is greater then $end_dat_of_application_approval then return 'No Remaining day'
            return '0';
        } elseif ($end_dat_of_application_approval > $today) {
            // difference between submit date and end date of application approval
            $applicationDeadline = $end_dat_of_application_approval->diff($today);
            $remainingDay = $applicationDeadline->days; // get remaining day
            if ($remainingDay == 0) {
                return 'no remaining day';
            } else {
                $interval = new DateInterval('P1D');
                $daterange = new DatePeriod($applicationStartDate, $interval, $end_dat_of_application_approval);
                foreach ($daterange as $date) {
                    if ($date->format('D') == 'Fri' || $date->format('D') == 'Sat' || in_array($date->format('Y-m-d'), $holidays)) {
                        // if this $date is friday or saturday or holiday then increase 1 remaining day
                        $remainingDay++;
                    }
                    // this condition increase 2 remaining day for 1 $date, if this $date is friday and in holiday.!!!!
                    // this is used also in holidayAndOffDay() function
//                    elseif (in_array($date->format('Y-m-d'), $holidays)){
//                        $remainingDay++;
//                    }
                }
            }
        } else {
            return 'N/A';
        }
        return $remainingDay;
    }

    public static function getAgendaWiseApplicationData($agenda_name, $agenda_type, $meeting_id, $process_type_id)
    {
        switch ($process_type_id) {
            case 2: //work permit new
                $applicationData = Agenda::leftJoin('process_list_board_meeting', 'process_list_board_meeting.agenda_id', '=', 'agenda.id')
                    ->leftJoin('process_list', 'process_list.id', '=', 'process_list_board_meeting.process_id')
                    ->leftJoin('wp_apps', 'process_list.ref_id', '=', 'wp_apps.id')
//                    ->leftJoin('attachment_list', 'app_documents.doc_info_id', '=', 'attachment_list.id')
                    ->leftjoin('app_documents', function ($on) {
                        $on->on('process_list.ref_id', '=', 'app_documents.ref_id')
                            ->on('process_list.process_type_id', '=', 'app_documents.process_type_id', 'and');
                    })
                    ->leftJoin('attachment_list', 'app_documents.doc_info_id', '=', 'attachment_list.id')
                    ->select('process_list_board_meeting.process_id', 'process_list_board_meeting.basic_salary_from_dd',
                        'process_list_board_meeting.process_desc_from_dd', 'process_list.ref_id', 'process_list.company_id', 'process_list.process_desc',
                        'process_list.tracking_no', 'wp_apps.*',
                        DB::raw("group_concat(attachment_list.doc_name SEPARATOR '@@') as DocInfo"),
                        DB::raw("group_concat(attachment_list.short_note SEPARATOR '@@') as DocInfoShortName"))
                    ->where('agenda.name', $agenda_name)
                    ->where('agenda.agenda_type', $agenda_type)
                    ->where('process_list_board_meeting.board_meeting_id', $meeting_id)
                    ->groupBy('tracking_no')
                    ->get();
                break;
            case 3: //work permit Extension

                $applicationData = Agenda::leftJoin('process_list_board_meeting', 'process_list_board_meeting.agenda_id', '=', 'agenda.id')
                    ->leftJoin('process_list', 'process_list.id', '=', 'process_list_board_meeting.process_id')
                    ->leftJoin('wpe_apps', 'process_list.ref_id', '=', 'wpe_apps.id')
                    ->leftjoin('app_documents', function ($on) {
                        $on->on('process_list.ref_id', '=', 'app_documents.ref_id')
                            ->on('process_list.process_type_id', '=', 'app_documents.process_type_id', 'and');
                    })
                    ->leftJoin('attachment_list', 'app_documents.doc_info_id', '=', 'attachment_list.id')
                    ->select('process_list_board_meeting.process_id', 'process_list_board_meeting.basic_salary_from_dd',
                        'process_list.ref_id', 'process_list.company_id', 'process_list.process_desc',
                        'process_list_board_meeting.process_desc_from_dd', 'process_list.tracking_no', 'wpe_apps.*',
                        DB::raw("group_concat(attachment_list.doc_name SEPARATOR '@@') as DocInfo"),
                        DB::raw("group_concat(attachment_list.short_note SEPARATOR '@@') as DocInfoShortName"))
                    ->where('agenda.name', $agenda_name)
                    ->where('agenda.agenda_type', $agenda_type)
                    ->where('process_list_board_meeting.board_meeting_id', $meeting_id)
                    ->groupBy('tracking_no')
                    ->get();
                break;
            case 4: //work permit Amendment

                $applicationData = Agenda::leftJoin('process_list_board_meeting', 'process_list_board_meeting.agenda_id', '=', 'agenda.id')
                    ->leftJoin('process_list', 'process_list.id', '=', 'process_list_board_meeting.process_id')
                    ->leftJoin('wpa_apps', 'process_list.ref_id', '=', 'wpa_apps.id')
                    ->leftjoin('app_documents', function ($on) {
                        $on->on('process_list.ref_id', '=', 'app_documents.ref_id')
                            ->on('process_list.process_type_id', '=', 'app_documents.process_type_id', 'and');
                    })
                    ->leftJoin('attachment_list', 'app_documents.doc_info_id', '=', 'attachment_list.id')
                    ->select('process_list_board_meeting.process_id', 'process_list_board_meeting.basic_salary_from_dd',
                        'process_list_board_meeting.process_desc_from_dd', 'process_list.ref_id', 'process_list.company_id',
                        'process_list.process_desc', 'process_list.tracking_no', 'wpa_apps.*',
                        DB::raw("group_concat(attachment_list.doc_name SEPARATOR '@@') as DocInfo"),
                        DB::raw("group_concat(attachment_list.short_note SEPARATOR '@@') as DocInfoShortName"))
                    ->where('agenda.name', $agenda_name)
                    ->where('agenda.agenda_type', $agenda_type)
                    ->where('process_list_board_meeting.board_meeting_id', $meeting_id)
                    ->groupBy('tracking_no')
                    ->get();

                break;
            case 5: //work permit Cancellation
                $applicationData = Agenda::leftJoin('process_list_board_meeting', 'process_list_board_meeting.agenda_id', '=', 'agenda.id')
                    ->leftJoin('process_list', 'process_list.id', '=', 'process_list_board_meeting.process_id')
                    ->leftJoin('wpc_apps', 'process_list.ref_id', '=', 'wpc_apps.id')
                    ->leftjoin('app_documents', function ($on) {
                        $on->on('process_list.ref_id', '=', 'app_documents.ref_id')
                            ->on('process_list.process_type_id', '=', 'app_documents.process_type_id', 'and');
                    })
                    ->leftJoin('attachment_list', 'app_documents.doc_info_id', '=', 'attachment_list.id')
                    ->select('process_list_board_meeting.process_id', 'process_list_board_meeting.basic_salary_from_dd',
                        'process_list_board_meeting.process_desc_from_dd', 'process_list.ref_id',
                        'process_list.company_id', 'process_list.process_desc', 'process_list.tracking_no', 'wpc_apps.*',
                        DB::raw("group_concat(attachment_list.doc_name SEPARATOR '@@') as DocInfo"),
                        DB::raw("group_concat(attachment_list.short_note SEPARATOR '@@') as DocInfoShortName"))
                    ->where('agenda.name', $agenda_name)
                    ->where('agenda.agenda_type', $agenda_type)
                    ->where('process_list_board_meeting.board_meeting_id', $meeting_id)
                    ->groupBy('tracking_no')
                    ->get();
                break;
            case 6: //Office Permission New
                $applicationData = Agenda::leftJoin('process_list_board_meeting', 'process_list_board_meeting.agenda_id', '=', 'agenda.id')
                    ->leftJoin('process_list', 'process_list.id', '=', 'process_list_board_meeting.process_id')
                    ->leftJoin('opn_apps', 'process_list.ref_id', '=', 'opn_apps.id')
                    ->leftjoin('app_documents', function ($on) {
                        $on->on('process_list.ref_id', '=', 'app_documents.ref_id')
                            ->on('process_list.process_type_id', '=', 'app_documents.process_type_id', 'and');
                    })
                    ->leftJoin('attachment_list', 'app_documents.doc_info_id', '=', 'attachment_list.id')
                    ->select('process_list_board_meeting.process_id', 'process_list_board_meeting.basic_salary_from_dd',
                        'process_list_board_meeting.process_desc_from_dd', 'process_list.ref_id', 'process_list.company_id', 'process_list.process_desc',
                        'process_list.tracking_no', 'opn_apps.*',
                        DB::raw("group_concat(attachment_list.doc_name SEPARATOR '@@') as DocInfo"),
                        DB::raw("group_concat(attachment_list.short_note SEPARATOR '@@') as DocInfoShortName")
                    )
                    ->where('agenda.name', $agenda_name)
                    ->where('agenda.agenda_type', $agenda_type)
                    ->where('process_list_board_meeting.board_meeting_id', $meeting_id)
                    ->groupBy('tracking_no')
                    ->get();
                break;
            case 7: //Office Permission Extension
                $applicationData = Agenda::leftJoin('process_list_board_meeting', 'process_list_board_meeting.agenda_id', '=', 'agenda.id')
                    ->leftJoin('process_list', 'process_list.id', '=', 'process_list_board_meeting.process_id')
                    ->leftJoin('ope_apps', 'process_list.ref_id', '=', 'ope_apps.id')
                    ->leftjoin('app_documents', function ($on) {
                        $on->on('process_list.ref_id', '=', 'app_documents.ref_id')
                            ->on('process_list.process_type_id', '=', 'app_documents.process_type_id', 'and');
                    })
                    ->leftJoin('attachment_list', 'app_documents.doc_info_id', '=', 'attachment_list.id')
                    ->select('process_list_board_meeting.process_id', 'process_list_board_meeting.basic_salary_from_dd',
                        'process_list_board_meeting.process_desc_from_dd', 'process_list.ref_id', 'process_list.company_id', 'process_list.process_desc',
                        'process_list.tracking_no', 'ope_apps.*',
                        DB::raw("group_concat(attachment_list.doc_name SEPARATOR '@@') as DocInfo"),
                        DB::raw("group_concat(attachment_list.short_note SEPARATOR '@@') as DocInfoShortName"))
                    ->where('agenda.name', $agenda_name)
                    ->where('agenda.agenda_type', $agenda_type)
                    ->where('process_list_board_meeting.board_meeting_id', $meeting_id)
                    ->groupBy('tracking_no')
                    ->get();
                break;
            case 8: //Office Permission Amendment
                $applicationData = Agenda::leftJoin('process_list_board_meeting', 'process_list_board_meeting.agenda_id', '=', 'agenda.id')
                    ->leftJoin('process_list', 'process_list.id', '=', 'process_list_board_meeting.process_id')
                    ->leftJoin('opa_apps', 'process_list.ref_id', '=', 'opa_apps.id')
                    ->leftjoin('app_documents', function ($on) {
                        $on->on('process_list.ref_id', '=', 'app_documents.ref_id')
                            ->on('process_list.process_type_id', '=', 'app_documents.process_type_id', 'and');
                    })
                    ->leftJoin('attachment_list', 'app_documents.doc_info_id', '=', 'attachment_list.id')
                    ->select('process_list_board_meeting.process_id', 'process_list_board_meeting.basic_salary_from_dd',
                        'process_list_board_meeting.process_desc_from_dd', 'process_list.ref_id', 'process_list.company_id', 'process_list.process_desc',
                        'process_list.tracking_no', 'opa_apps.*',
                        DB::raw("group_concat(attachment_list.doc_name SEPARATOR '@@') as DocInfo"),
                        DB::raw("group_concat(attachment_list.short_note SEPARATOR '@@') as DocInfoShortName"))
                    ->where('agenda.name', $agenda_name)
                    ->where('agenda.agenda_type', $agenda_type)
                    ->where('process_list_board_meeting.board_meeting_id', $meeting_id)
                    ->groupBy('tracking_no')
                    ->get();
                break;

            case 9: //Office Permission Cancellation
                $applicationData = Agenda::leftJoin('process_list_board_meeting', 'process_list_board_meeting.agenda_id', '=', 'agenda.id')
                    ->leftJoin('process_list', 'process_list.id', '=', 'process_list_board_meeting.process_id')
                    ->leftJoin('opc_apps', 'process_list.ref_id', '=', 'opc_apps.id')
                    ->leftjoin('app_documents', function ($on) {
                        $on->on('process_list.ref_id', '=', 'app_documents.ref_id')
                            ->on('process_list.process_type_id', '=', 'app_documents.process_type_id', 'and');
                    })
                    ->leftJoin('attachment_list', 'app_documents.doc_info_id', '=', 'attachment_list.id')
                    ->select('process_list_board_meeting.process_id', 'process_list_board_meeting.basic_salary_from_dd',
                        'process_list_board_meeting.process_desc_from_dd', 'process_list.ref_id', 'process_list.company_id', 'process_list.process_desc',
                        'process_list.tracking_no', 'opc_apps.*',
                        DB::raw("group_concat(attachment_list.doc_name SEPARATOR '@@') as DocInfo"),
                        DB::raw("group_concat(attachment_list.short_note SEPARATOR '@@') as DocInfoShortName"))
                    ->where('agenda.name', $agenda_name)
                    ->where('agenda.agenda_type', $agenda_type)
                    ->where('process_list_board_meeting.board_meeting_id', $meeting_id)
                    ->groupBy('tracking_no')
                    ->get();
                break;

            default:
                $applicationData = [];
                break;
        }
        return $applicationData;
    }

    public static function getAgendaWiseBasicInfo($company_id)
    {
        $getCompanyData = BasicInformation::where('company_id', $company_id)->where('is_approved', 1)->first();
        return $getCompanyData;

    }

    public static function getAgendaWiseWorkPermitNew($ref_app_tracking_no, $tableName)
    {
        $getWpnInfo = ProcessList::where('tracking_no', $ref_app_tracking_no)
            ->leftJoin($tableName, 'process_list.ref_id', '=', $tableName . '.id')
//            ->where('process_list.process_type_id', 2)
            ->first();
        return $getWpnInfo;

    }

    public static function getDocInfo($ref_app_tracking_no, $tableName)
    {
        $getWpnInfo = ProcessList::where('tracking_no', $ref_app_tracking_no)
            ->leftJoin($tableName, 'process_list.ref_id', '=', $tableName . '.id')
//            ->where('process_list.process_type_id', 2)
            ->first();
        return $getWpnInfo;

    }

    public static function getAgendaWiseApplicationDataOfMeetingMinutes($agenda_name, $agenda_type, $meeting_id, $process_type_id)
    {
        switch ($process_type_id) {
            case 2: //work permit new
                $applicationData = Agenda::leftJoin('process_list_board_meeting', 'process_list_board_meeting.agenda_id', '=', 'agenda.id')
                    ->leftJoin('board_meeting_process_status', 'process_list_board_meeting.bm_status_id', '=', 'board_meeting_process_status.id')
                    ->leftJoin('process_list', 'process_list.id', '=', 'process_list_board_meeting.process_id')
                    ->leftJoin('wp_apps', 'process_list.ref_id', '=', 'wp_apps.id')
                    ->select('process_list_board_meeting.process_id', 'process_list_board_meeting.bm_remarks', 'process_list.company_id', 'process_list.tracking_no',
                        'board_meeting_process_status.status_name as bm_status_name', 'process_list.ref_id', 'wp_apps.*')
                    ->where('agenda.name', $agenda_name)
                    ->where('agenda.agenda_type', $agenda_type)
                    ->where('process_list_board_meeting.board_meeting_id', $meeting_id)
                    ->groupBy('tracking_no')
                    ->get();

                break;
            case 3: //work permit Extension
                $applicationData = Agenda::leftJoin('process_list_board_meeting', 'process_list_board_meeting.agenda_id', '=', 'agenda.id')
                    ->leftJoin('board_meeting_process_status', 'process_list_board_meeting.bm_status_id', '=', 'board_meeting_process_status.id')
                    ->leftJoin('process_list', 'process_list.id', '=', 'process_list_board_meeting.process_id')
                    ->leftJoin('wpe_apps', 'process_list.ref_id', '=', 'wpe_apps.id')
                    ->select('process_list_board_meeting.process_id', 'process_list_board_meeting.bm_remarks', 'process_list.company_id', 'process_list.tracking_no',
                        'board_meeting_process_status.status_name as bm_status_name', 'process_list.ref_id', 'wpe_apps.*')
                    ->where('agenda.name', $agenda_name)
                    ->where('agenda.agenda_type', $agenda_type)
                    ->where('process_list_board_meeting.board_meeting_id', $meeting_id)
                    ->groupBy('tracking_no')
                    ->get();
                break;
            case 4: //work permit Amendment
                $applicationData = Agenda::leftJoin('process_list_board_meeting', 'process_list_board_meeting.agenda_id', '=', 'agenda.id')
                    ->leftJoin('board_meeting_process_status', 'process_list_board_meeting.bm_status_id', '=', 'board_meeting_process_status.id')
                    ->leftJoin('process_list', 'process_list.id', '=', 'process_list_board_meeting.process_id')
                    ->leftJoin('wpa_apps', 'process_list.ref_id', '=', 'wpa_apps.id')
                    ->select('process_list_board_meeting.process_id', 'process_list_board_meeting.bm_remarks', 'process_list.company_id', 'process_list.tracking_no',
                        'board_meeting_process_status.status_name as bm_status_name', 'process_list.ref_id', 'wpa_apps.*')
                    ->where('agenda.name', $agenda_name)
                    ->where('agenda.agenda_type', $agenda_type)
                    ->where('process_list_board_meeting.board_meeting_id', $meeting_id)
                    ->groupBy('tracking_no')
                    ->get();
                break;
            case 5: //work permit Cancellation
                $applicationData = Agenda::leftJoin('process_list_board_meeting', 'process_list_board_meeting.agenda_id', '=', 'agenda.id')
                    ->leftJoin('board_meeting_process_status', 'process_list_board_meeting.bm_status_id', '=', 'board_meeting_process_status.id')
                    ->leftJoin('process_list', 'process_list.id', '=', 'process_list_board_meeting.process_id')
                    ->leftJoin('wpc_apps', 'process_list.ref_id', '=', 'wpc_apps.id')
                    ->select('process_list_board_meeting.process_id', 'process_list_board_meeting.bm_remarks', 'process_list.company_id', 'process_list.tracking_no',
                        'board_meeting_process_status.status_name as bm_status_name', 'process_list.ref_id', 'wpc_apps.*')
                    ->where('agenda.name', $agenda_name)
                    ->where('agenda.agenda_type', $agenda_type)
                    ->where('process_list_board_meeting.board_meeting_id', $meeting_id)
                    ->groupBy('tracking_no')
                    ->get();
                break;

            case 6: //Office Permission New
                $applicationData = Agenda::leftJoin('process_list_board_meeting', 'process_list_board_meeting.agenda_id', '=', 'agenda.id')
                    ->leftJoin('board_meeting_process_status', 'process_list_board_meeting.bm_status_id', '=', 'board_meeting_process_status.id')
                    ->leftJoin('process_list', 'process_list.id', '=', 'process_list_board_meeting.process_id')
                    ->leftJoin('opn_apps', 'process_list.ref_id', '=', 'opn_apps.id')
                    ->select('process_list_board_meeting.process_id', 'process_list_board_meeting.bm_remarks', 'process_list.company_id', 'process_list.tracking_no',
                        'board_meeting_process_status.status_name as bm_status_name', 'process_list.ref_id', 'opn_apps.*')
                    ->where('agenda.name', $agenda_name)
                    ->where('agenda.agenda_type', $agenda_type)
                    ->where('process_list_board_meeting.board_meeting_id', $meeting_id)
                    ->groupBy('tracking_no')
                    ->get();
                break;

            case 7: //Office Permission Extension
                $applicationData = Agenda::leftJoin('process_list_board_meeting', 'process_list_board_meeting.agenda_id', '=', 'agenda.id')
                    ->leftJoin('board_meeting_process_status', 'process_list_board_meeting.bm_status_id', '=', 'board_meeting_process_status.id')
                    ->leftJoin('process_list', 'process_list.id', '=', 'process_list_board_meeting.process_id')
                    ->leftJoin('ope_apps', 'process_list.ref_id', '=', 'ope_apps.id')
                    ->select('process_list_board_meeting.process_id', 'process_list_board_meeting.bm_remarks', 'process_list.company_id', 'process_list.tracking_no',
                        'board_meeting_process_status.status_name as bm_status_name', 'process_list.ref_id', 'ope_apps.*')
                    ->where('agenda.name', $agenda_name)
                    ->where('agenda.agenda_type', $agenda_type)
                    ->where('process_list_board_meeting.board_meeting_id', $meeting_id)
                    ->groupBy('tracking_no')
                    ->get();
                break;

            case 8: //Office Permission Amendment
                $applicationData = Agenda::leftJoin('process_list_board_meeting', 'process_list_board_meeting.agenda_id', '=', 'agenda.id')
                    ->leftJoin('board_meeting_process_status', 'process_list_board_meeting.bm_status_id', '=', 'board_meeting_process_status.id')
                    ->leftJoin('process_list', 'process_list.id', '=', 'process_list_board_meeting.process_id')
                    ->leftJoin('opa_apps', 'process_list.ref_id', '=', 'opa_apps.id')
                    ->select('process_list_board_meeting.process_id', 'process_list_board_meeting.bm_remarks', 'process_list.company_id', 'process_list.tracking_no',
                        'board_meeting_process_status.status_name as bm_status_name', 'process_list.ref_id', 'opa_apps.*')
                    ->where('agenda.name', $agenda_name)
                    ->where('agenda.agenda_type', $agenda_type)
                    ->where('process_list_board_meeting.board_meeting_id', $meeting_id)
                    ->groupBy('tracking_no')
                    ->get();
                break;

            case 9: //Office Permission Cancellation
                $applicationData = Agenda::leftJoin('process_list_board_meeting', 'process_list_board_meeting.agenda_id', '=', 'agenda.id')
                    ->leftJoin('board_meeting_process_status', 'process_list_board_meeting.bm_status_id', '=', 'board_meeting_process_status.id')
                    ->leftJoin('process_list', 'process_list.id', '=', 'process_list_board_meeting.process_id')
                    ->leftJoin('opc_apps', 'process_list.ref_id', '=', 'opc_apps.id')
                    ->select('process_list_board_meeting.process_id', 'process_list_board_meeting.bm_remarks', 'process_list.company_id', 'process_list.tracking_no',
                        'board_meeting_process_status.status_name as bm_status_name', 'process_list.ref_id', 'opc_apps.*')
                    ->where('agenda.name', $agenda_name)
                    ->where('agenda.agenda_type', $agenda_type)
                    ->where('process_list_board_meeting.board_meeting_id', $meeting_id)
                    ->groupBy('tracking_no')
                    ->get();
                break;

            default:
                $applicationData = [];
                break;


        }
        return $applicationData;

    }

    public static function getRemmitanceForMeetingMinutes($process_id)
    {
        $applicatonData = ProcessList::leftJoin('ra_apps', 'process_list.ref_id', '=', 'ra_apps.id')
            ->leftJoin('process_list_board_meeting', 'process_list_board_meeting.process_id', '=', 'process_list.id')
            ->leftJoin('ra_bida_reg_info', 'ra_apps.id', '=', 'ra_bida_reg_info.app_id')
            ->leftJoin('ra_brief_desc_of_tech_service as rbdts', 'ra_apps.id', '=', 'rbdts.app_id')
            ->leftJoin('ra_imported_machinery as rim', 'ra_apps.id', '=', 'rim.app_id')
            ->leftjoin('app_documents', function ($on) {
                $on->on('process_list.ref_id', '=', 'app_documents.ref_id')
                    ->on('process_list.process_type_id', '=', 'app_documents.process_type_id', 'and');
            })
            ->where('process_list.process_type_id', 11)
            ->where('process_list.id', $process_id)
            ->select('ra_apps.*', 'process_list_board_meeting.bm_remarks',
                DB::raw("group_concat(DISTINCT rim.cnf_value  SEPARATOR '@@') as cnf_value"),
                DB::raw("group_concat(DISTINCT rim.cnf_value  SEPARATOR '@@') as import_year_from"),
                DB::raw("group_concat(DISTINCT rim.cnf_value  SEPARATOR '@@') as import_year_to"),
                DB::raw("group_concat(DISTINCT app_documents.doc_name SEPARATOR '@@') as DocInfo")
            )
            ->first();

        return $applicatonData;
    }

    public static function getRemittanceData($process_id)
    {
        $applicatonData = ProcessList::leftJoin('ra_apps', 'process_list.ref_id', '=', 'ra_apps.id')
            ->leftJoin('ra_bida_reg_info', 'ra_apps.id', '=', 'ra_bida_reg_info.app_id')
            ->leftJoin('ra_brief_desc_of_tech_service as rbdts', 'ra_apps.id', '=', 'rbdts.app_id')
            ->leftJoin('ra_imported_machinery as rim', 'ra_apps.id', '=', 'rim.app_id')
            ->leftJoin('ra_brief_statement as rbs', 'ra_apps.id', '=', 'rbs.app_id')
            ->leftjoin('app_documents', function ($on) {
                $on->on('process_list.ref_id', '=', 'app_documents.ref_id')
                    ->on('process_list.process_type_id', '=', 'app_documents.process_type_id', 'and');
            })
            ->leftJoin('attachment_list', 'app_documents.doc_info_id', '=', 'attachment_list.id')
            ->where('process_list.process_type_id', 11)
            ->where('process_list.id', $process_id)
            ->select('ra_apps.*',
                DB::raw("group_concat( DISTINCT ra_bida_reg_info.registration_no SEPARATOR '@@') as reg_info_reg_no"),
                DB::raw("group_concat(DISTINCT ra_bida_reg_info.registration_date SEPARATOR '@@') as reg_info_date"),
                DB::raw("group_concat(DISTINCT rbdts.brief_description  SEPARATOR '@@') as brief_description"),
                DB::raw("group_concat(DISTINCT rim.cnf_value  SEPARATOR '@@') as cnf_value"),
                DB::raw("group_concat(DISTINCT rim.cnf_value  SEPARATOR '@@') as import_year_from"),
                DB::raw("group_concat(DISTINCT rim.cnf_value  SEPARATOR '@@') as import_year_to"),
                DB::raw("group_concat(DISTINCT rbs.brief_statement  SEPARATOR '@@') as brief_statement"),
                DB::raw("group_concat(attachment_list.doc_name SEPARATOR '@@') as DocInfo"),
                DB::raw("group_concat(attachment_list.short_note SEPARATOR '@@') as DocInfoShortName")
            )
            ->first();
        return $applicatonData;
    }

    public static function StatementOfRemittance($process_id)
    {
        $statementOfRemittances = ProcessList::leftJoin('ra_apps', 'process_list.ref_id', '=', 'ra_apps.id')
            ->leftJoin('ra_statement_of_remittance', 'ra_apps.id', '=', 'ra_statement_of_remittance.app_id')
            ->where('process_list.process_type_id', 11)
            ->where('process_list.id', $process_id)
            ->get(['ra_statement_of_remittance.*']);
        return $statementOfRemittances;
    }

    public static function convertToBdtAmount($amount = 0)
    {
        // Check is the amount integer or float or decimal
        // Replace comma if exists

        $tmp = explode('.', $amount);  // for float or double values
        $strMoney = '';
        $amount = $tmp[0];
        $strMoney .= substr($amount, -3, 3);
        $amount = substr($amount, 0, -3);
        while (strlen($amount) > 0) {
            $strMoney = substr($amount, -2, 2) . ',' . $strMoney;
            $amount = substr($amount, 0, -2);
        }

        if (isset($tmp[1]))         // if float and double add the decimal digits here.
        {
            return $strMoney . '.' . $tmp[1];
        }
        return $strMoney;
    }

    public static function convertToMillionAmount($amount = 0)
    {
        if ($amount != "") {
            $amount = str_replace(',', '', trim($amount));
            $decimal_point = '00';

            if (strpos($amount, '.') !== false) {
                $separate = explode('.', $amount);
                $decimal_point = $separate[1];
            }

            return number_format($amount, strlen($decimal_point));
        }

        return 0;
    }

    public static function convert_number_to_words($number)
    {
        $common = new CommonFunction;
        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = array(
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'fourty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
            100 => 'hundred',
            1000 => 'thousand',
            1000000 => 'million',
            1000000000 => 'billion',
            1000000000000 => 'trillion',
            1000000000000000 => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX, E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . $common->convert_number_to_words(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int)($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . $common->convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int)($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $common->convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= $common->convert_number_to_words($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string)$fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }

    public static function convert_number_to_words_bangla($number)
    {
        $common = new CommonFunction;
        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = array(
            0 => 'zero',
            1 => 'এক ',
            2 => 'দুই ',
            3 => 'তিন ',
            4 => 'চার ',
            5 => 'পাঁচ ',
            6 => 'ছয়',
            7 => 'সাত',
            8 => 'আট ',
            9 => 'নয়',
            10 => 'দশ ',
            11 => 'এগারো',
            12 => 'বারো',
            13 => 'তেরো',
            14 => 'চৌদ্দ',
            15 => 'পনেরো',
            16 => 'ষোল',
            17 => 'সতেরো',
            18 => 'আঠারো',
            19 => 'ঊনিশ',
            20 => 'বিশ',
            21 => 'একুশ',
            22 => 'বাইশ',
            23 => 'তেইশ',
            24 => 'চব্বিশ',
            25 => 'পঁচিশ',
            26 => 'ছাব্বিশ',
            27 => 'সাতাশ',
            28 => 'আটাশ',
            29 => 'ঊনত্রিশ',
            30 => 'ত্রিশ',
            31 => 'একত্রিশ',
            35 => 'পঁয়ত্রিশ',
            40 => 'বাইশ',
            50 => 'তেইশ',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
            100 => 'hundred',
            1000 => 'thousand',
            1000000 => 'million',
            1000000000 => 'billion',
            1000000000000 => 'trillion',
            1000000000000000 => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX, E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . $common->convert_number_to_words(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int)($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . $common->convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int)($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $common->convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= $common->convert_number_to_words($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string)$fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }

    public static function getUrlByTrackingNo($trackingNo)
    {
        $data = ProcessList::leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
            ->where('tracking_no', $trackingNo)
            ->first(['process_list.ref_id',
                'process_list.process_type_id',
                'process_type.form_url',
                'process_type.form_id'
            ]);

        if (count($data) > 0) {
            $redirect_path = CommonFunction::getAppRedirectPathByJson($data->form_id);
            $url = url('process/' . $data->form_url . '/' . $redirect_path['view'] . '/' . Encryption::encodeId($data->ref_id) . '/' . Encryption::encodeId($data->process_type_id));
        } else {
            $url = '#';
        }
        return $url;
    }

    public static function getCertificateByTrackingNo($trackingNo)
    {
        $data = ProcessList::leftJoin('pdf_print_requests_queue', function ($join) {
            $join->on('pdf_print_requests_queue.process_type_id', '=', 'process_list.process_type_id')
                ->on('pdf_print_requests_queue.app_id', '=', 'process_list.ref_id');
        })
            ->where('process_list.tracking_no', $trackingNo)
            ->first(['pdf_print_requests_queue.certificate_link']);
        if (empty($data->certificate_link)) {
            $output = '<span class="badge badge-warning">Certificate not found</span>';
        } else {
            $output = '<a href="' . $data->certificate_link . '" target="_blank" class="btn btn-success btn-sm">View Certificate</a>';
        }
        return $output;
    }

    public static function getFullEntityType($entityId)
    {
        $entity = RjscNrEntityType::where('entity_type_id', $entityId)->first(['name']);

        return isset($entity->name) ? $entity->name : 'N/A';
    }

    static public function getFileStatus($fileListId, $applicationId)
    {
        $fileStatus = RjscNrSubmitForms::where('app_id', $applicationId)->where('ref_id', $fileListId)->first();
        if (count($fileStatus) > 0) {

            if ($fileStatus->file == "" || $fileStatus->file == "NULL" || $fileStatus->file == null) {
                return false;
            } else {
                return true;
            }

        } else {
            return false;
        }
    }

    static public function getFileUploadedStatus($fileListId, $applicationId)
    {
        $fileStatus = RjscNrSubmitForms::where('app_id', $applicationId)->where('ref_id', $fileListId)->first();
        if (count($fileStatus) > 0) {

            if ($fileStatus->doc == "" || $fileStatus->doc == "NULL" || $fileStatus->doc == null) {
                return false;
            } else {
                return true;
            }

        } else {
            return false;
        }
    }

    // For checking all generated pdf submitted or not
    static public function checkFileSubmission($applicationId)
    {
        $submitStatus = NewReg::where('id', $applicationId)->where('doc_status', '0')->first();
        if (count($submitStatus) != 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function isJson($string)
    {

        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    public static function checkFeedbackItem()
    {
        if (CommonFunction::getUserType() == '5x505') {
            $companyIds = CommonFunction::getUserCompanyWithZero();
            $maximumPendingFeedback = DB::table('configuration')->where('caption', 'FeedbackItem')->first()->value;

            $getTotalPending = ProcessList::where('is_feedback', 0)//feedback pending application
            ->where('process_list.status_id', '=', 25)
                ->whereIn('process_list.company_id', $companyIds)
                ->count();
            if ($maximumPendingFeedback < $getTotalPending) {
                return false;
            } else {
                return true;
            }
        }
        return true;
    }

    public static function asciiCharCheck($value)
    {
        if (mb_detect_encoding($value, 'ASCII', true)) {
            return true; // no ascii not found
        } else {
            return false;
//            Session::flash('error', 'non-ASCII Characters in main_business_objective [BI-1023]');
//            return redirect('licence-applications/company-registration/add#step2');

        }
    }


    public static function checkUTF($value)
    {
        if (mb_detect_encoding($value, 'UTF-8', true)) {
            return true; // no ascii not found
        } else {
            return false;
//            Session::flash('error', 'non-ASCII Characters in main_business_objective [BI-1023]');
//            return redirect('licence-applications/company-registration/add#step2');

        }
    }

    //get meting no and meting date.....
    public static function getMeetingInfo($process_id)
    {
        return BoardMeting::leftJoin('process_list_board_meeting', 'process_list_board_meeting.board_meeting_id', '=',
            'board_meting.id')
            ->where('process_list_board_meeting.process_id', $process_id)
            ->orderBy('process_list_board_meeting.id', 'desc')
            ->first(['board_meting.meting_number', 'board_meting.meting_date']);
    }

    public static function getBasicCompanyInfo($company_id)
    {
        return ProcessList::leftjoin('ea_apps as apps', 'apps.id', '=', 'process_list.ref_id')
            ->leftJoin('department', 'department.id', '=', 'process_list.department_id')
            ->leftJoin('country_info', 'country_info.id', '=', 'apps.country_of_origin_id')
            ->leftJoin('ea_organization_type', 'ea_organization_type.id', '=', 'apps.organization_type_id')
            ->leftJoin('ea_ownership_status', 'ea_ownership_status.id', '=', 'apps.ownership_status_id')
            ->leftJoin('ea_organization_status', 'ea_organization_status.id', '=', 'apps.organization_status_id')
            ->leftJoin('sector_info', 'sector_info.id', '=', 'apps.business_sector_id')
            ->leftJoin('sec_sub_sector_list', 'sec_sub_sector_list.id', '=', 'apps.business_sub_sector_id')
            ->where('process_list.process_type_id', 100)
            ->where('process_list.status_id', 25)
            ->where('process_list.company_id', $company_id)
            ->first([
                'apps.company_name',
                'apps.company_name_bn',
                'department.name as department',
                'country_info.nicename',
                'apps.organization_type_id',
                'apps.organization_type_other',
                'ea_organization_type.name as organization_type_name',
                'apps.ownership_status_id',
                'apps.ownership_status_other',
                'ea_ownership_status.name as ownership_status_name',
                'ea_organization_status.name as organization_status_name',
                'apps.business_sector_id',
                'apps.business_sector_others',
                'sector_info.name as sector_name',
                'apps.business_sub_sector_id',
                'apps.business_sub_sector_others',
                'sec_sub_sector_list.name as sub_sector_name',
                'apps.major_activities'
            ]);
    }

    public static function processTypeWiseAgendaApplication($board_meeting_id)
    {
        $board_meeting_id = Encryption::decodeId($board_meeting_id);
        $typeWiseApplication = ProcessListBoardMeting::leftJoin('process_list', 'process_list_board_meeting.process_id', '=', 'process_list.id')
            ->leftJoin('process_type', 'process_list.process_type_id', '=', 'process_type.id')
            ->where('board_meeting_id', $board_meeting_id)
            ->groupBy('process_type_id')
            ->get([DB::raw('count(process_type.id) as no_of_application ,process_type.id process_type_id, process_type.name,process_type.panel')]);;
        return $typeWiseApplication->toArray();
    }

    public static function getGeneralSubmission($process_type_id)
    {
        $process_type_data = ProcessType::where('id', $process_type_id)->first(['submission_conf_json']);
        $decoded_json = json_decode($process_type_data->submission_conf_json, true);
        return $data = $decoded_json['general_submission'];
    }

    public static function getGovtPaySubmission($process_type_id)
    {
        $process_type_data = ProcessType::where('id', $process_type_id)->first(['submission_conf_json']);
        $decoded_json = json_decode($process_type_data->submission_conf_json, true);
        return $decoded_json['payment_govt_fee'];
        //return $data = $decoded_json['payment_govt_fee'];
    }

    public static function getConditionFulfillSubmission($process_type_id)
    {
        $process_type_data = ProcessType::where('id', $process_type_id)->first(['submission_conf_json']);
        $decoded_json = json_decode($process_type_data->submission_conf_json, true);
        return $decoded_json['condition_fulfill'];
        //return $data = $decoded_json['condition_fulfill'];
    }

    public static function getReSubmissionJson($process_type_id, $app_id = 0)
    {

        $process_type_data = ProcessType::where('id', $process_type_id)->first(['resubmission_conf_sql']);
        $decoded_json = json_decode($process_type_data->resubmission_conf_sql, true);

        $requested_sql = str_replace("{app_id}", "$app_id", $decoded_json['general_resubmission']['process_starting_desk_sql']);
        $requested_sql = str_replace("{process_type_id}", "$process_type_id", $requested_sql);

        $sql_result_data = DB::select(DB::raw($requested_sql));

        $data = [
            'process_starting_desk' => (int)$sql_result_data[0]->process_starting_desk,
            'process_starting_status' => $decoded_json['general_resubmission']['process_starting_status'],
        ];

        return $data;
    }

    public static function getPaymentModeCodeMsg($pay_mode_code)
    {
        $returnData = [
            'pay_mode_msg' => 'Not found',
        ];

        $pay_mode = PayMode::where(['pay_mode_code' => trim($pay_mode_code), 'status' => 1])->pluck('pay_mode');
        if (!empty($pay_mode)) {
            $returnData['pay_mode_msg'] = $pay_mode;
        }

        return $returnData;
    }

    public static function getWorkingUserType($requested_company_id)
    {
        return CompanyAssociation::where([
            'user_id' => Auth::user()->id,
            'requested_company_id' => $requested_company_id,
            'request_type' => 'Add',
            'status_id' => 25,
            'status' => 1
        ])->first();
    }

    public static function checkBusinessClassBackdatedData($service_type)
    {
        $company_id = Auth::user()->working_company_id;
        $business_class_app_info = BidaRegistration::leftJoin('process_list', 'br_apps.id', '=', 'process_list.ref_id')
            ->where('process_list.process_type_id', $service_type)
            ->whereNotIn('process_list.status_id', [-1, 5, 6])
            ->where('process_list.company_id', $company_id)
            ->where(function ($query) {
                $query->whereNull('section_id')
                    ->orWhereNull('division_id')
                    ->orWhereNull('group_id')
                    ->orWhereNull('class_id')
                    ->orWhereNull('class_code')
                    ->orWhereNull('sub_class_id');
            })
            ->where('process_list.created_by', Auth::user()->id) // Multiple user for single company
            ->first([
                'br_apps.section_id',
                'br_apps.division_id',
                'br_apps.group_id',
                'br_apps.class_id',
                'br_apps.class_code',
                'process_list.ref_id',
                'process_list.process_type_id',
            ]);

        return $business_class_app_info;
    }

    public static function findCompanyNameWithoutWorkingID($company_name, $company_id)
    {

        $exitingCompany = CompanyInfo::where('company_name', trim($company_name))
            ->where('id', '!=', $company_id)->first(['company_name']);

        if (empty($exitingCompany)) {
            return true;
        }

        return false;
    }

    public static function getCompanyMajorActivities($company_id)
    {
        return ProcessList::leftjoin('ea_apps as apps', 'apps.id', '=', 'process_list.ref_id')
            ->where('process_list.process_type_id', 100)
            ->where('process_list.status_id', 25)
            ->where('process_list.company_id', $company_id)
            ->first([
                'apps.major_activities'
            ]);
    }

    public static function getBasicInfoUrl($company_id)
    {

        $basicAppID = ProcessList::leftjoin('ea_apps', 'ea_apps.id', '=', 'process_list.ref_id')
            ->where('process_list.process_type_id', 100)
            ->where('process_list.status_id', 25)
            ->where('process_list.company_id', $company_id)
            ->first(['process_list.ref_id', 'process_list.process_type_id', 'process_list.department_id', 'ea_apps.applicant_type']);

        if ($basicAppID['applicant_type'] == 'New Company Registration') {
            $BiRoute = 'basic-information/form-stakeholder/' . Encryption::encodeId('NCR') . '/' . Encryption::encodeId($company_id);
        } elseif ($basicAppID['applicant_type'] == 'Existing Company Registration') {
            $BiRoute = 'basic-information/form-stakeholder/' . Encryption::encodeId('ECR') . '/' . Encryption::encodeId($company_id);
        } else {
            $BiRoute = 'basic-information/form-bida/' . Encryption::encodeId('EUBS') . '/' . Encryption::encodeId($company_id);
        }

        if (!empty($company_id)) {
            return $BiRoute;
        }
    }

    public static function getAppRedirectPathByJson($json)
    {
        $openMode = 'edit';
        $form_id = json_decode($json, true);
        $url = (isset($form_id[$openMode]) ? explode('/', trim($form_id[$openMode], "/")) : '');
        $view = ($url[1] == 'edit' ? 'view-app' : 'view'); // view page
        $edit = ($url[1] == 'edit' ? 'edit-app' : 'view'); // edit page
        $array = [
            'view' => $view,
            'edit' => $edit
        ];
        return $array;
    }

    // generate a encrypted link to login to old version
    public static function generateLink()
    {
        $user_type = Auth::user()->user_type;
        $user_full_name = Auth::user()->user_full_name;
        $user_email = Auth::user()->user_email;
        $user_phone = Auth::user()->user_phone;
        $department_id = Auth::user()->department_id;
        $desk_id = explode(',', Auth::user()->desk_id)[0];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $time = date("Y-m-d H:i:s");

        $data = $user_type . "@@" . $user_full_name . "@@" . $user_email . "@@" . $user_phone . "@@" . $department_id . "@@" . $desk_id . "@@" . $user_agent . "@@" . $time;
        $encrypted_data = Encryption::encode($data);
        $link = "https://eservice.bida.gov.bd/login/thirdPartyLoginCheck/" . $encrypted_data;

        echo $link;
    }
    // generate a encrypted link to login to old version
    public static function isEserve()
    {
        $isEserve = Configuration::where('caption', 'IS_ESERVE')->value('value');
        return $isEserve;
    }

    public static function setAccessibleProcessTypeList()
    {
        if (\Illuminate\Support\Facades\Session::has('accessible_process') === false) {
            $accessible_process = CommonFunction::getAccessibleProcessTypeList();
            Session::put('accessible_process', $accessible_process);
        }
    }

    public static function getAccessibleProcessTypeList()
    {
        Auth::setUser(User::find(Auth::user()->id));
        $user_type = Auth::user()->user_type;
        $process_type_array = [];

        switch ($user_type) {
            case '1x101':
            case '2x202':
            case '14x141':
                $process_type_array[] = 102;
                $process_type_array[] = 12;
                $process_type_array[] = 13;
                $process_type_array[] = 14;
                $process_type_array[] = 15;
                $process_type_array[] = 16;
                $process_type_array[] = 11;
                $process_type_array[] = 1;
                $process_type_array[] = 10;
                $process_type_array[] = 17;
                $process_type_array[] = 18;
                $process_type_array[] = 19;
                $process_type_array[] = 20;
                $process_type_array[] = 21;
                $process_type_array[] = 6;
                $process_type_array[] = 7;
                $process_type_array[] = 8;
                $process_type_array[] = 9;
                $process_type_array[] = 2;
                $process_type_array[] = 3;
                $process_type_array[] = 4;
                $process_type_array[] = 5;

                $process_type_array[] = 104;
                $process_type_array[] = 106;
                $process_type_array[] = 107;
                break;
            case '5x505':

                $is_eligibility = CommonFunction::checkEligibility();

                if ($is_eligibility) {
                    $process_type_array[] = 1;
                    $process_type_array[] = 10;
                    $process_type_array[] = 18;
                    $process_type_array[] = 2;
                    $process_type_array[] = 3;
                    $process_type_array[] = 4;
                    $process_type_array[] = 5;
                    $process_type_array[] = 17;

                    $applicantDepartmentId = CommonFunction::getDeptIdByCompanyId(Auth::user()->company_ids);
                    $userSubdeptIds = CommonFunction::getUserSubDepartmentIds();
                    if ($applicantDepartmentId == 2) {
                        $process_type_array[] = 102;
                        $process_type_array[] = 12;
                        $process_type_array[] = 13;
                        $process_type_array[] = 14;
                        $process_type_array[] = 15;
                        $process_type_array[] = 16;
                        if(!in_array('4', $userSubdeptIds)){
                            $process_type_array[] = 21;
                        }
                        
                    } else if ($applicantDepartmentId == 1) {
                        $process_type_array[] = 6;
                        $process_type_array[] = 7;
                        $process_type_array[] = 8;
                        $process_type_array[] = 9;
                        $process_type_array[] = 19;
                        $process_type_array[] = 20;
                    }
                }
                break;
            case '4x404':
                $deskUserdeptIds = CommonFunction::getUserDepartmentIds();
                $deskUserSubdeptIds = CommonFunction::getUserSubDepartmentIds();
                /*
                 * IRC Recommendation New Menu Permission for all desk user
                 */
                if(checkUserDeskNone()){
                    $process_type_array[] = 13;
                    $process_type_array[] = 14;
                    $process_type_array[] = 15;
                    $process_type_array[] = 16;
                }
                
                /*
                 * BIDA Registration New Menu Permission
                 * BIDA Registration Amendment Menu Permission
                 * 
                 */
                if (in_array('3', $deskUserSubdeptIds) || in_array('4', $deskUserSubdeptIds)) {
                    $process_type_array[] = 102;
                    $process_type_array[] = 12;
                }

                // Import Permission Menu Permission
                if (in_array('2', $deskUserdeptIds) && in_array('3', $deskUserSubdeptIds)) {
                    $process_type_array[] = 21;
                }

                // Remittance Menu Permission
                if (in_array('2', $deskUserdeptIds) && in_array('3', $deskUserSubdeptIds)) {
                    $process_type_array[] = 11;
                }

                // Visa Recommendation New Menu Permission
                if (
                    (in_array('1', $deskUserdeptIds) && (in_array('5', $deskUserSubdeptIds) || in_array('6', $deskUserSubdeptIds)))
                    || (in_array('2', $deskUserdeptIds) && (in_array('2', $deskUserSubdeptIds)))
                ) {
                    $process_type_array[] = 1;
                }

                // Visa Recommendation Amendment Menu Permission
                if (
                    (in_array('1', $deskUserdeptIds) && in_array('6', $deskUserSubdeptIds))
                    || (in_array('2', $deskUserdeptIds) && (in_array('2', $deskUserSubdeptIds)))
                ) {
                    $process_type_array[] = 10;
                }

                // VIP Lounge Menu Permission
                if (in_array('1', $deskUserdeptIds) && in_array('6', $deskUserSubdeptIds)) {
                    $process_type_array[] = 17;
                }

                /*
                 * Office Permission New Menu Permission
                 * Office Permission Extension Menu Permission
                 * Office Permission Cancellation Menu Permission
                 */
                if (in_array('5', $deskUserSubdeptIds)) {
                    $process_type_array[] = 6;
                    $process_type_array[] = 7;
                    $process_type_array[] = 9;
                }

                /*
                 * Waiver Condition 7 Menu Permission
                 * Waiver Condition 8 Menu Permission
                 */
                if (in_array('1', $deskUserdeptIds) && in_array('5', $deskUserSubdeptIds)){
                    $process_type_array[] = 19;
                    $process_type_array[] = 20;
                }


                // Office Permission Amendment Menu Permission
                if (in_array('6', $deskUserSubdeptIds)) {
                    $process_type_array[] = 8;
                }

                /*
                 * Work Permit New Menu Permission
                 * Work Permit Extension Menu Permission
                 */
                if (in_array('2', $deskUserSubdeptIds) || in_array('5', $deskUserSubdeptIds)) {
                    $process_type_array[] = 2;
                    $process_type_array[] = 3;
                }

                /*
                 * Work Permit Amendment Menu Permission
                 * Work Permit Cancellation Menu Permission
                 */
                if (in_array('2', $deskUserSubdeptIds) || in_array('6', $deskUserSubdeptIds)) {
                    $process_type_array[] = 4;
                    $process_type_array[] = 5;
                }
                break;


            case '6x606':
                $deskUserdeptIds = CommonFunction::getUserDepartmentIds();
                $deskUserSubdeptIds = CommonFunction::getUserSubDepartmentIds();
                /*
                * Waiver Condition 8 Menu Permission
                */
                if (in_array('1', $deskUserdeptIds) && in_array('5', $deskUserSubdeptIds)){
                    $process_type_array[] = 20;
                }
                break;
        }

        return $process_type_array;
    }

    public static function getAgencyInfo($agency_type)
    {
        if (!empty($agency_type)) {
            return RegulatoryAgency::leftJoin('regulatory_agencies_details', 'regulatory_agencies_details.regulatory_agencies_id', '=', 'regulatory_agencies.id')
                ->where('regulatory_agencies.is_archive', 0)
                ->where('regulatory_agencies.status', 1)
                ->where('regulatory_agencies.agency_type', $agency_type)
                ->groupBy('regulatory_agencies.id')
                ->orderBy('regulatory_agencies.order', 'asc')
                ->get([
                    'regulatory_agencies.id',
                    'regulatory_agencies.name',
                    'regulatory_agencies.description',
                    'regulatory_agencies.url',
                    'regulatory_agencies.contact_name',
                    'regulatory_agencies.designation',
                    'regulatory_agencies.mobile',
                    'regulatory_agencies.phone',
                    'regulatory_agencies.email',
                    'regulatory_agencies.updated_at',
                    DB::raw('group_concat(regulatory_agencies_details.id) as regulatory_agencies_details_ids'),
                ]);
        }
        return false;
    }

    public static function getAgencyDetailsInfo($agency_details_ids)
    {
        if (!empty($agency_details_ids)) {
            $ids = explode(',', $agency_details_ids);
            return RegulatoryAgencyDetails::whereIn('id', $ids)->where('status', 1)->get([
                'id',
                'regulatory_agencies_id',
                'service_name',
                'is_online',
                'method_of_recv_service',
                'who_get_service',
                'documents',
                'fees',
                'updated_at'
            ]);
        }

        return false;
    }

    public static function getOssPidRedirectUrl()
    {
        $osspid = new Osspid(array(
            'client_id' => config('app.osspid_client_id'),
            'client_secret_key' => config('app.osspid_client_secret_key'),
            'osspid_auth_url' => config('app.osspid_auth_url'),
            'callback_url' => config('app.project_root') . '/osspid-callback'
        ));

        return $osspid->getRedirectURL();
    }

    public static function getBasicInformationByCompanyId($companyId)
    {
        if (empty($companyId)) {
            return false;
        }

        return ProcessList::leftjoin('ea_apps', 'ea_apps.id', '=', 'process_list.ref_id')
            ->leftJoin('company_info', 'company_info.id', '=', 'process_list.company_id')
            ->leftJoin('department', 'department.id', '=', 'process_list.department_id')
            ->leftJoin('ea_service', 'ea_service.id', '=', 'ea_apps.service_type')
            ->leftJoin('ea_reg_commercial_offices', 'ea_reg_commercial_offices.id', '=', 'ea_apps.reg_commercial_office')
            ->leftJoin('ea_ownership_status', 'ea_ownership_status.id', '=', 'ea_apps.ownership_status_id')
            ->leftJoin('ea_organization_type', 'ea_organization_type.id', '=', 'ea_apps.organization_type_id')
            ->leftJoin('country_info', 'country_info.id', '=', 'ea_apps.ceo_country_id')
            ->leftJoin('area_info as ceo_district', 'ceo_district.area_id', '=', 'ea_apps.ceo_district_id')
            ->leftJoin('area_info as ceo_thana', 'ceo_thana.area_id', '=', 'ea_apps.ceo_thana_id')
            ->leftJoin('area_info as office_division', 'office_division.area_id', '=', 'ea_apps.office_division_id')
            ->leftJoin('area_info as office_district', 'office_district.area_id', '=', 'ea_apps.office_district_id')
            ->leftJoin('area_info as office_thana', 'office_thana.area_id', '=', 'ea_apps.office_thana_id')
            ->leftJoin('area_info as factory_district', 'factory_district.area_id', '=', 'ea_apps.factory_district_id')
            ->leftJoin('area_info as factory_thana', 'factory_thana.area_id', '=', 'ea_apps.factory_thana_id')
            ->where('process_list.process_type_id', 100)
            ->where('process_list.status_id', 25)
            ->where('process_list.company_id', $companyId)
            ->where('company_info.is_eligible', 1)
            ->first([
                'ea_apps.*',
                'company_info.business_category',
                'process_list.department_id',
                'department.name as department',
                'ea_service.name as service_name',
                'ea_reg_commercial_offices.name as reg_commercial_office_name',
                'ea_ownership_status.name as ea_ownership_status',
                'ea_organization_type.id as ea_organization_type_id',
                'ea_organization_type.name as ea_organization_type',
                'country_info.nicename as ceo_country',
                'ceo_district.area_nm as ceo_district_name',
                'ceo_thana.area_nm as ceo_thana_name',
                'office_division.area_nm as office_division_name',
                'office_district.area_nm as office_district_name',
                'office_thana.area_nm as office_thana_name',
                'factory_district.area_nm as factory_district_name',
                'factory_thana.area_nm as factory_thana_name',
            ]);
    }

    public static function getBasicInformationByProcessRefId($process_type_id, $app_id)
    {
        if (empty($process_type_id)) {
            return false;
        }

        $table_info = ProcessType::where('id', $process_type_id)->first(['table_name']);
        $table_name = $table_info->table_name;

        return ProcessList::leftjoin("$table_name", "$table_name.id", "=", "process_list.ref_id")
            ->leftJoin("department", "department.id", "=", "process_list.department_id")
            ->leftJoin("ea_service", "ea_service.id", "=", "$table_name.service_type")
            ->leftJoin("ea_reg_commercial_offices", "ea_reg_commercial_offices.id", "=", "$table_name.reg_commercial_office")
            ->leftJoin("ea_ownership_status", "ea_ownership_status.id", "=", "$table_name.ownership_status_id")
            ->leftJoin("ea_organization_type", "ea_organization_type.id", "=", "$table_name.organization_type_id")
            ->leftJoin("country_info", "country_info.id", "=", "$table_name.ceo_country_id")
            ->leftJoin("area_info as ceo_district", "ceo_district.area_id", "=", "$table_name.ceo_district_id")
            ->leftJoin("area_info as ceo_thana", "ceo_thana.area_id", "=", "$table_name.ceo_thana_id")
            ->leftJoin("area_info as office_division", "office_division.area_id", "=", "$table_name.office_division_id")
            ->leftJoin("area_info as office_district", "office_district.area_id", "=", "$table_name.office_district_id")
            ->leftJoin("area_info as office_thana", "office_thana.area_id", "=", "$table_name.office_thana_id")
            ->leftJoin("area_info as factory_district", "factory_district.area_id", "=", "$table_name.factory_district_id")
            ->leftJoin("area_info as factory_thana", "factory_thana.area_id", "=", "$table_name.factory_thana_id")
            ->leftJoin('company_info', 'company_info.id', '=', 'process_list.company_id')
            ->where("process_list.process_type_id", $process_type_id)
            ->where("$table_name.id", $app_id)
            ->first([
                "$table_name.*",
//                "$table_name.company_name",
//                "$table_name.company_name_bn",
//                "$table_name.major_activities",
//                "$table_name.service_type",
//                "$table_name.organization_type_other",
//
//                "$table_name.ceo_dob",
//                "$table_name.ceo_passport_no",
//                "$table_name.ceo_nid",
//                "$table_name.ceo_full_name",
//                "$table_name.ceo_designation",
//                "$table_name.ceo_city",
//                "$table_name.ceo_state",
//                "$table_name.ceo_post_code",
//                "$table_name.ceo_address",
//                "$table_name.ceo_telephone_no",
//                "$table_name.ceo_mobile_no",
//                "$table_name.ceo_fax_no",
//                "$table_name.ceo_email",
//                "$table_name.ceo_father_name",
//                "$table_name.ceo_mother_name",
//                "$table_name.ceo_spouse_name",
//                "$table_name.ceo_gender",
//                "$table_name.ceo_country_id",
//                "$table_name.ceo_district_id",
//                "$table_name.ceo_thana_id",
//                "$table_name.ceo_auth_letter",
//
//                "$table_name.office_post_office",
//                "$table_name.office_post_code",
//                "$table_name.office_address",
//                "$table_name.office_telephone_no",
//                "$table_name.office_mobile_no",
//                "$table_name.office_fax_no",
//                "$table_name.office_email",
//
//                "$table_name.factory_post_office",
//                "$table_name.factory_post_code",
//                "$table_name.factory_address",
//                "$table_name.factory_telephone_no",
//                "$table_name.factory_mobile_no",
//                "$table_name.factory_fax_no",
//                "$table_name.factory_email",
//                "$table_name.factory_mouja",

                "company_info.business_category",
                "process_list.department_id",
                "department.name as department",
                "ea_service.name as service_name",
                "ea_reg_commercial_offices.name as reg_commercial_office_name",
                "ea_ownership_status.name as ea_ownership_status",
                "ea_organization_type.name as ea_organization_type",
                "ea_organization_type.id as ea_organization_type_id",
                "country_info.nicename as ceo_country",
                "ceo_district.area_nm as ceo_district_name",
                "ceo_thana.area_nm as ceo_thana_name",
                "office_division.area_nm as office_division_name",
                "office_district.area_nm as office_district_name",
                "office_thana.area_nm as office_thana_name",
                "factory_district.area_nm as factory_district_name",
                "factory_thana.area_nm as factory_thana_name",
            ]);

    }

    public static function getSecurityClearanceStatusNameById($status_id){
        $status_name = DB::select(DB::raw('select status_name from security_clearance_status where id='.$status_id));
        return isset($status_name[0]) ? $status_name[0]->status_name : '';
    }


    // For checking all generated pdf submitted or not
    static public function checkForeignFileSubmission($applicationId)
    {
        $submitStatus = NewRegForeign::where('id', $applicationId)->where('doc_status', '0')->first();
        if (count($submitStatus) != 0) {
            return true;
        } else {
            return false;
        }
    }

    static public function getForeignFileStatus($fileListId, $applicationId)
    {
        $fileStatus = RjscNrfSubmitForms::where('app_id', $applicationId)->where('ref_id', $fileListId)->first();
        if (count($fileStatus) > 0) {

            if ($fileStatus->file == "" || $fileStatus->file == "NULL" || $fileStatus->file == null) {
//                dd($fileStatus->file);
                return false;
            } else {
                return true;
            }

        } else {
            return false;
        }
    }

    static public function getForeignFileUploadedStatus($fileListId, $applicationId)
    {
        $fileStatus = RjscNrfSubmitForms::where('app_id', $applicationId)->where('ref_id', $fileListId)->first();
        if (count($fileStatus) > 0) {

            if ($fileStatus->doc == "" || $fileStatus->doc == "NULL" || $fileStatus->doc == null) {
                return false;
            } else {
                return true;
            }

        } else {
            return false;
        }
    }

    public static function getUserWorkingCompany()
    {
        if (Auth::user()) {
            return Auth::user()->working_company_id;
        } else {
            return 0;
        }
    }
    // Get stakeholder token for authorization

    public static function getToken($idp_url, $client_id, $client_secret)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array(
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'grant_type' => 'client_credentials'
        )));
        curl_setopt($curl, CURLOPT_URL, "$idp_url");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $result = curl_exec($curl);
        curl_close($curl);
        $decoded_json = json_decode($result, true);
        $token = $decoded_json['access_token'];
        return $token;
    }

    public static function getTokenV2($serverConfig)
    {

        $idp_url = $serverConfig->submission_token_url;
        $tokenKey = 'access_token';
        if ($serverConfig->submission_type == 'Token_based' && !empty($serverConfig->token_details)) {
            $tokenDetails = $serverConfig->token_details;
            if(!empty($tokenDetails->token_key)){
                $tokenKey = $tokenDetails->token_key;
            }
            $postFields = $tokenDetails->body;
            $headers = $tokenDetails->header;
        }else{
            $postFields = [
                'clientId'=>$serverConfig->submission_token_client,
                'clientSecret'=>$serverConfig->submission_token_secret
            ];
            $headers = array(
                'Content-Type: application/json'
            );
        }

        $postFields = json_encode($postFields);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "$idp_url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => $headers,
        ));
        $result = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        $decoded_json = json_decode($result, true);
        //dd($decoded_json,$postFields,$headers,$error);
        if (is_array($decoded_json)) {
            $commonFunctionStakeholder = new CommonFunctionStakeholder();
            $access_token = $commonFunctionStakeholder->array_search_key_recursive($tokenKey, $decoded_json);
            if (!empty($access_token[$tokenKey])) {
                return $access_token[$tokenKey];
            } else {
                return '';
            }
        }else{
            return '';
        }
    }// end -:- getTokenV2()

    public static function	base64Imagepath($photograph_base64,$prefix,$key){

        $yearMonth = date("Y") . "/" . date("m") . "/";
        $path = 'uploads/' . $yearMonth;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        if($key === ''){
            $splited = explode(',', substr($photograph_base64, 5), 2);
            $splitedtype1 = explode(';', substr($photograph_base64, 5), 2);
        }else{
            $splited = explode(',', substr($photograph_base64[$key], 5), 2);
            $splitedtype1 = explode(';', substr($photograph_base64[$key], 5),2);
        }
        $splitedtype = explode('/',$splitedtype1[0]);
        $imageData = $splited[1];
        $base64ResizeImage = base64_encode(ImageProcessing::resizeBase64Image($imageData, 300, 300));
        $base64ResizeImage = base64_decode($base64ResizeImage);
        $company_logo_name = trim(uniqid($prefix. '-', true) . '.' . $splitedtype[1]);
        file_put_contents($path . $company_logo_name, $base64ResizeImage);
        return $yearMonth . $company_logo_name;
    }

    public static function getAreaNameByAreaId($id){
        $area = AreaInfo::where('area_id',$id)->first();
        if ($area){
            return $area->area_nm;
        }else{
            return '';
        }
    }

    public static function  getAreaForShortfall($apiUrl, $token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'agent-id: 2',
                'Authorization: Bearer '.$token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $data = json_decode($response);
        return $data;
    }

    public static function publishToQueue($data){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://localhost:3000/msg-pub',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }


    public static function trackingNoGenerator($trackingPrefix, $process_type_id, $processDataId)
    {
        // Add date to the tracking prefix.
        $trackingPrefix = $trackingPrefix . date("dMY") . '-';

        // Build the update query.
        $query = "UPDATE process_list, process_list as table2 SET process_list.tracking_no = (
            SELECT CONCAT(?, LPAD(IFNULL(MAX(SUBSTR(table2.tracking_no, -5, 5)) + 1, 1), 5, '0')) AS tracking_no
            FROM (SELECT * FROM process_list) AS table2
            WHERE table2.process_type_id = ? AND table2.id != ? AND table2.tracking_no LIKE ?
        )
        WHERE process_list.id = ? AND table2.id = ?";

        // Execute the query.
        try {
            DB::transaction(function () use ($trackingPrefix, $process_type_id, $processDataId, $query) {
                DB::statement($query, [
                    $trackingPrefix,
                    $process_type_id,
                    $processDataId,
                    $trackingPrefix . '%',
                    $processDataId,
                    $processDataId
                ]);
            });

            return true;
        } catch (\Exception $e) {
            Log::error("An error occurred while generating the tracking number: " . $e->getMessage());
            return false;
        }
    }


    public static function generateAttachmentKey($organization_status_id, $ownership_status_id, $prefix) {
        $organization_key = "";
        $ownership_key = "";
    
        switch ($organization_status_id) {
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
    
        switch ($ownership_status_id) {
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
    
        return $prefix . "_" . $ownership_key . "_" . $organization_key;
    }


    // public static function applicationInProcessing($process_type_id)
    // {
    //     try {
    //         if(env('server_type') == 'local') {
    //             return false;
    //         }
    //         // if (in_array(env('server_type'), ['local', 'uat'])) {
    //         //     return false;
    //         // }
            
    //         if (!in_array($process_type_id, [13, 14, 15, 16]) || Auth::user()->user_type != "5x505") {
    //             return false;
    //         }

    //         $companyIds = CommonFunction::getUserCompanyWithZero();
        
    //         $statusIds = [];
    //         switch ($process_type_id) {
    //             case 13:
    //             case 16:
    //                 $statusIds = [1, 2, 5, 8, 9, 40, 41, 42, 15, 16, 11, 12];
    //                 break;
    //             case 14:
    //                 $statusIds = [1, 2, 5, 8, 9, 10, 40, 41, 42];
    //                 break;
    //             case 15:
    //                 $statusIds = [1, 2, 5, 8, 9, 40, 41, 42];
    //                 break;
    //         }

    //         // For single company id.
    //         $applicationInProcessing = ProcessList::where('process_type_id', $process_type_id)
    //             ->where('company_id', $companyIds)
    //             ->whereIn('status_id', $statusIds)
    //             ->exists();

    //         return $applicationInProcessing;
    //         // For multiple company ids.
    //         // $query = ProcessList::where('process_type_id', $process_type_id);
    //         // if (is_array($companyIds) && count($companyIds) > 1) {
    //         //     $query->whereIn('company_id', $companyIds);
    //         // } elseif (is_array($companyIds) && count($companyIds) === 1) {
    //         //     $query->where('company_id', $companyIds[0]);
    //         // }
    //         // $applicationInProcessing = $query->whereIn('status_id', $statusIds)->exists();
    //     } catch (\Exception $e) {
    //         Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [CM-3874]');
    //         Log::error("An error occurred while generating the application In Processing: " . $e->getMessage());
    //         return false;
    //     }

    // }
    public static function applicationInProcessing($process_type_id, $process_id = 0)
    {
        try {
            if(env('server_type') == 'local') {
                return false;
            }
            
            if (!($process_type_id == 13 || $process_type_id == 21) || Auth::user()->user_type != "5x505") {
                return false;
            }

            if($process_type_id == 13){
                $companyIds = CommonFunction::getUserCompanyWithZero();

                $applicationInProcessing = ProcessList::where('process_type_id', $process_type_id)
                    ->where('company_id', $companyIds)
                    ->whereIn('status_id', [1, 2, 5, 8, 9, 40, 41, 42, 15, 16, 11, 12])
                    ->exists();
            }
            elseif($process_type_id == 21){
                $companyIds = CommonFunction::getUserCompanyWithZero();

                $applicationInProcessing = ProcessList::where('process_type_id', $process_type_id)
                    ->where('company_id', $companyIds)
                    ->where('id','!=',$process_id)
                    ->whereNotIn('status_id', [4,6,7,25,-1])
                    ->exists();
            }

            return $applicationInProcessing;
            
        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [CM-3874]');
            Log::error("An error occurred while generating the application In Processing: " . $e->getMessage());
            return false;
        }

    }

    // Get applications by status
    public static function getApplicationsByStatus($status, $working_company_id, $bida_service, $limit = 4) {
        $recent_applications = ProcessList::leftJoin('process_type', 'process_list.process_type_id', '=', 'process_type.id')
            ->where('process_list.company_id', $working_company_id)
            ->where('process_list.status_id', $status)
            ->orderBy('process_list.updated_at', 'desc');

        if ($bida_service) {
            $recent_applications->whereIn('process_type.bida_service_status', [1]);
        } else {
            $recent_applications->whereIn('process_type.bida_service_status', [1,2]);
        }


        $recent_applications_data = $recent_applications    
            ->limit($limit)
            ->get([
                'process_list.tracking_no',
                DB::raw("CONCAT(IF (bida_service_status=1,'BIDA Service: ',''),`process_supper_name`,'- ',`process_sub_name`) AS service_name"),
                'process_type.form_url',
                'process_type.form_id',
                'process_list.updated_at',
                'process_list.process_type_id',
                'process_list.ref_id',
                'process_type.bida_service_status'
            ])->map(function ($item) {
                $item->appRedirectPath = CommonFunction::getAppRedirectPathByJson($item->form_id);
                return $item;
            });

        $recent_applications_count = $recent_applications->count();

        return [
            'data' => $recent_applications_data,
            'count' => $recent_applications_count,
        ];
    }

    /****************************** End of Class ******************************/
}
