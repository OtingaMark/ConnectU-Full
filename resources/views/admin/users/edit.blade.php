<x-layouts.app :title="'Admin - Edit User'" :pageTitle="'Edit User'">

    <div class="max-w-3xl mx-auto p-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Edit User</h1>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block font-semibold mb-2 text-gray-700 dark:text-gray-200">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3">
                </div>

                <div>
                    <label class="block font-semibold mb-2 text-gray-700 dark:text-gray-200">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3">
                </div>

                <div>
                    <label class="block font-semibold mb-2 text-gray-700 dark:text-gray-200">Role</label>
                    <select name="role"
                            class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3">
                        <option value="student" {{ strtolower(trim(old('role', $user->role) ?? '')) === 'student' ? 'selected' : '' }}>Student</option>
                        <option value="admin" {{ strtolower(trim(old('role', $user->role) ?? '')) === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-2 text-gray-700 dark:text-gray-200">Account Status</label>
                    <select name="status"
                            class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3">
                        <option value="active" {{ strtolower(trim(old('status', $user->status ?? 'active') ?? '')) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ strtolower(trim(old('status', $user->status ?? 'active') ?? '')) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-2 text-gray-700 dark:text-gray-200">Suspension Reason (Required when suspended)</label>
                    <textarea name="suspension_reason" rows="3"
                              class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-4 py-3">{{ old('suspension_reason', $user->suspension_reason) }}</textarea>
                </div>

                <div class="flex gap-3">
                    <button class="px-5 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save Changes</button>
                    <a href="{{ route('admin.users.index') }}"
                       class="px-5 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>
