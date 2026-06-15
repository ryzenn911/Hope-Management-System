<?php

use App\Http\Controllers\Admin\AdminLeaveController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff\AttendanceController;
use App\Http\Controllers\Staff\LeaveController;
use App\Http\Controllers\Staff\StaffDashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');
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

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
    Route::middleware(['is_admin'])
        ->prefix('admin')
        ->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
            Route::resource('employees', EmployeeController::class);
            Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
            Route::get('/leaves', [AdminLeaveController::class, 'index'])->name('admin.leaves.index');
            Route::patch('/leaves/{id}/status', [AdminLeaveController::class, 'updateStatus'])->name('admin.leaves.updateStatus');
        });

    Route::name('staff.')->prefix('staff')->group(function () {
        Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
        Route::controller(LeaveController::class)->group(function () {
            Route::get('/leaves', 'index')->name('leaves.index');
            Route::get('/leaves/create', 'create')->name('leaves.create');
            Route::post('/leaves', 'store')->name('leaves.store');
        });
        Route::controller(AttendanceController::class)->group(function () {
            Route::get('/attendance/scan', 'scanPage')->name('attendance.scan');
            Route::post('/attendance/store', 'store')->name('attendance.store');
        });

    });

});

require __DIR__.'/auth.php';
