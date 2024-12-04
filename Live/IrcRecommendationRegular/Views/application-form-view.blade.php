<?php
$accessMode = ACL::getAccsessRight('IRCRecommendationRegular');
if (!ACL::isAllowed($accessMode, '-V-')) {
    die('You have no access right! Please contact with system admin if you have any query.');
}
?>
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
    }

    .mb5 {
        margin-bottom: 5px;
    }

    .mb0 {
        margin-bottom: 0;
    }


</style>

<!-- Modal -->
<div class="modal fade" id="IRCModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content load_modal">
        </div>
    </div>
</div>

<section class="content" id="applicationForm">

    @if(in_array($appInfo->status_id,[5,6]))
        @include('ProcessPath::remarks-modal')
    @endif

    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="pull-left">
                    <strong style="line-height: 30px;">
                        Application for Import Registration Certificate (IRC) recommendations Regular
                    </strong>
                </div>
                <div class="pull-right" data-html2canvas-ignore="true">
                    @if(count($inspectionInfo) > 0)
                        @if((Auth::user()->user_type == '5x505' && in_array($appInfo->status_id,[25])) || in_array(Auth::user()->user_type, ['1x101','2x202', '4x404']))
                            @if($appInfo->irc_purpose_id != 2)
                                <a href="{{ url('irc-recommendation-regular/production-capacity-pdf/'.Encryption::encodeId($appInfo->id)) }}" class="btn btn-sm btn-primary mb5" title="" target="_blank">Production Capacity</a>
                            @endif
                            @if($appInfo->irc_purpose_id != 1)
                                <a href="{{ url('irc-recommendation-regular/existing-machines-pdf/'.Encryption::encodeId($appInfo->id)) }}" class="btn btn-sm btn-primary mb5" title="" target="_blank">Existing Machinery</a>
                            @endif
                            @if(!empty($last_inspection_id) && $appInfo->irc_purpose_id != 2)
                                <a href="{{ url('irc-recommendation-regular/entitlement-paper-pdf/'.Encryption::encodeId($last_inspection_id)) }}" class="btn btn-sm btn-primary mb5" title="" target="_blank">Entitlement Paper</a>
                            @endif
                        @endif
                    @endif

                    @if(count($inspectionInfo) > 0 && (in_array(Auth::user()->user_type, ['1x101','2x202', '4x404'])))
                        <a class="btn btn-sm btn-warning mb5" data-toggle="collapse" href="#inspectionInfo" role="button"
                           aria-expanded="false" aria-controls="collapseInspection">
                            <i class="far fa-list-alt"></i>
                            Inspection Details
                        </a>
                    @endif

                    @if (isset($appInfo) && in_array(Auth::user()->user_type, ['1x101','2x202', '4x404']))
                        <a class="btn btn-sm btn-primary mb5" data-toggle="collapse" href="#previousApplications" role="button"  aria-expanded="false" aria-controls="collapseExample">
                            Previous Applications
                        </a>
                    @endif

                    @if (isset($appInfo) && $appInfo->status_id == 25 && isset($appInfo->certificate_link) && in_array(Auth::user()->user_type, ['1x101','2x202', '4x404', '5x505']))
                        <a href="{{ url($appInfo->certificate_link) }}" class="btn show-in-view btn-sm btn-info mb5" title="Download Approval Copy" target="_blank">
                            <i class="fa  fa-file-pdf"></i>
                            Download Approval Copy
                        </a>
                    @endif

                    <a class="btn btn-sm btn-success mb5" data-toggle="collapse" href="#paymentInfo" role="button"
                       aria-expanded="false" aria-controls="collapseExample">
                        <i class="far fa-money-bill-alt"></i>
                        Payment Info.
                    </a>

                    @if(!in_array($appInfo->status_id,[-1,5,6]))
                        <a href="javascript:void(0)" class="btn btn-danger btn-sm mb5" title="Download Approval Copy" id="html2pdf">
                            <i class="fa fa-download"></i> Application Download as PDF
                        </a>
                    @endif

                    @if(in_array($appInfo->status_id,[5,6]))
                        <a data-toggle="modal" data-target="#remarksModal">
                            {!! Form::button('<i class="fa fa-eye"></i> Reason of '.$appInfo->status_name.'', array('type' => 'button', 'class' => 'btn btn-sm btn-danger')) !!}
                        </a>
                    @endif
                </div>

            </div>
            <div class="panel-body">

                 {{--Prevous Applications--}}
                 @include('IrcRecommendationRegular::prevous-applications')

                <ol class="breadcrumb">
                    <li><strong>Tracking no. : </strong>{{ $appInfo->tracking_no  }}</li>
                    <li><strong> Date of Submission: </strong> {{ \App\Libraries\CommonFunction::formateDate($appInfo->submitted_at) }} </li>
                    <li><strong>Current Status : </strong> {{ $appInfo->status_name }}</li>
                    <li><strong>Current Desk :</strong> 
                        {{ $appInfo->desk_id != 0 ? \App\Libraries\CommonFunction::getDeskName($appInfo->desk_id) : 'Applicant' }}
                    </li>
                </ol>

                {{--Inspection Information--}}
                @include('IrcRecommendationRegular::inspection-list')

                {{--Inspection Officer Information--}}
                @if($appInfo->desk_id == 21 && Auth::user()->user_type == '5x505')

                    <?php $io_officer = App\Modules\Users\Models\Users::where('id', $appInfo->user_id)->first(['user_full_name', 'designation', 'user_phone', 'user_email']) ?>
                    <div class="panel panel-info">
                        <div class="panel-heading"><strong>Inspection Officer Information</strong></div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Name</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            {{ (!empty($io_officer->user_full_name)) ? $io_officer->user_full_name :''  }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Designation</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            {{ (!empty($io_officer->designation)) ? $io_officer->designation :''  }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Contact No.</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            {{ (!empty($io_officer->user_phone)) ? $io_officer->user_phone :''  }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Email</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            {{ (!empty($io_officer->user_email)) ? $io_officer->user_email:''  }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                @endif

                {{--Payment information--}}
                @include('ProcessPath::payment-information')

                <!-- Inspection Submission Deadline Date -->
                @if((in_array($appInfo->status_id, [40, 41, 42, 5, 2]) && Auth::user()->user_type != '5x505'))
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border">Inspection Submission Deadline Date</legend>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Inspection Submission Deadline</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($appInfo->io_submission_deadline) ? \App\Libraries\CommonFunction::changeDateFormat($appInfo->io_submission_deadline) : '') }}
                                </div>

                            </div>
                        </div>
                    </fieldset>
                @endif

                {{-- Basic Information--}}
                <div class="panel panel-info">
                    <div class="panel-heading"><strong>Basic Information</strong></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">Purpose for IRC Recommendation:</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($appInfo->purpose_name)) ? $appInfo->purpose_name :''  }}
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">Purpose for Regular IRC Recommendation:</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($appInfo->regular_purpose_name)) ? $appInfo->regular_purpose_name :''  }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">
                                            Did you received BIDA Registration through online OSS?
                                        </span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->last_br)) ? ucfirst($appInfo->last_br) : ''  }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                @if($appInfo->last_br == 'yes')
                                    <div class="row">
                                        <div class="col-md-5 col-xs-6">
                                        <span class="v_label">
                                            Please give your approved Registration Tracking ID.
                                        </span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <a href="{{ $data['br_ref_app_url'] }}" target="_blank">
                                                <span class="label label-success label_tracking_no">{{ (empty($appInfo->br_ref_app_tracking_no) ? '' : $appInfo->br_ref_app_tracking_no) }}</span>
                                            </a>
                                            {!! CommonFunction::getCertificateByTrackingNo($appInfo->br_ref_app_tracking_no) !!}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Approved Date</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            {{ !empty($appInfo->br_ref_app_approve_date) ? date("d-M-Y", strtotime($appInfo->br_ref_app_approve_date)) : ''  }}
                                        </div>
                                    </div>
                                @endif

                                @if($appInfo->last_br == 'no')
                                    <div class="row">
                                        <div class="col-md-5 col-xs-6">
                                        <span class="v_label">
                                            Please give your manually approved BIDA Registration No.
                                        </span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            {{ (!empty($appInfo->br_manually_approved_no)) ? $appInfo->br_manually_approved_no : ''  }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Approved Date</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            {{ !empty($appInfo->br_manually_approved_date) ? date("d-M-Y", strtotime($appInfo->br_manually_approved_date)) : ''  }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

                <div class="panel panel-info">
                    <div class="panel-heading"><strong>IRC Information</strong></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">
                                            Did you receive BIDA IRC recommendation through OSS?
                                        </span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->last_irc_2nd_adhoc)) ? ucfirst($appInfo->last_irc_2nd_adhoc) : ''  }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                @if($appInfo->last_irc_2nd_adhoc == 'yes')
                                    <div class="row">
                                        <div class="col-md-5 col-xs-6">
                                        <span class="v_label">
                                            Please give your latest IRC recommendation Tracking ID 1st/2nd/3rd.
                                        </span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <a href="{{ $data['irc_ref_app_url'] }}" target="_blank">
                                                <span class="label label-success label_tracking_no">{{ (empty($appInfo->irc_ref_app_tracking_no) ? '' : $appInfo->irc_ref_app_tracking_no) }}</span>
                                            </a>

                                            {!! CommonFunction::getCertificateByTrackingNo($appInfo->irc_ref_app_tracking_no) !!}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Approved Date</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            {{ !empty($appInfo->irc_ref_app_approve_date) ? date("d-M-Y", strtotime($appInfo->irc_ref_app_approve_date)) : ''  }}
                                        </div>
                                    </div>
                                @endif

                                @if($appInfo->last_irc_2nd_adhoc == 'no')
                                    <div class="row">
                                        <div class="col-md-5 col-xs-6">
                                        <span class="v_label">
                                            Please give your memo number of latest IRC Recommendation 1st/2nd/3rd
                                        </span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            {{ (!empty($appInfo->irc_manually_approved_no)) ? $appInfo->irc_manually_approved_no : ''  }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Approved Date</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            {{ !empty($appInfo->irc_manually_approved_date) ? date("d-M-Y", strtotime($appInfo->irc_manually_approved_date)) : ''  }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

                <div class="panel panel-info">
                    <div class="panel-heading"><strong>IRC Information (CCI&E)</strong></div>
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                <span class="v_label">
                                    Please give your last IRC Number  (CCI&E)
                                </span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    <a href="{{ $data['irc_ref_app_url'] }}" target="_blank">
                                        <span class="label label-success label_tracking_no">{{ (empty($appInfo->irc_ccie_no) ? '' : $appInfo->irc_ccie_no) }}</span>
                                    </a>

                                    @if(!empty($appInfo->irc_ccie_brows_copy))
                                        <a target="_blank" class="documentUrl btn btn-xs btn-success" href="{{URL::to('/uploads/'.(!empty($appInfo->irc_ccie_brows_copy) ? $appInfo->irc_ccie_brows_copy : ''))}}">
                                            <i class="fa fa-file-pdf" aria-hidden="true"></i>
                                            View Certificate
                                        </a>
                                    @else
                                        <span class="badge badge-warning">Certificate not found</span>
                                    @endif

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Approved Date</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ !empty($appInfo->irc_ccie_approve_date) ? date("d-M-Y", strtotime($appInfo->irc_ccie_approve_date)) : ''  }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <fieldset class="scheduler-border">
                    <legend class="scheduler-border">Please specify your desired office:</legend>
                    <h4>You have selected <b>' {{$appInfo->divisional_office_name}} '</b>, {{ $appInfo->divisional_office_address }}.</h4>
                </fieldset>

                {{--Company basic information--}}
                <div class="panel panel-info">
                    <div class="panel-heading"><strong>Company Information</strong></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">Name of the organization/ company/ industrial project</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($appInfo->company_id)) ? $appInfo->company_name : '' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">Name of the organization/ company/ industrial project (বাংলা)</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($appInfo->company_id)) ? $appInfo->company_name_bn : '' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">Name of the project</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($appInfo->project_name)) ? $appInfo->project_name : '' }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">Type of the organization</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($appInfo->organization_type_name)) ? $appInfo->organization_type_name : '' }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">Status of the organization </span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($appInfo->organization_status_name)) ? $appInfo->organization_status_name : '' }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">Ownership status</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($appInfo->ownership_status_name)) ? $appInfo->ownership_status_name : '' }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">Country of Origin</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($appInfo->country_of_origin_name)) ? $appInfo->country_of_origin_name : '' }}
                            </div>
                        </div>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><strong>Other info. based on your business class (Code
                                    = {{ (!empty($appInfo->class_code)) ? $appInfo->class_code :''  }})</strong>
                            </legend>
                            <table class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th width="20%" scope="col">Category</th>
                                    <th width="10%" scope="col">Code</th>
                                    <th width="70%" scope="col">Description</th>
                                </tr>
                                </thead>
                                @if(!empty($business_code))
                                    <tbody>
                                    <tr>
                                        <td>Section</td>
                                        <td>{{ $business_code[0]['section_code'] }}</td>
                                        <td>{{ $business_code[0]['section_name'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Division</td>
                                        <td>{{ $business_code[0]['division_code'] }}</td>
                                        <td>{{ $business_code[0]['division_name'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Group</td>
                                        <td>{{ $business_code[0]['group_code'] }}</td>
                                        <td>{{ $business_code[0]['group_name'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Class</td>
                                        <td>{{ $business_code[0]['code'] }}</td>
                                        <td>{{ $business_code[0]['name'] }}</td>
                                    </tr>

                                    <tr>
                                        <td>Sub class</td>
                                        <td colspan="2">{{ (!empty($sub_class->name)) ? $sub_class->name : 'Other' }}</td>
                                    </tr>

                                    @if($appInfo->sub_class_id == 0)
                                        <tr>
                                            <td>Other sub class code</td>
                                            <td colspan="2">{{ (!empty($appInfo->other_sub_class_code)) ? $appInfo->other_sub_class_code : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Other sub class name</td>
                                            <td colspan="2">{{ (!empty($appInfo->other_sub_class_name)) ? $appInfo->other_sub_class_name : '' }}</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                @endif
                            </table>
                        </fieldset>

                        <div class="row">
                            <div class="col-md-3">
                                <span class="v_label">Major activities in brief</span>
                                <span class="pull-right">:</span>
                            </div>
                            <div class="col-md-9">
                                {{ (!empty($appInfo->major_activities)) ? $appInfo->major_activities :'N/A'  }}
                            </div>
                        </div>
                    </div>
                </div>

                {{--Information of Principal Promoter/Chairman/Managing Director/CEO/Country Manager--}}
                <div class="panel panel-info">
                    <div class="panel-heading"><strong>Information of Principal Promoter/ Chairman/ Managing
                            Director/ CEO/ Country Manager</strong></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Country</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->ceo_country_name)) ? $appInfo->ceo_country_name : ''  }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Date of Birth</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ !empty($appInfo->ceo_dob) ? date('d-M-Y', strtotime($appInfo->ceo_dob)) : ''  }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                @if($appInfo->ceo_country_id == 18)
                                    <div class="row">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">NID No.</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            {{ (!empty($appInfo->ceo_nid)) ? $appInfo->ceo_nid:''  }}
                                        </div>
                                    </div>
                                @else
                                    <div class="row">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Passport No.</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            {{ (!empty($appInfo->ceo_passport_no)) ? $appInfo->ceo_passport_no:''  }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Designation</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->ceo_designation)) ? $appInfo->ceo_designation:''  }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Full Name</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->ceo_full_name)) ? $appInfo->ceo_full_name:''  }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                @if($appInfo->ceo_country_id == 18)
                                    <div class="row">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">City</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            {{ (!empty($appInfo->ceo_district_name)) ? $appInfo->ceo_district_name :''  }}
                                        </div>
                                    </div>
                                @else
                                    <div class="row">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">District/City/State</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            {{ (!empty($appInfo->ceo_city)) ? $appInfo->ceo_city:''  }}                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                @if($appInfo->ceo_country_id == 18)
                                    <div class="row">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Police Station/Town</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            {{ (!empty($appInfo->ceo_thana_name)) ? $appInfo->ceo_thana_name :''  }}
                                        </div>
                                    </div>
                                @else
                                    <div class="row">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">State/Province</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            {{ (!empty($appInfo->ceo_state)) ? $appInfo->ceo_state:''  }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Post/Zip Code</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->ceo_post_code)) ? $appInfo->ceo_post_code:''  }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">House,Flat/Apartment,Road</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->ceo_address)) ? $appInfo->ceo_address:''  }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Telephone No</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->ceo_telephone_no)) ? $appInfo->ceo_telephone_no:''  }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Mobile No</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->ceo_mobile_no)) ? $appInfo->ceo_mobile_no:''  }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Father's Name</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->ceo_father_name)) ? $appInfo->ceo_father_name:''  }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Email</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->ceo_email)) ? $appInfo->ceo_email:''  }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Mother's Name</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->ceo_mother_name)) ? $appInfo->ceo_mother_name:''  }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Fax No</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->ceo_fax_no)) ? $appInfo->ceo_fax_no :''  }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Spouse name</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->ceo_spouse_name)) ? $appInfo->ceo_spouse_name:''  }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Gender</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->ceo_gender)) ? $appInfo->ceo_gender : '' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{--Office Address --}}
                <div class="panel panel-info">
                    <div class="panel-heading"><strong>Office Address</strong></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Division</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->office_division_name)) ? $appInfo->office_division_name :''  }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">District</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->office_district_name)) ? $appInfo->office_district_name :''  }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Police Station</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->office_thana_name)) ? $appInfo->office_thana_name :''  }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Post Office</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->office_post_office)) ? $appInfo->office_post_office:''  }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Post Code</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->office_post_code)) ? $appInfo->office_post_code:''  }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Address</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->office_address)) ? $appInfo->office_address:''  }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Telephone No</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->office_telephone_no)) ? $appInfo->office_telephone_no:''  }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Mobile No</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->office_mobile_no)) ? $appInfo->office_mobile_no:''  }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Fax No</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->office_fax_no)) ? $appInfo->office_fax_no:''  }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Email</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->office_email)) ? $appInfo->office_email:''  }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{--Factory Address --}}
                <div class="panel panel-info">
                    <div class="panel-heading"><strong>Factory Address(This would be IRC address)</strong></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">District</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->factory_district_name)) ? $appInfo->factory_district_name :''  }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Police Station</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->factory_thana_id)) ? $appInfo->factory_thana_name :''  }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Post Office</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->factory_post_office)) ? $appInfo->factory_post_office:''  }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Post Code</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->factory_post_code)) ? $appInfo->factory_post_code:''  }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Address</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->factory_address)) ? $appInfo->factory_address:''  }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Telephone No</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->factory_telephone_no)) ? $appInfo->factory_telephone_no:''  }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Mobile No</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->factory_mobile_no)) ? $appInfo->factory_mobile_no:''  }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5 col-xs-6">
                                        <span class="v_label">Fax No</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->factory_fax_no)) ? $appInfo->factory_fax_no:''  }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{--Registration Information--}}
                <div class="panel panel-info">
                    <div class="panel-heading"><strong>Registration Information</strong></div>
                    <div class="panel-body">

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">1. Project status</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3 col-xs-6">
                                        <span class="v_label">Project status</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-9 col-xs-6">
                                        {{ (!empty($appInfo->project_status_name)) ? $appInfo->project_status_name : ''  }}
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">2. Date of commercial operation</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3 col-xs-6">
                                        <span class="v_label">Date of commercial operation</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-9 col-xs-6">
                                        {{ !empty($appInfo->commercial_operation_date) ? date('d-M-Y',strtotime($appInfo->commercial_operation_date)) : ''  }}
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">3. Investment</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered" cellspacing="0"
                                                   width="100%">
                                                <tbody id="investment_tbl">
                                                <tr>
                                                    <th colspan="3">Items</th>
                                                </tr>

                                                <tr>
                                                    <th width="50%">Fixed Investment</th>
                                                    <td width="25%"></td>
                                                    <td width="25%"></td>

                                                </tr>
                                                <tr>
                                                    <td> &nbsp;&nbsp;&nbsp;&nbsp; Land (Million)</td>
                                                    <td>{{ (!empty($appInfo->local_land_ivst) ? $appInfo->local_land_ivst : '') }}</td>
                                                    <td>{{!empty($appInfo->local_land_ivst_ccy_code) ? $appInfo->local_land_ivst_ccy_code : ""}}</td>

                                                </tr>
                                                <tr>
                                                    <td> &nbsp;&nbsp;&nbsp;&nbsp; Building (Million)</td>
                                                    <td>{{(!empty($appInfo->local_building_ivst) ? $appInfo->local_building_ivst : '')}}</td>
                                                    <td>{{!empty($appInfo->local_building_ivst_ccy_code) ? $appInfo->local_building_ivst_ccy_code : ""}}</td>

                                                </tr>
                                                <tr>
                                                    <td> &nbsp;&nbsp;&nbsp;&nbsp; Machinery & Equipment (Million)</td>
                                                    <td>{{(!empty($appInfo->local_machinery_ivst) ? $appInfo->local_machinery_ivst : '')}}</td>
                                                    <td>{{!empty($appInfo->local_machinery_ivst_ccy_code) ? $appInfo->local_machinery_ivst_ccy_code : ""}}</td>
                                                </tr>
                                                <tr>
                                                    <td> &nbsp;&nbsp;&nbsp;&nbsp; Others (Million)</td>
                                                    <td>{{(!empty($appInfo->local_others_ivst) ? $appInfo->local_others_ivst : '')}}</td>
                                                    <td>{{!empty($appInfo->local_others_ivst_ccy_code) ? $appInfo->local_others_ivst_ccy_code :""}}</td>

                                                </tr>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp; Working Capital (Three Months)
                                                        (Million)
                                                    </td>
                                                    <td>{{ (!empty($appInfo->local_wc_ivst) ? $appInfo->local_wc_ivst : '') }}</td>
                                                    <td>{{!empty($appInfo->local_wc_ivst_ccy_code) ? $appInfo->local_wc_ivst_ccy_code :""}}</td>
                                                </tr>
                                                <tr>
                                                    <td> &nbsp;&nbsp;&nbsp;&nbsp; Total Investment (Million) (BDT)</td>
                                                    <td colspan="3">
                                                        {{ (!empty($appInfo->total_fixed_ivst_million) ? CommonFunction::convertToMillionAmount($appInfo->total_fixed_ivst_million) : '') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> &nbsp;&nbsp;&nbsp;&nbsp; Total Investment (BDT)</td>
                                                    <td colspan="3">
                                                        {{ (!empty($appInfo->total_fixed_ivst) ? CommonFunction::convertToBdtAmount($appInfo->total_fixed_ivst) : '') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> &nbsp;&nbsp;&nbsp;&nbsp; Dollar exchange rate (USD)</td>
                                                    <td colspan="3">
                                                        {{ (!empty($appInfo->usd_exchange_rate) ? $appInfo->usd_exchange_rate : '') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> &nbsp;&nbsp;&nbsp;&nbsp; Total Fee (BDT)</td>
                                                    <td colspan="3">
                                                        {{ (!empty($appInfo->total_fee) ? CommonFunction::convertToBdtAmount($appInfo->total_fee) : '') }}
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">4. Source of Finance</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered" cellspacing="0"
                                                   width="100%">
                                                <tbody>
                                                <tr>
                                                    <td width="50%"><strong>(a)</strong> Local Equity (Million)</td>
                                                    <td width="50%">{{(!empty($appInfo->finance_src_loc_equity_1) ? $appInfo->finance_src_loc_equity_1 : '')}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Foreign Equity (Million)</td>
                                                    <td>{{ (!empty($appInfo->finance_src_foreign_equity_1) ? $appInfo->finance_src_foreign_equity_1 : '') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Total Equity (Million)</th>
                                                    <td>{{ (!empty($appInfo->finance_src_loc_total_equity_1) ? CommonFunction::convertToMillionAmount($appInfo->finance_src_loc_total_equity_1) : '') }}</td>
                                                </tr>

                                                <tr>
                                                    <td><strong>(b)</strong> Local Loan (Million)</td>
                                                    <td>{{ (!empty($appInfo->finance_src_loc_loan_1) ? $appInfo->finance_src_loc_loan_1 : '') }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Foreign Loan (Million)</td>
                                                    <td>{{ (!empty($appInfo->finance_src_foreign_loan_1) ? $appInfo->finance_src_foreign_loan_1 : '') }}</td>
                                                </tr>

                                                <tr>
                                                    <th>Total Loan (Million)</th>
                                                    <td>{{ (!empty($appInfo->finance_src_total_loan) ? CommonFunction::convertToMillionAmount($appInfo->finance_src_total_loan) : '') }}</td>
                                                </tr>

                                                <tr>
                                                    <th>Total Financing Million (a+b)</th>
                                                    <td>{{ !empty($appInfo->finance_src_loc_total_financing_m) ? CommonFunction::convertToMillionAmount($appInfo->finance_src_loc_total_financing_m) : '' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Total Financing BDT (a+b)</th>
                                                    <td>{{ !empty($appInfo->finance_src_loc_total_financing_1) ? CommonFunction::convertToBdtAmount($appInfo->finance_src_loc_total_financing_1) : '' }}</td>
                                                </tr>

                                                </tbody>
                                            </table>
                                            <table class="table table-striped table-bordered" cellspacing="0"
                                                   width="100%" id="financeTableId">
                                                <thead>
                                                <tr>
                                                    <th colspan="4">
                                                        <i class="fa fa-question-circle" data-toggle="tooltip"
                                                           data-placement="top"
                                                           title="From the above information, the values of &quot;Local Equity (Million)&quot; and &quot;Local Loan (Million)&quot; will go into the
                                                           Equity Amount&quot; and &quot;Loan Amount&quot; respectively for Bangladesh. The summation of the &quot;Equity Amount&quot; and &quot;Loan Amount&quot; of other countries will be equal to the values of &quot;Foreign Equity (Million)&quot; and &quot;Foreign Loan (Million)&quot; respectively.">
                                                        </i>
                                                        Country wise source of finance (Million BDT)
                                                    </th>
                                                </tr>
                                                </thead>

                                                <tr>
                                                    <th>#</th>
                                                    <th>Country</th>
                                                    <th>Equity Amount</th>
                                                    <th>Loan Amount</th>
                                                </tr>

                                                @if(count($source_of_finance) > 0)
                                                    <?php $i = 1; ?>
                                                    @foreach($source_of_finance as $finance)
                                                        <tr>
                                                            <td>{{ $i++ }}</td>
                                                            <td>{{ $finance->country_name }}</td>
                                                            <td>{{ CommonFunction::convertToMillionAmount($finance->equity_amount) }}</td>
                                                            <td>{{ CommonFunction::convertToMillionAmount($finance->loan_amount) }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">5. Manpower of the organization</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                            <thead>
                                            <tr>
                                                <th class="text-center" style="padding: 5px;" colspan="3">Local (a)</th>
                                                <th class="text-center" style="padding: 5px;" colspan="3">Foreign (b)</th>
                                                <th class="text-center" style="padding: 5px;">Grand Total</th>
                                                <th class="text-center" style="padding: 5px;" colspan="2">Ratio</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td class="text-center" style="padding: 5px;">Executive</td>
                                                <td class="text-center" style="padding: 5px;">Supporting stuff</td>
                                                <td class="text-center" style="padding: 5px;">Total</td>
                                                <td class="text-center" style="padding: 5px;">Executive</td>
                                                <td class="text-center" style="padding: 5px;">Supporting stuff</td>
                                                <td class="text-center" style="padding: 5px;">Total</td>
                                                <td class="text-center" style="padding: 5px;">(a+b)</td>
                                                <td class="text-center" style="padding: 5px;">Local</td>
                                                <td class="text-center" style="padding: 5px;">Foreign</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 5px;">
                                                    <span> {{ (!empty($appInfo->local_male))? $appInfo->local_male:''  }}</span>
                                                </td>
                                                <td style="padding: 5px;">
                                                    <span> {{ (!empty($appInfo->local_female))? $appInfo->local_female:''  }}</span>
                                                </td>
                                                <td style="padding: 5px;">
                                                    <span> {{ (!empty($appInfo->local_total))? $appInfo->local_total:''  }}</span>
                                                </td>
                                                <td style="padding: 5px;">
                                                    <span> {{ (!empty($appInfo->foreign_male))? $appInfo->foreign_male:''  }}</span>
                                                </td>
                                                <td style="padding: 5px;">
                                                    <span> {{ (!empty($appInfo->foreign_female))? $appInfo->foreign_female:''  }}</span>
                                                </td>
                                                <td style="padding: 5px;">
                                                    <span> {{ (!empty($appInfo->foreign_total))? $appInfo->foreign_total:''  }}</span>
                                                </td>
                                                <td style="padding: 5px;">
                                                    <span> {{ (!empty($appInfo->manpower_total))? $appInfo->manpower_total:''  }}</span>
                                                </td>
                                                <td style="padding: 5px;">
                                                    <span> {{ (!empty($appInfo->manpower_local_ratio))? $appInfo->manpower_local_ratio:''  }}</span>
                                                </td>
                                                <td style="padding: 5px;">
                                                    <span> {{ (!empty($appInfo->manpower_foreign_ratio))? $appInfo->manpower_foreign_ratio:''  }}</span>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">6. Sales (in 100%)</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="col-md-4 col-xs-6">
                                            <span class="v_label">Local</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-8 col-xs-6">
                                            {{ (!empty($appInfo->local_sales)) ? $appInfo->local_sales :''  }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="col-md-4 col-xs-6">
                                            <span class="v_label">Foreign</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-8 col-xs-6">
                                            {{ (!empty($appInfo->foreign_sales)) ? $appInfo->foreign_sales :''  }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="col-md-4 col-xs-6">
                                            <span class="v_label">Total in %</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-8 col-xs-6">
                                            {{ (!empty($appInfo->total_sales)) ? $appInfo->total_sales :''  }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        {{--7. Annual production capacity--}}
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">7. Annual Production Capacity as per BIDA registration/Amendment</legend>
                            {{-- <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3 col-xs-6">
                                        <span class="v_label">Annual production start date</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-9 col-xs-6">
                                        {{ (!empty($appInfo->annual_production_start_date) ? date('d-M-Y', strtotime($appInfo->annual_production_start_date)): "") }}
                                    </div>
                                </div>
                            </div>
                            <br> --}}
                            <fieldset class="scheduler-border">
                                {{-- <legend class="scheduler-border">Annual Production Capacity as per BIDA registration</legend> --}}
                                <fieldset >
{{--                                    <legend class="scheduler-border">Raw Materials Details</legend>--}}
                                    <div class="panel panel-info">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="brProductionSpareTbl">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name of Product</th>
                                                    <th>Unit of Quantity</th>
                                                    <th>Quantity</th>
                                                    <th>Price (USD)</th>
                                                    <th>Sales Value in BDT (million)</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if(count($IrcBrAnnualProductionCapacity) > 0)
                                                    <?php $inc = 1; ?>
                                                    @foreach($IrcBrAnnualProductionCapacity as $BRapc)
                                                        <tr>
                                                            <td>{{ $inc++ }}</td>
                                                            <td>{{ $BRapc->product_name }}</td>
                                                            <td>{{ $BRapc->name }}</td>
                                                            <td>{{ $BRapc->quantity }}</td>
                                                            <td>{{ $BRapc->price_usd }}</td>
                                                            <td>{{ $BRapc->price_taka }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </fieldset>
                            </fieldset>
                            @if($appInfo->purpose_id != 2 && count($annualProductionCapacity) > 0)
                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">Recommended Annual Production Capacity as per IRC with Raw Materials.</legend>
                                    <fieldset id="annual_raw">
                                       {{-- <legend class="scheduler-border">Raw Materials Details</legend> --}}
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="productionCostTbl">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name of Product</th>
                                                    <th>Unit of Quantity</th>
                                                    <th>Quantity</th>
                                                    <th>Price (USD)</th>
                                                    <th>Sales Value in BDT (million)</th>
                                                    <th>Raw Materials Details</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $inc = 1 ?>
                                                @foreach($annualProductionCapacity as $apc)
                                                    <tr>
                                                        <td>{{ $inc++ }}</td>
                                                        <td>{{ $apc->product_name }}</td>
                                                        <td>{{ $apc->name }}</td>
                                                        <td>{{ $apc->quantity }}</td>
                                                        <td>{{ $apc->price_usd }}</td>
                                                        <td>{{ $apc->price_taka }}</td>
                                                        <td>
                                                            <a data-toggle="modal" data-target="#IRCModal" onclick="openModal(this)" data-action="{{ url('irc-recommendation-regular/get-raw-material/'.Encryption::encodeId($apc->apc_id)) }}" class="btn btn-xs btn-success"><i class="fa fa-folder-open"></i> Raw Material</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </fieldset>
                                </fieldset>
                            @endif

                            @if($appInfo->purpose_id != 1 && count($annualProductionSpareParts) > 0)
                                <fieldset class="scheduler-border" style="display: none;">
                                    <legend class="scheduler-border">Spare Parts Details</legend>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="productionSpareTbl">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name of the Spare Parts</th>
                                                <th>Unit of Quantity</th>
                                                <th>Quantity</th>
                                                <th>Price (USD)</th>
                                                <th>Value Taka (in million)</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $inc = 1 ?>
                                            @foreach($annualProductionSpareParts as $apsp)
                                                <tr>
                                                    <td>{{ $inc++ }}</td>
                                                    <td>{{ $apsp->product_name }}</td>
                                                    <td>{{ $apsp->name }}</td>
                                                    <td>{{ $apsp->quantity }}</td>
                                                    <td>{{ $apsp->price_usd }}</td>
                                                    <td>{{ $apsp->price_taka }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </fieldset>
                            @endif
                        </fieldset>

                        {{--8. Existing machines--}}
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">8. Existing machines ‍as per BIDA Registration/Amendment</legend>

                            {{--Spare Parts--}}
                            @if($appInfo->irc_purpose_id != 1 && count($existing_machines_spare) > 0)
                                <fieldset class="scheduler-border">
                                    {{-- <legend class="scheduler-border">Spare Parts</legend> --}}
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="table table-striped table-bordered" cellspacing="0"
                                                    width="100%" id="financeTableId">
                                                    <thead>
                                                    <tr>
                                                        <td>L/C Number</td>
                                                        <td>LC Date</td>
                                                        <td>L/C Value (In Foreign Currency)</td>
                                                        <td>Value (In BDT)</td>
                                                        <td>L/C Opening Bank & Branch Name</td>
                                                        <td>Attachment</td>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($existing_machines_spare as $EM_spare)
                                                        <tr>
                                                            <td>{{ ($EM_spare->lc_no ? $EM_spare->lc_no : '') }}</td>
                                                            <td>{{ ((!empty($EM_spare->lc_date)) ? date('d-M-Y', strtotime($EM_spare->lc_date)) : '') }}</td>
                                                            <td>{{ ($EM_spare->code ? $EM_spare->code : '') }}</td>
                                                            <td>{{ ($EM_spare->value_bdt ? $EM_spare->value_bdt : '') }}</td>
                                                            <td>{{ ($EM_spare->lc_bank_branch ? $EM_spare->lc_bank_branch : '') }}</td>
                                                            <td>
                                                                @if(!empty($EM_spare->attachment))
                                                                    <a target="_blank" class="btn btn-xs btn-primary"
                                                                    href="{{URL::to('/uploads/'.$EM_spare->attachment)}}"
                                                                    title="{{ $EM_spare->attachment}}">
                                                                        <i class="fa fa-file-pdf" aria-hidden="true"></i>
                                                                        Open File
                                                                    </a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td colspan="3"><span class="pull-right">Total</span></td>
                                                        <td>{{ $total_existing_machines_spare }}</td>
                                                    </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            @endif

                            {{--As Per L/C Open--}}
                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border">As Per L/C Open</legend>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table table-striped table-bordered" cellspacing="0"
                                                width="100%" id="financeTableId">
                                                <thead>
                                                <tr>
                                                    <td>Description of Machine</td>
                                                    <td>Unit of Quantity</td>
                                                    <td>Quantity (A)</td>
                                                    <td colspan="2">Unit Price (B)</td>
                                                    <td>Price Foreign Currency (A X B)</td>
                                                    <td>Price BDT (C)</td>
                                                    <td>Value Taka (in million)</td>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($existing_machines_lc as $existing_machines_lc)
                                                    <tr>
                                                        <td>{{ ($existing_machines_lc->product_name ? $existing_machines_lc->product_name : '') }}</td>
                                                        <td>{{ ($existing_machines_lc->name ? $existing_machines_lc->name : '') }}</td>
                                                        <td>{{ ($existing_machines_lc->quantity ? $existing_machines_lc->quantity : '') }}</td>
                                                        <td>{{ ($existing_machines_lc->unit_price ? $existing_machines_lc->unit_price : '') }}</td>
                                                        <td>{{ ($existing_machines_lc->code ? $existing_machines_lc->code : '') }}</td>
                                                        <td>{{ ($existing_machines_lc->price_foreign_currency ? $existing_machines_lc->price_foreign_currency : '') }}</td>
                                                        <td>{{ ($existing_machines_lc->price_bdt ? $existing_machines_lc->price_bdt : '') }}</td>
                                                        <td>{{ ($existing_machines_lc->price_taka_mil ? $existing_machines_lc->price_taka_mil : '') }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <td colspan="6"><span class="pull-right">Total</span></td>
                                                    <td>{{ $total_existing_machines_lc_bdt }}</td>
                                                    <td>{{ $appInfo->em_lc_total_taka_mil }}</td>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            {{--As per Local Procurement/ Collection--}}
                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border">As per Local Procurement/ Collection</legend>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="financeTableId">
                                                <thead>
                                                <tr>
                                                    <td>Description of Machine</td>
                                                    <td>Unit of Quantity</td>
                                                    <td>Quantity (A)</td>
                                                    <td>Unit Price (B)</td>
                                                    <td>Price BDT (A X B) </td>
                                                    <td>Value Taka (in million)</td>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($existing_machines_local as $existing_machines_local)
                                                    <tr>
                                                        <td>{{ ($existing_machines_local->product_name ? $existing_machines_local->product_name : '') }}</td>
                                                        <td>{{ ($existing_machines_local->name ? $existing_machines_local->name : '') }}</td>
                                                        <td>{{ ($existing_machines_local->quantity ? $existing_machines_local->quantity : '') }}</td>
                                                        <td>{{ ($existing_machines_local->unit_price ? $existing_machines_local->unit_price : '') }}</td>
                                                        <td>{{ ($existing_machines_local->price_bdt ? $existing_machines_local->price_bdt : '') }}</td>
                                                        <td>{{ ($existing_machines_local->price_taka_mil ? $existing_machines_local->price_taka_mil : '') }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <td colspan="5"><span class="pull-right">Total</span></td>
                                                    <td>{{ $appInfo->em_local_total_taka_mil }}</td>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">9. Value of the Existing Machineries</legend>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead class="alert alert-info">
                                    <tr>
                                        <th>Imported Value BDT</th>
                                        <th>Local Value BDT</th>
                                        <th>Total Value BDT</th>
                                        <th>Attachment</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            {{ !empty($appInfo->ex_machine_imported_value_bdt) ? $appInfo->ex_machine_imported_value_bdt : '' }}
                                        </td>
                                        <td>
                                            {{ !empty($appInfo->ex_machine_local_value_bdt) ? $appInfo->ex_machine_local_value_bdt : '' }}
                                        </td>
                                        <td>
                                            {{ !empty($appInfo->ex_machine_total_value_bdt) ? $appInfo->ex_machine_total_value_bdt : '' }}
                                        </td>
                                        <td>
                                            @if(!empty($appInfo->ex_machine_attachment))
                                                <a target="_blank" class="documentUrl btn btn-xs btn-primary" href="{{URL::to('/uploads/'.(!empty($appInfo->ex_machine_attachment) ? $appInfo->ex_machine_attachment : ''))}}">
                                                    <i class="fa fa-file-pdf" aria-hidden="true"></i>
                                                    Open File
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">10. Imported Raw materials/ Packaging Materials/ Spare parts details</legend>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead class="alert alert-info">
                                    <tr>
                                        <th colspan="2">Duration</th>
                                        <th>Total Price (USD)</th>
                                        <th>Total Price (BDT)</th>
                                        <th>Attachment</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <span class="v_label">From</span> {{ !empty($appInfo->import_duration_from_date) ? date('d-M-Y', strtotime($appInfo->import_duration_from_date)) : "" }}
                                        </td>
                                        <td>
                                            <span class="v_label">To</span> {{ !empty($appInfo->import_duration_to_date) ? date('d-M-Y', strtotime($appInfo->import_duration_to_date)) : "" }}
                                        </td>
                                        <td>
                                            {{ !empty($appInfo->import_total_price_usd) ? $appInfo->import_total_price_usd : '' }}
                                        </td>
                                        <td>
                                            {{ !empty($appInfo->import_total_price_bdt) ? $appInfo->import_total_price_bdt : '' }}
                                        </td>
                                        <td>
                                            @if(!empty($appInfo->import_attachment))
                                                <a target="_blank" class="documentUrl btn btn-xs btn-primary" href="{{URL::to('/uploads/'.(!empty($appInfo->import_attachment) ? $appInfo->import_attachment : ''))}}">
                                                    <i class="fa fa-file-pdf" aria-hidden="true"></i>
                                                    Open File
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">11. Production Details</legend>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead class="alert alert-info">
                                    <tr>
                                        <th colspan="2">Duration</th>
                                        <th>Total Quantity (a)</th>
                                        <th>Total Sales (b)</th>
                                        <th>Total Stock (a-b)</th>
                                        <th>Attachment</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <span class="v_label">From</span> {{ !empty($appInfo->production_duration_from_date) ? date('d-M-Y', strtotime($appInfo->production_duration_from_date)) : "" }}
                                        </td>
                                        <td>
                                            <span class="v_label">To</span> {{ !empty($appInfo->production_duration_to_date) ? date('d-M-Y', strtotime($appInfo->production_duration_to_date)) : "" }}
                                        </td>
                                        <td>
                                            {{ !empty($appInfo->production_total_quantity) ? $appInfo->production_total_quantity : '' }}
                                        </td>
                                        <td>
                                            {{ !empty($appInfo->production_total_sales) ? $appInfo->production_total_sales : '' }}
                                        </td>
                                        <td>
                                            {{ !empty($appInfo->production_total_stock) ? $appInfo->production_total_stock : '' }}
                                        </td>
                                        <td>
                                            @if(!empty($appInfo->production_attachment))
                                                <a target="_blank" class="documentUrl btn btn-xs btn-primary" href="{{URL::to('/uploads/'.(!empty($appInfo->production_attachment) ? $appInfo->production_attachment : ''))}}">
                                                    <i class="fa fa-file-pdf" aria-hidden="true"></i>
                                                    Open File
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">12. Sales Statement</legend>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="saleStatementTbl">
                                    <thead class="alert alert-info">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th rowspan="2">Month & Year</th>
                                        <th>Sales Value (BDT)</th>
                                        <th>VAT (BDT)</th>
                                        <th>Attachment</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(count($IRCSalesStatements) > 0)
                                        <?php
                                        $inc = 1;
                                        $total_bdt_value = 0;
                                        $total_vat_value = 0;
                                        ?>
                                        @foreach($IRCSalesStatements as $IRCSalesStatement)
                                            <tr>
                                                <td>{{ $inc++ }}</td>
                                                <td><span class="v_label">From</span> {{ !empty($IRCSalesStatement->sales_statement_from_date) ? date('d-M-Y', strtotime($IRCSalesStatement->sales_statement_from_date)) : "" }}</td>
                                                <td><span class="v_label">To</span> {{ !empty($IRCSalesStatement->sales_statement_to_date) ? date('d-M-Y', strtotime($IRCSalesStatement->sales_statement_to_date)) : "" }}</td>
                                                <td>{{ $IRCSalesStatement->sales_value_bdt }}</td>
                                                <td>{{ $IRCSalesStatement->sales_vat_bdt }}</td>
                                                <td>
                                                    @if(!empty($IRCSalesStatement->sales_attachment))
                                                        <a target="_blank" class="documentUrl btn btn-xs btn-primary" href="{{URL::to('/uploads/'.(!empty($IRCSalesStatement->sales_attachment) ? $IRCSalesStatement->sales_attachment : ''))}}">
                                                            <i class="fa fa-file-pdf" aria-hidden="true"></i>
                                                            Open File
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                            <?php
                                            $total_bdt_value += $IRCSalesStatement->sales_value_bdt;
                                            $total_vat_value += $IRCSalesStatement->sales_vat_bdt;
                                            ?>
                                        @endforeach
                                    @endif
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-right">Total:</td>
                                        <td>{{ number_format($appInfo->sales_value_bdt_total, 2) }}</td>
                                        <td>{{ number_format($appInfo->sales_vat_total, 2) }}</td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">13. Export Statement</legend>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead class="alert alert-info">
                                    <tr>
                                        <th colspan="2">Duration</th>
                                        <th>Total Price (USD)</th>
                                        <th>Total Price (BDT)</th>
                                        <th>Attachment</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <span class="v_label">From</span> {{ !empty($appInfo->export_duration_from_date) ? date('d-M-Y', strtotime($appInfo->export_duration_from_date)) : "" }}
                                        </td>
                                        <td>
                                            <span class="v_label">To</span> {{ !empty($appInfo->export_duration_to_date) ? date('d-M-Y', strtotime($appInfo->export_duration_to_date)) : "" }}
                                        </td>
                                        <td>
                                            {{ !empty($appInfo->export_total_price_usd) ? $appInfo->export_total_price_usd : '' }}
                                        </td>
                                        <td>
                                            {{ !empty($appInfo->export_total_price_bdt) ? $appInfo->export_total_price_bdt : '' }}
                                        </td>
                                        <td>
                                            @if(!empty($appInfo->export_attachment))
                                                <a target="_blank" class="documentUrl btn btn-xs btn-primary" href="{{URL::to('/uploads/'.(!empty($appInfo->export_attachment) ? $appInfo->export_attachment : ''))}}">
                                                    <i class="fa fa-file-pdf" aria-hidden="true"></i>
                                                    Open File
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">14. ‍A. According to the latest ad-hoc IRC</legend>
                            @if($appInfo->purpose_id != 2 && count($IrcSixMonthsImportRawMaterials) > 0)
                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">For Raw Materials</legend>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="insProductionRawTbl">
                                            <thead class="alert alert-info">
                                            <tr>
                                                <th>#</th>
                                                <th>Product Name</th>
                                                <th>Yearly production</th>
                                                <th colspan="2">Half yearly production</th>
                                                <th>Half year amount</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $inc = 1 ?>
                                            @foreach($IrcSixMonthsImportRawMaterials as $value)
                                                <tr>
                                                    <td>{{ $inc++ }}</td>
                                                    <td>{{ $value->product_name }}</td>
                                                    <td>{{ $value->yearly_production }}</td>
                                                    <td colspan="2">{{ $value->half_yearly_production }}</td>
                                                    {{-- <td>{{ $value->name }}</td> --}}
                                                    <td>{{ $value->half_yearly_import }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <table class="table" width="100%">
                                        <tr>
                                            <td colspan="2" class="text-right v_label">Total Import capacity for raw materials :</td>
                                            <td>{{ !empty($appInfo->ins_apc_half_yearly_import_total) ? $appInfo->ins_apc_half_yearly_import_total : "" }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="text-right v_label">Grand Total (in words) :</td>
                                            <td>{{ !empty($appInfo->ins_apc_half_yearly_import_total_in_word) ? $appInfo->ins_apc_half_yearly_import_total_in_word : "" }}</td>
                                        </tr>
                                    </table>

                                </fieldset>
                            @endif

                            {{-- @if($appInfo->purpose_id != 1)--}}
                            {{--     <fieldset class="scheduler-border">--}}
                            {{--         <legend class="scheduler-border">For Spare Parts</legend>--}}
                            {{--         <table class="table" width="100%">--}}
                            {{--             <tr>--}}
                            {{--                 <td colspan="2" class="text-right v_label">Total Import capacity for raw materials :</td>--}}
                            {{--                 <td>{{ !empty($appInfo->ins_apsp_half_yearly_import_total) ? $appInfo->ins_apsp_half_yearly_import_total : '' }}</td>--}}
                            {{--             </tr>--}}
                            {{--             <tr>--}}
                            {{--                 <td colspan="2" class="text-right v_label">Grand Total (in words) :</td>--}}
                            {{--                 <td>{{ !empty($appInfo->ins_apsp_half_yearly_import_total_in_word) ? $appInfo->ins_apsp_half_yearly_import_total_in_word : '' }}</td>--}}
                            {{--             </tr>--}}
                            {{--         </table>--}}
                            {{--     </fieldset>--}}
                            {{-- @endif--}}

                            @if($appInfo->irc_purpose_id != 1)
                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border"><span>Half-yearly import rights of adhoc based spare parts/ demand</span>
                                    </legend>
                                    <div class="panel-body">
                                        {{-- <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-7">
                                                    <span class="v_label">The total value of capitalized equipment LC by the organization</span>
                                                </div>

                                                <div class="col-md-5">
                                                    <span> {{ (!empty($appInfo->first_em_lc_total_taka_mil)) ? $appInfo->first_em_lc_total_taka_mil : '' }}</span>
                                                </div>
                                            </div>
                                        </div> --}}
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="col-md-3 col-xs-6">
                                                    <span class="v_label">Value In BDT</span>
                                                </div>
                                                <div class="col-md-9 col-xs-6">
                                                    <span> {{ (!empty($appInfo->first_em_lc_total_five_percent)) ? $appInfo->first_em_lc_total_five_percent : '' }}</span>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="col-md-2 col-xs-6">
                                                    <span class="v_label">in word</span>
                                                </div>
                                                <div class="col-md-10 col-xs-6">
                                                    <span> {{ (!empty($appInfo->first_em_lc_total_five_percent_in_word)) ? $appInfo->first_em_lc_total_five_percent_in_word : '' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="row"> --}}
                                            {{-- <div class="col-md-3">
                                                <div class="col-md-7 col-xs-6">
                                                    <span class="v_label">at the rate of</span>
                                                </div>
                                                <div class="col-md-5 col-xs-6">
                                                    <span> {{ (!empty($appInfo->first_em_lc_total_percent)) ? $appInfo->first_em_lc_total_percent : '' }}</span>
                                                </div>
                                            </div> --}}

                                            {{-- <div class="col-md-3">
                                                <div class="col-md-2 col-xs-6">
                                                    <span class="v_label">%</span>
                                                </div>
                                                <div class="col-md-10 col-xs-6">
                                                    <span> {{ (!empty($appInfo->first_em_lc_total_five_percent)) ? $appInfo->first_em_lc_total_five_percent : '' }}</span>
                                                </div>
                                            </div> --}}

                                            {{-- <div class="col-md-6">
                                                <div class="col-md-2 col-xs-6">
                                                    <span class="v_label">in word</span>
                                                </div>
                                                <div class="col-md-10 col-xs-6">
                                                    <span> {{ (!empty($appInfo->first_em_lc_total_five_percent_in_word)) ? $appInfo->first_em_lc_total_five_percent_in_word : '' }}</span>
                                                </div>
                                            </div> --}}
                                        {{-- </div> --}}
                                        <br>
                                        <div class="row">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    {!! Form::label('','Half-yearly import rights can be fixed for spare parts.',['class'=>'text-left']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            @endif

                            @if($appInfo->purpose_id == 3)
                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">Grand Total</legend>
                                    <table class="table" width="100%">
                                        <tr>
                                            <td colspan="2" class="text-right v_label">Grand Total: </td>
                                            <td>{{ !empty($appInfo->import_cap_grd_total) ? $appInfo->import_cap_grd_total : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="text-right v_label">Grand total (in words) : </td>
                                            <td>{{ !empty($appInfo->import_cap_grd_total_wrd) ? $appInfo->import_cap_grd_total_wrd : '' }}</td>
                                        </tr>
                                    </table>
                                </fieldset>
                            @endif
                        </fieldset>

                        @if($appInfo->irc_regular_purpose_id != 1)
                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border">14. B. According to the latest ad-hoc IRC Amendment Information</legend>
                                @if($appInfo->purpose_id != 2 && count($IrcSixMonthsImportRawMaterialsAmendment) > 0)
                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border">For Raw Materials</legend>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="insProductionRawTbl">
                                                <thead class="alert alert-info">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Product Name</th>
                                                    <th>Yearly production</th>
                                                    <th colspan="2">Half yearly production</th>
                                                    <th>Half year amount</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $inc = 1 ?>
                                                @foreach($IrcSixMonthsImportRawMaterialsAmendment as $value)
                                                    <tr>
                                                        <td>{{ $inc++ }}</td>
                                                        <td>{{ $value->n_product_name }}</td>
                                                        <td>{{ $value->n_yearly_production }}</td>
                                                        <td colspan="2">{{ $value->n_half_yearly_production }}</td>
                                                        {{-- <td>{{ $value->name }}</td> --}}
                                                        <td>{{ $value->n_half_yearly_import }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <table class="table" width="100%">
                                            <tr>
                                                <td colspan="2" class="text-right v_label">Total Import capacity for raw materials :</td>
                                                <td>{{ !empty($appInfo->n_ins_apc_half_yearly_import_total) ? $appInfo->n_ins_apc_half_yearly_import_total : "" }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="text-right v_label">Grand Total (in words) :</td>
                                                <td>{{ !empty($appInfo->n_ins_apc_half_yearly_import_total_in_word) ? $appInfo->n_ins_apc_half_yearly_import_total_in_word : "" }}</td>
                                            </tr>
                                        </table>

                                    </fieldset>
                                @endif

                                @if($appInfo->irc_purpose_id != 1)
                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border"><span>Half-yearly import rights of adhoc based spare parts/ demand</span>
                                        </legend>
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="col-md-3 col-xs-6">
                                                        <span class="v_label">Value In BDT</span>
                                                    </div>
                                                    <div class="col-md-9 col-xs-6">
                                                        <span> {{ (!empty($appInfo->n_first_em_lc_total_five_percent)) ? $appInfo->n_first_em_lc_total_five_percent : '' }}</span>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="col-md-2 col-xs-6">
                                                        <span class="v_label">in word</span>
                                                    </div>
                                                    <div class="col-md-10 col-xs-6">
                                                        <span> {{ (!empty($appInfo->n_first_em_lc_total_five_percent_in_word)) ? $appInfo->n_first_em_lc_total_five_percent_in_word : '' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-12">
                                                        {!! Form::label('','Half-yearly import rights can be fixed for spare parts.',['class'=>'text-left']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                @endif

                                @if($appInfo->purpose_id == 3)
                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border">Grand Total</legend>
                                        <table class="table" width="100%">
                                            <tr>
                                                <td colspan="2" class="text-right v_label">Grand Total: </td>
                                                <td>{{ !empty($appInfo->n_import_cap_grd_total) ? $appInfo->n_import_cap_grd_total : '' }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="text-right v_label">Grand total (in words) : </td>
                                                <td>{{ !empty($appInfo->n_import_cap_grd_total_wrd) ? $appInfo->n_import_cap_grd_total_wrd : '' }}</td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                @endif
                            </fieldset>
                        @endif

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">15. Public utility service</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">

                                        @if($appInfo->public_land == 1)
                                            <label class="checkbox-inline">
                                                <img src="{{ asset('assets/images/checked.png') }}" alt="Land" width="10" height="10"/> Land
                                            </label>
                                        @endif

                                        @if($appInfo->public_electricity == 1)
                                            <label class="checkbox-inline">
                                                <img src="{{ asset('assets/images/checked.png') }}" alt="Electricity" width="10" height="10"/> Electricity
                                            </label>
                                        @endif

                                        @if($appInfo->public_gas == 1)
                                            <label class="checkbox-inline">
                                                <img src="{{ asset('assets/images/checked.png') }}" alt="Gas" width="10" height="10"/> Gas
                                            </label>
                                        @endif

                                        @if($appInfo->public_telephone == 1)
                                            <label class="checkbox-inline">
                                                <img src="{{ asset('assets/images/checked.png') }}" alt="Telephone" width="10" height="10"/> Telephone
                                            </label>
                                        @endif

                                        @if($appInfo->public_road == 1)
                                            <label class="checkbox-inline">
                                                <img src="{{ asset('assets/images/checked.png') }}" alt="Road" width="10" height="10"/> Road
                                            </label>
                                        @endif

                                        @if($appInfo->public_water == 1)
                                            <label class="checkbox-inline">
                                                <img src="{{ asset('assets/images/checked.png') }}" alt="Water" width="10" height="10"/> Water
                                            </label>
                                        @endif

                                        @if($appInfo->public_drainage == 1)
                                            <label class="checkbox-inline">
                                                <img src="{{ asset('assets/images/checked.png') }}" alt="Drainage" width="10" height="10"/> Drainage
                                            </label>
                                        @endif

                                        @if($appInfo->public_others == 1)
                                            <label class="checkbox-inline">
                                                <img src="{{ asset('assets/images/checked.png') }}" alt="Others" width="10" height="10"/> {{ empty($appInfo->public_others_field) ? 'Others' : $appInfo->public_others_field }}
                                            </label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">16. Trade licence details</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Trade Licence Number</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->trade_licence_num)) ? $appInfo->trade_licence_num :''  }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Issuing Authority</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->trade_licence_issuing_authority)) ? $appInfo->trade_licence_issuing_authority:''  }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Issue Date</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ !empty($appInfo->trade_licence_issue_date) ? date('d-M-Y', strtotime($appInfo->trade_licence_issue_date)) :''  }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Validity Period</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->trade_licence_validity_period)) ? $appInfo->trade_licence_validity_period:''  }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">17. Incorporation Certificate Details</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Incorporation Number</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->inc_number)) ? $appInfo->inc_number :''  }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Issuing Authority</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->inc_issuing_authority)) ? $appInfo->inc_issuing_authority:''  }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">18. Tex Identification Number (TIN) Details</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">TIN Number</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->tin_number)) ? $appInfo->tin_number :''  }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Issuing Authority</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->tin_issuing_authority)) ? $appInfo->tin_issuing_authority:''  }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        {{--19. Fier license info--}}
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">19. Fire License Information Details</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Fire License Number</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->fl_number)) ? $appInfo->fl_number :'' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Expiry Date</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ !empty($appInfo->fl_expire_date) ? date('d-M-Y', strtotime($appInfo->fl_expire_date)) :'' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Issuing Authority</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->fl_issuing_authority)) ? $appInfo->fl_issuing_authority : '' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        {{--20. Environment/ Site clearance certificate--}}
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">20. Environment Clearance Certificate Details</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Environment License No</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->el_number)) ? $appInfo->el_number :'' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Expiry Date</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ !empty($appInfo->el_expire_date) ? date('d-M-Y', strtotime($appInfo->el_expire_date)) :'' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Issuing Authority</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->el_issuing_authority)) ? $appInfo->el_issuing_authority : '' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        {{--21. Bank information--}}
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">21. (a) Authorized Bank Information Details According to the latest IRC</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Bank name</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->existing_bank_name)) ? $appInfo->existing_bank_name : '' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Branch name</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->existing_branch_name)) ? $appInfo->existing_branch_name : '' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Account number</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->bank_account_number)) ? $appInfo->bank_account_number : '' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Account title</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->bank_account_title)) ? $appInfo->bank_account_title : '' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border" >
                            <legend class="scheduler-border">21. (b) Want to change bank information?</legend>

                            <div class="form-group">

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-5 col-xs-6">
                                                <span class="v_label">
                                                    Want to change bank information?
                                                </span>
                                                <span class="pull-right">&#58;</span>
                                            </div>
                                            <div class="col-md-7 col-xs-6">
                                                {{ (!empty($appInfo->chnage_bank_info)) ? ucfirst($appInfo->chnage_bank_info) : ''  }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border {{($appInfo->chnage_bank_info == 'yes') ? '': 'hidden'}}">
                            <legend class="scheduler-border">Authorized Bank Information Details</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Bank name</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->proposed_bank_name)) ? $appInfo->proposed_bank_name : '' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Branch name</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->proposed_branch_name)) ? $appInfo->proposed_branch_name : '' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Account number</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->n_bank_account_number)) ? $appInfo->n_bank_account_number : '' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Account title</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->n_bank_account_title)) ? $appInfo->n_bank_account_title : '' }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">NOC letter :</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            @if(!empty($appInfo->noc_letter))
                                                <a target="_blank" class="Url btn btn-xs btn-primary" href="{{URL::to('/uploads/'.(!empty($appInfo->noc_letter) ? $appInfo->noc_letter : ''))}}">
                                                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                                    Open File
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>


                        {{--22. Membership of Chamber/ Association information--}}
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">22. Membership of the Chamber/ Association Information Details</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Membership number</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->assoc_membership_number)) ? $appInfo->assoc_membership_number : '' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Chamber name</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->assoc_chamber_name)) ? $appInfo->assoc_chamber_name : '' }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Issuing date</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ !empty($appInfo->assoc_issuing_date) ? date('d-M-Y', strtotime($appInfo->assoc_issuing_date)) : '' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Expiry date</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ !empty($appInfo->assoc_expire_date) ? date('d-M-Y', strtotime($appInfo->assoc_expire_date)) : '' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        {{--23. BIN/ VAT--}}
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">23. Business Identification Number (BIN)/ Value-added Tex (VAT) Details</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">BIN/ VAT number</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->bin_vat_number)) ? $appInfo->bin_vat_number : '' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Issuing date</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ !empty($appInfo->bin_vat_issuing_date) ? date('d-M-Y', strtotime($appInfo->bin_vat_issuing_date)) : '' }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Issuing Authority</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->bin_vat_issuing_authority)) ? $appInfo->bin_vat_issuing_authority : '' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Expiry date</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ !empty($appInfo->bin_vat_expire_date) ? date('d-M-Y', strtotime($appInfo->bin_vat_expire_date)) : '' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        {{--24. Other Licenses/ NOC/ Permission/ Registration--}}
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">24. Other Licenses/ NOC/ Permission/ Registration</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped table-bordered" cellspacing="0"
                                               width="100%" id="financeTableId">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Licence Name</th>
                                                <th>Licence No/ Issue No</th>
                                                <th>Issuing Authority</th>
                                                <th>Date of Issue</th>
                                            </tr>
                                            </thead>
                                            @if(count($otherLicence)>0)
                                                <?php $inc = 0; ?>
                                                @foreach($otherLicence as $otherLicence)
                                                    <tr>
                                                        <td>{{ $i++ }}</td>
                                                        <td>{{ ($otherLicence->licence_name ? $otherLicence->licence_name : '') }}</td>
                                                        <td>{{ ($otherLicence->licence_no ? $otherLicence->licence_no : '') }}</td>
                                                        <td>{{ ($otherLicence->issuing_authority ? $otherLicence->issuing_authority : '') }}</td>
                                                        <td>{{ (!empty($otherLicence->issue_date) ? date('d-M-Y', strtotime($otherLicence->issue_date)) : '') }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                {{--List of Directors--}}
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <div class="pull-left"><strong>List of directors and high authorities</strong></div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">Information of (Chairman/ Managing Director/ Or Equivalent):</legend>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="col-md-5 col-xs-6">
                                                    <span class="v_label">Full Name</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7 col-xs-6">
                                                    {{ (!empty($appInfo->g_full_name)) ? $appInfo->g_full_name :''  }}
                                                </div>
                                            </div>
                                            <div class="col-md-6" style="border: none !important;">
                                                <div class="col-md-5 col-xs-6">
                                                    <span class="v_label">Position/ Designation</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7 col-xs-6">
                                                    {{ (!empty($appInfo->g_designation)) ? $appInfo->g_designation :''  }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="col-md-5 col-xs-6">
                                                    <span class="v_label">Signature</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7 col-xs-6">
                                                    @if(!empty($appInfo->g_signature))
                                                        <img src="{{ url('uploads/'.$appInfo->g_signature) }}" class="img-responsive">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">List of directors</legend>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" cellspacing="0" width="100%">
                                            <thead>
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th class="text-center">Name</th>
                                                <th class="text-center">Designation</th>
                                                <th class="text-center">Nationality</th>
                                                <th class="text-center" colspan="2">NID/PassportNo.</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(count($listOfDirectors) > 0)
                                                <?php $i = 1; ?>
                                                @foreach($listOfDirectors as $director)
                                                    <tr>
                                                        <td>{{ $i++ }}</td>
                                                        <td>{{ $director->l_director_name }}</td>
                                                        <td>{{ $director->l_director_designation }}</td>
                                                        <td>{{ $director->nationality }}</td>
                                                        <td>{{ $director->nid_etin_passport }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>

                {{--Attachment--}}
                <div class="panel panel-info">
                    <div class="panel-heading"><strong>Attachments</strong></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped table-bordered table-hover ">
                                    <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th colspan="6">Required attachments</th>
                                        <th colspan="2">
                                            @if(count($document) > 0)
                                                <a class="btn btn-xs btn-primary" target="_blank" href="{{ url('process/open-attachment/'.Encryption::encodeId($appInfo->process_type_id).'/'.Encryption::encodeId($appInfo->id)) }}"><i class="fa fa-link" aria-hidden="true"></i> Open all</a>
                                            @endif
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 1; ?>
                                    @if(count($document) > 0)
                                        @foreach($document as $row)
                                            <tr>
                                                <td>
                                                    <div align="left">{!! $i !!}<?php echo $row->doc_priority == "1" ? "<span class='required-star'></span>" : ""; ?></div>
                                                </td>
                                                <td colspan="6">{!!  $row->doc_name !!}</td>
                                                <td colspan="2">
                                                    @if(!empty($row->doc_file_path))

                                                        <div class="save_file">
                                                            <a target="_blank" class="btn btn-xs btn-primary" title=""
                                                               href="{{ URL::to('/uploads/'.(!empty($row->doc_file_path) ? $row->doc_file_path : '')) }}">
                                                                <i class="fa fa-file-pdf" aria-hidden="true"></i> Open
                                                                File</a>
                                                        </div>
                                                    @else
                                                        No file found
                                                    @endif
                                                </td>
                                            </tr>
                                            <?php $i++; ?>
                                        @endforeach
                                    @else
                                        <tr class="text-center">
                                            <td colspan="9"> No required documents!</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{--Declaration and undertaking--}}
                <div class="mb0 panel panel-info">
                    <div class="panel-heading"><strong>Declaration and undertaking</strong></div>
                    <div class="panel-body">
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">Authorized person of the organization</legend>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-5 col-xs-6">
                                                    <span class="v_label">Full Name</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7 col-xs-6">
                                                    {{ (!empty($appInfo->auth_full_name)) ? $appInfo->auth_full_name : '' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-5 col-xs-6">
                                                    <span class="v_label">Designation</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7 col-xs-6">
                                                    {{ (!empty($appInfo->auth_designation)) ? $appInfo->auth_designation : '' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-5 col-xs-6">
                                                    <span class="v_label">Mobile No.</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7 col-xs-6">
                                                    {{ (!empty($appInfo->auth_mobile_no)) ? $appInfo->auth_mobile_no : '' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-5 col-xs-6">
                                                    <span class="v_label">Email address</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7 col-xs-6">
                                                    {{ (!empty($appInfo->auth_email)) ? $appInfo->auth_email : '' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-5 col-xs-6">
                                                    <span class="v_label">Picture</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7 col-xs-6">
                                                    <img class="img-thumbnail"
                                                         src="{{ (!empty($appInfo->auth_image) ? url('users/upload/'.$appInfo->auth_image) : url('assets/images/photo_default.png')) }}"
                                                         alt="User Photo" width="100px">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-5 col-xs-6">
                                                    <span class="v_label">Date</span>
                                                    <span class="pull-right">&#58;</span>
                                                </div>
                                                <div class="col-md-7 col-xs-6">
                                                    {{ (!empty($appInfo->created_at) ? date('d-M-Y', strtotime($appInfo->created_at)) : '') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <div class="row">
                            <div class="col-md-12">
                                <div>
                                    <img style="width: 10px; height: auto;"
                                         src="{{ asset('assets/images/checked.png') }}"/>
                                    I do here by declare that the information given above is true to the best of my
                                    knowledge and I shall be liable for any false information/ statement is given.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<script src="{{ asset("vendor/html2pdf/html2pdf.bundle.js") }}"></script>
<script>

    var is_generate_pdf = false;
    document.getElementById("html2pdf").addEventListener("click", function(e) {
        if (!is_generate_pdf) {
            $('#html2pdf').children().removeClass('fa-download').addClass('fa-spinner fa-pulse');
            generatePDF();
        }
    });

    function generatePDF(){
        var element = $('#applicationForm').html();
        var downloadTime = 'Download time: ' + moment(new Date()).format('DD-MMM-YYYY h:mm a');
        var opt = {
            margin:       [0.80,0.50,0.80,0.50], //top, left, bottom, right
            // filename:     'myfile.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' },
            enableLinks:  true,
        };

        var html = '<div style="margin-top: 60px">' + element + '</div>';

        html2pdf().from(html).set(opt).toPdf().get('pdf').then(function (pdf) {
            var pageCount = pdf.internal.getNumberOfPages();

            pdf.setPage(1);
            pageWidth = pdf.internal.pageSize.getWidth();
            pageHeight = pdf.internal.pageSize.getHeight();

            var image = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKIAAAA2CAYAAABEBUJOAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAFjFJREFUeNrsXQlcVFXb/88MM8POiGzK4uAGuAEmSoZ7Si6fYGpuFZKWWhZimi0aaraYmVqZfpoab5mZqViZZZq4g6UgaioqIqAgm6zDDLPc99yZMzri7IDyvu88/u5vLvdsz73P/z7bOfcI2MhGNrKRjWxkIxvZyEY2spGNbGQj/cRpro4XfrvW60DOhaElNZXd+Bxue5lC4SXgctVl9SoVhHZ2xXJGlePp7HY+ukPX40umvJJrE4cNiE1Cy3ds8vgu6+QUO+CZcHHnyACRB/fo5bNoI2qN2IiBGNOr3926FbXVyC0twrn8HKRfv4SsG1ezK6WSneH+HZKTZ75z2SYaGxAtpvh1y/zT866+3d1XHDcpcohDdPfesBcIkHzkV2TcuIrO3r4oq6lCgIcPnusXDS6H+0AfDMPg5JXz+C7tIHM0O2tfB3fvpbtf/yjdJqL/DeI1pnHq2XRhpgezUOTs+v0nE2c+/uqwsfzgtgGQyOqwN/MkaqR18HRxw983stGnYxf8dDYNI3r0gcCOf7eP/LLb+JVcb+PmjqC27TAiNJIzOvyJTpeK8qdJQnw6T4h/9kT6nv01NlHZNKJemvJ5Uter5cXfvTliYo/YXv3V17LyruLIxUy4ObkgunsE9mScwO8EZOcL85C64FMs2rkJG6cveKCv4ooyvPnDBvi28kCQjz+e7ffU3f7e2rGxTKaQTz/45qqUxt5swKRth5rgmR0mRyb7HuZtm1RhYryp5CfORH/JpJ+vG7QLIz+rrOCN9bNvmMufBc+N5SXMjKpjrB3TzppGQz5MjK0H882ehGXOrg5OKCgrxo70Qwht1xEnrv0DEoDAi2jC+H7D8TMB49xhY3HoYgYYPX1N/nIpXOwdkHnzOj57LgEDlifC38MbA0LC0SOgI3YnLGudtHPzrurFM99PS1r3LpfDYRrxTAc2gVy0fVQQAa0hD36xkbpiM8Y8rOeaqCl4JfyxLy/LY2oj+mB5mWPBs7FKYXAtbdB/6SvxXX3FP26ducjZm5jTz3/bgTlbP8eEx4fgRkkh+geFYvrAUfiFmObko/vQw1cMF74Q/xQVoG+nbqgkQYouJcXGwd3JFSOJyb5ENOek3oNQeKcMSTs24nZludqMfzhhBuelgaMW9l40fUOtTMpFyyBWQElEUBkt2OLFkuMQawnIIX4IL2+MtYxaJNSoJS9P6NOx61ern31V7Vuu2rcd/YJD0c2/g1rj9QsOw5nrl/H6trVgzXUw8flKSJBSUS+FmJjdFwaMVJttXWL9Qi6Xi7gnokmgcgCTIgfDSWgPFQloSivvYOXPW4mvKcE00nb+yMnTB7w3+7MWJuwwarpaMrFgyqAm31KKsRD4VpHZpnnEx/Oj2rb2+tdHE2dw6xVyrNi7DX6tPLGFaL0N097A3K1fwN/dExumv3EvEibBSu8QCZQXLkNZUICavWkax9TZCTw/P/C6BsEuvAcWxjwPB4EQId7+IP4grhbfwriIAdidcRzl1RVYu38XXhoSg2cISMtqq1/h8fm56UnrPmlBgp5DzXRuCwajiGrHQYTPzGYCl4j0H0v6T2kWIL761QqPqxUl29dMmS1giIf2XkoyimqrIFcp0crRBcezz2H5hJlII/4hS6rycsi+3Q7Zj3vB1MmMR0sOQvCf7Afl5HF4cYjm5RPY2aGaaMG0KxcQ6O0LCQF0/IYPse2VdzGL1LlSlP+R20dz0/e/+enRFiToJDaT1cI1IwvG3QQs4eYEFVSDiqzQoBYD0SzTfDLvyhcrJ85q60hM5qZDP6ELMachHm1IQCIiGqoKpVV3IK2XoV/HrpB+uw1VY+Ig/WaXSRCqtSapU//zAVRNmQXJso/BVFdjLNGGUUE98Mu85XiBRNAnci8ToM9A3IaPkF9ahGXjpvMYDif5XwdSHJtYUGz0uqTBsZoNTB5SIPQwiPUVt5hZN86K/q0yzyaBSEzyUAKMCSEk6NhyZB/2XTiNiPbBYKPlzPwcNu+HGOIPOtfLUT17PurWbAbHxYoIXsVAtucPAsgZ8Lxdqr7Ezrqs3r8Tu197D9vT/kRnH3+kX7sI9oVYFPNc4KepP7/dxEJiUymLGxyJ5HogTY2YEvDDJDY9w9Ee5O9w9pq5YCHabmAzvVwia3xRo0BUqVQcqVK+PGHYWMjkctwqL4FEJsWKfT+gWFKNz56djaeJ9lKVlqL6xTlQ/H1OY+87VYH/eCl4nSzPQ6sKS1D90jwoMjLh5SrC+qlzsXjXZni7tUIn77ZIOX0MxUQD9yPRee/A4LkJm1a0aW6JUzOW3JLVHOv3kWOQBWYxwYRZFpuZO2wSTco1rg3njRjfe1A4G0gczT6rNr8q4heufX4OREJHvLX9/6GSSFCT8BaUuTc1IAyqgl1IOfjdSyHoVQyujxT8qBIykvnpP0YiRc3cJHiUV6r9w17iIEjI2JGBIRjaPQJrD+zW+K5Dn3ZIzbmY+JBk7WaiPLOFYDLeTFci1kRKx5iJNWUdYpsUiOVSyazn+j6pBqCnkyuulhZiTvR4FJTdxoGLZ7Dq2ddQ98lnUGbr8OWgJEjSTNhwXOphPzoX/C5l4DiqLGKMqZGgdsFSuBMzrGSU6rnoID8xpvQdijqilW/dKUU3//bo7hc47dg/GcLmlCxN6k41UW1PC9GMFdTXbaw/Zyxts8SUm2KpeTYYNS/f8ZVXsVz2lAMBwrLdycgngp/WfwSC2wRgR/qfeH/cdGKKT5NA4+B97RSZrQAJD/zIYnDsFfeiYyEBU41lU9vK6wWQfv0doiePxbHsLHV0/uEvW9Vg3JtxHC8OjsGo8L7u7+zcNIpU39kEcozT4zu5URAaix4raFDTUoh1I8yZDRmgj2/64g00ovlTzAh44iyxEgY14g9Z6aOiu0fw2JUyfh7e8Cb+2qlr/6jni7v4BpJDDOmXm/W2VVxxheynAOLw3ZvK5npLAYGOeWZNtRkz3bKtu9BaqULsY/3gRFwEP3cvpJw5pjbVLA3v3gfldbWjm0iAU2kaRveYYwYIBzXVvG5T+YsWRNCWaso99F5NBUYDm0Qj2nG5Mex0HUvXigqIeVShuKYSW156S31NfvwEFOevPIjsVnLwI0rAC6i+zy8URN2CoC8XjIynNt0cezmYenKu5KrPVaUOqE/3hKrI4YH0jvRf2+Hw2kzklBRieNdecCeBiwPPDn/lXCQRfAgJWoJGnZLXcx34AtVDljkrjEQLE8QPkzdTYAgzoimN9cvSYRP9szNOYnOT/AaB2MO/Q4SQL8DPRPuMDo2Eh2srtPfxu1te/9ufetsJhtwC173OgP5VgeNwDyu6plt50wmqYge9zep/PwSHV2diUEg4Wjm7Yvqmj4l2tMf43prn1blNgPvSrV92IqcPc0GtOufYwmdTrCVDGrFCZwFFCrUYpvoxy2XRa5rTLp4VuTk5t6mXy1FSVYGFKcn4/GAKZm5egd2nNItFFGey9Jvl8yKN1rPIGeRCmeNCTLmBlE5xOVQFeWoQstTayQXnbubiTN5VDRC9/XDgxuXghyws1oxfJ2/9FupT/VcQ9ZFFJrSh1vybckfMTuPoBeKuEwd8/dw9IeDzUSWrw+Q+g8CoVJg9bBzGEC3EEHCqiu/oB+IlN0i/7wBlvot5ecNiR8j+9IWqXGAcq1evq39vk6CpZ2AQegZ0QHZhPnKLb8G/tRd4SqXvI5KdFpDi/xIsGouWGy5ZM5WzDDP3ueg1zel52e7xHTUKhp1LXrX/R2yOn8f6jRrwlJYaT73IST0zlg1Kf2kHVSExx2akGFVlZerpvfWH9yKQBCxPdo/AzdLbEHu1hbIwD3ckNa2bQAj63nIRTCd21XO40MxutBQaaOb9WhKosFmFGDOCHYvNs14g3pTV8fhcjXmN6/cUBoeEqSNnjjbMlSmMdspxVoDnV2MiTyiA6pbGJ+T5S8BxkxOzbjhnzMjk8Pfwwftjpz1QxvJapVTwmkB4ifoWkdKc2CET0XOYtStPmsG8mpvDy23QTmwCXNbMtAwwB4h6TbOv0EHJrqzREgsAjm6uRWh80Q5TZQdFtnG3ieMoV4OP5cAutAz88FKj6RyOkG+wjOXVlWenbOZ0SKKZD70lkLm+2WELtKHVgY85PrReIPZp17msQiox3MjDw3T8ke9sYmQGgqjbJHJWgde2Vp3CMTYNyG1teMwKaR1xIZzLmlm45kTHYY8agVSrTTWzeoq1wUUTReHGgRgbOfhmQXmJYe3kKgLXq5VJraj3uoSvDlAYOU8NQOHoG7RTwoyX4WVjvE6GLUZ+WTGUPN7NFuBzPWoQan1Vc6L4VN3UE23bXC9SjFVA7NslvLKytqbwPgDV3Z8btOsZahyIBlw2RiJQjyrP8IDsgB85uWeP7TpW6WfSyx1c3wCDvGTfLsBQcdCl5hIuOeaYkTMDzFts0FwgjKV+rLlgWvIQzLLZ5tmgs5eVf+20UqUaxaORsuwH9kVTQRgzAhyROwRD+qH+t1TjGpHBPb9PSfrhqYhvWK9J1SjYWRYu5Gc9IBxcoAGcu1RvX/whUZp+iC8o25UC5dUbcHxz7t3ynOKblaufS7jyQdycxj4wdil9Y9qffZhRMeHV2i8aU/QEZTHNza+xdI9BINYp6g9l5GaP6tU+WJ1Mlh9LJ5FuFYSTxmvAEfU4uD4eUBUZSOUoOVDedgTPR0K1mB04DgrIz7VWJ65Vd2jUTPDJ9BaQSLseHCc90TiXA+HTo+g5D4qL2VCcyoL8r7/Aj4iAXKFgd5M48gim98zxuVoisVo73kLXI9zYNCb9ftvUIgijnxAYXPQQEdDp11+zNDt+cP0C4DAjDi5fryOajILFjg/7aZOMm+cKIZQlTlCRX/lpT8gvtILirAiKcyKobtLpPAIf5W16Lnww8BUM6w+eOJCCWwnHxNlwXDRXDUKW2L115CrV3hYg4NQWOufcEIQPLNCgZt2Q6aww475SGxuwGATiphlvXzqefe4MMc8a3PXqCY7QnphWp3tZnNEjYdcjyHBa5XRr1P/uC+kuMRSXXSBP89TPBNGG6nilwYocjqsTHF6boROx8IjmdAG/d8Q9FXTmeP3kHpE7W4CQE/9DQJhpYdrJJMho0GMqqyCigLcMiCyVS6rX7zub1kDN6fwSU+m09C1w3PSnaphaOxKckCEUxtd7yc94Qpnngvq/PYl5Vt41yU7vzgXX0/P+MXWI/QD/aHbWjvnjp5U+YiHHt3BtyJrEQCM8Gl321YRuSYxVQHx/9PPfbj68t5BdHa0lVWEB6r5Yj/o/9ms68PWF88olRFPaW/2UlMRXVBY6EbPdSrN4luDWMfFF8Ado9tRR5lxB3ZcbID9y5L52aw/sZrr5+K98lOaYapmvWzAAWf4M7klDZ2HEjTS7LB1ujHk2OkUS/VhU3RNLX16641Tqumf6DFJfU1y6TCLXfcQsDyG3SPxbvgB2oT3g/OVy1M5LgqrUigwGCWzUc87qKIgHxwWzSXQ+6l4xO+YPvwATGRIk9SUm2g7XiwuxL+vUD38v3ZBhIXAaa97OUjOUasYSsFwzxsw1ME5qI/izZBMmsZGxKsxd5sZObRJQm+TZ0BpFk2uki+6U8catTTr1U8L7PbXLsOSpqeAPfDDIYhdDSD78lGiuv6ySMq+9HxyT5sOuS5cHzbfOmAz59/z6D2rsOJwuX898Jx82+o8ns7alG7/qnVCRq1v6hhfmm/WRkvxkGqQbvyHRcbZZTHD9fWA/ZSzRgiPV0bgpWn9wD7ae/GPGsUVrN9hE+D9GUUtfmbHy1+2M2aRSMfKMDEaydgNT9fLrTMX/TWDuDBypPthz9hpbxtZh65pLRy9nMeELp2215h5Yf0ib4dc9t7CPRu+x2FI2baLPYFVT8szmFK35wN6ijTp7v/vS6sSnnkmY+PgQg3VulhWrV3NP7z8cHX38jfbHfqZqLxCirLoSe84cw3jih7rYG95F5EJBDqZtWnFiVt/ooVOHjZFY8eCnkh92bnINOQ4RXyVQ67NQUIqpzybW8bkqtL4WXVDArtANbOCXQbcO7S+sof+nUydMZ5xc9jq7Mpqd7dApE2l9KZ0+dcdnzzNpW7GBunf5g2b6b402sNL5Ui+B/TDfQB/s82I/xB9Ex6vQ8R0b3q82ob1EW66PF0N+q0VAlNTLOFGLZ62bM/yZGc9FReuto1Aq8cb36zAwOAw/ZRxHn/Yh8PXwhriVp3pDTjYFUy2rQ15FKb4/eRCRgcHEGvPx9bHfcHThFwYZOpObjdnJq/4K8w4Yum7W25WN0AIZ1Dk/Sx8uC8wwmgdMoA8ySUeI8fShHqIAmUrrsKkI9rPNOFqPbb+bni+hfeVSYbPBQxgLfB2tytbZQstyKQ/xFDDJlC8RfWmS6Lm2zSBabzWNRLV5vBs67Q7T/KCI9sfyo95ShYIwg46tratvvFTK/yA6rm6f2nq696uNnlNpmfZl0223RN+aTYv2R3QUCJlTyzbO+uyPXR+zO4Jpk9269HtWGpQEbewmnf6tvfHCwJHIu30LC3ZsxD+3C5B8Yj8mbvwApXfK1GnBEWGROHPjCh5r1xlpV87rT2SdPorpmz85MDiwS6NASIl9gLFUMwygwk+B/sUCa3TeYFZDxTfIr4noIaZHshZ0tE4yFcYaA9GxtiyM9pdAhTiA9hem1cq62kw7T0z35RHTrUbEDdppQaHlUzeCD6P3vEYnof3AeFr+G0S52j5DG9zv4QYpHO3zEjVoF2Zx+kZvAy6Pxc8Cp2XCrKyCnPWfTJjp3M7z3vYzLg7OmNR7MCqltXB3dEb/Za9hfK/+6OIXiOKKchRWliOsbSAy869hWNfH2JkRlNZWIcjLF9vS/8Tjnbrd7atWWofFu7cwBy9mfLptxsI3g/3EiiZwjXJ1QKHVcGH0wcfA8OoVkZ6P7yuoIJJ1BHrYSr5SqJZLpFpWu1e3FuAJZPxMHRfBUPrmcIOXoWGqRlsvjGrQhu3EOhpURO9bny8dpid/2E6nT5GJD7Gs14j3ZS8Xfr7VgWcXPuazd//8eO829f8kwFL/4FBEduqKW8RXjOjQBa8MicHFojx08PDBxVs3ENDai/iGUnB5PGK+Q1FUfQfzop+Br7sXng5/QpMGYhjsOHUIw1e+ce1c/rXojPe+mtdEINSCTwucRK2ZoeZijU55cgMtFq9jwlNpnm41FYSYCnEP/c3UaZ/cYEzoK6NaZw3tW6t5RRSgWh7ZsjF0vCU6Ppm2T912qQ2ORB1fVctfhZ52uuMtpuVa867b35oG97uagjCV3tMYatbH6OGleWjwBwnjIpe8fGH5L1uZojul6ui2TiZV/y7cvp45mX2OOXLprPrvGqmEWfTjV3ejYLZMqVSq69eSsm+O/c4MW/56yWOLXpz/08mD9rCRLY9oCdUrFNzYlQueyq0ofbGHX/vhg7v0FA4N6Yl23m0NDsLa+FziP6bl/INDFzOZjBvZaRwOd/NbQ8dufTpqWJ1NPDYgNop+PLrfZeXBXdHlUskwIZfXrYNX2/ZCgdCLz+Gqx5MzKkZWLyu+VnwrR6ZSnncVOpwYFRT6R9Lkl2/aRGIjG9nIRjaykY1s9Mjp3wIMAKmroILpKWZSAAAAAElFTkSuQmCC";
            pdf.addImage(image, 'PNG', pageWidth / 2 - 0.60, 0.50, 1.20, 0.40);

            pdf.setFontSize(14);
            pdf.text("Bangladesh Investment Development Authority (BIDA)", 1.80, 1.20);

            pdf.setFontType("italic");
            pdf.setFontSize(8);
            pdf.setTextColor(32, 32, 32);

            for (let j = 1; j < pageCount + 1 ; j++) {
                pdf.setPage(j);
                pdf.text(`${j} / ${pageCount}`, pageWidth - 1, pageHeight - 0.50);
                pdf.text(downloadTime, 0.60, pageHeight - 0.50);
            }

            //generated url
            var url = pdf.output('bloburl');
            $('#html2pdf').children().removeClass('fa-spinner fa-pulse').addClass('fa-download');
            $('#html2pdf').attr({href: url, target: "_blank"});
            is_generate_pdf = true;
            window.open(url, '_blank');
        });
    }

    function openModal(btn) {
        var this_action = btn.getAttribute('data-action');

        if(this_action != ''){
            $('#IRCModal .load_modal').html('');
            $.get(this_action, function(data, success) {
                if(success === 'success'){
                    $('#IRCModal .load_modal').html(data);
                }else{
                    $('#IRCModal .load_modal').html('Unknown Error!');
                }
                $('#IRCModal').modal('show', {backdrop: 'static'});
            });
        }
    }
</script>

