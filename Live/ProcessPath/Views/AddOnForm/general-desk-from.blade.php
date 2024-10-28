<div class="ad_desk_form form-group">
    <b>Ad Desk Form</b>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover ">
            <thead>
            <tr>
                <th>Organization Name</th>
                <th class="required-star">Level</th>
                <th class="required-star">Space(in sqft)</th>
                <th>remarks</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td> {!! Form::text('ad_desk_org_name',$appInfo->applicant_name,['class' => 'form-control input-sm', 'readonly']) !!}</td>
                <td> {!! Form::text('ad_desk_level',$appInfo->service_name,['data-rule-maxlength'=>'40','class' => 'form-control input-sm ad_desk_level required',]) !!}</td>
                <td> {!! Form::text('ad_desk_space',$appInfo->ad_desk_space,['data-rule-maxlength'=>'40',
                'class' => 'form-control input-sm ad_desk_space required',
                'onKeyUp' => 'CalculateSpaceRent("ad_desk_security_deposite", "ad_desk_rent", "ad_desk_service_charge",this.value)']) !!}</td>
                <td> {!! Form::textarea('ad_desk_remarks',$appInfo->ad_desk_remarks,['class' => 'form-control input-sm','rows'=>'1']) !!}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>