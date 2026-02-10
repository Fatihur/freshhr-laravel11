@extends('layouts.app')

@section('title', 'Edit Pengguna')
@section('header-title', 'Edit Pengguna')

@section('content')
<div style="max-width: 600px;">
    <div class="card">
        <h3 class="text-xl font-bold text-slate-800 mb-6">Edit Data Pengguna</h3>

        <form method="POST" action="{{ route('management.users.update', $user) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
            </div>

            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
            </div>

            <div>
                <label class="form-label">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                <input type="password" name="password" class="form-input" placeholder="Min. 8 karakter">
            </div>

            <div>
                <label class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" class="form-input">
            </div>

            <div>
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>
                    <option value="super_admin" {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="hr_admin" {{ old('role', $user->role) == 'hr_admin' ? 'selected' : '' }}>HR Admin</option>
                    <option value="dept_head" {{ old('role', $user->role) == 'dept_head' ? 'selected' : '' }}>Kepala Departemen</option>
                    <option value="employee" {{ old('role', $user->role) == 'employee' ? 'selected' : '' }}>Karyawan</option>
                </select>
            </div>

            <div>
                <label class="form-label">Hubungkan ke Karyawan (opsional)</label>
                <select name="employee_id" class="form-select">
                    <option value="">-- Tidak Dihubungkan --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ old('employee_id', $user->employee_id) == $emp->id ? 'selected' : '' }}>
                            {{ $emp->name }} ({{ $emp->employee_id }})
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-400 mt-1">Pilih karyawan yang akan dihubungkan ke akun ini</p>
            </div>

            <div class="flex gap-3 mt-6">
                <a href="{{ route('management.users.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary flex-1">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
