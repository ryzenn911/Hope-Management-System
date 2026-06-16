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
        $record = Attendance::query()
            ->where('employee_id', $employeeId)
            ->whereDate('date', Carbon::today()->toDateString())
            ->first();

        // រៀបចំ Format ទិន្នន័យបោះទៅ Frontend
        if ($record) {
            // 💡 កំណត់តម្លៃស្ថានភាពជាមុនសិន ដើម្បីយកទៅប្រើប្រាស់ក្នុងមុខងារគណនាសរុប (Total Status)
            $mornStatus = $record->check_in_morn ? $record->morn_status : 'Absent';
            $aftStatus = $record->check_in_aft ? $record->aft_status : 'Absent';

            $todayRecord = [
                'check_in_morn' => $record->check_in_morn ? Carbon::parse($record->check_in_morn)->format('H:i') : null,
                'check_out_morn' => $record->check_out_morn ? Carbon::parse($record->check_out_morn)->format('H:i') : null,
                'check_in_aft' => $record->check_in_aft ? Carbon::parse($record->check_in_aft)->format('H:i') : null,
                'check_out_aft' => $record->check_out_aft ? Carbon::parse($record->check_out_aft)->format('H:i') : null,
                'morn_status' => $mornStatus,
                'aft_status' => $aftStatus,
                // 💡 ជួសជុល៖ បោះតម្លៃដែលបានផ្ទៀងផ្ទាត់រួច ($mornStatus, $aftStatus) ចូលទៅគណនា
                'status' => $this->calculateTotalStatus($mornStatus, $aftStatus),
            ];
        } else {
            // 💡 ករណីថ្ងៃនេះមិនទាន់មានការស្កេនទាល់តែសោះ (ទិន្នន័យក្នុង DB មិនទាន់បង្កើត)
            $todayRecord = [
                'check_in_morn' => null,
                'check_out_morn' => null,
                'check_in_aft' => null,
                'check_out_aft' => null,
                'morn_status' => 'Absent',
                'aft_status' => 'Absent',
                'status' => 'absent',
            ];
        }

        return Inertia::render('Staff/Dashboard', [
            'todayRecord' => $todayRecord,
        ]);
    }

    /**
     * មុខងារជំនួយ (Helper) សម្រាប់គណនាស្ថានភាពរួមប្រចាំថ្ងៃ
     */
    private function calculateTotalStatus($morn, $aft)
    {
        // បើអវត្តមានទាំងព្រឹកទាំងរសៀល គឺ Absent
        if ($morn === 'Absent' && $aft === 'Absent') {
            return 'absent';
        }

        // បើមានពេលណាមួយជាប់យឺត (ទោះពេលមួយទៀត Present ឬ Absent) គឺសរុបទៅជា Late
        if ($morn === 'Late' || $aft === 'Late') {
            return 'late';
        }

        // បើមានពេលណាមួយ Present ហើយគ្មានពេលណា Late ឡើយ គឺសរុបទៅជា Present
        if ($morn === 'Present' || $aft === 'Present') {
            return 'present';
        }

        return 'absent';
    }
}
