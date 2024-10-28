<div class="wpe_duration form-group">
    <fieldset class="scheduler-border">
        <legend class="scheduler-border">Desired Duration</legend>
        <div class="form-group">
            <div class="row">
                <div class="col-md-3 {{$errors->has('approved_duration_start_date') ? 'has-error': ''}}">
                    {!! Form::label('approved_duration_start_date','Start Date',['class'=>'text-left col-md-12']) !!}
                    <div class="col-md-12">
                        <div class="input-group date start_date_div">
                            {!! Form::text('approved_duration_start_date', (!empty($wpn_info->approved_duration_start_date) ? date('d-M-Y', strtotime($wpn_info->approved_duration_start_date)) : ''), ['class' => 'form-control input-md date unsecure_datepicker', 'placeholder'=>'dd-mm-yyyy', 'id' => 'start_date']) !!}
                            <span class="input-group-addon"
                                  onclick="javascript:NewCssCal('start_date', 'ddMMMyyyy', 'arrow', '', '', '', '')">
                                <span class="fa fa-calendar"></span>
                            </span>
                        </div>
                        {!! $errors->first('approved_duration_start_date','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
                <div class="col-md-3 {{$errors->has('approved_duration_end_date') ? 'has-error': ''}}">
                    {!! Form::label('approved_duration_end_date','End Date',['class'=>'text-left col-md-12']) !!}
                    <div class="col-md-12">
                        <div class="input-group date end_date_div">
                            {!! Form::text('approved_duration_end_date', (!empty($wpn_info->approved_duration_end_date) ? date('d-M-Y', strtotime($wpn_info->approved_duration_end_date)) : ''), ['class' => 'form-control input-md unsecure_datepicker', 'placeholder'=>'dd-mm-yyyy', 'id' => 'end_date']) !!}
                            <span class="input-group-addon"
                                  onclick="javascript:NewCssCal('end_date', 'ddMMMyyyy', 'arrow', '', '', '', '')">
                                <span class="fa fa-calendar"></span>
                            </span>

                        </div>
                        <span class="text-danger" style="font-size: 12px; font-weight: bold" id="date_compare_error"></span>

                        {!! $errors->first('approved_duration_end_date','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
                <div class="col-md-3 {{$errors->has('approved_desired_duration') ? 'has-error': ''}}">
                    {!! Form::label('approved_desired_duration','Desired Duration (in days)',['class'=>'text-left col-md-12']) !!}
                    <div class="col-md-12">
                        {!! Form::text('approved_desired_duration', $wpn_info->approved_desired_duration, ['class' => 'form-control input-md']) !!}
                        {!! $errors->first('approved_desired_duration','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
                <div class="col-md-3 {{$errors->has('approved_duration_amount') ? 'has-error': ''}}">
                    {!! Form::label('approved_duration_amount','Payable amount',['class'=>'text-left col-md-12']) !!}
                    <div class="col-md-12">
                        {!! Form::text('approved_duration_amount', $wpn_info->approved_duration_amount, ['class' => 'form-control input-md']) !!}
                        {!! $errors->first('approved_duration_amount','<span class="help-block">:message</span>') !!}

                        <span class="text-danger" style="font-size: 12px; font-weight: bold"
                              id="approved_duration_year"></span>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
</div>
<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

<script>
    $(document).ready(function () {

        $.ajaxSetup({async: false});
        //getHelpText();


        @if($CurrentStatusId != 8)
        $('label[for="approved_duration_start_date"]').addClass('required-star');
        $("#start_date").addClass('required');
        $('label[for="approved_duration_end_date"]').addClass('required-star');
        $("#end_date").addClass('required');
        $('label[for="approved_desired_duration"]').addClass('required-star');
        $("#approved_desired_duration").addClass('required');
                @endif


        var process_id = '{{ $process_type_id }}';
        var dd_startDateDivClass = 'start_date_div';
        var dd_endDateDivClass = 'end_date_div';

        var dd_startDateValID = 'start_date';
        var dd_endDateValID = 'end_date';

        var dd_show_durationID = 'approved_desired_duration';
        var dd_show_amountID = 'approved_duration_amount';
        var dd_show_yearID = 'approved_duration_year';

        // For Old datepicker
        if ($(".unsecure_datepicker").length > 0) {

            $("#" + dd_startDateValID).on("focusin", function (e) {

                var startDateVal = $(this).val();

                if (startDateVal != '') {
                    // Min value set for end date
                    // $("." + dd_endDateDivClass).data("DateTimePicker").minDate(e.date);
                    var endDateVal = $("#" + dd_endDateValID).val();
                    if (endDateVal != '') {
                        getDesiredDurationAmount(process_id, startDateVal, endDateVal, dd_show_durationID, dd_show_amountID, dd_show_yearID);
                    } else {
                        $("#" + dd_endDateValID).addClass('error');
                    }
                } else {
                    $("#" + dd_show_durationID).val('');
                    $("#" + dd_show_amountID).val('');
                    $("#" + dd_show_yearID).text('');
                }
            });

            $("#" + dd_endDateValID).on("focusin", function (e) {

                // Max value set for start date
                // $("." + dd_startDateDivClass).data("DateTimePicker").maxDate(e.date);

                var startDateVal = $("#" + dd_startDateValID).val();

                if (startDateVal === '') {
                    $("#" + dd_startDateValID).addClass('error');
                } else {
                    // var day = moment(startDateVal, ['DD-MMM-YYYY']);
                    //var minStartDate = moment(day).add(1, 'day');
                    // $("." + dd_endDateDivClass).data("DateTimePicker").minDate(day);
                }

                var endDateVal = $("#" + dd_endDateValID).val();

                if (startDateVal != '' && endDateVal != '') {
                    getDesiredDurationAmount(process_id, startDateVal, endDateVal, dd_show_durationID, dd_show_amountID, dd_show_yearID);
                } else {
                    $("#" + dd_show_durationID).val('');
                    $("#" + dd_show_amountID).val('');
                    $("#" + dd_show_yearID).text('');
                }
            });
        }
        // For Bootstrap datepicker
        // also, this is default
        else {

            $(".start_date_div").datetimepicker({
                viewMode: 'days',
                format: 'DD-MMM-YYYY',
            });
            $(".end_date_div").datetimepicker({
                viewMode: 'days',
                format: 'DD-MMM-YYYY',
            });

            $("." + dd_startDateDivClass).on("dp.change", function (e) {

                var startDateVal = $("#" + dd_startDateValID).val();

                if (startDateVal != '') {
                    // Min value set for end date
                    $("." + dd_endDateDivClass).data("DateTimePicker").minDate(e.date);
                    var endDateVal = $("#" + dd_endDateValID).val();
                    if (endDateVal != '') {
                        getDesiredDurationAmount(process_id, startDateVal, endDateVal, dd_show_durationID, dd_show_amountID, dd_show_yearID);
                    } else {
                        $("#" + dd_endDateValID).addClass('error');
                    }
                } else {
                    $("#" + dd_show_durationID).val('');
                    $("#" + dd_show_amountID).val('');
                    $("#" + dd_show_yearID).text('');
                }
            });

            $("." + dd_endDateDivClass).on("dp.change", function (e) {

                // Max value set for start date
                $("." + dd_startDateDivClass).data("DateTimePicker").maxDate(e.date);

                var startDateVal = $("#" + dd_startDateValID).val();

                if (startDateVal === '') {
                    $("#" + dd_startDateValID).addClass('error');
                } else {
                    var day = moment(startDateVal, ['DD-MMM-YYYY']);
                    //var minStartDate = moment(day).add(1, 'day');
                    $("." + dd_endDateDivClass).data("DateTimePicker").minDate(day);
                }

                var endDateVal = $("#" + dd_endDateValID).val();

                if (startDateVal != '' && endDateVal != '') {
                    getDesiredDurationAmount(process_id, startDateVal, endDateVal, dd_show_durationID, dd_show_amountID, dd_show_yearID);
                } else {
                    $("#" + dd_show_durationID).val('');
                    $("#" + dd_show_amountID).val('');
                    $("#" + dd_show_yearID).text('');
                }
            });
        }
    });
</script>