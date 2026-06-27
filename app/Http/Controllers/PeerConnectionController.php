<?php

namespace App\Http\Controllers;

use App\Models\PeerConnection;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PeerConnectionController extends Controller
{
    public function accept(PeerConnection $connection)
    {
        $connection->update([
            'status' => 'accepted'
        ]);

        return back()->with(
            'success',
            'Connection accepted.'
        );
    }

    public function decline(PeerConnection $connection)
    {
        $connection->update([
            'status' => 'declined'
        ]);

        return back()->with(
            'success',
            'Connection declined.'
        );
    }
    public function send($userId)
{
    $targetUser = User::active()->findOrFail($userId);

    PeerConnection::firstOrCreate([
        'requester_id' => auth()->id(),
        'receiver_id' => $targetUser->id,
    ], [
        'status' => 'pending',
    ]);

    return back()->with('success', 'Connection request sent.');
}
}