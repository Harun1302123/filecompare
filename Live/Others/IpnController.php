<?php


namespace App\Modules\SonaliPayment\Controllers;


use App\Http\Controllers\Controller;
use App\Libraries\CommonFunction;
use App\Libraries\UtilFunction;
use App\Modules\Settings\Models\Configuration;
use App\Modules\SonaliPayment\Models\IpnRequest;
use App\Modules\SonaliPayment\Models\SonaliPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IpnController extends Controller
{
    /**
     * IPN request handling
     * @param Request $request
     */
    public function apiIpnRequestPOST(Request $request)
    {
        $requestData = $request->all();
        $allowed_ip_data = Configuration::where('caption', 'ALLOW_IP_IPN')->pluck('value');
        $allowed_ip = explode(",", $allowed_ip_data);
        $env_spg_user_id = trim(config('payment.spg_settings.spg_user_id'));
        $env_spg_password = trim(config('payment.spg_settings.spg_password'));
        $requestIp = UtilFunction::getVisitorRealIP();

        /*
         * Check request is empty or not.
         * if empty, then store the request and return with message.
         */
        DB::beginTransaction();
        $IpnRequest = IpnRequest::firstOrNew(['ref_tran_no' => trim($requestData['RefTranNo']), 'ref_tran_date_time' => trim($requestData['RefTransTime'])]);

        if (empty($requestData)) {
            $IpnRequest->request_ip = $requestIp;

            $response['Msg'] = 'Request empty';
            $response['ResponseCode'] = 0;
            $response['ResponseType'] = 'IPN_RESPONSE';
            header('Content-type: application/json');
            echo json_encode($response);

            $IpnRequest->ipn_response = json_encode($response);
            $IpnRequest->save();
            DB::commit();
            exit();
        }


        // Check request IP is authorized or not
        if (!in_array("$requestIp", $allowed_ip)) {
            $response['Msg'] = 'Request could not be processed due to unauthorized ip';
        }

        // Check request authentication information is valid or not
        if (!($env_spg_user_id == $requestData['UserId'] && $env_spg_password == $requestData['Password'])) {
            $response['Msg'] = 'Request could not be processed due to UserId and password';
        }

        // Check request type is valid or not
        if ($requestData['requestType'] != 'IPN') {
            $response['Msg'] = 'Request could not be processed due to request type';
        }

        try {
            $IpnRequest->request_ip = $requestIp;
            $IpnRequest->transaction_id = $requestData['TransId'];
            $IpnRequest->pay_mode_code = $requestData['PayMode'];
            $IpnRequest->trans_time = $requestData['TransTime'];
            $IpnRequest->ref_tran_no = $requestData['RefTranNo'];
            $IpnRequest->ref_tran_date_time = $requestData['RefTransTime'];
            $IpnRequest->trans_status = $requestData['TransStatus'];
            $IpnRequest->trans_amount = $requestData['TransAmount'];
            $IpnRequest->pay_amount = $requestData['PayAmount'];
            $IpnRequest->json_object = json_encode($requestData);


            /*
             * if there have any validation error like : invalid request type, unauthorized IP,
             * invalid user or password then return response with error status and
             * store request info with is_authorized_request = '0'.
             * Otherwise, return response with success status and store request info with
             * is_authorized_request = '1'.
             */
            if ($requestData['requestType'] != 'IPN' || !in_array("$requestIp",
                    $allowed_ip) || !($env_spg_user_id == $requestData['UserId'] && $env_spg_password == $requestData['Password'])
            ) {

                $response['ResponseCode'] = 0;
                $response['ResponseType'] = 'IPN_RESPONSE';
                $IpnRequest->is_authorized_request = 0;
            } else {
                $response['ResponseCode'] = 1;
                $response['ResponseType'] = 'IPN_RESPONSE';
                $response['Msg'] = 'Request has been processed successfully';
                $IpnRequest->is_authorized_request = 1;

                // no need to check for the counter payment
//                if ($requestData['PayMode'] != 'A01') {
//                    $sonaliPayment = SonaliPayment::leftJoin('process_list', 'process_list.tracking_no', '=', 'sp_payment.app_tracking_no')
//                        ->where('sp_payment.ref_tran_no', $requestData['RefTranNo'])
//                        ->first([
//                            'sp_payment.id',
//                            'sp_payment.payment_status',
//                            'sp_payment.is_verified',
//                            'sp_payment.status_code',
//                            'process_list.process_type_id',
//                            'process_list.status_id',
//                        ]);
//                    if ($sonaliPayment) {
//                        $IpnRequest->sp_payment_id = $sonaliPayment->id;
//
//                        /**
//                         * Checking whether this payment is complete.
//                         * If the payment is incomplete, the auto-recover status will be updated.
//                         */
//                        if ($sonaliPayment->payment_status != 1 or $sonaliPayment->is_verified != 1) {
//                            if (in_array($sonaliPayment->process_type_id, config('bida_service.active'))) {
//
//                                if (in_array($sonaliPayment->status_id, [-1, 15, 32])) {
//                                    $IpnRequest->is_required_auto_recover = 1;
//                                }
//                            }
//                        }
//                    }
//                }
            }

            header('Content-type: application/json');
            echo json_encode($response);
            $IpnRequest->ipn_response = json_encode($response);
            $IpnRequest->save();
            DB::commit();
            exit();

        } catch (Exception $e) {
            $response['ResponseCode'] = 0;
            $response['Msg'] = CommonFunction::showErrorPublic($e->getMessage());
            $response['ResponseType'] = 'IPN_RESPONSE';
            header('Content-type: application/json');
            echo json_encode($response);

            /*
             * if any exception has occurred, then the request will be missed.
             * So, we store request also for any exception with exception message.
             *
             */
            $IpnRequest->ipn_response = json_encode($response);
            $IpnRequest->save();
            DB::commit();
            exit();
        }
    }

    /**
     * @return mixed
     */

}