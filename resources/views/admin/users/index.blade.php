<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Users') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <h1 class="text-2xl font-bold mb-4">User Management</h1>
                <p class="text-gray-600 mb-6">View, suspend, or unsuspend platform users.</p>

                <!-- TABLE -->
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">ID</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Name</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Email</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Role</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Suspended</th>
                                <th class="px-4 py-2 text-sm font-medium text-gray-600">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @foreach ($users as $user)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ $user->id }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ $user->name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ $user->email }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ $user->role }}</td>

                                    <td class="px-4 py-2">
                                        @if ($user->is_suspended)
                                            <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-600 rounded-full">YES</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-600 rounded-full">NO</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-2">

                                        @if (!$user->is_suspended)
                                            <form action="{{ route('admin.users.suspend', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600 transition">
                                                    Suspend
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.users.unsuspend', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-1 text-sm bg-green-500 text-white rounded hover:bg-green-600 transition">
                                                    Un-Suspend
                                                </button>
                                            </form>
                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
