<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\DepartmentSchedule;
use App\Models\LeaveRequest;
use App\Models\OfficeSetting;
use App\Models\ScheduleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $todayAttendance = null;
        if ($user->employee) {
            $todayAttendance = Attendance::where('employee_id', $user->employee->id)
                ->whereDate('date', $today)
                ->first();
        }

        $officeSettings = OfficeSetting::where('is_active', true)->first();

        return view('attendance.index', compact('user', 'todayAttendance', 'officeSettings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'nullable|string',
        ]);

        $user = Auth::user();

        if (!$user->employee) {
            return back()->with('error', 'Akun Anda tidak terhubung ke data karyawan.');
        }

        $today = now()->toDateString();
        $currentTime = now();

        // Check if already checked in
        $existingAttendance = Attendance::where('employee_id', $user->employee->id)
            ->whereDate('date', $today)
            ->first();

        if ($existingAttendance && $existingAttendance->time_in) {
            return back()->with('error', 'Anda sudah melakukan absensi masuk hari ini.');
        }

        $officeSettings = OfficeSetting::where('is_active', true)->first();
        $workStartTime = $officeSettings?->work_start_time ?? '09:00:00';

        // 1. Validasi Radius
        $isInRadius = $this->checkRadius(
            $request->latitude,
            $request->longitude,
            $officeSettings?->latitude ?? -6.2088,
            $officeSettings?->longitude ?? 106.8456,
            $officeSettings?->radius ?? 100
        );

        if (!$isInRadius) {
            return $this->saveInvalidAttendance($user, $today, $currentTime, $request, Attendance::STATUS_INVALID_OUT_OF_RADIUS, 'Di luar radius kantor');
        }

        // 2. Validasi Jadwal (harus ada jadwal APPLIED)
        $hasSchedule = $this->checkSchedule($user->employee->id, $today);
        if (!$hasSchedule) {
            return $this->saveInvalidAttendance($user, $today, $currentTime, $request, Attendance::STATUS_INVALID_NO_SCHEDULE, 'Tidak ada jadwal kerja untuk hari ini');
        }

        // 3. Validasi Cuti (tidak bisa absen jika sedang cuti)
        $isOnLeave = $this->checkLeave($user->employee->id, $today);
        if ($isOnLeave) {
            return $this->saveInvalidAttendance($user, $today, $currentTime, $request, Attendance::STATUS_INVALID_ON_LEAVE, 'Anda sedang dalam periode cuti');
        }

        // 4. Tentukan status (tepat waktu atau terlambat)
        $workStartDateTime = now()->copy()->setTimeFromTimeString($workStartTime);
        $isLate = $currentTime->gt($workStartDateTime);

        $statusIn = $isLate ? Attendance::STATUS_VALID_LATE : Attendance::STATUS_VALID_ON_TIME;
        $statusLabel = $isLate ? 'late' : 'present';

        // Save photo if provided
        $photoPath = $this->savePhoto($request->photo, $user->employee->id, $today, 'in');

        // Create attendance record
        $attendance = Attendance::create([
            'employee_id' => $user->employee->id,
            'date' => $today,
            'time_in' => $currentTime->format('H:i:s'),
            'status' => $statusLabel,
            'status_in' => $statusIn,
            'location' => 'Kantor Utama',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'photo' => $photoPath,
        ]);

        // Log audit
        AuditLog::log(
            'check_in',
            'Attendance',
            $attendance->id,
            null,
            [
                'time_in' => $currentTime->format('H:i:s'),
                'status_in' => $statusIn,
                'location' => ['lat' => $request->latitude, 'lng' => $request->longitude],
            ],
            "Check-in {$statusIn} oleh {$user->name}"
        );

        $message = $isLate
            ? 'Absensi berhasil dicatat! Status: Terlambat'
            : 'Absensi berhasil dicatat! Selamat bekerja!';

        return back()->with('success', $message);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'nullable|string',
        ]);

        $user = Auth::user();
        $today = now()->toDateString();
        $currentTime = now();

        if (!$user->employee) {
            return back()->with('error', 'Akun Anda tidak terhubung ke data karyawan.');
        }

        $attendance = Attendance::where('employee_id', $user->employee->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Anda belum melakukan absensi masuk.');
        }

        if ($attendance->time_out) {
            return back()->with('error', 'Anda sudah melakukan absensi pulang hari ini.');
        }

        // Validasi Radius untuk check-out
        $officeSettings = OfficeSetting::where('is_active', true)->first();
        $isInRadius = $this->checkRadius(
            $request->latitude,
            $request->longitude,
            $officeSettings?->latitude ?? -6.2088,
            $officeSettings?->longitude ?? 106.8456,
            $officeSettings?->radius ?? 100
        );

        // Tentukan status check-out
        $workEndTime = $officeSettings?->work_end_time ?? '17:00:00';
        $workEndDateTime = now()->copy()->setTimeFromTimeString($workEndTime);
        $isEarly = $currentTime->lt($workEndDateTime);

        if ($isEarly) {
            $statusOut = Attendance::STATUS_OUT_VALID_EARLY;
        } else {
            $statusOut = Attendance::STATUS_OUT_VALID_NORMAL;
        }

        // Save photo if provided
        $photoPath = $this->savePhoto($request->photo, $user->employee->id, $today, 'out');

        $beforeData = ['time_out' => null];

        $attendance->update([
            'time_out' => $currentTime->format('H:i:s'),
            'status_out' => $statusOut,
            'check_out_latitude' => $request->latitude,
            'check_out_longitude' => $request->longitude,
            'check_out_photo' => $photoPath,
        ]);

        // Log audit
        AuditLog::log(
            'check_out',
            'Attendance',
            $attendance->id,
            $beforeData,
            [
                'time_out' => $currentTime->format('H:i:s'),
                'status_out' => $statusOut,
                'location' => ['lat' => $request->latitude, 'lng' => $request->longitude],
            ],
            "Check-out {$statusOut} oleh {$user->name}"
        );

        $message = $isEarly
            ? 'Absensi pulang berhasil dicatat! Status: Pulang Awal'
            : 'Absensi pulang berhasil dicatat!';

        return back()->with('success', $message);
    }

    /**
     * Check if location is within radius using Haversine formula
     */
    private function checkRadius($lat1, $lng1, $lat2, $lng2, $radius): bool
    {
        $earthRadius = 6371000; // meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance <= $radius;
    }

    /**
     * Check if employee has applied schedule for the date
     */
    private function checkSchedule($employeeId, $date): bool
    {
        // Cek di schedule_details dengan department_schedule status = applied
        return ScheduleDetail::whereHas('departmentSchedule', function ($query) {
                $query->where('status', DepartmentSchedule::STATUS_APPLIED);
            })
            ->where('employee_id', $employeeId)
            ->whereDate('date', $date)
            ->exists();
    }

    /**
     * Check if employee is on approved leave
     */
    private function checkLeave($employeeId, $date): bool
    {
        return LeaveRequest::where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->exists();
    }

    /**
     * Save invalid attendance attempt
     */
    private function saveInvalidAttendance($user, $today, $currentTime, $request, $statusIn, $reason)
    {
        // Save photo even for invalid attempts (for evidence)
        $photoPath = $this->savePhoto($request->photo, $user->employee->id, $today, 'in');

        $attendance = Attendance::create([
            'employee_id' => $user->employee->id,
            'date' => $today,
            'time_in' => $currentTime->format('H:i:s'),
            'status' => 'absent',
            'status_in' => $statusIn,
            'location' => 'Invalid',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'photo' => $photoPath,
            'rejection_reason' => $reason,
        ]);

        // Log audit for invalid attempt
        AuditLog::log(
            'check_in_failed',
            'Attendance',
            $attendance->id,
            null,
            [
                'status_in' => $statusIn,
                'reason' => $reason,
                'location' => ['lat' => $request->latitude, 'lng' => $request->longitude],
            ],
            "Check-in GAGAL ({$statusIn}) oleh {$user->name}: {$reason}"
        );

        return back()->with('error', "Absensi ditolak: {$reason}");
    }

    /**
     * Save photo from base64
     */
    private function savePhoto($photoData, $employeeId, $date, $type): ?string
    {
        if (!$photoData) {
            return null;
        }

        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photoData));
        $photoPath = "attendances/{$employeeId}_{$date}_{$type}.png";
        Storage::disk('public')->put($photoPath, $imageData);

        return $photoPath;
    }
}
