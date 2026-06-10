<!DOCTYPE html>
<html>
<head>
    <title>Skills</title>
</head>
<body>

<h1>Skill Sharing</h1>

@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<h2>Share a Skill</h2>

<form action="{{ route('skills.store') }}" method="POST">
    @csrf

    <label>Skill Name:</label><br>
    <input type="text" name="skill_name" value="{{ old('skill_name') }}" required><br><br>

    <label>Description:</label><br>
    <textarea name="description" rows="3" cols="50">{{ old('description') }}</textarea><br><br>

    <label>Skill Level:</label><br>
    <select name="skill_level" required>
        <option value="">-- Select Level --</option>
        <option value="Beginner">Beginner</option>
        <option value="Intermediate">Intermediate</option>
        <option value="Advanced">Advanced</option>
    </select><br><br>

    <label>Availability:</label><br>
    <input type="text" name="availability" value="{{ old('availability') }}"><br><br>

    <button type="submit">Share Skill</button>
</form>

<hr>

<h2>Available Skills</h2>

@if($skills->count() > 0)
    @foreach($skills as $skill)
        <div style="border: 1px solid #ccc; padding: 12px; margin-bottom: 12px;">
            <h3>{{ $skill->skill_name }}</h3>

            <p><strong>Description:</strong> {{ $skill->description }}</p>
            <p><strong>Level:</strong> {{ $skill->skill_level }}</p>
            <p><strong>Availability:</strong> {{ $skill->availability }}</p>
            <p><strong>Shared By:</strong> {{ $skill->user->name ?? 'Unknown' }}</p>
        </div>
    @endforeach
@else
    <p>No skills shared yet.</p>
@endif

</body>
</html>
