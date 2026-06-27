<x-layouts.app :title="'Admin - Platform Analytics'" :pageTitle="'Platform Analytics'">

    <div class="max-w-7xl mx-auto p-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Platform Analytics</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow"><h3>Users</h3><p class="text-4xl font-bold">{{ $users }}</p></div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow"><h3>Groups</h3><p class="text-4xl font-bold">{{ $groups }}</p></div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow"><h3>Skills</h3><p class="text-4xl font-bold">{{ $skills }}</p></div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow"><h3>Feedback</h3><p class="text-4xl font-bold">{{ $feedback }}</p></div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow"><h3>Messages</h3><p class="text-4xl font-bold">{{ $messages }}</p></div>
        </div>
    </div>

</x-layouts.app>
