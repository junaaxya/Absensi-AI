<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'course_id',
        'user_id',
        'enrolled_at',
        'started_at',
        'completed_at',
        'progress_percentage',
        'status',
        'score',
        'certificate_number',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'progress_percentage' => 'integer',
        'score' => 'decimal:2',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function materialProgress()
    {
        return $this->hasMany(CourseMaterialProgress::class);
    }

    public function updateProgress(): void
    {
        $totalMaterials = $this->course->materials()->count();

        if ($totalMaterials === 0) {
            $this->update(['progress_percentage' => 0]);
            return;
        }

        $completedMaterials = $this->materialProgress()->where('is_completed', true)->count();
        $percentage = (int) round(($completedMaterials / $totalMaterials) * 100);

        $data = ['progress_percentage' => $percentage];

        if ($percentage === 100 && $this->status !== 'completed') {
            $data['status'] = 'completed';
            $data['completed_at'] = now();
            $data['certificate_number'] = 'CERT-' . strtoupper(uniqid()) . '-' . $this->id;
        } elseif ($percentage > 0 && $this->status === 'enrolled') {
            $data['status'] = 'in_progress';
            $data['started_at'] = $data['started_at'] ?? now();
        }

        $this->update($data);
    }
}
