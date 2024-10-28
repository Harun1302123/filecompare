<?php

Route::group(array('module' => 'ProcessPath', 'middleware' => ['auth', 'XssProtection'], 'namespace' => 'App\Modules\ProcessPath\Controllers'), function () {

    // List of all process
    Route::get('process/list', "ProcessPathController@processListById");
    // for global search
    Route::post('process/list', "ProcessPathController@processListById");
    Route::get('process/list/feedback-list', "ProcessPathController@processListById");
    Route::get('process/list/{process_id}', "ProcessPathController@processListById");

    Route::get('process/view/{id}', "ProcessPathController@viewApplication");
    // get desk by status
    Route::post('process-path/get-desk-by-status', "ProcessPathController@getDeskByStatus");
    Route::post('process-path/batch-process-update', "ProcessPathController@updateProcess");
    Route::get('process-path/check-process-validity', "ProcessPathController@checkApplicationValidity");
    Route::post('process-path/ajax/{param}', 'ProcessPathController@ajaxRequest');
    Route::get('process-path/verify_history/{process_type_id}/{process_list_id}', 'ProcessPathController@verifyProcessHistory');

    //get desk by user
    Route::post('process-path/get-user-by-desk', "ProcessPathController@getUserByDesk");
    //shadow file
    Route::post('/process-path/request-shadow-file', "ProcessPathController@requestShadowFile");
    Route::get('process/get-feedback-list', "ProcessPathController@getFeedbackList");
    //    Route::get('process/get-list/{process_type_id}/{status}',[
    Route::get('process/get-list/{status?}/{desk?}', [
        'as' => 'process.getList',
        'uses' => 'ProcessPathController@getList'
    ]);
    Route::get('process/get-status-list', [
        'as' => 'process.getStatusList',
        'uses' => 'ProcessPathController@getStatusWiseList'
    ]);

    Route::get('process/set-process-type', [
        'as' => 'process.setProcessType',
        'uses' => 'ProcessPathController@setProcessType'
    ]);
    Route::get('process/search-process-type', [
        'as' => 'process.searchProcessType',
        'uses' => 'ProcessPathController@searchProcessType'
    ]);

    Route::resource('ProcessPath', 'ProcessPathController');

    Route::get('process/{url}/add/{process_type_id}', "ProcessPathController@entryForm");
    Route::get('process/{url}/{submodule}/add/{process_type_id}', "ProcessPathController@entryFormSubModule");

    Route::get('process/{url}/view/{app_id}/{process_type_id}', "ProcessPathController@editViewForm");

    // separate view page application start
    Route::get('process/{url}/view-app/{app_id}/{process_type_id}', "ProcessPathController@applicationView");
    Route::get('process/{url}/edit-app/{app_id}/{process_type_id}', "ProcessPathController@applicationEdit");
    // separate view page application end

    Route::get('process/{url}/{submodule}/view/{app_id}/{process_type_id}', "ProcessPathController@editViewFormSubModule");
//    Route::get('process/{url}/view/{app_id}/{process_type_id}/board-meeting', "ProcessPathController@editViewForm");



    Route::get('process/form', "ProcessPathController@getForm");
    Route::post('process/help-text', "ProcessPathController@getHelpText");
    Route::post('process/favorite-data-store', "ProcessPathController@favoriteDataStore");
    Route::post('process/favorite-data-remove', "ProcessPathController@favoriteDataRemove");
    Route::post('process/get-duration', 'ProcessPathController@getDuration');
    Route::post('process/get-date-duration', 'ProcessPathController@getDateDuration');
    Route::post('process/get-start-end-date-validation', 'ProcessPathController@startEndDateValidation');


    // Process flow graph route
    Route::get('process/graph/{process_type_id}/{app_id}', 'ProcessPathController@getProcessDataByAjax');

    //Notifications related routes like all process related status notification and send to mail or sms
    Route::get('process/notifications/{app_id}/{process_type_id}', 'ProcessPathController@notifications');

    // get meeting date
    Route::post('process-path/get-meeting-date', "ProcessPathController@getMeetingDate");
    Route::get('process/batch-process-set', "ProcessPathController@batchProcessSet");
    Route::get('process/batch-process-skip/{id}', "ProcessPathController@skipApplication");
    Route::get('process/batch-process-previous/{id}', "ProcessPathController@previousApplication");

    // Certificate Regeneration
    Route::get('process/certificate-regeneration/{app_id}/{process_type_id}', 'ProcessPathController@certificateRegeneration');

    // Basic info module company info
    Route::get('process/change-company/{company_id}', 'ProcessPathController@changeCompanyModal');
    Route::post('process/store-change-company', 'ProcessPathController@storeChangeCompany');


    // Get recent attachment
    Route::get('process/recent-attachment/{doc_id}', 'ProcessPathController@getRecentAttachment');

    // Resend email
    Route::get('process/resend-email/{process_type_id}/{app_id}/{status_id}', 'ProcessPathController@statusWiseEmailResend');

    // Process flow graph route
    Route::get('process/graph/{process_type_id}/{app_id}/{cat_id}', 'ProcessPathController@getProcessDataByAjax');
    // get shadow file history
    Route::get('process/get-shadow-file-hist/{process_type_id}/{ref_id}', 'ProcessPathController@getShadowFileHistory');
    // get application history
    Route::get('process/get-app-hist/{process_list_id}', 'ProcessPathController@getApplicationeHistory');

    //attachment panel
    Route::get('process/open-attachment/{process_type_id}/{app_id}/{doc_section?}', 'ProcessPathController@openAttachment');
    Route::get('process/get-last-remarks', 'ProcessPathController@getLastRemarks');

});
