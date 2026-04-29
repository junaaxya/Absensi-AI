<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxTerRate extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'category',
        'min_income',
        'max_income',
        'rate',
    ];

    protected $casts = [
        'min_income' => 'decimal:2',
        'max_income' => 'decimal:2',
        'rate' => 'decimal:6',
    ];
}
