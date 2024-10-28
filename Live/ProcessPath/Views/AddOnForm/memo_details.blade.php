@if((in_array($CurrentStatusId, [10, 11, 13, 14, 25])) || (in_array($CurrentStatusId, [5, 6]) && !empty($memo_info->memo_no) && !empty($memo_info->memo_date)) )
    <div class="wpe_duration form-group">
        <fieldset class="scheduler-border">
            <legend class="scheduler-border">Memo Details </legend>
            <div class="form-group">
                <div class="row">

                    <div class="col-md-4 form-group {{ $errors->has('memo_no') ? 'has-error' : '' }}" >
                        {!! Form::label('Memo Number', '', ['class' => 'required-star']) !!}
                        {!! Form::text('memo_no', $memo_info->memo_no, ['class' => 'form-control input-md', in_array($CurrentStatusId, [13, 14]) ? '' : 'readonly', 'id' => 'memo_no']) !!}
                    </div>

                    <div class="col-md-4{{$errors->has('memo_date') ? 'has-error': ''}}">
                        {!! Form::label('memo_date','Memo Date',['class'=>'text-left col-md-12']) !!}
                        <div class="col-md-12">
                            <div class="input-group date memo_date">
                                {!! Form::text('memo_date', (!empty($memo_info->memo_date) ? date('d-M-Y', strtotime($memo_info->memo_date)) : ''), ['class' => 'form-control input-md unsecure_datepicker', in_array($CurrentStatusId, [13, 14]) ? '' : 'readonly', 'placeholder'=>'dd-mm-yyyy', 'id' => 'memo_date']) !!}
                                <span class="input-group-addon"
                                    onclick="javascript:NewCssCal('start_date', 'ddMMMyyyy', 'arrow', '', '', '', '')">
                                    <span class="fa fa-calendar"></span>
                                </span>
                            </div>
                            {!! $errors->first('memo_date','<span class="help-block">:message</span>') !!}
                        </div>
                    </div>

                    

                    @if(in_array($CurrentStatusId, [13, 14]))
                        <div class="col-md-4 form-group {{ $errors->has('memo_attachment') ? 'has-error' : '' }}">
                            <label for="attach_file">Attach file
                                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title=""
                                    data-original-title="To select multiple files, hold down the CTRL or SHIFT key while selecting."></i>
                                <span class="text-danger" style="font-size: 9px; font-weight: bold">[File: *.pdf | Maximum 2 MB]</span>
                                
                                @if(!empty($memo_info->memo_attachment))
                                    <a style="margin-top: 5px;" target="_blank" class="btn btn-xs btn-primary" href="{{URL::to($memo_info->memo_attachment)}}">
                                        <i class="fa fa-file-pdf" aria-hidden="true"></i>
                                        Open File
                                    </a>
                                @endif

                            </label>
                            {!! Form::file('memo_attachment', ['class' => 'form-control input-md', 'id' => 'attach_file_id', 'multiple' => true, 'accept' => 'application/pdf', 'onchange' => 'uploadDocumentProcess(this.id)']) !!}
                            {!! $errors->first('memo_attachment', '<span class="help-block">:message</span>') !!}
                        </div>
                    @else
                        <div class="col-md-4">
                            {!! Form::label('memo_attachment','Memo Attachment',['class'=>'text-left col-md-12']) !!}
                            <div class="col-md-12">
                                @if(!empty($memo_info->memo_attachment))
                                    <a style="margin-top: 5px;" target="_blank" class="btn btn-xs btn-primary" href="{{URL::to($memo_info->memo_attachment)}}">
                                        <i class="fa fa-file-pdf" aria-hidden="true"></i>
                                        Open File
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </fieldset>
    </div>
@endif
<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

<script>
    $(document).ready(function () {
        $(".memo_date").datetimepicker({
            viewMode: 'days',
            format: 'DD-MMM-YYYY',
        });
    });

    function uploadDocumentProcess(id) {
        var file_id = document.getElementById(id);
        var file = file_id.files;
        if (file && file[0]) {
            if (!(file[0].type == 'application/pdf')) {
                swal({
                    type: 'error',
                    title: 'Oops...',
                    text: 'The file format is not valid! Please upload in pdf format.'
                });
                file_id.value = '';
                return false;
            }

            var file_size = parseFloat((file[0].size) / (1024 * 1024)).toFixed(1); //MB Calculation
            if (!(file_size <= 2)) {
                swal({
                    type: 'error',
                    title: 'Oops...',
                    text: 'Max file size 2MB. You have uploaded ' + file_size + 'MB'
                });
                file_id.value = '';
                return false;
            }
        }
    }
</script>