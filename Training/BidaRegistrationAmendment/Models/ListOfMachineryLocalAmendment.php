<?php 

namespace App\Modules\BidaRegistrationAmendment\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class ListOfMachineryLocalAmendment extends Model {
    protected $table = 'list_of_machinery_local_amendment';
    protected $fillable = [
        'id',
        'app_id',
        'process_type_id',
        'l_machinery_local_name',
        'l_machinery_local_qty',
        'l_machinery_local_unit_price',
        'l_machinery_local_total_value',
        'n_l_machinery_local_name',
        'n_l_machinery_local_qty',
        'n_l_machinery_local_unit_price',
        'n_l_machinery_local_total_value',
        'amendment_type',
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
