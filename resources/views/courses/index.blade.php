<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('All Courses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Search bar -->
            <form action="{{ route('courses.index') }}" method="GET" class="mb-6">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search courses..." 
                    value="{{ request('search') }}"
                    class="border rounded-lg px-4 py-2 w-full md:w-1/3"
                >
            </form>

            {{-- Search result message --}}
            @if(request('search'))
                <p class="text-gray-600 mb-4">
                    Showing results for: <strong>{{ request('search') }}</strong>
                </p>
            @endif

            {{-- No result --}}
            @if($courses->isEmpty())
                <p class="text-gray-500 text-lg">
                    No courses found for: <strong>{{ request('search') }}</strong>
                </p>
            @endif

            <!-- Course grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($courses as $course)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <a href="{{ route('courses.show', $course) }}">
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" 
                                 alt="{{ $course->title }}" 
                                 class="w-full h-48 object-cover">
                        </a>

                        <div class="p-6">
                            <h3 class="text-lg font-semibold">
                                <a href="{{ route('courses.show', $course) }}" class="hover:text-blue-500">
                                    {{ $course->title }}
                                </a>
                            </h3>

                            <p class="text-gray-600 mt-2">
                                By {{ $course->instructor->name ?? 'Unknown Instructor' }}
                            </p>

                            <p class="text-lg font-bold mt-4">
                                ${{ number_format($course->price, 2) }}
                            </p>

                            {{-- ============================= --}}
                            {{-- ENROLL BUTTON FOR LEARNER --}}
                            {{-- ============================= --}}
                            @if(auth()->check() && auth()->user()->hasRole('learner'))
                                @php
                                    $isEnrolled = auth()->user()
                                        ->enrollments()
                                        ->where('course_id', $course->id)
                                        ->exists();
                                @endphp

                                <div class="mt-4">
                                    @if($isEnrolled)
                                        <span class="text-green-600 font-semibold">
                                            Enrolled
                                        </span>
                                    @else
                                        <form action="{{ route('enroll.store', $course->id) }}" method="POST">
                                            @csrf
                                            <button 
                                                type="submit"
                                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                                                Enroll Now
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                            {{-- end enroll --}}
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
