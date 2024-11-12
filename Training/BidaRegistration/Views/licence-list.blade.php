@extends('layouts.admin')
@section('content')
    <?php
    $moduleName = Request::segment(1);
    $user_type = CommonFunction::getUserType();
    $accessMode = "V";
    if (!ACL::isAllowed($accessMode, 'V'))
        die('no access right!');

    ?>
    <section class="content">
        <div class="box">
            <div class="box-body">
                <div class="col-lg-12">
                    {!! Session::has('success') ? '<div class="alert alert-success alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("success") .'</div>' : '' !!}
                    {!! Session::has('error') ? '<div class="alert alert-danger alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("error") .'</div>' : '' !!}

                </div>
                <div class="col-lg-12">
                    <div class="panel panel-info" style="">
                        <div class="panel-heading">
                            <div class="pull-left">
                                <h5><i class="fa fa-list"></i>  <b>Licence List</b></h5>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                        <div class="panel-body">
                            <div class="col-md-6 text-center">
                                <a class="btn btn-primary" href="javascript:void(0);" data-toggle="modal" data-target="#constructionAlert" role="button">Individual Licence Application</a>
                            </div>
                            <div class="col-md-6 text-center">
                                <a class="btn btn-primary" href="{{URL::to('licence-application/individual-licence')}}" role="button">Single Licence&nbsp; Application</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="constructionAlert" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title"><img src="{{URL::to('/assets/images/warning.png')}}" alt="warning"/> Under Construction!!</h4>
                </div>
                <div class="modal-body">
                    <p><img src="{{URL::to('/assets/images/under-constructon.png')}}" style="width:60px;" alt="warning"/> <b>This is under construction.</b></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('footer-script')
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>

    @include('partials.datatable-scripts')
    <script language="javascript">

    </script>
    <style>
        *{
            font-weight: normal;
        }
        .unreadMessage td{
            font-weight: bold;
        }
    </style>
    @yield('footer-script2')
@endsection