@if(count($inspectionInfo) > 1)
    <div id="inspectionApproved">
        <fieldset class="scheduler-border">
            <legend class="scheduler-border">Below the inspection report list, select one for approval:</legend>
            <table class="table table-striped table-bordered table-hover" style="background: #fff;">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Inspection Datetime</th>
                    <th>Report Submit Datetime</th>
                    <th>Inspected By</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody style="color: #333">
                <?php
                $sl = 1;
                ?>
                @foreach($inspectionInfo as $inspection)
                    <tr>
                        <td>{{ $sl++ }}</td>
                        <td>{{ date('d M, Y h:i A', strtotime($inspection->inspection_report_date)) }}</td>
                        <td>{{ date('d M, Y h:i A', strtotime($inspection->created_at)) }}</td>
                        <td>{{ $inspection->io_name }}</td>
                        <td>
                            <label class="radio-inline">
                                <input type="radio" value="{{ Encryption::encodeId($inspection->id) }}" name="ins_approved_id" id="ins_approved_id" required class="required">
                                Approved
                            </label>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </fieldset>
    </div>
@elseif(count($inspectionInfo) == 1)
    <input type="hidden" value="{{ Encryption::encodeId($inspectionInfo[0]->id) }}" name="ins_approved_id" id="ins_approved_id">
@endif