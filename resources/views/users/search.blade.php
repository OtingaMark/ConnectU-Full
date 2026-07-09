<x-layouts.app :title="'Find People'" :pageTitle="'Find People'">

    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Find People</h1>
    <p class="text-gray-600 dark:text-gray-300 mb-6">
        Search for students by name or email and connect with them directly.
    </p>

    <form method="GET" action="{{ route('users.search') }}" class="mb-8 flex gap-3">
        <input type="text"
               name="search"
               value="{{ $search }}"
               placeholder="Search by name or email..."
             class="flex-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-300 rounded-lg px-4 py-3">

        <button class="px-6 py-3 bg-blue-600 text-white rounded-lg">
            Search
        </button>
    </form>

    @if($search && $users->count() === 0)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-8 text-center">
            <h2 class="text-xl font-bold">No users found</h2>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Try another name or email.</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($users as $user)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
                @if($user->profile?->profile_picture)
                    <img src="{{ asset('storage/' . $user->profile->profile_picture) }}"
                         onclick="openImageViewer(this.src)"
                         class="w-16 h-16 rounded-full object-cover mb-3 cursor-pointer">
                @else
                    <div class="w-16 h-16 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xl mb-3">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif

                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ $user->name }}
                </h2>

                <p class="text-gray-500">{{ $user->email }}</p>

                <div class="mt-4 text-sm text-gray-700 dark:text-gray-200 space-y-1">
                    <p><strong>Course:</strong> {{ $user->profile->course ?? 'No profile yet' }}</p>
                    <p><strong>Interests:</strong> {{ $user->profile->interests ?? 'Not added' }}</p>
                    <p><strong>Skills:</strong> {{ $user->profile->skills ?? 'Not added' }}</p>
                </div>

                <div class="mt-5 flex gap-3">
                    <a href="{{ route('users.show', $user->id) }}"
                              class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg">
                        View Profile
                    </a>

                    <a href="{{ route('messages.index', ['user' => $user->id]) }}"
                       class="px-4 py-2 bg-green-600 text-white rounded-lg">
                        Message
                    </a>

                    @if($user->connection_status === 'pending')
                        <span class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg">
                            Request Pending
                        </span>

                    @elseif($user->connection_status === 'accepted')
                        <span class="px-4 py-2 bg-green-100 text-green-700 rounded-lg">
                            Connected
                        </span>

                    @else
                        <form method="POST"
                              action="{{ route('connections.send', $user->id) }}"
                              onsubmit="return confirm('Send connection request to {{ $user->name }}?');">
                            @csrf
                            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                                Connect
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

</x-layouts.app>
