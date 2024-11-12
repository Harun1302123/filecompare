<div class="opc_shortfall_review form-group">
    <fieldset class="scheduler-border">
        <legend class="scheduler-border">Please select the sections those need to correction:</legend>
        <div class="form-group">
            <div class="row {{$errors->has('br_shortfall_review_section') ? 'has-error': ''}}">
                <div class="col-md-6">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="all_review" id="all_review_id" onclick="checkedAllCheckbox()" name="br_shortfall_review_section"> 
                            All
                        </label>
                    </div>
                    <div class="checkbox">
                        {{-- <label>
                            <input type="checkbox" value="company_info_review" 
                                   name="br_shortfall_review_section[]"> Company Information
                        </label> --}}
                        <label>
                            <input type="checkbox" value="company_info_review" name="br_shortfall_review_section[]" 
                                {{ isset($appInfo->company_info_review) && $appInfo->company_info_review == 1 ? 'checked' : '' }}> 
                                Company Information
                        </label>
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="promoter_info_review" name="br_shortfall_review_section[]"
                                {{ isset($appInfo->promoter_info_review) && $appInfo->promoter_info_review == 1 ? 'checked' : '' }}> 
                                Information of Principal Promoter/Chairman/Managing Director/CEO/Country Manager
                        </label>
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="office_address_review" name="br_shortfall_review_section[]"
                                {{ isset($appInfo->office_address_review) && $appInfo->office_address_review == 1 ? 'checked' : '' }}>
                                Office Address
                        </label>
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="factory_address_review" name="br_shortfall_review_section[]"
                                {{ isset($appInfo->factory_address_review) && $appInfo->factory_address_review == 1 ? 'checked' : '' }}>  
                                Factory Address
                        </label>
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="project_status_review" name="br_shortfall_review_section[]"
                                {{ isset($appInfo->project_status_review) && $appInfo->project_status_review == 1 ? 'checked' : '' }}>
                                 Project status & date of commercial operation
                        </label>
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="production_capacity_review" name="br_shortfall_review_section[]"
                                {{ isset($appInfo->production_capacity_review) && $appInfo->production_capacity_review == 1 ? 'checked' : '' }}>  
                                Annual production capacity
                        </label>
                    </div>

{{--                    <div class="checkbox">--}}
{{--                        <label>--}}
{{--                            <input type="checkbox" value="commercial_operation_review"--}}
{{--                                   name="br_shortfall_review_section[]">  Date of commercial operation--}}
{{--                        </label>--}}
{{--                    </div>--}}

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="sales_info_review" name="br_shortfall_review_section[]"
                                {{ isset($appInfo->sales_info_review) && $appInfo->sales_info_review == 1 ? 'checked' : '' }}>  
                                Sales (in 100%)
                        </label>
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="manpower_review" id="manpower_review_id" onclick="autoCheckedAttachment()" name="br_shortfall_review_section[]"
                                {{ isset($appInfo->manpower_review) && $appInfo->manpower_review == 1 ? 'checked' : '' }}>
                                Manpower of the organization
                        </label>
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="investment_review" id="investment_review_id" onclick="autoCheckListOfLocalAndImportedMachinery()" name="br_shortfall_review_section[]"
                                {{ isset($appInfo->investment_review) && $appInfo->investment_review == 1 ? 'checked' : '' }}>
                                Investment
                        </label>
                    </div>

{{--                    <div class="checkbox">--}}
{{--                        <label>--}}
{{--                            <input type="checkbox" value="source_finance_review"--}}
{{--                                   name="br_shortfall_review_section[]">  Source of finance--}}
{{--                        </label>--}}
{{--                    </div>--}}
                </div>
                
                <div class="col-md-6">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="utility_service_review" name="br_shortfall_review_section[]"
                                {{ isset($appInfo->utility_service_review) && $appInfo->utility_service_review == 1 ? 'checked' : '' }} >
                                Public utility service
                        </label>
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="trade_license_review" name="br_shortfall_review_section[]"
                                {{ isset($appInfo->trade_license_review) && $appInfo->trade_license_review == 1 ? 'checked' : '' }}>
                                Trade licence details
                        </label>
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="tin_review" name="br_shortfall_review_section[]"
                                {{ isset($appInfo->tin_review) && $appInfo->tin_review == 1 ? 'checked' : '' }}>
                                Tin
                        </label>
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="machinery_equipment_review" name="br_shortfall_review_section[]"
                                {{ isset($appInfo->machinery_equipment_review) && $appInfo->machinery_equipment_review == 1 ? 'checked' : '' }}>
                                Description of machinery and equipment
                        </label>
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="raw_materials_review" name="br_shortfall_review_section[]"
                                {{ isset($appInfo->raw_materials_review) && $appInfo->raw_materials_review == 1 ? 'checked' : '' }}>
                                Description of raw & packing materials
                        </label>
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="ceo_info_review" name="br_shortfall_review_section[]"
                                {{ isset($appInfo->ceo_info_review) && $appInfo->ceo_info_review == 1 ? 'checked' : '' }}>
                                Information of (Chairman/ Managing Director/ Or Equivalent)
                        </label>
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="director_list_review" id="director_list_review_id" onclick="autoCheckedAttachment()" name="br_shortfall_review_section[]"
                                {{ isset($appInfo->director_list_review) && $appInfo->director_list_review == 1 ? 'checked' : '' }}>
                                List of directors
                        </label>
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="imported_machinery_review" id="imported_machinery_review_id" onclick="autoCheckedAttachment()" name="br_shortfall_review_section[]"
                                {{ isset($appInfo->imported_machinery_review) && $appInfo->imported_machinery_review == 1 ? 'checked' : '' }}>
                                List of machinery to be imported and locally purchase/ procure
                        </label>
                    </div>

{{--                    <div class="checkbox">--}}
{{--                        <label>--}}
{{--                            <input type="checkbox" value="local_machinery_review"--}}
{{--                                   name="br_shortfall_review_section[]"> List of machinery locally purchase/ procure--}}
{{--                        </label>--}}
{{--                    </div>--}}

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="attachment_review" id="attachment_review_id" name="br_shortfall_review_section[]"
                                {{ isset($appInfo->attachment_review) && $appInfo->attachment_review == 1 ? 'checked' : '' }}>
                                Attachments
                        </label>
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="declaration_review" name="br_shortfall_review_section[]"
                                {{ isset($appInfo->declaration_review) && $appInfo->declaration_review == 1 ? 'checked' : '' }}>
                                Declaration and undertaking
                        </label>
                    </div>
                </div>
                {!! $errors->first('br_shortfall_review_section','<span class="help-block">:message</span>') !!}
            </div>
        </div>
    </fieldset>
</div>

<script>
    function checkedAllCheckbox() {
        if ($('#all_review_id').is(":checked")) {
            $('input[type="checkbox"]').prop('checked', true);
        } else {
            $('input[type="checkbox"]').prop('checked', false);
        }
    }

    function autoCheckedAttachment() {
        if ($('#manpower_review_id').is(":checked") || $('#director_list_review_id').is(":checked") || $('#imported_machinery_review_id').is(":checked")) {
            $('#attachment_review_id').prop('checked', true);
        } else {
            $('#attachment_review_id').prop('checked', false);
        }
    }

    function autoCheckListOfLocalAndImportedMachinery() {
        if($('#investment_review_id').is(":checked")) {
            $('#imported_machinery_review_id').prop('checked', true);
            autoCheckedAttachment();
        } else {
            $('#imported_machinery_review_id').prop('checked', false);
            autoCheckedAttachment();
        }
    }
</script>