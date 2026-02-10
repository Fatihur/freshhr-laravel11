<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\OfficeSetting;
use App\Models\Shift;
use Illuminate\Http\Request;

class OfficeSettingController extends Controller
{
    public function index()
    {
        $officeSettings = OfficeSetting::where('is_active', true)->first();
        $shifts = Shift::orderBy('start_time')->get();

        return view('management.office.index', compact('officeSettings', 'shifts'));
    }

    public function updateOffice(Request $request)
    {
        \Log::info('Office update request received', $request->all());

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'radius' => 'required|integer|min:10|max:1000',
                'tolerance' => 'required|integer|min:0|max:100',
                'work_start_time' => 'required|date_format:H:i',
                'work_end_time' => 'required|date_format:H:i|after:work_start_time',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Office update validation failed', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        }

        // Add is_active to validated data
        $validated['is_active'] = true;

        // Find existing active record or create new one
        $officeSetting = OfficeSetting::where('is_active', true)->first();
        \Log::info('Found office setting', ['office' => $officeSetting?->toArray()]);

        if ($officeSetting) {
            $officeSetting->update($validated);
            \Log::info('Office setting updated', ['id' => $officeSetting->id]);
        } else {
            // If no active record, find any record and update it
            $officeSetting = OfficeSetting::first();
            if ($officeSetting) {
                $officeSetting->update($validated);
                \Log::info('Office setting updated (was inactive)', ['id' => $officeSetting->id]);
            } else {
                OfficeSetting::create($validated);
                \Log::info('New office setting created');
            }
        }

        return back()->with('success', 'Pengaturan kantor berhasil disimpan.');
    }

    public function storeShift(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
        ]);

        Shift::create($validated);

        return back()->with('success', 'Shift berhasil ditambahkan.');
    }

    public function updateShift(Request $request, Shift $shift)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
        ]);

        $shift->update($validated);

        return back()->with('success', 'Shift berhasil diperbarui.');
    }

    public function destroyShift(Shift $shift)
    {
        if ($shift->schedules()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus shift yang sedang digunakan.');
        }

        $shift->delete();
        
        return back()->with('success', 'Shift berhasil dihapus.');
    }
}
