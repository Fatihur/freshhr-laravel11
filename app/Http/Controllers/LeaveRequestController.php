<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $leaveRequests = LeaveRequest::with('employee')
            ->when($user->role === 'employee', function ($query) use ($user) {
                return $query->where('employee_id', $user->employee_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get leave balance (mock data - in real app would calculate from records)
        $leaveBalance = [
            'remaining' => 14,
            'used' => 6,
            'total' => 20,
        ];

        return view('leave.index', compact('leaveRequests', 'leaveBalance', 'user'));
    }

    public function create()
    {
        return view('leave.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:annual,sick,emergency,maternity,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:1000',
            'handover_notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        LeaveRequest::create([
            'employee_id' => $user->employee_id,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'handover_notes' => $request->handover_notes,
            'status' => 'pending',
        ]);

        return redirect()->route('leave.index')->with('success', 'Pengajuan cuti berhasil dikirim.');
    }

    public function show(LeaveRequest $leaveRequest)
    {
        return view('leave.show', compact('leaveRequest'));
    }

    public function approve(LeaveRequest $leaveRequest, Request $request)
    {
        $user = Auth::user();
        
        switch ($user->role) {
            case 'dept_head':
                $leaveRequest->update([
                    'approval_dept_head' => true,
                    'dept_head_approved_at' => now(),
                ]);
                break;
            case 'hr_admin':
                $leaveRequest->update([
                    'approval_hrm' => true,
                    'hrm_approved_at' => now(),
                ]);
                break;
            case 'super_admin':
                $leaveRequest->update([
                    'approval_gm' => true,
                    'gm_approved_at' => now(),
                    'status' => 'approved',
                ]);
                break;
        }

        return back()->with('success', 'Pengajuan telah disetujui.');
    }

    public function reject(LeaveRequest $leaveRequest)
    {
        $leaveRequest->update(['status' => 'rejected']);
        
        return back()->with('success', 'Pengajuan telah ditolak.');
    }
}
