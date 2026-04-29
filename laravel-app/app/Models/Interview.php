<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'candidate_id',
        'interviewer_id',
        'scheduled_at',
        'duration_minutes',
        'type',
        'location',
        'status',
        'feedback',
        'score',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'duration_minutes' => 'integer',
        'score' => 'integer',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }
}
