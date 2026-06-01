<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalStaff = Employee::query()->count('id');
        $activeStaffCount = Employee::query()->where('status', 'active')->count();
        return Inertia::render('Admin/Dashboard', [
            'activeStaffCount' => $activeStaffCount,
            'totalStaff' => $totalStaff
        ]);
    }
}
