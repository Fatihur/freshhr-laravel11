@extends('layouts.app')

@section('title', 'Pengajuan Cuti')
@section('header-title', 'Cuti')

@section('content')
<div class="grid" style="grid-template-columns: 2fr 1fr; gap: 32px;">
    <!-- Left Content -->
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Pengajuan Cuti</h2>
                <p class="text-sm text-slate-400">Kelola pengajuan cuti Anda.</p>
            </div>
            <button onclick="document.getElementById('leaveModal').classList.add('active')" class="btn btn-primary" style="box-shadow: 0 10px 25px rgba(74, 222, 128, 0.3);">
                <i data-lucide="plus"></i>
                <span>Buat Pengajuan</span>
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2">
            <div class="card" style="border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.04); transition: box-shadow 0.3s;">
                <div class="flex items-center gap-4">
                    <div style="width: 48px; height: 48px; border-radius: 16px; background: #dcfce7; color: #22c55e; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="calendar"></i>
                    </div>
                    <div>
                        <p style="font-size: 10px; font-weight: 700; color: var(--slate-400); text-transform: uppercase; letter-spacing: 1px;">Sisa Cuti</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $leaveBalance['remaining'] ?? 14 }} Hari</p>
                    </div>
                </div>
                <div style="margin-top: 16px; height: 8px; width: 100%; background: var(--slate-100); border-radius: 9999px; overflow: hidden;">
                    <div style="height: 100%; width: {{ ($leaveBalance['remaining'] ?? 14) / ($leaveBalance['total'] ?? 20) * 100 }}%; background: var(--primary); transition: width 0.5s;"></div>
                </div>
            </div>
            <div class="card" style="border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.04); transition: box-shadow 0.3s;">
                <div class="flex items-center gap-4">
                    <div style="width: 48px; height: 48px; border-radius: 16px; background: #dbeafe; color: #3b82f6; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="file-text"></i>
                    </div>
                    <div>
                        <p style="font-size: 10px; font-weight: 700; color: var(--slate-400); text-transform: uppercase; letter-spacing: 1px;">Terpakai Tahun Ini</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $leaveBalance['used'] ?? 6 }} Hari</p>
                    </div>
                </div>
                <div style="margin-top: 16px; height: 8px; width: 100%; background: var(--slate-100); border-radius: 9999px; overflow: hidden;">
                    <div style="height: 100%; width: {{ ($leaveBalance['used'] ?? 6) / ($leaveBalance['total'] ?? 20) * 100 }}%; background: #3b82f6; transition: width 0.5s;"></div>
                </div>
            </div>
        </div>

        <!-- Leave Requests List -->
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                Riwayat Terbaru
            </h3>
            <div class="space-y-4">
                @forelse($leaveRequests as $request)
                    <a href="{{ route('leave.show', $request) }}" class="card" style="display: block; text-decoration: none; color: inherit; border: 2px solid white; transition: all 0.3s;"
                         onmouseenter="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 15px 40px rgba(0,0,0,0.08)';"
                         onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 30px rgba(0,0,0,0.04)';">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div style="width: 48px; height: 48px; border-radius: 16px; background: {{ $request->status === 'approved' ? '#dcfce7' : ($request->status === 'rejected' ? '#fee2e2' : '#f1f5f9') }}; color: {{ $request->status === 'approved' ? '#22c55e' : ($request->status === 'rejected' ? '#ef4444' : '#94a3b8') }}; display: flex; align-items: center; justify-content: center;">
                                    <i data-lucide="file-text"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">{{ $request->type_label }}</p>
                                    <p class="text-xs text-slate-400">{{ $request->start_date->format('d M Y') }} — {{ $request->end_date->format('d M Y') }} ({{ $request->duration }} hari)</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="badge {{ $request->status === 'approved' ? 'badge-success' : ($request->status === 'rejected' ? 'badge-danger' : 'badge-warning') }}">
                                    {{ $request->status_label }}
                                </span>
                                <i data-lucide="chevron-right" style="width: 18px; height: 18px; color: var(--slate-300);"></i>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="card text-center" style="padding: 60px;">
                        <i data-lucide="file-text" style="width: 64px; height: 64px; margin: 0 auto 20px; color: var(--slate-300);"></i>
                        <p class="text-slate-500">Belum ada pengajuan cuti</p>
                    </div>
                @endforelse
            </div>

            @if($leaveRequests->hasPages())
            <div class="flex justify-center mt-6">
                {{ $leaveRequests->links('pagination::simple-tailwind') }}
            </div>
            @endif
        </div>
    </div>

    <!-- Right Sidebar: Approval Timeline -->
    <div class="space-y-6">
        <div class="card" style="position: sticky; top: 32px; border: none; box-shadow: 0 20px 50px rgba(0,0,0,0.08);">
            <h3 class="text-lg font-bold text-slate-800 mb-8">Alur Persetujuan</h3>

            <div class="space-y-0" style="position: relative;">
                <div style="position: absolute; left: 19px; top: 16px; bottom: 16px; width: 2px; background: var(--slate-100);"></div>

                <!-- Step 1 -->
                <div class="flex items-start gap-6" style="padding-bottom: 32px;">
                    <div style="position: relative; z-index: 10; width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: var(--slate-900); display: flex; align-items: center; justify-content: center; border: 4px solid white; box-shadow: 0 4px 12px rgba(74, 222, 128, 0.3); flex-shrink: 0;">
                        <i data-lucide="check-circle-2" style="width: 18px; height: 18px;"></i>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-bold text-slate-800 truncate">Persetujuan Dept Head</p>
                        </div>
                        <p style="font-size: 10px; font-weight: 500; color: var(--slate-400); text-transform: uppercase; letter-spacing: 1px; margin-top: 2px;">Level 1: Tinjauan Manajer</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="flex items-start gap-6" style="padding-bottom: 32px;">
                    <div style="position: relative; z-index: 10; width: 40px; height: 40px; border-radius: 50%; background: var(--slate-100); color: var(--slate-300); display: flex; align-items: center; justify-content: center; border: 4px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.05); flex-shrink: 0;">
                        <i data-lucide="clock" style="width: 18px; height: 18px;"></i>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-bold text-slate-800 truncate">Verifikasi HRM</p>
                        </div>
                        <p style="font-size: 10px; font-weight: 500; color: var(--slate-400); text-transform: uppercase; letter-spacing: 1px; margin-top: 2px;">Level 2: Audit Data</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="flex items-start gap-6">
                    <div style="position: relative; z-index: 10; width: 40px; height: 40px; border-radius: 50%; background: var(--slate-100); color: var(--slate-300); display: flex; align-items: center; justify-content: center; border: 4px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.05); flex-shrink: 0;">
                        <i data-lucide="clock" style="width: 18px; height: 18px;"></i>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-bold text-slate-400 truncate">Persetujuan Akhir GM</p>
                        </div>
                        <p style="font-size: 10px; font-weight: 500; color: var(--slate-400); text-transform: uppercase; letter-spacing: 1px; margin-top: 2px;">Level 3: Rilis Langsung</p>
                    </div>
                </div>
            </div>

            <!-- Info -->
            <div style="margin-top: 40px; padding: 20px; background: var(--slate-50); border-radius: 24px; border: 1px solid var(--slate-100);">
                <div class="flex items-center gap-2 text-slate-400 mb-4">
                    <i data-lucide="info" style="width: 14px; height: 14px;"></i>
                    <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Informasi</span>
                </div>
                <p class="text-sm text-slate-600" style="line-height: 1.6;">
                    Pengajuan cuti memerlukan persetujuan dari 3 level: Kepala Departemen, HR Manager, dan General Manager.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div id="leaveModal" class="modal-overlay" onclick="if(event.target === this) this.classList.remove('active')">
    <div class="modal-content">
        <div class="card" style="border-radius: 40px; box-shadow: 0 25px 50px rgba(0,0,0,0.15); border: none; padding: 32px;">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl font-bold text-slate-800">Buat Pengajuan</h3>
                <button onclick="document.getElementById('leaveModal').classList.remove('active')" style="width: 40px; height: 40px; border-radius: 50%; background: var(--slate-50); border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--slate-400); transition: color 0.2s;">
                    <i data-lucide="x"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('leave.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="form-label">Kategori</label>
                    <div class="flex gap-2" style="overflow-x: auto; padding-bottom: 8px;">
                        @foreach(['annual' => 'Tahunan', 'sick' => 'Sakit', 'emergency' => 'Mendesak', 'maternity' => 'Melahirkan'] as $value => $label)
                            <label style="padding: 10px 24px; border-radius: 9999px; border: 2px solid var(--slate-100); font-size: 12px; font-weight: 700; white-space: nowrap; cursor: pointer; transition: all 0.2s; color: var(--slate-600);">
                                <input type="radio" name="type" value="{{ $value }}" style="display: none;" onchange="this.closest('label').style.borderColor='var(--primary)'; this.closest('label').style.background='#dcfce7';" required>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2" style="gap: 16px;">
                    <div>
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="end_date" class="form-input" required>
                    </div>
                </div>

                <div>
                    <label class="form-label">Alasan Cuti</label>
                    <textarea name="reason" class="form-textarea" placeholder="Jelaskan alasan cuti Anda..." style="border-radius: 32px;"></textarea>
                </div>

                <div>
                    <label class="form-label">Catatan Serah Terima</label>
                    <textarea name="handover_notes" class="form-textarea" placeholder="Siapa yang akan menangani tugas Anda?" style="border-radius: 32px;"></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-full" style="border-radius: 24px;">
                    Kirim Pengajuan
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 60;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-overlay::before {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(4px);
    }

    .modal-content {
        position: relative;
        width: 100%;
        max-width: 512px;
        animation: modalIn 0.3s ease-out;
    }

    @keyframes modalIn {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(20px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
</style>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
