<?php
$accessMode = ACL::getAccsessRight($process_info->acl_name);
if (!ACL::isAllowed($accessMode, $mode)) {
    die('You have no access right! Please contact with system admin if you have any query.');
}
?>
<?php
$user_type = Auth::user()->user_type;
?>
@extends('layouts.admin')
@section('content')
    @include('partials.messages')
    <link rel="stylesheet" href="{{ url("assets/plugins/select2.min.css") }}">
    <script src="{{ asset("assets/plugins/select2.min.js") }}"></script>
    <style>
        .node:active {
            fill: #fffa90;
        }
        .select2 {
            display: block !important;
            width: 100% !important;
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

        .wizard > .steps > ul > li {
            position: relative;
        }

        .steps .tooltip-inner a {
            background: #0f0f0f !important;
            margin: 0 !important;
            padding: 0 !important;
            cursor: pointer !important;
            color: #337ab7 !important;
            display: inline !important;
            text-decoration: underline !important;
        }

        .tooltip_text a {
            background: #0f0f0f !important;
            margin: 0 !important;
            padding: 0 !important;
            cursor: pointer !important;
            color: #337ab7 !important;
            display: inline !important;
            text-decoration: underline !important;
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
                    $('.msg').html('<div class="alert alert-success"><i class="fa fa-spinner fa-spin"></i> Opening form...</div>');
                } else if (checkTimeDif() > 6) {
                    $('.msg').html('<div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> It is almost done...</div>');
                } else if (checkTimeDif() > 2) {
                    $('.msg').html('<div class="alert alert-warning"><i class="fa fa-spinner fa-spin"></i> Preparing all data...</div>');
                } else {
                    $('.msg').html('<div class="alert alert-danger"><i class="fa fa-spinner fa-spin"></i> Please Wait...</div>');
                }

                // if(form_load_status == 1){
                //     clearTimeout(interval);
                // }

                interval = setTimeout(timeDelay, 1000);
            }
        </script>
        <?php $userPic = URL::to('/assets/images/loading-ttcredesign.gif'); ?>
        <img id="loading-image" src="{{$userPic}}" alt="Loading..." onload="timeDelay()"/>
    </div>
    {{--Include Batch Process--}}
    <?php
    $session_get = Session::get('is_batch_update');
    $is_delegation = Session::get('is_delegation');
    $single_process_id_encrypt = Session::get('single_process_id_encrypt');
    $next_app_info = Session::get('next_app_info');
    $total_selected_app = Session::get('total_selected_app');
    $total_process_app = Session::get('total_process_app');
    ?>
    @if(ACL::isAllowed($accessMode, '-UP-') && $viewMode == 'on' && !empty($hasDeskDepartmentWisePermission))
        <div>
            @include('ProcessPath::batch-process')
        </div>
        {{--@elseif(ACL::isAllowed($accessMode, '-UP-') && $viewMode == 'on' &&--}}
        {{--($appInfo->desk_id == 5 && (in_array(5, \App\Libraries\CommonFunction::getUserDeskIds()) || in_array(5, \App\Libraries\CommonFunction::getDelegatedUserDeskIds())) && $appInfo->department_id == 0 && $appInfo->process_type_id == 100))--}}
        {{--<div class="batch-process">--}}
        {{--@include('ProcessPath::batch-process')--}}
        {{--</div>--}}
    @else
        @include('ProcessPath::batch-process-skip')
    @endif

    {{--Include Application Process Flow--}}
    @if(in_array(Auth::user()->user_type, array('1x101','3x303','4x404', '13x303', '2x202'))  && $viewMode == 'on')
        <div class="col-md-12 clearfix">

            @if((Request::segment(2) != 'bida-registration' )  && Request::segment(6)){{-- 6= meeting module --}}
            <?php
            //            $boardMeetingInfo = CommonFunction::getBoardMeetingInfo(Request::segment(4)); // 4 = app id
            ?>
            {{--@include('BoardMeting::MeetingAndAgendaInfo')--}}
            @endif

        </div>
        {{--Process remaining day--}}

        @if(!in_array($appInfo->status_id,[-1,5,6]) && Auth::user()->user_type != '2x202')
            <div class="col-sm-12 clearfix">
                <div class="alert alert-success" role="alert" style="margin-bottom: 5px">
                    @if($appInfo->auto_process == 1)
                        <strong><i class="fa fa-gear"></i> The remaining days of application
                            processing: {{ $remainingDay }}
                            <br/>
                            <i class="fa fa-briefcase"></i> Automatic application processing : <i
                                    class="fa fa-check"></i>
                        </strong>
                    @else
                        <strong><i class="fa fa-gear"></i> The remaining days of application
                            processing: {{ $remainingDay }}
                            <br/>
                            <i class="fa fa-briefcase"></i> Automatic application processing : <i
                                    class="fa fa-times"></i>
                        </strong>
                    @endif
                </div>
            </div>
        @endif

        {{--button--}}
        <div class="col-sm-12 clearfix" style="margin-bottom: 5px">
            <div class="pull-right">

                @if(in_array(Auth::user()->user_type, ['1x101','2x202','4x404']) && ($appInfo->final_status == $appInfo->status_id))
                    <a href="{{ url('process/resend-email/'.\App\Libraries\Encryption::encodeId($appInfo->process_type_id).'/'. \App\Libraries\Encryption::encodeId($appInfo->ref_id).'/'. \App\Libraries\Encryption::encodeId($appInfo->status_id)) }}"
                       class="btn btn-primary">
                        Resend Email
                    </a>

                    <a href="{{ url('process/certificate-regeneration/'.\App\Libraries\Encryption::encodeId($appInfo->ref_id).'/'. \App\Libraries\Encryption::encodeId($appInfo->process_type_id)) }}"
                       class="btn btn-warning">
                        Approval Copy Regenerate
                    </a>
                @endif

                {{--@if(in_array($process_info->id, [100]) && in_array($appInfo->status_id,[25]) && \App\Libraries\ACL::getAccsessRight('processPath', '-CD-'))--}}
                {{--                    <div class="modal fade" id="changeDeptModal" tabindex="-1" role="dialog" aria-labelledby="changeDeptModalLabel" aria-hidden="true">--}}
                {{--                        <div class="modal-dialog modal-lg">--}}
                {{--                            <div class="modal-content load_modal"></div>--}}
                {{--                        </div>--}}
                {{--                    </div>--}}

                {{--                    <a class="btn btn-primary" data-toggle="modal" data-target="#changeDeptModal" onclick="openChangeDeptModal(this)" data-action="{{ url('process/change-dept/'.\App\Libraries\Encryption::encodeId($appInfo->department_id)) }}">--}}
                {{--                        Change Department--}}
                {{--                    </a>--}}
                {{--@endif--}}

                <button class="btn btn-info" id="processMap">View Process Map</button>

                {{--<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#processMap" aria-expanded="false" aria-controls="processMap">--}}
                {{--View Process Map--}}
                {{--</button>--}}



                @if($process_info->id != 100 && !empty($basicAppID))
                    <a target="_blank" href="{{ url($BiRoute) }}" class="btn btn-success">View Corresponding Basic
                        Information</a>
                @endif

                {{--<button class="btn btn-primary" id="viewShadowFileBtn">View Shadow File History</button>--}}

                @if(Auth::user()->user_type != '2x202')
                    <button class="btn btn-primary btn-md" type="button" data-toggle="collapse"
                            data-target="#viewShadowFileBtn"
                            aria-expanded="false" aria-controls="viewShadowFileBtn">
                        View Shadow File History
                    </button>
                @endif
            </div>
        </div>

        <div class="col-md-12">
            @include('ProcessPath::process-flow-graph')
        </div>

        {{--Include shadow file history--}}
        @include('ProcessPath::shadow-file-history')
    @endif

    <?php
    //    $errors = Session::get("errors");
    ?>

    @if($errors->has())
        <div class="col-sm-12">
            <div class="alert alert-danger">
                <div class="row">
                    @foreach ($errors->all() as $error)
                        <li class="col-md-4 col-sm-12">{{ $error }}</li>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="load_content" style="clear: both;"></div>
    {{--Include application history, shadow file history--}}
    @if(in_array(Auth::user()->user_type, array('1x101','2x202','3x303','4x404','5x505','9x901','9x902','9x903','9x904')) && $viewMode == 'on'  && !in_array($appInfo->process_type_id,config('stackholder.allow_for_view_edit_route')))
        <div class="col-md-12 process-history">
            @include('ProcessPath::application-history')
        </div>
    @endif

    <input type="hidden" name="_token" value="{{ csrf_token() }}">
@endsection
@section('footer-script')
    @include('partials.datatable-scripts')
    <script src="{{ asset("assets/scripts/datetimepicker_css.js") }}"></script>
    @if($viewMode != 'off')
        <script src="https://d3js.org/d3.v4.min.js" charset="utf-8"></script>
        <script src="{{asset('assets/plugins/dagrejs/dagre-d3.min.js')}}"></script>
    @endif
    <script>

        // Loaded Application form
        $(document).ready(function () {
            var openMode = '{{ $openMode }}';
            var process_type_id = '{{ $process_info->id }}';
            if (openMode == 'add') {
                var url = '{{ $url }}';
            } else {
                var app_id = '{{(isset($app_id) ? $app_id: '')}}';
                var url = '{{ $url }}' + app_id + '/' + openMode;
            }

            if (url != '') {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    type: "get",
                    dataType: "json",

                    url: "<?php echo url(); ?>/" + url,
                    data: {
                        openMode: openMode,
                        process_type_id: process_type_id
                    },
                    success: function (response) {
                        if (response.html == undefined) {
                            console.log(response);
                            $('.load_content').html("<h3 style='color: red;margin: 250px;text-align: center'>Form not found!!</h3>");
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
                    error: function (jqXHR, exception) {
                        $('#loading').hide();
                        clearTimeout(interval);

                        var msg = "<h3 style='color: #fcf8e3;margin: 250px;text-align: center'>Something was wrong!!!!</h3>";
                        if (jqXHR.status === 0) {
                            msg = "<h3 style='color: #fcf8e3;margin: 250px;text-align: center'>Not connect.\n Verify Network.</h3>";
                        } else if (jqXHR.status == 404) {
                            msg = "<h3 style='color: #fcf8e3;margin: 250px;text-align: center'>Requested page not found. [404]</h3>";
                        } else if (jqXHR.status == 500) {
                            msg = "<h3 style='color: #fcf8e3;margin: 250px;text-align: center'>Something was wrong!!!!</h3>Internal Server Error [500].";
                        } else if (exception === 'parsererror') {
                            msg = "<h3 style='color: #fcf8e3;margin: 250px;text-align: center'>Something was wrong!!!!</h3>Requested JSON parse failed.";
                        } else if (exception === 'timeout') {
                            msg = "<h3 style='color: #fcf8e3;margin: 250px;text-align: center'>Time out error.</h3>";
                        } else if (exception === 'abort') {
                            msg = "<h3 style='color: #fcf8e3;margin: 250px;text-align: center'>Ajax request aborted.</h3>";
                        } else {
                            msg = "<h3 style='color: #fcf8e3;margin: 250px;text-align: center'>Page not loaded successfully, Please reload the page again.\n</h3>" + jqXHR.responseText;
                        }
                        $('.batch-process').hide();
                        $('.process-history').hide();
                        $('.load_content').html(msg);
                    },
                    complete: function (response) {
                        form_load_status = 1;
                        clearTimeout(interval);
                        $('#loading').hide();
                        getHelpText();
                    }

                });
            } else {
                $('.load_content').html("<h3 style='color: red;margin: 250px;text-align: center'>Undefined URL in Process Type!</h3>");
                $('.batch-process').hide();
                $('.process-history').hide();
                $('#loading').hide();
            }
        });

        // Help text show
        function getHelpText() {
            // Get application module name
            var uri = '{{ Request::segment(1) }}';
            if (uri == 'process') {
                var submodule = '{{ Request::segment(3) }}';
                if (submodule == 'view' || submodule == 'add' || submodule == 'edit-app') {
                    uri = '{{ Request::segment(2) }}';
                } else {


                    uri = '{{ Request::segment(3) }}';
                    let servicekey = '';
                    @if(isset($serviceKey))
                     servicekey = '{{$serviceKey}}';
                    if(uri == 'external-service' || servicekey !=''){
                        uri = servicekey;
                    }
                    @endif

                }
            }
            if (uri) {
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    type: "POST",
                    url: "<?php echo url(); ?>/process/help-text", //checking open mode permission and get url
                    data: {
                        uri: uri,
                        _token: _token
                    },
                    success: function (response) {
                        $.each(response.data, function (key, value) {
                            datas = value.help_text;
                            count = key;
                            if (value.filed_id) {

                                // Front-end validation class
                                var validate_class = value.validation_class;

                                // New Datepicker initialization
                                if (validate_class == 'secure_datepicker') {
                                    $("#" + value.filed_id).closest("div").addClass('newDateTimePicker');
                                }
                                // End New Datepicker initialization

                                // Old Datepicker initialization
                                if (validate_class == 'unsecure_datepicker') {
                                    $("#" + value.filed_id).next().attr('onclick', "javascript:NewCssCal('" + value.filed_id + "', 'ddMMMyyyy', 'arrow', '', '', '', '')");
                                }
                                // End Old Datepicker initialization


                                if (validate_class.search("required") != -1) {

//                                    $("#office_mobile_no").closest("div").parent("div").prev().addClass('required-star');


                                    var closest_div = $("#" + value.filed_id).closest("div");
                                    (closest_div.hasClass('input-group') || closest_div.hasClass('intl-tel-input')) == false ?
                                        closest_div.prev().addClass('required-star') :
                                        closest_div.parent("div").prev().addClass('required-star');

                                }
                                $("#" + value.filed_id).addClass(value.validation_class);

                                if (value.type == 'tooltip') {

                                    if ($("#" + value.filed_id).hasClass("date") || $("#" + value.filed_id).hasClass("helpText15")) {
                                        $("#" + value.filed_id).before('<i class="fa fa-question-circle" style="cursor: pointer; position: absolute; top: 10px; left: -15px;" data-toggle="tooltip" title="' + value.help_text + '" ></i>');
                                    } else if ($("#" + value.filed_id).hasClass("helpTextCom")) {
                                        $("#" + value.filed_id).before('<i class="fa fa-question-circle" style="cursor: pointer; position: absolute; top: 10px; right: -5px;" data-toggle="tooltip" title="' + value.help_text + '" ></i>');
                                    } else if ($("#" + value.filed_id).hasClass("helpTextRadio")) {
                                        $("#" + value.filed_id).before('<i class="fa fa-question-circle" style="cursor: pointer; position: absolute; top: 3px; left: -20px;" data-toggle="tooltip" title="' + value.help_text + '" ></i>');
                                    } else if ($("#" + value.filed_id).hasClass("helpTextCheckbox")) {
                                        $('body').on('mouseover', '#' + value.filed_id, function () {
                                            $(this).attr("data-toggle", 'tooltip');
                                            $(this).attr("data-original-title", value.help_text).tooltip({delay: {show: 1500}});
                                        });


                                    } else if ($("#" + value.filed_id,).hasClass("step")) {
                                        $("#" + value.filed_id).parent().css('position', 'relative');
                                        $("#" + value.filed_id).before('<i class="fa fa-question-circle" style="cursor: pointer; position: absolute; top: 20px; right: 10px;" data-toggle="tooltip" title="' + value.help_text + '" ></i>');
                                    } else {
                                        // $("#" + value.filed_id).before('<i class="fa fa-question-circle" style="cursor: pointer; position: absolute; top: 10px; left: -5px;" data-toggle="tooltip" title=' + value.help_text + ' ></i>');
                                        $("#" + value.filed_id).before("<i class='fa fa-question-circle' style='cursor: pointer; position: absolute; top: 10px; left: -5px;' data-toggle='tooltip' title='" + value.help_text + "' ></i>");
                                    }
                                } else if (value.type == 'bubble') {
                                    $("#" + value.filed_id).after('<i class="bubble' + count + ' fa fa-question-circle"  id="bubble' + count + " #" + datas + '" style="cursor: pointer; position: absolute; top: 10px; right: 0px;"onclick="showHelpText(this.id)" data-toggle="tooltip" title="Please click here"  ></i>');
                                }

                                var textlength = value.filed_max_length;
                                if (textlength && textlength != 0) {
                                    $("#" + value.filed_id).attr('maxlength', textlength);
                                }
                            }
                        });
                    },
                    complete: function () {
                        $('[data-toggle="tooltip"]').tooltip({
                            html: true,
                            trigger: 'click hover',
                            placement: function (tip, element) {
                                const position = $(element).position();
                                if (position.left > 515) {
                                    return "left";
                                }
                                if (position.left < 515) {
                                    return "right";
                                }
                                if (position.top < 110) {
                                    return "bottom";
                                }
                                return "top";
                            }
                        });
                    }
                });
            }
        }

        function showHelpText(datas) {
            var id = datas.split('#')[0];
            var description = datas.split('#')[1];
            var html = '<p class="triangle-right top">' + description + '</p>';
            $("." + id).after(html).toggle()
        }

        // Process flow graph load
        var viewMode = "{{$viewMode}}";
        var base_url = "{{url('/')}}";
        if (viewMode != 'off') {
            sendRequestAndDraw('{{ $process_info->id }}', '{{ (isset($appInfo->ref_id) ? \App\Libraries\Encryption::encodeId($appInfo->ref_id) : 0) }}', '{{ (isset($cat_id) ? \App\Libraries\Encryption::encodeId($cat_id) : 1) }}');
        }

        function sendRequestAndDraw(processId, app_id, cat_id) {
            $.ajax({
                url: base_url + '/process/graph/' + processId + '/' + app_id + '/' + cat_id,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    processPath(response);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                },
            });
        }

        function processPath(response) {

            var svg = d3.select("svg"),
                inner = svg.select("g");
            var zoom = d3.zoom().on("zoom", function () {
                inner.attr("transform", d3.event.transform);
            });

            svg.call(zoom);
            // Create the renderer
            var render = new dagreD3.render();
            var g = new dagreD3.graphlib.Graph().setGraph({});


            var i = 0;
            var finalResult = true;
            var message = '';
            response.desks.forEach(function (desk) {
                // console.log(response.passed_desks_id);
                if (i === 0) {
                    // console.log(response.passed_status_id[i])
                    if (response.passed_status_id[i] == 5) { //5= shortfall
                        finalResult = false;
                    }
                }
                // console.log(desk.desk_id)
                g.setNode(desk.name, {label: desk.label});
                if (response.passed_desks_id.indexOf(desk.desk_id) != -1 && finalResult === true) {
                    g.node(desk.name).style = "fill: orange";
                } else {
                    if (desk.desk_id == 0)
                        g.node(desk.name).style = "fill: orange";
                    else
                        g.node(desk.name).style = "fill: #666";
                }
            });

            response.desk_action.forEach(function (action) {
                if (i === 0) {
                    if (response.passed_status_id[i] == 5) { //5= shortfall
                        finalResult = false;
                    }
                }

                g.setNode(action.name, {label: action.label, shape: action.shape});
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
            g.nodes().forEach(function (v) {
                var node = g.node(v);
                node.rx = node.ry = 5;
            });
            render(inner, g);
        }

        var today = new Date();
        $('.datetimepicker').datetimepicker({
            minDate: today,
        });
    </script>

    @if($session_get == 'batch_update')

        <script>
            (function (global) {

                if (typeof (global) === "undefined") {
                    throw new Error("window is undefined");
                }

                var _hash = "!";
                var noBackPlease = function () {
                    global.location.href += "#";

                    // making sure we have the fruit available for juice....
                    // 50 milliseconds for just once do not cost much (^__^)
                    global.setTimeout(function () {
                        global.location.href += "!";
                    }, 50);
                };

                // Earlier we had setInerval here....
                global.onhashchange = function () {
                    if (global.location.hash !== _hash) {
                        global.location.hash = _hash;
                    }
                };

                global.onload = function () {

                    noBackPlease();

                    // disables backspace on page except on input fields and textarea..
                    document.body.onkeydown = function (e) {
                        var elm = e.target.nodeName.toLowerCase();
                        if (e.which === 8 && (elm !== 'input' && elm !== 'textarea')) {
                            e.preventDefault();
                        }
                        // stopping event bubbling up the DOM tree..
                        e.stopPropagation();
                    };

                };

            })(window);
        </script>
    @endif

    @if(in_array(Auth::user()->user_type, array('1x101','2x202','3x303','4x404','5x505','9x901','9x902','9x903','9x904'))  && $viewMode == 'on')
        <script>
            $('#app_history_table').DataTable({
                searching: false,
                paging: false,
                info: false,
                ordering: false,
                responsive: true
            });
        </script>
    @endif
@endsection