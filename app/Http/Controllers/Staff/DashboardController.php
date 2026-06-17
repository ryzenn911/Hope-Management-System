<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $employeeId = $user->employee ? $user->employee->id : $user->id;

        $dateInput = $request->input('date');

        if ($dateInput) {
            try {
                $selectedDate = Carbon::createFromFormat('d-m-Y', $dateInput)->toDateString();
            } catch (\Exception $e) {
                $selectedDate = Carbon::today()->toDateString();
            }
        } else {
            $selectedDate = Carbon::today()->toDateString();
        }

        // 💡 បន្ថែម៖ ឆែកមើលថាតើថ្ងៃដែលបានជ្រើសរើស ត្រូវគ្នានឹងថ្ងៃបច្ចុប្បន្ន (Today) ដែរឬទេ
        $isToday = ($selectedDate === Carbon::today()->toDateString());

        $record = Attendance::query()
            ->where('employee_id', $employeeId)
            ->whereDate('date', $selectedDate)
            ->first();

        // រៀបចំ Format ទិន្នន័យបោះទៅ Frontend
        if ($record) {
            $mornStatus = $record->check_in_morn ? $record->morn_status : 'Absent';
            $aftStatus = $record->check_in_aft ? $record->aft_status : 'Absent';

            $todayRecord = [
                'check_in_morn' => $record->check_in_morn ? Carbon::parse($record->check_in_morn)->format('H:i') : null,
                'check_out_morn' => $record->check_out_morn ? Carbon::parse($record->check_out_morn)->format('H:i') : null,
                'check_in_aft' => $record->check_in_aft ? Carbon::parse($record->check_in_aft)->format('H:i') : null,
                'check_out_aft' => $record->check_out_aft ? Carbon::parse($record->check_out_aft)->format('H:i') : null,
                'morn_status' => $mornStatus,
                'aft_status' => $aftStatus,
                'status' => $this->calculateTotalStatus($mornStatus, $aftStatus),
            ];
        } else {
            $todayRecord = [
                'check_in_morn' => null,
                'check_out_morn' => null,
                'check_in_aft' => null,
                'check_out_aft' => null,
                'morn_status' => null,
                'aft_status' => null,
                'status' => null,
            ];
        }

        return Inertia::render('Staff/Dashboard', [
            'todayRecord' => $todayRecord,
            'selectedDate' => Carbon::parse($selectedDate)->format('d-m-Y'),
            'isToday' => $isToday, // 💡 បោះតម្លៃ true/false ទៅឱ្យ Frontend
        ]);
    }

    private function calculateTotalStatus($morn, $aft)
    {
        if ($morn === 'Absent' && $aft === 'Absent') {
            return 'absent';
        }
        if ($morn === 'Late' || $aft === 'Late') {
            return 'late';
        }
        if ($morn === 'Present' || $aft === 'Present') {
            return 'present';
        }

        return 'absent';
    }
}
