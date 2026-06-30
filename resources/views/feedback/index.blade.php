<x-layouts.app :title="'Feedback'" :pageTitle="'Feedback Hub'">

    @php
        $received = $feedbackReceived ?? collect();
        $given = $feedbackGiven ?? collect();
        $appGiven = $appFeedbackGiven ?? collect();
        $allUsers = $users ?? collect();

        $avgReceived = round((float) $received->avg('rating'), 1);
        $receivedCount = $received->count();
        $givenCount = $given->count();

        $distribution = collect([1, 2, 3, 4, 5])->mapWithKeys(function ($r) use ($received) {
            return [$r => $received->where('rating', $r)->count()];
        });

        $requestsCount = $appGiven->count();
        $reportedCount = 0;
    @endphp

    <div id="feedbackSkeleton" class="space-y-6">
        <div class="h-24 rounded-2xl bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="h-24 rounded-2xl bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
            <div class="h-24 rounded-2xl bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
            <div class="h-24 rounded-2xl bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
            <div class="h-24 rounded-2xl bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
        </div>
        <div class="h-72 rounded-2xl bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
    </div>

    <div id="feedbackContent" class="hidden space-y-8">
        <section class="rounded-2xl bg-gradient-to-r from-amber-600 to-orange-500 text-white p-8 shadow-lg">
            <h1 class="text-3xl md:text-4xl font-bold">Feedback and Reputation Center</h1>
            <p class="mt-2 text-amber-50 max-w-3xl">
                Build trust through peer ratings, meaningful comments, and transparent collaboration history.
            </p>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow">
                <p class="text-sm text-gray-500 dark:text-gray-300">Average Rating</p>
                <p class="text-3xl font-bold text-amber-700 dark:text-amber-400">{{ $receivedCount ? $avgReceived : '0.0' }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-300">out of 5.0</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow">
                <p class="text-sm text-gray-500 dark:text-gray-300">Feedback Received</p>
                <p class="text-3xl font-bold text-blue-700 dark:text-blue-400">{{ $receivedCount }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow">
                <p class="text-sm text-gray-500 dark:text-gray-300">Feedback Given</p>
                <p class="text-3xl font-bold text-emerald-700 dark:text-emerald-400">{{ $givenCount }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow">
                <p class="text-sm text-gray-500 dark:text-gray-300">App Feedback Submitted</p>
                <p class="text-3xl font-bold text-indigo-700 dark:text-indigo-400">{{ $requestsCount }}</p>
            </div>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Give Feedback</h2>
                    <span class="text-xs bg-amber-100 text-amber-700 px-3 py-1 rounded-full">Existing backend route active</span>
                </div>

                <div class="flex flex-wrap gap-2 mb-4 text-sm">
                    <button type="button" id="peerModeBtn" class="px-3 py-2 rounded-lg bg-amber-600 text-white">Peer Feedback</button>
                    <button type="button" id="appModeBtn" class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">App Feedback</button>
                </div>

                <form action="{{ route('feedback.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @csrf
                    <input type="hidden" name="feedback_type" id="feedbackType" value="peer">

                    <div id="peerReceiverWrap" class="md:col-span-2 space-y-2">
                        <input type="text" id="peerSearch" placeholder="Search active peer by name or email"
                               class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl px-4 py-3">

                        <select name="receiver_id" id="peerReceiver" required class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl px-4 py-3">
                            <option value="">Select peer</option>
                            @foreach($allUsers as $user)
                                <option value="{{ $user->id }}" data-key="{{ strtolower($user->name . ' ' . $user->email) }}" @selected(old('receiver_id') == $user->id)>{{ $user->name }} - {{ $user->email }}</option>
                            @endforeach
                        </select>
                    </div>

                    <select name="rating" id="ratingSelect" required class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl px-4 py-3">
                        <option value="">Select rating</option>
                        <option value="1" @selected(old('rating') == '1')>1 - Poor</option>
                        <option value="2" @selected(old('rating') == '2')>2 - Fair</option>
                        <option value="3" @selected(old('rating') == '3')>3 - Good</option>
                        <option value="4" @selected(old('rating') == '4')>4 - Very Good</option>
                        <option value="5" @selected(old('rating') == '5')>5 - Excellent</option>
                    </select>

                    <textarea name="comment" rows="4" placeholder="Share clear and constructive feedback"
                              class="md:col-span-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl px-4 py-3">{{ old('comment') }}</textarea>

                    <div class="md:col-span-2 flex justify-end">
                        <button class="bg-amber-600 text-white rounded-xl px-6 py-3 hover:bg-amber-700 transition">Submit Feedback</button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Rating Breakdown</h2>

                <div class="space-y-3">
                    @foreach([5, 4, 3, 2, 1] as $score)
                        @php
                            $count = $distribution[$score] ?? 0;
                            $pct = $receivedCount > 0 ? (int) round(($count / $receivedCount) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm text-gray-700 dark:text-gray-200 mb-1">
                                <span>{{ $score }} stars</span>
                                <span>{{ $count }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                <div class="h-full bg-amber-500" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 rounded-xl bg-gray-50 dark:bg-gray-700 p-4 text-sm text-gray-600 dark:text-gray-300">
                    Tip: detailed, respectful comments improve collaboration quality and trust.
                </div>
            </div>
        </section>

        <section class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Feedback Streams</h2>
                <div class="flex flex-wrap gap-2 text-sm">
                        <button type="button" class="feedback-tab px-3 py-2 rounded-lg bg-amber-600 text-white" data-tab="received">Peer Received</button>
                        <button type="button" class="feedback-tab px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200" data-tab="given">Peer Given</button>
                        <button type="button" class="feedback-tab px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200" data-tab="requests">App Feedback</button>
                    <button type="button" class="feedback-tab px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200" data-tab="reported">Reported</button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <input id="feedbackSearch" type="text" placeholder="Search by user name or comment"
                       class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl px-4 py-3">

                <select id="feedbackRatingFilter" class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl px-4 py-3">
                    <option value="all">All ratings</option>
                    <option value="5">5 stars</option>
                    <option value="4">4 stars</option>
                    <option value="3">3 stars</option>
                    <option value="2">2 stars</option>
                    <option value="1">1 star</option>
                </select>
            </div>

            <div class="feedback-panel" data-panel="received">
                @if($receivedCount)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="receivedGrid">
                        @foreach($received as $feedback)
                            <article class="feedback-card rounded-xl border border-gray-200 dark:border-gray-700 p-4"
                                     data-name="{{ strtolower($feedback->giver->name ?? 'unknown') }}"
                                     data-comment="{{ strtolower($feedback->comment ?? '') }}"
                                     data-rating="{{ $feedback->rating }}">
                                <div class="flex justify-between items-start gap-3">
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">Anonymous peer feedback</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-300">{{ $feedback->created_at }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-700">{{ $feedback->rating }}/5</span>
                                </div>

                                <p class="mt-3 text-sm text-gray-700 dark:text-gray-200">{{ $feedback->comment ?: 'No comment provided.' }}</p>

                                <div class="mt-4 flex flex-wrap gap-2 text-xs">
                                    <button type="button" onclick="comingSoonFeedback('Mark helpful will be enabled when feedback reactions are implemented.')"
                                            class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">Helpful</button>
                                    <button type="button" onclick="comingSoonFeedback('Reply to feedback will be enabled in a backend update.')"
                                            class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">Reply</button>
                                    <form method="POST" action="{{ route('reports.store') }}">
                                        @csrf
                                        <input type="hidden" name="feedback_id" value="{{ $feedback->id }}">
                                        <input type="hidden" name="reason" value="Inappropriate Feedback">
                                        <input type="hidden" name="description" value="Reported from peer feedback panel.">
                                        <button type="submit" class="px-3 py-2 rounded-lg bg-red-50 text-red-700 hover:bg-red-100">Report</button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-700 p-8 text-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">No feedback received yet</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Collaborate in groups and peer chats to start receiving ratings.</p>
                    </div>
                @endif
            </div>

            <div class="feedback-panel hidden" data-panel="given">
                @if($givenCount)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="givenGrid">
                        @foreach($given as $feedback)
                            <article class="feedback-card rounded-xl border border-gray-200 dark:border-gray-700 p-4"
                                     data-name="{{ strtolower($feedback->receiver->name ?? 'unknown') }}"
                                     data-comment="{{ strtolower($feedback->comment ?? '') }}"
                                     data-rating="{{ $feedback->rating }}">
                                <div class="flex justify-between items-start gap-3">
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">To {{ $feedback->receiver->name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-300">{{ $feedback->created_at }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700">{{ $feedback->rating }}/5</span>
                                </div>

                                <p class="mt-3 text-sm text-gray-700 dark:text-gray-200">{{ $feedback->comment ?: 'No comment provided.' }}</p>

                                <div class="mt-4 flex flex-wrap gap-2 text-xs">
                                    <button type="button" onclick="comingSoonFeedback('Edit window and revision history require backend support.')"
                                            class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">Edit</button>
                                    <button type="button" onclick="comingSoonFeedback('Copy-link support is not wired yet.')"
                                            class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">Copy Link</button>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-700 p-8 text-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">No feedback given yet</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Use the form above to submit your first peer review.</p>
                    </div>
                @endif
            </div>

            <div class="feedback-panel hidden" data-panel="requests">
                @if($appGiven->count())
                    <div class="space-y-3">
                        @foreach($appGiven as $feedback)
                            <article class="feedback-card rounded-xl border border-gray-200 dark:border-gray-700 p-4"
                                     data-name="connectu"
                                     data-comment="{{ strtolower($feedback->comment ?? '') }}"
                                     data-rating="{{ $feedback->rating ?? '' }}">
                                <div class="flex justify-between items-start gap-3">
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">App Feedback to Admin</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-300">{{ $feedback->created_at }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700">{{ $feedback->rating ? ($feedback->rating . '/5') : 'No rating' }}</span>
                                </div>

                                <p class="mt-3 text-sm text-gray-700 dark:text-gray-200">{{ $feedback->comment ?: 'No comment provided.' }}</p>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl bg-indigo-50 dark:bg-indigo-900/20 p-8 text-center">
                        <h3 class="text-lg font-semibold text-indigo-900 dark:text-indigo-300">No app feedback submitted yet</h3>
                        <p class="text-sm text-indigo-700 dark:text-indigo-300 mt-1">Use App Feedback mode in the form above to share bugs or ideas with admins.</p>
                    </div>
                @endif
            </div>

            <div class="feedback-panel hidden" data-panel="reported">
                <div class="rounded-xl bg-red-50 dark:bg-red-900/20 p-8 text-center">
                    <h3 class="text-lg font-semibold text-red-900 dark:text-red-300">Reported Feedback</h3>
                    <p class="text-sm text-red-700 dark:text-red-300 mt-1">{{ $reportedCount }} reported items. Detailed moderation view is pending backend support.</p>
                </div>
            </div>
        </section>
    </div>

    <div id="feedbackToast" class="hidden fixed bottom-6 right-6 z-50 max-w-sm rounded-xl bg-gray-900 text-white px-4 py-3 shadow-xl text-sm"></div>

    <script>
        const feedbackSkeleton = document.getElementById('feedbackSkeleton');
        const feedbackContent = document.getElementById('feedbackContent');

        setTimeout(() => {
            feedbackSkeleton.classList.add('hidden');
            feedbackContent.classList.remove('hidden');
        }, 450);

        const tabButtons = Array.from(document.querySelectorAll('.feedback-tab'));
        const panels = Array.from(document.querySelectorAll('.feedback-panel'));
        const searchInput = document.getElementById('feedbackSearch');
        const ratingFilter = document.getElementById('feedbackRatingFilter');
        const peerModeBtn = document.getElementById('peerModeBtn');
        const appModeBtn = document.getElementById('appModeBtn');
        const feedbackType = document.getElementById('feedbackType');
        const peerReceiverWrap = document.getElementById('peerReceiverWrap');
        const peerReceiver = document.getElementById('peerReceiver');
        const ratingSelect = document.getElementById('ratingSelect');
        const peerSearch = document.getElementById('peerSearch');

        function setSubmitMode(mode) {
            const peerMode = mode === 'peer';
            feedbackType.value = peerMode ? 'peer' : 'app';

            peerModeBtn.classList.toggle('bg-amber-600', peerMode);
            peerModeBtn.classList.toggle('text-white', peerMode);
            appModeBtn.classList.toggle('bg-amber-600', !peerMode);
            appModeBtn.classList.toggle('text-white', !peerMode);

            peerReceiverWrap.classList.toggle('hidden', !peerMode);
            peerReceiver.required = peerMode;
            ratingSelect.required = peerMode;

            if (!peerMode) {
                peerReceiver.value = '';
                ratingSelect.value = '';
            }
        }

        function setTab(tab) {
            tabButtons.forEach((btn) => {
                const active = btn.dataset.tab === tab;
                btn.classList.toggle('bg-amber-600', active);
                btn.classList.toggle('text-white', active);
                btn.classList.toggle('bg-gray-100', !active);
                btn.classList.toggle('dark:bg-gray-700', !active);
                btn.classList.toggle('text-gray-700', !active);
                btn.classList.toggle('dark:text-gray-200', !active);
            });

            panels.forEach((panel) => {
                panel.classList.toggle('hidden', panel.dataset.panel !== tab);
            });

            applyFeedbackFilters();
        }

        function normalize(v) {
            return (v || '').toString().toLowerCase().trim();
        }

        function applyFeedbackFilters() {
            const visiblePanel = panels.find((panel) => !panel.classList.contains('hidden'));
            if (!visiblePanel) {
                return;
            }

            const cards = Array.from(visiblePanel.querySelectorAll('.feedback-card'));
            const search = normalize(searchInput?.value);
            const rating = normalize(ratingFilter?.value || 'all');

            cards.forEach((card) => {
                const name = normalize(card.dataset.name);
                const comment = normalize(card.dataset.comment);
                const cardRating = normalize(card.dataset.rating);

                const searchMatch = !search || name.includes(search) || comment.includes(search);
                const ratingMatch = rating === 'all' || cardRating === rating;

                card.classList.toggle('hidden', !(searchMatch && ratingMatch));
            });
        }

        tabButtons.forEach((btn) => {
            btn.addEventListener('click', () => setTab(btn.dataset.tab));
        });

        [searchInput, ratingFilter].forEach((el) => {
            if (el) {
                el.addEventListener('input', applyFeedbackFilters);
                el.addEventListener('change', applyFeedbackFilters);
            }
        });

        function applyPeerSearch() {
            const query = normalize(peerSearch?.value);
            const options = Array.from(peerReceiver.options);

            options.forEach((opt, idx) => {
                if (idx === 0) {
                    opt.hidden = false;
                    return;
                }

                const key = normalize(opt.dataset.key || opt.textContent);
                opt.hidden = query && !key.includes(query);
            });
        }

        if (peerSearch) {
            peerSearch.addEventListener('input', applyPeerSearch);
        }

        if (peerModeBtn && appModeBtn) {
            peerModeBtn.addEventListener('click', () => setSubmitMode('peer'));
            appModeBtn.addEventListener('click', () => setSubmitMode('app'));
        }

        function showFeedbackToast(message) {
            const toast = document.getElementById('feedbackToast');
            if (!toast) return;
            toast.textContent = message;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 2400);
        }

        function comingSoonFeedback(message) {
            showFeedbackToast(message || 'This feature will be enabled in a future backend update.');
        }

        setSubmitMode('peer');
        setTab('received');
        applyPeerSearch();
    </script>

</x-layouts.app>