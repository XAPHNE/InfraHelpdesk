@extends('layouts.adminLTE')

@section('title', 'User Management')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">User List</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#userModal" id="addNewUser">
                Add New User
            </button>
        </div>
    </div>
    <div class="card-body">
        <table id="usersTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Is Admin</th>
                    <th>Is Vendor</th>
                    <th>Is Employee</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
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
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet"> --}}
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    var table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('user-management.index') }}",
        columns: [
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
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
        ]
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
            $('#isAdmin').bootstrapToggle(data.isAdmin ? 'on' : 'off');
            $('#isVendor').bootstrapToggle(data.isVendor ? 'on' : 'off');
            $('#isEmployee').bootstrapToggle(data.isEmployee ? 'on' : 'off');
        })
    });

    $('#userForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: "POST",
            url: "{{ route('user-management.store') }}",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#userModal').modal('hide');
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
                url: "{{ route('user-management.store') }}" + '/' + userId,
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
});
</script>
@endpush
