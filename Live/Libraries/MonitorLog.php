<?php
namespace App\Libraries;

use App\CronJobAudit;

class MonitorLog
{
    public static function cronAuditSave($file_path='', $record_index=0, $no_of_record=0, $comments=''){

        date_default_timezone_set("Asia/Dhaka");
        $file_path_array = explode('/',$file_path);
        $file_name = end($file_path_array);
        $file_name = trim($file_name);

        $CronJobAudit = CronJobAudit::firstOrNew(['file_name' => $file_name, 'comments' => $comments]);
        $CronJobAudit->full_address = $file_path;
        $CronJobAudit->record_index = 0;
        $CronJobAudit->no_of_record = $no_of_record;
        $CronJobAudit->cron_run_time = date('Y-m-d H:i:s',time());
        $CronJobAudit->save();
    }
}