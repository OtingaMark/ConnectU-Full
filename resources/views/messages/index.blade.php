<!DOCTYPE html>
<html>
<head>
    <title>Messages</title>
</head>
<body>

<h1>Messages</h1>

@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<h2>Send Message</h2>

<form action="{{ route('messages.store') }}" method="POST">
    @csrf

    <label>Select User:</label><br>
    <select name="receiver_id" required>
        <option value="">-- Choose User --</option>
        @foreach($users as $user)
            <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->email }}</option>
        @endforeach
    </select>

    <br><br>

    <label>Message:</label><br>
    <textarea name="message" rows="4" cols="50" required></textarea>

    <br><br>

    <button type="submit">Send Message</button>
</form>

<hr>

<h2>Received Messages</h2>

@if($receivedMessages->count() > 0)
    @foreach($receivedMessages as $message)
        <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
            <p><strong>From:</strong> {{ $message->sender->name ?? 'Unknown' }}</p>
            <p>{{ $message->message }}</p>
            <small>Sent at: {{ $message->created_at }}</small>
        </div>
    @endforeach
@else
    <p>No received messages yet.</p>
@endif

<hr>

<h2>Sent Messages</h2>

@if($sentMessages->count() > 0)
    @foreach($sentMessages as $message)
        <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
            <p><strong>To:</strong> {{ $message->receiver->name ?? 'Unknown' }}</p>
            <p>{{ $message->message }}</p>
            <small>Sent at: {{ $message->created_at }}</small>
        </div>
    @endforeach
@else
    <p>No sent messages yet.</p>
@endif

</body>
</html>