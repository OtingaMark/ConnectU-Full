<x-layouts.app :title="'Admin - Reports and Moderation'" :pageTitle="'Reports / Moderation'">

    <div class="max-w-7xl mx-auto p-8 space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Reports / Moderation</h1>

            <a href="{{ route('admin.reports.generate') }}"
               class="inline-block px-5 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Generate Report
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Privacy Rule</h2>
            <p class="text-gray-600 dark:text-gray-300">
                Admin can moderate reported users and reported content. Private direct message content is only shown when that specific message is reported.
            </p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow"><p class="text-sm text-gray-500 dark:text-gray-300">Pending</p><p class="text-3xl font-bold">{{ $pendingCount }}</p></div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow"><p class="text-sm text-gray-500 dark:text-gray-300">Reviewed</p><p class="text-3xl font-bold">{{ $reviewedCount }}</p></div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow"><p class="text-sm text-gray-500 dark:text-gray-300">Resolved</p><p class="text-3xl font-bold">{{ $resolvedCount }}</p></div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow"><p class="text-sm text-gray-500 dark:text-gray-300">Rejected</p><p class="text-3xl font-bold">{{ $rejectedCount }}</p></div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 overflow-x-auto">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Actionable Reports</h2>

            <table class="w-full min-w-[1200px]">
                <thead>
                    <tr class="text-left border-b dark:border-gray-700">
                        <th class="py-3">Status</th>
                        <th class="py-3">Reporter</th>
                        <th class="py-3">Reported User / Content</th>
                        <th class="py-3">Reason</th>
                        <th class="py-3">Description</th>
                        <th class="py-3">Date</th>
                        <th class="py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($actionableReports as $report)
                        <tr class="border-b dark:border-gray-700 align-top">
                            <td class="py-3 pr-3">
                                <span class="px-2 py-1 rounded-full text-xs
                                    {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $report->status === 'reviewed' ? 'bg-blue-100 text-blue-700' : '' }}
                                ">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </td>

                            <td class="py-3 pr-3 text-sm text-gray-700 dark:text-gray-200">
                                {{ $report->reporter->name ?? 'Unknown' }}
                            </td>

                            <td class="py-3 pr-3 text-sm text-gray-700 dark:text-gray-200">
                                @if($report->reportedUser)
                                    <div>User: {{ $report->reportedUser->name }}</div>
                                @elseif($report->studyGroup)
                                    <div>Learning Group: {{ $report->studyGroup->group_name }}</div>
                                @elseif($report->groupMessage)
                                    <div>Group Msg by {{ $report->groupMessage->user->name ?? 'Unknown' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-300">{{ \Illuminate\Support\Str::limit($report->groupMessage->message, 120) }}</div>
                                @elseif($report->directMessage)
                                    <div>Direct Msg</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-300">{{ \Illuminate\Support\Str::limit($report->directMessage->message, 120) }}</div>
                                @elseif($report->feedback)
                                    <div>Peer Feedback</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-300">
                                        Sender: {{ $report->feedback->giver->name ?? 'Unknown' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-300">
                                        Receiver: {{ $report->feedback->receiver->name ?? 'Unknown' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-300">
                                        Rating: {{ $report->feedback->rating ?? 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-300">
                                        {{ \Illuminate\Support\Str::limit($report->feedback->comment, 120) }}
                                    </div>
                                @elseif($report->skill)
                                    <div>Skill Listing</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-300">
                                        Skill: {{ $report->skill->skill_name ?? 'Unknown' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-300">
                                        Owner: {{ $report->skill->user->name ?? 'Unknown' }}
                                    </div>
                                @else
                                    <div>Unknown target</div>
                                @endif
                            </td>

                            <td class="py-3 pr-3 text-sm text-gray-700 dark:text-gray-200">{{ $report->reason }}</td>
                            <td class="py-3 pr-3 text-sm text-gray-700 dark:text-gray-200">{{ $report->description ?: '-' }}</td>
                            <td class="py-3 pr-3 text-sm text-gray-700 dark:text-gray-200">{{ $report->created_at->format('Y-m-d H:i') }}</td>

                            <td class="py-3">
                                <div class="space-y-2">
                                    <details class="relative">
                                        <summary class="list-none cursor-pointer inline-block px-2.5 py-1.5 text-xs bg-gray-700 text-white rounded hover:bg-gray-800">
                                            Actions
                                        </summary>

                                        <div class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg shadow-lg p-3 z-20 space-y-2">
                                            <form method="POST" action="{{ route('admin.reports.review', $report) }}" class="flex gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="text" name="admin_notes" placeholder="Admin note" class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded">
                                                <button class="px-2 py-1 text-xs bg-blue-600 text-white rounded">Review</button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.reports.resolve', $report) }}" class="flex gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="text" name="admin_notes" placeholder="Resolution note" class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded">
                                                <button class="px-2 py-1 text-xs bg-green-600 text-white rounded">Resolve</button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.reports.reject', $report) }}" class="flex gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="text" name="admin_notes" placeholder="Rejection note" class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded">
                                                <button class="px-2 py-1 text-xs bg-red-600 text-white rounded">Reject</button>
                                            </form>

                                            @if($report->reported_user_id)
                                                <form method="POST" action="{{ route('admin.reports.suspend-user', $report) }}" class="flex gap-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="text" name="suspension_reason" required minlength="5" placeholder="User suspension reason" class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded">
                                                    <button class="px-2 py-1 text-xs bg-amber-600 text-white rounded">Suspend User</button>
                                                </form>
                                            @endif

                                            @if($report->study_group_id)
                                                <form method="POST" action="{{ route('admin.reports.suspend-group', $report) }}" class="flex gap-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="text" name="suspension_reason" required minlength="5" placeholder="Group suspension reason" class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded">
                                                    <button class="px-2 py-1 text-xs bg-orange-600 text-white rounded">Suspend Group</button>
                                                </form>
                                            @endif

                                            @if($report->group_message_id || $report->direct_message_id || $report->study_group_id || $report->feedback_id || $report->skill_id)
                                                <form method="POST" action="{{ route('admin.reports.delete-content', $report) }}" onsubmit="return confirm('Delete reported content and resolve this report?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="w-full px-2 py-1 text-xs bg-gray-700 text-white rounded text-left">Delete Reported Content</button>
                                                </form>
                                            @endif
                                        </div>
                                    </details>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500 dark:text-gray-300">
                                No reports found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $actionableReports->links() }}
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 overflow-x-auto">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Completed Reports</h2>

            <table class="w-full min-w-[1000px]">
                <thead>
                    <tr class="text-left border-b dark:border-gray-700">
                        <th class="py-3">Status</th>
                        <th class="py-3">Reporter</th>
                        <th class="py-3">Target</th>
                        <th class="py-3">Reason</th>
                        <th class="py-3">Admin Notes</th>
                        <th class="py-3">Completed At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($completedReports as $report)
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-3 pr-3">
                                <span class="px-2 py-1 rounded-full text-xs {{ $report->status === 'resolved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </td>
                            <td class="py-3 pr-3 text-sm text-gray-700 dark:text-gray-200">{{ $report->reporter->name ?? 'Unknown' }}</td>
                            <td class="py-3 pr-3 text-sm text-gray-700 dark:text-gray-200">
                                @if($report->reportedUser)
                                    User: {{ $report->reportedUser->name }}
                                @elseif($report->studyGroup)
                                    Learning Group: {{ $report->studyGroup->group_name }}
                                @elseif($report->groupMessage)
                                    Group message
                                @elseif($report->directMessage)
                                    Direct message
                                @elseif($report->feedback)
                                    Feedback
                                @elseif($report->skill)
                                    Skill
                                @else
                                    Unknown
                                @endif
                            </td>
                            <td class="py-3 pr-3 text-sm text-gray-700 dark:text-gray-200">{{ $report->reason }}</td>
                            <td class="py-3 pr-3 text-sm text-gray-700 dark:text-gray-200">{{ $report->admin_notes ?: '-' }}</td>
                            <td class="py-3 pr-3 text-sm text-gray-700 dark:text-gray-200">{{ optional($report->reviewed_at)->format('Y-m-d H:i') ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 dark:text-gray-300">No completed reports yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $completedReports->links() }}
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 overflow-x-auto">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Appeals Management</h2>

            <table class="w-full min-w-[1000px]">
                <thead>
                    <tr class="text-left border-b dark:border-gray-700">
                        <th class="py-3">User</th>
                        <th class="py-3">Suspension Reason</th>
                        <th class="py-3">Appeal Message</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appeals as $appeal)
                        <tr class="border-b dark:border-gray-700 align-top">
                            <td class="py-3 pr-3 text-sm text-gray-700 dark:text-gray-200">{{ $appeal->user->name ?? 'Unknown' }}</td>
                            <td class="py-3 pr-3 text-sm text-gray-700 dark:text-gray-200">{{ $appeal->user->suspension_reason ?: '-' }}</td>
                            <td class="py-3 pr-3 text-sm text-gray-700 dark:text-gray-200">{{ $appeal->message }}</td>
                            <td class="py-3 pr-3 text-sm text-gray-700 dark:text-gray-200">{{ ucfirst($appeal->status) }}</td>
                            <td class="py-3">
                                @if($appeal->status === 'pending')
                                    <div class="space-y-2">
                                        <form method="POST" action="{{ route('admin.appeals.approve', $appeal) }}" class="flex gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="admin_response" placeholder="Admin response" class="w-44 px-2 py-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded">
                                            <button class="px-2 py-1 bg-green-600 text-white rounded">Approve Appeal</button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.appeals.reject', $appeal) }}" class="flex gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="admin_response" placeholder="Admin response" class="w-44 px-2 py-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded">
                                            <button class="px-2 py-1 bg-red-600 text-white rounded">Reject Appeal</button>
                                        </form>
                                    </div>
                                @else
                                    <div class="text-xs text-gray-600 dark:text-gray-300">
                                        Completed
                                        @if($appeal->admin_response)
                                            <div class="mt-1">Response: {{ $appeal->admin_response }}</div>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500 dark:text-gray-300">
                                No appeals found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $appeals->links() }}
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 overflow-x-auto">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Group Appeals Management</h2>

            <table class="w-full min-w-[1000px]">
                <thead>
                    <tr class="text-left border-b dark:border-gray-700">
                        <th class="py-3">Learning Group</th>
                        <th class="py-3">Requester</th>
                        <th class="py-3">Appeal Reason</th>
                        <th class="py-3">Appeal Message</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($groupAppeals as $appeal)
                        <tr class="border-b dark:border-gray-700 align-top">
                            <td class="py-3 pr-3 text-sm text-gray-700 dark:text-gray-200">{{ $appeal->studyGroup->group_name ?? 'Unknown' }}</td>
                            <td class="py-3 pr-3 text-sm text-gray-700 dark:text-gray-200">{{ $appeal->requester->name ?? 'Unknown' }}</td>
                            <td class="py-3 pr-3 text-sm text-gray-700 dark:text-gray-200">{{ $appeal->reason }}</td>
                            <td class="py-3 pr-3 text-sm text-gray-700 dark:text-gray-200">{{ $appeal->message }}</td>
                            <td class="py-3 pr-3 text-sm text-gray-700 dark:text-gray-200">{{ ucfirst($appeal->status) }}</td>
                            <td class="py-3">
                                @if($appeal->status === 'pending')
                                    <div class="space-y-2">
                                        <form method="POST" action="{{ route('admin.group-appeals.approve', $appeal) }}" class="flex gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="admin_response" placeholder="Admin response" class="w-44 px-2 py-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded">
                                            <button class="px-2 py-1 bg-green-600 text-white rounded">Approve Appeal</button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.group-appeals.reject', $appeal) }}" class="flex gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="admin_response" placeholder="Admin response" class="w-44 px-2 py-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded">
                                            <button class="px-2 py-1 bg-red-600 text-white rounded">Reject Appeal</button>
                                        </form>
                                    </div>
                                @else
                                    <div class="text-xs text-gray-600 dark:text-gray-300">
                                        Completed
                                        @if($appeal->admin_response)
                                            <div class="mt-1">Response: {{ $appeal->admin_response }}</div>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 dark:text-gray-300">No group appeals found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $groupAppeals->links() }}
            </div>
        </div>
    </div>

</x-layouts.app>
