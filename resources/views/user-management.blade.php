@extends('layouts.adminLTE')

@section('title', 'User Management')

@section('page_title', 'User Management')

@section('breadcrumb')
    <li class="breadcrumb-item active">User Management</li>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col grid-margin stretch-card">
        <div class="card">
            <div class="card-header bg-info">
                <h2 class="d-inline">User List</h2>
                <div class="card-tools">
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#userModal" id="addNewUser"><i class="fas fa-plus"></i> Add New User</button>
                </div>
            </div>
            <div class="card-body">
                <table id="usersTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Vendor Location</th>
                            <th>Is Admin</th>
                            <th>Is Vendor</th>
                            <th>Is Employee</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Add New User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    <input type="hidden" id="userId" name="userId">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                    <div class="form-group">
                        <label for="vendor_loc">Vendor Location</label>
                        <select class="form-control" id="vendor_loc" name="vendor_loc">
                            <option value="">Select</option>
                            <option value="HQ">HQ</option>
                            <option value="NTPS">NTPS</option>
                            <option value="LTPS">LTPS</option>
                            <option value="LKHEP">LKHEP</option>
                            <option value="KLHEP">KLHEP</option>
                            <option value="Longku">Longku</option>
                            <option value="Narengi">Narengi</option>
                            <option value="Jagiroad">Jagiroad</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="must_change_passwd">Must Change Password</label>
                        <input type="checkbox" id="must_change_passwd" name="must_change_passwd" data-toggle="toggle" data-on="Yes" data-off="No" data-onstyle="success" data-offstyle="secondary">
                    </div>
                    <div class="form-group">
                        <label for="isAdmin">Is Admin</label>
                        <input type="checkbox" id="isAdmin" name="isAdmin" data-toggle="toggle" data-on="Yes" data-off="No" data-onstyle="success" data-offstyle="secondary">
                    </div>
                    <div class="form-group">
                        <label for="isVendor">Is Vendor</label>
                        <input type="checkbox" id="isVendor" name="isVendor" data-toggle="toggle" data-on="Yes" data-off="No" data-onstyle="success" data-offstyle="secondary">
                    </div>
                    <div class="form-group">
                        <label for="isEmployee">Is Employee</label>
                        <input type="checkbox" id="isEmployee" name="isEmployee" data-toggle="toggle" data-on="Yes" data-off="No" data-onstyle="success" data-offstyle="secondary">
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this user?
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Confirm</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
    {{-- Add Stylesheet here --}}
@endpush

@push('scripts')
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        var table = $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('user-management.index') }}",
            lengthMenu: [5, 10, 25, 50, 100],
            pageLength: 5,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'vendor_loc', name: 'vendor_loc', orderable: false, searchable: false },
                { data: 'isAdmin', name: 'isAdmin', render: function(data, type, row) {
                    return data ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>';
                }},
                { data: 'isVendor', name: 'isVendor', render: function(data, type, row) {
                    return data ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>';
                }},
                { data: 'isEmployee', name: 'isEmployee', render: function(data, type, row) {
                    return data ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>';
                }},
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            dom: '<"top"lfB>rt<"bottom"ip><"clear">',
            buttons: [
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: ':not(:last-child)'  // Exclude the last column (Actions)
                    }
                },
                {
                    extend: 'csv',
                    exportOptions: {
                        columns: ':not(:last-child)'  // Exclude the last column (Actions)
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: ':not(:last-child)'  // Exclude the last column (Actions)
                    }
                },
                {
                    extend: 'pdf',
                    exportOptions: {
                        columns: ':not(:last-child)'  // Exclude the last column (Actions)
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: ':not(:last-child)'  // Exclude the last column (Actions)
                    }
                }
            ],
        });

        $('#addNewUser').click(function() {
            $('#userModalLabel').text('Add New User');
            $('#userForm').trigger('reset');
            $('#userId').val('');
            $('#userModal').modal('show');
        });

        $('body').on('click', '.editUser', function() {
            var userId = $(this).data('id');
            $.get("{{ route('user-management.index') }}" +'/' + userId +'/edit', function (data) {
                $('#userModalLabel').text('Edit User');
                $('#userModal').modal('show');
                $('#userId').val(data.id);
                $('#name').val(data.name);
                $('#email').val(data.email);
                $('#password').val('');
                $('#vendor_loc').val(data.vendor_loc);
                $('#must_change_passwd').bootstrapToggle(data.must_change_passwd ? 'on' : 'off');
                $('#isAdmin').bootstrapToggle(data.isAdmin ? 'on' : 'off');
                $('#isVendor').bootstrapToggle(data.isVendor ? 'on' : 'off');
                $('#isEmployee').bootstrapToggle(data.isEmployee ? 'on' : 'off');
            })
        });

        $('#userForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var url = "{{ route('user-management.store') }}";

            if ($('#userId').val()) {
                url = "{{ route('user-management.update', ':id') }}".replace(':id', $('#userId').val());
                formData.append('_method', 'PUT');  // Use PUT method for update
            }

            $.ajax({
                type: "POST",  // POST is used here because formData contains _method=PUT
                url: url,
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#userModal').modal('hide');
                    $('#userForm').trigger('reset');
                    table.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                    });
                },
                error: function(response) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong!',
                    });
                }
            });
        });


        $('body').on('click', '.deleteUser', function() {
            var userId = $(this).data('id');
            $('#deleteModal').modal('show');

            $('#confirmDelete').click(function() {
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('user-management.destroy', ':id') }}".replace(':id', userId), // Corrected route
                    success: function(response) {
                        $('#deleteModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message,
                        });
                    },
                    error: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong!',
                        });
                    }
                });
            });
        });

        // Initialize Bootstrap Toggle on modal show
        $('#userModal').on('shown.bs.modal', function () {
            $('#must_change_passwd').bootstrapToggle();
            $('#isAdmin').bootstrapToggle();
            $('#isVendor').bootstrapToggle();
            $('#isEmployee').bootstrapToggle();
        });
    });
</script>
@endpush
