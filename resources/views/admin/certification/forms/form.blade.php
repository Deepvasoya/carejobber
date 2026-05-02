{!! APFrmErrHelp::showErrorsNotice($errors) !!}
<div class="form-body">
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'name') !!}">
        {!! Form::label('name', 'Certification Name', ['class' => 'bold']) !!}
        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'e.g. First Aid & CPR, Medication Administration']) !!}
        {!! APFrmErrHelp::showErrors($errors, 'name') !!}
    </div>

    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'is_active') !!}">
        {!! Form::label('is_active', 'Active', ['class' => 'bold']) !!}
        <div class="radio-list">
            @php
                $active1 = old('is_active', isset($certification) ? $certification->is_active : 1) == 1 ? 'checked="checked"' : '';
                $active0 = old('is_active', isset($certification) ? $certification->is_active : 1) == 0 ? 'checked="checked"' : '';
            @endphp
            <label class="radio-inline"><input name="is_active" type="radio" value="1" {{ $active1 }}> Active</label>
            <label class="radio-inline"><input name="is_active" type="radio" value="0" {{ $active0 }}> Inactive</label>
        </div>
        {!! APFrmErrHelp::showErrors($errors, 'is_active') !!}
    </div>

    <div class="form-actions">
        {!! Form::button('Save <i class="fa fa-arrow-circle-right"></i>', ['class' => 'btn btn-large btn-primary', 'type' => 'submit']) !!}
        <a href="{{ route('list.certifications') }}" class="btn btn-default ms-2">Cancel</a>
    </div>
</div>