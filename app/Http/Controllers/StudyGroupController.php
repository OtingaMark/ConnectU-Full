<?php

namespace App\Http\Controllers;

use App\Models\GroupMember;
use App\Models\GroupInvitation;
use App\Models\GroupJoinRequest;
use App\Models\GroupMessage;
use App\Models\GroupSuspensionAppeal;
use App\Models\PeerConnection;
use App\Models\StudyGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class StudyGroupController extends Controller
{
    /**
     * List groups visible to the current user and rank them by relevance.
     */
    public function index()
    {
        $myProfile = auth()->user()->profile;

        // Users can discover active groups, while suspended groups are only visible to creator/admin managers.
        $studyGroups = StudyGroup::with(['user', 'members', 'invitations'])
            ->where(function ($query) {
                $query->where('status', 'active')
                    ->orWhere(function ($suspendedQuery) {
                        $suspendedQuery->where('status', 'suspended')
                            ->where(function ($managerQuery) {
                                $managerQuery->where('user_id', Auth::id())
                                    ->orWhereHas('members', function ($memberQuery) {
                                        $memberQuery->where('user_id', Auth::id())
                                            ->whereRaw('LOWER(TRIM(role)) IN (?, ?)', ['creator', 'admin']);
                                    });
                            });
                    });
            })
            ->where(function ($query) {
                $query->where('visibility', 'public')
                    ->orWhere('user_id', Auth::id())
                    ->orWhereHas('members', function ($q) {
                        $q->where('user_id', Auth::id());
                    })
                    ->orWhereHas('invitations', function ($q) {
                        $q->where('receiver_id', Auth::id())
                            ->where('status', 'pending');
                    });
            })
            ->latest()
            ->get();

        foreach ($studyGroups as $group) {
            $score = 0;

            if ($myProfile) {

                $myCourse = strtolower(trim($myProfile->course ?? ''));
                $groupCourse = strtolower(trim($group->course ?? ''));

                if (
                    $myCourse &&
                    $groupCourse &&
                    (
                        str_contains($groupCourse, $myCourse) ||
                        str_contains($myCourse, $groupCourse) ||
                        (str_contains($groupCourse, 'informatics') && str_contains($myCourse, 'informatics'))
                    )
                ) {
                    // Course similarity gets the highest weight in the relevance score.
                    $score += 50;
                }

                $interests = strtolower(
                    ($myProfile->interests ?? '') . ' ' .
                    ($myProfile->skills ?? '')
                );

                $groupText = strtolower(
                    ($group->group_name ?? '') . ' ' .
                    ($group->description ?? '')
                );

                $interestWords = preg_split('/[\s,]+/', $interests);

                foreach ($interestWords as $word) {
                    $word = rtrim($word, 's');

                    // Small keyword overlap boosts ranking; long words reduce noisy matches.
                    if (strlen($word) > 2 && str_contains($groupText, $word)) {
                        $score += 10;
                    }
                }
            }

            $group->match_score = min($score, 100);

            $group->is_joined = GroupMember::where('user_id', Auth::id())
                ->where('study_group_id', $group->id)
                ->exists();

            $group->my_invitation = GroupInvitation::where('receiver_id', Auth::id())
                ->where('study_group_id', $group->id)
                ->where('status', 'pending')
                ->first();

            // Keep "joined" and "invited" groups above generic discoverable groups.
            if ($group->is_joined) {
                $group->sort_order = 1;
            } elseif ($group->my_invitation) {
                $group->sort_order = 2;
            } else {
                $group->sort_order = 3;
            }
        }

        $studyGroups = $studyGroups
            ->sortBy([
                ['sort_order', 'asc'],
                ['match_score', 'desc'],
            ])
            ->values();

        return view('study-groups.index', compact('studyGroups'));
    }

    /**
     * Show group details, access checks, and eligible users for invitations.
     */
    public function show(StudyGroup $studyGroup)
    {
        $studyGroup->load(['user', 'members.user.profile', 'invitations.receiver', 'invitations.sender', 'messages.user']);
        $isPlatformAdmin = strtolower(trim((string) (Auth::user()->role ?? ''))) === 'admin';

        $myMembership = GroupMember::where('study_group_id', $studyGroup->id)
            ->where('user_id', Auth::id())
            ->first();

        $myRole = strtolower(trim((string) ($myMembership->role ?? '')));

        $canManageGroup = (int) $studyGroup->user_id === (int) Auth::id()
            || in_array($myRole, ['creator', 'admin'], true);

        // Suspended groups are visible only to managers so they can resolve moderation/admin actions.
        if ($studyGroup->isSuspended() && !$canManageGroup && !$isPlatformAdmin) {
            abort(403);
        }

        $isMember = GroupMember::where('study_group_id', $studyGroup->id)
            ->where('user_id', Auth::id())
            ->exists();

        $hasActiveInvitation = GroupInvitation::where('study_group_id', $studyGroup->id)
            ->where('receiver_id', Auth::id())
            ->where('status', 'pending')
            ->exists();

        if (
            $studyGroup->visibility === 'private' &&
            $studyGroup->user_id !== Auth::id() &&
            !$isMember &&
            !$hasActiveInvitation &&
            !$isPlatformAdmin
        ) {
            // Private groups require ownership, membership, or a pending invitation.
            abort(403);
        }

        $memberIds = GroupMember::where('study_group_id', $studyGroup->id)
            ->pluck('user_id')
            ->toArray();

        $invitedIds = GroupInvitation::where('study_group_id', $studyGroup->id)
            ->where('status', 'pending')
            ->pluck('receiver_id')
            ->toArray();

        $canInviteMembers = $canManageGroup || (
            $myMembership && $studyGroup->members_can_invite
        );

        // Invite options are restricted to accepted peer connections, excluding current members/pending invites.
        $connectedUserIds = PeerConnection::where('status', 'accepted')
            ->where(function ($query) {
                $query->where('requester_id', Auth::id())
                    ->orWhere('receiver_id', Auth::id());
            })
            ->get()
            ->map(function ($connection) {
                return $connection->requester_id == Auth::id()
                    ? $connection->receiver_id
                    : $connection->requester_id;
            });

        $inviteUsers = User::whereIn('id', $connectedUserIds)
            ->active()
            ->whereNotIn('id', $memberIds)
            ->whereNotIn('id', $invitedIds)
            ->orderBy('name')
            ->get();

        return view('study-groups.show', compact('studyGroup', 'inviteUsers', 'canInviteMembers'));
    }

    /**
     * Show the create form for a new study group.
     */
    public function create()
    {
        return view('study-groups.create');
    }

    /**
     * Persist a new study group and add the creator as the first member.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_name' => 'required|string|min:3|max:255',
            'course' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'max_members' => 'required|integer|min:2|max:100',
            'meeting_schedule' => 'nullable|string|max:255',
            'visibility' => 'required|in:public,private',
            'requires_approval' => 'nullable|boolean',
            'members_can_invite' => 'nullable|boolean',
            'group_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $validated['requires_approval'] = $request->has('requires_approval');
        $validated['members_can_invite'] = $request->has('members_can_invite');

        if ($request->hasFile('group_picture')) {
            $validated['group_picture'] = $request->file('group_picture')
                ->store('group-pictures', 'public');
        }

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
            ->with('success', 'Learning group created successfully.');
    }

    /**
     * Join a study group directly or submit a pending join request.
     */
    public function join(StudyGroup $studyGroup)
    {
        if ($studyGroup->isSuspended()) {
            return redirect()->route('study-groups.index')
                ->with('error', 'This learning group is currently suspended.');
        }

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
                ->with('error', 'This learning group is already full.');
        }

        if ($studyGroup->requires_approval) {
            GroupJoinRequest::updateOrCreate(
                [
                    'study_group_id' => $studyGroup->id,
                    'user_id' => Auth::id(),
                ],
                [
                    'status' => 'pending',
                ]
            );

            return redirect()->route('study-groups.index')
                ->with('success', 'Your request to join this group has been sent.');
        }

        GroupMember::create([
            'user_id' => Auth::id(),
            'study_group_id' => $studyGroup->id,
            'role' => 'member',
            'joined_at' => now(),
        ]);

        return redirect()->route('study-groups.index')
            ->with('success', 'You have joined the learning group successfully.');
    }

    /**
     * Send an invitation to a connected active user for this group.
     */
    public function invite(Request $request, StudyGroup $studyGroup)
    {
        if ($studyGroup->isSuspended()) {
            return back()->with('error', 'Invitations are disabled while this group is suspended.');
        }

        $myMembership = GroupMember::where('study_group_id', $studyGroup->id)
            ->where('user_id', Auth::id())
            ->first();

        $canManageGroup = $myMembership && in_array($myMembership->role, ['creator', 'admin']);

        $canInviteMembers = $canManageGroup || (
            $myMembership && $studyGroup->members_can_invite
        );

        if (!$canInviteMembers) {
            abort(403);
        }

        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);

        // Suspended/inactive accounts cannot receive new invitations.
        $receiver = User::query()->active()->find($validated['receiver_id']);
        if (!$receiver) {
            return back()->with('error', 'You can only invite active users.');
        }

        $alreadyMember = GroupMember::where('study_group_id', $studyGroup->id)
            ->where('user_id', $validated['receiver_id'])
            ->exists();

        if ($alreadyMember) {
            return back()->with('success', 'This user is already a member of the group.');
        }

        // firstOrCreate prevents duplicate pending invitations for the same receiver/group pair.
        GroupInvitation::firstOrCreate([
            'study_group_id' => $studyGroup->id,
            'receiver_id' => $validated['receiver_id'],
        ], [
            'sender_id' => Auth::id(),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Group invitation sent successfully.');
    }

    /**
     * Post a text/file/link message to the group chat.
     */
    public function sendMessage(Request $request, StudyGroup $studyGroup)
    {
        if ($studyGroup->isSuspended()) {
            return back()->with('error', 'Messaging is disabled while this group is suspended.');
        }

        $isMember = GroupMember::where('study_group_id', $studyGroup->id)
            ->where('user_id', Auth::id())
            ->exists();

        if (!$isMember) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => 'nullable|string|max:1000',
            'message_file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,txt,jpg,jpeg,png|max:20480',
            'resource_link' => 'nullable|url|max:500',
        ]);

        if (
            empty($validated['message']) &&
            !$request->hasFile('message_file') &&
            empty($validated['resource_link'])
        ) {
            // Keep group chat payload meaningful: at least one content channel is required.
            return back()->with('error', 'Please type a message, attach a file, or add a link.');
        }

        $filePath = null;
        $messageType = 'text';

        if ($request->hasFile('message_file')) {
            $filePath = $request->file('message_file')->store('group-messages', 'public');
            $messageType = 'file';
        }

        if (!empty($validated['resource_link'])) {
            // Distinguish pure link messages from mixed file+link payloads.
            $messageType = $filePath ? 'mixed' : 'link';
        }

        GroupMessage::create([
            'study_group_id' => $studyGroup->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'] ?? '',
            'file_path' => $filePath,
            'resource_link' => $validated['resource_link'] ?? null,
            'message_type' => $messageType,
        ]);

        return back()->with('success', 'Message sent to group.');
    }

    /**
     * Serve a stored group message attachment to authorized members.
     */
    public function groupAttachment(GroupMessage $message)
    {
        $isMember = GroupMember::where('study_group_id', $message->study_group_id)
            ->where('user_id', Auth::id())
            ->exists();

        if (!$isMember) {
            abort(403);
        }

        if (!$message->file_path || !Storage::disk('public')->exists($message->file_path)) {
            abort(404);
        }

        return response()->file(storage_path('app/public/' . $message->file_path));
    }

    /**
     * Remove current user from group membership with role safety checks.
     */
    public function leave(StudyGroup $studyGroup)
    {
        $membership = GroupMember::where('study_group_id', $studyGroup->id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$membership) {
            return redirect()->route('study-groups.index')
                ->with('error', 'You are not a member of this group.');
        }

        if ($membership->role === 'creator') {
            $otherAdmins = GroupMember::where('study_group_id', $studyGroup->id)
                ->where('user_id', '!=', Auth::id())
                ->whereIn('role', ['creator', 'admin'])
                ->count();

            // Prevent creator from leaving if that would orphan admin control.
            if ($otherAdmins === 0) {
                return back()->with('error', 'You are the only admin of this group. Promote another member before leaving.');
            }
        }

        $membership->delete();

        GroupMessage::create([
            'study_group_id' => $studyGroup->id,
            'user_id' => Auth::id(),
            'message' => auth()->user()->name . ' left the group.',
            'message_type' => 'system',
        ]);

        return redirect()->route('study-groups.index')
            ->with('success', 'You left the group successfully.');
    }

    /**
     * Return the current user's role in the given group.
     */
    private function getMyGroupRole(StudyGroup $studyGroup): ?string
    {
        return GroupMember::where('study_group_id', $studyGroup->id)
            ->where('user_id', Auth::id())
            ->value('role');
    }

    /**
     * Determine whether the current user can manage group members/settings.
     */
    private function userCanManageGroup(StudyGroup $studyGroup): bool
    {
        return in_array($this->getMyGroupRole($studyGroup), ['creator', 'admin']);
    }

    /**
     * Count creator/admin members to enforce admin safety rules.
     */
    private function adminCount(StudyGroup $studyGroup): int
    {
        return GroupMember::where('study_group_id', $studyGroup->id)
            ->whereIn('role', ['creator', 'admin'])
            ->count();
    }

    /**
     * Promote a regular member to admin.
     */
    public function promoteMember(StudyGroup $studyGroup, GroupMember $member)
    {
        $myRole = $this->getMyGroupRole($studyGroup);

        if (!$this->userCanManageGroup($studyGroup)) {
            abort(403);
        }

        if ($member->study_group_id !== $studyGroup->id) {
            abort(404);
        }

        if ($member->role !== 'member') {
            return back()->with('error', 'Only normal members can be promoted.');
        }

        $member->update(['role' => 'admin']);

        return back()->with('success', 'Member promoted to admin successfully.');
    }

    /**
     * Demote an admin to member (creator-only action).
     */
    public function demoteMember(StudyGroup $studyGroup, GroupMember $member)
    {
        $myRole = $this->getMyGroupRole($studyGroup);

        if ($myRole !== 'creator') {
            return back()->with('error', 'Only the group creator can demote admins.');
        }

        if ($member->study_group_id !== $studyGroup->id) {
            abort(404);
        }

        if ($member->role === 'creator') {
            return back()->with('error', 'You cannot demote the group creator.');
        }

        if ($member->role !== 'admin') {
            return back()->with('error', 'Only admins can be demoted.');
        }

        if ($this->adminCount($studyGroup) <= 1) {
            return back()->with('error', 'You cannot demote the last admin.');
        }

        $member->update(['role' => 'member']);

        return back()->with('success', 'Admin demoted to member successfully.');
    }

    /**
     * Remove a member from the group with role and safety guards.
     */
    public function removeMember(StudyGroup $studyGroup, GroupMember $member)
    {
        $myRole = $this->getMyGroupRole($studyGroup);

        if (!$this->userCanManageGroup($studyGroup)) {
            abort(403);
        }

        if ($member->study_group_id !== $studyGroup->id) {
            abort(404);
        }

        if ($member->role === 'creator') {
            return back()->with('error', 'You cannot remove the group creator.');
        }

        if ($member->user_id === Auth::id()) {
            return back()->with('error', 'Use the Leave Group button to remove yourself.');
        }

        if ($myRole === 'admin' && $member->role === 'admin') {
            // Creator can remove admins, but admin-to-admin removal is blocked.
            return back()->with('error', 'Admins cannot remove other admins.');
        }

        if (in_array($member->role, ['creator', 'admin']) && $this->adminCount($studyGroup) <= 1) {
            return back()->with('error', 'You cannot remove the last admin.');
        }

        $member->delete();

        GroupMessage::create([
            'study_group_id' => $studyGroup->id,
            'user_id' => Auth::id(),
            'message' => $member->user->name . ' was removed from the group.',
            'message_type' => 'system',
        ]);

        return back()->with('success', 'Member removed successfully.');
    }

    /**
     * Show the group edit form to creator/admin users.
     */
    public function edit(StudyGroup $studyGroup)
    {
        $membership = GroupMember::where('study_group_id', $studyGroup->id)
            ->where('user_id', Auth::id())
            ->whereIn('role', ['creator', 'admin'])
            ->first();

        if (!$membership) {
            abort(403);
        }

        return view('study-groups.edit', compact('studyGroup'));
    }

    /**
     * Update group settings and enforce member-cap constraints.
     */
    public function update(Request $request, StudyGroup $studyGroup)
    {
        $membership = GroupMember::where('study_group_id', $studyGroup->id)
            ->where('user_id', Auth::id())
            ->whereIn('role', ['creator', 'admin'])
            ->first();

        if (!$membership) {
            abort(403);
        }

        $validated = $request->validate([
            'group_name' => 'required|string|min:3|max:255',
            'course' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'max_members' => 'required|integer|min:2|max:100',
            'meeting_schedule' => 'nullable|string|max:255',
            'visibility' => 'required|in:public,private',
            'requires_approval' => 'nullable|boolean',
            'members_can_invite' => 'nullable|boolean',
            'group_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $validated['requires_approval'] = $request->has('requires_approval');
        $validated['members_can_invite'] = $request->has('members_can_invite');

        if ($request->hasFile('group_picture')) {
            $validated['group_picture'] = $request->file('group_picture')
                ->store('group-pictures', 'public');
        }

        $currentMembers = GroupMember::where('study_group_id', $studyGroup->id)->count();

        if ($validated['max_members'] < $currentMembers) {
            return back()
                ->withInput()
                ->with('error', 'Maximum members cannot be less than current group members.');
        }

        $studyGroup->update($validated);

        return redirect()
            ->route('study-groups.show', $studyGroup->id)
            ->with('success', 'Group settings updated successfully.');
    }

    /**
     * Delete a study group owned by the current user.
     */
    public function destroy(StudyGroup $studyGroup)
    {
        abort_unless((int) $studyGroup->user_id === (int) Auth::id(), 403);

        // Clean up uploaded media before deleting the group record.
        if (!empty($studyGroup->group_picture)) {
            Storage::disk('public')->delete($studyGroup->group_picture);
        }

        $studyGroup->delete();

        return redirect()->route('study-groups.index')
            ->with('success', 'Learning group deleted successfully.');
    }

    /**
     * Submit a pending join request for groups requiring approval.
     */
    public function requestToJoin(StudyGroup $studyGroup)
    {
        if ($studyGroup->isSuspended()) {
            return back()->with('error', 'This study group is currently suspended.');
        }

        $alreadyMember = GroupMember::where('study_group_id', $studyGroup->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($alreadyMember) {
            return back()->with('success', 'You are already a member of this group.');
        }

        GroupJoinRequest::updateOrCreate(
            [
                'study_group_id' => $studyGroup->id,
                'user_id' => Auth::id(),
            ],
            [
                // Re-requesting always resets the latest intent to pending.
                'status' => 'pending',
            ]
        );

        return back()->with('success', 'Join request sent.');
    }

    /**
     * Approve a join request and add the user as a member.
     */
    public function approveJoinRequest(GroupJoinRequest $joinRequest)
    {
        $studyGroup = $joinRequest->studyGroup;

        if (!$this->userCanManageGroup($studyGroup)) {
            abort(403);
        }

        GroupMember::firstOrCreate([
            'study_group_id' => $studyGroup->id,
            'user_id' => $joinRequest->user_id,
        ], [
            'role' => 'member',
            'joined_at' => now(),
        ]);

        GroupMessage::create([
            'study_group_id' => $studyGroup->id,
            'user_id' => $joinRequest->user_id,
            'message' => $joinRequest->user->name . ' joined the group.',
            'message_type' => 'system',
        ]);

        $joinRequest->update(['status' => 'approved']);

        return back()->with('success', 'Join request approved.');
    }

    /**
     * Decline a pending join request.
     */
    public function declineJoinRequest(GroupJoinRequest $joinRequest)
    {
        $studyGroup = $joinRequest->studyGroup;

        if (!$this->userCanManageGroup($studyGroup)) {
            abort(403);
        }

        $joinRequest->update(['status' => 'declined']);

        return back()->with('success', 'Join request declined.');
    }

    /**
     * Submit an appeal when a suspended group's creator requests review.
     */
    public function appealSuspension(Request $request, StudyGroup $studyGroup)
    {
        $membership = GroupMember::where('study_group_id', $studyGroup->id)
            ->where('user_id', Auth::id())
            ->first();

        $myRole = strtolower(trim((string) ($membership->role ?? '')));
        $isManager = in_array($myRole, ['creator', 'admin'], true);

        $isOwner = (int) $studyGroup->user_id === (int) Auth::id();

        if (!$isManager && !$isOwner) {
            abort(403);
        }

        if (!$studyGroup->isSuspended()) {
            return back()->with('error', 'This group is not suspended.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:2000',
        ]);

        $hasPending = GroupSuspensionAppeal::where('study_group_id', $studyGroup->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return back()->with('error', 'A group appeal is already pending review.');
        }

        GroupSuspensionAppeal::create([
            'study_group_id' => $studyGroup->id,
            'requester_id' => Auth::id(),
            'reason' => $validated['reason'],
            'message' => $validated['message'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Group suspension appeal submitted successfully.');
    }
}