<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateOnboarding extends Model
{
    use HasFactory, HasAuditLog;

    protected $table = 'candidate_onboarding';

    protected $fillable = [
        'candidate_id',
        'onboarding_task_id',
        'assigned_to',
        'status',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function onboardingTask()
    {
        return $this->belongsTo(OnboardingTask::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
