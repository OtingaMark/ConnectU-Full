<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        $profile = Auth::user()->profile;

        return view('profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'course' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'interests' => 'nullable|string',
            'skills' => 'nullable|string',
            'availability' => 'nullable|string|max:255',
        ]);

        Profile::updateOrCreate(
            ['user_id' => Auth::id()],
            $validated + ['user_id' => Auth::id()]
        );

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }
}