{{ Form::model($bom, ['route' => ['production.boms.update', $bom->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('product_id', __('Product'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('product_id', $products, null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Select Product')]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Name')]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('code', __('Code'), ['class' => 'form-label']) }}
            {{ Form::text('code', null, ['class' => 'form-control', 'placeholder' => __('Enter Code')]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('version', __('Active Version'), ['class' => 'form-label']) }}
            {{ Form::text('version', $bom->activeVersion?->version, ['class' => 'form-control', 'placeholder' => __('Enter Version')]) }}
        </div>
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">{{ __('Components') }}</h6>
                <a href="#" class="btn btn-sm btn-primary" id="bom-add-row">{{ __('Add Row') }}</a>
            </div>
            <div class="table-responsive">
                <table class="table" id="bom-components-table">
                    <thead>
                        <tr>
                            <th>{{ __('Component') }}</th>
                            <th style="width: 160px;">{{ __('Quantity') }}</th>
                            <th style="width: 160px;">{{ __('Scrap %') }}</th>
                            <th style="width: 60px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $lines = $bom->activeVersion?->lines ?? collect();
                        @endphp
                        @if ($lines->count() > 0)
                            @foreach ($lines as $line)
                                <tr>
                                    <td>
                                        {{ Form::select('components[]', $components, $line->component_product_id, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Select Component')]) }}
                                    </td>
                                    <td>
                                        {{ Form::number('quantities[]', $line->quantity, ['class' => 'form-control', 'required' => 'required', 'step' => '0.0001', 'min' => 0.0001]) }}
                                    </td>
                                    <td>
                                        {{ Form::number('scrap_percents[]', $line->scrap_percent, ['class' => 'form-control', 'step' => '0.01', 'min' => 0, 'max' => 100]) }}
                                    </td>
                                    <td class="text-end">
                                        <a href="#" class="btn btn-sm btn-danger bom-remove-row"><i class="ti ti-trash"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td>
                                    {{ Form::select('components[]', $components, null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Select Component')]) }}
                                </td>
                                <td>
                                    {{ Form::number('quantities[]', 1, ['class' => 'form-control', 'required' => 'required', 'step' => '0.0001', 'min' => 0.0001]) }}
                                </td>
                                <td>
                                    {{ Form::number('scrap_percents[]', 0, ['class' => 'form-control', 'step' => '0.01', 'min' => 0, 'max' => 100]) }}
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-danger bom-remove-row"><i class="ti ti-trash"></i></a>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
</div>
{{ Form::close() }}

<script>
    (function() {
        var addBtn = document.getElementById('bom-add-row');
        var tableBody = document.querySelector('#bom-components-table tbody');

        function bindRemove(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var rows = tableBody.querySelectorAll('tr');
                if (rows.length <= 1) {
                    return;
                }
                btn.closest('tr').remove();
            });
        }

        tableBody.querySelectorAll('.bom-remove-row').forEach(bindRemove);

        addBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var row = tableBody.querySelector('tr').cloneNode(true);
            row.querySelectorAll('select').forEach(function(select) {
                select.value = '';
            });
            row.querySelectorAll('input').forEach(function(input) {
                if (input.name === 'quantities[]') {
                    input.value = 1;
                } else {
                    input.value = 0;
                }
            });
            var removeBtn = row.querySelector('.bom-remove-row');
            removeBtn.replaceWith(removeBtn.cloneNode(true));
            bindRemove(row.querySelector('.bom-remove-row'));
            tableBody.appendChild(row);
        });
    })();
</script>

