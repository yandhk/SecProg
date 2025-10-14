<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Courses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('success') }}
                        </div>
                    @endif

                    <h3 class="text-2xl font-semibold mb-6">Your Enrolled Courses</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($enrollments as $enrollment)
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border">
                                <a href="{{ route('courses.show', $enrollment->course) }}">
                                    <img src="{{ asset('storage/' . $enrollment->course->thumbnail) }}" alt="{{ $enrollment->course->title }}" class="w-full h-48 object-cover">
                                </a>
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold">
                                        <a href="{{ route('courses.show', $enrollment->course) }}" class="hover:text-blue-500">{{ $enrollment->course->title }}</a>
                                    </h3>
                                    <p class="text-gray-600 mt-2">
                                        By {{ $enrollment->course->instructor->name ?? 'Unknown Instructor' }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="col-span-full">You are not enrolled in any courses yet. <a href="{{ route('home') }}" class="text-blue-500 hover:underline">Browse courses</a>.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
