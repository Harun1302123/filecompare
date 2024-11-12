<div class="panel panel-success">
    <div class="panel-heading">
        <h5><strong>Conditionally approve information</strong></h5>
    </div>
    <div class="panel-body">
        {!! Form::open(array('url' => Request::segment(1).'/conditionalApproveStore','method' => 'post','id' => 'WorkPermitPayment','enctype'=>'multipart/form-data',
                'method' => 'post', 'files' => true, 'role'=>'form')) !!}

        <input type="hidden" name="app_id" value="{{ \App\Libraries\Encryption::encodeId($appInfo->id) }}"/>

        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                <div class="form-group {{$errors->has('conditional_approved_file') ? 'has-error': ''}}"
                     style="overflow: hidden; margin-bottom: 15px;">
                    {!! Form::label('conditional_approved_file ','Attachment', ['class'=>'col-md-3 required-star text-left']) !!}
                    <div class="col-md-9">
                        <input type="file" id="conditional_approved_file"
                               name="conditional_approved_file" onchange="checkPdfDocumentType(this.id, 2)"
                               accept="application/pdf"
                               class="form-control input-md required" accept="application/pdf"/>
                        {!! $errors->first('conditional_approved_file','<span class="help-block">:message</span>') !!}
                        <span class="text-danger" style="font-size: 9px; font-weight: bold">[File Format: *.pdf | Maximum File size 2MB]</span>
                        <br>
                    </div>
                </div>

                <div class="form-group {{$errors->has('conditional_approved_remarks') ? 'has-error': ''}}"
                     style="overflow: hidden; margin-bottom: 15px;">
                    {!! Form::label('conditional_approved_remarks','Remarks',['class'=>'text-left col-md-3']) !!}
                    <div class="col-md-9">
                        {!! Form::textarea('conditional_approved_remarks', $appInfo->conditional_approved_remarks, ['data-rule-maxlength'=>'1000', 'placeholder'=>'Remarks', 'class' => 'form-control input-md',
                            'size'=>'5x6','maxlength'=>'1000']) !!}
                        {!! $errors->first('conditional_approved_remarks','<span class="help-block">:message</span>') !!}
                    </div>
                </div>

                <button type="submit" id="submitForm" style="cursor: pointer;"
                        class="btn btn-success btn-md pull-right"
                        value="Submit" name="actionBtn">Condition Fulfilled
                </button>
            </div>
        </div>

        {!! Form::close() !!}
    </div>
</div>
