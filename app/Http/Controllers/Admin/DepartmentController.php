<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $positions = Position::select('id', 'name')->get();
        $employeesQuery = Employee::with(['user', 'position']);

        if ($request->filled('position_id')) {
            $employeesQuery->where('position_id', $request->position_id);
        }
        $employees = $employeesQuery->get();
        $totalStaff = $employees->count();

        return Inertia::render('Admin/Department/Index', [
            'employees' => $employees,
            'positions' => $positions,
            'totalStaff' => $totalStaff,
            'filters' => $request->only(['position_id']),
        ]);
    }
}
