<?php

Route::group(['module' => 'IrcRecommendationSecondAdhoc', 'middleware' => ['auth', 'checkAdmin', 'XssProtection'],
    'namespace' => 'App\Modules\IrcRecommendationSecondAdhoc\Controllers'], function () {

    //******************* IRC Recommendation new
    Route::get('irc-recommendation-second-adhoc/add', 'IrcRecommendationSecondAdhocController@applicationForm');
    Route::get('irc-recommendation-second-adhoc/get-business-class-modal', 'IrcRecommendationSecondAdhocController@showBusinessClassModal');
    Route::get('irc-recommendation-second-adhoc/get-business-class-single-list', 'IrcRecommendationSecondAdhocController@getBusinessClassSingleList');
    Route::get('irc-recommendation-second-adhoc/get-district-by-division', 'IrcRecommendationSecondAdhocController@getDistrictByDivision');
    Route::post('irc-recommendation-second-adhoc/getDocList', 'IrcRecommendationSecondAdhocController@getDocList');
    Route::post('irc-recommendation-second-adhoc/add', 'IrcRecommendationSecondAdhocController@appStore');
    Route::get('irc-recommendation-second-adhoc/edit/{id}/{openMode}', "IrcRecommendationSecondAdhocController@applicationViewEdit");
    Route::get('irc-recommendation-second-adhoc/view/{id}', "IrcRecommendationSecondAdhocController@applicationView");
    Route::post('irc-recommendation-second-adhoc/upload-document', "IrcRecommendationSecondAdhocController@uploadDocument");
    Route::get('irc-recommendation-second-adhoc/afterPayment/{payment_id}', 'IrcRecommendationSecondAdhocController@afterPayment');
    Route::get('irc-recommendation-second-adhoc/afterCounterPayment/{payment_id}', 'IrcRecommendationSecondAdhocController@afterCounterPayment');
    Route::get('irc-recommendation-second-adhoc/preview', 'IrcRecommendationSecondAdhocController@preview');
    Route::get('irc-recommendation-second-adhoc/get-raw-material/{id}', 'IrcRecommendationSecondAdhocController@getRawMaterial');
    Route::get('irc-recommendation-second-adhoc/list-of-annual-production', 'IrcRecommendationSecondAdhocController@listOfAnnualProduction');

    // payment
    //inspection report
    Route::get('irc-recommendation-second-adhoc/inspection-report-view/{app_id}', 'IrcRecommendationSecondAdhocController@inspectionForm');
    Route::get('irc-recommendation-second-adhoc/report-generate/{inspection_id}', 'IrcRecommendationSecondAdhocController@inspectionReportGenerate');
    Route::get('irc-recommendation-second-adhoc/production-capacity-pdf/{app_id}', 'IrcRecommendationSecondAdhocController@ProductionCapacityPDF');
    Route::get('irc-recommendation-second-adhoc/entitlement-paper-pdf/{inspection_id}', 'IrcRecommendationSecondAdhocController@entitlementPaperPDF');
    Route::get('irc-recommendation-second-adhoc/existing-machines-pdf/{app_id}', 'IrcRecommendationSecondAdhocController@getExisitingMachinesPDF');

    //annual production capacity
    Route::get('irc-recommendation-second-adhoc/apc-form/{app_id}', "AppSubDetailsController@annualProductionCapacityForm");//
    Route::post('irc-recommendation-second-adhoc/store-annual-production', "AppSubDetailsController@annualProductionCapacityStore");//
    Route::post('irc-recommendation-second-adhoc/load-apc-data', "AppSubDetailsController@loadAannualProductionCapacityData");//
    Route::get('irc-recommendation-second-adhoc/apc-form-edit/{app_id}', "AppSubDetailsController@annualProductionCapacityEditForm");//
    Route::post('irc-recommendation-second-adhoc/update-annual-production', "AppSubDetailsController@annualProductionCapacityStore");
    Route::get('irc-recommendation-second-adhoc/apc-delete/{app_id}', "AppSubDetailsController@annualProductionCapacityDelete");

    #raw materails
    Route::get('irc-recommendation-second-adhoc/add-raw-material/{app_id}/{id}', 'AppSubDetailsController@rawMaterialForm');
    Route::post('irc-recommendation-second-adhoc/store-raw-material', 'AppSubDetailsController@storeRawMaterial');

    //Excel
    Route::get('irc-recommendation-second-adhoc/import/{app_id}/{apc_id}', "CsvUploadDownloadController@importRequest");
    Route::post('irc-recommendation-second-adhoc/upload-csv-file', 'CsvUploadDownloadController@uploadCsvFile');
    Route::get('irc-recommendation-second-adhoc/request/{path}/{app_id}/{apc_id}/{unit_of_product}', 'CsvUploadDownloadController@previewDataFromCsv');
    Route::post('irc-recommendation-second-adhoc/save-data/', 'CsvUploadDownloadController@saveDataFromCsv');

});

Route::group(array('module' => 'ProcessPath', 'middleware' => ['auth', 'checkAdmin'],
    'namespace' => 'App\Modules\ProcessPath\Controllers'), function () {
    Route::get('irc-recommendation-second-adhoc/list/{process_id}', 'ProcessPathController@processListById');
});
