<x-layouts.app :title="'Learning Groups'" :pageTitle="'Learning Groups'">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Learning Groups</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-2">
                Create, discover and join learning groups for collaborative growth.
            </p>
        </div>

        <a href="{{ route('study-groups.create') }}"
           class="px-5 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + Create Group
        </a>
    </div>

    @if($studyGroups->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($studyGroups as $group)
                @php
                    $memberCount = \App\Models\GroupMember::where('study_group_id', $group->id)->count();
                @endphp

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 hover:shadow-lg transition h-full flex flex-col">
                    <div class="flex flex-col gap-4 mb-4">
                        <div class="flex items-start gap-3 min-w-0">
                            @if($group->group_picture)
                                <img src="{{ asset('storage/' . $group->group_picture) }}"
                                     onclick="openImageViewer(this.src)"
                                     class="w-14 h-14 rounded-full object-cover cursor-pointer shrink-0">
                            @else
                                <div class="w-14 h-14 rounded-full bg-green-600 text-white flex items-center justify-center shrink-0">
                                    📚
                                </div>
                            @endif

                            <div class="min-w-0">
                                <h2 class="text-xl font-bold text-blue-700 break-words">
                                    {{ $group->group_name }}
                                </h2>
                                <p class="text-sm text-gray-500 break-words">
                                    {{ $group->course }}
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            @if(isset($group->match_score))
                                <span class="inline-block text-sm bg-green-100 text-green-700 px-3 py-1 rounded-full">
                                    {{ $group->match_score }}% Match
                                </span>
                            @endif

                            <span class="inline-block text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded-full">
                                {{ $memberCount }}/{{ $group->max_members }} Members
                            </span>
                        </div>
                    </div>

                    <p class="text-gray-700 dark:text-gray-200 mb-4 flex-1">
                        {{ $group->description ?? 'No description provided.' }}
                    </p>

                    <div class="flex flex-wrap gap-2 mt-3 mb-4">
                        @if($group->visibility === 'private')
                            <span class="px-3 py-1 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm">
                                🔒 Private
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm">
                                🌍 Public
                            </span>
                        @endif

                        @if($group->requires_approval)
                            <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-sm">
                                ⏳ Approval Required
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm">
                                ✅ Instant Join
                            </span>
                        @endif
                    </div>

                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300 mb-5">
                        <p><strong>Schedule:</strong> {{ $group->meeting_schedule ?? 'Not specified' }}</p>
                        <p><strong>Created By:</strong> {{ $group->user->name ?? 'Unknown' }}</p>
                    </div>

                    <a href="{{ route('study-groups.show', $group->id) }}"
                       class="mb-3 inline-block w-full px-4 py-2 text-center bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                        View Group
                    </a>

                    @php
                        $pendingJoinRequest = \App\Models\GroupJoinRequest::where('study_group_id', $group->id)
                            ->where('user_id', auth()->id())
                            ->where('status', 'pending')
                            ->exists();
                    @endphp

                    @if($group->is_joined)
                        <div class="px-4 py-3 bg-green-100 text-green-700 rounded-lg text-center">
                            You have joined this group
                        </div>

                    @elseif($pendingJoinRequest)
                        <div class="px-4 py-3 bg-yellow-100 text-yellow-700 rounded-lg text-center">
                            Request Pending
                        </div>

                    @elseif($group->requires_approval)
                        <form action="{{ route('study-groups.request-join', $group->id) }}" method="POST"
                              onsubmit="return confirm('Send request to join this group?');">
                            @csrf
                            <button type="submit"
                                    class="w-full px-4 py-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                                Request to Join
                            </button>
                        </form>

                    @else
                        <form action="{{ route('study-groups.join', $group->id) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to join this group?');">
                            @csrf
                            <button type="submit"
                                    class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Join Group
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-10 text-center">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">No learning groups yet</h2>
            <p class="text-gray-600 dark:text-gray-300 mt-2">
                Create the first learning group and start collaborating with your peers.
            </p>

            <a href="{{ route('study-groups.create') }}"
               class="inline-block mt-6 px-5 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Create Learning Group
            </a>
        </div>
    @endif

</x-layouts.app>