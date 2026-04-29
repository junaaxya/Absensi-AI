<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, HasAuditLog, SoftDeletes;

    protected $fillable = [
        'asset_category_id',
        'name',
        'asset_code',
        'description',
        'serial_number',
        'purchase_date',
        'purchase_price',
        'current_value',
        'condition',
        'location',
        'notes',
        'photo',
        'assigned_to',
        'assigned_at',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'current_value' => 'decimal:2',
        'assigned_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function maintenances()
    {
        return $this->hasMany(AssetMaintenance::class);
    }
}
