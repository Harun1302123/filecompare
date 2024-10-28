<?php

namespace App\Modules\ProcessPath\Services;

use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\Settings\Models\PdfPrintRequestQueue;
use App\Modules\Settings\Models\PdfServiceInfo;
use Illuminate\Support\Facades\Auth;

trait CertificateGenerationRequestTrait
{
    /**
     * certificateGenerationRequest
     *
     * @param  mixed $app_id
     * @param  mixed $process_type_id
     * @param  mixed $approver_desk_id
     * @param  mixed $department
     * @param  mixed $certificate_type
     * @return boolean
     */
    function certificateGenerationRequest($app_id, $process_type_id, $approver_desk_id = 0, $department = '', $certificate_type = 'generate')
    {
        $tableName = '';
        $fieldName = '';
        switch ($process_type_id) {
            case 1: //Visa Recommendation New (1)
                $appInfo = ProcessList::leftJoin('vr_apps as app', 'app.id', '=', 'process_list.ref_id')
                    ->where('process_list.ref_id', $app_id)
                    ->where('process_list.process_type_id', $process_type_id)
                    ->first(['app.app_type_id as app_type_id']);
                if (empty($appInfo)) {
                    return false;
                }

                // 5 = Visa On Arrival
                if ($appInfo->app_type_id == 5) {
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'vr_certificate_on_arrival')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                } else {
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'vr_certificate_others')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                }

                if (empty($pdf_info)) {
                    return false;
                }
                $tableName = $pdf_info->table_name;
                $fieldName = $pdf_info->field_name;
                break;
            case 2: //Work permit new (2)
                if ($department == 1) { //Commercial
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'wp_new_commercial')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                } else { //Industrial
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'wp_new_industrial')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                }

                if (empty($pdf_info)) {
                    return false;
                }
                $tableName = $pdf_info->table_name;
                $fieldName = $pdf_info->field_name;
                break;
            case 3: //Work permit extension (3)
                if ($department == 1) { //Commercial
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'wp_extension_commercial')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                } else { //Industrial
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'wp_extension_industrial')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                }

                if (empty($pdf_info)) {
                    return false;
                }
                $tableName = $pdf_info->table_name;
                $fieldName = $pdf_info->field_name;
                break;
            case 4: //Work permit amendment (4)
                if ($department == 1) { //Commercial
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'wp_amendment_commercial')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                } else { //Industrial
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'wp_amendment_industrial')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                }

                if (empty($pdf_info)) {
                    return false;
                }
                $tableName = $pdf_info->table_name;
                $fieldName = $pdf_info->field_name;
                break;
            case 5: //Work permit cancellation (5)
                if ($department == 1) { //Commercial
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'wp_cancellation_commercial')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                } else { //Industrial
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'wp_cancellation_industrial')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                }

                if (empty($pdf_info)) {
                    return false;
                }
                $tableName = $pdf_info->table_name;
                $fieldName = $pdf_info->field_name;
                break;
            case 6: // Office Permission New (6)
                // get office type of application
                $appInfo = ProcessList::leftJoin('opn_apps as app', 'app.id', '=', 'process_list.ref_id')
                    ->where('process_list.ref_id', $app_id)
                    ->where('process_list.process_type_id', $process_type_id)
                    ->first(['app.office_type']);
                if (empty($appInfo)) {
                    return false;
                }

                if ($appInfo->office_type == 1) { // Branch office
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'opn_branch_commercial')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                } elseif ($appInfo->office_type == 2) { // Liaison office
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'opn_liaison_commercial')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                } elseif ($appInfo->office_type == 3) { // Representative office
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'opn_representative_commercial')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                } else {
                    return false;
                }

                if (empty($pdf_info)) {
                    return false;
                }
                $tableName = $pdf_info->table_name;
                $fieldName = $pdf_info->field_name;
                break;
            case 7: // Office permission extension (7)
                $appInfo = ProcessList::leftJoin('ope_apps as app', 'app.id', '=', 'process_list.ref_id')
                    ->where('process_list.ref_id', $app_id)
                    ->where('process_list.process_type_id', $process_type_id)
                    ->first(['app.office_type']);
                if (empty($appInfo)) {
                    return false;
                }

                if ($appInfo->office_type == 1) { // Branch office
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'ope_branch_commercial')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                } elseif ($appInfo->office_type == 2) { // Liaison office
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'ope_liaison_commercial')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                } elseif ($appInfo->office_type == 3) { // Representative office
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'ope_representative_commercial')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                } else {
                    return false;
                }

                if (empty($pdf_info)) {
                    return false;
                }
                $tableName = $pdf_info->table_name;
                $fieldName = $pdf_info->field_name;
                break;
            case 8: // Office permission amendment (8)
                $pdf_info = PdfServiceInfo::where('certificate_name', 'opa_commercial')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                if (empty($pdf_info)) {
                    return false;
                }
                $tableName = $pdf_info->table_name;
                $fieldName = $pdf_info->field_name;
                break;
            case 9: // Office Permission Cancellation (9)
                $pdf_info = PdfServiceInfo::where('certificate_name', 'opc_commercial')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                if (empty($pdf_info)) {
                    return false;
                }
                $tableName = $pdf_info->table_name;
                $fieldName = $pdf_info->field_name;
                break;
            case 10: //Visa Recommendation Amendment(10)
                $appInfo = ProcessList::leftJoin('vra_apps as app', 'app.id', '=', 'process_list.ref_id') // according new visa type
                ->where('process_list.ref_id', $app_id)
                    ->where('process_list.process_type_id', $process_type_id)
                    ->first(['app.app_type_id as app_type_id']);
                if (empty($appInfo)) {
                    return false;
                }

                if ($appInfo->app_type_id == 5) {
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'vr_amendment_on_arrival')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                } else {
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'vr_amendment_others')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                }

                if (empty($pdf_info)) {
                    return false;
                }
                $tableName = $pdf_info->table_name;
                $fieldName = $pdf_info->field_name;
                break;
            case 11: //Remittance Approval new(11)
                $pdf_info = PdfServiceInfo::where('certificate_name', 'remittance_new')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                if (empty($pdf_info)) {
                    return false;
                }
                $tableName = $pdf_info->table_name;
                $fieldName = $pdf_info->field_name;
                break;
            case 12: //Bida Registration Amendment
                $pdf_info = PdfServiceInfo::where('certificate_name', 'bida_registration_amendment')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                if (empty($pdf_info)) {
                    return false;
                }
                $tableName = $pdf_info->table_name;
                $fieldName = $pdf_info->field_name;
                break;
            case 13: //IRC Recommendation New
                $appInfo = ProcessList::leftJoin('irc_apps as app', 'app.id', '=', 'process_list.ref_id')
                    ->where('process_list.ref_id', $app_id)
                    ->where('process_list.process_type_id', $process_type_id)
                    ->first(['app.irc_purpose_id as app_purpose_id']);
                if (empty($appInfo)) {
                    return false;
                }

                if ($appInfo->app_purpose_id == 1) { // 1 = raw materials
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'irc_rec_1st_adhoc_raw_materials')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                } else if ($appInfo->app_purpose_id == 2) { //2 =spare parts
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'irc_rec_1st_adhoc_spare_parts')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                } else if ($appInfo->app_purpose_id == 3) { // 3 = both
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'irc_rec_1st_adhoc_both')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                }

                if (empty($pdf_info)) {
                    return false;
                }
                $tableName = $pdf_info->table_name;
                $fieldName = $pdf_info->field_name;
                break;
            case 14: //IRC Recommendation 2nd adhoc
                $appInfo = ProcessList::leftJoin('irc_2nd_apps as app', 'app.id', '=', 'process_list.ref_id')
                    ->where('process_list.ref_id', $app_id)
                    ->where('process_list.process_type_id', $process_type_id)
                    ->first(['app.irc_purpose_id as app_purpose_id']);
                if (empty($appInfo)) {
                    return false;
                }

                if ($appInfo->app_purpose_id == 1) { // 1 = raw materials
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'irc_rec_2nd_adhoc_raw_materials')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                } else if ($appInfo->app_purpose_id == 2) { //2 =spare parts
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'irc_rec_2nd_adhoc_spare_parts')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                } else if ($appInfo->app_purpose_id == 3) { // 3 = both
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'irc_rec_2nd_adhoc_both')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                }

                if (empty($pdf_info)) {
                    return false;
                }
                $tableName = $pdf_info->table_name;
                $fieldName = $pdf_info->field_name;
                break;
            case 15: //IRC Recommendation 3rd adhoc
                $appInfo = ProcessList::leftJoin('irc_3rd_apps as app', 'app.id', '=', 'process_list.ref_id')
                    ->where('process_list.ref_id', $app_id)
                    ->where('process_list.process_type_id', $process_type_id)
                    ->first(['app.irc_purpose_id as app_purpose_id']);
                if (empty($appInfo)) {
                    return false;
                }

                if ($appInfo->app_purpose_id == 1) { // 1 = raw materials
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'irc_rec_3rd_adhoc_raw_materials')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                } else if ($appInfo->app_purpose_id == 2) { //2 =spare parts
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'irc_rec_3rd_adhoc_spare_parts')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                } else if ($appInfo->app_purpose_id == 3) { // 3 = both
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'irc_rec_3rd_adhoc_both')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                }

                if (empty($pdf_info)) {
                    return false;
                }
                $tableName = $pdf_info->table_name;
                $fieldName = $pdf_info->field_name;
                break;
                case 16: //IRC Recommendation Regular
                    $appInfo = ProcessList::leftJoin('irc_regular_apps as app', 'app.id', '=', 'process_list.ref_id')
                        ->where('process_list.ref_id', $app_id)
                        ->where('process_list.process_type_id', $process_type_id)
                        ->first(['app.irc_purpose_id as app_purpose_id']);
                    if (empty($appInfo)) {
                        return false;
                    }
    
                    if ($appInfo->app_purpose_id == 1) { // 1 = raw materials
                        $pdf_info = PdfServiceInfo::where('certificate_name', 'irc_rec_regular_adhoc_raw_materials')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                    } else if ($appInfo->app_purpose_id == 2) { //2 =spare parts
                        $pdf_info = PdfServiceInfo::where('certificate_name', 'irc_rec_regular_adhoc_spare_parts')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                    } else if ($appInfo->app_purpose_id == 3) { // 3 = both
                        $pdf_info = PdfServiceInfo::where('certificate_name', 'irc_rec_regular_adhoc_both')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                    }
    
                    if (empty($pdf_info)) {
                        return false;
                    }
                    $tableName = $pdf_info->table_name;
                    $fieldName = $pdf_info->field_name;
                    break;

            case 17: //VIP Lounge
                $pdf_info = PdfServiceInfo::where('certificate_name', 'vipl_certificate')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                if (empty($pdf_info)) {
                    return false;
                }
                $tableName = $pdf_info->table_name;
                $fieldName = $pdf_info->field_name;
                break;
            case 19: //Waiver Condition 7
                $pdf_info = PdfServiceInfo::where('certificate_name', 'waiver_condition_7')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                if (empty($pdf_info)) {
                    return false;
                }
                $tableName = $pdf_info->table_name;
                $fieldName = $pdf_info->field_name;

                break;
            case 20: //Waiver Condition 8
                $pdf_info = PdfServiceInfo::where('certificate_name', 'waiver_condition_8')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                if (empty($pdf_info)) {
                    return false;
                }
                $tableName = $pdf_info->table_name;
                $fieldName = $pdf_info->field_name;

                break;
            case 21: //Import Permission New
                $pdf_info = PdfServiceInfo::where('certificate_name', 'import_permission')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                if (empty($pdf_info)) {
                    return false;
                }
                $tableName = $pdf_info->table_name;
                $fieldName = $pdf_info->field_name;

                break;
            case 102: //BIDA Registration
                $appInfo = ProcessList::leftJoin('br_apps as app', 'app.id', '=', 'process_list.ref_id')
                    ->where('process_list.ref_id', $app_id)
                    ->where('process_list.process_type_id', $process_type_id)
                    ->first(['app.organization_status_id']);
                if (empty($appInfo)) {
                    return false;
                }

                if ($appInfo->organization_status_id === 1) { // Joint Venture
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'bida_registration_joint_venture')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                } else if ($appInfo->organization_status_id === 2) { // Foreign
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'bida_registration_foreign')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                } else if ($appInfo->organization_status_id === 3) { // Local
                    $pdf_info = PdfServiceInfo::where('certificate_name', 'bida_registration_local')->first(['pdf_server_url', 'reg_key', 'pdf_type', 'certificate_name', 'table_name', 'field_name']);
                }

                if (empty($pdf_info)) {
                    return false;
                }
                $tableName = $pdf_info->table_name;
                $fieldName = $pdf_info->field_name;
                break;
            default:
                return false;
                break;
        } // ending of switch case

        $url_store = PdfPrintRequestQueue::firstOrNew([
            'process_type_id' => $process_type_id,
            'app_id' => $app_id
        ]);

        $url_store->process_type_id = $process_type_id;
        $url_store->app_id = $app_id;
        $url_store->pdf_server_url = $pdf_info->pdf_server_url;
        $url_store->reg_key = $pdf_info->reg_key;
        $url_store->pdf_type = $pdf_info->pdf_type;
        $url_store->certificate_name = $pdf_info->certificate_name;
        $url_store->prepared_json = 0;
        $url_store->table_name = $tableName;
        $url_store->field_name = $fieldName;
        $url_store->url_requests = '';
        $url_store->job_sending_status = 0;
        $url_store->no_of_try_job_sending = 0;
        $url_store->job_receiving_status = 0;
        $url_store->no_of_try_job_receving = 0;
        if ($certificate_type == 'generate') {
            $url_store->signatory = Auth::user()->id;

            // Store approver information
            storeSignatureQRCode($process_type_id, $app_id, 0, $approver_desk_id, 'final');
        }

        $url_store->updated_at = date('Y-m-d H:i:s');
        return $url_store->save();
    }
}
