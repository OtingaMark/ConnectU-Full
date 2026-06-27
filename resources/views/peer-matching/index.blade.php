<x-layouts.app :title="'Peer Matching'" :pageTitle="'Peer Matching'">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Recommended Study Partners
        </h1>

        <p class="text-gray-600 dark:text-gray-300 mt-2">
            Connect with students who share similar interests,
            skills and learning goals.
        </p>

        <a href="{{ route('profile.edit') }}"
           class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Edit My Profile
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        @forelse($matches as $match)

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">

                <div class="flex justify-between items-start">

                    <div>
                        @if($match->user->profile?->profile_picture)
                            <img src="{{ asset('storage/' . $match->user->profile->profile_picture) }}"
                                 onclick="openImageViewer(this.src)"
                                 class="w-16 h-16 rounded-full object-cover mb-3 cursor-pointer">
                        @else
                            <div class="w-16 h-16 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xl mb-3">
                                {{ strtoupper(substr($match->user->name, 0, 1)) }}
                            </div>
                        @endif

                        <h2 class="text-xl font-bold">
                            {{ $match->user->name }}
                        </h2>

                        <p class="text-gray-500">
                            {{ $match->course }}
                        </p>
                    </div>

                    <div class="bg-blue-100 text-blue-700 px-4 py-2 rounded-xl font-bold">
                        {{ $match->match_score }}%
                    </div>

                </div>

                <p class="mt-4 text-gray-700 dark:text-gray-200">
                    {{ $match->bio }}
                </p>

                <div class="mt-4">
                    <p>
                        <strong>Availability:</strong>
                        {{ $match->availability }}
                    </p>
                </div>

                <div class="mt-4">
                    <p>
                        <strong>Shared Interests:</strong>
                        {{ $match->shared_interests ?: 'None' }}
                    </p>
                </div>

                <div class="mt-2">
                    <p>
                        <strong>Shared Skills:</strong>
                        {{ $match->shared_skills ?: 'None' }}
                    </p>
                </div>

                <div class="mt-6 flex gap-3">

                    @if(!$match->connection)

                        <form method="POST"
                              action="{{ route('connections.send', $match->user->id) }}">
                            @csrf

                            <button
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                Connect
                            </button>
                        </form>

                    @elseif($match->connection->status === 'pending')

                        <span
                            class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg">
                            Request Pending
                        </span>

                    @elseif($match->connection->status === 'accepted')

                        <a href="{{ route('messages.index', ['user' => $match->user->id]) }}"
                           class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            Message
                        </a>

                    @elseif($match->connection->status === 'declined')

                        <span
                            class="px-4 py-2 bg-red-100 text-red-700 rounded-lg">
                            Request Declined
                        </span>

                    @endif

                </div>

            </div>

        @empty

            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow">
                <p>
                    No matching peers found yet.
                    Try updating your profile with more interests and skills.
                </p>
            </div>

        @endforelse

    </div>

</x-layouts.app>