<?php

namespace App\Modules\BidaRegistrationAmendment\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class AnnualProductionCapacityAmendment extends Model {
    protected $table = 'annual_production_capacity_amendment';
    protected $fillable = [
        'id',
        'app_id',
        'process_type_id',
        'product_name',
        'quantity_unit',
        'quantity',
        'price_usd',
        'price_taka',
        'n_product_name',
        'n_quantity_unit',
        'n_quantity',
        'n_price_usd',
        'n_price_taka',
        'amendment_type',
        'status',
        'is_archive',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
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

    /*     * *****************************End of Model Class********************************** */
}
