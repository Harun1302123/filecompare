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
        .select2 {
            display: block !important;
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

    <input type="hidden" name="_token" value="{{ csrf_token() }}">
@endsection
@section('footer-script')
    @include('partials.datatable-scripts')
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
                        //$('.batch-process').hide();
                        //$('.process-history').hide();
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
                                    } else if ($("#" + value.filed_id).hasClass("step")) {
                                        $("#" + value.filed_id).parent().css('position', 'relative');
                                        $("#" + value.filed_id).before('<i class="fa fa-question-circle" style="cursor: pointer; position: absolute; top: 20px; right: 10px;" data-toggle="tooltip" title="' + value.help_text + '" ></i>');
                                    } else {
                                        $("#" + value.filed_id).before('<i class="fa fa-question-circle" style="cursor: pointer; position: absolute; top: 10px; left: -5px;" data-toggle="tooltip" title="' + value.help_text + '" ></i>');
                                    }
                                } else if (value.type == 'bubble') {
                                    $("#" + value.filed_id).after('<i class="bubble' + count + ' fa fa-question-circle"  id="bubble' + count + " #" + datas + '" style="cursor: pointer; position: absolute; top: 10px; right: 0px;"onclick="showHelpText(this.id)" data-toggle="tooltip" title="Please click here"  ></i>');
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
    </script>
@endsection