@extends('layouts.app')

@section('title', 'Tambah Pengguna')
@section('header-title', 'Tambah Pengguna')

@section('content')
<div style="max-width: 500px;">
    <div class="card">
        <h3 class="text-xl font-bold text-slate-800 mb-6">Pengguna Baru</h3>
        
        <form method="POST" action="{{ route('management.users.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="form-label">Nama</label>
                <input type="text" name="name" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Peran</label>
                <select name="role" class="form-select" required>
                    <option value="employee">Employee</option>
                    <option value="dept_head">Dept Head</option>
                    <option value="hr_admin">HR Admin</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>
            <div>
                <label class="form-label">Hubungkan ke Karyawan (Opsional)</label>
                <select name="employee_id" class="form-select">
                    <option value="">-- Tidak Ada --</option>
                    @foreach($employees ?? [] as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3 mt-6">
                <a href="{{ route('management.users.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary flex-1">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
