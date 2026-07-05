<?php

namespace App\Http\Controllers;

use App\Models\PeerConnection;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PeerConnectionController extends Controller
{
    /**
     * Approve the incoming request and apply the related changes.
     */
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

    /**
     * Decline the incoming request and update its status.
     */
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
    /**
     * Send the request and persist its initial state.
     */
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