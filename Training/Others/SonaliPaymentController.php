<?php

namespace App\Modules\SonaliPayment\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Models\ProcessType;
use App\Modules\Settings\Models\Configuration;
use App\Modules\SonaliPayment\Models\PaymentCategory;
use App\Modules\SonaliPayment\Models\PaymentDetails;
use App\Modules\SonaliPayment\Models\PaymentWithoutVat;
use App\Modules\SonaliPayment\Models\SonaliPayment;
use App\Modules\SonaliPayment\Models\SonaliPaymentHistory;
use App\Modules\Users\Models\CompanyInfo;
use App\Modules\VisaRecommendation\Models\VisaRecommendation;
use App\Modules\VisaRecommendationAmendment\Models\VisaRecommendationAmendment;
use App\Modules\WorkPermitAmendment\Models\WorkPermitAmendment;
use App\Modules\WorkPermitCancellation\Models\WorkPermitCancellation;
use App\Modules\WorkPermitExtension\Models\WorkPermitExtension;
use App\Modules\WorkPermitNew\Models\WorkPermitNew;
use App\VRCommonPool;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Milon\Barcode\DNS1D;
use yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Session;
use Mpdf\Mpdf;

class SonaliPaymentController extends Controller
{
    /**
     * Payment types
     * ================================
     * A01 => Sonali Bank Counter Based Payment
     * A02 => Sonali Bank Account
     * C01 => Sonali Bank Card(SBL)
     * ================================
     */

    public function index()
    {
        if (!ACL::getAccsessRight('SonaliPayment', '-V-')) {
            die('You have no access right! Please contact system administration for more information.');
        }

        try {
            $ProcessType = ProcessType::whereStatus(1)
                ->orderBy('name')
                ->lists('name', 'id')
                ->all();

            $CompanyInfo = CompanyInfo::orderBy('company_name')
                ->lists('company_name', 'id')
                ->all();

            $PaymentType = PaymentCategory::whereStatus(1)
                ->orderBy('name')
                ->lists('name', 'id')
                ->all();

            return view('SonaliPayment::list', compact('ProcessType', 'CompanyInfo', 'PaymentType'));
        } catch (\Exception $e) {
            Session::flash('error', 'Something went wrong! Error: ' . $e->getMessage() . '[SP-142]');
            return \redirect('dashboard');
        }
    }

    public function indivPaymentHistory($paymentId = '')
    {
        if (!ACL::getAccsessRight('SonaliPayment', '-V-')) {
            die('You have no access right! Please contact system administration for more information.');
        }

        try {
            $ProcessType = ProcessType::whereStatus(1)
                ->orderBy('name')
                ->lists('name', 'id')
                ->all();

            $CompanyInfo = CompanyInfo::orderBy('company_name')
                ->lists('company_name', 'id')
                ->all();

            $PaymentType = PaymentCategory::whereStatus(1)
                ->orderBy('name')
                ->lists('name', 'id')
                ->all();

            return view('SonaliPayment::payment-history',
                compact('ProcessType', 'CompanyInfo', 'PaymentType', 'paymentId'));
        } catch (\Exception $e) {
            Session::flash('error', 'Something went wrong! Error: ' . $e->getMessage() . '[SP-142]');
            return \redirect('dashboard');
        }
    }


    /*
     * Payment list
     */
    public function paymentList()
    {
        if (!ACL::getAccsessRight('SonaliPayment', '-E-')) {
            die('You have no access right! Please contact system administration for more information.');
        }

        DB::statement(DB::raw('set @rownum=0'));
        $list = SonaliPayment::leftJoin('process_list', function ($join) {
            $join->on('process_list.process_type_id', '=', 'sp_payment.process_type_id');
            $join->on('process_list.ref_id', '=', 'sp_payment.app_id');
        })
            ->where('sp_payment.updated_at', '>=', Carbon::now()->subMonths(3)) //search data from the last three months
            ->orderBy('sp_payment.id', 'desc')
            ->get([
                'sp_payment.id',
                DB::raw('@rownum  := @rownum  + 1 AS sl_no'),
                'app_tracking_no',
                'contact_email',
                'app_id',
                'sp_payment.process_type_id',
                'pay_mode_code',
                'transaction_id',
                'request_id',
                'payment_category_id',
                'ref_tran_no',
                'ref_tran_date_time',
                'pay_amount',
                'payment_status',
                'is_verified',
                'process_list.tracking_no'
            ]);

        return Datatables::of($list)
            ->addColumn('action', function ($list) {
                $btn = '';
                if (ACL::getAccsessRight('SonaliPayment', '-A-')) {
                    if ($list->payment_status == 1 && $list->is_verified == 1) {
                        $btn .= '<a href="' . url('/spg/verifyAndComplete/' . Encryption::encodeId($list->id)) . '" class="btn btn-xs btn-info"><i class="fa fa-check"></i> Verify & Complete</a> <br/>';
                    }
                    //$btn .= ' <a href="' . url('/spg/verify/' . Encryption::encodeId($list->id)) . '" class="btn btn-xs btn-primary"><i class="fa fa-check"></i> Verify</a>';
                    $btn .= ' <a href="' . url('/spg/payment-history/' . Encryption::encodeId($list->id)) . '" class="btn btn-xs btn-primary"><i class="fa fa-check"></i> Open</a>';
                    $btn .= ' <a href="' . url('/spg/ref-verification/' . Encryption::encodeId($list->id)) . '" class="btn btn-xs btn-success"><i class="fa fa-check"></i> Verify by Ref. No.</a>';
                }
                return $btn;
            })
            ->editColumn('payment_status', function ($list) {
                $status = '';
                if ($list->payment_status == 1) {
                    $status .= "<span class='label label-success'>Payment Success</span>";
                } elseif ($list->payment_status == '-1') {
                    $status .= "<span class='label label-warning'>Payment in-progress</span>";
                } else {
                    $status .= "<span class='label label-danger'>Payment Failed</span>";
                }

                if ($list->is_verified == 1) {
                    $status .= "<br/><span class='label label-info'>Verification success</span>";
                } else {
                    $status .= "<br/><span class='label label-danger'>Verification failed</span>";
                }
                return $status;
            })
            ->removeColumn('id')
            ->make(true);
    }

    public function paymentUnverifiedList()
    {
        $list = SonaliPayment::leftJoin('process_list', function ($join) {
            $join->on('process_list.process_type_id', '=', 'sp_payment.process_type_id');
            $join->on('process_list.ref_id', '=', 'sp_payment.app_id');
        })
            ->whereIn('process_list.status_id',[-1, 15, 32])
            ->where([
                'process_list.desk_id' => 0,
                'sp_payment.payment_status' => 1,
                'sp_payment.is_verified' => 0,
            ])
            ->orderBy('sp_payment.id', 'desc')
            ->get([
                'sp_payment.id',
                'app_tracking_no',
                'contact_email',
                'app_id',
                'sp_payment.process_type_id',
                'pay_mode_code',
                'transaction_id',
                'request_id',
                'payment_category_id',
                'ref_tran_no',
                'ref_tran_date_time',
                'pay_amount',
                'payment_status',
                'is_verified',
                'process_list.tracking_no'
            ]);

        return Datatables::of($list)
            ->addColumn('action', function ($list) {
                $btn = '';
                if (ACL::getAccsessRight('SonaliPayment', '-A-')) {
                    if ($list->payment_status == 1 && $list->is_verified == 1) {
                        $btn .= '<a href="' . url('/spg/verifyAndComplete/' . Encryption::encodeId($list->id)) . '" class="btn btn-xs btn-info"><i class="fa fa-check"></i> Verify & Complete</a> <br/>';
                    }
                    //$btn .= ' <a href="' . url('/spg/verify/' . Encryption::encodeId($list->id)) . '" class="btn btn-xs btn-primary"><i class="fa fa-check"></i> Verify</a>';
                    $btn .= ' <a href="' . url('/spg/payment-history/' . Encryption::encodeId($list->id)) . '" class="btn btn-xs btn-primary"><i class="fa fa-check"></i> Open</a>';
                    $btn .= ' <a href="' . url('/spg/ref-verification/' . Encryption::encodeId($list->id)) . '" class="btn btn-xs btn-success"><i class="fa fa-check"></i> Verify by Ref. No.</a>';
                }
                return $btn;
            })
            ->editColumn('payment_status', function ($list) {
                $status = '';
                if ($list->payment_status == 1) {
                    $status .= "<span class='label label-success'>Payment Success</span>";
                } elseif ($list->payment_status == '-1') {
                    $status .= "<span class='label label-warning'>Payment in-progress</span>";
                } else {
                    $status .= "<span class='label label-danger'>Payment Failed</span>";
                }

                if ($list->is_verified == 1) {
                    $status .= "<br/><span class='label label-info'>Verification success</span>";
                } else {
                    $status .= "<br/><span class='label label-danger'>Verification failed</span>";
                }
                return $status;
            })
            ->removeColumn('id')
            ->make(true);

    }

    public function indivPaymentHistoryData(Request $request)
    {
        if (!ACL::getAccsessRight('SonaliPayment', '-E-')) {
            die('You have no access right! Please contact system administration for more information.');
        }

        $spPaymentId = Encryption::decodeId($request->get('sp_payment_id'));

        DB::statement(DB::raw('set @rownum=0'));
        $list = SonaliPaymentHistory::leftJoin('process_list', function ($join) {
            $join->on('process_list.process_type_id', '=', 'sp_payment_history.process_type_id');
            $join->on('process_list.ref_id', '=', 'sp_payment_history.app_id');
        })->where('sp_payment_id', $spPaymentId)
            ->orderBy('sp_payment_history.id', 'desc')
            ->get([
                'sp_payment_history.id',
                DB::raw('@rownum  := @rownum  + 1 AS sl_no'),
                'app_tracking_no',
                'sp_payment_id',
                'contact_email',
                'app_id',
                'sp_payment_history.process_type_id',
                'pay_mode_code',
                'transaction_id',
                'request_id',
                'ref_tran_no',
                'ref_tran_date_time',
                'pay_amount',
                'payment_status',
                'is_verified',
                'process_list.tracking_no'
            ]);

        return Datatables::of($list)
            ->addColumn('action', function ($list) {
                $btn = '';
                if (ACL::getAccsessRight('SonaliPayment', '-A-')) {
                    $btn .= '<a href="' . url('/spg/history-verify/' . Encryption::encodeId($list->sp_payment_id)) . '/' . Encryption::encodeId($list->id) . '" class="btn btn-xs btn-primary"><i class="fa fa-check"></i> Verify</a>';
                }
                return $btn;
            })
            ->editColumn('payment_status', function ($list) {
                $status = '';
                if ($list->payment_status == 1) {
                    $status .= "<span class='label label-success'>Payment Success</span>";
                } elseif ($list->payment_status == '-1') {
                    $status .= "<span class='label label-warning'>Payment in-progress</span>";
                } else {
                    $status .= "<span class='label label-danger'>Payment Failed</span>";
                }

                if ($list->is_verified == 1) {
                    $status .= "<br/><span class='label label-info'>Verification success</span>";
                } else {
                    $status .= "<br/><span class='label label-danger'>Verification failed</span>";
                }
                return $status;
            })
            ->removeColumn('id')
            ->make(true);
    }


    /*
    * Payment list by search
    */
    public function getPaymentList(Request $request)
    {
        if (!ACL::getAccsessRight('SonaliPayment', '-E-')) {
            die('You have no access right! Please contact system administration for more information.');
        }

//        old
//        $process_type_id = $request->process_type;
//        $company_id = $request->company;
//        $payment_category_id = $request->payment_type;

        DB::statement(DB::raw('set @rownum=0'));
        $list = SonaliPayment::leftJoin('process_list', function ($join) {
            $join->on('process_list.process_type_id', '=', 'sp_payment.process_type_id');
            $join->on('process_list.ref_id', '=', 'sp_payment.app_id');
        })
            ->where('process_list.tracking_no', trim($request->search_text));
            // ->where('sp_payment.payment_status', 1); // including records of payment failures

//        old
//        if (!empty($process_type_id)) {
//            $list->where('sp_payment.process_type_id', $process_type_id);
//        }
//        if (!empty($company_id)) {
//            $list->where('process_list.company_id', $company_id);
//        }
//        if (!empty($payment_category_id)) {
//            $list->where('sp_payment.payment_category_id', $payment_category_id);
//        }

        $list->orderBy('sp_payment.id', 'desc')
            ->get([
                'sp_payment.id',
                DB::raw('@rownum  := @rownum  + 1 AS sl_no'),
                'pay_mode',
                'app_id',
                'sp_payment.process_type_id',
                'app_tracking_no',
                'pay_mode_code',
                'transaction_id',
                'request_id',
                'ref_tran_no',
                'ref_tran_date_time',
                'pay_amount',
                'payment_status',
                'is_verified',
                'process_list.tracking_no'
            ]);

        return Datatables::of($list)
            ->addColumn('action', function ($list) {
                $btn = '';
                if (ACL::getAccsessRight('SonaliPayment', '-A-')) {
                    if ($list->payment_status == 1 && ($list->is_verified == 0 || $list->is_verified == 1) && ($list->tracking_no == '' or $list->tracking_no == null)) {
                        $btn .= '<a href="' . url('/spg/verifyAndComplete/' . Encryption::encodeId($list->id)) . '" class="btn btn-xs btn-info"><i class="fa fa-check"></i> Verify & Complete</a> <br/>';
                    }
                    $btn .= '<a href="' . url('/spg/verify/' . Encryption::encodeId($list->id)) . '" class="btn btn-xs btn-primary"><i class="fa fa-check"></i> Verify</a>';
                }
                return $btn;
            })
            ->editColumn('payment_status', function ($list) {
                $status = '';
                if ($list->payment_status == 1) {
                    $status .= "<span class='label label-success'>Payment Success</span>";
                } elseif ($list->payment_status == '-1') {
                    $status .= "<span class='label label-warning'>Payment in-progress</span>";
                } else {
                    $status .= "<span class='label label-danger'>Payment Failed</span>";
                }

                if ($list->is_verified == 1) {
                    $status .= "<br/><span class='label label-info'>Verification success</span>";
                } else {
                    $status .= "<br/><span class='label label-danger'>Verification failed</span>";
                }
                return $status;
            })
            ->removeColumn('id')
            ->make(true);
    }


    /*
     * Multiple Sonali payment initiate
     */
    public static function initiatePaymentMultiple($paymentId = null)
    {
        //  if (config('app.server_type') == 'local' && config('payment.spg_settings.payment_mode') == 'off') 
        //  {
        //     // $ref_url = $_SERVER["HTTP_REFERER"];
        //     // $url_sec = explode('/', $ref_url);

        //     $paymentId = Encryption::decodeId($paymentId);
        //     $sonaliPayment = SonaliPayment::find($paymentId);

        //     $process_info = ProcessType::find($sonaliPayment->process_type_id);
        //     $process_list = ProcessList::where('process_type_id', $sonaliPayment->process_type_id)->where('ref_id', $sonaliPayment->app_id)->first();
             
        //     if ($process_list->status_id == -1){
        //         $sonaliPayment->payment_category_id = 1;
        //     }else{
        //         $sonaliPayment->payment_category_id = 2;
        //     }

        //     $sonaliPayment->payment_status = 1;
        //     $sonaliPayment->app_id = $sonaliPayment->app_id;
        //     $sonaliPayment->process_type_id = $sonaliPayment->process_type_id;
        //     $sonaliPayment->save();
        //     return redirect($process_info->form_url . '/afterPayment/' . Encryption::encodeId($paymentId));
        //  }

        try {

            if ($paymentId != null) {
                // DB::begintransaction();
                $web_service_url = config('payment.spg_settings.spg_web_service_url');
                $user_id = config('payment.spg_settings.spg_user_id');
                $password = config('payment.spg_settings.spg_password');
                $sblAccount = config('payment.spg_settings.spg_SBL_account');
                $payment_callback_url = config('payment.spg_settings.return_url_m');

                $decodePaymentId = Encryption::decodeId($paymentId);
                $paymentInfo = SonaliPayment::find($decodePaymentId);
                $payment_details = PaymentDetails::where('sp_payment_id', $paymentInfo->id)->get();

                /*
                 * $payment_details return a collection of data.
                 * php empty() function can't identify the collection is empty or not.
                 */
                if (empty($paymentInfo) || $payment_details->isEmpty()) {
                    //DB::rollback();
                    Session::flash('error', 'Payment record not found! [SPM-102]');
                    return redirect()->back();
                }

                // Get application process information & URL redirection
                $process_info = ProcessList::leftJoin('process_type as pt', 'pt.id', '=', 'process_list.process_type_id')
                    ->where('process_list.process_type_id', $paymentInfo->process_type_id)
                    ->where('process_list.ref_id', $paymentInfo->app_id)
                    ->first([
                        'process_list.tracking_no',
                        'pt.form_url',
                    ]);

                $ref_tran_no = $process_info->tracking_no . '-' . $paymentInfo->payment_category_id;
                $paymentInfo->ref_tran_no = $ref_tran_no;
                $paymentInfo->app_tracking_no = $process_info->tracking_no;
                $paymentInfo->save();
                //DB::commit();


                //DB::beginTransaction();


                /*
                 * This code execute if second time payment button click
                 */
                if (!empty($paymentInfo->request_id)) {
                    $paymentVerifyCheck = (new self())->transactionVerificationWithRefNo($decodePaymentId);

                    if (isset($paymentVerifyCheck['code']) && ($paymentVerifyCheck['code'] == '200' || $paymentVerifyCheck['code'] == 200)) {

                        // Single payment distribution verify
                        //(new self())->singlePaymentDetailsVerification($paymentInfo->id);

                        //DB::commit();
                        Session::flash('error', 'The payment was paid for this application [SPM-106]');
                        return redirect($process_info->form_url . '/afterPayment/' . Encryption::encodeId($decodePaymentId));
                    }
                    // Payment pending
                    if ($paymentVerifyCheck['code'] == '201') {
                        $from_time = strtotime(date($paymentInfo->ref_tran_date_time));
                        $to_time = strtotime(date('Y-m-d H:i:s'));
                        $time_diff_minute = round(abs($to_time - $from_time) / 60);

                        $waiting_minutes = Configuration::where('caption', 'Payment_pending_minute')->pluck('value');
                        if ($time_diff_minute < $waiting_minutes) {
                            //DB::commit();
                            Session::flash('error', 'Please try again after ' . (($waiting_minutes + 1) - $time_diff_minute) . ' minutes or call to help line. [SPM-107]');
                            return redirect()->back();
                        }
                    }
                }

                $send_request_id = config('payment.spg_settings.request_id_prefix') . rand(1000000, 9999999); // Will be change later
                $send_payment_date = date('Y-m-d');
                $send_ref_tran_date_time = date('Y-m-d H:i:s'); // need to clarify


                $body = '<?xml version="1.0" encoding="utf-8"?>
                 <soap:Envelope 
                 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                 xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
                 xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                    <soap:Header>
                        <SpgUserCredentials xmlns="http://tempuri.org/">
                            <userName>' . $user_id . '</userName>
                            <password>' . $password . '</password>
                        </SpgUserCredentials>
                    </soap:Header>
                    <soap:Body>
                        <GetSessionKey xmlns="http://tempuri.org/">
                            <strUserId>' . $user_id . '</strUserId>
                            <strPassKey>' . $password . '</strPassKey>
                            <strRequestId>' . $send_request_id . '</strRequestId>
                            <strAmount>' . ($paymentInfo->pay_amount + $paymentInfo->vat_on_pay_amount) . '</strAmount>
                            <strTranDate>' . $send_payment_date . '</strTranDate>
                            <strAccounts>' . $paymentInfo->receiver_ac_no . '</strAccounts>
                        </GetSessionKey>
                    </soap:Body>
                </soap:Envelope>';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $web_service_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_HTTPHEADER,
                    array("Content-Type: text/xml;charset=utf-8", 'SOAPAction: "http://tempuri.org/GetSessionKey"'));
                $result = curl_exec($ch);
                if (curl_errno($ch)) {
                    //DB::rollback();
                    Session::flash('error', 'Request Error: ' . curl_error($ch) . '[SPM-139]');
                    return redirect()->back();
                }

                $xml_parser = xml_parser_create();
                xml_parse_into_struct($xml_parser, $result, $val);

                $getToken = $val[3]["value"];
                $sessTokenDecode = json_decode($getToken);
                if (!isset($sessTokenDecode)) {
                    //DB::rollback();
                    Session::flash('error', 'Session token not found [SPM-103]');
                    return redirect()->back();
                }
                $sessionToken = $sessTokenDecode->scretKey;


                /*
                 * Payment credit data processing for
                 * request (Json & XML Format ) store in payment table
                 */
                $paymentReqCreditInfo = [];
                $sl = 1;
                $creditInfo = '';
                foreach ($payment_details as $data) {
                    $singleCreditInfo = [];
                    $singleCreditInfo['SLNO'] = $sl;
                    $singleCreditInfo['CreditAccount'] = $data->receiver_ac_no;
                    $singleCreditInfo['CrAmount'] = $data->pay_amount;
                    $singleCreditInfo['Purpose'] = $data->purpose_sbl;
                    $singleCreditInfo['Onbehalf'] = $paymentInfo->contact_name;

                    $paymentReqCreditInfo[] = $singleCreditInfo;

                    // Credit info body for soap request
                    $creditInfo .= "<CreditInfo>
            <SLNO>$sl</SLNO>
            <CreditAccount>" . $data->receiver_ac_no . "</CreditAccount>
            <CrAmount>" . $data->pay_amount . "</CrAmount>
            <Purpose>" . $data->purpose_sbl . "</Purpose>
            <Onbehalf>" . $paymentInfo->contact_name . "</Onbehalf>
            </CreditInfo>";

                    $sl++;
                }

                // SOAP Request for payment initiate, which will store in payment table
                $body = "<html>
<head></head>
<body onload='document.forms[0].submit()'>
<form name='PostForm' method='POST' action=" . config('payment.spg_settings.spg_web_portal_url') . ">
<textarea name='datarequest' id='datarequest' rows='15' style='width:100%; display:none;'>
<SpsRequestByeChallan>
<RequestInformation>
 <Authentication>
<ApiAccessUserId>" . $user_id . "</ApiAccessUserId>
<AuthenticationKey>" . $sessionToken . "</AuthenticationKey>
 </Authentication>
 <ReferenceInfo>
<RequestId>" . $send_request_id . "</RequestId>
<RefTranNo>" . $paymentInfo->ref_tran_no . "</RefTranNo>
<RefTranDateTime>" . $send_ref_tran_date_time . "</RefTranDateTime>
<ReturnUrl>" . $payment_callback_url . "</ReturnUrl>
<ReturnMethod>POST</ReturnMethod>
<TranAmount>" . ($paymentInfo->pay_amount + $paymentInfo->vat_on_pay_amount) . "</TranAmount>
<ContactName>" . $paymentInfo->contact_name . "</ContactName>
<ContactNo>" . $paymentInfo->contact_no . "</ContactNo>
<PayerId>" . $paymentInfo->id . "</PayerId>
<Address>" . $paymentInfo->address . "</Address>
 </ReferenceInfo>
 
  <CreditInformation>" . $creditInfo . "</CreditInformation>
 
</RequestInformation>
</SpsRequestByeChallan>
</textarea></form>
</body>
</html>";

                // payment request info save in JSON
                $paymentReqInfo['ApiAccessUserId'] = $user_id;
                $paymentReqInfo['AuthenticationKey'] = $sessionToken;
                $paymentReqInfo['RequestId'] = $send_request_id;
                $paymentReqInfo['RefTranNo'] = $paymentInfo->ref_tran_no;
                $paymentReqInfo['RefTranDateTime'] = $send_ref_tran_date_time;
                $paymentReqInfo['ReturnUrl'] = $payment_callback_url;
                $paymentReqInfo['ReturnMethod'] = 'POST';
                $paymentReqInfo['TranAmount'] = $paymentInfo->pay_amount + $paymentInfo->vat_on_pay_amount;
                $paymentReqInfo['ContactName'] = $paymentInfo->contact_name;
                $paymentReqInfo['ContactNo'] = $paymentInfo->contact_no;
                $paymentReqInfo['PayerId'] = $paymentInfo->id;
                $paymentReqInfo['Address'] = $paymentInfo->address;
                $paymentReqInfo['CreditInformation'] = json_encode($paymentReqCreditInfo);

                $paymentInfo->payment_request = json_encode($paymentReqInfo);
                $paymentInfo->payment_request_xml = $body;
                //$paymentInfo->receiver_ac_no = config('payment.spg_settings.spg_SBL_account');
                $paymentInfo->request_id = $send_request_id;
                $paymentInfo->payment_date = $send_payment_date;
                $paymentInfo->ref_tran_date_time = $send_ref_tran_date_time;
                $paymentInfo->spg_redirect_no += 1;
                $paymentInfo->save();

                //DB::commit();
                return view('SonaliPayment::paymentRequestMultiple',
                    compact('sessionToken','paymentInfo', 'payment_details'));
            } else {
//                DB::rollback();
                Session::flash('error', 'Invalid request! [SPM-104]');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            //DB::rollback();
            Session::flash('error', 'Something went wrong! . Error: ' . $e->getMessage() . '[SPM-105]');
            return redirect()->back();
        }
    }

    /*
     * Multiple Payment request callback
     */
    public function callbackMultiple(Request $request)
    {
        try {
            $responseData = $request->get('Request');
            $xml = simplexml_load_string($responseData);
            if ($xml === false) {
                Session::flash('error', 'Payment request callback error. 
                Please contact with Support team with your application tracking number. [SPM-146]');
                return redirect('dashboard');
            }

            $transaction_id = $xml->TransactionId->__toString();
            $refTranNo = $xml->RefTranNo->__toString();
            //$refTranDateTime = $xml->RefTranDateTime->__toString();
            //$TranAmount = $xml->TranAmount->__toString();
            //$PayAmount = $xml->PayAmount->__toString();
            //$vat = $xml->Vat->__toString();
            //$commission = $xml->Commission->__toString();
            //$request_id = $xml->RefTranNo->__toString();
            $transaction_status = $xml->TransactionStatus->__toString();
            $pay_mode_code = $xml->PayMode->__toString();

            //$payment_mode = 'online';
            //if ($pay_mode_code == 'A01') {
            //$payment_mode = 'offline'; //counter based payment
            //}

            $payment_mode = CommonFunction::getPaymentModeCodeMsg($pay_mode_code);

            $sonaliPayment = SonaliPayment::where('ref_tran_no', $refTranNo)
//                ->where('ref_tran_date_time', $refTranDateTime)
                ->first();

            // get process info for URL redirection
            $process_info = ProcessType::where('id', $sonaliPayment->process_type_id)->first();

//            $sonaliPayment->transaction_charge_amount = $PayAmount - $TranAmount;
//            $sonaliPayment->vat_on_transaction_charge = 0; // Sonali bank did not send the vat amount
            $sonaliPayment->pay_mode = $payment_mode['pay_mode_msg'];
            $sonaliPayment->pay_mode_code = $pay_mode_code;
//            $sonaliPayment->transaction_id = $transaction_id;
//            $sonaliPayment->sender_ac_no = 'Not given by SBL';
//            $sonaliPayment->payment_status = ($transaction_status == 200) ? 1 : (($pay_mode_code == 'A01') ? 3 : -2);
            $sonaliPayment->payment_response_xml = $responseData;
            $sonaliPayment->payment_response = json_encode($xml);

            // If counter payment then save status and redirect to after counter payment method
            if ($transaction_status == '5017' && $pay_mode_code == 'A01') {

                //$sonaliPayment->payment_status = $pay_mode_code == 'A01' ? 3 : -2;
                $sonaliPayment->payment_status = 3; // Waiting for Payment Confirmation
                $sonaliPayment->save();
                //DB::commit();
                return redirect($process_info->form_url . '/afterCounterPayment/' . Encryption::encodeId($sonaliPayment->id));
            }

            $sonaliPayment->save();


            /*
            * $transaction_status (5555) is payment cancellation status.
             * Could not process request. System Error.
            */
            if ($transaction_status == '5555') {
                Session::flash('error', 'Payment cancelled by user. [SPM-147]');
                return redirect('dashboard');
            }


            //DB::beginTransaction();


            // if transaction status == 200 (success) then check Transaction Verification
            if ($transaction_status == '200' || $transaction_status == 200) {
                //$verifyStatus = $this->transactionVerificationMultiple($sonaliPayment->id);
                $verifyStatus = $this->transactionVerificationWithRefNo($sonaliPayment->id);

                /*
                 * if transaction verification is ok, then redirect to corresponding controller
                 * for next modification/update on the payment.
                 */
                if (isset($verifyStatus['code']) && ($verifyStatus['code'] == '200' || $verifyStatus['code'] == 200)) {

                    // Single payment distribution verify
                    //$this->singlePaymentDetailsVerification($sonaliPayment->id);

                    //$sonaliPayment->save();
                    //DB::commit();
                    return redirect($process_info->form_url . '/afterPayment/' . Encryption::encodeId($sonaliPayment->id));
                }

                // if verification response is false then redirect with message
                //$sonaliPayment->save();
                //DB::commit();
                return redirect($process_info->form_url . '/list/' . Encryption::encodeId($process_info->id))
                    ->with('success', 'Your payment has been successfully submitted to the Sonali Bank, 
                    but the verification was not completed. Please contact the system support team
                    with this Transaction ID : ' . $transaction_id . '. [SPM-134]');
            }

//            else {
//                /*
//                 * if transaction status is 5017 and payment mode is 'A01'
//                 * then go to afterCounterPayment function to corresponding module
//                 */
//                if ($transaction_status == '5017' && $pay_mode_code == 'A01') {
//
//                    $sonaliPayment->payment_status = (($pay_mode_code == 'A01') ? 3 : -2);
//                    $sonaliPayment->save();
//                    DB::commit();
//                    return redirect($process_info->form_url . '/afterCounterPayment/' . Encryption::encodeId($sonaliPayment->id));
//                }
//            }
//

            //$sonaliPayment->save();
            //DB::commit();
            // redirect to corresponding application list with payment failure message
            return redirect($process_info->form_url . '/list/' . Encryption::encodeId($process_info->id))
                ->with('error', "Payment submission error. Please contact with system support team 
                with this Transaction ID : " . $transaction_id . " and Transaction Status : " . $transaction_status . " [SPM-134]");
        } catch (\Exception $e) {
            //DB::rollback();
            Session::flash('error', 'Payment callback error. Message : ' . $e->getMessage() . '[SP-132]');
            return redirect('dashboard');
        }
    }


    public function transactionVerificationWithRefNo($id)
    {
        try {
            $web_service_url = config('payment.spg_settings.spg_web_service_url');
            $user_id = config('payment.spg_settings.spg_user_id');
            $password = config('payment.spg_settings.spg_password');
            $ownerCode = config('payment.spg_settings.st_code');

            $paymentInfo = SonaliPayment::find($id);
            if (empty($paymentInfo)) {
                Session::flash('error', 'Invalid payment info [SP-150]');
                return false;
            }

            // Processing verification request as XML to store in process table and curl request
            $body = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Header>
    <SpgUserCredentials xmlns="http://tempuri.org/">
      <userName>' . $user_id . '</userName>
      <password>' . $password . '</password>
    </SpgUserCredentials>
  </soap:Header>
  <soap:Body>
    <TransactionVerificationWithRefNo xmlns="http://tempuri.org/">
      <OwnerCode>' . $ownerCode . '</OwnerCode>
      <RefNo>' . $paymentInfo->ref_tran_no . '</RefNo>
      <isEncPwd>true</isEncPwd>
    </TransactionVerificationWithRefNo>
  </soap:Body>
</soap:Envelope>';


            // Processing Verification  request as Json to store in payment table
            $payVerificationReqInfo['userName'] = $user_id;
            $payVerificationReqInfo['password'] = $password;
            $payVerificationReqInfo['OwnerCode'] = $ownerCode;
            $payVerificationReqInfo['RefNo'] = $paymentInfo->ref_tran_no;
            $payVerificationReqInfo['isEncPwd'] = 'true';
            $payVerificationReqInfoJSON = json_encode($payVerificationReqInfo);

            $paymentInfo->verification_request_xml = $body;
            $paymentInfo->verification_request = $payVerificationReqInfoJSON;
            $paymentInfo->save();

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $web_service_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: text/xml;charset=utf-8",
                'SOAPAction: "http://tempuri.org/TransactionVerificationWithRefNo"'
            ));
            $result = curl_exec($ch);

            if (curl_errno($ch)) {
                Session::flash('error', 'Request Error: ' . curl_error($ch) . '[SP-151]');
                return false;
            }
            $xml_parser = xml_parser_create();
            xml_parse_into_struct($xml_parser, $result, $val);
            $responseData = json_decode($val[3]['value']);

            // Verification request & response store JSON & XML format in Payment table
            $paymentInfo->verification_response_xml = $result;
            $paymentInfo->verification_response = json_encode($responseData);

            $paymentInfo->status_code = $responseData->StatusCode;
            $paymentInfo->transaction_id = $responseData->TransactionId;
            $payment_mode = CommonFunction::getPaymentModeCodeMsg($responseData->PayMode);
            $paymentInfo->pay_mode = $payment_mode['pay_mode_msg'];
            $paymentInfo->pay_mode_code = $responseData->PayMode;

            // if verification success then store payment info
            if (!empty($responseData->TransactionId) && ($responseData->StatusCode == '200' || $responseData->StatusCode == 200)) {

                $paymentInfo->transaction_charge_amount = $responseData->Commission;
                $paymentInfo->vat_on_transaction_charge = $responseData->Vat;
                $paymentInfo->total_amount = ($responseData->Commission + $responseData->Vat + $responseData->TranAmount);
                $paymentInfo->sender_ac_no = 'Not given by SBL';
                $paymentInfo->is_verified = 1;
                $paymentInfo->payment_status = 1;
                $paymentInfo->spg_trans_date_time = $responseData->TransactionDate;
                $paymentInfo->save();

                return [
                    'status' => true,
                    'code' => $responseData->StatusCode,
                    'message' => 'Verification success.',
                ];
            }

            $paymentInfo->save();
            Session::flash('error', 'Invalid verification response [SP-152]');

            return [
                'status' => false,
                'code' => $responseData->StatusCode,
                'message' => 'Verification failed.',
            ];

        } catch (\Exception $e) {
            Session::flash('error', 'Something went wrong. Error:' . $e->getMessage() . '[SP-153]');
            return false;
        }
    }


    /*
     * Transaction Verification by Reference Number
     */
    public function verifyTransactionByRefNo($id)
    {
        if (!ACL::getAccsessRight('SonaliPayment', '-E-')) {
            die('You have no access right! Please contact system administration for more information.');
        }

        try {

            DB::beginTransaction();
            $paymentId = Encryption::decodeId($id);
            // Payment verify by reference number and payment status update if not done yet.
            $verificationStatus = $this->transactionVerificationWithRefNo($paymentId);
            if (isset($verificationStatus['code']) && $verificationStatus['code'] == '200') {
                Session::flash('success', 'Payment verification successful. [SPC-154]');
            }
            DB::commit();

            // Error Session message will be set from transactionVerification() function
            return redirect('/spg');
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . "[SPC-156]");
            return redirect()->back();
        }
    }


    /*
     * Transaction Verification and application complete by user action
     */
    public function verifyAndComplete($id)
    {
        if (!ACL::getAccsessRight('SonaliPayment', '-E-')) {
            die('You have no access right! Please contact system administration for more information.');
        }

        try {
            DB::beginTransaction();
            $paymentId = Encryption::decodeId($id);
            // get payment info
            $paymentInfo = SonaliPayment::where('id', $paymentId)->first();
            if ($paymentInfo->payment_status != 1) {
                DB::rollback();
                Session::flash('error', 'Payment not successful yet. [SPC-1085]');
                return redirect('/spg');
            }

            // Get application process information
            $process_data = ProcessList::where('process_type_id', $paymentInfo->process_type_id)
                ->where('ref_id', $paymentInfo->app_id)
                ->first();
            if ($process_data->desk_id != 0) {
                DB::rollback();
                Session::flash('error', "The application is not in applicant desk. [SPC-1218]");
                return redirect()->back();
            }

            // get process info for URL redirection
            $process_info = ProcessType::where('id', $paymentInfo->process_type_id)->first();

            //$verificationStatus = $this->transactionVerification($paymentId);
            $verificationStatus = $this->transactionVerificationWithRefNo($paymentId);

            if (isset($verificationStatus['code']) && ($verificationStatus['code'] == '200' || $verificationStatus['code'] == 200)) {
                DB::commit();

                //Session::put('paymentVerifyBySystemUser', 'paymentVerifyBySystemUser');

                // A01 = Counter payment
                if ($paymentInfo->pay_mode_code == 'A01') {
                    return redirect($process_info->form_url . '/afterCounterPayment/' . Encryption::encodeId($paymentInfo->id));
                }

                // Single payment distribution verify
                //$this->singlePaymentDetailsVerification($paymentInfo->id);
                Session::put('payment.verifyComplete', true);
                Session::put('payment.updated_by', $paymentInfo->created_by);
                return redirect($process_info->form_url . '/afterPayment/' . Encryption::encodeId($paymentInfo->id));
            }
            DB::commit();

            // Session message will be set from transactionVerification() function
            return redirect('/spg');
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . "[SPC-1215]");
            return redirect()->back();
        }
    }


    /*
     * Counter Payment Check by Applicant
     */
    public function counterPaymentCheck($paymentId = null, $status = null)
    {
        if (!ACL::getAccsessRight('SonaliPayment', '-CPCR-')) {
            die('You have no access right! Please contact system administration for more information.');
        }

        try {
            DB::beginTransaction();
            $decodedStatus = Encryption::decodeId($status);
            $decodedPaymentId = Encryption::decodeId($paymentId);
            if ($paymentId != null) {

                $sonaliPayment = SonaliPayment::find($decodedPaymentId);
                if (empty($sonaliPayment)) {
                    Session::flash('error', 'Invalid payment info [SP-1599]');
                    return redirect()->back();
                }

                //$verifyStatus = $this->offlinePaymentVerify($paymentId);
                $verifyStatus = $this->verifyByRefNoForCounter($decodedPaymentId);

                /*
                 * if payment verification response is true
                 * then do necessary steps to transfer the application to submit status
                 */
                if ($decodedStatus == 1 && $verifyStatus == true) { // payment confirm = 1

                    // Single payment distribution verify
                    //$this->singlePaymentDetailsVerification($sonaliPayment->id);

                    // get process info for URL redirection
                    $process_info = ProcessType::where('id', $sonaliPayment->process_type_id)->first();
                    DB::commit();
                    return redirect($process_info->form_url . '/afterCounterPayment/' . Encryption::encodeId($sonaliPayment->id));
                }


                /*
                 * if user want to cancel counter payment
                 */
                if ($decodedStatus == 0) { // payment cancel = 0
                    if ($verifyStatus == true) {
                        DB::commit();
                        Session::flash('error', 'Your payment is already received by Sonali Bank.');
                        return redirect()->back();
                    }
                    $processInfo = ProcessList::where([
                        'ref_id' => $sonaliPayment->app_id,
                        'process_type_id' => $sonaliPayment->process_type_id,
                        'tracking_no' => $sonaliPayment->app_tracking_no,
                    ])->first();

                    if ($sonaliPayment->payment_category_id == 1 || $sonaliPayment->payment_category_id == 3) {
                        $sonaliPayment->payment_status = '-3'; // Payment Cancel Status
                        $processInfo->status_id = -1;
                    } elseif ($sonaliPayment->payment_category_id == 2) {
                        $sonaliPayment->payment_status = 0;
                        $processInfo->status_id = 15;
                    }

                    $sonaliPayment->save();
                    $processInfo->save();
                    Session::flash('success', 'Payment cancelled successfully');
                }

                // Error Session message will come from offlinePaymentVerify() function
                DB::commit();
                return redirect()->back();
            }

            DB::commit();
            Session::flash('error', 'Invalid payment id [SPC-111]');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', 'Something went wrong! Error:' . $e->getMessage() . ' [SPC-121]');
            return redirect()->back();
        }
    }


    public function verifyByRefNoForCounter($id)
    {
        try {

            $web_service_url = config('payment.spg_settings.spg_web_service_url');
            $user_id = config('payment.spg_settings.spg_user_id');
            $password = config('payment.spg_settings.spg_password');
            $ownerCode = config('payment.spg_settings.st_code');

            $paymentInfo = SonaliPayment::find($id);
            if (empty($paymentInfo)) {
                Session::flash('error', 'Invalid payment info [SP-159]');
                return false;
            }

            // Processing verification request as XML to store in process table and curl request
            $body = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Header>
    <SpgUserCredentials xmlns="http://tempuri.org/">
      <userName>' . $user_id . '</userName>
      <password>' . $password . '</password>
    </SpgUserCredentials>
  </soap:Header>
  <soap:Body>
    <TransactionVerificationWithRefNo xmlns="http://tempuri.org/">
      <OwnerCode>' . $ownerCode . '</OwnerCode>
      <RefNo>' . $paymentInfo->ref_tran_no . '</RefNo>
      <isEncPwd>true</isEncPwd>
    </TransactionVerificationWithRefNo>
  </soap:Body>
</soap:Envelope>';


            // Processing Verification  request as Json to store in payment table
            $payVerificationReqInfo['userName'] = $user_id;
            $payVerificationReqInfo['password'] = $password;
            $payVerificationReqInfo['OwnerCode'] = $ownerCode;
            $payVerificationReqInfo['RefNo'] = $paymentInfo->ref_tran_no;
            $payVerificationReqInfo['isEncPwd'] = 'true';
            $payVerificationReqInfoJSON = json_encode($payVerificationReqInfo);

            $paymentInfo->verification_request_xml = $body;
            $paymentInfo->verification_request = $payVerificationReqInfoJSON;
            $paymentInfo->save();

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $web_service_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: text/xml;charset=utf-8",
                'SOAPAction: "http://tempuri.org/TransactionVerificationWithRefNo"'
            ));
            $result = curl_exec($ch);

            if (curl_errno($ch)) {
                Session::flash('error', 'Request Error: ' . curl_error($ch) . '[SP-160]');
                return false;
            }
            $xml_parser = xml_parser_create();
            xml_parse_into_struct($xml_parser, $result, $val);
            $responseData = json_decode($val[3]['value']);


            // Verification request & response store JSON & XML format in Payment table
            $paymentInfo->verification_response_xml = $result;
            $paymentInfo->verification_response = json_encode($responseData);

            $paymentInfo->status_code = $responseData->StatusCode;
            $paymentInfo->transaction_id = $responseData->TransactionId;
            $payment_mode = CommonFunction::getPaymentModeCodeMsg($responseData->PayMode);
            $paymentInfo->pay_mode = $payment_mode['pay_mode_msg'];
            $paymentInfo->pay_mode_code = $responseData->PayMode;

            if (!empty($responseData->TransactionId) && $responseData->StatusCode == '200') {

                $paymentInfo->transaction_charge_amount = $responseData->Commission;
                $paymentInfo->vat_on_transaction_charge = $responseData->Vat;
                $paymentInfo->total_amount = ($responseData->Commission + $responseData->Vat + $responseData->TranAmount);
                $paymentInfo->sender_ac_no = 'Not given by SBL';
                $paymentInfo->is_verified = 1;
                $paymentInfo->payment_status = 1;
                $paymentInfo->spg_trans_date_time = $responseData->TransactionDate;
                $paymentInfo->save();
                return true;
            }
            $paymentInfo->save();
//            Session::flash('error', 'Invalid verification response [SP-161]');

            return false;
        } catch (\Exception $e) {
            Session::flash('error', 'Something went wrong. Error:' . $e->getMessage() . '[SP-162]');
            return false;
        }
    }


    /*
     * Counter payment voucher
     */
    public function paymentVoucher($paymentId = null)
    {
        if (empty($paymentId)) {
            Session::flash('error', 'Invalid payment id [SPC-113]');
            return redirect()->back();
        }

        try {

            $decodedPaymentId = Encryption::decodeId($paymentId);
            $paymentInfo = SonaliPayment::where('id', $decodedPaymentId)->first();
            $companyName = CompanyInfo::leftJoin('process_list', 'process_list.company_id', '=', 'company_info.id')
                ->where('process_list.process_type_id', $paymentInfo->process_type_id)
                ->where('process_list.ref_id', $paymentInfo->app_id)
                ->pluck('company_name');

            if (in_array($paymentInfo->process_type_id, [1, 10, 2, 3, 4, 5])) {
                $emp_info = $this->getEmployeeInfo($paymentInfo->process_type_id, $paymentInfo->app_id);
            }

            $mobile_number = Configuration::where('caption', 'SP_PAYMENT_HELPLINE_VOUCHER')->first();

            $dn1d = new DNS1D();
            $trackingNo = $paymentInfo->app_tracking_no; // tracking no push on barcode.
            if (!empty($trackingNo)) {
                $barcode = $dn1d->getBarcodePNG($trackingNo, 'C39');
                $barcode_url = 'data:image/png;base64,' . $barcode;
            } else {
                $barcode_url = '';
            }

            $contents = view("SonaliPayment::paymentVoucher-pdf",
                compact('decodedPaymentId', 'paymentInfo', 'barcode_url', 'companyName', 'emp_info'))->render();

            $mpdf = new mPDF([
                'utf-8', // mode - default ''
                'A4', // format - A4, for example, default ''
                10, // font size - default 0
                'dejavusans', // default font family
                10, // margin_left
                10, // margin right
                10, // margin top
                10, // margin bottom
                10, // margin header
                10, // margin footer
                'P'
            ]);
            // $mpdf->Bookmark('Start of the document');
            $mpdf->useSubstitutions;
            $mpdf->SetProtection(array('print'));
            $mpdf->SetDefaultBodyCSS('color', '#000');
            $mpdf->SetTitle("BIDA One Stop Service");
            $mpdf->SetSubject("Payment voucher");
            $mpdf->SetAuthor("Business Automation Limited");
            $mpdf->autoScriptToLang = true;
            $mpdf->baseScript = 1;
            $mpdf->autoVietnamese = true;
            $mpdf->autoArabic = true;

            $mpdf->autoLangToFont = true;
            $mpdf->SetDisplayMode('fullwidth');
            $mpdf->SetHTMLFooter('
                    <table width="100%">
                        <tr>
                            <td width="50%"><i style="font-size: 10px;">Download time: {DATE j-M-Y h:i a}</i></td>
                            <td width="50%" align="right"><i style="font-size: 10px;">Help line: ' . $mobile_number->value . '</i></td>
                        </tr>
                    </table>');
            $stylesheet = file_get_contents('assets/stylesheets/appviewPDF.css');
            $mpdf->setAutoTopMargin = 'stretch';
            $mpdf->setAutoBottomMargin = 'stretch';

            if ($paymentInfo->status_code == 200) {
                $mpdf->SetWatermarkImage('assets/images/paid.png', 1, [36, 35], [80, 70]);
                $mpdf->showWatermarkImage = true;
            }

            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->WriteHTML($contents, 2);

            $mpdf->defaultfooterfontsize = 10;
            $mpdf->defaultfooterfontstyle = 'B';
            $mpdf->defaultfooterline = 0;

            $mpdf->SetCompression(true);
            $mpdf->Output($paymentInfo->app_tracking_no . '.pdf', 'I');

        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [SPC-117]');
            return Redirect::back()->withInput();
        }
    }


    /*
     * Counter payment voucher
     */
    public function counterPaymentVoucher($paymentId = null)
    {
        if (empty($paymentId)) {
            Session::flash('error', 'Invalid payment id [SPC-112]');
            return redirect()->back();
        }

        try {

            $decodedPaymentId = Encryption::decodeId($paymentId);
            $paymentInfo = SonaliPayment::where('id', $decodedPaymentId)->first();
            $companyName = CompanyInfo::leftJoin('process_list', 'process_list.company_id', '=', 'company_info.id')
                ->where('process_list.process_type_id', $paymentInfo->process_type_id)
                ->where('process_list.ref_id', $paymentInfo->app_id)
                ->pluck('company_name');

            if (in_array($paymentInfo->process_type_id, [1, 10, 2, 3, 4, 5])) {
                $emp_info = $this->getEmployeeInfo($paymentInfo->process_type_id, $paymentInfo->app_id);
            }

            $mobile_number = Configuration::where('caption', 'SP_PAYMENT_HELPLINE_VOUCHER')->first();

            $dn1d = new DNS1D();
            $trackingNo = $paymentInfo->app_tracking_no; // tracking no push on barcode.
            if (!empty($trackingNo)) {
                $barcode = $dn1d->getBarcodePNG($trackingNo, 'C39');
                $barcode_url = 'data:image/png;base64,' . $barcode;
            } else {
                $barcode_url = '';
            }

            $contents = view("SonaliPayment::counterVoucher-pdf",
                compact('decodedPaymentId', 'paymentInfo', 'barcode_url', 'companyName','emp_info'))->render();

            $mpdf = new mPDF([
                'utf-8', // mode - default ''
                'A4', // format - A4, for example, default ''
                5, // font size - default 0
                'dejavusans', // default font family
                8, // margin_left
                8, // margin right
                8, // margin top
                8, // margin bottom
                8, // margin header
                8, // margin footer
                'P'
            ]);
            // $mpdf->Bookmark('Start of the document');
            $mpdf->useSubstitutions;
            $mpdf->SetProtection(array('print'));
            $mpdf->SetDefaultBodyCSS('color', '#000');
            $mpdf->SetTitle("BIDA One Stop Service");
            $mpdf->SetSubject("Subject");
            $mpdf->SetAuthor("Business Automation Limited");
            $mpdf->autoScriptToLang = true;
            $mpdf->baseScript = 1;
            $mpdf->autoVietnamese = true;
            $mpdf->autoArabic = true;

            $mpdf->autoLangToFont = true;
            $mpdf->SetDisplayMode('fullwidth');
            $mpdf->SetHTMLFooter('
                    <table width="100%">
                        <tr>
                            <td width="50%"><i style="font-size: 10px;">Download time: {DATE j-M-Y h:i a}</i></td>
                            <td width="50%" align="right"><i style="font-size: 10px;">Help line: ' . $mobile_number->value . '</i></td>
                        </tr>
                    </table>');
            $stylesheet = file_get_contents('assets/stylesheets/appviewPDF.css');
            $mpdf->setAutoTopMargin = 'stretch';
            $mpdf->setAutoBottomMargin = 'stretch';

            if ($paymentInfo->status_code == 200) {
                $mpdf->SetWatermarkImage('assets/images/paid.png', 1, [35, 34], [80, 200]);
                $mpdf->showWatermarkImage = true;
            }

            $mpdf->WriteHTML($stylesheet, 1);

            $mpdf->WriteHTML($contents, 2);

            $mpdf->defaultfooterfontsize = 10;
            $mpdf->defaultfooterfontstyle = 'B';
            $mpdf->defaultfooterline = 0;

            $mpdf->SetCompression(true);
            $mpdf->Output($paymentInfo->app_tracking_no . '.pdf', 'I');

        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [SPC-114]');
            return Redirect::back()->withInput();
        }
    }

    /**
     * Get Parameter as JSON decoded
     * @param $getParam
     * @return mixed
     */
    function getParamValue($getParam)
    {
        $this->writeLog("Request", $getParam);
        return $returnArray = json_decode($getParam, true);
    }

    /**
     * Write Log in Local File
     * @param $type
     * @param $log
     */
    function writeLog($type, $log)
    {
        date_default_timezone_set('Asia/Dhaka');
        $fileName = storage_path() . '/logs/' . date("Ymd") . ".txt";
        //echo $fileName;die();
        $file = fopen($fileName, "a");
        if ($type == "Request") {
            fwrite($file, "\r### " . date("H:i:s") . "\t" . $type . ":" . $log);
        } else {
            fwrite($file, "\r###" . $type . ":" . $log);
        }
        fclose($file);
    }

    /*
     * IPN API method end
     */


    /*
     * Sonali payment initiate
     * This function for single account payment, so it is not used due to multiple destination account.
     */
    public static function initiatePayment($paymentId = null)
    {

        // This is single account payment system, so, it is commented due to unused

        if (config('payment.spg_settings.payment_mode') == 'off') {
            $ref_url = $_SERVER["HTTP_REFERER"];
            $url_sec = explode('/', $ref_url);
            $process_info = ProcessType::where('id', Encryption::decodeId($url_sec[7]))->first();
            $process_list = ProcessList::where('process_type_id', Encryption::decodeId($url_sec[7]))
                ->where('ref_id', Encryption::decodeId($url_sec[6]))->first();

            $paymentId = Encryption::decodeId($paymentId);
            $sonaliPayment = SonaliPayment::where('id', $paymentId)->first();

//            if ($process_list->status_id == -1){
//                $sonaliPayment->payment_category_id = 1;
//            }else{
//                $sonaliPayment->payment_category_id = 2;
//            }


            $sonaliPayment->payment_status = 1;
            $sonaliPayment->app_id = Encryption::decodeId($url_sec[6]);
            $sonaliPayment->process_type_id = Encryption::decodeId($url_sec[7]);
            $sonaliPayment->save();
            return redirect($process_info->form_url . '/afterPayment/' . Encryption::encodeId($paymentId));
        }

        try {

            if ($paymentId != null) {
                DB::begintransaction();
                $web_service_url = config('payment.spg_settings.spg_web_service_url');
                $user_id = config('payment.spg_settings.spg_user_id');
                $password = config('payment.spg_settings.spg_password');
                $sblAccount = config('payment.spg_settings.spg_SBL_account');

                $decodePaymentId = Encryption::decodeId($paymentId);
                $paymentInfo = SonaliPayment::find($decodePaymentId);
                if (empty($paymentInfo)) {
                    DB::rollback();
                    Session::flash('error', 'Payment record not found! [SP-102]');
                    return redirect()->back();
                }

                $body = '<?xml version="1.0" encoding="utf-8"?>
                 <soap:Envelope 
                 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                 xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
                 xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                    <soap:Header>
                        <SpgUserCredentials xmlns="http://tempuri.org/">
                            <userName>' . $user_id . '</userName>
                            <password>' . $password . '</password>
                        </SpgUserCredentials>
                    </soap:Header>
                    <soap:Body>
                        <GetSessionKey xmlns="http://tempuri.org/">
                            <strUserId>' . $user_id . '</strUserId>
                            <strPassKey>' . $password . '</strPassKey>
                            <strRequestId>' . $paymentInfo->request_id . '</strRequestId>
                            <strAmount>' . $paymentInfo->pay_amount . '</strAmount>
                            <strTranDate>' . $paymentInfo->payment_date . '</strTranDate>
                            <strAccounts>' . $sblAccount . '</strAccounts>
                        </GetSessionKey>
                    </soap:Body>
                </soap:Envelope>';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $web_service_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_HTTPHEADER,
                    array("Content-Type: text/xml;charset=utf-8", 'SOAPAction: "http://tempuri.org/GetSessionKey"'));
                $result = curl_exec($ch);
                if (curl_errno($ch)) {
                    DB::rollback();
                    Session::flash('error', 'Request Error: ' . curl_error($ch) . '[SP-139]');
                    return redirect()->back();
                }

                $xml_parser = xml_parser_create();
                xml_parse_into_struct($xml_parser, $result, $val);

                $getToken = $val[3]["value"];
                $sessTokenDecode = json_decode($getToken);
                if (!isset($sessTokenDecode)) {
                    DB::rollback();
                    Session::flash('error', 'Session token not found [SP-103]');
                    return redirect()->back();
                }
                $sessionToken = $sessTokenDecode->scretKey;

                // payment request info save in JSON
                $paymentReqInfo['ApiAccessUserId'] = config('payment.spg_settings.spg_user_id');
                $paymentReqInfo['AuthenticationKey'] = $sessionToken;
                $paymentReqInfo['RequestId'] = $paymentInfo->request_id;
                $paymentReqInfo['RefTranNo'] = $paymentInfo->ref_tran_no;
                $paymentReqInfo['RefTranDateTime'] = $paymentInfo->ref_tran_date_time;
                $paymentReqInfo['ReturnUrl'] = config('payment.spg_settings.return_url');
                $paymentReqInfo['ReturnMethod'] = 'POST';
                $paymentReqInfo['TranAmount'] = $paymentInfo->pay_amount;
                $paymentReqInfo['ContactName'] = $paymentInfo->contact_name;
                $paymentReqInfo['ContactNo'] = $paymentInfo->contact_no;
                $paymentReqInfo['PayerId'] = $paymentInfo->id;
                $paymentReqInfo['Address'] = $paymentInfo->address;
                $paymentReqInfo['SLNO'] = $paymentInfo->sl_no;
                $paymentReqInfo['CreditAccount'] = config('payment.spg_settings.spg_SBL_account');
                $paymentReqInfo['CrAmount'] = $paymentInfo->pay_amount;
                $paymentReqInfo['Purpose'] = 'Application fee';
                $paymentReqInfo['Onbehalf'] = $paymentInfo->contact_name;

                $paymentReqInfoJSON = json_encode($paymentReqInfo);
                $paymentInfo->payment_request = $paymentReqInfoJSON;
                $paymentInfo->receiver_ac_no = config('payment.spg_settings.spg_SBL_account');
                $body = "<html>
<head></head>
<body onload='document.forms[0].submit()'>
<form name='PostForm' method='POST' action=" . config('payment.spg_settings.spg_web_service_url') . ">
<textarea name='datarequest' id='datarequest' rows='15' style='width:100%; display:none;'>
<SpsRequestByeChallan>
<RequestInformation>
 <Authentication>
<ApiAccessUserId>" . config('payment.spg_settings.spg_user_id') . "</ApiAccessUserId>
<AuthenticationKey>" . $sessionToken . "</AuthenticationKey>
 </Authentication>
 <ReferenceInfo>
<RequestId>" . $paymentInfo->request_id . "</RequestId>
<RefTranNo>" . $paymentInfo->ref_tran_no . "</RefTranNo>
<RefTranDateTime>" . $paymentInfo->ref_tran_date_time . "</RefTranDateTime>
<ReturnUrl>" . config('payment.spg_settings.return_url') . "</ReturnUrl>
<ReturnMethod>POST</ReturnMethod>
<TranAmount>" . $paymentInfo->pay_amount . "</TranAmount>
<ContactName>" . $paymentInfo->contact_name . "</ContactName>
<ContactNo>" . $paymentInfo->contact_no . "</ContactNo>
<PayerId>" . $paymentInfo->id . "</PayerId>
<Address>" . $paymentInfo->address . "</Address>
 </ReferenceInfo>
 <CreditInformation>
<CreditInfo>
<SLNO>" . $paymentInfo->sl_no . "</SLNO>
<CreditAccount>" . config('payment.spg_settings.spg_SBL_account') . "</CreditAccount>
<CrAmount>" . $paymentInfo->pay_amount . "</CrAmount>
<Purpose>Application fee</Purpose>
<Onbehalf>" . $paymentInfo->contact_name . "</Onbehalf>
</CreditInfo>
 </CreditInformation>
</RequestInformation>
</SpsRequestByeChallan>
</textarea></form>
</body>
</html>";
                $paymentInfo->payment_request_xml = $body;
                $paymentInfo->save();

                DB::commit();
                return view('SonaliPayment::paymentRequest', compact('sessionToken','paymentInfo'));
            } else {
                Session::flash('error', 'Invalid request! [SPC-104]');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Something went wrong! . Error: ' . $e->getMessage() . '[SP-105]');
            return redirect()->back();
        }
    }


    /*
     * Payment request callback
     * This function for single account payment, so it is not used due to multiple destination account.
     */
    public function callback(Request $request)
    {
        try {
            $responseData = $request->get('Request');
            $xml = simplexml_load_string($responseData);
            if ($xml === false) {
                Session::flash('error', 'Payment request callback error. 
                Please contact with Support team with your application tracking number. [SP-146]');
                return redirect('dashboard');
            }

            $transaction_id = $xml->TransactionId->__toString();
            $refTranNo = $xml->RefTranNo->__toString();
            $refTranDateTime = $xml->RefTranDateTime->__toString();
            $TranAmount = $xml->TranAmount->__toString();
            $PayAmount = $xml->PayAmount->__toString();
            //$request_id = $xml->RefTranNo->__toString();
            $transaction_status = $xml->TransactionStatus->__toString();
            $pay_mode_code = $xml->PayMode->__toString();

            $payment_mode = 'online';
            if ($pay_mode_code == 'A01') {
                $payment_mode = 'offline'; //counter based payment
            }

            DB::beginTransaction();
            $sonaliPayment = SonaliPayment::where('ref_tran_no', $refTranNo)
                ->where('ref_tran_date_time', $refTranDateTime)
                ->first();
            $sonaliPayment->transaction_charge_amount = $PayAmount - $TranAmount;
            $sonaliPayment->vat_on_transaction_charge = 0; // Sonali bank did not send the vat amount

            $sonaliPayment->pay_mode = $payment_mode;
            $sonaliPayment->pay_mode_code = $pay_mode_code;
            $sonaliPayment->transaction_id = $transaction_id;
            $sonaliPayment->sender_ac_no = 'Not given by SBL';
            $sonaliPayment->payment_status = ($transaction_status == 200) ? 1 : (($pay_mode_code == 'A01') ? 3 : -2);
            $sonaliPayment->payment_response_xml = $responseData;
            $sonaliPayment->payment_response = json_encode($xml);
            $sonaliPayment->save();

            // get process info for URL redirection
            $process_info = ProcessType::where('id', $sonaliPayment->process_type_id)->first();


            // if transaction status == 200 (success) then check Transaction Verification
            if ($transaction_status == '200') {
                $verifyStatus = $this->transactionVerification($sonaliPayment->id);
                /*
                 * if transaction verification is ok, then redirect to corresponding controller
                 * for next modification/updation on the payment.
                 */
                if ($verifyStatus) {
                    DB::commit();
                    return redirect($process_info->form_url . '/afterPayment/' . Encryption::encodeId($sonaliPayment->id));
                }
            } else {
                if ($transaction_status == '5017' && $pay_mode_code == 'A01') {

                    /*
                     * if transaction status is 5017 and payment mode is 'A01'
                     * then go to afterCounterPayment function to corresponding module
                     */

//                if (!empty($TranAmount) && !empty($PayAmount)) {
//                    $sonaliPayment->transaction_charge_amount = ($TranAmount - $PayAmount);
//                    $sonaliPayment->vat_on_transaction_charge = 0;
//                    $sonaliPayment->save();
//                }

                    DB::commit();
                    return redirect($process_info->form_url . '/afterCounterPayment/' . Encryption::encodeId($sonaliPayment->id));
                }
            }

            DB::commit();
            // redirect to corresponding application list with payment failure message
            return redirect($process_info->form_url . '/list/' . Encryption::encodeId($process_info->id))->with('error',
                'Payment submission failed [SP-134]');
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', 'Payment callback error : ' . $e->getMessage() . '[SP-132]');
            return redirect($process_info->form_url . '/list/' . Encryption::encodeId($process_info->id));
        }
    }


    /*
     * Transaction Verification by user action
     * This function for single account payment, so it is not used due to multiple destination account.
     */
    public function verifyTransaction($id)
    {
        if (!ACL::getAccsessRight('SonaliPayment', '-E-')) {
            die('You have no access right! Please contact system administration for more information.');
        }

        try {

            DB::beginTransaction();
            $paymentId = Encryption::decodeId($id);
            $verificationStatus = $this->transactionVerification($paymentId);

            if ($verificationStatus) {
                Session::flash('success', 'Payment verification successful. [SPC-109]');
            }
            DB::commit();

            // Error Session message will be set from transactionVerification() function
            return redirect('/spg');
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . "[SPC-1214]");
            return redirect()->back();
        }
    }


//    public function verifyTransactionHistory($id = '', $hist_id = '')
//    {
//        if (!ACL::getAccsessRight('SonaliPayment', '-E-')) {
//            die('You have no access right! Please contact system administration for more information.');
//        }
//
//        try {
//            DB::beginTransaction();
//            $paymentId = Encryption::decodeId($id);
//            $historyId = Encryption::decodeId($hist_id);
//            $spPaymentInfo = SonaliPayment::find($paymentId);
//
//            $verificationStatus = $this->transactionHistoryVerification($historyId);
//
//            /*
//             * if verification is true and (
//             * payment->is_verified is not equal 1 or payment->payment_status is not equal 1)
//             * then, update verification data in payment table.
//             */
//            if ($verificationStatus && ($spPaymentInfo->is_verified != '1' or $spPaymentInfo->payment_status != '1')) {
//                $paymentHistoryInfo = SonaliPaymentHistory::find($historyId);
//
//                $spPaymentInfo->request_id = $paymentHistoryInfo->request_id;
//                $spPaymentInfo->ref_tran_date_time = $paymentHistoryInfo->ref_tran_date_time;
//                $spPaymentInfo->verification_request_xml = $paymentHistoryInfo->verification_request_xml;
//                $spPaymentInfo->verification_request = $paymentHistoryInfo->verification_request;
//                $spPaymentInfo->verification_response_xml = $paymentHistoryInfo->verification_response_xml;
//                $spPaymentInfo->verification_response = $paymentHistoryInfo->verification_response;
//                $spPaymentInfo->is_verified = 1;
//                $spPaymentInfo->payment_status = 1;
//                $spPaymentInfo->save();
//
//                DB::commit();
//                Session::flash('success', 'Payment verification successful. [SPC-109]');
//                return redirect('/spg');
//            } /*
//             * if verification is true only,
//             * then no need to update verification data in payment table.
//             */
//            elseif ($verificationStatus) {
//                DB::commit();
//                Session::flash('success', 'Payment verification successful. [SPC-109]');
//                return redirect('/spg');
//            }
//
//            DB::commit();
//            // Error Session message will be set from transactionHistoryVerification() function
//            return redirect()->back();
//        } catch (\Exception $e) {
//            DB::rollback();
//            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . "[SPC-1214]");
//            return redirect()->back();
//        }
//    }


    /*
     * Transaction Verification
     *
     * Now we use verification by reference function in all.
     * This function for single account payment, so it is not used due to multiple destination account.
     */
    public function transactionVerification($id)
    {
        try {
            $web_service_url = config('payment.spg_settings.spg_web_service_url');
            $user_id = config('payment.spg_settings.spg_user_id');
            $password = config('payment.spg_settings.spg_password');
            $ownerCode = config('payment.spg_settings.st_code');

            $paymentInfo = SonaliPayment::find($id);
            if (empty($paymentInfo)) {
                Session::flash('error', 'Invalid payment info [SP-136]');
                return false;
            }

            // Processing verification  request as XML format to store in payment table and curl body
            $body = '<?xml version="1.0" encoding="utf-8"?>
                    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                    <soap:Header>
                    <SpgUserCredentials xmlns="http://tempuri.org/">
                            <userName>' . $user_id . '</userName>
                            <password>' . $password . '</password>
                    </SpgUserCredentials>
                    </soap:Header>
                    <soap:Body>
                    <TransactionVerification
                    xmlns="http://tempuri.org/">
                    <OwnerCode>' . $ownerCode . '</OwnerCode>
                    <ReferenceDate>' . date('Y-m-d', strtotime($paymentInfo->ref_tran_date_time)) . '</ReferenceDate>
                    <RequiestNo>' . $paymentInfo->request_id . '</RequiestNo>
                    <isEncPwd>true</isEncPwd>
                    </TransactionVerification>
                    </soap:Body>
                    </soap:Envelope>';

            // Processing verification  request as Json format to store in payment table
            $payVerificationReqInfo['userName'] = $user_id;
            $payVerificationReqInfo['password'] = $password;
            $payVerificationReqInfo['OwnerCode'] = $ownerCode;
            $payVerificationReqInfo['ReferenceDate'] = date('Y-m-d', strtotime($paymentInfo->ref_tran_date_time));
            $payVerificationReqInfo['RequiestNo'] = $paymentInfo->request_id;
            $payVerificationReqInfo['isEncPwd'] = 'true';

            $paymentInfo->verification_request_xml = $body;
            $paymentInfo->verification_request = json_encode($payVerificationReqInfo);
            $paymentInfo->save();

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $web_service_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: text/xml;charset=utf-8", 'SOAPAction: "http://tempuri.org/TransactionVerification"'
            ));
            $result = curl_exec($ch);

            if (curl_errno($ch)) {
                Session::flash('error', 'Request Error: ' . curl_error($ch) . '[SP-133]');
                return false;
            }
            $xml_parser = xml_parser_create();
            xml_parse_into_struct($xml_parser, $result, $val);
            $responseData = json_decode($val[3]['value']);

            // Verification request & response store JSON & XML format in Payment table
            $paymentInfo->verification_response_xml = $result;
            $paymentInfo->verification_response = json_encode($responseData);

            if (!empty($responseData->TransactionId) && $responseData->StatusCode == '200') {
                $paymentInfo->is_verified = 1;
                $paymentInfo->save();
                return true;
            }
            $paymentInfo->save();

            Session::flash('error', 'Invalid verification response [SP-138]');
            return false;
        } catch (\Exception $e) {
            Session::flash('error', 'Something went wrong. Error:' . $e->getMessage() . '[SP-138]');
            return false;
        }
    }


    /*
     * Multiple Sonali payment Transaction Verification
     * This function is not used due to parameter complexity, now we used only verify by reference no function for all type of transaction verify
     */
    public function transactionVerificationMultiple($id)
    {
        try {
            $web_service_url = config('payment.spg_settings.spg_web_service_url');
            $user_id = config('payment.spg_settings.spg_user_id');
            $password = config('payment.spg_settings.spg_password');
            $ownerCode = config('payment.spg_settings.st_code');

            $paymentInfo = SonaliPayment::find($id);
            if (empty($paymentInfo)) {
                Session::flash('error', 'Invalid payment info [SPM-136]');
                return false;
            }

            $body = '<?xml version="1.0" encoding="utf-8"?>
                    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                    <soap:Header>
                    <SpgUserCredentials xmlns="http://tempuri.org/">
                            <userName>' . $user_id . '</userName>
                            <password>' . $password . '</password>
                    </SpgUserCredentials>
                    </soap:Header>
                    <soap:Body>
                    <TransactionVerification
                    xmlns="http://tempuri.org/">
                    <OwnerCode>' . $ownerCode . '</OwnerCode>
                    <ReferenceDate>' . date('Y-m-d', strtotime($paymentInfo->ref_tran_date_time)) . '</ReferenceDate>
                    <RequiestNo>' . $paymentInfo->request_id . '</RequiestNo>
                    <isEncPwd>true</isEncPwd>
                    </TransactionVerification>
                    </soap:Body>
                    </soap:Envelope>';


            // Verification request store JSON & XML format in Payment table
            $payVerificationReqInfo['userName'] = $user_id;
            $payVerificationReqInfo['password'] = $password;
            $payVerificationReqInfo['OwnerCode'] = $ownerCode;
            $payVerificationReqInfo['ReferenceDate'] = date('Y-m-d', strtotime($paymentInfo->ref_tran_date_time));
            $payVerificationReqInfo['RequiestNo'] = $paymentInfo->request_id;
            $payVerificationReqInfo['isEncPwd'] = 'true';
            $payVerificationReqInfoJSON = json_encode($payVerificationReqInfo);

            $paymentInfo->verification_request_xml = $body;
            $paymentInfo->verification_request = $payVerificationReqInfoJSON;
            $paymentInfo->save();

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $web_service_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: text/xml;charset=utf-8", 'SOAPAction: "http://tempuri.org/TransactionVerification"'
            ));
            $result = curl_exec($ch);

            if (curl_errno($ch)) {
                Session::flash('error', 'Request Error: ' . curl_error($ch) . '[SP-133]');
                return false;
            }
            $xml_parser = xml_parser_create();
            xml_parse_into_struct($xml_parser, $result, $val);
            $responseData = json_decode($val[3]['value']);

            // Verification response store JSON & XML format in Payment table
            $paymentInfo->verification_response_xml = $result;
            $paymentInfo->verification_response = json_encode($responseData);

            if (!empty($responseData->TransactionId) && $responseData->StatusCode == '200') {
                $paymentInfo->is_verified = 1;
                $paymentInfo->save();
                return true;
            }
            $paymentInfo->save();

            Session::flash('error', 'Invalid verification response [SPM-138]');
            return false;
        } catch (\Exception $e) {
            Session::flash('error', 'Something went wrong. Error:' . $e->getMessage() . '[SPM-138]');
            return false;
        }
    }

    /*
     * Single payment details transaction verification singlePaymentDetailsVerification
     */
    public function singlePaymentDetailsVerification($id)
    {
        try {
            $web_service_url = config('payment.spg_settings.single_details_url');
            $user_id = config('payment.spg_settings.spg_user_id');
            $password = config('payment.spg_settings.spg_password');
            $ownerCode = config('payment.spg_settings.st_code');

            $paymentInfo = SonaliPayment::find($id);
            if (empty($paymentInfo)) {
                Session::flash('error', 'Invalid payment info [SPDV-136]');
                return false;
            }

            $body = "{\n\"AccessUser\":{\n\"userName\":\"$user_id\",\n\"password\":\"$password\"\n},\n\"OwnerCode\":\"$ownerCode\",\n\"ReferenceDate\":\"$paymentInfo->payment_date\",\n\"RequiestNo\":\"$paymentInfo->request_id\",\n\"isEncPwd\":true\n}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $web_service_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_ENCODING, "");
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "cache-control: no-cache", "content-type: application/json"
            ));

            $result = curl_exec($ch);

            if (curl_errno($ch)) {
                Session::flash('error', 'Details payment verification error: ' . curl_error($ch) . '[SPDV-133]');
                return false;
            }

            $result1 = json_decode($result);
            $result_json = json_decode($result1);

            foreach ($result_json as $key => $value) {

                $paymentDetailsInfo = PaymentDetails::where(['sp_payment_id' => $id, 'receiver_ac_no' => $value->TranAccount])->first();

                if (empty($paymentDetailsInfo)) {
                    Session::flash('error', 'Payment details info is not found. [SPDV-189]');
                    return false;
                }

                $paymentDetailsInfo->verification_request = $body;

                $paymentDetailsInfoJson['TransactionId'] = $value->TransactionId;
                $paymentDetailsInfoJson['TransactionDate'] = $value->TransactionDate;
                $paymentDetailsInfoJson['ReferenceDate'] = $value->ReferenceDate;
                $paymentDetailsInfoJson['ReferenceNo'] = $value->ReferenceNo;
                $paymentDetailsInfoJson['TranAccount'] = $value->TranAccount;
                $paymentDetailsInfoJson['TranAmount'] = $value->TranAmount;
                $paymentDetailsInfoJson['StatusCode'] = $value->StatusCode;

                $paymentDetailsInfo->verification_response = json_encode($paymentDetailsInfoJson);
                $paymentDetailsInfo->confirm_amount_sbl = $value->TranAmount;

                if (!empty($value->TransactionId) && $value->StatusCode == '200') {
                    $paymentDetailsInfo->is_verified = 1;
                }

                $paymentDetailsInfo->save();
            }

            return true;

        } catch (\Exception $e) {
            Session::flash('error', 'Something went wrong. Error:' . $e->getMessage() . '[SPDV-138]');
            return false;
        }
    }


    public function transactionHistoryVerification($id)
    {
        try {
            $web_service_url = config('payment.spg_settings.spg_web_service_url');
            $user_id = config('payment.spg_settings.spg_user_id');
            $password = config('payment.spg_settings.spg_password');
            $ownerCode = config('payment.spg_settings.st_code');

            $payment_history = SonaliPaymentHistory::find($id);
            if (empty($payment_history)) {
                Session::flash('error', 'Invalid payment info [SPM-136]');
                return false;
            }

            $body = '<?xml version="1.0" encoding="utf-8"?>
                    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                    <soap:Header>
                    <SpgUserCredentials xmlns="http://tempuri.org/">
                            <userName>' . $user_id . '</userName>
                            <password>' . $password . '</password>
                    </SpgUserCredentials>
                    </soap:Header>
                    <soap:Body>
                    <TransactionVerification
                    xmlns="http://tempuri.org/">
                    <OwnerCode>' . $ownerCode . '</OwnerCode>
                    <ReferenceDate>' . date('Y-m-d', strtotime($payment_history->ref_tran_date_time)) . '</ReferenceDate>
                    <RequiestNo>' . $payment_history->request_id . '</RequiestNo>
                    <isEncPwd>true</isEncPwd>
                    </TransactionVerification>
                    </soap:Body>
                    </soap:Envelope>';


            // Verification request store JSON & XML format in Payment table
            $payVerificationReqInfo['userName'] = $user_id;
            $payVerificationReqInfo['password'] = $password;
            $payVerificationReqInfo['OwnerCode'] = $ownerCode;
            $payVerificationReqInfo['ReferenceDate'] = date('Y-m-d', strtotime($payment_history->ref_tran_date_time));
            $payVerificationReqInfo['RequiestNo'] = $payment_history->request_id;
            $payVerificationReqInfo['isEncPwd'] = 'true';
            $payVerificationReqInfoJSON = json_encode($payVerificationReqInfo);

            $payment_history->verification_request_xml = $body;
            $payment_history->verification_request = $payVerificationReqInfoJSON;
            // TODO:: transaction info (id, pay_mode etc) update if previous info is empty
            $payment_history->save();

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $web_service_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: text/xml;charset=utf-8", 'SOAPAction: "http://tempuri.org/TransactionVerification"'
            ));
            $result = curl_exec($ch);

            if (curl_errno($ch)) {
                Session::flash('error', 'Request Error: ' . curl_error($ch) . '[SP-133]');
                return false;
            }
            $xml_parser = xml_parser_create();
            xml_parse_into_struct($xml_parser, $result, $val);
            $responseData = json_decode($val[3]['value']);

            // Verification response store JSON & XML format in Payment table
            $payment_history->verification_response_xml = $result;
            $payment_history->verification_response = json_encode($responseData);

            if (!empty($responseData->TransactionId) && $responseData->StatusCode == '200') {
                $payment_history->is_verified = 1;
                $payment_history->save();
                return true;
            }
            $payment_history->save();

            Session::flash('error', 'Invalid verification response [SPM-138]');
            return false;
        } catch (\Exception $e) {
            Session::flash('error', 'Something went wrong. Error:' . $e->getMessage() . '[SPM-138]');
            return false;
        }
    }


    /*
     * Offline payment verification
     */
    public function offlinePaymentVerify($paymentId)
    {
        try {
            $web_service_url = config('payment.spg_settings.spg_web_service_url');
            $user_id = config('payment.spg_settings.spg_user_id');
            $password = config('payment.spg_settings.spg_password');
            $ownerCode = config('payment.spg_settings.st_code');

            $decodedPaymentId = Encryption::decodeId($paymentId);
            $paymentInfo = SonaliPayment::find($decodedPaymentId);

            if (empty($paymentInfo)) {
                Session::flash('error', 'Invalid payment info. [SPC-115]');
                return false;
            }

            $body = '<?xml version="1.0" encoding="utf-8"?>
                    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                    <soap:Header>
                    <SpgUserCredentials xmlns="http://tempuri.org/">
                            <userName>' . $user_id . '</userName>
                            <password>' . $password . '</password>
                    </SpgUserCredentials>
                    </soap:Header>
                    <soap:Body>
                    <TransactionVerification
                    xmlns="http://tempuri.org/">
                    <OwnerCode>' . $ownerCode . '</OwnerCode>
                    <ReferenceDate>' . date('Y-m-d', strtotime($paymentInfo->ref_tran_date_time)) . '</ReferenceDate>
                    <RequiestNo>' . $paymentInfo->request_id . '</RequiestNo>
                    <isEncPwd>true</isEncPwd>
                    </TransactionVerification>
                    </soap:Body>
                    </soap:Envelope>';


            // Verification request store JSON & XML format in Payment table
            $payVerificationReqInfo['userName'] = $user_id;
            $payVerificationReqInfo['password'] = $password;
            $payVerificationReqInfo['OwnerCode'] = $ownerCode;
            $payVerificationReqInfo['ReferenceDate'] = date('Y-m-d', strtotime($paymentInfo->ref_tran_date_time));
            $payVerificationReqInfo['RequiestNo'] = $paymentInfo->request_id;
            $payVerificationReqInfo['isEncPwd'] = 'true';

            $payVerificationReqInfoJSON = json_encode($payVerificationReqInfo);
            $paymentInfo->offline_verify_request_xml = $body;
            $paymentInfo->offline_verify_request = $payVerificationReqInfoJSON;
            $paymentInfo->save();

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $web_service_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: text/xml;charset=utf-8", 'SOAPAction: "http://tempuri.org/TransactionVerification"'
            ));

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                Session::flash('error', 'Payment verification Request Error:. ' . curl_error($ch) . '[SPC-116]');
                return false;
            }

            $xml_parser = xml_parser_create();
            xml_parse_into_struct($xml_parser, $result, $val);
            $responseData = json_decode($val[3]['value']);
            //$responseData = $responseData[0];

            /*
             * if response is empty then return false
             */
            if (empty($responseData) || $responseData->StatusCode != "200") {
                Session::flash('error', 'Your payment is not received by Sonali Bank till now. [SPC-139]');
                return false;
            }

            // Verification response store JSON & XML format in Payment table
            $paymentInfo->offline_verify_response_xml = $result;
            $paymentInfo->offline_verify_response = json_encode($responseData);

            if (!empty($responseData->TransactionId) && $responseData->StatusCode == "200") {
                $paymentInfo->is_verified = 1;
                $paymentInfo->save();
                return true;
            }

            $paymentInfo->save();

            Session::flash('error', 'Invalid verification response. [SPC-119]');
            return false;
        } catch (\Exception $e) {
            Session::flash('error', 'Something went wrong! Error: . ' . $e->getMessage() . '[SPC-120]');
            return false;
        }
    }


    /*
     * Daily transaction list
     */
    public function dailyTransaction()
    {
        try {
            $web_service_url = config('payment.spg_settings.spg_web_service_url');
            $user_id = config('payment.spg_settings.spg_user_id');
            $password = config('payment.spg_settings.spg_password');
            $ownerCode = config('payment.spg_settings.st_code');

            $body = '<?xml version="1.0" encoding="utf-8"?>
                    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                    <soap:Header>
                    <SpgUserCredentials xmlns="http://tempuri.org/">
                            <userName>' . $user_id . '</userName>
                            <password>' . $password . '</password>
                    </SpgUserCredentials>
                    </soap:Header>
                    <soap:Body>
                    <DailyStTransaction
                    xmlns="http://tempuri.org/">
                    <OwnerCode>' . $ownerCode . '</OwnerCode>
                    <ReferenceDate>' . date('Y-m-d') . '</ReferenceDate>
                    <isEncPwd>true</isEncPwd>
                    </DailyStTransaction>
                    </soap:Body>
                    </soap:Envelope>';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $web_service_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER,
                array("Content-Type: text/xml;charset=utf-8", 'SOAPAction: "http://tempuri.org/DailyStTransaction"'));

            $result = curl_exec($ch);

            if (curl_errno($ch)) {
                Session::flash('error', 'Daily Transaction request error: ' . curl_error($ch) . '[SPC-130]');
                return redirect()->back();
            }

            $xml_parser = xml_parser_create();
            xml_parse_into_struct($xml_parser, $result, $val);
            echo 'Daily transaction details:</br></br></pre>';
            print_r($val[3]['value']);
            echo '</br></br>End of daily transaction details:</br>';
            $dailyTransactions = $val[3]['value'];
            return view('SonaliPayment::daily_transaction', compact('dailyTransactions'));
        } catch (\Exception $e) {
            Session::flash('error', 'Something went wrong. Error: ' . $e->getMessage() . '[SPC-110]');
            return redirect()->back();
        }
    }

    public static function getGovtVendorVatPercentage()
    {
        return (int)Configuration::where('caption', 'GOVT_VENDOR_VAT_FEE')->value('value');
    }

    public static function vatFreeAuditStore($payment_id, $amount)
    {
        PaymentWithoutVat::create([
            'sp_payment_id' => $payment_id,
            'amount' => $amount
        ]);
    }

    private function getEmployeeInfo($processTypeId, $appId)
    {
        $emp_info = [];
        switch ($processTypeId) {
            case 1:  //VISA RECOMMENDATION
                $emp_info = VisaRecommendation::where('id', $appId)
                    ->select('emp_name', 'emp_passport_no')
                    ->first();
                break;

            case 10: //VISA RECOMMENDATION AMENDMENT
                $emp_info = VisaRecommendationAmendment::where('id', $appId)
                    ->select(
                        DB::raw('COALESCE(n_emp_name, emp_name) AS emp_name'),
                        DB::raw('COALESCE(n_emp_passport_no, emp_passport_no) AS emp_passport_no')
                    )->first();
                break;

            case 2: //WORK PERMIT NEW
                $emp_info = WorkPermitNew::where('id', $appId)
                    ->select('emp_name', 'emp_passport_no')
                    ->first();
                break;

            case 3: //WORK PERMIT EXTENSION
                $emp_info = WorkPermitExtension::where('id', $appId)
                    ->select('emp_name', 'emp_passport_no')
                    ->first();
                break;

            case 4: //WORK PERMIT AMENDMENT
                $emp_info = WorkPermitAmendment::where('id', $appId)
                    ->select(
                        DB::raw('COALESCE(n_emp_name, emp_name) AS emp_name'),
                        DB::raw('COALESCE(n_emp_passport_no, emp_passport_no) AS emp_passport_no')
                    )->first();
                break;

            case 5: //WORK PERMIT CANCELLATION
                $emp_info = WorkPermitCancellation::where('id', $appId)
                    ->select(
                        'applicant_name as emp_name',
                        'applicant_pass_no as emp_passport_no'
                    )->first();
                break;

            default:
                $emp_info = (object)["emp_name" => "", "emp_passport_no" => ""];
                break;
        }

        return $emp_info;
    }
}