<div class="collapse" id="viewShadowFileBtn">
    <div class="col-md-12" id="shadowFileHistoryDiv">
        <div class="panel panel-warning" id="shadowFileHistory">
            <div class="panel-heading">
                <div class="pull-left">
                    Shadow File History
                </div>
                <div class="pull-right">
                    <button type="button" class="btn btn-warning" id="request_shadow_file">Request for shadow file</button>
                    {{--<button type="button" class="btn btn-info" id="already_generate_file"><i class="fa fa-arrow-down" aria-hidden="true"></i> Already Generate File</button>--}}
                </div>
                <div class="clearfix"></div>
            </div><!-- /.panel-heading -->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-responsive table-striped table-bordered table-hover ">
                        <thead>
                        <tr>
                            <th width="15%">Updated By</th>
                            <th width="15%">Generate Time</th>
                            <th width="15%">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sl = 0; ?>
                        @forelse($getShadowFile as $shadow)
                            <?php $sl++; ?>
                            <tr>
                                <td>{{Auth::user()->user_full_name}}</td>
                                <td>{{ date('d-m-Y h:i A', strtotime($shadow->updated_at  ))}}</td>

                                <td>
                                    @if(@$shadow->file_path != '')
                                        <a download="" href="{{ url($shadow->file_path) }}" class="btn btn-primary show-in-view btn-xs  download" data="{{$sl}}">
                                            <i class="fa fa-save"></i> Download
                                        </a>
                                    @else
                                        <a  class="btn btn-danger show-in-view btn-xs ">
                                            Requested
                                        </a>
                                        <a  class="btn btn-warning show-in-view btn-xs ">
                                            In-progress
                                        </a>
                                    @endif {{-- history files --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center">No result found!</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->
            </div><!-- /.panel-body -->
        </div>
    </div>
</div>

{{--<script src="{{ asset("assets/plugins/jquery.scrollTo.js") }}" type="text/javascript"></script>--}}
<script>
//    $('#already_generate_file').click(function() {
//        $.scrollTo($('#shadowFileHistory'), 1000);
//    });

//    $(document).ready(function(){
//        $('#viewShadowFileBtn').on('click', function(e) {
//            $('#shadowFileHistoryDiv').toggle('show');
//        });
//        $('#viewShadowFileBtn').click();
//    });

    $("#request_shadow_file").click(function () {
        var module_url = "{{ $process_info->form_url }}";
        var acl_name = "{{ $process_info->acl_name }}";
        btn = $(this);
        btn_content = btn.html();
        // btn.html("Sending...");
        btn.prop('disabled',true);
        btn.html('<i class="fa fa-spinner fa-spin" style="font-size: 20px"></i> &nbsp;' + btn_content);

        $.ajax({
            type: "POST",
//            url: "/"+module_url+"/request-shadow-file",
            url: "/process-path/request-shadow-file",
            data: {
                _token: $('input[name="_token"]').val(),
                // process_id: $('input[name="process_list_id"]').val(),
                module_name: acl_name,
                ref_id: '{{Encryption::encodeId($appInfo->ref_id)}}',
                process_id: '{{Encryption::encodeId($appInfo->process_list_id)}}',
                process_type_id: "{{Encryption::encodeId($appInfo->process_type_id)}}"
            },
            success: function (response) {
                if(response.responseCode==1){
                    btn.prop('disabled',false);
                    document.location.reload()
                }else if(response.responseCode==0){
                    toastr.error("", response.messages,
                        {
                            timeOut: 6000,
                            extendedTimeOut: 1000,
                            positionClass: "toast-bottom-right"
                        });
                    btn.prop('disabled',false);
                }
            }
        });
    });
</script>

