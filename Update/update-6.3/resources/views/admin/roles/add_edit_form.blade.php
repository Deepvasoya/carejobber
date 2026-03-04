@if (count($errors) > 0)
<div class="alert alert-danger">
    There were some problems with your input.
</div>
@foreach ($errors->all() as $error)
{{ $error }}<br/>
@endforeach
@endif
@include('flash::message')
<div class="form-body">
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'role_name') !!}">
        {!! Form::label('role_name', 'Role Name', ['class' => 'bold']) !!}
        {!! Form::text('role_name', null, ['required', 'class' => 'form-control', 'placeholder' => 'e.g. Content Editor']) !!}
        {!! APFrmErrHelp::showErrors($errors, 'role_name') !!}
    </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'role_abbreviation') !!}">
        {!! Form::label('role_abbreviation', 'Abbreviation', ['class' => 'bold']) !!}
        {!! Form::text('role_abbreviation', null, ['required', 'class' => 'form-control', 'placeholder' => 'e.g. CONT_ED']) !!}
        {!! APFrmErrHelp::showErrors($errors, 'role_abbreviation') !!}
    </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'role_description') !!}">
        {!! Form::label('role_description', 'Description', ['class' => 'bold']) !!}
        {!! Form::text('role_description', null, ['class' => 'form-control', 'placeholder' => 'Short description']) !!}
        {!! APFrmErrHelp::showErrors($errors, 'role_description') !!}
    </div>

    <div class="form-group mb-3">
        <label class="bold">Permissions</label>
        <p class="text-muted small">Select which permissions this role has. Permissions are grouped by module.</p>
        <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
            @forelse($permissions as $module => $items)
            <div class="mb-3">
                <strong class="text-primary">{{ $module ?: 'General' }}</strong>
                <div class="ms-3 mt-1">
                    @foreach($items as $perm)
                    <div class="form-check">
                        {!! Form::checkbox('permissions[]', $perm->id, in_array($perm->id, $rolePermissionIds ?? []), ['class' => 'form-check-input', 'id' => 'perm_' . $perm->id]) !!}
                        <label class="form-check-label" for="perm_{{ $perm->id }}">{{ $perm->name }} <small class="text-muted">({{ $perm->slug }})</small></label>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <p class="text-muted">No permissions defined. Run migrations to seed default permissions: <code>php artisan migrate</code></p>
            @endforelse
        </div>
    </div>
</div>
