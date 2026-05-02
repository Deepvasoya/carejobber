@extends('admin.layouts.admin_layout')
@section('content')
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li><a href="{{ route('admin.home') }}">Home</a> <i class="fa fa-circle"></i></li>
                <li><span>Other Certifications</span></li>
            </ul>
        </div>
        <h3 class="page-title">Manage Other Certifications <small>Certifications</small></h3>

        <div class="row">
            <div class="col-md-12">
                <div class="portlet light portlet-fit portlet-datatable bordered">
                    <div class="portlet-title">
                        <div class="caption"><i class="icon-settings font-dark"></i> <span class="caption-subject font-dark sbold uppercase">Other Certifications</span></div>
                        <div class="actions">
                            <a href="{{ route('create.certification') }}" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-plus"></i> Add New Certification</a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-container">
                            <form method="post" id="cert-search-form">
                                <table class="table table-striped table-bordered table-hover" id="certDatatableAjax">
                                    <thead>
                                        <tr class="filter">
                                            <td><input type="text" class="form-control" name="name" id="cert_name" placeholder="Certification Name"></td>
                                            <td>
                                                <select name="is_active" id="cert_is_active" class="form-control">
                                                    <option value="-1">Is Active?</option>
                                                    <option value="1" selected>Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr class="heading">
                                            <th>Name</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </form>
                        </div>
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
    var oTable = $('#certDatatableAjax').DataTable({
        processing: true, serverSide: true, stateSave: true, searching: false,
        ajax: {
            url: '{{ route('fetch.data.certifications') }}',
            data: function (d) {
                d.name      = $('#cert_name').val();
                d.is_active = $('#cert_is_active').val();
            }
        },
        columns: [
            {data: 'name', name: 'name'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
    $('#cert_name').on('keyup', function () { oTable.draw(); });
    $('#cert_is_active').on('change', function () { oTable.draw(); });
});

function deleteCertification(id) {
    if (!confirm('Are you sure?')) return;
    $.post('{{ route('delete.certification') }}', {id: id, _method: 'DELETE', _token: '{{ csrf_token() }}'})
        .done(function (r) {
            if (r === 'ok') { $('#certDatatableAjax').DataTable().row('#certDtRow' + id).remove().draw(false); }
            else { alert('Request Failed!'); }
        });
}
function makeActive(id) {
    $.post('{{ route('make.active.certification') }}', {id: id, _method: 'PUT', _token: '{{ csrf_token() }}'})
        .done(function (r) { if (r === 'ok') { $('#certDatatableAjax').DataTable().draw(); } });
}
function makeNotActive(id) {
    $.post('{{ route('make.not.active.certification') }}', {id: id, _method: 'PUT', _token: '{{ csrf_token() }}'})
        .done(function (r) { if (r === 'ok') { $('#certDatatableAjax').DataTable().draw(); } });
}
</script>
@endpush