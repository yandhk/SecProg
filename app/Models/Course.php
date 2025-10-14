<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'title',
        'description',
        'price',
        'thumbnail',
    ];

    /**
     * Get the instructor that owns the course.
     */
    public function instructor()
    {
        return $this->belongsTo(\App\Models\User::class, 'instructor_id');
    }

    /**
     * Get the enrollments for the course.
     */
    public function enrollments()
    {
        return $this->hasMany(\App\Models\Enrollment::class);
    }

    /**
     * The learners that belong to the course.
     */
    public function learners()
    {
        return $this->belongsToMany(\App\Models\User::class, 'enrollments', 'course_id', 'learner_id');
    }
}
