<?php

namespace App\Libraries;

use App\Modules\API\Models\ApiClientMaster;
use App\Modules\API\Models\ClientOauthToken;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\Signup\Models\UserVerificationData;
use App\Modules\Signup\Models\UserVerificationOtp;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Modules\Settings\Models\Configuration;

//use Illuminate\Support\Facades\Session;

class nidTokenServiceJWT
{

    public function generateNIDToken($clientData)
    {
        try {
            $tokenExpireTime = Configuration::where('caption', 'SIGNUP_NID_OTP_JWT_TOKEN_TIME')->pluck('value');

            $tokenEncodeData = [
                'client_id' => $clientData->client_id,
                'client_secret_key' => $clientData->client_secret_key,
                "exp" => time() + $tokenExpireTime
            ];
            $currentTime = Carbon::now();
            $otpExpireTime = $currentTime->addSeconds($tokenExpireTime);

            $jwtToken = JWT::encode($tokenEncodeData, $clientData->encryption_key, 'HS256');
            $tokenResponse['token'] = $jwtToken;
            $tokenResponse['expire_in'] = $otpExpireTime;   
            return $tokenResponse;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function storeNIDToken($jwtTokenArray)
    {
        $userVerificationTokenData = UserVerificationOtp::where('user_email', Session::get('oauth_data')->user_email)
            ->where('user_mobile', Session::get('oauth_data')->mobile)
            ->where('otp_status', 2)
            ->orderBy('id', 'DESC')
            ->first();
        $userVerificationTokenData->token = $jwtTokenArray['token'];
        $userVerificationTokenData->token_expire_time = $jwtTokenArray['expire_in'];
        $userVerificationTokenData->token_status = 1;
        $userVerificationTokenData->save();
    }

    public function checkNIDTokenValidity($token, $encryption_key)
    {
        try {

            /**
             * if there have an expire time in your payload while encoding JWT,
             * the JWT decode function() will automatically check the expire time.
             * No need to do extra checking.
             */
            $decoded_token = JWT::decode($token, $encryption_key, ['HS256']);

            $client_id = $decoded_token->client_id;
            $client_secret_key = $decoded_token->client_secret_key;

            // Check valid credential
            if($client_id == config('app.NID_JWT_ID') && $client_secret_key == config('app.NID_JWT_SECRET_KEY')){
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}