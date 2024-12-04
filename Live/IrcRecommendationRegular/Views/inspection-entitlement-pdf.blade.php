<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
</head>
<body style="font-family: Arial, Helvetica, sans-serif;">

<div class="content">
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="row" style="margin-bottom: 50px;">
                <div class="col-lg-6" style="width: 400px; float: left;">
                    <strong>Tracking No: </strong> {{ !empty($inspectionInfo->tracking_no) ? $inspectionInfo->tracking_no : '' }}
                </div>
                <div class="col-lg-6" style="width: 100px; float: right;">
                    <strong>Date: </strong> {{ !empty($inspectionInfo->approved_date) ? \Carbon\Carbon::parse($inspectionInfo->approved_date)->format('d-M-Y') : '' }}
                </div>
            </div>

            <table border="1" cellspacing="0" cellpadding="0" style="font-size: 11px; line-height: 18px; text-align: center; width: 100%">
                <tr>
                  <td rowspan="2" colspan="2">SL.</td>
                  <td rowspan="2">NAME AND ADDREES OF THE INDUSTRIAL ENTITY</td>
                  <td rowspan="2">NAME OF THE SPONSOR AND INDUSTRIAL SECTOR </td>
                  <td colspan="2">SIX-MONTHLY IMPORT ENTITLEMENT AS PER ORIGINAL ADHOC IRC </td>
                </tr>
                <tr>
                  <td>PRICE OF RAW MATERIAL IN BDT
                  </td>
                  <td>PRICE OF SPARE PARTS IN BDT
                  </td>
                </tr>
                <tr style="font-size: 8px;">
                  <td style="width: 20px;">1</td>
                  <td colspan="2">2</td>
                  <td>3</td>
                  <td>4</td>
                  <td>5</td>
                </tr>
                <tr>
                  <td rowspan="3"></td>
                  <td><b>NAME</b></td>
                  <td>{{ !empty($inspectionInfo->company_name) ? $inspectionInfo->company_name : '' }}</td>
                  <td rowspan="2">NAME OF THE SPONSOR: Bangladesh Investment Development Authority </td>
                  <td rowspan="3">{{ $inspectionInfo->irc_purpose_id != 2 ? $inspectionInfo->ins_apc_half_yearly_import_total:'00' }}</td>
                  <td rowspan="3">{{ $inspectionInfo->irc_purpose_id != 1 ? $inspectionInfo->first_em_lc_total_five_percent:'00' }}</td>
                </tr>
                <tr>
                  <td><b>ADDRESS OF OFFICE</b></td>
                  <td>{{ !empty($inspectionInfo->office_address) ? $inspectionInfo->office_address : '' }}</td>
                </tr>
                <tr>
                  <td><b>ADRESS  OF FACTORY</b></td>
                  <td>{{ !empty($inspectionInfo->factory_address) ? $inspectionInfo->factory_address : '' }}</td>
                  <td>{{ !empty($inspectionInfo->industrial_sector) ? $inspectionInfo->industrial_sector : '' }}</td>
                </tr>
            </table>
            <br>
            <br>
            <table border="1" cellspacing="0" cellpadding="0" style="font-size: 11px; text-align: center; width: 100%">
              <tr>
                @if ($inspectionInfo->irc_regular_purpose_id != 1)
                    <td colspan="2">
                      {{ $inspectionInfo->irc_regular_purpose_id == 2 
                          ? 'INCREASED PRICE OF SIX-MONTHLY IMPORT ENTITLEMENT' 
                          : 'DECREASED PRICE OF SIX-MONTHLY IMPORT ENTITLEMENT' }}
                    </td>
                @endif
                <td colspan="2">RECOMMENDED IMPORT ENTITLEMENT FOR REGULAR  IRC</td>
                <td rowspan="2">NAME AND DESIGNATION OF THE INSPECTION OFFICER DATE OF THE INSPECTION</td>
              </tr>
              <tr>
                @if ($inspectionInfo->irc_regular_purpose_id != 1)
                    <td>PRICE OF RAW MATERIAL IN BDT</td>
                    <td>PRICE OF SPARE PARTS IN BDT</td>
                @endif
                <td>PRICE OF RAW MATERIAL IN BDT</td>
                <td>PRICE OF SPARE PARTS IN BDT</td>
              </tr>
              <tr style="font-size: 8px;">
                <td>6</td>
                <td>7</td>
                <td>8</td>
                @if ($inspectionInfo->irc_regular_purpose_id != 1)
                    <td>9</td>
                    <td>10</td>
                @endif
              </tr>
              <tr>
                @if ($inspectionInfo->irc_regular_purpose_id == 2)  
                    <td>
                      {{ $inspectionInfo->irc_purpose_id != 2 ?
                      number_format((($inspectionInfo->apc_half_yearly_import_total + $inspectionInfo->apc_half_yearly_import_other) - $inspectionInfo->ins_apc_half_yearly_import_total), 2, '.', '') : '00' }}
                    </td>
                    <td>
                      {{ $inspectionInfo->irc_purpose_id != 1 ?
                      number_format(($inspectionInfo->em_lc_total_taka_mil - $inspectionInfo->first_em_lc_total_five_percent), 2, '.', '') :'00' }}
                    </td>
                @elseif ($inspectionInfo->irc_regular_purpose_id ==3)
                    <td>
                      {{ $inspectionInfo->irc_purpose_id != 2 ? 
                      number_format(($inspectionInfo->ins_apc_half_yearly_import_total - ($inspectionInfo->apc_half_yearly_import_total+$inspectionInfo->apc_half_yearly_import_other)), 2, '.', '') : '00' }}
                    </td>
                    <td>
                      {{ $inspectionInfo->irc_purpose_id != 1 ? 
                      number_format(($inspectionInfo->first_em_lc_total_five_percent - $inspectionInfo->em_lc_total_taka_mil), 2, '.', '') : '00' }}
                    </td>
                @endif
                <td>
                  {{ $inspectionInfo->irc_purpose_id != 2 ? $inspectionInfo->apc_half_yearly_import_total + $inspectionInfo->apc_half_yearly_import_other:'00' }}
                </td>
                <td>
                  {{ $inspectionInfo->irc_purpose_id != 1 ? $inspectionInfo->em_lc_total_taka_mil:'00' }}
                </td>
                <td>
                  {{ $inspectionInfo->io_name }}<br>
                  {{ !empty($inspectionInfo->updated_at) ? \Carbon\Carbon::parse($inspectionInfo->updated_at)->format('d-M-Y') : ''}}
                </td>
              </tr>
            </table>
        </div>
    </div>
</div>
</body>
</html>
