<?php

namespace App\Modules\IrcRecommendationThirdAdhoc\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class IrcSixMonthsImportRawMaterial extends Model {

    protected $table = 'irc_six_months_import_capacity_raw';
    protected $fillable = [
        'id',
        'process_type_id',
        'app_id',
        'product_name',
        'quantity_unit',
        'yearly_production',
        'half_yearly_production',
        'half_yearly_import',
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
