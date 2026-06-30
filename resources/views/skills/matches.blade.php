<x-layouts.app :title="'Skill Matches'" :pageTitle="'Skill Matches'">
    <div class="max-w-7xl mx-auto p-6 space-y-6">
        <section class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Matches for {{ $sourceSkill->skill_name }}</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Find peers based on skill name, category, type, level, and related keywords.</p>
                </div>
                <a href="{{ route('skills.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">Back to Skills</a>
            </div>

            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-5">
                <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Search skill keywords"
                       class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white">

                <select name="category" class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white">
                    @foreach(['Academic', 'Programming', 'Sports', 'Music', 'Public Speaking', 'Design', 'Other'] as $category)
                        <option value="{{ $category }}" @selected($filters['category'] === $category)>{{ $category }}</option>
                    @endforeach
                </select>

                <select name="skill_type" class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white">
                    <option value="all" @selected($filters['skill_type'] === 'all')>Compatible types</option>
                    <option value="can_teach" @selected($filters['skill_type'] === 'can_teach')>Can teach</option>
                    <option value="want_to_learn" @selected($filters['skill_type'] === 'want_to_learn')>Wants to learn</option>
                    <option value="exchange" @selected($filters['skill_type'] === 'exchange')>Exchange</option>
                    <option value="teamwork" @selected($filters['skill_type'] === 'teamwork')>Teamwork</option>
                </select>

                <div class="flex gap-2">
                    <select name="skill_level" class="flex-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white">
                        <option value="all" @selected($filters['skill_level'] === 'all')>All levels</option>
                        <option value="Beginner" @selected($filters['skill_level'] === 'Beginner')>Beginner</option>
                        <option value="Intermediate" @selected($filters['skill_level'] === 'Intermediate')>Intermediate</option>
                        <option value="Advanced" @selected($filters['skill_level'] === 'Advanced')>Advanced</option>
                    </select>
                    <button class="px-4 py-3 rounded-xl bg-blue-600 text-white hover:bg-blue-700">Filter</button>
                </div>
            </form>
        </section>

        <section>
            @if($matches->count())
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($matches as $skill)
                        @php
                            $type = \App\Models\Skill::normalizedType($skill->skill_type ?? null);
                            $label = match ($type) {
                                \App\Models\Skill::TYPE_CAN_TEACH => 'Can teach',
                                \App\Models\Skill::TYPE_WANT_TO_LEARN => 'Wants to learn',
                                \App\Models\Skill::TYPE_EXCHANGE => 'Exchange',
                                \App\Models\Skill::TYPE_TEAMWORK => 'Teamwork',
                                default => 'Can teach',
                            };
                        @endphp

                        <article class="bg-white dark:bg-gray-800 rounded-2xl shadow p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-bold text-lg text-gray-900 dark:text-white">{{ $skill->skill_name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $skill->user->name ?? 'Unknown' }}</p>
                                </div>
                                <span class="px-2 py-1 rounded-full text-xs bg-indigo-100 text-indigo-700">{{ $skill->match_score }}</span>
                            </div>

                            <div class="flex flex-wrap gap-2 mt-3">
                                <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700">{{ $skill->category }}</span>
                                <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">{{ $skill->skill_level }}</span>
                                <span class="px-2 py-1 rounded-full text-xs bg-emerald-100 text-emerald-700">{{ $label }}</span>
                            </div>

                            <p class="text-sm text-gray-700 dark:text-gray-200 mt-3">{{ $skill->description ?: 'No description provided.' }}</p>

                            @if($type === \App\Models\Skill::TYPE_EXCHANGE && $skill->exchange_skill_needed)
                                <p class="text-xs text-indigo-700 dark:text-indigo-300 mt-2">Exchange need: {{ $skill->exchange_skill_needed }}</p>
                            @endif

                            @if($type === \App\Models\Skill::TYPE_TEAMWORK && $skill->collaboration_goal)
                                <p class="text-xs text-purple-700 dark:text-purple-300 mt-2">Goal: {{ $skill->collaboration_goal }}</p>
                            @endif

                            <div class="mt-4 grid grid-cols-2 gap-2">
                                <a href="{{ route('messages.index', ['user' => $skill->user_id]) }}" class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-center text-sm">Message</a>
                                <a href="{{ route('users.show', $skill->user_id) }}" class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 text-center text-sm">View Profile</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-10 text-center">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">No matches found</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Try broadening search keywords or selecting all levels/types.</p>
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>
