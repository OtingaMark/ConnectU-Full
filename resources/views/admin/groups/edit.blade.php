<x-layouts.app :title="'Admin - Edit Learning Group'" :pageTitle="'Edit Learning Group'">

    <div class="max-w-3xl mx-auto p-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Edit Learning Group</h1>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
            <form method="POST" action="{{ route('admin.study-groups.update', $studyGroup) }}" class="space-y-5">
                @csrf
                @method('PATCH')

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
        </div>
    </div>

</x-layouts.app>
