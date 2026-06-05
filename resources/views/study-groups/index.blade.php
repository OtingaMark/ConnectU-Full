<!DOCTYPE html>
<html>
<head>
    <title>Study Groups</title>
</head>
<body>

<h1>Study Groups</h1>

@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<a href="{{ route('study-groups.create') }}">Create Study Group</a>

<hr>

@foreach($studyGroups as $group)
    <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
        <h3>{{ $group->group_name }}</h3>
        <p><strong>Course:</strong> {{ $group->course }}</p>
        <p><strong>Description:</strong> {{ $group->description }}</p>
        <p><strong>Max Members:</strong> {{ $group->max_members }}</p>
        <p><strong>Meeting Schedule:</strong> {{ $group->meeting_schedule }}</p>
        <p><strong>Created By:</strong> {{ $group->user->name ?? 'Unknown' }}</p>

        <form action="{{ route('study-groups.join', $group->id) }}" method="POST">
           @php
    $alreadyJoined = \App\Models\GroupMember::where('user_id', auth()->id())
        ->where('study_group_id', $group->id)
        ->exists();
@endphp

@if($alreadyJoined)
    <p style="color: green;">You have already joined this group.</p>
@else
    <form action="{{ route('study-groups.join', $group->id) }}" method="POST">
        @csrf
        <button type="submit">Join Group</button>
    </form>
@endif
        </form>
    </div>
@endforeach

</body>
</html>
