<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSubmissionComment extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'form_submission_id',
        'user_id',
        'comment',
    ];

    public function submission()
    {
        return $this->belongsTo(FormSubmission::class, 'form_submission_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
