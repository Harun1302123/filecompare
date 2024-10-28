{{--<div class="collapse" id="processMap">--}}
    {{--<div class="panel panel-orange pMap">--}}
        {{--<div class="panel-heading">Application Process Map <b><span style="color: #ffff00" id="shortFall"></span></b></div>--}}
        {{--<div class="panel-body">--}}
            {{--<svg width="100%" height="220">--}}
                {{--<g></g>--}}
            {{--</svg>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}


<div class="panel panel-orange pMap">
    <div class="panel-heading">Application Process Map <b><span style="color: #ffff00" id="shortFall"></span></b></div>
    <div class="panel-body">
        <svg width="100%" height="220">
            <g></g>
        </svg>
    </div>
</div>


<script>
    $(document).ready(function(){
        $('#processMap').on('click', function(event) {
            $('.pMap').toggle('show');
        });
        $('#processMap').click();
    });
</script>