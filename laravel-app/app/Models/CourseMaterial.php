<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseMaterial extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'course_id',
        'title',
        'type',
        'content',
        'file_path',
        'sort_order',
        'duration_minutes',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'duration_minutes' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function progress()
    {
        return $this->hasMany(CourseMaterialProgress::class);
    }
}
