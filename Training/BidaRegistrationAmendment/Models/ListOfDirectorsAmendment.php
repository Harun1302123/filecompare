<?php 

namespace App\Modules\BidaRegistrationAmendment\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Model;

class ListOfDirectorsAmendment extends Model {
    protected $table = 'list_of_director_amendment';
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

        'n_nationality_type',
        'n_identity_type',
        'n_l_director_name',
        'n_l_director_designation',
        'n_l_director_nationality',
        'n_nid_etin_passport',
        'n_gender',
        'n_date_of_birth',
        'n_passport_type',
        'n_date_of_expiry',
        'n_passport_scan_copy',

        'amendment_type',
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
