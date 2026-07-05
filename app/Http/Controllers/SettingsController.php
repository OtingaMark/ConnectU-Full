<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Show the form used to edit an existing record.
     */
    public function edit()
    {
        return view('settings.edit');
    }

    /**
     * Validate input and persist updates to an existing record.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'theme_mode' => 'required|in:light,dark,system',
            'accent_color' => 'required|in:blue,purple,green,pink',
        ]);

        auth()->user()->update($validated);

        return back()->with('success', 'Settings updated successfully.');
    }
}
