<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryComponent extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'name',
        'code',
        'type',
        'is_taxable',
        'is_fixed',
        'default_amount',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_taxable' => 'boolean',
        'is_fixed' => 'boolean',
        'default_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function employeeSalaryComponents()
    {
        return $this->hasMany(EmployeeSalaryComponent::class);
    }
}
