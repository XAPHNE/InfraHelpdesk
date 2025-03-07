@extends('layouts.adminLTE')

@section('title', 'Ticket Management')

@section('page_title', 'Ticket Management')

@section('breadcrumb')
    <li class="breadcrumb-item active">Ticket Management</li>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col grid-margin stretch-card">
        <div class="card card-info">
            <div class="card-header">
                <h2 class="d-inline">Ticket List</h2>
                <div class="card-tools">
                    @if(!auth()->user()->isVendor)
                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#ticketModal" id="addNewTicket"><i class="fas fa-plus"></i> Add New Ticket</button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                @if (auth()->user()->isAdmin || auth()->user()->isVendor)
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="date" id="start_date" class="form-control" placeholder="Start Date">
                        </div>
                        <div class="col-md-3">
                            <input type="date" id="end_date" class="form-control" placeholder="End Date">
                        </div>
                        <div class="col-md-4">
                            <button id="filter" class="btn btn-info"><i class="fas fa-search"></i></button>
                            <button id="clearFilterButton" class="btn btn-secondary">Clear</button>
                        </div>
                    </div>
                @endif
                <table id="ticketsTable" class="display nowrap table table-bordered table-hover">
                    <thead>
                        <tr class="table-primary">
                            <th class="text-center">#</th>
                            <th class="text-center">Ticket Number</th>
                            @if (auth()->user()->isAdmin || auth()->user()->isVendor)
                                <th>Created By</th>
                            @endif
                            <th class="text-center">Created At</th>
                            @if (auth()->user()->isAdmin || auth()->user()->isVendor)
                                <th class="text-center">Location</th>
                            @endif
                            <th class="text-center">Product</th>
                            <th class="text-center">Serial number</th>
                            @if (auth()->user()->isAdmin || auth()->user()->isVendor)
                                <th class="text-center">Call Type</th>
                                <th class="text-center">Time Taken</th>
                                <th class="text-center" style="min-width: 250px; max-width:350px">Action Taken</th>
                            @endif
                            <th class="text-center">Status</th>
                            <th class="text-center">Remarks</th>
                            <th class="text-center">Closed By</th>
                            <th class="text-center">Closed At</th>
                            @if (auth()->user()->isAdmin || auth()->user()->isVendor)
                                <th class="text-center">Actions</th>
                            @endif
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Ticket Modal -->
<div class="modal fade" id="ticketModal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ticketModalLabel">Add New Ticket</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="ticketForm">
                <div class="modal-body">
                    <input type="hidden" id="ticket_id" name="ticket_id">
                    <!-- Location Field (Only for new tickets) -->
                    <div class="form-group" id="location-group">
                        <label for="location">Location</label>
                        <select class="form-control" id="location" name="location" required>
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
                    <div class="form-group @if(auth()->user()->isVendor)disabled @endif">
                        <label for="subject">Product</label>
                        <select class="form-control" id="subject" name="subject" required @if(auth()->user()->isVendor) readonly disabled @endif>
                            <option value="">Select</option>
                            <option value="Desktop">Desktop</option>
                            <option value="Keyboard">Keyboard</option>
                            <option value="Laptop">Laptop</option>
                            <option value="Monitor">Monitor</option>
                            <option value="Mouse">Mouse</option>
                            <option value="Network">Network</option>
                            <option value="Office">Office</option>
                            <option value="Touchpad">Touchpad</option>
                            <option value="UPS">UPS</option>
                            <option class="font-weight-bold" value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="serial_num">Product serial number (Optional)</label>
                        <input type="text" class="form-control" id="serial_num" name="serial_num">
                    </div>
                    <div class="form-group">
                        <label for="description">Nature of problem</label>
                        <textarea class="form-control" id="description" name="description" required @if(auth()->user()->isVendor) readonly @endif></textarea>
                    </div>
                    <!-- Type of call Field (Only for Admin or Vendor) -->
                    @if (auth()->user()->isAdmin || auth()->user()->isVendor)
                        <div class="form-group">
                            <label for="call_type">Type of call</label>
                            <select class="form-control" id="call_type" name="call_type">
                                <option value="">Select</option>
                                <option value="Demo">Demo</option>
                                <option value="Installation">Installation</option>
                                <option value="Service">Service</option>
                            </select>
                        </div>
                    @endif
                    <!-- Status Field (Only for edit/update) -->
                    <div class="form-group" id="status-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="Open">Open</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>
                    <!-- Remarks Field (Only for Admin or Vendor) -->
                    @if(auth()->user()->isAdmin || auth()->user()->isVendor)
                    <div class="form-group" id="remarks-group">
                        <label for="remarks">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks"></textarea>
                    </div>
                    @endif
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
                Are you sure you want to delete this ticket?
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
    <style>
        .action-taken-column {
            width: 250px; /* Adjust the width as needed */
            white-space: normal; /* Allow text wrapping */
        }
    </style>
@endpush

@push('scripts')
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        // Pass the user's role information from Blade to JavaScript
        var isAdmin = @json(auth()->user()->isAdmin);
        var isVendor = @json(auth()->user()->isVendor);

        var table = $('#ticketsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('ticket-management.index') }}",
                data: function(d) {
                    if (isAdmin || isVendor) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                }
            },
            lengthMenu: [10, 25, 50, 100, 250, 500, 1000],
            lengthChange: true,
            scrollX: true,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', class: 'text-center', orderable: false, searchable: false },
                { 
                    data: 'ticket_number',
                    name: 'ticket_number',
                    class: 'text-right',
                    render: function(data, type, row) {
                        return '<a href="/ticket-management/' + row.id + '/details">' + data + '</a>';
                    }
                },
                // Conditionally add 'Created By' column
                ...(isAdmin || isVendor ? [{ data: 'created_by', name: 'created_by', class: 'text-center' }] : []),
                { data: 'created_at', name: 'created_at', class: 'text-center', render: function(data, type, row) {
                    return moment(data).format('DD-MM-YYYY, HH:mm A');
                }},
                // Conditionally add 'Location' column
                ...(isAdmin || isVendor ? [{ data: 'location', name: 'location', class: 'text-center' }] : []),
                { data: 'subject', name: 'subject', class: 'text-center' },
                { data: 'serial_num', name: 'serial_num', class: 'text-center' },
                // Conditionally add 'Call Type' and 'Time Taken' columns
                ...(isAdmin || isVendor ? [
                    { data: 'call_type', name: 'call_type', class: 'text-center' },
                    { data: 'time_taken', name: 'time_taken', class: 'text-center' },
                    { data: 'action_taken', name: 'action_taken' }
                ] : []),
                { data: 'status', name: 'status', class: 'text-center' },
                { data: 'remarks', name: 'remarks' },
                { data: 'closed_by', name: 'closed_by', class: 'text-center' },
                { data: 'closed_at', name: 'closed_at', class: 'text-center' },
                // Conditionally add 'Actions' column
                ...(isAdmin || isVendor ? [{ data: 'action', name: 'action', class: 'text-center', orderable: false, searchable: false }] : [])
            ],
            layout: {
                topEnd: {
                    buttons: ['excel'],
                    search :true
                },
            },
        });

        // Event listener for the filter button
        $('#filter').click(function() {
            table.draw();
        });

        $('#clearFilterButton').click(function() {
            $('#start_date').val('');
            $('#end_date').val('');
            table.draw();
        });

        $('#addNewTicket').click(function() {
            $('#ticketModalLabel').text('Add New Ticket');
            $('#ticketForm').trigger('reset');
            $('#ticket_id').val('');
            $('#status-group').hide(); // Hide the status field for adding a new ticket
            $('#location-group').show(); // Show the location field for adding a new ticket
            $('#ticketModal').modal('show');
        });

        $('body').on('click', '.editTicket', function() {
            var ticketId = $(this).data('id');
            $.get("{{ route('ticket-management.index') }}" + '/' + ticketId + '/edit', function(data) {
                $('#ticketModalLabel').text('Edit Ticket');
                $('#ticketModal').modal('show');
                $('#ticket_id').val(data.id);
                $('#location-group').hide(); // Hide the location field for editing
                $('#location').val(data.location);
                $('#status-group').show(); // Show the status field for editing
                $('#subject').val(data.subject);
                $('#serial_num').val(data.serial_num);
                $('#description').val(data.description);
                $('#call_type').val(data.call_type);
                $('#remarks').val(data.remarks);
                $('#status').val(data.status);

                @if(auth()->user()->isVendor)
                    $('#subject').prop('readonly', true);
                    $('#description').prop('readonly', true);
                @endif
            });
        });

        $('#ticketForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var url = "{{ route('ticket-management.store') }}";
            var $submitButton = $(this).find('button[type="submit"]');
            var $closeButton = $(this).find('button[class="close"]');
            alert("Please give a moment for processing");

            if ($('#ticket_id').val()) {
                url = "{{ route('ticket-management.update', ':id') }}".replace(':id', $('#ticket_id').val());
                formData.append('_method', 'PUT');
            }

            // Disable the save button
            $submitButton.prop('disabled', true);
            $closeButton.prop('disabled', true);

            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $submitButton.prop('disabled', false);
                    $closeButton.prop('disabled', true);
                    $('#ticketModal').modal('hide');
                    $('#ticketForm').trigger('reset');
                    table.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                    });
                },
                error: function(response) {
                    $submitButton.prop('disabled', false);
                    $closeButton.prop('disabled', true);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong!',
                    });
                },
                // complete: function() {
                //     // Re-enable the save button
                //     $submitButton.prop('disabled', false);
                // }
            });
        });

        $('body').on('click', '.deleteTicket', function() {
            var ticketId = $(this).data('id');
            $('#deleteModal').modal('show');

            $('#confirmDelete').click(function() {
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('ticket-management.destroy', ':id') }}".replace(':id', ticketId),
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
