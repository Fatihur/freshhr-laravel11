@extends('layouts.app')

@section('title', 'Edit Profil')
@section('header-title', 'Edit Profil')

@section('content')
<div class="space-y-8">
    <!-- Profile Info Card -->
    <div class="card">
        <h3 class="text-lg font-semibold text-slate-800 mb-6">Informasi Profil</h3>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="flex items-start gap-6 mb-6">
                <div class="flex flex-col items-center gap-2">
                    <img src="{{ $user->employee?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=4ade80&color=1e293b' }}"
                         alt="Avatar"
                         class="avatar-xl"
                         id="avatar-preview">
                    <label class="btn btn-sm btn-secondary" style="cursor: pointer;">
                        <i data-lucide="camera"></i>
                        Ganti Foto
                        <input type="file" name="avatar" accept="image/*" style="display: none;" onchange="previewAvatar(this)">
                    </label>
                    @error('avatar')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex-1 space-y-4">
                    <div>
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label">Nomor Telepon</label>
                        <input type="tel" name="phone" class="form-input" value="{{ old('phone', $user->employee?->phone ?? '') }}" placeholder="Opsional">
                        @error('phone')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password Card -->
    <div class="card">
        <h3 class="text-lg font-semibold text-slate-800 mb-6">Ubah Password</h3>

        <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            @method('PUT')

            <div class="space-y-4 max-w-md">
                <div>
                    <label class="form-label">Password Saat Ini</label>
                    <input type="password" name="current_password" class="form-input" required>
                    @error('current_password')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-input" required>
                    @error('password')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-input" required>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="lock"></i>
                    Ubah Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();

    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
