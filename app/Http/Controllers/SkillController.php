<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkillController extends Controller
{
    public function index()
    {
        $skills = Skill::with('user')->latest()->get();

        return view('skills.index', compact('skills'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'skill_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'skill_level' => 'required|string|max:100',
            'availability' => 'nullable|string|max:255',
        ]);

        Skill::create([
            'user_id' => Auth::id(),
            'skill_name' => $validated['skill_name'],
            'description' => $validated['description'] ?? null,
            'skill_level' => $validated['skill_level'],
            'availability' => $validated['availability'] ?? null,
        ]);

        return redirect()->route('skills.index')
            ->with('success', 'Skill shared successfully.');
    }
}