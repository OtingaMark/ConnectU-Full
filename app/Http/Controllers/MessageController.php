<?php

namespace App\Http\Controllers;

use App\Models\StudyGroup;
use App\Models\Message;
use App\Models\PeerConnection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MessageController extends Controller
{
    /**
     * Build direct and group chat data for the unified messaging page.
     */
    public function index()
    {
        $currentUserId = Auth::id();
        $draftMessage = trim((string) request('draft', ''));
        $messageMode = trim((string) request('mode', ''));

        // Only active groups where the current user is a member appear in the chat list.
        $myGroups = StudyGroup::with(['messages.user', 'members'])
            ->where('status', 'active')
            ->whereHas('members', function ($q) use ($currentUserId) {
                $q->where('user_id', $currentUserId);
            })
            ->get()
            ->map(function ($group) {
                // Pre-compute latest message so the unified chat list can be sorted consistently.
                $group->latest_message = $group->messages
                    ->sortByDesc('created_at')
                    ->first();

                $group->chat_type = 'group';

                return $group;
            });

        $receivedMessages = Message::with('sender')
            ->where('receiver_id', $currentUserId)
            ->latest()
            ->get();

        $sentMessages = Message::with('receiver')
            ->where('sender_id', $currentUserId)
            ->latest()
            ->get();

        // Combine both directions to build one direct-chat sidebar list.
        $allMessages = $receivedMessages->concat($sentMessages);

        $chatUserIds = $allMessages->map(function ($message) use ($currentUserId) {
            return $message->sender_id == $currentUserId
                ? $message->receiver_id
                : $message->sender_id;
        })->unique()->values();

        $requestedUserId = (int) request('user');

        if (
            $requestedUserId &&
            !$chatUserIds->contains($requestedUserId) &&
            User::active()->whereKey($requestedUserId)->exists()
        ) {
            // If user came from a profile/connect action, include that target even with no history yet.
            $chatUserIds->push($requestedUserId);
        }

        $chatUsers = User::active()->whereIn('id', $chatUserIds)->get();

        $chatUsers = $chatUsers->map(function ($user) use ($allMessages, $currentUserId) {
            $latestMessage = $allMessages->filter(function ($message) use ($user, $currentUserId) {
                return ($message->sender_id == $currentUserId && $message->receiver_id == $user->id)
                    || ($message->receiver_id == $currentUserId && $message->sender_id == $user->id);
            })->sortByDesc('created_at')->first();

            $user->latest_message = $latestMessage;

            // Count unread incoming messages from this specific user for badge rendering.
            $user->unread_count = Message::where('sender_id', $user->id)
                ->where('receiver_id', $currentUserId)
                ->where('is_read', false)
                ->count();

            return $user;
        })->sortByDesc(function ($user) {
            return optional($user->latest_message)->created_at;
        })->values();

        $directChats = $chatUsers->map(function ($user) {
            $user->chat_type = 'direct';
            return $user;
        });

        $chatItems = $myGroups->concat($directChats)
            ->sortByDesc(function ($item) {
                return optional($item->latest_message)->created_at ?? $item->created_at;
            })
            ->values();

        $activeGroupId = request('group');

        $activeGroup = null;
        $groupMessages = collect();

        if ($activeGroupId) {
            // Group chat access is membership-gated.
            $activeGroup = StudyGroup::with(['messages.user', 'members'])
                ->where('status', 'active')
                ->whereHas('members', function ($q) use ($currentUserId) {
                    $q->where('user_id', $currentUserId);
                })
                ->where('id', $activeGroupId)
                ->firstOrFail();

            $groupMessages = $activeGroup->messages
                ->sortBy('created_at')
                ->values();

            $activeUserId = null;
        }

        $activeUserId = $activeGroupId
            ? null
            : (request('user') ?? optional($chatUsers->first())->id);

        if ($activeUserId) {
            // Opening a direct thread marks unread messages from that user as read.
            Message::where('sender_id', $activeUserId)
                ->where('receiver_id', $currentUserId)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        $remainingFreeMessages = null;

        if ($activeUserId) {
            $connection = PeerConnection::where(function ($query) use ($activeUserId, $currentUserId) {
                $query->where('requester_id', $currentUserId)
                    ->where('receiver_id', $activeUserId);
            })->orWhere(function ($query) use ($activeUserId, $currentUserId) {
                $query->where('requester_id', $activeUserId)
                    ->where('receiver_id', $currentUserId);
            })->latest('id')->first();

            $isConnected = $connection && $connection->status === 'accepted';

            if (!$isConnected) {
                // Limit unconnected outreach to 3 sent messages per target user.
                $sentCount = Message::where('sender_id', $currentUserId)
                    ->where('receiver_id', $activeUserId)
                    ->count();

                $remainingFreeMessages = max(0, 3 - $sentCount);
            }
        }

        return view('messages.index', compact(
            'chatUsers',
            'receivedMessages',
            'sentMessages',
            'activeUserId',
            'myGroups',
            'chatItems',
            'activeGroupId',
            'activeGroup',
            'groupMessages',
            'draftMessage',
            'messageMode',
            'remainingFreeMessages'
        ));
    }

    /**
     * Validate and send a direct message, enforcing connection/message limits.
     */
    public function store(Request $request)
    {
        $currentUserId = Auth::id();
        $messageMode = trim((string) $request->input('mode', ''));

        $validated = $request->validate([
            'receiver_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('status', 'active');
                }),
                Rule::notIn([$currentUserId]),
            ],
            'message' => 'nullable|string|max:1000',
            'message_file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,txt,jpg,jpeg,png|max:20480',
            'resource_link' => 'nullable|url|max:500',
        ]);

        $receiverId = $validated['receiver_id'];

        // Allow three content styles: text, attachment, or resource link (any one is enough).
        if (
            empty($validated['message']) &&
            !$request->hasFile('message_file') &&
            empty($validated['resource_link'])
        ) {
            return redirect()
                ->route('messages.index', ['user' => $receiverId, 'mode' => $messageMode ?: null])
                ->with('error', 'Please type a message, attach a file, or add a link.');
        }

        $connection = PeerConnection::where(function ($query) use ($receiverId, $currentUserId) {
            $query->where('requester_id', $currentUserId)
                ->where('receiver_id', $receiverId);
        })->orWhere(function ($query) use ($receiverId, $currentUserId) {
            $query->where('requester_id', $receiverId)
                ->where('receiver_id', $currentUserId);
        })->latest('id')->first();

        $isConnected = $connection && $connection->status === 'accepted';

        if (!$isConnected) {
            $sentCount = Message::where('sender_id', $currentUserId)
                ->where('receiver_id', $receiverId)
                ->count();

            if ($sentCount >= 3) {
                return redirect()
                    ->route('messages.index', ['user' => $receiverId, 'mode' => $messageMode ?: null])
                    ->with('error', 'You can only send up to 3 messages until this user accepts your connection request.');
            }

            if (!$connection) {
                // First cold message auto-creates a pending peer request.
                $connection = PeerConnection::create([
                    'requester_id' => $currentUserId,
                    'receiver_id' => $receiverId,
                    'status' => 'pending',
                ]);
            }
        }

        $filePath = null;
        $messageType = 'text';

        if ($request->hasFile('message_file')) {
            $filePath = $request->file('message_file')->store('messages', 'public');
            $messageType = 'file';
        }

        // If both a file and link exist, preserve that richer state as "mixed".
        if (!empty($validated['resource_link'])) {
            $messageType = $filePath ? 'mixed' : 'link';
        }

        Message::create([
            'sender_id' => $currentUserId,
            'receiver_id' => $receiverId,
            'message' => $validated['message'] ?? '',
            'file_path' => $filePath,
            'resource_link' => $validated['resource_link'] ?? null,
            'message_type' => $messageType,
            'is_read' => false,
        ]);

        return redirect()
            ->route('messages.index', ['user' => $receiverId, 'mode' => $messageMode ?: null])
            ->with('success', 'Message sent successfully.');
    }

    /**
     * Serve a direct-message attachment after access checks.
     */
    public function attachment(Message $message)
    {
        // Only sender or receiver can open the stored attachment.
        if ($message->sender_id !== Auth::id() && $message->receiver_id !== Auth::id()) {
            abort(403);
        }

        if (!$message->file_path || !Storage::disk('public')->exists($message->file_path)) {
            abort(404);
        }

        return response()->file(storage_path('app/public/' . $message->file_path));
    }

    /**
     * Return active groups that include the current user as a member.
     */
    public function getMyGroups()
    {
        // Helper endpoint used by UI pieces that need current active group membership.
        $myGroups = StudyGroup::whereHas('members', function ($q) {
            $q->where('user_id', auth()->id());
        })->where('status', 'active')->get();

        return $myGroups;
    }

    /**
     * Redirect to the messaging page focused on a specific group chat.
     */
    public function groupChat(
        StudyGroup $studyGroup
    )
    {
        // Reuse the unified messages screen and focus a specific group via query string.
        return redirect()->route('messages.index', [
            'group' => $studyGroup->id
        ]);
    }
}