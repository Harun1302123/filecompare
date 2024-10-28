<?php

namespace App\Modules\ProcessPath\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;
class Desk extends Model
{
    protected $table = 'user_desk';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
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