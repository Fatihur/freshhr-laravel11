@extends('layouts.app')

@section('title', 'Manajemen Jabatan')
@section('header-title', 'Jabatan')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Manajemen Jabatan</h2>
            <p class="text-sm text-slate-400">Kelola struktur jabatan dan level organisasi.</p>
        </div>
        <a href="{{ route('management.positions.create') }}" class="btn btn-primary">
            <i data-lucide="plus"></i> Tambah Jabatan
        </a>
    </div>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama Jabatan</th>
                    <th>Departemen</th>
                    <th>Level</th>
                    <th style="text-align: center;">Jumlah Karyawan</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($positions as $pos)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div style="width: 40px; height: 40px; border-radius: 12px; background: #dbeafe; color: #3b82f6; display: flex; align-items: center; justify-content: center;">
                                    <i data-lucide="briefcase" style="width: 20px; height: 20px;"></i>
                                </div>
                                <span class="font-bold text-slate-800">{{ $pos->name }}</span>
                            </div>
                        </td>
                        <td class="text-slate-600">{{ $pos->department?->name ?? '-' }}</td>
                        <td><span class="badge badge-info">Level {{ $pos->level }}</span></td>
                        <td style="text-align: center;" class="font-bold">{{ $pos->employees_count ?? 0 }}</td>
                        <td style="text-align: right;">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('management.positions.edit', $pos) }}" class="btn btn-secondary btn-sm">
                                    <i data-lucide="edit-2" style="width: 14px; height: 14px;"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('management.positions.destroy', $pos) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jabatan ini?')">
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
                            <i data-lucide="briefcase" style="width: 48px; height: 48px; margin: 0 auto 16px; opacity: 0.5;"></i>
                            <p>Belum ada data jabatan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($positions instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="table-footer">
            <span class="table-footer-text">Menampilkan {{ $positions->firstItem() ?? 0 }} - {{ $positions->lastItem() ?? 0 }} dari {{ $positions->total() }} rekaman</span>
            {{ $positions->links('pagination::simple-tailwind') }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>lucide.createIcons();</script>
@endpush
