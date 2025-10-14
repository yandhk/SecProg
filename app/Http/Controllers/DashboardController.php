<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->user_type === 'instructor') {
            $courses = $user->courses()->latest()->get();

            return view('instructor.dashboard', compact('courses'));
        }

        // It's a learner
        $enrollments = $user->enrollments()->with('course')->latest()->get();

        return view('learner.dashboard', compact('enrollments'));
    }
}
