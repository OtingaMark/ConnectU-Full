<?php

namespace App\Http\Controllers;

use App\Models\StudyGroup;
use App\Models\Skill;
use App\Models\Resource;
use App\Models\Message;
use App\Models\PeerConnection;

class DashboardController extends Controller
{
    /**
     * Build the main user dashboard data set.
     *
     * This method gathers high-level platform stats and the current user's
     * pending connection requests, then passes everything to the dashboard view.
     *
     * Data prepared:
     * - Total number of study groups in the platform
     * - Total number of skills shared
     * - Total number of resources shared
     * - Total number of direct messages received by the current user
     * - Pending peer connection requests sent to the current user
     */
    public function index()
    {
        // Platform-wide counters displayed on dashboard statistic cards.
        $studyGroupsCount = StudyGroup::count();
        $skillsCount = Skill::count();
        $resourcesCount = Resource::count();

        // Incoming direct messages for the authenticated user.
        $messagesCount = Message::where('receiver_id', auth()->id())
            ->count();

        // Pending connection requests where active users requested to connect.
        $connectionRequests = PeerConnection::with('requester')
            ->where('receiver_id', auth()->id())
            ->where('status', 'pending')
            ->whereHas('requester', function ($query) {
                $query->active();
            })
            ->latest()
            ->get();

        // Render dashboard with all computed counters and request list.
        return view('dashboard', compact(
            'studyGroupsCount',
            'skillsCount',
            'resourcesCount',
            'messagesCount',
            'connectionRequests'
        ));
    }
}