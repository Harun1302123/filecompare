<?php

namespace App\Modules\IrcRecommendationRegular\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class IrcSixMonthsImportRawMaterialAmendment extends Model {

    protected $table = 'irc_six_months_import_capacity_raw_amendment';
    protected $fillable = [
        'id',
        'process_type_id',
        'app_id',
        'n_product_name',
        'n_quantity_unit',
        'n_yearly_production',
        'n_half_yearly_production',
        'n_half_yearly_import',
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
