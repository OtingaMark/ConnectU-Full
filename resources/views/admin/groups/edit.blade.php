<x-layouts.app :title="'Admin - Edit Learning Group'" :pageTitle="'Edit Learning Group'">

    <div class="max-w-3xl mx-auto p-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Edit Learning Group</h1>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
            <form method="POST" action="{{ route('admin.study-groups.update', $studyGroup) }}" class="space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block font-semibold mb-2 text-gray-700 dark:text-gray-200">Current Status</label>
                    @if(strtolower(trim($studyGroup->status ?? 'active')) === 'suspended')
                        <span class="inline-flex px-3 py-1 rounded-full bg-red-100 text-red-700 text-sm">Suspended</span>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
                            Reason: {{ $studyGroup->suspension_reason ?: 'No reason provided.' }}
                        </p>
                    @else
                        <span class="inline-flex px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm">Active</span>
                    @endif
                </div>

                <div>
                    <label class="block font-semibold mb-2 text-gray-700 dark:text-gray-200">Group Name</label>
                    <input type="text" name="group_name" value="{{ old('group_name', $studyGroup->group_name) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3">
                </div>

                <div>
                    <label class="block font-semibold mb-2 text-gray-700 dark:text-gray-200">Course</label>
                    <input type="text" name="course" value="{{ old('course', $studyGroup->course) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3">
                </div>

                <div>
                    <label class="block font-semibold mb-2 text-gray-700 dark:text-gray-200">Description</label>
                    <textarea name="description" rows="4"
                              class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3">{{ old('description', $studyGroup->description) }}</textarea>
                </div>

                <div>
                    <label class="block font-semibold mb-2 text-gray-700 dark:text-gray-200">Maximum Members</label>
                    <input type="number" name="max_members" min="2" max="100" value="{{ old('max_members', $studyGroup->max_members) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3">
                </div>

                <div>
                    <label class="block font-semibold mb-2 text-gray-700 dark:text-gray-200">Meeting Schedule</label>
                    <input type="text" name="meeting_schedule" value="{{ old('meeting_schedule', $studyGroup->meeting_schedule) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3">
                </div>

                <div>
                    <label class="block font-semibold mb-2 text-gray-700 dark:text-gray-200">Visibility</label>
                    <select name="visibility"
                            class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3">
                        <option value="public" {{ old('visibility', $studyGroup->visibility) === 'public' ? 'selected' : '' }}>Public</option>
                        <option value="private" {{ old('visibility', $studyGroup->visibility) === 'private' ? 'selected' : '' }}>Private</option>
                    </select>
                </div>

                <div class="space-y-3">
                    <label class="flex items-center gap-3 text-gray-700 dark:text-gray-200">
                        <input type="checkbox" name="requires_approval" value="1" {{ old('requires_approval', $studyGroup->requires_approval) ? 'checked' : '' }}>
                        <span>Require admin approval before users can join</span>
                    </label>

                    <label class="flex items-center gap-3 text-gray-700 dark:text-gray-200">
                        <input type="checkbox" name="members_can_invite" value="1" {{ old('members_can_invite', $studyGroup->members_can_invite) ? 'checked' : '' }}>
                        <span>Allow members to invite connected peers</span>
                    </label>
                </div>

                <div class="flex gap-3">
                    <button class="px-5 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save Changes</button>
                    <a href="{{ route('admin.groups.index') }}"
                       class="px-5 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                        Cancel
                    </a>
                </div>
            </form>

            <div class="mt-8 border-t dark:border-gray-700 pt-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Moderation</h2>

                @if(strtolower(trim($studyGroup->status ?? 'active')) !== 'suspended')
                    <form method="POST" action="{{ route('admin.groups.suspend', $studyGroup) }}" class="space-y-3"
                          onsubmit="return confirm('Suspend this group? Members will not see it publicly until restored.');">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block font-semibold mb-2 text-gray-700 dark:text-gray-200">Suspension Reason</label>
                            <textarea name="suspension_reason" rows="3" required minlength="5"
                                      class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3"
                                      placeholder="Explain why this group is being suspended."></textarea>
                        </div>

                        <button class="px-5 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Suspend Group
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.groups.suspend', $studyGroup) }}"
                          onsubmit="return confirm('Unsuspend this group and make it visible again?');">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="action" value="unsuspend">

                        <button class="px-5 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Unsuspend Group
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

</x-layouts.app>
