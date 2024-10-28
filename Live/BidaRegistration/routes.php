<?php

Route::group(['module' => 'BidaRegistration', 'middleware' => ['auth', 'checkAdmin', 'XssProtection'],
    'namespace' => 'App\Modules\BidaRegistration\Controllers'], function () {

    //******************* bida registration
    Route::get('bida-registration/app-home', 'BidaRegistrationController@appHome');
    Route::get('bida-registration/licence-list', 'BidaRegistrationController@licenceList');
    Route::get('bida-registration/individual-licence', 'BidaRegistrationController@individualLicence');
    Route::get('bida-registration/get-hscodes', 'BidaRegistrationController@appFormPdf');
    Route::get('bida-registration/add', 'BidaRegistrationController@applicationForm');
    Route::post('bida-registration/add', 'BidaRegistrationController@appStore');
    Route::post('bida-registration/upload-document', 'BidaRegistrationController@uploadDocument');
    Route::post('bida-registration/getDocList', 'BidaRegistrationController@getDocList');
    //Route::get('bida-registration/list-of-machinery-info', 'BidaRegistrationController@listOfMachineryInfo');
    //Route::get('bida-registration/view/{id}/{openMode}', "BidaRegistrationController@applicationViewEdit");
    Route::get('bida-registration/edit/{id}/{openMode}', "BidaRegistrationController@applicationEdit");
    Route::get('bida-registration/view/{id}', "BidaRegistrationController@applicationView");

    //******************* bida registration
    Route::get('bida-registration/preview', 'BidaRegistrationController@preview');
//    Route::get('bida-registration/certificate/{app_id}/{process_type_id}', 'BidaRegistrationController@certificateAndOther');
    Route::get('bida-registration/app-pdf/{id}', 'BidaRegistrationController@appFormPdf');
    Route::get('bida-registration/get-district-by-division', 'BidaRegistrationController@getDistrictByDivision');

    Route::get('bida-registration/payment-confirmatspg/initiateion', 'BidaRegistrationController@paymentConfirmation');
    Route::post('bida-registration/payment-success', 'BidaRegistrationController@paymentSuccess');
    Route::post('bida-registration/payment-failed', 'BidaRegistrationController@paymentFailed');
    Route::post('bida-registration/payment-cancelled', 'BidaRegistrationController@paymentCancelled');

//    Route::get('bida-registration/get-products', 'BidaRegistrationController@getProduct');

    Route::get('bida-registration/afterPayment/{payment_id}', 'BidaRegistrationController@afterPayment');
    Route::get('bida-registration/afterCounterPayment/{payment_id}', 'BidaRegistrationController@afterCounterPayment');

    //gov payment
    Route::post('bida-registration/payment', "BidaRegistrationController@Payment");


    // Request shadow file
    Route::post('bida-registration/request-shadow-file', 'BidaRegistrationController@requestShadowFile');

    // Business sub class modal
    Route::get('bida-registration/get-business-class-modal', 'BidaRegistrationController@showBusinessClassModal');
    Route::post('bida-registration/update-business-class', 'BidaRegistrationController@updateBusinessClass');
    Route::post('bida-registration/get-business-class-list', 'BidaRegistrationController@getBusinessClassList');
    Route::get('bida-registration/get-business-class-single-list', 'BidaRegistrationController@getBusinessClassSingleList');

    Route::get('bida-registration/directors-machinery-pdf/{app_id}', 'BidaRegistrationController@directorsMachineryPDF');

    Route::post('bida-registration/get-divisional-office', 'BidaRegistrationController@getDivisionalOffice')->name('get-divisional-office');

    //details list of director, imported and local machinery
    Route::get('bida-registration/list-of/{type}/{app_id}/{process_type_id}', 'AppSubDetailsController@detailList');
    //director section
    Route::get('bida-registration/get-list-of-directors', 'AppSubDetailsController@getListOfDirectors');
    Route::get('bida-registration/create-director/{app_id}/{process_type_id}', 'AppSubDetailsController@createDirector');
    Route::post('/bida-registration/store-verify-director', 'AppSubDetailsController@storeVerifyDirector');
    Route::get('bida-registration/edit-director/{id}', 'AppSubDetailsController@editDirector');
    Route::post('bida-registration/update-director', 'AppSubDetailsController@updateDirector');
    Route::get('bida-registration/delete-director/{id}', 'AppSubDetailsController@deleteDirector');
    Route::get('bida-registration/directors-more-lists/{app_id}/{process_type_id}', 'AppSubDetailsController@moreList');
    Route::get('bida-registration/get-directors-more-lists', 'AppSubDetailsController@getMoreList');
    Route::get('bida-registration/list-of-director-info', 'AppSubDetailsController@listOfDirectorsInfo');

    Route::post('bida-registration/load-listof-directors', 'AppSubDetailsController@loadListOfDirectiors');


    //end director

    //imported machinery
    Route::get('bida-registration/get-list-of-imported-machinery', 'AppSubDetailsController@getListOfImportedMachinery');
    Route::get('bida-registration/create-imported-machinery/{app_id}/{process_type_id}', 'AppSubDetailsController@createImportedMachinery');
    Route::post('bida-registration/store-imported-machinery', 'AppSubDetailsController@storeImportedMachinery');
    Route::get('bida-registration/edit-imported-machinery/{id}', 'AppSubDetailsController@editImportedMachinery');
    Route::post('bida-registration/update-imported-machinery', 'AppSubDetailsController@updateImportedMachinery');
    Route::get('bida-registration/delete-imported-machinery/{id}', 'AppSubDetailsController@deleteImportedMachinery');
    Route::get('bida-registration/machinery-and-equipment-info', 'AppSubDetailsController@machineryAndEquipmentInfo');
    //end imported machinery

    //local machiney
    Route::get('bida-registration/get-list-of-local-machinery', 'AppSubDetailsController@getListOfLocalMachinery');
    Route::get('bida-registration/create-local-machinery/{app_id}/{process_type_id}', 'AppSubDetailsController@createLocalMachinery');
    Route::post('bida-registration/store-local-machinery', 'AppSubDetailsController@storeLocalMachinery');
    Route::get('bida-registration/edit-local-machinery/{id}', 'AppSubDetailsController@editLocalMachinery');
    Route::post('bida-registration/update-local-machinery', 'AppSubDetailsController@updateLocalMachinery');
    Route::get('bida-registration/delete-local-machinery/{id}', 'AppSubDetailsController@deleteLocalMachinery');
    //end local machinery

    #existing machines routes
    Route::get('bida-registration/get-list-of-annual-production', 'AppSubDetailsController@getAnnualProductionList');
    Route::get('bida-registration/add-annual-production/{id}/{process_type_id}', 'AppSubDetailsController@addAnnualProduction');
    Route::post('bida-registration/store-annual-production', 'AppSubDetailsController@storeAnnualProduction');
    Route::get('bida-registration/edit-annual-production/{id}/{process_type_id}', 'AppSubDetailsController@editAnnualProduction');
    Route::post('bida-registration/update-annual-production', 'AppSubDetailsController@updateAnnualProduction');
    Route::get('bida-registration/delete-annual-production/{id}/{process_type_id}', 'AppSubDetailsController@deleteAnnualProduction');
    Route::get('bida-registration/add-raw-material/{app_id}/{ex_machines_id}/{process_type_id}', 'AppSubDetailsController@addRawMaterial');
    Route::post('bida-registration/store-raw-material', 'AppSubDetailsController@storeRawMaterial');
    #end existing machines routes

    #exel file update
    Route::get('bida-registration/excel/{type}/{app_id}', 'CsvUploadDownloadController@attachMachineryExcelData');
    Route::post('bida-registration/excel-file-upload', 'CsvUploadDownloadController@uploadMachineryDataFromExcel');
    Route::get('bida-registration/request/{path}/{type}/{app_id}', 'CsvUploadDownloadController@machineryDataPreviewFromExcel');
    Route::post('bida-registration/machinery-excel-data-store/','CsvUploadDownloadController@storeMachineryDataFromExcel');

//    Route::get('bida-registration/list-of-directors-machinery/{app_id}/{process_type_id}', 'AppSubDetailsController@listOfDirectorsMachineryOpen');

});

Route::group(array('module' => 'ProcessPath', 'middleware' => ['auth', 'checkAdmin'],
    'namespace' => 'App\Modules\ProcessPath\Controllers'), function () {
    Route::get('bida-registration/list/{process_id}', 'ProcessPathController@processListById');
});
