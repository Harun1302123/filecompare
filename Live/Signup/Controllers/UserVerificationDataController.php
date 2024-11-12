<?php


namespace App\Modules\Signup\Controllers;


use App\Http\Controllers\Controller;
use App\Modules\Signup\Models\UserVerificationData;
use Illuminate\Support\Facades\Session;

class UserVerificationDataController extends Controller
{
    /**
     * @param $user_email
     */
    public function storeUserVerificationData($user_email)
    {
        $userVerificationData = UserVerificationData::firstOrNew([
            'user_email' => $user_email
        ]);
        $userVerificationData->nationality_type = \session('nationality_type');
        $userVerificationData->identity_type = \session('identity_type');
        $userVerificationData->nid_info = Session::has('nid_info') ? Session::get('nid_info') : '';
        $userVerificationData->eTin_info = Session::has('eTin_info') ? Session::get('eTin_info') : '';
        $userVerificationData->passport_info = Session::has('passport_info') ? Session::get('passport_info') : '';
        $userVerificationData->save();
    }

    /**
     * @param $user_email
     */
    public function removeUserVerificationData($user_email)
    {
        UserVerificationData::where('user_email', $user_email)->delete();
    }

    public function updateNidTinPassportUserVerificationData($user_email)
    {
        UserVerificationData::where('user_email', $user_email)
            ->update([
                'nid_info' => null,
                'eTin_info' => null,
                'passport_info' => null,
            ]);
    }
}