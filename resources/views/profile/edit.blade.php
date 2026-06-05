<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
</head>
<body>

<h1>Edit Profile</h1>

@if(session('success'))
    <p style="color: green;">
        {{ session('success') }}
    </p>
@endif

<form action="{{ route('profile.update') }}" method="POST">
    @csrf

    <label>Course:</label><br>
    <input type="text" name="course"
        value="{{ old('course', $profile->course ?? '') }}"><br><br>

    <label>Bio:</label><br>
    <textarea name="bio">{{ old('bio', $profile->bio ?? '') }}</textarea><br><br>

    <label>Interests:</label><br>
    <textarea name="interests">{{ old('interests', $profile->interests ?? '') }}</textarea><br><br>

    <label>Skills:</label><br>
    <textarea name="skills">{{ old('skills', $profile->skills ?? '') }}</textarea><br><br>

    <label>Availability:</label><br>
    <input type="text" name="availability"
        value="{{ old('availability', $profile->availability ?? '') }}"><br><br>

    <button type="submit">Save Profile</button>
</form>

</body>
</html>
