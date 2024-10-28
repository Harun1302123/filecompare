<?php

namespace App\Modules\ProcessPath\Services;

//use App\Jobs\CommonPoolUpdate;
//use App\Jobs\StoreCertificateGenerationRequest;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Libraries\UtilFunction;
use App\Modules\Apps\Models\Airports;
use App\Modules\BasicInformation\Models\BasicInformation;
use App\Modules\BidaRegistration\Controllers\BidaRegistrationController;
use App\Modules\BidaRegistration\Models\BidaRegistration;
use App\Modules\BidaRegistrationAmendment\Controllers\BidaRegistrationAmendmentController;
use App\Modules\BidaRegistrationAmendment\Models\BidaRegistrationAmendment;
use App\Modules\BoardMeting\Models\BoardMeting;
use App\Modules\ImportPermission\Controllers\ImportPermissionController;
use App\Modules\ImportPermission\Models\ImportPermission;
use App\Modules\IrcRecommendationNew\Models\IrcInspection;
use App\Modules\IrcRecommendationNew\Models\IrcRecommendationNew;
use App\Modules\IrcRecommendationSecondAdhoc\Models\IrcRecommendationSecondAdhoc;
use App\Modules\IrcRecommendationSecondAdhoc\Models\SecondIrcInspection;
use App\Modules\IrcRecommendationThirdAdhoc\Models\IrcRecommendationThirdAdhoc;
use App\Modules\IrcRecommendationRegular\Models\IrcRecommendationRegular;
use App\Modules\IrcRecommendationRegular\Models\RegularIrcInspection;
use App\Modules\IrcRecommendationThirdAdhoc\Models\ThirdIrcInspection;
use App\Modules\OfficePermissionAmendment\Models\OfficePermissionAmendment;
use App\Modules\OfficePermissionCancellation\Models\OfficePermissionCancellation;
use App\Modules\OfficePermissionExtension\Models\OfficePermissionExtension;
use App\Modules\OfficePermissionNew\Models\OfficePermissionNew;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\Remittance\Models\Remittance;
use App\Modules\Settings\Models\HighComissions;
use App\Modules\Settings\Models\Stakeholder;
use App\Modules\Users\Models\CompanyInfo;
use App\Modules\Users\Models\Users;
use App\Modules\VipLounge\Models\VipLounge;
use App\Modules\VisaRecommendation\Models\VisaRecommendation;
use App\Modules\VisaRecommendationAmendment\Models\VisaRecommendationAmendment;
use App\Modules\WaiverCondition7\Models\WaiverCondition7;
use App\Modules\WaiverCondition8\Models\WaiverCondition8;
use App\Modules\WorkPermitAmendment\Models\WorkPermitAmendment;
use App\Modules\WorkPermitCancellation\Models\WorkPermitCancellation;
use App\Modules\WorkPermitExtension\Models\WorkPermitExtension;
use App\Modules\WorkPermitNew\Models\WorkPermitNew;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

trait CertificateMailAppDataProcessingTrait
{
    use CertificateGenerationRequestTrait;

    /**
     * CertificateMailOtherData
     *
     * Any customized code for different processType and status can be done in this function
     *
     * @param  mixed $process
     * @param  mixed $status_id
     * @param  mixed $approver_desk_id
     * @param  mixed $requestData
     */
    function CertificateMailOtherData($process_list_id, $status_id, $approver_desk_id = 0, $requestData)
    {
        $process = ProcessList::leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
            ->where('process_list.id', $process_list_id)
            ->first([
                'process_type.name as process_type_name',
                'process_type.process_supper_name',
                'process_type.process_sub_name',
                'process_list.*'
        ]);

        //  Get users email and phone no according to working company id
        $applicantEmailPhone = UtilFunction::geCompanyUsersEmailPhone($process->company_id);

        $appInfo = [
            'app_id' => $process->ref_id,
            'status_id' => $status_id,
            'process_type_id' => $process->process_type_id,
            'department_id' => $process->department_id,
            'tracking_no' => $process->tracking_no,
            'process_type_name' => $process->process_type_name,
            'process_supper_name' => $process->process_supper_name,
            'process_sub_name' => $process->process_sub_name,
            'remarks' => $requestData['remarks'],
            'resend_deadline' => empty($process->resend_deadline) ? null : date('d-M-Y', strtotime($process->resend_deadline))
        ];

        if ($status_id == 5) {
            CommonFunction::sendEmailSMS('APP_SHORTFALL', $appInfo, $applicantEmailPhone);
        } elseif ($status_id == 6) {
            CommonFunction::sendEmailSMS('APP_REJECT', $appInfo, $applicantEmailPhone);
        } elseif ($status_id == 19) {
            $meetingId = $requestData['board_meeting_id'];
            $boardMeeting = BoardMeting::find($meetingId);
            $appInfo['meting_number'] = $boardMeeting->meting_number;
            $appInfo['meeting_date'] = date('d-m-Y', strtotime($boardMeeting->meting_date));
            $appInfo['meeting_time'] = date('h:i:s a', strtotime($boardMeeting->meting_date));
            CommonFunction::sendEmailSMS('PROCEED_TO_MEETING', $appInfo, $applicantEmailPhone);
        }

        switch ($process->process_type_id) {
            //        case 100: // Basic Information
            //            if (in_array($status_id, ['25'])){
            //
            //                /*
            //                 * Enable company eligibility for other service/ process
            //                 * without eligibility, user can't access any service except Basic Information
            //                 */
            //                $output1 = CompanyInfo::where('id', $process->company_id)->update([
            //                    'is_eligible' => 1
            //                ]);
            //
            //                $data = [];
            //                $data['approved_date'] = date('Y-m-d H:i:s');
            //                $data['is_approved'] = 1;
            //                $output = BasicInformation::where('id',$process->ref_id)->update($data);
            //
            //                $output3 = ProcessList::where('id', $process_list_id)->update(['completed_date' => date('Y-m-d H:i:s')]);
            //
            //                if($output1 && $output && $output3 && count($applicantEmailPhone) > 0){
            //                    CommonFunction::sendEmailSMS('APP_APPROVE_WITHOUT_LETTER',$appInfo, $applicantEmailPhone);
            //                    return true;
            //                }
            //            }
            //
            //            return true;
            //            break;
            case 1:
                // Visa Recommendation New
                if ($status_id == 25) {
                    // update application data
                    $data = [];
                    $data['approved_date'] = date('Y-m-d H:i:s');
                    $output = VisaRecommendation::where('id', $process->ref_id)->update($data);

                    $certificateGenerate = $this->certificateGenerationRequest($process->ref_id, $process->process_type_id, $approver_desk_id);

                    if ($output && $certificateGenerate) {  //return true

                        $appInfo['attachment_certificate_name'] = 'vr_apps.certificate_link';
                        CommonFunction::sendEmailSMS('APP_APPROVE', $appInfo, $applicantEmailPhone);
                        $applicationData = VisaRecommendation::leftJoin('dept_application_type', 'dept_application_type.id', '=', 'vr_apps.app_type_id')
                            ->leftJoin('country_info', 'country_info.id', '=', 'vr_apps.emp_nationality_id')
                            ->leftJoin('high_comissions', 'high_comissions.id', '=', 'vr_apps.high_commision_id')
                            ->leftJoin('airports', 'airports.id', '=', 'vr_apps.airport_id')
                            ->where('vr_apps.id', $process->ref_id)
                            ->first([
                                'vr_apps.id',
                                'vr_apps.app_type_id',
                                'vr_apps.high_commision_id as high_commission_id',
                                'vr_apps.airport_id',
                                'vr_apps.emp_name as name',
                                'vr_apps.emp_passport_no as passport_number',
                                'vr_apps.emp_designation as designation',
                                'country_info.nationality',
                                'dept_application_type.name as visa_type',
                                'high_comissions.name as high_commission_name',
                                'high_comissions.address as high_commission_address',
                                'airports.name as airport_name',
                                'airports.city_name as airport_city',
                                'airports.country_name as airport_country'
                            ]);
                        $appInfo['name'] = $applicationData->name;
                        $appInfo['nationality'] = $applicationData->nationality;
                        $appInfo['passport_number'] = $applicationData->passport_number;
                        $appInfo['designation'] = $applicationData->designation;
                        $appInfo['visa_type'] = $applicationData->visa_type;
                        $appInfo['airport_name'] = $applicationData->airport_name;
                        $appInfo['airport_address'] = $applicationData->airport_city . ', ' . $applicationData->airport_country;
                        $appInfo['high_commission_name'] = $applicationData->high_commission_name;
                        $appInfo['high_commission_address'] = $applicationData->high_commission_address;

                        if ($applicationData->app_type_id == 5) { //Visa on arrival
                            $airportEmailPhone = Airports::where('id', $applicationData->airport_id)
                                ->get([
                                    'email as user_email',
                                    'phone as user_phone'
                                ]);
                            if (count($airportEmailPhone) > 0)
                                CommonFunction::sendEmailSMS('IMMIGRATION', $appInfo, $airportEmailPhone);
                        } else {
                            $embassyEmailPhone = HighComissions::where('id', $applicationData->high_commission_id)
                                ->get([
                                    'email as user_email',
                                    'phone as user_phone'
                                ]);
                            if (count($embassyEmailPhone) > 0)
                                CommonFunction::sendEmailSMS('EMBASSY_HIGH_COMMISSION', $appInfo, $embassyEmailPhone);
                        }


                        $stakeholderEmailPhone = Stakeholder::where('process_type_id', $process->process_type_id)
                            ->where('department_id', $process->department_id)
                            ->where('status', 1)
                            ->get([
                                'email as user_email',
                                'phone as user_phone'
                            ]);

                        if (count($stakeholderEmailPhone) > 0) {
                            CommonFunction::sendEmailSMS('VRN_ISSUED_LETTER_STAKEHOLDER', $appInfo, $stakeholderEmailPhone);
                        }
                    }

                    //store VR common pool information
                    VRCommonPoolManager::VRDataStore($process->tracking_no, $process->ref_id);
                }
                return true;
                break;

            case 2: // Work Permit New, 8 = Observation, 9 = Verified, 15 = Approved, 19 = Proceed to Meeting, 32 = Condition Satisfied

                if (in_array($status_id, ['8', '9', '15', '19'])) {
                    if ($status_id != 8) {
                        if (empty($requestData['approved_duration_start_date']) || empty($requestData['approved_duration_end_date']) || empty($requestData['approved_desired_duration']) || empty($requestData['approved_duration_amount'])) {
                            Session::flash('error', 'Application duration not fill up.[PPC-1201]');
                            return false;
                        }
                    }

                    //govt fees calculation and send mail.
                    $appInfo['approved_duration_start_date'] = (!empty($requestData['approved_duration_start_date']) ? date('Y-m-d', strtotime($requestData['approved_duration_start_date'])) : null);
                    $appInfo['approved_duration_end_date'] = (!empty($requestData['approved_duration_end_date']) ? date('Y-m-d', strtotime($requestData['approved_duration_end_date'])) : null);
                    $durationData = commonFunction::getDesiredDurationDiffDate($appInfo);
                    $approved_desired_duration = (string)$durationData['string'];
                    $appInfo['approve_duration_year'] = (int)$durationData['approve_duration_year'];
                    $appInfo['govt_fees'] = CommonFunction::getGovtFees($appInfo);
                    // end of gov fees calculation
                    $data = [];
                    $data['approved_duration_start_date'] = $appInfo['approved_duration_start_date'];
                    $data['approved_duration_end_date'] = $appInfo['approved_duration_end_date'];
                    $data['approved_desired_duration'] = $approved_desired_duration; //$requestData['approved_desired_duration'];
                    $data['approved_duration_amount'] = $appInfo['govt_fees']; //$requestData['approved_duration_amount'];
                    $data['basic_salary'] = (!empty($requestData['basic_salary']) ? $requestData['basic_salary'] : '');

                    //                    $duration_check = CommonFunction::durationCalculate($data['approved_duration_start_date'], $data['approved_duration_end_date'], $data['approved_desired_duration']);
                    //                    if($duration_check == 'false'){
                    //                        Session::flash('error', 'Application duration not correct.[WPN-1203]');
                    //                        return false;
                    //                    }


                    $output = WorkPermitNew::where('id', $process->ref_id)->update($data);

                    if ($output && $status_id == 15) {
                        CommonFunction::sendEmailSMS('APP_APPROVE_AND_PAYMENT', $appInfo, $applicantEmailPhone);
                    }
                } elseif ($status_id == 17) { // 17 = conditional approved ...
                    CommonFunction::sendEmailSMS('APP_CONDITIONAL_APPROVED', $appInfo, $applicantEmailPhone);
                } elseif ($status_id == 32) { // 32 = Condition Satisfied

                    $get_duration_data = WorkPermitNew::where('id', $process->ref_id)
                        ->first(['approved_duration_start_date', 'approved_duration_end_date']);

                    //  govt fees calculation and send mail.
                    $appInfo['approved_duration_start_date'] = $get_duration_data->approved_duration_start_date;
                    $appInfo['approved_duration_end_date'] = $get_duration_data->approved_duration_end_date;
                    $durationData = commonFunction::getDesiredDurationDiffDate($appInfo);
                    $appInfo['approve_duration_year'] = (int)$durationData['approve_duration_year'];
                    $appInfo['govt_fees'] = CommonFunction::getGovtFees($appInfo);
                    // end of gov fees calculation

                    CommonFunction::sendEmailSMS('APP_CONDITION_SATISFIED_AND_PAYMENT', $appInfo, $applicantEmailPhone);
                } elseif ($status_id == 25) {
                    // update application data
                    $data = [];
                    $data['approved_date'] = date('Y-m-d H:i:s');

                    $output = WorkPermitNew::where('id', $process->ref_id)->update($data);

                    $certificateGenerate = $this->certificateGenerationRequest($process->ref_id, $process->process_type_id, $approver_desk_id, $process->department_id);

                    if ($output && $certificateGenerate) {
                        $appInfo['attachment_certificate_name'] = 'wp_apps.certificate_link';
                        CommonFunction::sendEmailSMS('APP_ISSUED_LETTER', $appInfo, $applicantEmailPhone);

                        $stakeholderEmailPhone = Stakeholder::where('process_type_id', $process->process_type_id)
                            ->where('department_id', $process->department_id)
                            ->where('status', 1)
                            ->get([
                                'email as user_email',
                                'phone as user_phone'
                            ]);

                        if (count($stakeholderEmailPhone) > 0) {
                            $applicationData = WorkPermitNew::leftJoin('country_info', 'country_info.id', '=', 'wp_apps.emp_nationality_id')
                                ->where('wp_apps.id', $process->ref_id)
                                ->first([
                                    'country_info.nationality',
                                    'wp_apps.*'
                                ]);

                            $appInfo['name'] = $applicationData->emp_name;
                            $appInfo['designation'] = $applicationData->emp_designation;
                            $appInfo['nationality'] = $applicationData->nationality;
                            $appInfo['passport_number'] = $applicationData->emp_passport_no;
                            CommonFunction::sendEmailSMS('WP_ISSUED_LETTER_STAKEHOLDER', $appInfo, $stakeholderEmailPhone);
                        }
                    }

                    WPCommonPoolManager::wpnDataStore($process->tracking_no, $process->ref_id);
                }
                return true;
                break;

            case 3: // Work Permit Extension, 8 = Observation, 9 = Verified, 15 = Approved, 19 = Proceed to Meeting, 32 = Condition Satisfied
                if (in_array($status_id, ['8', '9', '15', '19'])) {
                    if ($status_id != 8) {
                        if (empty($requestData['approved_duration_start_date']) || empty($requestData['approved_duration_end_date']) || empty($requestData['approved_desired_duration']) || empty($requestData['approved_duration_amount'])) {
                            Session::flash('error', 'Application duration not fill up.[PPC-1216]');
                            return false;
                        }
                    }

                    //  govt fees calculation and send mail.
                    $appInfo['approved_duration_start_date'] = (!empty($requestData['approved_duration_start_date']) ? date('Y-m-d', strtotime($requestData['approved_duration_start_date'])) : null);
                    $appInfo['approved_duration_end_date'] = (!empty($requestData['approved_duration_end_date']) ? date('Y-m-d', strtotime($requestData['approved_duration_end_date'])) : null);
                    $durationData = commonFunction::getDesiredDurationDiffDate($appInfo);
                    $approved_desired_duration = (string)$durationData['string'];
                    $appInfo['approve_duration_year'] = (int)$durationData['approve_duration_year'];
                    $appInfo['govt_fees'] = CommonFunction::getGovtFees($appInfo);
                    // end of gov fees calculation

                    $data = [];
                    $data['approved_duration_start_date'] = $appInfo['approved_duration_start_date'];
                    $data['approved_duration_end_date'] = $appInfo['approved_duration_end_date'];
                    $data['approved_desired_duration'] = $approved_desired_duration;
                    $data['approved_duration_amount'] = $appInfo['govt_fees'];
                    $data['basic_salary'] = (!empty($requestData['basic_salary']) ? $requestData['basic_salary'] : '');

                    $output = WorkPermitExtension::where('id', $process->ref_id)->update($data);
                    if ($output && $status_id == 15) {
                        CommonFunction::sendEmailSMS('APP_APPROVE_AND_PAYMENT', $appInfo, $applicantEmailPhone);
                    }
                } elseif ($status_id == 17) { // 17 = conditional approved
                    CommonFunction::sendEmailSMS('APP_CONDITIONAL_APPROVED', $appInfo, $applicantEmailPhone);
                } elseif ($status_id == 32) { // 32 = Condition Satisfied
                    $get_duration_data = WorkPermitExtension::where('id', $process->ref_id)
                        ->first(['approved_duration_start_date', 'approved_duration_end_date', 'approved_desired_duration']);

                    // govt fees calculation and send mail.
                    $appInfo['approved_duration_start_date'] = $get_duration_data->approved_duration_start_date;
                    $appInfo['approved_duration_end_date'] = $get_duration_data->approved_duration_end_date;
                    $durationData = commonFunction::getDesiredDurationDiffDate($appInfo);
                    $appInfo['approve_duration_year'] = (int)$durationData['approve_duration_year'];
                    $appInfo['govt_fees'] = CommonFunction::getGovtFees($appInfo);
                    // end of gov fees calculation

                    CommonFunction::sendEmailSMS('APP_CONDITION_SATISFIED_AND_PAYMENT', $appInfo, $applicantEmailPhone);
                } elseif ($status_id == 25) {
                    // update application data
                    $data = [];
                    $data['approved_date'] = date('Y-m-d H:i:s');

                    $output = WorkPermitExtension::where('id', $process->ref_id)->update($data);
                    $certificateGenerate = $this->certificateGenerationRequest($process->ref_id, $process->process_type_id, $approver_desk_id, $process->department_id);

                    if ($output && $certificateGenerate) {
                        $appInfo['attachment_certificate_name'] = 'wpe_apps.certificate_link';
                        CommonFunction::sendEmailSMS('APP_ISSUED_LETTER', $appInfo, $applicantEmailPhone);

                        $stakeholderEmailPhone = Stakeholder::where('process_type_id', $process->process_type_id)
                            ->where('department_id', $process->department_id)
                            ->where('status', 1)
                            ->get([
                                'email as user_email',
                                'phone as user_phone'
                            ]);

                        if (count($stakeholderEmailPhone) > 0) {
                            $applicationData = WorkPermitExtension::leftJoin('country_info', 'country_info.id', '=', 'wpe_apps.emp_nationality_id')
                                ->where('wpe_apps.id', $process->ref_id)
                                ->first([
                                    'country_info.nationality',
                                    'wpe_apps.*'
                                ]);

                            $appInfo['name'] = $applicationData->emp_name;
                            $appInfo['designation'] = $applicationData->emp_designation;
                            $appInfo['nationality'] = $applicationData->nationality;
                            $appInfo['passport_number'] = $applicationData->emp_passport_no;
                            CommonFunction::sendEmailSMS('WP_ISSUED_LETTER_STAKEHOLDER', $appInfo, $stakeholderEmailPhone);
                        }
                    }

                    WPCommonPoolManager::wpeDataStore($process->tracking_no, $process->ref_id);
                }
                return true;
                break;

            case 4: // Work Permit Amendment 8 = Observation, 9 = Verified, 15 = Approved, 19 = Proceed to Meeting
                if (in_array($status_id, ['8', '9', '15', '19'])) {
                    $getWPAinfo = WorkPermitAmendment::where('id', $process->ref_id)
                        ->first([
                            'n_duration_start_date',
                            'n_duration_end_date',
                            'n_desired_duration',
                            'approved_effective_date'
                        ]);

                    $data = [];
                    if (($getWPAinfo->n_duration_start_date != '' && !empty($getWPAinfo->n_duration_start_date)) ||
                        ($getWPAinfo->n_duration_end_date != '' && !empty($getWPAinfo->n_duration_end_date)) ||
                        ($getWPAinfo->n_desired_duration != '' && !empty($getWPAinfo->n_desired_duration))
                    ) {
                        if ($status_id != 8) {
                            if (empty($requestData['approved_duration_start_date']) || empty($requestData['approved_duration_end_date']) || empty($requestData['approved_desired_duration'])) {
                                Session::flash('error', 'Application duration not fill up.[PPC-1217]');
                                return false;
                            }
                        }

                        $data['approved_duration_start_date'] = (!empty($requestData['approved_duration_start_date']) ? date('Y-m-d', strtotime($requestData['approved_duration_start_date'])) : null);
                        $data['approved_duration_end_date'] = (!empty($requestData['approved_duration_end_date']) ? date('Y-m-d', strtotime($requestData['approved_duration_end_date'])) : null);
                        $data['approved_desired_duration'] = $requestData['approved_desired_duration'];
                        $data['approved_duration_amount'] = $requestData['approved_duration_amount'];
                        $data['basic_salary'] = (!empty($requestData['basic_salary']) ? $requestData['basic_salary'] : '');
                    }

                    //effective approved date
                    if (!empty($getWPAinfo->approved_effective_date) && $getWPAinfo->approved_effective_date != '') {
                        $data['approved_effective_date'] = (!empty($requestData['approved_effective_date']) ? date('Y-m-d', strtotime($requestData['approved_effective_date'])) : '');
                    }

                    $output = WorkPermitAmendment::where('id', $process->ref_id)->update($data);
                    if (!$output) {
                        Session::flash('error', 'Application duration update error.[PPC-1215]');
                        return false;
                    }

                    if ($status_id == 15) {
                        $appInfo['govt_fees'] = CommonFunction::getGovtFees($appInfo);
                        CommonFunction::sendEmailSMS('APP_APPROVE_AND_PAYMENT', $appInfo, $applicantEmailPhone);
                    }
                } elseif ($status_id == 17) { // 17 = conditional approved
                    CommonFunction::sendEmailSMS('APP_CONDITIONAL_APPROVED', $appInfo, $applicantEmailPhone);
                } elseif ($status_id == 25) {

                    // update application data
                    $data = [];
                    $data['approved_date'] = date('Y-m-d H:i:s');

                    $output = WorkPermitAmendment::where('id', $process->ref_id)->update($data);

                    $certificateGenerate = $this->certificateGenerationRequest($process->ref_id, $process->process_type_id, $approver_desk_id, $process->department_id);

                    //$certificateJob = (new StoreCertificateGenerationRequest($process->ref_id, $process->process_type_id, $approver_desk_id, $process->department_id, Auth::user()->id, 'generate'))->onQueue('certificate');
                    //$this->dispatch($certificateJob);

                    if ($output && $certificateGenerate) {
                        $appInfo['attachment_certificate_name'] = 'wpa_apps.certificate_link';
                        CommonFunction::sendEmailSMS('APP_ISSUED_LETTER', $appInfo, $applicantEmailPhone);
                        //Email is not sending to stakeholder at issue letter for amendment as per discussion

                        $stakeholderEmailPhone = Stakeholder::where('process_type_id', $process->process_type_id)
                            ->where('department_id', $process->department_id)
                            ->where('status', 1)
                            ->get([
                                'email as user_email',
                                'phone as user_phone'
                            ]);

                        if (count($stakeholderEmailPhone) > 0) {
                            $applicationData = WorkPermitAmendment::leftJoin('country_info', 'country_info.id', '=', 'wpa_apps.emp_nationality_id')
                                ->where('wpa_apps.id', $process->ref_id)
                                ->first([
                                    'country_info.nationality',
                                    'wpa_apps.emp_name',
                                    'wpa_apps.emp_designation',
                                    'wpa_apps.emp_passport_no',
                                ]);

                            $appInfo['name'] = $applicationData->emp_name;
                            $appInfo['designation'] = $applicationData->emp_designation;
                            $appInfo['nationality'] = $applicationData->nationality;
                            $appInfo['passport_number'] = $applicationData->emp_passport_no;
                            CommonFunction::sendEmailSMS('WP_ISSUED_LETTER_STAKEHOLDER', $appInfo, $stakeholderEmailPhone);
                        }
                    }

                    WPCommonPoolManager::wpaDataStore($process->tracking_no, $process->ref_id);

//                    $commonPool = (new CommonPoolUpdate(WPCommonPoolManager::class, 'wpaDataStore', [$process->tracking_no, $process->ref_id]))->onQueue('common-pull');
//                    $this->dispatch($commonPool);
                }
                return true;
                break;

            case 5: // Work Permit Cancellation, 8 = Observation, 9 = Verified, 15 = Approved, 19 = Proceed to Meeting
                if (in_array($status_id, ['8', '9', '15', '19'])) { // Approved form Meeting Chairperson or Director
                    if ($status_id != 8) {
                        if (empty($requestData['approved_effect_date'])) {
                            Session::flash('error', 'Application duration not fill up.[PPC-1295]');
                            return false;
                        }
                    }
                    $data = [];
                    $data['approved_effect_date'] = (!empty($requestData['approved_effect_date']) ? date('Y-m-d', strtotime($requestData['approved_effect_date'])) : '');
                    $output = WorkPermitCancellation::where('id', $process->ref_id)->update($data);
                    if (!$output) {
                        Session::flash('error', 'Application duration update error!.[PPC-1296]');
                        return false;
                    }
                    if ($status_id == 15) {
                        CommonFunction::sendEmailSMS('APP_APPROVE_WITHOUT_LETTER', $appInfo, $applicantEmailPhone);
                    }
                } elseif ($status_id == 17) { // 17 = conditional approved
                    CommonFunction::sendEmailSMS('APP_CONDITIONAL_APPROVED', $appInfo, $applicantEmailPhone);
                } elseif ($status_id == 25) {
                    // update application data
                    $data = [];
                    $data['approved_date'] = date('Y-m-d H:i:s');

                    $output = WorkPermitCancellation::where('id', $process->ref_id)->update($data);

                    $certificateGenerate = $this->certificateGenerationRequest($process->ref_id, $process->process_type_id, $approver_desk_id, $process->department_id);
                    if ($output and $certificateGenerate) {  //return true
                        $appInfo['attachment_certificate_name'] = 'wpc_apps.certificate_link';
                        CommonFunction::sendEmailSMS('APP_ISSUED_LETTER', $appInfo, $applicantEmailPhone);

                        $stakeholderEmailPhone = Stakeholder::where('process_type_id', $process->process_type_id)
                            ->where('department_id', $process->department_id)
                            ->where('status', 1)
                            ->get([
                                'email as user_email',
                                'phone as user_phone'
                            ]);

                        if (count($stakeholderEmailPhone) > 0) {
                            $applicationData = WorkPermitCancellation::leftJoin('country_info', 'country_info.id', '=', 'wpc_apps.applicant_nationality')
                                ->where('wpc_apps.id', $process->ref_id)
                                ->first([
                                    'country_info.nationality',
                                    'wpc_apps.*'
                                ]);

                            $appInfo['name'] = $applicationData->applicant_name;
                            $appInfo['designation'] = $applicationData->applicant_position;
                            $appInfo['nationality'] = $applicationData->nationality;
                            $appInfo['passport_number'] = $applicationData->applicant_pass_no;
                            CommonFunction::sendEmailSMS('WP_ISSUED_LETTER_STAKEHOLDER', $appInfo, $stakeholderEmailPhone);
                        }
                    }

                    WPCommonPoolManager::wpcDataStore($process->tracking_no, $process->ref_id);
                }
                return true;
                break;

            case 6: // Office Permission New 8 = Observation, 9 = Verified, 15 = Approved, 19 = Proceed to Meeting, 32 = Condition Satisfied
                // Proceed to meeting from Desk(3)
                if (in_array($status_id, ['8', '9', '15', '19'])) {
                    if ($status_id != 8) {
                        if (empty($requestData['approved_duration_start_date']) || empty($requestData['approved_duration_end_date']) || empty($requestData['approved_desired_duration']) || empty($requestData['approved_duration_amount'])) {
                            Session::flash('error', 'Application duration not fill up.[PPC-1212]');
                            return false;
                        }
                    }

                    //  govt fees calculation and send mail.
                    $appInfo['approved_duration_start_date'] = (!empty($requestData['approved_duration_start_date']) ? date('Y-m-d', strtotime($requestData['approved_duration_start_date'])) : null);
                    $appInfo['approved_duration_end_date'] = (!empty($requestData['approved_duration_end_date']) ? date('Y-m-d', strtotime($requestData['approved_duration_end_date'])) : null);
                    $durationData = commonFunction::getDesiredDurationDiffDate($appInfo);
                    $approved_desired_duration = (string)$durationData['string'];
                    $appInfo['approve_duration_year'] = (int)$durationData['approve_duration_year'];
                    $appInfo['govt_fees'] = CommonFunction::getGovtFees($appInfo);
                    // end of gov fees calculation

                    $data = [];
                    $data['approved_duration_start_date'] = (!empty($requestData['approved_duration_start_date']) ? date('Y-m-d', strtotime($requestData['approved_duration_start_date'])) : null);
                    $data['approved_duration_end_date'] = (!empty($requestData['approved_duration_end_date']) ? date('Y-m-d', strtotime($requestData['approved_duration_end_date'])) : null);
                    $data['approved_desired_duration'] = $approved_desired_duration;
                    $data['approved_duration_amount'] = $appInfo['govt_fees'];

                    $output = OfficePermissionNew::where('id', $process->ref_id)->update($data);
                    if (!$output) {
                        Session::flash('error', 'Application duration update error!.[PPC-1213]');
                        return false;
                    }

                    // Approved from Desk
                    if ($status_id == 15) {
                        CommonFunction::sendEmailSMS('APP_APPROVE_AND_PAYMENT', $appInfo, $applicantEmailPhone);
                    }
                } elseif ($status_id == 32) { // 32 = Condition Satisfied
                    $get_duration_data = OfficePermissionNew::where('id', $process->ref_id)
                        ->first(['approved_duration_start_date', 'approved_duration_end_date']);

                    // govt fees calculation and send mail.
                    $appInfo['approved_duration_start_date'] = $get_duration_data->approved_duration_start_date;
                    $appInfo['approved_duration_end_date'] = $get_duration_data->approved_duration_end_date;
                    $durationData = commonFunction::getDesiredDurationDiffDate($appInfo);
                    $appInfo['approve_duration_year'] = (int)$durationData['approve_duration_year'];
                    $appInfo['govt_fees'] = CommonFunction::getGovtFees($appInfo);
                    // end of gov fees calculation

                    CommonFunction::sendEmailSMS('APP_CONDITION_SATISFIED_AND_PAYMENT', $appInfo, $applicantEmailPhone);
                } // Issued office permission from Desk(1)
                elseif ($status_id == 25) {

                    $opn_company_info = OfficePermissionNew::where('id', $process->ref_id)->first(['local_company_name', 'local_company_name_bn']);

                    // check company is exist
                    $checkExitingCompany = CommonFunction::findCompanyNameWithoutWorkingID($opn_company_info->local_company_name, $process->company_id);
                    if (!$checkExitingCompany) {
                        Session::flash('error', 'Company name: "' . $opn_company_info->local_company_name . '" is already exist!');
                        return false;
                    }

                    // update application data
                    $data = [];
                    $data['approved_date'] = date('Y-m-d H:i:s');

                    $output = OfficePermissionNew::where('id', $process->ref_id)->update($data);

                    // Update company name in company information and basic information table
                    $basic_app_id = ProcessList::leftjoin('ea_apps', 'ea_apps.id', '=', 'process_list.ref_id')
                        ->where('process_list.process_type_id', 100)
                        ->where('process_list.status_id', 25)
                        ->where('process_list.company_id', $process->company_id)
                        ->first(['process_list.ref_id']);

                    $opn_company_info_data = [];
                    $opn_company_info_data['company_name'] = $opn_company_info->local_company_name;
                    $opn_company_info_data['company_name_bn'] = $opn_company_info->local_company_name_bn;

                    CompanyInfo::where('id', $process->company_id)->update($opn_company_info_data);
                    BasicInformation::where('id', $basic_app_id->ref_id)->update($opn_company_info_data);

                    $certificateGenerate = $this->certificateGenerationRequest($process->ref_id, $process->process_type_id, $approver_desk_id, $process->department_id);
                    if ($output && $certificateGenerate) {
                        $appInfo['attachment_certificate_name'] = 'opn_apps.certificate_link';
                        CommonFunction::sendEmailSMS('APP_ISSUED_LETTER', $appInfo, $applicantEmailPhone);

                        $stakeholderEmailPhone = Stakeholder::where('process_type_id', $process->process_type_id)
                            ->where('department_id', $process->department_id)
                            ->where('status', 1)
                            ->get([
                                'email as user_email',
                                'phone as user_phone'
                            ]);

                        if (count($stakeholderEmailPhone) > 0) {
                            $applicationData = OfficePermissionNew::find($process->ref_id);
                            $appInfo['organization_name'] = $applicationData->company_name;
                            CommonFunction::sendEmailSMS('OP_ISSUED_LETTER_STAKEHOLDER', $appInfo, $stakeholderEmailPhone);
                        }
                    }

                    $isDataStored = OPCommonPoolManager::OPNDataStore($process->tracking_no, $process->ref_id);
                    if ($isDataStored === false) {
                        return false;
                    }
                }
                return true;
                break;

            case 7: // Office Permission Extension, 8 = Observation, 9 = Verified, 15 = Approved, 19 = Proceed to Meeting, 32 = Condition Satisfied
                if (in_array($status_id, ['8', '9', '19'])) {
                    if ($status_id != 8) {
                        if (empty($requestData['approved_duration_start_date']) || empty($requestData['approved_duration_end_date']) || empty($requestData['approved_desired_duration']) || empty($requestData['approved_duration_amount'])) {
                            Session::flash('error', 'Application duration not fill up.[PPC-1212]');
                            return false;
                        }
                    }

                    //  govt fees calculation and send mail.
                    $appInfo['approved_duration_start_date'] = (!empty($requestData['approved_duration_start_date']) ? date('Y-m-d', strtotime($requestData['approved_duration_start_date'])) : null);
                    $appInfo['approved_duration_end_date'] = (!empty($requestData['approved_duration_end_date']) ? date('Y-m-d', strtotime($requestData['approved_duration_end_date'])) : null);
                    $durationData = commonFunction::getDesiredDurationDiffDate($appInfo);
                    $approved_desired_duration = (string)$durationData['string'];
                    $appInfo['approve_duration_year'] = (int)$durationData['approve_duration_year'];
                    $appInfo['govt_fees'] = CommonFunction::getGovtFees($appInfo);
                    // end of gov fees calculation

                    $data = [];
                    $data['approved_is_remittance_allowed'] = (!empty($requestData['approved_is_remittance_allowed']) ? $requestData['approved_is_remittance_allowed'] : 'no');
                    $data['approved_duration_start_date'] = (!empty($requestData['approved_duration_start_date']) ? date('Y-m-d', strtotime($requestData['approved_duration_start_date'])) : null);
                    $data['approved_duration_end_date'] = (!empty($requestData['approved_duration_end_date']) ? date('Y-m-d', strtotime($requestData['approved_duration_end_date'])) : null);
                    $data['approved_desired_duration'] = $approved_desired_duration;
                    $data['approved_duration_amount'] = $appInfo['govt_fees'];

                    $output = OfficePermissionExtension::where('id', $process->ref_id)->update($data);
                    if (!$output) {
                        Session::flash('error', 'Application duration update error!.[PPC-1213]');
                        return false;
                    }

                    // Approved from Desk
                    //                if (in_array($status_id, ['15']) && $output) {
                    //                    CommonFunction::sendEmailSMS('APP_APPROVE_AND_PAYMENT', $appInfo, $applicantEmailPhone);
                    //                }

                } elseif ($status_id == 32) { // 32 = Condition Satisfied
                    $get_duration_data = OfficePermissionExtension::where('id', $process->ref_id)
                        ->first(['approved_duration_start_date', 'approved_duration_end_date']);

                    // govt fees calculation and send mail.
                    $appInfo['approved_duration_start_date'] = $get_duration_data->approved_duration_start_date;
                    $appInfo['approved_duration_end_date'] = $get_duration_data->approved_duration_end_date;
                    $durationData = commonFunction::getDesiredDurationDiffDate($appInfo);
                    $appInfo['approve_duration_year'] = (int)$durationData['approve_duration_year'];
                    $appInfo['govt_fees'] = CommonFunction::getGovtFees($appInfo);
                    // end of gov fees calculation

                    CommonFunction::sendEmailSMS('APP_CONDITION_SATISFIED_AND_PAYMENT', $appInfo, $applicantEmailPhone);
                } // Issued office permission from Desk(1)
                elseif ($status_id == 25) {

                    $ope_company_info = OfficePermissionExtension::where('id', $process->ref_id)->first(['local_company_name', 'local_company_name_bn']);

                    // check company is exist
                    $checkExitingCompany = CommonFunction::findCompanyNameWithoutWorkingID($ope_company_info->local_company_name, $process->company_id);
                    if (!$checkExitingCompany) {
                        Session::flash('error', 'Company name: "' . $ope_company_info->local_company_name . '" is already exist!');
                        return false;
                    }

                    // update application data
                    $data = [];
                    $data['approved_date'] = date('Y-m-d H:i:s');

                    $output = OfficePermissionExtension::where('id', $process->ref_id)->update($data);

                    // Update company name in company information and basic information table
                    $basic_app_id = ProcessList::leftjoin('ea_apps', 'ea_apps.id', '=', 'process_list.ref_id')
                        ->where('process_list.process_type_id', 100)
                        ->where('process_list.status_id', 25)
                        ->where('process_list.company_id', $process->company_id)
                        ->first(['process_list.ref_id']);

                    $ope_company_info_data = [];
                    $ope_company_info_data['company_name'] = $ope_company_info->local_company_name;
                    $ope_company_info_data['company_name_bn'] = $ope_company_info->local_company_name_bn;

                    CompanyInfo::where('id', $process->company_id)->update($ope_company_info_data);
                    BasicInformation::where('id', $basic_app_id->ref_id)->update($ope_company_info_data);

                    $certificateGenerate = $this->certificateGenerationRequest($process->ref_id, $process->process_type_id, $approver_desk_id, $process->department_id);
                    if ($output and $certificateGenerate) {  //return true
                        $appInfo['attachment_certificate_name'] = 'ope_apps.certificate_link';
                        CommonFunction::sendEmailSMS('APP_ISSUED_LETTER', $appInfo, $applicantEmailPhone);

                        $stakeholderEmailPhone = Stakeholder::where('process_type_id', $process->process_type_id)
                            ->where('department_id', $process->department_id)
                            ->where('status', 1)
                            ->get([
                                'email as user_email',
                                'phone as user_phone'
                            ]);

                        if (count($stakeholderEmailPhone) > 0) {
                            $applicationData = OfficePermissionExtension::find($process->ref_id);
                            $appInfo['organization_name'] = $applicationData->company_name;
                            CommonFunction::sendEmailSMS('OP_ISSUED_LETTER_STAKEHOLDER', $appInfo, $stakeholderEmailPhone);
                        }
                    }

                    $isDataStored = OPCommonPoolManager::OPEDataStore($process->tracking_no, $process->ref_id);
                    if ($isDataStored === false) {
                        return false;
                    }
                }
                return true;
                break;

            case 8: // Office Permission Amendment (8)

                // This will be uncommented if second time payment is required
                if (in_array($status_id, ['8', '9', '15', '19'])) {
                    // update approved effective date
                    $data = [];
                    $data['approved_effective_date'] = (!empty($requestData['approved_effective_date']) ? date('Y-m-d', strtotime($requestData['approved_effective_date'])) : '');
                    OfficePermissionAmendment::where('id', $process->ref_id)->update($data);

                    if ($status_id == 15) {
                        $appInfo['govt_fees'] = CommonFunction::getGovtFees($appInfo);
                        CommonFunction::sendEmailSMS('APP_APPROVE_AND_PAYMENT', $appInfo, $applicantEmailPhone);
                    }
                } elseif ($status_id == 25) {
                    // update application data
                    $data = [];
                    $data['approved_date'] = date('Y-m-d H:i:s');

                    $output = OfficePermissionAmendment::where('id', $process->ref_id)->update($data);

                    $certificateGenerate = $this->certificateGenerationRequest($process->ref_id, $process->process_type_id, $approver_desk_id, $process->department_id);
                    if ($output and $certificateGenerate) {  //return true
                        $appInfo['attachment_certificate_name'] = 'opa_apps.certificate_link';
                        CommonFunction::sendEmailSMS('APP_ISSUED_LETTER', $appInfo, $applicantEmailPhone);
                        //Email is not sending to stakeholder at issue letter for amendment as per discussion

                        $stakeholderEmailPhone = Stakeholder::where('process_type_id', $process->process_type_id)
                            ->where('department_id', $process->department_id)
                            ->where('status', 1)
                            ->get([
                                'email as user_email',
                                'phone as user_phone'
                            ]);

                        if (count($stakeholderEmailPhone) > 0) {
                            $applicationData = OfficePermissionAmendment::find($process->ref_id);
                            $appInfo['organization_name'] = $applicationData->local_company_name;
                            CommonFunction::sendEmailSMS('OP_ISSUED_LETTER_STAKEHOLDER', $appInfo, $stakeholderEmailPhone);
                        }
                    }
                    $isDataStored = OPCommonPoolManager::OPADataStore($process->tracking_no, $process->ref_id);
                    if ($isDataStored === false) {
                        return false;
                    }
                }
                return true;
                break;

            case 9: // Office Permission Cancellation, 8 = Observation, 9 = Verified, 15 = Approved, 19 = Proceed to Meeting
                if (in_array($status_id, ['8', '9', '15', '19'])) { // Approved from Meeting Chairperson or Director
                    if ($status_id != 8) {
                        if (empty($requestData['approved_effect_date'])) {
                            Session::flash('error', 'Application duration not fill up.[PPC-1214]');
                            return false;
                        }
                    }
                    $data = [];
                    $data['approved_effect_date'] = (!empty($requestData['approved_effect_date']) ? date('Y-m-d', strtotime($requestData['approved_effect_date'])) : '');
                    $output = OfficePermissionCancellation::where('id', $process->ref_id)->update($data);
                    if (!$output) {
                        Session::flash('error', 'Application duration update error!.[PPC-1213]');
                        return false;
                    }
                    if ($status_id == 15) {
                        CommonFunction::sendEmailSMS('APP_APPROVE_WITHOUT_LETTER', $appInfo, $applicantEmailPhone);
                    }
                } elseif ($status_id == 25) { // Issue Office Cancellation from Assistant Director
                    // update application data
                    $data = [];
                    $data['approved_date'] = date('Y-m-d H:i:s');

                    $output = OfficePermissionCancellation::where('id', $process->ref_id)->update($data);

                    $certificateGenerate = $this->certificateGenerationRequest($process->ref_id, $process->process_type_id, $approver_desk_id, $process->department_id);
                    if ($output && $certificateGenerate) {
                        $appInfo['attachment_certificate_name'] = 'opc_apps.certificate_link';
                        CommonFunction::sendEmailSMS('APP_ISSUED_LETTER', $appInfo, $applicantEmailPhone);

                        $stakeholderEmailPhone = Stakeholder::where('process_type_id', $process->process_type_id)
                            ->where('department_id', $process->department_id)
                            ->where('status', 1)
                            ->get([
                                'email as user_email',
                                'phone as user_phone'
                            ]);

                        if (count($stakeholderEmailPhone) > 0) {
                            $applicationData = OfficePermissionCancellation::find($process->ref_id);
                            $appInfo['organization_name'] = $applicationData->company_name;
                            CommonFunction::sendEmailSMS('OP_ISSUED_LETTER_STAKEHOLDER', $appInfo, $stakeholderEmailPhone);
                        }
                    }

                    $isDataStored = OPCommonPoolManager::OPCDataStore($process->tracking_no, $process->ref_id);
                    if ($isDataStored === false) {
                        return false;
                    }
                }

                //            elseif ($status_id == 5) {
                //                if (!empty($requestData['opc_shortfall_review_section'])) {
                //                    $data = [];
                //                    $review_section_array = [
                //                        'basic_instruction_review',
                //                        'office_type_review',
                //                        'company_info_review',
                //                        'capital_of_company_review',
                //                        'local_address_review',
                //                        'activities_in_bd_review',
                //                        'period_of_permission_review',
                //                        'organizational_set_up_review',
                //                        'expenses_review',
                //                        'attachment_review',
                //                        'declaration_review'
                //                    ];
                //                    foreach ($review_section_array as $key => $value) {
                //                        if (array_search($value, $requestData['opc_shortfall_review_section']) !== false) {
                //                            $data[$value] = 1;
                //                        } else {
                //                            $data[$value] = 0;
                //                        }
                //                    }
                //
                //                    OfficePermissionCancellation::where('id', $process->ref_id)->update($data);
                //                }
                //            }

                return true;
                break;

            case 19: // Waiver Condition 7 new 
                if ($status_id == 25) {

//                    $wvr_company_info = WaiverCondition7::where('id', $process->ref_id)->first(['local_company_name', 'local_company_name_bn']);
//
//                    // check company is exist
//                    $checkExitingCompany = CommonFunction::findCompanyNameWithoutWorkingID($wvr_company_info->local_company_name, $process->company_id);
//                    if (!$checkExitingCompany) {
//                        Session::flash('error', 'Company name: "' . $wvr_company_info->local_company_name . '" is already exist!');
//                        return false;
//                    }

                    // update application data
                    $data = [];
                    $data['approved_date'] = date('Y-m-d H:i:s');

                    $output = WaiverCondition7::where('id', $process->ref_id)->update($data);

                    // Update company name in company information and basic information table
//                    $basic_app_id = ProcessList::leftjoin('ea_apps', 'ea_apps.id', '=', 'process_list.ref_id')
//                        ->where('process_list.process_type_id', 100)
//                        ->where('process_list.status_id', 25)
//                        ->where('process_list.company_id', $process->company_id)
//                        ->first(['process_list.ref_id']);
//
//                    $wvr_company_info_data = [];
//                    $wvr_company_info_data['company_name'] = $wvr_company_info->local_company_name;
//                    $wvr_company_info_data['company_name_bn'] = $wvr_company_info->local_company_name_bn;
//
//                    CompanyInfo::where('id', $process->company_id)->update($wvr_company_info_data);
//                    BasicInformation::where('id', $basic_app_id->ref_id)->update($wvr_company_info_data);

                    $certificateGenerate = $this->certificateGenerationRequest($process->ref_id, $process->process_type_id, $approver_desk_id, $process->department_id);
                    if ($output && $certificateGenerate) {
                        $appInfo['attachment_certificate_name'] = 'waiver_con_7_apps.certificate_link';
                        CommonFunction::sendEmailSMS('APP_ISSUED_LETTER', $appInfo, $applicantEmailPhone);

                        $stakeholderEmailPhone = Stakeholder::where('process_type_id', $process->process_type_id)
                            ->where('department_id', $process->department_id)
                            ->where('status', 1)
                            ->get([
                                'email as user_email',
                                'phone as user_phone'
                            ]);

                        if (count($stakeholderEmailPhone) > 0) {
                            $applicationData = WaiverCondition7::find($process->ref_id);
                            $appInfo['organization_name'] = $applicationData->company_name;
                            CommonFunction::sendEmailSMS('OP_ISSUED_LETTER_STAKEHOLDER', $appInfo, $stakeholderEmailPhone);
                        }
                    }
                }
                return true;
                break;


            case 21: // Import Permission New
                if ($status_id == 25) {
                    // update application data
                    $data = [];
                    $data['approved_date'] = date('Y-m-d H:i:s');

                    $output = ImportPermission::where('id', $process->ref_id)->update($data);

                    $certificateGenerate = $this->certificateGenerationRequest($process->ref_id, $process->process_type_id, $approver_desk_id, $process->department_id);
                    if ($output && $certificateGenerate) {
                        $appInfo['attachment_certificate_name'] = 'ip_apps.certificate_link';
                        CommonFunction::sendEmailSMS('APP_ISSUED_LETTER', $appInfo, $applicantEmailPhone);

                        $stakeholderEmailPhone = Stakeholder::where('process_type_id', $process->process_type_id)
                            ->where('department_id', $process->department_id)
                            ->where('status', 1)
                            ->get([
                                'email as user_email',
                                'phone as user_phone'
                            ]);

                        if (count($stakeholderEmailPhone) > 0) {
                            $applicationData = ImportPermission::find($process->ref_id);
                            $appInfo['organization_name'] = $applicationData->company_name;
                            CommonFunction::sendEmailSMS('IP_ISSUED_LETTER_STAKEHOLDER', $appInfo, $stakeholderEmailPhone);
                        }
                    }

                    $ipRefAppTrackingNo = ImportPermission::where('id', $process->ref_id)->value('ref_app_tracking_no');
                    ImportPermissionController::IpMachineryImportedApproved($process->ref_id, $ipRefAppTrackingNo);
                }
                return true;
                break;

            case 20: // Waiver Condition 8
                if (in_array($status_id, ['13', '14'])) {
                    // Update application data
                    $data = [];
                    $data['memo_no'] = $requestData['memo_no'];
                    $data['memo_date'] = (!empty($requestData['memo_date']) ? date('Y-m-d', strtotime($requestData['memo_date'])) : null);

                    if (isset($requestData['memo_attachment'])) {
                        $file = $requestData['memo_attachment'];

                        if ($file->isValid()) {
                            // file upload directory will be uploads/year/month/file_name with extension
                            $original_file = $file->getClientOriginalName();
                            $file->move('uploads/', time() . $original_file);

                            // Store memo_attachment in $data array
                            $data['memo_attachment'] = 'uploads/' . time() . $original_file;
                        }
                    }

                    // Update memo_no and memo_date fields
                    WaiverCondition8::where('id', $process->ref_id)->update($data);
                } else if ($status_id == 25) {
                    // update application data
                    $data = [];
                    $data['approved_date'] = date('Y-m-d H:i:s');
                    $output = WaiverCondition8::where('id', $process->ref_id)->update($data);

                    $certificateGenerate = $this->certificateGenerationRequest($process->ref_id, $process->process_type_id, $approver_desk_id, $process->department_id);
                    if ($output && $certificateGenerate) {
                        $appInfo['attachment_certificate_name'] = 'waiver_con_8_apps.certificate_link';
                        CommonFunction::sendEmailSMS('APP_ISSUED_LETTER', $appInfo, $applicantEmailPhone);

                        $stakeholderEmailPhone = Stakeholder::where('process_type_id', $process->process_type_id)
                            ->where('department_id', $process->department_id)
                            ->where('status', 1)
                            ->get([
                                'email as user_email',
                                'phone as user_phone'
                            ]);

                        if (count($stakeholderEmailPhone) > 0) {
                            $applicationData = WaiverCondition8::find($process->ref_id);
                            $appInfo['organization_name'] = $applicationData->company_name;
                            CommonFunction::sendEmailSMS('WVR8_ISSUED_LETTER_STAKEHOLDER', $appInfo, $stakeholderEmailPhone);
                        }
                    }
                }

                return true;
                break;

            case 10: // Visa Recommendation Amendment (10)
                if ($status_id == 15) {
                    $appInfo['govt_fees'] = CommonFunction::getGovtFees($appInfo);
                    CommonFunction::sendEmailSMS('APP_APPROVE_AND_PAYMENT', $appInfo, $applicantEmailPhone);
                } elseif ($status_id == 25) {
                    // update application data
                    $data = [];
                    $data['approved_date'] = date('Y-m-d H:i:s');
                    $output = VisaRecommendationAmendment::where('id', $process->ref_id)->update($data);

                    $certificateGenerate = $this->certificateGenerationRequest($process->ref_id, $process->process_type_id, $approver_desk_id, $process->department_id);
                    if ($output && $certificateGenerate) {
                        $appInfo['attachment_certificate_name'] = 'vra_apps.certificate_link';
                        CommonFunction::sendEmailSMS('APP_ISSUED_LETTER', $appInfo, $applicantEmailPhone);

                        $applicationData = VisaRecommendationAmendment::leftJoin('dept_application_type', 'dept_application_type.id', '=', 'vra_apps.app_type_id')
                            ->leftJoin('country_info', 'country_info.id', '=', 'vra_apps.emp_nationality_id')
                            ->leftJoin('high_comissions', 'high_comissions.id', '=', 'vra_apps.n_high_commision_id')
                            ->where('vra_apps.id', $process->ref_id)
                            ->first([
                                'vra_apps.id',
                                'vra_apps.app_type_id',
                                'vra_apps.high_commision_id as high_commission_id',
                                'vra_apps.n_high_commision_id as n_high_commission_id',
                                'high_comissions.name as high_commission_name',
                                'high_comissions.address as high_commission_address',
                                'vra_apps.n_mission_country_id',
                                'vra_apps.airport_id',
                                'vra_apps.n_airport_id',
                                'vra_apps.emp_name as name',
                                'vra_apps.emp_passport_no as passport_number',
                                'vra_apps.emp_designation as designation',
                                'country_info.nationality',
                                'dept_application_type.name as visa_type',
                            ]);

                        $appInfo['name'] = $applicationData->name;
                        $appInfo['nationality'] = $applicationData->nationality;
                        $appInfo['passport_number'] = $applicationData->passport_number;
                        $appInfo['designation'] = $applicationData->designation;
                        $appInfo['visa_type'] = $applicationData->visa_type;
                        $appInfo['high_commission_name'] = $applicationData->high_commission_name;
                        $appInfo['high_commission_address'] = $applicationData->high_commission_address;

                        // If embassy/ high commission is changed then send email both embassy/ high commission
                        if ($applicationData->app_type_id != 5 && !empty($applicationData->n_mission_country_id) && !empty($applicationData->n_high_commission_id)) {
                            $embassyEmailPhone = HighComissions::whereIn('id', [$applicationData->high_commission_id, $applicationData->n_high_commission_id])
                                ->get([
                                    'email as user_email',
                                    'phone as user_phone'
                                ]);
                            if (count($embassyEmailPhone) > 0) {
                                CommonFunction::sendEmailSMS('EMBASSY_HIGH_COMMISSION', $appInfo, $embassyEmailPhone);
                            }
                        }

                        //                    if ($applicationData->app_type_id == 5) { // Visa on arrival
                        //                        $airportEmailPhone = Airports::whereIn('id', [$applicationData->airport_id,$applicationData->n_airport_id])
                        //                            ->get([
                        //                                'email as user_email',
                        //                                'phone as user_phone'
                        //                            ]);
                        //                        if (count($airportEmailPhone) > 0)
                        //                            CommonFunction::sendEmailSMS('IMMIGRATION', $appInfo, $airportEmailPhone);
                        //                    }

                        $stakeholderEmailPhone = Stakeholder::where('process_type_id', $process->process_type_id)
                            ->where('department_id', $process->department_id)
                            ->where('status', 1)
                            ->get([
                                'email as user_email',
                                'phone as user_phone'
                            ]);

                        if (count($stakeholderEmailPhone) > 0) {
                            CommonFunction::sendEmailSMS('VRA_ISSUED_LETTER_STAKEHOLDER', $appInfo, $stakeholderEmailPhone);
                        }
                    }

                    //store VR common pool information
                    VRCommonPoolManager::VRADataStore($process->tracking_no, $process->ref_id);
                }
                return true;
                break;

            case 11: // Remittance Approval new (11)
                if ($status_id == 15) {
                    $appInfo['govt_fees'] = CommonFunction::getGovtFees($appInfo);
                    CommonFunction::sendEmailSMS('APP_APPROVE_AND_PAYMENT', $appInfo, $applicantEmailPhone);
                } elseif ($status_id == 32) { // 32 = Condition Satisfied
                    $appInfo['govt_fees'] = CommonFunction::getGovtFees($appInfo);
                    CommonFunction::sendEmailSMS('APP_CONDITION_SATISFIED_AND_PAYMENT', $appInfo, $applicantEmailPhone);
                } elseif ($status_id == '18') { // 18 = Payment Confirm
                    // Store approver information
                    storeSignatureQRCode($process->process_type_id, $process->ref_id, 0, $approver_desk_id, 'first');
                    // qr code
                } elseif ($status_id == 25) {
                    $data = [];
                    $data['approved_date'] = date('Y-m-d H:i:s');

                    $output = Remittance::where('id', $process->ref_id)->update($data);

                    $certificateGenerate = $this->certificateGenerationRequest($process->ref_id, $process->process_type_id, $approver_desk_id, $process->department_id);

                    if ($output && $certificateGenerate) {
                        $appInfo['attachment_certificate_name'] = 'ra_apps.certificate_link';
                        CommonFunction::sendEmailSMS('APP_ISSUED_LETTER', $appInfo, $applicantEmailPhone);

                        $stakeholderEmailPhone = Stakeholder::where('process_type_id', $process->process_type_id)
                            ->where('department_id', $process->department_id)
                            ->where('status', 1)
                            ->get([
                                'email as user_email',
                                'phone as user_phone'
                            ]);

                        if (count($stakeholderEmailPhone) > 0) {
                            CommonFunction::sendEmailSMS('REMITTANCE_ISSUED_LETTER_STAKEHOLDER', $appInfo, $stakeholderEmailPhone);
                        }
                    }
                }
                return true;
                break;

            case 12: // BIDA Registration Amendment
                if ($status_id == 15) {
                    $appInfo['govt_fees'] = CommonFunction::getGovtFees($appInfo);
                    CommonFunction::sendEmailSMS('APP_APPROVE_AND_PAYMENT', $appInfo, $applicantEmailPhone);
                } elseif ($status_id == 25) {

                    // update application data
                    $data = [];
                    $data['approved_date'] = date('Y-m-d H:i:s');
                    $output = BidaRegistrationAmendment::where('id', $process->ref_id)->update($data);

                    // Generate Registration number
                    $bRegistration = new BidaRegistrationAmendmentController();
                    $bRegistration->RegNoGenerate($process->ref_id, $process->approval_center_id);

                    $certificateGenerate = $this->certificateGenerationRequest($process->ref_id, $process->process_type_id, $approver_desk_id);
                    // $listOfDirectorAndMechineryGenerate = UtilFunction::getBRAListOfDirectorsAndMachinery($process->ref_id, $process->process_type_id, 'F');

                    // if ($output && $output3 && !empty($listOfDirectorAndMechineryGenerate) && $certificateGenerate) {  //return true

                    if ($output && $certificateGenerate) {

                        // $appInfo['attachment'] = $_SERVER['DOCUMENT_ROOT'] . '/' . $listOfDirectorAndMechineryGenerate;
                        $appInfo['attachment_certificate_name'] = 'bra_apps.certificate_link';
                        CommonFunction::sendEmailSMS('APP_APPROVE', $appInfo, $applicantEmailPhone);

                        $stakeholderEmailPhone = Stakeholder::where('process_type_id', $process->process_type_id)
                            ->where('department_id', $process->department_id)
                            ->where('status', 1)
                            ->get([
                                'email as user_email',
                                'phone as user_phone'
                            ]);

                        if (count($stakeholderEmailPhone) > 0) {
                            CommonFunction::sendEmailSMS('BR_ISSUED_LETTER_STAKEHOLDER', $appInfo, $stakeholderEmailPhone);
                        }
                    }

                    //store BR common pool information
                    BRCommonPoolManager::BRADataStore($process->tracking_no, $process->ref_id);

                    BRCommonPoolManager::BRAMachineryDataStore($process->tracking_no, $process->ref_id);
                }
                return true;
                break;

            case 13: // IRC 1st adhoc

                //            if ($status_id == '15') {
                //                $appInfo['govt_fees'] = CommonFunction::getGovtFees($appInfo);
                //                CommonFunction::sendEmailSMS('APP_APPROVE_AND_PAYMENT', $appInfo, $applicantEmailPhone);
                //            }


                if ($status_id == 40) {

                    $io_submission_deadline = (!empty($requestData['io_submission_deadline']) ? date('Y-m-d', strtotime($requestData['io_submission_deadline'])) : '');
                    $output = IrcRecommendationNew::where('id', $process->ref_id)
                        ->update([
                            'io_submission_deadline' => $io_submission_deadline
                        ]);
                    if (!$output) {
                        Session::flash('error', 'Inspection submission deadline date error!.[PPC-1221]');
                        return false;
                    }

                    $applicationData = IrcRecommendationNew::leftJoin('irc_types', 'irc_apps.app_type_id', '=', 'irc_types.id')
                        ->Where('irc_apps.id', $process->ref_id)
                        ->first(['irc_apps.company_name', 'irc_types.type']);

                    $desk_user_info = Users::where('id', $process->user_id)
                        ->get([
                            'user_first_name', 'user_middle_name', 'user_last_name', 'designation', 'user_phone', 'user_email'
                        ]);

                    $appInfo['irc_type'] = $applicationData->type;
                    $appInfo['organization_name'] = $applicationData->company_name;
                    $appInfo['ins_officer_name'] = $desk_user_info[0]->user_first_name . ' ' . $desk_user_info[0]->user_middle_name . ' ' . $desk_user_info[0]->user_last_name;
                    $appInfo['ins_officer_designation'] = $desk_user_info[0]->designation;
                    $appInfo['ins_officer_phone_no'] = $desk_user_info[0]->user_phone;
                    $appInfo['ins_officer_email'] = $desk_user_info[0]->user_email;
                    $appInfo['io_submission_deadline'] = $io_submission_deadline;

                    // send mail
                    CommonFunction::sendEmailSMS('IRC_IO_ASSIGN_APPLICANT_CONTENT', $appInfo, $applicantEmailPhone);
                    CommonFunction::sendEmailSMS('IRC_IO_ASSIGN_DESK_CONTENT', $appInfo, $desk_user_info);
                }

                // This code will be removed after process button disabled code successful run
                //            elseif ($status_id == '41' && $process->user_id == 0) { // Field visit
                //                ProcessList::where('id', $process_list_id)->update(['user_id' => Auth::user()->id]);
                //            }

                elseif ($status_id == 42) {
                    //store inspection information here
                    inspectionStore($requestData);
                } elseif (in_array($status_id, ['8', '9'])) {
                    $deputy_director_info_update = IrcInspection::where('app_id', $process->ref_id)
                        ->orderBy('id', 'desc')
                        ->first();
                    $deputy_director_info_update->dd_name = CommonFunction::getUserFullName();
                    $deputy_director_info_update->dd_designation = Auth::user()->designation;
                    $deputy_director_info_update->dd_mobile_no = Auth::user()->user_phone;
                    $deputy_director_info_update->dd_email = Auth::user()->user_email;
                    $deputy_director_info_update->dd_signature = Auth::user()->signature;
                    $deputy_director_info_update->save();
                } elseif ($status_id == 25) {

                    if (empty($requestData['ins_approved_id'])) {
                        Session::flash('error', 'Inspection report not approved. [PPC-1232]');
                        return false;
                    }

                    // Update approved inspection data
                    $decode_ins_approved_id = Encryption::decodeId($requestData['ins_approved_id']);
                    $inspection = IrcInspection::where(['id' => $decode_ins_approved_id, 'app_id' => $process->ref_id])->first();
                    $inspection->ins_approved_status = 1;
                    $inspection->save();
                    // $inspection = IrcInspection::where(['id' => $decode_ins_approved_id, 'app_id' => $process->ref_id])->update(['ins_approved_status' => 1]);

                    // update application data
                    $data = [];
                    $data['approved_date'] = date('Y-m-d H:i:s');
                    $output = IrcRecommendationNew::where('id', $process->ref_id)->update($data);

                    $certificateGenerate = $this->certificateGenerationRequest($process->ref_id, $process->process_type_id, $approver_desk_id);

                    if ($output && $certificateGenerate) {
                        //$appInfo['attachment_certificate_name'] = 'irc_apps.certificate_link';
                        //CommonFunction::sendEmailSMS('APP_APPROVE', $appInfo, $applicantEmailPhone);

                        $inspection_amount = 0;
                        if ($inspection->irc_purpose_id == 1) {
                            $inspection_amount = $inspection->apc_half_yearly_import_total;
                        } elseif ($inspection->irc_purpose_id == 2) {
                            $inspection_amount = $inspection->em_lc_total_five_percent;
                        } elseif ($inspection->irc_purpose_id == 3) {
                            $inspection_amount = ($inspection->apc_half_yearly_import_total + $inspection->em_lc_total_five_percent);
                        }

                        $appInfo['inspection_amount'] = $inspection_amount;
                        CommonFunction::sendEmailSMS('APP_APPROVE_EXCEPT_IRC_APPROVAL_COPY', $appInfo, $applicantEmailPhone);
                    }
                    IRCCommonPoolManager::ircFirstAdhocDataStore($process->tracking_no, $process->ref_id);
                }
                return true;
                break;

            case 14: // IRC 2nd adhoc
                if ($status_id == 40) {

                    $io_submission_deadline = (!empty($requestData['io_submission_deadline']) ? date('Y-m-d', strtotime($requestData['io_submission_deadline'])) : '');
                    $output = IrcRecommendationSecondAdhoc::where('id', $process->ref_id)
                        ->update([
                            'io_submission_deadline' => $io_submission_deadline
                        ]);
                    if (!$output) {
                        Session::flash('error', 'Inspection submission deadline date error!.[PPC-1221]');
                        return false;
                    }

                    $applicationData = IrcRecommendationSecondAdhoc::leftJoin('irc_types', 'irc_2nd_apps.app_type_id', '=', 'irc_types.id')
                        ->Where('irc_2nd_apps.id', $process->ref_id)
                        ->first(['irc_2nd_apps.company_name', 'irc_types.type']);

                    $desk_user_info = Users::where('id', $process->user_id)
                        ->get([
                            'user_first_name', 'user_middle_name', 'user_last_name', 'designation', 'user_phone', 'user_email'
                        ]);

                    $appInfo['irc_type'] = $applicationData->type;
                    $appInfo['organization_name'] = $applicationData->company_name;
                    $appInfo['ins_officer_name'] = $desk_user_info[0]->user_first_name . ' ' . $desk_user_info[0]->user_middle_name . ' ' . $desk_user_info[0]->user_last_name;
                    $appInfo['ins_officer_designation'] = $desk_user_info[0]->designation;
                    $appInfo['ins_officer_phone_no'] = $desk_user_info[0]->user_phone;
                    $appInfo['ins_officer_email'] = $desk_user_info[0]->user_email;
                    $appInfo['io_submission_deadline'] = $io_submission_deadline;

                    // send mail
                    CommonFunction::sendEmailSMS('IRC_IO_ASSIGN_APPLICANT_CONTENT', $appInfo, $applicantEmailPhone);
                    CommonFunction::sendEmailSMS('IRC_IO_ASSIGN_DESK_CONTENT', $appInfo, $desk_user_info);
                } elseif ($status_id == 42) {
                    //store inspection information here
                    inspectionStore($requestData);
                } elseif (in_array($status_id, ['8', '9'])) {
                    $deputy_director_info_update = SecondIrcInspection::where('app_id', $process->ref_id)
                        ->orderBy('id', 'desc')
                        ->first();

                    if (!empty($deputy_director_info_update)) {
                        $deputy_director_info_update->dd_name = CommonFunction::getUserFullName();
                        $deputy_director_info_update->dd_designation = Auth::user()->designation;
                        $deputy_director_info_update->dd_mobile_no = Auth::user()->user_phone;
                        $deputy_director_info_update->dd_email = Auth::user()->user_email;
                        $deputy_director_info_update->dd_signature = Auth::user()->signature;
                        $deputy_director_info_update->save();
                    }
                } elseif ($status_id == 25) {
                    if (!empty($requestData['ins_approved_id'])) {
                        $decode_ins_approved_id = Encryption::decodeId($requestData['ins_approved_id']);

                        $getIRCSecondInspectionData = SecondIrcInspection::where(['id' => $decode_ins_approved_id, 'app_id' => $process->ref_id])->first();

                        IrcRecommendationSecondAdhoc::where('id', $process->ref_id)->update([
                            'second_em_lc_total_taka_mil' => $getIRCSecondInspectionData->em_lc_total_taka_mil,
                            'second_em_lc_total_percent' => $getIRCSecondInspectionData->em_lc_total_percent,
                            'second_em_lc_total_five_percent' => $getIRCSecondInspectionData->em_lc_total_five_percent,
                            'second_em_lc_total_five_percent_in_word' => $getIRCSecondInspectionData->em_lc_total_five_percent_in_word
                        ]);

                        $getIRCSecondInspectionData->ins_approved_status = 1;
                        $getIRCSecondInspectionData->save();
                    }

                    // update application data
                    $data = [];
                    $data['approved_date'] = date('Y-m-d H:i:s');
                    $output = IrcRecommendationSecondAdhoc::where('id', $process->ref_id)->update($data);

                    $certificateGenerate = $this->certificateGenerationRequest($process->ref_id, $process->process_type_id, $approver_desk_id);

                    if ($output && $certificateGenerate) {  //return true
                        $appInfo['attachment_certificate_name'] = 'irc_2nd_apps.certificate_link';
                        CommonFunction::sendEmailSMS('APP_APPROVE', $appInfo, $applicantEmailPhone);
                    }
                    IRCCommonPoolManager::ircSecondAdhocDataStore($process->tracking_no, $process->ref_id);
                }
                return true;
                break;

            case 15: // IRC 3rd adhoc

                if ($status_id == 40) {

                    $io_submission_deadline = (!empty($requestData['io_submission_deadline']) ? date('Y-m-d', strtotime($requestData['io_submission_deadline'])) : '');
                    $output = IrcRecommendationThirdAdhoc::where('id', $process->ref_id)
                        ->update([
                            'io_submission_deadline' => $io_submission_deadline
                        ]);
                    if (!$output) {
                        Session::flash('error', 'Inspection submission deadline date error!.[PPC-1221]');
                        return false;
                    }

                    $applicationData = IrcRecommendationThirdAdhoc::leftJoin('irc_types', 'irc_3rd_apps.app_type_id', '=', 'irc_types.id')
                        ->Where('irc_3rd_apps.id', $process->ref_id)
                        ->first(['irc_3rd_apps.company_name', 'irc_types.type']);

                    $desk_user_info = Users::where('id', $process->user_id)
                        ->get([
                            'user_first_name', 'user_middle_name', 'user_last_name', 'designation', 'user_phone', 'user_email'
                        ]);

                    $appInfo['irc_type'] = $applicationData->type;
                    $appInfo['organization_name'] = $applicationData->company_name;
                    $appInfo['ins_officer_name'] = $desk_user_info[0]->user_first_name . ' ' . $desk_user_info[0]->user_middle_name . ' ' . $desk_user_info[0]->user_last_name;
                    $appInfo['ins_officer_designation'] = $desk_user_info[0]->designation;
                    $appInfo['ins_officer_phone_no'] = $desk_user_info[0]->user_phone;
                    $appInfo['ins_officer_email'] = $desk_user_info[0]->user_email;
                    $appInfo['io_submission_deadline'] = $io_submission_deadline;

                    // send mail
                    CommonFunction::sendEmailSMS('IRC_IO_ASSIGN_APPLICANT_CONTENT', $appInfo, $applicantEmailPhone);
                    CommonFunction::sendEmailSMS('IRC_IO_ASSIGN_DESK_CONTENT', $appInfo, $desk_user_info);
                } elseif ($status_id == 42) {
                    //store inspection information here
                    inspectionStore($requestData);
                } elseif (in_array($status_id, ['8', '9'])) {
                    $deputy_director_info_update = ThirdIrcInspection::where('app_id', $process->ref_id)
                        ->orderBy('id', 'desc')
                        ->first();

                    if (!empty($deputy_director_info_update)) {
                        $deputy_director_info_update->dd_name = CommonFunction::getUserFullName();
                        $deputy_director_info_update->dd_designation = Auth::user()->designation;
                        $deputy_director_info_update->dd_mobile_no = Auth::user()->user_phone;
                        $deputy_director_info_update->dd_email = Auth::user()->user_email;
                        $deputy_director_info_update->dd_signature = Auth::user()->signature;
                        $deputy_director_info_update->save();
                    }
                } elseif ($status_id == 25) {

                    if (!empty($requestData['ins_approved_id'])) {
                        $decode_ins_approved_id = Encryption::decodeId($requestData['ins_approved_id']);

                        $getIRCThirdInspectionData = ThirdIrcInspection::where(['id' => $decode_ins_approved_id, 'app_id' => $process->ref_id])->first();

                        IrcRecommendationThirdAdhoc::where('id', $process->ref_id)->update([
                            'second_em_lc_total_taka_mil' => $getIRCThirdInspectionData->em_lc_total_taka_mil,
                            'second_em_lc_total_percent' => $getIRCThirdInspectionData->em_lc_total_percent,
                            'second_em_lc_total_five_percent' => $getIRCThirdInspectionData->em_lc_total_five_percent,
                            'second_em_lc_total_five_percent_in_word' => $getIRCThirdInspectionData->em_lc_total_five_percent_in_word
                        ]);

                        $getIRCThirdInspectionData->ins_approved_status = 1;
                        $getIRCThirdInspectionData->save();
                    }

                    // update application data
                    $data = [];
                    $data['approved_date'] = date('Y-m-d H:i:s');
                    $output = IrcRecommendationThirdAdhoc::where('id', $process->ref_id)->update($data);

                    $certificateGenerate = $this->certificateGenerationRequest($process->ref_id, $process->process_type_id, $approver_desk_id);

                    if ($output && $certificateGenerate) {  //return true
                        $appInfo['attachment_certificate_name'] = 'irc_3rd_apps.certificate_link';
                        CommonFunction::sendEmailSMS('APP_APPROVE', $appInfo, $applicantEmailPhone);
                    }

                    IRCCommonPoolManager::ircThirdAdhocDataStore($process->tracking_no, $process->ref_id);
                }
                return true;
                break;

            case 16: // IRC Regular

                if ($status_id == 40) {
                    $io_submission_deadline = (!empty($requestData['io_submission_deadline']) ? date('Y-m-d', strtotime($requestData['io_submission_deadline'])) : '');
                    $output = IrcRecommendationRegular::where('id', $process->ref_id)
                        ->update([
                            'io_submission_deadline' => $io_submission_deadline
                        ]);
                    if (!$output) {
                        Session::flash('error', 'Inspection submission deadline date error!.[PPC-12211]');
                        return false;
                    }

                    $applicationData = IrcRecommendationRegular::leftJoin('irc_types', 'irc_regular_apps.app_type_id', '=', 'irc_types.id')
                        ->Where('irc_regular_apps.id', $process->ref_id)
                        ->first(['irc_regular_apps.company_name', 'irc_types.type']);

                    $desk_user_info = Users::where('id', $process->user_id)
                        ->get([
                            'user_first_name', 'user_middle_name', 'user_last_name', 'designation', 'user_phone', 'user_email'
                        ]);

                    $appInfo['irc_type'] = $applicationData->type;
                    $appInfo['organization_name'] = $applicationData->company_name;
                    $appInfo['ins_officer_name'] = $desk_user_info[0]->user_first_name . ' ' . $desk_user_info[0]->user_middle_name . ' ' . $desk_user_info[0]->user_last_name;
                    $appInfo['ins_officer_designation'] = $desk_user_info[0]->designation;
                    $appInfo['ins_officer_phone_no'] = $desk_user_info[0]->user_phone;
                    $appInfo['ins_officer_email'] = $desk_user_info[0]->user_email;
                    $appInfo['io_submission_deadline'] = $io_submission_deadline;

                    // send mail
                    CommonFunction::sendEmailSMS('IRC_IO_ASSIGN_APPLICANT_CONTENT', $appInfo, $applicantEmailPhone);
                    CommonFunction::sendEmailSMS('IRC_IO_ASSIGN_DESK_CONTENT', $appInfo, $desk_user_info);
                } elseif ($status_id == 42) {
                    //store inspection information here
                    inspectionStore($requestData);
                } elseif (in_array($status_id, ['8', '9'])) {
                    $deputy_director_info_update = ThirdIrcInspection::where('app_id', $process->ref_id)
                        ->orderBy('id', 'desc')
                        ->first();

                    if (!empty($deputy_director_info_update)) {
                        $deputy_director_info_update->dd_name = CommonFunction::getUserFullName();
                        $deputy_director_info_update->dd_designation = Auth::user()->designation;
                        $deputy_director_info_update->dd_mobile_no = Auth::user()->user_phone;
                        $deputy_director_info_update->dd_email = Auth::user()->user_email;
                        $deputy_director_info_update->dd_signature = Auth::user()->signature;
                        $deputy_director_info_update->save();
                    }
                } elseif ($status_id == 25) {

                    if (!empty($requestData['ins_approved_id'])) {
                        $decode_ins_approved_id = Encryption::decodeId($requestData['ins_approved_id']);

                        $getIRCRegularInspectionData = RegularIrcInspection::where(['id' => $decode_ins_approved_id, 'app_id' => $process->ref_id])->first();
                        IrcRecommendationRegular::where('id', $process->ref_id)->update([
                            'second_em_lc_total_taka_mil' => $getIRCRegularInspectionData->em_lc_total_taka_mil,
                            'second_em_lc_total_percent' => $getIRCRegularInspectionData->em_lc_total_percent,
                            'second_em_lc_total_five_percent' => $getIRCRegularInspectionData->em_lc_total_five_percent,
                            'second_em_lc_total_five_percent_in_word' => $getIRCRegularInspectionData->em_lc_total_five_percent_in_word
                        ]);

                        $getIRCRegularInspectionData->ins_approved_status = 1;
                        $getIRCRegularInspectionData->save();
                    }

                    // update application data
                    $data = [];
                    $data['approved_date'] = date('Y-m-d H:i:s');
                    $output = IrcRecommendationRegular::where('id', $process->ref_id)->update($data);

                    $certificateGenerate = $this->certificateGenerationRequest($process->ref_id, $process->process_type_id, $approver_desk_id);

                    if ($output && $certificateGenerate) {  //return true
                        $appInfo['attachment_certificate_name'] = 'irc_regular_apps.certificate_link';
                        CommonFunction::sendEmailSMS('APP_APPROVE', $appInfo, $applicantEmailPhone);
                    }

                    // need to update common pool
                    IRCCommonPoolManager::ircRegularDataStore($process->tracking_no, $process->ref_id);
                }
                return true;
                break;

            case 17:
                // VIP Lounge
                if ($status_id == 25) {
                    // update application data
                    $approved_datetime = date('Y-m-d H:i:s');
                    $output = VipLounge::where('id', $process->ref_id)->update(['approved_date' => $approved_datetime]);

                    $certificateGenerate = $this->certificateGenerationRequest($process->ref_id, $process->process_type_id, $approver_desk_id);

                    if ($output && $certificateGenerate) {
                        $applicationData = VipLounge::where('vipl_apps.id', $process->ref_id)
                            ->first([
                                'vipl_apps.id',
                                'vipl_apps.airport_id',
                                'vipl_apps.emp_name as name',
                                'vipl_apps.emp_designation as designation',
                                'vipl_apps.company_name as company_name',
                            ]);

                        $appInfo['name'] = $applicationData->name;
                        $appInfo['designation'] = $applicationData->designation;
                        $appInfo['company_name'] = $applicationData->company_name;
                        $appInfo['approved_date'] = $approved_datetime;
                        $appInfo['attachment_certificate_name'] = 'vipl_apps.certificate_link';

                        CommonFunction::sendEmailSMS('APP_APPROVE', $appInfo, $applicantEmailPhone);

                        if ($applicationData->airport_id) {
                            $airportEmailPhone = Airports::where('id', $applicationData->airport_id)
                                ->get([
                                    'email as user_email',
                                    'phone as user_phone'
                                ]);
                            if (count($airportEmailPhone) > 0) {
                                CommonFunction::sendEmailSMS('VIPL_IMMIGRATION', $appInfo, $airportEmailPhone);
                            }
                        }

                        $stakeholderEmailPhone = Stakeholder::where('process_type_id', $process->process_type_id)
                            ->where('department_id', $process->department_id)
                            ->where('status', 1)
                            ->get([
                                'email as user_email',
                                'phone as user_phone'
                            ]);
                        if (count($stakeholderEmailPhone) > 0) {
                            CommonFunction::sendEmailSMS('VIPL_ISSUED_LETTER_STAKEHOLDER', $appInfo, $stakeholderEmailPhone);
                        }
                    }
                }

                return true;
                break;

            case 102: // BIDA Registration
                if ($status_id == 15) {
                    $appInfo['govt_fees'] = CommonFunction::getGovtFees($appInfo);
                    CommonFunction::sendEmailSMS('APP_APPROVE_AND_PAYMENT', $appInfo, $applicantEmailPhone);
                } elseif ($status_id == 25) {
                    // update application data
                    $data = [];
                    $data['approved_date'] = date('Y-m-d H:i:s');
                    $output = BidaRegistration::where('id', $process->ref_id)->update($data);
                    // Generate Registration number
                    $bRegistration = new BidaRegistrationController();
                    $bRegistration->RegNoGenerate($process->ref_id, $process->approval_center_id);

                    $certificateGenerate = $this->certificateGenerationRequest($process->ref_id, $process->process_type_id, $approver_desk_id);
                    // $listOfDirectorAndMechineryGenerate = UtilFunction::getListOfDirectorsAndMachinery($process->ref_id, $process->process_type_id, "F");

                    // if ($output && $output3 && !empty($listOfDirectorAndMechineryGenerate) && $certificateGenerate) {  //return true

                    if ($output && $certificateGenerate) {  //return true

                        // $appInfo['attachment'] = $_SERVER['DOCUMENT_ROOT'] . '/' . $listOfDirectorAndMechineryGenerate;
                        $appInfo['attachment_certificate_name'] = 'br_apps.certificate_link';
                        CommonFunction::sendEmailSMS('APP_APPROVE', $appInfo, $applicantEmailPhone);

                        $stakeholderEmailPhone = Stakeholder::where('process_type_id', $process->process_type_id)
                            ->where('department_id', $process->department_id)
                            ->where('status', 1)
                            ->get([
                                'email as user_email',
                                'phone as user_phone'
                            ]);

                        if (count($stakeholderEmailPhone) > 0) {
                            CommonFunction::sendEmailSMS('BR_ISSUED_LETTER_STAKEHOLDER', $appInfo, $stakeholderEmailPhone);
                        }
                    }

                    //store BR common pool information
                    BRCommonPoolManager::BRDataStore($process->tracking_no, $process->ref_id);

                    BRCommonPoolManager::BRMachineryDataStore($process->tracking_no, $process->ref_id);


                    // } elseif ($status_id == 5) {
                } elseif (in_array($status_id, [5, 8])) {
                    if (!empty($requestData['br_shortfall_review_section'])) {
                        $data = [];
                        $review_section_array = [
                            'company_info_review',
                            'promoter_info_review',
                            'office_address_review',
                            'factory_address_review',
                            'project_status_review',
                            'production_capacity_review',
                            'commercial_operation_review',
                            'sales_info_review',
                            'manpower_review',
                            'investment_review',
                            'source_finance_review',
                            'utility_service_review',
                            'trade_license_review',
                            'tin_review',
                            'machinery_equipment_review',
                            'raw_materials_review',
                            'ceo_info_review',
                            'director_list_review',
                            'imported_machinery_review',
                            'local_machinery_review',
                            'attachment_review',
                            'declaration_review'
                        ];

                        foreach ($review_section_array as $key => $value) {
                            if (array_search($value, $requestData['br_shortfall_review_section']) !== false) {
                                $data[$value] = 1;
                            } else {
                                $data[$value] = 0;
                            }
                        }

                        if ($data['project_status_review'] === 1) {
                            $data['commercial_operation_review'] = 1;
                        }
                        if ($data['investment_review'] === 1) {
                            $data['source_finance_review'] = 1;
                        }
                        if ($data['imported_machinery_review'] === 1) {
                            $data['local_machinery_review'] = 1;
                        }

                        BidaRegistration::where('id', $process->ref_id)->update($data);
                    }
                }
                return true;
                break;
                
            default:
                Session::flash('error', 'Unknown process type for Certificate and Others.[PPC-1200]');
                return false;
                break;
        } // ending of switch case
    }
}
