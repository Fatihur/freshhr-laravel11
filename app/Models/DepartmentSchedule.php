<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DepartmentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'year',
        'month',
        'status',
        'created_by',
        'submitted_by',
        'submitted_at',
        'applied_by',
        'applied_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'submitted_at' => 'datetime',
        'applied_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPLIED = 'applied';
    const STATUS_REJECTED = 'rejected';

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function applier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applied_by');
    }

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(ScheduleDetail::class);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }

    public function scopeApplied($query)
    {
        return $query->where('status', self::STATUS_APPLIED);
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function isApplied(): bool
    {
        return $this->status === self::STATUS_APPLIED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function canBeEditedBy($user): bool
    {
        // Draft: Only creator (dept head) can edit
        if ($this->isDraft()) {
            return $this->created_by === $user->id;
        }

        // Applied: Only HR Admin and Super Admin can edit
        if ($this->isApplied()) {
            return in_array($user->role, ['hr_admin', 'super_admin']);
        }

        // Submitted/Rejected: No one can edit
        return false;
    }

    public function canBeSubmittedBy($user): bool
    {
        return $this->isDraft() && $this->created_by === $user->id;
    }

    public function canBeAppliedBy($user): bool
    {
        return $this->isSubmitted() && in_array($user->role, ['hr_admin', 'super_admin']);
    }
}
