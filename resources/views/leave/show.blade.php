@extends('layouts.app')

@section('title', 'Detail Pengajuan Cuti')
@section('header-title', 'Detail Pengajuan Cuti')

@section('content')
<div class="grid" style="grid-template-columns: 2fr 1fr; gap: 32px;">
    <!-- Left Content -->
    <div class="space-y-6">
        <!-- Header Card -->
        <div class="card">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div style="width: 64px; height: 64px; border-radius: 20px; background: {{ $leaveRequest->status === 'approved' ? '#dcfce7' : ($leaveRequest->status === 'rejected' ? '#fee2e2' : '#fef9c3') }}; color: {{ $leaveRequest->status === 'approved' ? '#22c55e' : ($leaveRequest->status === 'rejected' ? '#ef4444' : '#eab308') }}; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="file-text" style="width: 28px; height: 28px;"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">{{ $leaveRequest->type_label }}</h2>
                        <p class="text-sm text-slate-400">Diajukan oleh {{ $leaveRequest->employee->name }} pada {{ $leaveRequest->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
                <span class="badge {{ $leaveRequest->status === 'approved' ? 'badge-success' : ($leaveRequest->status === 'rejected' ? 'badge-danger' : 'badge-warning') }}" style="font-size: 14px; padding: 8px 20px;">
                    {{ $leaveRequest->status_label }}
                </span>
            </div>

            <div class="grid grid-cols-3" style="gap: 16px; padding-top: 24px; border-top: 1px solid var(--slate-100);">
                <div>
                    <p class="text-xs text-slate-400 uppercase font-bold mb-1">Tanggal Mulai</p>
                    <p class="text-lg font-semibold text-slate-800">{{ $leaveRequest->start_date->format('d M Y') }}</p>
                    <p class="text-xs text-slate-400">{{ $leaveRequest->start_date->isoFormat('dddd') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase font-bold mb-1">Tanggal Selesai</p>
                    <p class="text-lg font-semibold text-slate-800">{{ $leaveRequest->end_date->format('d M Y') }}</p>
                    <p class="text-xs text-slate-400">{{ $leaveRequest->end_date->isoFormat('dddd') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase font-bold mb-1">Durasi</p>
                    <p class="text-lg font-semibold text-slate-800">{{ $leaveRequest->duration }} Hari</p>
                    <p class="text-xs text-slate-400">Kerja</p>
                </div>
            </div>
        </div>

        <!-- Details Card -->
        <div class="card">
            <h3 class="text-lg font-bold text-slate-800 mb-6">Detail Pengajuan</h3>

            <div class="space-y-6">
                <div>
                    <p class="text-xs text-slate-400 uppercase font-bold mb-2">Alasan Cuti</p>
                    <p class="text-sm text-slate-600" style="line-height: 1.8;">{{ $leaveRequest->reason ?: 'Tidak ada alasan yang diberikan' }}</p>
                </div>

                @if($leaveRequest->handover_notes)
                <div style="padding-top: 16px; border-top: 1px solid var(--slate-100);">
                    <p class="text-xs text-slate-400 uppercase font-bold mb-2">Catatan Serah Terima</p>
                    <p class="text-sm text-slate-600" style="line-height: 1.8;">{{ $leaveRequest->handover_notes }}</p>
                </div>
                @endif

                @if($leaveRequest->handover_to)
                <div style="padding-top: 16px; border-top: 1px solid var(--slate-100);">
                    <p class="text-xs text-slate-400 uppercase font-bold mb-2">Diserahkan Kepada</p>
                    <div class="flex items-center gap-3">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 12px;">
                            {{ substr($leaveRequest->handover_to, 0, 1) }}
                        </div>
                        <p class="text-sm font-semibold text-slate-700">{{ $leaveRequest->handover_to }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons for Approvers -->
        @if($leaveRequest->status === 'pending' && auth()->user()->role !== 'employee')
        <div class="card" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Tindakan Persetujuan</h3>
            <p class="text-sm text-slate-500 mb-6">Anda memiliki kewenangan untuk menyetujui atau menolak pengajuan ini.</p>

            <div class="flex gap-3">
                <form method="POST" action="{{ route('leave.reject', $leaveRequest) }}" class="flex-1">
                    @csrf
                    <button type="submit" class="btn btn-danger w-full">
                        <i data-lucide="x-circle"></i>
                        Tolak
                    </button>
                </form>
                <form method="POST" action="{{ route('leave.approve', $leaveRequest) }}" class="flex-1">
                    @csrf
                    <button type="submit" class="btn btn-primary w-full">
                        <i data-lucide="check-circle-2"></i>
                        Setujui
                    </button>
                </form>
            </div>
        </div>
        @endif

        @if($leaveRequest->status === 'rejected')
        <div class="card" style="background: #fee2e2; border: 1px solid #fecaca;">
            <div class="flex items-center gap-3 text-red-600">
                <i data-lucide="alert-circle" style="width: 20px; height: 20px;"></i>
                <span class="font-semibold">Pengajuan ini telah ditolak</span>
            </div>
        </div>
        @endif
    </div>

    <!-- Right Sidebar: Approval Timeline -->
    <div class="space-y-6">
        <div class="card" style="position: sticky; top: 32px;">
            <h3 class="text-lg font-bold text-slate-800 mb-8">Status Persetujuan</h3>

            <div class="space-y-0" style="position: relative;">
                <div style="position: absolute; left: 19px; top: 16px; bottom: 16px; width: 2px; background: var(--slate-100);"></div>

                <!-- Step 1: Dept Head -->
                <div class="flex items-start gap-6" style="padding-bottom: 32px;">
                    @if($leaveRequest->approval_dept_head)
                        <div style="position: relative; z-index: 10; width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: var(--slate-900); display: flex; align-items: center; justify-content: center; border: 4px solid white; box-shadow: 0 4px 12px rgba(74, 222, 128, 0.3); flex-shrink: 0;">
                            <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-bold text-slate-800 truncate">Dept Head</p>
                                <span style="font-size: 9px; font-weight: 700; color: #15803d; background: #dcfce7; padding: 2px 8px; border-radius: 9999px; white-space: nowrap;">Disetujui</span>
                            </div>
                            <p style="font-size: 10px; font-weight: 500; color: var(--slate-400); text-transform: uppercase; letter-spacing: 1px; margin-top: 2px;">{{ $leaveRequest->dept_head_approved_at?->format('d M Y') ?? '-' }}</p>
                        </div>
                    @else
                        <div style="position: relative; z-index: 10; width: 40px; height: 40px; border-radius: 50%; background: {{ $leaveRequest->status === 'pending' ? '#fef9c3' : 'var(--slate-100)' }}; color: {{ $leaveRequest->status === 'pending' ? '#eab308' : 'var(--slate-300)' }}; display: flex; align-items: center; justify-content: center; border: 4px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.05); flex-shrink: 0;">
                            <i data-lucide="clock" style="width: 18px; height: 18px;"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-bold {{ $leaveRequest->status === 'pending' ? 'text-slate-800' : 'text-slate-400' }} truncate">Dept Head</p>
                                @if($leaveRequest->status === 'pending')
                                    <span style="font-size: 9px; font-weight: 700; color: #a16207; background: #fef9c3; padding: 2px 8px; border-radius: 9999px; white-space: nowrap;">Menunggu</span>
                                @endif
                            </div>
                            <p style="font-size: 10px; font-weight: 500; color: var(--slate-400); text-transform: uppercase; letter-spacing: 1px; margin-top: 2px;">Level 1</p>
                        </div>
                    @endif
                </div>

                <!-- Step 2: HRM -->
                <div class="flex items-start gap-6" style="padding-bottom: 32px;">
                    @if($leaveRequest->approval_hrm)
                        <div style="position: relative; z-index: 10; width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: var(--slate-900); display: flex; align-items: center; justify-content: center; border: 4px solid white; box-shadow: 0 4px 12px rgba(74, 222, 128, 0.3); flex-shrink: 0;">
                            <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-bold text-slate-800 truncate">HR Manager</p>
                                <span style="font-size: 9px; font-weight: 700; color: #15803d; background: #dcfce7; padding: 2px 8px; border-radius: 9999px; white-space: nowrap;">Disetujui</span>
                            </div>
                            <p style="font-size: 10px; font-weight: 500; color: var(--slate-400); text-transform: uppercase; letter-spacing: 1px; margin-top: 2px;">{{ $leaveRequest->hrm_approved_at?->format('d M Y') ?? '-' }}</p>
                        </div>
                    @else
                        <div style="position: relative; z-index: 10; width: 40px; height: 40px; border-radius: 50%; background: {{ $leaveRequest->approval_dept_head ? '#fef9c3' : 'var(--slate-100)' }}; color: {{ $leaveRequest->approval_dept_head ? '#eab308' : 'var(--slate-300)' }}; display: flex; align-items: center; justify-content: center; border: 4px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.05); flex-shrink: 0;">
                            <i data-lucide="clock" style="width: 18px; height: 18px;"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-bold {{ $leaveRequest->approval_dept_head ? 'text-slate-800' : 'text-slate-400' }} truncate">HR Manager</p>
                            </div>
                            <p style="font-size: 10px; font-weight: 500; color: var(--slate-400); text-transform: uppercase; letter-spacing: 1px; margin-top: 2px;">Level 2</p>
                        </div>
                    @endif
                </div>

                <!-- Step 3: GM -->
                <div class="flex items-start gap-6">
                    @if($leaveRequest->approval_gm)
                        <div style="position: relative; z-index: 10; width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: var(--slate-900); display: flex; align-items: center; justify-content: center; border: 4px solid white; box-shadow: 0 4px 12px rgba(74, 222, 128, 0.3); flex-shrink: 0;">
                            <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-bold text-slate-800 truncate">General Manager</p>
                                <span style="font-size: 9px; font-weight: 700; color: #15803d; background: #dcfce7; padding: 2px 8px; border-radius: 9999px; white-space: nowrap;">Disetujui</span>
                            </div>
                            <p style="font-size: 10px; font-weight: 500; color: var(--slate-400); text-transform: uppercase; letter-spacing: 1px; margin-top: 2px;">{{ $leaveRequest->gm_approved_at?->format('d M Y') ?? '-' }}</p>
                        </div>
                    @else
                        <div style="position: relative; z-index: 10; width: 40px; height: 40px; border-radius: 50%; background: {{ $leaveRequest->approval_hrm ? '#fef9c3' : 'var(--slate-100)' }}; color: {{ $leaveRequest->approval_hrm ? '#eab308' : 'var(--slate-300)' }}; display: flex; align-items: center; justify-content: center; border: 4px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.05); flex-shrink: 0;">
                            <i data-lucide="clock" style="width: 18px; height: 18px;"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-bold {{ $leaveRequest->approval_hrm ? 'text-slate-800' : 'text-slate-400' }} truncate">General Manager</p>
                            </div>
                            <p style="font-size: 10px; font-weight: 500; color: var(--slate-400); text-transform: uppercase; letter-spacing: 1px; margin-top: 2px;">Level 3 - Final</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Employee Info -->
        <div class="card" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #bbf7d0;">
            <div class="flex items-center gap-4">
                <img src="{{ $leaveRequest->employee->avatar_url }}" alt="{{ $leaveRequest->employee->name }}" class="avatar" style="width: 48px; height: 48px; border: 3px solid white;">
                <div>
                    <p class="text-sm font-bold text-slate-800">{{ $leaveRequest->employee->name }}</p>
                    <p class="text-xs text-slate-500">{{ $leaveRequest->employee->position?->name ?? '-' }}</p>
                    <p class="text-xs text-slate-400">{{ $leaveRequest->employee->department?->name ?? '-' }}</p>
                </div>
            </div>
        </div>

        <a href="{{ route('leave.index') }}" class="btn btn-secondary w-full">
            <i data-lucide="arrow-left"></i>
            Kembali ke Daftar
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
