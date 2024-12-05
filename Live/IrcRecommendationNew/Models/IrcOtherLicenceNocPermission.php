<?php

namespace App\Modules\IrcRecommendationNew\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class IrcOtherLicenceNocPermission extends Model {

    protected $table = 'irc_other_licence_noc_permission';
    protected $fillable = [
        'id',
        'app_id',
        'licence_name',
        'licence_no',
        'issuing_authority',
        'issue_date',
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