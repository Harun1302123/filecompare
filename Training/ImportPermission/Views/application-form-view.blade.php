    <?php
    $accessMode = ACL::getAccsessRight('ImportPermission');
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
                            Application for Import Permission Certificate (IP)
                        </strong>
                    </div>
                    <div class="pull-right" data-html2canvas-ignore="true">

                        @if (isset($appInfo) && $appInfo->status_id == 25 && isset($appInfo->certificate_link) && in_array(Auth::user()->user_type, ['1x101','2x202', '4x404', '5x505']))
                            <a href="{{ url($appInfo->certificate_link) }}" class="btn show-in-view btn-sm btn-info"
                            title="Download Approval Copy" target="_blank">
                                <i class="fa  fa-file-pdf"></i>
                                Download Approval Copy
                            </a>
                        @endif

                        <a class="btn btn-sm btn-success" data-toggle="collapse" href="#paymentInfo" role="button"
                            aria-expanded="false" aria-controls="collapseExample">
                            <i class="far fa-money-bill-alt"></i>
                            Payment Info.
                        </a>

                        @if(!in_array($appInfo->status_id,[-1,5,6]))
                            <a href="javascript:void(0)" class="btn btn-danger btn-sm" title="Download Approval Copy" id="html2pdf">
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

                    <ol class="breadcrumb">
                        <li><strong>Tracking no. : </strong>{{ $appInfo->tracking_no  }}</li>
                        <li><strong> Date of Submission: </strong> {{ \App\Libraries\CommonFunction::formateDate($appInfo->submitted_at) }} </li>
                        <li><strong>Current Status : </strong> {{ $appInfo->status_name }}</li>
                        <li><strong>Current Desk :</strong> {{ $appInfo->desk_id != 0 ? \App\Libraries\CommonFunction::getDeskName($appInfo->desk_id) : 'Applicant' }} </li>
                    </ol>

                    {{--Payment information--}}
                    @include('ProcessPath::payment-information')

                    {{-- Basic Information--}}
                    <div class="panel panel-info">
                        <div class="panel-heading"><strong>Basic Information</strong></div>
                        <div class="panel-body">
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-5 col-xs-6">
                                            <span class="v_label">
                                                Did you receive your BIDA Registration/ BIDA Registration amendment approval online OSS?
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
                                                Please give your approved BIDA Registration/ BIDA Registration amendment Tracking ID.
                                            </span>
                                                <span class="pull-right">&#58;</span>
                                            </div>
                                            <div class="col-md-7 col-xs-6">
                                                <a href="{{$ref_app_url}}" target="_blank">
                                                    <span class="label label-success label_tracking_no">{{ (empty($appInfo->ref_app_tracking_no) ? '' : $appInfo->ref_app_tracking_no) }}</span>
                                                </a>
                                                &nbsp;{!! \App\Libraries\CommonFunction::getCertificateByTrackingNo($appInfo->ref_app_tracking_no) !!}
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-5 col-xs-6">
                                            <span class="v_label">
                                                Approved Date
                                            </span>
                                                <span class="pull-right">&#58;</span>
                                            </div>
                                            <div class="col-md-7 col-xs-6 mt-5">

                                                {{ (!empty($appInfo->ref_app_approve_date)) ?  date('d-M-Y',strtotime($appInfo->ref_app_approve_date)) : ''  }}

                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5 col-xs-6">
                                            <span class="v_label">
                                                Registration No.
                                            </span>
                                                <span class="pull-right">&#58;</span>
                                            </div>
                                            <div class="col-md-7 col-xs-6 mt-5">
                                        
                                                {{ (!empty($appInfo->reg_no)) ?  $appInfo->reg_no : ''  }}
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
                                                {{ (!empty($appInfo->manually_approved_br_no)) ? $appInfo->manually_approved_br_no : ''  }}
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-5 col-xs-6">
                                            <span class="v_label">
                                                Approved Date
                                            </span>
                                                <span class="pull-right">&#58;</span>
                                            </div>
                                            <div class="col-md-7 col-xs-6">
                                                {{ (!empty($appInfo->manually_approved_br_date)) ? $appInfo->manually_approved_br_date : ''  }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                        </div>
                    </div>

                    
                    {{--Company basic information--}}
                    <div class="panel panel-info">
                        <div class="panel-heading"><strong>A. Company Information</strong></div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Name of the organization/ company/ industrial project</span>
                                    <span class="pull-right">&#58;</span>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    {{ (!empty($appInfo->company_name)) ? $appInfo->company_name : '' }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5 col-xs-6">
                                    <span class="v_label">Name of the organization/ company/ industrial project (বাংলা)</span>
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
                                <legend class="scheduler-border"><strong>Other info. based on your business class (Code
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
                                    {{ (!empty($appInfo->major_activities)) ? $appInfo->major_activities :'N/A'  }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{--Information of Principal Promoter/Chairman/Managing Director/CEO/Country Manager--}}
                    <div class="panel panel-info">
                        <div class="panel-heading"><strong>B. Information of Principal Promoter/ Chairman/ Managing
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
                        <div class="panel-heading"><strong>C. Office Address</strong></div>
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
                        <div class="panel-heading"><strong>D. Factory Address(This would be IRC address)</strong></div>
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

                    <div class="panel panel-info">
                                    
                        <div class="panel-body">
                            {{--1. Project status--}}
                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border">Desired office:</legend>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4>You have selected <b>{{ $desire_office->des_office_name }}, </b>{{ $desire_office->des_office_address }} .</h4>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                        </div>
                    </div>

                    {{--Registration Information--}}
                    <div class="panel panel-info">
                        <div class="panel-heading"><strong>Registration Information</strong></div>
                        <div class="panel-body">
                            <!--                             1. Project status-->
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
                                <legend class="scheduler-border">

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
                                                    @if(count($annual_production_capacity)>0)
                                                        @foreach($annual_production_capacity as $value1)
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
                            <!--                            3. Date of commercial operation-->
                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border">3. Date of commercial operation</legend>
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
                            <!--                            4. Sales (in 100%)-->
                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border">4. Sales (in 100%)</legend>
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
                            <!--                            5. Manpower of the organization-->
                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border">5. Manpower of the organization</legend>
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
                            <!--                            6. Investment Information-->
                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border">6. Investment</legend>
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
                            <!--                            7. Source of Finance-->
                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border">7. Source of Finance</legend>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered" cellspacing="0"
                                                    width="100%">
                                                    <tbody>
                                                    @if($appInfo->organization_status_name != 'Foreign')
                                                    <tr>
                                                        <td width="50%"><strong>(a)</strong> Local Equity (Million)</td>
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
                            <!--                            8. Public utility service-->
                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border">8. Public utility service</legend>
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
                                                    <img src="{{ asset('assets/images/checked.png') }}" alt="Others" width="10" height="10"/> Others
                                                </label>
                                            @endif

                                        </div>
                                        @if($appInfo->public_others == 1 && !empty($appInfo->public_others_field ))
                                            <div class="col-md-12" style="padding-top: 5px;">
                                                {{ $appInfo->public_others_field }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </fieldset>
                            <!--                            9. Trade licence details-->
                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border">9. Trade licence details</legend>
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
                            <!--                            10. TIIN details-->
                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border">10. TIN</legend>
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

                                    </div>
                                </div>
                            </fieldset>
                            <!--                            11. Description of machinery and equipment-->
                            <!-- <fieldset class="scheduler-border">
                                <legend class="scheduler-border">
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
                            </fieldset> -->
                            <!--                            12. Description of raw & packing materials-->
                            <!-- <fieldset class="scheduler-border">
                                <legend class="scheduler-border">
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
                            </fieldset> -->


                        </div>
                    </div>

                        {{--List of Machineries--}}
                        <div class="panel panel-info">
                            <div class="panel-heading"><strong>List of Machineries</strong></div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border"> List of total importable machinery as registered with BIDA</legend>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="table-responsive">
                                                            <table class="table table-striped table-bordered" cellspacing="0" width="100%">
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
                                            <legend class="scheduler-border"> List of machinery to be imported under this application </legend>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="table-responsive">
                                                            <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                                <thead>
                                                                <tr>
                                                                    <th valign="top" class="text-center">#</th>
                                                                    {{-- <th valign="top" class="text-center" width="50%">Name of machineries with Standard Accessories</th> --}}
                                                                    <th valign="top" class="text-center">Name of machineries with Standard Accessories</th>
                                                                    <th valign="top" class="text-center">Quantity  BIDA Reg. /Amend ment</th>
                                                                    <th valign="top" class="text-center">Remaining Quantity</th>
                                                                    <th valign="top" class="text-center">Required Quantity</th>
                                                                    <th valign="top" class="text-center">Machinery Type</th>
                                                                    <th valign="top" class="text-center">H.S. Code&nbsp&nbsp</th>
                                                                    <th valign="top" class="text-center">Bill of Lading No.</th>
                                                                    <th valign="top" class="text-center" width="10%">Bill of Lading Date</th>
                                                                    <th valign="top" class="text-center">Invoice No</th>
                                                                    <th valign="top" class="text-center" width="10%">Invoice Date</th>
                                                                    <th valign="top" class="text-center">Total value as per Invoice</th>
                                                                    <th valign="top" class="text-center">Currency</th>
                                                                    <th valign="top" class="text-center">Total value equivalent (BDT)</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @if(count($listOfMechineryImportedSpare) > 0)
                                                                        <?php $i = 1; ?>
                                                                    @foreach($listOfMechineryImportedSpare as $importedSpare)
                                                                        <tr>
                                                                            <td>{{ $i++ }}</td>
                                                                            <td>{{ $importedSpare->name }}</td>
                                                                            <td>{{ $importedSpare->quantity }}</td>
                                                                            <td>{{ $importedSpare->remaining_quantity}}</td>
                                                                            <td>{{ $importedSpare->required_quantity }}</td>
                                                                            <td>{{ $importedSpare->machinery_type }}</td>
                                                                            <td>{{ $importedSpare->hs_code }}</td>
                                                                            <td>{{ $importedSpare->bill_loading_no }}</td>
                                                                            <td>{{ \Carbon\Carbon::parse($importedSpare->bill_loading_date)->format('d-M-Y') }}</td>
                                                                            {{-- <td>{{ $importedSpare->bill_loading_date }}</td> --}}
                                                                            <td>{{ $importedSpare->invoice_no }}</td>
                                                                            {{-- <td>{{ $importedSpare->invoice_date }}</td> --}}
                                                                            <td>{{ \Carbon\Carbon::parse($importedSpare->invoice_date)->format('d-M-Y') }}</td>
                                                                            <td>{{ $importedSpare->total_value_equivalent_usd ? number_format($importedSpare->total_value_equivalent_usd, 2) : 0.00 }}</td>
                                                                            <td>{{ $importedSpare->total_value_ccy ? $importedSpare->currency_code : ''  }}</td>
                                                                            <td>{{ $importedSpare->total_value_as_per_invoice ? number_format($importedSpare->total_value_as_per_invoice, 2) : 0.00 }}</td>

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
                                                        {{ ((!empty($appInfo->created_at)) ? date('d-M-Y', strtotime($appInfo->created_at)) : '') }}
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