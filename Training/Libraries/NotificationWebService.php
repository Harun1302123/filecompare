<?php


namespace App\Libraries;


use App\Modules\Settings\Models\Configuration;
use Carbon\Carbon;

class NotificationWebService
{
    /**
     * @return false|string
     */
    private function getToken()
    {
        $api_token = Configuration::firstOrNew(['caption' => 'email_sms_api_token']);
        if (isset($api_token->value2) && $api_token->value2 > time()) {
            $data = [
                'responseCode' => 1,
                'data' => $api_token->value
            ];

            return json_encode($data);
        }

        $sms_api_url_for_token = config('app.SMS_API_URL_FOR_TOKEN');
        $sms_client_id = config('app.SMS_CLIENT_ID');
        $sms_client_secret = config('app.SMS_CLIENT_SECRET');
        $sms_grant_type = config('app.SMS_GRANT_TYPE');

        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array(
                'client_id' => $sms_client_id,
                'client_secret' => $sms_client_secret,
                'grant_type' => $sms_grant_type
            )));
            curl_setopt($curl, CURLOPT_URL, "$sms_api_url_for_token");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($curl);

            if (curl_errno($curl)) {
                $data = ['responseCode' => 0, 'msg' => curl_error($curl), 'data' => ''];
                curl_close($curl);
                return json_encode($data);
            }
            curl_close($curl);

            if (!$result || !property_exists(json_decode($result), 'access_token')) {
                $data = ['responseCode' => 0, 'msg' => 'SMS API connection failed!', 'data' => ''];
                return json_encode($data);
            }

            $decoded_json = json_decode($result, true);
            $data = [
                'responseCode' => 1,
                'data' => $decoded_json['access_token'],
            ];

            // updating token
            $api_token->value = $decoded_json['access_token'];
            $api_token->value2 = (time() + $decoded_json['expires_in']) - 60;// deducted for latency
            $api_token->updated_at = Carbon::now();
            $api_token->save();

        } catch (Exception $e) {
            $data = ['responseCode' => 0, 'msg' => $e->getMessage() . $e->getFile() . $e->getLine(), 'data' => ''];
        }

        return json_encode($data);
    }

    /**
     * @param array $email_data
     * @return array
     */
    public function sendEmail(array $email_data)
    {
        $token_response = json_decode($this->getToken());
        if ($token_response->responseCode == 0) {
            return ['status' => 0, 'msg' => $token_response->msg];
        }
        $access_token = $token_response->data;

        try {
            $sms_api_url_for_token = config('app.EMAIL_API_URL_FOR_SEND');
            $base_email_for_api = config('app.EMAIL_FROM_FOR_EMAIL_API');
            $email_from_for_email_api = ($email_data['header_text']) ? $email_data['header_text'] . ' <' . $base_email_for_api . '>' : $base_email_for_api;

            $curl_handle = curl_init();
            curl_setopt($curl_handle, CURLOPT_POST, 1);
            curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query(array(
                'sender' => $email_from_for_email_api,
                'receipant' => $email_data['recipient'],
                'subject' => $email_data['subject'],
                'bodyText' => '',
                'bodyHtml' => $email_data['bodyHtml'],
                'cc' => $email_data['email_cc']
            )));
            curl_setopt($curl_handle, CURLOPT_URL, "$sms_api_url_for_token");
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl_handle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer $access_token",
                "Content-Type: application/x-www-form-urlencoded"
            ));
            $result = curl_exec($curl_handle);

            if (curl_errno($curl_handle)) {
                curl_close($curl_handle);
                return [
                    'status' => 0,
                    'msg' => curl_error($curl_handle)
                ];
            }
            curl_close($curl_handle);

            $decoded_json = json_decode($result, true);
            if (isset($decoded_json['status']) and $decoded_json['status'] == 200) {
                return ['status' => 1, 'msg' => $result, 'message_id' => $decoded_json['data']['id']];
            }

            return ['status' => 0, 'msg' => $result];
        } catch (\Exception $exception) {
            return ['status' => 0, 'msg' => $exception->getMessage() . $exception->getFile() . $exception->getLine()];
        }
    }

    /**
     * @param $mobile_number
     * @param $sms_body
     * @return array
     */
    public function sendSms($mobile_number, $sms_body)
    {
        $token_response = json_decode($this->getToken());
        if ($token_response->responseCode == 0) {
            return ['status' => 0, 'msg' => $token_response->msg];
        }
        $access_token = $token_response->data;

        try {
            $sms_api_url = config('app.SMS_API_URL_FOR_SEND');
            $curl_handle = curl_init();
            curl_setopt_array($curl_handle, array(
                CURLOPT_URL => "$sms_api_url",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "{\n\t    \"msg\": \"$sms_body\",\n\t    \"destination\": \"$mobile_number\"\n\t\n}\n",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $access_token",
                    "Content-Type: application/json",
                    "Content-Type: text/plain"
                ),
            ));
            $response = curl_exec($curl_handle);

            if (curl_errno($curl_handle)) {
                curl_close($curl_handle);
                return [
                    'status' => 0,
                    'msg' => curl_error($curl_handle)
                ];
            }
            curl_close($curl_handle);

            $decoded_json = json_decode($response, true);
            if (isset($decoded_json['status']) and $decoded_json['status'] == 200) {
                return ['status' => 1, 'msg' => $response, 'message_id' => $decoded_json['data']['id']];
            }

            return ['status' => 0, 'msg' => $response];
        } catch (\Exception $exception) {
            return ['status' => 0, 'msg' => $exception->getMessage() . $exception->getFile() . $exception->getLine()];
        }
    }
}