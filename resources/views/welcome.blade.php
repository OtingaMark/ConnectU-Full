<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ConnectU</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">

<div class="min-h-screen flex items-center justify-center px-6">
    <div class="max-w-6xl w-full grid grid-cols-1 md:grid-cols-2 bg-white rounded-2xl shadow-lg overflow-hidden">

        <div class="p-12 flex flex-col justify-center">
            <img src="{{ asset('images/logo.png') }}"
                 alt="ConnectU Logo"
                 class="connectu-logo mb-6">
            <h1 class="text-5xl font-bold text-blue-700">ConnectU</h1>
            <p class="mt-4 text-xl text-gray-700">
                A peer learning platform for students.
            </p>
            <p class="mt-4 text-gray-600">
                Find study partners, join learning groups, share resources, exchange skills and communicate with other students.
            </p>

            <div class="mt-8 flex gap-4">
                <a href="{{ route('login') }}"
                   class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Login
                </a>

                <a href="{{ route('register') }}"
                   class="px-6 py-3 border border-blue-600 text-blue-700 rounded-lg hover:bg-blue-50">
                    Register
                </a>
            </div>
        </div>

        <div class="bg-blue-700 text-white p-12 flex flex-col justify-center">
            <h2 class="text-3xl font-bold">Learn better together.</h2>

            <div class="mt-8 space-y-5">
                <div>
                    <h3 class="font-bold">Learning Groups</h3>
                    <p class="text-blue-100">Create and join collaboration groups for academics, hobbies, sports, and skills.</p>
                </div>

                <div>
                    <h3 class="font-bold">Skill Sharing</h3>
                    <p class="text-blue-100">Teach and learn skills from other students.</p>
                </div>

                <div>
                    <h3 class="font-bold">Resources</h3>
                    <p class="text-blue-100">Upload and access useful learning materials.</p>
                </div>

                <div>
                    <h3 class="font-bold">Messaging</h3>
                    <p class="text-blue-100">Communicate with peers privately.</p>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>