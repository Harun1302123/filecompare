@extends('layouts.plane')

@section('title', $processInfo->tracking_no)

@section('style')
    <style>
        /*body {*/
        /*    overflow: hidden; !* Hide scrollbars *!*/
        /*}*/

        /* Hide scrollbar for Chrome, Safari and Opera */
        body::-webkit-scrollbar {
            display: none;
        }

        /* Hide scrollbar for IE, Edge and Firefox */
        body {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }

        html, body, #attachment_panel {
            height: 100%;
        }
    </style>
@endsection

@section('body')
    <div class="col-md-12" id="attachment_panel">
        <div class="panel panel-info" style="margin-bottom: 0;">
            <div class="panel-heading" id="panel_heading">
                <strong>Tracking No.: {{ $processInfo->tracking_no }}</strong>
            </div>
            <div class="panel-body" style="padding: 10px;">
                @if(count($document) > 0)
                    <div id="docTabs">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist" id="tab_list">
                            <?php $i = 1; ?>
                            @foreach($document as $row)
                                @if(!empty($row->doc_file_path))
                                    <li role="presentation" class="<?php if ($i == 1) {
                                        echo 'active';
                                    } ?>">
                                        <a style="padding: 5px 10px;" href="#tabs{{$i}}" data-toggle="tab">
                                            @if(!empty($row->short_note))
                                                {{ $i }}. {{ $row->short_note }}
                                            @else
                                                {{ $i }}. Doc-{{ $i }}
                                            @endif
                                        </a>
                                    </li>
                                @endif
                                <?php $i++; ?>
                            @endforeach
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <?php $i = 1; ?>
                            @foreach($document as $row)
                                @if(!empty($row->doc_file_path))
                                    <div role="tabpanel" class="tab-pane <?php if ($i == 1) {
                                        echo 'active';
                                    }?>" id="tabs{{$i}}">
                                        @if(!empty($row->doc_file_path))
                                            <h4>
                                                <a href="{{url('/uploads/' . $row->doc_file_path)}}" download="{{ $row->doc_name }}"><i class="fa fa-download"></i> {{ $row->doc_name }}</a>
                                            </h4>
                                            <iframe src="/vendor/ViewerJS/index.html?zoom=page-width#../../<?php echo 'uploads/' . $row->doc_file_path; ?>" width="100%" style="text-align: center;"></iframe>
                                        @else
                                            <div class="">No file found!</div>
                                        @endif
                                    </div>
                                @endif
                                <?php $i++; ?>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="text-center text-danger data-not-found">Attachment not found</p>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('footer-script')
    <script>
        $(function () {
            var attachment_panel = $("#attachment_panel").outerHeight();
            var panel_heading = $("#panel_heading").outerHeight();
            var tab_list = $("#tab_list").outerHeight();
            var short_name = $("#tabs1 h4").outerHeight();
            var iframe_height = (attachment_panel - (panel_heading+20+tab_list+short_name+30))+'px'; // 20=padding, 30=diff
            $("iframe").each(function( index ) {
                $( this ).css('height', iframe_height);
            });
        });
    </script>
@endsection