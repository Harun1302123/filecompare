<div class="wpe_duration form-group">
    <fieldset class="scheduler-border">
        <legend class="scheduler-border">Effective date of amendment</legend>
        <div class="form-group">
            <div class="row">
                <div class="col-md-3 {{$errors->has('approved_effective_date') ? 'has-error': ''}}">
                    {!! Form::label('approved_effective_date','Approved effective date',['class'=>'text-left col-md-12']) !!}
                    <div class="col-md-12">
                        <div class="input-group date">
                            {!! Form::text('approved_effective_date', (!empty($opa_info->approved_effective_date) ? date('d-M-Y', strtotime($opa_info->approved_effective_date)) : ''), ['class' => 'form-control input-md', 'placeholder'=>'dd-mm-yyyy']) !!}
                            <span class="input-group-addon"
                                  onclick="javascript:NewCssCal('approved_effective_date', 'ddMMMyyyy', 'arrow', '', '', '', '')">
                                <span class="fa fa-calendar"></span>
                            </span>
                        </div>
                        {!! $errors->first('approved_effective_date','<span class="help-block">:message</span>') !!}
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
</div>