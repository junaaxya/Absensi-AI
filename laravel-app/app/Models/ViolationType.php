<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViolationType extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'name',
        'code',
        'points',
        'description',
        'is_auto',
        'is_active',
    ];

    protected $casts = [
        'is_auto' => 'boolean',
        'is_active' => 'boolean',
        'points' => 'integer',
    ];

    public function violations()
    {
        return $this->hasMany(Violation::class);
    }
}
