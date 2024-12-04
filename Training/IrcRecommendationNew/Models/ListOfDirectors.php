<?php 

namespace App\Modules\IrcRecommendationNew\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class ListOfDirectors extends Model {
    protected $table = 'list_of_directors';
    protected $fillable = [
        'id',
        'app_id',
        'process_type_id',
        'nationality_type',
        'identity_type',
        'l_director_name',
        'l_director_designation',
        'l_director_nationality',
        'nid_etin_passport',
        'gender',
        'date_of_birth',
        'passport_type',
        'date_of_expiry',
        'passport_scan_copy',
        'status',
        'is_archive',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
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
