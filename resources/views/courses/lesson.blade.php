<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $lesson->title }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">
        <div class="bg-white p-6 shadow rounded">
            <h1 class="text-2xl font-bold mb-4">{{ $lesson->title }}</h1>

            <div class="prose">
                {!! nl2br(e($lesson->content)) !!}
            </div>

            @if($lesson->video_url)
                <div class="mt-4">
                    <iframe width="100%" height="360"
                        src="{{ $lesson->video_url }}"
                        frameborder="0" allowfullscreen></iframe>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
