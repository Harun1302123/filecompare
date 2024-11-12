@extends("layouts.front")

@section("content")

    <div class="container" style="margin-top:30px;">

        <div class="row" style="background: snow; opacity:0.88; border-radius:8px;">
            <!--        <div class="col-md-10 col-md-offset-1">
            <div class="col-md-2 text-left">
                <img class="img-thumbnail img-responsive" src="{{ asset('front-end/images/logo.gif') }}" width="90px;">
            </div>
            <div class="col-md-3">
                <h3><strong>স্বরাষ্ট্র মন্ত্রণালয়</strong></h3>
                <h5><strong>গণপ্রজাতন্ত্রী বাংলাদেশ সরকার</strong></h5>
            </div>
        </div> -->

            <div class="col-md-10 col-md-offset-1" >

                @if(Session::has('success'))
                    <div class="alert alert-success">
                        {{ Session::get('success') }}
                    </div>
                @endif
                @if(Session::has('error'))
                    <div class="alert alert-warning">
                        {{ Session::get('error') }}
                    </div>
                @endif
                <hr/>


                <div class="col-md-6 col-sm-6 col-md-offset-3">
                    <br/>
                    {!! Form::open(array('url' => 'users/reSendEmailConfirm','method' => 'patch', 'class' => 'form-horizontal')) !!}
                    <fieldset>

                        <div class="form-group has-feedback {{ $errors->has('user_email') ? 'has-error' : ''}}">
                            <label  class="col-lg-4 text-left">Email Address </label>
                            <div class="col-lg-8">
                                {!! Form::text('user_email', $value = null, $attributes = array('class'=>'form-control required','placeholder'=>'Enter your Email Address','id'=>"user_email")) !!}
                                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                @if($errors->first('user_email'))
                                    <span  class="control-label">
                                <em><i class="fa fa-times-circle-o"></i> {{ $errors->first('user_email','') }}</em>
                            </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-12 col-sm-12"><br/></div>

                        <div class="form-group">
                            <div class="col-lg-10">
                                <button type="submit" class="btn btn-primary btn-block"><b>Submit</b></button>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-lg-5 text-left">
                                <b>{!! link_to('signup', 'New User? Sign Up!', array("class" => " ")) !!}</b>
                            </div>
                            <div class="col-lg-5 text-right">
                                <b>{!! link_to('users/login', 'Back to Login', array("class" => " ")) !!}</b>
                            </div>
                        </div>

                    </fieldset>
                    {!! Form::close() !!}

                    <br/><br/>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
@endsection