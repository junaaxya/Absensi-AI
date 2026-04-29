<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarningLetter extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'user_id',
        'type',
        'period_month',
        'total_points',
        'issued_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'total_points' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
