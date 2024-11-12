<?php

namespace App\Libraries;

use App\BRCommonPool;
use App\IRCCommonPool;
use App\Modules\API\Models\ApiErrorInfo;
use App\Modules\Apps\Models\pdfSignatureQrcode;
use App\Modules\BidaRegistration\Models\BidaRegistration;
use App\Modules\BidaRegistration\Models\ListOfDirectors;
use App\Modules\BidaRegistration\Models\ListOfMachineryImported;
use App\Modules\BidaRegistration\Models\ListOfMachineryLocal;
use App\Modules\BidaRegistrationAmendment\Models\BidaRegistrationAmendment;
use App\Modules\BidaRegistrationAmendment\Models\ListOfDirectorsAmendment;
use App\Modules\BidaRegistrationAmendment\Models\ListOfMachineryImportedAmendment;
use App\Modules\BidaRegistrationAmendment\Models\ListOfMachineryLocalAmendment;
use App\Modules\IrcRecommendationNew\Models\IrcRecommendationNew;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Services\BRCommonPoolManager;
use App\Modules\ProcessPath\Services\IRCCommonPoolManager;
use App\Modules\ProcessPath\Services\OPCommonPoolManager;
use App\Modules\ProcessPath\Services\VRCommonPoolManager;
use App\Modules\ProcessPath\Services\WPCommonPoolManager;
use App\Modules\Reports\Models\FavReports;
use App\Modules\Reports\Models\Reports;
use App\Modules\Reports\Models\ReportsMapping;
use App\Modules\Signup\Models\UserVerificationOtp;
use App\Modules\Users\Models\AreaInfo;
use App\Modules\Users\Models\Countries;
use App\Modules\Users\Models\Users;
use App\OPCommonPool;
use App\VRCommonPool;
use App\WPCommonPool;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use Illuminate\Support\Facades\URL;
use Mpdf\Mpdf;

class UtilFunction
{
    const BIDA_REGISTRATION_PROCESS_TYPE = 102;

    const BIDA_REGISTRATION_AMENDMENT_PROCESS_TYPE = 12;

    const APPROVED_PROCESS_STATUS = 25;

    public static function processVerifyData($applicationInfo)
    {
        return '#P' . $applicationInfo->process_type_id . '#AI' . $applicationInfo->id . '#D' . $applicationInfo->desk_id . '#S' . $applicationInfo->status_id;
    }

    public static function isReportAdmin()
    {
        return ['1x101', '15x151'];
    }

    public static function isAllowedToViewFvrtReport($report_id)
    {
        if (in_array(Auth::user()->user_type, ['1x101', '15x151'])) // report admin
        {
            return true;
        }
        $is_fvrt = FavReports::where('report_id', $report_id)
            ->where('user_id', Auth::user()->id)
            ->count();
        if ($is_fvrt > 0) {
            $is_publish = Reports::where([
                'report_id' => $report_id,
                'status' => 1
            ])->count();
            $is_assigned = ReportsMapping::where([
                'report_id' => $report_id,
                'user_type' => Auth::user()->user_type
            ])->count();
            if ($is_publish == 0 || $is_assigned == 0) {
                return false;
            }
        }
        return true;
    }

    public static function checkBRCommonPoolData($ref_app_tracking_no, $ref_id)
    {
        $tracking_column = Self::getRefAppServiceName($ref_app_tracking_no);

        $BRCommonPoolDataCount = BRCommonPool::where($tracking_column, $ref_app_tracking_no)->count();

        if ($BRCommonPoolDataCount < 1) {
            $histQry = DB::table('br_common_pool_hist')->where($tracking_column, $ref_app_tracking_no);
            $BRCommonPoolDataCount = $histQry->count();
            if($BRCommonPoolDataCount > 0){
                $BRCommonPoolDataHist= $histQry->first();
                $brCommonPool = BRCommonPool::where('br_tracking_no', $BRCommonPoolDataHist->br_tracking_no)->first();
                $ref_app_tracking_no = $brCommonPool ? $brCommonPool->bra_tracking_no : $ref_app_tracking_no;
            }
        }

        if ($tracking_column == 'br_tracking_no' && $BRCommonPoolDataCount < 1) {
            BRCommonPoolManager::BRDataStore($ref_app_tracking_no, $ref_id);
        } elseif ($tracking_column == 'bra_tracking_no' && $BRCommonPoolDataCount < 1) {
            BRCommonPoolManager::BRADataStore($ref_app_tracking_no, $ref_id);
        }
        return BRCommonPool::where($tracking_column, $ref_app_tracking_no)->first();
    }

    public static function checkWpCommonPoolData($ref_app_tracking_no, $ref_id)
    {
        $tracking_column = Self::getRefAppServiceName($ref_app_tracking_no);

        $wpCommonPoolDataCount = WPCommonPool::where($tracking_column, $ref_app_tracking_no)->count();

        if ($tracking_column == 'wpn_tracking_no' && $wpCommonPoolDataCount < 1) {
            WPCommonPoolManager::wpnDataStore($ref_app_tracking_no, $ref_id);
        } elseif ($tracking_column == 'wpe_tracking_no' && $wpCommonPoolDataCount < 1) {
            WPCommonPoolManager::wpeDataStore($ref_app_tracking_no, $ref_id);
        }

        return WPCommonPool::where($tracking_column, $ref_app_tracking_no)->first();
    }

    public static function checkVRCommonPoolData($ref_app_tracking_no, $ref_id)
    {
        $tracking_column = Self::getRefAppServiceName($ref_app_tracking_no);

        $VRCommonPoolDataCount = VRCommonPool::where($tracking_column, $ref_app_tracking_no)->count();

        if ($tracking_column == 'vr_tracking_no' && $VRCommonPoolDataCount < 1) {
            VRCommonPoolManager::VRDataStore($ref_app_tracking_no, $ref_id);
        } elseif ($tracking_column == 'vra_tracking_no' && $VRCommonPoolDataCount < 1) {
            VRCommonPoolManager::VRADataStore($ref_app_tracking_no, $ref_id);
        }

        return VRCommonPool::where($tracking_column, $ref_app_tracking_no)->first();
    }

    public static function checkIRCCommonPoolData($ref_app_tracking_no, $ref_id)
    {
        $tracking_column = self::getRefAppServiceName($ref_app_tracking_no);

        $IRCCommonPoolDataCount = IRCCommonPool::where($tracking_column, $ref_app_tracking_no)->count();

        if ($tracking_column == 'first_adhoc_tracking_no' && $IRCCommonPoolDataCount < 1) {
            IRCCommonPoolManager::ircFirstAdhocDataStore($ref_app_tracking_no, $ref_id);
        } elseif ($tracking_column == 'second_adhoc_tracking_no' && $IRCCommonPoolDataCount < 1) {
            IRCCommonPoolManager::ircSecondAdhocDataStore($ref_app_tracking_no, $ref_id);
        } elseif ($tracking_column == 'third_adhoc_tracking_no' && $IRCCommonPoolDataCount < 1) {
            IRCCommonPoolManager::ircThirdAdhocDataStore($ref_app_tracking_no, $ref_id);
        } 

        return IRCCommonPool::where($tracking_column, $ref_app_tracking_no)->first();
    }

    public static function checkOpCommonPoolData($ref_app_tracking_no, $ref_id)
    {
        $tracking_no_column = self::getRefAppServiceName($ref_app_tracking_no);
        $OPCommonPoolDataCount = OPCommonPool::where($tracking_no_column, $ref_app_tracking_no)->count();

        if ($OPCommonPoolDataCount < 1) {
            if ($tracking_no_column === "opn_tracking_no") {
                OPCommonPoolManager::OPNDataStore($ref_app_tracking_no, $ref_id);
            } elseif ($tracking_no_column === "ope_tracking_no") {
                OPCommonPoolManager::OPEDataStore($ref_app_tracking_no, $ref_id);
            }
        }

        return OPCommonPool::where($tracking_no_column, $ref_app_tracking_no)->first();
    }

    public static function getRefAppServiceName($ref_app_tracking_no)
    {
        $service_name = '';
        if (!empty($ref_app_tracking_no)) {
            $ref_no = explode('-', $ref_app_tracking_no);
            if ($ref_no[0] == 'WPN') {
                $service_name = 'wpn_tracking_no';
            } elseif ($ref_no[0] == 'WPE') {
                $service_name = 'wpe_tracking_no';
            } elseif ($ref_no[0] == 'VR') {
                $service_name = 'vr_tracking_no';
            } elseif ($ref_no[0] == 'VRA') {
                $service_name = 'vra_tracking_no';
            } elseif ($ref_no[0] == 'BR') {
                $service_name = 'br_tracking_no';
            } elseif ($ref_no[0] == 'BRA') {
                $service_name = 'bra_tracking_no';
            } elseif ($ref_no[0] == 'OPN') {
                $service_name = 'opn_tracking_no';
            } elseif ($ref_no[0] == 'OPE') {
                $service_name = 'ope_tracking_no';
            } elseif ($ref_no[0] == 'IRC' && $ref_no[1] === '2') {
                $service_name = 'second_adhoc_tracking_no';
            } elseif ($ref_no[0] == 'IRC' && $ref_no[1] === '3') {
                $service_name = 'third_adhoc_tracking_no';
            } elseif ($ref_no[0] == 'IRC' && !in_array($ref_no[1], ['2', '3'])) {
                $service_name = 'first_adhoc_tracking_no';
            }
        }
        return $service_name;
    }

    public static function getVisitorRealIP()
    {
        // Get real visitor IP behind CloudFlare network
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }
        // if above code is not working properly then
        /*
        if(!empty($_SERVER['HTTP_X_REAL_IP'])){
            $ip=$_SERVER['HTTP_X_REAL_IP'];
        }
        */

        //dd($request->ip(),$request->getClientIp(), $request->REMOTE_ADDR, $ip,$_SERVER['HTTP_X_REAL_IP']);

        return $ip;

    }

    public static function geCompanyUsersEmailPhone($company_id)
    {
        return Users::whereRaw("FIND_IN_SET('$company_id', company_ids)")
            ->where('user_type', '5x505')
            ->where('user_status', 'active')
            ->where('company_ids', '!=', 0)
            ->whereNotNull('company_ids')
            ->get(['user_email', 'user_phone']);
    }

    public static function geCompanySKHUsersEmailPhone($company_id)
    {
        return Users::whereRaw("FIND_IN_SET('$company_id', company_ids)")
            ->where('user_type', '5x505')
            ->where('user_status', 'active')
            ->where('company_ids', '!=', 0)
            ->whereNotNull('company_ids')
            ->get(['user_email', 'user_phone']);
    }

    public static function getUserDivisionIds()
    {
        if (Auth::user()) {
            $divisionIds = Auth::user()->division_id;
            $userDivisionIds = explode(',', $divisionIds);
            return $userDivisionIds;
        } else {
            return [0];
        }
    }

    public static function IrcCcieSubmitted($app_tracking_no, $stakeholder_status_id)
    {
        if (empty($app_tracking_no) || empty($stakeholder_status_id)) {
            return [
                'status' => 'error',
                'msg' => 'Request empty'
            ];
        }

        $processInfo = ProcessList::leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
            ->where('process_list.tracking_no', $app_tracking_no)
            ->first([
                'process_type.name as process_type_name',
                'process_type.process_supper_name',
                'process_type.process_sub_name',
                'process_list.ref_id',
                'process_list.status_id',
                'process_list.company_id',
                'process_list.process_type_id',
                'process_list.tracking_no',
            ]);

        if (empty($processInfo)) {
            return [
                'status' => 'error',
                'msg' => 'Process data not found'
            ];
        }

        switch ($processInfo->process_type_id) {
            case 13: // IRC 1st adhoc

                $appData = IrcRecommendationNew::find($processInfo->ref_id);

                if ($appData->is_ccie_submitted_irc_1st == 1) {
                    return [
                        'status' => 'error',
                        'msg' => 'Duplicate request. Your data already updated.'
                    ];
                }

                $requestData['tracking_no'] = $app_tracking_no;
                $requestData['status_id'] = $stakeholder_status_id;
                $requestData['request_ip'] = UtilFunction::getVisitorRealIP();
                $requestData['datetime'] = Carbon::now();

                $appData->is_ccie_submitted_irc_1st = 1;
                $appData->ccie_submitted_json = json_encode($requestData);
                $updatedAppData = $appData->save();

                $updateCommonPoolData = IRCCommonPool::where('first_adhoc_tracking_no', $processInfo->tracking_no)->update([
                    'is_ccie_submitted_irc_1st' => 1
                ]);

                if ($updatedAppData && $updateCommonPoolData) {
                    $applicantEmailPhone = UtilFunction::geCompanyUsersEmailPhone($processInfo->company_id);

                    $appInfo = [
                        'app_id' => $processInfo->ref_id,
                        'status_id' => $processInfo->status_id,
                        'process_type_id' => $processInfo->process_type_id,
                        'tracking_no' => $processInfo->tracking_no,
                        'process_type_name' => $processInfo->process_type_name,
                        'process_supper_name' => $processInfo->process_supper_name,
                        'process_sub_name' => $processInfo->process_sub_name,
                        'remarks' => '',
                    ];
                    $appInfo['attachment_certificate_name'] = 'irc_apps.certificate_link';
                    CommonFunction::sendEmailSMS('APP_APPROVE', $appInfo, $applicantEmailPhone);
                }

                break;
            default:
                return [
                    'status' => 'error',
                    'msg' => 'Request not processed'
                ];
        }

        return [
            'status' => 'success',
            'msg' => 'Request has been processed successfully'
        ];
    }

    /**
     * @param $appId
     * @param $process_type_id
     * @param $dest
     * @return string
     * @throws \Mpdf\MpdfException
     * List of directors and machinery PDF generation for BR and IRC
     */
    public static function getListOfDirectorsAndMachinery($appId, $process_type_id, $dest)
    {
        $appInfo = ProcessList::leftJoin('br_apps as apps', 'apps.id', '=', 'process_list.ref_id')
            ->where('process_list.ref_id', $appId)
            ->where('process_list.process_type_id', $process_type_id)
            ->where('process_list.status_id', 25)
            ->first([
                'process_list.tracking_no',
                'process_list.submitted_at',
                'apps.*'
            ]);

        $thana = AreaInfo::orderby('area_nm')->where('area_type', 3)->where('area_id', $appInfo->office_thana_id)->first(['area_nm']);
        $districts = AreaInfo::where('area_type', 2)->orderBy('area_nm', 'asc')->where('area_id', $appInfo->office_district_id)->first(['area_nm']);
        $nationality = Countries::where('country_status', 'Yes')->where('nationality', '!=', '')->orderby('nationality', 'asc')->lists('nationality', 'id')->all();
        $listOfDirector = ListOfDirectors::Where('app_id', $appInfo->id)->where('process_type_id', $process_type_id)->where('status', 1)->get();
        $listOfMechineryImported = ListOfMachineryImported::Where('app_id', $appInfo->id)->where('process_type_id', $process_type_id)->where('status', 1)->get();
        $machineryImportedTotal = ListOfMachineryImported::Where('app_id', $appInfo->id)->where('process_type_id', $process_type_id)->where('status', 1)->sum('l_machinery_imported_total_value');
        $listOfMechineryLocal = ListOfMachineryLocal::Where('app_id', $appInfo->id)->where('process_type_id', $process_type_id)->where('status', 1)->get();
        $machineryLocalTotal = ListOfMachineryLocal::Where('app_id', $appInfo->id)->where('process_type_id', $process_type_id)->where('status', 1)->sum('l_machinery_local_total_value');
        $office_address_full = $appInfo->office_address . ', ' . $appInfo->office_post_office . ', ' . (!empty($thana) ? $thana->area_nm : '') . ', ' . (!empty($districts) ? $districts->area_nm : '') . ', ' . $appInfo->office_post_code;

        $director = pdfSignatureQrcode::where([
            'process_type_id' => $process_type_id,
            'app_id' => $appInfo->id,
            'signature_type' => 'final',
        ])->first();

        //Barcode generator
        $dn1d = new DNS1D();
        $trackingNo = $appInfo->tracking_no; // tracking no push on barcode.
        if (!empty($trackingNo)) {
            $barcode = $dn1d->getBarcodePNG($trackingNo, 'C39', 2, 60);
            $barcode_url = 'data:image/png;base64,' . $barcode;
        } else {
            $barcode_url = '';
        }

        //Qr code generator
        $dn2d = new DNS2D();
        if (!empty($trackingNo)) {
            $qrcode = $dn2d->getBarcodePNG($trackingNo, 'QRCODE');
            $qrcode_url = 'data:image/png;base64,' . $qrcode;
        } else {
            $qrcode_url = '';
        }

        // Qr code generator
        // $dn2d = new DNS2D();
        // if (!empty($appId) && !empty($process_type_id)) {
        //     $encodedAppId = Encryption::dataEncode($appId);
        //     $encodedProcessTypeId = Encryption::dataEncode($process_type_id);
        //     $qrcode = $dn2d->getBarcodePNG(URL::to('/').'/director-machinery-docs/'.$encodedAppId.'/'. $encodedProcessTypeId, 'QRCODE');
        //     $qrcode_url = 'data:image/png;base64,' . $qrcode;
        // } else {
        //     $qrcode_url = '';
        // }

        // Company director signature url
        if (!empty($appInfo->g_signature)) {
            if (file_exists("uploads/" . $appInfo->g_signature)) {
                $signature = '<img src="uploads/' . $appInfo->g_signature . '" alt="" width="70">';
            } else {
                $url = url('uploads', $appInfo->g_signature);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $g_signature_response = curl_exec($ch);
                $g_signature_image_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
                curl_close($ch);
                $signature = '<img src="data:' . $g_signature_image_type . ';base64,' . base64_encode($g_signature_response) . '" width="70">';
            }
        } else {
            $signature = "";
        }

        // Desk user signature url
        if (!empty($director->signature_encode)) {
            $director_signature = '<img src="data:image/jpeg;base64,' . $director->signature_encode . '" width="70">';
        } else {
            $director_signature = "";
        }

        $contents = view('BidaRegistration::downloadListOfDirector',
            compact('listOfDirector', 'machineryImportedTotal', 'nationality', 'appInfo',
                'machineryLocalTotal', 'listOfMechineryImported', 'listOfMechineryLocal'))->render();

        $mpdf = new mPDF([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font_size' => 10,
            'default_font' => 'timesnewroman',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_header' => 10,
            'margin_footer' => 10,
            'setAutoTopMargin' => 'pad',
            'setAutoBottomMargin' => 'pad'
        ]);

        //static header section
        $mpdf->SetHTMLHeader('<div class="header">
            <div class="logo_image" style="float: left; width: 140px">
               <img src="' . $qrcode_url . '" alt="QR Code" height="60" />
             </div>
             <div style="text-align: right;">
               <span style="font-size: 18px;  float: right; font-weight: bold; color: #170280;">' . ucwords(CommonFunction::getCompanyNameById($appInfo->company_id)) . '</span><br>
                   <span style="font-size: 13px; font-weight: bold">' . $office_address_full . '</span>
             </div><br>
             <div class="barcode" style="text-align: center;">
                 <img src="' . $barcode_url . '" width="25%" alt="Barcode" height="30" />
             </div>
            </div>');

        if (config('app.server_type') != 'live') {
            $mpdf->SetWatermarkText('TEST PURPOSE ONLY');
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'timesnewroman';
            $mpdf->watermarkTextAlpha = 0.1;
        }

        $mpdf->useSubstitutions;
        $mpdf->SetProtection(array('print'));
        $mpdf->SetDefaultBodyCSS('color', '#000');
        $mpdf->SetTitle("Bangladesh Investment Development Authority (BIDA)");
        $mpdf->SetSubject("Director and machinery list");
        $mpdf->SetAuthor("Business Automation Limited");
        $mpdf->autoScriptToLang = true;
        $mpdf->baseScript = 1;
        $mpdf->autoVietnamese = true;
        $mpdf->autoArabic = true;
        $mpdf->autoLangToFont = true;
        $mpdf->SetDisplayMode('fullwidth');

        //static footer section
        $mpdf->SetHTMLFooter('
            <div style="margin-top:20px;">
                <table class="table" width="100%">
                    <tr>
                        <td style="align:left; width: 75%">
                           ' . $signature . '<br>' .
            $appInfo->g_full_name . '<br>' .
            $appInfo->g_designation . '<br>' .
            ucwords(CommonFunction::getCompanyNameById($appInfo->company_id)) . '<br>' .
            $office_address_full . ' 
                        </td>
                        <td style="text-align:center;" >
                                ' . $director_signature . '<br> 
                                (' . $director->signer_name . ') <br>
                                ' . $director->signer_designation . '<br> 
                                Phone: ' . $director->signer_phone . '<br>
                                Email: ' . $director->signer_email . '
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="font-size: 9px; text-align: center">
                            <br>
                            Note: This is an authenticated system generated documents and does not require signature.<br>
                            Document generated by BIDA One Stop Service System. <i>https://bidaquickserv.org</i>
                        </td>
                    </tr>
                </table>
    
            </div>
            <table width="100%">
                <tr>
                    <td width="50%"><i style="font-size: 9px;">Download time: {DATE j-M-Y h:i a}</i></td>
                    <td width="50%" align="right"><i style="font-size: 9px;">{PAGENO}/{nbpg}</i></td>
                </tr>
            </table>');

        $stylesheet = file_get_contents('assets/stylesheets/certificate.css');
        $mpdf->setAutoTopMargin = 'stretch';
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHTML($contents, 2);
        $mpdf->defaultfooterfontsize = 9;
        $mpdf->defaultfooterfontstyle = 'B';
        $mpdf->defaultfooterline = 0;
        $mpdf->SetCompression(true);

        $pdfFilePath = self::generatePath($process_type_id);

        $mpdf->Output($pdfFilePath, $dest); // F = save // I = Open

        if ($dest == 'F') {
            if ($process_type_id == 102) {
                BidaRegistration::where('id', $appInfo->id)->update([
                    'list_of_dir_machinery_doc' => $pdfFilePath,
                ]);
            } else if ($process_type_id == 13) {
                IrcRecommendationNew::where('id', $appInfo->id)->update([
                    'list_of_dir_machinery_doc' => $pdfFilePath,
                ]);
            }

            return $pdfFilePath;
        }
    }

    /**
     * @param $appId
     * @param $processTypeId
     * @param $dest
     * @return string
     * @throws \Mpdf\MpdfException
     * List of directors and machinery PDF generation for BRA
     */
    public static function getBRAListOfDirectorsAndMachinery($appId, $processTypeId, $dest)
    {
        $appInfo = ProcessList::leftJoin('bra_apps as apps', 'apps.id', '=', 'process_list.ref_id')
            ->where('process_list.ref_id', $appId)
            ->where('process_list.process_type_id', $processTypeId)
            ->where('process_list.status_id', 25)
            ->first([
                'process_list.tracking_no',
                'process_list.submitted_at',
                'apps.id',
                'apps.company_id',
                'apps.office_address',
                'apps.office_post_office',
                'apps.office_post_code',
                'apps.office_thana_id',
                'apps.office_district_id',
                'apps.g_designation',
                'apps.g_full_name',
                'apps.g_signature',
                'apps.n_g_signature',
                'apps.n_office_address',
                'apps.n_office_post_office',
                'apps.n_office_post_code',
                'apps.n_office_thana_id',
                'apps.n_office_district_id',
                'apps.n_g_designation',
                'apps.n_g_full_name',
                'apps.created_by',
                'apps.is_approval_online',
                'apps.ref_app_tracking_no',
                'apps.manually_approved_br_no',
                'apps.approved_date',
            ]);

        $appId_and_process_type_check = [
            'app_id' => $appId,
            'process_type_id' => $processTypeId,

        ];

        $office_address = empty($appInfo->n_office_address) ? $appInfo->office_address : $appInfo->n_office_address;
        $office_post_office = empty($appInfo->n_office_post_office) ? $appInfo->office_post_office : $appInfo->n_office_post_office;
        $office_post_code = empty($appInfo->n_office_post_code) ? $appInfo->office_post_code : $appInfo->n_office_post_code;
        $office_thana_id = empty($appInfo->n_office_thana_id) ? $appInfo->office_thana_id : $appInfo->n_office_thana_id;
        $office_district_id = empty($appInfo->n_office_district_id) ? $appInfo->office_district_id : $appInfo->n_office_district_id;
        $g_designation = empty($appInfo->n_g_designation) ? $appInfo->g_designation : $appInfo->n_g_designation;
        $g_full_name = empty($appInfo->n_g_full_name) ? $appInfo->g_full_name : $appInfo->n_g_full_name;
        $g_signature = empty($appInfo->n_g_signature) ? $appInfo->g_signature : $appInfo->n_g_signature;

        $thana = AreaInfo::orderby('area_nm')->where('area_type', 3)->where('area_id', $office_thana_id)->first(['area_nm']);
        $districts = AreaInfo::where('area_type', 2)->orderBy('area_nm', 'asc')->where('area_id', $office_district_id)->first(['area_nm']);
        $nationality = ['' => 'Select one'] + Countries::where('country_status', 'Yes')->where('nationality', '!=', '')->orderby('nationality', 'asc')->lists('nationality', 'id')->all();

        $office_address_full = $office_address . ', ' . $office_post_office . ', ' . (!empty($thana) ? $thana->area_nm : '') . ', ' . (!empty($districts) ? $districts->area_nm : '') . ', ' . $office_post_code;

        // $listOfDirector = DB::table('list_of_director_amendment')
        //     ->select(DB::raw('
        //         COALESCE(NULLIF(n_l_director_name, ""), l_director_name) as n_l_director_name,
        //         COALESCE(NULLIF(n_l_director_designation, ""), l_director_designation) as n_l_director_designation,
        //         COALESCE(NULLIF(n_l_director_nationality, ""), l_director_nationality) as n_l_director_nationality,
        //         COALESCE(NULLIF(n_nid_etin_passport, ""), nid_etin_passport) as n_nid_etin_passport,
        //         app_id, l_director_name, l_director_designation, l_director_nationality, nid_etin_passport, amendment_type 
        //     '))
        //     ->where($appId_and_process_type_check)
        //     ->where('amendment_type', '!=', 'delete')
        //     ->get();

        $listOfDirector = ListOfDirectorsAmendment::Where($appId_and_process_type_check)
            ->where('amendment_type', '!=', 'delete')
            ->get();

        $importedMachineryData['imported_machinery_data'] = ListOfMachineryImportedAmendment::Where($appId_and_process_type_check)
            ->where('amendment_type', '!=', 'delete')
            ->get();
        $importedMachineryData['imported_grand_total'] = ListOfMachineryImportedAmendment::where($appId_and_process_type_check)
            ->whereNotIn('amendment_type', ['delete', 'remove'])
            ->sum('total_million');

        $localMachineryData['local_machinery_data'] = ListOfMachineryLocalAmendment::Where($appId_and_process_type_check)
            ->where('amendment_type', '!=', 'delete')
            ->get();

        $localMachineryData['local_grand_total'] = ListOfMachineryLocalAmendment::where($appId_and_process_type_check)
            ->whereNotIn('amendment_type', ['delete', 'remove'])
            ->sum('total_million');

        $director = pdfSignatureQrcode::where([
            'process_type_id' => $processTypeId,
            'app_id' => $appId,
            'signature_type' => 'final',
        ])->first();

        //Barcode generator
        $dn1d = new DNS1D();
        $trackingNo = $appInfo->tracking_no; // tracking no push on barcode.
        if (!empty($trackingNo)) {
            $barcode = $dn1d->getBarcodePNG($trackingNo, 'C39', 2, 60);
            $barcode_url = 'data:image/png;base64,' . $barcode;
        } else {
            $barcode_url = '';
        }

        //Qr code generator
        $dn2d = new DNS2D();
        if (!empty($trackingNo)) {
            $qrcode = $dn2d->getBarcodePNG($trackingNo, 'QRCODE');
            $qrcode_url = 'data:image/png;base64,' . $qrcode;
        } else {
            $qrcode_url = '';
        }


        // // Qr code generator
        // $dn2d = new DNS2D();
        // if (!empty($appId) && !empty($processTypeId)) {
        //     $encodedAppId = Encryption::dataEncode($appId);
        //     $encodedProcessTypeId = Encryption::dataEncode($processTypeId);
        //     $qrcode = $dn2d->getBarcodePNG(URL::to('/').'/director-machinery-docs/'.$encodedAppId.'/'. $encodedProcessTypeId, 'QRCODE');
        //     $qrcode_url = 'data:image/png;base64,' . $qrcode;
        // } else {
        //     $qrcode_url = '';
        // }


        //signature url
        if (!empty($g_signature)) {
            if (file_exists("uploads/" . $g_signature)) {
                $signature = '<img src="uploads/' . $g_signature . '" alt="" width="70">';
            } else {
                $url = url('uploads', $g_signature);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $g_signature_response = curl_exec($ch);
                $g_signature_image_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
                curl_close($ch);
                $signature = '<img src="data:' . $g_signature_image_type . ';base64,' . base64_encode($g_signature_response) . '" width="70">';
            }
        } else {
            $signature = "";
        }

        // // Company director signature url
        // if (!empty($appInfo->g_signature)) {

        //     if (file_exists('uploads/'.$appInfo->g_signature)) {
        //         $signature = '<img src="uploads/' . $appInfo->g_signature . '" alt="" width="70">';
        //     } else {
        //         // curl request
        //         // $url = url('uploads/'.$appInfo->g_signature);
        //         $ch = curl_init();
        //         curl_setopt($ch, CURLOPT_URL, 'http://uat-bida.eserve.org.bd/uploads/2022/03/BIDA_BRA_623ae8e4bb6f39.15151263');
        //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //         // curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        //         // curl_setopt($ch, CURLOPT_POST, 1);
        //         // curl_setopt($ch, CURLOPT_HTTPGET, 1);
        //         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //         curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: image/jpeg'));
        //         // dd($ch);
        //         $result = curl_exec($ch);
        //         if ($result === false) {
        //             die(curl_error($ch));
        //         }
        //         // dd($result);
        //         if (!curl_errno($ch)) {
        //             $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //         } else {
        //             $http_code = 0;
        //         }

        //         curl_close($ch);
        //         // echo $result;
        //         $response = \GuzzleHttp\json_decode($result);
        //     }

        // } else {
        //     $signature = "";
        // }


        // Director signature url
        if (!empty($director->signature_encode)) {
            $director_signature = '<img src="data:image/jpeg;base64,' . $director->signature_encode . '" width="70">';
        } else {
            $director_signature = "";
        }

        $contents = view('BidaRegistrationAmendment::directors-machineries-pdf',
            compact('listOfDirector', 'importedMachineryData', 'localMachineryData', 'nationality', 'appInfo'))->render();

        $mpdf = new mPDF([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font_size' => 10,
            'default_font' => 'timesnewroman',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_header' => 10,
            'margin_footer' => 10,
            'setAutoTopMargin' => 'pad',
            'setAutoBottomMargin' => 'pad'
        ]);

        //static header section
        $mpdf->SetHTMLHeader('<div class="header">
            <div class="logo_image" style="float: left; width: 140px">
               <img src="' . $qrcode_url . '" alt="QR Code" height="60" />
             </div>
             <div style="text-align: right;">
               <span style="font-size: 18px;  float: right; font-weight: bold; color: #170280;">' . ucwords(CommonFunction::getCompanyNameById($appInfo->company_id)) . '</span><br>
               <span style="font-size: 13px; font-weight: bold">' . $office_address_full . '</span>
             </div><br>
             <div class="barcode" style="text-align: center;">
                 <img src="' . $barcode_url . '" width="25%" alt="Barcode" height="30" />
             </div>
            </div>');

        if (config('app.server_type') != 'live') {
            $mpdf->SetWatermarkText('TEST PURPOSE ONLY');
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'timesnewroman';
            $mpdf->watermarkTextAlpha = 0.1;
        }

        $mpdf->useSubstitutions;
        $mpdf->SetProtection(array('print'));
        $mpdf->SetDefaultBodyCSS('color', '#000');
        $mpdf->SetTitle("Bangladesh Investment Development Authority (BIDA)");
        $mpdf->SetSubject("Director and machinery list");
        $mpdf->SetAuthor("Business Automation Limited");
        $mpdf->autoScriptToLang = true;
        $mpdf->baseScript = 1;
        $mpdf->autoVietnamese = true;
        $mpdf->autoArabic = true;
        $mpdf->autoLangToFont = true;
        $mpdf->SetDisplayMode('fullwidth');

        //static footer section
        $mpdf->SetHTMLFooter('
            <div style="margin-top:20px;">
                <table class="table" width="100%">
                    <tr>
                        <td style="align:left; width: 75%">
                           ' . $signature . '<br>' .
            $g_full_name . '<br>' .
            $g_designation . '<br>' .
            ucwords(CommonFunction::getCompanyNameById($appInfo->company_id)) . '<br>' .
            $office_address_full . ' 
                        </td>
                        <td style="text-align:center;" >
                                ' . $director_signature . '<br> 
                                (' . $director->signer_name . ') <br>
                                ' . $director->signer_designation . '<br> 
                                Phone: ' . $director->signer_phone . '<br>
                                Email: ' . $director->signer_email . '
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="font-size: 9px; text-align: center">
                            <br>
                            Note: This is an authenticated system generated documents and does not require signature.<br>
                            Document generated by BIDA One Stop Service System. <i>https://bidaquickserv.org</i>
                        </td>
                    </tr>
                </table>
    
            </div>
            <table width="100%">
                <tr>
                    <td width="50%"><i style="font-size: 9px;">Download time: {DATE j-M-Y h:i a}</i></td>
                    <td width="50%" align="right"><i style="font-size: 9px;">{PAGENO}/{nbpg}</i></td>
                </tr>
            </table>');

        $stylesheet = file_get_contents('assets/stylesheets/certificate.css');
        $mpdf->setAutoTopMargin = 'stretch';
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHTML($contents, 2);
        $mpdf->defaultfooterfontsize = 9;
        $mpdf->defaultfooterfontstyle = 'B';
        $mpdf->defaultfooterline = 0;
        $mpdf->SetCompression(true);

        $pdfFilePath = self::generatePath($processTypeId);

        $mpdf->Output($pdfFilePath, $dest); // F save

        if ($dest == 'F') {
            BidaRegistrationAmendment::where('id', $appInfo->id)->update([
                'list_of_dir_machinery_doc' => $pdfFilePath,
            ]);

            return $pdfFilePath;
        }
    }

    public static function generatePath($process_type_id)
    {
        $yearMonth = date("Y") . "/" . date("m");
        $path = 'uploads/' . $yearMonth;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $prefix = '';
        switch ($process_type_id) {
            case 12:
                $prefix = "BRA_";
                break;
            case 13:
                $prefix = "IRC_";
                break;
            case 102:
                $prefix = "BR_";
                break;
            default;
        }

        $pdfName = uniqid($prefix . rand(1, 100000) . "_", true);
        return $path . "/" . $pdfName . '.pdf';
    }

    /*
     # Request
    <a href="{{ UtilFunction::fileUrlEncode($doc_path)}}">Open File</a>

     * */
    public static function fileUrlEncode($url)
    {
        if (!empty($url)) {
            // 60 minutes = 1 hour
            $expiredTime = Carbon::now()->addMinutes(60)->format('Y-m-d H:i:s');
            $encodedUrl = Encryption::encode("$url@expiredTime@$expiredTime");
            return url('attachment/' . $encodedUrl);
        }

        return '';
    }

    /*
     # Response
       Route::get('attachment/{url}', 'Controller@method');

     * */
    public static function getAttachment($encodedUrl)
    {
        $url = Encryption::decode($encodedUrl);
        $urlInfo = explode('@expiredTime@', $url);

        // Note: From laravel 5.2 => file Responses allowed
        if (!Carbon::parse($urlInfo[1])->isPast()) {
            return response()->file(public_path($urlInfo[0]));
        }

        return "Sorry! The URL has expired.";
    }

    /**
     * deadline expire date session store
     * @param $companyId
     */
    public static function isIrmsFeedbackSubmissionDateExpired($companyId)
    {
        if (!Session::has('irms_feedback_tracking_number')) {
            $today_time = strtotime(date('Y-m-d'));
            $pushTrackingNumbers = [];

            $data = ProcessList::leftJoin('br_apps', 'br_apps.id', '=', 'process_list.ref_id')
                ->leftJoin('client_irms_request_response as irr', 'irr.id', '=', 'br_apps.irms_req_res_id')
                ->select(
                    'process_list.company_id',
                    'process_list.tracking_no',
                    'process_list.json_object',
                    'process_list.ref_id',
                    'br_apps.irms_request_initiate',
                    'irr.feedback_deadline',
                    'irr.remarks',
                    'irr.status_id as irms_status_id'
                )
                ->where([
                    'process_list.process_type_id' => 102, // BIDA Registration
                    'process_list.status_id' => 25,
                    'process_list.company_id' => $companyId,
                    'br_apps.irms_request_initiate' => 1,
                ])
                ->whereIn('irr.status_id', [0, -1, 5])// 0=Pending, -1=save as draft, 5=shortfall
                ->get();

            foreach ($data as $key => $feedback_initiate) {
                if ($feedback_initiate->irms_status_id != 1 && strtotime($feedback_initiate->feedback_deadline) < $today_time) {
                    $pushTrackingNumbers[$feedback_initiate->tracking_no] = $feedback_initiate->tracking_no;
                }
            }

            Session::put('irms_feedback_tracking_number', $pushTrackingNumbers);
        }
    }

    /**
     * irms feedback initiate list
     * @param $companyId
     * @return mixed
     */
    public static function getIrmsFeedbackInitiateList($companyId)
    {
        return ProcessList::leftJoin('br_apps', 'br_apps.id', '=', 'process_list.ref_id')
            ->leftJoin('client_irms_request_response as irr', 'irr.id', '=', 'br_apps.irms_req_res_id')
            ->select(
                'process_list.company_id',
                'process_list.tracking_no',
                'process_list.json_object',
                'process_list.ref_id',
                'br_apps.irms_request_initiate',
                'irr.feedback_deadline',
                'irr.remarks',
                'irr.status_id as irms_status_id'
            )
            ->where([
                'process_list.process_type_id' => 102, // BIDA Registration
                'process_list.status_id' => 25,
                'process_list.company_id' => $companyId,
                'br_apps.irms_request_initiate' => 1,
            ])
            ->whereIn('irr.status_id', [0, -1, 1, 5])// 0=Pending, -1=save as draft, 1 = submit, 5=shortfall
            ->get();
    }

    public static function storeApiErrorInfo($api_name, $api_url, $request, $response)
    {
        $api_error_info = new ApiErrorInfo();
        $api_error_info->api_name = $api_name;
        $api_error_info->api_url = $api_url;
        $api_error_info->request_json = json_encode($request);
        $api_error_info->response_json = json_encode($response);
        $api_error_info->save();
    }

    public static function userProfileUrl($db_path, $local_path = null)
    {
        $file_path = (string)($local_path . $db_path);
        if (is_file(public_path($file_path))) {
            return url($file_path);
        } else {
            return url('assets/images/default_profile.jpg');
        }
    }

    /**
     * Returns the ID of the approval center (or division) associated with the given company ID.
     *
     * @param int $companyId The ID of the company.
     * @return int The ID of the approval center, or 1 if not found.
     */
    public static function getApprovalCenterId($companyId)
    {
        $defaultDivision = 1; // Head Office

        // Check if the company has been approved through online registration (BR Service)
        $approvalCenterId = ProcessList::where('process_type_id', UtilFunction::BIDA_REGISTRATION_PROCESS_TYPE)
            ->where('status_id', UtilFunction::APPROVED_PROCESS_STATUS)
            ->where('company_id', $companyId)
            ->value('approval_center_id');

        // If not found, check if the company has been approved through manual registration (BRA Service)
        if (empty($approvalCenterId)) {
            $approvalCenterId = ProcessList::where('process_type_id', UtilFunction::BIDA_REGISTRATION_AMENDMENT_PROCESS_TYPE)
                ->where('status_id', UtilFunction::APPROVED_PROCESS_STATUS)
                ->where('company_id', $companyId)
                ->value('approval_center_id');
        }

        // Return the approval center ID, or the default division if not found
        return !empty($approvalCenterId) ? $approvalCenterId : $defaultDivision;
    }


    public static function generateTrackingNumber($process_type_id, $process_list_id, $prefix)
    {
        DB::statement("UPDATE process_list AS table1
                INNER JOIN (
                    SELECT IFNULL(MAX(CAST(SUBSTR(tracking_no, -5, 5) AS UNSIGNED)), 0) + 1 AS next_tracking_no
                    FROM process_list
                    WHERE process_type_id = '$process_type_id' AND id != '$process_list_id' AND tracking_no LIKE '$prefix%'
                ) AS table2
                SET table1.tracking_no = CONCAT('$prefix', LPAD(table2.next_tracking_no, 5, '0'))
                WHERE table1.id = '$process_list_id'");

    }

    public static function verifyGoogleReCaptcha($recaptchaResponse)
    {
        $ip_address = UtilFunction::getVisitorRealIP();
        $postData = [
            'secret' => config('recaptcha.private_key'),
            'response' => $recaptchaResponse,
            'remoteip' => $ip_address
        ];

        $url = "https://www.google.com/recaptcha/api/siteverify";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        if (!curl_errno($ch)) {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        } else {
            $http_code = 0;
        }
        curl_close($ch);
        return ['http_code' => intval($http_code), 'data' => json_decode($response)];
    }


    public static function nidVerificationOtpSend($userMobile, $expireTimeSeconds=180)
    {
        $otp = random_int(100000, 999999);
        $smsBody = "Your sign up OTP is {$otp}, Thanks BIDA";
        $NotificationWebService = new NotificationWebService();
        $smsSendingResponse = $NotificationWebService->sendSms($userMobile, $smsBody);

        $userVerificationOtp = new UserVerificationOtp();
        $userVerificationOtp->sms_response = $smsSendingResponse['msg'];

        if ($smsSendingResponse['status'] == 1) {
            $userVerificationOtp->user_email = Session::get('oauth_data')->user_email;
            $userVerificationOtp->user_mobile = $userMobile;
            $userVerificationOtp->otp = $otp;
            $userVerificationOtp->otp_status = 1;
            $otpExpireTime = Carbon::now()->addSeconds($expireTimeSeconds);
            $userVerificationOtp->otp_expire_time = $otpExpireTime;
            $response = [
                'status' => "success",
                'statusCode' => '200',
            ];
        }else{
            $response = [
                'status' => "error",
                'statusCode' => '400',
                'message' => $smsSendingResponse['msg']
            ];
        }

        $userVerificationOtp->save();
        return $response;
    }


    public static function getListDataFromJson($json, $company_name)
    {
        $jsonDecoded = json_decode($json);
        $string = $company_name . '<br/>';
        foreach ($jsonDecoded as $key => $data) {
            $string .= $key . ": " . $data . '<br/>';
        }
        return $string;
    }

    public static function cleanAndCompareNames($input, $api) 
    {
        // remove extra spaces
        $inputCleaned = UtilFunction::cleanValue($input);
        $apiCleaned = UtilFunction::cleanValue($api);

        // Convert to uppercase
        $finalInput = strtoupper($inputCleaned);
        $finalApi = strtoupper($apiCleaned);

        // Compare both and return the result
        return $finalInput === $finalApi;
    }

    public static function cleanValue($value) 
    {
        $value = trim($value);
        // Replace unusual spaces with regular spaces
        $normalizedString = '';
        for ($i = 0; $i < mb_strlen($value); $i++) {
            $character = mb_substr($value, $i, 1);
            $codePoint = hexdec(bin2hex(mb_convert_encoding($character, 'UTF-32', 'UTF-8')));
            if (in_array(dechex($codePoint), ['a0', '202f'])) {
                $normalizedString .= ' ';
            } else if ($character == ' ' || ctype_print($character)) {
                $normalizedString .= $character;
            }
        }

        // Remove extra spaces between words
        return preg_replace('/\s+/', ' ', $normalizedString);
    }

    public static function cleanAndComparePostalCode($input, $api) 
    {
        // Convert to uppercase and remove extra spaces
        $inputCleaned = trim($input);
        $apiCleaned = trim($api);

        // convert to english
        $finalInput = CommonFunction::convert2English($inputCleaned);
        $finalApi = CommonFunction::convert2English($apiCleaned);

        // Compare both and return the result
        return $finalInput === $finalApi;
    }

}