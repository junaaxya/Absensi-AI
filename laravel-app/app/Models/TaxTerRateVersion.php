<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxTerRateVersion extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'regulation_code',
        'category',
        'min_income',
        'max_income',
        'rate',
        'effective_from',
        'effective_until',
        'created_by',
    ];

    protected $casts = [
        'min_income' => 'decimal:2',
        'max_income' => 'decimal:2',
        'rate' => 'decimal:5',
        'effective_from' => 'date',
        'effective_until' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
