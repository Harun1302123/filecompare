<?php

Route::group(['module' => 'IrcRecommendationThirdAdhoc', 'middleware' => ['auth', 'checkAdmin', 'XssProtection'],
    'namespace' => 'App\Modules\IrcRecommendationThirdAdhoc\Controllers'], function () {

    //******************* IRC Recommendation new
    Route::get('irc-recommendation-third-adhoc/add', 'IrcRecommendationThirdAdhocController@applicationForm');
    Route::get('irc-recommendation-third-adhoc/get-business-class-modal', 'IrcRecommendationThirdAdhocController@showBusinessClassModal');
    Route::get('irc-recommendation-third-adhoc/get-business-class-single-list', 'IrcRecommendationThirdAdhocController@getBusinessClassSingleList');
    Route::get('irc-recommendation-third-adhoc/get-district-by-division', 'IrcRecommendationThirdAdhocController@getDistrictByDivision');
    Route::post('irc-recommendation-third-adhoc/getDocList', 'IrcRecommendationThirdAdhocController@getDocList');

    Route::post('irc-recommendation-third-adhoc/add', 'IrcRecommendationThirdAdhocController@appStore');
    Route::get('irc-recommendation-third-adhoc/edit/{id}/{openMode}', "IrcRecommendationThirdAdhocController@applicationViewEdit");
    Route::get('irc-recommendation-third-adhoc/view/{id}', "IrcRecommendationThirdAdhocController@applicationView");
    Route::post('irc-recommendation-third-adhoc/upload-document', "IrcRecommendationThirdAdhocController@uploadDocument");
    Route::get('irc-recommendation-third-adhoc/afterPayment/{payment_id}', 'IrcRecommendationThirdAdhocController@afterPayment');
    Route::get('irc-recommendation-third-adhoc/afterCounterPayment/{payment_id}', 'IrcRecommendationThirdAdhocController@afterCounterPayment');
    Route::get('irc-recommendation-third-adhoc/preview', 'IrcRecommendationThirdAdhocController@preview');
    Route::get('irc-recommendation-third-adhoc/get-raw-material/{id}', 'IrcRecommendationThirdAdhocController@getRawMaterial');
    Route::get('irc-recommendation-third-adhoc/list-of-annual-production', 'IrcRecommendationThirdAdhocController@listOfAnnualProduction');

    // payment
    //inspection report
    Route::get('irc-recommendation-third-adhoc/inspection-report-view/{app_id}', 'IrcRecommendationThirdAdhocController@inspectionForm');
    Route::get('irc-recommendation-third-adhoc/report-generate/{inspection_id}', 'IrcRecommendationThirdAdhocController@inspectionReportGenerate');
    Route::get('irc-recommendation-third-adhoc/production-capacity-pdf/{app_id}', 'IrcRecommendationThirdAdhocController@ProductionCapacityPDF');
    Route::get('irc-recommendation-third-adhoc/entitlement-paper-pdf/{inspection_id}', 'IrcRecommendationThirdAdhocController@entitlementPaperPDF');
    Route::get('irc-recommendation-third-adhoc/existing-machines-pdf/{app_id}', 'IrcRecommendationThirdAdhocController@getExisitingMachinesPDF');

    //Excel
    Route::get('irc-recommendation-third-adhoc/import/{app_id}/{apc_id}', "CsvUploadDownloadController@importRequest");
    Route::post('irc-recommendation-third-adhoc/upload-csv-file', 'CsvUploadDownloadController@uploadCsvFile');
    Route::get('irc-recommendation-third-adhoc/request/{path}/{app_id}/{apc_id}/{unit_of_product}', 'CsvUploadDownloadController@previewDataFromCsv');
    Route::post('irc-recommendation-third-adhoc/save-data/', 'CsvUploadDownloadController@saveDataFromCsv');

    //annual production capacity
    Route::get('irc-recommendation-third-adhoc/apc-form/{app_id}', "AppSubDetailsController@annualProductionCapacityForm");
    Route::post('irc-recommendation-third-adhoc/store-annual-production', "AppSubDetailsController@annualProductionCapacityStore");
    Route::post('irc-recommendation-third-adhoc/load-apc-data', "AppSubDetailsController@loadAannualProductionCapacityData");
    Route::get('irc-recommendation-third-adhoc/apc-form-edit/{app_id}', "AppSubDetailsController@annualProductionCapacityEditForm");
    Route::post('irc-recommendation-third-adhoc/update-annual-production', "AppSubDetailsController@annualProductionCapacityStore");
    Route::get('irc-recommendation-third-adhoc/apc-delete/{app_id}', "AppSubDetailsController@annualProductionCapacityDelete");
    
    //Directors
    Route::get('/irc-recommendation-third-adhoc/create-director/{app_id}', "AppSubDetailsController@directorForm");
    Route::post('/irc-recommendation-third-adhoc/store-verify-director', "AppSubDetailsController@storeVerifyDirector");
    Route::post('irc-recommendation-third-adhoc/load-listof-directors', 'AppSubDetailsController@loadListOfDirectiors');
    Route::get('irc-recommendation-third-adhoc/director-form-edit/{id}', 'AppSubDetailsController@directorEditForm');
    Route::post('/irc-recommendation-third-adhoc/director-form-update', 'AppSubDetailsController@directorUpdate');
    Route::get('/irc-recommendation-third-adhoc/director-delete/{id}', 'AppSubDetailsController@directorDelete');

    #raw materails
    Route::get('irc-recommendation-third-adhoc/add-raw-material/{app_id}/{id}', 'AppSubDetailsController@rawMaterialForm');
    Route::post('irc-recommendation-third-adhoc/store-raw-material', 'AppSubDetailsController@storeRawMaterial');

    #Excel
    Route::get('irc-recommendation-third-adhoc/import/{app_id}/{apc_id}', "CsvUploadDownloadController@importRequest");
    Route::post('irc-recommendation-third-adhoc/upload-csv-file', 'CsvUploadDownloadController@uploadCsvFile');
    Route::get('irc-recommendation-third-adhoc/request/{path}/{app_id}/{apc_id}/{unit_of_product}', 'CsvUploadDownloadController@previewDataFromCsv');
    Route::post('irc-recommendation-third-adhoc/save-data/','CsvUploadDownloadController@saveDataFromCsv');
    
});

Route::group(array('module' => 'ProcessPath', 'middleware' => ['auth', 'checkAdmin'],
    'namespace' => 'App\Modules\ProcessPath\Controllers'), function () {
    Route::get('irc-recommendation-third-adhoc/list/{process_id}', 'ProcessPathController@processListById');
});
