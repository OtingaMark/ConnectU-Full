<?php

namespace App\Http\Controllers;

use App\Models\SuspensionAppeal;
use App\Models\User;
use Illuminate\Http\Request;

class SuspensionAppealController extends Controller
{
    /**
     * Show the form used to create a new record.
     */
    public function create(Request $request)
    {
        $userId = (int) $request->query('user');
        $user = User::findOrFail($userId);

        if (strtolower(trim($user->status ?? 'active')) !== 'suspended') {
            return redirect('/login')->with('error', 'Appeal is only available for suspended accounts.');
        }

        return view('auth.appeal-suspension', [
            'user' => $user,
        ]);
    }

    /**
     * Validate input and persist a new record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:2000',
        ]);

        $user = User::findOrFail($validated['user_id']);

        if (strtolower(trim($user->status ?? 'active')) !== 'suspended') {
            return redirect('/login')->with('error', 'This account is not currently suspended.');
        }

        $hasPending = SuspensionAppeal::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return redirect('/login')->with('status', 'An appeal is already pending review.');
        }

        SuspensionAppeal::create([
            'user_id' => $user->id,
            'reason' => $validated['reason'],
            'message' => $validated['message'],
            'status' => 'pending',
        ]);

        return redirect('/login')->with('status', 'Appeal submitted successfully. We will review it soon.');
    }
}
