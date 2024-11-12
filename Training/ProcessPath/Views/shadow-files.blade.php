<div class="table-responsive">
    <table class="table table-responsive table-striped table-bordered table-hover no-margin">
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
                        <a download="" href="{{ url($shadow->file_path) }}"
                           class="btn btn-primary show-in-view btn-xs  download" data="{{$sl}}">
                            <i class="fa fa-save"></i> Download
                        </a>
                    @else
                        <a class="btn btn-danger show-in-view btn-xs ">
                            Requested
                        </a>
                        <a class="btn btn-warning show-in-view btn-xs ">
                            In-progress
                        </a>
                    @endif history files
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