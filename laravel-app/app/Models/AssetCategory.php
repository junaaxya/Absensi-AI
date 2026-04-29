<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'name',
        'code',
        'description',
        'depreciation_method',
        'useful_life_years',
    ];

    protected $casts = [
        'useful_life_years' => 'integer',
    ];

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
