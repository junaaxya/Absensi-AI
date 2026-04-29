<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalaryComponent extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'user_id',
        'salary_component_id',
        'amount',
        'effective_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'effective_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salaryComponent()
    {
        return $this->belongsTo(SalaryComponent::class);
    }
}
