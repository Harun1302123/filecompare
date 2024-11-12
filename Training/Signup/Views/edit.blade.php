@extends('layouts.admin')

@section('page_heading',trans('messages.profile'))

@section("content")
    <?php
            use App\Libraries\Encryption;
    use App\Libraries\ACL;$accessMode = ACL::getAccsessRight('user');
    if (!ACL::isAllowed($accessMode, 'V'))
        die('no access right!');
    ?>
    <section class="content">

        <!-- Default box -->
        <div class="box box-success col-md-10">
            <div class="box-header with-border">

                @if(Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @endif
                @if(Session::has('error'))
                    <div class="alert alert-warning"> {{ Session::get('error') }}   </div>
                @endif
            </div>

            <div class="box-body">


                <div class="col-md-8">
                    <?php
                    $user_type_explode = explode('x', $users->user_type);
                    ?>
                    {!! Form::open(array('url' => '/users/update/'.Encryption::encodeId($users->id),'method' => 'patch', 'class' => 'form-horizontal',
                    'id'=> 'user_edit_form')) !!}
                    <fieldset>

                        {!! Form::hidden('selected_file', '', array('id' => 'selected_file')) !!}
                        {!! Form::hidden('validateFieldName', '', array('id' => 'validateFieldName')) !!}
                        {!! Form::hidden('isRequired', '', array('id' => 'isRequired')) !!}

                        <?php
                        $random_number = str_random(30);
                        ?>
                        {!! Form::hidden('TOKEN_NO', $random_number) !!}

                        <div class="form-group has-feedback {{ $errors->has('user_full_name') ? 'has-error' : ''}}">
                            <label  class="col-lg-4 text-left">{!! trans('messages.first_name') !!}</label>
                            <div class="col-lg-8">
                                {!! Form::text('user_full_name', $value = $users->user_full_name, $attributes = array('class'=>'form-control',
                                'id'=>"user_full_name", 'data-rule-maxlength'=>'50')) !!}
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                @if($errors->first('user_full_name'))
                                    <span class="control-label">
                                <em><i class="fa fa-times-circle-o"></i> {{ $errors->first('user_full_name','') }}</em>
                            </span>
                                @endif
                            </div>
                        </div>
                        @if(isset($user_type_explode[0]) && $user_type_explode[0]=='11')
                            <div class="form-group has-feedback">
                                <label  class="col-lg-4 text-left">Bank Name</label>
                                <div class="col-lg-8">
                                    {{ $bank_name }}
                                </div>
                            </div>


                            <div class="form-group has-feedback {{ $errors->has('bank_branch_id') ? 'has-error' : ''}}">
                                <label  class="col-lg-4 text-left">Bank Branch</label>
                                <div class="col-lg-8">
                                    {!! Form::select('bank_branch_id', $branch_list, $users->bank_branch_id, array('class'=>'form-control required',
                                    'placeholder' => 'Select One', 'id'=>"bank_branch_id")) !!}
                                    @if($errors->first('bank_branch_id'))
                                        <span class="control-label">
                                <em><i class="fa fa-times-circle-o"></i> {{ $errors->first('bank_branch_id','') }}</em>
                            </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="form-group has-feedback {{ $errors->has('user_type') ? 'has-error' : ''}}">
                            <label  class="col-lg-4 text-left">{!! trans('messages.specification') !!}</label>
                            <div class="col-lg-8">
                                {!! Form::select('user_type', $user_types, $users->user_type, $attributes = array('class'=>'form-control required',
                                'placeholder' => 'Select One', 'id'=>"user_type")) !!}
                                @if($errors->first('user_type'))
                                    <span class="control-label">
                                <em><i class="fa fa-times-circle-o"></i> {{ $errors->first('user_type','') }}</em>
                            </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group has-feedback {{ $errors->has('user_nid') ? 'has-error' : ''}}">
                            <label  class="col-lg-4 text-left">{!! trans('messages.nid') !!}</label>
                            <div class="col-lg-8">
                                {!! Form::text('user_nid', $value = $users->user_nid, $attributes = array('class'=>'form-control required', 'id'=>"user_nid",
                                'readonly' => "readonly", 'data-rule-maxlength'=>'20')) !!}
                                <span class="glyphicon glyphicon-flag form-control-feedback"></span>
                                @if($errors->first('user_nid'))
                                    <span class="control-label">
                                <em><i class="fa fa-times-circle-o"></i> {{ $errors->first('user_nid','') }}</em>
                            </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group has-feedback {{ $errors->has('user_DOB') ? 'has-error' : ''}}">
                            <label  class="col-lg-4 text-left">{!! trans('messages.dob') !!}</label>
                            <div class="col-lg-8">
                                {!! Form::text('user_DOB', $value = $users->user_DOB, $attributes = array('class'=>'form-control required',
                                'placeholder'=>'Enter your Birth Date','id'=>"user_DOB", 'readonly' => "readonly")) !!}
                                <span class="glyphicon glyphicon-calendar form-control-feedback"></span>
                                @if($errors->first('user_DOB'))
                                    <span class="control-label">
                                <em><i class="fa fa-times-circle-o"></i> {{ $errors->first('user_DOB','') }}</em>
                            </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group has-feedback {{ $errors->has('user_phone') ? 'has-error' : ''}}">
                            <label  class="col-lg-4 text-left">{!! trans('messages.mobile') !!}</label>
                            <div class="col-lg-8">
                                {!! Form::text('user_phone', $value = $users->user_phone, $attributes = array('class'=>'form-control required mobile_number_validation',
                                'placeholder'=>'Enter the Mobile Number','id'=>"user_phone", 'data-rule-maxlength'=>'16')) !!}
                                <span class="text-danger mobile_number_error"></span>
                                <span class="glyphicon glyphicon-phone form-control-feedback"></span>
                                @if($errors->first('user_phone'))
                                    <span  class="control-label">
                                <em><i class="fa fa-times-circle-o"></i> {{ $errors->first('user_phone','') }}</em>
                            </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group has-feedback {{ $errors->has('user_email') ? 'has-error' : ''}}">
                            <label  class="col-lg-4 text-left">{!! trans('messages.email') !!}</label>
                            <div class="col-lg-8">
                                {{ $users->user_email }}
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


                        {{--<div class="form-group has-feedback {{ $errors->has('user_username') ? 'has-error' : ''}}">--}}
                        {{--<label  class="col-lg-4 text-left">{!! trans('messages.user_name') !!}</label>--}}
                        {{--<div class="col-lg-8">--}}
                        {{--{!! Form::text('user_username', $value = $users->user_username, $attributes = array('class'=>'form-control required',--}}
                        {{--'placeholder'=>'Enter your desired User Name','id'=>"user_username")) !!}--}}
                        {{--<span class="glyphicon glyphicon-user form-control-feedback"></span>--}}
                        {{--@if($errors->first('user_username'))--}}
                        {{--<span  class="control-label">--}}
                        {{--<em><i class="fa fa-times-circle-o"></i> {{ $errors->first('user_username','') }}</em>--}}
                        {{--</span>--}}
                        {{--@endif--}}
                        {{--</div>--}}
                        {{--</div>--}}

                        <div class="col-md-12">
                            <div class="col-md-3">
                                <a href="/users/lists">
                                    <button type="button" class="btn btn-md btn-block btn-default">
                                        <i class="fa fa-times"></i>
                                        <b>Close</b></button>
                                </a>
                            </div>
                            <div class="col-md-3">&nbsp;</div>
                            <div class="col-md-3">
                                @if(ACL::getAccsessRight('user','E'))
                                    <button type="submit" class="btn btn-md btn-block btn-primary" id='submit_btn'><b>Save</b></button>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-12 col-md-offset-1">
                                {!! App\Libraries\CommonFunction::showAuditLog($users->updated_at, $users->updated_by) !!}
                            </div>
                        </div>

                    </fieldset>

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <!--</form>-->
                    {!! Form::close() !!}
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </section>
@endsection

@section('footer-script')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function () {
            $("#user_edit_form").validate({
                errorPlacement: function () {
                    return false;
                }
            });
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

        $("#code").blur(function () {
            var code = $(this).val().trim();
            if (code.length > 0 && code.length < 12) {
                $('.code-error').html('');
                $('#submit_btn').attr("disabled", false);
            } else {
                $('.code-error').html('Code number should be at least 1 character to maximum  11 characters!');
                $('#submit_btn').attr("disabled", true);
            }
        });
    </script>
    @endsection <!--- footer-script--->
