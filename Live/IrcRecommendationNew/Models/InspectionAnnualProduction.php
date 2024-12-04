<?php

namespace App\Modules\IrcRecommendationNew\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class InspectionAnnualProduction extends Model {

    protected $table = 'irc_inspection_annual_production';
    protected $fillable = [
        'id',
        'app_id',
        'inspection_id',
        'product_name',
        'unit_of_product',
        'fixed_production',
        'half_yearly_production',
        'half_yearly_import',
        'raw_material_total_price',
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
