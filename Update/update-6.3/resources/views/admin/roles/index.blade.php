@extends('admin.layouts.admin_layout')
@section('content')
<style type="text/css">
    .table td, .table th { font-size: 12px; line-height: 2.42857 !important; }
</style>
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li><a href="{{ route('admin.home') }}">Home</a> <i class="fa fa-circle"></i></li>
                <li><span>Manage Roles</span></li>
            </ul>
        </div>
        <h3 class="page-title">Manage Roles & Permissions</h3>
        @include('flash::message')
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light portlet-fit portlet-datatable bordered">
                    <div class="portlet-title">
                        <div class="actions">
                            <a href="{{ route('create.role') }}" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-plus"></i> Add New Role</a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-striped table-bordered table-hover" id="roles_datatable_ajax">
                            <thead>
                                <tr class="heading">
                                    <th>Role Name</th>
                                    <th>Abbreviation</th>
                                    <th>Description</th>
                                    <th>Admin Users</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
$(function () {
    $('#roles_datatable_ajax').DataTable({
        order: [[0, 'asc']],
        processing: true,
        serverSide: true,
        stateSave: true,
        ajax: '{!! route('fetch.data.roles') !!}',
        columns: [
            { data: 'role_name', name: 'roles.role_name' },
            { data: 'role_abbreviation', name: 'roles.role_abbreviation' },
            { data: 'role_description', name: 'roles.role_description' },
            { data: 'admins_count', name: 'admins_count', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
});
function deleteRole(id) {
    if (confirm('Are you sure you want to delete this role?')) {
        $.post("{{ route('delete.role') }}", { id: id, _method: 'DELETE', _token: '{{ csrf_token() }}' })
            .done(function (response) {
                if (response == 'ok') {
                    var table = $('#roles_datatable_ajax').DataTable();
                    table.row('#role_dt_row_' + id).remove().draw(false);
                } else if (response == 'in_use') {
                    alert('This role is assigned to admin users. Remove the assignment first.');
                } else {
                    alert('Request Failed!');
                }
            });
    }
}
</script>
@endpush
