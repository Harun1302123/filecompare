<div class="wpe_duration form-group">
    <fieldset class="scheduler-border">
        <legend class="scheduler-border">Account</legend>
        <div class="form-group">
            <div class="row">

                <div class="col-md-3 form-group {{$errors->has('is_accno') ? 'has-error' : ''}}">
                    {!! Form::label('Account Number','',['class'=>'required-star']) !!}
                    {!! Form::text('acc_number', '', ['class' => 'form-control required']) !!}
                </div>

                <div class="col-md-3 form-group {{$errors->has('is_accno') ? 'has-error' : ''}}">
                    {!! Form::label('Amount','',['class'=>'required-star']) !!}
                    {!! Form::number('amount', '', ['class' => 'form-control number onlyNumber required']) !!}
                </div>
            </div>
        </div>
    </fieldset>
</div>
