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
                <a href="{{ url('ticket-management') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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
                <a href="{{ url('ticket-management') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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
                <a href="{{ url('ticket-management') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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
                <a href="{{ url('ticket-management') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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
                {{-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> --}}
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
                {{-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> --}}
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
                {{-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> --}}
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
                {{-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> --}}
            </div>
        </div>
        @endif
    </div>
@endsection
