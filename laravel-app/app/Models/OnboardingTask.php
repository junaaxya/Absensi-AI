<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardingTask extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'name',
        'description',
        'department_id',
        'is_template',
        'order',
    ];

    protected $casts = [
        'is_template' => 'boolean',
        'order' => 'integer',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
