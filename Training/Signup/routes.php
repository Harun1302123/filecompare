<?php
Route::group(array('module' => 'Signup', 'middleware' => ['XssProtection'], 'namespace' => 'App\Modules\Signup\Controllers'), function () {

    Route::get('signup', 'SignupController@create');
    Route::patch('signup/store', 'SignupController@store');
    Route::get('google_signUp', 'SignupController@google_signUp');
    Route::get('osspid_signUpOne', 'SignupController@osspidSignUpOne');
    Route::patch('signup/google/store', 'SignupController@GoogleStore');
    Route::get('signup/verification/{confirmationCode}', [
        'as' => 'confirmation_path',
        'uses' => 'SignupController@verification'
    ]);

    Route::patch('signup/verification_store/{confirmationCode}', [
        'as' => 'confirmation_path',
        'uses' => 'SignupController@verificationStore'
    ]);
    Route::get('/signup/resend-mail', 'SignupController@resendMail');


    Route::get('signup/identity-verify', 'SignupController@identityVerify');
    Route::get('signup/identity-verify/nid-verify-auth', 'SignupController@nidVerifyAuth');
    Route::get('signup/identity-verify/nid-verify', 'SignupController@nidVerifyRequest');
    Route::get('signup/identity-verify/etin-verify', 'SignupController@etinVerify');
    Route::get('signup/getPassportData', 'SignupController@getPassportData');
    Route::post('signup/identity-verify', 'SignupController@identityVerifyConfirm');
    Route::post('signup/identity-verify-previous/{id}', 'SignupController@identityVerifyConfirmWithPreviousData');
    Route::get('signup/registration', 'SignupController@OSSsignupForm');
    Route::post('signup/registration', 'SignupController@OSSsignupStore');
    Route::post('signup/getPassportData', [
        'as' => 'getPassportData',
        'uses' => 'SignupController@getPassportData'
    ]);

    Route::match(['get', 'post'], 'signup/identity-verify-otp', 'SignupController@identityVerifyOtp')->name('signup.identity_verify_otp');
    Route::post('signup/otp-verify', 'SignupController@otpVerify')->name('signup.otp_verify');

});	