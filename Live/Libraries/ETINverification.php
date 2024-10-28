<?php


namespace App\Libraries;


class ETINverification
{
    private $etin_server_address;

    public function __construct()
    {
        $this->etin_server_address = config('app.ETIN_SERVER');
    }

    public function getAuthToken()
    {
        try {

            $data = [
                "UserName" => config('app.ETIN_USERNAME'),
                "Password" => config('app.ETIN_PASSWORD')
            ];

            $url = $this->etin_server_address . '/api/Auth/token';

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json"
                ),
            ));

            $response = curl_exec($curl);
//            $err = curl_error($curl);
            curl_close($curl);

            $decoded_output = json_decode($response);

            if (isset($decoded_output->token)) {
                return $decoded_output->token;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function verifyETIN($etin_number, $auth_token)
    {
        if (empty($etin_number)) {
            return $this->returnResponse('error', 400, [], 'Given ETIN data is not valid. Please make request with valid data');
        }
        if (empty($auth_token)) {
            return $this->returnResponse('error', 400, [], 'Given Authorization token is not valid');
        }

        try {
            $url = $this->etin_server_address . '/api/TIN/' . $etin_number;
            $auth_token = 'Bearer ' . $auth_token;
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Authorization:  " . $auth_token,
                    "Content-Type: application/json"
                ),
            ));

            $response = curl_exec($curl);
//            $err = curl_error($curl);
            curl_close($curl);

            $decoded_output = json_decode($response);

            $check = array_keys((array)$decoded_output);

            if ($check[1] == 'isError') {
                $status = 'error';
                $statusCode = 0;
                $responseData = [];
                $message = 'Invalid ETIN';
            } else {
                $status = 'success';
                $statusCode = 0;
                $responseData = json_decode(json_encode($decoded_output), true);
                $message = 'Valid ETIN';
            }

            return $this->returnResponse($status, $statusCode, $responseData, $message);
        } catch (\Exception $e) {
            return $this->returnResponse('error', $e->getCode(), [], $e->getMessage());
        }
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
}