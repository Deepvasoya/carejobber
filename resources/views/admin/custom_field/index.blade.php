@extends('admin.layouts.admin_layout')
@section('content')
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li><a href="{{ route('admin.home') }}">Home</a> <i class="fa fa-circle"></i></li>
                <li><span>{{ __('Custom Fields') }}</span></li>
            </ul>
        </div>
        <h3 class="page-title">{{ __('Custom Fields') }} <small>{{ __('Profile, job listing, resume builder') }}</small></h3>
        @include('flash::message')

        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="ri ri-input-cursor-move font-dark"></i>
                    <span class="caption-subject font-dark bold uppercase">{{ __('Fields') }}</span>
                </div>
                <div class="actions">
                    <a href="{{ route('create.custom.field') }}" class="btn btn-sm btn-success">
                        <i class="fa fa-plus"></i> {{ __('Add Custom Field') }}
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <p class="text-muted">{{ __('Define fields here, then include them in forms using the Blade partial (see code comments).') }}</p>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('Label') }}</th>
                                <th>{{ __('Slug') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Contexts') }}</th>
                                <th>{{ __('Order') }}</th>
                                <th>{{ __('Active') }}</th>
                                <th width="160">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fields as $f)
                            <tr>
                                <td>
                                    @if($f->icon_url)
                                        <img src="{{ $f->icon_url }}" alt="" style="max-height:32px;max-width:32px;object-fit:contain;vertical-align:middle;margin-right:8px;" />
                                    @endif
                                    <strong>{{ $f->label }}</strong>
                                    @if($f->is_required)<span class="badge bg-secondary ms-1">{{ __('Required') }}</span>@endif
                                </td>
                                <td><code>{{ $f->slug }}</code></td>
                                <td>{{ \App\Models\CustomField::fieldTypeLabels()[$f->field_type] ?? $f->field_type }}</td>
                                <td>
                                    @foreach($f->contexts ?? [] as $c)
                                        <span class="badge bg-light text-dark border">{{ \App\Models\CustomField::contextLabels()[$c] ?? $c }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $f->sort_order }}</td>
                                <td>@if($f->is_active)<span class="label label-success">{{ __('Yes') }}</span>@else<span class="label label-default">{{ __('No') }}</span>@endif</td>
                                <td>
                                    <a href="{{ route('edit.custom.field', $f->id) }}" class="btn btn-xs btn-primary">{{ __('Edit') }}</a>
                                    <form action="{{ route('delete.custom.field', $f->id) }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('Delete this field?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted">{{ __('No custom fields yet.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $fields->links() }}
            </div>
        </div>

        <div class="portlet light bordered">
            <div class="portlet-body">
                <h4>{{ __('Using in forms') }}</h4>
                <p>{{ __('In a Blade template, render all active fields for a context:') }}</p>
                <pre class="bg-light p-3 rounded border small"><code>@verbatim
@include('includes.custom_fields_for_context', ['context' => 'profile'])
@endverbatim</code></pre>
                <p class="mb-0"><code>context</code>: <code>profile</code>, <code>job_listing</code>, or <code>resume_builder</code>.</p>
            </div>
        </div>
    </div>
</div>
@endsection
