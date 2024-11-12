<div class="opc_effect form-group">
    <fieldset class="scheduler-border">
        <legend class="scheduler-border">Cancellation Effect Date</legend>
        <div class="form-group">
            <div class="row">
                <div class="col-md-4 {{$errors->has('approved_effect_date') ? 'has-error': ''}}">
                    {!! Form::label('approved_effect_date','Start Date',['class'=>'text-left col-md-12']) !!}
                    <div class="col-md-12">
                        <div class="input-group date">
                            {!! Form::text('approved_effect_date', (!empty($opc_info->approved_effect_date) ? date('d-M-Y', strtotime($opc_info->approved_effect_date)) : ''), ['class' => 'unsecure_datepicker form-control input-md', 'placeholder'=>'dd-mm-yyyy', 'id' => 'start_date']) !!}
                            <span class="input-group-addon"
                                  onclick="javascript:NewCssCal('start_date', 'ddMMMyyyy', 'arrow', '', '', '', '')">
                                <span class="fa fa-calendar"></span>
                            </span>
                        </div>
                        {!! $errors->first('approved_effect_date','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
</div>


<script>
    $(function () {
//        $('#datetimepicker6').datetimepicker({
//            viewMode: 'days',
//            format: 'DD-MMM-YYYY',
//            minDate: 'now'
//        });

        @if($CurrentStatusId != 8)
        $('label[for="approved_effect_date"]').addClass('required-star');
        $("#start_date").addClass('required');
        @endif

        // For Old datepicker
        if ($(".unsecure_datepicker").length > 0) {
            //
        }
    });
</script>