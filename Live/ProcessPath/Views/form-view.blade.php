<?php
$accessMode = ACL::getAccsessRight($appInfo->acl_name);
if (!ACL::isAllowed($accessMode, $mode)) {
    die('You have no access right! Please contact with system admin if you have any query.');
}
?>
<?php
$user_type = Auth::user()->user_type;
?>
@extends('layouts.admin', ['viewMode' => $viewMode])

@section('content')
    @include('partials.messages')

    <style>
        .node:active {
            fill: #fffa90;
        }


        /*Style for SVG*/
        svg {
            /*border: 1px solid #ccc;*/
            overflow: hidden;
            cursor: pointer;
            margin: 0 auto;
        }

        .node rect {
            stroke: #333;
            fill: #fff;
        }

        .edgePath path {
            stroke: #333;
            fill: #333;
            stroke-width: 1.5px;
        }

        .content {
            font-size: 14px;
        }

        .label_tracking_no {
            font-size: 100% !important;
        }
    </style>
@if (!in_array($user_type, ['1x101']))
<style>
#loading {
    width: 100vw;
    height: 100vh;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    position: fixed;
    display: block;
    /*opacity: .9;*/
    background-color: #fff;
    z-index: 99;
    text-align: center;
}
#loading-image {
    position: absolute;
    top: 50%;
    width: 220px;
    height: 200px;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 600;
}
.msg {
    position: absolute;
    top: 30%;
    left: 50%;
    transform: translate(-50%,-50%);
    font-size: 20px;
    font-weight: bold;
    width: 25%;
    z-index: 600;
}
.md5{
    margin-bottom: 5px;
}
</style>
@else
<style>
    #loading {
    width: 100%;
    height: 100%;
    top: 0px;
    left: 200px;
    position: fixed;
    display: block;
    background-color: #fff;
    z-index: 99;
    text-align: center;
}
#loading-image {
    position: absolute;
    top: 210px;
    width: 220px;
    height: 200px;
    left: 400px;
    z-index: 600;
}
.msg {
    position: absolute;
    top: 145px;
    left: 340px;
    font-size: 20px;
    font-weight: bold;
    width: 25%;
    z-index: 600;
}

</style>



@endif
    <div id="loading">
        <div class="msg"></div>
        <script>
            var form_load_status;
            var start_time = new Date();
            var interval;

            function checkTimeDif() {
                var now_is_the_time = new Date();
                return Math.floor((now_is_the_time - start_time) / 1000);
            }

            function timeDelay() {
                //console.log(new Date());
                // Difference between Start time and now is the time
                if (checkTimeDif() > 10) {
                    $('.msg').html(
                        '<div class="alert alert-success"><i class="fa fa-spinner fa-spin"></i> Opening form...</div>');
                } else if (checkTimeDif() > 6) {
                    $('.msg').html(
                        '<div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> It is almost done...</div>');
                } else if (checkTimeDif() > 2) {
                    $('.msg').html(
                        '<div class="alert alert-warning"><i class="fa fa-spinner fa-spin"></i> Preparing all data...</div>'
                    );
                } else {
                    $('.msg').html(
                        '<div class="alert alert-danger"><i class="fa fa-spinner fa-spin"></i> Please Wait...</div>');
                }

                // if(form_load_status == 1){
                //     clearTimeout(interval);
                // }

                interval = setTimeout(timeDelay, 1000);
            }
        </script>
        <?php $userPic = URL::to('/assets/images/loading-ttcredesign.gif'); ?>
        <img id="loading-image" src="{{ $userPic }}" alt="Loading..." onload="timeDelay()" />
    </div>
    {{-- Include Batch Process --}}
    <?php
    $session_get = Session::get('is_batch_update');
    $is_delegation = Session::get('is_delegation');
    $single_process_id_encrypt = Session::get('single_process_id_encrypt');
    $next_app_info = Session::get('next_app_info');
    $total_selected_app = Session::get('total_selected_app');
    $total_process_app = Session::get('total_process_app');
    ?>
    @if (ACL::isAllowed($accessMode, '-UP-') && !empty($hasDeskDepartmentWisePermission) or
            $appInfo->user_id == Auth::user()->id)
        <div>
            @include('ProcessPath::batch-process')
        </div>
    @else
        @include('ProcessPath::batch-process-skip')
    @endif

    {{-- Include Application Process Flow --}}
    @if (in_array(Auth::user()->user_type, ['1x101', '3x303', '4x404', '13x303', '2x202']))

        {{-- Process remaining day --}}
        @if (!in_array($appInfo->status_id, [-1, 5, 6]) && Auth::user()->user_type != '2x202')
            <div class="col-sm-12 clearfix">
                <div class="alert alert-success" role="alert" style="margin-bottom: 5px">
                    @if ($appInfo->auto_process == 1)
                        <strong><i class="fa fa-gear"></i> The remaining days of application
                            processing: {{ $remainingDay }}
                            <br />
                            <i class="fa fa-briefcase"></i> Automatic application processing : <i class="fa fa-check"></i>
                        </strong>
                    @else
                        <strong><i class="fa fa-gear"></i> The remaining days of application
                            processing: {{ $remainingDay }}
                            <br />
                            <i class="fa fa-briefcase"></i> Automatic application processing : <i class="fa fa-times"></i>
                        </strong>
                    @endif
                </div>
            </div>
        @endif

        <!-- Bootstrap Collapse menu --->
        <div class="col-sm-12 clearfix" style="margin-bottom: 5px">
            <div class="pull-right">
                @if (in_array(Auth::user()->user_type, ['1x101', '2x202', '4x404']) && $appInfo->final_status == $appInfo->status_id)
                    <a href="{{ url('process/resend-email/' . \App\Libraries\Encryption::encodeId($appInfo->process_type_id) . '/' . \App\Libraries\Encryption::encodeId($appInfo->ref_id) . '/' . \App\Libraries\Encryption::encodeId($appInfo->status_id)) }}"
                       class="btn btn-primary md5">
                        Resend Email
                    </a>

                    <a href="{{ url('process/certificate-regeneration/' . \App\Libraries\Encryption::encodeId($appInfo->ref_id) . '/' . \App\Libraries\Encryption::encodeId($appInfo->process_type_id)) }}"
                       class="btn btn-warning md5">
                        Approval Copy Regenerate
                    </a>
                @endif
                <button class="btn btn-primary md5" data-toggle="collapse" href="#processMap" aria-expanded="false"
                        aria-controls="processMap">View Process Map
                </button>
                @if ($appInfo->process_type_id != 100 && !empty($basicAppID))
                    <a target="_blank" href="{{ url($BiRoute) }}" class="btn btn-success md5">View Corresponding Basic
                        Information</a>
                @endif
                @if (Auth::user()->user_type != '2x202')
                    <button class="btn btn-primary md5" data-toggle="collapse" href="#ShadowFileHistoryDiv"
                            aria-expanded="false" aria-controls="ShadowFileHistoryDiv">View Shadow File History
                    </button>
                @endif
            </div>
        </div>
        <!-- End Bootstrap Collapse men --->


        <!-- Bootstrap Collapse panel --->
        <div class="col-md-12">
            @include('ProcessPath::process-map')
        </div>

        <div class="col-sm-12">
            <div class="collapse" id="ShadowFileHistoryDiv">
                <div class="panel panel-primary" id="shadowFileHistory">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h5><strong>Shadow File History</strong></h5>
                        </div>
                        <div class="pull-right">
                            <button type="button" class="btn btn-warning" id="request_shadow_file">Request for
                                shadow file
                            </button>
                            {{-- <button type="button" class="btn btn-info" id="already_generate_file"><i class="fa fa-arrow-down" aria-hidden="true"></i> Already Generate File</button> --}}
                        </div>
                        <div class="clearfix"></div>
                    </div><!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="text-center" id="shadow-file-loading">
                            <br />
                            <br />
                            <i class="fa fa-spinner fa-pulse fa-4x"></i>
                            <br />
                            <br />
                        </div>
                        <div id="shadow_file_content_area"></div>
                    </div><!-- /.panel-body -->
                </div>
            </div>
        </div>
        <!-- End Bootstrap Collapse panel --->
    @endif


    <div class="load_content" style="clear: both;"></div>

    {{-- Include application history --}}
    @if (in_array(Auth::user()->user_type, [
            '1x101',
            '2x202',
            '3x303',
            // '4x404',
            '5x505',
            '9x901',
            '9x902',
            '9x903',
            '9x904',
        ]) || Auth::user()->user_type == '4x404' && $hasAccessProcessHistory)
        <div class="col-md-12 process-history">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="pull-left">
                        <strong style="line-height: 30px;">Application Process History</strong>
                    </div>
                    <div class="pull-right">
                        <button class="btn btn-warning" data-toggle="collapse" href="#appHistory">
                            Click here to load history
                        </button>
                        @if (\Illuminate\Support\Facades\Auth::user()->user_type == '1x101')
                            <a href="{{ url('/process-path/verify_history/' . Encryption::encodeId($appInfo->process_type_id) . '/' . Encryption::encodeId($appInfo->process_list_id)) }}"
                               class="btn btn-success" target="_blank">Block chain verification</a>
                        @endif
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body">
                    <div id="appHistory" class="collapse">
                        <div class="text-center" id="history-loading">
                            <br />
                            <br />
                            <i class="fa fa-spinner fa-pulse fa-4x"></i>
                            <br />
                            <br />
                        </div>
                        <div id="history_content_area"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <input type="hidden" name="_token" value="{{ csrf_token() }}">
@endsection
@section('footer-script')
    <script src="{{ asset('assets/scripts/datetimepicker_css.js') }}"></script>
    <script>
        // Loaded Application form
        $(document).ready(function() {
            var process_type_id = '{{ $appInfo->process_type_id }}';
            var app_id = '{{ isset($app_id) ? $app_id : '' }}';
            var url = '{{ $url }}' + app_id;
            if (url != '') {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    type: "get",
                    dataType: "json",

                    url: "<?php echo url(); ?>/" + url,
                    data: {
                        process_type_id: process_type_id
                    },
                    success: function(response) {
                        if (response.html == undefined) {
                            console.log(response);
                            $('.load_content').html(
                                "<h3 style='color: red;margin: 250px;text-align: center'>Form not found!!</h3>"
                            );
                            $('.batch-process').hide();
                            $('.process-history').hide();
                        }
                        $('.load_content').html(response.html);

                        var pausecontent = new Array();
                        <?php  if ($errors != null){
                        foreach($errors as $key => $val){ ?>
                        pausecontent.push('<?php echo $val; ?>');
                        <?php }
                        } ?>

                        var html = "";
                        var i;
                        for (i = 0; i < pausecontent.length; i++) {
                            html += '<li class=" alert-danger ">' + pausecontent[i] + '</li>';
                        }
                        $('#validationError').show().html(html);
                        // $('#loading').hide();
                    },
                    error: function(jqXHR, exception) {
                        $('#loading').hide();
                        clearTimeout(interval);

                        var msg =
                            "<h3 style='color: #fcf8e3;margin: 250px;text-align: center'>Somthing was wrong!!!!</h3>";
                        if (jqXHR.status === 0) {
                            msg =
                                "<h3 style='color: #fcf8e3;margin: 250px;text-align: center'>Not connect.\n Verify Network.</h3>";
                        } else if (jqXHR.status == 404) {
                            msg =
                                "<h3 style='color: #fcf8e3;margin: 250px;text-align: center'>Requested page not found. [404]</h3>";
                        } else if (jqXHR.status == 500) {
                            msg =
                                "<h3 style='color: #fcf8e3;margin: 250px;text-align: center'>Something was wrong!!!!</h3>Internal Server Error [500].";
                        } else if (exception === 'parsererror') {
                            msg =
                                "<h3 style='color: #fcf8e3;margin: 250px;text-align: center'>Something was wrong!!!!</h3>Requested JSON parse failed.";
                        } else if (exception === 'timeout') {
                            msg =
                                "<h3 style='color: #fcf8e3;margin: 250px;text-align: center'>Time out error.</h3>";
                        } else if (exception === 'abort') {
                            msg =
                                "<h3 style='color: #fcf8e3;margin: 250px;text-align: center'>Ajax request aborted.</h3>";
                        } else {
                            msg =
                                "<h3 style='color: #fcf8e3;margin: 250px;text-align: center'>Page not loaded successfully, Please reload the page again.\n</h3>" +
                                jqXHR.responseText;
                        }
                        $('.batch-process').hide();
                        $('.process-history').hide();
                        $('.load_content').html(msg);
                    },
                    complete: function(response) {
                        form_load_status = 1;
                        clearTimeout(interval);
                        $('#loading').hide();
                    }
                });
            } else {
                $('.load_content').html(
                    "<h3 style='color: red;margin: 250px;text-align: center'>Undefined URL in Process Type!</h3>"
                );
                $('.batch-process').hide();
                $('.process-history').hide();
                $('#loading').hide();
            }


            /*
            This event fires immediately when the show instance method is called
            for process map loadin
             */
            $('#processMap').on('show.bs.collapse', function() {
                sendRequestAndDraw('{{ $appInfo->process_type_id }}',
                    '{{ isset($appInfo->ref_id) ? \App\Libraries\Encryption::encodeId($appInfo->ref_id) : 0 }}',
                    '{{ isset($cat_id) ? \App\Libraries\Encryption::encodeId($cat_id) : 0 }}');
            });


            /*
            This event fires immediately when the show instance method is called
            for process map loadin
             */
            $('#ShadowFileHistoryDiv').on('show.bs.collapse', function() {
                loadShadowFileHistory();
            });

            /*
            This event fires immediately when the show instance method is called
            for process map loadin
             */
            $('#appHistory').on('show.bs.collapse', function() {
                loadApplicationHistory();
            });


            $("#request_shadow_file").click(function() {
                btn = $(this);
                btn_content = btn.html();
                btn.prop('disabled', true);
                btn.html('<i class="fa fa-spinner fa-spin"></i> &nbsp;' + btn_content);

                $.ajax({
                    type: "GET",
                    url: "/process-path/request-shadow-file",
                    data: {
                        module_name: '{{ $appInfo->acl_name }}',
                        ref_id: '{{ Encryption::encodeId($appInfo->ref_id) }}',
                        process_id: '{{ Encryption::encodeId($appInfo->process_list_id) }}',
                        process_type_id: "{{ Encryption::encodeId($appInfo->process_type_id) }}"
                    },
                    success: function(response) {
                        if (response.responseCode == 1) {
                            btn.prop('disabled', false);
                            document.location.reload()
                        } else if (response.responseCode == 0) {
                            toastr.error("", response.messages, {
                                timeOut: 6000,
                                extendedTimeOut: 1000,
                                positionClass: "toast-bottom-right"
                            });
                            btn.prop('disabled', false);
                        }
                    }
                });
            });
        });


        // Process flow graph load
        // set flag for one time calling
        var is_map_draw = false;

        function sendRequestAndDraw(processId, app_id, cat_id) {
            if (!is_map_draw) {
                $.ajax({
                    url: "<?php echo url('/process/graph/'); ?>/" + processId + "/" + app_id + "/" + cat_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $.when(
                            $.getScript("/assets/plugins/d3/d3.v4.min.js"),
                            $.getScript("/assets/plugins/dagrejs/dagre-d3.min.js"),
                            $.Deferred(function(deferred) {
                                $(deferred.resolve);
                            })
                        ).done(function() {
                            processPath(response);
                            $("#map-loading").hide();
                            is_map_draw = true;
                        }).fail(function() {
                            alert('Unknown error occurred while resource loading. Please try again');
                        });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                    },
                });
            }
        }

        function processPath(response) {

            var svg = d3.select("svg"),
                inner = svg.select("g");
            var zoom = d3.zoom().on("zoom", function() {
                inner.attr("transform", d3.event.transform);
            });

            svg.call(zoom);
            // Create the renderer
            var render = new dagreD3.render();
            var g = new dagreD3.graphlib.Graph().setGraph({});


            var i = 0;
            var finalResult = true;
            var message = '';
            response.desks.forEach(function(desk) {
                // console.log(response.passed_desks_id);
                if (i === 0) {
                    // console.log(response.passed_status_id[i])
                    if (response.passed_status_id[i] == 5) { //5= shortfall
                        finalResult = false;
                    }
                }
                // console.log(desk.desk_id)
                g.setNode(desk.name, {
                    label: desk.label
                });
                if (response.passed_desks_id.indexOf(desk.desk_id) != -1 && finalResult === true) {
                    g.node(desk.name).style = "fill: orange";
                } else {
                    if (desk.desk_id == 0)
                        g.node(desk.name).style = "fill: orange";
                    else
                        g.node(desk.name).style = "fill: #666";
                }
            });

            response.desk_action.forEach(function(action) {
                if (i === 0) {
                    if (response.passed_status_id[i] == 5) { //5= shortfall
                        finalResult = false;
                    }
                }

                g.setNode(action.name, {
                    label: action.label,
                    shape: action.shape
                });
                if (response.passed_status_id.indexOf(action.action_id) != -1 && finalResult === true) {
                    if (action.action_id === 5) {
                        g.node(action.name).style = "fill: #666";
                    } else {
                        g.node(action.name).style = "fill: orange";
                    }
                } else {
                    g.node(action.name).style = "fill: #666";
                }

                i++
            });
            if (finalResult === false) {
                $('#shortFall').html('The current status is "shortfall". Applicant has to Re-submit again');
            } else {
                $('#shortFall').html('');
            }

            response.edge_path.forEach((edge) => {
                g.setEdge.apply(g, edge);
            });

            // Set the rankdir
            g.graph().rankdir = "LR";
            g.graph().nodesep = 60;

            // Set some general styles
            g.nodes().forEach(function(v) {
                var node = g.node(v);
                node.rx = node.ry = 5;
            });
            render(inner, g);
        }

        // Process flow graph load End


        // Shadow file history load by ajax
        // set flag for one time calling
        var is_shadow_file_loaded = false;

        function loadShadowFileHistory() {
            if (!is_shadow_file_loaded) {
                $.ajax({
                    url: "<?php echo url('/process/get-shadow-file-hist'); ?>/" +
                        '{{ \App\Libraries\Encryption::encodeId($appInfo->process_type_id) }}' + "/" +
                        '{{ \App\Libraries\Encryption::encodeId($appInfo->ref_id) }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $("#shadow_file_content_area").html(response.response);
                        $("#shadow-file-loading").hide();
                        is_shadow_file_loaded = true;
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                    },
                });
            }
        }

        // Shadow file history load by ajax End


        // Application history load by ajax
        // set flag for one time calling
        var is_app_history_loaded = false;

        function loadApplicationHistory() {
            if (!is_app_history_loaded) {
                $.ajax({
                    url: "<?php echo url('/process/get-app-hist'); ?>/" +
                        '{{ \App\Libraries\Encryption::encodeId($appInfo->process_list_id) }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $("#history_content_area").html(response.response);
                        $("#history-loading").hide();
                        is_app_history_loaded = true;
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                    },
                });
            }
        }

        // Application history load by ajax End
    </script>
@endsection