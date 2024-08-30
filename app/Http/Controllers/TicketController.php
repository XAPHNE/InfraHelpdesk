<?php

namespace App\Http\Controllers;

use App\Mail\TicketCreated;
use App\Models\ActionTaken;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\DataTables;

class TicketController extends Controller
{
    public function dashboard()
    {
        $currentYear = Carbon::now()->year;
        $currentQuarter = Carbon::now()->quarter;
        $currentMonth = Carbon::now()->month;

        // Open tickets for the current quarter
        $openTicketsQuarter = Ticket::where('status', 'Open')
            ->whereYear('created_at', $currentYear)
            ->whereRaw('QUARTER(created_at) = ?', [$currentQuarter])
            ->count();

        // Closed tickets for the current quarter
        $closedTicketsQuarter = Ticket::where('status', 'Closed')
            ->whereYear('created_at', $currentYear)
            ->whereRaw('QUARTER(created_at) = ?', [$currentQuarter])
            ->count();

        // Open tickets for the current month
        $openTicketsMonth = Ticket::where('status', 'Open')
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();

        // Closed tickets for the current month
        $closedTicketsMonth = Ticket::where('status', 'Closed')
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();

        // SLA overdue tickets for the current year
        $slaOverdueYear = Ticket::where('sla_overdue', '<', now())
            ->whereYear('sla_overdue', $currentYear)
            ->count();

        // SLA overdue tickets for the current quarter
        $slaOverdueQuarter = Ticket::where('sla_overdue', '<', now())
            ->whereYear('sla_overdue', $currentYear)
            ->whereRaw('QUARTER(sla_overdue) = ?', [$currentQuarter])
            ->count();

        // SLA overdue tickets for the current month
        $slaOverdueMonth = Ticket::where('sla_overdue', '<', now())
            ->whereYear('sla_overdue', $currentYear)
            ->whereMonth('sla_overdue', $currentMonth)
            ->count();

        // SLA overdue tickets for the last month
        $lastMonth = Carbon::now()->subMonth()->month;
        $slaOverdueLastMonth = Ticket::where('sla_overdue', '<', now())
            ->whereYear('sla_overdue', $currentYear)
            ->whereMonth('sla_overdue', $lastMonth)
            ->count();

        return view('dashboard', compact(
            'openTicketsQuarter', 
            'closedTicketsQuarter', 
            'openTicketsMonth', 
            'closedTicketsMonth',
            'slaOverdueYear',
            'slaOverdueQuarter',
            'slaOverdueMonth',
            'slaOverdueLastMonth'
        ));
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Ticket::with(['creator', 'closer']);
            // Check if the logged-in user is an employee
            if (auth()->user()->isEmployee) {
                // Fetch tickets created by the logged-in employee
                $query->where('created_by', auth()->user()->id);
            } else {
                // Fetch all tickets for non-employee users (Admins or Vendors)
                if ($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
                    // Ensure the start and end dates include the full day
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate = Carbon::parse($request->end_date)->endOfDay();
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            }
            $data = $query->latest()->get();
    
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn = '';
                        if (auth()->user()->isAdmin) {
                            $btn .= '<a href="javascript:void(0)" data-id="'.$row->id.'" class="edit btn btn-warning btn-sm editTicket"><i class="fas fa-edit"></i></a> ';
                            $btn .= '<a href="javascript:void(0)" data-id="'.$row->id.'" class="delete btn btn-danger btn-sm deleteTicket"><i class="fas fa-trash-alt"></i></a>';
                        } elseif (auth()->user()->isVendor) {
                            $btn .= '<a href="javascript:void(0)" data-id="'.$row->id.'" class="edit btn btn-warning btn-sm editTicket"><i class="fas fa-edit"></i></a> ';
                        }
                        return $btn;
                    })
                    ->editColumn('created_by', function($row) {
                        return $row->creator ? $row->creator->name : 'N/A';
                    })
                    ->editColumn('closed_by', function($row) {
                        return $row->closer ? $row->closer->name : 'N/A';
                    })
                    ->editColumn('serial_num', function($row) {
                        return $row->serial_num ? : 'N/A';
                    })
                    ->editColumn('call_type', function($row) {
                        return $row->call_type ? : 'N/A';
                    })
                    ->editColumn('time_taken', function($row) {
                        return $row->time_taken_human ? : 'N/A';
                    })
                    ->editColumn('remarks', function($row) {
                        return $row->remarks ? : 'N/A';
                    })
                    ->editColumn('closed_at', function($row) {
                        return $row->closed_at ? : 'N/A';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    
        $users = User::all();
        return view('ticket-management', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'location' => 'required|string',
            'subject' => 'required|string',
            'serial_num' => 'nullable|string',
            'description' => 'required|string',
            'action_taken' => 'nullable|string',
        ]);
    
        // Generate the ticket number based on location
        $ticketNumber = $this->generateTicketNumber($request->location);
    
        $ticket = Ticket::create([
            'ticket_number' => $ticketNumber,
            'created_by' => Auth::id(),  // Automatically assign the logged-in user
            'location' => $request->location,
            'subject' => $request->subject,
            'serial_num' => $request->serial_num,
            'description' => $request->description,
            'sla_overdue' => now()->addDays(2),  // Automatically add 2 days to the current time 
            'status' => 'Open',  // Set status to "Open" by default
        ]);
        
        // If there is an action taken, store it
        if ($request->action_taken) {
            $ticket->actionTakens()->create([
                'action_taken' => $request->action_taken,
            ]);
        }

        // Send email to the ticket creator
        // Mail::to(Auth::user()->email)->send(new TicketCreated($ticket));

        // Send email to the vendor with the same location
        // if ($ticket->location) {
        //     $vendor = User::where('vendor_loc', $ticket->location)->first();
        //     if ($vendor) {
        //         Mail::to($vendor->email)->send(new TicketCreated($ticket));
        //     }
        // }

        // Send email to hw-support@apgcl.org
        // Mail::to('support.hardware@apgcl.org')->send(new TicketCreated($ticket));
    
        return response()->json(['message' => 'Ticket created successfully.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ticket = Ticket::find($id);
        return view('ticket-details', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $ticket = Ticket::with('actionTakens')->findOrFail($id);
        return response()->json($ticket);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the ticket
        $ticket = Ticket::findOrFail($id);

        // Create an array to hold the fields that should be updated
        $updateFields = [];

        // Conditionally add fields to the update array if they exist in the request
        if ($request->has('mark_as_complete') && auth()->user()->isEmployee) {
            $updateFields['isClosedByEmployee'] = true;
            $updateFields['closedByEmployee_at'] = now();
        }

        if ($request->has('location')) {
            $updateFields['location'] = $request->location;
        }

        if ($request->has('subject')) {
            $updateFields['subject'] = $request->subject;
        }

        if ($request->has('serial_num')) {
            $updateFields['serial_num'] = $request->serial_num;
        }

        if ($request->has('description')) {
            $updateFields['description'] = $request->description;
        }

        if ($request->has('call_type')) {
            $updateFields['call_type'] = $request->call_type;
        }

        if ($request->has('status')) {
            $updateFields['status'] = $request->status;
    
            if ($request->status === 'Closed') {
                $updateFields['closed_by'] = Auth::id();
                $updateFields['closed_at'] = now();
    
                // Calculate the time_taken and store it in hours (or any other unit you prefer)
                $timeTaken = $ticket->created_at->diffInMinutes(now());
                $updateFields['time_taken'] = $timeTaken;
            }
        }

        // Conditionally update remarks only if the user is Admin or Vendor
        if (auth()->user()->isAdmin || auth()->user()->isVendor) {
            if ($request->has('remarks')) {
                $updateFields['remarks'] = $request->remarks;
            }
        }

        // Update the ticket with the fields that were present in the request
        $ticket->update($updateFields);

        return response()->json(['message' => 'Ticket updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Ticket::find($id)->delete();
        return response()->json(['message' => 'Ticket deleted successfully.']);
    }

    private function generateTicketNumber($location)
    {
        $locationCode = strtoupper($location);  // Convert location to uppercase
        $lastTicket = Ticket::where('location', $location)->latest()->first();
        $nextNumber = $lastTicket ? (int)substr($lastTicket->ticket_number, -7) + 1 : 1;
        $nextNumber = str_pad($nextNumber, 7, '0', STR_PAD_LEFT);

        return "APGCL/IIPL/{$locationCode}/{$nextNumber}";
    }

    public function storeActionTaken(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'action_taken' => 'required|string|max:255',
        ]);

        ActionTaken::create([
            'ticket_id' => $request->ticket_id,
            'action_taken' => $request->action_taken,
        ]);

        return response()->json(['message' => 'Action taken added successfully.']);
    }


}
