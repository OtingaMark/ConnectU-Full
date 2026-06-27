<?php

namespace App\Http\Controllers;

use App\Models\GroupMember;
use App\Models\GroupMessage;
use App\Models\Message;
use App\Models\Report;
use App\Models\StudyGroup;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reported_user_id' => 'nullable|exists:users,id',
            'study_group_id' => 'nullable|exists:study_groups,id',
            'group_message_id' => 'nullable|exists:group_messages,id',
            'direct_message_id' => 'nullable|exists:messages,id',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $targets = [
            $validated['reported_user_id'] ?? null,
            $validated['study_group_id'] ?? null,
            $validated['group_message_id'] ?? null,
            $validated['direct_message_id'] ?? null,
        ];

        $targetCount = collect($targets)->filter(fn ($value) => !is_null($value))->count();

        if ($targetCount !== 1) {
            return back()->with('error', 'Please report exactly one target at a time.');
        }

        if (!empty($validated['reported_user_id']) && (int) $validated['reported_user_id'] === (int) auth()->id()) {
            return back()->with('error', 'You cannot report your own account.');
        }

        if (!empty($validated['direct_message_id'])) {
            $message = Message::findOrFail($validated['direct_message_id']);

            $canAccess = $message->sender_id === auth()->id() || $message->receiver_id === auth()->id();

            abort_unless($canAccess, 403);
        }

        if (!empty($validated['group_message_id'])) {
            $groupMessage = GroupMessage::findOrFail($validated['group_message_id']);

            $isMember = GroupMember::where('study_group_id', $groupMessage->study_group_id)
                ->where('user_id', auth()->id())
                ->exists();

            abort_unless($isMember, 403);
        }

        if (!empty($validated['study_group_id'])) {
            $studyGroup = StudyGroup::findOrFail($validated['study_group_id']);

            $isVisible = $studyGroup->visibility === 'public'
                || (int) $studyGroup->user_id === (int) auth()->id()
                || GroupMember::where('study_group_id', $studyGroup->id)
                    ->where('user_id', auth()->id())
                    ->exists();

            abort_unless($isVisible, 403);
        }

        Report::create([
            'reporter_id' => auth()->id(),
            'reported_user_id' => $validated['reported_user_id'] ?? null,
            'study_group_id' => $validated['study_group_id'] ?? null,
            'group_message_id' => $validated['group_message_id'] ?? null,
            'direct_message_id' => $validated['direct_message_id'] ?? null,
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Report submitted. Our moderation team will review it.');
    }
}
