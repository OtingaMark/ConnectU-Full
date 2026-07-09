<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\GroupMember;
use App\Models\GroupMessage;
use App\Models\Message;
use App\Models\Report;
use App\Models\Skill;
use App\Models\StudyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * Validate input and persist a new record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reported_user_id' => 'nullable|exists:users,id',
            'study_group_id' => 'nullable|exists:study_groups,id',
            'group_message_id' => 'nullable|exists:group_messages,id',
            'direct_message_id' => 'nullable|exists:messages,id',
            'feedback_id' => 'nullable|exists:feedback,id',
            'skill_id' => 'nullable|exists:skills,id',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'evidence_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $targets = [
            $validated['reported_user_id'] ?? null,
            $validated['study_group_id'] ?? null,
            $validated['group_message_id'] ?? null,
            $validated['direct_message_id'] ?? null,
            $validated['feedback_id'] ?? null,
            $validated['skill_id'] ?? null,
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

        $reportedUserId = $validated['reported_user_id'] ?? null;

        if (!empty($validated['feedback_id'])) {
            $feedback = Feedback::with(['giver', 'receiver'])->findOrFail($validated['feedback_id']);

            $canAccess = (int) $feedback->receiver_id === (int) auth()->id()
                || (int) $feedback->giver_id === (int) auth()->id()
                || strtolower(trim((string) auth()->user()->role)) === 'admin';

            abort_unless($canAccess, 403);

            $reportedUserId = $feedback->giver_id;
        }

        if (!empty($validated['skill_id'])) {
            $skill = Skill::with('user')->findOrFail($validated['skill_id']);

            $canAccess = (int) $skill->user_id !== (int) auth()->id()
                || strtolower(trim((string) auth()->user()->role)) === 'admin';

            abort_unless($canAccess, 403);

            $reportedUserId = $skill->user_id;
        }

        $evidencePath = null;
        if ($request->hasFile('evidence_image')) {
            $evidencePath = $request->file('evidence_image')->store('report-evidence', 'public');
        }

        Report::create([
            'reporter_id' => auth()->id(),
            'reported_user_id' => $reportedUserId,
            'study_group_id' => $validated['study_group_id'] ?? null,
            'group_message_id' => $validated['group_message_id'] ?? null,
            'direct_message_id' => $validated['direct_message_id'] ?? null,
            'feedback_id' => $validated['feedback_id'] ?? null,
            'skill_id' => $validated['skill_id'] ?? null,
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'evidence_path' => $evidencePath,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Report submitted. Our moderation team will review it.');
    }
}
