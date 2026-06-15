<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalStaff = Employee::query()->count('id');
        $activeStaffCount = Employee::query()->where('status', 'active')->count();

        $positionData = Position::withCount(['employees' => function ($query) {
            $query->where('status', 'Active');
        }])->get();

        $chartLabels = $positionData->pluck('name');
        $chartCounts = $positionData->pluck('employees_count');

        return Inertia::render('Admin/Dashboard', [
            'activeStaffCount' => $activeStaffCount,
            'totalStaff' => $totalStaff,
            'chartLabels' => $chartLabels,
            'chartCounts' => $chartCounts,
        ]);
    }
}
