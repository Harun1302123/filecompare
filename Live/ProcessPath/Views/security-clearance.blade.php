@extends('layouts.admin')
@section('content')
    <?php
    $moduleName = Request::segment(1);
    $process_type_id = 2;
    $user_type = CommonFunction::getUserType();
    $desk_id_array = explode(',', \Session::get('user_desk_ids'));
    $accessMode = "V";
    if (!ACL::isAllowed($accessMode, 'V'))
        die('no access right!');
    $url = $_SERVER['REQUEST_URI'];
    $exp = explode("/", $url);
    $is_feedback = '';
    if (isset($exp[3]) && $exp[3] == 'feedback-list') {
        $is_feedback = $exp[3];
    }
    //    dd($exp[3]);

    ?>
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
    <section class="content">
        <div class="box">
            <div class="box-body">
                <div class="col-lg-12">
                    {!! Session::has('success') ? '<div class="alert alert-success alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("success") .'</div>' : '' !!}
                    {!! Session::has('error') ? '<div class="alert alert-danger alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("error") .'</div>' : '' !!}

                </div>

                @if(empty($delegated_desk))
                    <div class="modal fade" id="ProjectModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                         aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content" id="frmAddProject"></div>
                        </div>
                    </div>
                @endif
                <div class="col-lg-12">
                    <div class="panel panel-info" style="">
                        <div class="panel-heading">
                            <div class="pull-left">
                                <h5><i class="fa fa-list"></i> <b>Application list </b></h5>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                        <div class="panel-body">
                            <?php
                            $appsInDesk = CommonFunction::statuswiseSecurityClearanceApp($process_type_id); // 2 is the service ID of registration
                            $user = explode('x', Auth::user()->user_type);
                            ?>
                            <div class="clearfix">
                            @if($appsInDesk) {{-- Desk Officers --}}
                                <div class="" id="statuswiseAppsDiv">
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
                                </div>
                            </div>
                            @endif {{--checking not empty $appsInDesk --}}


                            <div class="nav-tabs-custom" style="margin-top: 15px;padding: 0px 5px;">
                                <ul class="nav nav-tabs">


                                    <li id="tab1" class="active">
                                        <a data-toggle="tab" href="#list_desk" class="mydesk" aria-expanded="true">
                                            <b>My Desk</b>
                                        </a>
                                    </li>

                                    <li id="tab2" class="">
                                        <a data-toggle="tab" href="#feedbackList" class="feedback_list"
                                           aria-expanded="false">
                                            <b>Search</b>
                                        </a>
                                    </li>

                                    <li class="pull-right process_type_tab">
                                        <div class="form-inline">
                                            <label class="control-label text-primary">Switch to another module's
                                                applications:</label>
                                            {!! Form::select('ProcessType', ['0' => 'All'] + $ProcessType, $process_type_id, ['class' => 'form-control ProcessType', 'style'=>'max-width:270px']) !!}
                                        </div>
                                    </li>
                                </ul>


                                <div class="tab-content">

                                    <div id="list_desk" class="tab-pane active" style="margin-top: 20px">
                                        <div class="table-responsive">

                                            <table id="table_desk" class="table table-striped display"
                                                   style="width: 100%">
                                                <thead>
                                                <tr>
                                                    <th style="width: 20%;">Tracking ID</th>
                                                    <th style="width: 25%;">Service</th>
                                                    <th style="width: 20%;">Serving Desk</th>
                                                    <th style="width: 25%;">Last Status</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                        <div id="list_search" class="tab-pane" style="margin-top: 20px">
                                            <div class="table-responsive">
                                                <table id="table_search" class="table table-striped"
                                                       style="width: 100%">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 20%;">Tracking ID</th>
                                                        <th style="width: 25%;">Service</th>
                                                        <th style="width: 20%;">Serving Desk</th>
                                                        <th style="width: 25%;">Last Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection
@section('footer-script')
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>

    @include('partials.datatable-scripts')
    <script language="javascript">
        $(function () {


            $('.ProcessType').change(function () {
                var process_type_id = $(this).val();
                sessionStorage.setItem("process_type_id", process_type_id);
                if(feedbacklist == true){
                    feedback_list.ajax.reload();
                }
            });
            $('.ProcessType').trigger('change');

            // table_desk.ajax.reload();
            // feedback_list.ajax.reload();

            // if($("#tab5").attr("class").toString() == 'active'){
            //     $("#tab5").click();
            // }
            /**
             * on click My Desk tab reload table with application list of current desk / Serving Desk
             * @type {jQuery}
             */
            var feedbacklist =  false;
            $('.mydesk').click(function () {
                feedbacklist =false;
                table_desk.ajax.reload();
            });
            $('.feedback_list').click(function () {
                feedbacklist =true;
                feedback_list.ajax.reload();
            });

            /**
             * table desk script
             * @type {jQuery}
             */
            table_desk = $('#table_desk').DataTable({
                iDisplayLength: 50,
                processing: true,
                serverSide: true,
                searching: true,
                ajax: {
                    url: '{{route("process.getSecurityclearancelist",['-1000', 'my-desk'])}}',
                    method: 'get',
                    data: function (d) {
                        d._token = $('input[name="_token"]').val();
                        d.process_type_id = parseInt(sessionStorage.getItem("process_type_id"));
                        d.is_feedback = "{{$is_feedback}}";
                        d.is_feedback_row = true;
                        d.is_feedback_row = true;
                    }
                },
                columns: [
                    {data: 'tracking_no', name: 'tracking_no'},
                    {data: 'process_name', name: 'process_name', searchable: false},
                    {data: 'desk', name: 'desk'},
                    {data: 'status_name_updated_time', name: 'status_name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                "aaSorting": []
            });


            feedback_list = $('#feedback_list').DataTable({
                iDisplayLength: 50,
                processing: true,
                serverSide: true,
                searching: true,
                ajax: {
                    url: '{{route("process.getList",['-1000','feedback_list'])}}',
                    method: 'get',
                    data: function (d) {
                        d._token = $('input[name="_token"]').val();
                        d.process_type_id = parseInt(sessionStorage.getItem("process_type_id"));
                        d.is_feedback_row = true;
                    }
                },
                columns: [
                    // {data: 'tracking_no', name: 'tracking_no'},
                    {data: 'desk', name: 'desk'},
                    {data: 'process_name', name: 'process_name', searchable: false},
                    {data: 'rating', name: 'rating'},
                    {data: 'status_name_updated_time', name: 'status_name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                "aaSorting": []
            });
            {{--@endif--}}



        });

        $('.statusWiseList').click(function () {
            $('#list_desk').removeClass('active');
            $('#tab1').removeClass('active');
            $('#tab2').addClass('active');
            $('#list_search').addClass('active');
            var data = $(this).attr("data-id");
            var typeAndStatus = data.split(",");
            var process_type_id = typeAndStatus[0];
            var statusId = typeAndStatus[1];


            //$("#search_process").trigger('click',[process_type_id,statusId]);

            $('#table_search').DataTable({
                destroy: true,
                iDisplayLength: 50,
                processing: true,
                serverSide: true,
                searching: false,
                //responsive: true,
                ajax: {
                    url: '{{route("process.getSecurityclearancelist")}}',
                    method: 'get',
                    data: function (d) {
                        d.process_status = statusId;
                        d.process_type_id = process_type_id;
                        d.status_wise_list = 'status_wise_list';
                    }
                },
                columns: [
                    {data: 'tracking_no', name: 'tracking_no'},
                    {data: 'process_name', name: 'process_name', searchable: false},
                    {data: 'desk', name: 'desk'},
                    {data: 'status_name_updated_time', name: 'status_name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                "aaSorting": []
            });
        });

    </script>
    <style>
        * {
            font-weight: normal;
        }

        /*.unreadMessage td {*/
        /*    font-weight: bold;*/
        /*}*/
    </style>
    @yield('footer-script2')
@endsection