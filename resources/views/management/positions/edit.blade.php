@extends('layouts.app')

@section('title', 'Edit Jabatan')
@section('header-title', 'Edit Jabatan')

@section('content')
<div style="max-width: 600px;">
    <div class="card">
        <h3 class="text-xl font-bold text-slate-800 mb-6">Edit Data Jabatan</h3>

        <form method="POST" action="{{ route('management.positions.update', $position) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="form-label">Nama Jabatan</label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $position->name) }}" required>
            </div>

            <div>
                <label class="form-label">Departemen</label>
                <select name="department_id" class="form-select" required>
                    <option value="">Pilih Departemen</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id', $position->department_id) == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Level (1-10)</label>
                <input type="number" name="level" class="form-input" min="1" max="10" value="{{ old('level', $position->level) }}" required>
                <p class="text-xs text-slate-400 mt-1">Level 1 = Entry level, Level 10 = C-Level</p>
            </div>

            <div>
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-textarea">{{ old('description', $position->description) }}</textarea>
            </div>

            <div class="flex gap-3 mt-6">
                <a href="{{ route('management.positions.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary flex-1">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
