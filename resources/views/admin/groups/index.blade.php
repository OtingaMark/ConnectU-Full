<x-layouts.app :title="'Admin - Learning Groups Management'" :pageTitle="'Learning Groups Management'">

    <div class="max-w-7xl mx-auto p-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Learning Groups Management</h1>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b dark:border-gray-700">
                        <th class="py-3">Group</th>
                        <th>Course</th>
                        <th>Members</th>
                        <th>Creator</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allGroups as $group)
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-3">{{ $group->group_name }}</td>
                            <td>{{ $group->course }}</td>
                            <td>{{ $group->members_count }}</td>
                            <td>{{ $group->user->name ?? 'Unknown' }}</td>
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('study-groups.show', $group) }}"
                                       class="px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                                        View Group
                                    </a>
                                    <a href="{{ route('admin.study-groups.edit', $group) }}"
                                       class="px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                        Edit Group
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.groups.delete', $group) }}"
                                          onsubmit="return confirm('Delete this learning group?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                            Delete Group
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $allGroups->links() }}
            </div>
        </div>
    </div>

</x-layouts.app>
