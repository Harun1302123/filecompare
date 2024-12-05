<?php

namespace App\Modules\IrcRecommendationSecondAdhoc\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class IrcSalesStatement extends Model {

    protected $table = 'irc_sales_statement';
    protected $fillable = [
        'id',
        'process_type_id',
        'app_id',
        'sales_statement_date',
        'sales_value_bdt',
        'sales_vat_bdt',
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