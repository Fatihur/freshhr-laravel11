@extends('layouts.app')

@section('title', 'Absensi')
@section('header-title', 'Absensi')

@php
    $hasCheckIn = $todayAttendance && $todayAttendance->time_in;
    $hasCheckOut = $todayAttendance && $todayAttendance->time_out;
    $isInvalid = $todayAttendance && str_starts_with($todayAttendance->status_in ?? '', 'invalid');
    $initialStatus = $hasCheckOut ? 'SUCCESS' : ($isInvalid ? 'INVALID' : ($hasCheckIn ? 'CHECKOUT' : 'IDLE'));
@endphp

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .attendance-container { max-width: 448px; margin: 0 auto; }
    #attendanceMap, #attendanceMapCheckout { height: 176px; width: 100%; border-radius: 40px; z-index: 1; }

    .location-badge {
        position: relative;
        margin-top: -40px;
        margin-left: 16px;
        z-index: 10;
        display: inline-flex;
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(12px);
        padding: 6px 16px;
        border-radius: 50px;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .location-badge-dot { width: 8px; height: 8px; border-radius: 50%; background: #22c55e; }
    .location-badge-text { font-size: 10px; font-weight: 700; color: var(--slate-800); text-transform: uppercase; letter-spacing: 1px; }

    .time-display { font-size: 48px; font-weight: 700; color: var(--slate-800); letter-spacing: -2px; font-variant-numeric: tabular-nums; }
    .date-display { font-size: 14px; font-weight: 500; color: var(--slate-400); text-transform: uppercase; letter-spacing: 2px; }

    .camera-viewport {
        width: 256px; height: 256px; background: var(--slate-100); border-radius: 60px;
        overflow: hidden; position: relative; box-shadow: 0 25px 50px rgba(0,0,0,0.12); border: 4px solid white;
    }
    .camera-viewport video, .camera-viewport img { width: 100%; height: 100%; object-fit: cover; }
    .camera-viewport video { transform: scaleX(-1); }

    .camera-placeholder {
        width: 100%; height: 100%; display: flex; flex-direction: column;
        align-items: center; justify-content: center; color: var(--slate-300);
    }
    .camera-placeholder svg { width: 48px; height: 48px; margin-bottom: 8px; }

    .capture-btn {
        position: absolute; bottom: 24px; left: 50%; transform: translateX(-50%);
        width: 56px; height: 56px; background: white; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15); cursor: pointer; border: none;
    }
    .capture-btn:active { transform: translateX(-50%) scale(0.9); }
    .capture-btn-inner { width: 40px; height: 40px; border: 4px solid var(--primary); border-radius: 50%; }

    .retake-btn {
        position: absolute; top: 16px; right: 16px; width: 40px; height: 40px;
        background: rgba(0,0,0,0.5); border: none; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; cursor: pointer; color: white;
    }

    .success-screen {
        min-height: 70vh; display: flex; flex-direction: column;
        align-items: center; justify-content: center; text-align: center; padding: 0 16px;
    }
    .success-icon {
        width: 96px; height: 96px; background: var(--primary); color: white; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 20px 40px rgba(74, 222, 128, 0.4); margin-bottom: 32px;
        animation: scaleIn 0.5s ease-out;
    }
    @keyframes scaleIn { from { transform: scale(0); } to { transform: scale(1); } }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Camera States */
    .camera-loading { display: none; }
    .camera-loading.active { display: flex; }
    .camera-preview { display: none; }
    .camera-preview.active { display: block; }
    .camera-video { display: none; }
    .camera-video.active { display: block; }
    .camera-idle { display: flex; }
    .camera-idle.hidden { display: none; }
</style>
@endpush

@section('content')
<div class="attendance-container" id="attendanceApp" data-status="{{ $initialStatus }}">

    @if($initialStatus === 'SUCCESS')
    {{-- SUCCESS SCREEN --}}
    <div class="success-screen">
        <div class="success-icon">
            <i data-lucide="check" style="width: 48px; height: 48px;"></i>
        </div>
        <h2 class="text-3xl font-bold text-slate-800 mb-2">Absensi Selesai!</h2>
        <p class="text-slate-400 mb-8">Anda telah melakukan absen masuk dan pulang hari ini.</p>
        @if($todayAttendance)
        <div class="card mb-8" style="width: 100%; max-width: 320px;">
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase">Masuk</span>
                    <span class="font-bold text-slate-800">{{ $todayAttendance->time_in }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase">Pulang</span>
                    <span class="font-bold text-slate-800">{{ $todayAttendance->time_out }}</span>
                </div>
                <div style="padding-top: 16px; border-top: 1px solid var(--slate-50);">
                    <span class="badge badge-success">Selesai</span>
                </div>
            </div>
        </div>
        @endif
        <a href="{{ route('dashboard') }}" class="btn btn-secondary" style="width: 100%; max-width: 320px;">Kembali ke Dashboard</a>
    </div>

    @elseif($initialStatus === 'INVALID')
    {{-- INVALID SCREEN --}}
    <div class="space-y-8" style="padding-bottom: 80px;">
        <div class="alert alert-error">
            <i data-lucide="alert-triangle"></i>
            <div>
                <strong>Absensi Ditolak</strong><br>
                {{ $todayAttendance?->rejection_reason ?? 'Anda tidak dapat melakukan absensi.' }}
            </div>
        </div>
        <div class="text-center">
            <p class="text-slate-500 mb-4">Silakan hubungi admin atau coba lagi.</p>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
        </div>
    </div>

    @elseif($initialStatus === 'CHECKOUT')
    {{-- CHECKOUT SCREEN --}}
    <div class="space-y-8" style="padding-bottom: 80px;">
        <div class="alert alert-info">
            <i data-lucide="info"></i>
            <div>
                <strong>Absen Masuk Tercatat</strong><br>
                Anda sudah absen masuk pukul {{ $todayAttendance?->time_in }}. Silakan lakukan absen pulang.
            </div>
        </div>

        <div id="attendanceMapCheckout"></div>
        <div class="location-badge">
            <div class="location-badge-dot"></div>
            <span class="location-badge-text" id="locationTextCheckout">Memuat lokasi...</span>
        </div>

        <div class="text-center space-y-2">
            <div class="time-display" id="currentTime">--:--:--</div>
            <div class="date-display" id="currentDate">-----------</div>
        </div>

        <div class="flex justify-center">
            <div class="camera-viewport">
                {{-- Loading State --}}
                <div class="camera-placeholder camera-loading" id="cameraLoadingCheckout">
                    <div style="width: 40px; height: 40px; border: 3px solid var(--slate-200); border-top-color: var(--primary); border-radius: 50%; animation: spin 1s linear infinite;"></div>
                    <span style="margin-top: 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Memuat Kamera...</span>
                </div>
                {{-- Captured Image --}}
                <img class="camera-preview" id="capturedImageCheckout" alt="Captured">
                {{-- Video Stream --}}
                <video class="camera-video" id="videoCheckout" autoplay playsinline muted></video>
                {{-- Idle State --}}
                <div class="camera-placeholder camera-idle" id="cameraIdleCheckout">
                    <i data-lucide="camera" style="width: 48px; height: 48px;"></i>
                    <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Kamera Verifikasi</span>
                </div>
                {{-- Capture Button --}}
                <button class="capture-btn" id="captureBtnCheckout" style="display: none;">
                    <div class="capture-btn-inner"></div>
                </button>
                {{-- Retake Button --}}
                <button class="retake-btn" id="retakeBtnCheckout" style="display: none;">
                    <i data-lucide="refresh-cw" style="width: 18px; height: 18px;"></i>
                </button>
            </div>
        </div>

        <div id="cameraErrorCheckout" class="alert alert-error" style="display: none;"></div>

        <div class="space-y-4" id="checkoutActions">
            <button id="startCameraCheckout" class="btn btn-primary btn-lg w-full" style="border-radius: 32px;">
                <span style="display: flex; align-items: center; gap: 8px; justify-content: center;">
                    <i data-lucide="camera" style="width: 20px; height: 20px;"></i>
                    Verifikasi & Pulang
                </span>
            </button>
        </div>

        <form method="POST" action="{{ route('attendance.checkout') }}" id="checkoutForm" style="display: none;">
            @csrf
            <input type="hidden" name="latitude" id="checkoutLat">
            <input type="hidden" name="longitude" id="checkoutLng">
            <input type="hidden" name="photo" id="checkoutPhoto">
            <button type="submit" class="btn btn-primary btn-lg w-full" style="border-radius: 32px;">Konfirmasi Pulang</button>
        </form>
    </div>

    @else
    {{-- IDLE SCREEN (CHECK IN) --}}
    <div class="space-y-8" style="padding-bottom: 80px;">
        @if(!auth()->user()?->employee)
        <div class="alert alert-error">
            <i data-lucide="alert-triangle"></i>
            <div>
                <strong>Akun Belum Terhubung</strong><br>
                Akun Anda belum terhubung ke data karyawan. Silakan hubungi admin.
            </div>
        </div>
        @endif

        <div id="attendanceMap"></div>
        <div class="location-badge">
            <div class="location-badge-dot"></div>
            <span class="location-badge-text" id="locationText">Mendeteksi lokasi...</span>
        </div>

        {{-- Debug Info untuk Admin --}}
        @if(auth()->user()?->role === 'super_admin')
        <div class="card" style="background: var(--slate-50); border: 1px solid var(--slate-200); padding: 12px; border-radius: 16px; margin-top: 8px;">
            <p class="text-xs font-bold text-slate-400 uppercase mb-2">Debug Lokasi (Admin Only)</p>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div>
                    <span class="text-slate-400">Lokasi Anda:</span><br>
                    <span id="debugUserLat" class="font-mono">--</span>, <span id="debugUserLng" class="font-mono">--</span>
                </div>
                <div>
                    <span class="text-slate-400">Lokasi Kantor:</span><br>
                    <span class="font-mono">{{ $officeSettings->latitude ?? '--' }}</span>, <span class="font-mono">{{ $officeSettings->longitude ?? '--' }}</span>
                </div>
                <div>
                    <span class="text-slate-400">Jarak:</span><br>
                    <span id="debugDistance" class="font-mono">--</span> m
                </div>
                <div>
                    <span class="text-slate-400">Radius:</span><br>
                    <span class="font-mono">{{ $officeSettings->radius ?? 100 }}</span> m
                </div>
            </div>
            <form method="POST" action="{{ route('management.office.update') }}" style="margin-top: 12px;">
                @csrf
                @method('PUT')
                <input type="hidden" name="name" value="{{ $officeSettings->name ?? 'Kantor' }}">
                <input type="hidden" name="latitude" id="updateOfficeLat">
                <input type="hidden" name="longitude" id="updateOfficeLng">
                <input type="hidden" name="radius" value="{{ $officeSettings->radius ?? 100 }}">
                <input type="hidden" name="tolerance" value="{{ $officeSettings->tolerance ?? 10 }}">
                <input type="hidden" name="work_start_time" value="{{ $officeSettings->work_start_time?->format('H:i') ?? '09:00' }}">
                <input type="hidden" name="work_end_time" value="{{ $officeSettings->work_end_time?->format('H:i') ?? '17:00' }}">
                <button type="submit" id="btnUpdateOffice" class="btn btn-sm btn-secondary w-full" style="display: none;">
                    <i data-lucide="map-pin" style="width: 12px; height: 12px;"></i>
                    Gunakan Lokasi Saat Ini sebagai Kantor
                </button>
            </form>
        </div>
        @endif

        <div class="text-center space-y-2">
            <div class="time-display" id="currentTime">--:--:--</div>
            <div class="date-display" id="currentDate">-----------</div>
        </div>

        <div class="flex justify-center">
            <div class="camera-viewport">
                {{-- Loading State --}}
                <div class="camera-placeholder camera-loading" id="cameraLoading">
                    <div style="width: 40px; height: 40px; border: 3px solid var(--slate-200); border-top-color: var(--primary); border-radius: 50%; animation: spin 1s linear infinite;"></div>
                    <span style="margin-top: 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Memuat Kamera...</span>
                </div>
                {{-- Captured Image --}}
                <img class="camera-preview" id="capturedImage" alt="Captured">
                {{-- Video Stream --}}
                <video class="camera-video" id="video" autoplay playsinline muted></video>
                {{-- Idle State --}}
                <div class="camera-placeholder camera-idle" id="cameraIdle">
                    <i data-lucide="camera" style="width: 48px; height: 48px;"></i>
                    <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Kamera Verifikasi</span>
                </div>
                {{-- Capture Button --}}
                <button class="capture-btn" id="captureBtn" style="display: none;">
                    <div class="capture-btn-inner"></div>
                </button>
                {{-- Retake Button --}}
                <button class="retake-btn" id="retakeBtn" style="display: none;">
                    <i data-lucide="refresh-cw" style="width: 18px; height: 18px;"></i>
                </button>
            </div>
        </div>

        <div id="cameraError" class="alert alert-error" style="display: none;"></div>

        <div class="space-y-4" id="checkinActions">
            @php
                $hasEmployee = auth()->user()?->employee ? 'true' : 'false';
            @endphp
            <button id="startCamera"
                    class="btn btn-primary btn-lg w-full"
                    style="border-radius: 32px; {{ $hasEmployee ? '' : 'opacity: 0.5; cursor: not-allowed;' }}"
                    {{ $hasEmployee ? '' : 'disabled' }}>
                <span style="display: flex; align-items: center; gap: 8px; justify-content: center;">
                    <i data-lucide="camera" style="width: 20px; height: 20px;"></i>
                    Verifikasi & Absen
                </span>
            </button>
            @if(!$hasEmployee)
            <p class="text-center text-xs text-slate-400">Akun Anda belum terhubung ke data karyawan</p>
            @endif
        </div>

        <form method="POST" action="{{ route('attendance.store') }}" id="checkinForm" style="display: none;">
            @csrf
            <div class="card" style="background: var(--slate-50); border: none; padding: 16px; border-radius: 28px;">
                <div class="flex items-center gap-3">
                    <div style="width: 40px; height: 40px; background: #dcfce7; color: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="smartphone" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div style="text-align: left;">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status Perangkat</p>
                        <p class="text-xs font-bold text-slate-700">Aplikasi Web Terverifikasi</p>
                    </div>
                </div>
            </div>
            <input type="hidden" name="latitude" id="checkinLat">
            <input type="hidden" name="longitude" id="checkinLng">
            <input type="hidden" name="photo" id="checkinPhoto">
            <button type="submit" class="btn btn-primary btn-lg w-full" style="border-radius: 32px; margin-top: 16px;">Konfirmasi Kehadiran</button>
        </form>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// Office settings
const officeLat = {{ $officeSettings->latitude ?? -6.2088 }};
const officeLng = {{ $officeSettings->longitude ?? 106.8456 }};
const officeRadius = {{ $officeSettings->radius ?? 500 }};

// Global variables
let currentLat = officeLat;
let currentLng = officeLng;
let stream = null;

// Initialize maps
function initMap(mapId) {
    const mapEl = document.getElementById(mapId);
    if (!mapEl) return null;

    const map = L.map(mapId, { zoomControl: false, attributionControl: false }).setView([officeLat, officeLng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

    const officeIcon = L.divIcon({
        html: '<div style="background: #4ade80; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg></div>',
        iconSize: [24, 24], iconAnchor: [12, 24]
    });

    L.marker([officeLat, officeLng], { icon: officeIcon }).addTo(map);
    L.circle([officeLat, officeLng], { color: '#4ade80', fillColor: '#4ade80', fillOpacity: 0.15, radius: officeRadius, weight: 2 }).addTo(map);

    return map;
}

// Calculate distance
function calculateDistance(lat1, lng1, lat2, lng2) {
    const R = 6371e3;
    const φ1 = lat1 * Math.PI / 180, φ2 = lat2 * Math.PI / 180;
    const Δφ = (lat2 - lat1) * Math.PI / 180, Δλ = (lng2 - lng1) * Math.PI / 180;
    const a = Math.sin(Δφ/2)**2 + Math.cos(φ1)*Math.cos(φ2)*Math.sin(Δλ/2)**2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
}

// Update time
function updateTime() {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long' });

    document.querySelectorAll('#currentTime').forEach(el => el.textContent = timeStr);
    document.querySelectorAll('#currentDate').forEach(el => el.textContent = dateStr);
}

// Get location
function getLocation() {
    if (!navigator.geolocation) {
        document.querySelectorAll('#locationText, #locationTextCheckout').forEach(el => {
            el.textContent = 'Browser tidak mendukung lokasi';
        });
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            currentLat = pos.coords.latitude;
            currentLng = pos.coords.longitude;

            const distance = calculateDistance(currentLat, currentLng, officeLat, officeLng);
            const isInRange = distance <= officeRadius;
            const text = isInRange ? 'Di Jangkauan (' + Math.round(distance) + 'm)' : 'Di Luar Jangkauan (' + Math.round(distance) + 'm)';

            document.querySelectorAll('#locationText, #locationTextCheckout').forEach(el => el.textContent = text);

            // Update debug info for admin
            const debugLat = document.getElementById('debugUserLat');
            const debugLng = document.getElementById('debugUserLng');
            const debugDist = document.getElementById('debugDistance');
            const updateBtn = document.getElementById('btnUpdateOffice');
            const updateLat = document.getElementById('updateOfficeLat');
            const updateLng = document.getElementById('updateOfficeLng');

            if (debugLat) debugLat.textContent = currentLat.toFixed(6);
            if (debugLng) debugLng.textContent = currentLng.toFixed(6);
            if (debugDist) debugDist.textContent = Math.round(distance);
            if (updateBtn) updateBtn.style.display = 'flex';
            if (updateLat) updateLat.value = currentLat;
            if (updateLng) updateLng.value = currentLng;
        },
        (err) => {
            console.log('Location error:', err);
            document.querySelectorAll('#locationText, #locationTextCheckout').forEach(el => {
                el.textContent = 'Lokasi tidak tersedia (menggunakan lokasi kantor)';
            });
        },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 60000 }
    );
}

// Camera functions
async function startCamera(isCheckout = false) {
    const prefix = isCheckout ? 'Checkout' : '';
    const loadingEl = document.getElementById('cameraLoading' + prefix);
    const idleEl = document.getElementById('cameraIdle' + prefix);
    const videoEl = document.getElementById('video' + prefix);
    const captureBtn = document.getElementById('captureBtn' + prefix);
    const errorEl = document.getElementById('cameraError' + prefix);
    const actionsEl = document.getElementById(isCheckout ? 'checkoutActions' : 'checkinActions');

    // Show loading
    loadingEl.classList.add('active');
    idleEl.classList.add('hidden');
    errorEl.style.display = 'none';

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        errorEl.textContent = 'Browser Anda tidak mendukung akses kamera.';
        errorEl.style.display = 'block';
        loadingEl.classList.remove('active');
        idleEl.classList.remove('hidden');
        return;
    }

    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user', width: { ideal: 720 }, height: { ideal: 720 } },
            audio: false
        });

        videoEl.srcObject = stream;
        videoEl.classList.add('active');
        loadingEl.classList.remove('active');
        captureBtn.style.display = 'flex';
        if (actionsEl) actionsEl.style.display = 'none';

        videoEl.onloadedmetadata = () => videoEl.play();
    } catch (err) {
        console.error('Camera error:', err);
        errorEl.textContent = 'Tidak dapat mengakses kamera. Pastikan Anda memberikan izin kamera.';
        errorEl.style.display = 'block';
        loadingEl.classList.remove('active');
        idleEl.classList.remove('hidden');
    }
}

function capturePhoto(isCheckout = false) {
    const prefix = isCheckout ? 'Checkout' : '';
    const videoEl = document.getElementById('video' + prefix);
    const canvas = document.createElement('canvas');
    canvas.width = 400;
    canvas.height = 400;
    const ctx = canvas.getContext('2d');

    // Flip horizontally
    ctx.translate(canvas.width, 0);
    ctx.scale(-1, 1);

    const size = Math.min(videoEl.videoWidth, videoEl.videoHeight);
    const x = (videoEl.videoWidth - size) / 2;
    const y = (videoEl.videoHeight - size) / 2;
    ctx.drawImage(videoEl, x, y, size, size, 0, 0, 400, 400);

    const imageData = canvas.toDataURL('image/png');

    // Stop camera
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }

    // Show captured image
    document.getElementById('capturedImage' + prefix).src = imageData;
    document.getElementById('capturedImage' + prefix).classList.add('active');
    videoEl.classList.remove('active');
    document.getElementById('captureBtn' + prefix).style.display = 'none';
    document.getElementById('retakeBtn' + prefix).style.display = 'flex';

    // Update form
    if (isCheckout) {
        document.getElementById('checkoutPhoto').value = imageData;
        document.getElementById('checkoutLat').value = currentLat;
        document.getElementById('checkoutLng').value = currentLng;
        document.getElementById('checkoutForm').style.display = 'block';
    } else {
        document.getElementById('checkinPhoto').value = imageData;
        document.getElementById('checkinLat').value = currentLat;
        document.getElementById('checkinLng').value = currentLng;
        document.getElementById('checkinForm').style.display = 'block';
    }
}

function retakePhoto(isCheckout = false) {
    const prefix = isCheckout ? 'Checkout' : '';

    document.getElementById('capturedImage' + prefix).classList.remove('active');
    document.getElementById('retakeBtn' + prefix).style.display = 'none';

    if (isCheckout) {
        document.getElementById('checkoutForm').style.display = 'none';
    } else {
        document.getElementById('checkinForm').style.display = 'none';
    }

    startCamera(isCheckout);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Initialize maps
    initMap('attendanceMap');
    initMap('attendanceMapCheckout');

    // Start time updates
    updateTime();
    setInterval(updateTime, 1000);

    // Get location
    getLocation();

    // Check In Camera
    const startBtn = document.getElementById('startCamera');
    if (startBtn) {
        startBtn.addEventListener('click', () => startCamera(false));
    }

    const captureBtn = document.getElementById('captureBtn');
    if (captureBtn) {
        captureBtn.addEventListener('click', () => capturePhoto(false));
    }

    const retakeBtn = document.getElementById('retakeBtn');
    if (retakeBtn) {
        retakeBtn.addEventListener('click', () => retakePhoto(false));
    }

    // Check Out Camera
    const startBtnCheckout = document.getElementById('startCameraCheckout');
    if (startBtnCheckout) {
        startBtnCheckout.addEventListener('click', () => startCamera(true));
    }

    const captureBtnCheckout = document.getElementById('captureBtnCheckout');
    if (captureBtnCheckout) {
        captureBtnCheckout.addEventListener('click', () => capturePhoto(true));
    }

    const retakeBtnCheckout = document.getElementById('retakeBtnCheckout');
    if (retakeBtnCheckout) {
        retakeBtnCheckout.addEventListener('click', () => retakePhoto(true));
    }
});
</script>
@endpush
