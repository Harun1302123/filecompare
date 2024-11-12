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
                    {{-- <strong>Date:</strong> {{ date('F j, Y', strtotime($appInfo->submitted_at)) }} --}}
                    <strong>Date:</strong> {{ !empty($appInfo->approved_date) ? date('F j, Y', strtotime($appInfo->approved_date)) : '' }}
                    
                </td>
            </tr>
            <tr>
                <?php
                $tracking_no = "";
                if ($appInfo->is_approval_online == "yes") {
                    $tracking_no = $appInfo->ref_app_tracking_no;
                }
                if($appInfo->is_approval_online == "no") {
                    $tracking_no = $appInfo->manually_approved_br_no;
                }
                ?>
                <td style="padding: 0"><strong>Subject:</strong> Amendment of the registration ({{ $tracking_no }})</td>
            </tr>
            </tbody>
        </table>

        @if (count($listOfDirector) > 0)
            <h5 style="text-align:center; margin-top:15px">List of Directors</h5>
            <table class="table table-bordered" width="100%">
                <thead class="text-center">
                <tr>
                    <td colspan="4" style="font-weight: bold;text-align: center;">Existing Information </td>
                    <td colspan="5" style="font-weight: bold;text-align: center;">Amendment information</td>
                </tr>
                <tr>
                    <th style="font-weight: bold;text-align: center;">Name</th>
                    <th style="font-weight: bold;text-align: center;">Designation</th>
                    <th style="font-weight: bold;text-align: center;">Nationality</th>
                    <th style="font-weight: bold;text-align: center;">NID/Passport No.</th>

                    <th style="font-weight: bold;text-align: center;">Name</th>
                    <th style="font-weight: bold;text-align: center;">Designation</th>
                    <th style="font-weight: bold;text-align: center;">Nationality</th>
                    <th style="font-weight: bold;text-align: center;">NID/Passport No.</th>

                    {{-- <th style="font-weight: bold;text-align: center;">Action</th> --}}

                </tr>
                </thead>
                <tbody>
                @foreach($listOfDirector as $list)
                    <tr>
                        <td>{{ empty($list->l_director_name) ? "" : $list->l_director_name }}</td>
                        <td>{{ empty($list->l_director_designation) ? "" : $list->l_director_designation }}</td>
                        <td>{{ empty($list->l_director_nationality) ? "" : $nationality[$list->l_director_nationality] }}</td>
                        <td>{{ empty($list->nid_etin_passport) ? "" : $list->nid_etin_passport }}</td>

                        {{-- <td>{{ empty($list->n_l_director_name) ? "" : $list->n_l_director_name }}</td>
                        <td>{{ empty($list->n_l_director_designation) ? "" : $list->n_l_director_designation }}</td>
                        <td>{{ empty($list->n_l_director_nationality) ? "" : $nationality[$list->n_l_director_nationality] }}</td>
                        <td>{{ empty($list->n_nid_etin_passport) ? "" : $list->n_nid_etin_passport }}</td> --}}

                        @if ($list->amendment_type != 'no change')
                            <td>{{ empty($list->n_l_director_name) ? "" : $list->n_l_director_name }}</td>
                            <td>{{ empty($list->n_l_director_designation) ? "" : $list->n_l_director_designation }}</td>
                            <td>{{ empty($list->n_l_director_nationality) ? "" : $nationality[$list->n_l_director_nationality] }}</td>
                            <td>{{ empty($list->n_nid_etin_passport) ? "" : $list->n_nid_etin_passport }}</td>
                        @else
                            <td>{{ empty($list->l_director_name) ? "" : $list->l_director_name }}</td>
                            <td>{{ empty($list->l_director_designation) ? "" : $list->l_director_designation }}</td>
                            <td>{{ empty($list->l_director_nationality) ? "" : $nationality[$list->l_director_nationality] }}</td>
                            <td>{{ empty($list->nid_etin_passport) ? "" : $list->nid_etin_passport }}</td>
                        @endif 


                        {{-- <td>
                            @if($list->amendment_type == 'add')
                                {{ 'Added' }}
                            @elseif($list->amendment_type == 'edit')
                                {{ 'Updated' }}
                            @elseif($list->amendment_type == 'delete')
                                {{ 'Deleted' }}
                            @elseif($list->amendment_type == 'no change')
                                {{ 'No changed' }}
                            @else
                                {{ ucfirst($list->amendment_type) }}
                            @endif
                        </td> --}}
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif

        @if (count($importedMachineryData) > 0)
            <pagebreak></pagebreak>
            <h5 style="text-align:center; margin-top:40px">List of Machinery to be imported</h5>
            <table class="table table-bordered" cellspacing="10" width="100%">
                <thead class="text-center">
                <tr>
                    <td colspan="4" style="font-weight: bold;text-align: center;">Existing Information </td>
                    <td colspan="5" style="font-weight: bold;text-align: center;">Amendment Information</td>
                </tr>
                <tr>
                    <th style="font-weight: bold;text-align: center;">Name of Machineries</th>
                    <th style="font-weight: bold;text-align: center;">Quantity</th>
                    <th style="font-weight: bold;text-align: center;">Unit Prices TK</th>
                    <th style="font-weight: bold;text-align: center;">Total Value (Million) TK</th>

                    <th style="font-weight: bold;text-align: center;">Name of Machineries</th>
                    <th style="font-weight: bold;text-align: center;">Quantity</th>
                    <th style="font-weight: bold;text-align: center;">Unit Prices TK</th>
                    <th style="font-weight: bold;text-align: center;">Total Value (Million) TK</th>

                    {{-- <th style="font-weight: bold;text-align: center;">Action</th> --}}

                </tr>
                </thead>
                <tbody>

                @foreach($importedMachineryData['imported_machinery_data'] as $mechinery)
                    <tr>
                        <td>{{ empty($mechinery->l_machinery_imported_name) ? "" : $mechinery->l_machinery_imported_name }}</td>
                        <td>{{ empty($mechinery->l_machinery_imported_qty) ? "" : $mechinery->l_machinery_imported_qty }}</td>
                        <td>{{ empty($mechinery->l_machinery_imported_unit_price) ? "" : $mechinery->l_machinery_imported_unit_price }}</td>
                        <td>{{ empty($mechinery->l_machinery_imported_total_value) ? "" : $mechinery->l_machinery_imported_total_value }}</td>

                        {{-- <td>{{ empty($mechinery->n_l_machinery_imported_name) ? "" : $mechinery->n_l_machinery_imported_name }}</td>
                        <td>{{ empty($mechinery->n_l_machinery_imported_qty) ? "" : $mechinery->n_l_machinery_imported_qty }}</td>
                        <td>{{ empty($mechinery->n_l_machinery_imported_unit_price) ? "" : $mechinery->n_l_machinery_imported_unit_price }}</td>
                        <td>{{ empty($mechinery->n_l_machinery_imported_total_value) ? "" : $mechinery->n_l_machinery_imported_total_value }}</td> --}}

                        @if ($mechinery->amendment_type != 'no change')
                            <td>{{ empty($mechinery->n_l_machinery_imported_name) ? "" : $mechinery->n_l_machinery_imported_name }}</td>
                            <td>{{ empty($mechinery->n_l_machinery_imported_qty) ? "" : $mechinery->n_l_machinery_imported_qty }}</td>
                            <td>{{ empty($mechinery->n_l_machinery_imported_unit_price) ? "" : $mechinery->n_l_machinery_imported_unit_price }}</td>
                            <td>{{ empty($mechinery->n_l_machinery_imported_total_value) ? "" : $mechinery->n_l_machinery_imported_total_value }}</td>
                        @else
                            <td>{{ empty($mechinery->l_machinery_imported_name) ? "" : $mechinery->l_machinery_imported_name }}</td>
                            <td>{{ empty($mechinery->l_machinery_imported_qty) ? "" : $mechinery->l_machinery_imported_qty }}</td>
                            <td>{{ empty($mechinery->l_machinery_imported_unit_price) ? "" : $mechinery->l_machinery_imported_unit_price }}</td>
                            <td>{{ empty($mechinery->l_machinery_imported_total_value) ? "" : $mechinery->l_machinery_imported_total_value }}</td>
                        @endif 

                        {{-- <td>
                            @if($mechinery->amendment_type == 'add')
                                {{ 'Added' }}
                            @elseif($mechinery->amendment_type == 'edit')
                                {{ 'Updated' }}
                            @elseif($mechinery->amendment_type == 'delete')
                                {{ 'Deleted' }}
                            @elseif($mechinery->amendment_type == 'no change')
                                {{ 'No changed' }}
                            @else
                                {{ ucfirst($mechinery->amendment_type) }}
                            @endif
                        </td> --}}
                    </tr>
                @endforeach
                <tr>
                    <td colspan="7" class="text-right">
                        <strong>Total</strong>
                    </td>
                    <td>
                        <strong>{{ $importedMachineryData['imported_grand_total'] }}</strong>
                    </td>
                </tr>
                </tbody>
            </table>
        @endif

        @if (count($localMachineryData) > 0)
            <pagebreak></pagebreak>
            <h5 style="text-align:center; margin-top:40px">List of Machinery to be Local</h5>
            <table class="table table-bordered" width="100%">
                <thead class="text-center">
                <tr>
                    <td colspan="4" style="font-weight: bold;text-align: center;">Existing Information </td>
                    <td colspan="5" style="font-weight: bold;text-align: center;">Amendment Information</td>
                </tr>
                <tr>
                    <th style="font-weight: bold;text-align: center;">Name of Machineries</th>
                    <th style="font-weight: bold;text-align: center;">Quantity</th>
                    <th style="font-weight: bold;text-align: center;">Unit Prices TK</th>
                    <th style="font-weight: bold;text-align: center;">Total Value (Million) TK</th>

                    <th style="font-weight: bold;text-align: center;">Name of Machineries</th>
                    <th style="font-weight: bold;text-align: center;">Quantity</th>
                    <th style="font-weight: bold;text-align: center;">Unit Prices TK</th>
                    <th style="font-weight: bold;text-align: center;">Total Value (Million) TK</th>

                    {{-- <th style="font-weight: bold;text-align: center;">Action</th> --}}

                </tr>
                </thead>
                <tbody>

                @foreach($localMachineryData['local_machinery_data'] as $mechineryLocal)
                    <tr>
                        <td>{{ empty($mechineryLocal->l_machinery_local_name) ? "" : $mechineryLocal->l_machinery_local_name }}</td>
                        <td>{{ empty($mechineryLocal->l_machinery_local_qty) ? "" : $mechineryLocal->l_machinery_local_qty }}</td>
                        <td>{{ empty($mechineryLocal->l_machinery_local_unit_price) ? "" : $mechineryLocal->l_machinery_local_unit_price }}</td>
                        <td>{{ empty($mechineryLocal->l_machinery_local_total_value) ? "" : $mechineryLocal->l_machinery_local_total_value }}</td>

                        @if($mechineryLocal->amendment_type != 'no change')
                            <td>{{ empty($mechineryLocal->n_l_machinery_local_name) ? "" : $mechineryLocal->n_l_machinery_local_name }}</td>
                            <td>{{ empty($mechineryLocal->n_l_machinery_local_qty) ? "" : $mechineryLocal->n_l_machinery_local_qty }}</td>
                            <td>{{ empty($mechineryLocal->n_l_machinery_local_unit_price) ? "" : $mechineryLocal->n_l_machinery_local_unit_price }}</td>
                            <td>{{ empty($mechineryLocal->n_l_machinery_local_total_value) ? "" : $mechineryLocal->n_l_machinery_local_total_value }}</td>
                        @else
                            <td>{{ empty($mechineryLocal->l_machinery_local_name) ? "" : $mechineryLocal->l_machinery_local_name }}</td>
                            <td>{{ empty($mechineryLocal->l_machinery_local_qty) ? "" : $mechineryLocal->l_machinery_local_qty }}</td>
                            <td>{{ empty($mechineryLocal->l_machinery_local_unit_price) ? "" : $mechineryLocal->l_machinery_local_unit_price }}</td>
                            <td>{{ empty($mechineryLocal->l_machinery_local_total_value) ? "" : $mechineryLocal->l_machinery_local_total_value }}</td>
                        @endif
                        {{-- <td>
                            @if($mechineryLocal->amendment_type == 'add')
                                {{ 'Added' }}
                            @elseif($mechineryLocal->amendment_type == 'edit')
                                {{ 'Updated' }}
                            @elseif($mechineryLocal->amendment_type == 'delete')
                                {{ 'Deleted' }}
                            @elseif($mechineryLocal->amendment_type == 'no change')
                                {{ 'No changed' }}
                            @else
                                {{ ucfirst($mechineryLocal->amendment_type) }}
                            @endif
                        </td> --}}
                    </tr>
                @endforeach
                <tr>
                    <td colspan="7" class="text-right"><strong>Total</strong></td>
                    <td><strong>{{ $localMachineryData['local_grand_total'] }}</strong></td>
                </tr>
                </tbody>
            </table>
        @endif
    </div>
</div>
</body>
</html>