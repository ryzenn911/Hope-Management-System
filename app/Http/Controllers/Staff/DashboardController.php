<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::now();
        $startOfWeek = $date->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $date->copy()->startOfWeek(Carbon::MONDAY)->addDays(4);
        $attendanceData = Attendance::query()->where('user_id', Auth::id())
            ->whereBetween('date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->get()
            ->keyBy('date');

        $weeklyRecords = [];
        for ($i = 0; $i < 5; $i++) {
            $currentDate = $startOfWeek->copy()->addDays($i);
            $dateStr = $currentDate->format('Y-m-d');

            $weeklyRecords[] = [
                'date' => $dateStr,
                'day_name' => $currentDate->format('l'),
                'kh_day' => $this->getKhmerDay($currentDate->format('l')),
                'display_date' => $currentDate->format('d M, Y'),
                'record' => $attendanceData[$dateStr] ?? null,
            ];
        }

        return inertia('Staff/Dashboard', [
            'weeklyRecords' => $weeklyRecords,
            'weekRange' => $startOfWeek->format('d M').' - '.$endOfWeek->format('d M, Y'),
            'currentDate' => $startOfWeek->format('Y-m-d'),
            // ... props ផ្សេងៗទៀត (leaves, user etc.)
        ]);
    }

    // Function ជំនួយសម្រាប់បកប្រែឈ្មោះថ្ងៃ
    private function getKhmerDay($day)
    {
        $days = [
            'Monday' => 'ច័ន្ទ', 'Tuesday' => 'អង្គារ', 'Wednesday' => 'ពុធ',
            'Thursday' => 'ព្រហស្បតិ៍', 'Friday' => 'សុក្រ',
        ];

        return $days[$day] ?? '';
    }
}
