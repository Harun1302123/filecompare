<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span>
    </button>
    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-file-pdf-o"></i> Recently uploaded documents</h4>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-lg-12" style="max-height:355px; overflow-y: scroll">
            <table class="table table-bordered">
                <tr class="alert alert-info">
                    <th>{{ (isset($recent_doc_list[0]) ? $recent_doc_list[0]->doc_name : 'Attachment not found') }}</th>
                    <th>Action</th>
                </tr>
                @forelse($recent_doc_list as $key => $attachment)
                    <tr class="productRow">
                        <td>
                            {!! Form::hidden("app_documents_id[$key]", $attachment->id) !!}
                            <b>Tracking No. : </b> {{ $attachment->tracking_no }} &nbsp;&nbsp;&nbsp;&nbsp; <b>Uploaded at : </b>{{ $attachment->updated_at }}
                        </td>
                        <td width="15%">
                            <a href="{{ url('/uploads/'.$attachment->doc_file_path) }}" target="_blank"
                               class="btn btn-info btn-xs"><i
                                        class="fa fa-link"></i> Open</a>
                            <button type="button" class="btn btn-danger btn-xs"
                                    onclick="addAttachment('{{ $doc_id }}', '{{ $attachment->doc_file_path }}')"><i
                                        class="fa fa-plus"></i> Add
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr class="productRow">
                        <td colspan="2">
                            No recent attachment found
                        </td>
                    </tr>
                @endforelse
            </table>
        </div>
    </div>
</div>

<div class="modal-footer" style="text-align:left;">
    <div class="pull-right">
        {!! Form::button('<i class="fa fa-times"></i> Close', array('type' => 'button', 'id' => 'modal_close_btn', 'class' => 'btn btn-danger', 'data-dismiss' => 'modal')) !!}
    </div>
    <div class="clearfix"></div>
</div>


<script>
    function addAttachment(doc_id, doc_path) {

        // check that, there have a file already added from recent attachment
        // if have, then remove it
        var exist_element = document.getElementById('label_file' + doc_id);
        if (exist_element) {
            exist_element.parentNode.removeChild(exist_element);
        }

        // create a new element for chosen attachment
        var label_element = '<label class="saved_file_' + doc_id + '" id="label_file' + doc_id + '">' +
            '<br>' +
            '<b>File: ' + doc_path +
            ' <a href="javascript:void(0)" onclick="EmptyFile(' + doc_id + ')"><span class="btn btn-xs btn-danger"><i class="fa fa-times"></i></span> </a>' +
            '</b>' +
            '</label>';
        // Select an element after which the new element will be attached
        var insertAt = document.getElementById('file' + doc_id);

        // insert the new element at after of selected element
        insertAt.insertAdjacentHTML("afterend", label_element);
        // insertAt.parentNode.insertBefore(label_element, insertAt);


        // Add the attached attachment's address to the file's hidden input
        document.getElementById('validate_field_' + doc_id).value = doc_path;

        // Removal if there is a required class of input field
        insertAt.classList.remove('required');

        // Close the recent attachment modal
        document.getElementById('modal_close_btn').click();
    }
</script>