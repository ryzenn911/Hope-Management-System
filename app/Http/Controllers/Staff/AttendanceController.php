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

        // កែប្រែ៖ ប្រើ Eager Loading ទាញយក position មកជាមួយដើម្បីកុំឱ្យជួបបញ្ហា N+1 query
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
        $nowTime = $now->toTimeString();

        $attendance = Attendance::query()->where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        // ---------------------------------------------------------
        // ៤. Logic ៖ កត់ត្រាវត្តមាន ៤ ពេល និងបែងចែកម៉ោងតាមមុខតំណែង
        // ---------------------------------------------------------

        // ម៉ោងច្បាស់លាស់ពី Database Shift
        $mornInTime = Carbon::parse($shift->morn_in_time);
        $mornOutTime = Carbon::parse($shift->morn_out_time);
        $aftInTime = Carbon::parse($shift->aft_in_time);
        $aftOutTime = Carbon::parse($shift->aft_out_time);

        // ទាញព័ត៌មានមុខតំណែងរបស់បុគ្គលិក (Position Name) ពីតារាង positions តាមរយៈ relationship
        $positionName = optional($employee->position)->name_en;

        // បង្កើត Variable សម្រាប់ចាំទទួលម៉ោងលក្ខខណ្ឌ
        $earliestMornOut = null;
        $autoMornOutDeadLine = null;

        // ឆែកមើលលក្ខខណ្ឌ៖ បើជាគ្រូបង្រៀន (Teacher)
        if ($positionName && strpos(strtolower($positionName), 'teacher') !== false) {
            $earliestMornOut = Carbon::parse('11:00:00');
            $autoMornOutDeadLine = Carbon::parse('11:30:00');
        } else {
            // សម្រាប់មុខតំណែងផ្សេងៗទៀត (Other Positions)
            $earliestMornOut = Carbon::parse('12:00:00');
            $autoMornOutDeadLine = Carbon::parse('12:30:00');
        }

        // === ករណីទី ១៖ CHECK-IN ព្រឹក ===
        if (! $attendance) {
            // បើស្កេនយឺតជាងម៉ោងកំណត់ ៣០ នាទី ស្មើនឹង Late (ឧទាហរណ៍៖ លើសពី ០៨:៣០)
            $mornInDeadline = $mornInTime->copy()->addMinutes(30);
            $mornStatus = $now->greaterThan($mornInDeadline) ? 'Late' : 'Present';

            Attendance::create([
                'employee_id' => $employee->id,
                'date' => $today,
                'check_in_morn' => $nowTime,
                'morn_status' => $mornStatus,
            ]);

            return back()->with('success', 'Check-in វគ្គពេលព្រឹកជោគជ័យ! ស្ថានភាព៖ '.($mornStatus == 'Late' ? 'យឺត' : 'ទាន់ម៉ោង'));
        }

        // ករណីពិសេស៖ ភ្លេច Check-out ព្រឹក ហើយមកស្កេនពេលផុតម៉ោងកំណត់ (Teacher: 11:30, Other: 12:30)
        if ($attendance->check_out_morn === null && $now->greaterThan($autoMornOutDeadLine)) {
            // កំណត់ម៉ោងចេញព្រឹកអូតូ ត្រឹមម៉ោងបញ្ចប់ក្នុង Shift
            $attendance->check_out_morn = $shift->morn_out_time;

            // បន្តកត់ត្រាចូលរសៀលតែម្តង
            $aftInDeadline = $aftInTime->copy()->addMinutes(30);
            $aftStatus = $now->greaterThan($aftInDeadline) ? 'Late' : 'Present';

            $attendance->check_in_aft = $nowTime;
            $attendance->aft_status = $aftStatus;
            $attendance->save();

            return back()->with('success', 'ប្រព័ន្ធបាន Auto Check-out ព្រឹក និង Check-in រសៀលជោគជ័យ!');
        }

        // === ករណីទី ២៖ CHECK-OUT ព្រឹក ===
        if ($attendance->check_out_morn === null) {
            // លក្ខខណ្ឌ៖ ឃាត់មិនឱ្យចេញមុនម៉ោង (Teacher: 11:00, Other: 12:00)
            if ($now->lessThan($earliestMornOut)) {
                return back()->withErrors([
                    'error' => 'មិនទាន់អាច Check-out ព្រឹកបានទេ! សម្រាប់មុខតំណែងរបស់អ្នក លុះត្រាតែដល់ម៉ោង '.$earliestMornOut->format('H:i').' ឡើងទៅ។',
                ]);
            }

            $attendance->update(['check_out_morn' => $nowTime]);

            return back()->with('success', 'Check-out វគ្គពេលព្រឹកជោគជ័យ!');
        }

        // === ករណីទី ៣៖ CHECK-IN រសៀល ===
        if ($attendance->check_in_aft === null) {
            if ($attendance->check_out_morn === null) {
                return back()->withErrors([
                    'error' => 'ការស្កេនបដិសេធ! អ្នកត្រូវតែ Check-out វគ្គពេលព្រឹកឱ្យបានរួចរាល់ជាមុនសិន។',
                ]);
            }

            // បើស្កេនយឺតជាងម៉ោងកំណត់ រសៀល ៣០ នាទី ស្មើនឹង Late
            $aftInDeadline = $aftInTime->copy()->addMinutes(30);
            $aftStatus = $now->greaterThan($aftInDeadline) ? 'Late' : 'Present';

            $attendance->update([
                'check_in_aft' => $nowTime,
                'aft_status' => $aftStatus,
            ]);

            return back()->with('success', 'Check-in វគ្គពេលរសៀលជោគជ័យ! ស្ថានភាព៖ '.($aftStatus == 'Late' ? 'យឺត' : 'ទាន់ម៉ោង'));
        }

        // === ករណីទី ៤៖ CHECK-OUT ល្ងាច ===
        if ($attendance->check_out_aft === null) {
            if ($attendance->check_in_aft === null) {
                return back()->withErrors([
                    'error' => 'ការស្កេនបដិសេធ! អ្នកមិនទាន់បាន Check-in វគ្គពេលរសៀលនៅឡើយទេ។',
                ]);
            }

            $attendance->update(['check_out_aft' => $nowTime]);

            return back()->with('success', 'Check-out វគ្គពេលល្ងាចជោគជ័យ! សូមសម្រាកចុះ។');
        }

        return back()->withErrors(['error' => 'អ្នកបានបំពេញវត្តមានគ្រប់ ៤ ពេលរួចរាល់ហើយសម្រាប់ថ្ងៃនេះ!']);
    }
}
