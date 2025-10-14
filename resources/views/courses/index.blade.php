<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('All Courses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($courses as $course)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <a href="{{ route('courses.show', $course) }}">
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-48 object-cover">
                        </a>
                        <div class="p-6">
                            <h3 class="text-lg font-semibold">
                                <a href="{{ route('courses.show', $course) }}" class="hover:text-blue-500">{{ $course->title }}</a>
                            </h3>
                                                            <p class="text-gray-600 mt-2">
                                                                By {{ $course->instructor->name ?? 'Unknown Instructor' }}
                                                            </p>                            <p class="text-lg font-bold mt-4">
                                ${{ number_format($course->price, 2) }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
