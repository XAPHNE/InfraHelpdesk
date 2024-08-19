<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::latest()->get();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn = '<a href="javascript:void(0)" data-id="'.$row->id.'" class="edit btn btn-warning btn-sm editUser"><i class="fas fa-edit"></i></a> ';
                        $btn .= '<a href="javascript:void(0)" data-id="'.$row->id.'" class="delete btn btn-danger btn-sm deleteUser"><i class="fas fa-trash-alt"></i></a>';
                        return $btn;
                    })
                    ->editColumn('vendor_loc', function($row) {
                        return $row->vendor_loc ? : 'N/A';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('user-management');
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'vendor_loc' => 'nullable|string',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'isAdmin' => $request->has('isAdmin') ? 1 : 0,
            'isVendor' => $request->has('isVendor') ? 1 : 0,
            'isEmployee' => $request->has('isEmployee') ? 1 : 0,
            'vendor_loc' => $request->vendor_loc,
        ]);

        return response()->json(['message' => 'User created successfully.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'vendor_loc' => 'nullable|string',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
            'isAdmin' => $request->has('isAdmin') ? 1 : 0,
            'isVendor' => $request->has('isVendor') ? 1 : 0,
            'isEmployee' => $request->has('isEmployee') ? 1 : 0,
            'vendor_loc' => $request->vendor_loc,
        ]);

        return response()->json(['message' => 'User updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::find($id)->delete();
        return response()->json(['message' => 'User deleted successfully.']);
    }
}
