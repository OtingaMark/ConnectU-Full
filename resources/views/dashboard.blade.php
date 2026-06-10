<!DOCTYPE html>
<html>
<head>
    <title>ConnectU Dashboard</title>
</head>
<body>

<h1>ConnectU Dashboard</h1>

<p>Welcome to ConnectU. Choose a feature below:</p>

<ul>
    <li><a href="{{ route('profile.edit') }}">Manage Profile</a></li>
    <li><a href="{{ route('peer-matching.index') }}">Find Study Partners</a></li>
    <li><a href="{{ route('study-groups.index') }}">Study Groups</a></li>
    <li><a href="{{ route('messages.index') }}">Messages</a></li>
    <li><a href="{{ route('resources.index') }}">Learning Resources</a></li>
</ul>

<form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit">Logout</button>
</form>

</body>
</html>