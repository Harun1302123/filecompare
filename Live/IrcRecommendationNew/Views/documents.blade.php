<div class="modal fade" id="recentAttachmentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content load_modal"></div>
    </div>
</div>

<div class="panel panel-info">
    <div class="panel-heading"><strong>Necessary documents to be attached here (Only PDF file)</strong>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered display" width="100%">
                <thead>
                <tr>
                    <th>No.</th>
                    <th colspan="6">Required attachment (you may prefer to select file from recent attachment) <i class="fa fa-question-circle" style="cursor: pointer" data-toggle="tooltip" title="" data-original-title="If your present company has already attached any file while doing application previously, there will be a button as 'recent attachment' after the name of attachment" aria-describedby="tooltip"></i></th>
                    <th colspan="2">Attached PDF file (Each File Max. size 2MB)</th>
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

                            <td colspan="6">
                                {!! $row->doc_name !!}
                                <a class='btn btn-xs btn-primary' data-toggle='modal' data-target='#recentAttachmentModal' onclick='openModal(this)'
                                   data-action='{{ url('process/recent-attachment/'. \App\Libraries\Encryption::encodeId($row->id)) }}'>
                                    <i class='fa fa-file-pdf'></i> Recent attachment
                                </a>
                            </td>

                            <td colspan="2">
                                <input name="document_id_<?php echo $row->id; ?>" type="hidden" value="{{(!empty($row->document_id) ? $row->document_id : '')}}">
                                <input type="hidden" value="{!!  $row->doc_name !!}" id="doc_name_<?php echo $row->id; ?>" name="doc_name_<?php echo $row->id; ?>"/>
                                <input name="file<?php echo $row->id; ?>"
                                       <?php if (empty($row->doc_file_path) && empty($allRequestVal["file$row->id"]) && $row->doc_priority == "1") {
                                           echo "class='required'";
                                       } ?>
                                       id="file<?php echo $row->id; ?>" type="file" size="20"
                                       onchange="uploadDocument('preview_<?php echo $row->id; ?>', this.id, 'validate_field_<?php echo $row->id; ?>', '<?php echo $row->doc_priority; ?>')"/>

                                {{-- if this document hav attachment then show it --}}
                                @if(!empty($row->doc_file_path))
                                    <div class="save_file saved_file_{{$row->id}}">
                                        <a target="_blank" class="documentUrl btn btn-xs btn-primary" href="{{URL::to('/uploads/'.(!empty($row->doc_file_path) ? $row->doc_file_path : ''))}}"
                                           title="{{$row->doc_name}}">
                                            <i class="fa fa-file-pdf" aria-hidden="true"></i>
                                            Open File
                                        </a>
                                        <a href="javascript:void(0)" onclick="removeAttachedFile({!! $row->id !!}, {!! $row->doc_priority !!})"><span class="btn btn-xs btn-danger"><i class="fa fa-times"></i></span> </a>
                                    </div>
                                @endif

                                <div id="preview_<?php echo $row->id; ?>">
                                    <input type="hidden" value="<?php echo !empty($row->doc_file_path) ? $row->doc_file_path : ''?>"
                                           id="validate_field_<?php echo $row->id; ?>"
                                           name="validate_field_<?php echo $row->id; ?>"
                                           class="<?php echo $row->doc_priority == "1" ? "required" : '';  ?>"/>
                                </div>

                            </td>
                        </tr>
                        <?php $i++; ?>
                    @endforeach
                @else
                    <tr>
                        <td colspan="9" style="text-align: center"><span class="label label-info">No Required Documents!</span></td>
                    </tr>
                @endif
                <tr>
                    <td>N.B</td>
                    <td colspan="6">All documents shall have to be attested by the Chairman/ CEO / Managing dirctor/ Country Manager/ Chief executive of the Company/ firms.</td>
                    <td colspan="2">Document's must be submitted by an authorized person of the organization including the letter of authorization.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>


<script>
    $('[data-toggle="tooltip"]').tooltip();

    function openModal(btn) {
        var this_action = btn.getAttribute('data-action');
        var data_target = btn.getAttribute('data-target');
        if (this_action != '') {
            $.get(this_action, function (data, success) {
                if (success === 'success') {
                    $(data_target + ' .load_modal').html(data);
                } else {
                    $(data_target + ' .load_modal').html('Unknown Error!');
                }
                $(data_target).modal('show', {backdrop: 'static'});
            });
        }
    }
</script>