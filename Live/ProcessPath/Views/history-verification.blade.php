
@extends('layouts.admin')
@section('content')

<style type="text/css">

</style>
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                {{--start application form with wizard--}}
                {!! Session::has('success') ? '<div class="alert alert-success alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("success") .'</div>' : '' !!}
                {!! Session::has('error') ? '<div class="alert alert-danger alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("error") .'</div>' : '' !!}

                <div class="panel panel-info" id="inputForm">
                    <div class="panel-heading">
                        <h5><strong>  Block Chain Verification</strong></h5>
                    </div>
                    <div class="panel-body" style="font-size: 14px;">

                        <?php echo $html; ?>
                            {{--<a href="{{ url('/process/list') }}">--}}
                                {{--{!! Form::button('<i class="fa fa-times"></i> Close', array('type' => 'button', 'class' => 'btn btn-default')) !!}--}}
                            {{--</a>--}}
                    </div>
                </div>
            </div>
        </div>


        @endsection

        @section('footer-script')
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>

            @include('partials.datatable-scripts')

            <script language="javascript">
                $(document).ready(function(){
                    $('#myTable').DataTable();
                });
            </script>
@endsection