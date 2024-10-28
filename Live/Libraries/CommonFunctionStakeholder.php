<?php

namespace App\Libraries;

use App\Modules\ExternalLicense\Controllers\ExternalLicenseController;

class CommonFunctionStakeholder
{
//    public $submissionResponse = '{
//    "JSONObject": {
//        "data": {
//            "application_url": "https://10.73.20.142:8205/bcare/telephoneOut?custId=111339722&companyType=1&token=eyJhbGciOiJIUzUxMiJ9.eyJVU0VSX05BTUUiOiIwMDAwMDAwMDAwMSIsIkNVU1RfVFlQRSI6IkQiLCJleHAiOjE2OTU2MjYzNDh9.4UfwQnagpv2WnqE2e8mVB3rF7J1ZR5kyOiMJECtRB3qY1wxZ5XaFHiTeDTf0kuDpvtrXVnk6Mgd4FYIqsLAcpQ",
//            "cust_id": 111339722
//        },
//        "othersInfo": {
//            "account_number": "",
//            "certificate_url": ""
//        },
//        "message": "Application Submitted successfully external service unified format.",
//        "responseCode": 200,
//        "status": "Submitted",
//        "statusCode": "1"
//    }
//}';
//    public $tokenResponse = '{
//    "JSONObject": {
//        "data": {
//            "access_token": "eyJhbGciOiJIUzUxMiJ9.eyJVU0VSX05BTUUiOiIwMDAwMDAwMDAwMSIsIkNVU1RfVFlQRSI6IkQiLCJleHAiOjE2OTU2MjYzNDh9.4UfwQnagpv2WnqE2e8mVB3rF7J1ZR5kyOiMJECtRB3qY1wxZ5XaFHiTeDTf0kuDpvtrXVnk6Mgd4FYIqsLAcpQ",
//            "custId": 111310720,
//            "userName": "00000000001"
//        },
//        "success": true,
//        "message": "Successfully Token Generate",
//        "responseCode": 200
//    }
//}';

    function array_search_key_recursive($searchKey, $array)
    {
        $results = [];
        foreach ($array as $key => $value) {
            if ($key === $searchKey) {
                $results[$key] = $value;
            }
            if (is_array($value)) {
                $results = array_merge($results, $this->array_search_key_recursive($searchKey, $value));
            }
        }
        return $results;
    }// end -:- array_search_key_recursive()

    public static function externalServiceSubmissionRequest($result)
    {
        $responseDecode = json_decode($result, true);
        if (is_array($responseDecode)) {
            $commonFunctionStakeholder = new CommonFunctionStakeholder();
            $application_url = $commonFunctionStakeholder->array_search_key_recursive('application_url', $responseDecode);
            $cust_id = $commonFunctionStakeholder->array_search_key_recursive('cust_id', $responseDecode);
            $message = $commonFunctionStakeholder->array_search_key_recursive('message', $responseDecode);
            $status = $commonFunctionStakeholder->array_search_key_recursive('status', $responseDecode);
            $statusCode = $commonFunctionStakeholder->array_search_key_recursive('statusCode', $responseDecode);
            //$account_number = $commonFunctionStakeholder->array_search_key_recursive('account_number', $responseDecode);
            //$certificate_url = $commonFunctionStakeholder->array_search_key_recursive('certificate_url', $responseDecode);
            $responseCode = $commonFunctionStakeholder->array_search_key_recursive('responseCode', $responseDecode);
            $othersInfo = $commonFunctionStakeholder->array_search_key_recursive('othersInfo', $responseDecode);

            $jsonResponse = [];
            $jsonResponse['application_url'] = !empty($application_url['application_url']) ? $application_url['application_url'] : '';
            $jsonResponse['cust_id'] = !empty($cust_id['cust_id']) ? $cust_id['cust_id'] : '';
            $jsonResponse['message'] = !empty($message['message']) ? $message['message'] : '';
            $jsonResponse['responseCode'] = !empty($responseCode['responseCode']) ? $responseCode['responseCode'] : '';
            $jsonResponse['status'] = !empty($status['status']) ? $status['status'] : '';
            $jsonResponse['statusCode'] = !empty($statusCode['statusCode']) ? $statusCode['statusCode'] : 0;
            //$jsonResponse['othersInfo']['account_number'] = !empty($account_number['account_number']) ? $account_number['account_number'] : '';
            //$jsonResponse['othersInfo']['certificate_url'] = !empty($certificate_url['certificate_url']) ? $certificate_url['certificate_url'] : '';
            $jsonResponse['othersInfo'] = is_array($othersInfo)?$othersInfo:'';
            $jsonEncode = json_encode($jsonResponse);

            return ['http_code' => $jsonResponse['responseCode'], 'result' => $jsonEncode];
        } else {
            return ['http_code' => 0, 'result' => null];
        }
    }// end -:- externalServiceSubmissionRequest()

    public static function getExternalServiceToken($idp_url, $postFields, $headers)
    {
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
        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        $decodedJson = json_decode($response, true);

        if (is_array($decodedJson)) {
            $commonFunctionStakeholder = new CommonFunctionStakeholder();
            $access_token = $commonFunctionStakeholder->array_search_key_recursive('access_token', $decodedJson);
            if (!empty($access_token['access_token'])) {
                return $access_token['access_token'];
            } else {
                return '';
            }
        } else {
            return '';
        }
    }// end -:- getExternalServiceToken()
}// end -:- CommonFunctionStakeholder
?>
