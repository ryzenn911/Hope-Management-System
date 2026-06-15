<?php

namespace App\Http\Controllers\Staff;

use App\Notifications\LeaveRequestNotification;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Leave;
use Carbon\Carbon;
use Carbon\CarbonPeriod;


class LeaveController extends Controller
{

    public function create()
    {
        return Inertia::render('Staff/Leaves/Create');
    }

     public function index()
    {
        return Inertia::render('Staff/Leaves/Index');
    }

    public function store(Request $request)
    {

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $period = CarbonPeriod::create($startDate, $endDate);
        $totalDays = 0;

        foreach ($period as $date) {
            if (!$date->isWeekend()) {
                $totalDays++;
            }
        }
        $request->validate([
            'leave_type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_days' => 'required|integer|min:1',
            'reason' => 'nullable|string',
        ]);

        $leave = Leave::create([
            'employee_id' => $request->user()->employee->id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $request->total_days,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        $admin = User::query()->where('role', 'admin')->first();

        if ($admin && $leave) {
            $admin->notify(new LeaveRequestNotification($leave));
        }
        return redirect()->route('staff.dashboard')->with('success', 'The leave request has been submitted!');
    }
}
