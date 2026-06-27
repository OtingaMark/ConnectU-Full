<x-layouts.app :title="'Admin - Users Management'" :pageTitle="'Users Management'">

    <div class="max-w-7xl mx-auto p-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Users Management</h1>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-4 md:p-6 overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead>
                    <tr class="text-left border-b dark:border-gray-700">
                        <th class="py-3">Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allUsers as $user)
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-3">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td>
                                @if(strtolower(trim($user->status ?? 'active')) === 'suspended')
                                    <span class="px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs">Suspended</span>
                                @else
                                    <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs">Active</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="px-2.5 py-1.5 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                        Edit
                                    </a>

                                    @if(auth()->id() !== $user->id)
                                        <details class="relative">
                                            <summary class="list-none cursor-pointer px-2.5 py-1.5 text-xs bg-gray-700 text-white rounded hover:bg-gray-800">
                                                Actions
                                            </summary>

                                            <div class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg shadow-lg p-3 z-20 space-y-2">
                                                @if(strtolower(trim($user->status ?? 'active')) === 'suspended')
                                                    <form method="POST"
                                                          action="{{ route('admin.users.suspend', $user) }}"
                                                          onsubmit="return confirm('Unsuspend this user?')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="action" value="unsuspend">
                                                        <button class="w-full px-2.5 py-1.5 text-xs bg-emerald-600 text-white rounded hover:bg-emerald-700 text-left">
                                                            Unsuspend User
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST"
                                                          action="{{ route('admin.users.suspend', $user) }}"
                                                          class="space-y-2"
                                                          onsubmit="return confirm('Suspend this user account?')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="text"
                                                               name="suspension_reason"
                                                               required
                                                               minlength="5"
                                                               placeholder="Suspension reason"
                                                               class="w-full px-2 py-1.5 text-xs border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded">
                                                        <button class="w-full px-2.5 py-1.5 text-xs bg-amber-600 text-white rounded hover:bg-amber-700 text-left">
                                                            Suspend User
                                                        </button>
                                                    </form>
                                                @endif

                                                <form method="POST"
                                                      action="{{ route('admin.change-role', $user) }}"
                                                      onsubmit="return confirm('Change this user role?')">
                                                    @csrf
                                                    <button class="w-full px-2.5 py-1.5 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 text-left">
                                                        {{ strtolower(trim($user->role ?? '')) === 'admin' ? 'Make Student' : 'Make Admin' }}
                                                    </button>
                                                </form>

                                                <form method="POST"
                                                      action="{{ route('admin.users.delete', $user) }}"
                                                      onsubmit="return confirm('Delete this user account? This cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="w-full px-2.5 py-1.5 text-xs bg-red-600 text-white rounded hover:bg-red-700 text-left">
                                                        Delete User
                                                    </button>
                                                </form>
                                            </div>
                                        </details>
                                    @else
                                        <span class="text-gray-500">You</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $allUsers->links() }}
            </div>
        </div>
    </div>

</x-layouts.app>
