<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Session;
use Mockery\Exception;

class Osspid
{
    private $version = '1.1';
    // LOCAL
    //private $osspid_base_url = 'http://localhost:8088';
    //private $osspid_auth_url = 'http://localhost:8088/osspid-client/auth';

    // DEV
    //private $osspid_base_url = 'http://dev-mongo.eserve.org.bd:8075';
    //private $osspid_auth_url = 'http://dev-mongo.eserve.org.bd:8075/osspid-client/auth';

    // UAT
    //private $osspid_base_url = 'https://osspid-uat.eserve.org.bd:5040';
    //private $osspid_auth_url = 'https://osspid-uat.eserve.org.bd:5040/osspid-client/auth';

    // for Local, Dev, UAT, Training server
//    private $osspid_base_url_local = 'https://osspid.eserve.org.bd:5040';
    // For live server
//    private $osspid_base_url_local = 'https://192.168.151.118';
    private $osspid_auth_url = 'https://osspid.org/osspid-client/auth';


    private $client_id;
    private $client_secret_key;
    private $callback_url;
    private $encryptor;

    /**
     * Osspid constructor.
     * @param array $options
     */
    public function __construct($options = [
        'client_id' => '',
        'client_secret_key' => '',
        'callback_url' => ''
    ])
    {

        $this->client_id = $options['client_id'];
        $this->client_secret_key = $options['client_secret_key'];
        $this->callback_url = $options['callback_url'];
        $this->encryptor = new Encryptor();
    }

    /**
     * Redirect to OSSPID oAuth
     */
    public function redirect()
    {
        $osspid_redirect_path = $this->buildUrl();
        header("Location: $osspid_redirect_path");
        exit();
    }

    /**
     * Build OSSPID redirect URL
     * @return string
     */
    private function buildUrl()
    {
        $encrypted_secret_key = $this->encryptor->encrypt($this->client_secret_key);
        return $this->osspid_auth_url .
            "?client_id={$this->client_id}" .
            "&cs={$encrypted_secret_key}" .
            "&callback_url={$this->callback_url}";
    }

    /**
     * Get OSSPID redirect URL
     * @return string
     */
    public function getRedirectURL()
    {
        return $this->buildUrl();
    }

    /**
     * Handshaking with OSSPID for oauth token verification
     * @param $oauth_token
     * @param $email
     * @return bool
     */
    public function verifyOauthToken($oauth_token, $email)
    {

        $osspid_base_url_local = config('app.osspid_base_url_ip');
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $ip_address = $_SERVER['REMOTE_ADDR'];

        $postdata =
            array(
                'client_id' => $this->client_id,
                'client_secret_key' => $this->client_secret_key,
                'oauth_token' => $oauth_token,
                'email' => $email,
                'user_agent' => $user_agent,
                'ip_address' => $ip_address
            )
        ;

        try {
            $handle = curl_init();
            curl_setopt($handle, CURLOPT_URL, $osspid_base_url_local . "/api/verify-token");
            curl_setopt($handle, CURLOPT_TIMEOUT, 30);
            curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($handle, CURLOPT_POST, 1);
            curl_setopt($handle, CURLOPT_POSTFIELDS, $postdata);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC
            $response = curl_exec($handle);

            //----------------
            if (curl_error($handle)) {
                $error_msg = curl_error($handle);
                //dd($error_msg);
                return false;
            }

            $result = json_decode($response);
            return $result->responseCode == 1 && $result->isValid == true;
        } catch (Exception $e) {
            return false;
        }

    }

    /**
     * Request for increasing oAuth Token Expire Time for verified Client
     * @param $oauth_token
     * @param $email
     * @return bool
     */
    public function requestForIncreaseOauthTokenExpireTime($oauth_token, $email)
    {
        $osspid_base_url_local = config('app.osspid_base_url_ip');
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $postdata =
            array(
                'client_id' => $this->client_id,
                'client_secret_key' => $this->client_secret_key,
                'oauth_token' => $oauth_token,
                'email' => $email,
                'user_agent' => $user_agent,
                'ip_address' => $ip_address
            )
        ;
        try {
            $handle = curl_init();
            curl_setopt($handle, CURLOPT_URL, $osspid_base_url_local . "/api/request-for-increase-token-expire-time");
            curl_setopt($handle, CURLOPT_TIMEOUT, 30);
            curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($handle, CURLOPT_POST, 1);
            curl_setopt($handle, CURLOPT_POSTFIELDS, $postdata);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC
            $response = curl_exec($handle);
            $result = json_decode($response);
            return $result->responseCode == 1;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Logout/Kill session in OSSPID
     * @param $oauth_token
     * @param $email
     * @return bool
     */
    public function logoutFromOsspid($oauth_token, $email)
    {
        $osspid_base_url_local = config('app.osspid_base_url_ip');
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $postdata =
            array(
                'client_id' => $this->client_id,
                'client_secret_key' => $this->client_secret_key,
                'oauth_token' => $oauth_token,
                'email' => $email,
                'ip_address'=>$ip_address,
                'user_agent'=>$user_agent
            )
        ;
        try {
            $handle = curl_init();
            curl_setopt($handle, CURLOPT_URL, $osspid_base_url_local . "/api/osspid-client-logout");
            curl_setopt($handle, CURLOPT_TIMEOUT, 30);
            curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($handle, CURLOPT_POST, 1);
            curl_setopt($handle, CURLOPT_POSTFIELDS, $postdata);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC
            $response = curl_exec($handle);
            $result = json_decode($response);
            if ($result == null)
                return true;

            return $result->responseCode == 1;
        } catch (Exception $e) {
            return false;
        }
    }

}

// End of Osspid Controller