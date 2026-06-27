<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ConnectU</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">

<div class="min-h-screen flex items-center justify-center px-6 py-10">
    <div class="max-w-5xl w-full bg-white rounded-2xl shadow-lg grid grid-cols-1 md:grid-cols-2 overflow-hidden">

        <div class="p-12 flex flex-col items-center justify-center text-center border-r">
            <img src="{{ asset('images/logo.png') }}"
                 alt="ConnectU Logo"
                 class="connectu-logo mb-6">

            <h1 class="text-3xl font-bold text-gray-900">Create Your Account</h1>
            <p class="text-gray-600 mt-3">
                Join ConnectU and start collaborating with other students.
            </p>
        </div>

        <div class="p-12">
            <h2 class="text-2xl font-bold text-center mb-8">Register</h2>

            <x-auth-session-status class="text-center" :status="session('status')" />

            <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block font-semibold mb-2">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                           placeholder="Enter your full name"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block font-semibold mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           placeholder="Enter your email"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block font-semibold mb-2">Password</label>
                    <input type="password" name="password" required
                           placeholder="Create a password"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block font-semibold mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                           placeholder="Confirm your password"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                </div>

                <button type="submit"
                        class="w-full bg-blue-700 text-white py-3 rounded-lg hover:bg-blue-800">
                    Register
                </button>
            </form>

            <p class="text-center text-sm mt-6">
                Already have an account?
                <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Login</a>
            </p>
        </div>

    </div>
</div>

</body>
</html>