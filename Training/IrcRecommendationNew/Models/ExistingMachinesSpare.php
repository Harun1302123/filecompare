<?php

namespace App\Modules\IrcRecommendationNew\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class ExistingMachinesSpare extends Model {

    protected $table = 'irc_existing_machines_spare';
    protected $fillable = [
        'id',
        'app_id',
        'lc_no',
        'lc_date',
        'lc_value_currency',
        'value_bdt',
        'lc_bank_branch',
        'attachment',
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
