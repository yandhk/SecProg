<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2">
                    <div>
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6">
                        <h1 class="text-2xl font-bold">{{ $course->title }}</h1>
                        <p class="text-gray-600 mt-2">
                            By {{ $course->instructor->name ?? 'Unknown Instructor' }}
                        </p>
                        <p class="mt-4 text-gray-800">
                            {{ $course->description }}
                        </p>
                        <p class="text-2xl font-bold mt-6">
                            @if($course->price > 0)
                                Rp {{ number_format($course->price, 0, ',', '.') }}
                            @else
                                Free
                            @endif
                        </p>
                        <div class="mt-6">
                            @auth
                                @if(auth()->user()->user_type === 'learner')
                                    @php
                                        $isEnrolled = auth()->user()->enrollments()->where('course_id', $course->id)->exists();
                                    @endphp

                                    @if($isEnrolled)
                                        <p class="font-semibold text-green-600">You are already enrolled in this course.</p>
                                    @else
                                        @if($course->price > 0)
                                            <a href="{{ route('payment.checkout', $course) }}"
                                               class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                Enroll Now - Rp {{ number_format($course->price, 0, ',', '.') }}
                                            </a>
                                        @else
                                            <form method="POST" action="{{ route('enroll.store', $course) }}">
                                                @csrf
                                                <x-primary-button>
                                                    {{ __('Enroll for Free') }}
                                                </x-primary-button>
                                            </form>
                                        @endif
                                    @endif
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
