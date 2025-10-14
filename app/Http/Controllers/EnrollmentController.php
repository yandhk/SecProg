<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function store(Request $request, Course $course)
    {
        // Check if already enrolled
        $isEnrolled = Enrollment::where('learner_id', $request->user()->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($isEnrolled) {
            return redirect()->route('courses.show', $course)->with('error', 'You are already enrolled in this course.');
        }

        Enrollment::create([
            'learner_id' => $request->user()->id,
            'course_id' => $course->id,
        ]);

        return redirect()->route('dashboard')->with('success', 'Successfully enrolled in the course!');
    }
}
