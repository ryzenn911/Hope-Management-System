<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyLocation;
use App\Models\AttendanceSetting;
use App\Models\AttendanceShift;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class AttendanceSettingController extends Controller
{
    /**
     * បង្ហាញទំព័រដើមនៃ Attendance (មានទាំង Logs និង Settings)
     */
    // ក្នុង app/Http/Controllers/Admin/AttendanceSettingController.php

    public function index()
    {
        $attendanceSettings = CompanyLocation::first();
        $shifts = AttendanceShift::all();

        // ប្តូរពី Staff:: មកជា Employee::
        $attendanceLogs = Employee::select(
            'employees.employee_code', // ប្រើឈ្មោះតារាង employees
            'employees.name_en',       // ប្រើឈ្មោះតារាង employees
            'attendance_settings.s1_in',
            'attendance_settings.s1_out',
            'attendance_settings.s2_in',
            'attendance_settings.s2_out',
            'attendance_settings.status'
        )
            ->leftJoin('attendance_settings', function ($join) {
                $join->on('employees.id', '=', 'attendance_settings.employee_id'); // ផ្ទៀងផ្ទាត់ FK
                    // ->whereDate('attendance_settings.attendance_date', now()->toDateString());
            })
            ->get();

        return Inertia::render('Admin/Attendance/Index', [
            'attendanceSettings' => $attendanceSettings,
            'shifts' => $shifts,
            'attendanceLogs' => $attendanceLogs
        ]);
    }

    /**
     * សម្រាប់ Update ទីតាំងក្រុមហ៊ុន (Lat, Lng, Radius)
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'required|integer',
        ]);

        CompanyLocation::updateOrCreate(
            ['id' => 1],
            [
                'lat' => $request->lat,
                'lng' => $request->lng,
                'radius' => $request->radius
            ]
        );

        return Redirect::back()->with('success', 'រក្សាទុកទីតាំងបានជោគជ័យ!');
    }

    /**
     * សម្រាប់រក្សាទុកការកំណត់ម៉ោងឱ្យបុគ្គលិក (១ វេន ឬ ២ វេន)
     */
    public function saveStaffSetting(Request $request)
    {
        // បងនឹងត្រូវសរសេរ Logic រក្សាទុកម៉ោងចូល/ចេញនៅទីនេះក្នុងជំហានបន្ទាប់
    }

    public function saveShift(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'session_count' => 'required|in:1,2',
            's1_in' => 'required',
            's1_out' => 'required',
        ]);

        // ប្រសិនបើជ្រើសរើស ២ វេន ត្រូវ Validate ម៉ោងវេនទី ២ ដែរ
        if ($request->session_count == 2) {
            $request->validate([
                's2_in' => 'required',
                's2_out' => 'required',
            ]);
        }

        \App\Models\AttendanceSetting::updateOrCreate(
            ['employee_id' => $request->employee_id],
            [
                'session_count' => $request->session_count,
                's1_in' => $request->s1_in,
                's1_out' => $request->s1_out,
                's2_in' => $request->session_count == 2 ? $request->s2_in : null,
                's2_out' => $request->session_count == 2 ? $request->s2_out : null,
            ]
        );

        return Redirect::back()->with('success', 'ការកំណត់ម៉ោងត្រូវបានរក្សាទុក!');
    }
}
