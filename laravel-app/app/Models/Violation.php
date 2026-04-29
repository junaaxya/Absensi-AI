<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use App\Traits\PayrollFreezeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    use HasFactory, HasAuditLog, PayrollFreezeTrait;

    protected $fillable = [
        'user_id',
        'violation_type_id',
        'tanggal',
        'points',
        'reference_type',
        'reference_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'points' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function violationType()
    {
        return $this->belongsTo(ViolationType::class);
    }

    public function reference()
    {
        return $this->morphTo('reference');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
