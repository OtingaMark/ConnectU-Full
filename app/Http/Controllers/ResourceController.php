<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResourceController extends Controller
{
    public function index()
    {
        $resources = Resource::with('user')->latest()->get();

        return view('resources.index', compact('resources'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course' => 'required|string|max:255',
            'resource_file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,txt,jpg,jpeg,png|max:10240',
            'resource_link' => 'nullable|url',
        ]);

        $filePath = null;

        if ($request->hasFile('resource_file')) {
            $filePath = $request->file('resource_file')->store('resources', 'public');
        }

        Resource::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'course' => $validated['course'],
            'file_path' => $filePath,
            'resource_link' => $validated['resource_link'] ?? null,
        ]);

        return redirect()->route('resources.index')
            ->with('success', 'Resource uploaded successfully.');
    }
}