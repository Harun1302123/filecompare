<div class="table-responsive">
    <table id="directorListModal" class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Designation</th>
            <th>Nationality</th>
            <th>NID/ Passport No</th>
            <td>Action</td>
        </tr>
        </thead>
        <tbody>
        @if (count($getData) > 0)
           @foreach ($getData as $director)
               <tr>
                   <td>{{ $director->sl }}</td>
                   <td>{{ $director->l_director_name }}</td>
                   <td>{{ $director->l_director_designation }}</td>
                   <td>{{ $director->nationality }}</td>
                   <td>{{ $director->nid_etin_passport }}</td>
                   <td>
                    <div style="width: 55px; display: inline-block; text-align: center;">
                        <a class="btn btn-xs btn-success"data-toggle="modal"
                           data-target="#irc3rdadhocModal"
                           onclick="openModal(this, 'irc3rdadhocModal')"
                           data-action="{{ url('irc-recommendation-third-adhoc/director-form-edit/'.Encryption::encodeId($director->id)) }}">
                            <i class="far fa-edit"></i>
                        </a>
                        <a class="btn btn-xs btn-danger"
                           onclick="confirmDelete('{{ url('irc-recommendation-third-adhoc/director-delete/'.Encryption::encodeId($director->id)) }}', 'director')">
                            <i class="far fa-trash-alt"></i>
                        </a>
                    </div>
                   </td>
               </tr>
                
           @endforeach
        @else
        <tr>
           <td colspan="6" class="text-center"><span class="text-danger">No data available!</span></td>
        </tr>
        @endif

        </tbody>
    </table>
</div>