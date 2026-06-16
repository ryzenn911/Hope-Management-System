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
        $employee = $user->employee;

        if (! $employee) {
            return back()->withErrors(['error' => 'មិនមានទិន្នន័យបុគ្គលិកឡើយ។']);
        }

        // ១. កំណត់សប្ដាហ៍ (សប្ដាហ៍បច្ចុប្បន្ន ឬ សប្ដាហ៍ដែលទើបចុច Next/Prev មក)
        // សន្មតថាបងប្រើប្រាស់ query string ដូចជា ?week=2026-W25 ឬ ?date=2026-06-16
        $targetDate = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::now();

        // រកថ្ងៃចន្ទ និងថ្ងៃសុក្រ នៃសប្ដាហ៍នោះ
        $startOfWeek = $targetDate->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $targetDate->copy()->endOfWeek(Carbon::FRIDAY);

        // បង្កើត String សម្រាប់បង្ហាញ props.weekRange (ឧទាហរណ៍៖ "15 Jun 2026 - 19 Jun 2026")
        $weekRange = $startOfWeek->format('d M Y').' - '.$endOfWeek->format('d M Y');

        // ២. ទាញយកទិន្នន័យវត្តមានទាំងអស់ក្នុងសប្ដាហ៍នោះរបស់បុគ្គលិកម្នាក់នេះ
        $attendances = Attendance::query()
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->get()
            ->keyBy('date'); // ចង Key តាម Date ដើម្បីងាយស្រួលទាញយកមក Map

        // ៣. បង្កើតអារេឈ្មោះថ្ងៃជាភាសាខ្មែរ និងអង់គ្លេស
        $khmerDays = [
            'Monday' => 'ចន្ទ',
            'Tuesday' => 'អង្គារ',
            'Wednesday' => 'ពុធ',
            'Thursday' => 'ព្រហស្បតិ៍',
            'Friday' => 'សុក្រ',
        ];

        $weeklyRecords = [];

        // ៤. Loop ពីថ្ងៃចន្ទ ដល់ ថ្ងៃសុក្រ ដើម្បីរៀបចំទិន្នន័យឱ្យត្រូវ Format របស់ Vue.js
        for ($date = $startOfWeek->copy(); $date->lessThanOrEqualTo($endOfWeek); $date->addDay()) {
            $dateString = $date->toDateString();
            $dayNameEn = $date->format('l'); // ទាញឈ្មោះថ្ងៃជាអង់គ្លេស (e.g., Monday)

            // ទាញយក Record ពី Database បើមាន (បើគ្មានទេ ស្មើនឹង null)
            $record = $attendances->get($dateString);

            $weeklyRecords[] = [
                'date' => $dateString,
                'display_date' => $date->format('d M Y'),         // សម្រាប់បង្ហាញឧទាហរណ៍ "15 Jun 2026"
                'day_name' => $dayNameEn,                      // "Monday"
                'kh_day' => $khmerDays[$dayNameEn] ?? '',    // "ចន្ទ"
                'record' => $record ? [
                    'check_in_morn' => $record->check_in_morn ? Carbon::parse($record->check_in_morn)->format('H:i') : null,
                    'check_out_morn' => $record->check_out_morn ? Carbon::parse($record->check_out_morn)->format('H:i') : null,
                    'check_in_aft' => $record->check_in_aft ? Carbon::parse($record->check_in_aft)->format('H:i') : null,
                    'check_out_aft' => $record->check_out_aft ? Carbon::parse($record->check_out_aft)->format('H:i') : null,
                    'morn_status' => $record->morn_status,      // Present ឬ Late ពី Database
                    'aft_status' => $record->aft_status,       // Present ឬ Late ពី Database
                    'status' => strtolower($record->status ?? 'absent'), // ស្ថានភាពរួម (present, late, absent)
                ] : null, // បើថ្ងៃហ្នឹងអត់ទាន់មានការស្កេនទាល់តែសោះ ផ្ញើទៅ null
            ];
        }

        // ៥. បោះទៅកាន់ Inertia View
        return Inertia::render('Staff/Dashboard', [
            'weeklyRecords' => $weeklyRecords,
            'weekRange' => $weekRange,
        ]);
    }
}
