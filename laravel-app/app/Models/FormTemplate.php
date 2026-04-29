<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormTemplate extends Model
{
    use HasFactory, HasAuditLog, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'fields',
        'requires_approval',
        'approval_roles',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'fields' => 'array',
        'approval_roles' => 'array',
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions()
    {
        return $this->hasMany(FormSubmission::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
