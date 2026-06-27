<x-layouts.app :title="'Settings'" :pageTitle="'Settings'">

    @php
        $accent = auth()->user()->accentColor();
    @endphp

    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Settings</h1>
        <p class="text-gray-600 dark:text-gray-300 mb-6">Customize your ConnectU appearance.</p>

        @if(session('success'))
            <div class="mb-6 rounded-xl bg-green-100 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-8">
            <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block font-semibold text-gray-700 dark:text-gray-200 mb-2">Theme Mode</label>

                    <select name="theme_mode"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-300 rounded-xl px-4 py-3">
                        <option value="light" {{ auth()->user()->theme_mode === 'light' ? 'selected' : '' }}>
                            Light Mode
                        </option>
                        <option value="dark" {{ auth()->user()->theme_mode === 'dark' ? 'selected' : '' }}>
                            Dark Mode
                        </option>
                        <option value="system" {{ auth()->user()->theme_mode === 'system' ? 'selected' : '' }}>
                            System Default
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 dark:text-gray-200 mb-2">Accent Color</label>

                    <select name="accent_color"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-300 rounded-xl px-4 py-3">
                        <option value="blue" {{ auth()->user()->accent_color === 'blue' ? 'selected' : '' }}>
                            Blue
                        </option>
                        <option value="purple" {{ auth()->user()->accent_color === 'purple' ? 'selected' : '' }}>
                            Purple
                        </option>
                        <option value="green" {{ auth()->user()->accent_color === 'green' ? 'selected' : '' }}>
                            Green
                        </option>
                        <option value="pink" {{ auth()->user()->accent_color === 'pink' ? 'selected' : '' }}>
                            Pink
                        </option>
                    </select>
                </div>

                <button type="submit"
                        class="px-6 py-3 bg-{{ $accent }}-600 text-white rounded-xl hover:bg-{{ $accent }}-700">
                    Save Settings
                </button>
            </form>
        </div>
    </div>

</x-layouts.app>
