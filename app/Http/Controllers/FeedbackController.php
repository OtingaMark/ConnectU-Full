<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FeedbackController extends Controller
{
    /**
     * Display feedback forms and history for the current user.
     */
    public function index()
    {
        $users = User::active()
            ->where('role', 'student')
            ->where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get();

        $feedbackGiven = Feedback::with('receiver')
            ->where('giver_id', Auth::id())
            ->where('feedback_type', 'peer')
            ->latest()
            ->get();

        $feedbackReceived = Feedback::with('giver')
            ->where('receiver_id', Auth::id())
            ->where('feedback_type', 'peer')
            ->whereHas('giver', fn ($query) => $query->where('status', 'active'))
            ->latest()
            ->get();

        $appFeedbackGiven = Feedback::with('giver')
            ->where('giver_id', Auth::id())
            ->where('feedback_type', 'app')
            ->latest()
            ->get();

        return view('feedback.index', compact(
            'users',
            'feedbackGiven',
            'feedbackReceived',
            'appFeedbackGiven'
        ));
    }

    /**
     * Validate and store peer feedback or app feedback submissions.
     */
    public function store(Request $request)
    {
        $feedbackType = $request->input('feedback_type', 'peer');

        // If no peer receiver is provided, treat submission as app feedback.
        // This prevents frontend mode-toggle mismatches from forcing peer validation.
        if ($feedbackType !== 'app' && !$request->filled('receiver_id')) {
            $feedbackType = 'app';
        }

        if ($feedbackType === 'app') {
            $validated = $request->validate([
                'feedback_type' => ['required', Rule::in(['peer', 'app'])],
                'rating' => 'nullable|integer|min:1|max:5',
                'comment' => 'nullable|string|max:500',
            ]);

            $rating = array_key_exists('rating', $validated) ? $validated['rating'] : null;

            $adminReceiver = User::query()
                ->where('role', 'admin')
                ->where('status', 'active')
                ->orderBy('id')
                ->first();

            // Fallback to current user to avoid blocking app feedback when no admin is active.
            $receiverId = $adminReceiver?->id ?? Auth::id();

            Feedback::create([
                'giver_id' => Auth::id(),
                'receiver_id' => $receiverId,
                'feedback_type' => 'app',
                'rating' => $rating,
                'comment' => $validated['comment'] ?? null,
            ]);

            return redirect()->route('feedback.index')
                ->with('success', 'App feedback submitted successfully.');
        }

        $validated = $request->validate([
            'feedback_type' => ['required', Rule::in(['peer', 'app'])],
            'receiver_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('status', 'active')
                        ->where('role', 'student');
                }),
                Rule::notIn([Auth::id()])
            ],
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $alreadyRated = Feedback::where('giver_id', Auth::id())
            ->where('receiver_id', $validated['receiver_id'])
            ->where('feedback_type', 'peer')
            ->exists();

        if ($alreadyRated) {
            return redirect()->route('feedback.index')
                ->with('success', 'You have already submitted feedback for this user.');
        }

        Feedback::create([
            'giver_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'feedback_type' => 'peer',
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return redirect()->route('feedback.index')
            ->with('success', 'Feedback submitted successfully.');
    }
}