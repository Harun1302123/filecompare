<?php 

namespace App\Modules\ImportPermission\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class MasterMachineryImported extends Model
{

    protected $table = 'master_list_of_machinery_imported';
    protected $guarded = ['id'];

    public static function boot() {
        parent::boot();
        static::creating(function($post) {
            $post->created_by    = CommonFunction::getUserId();
            $post->updated_by    = CommonFunction::getUserId();
        });

        static::updating(function($post) {
            $post->updated_by = CommonFunction::getUserId();
        });
    }


    public static function processName($name)
    {
        $processedName = strtolower(str_replace(' ', '', $name));
        return $processedName;
    }
}