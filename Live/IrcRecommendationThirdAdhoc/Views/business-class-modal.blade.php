<style>
    tr.group,
    tr.group:hover {
        background-color: #ddd !important;
    }
    p {
        margin-left: 20px;
    }
</style>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title" id="myModalLabel">List of business classes</h4>
</div>

<div class="modal-body">
    <div class="table-responsive">
        <table id="list" class="table table-bordered dt-responsive" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th width="90%">Class</th>
                <th>Section (code)</th>
                <th width="10%">Action</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-footer">
    <div class="pull-left">
        {!! Form::button('<i class="fa fa-times"></i> Close', array('type' => 'button', 'class' => 'btn btn-danger', 'data-dismiss' => 'modal', 'id' => 'closeBusinessModal')) !!}
    </div>
    <div class="clearfix"></div>
</div>

<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
<script src="{{ asset("assets/scripts/datatable/jquery.dataTables.min.js") }}"></script>
<script src="{{ asset("assets/scripts/datatable/dataTables.bootstrap.min.js") }}"></script>
<script src="{{ asset("assets/scripts/datatable/dataTables.responsive.min.js") }}"></script>
<script src="{{ asset("assets/scripts/datatable/responsive.bootstrap.min.js") }}"></script>
<script>
    $(function () {
        var groupColumn = 1;
        var table = $('#list').DataTable({
            processing: true,
            serverSide: true,
            iDisplayLength: 10,
            columnDefs: [
                { "visible": false, "targets": groupColumn }
            ],
            order: [[ groupColumn, 'asc' ]],
            ajax: {
                url: '{{url("bida-registration/get-business-class-list")}}',
                method:'post',
                data: function (d) {
                    d._token = $('input[name="_token"]').val();
                }
            },
            columns: [
                {data: 'class', name: 'class'},
                {data: 'section_name_code', name: 'section_name_code'},
                {data: 'action', name: 'action', orderable: true, searchable: true}
            ],
            drawCallback: function ( settings ) {

                $(".paginate_button").css({"display": "inline"});

                var api = this.api();
                var rows = api.rows( {page:'current'} ).nodes();
                var last=null;

                api.column(groupColumn, {page:'current'} ).data().each( function ( group, i ) {
                    if ( last !== group ) {
                        $(rows).eq( i ).before(
                            '<tr class="group"><td colspan="2">'+group+'</td></tr>'
                        );

                        last = group;
                    }
                } );
            },
            "aaSorting": [],
            // "drawCallback": function() {
            //     $(".paginate_button").css({"display": "inline"});
            // }
        });



        // Order by the grouping
        $('#list tbody').on( 'click', 'tr.group', function () {
            var currentOrder = table.order()[0];
            if ( currentOrder[0] === groupColumn && currentOrder[1] === 'asc' ) {
                table.order( [ groupColumn, 'desc' ] ).draw();
            }
            else {
                table.order( [ groupColumn, 'asc' ] ).draw();
            }
        } );

    });
</script>
