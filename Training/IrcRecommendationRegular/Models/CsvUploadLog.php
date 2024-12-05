<?php

namespace App\Modules\IrcRecommendationRegular\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CsvUploadLog extends Model {

    protected $table = 'csv_upload_log';
    protected $fillable = [
        'id',
        'file_name',
        'file_path',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'
    ];

    public static function boot()
    {
        if (Auth::user()) {
            parent::boot();

            static::creating(function ($post) {
                $post->created_by = CommonFunction::getUserId();
                $post->updated_by = CommonFunction::getUserId();
            });

            static::updating(function ($post) {
                $post->updated_by = CommonFunction::getUserId();
            });
        }
    }

    public static function getCsvList()
    {
        DB::statement(DB::raw('set @serial=0'));

        if(Auth::user()->user_type == '4x404'){
            $assigned_factory_ids = CommonFunction::getAssignedFactoryIds();
            $result = CsvUploadLog::leftJoin('users as u','u.id','=','csv_upload_log.created_by')
                ->leftJoin('factory_info','factory_info.id','=','csv_upload_log.factory_id')
                ->whereIn('csv_upload_log.factory_id', $assigned_factory_ids)
                ->orderBy('csv_upload_log.id', 'desc')
                ->get([
                    'csv_upload_log.id',
                    'csv_upload_log.file_name',
                    'csv_upload_log.file_path',
                    'csv_upload_log.created_by',
                    'csv_upload_log.created_at as upload_date',
                    'u.user_full_name as uploaded_by',
                    'factory_info.factory_name',
                    DB::raw('@serial  := @serial  + 1 AS serial')
                ]);
        }else{
            $result = CsvUploadLog::leftJoin('users as u','u.id','=','csv_upload_log.created_by')
                ->leftJoin('factory_info','factory_info.id','=','csv_upload_log.factory_id')
                ->orderBy('csv_upload_log.id', 'desc')
                ->get([
                    'csv_upload_log.id',
                    'csv_upload_log.file_name',
                    'csv_upload_log.file_path',
                    'csv_upload_log.created_by',
                    'csv_upload_log.created_at as upload_date',
                    'u.user_full_name as uploaded_by',
                    'factory_info.factory_name',
                    DB::raw('@serial  := @serial  + 1 AS serial')
                ]);
        }

        return $result;

    }

    /*     * ******************End of Model Class***************** */
}