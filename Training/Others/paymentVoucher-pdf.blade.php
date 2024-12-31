<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset("assets/stylesheets/styles.css") }}" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
</head>
<body>

<?php
//    $totalAmount= $paymentInfo->pay_amount + $paymentInfo->transaction_charge_amount + $paymentInfo->vat_on_transaction_charge;
//$totalAmount= ($paymentInfo->pay_amount + $paymentInfo->transaction_charge_amount + $paymentInfo->vat_on_transaction_charge);

?>

<section class="content" id="applicationForm" style="position: relative">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">

                <!--Applicent Copy-->
                <table class="" cellspacing="0" width="100%">
                    <tr>
                        <td width="30%" style="text-align: left;">
                            <img src="{{ Session::get('logo') }}" alt="BIDA Logo" style="max-width: 150px;">
                        </td>
                        <td width="70%" style="text-align: right;">
                            <h4 style="font-size: 18px; color: #092270;">{{ Session::get('title') }}</h4>
                            <p style="font-size: 12px;">{{ Session::get('manage_by') }}</p>
                        </td>
                    </tr>
                </table>


                <table class="" cellspacing="0" width="100%">
                    <tr>
                        <td style="text-align: center;">
                            <h3 class="text-center">One Stop Service(OSS)</h3>
                        </td>
                    </tr>
                </table>


                <table class="" cellspacing="0" width="100%">
                    <tr>
                        <td width="65%" style="text-align: right;">
                            <img src="{{ $barcode_url }}" width="200px" height="30px"/>
                        </td>
                        <td width="35%" style="text-align: right">
                            <p style="border: 1px solid black;">&nbsp;&nbsp; Applicant Copy &nbsp;&nbsp;</p>
                        </td>
                    </tr>
                </table>

                <table class="" cellspacing="0" width="100%">
                    <tr>
                        <td style="text-align: left;">
                            <b style="padding: 5px;font-size: 18px;">Payment Information:
                            </b>
                        </td>

                        <td style="text-align: right;">
                            <p style="padding: 5px;">Tracking No.: {{ $paymentInfo->app_tracking_no }}</p>
                        </td>
                    </tr>
                </table>

                <table class="" cellspacing="0" width="100%">
                    <tr>
                        <td style="text-align: left;">
                            <p style="padding: 5px;">Payment ID : {{$paymentInfo->request_id}}</p>
                        </td>
                        <td style="text-align: right;">
                            <p style="padding: 5px;">Date: {{date('d-M-Y', strtotime($paymentInfo->payment_date))}}</p>
                        </td>
                    </tr>
                </table>

                <table class="" cellspacing="0" width="100%">
                    <tr>
                        <td style="text-align: left;">
                            <p style="padding: 5px;">Bank Name : Sonali Bank Limited</p>
                        </td>
                        <td style="text-align: right;">
                            <p style="padding: 5px;">Payment Mode: {{ $paymentInfo->pay_mode }}</p>
                        </td>
                    </tr>
                </table>

                <table class="table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th style="padding: 5px;">Payment Summary</th>
                        <th class="text-center" style="padding: 5px;">Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td style="text-align: left;">
                            <span style="padding: 5px">Pay Amount</span>
                        </td>
                        <td style="text-align: center;padding: 5px">
                            <span style="padding: 5px"> {{$paymentInfo->pay_amount}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left;">
                            <span style="padding: 5px">VAT on pay amount</span>
                        </td>
                        <td style="text-align: center;padding: 5px">
                            <span style="padding: 5px"> {{$paymentInfo->vat_on_pay_amount}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left;">
                            <span style="padding: 5px">Transaction charge</span>
                        </td>
                        <td style="text-align: center;padding: 5px">
                            <span style="padding: 5px"> {{$paymentInfo->transaction_charge_amount}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left;">
                            <span style="padding: 5px">VAT on transaction charge</span>
                        </td>
                        <td style="text-align: center;padding: 5px">
                            <span style="padding: 5px"> {{$paymentInfo->vat_on_transaction_charge}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left;">
                            <span style="padding: 5px; font-weight: bold;">Total Fees</span>
                        </td>
                        <td style="text-align: center;padding: 5px">
                            <span style="padding: 5px; font-weight: bold;"> {{$paymentInfo->total_amount}}</span>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <table class="" cellspacing="0" width="100%">
                    <tr>
                        <td style="text-align: left;">
                            <p style="padding: 5px;">Amount in words(Taka)
                                : {{ ucfirst(\App\Libraries\CommonFunction::convert_number_to_words($paymentInfo->total_amount)) }} only
                            </p>
                        </td>
                    </tr>
                </table>

                <table class="" cellspacing="0" width="100%">
                    <tr>
                        <td width="100%" style="text-align: left;">
                            <p style="padding: 5px;">Organization Name
                                : {{ $companyName  }}
                            </p>
                        </td>
                        {{-- <td width="50%" style="text-align: right;">
                            <span style="padding: 5px;">
                                Tracking No.: {{ $paymentInfo->app_tracking_no }}
                            </span>
                        </td> --}}
                    </tr>
                </table>

                <table class="" cellspacing="0" width="100%">
                    <tr>
                        <td width="50%" style="text-align: left">
                            <p style="padding: 5px;">Depositor Name
                                : {{$paymentInfo->contact_name}}
                            </p>
                        </td>
                        <td width="50%" style="text-align: right">
                            <p style="padding: 5px;">Depositor Mobile Number
                                : {{$paymentInfo->contact_no}}
                            </p>
                        </td>
                    </tr>
                </table>

                @if(in_array($paymentInfo->process_type_id, [1,2,3,4,5,10]))
                    <table width="100%">
                        <tr>
                            <td width="100%" style="text-align: left">
                                <p style="padding: 5px;">Name and Passport No. : {{isset($emp_info) && !empty($emp_info->emp_name) ? $emp_info->emp_name.' , ' : ''}} {{ isset($emp_info) && !empty($emp_info->emp_passport_no) ? $emp_info->emp_passport_no : '' }} </p>
                            </td>
                        </tr>
                    </table>
                @endif
                <br/>

                <table class="" cellspacing="0" width="100%">
                    <tr>
                        <td width="45%">
                            <span>_____________________</span><br/>
                            <span>Depositor signature</span>
                        </td>
                        <td width="40%">
                            <span>____________</span><br/>
                            <span>Received By</span>
                        </td>
                        <td width="15%">
                            <span style="text-align: right;">______________</span><br/>
                            <span style="text-align: right;">Approved By</span>
                        </td>
                    </tr>
                </table>

                <table class="" cellspacing="0" width="100%">
                    <tr>
                        <td style="text-align: left;">
                            <b style="padding: 5px;">Note: Payment could only be made through any branches of Sonali Bank Ltd.(Online Branch).</b>
                        </td>
                    </tr>
                </table>

                <table class="" cellspacing="0" width="100%">
                    <tr>
                        <td style="text-align: center;">
                            <small>Voucher generated by BIDA One Stop Service System & Manage by Business Automation Ltd.</small>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</section>


<link rel="stylesheet" href="{{ url('assets/css/jquery.steps.css') }}">
<script src="{{ asset("assets/scripts/jquery.steps.js") }}"></script>

</body>
</html>