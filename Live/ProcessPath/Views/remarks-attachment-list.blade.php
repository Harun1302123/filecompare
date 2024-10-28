@if (!empty($remarks_attachment))
    @foreach ($remarks_attachment as $remarks_attachment)
        <a target="_blank" href="{{ url($remarks_attachment->file) }}" style="margin-top: 10px;"
           class="btn btn-primary btn-xs">
            <i class="fa fa-save"></i> Download Attachment
        </a>
    @endforeach
@else
    No attachments were added in the last processing
@endif