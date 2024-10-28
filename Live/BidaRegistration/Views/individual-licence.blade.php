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

                @if(empty($delegated_desk))
                    <div class="modal fade" id="ProjectModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content" id="frmAddProject"></div>
                        </div>
                    </div>
                @endif
                <div class="col-lg-12">
                    <div class="panel panel-info" style="">
                        <div class="panel-heading">
                            <div class="pull-left">
                                <h5><i class="fa fa-list"></i>  <b>Individual Licence List</b></h5>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                        <div class="panel-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 right-buffer">
                                        <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn btn-primary txt-style">Licence  Entities</div>
                                    </div>
                                    <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6  left-buffer">
                                        <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn btn-primary txt-style">Name of Licence</div>
                                    </div>
                                </div>
                                <div class="row top-buffer">
                                    <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 right-buffer">
                                        <a href="http://www.roc.gov.bd/" target="_blank" class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn btn-green txt-style">
                                            <div class="licence_logo">
                                                <img class="img pull-left" src="{{ asset('assets/images/u34.png') }}">
                                            </div>
                                            <p class="pull-left licence_entity">Registrar of Joint Stock Companies And Firms (RJSC)</p>
                                        </a>
                                    </div>
                                    <div class="col-md-5 col-lg-5 col-sm-5 col-xs-5  left-buffer right-buffer">
                                        <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn-green txt-style ">
                                            <p class="pull-left licence_name ">Name Clearance</p>
                                            @if($licenseApplications->nc_application != null)
                                            <a type="button" class="pull-right btn btn-default btn-success btn-sm">Applied</a>
                                            @endif
                                            <a style="margin: 0px 2px" type="button" href="http://www.roc.gov.bd/site/page/3de99758-e1a5-4ba0-aa0d-c0077c1d98f6/%E0%A6%A8%E0%A6%BE%E0%A6%AE%E0%A7%87%E0%A6%B0-%E0%A6%9B%E0%A6%BE%E0%A7%9C%E0%A6%AA%E0%A6%A4%E0%A7%8D%E0%A6%B0-%E0%A6%AA%E0%A7%8D%E0%A6%B0%E0%A6%A6%E0%A6%BE%E0%A6%A8" target="_blank" class="pull-right btn btn-default btn-primary btn-sm">Service Details</a>
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-lg-1 col-sm-1 col-xs-1  left-buffer">
                                        @if($licenseApplications->nc_application != null)
                                            <?php
                                            $explodeCr = explode("@",$licenseApplications->nc_application);
                                            ?>
                                            <a class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn btn-warning licence_apply txt-style" href="{{url('process/licence-applications/name-clearance/view/'.\App\Libraries\Encryption::encodeId($explodeCr[1]).'/'.\App\Libraries\Encryption::encodeId($explodeCr[2]))}}" role="button">Open</a>
                                        @else
                                            <a class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn btn-success licence_apply txt-style" href="{{url('licence-applications/name-clearance/add')}}" role="button">Apply</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="row top-buffer">
                                    <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 right-buffer">
                                        <a href="https://www.sonalibank.com.bd/" target="_blank" class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn btn-green txt-style">
                                            <div class="licence_logo">
                                                <img class="img pull-left" src="{{ asset('assets/images/u39.png') }}">
                                            </div>
                                            <p class="pull-left licence_entity">Sonali Bank</p>
                                        </a>
                                    </div>
                                    <div class="col-md-5 col-lg-5 col-sm-5 col-xs-5  left-buffer right-buffer">
                                        <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn-green txt-style">
                                            <p class="pull-left licence_name">Bank Account Open</p>
                                            @if($licenseApplications->ba_application != null)
                                                <a type="button" class="pull-right btn btn-default btn-success btn-sm">Applied</a>
                                            @endif
                                            <button style="margin: 0px 2px" type="button" class="pull-right btn btn-default btn-primary btn-sm">Service Details</button>
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-lg-1 col-sm-1 col-xs-1  left-buffer">
                                        @if($licenseApplications->ba_application != null)
                                            <?php
                                            $explodeCr = explode("@",$licenseApplications->ba_application);
                                            ?>
                                                <a class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn btn-warning licence_apply txt-style" href="{{url('process/licence-applications/bank-account/view/'.\App\Libraries\Encryption::encodeId($explodeCr[1]).'/'.\App\Libraries\Encryption::encodeId($explodeCr[2]))}}" role="button">Open</a>
                                        @else
                                            <a class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn btn-success licence_apply txt-style" href="{{url('/licence-applications/bank-account/add')}}" role="button">Apply</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="row top-buffer">
                                    <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 right-buffer">
                                        <a href="http://www.roc.gov.bd/" target="_blank" class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn btn-green txt-style">
                                            <div class="licence_logo">
                                                <img class="img pull-left" src="{{ asset('assets/images/u34.png') }}">
                                            </div>
                                            <p class="pull-left licence_entity">Registrar of Joint Stock Companies And Firms (RJSC)</p>
                                        </a>
                                    </div>
                                    <div class="col-md-5 col-lg-5 col-sm-5 col-xs-5  left-buffer right-buffer">
                                        <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn-green txt-style">
                                            <p class="pull-left licence_name">Company Registration</p>

                                            @if($licenseApplications->cr_application != null)
                                                <a type="button" class="pull-right btn btn-default btn-success btn-sm">Applied</a>
                                            @endif
                                            <a  style="margin: 0px 2px" type="button" class="pull-right btn btn-default btn-primary btn-sm" href="http://www.roc.gov.bd/site/page/855dc577-3035-4ca4-b376-49c517099a3e/Entity-Registration" target="_blank">Service Details</a>
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-lg-1 col-sm-1 col-xs-1  left-buffer">
                                        @if($licenseApplications->cr_application != null)
                                            <?php
                                            $explodeCr = explode("@",$licenseApplications->cr_application);
                                            ?>
                                            <a class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn btn-warning licence_apply txt-style" href="{{url('process/licence-applications/company-registration/view/'.\App\Libraries\Encryption::encodeId($explodeCr[1]).'/'.\App\Libraries\Encryption::encodeId($explodeCr[2]))}}" role="button">Open</a>
                                        @else
                                            <a class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn btn-success licence_apply txt-style" href="{{url ('licence-applications/company-registration/add')}}">Apply</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="row top-buffer">
                                    <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 right-buffer">
                                        <a href="http://nbr.gov.bd/" target="_blank" class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn btn-green txt-style">
                                            <div class="licence_logo">
                                                <img class="img pull-left" src="{{ asset('assets/images/u34.png') }}">
                                            </div>
                                            <p class="pull-left licence_entity">National Board of Revenue</p>
                                        </a>
                                    </div>
                                    <div class="col-md-5 col-lg-5 col-sm-5 col-xs-5  left-buffer right-buffer">
                                        <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn-green txt-style">
                                            <p class="pull-left licence_name">e-TIN Registration</p>
                                            @if($licenseApplications->etin_application != null)
                                                <a type="button" class="pull-right btn btn-default btn-success btn-sm">Applied</a>
                                            @endif
                                            <a style="margin: 0px 2px" type="button" class="pull-right btn btn-default btn-primary btn-sm" href="http://nbr.gov.bd/" target="_blank">Service Details</a>
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-lg-1 col-sm-1 col-xs-1  left-buffer">
                                        @if($licenseApplications->etin_application != null)
                                            <?php
                                            $explodeCr = explode("@",$licenseApplications->etin_application);
                                            ?>
                                            <a class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn btn-warning licence_apply txt-style" href="{{url('process/licence-applications/e-tin/view/'.\App\Libraries\Encryption::encodeId($explodeCr[1]).'/'.\App\Libraries\Encryption::encodeId($explodeCr[2]))}}" role="button">Open</a>
                                        @else
                                            <a class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn btn-success licence_apply txt-style" href="{{url('licence-applications/e-tin/add')}}" role="button">Apply</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="row top-buffer">
                                    <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 right-buffer">
                                        <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn btn-green txt-style">
                                            <div class="licence_logo">
                                                <img class="img pull-left" src="{{ asset('assets/images/u34.png') }}">
                                            </div>
                                            <p class="pull-left licence_entity">City Corporation (DNCC, DSCC, CCC)</p>
                                        </div>
                                    </div>
                                    <div class="col-md-5 col-lg-5 col-sm-5 col-xs-5  left-buffer right-buffer">
                                        <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn-green txt-style">
                                            <p class="pull-left licence_name">Trade License</p>
                                            @if($licenseApplications->tl_application != null)
                                                <a type="button" class="pull-right btn btn-default btn-success btn-sm">Applied</a>
                                            @endif
                                            <a style="margin: 0px 2px" type="button" class="pull-right btn btn-default btn-primary btn-sm" href="http://www.dhakasouthcity.gov.bd/" target="_blank" >Service Details</a>
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-lg-1 col-sm-1 col-xs-1  left-buffer">
                                        @if($licenseApplications->tl_application != null)
                                            <?php
                                            $explodeCr = explode("@",$licenseApplications->tl_application);
                                            ?>
                                            <a class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn btn-warning licence_apply txt-style" href="{{url('process/licence-applications/trade-licence/view/'.\App\Libraries\Encryption::encodeId($explodeCr[1]).'/'.\App\Libraries\Encryption::encodeId($explodeCr[2]))}}" role="button">Open</a>
                                        @else
                                            <a class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn btn-success licence_apply txt-style" href="{{url ('licence-applications/trade-licence/add')}}" >Apply</a>
                                        @endif
                                    </div>
                                </div>
                                {{--<div class="row top-buffer">--}}
                                {{--<div class="col-md-5 col-lg-5 col-sm-5 col-xs-5  right-buffer">--}}
                                {{--<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn btn-green txt-style">--}}
                                {{--<div class="licence_logo">--}}
                                {{--<img class="img pull-left" src="{{ asset('assets/images/u34.png') }}">--}}
                                {{--</div>--}}
                                {{--<p class="pull-left licence_entity">Bangladesh Investment Development Authority</p>--}}
                                {{--</div>--}}
                                {{--</div>--}}
                                {{--<div class="col-md-5 col-lg-5 col-sm-5 col-xs-5  left-buffer right-buffer">--}}
                                {{--<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn btn-green txt-style">--}}
                                {{--<p class="pull-left licence_name">Registration</p>--}}
                                {{--</div>--}}
                                {{--</div>--}}
                                {{--<div class="col-md-2 col-lg-2 col-sm-2 col-xs-2  left-buffer">--}}
                                {{--<a class="col-md-12 col-lg-12 col-sm-12 col-xs-12 btn btn-success txt-style" href="{{ url ('/licence-application/list/'.\App\Libraries\Encryption::encodeId(102)) }}" role="button">Apply</a>--}}
                                {{--</div>--}}
                                {{--</div>--}}

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