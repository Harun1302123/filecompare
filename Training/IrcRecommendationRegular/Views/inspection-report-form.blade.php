@extends('layouts.admin')

@section('style')
    <style>
        .panel-heading {
            padding: 2px 5px;
            overflow: hidden;
        }

        .row > .col-md-5, .row > .col-md-7, .row > .col-md-3, .row > .col-md-9, .row > .col-md-12 > strong:first-child {
            padding-bottom: 5px;
            display: block;
        }

        legend.scheduler-border {
            font-weight: normal !important;
        }

        .table {
            margin: 0;
        }

        .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {
            padding: 5px;
            font-size: 14px;
        }

        .mb5 {
            margin-bottom: 5px;
        }

        .mb0 {
            margin-bottom: 0;
        }
        label {
            font-weight: 100 !important;
        }
    </style>
@endsection

@section('content')
    <div class="col-md-12">
        <div class="box">
            <div class="box-body" id="inputForm">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h5><strong>Inspection Report Submission</strong></h5>
                        </div>
                        @if(in_array(Auth::user()->user_type, ['1x101','2x202', '4x404']))
                            <div class="pull-right">
                                <a href="{{ url('irc-recommendation-regular/report-generate/'.Encryption::encodeId($inspectionInfo->id)) }}" class="btn btn-sm btn-success" title="" target="_blank">Inspection report</a>
                            </div>
                        @endif

                        <div class="clearfix"></div>
                    </div>

                    <div class="panel-body">

                        <div class="row">
                            <div class="col-md-12">
                                <ol class="breadcrumb">
                                    <li><strong>Tracking no. : </strong> {{ $inspectionInfo->tracking_no }}</li>
                                    <li><strong> Date of
                                            Submission: </strong> {{ \App\Libraries\CommonFunction::formateDate($inspectionInfo->submitted_at) }}
                                    </li>
                                    <li><strong>Current Status : </strong> {{ $inspectionInfo->status_name }}</li>
                                    <li><strong>Current Desk
                                            :</strong> {{ $inspectionInfo->desk_id != 0 ? \App\Libraries\CommonFunction::getDeskName($inspectionInfo->desk_id) : 'Applicant' }}
                                    </li>
                                </ol>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <span class="v_label">পরিদর্শনের তারিখ</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-6">
                                            <span>{{(empty($inspectionInfo->inspection_report_date) ? '' : date('d-M-Y h:i A', strtotime($inspectionInfo->inspection_report_date)))}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">১. প্রকল্পের তথ্য</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <span class="v_label">শিল্প প্রকল্পের নাম</span>
                                                <span class="pull-right">&#58;</span>
                                            </div>
                                            <div class="col-md-9">
                                                <span>{{ (!empty($inspectionInfo->company_name) ? $inspectionInfo->company_name : '') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <span class="v_label">অফিসের ঠিকানা</span>
                                                <span class="pull-right">&#58;</span>
                                            </div>
                                            <div class="col-md-9">
                                                <span>{{ (!empty($inspectionInfo->office_address) ? $inspectionInfo->office_address : '') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <span class="v_label">কারখানার ঠিকানা</span>
                                                <span class="pull-right">&#58;</span>
                                            </div>
                                            <div class="col-md-9">
                                                <span>{{ (!empty($inspectionInfo->factory_address) ? $inspectionInfo->factory_address : '') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">২. শিল্প খাতের তথ্য</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <span class="v_label">শিল্প খাত</span>
                                                <span class="pull-right">&#58;</span>
                                            </div>
                                            <div class="col-md-9">
                                                <span>{{ $inspectionInfo->industrial_sector }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">৩. বিনিয়োগের তথ্য</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <span class="v_label">বিনিয়োগের প্রকৃতি</span>
                                                <span class="pull-right">&#58;</span>
                                            </div>
                                            <div class="col-md-9">
                                                <span>{{ (!empty($inspectionInfo->organization_status_name) ? $inspectionInfo->organization_status_name : '') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">৪. উদ্যোক্তার তথ্য</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="col-md-5">
                                                <span class="v_label">উদ্যোক্তার নাম</span>
                                                <span class="pull-right">&#58;</span>
                                            </div>
                                            <div class="col-md-7">
                                                <span>{{ $inspectionInfo->entrepreneur_name }}</span>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="col-md-5">
                                                <span class="v_label">ঠিকানা</span>
                                                <span class="pull-right">&#58;</span>
                                            </div>
                                            <div class="col-md-7">
                                                <span>{{ $inspectionInfo->entrepreneur_address }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">৫. নিবন্ধনকারীর তথ্য</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="col-md-5">
                                                <span class="v_label">কতৃপক্ষের নাম</span>
                                                <span class="pull-right">&#58;</span>
                                            </div>
                                            <div class="col-md-7">
                                                <span>{{ $inspectionInfo->registering_authority_name }}</span>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="col-md-5">
                                                <span class="v_label">স্মারক নং</span>
                                                <span class="pull-right">&#58;</span>
                                            </div>
                                            <div class="col-md-7">
                                                <span>{{ $inspectionInfo->registering_authority_memo_no }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="col-md-5">
                                                <span class="v_label">নিবন্ধন নম্বর</span>
                                                <span class="pull-right">&#58;</span>
                                            </div>
                                            <div class="col-md-7">
                                                <span>{{ (!empty($inspectionInfo->reg_no) ? $inspectionInfo->reg_no : '') }}</span>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="col-md-5">
                                                <span class="v_label">নিবন্ধনের তারিখ</span>
                                                <span class="pull-right">&#58;</span>
                                            </div>
                                            <div class="col-md-7">
                                                <span>{{ (empty($inspectionInfo->date_of_registration) ? '' : date('d-M-Y', strtotime($inspectionInfo->date_of_registration))) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading"><strong>৬. বিবিধ রেজিস্ট্রেশন নং</strong></div>
                            <div class="panel-body">
                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">(ক) ট্রেড লাইসেন্স</legend>
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <div class="col-md-5">
                                                    <span class="v_label">ট্রেড লাইসেন্স নম্বর</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7">
                                                    <span>{{$inspectionInfo->trade_licence_num}}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <div class="col-md-5">
                                                    <span class="v_label">ইস্যুয়িং অথরিটি</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7">
                                                    <span>{{$inspectionInfo->trade_licence_issuing_authority}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <div class="col-md-5">
                                                    <span class="v_label">ইস্যুয়িং ডেট</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7">
                                                    <span>{{(empty($inspectionInfo->trade_licence_issue_date) ? '' : date('d-M-Y', strtotime($inspectionInfo->trade_licence_issue_date)))}}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <div class="col-md-5">
                                                    <span class="v_label">মেয়াদ উত্তীর্ণ সময়কাল</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7">
                                                    {{-- <span>{{(empty($inspectionInfo->trade_licence_validity_period) ? '' : date('d-M-Y', strtotime($inspectionInfo->trade_licence_validity_period)))}}</span> --}}
                                                    <span>{{(empty($inspectionInfo->trade_licence_validity_period) ? '' : $inspectionInfo->trade_licence_validity_period)}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">(খ) টি আই এন নং</legend>
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <div class="col-md-5">
                                                    <span class="v_label">টি আইএন নম্বর</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7">
                                                    <span>{{$inspectionInfo->tin_number}}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <div class="col-md-5">
                                                    <span class="v_label">ইস্যুয়িং অথরিটি</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7">
                                                    <span>{{$inspectionInfo->tin_issuing_authority}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">(গ) ব্যাংক প্রত্যয়ন পত্র</legend>

                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <div class="col-md-5">
                                                    <span class="v_label">ব্যাংকের নাম</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7">
                                                    <span>{{$inspectionInfo->existing_bank_name}}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <div class="col-md-5">
                                                    <span class="v_label">ব্যাংক শাখার নাম</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7">
                                                    <span>{{$inspectionInfo->existing_branch_name}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <div class="col-md-5">
                                                    <span class="v_label">হিসাব নম্বর</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7">
                                                    <span>{{$inspectionInfo->bank_account_number}}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="col-md-5">
                                                    <span class="v_label">একাউন্ট নাম</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7">
                                                    <span>{{$inspectionInfo->bank_account_title}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="scheduler-border {{($inspectionInfo->chnage_bank_info == 'yes') ? '': 'hidden'}}">
                                    <legend class="scheduler-border"> সংশোধীত ব্যাংক প্রত্যয়ন পত্র</legend>

                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <div class="col-md-5">
                                                    <span class="v_label">ব্যাংকের নাম</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7">
                                                    <span>{{$inspectionInfo->proposed_bank_name}}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <div class="col-md-5">
                                                    <span class="v_label">ব্যাংক শাখার নাম</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7">
                                                    <span>{{$inspectionInfo->proposed_branch_name}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <div class="col-md-5">
                                                    <span class="v_label">হিসাব নম্বর</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7">
                                                    <span>{{$inspectionInfo->n_bank_account_number}}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="col-md-5">
                                                    <span class="v_label">একাউন্ট নাম</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7">
                                                    <span>{{$inspectionInfo->n_bank_account_title}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">(ঘ) মেম্বারশিপ অফ চেম্বার / এসোসিয়েশন ইনফরমেশন</legend>
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <div class="col-md-5">
                                                    <span class="v_label">সদস্য নম্বর</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7">
                                                    <span>{{$inspectionInfo->assoc_membership_number}}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <div class="col-md-5">
                                                    <span class="v_label">চেম্বার নাম</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7">
                                                    <span>{{$inspectionInfo->assoc_chamber_name}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <div class="col-md-5">
                                                    <span class="v_label">ইস্যুয়িং ডেট</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7">
                                                    {{( empty($inspectionInfo->assoc_issuing_date) ? '' : date('d-M-Y', strtotime($inspectionInfo->assoc_issuing_date)))}}
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <div class="col-md-5">
                                                    <span class="v_label">মেয়াদ উত্তীর্ণ তারিখ</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7">
                                                    {{( empty($inspectionInfo->assoc_expire_date) ? '' : date('d-M-Y', strtotime($inspectionInfo->assoc_expire_date)))}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">(ঙ) ফায়ার লাইসেন্স নং</legend>
                                    <div class="row">
                                        {{--Already have--}}
                                        @if(!empty($inspectionInfo->fl_number) || !empty($inspectionInfo->fl_expire_date))
                                            <div class="form-group">
                                                <div class="col-md-6">
                                                    <div class="col-md-5">
                                                        <span class="v_label">ফায়ার লাইসেন্স নম্বর</span>
                                                        <span class="pull-right">&#58;</span>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <span>{{$inspectionInfo->fl_number}}</span>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="col-md-5">
                                                        <span class="v_label">মেয়াদ উত্তীর্ণ তারিখ</span>
                                                        <span class="pull-right">&#58;</span>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <span>{{(empty($inspectionInfo->fl_expire_date) ? '' : date('d-M-Y', strtotime($inspectionInfo->fl_expire_date)))}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        {{--Applied for--}}
                                        @if(!empty($inspectionInfo->fl_application_number) || !empty($inspectionInfo->fl_apply_date))
                                            <div class="form-group">
                                                <div class="col-md-6">
                                                    <div class="col-md-5">
                                                        <span class="v_label">আবেদন নম্বর</span>
                                                        <span class="pull-right">&#58;</span>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <span>{{$inspectionInfo->fl_application_number}}</span>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="col-md-5">
                                                        <span class="v_label">আবেদনের তারিখ</span>
                                                        <span class="pull-right">&#58;</span>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <span>{{(empty($inspectionInfo->fl_apply_date) ? '' : date('d-M-Y', strtotime($inspectionInfo->fl_apply_date)))}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <div class="col-md-5">
                                                <span class="v_label">ইস্যুয়িং অথরিটি</span>
                                                <span class="pull-right">&#58;</span>
                                            </div>
                                            <div class="col-md-7">
                                                <span>{{$inspectionInfo->fl_issuing_authority}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">(চ) ইনকর্পোরেশন</legend>
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <div class="col-md-5">
                                                    <span class="v_label">ইনকর্পোরেশন নম্বর</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7">
                                                    <span>{{$inspectionInfo->inc_number}}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <div class="col-md-5">
                                                    <span class="v_label">ইস্যুয়িং অথরিটি</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7">
                                                    <span>{{$inspectionInfo->inc_issuing_authority}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">(ছ) পরিবেশ ছাড়পত্র</legend>
                                    <div class="row">
                                        {{--Already have--}}
                                        @if(!empty($inspectionInfo->el_number) || !empty($inspectionInfo->el_expire_date))
                                            <div class="form-group">
                                                <div class="col-md-6">
                                                    <div class="col-md-5">
                                                        <span class="v_label">পরিবেশ ছাড়পত্র নম্বর</span>
                                                        <span class="pull-right">&#58;</span>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <span>{{ $inspectionInfo->el_number }}</span>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="col-md-5">
                                                        <span class="v_label">মেয়াদ উত্তীর্ণ তারিখ</span>
                                                        <span class="pull-right">&#58;</span>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <span>{{(empty($inspectionInfo->el_expire_date) ? '' : date('d-M-Y', strtotime($inspectionInfo->el_expire_date)))}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        {{--Applied for--}}
                                        @if(!empty($inspectionInfo->el_application_number) || !empty($inspectionInfo->el_apply_date))
                                            <div class="form-group">
                                                <div class="col-md-6">
                                                    <div class="col-md-5">
                                                        <span class="v_label">আবেদন নম্বর</span>
                                                        <span class="pull-right">&#58;</span>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <span>{{ $inspectionInfo->el_application_number }}</span>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="col-md-5">
                                                        <span class="v_label">আবেদনের তারিখ</span>
                                                        <span class="pull-right">&#58;</span>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <span>{{(empty($inspectionInfo->el_apply_date) ? '' : date('d-M-Y', strtotime($inspectionInfo->el_apply_date)))}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-6 form-group">
                                                <div class="col-md-5">
                                                    <span class="v_label">ইস্যুয়িং অথরিটি</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7">
                                                    <span>{{$inspectionInfo->el_issuing_authority}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">৭. প্রকল্পের অবস্থান</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="col-md-5">
                                                <span class="v_label">প্রকল্পের অবস্থান </span>
                                                <span class="pull-right">&#58;</span>
                                            </div>
                                            <div class="col-md-7">
                                                <span>{{ (!empty($inspectionInfo->project_status_name) ? $inspectionInfo->project_status_name : '') }}</span>
                                            </div>
                                        </div>

                                        @if($inspectionInfo->project_status_id == 4)
                                            <div class="col-md-6">
                                                <div class="col-md-5">
                                                    <span class="v_label">অন্যান্য </span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7">
                                                    <span>{{ (!empty($inspectionInfo->other_details) ? $inspectionInfo->other_details : '') }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading"><strong>৮. বিনিয়োজিত মূলধন</strong></div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" cellspacing="0" width="100%">
                                        <tbody id="annual_production_capacity">
                                        <tr>
                                            <td>
                                                <div style="position: relative;">
                                                    <span class="helpTextCom v_label" id="investment_land_label">&nbsp; জমি </span>
                                                </div>
                                            </td>
                                            <td>
                                                <table style="width:100%;">
                                                    <tr>
                                                        <td style="width:75%;">
                                                            {{$inspectionInfo->local_land_ivst}} BDT
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>
                                                <div style="position: relative;">
                                                                <span class="helpTextCom v_label"
                                                                      id="investment_building_label">&nbsp; ভবন</span>
                                                </div>
                                            </td>
                                            <td>
                                                <table style="width:100%;">
                                                    <tr>
                                                        <td style="width:75%;">
                                                            {{$inspectionInfo->local_building_ivst}} BDT
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>
                                                <div style="position: relative;">
                                                                <span class="helpTextCom v_label"
                                                                      id="investment_machinery_equp_label">&nbsp; যন্ত্রপাতি ও সরঞ্জামাদি <small>(মিলিয়ন)</small></span>
                                                </div>
                                            </td>
                                            <td>
                                                <table style="width:100%;">
                                                    <tr>
                                                        <td style="width:75%;">
                                                            {{$inspectionInfo->local_machinery_ivst}} BDT
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>
                                                <div style="position: relative;">
                                                    <span class="helpTextCom v_label" id="investment_others_label">&nbsp; অন্যান্য <small>(মিলিয়ন)</small></span>
                                                </div>
                                            </td>
                                            <td>
                                                <table style="width:100%;">
                                                    <tr>
                                                        <td style="width:75%;">
                                                            {{$inspectionInfo->local_others_ivst}} BDT
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>
                                                <div style="position: relative;">
                                                                <span class="helpTextCom v_label"
                                                                      id="investment_working_capital_label">&nbsp; চলতি মূলধন <small>(মিলিয়ন)</small></span>
                                                </div>
                                            </td>
                                            <td>
                                                <table style="width:100%;">
                                                    <tr>
                                                        <td style="width:75%;">
                                                            {{$inspectionInfo->local_wc_ivst}} BDT
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div style="position: relative;">
                                                                <span class="helpTextCom v_label"
                                                                      id="investment_total_invst_mi_label">&nbsp; মোট মূলধন<small>(মিলিয়ন) (টাকা)</small></span>
                                                </div>
                                            </td>
                                            <td colspan="3">
                                                {{$inspectionInfo->total_fixed_ivst_million}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div style="position: relative;">
                                                                <span class="helpTextCom v_label"
                                                                      id="investment_total_invst_bd_label">&nbsp; মোট মূলধন <small>(টাকা)</small></span>
                                                </div>
                                            </td>
                                            <td colspan="3">
                                                {{$inspectionInfo->total_fixed_ivst}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div style="position: relative;">
                                                                <span class="helpTextCom v_label"
                                                                      id="investment_total_invst_usd_label">&nbsp; ডলার এক্সচেঞ্জ রেট (USD)</span>
                                                </div>
                                            </td>
                                            <td colspan="3">
                                                {{$inspectionInfo->usd_exchange_rate}}
                                                (<span class="help-text">Exchange Rate Ref: <a
                                                            href="https://www.bangladesh-bank.org/econdata/exchangerate.php"
                                                            target="_blank">Bangladesh Bank</a>. Please Enter Today's Exchange Rate</span>)
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div style="position: relative;">
                                                                <span class="helpTextCom v_label"
                                                                      id="investment_total_fee_bd_label">&nbsp; টোটাল ফি <small>(টাকা)</small></span>
                                                </div>
                                            </td>
                                            <td>
                                                {{$inspectionInfo->total_fee}}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">৯. স্থাপিত যন্ত্রপাতির বিবরণ</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <div class="col-md-5">
                                                <span class="v_label">স্থানীয় ভাবে সংগৃহীত (মিলিয়ন) (টাকা) </span>
                                                <span class="pull-right">&#58;</span>
                                            </div>
                                            <div class="col-md-7">
                                                <span>{{ $inspectionInfo->em_local_total_taka_mil }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <div class="col-md-5">
                                                <span class="v_label">এলসিকৃত (মিলিয়ন) (টাকা) </span>
                                                <span class="pull-right">&#58;</span>
                                            </div>
                                            <div class="col-md-7">
                                                <span>{{ $inspectionInfo->em_lc_total_taka_mil }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading"><strong>১০. জনবল</strong></div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" cellspacing="0" width="100%">
                                        <tbody id="manpower">
                                        <tr>
                                            <th colspan="3">বাংলাদেশী</th>
                                            <th colspan="3">বিদেশী</th>
                                            <th colspan="1">সর্বমোট</th>
                                            <th colspan="2">অনুপাত</th>
                                        </tr>
                                        <tr>
                                            <th>কার্যনির্বাহী</th>
                                            <th>সাপোর্টিং</th>
                                            <th>মোট (a)</th>
                                            <th>কার্যনির্বাহী</th>
                                            <th>সাপোর্টিং</th>
                                            <th>মোট (b)</th>
                                            <th> (a+b)</th>
                                            <th>স্থানীয়</th>
                                            <th>বিদেশী</th>
                                        </tr>
                                        <tr>
                                            <td>{{$inspectionInfo->local_male}}</td>
                                            <td>{{$inspectionInfo->local_female}}</td>
                                            <td>{{$inspectionInfo->local_total}}</td>
                                            <td>{{$inspectionInfo->foreign_male}}</td>
                                            <td>{{$inspectionInfo->foreign_female}}</td>
                                            <td>{{$inspectionInfo->foreign_total}}</td>
                                            <td>{{$inspectionInfo->manpower_total}}</td>
                                            <td>{{$inspectionInfo->manpower_local_ratio}}</td>
                                            <td>{{$inspectionInfo->manpower_foreign_ratio}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">১১. নিবন্ধপত্র / নিবন্ধনপত্রে সংশোধী অনুযায়ী বার্ষিক উৎপাদন ক্ষমতা</div>
                            <div class="panel-body">
                                @if($inspectionInfo->irc_purpose_id != 2 && count($inspectionAnnualProductionCapacity) > 0)
                                    <table class="table table-bordered dt-responsive" cellspacing="0" width="100%">
                                        <thead class="alert alert-info">
                                        <tr>
                                            <th class="text-center  ">ক্রমিক নং</th>
                                            <th class="text-center">পন্য/ সেবার নাম</th>
                                            <th colspan="2" class="text-center">নির্ধারিত বার্ষিক উৎপাদন ক্ষমতা</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $count = 1;?>
                                        @foreach($inspectionAnnualProductionCapacity as $apc)
                                            <tr>
                                                <td><?php echo $count++ ?></td>
                                                <td>
                                                    {{ (!empty($apc->product_name) ? $apc->product_name : '') }}
                                                </td>
                                                <td>
                                                    {{ (!empty($apc->fixed_production) ? $apc->fixed_production : '') }}
                                                </td>
                                                <td>
                                                    {{ (!empty($apc->unit_name) ? $apc->unit_name : '') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @endif

                                @if($inspectionInfo->irc_purpose_id != 1 && count($inspectionAnnualProductionSpareParts) > 0)
                                    <table class="table table-bordered dt-responsive" cellspacing="0" width="100%">
                                        <thead class="alert alert-info">
                                        <tr>
                                            <th class="text-center">ক্রমিক নং</th>
                                            <th class="text-center">পন্য/ সেবার নাম</th>
                                            <th colspan="2" class="text-center">নির্ধারিত বার্ষিক উৎপাদন ক্ষমতা</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $count = 1;?>
                                        @foreach($inspectionAnnualProductionSpareParts as $apsp)
                                            <tr>
                                                <td><?php echo $count++ ?></td>
                                                <td>
                                                    {{ (!empty($apsp->product_name) ? $apsp->product_name : '') }}
                                                </td>
                                                <td>
                                                    {{ (!empty($apsp->fixed_production) ? $apsp->fixed_production : '') }}
                                                </td>
                                                <td>
                                                    {{ (!empty($apsp->unit_name) ? $apsp->unit_name : '') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading"><strong>১২. এডহক ভিত্তিক কাঁচামালের ষান্মাসিক আমদানিস্বত্ব/
                                    চাহিদা</strong></div>
                            <div class="panel-body">

                                @if($inspectionInfo->irc_purpose_id != 2 && count($inspectionAnnualProductionCapacity) > 0)
                                    <table class="table table-bordered dt-responsive" cellspacing="0" width="100%">
                                        <thead class="alert alert-info">
                                        <tr>
                                            <td colspan="6">উদ্যোক্তা কর্তৃক দাখিল কৃত তথ্য অনুযায়ী:</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $count = 1;?>
                                        @foreach($inspectionAnnualProductionCapacity as $apc)
                                            <tr>
                                                <td><?php echo $count++ ?></td>
                                                <td width="15%">
                                                    <table>
                                                        <tr>
                                                            <td>প্রতি</td>
                                                            <td>
                                                                {{ (!empty($apc->unit_of_product) ? $apc->unit_of_product : '') }}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                <td width="15%">
                                                    {{ (!empty($apc->unit_name) ? $apc->unit_name : '') }}
                                                </td>
                                                <td width="15%">
                                                    {{ (!empty($apc->product_name) ? $apc->product_name : '') }}
                                                </td>
                                                <td width="25%"> উৎপাদনের জন্য কাঁচামাল প্রয়োজন</td>
                                                <td width="30%">
                                                    <table>
                                                        <tr>
                                                            <td>
                                                                {{ (!empty($apc->raw_material_total_price) ? $apc->raw_material_total_price : '') }}
                                                            </td>
                                                            <td>টাকার</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <br>
                                    <span>
                                        <strong>কাঁচামালের ষান্মাসিক আমদানিস্বত্ব/ চাহিদা</strong><br>
                                        উদ্যোক্তা কর্তৃক কারখানায় স্থাপিত যন্ত্রপাতি (এলসিকৃত/ স্থানীয়ভাবে সংগ্রহীত),
                                        নিয়োজিত জনবল, অবকাঠামোগত সুবিধা বিবেচনাপূর্বক প্রদত্ত তথ্য এবং প্রতিষ্ঠান
                                        কর্তৃপক্ষের সাথে আলোচনার ভিত্তিতে কারখানাটির বার্ষিক উৎপাদন ক্ষমতা প্রাথমিকভাবে
                                        নিম্নরূপ নির্ধারণ করা যেতে পারে।
                                    </span>
                                    <table class="table table-bordered dt-responsive" cellspacing="0" width="100%">
                                        <thead class="alert alert-info">
                                        <tr>
                                            <td>ক্রমিক নং</td>
                                            <td>পন্য/ সেবার নাম</td>
                                            <td>নির্ধারিত বার্ষিক উৎপাদন ক্ষমতা</td>
                                            <td>ষান্মাসিক উৎপাদন ক্ষমতা</td>
                                            <td>ষান্মাসিক আমদানিস্বত্ব (টাকা)</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $count = 1;?>
                                        @foreach($inspectionAnnualProductionCapacity as $apc)
                                            <tr>
                                                <td><?php echo $count++ ?></td>
                                                <td>{{ $apc->product_name }}</td>
                                                <td>{{ $apc->fixed_production }}</td>
                                                <td>{{ $apc->half_yearly_production }}</td>
                                                <td>{{ $apc->half_yearly_import }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td colspan="4"><span class="pull-right">মোট টাকা</span></td>
                                            <td>
                                                <span>{{ $inspectionInfo->apc_half_yearly_import_total }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"><span class="pull-right">অন্যান্য টাকার পরিমান</span></td>
                                            <td>
                                                <span>{{  
                                                (!empty($inspectionInfo->apc_half_yearly_import_other) ? $inspectionInfo->apc_half_yearly_import_other : 0)}}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"><span class="pull-right">সর্বমোট টাকা</span></td>
                                            <td>
                                                <span>{{ floatval(isset($inspectionInfo->apc_half_yearly_import_total) ? $inspectionInfo->apc_half_yearly_import_total : 0) + floatval(isset($inspectionInfo->apc_half_yearly_import_other) ? $inspectionInfo->apc_half_yearly_import_other : 0) }}</span>
                                            </td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                    <br>
                                    
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                {!! Form::label('apc_half_yearly_import_total_in_word','কথায়',['class'=>'text-left col-md-1 v_label']) !!}
                                                <div class="col-md-11">
                                                    <span>{{ $inspectionInfo->apc_half_yearly_import_total_in_word }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <br>
                            </div>
                        </div>

                        @if($inspectionInfo->irc_purpose_id != 1)
                            <div class="panel panel-info">
                                <div class="panel-heading"><strong>১৩. এডহক ভিত্তিক খুচরা যন্ত্রাংশের ষান্মাসিক আমদানিস্বত্ব/ চাহিদা</strong></div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                {!! Form::label('em_lc_total_taka_mil','প্রতিষ্ঠান কর্তৃক এলসিকৃত মূলধনী যন্ত্রপপাতির মোট মূল্যের',['class'=>'text-left col-md-5 v_label']) !!}
                                                <div class="col-md-7">
                                                    <span>{{ $inspectionInfo->em_lc_total_taka_mil ? $inspectionInfo->em_lc_total_taka_mil : '0.00' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            {!! Form::label('em_lc_total_five_percent_in_word','কথায়',['class'=>'col-md-2 text-left v_label']) !!}
                                            <div class="col-md-10">
                                                <span>{{ $inspectionInfo->em_lc_total_five_percent_in_word ? $inspectionInfo->em_lc_total_five_percent_in_word : '0.00' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            খুচরা  যন্ত্রাংশের জন্য ষান্মাসিক আমদানিস্বত্ব নির্ধারন করা যেতে পারে।
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="panel panel-info">
                            <div class="panel-heading"><strong>মন্তব্য</strong></div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-md-12 {{$errors->has('remarks') ? 'has-error': ''}}">
                                            <span>{{$inspectionInfo->remarks}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pull-right" style="padding-left: 1em;">
                            <button type="button" class="btn btn-danger" onclick="javascript:window.open('','_self').close();">Close</button>
                        </div>

                    </div> {{--end pannel body--}}

                </div>
                {{--End application form with wizard--}}
            </div>
        </div>
    </div>

{{--    <script>--}}

{{--        function openModal(btn) {--}}
{{--            var this_action = btn.getAttribute('data-action');--}}

{{--            if(this_action != ''){--}}
{{--                $('#IRCModal .load_modal').html('');--}}
{{--                $.get(this_action, function(data, success) {--}}
{{--                    if(success === 'success'){--}}
{{--                        $('#IRCModal .load_modal').html(data);--}}
{{--                    }else{--}}
{{--                        $('#IRCModal .load_modal').html('Unknown Error!');--}}
{{--                    }--}}
{{--                    $('#IRCModal').modal('show', {backdrop: 'static'});--}}
{{--                });--}}
{{--            }--}}
{{--        }--}}
{{--    </script>--}}
@endsection