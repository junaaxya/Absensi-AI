<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketCategory extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'name',
        'code',
        'description',
        'default_priority',
        'sla_response_hours',
        'sla_resolution_hours',
        'is_active',
    ];

    protected $casts = [
        'sla_response_hours' => 'integer',
        'sla_resolution_hours' => 'integer',
        'is_active' => 'boolean',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
