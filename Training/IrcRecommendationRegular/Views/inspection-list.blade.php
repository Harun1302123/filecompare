@if(count($inspectionInfo) > 0 && ((Auth::user()->user_type == '5x505' && in_array($appInfo->status_id,[16, 25])) || in_array(Auth::user()->user_type, ['1x101','2x202', '4x404'])))
    <div id="inspectionInfo" class="collapse">
        <div class="panel panel-info">
            <div class="panel-heading">
                <strong>Inspection Information</strong>
            </div>
            <div class="panel-body">
                @if(count($inspectionInfo) > 0)
                    <table class="table table-striped table-bordered">
                        <thead class="alert alert-blue">
                        <tr>
                            <td>SN#</td>
                            <td>Inspection Date</td>
                            <td>Report Submit Date</td>
                            <td>Inspected By</td>
                            <td>Status</td>
                            <td>Details</td>
                        </tr>
                        </thead>
                        <tbody style="background: #fff; color: #000">
                        <?php
                        $sl = 1;
                        $userDeskIds = CommonFunction::getUserDeskIds();
                        ?>
                        @foreach($inspectionInfo as $inspectionInfo)
                            {{--System admin, Deputy Director, Director, Managing Director & submited inspection officer are able to see details--}}
                            @if($inspectionInfo->created_by == Auth::user()->id || in_array(Auth::user()->user_type, ['1x101','2x202']) || (Auth::user()->user_type == '4x404' && (in_array(2, $userDeskIds) || in_array(3, $userDeskIds) || in_array(4, $userDeskIds))))
                                <tr>
                                    <td>{{ $sl++ }}</td>
                                    <td>{{ date('d-M-Y h:m A', strtotime($inspectionInfo->inspection_report_date)) }}</td>
                                    <td>{{ date('d-M-Y h:m A', strtotime($inspectionInfo->created_at)) }}</td>
                                    <td>{{ $inspectionInfo->io_name }}</td>
                                    <td>
                                        @if($inspectionInfo->ins_approved_status == 1)
                                            <span class='label label-success'>Approved</span>
                                        @else
                                            <span class='label label-warning'>Not Approved</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-info btn-sm" target="_blank" href="{{ url('/irc-recommendation-regular/inspection-report-view/'.Encryption::encodeId($inspectionInfo->id)) }}">View</a>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                @else
                    No Inspection form submitted yet
                @endif
            </div>
        </div>
    </div>
@endif