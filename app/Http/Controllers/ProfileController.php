<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $user->load(['studentProfile.department', 'facultyProfile.department']);
        
        return view('profile.show', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        $user->load(['studentProfile.department', 'facultyProfile.department']);
        
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        $userData = $request->only(['first_name', 'last_name', 'email', 'phone', 'address']);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $userData['profile_picture'] = $request->file('profile_picture')->store('avatars', 'public');
        }

        $user->update($userData);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function settings()
    {
        return view('profile.settings');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->withErrors(['password' => 'Password is incorrect.']);
        }

        $user = Auth::user();
        Auth::logout();
        
        $user->update(['is_active' => false]);

        return redirect('/')->with('success', 'Account deactivated successfully.');
    }
}
