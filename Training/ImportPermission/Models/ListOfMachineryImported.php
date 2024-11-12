<?php 

namespace App\Modules\ImportPermission\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class ListOfMachineryImported extends Model {
    protected $table = 'ip_list_of_machinery_imported';
    protected $fillable = [
        'id',
        'app_id',
        'process_type_id',
        'l_machinery_imported_name',
        'l_machinery_imported_qty',
        'l_machinery_imported_unit_price',
        'l_machinery_imported_total_value',
        'status',
        'is_archive',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public static function boot() {
        parent::boot();
        static::creating(function($post) {
            $post->created_by = CommonFunction::getUserId();
            $post->updated_by = CommonFunction::getUserId();
        });

        static::updating(function($post) {
            $post->updated_by = CommonFunction::getUserId();
        });
    }

}
