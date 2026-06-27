<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\PeerConnection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\StudyGroup;

class MessageController extends Controller
{
    public function index()
    {
        $currentUserId = Auth::id();

        $myGroups = StudyGroup::with(['messages.user', 'members'])
            ->where('status', 'active')
            ->whereHas('members', function ($q) use ($currentUserId) {
                $q->where('user_id', $currentUserId);
            })
            ->get()
            ->map(function ($group) {
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
            $chatUserIds->push($requestedUserId);
        }

        $chatUsers = User::active()->whereIn('id', $chatUserIds)->get();

        $chatUsers = $chatUsers->map(function ($user) use ($allMessages, $currentUserId) {
            $latestMessage = $allMessages->filter(function ($message) use ($user, $currentUserId) {
                return ($message->sender_id == $currentUserId && $message->receiver_id == $user->id)
                    || ($message->receiver_id == $currentUserId && $message->sender_id == $user->id);
            })->sortByDesc('created_at')->first();

            $user->latest_message = $latestMessage;

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
            Message::where('sender_id', $activeUserId)
                ->where('receiver_id', $currentUserId)
                ->where('is_read', false)
                ->update(['is_read' => true]);
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
            'groupMessages'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('status', 'active');
                }),
                Rule::notIn([Auth::id()]),
            ],
            'message' => 'nullable|string|max:1000',
            'message_file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,txt,jpg,jpeg,png|max:20480',
            'resource_link' => 'nullable|url|max:500',
        ]);

        $receiverId = $validated['receiver_id'];

        if (
            empty($validated['message']) &&
            !$request->hasFile('message_file') &&
            empty($validated['resource_link'])
        ) {
            return redirect()
                ->route('messages.index', ['user' => $receiverId])
                ->with('error', 'Please type a message, attach a file, or add a link.');
        }

        $connection = PeerConnection::where(function ($query) use ($receiverId) {
            $query->where('requester_id', Auth::id())
                ->where('receiver_id', $receiverId);
        })->orWhere(function ($query) use ($receiverId) {
            $query->where('requester_id', $receiverId)
                ->where('receiver_id', Auth::id());
        })->first();

        if (!$connection) {
            $sentCount = Message::where('sender_id', Auth::id())
                ->where('receiver_id', $receiverId)
                ->count();

            if ($sentCount >= 3) {
                return redirect()
                    ->route('messages.index', ['user' => $receiverId])
                    ->with('error', 'You have reached the 3-message limit. Wait for the user to accept your connection request.');
            }

            if ($sentCount == 2) {
                PeerConnection::create([
                    'requester_id' => Auth::id(),
                    'receiver_id' => $receiverId,
                    'status' => 'pending',
                ]);
            }
        } elseif ($connection->status !== 'accepted') {
            return redirect()
                ->route('messages.index', ['user' => $receiverId])
                ->with('error', 'Your connection request is still pending. Wait for the user to accept before continuing.');
        }

        $filePath = null;
        $messageType = 'text';

        if ($request->hasFile('message_file')) {
            $filePath = $request->file('message_file')->store('messages', 'public');
            $messageType = 'file';
        }

        if (!empty($validated['resource_link'])) {
            $messageType = $filePath ? 'mixed' : 'link';
        }

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiverId,
            'message' => $validated['message'] ?? '',
            'file_path' => $filePath,
            'resource_link' => $validated['resource_link'] ?? null,
            'message_type' => $messageType,
            'is_read' => false,
        ]);

        return redirect()
            ->route('messages.index', ['user' => $receiverId])
            ->with('success', 'Message sent successfully.');
    }

    public function attachment(Message $message)
    {
        if ($message->sender_id !== Auth::id() && $message->receiver_id !== Auth::id()) {
            abort(403);
        }

        if (!$message->file_path || !Storage::disk('public')->exists($message->file_path)) {
            abort(404);
        }

        return response()->file(storage_path('app/public/' . $message->file_path));
    }

    public function getMyGroups()
    {
        $myGroups = StudyGroup::whereHas('members', function ($q) {
            $q->where('user_id', auth()->id());
        })->where('status', 'active')->get();

        return $myGroups;
    }

    public function groupChat(
        StudyGroup $studyGroup
    )
    {
        return redirect()->route('messages.index', [
            'group' => $studyGroup->id
        ]);
    }
}