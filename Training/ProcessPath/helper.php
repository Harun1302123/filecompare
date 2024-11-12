<?php

use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Modules\IrcRecommendationNew\Models\InspectionAnnualProduction;
use App\Modules\IrcRecommendationNew\Models\IrcInspection;
use App\Modules\IrcRecommendationNew\Models\IrcRecommendationNew;
use App\Modules\IrcRecommendationSecondAdhoc\Models\IrcRecommendationSecondAdhoc;
use App\Modules\IrcRecommendationSecondAdhoc\Models\SecondInspectionAnnualProduction;
use App\Modules\IrcRecommendationSecondAdhoc\Models\SecondIrcInspection;
use App\Modules\IrcRecommendationThirdAdhoc\Models\IrcRecommendationThirdAdhoc;
use App\Modules\IrcRecommendationRegular\Models\IrcRecommendationRegular;
use App\Modules\IrcRecommendationRegular\Models\RegularIrcInspection;
use App\Modules\IrcRecommendationRegular\Models\RegularInspectionAnnualProduction;
use App\Modules\IrcRecommendationThirdAdhoc\Models\ThirdInspectionAnnualProduction;
use App\Modules\IrcRecommendationThirdAdhoc\Models\ThirdIrcInspection;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\Settings\Models\pdfSignatureQrcode;
use App\Modules\Users\Models\Users;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

function getDataFromJson($json)
{
    $jsonDecoded = json_decode($json);
    $string = '';
    foreach ($jsonDecoded as $key => $data) {
        $string .= $key . ":" . $data . ', ';
    }
    return $string;
}

function getListDataFromJson($json, $company_name)
{
    $jsonDecoded = json_decode($json);
    $string = $company_name . '<br/>';
    foreach ($jsonDecoded as $key => $data) {
        $string .= $key . ": " . $data . '<br/>';
    }
    return $string;
}

function getRating($list)
{
    $feedbackR = '';
    for ($i = 1; $i <= 5; $i++) {
        $color = '';
        if ($i == $list->rating) {
            $color = 'color: #DAC16C';
        }
        $title = '';
        switch ($i) {
            case 1:
                $title = 'Very poor';
                $img = '<img src="/assets/images/Feedbackimage/horrible.png" style="width: 100%" id="image_1">';
                if ($list->rating == 1) {
                    $img = '<img src="/assets/images/Feedbackimage/_horrible.png" style="width: 100%" id="image_1">';
                }
                break;
            case 2:
                $title = 'Poor';
                $img = '<img src="/assets/images/Feedbackimage/poor.png" style="width: 100%" id="image_1">';
                if ($list->rating == 2) {
                    $img = '<img src="/assets/images/Feedbackimage/_poor.png" style="width: 100%" id="image_1">';
                }
                break;
            case 3:
                $title = 'Average';
                $img = '<img src="/assets/images/Feedbackimage/averge.png" style="width: 100%" id="image_1">';
                if ($list->rating == 3) {
                    $img = '<img src="/assets/images/Feedbackimage/_averge.png" style="width: 100%" id="image_1">';
                }

                break;
            case 4:
                $title = 'Satisfied';
                $img = '<img src="/assets/images/Feedbackimage/good.png" style="width: 100%" id="image_1">';
                if ($list->rating == 4) {
                    $img = '<img src="/assets/images/Feedbackimage/_good.png" style="width: 100%" id="image_1">';
                }
                break;
            case 5:
                $title = 'Strongly Satisfied';
                $img = '<img src="/assets/images/Feedbackimage/excellent.png" style="width: 100%" id="image_1">';
                if ($list->rating == 5) {
                    $img = '<img src="/assets/images/Feedbackimage/_excellent.png" style="width: 100%" id="image_1">';
                }
                break;

            default:
                $title = 'something was wrong! ';
        }

        $feedbackR .= '<label   title="' . $title . '" class="pointer" style="cursor:pointer;font-size: 15px;width:40px; ' . $color . ' " >
        ' . $img . '
</label>';
    }
    $feedbackR .= '<br>' . $list->comment;
    return $feedbackR;
}

function storeSignatureQRCode($process_type_id, $app_id, $user_id = 0, $approver_desk_id, $signature_type = 'final')
{
    $pdf_sign = pdfSignatureQrcode::firstOrNew([
        "signature_type" => $signature_type,
        "app_id" => $app_id,
        "process_type_id" => $process_type_id,
    ]);

    if ($user_id == 0) {
        $pdf_sign->signer_user_id = Auth::user()->id;
        $pdf_sign->signer_desk = CommonFunction::getDeskName($approver_desk_id);
        $pdf_sign->signer_name = CommonFunction::getUserFullName();
        $pdf_sign->signer_designation = Auth::user()->designation;
        $pdf_sign->signer_phone = Auth::user()->user_phone;
        $pdf_sign->signer_mobile = Auth::user()->user_number;
        $pdf_sign->signer_email = Auth::user()->user_email;
        $pdf_sign->signature_encode = Auth::user()->signature_encode;
    } else {
        $user_info = Users::where('id', $user_id)->first([
            DB::raw("CONCAT(user_first_name,' ',user_middle_name, ' ',user_last_name) as user_full_name"),
            'designation',
            'user_phone',
            'user_number',
            'user_email',
            'signature_encode',
        ]);
        $pdf_sign->signer_user_id = $user_id;
        $pdf_sign->signer_desk = CommonFunction::getDeskName($approver_desk_id);
        $pdf_sign->signer_name = $user_info->user_full_name;
        $pdf_sign->signer_designation = $user_info->designation;
        $pdf_sign->signer_phone = $user_info->user_phone;
        $pdf_sign->signer_mobile = $user_info->user_number;
        $pdf_sign->signer_email = $user_info->user_email;
        $pdf_sign->signature_encode = $user_info->signature_encode;
    }
    $pdf_sign->save();
}

function inspectionStore($request)
{
    $app_id = (!empty($request['app_id']) ? Encryption::decodeId($request['app_id']) : '');
    $process_list_id = (!empty($request['process_list_id']) ? Encryption::decodeId($request['process_list_id']) : '');
    $process_type_id = ProcessList::where('id', $process_list_id)->pluck('process_type_id');

    if ($process_type_id == 13) {
        $appData = new IrcInspection();
    } elseif ($process_type_id == 14) {
        $appData = new SecondIrcInspection();
    } elseif ($process_type_id == 15) {
        $appData = new ThirdIrcInspection();
    } elseif ($process_type_id == 16) {
        $appData = new RegularIrcInspection();
    }

    $appData->app_id = $app_id;
    $appData->irc_purpose_id = $request['irc_purpose_id'];
    $appData->inspection_report_date = (empty($request['inspection_report_date']) ? '' : date('Y-m-d h:i', strtotime($request['inspection_report_date'])));
    $appData->company_name = $request['company_name'];
    $appData->office_address = $request['office_address'];
    $appData->factory_address = $request['factory_address'];
    $appData->industrial_sector = $request['industrial_sector'];
    $appData->organization_status_id = $request['organization_status_id'];
    $appData->entrepreneur_name = $request['entrepreneur_name'];
    $appData->entrepreneur_address = $request['entrepreneur_address'];
    $appData->registering_authority_name = $request['registering_authority_name'];
    $appData->registering_authority_memo_no = $request['registering_authority_memo_no'];
    $appData->reg_no = $request['reg_no'];
    $appData->date_of_registration = (empty($request['date_of_registration']) ? null : date('Y-m-d', strtotime($request['date_of_registration'])));

    $appData->local_male = $request['local_male'];
    $appData->local_female = $request['local_female'];
    $appData->local_total = $request['local_total'];
    $appData->foreign_male = $request['foreign_male'];
    $appData->foreign_female = $request['foreign_female'];
    $appData->foreign_total = $request['foreign_total'];
    $appData->manpower_total = $request['manpower_total'];
    $appData->manpower_local_ratio = $request['manpower_local_ratio'];
    $appData->manpower_foreign_ratio = $request['manpower_foreign_ratio'];

    $appData->local_land_ivst = (float)$request['local_land_ivst'];
    $appData->local_land_ivst_ccy = $request['local_land_ivst_ccy'];
    $appData->local_machinery_ivst = (float)$request['local_machinery_ivst'];
    $appData->local_machinery_ivst_ccy = $request['local_machinery_ivst_ccy'];
    $appData->local_building_ivst = (float)$request['local_building_ivst'];
    $appData->local_building_ivst_ccy = $request['local_building_ivst_ccy'];
    $appData->local_others_ivst = (float)$request['local_others_ivst'];
    $appData->local_others_ivst_ccy = $request['local_others_ivst_ccy'];
    $appData->local_wc_ivst = (float)$request['local_wc_ivst'];
    $appData->local_wc_ivst_ccy = $request['local_wc_ivst_ccy'];
    $appData->total_fixed_ivst = $request['total_fixed_ivst'];
    $appData->total_fixed_ivst_million = $request['total_fixed_ivst_million'];
    $appData->usd_exchange_rate = $request['usd_exchange_rate'];
    $appData->total_fee = $request['total_fee'];

    $appData->trade_licence_num = !empty($request['trade_licence_num']) ? $request['trade_licence_num'] : '';
    $appData->trade_licence_issue_date = (!empty($request['trade_licence_issue_date']) ? date('Y-m-d',
        strtotime($request['trade_licence_issue_date'])) : null);
    $appData->trade_licence_validity_period = !empty($request['trade_licence_validity_period']) ? $request['trade_licence_validity_period'] : "";
    $appData->trade_licence_issuing_authority = !empty($request['trade_licence_issuing_authority']) ? $request['trade_licence_issuing_authority'] : "";

    $appData->inc_number = !empty($request['inc_number']) ? $request['inc_number'] : "";
    $appData->inc_issuing_authority = !empty($request['inc_issuing_authority']) ? $request['inc_issuing_authority'] : "";

    $appData->tin_number = !empty($request['tin_number']) ? $request['tin_number'] : "";
    $appData->tin_issuing_authority = !empty($request['tin_issuing_authority']) ? $request['tin_issuing_authority'] : "";

    $appData->fl_number = isset($request['fl_number']) ? $request['fl_number'] : '';
    $appData->fl_expire_date = (!empty($request['fl_expire_date']) ? date('Y-m-d', strtotime($request['fl_expire_date'])) : null);

    $appData->fl_application_number = isset($request['fl_application_number']) ? $request['fl_application_number'] : '';

    $appData->fl_apply_date = (!empty($request['fl_apply_date']) ? date('Y-m-d', strtotime($request['fl_apply_date'])) : null);

    $appData->fl_issuing_authority = isset($request['fl_issuing_authority']) ? $request['fl_issuing_authority'] : '';;


    $appData->el_number = isset($request['el_number']) ? $request['el_number'] : '';
    $appData->el_expire_date = (!empty($request['el_expire_date']) ? date('Y-m-d', strtotime($request['el_expire_date'])) : null);

    $appData->el_application_number = isset($request['el_application_number']) ? $request['el_application_number'] : '';
    $appData->el_apply_date = (!empty($request['el_apply_date']) ? date('Y-m-d', strtotime($request['el_apply_date'])) : null);

    $appData->el_issuing_authority = isset($request['el_issuing_authority']) ? $request['el_issuing_authority'] : '';

    $appData->bank_account_number = $request['bank_account_number'];
    $appData->bank_account_title = $request['bank_account_title'];
    $appData->bank_id = $request['bank_id'];
    $appData->branch_id = $request['branch_id'];

    if ($process_type_id == 16) {

        $appData->chnage_bank_info = $request['chnage_bank_info'];
        $appData->n_bank_account_number = $request['n_bank_account_number'];
        $appData->n_bank_account_title = $request['n_bank_account_title'];
        $appData->n_bank_id = $request['n_bank_id'];
        $appData->n_branch_id = $request['n_branch_id'];
    }

    $appData->assoc_membership_number = !empty($request['assoc_membership_number']) ? $request['assoc_membership_number'] : "";
    $appData->assoc_chamber_name = !empty($request['assoc_chamber_name']) ? $request['assoc_chamber_name'] : "";
    $appData->assoc_issuing_date = (!empty($request['assoc_issuing_date']) ? date('Y-m-d',
        strtotime($request['assoc_issuing_date'])) : null);
    $appData->assoc_expire_date = (!empty($request['assoc_expire_date']) ? date('Y-m-d',
        strtotime($request['assoc_expire_date'])) : null);

    $appData->annual_production_start_date = !empty($request['annual_production_start_date']) ? date('Y-m-d', strtotime($request['annual_production_start_date'])) : null;
    $appData->em_lc_total_taka_mil = isset($request['em_lc_total_taka_mil']) ? $request['em_lc_total_taka_mil'] : '';
    $appData->em_local_total_taka_mil = isset($request['em_local_total_taka_mil']) ? $request['em_local_total_taka_mil'] : '';
    $appData->em_lc_total_percent = isset($request['em_lc_total_percent']) ? $request['em_lc_total_percent'] : '';
    $appData->em_lc_total_five_percent = isset($request['em_lc_total_five_percent']) ? $request['em_lc_total_five_percent'] : '';
    $appData->em_lc_total_five_percent_in_word = isset($request['em_lc_total_five_percent_in_word']) ? $request['em_lc_total_five_percent_in_word'] : '';

    $appData->project_status_id = $request['project_status_id'];
    if ($request['project_status_id'] == 4) {
        $appData->other_details = $request['other_details'];
    }

    if ($request['irc_purpose_id'] != 2) {
        $appData->apc_half_yearly_import_total = isset($request['apc_half_yearly_import_total']) ? $request['apc_half_yearly_import_total'] : 0;
        $appData->apc_half_yearly_import_other = isset($request['apc_half_yearly_import_other']) ? $request['apc_half_yearly_import_other'] : 0;
        $appData->apc_half_yearly_import_total_in_word = !empty($request['apc_half_yearly_import_total_in_word']) ? $request['apc_half_yearly_import_total_in_word'] : "";
    }

    $totalFee = 0;

    //only IRC 1st adhoc has gov. fee payment
    if ($process_type_id == 13) {
        $total_import = 0;
        if ($request['irc_purpose_id'] == 1) {
            $total_import = (isset($request['apc_half_yearly_import_total']) ? $request['apc_half_yearly_import_total'] : 0) * 2;
        } elseif ($request['irc_purpose_id'] == 2) {
            $total_import = (isset($request['em_lc_total_five_percent']) ? $request['em_lc_total_five_percent'] : 0) * 2;
        } else {
            $total_import = ((isset($request['apc_half_yearly_import_total']) ? $request['apc_half_yearly_import_total'] : 0) + (isset($request['em_lc_total_five_percent']) ? $request['em_lc_total_five_percent'] : 0)) * 2;
        }

        //Govt. fee calculation on half yearly import
        $total = DB::select("select * from irc_inspection_gov_fee_range where annual_total_import_min <= $total_import and annual_total_import_max >= $total_import and status = 1 and is_archive = 0");


        if ($request['app_type'] == 1 && !empty($total)) {
            $totalFee = $total[0]->primary_reg_fee;
        } else if ($total_import >= '50000001') {
            $totalFee = '60000';
        }
    }

    $appData->inspection_gov_fee = $totalFee;
    $appData->remarks = $request['irc_remarks'];
    $appData->entitlement_remarks = $request['entitlement_remarks'];

    //Inspection officer info
    $appData->io_name = CommonFunction::getUserFullName();
    $appData->io_designation = Auth::user()->designation;
    $appData->io_mobile_no = Auth::user()->user_phone;
    $appData->io_email = Auth::user()->user_email;
    $appData->io_signature = Auth::user()->signature;

    $appData->save();

    if ($process_type_id === 13) {
        IrcRecommendationNew::where('id', $app_id)->update(['inspection_gov_fee' => $totalFee]);
    } elseif ($process_type_id === 14) {
        IrcRecommendationSecondAdhoc::where('id', $app_id)->update(['inspection_gov_fee' => $totalFee]);
    } elseif ($process_type_id === 15) {
        IrcRecommendationThirdAdhoc::where('id', $app_id)->update(['inspection_gov_fee' => $totalFee]);
    } elseif ($process_type_id === 16) {
        IrcRecommendationRegular::where('id', $app_id)->update(['inspection_gov_fee' => $totalFee]);
    }

    // Annual production capacity
    if (!empty($appData->id) && !empty($request['product_name'][0])) {
        if ($process_type_id == 13) {
            InspectionAnnualProduction::where('app_id', $appData->id)->delete();
        } elseif ($process_type_id == 14) {
            SecondInspectionAnnualProduction::where('app_id', $appData->id)->delete();
        } elseif ($process_type_id == 15) {
            ThirdInspectionAnnualProduction::where('app_id', $appData->id)->delete();
        } elseif ($process_type_id == 16) {
            RegularInspectionAnnualProduction::where('app_id', $appData->id)->delete();
        }

        foreach ($request['product_name'] as $proKey => $proData) {
            if ($process_type_id == 13) {
                $annualCap = new InspectionAnnualProduction();
            } elseif ($process_type_id == 14) {
                $annualCap = new SecondInspectionAnnualProduction();
            } elseif ($process_type_id == 15) {
                $annualCap = new ThirdInspectionAnnualProduction();
            } elseif ($process_type_id == 16) {
                $annualCap = new RegularInspectionAnnualProduction();
            }
            $annualCap->app_id = $app_id;
            $annualCap->inspection_id = $appData->id;
            $annualCap->product_name = $request['product_name'][$proKey];
            $annualCap->unit_of_product = isset($request['unit_of_product'][$proKey]) ? $request['unit_of_product'][$proKey] : '';
            $annualCap->quantity_unit = isset($request['quantity_unit'][$proKey]) ? $request['quantity_unit'][$proKey] : '';
            $annualCap->fixed_production = $request['fixed_production'][$proKey];
            $annualCap->half_yearly_production = $request['half_yearly_production'][$proKey];
            $annualCap->half_yearly_import = $request['half_yearly_import'][$proKey];
            $annualCap->raw_material_total_price = isset($request['raw_material_total_price'][$proKey]) ? $request['raw_material_total_price'][$proKey] : '';
            $annualCap->save();
        }
    }
//        Session::flash('success', 'Successfully Application Submitted !');
    return true;
}