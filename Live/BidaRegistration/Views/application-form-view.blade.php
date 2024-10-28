<?php
$accessMode = ACL::getAccsessRight('BidaRegistration');
if (!ACL::isAllowed($accessMode, $mode)) {
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

    .previous-applications tbody tr {
        text-align: center;
        vertical-align: middle;
    }

    .previous-applications thead tr {
        text-align: center;
        vertical-align: middle;
    }

    .mb5 {
        margin-bottom: 5px;
    }

    .mb0 {
        margin-bottom: 0;
    }
</style>
<section class="content" id="applicationForm">

    @if(in_array($appInfo->status_id,[5,6,17,22]))
        @include('ProcessPath::remarks-modal')
    @endif

    <div class="col-md-12">

        {{-- Start if this is applicant user and status is 15, 32 (proceed for payment) --}}
        @if(in_array(Auth::user()->user_type,['5x505']) && in_array($appInfo->status_id, [15, 32]))
            @include('ProcessPath::government-payment-information')
        @endif
        {{-- End if this is applicant user and status is 15, 32 (proceed for payment) --}}

        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="pull-left">
                    <strong style="line-height: 30px;">
                        Application for Industrial Project Registration to Bangladesh
                    </strong>
                </div>
                <div class="pull-right">
                    @if (isset($appInfo) && in_array(Auth::user()->user_type, ['1x101','2x202', '4x404']))
                        <a class="btn btn-sm btn-primary" data-toggle="collapse" href="#previousApplications" role="button"  aria-expanded="false" aria-controls="collapseExample">
                            Previous Applications
                        </a>
                    @endif
                    @if (isset($appInfo) && $appInfo->status_id == 25)
                        <a href="/bida-registration/directors-machinery-pdf/{{ Encryption::encodeId($appInfo->id) }}"
                           class="btn show-in-view btn-sm btn-info"
                           title="List of director & machinery" target="_blank"> <i
                                    class="fa  fa-file-pdf-o"></i> List of director & machinery</a>
                    @endif

                    @if (isset($appInfo) && $appInfo->status_id == 25 && isset($appInfo->certificate_link) && in_array(Auth::user()->user_type, ['1x101','2x202', '4x404', '5x505']))
                        <a href="{{ url($appInfo->certificate_link) }}" class="btn show-in-view btn-sm btn-info"
                           title="Download Approval Copy" target="_blank">
                            <i class="fa  fa-file-pdf-o"></i>
                            Download Approval Copy
                        </a>
                    @endif

                    <a class="btn btn-sm btn-success" data-toggle="collapse" href="#paymentInfo" role="button"
                       aria-expanded="false" aria-controls="collapseExample">
                        <i class="far fa-money-bill-alt"></i>
                        Payment Info
                    </a>

                    @if(!in_array($appInfo->status_id,[-1,5,6]))
                        <a href="/bida-registration/app-pdf/{{ Encryption::encodeId($appInfo->id)}}"
                           target="_blank"
                           class="btn btn-danger btn-sm">
                            <i class="fa fa-download"></i>
                            Application Download as PDF
                        </a>
                    @endif

                    @if(in_array($appInfo->status_id,[5,6,17,22]))
                        <a data-toggle="modal" data-target="#remarksModal">
                            {!! Form::button('<i class="fa fa-eye"></i> Reason of '.$appInfo->status_name.'', array('type' => 'button', 'class' => 'btn btn-sm btn-danger')) !!}
                        </a>
                    @endif
                </div>

            </div>
            <div class="panel-body">

                <ol class="breadcrumb">
                    <li><strong>Tracking no. : </strong>{{ $appInfo->tracking_no  }}</li>
                    <li><strong> Date of Submission: </strong> {{ \App\Libraries\CommonFunction::formateDate($appInfo->submitted_at) }}
                    </li>
                    <li><strong>Current Status : </strong> {{ $appInfo->status_name }}</li>
                    <li><strong>Current Desk
                            :</strong> {{ $appInfo->desk_id != 0 ? \App\Libraries\CommonFunction::getDeskName($appInfo->desk_id) : 'Applicant' }}
                    </li>
                </ol>

                {{--Payment information--}}
                @include('ProcessPath::payment-information')

                {{--Prevous Applications--}}
                @include('BidaRegistration::prevous-applications')

                {{-- <fieldset class="scheduler-border">
                    <legend class="scheduler-border">Please specify your desired office:</legend>
                    <h4>You have selected <strong>'{{$appInfo->divisional_office_name}} '</strong>, {{ $appInfo->divisional_office_address }}.</h4>
                </fieldset> --}}

                {{--Company basic information--}}
                <div class="panel panel-info">
                    <div class="panel-heading">
                        @if($appInfo->company_info_review == 1)
                            <img src="{{ url('assets/images/new-icon-gif-8.jpg') }}" height="50px">
                        @endif
                        <strong>Company Information</strong>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">Name of organization/ company/ industrial project (English)</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($appInfo->company_name)) ? $appInfo->company_name : '' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">Name of organization/ company/ industrial project (Bangla)</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($appInfo->company_name_bn)) ? $appInfo->company_name_bn : '' }}
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
                            <legend class="scheduler-border"><strong>Info. based on your business class (Code
                                    = {{ (!empty($appInfo->class_code)) ? $appInfo->class_code :''  }})</strong>
                            </legend>
                            <table class="table table-striped table-bordered dt-responsive" cellspacing="0"
                                   width="100%">
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
                                {{ (!empty($appInfo->major_activities)) ? $appInfo->major_activities : '' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{--Information of Principal Promoter/Chairman/Managing Director/CEO/Country Manager--}}
                <div class="panel panel-info">
                    <div class="panel-heading">
                        @if($appInfo->promoter_info_review == 1)
                            <img src="{{ url('assets/images/new-icon-gif-8.jpg') }}" height="50px">
                        @endif
                        <strong>Information of Principal Promoter/ Chairman/ Managing
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
                                        {{ (!empty($appInfo->ceo_country_name)) ? $appInfo->ceo_country_name :''  }}
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
                                        {{ (!empty($appInfo->ceo_dob)) ? date('d-M-Y', strtotime($appInfo->ceo_dob)):''  }}
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
                                        <span class="v_label">Telephone No.</span>
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
                                        <span class="v_label">Mobile No.</span>
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
                                        <span class="v_label">Fax No.</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-7 col-xs-6">
                                        {{ (!empty($appInfo->ceo_fax_no)) ? $appInfo->ceo_fax_no : '' }}
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
                    <div class="panel-heading">
                        @if($appInfo->office_address_review == 1)
                            <img src="{{ url('assets/images/new-icon-gif-8.jpg') }}" height="50px">
                        @endif
                        <strong>Office Address</strong></div>
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
                                        <span class="v_label">Telephone No.</span>
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
                                        <span class="v_label">Mobile No.</span>
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
                                        <span class="v_label">Fax No.</span>
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
                    <div class="panel-heading">
                        @if($appInfo->factory_address_review == 1)
                            <img src="{{ url('assets/images/new-icon-gif-8.jpg') }}" height="50px">
                        @endif
                        <strong>Factory Address</strong></div>
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
                                            {{ (!empty($appInfo->factory_thana_name)) ? $appInfo->factory_thana_name :''  }}
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
                                            <span class="v_label">Telephone No.</span>
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
                                            <span class="v_label">Mobile No.</span>
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
                                            <span class="v_label">Fax No.</span>
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

                {{-- Desired office --}}
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border">Please specify your desired office:</legend>
                    <h4>You have selected <strong>'{{$appInfo->divisional_office_name}} '</strong>, {{ $appInfo->divisional_office_address }}.</h4>
                </fieldset>

                {{--Registration Information--}}
                <div class="panel panel-info">
                    <div class="panel-heading"><strong>Registration Information</strong></div>
                    <div class="panel-body">

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">
                                @if($appInfo->project_status_review == 1)
                                    <img src="{{ url('assets/images/new-icon-gif-8.jpg') }}" height="50px">
                                @endif
                                1. Project status</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3 col-xs-6">
                                        <span class="v_label">Project status</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-9 col-xs-6">
                                        {{ (!empty($appInfo->project_status_name)) ? $appInfo->project_status_name :''  }}
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">
                                @if($appInfo->production_capacity_review == 1)
                                    <img src="{{ url('assets/images/new-icon-gif-8.jpg') }}" height="50px">
                                @endif
                                2. Annual production capacity</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <label class="col-md-12 text-left"></label>
                                            <table class="table table-striped table-bordered dt-responsive"
                                                   cellspacing="0" width="100%">
                                                <thead>
                                                <tr>
                                                    <th valign="top" class="text-center valigh-middle">Name of Product
                                                    </th>
                                                    <th valign="top" class="text-center valigh-middle">Unit of Quantity</th>
                                                    <th valign="top" class="text-center valigh-middle">Quantity</th>
                                                    <th valign="top" class="text-center valigh-middle">Price (USD)</th>
                                                    <th colspan='2' valign="top" class="text-center valigh-middle">Sales Value in BDT (million)</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if(count($la_annual_production_capacity)>0)
                                                    @foreach($la_annual_production_capacity as $value1)
                                                        <tr>
                                                            <td width="30%">{{ (!empty($value1->product_name)) ? $value1->product_name : ''  }}</td>
                                                            <td width="20%">{{ (!empty($value1->unit_name)) ? $value1->unit_name : ''  }}</td>
                                                            <td width="10%">{{ (!empty($value1->quantity)) ? $value1->quantity : ''  }}</td>
                                                            <td width="10%">{{ (!empty($value1->price_usd)) ? $value1->price_usd : ''  }}</td>
                                                            <td width="20%"
                                                                colspan='2'>{{ (!empty($value1->price_taka)) ? CommonFunction::convertToMillionAmount($value1->price_taka) : ''  }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">
                                @if($appInfo->commercial_operation_review == 1)
                                    <img src="{{ url('assets/images/new-icon-gif-8.jpg') }}" height="50px">
                                @endif
                                3. Date of commercial operation</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3 col-xs-6">
                                        <span class="v_label">Date of commercial operation</span>
                                        <span class="pull-right">&#58;</span>
                                    </div>
                                    <div class="col-md-9 col-xs-6">
                                        {{ (!empty($appInfo->commercial_operation_date)) ? date('d-M-Y',strtotime($appInfo->commercial_operation_date)) :''  }}
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">
                                @if($appInfo->sales_info_review == 1)
                                    <img src="{{ url('assets/images/new-icon-gif-8.jpg') }}" height="50px">
                                @endif
                                4. Sales (in 100%)</legend>
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

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">
                                @if($appInfo->manpower_review == 1)
                                    <img src="{{ url('assets/images/new-icon-gif-8.jpg') }}" height="50px">
                                @endif
                                5. Manpower of the organization</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                            <thead>
                                            <tr>
                                                <td class="text-center" style="padding: 5px;" colspan="3">Local (a)</td>
                                                <td class="text-center" style="padding: 5px;" colspan="3">Foreign (b)
                                                </td>
                                                <td class="text-center" style="padding: 5px;">Grand Total</td>
                                                <td class="text-center" style="padding: 5px;" colspan="2">Ratio</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td class="text-center" style="padding: 5px;">Executive</td>
                                                <td class="text-center" style="padding: 5px;">Supporting Staff</td>
                                                <td class="text-center" style="padding: 5px;">Total</td>
                                                <td class="text-center" style="padding: 5px;">Executive</td>
                                                <td class="text-center" style="padding: 5px;">Supporting Staff</td>
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
                            <legend class="scheduler-border">
                                @if($appInfo->investment_review == 1)
                                    <img src="{{ url('assets/images/new-icon-gif-8.jpg') }}" height="50px">
                                @endif
                                6. Investment</legend>
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
                                                    <td>{{ (!empty($appInfo->local_land_ivst_ccy_code) ? $appInfo->local_land_ivst_ccy_code : '') }}</td>

                                                </tr>
                                                <tr>
                                                    <td> &nbsp;&nbsp;&nbsp;&nbsp; Building (Million)</td>
                                                    <td>{{(!empty($appInfo->local_building_ivst) ? $appInfo->local_building_ivst : '')}}</td>
                                                    <td>{{ (!empty($appInfo->local_building_ivst_ccy_code) ? $appInfo->local_building_ivst_ccy_code : '') }}</td>

                                                </tr>
                                                <tr>
                                                    <td> &nbsp;&nbsp;&nbsp;&nbsp; Machinery & Equipment (Million)</td>
                                                    <td>{{ (!empty($appInfo->local_machinery_ivst) ? $appInfo->local_machinery_ivst : '') }}</td>
                                                    <td>{{ (!empty($appInfo->local_machinery_ivst_ccy_code) ? $appInfo->local_machinery_ivst_ccy_code : '') }}</td>
                                                </tr>
                                                <tr>
                                                    <td> &nbsp;&nbsp;&nbsp;&nbsp; Others (Million)</td>
                                                    <td>{{(!empty($appInfo->local_others_ivst) ? $appInfo->local_others_ivst : '')}}</td>
                                                    <td>{{ (!empty($appInfo->local_machinery_ivst_ccy_code) ? $appInfo->local_machinery_ivst_ccy_code : '') }}</td>

                                                </tr>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp; Working Capital (Three Months)
                                                        (Million)
                                                    </td>
                                                    <td>{{ (!empty($appInfo->local_wc_ivst) ? $appInfo->local_wc_ivst : '') }}</td>
                                                    <td>{{ (!empty($appInfo->local_wc_ivst_ccy_code) ? $appInfo->local_wc_ivst_ccy_code : '') }}</td>
                                                </tr>
                                                <tr>
                                                    <td> &nbsp;&nbsp;&nbsp;&nbsp; Total Investment (Million) (BDT)</td>
                                                    <td>
                                                        {{ (!empty($appInfo->total_fixed_ivst_million) ? CommonFunction::convertToMillionAmount($appInfo->total_fixed_ivst_million) : '') }}
                                                    </td>
                                                    <td>
                                                        @if(!empty($appInfo->project_profile_attachment))
                                                            Project Profile:&nbsp;&nbsp;
                                                            <a target="_blank" class="btn btn-xs btn-primary" href="{{URL::to('/uploads/'.$appInfo->project_profile_attachment)}}" title="{{$appInfo->project_profile_attachment}}">
                                                                <i class="fa fa-file-pdf" aria-hidden="true"></i>
                                                                Open File
                                                            </a>
                                                        @endif
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
                            <legend class="scheduler-border">
                                @if($appInfo->source_finance_review == 1)
                                    <img src="{{ url('assets/images/new-icon-gif-8.jpg') }}" height="50px">
                                @endif
                                7. Source of Finance</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered" cellspacing="0"
                                                   width="100%">
                                                <tbody>
                                                @if($appInfo->organization_status_name != 'Foreign')
                                                    <tr>
                                                        <td width="50%">Local Equity (Million)</td>
                                                        <td width="50%">{{(!empty($appInfo->finance_src_loc_equity_1) ? $appInfo->finance_src_loc_equity_1 : '')}}</td>
                                                    </tr>
                                                @endif
                                                @if($appInfo->organization_status_name != 'Local')
                                                    <tr>
                                                        <td>Foreign Equity (Million)</td>
                                                        <td>{{ (!empty($appInfo->finance_src_foreign_equity_1) ? $appInfo->finance_src_foreign_equity_1 : '') }}</td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <th>Total Equity (Million)</th>
                                                    <td>{{ (!empty($appInfo->finance_src_loc_total_equity_1) ? CommonFunction::convertToMillionAmount($appInfo->finance_src_loc_total_equity_1) : '') }}</td>
                                                </tr>

                                                <tr>
                                                    <td>Local Loan (Million)</td>
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
                                                    <th>Total Financing Million (Equity + Loan)</th>
                                                    <td>{{ !empty($appInfo->finance_src_loc_total_financing_m) ? CommonFunction::convertToMillionAmount($appInfo->finance_src_loc_total_financing_m) : '' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Total Financing BDT (Equity + Loan)</th>
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
                                                    <td>#</td>
                                                    <td>Country</td>
                                                    <td>Equity Amount</td>
                                                    <td>Loan Amount</td>
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
                            <legend class="scheduler-border">
                                @if($appInfo->utility_service_review == 1)
                                    <img src="{{ url('assets/images/new-icon-gif-8.jpg') }}" height="50px">
                                @endif
                                8. Public utility service</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="checkbox-inline">
                                            @if($appInfo->public_land == 1) <img
                                                    src="{{ asset('assets/images/checked.png') }}" width="10"
                                                    height="10"/> Land @endif
                                        </label>
                                        <label class="checkbox-inline">
                                            @if($appInfo->public_electricity == 1) <img
                                                    src="{{ asset('assets/images/checked.png') }}" width="10"
                                                    height="10"/> Electricity @endif
                                        </label>
                                        <label class="checkbox-inline">
                                            @if($appInfo->public_gas == 1) <img
                                                    src="{{ asset('assets/images/checked.png') }}" width="10"
                                                    height="10"/> Gas @endif
                                        </label>
                                        <label class="checkbox-inline">
                                            @if($appInfo->public_telephone == 1) <img
                                                    src="{{ asset('assets/images/checked.png') }}" width="10"
                                                    height="10"/> Telephone @endif
                                        </label>
                                        <label class="checkbox-inline">
                                            @if($appInfo->public_road == 1) <img
                                                    src="{{ asset('assets/images/checked.png') }}" width="10"
                                                    height="10"/> Road @endif
                                        </label>
                                        <label class="checkbox-inline">
                                            @if($appInfo->public_water == 1) <img
                                                    src="{{ asset('assets/images/checked.png') }}" width="10"
                                                    height="10"/> Water @endif
                                        </label>
                                        <label class="checkbox-inline">
                                            @if($appInfo->public_drainage == 1) <img
                                                    src="{{ asset('assets/images/checked.png') }}" width="10"
                                                    height="10"/> Drainage @endif
                                        </label>
                                        <label class="checkbox-inline">
                                            @if($appInfo->public_others == 1) <img
                                                    src="{{ asset('assets/images/checked.png') }}" width="10"
                                                    height="10"/> Others @endif
                                        </label>
                                    </div>
                                    @if($appInfo->public_others == 1 && !empty($appInfo->public_others_field ))
                                        <div class="col-md-12" style="padding-top: 5px;">
                                            {{ $appInfo->public_others_field }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">
                                @if($appInfo->trade_license_review == 1)
                                    <img src="{{ url('assets/images/new-icon-gif-8.jpg') }}" height="50px">
                                @endif
                                9. Trade licence details</legend>
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
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border">
                                @if($appInfo->tin_review == 1)
                                    <img src="{{ url('assets/images/new-icon-gif-8.jpg') }}" height="50px">
                                @endif
                                10. Tin</legend>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">Tin Number</span>
                                            <span class="pull-right">&#58;</span>
                                        </div>
                                        <div class="col-md-7 col-xs-6">
                                            <span> {{ (!empty($appInfo->tin_number)) ? $appInfo->tin_number  :''  }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        @if($appInfo->organization_status_id == 3)
                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border">
                                    @if($appInfo->machinery_equipment_review == 1)
                                        <img src="{{ url('assets/images/new-icon-gif-8.jpg') }}" height="50px">
                                    @endif
                                    11. Description of machinery and equipment</legend>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <td></td>
                                                    <td>Quantity</td>
                                                    <td>Price (BDT)</td>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td class="v_label">Locally Collected</td>
                                                    <td>
                                                        <span> {{ (!empty($appInfo->machinery_local_qty)) ? $appInfo->machinery_local_qty  :''  }}</span>
                                                    </td>
                                                    <td>
                                                        <span> {{ (!empty($appInfo->machinery_local_price_bdt)) ? $appInfo->machinery_local_price_bdt  :''  }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="v_label">Imported</td>
                                                    <td>
                                                        <span> {{ (!empty($appInfo->imported_qty)) ? $appInfo->imported_qty  :''  }}</span>
                                                    </td>
                                                    <td>
                                                        <span> {{ (!empty($appInfo->imported_qty_price_bdt)) ? $appInfo->imported_qty_price_bdt  :''  }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="v_label">Total</td>
                                                    <td>
                                                        <span> {{ (!empty($appInfo->total_machinery_qty)) ? $appInfo->total_machinery_qty  :''  }}</span>
                                                    </td>
                                                    <td>
                                                        <span> {{ (!empty($appInfo->total_machinery_price)) ? $appInfo->total_machinery_price  :''  }}</span>
                                                    </td>
                                                </tr>
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border">
                                    @if($appInfo->raw_materials_review == 1)
                                        <img src="{{ url('assets/images/new-icon-gif-8.jpg') }}" height="50px">
                                    @endif
                                    12. Description of raw & packing materials</legend>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table table-bordered dt-responsive">
                                                <tbody>
                                                <tr>
                                                    <td width="20%" class="v_label">Locally</td>
                                                    <td>
                                                        <span> {{ (!empty($appInfo->local_description)) ? $appInfo->local_description  :''  }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="20%" class="v_label">Imported</td>
                                                    <td>
                                                        <span> {{ (!empty($appInfo->imported_description)) ? $appInfo->imported_description  :''  }}</span>
                                                    </td>
                                                </tr>
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        @endif
                    </div>
                </div>

                {{--List of Directors--}}
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <div class="pull-left"><strong>List of Directors and high authorities</strong></div>
                        @if(count($listOfDirectors) == 20)
                            <div class="pull-right">
                                <a target="_blank"
                                   href="{{ url("bida-registration/directors-more-lists/".Encryption::encodeId($decodedAppId).'/'.Encryption::encodeId($appInfo->process_type_id)) }}"
                                   class="btn btn-xs btn-warning"><i class="fa fa-list" aria-hidden="true"></i> More
                                    lists </a>
                            </div>
                        @endif
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">
                                        @if($appInfo->ceo_info_review == 1)
                                            <img src="{{ url('assets/images/new-icon-gif-8.jpg') }}" height="50px">
                                        @endif
                                        Information of (Chairman/ Managing Director/ Or
                                        Equivalent):
                                    </legend>
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
                                                        <img width="120px" class="img-thumbnail" alt="Signature" src="{{ url('uploads/'.$appInfo->g_signature) }}">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">
                                        @if($appInfo->director_list_review == 1)
                                            <img src="{{ url('assets/images/new-icon-gif-8.jpg') }}" height="50px">
                                        @endif
                                        List of directors</legend>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" cellspacing="0" width="100%">
                                            <thead>
                                            <tr>
                                                <th valign="top" class="text-center">#</th>
                                                <th valign="top" class="text-center">Name</th>
                                                <th valign="top" class="text-center">Designation</th>
                                                <th valign="top" class="text-center">Nationality</th>
                                                <th colspan="2" valign="top" class="text-center">NID/PassportNo.</th>
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
                                                        <td>{{ !empty($director->nationality) ? $director->nationality : "" }}</td>
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

                {{--List of Machineries--}}
                <div class="panel panel-info">
                    <div class="panel-heading"><strong>List of Machineries</strong></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">
                                        @if($appInfo->imported_machinery_review == 1)
                                            <img src="{{ url('assets/images/new-icon-gif-8.jpg') }}" height="50px">
                                        @endif
                                        List of machinery to be imported</legend>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered" cellspacing="0" width="100%">
                                                        <thead>
                                                        <tr>
                                                            <th valign="top" class="text-center">#</th>
                                                            <th valign="top" class="text-center" width="50%">Name of
                                                                machineries
                                                            </th>
                                                            <th valign="top" class="text-center">Quantity</th>
                                                            <th valign="top" class="text-center">Unit prices TK</th>
                                                            <th colspan="2" valign="top" class="text-center">Total value
                                                                (Million) TK
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @if(count($listOfMechineryImported) > 0)
                                                            <?php $i = 1; ?>
                                                            @foreach($listOfMechineryImported as $imported)
                                                                <tr>
                                                                    <td>{{ $i++ }}</td>
                                                                    <td>{{ $imported->l_machinery_imported_name }}</td>
                                                                    <td>{{ $imported->l_machinery_imported_qty }}</td>
                                                                    <td>{{ $imported->l_machinery_imported_unit_price }}</td>
                                                                    <td>{{ $imported->l_machinery_imported_total_value }}</td>
                                                                </tr>
                                                            @endforeach
                                                            <tr>
                                                                <td colspan="4" class="text-right">Total</td>
                                                                <td>{{ CommonFunction::convertToMillionAmount($machineryImportedTotal) }}</td>
                                                            </tr>
                                                        @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">
                                        @if($appInfo->local_machinery_review == 1)
                                            <img src="{{ url('assets/images/new-icon-gif-8.jpg') }}" height="50px">
                                        @endif
                                        List of machinery locally purchase/ procure
                                    </legend>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" cellspacing="0" width="100%">
                                            <thead>
                                            <tr>
                                                <th valign="top" class="text-center">#</th>
                                                <th valign="top" class="text-center" width="50%">Name of machineries
                                                </th>
                                                <th valign="top" class="text-center">Quantity</th>
                                                <th valign="top" class="text-center">Unit prices TK</th>
                                                <th colspan="2" valign="top" class="text-center">Total value (Million)
                                                    TK
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(count($listOfMechineryLocal) > 0)
                                                <?php $i = 1; ?>
                                                @foreach($listOfMechineryLocal as $local)
                                                    <tr>
                                                        <td>{{ $i++ }}</td>
                                                        <td>{{ $local->l_machinery_local_name }}</td>
                                                        <td>{{ $local->l_machinery_local_qty }}</td>
                                                        <td>{{ $local->l_machinery_local_unit_price }}</td>
                                                        <td>{{ $local->l_machinery_local_total_value }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="4" class="text-right">Total</td>
                                                    <td>{{ CommonFunction::convertToMillionAmount($machineryLocalTotal) }}</td>
                                                </tr>
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
                    <div class="panel-heading">
                        @if($appInfo->attachment_review == 1)
                            <img src="{{ url('assets/images/new-icon-gif-8.jpg') }}" height="50px">
                        @endif
                        <strong>Attachments</strong></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped table-bordered table-hover ">
                                    <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th colspan="6">Required attachments</th>
                                        <th colspan="2">
                                            <a class="btn btn-xs btn-primary" target="_blank" href="{{ url('process/open-attachment/'.Encryption::encodeId($appInfo->process_type_id).'/'.Encryption::encodeId($appInfo->id)) }}"><i class="fa fa-link" aria-hidden="true"></i> Open all</a>
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
                    <div class="panel-heading">
                        @if($appInfo->declaration_review == 1)
                            <img src="{{ url('assets/images/new-icon-gif-8.jpg') }}" height="50px">
                        @endif
                        <strong>Declaration and undertaking</strong></div>
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
                                                         alt="User Photo" width="120px">
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