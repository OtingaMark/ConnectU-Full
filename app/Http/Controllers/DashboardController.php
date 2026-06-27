<?php

namespace App\Http\Controllers;

use App\Models\StudyGroup;
use App\Models\Skill;
use App\Models\Resource;
use App\Models\Message;
use App\Models\PeerConnection;

class DashboardController extends Controller
{
    public function index()
    {
        $studyGroupsCount = StudyGroup::count();
        $skillsCount = Skill::count();
        $resourcesCount = Resource::count();

        $messagesCount = Message::where('receiver_id', auth()->id())
            ->count();

        $connectionRequests = PeerConnection::with('requester')
            ->where('receiver_id', auth()->id())
            ->where('status', 'pending')
            ->whereHas('requester', function ($query) {
                $query->active();
            })
            ->latest()
            ->get();

        return view('dashboard', compact(
            'studyGroupsCount',
            'skillsCount',
            'resourcesCount',
            'messagesCount',
            'connectionRequests'
        ));
    }
}