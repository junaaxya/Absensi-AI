<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormulaAuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'formula_audit_log';

    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'old_formula',
        'new_formula',
        'ip_address',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
