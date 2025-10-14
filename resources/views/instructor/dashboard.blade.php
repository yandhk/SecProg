<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Instructor Dashboard') }}
            </h2>
            <a href="{{ route('courses.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                {{ __('Create New Course') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-2xl font-semibold mb-6">Your Courses</h3>
                    <div class="space-y-4">
                        @forelse ($courses as $course)
                            <div class="flex items-center justify-between p-4 border rounded-lg">
                                <div>
                                    <a href="{{ route('courses.show', $course) }}" class="text-lg font-semibold hover:text-blue-500">{{ $course->title }}</a>
                                    <p class="text-sm text-gray-500 mt-1">{{ $course->enrollments->count() }} {{ Str::plural('Student', $course->enrollments->count()) }}</p>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('courses.edit', $course) }}" class="text-sm text-yellow-600 hover:text-yellow-800">Edit</a>
                                    <form method="POST" action="{{ route('courses.destroy', $course) }}" onsubmit="return confirm('Are you sure you want to delete this course?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p>You have not created any courses yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
