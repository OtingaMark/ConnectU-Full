<x-layouts.app :title="'Edit Group'" :pageTitle="'Edit Group Settings'">

    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Group Settings</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-2">
                Update group details, member limit, and joining permissions.
            </p>
        </div>

        @if(session('error'))
            <div class="mb-6 rounded-xl bg-red-100 p-4 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-8">
            <form method="POST"
                  action="{{ route('study-groups.update', $studyGroup->id) }}"
                enctype="multipart/form-data"
                  onsubmit="return confirm('Are you sure you want to update this group settings?');"
                  class="space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block font-semibold mb-2">Group Name</label>
                    <input type="text" name="group_name"
                           value="{{ old('group_name', $studyGroup->group_name) }}"
                              class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3">
                </div>

                <div>
                    <label class="block font-semibold mb-2">Course</label>
                    <input type="text" name="course"
                           value="{{ old('course', $studyGroup->course) }}"
                              class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3">
                </div>

                <div>
                    <label class="block font-semibold mb-2">Description</label>
                    <textarea name="description" rows="4"
                              class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3">{{ old('description', $studyGroup->description) }}</textarea>
                </div>

                <div>
                    <label class="block font-semibold mb-2">Maximum Members</label>
                    <input type="number" name="max_members"
                           value="{{ old('max_members', $studyGroup->max_members) }}"
                           min="2"
                           max="100"
                              class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3">
                </div>

                <div>
                    <label class="block font-semibold mb-2">Meeting Schedule</label>
                    <input type="text" name="meeting_schedule"
                           value="{{ old('meeting_schedule', $studyGroup->meeting_schedule) }}"
                              class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3">
                </div>

                <div>
                    <label class="block font-semibold mb-2">Visibility</label>
                    <select name="visibility"
                            class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3">
                        <option value="public" {{ old('visibility', $studyGroup->visibility) === 'public' ? 'selected' : '' }}>
                            Public - visible to everyone
                        </option>

                        <option value="private" {{ old('visibility', $studyGroup->visibility) === 'private' ? 'selected' : '' }}>
                            Private - invite only
                        </option>
                    </select>
                </div>

                <div class="space-y-3">
                    <label class="flex items-center gap-3">
                        <input type="checkbox"
                               name="requires_approval"
                               value="1"
                               {{ old('requires_approval', $studyGroup->requires_approval) ? 'checked' : '' }}>
                        <span>Require admin approval before users can join</span>
                    </label>

                    <label class="flex items-center gap-3">
                        <input type="checkbox"
                               name="members_can_invite"
                               value="1"
                               {{ old('members_can_invite', $studyGroup->members_can_invite) ? 'checked' : '' }}>
                        <span>Allow members to invite connected peers</span>
                    </label>
                </div>

                <div>
                    <label class="block font-semibold mb-2">Group Picture</label>
                    <input type="file"
                           name="group_picture"
                           accept="image/*"
                              class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3">
                </div>

                <div class="flex justify-between pt-4">
                    <div class="flex gap-3">
                        <a href="{{ route('study-groups.show', $studyGroup->id) }}"
                                  class="px-5 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                            Cancel
                        </a>

                        @if(auth()->id() == $studyGroup->user_id)
                            <form method="POST"
                                  action="{{ route('study-groups.destroy', $studyGroup->id) }}"
                                  onsubmit="return confirm('Delete this group permanently? This cannot be undone.');">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="px-5 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                    Delete Group
                                </button>
                            </form>
                        @endif
                    </div>

                    <button type="submit"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>
