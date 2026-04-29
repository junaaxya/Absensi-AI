<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxPtkpRate extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'status',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
}
