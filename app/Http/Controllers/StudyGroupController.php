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
        $studyGroups = StudyGroup::with('user')->latest()->get();

        return view('study-groups.index', compact('studyGroups'));
    }

    public function create()
    {
        return view('study-groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_name' => 'required|string|max:255',
            'course' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_members' => 'required|integer|min:2',
            'meeting_schedule' => 'nullable|string|max:255',
        ]);

        StudyGroup::create($validated + [
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('study-groups.index')
            ->with('success', 'Study group created successfully.');
    }

    public function join(StudyGroup $studyGroup)
    {
        GroupMember::firstOrCreate([
            'user_id' => Auth::id(),
            'study_group_id' => $studyGroup->id,
        ], [
            'role' => 'member',
            'joined_at' => now(),
        ]);

        return redirect()->route('study-groups.index')
            ->with('success', 'You have joined the study group successfully.');
    }
}