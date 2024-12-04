<?php

namespace App\Modules\IrcRecommendationSecondAdhoc\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class IrcBrAnnualProductionCapacity extends Model {

    protected $table = 'irc_br_annual_production_capacity';
    protected $fillable = [
        'id',
        'process_type_id',
        'app_id',
        'product_name',
        'quantity_unit',
        'quantity',
        'price_usd',
        'price_taka',
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
