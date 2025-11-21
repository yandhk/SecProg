<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Admin langsung ke admin dashboard
        if ($user->user_type === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Instructor dashboard
        if ($user->user_type === 'instructor') {
            $courses = $user->courses()->latest()->get();
            return view('instructor.dashboard', compact('courses'));
        }
        
        // Learner dashboard
        if ($user->user_type === 'learner') {
            $enrollments = $user->enrollments()->with('course')->latest()->get();
            return view('learner.dashboard', compact('enrollments'));
        }

        // Default fallback, misal user tipe lain
        return redirect()->route('home');
    }
}
