<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = \App\Models\Course::latest()->get();

        return view('courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $course = $request->user()->courses()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'thumbnail' => $thumbnailPath,
        ]);

        return redirect()->route('courses.show', $course);
    }

    /**
     * Display the specified resource.
     */
    public function show(\App\Models\Course $course)
    {
        return view('courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(\App\Models\Course $course)
    {
        return view('courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, \App\Models\Course $course)
    {
        // Authorize that the user owns the course
        if ($request->user()->id !== $course->instructor_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $thumbnailPath = $course->thumbnail;
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($course->thumbnail) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($course->thumbnail);
            }
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $course->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'thumbnail' => $thumbnailPath,
        ]);

        return redirect()->route('courses.show', $course);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(\App\Models\Course $course)
    {
        // Authorize that the user owns the course
        if (auth()->user()->id !== $course->instructor_id) {
            abort(403, 'Unauthorized action.');
        }

        // Delete the thumbnail
        if ($course->thumbnail) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        return redirect()->route('dashboard');
    }
}
