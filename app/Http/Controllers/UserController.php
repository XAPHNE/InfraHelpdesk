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
                        $btn = '<a href="javascript:void(0)" data-id="'.$row->id.'" class="edit btn btn-primary btn-sm editUser">Edit</a>';
                        $btn .= '<a href="javascript:void(0)" data-id="'.$row->id.'" class="delete btn btn-danger btn-sm deleteUser">Delete</a>';
                        return $btn;
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
        $userId = $request->userId;

        $user = User::updateOrCreate(
            ['id' => $userId],
            [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password ? bcrypt($request->password) : User::find($userId)->password,
                'isAdmin' => $request->has('isAdmin') ? 1 : 0,
                'isVendor' => $request->has('isVendor') ? 1 : 0,
                'isEmployee' => $request->has('isEmployee') ? 1 : 0,
            ]
        );

        return response()->json(['message' => 'User saved successfully.']);
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
        //
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
