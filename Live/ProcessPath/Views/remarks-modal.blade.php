<?php
//Process remarks attachment
$remarks_attachment = DB::select(DB::raw("select * from
                            `process_documents`
                            where `process_type_id` = $appInfo->process_type_id and `ref_id` = $appInfo->process_list_id and `status_id` = $appInfo->status_id
                            and `process_hist_id` = (SELECT MAX(process_hist_id) FROM process_documents WHERE ref_id=$appInfo->process_list_id AND process_type_id=$appInfo->process_type_id AND status_id=$appInfo->status_id)
                            ORDER BY id ASC"
));
?>

<div class="modal fade" id="remarksModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title"> <i class="fa fa-th-ask"></i> Reason of {{ $appInfo->status_name }}</h4>
            </div>

            <div class="modal-body">
                <div class="list-group">
                    <span class="list-group-item" style="color: rgba(0,0,0,0.8);">
                        <h4 class="list-group-item-heading">Remarks</h4>
                        <p class="list-group-item-text">{{ $appInfo->process_desc }}</p>
                    </span>

                    @if(!empty($remarks_attachment))
                        @foreach($remarks_attachment as $remarks_attachment)
                            <a target="_blank" href="{{ url($remarks_attachment->file) }}" style="margin-top: 10px;" class="btn btn-primary btn-xs">
                                <i class="fa fa-save"></i> Download Attachment
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="modal-footer" style="text-align:left;">
                <button type="button" class="btn btn-danger btn-md pull-right" data-dismiss="modal">Close</button>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>