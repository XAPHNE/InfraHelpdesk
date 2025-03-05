@extends('layouts.adminLTE')

@section('title', 'Dashboard')

@section('content-header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <!-- Open Tickets in Current Quarter -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $openTicketsQuarter }}</h3>
                    <p>Open Tickets (Current Quarter)</p>
                </div>
                <div class="icon">
                    <i class="ion ion-clipboard"></i>
                </div>
                <a href="javascript:void(0);" class="small-box-footer" onclick="showSection('open-tickets-quarter')">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Closed Tickets in Current Quarter -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $closedTicketsQuarter }}</h3>
                    <p>Closed Tickets (Current Quarter)</p>
                </div>
                <div class="icon">
                    <i class="ion ion-checkmark-circled"></i>
                </div>
                <a href="javascript:void(0);" class="small-box-footer" onclick="showSection('closed-tickets-quarter')">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Open Tickets in Current Month -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $openTicketsMonth }}</h3>
                    <p>Open Tickets (Current Month)</p>
                </div>
                <div class="icon">
                    <i class="ion ion-clipboard"></i>
                </div>
                <a href="javascript:void(0);" class="small-box-footer" onclick="showSection('open-tickets-month')">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Closed Tickets in Current Month -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $closedTicketsMonth }}</h3>
                    <p>Closed Tickets (Current Month)</p>
                </div>
                <div class="icon">
                    <i class="ion ion-checkmark-circled"></i>
                </div>
                <a href="javascript:void(0);" class="small-box-footer" onclick="showSection('closed-tickets-month')">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        @if (auth()->user()->isAdmin)
        <!-- SLA Overdue Tickets (Current Year) -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $slaOverdueYear }}</h3>
                    <p>SLA Overdue (This Year)</p>
                </div>
                <div class="icon">
                    <i class="ion ion-alert"></i>
                </div>
                <a href="javascript:void(0);" class="small-box-footer" onclick="showSection('sla-overdue-year')">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- SLA Overdue Tickets (Current Quarter) -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $slaOverdueQuarter }}</h3>
                    <p>SLA Overdue (This Quarter)</p>
                </div>
                <div class="icon">
                    <i class="ion ion-alert"></i>
                </div>
                <a href="javascript:void(0);" class="small-box-footer" onclick="showSection('sla-overdue-quarter')">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- SLA Overdue Tickets (Current Month) -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $slaOverdueMonth }}</h3>
                    <p>SLA Overdue (This Month)</p>
                </div>
                <div class="icon">
                    <i class="ion ion-alert"></i>
                </div>
                <a href="javascript:void(0);" class="small-box-footer" onclick="showSection('sla-overdue-month')">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- SLA Overdue Tickets (Last Month) -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $slaOverdueLastMonth }}</h3>
                    <p>SLA Overdue (Last Month)</p>
                </div>
                <div class="icon">
                    <i class="ion ion-alert"></i>
                </div>
                <a href="javascript:void(0);" class="small-box-footer" onclick="showSection('sla-overdue-last-month')">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        @endif
    </div>
    <!-- Detailed Ticket Tables Section -->
    <section>
        <!-- Open Tickets (Current Quarter) Table -->
        <div id="open-tickets-quarter" class="table-section" style="display: none;">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title">Open Tickets (Current Quarter) Details</h5>
                </div>
                <div class="card-body">
                    @if($openTicketsQuarterDetails->count())
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped dataTable" style="width:100%">
                                <thead>
                                    <tr class="table-primary">
                                        <th>Ticket ID</th>
                                        <th>Product</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($openTicketsQuarterDetails as $ticket)
                                        <tr>
                                            <td><a href="{{ route('ticket-management.details', $ticket->id) }}">{{ $ticket->ticket_number }}</a></td>
                                            <td>{{ $ticket->subject }}</td>
                                            <td>{{ $ticket->location }}</td>
                                            <td>{{ $ticket->status }}</td>
                                            <td>{{ $ticket->created_at->format('d-m-Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>No open ticket details available for the current quarter.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Closed Tickets (Current Quarter) Table -->
        <div id="closed-tickets-quarter" class="table-section" style="display: none;">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title">Closed Tickets (Current Quarter) Details</h5>
                </div>
                <div class="card-body">
                    @if($closedTicketsQuarterDetails->count())
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped dataTable" style="width:100%">
                                <thead>
                                    <tr class="table-primary">
                                        <th>Ticket ID</th>
                                        <th>Product</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Closed Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($closedTicketsQuarterDetails as $ticket)
                                        <tr>
                                            <td><a href="{{ route('ticket-management.details', $ticket->id) }}">{{ $ticket->ticket_number }}</a></td>
                                            <td>{{ $ticket->subject }}</td>
                                            <td>{{ $ticket->location }}</td>
                                            <td>{{ $ticket->status }}</td>
                                            <td>{{ $ticket->closed_at ? $ticket->closed_at->format('d-m-Y') : 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>No closed ticket details available for the current quarter.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Similarly, add sections for Open Tickets (Current Month) and Closed Tickets (Current Month) -->
        <div id="open-tickets-month" class="table-section" style="display: none;">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title">Open Tickets (Current Month) Details</h5>
                </div>
                <div class="card-body">
                    @if($openTicketsMonthDetails->count())
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped dataTable" style="width:100%">
                                <thead>
                                    <tr class="table-primary">
                                        <th>Ticket ID</th>
                                        <th>Product</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($openTicketsMonthDetails as $ticket)
                                        <tr>
                                            <td><a href="{{ route('ticket-management.details', $ticket->id) }}">{{ $ticket->ticket_number }}</a></td>
                                            <td>{{ $ticket->subject }}</td>
                                            <td>{{ $ticket->location }}</td>
                                            <td>{{ $ticket->status }}</td>
                                            <td>{{ $ticket->created_at->format('d-m-Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>No open ticket details available for the current month.</p>
                    @endif
                </div>
            </div>
        </div>

        <div id="closed-tickets-month" class="table-section" style="display: none;">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title">Closed Tickets (Current Month) Details</h5>
                </div>
                <div class="card-body">
                    @if($closedTicketsMonthDetails->count())
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped dataTable" style="width:100%">
                                <thead>
                                    <tr class="table-primary">
                                        <th>Ticket ID</th>
                                        <th>Product</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Closed Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($closedTicketsMonthDetails as $ticket)
                                        <tr>
                                            <td><a href="{{ route('ticket-management.details', $ticket->id) }}">{{ $ticket->ticket_number }}</a></td>
                                            <td>{{ $ticket->subject }}</td>
                                            <td>{{ $ticket->location }}</td>
                                            <td>{{ $ticket->status }}</td>
                                            <td>{{ $ticket->closed_at ? $ticket->closed_at->format('d-m-Y') : 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>No closed ticket details available for the current month.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- SLA Overdue Tables -->
        <!-- SLA Overdue (This Year) Table -->
        <div id="sla-overdue-year" class="table-section" style="display: none;">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title">SLA Overdue (This Year) Details</h5>
                </div>
                <div class="card-body">
                    @if($slaOverdueYearDetails->count())
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped dataTable" style="width:100%">
                                <thead>
                                    <tr class="table-primary">
                                        <th>Ticket ID</th>
                                        <th>Product</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>SLA Overdue Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($slaOverdueYearDetails as $ticket)
                                        <tr>
                                            <td><a href="{{ route('ticket-management.details', $ticket->id) }}">{{ $ticket->ticket_number }}</a></td>
                                            <td>{{ $ticket->subject }}</td>
                                            <td>{{ $ticket->location }}</td>
                                            <td>{{ $ticket->status }}</td>
                                            <td>{{ $ticket->sla_overdue->format('d-m-Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>No SLA overdue details available for the current year.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- SLA Overdue (This Quarter) Table -->
        <div id="sla-overdue-quarter" class="table-section" style="display: none;">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title">SLA Overdue (This Quarter) Details</h5>
                </div>
                <div class="card-body">
                    @if($slaOverdueQuarterDetails->count())
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped dataTable" style="width:100%">
                                <thead>
                                    <tr class="table-primary">
                                        <th>Ticket ID</th>
                                        <th>Product</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>SLA Overdue Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($slaOverdueQuarterDetails as $ticket)
                                        <tr>
                                            <td><a href="{{ route('ticket-management.details', $ticket->id) }}">{{ $ticket->ticket_number }}</a></td>
                                            <td>{{ $ticket->subject }}</td>
                                            <td>{{ $ticket->location }}</td>
                                            <td>{{ $ticket->status }}</td>
                                            <td>{{ $ticket->sla_overdue->format('d-m-Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>No SLA overdue details available for the current quarter.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- SLA Overdue (This Month) Table -->
        <div id="sla-overdue-month" class="table-section" style="display: none;">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title">SLA Overdue (This Month) Details</h5>
                </div>
                <div class="card-body">
                    @if($slaOverdueMonthDetails->count())
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped dataTable" style="width:100%">
                                <thead>
                                    <tr class="table-primary">
                                        <th>Ticket ID</th>
                                        <th>Product</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>SLA Overdue Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($slaOverdueMonthDetails as $ticket)
                                        <tr>
                                            <td><a href="{{ route('ticket-management.details', $ticket->id) }}">{{ $ticket->ticket_number }}</a></td>
                                            <td>{{ $ticket->subject }}</td>
                                            <td>{{ $ticket->location }}</td>
                                            <td>{{ $ticket->status }}</td>
                                            <td>{{ $ticket->sla_overdue->format('d-m-Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>No SLA overdue details available for the current month.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- SLA Overdue (Last Month) Table -->
        <div id="sla-overdue-last-month" class="table-section" style="display: none;">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title">SLA Overdue (Last Month) Details</h5>
                </div>
                <div class="card-body">
                    @if($slaOverdueLastMonthDetails->count())
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped dataTable" style="width:100%">
                                <thead>
                                    <tr class="table-primary">
                                        <th>Ticket ID</th>
                                        <th>Product</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>SLA Overdue Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($slaOverdueLastMonthDetails as $ticket)
                                        <tr>
                                            <td><a href="{{ route('ticket-management.details', $ticket->id) }}">{{ $ticket->ticket_number }}</a></td>
                                            <td>{{ $ticket->subject }}</td>
                                            <td>{{ $ticket->location }}</td>
                                            <td>{{ $ticket->status }}</td>
                                            <td>{{ $ticket->sla_overdue->format('d-m-Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>No SLA overdue details available for the last month.</p>
                    @endif
                </div>
            </div>
        </div>
        
    </section>
@endsection

@push('scripts')
    <script>
        function showSection(sectionId) {
            // Hide all detailed sections
            document.querySelectorAll('.table-section').forEach(section => {
                section.style.display = 'none';
            });
            // Display the selected section
            const sectionToShow = document.getElementById(sectionId);
            sectionToShow.style.display = 'block';

            // Initialize DataTable if not already initialized
            const table = sectionToShow.querySelector('.dataTable');
            if (!$.fn.DataTable.isDataTable(table)) {
                $(table).DataTable({
                    columnDefs: [{
                        targets: 'nosort',
                        orderable: false
                    }],
                    scrollX: true,
                    autoWidth: false
                });
            }
        }
    </script>
@endpush
