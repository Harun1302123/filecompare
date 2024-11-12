<div class="row">
    <div class="col-md-12">
    <div class="col-sm-8">

        @if($session_get == 'batch_update')

            <div class="col-sm-8">
                <i style="color: #a94442">
                    You are processing {{$total_process_app}} of {{$total_selected_app}} application in batch.
                    <br>
                    Tracking no. of next application is.{{$next_app_info}}</i>
            </div>
        @endif
    </div>
    @if($session_get == 'batch_update')

        <div class="col-md-2 pull-right">
            <div class="form-group">
                <a class="btn btn-primary btn-block" @if($total_process_app == $total_selected_app) disabled="" @else  href="/process/batch-process-skip/{{$single_process_id_encrypt}}" @endif > Next <i class="fa fa-angle-double-right"></i></a>
            </div>
        </div>
            <div class="col-md-2 pull-right">
                <div class="form-group">
                    <a class="btn btn-info btn-block"  @if($total_process_app == 1) disabled="" @else href="/process/batch-process-previous/{{$single_process_id_encrypt}}" @endif ><i class="fa fa-angle-double-left"></i> Previous</a>
                </div>
            </div>
    @endif
</div>
</div>