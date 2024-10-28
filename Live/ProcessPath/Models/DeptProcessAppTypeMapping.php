<?php

namespace App\Modules\ProcessPath\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Libraries\CommonFunction;

class DeptProcessAppTypeMapping extends Model
{
    protected $table = 'dept_process_app_type_mapping';

    protected $primaryKey = 'id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function boot() {
        parent::boot();
        static::creating(function($post) {
            $post->created_by = Auth::user()->id;
            $post->updated_by = CommonFunction::getUserId();
        });

        static::updating(function($post) {
            $post->updated_by = CommonFunction::getUserId();
        });
    }
}
