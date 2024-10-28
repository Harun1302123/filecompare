{!! Form::open(['url' => '#process/search','method' => 'post','id' => ''])!!}
<div class="row">
    <div class="col-md-12">
        <div class="col-md-5">
            <div class="col-md-6 ">
                <label for="">Process Type: </label>
                {{--                {!! Form::select('ProcessType', ['' => 'Select One'] + $ProcessType, session('active_process_list'), ['class' => 'form-control search_type']) !!}--}}
                {!! Form::select('ProcessType', [0 => 'All'] + $ProcessType,'', ['class' => 'form-control search_type']) !!} {{--process_type get by javascript --}}
            </div>
            <div class="col-md-6 ">
                <label for="">Status: </label>

                {!! Form::select('status', $status,(isset($status_id) ? $status_id : '') , ['class' => 'form-control search_status', 'id' => 'search_status']) !!}
            </div>
        </div>
        <div class="col-md-2">
            <label for="">Search Text: </label>
            {!! Form::text('search_text', (!empty($search_by_keyword) ? $search_by_keyword : ''), ['class' => 'form-control search_text', 'placeholder'=>'Write something']) !!}
        </div>
        <div class="col-md-5">
            <div class="col-md-5 ">
                <label for="">Date within: </label>
                {!! Form::select('searchTimeLine', $searchTimeLine, 'all', ['class' => 'form-control search_time']) !!}
            </div>
            <div class="col-md-5">
                <label for="">of</label>
                <div class="date_within input-group date">
                    {!! Form::text('date_within', date('d-M-Y'), ['class' => 'form-control search_date']) !!}
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
                </div>
            </div>
            <div class="col-md-2" style="">
                <label for="">&nbsp;</label> <br>
                <input type="button" id="search_process" class="btn btn-primary" value="Search">

            </div>
            {{--@if(\App\Libraries\CommonFunction::getUserType() == '4x404')--}}
            {{--<div class="col-md-3">--}}
            {{--<label for="">&nbsp;</label>--}}
            {{--<button type="button" id="batch_update" class="btn btn-info batch_update_search"  style="background: #5cb85c"><i class="fa fa-recycle"></i> Batch processing</button>--}}
            {{--</div>--}}
            {{--@endif--}}
        </div>


    </div>
</div>
{!! Form::close()!!}
<div id="list_search" class="" style="margin-top: 20px;">
    <table id="table_search"
           class="table table-striped table-bordered display"
           style="width: 100%">
        <thead>
        <tr>
            <th style="width: 15%;">Tracking ID</th>
            {{--<th>Current Desk</th>--}}
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

@section('footer-script2')
    <script>
        $(function () {
            $('.date_within').datetimepicker({
                viewMode: 'days',
                format: 'DD-MMM-YYYY',
                maxDate: 'now'
            });

            $('.ProcessType').change(function () {
                $.get('{{route("process.setProcessType")}}',
                    {
                        _token: $('input[name="_token"]').val(),
                        data: $(this).val()
                    }, function (data) {
                        if (data == 'success') {
                            table_desk.ajax.reload();
                            var len = table.length;
                            for (var i = 0; i < len; i++) {
                                table[i].ajax.reload();
                            }
                        }
                    });
            });
            $('#table_search').hide();
            //var search_list = '';
            $('#search_process').click(function (e, process_type_id, status_id) {
                $('#batch_update').slideDown();
                if (typeof (process_type_id) != "undefined") { //process type selected by trigger
                    $('.search_type').val(process_type_id); // process type selected by js
                }
                if ($('.search_type').val() == '') {
                    var process_type_new = $('.search_type').val();
//                    if(process_type_new == ''){
//                        alert ('Please select the Process Type');
//                        return  false;
//                    }

                }
                $('#table_search').show();

                var searchStatus = '';
                var searchType = '';
                var cardStatus = "{{ isset($search_by_status) ? $search_by_status : 0 }}";
                if (typeof (status_id) != "undefined") {
                    searchType = process_type_id;
                    searchStatus = status_id;
                } else {
                    searchType = $('.search_type').val();
                    searchStatus = $('.search_status').val();
                }
                $('#table_search').DataTable({
                    destroy: true,
                    iDisplayLength: 50,
                    processing: true,
                    serverSide: true,
                    searching: false,
                    // responsive: true,
                    ajax: {
                        url: '{{route("process.getList")}}',
                        method: 'get',
                        data: function (d) {
                            d.process_search = true;
                            d.search_type = searchType;
                            d.search_time = $('.search_time').val();
                            d.search_text = $('.search_text').val();
                            d.search_date = $('.search_date').val();
                            d.search_status = searchStatus;
                            d.card_status = cardStatus;
                            d.process_type_id = process_type_id;
                        },
                        dataSrc: function (response) {

//                            if (response.recordsTotal == 1) {
//                                //console.log(response.data[0].action);
//                                window.location.href = 'https://www.google.com/';
//                            }

                            // if(response.responseType == 'lock_by_user') {
                            //     if (window.confirm('The record locked by '+response.lock_by_user + 'would you like to force unlock?'))
                            //     {
                            //         window.location.href = response.url;
                            //     }
                            //     else
                            //     {
                            //         window.location.href = '/process/list';
                            //     }
                            // }

                            if (response.responseType == 'single') {
                                window.location.href = response.url;
                            }

                            return response.data;
                        }
                    },
                    columns: [
                        {data: 'tracking_no', name: 'tracking_no', searchable: false},
                        {data: 'process_name', name: 'process_name', searchable: false},
                        {data: 'desk_id', name: 'desk_id'},
                        {data: 'status_name_updated_time', name: 'status_name', searchable: false},
//                        {data: 'status_name', name: 'status_name', searchable: false},
//                        {data: 'updated_at', name: 'updated_at', searchable: false},
                        {data: 'json_object', name: 'json_object'},
                        {data: 'action', name: 'action', orderable: false, searchable: false}
                    ],
                    "aaSorting": []
                });
            });

            // Global search or dashboard search option
            @if(!empty($search_by_keyword))
            $("#search_process").trigger('click');
            @endif

            @if(!empty($search_by_status))
            $("#search_process").trigger('click', [-1000, {{ $search_by_keyword }}]);
            @endif

            $('.search_type').change(function () {
                $.get('{{route("process.searchProcessType")}}', {
                    _token: $('input[name="_token"]').val(),
                    data: $(this).val()
                }, function (response) {
                    let option;
                    if (response.responseCode === 1) {
                        $.each(response.data, function (id, value) {
                            option += '<option value="' + id + '">' + value + '</option>';
                        });
                    }
                    $('#search_status').html(option);
                });
            });

            // function openCity(evt, cityName) {
            //     var i, tabcontent;
            //     tabcontent = document.getElementsByClassName("tabcontent");
            //     for (i = 0; i < tabcontent.length; i++) {
            //         tabcontent[i].style.display = "none";
            //     }
            //
            //     document.getElementById(cityName).style.display = "block";
            //     evt.currentTarget.className += " active";
            //
            // }

            $('.statusWiseList').click(function () {

                $('#list_desk').removeClass('active');
                $('#tab1').removeClass('active');
                $('#tab2').removeClass('active');
                $('#tab4').removeClass('active');
                $('#desk_user_application').removeClass('active');
                $('#list_delg_desk').removeClass('active');
                $('#tab3').addClass('active');
                $('#list_search').addClass('active');

                var data = $(this).attr("data-id");
                var typeAndStatus = data.split(",");
                var process_type_id = typeAndStatus[0];
                var statusId = typeAndStatus[1];

                //$("#search_process").trigger('click',[process_type_id,statusId]);
                $('#table_search').show();

                $('#table_search').DataTable({
                    destroy: true,
                    iDisplayLength: 50,
                    processing: true,
                    serverSide: true,
                    searching: false,
                    //responsive: true,
                    ajax: {
                        url: '{{route("process.getList")}}',
                        method: 'get',
                        data: function (d) {
                            d.process_status = statusId;
                            d.process_type_id = process_type_id;
                            d.status_wise_list = 'status_wise_list';
                        }
                    },
                    columns: [
                        {data: 'tracking_no', name: 'tracking_no', searchable: false},
                        {data: 'process_name', name: 'process_name', searchable: false},
                        {data: 'desk_id', name: 'desk_id'},
                        {data: 'status_name_updated_time', name: 'status_name', searchable: false},
//                        {data: 'status_name', name: 'status_name', searchable: false},
//                        {data: 'updated_at', name: 'updated_at', searchable: false},
                        {data: 'json_object', name: 'json_object'},
                        {data: 'action', name: 'action', orderable: false, searchable: false}
                    ],
                    "aaSorting": [],

                    "fnDrawCallback": function () {

                    }
                });
            });


        });

        //current used the code for batch update
        @if(\App\Libraries\CommonFunction::getUserType() == '4x404')
        $('body').on('click', '.common_batch_update_search', function () {
            let current_process_id = $(this).parent().parent().find('.batchInputSearch').val();
            process_id_array = [];
            //var id = $('.batchInputSearch').val();
            $('.batchInputSearch').each(function (i, obj) {
                process_id_array.push(this.value);
            });
            console.log(process_id_array);
            var _token = $('input[name="_token"]').val();
            $.ajax({
                type: "get",
                url: "<?php echo url(); ?>/process/batch-process-set",
                async: false,
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
                        return false;
                    }
                }
            });
        });

        $('body').on('click', '.status_wise_batch_update', function () {
            let current_process_id = $(this).parent().parent().find('.batchInputStatus').val();
            process_id_array = [];

            $('.batchInputStatus').each(function (i, obj) {
                process_id_array.push(this.value);
            });
            console.log(process_id_array);
            var _token = $('input[name="_token"]').val();
            $.ajax({
                type: "get",
                url: "<?php echo url(); ?>/process/batch-process-set",
                async: false,
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
                        return false;
                    }
                }
            });
        });
        @endif

        function checkPosition() {
            if(window.innerWidth < 768){
                $('.table').addClass("dt-responsive");
            }
        }
        checkPosition();
    </script>

@endsection