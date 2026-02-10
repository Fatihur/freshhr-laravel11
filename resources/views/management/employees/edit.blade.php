@extends('layouts.app')

@section('title', 'Edit Karyawan')
@section('header-title', 'Edit Karyawan')

@section('content')
<div style="max-width: 600px;">
    <div class="card">
        <h3 class="text-xl font-bold text-slate-800 mb-6">Edit Data Karyawan</h3>

        <form method="POST" action="{{ route('management.employees.update', $employee) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="form-label">ID Karyawan</label>
                <input type="text" class="form-input" value="{{ $employee->employee_id }}" disabled>
            </div>

            <div>
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $employee->name) }}" required>
            </div>

            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" value="{{ old('email', $employee->email) }}" required>
            </div>

            <div>
                <label class="form-label">No. Telepon</label>
                <input type="text" name="phone" class="form-input" value="{{ old('phone', $employee->phone) }}">
            </div>

            <div class="grid grid-cols-2" style="gap: 16px;">
                <div>
                    <label class="form-label">Departemen</label>
                    <select name="department_id" class="form-select" required>
                        <option value="">Pilih Departemen</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Jabatan</label>
                    <select name="position_id" class="form-select" required>
                        <option value="">Pilih Jabatan</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}" {{ old('position_id', $employee->position_id) == $pos->id ? 'selected' : '' }}>
                                {{ $pos->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="on_leave" {{ old('status', $employee->status) == 'on_leave' ? 'selected' : '' }}>Cuti</option>
                    <option value="terminated" {{ old('status', $employee->status) == 'terminated' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>

            <div>
                <label class="form-label">Tanggal Bergabung</label>
                <input type="date" name="join_date" class="form-input" value="{{ old('join_date', $employee->join_date?->format('Y-m-d')) }}" required>
            </div>

            <div>
                <label class="form-label">Alamat</label>
                <textarea name="address" class="form-textarea">{{ old('address', $employee->address) }}</textarea>
            </div>

            <div class="flex gap-3 mt-6">
                <a href="{{ route('management.employees.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary flex-1">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
