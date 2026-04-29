<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkShift extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'days',
        'is_active',
        'description',
    ];

    protected $casts = [
        'days' => 'array',
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'shift_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
