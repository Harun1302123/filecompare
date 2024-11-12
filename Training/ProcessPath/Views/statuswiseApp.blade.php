<style>
    .statusBox{
        float: left;
        width: 120px;
        margin: 5px 3px;
        height: 80px;
    }
    .statusBox-inner {
        padding: 3px !important;
        font-weight: bold !important;
        height: 100%;
    }

</style>
<?php
$appsInDesk = CommonFunction::statuswiseAppInDesks($process_type_id); // 2 is the service ID of registration
$user = explode('x', Auth::user()->user_type);
?>

@if($appsInDesk) {{-- Desk Officers --}}
@foreach($appsInDesk as $row)

    <div class="statusBox">
        <div class="panel panel-success statusBox-inner" style="border-color: #347ab6">
            <a href="javascript:void(0)" class="statusWiseList" data-id="{{$row['process_type_id'].','.$row['status_id']}}" style="background: #008000">
            <div class="panel-heading" style="background:{{ $row['color'] }};color: white; padding: 10px 5px !important; alignment-adjust: central;height: 100%"
                 title="{{ $row['status_name']}}">

                <div class="row">
                    <div class="col-xs-12 text-center">
                        <div class="h3" style="margin-top:0;margin-bottom:3px;font-size:20px;" id="{{ $row['status_name']}}">
                            {{ $row['no_of_application'] }}
                        </div>
                    </div>
                </div>

                <div class="row" style=" text-decoration: none !important">
                    <div class="col-xs-12 text-center">
                        <div class="h3" style="margin-top:0;margin-bottom:0;font-size:13px; font-weight: bold">
                            {{ $row['status_name']}}
                        </div>
                    </div>
                </div>
            </div>
            </a>
        </div>
    </div>

@endforeach
@endif {{--checking not empty $appsInDesk --}}

