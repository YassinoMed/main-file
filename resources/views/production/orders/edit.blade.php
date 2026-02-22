{{ Form::model($order, ['route' => ['production.orders.update', $order->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('warehouse_id', __('Warehouse'), ['class' => 'form-label']) }}
            {{ Form::select('warehouse_id', $warehouses, null, ['class' => 'form-control', 'placeholder' => __('Select Warehouse')]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('work_center_id', __('Work Center'), ['class' => 'form-label']) }}
            {{ Form::select('work_center_id', $workCenters, null, ['class' => 'form-control', 'placeholder' => __('Select Work Center')]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('employee_id', __('Employee'), ['class' => 'form-label']) }}
            {{ Form::select('employee_id', $employees, null, ['class' => 'form-control', 'placeholder' => __('Select Employee')]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('priority', __('Priority'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('priority', ['low' => __('Low'), 'normal' => __('Normal'), 'high' => __('High'), 'urgent' => __('Urgent')], null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('planned_start_date', __('Planned Start Date'), ['class' => 'form-label']) }}
            {{ Form::date('planned_start_date', $order->planned_start_date, ['class' => 'form-control']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('planned_end_date', __('Planned End Date'), ['class' => 'form-label']) }}
            {{ Form::date('planned_end_date', $order->planned_end_date, ['class' => 'form-control']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}
            {{ Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3]) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
</div>
{{ Form::close() }}

