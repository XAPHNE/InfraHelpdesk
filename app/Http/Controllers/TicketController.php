<?php

namespace App\Http\Controllers;

use App\Models\ActionTaken;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Ticket::with(['creator', 'closer']);

            // If the logged-in user is an employee, show only their tickets
            if (auth()->user()->isEmployee) {
                $query->where('created_by', auth()->user()->id);
            } elseif (auth()->user()->isVendor) {
                // If vendor_loc is set, filter tickets by location
                if (!is_null(auth()->user()->vendor_loc)) {
                    $query->where('location', auth()->user()->vendor_loc);
                }
            }

            $data = $query->latest()->get();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        if (auth()->user()->isAdmin) {
                            $btn = '<a href="javascript:void(0)" data-id="'.$row->id.'" class="edit btn btn-warning btn-sm editTicket"><i class="fas fa-edit"></i></a> ';
                            $btn .= '<a href="javascript:void(0)" data-id="'.$row->id.'" class="delete btn btn-danger btn-sm deleteTicket"><i class="fas fa-trash-alt"></i></a>';
                        } elseif (auth()->user()->isVendor) {
                            $btn = '<a href="javascript:void(0)" data-id="'.$row->id.'" class="edit btn btn-warning btn-sm editTicket"><i class="fas fa-edit"></i></a> ';
                        } else {
                            $btn = '';
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
                    ->editColumn('sla_overdue', function($row) {
                        return $row->sla_overdue ? : 'N/A';
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
    
        return response()->json(['message' => 'Ticket created successfully.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ticket = Ticket::with('actionTakens')->findOrFail($id);
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
        $request->validate([
            'location' => 'required|string',
            'subject' => 'required|string',
            'serial_num' => 'nullable|string',
            'description' => 'required|string',
            'call_type' => 'required|string',
            'status' => 'required|string',
        ]);
    
        $ticket = Ticket::findOrFail($id);
    
        $ticket->update([
            'location' => $request->location,
            'subject' => $request->subject,
            'serial_num' => $request->serial_num,
            'description' => $request->description,
            'call_type' => $request->call_type,
            'status' => $request->status,
            'closed_by' => $request->status === 'Closed' ? Auth::id() : $ticket->closed_by,
            'closed_at' => $request->status === 'Closed' ? now() : $ticket->closed_at,
        ]);

        // Automatically handle remarks for admin or vendor
        if (auth()->user()->isAdmin || auth()->user()->isVendor) {
            $ticket->update([
                'remarks' => $request->remarks,
            ]);
        }
    
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
