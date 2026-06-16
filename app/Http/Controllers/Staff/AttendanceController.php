<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\PositionShift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    public function scanPage()
    {
        return Inertia::render('Staff/Attendance/Scan');
    }

    public function store(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // ១. ផ្ទៀងផ្ទាត់ QR Code (ប្រើប្រាស់តម្លៃក្រុមហ៊ុនរបស់អ្នក)
        $validQrCode = 'HOPE_ATTENDANCE';
        if ($request->qr_code !== $validQrCode) {
            return back()->withErrors([
                'error' => 'ការស្កេនបរាជ័យ! QR Code នេះមិនត្រឹមត្រូវ ឬមិនមែនជារបស់ក្រុមហ៊ុនឡើយ។',
            ]);
        }

        // ២. ផ្ទៀងផ្ទាត់ចម្ងាយ GPS
        $officeLat = 12.774667;
        $officeLng = 103.450524;
        $allowedRadius = 50;

        $earthRadius = 6371000;
        $latDelta = deg2rad($request->latitude - $officeLat);
        $lonDelta = deg2rad($request->longitude - $officeLng);
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($officeLat)) * cos(deg2rad($request->latitude)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        if ($distance > $allowedRadius) {
            return back()->withErrors([
                'error' => 'ការស្កេនបរាជ័យ! អ្នកស្ថិតនៅក្រៅបរិវេណក្រុមហ៊ុនប្រហែល '.round($distance).' ម៉ែត្រ។',
            ]);
        }

        // ៣. ទាញព័ត៌មានបុគ្គលិក និងវេនការងារ (Shift)
        $user = Auth::user();
        $employee = $user->employee;

        if (! $employee) {
            return back()->withErrors(['error' => 'គណនីរបស់អ្នកមិនមានទិន្នន័យបុគ្គលិកឡើយ។']);
        }

        $positionId = $employee->position_id;
        $shift = PositionShift::query()->where('position_id', $positionId)->first();

        if (! $shift) {
            return back()->withErrors(['error' => 'មុខតំណែងរបស់អ្នកមិនទាន់បានកំណត់ម៉ោងធ្វើការឡើយ។']);
        }

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();
        $nowTime = $now->toTimeString();

        $attendance = Attendance::query()->where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        // ៤. Logic កត់ត្រាវត្តមាន ៤ ពេល (ដក Leave_Early ចេញ និងប្តូរមកប្រើ return back() ទាំងអស់)
        if (! $attendance) {
            $mornInRule = Carbon::parse($shift->morn_in_time);
            $mornStatus = $now->greaterThan($mornInRule) ? 'Late' : 'Present';
            Attendance::create([
                'employee_id' => $employee->id,
                'date' => $today,
                'check_in_morn' => $nowTime,
                'morn_status' => $mornStatus,
            ]);

            return back()->with('success', 'Check-in វគ្គពេលព្រឹកជោគជ័យ!');
        }

        if ($attendance->check_out_morn === null) {
            // អាប់ដេតតែម៉ោងចេញ ដោយមិនប្តូរ Status ទៅជា Leave_Early ឡើយ
            $attendance->update(['check_out_morn' => $nowTime]);

            return back()->with('success', 'Check-out វគ្គពេលព្រឹកជោគជ័យ!');
        }

        if ($attendance->check_in_aft === null) {
            $aftInRule = Carbon::parse($shift->aft_in_time);
            $aftStatus = $now->greaterThan($aftInRule) ? 'Late' : 'Present';
            $attendance->update([
                'check_in_aft' => $nowTime,
                'aft_status' => $aftStatus,
            ]);

            return back()->with('success', 'Check-in វគ្គពេលរសៀលជោគជ័យ!');
        }

        if ($attendance->check_out_aft === null) {
            // អាប់ដេតតែម៉ោងចេញចុងក្រោយ ដោយមិនប្តូរ Status ឡើយ
            $attendance->update(['check_out_aft' => $nowTime]);

            return back()->with('success', 'Check-out វគ្គពេលល្ងាចជោគជ័យ!');
        }

        return back()->withErrors(['error' => 'អ្នកបានបំពេញវត្តមានគ្រប់ ៤ ពេលរួចរាល់ហើយសម្រាប់ថ្ងៃនេះ!']);
    }
}
