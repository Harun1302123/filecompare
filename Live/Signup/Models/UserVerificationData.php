<?php namespace App\Modules\Signup\Models;

use Illuminate\Database\Eloquent\Model;

class UserVerificationData extends Model
{
    protected $table = 'users_verification_data';

    protected $fillable = array(
        'user_email', 'nationality_type', 'identity_type', 'nid_info', 'eTin_info', 'passport_info'
    );
}
