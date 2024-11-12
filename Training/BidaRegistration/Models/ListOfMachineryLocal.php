<?php 

namespace App\Modules\BidaRegistration\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class ListOfMachineryLocal extends Model {
    protected $table = 'br_list_of_machinery_local';
    protected $fillable = [
        'id',
        'app_id',
        'process_type_id',
        'l_machinery_local_name',
        'l_machinery_local_qty',
        'l_machinery_local_unit_price',
        'l_machinery_local_total_value',
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
