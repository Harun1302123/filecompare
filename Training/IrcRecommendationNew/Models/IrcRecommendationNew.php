<?php

namespace App\Modules\IrcRecommendationNew\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class IrcRecommendationNew extends Model {
    protected $table = 'irc_apps';

    protected $guarded = ['id'];

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