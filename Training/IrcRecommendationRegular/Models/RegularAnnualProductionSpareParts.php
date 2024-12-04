<?php

namespace App\Modules\IrcRecommendationRegular\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class RegularAnnualProductionSpareParts extends Model {

    protected $table = 'irc_regular_annual_production_spare_parts';
    protected $fillable = [
        'id',
        'app_id',
        'product_name',
        'hs_code',
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
