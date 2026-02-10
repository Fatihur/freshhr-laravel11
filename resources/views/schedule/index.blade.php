@extends('layouts.app')

@section('title', 'Jadwal')
@section('header-title', 'Jadwal')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-bold text-slate-800">Jadwal Departemen</h2>
            <p class="text-sm text-slate-400 mt-1">Kelola shift mingguan untuk Tim Engineering.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="btn btn-secondary">Batalkan</button>
            <button class="btn btn-primary">Selesaikan & Terbitkan</button>
        </div>
    </div>

    <div class="schedule-split-grid">
        <div class="card" style="padding: 0; overflow: hidden; border-radius: 40px;">
            <div style="padding: 24px; border-bottom: 1px solid var(--slate-50); display: flex; align-items: center; justify-content: space-between;">
                <div class="flex items-center gap-4">
                    <button class="btn btn-secondary btn-sm"><i data-lucide="chevron-left"></i></button>
                    <h3 class="text-lg font-bold">10 - 24 Maret 2024</h3>
                    <button class="btn btn-secondary btn-sm"><i data-lucide="chevron-right"></i></button>
                </div>
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-2"><span style="width:12px;height:12px;background:#4ade80;border-radius:50%;"></span><span class="text-xs">Pagi</span></span>
                    <span class="flex items-center gap-2"><span style="width:12px;height:12px;background:#60a5fa;border-radius:50%;"></span><span class="text-xs">Malam</span></span>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: repeat(7, 1fr);">
                @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                    <div style="padding: 16px; text-align: center; background: var(--slate-50); font-size: 10px; font-weight: 700; color: var(--slate-400); text-transform: uppercase;">{{ $day }}</div>
                @endforeach
                @for($i = 0; $i < 14; $i++)
                    <div style="min-height: 120px; padding: 12px; border: 1px solid var(--slate-50); background: white;">
                        <span class="text-sm font-bold text-slate-400">{{ 10 + $i }}</span>
                        <div class="mt-2 space-y-1">
                            <div style="padding: 4px 8px; background: #dcfce7; color: #15803d; font-size: 9px; font-weight: 600; border-radius: 20px;">09:00-17:00</div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
        <div class="card">
            <h3 class="font-bold mb-4">Ringkasan</h3>
            <div class="space-y-3">
                <div class="flex justify-between"><span class="text-xs text-slate-400">Total Shift</span><span class="text-sm font-bold">42</span></div>
                <div class="flex justify-between"><span class="text-xs text-slate-400">Kurang Orang</span><span class="text-sm font-bold text-red-500">2</span></div>
            </div>
        </div>
    </div>
</div>
@endsection
