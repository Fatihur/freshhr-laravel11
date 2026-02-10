@extends('layouts.app')

@section('title', 'Laporan Absensi')
@section('header-title', 'Laporan')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Laporan Absensi</h2>
            <p class="text-sm text-slate-400">Catatan detail untuk {{ \Carbon\Carbon::parse($date)->format('d F Y') }}.</p>
        </div>
        <div class="flex items-center gap-3">
            <form method="GET" class="flex items-center gap-2">
                <input type="date" name="date" class="form-input" value="{{ $date }}" onchange="this.form.submit()">
                <input type="hidden" name="filter" value="{{ $filter }}">
            </form>
            <a href="{{ route('reports.export', ['date' => $date]) }}" class="btn btn-secondary">
                <i data-lucide="download"></i> Ekspor CSV
            </a>
        </div>
    </div>

    <div class="flex items-center gap-3" style="overflow-x: auto; padding-bottom: 8px;">
        <a href="?date={{ $date }}" class="btn {{ $filter == 'all' ? 'btn-primary' : 'btn-secondary' }}">Semua ({{ $allEmployees }})</a>
        <a href="?date={{ $date }}&filter=present" class="btn {{ $filter == 'present' ? 'btn-primary' : 'btn-secondary' }}">Hadir</a>
        <a href="?date={{ $date }}&filter=late" class="btn {{ $filter == 'late' ? 'btn-primary' : 'btn-secondary' }}">Terlambat</a>
        <a href="?date={{ $date }}&filter=absent" class="btn {{ $filter == 'absent' ? 'btn-primary' : 'btn-secondary' }}">Tidak Hadir</a>
    </div>

    <div class="space-y-4">
        @forelse($attendances as $attendance)
            <div class="card" style="padding: 24px; border-radius: 32px; transition: all 0.3s;" onmouseenter="this.style.borderColor='#bbf7d0'; this.style.boxShadow='0 20px 40px rgba(74, 222, 128, 0.05)';" onmouseleave="this.style.borderColor='white'; this.style.boxShadow='0 8px 30px rgba(0,0,0,0.04)';">
                <div class="flex items-center justify-between" style="flex-wrap: wrap; gap: 24px;">
                    <div class="flex items-center gap-4">
                        <div style="position: relative;">
                            <img src="{{ $attendance->employee->avatar_url }}" class="avatar-lg" alt="{{ $attendance->employee->name }}" style="width: 56px; height: 56px; border-radius: 20px;">
                            <div style="position: absolute; bottom: -4px; right: -4px; width: 16px; height: 16px; border-radius: 50%; border: 2px solid white; background: {{ $attendance->status == 'present' ? '#4ade80' : ($attendance->status == 'late' ? '#facc15' : '#f87171') }};"></div>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800">{{ $attendance->employee->name }}</h4>
                            <p class="text-xs text-slate-400">{{ $attendance->employee->position?->name ?? '-' }} • {{ $attendance->employee->department?->name ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-8">
                        <div>
                            <p style="font-size: 10px; font-weight: 700; color: var(--slate-400); text-transform: uppercase;">Jam Masuk</p>
                            <p class="font-bold text-slate-700">{{ $attendance->time_in?->format('H:i') ?? '-' }}</p>
                        </div>
                        <div>
                            <p style="font-size: 10px; font-weight: 700; color: var(--slate-400); text-transform: uppercase;">Jam Pulang</p>
                            <p class="font-bold text-slate-700">{{ $attendance->time_out?->format('H:i') ?? '-' }}</p>
                        </div>
                        <div>
                            <p style="font-size: 10px; font-weight: 700; color: var(--slate-400); text-transform: uppercase;">Lokasi</p>
                            <p class="font-bold text-slate-700">{{ $attendance->location ?? '-' }}</p>
                        </div>
                        <span class="badge {{ $attendance->status == 'present' ? 'badge-success' : ($attendance->status == 'late' ? 'badge-warning' : 'badge-danger') }}">
                            {{ $attendance->status_label }}
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="card text-center" style="padding: 60px;">
                <i data-lucide="file-text" style="width: 64px; height: 64px; margin: 0 auto 20px; color: var(--slate-300);"></i>
                <p class="text-slate-500">Tidak ada data absensi untuk tanggal ini</p>
            </div>
        @endforelse
    </div>

    <div class="flex items-center justify-between px-6">
        <p class="text-xs text-slate-400">Menampilkan <b>{{ $attendances->count() }}</b> dari {{ $allEmployees }} karyawan</p>
        <div>
            {{ $attendances->links('pagination::simple-tailwind') }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>lucide.createIcons();</script>
@endpush
