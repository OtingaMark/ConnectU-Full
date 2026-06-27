<?php

namespace App\Http\Controllers;

use App\Models\GroupInvitation;
use App\Models\GroupMember;

class GroupInvitationController extends Controller
{
    public function accept(GroupInvitation $invitation)
    {
        if ($invitation->receiver_id !== auth()->id()) {
            abort(403);
        }

        $invitation->update([
            'status' => 'accepted'
        ]);

        GroupMember::firstOrCreate([
            'user_id' => auth()->id(),
            'study_group_id' => $invitation->study_group_id,
        ], [
            'role' => 'member',
            'joined_at' => now(),
        ]);

        return back()->with(
            'success',
            'You joined the group successfully.'
        );
    }

    public function decline(GroupInvitation $invitation)
    {
        if ($invitation->receiver_id !== auth()->id()) {
            abort(403);
        }

        $invitation->update([
            'status' => 'declined'
        ]);

        return back()->with(
            'success',
            'Invitation declined.'
        );
    }
}