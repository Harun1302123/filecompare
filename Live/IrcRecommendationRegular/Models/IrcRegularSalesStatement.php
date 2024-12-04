<?php

namespace App\Modules\IrcRecommendationRegular\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class IrcRegularSalesStatement extends Model {

    protected $table = 'irc_regular_sales_statement';
    protected $fillable = [
        'id',
        'process_type_id',
        'app_id',
        'sales_statement_from_date',
        'sales_statement_to_date',
        'sales_value_bdt',
        'sales_vat_bdt',
        'sales_attachment',
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
