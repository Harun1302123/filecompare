<?php

namespace App\Modules\IrcRecommendationSecondAdhoc\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class IrcPurpose extends Model {
    protected $table = 'irc_purpose';
    protected $fillable = [
        'id',
        'purpose'
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

}
