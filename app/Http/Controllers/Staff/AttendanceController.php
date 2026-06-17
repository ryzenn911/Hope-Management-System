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

        $validQrCode = 'HOPE_ATTENDANCE';
        if ($request->qr_code !== $validQrCode) {
            return back()->withErrors([
                'error' => 'ការស្កេនបរាជ័យ! QR Code នេះមិនត្រឹមត្រូវឡើយ។',
            ]);
        }

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
                'error' => 'ការស្កេនបរាជ័យ! អ្នកស្ថិតនៅក្រៅបរិវេណទីតាំង។',
            ]);
        }

        $user = Auth::user();

        // ប្រើ Eager Loading ទាញយក position
        $employee = $user->employee()->with('position')->first();

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
        $nowTime = $now->toTimeString(); // ទម្រង់ "H:i:s"

        if ($nowTime < '06:00:00') {
            return back()->withErrors(['error' => 'The system opens for scanning from 06:00 AM onwards.']);
        }

        $mornInTime = $shift->morn_in_time;
        $aftInTime = $shift->aft_in_time;

        $earliestMornOut = '12:00:00';
        $autoMornOutDeadLine = '12:30:00';

        $eveningOutTime = $shift->evening_out_time ?? '17:00:00';
        $autoEveningOutDeadline = Carbon::parse($eveningOutTime)->addMinutes(30)->toTimeString();

        // បើជា គ្រូបង្រៀន (id = 8)
        if ($employee->position_id == 8 || (optional($employee->position)->name === 'គ្រូបង្រៀន')) {
            $mornInTime = '07:00:00';
            $aftInTime = '13:00:00';
            $earliestMornOut = '11:00:00';
            $autoMornOutDeadLine = '11:30:00';
            $eveningOutTime = '16:00:00';
            $autoEveningOutDeadline = '16:30:00';
        }

        // ទាញយកទិន្នន័យវត្តមានថ្ងៃនេះ
        $attendance = Attendance::query()->where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();
        if ($nowTime > $autoEveningOutDeadline) {
            return back()->withErrors(['error' => 'អ្នកមិនអាចស្កេនបានទេ! (Today is closed)']);
        }

        if ($attendance && $attendance->check_in_morn && $attendance->check_out_morn && $attendance->check_in_aft && $attendance->check_out_aft) {
            return back()->withErrors(['error' => 'អ្នកមិនអាចស្កេនបានទេ! (All records completed)']);
        }

        if (! $attendance) {
            if ($nowTime > $autoMornOutDeadLine) {
                $aftInDeadline = Carbon::parse($aftInTime)->addMinutes(30)->toTimeString();
                $aftStatus = ($nowTime >= $aftInDeadline) ? 'Late' : 'Present';
                Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $today,
                    'check_in_morn' => null,   // ទុកឱ្យ null តាមការចង់បានរបស់អ្នក
                    'check_out_morn' => null,  // ទុកឱ្យ null
                    'morn_status' => 'Absent', // បោះទៅជា Absent តែម្ដង
                    'check_in_aft' => $nowTime, // ចាប់យកម៉ោងបច្ចុប្បន្នចូលក្នុងប្រអប់ពេលរសៀល
                    'aft_status' => $aftStatus,
                ]);

                return back()->with('success', 'Morning missed (ABSENT). Afternoon check-in successful! Status: '.strtoupper($aftStatus));
            }

            $mornInDeadline = Carbon::parse($mornInTime)->addMinutes(30)->toTimeString();
            $mornStatus = ($nowTime >= $mornInDeadline) ? 'Late' : 'Present';

            Attendance::create([
                'employee_id' => $employee->id,
                'date' => $today,
                'check_in_morn' => $nowTime,
                'morn_status' => $mornStatus,
            ]);

            return back()->with('success', 'Morning check-in successful! Status: '.strtoupper($mornStatus));
        }

        // ករណីពិសេស៖ ធ្លាប់បាន Check-in ព្រឹក តែភ្លេច Check-out ព្រឹក រួចមកស្កេនពេលរសៀល
        if ($attendance->check_out_morn === null && $nowTime > $autoMornOutDeadLine) {
            $attendance->check_out_morn = $shift->morn_out_time; // ឱ្យម៉ោងតាម Shift

            $aftInDeadline = Carbon::parse($aftInTime)->addMinutes(30)->toTimeString();
            $aftStatus = ($nowTime >= $aftInDeadline) ? 'Late' : 'Present';

            $attendance->check_in_aft = $nowTime;
            $attendance->aft_status = $aftStatus;
            $attendance->save();

            return back()->with('success', 'System auto morning check-out and afternoon check-in successful!');
        }

        // === ករណីទី ២៖ CHECK-OUT ព្រឹក ===
        if ($attendance->check_out_morn === null) {
            if ($nowTime < $earliestMornOut) {
                return back()->withErrors([
                    'error' => 'Cannot check-out yet! For your position, you can check-out from '.Carbon::parse($earliestMornOut)->format('H:i').' onwards.',
                ]);
            }

            $attendance->check_out_morn = $nowTime;
            $attendance->save();

            return back()->with('success', 'Morning check-out successful!');
        }

        // === ករណីទី ៣៖ CHECK-IN រសៀល ===
        if ($attendance->check_in_aft === null) {
            $aftInDeadline = Carbon::parse($aftInTime)->addMinutes(30)->toTimeString();
            $aftStatus = ($nowTime >= $aftInDeadline) ? 'Late' : 'Present';

            $attendance->check_in_aft = $nowTime;
            $attendance->aft_status = $aftStatus;
            $attendance->save();

            return back()->with('success', 'Afternoon check-in successful! Status: '.strtoupper($aftStatus));
        }

        // === ករណីទី ៤៖ CHECK-OUT ល្ងាច ===
        if ($attendance->check_out_aft === null) {
            $attendance->check_out_aft = $nowTime;
            $attendance->save();

            return back()->with('success', 'Evening check-out successful! Have a good rest.');
        }

        return back()->withErrors(['error' => 'អ្នកមិនអាចស្កេនបានទេ! (Today record is already locked)']);
    }
}
