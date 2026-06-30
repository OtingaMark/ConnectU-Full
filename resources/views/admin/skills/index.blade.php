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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allSkills as $skill)
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-3">{{ $skill->skill_name }}</td>
                            <td>{{ $skill->description ?: 'No description' }}</td>
                            <td>{{ $skill->skill_level }}</td>
                            <td>{{ $skill->user->name ?? 'Unknown' }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.skills.delete', $skill) }}" onsubmit="return confirm('Delete this skill listing?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">{{ $allSkills->links() }}</div>
        </div>
    </div>

</x-layouts.app>
