<?php 

namespace App\Modules\ImportPermission\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class ListOfMachineryImportedSpareParts extends Model {
    protected $table = 'ip_list_of_machinery_imported_spare_parts';
    protected $fillable = [
        'id',
        'app_id',
        'process_type_id',
        'name',
        'quantity',
        'remaining_quantity',
        'required_quantity',
        'machinery_type',
        'hs_code',
        'bill_loading_no',
        'bill_loading_date',
        'invoice_no',
        'invoice_date',
        'total_value_as_per_invoice',
        'total_value_equivalent_usd',
        'total_value_ccy',
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
