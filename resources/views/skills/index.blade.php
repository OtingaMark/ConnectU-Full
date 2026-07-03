<x-layouts.app :title="'Skills'" :pageTitle="'Skills Hub'">

    @php
        $allSkills = $skills ?? collect();
        $mySkills = $allSkills->where('user_id', auth()->id())->values();

        $normalizedType = function ($skill) {
            return \App\Models\Skill::normalizedType($skill->skill_type ?? null);
        };

        $isLearning = function ($skill) {
            return \App\Models\Skill::normalizedType($skill->skill_type ?? null) === \App\Models\Skill::TYPE_WANT_TO_LEARN;
        };

        $isTeaching = function ($skill) {
            return \App\Models\Skill::normalizedType($skill->skill_type ?? null) === \App\Models\Skill::TYPE_CAN_TEACH;
        };

        $myLearningSkills = $mySkills->filter($isLearning)->values();
        $myTeachingSkills = $mySkills->filter($isTeaching)->values();

        $exchangeNeededSkills = $mySkills
            ->filter(fn ($skill) => $normalizedType($skill) === \App\Models\Skill::TYPE_EXCHANGE)
            ->pluck('exchange_skill_needed')
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->values();

        $myLearningSkillNames = $myLearningSkills
            ->pluck('skill_name')
            ->map(fn ($name) => trim((string) $name))
            ->merge($exchangeNeededSkills)
            ->filter()
            ->unique(fn ($name) => strtolower($name))
            ->values();

        $requestsReceived = 0;

        $profile = auth()->user()->profile;
        $interestsBlob = strtolower(trim(($profile->interests ?? '') . ' ' . ($profile->skills ?? '') . ' ' . ($profile->course ?? '')));
        $mySkillNames = $mySkills->pluck('skill_name')->map(fn ($n) => strtolower(trim($n)))->all();

        $recommended = $allSkills
            ->filter(function ($skill) use ($interestsBlob, $mySkillNames) {
                $name = strtolower((string) $skill->skill_name);
                $desc = strtolower((string) $skill->description);

                if (in_array($name, $mySkillNames, true)) {
                    return false;
                }

                if (empty($interestsBlob)) {
                    return true;
                }

                return str_contains($interestsBlob, $name) || str_contains($desc, $interestsBlob) || str_contains($interestsBlob, $desc);
            })
            ->unique('skill_name')
            ->take(6)
            ->values();
    @endphp

    <div id="skillsSkeleton" class="space-y-6">
        <div class="h-24 rounded-2xl bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="h-24 rounded-2xl bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
            <div class="h-24 rounded-2xl bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
            <div class="h-24 rounded-2xl bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
            <div class="h-24 rounded-2xl bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
        </div>
        <div class="h-72 rounded-2xl bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
    </div>

    <div id="skillsContent" class="hidden space-y-8">
        <section class="rounded-2xl bg-gradient-to-r from-blue-700 to-blue-500 text-white p-8 shadow-lg">
            <h1 class="text-3xl md:text-4xl font-bold">Skills Collaboration Network</h1>
            <p class="mt-2 text-blue-50 max-w-3xl">
                Share what you can teach, discover what to learn, and connect with peers to grow faster across courses and learning groups.
            </p>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow">
                <p class="text-sm text-gray-500 dark:text-gray-300">Total Skills</p>
                <p class="text-3xl font-bold text-blue-700 dark:text-blue-400">{{ $allSkills->count() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow">
                <p class="text-sm text-gray-500 dark:text-gray-300">Skills I'm Learning</p>
                <p class="text-3xl font-bold text-indigo-700 dark:text-indigo-400">{{ $myLearningSkills->count() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow">
                <p class="text-sm text-gray-500 dark:text-gray-300">Skills I Can Teach</p>
                <p class="text-3xl font-bold text-emerald-700 dark:text-emerald-400">{{ $myTeachingSkills->count() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow">
                <p class="text-sm text-gray-500 dark:text-gray-300">Requests Received</p>
                <p class="text-3xl font-bold text-amber-700 dark:text-amber-400">{{ $requestsReceived }}</p>
            </div>
        </section>

        <section class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Add New Skill</h2>
                <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full">Live in ConnectU profile ecosystem</span>
            </div>

            <form action="{{ route('skills.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3">
                @csrf
                <input type="text" name="skill_name" value="{{ old('skill_name') }}" required
                       placeholder="Skill name"
                       class="xl:col-span-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl px-4 py-3">

                <select name="category" required
                        class="xl:col-span-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl px-4 py-3">
                    <option value="">Category</option>
                    <option value="Academic">Academic</option>
                    <option value="Programming">Programming</option>
                    <option value="Sports">Sports</option>
                    <option value="Music">Music</option>
                    <option value="Public Speaking">Public Speaking</option>
                    <option value="Design">Design</option>
                    <option value="Other">Other</option>
                </select>

                <select name="skill_type" required
                        class="xl:col-span-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl px-4 py-3">
                    <option value="">Skill type</option>
                    <option value="can_teach">Can teach</option>
                    <option value="want_to_learn">Wants to learn</option>
                    <option value="exchange">Exchange</option>
                    <option value="teamwork">Teamwork</option>
                </select>

                <select name="skill_level" required
                        class="xl:col-span-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl px-4 py-3">
                    <option value="">Level</option>
                    <option value="Beginner">Beginner</option>
                    <option value="Intermediate">Intermediate</option>
                    <option value="Advanced">Advanced</option>
                </select>

                <input type="text" name="availability" value="{{ old('availability') }}"
                       placeholder="Availability (e.g. Weekend mentoring)"
                       class="xl:col-span-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl px-4 py-3">

                <input type="text" name="description" value="{{ old('description') }}"
                       placeholder="Description"
                       class="xl:col-span-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl px-4 py-3">

                  <input type="text" name="exchange_skill_needed" value="{{ old('exchange_skill_needed') }}"
                      placeholder="Exchange skill needed (optional)"
                      class="xl:col-span-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl px-4 py-3">

                  <input type="text" name="collaboration_goal" value="{{ old('collaboration_goal') }}"
                      placeholder="Collaboration goal (optional)"
                      class="xl:col-span-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl px-4 py-3">

                <button class="xl:col-span-6 bg-blue-600 text-white rounded-xl px-5 py-3 hover:bg-blue-700 transition">
                    Add Skill
                </button>
            </form>
        </section>

        <section class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 space-y-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Search & Filter</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
                <input id="skillsSearch" type="text" placeholder="Search skills..."
                       class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl px-4 py-3">

                <select id="skillsCategory" class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl px-4 py-3">
                    <option value="all">All categories</option>
                    <option value="academic">Academic</option>
                    <option value="programming">Programming</option>
                    <option value="sports">Sports</option>
                    <option value="music">Music</option>
                    <option value="public speaking">Public Speaking</option>
                    <option value="design">Design</option>
                    <option value="other">Other</option>
                </select>

                <select id="skillsLevel" class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl px-4 py-3">
                    <option value="all">All levels</option>
                    <option value="Beginner">Beginner</option>
                    <option value="Intermediate">Intermediate</option>
                    <option value="Advanced">Advanced</option>
                </select>

                <select id="skillsSort" class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl px-4 py-3">
                    <option value="newest">Sort: Newest</option>
                    <option value="popularity">Sort: Popularity</option>
                    <option value="alphabetical">Sort: Alphabetical</option>
                </select>
            </div>
        </section>

        <section>
            <div class="flex items-center justify-between mb-4 gap-3">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Skills Directory</h2>
                <p id="skillsResultCount" class="text-sm text-gray-600 dark:text-gray-300"></p>
            </div>

            @if($allSkills->count() > 0)
                <div id="skillsGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($allSkills as $skill)
                        @php
                            $nameKey = strtolower(trim($skill->skill_name));
                            $offerCount = $allSkills->where('skill_name', $skill->skill_name)->count();
                            $learnCount = $allSkills->filter(function ($s) use ($nameKey) {
                                return strtolower(trim($s->skill_name)) === $nameKey
                                    && \App\Models\Skill::normalizedType($s->skill_type ?? null) === \App\Models\Skill::TYPE_WANT_TO_LEARN;
                            })->count();

                            $category = $skill->category ?? 'Other';
                            $skillType = $normalizedType($skill);

                            $isMine = (int) $skill->user_id === (int) auth()->id();

                            $actionLabel = match ($skillType) {
                                \App\Models\Skill::TYPE_CAN_TEACH => 'Find Learners',
                                \App\Models\Skill::TYPE_WANT_TO_LEARN => 'Find Teachers',
                                default => 'Find Partners',
                            };

                            $typeBadge = match ($skillType) {
                                \App\Models\Skill::TYPE_CAN_TEACH => 'Can teach',
                                \App\Models\Skill::TYPE_WANT_TO_LEARN => 'Wants to learn',
                                \App\Models\Skill::TYPE_EXCHANGE => 'Exchange',
                                \App\Models\Skill::TYPE_TEAMWORK => 'Teamwork',
                                default => 'Can teach',
                            };
                        @endphp

                        <article class="skill-card bg-white dark:bg-gray-800 rounded-2xl shadow p-5 hover:shadow-xl hover:-translate-y-0.5 transition min-h-[300px] flex flex-col"
                                 data-name="{{ strtolower($skill->skill_name) }}"
                                 data-description="{{ strtolower($skill->description ?? '') }}"
                                 data-level="{{ $skill->skill_level }}"
                                 data-category="{{ strtolower($category) }}"
                                 data-offers="{{ $offerCount }}"
                                 data-created="{{ $skill->created_at?->timestamp ?? 0 }}">
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <div class="flex items-start gap-3 min-w-0">
                                    @if($skill->user?->profile?->profile_picture)
                                        <img src="{{ asset('storage/' . $skill->user->profile->profile_picture) }}"
                                             class="w-12 h-12 rounded-full object-cover shrink-0"
                                             alt="{{ $skill->user->name ?? 'User' }}">
                                    @else
                                        <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl shrink-0 font-bold">
                                            {{ strtoupper(substr($skill->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-lg text-gray-900 dark:text-white break-words">{{ $skill->skill_name }}</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-300">{{ $skill->user->name ?? 'Unknown' }}</p>
                                        <div class="flex flex-wrap gap-2 mt-1">
                                            <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700">{{ $category }}</span>
                                            <span class="px-2 py-1 rounded-full text-xs bg-indigo-100 text-indigo-700">{{ $skill->skill_level }}</span>
                                            <span class="px-2 py-1 rounded-full text-xs bg-emerald-100 text-emerald-700">{{ $typeBadge }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if($isMine)
                                    <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">My Skill</span>
                                @endif
                            </div>

                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 flex-1">
                                {{ $skill->description ?: 'No description yet. Add context so peers can discover this skill faster.' }}
                            </p>

                            @if($skillType === \App\Models\Skill::TYPE_EXCHANGE && !empty($skill->exchange_skill_needed))
                                <p class="text-xs text-indigo-700 dark:text-indigo-300 mb-2">Exchange: {{ $skill->skill_name }} for {{ $skill->exchange_skill_needed }}</p>
                            @endif

                            @if($skillType === \App\Models\Skill::TYPE_TEAMWORK && !empty($skill->collaboration_goal))
                                <p class="text-xs text-purple-700 dark:text-purple-300 mb-2">Teamwork: {{ $skill->collaboration_goal }}</p>
                            @endif

                            <div class="grid grid-cols-2 gap-2 mb-4 text-xs">
                                <div class="rounded-lg bg-gray-50 dark:bg-gray-700 p-2">
                                    <p class="text-gray-500 dark:text-gray-300">Offering</p>
                                    <p class="font-bold text-gray-900 dark:text-white">{{ $offerCount }}</p>
                                </div>
                                <div class="rounded-lg bg-gray-50 dark:bg-gray-700 p-2">
                                    <p class="text-gray-500 dark:text-gray-300">Want to Learn</p>
                                    <p class="font-bold text-gray-900 dark:text-white">{{ $learnCount }}</p>
                                </div>
                            </div>

                            @if($isMine)
                                <div class="grid grid-cols-3 gap-2 text-xs">
                                    <a href="{{ route('skills.edit', $skill) }}"
                                       class="px-2 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 text-center">Edit</a>
                                    <form method="POST" action="{{ route('skills.destroy', $skill) }}" onsubmit="return confirm('Delete this skill?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full px-2 py-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200">Delete</button>
                                    </form>
                                    <a href="{{ route('skills.matches', $skill) }}"
                                       class="px-2 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-center">{{ $actionLabel }}</a>
                                </div>
                            @else
                                <div class="grid grid-cols-3 gap-2 text-xs">
                                    <a href="{{ route('messages.index', ['user' => $skill->user_id]) }}"
                                       class="px-2 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-center">Message</a>
                                    <form method="POST" action="{{ route('reports.store') }}">
                                        @csrf
                                        <input type="hidden" name="reported_user_id" value="{{ $skill->user_id }}">
                                        <input type="hidden" name="reason" value="Inappropriate Content">
                                        <input type="hidden" name="description" value="Skill listing report from Skills directory.">
                                        <button type="submit" class="w-full px-2 py-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200">Report</button>
                                    </form>
                                    <a href="{{ route('messages.index', ['user' => $skill->user_id, 'draft' => 'Hi ' . ($skill->user->name ?? '') . ', I would like to request a skill exchange for ' . $skill->skill_name . '.']) }}"
                                       class="px-2 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 text-center">Request Skill Exchange</a>
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>

                <div id="skillsEmptyFilter" class="hidden rounded-2xl bg-white dark:bg-gray-800 p-10 shadow text-center">
                    <div class="text-5xl mb-3">?</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">No skills match your filters</h3>
                    <p class="text-gray-600 dark:text-gray-300 mt-2">Try changing search, category, level, or sorting options.</p>
                </div>
            @else
                <div class="rounded-2xl bg-white dark:bg-gray-800 p-10 shadow text-center">
                    <div class="text-5xl mb-3">!</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">No skills shared yet</h3>
                    <p class="text-gray-600 dark:text-gray-300 mt-2">Be the first to add a skill and start the learning network.</p>
                </div>
            @endif
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">My Skills - I Teach</h3>

                @if($myTeachingSkills->count())
                    <div class="flex flex-wrap gap-2">
                        @foreach($myTeachingSkills as $skill)
                            <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-sm">{{ $skill->skill_name }}</span>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-700 p-4 text-gray-600 dark:text-gray-300 text-sm">
                        No teaching skills tagged yet. Add availability text like "Teach on weekends".
                    </div>
                @endif

                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 mt-6">My Skills - I Want to Learn</h3>

                @if($myLearningSkillNames->count())
                    <div class="flex flex-wrap gap-2">
                        @foreach($myLearningSkillNames as $skillName)
                            <span class="px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-sm">{{ $skillName }}</span>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-700 p-4 text-gray-600 dark:text-gray-300 text-sm">
                        No learning skills tagged yet. Add availability text like "Looking to learn".
                    </div>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Recommended Skills</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Based on your course, interests, and current collaboration context.</p>

                @if($recommended->count())
                    <div class="space-y-3">
                        @foreach($recommended as $skill)
                            <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-3">
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $skill->skill_name }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $skill->description ?: 'Explore this skill with peers in ConnectU.' }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 p-4 text-blue-700 dark:text-blue-300 text-sm">
                        We need a little more activity to generate personalized recommendations. Add more profile interests or skill entries.
                    </div>
                @endif
            </div>
        </section>
    </div>

    <div id="skillsToast" class="hidden fixed bottom-6 right-6 z-50 max-w-sm rounded-xl bg-gray-900 text-white px-4 py-3 shadow-xl text-sm"></div>

    <script>
        const skeleton = document.getElementById('skillsSkeleton');
        const content = document.getElementById('skillsContent');
        setTimeout(() => {
            skeleton.classList.add('hidden');
            content.classList.remove('hidden');
        }, 450);

        const searchInput = document.getElementById('skillsSearch');
        const categoryFilter = document.getElementById('skillsCategory');
        const levelFilter = document.getElementById('skillsLevel');
        const sortFilter = document.getElementById('skillsSort');
        const grid = document.getElementById('skillsGrid');
        const resultCount = document.getElementById('skillsResultCount');
        const emptyFilter = document.getElementById('skillsEmptyFilter');

        function normalize(v) {
            return (v || '').toString().toLowerCase().trim();
        }

        function applyFilters() {
            if (!grid) {
                return;
            }

            const cards = Array.from(grid.querySelectorAll('.skill-card'));
            const search = normalize(searchInput?.value);
            const category = normalize(categoryFilter?.value || 'all');
            const level = normalize(levelFilter?.value || 'all');
            const sortBy = normalize(sortFilter?.value || 'newest');

            let visible = cards.filter((card) => {
                const name = normalize(card.dataset.name);
                const desc = normalize(card.dataset.description);
                const cardCategory = normalize(card.dataset.category);
                const cardLevel = normalize(card.dataset.level);

                const searchMatch = !search || name.includes(search) || desc.includes(search);
                const categoryMatch = category === 'all' || cardCategory === category;
                const levelMatch = level === 'all' || normalize(cardLevel) === level;

                const show = searchMatch && categoryMatch && levelMatch;
                card.classList.toggle('hidden', !show);
                return show;
            });

            visible.sort((a, b) => {
                if (sortBy === 'alphabetical') {
                    return normalize(a.dataset.name).localeCompare(normalize(b.dataset.name));
                }

                if (sortBy === 'popularity') {
                    return Number(b.dataset.offers || 0) - Number(a.dataset.offers || 0);
                }

                return Number(b.dataset.created || 0) - Number(a.dataset.created || 0);
            });

            visible.forEach((card) => grid.appendChild(card));

            if (resultCount) {
                resultCount.textContent = `${visible.length} skill${visible.length === 1 ? '' : 's'} found`;
            }

            if (emptyFilter) {
                emptyFilter.classList.toggle('hidden', visible.length > 0);
            }
        }

        [searchInput, categoryFilter, levelFilter, sortFilter].forEach((el) => {
            if (el) {
                el.addEventListener('input', applyFilters);
                el.addEventListener('change', applyFilters);
            }
        });

        function showToast(message) {
            const toast = document.getElementById('skillsToast');
            if (!toast) return;
            toast.textContent = message;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 2400);
        }

        applyFilters();
    </script>

</x-layouts.app>
