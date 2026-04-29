<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'form_template_id',
        'submitted_by',
        'data',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'attachments',
    ];

    protected $casts = [
        'data' => 'array',
        'attachments' => 'array',
        'approved_at' => 'datetime',
    ];

    public function template()
    {
        return $this->belongsTo(FormTemplate::class, 'form_template_id');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function comments()
    {
        return $this->hasMany(FormSubmissionComment::class);
    }
}
