<?php

namespace App\Http\Controllers;

use App\Models\GroupMember;
use App\Models\StudyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudyGroupController extends Controller
{
    public function index()
    {
        $studyGroups = StudyGroup::with(['user', 'members'])
            ->latest()
            ->get();

        return view('study-groups.index', compact('studyGroups'));
    }

    public function create()
    {
        return view('study-groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_name' => 'required|string|min:3|max:255',
            'course' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'max_members' => 'required|integer|min:2|max:100',
            'meeting_schedule' => 'nullable|string|max:255',
        ]);

        $studyGroup = StudyGroup::create($validated + [
            'user_id' => Auth::id(),
        ]);

        GroupMember::create([
            'user_id' => Auth::id(),
            'study_group_id' => $studyGroup->id,
            'role' => 'creator',
            'joined_at' => now(),
        ]);

        return redirect()->route('study-groups.index')
            ->with('success', 'Study group created successfully.');
    }

    public function join(StudyGroup $studyGroup)
    {
        $alreadyJoined = GroupMember::where('user_id', Auth::id())
            ->where('study_group_id', $studyGroup->id)
            ->exists();

        if ($alreadyJoined) {
            return redirect()->route('study-groups.index')
                ->with('success', 'You are already a member of this study group.');
        }

        $currentMembers = GroupMember::where('study_group_id', $studyGroup->id)->count();

        if ($currentMembers >= $studyGroup->max_members) {
            return redirect()->route('study-groups.index')
                ->with('success', 'This study group is already full.');
        }

        GroupMember::create([
            'user_id' => Auth::id(),
            'study_group_id' => $studyGroup->id,
            'role' => 'member',
            'joined_at' => now(),
        ]);

        return redirect()->route('study-groups.index')
            ->with('success', 'You have joined the study group successfully.');
    }
}