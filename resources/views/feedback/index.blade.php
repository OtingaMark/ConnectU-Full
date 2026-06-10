<!DOCTYPE html>
<html>
<head>
    <title>Feedback</title>
</head>
<body>

<h1>Feedback and Ratings</h1>

@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<h2>Give Feedback</h2>

<form action="{{ route('feedback.store') }}" method="POST">
    @csrf

    <label>Select User:</label><br>
    <select name="receiver_id" required>
        <option value="">-- Choose User --</option>
        @foreach($users as $user)
            <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->email }}</option>
        @endforeach
    </select><br><br>

    <label>Rating:</label><br>
    <select name="rating" required>
        <option value="">-- Select Rating --</option>
        <option value="1">1 - Poor</option>
        <option value="2">2 - Fair</option>
        <option value="3">3 - Good</option>
        <option value="4">4 - Very Good</option>
        <option value="5">5 - Excellent</option>
    </select><br><br>

    <label>Comment:</label><br>
    <textarea name="comment" rows="4" cols="50"></textarea><br><br>

    <button type="submit">Submit Feedback</button>
</form>

<hr>

<h2>Feedback Received</h2>

@if($feedbackReceived->count() > 0)
    @foreach($feedbackReceived as $feedback)
        <div style="border: 1px solid #ccc; padding: 12px; margin-bottom: 12px;">
            <p><strong>From:</strong> {{ $feedback->giver->name ?? 'Unknown' }}</p>
            <p><strong>Rating:</strong> {{ $feedback->rating }}/5</p>
            <p><strong>Comment:</strong> {{ $feedback->comment }}</p>
            <small>Submitted at: {{ $feedback->created_at }}</small>
        </div>
    @endforeach
@else
    <p>No feedback received yet.</p>
@endif

<hr>

<h2>Feedback Given</h2>

@if($feedbackGiven->count() > 0)
    @foreach($feedbackGiven as $feedback)
        <div style="border: 1px solid #ccc; padding: 12px; margin-bottom: 12px;">
            <p><strong>To:</strong> {{ $feedback->receiver->name ?? 'Unknown' }}</p>
            <p><strong>Rating:</strong> {{ $feedback->rating }}/5</p>
            <p><strong>Comment:</strong> {{ $feedback->comment }}</p>
            <small>Submitted at: {{ $feedback->created_at }}</small>
        </div>
    @endforeach
@else
    <p>No feedback given yet.</p>
@endif

</body>
</html>