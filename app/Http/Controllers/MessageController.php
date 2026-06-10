<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();

        $receivedMessages = Message::with('sender')
            ->where('receiver_id', Auth::id())
            ->latest()
            ->get();

        $sentMessages = Message::with('receiver')
            ->where('sender_id', Auth::id())
            ->latest()
            ->get();

        return view('messages.index', compact('users', 'receivedMessages', 'sentMessages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        return redirect()->route('messages.index')
            ->with('success', 'Message sent successfully.');
    }
}