<?php

namespace App\Modules\IrcRecommendationNew\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class ExistingMachinesLc extends Model {

    protected $table = 'irc_existing_machines_lc';
    protected $fillable = [
        'id',
        'app_id',
        'product_name',
        'quantity_unit',
        'quantity',
        'unit_price',
        'price_unit',
        'price_foreign_currency',
        'price_bdt',
        'price_taka_mil',
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
