<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_schedule_id',
        'employee_id',
        'date',
        'shift_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function departmentSchedule(): BelongsTo
    {
        return $this->belongsTo(DepartmentSchedule::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }
}
