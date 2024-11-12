<div class="modal fade" id="remarksHistoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
                            class="sr-only">Close</span></button>
                <h4 class="modal-title"> <i class="fa fa-th-ask"></i> Last Remarks & Attachments</h4>
            </div>

            <div class="modal-body">

                <div class="list-group">
                    <span class="list-group-item" style="color: rgba(0,0,0,0.8);">
                        <h4 class="list-group-item-heading">Remarks</h4>
                        @if ($appInfo->status_id != 1)
                            <p class="list-group-item-text">{{ $appInfo->process_desc }}</p>
                        @endif
                    </span>

                    <div class="attachmentArea">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                </div>

            </div>

            <div class="modal-footer" style="text-align:left;">
                <button type="button" class="btn btn-danger btn-md pull-right" data-dismiss="modal">Close</button>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>

<script>
    $loadedHistoryData = false;
    $('#remarksHistoryModal').on('shown.bs.modal', function(e) {
        if ($loadedHistoryData == false) {
            $.ajax({
                type: "GET",
                url: "<?php echo url(); ?>/process/get-last-remarks",
                data: {
                    process_type_id: '{{ $appInfo->process_type_id }}',
                    process_list_id: '{{ $appInfo->process_list_id }}',
                    status_id: '{{ $appInfo->status_id }}',
                },
                success: function(response) {
                    if (response.response.status_code == 200) {
                        $('.attachmentArea').html(response.response.data);
                        $loadedHistoryData = true;
                    } else {
                        $html =
                            '<span class="text-danger">Unknown error occured! Please reload this modal again</span>'
                        $('.attachmentArea').html($html);

                        // console.log(response.response.message);
                    }
                }
            });
        }
    })
</script>