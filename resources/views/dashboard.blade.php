@extends('layouts.app')

@section('title', 'Dashboard')
@section('header-title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Stat Cards -->
    <div class="grid grid-cols-4">
        <div class="stat-card">
            <div class="stat-card-icon green">
                <i data-lucide="users"></i>
            </div>
            <div>
                <div class="stat-card-value">{{ $stats['present'] }}</div>
                <div class="stat-card-label">Hadir Hari Ini</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon red">
                <i data-lucide="clock"></i>
            </div>
            <div>
                <div class="stat-card-value">{{ $stats['late'] }}</div>
                <div class="stat-card-label">Terlambat</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon blue">
                <i data-lucide="check-circle-2"></i>
            </div>
            <div>
                <div class="stat-card-value">{{ $stats['on_leave'] }}</div>
                <div class="stat-card-label">Sedang Cuti</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon yellow">
                <i data-lucide="x-circle"></i>
            </div>
            <div>
                <div class="stat-card-value">{{ $stats['absent'] }}</div>
                <div class="stat-card-label">Tidak Hadir</div>
            </div>
        </div>
    </div>

    <!-- Charts & Recent Activity -->
    <div class="dashboard-split-grid">
        <!-- Weekly Chart -->
        <div class="card">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-semibold text-slate-800">Absensi Mingguan</h3>
                <span class="badge badge-success">Hadir</span>
            </div>
            <div style="height: 300px; display: flex; align-items: flex-end; gap: 24px; padding: 0 16px;">
                @php
                    $maxCount = max(array_column($weeklyData, 'count'));
                    $maxCount = $maxCount > 0 ? $maxCount : 1;
                @endphp
                @foreach($weeklyData as $index => $day)
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 8px;">
                        <div style="width: 100%; height: {{ ($day['count'] / $maxCount) * 250 }}px; background: {{ $index === 2 ? '#4ade80' : '#e2e8f0' }}; border-radius: 10px; transition: all 0.3s;"></div>
                        <span class="text-xs text-slate-400">{{ $day['name'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Logs -->
        <div class="card">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-slate-800">Catatan Terbaru</h3>
                <a href="{{ route('reports.index') }}" class="text-xs font-bold text-green-500 hover:text-green-600">Lihat Semua</a>
            </div>
            <div class="space-y-6">
                @forelse($recentLogs as $log)
                    <div class="flex items-center justify-between group" style="cursor: pointer;">
                        <div class="flex items-center gap-3">
                            <img src="{{ $log->employee->avatar_url }}" class="avatar" alt="{{ $log->employee->name }}" style="border: 2px solid var(--slate-50); border-radius: 50%; width: 40px; height: 40px;">
                            <div>
                                <p class="text-sm font-semibold text-slate-800" style="transition: color 0.2s;">{{ $log->employee->name }}</p>
                                <p style="font-size: 10px; color: var(--slate-400);">
                                    {{ $log->time_in?->format('H:i') ?? '-' }} AM • {{ $log->location ?? 'Kantor' }}
                                </p>
                            </div>
                        </div>
                        <span class="badge {{ $log->status === 'present' ? 'badge-success' : 'badge-warning' }}">
                            {{ $log->status === 'present' ? 'Hadir' : 'Terlambat' }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400 text-center">Belum ada data absensi hari ini</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
