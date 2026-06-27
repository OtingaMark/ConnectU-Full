<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - ConnectU</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">

<div class="min-h-screen flex items-center justify-center px-6">
    <div class="max-w-4xl w-full bg-white rounded-2xl shadow-lg grid grid-cols-1 md:grid-cols-2 overflow-hidden">

        <div class="p-12 flex flex-col items-center justify-center text-center border-r">
            <div class="w-24 h-24 border-2 border-gray-300 flex items-center justify-center text-gray-500 mb-6">
                LOGO
            </div>

            <h1 class="text-3xl font-bold text-gray-900">Create New Password</h1>
            <p class="text-gray-600 mt-3">
                Enter your new password to regain access to ConnectU.
            </p>
        </div>

        <div class="p-12">
            <h2 class="text-2xl font-bold text-center mb-8">Reset Password</h2>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf

                <input type="hidden" name="token" value="{{ request()->route('token') }}">

                <div>
                    <label class="block font-semibold mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', request()->email) }}" required autofocus
                           placeholder="Enter your email"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block font-semibold mb-2">New Password</label>
                    <input type="password" name="password" required
                           placeholder="Create new password"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block font-semibold mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                           placeholder="Confirm new password"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                </div>

                <button type="submit"
                        class="w-full bg-blue-700 text-white py-3 rounded-lg hover:bg-blue-800">
                    Reset Password
                </button>
            </form>

            <p class="text-center text-sm mt-6">
                Remember your password?
                <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Login</a>
            </p>
        </div>

    </div>
</div>

</body>
</html>