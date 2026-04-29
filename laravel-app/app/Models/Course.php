<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, HasAuditLog, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'category',
        'instructor_id',
        'thumbnail',
        'duration_hours',
        'difficulty',
        'is_mandatory',
        'target_roles',
        'is_published',
        'published_at',
        'max_participants',
    ];

    protected $casts = [
        'target_roles' => 'array',
        'is_mandatory' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'duration_hours' => 'decimal:1',
    ];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function materials()
    {
        return $this->hasMany(CourseMaterial::class)->orderBy('sort_order');
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
