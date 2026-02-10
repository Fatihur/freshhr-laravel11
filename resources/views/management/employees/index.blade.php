@extends('layouts.app')

@section('title', 'Direktori Karyawan')
@section('header-title', 'Karyawan')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Direktori Karyawan</h2>
            <p class="text-sm text-slate-400">Kelola data resmi staf dan status kepegawaian.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('management.employees.create') }}" class="btn btn-primary">
                <i data-lucide="user-plus"></i> Tambah Karyawan
            </a>
        </div>
    </div>

    <!-- Search & Filter -->
    <form method="GET" action="{{ route('management.employees.index') }}" class="flex gap-4">
        <div class="flex-1" style="position: relative;">
            <i data-lucide="search" style="position: absolute; left: 24px; top: 50%; transform: translateY(-50%); color: var(--slate-400); width: 18px; height: 18px;"></i>
            <input type="text" name="search" class="form-input" style="padding-left: 56px;" placeholder="Cari nama, ID atau departemen..." value="{{ $search ?? '' }}">
        </div>
        <select name="status" class="form-select" style="width: 150px;" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="active" {{ ($status ?? '') == 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="on_leave" {{ ($status ?? '') == 'on_leave' ? 'selected' : '' }}>Cuti</option>
            <option value="terminated" {{ ($status ?? '') == 'terminated' ? 'selected' : '' }}>Tidak Aktif</option>
        </select>
        <button type="submit" class="btn btn-secondary">
            <i data-lucide="filter"></i> Filter
        </button>
    </form>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Info Karyawan</th>
                    <th>Jabatan</th>
                    <th>Tgl Bergabung</th>
                    <th style="text-align: center;">Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $emp)
                    <tr>
                        <td>
                            <div class="flex items-center gap-4">
                                <img src="{{ $emp->avatar_url }}" class="avatar" alt="" style="width: 40px; height: 40px; border-radius: 50%;">
                                <div>
                                    <h5 class="font-bold text-slate-800">{{ $emp->name }}</h5>
                                    <p style="font-size: 10px; font-weight: 700; color: var(--slate-400); text-transform: uppercase;">{{ $emp->employee_id }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-sm font-bold text-slate-700">{{ $emp->position?->name ?? '-' }}</span><br>
                            <span style="font-size: 10px; color: var(--slate-400);">{{ $emp->department?->name ?? '-' }}</span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2 text-slate-600">
                                <i data-lucide="calendar" style="width: 14px; height: 14px; color: var(--slate-300);"></i>
                                {{ $emp->join_date?->format('d M Y') ?? '-' }}
                            </div>
                        </td>
                        <td style="text-align: center;">
                            @if($emp->status == 'active')
                                <span class="badge badge-success">Aktif</span>
                            @elseif($emp->status == 'on_leave')
                                <span class="badge badge-warning">Cuti</span>
                            @else
                                <span class="badge badge-danger">Tidak Aktif</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('management.employees.edit', $emp) }}" class="btn btn-secondary btn-sm">
                                    <i data-lucide="edit-2" style="width: 14px; height: 14px;"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('management.employees.destroy', $emp) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 6px 12px;">
                                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 40px; color: var(--slate-400);">
                            <i data-lucide="users" style="width: 48px; height: 48px; margin: 0 auto 16px; opacity: 0.5;"></i>
                            <p>Belum ada data karyawan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="table-footer">
            <span class="table-footer-text">Menampilkan {{ $employees->firstItem() ?? 0 }} - {{ $employees->lastItem() ?? 0 }} dari {{ $employees->total() }} rekaman</span>
            {{ $employees->links('pagination::simple-tailwind') }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>lucide.createIcons();</script>
@endpush
