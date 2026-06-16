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

        // ប្រើ Eager Loading ទាញយក position មកជាមួយដើម្បីកុំឱ្យជួបបញ្ហា N+1 query
        $employee = $user->employee()->with('position')->first();

        if (! $employee) {
            return back()->withErrors(['error' => 'គណនីរបស់អ្នកមិនមានទិន្នន័យបុគ្គលិកឡើយ。']);
        }

        $positionId = $employee->position_id;
        $shift = PositionShift::query()->where('position_id', $positionId)->first();

        if (! $shift) {
            return back()->withErrors(['error' => 'មុខតំណែងរបស់អ្នកមិនទាន់បានកំណត់ម៉ោងធ្វើការឡើយ។']);
        }

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();
        $nowTime = $now->toTimeString(); // ទម្រង់ "H:i:s" សម្រាប់ធៀប និងបញ្ចូលក្នុង DB

        $attendance = Attendance::query()->where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        // ---------------------------------------------------------
        // ៤. Logic ៖ កត់ត្រាវត្តមាន ៤ ពេល និងបែងចែកម៉ោងតាមមុខតំណែង
        // ---------------------------------------------------------

        // ម៉ោងច្បាស់លាស់ទាញចេញពី Database Shift លំនាំដើម (សម្រាប់ Position ផ្សេងៗ)
        $mornInTime = $shift->morn_in_time;
        $aftInTime = $shift->aft_in_time;

        // កំណត់ម៉ោងលំនាំដើមសម្រាប់ Check-out (សម្រាប់ Position ផ្សេងៗ)
        $earliestMornOut = '12:00:00';
        $autoMornOutDeadLine = '12:30:00';

        // 💡 ជួសជុល៖ ពិនិត្យលក្ខខណ្ឌបើជា "គ្រូបង្រៀន" (id = 8 ឬ name = 'គ្រូបង្រៀន') ឱ្យបង្ខំកំណត់ម៉ោងឡូហ្សិកដាច់ដោយឡែក
        if ($employee->position_id == 8 || (optional($employee->position)->name === 'គ្រូបង្រៀន')) {
            // កែប្រែម៉ោងលក្ខខណ្ឌចូល សម្រាប់គ្រូបង្រៀន
            $mornInTime = '07:00:00';
            $aftInTime = '13:00:00';

            // កែប្រែម៉ោងលក្ខខណ្ឌចេញ សម្រាប់គ្រូបង្រៀន
            $earliestMornOut = '11:00:00';
            $autoMornOutDeadLine = '11:30:00';
        }

        // === ករណីទី ១៖ CHECK-IN ព្រឹក ===
        if (! $attendance) {
            // គណនាម៉ោងកំណត់ (ម៉ោងកំណត់ក្នុង Position + 30 នាទី) ប្រសិនបើលើសពី ឬស្មើ គឺ Late
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

        // ករណីពិសេស៖ ភ្លេច Check-out ព្រឹក ហើយមកស្កេនពេលផុតម៉ោងកំណត់
        if ($attendance->check_out_morn === null && $nowTime > $autoMornOutDeadLine) {
            $attendance->check_out_morn = $shift->morn_out_time;

            // ប្រសិនបើលើសពី ឬស្មើ ៣០ នាទីពេលរសៀល គឺដឹងតែយឺត Late
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
            // ប្រសិនបើលើសពី ឬស្មើ ៣០ នាទីពេលរសៀល គឺដឹងតែយឺត Late
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

        return back()->withErrors(['error' => 'You have already completed all attendance records for today!']);
    }
}
