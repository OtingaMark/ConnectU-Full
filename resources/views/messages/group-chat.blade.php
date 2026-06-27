<x-layouts.app :title="$studyGroup->group_name" :pageTitle="'Group Chat'">

    <div class="bg-white rounded-2xl shadow p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ $studyGroup->group_name }}</h1>

        @foreach($messages as $message)
            <div class="mb-4 rounded-xl border border-gray-100 p-4">
                <p class="font-semibold text-gray-900">{{ $message->user->name }}</p>
                <p class="mt-1 text-gray-700">{{ $message->message }}</p>
            </div>
        @endforeach
    </div>

</x-layouts.app>
