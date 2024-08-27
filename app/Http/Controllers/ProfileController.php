<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index()
    {
        // Get the authenticated user
        $user = auth()->user();

        // Return the view with the user's details
        return view('profile', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        // Validate the input
        $request->validate([
            'name' => 'nullable|string|max:255',
            'current_password' => 'required_with:password|string|min:8', // Require current password if new password is provided
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = $request->user();

        // Check if the current password is correct before allowing a password change
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->withErrors(['current_password' => 'The current password is incorrect.']);
            }

            // Update the password
            $user->password = Hash::make($request->password);
            $user->must_change_passwd = false;  // Reset the flag
        }

        // Update the user's name if provided
        if ($request->filled('name')) {
            $user->name = $request->name;
        }

        // Save the changes
        $user->save();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
