<x-layouts.app :title="'Admin Dashboard'" :pageTitle="'Admin Dashboard'">

<div class="max-w-7xl mx-auto p-8">

    <h1 class="text-4xl font-bold mb-8">
        Admin Dashboard
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow">
            <h3>Total Users</h3>
            <p class="text-4xl font-bold">{{ $users }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow">
            <h3>Learning Groups</h3>
            <p class="text-4xl font-bold">{{ $groups }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow">
            <h3>Messages</h3>
            <p class="text-4xl font-bold">{{ $messages }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow">
            <h3>Skills</h3>
            <p class="text-4xl font-bold">{{ $skills }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow">
            <h3>Feedback</h3>
            <p class="text-4xl font-bold">{{ $feedback }}</p>
        </div>

    </div>

    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-100 dark:border-blue-800 p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Privacy Rule</h2>
        <p class="text-gray-700 dark:text-gray-200">
            Admin can view platform-level counts and moderate users, groups, skills, and feedback.
            Admin cannot read private direct message content.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ route('admin.users.index') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 hover:shadow-lg transition">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Users Management</h3>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Edit users, change roles, and remove accounts.</p>
        </a>
        <a href="{{ route('admin.groups.index') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 hover:shadow-lg transition">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Learning Groups Management</h3>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Review groups, group members, and remove inappropriate groups.</p>
        </a>
        <a href="{{ route('admin.skills.index') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 hover:shadow-lg transition">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Skills Management</h3>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Moderate and remove inappropriate skills.</p>
        </a>
        <a href="{{ route('admin.feedback.index') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 hover:shadow-lg transition">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Feedback Management</h3>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Review feedback and moderate harmful content.</p>
        </a>
        <a href="{{ route('admin.reports.index') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 hover:shadow-lg transition">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Reports / Moderation</h3>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Use moderation workflows to keep ConnectU safe.</p>
        </a>
        <a href="{{ route('admin.analytics.index') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 hover:shadow-lg transition">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Platform Analytics</h3>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Track platform growth and engagement trends.</p>
        </a>
    </div>

</div>

</x-layouts.app>
