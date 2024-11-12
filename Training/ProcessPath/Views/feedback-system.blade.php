@extends('layouts.admin')
@section('content')
    <?php
    $moduleName = Request::segment(1);
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


                            <div class="nav-tabs-custom" style="margin-top: 15px;padding: 0px 5px;">
                                <ul class="nav nav-tabs">


                                    @if($user_type != '1x101' && $user_type != '2x202' && $user_type != '5x505' && $user_type != '14x141')
                                        @if($user_type == "4x404" && Auth::user()->desk_id == 1)
                                        @endif

                                        <li id="tab1" class="active">
                                            <a data-toggle="tab" href="#list_desk" class="mydesk" aria-expanded="true">
                                                <b>My Desk</b>
                                            </a>
                                        </li>
                                    @else
                                        <li id="tab1" class="active">
                                            <a data-toggle="tab" href="#list_desk" class="mydesk" aria-expanded="true">
                                                @if($is_feedback == 'feedback-list')
                                                    <b>Feedback (Pending)</b>
                                                @else
                                                    <b>List</b>
                                                @endif
                                            </a>
                                        </li>
                                    @endif

                                    <li id="tab5" class="">
                                        <a data-toggle="tab" href="#feedbackList" class="feedback_list"
                                           aria-expanded="false">
                                            <b>Feedback (Given)</b>
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
                                                    <th style="width: 20%;">Service</th>
                                                    <th style="width: 20%;">Serving Desk</th>
                                                    <th style="width: 20%;">Last Status</th>

                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div id="list_search" class="tab-pane" style="margin-top: 20px">
                                        @include('ProcessPath::search')
                                    </div>


                                        <div id="feedbackList" class="tab-pane" style="margin-top: 20px">
                                            <div class="table-responsive">
                                                <table id="feedback_list" class="table table-striped"
                                                       style="width: 100%">
                                                    <thead>
                                                    <tr>
                                                        <th>Tracking ID</th>
                                                        <th>Service</th>
                                                        <th>Serving Desk</th>
                                                        <th>Rating & Comment</th>
                                                        <th style="width: 15%;">Last Status</th>
                                                        <th >Action</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        @include('ProcessPath::rating')
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
                    url: '/process/get-feedback-list',
                    method: 'get',
                    data: function (d) {
                        d._token = $('input[name="_token"]').val();
                        d.process_type_id = parseInt(sessionStorage.getItem("process_type_id"));
                        d.is_feedback = "{{$is_feedback}}";
                        d.is_feedback_row = true;
                    }
                },
                columns: [
                    {data: 'tracking_no', name: 'tracking_no'},
                    {data: 'process_name', name: 'process_name', searchable: false},
                    {data: 'desk', name: 'desk_id'},
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
                    url: '/process/get-feedback-list',
                    method: 'get',
                    data: function (d) {
                        d._token = $('input[name="_token"]').val();
                        d.process_type_id = parseInt(sessionStorage.getItem("process_type_id"));
                        d.is_feedback_row = true;
                        d.given_feedback = "given-feedback";
                    }
                },
                columns: [
                    {data: 'tracking_no', name: 'tracking_no'},
                    {data: 'process_name', name: 'process_name', searchable: false},
                    {data: 'desk', name: 'desk_id'},
                    {data: 'rating', name: 'rating'},
                    {data: 'status_name_updated_time', name: 'status_name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                "aaSorting": []
            });
            {{--@endif--}}



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