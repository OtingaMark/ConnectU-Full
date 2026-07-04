<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Handle edit.
     */
    public function edit()
    {
        return view('settings.edit');
    }

    /**
     * Handle update.
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
