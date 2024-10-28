<?php


namespace App\Modules\BidaRegistration\Controllers;


use App\Http\Controllers\Controller;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Modules\Apps\Models\pdfSignatureQrcode;
use App\Modules\BidaRegistration\Models\ListOfDirectors;
use App\Modules\BidaRegistration\Models\ListOfMachineryImported;
use App\Modules\BidaRegistration\Models\ListOfMachineryLocal;
use App\Modules\IrcRecommendationNew\Models\AnnualProductionCapacity;
use App\Modules\IrcRecommendationNew\Models\ProductUnit;
use App\Modules\IrcRecommendationNew\Models\RawMaterial;
use App\Modules\IrcRecommendationSecondAdhoc\Models\SecondAnnualProductionCapacity;
use App\Modules\IrcRecommendationSecondAdhoc\Models\SecondRawMaterial;
use App\Modules\ProcessPath\Models\ProcessHistory;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\Users\Models\AreaInfo;
use App\Modules\Users\Models\Countries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use yajra\Datatables\Datatables;
use Mpdf\Mpdf;

class AppSubDetailsController extends Controller
{
    protected $irc_process_type_id;

    public function __construct()
    {
        $this->irc_process_type_id = 13;
    }

    /**
     * @param $list_type
     * @param $app_id
     * @param $viewMode
     * @param $encoded_process_type_id
     * @return \BladeView|bool|View
     */
    public function detailList($list_type, $app_id, $encoded_process_type_id)
    {
        $decode_app_id = Encryption::decodeId($app_id);
        $process_type_id = Encryption::decodeId($encoded_process_type_id);
        $companyIDS = Auth::user()->company_ids;
        $application_info = ProcessList::where('ref_id', $decode_app_id)
            ->leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
            ->leftJoin('process_status', 'process_status.id', '=', 'process_list.status_id')
            ->leftJoin('company_info', 'company_info.id', '=', 'process_list.company_id')
            ->where('process_list.process_type_id', $process_type_id)
            ->where('process_list.company_id', $companyIDS)
            ->first([
                'process_list.company_id',
                'process_list.tracking_no',
                'process_list.status_id',
                'process_list.process_type_id',
                'process_type.name as process_type_name',
                'process_status.status_name',
                'company_info.company_name',
            ]);

        $viewMode = 'on';
        $user_type = CommonFunction::getUserType();
        if (in_array($user_type, ['5x505'])) {
            if ($application_info->company_id === $companyIDS && in_array($application_info->status_id, [-1, 5, 22])) {
                $viewMode = 'off';
            }
        }
        return view('BidaRegistration::details-list')->with(compact('list_type', 'application_info',
            'encoded_process_type_id', 'app_id', 'viewMode'));
    }

    /**
     * @param $encoded_app_id
     * @param $encoded_process_type_id
     * @return \BladeView|bool|View
     */
    public function moreList($encoded_app_id, $encoded_process_type_id)
    {
        $mode = '-V-';
        return view("BidaRegistration::director.lists", compact('mode', 'encoded_app_id', 'encoded_process_type_id'));
    }

    /**
     * Get list of all directors
     *
     * @param  Request  $request
     * @return string
     */
    public function getMoreList(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [ASDC-1026]';
        }

        $nationality = ['' => 'Select one'] + Countries::where('country_status', 'Yes')->where('nationality', '!=',
                '')->orderby('nationality', 'asc')->lists('nationality', 'id')->all();

        $app_id = Encryption::decodeId($request->encoded_app_id);
        $process_type_id = Encryption::decodeId($request->encoded_process_type_id);

        DB::statement(DB::raw('set @rownum=0'));
        $list = ListOfDirectors::Where('app_id', $app_id)
            ->where('process_type_id', $process_type_id)
            ->where('status', 1)
            ->get([
                DB::raw('@rownum := @rownum+1 AS sl'),
                'list_of_directors.*'
            ]);

        return Datatables::of($list)
            ->addColumn('l_director_nationality', function ($list) use ($nationality) {
                return $nationality[$list->l_director_nationality];
            })
            ->editColumn('identity_type', function ($list) {
                return ucfirst($list->identity_type);
            })
            ->make(true);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse|string
     */
    public function machineryAndEquipmentInfo(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [ASDC-1009]';
        }

        $app_id = Encryption::decodeId($request->application_id);
        $process_type_id = Encryption::decodeId($request->process_type_id);

        $total_imported_machinery = DB::table('br_list_of_machinery_imported')
            ->where('app_id', $app_id)
            ->where('process_type_id', $process_type_id)
            ->sum('l_machinery_imported_total_value');
        $total_local_machinery = DB::table('br_list_of_machinery_local')
            ->where('app_id', $app_id)
            ->where('process_type_id', $process_type_id)
            ->sum('l_machinery_local_total_value');

        return response()->json([
            'total_imported_machinery' => $total_imported_machinery,
            'total_local_machinery' => $total_local_machinery
        ]);
    }


    /**
     * @param  Request  $request
     * @return JsonResponse|string
     */
    public function listOfDirectorsInfo(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [ASDC-1012]';
        }

        $app_id = Encryption::decodeId($request->application_id);
        $process_type_id = Encryption::decodeId($request->process_type_id);

        $total_list_of_dirctors = ListOfDirectors::where('app_id', $app_id)
            ->where('process_type_id', $process_type_id)
            ->count();

        return response()->json([
            'total_list_of_dirctors' => $total_list_of_dirctors
        ]);
    }


    /**
     * @param $appId
     * @return bool
     */
//    public function listOfDirectorsMachineryOpen($appId, $process_type_id)
//    {
//        $decodedAppId = $decodedAppId = Encryption::decodeId($appId);
//        $process_type_id = Encryption::decodeId($process_type_id);
//
//        try {
//
//            $appInfo = ProcessList::leftJoin('br_apps as apps', 'apps.id', '=', 'process_list.ref_id')
//                ->where('process_list.ref_id', $decodedAppId)
//                ->where('process_list.process_type_id', $process_type_id)
//                ->where('process_list.status_id', 25)
//                ->first([
//                    'process_list.tracking_no',
//                    'process_list.company_id',
//                    'apps.*'
//                ]);
//            $g_signature = empty($appInfo->n_g_signature) ? $appInfo->g_signature : $appInfo->n_g_signature;
//
//            $thana = AreaInfo::orderby('area_nm')->where('area_type', 3)->where('area_id',
//                $appInfo->office_thana_id)->first(['area_nm']);
//            $districts = AreaInfo::where('area_type', 2)->orderBy('area_nm', 'asc')->where('area_id',
//                $appInfo->office_district_id)->first(['area_nm']);
//            $nationality = Countries::where('country_status', 'Yes')->where('nationality', '!=',
//                '')->orderby('nationality', 'asc')->lists('nationality', 'id')->all();
//            $listOfDirector = ListOfDirectors::Where('app_id', $appInfo->id)->where('process_type_id',
//                $process_type_id)->where('status', 1)->get();
//            $listOfMechineryImported = ListOfMachineryImported::Where('app_id', $appInfo->id)->where('process_type_id',
//                $process_type_id)->where('status', 1)->get();
//            $listOfMechineryLocal = ListOfMachineryLocal::Where('app_id', $appInfo->id)->where('process_type_id',
//                $process_type_id)->where('status', 1)->get();
//
////            $userInfo = ProcessHistory::leftjoin('users', 'users.id', '=', 'process_list_hist.updated_by')
////                ->where('process_list_hist.process_type', $process_type_id)
////                ->where('process_list_hist.ref_id', $appInfo->id)
////                ->where('process_list_hist.status_id', 25)
////                ->first([
////                    'users.user_first_name', 'user_middle_name', 'user_last_name', 'users.designation',
////                    'users.user_email', 'users.signature', 'users.user_phone'
////                ]);
//
////            $userFullName = $userInfo->user_first_name.' '.$userInfo->user_middle_name.' '.$userInfo->user_last_name;
//
//            $director = pdfSignatureQrcode::where([
//                'process_type_id' => $process_type_id,
//                'app_id' => $appInfo->id,
//                'signature_type' => 'final',
//            ])->first();
//
//            //Barcode generator
//            $dn1d = new DNS1D();
//            $trackingNo = $appInfo->tracking_no; // tracking no push on barcode.
//            if (!empty($trackingNo)) {
//                $barcode = $dn1d->getBarcodePNG($trackingNo, 'C39');
//                $barcode_url = 'data:image/png;base64,'.$barcode;
//            } else {
//                $barcode_url = '';
//            }
//
//            //Qr code generator
//            $dn2d = new DNS2D();
//            if (!empty($trackingNo)) {
//                $qrcode = $dn2d->getBarcodePNG($trackingNo, 'QRCODE');
//                $qrcode_url = 'data:image/png;base64,'.$qrcode;
//            } else {
//                $qrcode_url = '';
//            }
//
//            //signature url
//            if (!empty($appInfo->g_signature)) {
//                $signature = '<img src="uploads/' . $appInfo->g_signature . '" alt="" width="70">';
//            } else {
//                $signature = "";
//            }
//
//            if (!empty($director->signature_encode)) {
//                $director_signature = '<img src="data:image/jpeg;base64,' . $director->signature_encode . '" width="70">';
//            } else {
//                $director_signature = "";
//            }
//
//            $contents = view('BidaRegistration::downloadListOfDirector',
//                compact('listOfDirector', 'machineryImportedTotal', 'nationality', 'machineryLocalTotal',
//                    'listOfMechineryImported', 'listOfMechineryLocal', 'appInfo'))->render();
//
//            $mpdf = new mPDF(['setAutoBottomMargin' => 'pad', 'setAutoTopMargin' => 'pad']);
//
//            //static header section
//            $mpdf->SetHTMLHeader('<div class="header">
//            <div class="logo_image" style="float: left; width: 140px">
//               <img src="assets/images/bida_logo.png" alt="" height="80px">
//             </div>
//             <div style="text-align: right;">
//               <span style="font-size: 18px;  float: right; font-weight: bold; color: #170280;">Bangladesh Investment Development Authority </span>
//               <span style="font-size: 18px;  float: right; font-weight: bold; color: #170280;">(BIDA)</span><br>
//               <span style="font-size: 13px; font-weight: bold">Prime Minister’s Office</span>
//             </div><br>
//             <div class="barcode" style="text-align: center;">
//                 <img src="'.$barcode_url.'" width="25%" alt="Barcode" height="30" />
//             </div>
//            </div>');
//
//            if (config('app.server_type') != 'live') {
//                $mpdf->SetWatermarkText('TEST PURPOSE ONLY');
//                $mpdf->showWatermarkText = true;
//                $mpdf->watermark_font = 'timesnewroman';
//                $mpdf->watermarkTextAlpha = 0.1;
//            }
//
//            $mpdf->useSubstitutions;
//            $mpdf->SetProtection(array('print'));
//            $mpdf->SetDefaultBodyCSS('color', '#000');
//            $mpdf->SetTitle("Bangladesh Investment Development Authority (BIDA)");
//            $mpdf->SetSubject("Subject");
//            $mpdf->SetAuthor("Business Automation Limited");
//            $mpdf->autoScriptToLang = true;
//            $mpdf->baseScript = 1;
//            $mpdf->autoVietnamese = true;
//            $mpdf->autoArabic = true;
//
//            $mpdf->autoLangToFont = true;
//            $mpdf->SetDisplayMode('fullwidth');
//
//            //static footer section
//            $mpdf->SetHTMLFooter('
//            <div style="margin-top:20px;">
//                <table class="table" width="100%">
//                    <tr>
//                        <td style="align:left; width: 75%">
//                          ' . $signature . '<br>'. $appInfo->g_full_name . '<br>' . $appInfo->g_designation . '<br>' . ucwords(CommonFunction::getCompanyNameById($appInfo->company_id)) . '<br>'
//                            . $appInfo->office_address . ', ' . $appInfo->office_post_office . ', ' . (!empty($appInfo->thana) ? $appInfo->thana->area_nm : '') . ', ' . (!empty($districts) ? $districts->area_nm : '') . ', ' . $appInfo->office_post_code . '
//                        </td>
//                        <td style="text-align:center;" >
//                                ' . $director_signature . '<br> (' . $director->signer_name . ') <br>
//                                ' . $director->signer_designation . '<br> Phone: ' . $director->signer_phone . '<br>
//                                Email: ' . $director->signer_email . '
//                        </td>
//                    </tr>
//                    <tr>
//                        <td colspan="2" style="font-size: 9px; text-align: center">
//                            <br>
//                            Bangladesh Investment Development Authority, Prime Minister\'s Office, Plot #E-6/B, Agargaon, Sher-E-Bangla Nagar, Dhaka-1207.<br>
//                            Phone: PABX 88-02-55007241-5, Fax: 88-02-55007238, Email: info@bida.gov.bd, Web: www.bida.gov.bd<br>
//                            <i>To verify the authenticity of the approval copy, please scan the QR & log on to https://bidaquickserv.org.</i>
//                        </td>
//                    </tr>
//                </table>
//
//            </div>
//            <table width="100%">
//                <tr>
//                    <td width="50%"><i style="font-size: 9px;">Download time: {DATE j-M-Y h:i a}</i></td>
//                    <td width="50%" align="right"><i style="font-size: 9px;">{PAGENO}/{nbpg}</i></td>
//                </tr>
//            </table>');
//
//            $stylesheet = file_get_contents('assets/stylesheets/certificate.css');
//            $mpdf->setAutoTopMargin = 'stretch';
//            $mpdf->setAutoBottomMargin = 'stretch';
//            $mpdf->WriteHTML($stylesheet, 1);
//            $mpdf->WriteHTML($contents, 2);
//            $mpdf->defaultfooterfontsize = 9;
//            $mpdf->defaultfooterfontstyle = 'B';
//            $mpdf->defaultfooterline = 0;
//            $mpdf->SetCompression(true);
//            $mpdf->Output($appInfo->tracking_no.'.pdf', 'I');
//
//        } catch (\Exception $e) {
//            Log::error('BRListOfDirectorMachineryOpen : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [ASDC-10006]');
//            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()).' [ASDC-10006]');
//            return Redirect::back()->withInput();
//        }
//    }


    /**
     * @param  Request  $request
     * @return string
     */
    public function getListOfDirectors(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [ASDC-1004]';
        }

        $view_mode = $request->view_mode;
        $user_desk_id = Auth::user()->desk_id;
        $app_id = Encryption::decodeId($request->get('app_id'));
        $process_type_id = Encryption::decodeId($request->get('encoded_process_type_id'));

        DB::statement(DB::raw('set @rownum=0'));
        $data = ListOfDirectors::leftJoin('country_info', 'list_of_directors.l_director_nationality', '=',
            'country_info.id')
            ->where('app_id', $app_id)
            ->where('process_type_id', $process_type_id)
            ->get([
                DB::raw('@rownum := @rownum+1 AS sl'),
                'list_of_directors.id',
                'list_of_directors.l_director_name',
                'list_of_directors.l_director_designation',
                'country_info.nationality as nationality',
                'list_of_directors.nid_etin_passport'
            ]);

        return Datatables::of($data)
            ->addColumn('action', function ($data) use ($view_mode, $user_desk_id) {
                $btn = "";
                if (($user_desk_id == 0) && ($view_mode != 'on')) {
                    $btn = '<a data-toggle="modal" data-target="#directorModel" onclick="openModal(this)" data-action="'.url('/bida-registration/edit-director/'.Encryption::encodeId($data->id)).'" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> Edit</a> ';
                    $btn .= '<a data-action="'.url('bida-registration/delete-director/'.Encryption::encodeId($data->id)).'" onclick="ConfirmDelete(this)" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</a>';
                    return $btn;
                }
                return $btn;
            })
            ->make(true);
    }

    /**
     * @param $app_id
     * @param $encoded_process_type
     * @return \BladeView|bool|View
     */
    public function createDirector($app_id, $encoded_process_type)
    {
        $nationality = ['' => 'Select one'] + Countries::where('country_status', 'Yes')->where('nationality', '!=',
                '')->orderby('nationality', 'asc')->lists('nationality', 'id')->all();
        $passport_nationalities = Countries::orderby('nationality')->where('nationality', '!=',
            '')->where('nationality', '!=', 'Bangladeshi')
            ->lists('nationality', 'id');
        $passport_types = [
            'ordinary' => 'Ordinary',
            'diplomatic' => 'Diplomatic',
            'official' => 'Official',
        ];

        $viewMode = 'off';

//        dd($app_id, $encoded_process_type);

        return view('BidaRegistration::director.create',
            compact('nationality', 'app_id', 'viewMode', 'encoded_process_type', 'passport_nationalities',
                'passport_types'));
    }

    /**
     * store NID, ETIN, Passport information
     * @param  Request  $request
     * @return JsonResponse|string
     */
    public function storeVerifyDirector(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [ASDC-1027]';
        }
        $rules = [];
        $messages = [];

        $app_id = Encryption::decodeId($request->app_id);
        $process_type_id = Encryption::decodeId($request->encoded_process_type);

        if ($request->btn_save == 'NID') {
            $nid_tin_passport = $request->user_nid;

            $rules['user_nid'] = 'required|numeric';
            $rules['nid_dob'] = 'required|date|date_format:d-M-Y';
            $rules['nid_name'] = 'required';
            $rules['nid_designation'] = 'required';
            $rules['nid_nationality'] = 'required';

        } elseif ($request->btn_save == 'ETIN') {
            $nid_tin_passport = $request->user_tin;

            $rules['user_etin'] = 'required|numeric';
            $rules['etin_dob'] = 'required|date|date_format:d-M-Y';
            $rules['etin_name'] = 'required';
            $rules['etin_designation'] = 'required';
            $rules['etin_nationality'] = 'required';

        } elseif ($request->btn_save == 'passport') {
            $nid_tin_passport = $request->passport_no;

            $rules['passport_surname'] = 'required';
            $rules['passport_given_name'] = 'required';
            $rules['passport_DOB'] = 'required';
            $rules['passport_type'] = 'required';
            $rules['passport_no'] = 'required';
            $rules['passport_date_of_expire'] = 'required';
        }

        $rules['nationality_type'] = 'required|in:bangladeshi,foreign';
        $rules['identity_type'] = 'required|in:nid,tin,passport';
        $rules['gender'] = 'required|in:male,female,other';


        $validation = Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validation->errors(),
            ]);
        }
        //check existing director from list
        $duplicateRowCheck = ListOfDirectors::Where('app_id', $app_id)->where('process_type_id',
            $process_type_id)->where('nid_etin_passport', $nid_tin_passport)->first();
        if (count($duplicateRowCheck) > 0) {
            return response()->json([
                'error' => true,
                'status' => 'The director information already exists for this application. [ASDC-1083]'
            ]);
        }

        try {
            DB::beginTransaction();
            $directorInfo = new ListOfDirectors();
            $directorInfo->app_id = $app_id;
            $directorInfo->process_type_id = $process_type_id;
            $directorInfo->nationality_type = $request->nationality_type;
            $directorInfo->identity_type = $request->identity_type;

            if ($request->btn_save == 'NID') {
                //NID session data previous code (Zaman vai).
                // $nid_data = json_decode(Encryption::decode(Session::get('nid_info')));

                // $directorInfo->l_director_name = $nid_data->return->voterInfo->voterInfo->nameEnglish;
                // $directorInfo->date_of_birth = date('Y-m-d',
                //     strtotime($nid_data->return->voterInfo->voterInfo->dateOfBirth));
                // $directorInfo->nid_etin_passport = $nid_data->return->nid;
                // $directorInfo->l_director_designation = $request->nid_designation;
                // $directorInfo->l_director_nationality = $request->nid_nationality;


                //NID session data
                $nid_data = json_decode(Encryption::decode(Session::get('nid_info')));

                $directorInfo->l_director_name = $nid_data->nameEn;
                $directorInfo->date_of_birth = date('Y-m-d', strtotime($nid_data->dateOfBirth));
                $directorInfo->nid_etin_passport = $nid_data->nationalId;
                $directorInfo->l_director_designation = $request->nid_designation;
                $directorInfo->l_director_nationality = $request->nid_nationality;

            } elseif ($request->btn_save == 'ETIN') {
                //ETIN session data
                $eTin_data = json_decode(Encryption::decode(Session::get('eTin_info')));

                $directorInfo->l_director_name = $eTin_data->assesName;
                $directorInfo->nid_etin_passport = $eTin_data->etin_number;
                $directorInfo->date_of_birth = date('Y-m-d', strtotime($eTin_data->dob));
                $directorInfo->l_director_designation = $request->etin_designation;
                $directorInfo->l_director_nationality = $request->etin_nationality;

            } elseif ($request->btn_save == 'passport') {
                $directorInfo->l_director_name = ucfirst(strtolower($request->passport_given_name)).' '.ucfirst(strtolower($request->passport_surname));
                $directorInfo->date_of_birth = date('Y-m-d', strtotime($request->passport_DOB));
                $directorInfo->l_director_nationality = $request->passport_nationality;
                $directorInfo->l_director_designation = $request->l_director_designation;
                $directorInfo->passport_type = $request->passport_type;
                $directorInfo->nid_etin_passport = $request->passport_no;
                $directorInfo->date_of_expiry = date('Y-m-d', strtotime($request->passport_date_of_expire));

                // Passport copy upload
                $yearMonth = date("Y")."/".date("m")."/";
                $path = 'users/upload/'.$yearMonth;
                $passport_pic_name = trim(uniqid('BIDA_PC_PN-'.$request->passport_no.'_', true).'.'.'jpeg');
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                if (!empty($request->get('passport_upload_manual_file'))) {
                    $passport_split = explode(',', substr($request->get('passport_upload_manual_file'), 5), 2);
                    $passport_image_data = $passport_split[1];
                    $passport_base64_decode = base64_decode($passport_image_data);
                    file_put_contents($path.$passport_pic_name, $passport_base64_decode);
                } else {
                    $passport_split = explode(',', substr($request->get('passport_upload_base_code'), 5), 2);
                    $passport_image_data = $passport_split[1];
                    $passport_base64_decode = base64_decode($passport_image_data);
                    file_put_contents($path.$passport_pic_name, $passport_base64_decode);
                }

                $directorInfo->passport_scan_copy = $passport_pic_name;
            }

            $directorInfo->gender = $request->gender;
            $directorInfo->save();
            DB::commit();

            /*
             * destroy NID session data ...
             */
            if ($directorInfo->identity_type == 'nid') {
                Session::forget('nid_info');
            }

            /*
             * destroy ETIN session data ...
             */
            if ($directorInfo->identity_type == 'tin') {
                Session::forget('eTin_info');
            }

            return response()->json([
                'success' => true,
                'status' => 'Data has been saved successfully',
                'link' => '/bida-registration/list-of/director/'.$request->app_id.'/'.$request->encoded_process_type
            ]);

        } catch (\Exception $e) {
            dd($e->getMessage(),$e->getFile(),$e->getLine());
            DB::rollback();
            Log::error('BRStoreVerifyDirector : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [ASDC-1050]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).'[ASDC-1050]'
            ]);
        }
    }

    /**
     * @param $id
     * @return \BladeView|bool|View
     */
    public function editDirector($id)
    {
        $id = Encryption::decodeId($id);
        $director_list = ListOfDirectors::find($id);
        $nationality = ['' => 'Select one'] + Countries::where('country_status', 'Yes')->where('nationality', '!=',
                '')->orderby('nationality', 'asc')->lists('nationality', 'id')->all();
        return view('BidaRegistration::director.edit', compact('director_list', 'nationality'));
    }

    /**
     * @param  Request  $request
     * @return JsonResponse|string
     */
    public function updateDirector(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [ASDC-1008]';
        }

        $id = Encryption::decodeId($request->get('id'));
        try {
            DB::beginTransaction();
            $director = ListOfDirectors::find($id);
            if ($director->identity_type == 'nid') {
                $director->l_director_designation = $request->nid_designation;
                $director->l_director_nationality = $request->nid_nationality;
            } elseif ($director->identity_type == 'tin') {
                $director->l_director_designation = $request->tin_designation;
                $director->l_director_nationality = $request->tin_nationality;
            } else {
                $director->l_director_name = $request->passport_name;
                $director->date_of_birth = date('Y-m-d', strtotime($request->passport_dob));
                $director->l_director_nationality = $request->passport_nationality;
                $director->l_director_designation = $request->passport_designation;
                $director->passport_type = $request->passport_type;
                $director->nid_etin_passport = $request->passport_no;
                $director->date_of_expiry = date('Y-m-d', strtotime($request->date_of_expiry));
            }

            $director->gender = $request->gender;
            $director->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 'Data has been updated successfully',
                'link' => '/bida-registration/list-of/director/'.Encryption::encodeId($director->app_id).'/'.Encryption::encodeId($director->process_type_id)
            ]);

        } catch (\Exception $e) {
            Log::error('BRUpdateDirector : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [ASDC-1052]');
            DB::rollback();
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).' [ASDC-1052]'
            ]);
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteDirector($id)
    {
        $id = Encryption::decodeId($id);
        $delete = ListOfDirectors::where('id', $id)->delete();

        if ($delete) {
            Session::flash('success', 'Data is deleted successfully!');
        }
        return Redirect::back();
    }


    /**
     * Get Imported Machinery list
     * @param  Request  $request
     * @return string
     */
    public function getListOfImportedMachinery(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [ASDC-1013]';
        }
        $view_mode = $request->view_mode;
        $user_desk_id = Auth::user()->desk_id;
        $app_id = Encryption::decodeId($request->get('app_id'));
        $process_type_id = Encryption::decodeId($request->get('encoded_process_type_id'));

        DB::statement(DB::raw('set @rownum=0'));
        $data = ListOfMachineryImported::where('app_id', $app_id)
            ->where('process_type_id', $process_type_id)
            ->get([
                DB::raw('@rownum := @rownum+1 AS sl'),
                'br_list_of_machinery_imported.id',
                'br_list_of_machinery_imported.l_machinery_imported_name',
                'br_list_of_machinery_imported.l_machinery_imported_qty',
                'br_list_of_machinery_imported.l_machinery_imported_unit_price',
                'br_list_of_machinery_imported.l_machinery_imported_total_value',
            ]);

        return Datatables::of($data)
            ->addColumn('action', function ($data) use ($view_mode, $user_desk_id) {
                $btn = "";
                if (($user_desk_id == 0) && ($view_mode != 'on')) {
                    $btn = '<a data-toggle="modal" data-target="#importedMachineryModel" onclick="openModal(this)" data-action="'.url('/bida-registration/edit-imported-machinery/'.Encryption::encodeId($data->id)).'" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> Edit</a> ';
                    $btn .= '<a data-action="'.url('bida-registration/delete-imported-machinery/'.Encryption::encodeId($data->id)).'" onclick="ConfirmDelete(this)" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</a>';
                }
                return $btn;
            })
            ->make(true);
    }


    /**
     * @param $app_id
     * @param $encoded_process_type_id
     * @return \BladeView|bool|View
     */
    public function createImportedMachinery($app_id, $encoded_process_type_id)
    {
        return view('BidaRegistration::imported_machinery.create', compact('app_id', 'encoded_process_type_id'));
    }

    /**
     * @param  Request  $request
     * @return JsonResponse|string
     */
    public function storeImportedMachinery(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [ASDC-1014]';
        }

        $rules = [];
        $messages = [];
        foreach ($request->l_machinery_imported_name as $k => $val) {
            $rules["l_machinery_imported_name.$k"] = 'required';
            $messages["l_machinery_imported_name.$k.required"] = 'Name of machineries field is required';
            $rules["l_machinery_imported_qty.$k"] = 'required';
            $messages["l_machinery_imported_qty.$k.required"] = 'Quantity field is required';
            $rules["l_machinery_imported_unit_price.$k"] = 'required';
            $messages["l_machinery_imported_unit_price.$k.required"] = 'Unit prices TK field is required';
            $rules["l_machinery_imported_total_value.$k"] = 'required';
            $messages["l_machinery_imported_total_value.$k.required"] = 'Total value field is required';
        }

        $validation = Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validation->errors(),
            ]);
        }

        $app_id = Encryption::decodeId($request->get('app_id'));
        $process_type_id = Encryption::decodeId($request->get('encoded_process_type_id'));

        try {
            DB::beginTransaction();
            foreach ($request->l_machinery_imported_name as $key => $value) {
                $imported_machinery = new ListOfMachineryImported();
                $imported_machinery->l_machinery_imported_name = $request->l_machinery_imported_name[$key];
                $imported_machinery->l_machinery_imported_qty = $request->l_machinery_imported_qty[$key];
                $imported_machinery->l_machinery_imported_unit_price = $request->l_machinery_imported_unit_price[$key];
                $imported_machinery->l_machinery_imported_total_value = $request->l_machinery_imported_total_value[$key];
                $imported_machinery->app_id = $app_id;
                $imported_machinery->process_type_id = $process_type_id;
                $imported_machinery->save();
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'status' => 'Data has been saved successfully',
                'link' => '/bida-registration/list-of/imported-machinery/'.$request->get('app_id').'/'.$request->get('encoded_process_type_id')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('BRStoreImportedMachinery : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [ASDC-1071]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).'[ASDC-1071]'
            ]);
        }

    }

    /**
     * @param $id
     * @return \BladeView|bool|View
     */
    public function editImportedMachinery($id)
    {
        $id = Encryption::decodeId($id);
        $imported_machinery = ListOfMachineryImported::find($id);
        return view('BidaRegistration::imported_machinery.edit', compact('imported_machinery'));
    }

    /**
     * @param  Request  $request
     * @return JsonResponse|string
     */
    public function updateImportedMachinery(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [ASDC-1016]';
        }

        $rules = [];
        $messages = [];
        $rules["l_machinery_imported_name"] = 'required';
        $messages["l_machinery_imported_name.required"] = 'Name of machineries field is required';
        $rules["l_machinery_imported_qty"] = 'required';
        $messages["l_machinery_imported_qty.required"] = 'Quantity field is required';
        $rules["l_machinery_imported_unit_price"] = 'required';
        $messages["l_machinery_imported_unit_price.required"] = 'Unit prices TK field is required';
        $rules["l_machinery_imported_total_value"] = 'required';
        $messages["l_machinery_imported_total_value.required"] = 'Total value field is required';

        $validation = Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validation->errors(),
            ]);
        }

        $id = Encryption::decodeId($request->get('id'));
        try {
            DB::beginTransaction();
            $imported_machinery = ListOfMachineryImported::find($id);
            $imported_machinery->l_machinery_imported_name = $request->l_machinery_imported_name;
            $imported_machinery->l_machinery_imported_qty = $request->l_machinery_imported_qty;
            $imported_machinery->l_machinery_imported_unit_price = $request->l_machinery_imported_unit_price;
            $imported_machinery->l_machinery_imported_total_value = $request->l_machinery_imported_total_value;
            $imported_machinery->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 'Data has been updated successfully',
                'link' => '/bida-registration/list-of/imported-machinery/'.Encryption::encodeId($imported_machinery->app_id).'/'.Encryption::encodeId($imported_machinery->process_type_id)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('BRUpdateImportedMachinery : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [ASDC-1072]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).' [ASDC-1072]'
            ]);
        }
    }


    /**
     * @param $id
     * @return mixed
     */
    public function deleteImportedMachinery($id)
    {
        $id = Encryption::decodeId($id);
        $delete = ListOfMachineryImported::where('id', $id)->delete();

        if ($delete) {
            Session::flash('success', 'Data is deleted successfully!');
        }
        return Redirect::back();
    }


    /**
     * Get Local Machinery list
     * @param  Request  $request
     * @return string
     */
    public function getListOfLocalMachinery(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [ASDC-1018]';
        }
        $view_mode = $request->view_mode;
        $user_desk_id = Auth::user()->desk_id;
        $app_id = Encryption::decodeId($request->get('app_id'));
        $process_type_id = Encryption::decodeId($request->get('encoded_process_type_id'));


        DB::statement(DB::raw('set @rownum=0'));
        $data = ListOfMachineryLocal::where('app_id', $app_id)
            ->where('process_type_id', $process_type_id)
            ->get([
                DB::raw('@rownum := @rownum+1 AS sl'),
                'br_list_of_machinery_local.id',
                'br_list_of_machinery_local.l_machinery_local_name',
                'br_list_of_machinery_local.l_machinery_local_qty',
                'br_list_of_machinery_local.l_machinery_local_unit_price',
                'br_list_of_machinery_local.l_machinery_local_total_value',
            ]);
        return Datatables::of($data)
            ->addColumn('action', function ($data) use ($view_mode, $user_desk_id) {
                $btn = "";
                if (($user_desk_id == 0) && ($view_mode != 'on')) {
                    $btn = '<a data-toggle="modal" data-target="#localMachineryModel" onclick="openModal(this)" data-action="'.url('/bida-registration/edit-local-machinery/'.Encryption::encodeId($data->id)).'" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> Edit</a> ';
                    $btn .= '<a data-action="'.url('bida-registration/delete-local-machinery/'.Encryption::encodeId($data->id)).'" onclick="ConfirmDelete(this)" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</a>';
                    return $btn;
                }
                return $btn;
            })
            ->make(true);
    }

    /**
     * @param $app_id
     * @param $encoded_process_type_id
     * @return \BladeView|bool|View
     */
    public function createLocalMachinery($app_id, $encoded_process_type_id)
    {
        return view('BidaRegistration::local_machinery.create', compact('app_id', 'encoded_process_type_id'));
    }

    /**
     * @param  Request  $request
     * @return JsonResponse|string
     */
    public function storeLocalMachinery(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [ASDC-1019]';
        }

        $rules = [];
        $messages = [];
        foreach ($request->l_machinery_local_name as $k => $val) {
            $rules["l_machinery_local_name.$k"] = 'required';
            $messages["l_machinery_local_name.$k.required"] = 'Name of machineries field is required';
            $rules["l_machinery_local_qty.$k"] = 'required';
            $messages["l_machinery_local_qty.$k.required"] = 'Quantity field is required';
            $rules["l_machinery_local_unit_price.$k"] = 'required';
            $messages["l_machinery_local_unit_price.$k.required"] = 'Unit prices TK field is required';
            $rules["l_machinery_local_total_value.$k"] = 'required';
            $messages["l_machinery_local_total_value.$k.required"] = 'Total value field is required';
        }

        $validation = Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validation->errors(),
            ]);
        }

        $app_id = Encryption::decodeId($request->get('app_id'));
        $process_type_id = Encryption::decodeId($request->get('encoded_process_type_id'));
        try {
            DB::beginTransaction();
            foreach ($request->l_machinery_local_name as $key => $value) {
                $local_machinery = new ListOfMachineryLocal();
                $local_machinery->l_machinery_local_name = $request->l_machinery_local_name[$key];
                $local_machinery->l_machinery_local_qty = $request->l_machinery_local_qty[$key];
                $local_machinery->l_machinery_local_unit_price = $request->l_machinery_local_unit_price[$key];
                $local_machinery->l_machinery_local_total_value = $request->l_machinery_local_total_value[$key];
                $local_machinery->app_id = $app_id;
                $local_machinery->process_type_id = $process_type_id;
                $local_machinery->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 'Data has been saved successfully',
                'link' => '/bida-registration/list-of/local-machinery/'.$request->get('app_id').'/'.$request->get('encoded_process_type_id')
            ]);

        } catch (\Exception $e) {
            Log::error('BRStoreLocalMachinery : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [ASDC-1081]');
            DB::rollback();
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).' [ASDC-1081]'
            ]);
        }

    }

    /**
     * @param $id
     * @return \BladeView|bool|View
     */
    public function editLocalMachinery($id)
    {
        $id = Encryption::decodeId($id);
        $local_machinery = ListOfMachineryLocal::find($id);

        return view('BidaRegistration::local_machinery.edit', compact('local_machinery'));
    }


    /**
     * @param  Request  $request
     * @return JsonResponse|string
     */
    public function updateLocalMachinery(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [ASDC-1021]';
        }

        $rules = [];
        $messages = [];

        $rules["l_machinery_local_name"] = 'required';
        $messages["l_machinery_local_name.required"] = 'Name of machineries field is required';
        $rules["l_machinery_local_qty"] = 'required';
        $messages["l_machinery_local_qty.required"] = 'Quantity field is required';
        $rules["l_machinery_local_unit_price"] = 'required';
        $messages["l_machinery_local_unit_price.required"] = 'Unit prices TK field is required';
        $rules["l_machinery_local_total_value"] = 'required';
        $messages["l_machinery_local_total_value.required"] = 'Total value field is required';


        $validation = Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validation->errors(),
            ]);
        }

        $id = Encryption::decodeId($request->get('id'));
        try {
            DB::beginTransaction();
            $local_machinery = ListOfMachineryLocal::find($id);
            $local_machinery->l_machinery_local_name = $request->l_machinery_local_name;
            $local_machinery->l_machinery_local_qty = $request->l_machinery_local_qty;
            $local_machinery->l_machinery_local_unit_price = $request->l_machinery_local_unit_price;
            $local_machinery->l_machinery_local_total_value = $request->l_machinery_local_total_value;
            $local_machinery->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 'Data has been updated successfully',
                'link' => '/bida-registration/list-of/local-machinery/'.Encryption::encodeId($local_machinery->app_id).'/'.Encryption::encodeId($local_machinery->process_type_id)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('BRUpdateLocalMachinery : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [ASDC-1082]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).' [ASDC-1082]'
            ]);
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteLocalMachinery($id)
    {
        $id = Encryption::decodeId($id);
        $delete = ListOfMachineryLocal::where('id', $id)->delete();

        if ($delete) {
            Session::flash('success', 'Data is deleted successfully!');
        }
        return Redirect::back();
    }

    /**
     * get annual production list
     * @param  Request  $request
     * @return string
     */
    public function getAnnualProductionList(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [ASDC-10101]';
        }

        $view_mode = $request->view_mode;
        $app_id = Encryption::decodeId($request->get('app_id'));
        $process_type_id = $request->get('process_type_id');
        $user_desk_id = Auth::user()->desk_id;

        DB::statement(DB::raw('set @rownum=0'));
        if ($process_type_id == 13){
            $data = AnnualProductionCapacity::leftJoin('product_unit', 'product_unit.id', '=',
                'irc_annual_production_capacity.quantity_unit')
                ->where('app_id', $app_id)
                ->get([DB::raw('@rownum := @rownum+1 AS sl'), 'irc_annual_production_capacity.*', 'product_unit.name as unit_name']);
        }

        if ($process_type_id == 14){
            $data = SecondAnnualProductionCapacity::leftJoin('product_unit', 'product_unit.id', '=',
                'irc_2nd_annual_production_capacity.quantity_unit')
                ->where('app_id', $app_id)
                ->get([DB::raw('@rownum := @rownum+1 AS sl'), 'irc_2nd_annual_production_capacity.*', 'product_unit.name as unit_name']);
        }


        return Datatables::of($data)
            ->addColumn('action', function ($data) use ($view_mode, $user_desk_id, $process_type_id) {
                $btn = "";
                if (($user_desk_id == 0) && ($view_mode != 'on')) {
                    $btn = '<a data-toggle="modal" data-target="#anualProductionModel" onclick="openModal(this)" data-action="'.url('/bida-registration/edit-annual-production/'.Encryption::encodeId($data->id)).'/'.Encryption::encodeId($process_type_id).'" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> Edit</a> ';
                    $btn .= '<a data-action="'.url('bida-registration/delete-annual-production/'.Encryption::encodeId($data->id) .'/'. Encryption::encodeId($process_type_id)).'" onclick="ConfirmDelete(this)" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</a>';
                    return $btn;
                }
                return $btn;
            })
            ->addColumn('raw_material', function ($data) use ($view_mode, $user_desk_id, $process_type_id) {
                $module = "";
                if ($process_type_id == 13) {
                    $module = 'irc-recommendation-new/';
                }
                if ($process_type_id == 14) {
                    $module = 'irc-recommendation-second-adhoc/';
                }
                $btn = "";
                if (($user_desk_id == 0) && ($view_mode != 'on')) {
                    $btn = '<a data-toggle="modal" data-target="#anualProductionModel" onclick="openModal(this)" data-action="'.url('/bida-registration/add-raw-material/'.Encryption::encodeId($data->app_id).'/'.Encryption::encodeId($data->id).'/'.Encryption::encodeId($process_type_id)).'" class="btn btn-xs btn-primary"><i class="fa fa-plus"></i> Manual</a> ';
                    $btn .= '<a data-toggle="modal" data-target="#anualProductionModel" onclick="openModal(this)" data-action="'. url('/'. $module .'import/'.Encryption::encodeId($data->app_id).'/'.Encryption::encodeId($data->id)).'" class="btn btn-xs btn-success"><i class="fa fa-plus"></i> Excel</a>';
                    return $btn;
                }
                return $btn;
            })
            ->make(true);
    }

    /**
     * Raw material add form
     * @param $app_id
     * @param $apc_product_id
     * @return \BladeView|bool|View
     */
    public function addRawMaterial($app_id, $apc_product_id, $process_type_id)
    {
        $process_type_id = Encryption::decodeId($process_type_id);
        if ($process_type_id == 13) {
            $annual_production_capacity = AnnualProductionCapacity::where('id', Encryption::decodeId($apc_product_id))->first(['unit_of_product', 'product_name']);
            $raw_material = RawMaterial::where('apc_product_id', Encryption::decodeId($apc_product_id))->get();
            $total_price = RawMaterial::where('apc_product_id', Encryption::decodeId($apc_product_id))->sum('price_taka');
        }

        if ($process_type_id == 14) {
            $annual_production_capacity = SecondAnnualProductionCapacity::where('id', Encryption::decodeId($apc_product_id))->first(['unit_of_product', 'product_name']);
            $raw_material = SecondRawMaterial::where('apc_product_id', Encryption::decodeId($apc_product_id))->get();
            $total_price = SecondRawMaterial::where('apc_product_id', Encryption::decodeId($apc_product_id))->sum('price_taka');
        }

        $productUnit = ['' => 'Select one'] + ProductUnit::where('status', 1)->where('is_archive', 0)->orderBy('name')->lists('name', 'id')->all();

        return \view('IrcRecommendationNew::raw_material.create-raw-material',
            compact('app_id', 'apc_product_id', 'annual_production_capacity', 'raw_material', 'total_price', 'process_type_id', 'productUnit'));
    }

    /**
     * store raw material
     * @param  Request  $request
     * @return JsonResponse|string
     */
    public function storeRawMaterial(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [ASDC-10210]';
        }
        /**
         * Multiple validation
         */
        $rules = [];
        $messages = [];
        foreach ($request->product_name as $k => $val) {
            $rules["product_name.$k"] = 'required';
            $messages["product_name.$k.required"] = 'Product name field is required';

            $rules["hs_code.$k"] = 'required';
            $messages["hs_code.$k.required"] = 'HS code field is required';

            $rules["quantity.$k"] = 'required';
            $messages["quantity.$k.required"] = 'Quantity field is required';

            $rules["quantity_unit.$k"] = 'required';
            $messages["quantity_unit.$k.required"] = 'Unit of quantity field is required';

            $rules["percent.$k"] = 'required';
            $messages["percent.$k.required"] = 'Percent field is required';

            $rules["price_taka.$k"] = 'required';
            $messages["price_taka.$k.required"] = 'Price (BD) field is required';
        }

        $validation = Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validation->errors(),
            ]);
        }

        try {
            $app_id = Encryption::decodeId($request->app_id);
            $apc_product_id = Encryption::decodeId($request->apc_product_id);

            DB::beginTransaction();
            if (!empty($app_id)) {
                $raw_material_ids = [];

                if ($request->process_type_id == 13) {
                    foreach ($request->product_name as $proKey => $proData) {
                        $raw_material_id = $request->raw_material_id[$proKey];
                        $raw_material = RawMaterial::findOrNew($raw_material_id);
                        $raw_material->app_id = $app_id;
                        $raw_material->apc_product_id = $apc_product_id;
                        $raw_material->product_name = $proData;
                        $raw_material->hs_code = $request->get('hs_code')[$proKey];
                        $raw_material->quantity = $request->get('quantity')[$proKey];
                        $raw_material->quantity_unit = $request->get('quantity_unit')[$proKey];
                        $raw_material->percent = $request->get('percent')[$proKey];
                        $raw_material->price_taka = $request->get('price_taka')[$proKey];
                        $raw_material->save();
                        $raw_material_ids[] = $raw_material->id;
                    }

                    if (count($raw_material_ids) > 0) {
                        RawMaterial::where('apc_product_id', $raw_material->apc_product_id)->whereNotIn('id',$raw_material_ids)->delete();
                    }

                    AnnualProductionCapacity::where('app_id', $app_id)->where('id', $apc_product_id)
                        ->update([
                            'unit_of_product' => $request->get('unit_of_product'),
                            'raw_material_total_price' => $request->get('raw_material_total_price')
                        ]);
                }

                if ($request->process_type_id == 14) {
                    foreach ($request->product_name as $proKey => $proData) {
                        $raw_material_id = $request->raw_material_id[$proKey];
                        $raw_material = SecondRawMaterial::findOrNew($raw_material_id);
                        $raw_material->app_id = $app_id;
                        $raw_material->apc_product_id = $apc_product_id;
                        $raw_material->product_name = $proData;
                        $raw_material->hs_code = $request->hs_code[$proKey];
                        $raw_material->quantity = $request->quantity[$proKey];
                        $raw_material->quantity_unit = $request->get('quantity_unit')[$proKey];
                        $raw_material->percent = $request->get('percent')[$proKey];
                        $raw_material->price_taka = $request->price_taka[$proKey];
                        $raw_material->save();
                        $raw_material_ids[] = $raw_material->id;
                    }

                    if (count($raw_material_ids) > 0) {
                        SecondRawMaterial::where('apc_product_id', $raw_material->apc_product_id)->whereNotIn('id',
                            $raw_material_ids)->delete();
                    }

                    SecondAnnualProductionCapacity::where('app_id', $app_id)->where('id', $apc_product_id)
                        ->update([
                            'unit_of_product' => $request->unit_of_product,
                            'raw_material_total_price' => $request->raw_material_total_price
                        ]);
                }
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 'Data has been saved successfully',
                'link' => '/bida-registration/list-of/annual-production/'.$request->get('app_id').'/'.Encryption::encodeId($request->process_type_id)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('IRCRawMaterialStore : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [ASDC-10020]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).' [ASDC-10021]'
            ]);
        }
    }

    /**
     * AnnualProduction add form
     * @param $app_id
     * @return \BladeView|bool|View
     */
    public function addAnnualProduction($app_id, $process_type_id)
    {
        $productUnit = ['' => 'Select one'] + ProductUnit::where('status', 1)->where('is_archive', 0)->lists('name',
                'id')->all();
        return \view('IrcRecommendationNew::raw_material.create-annual-production', compact('app_id', 'productUnit', 'process_type_id'));
    }

    /**
     * Annual Production value store here
     * @param  Request  $request
     * @return JsonResponse
     */
    public function storeAnnualProduction(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [ASDC-10210]';
        }
        /**
         * Multiple validation
         */
        $rules = [];
        $messages = [];
        foreach ($request->em_product_name as $k => $val) {
            $rules["em_product_name.$k"] = 'required';
            $messages["em_product_name.$k.required"] = 'Product name field is required';
            $rules["em_quantity_unit.$k"] = 'required';
            $messages["em_quantity_unit.$k.required"] = 'Quantity unit field is required';
            $rules["em_quantity.$k"] = 'required';
            $messages["em_quantity.$k.required"] = 'Quantity field is required';
            $rules["em_price_usd.$k"] = 'required';
            $messages["em_price_usd.$k.required"] = 'Price (USD) field is required';
            $rules["em_value_taka.$k"] = 'required';
            $messages["em_value_taka.$k.required"] = 'Price (BD) field is required';
        }

        $validation = Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validation->errors(),
            ]);
        }

        try {
            $app_id = Encryption::decodeId($request->app_id);
            DB::beginTransaction();
            if (!empty($app_id)) {
                if ($request->process_type_id == 13) {
                    foreach ($request->em_product_name as $proKey => $proData) {
                        $annualProduction = new AnnualProductionCapacity();
                        $annualProduction->app_id = $app_id;
                        $annualProduction->product_name = $proData;
                        $annualProduction->quantity_unit = $request->em_quantity_unit[$proKey];
                        $annualProduction->quantity = $request->em_quantity[$proKey];
                        $annualProduction->price_usd = $request->em_price_usd[$proKey];
                        $annualProduction->price_taka = $request->em_value_taka[$proKey];
                        $annualProduction->save();
                    }
                }

                if ($request->process_type_id == 14) {
                    foreach ($request->em_product_name as $proKey => $proData) {
                        $annualProduction = new SecondAnnualProductionCapacity();
                        $annualProduction->app_id = $app_id;
                        $annualProduction->product_name = $proData;
                        $annualProduction->quantity_unit = $request->em_quantity_unit[$proKey];
                        $annualProduction->quantity = $request->em_quantity[$proKey];
                        $annualProduction->price_usd = $request->em_price_usd[$proKey];
                        $annualProduction->price_taka = $request->em_value_taka[$proKey];
                        $annualProduction->save();
                    }
                }

            }
            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 'Data has been saved successfully',
                'link' => '/bida-registration/list-of/annual-production/'.$request->get('app_id').'/'.Encryption::encodeId($request->process_type_id)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('IRCAnnualProductionStore : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [ASDC-10022]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).' [ASDC-10023]'
            ]);
        }
    }

    /**
     * Annual Production edit view
     * @param $app_id
     * @return \BladeView|bool|View
     */
    public function editAnnualProduction($app_id, $process_type_id)
    {
        $process_type_id = Encryption::decodeId($process_type_id);
        $app_id = Encryption::decodeId($app_id);

        if ($process_type_id == 13) {
            $apc_product = AnnualProductionCapacity::find($app_id);
        }

        if ($process_type_id == 14) {
            $apc_product = SecondAnnualProductionCapacity::find($app_id);
        }

        $productUnit = ['' => 'Select one'] + ProductUnit::where('status', 1)->where('is_archive', 0)->lists('name',
                'id')->all();
        return \view('IrcRecommendationNew::raw_material.edit-annual-production',
            compact('apc_product', 'productUnit', 'process_type_id'));
    }

    /**
     * AnnualProduction update
     * @param  Request  $request
     * @return JsonResponse|string
     */
    public function updateAnnualProduction(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [ASDC-10200]';
        }

        $rules = [];
        $messages = [];

        $rules["em_product_name"] = 'required';
        $messages["em_product_name.required"] = 'Product name field is required';
        $rules["em_quantity_unit"] = 'required';
        $messages["em_quantity_unit.required"] = 'Quantity unit field is required';
        $rules["em_quantity"] = 'required';
        $messages["em_quantity.required"] = 'Quantity field is required';
        $rules["em_price_usd"] = 'required';
        $messages["em_price_usd.required"] = 'Price (USD) field is required';
        $rules["em_value_taka"] = 'required';
        $messages["em_value_taka.required"] = 'Price (BD) field is required';

        $validation = Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validation->errors(),
            ]);
        }

        try {

            $app_id = Encryption::decodeId($request->app_id);
            if ($request->process_type_id == 13){
                $apc_product = AnnualProductionCapacity::find($app_id);
                $apc_product->product_name = $request->em_product_name;
                $apc_product->quantity_unit = $request->em_quantity_unit;
                $apc_product->quantity = $request->em_quantity;
                $apc_product->price_usd = $request->em_price_usd;
                $apc_product->price_taka = $request->em_value_taka;
                $apc_product->save();
            }
            if ($request->process_type_id == 14){
                $apc_product = SecondAnnualProductionCapacity::find($app_id);
                $apc_product->product_name = $request->em_product_name;
                $apc_product->quantity_unit = $request->em_quantity_unit;
                $apc_product->quantity = $request->em_quantity;
                $apc_product->price_usd = $request->em_price_usd;
                $apc_product->price_taka = $request->em_value_taka;
                $apc_product->save();
            }


            return response()->json([
                'success' => true,
                'status' => 'Data has been saved successfully',
                'link' => '/bida-registration/list-of/annual-production/'.Encryption::encodeId($apc_product->app_id).'/'.Encryption::encodeId($request->process_type_id)
            ]);

        } catch (\Exception $e) {
            Log::error('IRCAnnualProductionUpdate : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine().' [ASDC-10024]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()).' [ASDC-10025]'
            ]);
        }
    }

    /**
     * delete annual-production
     * @param $id
     * @return mixed
     */
    public function deleteAnnualProduction($id, $process_type_id)
    {
        $process_type_id = Encryption::decodeId($process_type_id);
        if ($process_type_id == 13) {
            DB::table('irc_annual_production_capacity')->where('id', Encryption::decodeId($id))->delete();
        }

        if ($process_type_id == 14) {
            DB::table('irc_2nd_annual_production_capacity')->where('id', Encryption::decodeId($id))->delete();
        }

        Session::flash('success', 'Data has been deleted successfully');
        return Redirect::back();
    }

    public function loadListOfDirectiors(Request $request)
    {
        $app_id = Encryption::decodeId($request->app_id);
        $process_type_id = Encryption::decodeId($request->process_type_id);

        DB::statement(DB::raw('set @rownum=0'));
        $getData = ListOfDirectors::leftJoin('country_info', 'list_of_directors.l_director_nationality', '=',
            'country_info.id')
            ->where('app_id', $app_id)
            ->where('process_type_id', $process_type_id)
            ->orderBy('list_of_directors.id', 'DESC')
            ->get([
                DB::raw('@rownum := @rownum+1 AS sl'),
                'list_of_directors.id',
                'list_of_directors.l_director_name',
                'list_of_directors.l_director_designation',
                'country_info.nationality as nationality',
                'list_of_directors.nid_etin_passport'
            ]);

        $data = ['responseCode' => 1, 'data' => $getData];
        return response()->json($data);
    }
}