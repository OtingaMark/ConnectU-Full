<x-layouts.app :title="$studyGroup->group_name" :pageTitle="'Learning Group Details'">

    @php
        $isCreator = auth()->id() == $studyGroup->user_id;
        $isMember = $studyGroup->members
            ->where('user_id', auth()->id())
            ->count() > 0;

        $myMembership = $studyGroup->members->where('user_id', auth()->id())->first();
        $myRole = strtolower(trim((string) ($myMembership->role ?? '')));
        $canManageGroup = $isCreator || in_array($myRole, ['creator', 'admin'], true);

        $myInvitation = $studyGroup->invitations
            ->where('receiver_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        $pendingJoinRequests = $studyGroup->joinRequests()
            ->with('user')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $visibleMembers = $studyGroup->members->filter(function ($member) use ($canManageGroup) {
            $status = strtolower(trim($member->user->status ?? 'active'));

            return $status === 'active' || $canManageGroup;
        });
    @endphp

    <div class="mb-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $studyGroup->group_name }}
                </h1>

                <p class="text-gray-600 dark:text-gray-300 mt-2">
                    {{ $studyGroup->course }}
                </p>

                <div class="flex flex-wrap gap-2 mt-3">
                    @if(strtolower(trim($studyGroup->status ?? 'active')) === 'suspended')
                        <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-sm">
                            ⛔ Suspended
                        </span>
                    @endif

                    @if($studyGroup->visibility === 'private')
                        <span class="px-3 py-1 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm">
                            🔒 Private Group
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm">
                            🌍 Public Group
                        </span>
                    @endif

                    @if($studyGroup->requires_approval)
                        <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-sm">
                            ⏳ Join Approval Required
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm">
                            ✅ Open Joining
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if($canManageGroup)
                    <a href="{{ route('study-groups.edit', $studyGroup->id) }}"
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Edit Group
                    </a>
                @endif

                @if($isCreator)
                    <form method="POST"
                          action="{{ route('study-groups.destroy', $studyGroup->id) }}"
                          onsubmit="return confirm('Delete this group permanently? This cannot be undone.');">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Delete Group
                        </button>
                    </form>
                @endif

                <details class="relative">
                    <summary class="px-4 py-2 bg-red-100 text-red-700 rounded-lg cursor-pointer">Report Group</summary>
                    <div class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-xl shadow-lg p-4 z-20">
                        <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" class="space-y-2">
                            @csrf
                            <input type="hidden" name="study_group_id" value="{{ $studyGroup->id }}">

                            <select name="reason" required class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg px-3 py-2">
                                <option value="">Select reason</option>
                                <option value="Inappropriate Content">Inappropriate Content</option>
                                <option value="Harassment">Harassment</option>
                                <option value="Spam">Spam</option>
                                <option value="Other">Other</option>
                            </select>

                            <textarea name="description" rows="3" placeholder="Describe the issue" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg px-3 py-2"></textarea>

                            <input type="file"
                                name="evidence_image"
                                accept="image/png,image/jpeg,image/webp"
                                class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg px-3 py-2 text-sm">

                            <button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Submit Report</button>
                        </form>
                    </div>
                </details>

                <a href="{{ route('study-groups.index') }}"
                   class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                    Back
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl bg-green-100 p-4 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-xl bg-red-100 p-4 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @if(strtolower(trim($studyGroup->status ?? 'active')) === 'suspended')
        <div class="mb-6 rounded-xl bg-red-100 p-4 text-red-800">
            <p class="font-semibold">This group is suspended.</p>
            <p class="text-sm mt-1">Reason: {{ $studyGroup->suspension_reason ?: 'No reason provided.' }}</p>

            @if($canManageGroup)
                <details class="mt-3">
                    <summary class="cursor-pointer font-semibold">Submit Group Appeal</summary>

                    <form method="POST" action="{{ route('study-groups.appeal-suspension', $studyGroup) }}" class="mt-3 space-y-2">
                        @csrf
                        <select name="reason" required class="w-full border border-red-300 bg-white rounded-lg px-3 py-2">
                            <option value="">Select reason</option>
                            <option value="Request Reconsideration">Request Reconsideration</option>
                            <option value="Policy Clarification">Policy Clarification</option>
                            <option value="Behavior Improved">Behavior Improved</option>
                            <option value="Other">Other</option>
                        </select>

                        <textarea name="message" rows="3" required minlength="10" placeholder="Explain why the group should be restored."
                                  class="w-full border border-red-300 bg-white rounded-lg px-3 py-2"></textarea>

                        <button class="px-4 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800">Submit Appeal</button>
                    </form>
                </details>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow p-6 space-y-6">
            @if($isMember || $isCreator)
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Description</h2>
                    <p class="text-gray-700 dark:text-gray-200 mt-2">
                        {{ $studyGroup->description ?? 'No description provided.' }}
                    </p>
                </div>

                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Meeting Schedule</h2>
                    <p class="text-gray-700 dark:text-gray-200 mt-2">
                        {{ $studyGroup->meeting_schedule ?? 'Not specified' }}
                    </p>
                </div>
            @endif

            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Created By</h2>
                <p class="text-gray-700 dark:text-gray-200 mt-2">
                    {{ $studyGroup->user->name ?? 'Unknown' }}
                </p>
            </div>

            @if(!$isMember && !$isCreator)
                @if($myInvitation)
                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-blue-900">
                        <p>
                            <strong>{{ $myInvitation->sender->name ?? 'A user' }}</strong>
                            invited you to join this learning group.
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <form method="POST" action="{{ route('group-invitations.accept', $myInvitation->id) }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">
                                Accept
                            </button>
                        </form>

                        <form method="POST" action="{{ route('group-invitations.decline', $myInvitation->id) }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                                Decline
                            </button>
                        </form>
                    </div>
                @else
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 p-4 text-gray-700 dark:text-gray-200">
                        You are not a member of this learning group yet.
                    </div>
                @endif
            @endif

            @if($canInviteMembers)
                <div class="border-t pt-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3">
                        Invite Connected Members
                    </h2>

                    @if(isset($inviteUsers) && $inviteUsers->count() > 0)
                        <form method="POST" action="{{ route('study-groups.invite', $studyGroup->id) }}"
                              class="flex flex-col gap-3">
                            @csrf

                            <input type="text" id="inviteUserSearch"
                                   placeholder="Search connected active users"
                                   class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-3">

                            <select name="receiver_id" id="inviteUserSelect"
                                    class="flex-1 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-3">
                                <option value="">Select a connected user</option>

                                @foreach($inviteUsers as $user)
                                    <option value="{{ $user->id }}" data-key="{{ strtolower($user->name . ' ' . $user->email) }}">
                                        {{ $user->name }} - {{ $user->email }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="md:self-end">
                                <button type="submit"
                                        class="rounded-xl bg-blue-600 px-5 py-3 text-white hover:bg-blue-700">
                                    Send Invite
                                </button>
                            </div>
                        </form>
                    @else
                        <p class="text-gray-600 dark:text-gray-300">
                            No connected users available to invite.
                        </p>
                    @endif
                </div>
            @endif

            @if($myMembership)
                <div class="border-t pt-6">
                    <form method="POST"
                          action="{{ route('study-groups.leave', $studyGroup->id) }}"
                          onsubmit="return confirm('Are you sure you want to leave this group? You will lose access to group messages and resources.');">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="px-5 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Leave Group
                        </button>
                    </form>
                </div>
            @endif

            @if($canManageGroup && $pendingJoinRequests->count())
                <div class="mt-6 border-t pt-6">
                    <h2 class="text-xl font-bold mb-4">Join Requests</h2>

                    <div class="space-y-3">
                        @foreach($pendingJoinRequests as $joinRequest)
                            <div class="flex justify-between items-center bg-yellow-50 rounded-lg p-4">
                                <div>
                                    <p class="font-semibold">{{ $joinRequest->user->name }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">wants to join this learning group</p>
                                </div>

                                <div class="flex gap-2">
                                    <form method="POST"
                                          action="{{ route('group-join-requests.approve', $joinRequest->id) }}"
                                          onsubmit="return confirm('Approve this join request?');">
                                        @csrf
                                        <button class="px-4 py-2 bg-green-600 text-white rounded-lg">
                                            Approve
                                        </button>
                                    </form>

                                    <form method="POST"
                                          action="{{ route('group-join-requests.decline', $joinRequest->id) }}"
                                          onsubmit="return confirm('Decline this join request?');">
                                        @csrf
                                        <button class="px-4 py-2 bg-red-600 text-white rounded-lg">
                                            Decline
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
            @if($isMember)
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                    Members {{ $visibleMembers->count() }}/{{ $studyGroup->max_members }}
                </h2>

                <div class="space-y-3">
                    @foreach($visibleMembers as $member)
                        <div class="border border-gray-100 rounded-lg p-3">
                            <div class="flex justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    @if($member->user->profile?->profile_picture)
                                        <img src="{{ asset('storage/' . $member->user->profile->profile_picture) }}"
                                             onclick="openImageViewer(this.src)"
                                             class="w-16 h-16 rounded-full object-cover cursor-pointer">
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xl">
                                            {{ strtoupper(substr($member->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif

                                    <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        <a href="{{ route('users.show', $member->user->id) }}"
                                           class="hover:text-blue-700 hover:underline">
                                            {{ $member->user->name ?? 'Unknown' }}
                                        </a>

                                        @if($canManageGroup && strtolower(trim($member->user->status ?? 'active')) === 'suspended')
                                            <span class="ml-2 px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs">Suspended</span>
                                        @endif
                                    </p>

                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        @if($member->role === 'creator')
                                            👑 Creator
                                        @elseif($member->role === 'admin')
                                            ⭐ Admin
                                        @else
                                            👤 Member
                                        @endif
                                    </p>
                                </div>
                                </div>

                                @if(
                                    $canManageGroup &&
                                    $member->user_id !== auth()->id() &&
                                    $member->role !== 'creator' &&
                                    !($myMembership->role === 'admin' && $member->role === 'admin')
                                )
                                    <div class="flex flex-col gap-2">

                                        @if($member->role === 'member')
                                            <form method="POST"
                                                  action="{{ route('study-groups.members.promote', [$studyGroup->id, $member->id]) }}"
                                                  onsubmit="return confirm('Are you sure you want to promote this member to admin?');">
                                                @csrf
                                                @method('PATCH')

                                                <button class="text-sm px-3 py-1 rounded-lg bg-green-100 text-green-700 hover:bg-green-200">
                                                    Promote
                                                </button>
                                            </form>
                                        @endif

                                        @if($member->role === 'admin')
                                            <form method="POST"
                                                  action="{{ route('study-groups.members.demote', [$studyGroup->id, $member->id]) }}"
                                                  onsubmit="return confirm('Are you sure you want to demote this admin to member?');">
                                                @csrf
                                                @method('PATCH')

                                                <button class="text-sm px-3 py-1 rounded-lg bg-yellow-100 text-yellow-700 hover:bg-yellow-200">
                                                    Demote
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST"
                                              action="{{ route('study-groups.members.remove', [$studyGroup->id, $member->id]) }}"
                                              onsubmit="return confirm('Are you sure you want to remove this member from the group?');">
                                            @csrf
                                            @method('DELETE')

                                            <button class="text-sm px-3 py-1 rounded-lg bg-red-100 text-red-700 hover:bg-red-200">
                                                Remove
                                            </button>
                                        </form>

                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                    Membership Required
                </h2>
                <p class="text-gray-700 dark:text-gray-200">
                    Accept your invitation to view members and internal group information.
                </p>
            @endif

            @if(
                auth()->id() == $studyGroup->user_id &&
                $studyGroup->invitations->where('status', 'pending')->count() > 0
            )
                <div class="mt-6 border-t pt-4">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-3">
                        Pending Invitations
                    </h3>

                    <div class="space-y-2">
                        @foreach($studyGroup->invitations->where('status', 'pending') as $invitation)
                            <div class="rounded-lg bg-yellow-50 p-3 text-sm text-yellow-800">
                                {{ $invitation->receiver->name ?? 'Unknown' }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

    </div>

    @if($isMember)
        <div class="mt-6 rounded-2xl bg-gray-50 dark:bg-gray-900 p-6 shadow">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Learning Group Chat</h2>

            <div class="space-y-3 max-h-[420px] overflow-y-auto mb-4">
                @forelse($studyGroup->messages->sortBy('created_at') as $message)
                    @if($message->message_type === 'system')
                        <div class="text-center my-3">
                            <span class="inline-block bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm px-4 py-2 rounded-full">
                                {{ $message->message }}
                            </span>
                        </div>
                    @else
                        <div class="{{ $message->user_id == auth()->id() ? 'text-right' : 'text-left' }}">
                            <div class="inline-block max-w-xl rounded-2xl px-4 py-3 {{ $message->user_id == auth()->id() ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white' }}">

                                <div class="text-xs opacity-80 mb-1">
                                    {{ $message->user->name ?? 'Unknown' }}
                                </div>

                                @if($message->message)
                                    <div>{{ $message->message }}</div>
                                @endif

                                @if($message->resource_link)
                                    <div class="mt-1">
                                        <a href="{{ $message->resource_link }}" target="_blank" rel="noopener" class="underline">
                                            Open shared link
                                        </a>
                                    </div>
                                @endif

                                @if($message->file_path)
                                    <div class="mt-1">
                                        <a href="{{ route('group-messages.attachment', $message->id) }}" class="underline">
                                            Open attachment
                                        </a>
                                    </div>
                                @endif

                                <div class="text-[11px] opacity-75 mt-2">
                                    {{ $message->created_at->format('H:i') }}
                                </div>

                            </div>
                        </div>
                    @endif

                @empty
                    <div class="text-gray-600 dark:text-gray-300">No group messages yet.</div>
                @endforelse
            </div>

            @if(strtolower(trim($studyGroup->status ?? 'active')) === 'suspended')
                <div class="rounded-xl bg-red-100 p-4 text-red-800">
                    Messaging is disabled while this group is suspended.
                </div>
            @else
                <form method="POST"
                      action="{{ route('study-groups.messages.store', $studyGroup->id) }}"
                      enctype="multipart/form-data"
                      class="flex gap-3 items-center">
                    @csrf

                    <input type="text" name="message" placeholder="Type a message..."
                          class="flex-1 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-300 px-4 py-3" />

                    <input type="url" name="resource_link" placeholder="https://resource-link.com"
                          class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-300 px-4 py-3" />

                    <input type="file" name="message_file" class="text-sm" />

                    <button type="submit" class="rounded-xl bg-blue-600 px-5 py-3 text-white hover:bg-blue-700">
                        Send
                    </button>
                </form>
            @endif
        </div>
    @endif

    <script>
        const inviteSearch = document.getElementById('inviteUserSearch');
        const inviteSelect = document.getElementById('inviteUserSelect');

        function normalizeInvite(value) {
            return (value || '').toString().toLowerCase().trim();
        }

        function applyInviteSearch() {
            if (!inviteSelect) {
                return;
            }

            const query = normalizeInvite(inviteSearch?.value);
            const options = Array.from(inviteSelect.options);

            options.forEach((option, index) => {
                if (index === 0) {
                    option.hidden = false;
                    return;
                }

                const key = normalizeInvite(option.dataset.key || option.textContent);
                option.hidden = query && !key.includes(query);
            });
        }

        if (inviteSearch) {
            inviteSearch.addEventListener('input', applyInviteSearch);
            applyInviteSearch();
        }
    </script>

</x-layouts.app>