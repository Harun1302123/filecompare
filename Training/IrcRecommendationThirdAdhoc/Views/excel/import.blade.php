<link rel="stylesheet" href="{{ asset('assets/modules/signup/identity_verify.css') }}">

<div class="modal-header" style="background: #D5EDF7">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title" id="myModalLabel">Upload Sheet for <strong>{{ $annual_product_name->product_name }}</strong></h4>
</div>

{!! Form::open(array('url' => '/irc-recommendation-third-adhoc/upload-csv-file','method' => 'post', 'class' => 'form-horizontal', 'id' => 'batchUpload',
'enctype' =>'multipart/form-data', 'files' => 'true')) !!}

{!! Form::hidden('app_id', $app_id, ['class' => 'form-control input-md']) !!}
{!! Form::hidden('apc_id', $apc_id, ['class' => 'form-control input-md']) !!}

<div class="modal-body">
    <div class="row form-group" id="unit_of_product">
        <div class="col-md-6 {{$errors->has('unit_of_product') ? 'has-error': ''}}">
            {!! Form::label('unit_of_product','Unit of Product:', ['class'=>'col-md-4 text-left required-star']) !!}
            <div class="col-md-8">
                {!! Form::select('unit_of_product', [1=>'1', 10=>'10', 100=>'100', 1000=>'1000'], '', ["placeholder" => "Select One", 'class' => 'form-control input-md required']) !!}
                {!! $errors->first('unit_of_product','<span class="help-block">:message</span>') !!}
            </div>
        </div>
    </div>

    <div class="row">

{{--        <div class="col-md-12">--}}
{{--            <div class="form-group {{$errors->has('import_request') ? 'has-error' : ''}}">--}}
{{--                {!! Form::label('import_request','File',['class'=>'col-md-3 text-left required-star']) !!}--}}
{{--                <div class="col-md-9">--}}
{{--                    <div class="col-md-12">--}}
{{--                        {!! Form::file('import_request', null ,['class'=>'form-control required']) !!}--}}
{{--                        {!! $errors->first('import_request','<span class="help-block">:message</span>') !!}--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

        <div class="col-md-12">
            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Excel Sheet</legend>
                <div id="passport_upload_wrapper" class="passport-upload-wrapper">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <span id="passport_upload_error" class="text-danger text-left"></span>

                            <div style="text-align: center;" id="passport_upload_div">
                                <div class="passport-upload" style="height: 300px;">
                                    <div class="passport-upload-message">
                                        <i class="fas fa-file-excel fa-3x passport-upload-icon" id="file_icon"></i>
                                        <span id="file_name"></span>
                                        <p>
                                            Drop Your Excel Sheet here or
                                            <span style="color:#258DFF;">Browse</span>
                                            <small class="help-block" style="font-size: 9px;">[File Format: *.xls/ .xlsx/ .csv]</small>
                                        </p>
                                    </div>
                                    <input accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                           type="file" name="import_request" id="excel_upload" class="passport-upload-input" onchange="readURL(this)">
                                </div>

                            </div>
                            <div style="text-align: center">
                                <a href="{{ asset('assets/csv-sample/sample.xlsx') }}" ref="noopener" target="_blank">Sample Sheet</a>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

            <div class="alert alert-danger" style="font-size:13px;">
                <strong>Note:</strong> Upload only .csv, .xls or .xlsx file. Use the sample to upload,  otherwise data
                will be mismatched. To follow the given sample file, you can <a href="{!! url('uploads/csv-upload/sample/sample.xlsx') !!}" title="Sample file">
                    <strong>click here</strong></a>.
            </div>
        </div>

    </div>
</div>

<div class="modal-footer clearfix" style="background: #eee">
    <button type="button" class="btn btn-md btn-danger pull-left" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-md btn-primary pull-right">Upload</button>
</div>

{!! Form::close() !!}

<script>
    function readURL(input) {
        let file = $('#excel_upload')[0].files[0].name;
        $('#file_name').html(file);
    }
</script>