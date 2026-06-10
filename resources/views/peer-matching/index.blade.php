<!DOCTYPE html>
<html>
<head>
    <title>Peer Matching</title>
</head>
<body>

<h1>Recommended Study Partners</h1>

<a href="{{ route('profile.edit') }}">Edit My Profile</a>
<br><br>

@if($matches->count() > 0)

    @foreach($matches as $match)
        <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px;">
            <h3>{{ $match->user->name }}</h3>

            <p><strong>Course:</strong> {{ $match->course }}</p>
            <p><strong>Bio:</strong> {{ $match->bio }}</p>
            <p><strong>Availability:</strong> {{ $match->availability }}</p>

            <p><strong>Match Score:</strong> {{ $match->match_score }}%</p>

            <p>
                <strong>Shared Interests:</strong>
                {{ $match->shared_interests ?: 'None' }}
            </p>

            <p>
                <strong>Shared Skills:</strong>
                {{ $match->shared_skills ?: 'None' }}
            </p>
        </div>
    @endforeach

@else
    <p>No matching peers found yet. Try updating your profile with more interests and skills.</p>
@endif

</body>
</html>