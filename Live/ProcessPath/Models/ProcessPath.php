<?php

namespace App\Modules\ProcessPath\Models;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\CommonFunction;

class ProcessPath extends Model
{
    protected $table = 'process_path';

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