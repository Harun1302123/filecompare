@extends('layouts.admin')

@section('content')
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content load_modal"></div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <strong style="line-height: 35px;"><i class="fa fa-home"></i> Details List</strong>
                <div class="clearfix"></div>
            </div>

            <div class="panel-body">
                @include('partials.messages')
                <div class="nav-tabs-custom">
                    <ul class="nav nav-pills" id="myTabs">
                        <li class="{{ ($list_type == 'director') ? 'active' : '' }}">
                            <a data-toggle="tab" href="#tab_1" aria-expanded="true">
                                <strong> Directors</strong>
                            </a>
                        </li>
                        <li class="{{ ($list_type == 'imported-machinery') ? 'active' : '' }}">
                            <a data-toggle="tab" href="#tab_2" aria-expanded="false">
                                <strong> Imported machinery</strong>
                            </a>
                        </li>
                        <li class="{{ ($list_type == 'local-machinery') ? 'active' : '' }}">
                            <a data-toggle="tab" href="#tab_3" aria-expanded="false">
                                <strong> Local machinery</strong>
                            </a>
                        </li>

                    </ul>
                    <br>

                    <div class="tab-content">
                        <div id="tab_1" class="tab-pane {{ ($list_type == 'director') ? 'active' : '' }}">
                            <div class="table-responsive">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <strong style="line-height: 35px;"><i class="fa fa-home"></i> Directors </strong>
                                        @if((in_array($status_id, [-1,5])) && (Auth::user()->desk_id == 0)  && $viewMode != 'on')
                                            <a data-toggle="modal" data-target="#directorModel"
                                               onclick="openModal(this)"
                                               data-action="{{ url('/bida-registration/create-director/'. $app_id) }}">
                                                {!! Form::button('<i class="fa fa-plus"></i> <b>Add New Director</b>', array('type' => 'button', 'class' => 'pull-right btn btn-default')) !!}
                                            </a>
                                        @endif
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="panel-body">
                                        <table id="directorList" class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Designation</th>
                                                <th>Nationality</th>
                                                <th>NID/ Passport No</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.tab-pane -->

                        <div id="tab_2" class="tab-pane {{ ($list_type == 'imported-machinery') ? 'active' : '' }}">
                            <div class="results">
                                <div class="table-responsive">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <strong style="line-height: 35px;"><i class="fa fa-home"></i> Imported machinery</strong>
                                            @if((in_array($status_id, [-1,5])) && (Auth::user()->desk_id == 0)  && $viewMode != 'on')
                                                <a data-toggle="modal" data-target="#importedMachineryModel"
                                                   onclick="openModal(this)"
                                                   data-action="{{ url('/bida-registration/create-imported-machinery/'. $app_id) }}">
                                                    {!! Form::button('<i class="fa fa-plus"></i> <b>Add New Imported Machinery</b>', array('type' => 'button', 'class' => 'pull-right btn btn-default')) !!}
                                                </a>
                                            @endif
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="panel-body">
                                            <table id="importedMachineryList" class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name of machineries</th>
                                                    <th>Quantity</th>
                                                    <th>Unit prices TK</th>
                                                    <th>Total value (Million) TK</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot align="right">
                                                <tr>
                                                    <th colspan="4" style="text-align: right">Total:</th>
                                                    <th colspan="2"></th>
                                                </tr>
                                                <tr>
                                                    <th colspan="4" style="text-align: right">Grand total:</th>
                                                    <th id="totalImported" colspan="2"></th>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.tab-pane -->

                        <div id="tab_3" class="tab-pane {{ ($list_type == 'local-machinery') ? 'active' : '' }}">
                            <div class="results">
                                <div class="table-responsive">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <strong style="line-height: 35px;"><i class="fa fa-home"></i> Local machinery</strong>
                                            @if((in_array($status_id, [-1,5])) && (Auth::user()->desk_id == 0) && $viewMode != 'on')
                                                <a data-toggle="modal" data-target="#localMachineryModel"
                                                   onclick="openModal(this)"
                                                   data-action="{{ url('/bida-registration/create-local-machinery/'. $app_id) }}">
                                                    {!! Form::button('<i class="fa fa-plus"></i> <b>Add New Local Machinery</b>', array('type' => 'button', 'class' => 'pull-right btn btn-default')) !!}
                                                </a>
                                            @endif
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="panel-body">
                                            <table id="localMachineryList" class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name of machineries</th>
                                                    <th>Quantity</th>
                                                    <th>Unit prices TK</th>
                                                    <th>Total value (Million) TK</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot align="right">
                                                <tr>
                                                    <th colspan="4" style="text-align: right">Total:</th>
                                                    <th colspan="2"></th>
                                                </tr>
                                                <tr>
                                                    <th colspan="4" style="text-align: right">Grand total:</th>
                                                    <th id="totalLocal" colspan="2"></th>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.tab-pane -->
                    </div><!-- /.tab-content -->
                </div><!-- /.table-responsive -->
            </div><!-- /.panel-body -->
        </div>
    </div>
@endsection
@section('footer-script')
    @include('partials.datatable-scripts')

    <script>
        $(document).ready(function(){
            var url = document.location.toString();
            if (url.match('#')) {
                $('.nav-pills a[href="#' + url.split('#')[1] + '"]').tab('show');
                $('.nav-pills a').removeClass('active');
            }
        });

        window.onload = function () {
            var hashedElementArray  = window.location.hash.split('#');
            var lastElement = hashedElementArray.length-1;
            var activeTab = $('.nav-tabs a[data-toggle="tab"][href=#' + hashedElementArray[lastElement] + ']');
            activeTab && activeTab.tab('show');
        };


        var _token = $('input[name="_token"]').val();

        $(function () {
            $('#directorList').DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "lengthChange": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "iDisplayLength": 10,
                "searching": true,
                ajax: {
                    url: '{{url("bida-registration/get-list-of-directors")}}',
                    method: 'post',
                    data: {
                        _token:$('input[name="_token"]').val(),
                        view_mode:'{{ $viewMode }}',
                        status_id:'{{ $status_id }}',
                        app_id:"{{ $app_id }}"
                    }
                },
                columns: [
                    {data: 'sl', name: 'sl'},
                    {data: 'l_director_name', name: 'l_director_name'},
                    {data: 'l_director_designation', name: 'l_director_designation'},
                    {data: 'nationality', name: 'nationality'},
                    {data: 'nid_etin_passport', name: 'nid_etin_passport'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                "aaSorting": []
            });

            $('#importedMachineryList').DataTable({
                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };

                    // Total over all pages
                    totalImported = api
                        .column( 4 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    // Total over this page
                    pageTotalImported = api
                        .column( 4, { page: 'current'} )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    // Update footer
                    $( api.column( 4 ).footer() ).html(pageTotalImported.toFixed(3));
                    $("#totalImported").html(totalImported.toFixed(3));

                },
                processing: true,
                serverSide: false,
                "paging": true,
                "lengthChange": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "iDisplayLength": 10,
                ajax: {
                    url: '{{url("bida-registration/get-list-of-imported-machinery")}}',
                    method: 'post',
                    data: {
                        _token:$('input[name="_token"]').val(),
                        view_mode:'{{ $viewMode }}',
                        status_id:'{{ $status_id }}',
                        app_id:"{{ $app_id }}"
                    }
                },
                columns: [
                    {data: 'sl', name: 'sl'},
                    {data: 'l_machinery_imported_name', name: 'l_machinery_imported_name'},
                    {data: 'l_machinery_imported_qty', name: 'l_machinery_imported_qty'},
                    {data: 'l_machinery_imported_unit_price', name: 'l_machinery_imported_unit_price'},
                    {data: 'l_machinery_imported_total_value', name: 'l_machinery_imported_total_value'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                "aaSorting": []
            });

            $('#localMachineryList').DataTable({
                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };

                    // Total over all pages
                    totalLocal = api
                        .column( 4 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    // Total over this page
                    pageTotalLocal = api
                        .column( 4, { page: 'current'} )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    // Update footer
                    $( api.column( 4 ).footer() ).html(pageTotalLocal.toFixed(3));
                    $("#totalLocal").html(totalLocal.toFixed(3));

                },
                processing: true,
                serverSide: false,
                "paging": true,
                "lengthChange": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "iDisplayLength": 10,
                ajax: {
                    url: '{{url("bida-registration/get-list-of-local-machinery")}}',
                    method: 'post',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        view_mode: '{{ $viewMode }}',
                        status_id: '{{ $status_id }}',
                        app_id: "{{ $app_id }}"
                    }
                },
                columns: [
                    {data: 'sl', name: 'sl'},
                    {data: 'l_machinery_local_name', name: 'l_machinery_local_name'},
                    {data: 'l_machinery_local_qty', name: 'l_machinery_local_qty'},
                    {data: 'l_machinery_local_unit_price', name: 'l_machinery_local_unit_price'},
                    {data: 'l_machinery_local_total_value', name: 'l_machinery_local_total_value'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                "aaSorting": []
            });

        }); // end of function

        function openModal(btn) {
            //e.preventDefault();
            var this_action = btn.getAttribute('data-action');
            if(this_action != ''){
                $.get(this_action, function(data, success) {
                    if(success === 'success'){
                        $('#myModal .load_modal').html(data);
                    }else{
                        $('#myModal .load_modal').html('Unknown Error!');
                    }
                    $('#myModal').modal('show', {backdrop: 'static'});
                });
            }
        }

        //confirm delete alert
        function ConfirmDelete(btn) {
            var sure_delete = confirm("Are you sure you want to delete?");
            if (sure_delete) {
                var url = btn.getAttribute('data-action');
                window.location=(url);
            } else{
                return false;
            }
        }

    </script>
@endsection <!--- footer script--->

