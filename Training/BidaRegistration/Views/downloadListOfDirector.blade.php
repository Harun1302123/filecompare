<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>

<body>
<div class="container">
    <div class="row">
        <table width="100%">
            <tbody>
            <tr>
                <td width="75%" style="padding: 0">
                    <strong>Ref No: </strong> {{ !empty($appInfo->tracking_no) ? $appInfo->tracking_no : '' }}
                </td>
                <td width="25%" style="padding: 0; text-align: right">
                    <strong>Date:</strong> {{ date('F j, Y', strtotime($appInfo->submitted_at)) }}
                </td>
            </tr>
            <tr>
                <td style="padding: 0">
                    <strong>Subject: </strong>Registration of industrial project under the title of
                    <strong>{{ ucwords(CommonFunction::getCompanyNameById($appInfo->company_id)) }}</strong>
                </td>
            </tr>
            </tbody>
        </table>

        @if (count($listOfDirector) > 0)
            <h5 style="text-align:center; margin-top:10px">List of Directors</h5>
            <table class="table table-bordered" width="100%">
                <thead class="text-center">
                <tr>
                    <th width="10%">SL No.</th>
                    <th width="25%">Name</th>
                    <th width="25%">Designation</th>
                    <th width="20%">Nationality</th>
                    <th width="20%">NID/ Passport No.</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 1; ?>
                @foreach($listOfDirector as $list)
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ $list->l_director_name }}</td>
                        <td>{{ $list->l_director_designation }}</td>
                        <td>{{ $nationality[$list->l_director_nationality] }}</td>
                        <td>{{ $list->nid_etin_passport }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif

        @if (count($listOfMechineryImported) > 0)
            <div style="page-break-before:always"></div>
            <h5 style="text-align:center; margin-top:40px">List of Machinery to be imported</h5>
            <table class="table table-bordered" width="100%">
                <thead  class="text-center">
                <tr>
                    <th width="10%">SL No.</th>
                    <th width="45%">Name of Machinery</th>
                    <th width="15%">Quantity</th>
                    <th width="15%">Unit Prices TK</th>
                    <th width="15%">Total Value (Million) TK</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $j = 1;
                ?>
                @foreach($listOfMechineryImported as $mechinery)
                    <tr>
                        <td>{{ $j++ }}</td>
                        <td>{{ $mechinery->l_machinery_imported_name }}</td>
                        <td>{{ $mechinery->l_machinery_imported_qty }}</td>
                        <td>{{ $mechinery->l_machinery_imported_unit_price }}</td>
                        <td>{{ $mechinery->l_machinery_imported_total_value }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tr>
                    <td colspan="4" class="text-center"><strong>Total</strong></td>
                    <td><strong>{{ $machineryImportedTotal }}</strong></td>
                </tr>
            </table>
        @endif

        @if (count($listOfMechineryLocal) > 0)
            <div style="page-break-before:always"></div>
            <h5 style="text-align:center; margin-top:40px">List of Machinery to be Local</h5>
            <table class="table table-bordered" width="100%">
                <thead class="text-center">
                <tr>
                    <th width="10%">SL No.</th>
                    <th width="45%">Name of Machinery</th>
                    <th width="15%">Quantity</th>
                    <th width="15%">Unit Prices TK</th>
                    <th width="15%">Total Value (Million) TK</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $k = 1;
                ?>
                @foreach($listOfMechineryLocal as $mechineryLocal)
                    <tr>
                        <td>{{ $k++ }}</td>
                        <td>{{ $mechineryLocal->l_machinery_local_name }}</td>
                        <td>{{ $mechineryLocal->l_machinery_local_qty }}</td>
                        <td>{{ $mechineryLocal->l_machinery_local_unit_price }}</td>
                        <td>{{ $mechineryLocal->l_machinery_local_total_value }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tr>
                    <td colspan="4" class="text-center"><strong>Total</strong></td>
                    <td><strong>{{ $machineryLocalTotal }}</strong></td>
                </tr>
            </table>
        @endif
    </div>
</div>
</body>

</html>