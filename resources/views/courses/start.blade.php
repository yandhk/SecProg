<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Course: {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-2xl font-bold mb-4">Selamat belajar!</h3>
                <p>Kamu sedang memulai course: <strong>{{ $course->title }}</strong></p>

                
            </div>
        </div>
    </div>
</x-app-layout>
