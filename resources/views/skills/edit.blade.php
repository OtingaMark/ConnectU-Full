<x-layouts.app :title="'Edit Skill'" :pageTitle="'Edit Skill'">
    <div class="max-w-4xl mx-auto p-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Update Skill</h1>

            <form method="POST" action="{{ route('skills.update', $skill) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Skill Name</label>
                    <input type="text" name="skill_name" required value="{{ old('skill_name', $skill->skill_name) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Category</label>
                    <select name="category" required class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white">
                        @foreach(['Academic', 'Programming', 'Sports', 'Music', 'Public Speaking', 'Design', 'Other'] as $category)
                            <option value="{{ $category }}" @selected(old('category', $skill->category) === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Type</label>
                    <select name="skill_type" required class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white">
                        <option value="can_teach" @selected(\App\Models\Skill::normalizedType(old('skill_type', $skill->skill_type)) === \App\Models\Skill::TYPE_CAN_TEACH)>Can teach</option>
                        <option value="want_to_learn" @selected(\App\Models\Skill::normalizedType(old('skill_type', $skill->skill_type)) === \App\Models\Skill::TYPE_WANT_TO_LEARN)>Wants to learn</option>
                        <option value="exchange" @selected(\App\Models\Skill::normalizedType(old('skill_type', $skill->skill_type)) === \App\Models\Skill::TYPE_EXCHANGE)>Exchange</option>
                        <option value="teamwork" @selected(\App\Models\Skill::normalizedType(old('skill_type', $skill->skill_type)) === \App\Models\Skill::TYPE_TEAMWORK)>Teamwork</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Level</label>
                    <select name="skill_level" required class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white">
                        @foreach(['Beginner', 'Intermediate', 'Advanced'] as $level)
                            <option value="{{ $level }}" @selected(old('skill_level', $skill->skill_level) === $level)>{{ $level }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Availability</label>
                    <input type="text" name="availability" value="{{ old('availability', $skill->availability) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white"
                           placeholder="e.g. Weekends, Evenings, Online only">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Exchange Skill Needed</label>
                    <input type="text" name="exchange_skill_needed" value="{{ old('exchange_skill_needed', $skill->exchange_skill_needed) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white"
                           placeholder="e.g. UI Design">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Collaboration Goal</label>
                    <input type="text" name="collaboration_goal" value="{{ old('collaboration_goal', $skill->collaboration_goal) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white"
                           placeholder="e.g. Build a Laravel project, prepare for exam">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Description</label>
                    <textarea name="description" rows="4"
                              class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white"
                              placeholder="Describe your skill and expectations">{{ old('description', $skill->description) }}</textarea>
                </div>

                <div class="md:col-span-2 flex justify-between">
                    <a href="{{ route('skills.index') }}" class="px-5 py-3 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</a>
                    <button class="px-6 py-3 rounded-xl bg-blue-600 text-white hover:bg-blue-700">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
