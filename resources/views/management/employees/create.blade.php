@extends('layouts.app')

@section('title', 'Tambah Karyawan')
@section('header-title', 'Tambah Karyawan')

@section('content')
<div style="max-width: 600px;">
    <div class="card">
        <h3 class="text-xl font-bold text-slate-800 mb-6">Data Karyawan Baru</h3>
        
        <form method="POST" action="{{ route('management.employees.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" required>
            </div>
            <div>
                <label class="form-label">No. Telepon</label>
                <input type="text" name="phone" class="form-input">
            </div>
            <div class="grid grid-cols-2" style="gap: 16px;">
                <div>
                    <label class="form-label">Departemen</label>
                    <select name="department_id" class="form-select" required>
                        <option value="">Pilih Departemen</option>
                        @foreach($departments ?? [] as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Jabatan</label>
                    <select name="position_id" class="form-select" required>
                        <option value="">Pilih Jabatan</option>
                        @foreach($positions ?? [] as $pos)
                            <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="form-label">Tanggal Bergabung</label>
                <input type="date" name="join_date" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Alamat</label>
                <textarea name="address" class="form-textarea"></textarea>
            </div>
            <div class="flex gap-3 mt-6">
                <a href="{{ route('management.employees.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary flex-1">Simpan Karyawan</button>
            </div>
        </form>
    </div>
</div>
@endsection
