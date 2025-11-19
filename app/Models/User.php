<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Instructor courses relation
     */
    public function courses()
    {
        return $this->hasMany(\App\Models\Course::class, 'instructor_id');
    }

    /**
     * Learner enrollments relation
     */
    public function enrollments()
    {
        return $this->hasMany(\App\Models\Enrollment::class, 'learner_id');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role)
    {
        return $this->user_type === $role;
    }
}
