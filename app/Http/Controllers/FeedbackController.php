<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();

        $feedbackGiven = Feedback::with('receiver')
            ->where('giver_id', Auth::id())
            ->latest()
            ->get();

        $feedbackReceived = Feedback::with('giver')
            ->where('receiver_id', Auth::id())
            ->latest()
            ->get();

        return view('feedback.index', compact('users', 'feedbackGiven', 'feedbackReceived'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        Feedback::create([
            'giver_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return redirect()->route('feedback.index')
            ->with('success', 'Feedback submitted successfully.');
    }
}