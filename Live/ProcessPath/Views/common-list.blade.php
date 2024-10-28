@extends('layouts.admin')
@section('content')
    <?php
    $moduleName = Request::segment(1);
    $user_type = CommonFunction::getUserType();
    $desk_id_array = explode(',', \Session::get('user_desk_ids'));
    $accessMode = "V";
    if (!ACL::isAllowed($accessMode, 'V'))
        die('no access right!');

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
                                <h5><i class="fa fa-list"></i> <strong>List of Applications</strong></h5>
                            </div>
                            <div class="pull-right">
                                @if(!empty($process_info))
                                    @if(ACL::getAccsessRight($process_info->acl_name,'-A-'))
                                        @if (!$applicationInProcessing)
                                            <a href="{{URL::to('process/'.$process_info->form_url.'/add/'.\App\Libraries\Encryption::encodeId($process_info->id))}}"
                                            class="pull-right">
                                                {!! Form::button('<i class="fa fa-plus"></i> <b>New Application</b>', array('type' => 'button', 'class' => 'btn btn-info')) !!}
                                            </a>
                                        @endif
                                    @endif
                                @endif
                            </div>
                            <div class="clearfix"></div>
                        </div>

                        <div class="panel-body">
                            <div class="clearfix">
                                @if(!empty($desk_id_array[0]) || in_array($user_type, ["1x101","2x202","4x404"]))
                                    @if($user_type != "6x606")
                                        <div id="statuswiseAppsDiv">
                                            @include('ProcessPath::statuswiseApp')
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <div class="nav-tabs-custom" style="margin-top: 15px;padding: 0px 5px;">
                                <ul class="nav nav-tabs">


                                    @if($user_type != '1x101' && $user_type != '2x202' && $user_type != '5x505' && $user_type != '14x141')
                                        {{-- @if($user_type == "4x404" && Auth::user()->desk_id == 1) --}}
                                            {{--<li id="tab4" class="active">--}}
                                            {{--<a data-toggle="tab" href="#desk_user_application" class="deskUserApplication" aria-expanded="true">--}}
                                            {{--<b>My application</b>--}}
                                            {{--</a>--}}
                                            {{--</li>--}}
                                        {{-- @endif --}}

                                        <li id="tab1" class="active">
                                            <a data-toggle="tab" href="#list_desk" class="mydesk"
                                               aria-expanded="true">
                                                <b>My Desk</b>
                                            </a>
                                        </li>

                                        <li id="tab2" class="">
                                            <a data-toggle="tab" href="#list_delg_desk" aria-expanded="false"
                                               class="delgDesk">
                                                <b>Delegation Desk</b>
                                            </a>
                                        </li>

                                    @else
                                        <li id="tab1" class="active">
                                            <a data-toggle="tab" href="#list_desk" class="mydesk" aria-expanded="true">
                                                <b>List</b>
                                            </a>
                                        </li>
                                    @endif
                                    <li id="tab4" class="">
                                        <a data-toggle="tab" href="#favoriteList" class="favorite_list"
                                           aria-expanded="true">
                                            <b>Favorite</b>
                                        </a>
                                    </li>

                                    <li id="tab3" class="">
                                        <a data-toggle="tab" href="#list_search" aria-expanded="false"
                                           id="search_by_keyword" class="list_search">
                                            <b>Search</b>
                                        </a>
                                    </li>


                                    <li class="pull-right process_type_tab" id="processTypeDropdown">
                                        <div class="form-inline">
                                            <label class="control-label text-primary">Service Type :</label>
                                            {!! Form::select('ProcessType', ['0' => 'All'] + $ProcessType, $process_type_id, ['class' => 'form-control ProcessType', 'style'=>'max-width:270px']) !!}
                                        </div>
                                    </li>
                                </ul>


                                <div class="tab-content">

                                    <div id="list_desk" class="tab-pane active" style="margin-top: 20px">
                                        {{--@if(\App\Libraries\CommonFunction::getUserType() == '4x404')--}}
                                        {{--<div class="row">--}}
                                        {{--<div class="col-xs-12">--}}
                                        {{--<div class="text-right" style="margin: 0px 0px 10px">--}}
                                        {{--<button type="button" id="batch_update" class="btn btn-info batch_update" style="background: #5cb85c"><i class="fa fa-recycle"></i> Batch Processing</button>--}}
                                        {{--</div>--}}
                                        {{--</div>--}}
                                        {{--<div><br></div>--}}
                                        {{--</div>--}}
                                        {{--@endif--}}
                                        <table id="table_desk" class=" table table-striped table-bordered display" style="width: 100%">
                                            <thead>
                                            <tr>
                                                <th style="width: 15%;" >Tracking ID</th>
                                                <th style="width: 15%;">Service</th>
                                                <th style="width: 15%;">Serving Desk</th>
                                                <th style="width: 15%;">Last Status</th>
                                                <th style="width: 35%">Applicant Info</th>
                                                {{--<th>Status</th>--}}
                                                {{--<th>Modified</th>--}}
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="list_search" class="tab-pane" style="margin-top: 20px">
                                        @include('ProcessPath::search')
                                    </div>

                                    <div id="list_delg_desk" class="tab-pane" style="margin-top: 20px">
                                        <table id="table_delg_desk"
                                               class="table table-striped table-bordered display"
                                               style="width: 100%">
                                            <thead>
                                            <tr>
                                                <th style="width: 15%;">Tracking ID</th>
                                                <th style="width: 15%;">Service</th>
                                                <th style="width: 15%;">Serving Desk</th>
                                                <th style="width: 15%;">Last Status</th>
                                                <th style="width: 35%">Applicant Info</th>
                                                {{--<th>Status</th>--}}
                                                {{--<th>Modified</th>--}}
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>


                                    <div id="favoriteList" class="tab-pane" style="margin-top: 20px">
                                        <table id="favorite_list"
                                               class="table table-striped table-bordered display"
                                               style="width: 100%">
                                            <thead>
                                            <tr>
                                                <th style="width: 15%;">Tracking ID</th>
                                                <th style="width: 15%;">Service</th>
                                                <th style="width: 15%;">Serving Desk</th>
                                                <th style="width: 15%;">Last Status</th>
                                                <th style="width: 20%;">Applicant Info</th>
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
    </section>

@endsection
@section('footer-script')
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>

    @include('partials.datatable-scripts')
    <script language="javascript">
        $(function () {

            /**
             * Disable Datatable warning thrown
             * this code will disable the warning message of all datatable's from current page.
             * N.B : For debugging purposes, need to do a console log for each data table since warning will disable.
             */
            $.fn.dataTable.ext.errMode = 'none';

            // Global search or dashboard search option
            @if(isset($search_by_keyword) || isset($search_by_status))
            $('#search_by_keyword').trigger('click');
            return false;
            @endif

            $('.ProcessType').change(function () {
                var process_type_id = $(this).val();
                sessionStorage.setItem("process_type_id", process_type_id);
            });
            $('.ProcessType').trigger('change');
            /**
             * on click My Desk tab reload table with application list of current desk / Serving Desk
             * @type {jQuery}
             */
            $('.mydesk').click(function () {
                table_desk.ajax.reload();
                $('#processTypeDropdown').show();
            //    board_meting.ajax.reload();
            });

            $('.deskUserApplication').click(function () {
                table_desk_user_application.ajax.reload();
            });
            /**
             * on click Delegation Desk load table with delegated application list
             * @type {jQuery}
             */
            $('.delgDesk').click(function () {
                table_delg_desk.ajax.reload();
                $('#processTypeDropdown').show();
            });
            $('.favorite_list').click(function () {
                favorite_list.ajax.reload();
                $('#processTypeDropdown').hide();
            });

            $('.list_search').click(function () {
                $('#processTypeDropdown').hide();
            });

            /**
             * table desk script
             * @type {jQuery}
             */
            table_desk = $('#table_desk').DataTable({
                iDisplayLength: 50,
                processing: true,
                serverSide: true,
                // responsive: true,
                ajax: {
                    url: '{{route("process.getList",['-1000', 'my-desk'])}}',
                    method: 'get',
                    data: function (d) {
                        d._token = $('input[name="_token"]').val();
                        d.process_type_id = parseInt(sessionStorage.getItem("process_type_id"));
                        d.is_feedback = "";
                    }
                },
                columns: [

                    {data: 'tracking_no', name: 'tracking_no', searchable: true},
                    {data: 'process_name', name: 'process_name', searchable: false},
                    {data: 'desk_id', name: 'desk_id', searchable: false},
                    {data: 'status_name_updated_time', name: 'status_name', searchable: true},
                    {data: 'json_object', name: 'json_object', searchable: true},

                //    {data: 'status_name', name: 'status_name'},
                //    {data: 'updated_at', name: 'updated_at', searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                "aaSorting": [],
                // "order":[[3, 'desc']]
            }).on('error.dt', function (e, settings, techNote, message) {
                console.log('An error has been reported by DataTables: ', message);
            });

            /**
             * delegated application list table script
             * @type {jQuery}
             */
            favorite_list = $('#favorite_list').DataTable({
                iDisplayLength: 50,
                processing: true,
                serverSide: true,
                searching: true,
                ajax: {
                    url: '{{route("process.getList",['-1000','favorite_list'])}}',
                    method: 'get',
                    data: function (d) {
                        d._token = $('input[name="_token"]').val();
                        d.process_type_id = parseInt(sessionStorage.getItem("process_type_id"));
                        d.is_feedback = "";
                    }
                },
                columns: [
                    {data: 'tracking_no', name: 'tracking_no'},
                    {data: 'process_name', name: 'process_name', searchable: false},
                    {data: 'desk_id', name: 'desk_id', searchable: false},
                    {data: 'status_name_updated_time', name: 'status_name'},
                    {data: 'json_object', name: 'json_object'},
                //    {data: 'status_name', name: 'status_name'},
                //    {data: 'updated_at', name: 'updated_at', searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                "aaSorting": []
            }).on('error.dt', function (e, settings, techNote, message) {
                console.log('An error has been reported by DataTables: ', message);
            });


            table_delg_desk = $('#table_delg_desk').DataTable({
                iDisplayLength: 50,
                processing: true,
                serverSide: true,
                searching: true,
                ajax: {
                    url: '{{route("process.getList",['-1000','my-delg-desk'])}}',
                    method: 'get',
                    data: function (d) {
                        d._token = $('input[name="_token"]').val();
                        d.process_type_id = parseInt(sessionStorage.getItem("process_type_id"));
                        d.is_feedback = "";
                        d.status_wise_list = 'is_delegation';
                    }
                },
                columns: [
                    {data: 'tracking_no', name: 'tracking_no'},
                    {data: 'process_name', name: 'process_name', searchable: false},
                    {data: 'desk_id', name: 'desk_id', searchable: false},
                    {data: 'status_name_updated_time', name: 'status_name'},
                    {data: 'json_object', name: 'json_object'},
                //    {data: 'status_name', name: 'status_name'},
                //    {data: 'updated_at', name: 'updated_at', searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                "aaSorting": []
            }).on('error.dt', function (e, settings, techNote, message) {
                console.log('An error has been reported by DataTables: ', message);
            });
        });

        $('body').on('click', '.favorite_process', function () {

            var process_list_id = $(this).attr('id');
            $(this).css({"color": "#f0ad4e"}).removeClass('far fa-star favorite_process').addClass('fas fa-star remove_favorite_process');
            $(this).attr("title", "Added to your favorite list");
            var _token = $('input[name="_token"]').val();
            $.ajax({
                type: "POST",
                url: "<?php echo url(); ?>/process/favorite-data-store",
                data: {
                    _token: _token,
                    process_list_id: process_list_id
                },
                success: function (response) {
                    if (response.responseCode == 1) {
                    //    toastr.success('Your remarks updated Successfully!!');
                    }
                }
            });
        });

        $('body').on('click', '.remove_favorite_process', function () {

            var process_list_id = $(this).attr('id');
            // alert(process_list_id)
            $(this).css({"color": ""}).removeClass('fas fa-star remove_favorite_process').addClass('far fa-star favorite_process');
            $(this).attr("title", "Add to your favorite list");


            var _token = $('input[name="_token"]').val();
            $.ajax({
                type: "POST",
                url: "<?php echo url(); ?>/process/favorite-data-remove",
                data: {
                    _token: _token,
                    process_list_id: process_list_id
                },
                success: function (response) {
                    btn.html(btn_content);
                    if (response.responseCode == 1) {
                    //    toastr.success('Your remarks updated Successfully!!');
                    }
                }
            });
        });

    </script>

    <script>
        //for old it is not used for now
        /*
        $(".batch_update").click(function () {  //"select all" change
            process_id_array = [];
            var id = $('.batchInput').val();
            $('.batchInput').each(function (i, obj) {
                process_id_array.push(this.value);
            });
            console.log(process_id_array);
            var _token = $('input[name="_token"]').val();
            $.ajax({
                type: "get",
                url: "<?php echo url(); ?>/process/batch-process-set",
                data: {
                    _token: _token,
                    process_id_array: process_id_array
                },
                success: function (response) {
                    if (response.responseType == 'single') {
                        swal({
                            type: 'question',
                            // title: 'Are you sure?',
                            html: '<span style="font-size: 17px; font-weight: bold">Do you want to do batch process of the undermentioned applications?</span>',
                            // text: '<span>Do you want to do batch process of the undermentioned applications?</span>',
                            // showCloseButton: true,
                            showCancelButton: true,
                            focusConfirm: true,
                            cancelButtonText: 'No, cancel!',
                            confirmButtonText: 'Yes &rarr;',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.value) {
                                window.location.href = response.url;
                            }
                        })

                    }
                    if (response.responseType == false) {
                        toastr.error('did not found any data for search list!');
                    }
                }
            });
        });
        */
        @if(\App\Libraries\CommonFunction::getUserType() == '4x404')
        //current used the code for update batch
        $('body').on('click', '.is_delegation', function () {
            var is_blank_page = $(this).attr('target');
            var _token = $('input[name="_token"]').val();
            var current_process_id = $(this).parent().parent().find('.batchInputStatus').val();

            $.ajax({
                type: "get",
                url: "<?php echo url(); ?>/process/batch-process-set",
                async: false,
                data: {
                    _token: _token,
                    is_delegation: true,
                    current_process_id: current_process_id,
                },
                success: function (response) {

                    if (response.responseType == 'single') {
                        // window.location.href = response.url;
                        if (is_blank_page === undefined) {
                            window.location.href = response.url;
                        }
                        else{
                            window.open(response.url, '_blank');
                        }
                    }
                    if (response.responseType == false) {
                        toastr.error('did not found any data for search list!');
                    }
                }

            });
            return false;
        });

        $('body').on('click', '.common_batch_update', function () {
            var current_process_id = $(this).parent().parent().find('.batchInput').val();

            process_id_array = [];
            var id = $('.batchInput').val();
            $('.batchInput').each(function (i, obj) {
                process_id_array.push(this.value);
            });
            // return false;
            var _token = $('input[name="_token"]').val();
            $.ajax({
                type: "get",
                url: "<?php echo url(); ?>/process/batch-process-set",
                data: {
                    _token: _token,
                    process_id_array: process_id_array,
                    current_process_id: current_process_id,
                },
                success: function (response) {
                    if (response.responseType == 'single') {
                        window.location.href = response.url;
                    }
                    if (response.responseType == false) {
                        toastr.error('did not found any data for search list!');
                    }
                }

            });
            return false;
        });

        @endif
    </script>
    <style>
        * {
            font-weight: normal;
        }

        /*.unreadMessage td {*/
        /*    font-weight: bold;*/
        /*}*/

    </style>
    <script>
        function checkPosition() {
            if(window.innerWidth < 768){
                $('.table').addClass("dt-responsive");
            }
        }
        checkPosition();
    </script>
    @yield('footer-script2')
@endsection