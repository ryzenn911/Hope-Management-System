<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Notifications\LeaveStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminLeaveController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $leaves = Leave::with('employee.user')
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/Leaves/Index', [
            'leaves' => $leaves,
            'filters' => [
                'month' => $month,
                'year' => $year,
            ],
        ]);
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);
        $leave = Leave::with('employee.user')->findOrFail($id);

        $leave->update([
            'status' => $request->status,
            'approved_by' => Auth::id(),
        ]);
        if ($leave->employee && $leave->employee->user) {
            $leave->employee->user->notify(new LeaveStatusNotification($leave));
        }

        return back()->with('success', 'Status updated and notification sent to staff!');
    }
}
