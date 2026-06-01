<?php

use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AttendanceSettingController;
use App\Http\Controllers\Staff\LeaveController;
use App\Http\Controllers\Admin\AdminLeaveController;
use App\Http\Controllers\Staff\StaffDashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/login');


// =========================
// Admin Routes
// =========================
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('admin.dashboard');

        // Employees CRUD
        Route::resource('employees', EmployeeController::class);

        Route::get('/leaves', [AdminLeaveController::class, 'index'])->name('admin.leaves.index');
        Route::patch('/leaves/{id}/status', [AdminLeaveController::class, 'updateStatus'])->name('admin.leaves.updateStatus');

        // Attendance
        Route::prefix('attendance')
            ->controller(AttendanceSettingController::class)
            ->group(function () {

                Route::get('/', 'index')->name('admin.attendance');

                Route::get('/settings', 'index')
                    ->name('attendance.settings');

                Route::post('/settings/location', 'updateLocation')
                    ->name('attendance.updateLocation');

                Route::post('/settings/shift', 'saveShift')
                    ->name('attendance.saveShift');
            });
    });



Route::middleware(['auth'])->group(function () {
    Route::post('/notifications/mark-as-read', function () {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }
        return back();
    })->name('notifications.markRead');
    Route::post('/notifications/{id}/read', function ($id) {
        $user = Auth::user();
        if ($user) {
            $user->notifications()->where('id', $id)->first()?->markAsRead();
        }
        return back();
    })->name('notifications.singleRead');
});
// =========================
// Staff Routes
// =========================
Route::middleware('auth')->group(function () {
    // ដាក់បញ្ចូលក្នុង Group ដែលមាន name 'staff.'
    Route::name('staff.')->group(function () {
        Route::get('/staff/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
        Route::prefix('staff')->controller(LeaveController::class)->group(function () {
            Route::get('/leaves/create', 'create')->name('leaves.create');
            Route::post('/leaves', 'store')->name('leaves.store');
        });
    });

    // Profile (ទុកដដែល)
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
});

require __DIR__ . '/auth.php';
