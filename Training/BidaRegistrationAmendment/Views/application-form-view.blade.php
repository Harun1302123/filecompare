<style>
    .panel-heading {
        padding: 2px 5px;
        overflow: hidden;
    }
    .row > .col-md-5, .row > .col-md-7, .row > .col-md-3, .row > .col-md-9, .row> .col-md-12>strong:first-child {
        padding-bottom: 5px;
        display: block;
    }
    .img-thumbnail {
        /*height: 180px;*/
        max-width: 180px;
    }
    legend.scheduler-border {
        font-weight: normal !important;
    }
    .table {
        margin: 0;
    }

    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        padding: 5px;
    }

    .mb5 {
        margin-bottom: 5px;
    }
    .mb0 {
        margin-bottom: 0;
    }

    .bg-green{
        background-color: rgba(103, 219, 56, 1);
    }
    .bg-yellow{
        background-color: rgba(246, 209, 15, 1);
    }
    .light-green{
        background-color: rgba(223, 240, 216, 1);
    }
    .light-yellow{
        background-color: rgba(252, 248, 227, 1);
    }

</style>
<section class="content" id="applicationForm">
    @if(in_array($appInfo->status_id,[5,6,17,22]))
        @include('ProcessPath::remarks-modal')
    @endif

    <div class="col-md-12">
        {{-- Start if this is applicant user and status is 15, 32 (proceed for payment) --}}
        @if($viewMode == 'on' && in_array(Auth::user()->user_type,['5x505']) && in_array($appInfo->status_id, [15, 32]))
            @include('ProcessPath::government-payment-information')
        @endif
        {{-- End if this is applicant user and status is 15, 32 (proceed for payment) --}}

        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="pull-left">
                    <strong style="line-height: 30px;">
                        Application for BIDA Registration Amendment
                    </strong>
                </div>
                <div class="pull-right" data-html2canvas-ignore="true" id="pdf_id">
                    @if ($appInfo->status_id == 25)
                        <a href="/bida-registration-amendment/directors-machineries-pdf/{{ Encryption::encodeId($appInfo->id) }}"
                           class="btn show-in-view btn-sm btn-info" title="List of director & machinery" target="_blank"> <i class="fa  fa-file-pdf-o"></i> <strong>List of director & machinery</strong></a>
                    @endif

                    @if ($appInfo->status_id == 25 && isset($appInfo->certificate_link) && in_array(Auth::user()->user_type, ['1x101','2x202', '4x404', '5x505']))
                        <a href="{{ url($appInfo->certificate_link) }}" class="btn show-in-view btn-sm btn-info" title="Download Approval Copy" target="_blank">
                            <i class="fa  fa-file-pdf-o"></i>Download Approval Copy
                        </a>
                    @endif

                    <a class="btn btn-sm btn-success" data-toggle="collapse" href="#paymentInfo" role="button" aria-expanded="false" aria-controls="collapseExample">
                        <i class="fa fa-money"></i>Payment Info
                    </a>

                    @if(!in_array($appInfo->status_id,[-1,5,6]))
                        <a href="javascript:void(0)" class="btn btn-danger btn-sm" title="Download Approval Copy" id="html2pdf">
                            <i class="fa fa-download"></i> Application Download as PDF
                        </a>
                    @endif

                    @if(in_array($appInfo->status_id,[5,6,17,22]))
                        <a data-toggle="modal" data-target="#remarksModal">
                            {!! Form::button('<i class="fa fa-eye"></i> <b>Reason of '.$appInfo->status_name.' </b>', array('type' => 'button', 'class' => 'btn btn-sm btn-danger')) !!}
                        </a>
                    @endif
                </div>

            </div>
            <div class="panel-body">

                <ol class="breadcrumb">
                    <li><strong>Tracking no. : </strong>{{ $appInfo->tracking_no  }}</li>
                    <li><strong> Date of Submission: </strong> {{ \App\Libraries\CommonFunction::formateDate($appInfo->submitted_at) }}</li>
                    <li><strong>Current Status : </strong> {{ $appInfo->status_name }}</li>
                    <li><strong>Current Desk :</strong> {{ $appInfo->desk_id != 0 ? \App\Libraries\CommonFunction::getDeskName($appInfo->desk_id) : 'Applicant' }}</li>
                </ol>

                {{--Payment information--}}
                @include('ProcessPath::payment-information')
                {{--End payment information--}}

                {{--Star basic informaton section--}}
                <div class="panel panel-info">
                    <div class="panel-heading"><strong>Basic information</strong></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label"> Did you receive your BIDA Registration/ BIDA Registration amendment approval online OSS?</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($appInfo->is_approval_online)) ? ucfirst($appInfo->is_approval_online) : ''  }}
                            </div>
                        </div>
                        @if($appInfo->is_approval_online == 'yes')
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Please give your approved BIDA Registration/ BIDA Registration amendment Tracking No.</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    <a href="{{$data['ref_app_url']}}" target="_blank">
                                        <span class="label label-success label_tracking_no">{{ (empty($appInfo->ref_app_tracking_no) ? '' : $appInfo->ref_app_tracking_no) }}</span>
                                    </a>
                                    &nbsp
                                    &nbsp;{!! \App\Libraries\CommonFunction::getCertificateByTrackingNo($appInfo->ref_app_tracking_no) !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Approved Date</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($appInfo->ref_app_approve_date) ? date("d-M-Y", strtotime($appInfo->ref_app_approve_date)) : '')  }}
                                </div>
                            </div>
                        @endif
                        @if($appInfo->is_approval_online == 'no')
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Please give your manually approved BIDA Registration No</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($appInfo->manually_approved_br_no)) ? $appInfo->manually_approved_br_no : '' }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Approved Date</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($appInfo->manually_approved_br_date) ? date("d-M-Y", strtotime($appInfo->manually_approved_br_date)) : '')  }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                {{--End basic informaton section--}}

                {{--Star Manually Bida Registration Amendment informaton section--}}
                <div class="panel panel-info">
                    <div class="panel-heading"><strong>Manually Bida Registration Amendment Info</strong></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label"> Did you receive your last BIDA Registration amendment approval manually?</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($appInfo->is_bra_approval_manually)) ? ucfirst($appInfo->is_bra_approval_manually) : ''  }}
                            </div>
                        </div>
                        @if($appInfo->is_bra_approval_manually == 'yes')
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Please give your manually approved BIDA Registration Amendment memo No.</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    <a href="{{$data['ref_app_url']}}" target="_blank">
                                        <span class="label label-success label_tracking_no">{{ (empty($appInfo->manually_approved_bra_no) ? '' : $appInfo->manually_approved_bra_no) }}</span>
                                    </a>

                                    @if(!empty($appInfo->manually_bra_approval_copy))
                                        <a target="_blank" class="documentUrl btn btn-xs btn-success" href="{{URL::to('/uploads/'.(!empty($appInfo->manually_bra_approval_copy) ? $appInfo->manually_bra_approval_copy : ''))}}">
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
                                    {{ (!empty($appInfo->manually_approved_bra_date) ? date("d-M-Y", strtotime($appInfo->manually_approved_bra_date)) : '')  }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                {{--End Manually Bida Registration Amendment informaton section--}}

                <fieldset class="scheduler-border">
                    <legend class="scheduler-border">Please specify your desired office:</legend>
                    <h4>You have selected <b>'{{$appInfo->divisional_office_name}}
                            '</b>, {{ $appInfo->divisional_office_address }}
                        .</h4>
                </fieldset>

                {{--Start company information--}}
                <div class="panel panel-info">
                    <div class="panel-heading"><strong>A. Company information</strong></div>
                    <div class="panel-body">
                        {{-- <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">Name of organization/ company</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($appInfo->company_name)) ? $appInfo->company_name : ''  }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <span class="v_label">Name of organization/ company (বাংলা)</span>
                                <span class="pull-right">&#58;</span>
                            </div>
                            <div class="col-md-7 col-xs-6">
                                {{ (!empty($appInfo->company_name_bn)) ? $appInfo->company_name_bn : ''  }}
                            </div>
                        </div> --}}

                        <table class="table table-responsive table-bordered">
                            <thead>
                            <tr>
                                <th width="30%">Field name</th>
                                <th class="bg-yellow">Existing information (Latest BIDA Reg. Info.)</th>
                                <th class="bg-green">Proposed information</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Name of organization/ company</td>
                                <td class="light-yellow">
                                    {{ (!empty($appInfo->company_name)) ? $appInfo->company_name : ''  }}
                                </td>
                                <td class="light-green">
                                    {{ (!empty($appInfo->n_company_name)) ? $appInfo->n_company_name : ''  }}
                                </td>
                            </tr>
                            <tr>
                                <td>Name of organization/ company (বাংলা)</td>
                                <td class="light-yellow">
                                    {{ (!empty($appInfo->company_name_bn)) ? $appInfo->company_name_bn : ''  }}
                                </td>
                                <td class="light-green">
                                    {{ (!empty($appInfo->n_company_name_bn)) ? $appInfo->n_company_name_bn : ''  }}
                                </td>
                            </tr>
                            <tr>
                                <td>Name of the project</td>
                                <td class="light-yellow">
                                    {{ (!empty($appInfo->project_name)) ? $appInfo->project_name : ''  }}
                                </td>
                                <td class="light-green">
                                    {{ (!empty($appInfo->n_project_name)) ? $appInfo->n_project_name : ''  }}
                                </td>
                            </tr>
                            <tr>
                                <td>Type of the organization</td>
                                <td class="light-yellow">
                                    {{ (!empty($appInfo->organization_type_id)) ? $eaOrganizationType[$appInfo->organization_type_id] : ''  }}
                                </td>
                                <td class="light-green">
                                    {{ (!empty($appInfo->n_organization_type_id)) ? $eaOrganizationType[$appInfo->n_organization_type_id] : ''  }}
                                </td>
                            </tr>
                            <tr>
                                <td>Status of the organization</td>
                                <td class="light-yellow">
                                    {{ (!empty($appInfo->organization_status_id)) ? $eaOrganizationStatus[$appInfo->organization_status_id] : ''  }}
                                </td>
                                <td class="light-green">
                                    {{ (!empty($appInfo->n_organization_status_id)) ? $eaOrganizationStatus[$appInfo->n_organization_status_id] : ''  }}
                                </td>
                            </tr>
                            <tr>
                                <td>Ownership status</td>
                                <td class="light-yellow">
                                    {{ (!empty($appInfo->ownership_status_id)) ? $eaOwnershipStatus[$appInfo->ownership_status_id] : ''  }}
                                </td>
                                <td class="light-green">
                                    {{ (!empty($appInfo->n_ownership_status_id)) ? $eaOwnershipStatus[$appInfo->n_ownership_status_id] : ''  }}
                                </td>
                            </tr>

                            <tr>
                                <td>Country of Origin</td>
                                <td class="light-yellow">
                                    {{ (!empty($appInfo->country_of_origin_id)) ? $countries[$appInfo->country_of_origin_id] : ''  }}
                                </td>
                                <td class="light-green">
                                    {{ (!empty($appInfo->n_country_of_origin_id)) ? $countries[$appInfo->n_country_of_origin_id] : ''  }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <br>
                        <table class="table table-responsive table-bordered top-padding">
                            <thead>
                            <tr>
                                <th colspan="5"><strong>Business Sector</strong></th>
                            </tr>
                            <tr>
                                <th width="22%">Field name</th>
                                <th colspan="2" class="bg-yellow" width="39%">Existing information (Latest BIDA Reg. Info.)</th>
                                <th colspan="2" class="bg-green" width="39%">Proposed information</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Business Sector (BBS Class Code)</td>
                                <td colspan="2" class="light-yellow">
                                    {{ !empty($appInfo->class_code) ? $appInfo->class_code : '' }}
                                </td>
                                <td colspan="2" class="light-green">
                                    {{ !empty($appInfo->n_class_code) ? $appInfo->n_class_code : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td width="22%">Category</td>
                                <td width="10%" class="light-yellow">Code</td>
                                <td width="29%" class="light-yellow">Description</td>

                                <td width="10%" class="light-green">Code</td>
                                <td width="29%" class="light-green">Description</td>
                            </tr>
                            <tr>
                                <td width="22%">Section</td>
                                <td width="10%" class="light-yellow">{{ !empty($busness_code) ? $busness_code[0]['section_code'] : ''}}</td>
                                <td width="29%" class="light-yellow">{{ !empty($busness_code) ? $busness_code[0]['section_name'] : ''}}</td>

                                <td width="10%" class="light-green"><span id="section_code"></span>
                                    {{ !empty($n_busness_code) ? $n_busness_code[0]['section_code'] : ''}}
                                </td>
                                <td width="29%" class="light-green"><span id="section_name"></span>
                                    {{ !empty($n_busness_code) ? $n_busness_code[0]['section_name'] : ''}}
                                </td>
                            </tr>
                            <tr>
                                <td width="22%">Division</td>
                                <td width="10%" class="light-yellow">{{ !empty($busness_code) ? $busness_code[0]['division_code'] : ''}}</td>
                                <td width="29%" class="light-yellow">{{ !empty($busness_code) ? $busness_code[0]['division_name'] : ''}}</td>

                                <td width="10%" class="light-green"><span id="division_code"></span>
                                    {{ !empty($n_busness_code) ? $n_busness_code[0]['division_code'] : ''}}
                                </td>
                                <td width="29%" class="light-green"><span id="division_name"></span>
                                    {{ !empty($n_busness_code) ? $n_busness_code[0]['division_name'] : ''}}
                                </td>
                            </tr>
                            <tr>
                                <td width="22%">Group</td>
                                <td width="10%" class="light-yellow">{{ !empty($busness_code) ? $busness_code[0]['group_code'] : ''}}</td>
                                <td width="29%" class="light-yellow">{{ !empty($busness_code) ? $busness_code[0]['group_name'] : ''}}</td>

                                <td width="10%" class="light-green"><span id="group_code"></span>
                                    {{ !empty($n_busness_code) ? $n_busness_code[0]['group_code'] : ''}}
                                </td>
                                <td width="29%" class="light-green"><span id="group_name"></span>
                                    {{ !empty($n_busness_code) ? $n_busness_code[0]['group_name'] : ''}}
                                </td>
                            </tr>
                            <tr>
                                <td width="22%">Class</td>
                                <td width="10%" class="light-yellow">{{ !empty($busness_code) ? $busness_code[0]['code'] : ''}}</td>
                                <td width="29%" class="light-yellow">{{ !empty($busness_code) ? $busness_code[0]['name'] : ''}}</td>

                                <td width="10%" class="light-green"><span id="class_code"></span>
                                    {{ !empty($n_busness_code) ? $n_busness_code[0]['code'] : ''}}
                                </td>
                                <td width="29%" class="light-green"><span id="class_name"></span>
                                    {{ !empty($n_busness_code) ? $n_busness_code[0]['name'] : ''}}
                                </td>
                            </tr>
                            <tr>
                                <td width="22%">Sub class</td>
                                <td colspan="2" class="light-yellow">
                                    {{ (($appInfo->sub_class_id === 0) ? 'Other' : (!is_null($sub_class) ? $sub_class->name : '')) }}
                                </td>
                                <td colspan="2" class="light-green">
                                    {{ (($appInfo->n_sub_class_id === 0) ? 'other' : (!is_null($n_sub_class) ? $n_sub_class->name : '')) }}
                                </td>
                            </tr>

                            @if($appInfo->sub_class_id === 0 || $appInfo->n_sub_class_id === 0)
                                <tr>
                                    <td width="20%" class="">Other sub class code</td>
                                    <td colspan="2" class="light-yellow">
                                        {{ (!empty($appInfo->other_sub_class_code)) ? $appInfo->other_sub_class_code : '' }}
                                    </td>
                                    <td colspan="2" class="light-green">
                                        {{ (!empty($appInfo->n_other_sub_class_code)) ? $appInfo->n_other_sub_class_code : '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td width="20%">Other sub class name</td>
                                    <td colspan="2" class="light-yellow">
                                        {{ (!empty($appInfo->other_sub_class_name)) ? $appInfo->other_sub_class_name : '' }}
                                    </td>
                                    <td colspan="2" class="light-green">
                                        {{ (!empty($appInfo->n_other_sub_class_name)) ? $appInfo->n_other_sub_class_name : '' }}
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                {{--End company information--}}

                {{--Start CEO information--}}
                <div class="panel panel-info">
                    <div class="panel-heading"><strong>B. Information of Principal Promoter/ Chairman/ Managing Director/ CEO/ Country Manager</strong></div>
                    <div class="panel-body">
                        <table class="table table-responsive table-bordered">
                            <thead>
                            <tr>
                                <th width="30%">Field name</th>
                                <th class="bg-yellow">Existing information (Latest BIDA Reg. Info.)</th>
                                <th class="bg-green">Proposed information</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Country</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->ceo_country_id) ? $countries[$appInfo->ceo_country_id] : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_ceo_country_id) ? $countries[$appInfo->n_ceo_country_id] : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Date of Birth</td>
                                <td class="light-yellow">
                                    {{ (!empty($appInfo->ceo_dob) ? date("d-M-Y", strtotime($appInfo->ceo_dob)) : '') }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_ceo_dob) ? date("d-M-Y", strtotime($appInfo->n_ceo_dob)) : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>NID/ Passport No.</td>
                                @if($appInfo->ceo_country_id == 18)
                                    <td class="light-yellow">
                                        {{ !empty($appInfo->ceo_nid) ? $appInfo->ceo_nid : '' }}
                                    </td>
                                @else
                                    <td class="light-yellow">
                                        {{ !empty($appInfo->ceo_passport_no) ? $appInfo->ceo_passport_no : '' }}
                                    </td>
                                @endif

                                @if($appInfo->n_ceo_country_id == 18)
                                    <td class="light-green">
                                        {{ !empty($appInfo->n_ceo_nid) ? $appInfo->n_ceo_nid : '' }}
                                    </td>
                                @else
                                    <td class="light-green">
                                        {{ !empty($appInfo->n_ceo_passport_no) ? $appInfo->n_ceo_passport_no : '' }}
                                    </td>
                                @endif
                            </tr>
                            <tr>
                                <td>Designation</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->ceo_designation) ? $appInfo->ceo_designation : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_ceo_designation) ? $appInfo->n_ceo_designation : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Full Name</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->ceo_full_name) ? $appInfo->ceo_full_name : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_ceo_full_name) ? $appInfo->n_ceo_full_name : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>District/ City/ State</td>
                                @if($appInfo->ceo_country_id == 18)
                                    <td class="light-yellow">
                                        {{ !empty($appInfo->ceo_district_id) ? $districts[$appInfo->ceo_district_id] : '' }}
                                    </td>
                                @else
                                    <td class="light-yellow">
                                        {{ !empty($appInfo->ceo_city) ? $appInfo->ceo_city : '' }}
                                    </td>
                                @endif

                                @if($appInfo->n_ceo_country_id == 18)
                                    <td class="light-green">
                                        {{ !empty($appInfo->n_ceo_district_id) ? $districts[$appInfo->n_ceo_district_id] : '' }}
                                    </td>
                                @else
                                    <td class="light-green">
                                        {{ !empty($appInfo->n_ceo_city) ? $appInfo->n_ceo_city : '' }}
                                    </td>
                                @endif
                            </tr>
                            <tr>
                                <td>State/ Province/ Police station/ Town</td>

                                @if($appInfo->ceo_country_id == 18)
                                    <td class="light-yellow">
                                        {{ !empty($appInfo->ceo_thana_id) ? $thana[$appInfo->ceo_thana_id] : '' }}
                                    </td>
                                @else
                                    <td class="light-yellow">
                                        {{ !empty($appInfo->ceo_state) ? $appInfo->ceo_state : '' }}
                                    </td>
                                @endif

                                @if($appInfo->n_ceo_country_id == 18)
                                    <td class="light-green">
                                        {{ !empty($appInfo->n_ceo_thana_id) ? $thana[$appInfo->n_ceo_thana_id] : '' }}
                                    </td>
                                @else
                                    <td class="light-green">
                                        {{ !empty($appInfo->n_ceo_state) ? $appInfo->n_ceo_state : '' }}
                                    </td>
                                @endif
                            </tr>
                            <tr>
                                <td>Post/ Zip Code</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->ceo_post_code) ? $appInfo->ceo_post_code : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_ceo_post_code) ? $appInfo->n_ceo_post_code : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>House, Flat/ Apartment, Road</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->ceo_address) ? $appInfo->ceo_address : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_ceo_address) ? $appInfo->n_ceo_address : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Telephone No.</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->ceo_telephone_no) ? $appInfo->ceo_telephone_no : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_ceo_telephone_no) ? $appInfo->n_ceo_telephone_no : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Mobile No.</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->ceo_mobile_no) ? $appInfo->ceo_mobile_no : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_ceo_mobile_no) ? $appInfo->n_ceo_mobile_no : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->ceo_email) ? $appInfo->ceo_email : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_ceo_email) ? $appInfo->n_ceo_email : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Fax No.</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->ceo_fax_no) ? $appInfo->ceo_fax_no : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_ceo_fax_no) ? $appInfo->n_ceo_fax_no : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Father's Name</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->ceo_father_name) ? $appInfo->ceo_father_name : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_ceo_father_name) ? $appInfo->n_ceo_father_name : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Mother's Name</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->ceo_mother_name) ? $appInfo->ceo_mother_name : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_ceo_mother_name) ? $appInfo->n_ceo_mother_name : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Spouse name</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->ceo_spouse_name) ? $appInfo->ceo_spouse_name : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_ceo_spouse_name) ? $appInfo->n_ceo_spouse_name : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Gender</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->ceo_gender) ? $appInfo->ceo_gender : '' }}
                                </td>
                                <td class="light-green">
                                    {{ (!empty($appInfo->n_ceo_gender) && $appInfo->n_ceo_gender != "Not defined") ? $appInfo->n_ceo_gender : '' }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{--End CEO information--}}

                {{--Start office information--}}
                <div class="panel panel-info">
                    <div class="panel-heading"><strong>C. Office Address</strong></div>
                    <div class="panel-body">
                        <table class="table table-responsive table-bordered">
                            <thead>
                            <tr>
                                <th width="30%">Field name</th>
                                <th class="bg-yellow" width="35%">Existing information (Latest BIDA Reg. Info.)</th>
                                <th class="bg-green" width="35%">Proposed information</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Division</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->office_division_id) ? $divisions[$appInfo->office_division_id] : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_office_division_id) ? $divisions[$appInfo->n_office_division_id] : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>District</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->office_district_id) ? $districts[$appInfo->office_district_id] : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_office_district_id) ? $districts[$appInfo->n_office_district_id] : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Police Station</td>
                                <td class="light-yellow">
                                   {{ !empty($appInfo->office_thana_id) ? $thana[$appInfo->office_thana_id] : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_office_thana_id) ? $thana[$appInfo->n_office_thana_id] : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Post Office</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->office_post_office) ? $appInfo->office_post_office : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_office_post_office) ? $appInfo->n_office_post_office : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Post Code</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->office_post_code) ? $appInfo->office_post_code : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_office_post_code) ? $appInfo->n_office_post_code : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Address</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->office_address) ? $appInfo->office_address : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_office_address) ? $appInfo->n_office_address : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Telephone No.</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->office_telephone_no) ? $appInfo->office_telephone_no : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_office_telephone_no) ? $appInfo->n_office_telephone_no : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Mobile No.</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->office_mobile_no) ? $appInfo->office_mobile_no : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_office_mobile_no) ? $appInfo->n_office_mobile_no : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Fax No.</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->office_fax_no) ? $appInfo->office_fax_no : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_office_fax_no) ? $appInfo->n_office_fax_no : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Email </td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->office_email) ? $appInfo->office_email : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_office_email) ? $appInfo->n_office_email : '' }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{--End office information--}}

                {{--Start factory information--}}
                <div class="panel panel-info">
                    <div class="panel-heading"><strong>D. Factory Address</strong></div>
                    <div class="panel-body">
                        <table class="table table-responsive table-bordered">
                            <thead>
                            <tr>
                                <th width="30%">Field name</th>
                                <th class="bg-yellow" width="35%">Existing information (Latest BIDA Reg. Info.)</th>
                                <th class="bg-green" width="35%">Proposed information</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>District</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->factory_district_id) ? $districts[$appInfo->factory_district_id] : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_factory_district_id) ? $districts[$appInfo->n_factory_district_id] : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Police Station</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->factory_thana_id) ? $thana[$appInfo->factory_thana_id] : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_factory_thana_id) ? $thana[$appInfo->n_factory_thana_id] : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Post Office</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->factory_post_office) ? $appInfo->factory_post_office : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_factory_post_office) ? $appInfo->n_factory_post_office : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Post Code</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->factory_post_code) ? $appInfo->factory_post_code : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_factory_post_code) ? $appInfo->n_factory_post_code : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Address</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->factory_address) ? $appInfo->factory_address : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_factory_address) ? $appInfo->n_factory_address : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Telephone No.</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->factory_telephone_no) ? $appInfo->factory_telephone_no : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_factory_telephone_no) ? $appInfo->n_factory_telephone_no : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Mobile No.</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->factory_mobile_no) ? $appInfo->factory_mobile_no : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_factory_mobile_no) ? $appInfo->n_factory_mobile_no : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Fax No.</td>
                                <td class="light-yellow">
                                    {{ !empty($appInfo->factory_fax_no) ? $appInfo->factory_fax_no : '' }}
                                </td>
                                <td class="light-green">
                                    {{ !empty($appInfo->n_factory_fax_no) ? $appInfo->n_factory_fax_no : '' }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{--End factory information--}}

                {{--Star registration informaton section--}}
                <div class="panel panel-info">
                    <div class="panel-heading"><strong>Registration Information</strong></div>
                    <div class="panel-body">
                        {{---Start Project status--}}
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><strong>1. Project status</strong></legend>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                <tr>
                                    <th width="30%">Field name</th>
                                    <th class="bg-yellow" width="35%">Existing information (Latest BIDA Reg. Info.)</th>
                                    <th class="bg-green" width="35%">Proposed information</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Project status</td>
                                    <td class="light-yellow">
                                        {{ !empty($appInfo->project_status_id) ? $projectStatusList[$appInfo->project_status_id] : '' }}
                                    </td>
                                    <td class="light-green">
                                        {{ !empty($appInfo->n_project_status_id) ? $projectStatusList[$appInfo->n_project_status_id] : '' }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </fieldset>
                        {{---End Project status--}}

                        {{---Start annual production capacity--}}
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><strong>2. Annual production capacity</strong></legend>
                            <div class="table-responsive">
                                <div id="load_apc_data"></div>
                            </div>
                            @if($annualProductionCapacity > 20)
                                <br>
                                <a href="javascript:void(0)" onclick="loadAnnualProductionCapacityData('all', 'on')" id="apc_data">Load more data</a>
                            @endif
                        </fieldset>
                        {{---End annual production capacity--}}

                        {{---Start commercial operation--}}
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><strong>3. Date of commercial operation</strong></legend>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                <tr>
                                    <th width="30%">Field name</th>
                                    <th class="bg-yellow" width="35%">Existing information (Latest BIDA Reg. Info.)</th>
                                    <th class="bg-green" width="35%">Proposed information</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Date of commercial operation</td>
                                    <td class="light-yellow">
                                        {{ (!empty($appInfo->commercial_operation_date) ? date("d-M-Y", strtotime($appInfo->commercial_operation_date)) : '') }}
                                    </td>
                                    <td class="light-green">
                                        {{ (!empty($appInfo->n_commercial_operation_date)) ? date("d-M-Y", strtotime($appInfo->n_commercial_operation_date)) : '' }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </fieldset>
                        {{---end commercial operation--}}

                        {{---Start sales--}}
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><strong>4. Sales (in 100%)</strong></legend>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                <tr>
                                    <th width="30%">Field name</th>
                                    <th class="bg-yellow" width="35%">Existing information (Latest BIDA Reg. Info.)</th>
                                    <th class="bg-green" width="35%">Proposed information</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Local</td>
                                    <td class="light-yellow">
                                        {{ !empty($appInfo->local_sales) ? $appInfo->local_sales : '' }}
                                    </td>
                                    <td class="light-green">
                                        {{ !empty($appInfo->n_local_sales) ? $appInfo->n_local_sales : '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Foreign</td>
                                    <td class="light-yellow">
                                        {{ !empty($appInfo->foreign_sales) ? $appInfo->foreign_sales : '' }}
                                    </td>
                                    <td class="light-green">
                                        {{ !empty($appInfo->n_foreign_sales) ? $appInfo->n_foreign_sales : '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Total in %</td>
                                    <td class="light-yellow">
                                        {{ !empty($appInfo->total_sales) ? $appInfo->total_sales : '' }}
                                    </td>
                                    <td class="light-green">
                                        {{ !empty($appInfo->n_total_sales) ? $appInfo->n_total_sales : '' }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </fieldset>
                        {{---End sales--}}

                        {{---Start manpower--}}
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><strong>5. Manpower of the organization</strong></legend>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th colspan="3">Local (Bangladesh only)</th>
                                    <th colspan="3">Foreign (Abroad country)</th>
                                    <th>Grand total</th>
                                    <th colspan="2">Ratio</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Information</td>
                                    <td>Male</td>
                                    <td>Female</td>
                                    <td>Total (a)</td>

                                    <td>Male</td>
                                    <td>Female</td>
                                    <td>Total (b)</td>

                                    <td>(a+b)</td>

                                    <td>Local</td>
                                    <td>Foreign</td>
                                </tr>
                                <tr class="light-yellow">
                                    <td>Existing (Latest BIDA Reg. Info.)</td>
                                    <td>
                                        {{ !empty($appInfo->local_male) ? $appInfo->local_male : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->local_female) ? $appInfo->local_female : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->local_total) ? $appInfo->local_total : '' }}
                                    </td>

                                    <td>
                                        {{ !empty($appInfo->foreign_male) ? $appInfo->foreign_male : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->foreign_female) ? $appInfo->foreign_female : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->foreign_total) ? $appInfo->foreign_total : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->manpower_total) ? $appInfo->manpower_total : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->manpower_local_ratio) ? $appInfo->manpower_local_ratio : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->manpower_foreign_ratio) ? $appInfo->manpower_foreign_ratio : '' }}
                                    </td>
                                </tr>

                                <tr class="light-green">
                                    <td>Proposed</td>
                                    <td>
                                        {{ !empty($appInfo->n_local_male) ? $appInfo->n_local_male : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->n_local_female) ? $appInfo->n_local_female : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->n_local_total) ? $appInfo->n_local_total : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->n_foreign_male) ? $appInfo->n_foreign_male : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->n_foreign_female) ? $appInfo->n_foreign_female : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->n_foreign_total) ? $appInfo->n_foreign_total : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->n_manpower_total) ? $appInfo->n_manpower_total : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->n_manpower_local_ratio) ? $appInfo->n_manpower_local_ratio : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->n_manpower_foreign_ratio) ? $appInfo->n_manpower_foreign_ratio : '' }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </fieldset>
                        {{---End manpower--}}

                        {{---Start investment--}}
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><strong>6. Investment</strong></legend>
                            <div class="table-responsive">
                                <table class="table table-responsive table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th colspan="6">Items</th>
                                    </tr>
                                    <tr>
                                        <th width="30%">Fixed Investment</th>
                                        <th class="bg-yellow" width="35%" colspan="2">Existing information (Latest BIDA Reg. Info.)</th>
                                        <th class="bg-green" width="35%" colspan="2">Proposed information</th>
                                    </tr>

                                    </thead>
                                    <tbody>

                                    <tr>
                                        <td>Land (Million)</td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->local_land_ivst) ? $appInfo->local_land_ivst : '' }}
                                        </td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->local_land_ivst_ccy) ? $currencyBDT[$appInfo->local_land_ivst_ccy] : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_local_land_ivst) ? $appInfo->n_local_land_ivst : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_local_land_ivst_ccy) ? $currencyBDT[$appInfo->n_local_land_ivst_ccy] : '' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Building (Million)</td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->local_building_ivst) ? $appInfo->local_building_ivst : '' }}
                                        </td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->local_building_ivst_ccy) ? $currencyBDT[$appInfo->local_building_ivst_ccy] : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_local_building_ivst) ? $appInfo->n_local_building_ivst : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_local_building_ivst_ccy) ? $currencyBDT[$appInfo->n_local_building_ivst_ccy] : '' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Machinery & Equipment (Million)</td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->local_machinery_ivst) ? $appInfo->local_machinery_ivst : '' }}
                                        </td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->local_machinery_ivst_ccy) ? $currencyBDT[$appInfo->local_machinery_ivst_ccy] : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_local_machinery_ivst) ? $appInfo->n_local_machinery_ivst : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_local_machinery_ivst_ccy) ? $currencyBDT[$appInfo->n_local_machinery_ivst_ccy] : '' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Others (Million)</td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->local_others_ivst) ? $appInfo->local_others_ivst : '' }}
                                        </td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->local_others_ivst_ccy) ? $currencyBDT[$appInfo->local_others_ivst_ccy] : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_local_others_ivst) ? $appInfo->n_local_others_ivst : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_local_others_ivst_ccy) ? $currencyBDT[$appInfo->n_local_others_ivst_ccy] : '' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Working Capital (Three Months) (Million)</td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->local_wc_ivst) ? $appInfo->local_wc_ivst : '' }}
                                        </td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->local_wc_ivst_ccy) ? $currencyBDT[$appInfo->local_wc_ivst_ccy] : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_local_wc_ivst) ? $appInfo->n_local_wc_ivst : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_local_wc_ivst_ccy) ? $currencyBDT[$appInfo->n_local_wc_ivst_ccy] : '' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Total Investment (Million) (BDT)</td>
                                        <td class="light-yellow" colspan="2">
                                            {{ !empty($appInfo->total_fixed_ivst_million) ? $appInfo->total_fixed_ivst_million : '' }}
                                        </td>
                                        <td class="light-green" colspan="2">
                                            {{ !empty($appInfo->n_total_fixed_ivst_million) ? $appInfo->n_total_fixed_ivst_million : '' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Total Investment (BDT)</td>
                                        <td class="light-yellow" colspan="2">
                                            {{ !empty($appInfo->total_fixed_ivst) ? $appInfo->total_fixed_ivst : '' }}
                                        </td>
                                        <td class="light-green" colspan="2">
                                            {{ !empty($appInfo->n_total_fixed_ivst) ? $appInfo->n_total_fixed_ivst : '' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Dollar exchange rate (USD)</td>
                                        <td class="light-yellow" colspan="2">
                                            {{ !empty($appInfo->usd_exchange_rate) ? $appInfo->usd_exchange_rate : '' }}
                                        </td>
                                        <td class="light-green" colspan="2">
                                            {{ !empty($appInfo->n_usd_exchange_rate) ? $appInfo->n_usd_exchange_rate : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6"><span class="help-text pull-right">Exchange Rate Ref: <a href="https://www.bb.org.bd/econdata/exchangerate.php"
                                                                                                                 target="_blank">Bangladesh Bank</a>. Please Enter Today's Exchange Rate</span></td>
                                    </tr>
                                    <tr>
                                        <td>Total Fee (BDT)</td>
                                        <td class="light-yellow" colspan="2">
                                            {{ !empty($appInfo->total_fee) ? $appInfo->total_fee : '' }}
                                        </td>
                                        <td class="light-green" colspan="2">
                                            {{ !empty($appInfo->n_total_fee) ? $appInfo->n_total_fee : '' }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </fieldset>
                        {{---end investment--}}

                        {{---Start source of finance--}}
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><strong>7. Source of finance</strong></legend>
                            <div class="table-responsive">
                                <table class="table table-responsive table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th colspan="6">Items</th>
                                    </tr>
                                    <tr>
                                        <th width="30%">Fixed Investment</th>
                                        <th class="bg-yellow" width="35%">Existing information (Latest BIDA Reg. Info.)</th>
                                        <th class="bg-green" width="35%">Proposed information</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td><strong>(a)</strong> Local Equity (Million)</td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->finance_src_loc_equity_1) ? $appInfo->finance_src_loc_equity_1 : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_finance_src_loc_equity_1) ? $appInfo->n_finance_src_loc_equity_1 : '' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Foreign Equity (Million)</td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->finance_src_foreign_equity_1) ? $appInfo->finance_src_foreign_equity_1 : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_finance_src_foreign_equity_1) ? $appInfo->n_finance_src_foreign_equity_1 : '' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><strong>Total Equity</strong></td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->finance_src_loc_total_equity_1) ? $appInfo->finance_src_loc_total_equity_1 : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_finance_src_loc_total_equity_1) ? $appInfo->n_finance_src_loc_total_equity_1 : '' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><strong>(b)</strong> Local Loan (Million)</td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->finance_src_loc_loan_1) ? $appInfo->finance_src_loc_loan_1 : '' }}
                                        </td>

                                        <td class="light-green">
                                            {{ !empty($appInfo->n_finance_src_loc_loan_1) ? $appInfo->n_finance_src_loc_loan_1 : '' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Foreign Loan (Million)</td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->finance_src_foreign_loan_1) ? $appInfo->finance_src_foreign_loan_1 : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_finance_src_foreign_loan_1) ? $appInfo->n_finance_src_foreign_loan_1 : '' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Total Loan (Million)</td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->finance_src_total_loan) ? $appInfo->finance_src_total_loan : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_finance_src_total_loan) ? $appInfo->n_finance_src_total_loan : '' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><strong>Total Financing Million (a+b)</strong></td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->finance_src_loc_total_financing_m) ? $appInfo->finance_src_loc_total_financing_m : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_finance_src_loc_total_financing_m) ? $appInfo->n_finance_src_loc_total_financing_m : '' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><strong>Total Financing BDT (a+b)</strong></td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->finance_src_loc_total_financing_1) ? $appInfo->finance_src_loc_total_financing_1 : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_finance_src_loc_total_financing_1) ? $appInfo->n_finance_src_loc_total_financing_1 : '' }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <br>
                                <div class="table-responsive">
                                    <table width="100%" class="table table-bordered">

                                        <thead>
                                        <tr>
                                            <td colspan="6">Country wise source of finance (Million BDT)</td>
                                        </tr>
                                        <tr>
                                            <td class="bg-yellow" colspan="3">Existing information (Latest BIDA Reg.
                                                Info.)
                                            </td>
                                            <td class="bg-green" colspan="3">Proposed information</td>
                                        </tr>
                                        <tr>
                                            <th class="light-yellow">Country</th>
                                            <th class="light-yellow">Equity Amount</th>
                                            <th class="light-yellow">Loan Amount</th>

                                            <th class="light-green">Country</th>
                                            <th class="light-green">Equity Amount</th>
                                            <th class="light-green">Loan Amount</th>
                                        </tr>

                                        </thead>

                                        @foreach($sourceOfFinance as $source)
                                            <tr>
                                                <td class="light-yellow">{{ !empty($source->country_id) ? $countries[$source->country_id] : "" }}</td>
                                                <td class="light-yellow">{{ $source->equity_amount }}</td>
                                                <td class="light-yellow">{{ $source->loan_amount }}</td>

                                                <td class="light-green">{{ !empty($source->n_country_id) ? $countries[$source->n_country_id] : "" }}</td>
                                                <td class="light-green">{{ $source->n_equity_amount }}</td>
                                                <td class="light-green">{{ $source->n_loan_amount }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </fieldset>
                        {{---end source of finance--}}

                        {{---Start Public utility service--}}
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><strong>8. Public utility service</strong></legend>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                <tr>
                                    <th>Information</th>
                                    <th>Land</th>
                                    <th>Electricity</th>
                                    <th>Gas</th>
                                    <th>Telephone</th>
                                    <th>Road</th>
                                    <th>Water</th>
                                    <th>Drainage</th>
                                    <th>Others</th>

                                </tr>
                                </thead>
                                <tbody>
                                <tr class="light-yellow">
                                    <td class="bg-yellow">Existing (Latest BIDA Reg. Info.)</td>
                                    <td>
                                        {{ !empty($appInfo->public_land) ? $appInfo->public_land : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->public_electricity) ? $appInfo->public_electricity : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->public_gas) ? $appInfo->public_gas : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->public_telephone) ? $appInfo->public_telephone : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->public_road) ? $appInfo->public_road : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->public_water) ? $appInfo->public_water : '' }}
                                    </td>

                                    <td>
                                        {{ !empty($appInfo->public_drainage) ? $appInfo->public_drainage : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->public_others) ? $appInfo->public_others : '' }}
                                    </td>
                                </tr>

                                <tr class="light-green">
                                    <td class="bg-green">Proposed</td>
                                    <td>
                                        {{ !empty($appInfo->n_public_land) ? $appInfo->n_public_land : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->n_public_electricity) ? $appInfo->n_public_electricity : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->n_public_gas) ? $appInfo->n_public_gas : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->n_public_telephone) ? $appInfo->n_public_telephone : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->n_public_road) ? $appInfo->n_public_road : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->n_public_water) ? $appInfo->n_public_water : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->n_public_drainage) ? $appInfo->n_public_drainage : '' }}
                                    </td>
                                    <td>
                                        {{ !empty($appInfo->n_public_others) ? $appInfo->n_public_others : '' }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </fieldset>
                        {{---end--}}

                        {{--    Trade licence details--}}
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><strong>9. Trade licence details</strong></legend>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                <tr>
                                    <th>Field name</th>
                                    <th class="bg-yellow">Existing information (Latest BIDA Reg. Info.)</th>
                                    <th class="bg-green">Proposed information</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Trade Licence Number</td>
                                    <td class="light-yellow">
                                        {{ !empty($appInfo->trade_licence_num) ? $appInfo->trade_licence_num : '' }}
                                    </td>
                                    <td class="light-green">
                                        {{ !empty($appInfo->n_trade_licence_num) ? $appInfo->n_trade_licence_num : '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Issuing Authority</td>
                                    <td class="light-yellow">
                                        {{ !empty($appInfo->trade_licence_issuing_authority) ? $appInfo->trade_licence_issuing_authority : '' }}
                                    </td>
                                    <td class="light-green">
                                        {{ !empty($appInfo->n_trade_licence_issuing_authority) ? $appInfo->n_trade_licence_issuing_authority : '' }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </fieldset>

                        {{-- Tin--}}
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><strong>10. Tin</strong></legend>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                <tr>
                                    <th>Field name</th>
                                    <th class="bg-yellow">Existing information (Latest BIDA Reg. Info.)</th>
                                    <th class="bg-green">Proposed information</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Tin Number</td>
                                    <td class="light-yellow">
                                        {{ !empty($appInfo->tin_number) ? $appInfo->tin_number : '' }}
                                    </td>
                                    <td class="light-green">
                                        {{ !empty($appInfo->n_tin_number) ? $appInfo->n_tin_number : '' }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><strong>11. Existing BIDA Registration Amendment</strong></legend>
                            <div class="table-responsive">
                                <table width="100%" id="existing_bra_table" class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th class="table-header">#</th>
                                        <th class="table-header">BRA Ref/Memo</th>
                                        <th class="table-header">Approved Date</th>
                                    </tr>
                                    </thead>

                                    @if(count($existing_bra) > 0)
                                        <?php $i = 1; ?>
                                        @foreach($existing_bra as $value)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>
                                                    {{ (!empty($value->bra_memo_no) ? $value->bra_memo_no : '') }}
                                                </td>
                                                <td>
                                                    {{ (!empty($value->bra_approved_date) ? date('Y-m-d', strtotime($value->bra_approved_date)) : '') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </table>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><strong>12. Why do you want to BIDA Registration Amendment?</strong></legend>
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span>Major remarks in brief</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($appInfo->major_remarks)) ? $appInfo->major_remarks : '' }}
                                </div>
                            </div>
                        </fieldset>

                        @if($appInfo->organization_status_id == 3 || $appInfo->n_organization_status_id == 3)
                            {{-- Description of machinery and equipment--}}
                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border"><strong>13. Description of machinery and equipment</strong></legend>
                                <table class="table table-responsive table-bordered">
                                    <thead>
                                    <tr>
                                        <td width="20%">Field name</td>
                                        <td class="bg-yellow" width="40%" colspan="2">Existing information (Latest BIDA Reg. Info.)</td>
                                        <td class="bg-green" width="40%" colspan="2">Proposed information</td>
                                    </tr>
                                    <tr>
                                        <td width="20%"></td>
                                        <td width="20%" class="light-yellow">Quantity</td>
                                        <td width="20%" class="light-yellow">Price (BDT)</td>
                                        <td width="20%" class="light-green">Quantity</td>
                                        <td width="20%" class="light-green">Price (BDT)</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Locally Collected</td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->machinery_local_qty) ? $appInfo->machinery_local_qty : '' }}
                                        </td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->machinery_local_price_bdt) ? $appInfo->machinery_local_price_bdt : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_machinery_local_qty) ? $appInfo->n_machinery_local_qty : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_machinery_local_price_bdt) ? $appInfo->n_machinery_local_price_bdt : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Imported</td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->imported_qty) ? $appInfo->imported_qty : '' }}
                                        </td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->imported_qty_price_bdt) ? $appInfo->imported_qty_price_bdt : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_imported_qty) ? $appInfo->n_imported_qty : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_imported_qty_price_bdt) ? $appInfo->n_imported_qty_price_bdt : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->total_machinery_qty) ? $appInfo->total_machinery_qty : '' }}
                                        </td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->total_machinery_price) ? $appInfo->total_machinery_price : '' }}
                                        </td>
                                        {{-- <td class="light-yellow">
                                            {{ !empty($appInfo->total_machinery_price) ? ($appInfo->total_machinery_price) : '' }}
                                        </td> --}}
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_total_machinery_qty) ? $appInfo->n_total_machinery_qty : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_total_machinery_price) ? $appInfo->n_total_machinery_price : '' }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </fieldset>

                            {{------------- Description of raw & packing materials-------------}}
                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border"><strong>14. Description of raw & packing materials</strong></legend>
                                <table class="table table-responsive table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Field name</th>
                                        <th class="bg-yellow">Existing information (Latest BIDA Reg. Info.)</th>
                                        <th class="bg-green">Proposed information</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Locally</td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->local_description) ? $appInfo->local_description : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_local_description) ? $appInfo->n_local_description : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Imported</td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->imported_description) ? $appInfo->imported_description : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_imported_description) ? $appInfo->n_imported_description : '' }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </fieldset>
                        @endif


                    </div>
                </div>
                {{--End registration informaton section--}}

                {{--Star list of director section--}}
                <div class="panel panel-info">
                    <div class="panel-heading"><strong>List of Directors</strong></div>
                    <div class="panel-body">
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><strong>Information of (Chairman/ Managing Director/ Or Equivalent):</strong></legend>
                            <div class="table-responsive">
                                <table class="table table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th width="30%">Field name</th>
                                        <th class="bg-yellow" width="35%">Existing information (Latest BIDA Reg. Info.)</th>
                                        <th class="bg-green" width="35%">Proposed information</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Full Name</td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->g_full_name) ? $appInfo->g_full_name : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_g_full_name) ? $appInfo->n_g_full_name : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Position/ Designation</td>
                                        <td class="light-yellow">
                                            {{ !empty($appInfo->g_designation) ? $appInfo->g_designation : '' }}
                                        </td>
                                        <td class="light-green">
                                            {{ !empty($appInfo->n_g_designation) ? $appInfo->n_g_designation : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Signature</td>
                                        <td class="light-yellow">
                                            @if(!empty($appInfo->g_signature))
                                                <img src="{{ url('uploads/'.$appInfo->g_signature)  }}" class="img-thumbnail" id="g_signature" alt="Signature">
                                            @endif
                                        </td>
                                        <td class="light-green">
                                            @if(!empty($appInfo->n_g_signature))
                                                <img src="{{ url('uploads/'.$appInfo->n_g_signature)  }}" class="img-thumbnail" id="g_signature" alt="Signature">
                                            @endif
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </fieldset>
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><strong>List of directors</strong></legend>
                            <div class="table-responsive">
                                <div id="load_list_of_director"></div>
                                @if($list_of_directors > 20)
                                    <br>
                                    <a href="javascript:void(0)" onclick="listOfDirectors('all', 'on')" id="load_list_of_director_data">Load more data</a>
                                @endif
                            </div>
                        </fieldset>
                    </div>
                </div>
                {{--End list of director section--}}

                {{--Star list of Machineries section--}}
                <div class="panel panel-info">
                    <div class="panel-heading"><strong>List of Machineries</strong></div>
                    <div class="panel-body">
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><strong>List of machinery to be imported</strong></legend>
                            <div id="load_imported_machinery_data"></div>
                            @if($importedMachineryData > 20)
                                <br>
                                <a href="javascript:void(0)" onclick="loadImportedMachineryData('all', 'on')" id="load_imported_data">Load more data</a>
                            @endif
                        </fieldset>
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><strong>List of machinery locally purchase/ procure</strong></legend>
                            <div id="load_local_machinery_data"></div>
                            @if($localMachineryData > 20)
                                <br>
                                <a href="javascript:void(0)" onclick="loadLocalMachineryData('all', 'on')" id="load_local_data">Load more data</a>
                            @endif
                        </fieldset>
                    </div>
                </div>
                {{--End list of Machineries section--}}

                {{--Star attachment--}}
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
                                            <a class="btn btn-xs btn-primary" target="_blank" href="{{ url('process/open-attachment/'.Encryption::encodeId($appInfo->process_type_id).'/'.Encryption::encodeId($appInfo->id)) }}"><i class="fa fa-file-pdf" aria-hidden="true"></i> Open all</a>
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
                                                            <a target="_blank" class="btn btn-xs btn-primary" title="" href="{{ URL::to('/uploads/'.(!empty($row->doc_file_path) ? $row->doc_file_path : '')) }}"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Open File</a>
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
                                            <td colspan="9"> No required documents! </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                {{--End attachment--}}

                {{--     Declaration and undertaking--}}
                <div class="mb0 panel panel-info">
                    <div class="panel-heading"><strong>Declaration and undertaking</strong></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div>
                                    <img style="width: 10px; height: auto;" src="{{ asset('assets/images/checked.png') }}"/>
                                    I do here by declare that the information given above is true to the best of my knowledge and I shall be liable for any false information/ statement is given.
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

{{--list of director, List of Machineries data load--}}
@include('BidaRegistrationAmendment::bra-ajax-dataload')

<script>
    $(function () {
        listOfDirectors(20, 'on');
        loadImportedMachineryData(20, 'on');
        loadLocalMachineryData(20, 'on');
        loadAnnualProductionCapacityData(20, 'on');
    });
</script>

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
            pagesplit: true
        };

        var html = '<div style="margin-top: 60px">' + element + '</div>';

        html2pdf().from(html).set(opt).toPdf().get('pdf').then(function (pdf) {
            var pageCount = pdf.internal.getNumberOfPages();

            pdf.setPage(1);
            pageWidth = pdf.internal.pageSize.getWidth();
            pageHeight = pdf.internal.pageSize.getHeight();

            var image = "data:image/png;base64,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";
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


</script>