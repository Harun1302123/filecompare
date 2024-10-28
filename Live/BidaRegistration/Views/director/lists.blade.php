<?php
$accessMode = ACL::getAccsessRight('BidaRegistration');
if (!ACL::isAllowed($accessMode, $mode)) {
    die('You have no access right! Please contact with system admin if you have any query.');
}
?>

@extends('layouts.admin')
@section('content')
    <section class="content">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body" id="inputForm">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="pull-left">
                                <h5><strong>Show all directors</strong></h5>
                            </div>
                            <div class="pull-right">

                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered table-responsive" id="list">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Designation</th>
                                            <th>Nationality</th>
                                            <th>Identity type</th>
                                            <th>NID/Passport No.</th>
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
    </section>

@endsection
@section('footer-script')
    @include('partials.datatable-scripts')

    <script>
        $(function () {
            $('#list').DataTable({
                processing: true,
                serverSide: true,
                iDisplayLength: 10,
                ajax: {
                    method: 'get',
                    url: '{{ url("/bida-registration/get-directors-more-lists") }}',
                    data:{
                        encoded_app_id : '{{ $encoded_app_id }}',
                        encoded_process_type_id : '{{ $encoded_process_type_id }}',
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'sl', name: 'sl'},
                    {data: 'l_director_name', name: 'l_director_name'},
                    {data: 'l_director_designation', name: 'l_director_designation'},
                    {data: 'l_director_nationality', name: 'l_director_nationality'},
                    {data: 'identity_type', name: 'identity_type'},
                    {data: 'nid_etin_passport', name: 'nid_etin_passport'}
                ],
                "aaSorting": []
            });
        });
    </script>
@endsection <!--- footer script--->



