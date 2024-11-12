<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Config;

class SSLCommerzPaymentGateway
{
    // Store
    private $storeId;
    private $storePassword;
    // Callback
    private $successUrl;
    private $failureUrl;
    private $cancelUrl;
    private $directApiUrl;
    // EMI
    private $emi_option;
    private $emi_max_inst_option;
    private $emi_selected_inst;
    public $post_data;

    // Verification
    private $order_verify_url;

    public function __construct($module = 'visa-recommendation')
    {
        // Config
        $config = Config::get('payment.configurations.ssl_commerz');
        // Store Info
        $this->storeId = $config['store_id'];
        $this->storePassword = $config['store_passwd'];
        $this->directApiUrl = $config['direct_api_url'];

        // Callback URL
        $callback_url = $config['callback_urls'][$module];
        $this->successUrl = $callback_url['success_url'];
        $this->failureUrl = $callback_url['fail_url'];
        $this->cancelUrl = $callback_url['cancel_url'];

        // EMI
        $this->emi_option = $config['emi_option'];
        $this->emi_max_inst_option = $config['emi_max_inst_option'];
        $this->emi_selected_inst = $config['emi_selected_inst'];

        // ORDER VERIFY
        $this->order_verify_url = $config['order_verify_url'];

        $this->setPostBasicData();
    }

    public function setData($data)
    {
        if (count($data) == 0)
            return false;

        foreach ($data as $key => $value) {
            $this->post_data[$key] = $value;
        }
        return true;
    }

    private function setPostBasicData()
    {
        $this->post_data['store_id'] = $this->storeId;
        $this->post_data['store_passwd'] = $this->storePassword;
        $this->post_data['success_url'] = $this->successUrl;
        $this->post_data['fail_url'] = $this->failureUrl;
        $this->post_data['cancel_url'] = $this->cancelUrl;

        # EMI INFO
        $this->post_data['emi_option'] = $this->emi_option;
        $this->post_data['emi_max_inst_option'] = $this->emi_max_inst_option;
        $this->post_data['emi_selected_inst'] = $this->emi_selected_inst;
    }

    private function sslConnection()
    {
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $this->directApiUrl);
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($handle, CURLOPT_POST, 1);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $this->post_data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC


        $content = curl_exec($handle);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        if ($code == 200 && !(curl_errno($handle))) {
            curl_close($handle);
            return $content;
        } else {
            curl_close($handle);
            return false;
        }
    }

    public function makePayment()
    {
        $sslcommerzResponse = $this->sslConnection();

        if ($sslcommerzResponse) {
            # PARSE THE JSON RESPONSE
            $sslcz = json_decode($sslcommerzResponse, true);
            echo "<script>window.location.href = '" . $sslcz['GatewayPageURL'] . "';</script>";
            exit;
        } else {
            return 'FAILED TO CONNECT WITH SSLCOMMERZ API';
        }
    }

    /**
     * Order  Hash(SIGN and Hash KEY) Verification
     * @param $response_data
     * @return bool
     */
    public function _ipn_hash_verify($response_data)
    {
        if (isset($response_data) && isset($response_data['verify_sign']) && isset($response_data['verify_key'])) {
            # NEW ARRAY DECLARED TO TAKE VALUE OF ALL POST

            $pre_define_key = explode(',', $response_data['verify_key']);

            $new_data = array();
            if (!empty($pre_define_key)) {
                foreach ($pre_define_key as $value) {
                    if (isset($response_data[$value])) {
                        $new_data[$value] = ($response_data[$value]);
                    }
                }
            }

            # ADD MD5 OF STORE PASSWORD
            $new_data['store_passwd'] = md5($this->storePassword);

            # SORT THE KEY AS BEFORE
            ksort($new_data);

            $hash_string = "";
            foreach ($new_data as $key => $value) {
                $hash_string .= $key . '=' . ($value) . '&';
            }
            $hash_string = rtrim($hash_string, '&');

            if (md5($hash_string) == $response_data['verify_sign']) {

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Order Validation
     * @param $val_id
     * @return true/false boolean
     */
    public function orderValidation($val_id)
    {
        $store_id = $this->storeId;
        $store_passwd = $this->storePassword;

        $requested_url = ($this->order_verify_url . "?val_id=" . $val_id . "&store_id=" . $store_id . "&store_passwd=" . $store_passwd . "&v=1&format=json");

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $requested_url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false); # IF YOU RUN FROM LOCAL PC
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false); # IF YOU RUN FROM LOCAL PC

        $result = curl_exec($handle);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        if ($code == 200 && !(curl_errno($handle))) {

            return json_decode($result, 1);

        } else {

            return false;
        }
    }
}