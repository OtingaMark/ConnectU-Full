<x-layouts.app :title="'Create Learning Group'" :pageTitle="'Create Learning Group'">

    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create Learning Group</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-2">
                Start a group where students can collaborate, discuss topics and support each other.
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-8">
            <form action="{{ route('study-groups.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label class="block font-semibold mb-2">Group Name</label>
                    <input type="text" name="group_name" value="{{ old('group_name') }}"
                           placeholder="Example: Web Development Team"
                              class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block font-semibold mb-2">Course</label>
                    <input type="text" name="course" value="{{ old('course') }}"
                           placeholder="Example: Informatics and Computer Science"
                              class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block font-semibold mb-2">Description</label>
                    <textarea name="description" rows="4"
                              placeholder="Describe what this group is about..."
                              class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block font-semibold mb-2">Maximum Members</label>
                    <input type="number" name="max_members" value="{{ old('max_members', 10) }}"
                              class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block font-semibold mb-2">Meeting Schedule</label>
                    <input type="text" name="meeting_schedule" value="{{ old('meeting_schedule') }}"
                           placeholder="Example: Every Friday evening"
                              class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block font-semibold mb-2">Visibility</label>
                    <select name="visibility"
                            class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3">
                        <option value="public" {{ old('visibility') === 'public' ? 'selected' : '' }}>Public - visible to everyone</option>
                        <option value="private" {{ old('visibility') === 'private' ? 'selected' : '' }}>Private - invite only</option>
                    </select>
                </div>

                <div class="space-y-3">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="requires_approval" value="1" {{ old('requires_approval') ? 'checked' : '' }}>
                        <span>Require admin approval before users can join</span>
                    </label>

                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="members_can_invite" value="1" {{ old('members_can_invite') ? 'checked' : '' }}>
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

                <div class="flex justify-between items-center pt-4">
                    <a href="{{ route('study-groups.index') }}"
                              class="text-gray-600 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400">
                        ← Back to Learning Groups
                    </a>

                    <button type="submit"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Create Group
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>
