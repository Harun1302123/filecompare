<?php

Route::group(['module' => 'BidaRegistrationAmendment', 'middleware' => ['auth', 'checkAdmin', 'XssProtection'],
    'namespace' => 'App\Modules\BidaRegistrationAmendment\Controllers'], function() {
    //*************BIDA registration amendment*************
    Route::get('bida-registration-amendment/add', 'BidaRegistrationAmendmentController@applicationForm');
    Route::get('bida-registration-amendment/edit/{app_id}/{openMode}', "BidaRegistrationAmendmentController@applicationEdit");
    Route::get('bida-registration-amendment/view/{app_id}', "BidaRegistrationAmendmentController@applicationView");
    Route::post('/bida-registration-amendment/add', 'BidaRegistrationAmendmentController@appStore');
    Route::get('/bida-registration-amendment/preview', 'BidaRegistrationAmendmentController@preview');

    Route::get('bida-registration-amendment/check-tracking-no-exists', 'BidaRegistrationAmendmentController@checkTrackingNoExists');

    Route::post('bida-registration-amendment/getDocList', 'BidaRegistrationAmendmentController@getDocList');

    //business sub modal
    Route::get('bida-registration-amendment/get-business-class-modal', 'BidaRegistrationAmendmentController@showBusinessClassModal');
    Route::post('bida-registration-amendment/get-business-class-list', 'BidaRegistrationAmendmentController@getBusinessClassList');
    Route::get('bida-registration-amendment/get-business-class-single-list', 'BidaRegistrationAmendmentController@getBusinessClassSingleList');

    //bra director manage
    Route::post('bida-registration-amendment/load-director-list', 'AppSubDetailsController@directorList');
    Route::get('bida-registration-amendment/director-form/{app_id}', 'AppSubDetailsController@directorForm');
    Route::post('bida-registration-amendment/store-verify-director', 'AppSubDetailsController@storeVerifyDirector');
    Route::get('bida-registration-amendment/director-edit/{director_id}', 'AppSubDetailsController@directorEdit');
    Route::post('bida-registration-amendment/update-director', 'AppSubDetailsController@directorUpdate');
    Route::get('bida-registration-amendment/delete-director/{director_id}', 'AppSubDetailsController@directorDelete');
    Route::get('bida-registration-amendment/list-of-director-count', 'AppSubDetailsController@countDirector');


    Route::get('bida-registration-amendment/directors-machineries-pdf/{app_id}', 'AppSubDetailsController@directorsMachineriesPDF');

    //Load annual production capacity when page loaded and other action..
    Route::post('/bida-registration-amendment/load-apc-data', 'AppSubDetailsController@loadAannualProductionCapacityData');

    //Annual production capacity
    Route::get('bida-registration-amendment/apc-form/{app_id}', 'AppSubDetailsController@annualProductionCapacityForm');
    Route::get('bida-registration-amendment/apc-form-edit/{apc_id}', 'AppSubDetailsController@annualProductionCapacityUpdateForm');
    Route::post('bida-registration-amendment/apc-store', 'AppSubDetailsController@annualProductionCapacityStore');
    Route::post('/bida-registration-amendment/apc-data-update', 'AppSubDetailsController@annualProductionCapacityUpdate');
    Route::get('bida-registration-amendment/apc-delete/{apc_id}', 'AppSubDetailsController@annualProductionCapacityDelete');

    //Load imported machinery when page loaded and other action
    Route::post('bida-registration-amendment/load-imported-machinery-data', 'AppSubDetailsController@loadImportedMachineryData');
    //List of imported machinery
    Route::get('bida-registration-amendment/imported-machinery-form/{app_id}', 'AppSubDetailsController@importedMachineryForm');
    Route::post('bida-registration-amendment/imported-machinery-store', 'AppSubDetailsController@importedMachineryStore');
    Route::get('bida-registration-amendment/imported-machinery-edit-form/{im_id}', 'AppSubDetailsController@importedMachineryEditForm');
    Route::post('bida-registration-amendment/imported-machinery-update', 'AppSubDetailsController@importedMachineryUpdate');
    Route::get('bida-registration-amendment/imported-machinery-delete/{im_id}', 'AppSubDetailsController@importedMachineryDelete');
    Route::post('bida-registration-amendment/batch-delete-imported-machinery', 'AppSubDetailsController@importedMachineryBatchDelete');

    #Excel file upload
    Route::get('bida-registration-amendment/excel/{type}/{app_id}', 'CsvUploadDownloadController@attachMachineryExcelData');
    Route::post('bida-registration-amendment/excel-file-upload', 'CsvUploadDownloadController@uploadMachineryDataFromExcel');
    Route::get('bida-registration-amendment/request/{path}/{type}/{app_id}', 'CsvUploadDownloadController@machineryDataPreviewFromExcel');
    Route::post('bida-registration-amendment/machinery-excel-data-store/','CsvUploadDownloadController@storeMachineryDataFromExcel');

    //Load locally imported machinery when page loaded and other action
    Route::post('bida-registration-amendment/load-local-machinery-data', 'AppSubDetailsController@loadLocalMachineryData');
    //list of local machinery
    Route::get('bida-registration-amendment/local-machinery-form/{app_id}', 'AppSubDetailsController@localMachineryForm');
    Route::post('bida-registration-amendment/local-machinery-store', 'AppSubDetailsController@localMachineryStore');
    Route::get('bida-registration-amendment/local-machinery-edit-form/{lm_id}', 'AppSubDetailsController@localMachineryEditForm');
    Route::post('bida-registration-amendment/local-machinery-update', 'AppSubDetailsController@localMachineryUpdate');
    Route::get('bida-registration-amendment/local-machinery-delete/{lm_id}', 'AppSubDetailsController@localMachineryDelete');
    Route::post('bida-registration-amendment/batch-delete-local-machinery', 'AppSubDetailsController@localMachineryBatchDelete');

    //---------BIDA registration amendment payment-----------
    Route::get('bida-registration-amendment/afterPayment/{payment_id}', 'BidaRegistrationAmendmentController@afterPayment');
    Route::get('bida-registration-amendment/afterCounterPayment/{payment_id}', 'BidaRegistrationAmendmentController@afterCounterPayment');

    //gov payment
    Route::post('bida-registration-amendment/payment', "BidaRegistrationAmendmentController@Payment");

});

Route::group(array('module' => 'ProcessPath', 'middleware' => ['auth', 'checkAdmin'],
    'namespace' => 'App\Modules\ProcessPath\Controllers'), function () {
    Route::get('bida-registration-amendment/list/{process_id}', 'ProcessPathController@processListById');
});