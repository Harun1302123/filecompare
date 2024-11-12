<div class="ad_desk_form form-group">
    <b>Ad Desk Form</b>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover ">
            <thead>
            <tr>
                <th>Organization Name</th>
                <th class="required-star">Level</th>
                <th class="required-star">Space(in sqft)</th>
                <th>3 Month's security deposit (30tk/sqft)</th>
                <th>Monthly Rent (30tk/sqft)</th>
                <th>Service Charge (5tk/sqft)</th>
                <th>remarks</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td> {!! Form::text('ad_desk_org_name',$appInfo->applicant_name,['class' => 'form-control input-sm', 'readonly']) !!}</td>
                <td> {!! Form::text('ad_desk_level',$appInfo->ad_desk_level,['data-rule-maxlength'=>'40','class' => 'form-control input-sm ad_desk_level required',]) !!}</td>
                <td> {!! Form::text('ad_desk_space',$appInfo->ad_desk_space,['data-rule-maxlength'=>'40',
                'class' => 'form-control input-sm ad_desk_space required',
                'onKeyUp' => 'CalculateSpaceRent("ad_desk_security_deposite", "ad_desk_rent", "ad_desk_service_charge",this.value)']) !!}</td>
                <td> {!! Form::text('ad_desk_security_deposite',$appInfo->ad_desk_security_deposite,['data-rule-maxlength'=>'40','class' => 'form-control input-sm', 'id'=>'ad_desk_security_deposite', 'readonly']) !!}</td>
                <td> {!! Form::text('ad_desk_rent',$appInfo->ad_desk_rent,['data-rule-maxlength'=>'40','class' => 'form-control input-sm','id'=>'ad_desk_rent', 'readonly']) !!}</td>
                <td> {!! Form::text('ad_desk_service_charge',$appInfo->ad_desk_service_charge,['data-rule-maxlength'=>'40','class' => 'form-control input-sm','id'=>'ad_desk_service_charge', 'readonly']) !!}</td>
                <td> {!! Form::textarea('ad_desk_remarks',$appInfo->ad_desk_remarks,['class' => 'form-control input-sm','rows'=>'1']) !!}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>