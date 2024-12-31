<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
<div class="col-md-12">
    <div class="box">
        <div class="box-body">
            <!--Bank Copy-->
            <table width="100%">
                <tr>
                    <td width="30%" style="text-align: left;">
                        <img src="{{ Session::get('logo') }}" alt="BIDA Logo" style="max-width: 120px;">
                    </td>
                    <td width="70%" style="text-align: right;">
                        <h4 style="font-size: 18px; color: #092270;">{{ Session::get('title') }}</h4>
                        <span style="font-size: 12px;">{{ Session::get('manage_by') }}</span>
                    </td>
                </tr>
            </table>

            <table width="100%" style="margin-top: -15px;">
                <tr>
                    <td style="text-align: center;" colspan="2">
                        <h5 class="text-center">One Stop Service(OSS)</h5>
                    </td>
                </tr>
                <tr>
                    <td width="67%" style="text-align: right;">
                        <img src="{{ $barcode_url }}" width="220px" height="20px"/>
                        {{-- <img src="" width="220px" height="20px"/> --}}
                    </td>
                    <td style="text-align: right">
                        <span style="border: 1px solid black;">&nbsp;&nbsp; Bank Copy &nbsp;&nbsp;</span>
                    </td>
                </tr>
            </table>

            <table width="100%" style="font-size: 13px">
                <tr>
                    <td style="text-align: left;"> <strong style="padding: 5px;">Payment Information:</strong> </td>
                    <td style="text-align: right;">Tracking No.: {{ $paymentInfo->app_tracking_no }}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">Payment ID: {{$paymentInfo->request_id}}</td>
                    <td style="text-align: right;">Date: {{ date('d-M-Y', strtotime($paymentInfo->payment_date)) }}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">Bank Name: Sonali Bank Limited</td>
                    <td style="text-align: right;">Payment Mode: {{ $paymentInfo->pay_mode }}</td>
                </tr>
            </table>

            <table class="table table-bordered" width="100%" style="margin-bottom: 5px; font-size: 13px">
                <tr>
                    <th style="padding: 5px;">Payment Summary</th>
                    <th class="text-center" style="padding: 5px;">Amount</th>
                </tr>
                <tbody>
                <tr>
                    <td>Pay Amount</td>
                    <td style="text-align: center;">{{$paymentInfo->pay_amount}}</td>
                </tr>
                <tr>
                    <td>VAT on pay amount</td>
                    <td style="text-align: center;">{{$paymentInfo->vat_on_pay_amount}}</td>
                </tr>
                <tr>
                    <td>Transaction charge</td>
                    <td style="text-align: center;">{{$paymentInfo->transaction_charge_amount}}</td>
                </tr>
                <tr>
                    <td>VAT on transaction charge</td>
                    <td style="text-align: center;">{{$paymentInfo->vat_on_transaction_charge}}</td>
                </tr>
                <tr>
                    <th>Total Fees</th>
                    <th style="text-align: center;">{{ $paymentInfo->total_amount }}</th>
                </tr>
                </tbody>
            </table>

            <table width="100%" style="font-size: 13px">
                <tr>
                    <td width="100%" style="text-align: left;">Amount in words(Taka): 
                        {{ ucfirst(CommonFunction::convert_number_to_words($paymentInfo->total_amount)) }} only
                    </td>
                </tr>
            </table>
            
            <table width="100%" style="font-size: 13px">
                <tr>
                    <td width="100%" style="text-align: left;">
                        <p style="padding: 5px;">Organization Name : {{ $companyName  }} </p>
                    </td>
                </tr>
            </table>

            <table width="100%" style="font-size: 13px">
                <tr>
                    <td width="50%" style="text-align: left">
                        <p style="padding: 5px;">Depositor Name : {{$paymentInfo->contact_name}} </p>
                    </td>
                    <td width="50%" style="text-align: right">
                        <p style="padding: 5px;">Depositor Mobile Number : {{$paymentInfo->contact_no}} </p>
                    </td>
                </tr>
            </table>
            @if(in_array($paymentInfo->process_type_id, [1,2,3,4,5,10]))
                <table width="100%" style="font-size: 13px">
                    <tr>
                        <td width="100%" style="text-align: left">
                            <p style="padding: 5px;">Name and Passport No. : {{$emp_info->emp_name}}, {{ $emp_info->emp_passport_no }} </p>
                        </td>
                    </tr>
                </table>
            @endif

            {{-- <table width="100%">
                <tr>
                    <td colspan="2">Amount in
                        words(Taka): {{ ucfirst(CommonFunction::convert_number_to_words($paymentInfo->total_amount)) }}
                        only
                    </td>
                </tr>
                <tr width="60%">
                    <td>Organization Name: {{ $companyName }} </td>
                    <td style="text-align: right">Tracking No.: {{ $paymentInfo->app_tracking_no }}</td>
                </tr>
                <tr width="40%">
                    <td>Depositor Name: {{$paymentInfo->contact_name}}</td>
                    <td style="text-align: right">Depositor Mobile No.: {{$paymentInfo->contact_no}}</td>
                </tr>
            </table> --}}
            <br>
            <table width="100%" style="font-size: 13px">
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
            <table width="100%" style="text-align: center; font-size: 11px">
                <tr>
                    <td>
                        <strong>
                            <small>Note: Payment could only be made through any branches of Sonali Bank Ltd.(Online
                                Branch).
                            </small>
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td>
                        <small>Voucher generated by BIDA One Stop Service System & Manage by Business Automation Ltd.
                        </small>
                    </td>
                </tr>
            </table>

            <img src="assets/images/divider.png" style="width: 100%"/>

            <!--Applicent Copy-->
            <table width="100%">
                <tr>
                    <td width="30%" style="text-align: left;">
                        <img src="{{ Session::get('logo') }}" alt="BIDA Logo" style="max-width: 120px;">
                    </td>
                    <td width="70%" style="text-align: right;">
                        <h4 style="font-size: 18px; color: #092270;">{{ Session::get('title') }}</h4>
                        <span style="font-size: 12px;">{{ Session::get('manage_by') }}</span>
                    </td>
                </tr>
            </table>

            <table width="100%" style="margin-top: -15px;">
                <tr>
                    <td style="text-align: center;" colspan="2">
                        <h5 class="text-center">One Stop Service(OSS)</h5>
                    </td>
                </tr>
                <tr>
                    <td width="67%" style="text-align: right;">
                        <img src="{{ $barcode_url }}" width="220px" height="20px"/>
                        {{-- <img src="" width="220px" height="20px"/> --}}
                    </td>
                    <td style="text-align: right">
                        <span style="border: 1px solid black;">&nbsp;&nbsp; Applicant Copy &nbsp;&nbsp;</span>
                    </td>
                </tr>
            </table>

            <table width="100%" style="font-size: 13px">
                {{-- <tr>
                    <td colspan="2">
                        <strong style="padding: 5px;">Payment Information:</strong>
                    </td>
                </tr> --}}

                <tr>
                    <td style="text-align: left;"> <strong style="padding: 5px;">Payment Information:</strong> </td>
                    <td style="text-align: right">Tracking No.: {{ $paymentInfo->app_tracking_no }}</td>
                </tr>

                <tr>
                    <td style="text-align: left;">Payment ID: {{$paymentInfo->request_id}}</td>
                    <td style="text-align: right;">Date: {{date('d-M-Y', strtotime($paymentInfo->payment_date))}}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">Bank Name: Sonali Bank Limited</td>
                    <td style="text-align: right;">Payment Mode: {{ $paymentInfo->pay_mode }}</td>
                </tr>
            </table>

            <table class="table table-bordered" width="100%" style="margin-bottom: 5px; font-size: 13px">
                <tr>
                    <th style="padding: 5px;">Payment Summary</th>
                    <th class="text-center" style="padding: 5px;">Amount</th>
                </tr>
                <tbody>
                <tr>
                    <td>Pay Amount</td>
                    <td style="text-align: center;">{{$paymentInfo->pay_amount}}</td>
                </tr>
                <tr>
                    <td>VAT on pay amount</td>
                    <td style="text-align: center;">{{$paymentInfo->vat_on_pay_amount}}</td>
                </tr>
                <tr>
                    <td>Transaction charge</td>
                    <td style="text-align: center;">{{$paymentInfo->transaction_charge_amount}}</td>
                </tr>
                <tr>
                    <td>VAT on transaction charge</td>
                    <td style="text-align: center;">{{$paymentInfo->vat_on_transaction_charge}}</td>
                </tr>
                <tr>
                    <th>Total Fees</th>
                    <th style="text-align: center;">{{$paymentInfo->total_amount}}</th>
                </tr>
                </tbody>
            </table>

            <table width="100%" style="font-size: 13px">
                <tr>
                    <td width="100%" style="text-align: left;">Amount in words(Taka):
                        {{ ucfirst(CommonFunction::convert_number_to_words($paymentInfo->total_amount)) }} only
                    </td>
                </tr>
            </table>
            
            <table width="100%" style="font-size: 13px">
                <tr>
                    <td width="100%" style="text-align: left;">
                        <p style="padding: 5px;">Organization Name : {{ $companyName }} </p>
                    </td>
                </tr>
            </table>

            <table width="100%" style="font-size: 13px">
                <tr>
                    <td width="50%" style="text-align: left">
                        <p style="padding: 5px;">Depositor Name : {{$paymentInfo->contact_name}} </p>
                    </td>
                    <td width="50%" style="text-align: right">
                        <p style="padding: 5px;">Depositor Mobile Number : {{$paymentInfo->contact_no}} </p>
                    </td>
                </tr>
            </table>
            @if(in_array($paymentInfo->process_type_id, [1,2,3,4,5,10]))
                <table width="100%" style="font-size: 13px">
                    <tr>
                        <td width="100%" style="text-align: left">
                            <p style="padding: 5px;">Name and Passport No. : {{isset($emp_info) && !empty($emp_info->emp_name) ? $emp_info->emp_name.' , ' : ''}} {{ isset($emp_info) && !empty($emp_info->emp_passport_no) ? $emp_info->emp_passport_no : '' }} </p>
                        </td>
                    </tr>
                </table>
            @endif

            {{-- <table width="100%">
                <tr>
                    <td colspan="2">Amount in
                        words(Taka): {{ ucfirst(CommonFunction::convert_number_to_words($paymentInfo->total_amount)) }}
                        only
                    </td>
                </tr>
                <tr width="60%">
                    <td>Organization Name: {{ $companyName }}</td>
                    <td style="text-align: right">Tracking No.: {{ $paymentInfo->app_tracking_no }}</td>
                </tr>
                <tr width="40%">
                    <td>Depositor Name: {{$paymentInfo->contact_name}}</td>
                    <td style="text-align: right">Depositor Mobile No.: {{$paymentInfo->contact_no}}</td>
                </tr>
            </table> --}}
            <br/>
            <table width="100%" style="font-size: 13px">
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

            <table width="100%" style="text-align: center; font-size: 11px">
                <tr>
                    <td>
                        <strong>
                            <small>Note: Payment could only be made through any branches of Sonali Bank Ltd.(Online
                                Branch).
                            </small>
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td>
                        <small>Voucher generated by BIDA One Stop Service System & Manage by Business Automation Ltd.
                        </small>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
</body>
</html>