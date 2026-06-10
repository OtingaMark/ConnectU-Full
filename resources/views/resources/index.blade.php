<!DOCTYPE html>
<html>
<head>
    <title>Resources</title>
</head>
<body>

<h1>Learning Resources</h1>

@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<h2>Upload Resource</h2>

<form action="{{ route('resources.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <label>Title:</label><br>
    <input type="text" name="title" value="{{ old('title') }}" required><br><br>

    <label>Course:</label><br>
    <input type="text" name="course" value="{{ old('course') }}" required><br><br>

    <label>Description:</label><br>
    <textarea name="description" rows="3" cols="50">{{ old('description') }}</textarea><br><br>

    <label>Upload File:</label><br>
    <input type="file" name="resource_file"><br><br>

    <label>Resource Link:</label><br>
    <input type="url" name="resource_link" value="{{ old('resource_link') }}"><br><br>

    <button type="submit">Upload Resource</button>
</form>

<hr>

<h2>Available Resources</h2>

@if($resources->count() > 0)
    @foreach($resources as $resource)
        <div style="border: 1px solid #ccc; padding: 12px; margin-bottom: 12px;">
            <h3>{{ $resource->title }}</h3>

            <p><strong>Course:</strong> {{ $resource->course }}</p>
            <p><strong>Description:</strong> {{ $resource->description }}</p>
            <p><strong>Uploaded By:</strong> {{ $resource->user->name ?? 'Unknown' }}</p>

            @if($resource->file_path)
                <p>
                    <strong>File:</strong>
                    <a href="{{ asset('storage/' . $resource->file_path) }}" target="_blank">
                        View / Download File
                    </a>
                </p>
            @endif

            @if($resource->resource_link)
                <p>
                    <strong>Link:</strong>
                    <a href="{{ $resource->resource_link }}" target="_blank">
                        Open Resource Link
                    </a>
                </p>
            @endif

            <small>Uploaded at: {{ $resource->created_at }}</small>
        </div>
    @endforeach
@else
    <p>No resources uploaded yet.</p>
@endif

</body>
</html>
