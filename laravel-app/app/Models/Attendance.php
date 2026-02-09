<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'kegiatan',
        'status',
    ];

    protected $casts = [
        'tanggal'    => 'date',
        'jam_masuk'  => 'datetime:H:i:s',
        'jam_keluar' => 'datetime:H:i:s',
    ];

    // relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
