@extends('layouts.admin')

@section('page_heading', 'Data Preview')

@section('content')
    <div class="col-lg-12">
        <div class="hidden">
            {!! Session::has('hiddenMsg') ? Session::get("hiddenMsg") : '' !!}
        </div>
        {!! Session::has('success') ? '<div class="alert alert-success alert-dismissible">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("success") .'</div>' : '' !!}
        {!! Session::has('error') ? '<div class="alert alert-danger alert-dismissible">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("error") .'</div>' : '' !!}

        <div class="panel panel-info">
            <div class="panel-heading">
                <h5><strong>Preview of {{ ucwords(str_replace('-', ' ', $type_name)) }}</strong></h5>
            </div><!-- /.panel-heading -->

            {!! Form::open(array('url' => 'bida-registration/machinery-excel-data-store/','method' => 'post', 'class' => 'form-horizontal', 'id' => 'doRequestsForm',
                'enctype' =>'multipart/form-data', 'files' => 'true')) !!}
            <div class="panel-body">

                <input type="hidden" name="app_id" value="{{ $app_id }}">
                <input type="hidden" name="type_name" value="{{ $type_name }}">
                <div class="errormsg"></div>

                <div class="table-responsive" style="clear:both">
                    <table id="list" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
                           width="100%">
                        <?php $keys = array_keys($excelData[0]); ?>
                        <thead>
                        <tr>
                            @foreach ($keys as $value)
                                <th>  {{ucfirst(str_replace('_',' ',$value))}}  </th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        <?php  $total = 0; ?>
                        @foreach($excelData as $excelRow)
                            <?php $total += $excelRow['total_value_million_tk']; ?>
                            <?php $class = 'text-success'; ?>
                            <tr>
                                @foreach ($excelRow as $key => $value)

                                    <input type="hidden" name="{{ $key.'[]' }}"
                                           value="{{ ($value != '') ? $value : 0}}">
                                    <td>  {{ ($value != '') ? $value : 0}}  </td>

                                @endforeach
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="3"><span class="pull-right" style="font-weight: bold">Total:</span></td>
                            <td><strong><?php  echo $total ?></strong></td>
                        </tr>
                        </tbody>
                    </table>
                </div>


            </div>
            <!-- /.panel-body -->

            <div class="panel-footer">
                @if($alterStatus == 'off')
                    <div class="pull-right">
                        <button type="submit" id="saveCsvData" class="btn btn-md btn-primary"><i aria-hidden="true"></i>
                            Save
                        </button>
                    </div>
                    <a class="pull-left btn btn-danger"
                       href="{{ url('bida-registration/list-of/'. $type_name .'/'.$app_id.'/'.Encryption::encodeId(102)) }}">
                        Go Back</a>

                    <div class="clearfix"></div>
                @endif
            </div>

        {!! Form::close() !!}
        <!-- /.form end -->
        </div>
        <!-- /.panel -->
    </div>
@endsection

@section('footer-script')
@endsection
