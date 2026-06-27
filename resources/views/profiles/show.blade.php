<x-layouts.app :title="$user->name" :pageTitle="'User Profile'">

    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow p-6">
        @if($user->profile?->profile_picture)
            <img src="{{ asset('storage/' . $user->profile->profile_picture) }}"
                 onclick="openImageViewer(this.src)"
                 class="w-24 h-24 rounded-full object-cover mb-4 cursor-pointer">
        @else
            <div class="w-24 h-24 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-3xl mb-4">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
        @endif

        <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
        <p class="text-gray-500 mt-1">{{ $user->email }}</p>

        <div class="mt-6 space-y-3 text-gray-800">
            <p><strong>Course:</strong> {{ $user->profile->course ?? 'Not added' }}</p>
            <p><strong>Bio:</strong> {{ $user->profile->bio ?? 'Not added' }}</p>
            <p><strong>Interests:</strong> {{ $user->profile->interests ?? 'Not added' }}</p>
            <p><strong>Skills:</strong> {{ $user->profile->skills ?? 'Not added' }}</p>
            <p><strong>Availability:</strong> {{ $user->profile->availability ?? 'Not added' }}</p>
        </div>

        @if(auth()->id() !== $user->id)
            <div class="mt-6">
                @if(!$connection)
                    <form method="POST" action="{{ route('connections.send', $user->id) }}">
                        @csrf
                        <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Connect
                        </button>
                    </form>
                @elseif($connection->status === 'pending')
                    <span class="inline-block px-4 py-2 rounded-lg bg-yellow-100 text-yellow-700">
                        Request Pending
                    </span>
                @elseif($connection->status === 'accepted')
                    <a href="{{ route('messages.index', ['user' => $user->id]) }}"
                       class="inline-block px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Message
                    </a>
                @endif

                <details class="mt-4">
                    <summary class="cursor-pointer text-sm text-red-600 font-semibold">Report User</summary>

                    <form method="POST" action="{{ route('reports.store') }}" class="mt-3 space-y-2">
                        @csrf
                        <input type="hidden" name="reported_user_id" value="{{ $user->id }}">

                        <select name="reason" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">Select reason</option>
                            <option value="Harassment">Harassment</option>
                            <option value="Spam">Spam</option>
                            <option value="Impersonation">Impersonation</option>
                            <option value="Other">Other</option>
                        </select>

                        <textarea name="description" rows="3" placeholder="Describe the issue" class="w-full border border-gray-300 rounded-lg px-3 py-2"></textarea>

                        <button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Submit Report
                        </button>
                    </form>
                </details>
            </div>
        @endif
    </div>

</x-layouts.app>
