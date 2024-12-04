<link rel="stylesheet" href="{{ asset("vendor/datepicker/datepicker.min.css") }}">
<style>
    .margin-bottom {
        margin-bottom: 3px;
    }

    .radio-inline {
        padding-top: 0px !important;
    }

</style>
{!! Form::open(array('url' => '/irc-recommendation-third-adhoc/director-form-update','method' => 'POST', 'class' => 'form-horizontal smart-form','id'=>'directorForm',
        'enctype' =>'multipart/form-data', 'files' => 'true', 'role' => 'form')) !!}

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span>
    </button>
    <h4 class="modal-title" id="myModalLabel">
        Edit
        @if($directorInfo->identity_type == 'nid')
            NID
        @elseif($directorInfo->identity_type == 'tin')
            ETIN
        @else
            passport
        @endif
        information
    </h4>
</div>

<div class="modal-body">
    <div class="col-md-12">
        <div class="errorMsg alert alert-danger alert-dismissible hidden">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        </div>
        <div class="successMsg alert alert-success alert-dismissible hidden">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        </div>
        <input type="hidden" name="id" value="{{ Encryption::encodeId($directorInfo->id) }}">

        @if($directorInfo->identity_type == 'nid')
            <table class="table table-responsive table-bordered">
                <tbody>
                <tr>
                    <td>NID</td>
                    <td class="light-yellow">
                        <div class="form-group margin-bottom">
                            <div class="col-md-12 {{$errors->has('nid_etin_passport') ? 'has-error': ''}}">
                                {!! Form::text('nid_etin_passport', !empty($directorInfo->nid_etin_passport) ? $directorInfo->nid_etin_passport : '', ['class' => 'form-control input-md   ', 'id'=>'nid_etin_passport']) !!}
                                {!! $errors->first('nid_etin_passport','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Name</td>
                    <td class="light-yellow">
                        <div class="form-group margin-bottom">
                            <div class="col-md-12 {{$errors->has('l_director_name') ? 'has-error': ''}}">
                                {!! Form::text('l_director_name', !empty($directorInfo->l_director_name) ? $directorInfo->l_director_name : '', ['class' => 'form-control input-md   ', 'id'=>'l_director_name']) !!}
                                {!! $errors->first('l_director_name','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </td>
                    
                </tr>
                <tr>
                    <td>Date of Birth</td>
                    <td class="light-yellow">
                        <div class="input-group date" data-date-format="dd-mm-yyyy">
                            {!! Form::text('date_of_birth', !empty($directorInfo->date_of_birth) ? date('d-M-Y', strtotime($directorInfo->date_of_birth)) : '', ['class'=>'form-control input-md datepicker', 'id' => 'date_of_birth', 'placeholder'=>'Pick from datepicker']) !!}
                        </div>
                        {!! $errors->first('date_of_birth','<span class="help-block">:message</span>') !!}
                    </td>
                    
                </tr>
                <tr>
                    <td>Gender</td>
                    <td class="light-yellow">
                        <div class="form-group margin-bottom">
                            <div class="col-md-12">
                                <label class="radio-inline">
                                    {!! Form::radio('gender', 'Male', (($directorInfo->gender == 'Male') ? true : false),['class' => '', 'id'=>'gender']) !!}
                                    Male
                                </label>
                                <label class="radio-inline">
                                    {!! Form::radio('gender', 'Female', (($directorInfo->gender == 'Female') ? true : false),['class' => '', 'id'=>'gender']) !!}
                                    Female
                                </label>
                                <label class="radio-inline">
                                    {!! Form::radio('gender', 'Other', (($directorInfo->gender == 'Other') ? true : false),['class' => '', 'id'=>'gender']) !!}
                                    Other
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Designation</td>
                    <td class="light-yellow">
                        <div class="form-group margin-bottom">
                            <div class="col-md-12 {{$errors->has('l_director_designation') ? 'has-error': ''}}">
                                {!! Form::text('l_director_designation', !empty($directorInfo->l_director_designation) ? $directorInfo->l_director_designation : '', ['class' => 'form-control input-md  ', 'id'=>'l_director_designation']) !!}
                                {!! $errors->first('l_director_designation','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </td>
                    
                </tr>
                <tr>
                    <td>Nationality</td>
                    <td class="light-yellow">
                        <div class="form-group margin-bottom">
                            <div class="col-md-12 {{$errors->has('l_director_nationality') ? 'has-error': ''}}">
                                {!! Form::select('l_director_nationality', $nationality, !empty($directorInfo->l_director_nationality) ? $directorInfo->l_director_nationality : [], ['class' => 'form-control input-md  ', 'id'=>'l_director_nationality']) !!}
                                {!! $errors->first('l_director_nationality','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </td>
                    
                </tr>
                </tbody>
            </table>
        @endif

        @if($directorInfo->identity_type == 'tin')
            <table class="table table-responsive table-bordered">
                <tbody>
                <tr>
                    <td>TIN (Bangladesh)</td>
                    <td class="light-yellow">
                        <div class="form-group margin-bottom">
                            <div class="col-md-12 {{$errors->has('nid_etin_passport') ? 'has-error': ''}}">
                                {!! Form::text('nid_etin_passport', !empty($directorInfo->nid_etin_passport) ? $directorInfo->nid_etin_passport : '', ['class' => 'form-control input-md  ', 'id'=>'nid_etin_passport']) !!}
                                {!! $errors->first('nid_etin_passport','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </td>
                    
                </tr>
                <tr>
                    <td>Name</td>
                    <td class="light-yellow">
                        <div class="form-group margin-bottom">
                            <div class="col-md-12 {{$errors->has('l_director_name') ? 'has-error': ''}}">
                                {!! Form::text('l_director_name', !empty($directorInfo->l_director_name) ? $directorInfo->l_director_name : '', ['class' => 'form-control input-md  ', 'id'=>'l_director_name']) !!}
                                {!! $errors->first('l_director_name','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </td>
                    
                </tr>
                <tr>
                    <td>Date of Birth</td>
                    <td class="light-yellow">
                        <div class="form-group margin-bottom">
                            <div class="col-md-12 {{$errors->has('date_of_birth') ? 'has-error': ''}}">
                                <div class="input-group date" data-date-format="dd-mm-yyyy">
                                    {!! Form::text('date_of_birth', !empty($directorInfo->date_of_birth) ? date('d-M-Y', strtotime($directorInfo->date_of_birth)) : '', ['class'=>'form-control input-md datepicker', 'id' => 'date_of_birth', 'placeholder'=>'Pick from datepicker']) !!}
                                </div>
                                {!! $errors->first('date_of_birth','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </td>
                    
                </tr>
                <tr>
                    <td>Gender</td>
                    <td class="light-yellow">
                        <div class="form-group margin-bottom">
                            <div class="col-md-12">
                                <label class="radio-inline">
                                    {!! Form::radio('gender', 'Male', (($directorInfo->gender == 'Male') ? true : false),['class' => '', 'id'=>'gender']) !!}
                                    Male
                                </label>
                                <label class="radio-inline">
                                    {!! Form::radio('gender', 'Female', (($directorInfo->gender == 'Female') ? true : false),['class' => '', 'id'=>'gender']) !!}
                                    Female
                                </label>
                                <label class="radio-inline">
                                    {!! Form::radio('gender', 'Other', (($directorInfo->gender == 'Other') ? true : false),['class' => '', 'id'=>'gender']) !!}
                                    Other
                                </label>
                            </div>
                        </div>
                    </td>
                    
                </tr>
                <tr>
                    <td>Designation</td>
                    <td class="light-yellow">
                        <div class="form-group margin-bottom">
                            <div class="col-md-12 {{$errors->has('l_director_designation') ? 'has-error': ''}}">
                                {!! Form::text('l_director_designation', !empty($directorInfo->l_director_designation) ? $directorInfo->l_director_designation : '', ['class' => 'form-control input-md  ', 'id'=>'l_director_designation']) !!}
                                {!! $errors->first('l_director_designation','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </td>
                    
                </tr>
                <tr>
                    <td>Nationality</td>
                    <td class="light-yellow">
                        <div class="form-group margin-bottom">
                            <div class="col-md-12 {{$errors->has('l_director_nationality') ? 'has-error': ''}}">
                                {!! Form::select('l_director_nationality', $nationality, !empty($directorInfo->l_director_nationality) ? $directorInfo->l_director_nationality : [], ['class' => 'form-control input-md  ', 'id'=>'l_director_nationality']) !!}
                                {!! $errors->first('l_director_nationality','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </td>
                    
                </tr>
                </tbody>
            </table>
        @endif

        @if($directorInfo->identity_type == 'passport')
            <table class="table table-responsive table-bordered">
                <tbody>
                <tr>
                    <td>Name</td>
                    <td class="light-yellow">
                        <div class="form-group margin-bottom">
                            <div class="col-md-12 {{$errors->has('l_director_name') ? 'has-error': ''}}">
                                {!! Form::text('l_director_name', !empty($directorInfo->l_director_name) ? $directorInfo->l_director_name : '', ['class' => 'form-control input-md  ', 'id'=>'l_director_name']) !!}
                                {!! $errors->first('l_director_name','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </td>
                    
                </tr>
                <tr>
                    <td>Date of Birth</td>
                    <td class="light-yellow">
                        <div class="form-group margin-bottom">
                            <div class="col-md-12 {{$errors->has('date_of_birth') ? 'has-error': ''}}">
                                <div class="input-group date" data-date-format="dd-mm-yyyy">
                                    {!! Form::text('date_of_birth', !empty($directorInfo->date_of_birth) ? date('d-M-Y', strtotime($directorInfo->date_of_birth)) : '', ['class'=>'form-control input-md datepicker', 'id' => 'date_of_birth', 'placeholder'=>'Pick from datepicker']) !!}
                                </div>
                                {!! $errors->first('date_of_birth','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </td>
                    
                </tr>
                <tr>
                    <td>Gender</td>
                    <td class="light-yellow">
                        <div class="form-group margin-bottom">
                            <div class="col-md-12">
                                <label class="radio-inline">
                                    {!! Form::radio('gender', 'Male', (($directorInfo->gender == 'Male') ? true : false),['class' => '', 'id'=>'gender']) !!}
                                    Male
                                </label>
                                <label class="radio-inline">
                                    {!! Form::radio('gender', 'Female', (($directorInfo->gender == 'Female') ? true : false),['class' => '', 'id'=>'gender']) !!}
                                    Female
                                </label>
                                <label class="radio-inline">
                                    {!! Form::radio('gender', 'Other', (($directorInfo->gender == 'Other') ? true : false),['class' => '', 'id'=>'gender']) !!}
                                    Other
                                </label>
                            </div>
                        </div>
                    </td>
                    
                </tr>
                <tr>
                    <td>Designation</td>
                    <td class="light-yellow">
                        <div class="form-group margin-bottom">
                            <div class="col-md-12 {{$errors->has('l_director_designation') ? 'has-error': ''}}">
                                {!! Form::text('l_director_designation', !empty($directorInfo->l_director_designation) ? $directorInfo->l_director_designation : '', ['class' => 'form-control input-md  ', 'id'=>'l_director_designation']) !!}
                                {!! $errors->first('l_director_designation','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </td>
                    
                </tr>
                <tr>
                    <td>Nationality</td>
                    <td class="light-yellow">
                        <div class="form-group margin-bottom">
                            <div class="col-md-12 {{$errors->has('l_director_nationality') ? 'has-error': ''}}">
                                {!! Form::select('l_director_nationality', $nationality, !empty($directorInfo->l_director_nationality) ? $directorInfo->l_director_nationality : [], ['class' => 'form-control input-md  ', 'id'=>'l_director_nationality']) !!}
                                {!! $errors->first('l_director_nationality','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </td>
                    
                </tr>
                <tr>
                    <td>Passport type</td>
                    <td class="light-yellow">
                        <div class="form-group margin-bottom">
                            <div class="col-md-12 {{$errors->has('passport_type') ? 'has-error': ''}}">
                                {!! Form::select('passport_type', ['0'=>'select one','ordinary'=>'Ordinary','diplomatic'=>'Diplomatic','official'=>'Official'], $directorInfo->passport_type, ['class' => 'form-control input-md  ', 'id'=>'passport_type']) !!}
                                {!! $errors->first('passport_type','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </td>
                    
                </tr>
                <tr>
                    <td>Passport No.</td>
                    <td class="light-yellow">
                        <div class="form-group margin-bottom">
                            <div class="col-md-12 {{$errors->has('nid_etin_passport') ? 'has-error': ''}}">
                                {!! Form::text('nid_etin_passport', !empty($directorInfo->nid_etin_passport) ? $directorInfo->nid_etin_passport : '', ['class' => 'form-control input-md  ', 'id'=>'nid_etin_passport']) !!}
                                {!! $errors->first('nid_etin_passport','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </td>
                    
                </tr>
                <tr>
                    <td>Date of expiry</td>
                    <td class="light-yellow">
                        <div class="input-group date" data-date-format="dd-mm-yyyy">
                            {!! Form::text('date_of_expiry', !empty($directorInfo->date_of_expiry) ? date('d-M-Y', strtotime($directorInfo->date_of_expiry)) : '', ['class'=>'form-control input-md datepicker', 'id' => 'date_of_expiry', 'placeholder'=>'Pick from datepicker']) !!}
                        </div>
                        {!! $errors->first('date_of_expiry','<span class="help-block">:message</span>') !!}
                    </td>
                    
                </tr>
                </tbody>
            </table>
        @endif
    </div>
    <div class="clearfix"></div>
</div>

<div class="modal-footer" style="text-align:left;">
    <div class="pull-left">
        {!! Form::button('<i class="fa fa-times"></i> Close', array('type' => 'button', 'class' => 'btn btn-danger', 'data-dismiss' => 'modal')) !!}
    </div>
    <div class="pull-right">
        <button type="submit" class="btn btn-primary" id="director_update_btn" name="actionBtn" value="update">
            <i class="fa fa-chevron-circle-right"></i> Update
        </button>
    </div>
    <div class="clearfix"></div>
</div>
{!! Form::close() !!}

<script src="{{ asset("vendor/datepicker/datepicker.min.js") }}"></script>
<script>

    $("#amendment_type0").trigger('change');

    $(document).ready(function () {
        // Datepicker Plugin initialize
        $('.datepicker').datepicker({
            outputFormat: 'dd-MMM-y',
            // daysOfWeekDisabled: [5,6],
            theme : 'blue',
        });


        $("#directorForm").validate({
            errorPlacement: function () {
                return true;
            },
            submitHandler: formSubmit
        });

        var form = $("#directorForm"); //Get Form ID
        var url = form.attr("action"); //Get Form action
        var type = form.attr("method"); //get form's data send method
        var info_err = $('.errorMsg'); //get error message div
        var info_suc = $('.successMsg'); //get success message div

        //============Ajax Setup===========//
        function formSubmit() {
            $.ajax({
                type: type,
                url: url,
                data: form.serialize(),
                dataType: 'json',
                beforeSend: function (msg) {
                    $("#director_update_btn").html('<i class="fa fa-cog fa-spin"></i> Loading...');
                    $("#director_update_btn").prop('disabled', true); // disable button
                },
                success: function (data) {
                    //==========validation error===========//
                    if (data.success == false) {
                        info_err.hide().empty();
                        $.each(data.error, function (index, error) {
                            info_err.removeClass('hidden').append('<li>' + error + '</li>');
                        });
                        info_err.slideDown('slow');
                        info_err.delay(2000).slideUp(1000, function () {
                            $("#director_update_btn").html('Update');
                            $("#director_update_btn").prop('disabled', false);
                        });
                    }
                    //==========if data is saved=============//
                    if (data.success == true) {
                        $("#director_update_btn").html('Update');
                        $("#director_update_btn").prop('disabled', false);
                        info_suc.hide().empty();
                        info_suc.removeClass('hidden').html(data.status);
                        info_suc.slideDown('slow');
                        info_suc.delay(2000).slideUp(800, function () {
                            $("#irc3rdadhocModal").modal('hide');
                        });
                        form.trigger("reset");
                        LoadListOfDirectors();

                    }
                    //=========if data already submitted===========//
                    if (data.error == true) {
                        info_err.hide().empty();
                        info_err.removeClass('hidden').html(data.status);
                        info_err.slideDown('slow');
                        info_err.delay(1000).slideUp(800, function () {
                            $("#director_update_btn").html('Update');
                            $("#director_update_btn").prop('disabled', false);
                        });
                    }
                },
                error: function (data) {
                    var errors = data.responseJSON;
                    $("#Duplicated jQuery selector").prop('disabled', false);
                    console.log(errors);
                    alert('Sorry, an unknown Error has been occured! Please try again later.');
                }
            });
            return false;
        }
    });
</script>
