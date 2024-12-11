<?php

return [
   'spg_settings_stack_holder' => array(
        'payment_mode' => env('payment_mode', 'on'),
        'web_service_url' => env('spg_web_service_url','https://spg.com.bd:6313/SpgService.asmx'),
        'web_portal_url' => env('spg_web_portal_url', 'https://spg.com.bd:6313/SpgRequest/PaymentByPortal'),
        'user_id' => env('spg_user_id', 'BidaSblBd@2018'),
        'password' => env('spg_password', 'BiidaBusiness@2018@Bangladesh'),
        'SBL_account' => env('spg_SBL_account', '4440402000653'),
        'st_code' => env('st_code', 'bida'),
        'request_id_prefix' => '010',
        'stackholder_distribution_type' => '10',
        'return_url' => env('PROJECT_ROOT','https://bidaquickserv.org') . '/spg/stack-holder/callback',
        'return_url_m' => env('PROJECT_ROOT', 'https://bidaquickserv.org') . '/spg/stack-holder/callbackM',
        'single_details_url' => env('single_details_url', 'https://spg.com.bd:6313/api/SpgService/TransactionDetails'),
//            'return_url'=>'http://oss-framework.eserve.org.bd/spg/callback'
    ),

    /*
     * Sonali Payment API Configuration
     *
     * All of the default values are taken from LIVE .env
     */
    'spg_settings' => array(
        'payment_mode' => env('payment_mode', 'on'),
        'spg_user_id' => env('spg_user_id', 'BidaSblBd@2018'),
        'spg_password' => env('spg_password', 'BiidaBusiness@2018@Bangladesh'),
        'spg_web_service_url' => env('spg_web_service_url', 'https://spg.com.bd:6313/SpgService.asmx'),
        'spg_SBL_account' => env('spg_SBL_account', '4440402000653'),
        'request_id_prefix' => '010',
        'st_code' => env('st_code', 'bida'),
        'spg_web_portal_url' => env('spg_web_portal_url', 'https://spg.com.bd:6313/SpgRequest/PaymentByPortal'),
        'return_url' => env('PROJECT_ROOT', 'https://bidaquickserv.org') . '/spg/callback',
        'return_url_m' => env('PROJECT_ROOT', 'https://bidaquickserv.org') . '/spg/callbackM',
        'single_details_url' => env('single_details_url', 'https://spg.com.bd:6313/api/SpgService/TransactionDetails'),
    )

];