<?php

Route::group(['module' => 'IRCRecommendationNew', 'middleware' => ['auth', 'checkAdmin', 'XssProtection'],
    'namespace' => 'App\Modules\IrcRecommendationNew\Controllers'], function () {

    //******************* IRC Recommendation new
    Route::get('irc-recommendation-new/add', 'IrcRecommendationNewController@applicationForm');
    Route::get('irc-recommendation-new/get-business-class-modal', 'IrcRecommendationNewController@showBusinessClassModal');
    Route::get('irc-recommendation-new/get-business-class-single-list', 'IrcRecommendationNewController@getBusinessClassSingleList');
    Route::get('irc-recommendation-new/get-district-by-division', 'IrcRecommendationNewController@getDistrictByDivision');
    Route::post('irc-recommendation-new/getDocList', 'IrcRecommendationNewController@getDocList');
    Route::post('irc-recommendation-new/add', 'IrcRecommendationNewController@appStore');
    Route::get('irc-recommendation-new/edit/{id}/{openMode}', "IrcRecommendationNewController@applicationViewEdit");
    Route::get('irc-recommendation-new/view/{id}', "IrcRecommendationNewController@applicationView");
    Route::post('irc-recommendation-new/upload-document', "IrcRecommendationNewController@uploadDocument");
    Route::get('irc-recommendation-new/afterPayment/{payment_id}', 'IrcRecommendationNewController@afterPayment');
    Route::get('irc-recommendation-new/afterCounterPayment/{payment_id}', 'IrcRecommendationNewController@afterCounterPayment');
    Route::get('irc-recommendation-new/preview', 'IrcRecommendationNewController@preview');
    Route::get('irc-recommendation-new/get-raw-material/{id}', 'IrcRecommendationNewController@getRawMaterial');
    Route::get('irc-recommendation-new/list-of-annual-production', 'IrcRecommendationNewController@listOfAnnualProduction');

    //annual production capacity
    Route::get('irc-recommendation-new/apc-form/{app_id}', "AppSubDetailsController@annualProductionCapacityForm");
    Route::post('irc-recommendation-new/store-annual-production', "AppSubDetailsController@annualProductionCapacityStore");
    Route::post('irc-recommendation-new/load-apc-data', "AppSubDetailsController@loadAannualProductionCapacityData");
    Route::get('irc-recommendation-new/apc-form-edit/{app_id}', "AppSubDetailsController@annualProductionCapacityEditForm");
    Route::post('irc-recommendation-new/update-annual-production', "AppSubDetailsController@annualProductionCapacityStore");
    Route::get('irc-recommendation-new/apc-delete/{app_id}', "AppSubDetailsController@annualProductionCapacityDelete");

    #raw materails
    Route::get('irc-recommendation-new/add-raw-material/{app_id}/{id}', 'AppSubDetailsController@rawMaterialForm');
    Route::post('irc-recommendation-new/store-raw-material', 'AppSubDetailsController@storeRawMaterial');

    // payment
    Route::post('irc-recommendation-new/manual-payment', "IrcRecommendationNewController@manualPayment");

    //inspection report
    Route::get('irc-recommendation-new/inspection-report-view/{app_id}', 'IrcRecommendationNewController@inspectionForm');
    Route::get('irc-recommendation-new/report-generate/{inspection_id}', 'IrcRecommendationNewController@inspectionReportGenerate');
    Route::get('irc-recommendation-new/production-capacity-pdf/{app_id}', 'IrcRecommendationNewController@ProductionCapacityPDF');
    Route::get('irc-recommendation-new/entitlement-paper-pdf/{inspection_id}', 'IrcRecommendationNewController@entitlementPaperPDF');
    Route::get('irc-recommendation-new/existing-machines-pdf/{app_id}', 'IrcRecommendationNewController@ExisitingMachinesPDF');

    //Excel
    Route::get('irc-recommendation-new/import/{app_id}/{apc_id}', "CsvUploadDownloadController@importRequest");
    Route::post('irc-recommendation-new/upload-csv-file', 'CsvUploadDownloadController@uploadCsvFile');
    Route::get('irc-recommendation-new/request/{path}/{app_id}/{apc_id}/{unit_of_product}', 'CsvUploadDownloadController@previewDataFromCsv');
    Route::post('irc-recommendation-new/save-data/','CsvUploadDownloadController@saveDataFromCsv');
});

Route::group(array('module' => 'ProcessPath', 'middleware' => ['auth', 'checkAdmin'],
    'namespace' => 'App\Modules\ProcessPath\Controllers'), function () {
    Route::get('irc-recommendation-new/list/{process_id}', 'ProcessPathController@processListById');
});
