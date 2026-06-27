<x-layouts.app :title="'Edit Profile'" :pageTitle="'My Profile'">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Edit Your Profile
        </h1>

        <p class="text-gray-600 dark:text-gray-300 mt-2">
            Complete your profile so ConnectU can recommend suitable study partners.
        </p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 lg:col-span-2">

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <label class="block font-semibold text-gray-700 dark:text-gray-200 mb-2">
                        Course
                    </label>

                    <input type="text"
                           name="course"
                           value="{{ old('course', $profile->course ?? '') }}"
                           placeholder="Example: Computer Science"
                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500">

                    @error('course')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 dark:text-gray-200 mb-2">
                        Bio
                    </label>

                    <textarea name="bio"
                              rows="4"
                              placeholder="Write a short description about yourself..."
                              class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500">{{ old('bio', $profile->bio ?? '') }}</textarea>

                    @error('bio')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 dark:text-gray-200 mb-2">
                        Interests
                    </label>

                    <textarea name="interests"
                              rows="3"
                              placeholder="Example: web development, databases, group study"
                              class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500">{{ old('interests', $profile->interests ?? '') }}</textarea>

                    <p class="text-sm text-gray-500 mt-1">
                        Separate each interest using a comma.
                    </p>

                    @error('interests')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 dark:text-gray-200 mb-2">
                        Skills
                    </label>

                    <textarea name="skills"
                              rows="3"
                              placeholder="Example: PHP, Laravel, MySQL"
                              class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500">{{ old('skills', $profile->skills ?? '') }}</textarea>

                    <p class="text-sm text-gray-500 mt-1">
                        Separate each skill using a comma.
                    </p>

                    @error('skills')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 dark:text-gray-200 mb-2">
                        Availability
                    </label>

                    <input type="text"
                           name="availability"
                           value="{{ old('availability', $profile->availability ?? '') }}"
                           placeholder="Example: evening, weekends, afternoon"
                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500">

                    @error('availability')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block font-semibold mb-2">Profile Picture</label>

                    @if(auth()->user()->profile?->profile_picture)
                        <img src="{{ asset('storage/' . auth()->user()->profile->profile_picture) }}"
                             class="w-24 h-24 rounded-full object-cover mb-3">
                    @endif

                    <input type="file"
                           name="profile_picture"
                           accept="image/*"
                              class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3">

                    @error('profile_picture')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('dashboard', ['current_team' => auth()->user()->currentTeam->slug ?? 'mark-otingas-team']) }}"
                              class="px-5 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-300 dark:hover:bg-gray-600">
                        Cancel
                    </a>

                    <a href="{{ route('peer-matching.index') }}"
                       onclick="return confirm('Proceed to Peer Matching now?');"
                       class="px-5 py-3 bg-indigo-100 text-indigo-700 rounded-xl hover:bg-indigo-200">
                        Proceed to Peer Matching
                    </a>

                    <button type="submit"
                            class="px-5 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700">
                        Save Profile
                    </button>
                </div>
            </form>

        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
            <div class="w-20 h-20 rounded-full bg-blue-600 text-white flex items-center justify-center text-3xl font-bold mb-4">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>

            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                {{ auth()->user()->name }}
            </h2>

            <p class="text-gray-500">
                {{ auth()->user()->email }}
            </p>

            <div class="mt-6 space-y-3 text-sm text-gray-700 dark:text-gray-200">
                <p>
                    <strong>Why this matters:</strong>
                    ConnectU uses your course, interests, skills and availability to recommend suitable study partners.
                </p>

                <p>
                    <strong>Tip:</strong>
                    Use comma-separated words such as Laravel, MySQL, UI design, public speaking.
                </p>
            </div>

            <div class="mt-6 p-4 bg-blue-50 text-blue-700 rounded-xl">
                A complete profile improves your peer matching results.
            </div>
        </div>

    </div>

</x-layouts.app>