<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use App\Traits\HasAuditLog;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasAuditLog;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'jabatan',
        'foto',
        'has_face_data',
        'department_id',
        'shift_id',
        'company_id',
        'branch_id',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'no_telepon',
        'no_rekening',
        'nama_bank',
        'npwp',
        'status_pernikahan',
        'jumlah_tanggungan',
        'tanggal_masuk',
        'tanggal_keluar',
        'status_karyawan',
        'gaji_pokok',
        'no_bpjs_kesehatan',
        'no_bpjs_ketenagakerjaan',
        'emergency_contact_name',
        'emergency_contact_phone',
        'is_approved',
        'approved_at',
        'approved_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'no_rekening',
        'npwp',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'has_face_data' => 'boolean',
            'tanggal_lahir' => 'date',
            'tanggal_masuk' => 'date',
            'tanggal_keluar' => 'date',
            'gaji_pokok' => 'decimal:2',
            'jumlah_tanggungan' => 'integer',
            'is_approved' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function shift()
    {
        return $this->belongsTo(WorkShift::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(CompanyBranch::class, 'branch_id');
    }

    /**
     * Get the profile photo URL with hybrid fallback:
     * 1. Manual upload (foto column)
     * 2. Face dataset proxy (self-caching)
     * 3. Default SVG avatar
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }

        if ($this->has_face_data && $this->username) {
            return route('profile.photo');
        }

        return asset('img/default-avatar.svg');
    }

    public function getStatusPtkpAttribute(): ?string
    {
        if ($this->status_pernikahan === null) {
            return null;
        }

        return $this->status_pernikahan . '/' . $this->jumlah_tanggungan;
    }

    public function getKategoriTerAttribute(): ?string
    {
        $ptkp = $this->status_ptkp;

        if ($ptkp === null) {
            return null;
        }

        $categoryMap = [
            'TK/0' => 'A',
            'TK/1' => 'A',
            'K/0'  => 'A',
            'TK/2' => 'B',
            'TK/3' => 'B',
            'K/1'  => 'B',
            'K/2'  => 'B',
            'K/3'  => 'C',
        ];

        return $categoryMap[$ptkp] ?? null;
    }

    public function setNoRekeningAttribute(?string $value): void
    {
        $this->attributes['no_rekening'] = $value !== null ? Crypt::encryptString($value) : null;
    }

    public function getNoRekeningAttribute(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable) {
            return $value;
        }
    }

    public function setNpwpAttribute(?string $value): void
    {
        $this->attributes['npwp'] = $value !== null ? Crypt::encryptString($value) : null;
    }

    public function getNpwpAttribute(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable) {
            return $value;
        }
    }

    public function violations()
    {
        return $this->hasMany(Violation::class);
    }

    public function warningLetters()
    {
        return $this->hasMany(WarningLetter::class);
    }

    public function devices()
    {
        return $this->hasMany(EmployeeDevice::class);
    }

    public function payrollDetails()
    {
        return $this->hasMany(PayrollDetail::class);
    }

    public function employeeSalaryComponents()
    {
        return $this->hasMany(EmployeeSalaryComponent::class);
    }

    public function visitAttendances()
    {
        return $this->hasMany(VisitAttendance::class);
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function isPayrollReady(): bool
    {
        return $this->gaji_pokok > 0
            && $this->status_pernikahan !== null
            && $this->jumlah_tanggungan !== null
            && $this->tanggal_masuk !== null;
    }
}
