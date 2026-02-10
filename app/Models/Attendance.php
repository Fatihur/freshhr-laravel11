<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    // Status constants for check-in
    const STATUS_VALID_ON_TIME = 'valid_on_time';
    const STATUS_VALID_LATE = 'valid_late';
    const STATUS_INVALID_OUT_OF_RADIUS = 'invalid_out_of_radius';
    const STATUS_INVALID_NO_SCHEDULE = 'invalid_no_schedule';
    const STATUS_INVALID_ON_LEAVE = 'invalid_on_leave';
    const STATUS_INVALID_ALREADY_IN = 'invalid_already_in';
    const STATUS_INVALID_EARLY = 'invalid_early';

    // Status constants for check-out
    const STATUS_OUT_VALID_NORMAL = 'valid_normal';
    const STATUS_OUT_VALID_EARLY = 'valid_early';
    const STATUS_OUT_INVALID_NOT_YET_IN = 'invalid_not_yet_in';
    const STATUS_OUT_INVALID_ALREADY_OUT = 'invalid_already_out';

    protected $fillable = [
        'employee_id',
        'date',
        'time_in',
        'time_out',
        'status',
        'status_in',
        'status_out',
        'location',
        'latitude',
        'longitude',
        'check_out_latitude',
        'check_out_longitude',
        'photo',
        'check_out_photo',
        'notes',
        'rejection_reason',
    ];

    protected $casts = [
        'date' => 'date',
        'time_in' => 'datetime:H:i',
        'time_out' => 'datetime:H:i',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'check_out_latitude' => 'decimal:8',
        'check_out_longitude' => 'decimal:8',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'present' => 'Hadir',
            'late' => 'Terlambat',
            'absent' => 'Tidak Hadir',
            default => $this->status,
        };
    }

    public function getStatusInLabelAttribute(): ?string
    {
        if (!$this->status_in) return null;

        return match($this->status_in) {
            self::STATUS_VALID_ON_TIME => 'Valid - Tepat Waktu',
            self::STATUS_VALID_LATE => 'Valid - Terlambat',
            self::STATUS_INVALID_OUT_OF_RADIUS => 'Invalid - Di Luar Radius',
            self::STATUS_INVALID_NO_SCHEDULE => 'Invalid - Tidak Ada Jadwal',
            self::STATUS_INVALID_ON_LEAVE => 'Invalid - Sedang Cuti',
            self::STATUS_INVALID_ALREADY_IN => 'Invalid - Sudah Check-in',
            self::STATUS_INVALID_EARLY => 'Invalid - Terlalu Awal',
            default => $this->status_in,
        };
    }

    public function getStatusOutLabelAttribute(): ?string
    {
        if (!$this->status_out) return null;

        return match($this->status_out) {
            self::STATUS_OUT_VALID_NORMAL => 'Valid - Normal',
            self::STATUS_OUT_VALID_EARLY => 'Valid - Pulang Awal',
            self::STATUS_OUT_INVALID_NOT_YET_IN => 'Invalid - Belum Check-in',
            self::STATUS_OUT_INVALID_ALREADY_OUT => 'Invalid - Sudah Check-out',
            default => $this->status_out,
        };
    }

    public function isValid(): bool
    {
        return in_array($this->status_in, [self::STATUS_VALID_ON_TIME, self::STATUS_VALID_LATE]);
    }

    public function isInvalid(): bool
    {
        return $this->status_in && str_starts_with($this->status_in, 'invalid_');
    }
}
