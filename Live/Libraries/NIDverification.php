<?php


namespace App\Libraries;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Modules\Signup\Models\NidVerificationLog;


class NIDverification
{
    const LOGIN_REQUEST = 'LOGIN_REQUEST';

    const VERIFY_NID = 'VERIFY_NID';

    const VERSION = '1.0';

    private $nid_server_address;
    private $nid_token_server;
    private $nid_client_id;
    private $nid_reg_key;
    private $grant_type;

    public function __construct()
    {
        $this->nid_server_address = config('app.NID_SERVER');
        $this->nid_token_server = config('app.NID_TOKEN_SERVER');
        $this->nid_client_id = config('app.NID_SERVER_CLIENT_ID');
        $this->nid_reg_key = config('app.NID_SERVER_REG_KEY');
        $this->grant_type = config('app.NID_GRANT_TYPE');
    }


    public function getAuthToken()
    {
        try {

            $access_data = [
                "client_id" => $this->nid_client_id,
                "client_secret" => $this->nid_reg_key,
                "grant_type" => $this->grant_type
            ];
            $curl_handle = curl_init();
            curl_setopt($curl_handle, CURLOPT_POST, 1);
            curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($access_data));
            curl_setopt($curl_handle, CURLOPT_URL, $this->nid_token_server);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl_handle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $result = curl_exec($curl_handle);
            if (curl_errno($curl_handle)) {
                $data = ['responseCode' => 0, 'msg' => curl_error($curl_handle), 'data' => ''];
                curl_close($curl_handle);
                return json_encode($data);
            }
            curl_close($curl_handle);

            if (!$result || !property_exists(json_decode($result), 'access_token')) {
                $data = ['responseCode' => 0, 'msg' => 'API connection failed!', 'data' => ''];
                return json_encode($data);
            }

            $decoded_json = json_decode($result, true);
            return $decoded_json['access_token'];

        } catch (\Exception $e) {
            return false;
        }
    }

    public function verifyNID($nid_data, $auth_token)
    {
        if (empty($nid_data)) {
            return $this->returnResponse('error', 400, [], 'Given NID data is not valid. Please make request with valid data');
        }
        if (empty($auth_token)) {
            return $this->returnResponse('error', 400, [], 'Given Authorization token is not valid');
        }

        $userNid = $nid_data['nid_number'];
        $userDOB = date("Y-m-d", strtotime($nid_data['user_DOB']));

        if(strlen($userNid) == 13){ //13 to 17 digit nid convert
            $year = date('Y', strtotime($nid_data['user_DOB']));
            $userNid = $year.$userNid;
        }

        // Log NID request
        $nidReqLogId = $this->nidVerificationLogRequest($nid_data);

        //$json = '{"dateOfBirth":"'.$userDOB.'","nid":"'.$userNid.'"}';
        $json = json_encode([
            "dateOfBirth" => $userDOB,
            "nid" => $userNid
        ]);

        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->nid_server_address,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>$json,
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $auth_token",
                    "Content-Type: application/json"
                ),
            ));

            $response = curl_exec($curl);

            if ($response === false) {
                $nid_response_status = -1;
                Log::error('CURL Error in verifyNID() : ' . curl_error($curl));
            }else{
                $nid_response_status = 1;
            }

            curl_close($curl);

            $decoded_output = json_decode($response);
            
            // Log NID response
            $this->nidVerificationLogResponse($nidReqLogId, $nid_response_status);

            return $decoded_output;

        } catch (\Exception $e) {
            Log::error("Exception in verifyNID ({$e->getFile()} => {$e->getLine()}): {$e->getMessage()}");
            return $this->returnResponse('error', $e->getCode(), [], $e->getMessage());
        }
    }


    /* old nid verification code (zaman vai)
    public function getAuthToken()
    {
        try {

            $data = [
                "mongoDBRequest" => [
                    "requestData" => [
                        "reg_key" => config('app.NID_SERVER_REG_KEY'),
                        "clientId" => config('app.NID_SERVER_CLIENT_ID'),
                        "user_id" => 0
                    ],
                    "requestType" => self::LOGIN_REQUEST,
                    "version" => self::VERSION
                ]
            ];
            // http://103.219.147.5:8088/nid/api-request?param={"mongoDBRequest":{"requestData":{"reg_key":"A86471D7-941A-4350-A0C2-CC30F5205000","clientId":"BIDA","user_id":"0"},"requestType":"LOGIN_REQUEST","version":"1.0"}}
            $url = $this->nid_server_address . '/api-request?param=' . json_encode($data);

//            dd($url);

            // Initiate the curl session
            $handle = curl_init();
            // Set the URL
            curl_setopt($handle, CURLOPT_URL, $url);
            // Removes the headers from the output
            curl_setopt($handle, CURLOPT_HEADER, 0);
            // Return the output instead of displaying it directly
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
            // Execute the curl session
            $output = curl_exec($handle);
            // Check error
            if (curl_error($handle)) {
                $error_msg = curl_error($handle);
                dd($error_msg);
            }
            // Close the curl session
            curl_close($handle);

            $decoded_output = json_decode($output);
//            dd($decoded_output);

            if (isset($decoded_output->mongoDBRequest->responseStatus->responseData->auth_token)) {
                return $decoded_output->mongoDBRequest->responseStatus->responseData->auth_token;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function verifyNID(array $nid_data, $auth_token)
    {
        if (empty($nid_data)) {
            return $this->returnResponse('error', 400, [], 'Given NID data is not valid. Please make request with valid data');
        }
        if (empty($auth_token)) {
            return $this->returnResponse('error', 400, [], 'Given Authorization token is not valid');
        }

        try {

            $data = [
                "mongoDBRequest" => [
                    "requestData" => [
                        "nid" => $nid_data['nid_number'],
                        "dob" => date('Y-m-d', strtotime($nid_data['user_DOB'])),
                        "user_id" => 0,
                        "verification_flag" => 0,
                        "is_govt" => 'GOVT',
                        "auth_token" => $auth_token,
                    ],
                    "requestType" => self::VERIFY_NID,
                    "version" => self::VERSION
                ]
            ];
            // http://103.219.147.5:8088/nid/api-request?param={"mongoDBRequest":{"requestData":{"nid":"2807330309","dob":"1988-11-25","user_id":"0","verification_flag":"0","is_govt":"GOVT","auth_token":"jCITC6sOwI7fQOODDpggWJ9Qp8FmN2TiypY"},"requestType":"VERIFY_NID","version":"1.0"}}
            $url = $this->nid_server_address . '/api-request?param=' . json_encode($data);

            // Initiate the curl session
            $handle = curl_init();
            // Set the URL
            curl_setopt($handle, CURLOPT_URL, $url);
            // Removes the headers from the output
            curl_setopt($handle, CURLOPT_HEADER, 0);
            // Return the output instead of displaying it directly
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
            // Execute the curl session
            $output = curl_exec($handle);
            // Check error
            if (curl_error($handle)) {
                return $this->returnResponse('error', curl_errno($handle), [], curl_error($handle));
            }
            // Close the curl session
            curl_close($handle);

            $decoded_output = json_decode($output);
            $response_code = isset($decoded_output->mongoDBRequest->responseStatus->responseCode) ? intval($decoded_output->mongoDBRequest->responseStatus->responseCode) : 0;

            $responseData = [];
            switch ($response_code) {
                case 101:
                    $status = 'success';
                    $statusCode = $response_code;
                    $responseData = json_decode($decoded_output->mongoDBRequest->responseStatus->responseData->data, true);
                    $message = 'Valid NID';
                    break;
                case 444:
                    $status = 'error';
                    $statusCode = $response_code;
                    $message = 'Permission Denied';
                    break;
                case 999:
                    $status = 'success';
                    $statusCode = $response_code;
                    $message = 'Just inserted';
                    break;
                case 777:
                    $status = 'success';
                    $statusCode = $response_code;
                    $message = 'Sent to EC Server';
                    break;
                case 333:
                    $status = 'error';
                    $statusCode = $response_code;
                    $message = 'NID data not valid';
                    break;
                case 666:
                    $status = 'error';
                    $statusCode = $response_code;
                    $message = 'Invalid NID';
                    break;
                default:
                    $status = 'error';
                    $statusCode = 502;
                    $responseData = [];
                    $message = 'Invalid response from NID Server';
            }

            return $this->returnResponse($status, $statusCode, $responseData, $message);
        } catch (\Exception $e) {
            return $this->returnResponse('error', $e->getCode(), [], $e->getMessage());
        }
    }

    */

    public static function obj2ArrayForNID($nidData)
    {
        try {
            $parAddress = NIDVerification::explodeAddress(CommonFunction::convertUTF8($nidData->permanentAddress));
            $preAddress = NIDVerification::explodeAddress(CommonFunction::convertUTF8($nidData->presentAddress));
            $date_birth = explode('T', $nidData->dob);
            $response = [
                'full_name_bangla' => CommonFunction::convertUTF8($nidData->name),
                'full_name_english' => $nidData->nameEn,
                'father_name' => CommonFunction::convertUTF8($nidData->father),
                'mother_name' => CommonFunction::convertUTF8($nidData->mother),
                'birth_date' => $date_birth[0],
                'per_village_ward' => $parAddress['village_ward'],
                'per_police_station' => $parAddress['thana'],
                'per_district' => $parAddress['district'],
                'per_post_code' => $parAddress['post_code'],
                'per_post_office' => $parAddress['post_office'], // Added Post office
                'village_ward' => $preAddress['village_ward'],
                'police_station' => $preAddress['thana'],
                'district' => $preAddress['district'],
                'post_code' => $preAddress['post_code'],
                'post_office' => $preAddress['post_office'], // Added Post office
                'national_id' => $nidData->nid,
                'gender' => $nidData->gender,
                'spouse_name' => isset($nidData->spouse) ? CommonFunction::convertUTF8($nidData->spouse) : '',
                'marital_status' => '', //$nidData->maritialStatus
                'mobile' => '', //$nidData->mobileNo,
                'occupation' => '', //CommonFunction::convertUTF8($nidData->occupation),
            ];
            return $response;
        } catch (ValidationException $e) {
            echo $e;
            return null;
        } catch (\Exception $e) {
            echo $e;
            return null;
        }
    }


    public static function explodeAddress($address)
    {
        $adl = 4;
        $permanentAddress = explode(',', $address);
        if (count($permanentAddress) <= 1) {
            return [
                'village_ward' => $address,
                'thana' => '',
                'district' => '',
                'post_code' => '',
            ];
        }
        if (count($permanentAddress) > 1) {
            $data['district'] = trim($permanentAddress[count($permanentAddress) - 1]);
        } else {
            $data['district'] = '';
        }
        if (count($permanentAddress) > 3) {
            $data['thana'] = trim($permanentAddress[count($permanentAddress) - 2]);
            $per_post_office = trim($permanentAddress[count($permanentAddress) - 3]);
            $per_post_codes = explode('-', $per_post_office);
            if (count($per_post_codes) > 1) {
                $data['post_code'] = trim($per_post_codes[count($per_post_codes) - 1]);
                if (!is_numeric(CommonFunction::convert2English($data['post_code'])) && count($permanentAddress) > 4) {

                    $per_post_office = trim($permanentAddress[count($permanentAddress) - 4]);
                    $per_post_codes = explode('-', $per_post_office);
                    if (is_numeric(CommonFunction::convert2English(trim($per_post_codes[count($per_post_codes) - 1])))) {
                        $data['post_code'] = trim($per_post_codes[count($per_post_codes) - 1]);
                        $data['thana'] = trim($permanentAddress[count($permanentAddress) - 3]);
                        $adl = 5;
                    }
                }
                $data['post_code'] = CommonFunction::convert2English($data['post_code']);
            } else {
                $data['post_code'] = '';
            }
            // Added post office
            $data['post_office'] = str_replace('ডাকঘর:', '', $per_post_codes[0]);
        } else {
            $data['thana'] = '';
            $data['post_code'] = '';
            // Added post office
            $data['post_code'] = '';
        }

        $data['village_ward'] = $permanentAddress[0];
        for ($i = 1; $i <= count($permanentAddress) - $adl; $i++) {
            $data['village_ward'] .= ', ' . $permanentAddress[$i];
        }

        if ($adl == 5) {
            $data['village_ward'] .= ', ' . trim($permanentAddress[count($permanentAddress) - 2]);
        }
        return $data;
    }


    private function returnResponse($status, $statusCode, array $data = [], $message = 'Sorry, Something went wrong!')
    {
        return $response_data = [
            'status' => $status,
            'statusCode' => intval($statusCode),
            'data' => $data,
            'message' => $message
        ];
    }

    public function nidVerificationLogRequest($nid_data)
    {
        try{
            $nid = $nid_data['nid_number'];
            $dob = date("Y-m-d", strtotime($nid_data['user_DOB']));
            $nidLog = new NidVerificationLog();
            $nidLog->nid_request_time = Carbon::now();
            $nidLog->nid = $nid;
            $nidLog->dob = $dob;

            if (!empty($nid_data['user_nid_name'])) {
                $nidLog->user_nid_name = $nid_data['user_nid_name'];
            }

            if (!empty($nid_data['user_nid_postal_code'])) {
                $nidLog->user_nid_postal_code = $nid_data['user_nid_postal_code'];
            }


            if (Auth::check()) {
                $user = Auth::user();
                $nidLog->verify_by_email = $user->user_email;
                $nidLog->verify_by_mobile = $user->user_mobile;
                $nidLog->created_by = $user->id;
                $nidLog->updated_by = $user->id;
            } else {
                $oauth_data = \Illuminate\Support\Facades\Session::get('oauth_data');
                $nidLog->verify_by_email = $oauth_data->user_email;
                $nidLog->verify_by_mobile = $oauth_data->mobile;
            }

            $nidLog->save();

            return $nidLog->id;
        }catch (\Exception $e){
            Log::error("Error in nidVerificationLogRequest ({$e->getFile()} => {$e->getLine()}): {$e->getMessage()}");
            return null;
        }
    }


    public function nidVerificationLogResponse($nidReqLogId, $nid_response_status)
    {
        if($nidReqLogId == null){
            return false;
        }
        try{
            $nidLog = NidVerificationLog::find($nidReqLogId);
            if ($nidLog) {
                $nidLog->nid_response_status = $nid_response_status;
                $nidLog->nid_response_time = Carbon::now();
                $nidLog->save();
                return true;
            }else{
                Log::error('Data not found Error in nidVerificationLogResponse() : ' . $nidReqLogId);
                return false;
            }
        }catch (\Exception $e){
            Log::error("Error in nidVerificationLogResponse ({$e->getFile()} => {$e->getLine()}): {$e->getMessage()}");
            return false;
        }
    }


}