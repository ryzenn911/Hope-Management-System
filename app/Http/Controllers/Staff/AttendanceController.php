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
        // === ១. VALIDATE DATA ===
        $request->validate([
            'qr_code' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // === ២. CHECK QR CODE ===
        $validQrCode = 'HOPE_ATTENDANCE';
        if ($request->qr_code !== $validQrCode) {
            return back()->withErrors([
                'error' => 'ការស្កេនបរាជ័យ! QR Code នេះមិនត្រឹមត្រូវឡើយ។',
            ]);
        }

        // === ៣. CHECK RADIUS DISTANCE (GEOLOCATION) ===
        $officeLat = 12.774667;
        $officeLng = 103.450524;
        $allowedRadius = 50; // ៥០ ម៉ែត្រ

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
                'error' => 'ការស្កេនបរាជ័យ! អ្នកស្ថិតនៅក្រៅបរិវេណទីតាំងការិយាល័យ។',
            ]);
        }

        // === ៤. GET EMPLOYEE SHIFT DATA ===
        $user = Auth::user();
        $employee = $user->employee()->with('position')->first();

        if (! $employee) {
            return back()->withErrors(['error' => 'គណនីរបស់អ្នកមិនមានទិន្នន័យបុគ្គលិកឡើយ។']);
        }

        $positionId = $employee->position_id;
        $shift = PositionShift::query()->where('position_id', $positionId)->first();

        if (! $shift) {
            return back()->withErrors(['error' => 'មុខតំណែងរបស់អ្នកមិនទាន់បានកំណត់ម៉ោងធ្វើការឡើយ។']);
        }

        // === ៥. INITIALIZE TIME VARIABLES ===
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();
        $nowTime = $now->toTimeString(); // ទម្រង់ "H:i:s"

        // ប្រព័ន្ធបើកឱ្យស្កេនចាប់ពីម៉ោង ០៦:០០ ព្រឹកឡើងទៅ
        if ($nowTime < '06:00:00') {
            return back()->withErrors(['error' => 'The system opens for scanning from 06:00 AM onwards.']);
        }

        $mornInTime = $shift->morn_in_time;
        $aftInTime = $shift->aft_in_time;
        $earliestMornOut = '12:00:00';
        $autoMornOutDeadLine = '12:30:00'; // ម៉ោងផ្ដាច់វគ្គព្រឹក
        $eveningOutTime = $shift->evening_out_time ?? '17:00:00';

        // បទបញ្ជាពិសេស៖ បើជា គ្រូបង្រៀន (id = 8)
        if ($employee->position_id == 8 || (optional($employee->position)->name === 'គ្រូបង្រៀន')) {
            $mornInTime = '07:00:00';
            $aftInTime = '13:00:00';
            $earliestMornOut = '11:00:00';
            $autoMornOutDeadLine = '11:30:00';
            $eveningOutTime = '16:00:00';
        }

        // ទាញយកទិន្នន័យវត្តមានថ្ងៃនេះ
        $attendance = Attendance::query()->where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        // បិទការស្កេននៅពេលដាច់ថ្ងៃ (ម៉ោង ២៣:៥៩:៥៩)
        if ($nowTime > '23:59:59') {
            return back()->withErrors(['error' => 'អ្នកមិនអាចស្កេនបានទេ! (Today is closed)']);
        }

        // បើពេញទាំង ៤ ប្រអប់រួចរាល់ហើយ មិនឱ្យស្កេនទៀតទេ
        if ($attendance && $attendance->check_in_morn && $attendance->check_out_morn && $attendance->check_in_aft && $attendance->check_out_aft) {
            return back()->withErrors(['error' => 'អ្នកមិនអាចស្កេនបានទេ! (All records completed)']);
        }

        // ========================================================
        // ZONE 1: ស្កេនក្នុងម៉ោងវគ្គព្រឹក (ចាប់ពីម៉ោង 06:00 ដល់ 12:30)
        // ========================================================
        if ($nowTime <= $autoMornOutDeadLine) {

            // ១.១ ស្កេនលើកដំបូងបង្អស់ (Check-in ព្រឹក)
            if (! $attendance) {
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

            // ១.២ មាន Record ព្រឹកហើយ ឥឡូវមកស្កេនចេញ (Check-out ព្រឹក)
            if ($attendance->check_out_morn === null) {
                if ($nowTime < $earliestMornOut) {
                    return back()->withErrors([
                        'error' => 'មិនទាន់អាច check-out បានទេ! ម៉ោង '.Carbon::parse($earliestMornOut)->format('H:i').' ទើបអាច Check-out បាន។',
                    ]);
                }

                $attendance->check_out_morn = $nowTime;
                $attendance->save();

                return back()->with('success', 'Morning check-out successful!');
            }

            // បើគាត់ព្យាយាមចុចស្កេនលើកទី៣ ក្នុងវគ្គព្រឹក
            return back()->withErrors(['error' => 'អ្នកបានបំពេញទិន្នន័យវគ្គព្រឹករួចរាល់ហើយ។']);
        }

        // ========================================================
        // ZONE 2: ស្កេនក្នុងចន្លោះម៉ោងថ្ងៃត្រង់ (12:31 ដល់ 12:59) -> ចាក់សោដាច់ខាត
        // ========================================================
        if ($nowTime < '13:00:00') {
            return back()->withErrors([
                'error' => 'មិនទាន់ដល់ម៉ោង Check In នោះទេ។',
            ]);
        }

        // ========================================================
        // ZONE 3: ស្កេនក្នុងម៉ោងវគ្គរសៀល (ចាប់ពីម៉ោង 13:00 ឡើងទៅរហូតដល់យប់)
        // ========================================================

        // ៣.១ ករណីព្រឹកអត់បានមកសោះ (អត់ទាន់មាន Record ក្នុង DB ទាល់តែសោះ)
        if (! $attendance) {
            $aftInDeadline = Carbon::parse($aftInTime)->addMinutes(30)->toTimeString();
            $aftStatus = ($nowTime >= $aftInDeadline) ? 'Late' : 'Present';

            Attendance::create([
                'employee_id' => $employee->id,
                'date' => $today,
                'check_in_morn' => null,
                'check_out_morn' => null,
                'morn_status' => 'Absent', // ទម្លាក់វគ្គព្រឹកជា Absent ដោយស្វ័យប្រវត្តិ
                'check_in_aft' => $nowTime,
                'aft_status' => $aftStatus,
            ]);

            return back()->with('success', 'Morning missed (ABSENT). Afternoon check-in successful! Status: '.strtoupper($aftStatus));
        }

        // ៣.២ ករណីមាន Record ព្រឹករួចហើយ (មកបំពេញ Check-in រសៀល)
        if ($attendance->check_in_aft === null) {
            // 💡 មុខងារ Auto Fill ត្រូវបានលុបចោល៖ បើទោះជា check_out_morn ជា null ក៏ទុកឱ្យ null ដដែល
            $aftInDeadline = Carbon::parse($aftInTime)->addMinutes(30)->toTimeString();
            $aftStatus = ($nowTime >= $aftInDeadline) ? 'Late' : 'Present';

            $attendance->check_in_aft = $nowTime;
            $attendance->aft_status = $aftStatus;
            $attendance->save();

            return back()->with('success', 'Afternoon check-in successful! Status: '.strtoupper($aftStatus));
        }

        // ៣.៣ ករណីស្កេនចុងក្រោយគេបង្អស់ប្រចាំថ្ងៃ (Check-out ល្ងាច)
        if ($attendance->check_out_aft === null) {
            // 💡 មុខងារ Auto Fill ត្រូវបានលុបចោល៖ ចាប់យកម៉ោងស្កេនពិតប្រាកដរបស់គាត់ផ្ទាល់
            $attendance->check_out_aft = $nowTime;
            $attendance->save();

            return back()->with('success', 'Evening check-out successful! Have a good rest.');
        }

        return back()->withErrors(['error' => 'អ្នកមិនអាចស្កេនបានទេ! (Today record is already locked)']);
    }
}
