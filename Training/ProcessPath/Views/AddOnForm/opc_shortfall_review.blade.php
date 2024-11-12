<div class="opc_shortfall_review form-group">
    <fieldset class="scheduler-border">
        <legend class="scheduler-border">Please select the sections those need to correction:</legend>
        <div class="form-group">
            <div class="row {{$errors->has('opc_shortfall_review_section') ? 'has-error': ''}}">
                <div class="col-md-6">
                    <div class="checkbox"><label><input type="checkbox" value="basic_instruction_review" name="opc_shortfall_review_section[]"> Basic
                            instructions</label></div>
                    <div class="checkbox"><label><input type="checkbox" value="office_type_review" name="opc_shortfall_review_section[]"> Office Type</label>
                    </div>
                    <div class="checkbox"><label><input type="checkbox" value="company_info_review" name="opc_shortfall_review_section[]"> Company
                            information</label></div>
                    <div class="checkbox"><label><input type="checkbox" value="capital_of_company_review" name="opc_shortfall_review_section[]"> The capital
                            of the principal company (in US $)</label></div>
                    <div class="checkbox"><label><input type="checkbox" value="local_address_review" name="opc_shortfall_review_section[]"> Local address of
                            the principal company: (Bangladesh only)</label></div>
                    <div class="checkbox"><label><input type="checkbox" value="activities_in_bd_review" name="opc_shortfall_review_section[]"> Activities in
                            Bangladesh</label></div>
                </div>
                <div class="col-md-6">
                    <div class="checkbox"><label><input type="checkbox" value="period_of_permission_review" name="opc_shortfall_review_section[]"> Period for
                            which permission is sought for</label></div>
                    <div class="checkbox"><label><input type="checkbox" value="organizational_set_up_review" name="opc_shortfall_review_section[]"> Proposed
                            organizational set up of the office with expatriate and local man power ratio</label></div>
                    <div class="checkbox"><label><input type="checkbox" value="expenses_review" name="opc_shortfall_review_section[]"> Establishment
                            expenses and operational expenses of the office (in US Dollar)</label></div>
                    <div class="checkbox"><label><input type="checkbox" value="attachment_review" name="opc_shortfall_review_section[]"> Necessary documents to be attached here (Only PDF file to be attach here)</label>
                    <div class="checkbox"><label><input type="checkbox" value="declaration_review" name="opc_shortfall_review_section[]"> Declaration</label>
                    </div>
                </div>
                {!! $errors->first('opc_shortfall_review_section','<span class="help-block">:message</span>') !!}
            </div>
        </div>
    </fieldset>
</div>