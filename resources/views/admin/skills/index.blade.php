<x-layouts.app :title="'Admin - Skills Management'" :pageTitle="'Skills Management'">

    <div class="max-w-7xl mx-auto p-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Skills Management</h1>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b dark:border-gray-700">
                        <th class="py-3">Skill</th>
                        <th>Description</th>
                        <th>Level</th>
                        <th>Owner</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allSkills as $skill)
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-3">{{ $skill->skill_name }}</td>
                            <td>{{ $skill->description ?: 'No description' }}</td>
                            <td>{{ $skill->skill_level }}</td>
                            <td>{{ $skill->user->name ?? 'Unknown' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">{{ $allSkills->links() }}</div>
        </div>
    </div>

</x-layouts.app>
