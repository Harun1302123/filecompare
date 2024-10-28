<div class="wpc_effect form-group">
    <fieldset class="scheduler-border">
        <legend class="scheduler-border">Cancellation Effect Date</legend>
        <div class="form-group">
            <div class="row">
                <div class="col-md-4 {{$errors->has('approved_effect_date') ? 'has-error': ''}}">
                    {!! Form::label('approved_effect_date','Start Date',['class'=>'text-left col-md-12']) !!}
                    <div class="col-md-12">
                        <div class="input-group date start_date_div">
                            {!! Form::text('approved_effect_date', (!empty($wpc_info->approved_effect_date) ? date('d-M-Y', strtotime($wpc_info->approved_effect_date)) : ''), ['class' => 'form-control input-md unsecure_datepicker', 'placeholder'=>'dd-mm-yyyy', 'id' => 'start_date']) !!}
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
    $(document).ready(function () {
        $.ajaxSetup({async: false});
        //getHelpText();

        @if($CurrentStatusId != 8)
        $('label[for="approved_effect_date"]').addClass('required-star');
        $("#start_date").addClass('required');
        @endif


        // For Old datepicker
        if ($(".unsecure_datepicker").length > 0) {
            //
        }
            // For Bootstrap datepicker
        // also, this is default
        else {

            $(".start_date_div").datetimepicker({
                viewMode: 'days',
                format: 'DD-MMM-YYYY',
            });
        }
    });
</script>