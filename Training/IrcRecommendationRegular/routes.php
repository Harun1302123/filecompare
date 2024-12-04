<?php

Route::group(['module' => 'IrcRecommendationRegular', 'middleware' => ['auth', 'checkAdmin', 'XssProtection'],
    'namespace' => 'App\Modules\IrcRecommendationRegular\Controllers'], function () {

    //******************* IRC Recommendation new
    Route::get('irc-recommendation-regular/add', 'IrcRecommendationRegularController@applicationForm');
    Route::get('irc-recommendation-regular/get-business-class-modal', 'IrcRecommendationRegularController@showBusinessClassModal');
    Route::get('irc-recommendation-regular/get-business-class-single-list', 'IrcRecommendationRegularController@getBusinessClassSingleList');
    Route::get('irc-recommendation-regular/get-district-by-division', 'IrcRecommendationRegularController@getDistrictByDivision');
    Route::post('irc-recommendation-regular/getDocList', 'IrcRecommendationRegularController@getDocList');

    Route::post('irc-recommendation-regular/add', 'IrcRecommendationRegularController@appStore');
    Route::get('irc-recommendation-regular/edit/{id}/{openMode}', "IrcRecommendationRegularController@applicationViewEdit");
    Route::get('irc-recommendation-regular/view/{id}', "IrcRecommendationRegularController@applicationView");
    Route::post('irc-recommendation-regular/upload-document', "IrcRecommendationRegularController@uploadDocument");
    Route::get('irc-recommendation-regular/afterPayment/{payment_id}', 'IrcRecommendationRegularController@afterPayment');
    Route::get('irc-recommendation-regular/afterCounterPayment/{payment_id}', 'IrcRecommendationRegularController@afterCounterPayment');
    Route::get('irc-recommendation-regular/preview', 'IrcRecommendationRegularController@preview');
    Route::get('irc-recommendation-regular/get-raw-material/{id}', 'IrcRecommendationRegularController@getRawMaterial');
    Route::get('irc-recommendation-regular/list-of-annual-production', 'IrcRecommendationRegularController@listOfAnnualProduction');

    // payment
    //inspection report
    Route::get('irc-recommendation-regular/inspection-report-view/{app_id}', 'IrcRecommendationRegularController@inspectionForm');
    Route::get('irc-recommendation-regular/report-generate/{inspection_id}', 'IrcRecommendationRegularController@inspectionReportGenerate');
    Route::get('irc-recommendation-regular/production-capacity-pdf/{app_id}', 'IrcRecommendationRegularController@ProductionCapacityPDF');
    Route::get('irc-recommendation-regular/entitlement-paper-pdf/{inspection_id}', 'IrcRecommendationRegularController@entitlementPaperPDF');
    Route::get('irc-recommendation-regular/existing-machines-pdf/{app_id}', 'IrcRecommendationRegularController@ExisitingMachinesPDF');

    //Excel
    Route::get('irc-recommendation-regular/import/{app_id}/{apc_id}', "CsvUploadDownloadController@importRequest");
    Route::post('irc-recommendation-regular/upload-csv-file', 'CsvUploadDownloadController@uploadCsvFile');
    Route::get('irc-recommendation-regular/request/{path}/{app_id}/{apc_id}/{unit_of_product}', 'CsvUploadDownloadController@previewDataFromCsv');
    Route::post('irc-recommendation-regular/save-data/', 'CsvUploadDownloadController@saveDataFromCsv');

    //annual production capacity
    Route::get('irc-recommendation-regular/apc-form/{app_id}', "AppSubDetailsController@annualProductionCapacityForm");
    Route::post('irc-recommendation-regular/store-annual-production', "AppSubDetailsController@annualProductionCapacityStore");
    Route::post('irc-recommendation-regular/load-apc-data', "AppSubDetailsController@loadAannualProductionCapacityData");
    Route::get('irc-recommendation-regular/apc-form-edit/{app_id}', "AppSubDetailsController@annualProductionCapacityEditForm");
    Route::post('irc-recommendation-regular/update-annual-production', "AppSubDetailsController@annualProductionCapacityStore");
    Route::get('irc-recommendation-regular/apc-delete/{app_id}', "AppSubDetailsController@annualProductionCapacityDelete");
    Route::get('irc-recommendation-regular/production-info', "AppSubDetailsController@annualProductionCapacityInfo");
    
    //Directors
    Route::get('/irc-recommendation-regular/create-director/{app_id}', "AppSubDetailsController@directorForm");
    Route::post('/irc-recommendation-regular/store-verify-director', "AppSubDetailsController@storeVerifyDirector");
    Route::post('irc-recommendation-regular/load-listof-directors', 'AppSubDetailsController@loadListOfDirectiors');
    Route::get('irc-recommendation-regular/director-form-edit/{id}', 'AppSubDetailsController@directorEditForm');
    Route::post('/irc-recommendation-regular/director-form-update', 'AppSubDetailsController@directorUpdate');
    Route::get('/irc-recommendation-regular/director-delete/{id}', 'AppSubDetailsController@directorDelete');

    #raw materails
    Route::get('irc-recommendation-regular/add-raw-material/{app_id}/{id}', 'AppSubDetailsController@rawMaterialForm');
    Route::post('irc-recommendation-regular/store-raw-material', 'AppSubDetailsController@storeRawMaterial');

    #Excel
    Route::get('irc-recommendation-regular/import/{app_id}/{apc_id}', "CsvUploadDownloadController@importRequest");
    Route::post('irc-recommendation-regular/upload-csv-file', 'CsvUploadDownloadController@uploadCsvFile');
    Route::get('irc-recommendation-regular/request/{path}/{app_id}/{apc_id}/{unit_of_product}', 'CsvUploadDownloadController@previewDataFromCsv');
    Route::post('irc-recommendation-regular/save-data/','CsvUploadDownloadController@saveDataFromCsv');
    
});

Route::group(array('module' => 'ProcessPath', 'middleware' => ['auth', 'checkAdmin'],
    'namespace' => 'App\Modules\ProcessPath\Controllers'), function () {
    Route::get('irc-recommendation-regular/list/{process_id}', 'ProcessPathController@processListById');
});
