@extends('layouts.admin')

@section('page_heading',trans('messages.profile'))

@section('content')

    <div class="col-md-12">
        @if(Session::has('success'))
            <div class="alert alert-success alert-dismissible" >
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <i class="icon fa fa-check"></i>{{ Session::get('success') }}
            </div>
        @endif
        @if(Session::has('message'))
            <div class="alert alert-warning">
                {{session('message')}}
            </div>
        @endif
        @if(Session::has('error'))
            <div class="alert alert-warning">
                {{ Session::get('error') }}

            </div>
        @endif
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
                    <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="false">Profile</a></li>
                    <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">Change Password</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                        <div class="panel panel-info">
                            <div class="panel-body">
                                <div class="
                             ">
                                    <div class="col-md-6 col-sm-6">
                                        {!! Form::open(array('url' => '/users/profile_update','method' => 'patch', 'class' => 'form-horizontal', 'id'=> 'profile-form',
                                        'enctype' =>'multipart/form-data')) !!}
                                        <fieldset>
                                            <div class="row">
                                                <div class="progress hidden pull-right" id="upload_progress" style="width: 50%;">
                                                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                                    </div>
                                                </div>
                                            </div>

                                            @if($users->user_status == "rejected")
                                                <div class="form-group has-feedback {{ $errors->has('group_id') ? 'has-error' : ''}}">
                                                    <label  class="col-lg-4 text-left">Agency</label>
                                                    <div class="col-lg-8">
                                                        {!! Form::text('agency_id', $value = null, $attributes = array('class'=>'form-control bnEng required agency',
                                                        'placeholder'=>'Enter the Name of the Agency','id'=>"group_name",'onblur'=>'checkAutoComplete(this,"agency")')) !!}
                                                        {!! Form::hidden('agency_id','',array('class'=>'group_name_hidden','id'=>'group_id')) !!}
                                                        @if($errors->first('group_id'))
                                                            <span  class="control-label">
                                                    <em><i class="fa fa-times-circle-o"></i> {{ $errors->first('group_id','') }}</em>
                                                    </span>
                                                        @endif
                                                        <p class="empty-message"></p>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="form-group has-feedback">
                                                    <label  class="col-lg-4 text-left">User Type</label>
                                                    <div class="col-lg-8">
                                                        {{ $user_type_info->type_name }}
                                                    </div>
                                                </div>

                                                {{--Show Additional features/specification for users--}}
                                                {{--@foreach($additional_info as $info)--}}
                                                {{--<div class="form-group">--}}
                                                {{--<label  class="col-lg-4">{{$info['caption']}}</label>--}}
                                                {{--<div class="col-lg-8">--}}
                                                {{--{{$info['value']}}--}}
                                                {{--</div>--}}
                                                {{--</div>--}}
                                                {{--@endforeach--}}
                                                {{--end specifications--}}
                                            @endif

                                            <div class="form-group has-feedback">
                                                <label  class="col-lg-4 text-left">Email Address</label>
                                                <div class="col-lg-8">
                                                    {{ $users->user_email }}
                                                </div>

                                            </div>

                                            <div class="form-group has-feedback {{ $errors->has('user_full_name') ? 'has-error' : ''}}">
                                                <label  class="col-lg-4 text-left">User’s full name</label>
                                                <div class="col-lg-8">
                                                    {!! Form::text('user_full_name',$users->user_full_name, $attributes = array('class'=>'form-control required',
                                                    'placeholder'=>'Enter your Name','id'=>"user_full_name", 'data-rule-maxlength'=>'50')) !!}
                                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                                    {!! $errors->first('user_full_name','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label  class="col-lg-4 text-left">Date of Birth</label>
                                                <div class="col-lg-8">
                                                    <div class="datepicker input-group date" data-date-format="yyyy-mm-dd">
                                                        @if($users->user_DOB)
                                                            <?php $dob = App\Libraries\CommonFunction::changeDateFormat($users->user_DOB) ?>
                                                        @else
                                                            <?php $dob = '' ?>
                                                        @endif
                                                        {!! Form::text('user_DOB', $dob, ['class'=>'form-control required']) !!}
                                                        <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                                        {!! $errors->first('user_DOB','<span class="help-block">:message</span>') !!}


                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group has-feedback {{ $errors->has('user_phone') ? 'has-error' : ''}}">
                                                <label  class="col-lg-4 text-left">Mobile Number  </label>
                                                <div class="col-lg-8">
                                                    {!! Form::text('user_phone',$users->user_phone, $attributes = array('class'=>'form-control required mobile_number_validation',
                                                    'placeholder'=>'Enter your Mobile Number','id'=>"user_phone", 'data-rule-maxlength'=>'16')) !!}
                                                    <span class="text-danger mobile_number_error"></span>
                                                    <span class="glyphicon glyphicon-phone form-control-feedback"></span>
                                                    {!! $errors->first('user_phone','<span class="help-block">:message</span>') !!}
                                                </div>
                                            </div>

                                            <div class="form-group has-feedback {{ $errors->has('district') ? 'has-error' : ''}}">
                                                <label  class="col-lg-4 text-left">{!! trans('messages.district') !!}</label>
                                                <div class="col-lg-8">
                                                    {!! Form::select('district', $districts, $users->district, $attributes = array('class'=>'form-control required',
                                                    'id'=>"district")) !!}
                                                    @if($errors->first('district'))
                                                        <span class="control-label">
                                                    <em><i class="fa fa-times-circle-o"></i> {{ $errors->first('district','') }}</em>
                                                </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group has-feedback {{ $errors->has('thana') ? 'has-error' : ''}}">
                                                <label  class="col-lg-4 text-left">{!! trans('messages.thana') !!}</label>
                                                <div class="col-lg-8">
                                                    {!! Form::select('thana', [], $users->thana, $attributes = array('class'=>'form-control required',
                                                    'placeholder' => 'Select One', 'id'=>"thana")) !!}
                                                    @if($errors->first('thana'))
                                                        <span class="control-label">
                                                    <em><i class="fa fa-times-circle-o"></i> {{ $errors->first('thana','') }}</em>
                                                </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group has-feedback">
                                                <label  class="col-lg-4 text-left">User Status</label>
                                                <div class="col-lg-8">
                                                    {{ $users->user_status }}
                                                </div>
                                            </div>

                                            @if($users->user_status == "rejected")
                                                <div class="form-group has-feedback">
                                                    <label  class="col-lg-4 text-left">Reject Reason</label>
                                                    <div class="col-lg-8">
                                                        {{ $users->user_status_comment }}
                                                    </div>
                                                </div>
                                            @endif

                                            @if($users->user_status == "inactive")
                                                <div class="form-group has-feedback {{ $errors->has('authorization_file') ? 'has-error' : ''}}">
                                                    <label  class="col-lg-4 text-left  required-star">Authorization Letter<br/>
                                            <span style="font-size: 9px; color: grey">
                                                {!! $doc_config !!}
                                            </span>
                                                    </label>
                                                    <div class="col-lg-8">
                                                        {!! '<img src="' . $auth_file . '" class="profile-user-img img-responsive"  alt="Authorization File" id="authorized_file"  width="350" />' !!}
                                                        <br/>
                                                        <div id="auth_file_err" style="color: red;">

                                                        </div>
                                                        <input type="hidden" class="upload_flags" value="0">
                                                        {!! Form::file('authorization_file',['onchange'=>'readAuthFile(this)','data-ref'=>''.Encryption::encodeId(Auth::user()->id).'','data-type'=>'auth_file']) !!}
                                                        <button class="btn btn-xs btn-primary hidden change_btn" type="button">Change</button>
                                                        {!! $errors->first('authorization_file','<span class="help-block">:message</span>') !!}
                                                    </div>


                                                </div>
                                            @endif
                                            <div class="form-group">
                                                <div  class="col-lg-4"></div>
                                                <div class="col-lg-8">
                                                    <?php
                                                    $checked = '';
                                                    if ($users->auth_token_allow == '1')
                                                        $checked = "checked='checked'";
                                                    ?>
                                                    @if($user_type_info->auth_token_type=='optional')
                                                        <input type="checkbox" name="auth_token_allow" value="1" {{$checked}} id="all_second_step">
                                                        <label for="all_second_step">Allow two step verification</label>
                                                    @endif
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <div class="col-md-9"></div>
                                                <div class="col-md-3">
                                                    <button type="submit" class="btn btn-primary btn-block" id='update_info_btn'><b>Save</b></button>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-lg-12">
                                                    {!! App\Libraries\CommonFunction::showAuditLog($users->updated_at, $users->updated_by) !!}
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-1 col-sm-1"></div>

                                    <div class="col-md-5 col-sm-5"><br/>
                                        {{--$anonymous_image = '<img src="' . url() . '/users/upload/anonymous_image.jpg" alt="Anonymous Profile Picture" class="img-responsive img-circle"  />';--}}
                                        {{--$db_image = '<img src="' . url() . '/users/upload/' . $users->user_image . '" class="img-responsive img-circle" />';--}}
                                        {{--$image_html = ($users->user_image != "") ? $db_image : $anonymous_image;--}}
                                        {{--echo $image_html;--}}
                                        {{--if($users->user_image != "")--}}
                                        {!! '<img src="' . $profile_pic . '" class="profile-user-img img-responsive"  alt="Profile Picture" id="uploaded_pic"  width="200" />' !!}

                                        <div class="clearfix"><br/></div>
                                        <div class="form-group has-feedback">
                                            <label  class="col-lg-4 text-left">Profile Image <br/>


                                            </label>
                                            <div class="col-lg-8">
                                                <div id="user_err" style="color: red;"></div>
                                                <input type='file' onchange="readURL(this);" name="image"  data-type="user" data-ref="{{App\Libraries\Encryption::encodeId(Auth::user()->id)}}"/>
                                                <button class="btn btn-xs btn-primary hidden change_btn" onclick="return confirm('Are you sure?')" type="button">Upload this Image</button>
                                            </div>
                                            <div class="col-lg-2"></div>
                                            <div class="col-md-12" style="font-size: 12px; color: grey">
                                                {!! $image_config !!}
                                            </div>
                                        </div>
                                    </div>

                                    {!! Form::close() !!}
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div><!-- /.tab-pane -->

                    <div class="tab-pane" id="tab_2">
                        <div class="panel panel-info">
                            <div class="panel-body">
                                {!! Form::open(array('url' => '/users/update-password-from-profile','method' => 'patch', 'class' => 'form-horizontal',
                                'id'=> 'password_change_form')) !!}
                                <fieldset>
                                    <div class="clearfix"><br/><br/></div>

                                    <div class="form-group has-feedback {{ $errors->has('user_old_password') ? 'has-error' : ''}}">
                                        <label  class="col-lg-4 text-left">Old Password</label>
                                        <div class="col-lg-4">
                                            {!! Form::password('user_old_password', $attributes = array('class'=>'form-control required',
                                            'placeholder'=>'Enter your Old password','id'=>"user_old_password", 'data-rule-maxlength'=>'120')) !!}
                                            <span class="glyphicon glyphicon-check form-control-feedback"></span>
                                            {!! $errors->first('user_old_password','<span class="help-block">:message</span>') !!}
                                        </div>
                                    </div>

                                    <div class="form-group has-feedback {{ $errors->has('user_new_password') ? 'has-error' : ''}}">
                                        <label  class="col-lg-4 text-left">New Password</label>
                                        <div class="col-lg-4">
                                            {!! Form::password('user_new_password', $attributes = array('class'=>'form-control required',  'minlength' => "6",
                                            'placeholder'=>'Enter your New password','id'=>"user_new_password", 'data-rule-maxlength'=>'120')) !!}
                                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                            {!! $errors->first('user_new_password','<span class="help-block">:message</span>') !!}
                                        </div>
                                        <div class="col-lg-4">
                                            <code>[*Minimum 6 characters at least 1 Alphabet, 1 Number and 1 Special Character]</code>
                                        </div>
                                    </div>

                                    <div class="form-group has-feedback {{ $errors->has('user_confirm_password') ? 'has-error' : ''}}">
                                        <label  class="col-lg-4 text-left">Confirm New Password</label>
                                        <div class="col-lg-4">
                                            {!! Form::password('user_confirm_password', $attributes = array('class'=>'form-control required', 'minlength' => "6",
                                            'placeholder'=>'Confirm your New password','id'=>"user_confirm_password", 'data-rule-maxlength'=>'120')) !!}
                                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                            {!! $errors->first('user_confirm_password','<span class="help-block">:message</span>') !!}
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <div class="col-lg-2 col-lg-offset-6">
                                            <div class="clearfix"><br></div>
                                            <button type="submit" class="btn btn-block btn-primary" id="update_pass_btn"><b>Save</b></button>
                                        </div>
                                        <div class="col-lg-4"></div>
                                    </div>

                                    <div class="form-group has-feedback">
                                        <div  class="col-lg-1"></div>
                                        <div class="col-lg-5">
                                            {!! App\Libraries\CommonFunction::showAuditLog($users->updated_at, $users->updated_by) !!}
                                        </div>
                                    </div>
                                </fieldset>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                {!! Form::close() !!}
                            </div>
                        </div>

                    </div><!-- /.tab-pane -->

                </div><!-- /.tab-content -->
            </div><!-- nav-tabs-custom -->
    </div>

    <div class="clearfix"></div>

@endsection

@section('footer-script')

    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.devbridge-autocomplete/1.2.24/jquery.autocomplete.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

    @include('partials.datatable-scripts')

    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
    <script>

        $(function () {
            var _token = $('input[name="_token"]').val();
            $("#vreg_form").validate({
                errorPlacement: function () {
                    return false;
                }
            });
            $(".agency").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "{{url('users/get-agency')}}",
                        dataType: "json",
                        data: {
                            q: request.term
                        },
                        success: function (data) {
                            response(data);
                        }
                    });
                },
                response: function (event, ui) {
                    if (ui.content.length === 0) {
                        $(".empty-message").text("No results found");
                    } else {
                        $(".empty-message").empty();
                    }
                },
                select: function (event, data) {
                    $('.group_name_hidden').val(data.item.id);
                }
            });
        });


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function () {
            $("#district").change(function () {
                var self = $(this);
                var districtId = $('#district').val();
                if (districtId !== '') {
                    $(this).after('<span class="loading_data">Loading...</span>');
                    $("#loaderImg").html("<img style='margin-top: -15px;' src='<?php echo url(); ?>/public/assets/images/ajax-loader.gif' alt='loading' />");
                    $.ajax({
                        type: "GET",
                        url: "<?php echo url(); ?>/users/get-thana-by-district-id",
                        data: {
                            districtId: districtId
                        },
                        success: function (response) {
                            var option = '<option value="">Select One</option>';
                            if (response.responseCode == 1) {
                                $.each(response.data, function (id, value) {
                                    if (id == '{{$users->thana}}'){
                                        option += '<option value="'+ id + '" selected>' + value + '</option>';
                                    }
                                    else {
                                        option += '<option value="' + id + '">' + value + '</option>';
                                    }
                                });
                            }
                            $("#thana").html(option);
                            self.next().hide();
                        }
                    });
                }
            });
            $("#district").trigger('change');
        });

        $(function () {
            $('.datepicker').datetimepicker({
                viewMode: 'years',
                format: 'DD-MMM-YYYY',
                maxDate: 'now',
                minDate: '01/01/1916'
            });
        });

        $(function () {

            $('#accessList').DataTable({
                processing: true,
                serverSide: true,
                searchable: false,
                "bSort": false,
                "bLengthChange": false,
                "bJQueryUI": true,
                "bPaginate": false,
                "sScrollY": "150px",
                "bAutoWidth": false, // Disable the auto width calculation
                scrollCollapse: true,
                paging: false,
                ordering: true,
                ajax: {
                    url: '{{url("users/get-access-log-data")}}',
                    cache: false,
                    data: function (d) {
                        d._token = $('input[name="_token"]').val();
                    }
                },
                columns: [
                    {data: 'login_dt', name: 'login_dt'},
                    {data: 'logout_dt', name: 'logout_dt'},
                    {data: 'ip_address', name: 'ip_address'}
                ]
            });
        });

        $('#password_change_form').validate({
            rules: {
                user_confirm_password: {
                    equalTo: "#user_new_password"
                }
            },
            errorPlacement: function () {
                return false;
            }
        });

        $(document).ready(
                function () {
                    $("#profile-form").validate({
                        errorPlacement: function () {
                            return false;
                        }
                    });
                });

    </script>

    <style>
        #accessList{
            height: 100px !important;
            overflow: scroll;
        }
        .dataTables_scrollHeadInner{width:100% !important;}
        .profileinfo-table{width:100% !important;}
    </style>
@endsection