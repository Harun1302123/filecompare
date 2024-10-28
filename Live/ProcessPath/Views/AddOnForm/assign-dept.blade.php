<div class="ad_desk_form form-group">
    <div class="col-md-6">
        {!! Form::label('assign_dept_id','Assign department') !!}
        {!! Form::select('assign_dept_id',$departmentList, '',['class'=>'form-control input-md required', 'id' => 'assign_dept_id']) !!}
    </div>
</div>