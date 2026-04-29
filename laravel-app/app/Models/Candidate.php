<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate extends Model
{
    use HasFactory, HasAuditLog, SoftDeletes;

    protected $fillable = [
        'job_position_id',
        'name',
        'email',
        'phone',
        'resume_path',
        'cover_letter',
        'source',
        'status',
        'rating',
        'notes',
        'applied_at',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'rating' => 'integer',
    ];

    public function jobPosition()
    {
        return $this->belongsTo(JobPosition::class);
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }

    public function onboardingTasks()
    {
        return $this->belongsToMany(OnboardingTask::class, 'candidate_onboarding')
            ->withPivot(['assigned_to', 'status', 'completed_at', 'notes'])
            ->withTimestamps();
    }
}
