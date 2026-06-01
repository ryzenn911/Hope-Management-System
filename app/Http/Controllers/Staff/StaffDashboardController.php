<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Leave;

class StaffDashboardController extends Controller
{
    public function index(Request $request)
    {
        $employeeId = $request->user()->employee->id;

        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $leaves = Leave::query()
            ->where('employee_id', $employeeId)
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->orderBy('created_at', 'desc')
            ->get();

        $baseQuery = Leave::query()
            ->where('employee_id', $employeeId)
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month);

        $approvedDays = (clone $baseQuery)->where('status', 'approved')->sum('total_days');
        $rejectedDays = (clone $baseQuery)->where('status', 'rejected')->sum('total_days');
        $pendingDays  = (clone $baseQuery)->where('status', 'pending')->sum('total_days');
        
        $annualApproved = Leave::query()
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->whereYear('start_date', $year)
            ->sum('total_days');

        $leaveBalance = 18 - $annualApproved;

        return Inertia::render('Staff/Dashboard', [
            'leaves'       => $leaves,
            'approvedDays' => (int)$approvedDays,
            'rejectedDays' => (int)$rejectedDays,
            'pendingDays'  => (int)$pendingDays,
            'leaveBalance' => (int)$leaveBalance,
            'filters'      => [
                'month' => $month,
                'year'  => $year
            ]
        ]);
    }
}
