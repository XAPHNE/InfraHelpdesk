@extends('layouts.adminLTE')

@section('title', 'Ticket Details')

@section('page_title', 'Ticket Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('ticket-management.index') }}">Ticket Management</a></li>
    <li class="breadcrumb-item active">Ticket Details</li>
@stop

@section('content')
    <div class="card">
        <div class="card-header bg-info">
            <h3>Ticket Details</h3>
        </div>
        <div class="row">
            <!-- Ticket Details and Actions Taken -->
            <div class="col-md-6">
                <div class="card-body">
                    <h4>Ticket Number: {{ $ticket->ticket_number }}</h4>
                    @if (auth()->user()->isAdmin || auth()->user()->isVendor)
                        <p><strong>Raised by:</strong> {{ $ticket->creator->name }}</p>
                    @endif
                    <p><strong>Raised at:</strong> {{ $ticket->created_at->format('M d, Y h:i A') }}</p>
                    <p><strong>Product:</strong> {{ $ticket->subject }}</p>
                    <p><strong>Nature of Problem:</strong> {{ $ticket->description }}</p>
                    <p><strong>Location:</strong> {{ $ticket->location }}</p>
                    <p><strong>Status:</strong> {{ $ticket->status }}</p>
                    {{-- <p><strong>SLA Overdue on:</strong> {{ $ticket->sla_overdue->format('M d, Y h:i A') }}</p> --}}
                    @if ($ticket->closed_at)
                        <p><strong>Closed at:</strong> {{ $ticket->closed_at->format('M d, Y h:i A') }}</p>
                    @endif
                    @if ($ticket->time_taken)
                        <p><strong>Time Taken:</strong> {{ $ticket->time_taken_human }}</p>
                    @endif
                    <!-- Other ticket details -->
        
                    <hr>
        
                    <h5>Actions Taken:</h5>
                    <ul>
                        @foreach($ticket->actionTakens as $action)
                            <li>{{ $action->action_taken }} ({{ $action->created_at->format('M d, Y h:i A') }})</li>
                        @endforeach
                    </ul>
        
                    <!-- Add new action taken (only for vendors) -->
                    @if(auth()->user()->isVendor)
                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#actionModal" @if(auth()->user()->isVendor && $ticket->status == 'Closed') disabled @endif>
                            <i class="fas fa-plus"></i> Add Action Taken
                        </button>
                    @endif
                </div>
            </div>
        
            <!-- Form to Update Ticket -->
            <div class="col-md-6">
                <div class="card-body">
                    @if(auth()->user()->isAdmin || auth()->user()->isVendor)
                        <!-- Form (Visible only to Admin and Vendor) -->
                        <form id="updateTicketForm" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Call Type -->
                            <div class="form-group">
                                <label for="call_type">Type of call</label>
                                <select class="form-control" id="call_type" name="call_type" 
                                    @if(auth()->user()->isVendor && $ticket->status == 'Closed') disabled @endif>
                                    <option value="" {{ is_null($ticket->call_type) ? 'selected' : '' }}>-- Select --</option>
                                    <option value="Demo" {{ $ticket->call_type == 'Demo' ? 'selected' : '' }}>Demo</option>
                                    <option value="Installation" {{ $ticket->call_type == 'Installation' ? 'selected' : '' }}>Installation</option>
                                    <option value="Service" {{ $ticket->call_type == 'Service' ? 'selected' : '' }}>Service</option>
                                </select>
                            </div>
                        
                            <!-- Remarks -->
                            <div class="form-group">
                                <label for="remarks">Remarks:</label>
                                <textarea name="remarks" id="remarks" class="form-control" @if(auth()->user()->isVendor && $ticket->status == 'Closed') disabled @endif>{{ old('remarks', $ticket->remarks) }}</textarea>
                            </div>
                        
                            <!-- Status -->
                            <div class="form-group">
                                <label for="status">Status:</label>
                                <select name="status" id="status" class="form-control"
                                    @if(auth()->user()->isVendor && $ticket->status == 'Closed') disabled @endif>
                                    <option value="Open" {{ $ticket->status == 'Open' ? 'selected' : '' }}>Open</option>
                                    <option value="Closed" {{ $ticket->status == 'Closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                        
                            <button type="submit" class="btn btn-primary" @if(auth()->user()->isVendor && $ticket->status == 'Closed') disabled @endif>Update Ticket</button>
                            @if((auth()->user()->isAdmin || auth()->user()->isVendor) && $ticket->status == 'Closed')
    <button id="reopenTicketButton" class="btn btn-success">Reopen Ticket</button>
@endif
                        </form>
                    @endif
                    @if (auth()->user()->isEmployee)
                        <form id="updateTicketForm" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="mark_as_complete">
                                    <input type="checkbox" id="mark_as_complete" name="mark_as_complete" 
                                        {{ $ticket->isClosedByEmployee ? 'checked disabled' : '' }}>
                                    Mark as Complete
                                </label>
                            </div>

                            @if (!$ticket->isClosedByEmployee)
                                <button type="submit" class="btn btn-primary">Update</button>
                            @else
                                <p><strong>Marked as complete on:</strong> {{ $ticket->closedByEmployee_at->format('M d, Y h:i A') }}</p>
                            @endif
                        </form>
                    @endif
                </div>
            </div>
        </div>
        

    </div>

    <!-- Action Taken Modal -->
    <div class="modal fade" id="actionModal" tabindex="-1" role="dialog" aria-labelledby="actionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="actionModalLabel">Add Action Taken</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="actionForm">
                    <div class="modal-body">
                        <input type="hidden" id="ticket_id" name="ticket_id" value="{{ $ticket->id }}">
                        <div class="form-group">
                            <label for="action_taken">Action Taken</label>
                            <textarea class="form-control" id="action_taken" name="action_taken" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // Handle Action Taken form submission
    $('#actionForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "{{ route('action-taken.store') }}",  // Route to store action taken
            data: $(this).serialize(),
            success: function(response) {
                $('#actionModal').modal('hide');
                location.reload();  // Reload the page to see the new action
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

    // Handle Update Ticket form submission
    $('#updateTicketForm').submit(function(e) {
        e.preventDefault();
        
        // Serialize only the necessary form data
        var formData = $(this).serialize();

        // Get the ticket ID from a hidden input or elsewhere
        var ticketId = {{ $ticket->id }};
        var updateUrl = "{{ route('ticket-management.update', ':id') }}".replace(':id', ticketId);

        $.ajax({
            type: "POST",
            url: updateUrl,
            data: formData,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message,
                });
                location.reload();  // Reload the page to see the updated details
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

    $('#reopenTicketButton').click(function(){
        var ticketId = {{ $ticket->id }};
        var updateUrl = "{{ route('ticket-management.update', ':id') }}".replace(':id', ticketId);
        $.ajax({
            type: "POST",
            url: updateUrl,
            data: {
                _method: 'PUT',
                status: 'Open'
            },
            success: function(response){
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Ticket reopened successfully'
                });
                location.reload();
            },
            error: function(response){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Could not reopen ticket'
                });
            }
        });
    });
</script>
@endpush

