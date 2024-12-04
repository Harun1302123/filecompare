<?php

namespace App\Modules\IrcRecommendationThirdAdhoc\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class ThirdInspectionAnnualProductionSpareParts extends Model {

    protected $table = 'irc_3rd_inspection_annual_production_spare_parts';
    protected $fillable = [
        'id',
        'app_id',
        'inspection_id',
        'product_name',
        'fixed_production',
        'half_yearly_production',
        'half_yearly_import',
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
