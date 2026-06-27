<!DOCTYPE html>
<html lang="en"
      class="
      @auth
          {{ auth()->user()->theme_mode === 'dark' ? 'dark' : '' }}
      @endauth
      ">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'ConnectU' }}</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white">

@php
    $accent = auth()->check() ? auth()->user()->accentColor() : 'blue';
@endphp

<div class="min-h-screen flex">

    <aside id="sidebar" class="sidebar w-64 bg-white dark:bg-gray-800 shadow-lg transition-all duration-300 h-screen flex flex-col">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="h-11 w-11 shrink-0 rounded-xl bg-{{ $accent }}-600 text-white flex items-center justify-center">
                    <x-app-logo-icon class="h-6 w-6 fill-current text-white" />
                </div>
                <h1 class="text-2xl font-bold text-{{ $accent }}-700 dark:text-{{ $accent }}-400">ConnectU</h1>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-300">Peer Learning Platform</p>
        </div>
<nav class="p-4 space-y-2">
    @if(auth()->check() && strtolower(trim(auth()->user()->role ?? '')) === 'admin')
        <a href="{{ route('admin.dashboard') }}"
           class="block px-4 py-2 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-' . $accent . '-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200' }}">
            Admin Dashboard
        </a>
          <a href="{{ route('admin.users.index') }}"
              class="block px-4 py-2 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-' . $accent . '-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200' }}">
            Users Management
        </a>
          <a href="{{ route('admin.groups.index') }}"
              class="block px-4 py-2 rounded-lg {{ request()->routeIs('admin.groups.*') ? 'bg-' . $accent . '-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200' }}">
            Learning Groups Management
        </a>
          <a href="{{ route('admin.skills.index') }}"
              class="block px-4 py-2 rounded-lg {{ request()->routeIs('admin.skills.*') ? 'bg-' . $accent . '-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200' }}">
            Skills Management
        </a>
          <a href="{{ route('admin.feedback.index') }}"
              class="block px-4 py-2 rounded-lg {{ request()->routeIs('admin.feedback.*') ? 'bg-' . $accent . '-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200' }}">
            Feedback Management
        </a>
          <a href="{{ route('admin.reports.index') }}"
              class="block px-4 py-2 rounded-lg {{ request()->routeIs('admin.reports.*') ? 'bg-' . $accent . '-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200' }}">
            Reports / Moderation
        </a>
          <a href="{{ route('admin.analytics.index') }}"
              class="block px-4 py-2 rounded-lg {{ request()->routeIs('admin.analytics.*') ? 'bg-' . $accent . '-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200' }}">
            Platform Analytics
        </a>
    @else
        <a href="{{ route('dashboard', ['current_team' => auth()->user()->currentTeam->slug ?? 'mark-otingas-team']) }}"
           class="block px-4 py-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-' . $accent . '-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200' }}">
            Dashboard
        </a>

        <a href="{{ route('peer-matching.index') }}"
             class="block px-4 py-2 rounded-lg {{ request()->routeIs('peer-matching.index') ? 'bg-purple-600 text-white' : 'hover:bg-purple-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200' }}">
            Peer Matching
        </a>

        <a href="{{ route('study-groups.index') }}"
             class="block px-4 py-2 rounded-lg {{ request()->routeIs('study-groups.index') ? 'bg-' . $accent . '-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200' }}">
            Learning Groups
        </a>

        <a href="{{ route('users.search') }}"
             class="block px-4 py-2 rounded-lg {{ request()->routeIs('users.search') ? 'bg-indigo-600 text-white' : 'hover:bg-indigo-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200' }}">
            Find People
        </a>

        <a href="{{ route('skills.index') }}"
             class="block px-4 py-2 rounded-lg {{ request()->routeIs('skills.index') ? 'bg-green-600 text-white' : 'hover:bg-green-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200' }}">
            Skills
        </a>

        <a href="{{ route('messages.index') }}"
             class="block px-4 py-2 rounded-lg {{ request()->routeIs('messages.index') ? 'bg-pink-600 text-white' : 'hover:bg-pink-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200' }}">
            Messages
        </a>

        <a href="{{ route('feedback.index') }}"
             class="block px-4 py-2 rounded-lg {{ request()->routeIs('feedback.index') ? 'bg-yellow-600 text-white' : 'hover:bg-yellow-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200' }}">
            Feedback
        </a>
    @endif
</nav>

<div class="mt-auto p-4">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                onclick="return confirm('Are you sure you want to logout?')"
                class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">
            Logout
        </button>
    </form>
</div>
    </aside>

    <main class="flex-1">
        <header class="bg-white dark:bg-gray-800 shadow-sm px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <button id="toggleSidebar"
                    class="px-3 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                    ☰
                </button>

                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $pageTitle ?? 'Dashboard' }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-300">Welcome, {{ auth()->user()->name ?? 'Student' }}</p>
                </div>
            </div>

            <div class="relative">
                <button onclick="document.getElementById('profileMenu').classList.toggle('hidden')">
                    @if(auth()->user()->profile?->profile_picture)
                        <img src="{{ asset('storage/' . auth()->user()->profile->profile_picture) }}"
                             class="w-14 h-14 rounded-full object-cover border-2 border-{{ $accent }}-600">
                    @else
                        <div class="w-14 h-14 rounded-full bg-{{ $accent }}-600 text-white flex items-center justify-center font-bold text-xl">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                </button>

                <div id="profileMenu"
                     class="hidden absolute right-0 mt-3 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                    <a href="{{ route('profile.edit') }}"
                       class="block px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-900 dark:text-gray-100">
                        Edit Profile
                    </a>

                    <a href="{{ route('settings.edit') }}"
                       class="block px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-900 dark:text-gray-100">
                        Settings
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Are you sure you want to logout?')"
                                class="w-full text-left px-4 py-3 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <section class="p-6">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{ $slot }}
        </section>
    </main>

</div>

<div id="imageViewer"
     class="hidden fixed inset-0 bg-black/80 z-[9999] items-center justify-center">
    <button onclick="closeImageViewer()"
            class="absolute top-6 right-8 text-white text-4xl">
        ×
    </button>

    <img id="imageViewerImg"
         src=""
         class="max-w-[90%] max-h-[85vh] rounded-2xl object-contain">
</div>

<script>
document.getElementById('toggleSidebar').addEventListener('click', function () {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('hidden');
});

function openImageViewer(src) {
    document.getElementById('imageViewerImg').src = src;
    document.getElementById('imageViewer').classList.remove('hidden');
    document.getElementById('imageViewer').classList.add('flex');
}

function closeImageViewer() {
    document.getElementById('imageViewer').classList.add('hidden');
    document.getElementById('imageViewer').classList.remove('flex');
}
</script>

</body>
</html>