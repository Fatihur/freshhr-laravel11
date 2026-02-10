@extends('layouts.app')

@section('title', 'Pengguna Sistem')
@section('header-title', 'Pengguna Sistem')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Pengguna Sistem</h2>
            <p class="text-sm text-slate-400">Kelola akses pengguna dan peran dalam sistem.</p>
        </div>
        <a href="{{ route('management.users.create') }}" class="btn btn-primary">
            <i data-lucide="user-plus"></i> Tambah Pengguna
        </a>
    </div>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Peran</th>
                    <th>Terhubung ke Karyawan</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: var(--slate-900); display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px;">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <span class="font-bold text-slate-800">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="text-slate-600">{{ $user->email }}</td>
                        <td>
                            @php
                                $roleLabels = [
                                    'super_admin' => ['label' => 'Super Admin', 'class' => 'badge-danger'],
                                    'hr_admin' => ['label' => 'HR Admin', 'class' => 'badge-info'],
                                    'dept_head' => ['label' => 'Kepala Dept', 'class' => 'badge-success'],
                                    'employee' => ['label' => 'Karyawan', 'class' => 'badge-warning'],
                                ];
                                $roleInfo = $roleLabels[$user->role] ?? ['label' => $user->role, 'class' => 'badge-secondary'];
                            @endphp
                            <span class="badge {{ $roleInfo['class'] }}">{{ $roleInfo['label'] }}</span>
                        </td>
                        <td>
                            @if($user->employee)
                                <div class="flex items-center gap-2">
                                    <i data-lucide="link" style="width: 14px; height: 14px; color: var(--primary);"></i>
                                    <span class="text-sm text-slate-600">{{ $user->employee->name }}</span>
                                </div>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('management.users.edit', $user) }}" class="btn btn-secondary btn-sm">
                                    <i data-lucide="edit-2" style="width: 14px; height: 14px;"></i> Edit
                                </a>
                                @if(auth()->id() !== $user->id)
                                    <form method="POST" action="{{ route('management.users.destroy', $user) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="padding: 6px 12px;">
                                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 40px; color: var(--slate-400);">
                            <i data-lucide="shield-check" style="width: 48px; height: 48px; margin: 0 auto 16px; opacity: 0.5;"></i>
                            <p>Belum ada data pengguna</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="table-footer">
            <span class="table-footer-text">Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} rekaman</span>
            {{ $users->links('pagination::simple-tailwind') }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>lucide.createIcons();</script>
@endpush
