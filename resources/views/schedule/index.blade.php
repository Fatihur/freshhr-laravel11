@extends('layouts.app')

@section('title', 'Jadwal')
@section('header-title', 'Jadwal')

@section('content')
<div class="schedule-container space-y-8">
    <div class="schedule-header flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-bold text-slate-800">Jadwal Departemen</h2>
            <p class="text-sm text-slate-400 mt-1">Kelola shift mingguan untuk Tim Engineering.</p>
        </div>
        <div class="schedule-actions flex items-center gap-3">
            <button class="btn btn-secondary">Batalkan</button>
            <button class="btn btn-primary">Selesaikan & Terbitkan</button>
        </div>
    </div>

    <div class="schedule-split-grid">
        <div class="card schedule-calendar-wrapper" style="padding: 0; overflow: hidden; border-radius: 40px;">
            <div class="schedule-calendar-header" style="padding: 24px; border-bottom: 1px solid var(--slate-50); display: flex; align-items: center; justify-content: space-between;">
                <div class="flex items-center gap-4">
                    <button class="btn btn-secondary btn-sm"><i data-lucide="chevron-left"></i></button>
                    <h3 class="text-lg font-bold">10 - 24 Maret 2024</h3>
                    <button class="btn btn-secondary btn-sm"><i data-lucide="chevron-right"></i></button>
                </div>
                <div class="schedule-legend flex items-center gap-4">
                    <span class="flex items-center gap-2"><span style="width:12px;height:12px;background:#4ade80;border-radius:50%;"></span><span class="text-xs">Pagi</span></span>
                    <span class="flex items-center gap-2"><span style="width:12px;height:12px;background:#60a5fa;border-radius:50%;"></span><span class="text-xs">Malam</span></span>
                </div>
            </div>
            <div class="schedule-calendar-scroll">
                <div class="schedule-calendar-grid">
                    @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                        <div class="schedule-calendar-day-header">{{ $day }}</div>
                    @endforeach
                    @for($i = 0; $i < 14; $i++)
                        <div class="schedule-calendar-day">
                            <span class="text-sm font-bold text-slate-400">{{ 10 + $i }}</span>
                            <div class="mt-2 space-y-1">
                                <div class="schedule-shift-badge">09:00-17:00</div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
        <div class="card schedule-summary">
            <h3 class="font-bold mb-4">Ringkasan</h3>
            <div class="space-y-3">
                <div class="flex justify-between"><span class="text-xs text-slate-400">Total Shift</span><span class="text-sm font-bold">42</span></div>
                <div class="flex justify-between"><span class="text-xs text-slate-400">Kurang Orang</span><span class="text-sm font-bold text-red-500">2</span></div>
            </div>
        </div>
    </div>
</div>

<style>
    .schedule-split-grid {
        display: grid;
        grid-template-columns: 3fr 1fr;
        gap: 32px;
    }

    .schedule-calendar-scroll {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .schedule-calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(100px, 1fr));
        min-width: 700px;
    }

    .schedule-calendar-day-header {
        padding: 16px;
        text-align: center;
        background: var(--slate-50);
        font-size: 10px;
        font-weight: 700;
        color: var(--slate-400);
        text-transform: uppercase;
    }

    .schedule-calendar-day {
        min-height: 120px;
        padding: 12px;
        border: 1px solid var(--slate-50);
        background: white;
    }

    .schedule-shift-badge {
        padding: 4px 8px;
        background: #dcfce7;
        color: #15803d;
        font-size: 9px;
        font-weight: 600;
        border-radius: 20px;
        white-space: nowrap;
    }

    @media (max-width: 1024px) {
        .schedule-split-grid {
            grid-template-columns: 1fr;
        }

        .schedule-summary {
            order: -1;
        }
    }

    @media (max-width: 640px) {
        .schedule-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .schedule-header h2 {
            font-size: 20px;
        }

        .schedule-actions {
            width: 100%;
        }

        .schedule-actions .btn {
            flex: 1;
            justify-content: center;
        }

        .schedule-calendar-header {
            flex-direction: column;
            gap: 16px;
            align-items: flex-start !important;
        }

        .schedule-calendar-header h3 {
            font-size: 14px;
        }

        .schedule-legend {
            width: 100%;
            justify-content: flex-start;
        }

        .schedule-calendar-wrapper {
            border-radius: 24px !important;
        }

        .schedule-calendar-day {
            min-height: 80px;
            padding: 8px;
        }

        .schedule-calendar-day-header {
            padding: 12px 8px;
            font-size: 9px;
        }
    }
</style>
@endsection
