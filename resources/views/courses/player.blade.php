<x-app-layout>

<div class="flex h-screen">

    <!-- LEFT: Video Player -->
    <div class="flex-1 bg-black flex items-center justify-center">
        <video controls class="w-full h-full">
            <source src="{{ asset('storage/courses/'.$course->video_url) }}" type="video/mp4">
        </video>
    </div>

    <!-- RIGHT: Course Content -->
    <div class="w-80 bg-white border-l overflow-y-auto p-4">
        <h2 class="text-xl font-bold mb-4">{{ $course->title }}</h2>

        <h3 class="text-gray-600 mb-2">Course Content</h3>

        @foreach($sections as $section)
            <div class="mb-3">
                <p class="font-semibold">{{ $section->title }}</p>

                <ul class="ml-3 mt-1 text-sm text-gray-700">
                    @foreach($section->lessons as $lesson)
                        <li class="py-1 flex justify-between">
                            {{ $lesson->title }}
                            <span>{{ $lesson->duration }} min</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>

</div>

</x-app-layout>
