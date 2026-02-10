@extends('layouts.app')

@section('title', 'Tambah Jabatan')
@section('header-title', 'Tambah Jabatan')

@section('content')
<div style="max-width: 500px;">
    <div class="card">
        <h3 class="text-xl font-bold text-slate-800 mb-6">Jabatan Baru</h3>
        
        <form method="POST" action="{{ route('management.positions.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="form-label">Nama Jabatan</label>
                <input type="text" name="name" class="form-input" required>
            </div>
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
                <label class="form-label">Level (1-10)</label>
                <input type="number" name="level" class="form-input" min="1" max="10" value="1" required>
            </div>
            <div>
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-textarea"></textarea>
            </div>
            <div class="flex gap-3 mt-6">
                <a href="{{ route('management.positions.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary flex-1">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
