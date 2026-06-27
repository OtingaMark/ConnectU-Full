<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Suspension Appeal - ConnectU</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">

<div class="min-h-screen flex items-center justify-center px-6 py-10">
    <div class="max-w-2xl w-full bg-white rounded-2xl shadow p-8">
        <h1 class="text-2xl font-bold mb-2">Submit Suspension Appeal</h1>
        <p class="text-gray-600 mb-6">
            Account: {{ $user->email }}
        </p>

        <div class="mb-6 rounded-lg bg-red-100 text-red-800 px-4 py-3">
            <p class="font-semibold">Your account is currently suspended.</p>
            <p class="text-sm mt-1">Reason: {{ $user->suspension_reason ?: 'No reason provided.' }}</p>
        </div>

        <form method="POST" action="{{ route('appeal-suspension.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user->id }}">

            <div>
                <label class="block font-semibold mb-2">Appeal Reason</label>
                <select name="reason" required class="w-full border border-gray-300 rounded-lg px-4 py-3">
                    <option value="">Select a reason</option>
                    <option value="Request Reconsideration">Request Reconsideration</option>
                    <option value="Misunderstanding">Misunderstanding</option>
                    <option value="Behavior Improved">Behavior Improved</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold mb-2">Appeal Message</label>
                <textarea name="message" rows="6" required minlength="10"
                          placeholder="Explain why your account should be reactivated."
                          class="w-full border border-gray-300 rounded-lg px-4 py-3"></textarea>
            </div>

            <div class="flex items-center gap-3">
                <button class="px-5 py-3 bg-blue-700 text-white rounded-lg hover:bg-blue-800">
                    Submit Appeal
                </button>
                <a href="{{ route('login') }}" class="px-5 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Back to Login
                </a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
