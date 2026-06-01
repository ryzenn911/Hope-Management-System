<?php

namespace App\Http\Controllers\Admin;
use App\Notifications\LeaveStatusNotification;
use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class AdminLeaveController extends Controller
{
    public function index(Request $request)
    {
        // ទទួលតម្លៃខែ និងឆ្នាំពី Flatpickr (បើអត់មាន យកខែ/ឆ្នាំបច្ចុប្បន្ន)
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        // ទាញយកច្បាប់ទាំងអស់ រួមជាមួយឈ្មោះបុគ្គលិក (Relationship: employee -> user)
        $leaves = Leave::with('employee.user')
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/Leaves/Index', [
            'leaves' => $leaves,
            'filters' => [
                'month' => $month,
                'year' => $year
            ]
        ]);
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
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
