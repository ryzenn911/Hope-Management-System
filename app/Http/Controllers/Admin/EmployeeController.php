<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\EducationLevel;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Admin/Employees/Create', [
            'addresses' => Address::orderBy('name', 'asc')->get(['id', 'name']),
            'positions' => Position::orderBy('name', 'asc')->get(['id', 'name']),
            'educations' => EducationLevel::all(['id', 'name']),
        ]);
    }

    public function index(Request $request)
    {
        $employees = Employee::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    // កែឈ្មោះ column ឱ្យត្រូវនឹង Database របស់បង
                    $q->where('name_kh', 'like', "%{$search}%")
                        ->orWhere('name_en', 'like', "%{$search}%")
                        ->orWhere('employee_code', 'like', "%{$search}%");
                });
            })
            ->with(['position', 'address', 'education'])
            ->orderBy('name_kh', 'asc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Employees/Index', [
            'employees' => $employees,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'username' => 'required|string|max:255|unique:users,username',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:8',
                'employee_code' => 'required|string|unique:employees,employee_code',
                'name_kh' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'gender' => 'required|in:Male,Female',
                'dob' => 'required|date',
                'marital_status' => 'required|in:single,married,divorced,widowed',
                'phone' => 'required|string',
                'address_id' => 'required|exists:addresses,id',
                'position_id' => 'required',
                'other_position_name' => 'required_if:position_id,other',
                'education_id' => 'required',
                'other_education_name' => 'required_if:education_id,other',
                'hire_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:hire_date',
                'nssf_number' => 'nullable|string|max:50',
                'labor_book' => 'nullable|string|max:50',
                'family_number' => 'nullable|string|max:50',
            ],
            [
                'username.required' => 'Please enter a username.',
                'email.required' => 'Please enter an email address.',
                'password.required' => 'Please enter a password.',
                'employee_code.required' => 'Please enter a Staff ID.',
                'name_kh.required' => 'Please enter a Khmer name.',
                'name_en.required' => 'Please enter an English name.',
                'gender.required' => 'Please select a gender.',
                'marital_status.required' => 'Please select a marital status.',
                'dob.required' => 'Please select a date of birth.',
                'phone.required' => 'Please enter a phone number.',
                'address_id.required' => 'Please select an address.',
                'position_id.required' => 'Please select a position.',
                'other_position_name.required_if' => 'The other position name field is required when the position is "other".',
                'education_id.required' => 'Please select an education level.',
                'other_education_name.required_if' => 'The other education name field is required when the education is "other".',
                'hire_date.required' => 'Please enter a hire date.',
            ]
        );

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $positionId = $request->position_id;
        if ($request->position_id === 'other') {
            $newPosition = Position::firstOrCreate(['name' => trim($request->other_position_name)]);
            $positionId = $newPosition->id;
        }

        // ដោះស្រាយ Education
        $educationId = $request->education_id;
        if ($request->education_id === 'other') {
            $newEdu = EducationLevel::firstOrCreate(['name' => trim($request->other_education_name)]);
            $educationId = $newEdu->id;
        }

        try {
            DB::transaction(function () use ($request, $positionId, $educationId) {
                $user = User::create([
                    'name' => $request->name_en,
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => 'staff',
                ]);

                Employee::create([
                    'user_id' => $user->id,
                    'position_id' => $positionId,
                    'education_id' => $educationId,
                    'address_id' => $request->address_id,
                    'employee_code' => $request->employee_code,
                    'name_kh' => $request->name_kh,
                    'name_en' => $request->name_en,
                    'gender' => $request->gender,
                    'marital_status' => $request->marital_status,
                    'nssf_number' => $request->nssf_number,
                    'labor_book' => $request->labor_book,
                    'family_number' => str_replace(' ', '', $request->family_number),
                    'dob' => $request->dob,
                    'phone' => str_replace(' ', '', $request->phone),
                    'hire_date' => $request->hire_date,
                    'end_date' => $request->end_date,
                    'status' => $request->end_date ? 'Resign' : 'Active',
                ]);
            });

            return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'មានបញ្ហាបច្ចេកទេស៖ '.$e->getMessage()])->withInput();
        }
    }

    public function show(Employee $employee)
    {
        $employee->load(['user', 'position', 'address', 'education']);

        return inertia('Admin/Employees/View', [
            'employee' => $employee,
        ]);
    }

    public function edit(Employee $employee)
    {

        $employee->load('user');

        return Inertia::render('Admin/Employees/Edit', [
            'employee' => $employee,
            'addresses' => Address::all(),
            'positions' => Position::all(),
            'educations' => EducationLevel::all(),
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        // ១. Validation
        $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.$employee->user_id],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$employee->user_id],
            'password' => ['nullable', 'string', 'min:8'],

            'employee_code' => ['required', 'string', 'unique:employees,employee_code,'.$employee->id],
            'name_kh' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:Male,Female'],
            'dob' => ['required', 'date'],
            'phone' => ['required', 'string'],
            'family_number' => ['nullable', 'string'],
            'marital_status' => ['required', 'in:single,married,divorced,widowed'],
            'nssf_number' => ['nullable', 'string', 'max:255'],
            'labor_book' => ['nullable', 'string', 'max:255'],
            'address_id' => ['nullable', 'exists:addresses,id'],
            'position_id' => ['required'],
            'education_id' => ['required'],
            'other_position_name' => 'required_if:position_id,other',
            'other_education_name' => 'required_if:education_id,other',
            'hire_date' => ['required', 'date'],

            // កែត្រង់នេះ៖ ប្រើ Resign (តាម Enum ក្នុង DB)
            'status' => 'required|in:Active,Resigned',
            'end_date' => 'nullable|date',
        ]);

        // ២. ចាប់យក ID សម្រាប់ Position
        $positionId = $request->position_id;
        if ($request->position_id === 'other') {
            $newPos = Position::firstOrCreate(['name' => trim($request->other_position_name)]);
            $positionId = $newPos->id;
        }

        // ៣. ចាប់យក ID សម្រាប់ Education
        $educationId = $request->education_id;
        if ($request->education_id === 'other') {
            $newEdu = EducationLevel::firstOrCreate(['name' => trim($request->other_education_name)]);
            $educationId = $newEdu->id;
        }

        // ៤. Update User Account
        $user = $employee->user;
        $user->update([
            'name' => $request->name_en, // Update ឈ្មោះក្នុង Table User ដែរ
            'username' => $request->username,
            'email' => $request->email,
        ]);

        if ($request->filled('password') && $request->password !== '********') {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // ៥. Update ព័ត៌មានបុគ្គលិក (ប្រើ Variable $positionId និង $educationId)
        $employee->update([
            'employee_code' => $request->employee_code,
            'name_kh' => $request->name_kh,
            'name_en' => $request->name_en,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'marital_status' => $request->marital_status,
            'nssf_number' => $request->nssf_number,
            'labor_book' => $request->labor_book,
            'family_number' => str_replace(' ', '', $request->family_number),
            'phone' => str_replace(' ', '', $request->phone),
            'address_id' => $request->address_id,
            'position_id' => $positionId,
            'education_id' => $educationId,
            'hire_date' => $request->hire_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ]);

        return redirect()->route('employees.index')->with('success', 'Staff updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        DB::transaction(function () use ($employee) {
            if ($employee->user) {
                $employee->user()->delete();
            }
            /** @disregard */
            $employee->delete();
        });

        return redirect()->route('employees.index');
    }
}
