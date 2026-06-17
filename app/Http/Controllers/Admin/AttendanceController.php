<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    public function showOfficeQr()
    {
        $qrValue = 'HOPE_ATTENDANCE';
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data='.urlencode($qrValue);

        return Inertia::render('Admin/Attendance/OfficeQr', [
            'qrCodeUrl' => $qrCodeUrl,
            'qrValue' => $qrValue,
        ]);
    }

    public function index(Request $request)
    {
        $dateInput = $request->input('date');
        if ($dateInput) {
            $selectedDate = Carbon::createFromFormat('d-m-Y', $dateInput)->toDateString();
        } else {
            $selectedDate = Carbon::today()->toDateString();
        }

        $employees = Employee::with(['position', 'attendances' => function ($query) use ($selectedDate) {
            $query->whereDate('date', $selectedDate);
        }])->get()->map(function ($employee) {
            $attendance = $employee->attendances->first();

            return [
                'id' => $employee->id,
                'name_kh' => $employee->name_kh,
                'name_eng' => $employee->name_eng,
                'position' => $employee->position ? $employee->position->name : 'មិនទាន់កំណត់',
                'attendance_id' => $attendance ? $attendance->id : null,
                'check_in_morn' => $attendance && $attendance->check_in_morn ? Carbon::parse($attendance->check_in_morn)->format('H:i') : '--:--',
                'check_out_morn' => $attendance && $attendance->check_out_morn ? Carbon::parse($attendance->check_out_morn)->format('H:i') : '--:--',
                'check_in_aft' => $attendance && $attendance->check_in_aft ? Carbon::parse($attendance->check_in_aft)->format('H:i') : '--:--',
                'check_out_aft' => $attendance && $attendance->check_out_aft ? Carbon::parse($attendance->check_out_aft)->format('H:i') : '--:--',
                'morn_status' => $attendance ? $attendance->morn_status : null,
                'aft_status' => $attendance ? $attendance->aft_status : null,
            ];
        });

        return Inertia::render('Admin/Attendance/Index', [
            'employees' => $employees,
            'selectedDate' => Carbon::parse($selectedDate)->format('d-m-Y'),
        ]);
    }
}
