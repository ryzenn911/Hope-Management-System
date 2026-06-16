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

        // ចាប់យកកាលបរិច្ឆេទថ្ងៃនេះ (ទម្រង់ YYYY-MM-DD)
        $todayDate = Carbon::today()->toDateString();

        // ទាញយក Record វត្តមានរបស់ថ្ងៃនេះមួយគត់
        $record = Attendance::query()
            ->where('employee_id', $employee->id)
            ->where('date', $todayDate)
            ->first();

        // រៀបចំ Format ទិន្នន័យបោះទៅ Frontend
        $todayRecord = $record ? [
            'check_in_morn' => $record->check_in_morn ? Carbon::parse($record->check_in_morn)->format('H:i') : null,
            'check_out_morn' => $record->check_out_morn ? Carbon::parse($record->check_out_morn)->format('H:i') : null,
            'check_in_aft' => $record->check_in_aft ? Carbon::parse($record->check_in_aft)->format('H:i') : null,
            'check_out_aft' => $record->check_out_aft ? Carbon::parse($record->check_out_aft)->format('H:i') : null,
            'morn_status' => $record->morn_status,
            'aft_status' => $record->aft_status,
        ] : null;

        // បោះទៅកាន់ Inertia View
        return Inertia::render('Staff/Dashboard', [
            'todayRecord' => $todayRecord, // ប្តូរពី weeklyRecords មកជា todayRecord
        ]);
    }
}
