@extends('layouts.app')

@section('title', 'Audit Log')
@section('header-title', 'Audit Log')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Audit Log</h2>
            <p class="text-sm text-slate-400">Riwayat aktivitas penting sistem.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card">
        <form method="GET" action="{{ route('management.audit_logs.index') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="form-label">Jenis Entitas</label>
                <select name="entity_type" class="form-select" style="width: 200px;">
                    <option value="">Semua</option>
                    @foreach($entityTypes as $type)
                        <option value="{{ $type }}" {{ request('entity_type') == $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Aksi</label>
                <select name="action" class="form-select" style="width: 150px;">
                    <option value="">Semua</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucfirst($action) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-input" value="{{ request('date_from') }}">
            </div>
            <div>
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-input" value="{{ request('date_to') }}">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="filter"></i>
                    Filter
                </button>
                <a href="{{ route('management.audit_logs.index') }}" class="btn btn-secondary">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Aksi</th>
                    <th>Entitas</th>
                    <th>Deskripsi</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-sm font-bold">
                                    {{ substr($log->actor->name ?? 'S', 0, 1) }}
                                </div>
                                <span>{{ $log->actor->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td>
                            @php
                                $badgeClass = match($log->action) {
                                    'create' => 'badge-success',
                                    'update', 'apply', 'submit' => 'badge-info',
                                    'delete', 'reject' => 'badge-danger',
                                    'approve' => 'badge-success',
                                    default => 'badge-warning'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                {{ ucfirst($log->action) }}
                            </span>
                        </td>
                        <td>{{ $log->entity_type }} #{{ $log->entity_id }}</td>
                        <td class="max-w-md truncate" title="{{ $log->description }}">
                            {{ $log->description ?? '-' }}
                        </td>
                        <td class="font-mono text-xs">{{ $log->ip_address }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-slate-400">
                            Tidak ada data audit log
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div class="flex justify-center">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>lucide.createIcons();</script>
@endpush
