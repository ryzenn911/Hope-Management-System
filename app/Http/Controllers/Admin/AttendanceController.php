<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee; // ប្រាកដថាបងមាន Model ឈ្មោះ Staff ឬ Employee
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index()
    {

        $attendanceLogs = Employee::select(
            'staff.employee_code',
            'staff.name_en',
            'attendance_logs.s1_in',
            'attendance_logs.s1_out',
            'attendance_logs.s2_in',
            'attendance_logs.s2_out',
            'attendance_logs.status'
        )
            ->leftJoin('attendance_logs', function ($join) {
                $join->on('staff.id', '=', 'attendance_logs.staff_id')
                    ->whereDate('attendance_logs.attendance_date', now()->toDateString());
            })
            ->get();

        // ២. បោះទិន្នន័យទៅកាន់ Vue Index
        return Inertia::render('Admin/Attendance/Index', [
            'attendanceLogs' => $attendanceLogs
        ]);
    }
}
