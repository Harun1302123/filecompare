<div class="wpe_duration form-group">
    <fieldset class="scheduler-border">
        <legend class="scheduler-border">Inspection Submission Deadline Date</legend>
        <div class="form-group">
            <div class="row">
                <div class="col-md-12 {{$errors->has('io_submission_deadline') ? 'has-error': ''}}">
                    {!! Form::label('io_submission_deadline','Inspection Submission Deadline',['class'=>'text-left col-md-12']) !!}
                    <div class="col-md-4">
                        <div class="input-group date io_submission_deadline">
                            {!! Form::text('io_submission_deadline', '', ['class' => 'form-control input-md required', 'placeholder'=>'dd-mm-yyyy', 'id' => 'io_submission_deadline']) !!}
                            <span class="input-group-addon"
                                  onclick="javascript:NewCssCal('start_date', 'ddMMMyyyy', 'arrow', '', '', '', '')">
                                <span class="fa fa-calendar"></span>
                            </span>
                        </div>
                        {!! $errors->first('io_submission_deadline','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
</div>

<script>
    $(document).ready(function () {
        $(".io_submission_deadline").datetimepicker({
            viewMode: 'days',
            format: 'DD-MMM-YYYY',
        });
    });
</script>