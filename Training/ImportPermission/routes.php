<?php

Route::group(['module' => 'ImportPermission', 'middleware' => ['auth', 'checkAdmin', 'XssProtection'],
    'namespace' => 'App\Modules\ImportPermission\Controllers'], function () {

    //******************* Import Permission
    Route::get('import-permission/add', 'ImportPermissionController@applicationForm');
    Route::get('import-permission/get-business-class-modal', 'ImportPermissionController@showBusinessClassModal');
    Route::get('import-permission/get-business-class-single-list', 'ImportPermissionController@getBusinessClassSingleList');
    Route::get('import-permission/get-district-by-division', 'ImportPermissionController@getDistrictByDivision');
    Route::post('import-permission/getDocList', 'ImportPermissionController@getDocList');
    Route::post('import-permission/add', 'ImportPermissionController@appStore');
    Route::get('import-permission/edit/{id}/{openMode}', "ImportPermissionController@applicationEdit");
    Route::get('import-permission/view/{id}', "ImportPermissionController@applicationView");
    Route::post('import-permission/upload-document', "ImportPermissionController@uploadDocument");
    Route::get('import-permission/afterPayment/{payment_id}', 'ImportPermissionController@afterPayment');
    Route::get('import-permission/afterCounterPayment/{payment_id}', 'ImportPermissionController@afterCounterPayment');
    Route::get('import-permission/preview', 'ImportPermissionController@preview');
    Route::get('import-permission/get-raw-material/{id}', 'ImportPermissionController@getRawMaterial');
    Route::get('import-permission/list-of-annual-production', 'ImportPermissionController@listOfAnnualProduction');
});

Route::group(array('module' => 'ProcessPath', 'middleware' => ['auth', 'checkAdmin'],
    'namespace' => 'App\Modules\ProcessPath\Controllers'), function () {
    Route::get('import-permission/list/{process_id}', 'ProcessPathController@processListById');
});
