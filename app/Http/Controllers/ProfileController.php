<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use App\Models\PeerConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show the form used to edit an existing record.
     */
    public function edit()
    {
        $profile = Auth::user()->profile;

        return view('profile.edit', compact('profile'));
    }

    /**
     * Validate input and persist updates to an existing record.
     */
    public function update(Request $request)
    {
        $profile = Auth::user()->profile;

        $validated = $request->validate([
            'course' => 'required|string|min:3|max:255',
            'bio' => 'nullable|string|max:1000',
            'interests' => 'nullable|string|max:500',
            'skills' => 'nullable|string|max:500',
            'availability' => 'nullable|string|max:255',
                'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
    ]);

        if ($request->hasFile('profile_picture')) {
            if ($profile && $profile->profile_picture) {
                Storage::disk('public')->delete($profile->profile_picture);
            }

            $validated['profile_picture'] = $request->file('profile_picture')
                ->store('profile-pictures', 'public');
        }

        Profile::updateOrCreate(
            ['user_id' => Auth::id()],
            $validated + ['user_id' => Auth::id()]
        );

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    /**
     * Display detailed information for a specific record.
     */
    public function show(User $user)
    {
        $isAdminViewer = strtolower(trim(auth()->user()->role ?? '')) === 'admin';

        if ($user->isSuspended() && !$isAdminViewer) {
            abort(404);
        }

        $user->load('profile');

        $connection = PeerConnection::where(function ($query) use ($user) {
            $query->where('requester_id', auth()->id())
                ->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('requester_id', $user->id)
                ->where('receiver_id', auth()->id());
        })->first();

        return view('profiles.show', compact('user', 'connection'));
    }
}