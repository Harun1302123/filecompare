
<table width="100%" id="apcTable" class="table table-bordered">

    @if ($viewMode == 'off')
        <div class="alert alert-warning">
            <strong>Add :</strong> Add a new director for Existing or Proposed information.<br>
            <strong>Edit :</strong>  Edit/ update a director for proposed information.<br>
            <strong>Remove :</strong>Remove a director from your Existing information.<br>
            <strong>Delete :</strong>  If you delete any director from your list, you won't be able to retrieve this.<br>
        </div>
    @endif

    <tr>
        <td class="bg-yellow" colspan="4">Existing information (Latest BIDA <Reg></Reg>. Info.)</td>
        <td class="bg-green" colspan="4">Proposed information</td>
    </tr>
    <tr >
        <th class="light-yellow">Name</th>
        <th class="light-yellow">Designation</th>
        <th class="light-yellow">Nationality</th>
        <th class="light-yellow">NID/PassportNo.</th>

        <th class="light-green">Name</th>
        <th class="light-green">Designation</th>
        <th class="light-green">Nationality</th>
        <th class="light-green">NID/PassportNo.</th>
        <th>Action Type</th>

        @if ($viewMode == 'off')
            <th style="width: 55px;">Action</th>
        @endif

    </tr>

    <tbody>
    @if(count($list_of_directors) > 0)
        @foreach($list_of_directors as $director)
            <tr>
                <td class="light-yellow">
                    {{ !empty($director->l_director_name) ? $director->l_director_name : '' }}
                </td>
                <td class="light-yellow">
                    {{ !empty($director->l_director_designation) ? $director->l_director_designation : '' }}
                </td>
                <td class="light-yellow">
                    {{ !empty($director->ex_nationality) ? $director->ex_nationality : '' }}
                </td>
                <td class="light-yellow">
                    {{ !empty($director->nid_etin_passport) ? $director->nid_etin_passport : '' }}
                </td>


                <td class="light-green">
                    {{ !empty($director->n_l_director_name) ? $director->n_l_director_name : '' }}
                </td>
                <td class="light-green">
                    {{ !empty($director->n_l_director_designation) ? $director->n_l_director_designation : '' }}
                </td>
                <td class="light-green">
                    {{ !empty($director->pro_nationality) ? $director->pro_nationality : '' }}
                </td>
                <td class="light-green">
                    {{ !empty($director->n_nid_etin_passport) ? $director->n_nid_etin_passport : '' }}
                </td>
                <td>
                    @if(!in_array($director->amendment_type, ['no change']))
                        <span class="badge">
                        {{ $director->amendment_type }}
                    </span>
                    @endif
                </td>
                @if ($viewMode == 'off')
                <td>
                    <div style="width: 55px; display: inline-block; text-align: center;">
                        <a class="btn btn-xs btn-success"
                           data-toggle="modal"
                           data-target="#openDirectorModal"
                           onclick="openBraModal(this, 'openDirectorModal')"
                           data-action="{{ url('bida-registration-amendment/director-edit/'.Encryption::encodeId($director->id)) }}">
                            <i class="far fa-edit"></i>
                        </a>
                        <a class="btn btn-xs btn-danger"
                           onclick="confirmDelete('{{ url('bida-registration-amendment/delete-director/'.Encryption::encodeId($director->id)) }}', 'listOfDirectors')">
                            <i class="far fa-trash-alt"></i>
                        </a>
                    </div>
                </td>
                @endif
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="12" class="text-center"><span class="text-danger">No data available!</span></td>
        </tr>
    @endif
    </tbody>
</table>



