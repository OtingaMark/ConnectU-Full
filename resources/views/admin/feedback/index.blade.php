<x-layouts.app :title="'Admin - Feedback Management'" :pageTitle="'Feedback Management'">

    <div class="max-w-7xl mx-auto p-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Feedback Management</h1>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b dark:border-gray-700">
                        <th class="py-3">Type</th>
                        <th class="py-3">From</th>
                        <th>To</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allFeedback as $item)
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-3">
                                @if(($item->feedback_type ?? 'peer') === 'app')
                                    <span class="px-2 py-1 rounded-full text-xs bg-indigo-100 text-indigo-700">App Feedback</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs bg-amber-100 text-amber-700">Peer Feedback</span>
                                @endif
                            </td>
                            <td class="py-3">{{ $item->giver->name ?? 'Unknown' }}</td>
                            <td>
                                @if(($item->feedback_type ?? 'peer') === 'app')
                                    Admin Team
                                @else
                                    {{ $item->receiver->name ?? 'Unknown' }}
                                @endif
                            </td>
                            <td>{{ $item->rating ?? 'N/A' }}</td>
                            <td>{{ $item->comment ?: 'No comment' }}</td>
                            <td>{{ optional($item->created_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">{{ $allFeedback->links() }}</div>
        </div>
    </div>

</x-layouts.app>
