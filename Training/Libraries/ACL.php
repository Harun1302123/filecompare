<?php

namespace App\Libraries;

use App\Modules\ProcessPath\Models\ProcessList;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\Session;

class ACL
{
//    public static function db_reconnect()
//    {
//        if (Session::get('DB_MODE') == 'PRODUCTION') {
////        DB::purge('mysql-main');
////        DB::setDefaultConnection('mysql-main');
////        DB::setDefaultConnection(Session::get('mysql_access'));
//        }
//    }

    public static function hasUserModificationRight($userType, $right, $id)
    {
        try {
            $userId = CommonFunction::getUserId();
            if ($userType == '1x101')
                return true;

            if ($userId == $id)
                return true;

            return false;
        } catch (\Exception $e) {
            dd(CommonFunction::showErrorPublic($e->getMessage()));
            return false;
        }
    }

    public static function hasApplicationModificationRight($processTypeId, $id, $right)
    {
        try {
            $companyIds = CommonFunction::getUserCompanyWithZero();
            if ($right != 'E') {
                return true;
            } else {
                $processListData = ProcessList::where('ref_id', $id)->where('process_type_id', $processTypeId)->first(['company_id', 'status_id']);
                if ($processListData == null) {
                    return false;
                } elseif (in_array($processListData->company_id, $companyIds) && in_array($processListData->status_id, [-1, 5])) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (\Exception $e) {
            dd(CommonFunction::showErrorPublic($e->getMessage()));
            return false;
        }
    }

//    public static function hasCertificateModificationRight($right, $id)
//    {
//        try {
//            if ($right != 'E')
//                return true;
//            $info = UploadedCertificates::where('uploaded_certificates.doc_id', $id)->first(['company_id']);
//            $companyIds = CommonFunction::getUserCompanyWithZero();
//            if (in_array($info->company_id, $companyIds)) {
//                return true;
//            }
//            return false;
//        } catch (\Exception $e) {
//            dd(CommonFunction::showErrorPublic($e->getMessage()));
//            return false;
//        }
//    }

    public static function getAccsessRight($module, $right = '', $id = null)
    {
        $accessRight = '';
        if (Auth::user()) {
            $user_type = Auth::user()->user_type;
            $user_desk_ids = CommonFunction::getUserDeskIds();
        } else {
            die('You are not authorized user or your session has been expired!');
        }
        switch ($module) {
            case 'SonaliPayment':
                if (in_array($user_type, ['1x101', '2x202'])) {
                    $accessRight = '-A-V-E-';
                } elseif ($user_type == '5x505') {
                    /*
                     * CPC = Counter Payment Cancel
                     * CPCR = Counter Payment Confirmation Request
                     */
                    $accessRight = '-CPC-CPCR-';
                }
                break;
            case 'settings':
                if (in_array($user_type, ['1x101', '2x202'])) {
                    $accessRight = 'AVE PPR-ESQ ARB';
                } elseif ($user_type == '4x404' && !in_array(20, $user_desk_ids)) {
                    // PPR = PDF print request
                    // ESQ = Email SMS queue
                    // ARB = Application Rollback
                    $accessRight = 'PPR-ESQ ARB';
                }
                break;
            case 'dashboard':
                if ($user_type == '1x101' || $user_type == '2x202') {
                    $accessRight = 'AVESERN';
                } elseif ($user_type == '5x505') {
                    $accessRight = 'AVESERNH';
                } elseif ($user_type == '13x131') {
                    $accessRight = 'AVESERNH';
                }
                break;
            case 'user':
                if ($user_type == '1x101') {
                    $accessRight = '-A-V-E-R-APV-REJ-';
                } else if ($user_type == '2x202') {//IT Help Desk
                    $accessRight = '-A-V-E-R-';
                } else if ($user_type == '4x404' && !in_array(20, $user_desk_ids)) {
                    $accessRight = '-V-R-APV-REJ-';
                } else if ($user_type == '4x404' && in_array(20, $user_desk_ids)) {
                    $accessRight = '-V-';
                } else if ($user_type == '6x606' && in_array(24, $user_desk_ids)) {
                    $accessRight = '-V-';
                } else if ($user_type == traineeUserType()) {
                    $accessRight = '-V-';
                } else if ($user_type == '14x141') {//Programmer
                    $accessRight = '-A-V-E-R-';
                } else if ($user_type == '5x505') {
                    $accessRight = '-V-R-APV-REJ-';
                } else if ($user_type == '15x151') {//MIS User
                    $accessRight = '-A-V-E-R-';
                } else if ($user_type == '3x308') {//MIS BIDA
                    $accessRight = '-A-V-E-R-';
                }
                if ($right == "SPU") {
                    if (ACL::hasUserModificationRight($user_type, $right, $id))
                        return true;
                }
                break;
            case 'CompanyAssociation':
                if (in_array($user_type, ['1x101', '2x202'])) {
                    $accessRight = '-V-UP-';
                } elseif ($user_type == '5x505') {
                    $accessRight = '-A-V-';
                }
                break;
            case 'processPath':
                if ($user_type == '1x101') {
                    $accessRight = '-A-V-E-';
                } else if ($user_type == '5x505') {
                    $accessRight = '-CC-';
                }
                break;
            case 'VisaRecommendation':
            case 'VisaRecommendationAmendment':
            case 'WorkPermitNew':
            case 'WorkPermitExtension':
            case 'WorkPermitAmendment':
            case 'WorkPermitCancellation':
            case 'WaiverCondition7':
            case 'WaiverCondition8':
            case 'VipLounge':
                if (in_array($user_type, ['1x101', '2x202', '7x707', '8x808', '13x303'])) {
                    $accessRight = '-V-';
                } else if ($user_type == '5x505') {
                    $accessRight = '-A-E-V-';
                    if ($id != null && !(strpos($accessRight, $right) === false)) {
                        if (ACL::hasApplicationModificationRight('3', $id, $right) == false)
                            return false;
                    }
                } else if (in_array($user_type, ['3x303', '6x606', '7x707', '9x909', '13x303']) || ($user_type == '4x404' && !in_array(20, $user_desk_ids))) {
                    $accessRight = '-V-UP-';
                }
                break;
            case 'BasicInformation':
                if (in_array($user_type, ['1x101', '2x202', '7x707', '8x808', '13x303'])) {
                    $accessRight = '-V-CD-';
                } else if ($user_type == '5x505') {
                    $accessRight = '-A-V-E-';
                } else if ($user_type == '4x404' && !in_array(20, $user_desk_ids)) {
                    $accessRight = '-V-UP-CD-';
                } else if (in_array($user_type, ['6x606', '7x707', '9x909'])) {
                    $accessRight = '-V-UP-';
                }
                break;
            case 'LicenceApplication':
            case 'NewReg':
            case 'CompanyRegSingleForm':
            case 'BidaRegistration':
            case 'BidaRegistrationAmendment':
            case 'IRCRecommendationNew':
            case 'IRCRecommendationSecondAdhoc':
            case 'IRCRecommendationThirdAdhoc':
            case 'IRCRecommendationRegular':
            case 'ImportPermission':
            case 'erc':
            case 'BfscdNocExiting':
            case 'BfscdNocProposed':
                if (in_array($user_type, ['1x101', '2x202', '7x707', '8x808', '13x303'])) {
                    $accessRight = '-V-';
                } else if ($user_type == '5x505') {
                    $accessRight = '-A-V-E-';
                } else if (in_array($user_type, ['6x606', '7x707', '9x901']) || ($user_type == '4x404' && !in_array(20, $user_desk_ids))) {
                    $accessRight = '-V-UP-';
                }
                break;
            case 'NameClearance':
            case 'IndustrialIrc':
            case 'CdaForm':
            case 'VATReg':
            case 'NewConectionBPDB':
            case 'NewConnectionDPDC':
            case 'NewConnectionBREB':
            case 'DOE':
            case 'industrialIRC':
            case 'NewConnectionNESCO':
            case 'NewConnectionDESCO':
            case 'LsppCDA':
            case 'CdaOc':
            case 'BccCDA':
            case 'TradeLicenseDSCC':
            case 'CTCC':
            case 'SBaccount':
            case 'CityBankAccount':
            case 'eTINforeigner':
            case 'NewConnectionWZPDCL':
            case 'DNCC':
            case 'WasaNewConnection':
            case 'WasaDeepTubewell':
            case 'DCCI_COS':
            case 'RajukLUCGeneral':
            case 'MutationLand':
            case 'LimaFactoryLayout':
            case 'CompanyRegistration':
            case 'CompanyRegistrationForeign':
            case 'ExternalService':
                if (in_array($user_type, ['1x101', '2x202', '13x303']) || ($user_type == '4x404' && !in_array(20, $user_desk_ids))) {
                    $accessRight = '-V-';
                } else if ($user_type == '5x505') {
                    $accessRight = '-A-V-E-';
                } else if ($user_type == '9x901') {
                    $accessRight = '-V-UP-';
                }
                break;
            case 'OfficePermissionNew':
            case 'OfficePermissionExtension':
            case 'OfficePermissionAmendment':
            case 'OfficePermissionCancellation':
            case 'SingleLicence':
                if (in_array($user_type, ['1x101', '2x202', '7x707', '8x808', '13x303'])) {
                    $accessRight = '-V-';
                } else if ($user_type == '5x505') {
                    $accessRight = '-A-E-V-';
                    if ($id != null && !(strpos($accessRight, $right) === false)) {
                        if (ACL::hasApplicationModificationRight('3', $id, $right) == false)
                            return false;
                    }
                } else if (in_array($user_type, ['3x303', '6x606', '7x707', '9x909']) || ($user_type == '4x404' && !in_array(20, $user_desk_ids))) {
                    $accessRight = '-V-UP-';
                }
                break;
            case 'TradeLicence':
                if (in_array($user_type, ['1x101', '2x202', '13x303']) || ($user_type == '4x404' && !in_array(20, $user_desk_ids))) {
                    $accessRight = '-V-';
                } else if ($user_type == '5x505') {
                    $accessRight = '-A-V-E-';
                } else if ($user_type == '9x903') {
                    $accessRight = '-V-UP-';
                }
                break;
            case 'BankAccount':
                if (in_array($user_type, ['1x101', '2x202', '13x303']) || ($user_type == '4x404' && !in_array(20, $user_desk_ids))) {
                    $accessRight = '-V-';
                } else if ($user_type == '5x505') {
                    $accessRight = '-A-V-E-';
                } else if ($user_type == '9x904') {
                    $accessRight = '-V-UP-';
                }
                break;
            case 'E-tin':
                if (in_array($user_type, ['1x101', '2x202', '13x303']) || ($user_type == '4x404' && !in_array(20, $user_desk_ids))) {
                    $accessRight = '-V-';
                } else if ($user_type == '5x505') {
                    $accessRight = '-A-V-E-';
                } else if ($user_type == '9x902') {
                    $accessRight = '-V-UP-';
                }
                break;
            case 'BoardMeting':
                if (in_array($user_type, ['1x101', '2x202', '7x707', '8x808'])) {
                    $accessRight = '-V-';
                } else if ($user_type == '13x303') {
                    $accessRight = '-V-';
                } else if ($user_type == '3x303' || ($user_type == '4x404' && !in_array(20, $user_desk_ids))) {
                    $accessRight = '-A-E-V-UP-';
                }
                break;
            case 'MeetingForm':
                if ($user_type == '1x101') {
                    $accessRight = '-V-';
                } else if ($user_type == '13x303') {
                    $accessRight = '-A-V-E-UP-';
                } else if (in_array($user_type, ['6x606', '7x707', '9x909']) || ($user_type == '4x404' && !in_array(20, $user_desk_ids))) {
                    $accessRight = '-V-UP-';
                }
                break;
//            case 'certificate':
//                if ($user_type == '5x505' || $user_type == '6x606') {
//                    $accessRight = 'AVE';
//                    if ($id != null && !(strpos($accessRight, $right) === false)) {
//                        if (ACL::hasCertificateModificationRight($right, $id) == false)
//                            return false;
//                    }
//                } else if ($user_type == '1x101') {
//                    $accessRight = 'AVE';
//                }
//                break;
            case 'Remittance':
                if (in_array($user_type, ['1x101', '2x202', '7x707', '8x808', '13x303'])) {
                    $accessRight = '-V-';
                } else if ($user_type == '5x505') {
                    $accessRight = '-A-E-V-';
                    if ($id != null && !(strpos($accessRight, $right) === false)) {
                        if (ACL::hasApplicationModificationRight('11', $id, $right) == false)
                            return false;
                    }
                } else if (in_array($user_type, ['3x303', '6x606', '7x707', '9x909', '13x303']) || ($user_type == '4x404' && !in_array(20, $user_desk_ids))) {
                    $accessRight = '-V-UP-';
                }
                break;
            case 'faq':
                if ($user_type == '1x101') {
                    $accessRight = 'AVE';
                } else if ($user_type == '2x202' || $user_type == '2x205') {
                    $accessRight = 'V';
                }
                break;
            case 'search':
                if ($user_type == '1x101' || $user_type == '2x202' || $user_type == '2x203') {
                    $accessRight = 'AVE';
                } else if ($user_type == '3x300' || $user_type == '3x305') {
                    $accessRight = 'V';
                }
                break;
            case 'support':
                if ($user_type == '1x101' || $user_type == '2x202') {
                    $accessRight = '-A-V-E-';
                } else if (($user_type == '4x404' && !in_array(20, $user_desk_ids)) || $user_type == '5x505') {
                    $accessRight = '-V-';
                }
                break;
            case 'report':
                if ($user_type == '1x101' || $user_type == '15x151' || $user_type == '14x141') {
                    $accessRight = 'AVE';
                } else if ($user_type == '2x202' || $user_type == '3x308' || $user_type == '4x404' || $user_type == '5x505' || $user_type == '8x808') {
                    $accessRight = 'V';
                }
                break;
            case 'Training':
                if (in_array($user_type, ['1x101'])) {
                    $accessRight = '-A-V-E-';
                }
                break;
            case 'Training-Desk':
            if (in_array($user_type, ['4x404']) && checkUserTrainingDesk()) {
                if(checkUserTrainingDesk() == 1){
                    $accessRight = '-A-V-E-';
                }
                else if(checkUserTrainingDesk() == 2){
                    // DV = Director View
                    // DE = Director Edit
                    
                    $accessRight = '-DV-DE-';
                }
            }
            break;
            default:
                $accessRight = '';
        }

        if ($right != '') {
            if (strpos($accessRight, $right) === false) {
                return false;
            } else {
                return true;
            }
        } else {
            return $accessRight;
        }
    }

    public static function isAllowed($accessMode, $right)
    {
        if (strpos($accessMode, $right) === false) {
            return false;
        } else {
            return true;
        }
    }

    /*     * **********************************End of Class****************************************** */
}
