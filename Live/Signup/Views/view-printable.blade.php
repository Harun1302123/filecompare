@extends('layouts.admin')

@section('page_heading',trans('messages.user_view'))
@section('content')
<?php use App\Libraries\ACL;use App\Libraries\CommonFunction;use App\Libraries\Encryption;$accessMode=ACL::getAccsessRight('user');
if(!ACL::isAllowed($accessMode,'V')) die('no access right!');
?>
<div class="col-lg-12" xmlns="http://www.w3.org/1999/html">

    {!! Form::open(array('url' => 'users/reject/'.Request::segment(3),'method' => 'post', 'class' => 'form-horizontal', 'id' => 'rejectUser')) !!}
    <!-- Modal -->
    <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Reject User</h4>
                </div>
                <div class="modal-body">
                    <label class="required-star">Reject Reason : </label>
                    <textarea name="reject_reason" class="form-control" required ></textarea>
                </div>
                <div class="modal-footer">
                    @if(ACL::getAccsessRight('user','E'))
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{--modal end--}}

    {!! Form::close() !!}

    <section class="col-md-12" id="printDiv">
        <div class="row"><!-- Horizontal Form -->
            {!! Session::has('success') ? '<div class="alert alert-success alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("success") .'</div>' : '' !!}
            {!! Session::has('error') ? '<div class="alert alert-danger alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("error") .'</div>' : '' !!}

            {!! Form::open(array('url' => '/users/approve/'.Encryption::encodeId($user->id),'method' => 'post', 'class' => 'form-horizontal',   'id'=> 'user_edit_form')) !!}
            <div class="panel">
                <div class="panel-body">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">Profile of : {!! \App\Libraries\CommonFunction::getUserFullName() !!}</h3>
                        </div> <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="col-md-3">

                                @if (!empty($profile_pic))
                                    <img src="{{$profile_pic}}" alt="Auth letter" class="profile-user-img img-responsive img-circle" width="200"/>
                                @endif

                                @if (!empty($auth_file))
                                <a href="<?php echo $auth_file; ?>" target="_blank">
                                    Click to see authorization letter
                                </a>
                                @else

                                    <span class="text-danger">Authorization Letter Not Found</span>
                                @endif
                            </div>
                            <div class="col-md-9">

                                <dl class="dls-horizontal">
                                    <dt>Full Name :</dt>
                                    <dd>{!! \App\Libraries\CommonFunction::getUserFullName() !!}&nbsp;</dd>
                                    <dt>Type :</dt>
                                    <dd>{!!$user->type_name!!}&nbsp;</dd>
                                    <dt>NID :</dt>
                                    <dd>{!!$user->user_nid!!}&nbsp;</dd>
                                    <dt>Phone :</dt>
                                    <dd>{!!$user->user_phone!!}&nbsp;</dd>
                                    <dt>Email :</dt>
                                    <dd>{!!$user->user_email!!}&nbsp;</dd>
                                    <dt>Gender :</dt>
                                    <dd>{!!$user->user_gender!!}&nbsp;</dd>
                                    @if($user->district_name)
                                    <dt>District :</dt>
                                    <dd>{!!$user->district_name!!}&nbsp;</dd>
                                    @endif
                                    @if($user->thana_name)
                                    <dt>Thana :</dt>
                                    <dd>{!!$user->thana_name!!}&nbsp;</dd>
                                    @endif
                                    @if($user->user_DOB)
                                        <dt>Date of Birth :</dt>
                                        <dd>
                                            {!!CommonFunction::changeDateFormat($user->user_DOB)!!}&nbsp;
                                        </dd>
                                    @endif
                                    <dd>
                                    @if ($user->is_approved != 1)
                                        <dt>Verification expire time :</dt>
                                        <dd>{!!$user->user_hash_expire_time!!}&nbsp;</dd>
                                    @endif
                                    @if(in_array(Auth::user()->user_type,array("1x101","2x202","2x203")))
                                        @foreach($userMoreInfo as $key=>$info)
                                            <dt>{!!$key!!} :</dt>
                                            <dd>{!!$info!!}&nbsp;</dd>
                                        @endforeach
                                    @endif
                                </dl>


                                <?php
                                $approval = '';
                                $type = explode('x', $user->user_type);
                                if (substr($type[1], 2, 2) == 0) {
                                    echo Form::select('user_type', $user_types, $user->user_type, $attributes = array('class' => 'form-control required', 'required' => 'required',
                                        'placeholder' => 'Select One', 'id' => "user_type"));
                                }
                                $approval = '<button type="submit" class="btn btn-sm btn-success"> <i class="fa  fa-check "></i> Approve</button></form>';

                                 $approval.=' <a data-toggle="modal" data-target="#myModal2" class="btn btn-sm btn-danger addProjectModa2"><i class="fa fa-times"></i>&nbsp;Reject User</a> ';
                                ?>


                            </div>
                        </div><!-- /.box -->
                    </div>

                    <div class="col-md-12">

                        <div class="pull-left">
                            {{--<button type="button" class="btn btn-sm btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>--}}
                            <a href="{{ url('users/lists') }}" class="btn btn-sm btn-default"><i class="fa fa-times"></i> Close</a>
                        </div>
                        <div class="pull-right">
                            <?php
                            $changeDistrict = '<a href="' . url('users/change-sb-districts/' . Encryption::encodeId($user->id)) . '" class="btn btn-sm btn-info"><i class="fa fa-edit"></i> Change Districts </a>';
                            $edit = '<a href="' . url('users/edit/' . Encryption::encodeId($user->id)) . '" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>';
                            $reset_password = '<a href="' . url('users/reset-password/' . Encryption::encodeId($user->id)) . '" class="btn btn-sm btn-warning"'
                                    . 'onclick="return confirm(\'Are you sure?\')">'
                                    . '<i class="fa fa-refresh"></i> Reset password</a>';

                            $logged_in_user_type = Auth::user()->user_type;
                            if ($logged_in_user_type != '12_431') { // 12_431 is for Hajj Agency
                                if ($user->user_status == 'inactive') {
                                    $activate = '<a href="' . url('users/activate/' . Encryption::encodeId($user->id)) . '" class="btn btn-sm btn-success"><i class="fa fa-unlock"></i>  Activate</a>';
                                } else {
                                    $activate = '<a href="' . url('users/activate/' . Encryption::encodeId($user->id)) . '" class="btn btn-sm btn-danger"'
                                            . 'onclick="return confirm(\'Are you sure?\')">'
                                            . '<i class="fa fa-unlock-alt"></i> Deactivate</a>';
                                }
                            }

//                            if ($user->is_sub_admin == 0) {
//                                $make_sub_admin = '<a href="' . url('users/make-sub-admin/' . Encryption::encodeId($user->id)) . '" class=" btn btn-sm btn-success"><i class="fa fa-unlock"></i> Make Sub-admin</a>';
//                            } else {
//                                $make_sub_admin = '<a href="' . url('users/make-sub-admin/' . Encryption::encodeId($user->id)) . '" class=" btn btn-sm btn-danger"><i class="fa fa-unlock-alt"></i> Revoke Admin Permission</a>';
//                            }

                            if ($user->is_approved == true) {
                                if($logged_in_user_type=='2x203'){
                                    echo '<button>Send Password reset token </button>';
                                }elseif(CommonFunction::isAdmin()){
                                    if($logged_in_user_type=='7x711' || $logged_in_user_type=='7x712'){
                                        if(ACL::getAccsessRight('user','E'))
                                        echo '&nbsp;' . $changeDistrict;
                                    }
                                    if(ACL::getAccsessRight('user','E'))
                                        echo '&nbsp;' .$edit;
                                    if(ACL::getAccsessRight('user','R'))
                                        echo '&nbsp;' . $reset_password;
                                    if(ACL::getAccsessRight('user','E'))
                                        echo '&nbsp;' . $activate;
                                }
                            } else {
                                echo $approval;
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </section>
    @endsection <!--content section-->