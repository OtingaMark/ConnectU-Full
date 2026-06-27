<x-layouts.app :title="'Messages'" :pageTitle="'Messages'">

    @php
        $allMessages = $receivedMessages->concat($sentMessages)->sortBy('created_at');
        $activeTab = request('tab', 'all');

        $activeUser = ($activeGroup ?? null)
            ? null
            : ($chatUsers->where('id', $activeUserId ?? request('user'))->first() ?? $chatUsers->first());

        if ($activeUser) {
            $conversationMessages = $allMessages->filter(function ($message) use ($activeUser) {
                return ($message->sender_id == auth()->id() && $message->receiver_id == $activeUser->id)
                    || ($message->receiver_id == auth()->id() && $message->sender_id == $activeUser->id);
            });
        } else {
            $conversationMessages = collect();
        }

        $filteredChatItems = collect($chatItems ?? [])->filter(function ($item) use ($activeTab) {
            if ($activeTab === 'groups') {
                return $item->chat_type === 'group';
            }

            if ($activeTab === 'direct') {
                return $item->chat_type === 'direct';
            }

            return true;
        });
    @endphp

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow overflow-hidden h-[78vh] grid grid-cols-1 lg:grid-cols-3">

        {{-- LEFT SIDEBAR --}}
        <div class="border-r border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex flex-col min-h-0">

            {{-- Search --}}
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 shrink-0">
                <div class="flex items-center gap-3">
                    <input id="chatSearch"
                           type="text"
                           placeholder="Search users or messages..."
                              class="flex-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500">

                    <button type="button"
                            class="bg-blue-600 text-white px-4 py-3 rounded-xl hover:bg-blue-700">
                        ✎
                    </button>
                </div>
            </div>

            {{-- Chat list --}}
            <div class="flex-1 min-h-0 overflow-y-auto">

                {{-- Filters --}}
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 sticky top-0 z-10">
                    <div class="flex items-center gap-4 text-sm font-semibold">
                        <a href="{{ route('messages.index') }}"
                           class="{{ $activeTab === 'all' ? 'text-blue-700 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400' }}">
                            All
                        </a>

                        <a href="{{ route('messages.index', ['tab' => 'groups']) }}"
                           class="{{ $activeTab === 'groups' ? 'text-blue-700 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400' }}">
                            Groups
                        </a>

                        <a href="{{ route('messages.index', ['tab' => 'direct']) }}"
                           class="{{ $activeTab === 'direct' ? 'text-blue-700 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400' }}">
                            Direct
                        </a>
                    </div>
                </div>

                @forelse($filteredChatItems as $item)

                    @if($item->chat_type === 'group')
                        <a href="{{ route('messages.index', ['group' => $item->id, 'tab' => $activeTab]) }}"
                                    class="chat-item flex items-center gap-4 p-4 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 {{ (int)($activeGroupId ?? 0) === (int)$item->id ? 'bg-blue-100 dark:bg-gray-700' : '' }}"
                           data-name="{{ $item->group_name }}">

                            @if($item->group_picture)
                                <img src="{{ asset('storage/' . $item->group_picture) }}"
                                     onclick="event.preventDefault(); openImageViewer(this.src)"
                                     class="w-14 h-14 rounded-full object-cover cursor-pointer">
                            @else
                                <div class="w-14 h-14 rounded-full bg-green-600 text-white flex items-center justify-center">
                                    📚
                                </div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-center">
                                    <h3 class="font-bold text-gray-900 dark:text-white truncate">
                                        {{ $item->group_name }}
                                    </h3>

                                    @if($item->latest_message)
                                        <span class="text-xs text-gray-500 dark:text-gray-300">
                                            {{ $item->latest_message->created_at->format('H:i') }}
                                        </span>
                                    @endif
                                </div>

                                <p class="text-sm text-gray-500 dark:text-gray-300 truncate">
                                    @if($item->latest_message)
                                        {{ $item->latest_message->user->name ?? 'Someone' }}:
                                        {{ $item->latest_message->message ?: 'Attachment or link shared' }}
                                    @else
                                        Start group chat
                                    @endif
                                </p>
                            </div>
                        </a>
                    @else
                        @php
                            $user = $item;
                            $latestMessage = $user->latest_message;
                            $unreadCount = $user->unread_count ?? 0;
                            $isActive = !($activeGroupId ?? null) && $activeUser && $activeUser->id == $user->id;
                        @endphp

                        <a href="{{ route('messages.index', ['user' => $user->id, 'tab' => $activeTab]) }}"
                                    class="chat-item flex items-center gap-4 p-4 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 {{ $isActive ? 'bg-blue-100 dark:bg-gray-700' : '' }}"
                           data-name="{{ $user->name }}">

                            @if($user->profile?->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile->profile_picture) }}"
                                     onclick="event.preventDefault(); openImageViewer(this.src)"
                                     class="w-12 h-12 rounded-full object-cover cursor-pointer">
                            @else
                                <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-center">
                                    <h3 class="font-bold text-gray-900 dark:text-white truncate">
                                        {{ $user->name }}
                                    </h3>

                                    @if($latestMessage)
                                        <span class="text-xs text-gray-500 dark:text-gray-300">
                                            {{ $latestMessage->created_at->format('H:i') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex justify-between items-center gap-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-300 truncate">
                                        @if($latestMessage)
                                            @if($latestMessage->sender_id == auth()->id())
                                                You:
                                            @endif

                                            @if($latestMessage->message)
                                                {{ $latestMessage->message }}
                                            @elseif($latestMessage->file_path)
                                                📎 Attachment
                                            @elseif($latestMessage->resource_link)
                                                🔗 Link shared
                                            @endif
                                        @else
                                            Start a conversation
                                        @endif
                                    </p>

                                    @if($unreadCount > 0)
                                        <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                                            {{ $unreadCount }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endif

                @empty
                    <div class="p-6 text-gray-600 dark:text-gray-300">
                        No chats available.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- RIGHT PANEL --}}
        <div class="lg:col-span-2 flex flex-col bg-blue-50 dark:bg-gray-900 min-h-0">

            @if($activeGroup)

                {{-- Group header --}}
                <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-4 flex justify-between items-center shrink-0">
                    <div>
                        <a href="{{ route('study-groups.show', $activeGroup->id) }}"
                           class="text-xl font-bold text-gray-900 dark:text-white hover:text-blue-700 dark:hover:text-blue-400 hover:underline">
                            {{ $activeGroup->group_name }}
                        </a>

                        <p class="text-sm text-blue-600">
                            Group chat • {{ $activeGroup->members->count() }} members
                        </p>
                    </div>

                    <details class="relative">
                        <summary class="text-gray-600 dark:text-gray-300 text-xl cursor-pointer">⋮</summary>
                        <div class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-xl shadow-lg p-4 z-20">
                            <form method="POST" action="{{ route('reports.store') }}" class="space-y-2">
                                @csrf
                                <input type="hidden" name="study_group_id" value="{{ $activeGroup->id }}">

                                <select name="reason" required class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg px-3 py-2 text-sm">
                                    <option value="">Report group for...</option>
                                    <option value="Inappropriate Content">Inappropriate Content</option>
                                    <option value="Harassment">Harassment</option>
                                    <option value="Spam">Spam</option>
                                    <option value="Other">Other</option>
                                </select>

                                <textarea name="description" rows="3" placeholder="Describe the issue" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg px-3 py-2 text-sm"></textarea>

                                <button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">Submit Report</button>
                            </form>
                        </div>
                    </details>
                </div>

                {{-- Group messages --}}
                <div class="flex-1 min-h-0 overflow-y-auto p-6 space-y-4">
                    @forelse($groupMessages as $message)
                        @if($message->message_type === 'system')
                            <div class="text-center my-3">
                                <span class="inline-block bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm px-4 py-2 rounded-full">
                                    {{ $message->message }}
                                </span>
                            </div>
                        @else
                            <div class="{{ $message->user_id == auth()->id() ? 'text-right' : 'text-left' }}">
                                <div class="inline-block max-w-xl rounded-2xl px-4 py-3 shadow
                                    {{ $message->user_id == auth()->id() ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white' }}">

                                    <div class="text-xs opacity-80 mb-1">
                                        {{ $message->user->name ?? 'Unknown' }}
                                    </div>

                                    @if($message->message)
                                        <div>{{ $message->message }}</div>
                                    @endif

                                    @if($message->resource_link)
                                        <a href="{{ $message->resource_link }}"
                                           target="_blank"
                                           class="block mt-2 underline">
                                            Open shared link
                                        </a>
                                    @endif

                                    @if($message->file_path)
                                        <a href="{{ route('group-messages.attachment', $message->id) }}"
                                           target="_blank"
                                           class="block mt-2 underline">
                                            Open attachment
                                        </a>
                                    @endif

                                    <div class="text-[11px] opacity-75 mt-2 flex items-center justify-between gap-2">
                                        <span>{{ $message->created_at->format('H:i') }}</span>

                                        @if($message->user_id !== auth()->id())
                                            <details class="relative">
                                                <summary class="cursor-pointer">Report</summary>
                                                <div class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-xl shadow-lg p-3 z-20 text-gray-900 dark:text-white">
                                                    <form method="POST" action="{{ route('reports.store') }}" class="space-y-2">
                                                        @csrf
                                                        <input type="hidden" name="group_message_id" value="{{ $message->id }}">

                                                        <select name="reason" required class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg px-3 py-2 text-sm">
                                                            <option value="">Reason</option>
                                                            <option value="Harassment">Harassment</option>
                                                            <option value="Spam">Spam</option>
                                                            <option value="Inappropriate Content">Inappropriate Content</option>
                                                            <option value="Other">Other</option>
                                                        </select>

                                                        <textarea name="description" rows="2" placeholder="Optional details" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg px-3 py-2 text-sm"></textarea>

                                                        <button class="px-3 py-2 bg-red-600 text-white rounded-lg text-xs">Report Message</button>
                                                    </form>
                                                </div>
                                            </details>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="h-full flex items-center justify-center text-gray-600 dark:text-gray-300">
                            No group messages yet.
                        </div>
                    @endforelse
                </div>

                {{-- Group send form --}}
                <form method="POST"
                      action="{{ route('study-groups.messages.store', $activeGroup->id) }}"
                      enctype="multipart/form-data"
                      class="p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex gap-3 items-center shrink-0">
                    @csrf

                    <input type="file"
                           name="message_file"
                           id="group_message_file"
                           class="hidden">

                    <label for="group_message_file"
                           class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center cursor-pointer text-xl shrink-0">
                        📎
                    </label>

                    <input type="url"
                           name="resource_link"
                           placeholder="Paste link"
                              class="w-52 rounded-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-300 px-4 py-3">

                    <input type="text"
                           name="message"
                           placeholder="Type a group message"
                              class="flex-1 rounded-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-300 px-4 py-3">

                    <button type="submit"
                            class="w-12 h-12 rounded-full bg-blue-600 text-white text-xl shrink-0 hover:bg-blue-700">
                        ➤
                    </button>
                </form>

            @elseif($activeUser)

                {{-- Direct header --}}
                <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-4 flex justify-between items-center shrink-0">
                    <div class="flex items-center gap-3">
                        @if($activeUser->profile?->profile_picture)
                            <img src="{{ asset('storage/' . $activeUser->profile->profile_picture) }}"
                                 onclick="openImageViewer(this.src)"
                                 class="w-12 h-12 rounded-full object-cover cursor-pointer">
                        @else
                            <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                                {{ strtoupper(substr($activeUser->name, 0, 1)) }}
                            </div>
                        @endif

                        <div>
                            <h2 class="font-bold text-gray-900 dark:text-white">
                                {{ $activeUser->name }}
                            </h2>
                            <p class="text-sm text-blue-600">ConnectU peer</p>
                        </div>
                    </div>

                    <details class="relative">
                        <summary class="text-gray-600 dark:text-gray-300 text-xl cursor-pointer">⋮</summary>
                        <div class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-xl shadow-lg p-4 z-20">
                            <form method="POST" action="{{ route('reports.store') }}" class="space-y-2">
                                @csrf
                                <input type="hidden" name="reported_user_id" value="{{ $activeUser->id }}">

                                <select name="reason" required class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg px-3 py-2 text-sm">
                                    <option value="">Report user for...</option>
                                    <option value="Harassment">Harassment</option>
                                    <option value="Spam">Spam</option>
                                    <option value="Impersonation">Impersonation</option>
                                    <option value="Other">Other</option>
                                </select>

                                <textarea name="description" rows="3" placeholder="Describe the issue" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg px-3 py-2 text-sm"></textarea>

                                <button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">Submit Report</button>
                            </form>
                        </div>
                    </details>
                </div>

                {{-- Direct messages --}}
                <div class="flex-1 min-h-0 overflow-y-auto p-6 space-y-4">
                    @forelse($conversationMessages as $message)
                        @php
                            $mine = $message->sender_id == auth()->id();
                        @endphp

                        <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-md rounded-2xl px-4 py-3 shadow
                                {{ $mine ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-bl-none border border-blue-100 dark:border-gray-700' }}">

                                @if($message->message)
                                    <p>{{ $message->message }}</p>
                                @endif

                                @if($message->file_path)
                                    @php
                                        $extension = strtolower(pathinfo($message->file_path, PATHINFO_EXTENSION));
                                        $fileUrl = route('messages.attachment', $message->id);
                                    @endphp

                                    <div class="mt-3 bg-white/90 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl p-3 text-gray-900 dark:text-white">
                                        @if(in_array($extension, ['jpg', 'jpeg', 'png']))
                                            <img src="{{ $fileUrl }}"
                                                 class="rounded-lg max-h-56 mb-2">
                                        @else
                                            <div class="flex items-center gap-3">
                                                <div class="w-12 h-12 bg-blue-600 text-white rounded-lg flex items-center justify-center font-bold">
                                                    {{ strtoupper($extension) }}
                                                </div>
                                                <div>
                                                    <p class="font-semibold">Attached File</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-300">{{ strtoupper($extension) }}</p>
                                                </div>
                                            </div>
                                        @endif

                                        <a href="{{ $fileUrl }}"
                                           target="_blank"
                                           class="inline-block mt-2 text-blue-600 hover:underline">
                                            Open attachment
                                        </a>
                                    </div>
                                @endif

                                @if($message->resource_link)
                                    <div class="mt-3 bg-white/90 dark:bg-gray-700 border border-blue-100 dark:border-gray-600 rounded-xl p-3 text-gray-900 dark:text-white">
                                        <p class="text-sm font-semibold text-blue-700 dark:text-blue-400">Shared Link</p>
                                        <a href="{{ $message->resource_link }}"
                                           target="_blank"
                                           class="text-blue-600 dark:text-blue-400 hover:underline break-all">
                                            {{ $message->resource_link }}
                                        </a>
                                    </div>
                                @endif

                                <div class="text-xs mt-2 {{ $mine ? 'text-blue-100' : 'text-gray-500 dark:text-gray-300' }} flex items-center justify-between gap-2">
                                    <span>
                                        {{ $message->created_at->format('H:i') }}
                                        @if($mine)
                                            ✓✓
                                        @endif
                                    </span>

                                    @if(!$mine)
                                        <details class="relative">
                                            <summary class="cursor-pointer">Report</summary>
                                            <div class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-xl shadow-lg p-3 z-20 text-gray-900 dark:text-white">
                                                <form method="POST" action="{{ route('reports.store') }}" class="space-y-2">
                                                    @csrf
                                                    <input type="hidden" name="direct_message_id" value="{{ $message->id }}">

                                                    <select name="reason" required class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg px-3 py-2 text-sm">
                                                        <option value="">Reason</option>
                                                        <option value="Harassment">Harassment</option>
                                                        <option value="Spam">Spam</option>
                                                        <option value="Threatening Content">Threatening Content</option>
                                                        <option value="Other">Other</option>
                                                    </select>

                                                    <textarea name="description" rows="2" placeholder="Optional details" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg px-3 py-2 text-sm"></textarea>

                                                    <button class="px-3 py-2 bg-red-600 text-white rounded-lg text-xs">Report Message</button>
                                                </form>
                                            </div>
                                        </details>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex items-center justify-center text-gray-600 dark:text-gray-300">
                            Start chatting with {{ $activeUser->name }}.
                        </div>
                    @endforelse
                </div>

                {{-- Direct send form --}}
                <div class="bg-white dark:bg-gray-800 p-4 border-t border-gray-200 dark:border-gray-700 shrink-0">
                    <form action="{{ route('messages.store') }}"
                          method="POST"
                          enctype="multipart/form-data"
                          class="space-y-2">
                        @csrf

                        <input type="hidden" name="receiver_id" value="{{ $activeUser->id }}">

                        <input type="file"
                               id="message_file"
                               name="message_file"
                               class="hidden">

                        <div class="flex items-center gap-3">
                            <label for="message_file"
                                   class="w-12 h-12 rounded-full bg-blue-100 text-blue-700 hover:bg-blue-200 cursor-pointer flex items-center justify-center text-2xl shrink-0">
                                📎
                            </label>

                            <input type="url"
                                   name="resource_link"
                                   placeholder="Paste link"
                                class="w-44 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-300 rounded-full px-4 py-3 focus:ring-2 focus:ring-blue-500">

                            <input type="text"
                                   name="message"
                                   placeholder="Type a message"
                                class="flex-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-300 rounded-full px-5 py-3 focus:ring-2 focus:ring-blue-500">

                            <button type="submit"
                                    class="w-12 h-12 bg-blue-600 text-white rounded-full hover:bg-blue-700 flex items-center justify-center text-xl shrink-0">
                                ➤
                            </button>
                        </div>

                        <div class="flex items-center justify-between">
                            <p id="selectedFileName" class="text-xs text-gray-500 dark:text-gray-300 truncate"></p>
                            <p class="text-xs text-gray-400 dark:text-gray-300">Max file size: 20MB</p>
                        </div>
                    </form>
                </div>

            @else
                <div class="h-full flex items-center justify-center text-gray-600 dark:text-gray-300">
                    Select a chat to start messaging.
                </div>
            @endif

        </div>
    </div>

    <script>
        const fileInput = document.getElementById('message_file');
        const selectedFileName = document.getElementById('selectedFileName');

        if (fileInput && selectedFileName) {
            fileInput.addEventListener('change', function () {
                selectedFileName.textContent = this.files.length
                    ? 'Selected file: ' + this.files[0].name
                    : '';
            });
        }

        const searchInput = document.getElementById('chatSearch');

        if (searchInput) {
            searchInput.addEventListener('keyup', function () {
                const searchValue = this.value.toLowerCase();
                const chatItems = document.querySelectorAll('.chat-item');

                chatItems.forEach(function (item) {
                    const name = item.dataset.name.toLowerCase();

                    item.style.display = name.includes(searchValue) ? 'flex' : 'none';
                });
            });
        }
    </script>

</x-layouts.app>