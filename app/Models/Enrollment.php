<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'learner_id',
        'course_id',
    ];

    /**
     * Get the learner that owns the enrollment.
     */
    public function learner()
    {
        return $this->belongsTo(\App\Models\User::class, 'learner_id');
    }

    /**
     * Get the course that owns the enrollment.
     */
    public function course()
    {
        return $this->belongsTo(\App\Models\Course::class);
    }
}
