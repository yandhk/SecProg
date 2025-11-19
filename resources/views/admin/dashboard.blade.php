<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h1 class="text-3xl font-bold mb-4">Welcome, Admin 👋</h1>
                    <p class="text-gray-600 mb-6">
                        Manage the platform using the tools below.
                    </p>

                    <!-- CARD CONTAINER -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                        <!-- Manage Users Card -->
                        <a href="{{ route('admin.users') }}"
                           class="block bg-white border rounded-xl p-6 shadow hover:shadow-md transition">

                            <h3 class="text-xl font-semibold mb-2">👥 Manage Users</h3>
                            <p class="text-gray-600 text-sm">
                                View, suspend, and manage platform users.
                            </p>
                        </a>

                        <!-- All Courses -->
                        <a href="{{ route('courses.index') }}"
                           class="block bg-white border rounded-xl p-6 shadow hover:shadow-md transition">

                            <h3 class="text-xl font-semibold mb-2">📚 All Courses</h3>
                            <p class="text-gray-600 text-sm">
                                Browse and view all available courses.
                            </p>
                        </a>

                        <!-- (Optional) Add More Admin Tools -->
                        <div class="block bg-gray-50 border rounded-xl p-6 opacity-60">
                            <h3 class="text-xl font-semibold mb-2">🛠 More Tools</h3>
                            <p class="text-gray-600 text-sm">
                                Additional admin features coming soon...
                            </p>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
