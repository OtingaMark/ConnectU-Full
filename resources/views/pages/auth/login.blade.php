<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ConnectU</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">

<div class="min-h-screen flex items-center justify-center px-6">
    <div class="max-w-5xl w-full bg-white rounded-2xl shadow-lg grid grid-cols-1 md:grid-cols-2 overflow-hidden">

        <div class="p-12 flex flex-col items-center justify-center text-center border-r">
            <img src="{{ asset('images/logo.png') }}"
                 alt="ConnectU Logo"
                 class="connectu-logo mb-6">

            <h1 class="text-3xl font-bold text-gray-900">Welcome Back!</h1>
            <p class="text-gray-600 mt-3">
                Login to continue to ConnectU.
            </p>
        </div>

        <div class="p-12">
            <h2 class="text-2xl font-bold text-center mb-8">Login</h2>

            <x-auth-session-status class="text-center" :status="session('status')" />

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-3 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-3 text-sm">
                    {{ session('error') }}

                    @if(session('suspension_user_id'))
                        <div class="mt-2">
                            <a href="{{ route('appeal-suspension.create', ['user' => session('suspension_user_id')]) }}"
                               class="inline-block px-3 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800">
                                Submit Appeal
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block font-semibold mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="Enter your email"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block font-semibold mb-2">Password</label>
                    <div class="relative">
                        <input type="password" id="loginPassword" name="password" required
                               placeholder="Enter your password"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-24 focus:ring-2 focus:ring-blue-500">
                        <button type="button" id="toggleLoginPassword"
                                class="absolute inset-y-0 right-3 my-auto text-sm text-blue-700 hover:underline">
                            Show
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-between text-sm">
                    <label>
                        <input type="checkbox" name="remember" class="mr-2">
                        Remember me
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline">
                            Forgot Password?
                        </a>
                    @endif
                </div>

                <button type="submit"
                        class="w-full bg-blue-700 text-white py-3 rounded-lg hover:bg-blue-800">
                    Login
                </button>
            </form>

            <p class="text-center text-sm mt-6">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Register</a>
            </p>
        </div>

    </div>
</div>

<script>
    (function () {
        const passwordInput = document.getElementById('loginPassword');
        const toggleButton = document.getElementById('toggleLoginPassword');

        if (!passwordInput || !toggleButton) {
            return;
        }

        toggleButton.addEventListener('click', function () {
            const showing = passwordInput.type === 'text';
            passwordInput.type = showing ? 'password' : 'text';
            toggleButton.textContent = showing ? 'Show' : 'Hide';
        });
    })();
</script>

</body>
</html>