<x-layouts.app :title="'Dashboard'" :pageTitle="'Dashboard'">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Welcome back, {{ auth()->user()->name }}
        </h1>
        <p class="text-gray-600 dark:text-gray-300 mt-2">
            Manage your learning groups, skills, messages and peer connections from one place.
        </p>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow">
            <p class="text-gray-500">Learning Groups Joined</p>
            <h2 class="text-3xl font-bold text-blue-700">
                {{ \App\Models\GroupMember::where('user_id', auth()->id())->whereHas('studyGroup', function ($query) {
                    $query->where('status', 'active');
                })->count() }}
            </h2>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow">
            <p class="text-gray-500">Skills Shared</p>
            <h2 class="text-3xl font-bold text-green-700">
                {{ \App\Models\Skill::where('user_id', auth()->id())->count() }}
            </h2>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow">
            <p class="text-gray-500">Unread Messages</p>
            <h2 class="text-3xl font-bold text-pink-700">
                {{ \App\Models\Message::where('receiver_id', auth()->id())->where('is_read', false)->count() }}
            </h2>
        </div>
    </div>

    @if($connectionRequests->count())
        <div class="mb-8 rounded-2xl border border-indigo-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Connection Requests</h2>

            @foreach($connectionRequests as $request)
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 py-4 border-t first:border-t-0">
                    <div>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $request->requester->name }}</p>
                        <p class="text-gray-600 dark:text-gray-300">wants to connect with you</p>
                    </div>

                    <div class="flex gap-3">
                        <form method="POST" action="{{ route('connections.accept', $request->id) }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition">
                                Accept
                            </button>
                        </form>

                        <form method="POST" action="{{ route('connections.decline', $request->id) }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                                Decline
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Quick Actions</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <a href="{{ route('peer-matching.index') }}" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow hover:shadow-lg transition">
            <h3 class="text-xl font-bold text-purple-700">Peer Matching</h3>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Find students with similar interests and learning goals.</p>
        </a>

        <a href="{{ route('study-groups.index') }}" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow hover:shadow-lg transition">
            <h3 class="text-xl font-bold text-blue-700">Learning Groups</h3>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Create and join collaboration groups for academics, hobbies, sports, and skills.</p>
        </a>

        <a href="{{ route('skills.index') }}" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow hover:shadow-lg transition">
            <h3 class="text-xl font-bold text-green-700">Skills</h3>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Share skills and learn from other students.</p>
        </a>

        <a href="{{ route('messages.index') }}" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow hover:shadow-lg transition">
            <h3 class="text-xl font-bold text-pink-700">Messages</h3>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Chat privately and share files, images or useful links with peers.</p>
        </a>

        <a href="{{ route('feedback.index') }}" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow hover:shadow-lg transition">
            <h3 class="text-xl font-bold text-yellow-700">Feedback</h3>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Rate and review your learning experiences.</p>
        </a>

    </div>

</x-layouts.app>