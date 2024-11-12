@extends('layouts.admin')

@section('page_heading',trans('messages.user_list'))

@section('content')
<?php use App\Libraries\ACL;$accessMode=ACL::getAccsessRight('user');
if(!ACL::isAllowed($accessMode,'V')) die('no access right!');
?>
<div class="col-lg-12">

    {!! Session::has('success') ? '<div class="alert alert-success alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("success") .'</div>' : '' !!}
    {!! Session::has('error') ? '<div class="alert alert-danger alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("error") .'</div>' : '' !!}
    <div class="panel panel-primary">
        <div class="panel-heading">
            &nbsp;
            <!--1x101 is Sys Admin, 7x712 is SB Admin, 11x422 is Bank Admin-->
            @if(ACL::getAccsessRight('user','A'))
            <a class="" href="{{ url('/users/create-new-user') }}">
                {!! Form::button('<i class="fa fa-plus"></i><b> ' .trans('messages.new_user').'</b>', array('type' => 'button', 'class' => 'btn btn-info')) !!}
            </a>
            @endif
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">
            <div class="table-responsive">
                <table id="list" class="table table-striped table-bordered dt-responsive " cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>User Full Name</th>
                            <th>Email Address</th>
                            <th>User Type</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Member Since</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div><!-- /.table-responsive -->
        </div><!-- /.panel-body -->
    </div><!-- /.panel -->
</div><!-- /.col-lg-12 -->
<div class="col-md-12">
    <!-- Modal -->
    <div class="modal fade" id="userModal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">User Configuration
                        <span class="loading" style="display: none;">
                            {!! Html::image("assets/images/ajax-loader.gif", "Loading...") !!}
                        </span>
                    </h4>

                </div>
                <div class="modal-body">
                    {{--@include('users::view-printable')--}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary submit_captcha">Submit</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection <!--content section-->

@section('footer-script')

<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

@include('partials.datatable-scripts')
<script>
    $(function () {
        $('#list').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{url("users/get-row-details-data")}}',
                method:'post',
                data: function (d) {
                    d._token = $('input[name="_token"]').val();
                }
            },
            columns: [
                {data: 'user_full_name', name: 'user_full_name'},
                {data: 'user_email', name: 'user_email'},
                {data: 'type_name', name: 'type_name'},
                {data: 'users_district', name: 'users_district'},
                {data: 'user_status', name: 'user_status'},
                {data: 'user_first_login', name: 'user_first_login'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            "aaSorting": []
        });
    });

//    function openModal(obj)
//    {
//        $.get('/users/view/'+$(obj).data('id'), function (response) {
//            $('#').modal();
//        });
//        return false;
//    }


</script>

@endsection <!--- footer-script--->
