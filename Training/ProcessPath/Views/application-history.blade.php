<div class="panel panel-orange">
    <div class="panel-heading"> Application Process History
    @if(\Illuminate\Support\Facades\Auth::user()->user_type == '1x101')
        <span class="pull-right"><a href="{{url('/process-path/verify_history/'.Encryption::encodeId($appInfo->process_type_id).'/'.Encryption::encodeId($appInfo->process_list_id))}}" class="btn btn-primary btn-xs">Block Chain Verification</a> </span>
    @endif
    </div><!-- /.panel-heading -->
    <div class="panel-body">
        <table id="app_history_table" class="table table-striped table-bordered display" style="width: 100%;">
                <thead>
                <tr>
                    <th width="10%" class="text-center">Current Desk</th>
                    <th width="10%">Last updated by</th>
                    <th width="10%">Status</th>
                    <th width="10%">Process Time</th>
                    <th width="25%">Remarks</th>
                    <th width="3%">Attachment</th>
                </tr>
                </thead>
                <tbody>
                <?php $sl = 0; ?>
                @forelse($process_history as $key=>$history)

                    <?php
                        if(\Illuminate\Support\Facades\Auth::user()->user_type == '5x505'){
                            if(!in_array($history->status_id,[1,2,5,6,25])){
                                continue;
                            }
                        }
                    ?>
                    <?php $sl++; ?>
                    <tr>
                        <td class="text-center"> {{ $history->deskname }}</td>
                        <?php

                        ?>
                        <td>{{$history->user_first_name . ' ' . $history->user_middle_name . ' ' . $history->user_last_name}}
                          @if(isset($history->on_behalf_of_user) && $history->on_behalf_of_user !=0)
                                {{'On behalf of user :'. \App\Libraries\CommonFunction::getUserFullnameById($history->on_behalf_of_user)}}
                            @endif
                            <?php
                            if(isset($process_history[$key+1])){
                                if($process_history[$key+1]->deskname != 'Applicant')
                                echo '[Desk: '.$process_history[$key+1]->deskname.']';
                                else{
                                    echo '['.$process_history[$key+1]->deskname.']';
                                }
                            }else{
                                echo "[Applicant]";
                            }
                            ?>
                            </td>
                        <td>{{$history->status_name}}</td>
                        <td>{{ date('d-m-Y h:i A', strtotime($history->updated_at  ))}}</td>
                        <td>{{$history->process_desc}}</td>
                        <td>
                            @if(@$history->files != '')
                                <?php $historyFile = explode(",", @$history->files); ?>

                                @foreach($historyFile as $value)
                                    <a target="_blank" href="{{ url($value) }}" class="btn btn-primary show-in-view btn-xs  download" data="{{$sl}}">
                                        <i class="fa fa-file-pdf-o"></i> Open File
                                    </a>
                                @endforeach
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center">No result found!</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
    </div><!-- /.panel-body -->
</div>

