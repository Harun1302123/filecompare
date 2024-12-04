<?php

namespace App\Modules\IrcRecommendationRegular\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class RegularRawMaterial extends Model {

    protected $table = 'irc_regular_raw_material';
    protected $fillable = [
        'id',
        'app_id',
        'apc_product_id',
        'product_name',
        'hs_code',
        'quantity',
        'quantity_unit',
        'percent',
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
