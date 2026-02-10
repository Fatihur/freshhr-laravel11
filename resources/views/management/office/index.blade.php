@extends('layouts.app')

@section('title', 'Pengaturan Kantor')
@section('header-title', 'Pengaturan Kantor')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 400px;
        border-radius: 20px;
        margin-bottom: 20px;
    }
    .map-controls {
        display: flex;
        gap: 8px;
        margin-bottom: 16px;
    }
    .map-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 50px;
        border: 1px solid var(--slate-200);
        background: white;
        font-size: 13px;
        font-weight: 500;
        color: var(--slate-600);
        cursor: pointer;
        transition: all 0.2s;
    }
    .map-btn:hover {
        background: var(--slate-50);
        border-color: var(--primary);
        color: var(--slate-800);
    }
    .map-btn.primary {
        background: var(--primary);
        border-color: var(--primary);
        color: var(--slate-900);
    }
    .map-btn.primary:hover {
        background: var(--primary-dark);
    }
    .radius-circle-info {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 16px;
        background: #dbeafe;
        border-radius: 12px;
        font-size: 13px;
        color: #1e40af;
        margin-top: 12px;
    }
</style>
@endpush

@section('content')
<div class="space-y-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Pengaturan Kantor</h2>
        <p class="text-sm text-slate-400">Konfigurasi lokasi kantor dan pengaturan shift.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: #dcfce7; color: #166534; padding: 12px 16px; border-radius: 12px; margin-bottom: 16px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error" style="background: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 12px; margin-bottom: 16px;">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error" style="background: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 12px; margin-bottom: 16px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-2" style="gap: 32px;">
        <!-- Office Location -->
        <div class="card">
            <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                <i data-lucide="map-pin" style="color: var(--primary);"></i>
                Lokasi Kantor
            </h3>
            <form method="POST" action="{{ route('management.office.update') }}" class="space-y-4" id="officeForm">
                @csrf
                @method('PUT')

                <div>
                    <label class="form-label">Nama Kantor</label>
                    <input type="text" name="name" class="form-input" value="{{ $officeSettings->name ?? 'Kantor Pusat' }}">
                </div>

                <!-- Map -->
                <div>
                    <label class="form-label">Pilih Lokasi di Peta</label>
                    <div class="map-controls">
                        <button type="button" class="map-btn primary" onclick="getCurrentLocation()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M12 1v6m0 6v10m11-11h-6m-6 0H1"></path></svg>
                            Gunakan Lokasi Saat Ini
                        </button>
                        <button type="button" class="map-btn" onclick="resetMap()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
                            Reset
                        </button>
                    </div>
                    <div id="map"></div>
                    <div class="radius-circle-info">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4m0-4h.01"></path></svg>
                        <span>Lingkaran hijau menunjukkan radius absensi. Seret marker untuk mengubah lokasi.</span>
                    </div>
                </div>

                <div class="grid grid-cols-2" style="gap: 16px;">
                    <div>
                        <label class="form-label">Latitude</label>
                        <input type="number" step="any" name="latitude" id="latitude" class="form-input" value="{{ $officeSettings->latitude ?? '-6.2088' }}">
                    </div>
                    <div>
                        <label class="form-label">Longitude</label>
                        <input type="number" step="any" name="longitude" id="longitude" class="form-input" value="{{ $officeSettings->longitude ?? '106.8456' }}">
                    </div>
                </div>
                <div class="grid grid-cols-2" style="gap: 16px;">
                    <div>
                        <label class="form-label">Radius (meter)</label>
                        <input type="number" name="radius" class="form-input" value="{{ $officeSettings->radius ?? '100' }}">
                    </div>
                    <div>
                        <label class="form-label">Toleransi (meter)</label>
                        <input type="number" name="tolerance" class="form-input" value="{{ $officeSettings->tolerance ?? '10' }}">
                    </div>
                </div>
                <div class="grid grid-cols-2" style="gap: 16px;">
                    <div>
                        <label class="form-label">Jam Masuk</label>
                        <input type="time" name="work_start_time" class="form-input" value="{{ $officeSettings->work_start_time ?? '09:00' }}">
                    </div>
                    <div>
                        <label class="form-label">Jam Pulang</label>
                        <input type="time" name="work_end_time" class="form-input" value="{{ $officeSettings->work_end_time ?? '17:00' }}">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-full">Simpan Pengaturan</button>
            </form>
        </div>

        <!-- Shifts -->
        <div class="card">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="clock" style="color: var(--primary);"></i>
                    Daftar Shift
                </h3>
                <button class="btn btn-primary btn-sm" onclick="document.getElementById('shiftModal').classList.add('active')">
                    <i data-lucide="plus"></i> Tambah
                </button>
            </div>
            <div class="space-y-3">
                @php
                    $mockShifts = [
                        ['name' => 'Shift Pagi', 'time' => '09:00 - 17:00'],
                        ['name' => 'Shift Siang', 'time' => '13:00 - 21:00'],
                        ['name' => 'Shift Malam', 'time' => '21:00 - 05:00'],
                    ];
                @endphp
                @foreach($mockShifts as $shift)
                    <div class="flex items-center justify-between p-4" style="background: var(--slate-50); border-radius: 16px;">
                        <div>
                            <p class="font-bold text-slate-800">{{ $shift['name'] }}</p>
                            <p class="text-xs text-slate-400">{{ $shift['time'] }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="btn btn-secondary btn-sm"><i data-lucide="edit-2" style="width:14px;height:14px;"></i></button>
                            <button class="btn btn-secondary btn-sm" style="color: #ef4444;"><i data-lucide="trash-2" style="width:14px;height:14px;"></i></button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Shift Modal -->
<div id="shiftModal" class="modal-overlay" onclick="if(event.target === this) this.classList.remove('active')">
    <div class="modal-content" style="max-width: 400px;">
        <div class="card" style="border-radius: 32px;">
            <h3 class="text-xl font-bold text-slate-800 mb-6">Tambah Shift</h3>
            <form method="POST" action="{{ route('management.office.shifts.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label">Nama Shift</label>
                    <input type="text" name="name" class="form-input" required>
                </div>
                <div class="grid grid-cols-2" style="gap: 16px;">
                    <div>
                        <label class="form-label">Jam Mulai</label>
                        <input type="time" name="start_time" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Jam Selesai</label>
                        <input type="time" name="end_time" class="form-input" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-full">Simpan</button>
            </form>
        </div>
    </div>
</div>

<style>
.modal-overlay { position: fixed; inset: 0; z-index: 60; display: none; align-items: center; justify-content: center; padding: 16px; }
.modal-overlay.active { display: flex; }
.modal-overlay::before { content: ''; position: absolute; inset: 0; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); }
.modal-content { position: relative; width: 100%; }
</style>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let map, marker, circle;
    const defaultLat = {{ $officeSettings->latitude ?? -6.2088 }};
    const defaultLng = {{ $officeSettings->longitude ?? 106.8456 }};
    const defaultRadius = {{ $officeSettings->radius ?? 100 }};

    function initMap() {
        // Initialize map
        map = L.map('map').setView([defaultLat, defaultLng], 15);

        // Add tile layer (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Add marker
        marker = L.marker([defaultLat, defaultLng], {
            draggable: true
        }).addTo(map);

        // Add radius circle
        circle = L.circle([defaultLat, defaultLng], {
            color: '#4ade80',
            fillColor: '#4ade80',
            fillOpacity: 0.2,
            radius: defaultRadius
        }).addTo(map);

        // Update inputs when marker is dragged
        marker.on('dragend', function(e) {
            const pos = marker.getLatLng();
            updateInputs(pos.lat, pos.lng);
            updateCircle(pos.lat, pos.lng);
        });

        // Update marker and inputs when map is clicked
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateInputs(e.latlng.lat, e.latlng.lng);
            updateCircle(e.latlng.lat, e.latlng.lng);
        });

        // Update circle when radius input changes
        document.querySelector('input[name="radius"]').addEventListener('input', function(e) {
            const radius = parseInt(e.target.value) || 100;
            circle.setRadius(radius);
        });
    }

    function updateInputs(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(6);
        document.getElementById('longitude').value = lng.toFixed(6);
    }

    function updateCircle(lat, lng) {
        circle.setLatLng([lat, lng]);
    }

    function getCurrentLocation() {
        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung geolocation.');
            return;
        }

        const btn = document.querySelector('.map-btn.primary');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite;"><circle cx="12" cy="12" r="10" stroke-dasharray="60" stroke-dashoffset="20"></circle></svg> Mendeteksi...';

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                // Update map view
                map.setView([lat, lng], 16);
                marker.setLatLng([lat, lng]);
                updateCircle(lat, lng);
                updateInputs(lat, lng);

                btn.innerHTML = originalText;
                alert('Lokasi berhasil dideteksi!');
            },
            function(error) {
                btn.innerHTML = originalText;
                let message = 'Tidak dapat mendeteksi lokasi.';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        message = 'Anda menolak izin akses lokasi.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message = 'Informasi lokasi tidak tersedia.';
                        break;
                    case error.TIMEOUT:
                        message = 'Waktu deteksi lokasi habis.';
                        break;
                }
                alert(message);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }

    function resetMap() {
        map.setView([defaultLat, defaultLng], 15);
        marker.setLatLng([defaultLat, defaultLng]);
        updateCircle(defaultLat, defaultLng);
        updateInputs(defaultLat, defaultLng);
    }

    // Add spin animation
    const style = document.createElement('style');
    style.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
    document.head.appendChild(style);

    // Initialize map when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initMap();

        // Form validation before submit
        const officeForm = document.getElementById('officeForm');
        officeForm.addEventListener('submit', function(e) {
            const lat = document.getElementById('latitude').value;
            const lng = document.getElementById('longitude').value;
            const radius = document.querySelector('input[name="radius"]').value;

            console.log('Form submission data:', {
                latitude: lat,
                longitude: lng,
                radius: radius
            });

            if (!lat || !lng || lat === '' || lng === '') {
                e.preventDefault();
                alert('Error: Koordinat lokasi tidak valid. Pastikan peta sudah dimuat dengan benar.');
                return false;
            }

            if (parseFloat(lat) === 0 && parseFloat(lng) === 0) {
                e.preventDefault();
                alert('Error: Koordinat tidak boleh 0,0. Silakan pilih lokasi di peta.');
                return false;
            }
        });
    });

    // Initialize lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
@endpush
