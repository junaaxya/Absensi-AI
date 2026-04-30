<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BpjsRateVersion extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'company_id',
        'program',
        'employer_rate',
        'employee_rate',
        'max_salary_basis',
        'min_salary_basis',
        'effective_from',
        'effective_until',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'employer_rate' => 'decimal:5',
        'employee_rate' => 'decimal:5',
        'max_salary_basis' => 'decimal:2',
        'min_salary_basis' => 'decimal:2',
        'effective_from' => 'date',
        'effective_until' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
