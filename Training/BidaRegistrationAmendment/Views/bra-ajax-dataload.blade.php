<script>
    // list of directors load start
    function listOfDirectors(limit, viewMode) {
        $.ajax({
            url: "{{ url("bida-registration-amendment/load-director-list") }}",
            type: "POST",
            data: {
                app_id: "{{ Encryption::encodeId($appInfo->id) }}",
                limit: limit,
                viewMode: viewMode,
                _token : $('input[name="_token"]').val()
            },
            success: function(response){
                if (response.responseCode == 1){
                    $('#load_list_of_director').html(response.html);

                    if (limit == 'all') {
                        $('#load_list_of_director_data').text('Less data');
                        $('#load_list_of_director_data').attr('onclick', "listOfDirectors(20, '"+ viewMode +"')");
                    } else {
                        $('#load_list_of_director_data').text('Load more data');
                        $('#load_list_of_director_data').attr('onclick', "listOfDirectors('all', '"+ viewMode +"')");
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Unknown error occured. Please, try again after reload');
            }
        });
    }
    // list of directors load end

    function loadImportedMachineryData(limit, viewMode) {
        $.ajax({
            url: "{{ url("bida-registration-amendment/load-imported-machinery-data") }}",
            type: "POST",
            data: {
                app_id: "{{ Encryption::encodeId($appInfo->id) }}",
                limit: limit,
                viewMode: viewMode,
                _token : $('input[name="_token"]').val()
            },
            success: function(response){
                if (response.responseCode == 1){
                    $('#load_imported_machinery_data').html(response.html);

                    if (limit == 'all') {
                        $('#load_imported_data').text('Less data');
                        $('#load_imported_data').attr('onclick', "loadImportedMachineryData(20, '"+ viewMode +"')");
                    } else {
                        $('#load_imported_data').text('Load more data');
                        $('#load_imported_data').attr('onclick', "loadImportedMachineryData('all', '"+ viewMode +"')");
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Unknown error occured. Please, try again after reload');
            }
        });
    }

    function loadLocalMachineryData(limit, viewMode) {
        $.ajax({
            url: "{{ url("bida-registration-amendment/load-local-machinery-data") }}",
            type: "POST",
            data: {
                app_id: "{{ Encryption::encodeId($appInfo->id) }}",
                limit: limit,
                viewMode: viewMode,
                _token : $('input[name="_token"]').val()
            },
            success: function(response){
                if (response.responseCode == 1){
                    $('#load_local_machinery_data').html(response.html);

                    if (limit == 'all') {
                        $('#load_local_data').text('Less data');
                        $('#load_local_data').attr('onclick', "loadLocalMachineryData(20, '"+ viewMode +"')");
                    } else {
                        $('#load_local_data').text('Load more data');
                        $('#load_local_data').attr('onclick', "loadLocalMachineryData('all', '"+ viewMode +"')");
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Unknown error occured. Please, try again after reload');
            }
        });
    }

    function loadAnnualProductionCapacityData(limit, viewMode) {
        $.ajax({
            url: "{{ url("bida-registration-amendment/load-apc-data") }}",
            type: "POST",
            data: {
                app_id: "{{ Encryption::encodeId($appInfo->id) }}",
                limit: limit,
                viewMode: viewMode,
                _token : $('input[name="_token"]').val()
            },
            success: function(response){
                if (response.responseCode == 1){
                    $('#load_apc_data').html(response.html);
                }

                if (limit == 'all') {
                    $('#apc_data').text('Less data');
                    $('#apc_data').attr('onclick', "loadAnnualProductionCapacityData(20, '"+ viewMode +"')");
                } else {
                    $('#apc_data').text('Load more data');
                    $('#apc_data').attr('onclick', "loadAnnualProductionCapacityData('all', '"+ viewMode +"')");
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Unknown error occured. Please, try again after reload');
            }
        });
    }


</script>
