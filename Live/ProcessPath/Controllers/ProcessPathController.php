<?php

namespace App\Modules\ProcessPath\Controllers;

use App\Http\Controllers\Controller;
use App\IRCCommonPool;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\PDFmodifier;
use App\Libraries\Encryption;
use App\Libraries\MLVerification;
use App\Libraries\UtilFunction;
use App\Modules\Apps\Models\AppDocuments;
use App\Modules\Apps\Models\ModifiedDocument;
use App\Modules\Apps\Models\EmailQueue;
use App\Modules\Apps\Models\ShadowFile;
use App\Modules\BasicInformation\Models\BasicInformation;
use App\Modules\BasicInformation\Models\EA_OrganizationStatus;
use App\Modules\BoardMeting\Models\Agenda;
use App\Modules\BoardMeting\Models\AgendaMapping;
use App\Modules\BoardMeting\Models\BoardMeetingProcessStatus;
use App\Modules\BoardMeting\Models\BoardMeting;
use App\Modules\BoardMeting\Models\ProcessListBoardMeting;
use App\Modules\CompanyAssociation\Models\CompanyAssociation;
use App\Modules\IrcRecommendationNew\Models\AnnualProductionCapacity;
use App\Modules\IrcRecommendationNew\Models\AnnualProductionSpareParts;
use App\Modules\IrcRecommendationNew\Models\BusinessClass;
use App\Modules\IrcRecommendationNew\Models\IrcInspection;
use App\Modules\IrcRecommendationNew\Models\IrcProjectStatus;
use App\Modules\IrcRecommendationNew\Models\ProductUnit;
use App\Modules\IrcRecommendationRegular\Models\IrcSixMonthsImportRawMaterialAmendment;
use App\Modules\IrcRecommendationSecondAdhoc\Models\IrcSixMonthsImportRawMaterial;
use App\Modules\IrcRecommendationSecondAdhoc\Models\SecondAnnualProductionCapacity;
use App\Modules\IrcRecommendationSecondAdhoc\Models\SecondAnnualProductionSpareParts;
use App\Modules\IrcRecommendationSecondAdhoc\Models\SecondIrcInspection;
use App\Modules\IrcRecommendationThirdAdhoc\Models\ThirdAnnualProductionCapacity;
use App\Modules\IrcRecommendationThirdAdhoc\Models\ThirdAnnualProductionSpareParts;
use App\Modules\IrcRecommendationThirdAdhoc\Models\ThirdIrcInspection;

use App\Modules\IrcRecommendationRegular\Models\RegularAnnualProductionCapacity;
use App\Modules\IrcRecommendationRegular\Models\RegularAnnualProductionSpareParts;
use App\Modules\IrcRecommendationRegular\Models\RegularIrcInspection;

//use App\Modules\LicenceApplication\Models\BankAccount\BankAccount;
//use App\Modules\LicenceApplication\Models\CompanyRegistration\CompanyRegistration;
//use App\Modules\LicenceApplication\Models\Etin\Etin;
//use App\Modules\LicenceApplication\Models\NameClearance\NameClearance;
//use App\Modules\LicenceApplication\Models\TradeLicence;
//use App\Modules\Users\Models\UsersModel;
use App\Modules\OfficePermissionAmendment\Models\OfficePermissionAmendment;
use App\Modules\OfficePermissionCancellation\Models\OfficePermissionCancellation;
use App\Modules\OfficePermissionExtension\Models\OfficePermissionExtension;
use App\Modules\OfficePermissionNew\Models\OfficePermissionNew;
use App\Modules\ProcessPath\Models\HelpText;
use App\Modules\ProcessPath\Models\ProcessDoc;
use App\Modules\ProcessPath\Models\ProcessFavoriteList;
use App\Modules\ProcessPath\Models\ProcessHistory;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Models\ProcessPath;
use App\Modules\ProcessPath\Models\ProcessStatus;
use App\Modules\ProcessPath\Models\ProcessType;
use App\Modules\ProcessPath\Models\UserDesk;
use App\Modules\ProcessPath\Services\CertificateMailAppDataProcessingTrait;
use App\Modules\Settings\Models\Bank;
use App\Modules\Settings\Models\Configuration;
use App\Modules\Settings\Models\Currencies;
use App\Modules\Settings\Models\Holiday;
use App\Modules\Users\Models\CompanyInfo;
use App\Modules\Users\Models\DepartmentInfo;
use App\Modules\Users\Models\Users;
use App\Modules\WaiverCondition8\Models\WaiverCondition8;
use App\Modules\WorkPermitCancellation\Models\WorkPermitCancellation;
use App\Modules\WorkPermitExtension\Models\WorkPermitExtension;
use App\Modules\WorkPermitNew\Models\WorkPermitNew;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use ParagonIE\EasyRSA\EasyRSA;
use ParagonIE\EasyRSA\KeyPair;
use yajra\Datatables\Datatables;
use Illuminate\Support\Str;

class ProcessPathController extends Controller
{
    use CertificateMailAppDataProcessingTrait;

    public $processPathTable = 'process_path';
    public $deskTable = 'user_desk';
    public $processStatus = 'process_status';
    public $processType = 'process_type';
    public $shortFallId = '5,6';
    protected $aclName;

    public function __construct()
    {
        //        if (Session::has('lang'))
        //            App::setLocale(Session::get('lang'));

        $this->aclName = 'processPath';
    }

    /**
     * Show application list
     * @param string $id
     * @param string $processStatus
     * @return \BladeView|bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function processListById(Request $request, $id = '', $processStatus = '')
    {
        // the code only batch update and delegation
        $userType = Auth::user()->user_type;
        if ($userType == '4x404') {
            Session::forget('is_delegation');
            Session::forget('batch_process_id');
            Session::forget('is_batch_update');
            Session::forget('single_process_id_encrypt');
            Session::forget('next_app_info');
            Session::forget('total_selected_app');
            Session::forget('total_process_app');
        }
        //end

        $url = $_SERVER['REQUEST_URI'];
        $exp = explode("/", $url);
        if (count($exp) == 5) {
            $id = $exp[4]; // for sub module url get process id
        }
        // Basic Information process is set as default
        // desk user get a link named 'pending process in your desk(20)'
        $process_type_id = $id != '' ? Encryption::decodeId($id) : 0;


        if (!session()->has('active_process_list')) {
            session()->set('active_process_list', $process_type_id);
        }

        if (CommonFunction::checkEligibility() == 0 && $userType == '5x505') {
            $ProcessType = ProcessType::whereStatus(1)->where('id', 100)->lists('name', 'id')->all();
        } else {

            $ProcessType = ProcessType::whereStatus(1)
                ->where(function ($query) use ($userType) {
                    $query->where('active_menu_for', 'like', "%$userType%");
                })
                ->orderBy('name')
                ->lists('name', 'id')
                ->all();
        }


        $process_info = ProcessType::where('id', $process_type_id)->first(['acl_name', 'form_url', 'name', 'id']);
        $processStatus = null;


        $status = [0 => 'All'] + ProcessStatus::where('status', 1)
                ->whereNotIn('id', [-1, 3])
                ->where('process_type_id', $process_type_id)
                ->distinct('status_name')
                ->orderBy('status_name', 'asc')
                ->lists('status_name', 'id')
                ->all();

        $searchTimeLine = [
            '' => 'Select One',
            //            '1' => '1 Day',
            '7' => '1 Week',
            '15' => '2 Weeks',
            '30' => '1 Month',
            '90' => '3 Months',
            '180' => '6 Months',
            'all' => 'All',
        ];

        // Global search or dashboard search option
        if ($request->isMethod('post')) {
            // $search_by_keyword = $request->get('search_by_keyword');
            $search_by_keyword = isset($request->search_by_keyword) ? htmlspecialchars(trim($request->search_by_keyword), ENT_QUOTES) : '';
            $search_by_status = $request->get('search_by_status');
        }
        
        $applicationInProcessing = 0;
        // if (in_array($process_type_id, [13, 14, 15, 16]) && Auth::user()->user_type == "5x505") {
        //     $applicationInProcessing = CommonFunction::applicationInProcessing($process_type_id);
        // }
        if (($process_type_id == 13 || $process_type_id == 21 ) && Auth::user()->user_type == "5x505") {
            $applicationInProcessing = CommonFunction::applicationInProcessing($process_type_id);
        }

        $urir = $request->getRequestUri();
        if ($urir == "/process/list/feedback-list") {
            return view("ProcessPath::feedback-system", compact('status', 'ProcessType', 'processStatus', 'searchTimeLine', 'process_type_id', 'process_info', 'search_by_keyword'));
        } elseif ($urir == "/process/list/security-clearance") {
            return view("ProcessPath::security-clearance", compact('status', 'ProcessType', 'processStatus', 'searchTimeLine', 'process_type_id', 'process_info', 'search_by_keyword'));
        }
        return view("ProcessPath::common-list", compact(
            'status',
            'ProcessType',
            'processStatus',
            'searchTimeLine',
            'process_type_id',
            'process_info',
            'search_by_keyword',
            'search_by_status',
            'applicationInProcessing'
        ));
    }

    public function setProcessType(Request $request)  //ajax set process type
    {
        session()->put('active_process_list', $request->get('data'));
        return 'success';
    }

    public function searchProcessType(Request $request)  //ajax get process type
    {
        $process_type_id = $request->get('data');
        $status = ProcessStatus::whereNotIn('id', [-1, 3]);
        if ($process_type_id != 0) {
            $status->where('process_type_id', $process_type_id);
        } else {
            $status->distinct('status_name');
        }
        $status = $status->orderBy('status_name', 'asc')->lists('status_name', 'id')->all();
        $status = [0 => 'All'] + $status;
        $data = ['responseCode' => 1, 'data' => $status];
        return response()->json($data);
    }

    public function getList(Request $request, $status = '', $desk = '')
    {
        //        $process_type_id = session('active_process_list');
        $process_type_id = $request->get('process_type_id'); //new process type get by javascript session
        $status == '-1000' ? '' : $status;
        $user_type = Auth::user()->user_type;
        $companyIds = CommonFunction::getUserCompanyWithZero();

        $process_status = $request->get('process_status');
        $request_object = $request->get('status_wise_list');

        // when click status from common list then call the function
        if ($request_object === 'status_wise_list') {
            $list = ProcessList::getStatusWiseApplication($process_type_id, $process_status);
        } else {
            $list = ProcessList::getApplicationList($process_type_id, $request, $desk);
        }

        $get_user_desk_ids = explode(",", CommonFunction::getDeskId());

        /*
         * If search option has only one result then open the application
         * */
        if ($request->has('process_search')) {
            $list_count = $list->get()->count();
            if ($list_count == 1) {
                $single_data = $list->first();

                $redirect_path = CommonFunction::getAppRedirectPathByJson($single_data->form_id);
                if (in_array($single_data->status_id, [-1, 5, 22]) && $user_type == "5x505") {
                    $redirect_path = $redirect_path['edit'];
                } else {
                    $redirect_path = $redirect_path['view'];
                }
                //                Lock application by current user
//                if (
//                    $single_data->locked_by > 0
//                    && Carbon::createFromFormat('Y-m-d H:i:s', $single_data->locked_at)->diffInMinutes() < 3 and $single_data->locked_by != Auth::user()->id
//                ) {
//                    $locked_by_user = UsersModel::where('id', $single_data->locked_by)
//                        ->select(DB::raw("CONCAT(users.user_first_name,' ',users.user_middle_name, ' ',users.user_last_name) as user_name"))
//                        ->pluck('user_name');
//
//
//                    return response()->json([
//                        'responseType' => 'lock_by_user',
//                        'url' => url('process/' . $single_data->form_url . '/' . $redirect_path . '/' . Encryption::encodeId($single_data->ref_id) . '/' . Encryption::encodeId($single_data->process_type_id)),
//                        'lock_by_user' => $locked_by_user
//                    ]);
//                }
                //                Lock application by current user

                return response()->json([
                    'responseType' => 'single',
                    'url' => url('process/' . $single_data->form_url . '/' . $redirect_path . '/' . Encryption::encodeId($single_data->ref_id) . '/' . Encryption::encodeId($single_data->process_type_id))
                ]);
            }
        }

        $class = $this->batchUpdateClass($request, $desk);

        return Datatables::of($list)
            ->addColumn('action', function ($list) use ($status, $request, $user_type, $companyIds, $desk, $class) {
                $html = '';
                $redirect_path = CommonFunction::getAppRedirectPathByJson($list->form_id);
//                if (
//                    $list->locked_by > 0
//                    && !empty($list->locked_at) ? Carbon::createFromFormat('Y-m-d H:i:s', $list->locked_at)->diffInMinutes() < 3 : '' and $list->locked_by != Auth::user()->id
//                ) {
//                    $locked_by_user = UsersModel::where('id', $list->locked_by)
//                        ->select(DB::raw("CONCAT(users.user_first_name,' ',users.user_middle_name, ' ',users.user_last_name) as user_name"))
//                        ->pluck('user_name');
//
//                    $html = '<img width="20" src="' . url('/assets/images/Lock-icon_2.png') . '"/>' .
//                        '<a onclick="return confirm(' . "'The record locked by $locked_by_user, would you like to force unlock?'" . ')"
//                            target="_blank" href="' . url('process/' . $list->form_url . '/' . $redirect_path['view'] . '/' . Encryption::encodeId($list->ref_id) . '/' . Encryption::encodeId($list->process_type_id)) . '"
//                            class="btn btn-xs btn-primary button-color"> Open</a> &nbsp; ';
//                }
//                else {

                if (in_array($list->status_id, [-1, 5, 22]) && Auth::user()->user_type == "5x505") {
                    $html = '<a  href="' . url('process/' . $list->form_url . '/' . $redirect_path['edit'] . '/' . Encryption::encodeId($list->ref_id) . '/' . Encryption::encodeId($list->process_type_id)) . '" class="btn btn-xs btn-success button-color ' . $class['button_class'] . ' " style="color: white"> <i class="fa fa-folder-open"></i> Edit</a><br>';
                } else {
                    $html = '<a  href="' . url('process/' . $list->form_url . '/' . $redirect_path['view'] . '/' . Encryption::encodeId($list->ref_id) . '/' . Encryption::encodeId($list->process_type_id)) . '" class="btn btn-xs btn-primary button-color ' . $class['button_class'] . ' " style="color: white"> <i class="fa fa-folder-open"></i> Open</a><br>';
                }
//                }
                return $html;
            })
            ->editColumn('json_object', function ($list) use ($request) {
                return @getListDataFromJson($list->json_object, $list->company_name);
            })
            ->editColumn('tracking_no', function ($list) use ($desk, $request, $class) {

                $existingFavoriteItem = CommonFunction::checkFavoriteItem($list->id);
                $htm = '';
                if ($existingFavoriteItem > 0) {
                    $htm .= '<i  style="cursor: pointer;color:#f0ad4e" class="fas fa-star remove_favorite_process" title="Added to your favorite list" id=' . Encryption::encodeId($list->id) . '></i> ' . $list->tracking_no;
                } else {
                    $htm .= '<i style="cursor: pointer" class="far fa-star favorite_process"  title="Add to your favorite list" id=' . Encryption::encodeId($list->id) . '></i> ' . $list->tracking_no;
                }

                if ($desk != 'favorite_list') {
                    $htm .= '<input type="hidden" class="' . $class['input_class'] . '" name="batch_input"  value=' . Encryption::encodeId($list->id) . '>';
                }

                return $htm;
            })
            //            ->addColumn('desk', function ($list) {
            //                //return $list->desk_id == 0 ? 'Applicant' : $list->desk_name;
            //                return $list->desk_id == 0 ? 'Applicant' : ($list->user_id == 0 ? $list->desk_name : ('<span>' . $list->desk_name . '</span><br/><span>' . $list->user_first_name . ' ' . $list->user_last_name . '</span>'));
            //            })
            ->editColumn('desk_id', function ($list) {
                //return $list->desk_id == 0 ? 'Applicant' : $list->desk_name;
                return $list->desk_id == 0 ? 'Applicant' : ($list->user_id == 0 ? $list->desk_name : ('<span>' . $list->desk_name . '</span><br/><span>' . $list->user_first_name . ' ' . $list->user_last_name . '</span>'));
            })
            //            ->editColumn('updated_at', function ($list) {
            //                return CommonFunction::updatedOn($list->updated_at);
            //            })
            ->filterColumn('status_name_updated_time', function ($query, $keyword) {
                $sql = "CONCAT(process_status.status_name,'<br/>',process_list.updated_at) like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            //->removeColumn('id', 'ref_id', 'process_type_id', 'updated_by', 'closed_by', 'created_by', 'updated_by', 'status_id', 'locked_by', 'ref_fields')
            ->removeColumn('id', 'ref_id', 'process_type_id', 'updated_by', 'closed_by', 'created_by', 'updated_by', 'status_id', 'ref_fields')
            ->setRowAttr([
                'style' => function ($list) {
                    $color = '';
                    if ($list->priority == 1) {
                        //                        $color .= 'background:#f000';
                        $color .= '';
                    } elseif ($list->priority == 2) {
                        $color .= '    background: -webkit-linear-gradient(left, rgba(220,251,199,1) 0%, rgba(220,251,199,1) 80%, rgba(255,255,255,1) 100%);';
                    } elseif ($list->priority == 3) {
                        $color .= '    background: -webkit-linear-gradient(left, rgba(255,251,199,1) 0%, rgba(255,251,199,1) 40%, rgba(255,251,199,1) 80%, rgba(255,255,255,1) 100%);';
                    }
                    return $color;
                },
//                'class' => function ($list) use ($get_user_desk_ids) {
//                    //                    if($list->read_status == 0){
//                    if (!in_array($list->status_id, [-1, 5, 6, 25]) && $list->read_status == 0 && in_array($list->desk_id, $get_user_desk_ids)) {
//                        return 'unreadMessage';
//                    } elseif (in_array($list->status_id, [5, 6, 25]) && $list->read_status == 0 && $list->created_by == CommonFunction::getUserId()) {
//                        return 'unreadMessage';
//                    }
//                }
            ])
            ->make(true);
    }

    //    public function getStatusWiseList(Request $request)
    //    {
    //        $process_type_id = $request->get('process_type_id');
    //        $status = $request->get('process_status');
    //        $list = ProcessList::getStatusWiseApplication($process_type_id, $status);
    //        $get_user_desk_ids = explode(",", CommonFunction::getDeskId());
    //
    //        return Datatables::of($list)
    //            ->addColumn('action', function ($list) use ($status, $request) {
    //                if ($list->locked_by > 0
    //                    && Carbon::createFromFormat('Y-m-d H:i:s', $list->locked_at)->diffInMinutes() < 3 and $list->locked_by != Auth::user()->id
    //                ) {
    //                    $locked_by_user = UsersModel::where('id', $list->locked_by)
    //                        ->select(DB::raw("CONCAT(users.user_first_name,' ',users.user_middle_name, ' ',users.user_last_name) as user_name"))
    //                        ->pluck('user_name');
    //                    $html = '<img width="20" src="' . url('/assets/images/Lock-icon_2.png') . '"/>' .
    //                        '<a onclick="return confirm(' . "'The record locked by $locked_by_user, would you like to force unlock?'" . ')"
    //                            target="_blank" href="' . url('process/' . $list->form_url . '/view/' . Encryption::encodeId($list->ref_id) . '/' . Encryption::encodeId($list->process_type_id)) . '"
    //                            class="btn btn-xs btn-primary button-color"> Open</a> &nbsp; ';
    //
    //                } else {
    //                    $html = '<a target="_blank" href="' . url('process/' . $list->form_url . '/view/' . Encryption::encodeId($list->ref_id) . '/' . Encryption::encodeId($list->process_type_id)) . '" class="btn btn-xs btn-primary button-color" style="color: white"> <i class="fa fa-folder-open"></i> Open</a>  &nbsp;';
    //                }
    //                if (!empty($request->get('is_feedback'))) { //Application wise feedback related task
    //                    $process_id = "'" . Encryption::encodeId($list->id) . "'";
    //                    $html .= '<a type="button" onclick="rating(' . $process_id . ')" href="javascript:void(0)" class="btn btn-xs btn-success button-color" style="color: white"> <i class="fa fa-comment"></i> Feedback</a>  &nbsp;';
    //                }
    //                return $html;
    //            })
    //            ->editColumn('json_object', function ($list) use ($request) {
    //                if (empty($request->get('is_feedback'))) { //Application wise feedback related task
    //                    return @getListDataFromJson($list->json_object, $list->company_name);
    //                }
    //            })
    //            ->editColumn('rating', function ($list) use ($request) {
    //                $feedbackR = '';
    //                if (isset($request->is_feedback_row)) {
    //                    $feedbackR = @getRating($list);
    //                }
    //                return $feedbackR;
    //
    //            })
    //            ->editColumn('tracking_no', function ($list) {
    //                $existingFavoriteItem = CommonFunction::checkFavoriteItem($list->id);
    //                if ($existingFavoriteItem > 0) {
    //                    return '<i style="cursor: pointer;color:#f0ad4e" class="fas fa-star remove_favorite_process" title="Added to your favorite list" id=' . Encryption::encodeId($list->id) . '></i> ' . $list->tracking_no;
    //                } else {
    //                    return '<i style="cursor: pointer" class="far fa-star favorite_process"  title="Add to your favorite list" id=' . Encryption::encodeId($list->id) . '></i> ' . $list->tracking_no;
    //                }
    //            })
    //            ->addColumn('desk', function ($list) {
    //                //return $list->desk_id == 0 ? 'Applicant' : $list->desk_name;
    //                return $list->desk_id == 0 ? 'Applicant' : ($list->user_id == 0 ? $list->desk_name : ('<span>' . $list->desk_name . '</span><br/><span>' . $list->user_first_name . ' ' . $list->user_last_name . '</span>'));
    //            })
    ////            ->editColumn('updated_at', function ($list) {
    ////                return CommonFunction::updatedOn($list->updated_at);
    ////            })
    //            ->filterColumn('status_name_updated_time', function ($query, $keyword) {
    //                $sql = "CONCAT(process_status.status_name,'<br/>',process_list.updated_at) like ?";
    //                $query->whereRaw($sql, ["%{$keyword}%"]);
    //            })
    //            ->removeColumn('id', 'ref_id', 'process_type_id', 'updated_by', 'closed_by', 'created_by', 'updated_by', 'desk_id', 'status_id', 'locked_by', 'ref_fields')
    //            ->setRowAttr([
    //                'style' => function ($list) {
    //                    $color = '';
    //                    if ($list->priority == 1) {
    ////                        $color .= 'background:#f000';
    //                        $color .= '';
    //                    } elseif ($list->priority == 2) {
    //                        $color .= '    background: -webkit-linear-gradient(left, rgba(220,251,199,1) 0%, rgba(220,251,199,1) 80%, rgba(255,255,255,1) 100%);';
    //                    } elseif ($list->priority == 3) {
    //                        $color .= '    background: -webkit-linear-gradient(left, rgba(255,251,199,1) 0%, rgba(255,251,199,1) 40%, rgba(255,251,199,1) 80%, rgba(255,255,255,1) 100%);';
    //                    }
    //                    return $color;
    //                },
    //                'class' => function ($list) use ($get_user_desk_ids) {
    ////                    if($list->read_status == 0){
    //                    if (!in_array($list->status_id, [-1, 5, 6, 25]) && $list->read_status == 0 && in_array($list->desk_id, $get_user_desk_ids)) {
    //                        return 'unreadMessage';
    //                    } elseif (in_array($list->status_id, [5, 6, 25]) && $list->read_status == 0 && $list->created_by == CommonFunction::getUserId()) {
    //                        return 'unreadMessage';
    //                    }
    //                }
    //            ])
    //            ->make(true);
    //    }

    public function getDeskByStatus(Request $request)
    {
        $process_list_id = Encryption::decodeId($request->get('process_list_id'));
        $status_from = Encryption::decodeId($request->get('status_from'));
        $cat_id = Encryption::decodeId($request->get('cat_id'));
        $statusId = trim($request->get('statusId'));

        $processInfo = ProcessList::where('id', $process_list_id)
            ->first([
                'process_type_id', 'desk_id', 'ref_id', 'department_id'
            ]);

        //only for board meeting proceed to meting
        $ProceedToMeeting = $this->ProceedToMeeting($statusId, $processInfo->process_type_id); //return 0 or 1

        $sql = "SELECT DGN.id, DGN.desk_name
                        FROM user_desk DGN WHERE
                        find_in_set(DGN.id,
                        (SELECT desk_to FROM process_path APP
                         where APP.desk_from LIKE '%$processInfo->desk_id%'
                            AND APP.status_from = '$status_from'
                            AND APP.cat_id = '$cat_id'
                            AND APP.process_type_id = '$processInfo->process_type_id'
                            AND APP.status_to REGEXP '^([0-9]*[,]+)*$statusId([,]+[,0-9]*)*$')) ";


        $deskList = \DB::select(DB::raw($sql));

        //extra sql code here
        $ext_sql = "SELECT APP.id, APP.ext_sql1 FROM process_path APP WHERE APP.desk_from LIKE '%$processInfo->desk_id%'
            AND APP.status_from = '$status_from'
            AND APP.process_type_id = '$processInfo->process_type_id'
            AND APP.cat_id = '$cat_id'
            AND APP.status_to LIKE '%$statusId%' limit 1";

        $ext_sql_data = \DB::select(DB::raw($ext_sql));

        $singleUserList = [];
        if (count($ext_sql_data) > 0) {
            if ($ext_sql_data[0]->ext_sql1 != null) { // ext_sql one not null
                //                $fullSql = $ext_sql_data[0]->ext_sql1.$processInfo->ref_id.' ORDER BY PP.id desc limit 1'; // concat app id
                //                $fullSql = $ext_sql_data[0]->ext_sql1.$processInfo->ref_id; // concat app id
                $fullSql = str_replace("{app_id}", "$processInfo->ref_id", $ext_sql_data[0]->ext_sql1);
                $ext_sql_desk_list = \DB::select(DB::raw($fullSql));
                //dd($ext_sql_desk_list);
                //                if ($ext_sql_desk_list[0]->desk_name !=''){
                //                    $deskList0['id'] = $ext_sql_desk_list[0]->desk_id; // assign new desk list from new query
                //                    $deskList0['desk_name'] = $ext_sql_desk_list[0]->desk_name; // assign new desk list from new query
                //
                //                    $singleUserList['user_id'] = $ext_sql_desk_list[0]->user_id; // assign new desk list from new query
                //                    $singleUserList['user_name'] = $ext_sql_desk_list[0]->user_full_name; // assign new desk list from new query
                //                    $deskList[] = (object)$deskList0;
                //                    if ($deskList == null){ // desk = null or no desk
                //                        $deskList =[];
                //                    }
                //                }
            }
        }
        //end of extra sql


        $list = array();
        foreach ($deskList as $k => $v) {
            $tmpDeskId = $v->id;
            $list[$tmpDeskId] = $v->desk_name;
        }
        $fileRemarkData = "SELECT APP.id, APP.file_attachment,APP.remarks,APP.desk_to
                                   FROM process_path APP
                                   WHERE APP.desk_from LIKE '%$processInfo->desk_id%'
                                   AND APP.status_from = '$status_from'
                                   AND APP.cat_id = '$cat_id'
                                   AND APP.process_type_id = '$processInfo->process_type_id'
                                   AND APP.status_to REGEXP '^([0-9]*[,]+)*$statusId([,]+[,0-9]*)*$'  limit 1";

        $fileRemarkData = \DB::select(DB::raw($fileRemarkData));
        $fileAttach = 0;
        $remarks = 0;
        if (count($fileRemarkData) > 0) {
            $fileAttach = $fileRemarkData[0]->file_attachment;
            $remarks = $fileRemarkData[0]->remarks;
        }

        if (count($ext_sql_data) > 0 && $ext_sql_data[0]->ext_sql1 != null) {
            if ($ext_sql_desk_list[0]->returnStatus == 1) {
                $applicable_desk = $list;
            } else {
                $applicable_desk = [];
            }
        } else {
            $applicable_desk = $list;
        }

        $processTypeFinalStatus = ProcessType::where('id', $processInfo->process_type_id)->first(['final_status']);
        $finalStatus = explode(",", $processTypeFinalStatus->final_status);
        $pinNumber = '';
        //        if (in_array($statusId, $finalStatus)) {  //checking final status
        //            $result = CommonFunction::requestPinNumber();
        //            if ($result == true)
        //                $pinNumber = 1;
        //        }
        //        dd($statusId, $processInfo->process_type_id, $processInfo->ref_id, $processInfo->department_id);

        // Condition approved remarks
        $conditional_approved_remarks = ProcessHistory::where('process_type', $processInfo->process_type_id)
            ->where('ref_id', $processInfo->ref_id)
            ->whereIn('status_id', [15, 17])
            ->first(['process_desc']);
        $conditional_approved_remarks = !empty($conditional_approved_remarks) ? $conditional_approved_remarks->process_desc : '';

        /* load add on form content*/
        $html = $this->requestFormContent($statusId, $processInfo->process_type_id, $processInfo->ref_id, $processInfo->department_id, $conditional_approved_remarks);

        $data = [
            'responseCode' => 1, 'data' => $applicable_desk, 'html' => $html, 'remarks' => $remarks,
            'file_attachment' => $fileAttach, 'pin_number' => $pinNumber,
            'users' => $singleUserList,
            'chk_sts' => $statusId,
            'meeting_number' => $ProceedToMeeting,
            'conditional_approved_remarks' => $conditional_approved_remarks
        ];

        return response()->json($data);
    }

    protected function getUserByDesk(Request $request)
    {
        $desk_to = trim($request->get('desk_to'));
        $statusId = trim($request->get('statusId'));
        $app_id = Encryption::decodeId($request->get('app_id'));
        $cat_id = Encryption::decodeId($request->get('cat_id'));
        $status_from = Encryption::decodeId($request->get('status_from'));
        $desk_from = Encryption::decodeId($request->get('desk_from'));
        $process_type_id = Encryption::decodeId($request->get('process_type_id'));
        $department_id = Encryption::decodeId($request->get('department_id'));
        $sub_department_id = Encryption::decodeId($request->get('sub_department_id'));
        $approval_center_id = Encryption::decodeId($request->get('approval_center_id'));
        $ext_sql = "SELECT APP.id, APP.ext_sql, APP.ext_sql2 
            FROM process_path APP WHERE APP.desk_from LIKE '%$desk_from%'
            AND APP.status_from = '$status_from'
            AND APP.cat_id = '$cat_id'
            AND APP.process_type_id = '$process_type_id'
            AND APP.status_to LIKE '%$statusId%' limit 1";
        $ext_sql_data = \DB::select(DB::raw($ext_sql));

        if ($ext_sql_data[0]->ext_sql2 != null) { // ext_sql two not null
            $extraSQL = $ext_sql_data[0]->ext_sql2;
            $fullSql = str_replace("{desk_to}", "$desk_to", $extraSQL);
            $fullSql1 = str_replace("{app_id}", "$app_id", $fullSql);
            //$fullSql = $ext_sql_data[0]->ext_sql2." REGEXP '^([0-9]*[,]+)*$desk_to([,]+[,0-9]*)*$'"; // concat app id
            $userList = \DB::select(DB::raw($fullSql1));
        } else {
            if (in_array(CommonFunction::getUserType(), ['9x901', '9x902', '9x903', '9x904'])) {
                $sql = "SELECT id as user_id, concat(user_first_name,' ', user_middle_name, ' ', user_last_name) as user_full_name from users WHERE is_approved = 1
                AND user_status='active'
                AND desk_id REGEXP '^([0-9]*[,]+)*$desk_to([,]+[,0-9]*)*$' ";
                //            } elseif ($process_type_id == 100 && $desk_from == 5 && $desk_to == 1 && $statusId == 10) {   // Basic information haven't process flow right now
                //                $sql = "SELECT id as user_id, concat(user_first_name,' ', user_middle_name, ' ', user_last_name) as user_full_name from users WHERE is_approved = 1
                //                AND user_status='active'
                //                AND desk_id REGEXP '^([0-9]*[,]+)*$desk_to([,]+[,0-9]*)*$' ";
            } elseif (in_array($process_type_id, [12, 102, 13, 14, 15, 16, 1, 10, 2, 3, 4, 5])) {  //BR=102, BRA=12 IRC 1st=13, IRC 2nd=14, IRC 3rd=15, IRC Regular=16, VRN=1, VRA=10, WPN=2,WPE=3,WPA=4,WPC=5
                $sql = "SELECT id as user_id, concat(user_first_name,' ', user_middle_name, ' ', user_last_name) as user_full_name from users WHERE is_approved = 1
                AND user_status='active'
                AND desk_id REGEXP '^([0-9]*[,]+)*$desk_to([,]+[,0-9]*)*$'
                AND department_id REGEXP '^([0-9]*[,]+)*$department_id([,]+[,0-9]*)*$'
                AND sub_department_id REGEXP '^([0-9]*[,]+)*$sub_department_id([,]+[,0-9]*)*$'
                AND division_id REGEXP '^([0-9]*[,]+)*$approval_center_id([,]+[,0-9]*)*$' ";
            } else {
                $sql = "SELECT id as user_id, concat(user_first_name,' ', user_middle_name, ' ', user_last_name) as user_full_name from users WHERE is_approved = 1
                AND user_status='active'
                AND desk_id REGEXP '^([0-9]*[,]+)*$desk_to([,]+[,0-9]*)*$'
                AND department_id REGEXP '^([0-9]*[,]+)*$department_id([,]+[,0-9]*)*$'
                AND sub_department_id REGEXP '^([0-9]*[,]+)*$sub_department_id([,]+[,0-9]*)*$'";
            }
            $userList = DB::select(DB::raw($sql));
        }
        $data = ['responseCode' => 1, 'data' => $userList];
        return response()->json($data);
    }


    /*
     * Application Processing
     */
    public function updateProcess(Request $request)
    {
        $rules = [
            'status_id' => 'required|numeric',
        ];
        if ($request->get('is_remarks_required') == 1) {
            $rules['remarks'] = 'required';
        }
        if ($request->get('is_file_required') == 1) {
            $rules['attach_file'] = 'requiredarray';
        }

        if ($request->has('desk_status')) {
            $rules['desk_status'] = 'required|numeric';
        }

        //        if ($request->has('assign_dept_id')) {
        //            $rules['assign_dept_id'] = 'required|numeric';
        //        }

        if ($request->has('is_user')) {
            $rules['is_user'] = 'required|numeric';
        }

        // if (in_array($request->get('status_id'), [5, 15])) {
        //     $rules['resend_deadline'] = 'required';
        // }

        if ($request->has('pin_number')) {
            if ($request->get('pin_number') == '') {
                \Session::flash('error', "Pin number Field Is Required [PPC-1000]");
                return redirect()->back();
            }
        }
        $customMessages = [
            'status_id.required' => 'Apply Status Field Is Required',
            'remarks.required' => 'Remarks Field Is Required',
            'attach_file.requiredarray' => 'Attach File Field Is Required',
        ];
        $this->validate($request, $rules, $customMessages);

        try {

            // if isset Application processing PIN number, then match the PIN
            if ($request->has('pin_number')) {
                $security_code = trim($request->get('pin_number'));
                $user_id = CommonFunction::getUserId();
                $pin_number = $security_code . '-' . $user_id;
                $encrypted_pin = Encryption::encode($pin_number);
                $count = Users::where('id', $user_id)->where(['pin_number' => $encrypted_pin])->count();
                if ($count <= 0) {
                    \Session::flash('error', "Security Code doesn't match. [PPC-1001]");
                    return redirect()->back();
                }
            }

            $process_list_id = Encryption::decodeId($request->get('process_list_id'));
            $cat_id = Encryption::decodeId($request->get('cat_id'));
            $existProcessInfo = ProcessList::leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
                ->where('process_list.id', $process_list_id)
                ->first([
                    'process_type.name as process_type_name',
                    'process_type.process_supper_name',
                    'process_type.process_sub_name',
                    'process_list.*'
                ]);

            $statusID = trim($request->get('status_id'));
            $deskID = ($request->has('desk_id') ? trim($request->get('desk_id')) : 0);

            /*
             * Verify Process Path
             * Check whether the application's process_type_id and cat_id and status_from
             * and desk_from and desk_to and status_to are equal with any of one row from process_path table
             */
            $process_path_count = DB::select(DB::raw("select count(*) as procss_path from process_path
                                        where process_type_id = $existProcessInfo->process_type_id
                                        AND cat_id = $cat_id
										AND status_from = $existProcessInfo->status_id
                                        AND desk_from = $existProcessInfo->desk_id
                                        AND desk_to = $deskID
                                        AND status_to REGEXP '^([0-9]*[,]+)*$statusID([,]+[,0-9]*)*$'"));

            if ($existProcessInfo->process_type_id != 100 && $process_path_count[0]->procss_path == 0) {
                //Session::flash('error', 'Sorry, invalid process request.[PPC-1002]');
                Session::flash('error', 'Sorry, application already processed by another user.[PPC-1002]');
                return redirect('process/list/' . Encryption::encodeId($existProcessInfo->process_type_id));
            }

            // Process data verification, if verification is true then proceed for Processing
            if ($request->get('data_verification') == Encryption::encode(UtilFunction::processVerifyData($existProcessInfo))) {
                $processData = [];
                $on_behalf_of_user = 0;
                //$my_desk_ids = CommonFunction::getUserDeskIds();

                //                if (!in_array($existProcessInfo->desk_id,$my_desk_ids)) {
                //                    $on_behalf_of_user = Encryption::decodeId($request->get('on_behalf_user_id'));
                //                }

                if ($request->get('on_behalf_user_id') != "") {
                    if (Encryption::decodeId($request->get('on_behalf_user_id')) != 0) {
                        $on_behalf_of_user = Encryption::decodeId($request->get('on_behalf_user_id'));
                    }
                }

                // Desk user identify checking
                $user_id = 0;
                if ($request->has('is_user')) {
                    $user_id = trim($request->get('is_user'));
                    $findUser = Users::where('id', $user_id)->count();
                    if (empty($findUser)) {
                        \Session::flash('error', 'Desk user not found!.[PPC-1019]');
                        return Redirect::back()->withInput();
                    }
                }


                /*
                 * if this is basic information application and help desk
                 * then check that, application's assigned department and desk user's department
                 * is same or not.
                 */
                //                if ($existProcessInfo->process_type_id == 100 && $existProcessInfo->desk_id == 5 && in_array(5, CommonFunction::getUserDeskIds()) && $request->has('is_user')) {
                //                    $usersDept = explode(',', $findUser->department_id);
                //                    if (!in_array(trim($request->get('assign_dept_id')), $usersDept)) {
                //                        DB::rollback();
                //                        \Session::flash('error', "Desk user's department should be same as like as Applications department!.[PPC-1021]");
                //                        return Redirect::back()->withInput();
                //                    }
                //                    $processData['sub_department_id'] = $findUser->sub_department_id;
                //                }

                // Updating process list
                $status_from = $existProcessInfo->status_id;
                $deskFrom = $existProcessInfo->desk_id;

                // if process type is Basic Information and application desk id is equal to 5 and current user's desk is 1 or 2 or 3
                // then desk and status set as below
                //                if ($existProcessInfo->process_type_id == 100 && $existProcessInfo->desk_id == 5 && (in_array(1, CommonFunction::getUserDeskIds()) or in_array(2, CommonFunction::getUserDeskIds()) or in_array(3, CommonFunction::getUserDeskIds()))) {
                //                    $status_from = 10;
                //                    $deskFrom = 1;
                //                }

                if (empty($deskID)) {
                    $whereCond = "select * from process_path where process_type_id='$existProcessInfo->process_type_id' AND cat_id ='$cat_id' AND status_from = '$status_from' AND desk_from = '$deskFrom'
                        AND status_to REGEXP '^([0-9]*[,]+)*$statusID([,]+[,0-9]*)*$'";


                    $processPath = DB::select(DB::raw($whereCond));
                    $deskList = null;
                    // previous ext_sql, now ext_sql use for status load.
                    if (count($processPath) > 0 && $processPath[0]->ext_sql1 != "NULL" && $processPath[0]->ext_sql1 != "") { // ext_sql not null
                        $fullSql = $processPath[0]->ext_sql1 . $existProcessInfo->ref_id; // concat app id
                        $ext_sql_desk_list = \DB::select(DB::raw($fullSql));
                        if ($ext_sql_desk_list[0]->returnStatus == 1) { // company_type_id = 1 and pr_cer_uplodead = no
                            $deskList = $ext_sql_desk_list; // assign new desk list from new query
                            //                            if ($deskList[0]->deskIsnull != -100){ // desk = null or no desk
                            //                                $desk_id = $deskList[0]->deskIsnull;
                            //                            }
                        }


                        //                        elseif($ext_sql_desk_list[0]->returnStatus == -100){ // continue the previous query
                        //                            if ($processPath[0]->desk_to == '0')  // Sent to Applicant
                        //                                $desk_id = 0;
                        //                            if ($processPath[0]->desk_to == '-1')   // Keep in same desk
                        //                                $desk_id = $deskFrom;
                        //                        }
                    }
                    if (!empty($deskList[0]->deskIsnull) && $deskList[0]->deskIsnull != -100) {
                        $deskID = $deskList[0]->deskIsnull;
                    } else {
                        $deskID = 0;
                        $user_id = 0;
                        if (count($processPath) > 0 && $processPath[0]->desk_to == '0')  // Sent to Applicant
                            $deskID = 0;
                        if (count($processPath) > 0 && $processPath[0]->desk_to == '-1') {  // Keep in same desk
                            $deskID = $deskFrom;
                            $user_id = CommonFunction::getUserId(); //user wise application assign
                        }
                    }
                }

//                if ($statusID == 0) {
//                    \Session::flash('error', "Invalid Status Id. [PPC-1015]");
//                    return redirect()->back();
//                }

                // Process data for modification
                $processData['desk_id'] = $deskID;
                $processData['status_id'] = $statusID;
                //'priority' => $request->get('priority'),
                $processData['priority'] = 1;
                $processData['process_desc'] = $request->get('remarks');
                $processData['approval_copy_remarks'] = $request->get('approval_copy_remarks');
                $processData['user_id'] = $user_id;
                $processData['on_behalf_of_user'] = $on_behalf_of_user;
                $processData['updated_by'] = Auth::user()->id;
                //$processData['locked_by'] = 0;
                //$processData['locked_at'] = null;
                //$processData['read_status'] = 0;

                if (
                    in_array($request->get('status_id'), [5, 32]) ||
                    ($request->get('status_id') == 15 && !in_array($existProcessInfo->process_type_id, [5, 9])) // 5 = WPC, 9 = OPC
                ) {
                    if (empty($request->get('resend_deadline'))) {
                        $processData['resend_deadline'] = $this->getResendDeadline();
                    } else {
                        $processData['resend_deadline'] = date('Y-m-d', strtotime($request->get('resend_deadline')));
                    }
                }

                $processTypeFinalStatus = ProcessType::where('id', $existProcessInfo->process_type_id)->first(['final_status']);
                $finalStatus = explode(",", $processTypeFinalStatus->final_status);
                $closed_by = 0;
                if (in_array($statusID, $finalStatus)) {  //checking final status and current status are same ??
                    $closed_by = $processData['closed_by'] = CommonFunction::getUserId();
                    $processData['completed_date'] = date('Y-m-d H:i:s');
                }


                // Process Hash value generate
                $id = $existProcessInfo->id;
                $ref_id = $existProcessInfo->ref_id;
                $trackingNo = $existProcessInfo->tracking_no;
                $desk_id = $deskID;
                $processTypeId = $existProcessInfo->process_type_id;
                $status_id = $request->get('status_id');
                $on_behalf_of_users = $on_behalf_of_user;
                $process_desc = $request->get('remarks');
                $closed_byy = $closed_by;
                $locked_at = null;
                $locked_by = 0;
                $updated_by = Auth::user()->id;
                $result = $id . ', ' . $ref_id . ', ' . $trackingNo . ', ' . $desk_id . ', ' . $processTypeId . ',' . $status_id . ', '
                    . $on_behalf_of_users . ', ' . $process_desc . ', ' . $closed_byy . ', ' . $locked_at . ', ' . $locked_by . ',' . $updated_by;


                $keyPair = KeyPair::generateKeyPair(2048);

                $publicKey = $keyPair->getPublicKey();

                $hashData = EasyRSA::encrypt($result, $publicKey);
                $previousHash = $existProcessInfo->hash_value;

                $processData['previous_hash'] = $previousHash;
                $processData['hash_value'] = $hashData;
                // End of Process Hash value generate


                DB::beginTransaction();

                $file_path = "";
                if ($request->hasFile('attach_file')) {
                    $attach_file = $request->file('attach_file');
                    foreach ($attach_file as $afile) {
                        $original_file = $afile->getClientOriginalName();
                        $original_file_name = pathinfo($original_file, PATHINFO_FILENAME);
                        $original_file_extension = $afile->getClientOriginalExtension();
                        $slug_file_name = Str::slug($original_file_name, '-');
                        $file_name = $slug_file_name . '-' . time() . '.' . $original_file_extension;
                        $afile->move('uploads/', $file_name);
                        $file = new ProcessDoc;
                        $file->process_type_id = $existProcessInfo->process_type_id;
                        $file->ref_id = $process_list_id;
                        $file->desk_id = $request->get('desk_id');
                        $file->status_id = $request->get('status_id');
                        $file_path = 'uploads/' . $file_name;
                        $file->file = $file_path;
                        $file->save();
                    }
                }

                //checking agenda and entry agenda wise process
                if (isset($request->board_meeting_id)) {
                    $processData['user_id'] = $existProcessInfo->user_id;
                    $processData['desk_id'] = 6;
                    $board_meeting_id = trim($request->get('board_meeting_id'));
                    $basic_salary_from_dd = $request->get('basic_salary');

                    // desired_duration related code for meeting module
                    if ($processTypeId == 5 || $processTypeId == 9) { // 5 = Work Permit Cancellation, 9 = Office Permission Cancellation
                        $duration_start_date_from_dd = isset($request->approved_effect_date) ? date('Y-m-d', strtotime($request->approved_effect_date)) : null;
                    } else {
                        $duration_start_date_from_dd = isset($request->approved_duration_start_date) ? date('Y-m-d', strtotime($request->approved_duration_start_date)) : null;
                    }
                    $duration_end_date_from_dd = isset($request->approved_duration_end_date) ? date('Y-m-d', strtotime($request->approved_duration_end_date)) : null;
                    $desired_duration_from_dd = isset($request->approved_desired_duration) ? $request->approved_desired_duration : "";
                    $duration_amount_from_dd = isset($request->approved_duration_amount) ? $request->approved_duration_amount : "";
                    // end desired_duration related code for meeting module

                    $application_id = decodeId($request->get('application_ids')[0]);

                    $findBoardMeeting = BoardMeting::where('id', $board_meeting_id)->count();
                    if (empty($findBoardMeeting)) {
                        DB::rollback();
                        \Session::flash('error', 'Board Meeting not found!.[PPC-1020]');
                        return Redirect::back()->withInput();
                    }
                    $getBmProcessId = $this->assignAgendaProcess(
                        $statusID,
                        $process_list_id,
                        $board_meeting_id,
                        $existProcessInfo->process_type_id,
                        $basic_salary_from_dd,
                        $application_id,
                        $process_desc,
                        $duration_start_date_from_dd,
                        $duration_end_date_from_dd,
                        $desired_duration_from_dd,
                        $duration_amount_from_dd
                    );
                    if (empty($getBmProcessId)) {
                        DB::rollback();
                        \Session::flash('error', 'Agenda assigning failure!.[PPC-1022]');
                        return Redirect::back()->withInput();
                    }
                    $processData['bm_process_id'] = $getBmProcessId;
                }


                //checking Reference Number Incorporation Number
                //$this->storeRefIncNo($statusID, $process_list_id, $existProcessInfo->process_type_id, $process_desc, $request, $file_path);


                // Update Department id for Basic Info
                // it's only for BIDA
                //                if ($existProcessInfo->process_type_id == 100 && in_array($existProcessInfo->status_id, [1, 2]) && empty($existProcessInfo->department_id)) {
                //                    $processData['department_id'] = $request->get('assign_dept_id');
                //                }

                // dd($existProcessInfo);
                ProcessList::where('id', $existProcessInfo->id)->update($processData);

                /*
                 * process type wise, process status wise additional info update
                 * application certificate generation, email or sms sending function,
                 * During the processing of the application, the data provided by the desk user in the add-on form is given
                 * CertificateMailOtherData() comes from app\Modules\ProcessPath\helper.php
                 */

                $result = $this->CertificateMailOtherData($existProcessInfo->id, $statusID, $existProcessInfo->desk_id, $request->all(), $this);
                // dd($result);
                if ($result == false) {
                    DB::rollback();
                    // Session error message will come from CertificateMailOtherData() function (if needed)
                    return Redirect::back()->withInput();
                }
                //                $getAclName = ProcessType::where('id',$existProcessInfo->process_type_id)->first()->acl_name;
                //                $this->requestShadowFile($existProcessInfo->id, $existProcessInfo->ref_id, $existProcessInfo->process_type_id, $getAclName);

                DB::commit();

                // batch update
                $batch_process_id = Session::has('batch_process_id') ? Session::get('batch_process_id') : [];
                if (isset($request->is_batch_update) && isset($request->single_process_id_encrypt) && is_array($batch_process_id) && count($batch_process_id) > 0) {
                    //$batch_process_id = Session::get('batch_process_id');

                    $single_process_id_encrypt_next = null;
                    $single_process_id_encrypt_second_next_key = null;
                    $find_current_key = array_search($request->get('single_process_id_encrypt'), $batch_process_id); //find current key
                    $keys = array_keys($batch_process_id); //total key
                    $nextKey = isset($keys[array_search($find_current_key, $keys) + 1]) ? $keys[array_search($find_current_key, $keys) + 1] : ''; //next key
                    $second_nextKey = isset($keys[array_search($find_current_key, $keys) + 2]) ? $keys[array_search($find_current_key, $keys) + 2] : ''; //second next key

                    if (!empty($nextKey)) {
                        $single_process_id_encrypt_next = $batch_process_id[$nextKey]; //next process id
                    }
                    if (!empty($second_nextKey)) {
                        $single_process_id_encrypt_second_next_key = $batch_process_id[$second_nextKey]; //next second process id
                    }

                    if (empty($single_process_id_encrypt_next)) {
                        \Session::flash('success', 'Process has been updated successfully.');
                        return redirect('process/list/' . Encryption::encodeId($existProcessInfo->process_type_id));
                    }

                    Session::put('single_process_id_encrypt', $single_process_id_encrypt_next);
                    $nextAppInfo = 'null';
                    if ($single_process_id_encrypt_second_next_key != null) {
                        $nextAppInfo = ProcessList::where('process_list.id', Encryption::decodeId($single_process_id_encrypt_second_next_key))->first(['tracking_no'])->tracking_no;
                    }

                    Session::put('next_app_info', $nextAppInfo);
                    $get_total_process_app = Session::get('total_process_app');
                    Session::put('total_process_app', $get_total_process_app + 1);

                    $processData = ProcessList::leftJoin('process_type', 'process_list.process_type_id', '=', 'process_type.id')
                        ->where('process_list.id', Encryption::decodeId($single_process_id_encrypt_next))->first(['process_type.form_url', 'process_type.form_id', 'process_list.ref_id', 'process_list.process_type_id']);

                    $redirect_path = CommonFunction::getAppRedirectPathByJson($processData->form_id);
                    $redirectUrl = 'process/' . $processData->form_url . '/' . $redirect_path['view'] . '/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id);

                    // \Session::flash('success', 'Process has been updated successfully.');

                    return redirect($redirectUrl);
                }
                //end

                \Session::flash('success', 'Process has been updated successfully.');
            } else {
                \Session::flash('error', 'Sorry, Data has been updated by another user. [PPC-1003]');
            }
            return redirect('process/list/' . Encryption::encodeId($existProcessInfo->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            // dd($e->getMessage() , $e->getFile() , $e->getLine());
            Log::error('PPCUpdateProcess: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [PPC-1004]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage(), $e->getFile()) . '[PPC-1004]');
            return redirect()->back();
        }
    }

    public function getResendDeadline()
    {
        $deadline_days = (int) Configuration::where('caption', 'APP_RESEND_DEADLINE')->pluck('value');

        $todays_date = strtotime(date('Y-m-d'));
        $day_count = 0;

        $holidays = Holiday::where('is_active', 1)
            ->where('holiday_date', '>=', date('Y-m-d'))
            ->lists('holiday_date')
            ->toArray();
        $resend_deadline_date = '';

        for ($work_day = $todays_date; $day_count < $deadline_days; $work_day += 86400) {

            $day_name = date('D', $work_day);

            /**
             * if this day is not Friday and Saturday and not on the holiday list
             * then count this day as working day
             */
            if ($day_name != 'Fri' && $day_name != 'Sat' && !in_array(date('Y-m-d', $work_day), $holidays)) {
                $day_count++;
            }

            /**
             * if working day counting is equal to configure value
             * then set this day as resend deadline
             */
            if ($day_count === $deadline_days) {
                $resend_deadline_date = date('Y-m-d', $work_day);
            }
        }

        return $resend_deadline_date;
    }


    public function requestFormContent($CurrentStatusId, $process_type_id, $ref_id, $dept_id, $conditional_approved_remarks)
    {
        $form_id = ProcessStatus::where('process_type_id', $process_type_id)->where('id', $CurrentStatusId)->pluck('form_id');

        if ($form_id == 'AddOnForm/noc-data-process') {
            $imeiData = Imei::where('ref_id', $ref_id)->get();
            $getDesk = UserDesk::all();
            $appData = NocModel::where('id', $ref_id)->first();
            $public_html = strval(view("ProcessPath::{$form_id}", compact('imeiData', 'getDesk', 'appData')));
        } elseif ($form_id == 'AddOnForm/assign-dept') {
            if (empty($dept_id) or $dept_id == '') {
                // select all department type without Communication Department name.
                $departmentList = ['' => 'Select one'] + DepartmentInfo::where('id', '!=', 4)->where('is_archive', 0)->where('status', 1)->orderBy('name')->lists('name', 'id')->all();
                $public_html = strval(view("ProcessPath::{$form_id}", compact('departmentList')));
            } else {
                $public_html = '';
            }
        } elseif ($form_id == 'AddOnForm/wpn_duration') {
            $wpn_info = WorkPermitNew::where('id', $ref_id)
                ->first([
                    'approved_duration_start_date',
                    'approved_duration_end_date',
                    'approved_desired_duration',
                    'approved_duration_amount',
                ]);
            $public_html = strval(view("ProcessPath::{$form_id}", compact('process_type_id', 'wpn_info', 'CurrentStatusId')));
        } elseif ($form_id == 'AddOnForm/wpa_duration') {
            $wpa_info = ProcessList::leftJoin('wpa_apps as apps', 'apps.id', '=', 'process_list.ref_id')
                ->where('process_list.ref_id', $ref_id)
                ->where('process_list.process_type_id', $process_type_id)
                ->first([
                    'n_duration_start_date',
                    'n_duration_end_date',
                    'n_desired_duration',
                    'approved_duration_start_date',
                    'approved_duration_end_date',
                    'approved_desired_duration',
                    'approved_effective_date',
                    'n_desired_amount',
                    'status_id'
                ]);
            if (in_array($CurrentStatusId, [8, 9, 15, 19])) {
                $public_html = strval(view("ProcessPath::{$form_id}", compact('process_type_id', 'wpa_info', 'CurrentStatusId')));
            } else {
                $public_html = '';
            }
        } elseif ($form_id == 'AddOnForm/wpe_duration') {
            $wpe_info = WorkPermitExtension::where('id', $ref_id)
                ->first([
                    'approved_duration_start_date',
                    'approved_duration_end_date',
                    'approved_desired_duration',
                    'approved_duration_amount',
                ]);
            $public_html = strval(view("ProcessPath::{$form_id}", compact('process_type_id', 'wpe_info', 'CurrentStatusId')));
        } elseif ($form_id == 'AddOnForm/wpc_effect_date') {
            $wpc_info = WorkPermitCancellation::where('id', $ref_id)
                ->first([
                    'approved_effect_date',
                ]);
            $public_html = strval(view("ProcessPath::{$form_id}", compact('wpc_info', 'CurrentStatusId')));
        } elseif ($form_id == 'AddOnForm/opn_duration') {
            $opn_info = OfficePermissionNew::where('id', $ref_id)
                ->first([
                    'approved_duration_start_date',
                    'approved_duration_end_date',
                    'approved_desired_duration',
                    'approved_duration_amount',
                ]);
            $public_html = strval(view("ProcessPath::{$form_id}", compact('process_type_id', 'opn_info', 'CurrentStatusId')));
        } elseif ($form_id == 'AddOnForm/ope_duration') {
            $ope_info = OfficePermissionExtension::where('id', $ref_id)
                ->first([
                    'approved_duration_start_date',
                    'approved_duration_end_date',
                    'approved_desired_duration',
                    'approved_duration_amount',
                    'is_remittance_allowed',
                ]);
            $public_html = strval(view("ProcessPath::{$form_id}", compact('process_type_id', 'ope_info', 'CurrentStatusId')));
        } elseif ($form_id == 'AddOnForm/opa_approved_effective_date') {
            $opa_info = OfficePermissionAmendment::where('id', $ref_id)
                ->first([
                    'approved_effective_date'
                ]);
            $public_html = strval(view("ProcessPath::{$form_id}", compact('process_type_id', 'opa_info', 'CurrentStatusId')));
        } elseif ($form_id == 'AddOnForm/opc_effect_date') {
            $opc_info = OfficePermissionCancellation::where('id', $ref_id)
                ->first([
                    'approved_effect_date',
                ]);
            $public_html = strval(view("ProcessPath::{$form_id}", compact('opc_info', 'CurrentStatusId')));
        } elseif ($form_id == 'AddOnForm/account_amount') {
            $public_html = strval(view("ProcessPath::{$form_id}"));
        }
        elseif ($form_id == 'AddOnForm/memo_details') {
            $memo_info = WaiverCondition8::where('id', $ref_id)
                ->first([
                    'memo_no',
                    'memo_date',
                    'memo_attachment',
                ]);

            if (in_array($CurrentStatusId, [5, 6, 10, 11, 13, 14, 25])) {
                $public_html = strval(view("ProcessPath::{$form_id}", compact('memo_info', 'CurrentStatusId')));
            } else {
                $public_html = '';
            }

            // $public_html = strval(view("ProcessPath::{$form_id}", compact('memo_info', 'CurrentStatusId')));
        }elseif ($form_id == 'AddOnForm/ircn_inspec_report') {

            // check this inspection officer has any submitted report and load data from inspection table
            $appInfo = ProcessList::leftJoin('irc_inspection as apps', 'apps.app_id', '=', 'process_list.ref_id')
                ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
                ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                    $join->on('ps.id', '=', 'process_list.status_id');
                    $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
                })
                ->where('process_list.ref_id', $ref_id)
                ->where('process_list.process_type_id', $process_type_id)
                ->where('apps.created_by', Auth::user()->id)
                ->orderBy('apps.id', 'DESC')
                ->first([
                    'process_list.id as process_list_id',
                    'process_list.desk_id',
                    'process_list.ref_id',
                    'process_list.tracking_no',
                    'process_list.submitted_at',
                    'ps.status_name',
                    'apps.*',
                ]);

            if (!empty($appInfo)) {
                $appInfo->id = $appInfo->app_id; // $ref_id
            } else {
                // load fresh data from irc_app table
                $appInfo = ProcessList::leftJoin('irc_apps as apps', 'apps.id', '=', 'process_list.ref_id')
                    ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
                    ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                        $join->on('ps.id', '=', 'process_list.status_id');
                        $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
                    })
                    ->leftJoin('area_info as office_thana', 'office_thana.area_id', '=', 'apps.office_thana_id')
                    ->leftJoin('area_info as office_district', 'office_district.area_id', '=', 'apps.office_district_id')
                    ->leftJoin('area_info as factory_thana', 'factory_thana.area_id', '=', 'apps.factory_thana_id')
                    ->leftJoin('area_info as factory_district', 'factory_district.area_id', '=', 'apps.factory_district_id')
                    ->where('process_list.ref_id', $ref_id)
                    ->where('process_list.process_type_id', $process_type_id)
                    ->first([
                        'process_list.id as process_list_id',
                        'process_list.desk_id',
                        'process_list.ref_id',
                        'process_list.tracking_no',
                        'process_list.submitted_at',
                        'ps.status_name',
                        'office_thana.area_nm as office_thana_name',
                        'office_district.area_nm as office_district_name',
                        'factory_thana.area_nm as factory_thana_name',
                        'factory_district.area_nm as factory_district_name',
                        'apps.id',
                        'apps.company_name',
                        'apps.class_code',
                        'apps.sub_class_id',
                        'apps.organization_status_id',
                        'apps.ceo_full_name',
                        'apps.ceo_address',
                        'apps.ref_app_tracking_no',
                        'apps.reg_no',
                        'apps.ref_app_approve_date',
                        'apps.trade_licence_num',
                        'apps.trade_licence_issuing_authority',
                        'apps.trade_licence_issue_date',
                        'apps.trade_licence_validity_period',
                        'apps.tin_number',
                        'apps.tin_issuing_authority',
                        'apps.bank_id',
                        'branch_id',
                        'apps.bank_account_number',
                        'apps.bank_address',
                        'apps.bank_account_title',
                        'apps.assoc_membership_number',
                        'apps.assoc_chamber_name',
                        'apps.assoc_issuing_date',
                        'apps.assoc_expire_date',
                        'apps.fire_license_info',
                        'apps.fl_number',
                        'apps.fl_expire_date',
                        'apps.fl_application_number',
                        'apps.fl_apply_date',
                        'apps.fl_issuing_authority',
                        'apps.inc_number',
                        'apps.inc_issuing_authority',
                        'apps.environment_clearance',
                        'apps.el_number',
                        'apps.el_expire_date',
                        'apps.el_application_number',
                        'apps.el_apply_date',
                        'apps.el_issuing_authority',
                        'apps.project_status_id',
                        'apps.local_land_ivst',
                        'apps.local_building_ivst',
                        'apps.local_machinery_ivst',
                        'apps.local_others_ivst',
                        'apps.local_wc_ivst',
                        'apps.total_fixed_ivst_million',
                        'apps.total_fixed_ivst',
                        'apps.usd_exchange_rate',
                        'apps.total_fee',
                        'apps.em_local_total_taka_mil',
                        'apps.em_lc_total_taka_mil',
                        'apps.local_male',
                        'apps.local_female',
                        'apps.local_total',
                        'apps.foreign_male',
                        'apps.foreign_female',
                        'apps.foreign_total',
                        'apps.manpower_total',
                        'apps.manpower_local_ratio',
                        'apps.manpower_foreign_ratio',
                        'apps.irc_purpose_id',
                        'apps.office_address',
                        'apps.office_post_office',
                        'apps.office_post_code',
                        'apps.factory_address',
                        'apps.factory_post_office',
                        'apps.factory_post_code',
                        'apps.other_sub_class_code',
                        'apps.other_sub_class_name'
                    ]);

                $appInfo->office_address = $appInfo->office_address . ', ' . $appInfo->office_post_office . ', ' . $appInfo->office_thana_name . ', ' . $appInfo->office_district_name . '- ' . $appInfo->office_post_code;
                $appInfo->factory_address = $appInfo->factory_address . ', ' . $appInfo->factory_post_office . ', ' . $appInfo->factory_thana_name . ', ' . $appInfo->factory_district_name . '- ' . $appInfo->factory_post_code;
                //Business Sector
                $query = DB::select("
                            Select 
                            sec_class.id, 
                            sec_class.code, 
                            sec_class.name, 
                            sec_group.id as group_id,
                            sec_group.code as group_code,
                            sec_group.name as group_name,
                            sec_division.id as division_id,
                            sec_division.code as division_code,
                            sec_division.name as division_name,
                            sec_section.id as section_id,
                            sec_section.code as section_code,
                            sec_section.name as section_name
                            from (select * from sector_info_bbs where type = 4) sec_class
                            left join sector_info_bbs sec_group on sec_class.pare_id = sec_group.id 
                            left join sector_info_bbs sec_division on sec_group.pare_id = sec_division.id 
                            left join sector_info_bbs sec_section on sec_division.pare_id = sec_section.id 
                            where sec_class.code = '$appInfo->class_code' limit 1;
                          ");

                $busness_code = json_decode(json_encode($query), true);

                if ($appInfo->sub_class_id == 0) {
                    $sub_class = ['name' => $appInfo->other_sub_class_code .' - '.$appInfo->other_sub_class_name];
                } else {
                    $sub_class = BusinessClass::select('id', DB::raw("CONCAT(code, ' - ', name) as name"))->where('id', $appInfo->sub_class_id)->first();
                }
                $appInfo->industrial_sector = $appInfo->class_code . ' - ' . $busness_code[0]['section_name'] . ', ' . $sub_class['name'];
            }

            $annualProductionCapacity = AnnualProductionCapacity::leftJoin('product_unit', 'product_unit.id', '=', 'irc_annual_production_capacity.quantity_unit')
                ->where('irc_annual_production_capacity.app_id', $appInfo->id)
                ->where('irc_annual_production_capacity.status', 1)->where('irc_annual_production_capacity.is_archive', 0)
                ->get(['product_unit.name as unit_name', 'irc_annual_production_capacity.*']);

            $annualProductionSpareParts = AnnualProductionSpareParts::leftJoin('product_unit', 'product_unit.id', '=', 'irc_annual_production_spare_parts.quantity_unit')
                ->where('irc_annual_production_spare_parts.app_id', $appInfo->id)
                ->where('irc_annual_production_spare_parts.status', 1)->where('irc_annual_production_spare_parts.is_archive', 0)
                ->get(['product_unit.name as unit_name', 'irc_annual_production_spare_parts.*']);

            $projectStatusList = IrcProjectStatus::where('is_archive', 0)->where('inspection_status', 1)->lists('name', 'id');
            $productUnit = ['' => 'Select one'] + ProductUnit::where('status', 1)->where('is_archive', 0)->lists('name', 'id')->all();
            $currencyBDT = Currencies::orderBy('code')->whereIn('code', ['BDT'])->where('is_archive', 0)
                ->where('is_active', 1)->lists('code', 'id');
            $eaOrganizationStatus = ['' => 'Select one'] + EA_OrganizationStatus::where('is_archive', 0)
                    ->where('status', 1)->orderBy('name')->lists('name', 'id')->all();

            $banks = ['' => 'Select One'] + Bank::where('is_active', 1)->where('is_archive', 0)->orderBy(
                    'name',
                    'asc'
                )->lists('name', 'id')->all();

            $totalFee = DB::table('irc_inspection_gov_fee_range')->where('status', 1)->get();

            $public_html = strval(view("ProcessPath::{$form_id}", compact(
                'appInfo',
                'projectStatusList',
                'annualProductionCapacity',
                'annualProductionSpareParts',
                'eaOrganizationStatus',
                'currencyBDT',
                'productUnit',
                'banks',
                'totalFee'
            )));
        } elseif ($form_id == 'AddOnForm/irc_2nd_inspec_report') {
            // check this inspection officer has any submitted report and load data from inspection table
            $appInfo = ProcessList::leftJoin('irc_2nd_inspection as apps', 'apps.app_id', '=', 'process_list.ref_id')
                ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
                ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                    $join->on('ps.id', '=', 'process_list.status_id');
                    $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
                })
                ->where('process_list.ref_id', $ref_id)
                ->where('process_list.process_type_id', $process_type_id)
                ->where('apps.created_by', Auth::user()->id)
                ->orderBy('apps.id', 'DESC')
                ->first([
                    'process_list.id as process_list_id',
                    'process_list.desk_id',
                    'process_list.ref_id',
                    'process_list.tracking_no',
                    'process_list.submitted_at',
                    'ps.status_name',
                    'apps.*',
                ]);

            if (!empty($appInfo)) {
                $appInfo->id = $appInfo->app_id; // $ref_id
            } else {
                // load fresh data from irc_2nd_apps table
                $appInfo = ProcessList::leftJoin('irc_2nd_apps as apps', 'apps.id', '=', 'process_list.ref_id')
                    ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
                    ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                        $join->on('ps.id', '=', 'process_list.status_id');
                        $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
                    })
                    ->leftJoin('area_info as office_thana', 'office_thana.area_id', '=', 'apps.office_thana_id')
                    ->leftJoin('area_info as office_district', 'office_district.area_id', '=', 'apps.office_district_id')
                    ->leftJoin('area_info as factory_thana', 'factory_thana.area_id', '=', 'apps.factory_thana_id')
                    ->leftJoin('area_info as factory_district', 'factory_district.area_id', '=', 'apps.factory_district_id')
                    ->where('process_list.ref_id', $ref_id)
                    ->where('process_list.process_type_id', $process_type_id)
                    ->first([
                        'process_list.id as process_list_id',
                        'process_list.desk_id',
                        'process_list.ref_id',
                        'process_list.tracking_no',
                        'process_list.submitted_at',
                        'ps.status_name',
                        'office_thana.area_nm as office_thana_name',
                        'office_district.area_nm as office_district_name',
                        'factory_thana.area_nm as factory_thana_name',
                        'factory_district.area_nm as factory_district_name',
                        'apps.id',
                        'apps.company_name',
                        'apps.class_code',
                        'apps.sub_class_id',
                        'apps.organization_status_id',
                        'apps.ceo_full_name',
                        'apps.ceo_address',
                        'apps.last_br',
                        'apps.br_ref_app_tracking_no',
                        'apps.br_manually_approved_no',
                        'apps.irc_ref_app_tracking_no',
                        'apps.reg_no',
                        'apps.br_ref_app_approve_date',
                        'apps.trade_licence_num',
                        'apps.trade_licence_issuing_authority',
                        'apps.trade_licence_issue_date',
                        'apps.trade_licence_validity_period',
                        'apps.tin_number',
                        'apps.tin_issuing_authority',
                        'apps.bank_id',
                        'branch_id',
                        'apps.bank_account_number',
                        'apps.bank_account_title',
                        'apps.assoc_membership_number',
                        'apps.assoc_chamber_name',
                        'apps.assoc_issuing_date',
                        'apps.assoc_expire_date',
                        'apps.fl_number',
                        'apps.fl_expire_date',
                        'apps.fl_issuing_authority',
                        'apps.inc_number',
                        'apps.inc_issuing_authority',
                        'apps.el_number',
                        'apps.el_expire_date',
                        'apps.el_issuing_authority',
                        'apps.project_status_id',
                        'apps.local_land_ivst',
                        'apps.local_building_ivst',
                        'apps.local_machinery_ivst',
                        'apps.local_others_ivst',
                        'apps.local_wc_ivst',
                        'apps.total_fixed_ivst_million',
                        'apps.total_fixed_ivst',
                        'apps.usd_exchange_rate',
                        'apps.total_fee',
                        'apps.local_male',
                        'apps.local_female',
                        'apps.local_total',
                        'apps.foreign_male',
                        'apps.foreign_female',
                        'apps.foreign_total',
                        'apps.manpower_total',
                        'apps.manpower_local_ratio',
                        'apps.manpower_foreign_ratio',
                        'apps.irc_purpose_id',
                        'apps.office_address',
                        'apps.office_post_office',
                        'apps.office_post_code',
                        'apps.factory_address',
                        'apps.factory_post_office',
                        'apps.factory_post_code',
                        'apps.other_sub_class_code',
                        'apps.other_sub_class_name',
                        'apps.ex_machine_local_value_bdt',
                        'apps.ex_machine_imported_value_bdt',
                        'apps.ins_apc_half_yearly_import_total',
                        'apps.ins_apc_half_yearly_import_total_in_word',
                        'apps.first_em_lc_total_five_percent',
                        'apps.first_em_lc_total_five_percent_in_word'
                    ]);

                $appInfo->office_address = $appInfo->office_address . ', ' . $appInfo->office_post_office . ', ' . $appInfo->office_thana_name . ', ' . $appInfo->office_district_name . '- ' . $appInfo->office_post_code;
                $appInfo->factory_address = $appInfo->factory_address . ', ' . $appInfo->factory_post_office . ', ' . $appInfo->factory_thana_name . ', ' . $appInfo->factory_district_name . '- ' . $appInfo->factory_post_code;

                //Business Sector
                $query = DB::select("
                            Select 
                            sec_class.id, 
                            sec_class.code, 
                            sec_class.name, 
                            sec_group.id as group_id,
                            sec_group.code as group_code,
                            sec_group.name as group_name,
                            sec_division.id as division_id,
                            sec_division.code as division_code,
                            sec_division.name as division_name,
                            sec_section.id as section_id,
                            sec_section.code as section_code,
                            sec_section.name as section_name
                            from (select * from sector_info_bbs where type = 4) sec_class
                            left join sector_info_bbs sec_group on sec_class.pare_id = sec_group.id 
                            left join sector_info_bbs sec_division on sec_group.pare_id = sec_division.id 
                            left join sector_info_bbs sec_section on sec_division.pare_id = sec_section.id 
                            where sec_class.code = '$appInfo->class_code' limit 1;
                          ");

                $busness_code = json_decode(json_encode($query), true);
                if ($appInfo->sub_class_id == 0) {
                    $sub_class = ['name' => $appInfo->other_sub_class_code .' - '.$appInfo->other_sub_class_name];
                } else {
                    $sub_class = BusinessClass::select('id', DB::raw("CONCAT(code, ' - ', name) as name"))->where('id', $appInfo->sub_class_id)->first();
                }
                $appInfo->industrial_sector = $appInfo->class_code . ' - ' . $busness_code[0]['section_name'] . ', ' . $sub_class['name'];

                //section 5
                $appInfo->registering_authority_memo_no = ($appInfo->last_br == 'yes') ? $appInfo->br_ref_app_tracking_no : $appInfo->br_manually_approved_no;
                $appInfo->date_of_registration = empty($appInfo->br_ref_app_approve_date) ? '' : date('d-M-Y', strtotime($appInfo->br_ref_app_approve_date));

                //section 9
                $appInfo->em_local_total_taka_mil = $appInfo->ex_machine_local_value_bdt;
                $appInfo->em_lc_total_taka_mil = $appInfo->ex_machine_imported_value_bdt;

                //section 12
                $appInfo->apc_half_yearly_import_total = $appInfo->ins_apc_half_yearly_import_total;
                $appInfo->apc_half_yearly_import_total_in_word = $appInfo->ins_apc_half_yearly_import_total_in_word;

                //section 13
                if ($appInfo->irc_purpose_id != 1) {
                    $getInspectionId = IRCCommonPool::where('first_adhoc_tracking_no', $appInfo->irc_ref_app_tracking_no)->first(['first_adhoc_inspection_id']);
                    if (!empty($getInspectionId)) {
                        $ircFirstAdhocInfo = IrcInspection::where('id', $getInspectionId->first_adhoc_inspection_id)->where('ins_approved_status', 1)
                            ->first(['em_lc_total_taka_mil', 'em_lc_total_percent', 'em_lc_total_five_percent', 'em_lc_total_five_percent_in_word']);

                        $appInfo->em_lc_total_taka_mil = !empty($ircFirstAdhocInfo->em_lc_total_taka_mil) ? $ircFirstAdhocInfo->em_lc_total_taka_mil : "";
                        $appInfo->em_lc_total_percent = !empty($ircFirstAdhocInfo->em_lc_total_percent) ? $ircFirstAdhocInfo->em_lc_total_percent : "";
                        $appInfo->em_lc_total_five_percent = !empty($ircFirstAdhocInfo->em_lc_total_five_percent) ? $ircFirstAdhocInfo->em_lc_total_five_percent : $appInfo->first_em_lc_total_five_percent;
                        $appInfo->em_lc_total_five_percent_in_word = !empty($ircFirstAdhocInfo->em_lc_total_five_percent_in_word) ? $ircFirstAdhocInfo->em_lc_total_five_percent_in_word : $appInfo->first_em_lc_total_five_percent_in_word;
                    }
                }
            }

            $annualProductionCapacity = SecondAnnualProductionCapacity::leftJoin('product_unit', 'product_unit.id', '=', 'irc_2nd_annual_production_capacity.quantity_unit')
                ->where('irc_2nd_annual_production_capacity.app_id', $appInfo->id)
                ->where('irc_2nd_annual_production_capacity.status', 1)->where('irc_2nd_annual_production_capacity.is_archive', 0)
                ->get(['product_unit.name as unit_name', 'irc_2nd_annual_production_capacity.*']);

            $ircSixMonthsImportRawMaterials = IrcSixMonthsImportRawMaterial::leftJoin('product_unit', 'product_unit.id', '=', 'irc_six_months_import_capacity_raw.quantity_unit')
                ->where('irc_six_months_import_capacity_raw.app_id', $appInfo->id)
                ->where('irc_six_months_import_capacity_raw.process_type_id', 14)
                ->where('irc_six_months_import_capacity_raw.status', 1)->where('irc_six_months_import_capacity_raw.is_archive', 0)
                ->get(['product_unit.name as unit_name', 'irc_six_months_import_capacity_raw.*']);


            $annualProductionSpareParts = SecondAnnualProductionSpareParts::leftJoin('product_unit', 'product_unit.id', '=', 'irc_2nd_annual_production_spare_parts.quantity_unit')
                ->where('irc_2nd_annual_production_spare_parts.app_id', $appInfo->id)
                ->where('irc_2nd_annual_production_spare_parts.status', 1)->where('irc_2nd_annual_production_spare_parts.is_archive', 0)
                ->get(['product_unit.name as unit_name', 'irc_2nd_annual_production_spare_parts.*']);

            $projectStatusList = IrcProjectStatus::where('is_archive', 0)->where('inspection_status', 1)->lists('name', 'id');

            $productUnit = ['' => 'Select one'] + ProductUnit::where('status', 1)->where('is_archive', 0)->lists('name', 'id')->all();
            $currencyBDT = Currencies::orderBy('code')->whereIn('code', ['BDT'])->where('is_archive', 0)
                ->where('is_active', 1)->lists('code', 'id');
            $eaOrganizationStatus = ['' => 'Select one'] + EA_OrganizationStatus::where('is_archive', 0)
                    ->where('status', 1)->orderBy('name')->lists('name', 'id')->all();

            $banks = ['' => 'Select One'] + Bank::where('is_active', 1)->where('is_archive', 0)->orderBy(
                    'name',
                    'asc'
                )->lists('name', 'id')->all();

            $public_html = strval(view("ProcessPath::{$form_id}", compact(
                'appInfo',
                'projectStatusList',
                'annualProductionCapacity',
                'annualProductionSpareParts',
                'eaOrganizationStatus',
                'currencyBDT',
                'productUnit',
                'banks',
                'ircSixMonthsImportRawMaterials'
            )));
        } elseif ($form_id == 'AddOnForm/irc_3rd_inspec_report') {
            // check this inspection officer has any submitted report and load data from inspection table
            $appInfo = ProcessList::leftJoin('irc_3rd_inspection as apps', 'apps.app_id', '=', 'process_list.ref_id')
                ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
                ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                    $join->on('ps.id', '=', 'process_list.status_id');
                    $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
                })
                ->where('process_list.ref_id', $ref_id)
                ->where('process_list.process_type_id', $process_type_id)
                ->where('apps.created_by', Auth::user()->id)
                ->orderBy('apps.id', 'DESC')
                ->first([
                    'process_list.id as process_list_id',
                    'process_list.desk_id',
                    'process_list.ref_id',
                    'process_list.tracking_no',
                    'process_list.submitted_at',
                    'ps.status_name',
                    'apps.*',
                ]);

            if (!empty($appInfo)) {
                $appInfo->id = $appInfo->app_id; // $ref_id
            } else {
                // load fresh data from irc_3rd_apps table
                $appInfo = ProcessList::leftJoin('irc_3rd_apps as apps', 'apps.id', '=', 'process_list.ref_id')
                    ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
                    ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                        $join->on('ps.id', '=', 'process_list.status_id');
                        $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
                    })
                    ->leftJoin('area_info as office_thana', 'office_thana.area_id', '=', 'apps.office_thana_id')
                    ->leftJoin('area_info as office_district', 'office_district.area_id', '=', 'apps.office_district_id')
                    ->leftJoin('area_info as factory_thana', 'factory_thana.area_id', '=', 'apps.factory_thana_id')
                    ->leftJoin('area_info as factory_district', 'factory_district.area_id', '=', 'apps.factory_district_id')
                    ->where('process_list.ref_id', $ref_id)
                    ->where('process_list.process_type_id', $process_type_id)
                    ->first([
                        'process_list.id as process_list_id',
                        'process_list.desk_id',
                        'process_list.ref_id',
                        'process_list.tracking_no',
                        'process_list.submitted_at',
                        'ps.status_name',
                        'office_thana.area_nm as office_thana_name',
                        'office_district.area_nm as office_district_name',
                        'factory_thana.area_nm as factory_thana_name',
                        'factory_district.area_nm as factory_district_name',
                        'apps.id',
                        'apps.company_name',
                        'apps.class_code',
                        'apps.sub_class_id',
                        'apps.organization_status_id',
                        'apps.ceo_full_name',
                        'apps.ceo_address',
                        'apps.last_br',
                        'apps.br_ref_app_tracking_no',
                        'apps.br_manually_approved_no',
                        'apps.irc_ref_app_tracking_no',
                        'apps.reg_no',
                        'apps.br_ref_app_approve_date',
                        'apps.trade_licence_num',
                        'apps.trade_licence_issuing_authority',
                        'apps.trade_licence_issue_date',
                        'apps.trade_licence_validity_period',
                        'apps.tin_number',
                        'apps.tin_issuing_authority',
                        'apps.bank_id',
                        'branch_id',
                        'apps.bank_account_number',
                        'apps.bank_account_title',
                        'apps.assoc_membership_number',
                        'apps.assoc_chamber_name',
                        'apps.assoc_issuing_date',
                        'apps.assoc_expire_date',
                        'apps.fl_number',
                        'apps.fl_expire_date',
                        'apps.fl_issuing_authority',
                        'apps.inc_number',
                        'apps.inc_issuing_authority',
                        'apps.el_number',
                        'apps.el_expire_date',
                        'apps.el_issuing_authority',
                        'apps.project_status_id',
                        'apps.local_land_ivst',
                        'apps.local_building_ivst',
                        'apps.local_machinery_ivst',
                        'apps.local_others_ivst',
                        'apps.local_wc_ivst',
                        'apps.total_fixed_ivst_million',
                        'apps.total_fixed_ivst',
                        'apps.usd_exchange_rate',
                        'apps.total_fee',
                        'apps.local_male',
                        'apps.local_female',
                        'apps.local_total',
                        'apps.foreign_male',
                        'apps.foreign_female',
                        'apps.foreign_total',
                        'apps.manpower_total',
                        'apps.manpower_local_ratio',
                        'apps.manpower_foreign_ratio',
                        'apps.irc_purpose_id',
                        'apps.office_address',
                        'apps.office_post_office',
                        'apps.office_post_code',
                        'apps.factory_address',
                        'apps.factory_post_office',
                        'apps.factory_post_code',
                        'apps.other_sub_class_code',
                        'apps.other_sub_class_name',
                        'apps.ex_machine_local_value_bdt',
                        'apps.ex_machine_imported_value_bdt',
                        'apps.ins_apc_half_yearly_import_total',
                        'apps.ins_apc_half_yearly_import_total_in_word',
                        'apps.first_em_lc_total_five_percent',
                        'apps.first_em_lc_total_five_percent_in_word',
                    ]);

                $appInfo->office_address = $appInfo->office_address . ', ' . $appInfo->office_post_office . ', ' . $appInfo->office_thana_name . ', ' . $appInfo->office_district_name . '- ' . $appInfo->office_post_code;
                $appInfo->factory_address = $appInfo->factory_address . ', ' . $appInfo->factory_post_office . ', ' . $appInfo->factory_thana_name . ', ' . $appInfo->factory_district_name . '- ' . $appInfo->factory_post_code;
                //Business Sector
                $query = DB::select("
                            Select 
                            sec_class.id, 
                            sec_class.code, 
                            sec_class.name, 
                            sec_group.id as group_id,
                            sec_group.code as group_code,
                            sec_group.name as group_name,
                            sec_division.id as division_id,
                            sec_division.code as division_code,
                            sec_division.name as division_name,
                            sec_section.id as section_id,
                            sec_section.code as section_code,
                            sec_section.name as section_name
                            from (select * from sector_info_bbs where type = 4) sec_class
                            left join sector_info_bbs sec_group on sec_class.pare_id = sec_group.id 
                            left join sector_info_bbs sec_division on sec_group.pare_id = sec_division.id 
                            left join sector_info_bbs sec_section on sec_division.pare_id = sec_section.id 
                            where sec_class.code = '$appInfo->class_code' limit 1;
                          ");

                $busness_code = json_decode(json_encode($query), true);

                if ($appInfo->sub_class_id == 0) {
                    $sub_class = ['name' => $appInfo->other_sub_class_code .' - '.$appInfo->other_sub_class_name];
                } else {
                    $sub_class = BusinessClass::select('id', DB::raw("CONCAT(code, ' - ', name) as name"))->where('id', $appInfo->sub_class_id)->first();
                }
                $appInfo->industrial_sector = $appInfo->class_code . ' - ' . $busness_code[0]['section_name'] . ', ' . $sub_class['name'];

                //section 5
                $appInfo->registering_authority_memo_no = ($appInfo->last_br == 'yes') ? $appInfo->br_ref_app_tracking_no : $appInfo->br_manually_approved_no;
                $appInfo->date_of_registration = empty($appInfo->br_ref_app_approve_date) ? '' : date('d-M-Y', strtotime($appInfo->br_ref_app_approve_date));

                //section 9
                $appInfo->em_local_total_taka_mil = $appInfo->ex_machine_local_value_bdt;
                $appInfo->em_lc_total_taka_mil = $appInfo->ex_machine_imported_value_bdt;

                //section 12
                $appInfo->apc_half_yearly_import_total = $appInfo->ins_apc_half_yearly_import_total;
                $appInfo->apc_half_yearly_import_total_in_word = $appInfo->ins_apc_half_yearly_import_total_in_word;

                //section 13
                if ($appInfo->irc_purpose_id != 1) {
                    $getInspectionId = IRCCommonPool::where('second_adhoc_tracking_no', $appInfo->irc_ref_app_tracking_no)->first(['second_adhoc_inspection_id']);
                    if (!empty($getInspectionId)) {
                        $ircSecondAdhocInfo = SecondIrcInspection::where('id', $getInspectionId->second_adhoc_inspection_id)->where('ins_approved_status', 1)
                            ->first(['em_lc_total_taka_mil', 'em_lc_total_percent', 'em_lc_total_five_percent', 'em_lc_total_five_percent_in_word']);
                        $appInfo->em_lc_total_taka_mil = !empty($ircSecondAdhocInfo->em_lc_total_taka_mil) ? $ircSecondAdhocInfo->em_lc_total_taka_mil : "";
                        $appInfo->em_lc_total_percent = !empty($ircSecondAdhocInfo->em_lc_total_percent) ? $ircSecondAdhocInfo->em_lc_total_percent : "";
                        $appInfo->em_lc_total_five_percent = !empty($ircSecondAdhocInfo->em_lc_total_five_percent) ? $ircSecondAdhocInfo->em_lc_total_five_percent : $appInfo->first_em_lc_total_five_percent;
                        $appInfo->em_lc_total_five_percent_in_word = !empty($ircSecondAdhocInfo->em_lc_total_five_percent_in_word) ? $ircSecondAdhocInfo->em_lc_total_five_percent_in_word : $appInfo->first_em_lc_total_five_percent_in_word;
                    }
                }
            }

            $annualProductionCapacity = ThirdAnnualProductionCapacity::leftJoin('product_unit', 'product_unit.id', '=', 'irc_3rd_annual_production_capacity.quantity_unit')
                ->where('irc_3rd_annual_production_capacity.app_id', $appInfo->id)
                ->where('irc_3rd_annual_production_capacity.status', 1)->where('irc_3rd_annual_production_capacity.is_archive', 0)
                ->get(['product_unit.name as unit_name', 'irc_3rd_annual_production_capacity.*']);

            $ircSixMonthsImportRawMaterials = IrcSixMonthsImportRawMaterial::leftJoin('product_unit', 'product_unit.id', '=', 'irc_six_months_import_capacity_raw.quantity_unit')
                ->where('irc_six_months_import_capacity_raw.app_id', $appInfo->id)
                ->where('irc_six_months_import_capacity_raw.process_type_id', 15)
                ->where('irc_six_months_import_capacity_raw.status', 1)->where('irc_six_months_import_capacity_raw.is_archive', 0)
                ->get(['product_unit.name as unit_name', 'irc_six_months_import_capacity_raw.*']);


            $annualProductionSpareParts = ThirdAnnualProductionSpareParts::leftJoin('product_unit', 'product_unit.id', '=', 'irc_3rd_annual_production_spare_parts.quantity_unit')
                ->where('irc_3rd_annual_production_spare_parts.app_id', $appInfo->id)
                ->where('irc_3rd_annual_production_spare_parts.status', 1)->where('irc_3rd_annual_production_spare_parts.is_archive', 0)
                ->get(['product_unit.name as unit_name', 'irc_3rd_annual_production_spare_parts.*']);

            $projectStatusList = IrcProjectStatus::where('is_archive', 0)->where('inspection_status', 1)->lists('name', 'id');

            $productUnit = ['' => 'Select one'] + ProductUnit::where('status', 1)->where('is_archive', 0)->lists('name', 'id')->all();
            $currencyBDT = Currencies::orderBy('code')->whereIn('code', ['BDT'])->where('is_archive', 0)
                ->where('is_active', 1)->lists('code', 'id');
            $eaOrganizationStatus = ['' => 'Select one'] + EA_OrganizationStatus::where('is_archive', 0)
                    ->where('status', 1)->orderBy('name')->lists('name', 'id')->all();

            $banks = ['' => 'Select One'] + Bank::where('is_active', 1)->where('is_archive', 0)->orderBy(
                    'name',
                    'asc'
                )->lists('name', 'id')->all();

            $public_html = strval(view("ProcessPath::{$form_id}", compact(
                'appInfo',
                'projectStatusList',
                'annualProductionCapacity',
                'annualProductionSpareParts',
                'eaOrganizationStatus',
                'currencyBDT',
                'productUnit',
                'banks',
                'ircSixMonthsImportRawMaterials'
            )));
        } elseif ($form_id == 'AddOnForm/irc_regular_inspec_report') {
            // check this inspection officer has any submitted report and load data from inspection table
            $appInfo = ProcessList::leftJoin('irc_regular_inspection as apps', 'apps.app_id', '=', 'process_list.ref_id')
                ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
                ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                    $join->on('ps.id', '=', 'process_list.status_id');
                    $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
                })
                ->where('process_list.ref_id', $ref_id)
                ->where('process_list.process_type_id', $process_type_id)
                ->where('apps.created_by', Auth::user()->id)
                ->orderBy('apps.id', 'DESC')
                ->first([
                    'process_list.id as process_list_id',
                    'process_list.desk_id',
                    'process_list.ref_id',
                    'process_list.tracking_no',
                    'process_list.submitted_at',
                    'ps.status_name',
                    'apps.*',
                ]);

            if (!empty($appInfo)) {
                $appInfo->id = $appInfo->app_id; // $ref_id
            } else {
                // load fresh data from irc_regular_apps table
                $appInfo = ProcessList::leftJoin('irc_regular_apps as apps', 'apps.id', '=', 'process_list.ref_id')
                    ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
                    ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                        $join->on('ps.id', '=', 'process_list.status_id');
                        $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
                    })
                    ->leftJoin('area_info as office_thana', 'office_thana.area_id', '=', 'apps.office_thana_id')
                    ->leftJoin('area_info as office_district', 'office_district.area_id', '=', 'apps.office_district_id')
                    ->leftJoin('area_info as factory_thana', 'factory_thana.area_id', '=', 'apps.factory_thana_id')
                    ->leftJoin('area_info as factory_district', 'factory_district.area_id', '=', 'apps.factory_district_id')
                    ->where('process_list.ref_id', $ref_id)
                    ->where('process_list.process_type_id', $process_type_id)
                    ->first([
                        'process_list.id as process_list_id',
                        'process_list.desk_id',
                        'process_list.ref_id',
                        'process_list.tracking_no',
                        'process_list.submitted_at',
                        'ps.status_name',
                        'office_thana.area_nm as office_thana_name',
                        'office_district.area_nm as office_district_name',
                        'factory_thana.area_nm as factory_thana_name',
                        'factory_district.area_nm as factory_district_name',
                        'apps.id',
                        'apps.company_name',
                        'apps.class_code',
                        'apps.sub_class_id',
                        'apps.organization_status_id',
                        'apps.ceo_full_name',
                        'apps.ceo_address',
                        'apps.last_br',
                        'apps.br_ref_app_tracking_no',
                        'apps.br_manually_approved_no',
                        'apps.irc_ref_app_tracking_no',
                        'apps.reg_no',
                        'apps.br_ref_app_approve_date',
                        'apps.trade_licence_num',
                        'apps.trade_licence_issuing_authority',
                        'apps.trade_licence_issue_date',
                        'apps.trade_licence_validity_period',
                        'apps.tin_number',
                        'apps.tin_issuing_authority',

                        'apps.bank_id',
                        'branch_id',
                        'apps.bank_account_number',
                        'apps.bank_account_title',

                        'apps.chnage_bank_info',
                        'apps.n_bank_id',
                        'apps.n_branch_id',
                        'apps.n_bank_account_number',
                        'apps.n_bank_account_title',
                        'apps.noc_letter',

                        'apps.assoc_membership_number',
                        'apps.assoc_chamber_name',
                        'apps.assoc_issuing_date',
                        'apps.assoc_expire_date',
                        'apps.fl_number',
                        'apps.fl_expire_date',
                        'apps.fl_issuing_authority',
                        'apps.inc_number',
                        'apps.inc_issuing_authority',
                        'apps.el_number',
                        'apps.el_expire_date',
                        'apps.el_issuing_authority',
                        'apps.project_status_id',
                        'apps.local_land_ivst',
                        'apps.local_building_ivst',
                        'apps.local_machinery_ivst',
                        'apps.local_others_ivst',
                        'apps.local_wc_ivst',
                        'apps.total_fixed_ivst_million',
                        'apps.total_fixed_ivst',
                        'apps.usd_exchange_rate',
                        'apps.total_fee',
                        'apps.local_male',
                        'apps.local_female',
                        'apps.local_total',
                        'apps.foreign_male',
                        'apps.foreign_female',
                        'apps.foreign_total',
                        'apps.manpower_total',
                        'apps.manpower_local_ratio',
                        'apps.manpower_foreign_ratio',
                        'apps.irc_purpose_id',
                        'apps.office_address',
                        'apps.office_post_office',
                        'apps.office_post_code',
                        'apps.factory_address',
                        'apps.factory_post_office',
                        'apps.factory_post_code',
                        'apps.other_sub_class_code',
                        'apps.other_sub_class_name',
                        'apps.ex_machine_local_value_bdt',
                        'apps.ex_machine_imported_value_bdt',
                        'apps.ins_apc_half_yearly_import_total',
                        'apps.ins_apc_half_yearly_import_total_in_word',
                        'apps.first_em_lc_total_five_percent',
                        'apps.first_em_lc_total_five_percent_in_word',
                        'apps.first_em_lc_total_taka_mil',
                        'apps.first_em_lc_total_percent',
                        'apps.chnage_bank_info',
                        'apps.n_first_em_lc_total_five_percent_in_word',
                        'apps.n_first_em_lc_total_five_percent',
                        'apps.first_em_lc_total_five_percent_in_word',
                        'apps.first_em_lc_total_five_percent',
                        'apps.n_ins_apc_half_yearly_import_total_in_word',
                        'apps.n_ins_apc_half_yearly_import_total',
                    ]);

                $appInfo->office_address = $appInfo->office_address . ', ' . $appInfo->office_post_office . ', ' . $appInfo->office_thana_name . ', ' . $appInfo->office_district_name . '- ' . $appInfo->office_post_code;
                $appInfo->factory_address = $appInfo->factory_address . ', ' . $appInfo->factory_post_office . ', ' . $appInfo->factory_thana_name . ', ' . $appInfo->factory_district_name . '- ' . $appInfo->factory_post_code;
                //Business Sector
                $query = DB::select("
                            Select 
                            sec_class.id, 
                            sec_class.code, 
                            sec_class.name, 
                            sec_group.id as group_id,
                            sec_group.code as group_code,
                            sec_group.name as group_name,
                            sec_division.id as division_id,
                            sec_division.code as division_code,
                            sec_division.name as division_name,
                            sec_section.id as section_id,
                            sec_section.code as section_code,
                            sec_section.name as section_name
                            from (select * from sector_info_bbs where type = 4) sec_class
                            left join sector_info_bbs sec_group on sec_class.pare_id = sec_group.id 
                            left join sector_info_bbs sec_division on sec_group.pare_id = sec_division.id 
                            left join sector_info_bbs sec_section on sec_division.pare_id = sec_section.id 
                            where sec_class.code = '$appInfo->class_code' limit 1;
                          ");

                $busness_code = json_decode(json_encode($query), true);

                if ($appInfo->sub_class_id == 0) {
                    $sub_class = ['name' => $appInfo->other_sub_class_code .' - '.$appInfo->other_sub_class_name];
                } else {
                    $sub_class = BusinessClass::select('id', DB::raw("CONCAT(code, ' - ', name) as name"))->where('id', $appInfo->sub_class_id)->first();
                }
                $appInfo->industrial_sector = $appInfo->class_code . ' - ' . $busness_code[0]['section_name'] . ', ' . $sub_class['name'];

                //section 5
                $appInfo->registering_authority_memo_no = ($appInfo->last_br == 'yes') ? $appInfo->br_ref_app_tracking_no : $appInfo->br_manually_approved_no;
                $appInfo->date_of_registration = empty($appInfo->br_ref_app_approve_date) ? '' : date('d-M-Y', strtotime($appInfo->br_ref_app_approve_date));

                //section 9
                $appInfo->em_local_total_taka_mil = $appInfo->ex_machine_local_value_bdt;
                $appInfo->em_lc_total_taka_mil = $appInfo->ex_machine_imported_value_bdt;

                //section 12
                $appInfo->apc_half_yearly_import_total = $appInfo->ins_apc_half_yearly_import_total;
                $appInfo->apc_half_yearly_import_total_in_word = $appInfo->ins_apc_half_yearly_import_total_in_word;

                //section 13
                if ($appInfo->irc_purpose_id != 1) {
                    $getInspectionId = IRCCommonPool::where('third_adhoc_tracking_no', $appInfo->irc_ref_app_tracking_no)->first(['third_adhoc_inspection_id']);
                    if (!empty($getInspectionId)) {
                        $ircSecondAdhocInfo = RegularIrcInspection::where('id', $getInspectionId->third_adhoc_inspection_id)->where('ins_approved_status', 1)
                            ->first(['em_lc_total_taka_mil', 'em_lc_total_percent', 'em_lc_total_five_percent', 'em_lc_total_five_percent_in_word']);
                        $appInfo->em_lc_total_taka_mil = !empty($ircSecondAdhocInfo->em_lc_total_taka_mil) ? $ircSecondAdhocInfo->em_lc_total_taka_mil : "";
                        $appInfo->em_lc_total_percent = !empty($ircSecondAdhocInfo->em_lc_total_percent) ? $ircSecondAdhocInfo->em_lc_total_percent : "";
                        $appInfo->em_lc_total_five_percent = !empty($ircSecondAdhocInfo->em_lc_total_five_percent) ? $ircSecondAdhocInfo->em_lc_total_five_percent : $appInfo->first_em_lc_total_five_percent;
                        $appInfo->em_lc_total_five_percent_in_word = !empty($ircSecondAdhocInfo->em_lc_total_five_percent_in_word) ? $ircSecondAdhocInfo->em_lc_total_five_percent_in_word : $appInfo->first_em_lc_total_five_percent_in_word;
                    }
                }
            }

            $annualProductionCapacity = RegularAnnualProductionCapacity::leftJoin('product_unit', 'product_unit.id', '=', 'irc_regular_annual_production_capacity.quantity_unit')
                ->where('irc_regular_annual_production_capacity.app_id', $appInfo->id)
                ->where('irc_regular_annual_production_capacity.status', 1)->where('irc_regular_annual_production_capacity.is_archive', 0)
                ->get(['product_unit.name as unit_name', 'irc_regular_annual_production_capacity.*']);
            // Raw Materials
            $ircSixMonthsImportRawMaterials = IrcSixMonthsImportRawMaterial::leftJoin('product_unit', 'product_unit.id', '=', 'irc_six_months_import_capacity_raw.quantity_unit')
                ->where('irc_six_months_import_capacity_raw.app_id', $appInfo->id)
                ->where('irc_six_months_import_capacity_raw.process_type_id', $process_type_id)
                ->where('irc_six_months_import_capacity_raw.status', 1)->where('irc_six_months_import_capacity_raw.is_archive', 0)
                ->get(['product_unit.name as unit_name', 'irc_six_months_import_capacity_raw.*']);
            // $ircSixMonthsImportRawMaterials = IrcSixMonthsImportRawMaterialAmendment::leftJoin('product_unit', 'product_unit.id', '=', 'irc_six_months_import_capacity_raw_amendment.n_quantity_unit')
            //     ->where('irc_six_months_import_capacity_raw_amendment.app_id', $appInfo->id)
            //     ->where('irc_six_months_import_capacity_raw_amendment.process_type_id', $process_type_id)
            //     ->where('irc_six_months_import_capacity_raw_amendment.status', 1)->where('irc_six_months_import_capacity_raw_amendment.is_archive', 0)
            //     ->get(['product_unit.name as unit_name', 'irc_six_months_import_capacity_raw_amendment.*']);


            $annualProductionSpareParts = RegularAnnualProductionSpareParts::leftJoin('product_unit', 'product_unit.id', '=', 'irc_regular_annual_production_spare_parts.quantity_unit')
                ->where('irc_regular_annual_production_spare_parts.app_id', $appInfo->id)
                ->where('irc_regular_annual_production_spare_parts.status', 1)->where('irc_regular_annual_production_spare_parts.is_archive', 0)
                ->get(['product_unit.name as unit_name', 'irc_regular_annual_production_spare_parts.*']);

            $projectStatusList = IrcProjectStatus::where('is_archive', 0)->where('inspection_status', 1)->lists('name', 'id');

            $productUnit = ['' => 'Select one'] + ProductUnit::where('status', 1)->where('is_archive', 0)->lists('name', 'id')->all();
            $currencyBDT = Currencies::orderBy('code')->whereIn('code', ['BDT'])->where('is_archive', 0)
                ->where('is_active', 1)->lists('code', 'id');
            $eaOrganizationStatus = ['' => 'Select one'] + EA_OrganizationStatus::where('is_archive', 0)
                    ->where('status', 1)->orderBy('name')->lists('name', 'id')->all();

            $banks = ['' => 'Select One'] + Bank::where('is_active', 1)->where('is_archive', 0)->orderBy(
                    'name',
                    'asc'
                )->lists('name', 'id')->all();

            $totalFee = DB::table('irc_inspection_gov_fee_range')->where('status', 1)->get();

            $public_html = strval(view("ProcessPath::{$form_id}", compact(
                'appInfo',
                'projectStatusList',
                'annualProductionCapacity',
                'annualProductionSpareParts',
                'eaOrganizationStatus',
                'currencyBDT',
                'productUnit',
                'banks',
                'ircSixMonthsImportRawMaterials',
                'totalFee'
            )));
        } elseif ($form_id == 'AddOnForm/irc_io_assign') {
            $public_html = strval(view("ProcessPath::{$form_id}"));
        } elseif ($form_id == 'AddOnForm/ircn_inspec_approved') {
            if ($process_type_id == 13) {
                $inspectionInfo = IrcInspection::where('app_id', $ref_id)
                    ->orderBy('id', 'desc')
                    ->get([
                        'id',
                        'io_name',
                        'inspection_report_date',
                        'created_at'
                    ]);

                $public_html = strval(view("ProcessPath::{$form_id}", compact('inspectionInfo')));
            } elseif ($process_type_id == 14) {
                $inspectionInfo = SecondIrcInspection::where('app_id', $ref_id)
                    ->orderBy('id', 'desc')
                    ->get([
                        'id',
                        'io_name',
                        'inspection_report_date',
                        'created_at'
                    ]);

                if (!empty($inspectionInfo)) {
                    $public_html = strval(view("ProcessPath::{$form_id}", compact('inspectionInfo')));
                }
            } elseif ($process_type_id == 15) {
                $inspectionInfo = ThirdIrcInspection::where('app_id', $ref_id)
                    ->orderBy('id', 'desc')
                    ->get([
                        'id',
                        'io_name',
                        'inspection_report_date'
                    ]);

                if (!empty($inspectionInfo)) {
                    $public_html = strval(view("ProcessPath::{$form_id}", compact('inspectionInfo')));
                }
            } elseif ($process_type_id == 16) {
                $inspectionInfo = RegularIrcInspection::where('app_id', $ref_id)
                    ->orderBy('id', 'desc')
                    ->get([
                        'id',
                        'io_name',
                        'inspection_report_date'
                    ]);

                if (!empty($inspectionInfo)) {
                    $public_html = strval(view("ProcessPath::{$form_id}", compact('inspectionInfo')));
                }
            } else {
                $public_html = '';
            }
        } elseif ($form_id == 'AddOnForm/br_shortfall_review') {
            $appInfo  = ProcessList::leftJoin('br_apps as apps', 'apps.id', '=', 'process_list.ref_id')
                ->where('process_list.process_type_id', $process_type_id)
                ->where('process_list.status_id', 8) //Observation
                ->where('process_list.ref_id', $ref_id)
                ->orderBy('apps.id', 'DESC')
                ->first([
                    'apps.company_info_review',
                    'apps.promoter_info_review',
                    'apps.office_address_review',
                    'apps.factory_address_review',
                    'apps.project_status_review',
                    'apps.production_capacity_review',
                    'apps.commercial_operation_review',
                    'apps.sales_info_review',
                    'apps.manpower_review',
                    'apps.investment_review',
                    'apps.source_finance_review',
                    'apps.utility_service_review',
                    'apps.trade_license_review',
                    'apps.tin_review',
                    'apps.machinery_equipment_review',
                    'apps.raw_materials_review',
                    'apps.ceo_info_review',
                    'apps.director_list_review',
                    'apps.imported_machinery_review',
                    'apps.local_machinery_review',
                    'apps.attachment_review',
                    'apps.declaration_review',
                ]);
            $public_html = strval(view("ProcessPath::{$form_id}", compact('appInfo')));
        } else {
            $public_html = '';
        }
        return $public_html;
    }

    public function processAddOnForm($process_list_id, $status_id)
    {
        $process = ProcessList::where('id', $process_list_id)->first();
        $toUrl = ProcessStatus::where('process_type_id', $process->process_type_id)->where('id', $status_id)->pluck('to_url');

        $url = '';
        switch ($process->process_type_id) {
            case 100: // Basic information application
                //                if (in_array($status_id, ['25']))  //Update desk AddOn form information with mail
                //                {
                //                    $url = $toUrl . '/' . Encryption::encodeId($process->ref_id) . '/' . Encryption::encodeId($process->process_type_id);
                //                }
                break;
            case 1: // Visa Recommendation application
                if (in_array($status_id, ['25'])) {
                    $url = $toUrl . '/' . Encryption::encodeId($process->ref_id) . '/' . Encryption::encodeId($process->process_type_id);
                }
                break;
            case 5: // Liaison Representative application
                if (in_array($status_id, ['25'])) {
                    $url = $toUrl . '/' . Encryption::encodeId($process->ref_id) . '/' . Encryption::encodeId($process->process_type_id);
                }
                break;
            case 2: // Work Permit application
                if (in_array($status_id, ['25'])) {
                    $url = $toUrl . '/' . Encryption::encodeId($process->ref_id) . '/' . Encryption::encodeId($process->process_type_id);
                }
                break;
            case 3: // Foreign Borrowing application
                if (in_array($status_id, ['25'])) {
                    $url = $toUrl . '/' . Encryption::encodeId($process->ref_id) . '/' . Encryption::encodeId($process->process_type_id);
                }
                break;
            default:
                break;
        }

        return $url;
    }


    /**
     * Check application validity for application process
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkApplicationValidity(Request $request)
    {
        $process_list_id = Encryption::decodeId($request->get('process_list_id'));
        /*
         * $existProcessInfo variable must should be same as $verificationData variable
         * of editViewForm() function, it's necessary for application verification
         */
        $existProcessInfo = ProcessList::where('id', $process_list_id)
            ->first([
                'id',
                //'ref_id',
                //'company_id',
                'process_type_id',
                //'department_id', // it's required for Basic Information application
                'status_id',
                'desk_id',
                //'user_id',
                //'updated_at',
                //'created_by',
                //'tracking_no',
                //'locked_by',
            ]);
        if ($request->get('data_verification') == Encryption::encode(UtilFunction::processVerifyData($existProcessInfo))) {
            return response()->json(array('responseCode' => 1));
        }
        return response()->json(array('responseCode' => 0));
    }


    /**
     * Load status list
     * @param $param
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxRequest($param, Request $request)
    {
        try {
            $data = ['responseCode' => 0];
            $application_id = Encryption::decodeId($request->get('application_id'));
            $cat_id = Encryption::decodeId($request->get('cat_id'));
            $process_list_id = Encryption::decodeId($request->get('process_list_id'));
            $appInfo = ProcessList::where('id', $process_list_id)->first(
                [
                    'process_type_id',
                    'id as process_list_id',
                    'status_id',
                    'ref_id',
                    'id',
                    'json_object',
                    'desk_id',
                    'updated_at',
                    'process_desc'
                ]
            );
            $statusFrom = $appInfo->status_id; // current process status
            $deskId = $appInfo->desk_id; // Current desk id
            $process_type_id = $appInfo->process_type_id; // Current desk id

            DB::beginTransaction();
            if ($param == 'load-status-list') {

                // if process type is Basic Information and application desk id is equal to 5 and current desk is 1 or 2 or 3
                // then status will load from desk 1
                if ($process_type_id == 100 && $deskId == 5 && (in_array(1, CommonFunction::getUserDeskIds()) or in_array(2, CommonFunction::getUserDeskIds()) or in_array(3, CommonFunction::getUserDeskIds()))) {
                    $deskId = 1;
                    $statusFrom = 10;
                }

                // Get extra SQL process this process path for Status loading,
                // if have any extra sql then , load status list from extra SQL
                // otherwise status list load from Static Query
                $getExtraSQL = ProcessPath::where(['status_from' => $statusFrom, 'desk_from' => $deskId, 'process_type_id' => $process_type_id, 'cat_id' => $cat_id])
                    ->first(['id', 'ext_sql']);

                // dd($getExtraSQL);
                if (!empty($getExtraSQL->ext_sql)) {
                    $extraSQL = $getExtraSQL->ext_sql;
                    $fullSql = str_replace("{app_id}", "$appInfo->ref_id", $extraSQL);
                } else {
                    $fullSql = "SELECT APS.id, APS.status_name
                        FROM process_status APS
                        WHERE find_in_set(APS.id,
                        (SELECT GROUP_CONCAT(status_to) FROM process_path APP
                        WHERE APP.status_from = '$statusFrom' 
                        AND APP.desk_from = '$deskId'  
                        AND APP.cat_id = '$cat_id'  
                        AND APP.process_type_id = '$process_type_id'))
                        AND APS.process_type_id = '$process_type_id'
                        order by APS.status_name";
                }
                $statusList = \DB::select(DB::raw($fullSql));

                $getProcessInfo = ProcessHistory::where('process_id', $appInfo->id)
                    ->where('status_id', 5)
                    ->count();

                if($getProcessInfo > 3) {
                    $statusList = array_filter($statusList, function ($status) {
                        return $status->id != 5;
                    });
                }

                $priority = '';
                //                $priority = DB::table('process_priority')->where('is_active', '=', 1)->lists('name', 'id');

                // Get suggested desk
                $suggested_status = $this->getSuggestedStatus($appInfo, $cat_id);

                $data = ['responseCode' => 1, 'data' => $statusList, 'priority' => $priority, 'suggested_status' => $suggested_status];
            }
            DB::commit();
            return response()->json($data);
        } catch (Exception $e) {
            DB::rollback();
            Log::error('PPCAjaxRequest: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [PPC-1006]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . '[PPC-1006]');
            return Redirect::back()->withInput();
        }
    }

    public function getSuggestedStatus($appInfo, $cat_id)
    {
        // Get suggested status by comment
        $suggested_status_by_comment = 0;
        $suggested_status_data = ProcessType::where('id', $appInfo->process_type_id)->first(['suggested_status_json']);

        if (!empty($suggested_status_data->suggested_status_json)) {
            $suggested_status_json = json_decode($suggested_status_data->suggested_status_json);
            if (!empty($suggested_status_json)) {
                foreach ($suggested_status_json as $json) {
                    $search_result = strpos($appInfo->process_desc, $json->comments);
                    if ($search_result !== false) {
                        $suggested_status_by_comment = $json->status;
                        break;
                    }
                }
            }
        }
        if (!empty($suggested_status_by_comment)) {
            return $suggested_status_by_comment;
        }

        // Get suggested status by process path
        $suggested_status_data = ProcessPath::where([
            'process_type_id' => $appInfo->process_type_id,
            'cat_id' => $cat_id,
            'desk_from' => $appInfo->desk_id,
            'status_from' => $appInfo->status_id,
        ])->where('suggested_status', '!=', 0)->first(['suggested_status']);

        return empty($suggested_status_data->suggested_status) ? 0 : $suggested_status_data->suggested_status;
    }

    public function verifyProcessHistory($type_id, $process_list_id)
    {
        try {
            $type_id = Encryption::decodeId($type_id);
            $process_list_id = Encryption::decodeId($process_list_id);

            $process_history = DB::select(DB::raw("select  `process_list_hist`.`status_id`,`as`.`status_name`,          
                                if(`process_list_hist`.`desk_id`=0,\"-\",`ud`.`desk_name`) `deskname`,
                                `users`.`user_full_name`, 
                                `process_list_hist`.`process_id`,
                                `process_list_hist`.`ref_id`,
                                `process_list_hist`.`process_type`,
                                `process_list_hist`.`tracking_no`,
                                `process_list_hist`.`closed_by`,
                                `process_list_hist`.`locked_by`,
                                `process_list_hist`.`locked_at`,
                                `process_list_hist`.`desk_id`,
                                `process_list_hist`.`status_id`,
                                `process_list_hist`.`process_desc`,
                                `process_list_hist`.`created_by`,
                                `process_list_hist`.`on_behalf_of_user`,
                                `process_list_hist`.`updated_by`,
                                `process_list_hist`.`status_id`,
                                `process_list_hist`.`process_desc`,
                                `process_list_hist`.`process_id`,
                                `process_list_hist`.`updated_at`,
                                `process_list_hist`.`hash_value`,                                                                
                                 group_concat(`pd`.`file`) as files
                                from `process_list_hist`
                                left join `process_documents` as `pd` on `process_list_hist`.`id` = `pd`.`process_hist_id`
                                left join `user_desk` as `ud` on `process_list_hist`.`desk_id` = `ud`.`id`
                                left join `users` on `process_list_hist`.`updated_by` = `users`.`id`     
                                
                                left join `process_status` as `as` on `process_list_hist`.`status_id` = `as`.`id`
                                and `process_list_hist`.`process_type` = `as`.`process_type_id`
                                where `process_list_hist`.`process_id`  = '$process_list_id'
                                and `process_list_hist`.`process_type` = '$type_id' 
                                and `process_list_hist`.`hash_value` !='' 
                                and `process_list_hist`.`status_id` != -1
                    group by `process_list_hist`.`process_id`,`process_list_hist`.`desk_id`, `process_list_hist`.`status_id`, process_list_hist.updated_at
                    order by process_list_hist.updated_at desc
                    limit 20
                    "));

            $html = "";
            if (count($process_history) > 1) {

                $keyPair = KeyPair::generateKeyPair(2048);
                $secretKey = $keyPair->getPrivateKey();
                $html .= "<div class=\"table-responsive\"><table border='1px solid' class='table table-striped table-bordered dt-responsive nowrap dataTable no-footer dtr-inline' style='text-align: center; width: 100%;'>";
                $html .= "<th id='1' style='text-align: center'>Process ID</th><th style='text-align: center'>On Desk</th><th style='text-align: center'>Updated By</th><th style='text-align: center'>Status</th><th style='text-align: center'>Process Time</th><th style='text-align: center'>Verification Status</th>";

                foreach ($process_history as $data) {

                    $resultData = $data->process_id . ', ' . $data->ref_id . ', ' . $data->tracking_no . ', ' . $data->desk_id . ', ' . $data->process_type . ',' . $data->status_id . ', '
                        . $data->on_behalf_of_user . ', ' . $data->process_desc . ', ' . $data->closed_by . ', ' . $data->locked_at . ', ' . $data->locked_by . ',' . $data->updated_by;
                    $plaintext = EasyRSA::decrypt($data->hash_value, $secretKey);
                    $time = date('d-m-Y h:i A', strtotime($data->updated_at));
                    $html .= "<tr><td style='text-align: center; padding: 10px'> $data->process_id </td><td  style='padding: 10px'> $data->deskname </td><td  style='padding: 10px'> $data->user_full_name </td><td  style='padding: 10px'> $data->status_name </td><td  style='padding: 10px'>$time</td>";
                    if ($resultData == $plaintext) {
                        $verification = "<font color='green'><h4><i class=\"fa fa-check-square\"></i> </h4></font>";
                    } else {
                        $verification = "<font color='red'><h4><i class=\"fa fa-ban\"></i> </h4></font>";
                    }
                    $html .= "<td style='text-align: center'> $verification </td> </tr>";
                }
                $html .= "</table></div>";
            } else {
                $html .= "<div style='text-align: center;'><h3>No result found!</h3></div>";
            }
            return view("ProcessPath::history-verification")->with("html", $html);
        } catch (Exception $e) {
            Log::error('PPCProcessHistory: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [PPC-1007]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . '[PPC-1007]');
            return Redirect::back()->withInput();
        }
    }

    public function entryForm($method = '', $process_type_id)
    {
        try {
            $mode = '-A-';
            $viewMode = 'off';
            $openMode = 'add';
            $process_id = Encryption::decodeId($process_type_id);
            $process_info = ProcessType::where('id', $process_id)->first();
            $form_id = json_decode($process_info->form_id, true);
            $url = (isset($form_id[$openMode]) ? $form_id[$openMode] : '');
            return view(
                "ProcessPath::form",
                compact('process_info', 'method', 'mode', 'viewMode', 'openMode', 'url')
            );
        } catch (\Exception $e) {
            Log::error('PPCEntryForm: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [PPC-1008]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [PPC-1008]');
            return Redirect::back()->withInput();
        }
    }

    public function entryFormSubModule($method = '', $submodule, $process_type_id)
    {
        try {
            $mode = '-A-';
            $viewMode = 'off';
            $openMode = 'add';
            $process_id = Encryption::decodeId($process_type_id);
            $process_info = ProcessType::where('id', $process_id)->first();
            $form_id = json_decode($process_info->form_id, true);
            $url = (isset($form_id[$openMode]) ? $form_id[$openMode] : '');
            $serviceDetailConfig = json_decode($process_info->external_service_config);
            $serviceKey = !empty($serviceDetailConfig->service_key)?$serviceDetailConfig->service_key:'';
            return view(
                "ProcessPath::form",
                compact('process_info', 'method', 'mode', 'viewMode', 'openMode', 'url','serviceKey')
            );
        } catch (\Exception $e) {
            Log::error('PPCEntryForm: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [PPC-1008]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [PPC-1008]');
            return Redirect::back()->withInput();
        }
    }

    public function editViewForm($module_url = '', $app_id, $process_type_id)
    {
        $process_type_idd = $process_type_id != '' ? Encryption::decodeId($process_type_id) : '';
        $allowProcessType = config('stackholder.allow_for_view_edit_route');
        if (!in_array($process_type_idd, $allowProcessType)) {
            Session::flash('error', 'Invalid URL ! This incident will be reported.');
            return redirect('dashboard');
        }

        $process_info = ProcessType::where('id', $process_type_idd)->first();

        $app_idd = $app_id != '' ? Encryption::decodeId($app_id) : '';
        $user_type = CommonFunction::getUserType();

        $appInfo = ProcessList::leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
            ->where([
                'ref_id' => $app_idd,
                'process_type_id' => $process_type_idd,
            ])->first([
                'process_list.id as process_list_id',
                'process_list.desk_id',
                'process_list.approval_center_id',
                'process_list.department_id',
                'process_list.sub_department_id',
                'process_list.process_type_id',
                'process_list.status_id',
                //'process_list.locked_by',
                //'process_list.locked_at',
                'process_list.ref_id',
                'process_list.tracking_no',
                'process_list.company_id',
                'process_list.process_desc',
                'process_list.priority',
                'process_list.user_id',
                //'process_list.read_status',
                'process_list.created_by',
                'process_list.updated_at',
                'process_type.auto_process',
                'process_type.final_status',
                'process_type.max_processing_day',
                'process_list.submitted_at',
            ]);
        if (empty($appInfo)) {
            Session::flash('error', 'Invalid application [PPC-1096]');
            return \redirect()->back();
        }

        $cat_id = $this->getCatId($appInfo);

        // ViewMode, EditMode permission setting
        $viewMode = 'on';
        $openMode = 'view';
        $mode = '-V-';
        $companyIds = CommonFunction::getUserCompanyWithZero();
        if (in_array($user_type, ['5x505'])) {
            if (in_array($appInfo->company_id, $companyIds) && in_array($appInfo->status_id, [-1, 5, 22])) {
                $mode = '-E-';
                $viewMode = 'off';
                $openMode = 'edit';
            }
        }
        $form_id = json_decode($process_info->form_id, true);
        $url = (isset($form_id[$openMode]) ? $form_id[$openMode] : '');

        $hasDeskDepartmentWisePermission = CommonFunction::hasDeskDepartmentWisePermission($appInfo->desk_id, $appInfo->approval_center_id, $appInfo->department_id, $appInfo->sub_department_id, $appInfo->process_type_id, $appInfo->user_id, $user_type);

        // Read or Unread status update
//        if (($hasDeskDepartmentWisePermission && $appInfo->read_status == 0) // for all user
//            || ($appInfo->created_by == Auth::user()->id && in_array($appInfo->status_id, [5, 6, 25]) && $appInfo->read_status == 0) // for applicant user
//            || ($appInfo->desk_id == 5 && in_array(5, \App\Libraries\CommonFunction::getUserDeskIds()) && $appInfo->department_id == 0 && $appInfo->process_type_id == 100 && $appInfo->read_status == 0) // for help desk user and basic information module
//        ) {
//            DB::table('process_list')->where('ref_id', $app_idd)->where('process_type_id', $appInfo->process_type_id)->update(['read_status' => 1]);
//        }

        // application remaining day
        //        if($hasDeskDepartmentWisePermission){
        if ($appInfo->auto_process == 1 && $user_type != '5x505') {
            $holiday = DB::select(DB::raw('select group_concat(holiday_date) as holiday_date from govt_holiday where is_active =1'));;
            $holidays = explode(',', $holiday[0]->holiday_date);
            $remainingDay = CommonFunction::getRemainingDay($appInfo->submitted_at, $holidays, $appInfo->max_processing_day);
        } else {
            $remainingDay = 'N/A';
        }
        //    }

        // BI application type for company information
        $BiRoute = 'basic-information/form-bida/' . Encryption::encodeId('NUBS') . '/' . Encryption::encodeId($appInfo->company_id);
        if (in_array($user_type, ['5x505', '4x404', '3x303', '1x101', '2x202', '13x303'])) { //process related code run  only this users
            // get corresponding basic information application ID
            $basicAppID = ProcessList::leftjoin('ea_apps', 'ea_apps.id', '=', 'process_list.ref_id')
                ->where('process_list.process_type_id', 100)
                ->where('process_list.status_id', 25)
                ->where('process_list.company_id', $appInfo->company_id)
                ->first(['process_list.ref_id', 'process_list.process_type_id', 'process_list.department_id', 'ea_apps.*']);
            if ($basicAppID->applicant_type == 'New Company Registration') {
                $BiRoute = 'basic-information/form-stakeholder/' . Encryption::encodeId('NCR') . '/' . Encryption::encodeId($appInfo->company_id);
            } elseif ($basicAppID->applicant_type == 'Existing Company Registration') {
                $BiRoute = 'basic-information/form-stakeholder/' . Encryption::encodeId('ECR') . '/' . Encryption::encodeId($appInfo->company_id);
            } else {
                $BiRoute = 'basic-information/form-bida/' . Encryption::encodeId('EUBS') . '/' . Encryption::encodeId($appInfo->company_id);
            }

            // Lock application by current user
//            if (in_array($appInfo->status_id, [1, 2, 8, 9, 16, 21, 30]) && $hasDeskDepartmentWisePermission) {
//                DB::table('process_list')->where('ref_id', $app_idd)->where('process_type_id', $appInfo->process_type_id)->update([
//                    'locked_by' => Auth::user()->id,
//                    'locked_at' => date('Y-m-d H:i:s')
//                ]);
//            }

            /*
            * $verificationData variable must should be same as $existProcessInfo variable
            * of checkApplicationValidity() function, it's necessary for application verification
            */
            $verificationData = ProcessList::where('process_list.ref_id', $app_idd)
                ->where('process_list.process_type_id', $process_type_idd)
                ->first([
                    'id',
                    'ref_id',
                    'company_id',
                    'process_type_id',
                    'department_id', // it's required for Basic Information application
                    'status_id',
                    'desk_id',
                    'user_id',
                    'updated_at',
                    'created_by',
                    'tracking_no',
                    //'locked_by',
                ]);

            $process_history = DB::select(DB::raw("select  `process_list_hist`.`desk_id`,`as`.`status_name`,
                                `process_list_hist`.`process_id`,                           
                                if(`process_list_hist`.`desk_id`=0,\"Applicant\",`ud`.`desk_name`) `deskname`,
                                `users`.`user_first_name`, 
                                `users`.`user_middle_name`, 
                                `users`.`user_last_name`,
                                `process_list_hist`.`on_behalf_of_user`,
                                `process_list_hist`.`updated_by`, 
                                `process_list_hist`.`status_id`, 
                                `process_list_hist`.`process_desc`, 
                                `process_list_hist`.`process_id`, 
                                `process_list_hist`.`updated_at`,
                                 group_concat(`pd`.`file`) as files
                                
                    
                                from `process_list_hist`
                                left join `process_documents` as `pd` on `process_list_hist`.`id` = `pd`.`process_hist_id`
                                left join `user_desk` as `ud` on `process_list_hist`.`desk_id` = `ud`.`id`
                                left join `users` on `process_list_hist`.`updated_by` = `users`.`id`     
                                
                                left join `process_status` as `as` on `process_list_hist`.`status_id` = `as`.`id`
                                and `process_list_hist`.`process_type` = `as`.`process_type_id`
                                where `process_list_hist`.`process_id`  = '$appInfo->process_list_id'
                                and `process_list_hist`.`process_type` = '$appInfo->process_type_id' 
                               
                                and `process_list_hist`.`status_id` != -1
                    /*group by `process_list_hist`.`process_id`,`process_list_hist`.`desk_id`, `process_list_hist`.`status_id`, process_list_hist.updated_at
                    order by process_list_hist.updated_at desc*/
                    group by 
                    /* `process_list_hist`.`process_id`,`process_list_hist`.`desk_id`, */
                   process_list_hist.updated_at
                    order by process_list_hist.id desc

                    "));

            $getShadowFile = ShadowFile::where('user_id', CommonFunction::getUserId())
                ->where('ref_id', $app_idd)
                ->where('process_type_id', $process_type_idd)
                ->orderBy('id', 'DESC')
                ->get();
        }

        // Basic salary for WPN, WPE services & Commercial department
        if (in_array($process_type_idd, [2, 3]) && $appInfo->department_id == 1) {
            $get_basic_salary = DB::table($process_info->table_name)->where('id', $app_idd)->first(['basic_salary']);
            $appInfo->basic_salary = $get_basic_salary->basic_salary;
        }

        return view("ProcessPath::form", compact(
            'url',
            'mode',
            'app_id',
            'process_info',
            'viewMode',
            'appInfo',
            'verificationData',
            'remainingDay',
            'hasDeskDepartmentWisePermission',
            'basicAppID',
            'BiRoute',
            'process_type_id',
            'openMode',
            'process_history',
            'getShadowFile',
            'cat_id'
        ));
    }

    public function applicationEdit($module_url = '', $app_id, $process_type_id)
    {

        $process_type_idd = $process_type_id != '' ? Encryption::decodeId($process_type_id) : '';
        $process_info = ProcessType::where('id', $process_type_idd)->first();
        $app_idd = $app_id != '' ? Encryption::decodeId($app_id) : '';

        $appInfo = ProcessList::leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
            ->where([
                'ref_id' => $app_idd,
                'process_type_id' => $process_type_idd,
            ])->first([
                //                'process_list.id as process_list_id',
                //                'process_list.desk_id',
                //                'process_list.approval_center_id',
                //                'process_list.department_id',
                //                'process_list.sub_department_id',
                //                'process_list.process_type_id',
                //                'process_list.status_id',
                //                'process_list.locked_by',
                //                'process_list.locked_at',
                //                'process_list.ref_id',
                //                'process_list.tracking_no',
                //                'process_list.company_id',
                //                'process_list.process_desc',
                //                'process_list.priority',
                //                'process_list.user_id',
                //                'process_list.read_status',
                'process_type.form_id',
                //                'process_list.created_by',
                //                'process_list.updated_at',
                //                'process_list.submitted_at',
            ]);
        if (empty($appInfo)) {
            Session::flash('error', 'Invalid application [PPC-1098]');
            return \redirect()->back();
        }

        $viewMode = 'off';
        $openMode = 'edit';
        $mode = '-E-';
        $form_id = json_decode($appInfo->form_id, true);
        $url = $form_id['edit'];

        return view("ProcessPath::form-edit", compact(
            'process_info',
            'mode',
            'url',
            'app_id',
            'viewMode',
            'appInfo',
            'process_type_id',
            'openMode'
        ));
    }

    public function applicationView($module_url = '', $app_id, $process_type_id)
    {
        $process_type_idd = $process_type_id != '' ? Encryption::decodeId($process_type_id) : '';
        $user_type = CommonFunction::getUserType();
        $mode = '-V-';
        $viewMode = 'on';
        $app_idd = $app_id != '' ? Encryption::decodeId($app_id) : '';
        $appInfo = ProcessList::leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
            ->where([
                'ref_id' => $app_idd,
                'process_type_id' => $process_type_idd,
            ])->first([
                'process_list.id as process_list_id',
                'process_list.desk_id',
                'process_list.approval_center_id',
                'process_list.department_id',
                'process_list.sub_department_id',
                'process_list.process_type_id',
                'process_list.status_id',
                //'process_list.locked_by',
                //'process_list.locked_at',
                'process_list.ref_id',
                'process_list.tracking_no',
                'process_list.company_id',
                'process_list.process_desc',
                'process_list.priority',
                'process_list.user_id',
                //'process_list.read_status',
                'process_list.created_by',
                'process_list.updated_at',
                'process_type.auto_process',
                'process_type.final_status',
                'process_type.max_processing_day',
                'process_type.acl_name',
                'process_type.form_id',
                'process_type.table_name',
                'process_list.submitted_at',
            ]);

        if (empty($appInfo)) {
            Session::flash('error', 'Invalid application [PPC-1097]');
            return \redirect()->back();
        }
        $cat_id = $this->getCatId($appInfo);
        $form_id = json_decode($appInfo->form_id, true);
        $url = $form_id['view'];

        //        $hasDeskDepartmentWisePermission = CommonFunction::hasDeskDepartmentWisePermission($appInfo->desk_id, $appInfo->department_id, $appInfo->sub_department_id, $appInfo->process_type_id, $appInfo->user_id, $user_type);
        $hasDeskDepartmentWisePermission = CommonFunction::hasDeskDepartmentWisePermission($appInfo->desk_id, $appInfo->approval_center_id, $appInfo->department_id, $appInfo->sub_department_id, $appInfo->process_type_id, $appInfo->user_id, $user_type);
        //        dd($hasDeskDepartmentWisePermission);

        // Read or Unread status update
//        if (($hasDeskDepartmentWisePermission && $appInfo->read_status == 0) // for all user
//            || ($appInfo->created_by == Auth::user()->id && in_array($appInfo->status_id, [5, 6, 25]) && $appInfo->read_status == 0) // for applicant user
//            || ($appInfo->desk_id == 5 && in_array(5, \App\Libraries\CommonFunction::getUserDeskIds()) && $appInfo->department_id == 0 && $appInfo->process_type_id == 100 && $appInfo->read_status == 0) // for help desk user and basic information module
//        ) {
//            DB::table('process_list')->where('ref_id', $app_idd)->where('process_type_id', $appInfo->process_type_id)->update(['read_status' => 1]);
//        }

        // application remaining day
        if ($appInfo->auto_process == 1 && $user_type != '5x505') {
            $holiday = DB::select(DB::raw('select group_concat(holiday_date) as holiday_date from govt_holiday where is_active =1'));;
            $holidays = explode(',', $holiday[0]->holiday_date);
            $remainingDay = CommonFunction::getRemainingDay($appInfo->submitted_at, $holidays, $appInfo->max_processing_day);
        } else {
            $remainingDay = 'N/A';
        }

        // BI application type for company information
        $BiRoute = 'basic-information/form-bida/' . Encryption::encodeId('NUBS') . '/' . Encryption::encodeId($appInfo->company_id);
        if (in_array($user_type, ['6x606', '5x505', '4x404', '3x303', '1x101', '2x202', '13x303'])) { //process related code run  only this users
            // get corresponding basic information application ID
            $basicAppID = ProcessList::leftjoin('ea_apps', 'ea_apps.id', '=', 'process_list.ref_id')
                ->where('process_list.process_type_id', 100)
                ->where('process_list.status_id', 25)
                ->where('process_list.company_id', $appInfo->company_id)
                ->first(['process_list.ref_id', 'process_list.process_type_id', 'process_list.department_id', 'ea_apps.*']);
            if ($basicAppID->applicant_type == 'New Company Registration') {
                $BiRoute = 'basic-information/form-stakeholder/' . Encryption::encodeId('NCR') . '/' . Encryption::encodeId($appInfo->company_id);
            } elseif ($basicAppID->applicant_type == 'Existing Company Registration') {
                $BiRoute = 'basic-information/form-stakeholder/' . Encryption::encodeId('ECR') . '/' . Encryption::encodeId($appInfo->company_id);
            } else {
                $BiRoute = 'basic-information/form-bida/' . Encryption::encodeId('EUBS') . '/' . Encryption::encodeId($appInfo->company_id);
            }

            // Lock application by current user
//            if (in_array($appInfo->status_id, [1, 2, 8, 9, 16, 21, 30]) && $hasDeskDepartmentWisePermission) {
//                DB::table('process_list')->where('ref_id', $app_idd)->where('process_type_id', $appInfo->process_type_id)->update([
//                    'locked_by' => Auth::user()->id,
//                    'locked_at' => date('Y-m-d H:i:s')
//                ]);
//            }

            /*
            * $verificationData variable must should be same as $existProcessInfo variable
            * of checkApplicationValidity() function, it's necessary for application verification
            */
            $verificationData['id'] = $appInfo->process_list_id;
            $verificationData['ref_id'] = $appInfo->ref_id;
            $verificationData['company_id'] = $appInfo->company_id;
            $verificationData['process_type_id'] = $appInfo->process_type_id;
            $verificationData['department_id'] = $appInfo->department_id;
            $verificationData['status_id'] = $appInfo->status_id;
            $verificationData['desk_id'] = $appInfo->desk_id;
            $verificationData['user_id'] = $appInfo->user_id;
            $verificationData['updated_at'] = $appInfo->updated_at->format('yy-m-d H:i:s');
            $verificationData['created_by'] = $appInfo->created_by;
            $verificationData['tracking_no'] = $appInfo->tracking_no;
            $verificationData = (object) $verificationData;
        }

        // Basic salary for WPN, WPE, WPA services & Commercial department
        if (in_array($process_type_idd, [2, 3]) && $appInfo->department_id == 1) {
            $get_basic_salary = DB::table($appInfo->table_name)->where('id', $app_idd)->first(['basic_salary']);
            $appInfo->basic_salary = $get_basic_salary->basic_salary;
        }

        $hasAccessProcessHistory = true;
        if($user_type == '4x404') {
            $userDepartmentIds = CommonFunction::getUserDepartmentIds();
            $userSubDepartmentIds = CommonFunction::getUserSubDepartmentIds();
    
            $appInfoDepartmentId = $appInfo->department_id;
            $appInfoSubDepartmentId = $appInfo->sub_department_id;
    
            // $hasAccessProcessHistory = in_array($appInfoDepartmentId, $userDepartmentIds) && in_array($appInfoSubDepartmentId, $userSubDepartmentIds);

            // If either condition is not met, set $hasAccessProcessHistory to false
            if (!in_array($appInfo->process_type_id,[13,14,15,16]) && (!in_array($appInfoDepartmentId, $userDepartmentIds) || !in_array($appInfoSubDepartmentId, $userSubDepartmentIds))) {
                $hasAccessProcessHistory = false;
            }
        }

        return view("ProcessPath::form-view", compact(
            'mode',
            'viewMode',
            'appInfo',
            'cat_id',
            'remainingDay',
            'url',
            'app_id',
            'hasDeskDepartmentWisePermission',
            'verificationData',
            'BiRoute',
            'basicAppID',
            'hasAccessProcessHistory'
        ));
    }

    public function editViewFormSubModule($module_url = '', $subModule, $app_id, $process_type_id)
    {
        $process_type_idd = $process_type_id != '' ? Encryption::decodeId($process_type_id) : '';
        $process_info = ProcessType::where('id', $process_type_idd)->first();
        $app_idd = $app_id != '' ? Encryption::decodeId($app_id) : '';
        $user_type = CommonFunction::getUserType();

        $appInfo = ProcessList::leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
            ->where([
                'ref_id' => $app_idd,
                'process_type_id' => $process_type_idd,
            ])->first([
                'process_list.id as process_list_id',
                'process_list.desk_id',
                'process_list.department_id',
                'process_list.sub_department_id',
                'process_list.process_type_id',
                'process_list.status_id',
                //'process_list.locked_by',
                //'process_list.locked_at',
                'process_list.ref_id',
                'process_list.tracking_no',
                'process_list.company_id',
                'process_list.process_desc',
                'process_list.priority',
                'process_list.user_id',
                //'process_list.read_status',
                'process_list.created_by',
                'process_list.updated_at',
                'process_type.auto_process',
                'process_type.max_processing_day',
                'process_list.submitted_at',
            ]);

        if (empty($appInfo)) {
            Session::flash('error', 'Invalid application [PPC-1098]');
            return \redirect()->back();
        }

        $cat_id = $this->getCatId($appInfo);

        // ViewMode, EditMode permission setting
        $viewMode = 'on';
        $openMode = 'view';
        $mode = '-V-';
        if (in_array($user_type, ['5x505'])) {
            $companyIds = CommonFunction::getUserCompanyWithZero();
            if (in_array($appInfo->company_id, $companyIds) && in_array($appInfo->status_id, [-1, 5, 22])) {
                $mode = '-E-';
                $viewMode = 'off';
                $openMode = 'edit';
            }
        }

        $form_id = json_decode($process_info->form_id, true);
        $url = (isset($form_id[$openMode]) ? $form_id[$openMode] : '');
        $serviceDetailConfig = json_decode($process_info->external_service_config);
        $serviceKey = !empty($serviceDetailConfig->service_key)?$serviceDetailConfig->service_key:'';

        $hasDeskDepartmentWisePermission = CommonFunction::hasDeskDepartmentWisePermission($appInfo->desk_id, null, $appInfo->department_id, $appInfo->sub_department_id, $appInfo->process_type_id, $appInfo->user_id, $user_type);
        // Read or Unread status update
//        if (($hasDeskDepartmentWisePermission && $appInfo->read_status == 0) // for all user
//            || ($appInfo->created_by == Auth::user()->id && in_array($appInfo->status_id, [5, 6, 25]) && $appInfo->read_status == 0) // for applicant user
//            || ($appInfo->desk_id == 5 && in_array(5, \App\Libraries\CommonFunction::getUserDeskIds()) && $appInfo->department_id == 0 && $appInfo->process_type_id == 100 && $appInfo->read_status == 0) // for help desk user and basic information module
//        ) {
//            DB::table('process_list')->where('ref_id', $app_idd)->where('process_type_id', $appInfo->process_type_id)->update(['read_status' => 1]);
//        }
        // application remaining day
        //        if($hasDeskDepartmentWisePermission){
        if ($appInfo->auto_process == 1 && $user_type != '5x505') {
            $holiday = DB::select(DB::raw('select group_concat(holiday_date) as holiday_date from govt_holiday where is_active =1'));;
            $holidays = explode(',', $holiday[0]->holiday_date);
            $remainingDay = CommonFunction::getRemainingDay($appInfo->submitted_at, $holidays, $appInfo->max_processing_day);
        } else {
            $remainingDay = 'N/A';
        }
        //    }

        if (in_array($user_type, ['5x505', '4x404', '3x303', '1x101', '2x202', '13x303', '9x901', '9x902', '9x903', '9x904'])) { //process related code run  only this users
            // get corresponding basic information application ID
            $basicAppID = ProcessList::leftjoin('ea_apps', 'ea_apps.id', '=', 'process_list.ref_id')
                ->where('process_list.process_type_id', 100)
                ->where('process_list.status_id', 25)
                ->where('process_list.company_id', $appInfo->company_id)
                ->first(['process_list.ref_id', 'process_list.process_type_id']);

            // Lock application by current user
            //$userDeskIds = CommonFunction::getUserDeskIds();
//            if (in_array($appInfo->status_id, [1, 2, 3, 4, 5, 6]) && in_array($appInfo->desk_id, $userDeskIds) && ($appInfo->user_id == CommonFunction::getUserId() || $appInfo->desk_id == 1)) {
//                DB::table('process_list')->where('ref_id', $app_idd)->where('process_type_id', $appInfo->process_type_id)->update([
//                    'locked_by' => Auth::user()->id,
//                    'locked_at' => date('Y-m-d H:i:s')
//                ]);
//            }

            /*
            * $verificationData variable must should be same as $existProcessInfo variable
            * of checkApplicationValidity() function, it's necessary for application verification
            */
            $verificationData = ProcessList::where('process_list.ref_id', $app_idd)
                ->where('process_list.process_type_id', $process_type_idd)
                ->first([
                    'id',
                    'ref_id',
                    'company_id',
                    'process_type_id',
                    'department_id', // it's required for Basic Information application
                    'status_id',
                    'desk_id',
                    'user_id',
                    'updated_at',
                    'created_by',
                    'tracking_no',
                    //'locked_by',
                ]);
            $process_history = DB::select(DB::raw("select  `process_list_hist`.`desk_id`,`as`.`status_name`,
                                `process_list_hist`.`process_id`,                           
                                if(`process_list_hist`.`desk_id`=0,\"Applicant\",`ud`.`desk_name`) `deskname`,
                                `users`.`user_first_name`, 
                                `users`.`user_middle_name`, 
                                `users`.`user_last_name`,
                                `process_list_hist`.`updated_by`, 
                                `process_list_hist`.`status_id`, 
                                `process_list_hist`.`process_desc`, 
                                `process_list_hist`.`process_id`, 
                                `process_list_hist`.`updated_at`,
                                 group_concat(`pd`.`file`) as files
                                
                    
                                from `process_list_hist`
                                left join `process_documents` as `pd` on `process_list_hist`.`id` = `pd`.`process_hist_id`
                                left join `user_desk` as `ud` on `process_list_hist`.`desk_id` = `ud`.`id`
                                left join `users` on `process_list_hist`.`updated_by` = `users`.`id`     
                                
                                left join `process_status` as `as` on `process_list_hist`.`status_id` = `as`.`id`
                                and `process_list_hist`.`process_type` = `as`.`process_type_id`
                                where `process_list_hist`.`process_id`  = '$appInfo->process_list_id'
                                and `process_list_hist`.`process_type` = '$appInfo->process_type_id' 
                               
                                and `process_list_hist`.`status_id` != -1
                    /*group by `process_list_hist`.`process_id`,`process_list_hist`.`desk_id`, `process_list_hist`.`status_id`, process_list_hist.updated_at
                    order by process_list_hist.updated_at desc*/
                    group by 
                    /* `process_list_hist`.`process_id`,`process_list_hist`.`desk_id`, */
                   process_list_hist.updated_at
                    order by process_list_hist.updated_at desc

                    "));

            $getShadowFile = ShadowFile::where('user_id', CommonFunction::getUserId())
                ->where('ref_id', $app_idd)
                ->where('process_type_id', $process_type_idd)
                ->orderBy('id', 'DESC')
                ->get();
        }


        $BiRoute = 'basic-information/form-bida/' . Encryption::encodeId('NUBS') . '/' . Encryption::encodeId($appInfo->company_id);

        if (in_array($user_type, ['5x505', '4x404', '3x303', '1x101', '13x303'])) { //process related code run  only this users
            // get corresponding basic information application ID
            $basicAppID = ProcessList::leftjoin('ea_apps', 'ea_apps.id', '=', 'process_list.ref_id')
                ->where('process_list.process_type_id', 100)
                ->where('process_list.status_id', 25)
                ->where('process_list.company_id', $appInfo->company_id)
                ->first(['process_list.ref_id', 'process_list.process_type_id', 'process_list.department_id', 'ea_apps.*']);
            if ($basicAppID->applicant_type == 'New Company Registration') {
                $BiRoute = 'basic-information/form-stakeholder/' . Encryption::encodeId('NCR') . '/' . Encryption::encodeId($appInfo->company_id);
            } elseif ($basicAppID->applicant_type == 'Existing Company Registration') {
                $BiRoute = 'basic-information/form-stakeholder/' . Encryption::encodeId('ECR') . '/' . Encryption::encodeId($appInfo->company_id);
            } else {
                $BiRoute = 'basic-information/form-bida/' . Encryption::encodeId('EUBS') . '/' . Encryption::encodeId($appInfo->company_id);
            }
        }
        return view("ProcessPath::form", compact(
            'url',
            'mode',
            'app_id',
            'process_info',
            'viewMode',
            'appInfo',
            'verificationData',
            'remainingDay',
            'hasDeskDepartmentWisePermission',
            'basicAppID',
            'BiRoute',
            'process_type_id',
            'openMode',
            'process_history',
            'getDesk',
            'getShadowFile',
            'cat_id',
            'serviceKey'
        ));
    }

    public function getCatId($appInfo)
    {
        $cat_id = 1;
        $data = DB::table('process_path_cat_mapping')->where('process_type_id', $appInfo->process_type_id)->where('department_id', $appInfo->department_id)->first();
        if ($data) {
            $cat_id = $data->cat_id;
        }

        //  previous code

        //        $cat_id = 1;
        //        switch ($appInfo->process_type_id) {
        //            case 2: // Work Permit New
        //            case 3: // Work Permit Extension
        //            case 4: // Work Permit Amendment
        //            case 5: // Work Permit Cancellation
        //                if ($appInfo->department_id == 2) {
        //                    $cat_id = 2;
        //                }
        //                break;
        //            default:
        //                $cat_id = 1;
        //                break;
        //        }

        return $cat_id;
    }

    public function getForm(request $request)
    {
        $app_idd = $request->get('app_id');
        $openMode = $request->get('openMode');
        $process_type_idd = $request->get('process_type_id');
        $process_type_id = $process_type_idd != '' ? Encryption::decodeId($process_type_idd) : '';
        $process_type = ProcessType::where('id', $process_type_id)->first();
        $form_id = json_decode($process_type->form_id);

        if (isset($form_id->$openMode)) {
            $url = $form_id->$openMode;
        } else {
            $url = "";
        }

        $data = ['responseCode' => 1, 'url' => $url, 'process_type_id' => $process_type_idd, 'app_id' => $app_idd, 'openMode' => $openMode];
        return $data;
    }

    public function getHelpText(Request $request)
    {
        if ($request->has('uri') && $request->get('uri') != '') {
            $module = $request->get('uri');
        }

        if (!empty($module)) {
            $data = HelpText::where('is_active', 1)->where('module', $module)->get(['filed_id', 'help_text', 'type', 'validation_class', 'filed_max_length']);
        } else {
            $data = HelpText::where('is_active', 1)->get(['filed_id', 'help_text', 'type', 'filed_max_length']);
        }
        return response()->json(['data' => $data]);
    }

    public function notifications($app_id, $process_type_id)
    {
        $app_id = Encryption::decodeId($app_id);
        $process_type_id = Encryption::decodeId($process_type_id);

        $getProcessData = ProcessList::where('ref_id', $app_id)->where('process_type_id', $process_type_id)->first(['status_id', 'company_id']);
        $getEmails = Users::where('user_sub_type', $getProcessData->company_id)->get(['user_email', 'user_phone']);
        $getProcessStatusData = ProcessStatus::where('process_type_id', $process_type_id)->where('id', $getProcessData->status_id)->first();
        $getProcessName = ProcessType::where('id', $getProcessStatusData->process_type_id)->first()->name;

        if ($getProcessStatusData->notification_content == '') {
            $content = "Please provide your mail content from database";
        } else {
            $content = $getProcessStatusData->notification_content;
        }

        if ($getProcessStatusData->notification_method == 1 || $getProcessStatusData->notification_method == 3) { //sent to mail

            foreach ($getEmails as $users) {
                $body_msg = '<span style="color:black;text-align:justify;"><b>';
                $body_msg .= $content;
                $body_msg .= '</span>';
                $body_msg .= '<br/><br/><br/>Thanks<br/>';
                $body_msg .= '<b>' . env('PROJECT_NAME') . '</b>';

                $header = "Application Update Information for " . $getProcessName;
                $param = $body_msg;
                $email_content = view("Users::message", compact('header', 'param'))->render();

                $emailQueue = new EmailQueue();
                $emailQueue->service_id = $process_type_id; // service_id of LPP
                $emailQueue->app_id = $app_id;
                $emailQueue->email_content = $email_content;
                $emailQueue->email_to = $users->user_email;
                $emailQueue->sms_to = '';
                $emailQueue->email_subject = $header;
                $emailQueue->attachment = '';
                $emailQueue->save();
            }
        }
        if ($getProcessStatusData->notification_method == 2 || $getProcessStatusData->notification_method == 3) { //sent to sms

            foreach ($getEmails as $users) {
                $body_msg = '<span style="color:black;text-align:justify;"><b>';
                $body_msg .= $content;
                $body_msg .= '</span>';
                $body_msg .= '<br/><br/><br/>Thanks<br/>';
                $body_msg .= '<b>' . env('PROJECT_NAME') . '</b>';

                $header = "Application Update Information for " . $getProcessName;
                $param = $body_msg;
                $sms_content = view("Users::message", compact('header', 'param'))->render();

                $emailQueue = new EmailQueue();
                $emailQueue->service_id = $process_type_id; // service_id of LPP
                $emailQueue->app_id = $app_id;
                $emailQueue->email_content = '';
                $emailQueue->sms_content = $sms_content;
                $emailQueue->email_to = '';
                $emailQueue->sms_to = $users->user_phone;
                $emailQueue->email_subject = $header;
                $emailQueue->attachment = '';
                $emailQueue->save();
            }
        }
    }

    public function certificateRegeneration($app_id, $process_type_id)
    {
        $app_id = Encryption::decodeId($app_id);
        $process_type_id = Encryption::decodeId($process_type_id);

        $dept_id = ProcessList::where([
            'ref_id' => $app_id,
            'process_type_id' => $process_type_id
        ])->pluck('department_id');

        if (empty($dept_id)) {
            Session::flash('error', 'Sorry, invalid department of this application. [PPC-1044]');
            return redirect()->back();
        }

        $certificateRegenerate = $this->certificateGenerationRequest($app_id, $process_type_id, 0, $dept_id, 'regenerate');
        if (!$certificateRegenerate) {
            Session::flash('error', 'Sorry, something went wrong. [PPC-1045]');
            return redirect()->back();
        }

        Session::flash('success', 'Certificate regenerate process has been completed successfully.');
        return redirect()->back();
    }

    public function favoriteDataStore(Request $request)
    {
        $process_id = Encryption::decodeId($request->get('process_list_id'));
        ProcessFavoriteList::create([
            'process_id' => $process_id,
            'user_id' => CommonFunction::getUserId()
        ]);
        return response()->json('success');
    }

    public function favoriteDataRemove(Request $request)
    {
        $process_id = Encryption::decodeId($request->get('process_list_id'));
        ProcessFavoriteList::where('process_id', $process_id)
            ->where('user_id', CommonFunction::getUserId())
            ->delete();
        return response()->json('success');
    }

    public function requestShadowFile($process_id = '', $app_id = '', $process_type_id = '', $module_name = '')
    {
        try {
            $request = new input();
            if (!empty($request->get('process_id'))) {
                $process_id = Encryption::decodeId($request->get('process_id'));
                $module_name = str_replace("", '', $request->get('module_name'));
                $process_type_id = Encryption::decodeId($request->get('process_type_id'));
                $app_id = Encryption::decodeId($request->get('ref_id'));
            }
            $jsonData['process_id'] = $process_id;
            $jsonData['module_name'] = $module_name;
            $jsonData['process_type_id'] = $process_type_id;
            $jsonData['app_id'] = $app_id;
            $jsonInfo = json_encode($jsonData);
            ShadowFile::create([
                'file_path' => '',
                'user_id' => CommonFunction::getUserId(),
                'process_type_id' => $process_type_id,
                'ref_id' => $app_id,
                'shadow_file_perimeter' => $jsonInfo
            ]);
            if (!empty($request->get('process_id'))) {
                return response()->json(['responseCode' => 1, 'status' => 'success']);
            }
            return true;
        } catch (Exception $e) {
            Log::error('PPCRequestShadowFile: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [PPC-1027]');
            if (!empty($request->get('process_id'))) {
                return response()->json(['responseCode' => 0, 'messages' => CommonFunction::showErrorPublic($e->getMessage()) . ' [PPC-1027]']);
            }
            return false;
        }
    }


    public function getProcessData($processTypeId, $appId = 0)
    {
        $app_id = Encryption::decodeId($appId);


        $resubmitId = ProcessHistory::where('ref_id', $app_id)
            ->where('process_type', $processTypeId)
            ->where('status_id', '=', 2)
            ->orderBy('id', 'desc')
            ->first(['id']);


        if ($resubmitId != null) {
            $sql2 = "SELECT  group_concat(distinct desk_id) as deskIds,group_concat(distinct status_id) as statusIds from process_list_hist
                where id >= $resubmitId->id and ref_id= $app_id 
                and process_type=$processTypeId and status_id!='-1'";
            $processHistory = \DB::select(DB::raw($sql2));
        } else {
            $sql = "select  group_concat(distinct desk_id) as deskIds,group_concat(distinct status_id) as statusIds,group_concat(distinct id) as history_id  from process_list_hist
                where ref_id = $app_id and process_type = $processTypeId and status_id!='-1'";
            $processHistory = \DB::select(DB::raw($sql));
        }
        //extra code for dynamic graph
        //        dd($processHistory);
        $passed_desks_ids = explode(',', $processHistory[0]->deskIds);
        $passed_status_ids = explode(',', $processHistory[0]->statusIds);

        array_push($passed_desks_ids, 0);

        foreach ($passed_desks_ids as $v) {
            $passed_desks_id[] = (int)$v;
        }

        foreach ($passed_status_ids as $va) {
            $passed_status_id[] = (int)$va;
        }
        $passed_status_id = array_reverse($passed_status_id);

        //        $history_id = array_reverse($history_id);


        //extra code for dynamic graph end

        $processPathTable = $this->processPathTable;
        $deskTable = $this->deskTable;
        $processStatus = $this->processStatus;

        $fullProcessPath = DB::table($processPathTable)
            ->leftJoin($deskTable . ' as from_desk', $processPathTable . '.desk_from', '=', 'from_desk.id')
            ->leftJoin($deskTable . ' as to_desk', $processPathTable . '.desk_to', '=', 'to_desk.id')
            ->leftJoin($processStatus . ' as from_process_status', function ($join) use ($processTypeId, $processPathTable) {
                $join->where('from_process_status.process_type_id', '=', $processTypeId);
                $join->on($processPathTable . '.status_from', '=', 'from_process_status.id');
            })
            ->leftJoin($processStatus . ' as to_process_status', function ($join) use ($processTypeId, $processPathTable) {
                $join->where('to_process_status.process_type_id', '=', $processTypeId);
                $join->on($processPathTable . '.status_to', '=', 'to_process_status.id');
            })
            ->select(
                $processPathTable . '.desk_from',
                $processPathTable . '.desk_to',
                $processPathTable . '.status_from as status_from',
                $processPathTable . '.status_to as status_to',
                'from_desk.desk_name as from_desk_name',
                'to_desk.desk_name as to_desk_name',
                'from_process_status.status_name as from_status_name',
                'to_process_status.status_name as to_status_name',
                'to_process_status.id as status_id'
            )
            ->where($processPathTable . '.process_type_id', $processTypeId)
            ->orderBy('process_path.id', 'ASC')
            ->get();
        //        echo "<pre>";
        //        print_r($fullProcessPath);
        //        exit;

        $moveToNextPath = [];
        $deskActions = [];
        $i = 0;

        foreach ($fullProcessPath as $process) {

            if ($i == 0) {
                $moveToNextPath[] = [
                    'Applicant',
                    $process->from_desk_name,
                    [
                        'label' => isset($resubmitId->id) ? 'Re-submitted' : $process->from_status_name,
                        //                        'label' => $process->from_status_name.' ('.$process->status_from.') ',
                        //                        'style' => 'stroke: #f77',
                    ],
                ];
            }

            if (intval($process->desk_to) > 0) {

                $moveToNextPath[] = [
                    $process->from_desk_name,
                    $process->to_desk_name,
                    [
                        'label' => $process->to_status_name,
                        //                        'label' => $process->to_status_name.' ('.$process->status_to.') ',
                        //                        'style' => 'stroke: #f77',
                    ],
                ];
            } else {
                $moveToNextPath[] = [
                    $process->from_desk_name,
                    $process->from_desk_name . '_' . $process->to_status_name,
                    ['label' => $process->to_status_name],
                    //                    ['label' => $process->to_status_name . ' (' . $process->status_to . ') '],
                ];

                $deskActions[] = [
                    'name' => $process->from_desk_name . '_' . $process->to_status_name,
                    'label' => $process->to_status_name,
                    'action_id' => $process->status_id,
                    'shape' => 'ellipse',
                    'background' => $this->getColor($process->status_to),
                ];
            }

            $i++;
        }

        $allFromDeskForThisProcess = DB::table($processPathTable)
            ->select('from_desk.desk_name as name', 'from_desk.id as desk_id', DB::raw('CONCAT(from_desk.desk_name, " (", from_desk.id, ")") as label'))
            ->leftJoin($deskTable . ' as from_desk', $processPathTable . '.desk_from', '=', 'from_desk.id')
            ->where($processPathTable . '.process_type_id', $processTypeId)
            ->groupBy('desk_from')
            ->get();

        array_push($allFromDeskForThisProcess, [
            'name' => 'Applicant',
            'label' => 'Applicant',
            'desk_id' => 0
        ]);
        //        dd($deskActions);

        return response()->json([
            'desks' => $allFromDeskForThisProcess,
            'desk_action' => $deskActions,
            'edge_path' => $moveToNextPath,
            'passed_desks_id' => $passed_desks_id,
            'passed_status_id' => $passed_status_id,
        ]);
    }

    public function getColor($i)
    {
        $colorArray = [
            '#800000',
            '#3cb44b',
            '#e6194b',
            '#911eb4',
            '#aa6e28',
            '#145A32',
            '#000080',
            '#000000',
            '#1B2631',
            '#1B4F72',
            '#008000',
            '#800080',
        ];
        try {
            return $colorArray[$i];
        } catch (\Exception $e) {

            return '#9d68d0';
        }
    }

    protected function ProceedToMeeting($statusId, $process_type_id)
    {

        if ($statusId == 19) { //19 = Proceed To Meeting
            //            if($process_type_id == 11){ // Remittance New
            //                $meetingType = 2; //Executive Council of BIDA
            //            }else{
            //                $meetingType = 1; //Inter-Ministerial Committee Meeting
            //            }

            $departmentIds = Auth::user()->department_id;
            if ($departmentIds == 2) {
                $meetingType = 2; //Executive Council of BIDA
            } else {
                $meetingType = 1; //Inter-Ministerial Committee Meeting
            }
            $meetingStatus = BoardMeetingProcessStatus::where('type_id', 3)->lists('id')->toArray();
            $getMeetingNumber = BoardMeting::where('is_active', 1)
                ->where('status', 6)
                ->where('meting_type', $meetingType)
                ->whereNotIn('status', $meetingStatus)
                ->orderBy('meting_date', 'DESC')
                //                ->where('meting_date','>=', Carbon::now())
                ->get(['meting_number', 'id', 'meting_date']);

            return $getMeetingNumber;
        } else {
            return $getMeetingNumber = [];
        }
    }

    protected function getMeetingDate(Request $request)
    {
        $meeting_id = $request->get('meeting_id');
        $singleMeetingDate = BoardMeting::where('id', $meeting_id)->first(['meting_date']);
        return response()->json(['is_calender' => 1, 'meeting_date' => $singleMeetingDate]);
    }


//    protected function storeRefIncNo($statusID, $process_list_id, $process_type_id, $process_desc, $request, $file_path)
//    {
//        $application_id = decodeId($request->get('application_ids')[0]);
//        //        dd($application_id);
//        $ref_no = $request->get('ref_no');
//        $incorporation_number = $request->get('incorporation_number');
//        $etin_number = $request->get('etin_number');
//        $tl_number = $request->get('tl_number');
//        $acc_number = $request->get('acc_number');
//        $branch_name = $request->get('branch_name');
//        $reg_no = $request->get('reg_number');
//
//        if ($statusID == 10) {
//
//
//            if ($process_type_id == 107) {
//                $nameclearance = NameClearance::where('id', $application_id)->update(
//                    [
//                        'ref_no' => $ref_no,
//                        'add_file_path' => $file_path,
//                        'incorporation_number' => $incorporation_number
//                    ]
//                );
//            } elseif ($process_type_id == 106) {
//                $nameclearance = Etin::where('id', $application_id)->update(
//                    [
//                        'ref_no' => $ref_no,
//                        'add_file_path' => $file_path,
//                        'etin_number' => $etin_number
//                    ]
//                );
//            } elseif ($process_type_id == 105) {
//                $tradeLicence = TradeLicence::where('id', $application_id)->update(
//                    [
//                        'ref_no' => $ref_no,
//                        'add_file_path' => $file_path,
//                        'tl_number' => $tl_number
//                    ]
//                );
//            } elseif ($process_type_id == 103) {
//                $bankAccount = BankAccount::where('id', $application_id)->update(
//                    [
//                        'ref_no' => $ref_no,
//                        'add_file_path' => $file_path,
//                        'acc_no' => $acc_number,
//                        'branch_name' => $branch_name
//                    ]
//                );
//            } elseif ($process_type_id == 104) {
//                $CompanyRegistration = CompanyRegistration::where('id', $application_id)->update(
//                    [
//                        'ref_no' => $ref_no,
//                        'add_file_path' => $file_path,
//                        'reg_no' => $reg_no
//                    ]
//                );
//            } else {
//                return false;
//            }
//        } else {
//            return false;
//        }
//    }

    protected function assignAgendaProcess(
        $statusID,
        $process_list_id,
        $board_meeting_id,
        $process_type_id,
        $basic_salary_from_dd,
        $application_id,
        $process_desc,
        $duration_start_date_from_dd,
        $duration_end_date_from_dd,
        $desired_duration_from_dd,
        $duration_amount_from_dd
    ) {
        if ($statusID == 19) { // 19 = proceed to meeting
            //do for later automatic create agenda
            if ($process_type_id == 6) {
                $officeType = OfficePermissionNew::where('id', $application_id)->first()->office_type;
            } elseif ($process_type_id == 7) {
                $officeType = OfficePermissionExtension::where('id', $application_id)->first()->office_type;
            } elseif ($process_type_id == 8) {
                $officeType = OfficePermissionAmendment::where('id', $application_id)->first()->office_type;
            } elseif ($process_type_id == 9) {
                $officeType = OfficePermissionCancellation::where('id', $application_id)->first()->office_type;
            } else {
                $officeType = 0;
            }

            //
            switch (true) {
                case $process_type_id == 2: //work permit new
                    $agendaMapping = AgendaMapping::where('agenda_name', 'AGENDA 5')
                        ->where('type', 'A')->first(['id', 'agenda_name', 'type']);
                    break;
                case $process_type_id == 3: //work permit Extension
                    $agendaMapping = AgendaMapping::where('agenda_name', 'AGENDA 5')
                        ->where('type', 'B')->first(['id', 'agenda_name', 'type']);
                    break;
                case $process_type_id == 4: //work permit Amendment
                    $agendaMapping = AgendaMapping::where('agenda_name', 'AGENDA 5')
                        ->where('type', 'C')->first(['id', 'agenda_name', 'type']);
                    break;
                case $process_type_id == 5: //work permit Cancellation
                    $agendaMapping = AgendaMapping::where('agenda_name', 'AGENDA 5')
                        ->where('type', 'D')->first(['id', 'agenda_name', 'type']);
                    break;
                case $process_type_id == 11: //Remittance New
                    $agendaMapping = AgendaMapping::where('agenda_name', 'AGENDA 3')
                        ->where('type', 'E')->first(['id', 'agenda_name', 'type']);
                    break;


                //for office permission
                case $process_type_id == 6 and $officeType == 1: //Office Permission New //Branch Office
                    $agendaMapping = AgendaMapping::where('agenda_name', 'AGENDA 2')
                        ->where('type', 'A')->first(['id', 'agenda_name', 'type']);
                    break;
                case $process_type_id == 7 and $officeType == 1: //Office Permission Extension //Branch Office
                    $agendaMapping = AgendaMapping::where('agenda_name', 'AGENDA 2')
                        ->where('type', 'B')->first(['id', 'agenda_name', 'type']);
                    break;
                case $process_type_id == 8 and $officeType == 1: //Office Permission Amendment //Branch Office
                    $agendaMapping = AgendaMapping::where('agenda_name', 'AGENDA 2')
                        ->where('type', 'C')->first(['id', 'agenda_name', 'type']);
                    break;
                case $process_type_id == 9 and $officeType == 1: //Office Permission Cancellation //Branch Office
                    $agendaMapping = AgendaMapping::where('agenda_name', 'AGENDA 2')
                        ->where('type', 'D')->first(['id', 'agenda_name', 'type']);
                    break;


                case $process_type_id == 6 and $officeType == 2: //Office Permission New  // Liasion
                    $agendaMapping = AgendaMapping::where('agenda_name', 'AGENDA 3')
                        ->where('type', 'A')->first(['id', 'agenda_name', 'type']);
                    break;
                case $process_type_id == 7 and $officeType == 2: //Office Permission Extension // liasion
                    $agendaMapping = AgendaMapping::where('agenda_name', 'AGENDA 3')
                        ->where('type', 'B')->first(['id', 'agenda_name', 'type']);
                    break;
                case $process_type_id == 8 and $officeType == 2: //Office Permission Amendment // liasion
                    $agendaMapping = AgendaMapping::where('agenda_name', 'AGENDA 3')
                        ->where('type', 'C')->first(['id', 'agenda_name', 'type']);
                    break;
                case $process_type_id == 9 and $officeType == 2: //Office Permission Cancellation // liasion
                    $agendaMapping = AgendaMapping::where('agenda_name', 'AGENDA 3')
                        ->where('type', 'D')->first(['id', 'agenda_name', 'type']);
                    break;


                case $process_type_id == 6 and $officeType == 3: //Office Permission New //Representative
                    $agendaMapping = AgendaMapping::where('agenda_name', 'AGENDA 4')
                        ->where('type', 'A')->first(['id', 'agenda_name', 'type']);
                    break;
                case $process_type_id == 7 and $officeType == 3: //Office Permission Extension //Representative
                    $agendaMapping = AgendaMapping::where('agenda_name', 'AGENDA 4')
                        ->where('type', 'B')->first(['id', 'agenda_name', 'type']);
                    break;
                case $process_type_id == 8 and $officeType == 3: //Office Permission Amendment //Representative
                    $agendaMapping = AgendaMapping::where('agenda_name', 'AGENDA 4')
                        ->where('type', 'C')->first(['id', 'agenda_name', 'type']);
                    break;
                case $process_type_id == 9 and $officeType == 3: //Office Permission Cancellation //Representative
                    $agendaMapping = AgendaMapping::where('agenda_name', 'AGENDA 4')
                        ->where('type', 'D')->first(['id', 'agenda_name', 'type']);
                    break;

                default:
                    \Session::flash('error', "Agenda Or Office type not define [BM-10001]");
                    return false;
                    //                    $agendaMapping = 'unknown';
                    break;
            }
            $getAgenda = Agenda::create([
                'name' => $agendaMapping->agenda_name,
                'agenda_type' => $agendaMapping->type, //A,B,C,D,E type
                'board_meting_id' => $board_meeting_id,
                'process_type_id' => $process_type_id,
                'is_active' => 1,
            ]);

            $bm_process_list = ProcessListBoardMeting::create([
                'process_id' => $process_list_id,
                'agenda_id' => $getAgenda->id,
                'basic_salary_from_dd' => $basic_salary_from_dd,
                'pl_agenda_name' => $agendaMapping->agenda_name,
                'board_meeting_id' => $board_meeting_id,
                'process_desc_from_dd' => $process_desc,
                // new code added date of 24-08-2019 by R
                'duration_start_date_from_dd' => $duration_start_date_from_dd,
                'duration_end_date_from_dd' => $duration_end_date_from_dd,
                'desired_duration_from_dd' => $desired_duration_from_dd,
                'duration_amount_from_dd' => $duration_amount_from_dd,
                //end of new code
                'is_active' => 1,
            ]);

            if ($bm_process_list) {
                return $bm_process_list->id;
            }
            return false;
            //dd($bm_process_list);
        }
    }

    public function getDuration(Request $request)
    {
        if (empty($request->get('start_date')) || empty($request->get('end_date')) || empty($request->get('process_type_id'))) {
            \Session::flash('error', 'Application duration info not fill up.[PPC-1218]');
            return false;
        }

        $appInfo = [];
        $appInfo['approved_duration_start_date'] = $request->get('start_date');
        $appInfo['approved_duration_end_date'] = $request->get('end_date');
        $appInfo['process_type_id'] = $request->get('process_type_id');

        $durationData = commonFunction::getDesiredDurationDiffDate($appInfo);
        $duration_difference = (string)$durationData['string'];
        $duration_year = (string)$durationData['duration_year'];
        $appInfo['approve_duration_year'] = (int)$durationData['approve_duration_year'];
        $duration_fees = (string)commonFunction::getGovtFees($appInfo);

        if ($duration_difference == 'Something wrong' || $duration_year == 'Something wrong') {
            $duration_fees = 'Something wrong';
        }

        return response()->json(array('duration_difference' => $duration_difference, 'duration_fees' => $duration_fees, 'duration_year' => $duration_year));
    }

    public function getDateDuration(Request $request)
    {
        if (empty($request->get('start_date')) || empty($request->get('end_date'))) {
            return false;
        }

        $appInfo = [];
        $appInfo['approved_duration_start_date'] = $request->get('start_date');
        $appInfo['approved_duration_end_date'] = $request->get('end_date');

        $durationData = commonFunction::getDesiredDurationDiffDate($appInfo);
        $duration_difference = (string)$durationData['string'];

        return response()->json(array('duration_difference' => $duration_difference));
    }

    public function startEndDateValidation(Request $request)
    {
        if (empty($request->get('start_date')) || empty($request->get('end_date'))) {
            return false;
        }

        $start_date = Carbon::parse($request->get('start_date'))->getTimestamp();
        $end_date = Carbon::parse($request->get('end_date'))->getTimestamp();

        if ($start_date > $end_date) {
            $response = 0; // 0 = error
        } else {
            $response = 1; //1= success
        }

        return response()->json(array('response' => $response));
    }

    public function changeCompanyModal($companyId)
    {
        $encoded_company_id = $companyId;
        $decoded_company_id = Encryption::decodeId($companyId);
        $current_company_info = CompanyInfo::where('id', $decoded_company_id)->first(['company_name', 'company_name_bn']);
        $company_lists = CompanyInfo::where('is_rejected', 'no')->orderBy('company_name', 'ASC')->lists('company_name', 'id')->all();
        $aclName = $this->aclName;
        $mode = '-CC-';
        return view('ProcessPath::change-company-modal', compact('current_company_info', 'company_lists', 'encoded_company_id', 'decoded_company_id', 'aclName', 'mode'));
    }

    public function storeChangeCompany(Request $request)
    {
        if (!ACL::getAccsessRight($this->aclName, '-CC-')) {
            return response()->json([
                'error' => true,
                'status' => 'You have no access right! Contact with system admin for more information. [PPC-971]'
            ]);
        }

        $rules = [
            'change_type' => 'required',
            'company_name_en' => 'required_if:change_type,1',
            //'company_name_bn' => 'required_if:change_type,1',
            'new_company_id' => 'required_if:change_type,2',
            'authorization_letter' => 'required_if:change_type,2',
        ];

        $messages = [
            'company_name_en.required_if' => 'Company name english is required.',
            //'company_name_bn.required_if' => 'Company name bangla is required.',
            'new_company_id.required_if' => 'Need to select a new company.',
            'authorization_letter.required_if' => 'Authorization letter is required.',
        ];

        $validation = Validator::make(Input::all(), $rules, $messages);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validation->errors(),
            ]);
        }

        try {

            DB::beginTransaction();

            $current_company_id = Encryption::decodeId($request->get('current_company_id'));
            $check_status = ProcessList::where([
                'company_id' => $current_company_id,
                'process_type_id' => 100,
                'status_id' => 25
            ])->count();

            if ($check_status > 0) {
                return response()->json([
                    'error' => true,
                    'status' => 'Sorry! Your company is already approved. [PPC-1031]'
                ]);
            }


            // 1 = Name Correction
            if ($request->get('change_type') == '1') {

                $check_duplicate_company = CompanyInfo::where('company_name', $request->get('company_name_en'))->count();
                if ($check_duplicate_company > 0) {
                    return response()->json([
                        'error' => true,
                        'status' => 'Sorry! <strong>' . $request->get('company_name_en') . '</strong> is already exist. [PPC-1028]'
                    ]);
                }

                // Update new company name into company table
                CompanyInfo::where('id', $current_company_id)->update([
                    'company_name' => $request->get('company_name_en'),
                    'company_name_bn' => $request->get('company_name_bn')
                ]);
                // End Update new company name into company table

                // Update new company name into Basic Information table
                BasicInformation::where('company_id', $current_company_id)
                    ->update([
                        'company_name' => $request->get('company_name_en'),
                        'company_name_bn' => $request->get('company_name_bn')
                    ]);
                // End Update new company name into Basic Information table

                // Update new company name into all existing application
                $process_data = ProcessList::where('company_id', $current_company_id)->where('process_type_id', 100)->get(['id', 'json_object', 'company_id']);
                foreach ($process_data as $process) {
                    $json_data = json_decode($process->json_object, 1);
                    $json_data['Company Name'] = $request->get('company_name_en');
                    $process->json_object = json_encode($json_data);
                    $process->save();
                }
                // End Update new company name into all existing application
            } // 2 = Company Change
            elseif ($request->get('change_type') == '2') {

                $new_company_id = $request->get('new_company_id');

                if ($current_company_id == $new_company_id) {
                    return response()->json([
                        'error' => true,
                        'status' => 'Sorry! Your requested & current company is same. [PPC-1029]'
                    ]);
                }
                $new_company_info = CompanyInfo::where('id', $new_company_id)
                    ->first(['company_name', 'company_name_bn']);

                /*
 * Following code was developed before introducing the company association.
 * but this code is not required from now.
 *
                // Update new company name into all existing application
                $process_data = ProcessList::where('company_id', $current_company_id)->where('process_type_id', 100)->get(['id', 'json_object', 'company_id']);
                foreach ($process_data as $process) {
                    $json_data = json_decode($process->json_object, 1);
                    $json_data['Company Name'] = $new_company_info->company_name;
                    $process->json_object = json_encode($json_data);
                    $process->company_id = $new_company_id;
                    $process->save();
                }
                // End Update new company name into all existing application

                // Update company name into Basic Information table
                BasicInformation::where('company_id', $current_company_id)->update([
                    'company_id' => $new_company_id,
                    'company_name' => $new_company_info->company_name,
                    'company_name_bn' => $new_company_info->company_name_bn
                ]);
                // End Update company name into Basic Information table


                // Update new company id into users table
                Users::where('company_ids', $current_company_id)->update([
                    'company_ids' => $new_company_id,
                    'working_company_id' => $new_company_id
                ]);
                // End Update new company id into users table
*/


                // Update new company id into users table
                $userData = Users::find(Auth::user()->id);
                $userData->company_ids = str_replace($current_company_id, $new_company_id, $userData->company_ids);
                $userData->working_company_id = $new_company_id;
                $userData->save();
                // End Update new company id into users table


                // Deactivate the request of current company association
                CompanyAssociation::where([
                    'user_id' => Auth::user()->id,
                    'request_type' => 'Add',
                    'requested_company_id' => $current_company_id
                ])->update([
                    'status' => 0
                ]);
                // End Deactivate the request of current company association


                // Store new request for company association
                $company_association = CompanyAssociation::firstOrNew([
                    'user_id' => Auth::user()->id,
                    'requested_company_id' => $new_company_id,
                    'request_type' => 'Add'
                ]);
                $company_association->company_type = 'existing';
                $company_association->authorization_letter = $request->get('authorization_letter');
                $company_association->current_company_ids = $current_company_id;
                $company_association->approved_user_type = 'Employee';
                $company_association->user_remarks = 'Request from BI company change';
                $company_association->application_date = date('Y-m-d H:i:s');
                $company_association->status_id = 25;
                $company_association->status = 1;
                $company_association->save();
                // End Store new request for company association


                // Reset menu and widget permission, for further setup
                Session::forget('accessible_process');
            } else {
                DB::rollback();
                return response()->json([
                    'error' => true,
                    'status' => 'Sorry! Unknown change type. [PPC-1111]'
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 'Company has been changed successfully',
                'link' => '/dashboard'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('PPCStoreChangeCompany: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [PPC-1032]');
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()) . ' [PPC-1032]'
            ]);
        }
    }


    public function getRecentAttachment($doc_id)
    {
        $doc_id = Encryption::decodeId($doc_id);

        $working_company_id = Auth::user()->working_company_id;

        $recent_doc_list = DB::select(DB::raw("select `app_documents`.`id`, `app_documents`.`doc_name`, `app_documents`.`doc_file_path`, `process_list`.`tracking_no`, `app_documents`.`updated_at`
                from `app_documents` 
                left join `process_list` on `process_list`.`process_type_id` = `app_documents`.`process_type_id` and `process_list`.`ref_id` = `app_documents`.`ref_id` 
                where `process_list`.`company_id` = $working_company_id and `process_list`.`status_id` = 25 and `app_documents`.`doc_info_id` = $doc_id and `app_documents`.`doc_file_path` != ''"));
        return view("ProcessPath::reusable-attachment", compact('recent_doc_list', 'doc_id'));
    }


    public static function batchProcessSet(Request $request)
    {
        Session::forget('is_delegation');
        $single_process_id_encrypt_current = '';
        if (!empty($request->current_process_id)) {
            $single_process_id_encrypt_current = $request->current_process_id;
        }
        if ($request->get('is_delegation') == true) {
            Session::put('is_delegation', 'is_delegation');
            $processData = ProcessList::leftJoin('process_type', 'process_list.process_type_id', '=', 'process_type.id')
                ->where('process_list.id', Encryption::decodeId($single_process_id_encrypt_current))
                ->first(['process_type.form_url', 'process_type.form_id', 'process_list.ref_id', 'process_list.process_type_id', 'tracking_no']);

            $redirect_path = CommonFunction::getAppRedirectPathByJson($processData->form_id);
            $url = url('process/' . $processData->form_url . '/' . $redirect_path['view'] . '/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));

            return response()->json([
                'responseType' => 'single',
                'url' => $url
            ]);
        }
        if (empty($request->process_id_array)) {
            return response()->json([
                'responseType' => false,
                'url' => '',
            ]);
        }

        Session::forget('batch_process_id');
        Session::forget('is_batch_update');
        Session::forget('single_process_id_encrypt');
        Session::forget('next_app_info');
        Session::forget('total_selected_app');
        Session::forget('total_process_app');

        $process_id_encryption = $request->process_id_array;
        $total_selected_app = count($process_id_encryption);

        $single_process_id_encrypt_next = null;
        $find_current_key = array_search($single_process_id_encrypt_current, $process_id_encryption); //find current key
        $keys = array_keys($process_id_encryption); //total key
        $nextKey = isset($keys[array_search($find_current_key, $keys) + 1]) ? $keys[array_search($find_current_key, $keys) + 1] : ''; //next key
        if (!empty($nextKey)) {
            $single_process_id_encrypt_next = $process_id_encryption[$nextKey]; //next process id
            $single_process_id_encrypt_next = Encryption::decodeId($single_process_id_encrypt_next);
        }
        $process_id = Encryption::decodeId($single_process_id_encrypt_current);

        Session::put('batch_process_id', $request->process_id_array);
        Session::put('is_batch_update', 'batch_update');
        Session::put('single_process_id_encrypt', $single_process_id_encrypt_current);
        Session::put('total_selected_app', $total_selected_app);
        Session::put('total_process_app', $find_current_key + 1);

        $processData = ProcessList::leftJoin('process_type', 'process_list.process_type_id', '=', 'process_type.id')
            ->where('process_list.id', $process_id)
            ->first(['process_type.form_url', 'process_type.form_id', 'process_list.ref_id', 'process_list.process_type_id', 'tracking_no']);
        $nextAppInfo = 'null';
        if ($single_process_id_encrypt_next != null) {
            $nextAppInfo = ProcessList::where('process_list.id', $single_process_id_encrypt_next)->first(['tracking_no'])->tracking_no;
        }

        Session::put('next_app_info', $nextAppInfo);

        $redirect_path = CommonFunction::getAppRedirectPathByJson($processData->form_id);
        $url = url('process/' . $processData->form_url . '/' . $redirect_path['view'] . '/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id));

        return response()->json([
            'responseType' => 'single',
            'url' => $url
        ]);
    }

    public function skipApplication($single_process_id_encrypt_current)
    {
        $batch_process_id = Session::get('batch_process_id');

        $single_process_id_encrypt_next = null;
        $single_process_id_encrypt_second_next_key = null;
        $find_current_key = array_search($single_process_id_encrypt_current, $batch_process_id); //find current key
        $keys = array_keys($batch_process_id); //total key
        $nextKey = isset($keys[array_search($find_current_key, $keys) + 1]) ? $keys[array_search($find_current_key, $keys) + 1] : ''; //next key
        $second_nextKey = isset($keys[array_search($find_current_key, $keys) + 2]) ? $keys[array_search($find_current_key, $keys) + 2] : ''; //second next key

        if (!empty($nextKey)) {
            $single_process_id_encrypt_next = $batch_process_id[$nextKey]; //next process id
        }
        if (!empty($second_nextKey)) {
            $single_process_id_encrypt_second_next_key = $batch_process_id[$second_nextKey]; //next process id
        }

        if (empty($nextKey)) {
            $existProcessInfo = ProcessList::where('process_list.id', Encryption::decodeId($batch_process_id[0]))
                ->first(['process_list.process_type_id']);
            \Session::flash('error', 'Sorry data not found!. [PPC-1081]');
            return redirect('process/list/' . Encryption::encodeId($existProcessInfo->process_type_id));
        }

        Session::put('single_process_id_encrypt', $single_process_id_encrypt_next);
        $get_total_process_app = Session::get('total_process_app');
        Session::put('total_process_app', $get_total_process_app + 1);

        $nextAppInfo = 'null';
        if ($single_process_id_encrypt_second_next_key != null) {
            $nextAppInfo = ProcessList::where('process_list.id', Encryption::decodeId($single_process_id_encrypt_second_next_key))->first(['tracking_no'])->tracking_no;
        }
        Session::put('next_app_info', $nextAppInfo);

        $processData = ProcessList::leftJoin('process_type', 'process_list.process_type_id', '=', 'process_type.id')
            ->where('process_list.id', Encryption::decodeId($single_process_id_encrypt_next))
            ->first(['process_type.form_url', 'process_type.form_id', 'process_list.ref_id', 'process_list.process_type_id', 'tracking_no']);

        $redirect_path = CommonFunction::getAppRedirectPathByJson($processData->form_id);
        $redirectUrl = 'process/' . $processData->form_url . '/' . $redirect_path['view'] . '/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id);

        return redirect($redirectUrl);
    }

    public function previousApplication($single_process_id_encrypt_current)
    {
        $batch_process_id = Session::get('batch_process_id');


        $single_process_id_encrypt_previous = null;
        $single_process_id_encrypt_next = null;
        $find_current_key = array_search($single_process_id_encrypt_current, $batch_process_id); //find current key
        $keys = array_keys($batch_process_id); //total key
        $previousKey = isset($keys[array_search($find_current_key, $keys) - 1]) ? $keys[array_search($find_current_key, $keys) - 1] : null; //next key

        if (!is_null($previousKey)) {
            $single_process_id_encrypt_previous = $batch_process_id[$previousKey]; //next process id
        }
        if (!empty($find_current_key)) {
            $single_process_id_encrypt_next = $batch_process_id[$find_current_key]; //next process id
        }


        if (is_null($previousKey)) {
            $existProcessInfo = ProcessList::where('process_list.id', Encryption::decodeId($batch_process_id[0]))
                ->first(['process_list.process_type_id']);
            \Session::flash('error', 'Sorry data not found!. [PPC-1082]');
            return redirect('process/list/' . Encryption::encodeId($existProcessInfo->process_type_id));
        }

        Session::put('single_process_id_encrypt', $single_process_id_encrypt_previous);
        $get_total_process_app = Session::get('total_process_app');
        Session::put('total_process_app', $get_total_process_app - 1);

        $nextAppInfo = 'null';
        if ($single_process_id_encrypt_next != null) {
            $nextAppInfo = ProcessList::where('process_list.id', Encryption::decodeId($single_process_id_encrypt_next))->first(['tracking_no'])->tracking_no;
        }
        Session::put('next_app_info', $nextAppInfo);
        $processData = ProcessList::leftJoin('process_type', 'process_list.process_type_id', '=', 'process_type.id')
            ->where('process_list.id', Encryption::decodeId($single_process_id_encrypt_previous))
            ->first(['process_type.form_url', 'process_type.form_id', 'process_list.ref_id', 'process_list.process_type_id', 'tracking_no']);

        $redirect_path = CommonFunction::getAppRedirectPathByJson($processData->form_id);
        $redirectUrl = 'process/' . $processData->form_url . '/' . $redirect_path['view'] . '/' . Encryption::encodeId($processData->ref_id) . '/' . Encryption::encodeId($processData->process_type_id);

        return redirect($redirectUrl);
    }

    public function getFeedbackData($list, $request)
    {
        return Datatables::of($list)
            ->addColumn('action', function ($list) use ($request) {
                $html = '';
                $redirect_path = CommonFunction::getAppRedirectPathByJson($list->form_id);
                if (!empty($request->get('is_feedback')) && CommonFunction::getUserType() == '5x505') { //when show  feedback list then hide the section
                    $process_id = "'" . Encryption::encodeId($list->id) . "'";
                    $html .= '<a type="button" onclick="rating(' . $process_id . ')" href="javascript:void(0)" class="btn btn-xs btn-success button-color" style="color: white"> <i class="fa fa-comment"></i> Feedback</a>  &nbsp;';
                }
                if (Auth::user()->user_type == '5x505' && CommonFunction::checkFeedbackItem() == true) {
                    $html .= '<a target="_blank" class="btn btn-xs btn-primary button-color"  href="' . url('process/' . $list->form_url . '/' . $redirect_path['view'] . '/' . Encryption::encodeId($list->ref_id) . '/' . Encryption::encodeId($list->process_type_id)) . '" >Open</a>';
                }

                return $html;
            })
            ->editColumn('rating', function ($list) use ($request) {
                $feedbackR = '';
                if (isset($request->is_feedback_row)) {

                    $feedbackR = @getRating($list);
                }
                return $feedbackR;
            })
            ->addColumn('desk', function ($list) {
                //return $list->desk_id == 0 ? 'Applicant' : $list->desk_name;
                return $list->desk_id == 0 ? 'Applicant' : ($list->user_id == 0 ? $list->desk_name : ('<span>' . $list->desk_name . '</span><br/><span>' . $list->user_first_name . ' ' . $list->user_last_name . '</span>'));
            })
            //->removeColumn('id', 'ref_id', 'process_type_id', 'updated_by', 'closed_by', 'created_by', 'updated_by', 'desk_id', 'status_id', 'locked_by', 'ref_fields')
            ->removeColumn('id', 'ref_id', 'process_type_id', 'updated_by', 'closed_by', 'created_by', 'updated_by', 'desk_id', 'status_id', 'ref_fields')
            ->make(true);
    }

    //    public function batchUpdateClass($request, $desk, $element)
    //    {
    //
    //        //this is for batch update code
    //        $class = '';
    //        if ($element == 'button') { // for input button open
    //            if ($request->has('process_search')) { //work for search parameter
    //                $class = 'common_batch_update_search';
    //            } elseif ($request->has('status_wise_list')) {
    //                $class = "status_wise_batch_update";
    //
    //                if ($request->get('status_wise_list') == 'is_delegation') {
    //                    //                $class = 'status_wise_batch_update is_delegation';
    //                    $class = 'is_delegation';
    //                }
    //            } else {
    //                $class = "common_batch_update";
    //            }
    //        } else { //for input hidden
    //
    //            if ($request->has('process_search')) { //work for search parameter
    //                $class = 'batchInputSearch';
    //            } elseif ($request->has('status_wise_list')) {
    //                $class = "batchInputStatus";
    //            } else {
    //                if ($desk == 'my-desk') { //for batch update
    //                    $class = 'batchInput';
    //                }
    //            }
    //        }
    //
    //        return $class;
    //    }

    public function batchUpdateClass($request, $desk)
    {

        //this is for batch update code
        $class = [
            'button_class' => '',
            'input_class' => ''
        ];


        if ($request->has('process_search')) { //work for search parameter
            $class['button_class'] = 'common_batch_update_search';
            $class['input_class'] = 'batchInputSearch';
        } elseif ($request->has('status_wise_list')) {
            $class['button_class'] = "status_wise_batch_update";
            $class['input_class'] = "batchInputStatus";

            if ($request->get('status_wise_list') == 'is_delegation') {
                $class['button_class'] = 'is_delegation';
            }
        } else {
            //$class['button_class'] = "common_batch_update";
            if ($desk != 'favorite_list') {
                $class['button_class'] = "common_batch_update";
            }
            if ($desk == 'my-desk') { //for batch update
                $class['input_class'] = 'batchInput';
            }
        }

        return $class;
    }

    public function statusWiseEmailResend($process_type_id, $app_id, $status_id)
    {
        $process_type_id = Encryption::decodeId($process_type_id);
        $app_id = Encryption::decodeId($app_id);
        $status_id = Encryption::decodeId($status_id);

        $email_queues = EmailQueue::where('process_type_id', $process_type_id)
            ->where('app_id', $app_id)
            ->where('status_id', $status_id)
            ->get(['id', 'email_status', 'email_no_of_try']);

        if (count($email_queues) == 0) {
            Session::flash('error', 'Sorry, No email found. [PPC-1083]');
            return redirect()->back();
        }

        foreach ($email_queues as $email_queue) {
            $data = EmailQueue::find($email_queue->id);
            $data->email_status = 0;
            $data->email_no_of_try = 0;
            $data->save();
        }

        Session::flash('success', 'Email resend process has been completed successfully.');
        return redirect()->back();
    }

    public function getProcessDataByAjax($processTypeId, $appId = 0, $cat_id)
    {

        $app_id = Encryption::decodeId($appId);
        $cat_id = Encryption::decodeId($cat_id);
        $resubmitId = ProcessHistory::where('ref_id', $app_id)
            ->where('process_type', $processTypeId)
            ->where('status_id', '=', 2)
            ->orderBy('id', 'desc')
            ->first(['id']);


        if ($resubmitId != null) {
            $sql2 = "SELECT  group_concat(distinct desk_id) as deskIds,group_concat(distinct status_id) as statusIds from process_list_hist
                where id >= $resubmitId->id and ref_id= $app_id 
                and process_type=$processTypeId and status_id!='-1'";
            $processHistory = \DB::select(DB::raw($sql2));
        } else {
            $sql = "select  group_concat(distinct desk_id) as deskIds,group_concat(distinct status_id) as statusIds,group_concat(distinct id) as history_id  from process_list_hist
                where ref_id = $app_id and process_type = $processTypeId and status_id!='-1'";
            $processHistory = \DB::select(DB::raw($sql));
        }

        //extra code for dynamic graph
        $passed_desks_ids = explode(',', $processHistory[0]->deskIds);
        $passed_status_ids = explode(',', $processHistory[0]->statusIds);

        array_push($passed_desks_ids, 0);

        foreach ($passed_desks_ids as $v) {
            $passed_desks_id[] = (int)$v;
        }

        foreach ($passed_status_ids as $va) {
            $passed_status_id[] = (int)$va;
        }

        $passed_status_id = array_reverse($passed_status_id);
        //extra code for dynamic graph end

        $processPathTable = $this->processPathTable;
        $deskTable = $this->deskTable;
        $processStatus = $this->processStatus;

        $fullProcessPath = DB::table($processPathTable)
            ->leftJoin($deskTable . ' as from_desk', $processPathTable . '.desk_from', '=', 'from_desk.id')
            ->leftJoin($deskTable . ' as to_desk', $processPathTable . '.desk_to', '=', 'to_desk.id')
            ->leftJoin(
                $processStatus . ' as from_process_status',
                function ($join) use ($processTypeId, $processPathTable) {
                    $join->where('from_process_status.process_type_id', '=', $processTypeId);
                    $join->on($processPathTable . '.status_from', '=', 'from_process_status.id');
                }
            )
            ->leftJoin(
                $processStatus . ' as to_process_status',
                function ($join) use ($processTypeId, $processPathTable) {
                    $join->where('to_process_status.process_type_id', '=', $processTypeId);
                    $join->on($processPathTable . '.status_to', '=', 'to_process_status.id');
                }
            )
            ->select(
                $processPathTable . '.desk_from',
                $processPathTable . '.desk_to',
                $processPathTable . '.status_from as status_from',
                $processPathTable . '.status_to as status_to',
                'from_desk.desk_name as from_desk_name',
                'to_desk.desk_name as to_desk_name',
                'from_process_status.status_name as from_status_name',
                'to_process_status.status_name as to_status_name',
                'to_process_status.id as status_id'
            )
            ->where($processPathTable . '.process_type_id', $processTypeId)
            ->where($processPathTable . '.cat_id', $cat_id)
            ->orderBy('process_path.id', 'ASC')
            ->get();
        //        echo "<pre>";
        //        print_r($fullProcessPath);
        //        exit;

        $moveToNextPath = [];
        $deskActions = [];
        $i = 0;

        foreach ($fullProcessPath as $process) {

            if ($i == 0) {
                $moveToNextPath[] = [
                    'Applicant',
                    $process->from_desk_name,
                    [
                        'label' => isset($resubmitId->id) ? 'Re-submitted' : $process->from_status_name,
                        //                        'label' => $process->from_status_name.' ('.$process->status_from.') ',
                        //                        'style' => 'stroke: #f77',
                    ],
                ];
            }

            if (intval($process->desk_to) > 0) {

                $moveToNextPath[] = [
                    $process->from_desk_name,
                    $process->to_desk_name,
                    [
                        'label' => $process->to_status_name,
                        //                        'label' => $process->to_status_name.' ('.$process->status_to.') ',
                        //                        'style' => 'stroke: #f77',
                    ],
                ];
            } else {
                $moveToNextPath[] = [
                    $process->from_desk_name,
                    $process->from_desk_name . '_' . $process->to_status_name,
                    ['label' => $process->to_status_name],
                    //                    ['label' => $process->to_status_name . ' (' . $process->status_to . ') '],
                ];

                $deskActions[] = [
                    'name' => $process->from_desk_name . '_' . $process->to_status_name,
                    'label' => $process->to_status_name,
                    'action_id' => $process->status_id,
                    'shape' => 'ellipse',
                    'background' => $this->getColor($process->status_to),
                ];
            }

            $i++;
        }

        $allFromDeskForThisProcess = DB::table($processPathTable)
            ->select(
                'from_desk.desk_name as name',
                'from_desk.id as desk_id',
                DB::raw('CONCAT(from_desk.desk_name, " (", from_desk.id, ")") as label')
            )
            ->leftJoin($deskTable . ' as from_desk', $processPathTable . '.desk_from', '=', 'from_desk.id')
            ->where($processPathTable . '.process_type_id', $processTypeId)
            ->groupBy('desk_from')
            ->get();

        array_push($allFromDeskForThisProcess, [
            'name' => 'Applicant',
            'label' => 'Applicant',
            'desk_id' => 0
        ]);

        return response()->json([
            'desks' => $allFromDeskForThisProcess,
            'desk_action' => $deskActions,
            'edge_path' => $moveToNextPath,
            'passed_desks_id' => $passed_desks_id,
            'passed_status_id' => $passed_status_id,
        ]);
    }

    public function getShadowFileHistory($process_type_id, $ref_id)
    {
        $process_type_id = Encryption::decodeId($process_type_id);
        $ref_id = Encryption::decodeId($ref_id);
        $getShadowFile = ShadowFile::where('user_id', CommonFunction::getUserId())
            ->where('ref_id', $ref_id)
            ->where('process_type_id', $process_type_id)
            ->orderBy('id', 'DESC')
            ->get();
        $content = strval(view('ProcessPath::shadow-files', compact('getShadowFile')));
        return response()->json(['response' => $content]);
    }

    public function getApplicationeHistory($process_list_id)
    {
        $process_list_id = Encryption::decodeId($process_list_id);
        $process_history = DB::select(DB::raw("select  `process_list_hist`.`desk_id`,`as`.`status_name`,
                                `process_list_hist`.`process_id`,
                                if(`process_list_hist`.`desk_id`=0,\"Applicant\",`ud`.`desk_name`) `deskname`,
                                `users`.`user_first_name`, 
                                `users`.`user_middle_name`, 
                                `users`.`user_last_name`, 
                                `process_list_hist`.`updated_by`, 
                                `process_list_hist`.`status_id`, 
                                `process_list_hist`.`process_desc`, 
                                `process_list_hist`.`process_id`, 
                                `process_list_hist`.`updated_at`,
                                 group_concat(`pd`.`file`) as files
                                from `process_list_hist`
                                left join `process_documents` as `pd` on `process_list_hist`.`id` = `pd`.`process_hist_id`
                                left join `user_desk` as `ud` on `process_list_hist`.`desk_id` = `ud`.`id`
                                left join `users` on `process_list_hist`.`updated_by` = `users`.`id`     
                                
                                left join `process_status` as `as` on `process_list_hist`.`status_id` = `as`.`id`
                                and `process_list_hist`.`process_type` = `as`.`process_type_id`
                                where `process_list_hist`.`process_id`  = '$process_list_id' 
                                and `process_list_hist`.`status_id` != -1
                    group by `process_list_hist`.`process_id`,`process_list_hist`.`desk_id`, `process_list_hist`.`status_id`, process_list_hist.updated_at
                    order by process_list_hist.updated_at desc

                    "));
        $content = strval(view('ProcessPath::application-history-table', compact('process_history')));
        return response()->json(['response' => $content]);
    }

    public function openAttachment($process_type_id, $app_id, $doc_section = '')
    {
        $decodedAppId = Encryption::decodeId($app_id);
        $processTypeId = Encryption::decodeId($process_type_id);
        $doc_section = empty($doc_section) ? 'master' : Encryption::decodeId($doc_section);

        //get tracking no from process list
        $processInfo = ProcessList::where('ref_id', $decodedAppId)->where('process_type_id', $processTypeId)->first(['tracking_no']);

        //document info ..
        $document = AppDocuments::leftJoin('attachment_list', 'attachment_list.id', '=', 'app_documents.doc_info_id')
            ->leftJoin('attachment_type', 'attachment_type.id', '=', 'attachment_list.attachment_type_id')
            //->where('attachment_type.key', $attachment_key)
            ->where('app_documents.ref_id', $decodedAppId)
            ->where('app_documents.process_type_id', $processTypeId)
            ->where('app_documents.doc_section', $doc_section)
            ->where('app_documents.doc_file_path', '!=', '')
            ->get([
                'attachment_list.id',
                'attachment_list.doc_priority',
                'attachment_list.short_note',
                'attachment_list.additional_field',
                'app_documents.id as document_id',
                'app_documents.doc_file_path as doc_file_path',
                'app_documents.doc_name',
            ]);


        return view('ProcessPath::attachment-panel', compact('document', 'processInfo'));
    }

    public function getFeedbackList(Request $request)
    {
        $process_type_id = $request->get('process_type_id'); //new process type get by javascript session
        $list = ProcessList::getApplicationList($process_type_id, $request, $desk = '');
        return $this->getFeedbacKData($list, $request);
    }

    /**
     * getLastRemarks
     *
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLastRemarks(Request $request)
    {
        try {
            $process_type_id = $request->process_type_id;
            $process_list_id = $request->process_list_id;
            $status_id = $request->status_id;


            $remarks_attachment = DB::select(DB::raw(
                "select * from
                                `process_documents`
                                where `process_type_id` = $process_type_id and 
                                `ref_id` = $process_list_id and `status_id` = $status_id
                                and `process_hist_id` = (SELECT MAX(process_hist_id) FROM process_documents 
                                WHERE ref_id=$process_list_id AND 
                                process_type_id=$process_type_id AND status_id=$status_id)
                                ORDER BY id ASC"
            ));


            $data['status_code'] = 200;
            $data['status'] = 'success';
            $data['message'] = '';
            $data['data'] = strval(view('ProcessPath::remarks-attachment-list', compact('remarks_attachment')));;
        } catch (\Exception $exception) {
            $data['status_code'] = 404;
            $data['status'] = 'error';
            $data['message'] = $exception->getMessage();
        } finally {
            return response()->json(['response' => $data]);
        }
    }

    public function irmsFeedbackList(){

        $userType = Auth::user()->user_type;
        $companyIds = CommonFunction::getUserWorkingCompany();
        return view("ProcessPath::irms-feedback-list", compact(
            'companyIds', 
            'userType'
        ));
    }

    public function getIrmsList(Request $request){
        $list = ProcessList::leftJoin('br_apps', 'br_apps.id', '=', 'process_list.ref_id')
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
                        'process_list.company_id' => $request->companyIds,
                        'br_apps.irms_request_initiate' => 1,
                    ])
                    ->whereIn('irr.status_id', [0, -1, 1, 5])// 0=Pending, -1=save as draft, 1 = submit, 5=shortfall
                    ->get();

        if ($list !== null) {
            return Datatables::of($list)

            ->editColumn('feedback_deadline', function ($row) {
                return Carbon::parse($row->feedback_deadline)->format('Y-m-d');
            })
            ->addColumn('irms_status_id', function ($row) {
                if ($row->irms_status_id == -1) {
                    return 'Draft';
                } elseif ($row->irms_status_id == 1) {
                    return 'Submit';
                } elseif ($row->irms_status_id == 5) {
                    return 'Shortfall';
                } else {
                    return 'Pending';
                }
            })
            ->addColumn('json_data', function ($row) {
                return getListDataFromJson($row->json_object, CommonFunction::getCompanyNameById($row->company_id));
            })
            ->addColumn('Service', function($row){
        
                return 'BIDA Registration';
            })
            ->addColumn('action', function ($row) {
                if (in_array($row->irms_status_id, [0, -1, 5])) {
                    $url = url('irms-portal-login/'.Encryption::encode($row->tracking_no));
                    $html = '<a href="'.$url.'" class="btn btn-xs btn-primary button-color"><i class="fa fa-folder-open"></i> Open</a>';
                                    
                } else {
                    $html = '';
                    
                }
                return $html;
            })
            ->make(true);
        } else {
            return response()->json(['error' => 'Data not found'], 404);
        }
        
    }
    
}
