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
                <h5><strong>Preview of Raw Materials</strong></h5>
            </div><!-- /.panel-heading -->

            {!! Form::open(array('url' => 'irc-recommendation-regular/save-data/','method' => 'post', 'class' => 'form-horizontal', 'id' => 'doRequestsForm',
                'enctype' =>'multipart/form-data', 'files' => 'true')) !!}
            <div class="panel-body">

                <input type="hidden" name="app_id" value="{{ $app_id }}">
                <input type="hidden" name="apc_id" value="{{ $apc_id }}">
                <input type="hidden" name="unit_of_product" value="{{ $unit_of_product }}">

                <div class="errormsg"></div>

                <div class="table-responsive" style="clear:both">
                    <table id="list" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
                           width="100%">
                        <?php
                        $keys = array_keys($excelData[0]);
                        ?>

                        <thead>
                        <tr>
                            @foreach ($keys as $value)
                                <th>  {{ucfirst(str_replace('_',' ',$value))}}  </th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($excelData as $excelRow)
                            <?php
                            $class = 'text-success';
                            ?>
                            <tr>
                                @foreach ($excelRow as $key => $value)
                                    <input type="hidden" name="{{ $key.'[]' }}"
                                           value="{{ ($value != '') ? $value : 0}}">
                                    <td>  {{ ($value != '') ? $value : 0}}  </td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>


            </div>
            <!-- /.panel-body -->

            <div class="panel-footer">
                @if($alterStatus == 'off')
                    <div class="pull-right">
                        <button type="submit" id="saveCsvData" class="btn btn-md btn-primary"><i aria-hidden="true"></i>
                            Save</button>
                    </div>
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
    <script>
        $(document).ready(function () {
//        $("#saveCsvData").on('click', function () {
//            var request_title = $('#request_title').val();
//            if (request_title.trim() == '') {
//                alert("Please select a request title");
//                $('#request_title').addClass('error');
//                return false;
//            } else {
//                $('#request_title').removeClass('error');
//            }
//        });
        });
    </script>
@endsection
