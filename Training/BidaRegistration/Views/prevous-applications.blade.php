<div id="previousApplications" class="collapse">
    <div class="panel panel-success">
        <div class="panel-heading">
            <strong>Prevous Applications Information</strong>
        </div>

        <div class="panel-body">
            @if(count($listOfPreviousApplications)>0)
                <div style="margin:0;" class="panel panel-info">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered dt-responsive previous-applications" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th valign="top" class="text-center valigh-middle">Tracking No. </th>
                                            <th valign="top" class="text-center valigh-middle">Status</th>
                                            <th valign="top" class="text-center valigh-middle">Submited Date</th>
                                            <th valign="top" class="text-center valigh-middle">Last Updated Date</th>
                                            <th valign="top" class="text-center valigh-middle">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($listOfPreviousApplications as $previousApplication)
                                            <tr>
                                                <td>{{ (!empty($previousApplication->tracking_no)) ? $previousApplication->tracking_no : ''  }}</td>
                                                <td>{{ (!empty($previousApplication->status_name)) ? $previousApplication->status_name : ''  }}</td>
                                                <td>{{ (!empty($previousApplication->formatted_submitted_at)) ? $previousApplication->formatted_submitted_at : ''  }}</td>
                                                <td>{{ (!empty($previousApplication->formatted_updated_at)) ? $previousApplication->formatted_updated_at : ''  }}</td>
                                                <td>
                                                    <a class="btn btn-xs btn-primary" target="_blank" href="{{$previousApplication->previous_app_url}}" role="button"> Open </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-center valigh-middle"><b>No previous Shortfall, Archive, Reject, or Cancelled application found.</b></p>
            @endif
        </div>
    </div>
</div>