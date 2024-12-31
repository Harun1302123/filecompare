<html>
<head></head>
<body onload='document.forms[0].submit()'>
<form name='PostForm' method='POST' action='{{ config('payment.spg_settings.spg_web_portal_url') }}'>
<textarea name='datarequest' id='datarequest' rows='15' style='width:100%; display:none;'>
<SpsRequestByeChallan>
<RequestInformation>
 <Authentication>
<ApiAccessUserId>{{ config('payment.spg_settings.spg_user_id') }}</ApiAccessUserId>
<AuthenticationKey>{{$sessionToken}}</AuthenticationKey>
 </Authentication>
 <ReferenceInfo>
<RequestId>{{ $paymentInfo->request_id }}</RequestId>
<RefTranNo>{{ $paymentInfo->ref_tran_no }}</RefTranNo>
<RefTranDateTime>{{ $paymentInfo->ref_tran_date_time }}</RefTranDateTime>
<ReturnUrl>{{ config('payment.spg_settings.return_url_m') }}</ReturnUrl>
<ReturnMethod>POST</ReturnMethod>
<TranAmount>{{ ($paymentInfo->pay_amount+$paymentInfo->vat_on_pay_amount) }}</TranAmount>
<ContactName>{{ $paymentInfo->contact_name }}</ContactName>
<ContactNo>{{ $paymentInfo->contact_no }}</ContactNo>
<PayerId>{{ $paymentInfo->id }}</PayerId>
<Address>{{ $paymentInfo->address }}</Address>
 </ReferenceInfo>

  <CreditInformation>
  <?php $sl = 1; ?>
      @foreach($payment_details as $data)
          <CreditInfo>
<SLNO>{{$sl}}</SLNO>
<CreditAccount>{{ $data->receiver_ac_no }}</CreditAccount>
<CrAmount>{{$data->pay_amount}}</CrAmount>
<Purpose>{{ $data->purpose_sbl }}</Purpose>
<Onbehalf>{{$paymentInfo->contact_name }}</Onbehalf>
</CreditInfo>
          <?php $sl++; ?>
      @endforeach
 </CreditInformation>

</RequestInformation>
</SpsRequestByeChallan>
</textarea></form>
</body>
</html>