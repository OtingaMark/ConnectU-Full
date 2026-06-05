<!DOCTYPE html>
<html>
<head>
    <title>Create Study Group</title>
</head>
<body>

<h1>Create Study Group</h1>

<form action="{{ route('study-groups.store') }}" method="POST">
    @csrf

    <label>Group Name:</label><br>
    <input type="text" name="group_name" value="{{ old('group_name') }}"><br><br>

    <label>Course:</label><br>
    <input type="text" name="course" value="{{ old('course') }}"><br><br>

    <label>Description:</label><br>
    <textarea name="description">{{ old('description') }}</textarea><br><br>

    <label>Max Members:</label><br>
    <input type="number" name="max_members" value="{{ old('max_members', 10) }}"><br><br>

    <label>Meeting Schedule:</label><br>
    <input type="text" name="meeting_schedule" value="{{ old('meeting_schedule') }}"><br><br>

    <button type="submit">Create Group</button>
</form>

<br>
<a href="{{ route('study-groups.index') }}">Back to Study Groups</a>

</body>
</html>
