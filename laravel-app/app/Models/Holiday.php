<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'name',
        'date',
        'description',
        'is_national',
    ];

    protected $casts = [
        'date' => 'date',
        'is_national' => 'boolean',
    ];
}
