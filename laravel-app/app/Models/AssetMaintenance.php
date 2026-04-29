<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetMaintenance extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'asset_id',
        'maintenance_type',
        'description',
        'cost',
        'performed_by',
        'performed_at',
        'next_maintenance_at',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'performed_at' => 'date',
        'next_maintenance_at' => 'date',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
